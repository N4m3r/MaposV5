<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Audit_model extends CI_Model
{
    private $auditTable = 'audit_log';
    private $legacyTable = 'logs';

    public function __construct()
    {
        parent::__construct();
    }

    // ========================================================================
    // Legacy methods (logs table) - kept for backward compatibility
    // ========================================================================

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($this->legacyTable);
        $this->db->order_by('idLogs', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function add($data)
    {
        $this->db->insert($this->legacyTable, $data);
        if ($this->db->affected_rows() >= 1) {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($this->legacyTable);
    }

    public function clean()
    {
        $this->db->where('data <', date('Y-m-d', strtotime('- 30 days')));
        $this->db->delete($this->legacyTable);

        if ($this->db->affected_rows()) {
            return true;
        }

        return false;
    }

    // ========================================================================
    // New audit_log methods
    // ========================================================================

    /**
     * Log an action to the audit_log table
     */
    public function logAction(array $data): bool
    {
        if (!$this->db->table_exists($this->auditTable)) {
            // Fallback to legacy log if audit_log doesn't exist yet
            return $this->add([
                'usuario' => $data['username'] ?? 'system',
                'tarefa'  => ($data['action'] ?? 'unknown') . ' ' . ($data['table_name'] ?? '') . ' ' . ($data['record_id'] ?? ''),
                'data'    => date('Y-m-d'),
                'hora'    => date('H:i:s'),
                'ip'      => $data['ip_address'] ?? '',
            ]);
        }

        $insert = [
            'user_id'     => $data['user_id'] ?? null,
            'username'    => $data['username'] ?? 'system',
            'action'      => $data['action'] ?? 'unknown',
            'table_name'  => $data['table_name'] ?? '',
            'record_id'   => $data['record_id'] ?? null,
            'old_data'    => isset($data['old_data']) ? json_encode($data['old_data'], JSON_UNESCAPED_UNICODE) : null,
            'new_data'    => isset($data['new_data']) ? json_encode($data['new_data'], JSON_UNESCAPED_UNICODE) : null,
            'ip_address'  => $data['ip_address'] ?? '',
            'user_agent'  => $data['user_agent'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->auditTable, $insert);
        return $this->db->affected_rows() >= 1;
    }

    /**
     * Get audit entries by table name
     */
    public function getByTable(string $table, int $limit = 50, int $offset = 0): array
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return [];
        }

        return $this->db->where('table_name', $table)
            ->order_by('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get($this->auditTable)
            ->result();
    }

    /**
     * Get audit entries for a specific record
     */
    public function getByRecord(string $table, string $recordId, int $limit = 50): array
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return [];
        }

        return $this->db->where('table_name', $table)
            ->where('record_id', $recordId)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get($this->auditTable)
            ->result();
    }

    /**
     * Get audit entries by user
     */
    public function getByUser(int $userId, int $limit = 50, int $offset = 0): array
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return [];
        }

        return $this->db->where('user_id', $userId)
            ->order_by('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get($this->auditTable)
            ->result();
    }

    /**
     * Clean audit entries older than N days
     */
    public function cleanOld(int $days = 90): bool
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return false;
        }

        $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->delete($this->auditTable);
        return true;
    }

    /**
     * Count audit_log entries with optional filters
     */
    public function countAudit(array $filters = []): int
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return 0;
        }

        $this->applyAuditFilters($filters);
        return $this->db->count_all_results($this->auditTable);
    }

    /**
     * Get audit_log entries with pagination and filters
     */
    public function getAudit(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return [];
        }

        $this->applyAuditFilters($filters);
        return $this->db->order_by('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get($this->auditTable)
            ->result();
    }

    /**
     * Get distinct table names from audit_log (for filter dropdown)
     */
    public function getTableNames(): array
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return [];
        }

        return $this->db->select('table_name')
            ->distinct()
            ->order_by('table_name')
            ->get($this->auditTable)
            ->result_array();
    }

    /**
     * Get distinct actions from audit_log (for filter dropdown)
     */
    public function getActions(): array
    {
        if (!$this->db->table_exists($this->auditTable)) {
            return [];
        }

        return $this->db->select('action')
            ->distinct()
            ->order_by('action')
            ->get($this->auditTable)
            ->result_array();
    }

    private function applyAuditFilters(array $filters): void
    {
        if (!empty($filters['table_name'])) {
            $this->db->where('table_name', $filters['table_name']);
        }
        if (!empty($filters['action'])) {
            $this->db->where('action', $filters['action']);
        }
        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
    }
}