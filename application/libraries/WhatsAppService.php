<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * WhatsApp Service - Integracao com Evolution API / Z-API / Meta
 * Gerencia conexao, QR code e envio de mensagens WhatsApp.
 */
class WhatsAppService
{
    private $ci;
    private $config;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->database();
        $this->ci->load->model('notificacoes_config_model');

        $this->config = $this->ci->notificacoes_config_model->getConfig();
    }

    public function enviarMensagem($numero, $mensagem)
    {
        $provedor = $this->config->whatsapp_provedor ?? 'desativado';

        if ($provedor === 'desativado' || !$this->ci->notificacoes_config_model->isWhatsAppAtivo()) {
            return ['success' => false, 'error' => 'WhatsApp desativado'];
        }

        $numero = $this->formatarNumero($numero);

        if ($provedor === 'evolution') {
            return $this->enviarEvolution($numero, $mensagem);
        }

        if ($provedor === 'zapi') {
            return $this->enviarZApi($numero, $mensagem);
        }

        if ($provedor === 'meta') {
            return $this->enviarMeta($numero, $mensagem);
        }

        return ['success' => false, 'error' => 'Provedor nao suportado: ' . $provedor];
    }

    public function verificarConexao()
    {
        $provedor = $this->config->whatsapp_provedor ?? 'desativado';

        if ($provedor === 'desativado') {
            return ['connected' => false, 'status' => 'desativado', 'message' => 'WhatsApp desativado'];
        }

        if ($provedor === 'evolution') {
            return $this->verificarEvolution();
        }

        return ['connected' => false, 'status' => 'desconhecido', 'message' => 'Provedor nao suportado'];
    }

    public function obterQRCode()
    {
        $provedor = $this->config->whatsapp_provedor ?? 'desativado';

        if ($provedor !== 'evolution') {
            return ['success' => false, 'error' => 'QR Code disponivel apenas para Evolution API'];
        }

        $url = rtrim($this->config->evolution_url ?? '', '/') . '/instance/connect/' . ($this->config->evolution_instance ?? 'mapos');
        $apiKey = $this->config->evolution_apikey ?? '';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            return ['success' => true, 'qrcode' => $data['base64'] ?? $data['code'] ?? null];
        }

        return ['success' => false, 'error' => 'Erro ao obter QR Code: HTTP ' . $httpCode];
    }

    public function desconectar()
    {
        $provedor = $this->config->whatsapp_provedor ?? 'desativado';

        if ($provedor !== 'evolution') {
            return ['success' => false, 'error' => 'Desconecta disponivel apenas para Evolution API'];
        }

        $url = rtrim($this->config->evolution_url ?? '', '/') . '/instance/logout/' . ($this->config->evolution_instance ?? 'mapos');
        $apiKey = $this->config->evolution_apikey ?? '';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'message' => 'Desconectado com sucesso'];
        }

        return ['success' => false, 'error' => 'Erro ao desconectar: HTTP ' . $httpCode];
    }

    public static function formatarNumero($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($numero) === 11) {
            $numero = '55' . $numero;
        }
        if (strlen($numero) === 10) {
            $numero = '55' . $numero;
        }
        return $numero;
    }

    private function enviarEvolution($numero, $mensagem)
    {
        $url = rtrim($this->config->evolution_url ?? '', '/') . '/message/sendText/' . ($this->config->evolution_instance ?? 'mapos');
        $apiKey = $this->config->evolution_apikey ?? '';

        $payload = json_encode([
            'number' => $numero,
            'text' => $mensagem,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'message' => 'Mensagem enviada'];
        }

        return ['success' => false, 'error' => 'Erro Evolution API: HTTP ' . $httpCode];
    }

    private function enviarZApi($numero, $mensagem)
    {
        $url = $this->config->z_api_url ?? '';
        $token = $this->config->z_api_token ?? '';

        if (empty($url) || empty($token)) {
            return ['success' => false, 'error' => 'Z-API nao configurada'];
        }

        $payload = json_encode([
            'phone' => $numero,
            'message' => $mensagem,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'message' => 'Mensagem enviada via Z-API'];
        }

        return ['success' => false, 'error' => 'Erro Z-API: HTTP ' . $httpCode];
    }

    private function enviarMeta($numero, $mensagem)
    {
        $phoneId = $this->config->meta_phone_number_id ?? '';
        $token = $this->config->meta_access_token ?? '';

        if (empty($phoneId) || empty($token)) {
            return ['success' => false, 'error' => 'Meta WhatsApp nao configurada'];
        }

        $url = "https://graph.facebook.com/v17.0/{$phoneId}/messages";

        $payload = json_encode([
            'messaging_product' => 'whatsapp',
            'to' => $numero,
            'type' => 'text',
            'text' => ['body' => $mensagem],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'message' => 'Mensagem enviada via Meta'];
        }

        return ['success' => false, 'error' => 'Erro Meta API: HTTP ' . $httpCode];
    }

    private function verificarEvolution()
    {
        $url = rtrim($this->config->evolution_url ?? '', '/') . '/instance/fetchInstances/' . ($this->config->evolution_instance ?? 'mapos');
        $apiKey = $this->config->evolution_apikey ?? '';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            $state = $data[0]['state'] ?? $data['state'] ?? 'unknown';
            return [
                'connected' => $state === 'open',
                'status' => $state,
                'message' => $state === 'open' ? 'Conectado' : 'Status: ' . $state,
            ];
        }

        return ['connected' => false, 'status' => 'erro', 'message' => 'Erro ao verificar: HTTP ' . $httpCode];
    }
}