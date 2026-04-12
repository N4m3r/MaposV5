<?php

/**
 * Migration para adicionar suporte ao Banco Cora
 * - Campos para linha digitável do boleto
 * - Campo para código PIX
 * - Campos de datas de pagamento e atualização
 * - Aumenta tamanho do campo message
 */
class Migration_add_cora_support extends CI_Migration
{
    public function up()
    {
        // Adiciona campo para linha digitável do boleto
        if (!$this->db->field_exists('cobrancas', 'linha_digitavel')) {
            $this->dbforge->add_column('cobrancas', [
                'linha_digitavel' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'default' => null,
                    'after' => 'barcode',
                ],
            ]);
        }

        // Adiciona campo específico para código PIX
        if (!$this->db->field_exists('cobrancas', 'pix_code')) {
            $this->dbforge->add_column('cobrancas', [
                'pix_code' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'default' => null,
                    'after' => 'link',
                ],
            ]);
        }

        // Adiciona campo para data de pagamento
        if (!$this->db->field_exists('cobrancas', 'paid_at')) {
            $this->dbforge->add_column('cobrancas', [
                'paid_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'default' => null,
                    'after' => 'expire_at',
                ],
            ]);
        }

        // Adiciona campo para data de atualização
        if (!$this->db->field_exists('cobrancas', 'updated_at')) {
            $this->dbforge->add_column('cobrancas', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'default' => null,
                    'after' => 'created_at',
                ],
            ]);
        }

        // Aumenta o tamanho do campo message para descrições mais longas
        $this->db->query('ALTER TABLE cobrancas MODIFY COLUMN message TEXT NULL DEFAULT NULL');

        // Cria índice para buscas por charge_id
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_cobrancas_charge_id ON cobrancas(charge_id)');

        // Cria índice para buscas por status e gateway
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_cobrancas_status_gateway ON cobrancas(status, payment_gateway)');

        log_info('Migration add_cora_support executada com sucesso.');
    }

    public function down()
    {
        // Remove índices
        $this->db->query('DROP INDEX IF EXISTS idx_cobrancas_charge_id ON cobrancas');
        $this->db->query('DROP INDEX IF EXISTS idx_cobrancas_status_gateway ON cobrancas');

        // Remove colunas adicionadas
        if ($this->db->field_exists('cobrancas', 'linha_digitavel')) {
            $this->dbforge->drop_column('cobrancas', 'linha_digitavel');
        }

        if ($this->db->field_exists('cobrancas', 'pix_code')) {
            $this->dbforge->drop_column('cobrancas', 'pix_code');
        }

        if ($this->db->field_exists('cobrancas', 'paid_at')) {
            $this->dbforge->drop_column('cobrancas', 'paid_at');
        }

        if ($this->db->field_exists('cobrancas', 'updated_at')) {
            $this->dbforge->drop_column('cobrancas', 'updated_at');
        }

        // Restaura tamanho do campo message
        $this->db->query('ALTER TABLE cobrancas MODIFY COLUMN message VARCHAR(255) NOT NULL');

        log_info('Rollback da migration add_cora_support executado.');
    }
}
