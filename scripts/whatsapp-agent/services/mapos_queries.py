from database import execute_query, execute_scalar

class MaposQueries:
    """Consultas no banco do MapOS para o Agente IA"""

    # ==================== CLIENTE ====================

    def buscar_cliente_por_numero(self, numero: str):
        """Busca cliente vinculado ao numero WhatsApp"""
        sql = """
            SELECT c.idClientes, c.nomeCliente, c.celular, c.email,
                   c.rua, c.numero, c.bairro, c.cidade, c.estado
            FROM clientes c
            JOIN whatsapp_integracao w ON w.clientes_id = c.idClientes
            WHERE w.numero_telefone = :numero AND w.situacao = 1
            LIMIT 1
        """
        rows = execute_query(sql, {'numero': numero})
        return rows[0] if rows else None

    def buscar_cliente_por_nome(self, nome: str):
        """Busca cliente por nome (LIKE)"""
        sql = """
            SELECT idClientes, nomeCliente, celular, email
            FROM clientes
            WHERE nomeCliente LIKE :nome
            LIMIT 5
        """
        return execute_query(sql, {'nome': f'%{nome}%'})

    def total_em_aberto_cliente(self, cliente_id: int):
        """Total em aberto do cliente (cobrancas + OS nao faturadas)"""
        sql = """
            SELECT COALESCE(SUM(valor), 0) as total
            FROM cobrancas
            WHERE clientes_id = :cliente_id AND baixado = 0
        """
        return execute_scalar(sql, {'cliente_id': cliente_id}) or 0

    # ==================== OS ====================

    def listar_os_cliente(self, cliente_id: int, limite: int = 10):
        """Lista OS do cliente"""
        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.garantia,
                   o.descricaoProduto, o.defeito, o.status,
                   o.observacoes, o.laudoTecnico,
                   COALESCE((SELECT SUM(valor) FROM produtos_os WHERE os_id = o.idOs), 0) as total_produtos,
                   COALESCE((SELECT SUM(valor) FROM servicos_os WHERE os_id = o.idOs), 0) as total_servicos
            FROM os o
            WHERE o.clientes_id = :cliente_id
            ORDER BY o.idOs DESC
            LIMIT :limite
        """
        return execute_query(sql, {'cliente_id': cliente_id, 'limite': limite})

    def buscar_os(self, os_id: int):
        """Busca OS especifica"""
        sql = """
            SELECT o.*, c.nomeCliente, c.celular, u.nome as tecnico_nome
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            LEFT JOIN usuarios u ON u.idUsuarios = o.usuarios_id
            WHERE o.idOs = :os_id
            LIMIT 1
        """
        rows = execute_query(sql, {'os_id': os_id})
        return rows[0] if rows else None

    def listar_os_tecnico(self, usuario_id: int, data: str = None):
        """Lista OS atribuidas ao tecnico"""
        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.descricaoProduto,
                   o.defeito, o.status, c.nomeCliente
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            WHERE o.usuarios_id = :usuario_id
        """
        params = {'usuario_id': usuario_id}
        if data:
            sql += " AND DATE(o.dataInicial) = :data"
            params['data'] = data
        sql += " ORDER BY o.idOs DESC"
        return execute_query(sql, params)

    def resumo_os_dia(self, data: str = None):
        """Resumo de OS do dia (para admin)"""
        if not data:
            data = "CURDATE()"
            sql = f"""
                SELECT status, COUNT(*) as quantidade
                FROM os
                WHERE DATE(dataInicial) = {data}
                GROUP BY status
            """
            return execute_query(sql)
        else:
            sql = """
                SELECT status, COUNT(*) as quantidade
                FROM os
                WHERE DATE(dataInicial) = :data
                GROUP BY status
            """
            return execute_query(sql, {'data': data})

    def os_atrasadas(self):
        """OS nao finalizadas com data vencida"""
        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.descricaoProduto,
                   o.defeito, o.status, c.nomeCliente, c.celular
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            WHERE o.status NOT IN ('Finalizado', 'Cancelado', 'Faturado')
              AND o.dataFinal < CURDATE()
            ORDER BY o.dataFinal ASC
            LIMIT 20
        """
        return execute_query(sql)

    # ==================== USUARIO / TECNICO ====================

    def buscar_usuario_por_numero(self, numero: str):
        """Busca usuario (tecnico/admin) vinculado ao numero"""
        sql = """
            SELECT u.idUsuarios, u.nome, u.celular, u.email, p.nome as permissao_nome
            FROM usuarios u
            JOIN permissoes p ON p.idPermissao = u.permissoes_id
            JOIN whatsapp_integracao w ON w.usuarios_id = u.idUsuarios
            WHERE w.numero_telefone = :numero AND w.situacao = 1
            LIMIT 1
        """
        rows = execute_query(sql, {'numero': numero})
        return rows[0] if rows else None

    def total_os_abertas(self):
        """Total de OS em aberto"""
        sql = """
            SELECT COUNT(*) as total
            FROM os
            WHERE status NOT IN ('Finalizado', 'Cancelado')
        """
        return execute_scalar(sql) or 0

    # ==================== VENDAS ====================

    def vendas_pendentes(self, limite: int = 20):
        """Vendas nao faturadas"""
        sql = """
            SELECT v.idVendas, v.data, v.valorTotal, v.faturado,
                   c.nomeCliente, c.celular
            FROM vendas v
            JOIN clientes c ON c.idClientes = v.clientes_id
            WHERE v.faturado = 0
            ORDER BY v.idVendas DESC
            LIMIT :limite
        """
        return execute_query(sql, {'limite': limite})

    # ==================== COBRANCAS ====================

    def cobrancas_vencidas(self, limite: int = 20):
        """Cobrancas vencidas nao pagas"""
        sql = """
            SELECT cb.idCobranca, cb.descricao, cb.valor, cb.data_vencimento,
                   cb.baixado, cb.data_pagamento, c.nomeCliente
            FROM cobrancas cb
            JOIN clientes c ON c.idClientes = cb.clientes_id
            WHERE cb.baixado = 0 AND cb.data_vencimento < CURDATE()
            ORDER BY cb.data_vencimento ASC
            LIMIT :limite
        """
        return execute_query(sql, {'limite': limite})
