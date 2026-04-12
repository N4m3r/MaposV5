<?php
/**
 * Cliente Repository
 * Repositório para operações de clientes
 */

namespace Repositories;

use CI_Model;

class ClienteRepository extends AbstractRepository
{
    protected string $tableName = 'clientes';

    public function __construct()
    {
        $ci = \u0026get_instance();
        $ci->load->model('clientes_model');
        parent::__construct($ci->clientes_model);
    }

    /**
     * Busca clientes por termo de pesquisa
     */
    public function search(string $term): array
    {
        $this->model->db->like('nomeCliente', $term);
        $this->model->db->or_like('documento', $term);
        $this->model->db->or_like('email', $term);
        return $this->model->getAll();
    }

    /**
     * Busca cliente por documento (CPF/CNPJ)
     */
    public function findByDocumento(string $documento): ?object
    {
        $documento = preg_replace('/[^0-9]/', '', $documento);
        $this->model->db->where('documento', $documento);
        $result = $this->model->getAll();
        return $result[0] ?? null;
    }

    /**
     * Busca clientes com aniversário hoje
     */
    public function findAniversariantes(): array
    {
        $mesDia = date('m-d');
        $this->model->db->like('dataNascimento', "%-{$mesDia}", 'after');
        return $this->model->getAll();
    }
}
