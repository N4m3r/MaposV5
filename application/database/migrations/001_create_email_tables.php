<?php
/**
 * Migration: Cria tabelas para sistema de email
 */

class Migration_Create_Email_Tables {
    
    public function up($db) {
        // Tabela de fila de emails
        $sql = "CREATE TABLE IF NOT EXISTS email_queue (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        echo "Tabela email_queue criada\n";
        
        // Tabela de tracking
        $sql2 = "CREATE TABLE IF NOT EXISTS email_tracking (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql2);
        echo "Tabela email_tracking criada\n";
        
        // Tabela de eventos agendados
        $sql3 = "CREATE TABLE IF NOT EXISTS scheduled_events (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_type VARCHAR(100) NOT NULL,
            event_data JSON NULL,
            execute_at DATETIME NOT NULL,
            status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
            executed_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_status (status),
            INDEX idx_execute_at (execute_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql3);
        echo "Tabela scheduled_events criada\n";
    }
    
    public function down($db) {
        $db->query("DROP TABLE IF EXISTS email_queue");
        $db->query("DROP TABLE IF EXISTS email_tracking");
        $db->query("DROP TABLE IF EXISTS scheduled_events");
        echo "Tabelas removidas\n";
    }
}
