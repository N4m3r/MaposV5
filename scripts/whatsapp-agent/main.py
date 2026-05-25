from fastapi import FastAPI, Request, Header, HTTPException, Depends
import logging
import os
import threading
import config
from apscheduler.schedulers.background import BackgroundScheduler

from database import execute_query
from services.evolution_api import EvolutionAPI
from services.mapos_queries import MaposQueries
from services.session_store import SessionStore
from services.dashboard_helpers import (
    get_stats, get_logs, get_sessions, get_notifications,
    create_notification, delete_notification, delete_session,
    mask_sensitive, get_config_masked, get_config_raw, save_env_config,
    test_evolution, test_whisper, test_database, test_llm,
    increment_stat
)

# Imports from extracted modules
from services.os_status import processar_alterar_status
from services.user_profile import (
    identificar_usuario, _menu_lista, _enviar_menu_interativo,
    _enviar_botoes_confirmacao, eh_admin, eh_admin_ou_tecnico,
    eh_cliente, limpar_numero, PERMISSOES_MAP
)
from services.webhook_handler import (
    process_webhook, extrair_numero, extrair_mensagem,
    extrair_audio_info, _is_from_me, _extrair_msg_id,
    _is_duplicado, registrar_log
)
from services.reports import (
    gerar_pdf_relatorio, enviar_pdf_whatsapp,
    analisar_relatorio_com_glm, enviar_relatorio_diario,
    notificar_os_vencendo, _limpar_sessoes_expiradas, limpar_pdfs_temp
)
from services.command_router import processar_comando

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

# Lock para sessao de criacao de OS (evita race condition)
_sessao_lock = threading.Lock()

# Inicializar modulos extraidos
import services.os_creation as os_creation
import services.os_status as os_status_mod
import services.user_profile as user_profile_mod
import services.webhook_handler as webhook_handler_mod
import services.reports as reports_mod
import services.command_router as command_router_mod

os_creation.init(evo, queries, sessions, _sessao_lock)
os_status_mod.init(evo, queries, sessions)
user_profile_mod.init(evo, queries, sessions)
webhook_handler_mod.init(evo, queries, sessions)
reports_mod.init(evo, queries, sessions)
command_router_mod.init(evo, queries, sessions)


# ========== MIDDLEWARE / UTILS ==========


def verificar_api_key(x_api_key: str = Header(None)):
    """Exige API Key valida. Nao aceita chave vazia ou ausente."""
    if not config.AGENT_API_KEY:
        # Se nao ha chave configurada, permitir (modo desenvolvimento)
        return
    if x_api_key == config.AGENT_API_KEY:
        return
    raise HTTPException(status_code=401, detail="API Key invalida")


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
    Delegado para services.webhook_handler.process_webhook.
    """
    try:
        payload = await request.json()
    except Exception:
        payload = {}
    return process_webhook(payload, x_api_key or '')


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