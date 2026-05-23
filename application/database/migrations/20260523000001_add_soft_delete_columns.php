<?php

/**
 * Migration: Add soft delete columns to main tables.
 * Adds deleted_at TIMESTAMP NULL to: os, clientes, produtos, servicos, usuarios, cobrancas, lancamentos.
 */

class Migration_20260523000001_add_soft_delete_columns extends CI_Migration {

    private $tables = [
        'os',
        'clientes',
        'produtos',
        'servicos',
        'usuarios',
        'cobrancas',
        'lancamentos',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if ($this->db->table_exists($table)) {
                if (! $this->db->field_exists('deleted_at', $table)) {
                    $this->dbforge->add_column($table, [
                        'deleted_at' => [
                            'type' => 'TIMESTAMP',
                            'null' => true,
                            'default' => null,
                            'after' => $this->getLastColumn($table),
                        ],
                    ]);
                }
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if ($this->db->table_exists($table)) {
                if ($this->db->field_exists('deleted_at', $table)) {
                    $this->dbforge->drop_column($table, 'deleted_at');
                }
            }
        }
    }

    private function getLastColumn($table)
    {
        $fields = $this->db->field_data($table);
        if (! empty($fields)) {
            return end($fields)->name;
        }

        return 'id';
    }
}