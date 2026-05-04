<?php
/**
 * Auth Controller for API v2
 * Autenticação JWT para API
 */

require_once APPPATH . 'controllers/api/v2/ApiResponseTrait.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends CI_Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('usuarios_model');
    }

    /**
     * Login - Gera token JWT
     */
    public function login()
    {
        // Apenas POST permitido
        if ($this->input->method() !== 'POST') {
            return $this->error('Method not allowed', 405);
        }

        // Obtém dados do request
        $input = $this->getJsonInput();
        $email = $input['email'] ?? $this->input->post('email');
        $password = $input['password'] ?? $this->input->post('password');

        // Valida campos obrigatórios
        if (!$email || !$password) {
            return $this->error('Email and password are required', 400);
        }

        // Busca usuário
        $usuario = $this->usuarios_model->getByEmail($email);

        if (!$usuario) {
            return $this->error('Invalid credentials', 401);
        }

        // Verifica senha (suporta hash antigo e novo)
        $passwordValid = $this->verifyPassword($password, $usuario->senha);

        if (!$passwordValid) {
            return $this->error('Invalid credentials', 401);
        }

        // Verifica se usuário está ativo
        if ($usuario->situacao != 1) {
            return $this->error('User account is disabled', 403);
        }

        // Gera token JWT
        $token = $this->generateToken($usuario);
        $refreshToken = $this->generateRefreshToken($usuario);

        // Retorna resposta
        return $this->success([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 86400, // 24 horas
            'user' => [
                'id' => $usuario->idUsuarios,
                'name' => $usuario->nome,
                'email' => $usuario->email,
                'permissions' => $this->getUserPermissions($usuario->permissoes_id)
            ]
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        if ($this->input->method() !== 'POST') {
            return $this->error('Method not allowed', 405);
        }

        $input = $this->getJsonInput();
        $refreshToken = $input['refresh_token'] ?? $this->input->post('refresh_token');

        if (!$refreshToken) {
            return $this->error('Refresh token required', 400);
        }

        try {
            $key = getenv('JWT_SECRET') ?: 'mapos-secret-key';
            $decoded = JWT::decode($refreshToken, new Key($key, 'HS256'));

            // Busca usuário
            $usuario = $this->usuarios_model->getById($decoded->sub);

            if (!$usuario || $usuario->situacao != 1) {
                return $this->error('Invalid token', 401);
            }

            // Gera novo token
            $token = $this->generateToken($usuario);
            $newRefreshToken = $this->generateRefreshToken($usuario);

            return $this->success([
                'access_token' => $token,
                'refresh_token' => $newRefreshToken,
                'token_type' => 'Bearer',
                'expires_in' => 86400
            ]);

        } catch (\Exception $e) {
            return $this->error('Invalid token', 401);
        }
    }

    /**
     * Logout - revoga token (opcional)
     */
    public function logout()
    {
        // Em implementação stateless, o logout é feito no cliente
        // Aqui podemos adicionar token a uma blacklist se necessário
        return $this->success(['message' => 'Logged out successfully']);
    }

    /**
     * Health check - verifica se API está funcionando
     */
    public function health()
    {
        return $this->success([
            'status' => 'ok',
            'version' => 'v2',
            'timestamp' => date('c'),
            'environment' => ENVIRONMENT
        ]);
    }

    /**
     * Verifica senha (suporta hash antigo e novo)
     */
    private function verifyPassword(string $password, string $hash): bool
    {
        // Verifica se é Argon2
        if (strpos($hash, '$argon2id$') === 0 || strpos($hash, '$argon2i$') === 0) {
            return password_verify($password, $hash);
        }

        // Verifica se é Bcrypt
        if (strpos($hash, '$2y$') === 0) {
            return password_verify($password, $hash);
        }

        // Fallback para hash antigo (md5)
        return md5($password) === $hash;
    }

    /**
     * Retorna a chave JWT do config (API_JWT_KEY)
     */
    private function getJwtKey(): string
    {
        $this->load->config('jwt');
        return $this->config->item('jwt_key') ?: 'mapos-secret-key';
    }

    /**
     * Gera token JWT
     */
    private function generateToken(object $usuario): string
    {
        $key = $this->getJwtKey();

        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'exp' => time() + 86400,
            'sub' => $usuario->idUsuarios,
            'email' => $usuario->email,
            'name' => $usuario->nome,
            'permissions' => $this->getUserPermissions($usuario->permissoes_id),
            'API_TIME' => time() // compatibilidade com Authorization_Token library
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    /**
     * Gera refresh token
     */
    private function generateRefreshToken(object $usuario): string
    {
        $key = $this->getJwtKey();

        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'exp' => time() + (86400 * 30),
            'sub' => $usuario->idUsuarios,
            'type' => 'refresh',
            'API_TIME' => time()
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    /**
     * Obtém permissões do usuário
     */
    private function getUserPermissions(int $permissoesId): array
    {
        $this->load->model('permissoes_model');
        $permissoes = $this->permissoes_model->getById($permissoesId);

        if (!$permissoes) {
            return [];
        }

        return [
            'view_clientes' => (bool) $permissoes->vCliente,
            'add_clientes' => (bool) $permissoes->aCliente,
            'edit_clientes' => (bool) $permissoes->eCliente,
            'delete_clientes' => (bool) $permissoes->dCliente,
            'view_os' => (bool) $permissoes->vOs,
            'add_os' => (bool) $permissoes->aOs,
            'edit_os' => (bool) $permissoes->eOs,
            'delete_os' => (bool) $permissoes->dOs,
            'view_vendas' => (bool) $permissoes->vVenda,
            'add_vendas' => (bool) $permissoes->aVenda,
            'edit_vendas' => (bool) $permissoes->eVenda,
            'delete_vendas' => (bool) $permissoes->dVenda,
            'view_produtos' => (bool) $permissoes->vProduto,
            'add_produtos' => (bool) $permissoes->aProduto,
            'edit_produtos' => (bool) $permissoes->eProduto,
            'delete_produtos' => (bool) $permissoes->dProduto,
        ];
    }

    /**
     * Retorna dados JSON do request
     */
    private function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
}
