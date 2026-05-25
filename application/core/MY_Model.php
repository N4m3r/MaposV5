<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base Model with Mass Assignment Protection
 * Models that extend this class can define $fillable to whitelist
 * allowed fields for insert/update operations.
 */
class MY_Model extends CI_Model
{
    protected $table = '';
    protected $fillable = [];
    protected $returnInsertId = false;

    /**
     * Filter data array to only include fillable fields.
     * If $fillable is empty, all fields are allowed (backwards compatibility).
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Insert with fillable protection and optional transaction.
     */
    public function insert(array $data)
    {
        $data = $this->filterFillable($data);

        if ($this->returnInsertId) {
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }

        $this->db->insert($this->table, $data);
        return $this->db->affected_rows() >= 1;
    }

    /**
     * Update with fillable protection.
     */
    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);
        $this->db->where($this->primaryKey ?? 'id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Find by ID.
     */
    public function findById(int $id)
    {
        return $this->db->where($this->primaryKey ?? 'id', $id)
            ->get($this->table)
            ->row();
    }

    /**
     * Get all records.
     */
    public function findAll(int $limit = null, int $offset = 0): array
    {
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get($this->table)->result();
    }

    /**
     * Soft delete (if deleted_at column exists).
     */
    public function softDelete(int $id): bool
    {
        if (!$this->db->field_exists('deleted_at', $this->table)) {
            return $this->delete($id);
        }

        $this->db->where($this->primaryKey ?? 'id', $id);
        $this->db->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Hard delete.
     */
    public function delete(int $id): bool
    {
        $this->db->where($this->primaryKey ?? 'id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows() >= 1;
    }
}