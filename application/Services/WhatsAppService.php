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
    protected $apiVersion = 'v1'; // v1 = Node, v2 = Go
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
        $this->detectarVersao();
    }

    private function addDebug($tipo, $msg)
    {
        $this->debugLog[] = ['tipo' => $tipo, 'msg' => $msg];
    }

    /**
     * Resolve URL da API. Usa URL interna configurada manualmente se existir,
     * caso contrario mantem a URL externa.
     */
    private function resolveApiUrl($url)
    {
        if (empty($url)) {
            return $url;
        }

        // Se tiver URL interna configurada explicitamente, usa ela
        if (!empty($this->config->evolution_url_interna)) {
            $internal = rtrim($this->config->evolution_url_interna, '/');
            $this->addDebug('info', 'Usando URL interna configurada: ' . $internal);
            return $internal;
        }

        return rtrim($url, '/');
    }

    /**
     * Detecta automaticamente a versao da Evolution API (v1 Node vs v2 Go)
     */
    private function detectarVersao()
    {
        // Se ja tiver versao configurada no banco, usa ela
        $versaoConfig = $this->config->evolution_version ?? 'v2';
        if ($versaoConfig === 'go' || $versaoConfig === 'v2') {
            $this->apiVersion = 'v2';
        } else {
            $this->apiVersion = 'v1';
        }

        $this->addDebug('info', 'Versao Evolution API detectada/configurada: ' . $this->apiVersion);
    }

    /**
     * Retorna o endpoint correto conforme a versao da API
     */
    private function endpoint($tipo, $instance = null)
    {
        $base = $this->apiUrl;
        $inst = $instance ?: ($this->config->evolution_instance ?? '');

        if ($this->apiVersion === 'v2') {
            switch ($tipo) {
                case 'instance_all':
                    return $base . '/instance/fetchInstances';
                case 'instance_status':
                    return $base . '/instance/connectionState/' . urlencode($inst);
                case 'instance_connect':
                    return $base . '/instance/connect/' . urlencode($inst);
                case 'instance_qr':
                    return $base . '/instance/connect/' . urlencode($inst);
                case 'instance_disconnect':
                    return $base . '/instance/logout/' . urlencode($inst);
                case 'send_text':
                    return $base . '/message/sendText/' . urlencode($inst);
                default:
                    return $base . '/' . $tipo;
            }
        }

        // v1 (Node)
        switch ($tipo) {
            case 'instance_all':
                return $base . '/instance/all';
            case 'instance_status':
                return $base . '/instance/status';
            case 'instance_connect':
                return $base . '/instance/connect';
            case 'instance_qr':
                return $base . '/instance/qr?instanceId=' . urlencode($inst);
            case 'instance_disconnect':
                return $base . '/instance/disconnect';
            case 'send_text':
                return $base . '/send/text';
            default:
                return $base . '/' . $tipo;
        }
    }

    /**
     * Faz requisicao HTTP com headers de navegador real para evitar bloqueios.
     */
    private function request($url, $method = 'GET', $data = [], $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HEADER, true);

        // Headers padrao que imitam um navegador real (evita bloqueio Cloudflare/Nginx)
        $defaultHeaders = [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
        ];

        $allHeaders = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        $respHeaders = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        return [
            'body' => $body,
            'headers' => $respHeaders,
            'http_code' => $httpCode,
            'error' => $error,
            'final_url' => $finalUrl,
        ];
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

        $url = $this->endpoint('instance_all');
        $this->addDebug('info', 'GET ' . $url);
        $resp = $this->request($url, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);
        $this->addDebug('info', 'HTTP /instance/all: ' . $resp['http_code']);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            // v1: data[], v2 Go: instances[] ou data[]
            $instances = $data['data'] ?? $data['instances'] ?? [];

            if (empty($instances)) {
                return ['connected' => false, 'status' => 'sem_instancias', 'error' => 'Nenhuma instancia encontrada no servidor', 'debug' => $this->debugLog];
            }

            foreach ($instances as $inst) {
                // v1: name/connected, v2: instanceId/name/status
                $name = $inst['name'] ?? $inst['instanceId'] ?? $inst['instanceName'] ?? '';
                $isConnected = false;
                if ($this->apiVersion === 'v2') {
                    $status = $inst['status'] ?? $inst['connectionStatus'] ?? $inst['state'] ?? '';
                    $isConnected = in_array(strtolower($status), ['connected', 'open', 'conectado'], true);
                } else {
                    $isConnected = !empty($inst['connected']);
                }
                if (strcasecmp($name, $this->config->evolution_instance) === 0) {
                    $this->CI->notificacoes_config_model->atualizarEstadoEvolution($isConnected ? 'open' : 'desconectado');
                    $this->addDebug('ok', 'Instancia encontrada. Connected=' . ($isConnected ? 'true' : 'false'));
                    return [
                        'connected' => $isConnected,
                        'status' => $isConnected ? 'open' : 'desconectado',
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
        $urlAll = $this->endpoint('instance_all');
        $resp = $this->request($urlAll, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            $instances = $data['data'] ?? $data['instances'] ?? [];
            foreach ($instances as $inst) {
                $name = $inst['name'] ?? $inst['instanceId'] ?? $inst['instanceName'] ?? '';
                if (strcasecmp($name, $this->config->evolution_instance) === 0) {
                    // v1: token, v2: apikey ou token
                    $token = $inst['token'] ?? $inst['apikey'] ?? $inst['instanceApikey'] ?? null;
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
        $urlQr = $this->endpoint('instance_qr');
        $methodQr = $this->apiVersion === 'v2' ? 'POST' : 'GET';
        $this->addDebug('url', $methodQr . ' ' . $urlQr);
        $respQr = $this->request($urlQr, $methodQr, [], ['apikey: ' . $instanceToken]);
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

        $url = $this->endpoint('send_text');
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

        $url = $this->endpoint('instance_disconnect');
        $this->addDebug('info', 'POST ' . $url);
        $payloadDisconnect = $this->apiVersion === 'v2' ? [] : ['instanceId' => $this->config->evolution_instance];
        $resp = $this->request($url, 'POST', $payloadDisconnect, [
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

        // 2. Teste /instance/all ou /instance/fetchInstances
        $urlAll = $this->endpoint('instance_all');
        $respAll = $this->request($urlAll, 'GET', [], ['apikey: ' . ($this->config->evolution_apikey ?? '')]);
        $resultado['teste_instance_all'] = [
            'http_code' => $respAll['http_code'],
            'ok' => $respAll['http_code'] === 200,
        ];

        if ($respAll['http_code'] === 200) {
            $data = json_decode($respAll['body'], true);
            $instancias = $data['data'] ?? $data['instances'] ?? [];
            $resultado['instancias_encontradas'] = count($instancias);

            foreach ($instancias as $inst) {
                $name = $inst['name'] ?? $inst['instanceId'] ?? $inst['instanceName'] ?? '';
                $connected = false;
                if ($this->apiVersion === 'v2') {
                    $status = $inst['status'] ?? $inst['connectionStatus'] ?? $inst['state'] ?? '';
                    $connected = in_array(strtolower($status), ['connected', 'open', 'conectado'], true);
                } else {
                    $connected = !empty($inst['connected']);
                }
                if (strcasecmp($name, $this->config->evolution_instance ?? '') === 0) {
                    $resultado['instancia'] = [
                        'nome' => $name,
                        'connected' => $connected,
                        'token' => !empty($inst['token'] ?? $inst['apikey'] ?? null) ? substr($inst['token'] ?? $inst['apikey'], 0, 8) . '...' : 'N/A',
                    ];
                }
            }
        }

        // 3. Teste /instance/status (se tiver token)
        $token = $this->getInstanceToken();
        if ($token) {
            $urlStatus = $this->endpoint('instance_status');
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
