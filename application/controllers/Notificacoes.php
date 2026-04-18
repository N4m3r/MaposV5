<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notificacoes_model');
        $this->notificacoes_model->ensureTableExists();
    }

    public function listar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $usuario_id = $this->session->userdata('id_admin');
        $notificacoes = $this->notificacoes_model->getNotificacoes($usuario_id, 15);
        $nao_lidas = $this->notificacoes_model->countNaoLidas($usuario_id);

        echo json_encode([
            'success' => true,
            'notificacoes' => $notificacoes,
            'nao_lidas' => $nao_lidas,
        ]);
    }

    public function marcar_lida()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $usuario_id = $this->session->userdata('id_admin');

        if ($id) {
            $this->notificacoes_model->marcarLida($id, $usuario_id);
        } else {
            $this->notificacoes_model->marcarTodasLidas($usuario_id);
        }

        echo json_encode(['success' => true]);
    }

    public function trocar_tema()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $tema = $this->input->post('tema');

        $temas_validos = ['default', 'white', 'puredark', 'darkviolet', 'darkorange', 'whitegreen', 'whiteblack'];
        if (!in_array($tema, $temas_validos)) {
            echo json_encode(['success' => false, 'error' => 'Tema inválido']);
            return;
        }

        $this->db->where('config', 'app_theme');
        $this->db->update('configuracoes', ['valor' => $tema]);

        echo json_encode(['success' => true, 'tema' => $tema]);
    }
}