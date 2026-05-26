"""
Central command dispatcher for the WhatsApp agent.

Routes classified intents to the appropriate query, action, or
interactive flow.  Module-level dependencies are set via ``init()``
so that main.py can inject the shared instances after they are
created.

Heavy logic (OS creation, status changes, etc.) lives in dedicated
service modules; this file only dispatches.
"""

import logging
import re

from database import execute_update
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services.session_store import SessionStore
from services import nlp
from services.os_creation import processar_criacao_os
from services.os_status import processar_alterar_status
from services.user_profile import PERMISSOES_MAP, get_perfil, eh_admin, eh_admin_ou_tecnico, eh_cliente
from services.webhook_handler import registrar_log

logger = logging.getLogger(__name__)

# ---------- Module-level state (set via init) ----------
evo: EvolutionAPI | None = None
queries: MaposQueries | None = None
sessions: SessionStore | None = None


def init(evo_instance: EvolutionAPI, queries_instance: MaposQueries,
         sessions_instance: SessionStore):
    """Inject shared service instances."""
    global evo, queries, sessions
    evo = evo_instance
    queries = queries_instance
    sessions = sessions_instance


# ============================================================
# Central command dispatcher
# ============================================================

def processar_comando(comando: str, params: dict, usuario: dict, texto_original: str = '') -> dict:
    """Process the classified command and return response data."""

    numero = usuario.get('numero', '') if usuario else ''

    # If there is an active OS creation session, redirect to the interactive flow
    _os_check = sessions.get_os_session(numero) if numero else None
    if _os_check and not _os_check.get('expired'):
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
        if not usuario or not eh_admin(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para administradores.'}
        if numero:
            resposta = processar_criacao_os(numero, texto_original, usuario)
            return {'mensagem': resposta}
        else:
            return {'mensagem': '❌ Nao foi possivel identificar seu numero para criar OS.'}

    elif comando == 'alterar_status_os':
        if not usuario or not eh_admin(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para administradores.'}
        if numero:
            resposta = processar_alterar_status(numero, texto_original, usuario)
            return {'mensagem': resposta}
        else:
            return {'mensagem': '❌ Nao foi possivel identificar seu numero para alterar status.'}

    elif comando == 'checkin_tecnico':
        if not usuario or not eh_admin_ou_tecnico(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para tecnicos e administradores.'}
        params = nlp.extrair_parametros(texto_original, 'checkin_tecnico')
        os_id = params.get('os_id')
        if not os_id:
            match = re.search(r'#?\s*(\d+)', texto_original)
            if match:
                os_id = int(match.group(1))
        if os_id:
            os_data = queries.buscar_os(os_id)
            if os_data:
                from datetime import datetime
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
        if not usuario or not eh_admin_ou_tecnico(usuario):
            return {'mensagem': '🔒 Comando disponivel apenas para tecnicos e administradores.'}
        params = nlp.extrair_parametros(texto_original, 'checkout_tecnico')
        os_id = params.get('os_id')
        if not os_id:
            match = re.search(r'#?\s*(\d+)', texto_original)
            if match:
                os_id = int(match.group(1))
        if os_id:
            os_data = queries.buscar_os(os_id)
            if os_data:
                from datetime import datetime
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

    # === REPORTS WITH GLM ANALYSIS ===
    elif comando == 'relatorio_financeiro':
        resultado = queries.relatorio_financeiro()
        dados = resultado
        if usuario and eh_admin_ou_tecnico(usuario):
            from services.reports import gerar_pdf_relatorio
            pdf_url = gerar_pdf_relatorio('resumo_financeiro', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_vendas':
        resultado = queries.relatorio_vendas()
        dados = resultado
        if usuario and eh_admin(usuario):
            from services.reports import gerar_pdf_relatorio
            pdf_url = gerar_pdf_relatorio('vendas', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_estoque':
        resultado = queries.relatorio_estoque()
        dados = resultado
        if usuario and eh_admin(usuario):
            from services.reports import gerar_pdf_relatorio
            pdf_url = gerar_pdf_relatorio('estoque', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_produtividade':
        resultado = queries.relatorio_produtividade()
        dados = resultado
        if usuario and eh_admin(usuario):
            from services.reports import gerar_pdf_relatorio
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
            from services.reports import gerar_pdf_relatorio
            pdf_url = gerar_pdf_relatorio('os_mes', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'relatorio_atrasados':
        resultado = queries.relatorio_atrasados()
        dados = resultado
        if usuario and eh_admin(usuario):
            from services.reports import gerar_pdf_relatorio
            pdf_url = gerar_pdf_relatorio('os_periodo', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    elif comando == 'os_finalizadas_mes':
        resultado = queries.os_finalizadas_mes()
        dados = resultado
        if usuario and eh_admin(usuario):
            from services.reports import gerar_pdf_relatorio
            pdf_url = gerar_pdf_relatorio('os_finalizadas', numero)
            if pdf_url:
                dados['pdf_url'] = pdf_url

    return dados