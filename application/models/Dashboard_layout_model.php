<?php
/**
 * Model: Dashboard_layout_model
 *
 * Gerencia layouts customizados do dashboard (F3.1 + F3.2).
 *
 * Estrategia de "layout ativo":
 *   - Pode haver varios layouts por usuario (perfis salvos)
 *   - Apenas 1 pode ter ativo=1 por usuario
 *   - Fallback: se o usuario nao tem layout ativo, busca o do perfil
 *     (admin/tecnico/financeiro) ou um default
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard_layout_model extends CI_Model
{
    private $table = 'dashboard_layouts';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna o layout ativo do usuario.
     * Ordem: user_personal > user_profile > global_perfil
     */
    public function getAtivo($userId, $perfil = 'padrao')
    {
        if (!$this->db->table_exists($this->table)) {
            return $this->defaultLayout();
        }

        // 1) Layout pessoal ativo
        $row = $this->db
            ->where('user_id', $userId)
            ->where('ativo', 1)
            ->order_by('updated_at', 'desc')
            ->get($this->table)
            ->row();

        // 2) Se nao tem, pega o do perfil
        if (!$row) {
            $row = $this->db
                ->where('user_id', null)
                ->where('perfil', $perfil)
                ->where('ativo', 1)
                ->get($this->table)
                ->row();
        }

        if (!$row) {
            return $this->defaultLayout();
        }

        return $this->decodeRow($row);
    }

    /**
     * Salva (upsert) o layout do usuario.
     * Se userId/perfil ja existe, atualiza; senao cria.
     */
    public function salvar($userId, $perfil, array $layout, array $visibility, $nomeAmigavel = null)
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }

        $layoutJson = json_encode(array_values($layout), JSON_UNESCAPED_UNICODE);
        $visJson    = json_encode($visibility, JSON_UNESCAPED_UNICODE);
        $now        = date('Y-m-d H:i:s');

        // Procura existente
        $existing = $this->db
            ->where('user_id', $userId)
            ->where('perfil', $perfil)
            ->get($this->table)
            ->row();

        if ($existing) {
            $this->db
                ->where('id', $existing->id)
                ->update($this->table, [
                    'layout'     => $layoutJson,
                    'visibility' => $visJson,
                    'nome'       => $nomeAmigavel ?: $existing->nome,
                    'updated_at' => $now,
                ]);
            // Marca como ativo do usuario
            $this->ativar($userId, $perfil);
            return true;
        }

        // Cria novo
        $ok = $this->db->insert($this->table, [
            'user_id'    => $userId,
            'perfil'     => $perfil,
            'nome'       => $nomeAmigavel,
            'layout'     => $layoutJson,
            'visibility' => $visJson,
            'ativo'      => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($ok) {
            $this->ativar($userId, $perfil);
        }
        return $ok;
    }

    /**
     * Marca o layout como ativo para o usuario (desativa os outros).
     */
    public function ativar($userId, $perfil)
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        // Desativa todos do usuario
        $this->db
            ->where('user_id', $userId)
            ->update($this->table, ['ativo' => 0, 'updated_at' => $now]);
        // Ativa o escolhido
        return $this->db
            ->where('user_id', $userId)
            ->where('perfil', $perfil)
            ->update($this->table, ['ativo' => 1, 'updated_at' => $now]);
    }

    /**
     * Remove a customizacao do usuario (volta ao default do perfil).
     */
    public function resetar($userId, $perfil)
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }

        // Remove layouts pessoais ativos
        $this->db
            ->where('user_id', $userId)
            ->delete($this->table);

        // Ativa o layout do perfil (se existir)
        $perfilLayout = $this->db
            ->where('user_id', null)
            ->where('perfil', $perfil)
            ->get($this->table)
            ->row();
        if ($perfilLayout) {
            $this->ativar($userId, $perfil);
        }
        return true;
    }

    /**
     * Lista todos os perfis salvos pelo usuario.
     */
    public function listarPerfis($userId)
    {
        if (!$this->db->table_exists($this->table)) {
            return [];
        }

        $rows = $this->db
            ->select('perfil, nome, ativo, updated_at')
            ->where('user_id', $userId)
            ->order_by('ativo', 'desc')
            ->order_by('updated_at', 'desc')
            ->get($this->table)
            ->result();

        return array_map(function($r) {
            return [
                'perfil' => $r->perfil,
                'nome'   => $r->nome ?: $r->perfil,
                'ativo'  => (bool) $r->ativo,
                'atualizado_em' => $r->updated_at,
            ];
        }, $rows);
    }

    /**
     * Layout padrao de fallback (quando nao ha customizacao).
     */
    public function defaultLayout()
    {
        return [
            'layout' => [
                'kpis',
                'quick-actions',
                'charts',
                'atividades',
            ],
            'visibility' => [
                'kpis'           => true,
                'quick-actions'  => true,
                'charts'         => true,
                'atividades'     => true,
            ],
        ];
    }

    // ====================================================================
    private function decodeRow($row)
    {
        $layout = json_decode($row->layout, true);
        $vis    = json_decode($row->visibility, true);

        if (!is_array($layout)) $layout = [];
        if (!is_array($vis))    $vis    = [];

        return [
            'layout'     => $layout,
            'visibility' => $vis,
            'perfil'     => $row->perfil,
            'nome'       => $row->nome,
        ];
    }
}
