<?php
/**
 * Model: Ux_columns_model
 * Persistencia da preferencia de colunas por usuario/listagem (F3.5).
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ux_columns_model extends CI_Model
{
    private $table = 'ux_user_columns';

    public function __construct()
    {
        parent::__construct();
    }

    public function get($userId, $tableKey)
    {
        if (!$this->db->table_exists($this->table)) {
            return ['hidden' => [], 'order' => []];
        }
        $row = $this->db
            ->where('user_id', $userId)
            ->where('table_key', $tableKey)
            ->get($this->table)
            ->row();
        if (!$row) return ['hidden' => [], 'order' => []];

        return [
            'hidden' => json_decode($row->hidden ?: '[]', true) ?: [],
            'order'  => json_decode($row->ordem ?: '[]', true) ?: [],
        ];
    }

    public function salvar($userId, $tableKey, array $hidden, array $order)
    {
        if (!$this->db->table_exists($this->table)) return false;
        $now = date('Y-m-d H:i:s');
        $existing = $this->db
            ->where('user_id', $userId)
            ->where('table_key', $tableKey)
            ->get($this->table)
            ->row();
        if ($existing) {
            return $this->db
                ->where('id', $existing->id)
                ->update($this->table, [
                    'hidden'     => json_encode(array_values($hidden), JSON_UNESCAPED_UNICODE),
                    'ordem'      => json_encode(array_values($order),  JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ]);
        }
        return $this->db->insert($this->table, [
            'user_id'    => $userId,
            'table_key'  => $tableKey,
            'hidden'     => json_encode(array_values($hidden), JSON_UNESCAPED_UNICODE),
            'ordem'      => json_encode(array_values($order),  JSON_UNESCAPED_UNICODE),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function resetar($userId, $tableKey)
    {
        if (!$this->db->table_exists($this->table)) return true;
        return $this->db
            ->where('user_id', $userId)
            ->where('table_key', $tableKey)
            ->delete($this->table);
    }
}
