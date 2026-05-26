<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Data_breach_model extends CI_Model
{
    private $table = 'data_breach_notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(int $limit = 50, int $offset = 0): array
    {
        if (!$this->db->table_exists($this->table)) {
            return [];
        }
        return $this->db->order_by('data_ocorrencia', 'DESC')
            ->limit($limit, $offset)
            ->get($this->table)
            ->result();
    }

    public function getById(int $id)
    {
        if (!$this->db->table_exists($this->table)) {
            return null;
        }
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function count(): int
    {
        if (!$this->db->table_exists($this->table)) {
            return 0;
        }
        return $this->db->count_all($this->table);
    }

    public function add(array $data): int
    {
        if (!$this->db->table_exists($this->table)) {
            return 0;
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Marca notificacao como enviada aos titulares e conta os afetados
     */
    public function markTitularesNotificados(int $id, int $numAfetados): bool
    {
        return $this->update($id, [
            'titulares_notificados' => 1,
            'data_notificacao_titulares' => date('Y-m-d H:i:s'),
            'num_titulares_afetados' => $numAfetados,
        ]);
    }
}