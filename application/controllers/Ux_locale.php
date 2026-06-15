<?php
/**
 * Controller: Ux_locale
 * Troca o idioma do sistema (F3.7).
 *
 * Endpoint:
 *   GET  /ux_locale/listar        -> idiomas disponiveis
 *   POST /ux_locale/setar         body: {locale: 'en-US'}
 *   GET  /ux_locale/strings       -> strings do locale atual (para o JS consumir)
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ux_locale extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listar()
    {
        $available = $this->_availableLocales();
        $current = current_locale();
        return $this->json_success(['current' => $current, 'available' => $available]);
    }

    public function setar()
    {
        $locale = $this->input->post('locale', true);
        if (!$locale || !in_array($locale, $this->_availableLocales(true), true)) {
            return $this->json_error('Locale inválido', 400);
        }
        $this->session->set_userdata('app_locale', $locale);
        return $this->json_success(['locale' => $locale]);
    }

    public function strings()
    {
        $locale = current_locale();
        return $this->json_success([
            'locale'  => $locale,
            'strings' => load_locale_strings($locale),
        ]);
    }

    private function _availableLocales(bool $onlyKeys = false): array
    {
        $dir = APPPATH . 'language';
        if (!is_dir($dir)) return [];
        $out = [];
        foreach (scandir($dir) as $f) {
            if (substr($f, -5) === '.json') {
                $key = substr($f, 0, -5);
                if ($onlyKeys) { $out[] = $key; continue; }
                $raw = @file_get_contents($dir . DIRECTORY_SEPARATOR . $f);
                $data = $raw !== false ? json_decode($raw, true) : null;
                $out[] = [
                    'locale'  => $key,
                    'name'    => is_array($data) ? ($data['_meta']['name'] ?? $key) : $key,
                    'english' => is_array($data) ? ($data['_meta']['english_name'] ?? '') : '',
                    'rtl'     => is_array($data) ? (bool) ($data['_meta']['rtl'] ?? false) : false,
                ];
            }
        }
        return $out;
    }

    private function json_success($d, $c = 200) { return json_success($d, $c); }
    private function json_error($m, $c = 400)   { return json_error($m, $c); }
}
