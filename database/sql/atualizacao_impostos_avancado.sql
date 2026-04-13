-- =============================================
-- MIGRAÇÃO: Sistema de Impostos Avançado MAP-OS
-- Cria tabelas adicionais necessárias para o módulo de impostos
-- =============================================

-- Tabela: config_sistema_impostos (configurações do sistema)
CREATE TABLE IF NOT EXISTS `config_sistema_impostos` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `chave` VARCHAR(100) NOT NULL,
    `valor` TEXT NULL,
    `updated_at` DATETIME NOT NULL,
    UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir configurações padrão
INSERT INTO `config_sistema_impostos` (`chave`, `valor`, `updated_at`) VALUES
('IMPOSTO_RETENCAO_AUTOMATICA', '0', NOW()),
('IMPOSTO_ANEXO_PADRAO', 'III', NOW()),
('IMPOSTO_FAIXA_ATUAL', '1', NOW()),
('IMPOSTO_ISS_MUNICIPAL', '5.00', NOW()),
('IMPOSTO_DRE_INTEGRACAO', '0', NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Tabela: impostos_config_simples (alíquotas do Simples Nacional)
CREATE TABLE IF NOT EXISTS `impostos_config_simples` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `anexo` VARCHAR(10) NOT NULL COMMENT 'I, II, III, IV ou V',
    `faixa` INT(11) NOT NULL COMMENT '1 a 5',
    `aliquota_nominal` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `irpj` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `csll` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `cofins` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `pis` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `cpp` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `iss` DECIMAL(5,2) NOT NULL DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    INDEX `idx_anexo` (`anexo`),
    INDEX `idx_faixa` (`faixa`),
    INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir alíquotas do Anexo III (Serviços) - Simples Nacional 2024
INSERT INTO `impostos_config_simples` (`anexo`, `faixa`, `aliquota_nominal`, `irpj`, `csll`, `cofins`, `pis`, `cpp`, `iss`, `ativo`, `created_at`, `updated_at`) VALUES
-- 1ª Faixa: até R$ 180.000
('III', 1, 6.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NOW(), NOW()),
-- 2ª Faixa: R$ 180.001 a R$ 360.000
('III', 2, 11.20, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NOW(), NOW()),
-- 3ª Faixa: R$ 360.001 a R$ 720.000
('III', 3, 13.95, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NOW(), NOW()),
-- 4ª Faixa: R$ 720.001 a R$ 1.800.000
('III', 4, 16.17, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NOW(), NOW()),
-- 5ª Faixa: R$ 1.800.001 a R$ 4.800.000
('III', 5, 18.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Tabela: impostos_retidos_v2 (retenções automáticas com dados completos)
-- Versão estendida da tabela existente para suportar retenção automática
CREATE TABLE IF NOT EXISTS `impostos_retidos_v2` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cobranca_id` INT(11) UNSIGNED NULL,
    `os_id` INT(11) UNSIGNED NULL,
    `venda_id` INT(11) UNSIGNED NULL,
    `cliente_id` INT(11) NOT NULL,
    `valor_bruto` DECIMAL(15,2) NOT NULL,
    `valor_liquido` DECIMAL(15,2) NOT NULL,
    `aliquota_aplicada` DECIMAL(5,2) NOT NULL,
    `irpj_valor` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `csll_valor` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `cofins_valor` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `pis_valor` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `iss_valor` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `total_impostos` DECIMAL(10,2) NOT NULL,
    `data_competencia` DATE NOT NULL,
    `data_retencao` DATETIME NOT NULL,
    `nota_fiscal` VARCHAR(50) NULL,
    `status` ENUM('Retido','Estornado','Cancelado') NOT NULL DEFAULT 'Retido',
    `observacao` TEXT NULL,
    `dre_lancamento_id` INT(11) UNSIGNED NULL,
    `usuarios_id` INT(11) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    INDEX `idx_cobranca` (`cobranca_id`),
    INDEX `idx_os` (`os_id`),
    INDEX `idx_venda` (`venda_id`),
    INDEX `idx_cliente` (`cliente_id`),
    INDEX `idx_data_competencia` (`data_competencia`),
    INDEX `idx_status` (`status`),
    INDEX `idx_dre_lancamento` (`dre_lancamento_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- FIM DA MIGRAÇÃO
-- =============================================
