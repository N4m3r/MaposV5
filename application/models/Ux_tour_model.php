<?php
/**
 * Model: ux_tour_progress
 *
 * Gerencia o progresso de tours guiados por usuario.
 * Adicionado em 2026-06-14 (Fase 2.1.2 do Plano UX).
 *
 * Estrutura da tabela:
 *   id, user_id, tour_key, completed_at, skipped, created_at, updated_at
 *
 * Uso:
 *   $this->load->model('ux_tour_model');
 *   $status = $this->ux_tour_model->getStatus($userId, 'dashboard_inicial');
 *   $this->ux_tour_model->markCompleted($userId, 'dashboard_inicial');
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ux_tour_model extends CI_Model
{
    private $tableName = 'ux_tour_progress';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna o status de um tour para um usuario.
     *
     * @param int    $userId
     * @param string $tourKey
     * @return array|null ['completed' => bool, 'skipped' => bool, 'completed_at' => string|null]
     */
    public function getStatus($userId, $tourKey)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return ['completed' => false, 'skipped' => false, 'completed_at' => null];
        }

        $row = $this->db
            ->where('user_id', $userId)
            ->where('tour_key', $tourKey)
            ->get($this->tableName)
            ->row();

        if (!$row) {
            return ['completed' => false, 'skipped' => false, 'completed_at' => null];
        }

        return [
            'completed'    => !empty($row->completed_at),
            'skipped'      => (bool) $row->skipped,
            'completed_at' => $row->completed_at,
        ];
    }

    /**
     * Retorna todos os tours concluidos/pulados de um usuario.
     */
    public function getAllForUser($userId)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return [];
        }

        $rows = $this->db
            ->where('user_id', $userId)
            ->get($this->tableName)
            ->result();

        $out = [];
        foreach ($rows as $r) {
            $out[$r->tour_key] = [
                'completed'    => !empty($r->completed_at),
                'skipped'      => (bool) $r->skipped,
                'completed_at' => $r->completed_at,
            ];
        }
        return $out;
    }

    /**
     * Marca um tour como concluido (upsert).
     */
    public function markCompleted($userId, $tourKey)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $existing = $this->db
            ->where('user_id', $userId)
            ->where('tour_key', $tourKey)
            ->get($this->tableName)
            ->row();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $this->db
                ->where('user_id', $userId)
                ->where('tour_key', $tourKey)
                ->update($this->tableName, [
                    'completed_at' => $now,
                    'skipped'      => 0,
                    'updated_at'   => $now,
                ]);
            return $this->db->affected_rows() > 0;
        }

        return $this->db->insert($this->tableName, [
            'user_id'      => $userId,
            'tour_key'     => $tourKey,
            'completed_at' => $now,
            'skipped'      => 0,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);
    }

    /**
     * Marca um tour como pulado.
     */
    public function markSkipped($userId, $tourKey)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        $existing = $this->db
            ->where('user_id', $userId)
            ->where('tour_key', $tourKey)
            ->get($this->tableName)
            ->row();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $this->db
                ->where('user_id', $userId)
                ->where('tour_key', $tourKey)
                ->update($this->tableName, [
                    'skipped'    => 1,
                    'updated_at' => $now,
                ]);
            return true;
        }

        return $this->db->insert($this->tableName, [
            'user_id'    => $userId,
            'tour_key'   => $tourKey,
            'skipped'    => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reseta o progresso de um tour (permite refazer).
     */
    public function reset($userId, $tourKey)
    {
        if (!$this->db->table_exists($this->tableName)) {
            return false;
        }

        return $this->db
            ->where('user_id', $userId)
            ->where('tour_key', $tourKey)
            ->delete($this->tableName);
    }

    /**
     * Estatísticas globais de conclusao de tours (para admin/dashboard).
     */
    public function getStats()
    {
        if (!$this->db->table_exists($this->tableName)) {
            return ['total_concluidos' => 0, 'total_pulados' => 0, 'total_pendentes' => 0];
        }

        $concluidos = $this->db
            ->where('completed_at IS NOT NULL', null, false)
            ->count_all_results($this->tableName);
        $pulados = $this->db
            ->where('skipped', 1)
            ->count_all_results($this->tableName);
        $usuarios_unicos = $this->db
            ->select('COUNT(DISTINCT(user_id)) AS total')
            ->get($this->tableName)
            ->row();
        $usuarios_total = $this->db->count_all('usuarios');

        return [
            'total_concluidos'   => $concluidos,
            'total_pulados'      => $pulados,
            'usuarios_com_tour'  => (int) ($usuarios_unicos->total ?? 0),
            'usuarios_total'     => $usuarios_total,
            'taxa_adocao'        => $usuarios_total > 0
                ? round(($usuarios_unicos->total / $usuarios_total) * 100, 1)
                : 0,
        ];
    }
}
