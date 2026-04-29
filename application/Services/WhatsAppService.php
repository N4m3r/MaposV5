<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Servico de integracao com WhatsApp (Evolution API - Go/SaaS)
 *
 * Endpoints testados e funcionais na Evolution Go:
 * - GET /instance/all           -> API Key global
 * - GET /instance/status        -> Token da instancia
 * - GET /instance/qr            -> Token da instancia (so quando desconectado)
 * - POST /instance/connect      -> Token da instancia
 * - POST /instance/disconnect   -> Token da instancia
 * - POST /send/text             -> Token da instancia
 */
class WhatsAppService
{
    protected $CI;
    protected $config;
    protected $apiUrl;
    public $debugLog = [];

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('notificacoes_config_model');
        $this->config = $this->CI->notificacoes_config_model->getConfig();

        if ($this->config && !empty($this->config->evolution_url)) {
            $url = rtrim($this->config->evolution_url, '/');
            $url = preg_replace('#/swagger(/index\.html)?$#', '', $url);
            $this->config->evolution_url = rtrim($url, '/');
        }

        $this->apiUrl = $this->resolveApiUrl($this->config->evolution_url ?? '');
    }

    private function addDebug($tipo, $msg)
    {
        $this->debugLog[] = ['tipo' => $tipo, 'msg' => $msg];
    }

    /**
     * Resolve URL interna da API quando servidor e Evolution estao no mesmo host.
     * Evita loopback pelo Cloudflare/Nginx.
     */
    private function resolveApiUrl($url)
    {
        if (empty($url)) {
            return $url;
        }

        $host = parse_url($url, PHP_URL_HOST);

        // Se tiver URL interna configurada explicitamente, usa ela
        if (!empty($this->config->evolution_url_interna)) {
            $internal = rtrim($this->config->evolution_url_interna, '/');
            $this->addDebug('info', 'Usando URL interna configurada: ' . $internal);
            return $internal;
        }

        // Fallback automatico para dominios conhecidos que rodam localmente
        $localHosts = ['evo.jj-ferreiras.com.br'];
        if (in_array($host, $localHosts, true)) {
            $localUrl = 'http://127.0.0.1:8091';
            $this->addDebug('info', 'Detectado host local (' . $host . '). Usando URL interna: ' . $localUrl);
            return $localUrl;
        }

        return rtrim($url, '/');
    }
    private function request($url, $method = 'GET', $data = [], $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_HEADER, true);

        $allHeaders = array_merge(['Accept: application/json'], $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $respHeaders = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        return ['body' => $body, 'headers' => $respHeaders, 'http_code' => $httpCode, 'error' => $error];
    }

    /**
     * Verifica conexao com o servidor Evolution
     */
    public function verificarConexao()
    {
        $this->debugLog = [];

        if (empty($this->config->evolution_url) || empty($this->config->evolution_apikey)) {
            return ['connected' => false, 'status' => 'nao_configurado', 'error' => 'URL ou API Key nao configuradas'];
        }

        $url = $this->apiUrl . '/instance/all';
        $this->addDebug('info', 'GET ' . $url);
        $resp = $this->request($url, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);
        $this->addDebug('info', 'HTTP /instance/all: ' . $resp['http_code']);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            $instances = $data['data'] ?? [];

            if (empty($instances)) {
                return ['connected' => false, 'status' => 'sem_instancias', 'error' => 'Nenhuma instancia encontrada no servidor', 'debug' => $this->debugLog];
            }

            foreach ($instances as $inst) {
                $name = $inst['name'] ?? '';
                if (strcasecmp($name, $this->config->evolution_instance) === 0) {
                    $connected = !empty($inst['connected']);
                    $this->CI->notificacoes_config_model->atualizarEstadoEvolution($connected ? 'open' : 'desconectado');
                    $this->addDebug('ok', 'Instancia encontrada. Connected=' . ($connected ? 'true' : 'false'));
                    return [
                        'connected' => $connected,
                        'status' => $connected ? 'open' : 'desconectado',
                        'data' => $inst,
                        'debug' => $this->debugLog,
                    ];
                }
            }
            return [
                'connected' => false,
                'status' => 'instancia_nao_encontrada',
                'error' => 'Instancia "' . $this->config->evolution_instance . '" nao existe no servidor',
                'debug' => $this->debugLog,
            ];
        }

        if ($resp['error']) {
            $this->addDebug('erro', 'CURL Error: ' . $resp['error']);
        }
        $this->addDebug('erro', 'HTTP ' . $resp['http_code']);
        $this->addDebug('info', 'Body: ' . substr($resp['body'], 0, 300));

        return [
            'connected' => false,
            'status' => 'erro',
            'error' => 'HTTP ' . $resp['http_code'] . ($resp['error'] ? ' | CURL: ' . $resp['error'] : ''),
            'debug' => $this->debugLog,
            'body_preview' => substr($resp['body'], 0, 300),
        ];
    }

    /**
     * Obtem o token da instancia. Prioridade:
     * 1. Token salvo no banco (evolution_instance_token)
     * 2. Busca via /instance/all
     */
    private function getInstanceToken()
    {
        // 1. Usa token salvo no banco
        if (!empty($this->config->evolution_instance_token)) {
            $this->addDebug('info', 'Usando token da instancia do banco');
            return $this->config->evolution_instance_token;
        }

        // 2. Busca via /instance/all
        $this->addDebug('info', 'Token nao salvo no banco. Buscando via /instance/all...');
        $urlAll = $this->apiUrl . '/instance/all';
        $resp = $this->request($urlAll, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            foreach ($data['data'] ?? [] as $inst) {
                if (strcasecmp($inst['name'] ?? '', $this->config->evolution_instance) === 0) {
                    $token = $inst['token'] ?? null;
                    if ($token) {
                        $this->addDebug('sucesso', 'Token resolvido via API');
                        // Salva no banco para proxima vez
                        $this->CI->notificacoes_config_model->atualizarInstanceToken($token);
                        return $token;
                    }
                }
            }
        }

        $this->addDebug('erro', 'Nao foi possivel obter token da instancia');
        return null;
    }

    /**
     * Obtem QR Code para conectar instancia
     */
    public function obterQRCode()
    {
        $this->debugLog = [];

        if (empty($this->config->evolution_url) || empty($this->config->evolution_apikey) || empty($this->config->evolution_instance)) {
            return ['success' => false, 'error' => 'Configure URL, API Key e Instancia primeiro'];
        }

        // 1. Verifica se ja esta conectado
        $status = $this->verificarConexao();
        if ($status['connected']) {
            return ['success' => false, 'error' => 'Instancia ja esta conectada', 'already_connected' => true];
        }

        // 2. Obtem token da instancia (do banco ou via API)
        $instanceToken = $this->getInstanceToken();
        if (!$instanceToken) {
            return ['success' => false, 'error' => 'Token da instancia nao encontrado. Clique em Diagnostico para verificar.', 'debug' => $this->debugLog];
        }

        // 3. Obtem QR Code usando o token da instancia
        $urlQr = $this->apiUrl . '/instance/qr?instanceId=' . urlencode($this->config->evolution_instance);
        $this->addDebug('url', 'GET ' . $urlQr);
        $respQr = $this->request($urlQr, 'GET', [], ['apikey: ' . $instanceToken]);
        $this->addDebug('info', 'HTTP /instance/qr: ' . $respQr['http_code']);

        if ($respQr['http_code'] === 200) {
            $qrData = json_decode($respQr['body'], true);
            $qrCode = $qrData['data']['Qrcode'] ?? $qrData['data']['qrcode'] ?? $qrData['Qrcode'] ?? $qrData['qrcode'] ?? null;
            $pairingCode = $qrData['data']['Code'] ?? $qrData['data']['code'] ?? null;

            if ($qrCode) {
                $this->addDebug('sucesso', 'QR Code obtido com sucesso!');
                return ['success' => true, 'qr_code' => $qrCode, 'pairing_code' => $pairingCode, 'debug' => $this->debugLog];
            }
            return ['success' => false, 'error' => 'QR Code nao presente na resposta da API', 'debug' => $this->debugLog];
        }

        // Se ja esta logado, retorna mensagem amigavel
        if ($respQr['http_code'] == 400 && strpos($respQr['body'], 'already logged in') !== false) {
            $this->addDebug('info', 'Sessao ja esta logada (sem QR Code necessario)');
            return ['success' => false, 'error' => 'Sessao ja esta logada', 'already_connected' => true, 'debug' => $this->debugLog];
        }

        $this->addDebug('erro', 'Falha ao obter QR Code: HTTP ' . $respQr['http_code']);
        $this->addDebug('info', 'Body: ' . substr($respQr['body'], 0, 300));
        return ['success' => false, 'error' => 'Erro ao obter QR Code (HTTP ' . $respQr['http_code'] . ')', 'debug' => $this->debugLog];
    }

    /**
     * Envia mensagem via Evolution
     */
    public function enviarMensagem($numero, $mensagem)
    {
        $this->debugLog = [];
        $numero = $this->limparNumero($numero);
        if (strlen($numero) < 11) {
            return ['success' => false, 'error' => 'Numero de telefone invalido'];
        }

        // Obtem token da instancia
        $instanceToken = $this->getInstanceToken();
        if (!$instanceToken) {
            return ['success' => false, 'error' => 'Nao foi possivel obter token da instancia'];
        }

        $url = $this->apiUrl . '/send/text';
        $payload = [
            'number' => $numero,
            'text' => $mensagem,
            'delay' => 1200,
        ];
        $this->addDebug('info', 'POST ' . $url);
        $this->addDebug('info', 'Payload: ' . json_encode($payload));
        $resp = $this->request($url, 'POST', $payload, [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $instanceToken,
        ]);
        $this->addDebug('info', 'HTTP /send/text: ' . $resp['http_code']);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            return ['success' => true, 'message_id' => $data['data']['key']['id'] ?? null, 'debug' => $this->debugLog];
        }

        // Se numero nao existe no WhatsApp
        if ($resp['http_code'] == 500 && strpos($resp['body'], 'not registered') !== false) {
            return ['success' => false, 'error' => 'Numero nao esta registrado no WhatsApp', 'debug' => $this->debugLog];
        }

        $this->addDebug('erro', 'Erro ao enviar: HTTP ' . $resp['http_code']);
        $this->addDebug('info', 'Body: ' . substr($resp['body'], 0, 300));
        return ['success' => false, 'error' => 'Erro ao enviar (HTTP ' . $resp['http_code'] . ')', 'debug' => $this->debugLog];
    }

    /**
     * Desconecta instancia
     */
    public function desconectar()
    {
        $this->debugLog = [];

        // Obtem token da instancia
        $instanceToken = $this->getInstanceToken();
        if (!$instanceToken) {
            return ['success' => false, 'error' => 'Nao foi possivel obter token da instancia'];
        }

        $url = $this->apiUrl . '/instance/disconnect';
        $this->addDebug('info', 'POST ' . $url);
        $resp = $this->request($url, 'POST', ['instanceId' => $this->config->evolution_instance], [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $instanceToken,
        ]);
        $this->addDebug('info', 'HTTP /instance/disconnect: ' . $resp['http_code']);

        if ($resp['http_code'] === 200 || $resp['http_code'] === 201) {
            $this->CI->notificacoes_config_model->atualizarEstadoEvolution('desconectado');
            return ['success' => true, 'debug' => $this->debugLog];
        }

        $this->addDebug('erro', 'Erro ao desconectar: HTTP ' . $resp['http_code']);
        $this->addDebug('info', 'Body: ' . substr($resp['body'], 0, 300));
        return ['success' => false, 'error' => 'Erro ao desconectar (HTTP ' . $resp['http_code'] . ')', 'debug' => $this->debugLog];
    }

    /**
     * Diagnostico completo da integracao
     */
    public function diagnostico()
    {
        $this->debugLog = [];
        $resultado = [];

        // 1. Configuracoes
        $resultado['config'] = [
            'url' => $this->config->evolution_url ?? 'N/A',
            'apikey' => !empty($this->config->evolution_apikey) ? substr($this->config->evolution_apikey, 0, 8) . '...' : 'N/A',
            'instance' => $this->config->evolution_instance ?? 'N/A',
            'instance_token' => !empty($this->config->evolution_instance_token) ? substr($this->config->evolution_instance_token, 0, 8) . '...' : 'N/A',
            'ativo' => $this->config->whatsapp_ativo ?? 'N/A',
            'provedor' => $this->config->whatsapp_provedor ?? 'N/A',
        ];

        // 2. Teste /instance/all
        $urlAll = $this->apiUrl . '/instance/all';
        $respAll = $this->request($urlAll, 'GET', [], ['apikey: ' . ($this->config->evolution_apikey ?? '')]);
        $resultado['teste_instance_all'] = [
            'http_code' => $respAll['http_code'],
            'ok' => $respAll['http_code'] === 200,
        ];

        if ($respAll['http_code'] === 200) {
            $data = json_decode($respAll['body'], true);
            $instancias = $data['data'] ?? [];
            $resultado['instancias_encontradas'] = count($instancias);

            foreach ($instancias as $inst) {
                if (strcasecmp($inst['name'] ?? '', $this->config->evolution_instance ?? '') === 0) {
                    $resultado['instancia'] = [
                        'nome' => $inst['name'],
                        'connected' => $inst['connected'] ?? false,
                        'token' => !empty($inst['token']) ? substr($inst['token'], 0, 8) . '...' : 'N/A',
                    ];
                }
            }
        }

        // 3. Teste /instance/status (se tiver token)
        $token = $this->getInstanceToken();
        if ($token) {
            $urlStatus = $this->apiUrl . '/instance/status';
            $respStatus = $this->request($urlStatus, 'GET', [], ['apikey: ' . $token]);
            $resultado['teste_instance_status'] = [
                'http_code' => $respStatus['http_code'],
                'ok' => $respStatus['http_code'] === 200,
            ];
            if ($respStatus['http_code'] === 200) {
                $resultado['status_dados'] = json_decode($respStatus['body'], true);
            }
        }

        return $resultado;
    }

    private function limparNumero($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($numero) == 11 || strlen($numero) == 10) {
            $numero = '55' . $numero;
        }
        return $numero;
    }

    /**
     * Retorna a URL de API resolvida (pode ser interna)
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Formata numero para exibicao
     */
    public static function formatarNumero($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($numero) == 13 && substr($numero, 0, 2) === '55') {
            return '(' . substr($numero, 2, 2) . ') ' . substr($numero, 4, 5) . '-' . substr($numero, 9);
        }
        if (strlen($numero) == 12 && substr($numero, 0, 2) === '55') {
            return '(' . substr($numero, 2, 2) . ') ' . substr($numero, 4, 4) . '-' . substr($numero, 8);
        }
        return $numero;
    }
}
