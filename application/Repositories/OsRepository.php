<?php
/**
 * OS Repository
 * Repositório para operações de Ordens de Serviço
 */

namespace Repositories;

use CI_Model;

class OsRepository extends AbstractRepository
{
    protected string $tableName = 'os';
    protected string $primaryKey = 'idOs';

    public function __construct()
    {
        $ci = &get_instance();
        $ci->load->model('os_model');
        parent::__construct($ci->os_model);
    }

    /**
     * Cria nova OS
     */
    public function create(array $data): int
    {
        $this->model->add('os', $data, true);
        return $this->model->db->insert_id();
    }

    /**
     * Atualiza OS
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->edit('os', $data, 'idOs', $id);
    }

    /**
     * Remove OS
     */
    public function delete(int $id): bool
    {
        return $this->model->delete('os', 'idOs', $id);
    }

    /**
     * Busca OS por cliente
     */
    public function findByCliente(int $clienteId, array $status = []): array
    {
        $this->model->db->where('idClientes', $clienteId);
        if (!empty($status)) {
            $this->model->db->where_in('status', $status);
        }
        return $this->model->getAll();
    }

    /**
     * Busca OS prestes a vencer
     */
    public function findOsVencendo(int $dias = 2): array
    {
        $dataLimite = date('Y-m-d', strtotime("+{$dias} days"));
        $this->model->db->where('dataFinal', $dataLimite);
        $this->model->db->where_not_in('status', ['Finalizado', 'Cancelado']);
        return $this->model->getAll();
    }

    /**
     * Busca OS atrasadas
     */
    public function findOsAtrasadas(): array
    {
        $hoje = date('Y-m-d');
        $this->model->db->where('dataFinal <', $hoje);
        $this->model->db->where_not_in('status', ['Finalizado', 'Cancelado']);
        return $this->model->getAll();
    }

    /**
     * Busca OS por período
     */
    public function findByPeriod(string $inicio, string $fim): array
    {
        $this->model->db->where('dataInicial >=', $inicio);
        $this->model->db->where('dataInicial <=', $fim);
        return $this->model->getAll();
    }
}
