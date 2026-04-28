<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Serviço de integração com WhatsApp
 * Suporta Evolution API, Meta API e Z-API
 */
class WhatsAppService
{
    protected $CI;
    protected $config;
    protected $provedor;
    public $debugLog = [];

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('notificacoes_config_model');
        $this->config = $this->CI->notificacoes_config_model->getConfig();
        $this->provedor = $this->config ? $this->config->whatsapp_provedor : 'desativado';

        if ($this->config && !empty($this->config->evolution_url)) {
            $url = rtrim($this->config->evolution_url, '/');
            $url = preg_replace('#/swagger(/index\.html)?$#', '', $url);
            $this->config->evolution_url = rtrim($url, '/');
        }
    }

    /**
     * Retorna true se está usando Evolution Go (SaaS)
     */
    private function isEvolutionGo()
    {
        return ($this->config->evolution_version ?? 'v2') === 'go';
    }

    /**
     * Envia mensagem de texto
     */
    public function enviarMensagem($numero, $mensagem, $options = [])
    {
        if ($this->provedor == 'desativado') {
            return [
                'success' => false,
                'error' => 'WhatsApp desativado nas configurações'
            ];
        }

        $numero = $this->limparNumero($numero);

        if (!$this->validarNumero($numero)) {
            return [
                'success' => false,
                'error' => 'Número de telefone inválido'
            ];
        }

        switch ($this->provedor) {
            case 'evolution':
                return $this->enviarEvolution($numero, $mensagem, $options);
            case 'meta_api':
                return $this->enviarMetaAPI($numero, $mensagem, $options);
            case 'z_api':
                return $this->enviarZAPI($numero, $mensagem, $options);
            default:
                return [
                    'success' => false,
                    'error' => 'Provedor não suportado: ' . $this->provedor
                ];
        }
    }

    /**
     * Verifica se está conectado (para Evolution)
     */
    public function verificarConexao()
    {
        if ($this->provedor == 'desativado') {
            return ['connected' => false, 'status' => 'desativado'];
        }

        if ($this->provedor == 'evolution') {
            return $this->verificarConexaoEvolution();
        }

        if ($this->provedor == 'meta_api') {
            return $this->verificarConexaoMeta();
        }

        return ['connected' => false, 'status' => 'provedor_nao_suportado'];
    }

    /**
     * Obtém QR Code para conexão (Evolution)
     */
    public function obterQRCode()
    {
        if ($this->provedor != 'evolution') {
            return ['success' => false, 'error' => 'QR Code disponível apenas para Evolution API'];
        }

        return $this->obterQRCodeEvolution();
    }

    /**
     * Desconecta a instância (Evolution)
     */
    public function desconectar()
    {
        if ($this->provedor != 'evolution') {
            return ['success' => false, 'error' => 'Operação disponível apenas para Evolution API'];
        }

        return $this->desconectarEvolution();
    }

    /**
     * Adiciona log de debug
     */
    private function addDebug($tipo, $msg)
    {
        $this->debugLog[] = ['tipo' => $tipo, 'msg' => $msg];
        log_message('debug', '[Evolution] ' . $msg);
    }

    /**
     * Arquivo de cache para token da instância
     */
    private function getTokenCacheFile()
    {
        $key = md5(($this->config->evolution_url ?? '') . '_' . ($this->config->evolution_instance ?? ''));
        return APPPATH . 'cache/evolution_token_' . $key . '.json';
    }

    /**
     * Lê token do cache de arquivo
     */
    private function lerTokenCache()
    {
        $file = $this->getTokenCacheFile();
        if (file_exists($file)) {
            $data = json_decode(@file_get_contents($file), true);
            if ($data && ($data['expires'] ?? 0) > time()) {
                $this->addDebug('info', 'Token lido do cache de arquivo');
                return $data['token'] ?? null;
            }
        }
        return null;
    }

    /**
     * Salva token no cache de arquivo
     */
    private function salvarTokenCache($token)
    {
        $file = $this->getTokenCacheFile();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($file, json_encode([
            'token' => $token,
            'expires' => time() + 86400
        ]));
    }

    /**
     * Resolve o token específico da instância via /instance/all
     */
    private function resolveInstanceToken()
    {
        $this->debugLog = [];

        // Validações básicas
        if (empty($this->config->evolution_url)) {
            $this->addDebug('erro', 'URL do Evolution NÃO configurada. Vá em Configurações > Notificações e salve.');
            return null;
        }
        if (empty($this->config->evolution_apikey)) {
            $this->addDebug('erro', 'API Key do Evolution NÃO configurada');
            return null;
        }
        if (empty($this->config->evolution_instance)) {
            $this->addDebug('erro', 'Nome da instância NÃO configurado');
            return null;
        }

        // 1. Cache
        $cached = $this->lerTokenCache();
        if ($cached) {
            return $cached;
        }

        // 2. Resolve via /instance/all
        $url = rtrim($this->config->evolution_url, '/') . '/instance/all';
        $headers = [
            'Content-Type: application/json',
            'apikey: ' . $this->config->evolution_apikey
        ];

        $this->addDebug('url', 'GET ' . $url);
        $this->addDebug('info', 'Instância procurada: "' . $this->config->evolution_instance . '"');

        // Resolve IP para debug
        $host = parse_url($url, PHP_URL_HOST);
        if ($host) {
            $ip = @gethostbyname($host);
            $this->addDebug('info', 'DNS: ' . $host . ' -> ' . ($ip !== $host ? $ip : 'FALHA ao resolver'));
        }

        $response = $this->makeRequest($url, 'GET', [], $headers);
        $this->addDebug('info', 'HTTP Resposta: ' . $response['http_code']);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $instances = $data['data'] ?? [];
            $this->addDebug('info', 'Total de instâncias retornadas: ' . count($instances));

            foreach ($instances as $inst) {
                $instName = $inst['name'] ?? '';
                if (strcasecmp($instName, $this->config->evolution_instance) === 0) {
                    $token = $inst['token'] ?? null;
                    if ($token) {
                        $this->addDebug('sucesso', 'Token resolvido para instância "' . $instName . '"');
                        $this->salvarTokenCache($token);
                        return $token;
                    }
                }
            }
            $this->addDebug('erro', 'Instância "' . $this->config->evolution_instance . '" NÃO encontrada');
        } else {
            $this->addDebug('erro', 'Falha HTTP ' . $response['http_code']);
            $this->addDebug('erro', 'Body: ' . substr($response['body'] ?? '', 0, 300));
        }

        return null;
    }

    /**
     * Envia mensagem via Evolution API
     */
    private function enviarEvolution($numero, $mensagem, $options = [])
    {
        $instanceToken = $this->resolveInstanceToken();
        if (!$instanceToken) {
            return [
                'success' => false,
                'error' => 'Instância não encontrada. Verifique a configuração.',
                'debug' => $this->debugLog
            ];
        }

        $url = rtrim($this->config->evolution_url, '/') . '/send/text';
        $payload = [
            'number' => $numero,
            'text' => $mensagem,
            'delay' => 1200,
        ];

        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $instanceToken
        ];

        $response = $this->makeRequest($url, 'POST', $payload, $headers);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            return [
                'success' => true,
                'message_id' => $data['data']['key']['id'] ?? $data['key']['id'] ?? null,
                'timestamp' => $data['data']['messageTimestamp'] ?? $data['messageTimestamp'] ?? time(),
                'response' => $data
            ];
        }

        return [
            'success' => false,
            'error' => $this->extrairErro($response),
            'http_code' => $response['http_code']
        ];
    }

    /**
     * Envia mensagem via Meta API (WhatsApp Business)
     */
    private function enviarMetaAPI($numero, $mensagem, $options = [])
    {
        $url = "https://graph.facebook.com/v18.0/{$this->config->meta_phone_number_id}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $numero,
            'type' => 'text',
            'text' => ['body' => $mensagem]
        ];

        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $this->config->meta_access_token
        ];

        $response = $this->makeRequest($url, 'POST', $payload, $headers);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            return [
                'success' => true,
                'message_id' => $data['messages'][0]['id'] ?? null,
                'response' => $data
            ];
        }

        return [
            'success' => false,
            'error' => $this->extrairErro($response),
            'http_code' => $response['http_code']
        ];
    }

    /**
     * Envia mensagem via Z-API
     */
    private function enviarZAPI($numero, $mensagem, $options = [])
    {
        $url = rtrim($this->config->z_api_url, '/') . '/send-text';

        $payload = [
            'phone' => $numero,
            'message' => $mensagem
        ];

        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $this->config->z_api_token
        ];

        $response = $this->makeRequest($url, 'POST', $payload, $headers);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            return [
                'success' => true,
                'message_id' => $data['messageId'] ?? $data['id'] ?? null,
                'response' => $data
            ];
        }

        return [
            'success' => false,
            'error' => $this->extrairErro($response),
            'http_code' => $response['http_code']
        ];
    }

    /**
     * Verifica conexão Evolution
     */
    private function verificarConexaoEvolution()
    {
        $this->debugLog = [];

        if (empty($this->config->evolution_url)) {
            return [
                'connected' => false,
                'status' => 'nao_configurado',
                'error' => 'URL do Evolution não configurada'
            ];
        }

        $url = rtrim($this->config->evolution_url, '/') . '/instance/all';
        $headers = [
            'Content-Type: application/json',
            'apikey: ' . $this->config->evolution_apikey
        ];

        $response = $this->makeRequest($url, 'GET', [], $headers);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $instances = $data['data'] ?? [];

            foreach ($instances as $inst) {
                $instName = $inst['name'] ?? '';
                if (strcasecmp($instName, $this->config->evolution_instance) === 0) {
                    $connected = $inst['connected'] ?? false;
                    $estado = $connected ? 'open' : 'desconectado';

                    if (!empty($inst['token'])) {
                        $this->salvarTokenCache($inst['token']);
                    }

                    $this->CI->notificacoes_config_model->atualizarEstadoEvolution($estado);

                    return [
                        'connected' => $connected,
                        'status' => $estado,
                        'data' => $inst,
                        'debug' => $this->debugLog
                    ];
                }
            }

            return [
                'connected' => false,
                'status' => 'instancia_nao_encontrada',
                'error' => 'Instância não encontrada',
                'debug' => $this->debugLog
            ];
        }

        return [
            'connected' => false,
            'status' => 'erro',
            'error' => 'HTTP ' . $response['http_code'],
            'debug' => $this->debugLog
        ];
    }

    /**
     * Obtém QR Code Evolution
     */
    private function obterQRCodeEvolution()
    {
        $this->debugLog = [];
        $this->addDebug('info', '=== Iniciando obtenção de QR Code ===');

        $conexao = $this->verificarConexaoEvolution();
        if ($conexao['connected']) {
            $this->addDebug('warn', 'Já está conectado. Abortando QR Code.');
            return [
                'success' => false,
                'error' => 'Já está conectado',
                'already_connected' => true,
                'debug' => $this->debugLog
            ];
        }

        $this->addDebug('info', 'Instância desconectada. Tentando criar...');
        $criar = $this->criarInstanciaEvolution();
        $this->addDebug('info', 'Criar instância: HTTP ' . $criar['http_code']);

        // Aguarda um momento para a instância ser criada no backend
        if ($criar['http_code'] == 200 || $criar['http_code'] == 201) {
            sleep(2);
        }

        $instanceToken = $this->resolveInstanceToken();
        if (!$instanceToken) {
            $this->addDebug('erro', 'FALHA: Não foi possível resolver o token da instância');
            return [
                'success' => false,
                'error' => 'Instância não encontrada no servidor após criação.',
                'debug' => $this->debugLog
            ];
        }

        $url = rtrim($this->config->evolution_url, '/') . '/instance/qr?instanceId=' . urlencode($this->config->evolution_instance);

        $this->addDebug('url', 'GET ' . $url);
        $this->addDebug('info', 'Token usado: ***' . substr($instanceToken, -4));

        $headers = [
            'apikey: ' . $instanceToken
        ];

        $response = $this->makeRequest($url, 'GET', [], $headers);
        $this->addDebug('info', 'QR Code HTTP: ' . $response['http_code']);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $qrData = $data['data'] ?? $data;
            $qrCode = $qrData['Qrcode'] ?? $qrData['qrcode'] ?? $qrData['base64'] ?? null;

            if ($qrCode) {
                $this->addDebug('sucesso', 'QR Code obtido!');
            } else {
                $this->addDebug('warn', 'QR Code não presente na resposta');
            }

            return [
                'success' => true,
                'qr_code' => $qrCode,
                'pairing_code' => $qrData['Code'] ?? $qrData['code'] ?? $qrData['pairingCode'] ?? null,
                'count' => $qrData['count'] ?? 0,
                'debug' => $this->debugLog
            ];
        }

        $this->addDebug('erro', 'Falha HTTP ' . $response['http_code'] . ': ' . $this->extrairErro($response));

        return [
            'success' => false,
            'error' => $this->extrairErro($response),
            'http_code' => $response['http_code'],
            'debug' => $this->debugLog
        ];
    }

    /**
     * Cria instância Evolution se não existir
     */
    private function criarInstanciaEvolution()
    {
        $url = rtrim($this->config->evolution_url, '/') . '/instance/create';

        $payload = [
            'name' => $this->config->evolution_instance,
            'token' => $this->config->evolution_apikey,
            'integration' => 'WHATSAPP-BAILEYS',
            'qrcode' => true,
        ];

        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $this->config->evolution_apikey
        ];

        $response = $this->makeRequest($url, 'POST', $payload, $headers);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $token = $data['data']['token'] ?? $data['token'] ?? null;
            if ($token) {
                $this->salvarTokenCache($token);
            }
        }

        return $response;
    }

    /**
     * Desconecta instância Evolution
     */
    private function desconectarEvolution()
    {
        $instanceToken = $this->resolveInstanceToken();
        if (!$instanceToken) {
            return [
                'success' => false,
                'error' => 'Instância não encontrada',
                'debug' => $this->debugLog
            ];
        }

        $url = rtrim($this->config->evolution_url, '/') . '/instance/disconnect';
        $method = 'POST';

        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $instanceToken
        ];

        $payload = $method === 'POST' ? ['instanceId' => $this->config->evolution_instance] : [];
        $response = $this->makeRequest($url, $method, $payload, $headers);

        if (in_array($response['http_code'], [200, 201])) {
            $this->CI->notificacoes_config_model->atualizarEstadoEvolution('desconectado');
            return ['success' => true, 'debug' => $this->debugLog];
        }

        return [
            'success' => false,
            'error' => $this->extrairErro($response),
            'debug' => $this->debugLog
        ];
    }

    /**
     * Verifica conexão Meta API
     */
    private function verificarConexaoMeta()
    {
        $url = "https://graph.facebook.com/v18.0/{$this->config->meta_phone_number_id}?fields=verified_name,quality_score";

        $headers = [
            'Authorization: Bearer ' . $this->config->meta_access_token
        ];

        $response = $this->makeRequest($url, 'GET', [], $headers);

        return [
            'connected' => $response['http_code'] == 200,
            'status' => $response['http_code'] == 200 ? 'conectado' : 'erro',
            'response' => json_decode($response['body'], true)
        ];
    }

    /**
     * Faz requisição HTTP com retry e fallback
     */
    private function makeRequest($url, $method = 'GET', $data = [], $headers = [])
    {
        $tentativas = 0;
        $maxTentativas = 2;

        while ($tentativas < $maxTentativas) {
            $tentativas++;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            // User-Agent para passar pelo CloudFlare
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

            // Headers customizados
            $defaultHeaders = [
                'Accept: application/json',
                'Accept-Language: pt-BR,pt;q=0.9',
            ];
            $allHeaders = array_merge($defaultHeaders, $headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            } elseif ($method == 'DELETE') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            }

            // Log para debug
            if (!empty($this->debugLog)) {
                $this->addDebug('info', 'curl: ' . $method . ' ' . $url);
            }

            $body = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            if ($error) {
                log_message('error', '[Evolution] curl error: ' . $error);
                if (!empty($this->debugLog)) {
                    $this->addDebug('erro', 'curl error: ' . $error);
                }
            }

            // Se sucesso, retorna
            if ($httpCode == 200 || $httpCode == 201) {
                return [
                    'body' => $body,
                    'http_code' => $httpCode,
                    'error' => $error,
                    'info' => $info
                ];
            }

            // Se 404 na primeira tentativa, tenta com HTTP (sem S) no lugar de HTTPS
            if ($httpCode == 404 && $tentativas < $maxTentativas && strpos($url, 'https://') === 0) {
                $url = str_replace('https://', 'http://', $url);
                if (!empty($this->debugLog)) {
                    $this->addDebug('warn', 'HTTPS falhou com 404. Tentando HTTP...');
                }
                continue;
            }

            // Se erro DNS/connect, tenta novamente
            if (($httpCode == 0 || $httpCode >= 500) && $tentativas < $maxTentativas) {
                if (!empty($this->debugLog)) {
                    $this->addDebug('warn', 'Erro de conexão. Tentando novamente...');
                }
                sleep(1);
                continue;
            }

            return [
                'body' => $body,
                'http_code' => $httpCode,
                'error' => $error,
                'info' => $info
            ];
        }

        return [
            'body' => '',
            'http_code' => 0,
            'error' => 'Max retries exceeded',
            'info' => []
        ];
    }

    /**
     * Extrai mensagem de erro da resposta
     */
    private function extrairErro($response)
    {
        if (!empty($response['error'])) {
            return $response['error'];
        }

        $data = json_decode($response['body'], true);

        if (isset($data['message'])) {
            return $data['message'];
        }

        if (isset($data['error']) && is_array($data['error'])) {
            return $data['error']['message'] ?? json_encode($data['error']);
        }

        return 'Erro desconhecido (HTTP ' . $response['http_code'] . ')';
    }

    /**
     * Limpa número de telefone
     */
    private function limparNumero($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);

        if (strlen($numero) == 11 || strlen($numero) == 10) {
            $numero = '55' . $numero;
        }

        return $numero;
    }

    /**
     * Valida número de telefone
     */
    private function validarNumero($numero)
    {
        $len = strlen($numero);
        return $len >= 11 && $len <= 15;
    }

    /**
     * Formata número para exibição
     */
    public static function formatarNumero($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);

        if (strlen($numero) == 13 && substr($numero, 0, 2) == '55') {
            return '+55 (' . substr($numero, 2, 2) . ') ' . substr($numero, 4, 5) . '-' . substr($numero, 9, 4);
        }

        if (strlen($numero) == 11) {
            return '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 5) . '-' . substr($numero, 7, 4);
        }

        return $numero;
    }
}
