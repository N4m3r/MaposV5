from sqlalchemy import create_engine, text
from sqlalchemy.pool import QueuePool
import config
import logging

logger = logging.getLogger(__name__)

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

def execute_query(sql, params=None):
    """Executa uma query e retorna lista de dicts"""
    try:
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            rows = result.mappings().all()
            return [dict(row) for row in rows]
    except Exception as e:
        logger.error(f"Erro ao executar query: {e}")
        raise

def execute_scalar(sql, params=None):
    """Executa uma query e retorna um unico valor"""
    try:
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            row = result.fetchone()
            if row:
                return row[0]
            return None
    except Exception as e:
        logger.error(f"Erro ao executar scalar: {e}")
        raise

def execute_insert(sql, params=None):
    """Executa INSERT e retorna o ID gerado"""
    try:
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            conn.commit()
            return result.lastrowid
    except Exception as e:
        logger.error(f"Erro ao executar insert: {e}")
        raise

def execute_update(sql, params=None):
    """Executa UPDATE/DELETE"""
    try:
        with get_connection() as conn:
            result = conn.execute(text(sql), params or {})
            conn.commit()
            return result.rowcount
    except Exception as e:
        logger.error(f"Erro ao executar update: {e}")
        raise
