-- =====================================================
-- MIGRAÇÃO MANUAL - MAPOS V5
-- Execute este script no phpMyAdmin ou MySQL Workbench
-- =====================================================

-- -----------------------------------------------------
-- Tabela: os_nfse_emitida (Notas fiscais de serviço)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_nfse_emitida` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL COMMENT 'ID da OS vinculada',
  `numero_nfse` VARCHAR(20) NULL COMMENT 'Número da NFS-e',
  `chave_acesso` VARCHAR(50) NULL,
  `data_emissao` DATETIME NULL,
  `valor_servicos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_deducoes` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_liquido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `aliquota_iss` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `valor_iss` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_inss` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_irrf` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_csll` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_pis` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_cofins` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_total_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `situacao` ENUM('Pendente', 'Emitida', 'Cancelada', 'Substituida') NOT NULL DEFAULT 'Pendente',
  `codigo_verificacao` VARCHAR(20) NULL,
  `link_impressao` VARCHAR(500) NULL,
  `xml_path` VARCHAR(500) NULL,
  `protocolo` VARCHAR(50) NULL,
  `mensagem_retorno` TEXT NULL,
  `cobranca_id` INT(11) NULL COMMENT 'ID da cobrança/boleto vinculado',
  `emitido_por` INT(11) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_numero_nfse` (`numero_nfse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Tabela: os_boleto_emitido (Boletos gerados para OS)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_boleto_emitido` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `nfse_id` INT(11) NULL COMMENT 'ID da NFS-e vinculada',
  `nosso_numero` VARCHAR(50) NULL,
  `linha_digitavel` VARCHAR(60) NULL,
  `codigo_barras` VARCHAR(44) NULL,
  `data_emissao` DATE NULL,
  `data_vencimento` DATE NULL,
  `data_pagamento` DATE NULL,
  `valor_original` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_desconto_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor descontado dos impostos (NFSe)',
  `valor_liquido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_pago` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `multa` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `juros` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado') NOT NULL DEFAULT 'Pendente',
  `instrucoes` TEXT NULL,
  `sacado_nome` VARCHAR(255) NULL,
  `sacado_documento` VARCHAR(20) NULL,
  `sacado_endereco` VARCHAR(500) NULL,
  `pdf_path` VARCHAR(500) NULL,
  `remessa_id` INT(11) NULL,
  `retorno_id` INT(11) NULL,
  `gateway` VARCHAR(50) NULL COMMENT 'Gateway de pagamento usado',
  `gateway_transaction_id` VARCHAR(100) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_nfse_id` (`nfse_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Adicionar colunas na tabela os
-- -----------------------------------------------------
ALTER TABLE `os`
ADD COLUMN IF NOT EXISTS `nfse_status` ENUM('Pendente', 'Emitida', 'Cancelada') NOT NULL DEFAULT 'Pendente' COMMENT 'Status da NFS-e vinculada' AFTER `status`,
ADD COLUMN IF NOT EXISTS `boleto_status` ENUM('Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado') NOT NULL DEFAULT 'Pendente' COMMENT 'Status do boleto vinculado' AFTER `nfse_status`,
ADD COLUMN IF NOT EXISTS `data_vencimento_boleto` DATE NULL DEFAULT NULL COMMENT 'Data de vencimento do boleto' AFTER `boleto_status`,
ADD COLUMN IF NOT EXISTS `valor_com_impostos` DECIMAL(15, 2) NULL DEFAULT NULL COMMENT 'Valor liquido apos deducao de impostos' AFTER `valor_desconto`;

-- -----------------------------------------------------
-- Tabela: certificado_digital (se não existir)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificado_digital` (
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
  `ultimo_acesso` DATETIME NULL,
  `ultimo_erro` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  INDEX `idx_cnpj` (`cnpj`),
  INDEX `idx_ativo` (`ativo`),
  INDEX `idx_data_validade` (`data_validade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Tabela: impostos_config (se não existir)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `impostos_config` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `config_key` VARCHAR(50) NOT NULL,
  `config_value` TEXT NULL,
  `descricao` VARCHAR(255) NULL,
  `updated_at` DATETIME NULL,
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Tabela: dre_contas (se não existir)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dre_contas` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(10) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT NULL,
  `tipo` ENUM('RECEITA', 'CUSTO', 'DESPESA', 'ATIVO', 'PASSIVO', 'PATRIMONIO') NOT NULL,
  `grupo` VARCHAR(50) NULL,
  `sinal` ENUM('POSITIVO', 'NEGATIVO') DEFAULT 'POSITIVO',
  `ordem` INT(11) DEFAULT 0,
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  UNIQUE KEY `codigo` (`codigo`),
  INDEX `idx_grupo` (`grupo`),
  INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Tabela: dre_lancamentos (se não existir)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dre_lancamentos` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conta_id` INT(11) UNSIGNED NOT NULL,
  `data` DATE NOT NULL,
  `valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `tipo_movimento` ENUM('DEBITO', 'CREDITO') NOT NULL,
  `descricao` TEXT NULL,
  `documento` VARCHAR(100) NULL,
  `os_id` INT(11) NULL,
  `venda_id` INT(11) NULL,
  `lancamento_id` INT(11) NULL,
  `usuarios_id` INT(11) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  INDEX `idx_conta_id` (`conta_id`),
  INDEX `idx_data` (`data`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_venda_id` (`venda_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Inserir permissões do sistema NFSe
-- -----------------------------------------------------
INSERT IGNORE INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`) VALUES
('Visualizar NFSe (OS)', NOW(), 'a:1:{s:5:"vNFSe";s:1:"1";}', 1),
('Cadastrar NFSe (OS)', NOW(), 'a:1:{s:5:"cNFSe";s:1:"1";}', 1),
('Editar NFSe (OS)', NOW(), 'a:1:{s:5:"eNFSe";s:1:"1";}', 1),
('Visualizar Boleto OS', NOW(), 'a:1:{s:9:"vBoletoOS";s:1:"1";}', 1),
('Cadastrar Boleto OS', NOW(), 'a:1:{s:9:"cBoletoOS";s:1:"1";}', 1),
('Editar Boleto OS', NOW(), 'a:1:{s:9:"eBoletoOS";s:1:"1";}', 1),
('Relatório NFSe', NOW(), 'a:1:{s:5:"rNFSe";s:1:"1";}', 1);

-- -----------------------------------------------------
-- Inserir configurações padrão de impostos
-- -----------------------------------------------------
INSERT IGNORE INTO `impostos_config` (`config_key`, `config_value`, `descricao`) VALUES
('IMPOSTO_ANEXO_PADRAO', 'III', 'Anexo do Simples Nacional padrão'),
('IMPOSTO_FAIXA_ATUAL', '1', 'Faixa de faturamento atual'),
('IMPOSTO_ISS_MUNICIPAL', '5.00', 'Alíquota ISS municipal (%)'),
('IMPOSTO_RETENCAO_AUTOMATICA', '0', 'Retenção automática de impostos (1=sim, 0=não)'),
('IMPOSTO_DRE_INTEGRACAO', '0', 'Integração automática com DRE (1=sim, 0=não)');

-- -----------------------------------------------------
-- Inserir contas DRE padrão (se não existirem)
-- -----------------------------------------------------
INSERT IGNORE INTO `dre_contas` (`codigo`, `nome`, `tipo`, `grupo`, `sinal`, `ordem`, `ativo`) VALUES
('1.1', 'Receita de Serviços', 'RECEITA', 'RECEITA_BRUTA', 'POSITIVO', 1, 1),
('1.2', 'Receita de Vendas', 'RECEITA', 'RECEITA_BRUTA', 'POSITIVO', 2, 1),
('1.3', 'Outras Receitas Operacionais', 'RECEITA', 'OUTRAS_RECEITAS', 'POSITIVO', 3, 1),
('2.1', 'Deduções de Receita', 'DESPESA', 'DEDUCOES', 'NEGATIVO', 10, 1),
('3.1', 'Custo dos Serviços', 'CUSTO', 'CUSTO', 'NEGATIVO', 20, 1),
('3.2', 'Custo das Vendas', 'CUSTO', 'CUSTO', 'NEGATIVO', 21, 1),
('4.1', 'Despesas com Pessoal', 'DESPESA', 'DESPESA_OPERACIONAL', 'NEGATIVO', 30, 1),
('4.2', 'Despesas Administrativas', 'DESPESA', 'DESPESA_OPERACIONAL', 'NEGATIVO', 31, 1),
('4.3', 'Despesas com Vendas', 'DESPESA', 'DESPESA_OPERACIONAL', 'NEGATIVO', 32, 1),
('5.1', 'Outras Despesas', 'DESPESA', 'OUTRAS_DESPESAS', 'NEGATIVO', 40, 1),
('6.1', 'Imposto de Renda', 'DESPESA', 'IMPOSTO_RENDA', 'NEGATIVO', 50, 1);

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================
-- Após executar, acesse: https://jj-ferreiras.com.br/MaposV5/index.php/nfse_os
