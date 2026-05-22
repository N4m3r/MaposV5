<?php
/**
 * Agente_ia_relatorios_model
 * Model para buscar dados brutos de relatorios do agente IA.
 * Nao gera PDF — so retorna dados estruturados para o controller montar.
 */

class Agente_ia_relatorios_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ========================================================================
    // RELATORIO: ORDENS DE SERVICO (Diario / Mes / Periodo)
    // ========================================================================

    public function osPeriodo(string $dtInicio, string $dtFim, ?int $tecnicoId = null, ?int $clienteId = null, ?string $status = null): array
    {
        $this->db->from('os o');
        $this->db->select([
            'o.idOs',
            'o.dataInicial',
            'o.dataFinal',
            'o.garantia',
            'o.descricaoProduto',
            'o.defeito',
            'o.status',
            'o.valorTotal',
            'o.observacoes',
            'c.nomeCliente',
            'c.telefone',
            'u.nome',
            'u.idUsuarios AS tecnico_id'
        ]);
        $this->db->join('clientes c', 'c.idClientes = o.clientes_id', 'left');
        $this->db->join('usuarios u', 'u.idUsuarios = o.usuarios_id', 'left');
        $this->db->where('DATE(o.dataInicial) >=', $dtInicio);
        $this->db->where('DATE(o.dataInicial) <=', $dtFim);

        if ($tecnicoId) {
            $this->db->where('o.usuarios_id', $tecnicoId);
        }
        if ($clienteId) {
            $this->db->where('o.clientes_id', $clienteId);
        }
        if ($status) {
            $this->db->where('o.status', $status);
        }

        $this->db->order_by('o.dataInicial', 'DESC');
        $items = $this->db->get()->result_array();

        // Resumo estatistico
        $totalOS = count($items);
        $totalValor = array_sum(array_column($items, 'valorTotal'));
        $porStatus = array_count_values(array_column($items, 'status'));

        return [
            'tipo'        => 'os_periodo',
            'periodo'     => ['inicio' => $dtInicio, 'fim' => $dtFim],
            'items'       => $items,
            'resumo'      => [
                'total_os'    => $totalOS,
                'total_valor' => $totalValor,
                'por_status'  => $porStatus,
                'media_valor' => $totalOS > 0 ? round($totalValor / $totalOS, 2) : 0,
            ]
        ];
    }

    // ========================================================================
    // RELATORIO: HISTORICO DO CLIENTE
    // ========================================================================

    public function historicoCliente(int $clienteId, int $limite = 50): array
    {
        $this->db->from('os o');
        $this->db->select([
            'o.idOs',
            'o.dataInicial',
            'o.status',
            'o.valorTotal',
            'o.descricaoProduto',
            'o.defeito'
        ]);
        $this->db->where('o.clientes_id', $clienteId);
        $this->db->order_by('o.dataInicial', 'DESC');
        $this->db->limit($limite);
        $items = $this->db->get()->result_array();

        // Cobrancas/lancamentos
        $this->db->from('lancamentos');
        $this->db->where('clientes_id', $clienteId);
        $this->db->order_by('data_vencimento', 'DESC');
        $lancamentos = $this->db->get()->result_array();

        $totalDivida = array_sum(array_column($lancamentos, 'valor'));

        $cliente = $this->db
            ->where('idClientes', $clienteId)
            ->get('clientes')
            ->row_array();

        return [
            'tipo'         => 'historico_cliente',
            'cliente'      => $cliente,
            'os'           => $items,
            'lancamentos'  => $lancamentos,
            'resumo'       => [
                'total_os'      => count($items),
                'total_divida'    => $totalDivida,
                'os_finalizadas'  => count(array_filter($items, fn($i) => in_array($i['status'], ['Finalizado', 'Faturado']))),
            ]
        ];
    }

    // ========================================================================
    // RELATORIO: RESUMO FINANCEIRO
    // ========================================================================

    public function resumoFinanceiro(string $dtInicio, string $dtFim): array
    {
        // Lancamentos a receber/recebidos
        $this->db->from('lancamentos');
        $this->db->where('data_vencimento >=', $dtInicio);
        $this->db->where('data_vencimento <=', $dtFim);
        $lancamentos = $this->db->get()->result_array();

        $aReceber = array_filter($lancamentos, fn($l) => ($l['baixado'] ?? 0) == 0);
        $recebido = array_filter($lancamentos, fn($l) => ($l['baixado'] ?? 0) == 1);

        $porTipo = array_reduce($lancamentos, function($acc, $item) {
            $tipo = $item['tipo'] ?? 'Outro';
            $acc[$tipo] = ($acc[$tipo] ?? 0) + (float)($item['valor'] ?? 0);
            return $acc;
        }, []);

        return [
            'tipo'         => 'resumo_financeiro',
            'periodo'      => ['inicio' => $dtInicio, 'fim' => $dtFim],
            'resumo'       => [
                'total_lancamentos' => count($lancamentos),
                'total_valor'       => array_sum(array_column($lancamentos, 'valor')),
                'total_a_receber'   => array_sum(array_column($aReceber, 'valor')),
                'total_recebido'    => array_sum(array_column($recebido, 'valor')),
                'por_tipo'          => $porTipo,
            ],
            'items'        => $lancamentos
        ];
    }

    // ========================================================================
    // RELATORIO: VENDAS
    // ========================================================================

    public function resumoVendas(string $dtInicio, string $dtFim): array
    {
        $this->db->from('vendas v');
        $this->db->select([
            'v.idVendas',
            'v.dataVenda',
            'v.valortotal',
            'v.obscliente',
            'c.nomeCliente'
        ]);
        $this->db->join('clientes c', 'c.idClientes = v.clientes_id', 'left');
        $this->db->where('DATE(v.dataVenda) >=', $dtInicio);
        $this->db->where('DATE(v.dataVenda) <=', $dtFim);
        $this->db->order_by('v.dataVenda', 'DESC');
        $items = $this->db->get()->result_array();

        $total      = array_sum(array_column($items, 'valortotal'));
        $ticketMedio = count($items) > 0 ? round($total / count($items), 2) : 0;

        return [
            'tipo'        => 'vendas',
            'periodo'     => ['inicio' => $dtInicio, 'fim' => $dtFim],
            'items'       => $items,
            'resumo'      => [
                'total_vendas'  => count($items),
                'total_valor'   => $total,
                'ticket_medio'  => $ticketMedio,
            ]
        ];
    }

    // ========================================================================
    // RELATORIO: ESTOQUE
    // ========================================================================

    public function estoqueAtual(): array
    {
        $this->db->from('produtos');
        $this->db->select([
            'idProdutos',
            'descricao',
            'estoque',
            'estoqueMinimo',
            'precoVenda',
            'ativo',
            'unidade'
        ]);
        $this->db->where('ativo', 1);
        $this->db->order_by('estoque', 'ASC');
        $items = $this->db->get()->result_array();

        $baixoMinimo = array_filter($items, fn($p) => (float)($p['estoque'] ?? 0) < (float)($p['estoqueMinimo'] ?? 0));

        return [
            'tipo'        => 'estoque',
            'items'       => $items,
            'alertas'     => array_values($baixoMinimo),
            'resumo'      => [
                'total_produtos' => count($items),
                'baixo_minimo'   => count($baixoMinimo),
                'valor_estoque'  => array_sum(array_map(fn($p) => (float)($p['estoque'] ?? 0) * (float)($p['precoVenda'] ?? 0), $items)),
            ]
        ];
    }

    // ========================================================================
    // RELATORIO: PRODUTIVIDADE TECNICO
    // ========================================================================

    public function produtividadeTecnico(int $tecnicoId, string $dtInicio, string $dtFim): array
    {
        $this->db->from('os');
        $this->db->where('usuarios_id', $tecnicoId);
        $this->db->where('DATE(dataInicial) >=', $dtInicio);
        $this->db->where('DATE(dataInicial) <=', $dtFim);
        $items = $this->db->get()->result_array();

        $tecnico = $this->db
            ->where('idUsuarios', $tecnicoId)
            ->get('usuarios')
            ->row_array();

        $finalizadas = array_filter($items, fn($i) => in_array($i['status'], ['Finalizado', 'Faturado']));
        $abertas     = array_filter($items, fn($i) => $i['status'] === 'Aberto');

        return [
            'tipo'        => 'produtividade_tecnico',
            'tecnico'     => $tecnico,
            'periodo'     => ['inicio' => $dtInicio, 'fim' => $dtFim],
            'items'       => $items,
            'resumo'      => [
                'total_os'      => count($items),
                'finalizadas'   => count($finalizadas),
                'abertas'       => count($abertas),
                'total_valor'   => array_sum(array_column($items, 'valorTotal')),
                'media_por_os'  => count($items) > 0 ? round(array_sum(array_column($items, 'valorTotal')) / count($items), 2) : 0,
            ]
        ];
    }

    // ========================================================================
    // UTIL: DADOS DE HOJE / MES ATUAL PARA TEXTO
    // ========================================================================

    public function osHoje(): array
    {
        return $this->osPeriodo(date('Y-m-d'), date('Y-m-d'));
    }

    public function osMes(): array
    {
        $inicio = date('Y-m-01');
        $fim    = date('Y-m-t');
        return $this->osPeriodo($inicio, $fim);
    }

    // ========================================================================
    // RELATORIO: COBRANCAS VENCIDAS
    // ========================================================================

    public function cobrancasVencidas(): array
    {
        $this->db->from('cobrancas cb');
        $this->db->select([
            'cb.idCobranca', 'cb.descricao', 'cb.valor',
            'cb.data_vencimento', 'cb.baixado', 'cb.data_pagamento',
            'c.nomeCliente', 'c.celular'
        ]);
        $this->db->join('clientes c', 'c.idClientes = cb.clientes_id', 'left');
        $this->db->where('cb.baixado', 0);
        $this->db->where('cb.data_vencimento <', date('Y-m-d'));
        $this->db->order_by('cb.data_vencimento', 'ASC');
        $this->db->limit(50);
        $items = $this->db->get()->result_array();

        $totalValor = array_sum(array_column($items, 'valor'));

        return [
            'tipo'         => 'cobrancas_vencidas',
            'resumo'       => [
                'total_cobrancas' => count($items),
                'valor_total'     => $totalValor,
            ],
            'items'        => $items
        ];
    }

    // ========================================================================
    // RELATORIO: OS ATRASADOS (AGRUPADO)
    // ========================================================================

    public function osAtrasados(): array
    {
        // Por status
        $this->db->from('os o');
        $this->db->select([
            'o.status',
            'COUNT(*) as quantidade',
            'COALESCE(SUM(o.valorTotal), 0) as valor_total'
        ]);
        $this->db->where('o.status NOT IN ("Finalizado", "Cancelado", "Faturado")', null, false);
        $this->db->where('o.dataFinal <', date('Y-m-d'));
        $this->db->group_by('o.status');
        $porStatus = $this->db->get()->result_array();

        // Top clientes com mais atrasos
        $this->db->from('os o');
        $this->db->select([
            'c.nomeCliente',
            'COUNT(o.idOs) as qtd_atrasadas',
            'MIN(o.dataFinal) as mais_antiga'
        ]);
        $this->db->join('clientes c', 'c.idClientes = o.clientes_id');
        $this->db->where('o.status NOT IN ("Finalizado", "Cancelado", "Faturado")', null, false);
        $this->db->where('o.dataFinal <', date('Y-m-d'));
        $this->db->group_by('c.idClientes');
        $this->db->order_by('qtd_atrasadas', 'DESC');
        $this->db->limit(10);
        $topAtrasados = $this->db->get()->result_array();

        $total = array_sum(array_column($porStatus, 'quantidade'));

        return [
            'tipo'          => 'os_atrasados',
            'resumo'        => [
                'total_os_atrasadas' => (int)$total,
                'por_status'         => array_column($porStatus, 'quantidade', 'status'),
            ],
            'top_atrasados' => $topAtrasados,
            'items'         => $porStatus
        ];
    }

    // ========================================================================
    // RELATORIO: TOP CLIENTES
    // ========================================================================

    public function clientesTop(int $limite = 15): array
    {
        $this->db->from('clientes c');
        $this->db->select([
            'c.idClientes', 'c.nomeCliente', 'c.celular',
            'COUNT(o.idOs) as total_os',
            'SUM(CASE WHEN o.status IN ("Finalizado","Faturado") THEN 1 ELSE 0 END) as finalizadas',
            'COALESCE(SUM(o.valorTotal), 0) as valor_total',
            'MAX(o.dataInicial) as ultima_os'
        ]);
        $this->db->join('os o', 'o.clientes_id = c.idClientes', 'left');
        $this->db->group_by('c.idClientes');
        $this->db->having('total_os >', 0);
        $this->db->order_by('total_os', 'DESC');
        $this->db->limit($limite);
        $items = $this->db->get()->result_array();

        return [
            'tipo'    => 'clientes_top',
            'resumo'  => [
                'total_clientes' => count($items),
                'total_os'       => array_sum(array_column($items, 'total_os')),
            ],
            'items'   => $items
        ];
    }
}
