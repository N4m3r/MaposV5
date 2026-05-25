<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add performance indexes for frequently queried columns
 * Based on analysis of the most common WHERE clauses and JOIN conditions
 */
class Migration_Add_performance_indexes extends CI_Migration
{
    public function up()
    {
        $indexes = [
            // OS - most filtered table
            ['table' => 'os', 'name' => 'idx_os_status', 'column' => 'status'],
            ['table' => 'os', 'name' => 'idx_os_dataInicial', 'column' => 'dataInicial'],
            ['table' => 'os', 'name' => 'idx_os_clientes_id', 'column' => 'clientes_id'],
            ['table' => 'os', 'name' => 'idx_os_usuarios_id', 'column' => 'usuarios_id'],
            ['table' => 'os', 'name' => 'idx_os_status_data', 'column' => 'status, dataInicial'],

            // Lancamentos - financial queries
            ['table' => 'lancamentos', 'name' => 'idx_lanc_baixado', 'column' => 'baixado'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_data_vencimento', 'column' => 'data_vencimento'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_tipo', 'column' => 'tipo'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_tipo_baixado', 'column' => 'tipo, baixado'],

            // Produtos_os / Servicos_os - OS detail queries
            ['table' => 'produtos_os', 'name' => 'idx_produtos_os_os_id', 'column' => 'os_id'],
            ['table' => 'servicos_os', 'name' => 'idx_servicos_os_os_id', 'column' => 'os_id'],

            // Clientes - search
            ['table' => 'clientes', 'name' => 'idx_clientes_nomeCliente', 'column' => 'nomeCliente'],
            ['table' => 'clientes', 'name' => 'idx_clientes_documento', 'column' => 'documento'],
            ['table' => 'clientes', 'name' => 'idx_clientes_email', 'column' => 'email'],

            // Usuarios - login lookups
            ['table' => 'usuarios', 'name' => 'idx_usuarios_email', 'column' => 'email'],
            ['table' => 'usuarios', 'name' => 'idx_usuarios_situacao', 'column' => 'situacao'],

            // Cobrancas - billing queries
            ['table' => 'cobrancas', 'name' => 'idx_cobrancas_status', 'column' => 'status'],
            ['table' => 'cobrancas', 'name' => 'idx_cobrancas_os_id', 'column' => 'os_id'],
        ];

        foreach ($indexes as $index) {
            if ($this->db->table_exists($index['table'])) {
                // Check if index already exists
                $existing = $this->db->query(
                    "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = ?
                     AND INDEX_NAME = ?",
                    [$index['table'], $index['name']]
                )->result();

                if (empty($existing)) {
                    $this->dbforge->add_key($index['column']);
                    $this->dbforge->create_table($index['table'] . '_temp', true);

                    $fields = explode(', ', $index['column']);
                    $addIndex = "ALTER TABLE `{$index['table']}` ADD INDEX `{$index['name']}` (" . implode(', ', array_map(function($f) { return "`$f`"; }, $fields)) . ")";

                    try {
                        $this->db->query($addIndex);
                    } catch (Exception $e) {
                        log_message('warning', "Index {$index['name']} already exists or failed: " . $e->getMessage());
                    }
                }
            }
        }
    }

    public function down()
    {
        $indexes = [
            ['table' => 'os', 'name' => 'idx_os_status'],
            ['table' => 'os', 'name' => 'idx_os_dataInicial'],
            ['table' => 'os', 'name' => 'idx_os_clientes_id'],
            ['table' => 'os', 'name' => 'idx_os_usuarios_id'],
            ['table' => 'os', 'name' => 'idx_os_status_data'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_baixado'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_data_vencimento'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_tipo'],
            ['table' => 'lancamentos', 'name' => 'idx_lanc_tipo_baixado'],
            ['table' => 'produtos_os', 'name' => 'idx_produtos_os_os_id'],
            ['table' => 'servicos_os', 'name' => 'idx_servicos_os_os_id'],
            ['table' => 'clientes', 'name' => 'idx_clientes_nomeCliente'],
            ['table' => 'clientes', 'name' => 'idx_clientes_documento'],
            ['table' => 'clientes', 'name' => 'idx_clientes_email'],
            ['table' => 'usuarios', 'name' => 'idx_usuarios_email'],
            ['table' => 'usuarios', 'name' => 'idx_usuarios_situacao'],
            ['table' => 'cobrancas', 'name' => 'idx_cobrancas_status'],
            ['table' => 'cobrancas', 'name' => 'idx_cobrancas_os_id'],
        ];

        foreach ($indexes as $index) {
            if ($this->db->table_exists($index['table'])) {
                try {
                    $this->db->query("ALTER TABLE `{$index['table']}` DROP INDEX `{$index['name']}`");
                } catch (Exception $e) {
                    // Index may not exist, ignore
                }
            }
        }
    }
}