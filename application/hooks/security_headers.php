<?php
/**
 * Security Headers Hook
 * Aplica headers de segurança em todas as requisições
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function apply_security_headers()
{
    // Prevenir clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Prevenir MIME sniffing
    header('X-Content-Type-Options: nosniff');

    // Proteção XSS (legado)
    header('X-XSS-Protection: 1; mode=block');

    // Controlar referrer
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions Policy
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // HSTS em produção
    if (ENVIRONMENT === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Remover headers que identificam o servidor
    header_remove('X-Powered-By');
    header_remove('Server');
}
