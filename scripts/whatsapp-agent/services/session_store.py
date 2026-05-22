"""
Persistencia de sessoes interativas via MySQL.
Substitui os dicts em memoria (_sessoes_os, _sessoes_status) para que
sessoes sobrevivam a restarts do agente.
"""
import json
import time
import logging
from database import execute_query, execute_insert, execute_update

logger = logging.getLogger(__name__)

SESSAO_TTL = 900  # 15 minutos


class SessionStore:
    """Armazena sessoes interativas (criacao de OS, alteracao de status) no banco."""

    def __init__(self):
        self._ensure_table()

    def _ensure_table(self):
        """Cria a tabela de sessoes se nao existir."""
        sql = """
            CREATE TABLE IF NOT EXISTS whatsapp_sessoes (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                numero_telefone VARCHAR(20) NOT NULL,
                tipo ENUM('os','status') NOT NULL,
                etapa VARCHAR(50) NOT NULL,
                dados JSON NOT NULL,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY idx_numero_tipo (numero_telefone, tipo),
                KEY idx_criado (criado_em)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        """
        try:
            from database import engine
            from sqlalchemy import text
            with engine.connect() as conn:
                conn.execute(text(sql))
                conn.commit()
        except Exception as e:
            logger.warning(f"Tabela whatsapp_sessoes ja existe ou erro: {e}")

    # ---- Sessao de Criacao de OS ----

    def get_os_session(self, numero: str) -> dict | None:
        """Retorna a sessao de criacao de OS para o numero, ou None.
        Se a sessao expirou, retorna None e o chamador deve notificar o usuario."""
        rows = execute_query(
            "SELECT etapa, dados FROM whatsapp_sessoes WHERE numero_telefone = :numero AND tipo = 'os'",
            {'numero': numero}
        )
        if not rows:
            return None
        row = rows[0]
        dados = row['dados'] if isinstance(row['dados'], dict) else json.loads(row['dados'])
        # Verificar TTL — se expirou, retorna None mas marca como expirada
        if self._expire_old(numero, 'os'):
            return {'expired': True}
        return {'etapa': row['etapa'], 'dados': dados}

    def set_os_session(self, numero: str, etapa: str, dados: dict, clientes: list = None):
        """Salva ou atualiza a sessao de criacao de OS."""
        full_dados = dict(dados)
        if clientes:
            full_dados['_clientes'] = clientes
        dados_json = json.dumps(full_dados, ensure_ascii=False, default=str)
        try:
            execute_update(
                """INSERT INTO whatsapp_sessoes (numero_telefone, tipo, etapa, dados)
                   VALUES (:numero, 'os', :etapa, :dados)
                   ON DUPLICATE KEY UPDATE etapa = :etapa, dados = :dados, atualizado_em = NOW()""",
                {'numero': numero, 'etapa': etapa, 'dados': dados_json}
            )
        except Exception:
            # Fallback: tenta UPDATE se INSERT falhar por chave duplicada
            execute_update(
                """UPDATE whatsapp_sessoes SET etapa = :etapa, dados = :dados, atualizado_em = NOW()
                   WHERE numero_telefone = :numero AND tipo = 'os'""",
                {'numero': numero, 'etapa': etapa, 'dados': dados_json}
            )

    def del_os_session(self, numero: str):
        """Remove a sessao de criacao de OS."""
        execute_update(
            "DELETE FROM whatsapp_sessoes WHERE numero_telefone = :numero AND tipo = 'os'",
            {'numero': numero}
        )

    # ---- Sessao de Alteracao de Status ----

    def get_status_session(self, numero: str) -> dict | None:
        """Retorna a sessao de alteracao de status, ou None.
        Se a sessao expirou, retorna None mas indica que expirou."""
        rows = execute_query(
            "SELECT etapa, dados FROM whatsapp_sessoes WHERE numero_telefone = :numero AND tipo = 'status'",
            {'numero': numero}
        )
        if not rows:
            return None
        row = rows[0]
        dados = row['dados'] if isinstance(row['dados'], dict) else json.loads(row['dados'])
        if self._expire_old(numero, 'status'):
            return {'expired': True}
        return {'etapa': row['etapa'], 'dados': dados}

    def set_status_session(self, numero: str, etapa: str, dados: dict):
        """Salva ou atualiza a sessao de alteracao de status."""
        dados_json = json.dumps(dados, ensure_ascii=False, default=str)
        try:
            execute_update(
                """INSERT INTO whatsapp_sessoes (numero_telefone, tipo, etapa, dados)
                   VALUES (:numero, 'status', :etapa, :dados)
                   ON DUPLICATE KEY UPDATE etapa = :etapa, dados = :dados, atualizado_em = NOW()""",
                {'numero': numero, 'etapa': etapa, 'dados': dados_json}
            )
        except Exception:
            execute_update(
                """UPDATE whatsapp_sessoes SET etapa = :etapa, dados = :dados, atualizado_em = NOW()
                   WHERE numero_telefone = :numero AND tipo = 'status'""",
                {'numero': numero, 'etapa': etapa, 'dados': dados_json}
            )

    def del_status_session(self, numero: str):
        """Remove a sessao de alteracao de status."""
        execute_update(
            "DELETE FROM whatsapp_sessoes WHERE numero_telefone = :numero AND tipo = 'status'",
            {'numero': numero}
        )

    # ---- Limpeza ----

    def cleanup_expired(self):
        """Remove sessoes expiradas (mais antigas que SESSAO_TTL segundos)."""
        try:
            execute_update(
                """DELETE FROM whatsapp_sessoes
                   WHERE atualizado_em < DATE_SUB(NOW(), INTERVAL :ttl SECOND)""",
                {'ttl': SESSAO_TTL}
            )
        except Exception as e:
            logger.error(f"Erro ao limpar sessoes expiradas: {e}")

    def _expire_old(self, numero: str, tipo: str) -> bool:
        """Verifica se a sessao expirou e a remove se necessario.
        Retorna True se a sessao expirou (foi removida), False caso contrario.
        Usa NOW() do MySQL para evitar problemas de timezone."""
        rows = execute_query(
            """SELECT atualizado_em < DATE_SUB(NOW(), INTERVAL :ttl SECOND) as expirou
               FROM whatsapp_sessoes
               WHERE numero_telefone = :numero AND tipo = :tipo""",
            {'numero': numero, 'tipo': tipo, 'ttl': SESSAO_TTL}
        )
        if rows and rows[0].get('expirou'):
            if tipo == 'os':
                self.del_os_session(numero)
            else:
                self.del_status_session(numero)
            return True
        return False