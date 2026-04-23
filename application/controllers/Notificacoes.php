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

        // Detectar se é técnico logado no portal ou admin
        $usuario_id = $this->session->userdata('id_admin');
        $tipo_usuario = 'admin';

        if (!$usuario_id && $this->session->userdata('logado_tecnico')) {
            $usuario_id = $this->session->userdata('tec_id');
            $tipo_usuario = 'tecnico';
        }

        if (!$usuario_id) {
            echo json_encode([
                'success' => false,
                'error' => 'Usuário não autenticado',
                'nao_lidas' => 0,
                'notificacoes' => []
            ]);
            return;
        }

        $notificacoes = $this->notificacoes_model->getNotificacoes($usuario_id, 15, false, $tipo_usuario);
        $nao_lidas = $this->notificacoes_model->countNaoLidas($usuario_id, $tipo_usuario);

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

        // Detectar se é técnico logado no portal ou admin
        $usuario_id = $this->session->userdata('id_admin');
        $tipo_usuario = 'admin';

        if (!$usuario_id && $this->session->userdata('logado_tecnico')) {
            $usuario_id = $this->session->userdata('tec_id');
            $tipo_usuario = 'tecnico';
        }

        if (!$usuario_id) {
            echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
            return;
        }

        if ($id) {
            $this->notificacoes_model->marcarLida($id, $usuario_id, $tipo_usuario);
        } else {
            $this->notificacoes_model->marcarTodasLidas($usuario_id, $tipo_usuario);
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

        $this->load->database();
        $this->db->where('config', 'app_theme');
        $this->db->update('configuracoes', ['valor' => $tema]);

        echo json_encode(['success' => true, 'tema' => $tema]);
    }
}