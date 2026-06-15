<?php
/**
 * API Controller
 * Documentação e interface da API
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Api extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Documentação da API
     */
    public function docs()
    {
        $this->data['menuFerramentasV5'] = true;
        $this->data['menuApiDocs'] = true;
        $this->data['apiBaseUrl'] = base_url('api/v2');
        $this->data['view'] = 'api/docs';

        return $this->layout();
    }

    /**
     * Alias para docs (quando acessado como api/v2)
     */
    public function v2()
    {
        $this->docs();
    }

    /**
     * Gerar token JWT (para testes)
     */
    public function token()
    {
        if (!$this->input->post()) {
            show_error('Método não permitido');
        }

        $userId = $this->session->userdata('id');
        $email = $this->session->userdata('email');

        // Gera token usando Firebase JWT
        $this->load->config('jwt');
        $key = $this->config->item('jwt_key');
        if (empty($key)) {
            log_message('error', 'Api::token() - API_JWT_KEY nao configurado no .env. Autenticacao recusada.');
            show_error('API_JWT_KEY nao configurado. Configure no .env.', 500);
            return;
        }
        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 horas
            'sub' => $userId,
            'email' => $email
        ];

        $jwt = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'token' => $jwt,
                'expires' => date('Y-m-d H:i:s', $payload['exp'])
            ]));
    }

}
