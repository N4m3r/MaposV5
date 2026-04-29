<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Serviço de integração com WhatsApp (Evolution API)
 */
class WhatsAppService
{
    protected $CI;
    protected $config;
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
    }

    private function addDebug($tipo, $msg)
    {
        $this->debugLog[] = ['tipo' => $tipo, 'msg' => $msg];
    }

    /**
     * Faz requisição HTTP simples
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
     * Verifica conexão com o servidor Evolution
     */
    public function verificarConexao()
    {
        $this->debugLog = [];

        if (empty($this->config->evolution_url) || empty($this->config->evolution_apikey)) {
            return ['connected' => false, 'status' => 'nao_configurado', 'error' => 'URL ou API Key não configuradas'];
        }

        $url = $this->config->evolution_url . '/instance/all';
        $resp = $this->request($url, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            $instances = $data['data'] ?? [];

            foreach ($instances as $inst) {
                $name = $inst['name'] ?? '';
                if (strcasecmp($name, $this->config->evolution_instance) === 0) {
                    $connected = !empty($inst['connected']);
                    $this->CI->notificacoes_config_model->atualizarEstadoEvolution($connected ? 'open' : 'desconectado');
                    return ['connected' => $connected, 'status' => $connected ? 'open' : 'desconectado', 'data' => $inst];
                }
            }
            return ['connected' => false, 'status' => 'instancia_nao_encontrada', 'error' => 'Instância "' . $this->config->evolution_instance . '" não existe no servidor'];
        }

        return ['connected' => false, 'status' => 'erro', 'error' => 'HTTP ' . $resp['http_code'], 'debug' => $resp['body'], 'headers' => $resp['headers']];
    }

    /**
     * Obtém o token da instância. Prioridade:
     * 1. Token salvo no banco (evolution_instance_token)
     * 2. Busca via /instance/all
     */
    private function getInstanceToken()
    {
        // 1. Usa token salvo no banco
        if (!empty($this->config->evolution_instance_token)) {
            $this->addDebug('info', 'Usando token da instância do banco');
            return $this->config->evolution_instance_token;
        }

        // 2. Busca via /instance/all
        $this->addDebug('info', 'Token não salvo no banco. Buscando via /instance/all...');
        $urlAll = $this->config->evolution_url . '/instance/all';
        $resp = $this->request($urlAll, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            foreach ($data['data'] ?? [] as $inst) {
                if (strcasecmp($inst['name'] ?? '', $this->config->evolution_instance) === 0) {
                    $token = $inst['token'] ?? null;
                    if ($token) {
                        $this->addDebug('sucesso', 'Token resolvido via API');
                        // Salva no banco para próxima vez
                        $this->CI->notificacoes_config_model->atualizarInstanceToken($token);
                        return $token;
                    }
                }
            }
        }

        $this->addDebug('erro', 'Não foi possível obter token da instância');
        return null;
    }

    /**
     * Obtém QR Code para conectar instância
     */
    public function obterQRCode()
    {
        $this->debugLog = [];

        if (empty($this->config->evolution_url) || empty($this->config->evolution_apikey) || empty($this->config->evolution_instance)) {
            return ['success' => false, 'error' => 'Configure URL, API Key e Instância primeiro'];
        }

        // 1. Verifica se já está conectado
        $status = $this->verificarConexao();
        if ($status['connected']) {
            return ['success' => false, 'error' => 'Instância já está conectada', 'already_connected' => true];
        }

        // 2. Obtém token da instância (do banco ou via API)
        $instanceToken = $this->getInstanceToken();
        if (!$instanceToken) {
            return ['success' => false, 'error' => 'Token da instância não encontrado. Clique em Diagnóstico para verificar.', 'debug' => $this->debugLog];
        }

        // 3. Obtém QR Code usando o token da instância
        $urlQr = $this->config->evolution_url . '/instance/qr?instanceId=' . urlencode($this->config->evolution_instance);
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
            return ['success' => false, 'error' => 'QR Code não presente na resposta da API', 'debug' => $this->debugLog];
        }

        $this->addDebug('erro', 'Falha ao obter QR Code: HTTP ' . $respQr['http_code']);
        return ['success' => false, 'error' => 'Erro ao obter QR Code (HTTP ' . $respQr['http_code'] . ')', 'debug' => $this->debugLog];
    }

    /**
     * Envia mensagem via Evolution
     */
    public function enviarMensagem($numero, $mensagem)
    {
        $numero = $this->limparNumero($numero);
        if (strlen($numero) < 11) {
            return ['success' => false, 'error' => 'Número de telefone inválido'];
        }

        // Obtém token da instância
        $urlAll = $this->config->evolution_url . '/instance/all';
        $resp = $this->request($urlAll, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);
        if ($resp['http_code'] !== 200) {
            return ['success' => false, 'error' => 'Não foi possível obter token da instância'];
        }

        $data = json_decode($resp['body'], true);
        $token = null;
        foreach ($data['data'] ?? [] as $inst) {
            if (strcasecmp($inst['name'], $this->config->evolution_instance) === 0) {
                $token = $inst['token'] ?? null;
                break;
            }
        }

        if (!$token) {
            return ['success' => false, 'error' => 'Instância não encontrada'];
        }

        $url = $this->config->evolution_url . '/send/text';
        $resp = $this->request($url, 'POST', [
            'number' => $numero,
            'text' => $mensagem,
            'delay' => 1200
        ], [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $token
        ]);

        if ($resp['http_code'] === 200) {
            $data = json_decode($resp['body'], true);
            return ['success' => true, 'message_id' => $data['data']['key']['id'] ?? null];
        }

        return ['success' => false, 'error' => 'Erro ao enviar (HTTP ' . $resp['http_code'] . ')'];
    }

    /**
     * Desconecta instância
     */
    public function desconectar()
    {
        $urlAll = $this->config->evolution_url . '/instance/all';
        $resp = $this->request($urlAll, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);
        if ($resp['http_code'] !== 200) {
            return ['success' => false, 'error' => 'Não foi possível obter token'];
        }

        $data = json_decode($resp['body'], true);
        $token = null;
        foreach ($data['data'] ?? [] as $inst) {
            if (strcasecmp($inst['name'], $this->config->evolution_instance) === 0) {
                $token = $inst['token'] ?? null;
                break;
            }
        }

        if (!$token) {
            return ['success' => false, 'error' => 'Instância não encontrada'];
        }

        $url = $this->config->evolution_url . '/instance/disconnect';
        $resp = $this->request($url, 'POST', ['instanceId' => $this->config->evolution_instance], [
            'Content-Type: application/json; charset=utf-8',
            'apikey: ' . $token
        ]);

        if ($resp['http_code'] === 200 || $resp['http_code'] === 201) {
            $this->CI->notificacoes_config_model->atualizarEstadoEvolution('desconectado');
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Erro ao desconectar (HTTP ' . $resp['http_code'] . ')'];
    }

    private function limparNumero($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($numero) == 11 || strlen($numero) == 10) {
            $numero = '55' . $numero;
        }
        return $numero;
    }
}
