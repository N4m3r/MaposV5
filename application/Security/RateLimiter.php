<?php
/**
 * Rate Limiter
 * Sistema de rate limiting para API
 */

namespace Libraries\Security;

class RateLimiter
{
    private $cache;
    private int $defaultLimit = 100;
    private int $defaultWindow = 3600; // 1 hora

    public function __construct()
    {
        $ci = \u0026get_instance();
        $ci->load->database();
        $this->cache = $ci->db;
    }

    /**
     * Verifica se a requisição está dentro do limite
     */
    public function check(string $identifier, int $maxRequests = null, int $window = null): array
    {
        $maxRequests = $maxRequests ?? $this->defaultLimit;
        $window = $window ?? $this->defaultWindow;

        $key = "rate_limit:{$identifier}";
        $now = time();
        $windowStart = $now - $window;

        // Usa tabela de rate limiting ou cache em arquivo
        $attempts = $this->getAttempts($key, $windowStart);

        if ($attempts >= $maxRequests) {
            $resetTime = $this->getResetTime($key);

            return [
                'allowed' => false,
                'limit' => $maxRequests,
                'remaining' => 0,
                'reset_at' => $resetTime,
                'retry_after' => max(0, $resetTime - $now)
            ];
        }

        // Incrementa contador
        $this->incrementAttempt($key, $now, $window);

        return [
            'allowed' => true,
            'limit' => $maxRequests,
            'remaining' => $maxRequests - $attempts - 1,
            'reset_at' => $now + $window
        ];
    }

    /**
     * Obtém número de tentativas
     */
    private function getAttempts(string $key, int $windowStart): int
    {
        // Tenta usar cache em arquivo primeiro
        $cacheFile = APPPATH . "cache/ratelimit_{$key}.cache";

        if (file_exists($cacheFile)) {
            $data = unserialize(file_get_contents($cacheFile));
            if ($data['expires'] > time()) {
                return $data['count'];
            }
        }

        return 0;
    }

    /**
     * Incrementa tentativa
     */
    private function incrementAttempt(string $key, int $timestamp, int $window): void
    {
        $cacheFile = APPPATH . "cache/ratelimit_{$key}.cache";
        $count = 1;

        if (file_exists($cacheFile)) {
            $data = unserialize(file_get_contents($cacheFile));
            if ($data['expires'] > time()) {
                $count = $data['count'] + 1;
            }
        }

        $data = [
            'count' => $count,
            'expires' => $timestamp + $window
        ];

        file_put_contents($cacheFile, serialize($data));
    }

    /**
     * Obtém tempo de reset
     */
    private function getResetTime(string $key): int
    {
        $cacheFile = APPPATH . "cache/ratelimit_{$key}.cache";

        if (file_exists($cacheFile)) {
            $data = unserialize(file_get_contents($cacheFile));
            return $data['expires'];
        }

        return time();
    }

    /**
     * Limpa rate limit para um identificador
     */
    public function reset(string $identifier): bool
    {
        $key = "rate_limit:{$identifier}";
        $cacheFile = APPPATH . "cache/ratelimit_{$key}.cache";

        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }

        return true;
    }

    /**
     * Aplica headers de rate limit na resposta
     */
    public function applyHeaders(array $limitData): void
    {
        header('X-RateLimit-Limit: ' . $limitData['limit']);
        header('X-RateLimit-Remaining: ' . $limitData['remaining']);
        header('X-RateLimit-Reset: ' . $limitData['reset_at']);

        if (!$limitData['allowed']) {
            header('Retry-After: ' . $limitData['retry_after']);
            http_response_code(429);
        }
    }

    /**
     * Middleware para verificar rate limit
     */
    public function middleware(string $identifier, int $maxRequests = null, int $window = null): bool
    {
        $result = $this->check($identifier, $maxRequests, $window);
        $this->applyHeaders($result);

        return $result['allowed'];
    }
}
