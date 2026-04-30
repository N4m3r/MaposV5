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

        // Tenta carregar configs SMTP do banco (tabela configuracoes)
        $dbConfig = [];
        if (isset($this->ci->db)) {
            $keys = ['email_smtp_host', 'email_smtp_port', 'email_smtp_user', 'email_smtp_pass', 'email_smtp_crypto', 'email_from', 'email_from_name'];
            $this->ci->db->where_in('config', $keys);
            $rows = $this->ci->db->get('configuracoes')->result();
            foreach ($rows as $r) {
                $key = str_replace('email_smtp_', 'smtp_', $r->config);
                $dbConfig[$key] = $r->valor;
            }
            if (!empty($dbConfig['smtp_crypto'])) {
                $dbConfig['smtp_crypto'] = $dbConfig['smtp_crypto'];
            }
        }

        $this->config = array_merge([
            'pool_size' => 3,
            'smtp_host' => $dbConfig['smtp_host'] ?? $this->ci->data['config']['smtp_host'] ?? $_ENV['EMAIL_SMTP_HOST'] ?? '',
            'smtp_port' => (int) ($dbConfig['smtp_port'] ?? $this->ci->data['config']['smtp_port'] ?? $_ENV['EMAIL_SMTP_PORT'] ?? 587),
            'smtp_user' => $dbConfig['smtp_user'] ?? $this->ci->data['config']['smtp_user'] ?? $_ENV['EMAIL_SMTP_USER'] ?? '',
            'smtp_pass' => $dbConfig['smtp_pass'] ?? $this->ci->data['config']['smtp_pass'] ?? $_ENV['EMAIL_SMTP_PASS'] ?? '',
            'smtp_crypto' => $dbConfig['smtp_crypto'] ?? $this->ci->data['config']['smtp_crypto'] ?? $_ENV['EMAIL_SMTP_CRYPTO'] ?? 'tls',
            'from_email' => $dbConfig['from'] ?? $this->ci->data['config']['email'] ?? $_ENV['EMAIL_FROM'] ?? '',
            'from_name' => $dbConfig['from_name'] ?? $this->ci->data['config']['nome'] ?? $_ENV['EMAIL_FROM_NAME'] ?? 'Sistema',
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

                // CC
                $cc = is_object($email) ? ($email->cc ?? null) : ($email['cc'] ?? null);
                if (!empty($cc)) {
                    $ccEmails = is_string($cc) ? json_decode($cc, true) : (array)$cc;
                    if (is_array($ccEmails) && !empty($ccEmails)) {
                        $this->ci->email->cc($ccEmails);
                    }
                }

                // BCC
                $bcc = is_object($email) ? ($email->bcc ?? null) : ($email['bcc'] ?? null);
                if (!empty($bcc)) {
                    $bccEmails = is_string($bcc) ? json_decode($bcc, true) : (array)$bcc;
                    if (is_array($bccEmails) && !empty($bccEmails)) {
                        $this->ci->email->bcc($bccEmails);
                    }
                }

                // Attachments
                $attachments = is_object($email) ? ($email->attachments ?? null) : ($email['attachments'] ?? null);
                if (!empty($attachments)) {
                    $attachList = is_string($attachments) ? json_decode($attachments, true) : (array)$attachments;
                    if (is_array($attachList)) {
                        foreach ($attachList as $attachPath) {
                            if (file_exists($attachPath)) {
                                $this->ci->email->attach($attachPath);
                            }
                        }
                    }
                }

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
