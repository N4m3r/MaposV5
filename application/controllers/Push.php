<?php
/**
 * Push Subscriptions (F5.6)
 *
 * Endpoints:
 *  POST /push/subscribe      : inscreve um usuario em um endpoint
 *  POST /push/unsubscribe    : remove inscricao
 *  POST /push/send           : dispara um push (uso interno, pelo Notificacoes)
 *  GET  /push/vapid          : retorna a chave publica VAPID (frontend precisa para subscribe)
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Push extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function subscribe()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $userId = (int)$this->session->userdata('id');
        if (!$userId) {
            return $this->_json(['success' => false, 'msg' => 'Usuario nao logado'], 401);
        }

        $body = json_decode($this->input->raw_input_stream, true);
        if (!is_array($body)) {
            $body = $this->input->post();
        }

        $endpoint = isset($body['endpoint']) ? trim((string)$body['endpoint']) : '';
        $keys = isset($body['keys']) && is_array($body['keys']) ? $body['keys'] : [];
        $p256dh = isset($keys['p256dh']) ? trim((string)$keys['p256dh']) : '';
        $auth   = isset($keys['auth'])   ? trim((string)$keys['auth'])   : '';

        if (!$endpoint || !$p256dh || !$auth) {
            return $this->_json(['success' => false, 'msg' => 'Dados de inscricao invalidos'], 400);
        }
        if (strlen($endpoint) > 500) {
            return $this->_json(['success' => false, 'msg' => 'Endpoint muito longo'], 400);
        }

        $userAgent = $this->input->user_agent() ?: null;

        // Upsert
        $exists = $this->db->get_where('push_subscriptions', ['endpoint' => $endpoint])->row();
        $data = [
            'user_id'    => $userId,
            'endpoint'   => $endpoint,
            'p256dh'     => $p256dh,
            'auth'       => $auth,
            'user_agent' => $userAgent,
            'ativo'      => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($exists) {
            $this->db->where('id', $exists->id)->update('push_subscriptions', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('push_subscriptions', $data);
        }
        return $this->_json(['success' => true, 'msg' => 'Inscrito em notificacoes push']);
    }

    public function unsubscribe()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $body = json_decode($this->input->raw_input_stream, true) ?: $this->input->post();
        $endpoint = isset($body['endpoint']) ? trim((string)$body['endpoint']) : '';
        if (!$endpoint) {
            return $this->_json(['success' => false, 'msg' => 'Endpoint nao enviado'], 400);
        }
        $this->db->where('endpoint', $endpoint)->update('push_subscriptions', [
            'ativo' => 0, 'updated_at' => date('Y-m-d H:i:s')
        ]);
        return $this->_json(['success' => true]);
    }

    /**
     * Dispara uma notificacao push para um usuario (ou todos).
     *
     * Chamada interna (pelo modulo de Notificacoes):
     *   $this->load->library('push_service');
     *   $this->push_service->sendToUser($userId, $titulo, $mensagem, $url);
     *
     * Endpoint publico para testes (requer login admin):
     *   POST /push/send
     *   { user_id: int, titulo: string, mensagem: string, url?: string }
     */
    public function send()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $permissao = $this->session->userdata('permissao');
        if (!$this->permission->checkPermission($permissao, 'cConfiguracao')) {
            return $this->_json(['success' => false, 'msg' => 'Sem permissao'], 403);
        }

        $userId   = (int)$this->input->post('user_id');
        $titulo   = trim((string)$this->input->post('titulo'));
        $mensagem = trim((string)$this->input->post('mensagem'));
        $url      = trim((string)$this->input->post('url')) ?: null;

        if (!$titulo || !$mensagem) {
            return $this->_json(['success' => false, 'msg' => 'Titulo e mensagem sao obrigatorios'], 400);
        }

        $this->load->library('Push_service');
        $ok = $this->push_service->sendToUser($userId, $titulo, $mensagem, $url);
        return $this->_json(['success' => $ok]);
    }

    public function vapid()
    {
        // Carrega a chave publica VAPID para o frontend poder se inscrever
        $publicKey = $this->config->item('vapid_public_key');
        if (!$publicKey) {
            $publicKey = getenv('VAPID_PUBLIC_KEY') ?: '';
        }
        return $this->_json([
            'success' => true,
            'publicKey' => $publicKey,
            'enabled' => (bool)$publicKey,
        ]);
    }

    private function _json($data, $code = 200)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
