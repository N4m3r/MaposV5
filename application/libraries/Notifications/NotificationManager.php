<?php
/**
 * Notification Manager
 * Sistema de notificações multicanal (Email, WhatsApp, SMS, Push)
 */

namespace Libraries\Notifications;

use Libraries\Email\EmailQueue;

class NotificationManager
{
    private EmailQueue $emailQueue;
    private array $config;

    public function __construct()
    {
        $this->emailQueue = new EmailQueue();
        $this->config = [
            'whatsapp_enabled' => config_item('whatsapp_enabled') ?? false,
            'whatsapp_api' => config_item('whatsapp_api') ?? '',
            'sms_enabled' => config_item('sms_enabled') ?? false,
            'sms_provider' => config_item('sms_provider') ?? '',
            'push_enabled' => config_item('push_enabled') ?? false
        ];
    }

    /**
     * Envia notificação pelo canal especificado
     */
    public function send(string $channel, string $to, string $message, array $data = []): array
    {
        $results = [];

        switch ($channel) {
            case 'email':
                $results['email'] = $this->sendEmail($to, $message, $data);
                break;

            case 'whatsapp':
                $results['whatsapp'] = $this->sendWhatsApp($to, $message, $data);
                break;

            case 'sms':
                $results['sms'] = $this->sendSms($to, $message, $data);
                break;

            case 'push':
                $results['push'] = $this->sendPush($to, $message, $data);
                break;

            case 'all':
                $results['email'] = $this->sendEmail($to, $message, $data);
                $results['whatsapp'] = $this->sendWhatsApp($to, $message, $data);
                $results['sms'] = $this->sendSms($to, $message, $data);
                break;

            default:
                throw new \InvalidArgumentException("Canal {$channel} não suportado");
        }

        return $results;
    }

    /**
     * Envia email
     */
    private function sendEmail(string $to, string $message, array $data): array
    {
        try {
            $id = $this->emailQueue->enqueue([
                'to' => $to,
                'subject' => $data['subject'] ?? 'Notificação',
                'template' => $data['template'] ?? 'default',
                'template_data' => $data['template_data'] ?? ['mensagem' => $message],
                'priority' => $data['priority'] ?? 3
            ]);

            return [
                'success' => true,
                'id' => $id,
                'channel' => 'email'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'channel' => 'email'
            ];
        }
    }

    /**
     * Envia WhatsApp
     */
    private function sendWhatsApp(string $to, string $message, array $data): array
    {
        if (!$this->config['whatsapp_enabled']) {
            return ['success' => false, 'error' => 'WhatsApp não configurado'];
        }

        try {
            // Limpa o número
            $to = preg_replace('/[^0-9]/', '', $to);

            // Adiciona código do país se não tiver
            if (strlen($to) === 11 || strlen($to) === 10) {
                $to = '55' . $to;
            }

            // Usa API Evolution ou similar
            $apiUrl = $this->config['whatsapp_api'];

            if (empty($apiUrl)) {
                return ['success' => false, 'error' => 'URL da API não configurada'];
            }

            $payload = [
                'number' => $to,
                'text' => $message
            ];

            // Se tiver mídia
            if (!empty($data['media_url'])) {
                $payload['media'] = $data['media_url'];
                $payload['caption'] = $message;
            }

            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                return [
                    'success' => true,
                    'response' => json_decode($response, true),
                    'channel' => 'whatsapp'
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro na API: ' . $response,
                'channel' => 'whatsapp'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'channel' => 'whatsapp'
            ];
        }
    }

    /**
     * Envia SMS
     */
    private function sendSms(string $to, string $message, array $data): array
    {
        if (!$this->config['sms_enabled']) {
            return ['success' => false, 'error' => 'SMS não configurado'];
        }

        try {
            // Limpa o número
            $to = preg_replace('/[^0-9]/', '', $to);

            $provider = $this->config['sms_provider'];

            switch ($provider) {
                case 'twilio':
                    return $this->sendTwilioSms($to, $message);

                case 'zenvia':
                    return $this->sendZenviaSms($to, $message);

                default:
                    return ['success' => false, 'error' => 'Provedor não configurado'];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'channel' => 'sms'
            ];
        }
    }

    /**
     * Envia SMS via Twilio
     */
    private function sendTwilioSms(string $to, string $message): array
    {
        $sid = config_item('twilio_sid');
        $token = config_item('twilio_token');
        $from = config_item('twilio_from');

        if (!$sid || !$token || !$from) {
            return ['success' => false, 'error' => 'Credenciais Twilio não configuradas'];
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $data = [
            'To' => '+55' . $to,
            'From' => $from,
            'Body' => $message
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "{$sid}:{$token}",
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => $httpCode === 201,
            'response' => json_decode($response, true),
            'channel' => 'sms'
        ];
    }

    /**
     * Envia notificação push
     */
    private function sendPush(string $to, string $message, array $data): array
    {
        if (!$this->config['push_enabled']) {
            return ['success' => false, 'error' => 'Push não configurado'];
        }

        // Salva no banco para o service worker processar
        $ci = \u0026get_instance();
        $ci->load->database();

        $ci->db->insert('push_notifications', [
            'user_id' => $to,
            'title' => $data['title'] ?? 'MAPOS',
            'message' => $message,
            'data' => json_encode($data['payload'] ?? []),
            'sent' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'success' => true,
            'id' => $ci->db->insert_id(),
            'channel' => 'push'
        ];
    }

    /**
     * Agenda notificação para envio futuro
     */
    public function schedule(string $channel, string $to, string $message, string $scheduledAt, array $data = []): int
    {
        $ci = \u0026get_instance();
        $ci->load->database();

        $ci->db->insert('scheduled_notifications', [
            'channel' => $channel,
            'to' => $to,
            'message' => $message,
            'data' => json_encode($data),
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $ci->db->insert_id();
    }
}
