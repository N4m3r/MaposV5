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

        // Limpa o número
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
        log_message('debug', $msg);
    }

    /**
     * Arquivo de cache para token da instância
     */
    private function getTokenCacheFile()
    {
        return APPPATH . 'cache/evolution_token_' . md5($this->config->evolution_url . '_' . $this->config->evolution_instance) . '.json';
    }

    /**
     * Lê token do cache de arquivo
     */
    private function lerTokenCache()
    {
        $file = $this->getTokenCacheFile();
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if ($data && ($data['expires'] ?? 0) > time()) {
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
            'expires' => time() + 86400 // válido por 24h
        ]));
    }

    /**
     * Resolve o token específico da instância no Evolution Go
     * 1. Tenta usar token do cache de arquivo
     * 2. Tenta resolver via /instance/all
     * 3. Salva no cache quando resolve
     */
    private function resolveInstanceToken()
    {
        $this->debugLog = [];

        // Verifica se config básica existe
        if (empty($this->config->evolution_url)) {
            $this->addDebug('erro', 'URL do Evolution não configurada no banco');
            return null;
        }
        if (empty($this->config->evolution_apikey)) {
            $this->addDebug('erro', 'API Key do Evolution não configurada no banco');
            return null;
        }
        if (empty($this->config->evolution_instance)) {
            $this->addDebug('erro', 'Nome da instância não configurado no banco');
            return null;
        }

        // 1. Verifica se já tem token no cache
        $cachedToken = $this->lerTokenCache();
        if ($cachedToken) {
            $this->addDebug('sucesso', 'Usando token do cache de arquivo');
            return $cachedToken;
        }

        $this->addDebug('info', 'Nenhum token em cache. Resolvendo via API...');

        // 2. Resolve via API
        $url = rtrim($this->config->evolution_url, '/') . '/instance/all';
        $headers = [
            'apikey: ' . $this->config->evolution_apikey
        ];

        $this->addDebug('url', 'GET ' . $url);
        $this->addDebug('info', 'Headers: apikey=***' . substr($this->config->evolution_apikey, -4));
        $this->addDebug('info', 'Instância procurada: "' . $this->config->evolution_instance . '"');

        $response = $this->makeRequest($url, 'GET', [], $headers);
        $this->addDebug('info', 'HTTP Resposta: ' . $response['http_code']);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $instances = $data['data'] ?? [];
            $this->addDebug('info', 'Total de instâncias retornadas: ' . count($instances));

            foreach ($instances as $inst) {
                $instName = $inst['name'] ?? '';
                $this->addDebug('info', 'Comparando: "' . $instName . '" com "' . $this->config->evolution_instance . '"');
                // Comparação case-insensitive
                if (strcasecmp($instName, $this->config->evolution_instance) === 0) {
                    $token = $inst['token'] ?? null;
                    if ($token) {
                        $this->addDebug('sucesso', 'Token resolvido para instância "' . $instName . '"');
                        // Salva no cache de arquivo
                        $this->salvarTokenCache($token);
                        return $token;
                    }
                }
            }
            $this->addDebug('erro', 'Instância "' . $this->config->evolution_instance . '" NÃO encontrada em ' . count($instances) . ' instâncias');
        } else {
            $this->addDebug('erro', 'Falha ao listar instâncias. HTTP: ' . $response['http_code']);
            $this->addDebug('erro', 'Body: ' . substr($response['body'] ?? '', 0, 500));
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
                'error' => 'Instância não encontrada no servidor. Verifique se a instância "' . $this->config->evolution_instance . '" existe.'
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

        log_message('debug', '[Evolution] Enviando mensagem para: ' . $numero . ' | URL: ' . $url);
        $response = $this->makeRequest($url, 'POST', $payload, $headers);
        log_message('debug', '[Evolution] Resposta envio HTTP: ' . $response['http_code'] . ' | Body: ' . substr($response['body'] ?? '', 0, 500));

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
            'http_code' => $response['http_code'],
            'response' => $response['body']
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
            'text' => [
                'body' => $mensagem
            ]
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
            'http_code' => $response['http_code'],
            'response' => $response['body']
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
        $url = rtrim($this->config->evolution_url, '/') . '/instance/all';
        $headers = [
            'apikey: ' . $this->config->evolution_apikey
        ];

        $this->addDebug('url', 'GET ' . $url);
        $this->addDebug('info', 'Procurando instância: "' . $this->config->evolution_instance . '"');

        $response = $this->makeRequest($url, 'GET', [], $headers);
        $this->addDebug('info', 'HTTP Resposta: ' . $response['http_code']);

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $instances = $data['data'] ?? [];

            foreach ($instances as $inst) {
                $instName = $inst['name'] ?? '';
                if (strcasecmp($instName, $this->config->evolution_instance) === 0) {
                    $connected = $inst['connected'] ?? false;
                    $estado = $connected ? 'open' : 'desconectado';

                    $this->addDebug('sucesso', 'Instância "' . $instName . '" encontrada. Connected=' . ($connected ? 'true' : 'false'));

                    if (!empty($inst['token'])) {
                        $this->addDebug('info', 'Token salvo no cache');
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

            $this->addDebug('erro', 'Instância "' . $this->config->evolution_instance . '" NÃO encontrada entre ' . count($instances) . ' instâncias');
            return [
                'connected' => false,
                'status' => 'instancia_nao_encontrada',
                'error' => 'Instância não encontrada no servidor',
                'debug' => $this->debugLog
            ];
        }

        $this->addDebug('erro', 'Falha HTTP ' . $response['http_code'] . ': ' . $this->extrairErro($response));

        return [
            'connected' => false,
            'status' => 'erro',
            'error' => $this->extrairErro($response),
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

        // Primeiro verifica se já está conectado
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

        // Cria a instância se não existir
        $this->addDebug('info', 'Instância desconectada. Tentando criar...');
        $criar = $this->criarInstanciaEvolution();
        $this->addDebug('info', 'Criar instância: HTTP ' . $criar['http_code']);

        // Resolve token da instância
        $this->addDebug('info', 'Resolvendo token da instância...');
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
        $this->addDebug('info', 'Usando token de instância: ***' . substr($instanceToken, -4));

        $headers = [
            'apikey: ' . $instanceToken
        ];

        $response = $this->makeRequest($url, 'GET', [], $headers);
        $this->addDebug('info', 'QR Code HTTP: ' . $response['http_code']);
        $this->addDebug('info', 'QR Code Body: ' . substr($response['body'] ?? '', 0, 300));

        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $qrData = $data['data'] ?? $data;

            $qrCode = $qrData['Qrcode'] ?? $qrData['qrcode'] ?? $qrData['base64'] ?? null;
            if ($qrCode) {
                $this->addDebug('sucesso', 'QR Code obtido com sucesso!');
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

        $this->addDebug('erro', 'Falha ao obter QR Code: HTTP ' . $response['http_code']);
        $this->addDebug('erro', 'Erro: ' . $this->extrairErro($response));

        return [
            'success' => false,
            'error' => $this->extrairErro($response),
            'http_code' => $response['http_code'],
            'response' => $response['body'],
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

        log_message('debug', '[Evolution] Criando instância em: ' . $url . ' | Nome: ' . $this->config->evolution_instance);
        $response = $this->makeRequest($url, 'POST', $payload, $headers);
        log_message('debug', '[Evolution] Criar instância resposta HTTP: ' . $response['http_code'] . ' | Body: ' . substr($response['body'] ?? '', 0, 500));

        // Se a instância foi criada com sucesso, extrai o token da resposta
        if ($response['http_code'] == 200) {
            $data = json_decode($response['body'], true);
            $token = $data['data']['token'] ?? $data['token'] ?? null;
            if ($token) {
                $this->addDebug('info', 'Token extraído da criação. Salvando no cache.');
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
                'error' => 'Instância não encontrada no servidor.'
            ];
        }

        $url = rtrim($this->config->evolution_url, '/') . '/instance/disconnect';

        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $instanceToken
        ];

        log_message('debug', '[Evolution] Desconectando instância em: ' . $url);
        $response = $this->makeRequest($url, 'POST', ['instanceId' => $this->config->evolution_instance], $headers);
        log_message('debug', '[Evolution] Desconectar resposta HTTP: ' . $response['http_code'] . ' | Body: ' . substr($response['body'] ?? '', 0, 500));

        if (in_array($response['http_code'], [200, 201])) {
            $this->CI->notificacoes_config_model->atualizarEstadoEvolution('desconectado');
            return ['success' => true];
        }

        return [
            'success' => false,
            'error' => $this->extrairErro($response)
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
     * Faz requisição HTTP
     */
    private function makeRequest($url, $method = 'GET', $data = [], $headers = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return [
            'body' => $body,
            'http_code' => $httpCode,
            'error' => $error
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
        // Remove tudo exceto números
        $numero = preg_replace('/[^0-9]/', '', $numero);

        // Adiciona código do país se não tiver
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
        // Deve ter entre 11 e 15 dígitos (com código do país)
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
            // Brasil com código
            return '+55 (' . substr($numero, 2, 2) . ') ' . substr($numero, 4, 5) . '-' . substr($numero, 9, 4);
        }

        if (strlen($numero) == 11) {
            // Brasil sem código
            return '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 5) . '-' . substr($numero, 7, 4);
        }

        return $numero;
    }
}
