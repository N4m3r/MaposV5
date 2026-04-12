<?php
/**
 * Security Headers
 * Headers de segurança para proteção contra ataques comuns
 */

namespace Libraries\Security;

class SecurityHeaders
{
    private array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'x_frame_options' => 'SAMEORIGIN',
            'x_content_type_options' => 'nosniff',
            'x_xss_protection' => '1; mode=block',
            'referrer_policy' => 'strict-origin-when-cross-origin',
            'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
            'hsts_max_age' => 31536000,
            'hsts_include_subdomains' => true,
            'csp_enabled' => true
        ], $config);
    }

    /**
     * Aplica todos os headers de segurança
     */
    public function apply(): void
    {
        // Prevenir clickjacking
        header('X-Frame-Options: ' . $this->config['x_frame_options']);

        // Prevenir MIME sniffing
        header('X-Content-Type-Options: ' . $this->config['x_content_type_options']);

        // Proteção XSS (legado, mas ainda útil)
        header('X-XSS-Protection: ' . $this->config['x_xss_protection']);

        // Controlar referrer
        header('Referrer-Policy: ' . $this->config['referrer_policy']);

        // Permissions Policy
        header('Permissions-Policy: ' . $this->config['permissions_policy']);

        // HSTS em produção
        if (ENVIRONMENT === 'production' || ENVIRONMENT === 'prod') {
            $hsts = 'max-age=' . $this->config['hsts_max_age'];
            if ($this->config['hsts_include_subdomains']) {
                $hsts .= '; includeSubDomains';
            }
            header('Strict-Transport-Security: ' . $hsts);
        }

        // Content Security Policy
        if ($this->config['csp_enabled']) {
            $this->applyCSP();
        }
    }

    /**
     * Aplica Content Security Policy
     */
    private function applyCSP(): void
    {
        $nonce = bin2hex(random_bytes(16));
        define('CSP_NONCE', $nonce);

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net",
            "img-src 'self' data: blob:",
            "font-src 'self' cdn.jsdelivr.net",
            "connect-src 'self'",
            "frame-src 'none'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ];

        header('Content-Security-Policy: ' . implode('; ', $csp));
    }

    /**
     * Remove headers sensíveis
     */
    public function removeSensitiveHeaders(): void
    {
        header_remove('X-Powered-By');
        header_remove('Server');
    }

    /**
     * Gera nonce para scripts inline
     */
    public static function nonce(): string
    {
        return defined('CSP_NONCE') ? CSP_NONCE : bin2hex(random_bytes(16));
    }
}
