<?php
/**
 * Webhook Manager
 * Gerenciamento de webhooks
 */

namespace Libraries\Webhooks;

class WebhookManager
{
    private $db;
    private string $secret;

    public function __construct()
    {
        $ci = \&get_instance();
        $ci->load->database();
        $this->db = $ci->db;
        $this->secret = config_item('webhook_secret') ?? bin2hex(random_bytes(32));
    }

    /**
     * Registra um novo webhook
     */
    public function register(array $data): int
    {
        $insert = [
            'url' => $data['url'],
            'events' => json_encode($data['events'] ?? []),
            'secret' => $data['secret'] ?? bin2hex(random_bytes(16)),
            'active' => $data['active'] ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('webhooks', $insert);
        return $this->db->insert_id();
    }

    /**
     * Dispara webhook para evento
     */
    public function trigger(string $event, array $payload): array
    {
        // Busca webhooks para este evento
        $this->db->where('active', 1);
        $webhooks = $this->db->get('webhooks')->result();

        $results = [];

        foreach ($webhooks as $webhook) {
            $events = json_decode($webhook->events, true) ?? [];

            if (in_array($event, $events) || in_array('*', $events)) {
                $results[] = $this->send($webhook, $event, $payload);
            }
        }

        return $results;
    }

    /**
     * Envia webhook
     */
    private function send(object $webhook, string $event, array $payload): array
    {
        $payloadData = [
            'event' => $event,
            'timestamp' => time(),
            'data' => $payload
        ];

        $signature = $this->signPayload($payloadData, $webhook->secret);

        $ch = curl_init($webhook->url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payloadData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Webhook-Signature: ' . $signature,
                'X-Webhook-Event: ' . $event,
                'X-Webhook-ID: ' . $webhook->id,
                'User-Agent: MAPOS-Webhook/1.0'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $success = $httpCode >= 200 && $httpCode < 300;

        // Registra tentativa
        $this->logAttempt($webhook->id, $event, $payloadData, $success, $httpCode, $response);

        return [
            'webhook_id' => $webhook->id,
            'success' => $success,
            'http_code' => $httpCode,
            'error' => $error ?: null
        ];
    }

    /**
     * Assina payload
     */
    private function signPayload(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }

    /**
     * Verifica assinatura
     */
    public function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    /**
     * Registra tentativa de webhook
     */
    private function logAttempt(int $webhookId, string $event, array $payload, bool $success, int $httpCode, string $response): void
    {
        $this->db->insert('webhook_logs', [
            'webhook_id' => $webhookId,
            'event' => $event,
            'payload' => json_encode($payload),
            'success' => $success ? 1 : 0,
            'http_code' => $httpCode,
            'response' => substr($response, 0, 1000), // Limita tamanho
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Obtém estatísticas de webhooks
     */
    public function getStats(int $webhookId): array
    {
        $this->db->where('webhook_id', $webhookId);
        $logs = $this->db->get('webhook_logs')->result();

        $total = count($logs);
        $success = count(array_filter($logs, fn($l) => $l->success));

        return [
            'total' => $total,
            'success' => $success,
            'failed' => $total - $success,
            'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0
        ];
    }
}
