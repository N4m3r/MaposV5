<?php
/**
 * Api_tools
 * Endpoints utilitarios da API (tokens, health check)
 * Estende CI_Controller diretamente para nao depender de sessao do MapOS
 */

class Api_tools extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    /**
     * Gerar token JWT (para testes)
     */
    public function token()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Method not allowed']));
            return;
        }

        $userId = $this->session->userdata('id');
        $email = $this->session->userdata('email');

        if (!$userId) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Not authenticated']));
            return;
        }

        $key = getenv('JWT_SECRET') ?: 'mapos-secret-key';
        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24),
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

    /**
     * Gerar token de autorizacao do Agente IA
     * Formato: AUTH-XXXXXXXX
     */
    public function auth_token()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Method not allowed']));
            return;
        }

        // Requer sessao ativa para gerar token (seguranca)
        if (!$this->session->userdata('logado')) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Not authenticated']));
            return;
        }

        $token = 'AUTH-' . strtoupper(bin2hex(random_bytes(4)));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'token' => $token,
                'expires_in' => 900,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
            ]));
    }
}
