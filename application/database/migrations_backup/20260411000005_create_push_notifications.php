<?php
/**
 * Migration: Criar tabelas para notificações
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_push_notifications extends CI_Migration
{
    public function up()
    {
        // Tabela de notificações push
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'data' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('sent');
        $this->dbforge->create_table('push_notifications', true);

        // Tabela de notificações agendadas
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'channel' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'to' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'data' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'status' => [
                'type' => "ENUM('pending', 'sent', 'failed')",
                'default' => 'pending'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('scheduled_at');
        $this->dbforge->create_table('scheduled_notifications', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('push_notifications', true);
        $this->dbforge->drop_table('scheduled_notifications', true);
    }
}
