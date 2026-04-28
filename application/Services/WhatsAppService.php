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

        $allHeaders = array_merge(['Accept: application/json'], $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return ['body' => $body, 'http_code' => $httpCode, 'error' => $error];
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

        return ['connected' => false, 'status' => 'erro', 'error' => 'HTTP ' . $resp['http_code'], 'debug' => $resp['body']];
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

        // 2. Obtém token da instância via /instance/all
        $urlAll = $this->config->evolution_url . '/instance/all';
        $this->addDebug('url', 'GET ' . $urlAll);
        $respAll = $this->request($urlAll, 'GET', [], ['apikey: ' . $this->config->evolution_apikey]);
        $this->addDebug('info', 'HTTP /instance/all: ' . $respAll['http_code']);

        if ($respAll['http_code'] !== 200) {
            $this->addDebug('erro', 'Falha ao listar instâncias: HTTP ' . $respAll['http_code']);
            return ['success' => false, 'error' => 'Não foi possível listar instâncias (HTTP ' . $respAll['http_code'] . ')', 'debug' => $this->debugLog];
        }

        $data = json_decode($respAll['body'], true);
        $instances = $data['data'] ?? [];
        $this->addDebug('info', 'Instâncias encontradas: ' . count($instances));

        $instanceToken = null;
        foreach ($instances as $inst) {
            $name = $inst['name'] ?? '';
            $this->addDebug('info', '  - Instância: "' . $name . '"');
            if (strcasecmp($name, $this->config->evolution_instance) === 0) {
                $instanceToken = $inst['token'] ?? null;
                $this->addDebug('sucesso', 'Instância encontrada! Token: ' . ($instanceToken ? 'SIM' : 'NÃO'));
                break;
            }
        }

        if (!$instanceToken) {
            $this->addDebug('erro', 'Token da instância não encontrado');
            return ['success' => false, 'error' => 'Instância "' . $this->config->evolution_instance . '" não encontrada ou sem token', 'debug' => $this->debugLog];
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
