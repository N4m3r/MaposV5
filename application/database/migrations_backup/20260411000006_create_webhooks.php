<?php
/**
 * Migration: Criar tabelas para webhooks
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_webhooks extends CI_Migration
{
    public function up()
    {
        // Tabela de webhooks
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false
            ],
            'events' => [
                'type' => 'TEXT',
                'null' => false,
                'comment' => 'JSON array de eventos'
            ],
            'secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('active');
        $this->dbforge->create_table('webhooks', true);

        // Tabela de logs de webhooks
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'webhook_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'event' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'payload' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'success' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'http_code' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => true
            ],
            'response' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('webhook_id');
        $this->dbforge->add_key('event');
        $this->dbforge->add_key('created_at');
        $this->dbforge->create_table('webhook_logs', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('webhooks', true);
        $this->dbforge->drop_table('webhook_logs', true);
    }
}
