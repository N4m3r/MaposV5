from fastapi import FastAPI, Request, Header, HTTPException
from fastapi.responses import JSONResponse
import json
import logging
import time
import requests as http_requests
import config

from database import execute_query, execute_insert, execute_update
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services import nlp
from services.llm import classificar_com_llm
from services.whisper_asr import transcrever_audio
from services.whatsapp_media import download_and_decrypt_audio

# Configurar logging
logging.basicConfig(
    level=getattr(logging, config.LOG_LEVEL, logging.INFO),
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

app = FastAPI(title="Agente IA WhatsApp - MapOS", version="2.0.0")

# Instancias
evo = EvolutionAPI()
queries = MaposQueries()

# Deduplicacao de mensagens (evita processar a mesma mensagem duas vezes)
_msg_ids = {}
_DEDUP_TTL = 60  # segundos para expirar IDs antigos


# ========== MIDDLEWARE / UTILS ==========

def verificar_api_key(x_api_key: str = Header(None)):
    # Webhook do Evolution Go nao envia API key custom
    # Aceitar: com API key correta, ou sem API key, ou com API key vazia
    if not x_api_key or x_api_key == '' or x_api_key == config.AGENT_API_KEY:
        return
    raise HTTPException(status_code=401, detail="API Key invalida")


def extrair_numero(payload: dict) -> str:
    """Extrai numero do remetente do payload do Evolution Go v0.7+"""
    try:
        data = payload.get('data', {})
        event = payload.get('event', '')

        # Ignorar eventos que nao sao mensagens
        if event in ('Connected', 'OfflineSyncCompleted', 'Receipt',
                      'ChatPresence', 'ConnectionUpdate'):
            return ''

        # Evolution Go v0.7+ formato: data.Info.Chat (jid do chat)
        info = data.get('Info', {})
        if info:
            is_from_me = info.get('IsFromMe', False)
            if is_from_me:
                # Mensagem enviada pelo proprio bot - ignorar
                return ''
            chat = info.get('Chat', '')
            if chat:
                return chat.split('@')[0]

        # Formato antigo (Evolution API v1): data.key.remoteJid
        key = data.get('key', {})
        remote_jid = key.get('remoteJid', '')
        if remote_jid:
            return remote_jid.split('@')[0]

        message = data.get('message', {})
        if 'key' in message:
            remote_jid = message['key'].get('remoteJid', '')
            if remote_jid:
                return remote_jid.split('@')[0]

        sender = data.get('sender', '') or data.get('senderJid', '')
        if sender:
            return sender.split('@')[0]

        return ''
    except Exception:
        return ''


def _is_from_me(payload: dict) -> bool:
    """Verifica se a mensagem foi enviada pelo proprio bot"""
    data = payload.get('data', {})
    info = data.get('Info', {})
    if info:
        return info.get('IsFromMe', False)
    key = data.get('key', {})
    return key.get('fromMe', False)


def _extrair_msg_id(payload: dict) -> str:
    """Extrai ID unico da mensagem para deduplicacao"""
    data = payload.get('data', {})
    # Evolution Go v0.7+: data.Info.ID
    info = data.get('Info', {})
    if info and info.get('ID'):
        return info.get('ID', '')
    # Formato antigo: data.key.id
    key = data.get('key', {})
    if key and key.get('id'):
        return key['id']
    return ''


def _is_duplicado(msg_id: str) -> bool:
    """Verifica se a mensagem ja foi processada recentemente (deduplicacao)"""
    if not msg_id:
        return False
    agora = time.time()
    # Limpar IDs expirados
    expirados = [k for k, v in _msg_ids.items() if agora - v > _DEDUP_TTL]
    for k in expirados:
        del _msg_ids[k]
    if msg_id in _msg_ids:
        return True
    _msg_ids[msg_id] = agora
    return False


def extrair_mensagem(payload: dict) -> str:
    """Extrai texto da mensagem do payload (Evolution Go v0.7+ e v1)"""
    try:
        data = payload.get('data', {})

        # Evolution Go v0.7+ formato: data.Message
        msg = data.get('Message', {})
        if msg:
            if 'conversation' in msg:
                return msg['conversation']
            if 'extendedTextMessage' in msg:
                return msg['extendedTextMessage'].get('text', '')

        # Formato antigo (Evolution API v1): data.message
        if 'message' in data:
            msg = data['message']
            if 'conversation' in msg:
                return msg['conversation']
            if 'extendedTextMessage' in msg:
                return msg['extendedTextMessage'].get('text', '')

        if 'body' in data:
            return data['body']

        return ''
    except Exception:
        return ''


def extrair_audio_info(payload: dict) -> dict:
    """Extrai informacoes de audio do payload (Evolution Go v0.7+ e v1)"""
    try:
        data = payload.get('data', {})

        # Evolution Go v0.7+ formato: data.Message.audioMessage
        msg = data.get('Message', {})
        audio_msg = msg.get('audioMessage', {})
        if audio_msg:
            info = data.get('Info', {})
            # Evolution Go v0.7+ usa 'URL' maiusculo
            url = audio_msg.get('URL', '') or audio_msg.get('url', '')
            # media key para descriptografar audio .enc do WhatsApp
            context_info = audio_msg.get('contextInfo', {})
            media_domain = context_info.get('mediaDomainInfo', {})
            media_key_b64 = media_domain.get('e2EeMediaKey', '') or audio_msg.get('mediaKey', '')
            # directPath como fallback para URL
            direct_path = audio_msg.get('directPath', '')
            if not url and direct_path:
                url = f"https://mmg.whatsapp.net{direct_path}"

            return {
                'tem_audio': True,
                'msg_id': info.get('ID', ''),
                'url': url,
                'media_key_b64': media_key_b64,
                'mimetype': audio_msg.get('mimetype', 'audio/ogg; codecs=opus') or audio_msg.get('Mimetype', 'audio/ogg; codecs=opus'),
                'segundos': audio_msg.get('seconds', 0) or audio_msg.get('Seconds', 0),
            }

        # Formato antigo (Evolution API v1): data.message.audioMessage
        message = data.get('message', {})
        audio_msg = message.get('audioMessage', {})
        if audio_msg:
            return {
                'tem_audio': True,
                'media_key': message.get('key', {}),
                'msg_id': data.get('key', {}).get('id', ''),
                'url': audio_msg.get('url', ''),
                'mimetype': audio_msg.get('mimetype', 'audio/ogg; codecs=opus'),
                'segundos': audio_msg.get('seconds', 0),
            }

        # Formato alternativo (Evolution API v2)
        if data.get('type') == 'audio':
            return {
                'tem_audio': True,
                'media_key': data.get('key', {}),
                'msg_id': data.get('key', {}).get('id', ''),
                'url': data.get('mediaUrl', data.get('url', '')),
                'mimetype': data.get('mimetype', 'audio/ogg'),
                'segundos': data.get('duration', 0),
            }

        return {'tem_audio': False}

    except Exception as e:
        logger.error(f"Erro ao extrair info de audio: {e}")
        return {'tem_audio': False}


def limpar_numero(numero: str) -> str:
    """Remove tudo exceto digitos e adiciona 55 se necessario"""
    numero = ''.join(filter(str.isdigit, numero))
    if len(numero) == 11 or len(numero) == 10:
        numero = '55' + numero
    return numero


def identificar_usuario(numero: str):
    """Identifica se o numero pertence a cliente, tecnico ou admin"""
    numero = limpar_numero(numero)

    sql = """
        SELECT w.*,
               c.nomeCliente as nome_cliente,
               u.nome as nome_usuario,
               p.nome as permissao_nome
        FROM whatsapp_integracao w
        LEFT JOIN clientes c ON c.idClientes = w.clientes_id
        LEFT JOIN usuarios u ON u.idUsuarios = w.usuarios_id
        LEFT JOIN permissoes p ON p.idPermissao = u.permissoes_id
        WHERE w.numero_telefone = :numero AND w.situacao = 1
        LIMIT 1
    """
    rows = execute_query(sql, {'numero': numero})
    if rows:
        row = rows[0]
        tipo = row.get('tipo_vinculo', 'desconhecido')
        if tipo == 'cliente':
            return {
                'tipo': 'cliente',
                'tipo_vinculo': 'cliente',
                'clientes_id': row.get('clientes_id'),
                'usuarios_id': None,
                'nome': row.get('nome_cliente', 'Cliente'),
                'numero': numero
            }
        else:
            return {
                'tipo': tipo,
                'tipo_vinculo': tipo,
                'clientes_id': None,
                'usuarios_id': row.get('usuarios_id'),
                'nome': row.get('nome_usuario', 'Usuario'),
                'numero': numero
            }

    return None


def registrar_log(numero: str, direcao: str, conteudo: str, intencao: str = None, status: str = 'recebido'):
    """Registra interacao no banco"""
    try:
        sql = """
            INSERT INTO whatsapp_log_interacoes
            (numero_telefone, tipo_mensagem, direcao, conteudo, intencao_detectada, status)
            VALUES (:numero, 'texto', :direcao, :conteudo, :intencao, :status)
        """
        execute_insert(sql, {
            'numero': numero,
            'direcao': direcao,
            'conteudo': conteudo[:1000],
            'intencao': intencao,
            'status': status
        })
    except Exception as e:
        logger.error(f"Erro ao registrar log: {e}")


def processar_comando(comando: str, params: dict, usuario: dict) -> dict:
    """Processa o comando classificado e retorna os dados para resposta"""
    dados = {}

    if comando == 'status_os':
        if usuario['tipo'] == 'cliente' and usuario.get('clientes_id'):
            oss = queries.listar_os_cliente(usuario['clientes_id'])
            dados = {'oss': oss}
        elif usuario['tipo'] in ('tecnico', 'admin') and usuario.get('usuarios_id'):
            oss = queries.listar_os_tecnico(usuario['usuarios_id'])
            dados = {'oss': oss}
        else:
            dados = {'oss': []}

    elif comando == 'detalhes_os':
        os_id = params.get('os_id')
        if os_id:
            os_data = queries.buscar_os(os_id)
            dados = {'os': os_data}
        else:
            if usuario.get('clientes_id'):
                oss = queries.listar_os_cliente(usuario['clientes_id'], 1)
                dados = {'os': oss[0] if oss else None}
            elif usuario.get('usuarios_id'):
                oss = queries.listar_os_tecnico(usuario['usuarios_id'])
                dados = {'os': oss[0] if oss else None}
            else:
                dados = {'os': None}

    elif comando == 'quanto_devo':
        if usuario.get('clientes_id'):
            total = queries.total_em_aberto_cliente(usuario['clientes_id'])
            dados = {'total': total}
        else:
            dados = {'total': 0}

    elif comando == 'minhas_os_hoje':
        if usuario.get('usuarios_id'):
            from datetime import date
            hoje = date.today().isoformat()
            oss = queries.listar_os_tecnico(usuario['usuarios_id'], hoje)
            dados = {'oss': oss}
        else:
            dados = {'oss': []}

    elif comando == 'relatorio_os':
        resumo = queries.resumo_os_dia()
        dados = {'resumo': resumo}

    elif comando == 'os_atrasadas':
        oss = queries.os_atrasadas()
        dados = {'oss': oss}

    elif comando == 'vendas_pendentes':
        vendas = queries.vendas_pendentes()
        dados = {'vendas': vendas}

    elif comando == 'cobrancas_vencidas':
        cobs = queries.cobrancas_vencidas()
        dados = {'cobrancas': cobs}

    elif comando == 'total_os_abertas':
        total = queries.total_os_abertas()
        dados = {'total': total}

    elif comando == 'sair':
        dados = {}

    elif comando == 'criar_os':
        dados = {
            'mensagem': f"""{usuario['nome']}, para criar uma OS acesse o painel do MapOS.

Em breve voce podera criar pelo WhatsApp tambem!"""
        }

    return dados


# ========== ENDPOINTS ==========

@app.get("/health")
async def health():
    """Verifica se o agente esta online e consegue acessar o banco"""
    try:
        result = execute_query("SELECT 1 as ok")
        db_ok = bool(result and result[0].get('ok') == 1)
    except Exception:
        db_ok = False

    # Verificar LLM
    llm_status = "regex" if not config.LLM_PROVIDER else config.LLM_PROVIDER

    return {
        "status": "ok",
        "database": "online" if db_ok else "offline",
        "llm": llm_status,
        "whisper": config.WHISPER_URL,
        "version": "2.0.0"
    }


@app.post("/webhook/evolution")
async def webhook_evolution(request: Request, x_api_key: str = Header(None)):
    """
    Recebe webhook do Evolution Go quando uma mensagem chega.
    Suporta texto e audio (transcricao automatica via Whisper).
    """
    # Aceitar webhook do Evolution Go (sem API key) ou com API key correta
    if x_api_key and x_api_key != config.AGENT_API_KEY:
        raise HTTPException(status_code=401, detail="Unauthorized")

    try:
        payload = await request.json()
    except Exception:
        payload = {}

    event = payload.get('event', '')
    logger.info(f"Webhook recebido: event={event}")

    # Ignorar eventos que nao sao mensagens recebidas
    # SendMessage = propria resposta do bot (evita eco/loop)
    if event not in ('Message', 'MESSAGES_UPSERT', ''):
        return {"status": "ignorado", "motivo": f"evento_{event}"}

    # Extrair dados
    numero = extrair_numero(payload)
    if not numero:
        if config.DEBUG:
            logger.debug(f"Payload sem numero: {json.dumps(payload, indent=2, ensure_ascii=False)[:500]}")
        return {"status": "ignorado", "motivo": "sem numero"}

    numero = limpar_numero(numero)

    # Ignorar mensagens do proprio bot (Evolution Go envia IsFromMe=true)
    if _is_from_me(payload):
        return {"status": "ignorado", "motivo": "mensagem propria"}

    # Deduplicacao: ignorar mensagens ja processadas
    msg_id = _extrair_msg_id(payload)
    if _is_duplicado(msg_id):
        logger.info(f"Mensagem duplicada ignorada: msg_id={msg_id}")
        return {"status": "ignorado", "motivo": "duplicada"}

    # Verificar se e audio
    audio_info = extrair_audio_info(payload)

    texto = ''

    if audio_info.get('tem_audio'):
        # ===== PROCESSAR AUDIO =====
        logger.info(f"Audio recebido de {numero} ({audio_info.get('segundos', 0)}s)")
        logger.info(f"Audio info: url={bool(audio_info.get('url'))}, media_key={bool(audio_info.get('media_key_b64'))}, msg_id={audio_info.get('msg_id', 'N/A')}")

        # Tentar transcrever via Whisper
        transcricao = None
        audio_file_to_cleanup = None

        if audio_info.get('url') and audio_info.get('media_key_b64'):
            # Baixar e descriptografar audio .enc do WhatsApp
            logger.info("Baixando e descriptografando audio do WhatsApp...")
            dec_result = download_and_decrypt_audio(audio_info['url'], audio_info['media_key_b64'])
            if dec_result.get('success'):
                transcricao = transcrever_audio(audio_file=dec_result['file_path'])
                audio_file_to_cleanup = dec_result['file_path']
            else:
                logger.warning(f"Falha ao descriptografar audio: {dec_result.get('error')}")

        if not transcricao and audio_info.get('url'):
            # Fallback: tentar URL direta (se nao for .enc)
            if '.enc' not in audio_info.get('url', ''):
                transcricao = transcrever_audio(audio_url=audio_info['url'])

        if not transcricao and audio_info.get('msg_id'):
            # Fallback: tentar baixar via Evolution API
            resultado = evo.baixar_midia(msg_id=audio_info['msg_id'], message_key=audio_info.get('media_key'))
            if resultado.get('success'):
                transcricao = transcrever_audio(audio_file=resultado['file_path'])
                try:
                    import os
                    os.unlink(resultado['file_path'])
                except Exception:
                    pass

        if audio_file_to_cleanup:
            try:
                import os
                os.unlink(audio_file_to_cleanup)
            except Exception:
                pass

        if transcricao and transcricao.get('sucesso'):
            texto = transcricao['texto']
            logger.info(f"Audio transcrito: {texto!r}")
            registrar_log(numero, 'entrada', f"[AUDIO] {texto}", 'transcricao_audio')
        else:
            erro = transcricao.get('erro', 'erro desconhecido') if transcricao else 'servico indisponivel'
            logger.warning(f"Falha na transcricao de audio: {erro}")
            evo.enviar_texto(numero, "Desculpe, nao consegui entender o audio. Pode enviar por texto?")
            registrar_log(numero, 'entrada', '[AUDIO] falha transcricao', 'erro_audio', 'erro')
            return {"status": "erro_audio", "erro": erro}
    else:
        # ===== PROCESSAR TEXTO =====
        texto = extrair_mensagem(payload)
        if not texto:
            return {"status": "ignorado", "motivo": "sem mensagem"}

        registrar_log(numero, 'entrada', texto)

    # Identificar usuario
    usuario = identificar_usuario(numero)

    if not usuario:
        resposta = """Ola! Seu numero nao esta vinculado ao nosso sistema.

Entre em contato com nossa equipe para cadastrar seu WhatsApp."""
        registrar_log(numero, 'saida', resposta, 'nao_cadastrado', 'respondido')
        evo.enviar_texto(numero, resposta)
        return {"status": "nao_cadastrado"}

    # Atualizar ultima interacao
    execute_update(
        "UPDATE whatsapp_integracao SET ultima_interacao = NOW() WHERE numero_telefone = :numero",
        {'numero': numero}
    )

    # ===== CLASSIFICAR INTENCAO =====
    if config.LLM_PROVIDER:
        # Usar LLM como classificador primario
        resultado = classificar_com_llm(texto)
        if resultado and resultado.get('intencao') != 'desconhecido':
            comando = resultado['intencao']
            params = resultado.get('entidades', {})
        else:
            # Fallback para regex
            comando, params = nlp.classificar(texto)
    else:
        # Regex puro (modo sem LLM)
        comando, params = nlp.classificar(texto)

    logger.info(f"Classificacao: texto={texto!r} comando={comando} params={params}")

    # ===== PROCESSAR COMANDO =====
    dados = processar_comando(comando, params, usuario)

    # ===== FORMATAR E ENVIAR RESPOSTA =====
    resposta = nlp.formatar_resposta(comando, dados, usuario)

    resultado = evo.enviar_texto(numero, resposta)

    status_envio = 'respondido' if resultado.get('success') else 'erro'
    registrar_log(numero, 'saida', resposta, comando, status_envio)

    # Repassar para n8n se configurado (em background para nao bloquear resposta)
    import threading
    if config.N8N_WEBHOOK_URL:
        threading.Thread(target=forward_to_n8n, args=(payload,), daemon=True).start()

    return {
        "status": "ok",
        "comando": comando,
        "numero": numero,
        "tipo_mensagem": "audio" if audio_info.get('tem_audio') else "texto",
        "texto_processado": texto,
        "envio_success": resultado.get('success')
    }


def forward_to_n8n(payload: dict):
    """Repassa o payload original para o n8n (workflow existente)."""
    n8n_url = config.N8N_WEBHOOK_URL
    if not n8n_url:
        return
    try:
        http_requests.post(n8n_url, json=payload, timeout=5)
        logger.debug(f"Payload repassado para n8n: {n8n_url}")
    except Exception as e:
        logger.warning(f"Falha ao repassar para n8n: {e}")


# ========== EXECUCAO ==========

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=config.AGENT_PORT)