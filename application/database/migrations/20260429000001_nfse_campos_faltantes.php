<?php
/**
 * Migration: Ajustes NFSe — campos faltantes
 *
 * Adiciona:
 *  - emitente.inscricao_estadual
 *  - os_nfse_emitida.xml_dps
 *  - os_nfse_emitida.xml_nfse
 *
 * Segura para executar multiplas vezes.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Nfse_campos_faltantes extends CI_Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        // Tabela certificado_digital
        if (!$this->db->table_exists('certificado_digital')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `certificado_digital` (
              `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `tipo` ENUM('A1', 'A3') DEFAULT 'A1',
              `cnpj` VARCHAR(14) NOT NULL,
              `razao_social` VARCHAR(255) NULL,
              `nome_fantasia` VARCHAR(255) NULL,
              `arquivo_caminho` VARCHAR(500) NULL,
              `arquivo_hash` VARCHAR(255) NULL,
              `senha` TEXT NULL,
              `data_validade` DATETIME NULL,
              `data_emissao` DATETIME NULL,
              `emissor` VARCHAR(100) NULL,
              `serial_number` VARCHAR(100) NULL,
              `ativo` TINYINT(1) DEFAULT 0,
              `ambiente` ENUM('homologacao','producao') DEFAULT 'homologacao',
              `ultimo_acesso` DATETIME NULL,
              `ultimo_erro` TEXT NULL,
              `created_at` DATETIME NULL,
              `updated_at` DATETIME NULL,
              INDEX `idx_cnpj` (`cnpj`),
              INDEX `idx_ativo` (`ativo`),
              INDEX `idx_data_validade` (`data_validade`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            log_message('info', 'Tabela certificado_digital criada');
        }

        // Tabela os_boleto_emitido
        if (!$this->db->table_exists('os_boleto_emitido')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `os_boleto_emitido` (
              `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `os_id` INT(11) NOT NULL,
              `nfse_id` INT(11) UNSIGNED NULL,
              `nosso_numero` VARCHAR(50) NULL,
              `linha_digitavel` VARCHAR(60) NULL,
              `codigo_barras` VARCHAR(44) NULL,
              `data_emissao` DATE NULL,
              `data_vencimento` DATE NULL,
              `data_pagamento` DATE NULL,
              `valor_original` DECIMAL(15,2) DEFAULT '0.00',
              `valor_desconto_impostos` DECIMAL(15,2) DEFAULT '0.00',
              `valor_liquido` DECIMAL(15,2) DEFAULT '0.00',
              `valor_pago` DECIMAL(15,2) DEFAULT '0.00',
              `multa` DECIMAL(15,2) DEFAULT '0.00',
              `juros` DECIMAL(15,2) DEFAULT '0.00',
              `status` ENUM('Pendente','Emitido','Pago','Vencido','Cancelado') DEFAULT 'Pendente',
              `instrucoes` TEXT NULL,
              `sacado_nome` VARCHAR(255) NULL,
              `sacado_documento` VARCHAR(20) NULL,
              `sacado_endereco` VARCHAR(500) NULL,
              `pdf_path` VARCHAR(500) NULL,
              `remessa_id` INT(11) NULL,
              `retorno_id` INT(11) NULL,
              `gateway` VARCHAR(50) NULL,
              `gateway_transaction_id` VARCHAR(100) NULL,
              `created_at` DATETIME NULL,
              `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            log_message('info', 'Tabela os_boleto_emitido criada');
        }

        // emitente.inscricao_estadual
        if ($this->db->table_exists('emitente') && !$this->db->field_exists('inscricao_estadual', 'emitente')) {
            $this->db->query("ALTER TABLE `emitente` ADD COLUMN `inscricao_estadual` VARCHAR(20) NULL COMMENT 'Inscrição Estadual do prestador' AFTER `inscricao_municipal`");
            log_message('info', 'Coluna emitente.inscricao_estadual adicionada');
        }

        // os_nfse_emitida.xml_dps
        if ($this->db->table_exists('os_nfse_emitida') && !$this->db->field_exists('xml_dps', 'os_nfse_emitida')) {
            $this->db->query("ALTER TABLE `os_nfse_emitida` ADD COLUMN `xml_dps` LONGTEXT NULL COMMENT 'XML DPS assinado armazenado' AFTER `xml_path`");
            log_message('info', 'Coluna os_nfse_emitida.xml_dps adicionada');
        }

        // os_nfse_emitida.xml_nfse
        if ($this->db->table_exists('os_nfse_emitida') && !$this->db->field_exists('xml_nfse', 'os_nfse_emitida')) {
            $this->db->query("ALTER TABLE `os_nfse_emitida` ADD COLUMN `xml_nfse` LONGTEXT NULL COMMENT 'XML NFSe de retorno armazenado' AFTER `xml_dps`");
            log_message('info', 'Coluna os_nfse_emitida.xml_nfse adicionada');
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        log_message('info', 'Migration Nfse_campos_faltantes executada com sucesso.');
    }

    public function down()
    {
        log_message('info', 'Migration Nfse_campos_faltantes: rollback desabilitado para proteger dados.');
    }
}
