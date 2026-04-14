<?php
/**
 * Migration: Adicionar índices de performance
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_performance_indexes extends CI_Migration
{
    public function up()
    {
        // Índice para pesquisas de OS por cliente e status
        $this->db->query("CREATE INDEX idx_os_cliente_status ON os(idClientes, status, dataFinal)");

        // Índice para pesquisas de OS por data
        $this->db->query("CREATE INDEX idx_os_data_status ON os(dataFinal, status)");

        // Índice para vendas por cliente
        $this->db->query("CREATE INDEX idx_vendas_cliente ON vendas(idClientes, dataVenda)");

        // Índice para lançamentos por data
        $this->db->query("CREATE INDEX idx_lancamentos_data ON lancamentos(data_vencimento, tipo)");

        // Índice para produtos por código de barras
        $this->db->query("CREATE INDEX idx_produtos_codigo ON produtos(codDeBarra, estoque)");

        // Índice para pesquisa full-text em clientes (MySQL 5.6+)
        try {
            $this->db->query("CREATE FULLTEXT INDEX idx_clientes_busca ON clientes(nomeCliente, documento, email)");
        } catch (Exception $e) {
            // Full-text pode não estar disponível em todas as versões
            log_message('warning', 'Full-text index not created: ' . $e->getMessage());
        }

        // Índices adicionais para tabelas de email e eventos
        if ($this->db->table_exists('email_queue')) {
            $this->db->query("CREATE INDEX idx_email_status_scheduled ON email_queue(status, scheduled_at)");
            $this->db->query("CREATE INDEX idx_email_priority ON email_queue(priority, created_at)");
        }

        if ($this->db->table_exists('scheduled_events')) {
            $this->db->query("CREATE INDEX idx_events_status_scheduled ON scheduled_events(status, scheduled_at)");
            $this->db->query("CREATE INDEX idx_events_entity ON scheduled_events(entity_type, entity_id)");
        }
    }

    public function down()
    {
        // Remove índices
        $this->db->query("DROP INDEX IF EXISTS idx_os_cliente_status ON os");
        $this->db->query("DROP INDEX IF EXISTS idx_os_data_status ON os");
        $this->db->query("DROP INDEX IF EXISTS idx_vendas_cliente ON vendas");
        $this->db->query("DROP INDEX IF EXISTS idx_lancamentos_data ON lancamentos");
        $this->db->query("DROP INDEX IF EXISTS idx_produtos_codigo ON produtos");

        try {
            $this->db->query("DROP INDEX IF EXISTS idx_clientes_busca ON clientes");
        } catch (Exception $e) {
            // Ignora erro
        }

        if ($this->db->table_exists('email_queue')) {
            $this->db->query("DROP INDEX IF EXISTS idx_email_status_scheduled ON email_queue");
            $this->db->query("DROP INDEX IF EXISTS idx_email_priority ON email_queue");
        }

        if ($this->db->table_exists('scheduled_events')) {
            $this->db->query("DROP INDEX IF EXISTS idx_events_status_scheduled ON scheduled_events");
            $this->db->query("DROP INDEX IF EXISTS idx_events_entity ON scheduled_events");
        }
    }
}
