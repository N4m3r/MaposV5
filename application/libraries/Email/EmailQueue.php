<?php

namespace Libraries\Email;

/**
 * Email Queue Library
 * Gerencia fila de emails no banco de dados
 */
class EmailQueue
{
    private $ci;
    private $table = 'email_queue';

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->database();
    }

    /**
     * Adiciona email à fila
     */
    public function enqueue(array $data): int
    {
        $email = strtolower(trim($data['to'] ?? ''));

        // Verifica blacklist
        if ($this->isBlacklisted($email)) {
            log_message('warning', "Email bloqueado pela blacklist: {$email}");
            return 0;
        }

        $insert = [
            'to_email' => $data['to'],
            'to_name' => $data['to_name'] ?? null,
            'cc' => !empty($data['cc']) ? json_encode((array)$data['cc']) : null,
            'bcc' => !empty($data['bcc']) ? json_encode((array)$data['bcc']) : null,
            'subject' => $data['subject'],
            'body_html' => $data['body_html'] ?? null,
            'body_text' => $data['body_text'] ?? null,
            'template' => $data['template'] ?? null,
            'template_data' => !empty($data['template_data']) ? json_encode($data['template_data']) : null,
            'attachments' => !empty($data['attachments']) ? json_encode((array)$data['attachments']) : null,
            'priority' => $data['priority'] ?? 3,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->ci->db->insert($this->table, $insert);
        return $this->ci->db->insert_id();
    }

    /**
     * Agenda email para envio futuro
     */
    public function schedule(array $data, string $scheduledAt): int
    {
        $insert = [
            'to_email' => $data['to'],
            'to_name' => $data['to_name'] ?? null,
            'subject' => $data['subject'],
            'template' => $data['template'] ?? null,
            'template_data' => !empty($data['template_data']) ? json_encode($data['template_data']) : null,
            'priority' => $data['priority'] ?? 3,
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->ci->db->insert($this->table, $insert);
        return $this->ci->db->insert_id();
    }

    /**
     * Processa emails pendentes
     */
    public function process(int $limit = 50): array
    {
        // Busca emails pendentes ou agendados que chegaram a hora
        $this->ci->db->where_in('status', ['pending', 'scheduled']);
        $this->ci->db->where('(scheduled_at IS NULL OR scheduled_at <= "' . date('Y-m-d H:i:s') . '")', null, false);
        $this->ci->db->order_by('priority', 'ASC');
        $this->ci->db->order_by('created_at', 'ASC');
        $this->ci->db->limit($limit);

        $query = $this->ci->db->get($this->table);
        $emails = $query->result();

        // Marca como processando
        foreach ($emails as $email) {
            $this->ci->db->where('id', $email->id);
            $this->ci->db->update($this->table, [
                'status' => 'processing',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $emails;
    }

    /**
     * Marca email como enviado
     */
    public function markAsSent(int $id, string $messageId = ''): bool
    {
        $this->ci->db->where('id', $id);
        return $this->ci->db->update($this->table, [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s'),
            'message_id' => $messageId,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Marca email como falho
     */
    public function markAsFailed(int $id, string $error): bool
    {
        $this->ci->db->where('id', $id);
        $this->ci->db->set('attempts', 'attempts + 1', false);
        $this->ci->db->update($this->table, [
            'status' => 'failed',
            'error_message' => $error,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return $this->ci->db->affected_rows() > 0;
    }

    /**
     * Cancela email
     */
    public function cancel(int $id): bool
    {
        $this->ci->db->where('id', $id);
        $this->ci->db->where_in('status', ['pending', 'scheduled']);
        return $this->ci->db->update($this->table, [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Retorna estatísticas
     */
    public function getStats(): array
    {
        $stats = [
            'pending' => 0,
            'processing' => 0,
            'sent' => 0,
            'failed' => 0,
            'cancelled' => 0,
            'scheduled' => 0
        ];

        $query = $this->ci->db->query("
            SELECT status, COUNT(*) as total
            FROM {$this->table}
            GROUP BY status
        ");

        if ($query) {
            foreach ($query->result() as $row) {
                if (isset($stats[$row->status])) {
                    $stats[$row->status] = (int)$row->total;
                }
            }
        }

        // Metricas adicionais
        $stats['taxa_sucesso'] = $this->getTaxaSucesso();
        $stats['enviados_hoje'] = $this->getEnviadosHoje();
        $stats['aberturas'] = $this->getAberturas();
        $stats['cliques'] = $this->getCliques();
        $stats['bounce'] = $this->getBounceRate();

        return $stats;
    }

    public function getTaxaSucesso(): float
    {
        $totalQuery = $this->ci->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status IN ('sent','failed')");
        $sentQuery  = $this->ci->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'sent'");
        $total = $totalQuery ? $totalQuery->row() : null;
        $sent  = $sentQuery ? $sentQuery->row() : null;
        if (!$total || $total->total == 0) {
            return 0.0;
        }
        return round(($sent->total / $total->total) * 100, 1);
    }

    public function getEnviadosHoje(): int
    {
        $query = $this->ci->db->query("
            SELECT COUNT(*) as total FROM {$this->table}
            WHERE status = 'sent' AND DATE(sent_at) = CURDATE()
        ");
        if (!$query) {
            return 0;
        }
        $row = $query->row();
        return (int) ($row->total ?? 0);
    }

    public function getAberturas(): int
    {
        if (!$this->ci->db->table_exists('email_tracking')) {
            return 0;
        }
        $query = $this->ci->db->query("
            SELECT COUNT(*) as total FROM email_tracking WHERE opened = 1
        ");
        if (!$query) {
            return 0;
        }
        $row = $query->row();
        return (int) ($row->total ?? 0);
    }

    public function getCliques(): int
    {
        if (!$this->ci->db->table_exists('email_tracking')) {
            return 0;
        }
        $query = $this->ci->db->query("
            SELECT COUNT(*) as total FROM email_tracking WHERE clicked = 1
        ");
        if (!$query) {
            return 0;
        }
        $row = $query->row();
        return (int) ($row->total ?? 0);
    }

    public function getBounceRate(): float
    {
        $totalQuery = $this->ci->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE attempts > 0");
        $failedQuery = $this->ci->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'failed'");
        $total = $totalQuery ? $totalQuery->row() : null;
        $failed = $failedQuery ? $failedQuery->row() : null;
        if (!$total || $total->total == 0) {
            return 0.0;
        }
        return round(($failed->total / $total->total) * 100, 1);
    }

    public function getLogs(int $limit = 50, int $offset = 0, string $status = ''): array
    {
        if (!empty($status)) {
            $this->ci->db->where('status', $status);
        }
        $this->ci->db->order_by('id', 'DESC');
        $this->ci->db->limit($limit, $offset);
        $query = $this->ci->db->get($this->table);
        return $query ? $query->result() : [];
    }

    public function countLogs(string $status = ''): int
    {
        if (!empty($status)) {
            $this->ci->db->where('status', $status);
        }
        return $this->ci->db->count_all_results($this->table);
    }

    public function isBlacklisted(string $email): bool
    {
        $email = strtolower(trim($email));
        if (empty($email)) {
            return false;
        }

        // Verifica blacklist no banco (tabela email_blacklist)
        if ($this->ci->db->table_exists('email_blacklist')) {
            $this->ci->db->where('email', $email);
            if ($this->ci->db->count_all_results('email_blacklist') > 0) {
                return true;
            }
        }

        // Verifica blacklist na configuracao (campo email_blacklist)
        $this->ci->db->where('config', 'email_blacklist');
        $row = $this->ci->db->get('configuracoes')->row();
        if ($row && !empty($row->valor)) {
            $blacklist = array_map('strtolower', array_map('trim', explode("\n", $row->valor)));
            if (in_array($email, $blacklist, true)) {
                return true;
            }
        }

        return false;
    }

    public function getBlacklist(): array
    {
        if (!$this->ci->db->table_exists('email_blacklist')) {
            return [];
        }
        return $this->ci->db->get('email_blacklist')->result() ?? [];
    }

    /**
     * Busca email por ID
     */
    public function find(int $id): ?object
    {
        $query = $this->ci->db->get_where($this->table, ['id' => $id]);
        return $query->row() ?: null;
    }
}
