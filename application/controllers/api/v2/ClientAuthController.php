<?php

require_once APPPATH . 'controllers/api/v2/ApiResponseTrait.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ClientAuthController extends MY_Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('clientes_model');
    }

    public function login(): void
    {
        if ($this->input->method() !== 'POST') {
            $this->error('Method not allowed', 405);
            return;
        }

        $input = $this->getJsonInput();
        $email = $input['email'] ?? $this->input->post('email');
        $password = $input['senha'] ?? $input['password'] ?? $this->input->post('senha');

        if (!$email || !$password) {
            $this->error('Email e senha sao obrigatorios', 400);
            return;
        }

        $cliente = $this->db->where('email', $email)->get('clientes')->row();

        if (!$cliente) {
            $this->error('Credenciais invalidas', 401);
            return;
        }

        // Clientes podem ter senha propria ou usar documento como senha
        $valid = false;
        if (!empty($cliente->senha)) {
            if (password_verify($password, $cliente->senha)) {
                $valid = true;
            } elseif ($cliente->senha === md5($password)) {
                $valid = true;
                // Migra para bcrypt
                $this->db->where('idClientes', $cliente->idClientes)
                    ->update('clientes', ['senha' => password_hash($password, PASSWORD_BCRYPT)]);
            }
        }

        if (!$valid) {
            $this->error('Credenciais invalidas', 401);
            return;
        }

        $jwtKey = $this->config->item('jwt_key');
        if (!$jwtKey && isset($_ENV['API_JWT_KEY'])) {
            $jwtKey = $_ENV['API_JWT_KEY'];
        }

        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'exp' => time() + 86400,
            'sub' => $cliente->idClientes,
            'client_id' => $cliente->idClientes,
            'email' => $cliente->email,
            'name' => $cliente->nomeCliente,
            'type' => 'client',
            'API_TIME' => time(),
        ];

        $token = JWT::encode($payload, $jwtKey, 'HS256');

        $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 86400,
            'client' => [
                'id' => $cliente->idClientes,
                'nome' => $cliente->nomeCliente,
                'email' => $cliente->email,
            ],
        ]);
    }

    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}