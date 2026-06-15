<?php
/**
 * Controller: Ux_tour
 *
 * Endpoints para gerenciar o progresso dos tours guiados.
 * Adicionado em 2026-06-14 (Fase 2.1.5 do Plano UX).
 *
 * Rotas:
 *   GET  /index.php/ux_tour/listar              -> status de TODOS os tours do usuario logado
 *   GET  /index.php/ux_tour/status?key=xxx      -> status de 1 tour especifico
 *   GET  /index.php/ux_tour/definicoes         -> JSON com definicoes de todos os tours
 *   POST /index.php/ux_tour/concluir           -> body: {tour_key}
 *   POST /index.php/ux_tour/pular              -> body: {tour_key}
 *   POST /index.php/ux_tour/reiniciar          -> body: {tour_key} (permite refazer)
 *   GET  /index.php/ux_tour/estatisticas       -> stats globais (admin)
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ux_tour extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ux_tour_model');
        $this->load->config('ux_tours');
    }

    /**
     * Retorna o status de TODOS os tours do usuario logado.
     * Util para carregar o runner JS sem multiplas chamadas.
     */
    public function listar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $status = $this->ux_tour_model->getAllForUser($userId);
        return $this->json_success(['tours' => $status]);
    }

    /**
     * Retorna o status de 1 tour especifico.
     * GET /ux_tour/status?key=dashboard_inicial
     */
    public function status()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $key = $this->input->get('key', true);
        if (empty($key)) {
            return $this->json_error('Parâmetro "key" é obrigatório', 400);
        }

        $status = $this->ux_tour_model->getStatus($userId, $key);
        return $this->json_success(['tour_key' => $key, 'status' => $status]);
    }

    /**
     * Retorna a definicao de todos os tours (titulo, steps, etc).
     * O frontend consome isso para inicializar o Driver.js.
     */
    public function definicoes()
    {
        $tours = $this->config->item('ux_tours');
        if (!is_array($tours)) {
            $tours = [];
        }

        // Mescla o status pessoal do usuario para o frontend decidir se exibe botao "rever"
        $userId = (int) $this->session->userdata('id');
        $status = [];
        if ($userId > 0) {
            $status = $this->ux_tour_model->getAllForUser($userId);
        }

        return $this->json_success([
            'tours'  => $tours,
            'status' => $status,
        ]);
    }

    /**
     * Marca um tour como concluido.
     * POST /ux_tour/concluir   body: {tour_key: 'dashboard_inicial'}
     */
    public function concluir()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $key = $this->input->post('tour_key', true);
        if (empty($key)) {
            return $this->json_error('Campo "tour_key" é obrigatório', 400);
        }

        $ok = $this->ux_tour_model->markCompleted($userId, $key);
        if (!$ok) {
            return $this->json_error('Não foi possível registrar a conclusão (tabela indisponível?)', 500);
        }

        log_message('info', "[UX Tour] Usuario {$userId} concluiu tour '{$key}'");
        return $this->json_success(['tour_key' => $key, 'status' => 'concluido']);
    }

    /**
     * Marca um tour como pulado.
     * POST /ux_tour/pular   body: {tour_key: 'dashboard_inicial'}
     */
    public function pular()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $key = $this->input->post('tour_key', true);
        if (empty($key)) {
            return $this->json_error('Campo "tour_key" é obrigatório', 400);
        }

        $ok = $this->ux_tour_model->markSkipped($userId, $key);
        if (!$ok) {
            return $this->json_error('Não foi possível registrar o pulo', 500);
        }

        log_message('info', "[UX Tour] Usuario {$userId} pulou tour '{$key}'");
        return $this->json_success(['tour_key' => $key, 'status' => 'pulado']);
    }

    /**
     * Reseta o progresso de um tour (permite refazer).
     * POST /ux_tour/reiniciar   body: {tour_key: 'dashboard_inicial'}
     */
    public function reiniciar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) {
            return $this->json_error('Usuário não autenticado', 401);
        }

        $key = $this->input->post('tour_key', true);
        if (empty($key)) {
            return $this->json_error('Campo "tour_key" é obrigatório', 400);
        }

        $ok = $this->ux_tour_model->reset($userId, $key);
        if (!$ok) {
            return $this->json_error('Não foi possível reiniciar o tour', 500);
        }

        log_message('info', "[UX Tour] Usuario {$userId} reiniciou tour '{$key}'");
        return $this->json_success(['tour_key' => $key, 'status' => 'pendente']);
    }

    /**
     * Estatisticas globais de conclusao (admin).
     * GET /ux_tour/estatisticas
     */
    public function estatisticas()
    {
        $stats = $this->ux_tour_model->getStats();
        return $this->json_success(['stats' => $stats]);
    }
}
