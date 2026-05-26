from sqlalchemy import create_engine, text
from sqlalchemy.pool import QueuePool
from sqlalchemy.exc import OperationalError
import config
import logging
import time

logger = logging.getLogger(__name__)

MAX_RETRIES = 3
RETRY_DELAY = 0.5  # seconds

# Criar engine com pool de conexoes
engine = create_engine(
    config.DATABASE_URL,
    poolclass=QueuePool,
    pool_size=5,
    max_overflow=10,
    pool_pre_ping=True,
    pool_recycle=3600,
    echo=config.DEBUG
)

def get_connection():
    """Retorna uma conexao do pool"""
    return engine.connect()

def _is_deadlock(exc):
    """Verifica se a excecao e um deadlock MySQL (error 1213 ou 1205)"""
    msg = str(exc).lower()
    return 'deadlock' in msg or '1213' in msg or '1205' in msg or 'lock wait timeout' in msg

def _retry_on_deadlock(fn, *args, **kwargs):
    """Executa funcao com retry em caso de deadlock"""
    for attempt in range(MAX_RETRIES):
        try:
            return fn(*args, **kwargs)
        except OperationalError as e:
            if _is_deadlock(e) and attempt < MAX_RETRIES - 1:
                delay = RETRY_DELAY * (2 ** attempt)
                logger.warning(f"Deadlock detectado, tentativa {attempt + 1}/{MAX_RETRIES}, aguardando {delay}s")
                time.sleep(delay)
                continue
            raise
        except Exception as e:
            if _is_deadlock(e) and attempt < MAX_RETRIES - 1:
                delay = RETRY_DELAY * (2 ** attempt)
                logger.warning(f"Lock timeout, tentativa {attempt + 1}/{MAX_RETRIES}, aguardando {delay}s")
                time.sleep(delay)
                continue
            raise

def execute_query(sql, params=None):
    """Executa uma query e retorna lista de dicts"""
    def _run():
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            rows = result.mappings().all()
            return [dict(row) for row in rows]
    try:
        return _retry_on_deadlock(_run)
    except Exception as e:
        logger.error(f"Erro ao executar query: {e}")
        raise

def execute_scalar(sql, params=None):
    """Executa uma query e retorna um unico valor"""
    def _run():
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            row = result.fetchone()
            if row:
                return row[0]
            return None
    try:
        return _retry_on_deadlock(_run)
    except Exception as e:
        logger.error(f"Erro ao executar scalar: {e}")
        raise

def execute_insert(sql, params=None):
    """Executa INSERT e retorna o ID gerado"""
    def _run():
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            conn.commit()
            return result.lastrowid
    try:
        return _retry_on_deadlock(_run)
    except Exception as e:
        logger.error(f"Erro ao executar insert: {e}")
        raise

def execute_update(sql, params=None):
    """Executa UPDATE/DELETE"""
    def _run():
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            conn.commit()
            return result.rowcount
    try:
        return _retry_on_deadlock(_run)
    except Exception as e:
        logger.error(f"Erro ao executar update: {e}")
        raise
