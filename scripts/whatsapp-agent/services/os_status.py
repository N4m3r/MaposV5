"""
Modulo de alteracao interativa de status de OS.

Extrai de main.py a maquina de estados e funcoes auxiliares
para o fluxo de mudanca de status de uma Ordem de Servico.
"""
import json
import logging

import requests as http_requests

import config
from database import execute_query
from services.nlp import _fmt_status_emoji, OS_NUMBER_RE
import services.nlp as nlp

# Dependencias injetadas via init()
evo = None
queries = None
sessions = None
logger = logging.getLogger(__name__)

# Lista de status possiveis para uma OS
STATUS_OS_LISTA = [
    'Aberto', 'Em Andamento', 'Aguardando Pecas',
    'Orcamento', 'Aprovado', 'Faturado', 'Finalizado', 'Cancelado'
]


def init(evo_api, mapos_queries, session_store):
    """Inicializa dependencias do modulo.

    Args:
        evo_api: instancia de EvolutionAPI
        mapos_queries: instancia de MaposQueries
        session_store: instancia de SessionStore
    """
    global evo, queries, sessions
    evo = evo_api
    queries = mapos_queries
    sessions = session_store


# ---- Helpers de sessao ----

def _criar_sessao_status(numero: str, etapa: str, dados: dict):
    """Cria sessao de alteracao de status persistida no banco."""
    sessions.set_status_session(numero, etapa, dados)


def _get_sessao_status(numero: str) -> dict | None:
    """Recupera sessao de status do banco."""
    return sessions.get_status_session(numero)


def _del_sessao_status(numero: str):
    """Remove sessao de status do banco."""
    sessions.del_status_session(numero)


# ---- Formatacao ----

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
    """Retorna apenas a lista de status numerados (alias para _formatar_escolha_status)."""
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


# ---- Sincronizacao com MapOS API ----

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


# ---- Maquina de estados principal ----

def processar_alterar_status(numero: str, texto: str, usuario: dict) -> str:
    """Processa o fluxo interativo de alteracao de status de OS passo a passo."""
    # Importacoes locais para evitar dependencia circular
    from services.user_profile import eh_admin_ou_tecnico, eh_cliente

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
        match = OS_NUMBER_RE.search(texto)
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
            '1': 'Aberto', '2': 'Em Andamento', '3': 'Aguardando Pecas',
            '4': 'Orcamento', '5': 'Aprovado', '6': 'Faturado',
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
                    novo_status = 'Orcamento'
                elif 'aguard' in escolha or 'peca' in escolha or 'peça' in escolha:
                    novo_status = 'Aguardando Pecas'

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