"""
Report generation, PDF delivery, scheduled notifications, and cleanup.

Module-level dependencies are set via ``init()`` so that main.py
can inject the shared instances after they are created.
"""

import json
import logging
import os
import glob as glob_module
import time

import requests as http_requests

import config
from database import execute_query, execute_insert, execute_update
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services.session_store import SessionStore
from services.result import Result
from services import nlp
from services.nlp import _fmt_status_emoji

logger = logging.getLogger(__name__)

# ---------- Module-level state (set via init) ----------
evo: EvolutionAPI | None = None
queries: MaposQueries | None = None
sessions: SessionStore | None = None

ADMIN_NUMERO = config.ADMIN_NUMERO


def init(evo_instance: EvolutionAPI, queries_instance: MaposQueries,
         sessions_instance: SessionStore):
    """Inject shared service instances."""
    global evo, queries, sessions
    evo = evo_instance
    queries = queries_instance
    sessions = sessions_instance


# ============================================================
# PDF generation & delivery
# ============================================================

def gerar_pdf_relatorio(tipo: str, numero: str) -> str:
    """Generate a PDF report via MapOS API and return the download URL."""
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


def enviar_pdf_whatsapp(numero: str, pdf_url: str, caption: str = 'Relatorio') -> Result:
    """Download a PDF and send it via WhatsApp using Evolution API."""
    if not pdf_url:
        return Result.fail('URL vazia')

    try:
        # Download the PDF
        resp = http_requests.get(pdf_url, timeout=60)
        if resp.status_code != 200:
            logger.error(f"Erro ao baixar PDF: status={resp.status_code}")
            return Result.fail(f'HTTP {resp.status_code}')

        # Save temporarily
        import tempfile
        tmp = tempfile.NamedTemporaryFile(suffix='.pdf', delete=False)
        tmp.write(resp.content)
        tmp.close()

        # Send via Evolution API
        try:
            result = evo.enviar_documento(numero, tmp.name, caption)
        finally:
            try:
                os.unlink(tmp.name)
            except Exception as exc:
                logger.debug("Failed to cleanup temp PDF file %s: %s", tmp.name, exc)

        return result
    except Exception as e:
        logger.error(f"Erro ao enviar PDF: {e}")
        return Result.fail(str(e))


# ============================================================
# LLM-based report analysis
# ============================================================

def analisar_relatorio_com_glm(tipo: str, dados: dict) -> str:
    """Use LLM (GLM) to generate a concise analysis summary for a report."""
    if not config.LLM_CLOUD_URL or not config.LLM_CLOUD_MODEL:
        return ''

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


# ============================================================
# Scheduled notifications
# ============================================================

def enviar_relatorio_diario():
    """Send a daily report to admins with active scheduled notifications."""
    try:
        # Look for admins via scheduled notifications table
        agendados = execute_query(
            """SELECT n.numero_telefone, n.tipo_notificacao
               FROM agente_ia_notificacoes_agendadas n
               WHERE n.tipo_notificacao = 'relatorio_diario'
                 AND n.situacao = 1
                 AND (n.horario IS NULL OR TIME(NOW()) BETWEEN TIME(n.horario) - INTERVAL 5 MINUTE AND TIME(n.horario) + INTERVAL 5 MINUTE)
            """
        )

        # If no scheduled entries, use config as fallback
        if not agendados and not config.RELATORIO_DIARIO_HORA:
            return

        # If no scheduled entries but has config, use DB admins
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

            # LLM analysis if available
            if config.LLM_CLOUD_URL:
                try:
                    analise = analisar_relatorio_com_glm('relatorio_os', dados)
                    if analise:
                        evo.enviar_texto(numero, f"📊 *Analise IA:*\n{analise}")
                        registrar_log(numero, 'saida', analise, 'relatorio_diario_glm', 'enviado')
                except Exception as exc:
                    logger.debug("Failed to run LLM analysis for daily report: %s", exc)

            # Update last sent timestamp
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
    """Notify technicians/admins about OS that expire tomorrow."""
    try:
        # Check for scheduled recipients
        agendados = execute_query(
            """SELECT numero_telefone FROM agente_ia_notificacoes_agendadas
               WHERE tipo_notificacao = 'os_vencendo' AND situacao = 1"""
        )

        # If no scheduled entries and no config, skip
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

        # Build the list of destination numbers
        numeros_destino = set()
        if agendados:
            numeros_destino = {a['numero_telefone'] for a in agendados}
        else:
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
                os_do_dest = oss[:5]  # Admin sees all

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


# ============================================================
# Session cleanup
# ============================================================

def _limpar_sessoes_expiradas():
    """Remove expired OS and status sessions and stale dedup entries."""
    sessions.cleanup_expired()
    # Dedup cleanup is handled inside _is_duplicado, but we also
    # do a batch sweep here for thoroughness.
    agora = time.time()
    from services.webhook_handler import _msg_ids, _msg_ids_lock, _DEDUP_TTL
    with _msg_ids_lock:
        expirados = [k for k, v in _msg_ids.items() if agora - v > _DEDUP_TTL]
        for k in expirados:
            del _msg_ids[k]


# ============================================================
# Temporary PDF cleanup
# ============================================================

def limpar_pdfs_temp():
    """Remove PDFs older than 1 hour from the temporary reports directory."""
    dir_pdfs = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'relatorios_temp')
    if not os.path.isdir(dir_pdfs):
        return
    agora = time.time()
    limite = 3600  # 1 hour
    removidos = 0
    for arq in glob_module.glob(os.path.join(dir_pdfs, '*.pdf')):
        try:
            if agora - os.path.getmtime(arq) > limite:
                os.remove(arq)
                removidos += 1
        except Exception as exc:
            logger.debug("Failed to remove temp PDF %s: %s", arq, exc)
    if removidos:
        logger.info(f"Limpeza PDFs: {removidos} arquivo(s) removido(s)")


# ============================================================
# Helper used by reports (log function reuses the one from
# webhook_handler to avoid duplication, but we also keep a local
# wrapper so the module is self-contained when possible).
# ============================================================

def registrar_log(numero: str, direcao: str, conteudo: str,
                  intencao: str = None, status: str = 'recebido'):
    """Log an interaction to the DB (delegates to webhook_handler to avoid duplication)."""
    from services.webhook_handler import registrar_log as _registrar_log
    _registrar_log(numero, direcao, conteudo, intencao, status)