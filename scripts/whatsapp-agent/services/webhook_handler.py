"""
Webhook handler for Evolution API payloads.

Responsible for:
- Extracting sender number, message text, and audio info from Evolution payloads
- Deduplication of incoming messages
- Phone number normalization
- Logging interactions to the database
- Core webhook processing (classification, command routing, response)

Module-level dependencies are set via ``init()`` so that main.py
can inject the shared instances after they are created.
"""

import json
import logging
import re
import threading
import time

import requests as http_requests

import config
from database import execute_query, execute_insert, execute_update
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services.session_store import SessionStore
from services.result import Result
from services import nlp
from services.nlp import _fmt_status_emoji, _fmt_moeda, _fmt_data
from services.llm import classificar_com_llm, interpretar_audio_os, extrair_dados_os_audio
from services.whisper_asr import transcrever_audio
from services.whatsapp_media import download_and_decrypt_audio

logger = logging.getLogger(__name__)

# ---------- Module-level state (set via init) ----------
evo: EvolutionAPI | None = None
queries: MaposQueries | None = None
sessions: SessionStore | None = None
_msg_ids: dict = {}
_DEDUP_TTL = 60  # seconds to keep dedup IDs
_msg_ids_lock = threading.Lock()

# Permission map (same as main.py)
PERMISSOES_MAP = {
    1: 'admin',
    2: 'tecnico',
    3: 'financeiro',
    4: 'vendedor',
    5: 'cliente',
    6: 'cliente',
}

ADMIN_NUMERO = config.ADMIN_NUMERO


def init(evo_instance: EvolutionAPI, queries_instance: MaposQueries,
         sessions_instance: SessionStore):
    """Inject shared service instances."""
    global evo, queries, sessions
    evo = evo_instance
    queries = queries_instance
    sessions = sessions_instance


# ============================================================
# Payload extraction helpers
# ============================================================

def extrair_numero(payload: dict) -> str:
    """Extract sender number from Evolution Go v0.7+ payload."""
    try:
        data = payload.get('data', {})
        event = payload.get('event', '')

        if event in ('Connected', 'OfflineSyncCompleted', 'Receipt',
                      'ChatPresence', 'ConnectionUpdate'):
            return ''

        info = data.get('Info', {})
        if info:
            is_from_me = info.get('IsFromMe', False)
            if is_from_me:
                return ''
            chat = info.get('Chat', '')
            if chat:
                return chat.split('@')[0]

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
    except Exception as e:
        logger.warning(f"Erro ao extrair numero: {e}")
        return ''


def extrair_mensagem(payload: dict) -> str:
    """Extract text message from payload (conversation, buttons, lists)."""
    try:
        data = payload.get('data', {})

        msg = data.get('Message', {})
        if msg:
            if 'conversation' in msg:
                return msg['conversation']
            if 'extendedTextMessage' in msg:
                return msg['extendedTextMessage'].get('text', '')
            # Interactive: button
            buttons_resp = msg.get('buttonsResponseMessage', {})
            if buttons_resp:
                return buttons_resp.get('selectedDisplayText', '') or buttons_resp.get('selectedId', '')
            # Interactive: list
            list_resp = msg.get('listResponseMessage', {})
            if list_resp:
                row = list_resp.get('title', '') or list_resp.get('description', '')
                single_reply = list_resp.get('singleSelectReply', {})
                if single_reply:
                    return single_reply.get('selectedRowId', row)

        if 'message' in data:
            msg = data['message']
            if 'conversation' in msg:
                return msg['conversation']
            if 'extendedTextMessage' in msg:
                return msg['extendedTextMessage'].get('text', '')
            # Interactive: button
            buttons_resp = msg.get('buttonsResponseMessage', {})
            if buttons_resp:
                return buttons_resp.get('selectedDisplayText', '') or buttons_resp.get('selectedId', '')
            # Interactive: list
            list_resp = msg.get('listResponseMessage', {})
            if list_resp:
                row = list_resp.get('title', '') or list_resp.get('description', '')
                single_reply = list_resp.get('singleSelectReply', {})
                if single_reply:
                    return single_reply.get('selectedRowId', row)

        if 'body' in data:
            return data['body']

        return ''
    except Exception as e:
        logger.warning(f"Erro ao extrair mensagem: {e}")
        return ''


def extrair_audio_info(payload: dict) -> dict:
    """Extract audio information (URL, media key, mimetype, duration) from payload."""
    try:
        data = payload.get('data', {})

        msg = data.get('Message', {})
        audio_msg = msg.get('audioMessage', {})
        if audio_msg:
            info = data.get('Info', {})
            url = audio_msg.get('URL', '') or audio_msg.get('url', '')
            media_key_b64 = audio_msg.get('mediaKey', '') or audio_msg.get('MediaKey', '')
            if not media_key_b64:
                context_info = audio_msg.get('contextInfo', {})
                media_domain = context_info.get('mediaDomainInfo', {})
                media_key_b64 = media_domain.get('e2EeMediaKey', '')
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


def _is_from_me(payload: dict) -> bool:
    """Check whether the message was sent by the bot itself."""
    data = payload.get('data', {})
    info = data.get('Info', {})
    if info:
        return info.get('IsFromMe', False)
    key = data.get('key', {})
    return key.get('fromMe', False)


def _extrair_msg_id(payload: dict) -> str:
    """Extract message ID for deduplication."""
    data = payload.get('data', {})
    info = data.get('Info', {})
    if info and info.get('ID'):
        return info.get('ID', '')
    key = data.get('key', {})
    if key and key.get('id'):
        return key['id']
    return ''


def _is_duplicado(msg_id: str) -> bool:
    """Return True if *msg_id* was already processed within _DEDUP_TTL seconds."""
    if not msg_id:
        return False
    agora = time.time()
    with _msg_ids_lock:
        expirados = [k for k, v in _msg_ids.items() if agora - v > _DEDUP_TTL]
        for k in expirados:
            del _msg_ids[k]
        if msg_id in _msg_ids:
            return True
        _msg_ids[msg_id] = agora
        return False


def limpar_numero(numero: str) -> str:
    """Normalize a phone number: keep digits, prepend 55 if 10-11 digits."""
    numero = ''.join(filter(str.isdigit, numero))
    if len(numero) == 11 or len(numero) == 10:
        numero = '55' + numero
    return numero


def registrar_log(numero: str, direcao: str, conteudo: str,
                   intencao: str = None, status: str = 'recebido'):
    """Log an interaction to the whatsapp_log_interacoes DB table."""
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


# ============================================================
# User identification (kept here because the webhook depends on it)
# ============================================================

def identificar_usuario(numero: str):
    """Identify whether a phone number belongs to an admin, tecnico, or client."""
    numero = limpar_numero(numero)

    if numero == ADMIN_NUMERO:
        sql_admin = """
            SELECT u.idUsuarios, u.nome, u.celular, u.permissoes_id,
                   p.nome as permissao_nome
            FROM usuarios u
            LEFT JOIN permissoes p ON p.idPermissao = u.permissoes_id
            WHERE u.permissoes_id = 1 AND u.situacao = 1
            LIMIT 1
        """
        admin_rows = execute_query(sql_admin)
        if admin_rows:
            admin = admin_rows[0]
            return {
                'tipo': 'admin',
                'tipo_vinculo': 'admin',
                'clientes_id': None,
                'usuarios_id': admin.get('idUsuarios'),
                'permissoes_id': 1,
                'nome': admin.get('nome', 'Administrador'),
                'numero': numero
            }
        return {
            'tipo': 'admin',
            'tipo_vinculo': 'admin',
            'clientes_id': None,
            'usuarios_id': None,
            'permissoes_id': 1,
            'nome': 'Administrador',
            'numero': numero
        }

    sql = """
        SELECT w.*,
               c.idClientes as cli_id, c.nomeCliente as nome_cliente, c.celular as cli_celular,
               u.idUsuarios as usr_id, u.nome as nome_usuario, u.celular as usr_celular,
               u.permissoes_id, p.nome as permissao_nome
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
        permissoes_id = row.get('permissoes_id')
        usuarios_id = row.get('usr_id') or row.get('usuarios_id')
        clientes_id = row.get('cli_id') or row.get('clientes_id')

        if usuarios_id and permissoes_id:
            perfil = PERMISSOES_MAP.get(int(permissoes_id), 'desconhecido')
            nome = row.get('nome_usuario') or row.get('nome') or 'Usuario'
            return {
                'tipo': perfil,
                'tipo_vinculo': perfil,
                'clientes_id': clientes_id if perfil == 'cliente' else None,
                'usuarios_id': usuarios_id,
                'permissoes_id': int(permissoes_id),
                'nome': nome,
                'numero': numero
            }

        if clientes_id:
            return {
                'tipo': 'cliente',
                'tipo_vinculo': 'cliente',
                'clientes_id': clientes_id,
                'usuarios_id': None,
                'permissoes_id': None,
                'nome': row.get('nome_cliente') or row.get('nomeCliente') or 'Cliente',
                'numero': numero
            }

        tipo_vinculo = row.get('tipo_vinculo', 'desconhecido')
        return {
            'tipo': tipo_vinculo,
            'tipo_vinculo': tipo_vinculo,
            'clientes_id': row.get('clientes_id'),
            'usuarios_id': row.get('usuarios_id'),
            'permissoes_id': None,
            'nome': row.get('nome_usuario') or row.get('nome_cliente') or row.get('nome') or 'Usuario',
            'numero': numero
        }

    # Second attempt: search directly in usuarios table
    sql_usr = """
        SELECT u.idUsuarios, u.nome, u.celular, u.permissoes_id,
               p.nome as permissao_nome
        FROM usuarios u
        LEFT JOIN permissoes p ON p.idPermissao = u.permissoes_id
        WHERE REPLACE(REPLACE(REPLACE(u.celular, '(', ''), ')', ''), '-', '') LIKE :busca
           OR u.celular = :celular
           OR CONCAT('55', u.celular) = :numero
        LIMIT 1
    """
    usr_rows = execute_query(sql_usr, {
        'busca': f'%{numero[-11:]}%',
        'celular': numero[-11:] if len(numero) > 10 else numero,
        'numero': numero
    })
    if usr_rows:
        usr = usr_rows[0]
        perm_id = int(usr.get('permissoes_id', 0) or 0)
        perfil = PERMISSOES_MAP.get(perm_id, 'desconhecido')

        try:
            execute_insert("""
                INSERT INTO whatsapp_integracao (numero_telefone, usuarios_id, tipo_vinculo, situacao)
                VALUES (:numero, :usuarios_id, :tipo, 1)
                ON DUPLICATE KEY UPDATE usuarios_id = :usuarios_id, tipo_vinculo = :tipo, situacao = 1
            """, {
                'numero': numero,
                'usuarios_id': usr['idUsuarios'],
                'tipo': perfil,
            })
        except Exception as e:
            logger.warning(f"Auto-cadastro whatsapp_integracao: {e}")

        return {
            'tipo': perfil,
            'tipo_vinculo': perfil,
            'clientes_id': None,
            'usuarios_id': usr['idUsuarios'],
            'permissoes_id': perm_id,
            'nome': usr.get('nome', 'Usuario'),
            'numero': numero
        }

    # Third attempt: search in clientes table
    sql_cli = """
        SELECT idClientes, nomeCliente, celular
        FROM clientes
        WHERE REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', '') LIKE :busca
           OR celular = :celular
           OR CONCAT('55', celular) = :numero
        LIMIT 1
    """
    cli_rows = execute_query(sql_cli, {
        'busca': f'%{numero[-11:]}%',
        'celular': numero[-11:] if len(numero) > 10 else numero,
        'numero': numero
    })
    if cli_rows:
        cli = cli_rows[0]

        try:
            execute_insert("""
                INSERT INTO whatsapp_integracao (numero_telefone, clientes_id, tipo_vinculo, situacao)
                VALUES (:numero, :clientes_id, 'cliente', 1)
                ON DUPLICATE KEY UPDATE clientes_id = :clientes_id, tipo_vinculo = 'cliente', situacao = 1
            """, {
                'numero': numero,
                'clientes_id': cli['idClientes'],
            })
        except Exception as e:
            logger.warning(f"Auto-cadastro cliente whatsapp_integracao: {e}")

        return {
            'tipo': 'cliente',
            'tipo_vinculo': 'cliente',
            'clientes_id': cli['idClientes'],
            'usuarios_id': None,
            'permissoes_id': None,
            'nome': cli.get('nomeCliente', 'Cliente'),
            'numero': numero
        }

    return None


# ============================================================
# Permission helpers (used by command_router)
# ============================================================

def eh_admin(usuario: dict) -> bool:
    """Check if user is administrator."""
    if not usuario:
        return False
    numero = usuario.get('numero', '')
    if numero == ADMIN_NUMERO:
        return True
    return usuario.get('permissoes_id') == 1 or usuario.get('tipo') in ('admin', 'Administrador')


def eh_admin_ou_tecnico(usuario: dict) -> bool:
    """Check if user is admin or tecnico."""
    if not usuario:
        return False
    numero = usuario.get('numero', '')
    if numero == ADMIN_NUMERO:
        return True
    pid = usuario.get('permissoes_id')
    if pid and int(pid) in (1, 2):
        return True
    return usuario.get('tipo') in ('admin', 'tecnico', 'Administrador', 'Tecnico')


def eh_cliente(usuario: dict) -> bool:
    """Check if user is a client (can only see their own data)."""
    if not usuario:
        return False
    return usuario.get('tipo') == 'cliente' or (
        usuario.get('permissoes_id') and int(usuario.get('permissoes_id', 0)) in (5, 6)
    )


# ============================================================
# Core webhook processing
# ============================================================

def process_webhook(payload: dict, api_key: str) -> dict:
    """
    Main webhook processing entry-point.

    Called from main.py's FastAPI route.  Returns a dict describing the
    outcome so the route can return it as JSON.
    """
    # --- API key check ---
    if config.AGENT_API_KEY and api_key != config.AGENT_API_KEY:
        from fastapi import HTTPException
        raise HTTPException(status_code=401, detail="Unauthorized")

    event = payload.get('event', '')
    logger.info(f"Webhook recebido: event={event}")

    if event not in ('Message', 'MESSAGES_UPSERT', ''):
        return {"status": "ignorado", "motivo": f"evento_{event}"}

    numero = extrair_numero(payload)
    if not numero:
        if config.DEBUG:
            logger.debug(f"Payload sem numero: {json.dumps(payload, indent=2, ensure_ascii=False)[:500]}")
        return {"status": "ignorado", "motivo": "sem numero"}

    numero = limpar_numero(numero)

    if _is_from_me(payload):
        return {"status": "ignorado", "motivo": "mensagem propria"}

    msg_id = _extrair_msg_id(payload)
    if _is_duplicado(msg_id):
        logger.info(f"Mensagem duplicada ignorada: msg_id={msg_id}")
        return {"status": "ignorado", "motivo": "duplicada"}

    audio_info = extrair_audio_info(payload)
    texto = ''

    if audio_info.get('tem_audio'):
        logger.info(f"Audio recebido de {numero} ({audio_info.get('segundos', 0)}s)")

        transcricao = None
        audio_file_to_cleanup = None

        if audio_info.get('url') and audio_info.get('media_key_b64'):
            logger.info("Baixando e descriptografando audio do WhatsApp...")
            dec_result = download_and_decrypt_audio(audio_info['url'], audio_info['media_key_b64'])
            if dec_result.get('success'):
                transcricao = transcrever_audio(audio_file=dec_result['file_path'])
                audio_file_to_cleanup = dec_result['file_path']
            else:
                logger.warning(f"Falha ao descriptografar audio: {dec_result.get('error')}")

        if not transcricao and audio_info.get('url'):
            if '.enc' not in audio_info.get('url', ''):
                transcricao = transcrever_audio(audio_url=audio_info['url'])

        if not transcricao and audio_info.get('msg_id'):
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
            evo.enviar_texto(numero, "❌ Nao consegui entender o audio. Pode enviar por texto?")
            registrar_log(numero, 'entrada', '[AUDIO] falha transcricao', 'erro_audio', 'erro')
            return {"status": "erro_audio", "erro": erro}
    else:
        texto = extrair_mensagem(payload)
        if not texto:
            return {"status": "ignorado", "motivo": "sem mensagem"}
        registrar_log(numero, 'entrada', texto)

    # Identify user
    usuario = identificar_usuario(numero)

    if not usuario:
        resposta = """Ola! 👋 Seu numero nao esta vinculado ao nosso sistema.

Entre em contato com nossa equipe para cadastrar seu WhatsApp."""
        registrar_log(numero, 'saida', resposta, 'nao_cadastrado', 'respondido')
        evo.enviar_texto(numero, resposta)
        return {"status": "nao_cadastrado"}

    execute_update(
        "UPDATE whatsapp_integracao SET ultima_interacao = NOW() WHERE numero_telefone = :numero",
        {'numero': numero}
    )

    # Clean up expired sessions
    sessions.cleanup_expired()
    agora_dedup = time.time()
    with _msg_ids_lock:
        expirados_msg = [k for k, v in _msg_ids.items() if agora_dedup - v > _DEDUP_TTL]
        for k in expirados_msg:
            del _msg_ids[k]

    # --- OS creation session active? ---
    os_session = sessions.get_os_session(numero)
    if os_session:
        if os_session.get('expired'):
            msg = "⏰ Sessao expirada por inatividade (15 min).\n\nDigite *ajuda* para recomecar."
            evo.enviar_texto(numero, msg)
            registrar_log(numero, 'saida', msg, 'sessao_expirada', 'respondido')
            return {"status": "ok", "comando": "sessao_expirada", "numero": numero}

        texto_processado = texto
        if audio_info.get('tem_audio') and config.LLM_PROVIDER:
            try:
                resultado_audio = interpretar_audio_os(
                    texto, os_session.get('etapa', ''), os_session.get('dados', {})
                )
                if resultado_audio:
                    logger.info(f"Audio OS interpretado: {resultado_audio}")
                    extras = resultado_audio.get('dados_extras') or {}
                    etapa_detectada = resultado_audio.get('etapa_detectada', '')
                    texto_processado = resultado_audio.get('texto_processado', texto)

                    if extras.get('tipo_item') and os_session.get('etapa') == 'produto_servico':
                        texto_processado = extras['tipo_item']
                    if extras.get('nome_busca') and os_session.get('etapa') == 'buscar_item':
                        texto_processado = extras['nome_busca']
                    if extras.get('valor') is not None and os_session.get('etapa') == 'valor':
                        texto_processado = str(extras['valor'])
                    if extras.get('quantidade') is not None and os_session.get('etapa') == 'quantidade_item':
                        texto_processado = str(int(extras['quantidade']))

                    has_extras = any(v for k, v in extras.items()
                                     if k in ('cliente_nome', 'defeito', 'descricao') and v)
                    if has_extras and os_session.get('etapa') in ('cliente', 'defeito', 'equipamento'):
                        partes = []
                        if extras.get('cliente_nome'):
                            partes.append(f"para {extras['cliente_nome']}")
                        if extras.get('defeito'):
                            partes.append(f"defeito {extras['defeito']}")
                        if extras.get('descricao'):
                            partes.append(f"equipamento {extras['descricao']}")
                        if partes:
                            texto_processado = ' '.join(partes)
            except Exception as e:
                logger.warning(f"Erro ao interpretar audio com LLM: {e}")

        # Import here to avoid circular dependency at module level
        from services.os_creation import processar_criacao_os
        resposta = processar_criacao_os(numero, texto_processado, usuario)
        if resposta:
            resultado_envio = evo.enviar_texto(numero, resposta)
        else:
            resultado_envio = Result.ok()
        registrar_log(numero, 'saida', resposta or '[BOTOES INTERATIVOS]', 'criar_os_interativo', 'respondido' if resultado_envio.success else 'erro')
        return {
            "status": "ok",
            "comando": "criar_os_interativo",
            "numero": numero,
            "tipo_mensagem": "audio" if audio_info.get('tem_audio') else "texto",
            "texto_processado": texto,
            "envio_success": resultado_envio.success
        }

    # --- Status change session active? ---
    status_session = sessions.get_status_session(numero)
    if status_session:
        if status_session.get('expired'):
            msg = "⏰ Sessao expirada por inatividade (15 min).\n\nDigite *ajuda* para recomecar."
            evo.enviar_texto(numero, msg)
            registrar_log(numero, 'saida', msg, 'sessao_expirada', 'respondido')
            return {"status": "ok", "comando": "sessao_expirada", "numero": numero}

        from services.os_status import processar_alterar_status
        resposta = processar_alterar_status(numero, texto, usuario)
        if resposta:
            resultado_envio = evo.enviar_texto(numero, resposta)
        else:
            resultado_envio = Result.ok()
        registrar_log(numero, 'saida', resposta or '[BOTOES INTERATIVOS]', 'alterar_status_interativo', 'respondido' if resultado_envio.success else 'erro')
        return {
            "status": "ok",
            "comando": "alterar_status_interativo",
            "numero": numero,
            "tipo_mensagem": "audio" if audio_info.get('tem_audio') else "texto",
            "texto_processado": texto,
            "envio_success": resultado_envio.success
        }

    # --- Classify intent ---
    if config.LLM_PROVIDER:
        resultado = classificar_com_llm(texto)
        if resultado and resultado.get('intencao') != 'desconhecido':
            comando = resultado['intencao']
            params = resultado.get('entidades', {})
        else:
            comando, params = nlp.classificar(texto)
    else:
        comando, params = nlp.classificar(texto)

    logger.info(f"Classificacao: texto={texto!r} comando={comando} params={params}")

    # --- Fallback: detect criar_os by keywords in audio ---
    if audio_info.get('tem_audio') and comando != 'criar_os':
        texto_lower = texto.lower()
        palavras_criar_os = [
            'criar os', 'abrir os', 'nova os', 'cadastrar os',
            'criar ordem', 'abrir ordem', 'nova ordem',
            'ordem de serviço', 'ordem de servico',
            'quero uma os', 'preciso de uma os',
            'abri uma os', 'abrir uma os',
            'fazer uma os', 'gerar uma os',
            'os para', 'os pra',
            'abrir chamado', 'novo chamado',
        ]
        if any(p in texto_lower for p in palavras_criar_os):
            logger.info(f"Audio com intencao criar_os detectada por palavras-chave (classificacao original: {comando}): {texto!r}")
            comando = 'criar_os'
            params = {}

    # --- Full OS creation from audio ---
    if comando == 'criar_os' and audio_info.get('tem_audio') and config.LLM_PROVIDER:
        from services.os_creation import criar_os_completa_via_audio
        resultado_audio_os = criar_os_completa_via_audio(numero, texto, usuario)
        if resultado_audio_os:
            resultado_envio = evo.enviar_texto(numero, resultado_audio_os)
            registrar_log(numero, 'saida', resultado_audio_os[:200], 'criar_os_audio', 'respondido' if resultado_envio.success else 'erro')
            return {
                "status": "ok",
                "comando": "criar_os_audio",
                "numero": numero,
                "tipo_mensagem": "audio",
                "texto_processado": texto,
                "envio_success": resultado_envio.success
            }
        # If full creation failed, fall through to interactive flow

    # --- Process command ---
    from services.command_router import processar_comando
    from services.dashboard_helpers import increment_stat
    increment_stat('comandos_executados')
    dados = processar_comando(comando, params, usuario, texto)

    # --- Help: send interactive menu ---
    if comando == 'ajuda':
        resultado = _enviar_menu_interativo(numero, usuario)
        if not resultado.success:
            resposta = nlp.formatar_resposta(comando, dados, usuario)
            resultado = evo.enviar_texto(numero, resposta)
        registrar_log(numero, 'saida', '[MENU INTERATIVO]', comando, 'respondido' if resultado.success else 'erro')
        return {
            "status": "ok",
            "comando": comando,
            "numero": numero,
            "tipo_mensagem": "audio" if audio_info.get('tem_audio') else "texto",
            "texto_processado": texto,
            "envio_success": resultado.success
        }

    # --- Format and send response ---
    if dados.get('mensagem'):
        resposta = dados['mensagem']
    else:
        resposta = nlp.formatar_resposta(comando, dados, usuario)

    if not resposta:
        return {
            "status": "ok",
            "comando": comando,
            "numero": numero,
            "tipo_mensagem": "audio" if audio_info.get('tem_audio') else "texto",
            "texto_processado": texto,
            "envio_success": True
        }

    resultado = evo.enviar_texto(numero, resposta)

    status_envio = 'respondido' if resultado.success else 'erro'
    registrar_log(numero, 'saida', resposta, comando, status_envio)

    # --- LLM analysis for reports ---
    tipos_relatorio = [
        'relatorio_financeiro', 'relatorio_vendas', 'relatorio_estoque',
        'relatorio_produtividade', 'relatorio_clientes_top',
        'relatorio_os_periodo', 'relatorio_atrasados', 'os_finalizadas_mes'
    ]
    if comando in tipos_relatorio and config.LLM_CLOUD_URL:
        from services.reports import analisar_relatorio_com_glm
        analise = analisar_relatorio_com_glm(comando, dados)
        if analise:
            msg_analise = f"\n\n🤖 *Analise IA:*\n{analise}"
            evo.enviar_texto(numero, msg_analise)

    # --- Send PDF if available ---
    pdf_url = dados.get('pdf_url')
    if pdf_url:
        from services.reports import enviar_pdf_whatsapp
        enviar_pdf_whatsapp(numero, pdf_url, f"Relatorio - {comando.replace('relatorio_', '').replace('_', ' ').title()}")

    # --- Forward to n8n if configured ---
    if config.N8N_WEBHOOK_URL:
        import threading
        threading.Thread(target=forward_to_n8n, args=(payload,), daemon=True).start()

    return {
        "status": "ok",
        "comando": comando,
        "numero": numero,
        "tipo_mensagem": "audio" if audio_info.get('tem_audio') else "texto",
        "texto_processado": texto,
        "envio_success": resultado.success
    }


# ============================================================
# Helpers called from process_webhook
# ============================================================

def _enviar_menu_interativo(numero: str, usuario: dict) -> dict:
    """Send interactive list menu according to user profile."""
    menu = _menu_lista(usuario)
    resultado = evo.enviar_lista(
        numero=numero,
        title=menu['title'],
        description=menu['description'],
        button_text=menu['buttonText'],
        sections=menu['sections'],
        footer=menu.get('footer', '')
    )
    return resultado


def _menu_lista(usuario: dict) -> dict:
    """Build the interactive menu list according to user profile."""
    tipo = usuario.get('tipo_vinculo', 'desconhecido') if usuario else 'desconhecido'
    perm_id = usuario.get('permissoes_id') if usuario else None
    nome = usuario.get('nome', 'Cliente') if usuario else 'Cliente'
    primeiro_nome = nome.split()[0] if nome else 'Cliente'

    if perm_id and int(perm_id) == 1:
        perfil = 'admin'
    elif perm_id and int(perm_id) == 2:
        perfil = 'tecnico'
    elif tipo == 'cliente' or (perm_id and int(perm_id) in (5, 6)):
        perfil = 'cliente'
    elif tipo in ('admin', 'Administrador'):
        perfil = 'admin'
    elif tipo in ('tecnico', 'Tecnico'):
        perfil = 'tecnico'
    elif tipo == 'financeiro':
        perfil = 'admin'
    elif tipo == 'vendedor':
        perfil = 'tecnico'
    else:
        perfil = tipo

    if perfil == 'cliente':
        sections = [{
            'title': '📋 Meus Dados',
            'rows': [
                {'title': '📋 Status das minhas OS', 'description': 'Ver suas ordens de servico', 'rowId': 'status_os'},
                {'title': '💰 Quanto devo', 'description': 'Consultar valor em aberto', 'rowId': 'quanto_devo'},
                {'title': '🔍 Detalhes da OS', 'description': 'Ver detalhes de uma OS pelo numero', 'rowId': 'detalhes_os'},
            ]
        }]
    elif perfil == 'tecnico':
        sections = [
            {
                'title': '📋 Minhas OS',
                'rows': [
                    {'title': '📋 OS de hoje', 'description': 'Suas ordens de servico do dia', 'rowId': 'minhas_os_hoje'},
                    {'title': '🔍 Detalhes da OS', 'description': 'Ver detalhes de uma OS', 'rowId': 'detalhes_os'},
                    {'title': '📊 Relatorio de OS', 'description': 'Resumo de OS do dia', 'rowId': 'relatorio_os'},
                    {'title': '⚠️ OS atrasadas', 'description': 'Servicos em atraso', 'rowId': 'os_atrasadas'},
                    {'title': '👷 Produtividade', 'description': 'Desempenho da equipe', 'rowId': 'relatorio_produtividade'},
                ]
            },
            {
                'title': '📍 Atendimento',
                'rows': [
                    {'title': '📍 Cheguei na OS', 'description': 'Check-in no atendimento', 'rowId': 'checkin_tecnico'},
                    {'title': '🚪 Saindo da OS', 'description': 'Check-out do atendimento', 'rowId': 'checkout_tecnico'},
                ]
            }
        ]
    else:
        # Admin
        sections = [
            {
                'title': '📋 Operacional',
                'rows': [
                    {'title': '📊 Relatorio de OS', 'description': 'Resumo de OS do dia', 'rowId': 'relatorio_os'},
                    {'title': '⚠️ OS atrasadas', 'description': 'Servicos em atraso', 'rowId': 'os_atrasadas'},
                    {'title': '📋 Total OS abertas', 'description': 'Quantidade em aberto', 'rowId': 'total_os_abertas'},
                    {'title': '🔍 Detalhes da OS', 'description': 'Ver OS especifica pelo numero', 'rowId': 'detalhes_os'},
                ]
            },
            {
                'title': '💰 Financeiro',
                'rows': [
                    {'title': '📈 Relatorio financeiro', 'description': 'Receitas, despesas e lucro', 'rowId': 'relatorio_financeiro'},
                    {'title': '📊 Relatorio vendas', 'description': 'Vendas do periodo', 'rowId': 'relatorio_vendas'},
                    {'title': '📄 Cobrancas vencidas', 'description': 'Cobrancas atrasadas', 'rowId': 'cobrancas_vencidas'},
                    {'title': '💰 Vendas pendentes', 'description': 'Vendas nao faturadas', 'rowId': 'vendas_pendentes'},
                    {'title': '💸 OS finalizadas', 'description': 'OS concluidas para cobranca', 'rowId': 'os_finalizadas_mes'},
                ]
            },
            {
                'title': '📦 Gestao',
                'rows': [
                    {'title': '📦 Relatorio estoque', 'description': 'Produtos e alertas', 'rowId': 'relatorio_estoque'},
                    {'title': '👷 Produtividade', 'description': 'Desempenho dos tecnicos', 'rowId': 'relatorio_produtividade'},
                    {'title': '🏆 Top clientes', 'description': 'Clientes que mais trazem', 'rowId': 'relatorio_clientes_top'},
                    {'title': '📅 OS do mes', 'description': 'OS por periodo', 'rowId': 'relatorio_os_periodo'},
                    {'title': '📋 Relatorio atrasados', 'description': 'Clientes com OS em atraso', 'rowId': 'relatorio_atrasados'},
                ]
            },
            {
                'title': '📝 Acoes',
                'rows': [
                    {'title': '📝 Criar OS', 'description': 'Abrir nova ordem de servico', 'rowId': 'criar_os'},
                    {'title': '🔄 Alterar status', 'description': 'Mudar status de uma OS', 'rowId': 'alterar_status_os'},
                    {'title': '📍 Cheguei na OS', 'description': 'Check-in no atendimento', 'rowId': 'checkin_tecnico'},
                    {'title': '🚪 Saindo da OS', 'description': 'Check-out do atendimento', 'rowId': 'checkout_tecnico'},
                ]
            },
        ]

    return {
        'numero': usuario.get('numero', '') if usuario else '',
        'title': f'Ola {primeiro_nome}! JJ Ferreiras',
        'description': 'Escolha uma opcao no menu abaixo:',
        'buttonText': 'Ver opcoes',
        'sections': sections,
        'footer': 'JJ Ferreiras'
    }


def _enviar_botoes_confirmacao(numero: str, title: str, description: str,
                                opcoes: list = None, footer: str = ''):
    """Send interactive confirmation buttons."""
    if not opcoes:
        opcoes = [
            {'displayText': '✅ Confirmar', 'id': 'CONFIRMAR'},
            {'displayText': '❌ Cancelar', 'id': 'CANCELAR'},
        ]
    buttons = [{'type': 'reply', 'displayText': o['displayText'], 'id': o['id']} for o in opcoes[:3]]
    return evo.enviar_botoes(numero, title, description, buttons, footer)


def forward_to_n8n(payload: dict):
    """Forward the raw payload to an n8n webhook if configured."""
    n8n_url = config.N8N_WEBHOOK_URL
    if not n8n_url:
        return
    try:
        http_requests.post(n8n_url, json=payload, timeout=5)
        logger.debug(f"Payload repassado para n8n: {n8n_url}")
    except Exception as e:
        logger.warning(f"Falha ao repassar para n8n: {e}")