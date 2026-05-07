<?php
/**
 * CobrancasController - API v2
 * Endpoints para consulta de cobrancas/lancamentos (usado pelo n8n / Agente IA)
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class CobrancasController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET /api/v2/cobrancas/pendentes
     *
     * Query:
     *   dias_ate_vencimento (int) default 3
     *   limit               (int) default 100
     *   page                (int) default 1
     *
     * Retorna lancamentos a receber (baixado=0) proximos do vencimento.
     */
    public function pendentes_get()
    {
        $diasAteVencimento = (int) ($this->input->get('dias_ate_vencimento') ?: 3);
        $limit  = (int) ($this->input->get('limit') ?: 100);
        $page   = (int) ($this->input->get('page') ?: 1);
        $offset = ($page - 1) * $limit;

        $dataLimite = date('Y-m-d', strtotime("+{$diasAteVencimento} days"));

        // Busca lancamentos do tipo receita nao baixados com vencimento proximo
        $this->db->from('lancamentos l');
        $this->db->select([
            'l.idLancamentos',
            'l.descricao',
            'l.valor',
            'l.data_vencimento',
            'l.baixado',
            'l.clientes_id',
            'c.nomeCliente',
            'c.telefone',
        ]);
        $this->db->join('clientes c', 'c.idClientes = l.clientes_id', 'left');
        $this->db->where('l.tipo', 'receita');
        $this->db->where('l.baixado', 0);
        $this->db->where('l.data_vencimento <=', $dataLimite);
        $this->db->where('l.data_vencimento >=', date('Y-m-d'));
        $this->db->order_by('l.data_vencimento', 'ASC');

        $total = $this->db->count_all_results('', false);

        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $items = $query ? $query->result_array() : [];

        // Formata para uso nos workflows
        $resultado = [];
        foreach ($items as $item) {
            $resultado[] = [
                'idLancamentos'   => (int) $item['idLancamentos'],
                'descricao'       => $item['descricao'],
                'valor'           => number_format((float)$item['valor'], 2, ',', '.'),
                'data_vencimento' => date('d/m/Y', strtotime($item['data_vencimento'])),
                'baixado'         => (int) $item['baixado'],
                'clientes_id'     => (int) $item['clientes_id'],
                'cliente_nome'    => $item['nomeCliente'] ?: 'Cliente',
                'telefone'        => $item['telefone'] ?: '',
            ];
        }

        return $this->success([
            'items'       => $resultado,
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'data_limite' => $dataLimite,
        ]);
    }

    /**
     * GET /api/v2/cobrancas
     *
     * Lista todas as cobrancas com filtros.
     */
    public function index_get()
    {
        $clienteId = (int) ($this->input->get('cliente_id') ?: 0);
        $status    = $this->input->get('status'); // pago|pendente
        $limit     = (int) ($this->input->get('limit') ?: 50);
        $page      = (int) ($this->input->get('page') ?: 1);
        $offset    = ($page - 1) * $limit;

        $this->db->from('lancamentos l');
        $this->db->select([
            'l.idLancamentos',
            'l.descricao',
            'l.valor',
            'l.data_vencimento',
            'l.data_pagamento',
            'l.baixado',
            'l.tipo',
            'l.clientes_id',
            'c.nomeCliente',
            'c.telefone',
        ]);
        $this->db->join('clientes c', 'c.idClientes = l.clientes_id', 'left');

        if ($clienteId) {
            $this->db->where('l.clientes_id', $clienteId);
        }
        if ($status === 'pago') {
            $this->db->where('l.baixado', 1);
        } elseif ($status === 'pendente') {
            $this->db->where('l.baixado', 0);
        }

        $this->db->order_by('l.data_vencimento', 'DESC');
        $total = $this->db->count_all_results('', false);

        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $items = $query ? $query->result_array() : [];

        return $this->success([
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
        ]);
    }
}
