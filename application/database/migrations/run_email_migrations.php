<?php
/**
 * Script para executar migrations de email
 * Uso: php run_email_migrations.php
 */

// Carrega o CodeIgniter
require_once __DIR__ . '/../../../index.php';

$ci = &get_instance();
$ci->load->database();
$ci->load->dbforge();

echo "=== Migrations de Email ===\n\n";

// Migration 001: Tabelas básicas
$migration1 = new class($ci->db) {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function up() {
        // Tabela email_queue
        $this->db->query("CREATE TABLE IF NOT EXISTS email_queue (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            to_email VARCHAR(255) NOT NULL,
            to_name VARCHAR(255) NULL,
            subject VARCHAR(500) NOT NULL,
            body_html TEXT NULL,
            body_text TEXT NULL,
            template VARCHAR(100) NULL,
            template_data JSON NULL,
            priority TINYINT(1) DEFAULT 3,
            status ENUM('pending', 'processing', 'sent', 'failed', 'cancelled', 'scheduled') DEFAULT 'pending',
            scheduled_at DATETIME NULL,
            sent_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            retry_count TINYINT(1) DEFAULT 0,
            error_message TEXT NULL,
            message_id VARCHAR(255) NULL,
            INDEX idx_status (status),
            INDEX idx_priority (priority),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Tabela email_queue criada\n";

        // Tabela email_tracking
        $this->db->query("CREATE TABLE IF NOT EXISTS email_tracking (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email_queue_id INT(11) UNSIGNED NOT NULL,
            tracking_id VARCHAR(64) NOT NULL UNIQUE,
            opened TINYINT(1) DEFAULT 0,
            opened_at DATETIME NULL,
            clicked TINYINT(1) DEFAULT 0,
            clicked_at DATETIME NULL,
            clicked_url TEXT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_tracking_id (tracking_id),
            INDEX idx_email_queue_id (email_queue_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Tabela email_tracking criada\n";

        // Tabela email_clicks
        $this->db->query("CREATE TABLE IF NOT EXISTS email_clicks (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            tracking_id VARCHAR(32) NOT NULL,
            url TEXT NOT NULL,
            clicked_at DATETIME NOT NULL,
            ip_address VARCHAR(45) NULL,
            INDEX idx_tracking_id (tracking_id),
            INDEX idx_clicked_at (clicked_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Tabela email_clicks criada\n";

        // Tabela scheduled_events
        $this->db->query("CREATE TABLE IF NOT EXISTS scheduled_events (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_type VARCHAR(100) NOT NULL,
            event_data JSON NULL,
            execute_at DATETIME NOT NULL,
            status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
            executed_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_status (status),
            INDEX idx_execute_at (execute_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Tabela scheduled_events criada\n";
    }
};

try {
    echo "Executando migrations...\n";
    $migration1->up();
    echo "\n=== Migrations concluídas com sucesso! ===\n";
} catch (Exception $e) {
    echo "\n✗ Erro: " . $e->getMessage() . "\n";
}
