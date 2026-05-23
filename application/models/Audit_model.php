<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Audit_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ========== Legacy logs table methods ==========

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
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
        $this->db->insert('logs', $data);
        if ($this->db->affected_rows() >= 1) {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all('logs');
    }

    public function clean()
    {
        $this->db->where('data <', date('Y-m-d', strtotime('- 30 days')));
        $this->db->delete('logs');

        if ($this->db->affected_rows()) {
            return true;
        }

        return false;
    }

    // ========== Audit log methods ==========

    /**
     * Log an audit event.
     *
     * @param  string  $action    create, update, delete, login, etc.
     * @param  string  $table     Table name affected
     * @param  int|null  $recordId  Record ID affected
     * @param  array|null  $oldData   Previous data (for updates/deletes)
     * @param  array|null  $newData   New data (for creates/updates)
     */
    public function log($action, $table, $recordId = null, $oldData = null, $newData = null)
    {
        $ci = &get_instance();
        $userId = $ci->session->userdata('id_admin') ?: $ci->session->userdata('tec_id') ?: 0;
        $ip = $ci->input->ip_address();

        $data = [
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'old_data' => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'new_data' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->insert('audit_log', $data);
    }

    /**
     * Get audit logs with filters.
     */
    public function getAuditLogs($filters = [], $perpage = 0, $start = 0)
    {
        $this->db->from('audit_log');

        if (! empty($filters['action'])) {
            $this->db->where('action', $filters['action']);
        }
        if (! empty($filters['table_name'])) {
            $this->db->where('table_name', $filters['table_name']);
        }
        if (! empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }
        if (! empty($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to']);
        }

        $this->db->order_by('id', 'desc');

        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }

        return $this->db->get()->result();
    }

    /**
     * Clean audit logs older than N days.
     */
    public function cleanAuditLogs($days = 90)
    {
        $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->delete('audit_log');

        return $this->db->affected_rows() >= 0;
    }
}