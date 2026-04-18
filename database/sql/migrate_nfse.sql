-- ============================================
-- Migração: Tabelas NFS-e, Boletos e Impostos
-- Execute este SQL no banco de dados para criar as tabelas necessárias
-- ============================================

-- Tabela: NFS-e Emitidas
CREATE TABLE IF NOT EXISTS `os_nfse_emitida` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL COMMENT 'ID da OS vinculada',
  `numero_nfse` VARCHAR(20) NULL COMMENT 'Número da NFS-e',
  `chave_acesso` VARCHAR(81) NULL COMMENT 'Chave de acesso NFS-e Nacional (44+digitos)',
  `data_emissao` DATETIME NULL,
  `data_emissao_api` DATETIME NULL COMMENT 'Data/hora de emissão retornada pela API Nacional',
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
  `situacao` ENUM('Pendente', 'Emitida', 'Cancelada', 'Substituida', 'Rejeitada') NOT NULL DEFAULT 'Pendente',
  `codigo_verificacao` VARCHAR(20) NULL,
  `link_impressao` VARCHAR(500) NULL,
  `url_danfe` VARCHAR(500) NULL COMMENT 'URL do DANFSe na API Nacional',
  `xml_path` VARCHAR(500) NULL,
  `xml_dps` LONGTEXT NULL COMMENT 'XML DPS assinado enviado à API Nacional',
  `xml_nfse` LONGTEXT NULL COMMENT 'XML NFS-e retornado pela API Nacional',
  `protocolo` VARCHAR(50) NULL,
  `ambiente` ENUM('homologacao','producao') NOT NULL DEFAULT 'homologacao' COMMENT 'Ambiente de emissão',
  `motivo_cancelamento` TEXT NULL COMMENT 'Motivo do cancelamento',
  `mensagem_retorno` TEXT NULL,
  `cobranca_id` INT(11) NULL COMMENT 'ID da cobrança/boleto vinculado',
  `emitido_por` INT(11) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_numero_nfse` (`numero_nfse`),
  INDEX `idx_chave_acesso` (`chave_acesso`),
  INDEX `idx_situacao` (`situacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- ALTER TABLE: Adicionar colunas NFS-e Nacional em tabela existente
-- Execute APENAS se a tabela já existir sem estas colunas
-- =============================================
-- ALTER TABLE `os_nfse_emitida` ADD COLUMN `chave_acesso` VARCHAR(81) NULL COMMENT 'Chave de acesso NFS-e Nacional' AFTER `numero_nfse`;
-- ALTER TABLE `os_nfse_emitida` ADD COLUMN `data_emissao_api` DATETIME NULL COMMENT 'Data/hora emissão API Nacional' AFTER `data_emissao`;
-- ALTER TABLE `os_nfse_emitida` ADD COLUMN `url_danfe` VARCHAR(500) NULL COMMENT 'URL DANFSe API Nacional' AFTER `link_impressao`;
-- ALTER TABLE `os_nfse_emitida` ADD COLUMN `xml_dps` LONGTEXT NULL COMMENT 'XML DPS assinado' AFTER `xml_path`;
-- ALTER TABLE `os_nfse_emitida` ADD COLUMN `xml_nfse` LONGTEXT NULL COMMENT 'XML NFS-e retornado' AFTER `xml_dps`;
-- ALTER TABLE `os_nfse_emitida` ADD COLUMN `ambiente` ENUM('homologacao','producao') NOT NULL DEFAULT 'homologacao' COMMENT 'Ambiente emissão' AFTER `xml_nfse`;
-- ALTER TABLE `os_nfse_emitida` ADD COLUMN `motivo_cancelamento` TEXT NULL COMMENT 'Motivo cancelamento' AFTER `ambiente`;
-- ALTER TABLE `os_nfse_emitida` MODIFY COLUMN `situacao` ENUM('Pendente','Emitida','Cancelada','Substituida','Rejeitada') NOT NULL DEFAULT 'Pendente';
-- ALTER TABLE `os_nfse_emitida` ADD INDEX `idx_chave_acesso` (`chave_acesso`);
-- ALTER TABLE `os_nfse_emitida` ADD INDEX `idx_situacao` (`situacao`);

-- Tabela: Boletos Emitidos para OS
CREATE TABLE IF NOT EXISTS `os_boleto_emitido` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `nfse_id` INT(11) UNSIGNED NULL COMMENT 'ID da NFS-e vinculada',
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

-- Tabela: Configurações de Impostos (Simples Nacional)
CREATE TABLE IF NOT EXISTS `configuracoes_impostos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cnpj` VARCHAR(18) NOT NULL,
  `razao_social` VARCHAR(255) NULL,
  `anexo_simples` ENUM('I', 'II', 'III', 'IV', 'V') DEFAULT 'III',
  `faixa_simples` TINYINT(1) DEFAULT 1,
  `aliquota_simples` DECIMAL(5,2) DEFAULT 6.00,
  `retencao_iss` TINYINT(1) DEFAULT 0,
  `aliquota_iss` DECIMAL(5,2) DEFAULT 5.00,
  `retencao_pis` TINYINT(1) DEFAULT 0,
  `aliquota_pis` DECIMAL(5,2) DEFAULT 0.65,
  `retencao_cofins` TINYINT(1) DEFAULT 0,
  `aliquota_cofins` DECIMAL(5,2) DEFAULT 3.00,
  `retencao_csll` TINYINT(1) DEFAULT 0,
  `aliquota_csll` DECIMAL(5,2) DEFAULT 1.00,
  `retencao_ir` TINYINT(1) DEFAULT 0,
  `aliquota_ir` DECIMAL(5,2) DEFAULT 1.50,
  `retencao_inss` TINYINT(1) DEFAULT 0,
  `aliquota_inss` DECIMAL(5,2) DEFAULT 11.00,
  `valor_minimo_retencao` DECIMAL(15,2) DEFAULT 0.00,
  `ativar_retencao_automatica` TINYINT(1) DEFAULT 0,
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_cnpj` (`cnpj`),
  INDEX `idx_anexo` (`anexo_simples`),
  INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados iniciais: Anexo V (Comércio - Materiais de Construção)
INSERT IGNORE INTO `configuracoes_impostos` (`cnpj`, `razao_social`, `anexo_simples`, `faixa_simples`, `aliquota_simples`, `retencao_iss`, `aliquota_iss`, `retencao_pis`, `aliquota_pis`, `retencao_cofins`, `aliquota_cofins`, `retencao_csll`, `aliquota_csll`, `retencao_ir`, `aliquota_ir`, `retencao_inss`, `aliquota_inss`, `valor_minimo_retencao`, `ativar_retencao_automatica`, `ativo`, `created_at`) VALUES
('00000000000000', 'Empresa Padrao', 'V', 1, 6.00, 0, 0.00, 0, 0.50, 0, 2.34, 0, 0.80, 0, 0.80, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 2, 8.21, 0, 0.00, 0, 0.63, 0, 2.67, 0, 1.46, 0, 1.46, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 3, 10.46, 0, 0.00, 0, 0.77, 0, 3.27, 0, 1.90, 0, 1.90, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 4, 11.14, 0, 0.82, 0, 2.15, 0, 3.46, 0, 2.15, 0, 0.82, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 5, 12.05, 0, 0.87, 0, 2.36, 0, 3.67, 0, 2.36, 0, 0.87, 0, 0.00, 0.00, 0, 1, NOW());

-- Dados iniciais: Anexo III (Serviços - Padrão)
INSERT IGNORE INTO `configuracoes_impostos` (`cnpj`, `razao_social`, `anexo_simples`, `faixa_simples`, `aliquota_simples`, `retencao_iss`, `aliquota_iss`, `retencao_pis`, `aliquota_pis`, `retencao_cofins`, `aliquota_cofins`, `retencao_csll`, `aliquota_csll`, `retencao_ir`, `aliquota_ir`, `retencao_inss`, `aliquota_inss`, `valor_minimo_retencao`, `ativar_retencao_automatica`, `ativo`, `created_at`) VALUES
('00000000000000', 'Empresa Padrao', 'III', 1, 6.00, 0, 0.00, 0, 0.30, 0, 1.38, 0, 0.36, 0, 0.36, 0, 2.40, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'III', 2, 11.20, 0, 0.00, 0, 0.60, 0, 2.76, 0, 0.72, 0, 0.72, 0, 4.80, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'III', 3, 13.95, 0, 0.00, 0, 0.75, 0, 3.45, 0, 0.90, 0, 0.90, 0, 5.95, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'III', 4, 16.17, 0, 0.00, 0, 0.87, 0, 3.99, 0, 1.04, 0, 1.04, 0, 6.89, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'III', 5, 18.00, 0, 0.00, 0, 0.97, 0, 4.45, 0, 1.16, 0, 1.16, 0, 7.66, 0.00, 0, 1, NOW());

-- Tabela: Configurações do Sistema de Impostos
CREATE TABLE IF NOT EXISTS `config_sistema_impostos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chave` VARCHAR(100) NOT NULL,
  `valor` TEXT NULL,
  `descricao` VARCHAR(255) NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados padrão para config_sistema_impostos
INSERT IGNORE INTO `config_sistema_impostos` (`chave`, `valor`, `descricao`) VALUES
('IMPOSTO_ANEXO_PADRAO', 'III', 'Anexo do Simples Nacional padrao'),
('IMPOSTO_FAIXA_ATUAL', '1', 'Faixa de faturamento atual (1-5)'),
('IMPOSTO_RETENCAO_AUTOMATICA', '0', 'Habilitar retencao automatica (1=Sim, 0=Nao)'),
('IMPOSTO_DRE_INTEGRACAO', '0', 'Integrar retencoes com DRE (1=Sim, 0=Nao)'),
('IMPOSTO_ISS_MUNICIPAL', '5.00', 'Aliquota de ISS municipal (%)'),
('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL', '010701', 'Codigo de Tributacao Nacional (LC 116/2003)'),
('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL', '100', 'Codigo de Tributacao Municipal'),
('IMPOSTO_DESCRICAO_SERVICO', 'Suporte tecnico em informatica, inclusive instalacao, configuracao e manutencao de programas de computacao e bancos de dados.', 'Descricao do servico para NFS-e');

-- Tabela: Impostos Retidos (para DRE)
CREATE TABLE IF NOT EXISTS `impostos_retidos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cobranca_id` INT(11) NULL,
  `os_id` INT(11) UNSIGNED NULL,
  `venda_id` INT(11) UNSIGNED NULL,
  `cliente_id` INT(11) UNSIGNED NOT NULL,
  `valor_bruto` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_liquido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `aliquota_aplicada` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `irpj_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `csll_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `cofins_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `pis_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `iss_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `data_competencia` DATE NULL,
  `data_retencao` DATETIME NULL,
  `nota_fiscal` VARCHAR(50) NULL,
  `status` ENUM('Retido', 'Estornado', 'Baixado') NOT NULL DEFAULT 'Retido',
  `dre_lancamento_id` INT(11) NULL,
  `observacao` TEXT NULL,
  `usuarios_id` INT(11) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_cobranca_id` (`cobranca_id`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_cliente_id` (`cliente_id`),
  INDEX `idx_data_competencia` (`data_competencia`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;