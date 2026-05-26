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
    protected $softDelete = false;

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
     * Apply soft delete filter to queries.
     * Call this before get() to exclude soft-deleted records.
     */
    protected function withTrashed(bool $includeTrashed = false): self
    {
        if ($this->softDelete && !$includeTrashed && $this->db->field_exists('deleted_at', $this->table)) {
            $this->db->where($this->table . '.deleted_at IS NULL', null, false);
        }
        return $this;
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
     * Find by ID (respects soft delete).
     */
    public function findById(int $id, bool $withTrashed = false)
    {
        if ($this->softDelete && !$withTrashed) {
            $this->db->where($this->table . '.deleted_at IS NULL', null, false);
        }
        return $this->db->where($this->primaryKey ?? 'id', $id)
            ->get($this->table)
            ->row();
    }

    /**
     * Get all records (respects soft delete).
     */
    public function findAll(int $limit = null, int $offset = 0, bool $withTrashed = false): array
    {
        if ($this->softDelete && !$withTrashed && $this->db->field_exists('deleted_at', $this->table)) {
            $this->db->where($this->table . '.deleted_at IS NULL', null, false);
        }
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get($this->table)->result();
    }

    /**
     * Soft delete (if softDelete enabled and deleted_at column exists).
     */
    public function softDelete(int $id): bool
    {
        if (!$this->softDelete || !$this->db->field_exists('deleted_at', $this->table)) {
            return $this->hardDelete($id);
        }

        $this->db->where($this->primaryKey ?? 'id', $id);
        $this->db->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Hard delete - permanently removes the record.
     */
    public function hardDelete(int $id): bool
    {
        $this->db->where($this->primaryKey ?? 'id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows() >= 1;
    }

    /**
     * Delete by ID - uses soft delete if enabled, otherwise hard delete.
     * Subclasses may override with delete($table, $fieldID, $ID) signature.
     */
    public function deleteById(int $id): bool
    {
        if ($this->softDelete && $this->db->field_exists('deleted_at', $this->table)) {
            return $this->softDelete($id);
        }
        return $this->hardDelete($id);
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(int $id): bool
    {
        $this->db->where($this->primaryKey ?? 'id', $id);
        $this->db->update($this->table, ['deleted_at' => null]);
        return $this->db->affected_rows() >= 0;
    }
}