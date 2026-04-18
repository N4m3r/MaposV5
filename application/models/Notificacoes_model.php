<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getNotificacoes($usuario_id, $limite = 10, $nao_lidas = false)
    {
        if (!$this->db->table_exists('notificacoes')) {
            return [];
        }

        $this->db->where('usuario_id', $usuario_id);
        if ($nao_lidas) {
            $this->db->where('lida', 0);
        }
        $this->db->order_by('data_notificacao', 'DESC');
        $this->db->limit($limite);

        return $this->db->get('notificacoes')->result();
    }

    public function countNaoLidas($usuario_id)
    {
        if (!$this->db->table_exists('notificacoes')) {
            return 0;
        }

        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('lida', 0);
        return $this->db->count_all_results('notificacoes');
    }

    public function adicionar($dados)
    {
        if (!$this->db->table_exists('notificacoes')) {
            return false;
        }

        return $this->db->insert('notificacoes', [
            'usuario_id' => $dados['usuario_id'],
            'titulo' => $dados['titulo'],
            'mensagem' => $dados['mensagem'],
            'url' => $dados['url'] ?? null,
            'icone' => $dados['icone'] ?? 'bx-bell',
            'tipo' => $dados['tipo'] ?? 'info',
            'lida' => 0,
            'data_notificacao' => date('Y-m-d H:i:s'),
        ]);
    }

    public function marcarLida($id, $usuario_id)
    {
        $this->db->where('id', $id);
        $this->db->where('usuario_id', $usuario_id);
        return $this->db->update('notificacoes', ['lida' => 1]);
    }

    public function marcarTodasLidas($usuario_id)
    {
        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('lida', 0);
        return $this->db->update('notificacoes', ['lida' => 1]);
    }

    public function notificarTodos($dados)
    {
        if (!$this->db->table_exists('notificacoes')) {
            return;
        }

        $usuarios = $this->db->select('idUsuarios')->get('usuarios')->result();
        foreach ($usuarios as $u) {
            $dados['usuario_id'] = $u->idUsuarios;
            $this->adicionar($dados);
        }
    }

    public function limparAntigas($dias = 30)
    {
        if (!$this->db->table_exists('notificacoes')) {
            return;
        }

        $this->db->where('data_notificacao <', date('Y-m-d H:i:s', strtotime("-{$dias} days")));
        $this->db->where('lida', 1);
        $this->db->delete('notificacoes');
    }

    public function ensureTableExists()
    {
        if ($this->db->table_exists('notificacoes')) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `notificacoes` (
              `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `usuario_id` INT(11) NOT NULL,
              `titulo` VARCHAR(200) NOT NULL,
              `mensagem` TEXT NOT NULL,
              `url` VARCHAR(500) NULL,
              `icone` VARCHAR(50) DEFAULT 'bx-bell',
              `tipo` VARCHAR(30) DEFAULT 'info',
              `lida` TINYINT(1) DEFAULT 0,
              `data_notificacao` DATETIME NOT NULL,
              PRIMARY KEY (`id`),
              INDEX `idx_usuario_lida` (`usuario_id`, `lida`),
              INDEX `idx_data` (`data_notificacao`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }
}