<?php

/**
 * Migration: Add performance indexes to frequently queried columns.
 */

class Migration_20260523000002_add_performance_indexes extends CI_Migration {

    public function up()
    {
        $indexes = [
            ['table' => 'os', 'column' => 'status', 'name' => 'idx_os_status'],
            ['table' => 'os', 'column' => 'dataInicial', 'name' => 'idx_os_data_inicial'],
            ['table' => 'os', 'columns' => ['status', 'dataInicial'], 'name' => 'idx_os_status_data'],
            ['table' => 'os', 'column' => 'clientes_id', 'name' => 'idx_os_cliente'],
            ['table' => 'lancamentos', 'column' => 'baixado', 'name' => 'idx_lanc_baixado'],
            ['table' => 'lancamentos', 'column' => 'data_pagamento', 'name' => 'idx_lanc_data'],
            ['table' => 'lancamentos', 'column' => 'tipo', 'name' => 'idx_lanc_tipo'],
        ];

        foreach ($indexes as $index) {
            if (! $this->db->table_exists($index['table'])) {
                continue;
            }

            $columns = isset($index['columns']) ? implode(', ', $index['columns']) : $index['column'];
            $sql = "ALTER TABLE {$index['table']} ADD INDEX {$index['name']} ({$columns})";

            try {
                $this->db->query($sql);
            } catch (Exception $e) {
                // Index may already exist, skip
                log_message('info', "Index {$index['name']} skipped: " . $e->getMessage());
            }
        }
    }

    public function down()
    {
        $indexes = [
            ['table' => 'os', 'name' => 'idx_os_status'],
            ['table' => 'os', 'name' => 'idx_os_data_inicial'],
            ['table' => 'os', 'name' => 'idx_os_status_data'],
            ['table' => 'os', 'name' => 'idx_os_cliente'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_baixado'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_data'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_tipo'],
        ];

        foreach ($indexes as $index) {
            if ($this->db->table_exists($index['table'])) {
                try {
                    $this->db->query("ALTER TABLE {$index['table']} DROP INDEX {$index['name']}");
                } catch (Exception $e) {
                    // Index may not exist, skip
                }
            }
        }
    }
}