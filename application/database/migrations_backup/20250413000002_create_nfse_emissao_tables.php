<?php
/**
 * Migration: Sistema de Emissão de NFS-e e Boletos vinculados à OS
 * Cria tabelas para gerenciar emissão de notas e cobranças integradas
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_nfse_emissao_tables extends CI_Migration {

    public function up()
    {
        // Tabela: os_nfse_emitida - Notas fiscais de serviço emitidas
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'os_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'comment' => 'ID da OS vinculada'
            ],
            'numero_nfse' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
                'comment' => 'Número da NFS-e'
            ],
            'chave_acesso' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'data_emissao' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'valor_servicos' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_deducoes' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_liquido' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'aliquota_iss' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00
            ],
            'valor_iss' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_inss' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_irrf' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_csll' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_pis' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_cofins' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_total_impostos' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'situacao' => [
                'type' => 'ENUM',
                'constraint' => ['Pendente', 'Emitida', 'Cancelada', 'Substituida'],
                'default' => 'Pendente'
            ],
            'codigo_verificacao' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ],
            'link_impressao' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ],
            'xml_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ],
            'protocolo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'mensagem_retorno' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'cobranca_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'ID da cobrança/boleto vinculado'
            ],
            'emitido_por' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('os_id');
        $this->dbforge->add_key('numero_nfse');
        $this->dbforge->create_table('os_nfse_emitida', TRUE, ['ENGINE' => 'InnoDB']);

        // Tabela: os_boleto_emitido - Boletos gerados para OS
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'os_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'nfse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'ID da NFS-e vinculada'
            ],
            'nosso_numero' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'linha_digitavel' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => TRUE
            ],
            'codigo_barras' => [
                'type' => 'VARCHAR',
                'constraint' => 44,
                'null' => TRUE
            ],
            'data_emissao' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'data_vencimento' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'data_pagamento' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'valor_original' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_desconto_impostos' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
                'comment' => 'Valor descontado dos impostos (NFSe)'
            ],
            'valor_liquido' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'valor_pago' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'multa' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'juros' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado'],
                'default' => 'Pendente'
            ],
            'instrucoes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'sacado_nome' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'sacado_documento' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ],
            'sacado_endereco' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ],
            'pdf_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ],
            'remessa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'retorno_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'gateway' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE,
                'comment' => 'Gateway de pagamento usado'
            ],
            'gateway_transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('os_id');
        $this->dbforge->add_key('nfse_id');
        $this->dbforge->add_key('status');
        $this->dbforge->create_table('os_boleto_emitido', TRUE, ['ENGINE' => 'InnoDB']);

        // Criar foreign keys
        $this->db->query('ALTER TABLE `os_nfse_emitida`
            ADD CONSTRAINT `fk_nfse_emitida_os`
            FOREIGN KEY (`os_id`)
            REFERENCES `os` (`idOs`)
            ON DELETE CASCADE
        ');

        $this->db->query('ALTER TABLE `os_boleto_emitido`
            ADD CONSTRAINT `fk_boleto_emitido_os`
            FOREIGN KEY (`os_id`)
            REFERENCES `os` (`idOs`)
            ON DELETE CASCADE
        ');

        $this->db->query('ALTER TABLE `os_boleto_emitido`
            ADD CONSTRAINT `fk_boleto_emitido_nfse`
            FOREIGN KEY (`nfse_id`)
            REFERENCES `os_nfse_emitida` (`id`)
            ON DELETE SET NULL
        ');

        // Adicionar colunas extras na tabela OS
        if (!$this->db->field_exists('nfse_status', 'os')) {
            $this->dbforge->add_column('os', [
                'nfse_status' => [
                    'type' => 'ENUM',
                    'constraint' => ['Pendente', 'Emitida', 'Cancelada'],
                    'default' => 'Pendente',
                    'after' => 'status'
                ],
                'boleto_status' => [
                    'type' => 'ENUM',
                    'constraint' => ['Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado'],
                    'default' => 'Pendente',
                    'after' => 'nfse_status'
                ],
                'data_vencimento_boleto' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                    'after' => 'boleto_status'
                ],
                'valor_com_impostos' => [
                    'type' => 'DECIMAL',
                    'constraint' => '15,2',
                    'null' => TRUE,
                    'after' => 'valor_total'
                ]
            ]);
        }
    }

    public function down()
    {
        // Remover foreign keys
        $this->db->query('ALTER TABLE `os_nfse_emitida` DROP FOREIGN KEY `fk_nfse_emitida_os`');
        $this->db->query('ALTER TABLE `os_boleto_emitido` DROP FOREIGN KEY `fk_boleto_emitido_os`');
        $this->db->query('ALTER TABLE `os_boleto_emitido` DROP FOREIGN KEY `fk_boleto_emitido_nfse`');

        // Remover colunas extras da OS
        if ($this->db->field_exists('nfse_status', 'os')) {
            $this->dbforge->drop_column('os', 'nfse_status');
        }
        if ($this->db->field_exists('boleto_status', 'os')) {
            $this->dbforge->drop_column('os', 'boleto_status');
        }
        if ($this->db->field_exists('data_vencimento_boleto', 'os')) {
            $this->dbforge->drop_column('os', 'data_vencimento_boleto');
        }
        if ($this->db->field_exists('valor_com_impostos', 'os')) {
            $this->dbforge->drop_column('os', 'valor_com_impostos');
        }

        // Dropar tabelas
        $this->dbforge->drop_table('os_boleto_emitido', TRUE);
        $this->dbforge->drop_table('os_nfse_emitida', TRUE);
    }
}
