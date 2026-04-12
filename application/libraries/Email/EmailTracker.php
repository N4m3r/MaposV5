<?php

namespace Libraries\Email;

/**
 * Email Tracker
 * Rastreamento de aberturas e cliques em emails
 */
class EmailTracker
{
    private $ci;
    private $table = 'email_tracking';
    private $clicksTable = 'email_clicks';

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->database();
    }

    /**
     * Gera ID de tracking para um email
     */
    public function generateTrackingId(int $emailId): string
    {
        $trackingId = hash('sha256', $emailId . '_' . uniqid() . '_' . time());
        
        $this->ci->db->insert($this->table, [
            'email_queue_id' => $emailId,
            'tracking_id' => $trackingId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $trackingId;
    }

    /**
     * Rastrea abertura de email
     */
    public function trackOpen(string $trackingId): bool
    {
        $this->ci->db->where('tracking_id', $trackingId);
        return $this->ci->db->update($this->table, [
            'opened' => 1,
            'opened_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Rastrea clique em link
     */
    public function trackClick(string $trackingId, string $url): bool
    {
        $this->ci->db->where('tracking_id', $trackingId);
        return $this->ci->db->update($this->table, [
            'clicked' => 1,
            'clicked_at' => date('Y-m-d H:i:s'),
            'clicked_url' => $url
        ]);
    }

    /**
     * Adiciona pixel de tracking ao HTML
     */
    public function addTrackingPixel(string $html, string $trackingId, string $baseUrl): string
    {
        $pixelUrl = $baseUrl . 'email/track/' . $trackingId;
        $pixel = '<img src="' . $pixelUrl . '" width="1" height="1" alt="" />';
        
        // Adiciona antes do fechamento do body
        return str_replace('</body>', $pixel . '</body>', $html);
    }

    /**
     * Obtém estatísticas de tracking
     */
    public function getStats(int $emailId): array
    {
        $query = $this->ci->db->get_where($this->table, ['email_queue_id' => $emailId]);
        $row = $query->row();
        
        if (!$row) {
            return ['opened' => false, 'clicked' => false];
        }
        
        return [
            'opened' => (bool)$row->opened,
            'opened_at' => $row->opened_at,
            'clicked' => (bool)$row->clicked,
            'clicked_at' => $row->clicked_at,
            'clicked_url' => $row->clicked_url
        ];
    }
}
