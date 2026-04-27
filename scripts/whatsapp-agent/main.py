from fastapi import FastAPI, Request, Header, HTTPException
from fastapi.responses import JSONResponse
import json
import config

from database import execute_query, execute_insert
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services import nlp

app = FastAPI(title="Agente IA WhatsApp - MapOS", version="1.0.0")

# Instancias
evo = EvolutionAPI()
queries = MaposQueries()


# ========== MIDDLEWARE / UTILS ==========

def verificar_api_key(x_api_key: str = Header(None)):
    if not x_api_key or x_api_key != config.AGENT_API_KEY:
        raise HTTPException(status_code=401, detail="API Key invalida")


def extrair_numero(payload: dict) -> str:
    """Extrai numero do remetente do payload do Evolution Go"""
    try:
        data = payload.get('data', {})
        # Tentar varios formatos de payload do Evolution
        key = data.get('key', {})
        remote_jid = key.get('remoteJid', '')
        if remote_jid:
            numero = remote_jid.split('@')[0]
            return numero

        # Formato alternativo
        message = data.get('message', {})
        if 'key' in message:
            remote_jid = message['key'].get('remoteJid', '')
            if remote_jid:
                return remote_jid.split('@')[0]

        # Novo formato v2
        sender = data.get('sender', '') or data.get('senderJid', '')
        if sender:
            return sender.split('@')[0]

        return ''
    except Exception:
        return ''


def extrair_mensagem(payload: dict) -> str:
    """Extrai texto da mensagem do payload"""
    try:
        data = payload.get('data', {})

        # Texto simples
        if 'message' in data:
            msg = data['message']
            if 'conversation' in msg:
                return msg['conversation']
            if 'extendedTextMessage' in msg:
                return msg['extendedTextMessage'].get('text', '')

        # Formato alternativo
        if 'body' in data:
            return data['body']

        return ''
    except Exception:
        return ''


def limpar_numero(numero: str) -> str:
    """Remove tudo exceto digitos e adiciona 55 se necessario"""
    numero = ''.join(filter(str.isdigit, numero))
    if len(numero) == 11 or len(numero) == 10:
        numero = '55' + numero
    return numero


def identificar_usuario(numero: str):
    """Identifica se o numero pertence a cliente, tecnico ou admin"""
    numero = limpar_numero(numero)

    # Buscar vinculo
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
            'conteudo': conteudo[:1000],  # limita tamanho
            'intencao': intencao,
            'status': status
        })
    except Exception as e:
        if config.DEBUG:
            print(f"Erro ao registrar log: {e}")


# ========== ENDPOINTS ==========

@app.get("/health")
async def health():
    """Verifica se o agente esta online e consegue acessar o banco"""
    try:
        result = execute_query("SELECT 1 as ok")
        db_ok = bool(result and result[0].get('ok') == 1)
    except Exception:
        db_ok = False

    return {
        "status": "ok",
        "database": "online" if db_ok else "offline",
        "version": "1.0.0"
    }


@app.post("/webhook/evolution")
async def webhook_evolution(request: Request, x_api_key: str = Header(None)):
    """
    Recebe webhook do Evolution Go quando uma mensagem chega.
    O Evolution Go envia evento 'messages.upsert' para esta URL.
    """
    # Verificar API Key
    if x_api_key != config.AGENT_API_KEY:
        raise HTTPException(status_code=401, detail="Unauthorized")

    try:
        payload = await request.json()
    except Exception:
        payload = {}

    if config.DEBUG:
        print(f"[WEBHOOK] Payload recebido: {json.dumps(payload, indent=2, ensure_ascii=False)}")

    # Extrair dados
    numero = extrair_numero(payload)
    mensagem = extrair_mensagem(payload)

    if not numero or not mensagem:
        return {"status": "ignorado", "motivo": "sem numero ou mensagem"}

    numero = limpar_numero(numero)

    # Ignorar mensagens do proprio bot (evita loop)
    meu_numero = config.EVOLUTION_INSTANCE or ''
    if numero in [meu_numero, '']:
        return {"status": "ignorado", "motivo": "mensagem propria"}

    # Registrar entrada
    registrar_log(numero, 'entrada', mensagem)

    # Identificar usuario
    usuario = identificar_usuario(numero)

    if not usuario:
        # Numero nao cadastrado
        resposta = """Ola! Seu numero nao esta vinculado ao nosso sistema.

Entre em contato com nossa equipe para cadastrar seu WhatsApp.
"""
        registrar_log(numero, 'saida', resposta, 'nao_cadastrado', 'respondido')
        evo.enviar_texto(numero, resposta)
        return {"status": "nao_cadastrado"}

    # Atualizar ultima interacao
    execute_query(
        "UPDATE whatsapp_integracao SET ultima_interacao = NOW() WHERE numero_telefone = :numero",
        {'numero': numero}
    )

    # Classificar comando
    comando, params = nlp.classificar(mensagem)

    # Processar comando
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
            os = queries.buscar_os(os_id)
            dados = {'os': os}
        else:
            # Tentar pegar a ultima OS do usuario
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

    elif comando == 'criar_os':
        # MVP: apenas informa que precisa usar o painel
        dados = {
            'mensagem': f"""{usuario['nome']}, para criar uma OS acesse o painel do MapOS.

Em breve voce podera criar pelo WhatsApp tambem!
"""
        }

    # Formatar resposta
    resposta = nlp.formatar_resposta(comando, dados, usuario)

    # Enviar resposta
    resultado = evo.enviar_texto(numero, resposta)

    # Registrar saida
    status_envio = 'respondido' if resultado.get('success') else 'erro'
    registrar_log(numero, 'saida', resposta, comando, status_envio)

    return {
        "status": "ok",
        "comando": comando,
        "numero": numero,
        "envio_success": resultado.get('success')
    }


# ========== EXECUCAO ==========

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=config.AGENT_PORT)
