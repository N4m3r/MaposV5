<?php
/**
 * Migration: Criar tabela scheduled_events
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_scheduled_events extends CI_Migration
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
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'email, webhook, notification, task'
            ],
            'event_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'entity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'os, cliente, venda, cobranca'
            ],
            'entity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'recurring' => [
                'type' => 'BOOLEAN',
                'default' => false
            ],
            'recurrence_rule' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'cron expression'
            ],
            'payload' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'status' => [
                'type' => "ENUM('scheduled', 'completed', 'failed', 'cancelled')",
                'default' => 'scheduled'
            ],
            'executed_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('scheduled_at');
        $this->dbforge->add_key('entity_type');
        $this->dbforge->add_key('entity_id');
        $this->dbforge->create_table('scheduled_events', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('scheduled_events');
    }
}
