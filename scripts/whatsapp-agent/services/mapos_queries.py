from database import execute_query, execute_scalar, execute_insert
from datetime import date, datetime, timedelta
import re

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

    # Nomes de clientes multi-filial que devem exibir numero da loja
    MULTI_FILIAL_KEYWORDS = ['nova era', 'patio gourmet']

    def _extrair_loja_cnpj(self, documento: str) -> str:
        """Extrai o numero da loja a partir do CNPJ (posicoes 11 e 12).
        Ex: 04.240.370/0057-01 -> Loja 57
            04240370005701      -> Loja 57
        As posicoes 11 e 12 (1-based) do CNPJ sem formatacao
        representam o numero da loja (filial).
        """
        if not documento:
            return ''
        doc = documento.replace('.', '').replace('-', '').replace('/', '')
        # CNPJ tem 14 digitos: 8 base + 4 filial + 2 digito
        # Posicoes 11 e 12 (1-based) = indices 10 e 11 (0-based) = loja
        if len(doc) == 14:
            loja_digits = doc[10:12]
            num = int(loja_digits)
            return f'Loja {num}'
        # Formato com pontuacao: 04.240.370/0057-01
        import re
        match = re.search(r'/(\d{4})-', documento)
        if match:
            num = int(match.group(1))
            return f'Loja {num}'
        return ''

    def buscar_cliente_por_nome(self, nome: str):
        """Busca cliente por nome, CNPJ/CPF ou ID. Para clientes multi-filial,
        exibe o numero da loja extraido do CNPJ."""
        # Limpar entrada
        nome_limpo = nome.strip()
        if not nome_limpo:
            return []

        # Se digitou apenas numeros, pode ser ID ou CNPJ/CPF
        digitos = re.sub(r'\D', '', nome_limpo)

        # PRIORIDADE 1: Busca por "Loja X" — mapeia para numero da filial no CNPJ (posicoes 11-12)
        loja_match = re.search(r'[Ll]oja\s*(\d+)', nome_limpo)
        if loja_match:
            num_loja = int(loja_match.group(1))
            # Buscar clientes multi-filial conhecidos (Nova Era, Patio Gourmet) + qualquer um com CNPJ de filial
            filiais = execute_query(
                """SELECT idClientes, nomeCliente, documento, celular, email
                   FROM clientes
                   WHERE documento LIKE :padrao_fmt
                      OR (REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') LIKE :padrao_limpo
                          AND LENGTH(REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '')) = 14)
                   LIMIT 100""",
                {'padrao_fmt': f'%/{num_loja:04d}-%', 'padrao_limpo': f'%{num_loja:04d}%'}
            )
            # Filtrar: manter apenas os cujo numero da loja (posicoes 11-12) bate exatamente
            resultado = []
            for r in filiais:
                loja = self._extrair_loja_cnpj(r.get('documento', '') or '')
                if loja == f'Loja {num_loja}':
                    r['nomeExibicao'] = f"{r['nomeCliente']} - {loja}"
                    resultado.append(r)
            if resultado:
                return resultado[:10]

        # PRIORIDADE 2: Busca por CNPJ/CPF (digitos longos)
        if len(digitos) >= 8:
            by_doc = execute_query(
                "SELECT idClientes, nomeCliente, documento, celular, email FROM clientes WHERE REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') LIKE :doc LIMIT 10",
                {'doc': f'%{digitos}%'}
            )
            if by_doc:
                resultado = []
                for r in by_doc:
                    loja = self._extrair_loja_cnpj(r.get('documento', '') or '')
                    r['nomeExibicao'] = f"{r['nomeCliente']} - {loja}" if loja else r['nomeCliente']
                    resultado.append(r)
                return resultado[:10]

        # PRIORIDADE 3: Busca por ID exato (so se digitou apenas numeros, sem "Loja")
        if digitos and len(digitos) <= 6 and digitos.isdigit() and 'loja' not in nome_limpo.lower():
            by_id = execute_query(
                "SELECT idClientes, nomeCliente, documento, celular, email FROM clientes WHERE idClientes = :id LIMIT 1",
                {'id': int(digitos)}
            )
            if by_id:
                for r in by_id:
                    loja = self._extrair_loja_cnpj(r.get('documento', '') or '')
                    r['nomeExibicao'] = f"{r['nomeCliente']} - {loja}" if loja else r['nomeCliente']
                return by_id

        # PRIORIDADE 4: Busca por nome (LIKE)
        rows = execute_query(
            "SELECT idClientes, nomeCliente, documento, celular, email FROM clientes WHERE nomeCliente LIKE :nome LIMIT 20",
            {'nome': f'%{nome_limpo}%'}
        )
        if not rows:
            return []

        nome_lower = nome_limpo.lower()
        eh_multifilial = any(kw in nome_lower for kw in self.MULTI_FILIAL_KEYWORDS)

        if eh_multifilial:
            # Agrupa por nome base e adiciona numero da loja
            resultado = []
            vistos = set()
            for r in rows:
                loja = self._extrair_loja_cnpj(r.get('documento', '') or '')
                nome_fmt = f"{r['nomeCliente']} - {loja}" if loja else r['nomeCliente']
                chave = (r['nomeCliente'], r.get('documento', ''))
                if chave not in vistos:
                    vistos.add(chave)
                    resultado.append({
                        'idClientes': r['idClientes'],
                        'nomeCliente': r['nomeCliente'],
                        'nomeExibicao': nome_fmt,
                        'documento': r.get('documento', ''),
                        'celular': r.get('celular', ''),
                        'email': r.get('email', ''),
                    })
            resultado.sort(key=lambda x: x['nomeExibicao'])
            return resultado[:10]

        for r in rows:
            loja = self._extrair_loja_cnpj(r.get('documento', '') or '')
            r['nomeExibicao'] = f"{r['nomeCliente']} - {loja}" if loja else r['nomeCliente']
        return rows[:5]

    def buscar_cliente_por_id(self, cliente_id: int):
        """Busca cliente por ID"""
        sql = """
            SELECT idClientes, nomeCliente, celular, email,
                   rua, numero, bairro, cidade, estado
            FROM clientes
            WHERE idClientes = :id
            LIMIT 1
        """
        rows = execute_query(sql, {'id': cliente_id})
        return rows[0] if rows else None

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
                   COALESCE((SELECT SUM(subTotal) FROM produtos_os WHERE os_id = o.idOs), 0) as total_produtos,
                   COALESCE((SELECT SUM(subTotal) FROM servicos_os WHERE os_id = o.idOs), 0) as total_servicos
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

    # ==================== RELATORIOS AVANCADOS ====================

    def relatorio_financeiro(self, dt_inicio: str = None, dt_fim: str = None):
        """Resumo financeiro: receitas, despesas, a receber, recebido"""
        if not dt_inicio:
            dt_inicio = date.today().replace(day=1).isoformat()
        if not dt_fim:
            dt_fim = date.today().isoformat()

        # Lancamentos do periodo
        sql_lanc = """
            SELECT tipo, baixado, SUM(valor) as total_valor, COUNT(*) as qtd
            FROM lancamentos
            WHERE data_vencimento BETWEEN :dt_inicio AND :dt_fim
            GROUP BY tipo, baixado
        """
        lancamentos = execute_query(sql_lanc, {'dt_inicio': dt_inicio, 'dt_fim': dt_fim})

        # Total OS faturadas no periodo
        sql_os = """
            SELECT status, COUNT(*) as qtd, COALESCE(SUM(valorTotal), 0) as total
            FROM os
            WHERE dataInicial BETWEEN :dt_inicio AND :dt_fim
            GROUP BY status
        """
        os_stats = execute_query(sql_os, {'dt_inicio': dt_inicio, 'dt_fim': dt_fim})

        # Top clientes por valor
        sql_top = """
            SELECT c.nomeCliente, SUM(l.valor) as total, COUNT(*) as qtd
            FROM lancamentos l
            JOIN clientes c ON c.idClientes = l.clientes_id
            WHERE l.data_vencimento BETWEEN :dt_inicio AND :dt_fim
            GROUP BY c.idClientes
            ORDER BY total DESC
            LIMIT 5
        """
        top_clientes = execute_query(sql_top, {'dt_inicio': dt_inicio, 'dt_fim': dt_fim})

        # Calcular totais
        total_receita = sum(r['total_valor'] for r in lancamentos if r['tipo'] == 'receita')
        total_despesa = sum(r['total_valor'] for r in lancamentos if r['tipo'] == 'despesa')
        a_receber = sum(r['total_valor'] for r in lancamentos if r.get('baixado') == 0 and r['tipo'] == 'receita')
        recebido = sum(r['total_valor'] for r in lancamentos if r.get('baixado') == 1 and r['tipo'] == 'receita')

        return {
            'tipo': 'relatorio_financeiro',
            'periodo': {'inicio': dt_inicio, 'fim': dt_fim},
            'resumo': {
                'total_receita': float(total_receita),
                'total_despesa': float(total_despesa),
                'lucro': float(total_receita - total_despesa),
                'a_receber': float(a_receber),
                'recebido': float(recebido),
                'total_os_periodo': sum(r['qtd'] for r in os_stats),
                'os_por_status': {r['status']: r['qtd'] for r in os_stats},
            },
            'top_clientes': top_clientes,
            'os_stats': os_stats,
            'lancamentos': lancamentos,
        }

    def relatorio_vendas(self, dt_inicio: str = None, dt_fim: str = None):
        """Relatorio de vendas por periodo"""
        if not dt_inicio:
            dt_inicio = date.today().replace(day=1).isoformat()
        if not dt_fim:
            dt_fim = date.today().isoformat()

        sql = """
            SELECT v.idVendas, v.dataVenda, v.valorTotal, v.faturado,
                   c.nomeCliente, c.celular
            FROM vendas v
            JOIN clientes c ON c.idClientes = v.clientes_id
            WHERE DATE(v.dataVenda) BETWEEN :dt_inicio AND :dt_fim
            ORDER BY v.dataVenda DESC
        """
        vendas = execute_query(sql, {'dt_inicio': dt_inicio, 'dt_fim': dt_fim})

        total = sum(float(v.get('valorTotal', 0) or 0) for v in vendas)
        faturadas = [v for v in vendas if v.get('faturado') == 1 or v.get('faturado') == '1']
        pendentes = [v for v in vendas if v.get('faturado') == 0 or v.get('faturado') == '0']

        return {
            'tipo': 'relatorio_vendas',
            'periodo': {'inicio': dt_inicio, 'fim': dt_fim},
            'resumo': {
                'total_vendas': len(vendas),
                'valor_total': total,
                'ticket_medio': round(total / len(vendas), 2) if vendas else 0,
                'faturadas': len(faturadas),
                'pendentes': len(pendentes),
            },
            'vendas': vendas[:20],
        }

    def relatorio_estoque(self):
        """Relatorio de estoque atual e alertas"""
        sql = """
            SELECT idProdutos, descricao, estoque, estoqueMinimo,
                   precoVenda, unidade, ativo
            FROM produtos
            WHERE ativo = 1
            ORDER BY CASE WHEN estoque < estoqueMinimo THEN 0 ELSE 1 END, descricao
        """
        produtos = execute_query(sql)

        baixo_minimo = [p for p in produtos if float(p.get('estoque', 0) or 0) < float(p.get('estoqueMinimo', 0) or 0)]
        valor_estoque = sum(float(p.get('estoque', 0) or 0) * float(p.get('precoVenda', 0) or 0) for p in produtos)

        return {
            'tipo': 'relatorio_estoque',
            'resumo': {
                'total_produtos': len(produtos),
                'baixo_minimo': len(baixo_minimo),
                'valor_estoque': round(valor_estoque, 2),
            },
            'alertas': baixo_minimo[:15],
            'produtos': produtos[:30],
        }

    def relatorio_produtividade(self, dt_inicio: str = None, dt_fim: str = None):
        """Produtividade dos tecnicos no periodo"""
        if not dt_inicio:
            dt_inicio = date.today().replace(day=1).isoformat()
        if not dt_fim:
            dt_fim = date.today().isoformat()

        sql = """
            SELECT u.idUsuarios, u.nome,
                   COUNT(o.idOs) as total_os,
                   SUM(CASE WHEN o.status IN ('Finalizado', 'Faturado') THEN 1 ELSE 0 END) as finalizadas,
                   SUM(CASE WHEN o.status = 'Aberto' THEN 1 ELSE 0 END) as abertas,
                   COALESCE(SUM(o.valorTotal), 0) as valor_total
            FROM usuarios u
            LEFT JOIN os o ON o.usuarios_id = u.idUsuarios
                AND DATE(o.dataInicial) BETWEEN :dt_inicio AND :dt_fim
            WHERE u.permissoes_id IN (SELECT idPermissao FROM permissoes WHERE nome LIKE '%tecnico%' OR nome LIKE '%Tecnico%')
               OR u.situacao = 1
            GROUP BY u.idUsuarios, u.nome
            ORDER BY total_os DESC
        """
        tecnicos = execute_query(sql, {'dt_inicio': dt_inicio, 'dt_fim': dt_fim})

        return {
            'tipo': 'relatorio_produtividade',
            'periodo': {'inicio': dt_inicio, 'fim': dt_fim},
            'resumo': {
                'total_tecnicos': len(tecnicos),
                'total_os': sum(int(t.get('total_os', 0) or 0) for t in tecnicos),
                'total_valor': float(sum(float(t.get('valor_total', 0) or 0) for t in tecnicos)),
            },
            'tecnicos': tecnicos,
        }

    def relatorio_clientes_top(self, limite: int = 10):
        """Top clientes por quantidade de OS e valor"""
        sql = """
            SELECT c.idClientes, c.nomeCliente, c.celular,
                   COUNT(o.idOs) as total_os,
                   SUM(CASE WHEN o.status IN ('Finalizado','Faturado') THEN 1 ELSE 0 END) as finalizadas,
                   COALESCE(SUM(o.valorTotal), 0) as valor_total,
                   MAX(o.dataInicial) as ultima_os
            FROM clientes c
            LEFT JOIN os o ON o.clientes_id = c.idClientes
            GROUP BY c.idClientes
            HAVING total_os > 0
            ORDER BY total_os DESC
            LIMIT :limite
        """
        clientes = execute_query(sql, {'limite': limite})

        return {
            'tipo': 'relatorio_clientes_top',
            'resumo': {
                'total_clientes': len(clientes),
                'total_os': sum(int(c.get('total_os', 0) or 0) for c in clientes),
            },
            'clientes': clientes,
        }

    def relatorio_os_periodo(self, dt_inicio: str = None, dt_fim: str = None, status: str = None):
        """Relatorio detalhado de OS por periodo"""
        if not dt_inicio:
            dt_inicio = date.today().replace(day=1).isoformat()
        if not dt_fim:
            dt_fim = date.today().isoformat()

        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.descricaoProduto,
                   o.defeito, o.status, o.valorTotal,
                   c.nomeCliente, u.nome as tecnico_nome
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            LEFT JOIN usuarios u ON u.idUsuarios = o.usuarios_id
            WHERE DATE(o.dataInicial) BETWEEN :dt_inicio AND :dt_fim
        """
        params = {'dt_inicio': dt_inicio, 'dt_fim': dt_fim}
        if status:
            sql += " AND o.status = :status"
            params['status'] = status
        sql += " ORDER BY o.dataInicial DESC LIMIT 50"

        oss = execute_query(sql, params)

        por_status = {}
        total_valor = 0
        for o in oss:
            s = o.get('status', 'Desconhecido')
            por_status[s] = por_status.get(s, 0) + 1
            total_valor += float(o.get('valorTotal', 0) or 0)

        return {
            'tipo': 'relatorio_os_periodo',
            'periodo': {'inicio': dt_inicio, 'fim': dt_fim},
            'resumo': {
                'total_os': len(oss),
                'total_valor': round(total_valor, 2),
                'por_status': por_status,
                'media_valor': round(total_valor / len(oss), 2) if oss else 0,
            },
            'os': oss,
        }

    def relatorio_atrasados(self):
        """Clientes com OS atrasadas agrupados"""
        sql = """
            SELECT o.status, COUNT(*) as quantidade,
                   COALESCE(SUM(o.valorTotal), 0) as valor_total
            FROM os o
            WHERE o.status NOT IN ('Finalizado', 'Cancelado', 'Faturado')
              AND o.dataFinal < CURDATE()
            GROUP BY o.status
        """
        por_status = execute_query(sql)

        sql2 = """
            SELECT c.nomeCliente, COUNT(o.idOs) as qtd_atrasadas,
                   MIN(o.dataFinal) as mais_antiga
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            WHERE o.status NOT IN ('Finalizado', 'Cancelado', 'Faturado')
              AND o.dataFinal < CURDATE()
            GROUP BY c.idClientes
            ORDER BY qtd_atrasadas DESC
            LIMIT 10
        """
        top_atrasados = execute_query(sql2)

        total = sum(int(r.get('quantidade', 0) or 0) for r in por_status)

        return {
            'tipo': 'relatorio_atrasados',
            'resumo': {
                'total_os_atrasadas': total,
                'por_status': {r['status']: int(r.get('quantidade', 0) or 0) for r in por_status},
            },
            'top_atrasados': top_atrasados,
        }

    # ==================== CRIAR OS ====================

    # ==================== OS FINALIZADAS POR MES (COBRANCA) ====================

    def os_finalizadas_mes(self, dt_inicio: str = None, dt_fim: str = None):
        """OS finalizadas/faturadas por mes, agrupadas por cliente para cobranca."""
        if not dt_inicio:
            dt_inicio = date.today().replace(day=1).isoformat()
        if not dt_fim:
            dt_fim = date.today().isoformat()

        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.descricaoProduto,
                   o.defeito, o.status, o.valorTotal,
                   c.idClientes, c.nomeCliente, c.celular, c.email
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            WHERE o.status IN ('Finalizado', 'Faturado')
              AND DATE(o.dataFinal) BETWEEN :dt_inicio AND :dt_fim
            ORDER BY c.nomeCliente, o.dataFinal
        """
        oss = execute_query(sql, {'dt_inicio': dt_inicio, 'dt_fim': dt_fim})

        por_cliente = {}
        total_valor = 0
        for o in oss:
            cid = o['idClientes']
            if cid not in por_cliente:
                por_cliente[cid] = {
                    'idClientes': cid,
                    'nomeCliente': o['nomeCliente'],
                    'celular': o.get('celular', ''),
                    'email': o.get('email', ''),
                    'qtd_os': 0,
                    'total_valor': 0,
                }
            por_cliente[cid]['qtd_os'] += 1
            valor = float(o.get('valorTotal', 0) or 0)
            por_cliente[cid]['total_valor'] = round(por_cliente[cid]['total_valor'] + valor, 2)
            total_valor += valor

        # Ordenar por valor (maiores primeiro)
        clientes_lista = sorted(por_cliente.values(), key=lambda x: x['total_valor'], reverse=True)

        return {
            'tipo': 'os_finalizadas_mes',
            'periodo': {'inicio': dt_inicio, 'fim': dt_fim},
            'resumo': {
                'total_os': len(oss),
                'total_valor': round(total_valor, 2),
                'total_clientes': len(por_cliente),
            },
            'por_cliente': clientes_lista,
            'os': oss,
        }

    def criar_os(self, cliente_id: int, descricao: str = '', defeito: str = '',
                 usuario_id: int = 1, observacoes: str = '', status: str = 'Aberto',
                 valor_total: float = None, itens: list = None):
        """Cria uma OS diretamente no banco, com suporte a produtos/servicos e valor."""
        valor_sql = ':valor_total' if valor_total is not None else '0.00'
        params = {
            'cliente_id': cliente_id,
            'usuario_id': usuario_id,
            'descricao': descricao or 'Nao especificado',
            'defeito': defeito or '',
            'status': status,
            'observacoes': observacoes or '',
        }
        if valor_total is not None:
            params['valor_total'] = valor_total

        sql = f"""
            INSERT INTO os (dataInicial, dataFinal, clientes_id, usuarios_id,
                          descricaoProduto, defeito, status, observacoes, faturado, valorTotal)
            VALUES (CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), :cliente_id, :usuario_id,
                    :descricao, :defeito, :status, :observacoes, 0, {valor_sql})
        """
        os_id = execute_insert(sql, params)

        # Inserir produtos/servicos na OS
        if itens and os_id:
            for item in itens:
                tipo = item.get('tipo', 'produto')
                item_id = item.get('id')
                quantidade = item.get('quantidade', 1)
                preco = item.get('preco', 0)
                descricao_item = item.get('descricao', '')
                sub_total = round(quantidade * preco, 2)

                if tipo == 'produto':
                    execute_insert(
                        """INSERT INTO produtos_os (quantidade, descricao, preco, os_id, produtos_id, subTotal)
                           VALUES (:quantidade, :descricao, :preco, :os_id, :produtos_id, :sub_total)""",
                        {'quantidade': quantidade, 'descricao': descricao_item,
                         'preco': preco, 'os_id': os_id, 'produtos_id': item_id, 'sub_total': sub_total}
                    )
                elif tipo == 'servico':
                    execute_insert(
                        """INSERT INTO servicos_os (servico, quantidade, preco, os_id, servicos_id, subTotal)
                           VALUES (:servico, :quantidade, :preco, :os_id, :servicos_id, :sub_total)""",
                        {'servico': descricao_item, 'quantidade': quantidade,
                         'preco': preco, 'os_id': os_id, 'servicos_id': item_id, 'sub_total': sub_total}
                    )

        return os_id

    # ==================== PRODUTOS E SERVICOS ====================

    def buscar_produtos(self, termo: str = '', limite: int = 10):
        """Busca produtos por nome. Retorna id, descricao, precoVenda, unidade."""
        if not termo:
            sql = "SELECT idProdutos, descricao, precoVenda, unidade FROM produtos ORDER BY descricao LIMIT :limite"
            return execute_query(sql, {'limite': limite})
        sql = """SELECT idProdutos, descricao, precoVenda, unidade FROM produtos
                 WHERE descricao LIKE :termo ORDER BY descricao LIMIT :limite"""
        return execute_query(sql, {'termo': f'%{termo}%', 'limite': limite})

    def buscar_servicos(self, termo: str = '', limite: int = 10):
        """Busca servicos por nome. Retorna id, nome, descricao, preco."""
        if not termo:
            sql = "SELECT idServicos, nome, descricao, preco FROM servicos ORDER BY nome LIMIT :limite"
            return execute_query(sql, {'limite': limite})
        sql = """SELECT idServicos, nome, descricao, preco FROM servicos
                 WHERE nome LIKE :termo OR descricao LIKE :termo2 ORDER BY nome LIMIT :limite"""
        return execute_query(sql, {'termo': f'%{termo}%', 'termo2': f'%{termo}%', 'limite': limite})

    def buscar_produto_por_id(self, produto_id: int):
        """Retorna dados de um produto pelo ID."""
        rows = execute_query(
            "SELECT idProdutos, descricao, precoVenda, unidade FROM produtos WHERE idProdutos = :id LIMIT 1",
            {'id': produto_id}
        )
        return rows[0] if rows else None

    def buscar_servico_por_id(self, servico_id: int):
        """Retorna dados de um servico pelo ID."""
        rows = execute_query(
            "SELECT idServicos, nome, descricao, preco FROM servicos WHERE idServicos = :id LIMIT 1",
            {'id': servico_id}
        )
        return rows[0] if rows else None

    # ==================== ALTERAR STATUS OS ====================

    STATUS_VALIDOS = [
        'Aberto', 'Orçamento', 'Aprovado', 'Em Andamento',
        'Aguardando Peças', 'Faturado', 'Finalizado', 'Cancelado'
    ]

    def alterar_status_os(self, os_id: int, novo_status: str, laudo: str = None) -> dict:
        """Altera o status de uma OS e retorna os dados atualizados"""
        # Verificar se o status e valido
        status_cap = novo_status.strip().title()
        # Mapear variacoes comuns
        status_map = {
            'Aberto': 'Aberto',
            'Aberta': 'Aberto',
            'Andamento': 'Em Andamento',
            'Em Andamento': 'Em Andamento',
            'Em andamento': 'Em Andamento',
            'Orcamento': 'Orçamento',
            'Orçamento': 'Orçamento',
            'Aprovado': 'Aprovado',
            'Aprovada': 'Aprovado',
            'Faturado': 'Faturado',
            'Faturada': 'Faturado',
            'Finalizado': 'Finalizado',
            'Finalizada': 'Finalizado',
            'Concluido': 'Finalizado',
            'Concluida': 'Finalizado',
            'Cancelado': 'Cancelado',
            'Cancelada': 'Cancelado',
            'Aguardando': 'Aguardando Peças',
            'Aguardando pecas': 'Aguardando Peças',
            'Aguardando Peças': 'Aguardando Peças',
        }
        status_correto = status_map.get(status_cap, status_cap)

        if status_correto not in self.STATUS_VALIDOS:
            return {
                'sucesso': False,
                'erro': f'Status "{novo_status}" nao e valido.',
                'status_validos': self.STATUS_VALIDOS
            }

        # Buscar OS atual
        os_atual = self.buscar_os(os_id)
        if not os_atual:
            return {
                'sucesso': False,
                'erro': f'OS #{os_id} nao encontrada.'
            }

        status_anterior = os_atual.get('status', '')

        # Atualizar no banco
        sql = "UPDATE os SET status = :status WHERE idOs = :os_id"
        params = {'status': status_correto, 'os_id': os_id}

        # Se tem laudo, atualizar junto
        if laudo:
            sql = "UPDATE os SET status = :status, laudoTecnico = :laudo WHERE idOs = :os_id"
            params['laudo'] = laudo

        # Se finalizado, marcar dataFinal
        if status_correto == 'Finalizado':
            sql = sql.replace('WHERE idOs = :os_id',
                              ', dataFinal = CURDATE() WHERE idOs = :os_id')

        rows = execute_update(sql, params)

        if rows > 0:
            os_nova = self.buscar_os(os_id)
            return {
                'sucesso': True,
                'os_id': os_id,
                'status_anterior': status_anterior,
                'novo_status': status_correto,
                'os': os_nova
            }
        else:
            return {
                'sucesso': False,
                'erro': 'Nao foi possivel atualizar a OS.'
            }

    def buscar_os_por_tecnico_status(self, usuario_id: int, status: str = None, limite: int = 20):
        """Busca OS do tecnico, opcionalmente filtrando por status"""
        sql = """
            SELECT o.idOs, o.dataInicial, o.dataFinal, o.descricaoProduto,
                   o.defeito, o.status, c.nomeCliente
            FROM os o
            JOIN clientes c ON c.idClientes = o.clientes_id
            WHERE o.usuarios_id = :usuario_id
        """
        params = {'usuario_id': usuario_id}
        if status:
            sql += " AND o.status = :status"
            params['status'] = status
        sql += " ORDER BY o.idOs DESC LIMIT :limite"
        params['limite'] = limite
        return execute_query(sql, params)