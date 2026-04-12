<?php
/**
 * Migration: Adicionar vínculo entre OS e NFS-e importada
 * Permite vincular notas fiscais às ordens de serviço
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_vinculo_os_nfse extends CI_Migration {

    public function up()
    {
        // Adicionar coluna os_id na tabela certificado_nfe_importada
        if (!$this->db->field_exists('os_id', 'certificado_nfe_importada')) {
            $this->dbforge->add_column('certificado_nfe_importada', [
                'os_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'after' => 'imposto_integrado'
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'data_importacao'
                ]
            ]);

            // Criar índice para performance
            $this->db->query('ALTER TABLE `certificado_nfe_importada` ADD INDEX `idx_os_id` (`os_id`)');

            // Criar foreign key
            $this->db->query('ALTER TABLE `certificado_nfe_importada`
                ADD CONSTRAINT `fk_nfse_os`
                FOREIGN KEY (`os_id`)
                REFERENCES `os` (`idOs`)
                ON DELETE SET NULL
                ON UPDATE CASCADE'
            );
        }

        // Adicionar coluna os_id na tabela cobrancas se não existir
        if (!$this->db->field_exists('os_id', 'cobrancas')) {
            $this->dbforge->add_column('cobrancas', [
                'os_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'after' => 'vendas_id'
                ]
            ]);

            $this->db->query('ALTER TABLE `cobrancas` ADD INDEX `idx_cobrancas_os_id` (`os_id`)');
        }
    }

    public function down()
    {
        // Remover foreign key
        $this->db->query('ALTER TABLE `certificado_nfe_importada` DROP FOREIGN KEY `fk_nfse_os`');

        // Remover colunas
        if ($this->db->field_exists('os_id', 'certificado_nfe_importada')) {
            $this->dbforge->drop_column('certificado_nfe_importada', 'os_id');
        }

        if ($this->db->field_exists('updated_at', 'certificado_nfe_importada')) {
            $this->dbforge->drop_column('certificado_nfe_importada', 'updated_at');
        }
    }
}
