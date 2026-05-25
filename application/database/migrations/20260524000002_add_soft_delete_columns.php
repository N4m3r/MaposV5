<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add soft delete support to main tables
 * Adds deleted_at column to critical tables instead of hard deletes.
 */
class Migration_Add_soft_delete_columns extends CI_Migration
{
    private $tables = [
        'os',
        'clientes',
        'produtos',
        'servicos',
        'usuarios',
        'lancamentos',
        'cobrancas',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if ($this->db->table_exists($table)) {
                if (!$this->db->field_exists('deleted_at', $table)) {
                    $fields = [
                        'deleted_at' => [
                            'type' => 'DATETIME',
                            'null' => true,
                            'default' => null,
                            'after' => $this->getLastColumn($table),
                        ],
                    ];
                    $this->dbforge->add_column($table, $fields);
                }
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if ($this->db->table_exists($table) && $this->db->field_exists('deleted_at', $table)) {
                $this->dbforge->drop_column($table, 'deleted_at');
            }
        }
    }

    private function getLastColumn($table)
    {
        $fields = $this->db->field_data($table);
        if (!empty($fields)) {
            return $fields[count($fields) - 1]->name;
        }
        return 'id';
    }
}