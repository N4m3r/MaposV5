<?php

namespace Libraries\Email;

/**
 * SMTP Pool Library
 * Gerencia pool de conexões SMTP para envio em lote
 */
class SmtpPool
{
    private $ci;
    private $config;
    private $connections = [];
    private $currentConnection = 0;

    public function __construct(array $config = [])
    {
        $this->ci = &get_instance();
        $this->config = array_merge([
            'pool_size' => 3,
            'smtp_host' => $this->ci->data['config']['smtp_host'] ?? '',
            'smtp_port' => $this->ci->data['config']['smtp_port'] ?? 587,
            'smtp_user' => $this->ci->data['config']['smtp_user'] ?? '',
            'smtp_pass' => $this->ci->data['config']['smtp_pass'] ?? '',
            'smtp_crypto' => 'tls',
            'timeout' => 30
        ], $config);
    }

    /**
     * Inicializa pool de conexões
     */
    public function initialize(): bool
    {
        if (empty($this->config['smtp_host'])) {
            log_message('error', 'SMTP Pool: Host não configurado');
            return false;
        }
        return true;
    }

    /**
     * Envia lote de emails
     */
    public function sendBatch(array $emails): array
    {
        $results = [];
        if (empty($emails)) {
            return $results;
        }

        $this->ci->load->library('email');

        $config = [
            'protocol' => 'smtp',
            'smtp_host' => $this->config['smtp_host'],
            'smtp_port' => $this->config['smtp_port'],
            'smtp_user' => $this->config['smtp_user'],
            'smtp_pass' => $this->config['smtp_pass'],
            'smtp_crypto' => $this->config['smtp_crypto'],
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            'wordwrap' => true
        ];

        foreach ($emails as $email) {
            $emailId = is_object($email) ? $email->id : $email['id'];

            try {
                $this->ci->email->initialize($config);
                $this->ci->email->clear();

                $from = $this->ci->data['config']['email'] ?? 'noreply@example.com';
                $fromName = $this->ci->data['config']['nome'] ?? 'Sistema';
                $this->ci->email->from($from, $fromName);

                $toEmail = is_object($email) ? $email->to_email : $email['to_email'];
                $toName = is_object($email) ? ($email->to_name ?? '') : ($email['to_name'] ?? '');
                $this->ci->email->to($toEmail, $toName);

                $subject = is_object($email) ? $email->subject : $email['subject'];
                $this->ci->email->subject($subject);

                $bodyHtml = is_object($email) ? ($email->body_html ?? '') : ($email['body_html'] ?? '');
                $this->ci->email->message($bodyHtml ?: '');

                if ($this->ci->email->send()) {
                    $results[$emailId] = [
                        'success' => true,
                        'message_id' => '<' . md5(uniqid()) . '-' . $emailId . '@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '>'
                    ];
                } else {
                    $results[$emailId] = [
                        'success' => false,
                        'error' => 'Failed to send'
                    ];
                }

                usleep(100000);

            } catch (\Exception $e) {
                $results[$emailId] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Envia email único
     */
    public function sendSingle(array $email): array
    {
        return $this->sendBatch([$email]);
    }

    /**
     * Verifica status das conexões
     */
    public function getStatus(): array
    {
        return [
            'pool_size' => $this->config['pool_size'],
            'active_connections' => count($this->connections),
            'smtp_host' => $this->config['smtp_host'],
            'smtp_port' => $this->config['smtp_port']
        ];
    }
}
