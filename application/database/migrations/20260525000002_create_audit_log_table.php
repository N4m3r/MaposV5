<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Create audit_log table for structured audit trail
 * Replaces the minimal 'logs' table for compliance and traceability.
 */
class Migration_Create_audit_log_table extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('audit_log')) {
            return;
        }

        $fields = [
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'table_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'record_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'old_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('audit_log', true);

        $indexes = [
            'idx_audit_table_record' => 'table_name, record_id',
            'idx_audit_user' => 'user_id',
            'idx_audit_action' => 'action',
            'idx_audit_created' => 'created_at',
        ];
        foreach ($indexes as $name => $cols) {
            $exists = $this->db->query("SHOW INDEX FROM audit_log WHERE Key_name = ?", [$name])->num_rows();
            if (!$exists) {
                $this->db->query("ALTER TABLE audit_log ADD INDEX `{$name}` ({$cols})");
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('audit_log')) {
            $this->dbforge->drop_table('audit_log');
        }
    }
}