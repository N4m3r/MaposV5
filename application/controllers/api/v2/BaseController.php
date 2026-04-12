<?php
/**
 * Base Controller for API v2
 * Controller base para todos os endpoints da API v2
 */

require_once APPPATH . 'controllers/api/v2/ApiResponseTrait.php';
require_once APPPATH . 'Security/RateLimiter.php';

use Libraries\Cache\CacheManager;
use Libraries\Security\RateLimiter;

class BaseController extends CI_Controller
{
    use ApiResponseTrait;

    protected ?object $currentUser = null;
    protected array $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    protected CacheManager $cache;
    protected RateLimiter $rateLimiter;

    public function __construct()
    {
        parent::__construct();

        // Carrega helpers e libraries necessários
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');

        // Inicializa cache
        $this->cache = new CacheManager();

        // Inicializa rate limiter
        $this->rateLimiter = new RateLimiter();

        // Verifica rate limiting
        $this->checkRateLimit();

        // Verifica autenticação
        $this->authenticate();

        // Configura headers CORS
        $this->setCorsHeaders();
    }

    /**
     * Autentica requisição via JWT
     */
    protected function authenticate(): void
    {
        $authHeader = $this->input->get_request_header('Authorization', true);

        if (!$authHeader) {
            $this->unauthorized('Token não fornecido');
            exit;
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $this->load->library('Authorization_Token');
            $decoded = $this->authorization_token->validateToken($token);

            if (!$decoded) {
                $this->unauthorized('Token inválido');
                exit;
            }

            $this->currentUser = $decoded;

        } catch (\Exception $e) {
            $this->unauthorized('Erro na autenticação: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * Configura headers CORS
     */
    protected function setCorsHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Expose-Headers: X-RateLimit-Limit, X-RateLimit-Remaining');

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
        if (!$this->currentUser || !in_array($permission, $this->currentUser->permissions ?? [])) {
            $this->forbidden('Permissão negada');
            exit;
        }
    }

    /**
     * Loga acesso à API
     */
    protected function logAccess(string $endpoint, string $method): void
    {
        $ci = \u0026get_instance();
        $ci->load->model('Audit_model');

        $ci->Audit_model->addLog([
            'acao' => 'api_access',
            'tabela' => 'api',
            'id_registro' => 0,
            'detalhes' => "{$method} {$endpoint}",
            'ip' => $this->input->ip_address()
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
}
