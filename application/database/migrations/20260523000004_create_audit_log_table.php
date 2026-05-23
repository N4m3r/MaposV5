<?php

/**
 * Migration: Create audit_log table for change tracking.
 */

class Migration_20260523000004_create_audit_log_table extends CI_Migration {

    public function up()
    {
        if (! $this->db->table_exists('audit_log')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => null,
                ],
                'action' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => false,
                ],
                'table_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => false,
                ],
                'record_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'default' => null,
                ],
                'old_data' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'default' => null,
                ],
                'new_data' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'default' => null,
                ],
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => true,
                    'default' => null,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
                ],
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('audit_log');

            // Add indexes for common queries
            $this->db->query('CREATE INDEX idx_audit_action ON audit_log(action)');
            $this->db->query('CREATE INDEX idx_audit_table ON audit_log(table_name)');
            $this->db->query('CREATE INDEX idx_audit_user ON audit_log(user_id)');
            $this->db->query('CREATE INDEX idx_audit_created ON audit_log(created_at)');
        }
    }

    public function down()
    {
        if ($this->db->table_exists('audit_log')) {
            $this->db->query('DROP INDEX idx_audit_action ON audit_log');
            $this->db->query('DROP INDEX idx_audit_table ON audit_log');
            $this->db->query('DROP INDEX idx_audit_user ON audit_log');
            $this->db->query('DROP INDEX idx_audit_created ON audit_log');
            $this->dbforge->drop_table('audit_log');
        }
    }
}