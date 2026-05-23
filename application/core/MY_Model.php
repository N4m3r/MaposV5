<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * MY_Model — Base model with soft delete, fillable, and audit logging.
 *
 * Properties to set in child models:
 *   protected $table      = 'tablename';      // Required
 *   protected $primaryKey = 'id';               // Default: 'id'
 *   protected $fillable    = ['col1', 'col2'];  // Columns allowed for mass assignment
 *   protected $softDelete  = true;              // Enable soft delete (requires deleted_at column)
 *   protected $deletedAtField = 'deleted_at';   // Column name for soft delete timestamp
 */
class MY_Model extends CI_Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $softDelete = false;
    protected $deletedAtField = 'deleted_at';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Filter $data against $fillable whitelist.
     */
    protected function filterData(array $data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Insert a record. Respects $fillable whitelist.
     */
    public function insert(array $data)
    {
        $data = $this->filterData($data);
        $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() >= 1) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update a record by primary key. Respects $fillable whitelist.
     */
    public function update($id, array $data)
    {
        $data = $this->filterData($data);
        $this->db->where($this->primaryKey, $id);
        $this->db->update($this->table, $data);

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Soft delete (set deleted_at) or hard delete.
     */
    public function delete($id)
    {
        if ($this->softDelete) {
            $this->db->where($this->primaryKey, $id);
            $this->db->update($this->table, [$this->deletedAtField => date('Y-m-d H:i:s')]);

            return $this->db->affected_rows() >= 1;
        }

        $this->db->where($this->primaryKey, $id);
        $this->db->delete($this->table);

        return $this->db->affected_rows() >= 1;
    }

    /**
     * Permanently delete a record (bypass soft delete).
     */
    public function forceDelete($id)
    {
        $this->db->where($this->primaryKey, $id);
        $this->db->delete($this->table);

        return $this->db->affected_rows() >= 1;
    }

    /**
     * Find a record by primary key. Excludes soft-deleted by default.
     */
    public function find($id)
    {
        if ($this->softDelete) {
            $this->db->where($this->deletedAtField, null);
        }

        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
    }

    /**
     * Find a record including soft-deleted.
     */
    public function findWithTrashed($id)
    {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
    }

    /**
     * Get all records. Excludes soft-deleted by default.
     */
    public function all($limit = null, $offset = null)
    {
        if ($this->softDelete) {
            $this->db->where($this->deletedAtField, null);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get($this->table)->result();
    }

    /**
     * Count records. Excludes soft-deleted by default.
     */
    public function count()
    {
        if ($this->softDelete) {
            $this->db->where($this->deletedAtField, null);
        }

        return $this->db->count_all_results($this->table);
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore($id)
    {
        if (! $this->softDelete) {
            return false;
        }

        $this->db->where($this->primaryKey, $id);
        $this->db->update($this->table, [$this->deletedAtField => null]);

        return $this->db->affected_rows() >= 1;
    }
}