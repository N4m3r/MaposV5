from fastapi import FastAPI, Request, Header, HTTPException, Depends
from fastapi.responses import JSONResponse
import json
import logging
import os
import glob as glob_module
import time
import re
import threading
import requests as http_requests
from datetime import datetime
import config
from apscheduler.schedulers.background import BackgroundScheduler

from database import execute_query, execute_insert, execute_update
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services import nlp
from services.nlp import _fmt_status_emoji, _fmt_moeda, _fmt_data
from services.llm import classificar_com_llm, interpretar_audio_os, extrair_dados_os_audio
from services.whisper_asr import transcrever_audio
from services.whatsapp_media import download_and_decrypt_audio
from services.session_store import SessionStore
from services.result import Result
from services.dashboard_helpers import (
    get_stats, get_logs, get_sessions, get_notifications,
    create_notification, delete_notification, delete_session,
    mask_sensitive, get_config_masked, get_config_raw, save_env_config,
    test_evolution, test_whisper, test_database, test_llm,
    increment_stat
)

# Configurar logging
logging.basicConfig(
    level=getattr(logging, config.LOG_LEVEL, logging.INFO),
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

app = FastAPI(title="Agente IA WhatsApp - MapOS", version="3.0.0")

# Instancias
evo = EvolutionAPI()
queries = MaposQueries()
sessions = SessionStore()

# Numero do administrador (acesso total) — lido do .env
ADMIN_NUMERO = config.ADMIN_NUMERO

# Deduplicacao de mensagens (evita processar a mesma mensagem duas vezes)
_msg_ids = {}
_DEDUP_TTL = 60  # segundos para expirar IDs antigos
_msg_ids_lock = threading.Lock()

# Lock para sessao de criacao de OS (evita race condition)
_sessao_lock = threading.Lock()

# Mapeamento de permissoes_id para perfil de acesso
PERMISSOES_MAP = {
    1: 'admin',        # Administrador
    2: 'tecnico',      # Tecnico
    3: 'financeiro',   # Financeiro
    4: 'vendedor',     # Vendedor
    5: 'cliente',      # Cliente
    6: 'cliente',      # Cliente secundario
}


def _menu_lista(usuario: dict) -> dict:
    """Constroi o menu interativo de lista conforme o perfil do usuario."""
    tipo = usuario.get('tipo_vinculo', 'desconhecido') if usuario else 'desconhecido'
    perm_id = usuario.get('permissoes_id') if usuario else None
    nome = usuario.get('nome', 'Cliente') if usuario else 'Cliente'
    primeiro_nome = nome.split()[0] if nome else 'Cliente'

    # Determinar perfil
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


def _enviar_menu_interativo(numero: str, usuario: dict) -> dict:
    """Envia o menu interativo (lista) conforme o perfil do usuario."""
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


def _enviar_botoes_confirmacao(numero: str, title: str, description: str,
                                opcoes: list = None, footer: str = ''):
    """Envia botoes interativos de confirmacao.
    opcoes: lista de dicts [{'displayText': '...', 'id': '...'}] (max 3)
    Default: Confirmar / Cancelar
    """
    if not opcoes:
        opcoes = [
            {'displayText': '✅ Confirmar', 'id': 'CONFIRMAR'},
            {'displayText': '❌ Cancelar', 'id': 'CANCELAR'},
        ]
    buttons = [{'type': 'reply', 'displayText': o['displayText'], 'id': o['id']} for o in opcoes[:3]]
    return evo.enviar_botoes(numero, title, description, buttons, footer)


def _criar_sessao_os(numero: str, etapa: str, dados: dict, clientes: list = None):
    """Cria sessao de OS persistida no banco."""
    sessions.set_os_session(numero, etapa, dados, clientes)


def _get_sessao_os(numero: str) -> dict | None:
    """Recupera sessao de OS do banco."""
    return sessions.get_os_session(numero)


def _del_sessao_os(numero: str):
    """Remove sessao de OS do banco."""
    sessions.del_os_session(numero)


def _criar_sessao_status(numero: str, etapa: str, dados: dict):
    """Cria sessao de alteracao de status persistida no banco."""
    sessions.set_status_session(numero, etapa, dados)


def _get_sessao_status(numero: str) -> dict | None:
    """Recupera sessao de status do banco."""
    return sessions.get_status_session(numero)


def _del_sessao_status(numero: str):
    """Remove sessao de status do banco."""
    sessions.del_status_session(numero)


def _avancar_etapa_os(numero: str, dados: dict, etapa_atual: str) -> str | None:
    """Verifica dados ja preenchidos e avanca para a proxima etapa vazia.
    Retorna a resposta se avancou automaticamente, ou None se precisa interagir com o usuario."""
    # Se defeito ja esta preenchido, pular para equipamento
    if etapa_atual == 'defeito' and dados.get('defeito'):
        # Verificar se equipamento tambem esta preenchido
        if dados.get('descricao') and dados['descricao'] != 'Nao especificado':
            # Tem defeito e equipamento — pular para produto/servico ou confirmar
            if dados.get('itens') or dados.get('_item_selecionado'):
                # Ja tem item, ir para valor ou confirmar
                if dados.get('valor_total') is not None:
                    # Tem tudo, ir para confirmar
                    dados_avancados = dict(dados)
                    _criar_sessao_os(numero, 'confirmar', dados_avancados)
                    resumo = (
                        f"👤 Cliente: *{dados.get('cliente_nome', 'Nao informado')}*\n"
                        f"🔧 Defeito: *{dados.get('defeito', 'Nao informado')}*\n"
                        f"📦 Equipamento: *{dados.get('descricao', 'Nao informado')}*"
                    )
                    if dados.get('_item_selecionado'):
                        resumo += f"\n📋 Item: *{dados['_item_selecionado']}*"
                    if dados.get('valor_total'):
                        resumo += f"\n💰 Valor: *R$ {dados['valor_total']:.2f}*"
                    _enviar_botoes_confirmacao(
                        numero, title='📝 Confirmar OS', description=resumo,
                        opcoes=[
                            {'displayText': '✅ Confirmar', 'id': 'CONFIRMAR'},
                            {'displayText': '❌ Cancelar', 'id': 'CANCELAR'},
                            {'displayText': '✏️ Corrigir cliente', 'id': 'CORRIGIR'},
                        ], footer='JJ Ferreiras'
                    )
                    return ''
                # Tem defeito, equipamento e item mas sem valor — pular para produto_servico
                dados_avancados = dict(dados)
                dados_avancados['defeito'] = dados.get('defeito', '')
                dados_avancados['descricao'] = dados.get('descricao', '')
                _criar_sessao_os(numero, 'produto_servico', dados_avancados)
                evo.enviar_botoes(numero,
                    title='📦 Produto/Servico',
                    description='Deseja adicionar um produto ou servico do catalogo?',
                    buttons=[
                        {'type': 'reply', 'displayText': '📦 Produto', 'id': 'TIPO_PRODUTO'},
                        {'type': 'reply', 'displayText': '🔧 Servico', 'id': 'TIPO_SERVICO'},
                        {'type': 'reply', 'displayText': '⏭ Pular', 'id': 'PULAR_ITEM'},
                    ], footer='JJ Ferreiras'
                )
                return '🎤 Dados ja preenchidos do audio!\nDeseja adicionar um *produto* ou *servico* do catalogo?\nDigite *produto*, *servico* ou *pular*.'
            # Tem defeito e equipamento mas sem item
            dados_avancados = dict(dados)
            dados_avancados['defeito'] = dados.get('defeito', '')
            dados_avancados['descricao'] = dados.get('descricao', '')
            _criar_sessao_os(numero, 'produto_servico', dados_avancados)
            evo.enviar_botoes(numero,
                title='📦 Produto/Servico',
                description='Deseja adicionar um produto ou servico do catalogo?',
                buttons=[
                    {'type': 'reply', 'displayText': '📦 Produto', 'id': 'TIPO_PRODUTO'},
                    {'type': 'reply', 'displayText': '🔧 Servico', 'id': 'TIPO_SERVICO'},
                    {'type': 'reply', 'displayText': '⏭ Pular', 'id': 'PULAR_ITEM'},
                ], footer='JJ Ferreiras'
            )
            return '🎤 Dados preenchidos do audio!\nDeseja adicionar um *produto* ou *servico*?\nDigite *produto*, *servico* ou *pular*.'

        # Tem defeito mas nao equipamento — pular para equipamento
        dados_avancados = dict(dados)
        dados_avancados['defeito'] = dados.get('defeito', '')
        _criar_sessao_os(numero, 'equipamento', dados_avancados)
        evo.enviar_botoes(numero,
            title=f'Defeito: {dados["defeito"][:50]}',
            description='Informe o equipamento ou produto:',
            buttons=[
                {'type': 'reply', 'displayText': '⏭ Pular equipamento', 'id': 'PULAR_EQUIP'},
            ], footer='JJ Ferreiras'
        )
        return f"✅ Defeito preenchido: *{dados['defeito']}*\n🔧 Informe o *equipamento/produto* (ou *pular*):"

    return None


# ========== MIDDLEWARE / UTILS ==========

def eh_admin(usuario: dict) -> bool:
    """Verifica se o usuario e administrador (permissoes_id = 1 ou numero admin)."""
    if not usuario:
        return False
    numero = usuario.get('numero', '')
    if numero == ADMIN_NUMERO:
        return True
    return usuario.get('permissoes_id') == 1 or usuario.get('tipo') in ('admin', 'Administrador')


def eh_admin_ou_tecnico(usuario: dict) -> bool:
    """Verifica se o usuario e admin ou tecnico (pode acessar relatorios e alterar OS)."""
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
    """Verifica se o usuario e cliente (so pode ver seus proprios dados)."""
    if not usuario:
        return False
    return usuario.get('tipo') == 'cliente' or (
        usuario.get('permissoes_id') and int(usuario.get('permissoes_id', 0)) in (5, 6)
    )

def verificar_api_key(x_api_key: str = Header(None)):
    """Exige API Key valida. Nao aceita chave vazia ou ausente."""
    if not config.AGENT_API_KEY:
        # Se nao ha chave configurada, permitir (modo desenvolvimento)
        return
    if x_api_key == config.AGENT_API_KEY:
        return
    raise HTTPException(status_code=401, detail="API Key invalida")


def extrair_numero(payload: dict) -> str:
    """Extrai numero do remetente do payload do Evolution Go v0.7+"""
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


def _is_from_me(payload: dict) -> bool:
    data = payload.get('data', {})
    info = data.get('Info', {})
    if info:
        return info.get('IsFromMe', False)
    key = data.get('key', {})
    return key.get('fromMe', False)


def _extrair_msg_id(payload: dict) -> str:
    data = payload.get('data', {})
    info = data.get('Info', {})
    if info and info.get('ID'):
        return info.get('ID', '')
    key = data.get('key', {})
    if key and key.get('id'):
        return key['id']
    return ''


def _is_duplicado(msg_id: str) -> bool:
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


def extrair_mensagem(payload: dict) -> str:
    try:
        data = payload.get('data', {})

        msg = data.get('Message', {})
        if msg:
            if 'conversation' in msg:
                return msg['conversation']
            if 'extendedTextMessage' in msg:
                return msg['extendedTextMessage'].get('text', '')
            # Mensagem interativa: botao
            buttons_resp = msg.get('buttonsResponseMessage', {})
            if buttons_resp:
                return buttons_resp.get('selectedDisplayText', '') or buttons_resp.get('selectedId', '')
            # Mensagem interativa: lista
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
            # Mensagem interativa: botao
            buttons_resp = msg.get('buttonsResponseMessage', {})
            if buttons_resp:
                return buttons_resp.get('selectedDisplayText', '') or buttons_resp.get('selectedId', '')
            # Mensagem interativa: lista
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


def limpar_numero(numero: str) -> str:
    numero = ''.join(filter(str.isdigit, numero))
    if len(numero) == 11 or len(numero) == 10:
        numero = '55' + numero
    return numero


def identificar_usuario(numero: str):
    """Identifica se o numero pertence a um admin, tecnico ou cliente.
    Permissoes derivadas do permissoes_id do usuarios, nao do campo tipo_vinculo.
    Admin numero 92992150107 sempre tem acesso total.
    """
    numero = limpar_numero(numero)

    # Admin master - acesso total independente do cadastro
    if numero == ADMIN_NUMERO:
        # Buscar dados do admin no banco se existir
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
        # Fallback: admin nao encontrado no banco, mas numero reconhecido
        return {
            'tipo': 'admin',
            'tipo_vinculo': 'admin',
            'clientes_id': None,
            'usuarios_id': None,
            'permissoes_id': 1,
            'nome': 'Administrador',
            'numero': numero
        }

    # Buscar na tabela whatsapp_integracao com JOIN completo
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

        # Se tem usuario do sistema, determinar tipo pelo permissoes_id
        if usuarios_id and permissoes_id:
            perfil = PERMISSOES_MAP.get(int(permissoes_id), 'desconhecido')
            nome = row.get('nome_usuario') or row.get('nome') or 'Usuario'

            # Admin tambem herda acesso de cliente se tiver clientes_id
            return {
                'tipo': perfil,
                'tipo_vinculo': perfil,
                'clientes_id': clientes_id if perfil == 'cliente' else None,
                'usuarios_id': usuarios_id,
                'permissoes_id': int(permissoes_id),
                'nome': nome,
                'numero': numero
            }

        # Se so tem cliente vinculado
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

        # Tem registro mas sem vinculacao clara - usar tipo_vinculo antigo
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

    # Segunda tentativa: buscar numero diretamente na tabela usuarios (celular)
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

        # Cadastrar automaticamente na whatsapp_integracao
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

    # Terceira tentativa: buscar numero na tabela clientes (celular)
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

        # Cadastrar automaticamente na whatsapp_integracao
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


def registrar_log(numero: str, direcao: str, conteudo: str, intencao: str = None, status: str = 'recebido'):
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


# ========== GERACAO DE PDF VIA MAPOS API ==========

def gerar_pdf_relatorio(tipo: str, numero: str) -> str:
    """Gera PDF do relatorio via API do MapOS e retorna a URL de download."""
    mapos_url = config.MAPOS_URL.rstrip('/')
    api_key = config.MAPOS_API_KEY

    if not mapos_url or not api_key:
        logger.warning("MAPOS_URL ou MAPOS_API_KEY nao configurados")
        return ''

    url = f"{mapos_url}/api/v2/relatorios/exportar"

    try:
        resp = http_requests.post(url, data={
            'tipo': tipo,
            'formato': 'pdf',
        }, headers={
            'X-API-KEY': api_key,
        }, timeout=30)

        if resp.status_code == 200:
            data = resp.json()
            if data.get('success'):
                return data.get('data', {}).get('download_url', '')
        logger.warning(f"Erro ao gerar PDF: status={resp.status_code}")
        return ''
    except Exception as e:
        logger.error(f"Excecao ao gerar PDF: {e}")
        return ''


def enviar_pdf_whatsapp(numero: str, pdf_url: str, caption: str = 'Relatorio'):
    """Baixa o PDF e envia via WhatsApp."""
    if not pdf_url:
        return Result.fail('URL vazia')

    try:
        # Baixar o PDF
        resp = http_requests.get(pdf_url, timeout=60)
        if resp.status_code != 200:
            logger.error(f"Erro ao baixar PDF: status={resp.status_code}")
            return Result.fail(f'HTTP {resp.status_code}')

        # Salvar temporariamente
        import tempfile
        import os
        tmp = tempfile.NamedTemporaryFile(suffix='.pdf', delete=False)
        tmp.write(resp.content)
        tmp.close()

        # Enviar via Evolution API
        try:
            result = evo.enviar_documento(numero, tmp.name, caption)
        finally:
            try:
                os.unlink(tmp.name)
            except Exception:
                pass

        return result
    except Exception as e:
        logger.error(f"Erro ao enviar PDF: {e}")
        return Result.fail(str(e))



# ========== CRIACAO COMPLETA VIA AUDIO ==========

def criar_os_completa_via_audio(numero: str, texto_audio: str, usuario: dict) -> str | None:
    """Tenta criar OS completa a partir de um unico audio usando LLM para extrair todos os dados.
    Retorna a mensagem de resposta ou None se nao conseguiu extrair dados suficientes."""
    if not config.LLM_PROVIDER:
        return None

    if not eh_admin(usuario):
        return "🔒 Apenas administradores podem criar OS."

    try:
        dados_os = extrair_dados_os_audio(texto_audio)
        if not dados_os:
            logger.info(f"Nao foi possivel extrair dados OS do audio: {texto_audio!r}")
            return None

        logger.info(f"Dados extraidos do audio: {dados_os}")
    except Exception as e:
        logger.warning(f"Erro ao extrair dados OS do audio: {e}")
        return None

    # Verificar se temos dados minimos (pelo menos cliente OU defeito)
    cliente_nome = dados_os.get('cliente')
    defeito = dados_os.get('defeito')
    equipamento = dados_os.get('equipamento')
    tipo_item = dados_os.get('tipo_item')
    nome_item = dados_os.get('nome_item')
    quantidade = dados_os.get('quantidade')
    valor = dados_os.get('valor')

    if not cliente_nome and not defeito:
        return None

    # Buscar cliente
    cliente_id = None
    cliente_final = None
    if cliente_nome:
        clientes = queries.buscar_cliente_por_nome(cliente_nome)
        if len(clientes) == 1:
            cliente_id = clientes[0]['idClientes']
            cliente_final = clientes[0].get('nomeExibicao', clientes[0]['nomeCliente'])
        elif len(clientes) > 1:
            # Multiplos clientes — nao conseguimos resolver automaticamente
            # Iniciar fluxo interativo com os dados que temos
            _criar_sessao_os(numero, 'escolher_cliente_lista', {
                'defeito': defeito or '',
                'descricao': equipamento or '',
            }, clientes[:10])
            sections = [{
                'title': f'{len(clientes)} clientes encontrados',
                'rows': [
                    {'title': c.get('nomeExibicao', c['nomeCliente']),
                     'description': f'ID: {c["idClientes"]}',
                     'rowId': f'cliente_{c["idClientes"]}'}
                    for c in clientes[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
            return f"🎤 Encontrei {len(clientes)} clientes para *\"{cliente_nome}\"*.\nSelecione o cliente correto:"
        else:
            # Cliente nao encontrado — iniciar fluxo pedindo o cliente
            _criar_sessao_os(numero, 'cliente', {
                'defeito': defeito or '',
                'descricao': equipamento or '',
            })
            return f"🎤 Nao encontrei o cliente *\"{cliente_nome}\"*.\n\nQual o nome, CNPJ ou Loja do cliente?"

    # Se nao tem cliente e o usuario e cliente, usar o proprio ID
    if not cliente_id and usuario.get('tipo') == 'cliente' and usuario.get('clientes_id'):
        cliente_id = usuario['clientes_id']
        cliente_final = usuario['nome']

    if not cliente_id:
        _criar_sessao_os(numero, 'cliente', {
            'defeito': defeito or '',
            'descricao': equipamento or '',
        })
        return "🎤 Qual o *nome do cliente* para a OS?"

    # Buscar produto/servico no catalogo se mencionado
    itens = []
    item_selecionado = None
    if nome_item and tipo_item:
        if tipo_item == 'produto':
            produtos = queries.buscar_produtos(nome_item, limite=1)
            if produtos:
                p = produtos[0]
                preco = float(p.get('precoVenda', 0) or 0)
                qtd = quantidade or 1
                itens = [{
                    'tipo': 'produto',
                    'id': p['idProdutos'],
                    'descricao': p['descricao'],
                    'preco': preco,
                    'quantidade': qtd,
                }]
                item_selecionado = f"{p['descricao']} x{qtd} — R$ {preco * qtd:.2f}"
        elif tipo_item == 'servico':
            servicos = queries.buscar_servicos(nome_item, limite=1)
            if servicos:
                s = servicos[0]
                preco = float(s.get('preco', 0) or 0)
                qtd = quantidade or 1
                itens = [{
                    'tipo': 'servico',
                    'id': s['idServicos'],
                    'descricao': s['nome'],
                    'preco': preco,
                    'quantidade': qtd,
                }]
                item_selecionado = f"{s['nome']} x{qtd} — R$ {preco * qtd:.2f}"

    # Usar valor informado ou calcular do item
    if valor is None and itens:
        valor = sum(i['preco'] * i['quantidade'] for i in itens)

    # Criar a OS
    usuario_id = usuario.get('usuarios_id') or 1
    try:
        resultado = criar_os_via_api(
            cliente_id=cliente_id,
            descricao=equipamento or 'Nao especificado',
            defeito=defeito or '',
            usuario_id=usuario_id,
            valor_total=valor,
            itens=itens if itens else None,
        )

        if resultado and resultado.get('sucesso') is not False:
            os_id = resultado.get('os_id') or resultado
            os_data = resultado.get('os') or queries.buscar_os(os_id)

            resposta = f"""✅ *OS #{os_id} criada com sucesso!*

━━━━━━━━━━━━━━━━━
👤 Cliente: {os_data.get('nomeCliente', cliente_final or '')}
🔧 Defeito: {os_data.get('defeito', defeito or '')}
📦 Equipamento: {os_data.get('descricaoProduto', equipamento or 'Nao especificado')}"""
            if item_selecionado:
                resposta += f"\n📋 Item: *{item_selecionado}*"
            if valor:
                resposta += f"\n💰 Valor: *R$ {valor:.2f}*"
            resposta += f"""
📋 Status: {os_data.get('status', 'Aberto')}
📅 Data: {os_data.get('dataInicial', 'Hoje')}
━━━━━━━━━━━━━━━━━

Digite *detalhes da OS {os_id}* para acompanhar."""

            # Tentar gerar e enviar PDF da OS
            mapos_url = config.MAPOS_URL.rstrip('/')
            api_key = config.MAPOS_API_KEY
            if mapos_url and api_key:
                try:
                    pdf_url = f"{mapos_url}/api/v2/relatorios/exportar"
                    resp = http_requests.post(pdf_url, data={
                        'tipo': 'os_periodo',
                        'formato': 'pdf',
                    }, headers={
                        'X-API-KEY': api_key,
                    }, timeout=30)
                    if resp.status_code == 200:
                        data_resp = resp.json()
                        if data_resp.get('success'):
                            download_url = data_resp.get('data', {}).get('download_url', '')
                            if download_url:
                                enviar_pdf_whatsapp(numero, download_url, f'OS #{os_id}')
                except Exception as e:
                    logger.warning(f"Erro ao gerar PDF da OS: {e}")

            return resposta
        else:
            return f"❌ Erro ao criar OS. Tente novamente ou digite *ajuda*."

    except Exception as e:
        logger.error(f"Erro ao criar OS via audio: {e}")
        return f"❌ Erro ao criar OS: {str(e)}\n\nTente novamente ou digite *ajuda*."


# ========== CRIACAO INTERATIVA DE OS ==========

def processar_criacao_os(numero: str, texto: str, usuario: dict) -> str:
    """Processa o fluxo interativo de criacao de OS passo a passo."""

    # Verificar se ha sessao ativa
    with _sessao_lock:
        sessao = sessions.get_os_session(numero)
        if sessao and '_clientes' in sessao.get('dados', {}):
            sessao['clientes'] = sessao['dados'].pop('_clientes')

    # Comando para cancelar
    texto_limpo = texto.lower().strip()
    if texto_limpo in ('cancelar', 'cancela', 'desistir', 'abandonar', '#cancelar'):
        sessions.del_os_session(numero)
        return "❌ Criacao de OS cancelada.\n\nDigite *ajuda* para ver as opcoes."

    # Se nao ha sessao, iniciar fluxo
    if not sessao:
        # Buscar clientes que correspondem ao texto (se informou nome)
        cliente_nome = None
        cliente_id = None
        defeito = None
        descricao = None

        # Tentar extrair dados da mensagem inicial
        params = nlp.extrair_parametros(texto, 'criar_os')

        # Extracao inteligente de cliente e defeito da mensagem completa
        _texto_lower = texto.lower().strip()
        import re as _re

        # Verificar se mencionou "loja X" para clientes multi-filial
        loja_num = None
        loja_match = _re.search(r'loja\s*(\d+)', _texto_lower)
        if not loja_match:
            loja_match = _re.search(r'(?:nova\s*era|patio\s*gourmet)\s+(\d+)', _texto_lower)
        if loja_match:
            loja_num = int(loja_match.group(1))

        # Tentar extrair nome do cliente de forma mais ampla
        if not params.get('cliente_nome'):
            # Padroes: "criar os para joao silva", "os para maria", "nova os cliente pedro"
            cliente_match = _re.search(
                r'(?:para|cliente|pra|do|da)\s+([\w\sáàãâéèêíïóòõôúùûçñ]+?)(?:\s*[-,;]|\s+defeito|\s+problema|\s+com\s|$)',
                _texto_lower
            )
            if cliente_match:
                nome_extraido = cliente_match.group(1).strip()
                # Limpar palavras de ligacao
                nome_limpo = _re.sub(r'\b(os|para|cliente|nova|nova ordem|ordem|servico|criar|abrir|cadastrar)\b', '', nome_extraido).strip()
                if len(nome_limpo) >= 3:
                    params['cliente_nome'] = nome_limpo

        # Tentar extrair defeito de forma mais ampla se nao achou
        if not params.get('defeito'):
            defeito_match = _re.search(
                r'(?:defeito|problema|queixa|reclamacao|assunto)\s*[:\-]?\s*(.+?)(?:,|$)',
                _texto_lower
            )
            if defeito_match:
                params['defeito'] = defeito_match.group(1).strip()

        if params.get('cliente_nome'):
            cliente_nome = params['cliente_nome']
            # Buscar cliente no banco
            clientes = queries.buscar_cliente_por_nome(cliente_nome)
            if len(clientes) == 1:
                cliente_id = clientes[0]['idClientes']
                cliente_nome = clientes[0]['nomeCliente']
            elif len(clientes) > 1 and loja_num:
                # Para multi-filial, buscar loja especifica usando _extrair_loja_cnpj
                for c in clientes:
                    loja = queries._extrair_loja_cnpj(c.get('documento', '') or '')
                    if loja == f'Loja {loja_num}':
                        cliente_id = c['idClientes']
                        cliente_nome = c['nomeExibicao'] if c.get('nomeExibicao') else c['nomeCliente']
                        break
            elif len(clientes) > 1:
                # Multiplos clientes - enviar lista interativa
                sections = [{
                    'title': f'{len(clientes)} clientes encontrados',
                    'rows': [
                        {'title': c.get('nomeExibicao', c['nomeCliente']),
                         'description': f'ID: {c["idClientes"]}',
                         'rowId': f'cliente_{c["idClientes"]}'}
                        for c in clientes[:10]
                    ]
                }]
                evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
                # Salvar clientes na sessao mapeados por id
                clientes_map = {str(c['idClientes']): c for c in clientes[:10]}
                _criar_sessao_os(numero, 'escolher_cliente_lista', {'defeito': params.get('defeito', '')}, clientes[:10])
                sessao_temp = sessions.get_os_session(numero)
                if sessao_temp:
                    sessao_temp['dados']['_clientes_map'] = {k: {'idClientes': v['idClientes'], 'nomeCliente': v['nomeCliente']} for k, v in clientes_map.items()}
                    sessions.set_os_session(numero, sessao_temp['etapa'], sessao_temp['dados'], sessao_temp.get('clientes'))
                return ''

        if params.get('defeito'):
            defeito = params['defeito']

        # Se ja temos o cliente, ir direto para defeito/equipamento
        if cliente_id:
            _criar_sessao_os(numero, 'defeito', {
                'cliente_id': cliente_id,
                'cliente_nome': cliente_nome,
                'defeito': defeito or '',
                'descricao': '',
            })
            if defeito:
                # Ja tem cliente e defeito, pular para equipamento
                _criar_sessao_os(numero, 'equipamento', {
                    'cliente_id': cliente_id,
                    'cliente_nome': cliente_nome,
                    'defeito': defeito,
                    'descricao': '',
                })
                return f"""✅ Cliente: *{cliente_nome}*
🔧 Defeito: *{defeito}*

Informe o *equipamento/produto* (ou digite *pular*):"""
            return f"""✅ Cliente: *{cliente_nome}*

📝 Descreva o *defeito* ou problema relatado:"""

        # Se o usuario e cliente, usar o proprio ID
        if usuario.get('tipo') == 'cliente' and usuario.get('clientes_id'):
            _criar_sessao_os(numero, 'defeito', {
                'cliente_id': usuario['clientes_id'],
                'cliente_nome': usuario['nome'],
                'defeito': '',
                'descricao': '',
            })
            return f"""✅ {usuario['nome']}, vou criar uma OS para voce.

📝 Descreva o *defeito* ou problema:"""

        # Pedir nome do cliente
        _criar_sessao_os(numero, 'cliente', {
            'defeito': defeito or '',
            'descricao': '',
        })
        return """📝 *Nova Ordem de Servico*

Qual o *nome do cliente*?
(Digite o nome, CNPJ, ID ou *Loja X*)"""

    # Processar etapa da sessao
    etapa = sessao['etapa']
    dados = sessao.get('dados', {})

    if etapa == 'escolher_cliente_lista':
        # Usuario escolheu da lista interativa (rowId = cliente_ID)
        clientes = sessao.get('clientes', [])
        cliente_encontrado = None

        # rowId vem como "cliente_42" ou o proprio numero
        texto_limpo_id = texto_limpo.replace('cliente_', '').strip()
        for c in clientes:
            if str(c['idClientes']) == texto_limpo_id:
                cliente_encontrado = c
                break

        if cliente_encontrado:
            dados['cliente_id'] = cliente_encontrado['idClientes']
            dados['cliente_nome'] = cliente_encontrado['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
            # Verificar se defeito/equipamento ja estao preenchidos
            pulou = _avancar_etapa_os(numero, dados, 'defeito')
            if pulou is not None:
                return pulou
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""

        # Fallback: tentar como indice numerico
        try:
            idx = int(texto_limpo) - 1
            if 0 <= idx < len(clientes):
                cliente_encontrado = clientes[idx]
        except (ValueError, IndexError):
            pass

        if cliente_encontrado:
            dados['cliente_id'] = cliente_encontrado['idClientes']
            dados['cliente_nome'] = cliente_encontrado['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""

        # Tentar buscar pelo nome digitado
        clientes_busca = queries.buscar_cliente_por_nome(texto.strip())
        if len(clientes_busca) == 1:
            dados['cliente_id'] = clientes_busca[0]['idClientes']
            dados['cliente_nome'] = clientes_busca[0]['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""

        return "❌ Cliente nao encontrado.\n\nDigite o *nome*, *CNPJ/CPF*, *ID* ou *Loja X*.\nExemplo: *Nova Era*, *04240370005701*, *Loja 57*\n\nOu *cancelar* para desistir."

    if etapa == 'escolher_cliente':
        # Usuario escolheu um cliente da lista numerada
        try:
            idx = int(texto_limpo) - 1
            clientes = sessao.get('clientes', [])
            if 0 <= idx < len(clientes):
                dados['cliente_id'] = clientes[idx]['idClientes']
                dados['cliente_nome'] = clientes[idx]['nomeCliente']
                sessao['etapa'] = 'defeito'
                _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
                return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""
        except (ValueError, IndexError):
            pass

        # Tentar buscar pelo nome digitado
        clientes = queries.buscar_cliente_por_nome(texto.strip())
        if len(clientes) == 1:
            dados['cliente_id'] = clientes[0]['idClientes']
            dados['cliente_nome'] = clientes[0]['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], sessao.get('clientes'))
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""
        elif len(clientes) > 1:
            # Enviar lista interativa
            sections = [{
                'title': f'{len(clientes)} clientes encontrados',
                'rows': [
                    {'title': c.get('nomeExibicao', c['nomeCliente']),
                     'description': f'ID: {c["idClientes"]}',
                     'rowId': f'cliente_{c["idClientes"]}'}
                    for c in clientes[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
            sessao['clientes'] = clientes[:10]
            sessao['etapa'] = 'escolher_cliente_lista'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], clientes[:10])
            return ''

        return "❌ Cliente nao encontrado.\n\nDigite o *nome*, *CNPJ/CPF*, *ID* ou *Loja X*.\nExemplo: *Nova Era*, *04240370005701*, *Loja 57*\n\nOu *cancelar* para desistir."

    elif etapa == 'cliente':
        # Buscar cliente pelo nome informado
        clientes = queries.buscar_cliente_por_nome(texto.strip())
        if len(clientes) == 1:
            dados['cliente_id'] = clientes[0]['idClientes']
            dados['cliente_nome'] = clientes[0]['nomeCliente']
            sessao['etapa'] = 'defeito'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            # Verificar se defeito/equipamento ja estao preenchidos (ex: audio)
            pulou = _avancar_etapa_os(numero, dados, 'defeito')
            if pulou is not None:
                return pulou
            return f"""✅ Cliente: *{dados['cliente_nome']}*

📝 Descreva o *defeito* ou problema relatado:"""
        elif len(clientes) > 1:
            # Enviar lista interativa
            sections = [{
                'title': f'{len(clientes)} clientes encontrados',
                'rows': [
                    {'title': c.get('nomeExibicao', c['nomeCliente']),
                     'description': f'ID: {c["idClientes"]}',
                     'rowId': f'cliente_{c["idClientes"]}'}
                    for c in clientes[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o cliente', 'Qual cliente deseja?', 'Escolher', sections)
            sessao['etapa'] = 'escolher_cliente_lista'
            sessao['clientes'] = clientes[:10]
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'], clientes[:10])
            return ''
        else:
            return "❌ Cliente nao encontrado.\n\nDigite o *nome*, *CNPJ/CPF*, *ID* ou *Loja X*.\nExemplo: *Nova Era*, *04240370005701*, *Loja 57*\n\nOu *cancelar* para desistir."

    elif etapa == 'defeito':
        dados['defeito'] = texto.strip()
        sessao['etapa'] = 'equipamento'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
        # Enviar botoes para equipamento (pular ou informar)
        evo.enviar_botoes(numero,
            title=f'Defeito: {dados["defeito"][:50]}',
            description='Informe o equipamento ou produto:',
            buttons=[
                {'type': 'reply', 'displayText': '⏭ Pular equipamento', 'id': 'PULAR_EQUIP'},
            ],
            footer='JJ Ferreiras'
        )
        return '🔧 Informe o *equipamento/produto* (ou digite *pular*):'

    elif etapa == 'equipamento':
        if texto_limpo in ('pular', 'nao sei', 'nenhum', '-', 'pular_equip'):
            dados['descricao'] = 'Nao especificado'
        else:
            dados['descricao'] = texto.strip()

        sessao['etapa'] = 'produto_servico'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])

        # Enviar botoes para produto/servico
        evo.enviar_botoes(numero,
            title='📦 Produto/Servico',
            description='Deseja adicionar um produto ou servico do catalogo?',
            buttons=[
                {'type': 'reply', 'displayText': '📦 Produto', 'id': 'TIPO_PRODUTO'},
                {'type': 'reply', 'displayText': '🔧 Servico', 'id': 'TIPO_SERVICO'},
                {'type': 'reply', 'displayText': '⏭ Pular', 'id': 'PULAR_ITEM'},
            ],
            footer='JJ Ferreiras'
        )
        return 'Deseja adicionar um *produto* ou *servico* do catalogo?\nDigite *produto*, *servico* ou *pular*.'

    elif etapa == 'produto_servico':
        # Escolha do tipo: produto ou servico
        if texto_limpo in ('pular', 'nao', 'nenhum', 'pular_item'):
            # Pular item, ir direto para valor total
            dados['itens'] = []
            sessao['etapa'] = 'valor'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            evo.enviar_botoes(numero,
                title='💰 Valor da OS',
                description='Informe o valor total ou pule:',
                buttons=[
                    {'type': 'reply', 'displayText': '⏭ Sem valor', 'id': 'PULAR_VALOR'},
                ],
                footer='JJ Ferreiras'
            )
            return '💰 Informe o *valor total* da OS (ex: 150.00) ou digite *pular*:'

        elif texto_limpo in ('produto', 'produtos', 'tipo_produto', 'item'):
            dados['_tipo_item'] = 'produto'
            sessao['etapa'] = 'buscar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return '🔍 Digite o *nome do produto* para buscar no catalogo:'

        elif texto_limpo in ('servico', 'servicos', 'tipo_servico'):
            dados['_tipo_item'] = 'servico'
            sessao['etapa'] = 'buscar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return '🔍 Digite o *nome do servico* para buscar no catalogo:'

        else:
            # Tentar detectar automaticamente
            sessao['etapa'] = 'buscar_item'
            dados['_tipo_item'] = 'produto'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return '🔍 Digite o *nome do produto ou servico* para buscar no catalogo:'

    elif etapa == 'buscar_item':
        tipo_item = dados.get('_tipo_item', 'produto')
        termo = texto.strip()

        if tipo_item == 'produto':
            itens = queries.buscar_produtos(termo, limite=10)
            if not itens:
                return f"❌ Nenhum produto encontrado para *\"{termo}\"*.\n\nTente outro nome ou digite *pular* para prosseguir sem item."
            if len(itens) == 1:
                item = itens[0]
                preco = float(item['precoVenda'] or 0)
                dados['itens'] = [{
                    'tipo': 'produto',
                    'id': item['idProdutos'],
                    'descricao': item['descricao'],
                    'preco': preco,
                    'quantidade': 1,
                }]
                dados['_item_selecionado'] = f"📦 {item['descricao']} — R$ {preco:.2f}"
                sessao['etapa'] = 'quantidade_item'
                _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
                return f"""✅ Produto: *{item['descricao']}*
💰 Preco: *R$ {preco:.2f}*

Digite a *quantidade* (ou *1*):"""
            # Multiplos produtos - enviar lista interativa
            sections = [{
                'title': f'{len(itens)} produtos encontrados',
                'rows': [
                    {'title': f"{item['descricao'][:40]}",
                     'description': f"R$ {float(item['precoVenda'] or 0):.2f}",
                     'rowId': f"prod_{item['idProdutos']}"}
                    for item in itens[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o produto', 'Escolha um produto:', 'Escolher', sections)
            dados['_tipo_item'] = 'produto'
            sessao['etapa'] = 'selecionar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return ''

        else:  # servico
            itens = queries.buscar_servicos(termo, limite=10)
            if not itens:
                return f"❌ Nenhum servico encontrado para *\"{termo}\"*.\n\nTente outro nome ou digite *pular* para prosseguir sem item."
            if len(itens) == 1:
                item = itens[0]
                preco = float(item['preco'] or 0)
                dados['itens'] = [{
                    'tipo': 'servico',
                    'id': item['idServicos'],
                    'descricao': item['nome'],
                    'preco': preco,
                    'quantidade': 1,
                }]
                dados['_item_selecionado'] = f"🔧 {item['nome']} — R$ {preco:.2f}"
                sessao['etapa'] = 'quantidade_item'
                _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
                return f"""✅ Servico: *{item['nome']}*
💰 Preco: *R$ {preco:.2f}*

Digite a *quantidade* (ou *1*):"""
            # Multiplos servicos - enviar lista interativa
            sections = [{
                'title': f'{len(itens)} servicos encontrados',
                'rows': [
                    {'title': f"{item['nome'][:40]}",
                     'description': f"R$ {float(item['preco'] or 0):.2f}",
                     'rowId': f"serv_{item['idServicos']}"}
                    for item in itens[:10]
                ]
            }]
            evo.enviar_lista(numero, 'Selecione o servico', 'Escolha um servico:', 'Escolher', sections)
            dados['_tipo_item'] = 'servico'
            sessao['etapa'] = 'selecionar_item'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return ''

    elif etapa == 'selecionar_item':
        # Usuario selecionou item da lista interativa
        tipo_item = dados.get('_tipo_item', 'produto')
        item_encontrado = None

        # Parsear rowId: prod_42 ou serv_7
        if texto_limpo.startswith('prod_'):
            prod_id = int(texto_limpo.replace('prod_', ''))
            item_encontrado = queries.buscar_produto_por_id(prod_id)
            if item_encontrado:
                tipo_item = 'produto'
        elif texto_limpo.startswith('serv_'):
            serv_id = int(texto_limpo.replace('serv_', ''))
            item_encontrado = queries.buscar_servico_por_id(serv_id)
            if item_encontrado:
                tipo_item = 'servico'
        else:
            # Fallback: tentar busca por texto
            if tipo_item == 'produto':
                itens = queries.buscar_produtos(texto.strip(), limite=1)
                item_encontrado = itens[0] if itens else None
            else:
                itens = queries.buscar_servicos(texto.strip(), limite=1)
                item_encontrado = itens[0] if itens else None

        if not item_encontrado:
            return "❌ Item nao encontrado.\n\nTente novamente ou digite *pular* para prosseguir sem item."

        if tipo_item == 'produto':
            preco = float(item_encontrado.get('precoVenda', 0) or 0)
            dados['itens'] = [{
                'tipo': 'produto',
                'id': item_encontrado['idProdutos'],
                'descricao': item_encontrado['descricao'],
                'preco': preco,
                'quantidade': 1,
            }]
            dados['_item_selecionado'] = f"📦 {item_encontrado['descricao']} — R$ {preco:.2f}"
        else:
            preco = float(item_encontrado.get('preco', 0) or 0)
            dados['itens'] = [{
                'tipo': 'servico',
                'id': item_encontrado['idServicos'],
                'descricao': item_encontrado['nome'],
                'preco': preco,
                'quantidade': 1,
            }]
            dados['_item_selecionado'] = f"🔧 {item_encontrado['nome']} — R$ {preco:.2f}"

        sessao['etapa'] = 'quantidade_item'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
        return f"""✅ Selecionado: *{dados['_item_selecionado']}*

Digite a *quantidade* (ou *1*):"""

    elif etapa == 'quantidade_item':
        # Receber quantidade do item
        try:
            qtd = int(texto_limpo)
            if qtd < 1:
                qtd = 1
        except ValueError:
            qtd = 1

        subtotal = 0
        if dados.get('itens'):
            dados['itens'][0]['quantidade'] = qtd
            item = dados['itens'][0]
            subtotal = round(qtd * item['preco'], 2)
            dados['_item_selecionado'] = f"{item['descricao']} x{qtd} — R$ {subtotal:.2f}"

        # Ir para etapa de valor total
        sessao['etapa'] = 'valor'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
        evo.enviar_botoes(numero,
            title='💰 Valor da OS',
            description=f'Subtotal: R$ {subtotal:.2f}\nInforme o valor total ou pule:',
            buttons=[
                {'type': 'reply', 'displayText': f'💰 Usar R$ {subtotal:.2f}', 'id': f'VALOR_{subtotal:.2f}'},
                {'type': 'reply', 'displayText': '⏭ Sem valor', 'id': 'PULAR_VALOR'},
            ],
            footer='JJ Ferreiras'
        )
        return f'💰 Informe o *valor total* da OS ou digite *pular* para prosseguir sem valor:'

    elif etapa == 'valor':
        # Receber valor total da OS
        valor_total = None

        # Parsear valor: "150.00", "R$ 150", "pular", "VALOR_150.00"
        if texto_limpo.startswith('valor_'):
            try:
                valor_total = float(texto_limpo.replace('valor_', '').strip())
            except ValueError:
                pass
        elif texto_limpo in ('pular', 'nao', 'nenhum', 'pular_valor', 'sem valor'):
            valor_total = None
        else:
            # Tentar extrair numero do texto
            valor_str = re.sub(r'[R$\s]', '', texto_limpo).replace(',', '.')
            try:
                valor_total = float(valor_str)
                if valor_total < 0:
                    valor_total = None
            except ValueError:
                return "❌ Valor invalido.\n\nDigite o valor (ex: *150.00*) ou *pular* para prosseguir sem valor."

        dados['valor_total'] = valor_total
        sessao['etapa'] = 'confirmar'
        _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])

        # Montar resumo para confirmacao
        resumo_texto = (
            f"👤 Cliente: *{dados.get('cliente_nome', 'Nao informado')}*\n"
            f"🔧 Defeito: *{dados.get('defeito', 'Nao informado')}*\n"
            f"📦 Equipamento: *{dados.get('descricao', 'Nao informado')}*"
        )
        if dados.get('_item_selecionado'):
            resumo_texto += f"\n📋 Item: *{dados['_item_selecionado']}*"
        if dados.get('valor_total'):
            resumo_texto += f"\n💰 Valor: *R$ {dados['valor_total']:.2f}*"

        _enviar_botoes_confirmacao(
            numero,
            title='📝 Confirmar OS',
            description=resumo_texto,
            opcoes=[
                {'displayText': '✅ Confirmar', 'id': 'CONFIRMAR'},
                {'displayText': '❌ Cancelar', 'id': 'CANCELAR'},
                {'displayText': '✏️ Corrigir cliente', 'id': 'CORRIGIR'},
            ],
            footer='JJ Ferreiras'
        )
        return ''

    elif etapa == 'confirmar':
        if texto_limpo in ('confirmar', 'confirmo', 'sim', 'confirma', 'ok', 'criar'):
            # Criar a OS via API do MapOS (com fallback para SQL direto)
            usuario_id = usuario.get('usuarios_id') or 1
            try:
                resultado = criar_os_via_api(
                    cliente_id=dados.get('cliente_id'),
                    descricao=dados.get('descricao', 'Nao especificado'),
                    defeito=dados.get('defeito', ''),
                    usuario_id=usuario_id,
                    valor_total=dados.get('valor_total'),
                    itens=dados.get('itens'),
                )

                if resultado and resultado.get('sucesso') is not False:
                    os_id = resultado.get('os_id') or resultado
                    os_data = resultado.get('os') or queries.buscar_os(os_id)

                    # Limpar sessao apos sucesso
                    _del_sessao_os(numero)

                    resposta = f"""✅ *OS #{os_id} criada com sucesso!*

━━━━━━━━━━━━━━━━━
👤 Cliente: {os_data.get('nomeCliente', dados.get('cliente_nome', ''))}
🔧 Defeito: {os_data.get('defeito', dados.get('defeito', ''))}
📦 Equipamento: {os_data.get('descricaoProduto', dados.get('descricao', ''))}"""
                    if dados.get('_item_selecionado'):
                        resposta += f"\n📋 Item: *{dados['_item_selecionado']}*"
                    if dados.get('valor_total'):
                        resposta += f"\n💰 Valor: *R$ {dados['valor_total']:.2f}*"
                    resposta += f"""
📋 Status: {os_data.get('status', 'Aberto')}
📅 Data: {os_data.get('dataInicial', 'Hoje')}
━━━━━━━━━━━━━━━━━

Digite *detalhes da OS {os_id}* para acompanhar."""

                    # Tentar gerar e enviar PDF da OS
                    mapos_url = config.MAPOS_URL.rstrip('/')
                    api_key = config.MAPOS_API_KEY
                    if mapos_url and api_key:
                        try:
                            pdf_url = f"{mapos_url}/api/v2/relatorios/exportar"
                            resp = http_requests.post(pdf_url, data={
                                'tipo': 'os_periodo',
                                'formato': 'pdf',
                            }, headers={
                                'X-API-KEY': api_key,
                            }, timeout=30)

                            if resp.status_code == 200:
                                data_resp = resp.json()
                                if data_resp.get('success'):
                                    download_url = data_resp.get('data', {}).get('download_url', '')
                                    if download_url:
                                        enviar_pdf_whatsapp(numero, download_url, f'OS #{os_id}')
                        except Exception as e:
                            logger.warning(f"Erro ao gerar PDF da OS: {e}")

                    return resposta
                else:
                    _del_sessao_os(numero)
                    return "❌ Erro ao criar OS. Tente novamente ou digite *ajuda*."

            except Exception as e:
                logger.error(f"Erro ao criar OS: {e}")
                _del_sessao_os(numero)
                return f"❌ Erro ao criar OS: {str(e)}\n\nTente novamente ou digite *ajuda*."

        elif texto_limpo in ('nao', 'corrigir', 'alterar', 'editar', 'corrigir cliente'):
            sessao['etapa'] = 'cliente'
            _criar_sessao_os(numero, sessao['etapa'], sessao['dados'])
            return "🔄 Vamos corrigir. Qual o *nome do cliente*?"

        else:
            return "Digite *CONFIRMAR* para criar a OS ou *CANCELAR* para desistir."

    # Fallback - limpar sessao
    _del_sessao_os(numero)
    return "❌ Fluxo de criacao encerrado.\n\nDigite *ajuda* para ver as opcoes."


# ========== ALTERACAO INTERATIVA DE STATUS DE OS ==========

STATUS_OS_LISTA = [
    'Aberto', 'Em Andamento', 'Aguardando Peças',
    'Orçamento', 'Aprovado', 'Faturado', 'Finalizado', 'Cancelado'
]

def processar_alterar_status(numero: str, texto: str, usuario: dict) -> str:
    """Processa o fluxo interativo de alteracao de status de OS passo a passo."""

    # Comando para cancelar
    texto_limpo = texto.lower().strip()
    if texto_limpo in ('cancelar', 'cancela', 'desistir', 'abandonar', '#cancelar'):
        sessions.del_status_session(numero)
        return "❌ Alteracao de status cancelada.\n\nDigite *ajuda* para ver as opcoes."

    # Verificar sessao ativa
    sessao = sessions.get_status_session(numero)

    # Se nao ha sessao, iniciar fluxo
    if not sessao:
        # Verificar se o usuario informou o numero da OS na mensagem
        params = nlp.extrair_parametros(texto, 'alterar_status_os')
        os_id = params.get('os_id')

        # Verificar se informou o status na mensagem
        novo_status_texto = None
        texto_lower = texto.lower()
        for s in STATUS_OS_LISTA:
            if s.lower() in texto_lower:
                novo_status_texto = s
                break
        # Variacoes comuns
        if not novo_status_texto:
            if 'finalizar' in texto_lower or 'concluir' in texto_lower or 'concluido' in texto_lower:
                novo_status_texto = 'Finalizado'
            elif 'cancelar' in texto_lower and 'os' in texto_lower:
                novo_status_texto = 'Cancelado'

        # Se e tecnico/admin, mostrar suas OS
        if eh_admin_ou_tecnico(usuario) and usuario.get('usuarios_id'):
            oss = queries.buscar_os_por_tecnico_status(usuario['usuarios_id'], limite=10)
            if oss:
                lista = "\n".join(
                    f"• *OS #{o['idOs']}* - {o['status']} | {o['nomeCliente']} | {o['descricaoProduto'] or 'Sem equipamento'}"
                    for o in oss[:8]
                )
            else:
                lista = "Voce nao tem OS atribuidas."

            # Se ja temos OS e status, ir direto para confirmacao
            if os_id and novo_status_texto:
                os_data = queries.buscar_os(os_id)
                if os_data:
                    _criar_sessao_status(numero, 'confirmar', {
                        'os_id': os_id,
                        'status_anterior': os_data.get('status', ''),
                        'novo_status': novo_status_texto,
                    })
                    sessao = _get_sessao_status(numero)
                    return _formatar_confirmacao_status(sessao['dados'], os_data, usuario)

            # Se temos OS mas nao status
            if os_id and not novo_status_texto:
                os_data = queries.buscar_os(os_id)
                if os_data:
                    _criar_sessao_status(numero, 'novo_status', {
                        'os_id': os_id,
                        'status_anterior': os_data.get('status', ''),
                    })
                    return _formatar_escolha_status(os_data, usuario)

            # Pedir numero da OS
            _criar_sessao_status(numero, 'os_id', {})
            return f"""🔄 *Alterar Status de OS*

Suas OS:
{lista}

Qual o *numero da OS* que deseja alterar?"""

        # Se e cliente, nao permitir
        if eh_cliente(usuario):
            return f"🔒 {usuario.get('nome', 'Cliente')}, a alteracao de status e feita pela equipe tecnica.\n\nEntre em contato conosco para atualizar sua OS."

        # Pedir numero da OS
        _criar_sessao_status(numero, 'os_id', {})
        return """🔄 *Alterar Status de OS*

Qual o *numero da OS* que deseja alterar?"""

    # === PROCESSAR ETAPAS ===
    etapa = sessao['etapa']
    dados = sessao.get('dados', {})

    if etapa == 'os_id':
        # Extrair numero da mensagem
        match = nlp.OS_NUMBER_RE.search(texto)
        if not match:
            return "❌ Informe o *numero da OS* (ex: 15, #23, OS 42):"

        os_id = int(match.group(1))
        os_data = queries.buscar_os(os_id)

        if not os_data:
            return f"❌ OS #{os_id} nao encontrada. Verifique o numero e tente novamente.\n\nDigite *cancelar* para sair."

        dados['os_id'] = os_id
        dados['status_anterior'] = os_data.get('status', '')
        sessao['etapa'] = 'novo_status'
        _criar_sessao_status(numero, sessao['etapa'], sessao['dados'])

        return _formatar_escolha_status(os_data, usuario)

    elif etapa == 'novo_status':
        # Selecionar novo status
        escolha = texto_limpo.strip()
        os_id = dados.get('os_id')
        os_data = queries.buscar_os(os_id)

        # Mapear escolha numerica ou texto
        status_map = {
            '1': 'Aberto', '2': 'Em Andamento', '3': 'Aguardando Peças',
            '4': 'Orçamento', '5': 'Aprovado', '6': 'Faturado',
            '7': 'Finalizado', '8': 'Cancelado',
        }

        novo_status = None

        # Tentar por numero
        if escolha in status_map:
            novo_status = status_map[escolha]
        else:
            # Tentar por texto
            for s in STATUS_OS_LISTA:
                if s.lower() in escolha or escolha in s.lower():
                    novo_status = s
                    break
            # Variacoes
            if not novo_status:
                if 'finalizar' in escolha or 'concluir' in escolha:
                    novo_status = 'Finalizado'
                elif 'cancelar' in escolha:
                    novo_status = 'Cancelado'
                elif 'andamento' in escolha:
                    novo_status = 'Em Andamento'
                elif 'aprov' in escolha:
                    novo_status = 'Aprovado'
                elif 'fatur' in escolha:
                    novo_status = 'Faturado'
                elif 'orcamento' in escolha or 'orçamento' in escolha:
                    novo_status = 'Orçamento'
                elif 'aguard' in escolha or 'peca' in escolha or 'peça' in escolha:
                    novo_status = 'Aguardando Peças'

        if not novo_status:
            return _formatar_escolha_status(os_data, usuario)

        dados['novo_status'] = novo_status
        sessao['etapa'] = 'confirmar'
        _criar_sessao_status(numero, sessao['etapa'], sessao['dados'])

        return _formatar_confirmacao_status(dados, os_data, usuario)

    elif etapa == 'confirmar':
        if texto_limpo in ('confirmar', 'confirmo', 'sim', 'confirma', 'ok', 'alterar', 'mudar'):
            # Executar alteracao de status
            os_id = dados.get('os_id')
            novo_status = dados.get('novo_status')

            resultado = queries.alterar_status_os(os_id, novo_status)

            # Limpar sessao
            _del_sessao_status(numero)

            if resultado.get('sucesso'):
                os_data = resultado.get('os', {})
                nome_cliente = os_data.get('nomeCliente', '') if os_data else ''
                equipamento = os_data.get('descricaoProduto', '') if os_data else ''

                resposta = f"""✅ *Status da OS #{os_id} alterado!*

━━━━━━━━━━━━━━━━━
{_fmt_status_emoji(dados.get('status_anterior', ''))} Anterior: *{dados.get('status_anterior', '')}*
{_fmt_status_emoji(resultado.get('novo_status', novo_status))} Novo: *{resultado.get('novo_status', novo_status)}*
👤 Cliente: {nome_cliente}
🔧 Equipamento: {equipamento or 'Nao informado'}
━━━━━━━━━━━━━━━━━

Digite *detalhes da OS {os_id}* para ver os detalhes atualizados."""

                # Chamar API do MapOS para atualizar la
                _atualizar_os_mapos_api(os_id, novo_status)

                return resposta
            else:
                return f"❌ Erro: {resultado.get('erro', 'Nao foi possivel alterar o status.')}\n\nTente novamente ou digite *ajuda*."

        elif texto_limpo in ('nao', 'corrigir', 'outro', 'voltar'):
            sessao['etapa'] = 'novo_status'
            _criar_sessao_status(numero, sessao['etapa'], sessao['dados'])
            os_data = queries.buscar_os(dados.get('os_id'))
            return f"🔄 Escolha outro status:\n\n" + _formatar_lista_status(os_data, usuario)

        else:
            return "Digite *CONFIRMAR* para alterar o status ou *CANCELAR* para desistir."

    # Fallback
    _del_sessao_status(numero)
    return "❌ Fluxo de alteracao encerrado.\n\nDigite *ajuda* para ver as opcoes."


def _formatar_escolha_status(os_data: dict, usuario: dict) -> str:
    """Formata mensagem para escolha de novo status."""
    os_id = os_data.get('idOs', '')
    status_atual = os_data.get('status', '')
    cliente = os_data.get('nomeCliente', '')
    equip = os_data.get('descricaoProduto', '') or 'Nao informado'
    emoji_atual = _fmt_status_emoji(status_atual)

    status_numerados = "\n".join(
        f"{_fmt_status_emoji(s)} {i+1}. {s}" for i, s in enumerate(STATUS_OS_LISTA)
    )

    return f"""{emoji_atual} *OS #{os_id}* — Status atual: *{status_atual}*
👤 {cliente} · 🔧 {equip}

Escolha o *novo status*:

{status_numerados}

Digite o *numero* ou o *nome* do status:"""


def _formatar_lista_status(os_data: dict, usuario: dict) -> str:
    """Retorna apenas a lista de status numerados."""
    return _formatar_escolha_status(os_data, usuario)


def _formatar_confirmacao_status(dados: dict, os_data: dict, usuario: dict) -> str:
    """Formata mensagem de confirmacao de alteracao de status."""
    os_id = dados.get('os_id')
    status_anterior = dados.get('status_anterior', '')
    novo_status = dados.get('novo_status', '')
    cliente = os_data.get('nomeCliente', '') if os_data else ''
    equip = (os_data.get('descricaoProduto', '') or 'Nao informado') if os_data else ''
    emoji_ant = _fmt_status_emoji(status_anterior)
    emoji_novo = _fmt_status_emoji(novo_status)

    return f"""🔄 *Confirmar alteracao de status*

━━━━━━━━━━━━━━━━━
📋 *OS #{os_id}* — {cliente}
🔧 {equip}

{emoji_ant} Status atual: *{status_anterior}*
{emoji_novo} Novo status: *{novo_status}*
━━━━━━━━━━━━━━━━━

✅ *CONFIRMAR* — alterar o status
❌ *CANCELAR* — desistir
🔄 *OUTRO* — escolher status diferente"""


def _atualizar_os_mapos_api(os_id: int, novo_status: str):
    """Chama a API do MapOS para atualizar o status da OS (sincronizacao)."""
    mapos_url = config.MAPOS_URL.rstrip('/')
    api_key = config.MAPOS_API_KEY

    if not mapos_url or not api_key:
        logger.warning("MAPOS_URL ou MAPOS_API_KEY nao configurados para atualizar OS via API")
        return

    url = f"{mapos_url}/api/v2/acoes/executar"
    try:
        resp = http_requests.post(url, data={
            'acao': 'atualizar_status_os',
            'dados': json.dumps({'os_id': os_id, 'status': novo_status}),
        }, headers={
            'X-API-KEY': api_key,
        }, timeout=15)
        if resp.status_code == 200:
            logger.info(f"OS #{os_id} status atualizado via MapOS API: {novo_status}")
        else:
            logger.warning(f"Falha ao atualizar OS #{os_id} via API: {resp.status_code}")
    except Exception as e:
        logger.warning(f"Erro ao chamar MapOS API para atualizar OS: {e}")

def processar_comando(comando: str, params: dict, usuario: dict, texto_original: str = '') -> dict:
    """Processa o comando classificado e retorna os dados para resposta"""

    # Numero do usuario para PDF e sessoes
    numero = usuario.get('numero', '') if usuario else ''

    # Se ha sessao de criacao de OS ativa, redirecionar para o fluxo interativo
    _os_check = sessions.get_os_session(numero) if numero else None
    if _os_check and not _os_check.get('expired'):
        # Qualquer mensagem durante sessao de OS vai para o fluxo
        resposta = processar_criacao_os(numero, texto_original, usuario)
        return {'mensagem': resposta}

    dados = {}

    if comando == 'status_os':
        if eh_cliente(usuario) and usuario.get('clientes_id'):
            oss = queries.listar_os_cliente(usuario['clientes_id'])
            dados = {'oss': oss}
        elif eh_admin_ou_tecnico(usuario) and usuario.get('usuarios_id'):
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
        # Apenas admin pode criar OS
        if not usuario or not eh_admin(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para administradores.'}
        # Iniciar fluxo interativo
        if numero:
            resposta = processar_criacao_os(numero, texto_original, usuario)
            return {'mensagem': resposta}
        else:
            return {'mensagem': '❌ Nao foi possivel identificar seu numero para criar OS.'}

    elif comando == 'alterar_status_os':
        # Apenas admin pode alterar status de OS
        if not usuario or not eh_admin(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para administradores.'}
        # Iniciar fluxo interativo de alteracao de status
        if numero:
            resposta = processar_alterar_status(numero, texto_original, usuario)
            return {'mensagem': resposta}
        else:
            return {'mensagem': '❌ Nao foi possivel identificar seu numero para alterar status.'}

    elif comando == 'checkin_tecnico':
        # Tecnico chegou no local do atendimento
        if not usuario or not eh_admin_ou_tecnico(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para tecnicos e administradores.'}
        params = nlp.extrair_parametros(texto_original, 'checkin_tecnico')
        os_id = params.get('os_id')
        if not os_id:
            # Tentar extrair numero da mensagem
            import re
            match = re.search(r'#?\s*(\d+)', texto_original)
            if match:
                os_id = int(match.group(1))
        if os_id:
            os_data = queries.buscar_os(os_id)
            if os_data:
                # Registrar checkin na OS
                now_str = datetime.now().strftime('%d/%m/%Y %H:%M')
                laudo = f"[Check-in] Tecnico {usuario.get('nome', 'Desconhecido')} chegou no local em {now_str}"
                execute_update(
                    "UPDATE os SET laudoTecnico = CONCAT(IFNULL(laudoTecnico,''), :laudo) WHERE idOs = :os_id",
                    {'laudo': f'\n{laudo}', 'os_id': os_id}
                )
                registrar_log(numero, 'entrada', laudo, 'checkin_tecnico', 'registrado')
                return {'mensagem': f"✅ *Check-in registrado!*\n\n📋 OS *#{os_id}* as {now_str}\n👷 Tecnico: *{usuario.get('nome', 'Desconhecido')}*\n🔧 {os_data.get('descricaoProduto', 'Nao informado')}"}
            else:
                return {'mensagem': f'❌ OS #{os_id} nao encontrada. Informe o numero da OS.'}
        return {'mensagem': '📍 Informe o numero da OS para registrar o check-in.\n\nExemplo: *cheguei na OS 42*'}

    elif comando == 'checkout_tecnico':
        # Tecnico esta saindo do local do atendimento
        if not usuario or not eh_admin_ou_tecnico(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para tecnicos e administradores.'}
        params = nlp.extrair_parametros(texto_original, 'checkout_tecnico')
        os_id = params.get('os_id')
        if not os_id:
            import re
            match = re.search(r'#?\s*(\d+)', texto_original)
            if match:
                os_id = int(match.group(1))
        if os_id:
            os_data = queries.buscar_os(os_id)
            if os_data:
                now_str = datetime.now().strftime('%d/%m/%Y %H:%M')
                laudo = f"[Check-out] Tecnico {usuario.get('nome', 'Desconhecido')} finalizou atendimento em {now_str}"
                execute_update(
                    "UPDATE os SET laudoTecnico = CONCAT(IFNULL(laudoTecnico,''), :laudo) WHERE idOs = :os_id",
                    {'laudo': f'\n{laudo}', 'os_id': os_id}
                )
                registrar_log(numero, 'saida', laudo, 'checkout_tecnico', 'registrado')
                return {'mensagem': f"✅ *Check-out registrado!*\n\n📋 OS *#{os_id}* as {now_str}\n\nDeseja alterar o status da OS?\nDigite *alterar status OS {os_id}*"}
            else:
                return {'mensagem': f'❌ OS #{os_id} nao encontrada. Informe o numero da OS.'}
        return {'mensagem': '🚪 Informe o numero da OS para registrar o check-out.\n\nExemplo: *saindo da OS 42*'}

    # === NOVOS RELATORIOS COM ANALISE GLM ===
    elif comando == 'relatorio_financeiro':
        resultado = queries.relatorio_financeiro()
        dados = resultado
        # Gerar PDF se admin/tecnico
        if usuario and eh_admin_ou_tecnico(usuario):
            pdf_url = gerar_pdf_relatorio('resumo_financeiro', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_vendas':
        resultado = queries.relatorio_vendas()
        dados = resultado
        if usuario and eh_admin(usuario):
            pdf_url = gerar_pdf_relatorio('vendas', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_estoque':
        resultado = queries.relatorio_estoque()
        dados = resultado
        if usuario and eh_admin(usuario):
            pdf_url = gerar_pdf_relatorio('estoque', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_produtividade':
        resultado = queries.relatorio_produtividade()
        dados = resultado
        if usuario and eh_admin(usuario):
            pdf_url = gerar_pdf_relatorio('produtividade_tecnico', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_clientes_top':
        resultado = queries.relatorio_clientes_top()
        dados = resultado

    elif comando == 'relatorio_os_periodo':
        resultado = queries.relatorio_os_periodo()
        dados = resultado
        if usuario and eh_admin(usuario):
            pdf_url = gerar_pdf_relatorio('os_mes', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_atrasados':
        resultado = queries.relatorio_atrasados()
        dados = resultado
        if usuario and eh_admin(usuario):
            pdf_url = gerar_pdf_relatorio('os_periodo', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'os_finalizadas_mes':
        resultado = queries.os_finalizadas_mes()
        dados = resultado
        if usuario and eh_admin(usuario):
            pdf_url = gerar_pdf_relatorio('os_finalizadas', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    return dados


# ========== ANALISE GLM DE RELATORIOS ==========

def analisar_relatorio_com_glm(tipo: str, dados: dict) -> str:
    """Usa LLM (GLM) para gerar analise resumida e inteligente de relatorios."""
    if not config.LLM_CLOUD_URL or not config.LLM_CLOUD_MODEL:
        return ''

    # Montar resumo dos dados para enviar ao LLM
    resumo = dados.get('resumo', {})
    tipo_nome = {
        'relatorio_financeiro': 'Financeiro',
        'relatorio_vendas': 'Vendas',
        'relatorio_estoque': 'Estoque',
        'relatorio_produtividade': 'Produtividade',
        'relatorio_clientes_top': 'Top Clientes',
        'relatorio_os_periodo': 'OS por Periodo',
        'relatorio_atrasados': 'Atrasados',
        'os_finalizadas_mes': 'OS Finalizadas (Cobranca)',
    }.get(tipo, tipo)

    prompt = f"""Analise o relatorio abaixo de forma resumida e pratique para um administrador.
Destaque os pontos mais importantes, alertas e recomendacoes.

Relatorio: {tipo_nome}
Dados: {json.dumps(resumo, default=str, ensure_ascii=False)}

Responda em ate 3 linhas com:
1. Resumo principal
2. Alertas (se houver)
3. Recomendacao"""

    try:
        url = f"{config.LLM_CLOUD_URL}/chat/completions"
        headers = {"Content-Type": "application/json"}
        if config.LLM_CLOUD_API_KEY:
            headers["Authorization"] = f"Bearer {config.LLM_CLOUD_API_KEY}"

        payload = {
            "model": config.LLM_CLOUD_MODEL,
            "messages": [
                {"role": "system", "content": "Voce e um assistente de gestao. Analise dados e forneça insights resumidos em portugues brasileiro."},
                {"role": "user", "content": prompt}
            ],
            "temperature": 0.3,
            "max_tokens": 200
        }

        resp = http_requests.post(url, json=payload, headers=headers, timeout=30)
        if resp.status_code == 200:
            data = resp.json()
            analise = data.get('choices', [{}])[0].get('message', {}).get('content', '').strip()
            return analise
    except Exception as e:
        logger.warning(f"Erro na analise GLM: {e}")

    return ''


# ========== ENDPOINTS ==========

@app.get("/health")
async def health():
    try:
        result = execute_query("SELECT 1 as ok")
        db_ok = bool(result and result[0].get('ok') == 1)
    except Exception:
        db_ok = False

    llm_status = "regex" if not config.LLM_PROVIDER else config.LLM_PROVIDER

    return {
        "status": "ok",
        "database": "online" if db_ok else "offline",
        "llm": llm_status,
        "whisper": config.WHISPER_URL,
        "version": "3.0.0"
    }


@app.post("/webhook/evolution")
async def webhook_evolution(request: Request, x_api_key: str = Header(None)):
    """
    Recebe webhook do Evolution Go quando uma mensagem chega.
    Suporta texto e audio (transcricao automatica via Whisper).
    """
    if x_api_key and x_api_key != config.AGENT_API_KEY:
        raise HTTPException(status_code=401, detail="Unauthorized")

    try:
        payload = await request.json()
    except Exception:
        payload = {}

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

    # Identificar usuario
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

    # Limpar sessoes expiradas periodicamente
    _limpar_sessoes_expiradas()

    # ===== VERIFICAR SE HA SESSAO DE CRIACAO DE OS ATIVA =====
    os_session = sessions.get_os_session(numero)
    if os_session:
        if os_session.get('expired'):
            msg = "⏰ Sessao expirada por inatividade (15 min).\n\nDigite *ajuda* para recomecar."
            evo.enviar_texto(numero, msg)
            registrar_log(numero, 'saida', msg, 'sessao_expirada', 'respondido')
            return {"status": "ok", "comando": "sessao_expirada", "numero": numero}

        # Se audio transcrito, usar LLM para interpretar no contexto da sessao
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

                    # Se o LLM detectou tipo de item (produto/servico), usar diretamente
                    if extras.get('tipo_item') and os_session.get('etapa') == 'produto_servico':
                        texto_processado = extras['tipo_item']

                    # Se o LLM detectou nome de busca para produto/servico
                    if extras.get('nome_busca') and os_session.get('etapa') == 'buscar_item':
                        texto_processado = extras['nome_busca']

                    # Se o LLM detectou valor numerico
                    if extras.get('valor') is not None and os_session.get('etapa') == 'valor':
                        texto_processado = str(extras['valor'])

                    # Se o LLM detectou quantidade
                    if extras.get('quantidade') is not None and os_session.get('etapa') == 'quantidade_item':
                        texto_processado = str(int(extras['quantidade']))

                    # Se o LLM detectou dados de etapas anteriores, reiniciar fluxo com tudo
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

        resposta = processar_criacao_os(numero, texto_processado, usuario)
        # Se resposta vazia, foi enviada via botoes interativos
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

    # ===== VERIFICAR SE HA SESSAO DE ALTERACAO DE STATUS ATIVA =====
    status_session = sessions.get_status_session(numero)
    if status_session:
        if status_session.get('expired'):
            msg = "⏰ Sessao expirada por inatividade (15 min).\n\nDigite *ajuda* para recomecar."
            evo.enviar_texto(numero, msg)
            registrar_log(numero, 'saida', msg, 'sessao_expirada', 'respondido')
            return {"status": "ok", "comando": "sessao_expirada", "numero": numero}
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

    # ===== CLASSIFICAR INTENCAO =====
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

    # ===== FALLBACK: DETECTAR CRIAR_OS POR PALAVRAS-CHAVE NO AUDIO =====
    # Se audio foi transcrito e contem palavras de criar OS, tentar criar_os_completa_via_audio
    # mesmo que a classificacao LLM/regex nao tenha detectado criar_os
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

    # ===== CRIACAO COMPLETA VIA AUDIO =====
    # Se audio com intencao de criar OS, tentar criar direto com todos os dados
    if comando == 'criar_os' and audio_info.get('tem_audio') and config.LLM_PROVIDER:
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
        # Se nao conseguiu criar completa, cair no fluxo interativo normalmente

    # ===== PROCESSAR COMANDO =====
    increment_stat('comandos_executados')
    dados = processar_comando(comando, params, usuario, texto)

    # ===== ENVIAR MENU INTERATIVO PARA AJUDA =====
    if comando == 'ajuda':
        resultado = _enviar_menu_interativo(numero, usuario)
        # Fallback para texto se lista falhar
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

    # ===== FORMATAR E ENVIAR RESPOSTA =====
    if dados.get('mensagem'):
        # Resposta direta (fluxo interativo de OS)
        resposta = dados['mensagem']
    else:
        resposta = nlp.formatar_resposta(comando, dados, usuario)

    # Pular envio se resposta vazia (enviada via botoes/lista interativa)
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

    # ===== ANALISE GLM PARA RELATORIOS =====
    tipos_relatorio = [
        'relatorio_financeiro', 'relatorio_vendas', 'relatorio_estoque',
        'relatorio_produtividade', 'relatorio_clientes_top',
        'relatorio_os_periodo', 'relatorio_atrasados', 'os_finalizadas_mes'
    ]
    if comando in tipos_relatorio and config.LLM_CLOUD_URL:
        analise = analisar_relatorio_com_glm(comando, dados)
        if analise:
            msg_analise = f"\n\n🤖 *Analise IA:*\n{analise}"
            evo.enviar_texto(numero, msg_analise)

    # ===== ENVIAR PDF SE DISPONIVEL =====
    pdf_url = dados.get('pdf_url')
    if pdf_url:
        enviar_pdf_whatsapp(numero, pdf_url, f"Relatorio - {comando.replace('relatorio_', '').replace('_', ' ').title()}")

    # Repassar para n8n se configurado
    import threading
    if config.N8N_WEBHOOK_URL:
        threading.Thread(target=forward_to_n8n, args=(payload,), daemon=True).start()

    return {
        "status": "ok",
        "comando": comando,
        "numero": numero,
        "tipo_mensagem": "audio" if audio_info.get('tem_audio') else "texto",
        "texto_processado": texto,
        "envio_success": resultado.success
    }


def forward_to_n8n(payload: dict):
    n8n_url = config.N8N_WEBHOOK_URL
    if not n8n_url:
        return
    try:
        http_requests.post(n8n_url, json=payload, timeout=5)
        logger.debug(f"Payload repassado para n8n: {n8n_url}")
    except Exception as e:
        logger.warning(f"Falha ao repassar para n8n: {e}")


# ========== LIMPEZA PERIODICA DE SESSOES ==========

def _limpar_sessoes_expiradas():
    """Remove sessoes de OS e status que expiraram."""
    sessions.cleanup_expired()
    # Limpar deduplicacao
    agora = time.time()
    expirados_msg = [k for k, v in _msg_ids.items() if agora - v > _DEDUP_TTL]
    for k in expirados_msg:
        del _msg_ids[k]


# ========== CRIACAO DE OS VIA MAPOS API ==========

def criar_os_via_api(cliente_id: int, descricao: str = '', defeito: str = '',
                     usuario_id: int = None, observacoes: str = '', status: str = 'Aberto',
                     valor_total: float = None, itens: list = None) -> dict:
    """Cria OS via API do MapOS (unifica com o fluxo PHP)."""
    mapos_url = config.MAPOS_URL.rstrip('/')
    api_key = config.MAPOS_API_KEY

    if not mapos_url or not api_key:
        logger.warning("MAPOS_URL ou MAPOS_API_KEY nao configurados — criando via SQL direto")
        return queries.criar_os(
            cliente_id=cliente_id, descricao=descricao, defeito=defeito,
            usuario_id=usuario_id or 1, observacoes=observacoes, status=status,
            valor_total=valor_total, itens=itens
        )

    url = f"{mapos_url}/api/v2/acoes/executar"
    payload_dados = {
        'clientes_id': cliente_id,
        'descricaoProduto': descricao or 'Nao especificado',
        'defeito': defeito or '',
        'usuarios_id': usuario_id or 1,
        'status': status,
        'observacoes': observacoes or '',
    }
    if valor_total is not None:
        payload_dados['valorTotal'] = valor_total
    if itens:
        payload_dados['itens'] = itens

    payload = {
        'acao': 'criar_os',
        'dados': json.dumps(payload_dados),
    }
    try:
        resp = http_requests.post(url, data=payload, headers={'X-API-KEY': api_key}, timeout=15)
        if resp.status_code == 200:
            data = resp.json()
            if data.get('success'):
                resultado = data.get('data', {}).get('resultado', {})
                os_id = resultado.get('os_id')
                if os_id:
                    os_data = queries.buscar_os(os_id)
                    return {
                        'sucesso': True,
                        'os_id': os_id,
                        'os': os_data,
                        'via_api': True
                    }
        logger.warning(f"Criacao OS via API falhou (status {resp.status_code}), tentando SQL direto")
    except Exception as e:
        logger.warning(f"Excecao ao criar OS via API: {e}, tentando SQL direto")

    # Fallback: SQL direto
    return queries.criar_os(
        cliente_id=cliente_id, descricao=descricao, defeito=defeito,
        usuario_id=usuario_id or 1, observacoes=observacoes, status=status,
        valor_total=valor_total, itens=itens
    )


# ========== NOTIFICACOES AGENDADAS ==========

def enviar_relatorio_diario():
    """Envia relatorio diario para admins com notificacao agendada ativa."""
    try:
        # Buscar admins via tabela de notificacoes agendadas
        agendados = execute_query(
            """SELECT n.numero_telefone, n.tipo_notificacao
               FROM agente_ia_notificacoes_agendadas n
               WHERE n.tipo_notificacao = 'relatorio_diario'
                 AND n.situacao = 1
                 AND (n.horario IS NULL OR TIME(NOW()) BETWEEN TIME(n.horario) - INTERVAL 5 MINUTE AND TIME(n.horario) + INTERVAL 5 MINUTE)
            """
        )

        # Se nao ha agendados, usar config como fallback
        if not agendados and not config.RELATORIO_DIARIO_HORA:
            return

        # Se nao ha agendados mas tem config, usar admins do banco
        if not agendados:
            sql = """
                SELECT w.numero_telefone, u.nome, u.idUsuarios
                FROM whatsapp_integracao w
                JOIN usuarios u ON u.idUsuarios = w.usuarios_id
                WHERE u.permissoes_id = 1 AND w.situacao = 1
            """
            if ADMIN_NUMERO:
                sql += f" UNION SELECT '{ADMIN_NUMERO}', 'Administrador', 1"
            admins = execute_query(sql)
        else:
            admins = [{'numero_telefone': a['numero_telefone'], 'nome': 'Administrador'} for a in agendados]

        if not admins:
            return

        resumo = queries.resumo_os_dia()
        dados = {'resumo': resumo}
        for admin in admins:
            numero = admin['numero_telefone']
            nome = admin.get('nome', 'Administrador')
            usuario = {'tipo': 'admin', 'tipo_vinculo': 'admin', 'nome': nome, 'numero': numero}
            resposta = nlp.formatar_resposta('relatorio_os', dados, usuario)
            evo.enviar_texto(numero, resposta)
            registrar_log(numero, 'saida', resposta, 'relatorio_diario', 'enviado')

            # Analise com GLM se disponivel
            if config.LLM_CLOUD_URL:
                try:
                    analise = analisar_relatorio_com_glm('relatorio_os', dados)
                    if analise:
                        evo.enviar_texto(numero, f"📊 *Analise IA:*\n{analise}")
                        registrar_log(numero, 'saida', analise, 'relatorio_diario_glm', 'enviado')
                except Exception:
                    pass

            # Atualizar ultimo_envio na tabela de agendados
            execute_update(
                """UPDATE agente_ia_notificacoes_agendadas
                   SET ultimo_envio = NOW()
                   WHERE numero_telefone = :numero AND tipo_notificacao = 'relatorio_diario'""",
                {'numero': numero}
            )
            time.sleep(1)
    except Exception as e:
        logger.error(f"Erro ao enviar relatorio diario: {e}")


def notificar_os_vencendo():
    """Notifica tecnicos/admins sobre OS que vencem amanha."""
    try:
        # Verificar se ha destinatarios agendados para essa notificacao
        agendados = execute_query(
            """SELECT numero_telefone FROM agente_ia_notificacoes_agendadas
               WHERE tipo_notificacao = 'os_vencendo' AND situacao = 1"""
        )

        # Se nao ha agendados e nao tem config, pular
        if not agendados and not config.NOTIFICACAO_VENCENDO_HORA:
            return

        sql = """
            SELECT o.idOs, o.dataFinal, o.descricaoProduto, o.defeito, o.status,
                   c.nomeCliente, u.nome as tecnico_nome, u.celular,
                   w.numero_telefone
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            LEFT JOIN usuarios u ON u.idUsuarios = o.usuarios_id
            LEFT JOIN whatsapp_integracao w ON w.usuarios_id = u.idUsuarios AND w.situacao = 1
            WHERE o.status NOT IN ('Finalizado', 'Cancelado', 'Faturado')
              AND DATE(o.dataFinal) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
        """
        oss = execute_query(sql)
        if not oss:
            return

        # Construir lista de numeros a notificar
        numeros_destino = set()
        if agendados:
            numeros_destino = {a['numero_telefone'] for a in agendados}
        else:
            # Fallback: notificar tecnicos responsaveis + admin
            for os_item in oss:
                if os_item.get('numero_telefone'):
                    numeros_destino.add(os_item['numero_telefone'])
            if ADMIN_NUMERO:
                numeros_destino.add(ADMIN_NUMERO)

        notificados = set()
        for numero in numeros_destino:
            if numero in notificados:
                continue
            os_do_dest = [o for o in oss if o.get('numero_telefone') == numero]
            if not os_do_dest:
                os_do_dest = oss[:5]  # Admin ve todas

            msg = "⚠️ *OS vencendo amanha:*\n\n"
            for o in os_do_dest[:5]:
                emoji = _fmt_status_emoji(o.get('status', ''))
                msg += f"{emoji} *#{o['idOs']}* — {o['nomeCliente']}\n"
                msg += f"   {o['descricaoProduto'] or 'Nao informado'} · {o['status']}\n\n"
            evo.enviar_texto(numero, msg)
            registrar_log(numero, 'saida', msg, 'notificacao_vencendo', 'enviado')
            notificados.add(numero)

            execute_update(
                """UPDATE agente_ia_notificacoes_agendadas
                   SET ultimo_envio = NOW()
                   WHERE numero_telefone = :numero AND tipo_notificacao = 'os_vencendo'""",
                {'numero': numero}
            )
            time.sleep(1)
    except Exception as e:
        logger.error(f"Erro ao notificar OS vencendo: {e}")


# ========== DASHBOARD ENDPOINTS ==========

DASHBOARD_HTML_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'templates', 'dashboard.html')


@app.get('/dashboard')
async def dashboard_page(key: str = None):
    """Serve a pagina do dashboard. Requer API key via query param."""
    if config.AGENT_API_KEY and key != config.AGENT_API_KEY:
        raise HTTPException(status_code=401, detail="API Key invalida")
    if not os.path.exists(DASHBOARD_HTML_PATH):
        raise HTTPException(status_code=404, detail="Dashboard nao encontrado")
    from fastapi.responses import FileResponse
    return FileResponse(DASHBOARD_HTML_PATH, media_type='text/html')


@app.get('/api/stats', dependencies=[Depends(verificar_api_key)])
async def api_stats():
    """Retorna estatisticas do agente."""
    return get_stats()


@app.get('/api/config', dependencies=[Depends(verificar_api_key)])
async def api_config():
    """Retorna configuracoes atuais (valores sensiveis mascarados)."""
    return get_config_masked()


@app.put('/api/config', dependencies=[Depends(verificar_api_key)])
async def api_config_update(request: Request):
    """Atualiza configuracoes no .env."""
    body = await request.json()
    result = save_env_config(body)
    return result


@app.get('/api/logs', dependencies=[Depends(verificar_api_key)])
async def api_logs(numero: str = None, tipo: str = None, intencao: str = None, limite: int = 100):
    """Retorna logs de interacao."""
    return get_logs(limite=limite, numero=numero, tipo=tipo, intencao=intencao)


@app.get('/api/sessions', dependencies=[Depends(verificar_api_key)])
async def api_sessions():
    """Retorna sessoes ativas."""
    return get_sessions()


@app.delete('/api/sessions/{numero}', dependencies=[Depends(verificar_api_key)])
async def api_session_delete(numero: str):
    """Remove uma sessao ativa."""
    success = delete_session(numero)
    return {'success': success}


@app.get('/api/notifications', dependencies=[Depends(verificar_api_key)])
async def api_notifications():
    """Retorna notificacoes agendadas."""
    return get_notifications()


@app.post('/api/notifications', dependencies=[Depends(verificar_api_key)])
async def api_notification_create(request: Request):
    """Cria uma notificacao agendada."""
    body = await request.json()
    numero = body.get('numero_telefone', '')
    tipo = body.get('tipo_notificacao', 'relatorio_diario')
    horario = body.get('horario', '08:00')
    dias = body.get('dias_semana', '1,2,3,4,5')
    return create_notification(numero, tipo, horario, dias)


@app.delete('/api/notifications/{notif_id}', dependencies=[Depends(verificar_api_key)])
async def api_notification_delete(notif_id: int):
    """Remove uma notificacao agendada."""
    success = delete_notification(notif_id)
    return {'success': success}


@app.post('/api/test/evolution', dependencies=[Depends(verificar_api_key)])
async def api_test_evolution():
    """Testa conexao com Evolution API."""
    return test_evolution()


@app.post('/api/test/whisper', dependencies=[Depends(verificar_api_key)])
async def api_test_whisper():
    """Testa conexao com Whisper ASR."""
    return test_whisper()


@app.post('/api/test/database', dependencies=[Depends(verificar_api_key)])
async def api_test_database():
    """Testa conexao com banco de dados."""
    return test_database()


@app.post('/api/test/llm', dependencies=[Depends(verificar_api_key)])
async def api_test_llm():
    """Testa classificacao com LLM."""
    return test_llm()


@app.post('/api/send-message', dependencies=[Depends(verificar_api_key)])
async def api_send_message(request: Request):
    """Envia mensagem WhatsApp para um numero."""
    body = await request.json()
    numero = body.get('numero', '')
    mensagem = body.get('mensagem', '')
    if not numero or not mensagem:
        raise HTTPException(status_code=400, detail='Campos obrigatorios: numero, mensagem')
    numero = limpar_numero(numero)
    try:
        evo.enviar_texto(numero, mensagem)
        increment_stat('mensagens_processadas')
        registrar_log(numero, 'saida', mensagem, 'manual', 'enviado')
        return {'success': True, 'message': f'Mensagem enviada para {numero}'}
    except Exception as ex:
        return {'success': False, 'error': str(ex)}


@app.post('/api/trigger-report', dependencies=[Depends(verificar_api_key)])
async def api_trigger_report():
    """Dispara relatorio diario manualmente."""
    try:
        enviar_relatorio_diario()
        return {'success': True, 'message': 'Relatorio diario disparado'}
    except Exception as ex:
        return {'success': False, 'error': str(ex)}


# ========== LIMPEZA DE PDFs TEMPORARIOS ==========

def limpar_pdfs_temp():
    """Remove PDFs com mais de 1 hora no diretorio temporario."""
    dir_pdfs = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'relatorios_temp')
    if not os.path.isdir(dir_pdfs):
        return
    agora = time.time()
    limite = 3600  # 1 hora
    removidos = 0
    for arq in glob_module.glob(os.path.join(dir_pdfs, '*.pdf')):
        try:
            if agora - os.path.getmtime(arq) > limite:
                os.remove(arq)
                removidos += 1
        except Exception:
            pass
    if removidos:
        logger.info(f"Limpeza PDFs: {removidos} arquivo(s) removido(s)")


# ========== SCHEDULER ==========

scheduler = BackgroundScheduler(timezone='America/Sao_Paulo')

# Relatorio diario
if config.RELATORIO_DIARIO_HORA:
    hora, minuto = config.RELATORIO_DIARIO_HORA.split(':')
    scheduler.add_job(enviar_relatorio_diario, 'cron', hour=int(hora), minute=int(minuto), id='relatorio_diario')
    logger.info(f"Scheduler: relatorio diario agendado para {config.RELATORIO_DIARIO_HORA}")

# Notificacao de OS vencendo
if config.NOTIFICACAO_VENCENDO_HORA:
    hora, minuto = config.NOTIFICACAO_VENCENDO_HORA.split(':')
    scheduler.add_job(notificar_os_vencendo, 'cron', hour=int(hora), minute=int(minuto), id='notificacao_vencendo')
    logger.info(f"Scheduler: notificacao OS vencendo agendada para {config.NOTIFICACAO_VENCENDO_HORA}")

# Limpeza de PDFs a cada 30 minutos
scheduler.add_job(limpar_pdfs_temp, 'interval', minutes=30, id='limpeza_pdfs')
scheduler.start()

# ========== EXECUCAO ==========

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=config.AGENT_PORT)