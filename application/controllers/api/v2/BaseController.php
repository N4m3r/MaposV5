<?php
/**
 * Base Controller for API v2
 * Controller base para todos os endpoints da API v2
 */

require_once APPPATH . 'controllers/api/v2/ApiResponseTrait.php';

// Carrega classes auxiliares manualmente (sem depender do autoload do Composer)
if (file_exists(APPPATH . 'libraries/Cache/CacheManager.php')) {
    require_once APPPATH . 'libraries/Cache/CacheManager.php';
}
if (file_exists(APPPATH . 'Security/RateLimiter.php')) {
    require_once APPPATH . 'Security/RateLimiter.php';
}

class BaseController extends MY_Controller
{
    use ApiResponseTrait;

    protected ?object $currentUser = null;
    protected array $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    public $cache;
    protected $rateLimiter;

    public function __construct()
    {
        parent::__construct();

        // Carrega helpers e libraries necessários
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');

        // Inicializa cache e rate limiter (fallback se classes nao existirem)
        // Usa stub de cache para evitar conflitos com CI Loader
        $this->cache = new class {
            public function remember(string $key, callable $callback, int $ttl = 300): mixed
            {
                return $callback();
            }
            public function flush(): void {}
        };

        $this->rateLimiter = null;
        if (class_exists('Libraries\Security\RateLimiter')) {
            $this->rateLimiter = new \Libraries\Security\RateLimiter();
        } elseif (class_exists('RateLimiter')) {
            $this->rateLimiter = new \RateLimiter();
        }

        // Verifica rate limiting
        if ($this->rateLimiter) {
            $this->checkRateLimit();
        }

        // Verifica autenticação
        $this->authenticate();

        // Configura headers CORS
        $this->setCorsHeaders();
    }

    /**
     * Autentica requisição via JWT, API Key de ambiente ou token fixo
     */
    protected function authenticate(): void
    {
        $authHeader   = $this->input->get_request_header('Authorization', true);
        $apiKeyHeader = $this->input->get_request_header('X-API-Key', true);

        // 1. API Key via header Authorization: Bearer <key> ou X-API-Key
        $envApiKey = $_ENV['API_MAPOS_KEY'] ?? '';
        if ($apiKeyHeader && $envApiKey && $apiKeyHeader === $envApiKey) {
            $scopeHeader = $this->input->get_request_header('X-API-Scopes');
            if (!empty($scopeHeader)) {
                $scopes = array_map('trim', explode(',', $scopeHeader));
            } else {
                log_message('warning', 'API key authenticated without X-API-Scopes header — denied');
                $this->forbidden('X-API-Scopes header is required when using API key authentication');
                exit;
            }
            $this->currentUser = (object) [
                'sub'         => 0,
                'email'       => 'api@system',
                'name'        => 'API System',
                'permissions' => $scopes
            ];
            return;
        }

        if ($authHeader && $envApiKey && str_replace('Bearer ', '', $authHeader) === $envApiKey) {
            $scopeHeader = $this->input->get_request_header('X-API-Scopes');
            if (!empty($scopeHeader)) {
                $scopes = array_map('trim', explode(',', $scopeHeader));
            } else {
                log_message('warning', 'API key authenticated without X-API-Scopes header — denied');
                $this->forbidden('X-API-Scopes header is required when using API key authentication');
                exit;
            }
            $this->currentUser = (object) [
                'sub'         => 0,
                'email'       => 'api@system',
                'name'        => 'API System',
                'permissions' => $scopes
            ];
            return;
        }

        // 3. Fallback para JWT Bearer padrão
        if (!$authHeader) {
            $this->unauthorized('Token ou API Key nao fornecido');
            exit;
        }

        $token = str_replace('Bearer ', '', $authHeader);

        // Verificar se token esta na blacklist (logout)
        $blacklistFile = APPPATH . 'cache/jwt_blacklist/' . md5($token) . '.json';
        if (file_exists($blacklistFile)) {
            $this->unauthorized('Token revoked');
            exit;
        }

        try {
            $this->load->config('jwt');
            $key = $this->config->item('jwt_key');
            $alg = $this->config->item('jwt_algorithm') ?: 'HS256';

            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($key, $alg));

            if (empty($decoded->sub)) {
                $this->unauthorized('Token invalido: sem identificacao de usuario');
                exit;
            }

            $this->currentUser = $decoded;

        } catch (\Exception $e) {
            $this->unauthorized('Erro na autenticacao: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * Configura headers CORS com whitelist configuravel
     */
    protected function setCorsHeaders(): void
    {
        $allowedOrigins = array_filter(array_map('trim', explode(',', $_ENV['CORS_ORIGINS'] ?? '')));
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (in_array($origin, $allowedOrigins, true)) {
            header("Access-Control-Allow-Origin: {$origin}");
        } elseif (!empty($allowedOrigins)) {
            // Se whitelist existe mas origin nao esta nela, rejeita
            header('Access-Control-Allow-Origin: none');
        } else {
            // Sem whitelist configurada: rejeitar em producao, permitir apenas localhost em desenvolvimento
            if (ENVIRONMENT === 'development') {
                header('Access-Control-Allow-Origin: http://localhost:8000');
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'CORS origin not allowed']);
                exit;
            }
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Key');
        header('Access-Control-Expose-Headers: X-RateLimit-Limit, X-RateLimit-Remaining');
        header('Vary: Origin');

        if ($this->input->method() === 'OPTIONS') {
            exit;
        }
    }

    /**
     * Retorna dados paginados da requisição
     */
    protected function getPaginationParams(): array
    {
        $page = (int) $this->input->get('page') ?: 1;
        $perPage = (int) $this->input->get('per_page') ?: 20;

        // Limita per_page máximo
        if ($perPage > 100) {
            $perPage = 100;
        }

        $offset = ($page - 1) * $perPage;

        return [
            'page' => $page,
            'per_page' => $perPage,
            'offset' => $offset
        ];
    }

    /**
     * Aplica filtros da requisição
     */
    protected function applyFilters(array $allowedFilters): array
    {
        $filters = [];

        foreach ($allowedFilters as $filter) {
            $value = $this->input->get($filter);
            if ($value !== null) {
                $filters[$filter] = $value;
            }
        }

        return $filters;
    }

    /**
     * Retorna dados JSON da requisição
     */
    protected function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    /**
     * Cacheia resposta
     */
    protected function cacheResponse(string $key, callable $callback, int $ttl = 300): mixed
    {
        return $this->cache->remember($key, $callback, $ttl);
    }

    /**
     * Limpa cache
     */
    protected function clearCache(string $pattern): void
    {
        $this->cache->flush();
    }

    /**
     * Valida permissão do usuário
     */
    protected function checkPermission(string $permission): void
    {
        $permissions = $this->currentUser->permissions ?? [];

        // API key de sistema tem permissao universal '*'
        if (in_array('*', $permissions) || in_array($permission, $permissions)) {
            return;
        }

        $this->forbidden('Permissao negada');
        exit;
    }

    /**
     * Loga acesso à API na tabela audit_log
     */
    protected function logAccess(string $endpoint, string $method): void
    {
        $ci = &get_instance();
        $ci->load->model('Audit_model');

        $ci->Audit_model->logAction([
            'user_id'     => $this->authUserId ?? null,
            'username'    => $this->authUsername ?? 'api',
            'action'      => 'api_access',
            'table_name'  => 'api',
            'record_id'   => null,
            'new_data'    => "{$method} {$endpoint}",
            'ip_address'  => $this->input->ip_address(),
            'user_agent'  => $this->input->user_agent(),
        ]);
    }

    /**
     * Verifica rate limiting
     */
    protected function checkRateLimit(): void
    {
        // Identificador baseado no IP ou usuário autenticado
        $identifier = $this->input->ip_address();

        // Limites diferentes por método HTTP
        $limits = [
            'GET' => ['limit' => 1000, 'window' => 3600],    // 1000 requisições/hora
            'POST' => ['limit' => 100, 'window' => 3600],     // 100 requisições/hora
            'PUT' => ['limit' => 100, 'window' => 3600],      // 100 requisições/hora
            'PATCH' => ['limit' => 100, 'window' => 3600],    // 100 requisições/hora
            'DELETE' => ['limit' => 50, 'window' => 3600]    // 50 requisições/hora
        ];

        $method = $this->input->method();
        $limit = $limits[$method] ?? $limits['GET'];

        $result = $this->rateLimiter->check($identifier, $limit['limit'], $limit['window']);

        // Aplica headers
        $this->rateLimiter->applyHeaders($result);

        // Bloqueia se excedeu limite
        if (!$result['allowed']) {
            $this->error('Rate limit exceeded. Please try again later.', 429);
            exit;
        }
    }

    /**
     * _remap - permite sufixos HTTP (_post, _get, etc.) nos métodos da API v2
     */
    public function _remap(string $method, array $params = []): void
    {
        $httpMethod = strtolower($this->input->method());
        $methodWithSuffix = $method . '_' . $httpMethod;

        if (method_exists($this, $methodWithSuffix)) {
            call_user_func_array([$this, $methodWithSuffix], $params);
            return;
        }

        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $params);
            return;
        }

        $this->notFound('Endpoint nao encontrado');
    }
}
