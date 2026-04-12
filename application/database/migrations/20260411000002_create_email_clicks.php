<?php
/**
 * Migration: Criar tabela email_clicks
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_email_clicks extends CI_Migration
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
            'tracking_id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false
            ],
            'url' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'clicked_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('tracking_id');
        $this->dbforge->add_key('clicked_at');
        $this->dbforge->create_table('email_clicks', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('email_clicks');
    }
}
