<?php
/**
 * Helper: __() — Internacionalizacao (F3.7 do Plano UX)
 *
 * Uso:
 *   echo __('Save');          // -> "Salvar" (se locale=pt-BR)
 *   echo __('Hello :name', [':name' => $user->name]);
 *
 * Locale e determinado por:
 *   1) Variavel de sessao 'app_locale' (se setada)
 *   2) Configuracao 'app_locale' (banco)
 *   3) Constante APP_LOCALE (se definida)
 *   4) Fallback: 'pt-BR'
 *
 * Arquivos de traducao: application/language/{locale}.json
 * Estrutura: { "Save": "Salvar", "Hello :name": "Ola :name", ... }
 *
 * Para adicionar novo idioma:
 *   1) Criar application/language/en-US.json com as strings
 *   2) Trocar o locale via endpoint /ux_locale/setar
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('current_locale')) {
    function current_locale(): string
    {
        $ci = &get_instance();
        // 1) Sessao
        $sess = $ci->session->userdata('app_locale');
        if (!empty($sess)) return $sess;
        // 2) Config do banco (cache em data->configuration)
        if (isset($ci->data['configuration']['app_locale']) && !empty($ci->data['configuration']['app_locale'])) {
            return $ci->data['configuration']['app_locale'];
        }
        // 3) APP_LOCALE
        if (defined('APP_LOCALE')) return APP_LOCALE;
        // 4) Fallback
        return 'pt-BR';
    }
}

if (!function_exists('load_locale_strings')) {
    function load_locale_strings(string $locale): array
    {
        static $cache = [];
        if (isset($cache[$locale])) return $cache[$locale];
        $path = APPPATH . 'language/' . $locale . '.json';
        if (!is_file($path)) {
            $cache[$locale] = [];
            return $cache[$locale];
        }
        $raw = @file_get_contents($path);
        $decoded = $raw !== false ? json_decode($raw, true) : null;
        $cache[$locale] = is_array($decoded) ? $decoded : [];
        return $cache[$locale];
    }
}

if (!function_exists('__')) {
    /**
     * Traduz uma string. Se a string nao existir no locale atual, retorna a propria string.
     * Suporta placeholders :nome, :valor, etc.
     */
    function __(string $key, array $replace = []): string
    {
        $locale = current_locale();
        $strings = load_locale_strings($locale);
        $str = $strings[$key] ?? $key;
        if (!empty($replace)) {
            $str = strtr($str, $replace);
        }
        return $str;
    }
}

if (!function_exists('_e')) {
    /**
     * Traduz + escapa HTML.
     */
    function _e(string $key, array $replace = []): string
    {
        return htmlspecialchars(__($key, $replace), ENT_QUOTES, 'UTF-8');
    }
}
