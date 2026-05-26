"""
Funcoes auxiliares para o Dashboard do Agente WhatsApp.
"""
import os
import time
import logging
from datetime import datetime
from database import execute_query, execute_scalar
from services.user_profile import PERMISSOES_MAP, get_perfil

logger = logging.getLogger(__name__)

# Tempo de inicio do agente
START_TIME = time.time()

# Contadores em memoria
_stats = {
    'mensagens_processadas': 0,
    'comandos_executados': 0,
    'erros': 0,
    'llm_chamadas': 0,
    'llm_erros': 0,
}


def increment_stat(key: str, value: int = 1):
    """Incrementa um contador estatistico."""
    _stats[key] = _stats.get(key, 0) + value


def get_stats() -> dict:
    """Retorna estatisticas do agente."""
    uptime = time.time() - START_TIME
    horas = int(uptime // 3600)
    minutos = int((uptime % 3600) // 60)

    # Contar logs de hoje
    try:
        msgs_hoje = execute_scalar(
            "SELECT COUNT(*) FROM whatsapp_log_interacoes WHERE DATE(created_at) = CURDATE()"
        ) or 0
        erros_hoje = execute_scalar(
            "SELECT COUNT(*) FROM whatsapp_log_interacoes WHERE DATE(created_at) = CURDATE() AND status = 'erro'"
        ) or 0
    except Exception as exc:
        logger.exception("Error in get_stats - counting today's logs: %s", exc)
        msgs_hoje = 0
        erros_hoje = 0

    # Contar sessoes ativas
    try:
        sessoes_ativas = execute_scalar(
            "SELECT COUNT(*) FROM whatsapp_sessoes WHERE atualizado_em > DATE_SUB(NOW(), INTERVAL 600 SECOND)"
        ) or 0
    except Exception as exc:
        logger.exception("Error in get_stats - counting active sessions: %s", exc)
        sessoes_ativas = 0

    # Contar numeros integrados
    try:
        numeros_integrados = execute_scalar(
            "SELECT COUNT(*) FROM whatsapp_integracao WHERE situacao = 1"
        ) or 0
    except Exception as exc:
        logger.exception("Error in get_stats - counting integrated numbers: %s", exc)
        numeros_integrados = 0

    # Comandos por tipo (ultimos 7 dias)
    try:
        comandos_por_tipo = execute_query(
            """SELECT intencao_detectada, COUNT(*) as qtd
               FROM whatsapp_log_interacoes
               WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 AND intencao_detectada IS NOT NULL
               GROUP BY intencao_detectada
               ORDER BY qtd DESC
               LIMIT 15"""
        )
    except Exception as exc:
        logger.exception("Error in get_stats - counting commands by type: %s", exc)
        comandos_por_tipo = []

    return {
        'uptime_seconds': round(uptime),
        'uptime_human': f"{horas}h {minutos}m",
        'mensagens_processadas': _stats.get('mensagens_processadas', 0),
        'comandos_executados': _stats.get('comandos_executados', 0),
        'erros': _stats.get('erros', 0),
        'llm_chamadas': _stats.get('llm_chamadas', 0),
        'msgs_hoje': int(msgs_hoje),
        'erros_hoje': int(erros_hoje),
        'sessoes_ativas': int(sessoes_ativas),
        'numeros_integrados': int(numeros_integrados),
        'comandos_por_tipo': comandos_por_tipo,
    }


def get_logs(limite: int = 100, numero: str = None, tipo: str = None,
             intencao: str = None) -> list:
    """Retorna logs de interacao do banco."""
    sql = """
        SELECT id, numero_telefone, tipo_mensagem, direcao, conteudo,
               intencao_detectada, status, created_at
        FROM whatsapp_log_interacoes
        WHERE 1=1
    """
    params = {}
    if numero:
        sql += " AND numero_telefone LIKE :numero"
        params['numero'] = f'%{numero}%'
    if tipo:
        sql += " AND tipo_mensagem = :tipo"
        params['tipo'] = tipo
    if intencao:
        sql += " AND intencao_detectada = :intencao"
        params['intencao'] = intencao
    sql += " ORDER BY id DESC LIMIT :limite"
    params['limite'] = limite

    try:
        return execute_query(sql, params)
    except Exception as e:
        logger.error(f"Erro ao buscar logs: {e}")
        return []


def get_sessions() -> list:
    """Retorna sessoes interativas ativas."""
    try:
        return execute_query(
            """SELECT id, numero_telefone, tipo, etapa, dados, criado_em, atualizado_em
               FROM whatsapp_sessoes
               WHERE atualizado_em > DATE_SUB(NOW(), INTERVAL 600 SECOND)
               ORDER BY atualizado_em DESC"""
        )
    except Exception as e:
        logger.error(f"Erro ao buscar sessoes: {e}")
        return []


def get_notifications() -> list:
    """Retorna notificacoes agendadas."""
    try:
        return execute_query(
            """SELECT id, numero_telefone, tipo_notificacao, horario, dias_semana,
                      situacao, ultimo_envio, created_at
               FROM agente_ia_notificacoes_agendadas
               ORDER BY tipo_notificacao, horario"""
        )
    except Exception as e:
        logger.error(f"Erro ao buscar notificacoes: {e}")
        return []


def create_notification(numero: str, tipo: str, horario: str, dias_semana: str = '1,2,3,4,5') -> dict:
    """Cria uma nova notificacao agendada."""
    from database import execute_insert
    try:
        id_ = execute_insert(
            """INSERT INTO agente_ia_notificacoes_agendadas
               (numero_telefone, tipo_notificacao, horario, dias_semana, situacao)
               VALUES (:numero, :tipo, :horario, :dias, 1)""",
            {'numero': numero, 'tipo': tipo, 'horario': horario, 'dias': dias_semana}
        )
        return {'success': True, 'id': id_}
    except Exception as e:
        logger.error(f"Erro ao criar notificacao: {e}")
        return {'success': False, 'error': str(e)}


def delete_notification(notif_id: int) -> bool:
    """Remove uma notificacao agendada."""
    from database import execute_update
    try:
        rows = execute_update(
            "DELETE FROM agente_ia_notificacoes_agendadas WHERE id = :id",
            {'id': notif_id}
        )
        return rows > 0
    except Exception as e:
        logger.error(f"Erro ao remover notificacao: {e}")
        return False


def delete_session(numero: str) -> bool:
    """Remove uma sessao ativa."""
    from database import execute_update
    try:
        execute_update(
            "DELETE FROM whatsapp_sessoes WHERE numero_telefone = :numero",
            {'numero': numero}
        )
        return True
    except Exception as e:
        logger.error(f"Erro ao remover sessao: {e}")
        return False


def mask_sensitive(value: str, visible_chars: int = 4) -> str:
    """Mascara valores sensiveis (senhas, API keys)."""
    if not value or len(value) <= visible_chars:
        return '***' if value else ''
    return value[:visible_chars] + '*' * (len(value) - visible_chars)


SENSITIVE_KEYS = {
    'MYSQL_PASS', 'EVOLUTION_API_KEY', 'AGENT_API_KEY', 'MAPOS_API_KEY',
    'LLM_CLOUD_API_KEY', 'OLLAMA_API_KEY', 'APP_ENCRYPTION_KEY',
    'API_JWT_KEY', 'API_MAPOS_KEY', 'DB_PASSWORD',
}


def get_config_masked() -> dict:
    """Retorna configuracoes atuais com valores sensiveis mascarados."""
    import config
    result = {}
    for attr in dir(config):
        if attr.startswith('_'):
            continue
        value = getattr(config, attr)
        if isinstance(value, (str, int, float, bool)):
            if attr in SENSITIVE_KEYS:
                result[attr] = mask_sensitive(str(value))
            else:
                result[attr] = value
    return result


def get_config_raw() -> dict:
    """Retorna configuracoes com valores reais (para edicao)."""
    import config
    result = {}
    for attr in dir(config):
        if attr.startswith('_'):
            continue
        value = getattr(config, attr)
        if isinstance(value, (str, int, float, bool)):
            result[attr] = value
    return result


def save_env_config(updates: dict) -> dict:
    """Salva configuracoes no arquivo .env e recarrega."""
    import config
    env_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '.env')
    if not os.path.exists(env_path):
        env_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), '.env')

    # Ler .env existente
    lines = []
    if os.path.exists(env_path):
        with open(env_path, 'r', encoding='utf-8') as f:
            lines = f.readlines()

    # Atualizar valores
    env_dict = {}
    for line in lines:
        line = line.strip()
        if line and not line.startswith('#') and '=' in line:
            key, _, val = line.partition('=')
            env_dict[key.strip()] = val.strip().strip('"').strip("'")

    # Aplicar updates
    for key, value in updates.items():
        if isinstance(value, bool):
            env_dict[key] = 'true' if value else 'false'
        else:
            env_dict[key] = str(value)

    # Reconstruir arquivo preservando comentarios e ordem
    new_lines = []
    written_keys = set()
    for line in lines:
        stripped = line.strip()
        if stripped and not stripped.startswith('#') and '=' in stripped:
            key = stripped.partition('=')[0].strip()
            if key in env_dict:
                val = env_dict[key]
                # Preservar aspas se o valor original tinha
                if any(c in val for c in (' ', '/', '#', ';')):
                    new_lines.append(f"{key}=\"{val}\"\n")
                else:
                    new_lines.append(f"{key}={val}\n")
                written_keys.add(key)
            else:
                new_lines.append(line)
        else:
            new_lines.append(line)

    # Adicionar chaves novas
    for key, value in env_dict.items():
        if key not in written_keys:
            val = str(value)
            if any(c in val for c in (' ', '/', '#', ';')):
                new_lines.append(f"{key}=\"{val}\"\n")
            else:
                new_lines.append(f"{key}={val}\n")

    with open(env_path, 'w', encoding='utf-8') as f:
        f.writelines(new_lines)

    # Recarregar configuracoes
    from dotenv import load_dotenv
    load_dotenv(env_path, override=True)

    # Recarregar variaveis do config
    import importlib
    importlib.reload(config)

    return {'success': True, 'message': 'Configuracoes salvas. Reinicie o agente para aplicar mudancas que nao sao lidas em tempo real.'}


def test_evolution() -> dict:
    """Testa conexao com Evolution API."""
    import requests
    import config
    try:
        url = f"{config.EVOLUTION_URL.rstrip('/')}/instance/connect/{config.EVOLUTION_INSTANCE}"
        headers = {'apikey': config.EVOLUTION_API_KEY}
        resp = requests.get(url, headers=headers, timeout=10)
        return {
            'success': resp.status_code == 200,
            'status_code': resp.status_code,
            'url': url,
            'message': 'Conectado com sucesso' if resp.status_code == 200 else f'Erro HTTP {resp.status_code}'
        }
    except Exception as exc:
        logger.exception("Error in test_evolution: %s", exc)
        return {'success': False, 'message': str(exc)}


def test_whisper() -> dict:
    """Testa conexao com Whisper ASR."""
    import requests
    import config
    try:
        url = f"{config.WHISPER_URL.rstrip('/')}/asr"
        resp = requests.get(url, timeout=10)
        return {
            'success': resp.status_code in (200, 405),  # 405 = GET not allowed mas servidor ok
            'url': url,
            'message': 'Whisper ASR acessivel' if resp.status_code in (200, 405, 422) else f'Erro HTTP {resp.status_code}'
        }
    except Exception as exc:
        logger.exception("Error in test_whisper: %s", exc)
        return {'success': False, 'message': str(exc)}


def test_database() -> dict:
    """Testa conexao com o banco de dados."""
    import config
    try:
        result = execute_scalar("SELECT 1")
        return {
            'success': True,
            'message': f'Conexao OK (resultado: {result})',
            'host': config.MYSQL_HOST,
            'database': config.MYSQL_DB
        }
    except Exception as exc:
        logger.exception("Error in test_database: %s", exc)
        return {'success': False, 'message': str(exc)}


def test_llm() -> dict:
    """Testa classificacao com LLM."""
    from services.llm import classificar_com_llm
    import config
    if not config.LLM_PROVIDER:
        return {'success': True, 'provider': 'regex', 'message': 'Usando regex (nenhum LLM configurado)'}

    try:
        result = classificar_com_llm("qual o status da minha os?")
        if result:
            return {
                'success': True,
                'provider': config.LLM_PROVIDER,
                'model': config.LLM_CLOUD_MODEL or config.LLM_MODEL,
                'result': result,
                'message': f'LLM {config.LLM_PROVIDER} respondeu corretamente'
            }
        return {
            'success': False,
            'provider': config.LLM_PROVIDER,
            'message': 'LLM retornou None (fallback para regex)'
        }
    except Exception as exc:
        logger.exception("Error in test_llm: %s", exc)
        return {'success': False, 'provider': config.LLM_PROVIDER, 'message': str(exc)}