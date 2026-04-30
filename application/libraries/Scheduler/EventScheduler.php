<?php

namespace Libraries\Scheduler;

/**
 * Event Scheduler
 * Agendamento e processamento de eventos
 */
class EventScheduler
{
    private $ci;
    private $table = 'scheduled_events';

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->database();
    }

    /**
     * Agenda um evento (formato legado: type string)
     */
    public function schedule(string $type, array $data, string $executeAt): int
    {
        $this->ci->db->insert($this->table, [
            'event_type' => $type,
            'event_data' => json_encode($data),
            'execute_at' => $executeAt,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->ci->db->insert_id();
    }

    /**
     * Agenda um evento (formato AutoEvents: array com type, scheduled_at, payload)
     */
    public function scheduleEvent(array $event): int
    {
        $insert = [
            'event_type' => $event['type'] ?? 'email',
            'event_data' => json_encode($event['payload'] ?? $event),
            'execute_at' => $event['scheduled_at'] ?? date('Y-m-d H:i:s'),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->ci->db->insert($this->table, $insert);
        return $this->ci->db->insert_id();
    }

    /**
     * Processa eventos pendentes
     */
    public function process(): void
    {
        $now = date('Y-m-d H:i:s');
        
        $query = $this->ci->db->get_where($this->table, [
            'status' => 'pending',
            'execute_at <=' => $now
        ]);
        
        $events = $query->result();
        
        foreach ($events as $event) {
            $this->execute($event);
        }
    }

    /**
     * Executa um evento específico
     */
    private function execute(object $event): void
    {
        $data = json_decode($event->event_data, true);
        
        switch ($event->event_type) {
            case 'send_email':
                $this->scheduleEmail($data);
                break;
            case 'reminder':
                $this->scheduleReminder($data);
                break;
        }
        
        // Marca como executado
        $this->ci->db->where('id', $event->id);
        $this->ci->db->update($this->table, [
            'status' => 'completed',
            'executed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Agenda email via evento
     */
    private function scheduleEmail(array $data): void
    {
        $queue = new \Libraries\Email\EmailQueue();
        
        $queue->enqueue([
            'to' => $data['to'],
            'to_name' => $data['to_name'] ?? null,
            'subject' => $data['subject'],
            'template' => $data['template'],
            'template_data' => $data['template_data'] ?? [],
            'priority' => $data['priority'] ?? 3
        ]);
    }

    /**
     * Agenda lembrete
     */
    private function scheduleReminder(array $data): void
    {
        // Implementação de lembretes
        log_message('info', 'Reminder scheduled: ' . json_encode($data));
    }
}
