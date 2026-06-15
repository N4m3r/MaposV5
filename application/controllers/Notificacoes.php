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
        try {
            $this->notificacoes_model->ensureTableExists();
        } catch (Exception $e) {
            log_message('error', 'Falha ao garantir tabela notificacoes: ' . $e->getMessage());
        }
    }

    public function listar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        header('Content-Type: application/json');

        try {
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
        } catch (Exception $e) {
            log_message('error', 'Erro em Notificacoes::listar: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Erro interno',
                'nao_lidas' => 0,
                'notificacoes' => []
            ]);
        }
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

        // Invalidar cache de configuracoes (TTL de 5min em MY_Controller::load_configuration)
        $cacheFile = APPPATH . 'cache/configuracoes_cache.json';
        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }

        echo json_encode(['success' => true, 'tema' => $tema]);
    }

    /**
     * GET /notificacoes/smart
     * Retorna contadores para badges inteligentes no topo (F4.1):
     *   - OS atrasadas
     *   - Boletos vencendo hoje
     *   - Lancamentos atrasados
     *   - OS aguardando aprovacao (se for gerente)
     *   - Clientes novos (ultimos 7 dias)
     */
    public function smart()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->database();

        $smart = [
            'os_atrasadas'         => 0,
            'boletos_hoje'         => 0,
            'lancamentos_atrasados'=> 0,
            'os_aguardando'        => 0,
            'clientes_novos'       => 0,
        ];

        // 1) OS atrasadas (dataFinal < hoje e status nao eh 'Finalizado'/'Cancelado')
        if ($this->db->table_exists('os')) {
            $smart['os_atrasadas'] = (int) $this->db
                ->where('dataFinal <', date('Y-m-d'))
                ->where_not_in('status', ['Finalizado', 'Cancelado', 'Faturado'])
                ->count_all_results('os');
            // OS aguardando aprovacao
            $smart['os_aguardando'] = (int) $this->db
                ->where('status', 'Aguardando Aprovação')
                ->count_all_results('os');
        }

        // 2) Boletos vencendo hoje
        if ($this->db->table_exists('cobrancas')) {
            $smart['boletos_hoje'] = (int) $this->db
                ->where('vencimento', date('Y-m-d'))
                ->where('status', 'pendente')
                ->count_all_results('cobrancas');
        }

        // 3) Lancamentos atrasados
        if ($this->db->table_exists('lancamentos')) {
            $smart['lancamentos_atrasados'] = (int) $this->db
                ->where('vencimento <', date('Y-m-d'))
                ->where('status', 'pendente')
                ->count_all_results('lancamentos');
        }

        // 4) Clientes novos (7 dias)
        if ($this->db->table_exists('clientes')) {
            $smart['clientes_novos'] = (int) $this->db
                ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                ->count_all_results('clientes');
        }

        $total = array_sum($smart);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'smart' => $smart, 'total' => $total]);
    }
}