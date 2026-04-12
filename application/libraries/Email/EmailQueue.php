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
        $insert = [
            'to_email' => $data['to'],
            'to_name' => $data['to_name'] ?? null,
            'subject' => $data['subject'],
            'body_html' => $data['body_html'] ?? null,
            'body_text' => $data['body_text'] ?? null,
            'template' => $data['template'] ?? null,
            'template_data' => !empty($data['template_data']) ? json_encode($data['template_data']) : null,
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
        $this->ci->db->set('retry_count', 'retry_count + 1', false);
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

        return $stats;
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
