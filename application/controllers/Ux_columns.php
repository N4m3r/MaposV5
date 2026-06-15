<?php
/**
 * Controller: Ux_columns
 *
 * Persiste preferencias de colunas por usuario/listagem (F3.5).
 *
 * Endpoints:
 *   GET  /ux_columns/listar?table_key=xxx
 *   POST /ux_columns/salvar  body: {table_key, hidden: [], order: []}
 *   POST /ux_columns/resetar body: {table_key}
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ux_columns extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ux_columns_model');
    }

    public function listar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) return $this->json_error('Nao autenticado', 401);
        $key = $this->input->get('table_key', true);
        if (empty($key)) return $this->json_error('table_key obrigatorio', 400);
        $state = $this->ux_columns_model->get($userId, $key);
        return $this->json_success(['state' => $state]);
    }

    public function salvar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) return $this->json_error('Nao autenticado', 401);
        $key = $this->input->post('table_key', true);
        if (empty($key) || !preg_match('/^[a-z0-9_]{1,60}$/i', $key)) {
            return $this->json_error('table_key invalido', 400);
        }
        $hidden = $this->input->post('hidden');
        $order  = $this->input->post('order');
        if (!is_array($hidden)) $hidden = [];
        if (!is_array($order))  $order  = [];

        // Sanitiza
        $hiddenSan = array_values(array_filter($hidden, function($v) {
            return is_string($v) && preg_match('/^[a-z0-9_]{1,40}$/i', $v);
        }));
        $orderSan = array_values(array_filter($order, function($v) {
            return is_string($v) && preg_match('/^[a-z0-9_]{1,40}$/i', $v);
        }));

        $ok = $this->ux_columns_model->salvar($userId, $key, $hiddenSan, $orderSan);
        if (!$ok) return $this->json_error('Falha ao salvar', 500);
        return $this->json_success(['saved' => true]);
    }

    public function resetar()
    {
        $userId = (int) $this->session->userdata('id');
        if ($userId <= 0) return $this->json_error('Nao autenticado', 401);
        $key = $this->input->post('table_key', true);
        if (empty($key)) return $this->json_error('table_key obrigatorio', 400);
        $this->ux_columns_model->resetar($userId, $key);
        return $this->json_success(['reset' => true]);
    }

    private function json_success($data, $code = 200) { return json_success($data, $code); }
    private function json_error($msg, $code = 400)     { return json_error($msg, $code); }
}
