<?php
/**
 * Webhooks Model
 */

class Webhooks_model extends CI_Model
{
    protected $table = 'webhooks';
    protected $logsTable = 'webhook_logs';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buscar todos os webhooks
     */
    public function getAll(bool $activeOnly = false): array
    {
        if ($activeOnly) {
            $this->db->where('active', 1);
        }
        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar webhook por ID
     */
    public function getById(int $id): ?object
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    /**
     * Buscar webhooks por evento
     */
    public function getByEvent(string $event): array
    {
        $webhooks = $this->getAll(true);
        $filtered = [];

        foreach ($webhooks as $webhook) {
            $events = json_decode($webhook->events ?? '[]', true);
            if (in_array($event, $events) || in_array('*', $events)) {
                $filtered[] = $webhook;
            }
        }

        return $filtered;
    }

    /**
     * Inserir webhook
     */
    public function insert(array $data): int
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Atualizar webhook
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Excluir webhook
     */
    public function delete(int $id): bool
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Inserir log
     */
    public function insertLog(array $data): int
    {
        $this->db->insert($this->logsTable, $data);
        return $this->db->insert_id();
    }

    /**
     * Buscar logs de um webhook
     */
    public function getLogs(int $webhookId, int $limit = 50): array
    {
        return $this->db
            ->where('webhook_id', $webhookId)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get($this->logsTable)
            ->result();
    }

    /**
     * Buscar logs recentes
     */
    public function getRecentLogs(int $limit = 10): array
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get($this->logsTable)
            ->result();
    }

    /**
     * Estatísticas
     */
    public function getStats(): array
    {
        return [
            'total' => $this->db->count_all_results($this->table),
            'active' => $this->db->where('active', 1)->count_all_results($this->table),
            'total_calls' => $this->db->count_all_results($this->logsTable),
            'successful_calls' => $this->db->where('success', 1)->count_all_results($this->logsTable)
        ];
    }
}
