<?php
/**
 * Migration: Criar tabela email_queue
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_email_queue extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'to_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'to_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'from_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'from_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false
            ],
            'body_html' => [
                'type' => 'LONGTEXT',
                'null' => true
            ],
            'body_text' => [
                'type' => 'LONGTEXT',
                'null' => true
            ],
            'template' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'template_data' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'attachments' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'priority' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 3
            ],
            'status' => [
                'type' => "ENUM('pending', 'processing', 'sent', 'failed', 'cancelled')",
                'default' => 'pending'
            ],
            'attempts' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'max_retries' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 3
            ],
            'tracking_id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
                'unique' => true
            ],
            'message_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'last_attempt' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'failed_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'opened_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'clicked_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('tracking_id', false);
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('scheduled_at');
        $this->dbforge->add_key('priority');
        $this->dbforge->add_key('template');
        $this->dbforge->create_table('email_queue', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('email_queue');
    }
}
