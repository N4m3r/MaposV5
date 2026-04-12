<?php
/**
 * Venda Repository
 * Repositório para operações de Vendas
 */

namespace Repositories;

use CI_Model;

class VendaRepository extends AbstractRepository
{
    protected string $tableName = 'vendas';
    protected string $primaryKey = 'idVendas';

    public function __construct()
    {
        $ci = \u0026get_instance();
        $ci->load->model('vendas_model');
        parent::__construct($ci->vendas_model);
    }

    /**
     * Busca vendas por cliente
     */
    public function findByCliente(int $clienteId): array
    {
        $this->model->db->where('idClientes', $clienteId);
        return $this->model->getAll();
    }

    /**
     * Busca vendas por período
     */
    public function findByPeriod(string $inicio, string $fim): array
    {
        $this->model->db->where('dataVenda >=', $inicio);
        $this->model->db->where('dataVenda <=', $fim);
        return $this->model->getAll();
    }

    /**
     * Calcula total de vendas por período
     */
    public function getTotalVendas(string $inicio, string $fim): float
    {
        $this->model->db->select_sum('total', 'total_vendas');
        $this->model->db->where('dataVenda >=', $inicio);
        $this->model->db->where('dataVenda <=', $fim);
        $result = $this->model->db->get('vendas')->row();
        return (float) ($result->total_vendas ?? 0);
    }
}
