-- ============================================================
-- MapOS V5 - Script de Atualizacao do Banco de Dados
-- IDEMPOTENTE: seguro para executar multiplas vezes
-- Banco: MySQL 8.0+ / MariaDB 10.3+
-- Charset: utf8mb4
-- ============================================================
-- Execute este script no phpMyAdmin ou cliente MySQL do Kinghost
-- As instrucoes usam IF NOT EXISTS / procedimentos seguros
-- ============================================================

SET NAMES utf8mb4;
USE `jjferreiras05`;
SET FOREIGN_KEY_CHECKS = 0;


-- ============================================================
-- MIGRATION: 20260417000001 - Consolidated Schema Update
-- ============================================================

-- --- cobrancas ---
CREATE TABLE IF NOT EXISTS `cobrancas` (
    `idCobranca` INT(11) NOT NULL AUTO_INCREMENT,
    `charge_id` VARCHAR(255) DEFAULT NULL,
    `conditional_discount_date` DATE DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `custom_id` INT(11) DEFAULT NULL,
    `expire_at` DATE DEFAULT NULL,
    `paid_at` DATETIME DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `payment_method` VARCHAR(11) DEFAULT NULL,
    `payment_url` VARCHAR(255) DEFAULT NULL,
    `request_delivery_address` VARCHAR(64) DEFAULT NULL,
    `status` VARCHAR(36) DEFAULT NULL,
    `total` VARCHAR(15) DEFAULT NULL,
    `barcode` VARCHAR(255) DEFAULT NULL,
    `linha_digitavel` VARCHAR(255) DEFAULT NULL,
    `link` VARCHAR(255) DEFAULT NULL,
    `pix_code` TEXT DEFAULT NULL,
    `payment_gateway` VARCHAR(255) DEFAULT NULL,
    `payment` VARCHAR(64) DEFAULT NULL,
    `pdf` VARCHAR(255) DEFAULT NULL,
    `vendas_id` INT(11) DEFAULT NULL,
    `os_id` INT(11) DEFAULT NULL,
    `clientes_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`idCobranca`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add column `linha_digitavel` to `cobrancas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND COLUMN_NAME = 'linha_digitavel');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD COLUMN `linha_digitavel` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `pix_code` to `cobrancas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND COLUMN_NAME = 'pix_code');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD COLUMN `pix_code` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `paid_at` to `cobrancas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND COLUMN_NAME = 'paid_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD COLUMN `paid_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `updated_at` to `cobrancas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND COLUMN_NAME = 'updated_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD COLUMN `updated_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_cobrancas_charge_id` to `cobrancas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND INDEX_NAME = 'idx_cobrancas_charge_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD INDEX `idx_cobrancas_charge_id` (`charge_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_cobrancas_status_gateway` to `cobrancas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND INDEX_NAME = 'idx_cobrancas_status_gateway');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD INDEX `idx_cobrancas_status_gateway` (`status`, `payment_gateway`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- os ---
-- Add column `tecnico_responsavel` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'tecnico_responsavel');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `tecnico_responsavel` INT(11) DEFAULT NULL COMMENT 'ID do usuario tecnico responsavel pela OS''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `nfse_status` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'nfse_status');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `nfse_status` ENUM('Pendente','Emitida','Cancelada') DEFAULT 'Pendente' COMMENT 'Status da NFS-e vinculada''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `boleto_status` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'boleto_status');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `boleto_status` ENUM('Pendente','Emitido','Pago','Vencido','Cancelado') DEFAULT 'Pendente' COMMENT 'Status do boleto vinculado''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `data_vencimento_boleto` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'data_vencimento_boleto');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `data_vencimento_boleto` DATE DEFAULT NULL COMMENT 'Data de vencimento do boleto''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `valor_com_impostos` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'valor_com_impostos');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `valor_com_impostos` DECIMAL(15,2) DEFAULT NULL COMMENT 'Valor liquido apos deducao de impostos''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `certificado_vinculado` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'certificado_vinculado');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `certificado_vinculado` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `retencao_impostos` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'retencao_impostos');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `retencao_impostos` TINYINT(1) DEFAULT 0'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `calculo_impostos` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'calculo_impostos');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `calculo_impostos` TEXT DEFAULT NULL COMMENT 'JSON com detalhes dos impostos calculados''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `obra_id` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'obra_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `obra_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID da obra vinculada''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_tecnico_responsavel` to `os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND INDEX_NAME = 'idx_tecnico_responsavel');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os` ADD INDEX `idx_tecnico_responsavel` (`tecnico_responsavel`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_obra_id` to `os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND INDEX_NAME = 'idx_obra_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os` ADD INDEX `idx_obra_id` (`obra_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `status` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'status');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `status` VARCHAR(45) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- os_nfse_emitida ---
CREATE TABLE IF NOT EXISTS `os_nfse_emitida` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL COMMENT 'ID da OS vinculada',
    `numero_nfse` VARCHAR(20) DEFAULT NULL,
    `chave_acesso` VARCHAR(50) DEFAULT NULL,
    `data_emissao` DATETIME DEFAULT NULL,
    `valor_servicos` DECIMAL(15,2) DEFAULT 0.00,
    `valor_deducoes` DECIMAL(15,2) DEFAULT 0.00,
    `valor_liquido` DECIMAL(15,2) DEFAULT 0.00,
    `aliquota_iss` DECIMAL(5,2) DEFAULT 0.00,
    `valor_iss` DECIMAL(15,2) DEFAULT 0.00,
    `valor_inss` DECIMAL(15,2) DEFAULT 0.00,
    `valor_irrf` DECIMAL(15,2) DEFAULT 0.00,
    `valor_csll` DECIMAL(15,2) DEFAULT 0.00,
    `valor_pis` DECIMAL(15,2) DEFAULT 0.00,
    `valor_cofins` DECIMAL(15,2) DEFAULT 0.00,
    `valor_total_impostos` DECIMAL(15,2) DEFAULT 0.00,
    `situacao` ENUM('Pendente','Emitida','Cancelada','Substituida') DEFAULT 'Pendente',
    `codigo_verificacao` VARCHAR(20) DEFAULT NULL,
    `link_impressao` VARCHAR(500) DEFAULT NULL,
    `xml_path` VARCHAR(500) DEFAULT NULL,
    `protocolo` VARCHAR(50) DEFAULT NULL,
    `mensagem_retorno` TEXT DEFAULT NULL,
    `cobranca_id` INT(11) DEFAULT NULL,
    `emitido_por` INT(11) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `n_dps` VARCHAR(50) DEFAULT NULL,
    `ambiente` ENUM('homologacao','producao') DEFAULT 'homologacao',
    `regime_tributario` ENUM('simples_nacional','lucro_presumido','lucro_real') DEFAULT 'simples_nacional' COMMENT 'Regime tributario',
    `valor_das` DECIMAL(15,2) DEFAULT NULL COMMENT 'Valor estimado do DAS',
    `retem_iss` TINYINT(1) DEFAULT 0 COMMENT 'Tomador retém ISS',
    `retem_irrf` TINYINT(1) DEFAULT 0 COMMENT 'Tomador retém IRRF',
    `retem_pis` TINYINT(1) DEFAULT 0 COMMENT 'Tomador retém PIS',
    `retem_cofins` TINYINT(1) DEFAULT 0 COMMENT 'Tomador retém COFINS',
    `retem_csll` TINYINT(1) DEFAULT 0 COMMENT 'Tomador retém CSLL',
    `valor_retencao_iss` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Valor ISS retido',
    `valor_retencao_irrf` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Valor IRRF retido',
    `valor_retencao_pis` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Valor PIS retido',
    `valor_retencao_cofins` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Valor COFINS retido',
    `valor_retencao_csll` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Valor CSLL retido',
    `valor_total_retencao` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total das retenções',
    `competencia` DATE DEFAULT NULL COMMENT 'Mes/ano de competencia',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- lancamentos ---
-- Add column `observacoes` to `lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lancamentos' AND COLUMN_NAME = 'observacoes');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `lancamentos` ADD COLUMN `observacoes` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `webhook_notificado` to `lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lancamentos' AND COLUMN_NAME = 'webhook_notificado');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `lancamentos` ADD COLUMN `webhook_notificado` TINYINT(1) DEFAULT 0'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- usuarios ---
-- Add column `is_tecnico` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'is_tecnico');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `is_tecnico` TINYINT(1) DEFAULT 0 COMMENT 'Indica se e tecnico de campo''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `nivel_tecnico` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'nivel_tecnico');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `nivel_tecnico` ENUM('I','II','III','IV') DEFAULT 'II' COMMENT 'Nivel do tecnico''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `especialidades` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'especialidades');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `especialidades` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `veiculo_placa` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'veiculo_placa');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `veiculo_placa` VARCHAR(10) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `veiculo_tipo` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'veiculo_tipo');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `veiculo_tipo` ENUM('Moto','Carro','Nenhum') DEFAULT 'Nenhum''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `coordenadas_base_lat` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'coordenadas_base_lat');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `coordenadas_base_lat` DECIMAL(10,8) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `coordenadas_base_lng` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'coordenadas_base_lng');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `coordenadas_base_lng` DECIMAL(11,8) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `raio_atuacao_km` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'raio_atuacao_km');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `raio_atuacao_km` INT DEFAULT 50'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `plantao_24h` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'plantao_24h');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `plantao_24h` TINYINT(1) DEFAULT 0'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `app_tecnico_instalado` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'app_tecnico_instalado');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `app_tecnico_instalado` TINYINT(1) DEFAULT 0'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `token_app` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'token_app');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `token_app` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `token_expira` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'token_expira');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `token_expira` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `ultimo_acesso_app` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'ultimo_acesso_app');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `ultimo_acesso_app` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `foto_tecnico` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'foto_tecnico');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `foto_tecnico` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- clientes ---
-- Add column `contato` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'contato');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `contato` VARCHAR(45) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `complemento` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'complemento');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `complemento` VARCHAR(45) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `fornecedor` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'fornecedor');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `fornecedor` BOOLEAN DEFAULT 0'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `senha` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'senha');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `senha` VARCHAR(200) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `asaas_id` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'asaas_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `asaas_id` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- vendas ---
-- Add column `observacoes` to `vendas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vendas' AND COLUMN_NAME = 'observacoes');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `vendas` ADD COLUMN `observacoes` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `observacoes_cliente` to `vendas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vendas' AND COLUMN_NAME = 'observacoes_cliente');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `vendas` ADD COLUMN `observacoes_cliente` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `garantia` to `vendas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vendas' AND COLUMN_NAME = 'garantia');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `vendas` ADD COLUMN `garantia` VARCHAR(45) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `status` to `vendas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vendas' AND COLUMN_NAME = 'status');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `vendas` ADD COLUMN `status` VARCHAR(45) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- emitente ---
-- Add column `cep` to `emitente` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'emitente' AND COLUMN_NAME = 'cep');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `emitente` ADD COLUMN `cep` VARCHAR(20) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `inscricao_municipal` to `emitente` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'emitente' AND COLUMN_NAME = 'inscricao_municipal');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `emitente` ADD COLUMN `inscricao_municipal` VARCHAR(50) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- resets_de_senha ---
CREATE TABLE IF NOT EXISTS `resets_de_senha` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(200) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `data_expiracao` DATETIME NOT NULL,
    `token_utilizado` TINYINT DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- email_queue ---
CREATE TABLE IF NOT EXISTS `email_queue` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `to_email` VARCHAR(255) NOT NULL,
    `to_name` VARCHAR(255) DEFAULT NULL,
    `subject` VARCHAR(500) NOT NULL,
    `body_html` LONGTEXT DEFAULT NULL,
    `body_text` LONGTEXT DEFAULT NULL,
    `template` VARCHAR(100) DEFAULT NULL,
    `template_data` TEXT DEFAULT NULL,
    `attachments` TEXT DEFAULT NULL,
    `cc` TEXT DEFAULT NULL,
    `bcc` TEXT DEFAULT NULL,
    `priority` TINYINT(1) DEFAULT 3,
    `status` ENUM('pending','processing','sent','failed','cancelled','scheduled') DEFAULT 'pending',
    `attempts` TINYINT(1) DEFAULT 0,
    `max_retries` TINYINT(1) DEFAULT 3,
    `tracking_id` VARCHAR(32) DEFAULT NULL,
    `message_id` VARCHAR(255) DEFAULT NULL,
    `scheduled_at` DATETIME DEFAULT NULL,
    `sent_at` DATETIME DEFAULT NULL,
    `opened_at` DATETIME DEFAULT NULL,
    `clicked_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    `last_attempt` DATETIME DEFAULT NULL,
    `failed_at` DATETIME DEFAULT NULL,
    `error_message` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- email_tracking ---
CREATE TABLE IF NOT EXISTS `email_tracking` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email_queue_id` INT(11) UNSIGNED NOT NULL,
    `tracking_id` VARCHAR(64) NOT NULL,
    `opened` TINYINT(1) DEFAULT 0,
    `opened_at` DATETIME DEFAULT NULL,
    `clicked` TINYINT(1) DEFAULT 0,
    `clicked_at` DATETIME DEFAULT NULL,
    `clicked_url` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- email_clicks ---
CREATE TABLE IF NOT EXISTS `email_clicks` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tracking_id` VARCHAR(32) NOT NULL,
    `url` TEXT NOT NULL,
    `clicked_at` DATETIME NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- email_blacklist ---
CREATE TABLE IF NOT EXISTS `email_blacklist` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `motivo` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- scheduled_events ---
CREATE TABLE IF NOT EXISTS `scheduled_events` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_type` VARCHAR(100) NOT NULL,
    `event_data` JSON DEFAULT NULL,
    `execute_at` DATETIME NOT NULL,
    `status` ENUM('pending','completed','failed') DEFAULT 'pending',
    `executed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- webhooks ---
CREATE TABLE IF NOT EXISTS `webhooks` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `secret` VARCHAR(255) DEFAULT NULL,
    `events` JSON DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- webhook_logs ---
CREATE TABLE IF NOT EXISTS `webhook_logs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `webhook_id` INT(11) UNSIGNED NOT NULL,
    `event_type` VARCHAR(100) NOT NULL,
    `payload` TEXT DEFAULT NULL,
    `response` TEXT DEFAULT NULL,
    `status_code` INT DEFAULT NULL,
    `success` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- certificado_config ---
CREATE TABLE IF NOT EXISTS `certificado_config` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_emitente` INT(11) UNSIGNED NOT NULL,
    `certificado_p12` LONGTEXT DEFAULT NULL,
    `senha_certificado` VARCHAR(255) DEFAULT NULL,
    `cnpj_certificado` VARCHAR(14) DEFAULT NULL,
    `valido_de` DATETIME DEFAULT NULL,
    `valido_ate` DATETIME DEFAULT NULL,
    `arquivo_crt` LONGTEXT DEFAULT NULL,
    `arquivo_key` LONGTEXT DEFAULT NULL,
    `ambiente` ENUM('homologacao','producao') DEFAULT 'homologacao',
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- nfse_importada ---
CREATE TABLE IF NOT EXISTS `nfse_importada` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_os` INT(11) UNSIGNED DEFAULT NULL,
    `numero_nfse` VARCHAR(50) NOT NULL,
    `codigo_verificacao` VARCHAR(50) DEFAULT NULL,
    `data_emissao` DATETIME DEFAULT NULL,
    `valor_servico` DECIMAL(10,2) DEFAULT NULL,
    `valor_liquido` DECIMAL(10,2) DEFAULT NULL,
    `prestador_cnpj` VARCHAR(14) DEFAULT NULL,
    `prestador_nome` VARCHAR(255) DEFAULT NULL,
    `tomador_cnpj` VARCHAR(14) DEFAULT NULL,
    `tomador_nome` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('ativa','cancelada') DEFAULT 'ativa',
    `xml_content` LONGTEXT DEFAULT NULL,
    `pdf_content` LONGTEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- push_notifications ---
CREATE TABLE IF NOT EXISTS `push_notifications` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `data` JSON DEFAULT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- notificacoes ---
CREATE TABLE IF NOT EXISTS `notificacoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) NOT NULL,
    `titulo` VARCHAR(200) NOT NULL,
    `mensagem` TEXT NOT NULL,
    `url` VARCHAR(500) DEFAULT NULL,
    `icone` VARCHAR(50) DEFAULT 'bx-bell',
    `tipo` VARCHAR(30) DEFAULT 'info',
    `lida` TINYINT(1) DEFAULT 0,
    `data_notificacao` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_usuario_lida` to `notificacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notificacoes' AND INDEX_NAME = 'idx_usuario_lida');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `notificacoes` ADD INDEX `idx_usuario_lida` (`usuario_id`, `lida`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_data` to `notificacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notificacoes' AND INDEX_NAME = 'idx_data');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `notificacoes` ADD INDEX `idx_data` (`data_notificacao`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- checkin ---
CREATE TABLE IF NOT EXISTS `checkin` (
    `idCheckin` INT NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `tecnico_id` INT(11) NOT NULL,
    `tipo` ENUM('inicio','pausa','retorno','finalizacao','checkin','checkout') NOT NULL,
    `data_hora` DATETIME NOT NULL,
    `observacao` TEXT DEFAULT NULL,
    `foto` VARCHAR(255) DEFAULT NULL,
    `latitude` DECIMAL(10,8) DEFAULT NULL,
    `longitude` DECIMAL(11,8) DEFAULT NULL,
    `localizacao` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`idCheckin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_os_id` to `checkin` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'checkin' AND INDEX_NAME = 'idx_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `checkin` ADD INDEX `idx_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- os_checkin ---
CREATE TABLE IF NOT EXISTS `os_checkin` (
    `idCheckin` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `usuarios_id` INT(11) NOT NULL,
    `data_entrada` DATETIME DEFAULT NULL,
    `data_saida` DATETIME DEFAULT NULL,
    `latitude_entrada` DECIMAL(10,8) DEFAULT NULL,
    `longitude_entrada` DECIMAL(11,8) DEFAULT NULL,
    `latitude_saida` DECIMAL(10,8) DEFAULT NULL,
    `longitude_saida` DECIMAL(11,8) DEFAULT NULL,
    `observacao_entrada` TEXT DEFAULT NULL,
    `observacao_saida` TEXT DEFAULT NULL,
    `status` VARCHAR(30) DEFAULT 'Em Andamento',
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `data_atualizacao` DATETIME DEFAULT NULL,
    PRIMARY KEY (`idCheckin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_os_id` to `os_checkin` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os_checkin' AND INDEX_NAME = 'idx_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os_checkin` ADD INDEX `idx_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_status` to `os_checkin` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os_checkin' AND INDEX_NAME = 'idx_status');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os_checkin` ADD INDEX `idx_status` (`status`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- os_assinaturas ---
CREATE TABLE IF NOT EXISTS `os_assinaturas` (
    `idAssinatura` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `checkin_id` INT(11) DEFAULT NULL,
    `tipo` VARCHAR(20) DEFAULT NULL COMMENT 'tecnico_entrada, tecnico_saida, cliente_saida',
    `assinatura` VARCHAR(255) DEFAULT NULL COMMENT 'Caminho da imagem da assinatura',
    `nome_assinante` VARCHAR(100) DEFAULT NULL,
    `documento_assinante` VARCHAR(20) DEFAULT NULL,
    `data_assinatura` DATETIME NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idAssinatura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_os_id` to `os_assinaturas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os_assinaturas' AND INDEX_NAME = 'idx_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os_assinaturas` ADD INDEX `idx_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_tipo` to `os_assinaturas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os_assinaturas' AND INDEX_NAME = 'idx_tipo');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os_assinaturas` ADD INDEX `idx_tipo` (`tipo`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- os_fotos_atendimento ---
CREATE TABLE IF NOT EXISTS `os_fotos_atendimento` (
    `idFoto` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `checkin_id` INT(11) DEFAULT NULL,
    `usuarios_id` INT(11) NOT NULL,
    `arquivo` VARCHAR(255) NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `etapa` VARCHAR(20) DEFAULT 'durante',
    `tamanho` INT(11) DEFAULT NULL,
    `tipo_arquivo` VARCHAR(10) DEFAULT NULL,
    `imagem_base64` LONGTEXT DEFAULT NULL,
    `mime_type` VARCHAR(30) DEFAULT NULL,
    `data_upload` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idFoto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_os_id` to `os_fotos_atendimento` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os_fotos_atendimento' AND INDEX_NAME = 'idx_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os_fotos_atendimento` ADD INDEX `idx_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_etapa` to `os_fotos_atendimento` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os_fotos_atendimento' AND INDEX_NAME = 'idx_etapa');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os_fotos_atendimento` ADD INDEX `idx_etapa` (`etapa`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- fotos_atendimento ---
CREATE TABLE IF NOT EXISTS `fotos_atendimento` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `checkin_id` INT(11) NOT NULL,
    `os_id` INT(11) NOT NULL,
    `imagem` LONGBLOB NOT NULL,
    `imagem_base64` LONGTEXT DEFAULT NULL,
    `mime_type` VARCHAR(30) DEFAULT NULL,
    `tipo` ENUM('antes','depois','assinatura','outro') DEFAULT 'outro',
    `data` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- os_tecnico_atribuicao ---
CREATE TABLE IF NOT EXISTS `os_tecnico_atribuicao` (
    `idAtribuicao` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL COMMENT 'ID da OS',
    `tecnico_id` INT(11) NOT NULL COMMENT 'ID do tecnico atribuido',
    `atribuido_por` INT(11) NOT NULL COMMENT 'ID do usuario que fez a atribuicao',
    `data_atribuicao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `data_remocao` DATETIME DEFAULT NULL,
    `motivo_remocao` TEXT DEFAULT NULL,
    `observacao` TEXT DEFAULT NULL,
    PRIMARY KEY (`idAtribuicao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- os_status_history ---
CREATE TABLE IF NOT EXISTS `os_status_history` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `status_antigo` VARCHAR(45) DEFAULT NULL,
    `status_novo` VARCHAR(45) NOT NULL,
    `usuario_id` INT(11) DEFAULT NULL,
    `observacao` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- anotacoes_os ---
CREATE TABLE IF NOT EXISTS `anotacoes_os` (
    `idAnotacoes` INT(11) NOT NULL AUTO_INCREMENT,
    `anotacao` VARCHAR(255) NOT NULL,
    `data_hora` DATETIME NOT NULL,
    `os_id` INT(11) NOT NULL,
    PRIMARY KEY (`idAnotacoes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- os_boleto_emitido ---
CREATE TABLE IF NOT EXISTS `os_boleto_emitido` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `nfse_id` INT(11) UNSIGNED DEFAULT NULL,
    `nosso_numero` VARCHAR(50) DEFAULT NULL,
    `linha_digitavel` VARCHAR(60) DEFAULT NULL,
    `codigo_barras` VARCHAR(44) DEFAULT NULL,
    `data_emissao` DATE DEFAULT NULL,
    `data_vencimento` DATE DEFAULT NULL,
    `data_pagamento` DATE DEFAULT NULL,
    `valor_original` DECIMAL(15,2) DEFAULT 0.00,
    `valor_desconto_impostos` DECIMAL(15,2) DEFAULT 0.00,
    `valor_liquido` DECIMAL(15,2) DEFAULT 0.00,
    `valor_pago` DECIMAL(15,2) DEFAULT 0.00,
    `multa` DECIMAL(15,2) DEFAULT 0.00,
    `juros` DECIMAL(15,2) DEFAULT 0.00,
    `status` ENUM('Pendente','Emitido','Pago','Vencido','Cancelado') DEFAULT 'Pendente',
    `instrucoes` TEXT DEFAULT NULL,
    `sacado_nome` VARCHAR(255) DEFAULT NULL,
    `sacado_documento` VARCHAR(20) DEFAULT NULL,
    `sacado_endereco` VARCHAR(500) DEFAULT NULL,
    `pdf_path` VARCHAR(500) DEFAULT NULL,
    `remessa_id` INT(11) DEFAULT NULL,
    `retorno_id` INT(11) DEFAULT NULL,
    `gateway` VARCHAR(50) DEFAULT NULL,
    `gateway_transaction_id` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- usuarios_cliente ---
CREATE TABLE IF NOT EXISTS `usuarios_cliente` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id` INT(11) DEFAULT NULL COMMENT 'ID do cliente vinculado',
    `nome` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `telefone` VARCHAR(20) DEFAULT NULL,
    `celular` VARCHAR(20) DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `ultimo_acesso` DATETIME DEFAULT NULL,
    `token_reset` VARCHAR(255) DEFAULT NULL,
    `token_expira` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_cliente_id` to `usuarios_cliente` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios_cliente' AND INDEX_NAME = 'idx_cliente_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `usuarios_cliente` ADD INDEX `idx_cliente_id` (`cliente_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- usuarios_cliente_cnpjs ---
CREATE TABLE IF NOT EXISTS `usuarios_cliente_cnpjs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_cliente_id` INT(11) NOT NULL,
    `cnpj` VARCHAR(18) NOT NULL COMMENT 'CNPJ formato 00.000.000/0000-00',
    `razao_social` VARCHAR(255) DEFAULT NULL,
    `nome_fantasia` VARCHAR(255) DEFAULT NULL,
    `principal` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_usuario_cnpj` to `usuarios_cliente_cnpjs` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios_cliente_cnpjs' AND INDEX_NAME = 'idx_usuario_cnpj');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `usuarios_cliente_cnpjs` ADD INDEX `idx_usuario_cnpj` (`usuario_cliente_id`, `cnpj`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- usuarios_cliente_permissoes ---
CREATE TABLE IF NOT EXISTS `usuarios_cliente_permissoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_cliente_id` INT(11) NOT NULL,
    `chave` VARCHAR(100) NOT NULL COMMENT 'Nome da permissao/configuracao',
    `valor` TEXT DEFAULT NULL COMMENT 'Valor da configuracao',
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_usuario_chave` to `usuarios_cliente_permissoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios_cliente_permissoes' AND INDEX_NAME = 'idx_usuario_chave');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `usuarios_cliente_permissoes` ADD INDEX `idx_usuario_chave` (`usuario_cliente_id`, `chave`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- os_documentos ---
CREATE TABLE IF NOT EXISTS `os_documentos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) UNSIGNED NOT NULL,
    `tipo` ENUM('boleto','nfse','nfe','nfce','recibo','contrato','outro') NOT NULL,
    `descricao` VARCHAR(255) DEFAULT NULL,
    `numero_documento` VARCHAR(100) DEFAULT NULL,
    `valor` DECIMAL(15,2) DEFAULT NULL,
    `data_emissao` DATE DEFAULT NULL,
    `data_vencimento` DATE DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT NULL,
    `arquivo` VARCHAR(500) DEFAULT NULL,
    `link_externo` TEXT DEFAULT NULL,
    `gateway_id` VARCHAR(100) DEFAULT NULL,
    `charge_id` VARCHAR(100) DEFAULT NULL,
    `nfse_id` INT(11) UNSIGNED DEFAULT NULL,
    `observacoes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- dre_contas ---
CREATE TABLE IF NOT EXISTS `dre_contas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `codigo` VARCHAR(50) NOT NULL,
    `nome` VARCHAR(255) NOT NULL,
    `tipo` ENUM('receita','custo','despesa') NOT NULL,
    `grupo` VARCHAR(100) DEFAULT NULL,
    `sinal` ENUM('POSITIVO','NEGATIVO') DEFAULT 'POSITIVO',
    `conta_pai_id` INT(11) UNSIGNED DEFAULT NULL,
    `nivel` INT DEFAULT 1,
    `ordem` INT DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_tipo` to `dre_contas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_contas' AND INDEX_NAME = 'idx_tipo');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `dre_contas` ADD INDEX `idx_tipo` (`tipo`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_ativo` to `dre_contas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_contas' AND INDEX_NAME = 'idx_ativo');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `dre_contas` ADD INDEX `idx_ativo` (`ativo`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- dre_lancamentos ---
CREATE TABLE IF NOT EXISTS `dre_lancamentos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `conta_id` INT(11) UNSIGNED NOT NULL,
    `data` DATE NOT NULL,
    `valor` DECIMAL(15,2) NOT NULL,
    `tipo_movimento` ENUM('CREDITO','DEBITO') DEFAULT 'CREDITO',
    `descricao` TEXT DEFAULT NULL,
    `documento` VARCHAR(100) DEFAULT NULL,
    `os_id` INT(11) UNSIGNED DEFAULT NULL,
    `venda_id` INT(11) UNSIGNED DEFAULT NULL,
    `lancamento_id` INT(11) UNSIGNED DEFAULT NULL,
    `usuarios_id` INT(11) UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add column `os_id` to `dre_lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'os_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `os_id` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `venda_id` to `dre_lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'venda_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `venda_id` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `lancamento_id` to `dre_lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'lancamento_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `lancamento_id` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `usuarios_id` to `dre_lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'usuarios_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `usuarios_id` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `updated_at` to `dre_lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'updated_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `updated_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `tipo_movimento` to `dre_lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'tipo_movimento');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `tipo_movimento` ENUM('CREDITO','DEBITO') DEFAULT 'CREDITO''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `documento` to `dre_lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'documento');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `documento` VARCHAR(100) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_conta_id` to `dre_lancamentos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND INDEX_NAME = 'idx_conta_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD INDEX `idx_conta_id` (`conta_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_data_referencia` to `dre_lancamentos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND INDEX_NAME = 'idx_data_referencia');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD INDEX `idx_data_referencia` (`data`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_os_id` to `dre_lancamentos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND INDEX_NAME = 'idx_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD INDEX `idx_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- dre_demonstracoes ---
CREATE TABLE IF NOT EXISTS `dre_demonstracoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `data_inicio` DATE NOT NULL,
    `data_fim` DATE NOT NULL,
    `tipo` ENUM('mensal','trimestral','anual') DEFAULT 'mensal',
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_data` to `dre_demonstracoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_demonstracoes' AND INDEX_NAME = 'idx_data');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `dre_demonstracoes` ADD INDEX `idx_data` (`data_inicio`, `data_fim`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_tipo` to `dre_demonstracoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_demonstracoes' AND INDEX_NAME = 'idx_tipo');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `dre_demonstracoes` ADD INDEX `idx_tipo` (`tipo`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- impostos_config ---
CREATE TABLE IF NOT EXISTS `impostos_config` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cnpj` VARCHAR(18) NOT NULL,
    `razao_social` VARCHAR(255) DEFAULT NULL,
    `anexo_simples` ENUM('I','II','III','IV','V') DEFAULT 'III',
    `faixa_simples` TINYINT(1) DEFAULT 1,
    `aliquota_simples` DECIMAL(5,2) DEFAULT 6.00,
    `retencao_iss` TINYINT(1) DEFAULT 0,
    `aliquota_iss` DECIMAL(5,2) DEFAULT 2.00,
    `retencao_pis` TINYINT(1) DEFAULT 0,
    `aliquota_pis` DECIMAL(5,2) DEFAULT 0.65,
    `retencao_cofins` TINYINT(1) DEFAULT 0,
    `aliquota_cofins` DECIMAL(5,2) DEFAULT 3.00,
    `retencao_csll` TINYINT(1) DEFAULT 0,
    `aliquota_csll` DECIMAL(5,2) DEFAULT 1.00,
    `retencao_inss` TINYINT(1) DEFAULT 0,
    `aliquota_inss` DECIMAL(5,2) DEFAULT 11.00,
    `retencao_ir` TINYINT(1) DEFAULT 0,
    `aliquota_ir` DECIMAL(5,2) DEFAULT 1.50,
    `valor_minimo_retencao` DECIMAL(15,2) DEFAULT 0.00,
    `ativar_retencao_automatica` TINYINT(1) DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- impostos_retidos ---
CREATE TABLE IF NOT EXISTS `impostos_retidos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_os` INT(11) UNSIGNED DEFAULT NULL,
    `id_venda` INT(11) UNSIGNED DEFAULT NULL,
    `tipo_imposto` ENUM('iss','pis','cofins','csll','ir','inss') NOT NULL,
    `base_calculo` DECIMAL(15,2) NOT NULL,
    `aliquota` DECIMAL(5,2) NOT NULL,
    `valor_retido` DECIMAL(15,2) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_id_os` to `impostos_retidos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'impostos_retidos' AND INDEX_NAME = 'idx_id_os');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `impostos_retidos` ADD INDEX `idx_id_os` (`id_os`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_id_venda` to `impostos_retidos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'impostos_retidos' AND INDEX_NAME = 'idx_id_venda');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `impostos_retidos` ADD INDEX `idx_id_venda` (`id_venda`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_tipo_imposto` to `impostos_retidos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'impostos_retidos' AND INDEX_NAME = 'idx_tipo_imposto');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `impostos_retidos` ADD INDEX `idx_tipo_imposto` (`tipo_imposto`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- --- configuracoes_impostos ---
CREATE TABLE IF NOT EXISTS `configuracoes_impostos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cnpj` VARCHAR(18) NOT NULL,
    `razao_social` VARCHAR(255) DEFAULT NULL,
    `anexo_simples` ENUM('I','II','III','IV','V') DEFAULT 'III',
    `faixa_simples` TINYINT(1) DEFAULT 1,
    `aliquota_simples` DECIMAL(5,2) DEFAULT 6.00,
    `retencao_iss` TINYINT(1) DEFAULT 0,
    `aliquota_iss` DECIMAL(5,2) DEFAULT 2.00,
    `retencao_pis` TINYINT(1) DEFAULT 0,
    `aliquota_pis` DECIMAL(5,2) DEFAULT 0.65,
    `retencao_cofins` TINYINT(1) DEFAULT 0,
    `aliquota_cofins` DECIMAL(5,2) DEFAULT 3.00,
    `retencao_csll` TINYINT(1) DEFAULT 0,
    `aliquota_csll` DECIMAL(5,2) DEFAULT 1.00,
    `retencao_inss` TINYINT(1) DEFAULT 0,
    `aliquota_inss` DECIMAL(5,2) DEFAULT 11.00,
    `retencao_ir` TINYINT(1) DEFAULT 0,
    `aliquota_ir` DECIMAL(5,2) DEFAULT 1.50,
    `valor_minimo_retencao` DECIMAL(15,2) DEFAULT 0.00,
    `ativar_retencao_automatica` TINYINT(1) DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- calculos_impostos ---
CREATE TABLE IF NOT EXISTS `calculos_impostos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cnpj` VARCHAR(18) NOT NULL,
    `os_id` INT(11) UNSIGNED DEFAULT NULL,
    `venda_id` INT(11) UNSIGNED DEFAULT NULL,
    `cobranca_id` INT(11) UNSIGNED DEFAULT NULL,
    `valor_bruto` DECIMAL(15,2) NOT NULL,
    `valor_liquido` DECIMAL(15,2) NOT NULL,
    `iss` DECIMAL(15,2) DEFAULT 0.00,
    `pis` DECIMAL(15,2) DEFAULT 0.00,
    `cofins` DECIMAL(15,2) DEFAULT 0.00,
    `csll` DECIMAL(15,2) DEFAULT 0.00,
    `inss` DECIMAL(15,2) DEFAULT 0.00,
    `ir` DECIMAL(15,2) DEFAULT 0.00,
    `total_impostos` DECIMAL(15,2) DEFAULT 0.00,
    `aliquota_efetiva` DECIMAL(5,2) DEFAULT NULL,
    `competencia` DATE NOT NULL,
    `status` ENUM('calculado','retido','recolhido','cancelado') DEFAULT 'calculado',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- V5 Tecnico tables ---
CREATE TABLE IF NOT EXISTS `checklist_templates` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `categoria` VARCHAR(50) DEFAULT 'geral',
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `checklist_template_items` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `template_id` INT NOT NULL,
    `ordem` INT DEFAULT 0,
    `descricao` VARCHAR(255) NOT NULL,
    `tipo` VARCHAR(20) DEFAULT 'checkbox',
    `obrigatorio` TINYINT(1) DEFAULT 0,
    `opcoes` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_checklist` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `os_id` INT NOT NULL,
    `template_id` INT DEFAULT NULL,
    `item_id` INT NOT NULL,
    `descricao` VARCHAR(255) NOT NULL,
    `status` ENUM('pendente','ok','nao_aplicavel','com_problema') DEFAULT 'pendente',
    `observacao` TEXT DEFAULT NULL,
    `evidencia_foto` VARCHAR(255) DEFAULT NULL,
    `verificado_por` INT DEFAULT NULL,
    `verificado_at` TIMESTAMP DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_timeline` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `os_id` INT NOT NULL,
    `tipo` VARCHAR(50) NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `usuario_id` INT DEFAULT NULL,
    `usuario_nome` VARCHAR(100) DEFAULT NULL,
    `metadata` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_pecas_utilizadas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `os_id` INT NOT NULL,
    `produto_id` INT DEFAULT NULL,
    `nome_peca` VARCHAR(255) NOT NULL,
    `codigo` VARCHAR(100) DEFAULT NULL,
    `quantidade` DECIMAL(10,2) DEFAULT 1,
    `valor_unitario` DECIMAL(10,2) DEFAULT 0,
    `valor_total` DECIMAL(10,2) DEFAULT 0,
    `tipo` ENUM('produto','servico','insumo','outro') DEFAULT 'produto',
    `instalado_por` INT DEFAULT NULL,
    `instalado_at` TIMESTAMP DEFAULT NULL,
    `garantia_dias` INT DEFAULT 0,
    `observacao` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_etapas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `os_id` INT NOT NULL,
    `etapa` VARCHAR(50) NOT NULL,
    `status` ENUM('pendente','em_andamento','concluida','cancelada') DEFAULT 'pendente',
    `ordem` INT DEFAULT 0,
    `tempo_estimado_minutos` INT DEFAULT NULL,
    `tempo_real_minutos` INT DEFAULT NULL,
    `iniciado_at` TIMESTAMP DEFAULT NULL,
    `concluido_at` TIMESTAMP DEFAULT NULL,
    `responsavel_id` INT DEFAULT NULL,
    `observacao` TEXT DEFAULT NULL,
    `checklist` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tecnico_competencias` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `usuario_id` INT NOT NULL,
    `competencia` VARCHAR(100) NOT NULL,
    `nivel` ENUM('basico','intermediario','avancado','especialista') DEFAULT 'basico',
    `certificado` VARCHAR(255) DEFAULT NULL,
    `validade_certificado` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tecnico_avaliacoes` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `os_id` INT NOT NULL,
    `tecnico_id` INT NOT NULL,
    `cliente_id` INT NOT NULL,
    `nota_geral` INT DEFAULT NULL,
    `nota_atendimento` INT DEFAULT NULL,
    `nota_solucao` INT DEFAULT NULL,
    `nota_tempo` INT DEFAULT NULL,
    `comentario` TEXT DEFAULT NULL,
    `avaliado_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- Catalogo de servicos ---
CREATE TABLE IF NOT EXISTS `servicos_catalogo` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `codigo` VARCHAR(20) NOT NULL,
    `nome` VARCHAR(255) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `categoria` VARCHAR(100) DEFAULT 'Geral',
    `especialidade` VARCHAR(50) DEFAULT NULL,
    `tempo_estimado_minutos` INT DEFAULT 60,
    `checklist_padrao` JSON DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_servicos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `servico_id` INT(11) NOT NULL,
    `quantidade` INT DEFAULT 1,
    `observacao` TEXT DEFAULT NULL,
    `status` ENUM('Pendente','EmExecucao','Concluido','Cancelado') DEFAULT 'Pendente',
    `checklist_execucao` JSON DEFAULT NULL,
    `checklist_completude` INT DEFAULT 0,
    `tecnico_id` INT(11) DEFAULT NULL,
    `data_inicio` DATETIME DEFAULT NULL,
    `data_conclusao` DATETIME DEFAULT NULL,
    `tempo_execucao_minutos` INT DEFAULT NULL,
    `fotos` JSON DEFAULT NULL,
    `assinatura_cliente` TEXT DEFAULT NULL,
    `laudo_tecnico` TEXT DEFAULT NULL,
    `ordem_execucao` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tec_os_execucao` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `tecnico_id` INT(11) NOT NULL,
    `tipo_servico` ENUM('INS','MP','MC','CT','TR','UP','URG') DEFAULT 'MC',
    `especialidade` VARCHAR(50) DEFAULT NULL,
    `checkin_horario` DATETIME DEFAULT NULL,
    `checkin_latitude` DECIMAL(10,8) DEFAULT NULL,
    `checkin_longitude` DECIMAL(11,8) DEFAULT NULL,
    `checkin_endereco` VARCHAR(255) DEFAULT NULL,
    `checkin_foto` VARCHAR(255) DEFAULT NULL,
    `checkin_distancia_metros` INT DEFAULT NULL,
    `checkout_horario` DATETIME DEFAULT NULL,
    `checkout_latitude` DECIMAL(10,8) DEFAULT NULL,
    `checkout_longitude` DECIMAL(11,8) DEFAULT NULL,
    `checkout_endereco` VARCHAR(255) DEFAULT NULL,
    `checkout_foto` VARCHAR(255) DEFAULT NULL,
    `checkout_distancia_metros` INT DEFAULT NULL,
    `tempo_atendimento_minutos` INT DEFAULT NULL,
    `tempo_deslocamento_minutos` INT DEFAULT NULL,
    `km_deslocamento` DECIMAL(10,2) DEFAULT NULL,
    `checklist_json` JSON DEFAULT NULL,
    `checklist_completude` INT DEFAULT 0,
    `fotos_antes` JSON DEFAULT NULL,
    `fotos_depois` JSON DEFAULT NULL,
    `fotos_durante` JSON DEFAULT NULL,
    `fotos_galeria_json` JSON DEFAULT NULL,
    `assinatura_cliente` TEXT DEFAULT NULL,
    `nome_responsavel` VARCHAR(255) DEFAULT NULL,
    `avaliacao` INT DEFAULT NULL,
    `comentario_cliente` TEXT DEFAULT NULL,
    `laudo_tecnico` TEXT DEFAULT NULL,
    `materiais_utilizados` JSON DEFAULT NULL,
    `observacoes_tecnico` TEXT DEFAULT NULL,
    `problema_encontrado` TEXT DEFAULT NULL,
    `solucao_aplicada` TEXT DEFAULT NULL,
    `recomendacoes` TEXT DEFAULT NULL,
    `oportunidade_venda` TINYINT(1) DEFAULT 0,
    `descricao_oportunidade` TEXT DEFAULT NULL,
    `status_execucao` ENUM('Agendada','EmDeslocamento','EmAtendimento','Pausada','Concluida','Cancelada') DEFAULT 'Agendada',
    `aprovada` TINYINT(1) DEFAULT 0,
    `aprovada_por` INT(11) DEFAULT NULL,
    `data_aprovacao` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tec_checklist_template` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tipo_os` VARCHAR(50) DEFAULT NULL,
    `tipo_servico` ENUM('INS','MP','MC','CT','TR','UP') DEFAULT 'MC',
    `nome_template` VARCHAR(100) DEFAULT NULL,
    `itens` JSON DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tec_estoque_veiculo` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tecnico_id` INT(11) NOT NULL,
    `produto_id` INT(11) NOT NULL,
    `quantidade_disponivel` INT DEFAULT 0,
    `quantidade_reservada` INT DEFAULT 0,
    `localizacao` ENUM('Veiculo','EmUso','Retirado') DEFAULT 'Veiculo',
    `ultima_movimentacao` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tec_rotas_tracking` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tecnico_id` INT(11) NOT NULL,
    `data` DATE NOT NULL,
    `pontos_rota` JSON DEFAULT NULL,
    `km_total` DECIMAL(10,2) DEFAULT 0.00,
    `os_atendidas` INT DEFAULT 0,
    `tempo_total_horas` DECIMAL(5,2) DEFAULT 0.00,
    `combustivel_estimado` DECIMAL(10,2) DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tec_estoque_historico` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tecnico_id` INT(11) NOT NULL,
    `produto_id` INT(11) NOT NULL,
    `tipo` ENUM('entrada','saida') NOT NULL,
    `quantidade` INT NOT NULL,
    `os_id` INT(11) DEFAULT NULL,
    `observacao` VARCHAR(255) DEFAULT NULL,
    `data_hora` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `registrado_por` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --- Obras ---
CREATE TABLE IF NOT EXISTS `obras` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `codigo` VARCHAR(50) NOT NULL,
    `nome` VARCHAR(255) NOT NULL,
    `cliente_id` INT(11) NOT NULL,
    `tipo_obra` ENUM('Condominio','Comercio','Residencia','Industrial','Publica') DEFAULT 'Condominio',
    `especialidade_principal` VARCHAR(50) DEFAULT NULL,
    `endereco` TEXT DEFAULT NULL,
    `bairro` VARCHAR(100) DEFAULT NULL,
    `cidade` VARCHAR(100) DEFAULT NULL,
    `estado` VARCHAR(2) DEFAULT NULL,
    `cep` VARCHAR(9) DEFAULT NULL,
    `coordenadas_lat` DECIMAL(10,8) DEFAULT NULL,
    `coordenadas_lng` DECIMAL(11,8) DEFAULT NULL,
    `data_inicio_contrato` DATE DEFAULT NULL,
    `data_fim_prevista` DATE DEFAULT NULL,
    `data_fim_real` DATE DEFAULT NULL,
    `prazo_dias` INT DEFAULT NULL,
    `status` ENUM('Prospeccao','Orcamentacao','Contratada','EmExecucao','Paralisada','Finalizada','Entregue','Garantia') DEFAULT 'Prospeccao',
    `percentual_concluido` INT DEFAULT 0,
    `gestor_obra_id` INT(11) DEFAULT NULL,
    `responsavel_tecnico_id` INT(11) DEFAULT NULL,
    `responsavel_comercial_id` INT(11) DEFAULT NULL,
    `contrato_arquivo` VARCHAR(255) DEFAULT NULL,
    `projeto_arquivo` VARCHAR(255) DEFAULT NULL,
    `art_arquivo` VARCHAR(255) DEFAULT NULL,
    `memorial_descritivo` TEXT DEFAULT NULL,
    `observacoes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `obra_etapas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `obra_id` INT(11) NOT NULL,
    `numero_etapa` INT DEFAULT 1,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `especialidade` VARCHAR(50) DEFAULT NULL,
    `data_inicio_prevista` DATE DEFAULT NULL,
    `data_fim_prevista` DATE DEFAULT NULL,
    `data_inicio_real` DATE DEFAULT NULL,
    `data_fim_real` DATE DEFAULT NULL,
    `percentual_concluido` INT DEFAULT 0,
    `status` ENUM('NaoIniciada','EmAndamento','Concluida','Atrasada','Paralisada') DEFAULT 'NaoIniciada',
    `tecnicos_designados` JSON DEFAULT NULL,
    `os_ids` JSON DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `obra_diario` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `obra_id` INT(11) NOT NULL,
    `data` DATE NOT NULL,
    `clima_manha` ENUM('Sol','Nublado','Chuva','Garoa') DEFAULT NULL,
    `clima_tarde` ENUM('Sol','Nublado','Chuva','Garoa') DEFAULT NULL,
    `equipe_presente` JSON DEFAULT NULL,
    `atividades_executadas` TEXT DEFAULT NULL,
    `etapas_avancadas` JSON DEFAULT NULL,
    `fotos` JSON DEFAULT NULL,
    `problemas` TEXT DEFAULT NULL,
    `acoes_corretivas` TEXT DEFAULT NULL,
    `material_recebido` TEXT DEFAULT NULL,
    `material_consumido` TEXT DEFAULT NULL,
    `visitas_cliente` TINYINT(1) DEFAULT 0,
    `visitas_fiscalizacao` TINYINT(1) DEFAULT 0,
    `preenchido_por` INT(11) DEFAULT NULL,
    `preenchido_em` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `obra_equipe` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `obra_id` INT(11) NOT NULL,
    `tecnico_id` INT(11) NOT NULL,
    `funcao` VARCHAR(50) DEFAULT 'Tecnico',
    `data_entrada` DATE NOT NULL,
    `data_saida` DATE DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `observacoes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- MIGRATION: 20260421000002 - Atividades tables
-- ============================================================
CREATE TABLE IF NOT EXISTS `atividades_tipos` (
    `idTipo` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `categoria` VARCHAR(50) DEFAULT 'geral',
    `icone` VARCHAR(50) DEFAULT NULL,
    `cor` VARCHAR(7) DEFAULT '#2196F3',
    `situacao` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`idTipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_atividades` (
    `idAtividade` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `tipo_atividade_id` INT(11) UNSIGNED DEFAULT NULL,
    `tecnico_id` INT(11) DEFAULT NULL,
    `titulo` VARCHAR(200) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `status` ENUM('pendente','em_andamento','concluida','cancelada') DEFAULT 'pendente',
    `prioridade` ENUM('baixa','media','alta','urgente') DEFAULT 'media',
    `data_inicio` DATETIME DEFAULT NULL,
    `data_fim` DATETIME DEFAULT NULL,
    `tempo_estimado_minutos` INT DEFAULT NULL,
    `checklist_json` JSON DEFAULT NULL,
    `assinatura_tecnico` VARCHAR(255) DEFAULT NULL,
    `assinatura_cliente` VARCHAR(255) DEFAULT NULL,
    `observacao` TEXT DEFAULT NULL,
    `obra_id` INT(11) UNSIGNED DEFAULT NULL,
    `etapa_id` INT(11) DEFAULT NULL,
    `obra_atividade_id` INT(11) DEFAULT NULL,
    `foto_checkin` VARCHAR(255) DEFAULT NULL COMMENT 'caminho da foto de entrada',
    `foto_checkout` VARCHAR(255) DEFAULT NULL COMMENT 'caminho da foto de saida',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`idAtividade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_atividades_materiais` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `atividade_id` INT(11) UNSIGNED NOT NULL,
    `produto_id` INT(11) DEFAULT NULL,
    `nome` VARCHAR(200) DEFAULT NULL,
    `quantidade` DECIMAL(10,2) DEFAULT 1,
    `unidade` VARCHAR(20) DEFAULT 'un',
    `observacao` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_atividades_pausas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `atividade_id` INT(11) UNSIGNED NOT NULL,
    `motivo` VARCHAR(200) DEFAULT NULL,
    `data_inicio` DATETIME NOT NULL,
    `data_fim` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_atividades_checklist` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `atividade_id` INT(11) UNSIGNED NOT NULL,
    `descricao` VARCHAR(255) NOT NULL,
    `concluido` TINYINT(1) DEFAULT 0,
    `ordem` INT DEFAULT 0,
    `obrigatorio` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_atividades_fotos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `atividade_id` INT(11) UNSIGNED NOT NULL,
    `caminho_arquivo` VARCHAR(255) NOT NULL,
    `tipo` ENUM('antes','durante','depois','problema') DEFAULT 'durante',
    `descricao` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- MIGRATION: 20260421000003 - obra_id + obra_etapa_atividades_tipos
-- ============================================================
CREATE TABLE IF NOT EXISTS `obra_etapa_atividades_tipos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `etapa_id` INT(11) NOT NULL,
    `obra_id` INT(11) NOT NULL,
    `tipo_atividade_id` INT(11) UNSIGNED NOT NULL,
    `ordem` INT DEFAULT 0,
    `obrigatorio` TINYINT(1) DEFAULT 0,
    `duracao_estimada` INT DEFAULT NULL COMMENT 'minutos estimados',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_etapa` (`etapa_id`),
    INDEX `idx_obra` (`obra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `obra_atividades_fotos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `atividade_id` INT(11) UNSIGNED NOT NULL,
    `obra_id` INT(11) NOT NULL,
    `etapa_id` INT(11) DEFAULT NULL,
    `caminho_arquivo` VARCHAR(255) NOT NULL,
    `tipo` ENUM('checkin','execucao','checkout','problema') DEFAULT 'execucao',
    `descricao` VARCHAR(255) DEFAULT NULL,
    `latitude` DECIMAL(10,8) DEFAULT NULL,
    `longitude` DECIMAL(11,8) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_atividade_foto` (`atividade_id`),
    INDEX `idx_obra_foto` (`obra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- MIGRATION: 20260422000001 - obra_atividades_vinculo + progresso_real
-- ============================================================
CREATE TABLE IF NOT EXISTS `obra_atividades_vinculo` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `atividade_realizada_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID da os_atividades',
    `obra_atividade_id` INT(11) DEFAULT NULL COMMENT 'ID da obra_atividades (planejada)',
    `etapa_id` INT(11) NOT NULL,
    `obra_id` INT(11) NOT NULL,
    `tecnico_id` INT(11) NOT NULL,
    `data_vinculo` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_atividade_realizada` (`atividade_realizada_id`),
    INDEX `idx_etapa_vinculo` (`etapa_id`),
    INDEX `idx_obra_vinculo` (`obra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add column `progresso_real` to `obra_etapas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'obra_etapas' AND COLUMN_NAME = 'progresso_real');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `obra_etapas` ADD COLUMN `progresso_real` INT(3) DEFAULT 0 COMMENT 'Progresso baseado nas atividades registradas''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260425000002 - obras_config tables
-- ============================================================
CREATE TABLE IF NOT EXISTS `obras_config` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chave` VARCHAR(100) NOT NULL,
    `valor` TEXT DEFAULT NULL,
    `descricao` VARCHAR(255) DEFAULT NULL,
    `grupo` VARCHAR(50) DEFAULT 'geral',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- MIGRATION: 20260426000001 - nfse certificado + simples_nacional
-- ============================================================
-- Add column `ambiente` to `certificado_digital` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'certificado_digital' AND COLUMN_NAME = 'ambiente');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `certificado_digital` ADD COLUMN `ambiente` ENUM('homologacao','producao') DEFAULT 'homologacao''), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260428000001 - remove lucro_presumido (skip - config change)
-- ============================================================
-- (Config change only - no schema changes)

-- ============================================================
-- MIGRATION: 20260428000002 - add inscricoes to clientes
-- ============================================================
-- Add column `inscricao_estadual` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'inscricao_estadual');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `inscricao_estadual` VARCHAR(50) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `inscricao_municipal` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'inscricao_municipal');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `inscricao_municipal` VARCHAR(50) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260428000003 - add inscricao_municipal to emitente
-- ============================================================
-- Already added earlier (emitente.inscricao_municipal)

-- ============================================================
-- MIGRATION: 20260429000001 - nfse campos faltantes
-- ============================================================
-- Already handled in os_nfse_emitida table above

-- ============================================================
-- MIGRATION: 20260430000001 - corrige colunas faltantes v2
-- ============================================================
-- These are all covered by the consolidated schema above

-- ============================================================
-- MIGRATION: 20260501000001 - add n_dps to os_nfse_emitida
-- ============================================================
-- Add column `n_dps` to `os_nfse_emitida` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os_nfse_emitida' AND COLUMN_NAME = 'n_dps');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os_nfse_emitida` ADD COLUMN `n_dps` VARCHAR(50) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260501000003 - agente_ia tables
-- ============================================================
CREATE TABLE IF NOT EXISTS `agente_ia_configuracoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chave` VARCHAR(100) NOT NULL,
    `valor` TEXT DEFAULT NULL,
    `descricao` VARCHAR(255) DEFAULT NULL,
    `grupo` VARCHAR(50) NOT NULL DEFAULT 'geral',
    `sensivel` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=ocultar em logs',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `agente_ia_logs_conversa` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_telefone` VARCHAR(20) NOT NULL,
    `usuarios_id` INT(11) DEFAULT NULL,
    `clientes_id` INT(11) DEFAULT NULL,
    `tipo` ENUM('recebido','enviado','sistema','erro') NOT NULL DEFAULT 'recebido',
    `conteudo` TEXT DEFAULT NULL,
    `intencao_detectada` VARCHAR(50) DEFAULT NULL,
    `acao_executada` VARCHAR(100) DEFAULT NULL,
    `status` VARCHAR(20) DEFAULT 'recebido',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `agente_ia_permissoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_telefone` VARCHAR(20) NOT NULL,
    `acao` VARCHAR(50) NOT NULL COMMENT 'criar_os,ver_os,etc',
    `permitido` TINYINT(1) DEFAULT 1,
    `autorizado_por` VARCHAR(50) DEFAULT NULL COMMENT 'codigo,admin,sistema',
    `data_autorizacao` DATETIME DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_acao` to `agente_ia_permissoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'agente_ia_permissoes' AND INDEX_NAME = 'idx_acao');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `agente_ia_permissoes` ADD INDEX `idx_acao` (`acao`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_ativo` to `agente_ia_permissoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'agente_ia_permissoes' AND INDEX_NAME = 'idx_ativo');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `agente_ia_permissoes` ADD INDEX `idx_ativo` (`ativo`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


CREATE TABLE IF NOT EXISTS `agente_ia_autorizacoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_telefone` VARCHAR(20) NOT NULL,
    `codigo` VARCHAR(6) NOT NULL,
    `acao` VARCHAR(50) NOT NULL,
    `status` ENUM('pendente','autorizado','expirado','cancelado') DEFAULT 'pendente',
    `tentativas` INT DEFAULT 0,
    `resultado_json` TEXT DEFAULT NULL,
    `observacoes` TEXT DEFAULT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_expires_at` to `agente_ia_autorizacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'agente_ia_autorizacoes' AND INDEX_NAME = 'idx_expires_at');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `agente_ia_autorizacoes` ADD INDEX `idx_expires_at` (`expires_at`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_acao_status` to `agente_ia_autorizacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'agente_ia_autorizacoes' AND INDEX_NAME = 'idx_acao_status');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `agente_ia_autorizacoes` ADD INDEX `idx_acao_status` (`acao`, `status`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_usuarios_id` to `agente_ia_autorizacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'agente_ia_autorizacoes' AND INDEX_NAME = 'idx_usuarios_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `agente_ia_autorizacoes` ADD INDEX `idx_usuarios_id` (`numero_telefone`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260504000001 - fix webhooks table
-- ============================================================
-- webhooks table already created above

-- ============================================================
-- MIGRATION: 20260505000001 - agente_ia relatorios templates
-- ============================================================
CREATE TABLE IF NOT EXISTS `agente_ia_relatorios` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo` VARCHAR(50) NOT NULL COMMENT 'diario,semanal,mensal',
    `numero_telefone` VARCHAR(20) NOT NULL,
    `conteudo` LONGTEXT DEFAULT NULL,
    `enviado` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `agente_ia_relatorios_templates` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo` VARCHAR(50) NOT NULL COMMENT 'diario,semanal,mensal',
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `conteudo_template` LONGTEXT DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- MIGRATION: 20260521000001 - whatsapp_log + notificacoes_agendadas
-- ============================================================
CREATE TABLE IF NOT EXISTS `whatsapp_log_interacoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_telefone` VARCHAR(20) NOT NULL,
    `tipo_mensagem` ENUM('texto','audio','documento','imagem') DEFAULT 'texto',
    `direcao` ENUM('entrada','saida') NOT NULL,
    `conteudo` TEXT DEFAULT NULL,
    `intencao_detectada` VARCHAR(50) DEFAULT NULL,
    `status` VARCHAR(20) DEFAULT 'recebido',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_numero` to `whatsapp_log_interacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_log_interacoes' AND INDEX_NAME = 'idx_numero');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `whatsapp_log_interacoes` ADD INDEX `idx_numero` (`numero_telefone`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_intencao` to `whatsapp_log_interacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_log_interacoes' AND INDEX_NAME = 'idx_intencao');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `whatsapp_log_interacoes` ADD INDEX `idx_intencao` (`intencao_detectada`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_created` to `whatsapp_log_interacoes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'whatsapp_log_interacoes' AND INDEX_NAME = 'idx_created');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `whatsapp_log_interacoes` ADD INDEX `idx_created` (`created_at`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


CREATE TABLE IF NOT EXISTS `agente_ia_notificacoes_agendadas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_telefone` VARCHAR(20) NOT NULL,
    `tipo_notificacao` ENUM('relatorio_diario','os_vencendo','os_atrasada','pesquisa_satisfacao') NOT NULL,
    `horario` TIME DEFAULT NULL,
    `dias_semana` VARCHAR(20) DEFAULT '1,2,3,4,5' COMMENT 'Dias da semana (1=Seg, 7=Dom)',
    `situacao` TINYINT(1) DEFAULT 1,
    `ultimo_envio` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_numero` to `agente_ia_notificacoes_agendadas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'agente_ia_notificacoes_agendadas' AND INDEX_NAME = 'idx_numero');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `agente_ia_notificacoes_agendadas` ADD INDEX `idx_numero` (`numero_telefone`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_tipo` to `agente_ia_notificacoes_agendadas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'agente_ia_notificacoes_agendadas' AND INDEX_NAME = 'idx_tipo');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `agente_ia_notificacoes_agendadas` ADD INDEX `idx_tipo` (`tipo_notificacao`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


CREATE TABLE IF NOT EXISTS `whatsapp_integracao` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_telefone` VARCHAR(20) NOT NULL COMMENT 'Numero com DDD e DDI',
    `clientes_id` INT(11) DEFAULT NULL COMMENT 'Vinculo com cliente',
    `usuarios_id` INT(11) DEFAULT NULL COMMENT 'Vinculo com usuario interno',
    `situacao` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
    `ultima_interacao` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_numero_telefone` (`numero_telefone`),
    INDEX `idx_clientes_id` (`clientes_id`),
    INDEX `idx_usuarios_id` (`usuarios_id`),
    INDEX `idx_situacao` (`situacao`),
    INDEX `idx_numero_situacao` (`numero_telefone`, `situacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- MIGRATION: 20260524000002 - soft delete columns
-- ============================================================
-- Add column `deleted_at` to `os` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `os` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `deleted_at` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `deleted_at` to `produtos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'produtos' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `produtos` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `deleted_at` to `servicos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'servicos' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `servicos` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `deleted_at` to `usuarios` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `deleted_at` to `lancamentos` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lancamentos' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `lancamentos` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `deleted_at` to `cobrancas` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260524000003 - performance indexes
-- ============================================================
-- Add index `idx_os_status` to `os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND INDEX_NAME = 'idx_os_status');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os` ADD INDEX `idx_os_status` (`status`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_os_dataInicial` to `os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND INDEX_NAME = 'idx_os_dataInicial');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os` ADD INDEX `idx_os_dataInicial` (`dataInicial`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_os_clientes_id` to `os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND INDEX_NAME = 'idx_os_clientes_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os` ADD INDEX `idx_os_clientes_id` (`clientes_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_os_usuarios_id` to `os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND INDEX_NAME = 'idx_os_usuarios_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os` ADD INDEX `idx_os_usuarios_id` (`usuarios_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_os_status_data` to `os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'os' AND INDEX_NAME = 'idx_os_status_data');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `os` ADD INDEX `idx_os_status_data` (`status`, `dataInicial`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_lanc_baixado` to `lancamentos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lancamentos' AND INDEX_NAME = 'idx_lanc_baixado');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `lancamentos` ADD INDEX `idx_lanc_baixado` (`baixado`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_lanc_data_vencimento` to `lancamentos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lancamentos' AND INDEX_NAME = 'idx_lanc_data_vencimento');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `lancamentos` ADD INDEX `idx_lanc_data_vencimento` (`data_vencimento`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_lanc_tipo` to `lancamentos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lancamentos' AND INDEX_NAME = 'idx_lanc_tipo');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `lancamentos` ADD INDEX `idx_lanc_tipo` (`tipo`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_lanc_tipo_baixado` to `lancamentos` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lancamentos' AND INDEX_NAME = 'idx_lanc_tipo_baixado');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `lancamentos` ADD INDEX `idx_lanc_tipo_baixado` (`tipo`, `baixado`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_produtos_os_os_id` to `produtos_os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'produtos_os' AND INDEX_NAME = 'idx_produtos_os_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `produtos_os` ADD INDEX `idx_produtos_os_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_servicos_os_os_id` to `servicos_os` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'servicos_os' AND INDEX_NAME = 'idx_servicos_os_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `servicos_os` ADD INDEX `idx_servicos_os_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_clientes_nomeCliente` to `clientes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND INDEX_NAME = 'idx_clientes_nomeCliente');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `clientes` ADD INDEX `idx_clientes_nomeCliente` (`nomeCliente`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_clientes_documento` to `clientes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND INDEX_NAME = 'idx_clientes_documento');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `clientes` ADD INDEX `idx_clientes_documento` (`documento`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_clientes_email` to `clientes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND INDEX_NAME = 'idx_clientes_email');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `clientes` ADD INDEX `idx_clientes_email` (`email`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_usuarios_email` to `usuarios` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND INDEX_NAME = 'idx_usuarios_email');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD INDEX `idx_usuarios_email` (`email`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_usuarios_situacao` to `usuarios` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND INDEX_NAME = 'idx_usuarios_situacao');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `usuarios` ADD INDEX `idx_usuarios_situacao` (`situacao`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_cobrancas_status` to `cobrancas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND INDEX_NAME = 'idx_cobrancas_status');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD INDEX `idx_cobrancas_status` (`status`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_cobrancas_os_id` to `cobrancas` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cobrancas' AND INDEX_NAME = 'idx_cobrancas_os_id');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `cobrancas` ADD INDEX `idx_cobrancas_os_id` (`os_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260525000001 - LGPD + token_acesso
-- ============================================================
-- Add column `consentimento_lgpd` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'consentimento_lgpd');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `consentimento_lgpd` TINYINT(1) DEFAULT 0'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `data_consentimento` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'data_consentimento');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `data_consentimento` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `origem_dados` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'origem_dados');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `origem_dados` VARCHAR(50) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add column `token_acesso` to `clientes` if not exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND COLUMN_NAME = 'token_acesso');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `clientes` ADD COLUMN `token_acesso` VARCHAR(64) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_clientes_token_acesso` to `clientes` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clientes' AND INDEX_NAME = 'idx_clientes_token_acesso');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `clientes` ADD INDEX `idx_clientes_token_acesso` (`token_acesso`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260525000002 - audit_log
-- ============================================================
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED DEFAULT NULL,
    `username` VARCHAR(150) DEFAULT NULL,
    `action` VARCHAR(30) NOT NULL,
    `table_name` VARCHAR(100) NOT NULL,
    `record_id` VARCHAR(50) DEFAULT NULL,
    `old_data` JSON DEFAULT NULL,
    `new_data` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Add index `idx_audit_table_record` to `audit_log` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_log' AND INDEX_NAME = 'idx_audit_table_record');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `audit_log` ADD INDEX `idx_audit_table_record` (`table_name`, `record_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_audit_user` to `audit_log` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_log' AND INDEX_NAME = 'idx_audit_user');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `audit_log` ADD INDEX `idx_audit_user` (`user_id`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_audit_action` to `audit_log` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_log' AND INDEX_NAME = 'idx_audit_action');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `audit_log` ADD INDEX `idx_audit_action` (`action`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index `idx_audit_created` to `audit_log` if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_log' AND INDEX_NAME = 'idx_audit_created');
SET @sql = IF(@idx_exists = 0, CONCAT('ALTER TABLE `audit_log` ADD INDEX `idx_audit_created` (`created_at`)'), 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- MIGRATION: 20260525000005 - data_breach_notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS `data_breach_notifications` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(255) NOT NULL,
    `descricao` TEXT NOT NULL,
    `tipo_dado_afetado` VARCHAR(255) NOT NULL COMMENT 'Ex: dados pessoais, financeiros, credenciais',
    `medidas_adotadas` TEXT DEFAULT NULL,
    `data_ocorrencia` DATETIME NOT NULL,
    `data_descoberta` DATETIME NOT NULL,
    `notificado_anpd` TINYINT(1) DEFAULT 0,
    `data_notificacao_anpd` DATETIME DEFAULT NULL,
    `titulares_notificados` TINYINT(1) DEFAULT 0,
    `data_notificacao_titulares` DATETIME DEFAULT NULL,
    `num_titulares_afetados` INT(11) DEFAULT 0,
    `status` VARCHAR(30) DEFAULT 'investigando' COMMENT 'investigando, notificado, resolvido',
    `registrado_por` INT(11) UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `status` (`status`),
    INDEX `data_ocorrencia` (`data_ocorrencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- CONFIGURACOES PADRAO
-- ============================================================
INSERT IGNORE INTO `configuracoes` (`idConfig`, `config`, `valor`) VALUES
(7, 'notifica_whats', 'Prezado(a), {CLIENTE_NOME} a OS de nº {NUMERO_OS} teve o status alterado para: {STATUS_OS}'),
(8, 'control_baixa', '0'),
(9, 'control_editos', '1'),
(10, 'control_datatable', '1'),
(11, 'pix_key', ''),
(12, 'os_status_list', '["Aberto","Faturado","Negociacao","Em Andamento","Orcamento","Finalizado","Cancelado","Aguardando Pecas","Aprovado"]'),
(13, 'control_edit_vendas', '1'),
(14, 'email_automatico', '1'),
(15, 'control_2vias', '0');

-- ============================================================
-- DADOS DRE PADRAO
-- ============================================================
INSERT IGNORE INTO `dre_contas` (`codigo`, `nome`, `tipo`, `grupo`, `sinal`, `nivel`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
('1', 'RECEITA BRUTA', 'receita', 'RECEITA_BRUTA', 'POSITIVO', 1, 1, 1, NOW(), NOW()),
('1.1', 'Receita de Servicos', 'receita', 'RECEITA_BRUTA', 'POSITIVO', 2, 2, 1, NOW(), NOW()),
('1.2', 'Receita de Produtos', 'receita', 'RECEITA_BRUTA', 'POSITIVO', 2, 3, 1, NOW(), NOW()),
('1.3', 'Outras Receitas', 'receita', 'OUTRAS_RECEITAS', 'POSITIVO', 2, 4, 1, NOW(), NOW()),
('2', 'DEDUCOES DA RECEITA', 'despesa', 'DEDUCOES', 'NEGATIVO', 1, 5, 1, NOW(), NOW()),
('2.1', 'ISS', 'despesa', 'DEDUCOES', 'NEGATIVO', 2, 6, 1, NOW(), NOW()),
('2.2', 'ISS Retido na Fonte', 'despesa', 'DEDUCOES', 'NEGATIVO', 2, 7, 1, NOW(), NOW()),
('3', 'CUSTO DOS SERVICOS', 'custo', 'CUSTO', 'NEGATIVO', 1, 10, 1, NOW(), NOW()),
('4', 'DESPESAS OPERACIONAIS', 'despesa', 'DESPESA_OPERACIONAL', 'NEGATIVO', 1, 20, 1, NOW(), NOW()),
('6', 'IMPOSTO DE RENDA E CONTRIBUICOES', 'despesa', 'IMPOSTO_RENDA', 'NEGATIVO', 1, 30, 1, NOW(), NOW()),
('6.1', 'DAS - Simples Nacional', 'despesa', 'IMPOSTO_RENDA', 'NEGATIVO', 2, 31, 1, NOW(), NOW()),
('6.2', 'IRRF Retido na Fonte', 'despesa', 'IMPOSTO_RENDA', 'NEGATIVO', 2, 32, 1, NOW(), NOW()),
('6.3', 'PIS/COFINS Retidos', 'despesa', 'IMPOSTO_RENDA', 'NEGATIVO', 2, 33, 1, NOW(), NOW()),
('6.4', 'CSLL Retido', 'despesa', 'IMPOSTO_RENDA', 'NEGATIVO', 2, 34, 1, NOW(), NOW()),
('7', 'OUTRAS DESPESAS', 'despesa', 'OUTRAS_DESPESAS', 'NEGATIVO', 1, 35, 1, NOW(), NOW());

-- ============================================================
-- CONFIG IMPOSTOS PADRAO
-- ============================================================
INSERT IGNORE INTO `impostos_config` (`tipo_regime`, `anexo_simples`, `aliquota_iss`, `retem_iss`, `aliquota_simples`, `created_at`, `updated_at`) VALUES
('simples_nacional', 'III', 2.00, 0, 6.00, NOW(), NOW());

-- ============================================================
-- PERMISSOES ADMIN - garantir todas as permissoes
-- ============================================================
UPDATE `permissoes` SET `permissoes` = JSON_MERGE_PRESERVE(
    COALESCE(JSON_VALID(`permissoes`), '{}'),
    '{
        "aCliente":"1","vCliente":"1","eCliente":"1","dCliente":"1","cCliente":"1",
        "aOs":"1","vOs":"1","eOs":"1","dOs":"1",
        "aProduto":"1","vProduto":"1","eProduto":"1","dProduto":"1",
        "aServico":"1","vServico":"1","eServico":"1","dServico":"1",
        "aVenda":"1","vVenda":"1","eVenda":"1","dVenda":"1",
        "aArquivo":"1","vArquivo":"1","eArquivo":"1","dArquivo":"1",
        "aCobranca":"1","vCobranca":"1","eCobranca":"1","dCobranca":"1",
        "aLancamento":"1","vLancamento":"1","eLancamento":"1","dLancamento":"1",
        "aUsuario":"1","vUsuario":"1","eUsuario":"1","dUsuario":"1",
        "aPermissao":"1","vPermissao":"1","ePermissao":"1","dPermissao":"1",
        "aConfiguracao":"1","eConfiguracao":"1",
        "aRelatorio":"1","vRelatorio":"1",
        "vTecnicoDashboard":"1","eTecnicoOs":"1",
        "aGarantia":"1","vGarantia":"1","eGarantia":"1",
        "vMapa":"1","aImportar":"1","vPainel":"1","eNfse":"1",
        "aObra":"1","vObra":"1","eObra":"1","dObra":"1",
        "cAgenteIA":"1","vAgenteIA":"1","eAgenteIA":"1",
        "cAuditoria":"1",
        "lgpd_exportar":"1","lgpd_anonimizar":"1","lgpd_consentimento":"1",
        "vTecnicoFotos":"1","vTecnicoAssinaturas":"1",
        "vDashboard":"1","vRelatorioCompleto":"1","vExportarDados":"1",
        "vDRE":"1","vDREDemonstracao":"1","vDREContas":"1","vDRELancamentos":"1","cDRE":"1","eDRE":"1","dDRE":"1",
        "vRelatorioTecnicos":"1","vRelatorioAtendimentos":"1","vWebhooks":"1",
        "vBtnAtendimento":"1","cDocOs":"1",
        "vCertificado":"1","cCertificado":"1","eCertificado":"1","dCertificado":"1",
        "vImpostos":"1","cImpostos":"1","eImpostos":"1","dImpostos":"1","cImpostosConfig":"1","vImpostosRelatorio":"1","vImpostosExportar":"1",
        "vUsuariosCliente":"1","cUsuariosCliente":"1","eUsuariosCliente":"1","dUsuariosCliente":"1","cPermUsuariosCliente":"1",
        "vNFSe":"1","cNFSe":"1","eNFSe":"1","vBoletoOS":"1","cBoletoOS":"1","eBoletoOS":"1","rNFSe":"1"
    }'
) WHERE `idPermissao` = 1;

-- ============================================================
-- GRUPO TECNICO (criar se nao existir)
-- ============================================================
INSERT IGNORE INTO `permissoes` (`nome`, `permissoes`, `situacao`, `data`) VALUES
('Técnico', '{"vCliente":"1","vProduto":"1","vServico":"1","vOs":"1","vBtnAtendimento":"1","vTecnicoOS":"1","eTecnicoCheckin":"1","eTecnicoCheckout":"1","eTecnicoFotos":"1","vTecnicoDashboard":"1","vTecnicoFotos":"1","vTecnicoAssinaturas":"1","vRelatorioTecnicos":"1","vRelatorioAtendimentos":"1","vDashboard":"1"}', 1, CURDATE());

-- ============================================================
-- ATUALIZAR VERSAO DA MIGRATION
-- ============================================================
DELETE FROM `migrations`;
INSERT INTO `migrations` (`version`) VALUES ('20260525000005');

-- ============================================================
-- CORRECAO: email_queue - colunas faltantes no servidor
-- A tabela existente no servidor tem: id, to, cc, bcc, message, status, scheduled_at, date, headers
-- As colunas abaixo sao necessarias pelo codigo novo
-- ============================================================

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'to_email');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `to_email` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'to_name');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `to_name` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'subject');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `subject` VARCHAR(500) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'body_html');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `body_html` LONGTEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'body_text');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `body_text` LONGTEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'template');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `template` VARCHAR(100) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'template_data');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `template_data` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'attachments');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `attachments` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'priority');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `priority` TINYINT(1) DEFAULT 3'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'attempts');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `attempts` TINYINT(1) DEFAULT 0'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'max_retries');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `max_retries` TINYINT(1) DEFAULT 3'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'tracking_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `tracking_id` VARCHAR(32) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'message_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `message_id` VARCHAR(255) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'sent_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `sent_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'opened_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `opened_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'clicked_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `clicked_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'created_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `created_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'updated_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `updated_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'last_attempt');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `last_attempt` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'failed_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `failed_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'error_message');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `error_message` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'ip_address');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `ip_address` VARCHAR(45) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'user_agent');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `email_queue` ADD COLUMN `user_agent` TEXT DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Migrar dados das colunas antigas para as novas (se existirem dados)
UPDATE `email_queue` SET `to_email` = `to` WHERE `to_email` IS NULL AND `to` IS NOT NULL AND `to_email` IS NULL;
UPDATE `email_queue` SET `body_html` = `message` WHERE `body_html` IS NULL AND `message` IS NOT NULL AND `body_html` IS NULL;
UPDATE `email_queue` SET `created_at` = `date` WHERE `created_at` IS NULL AND `date` IS NOT NULL AND `created_at` IS NULL;

-- Alterar o status ENUM para incluir novos valores
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_queue' AND COLUMN_NAME = 'status' AND COLUMN_TYPE LIKE '%processing%');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `email_queue` MODIFY COLUMN `status` ENUM(''pending'',''processing'',''sent'',''failed'',''cancelled'',''scheduled'') DEFAULT ''pending''', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================
-- CORRECAO: dre_lancamentos - colunas faltantes no servidor
-- A tabela existente tem: id_os, id_venda, id_lancamento
-- O codigo usa: os_id, venda_id, lancamento_id, tipo_movimento, documento, usuarios_id, updated_at
-- ============================================================

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'os_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `os_id` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'venda_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `venda_id` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'lancamento_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `lancamento_id` INT(11) UNSIGNED DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'tipo_movimento');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `tipo_movimento` VARCHAR(50) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'documento');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `documento` VARCHAR(100) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'usuarios_id');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `usuarios_id` INT(11) DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dre_lancamentos' AND COLUMN_NAME = 'updated_at');
SET @sql = IF(@col_exists = 0, CONCAT('ALTER TABLE `dre_lancamentos` ADD COLUMN `updated_at` DATETIME DEFAULT NULL'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Migrar dados das colunas antigas para as novas
UPDATE `dre_lancamentos` SET `os_id` = `id_os` WHERE `os_id` IS NULL AND `id_os` IS NOT NULL;
UPDATE `dre_lancamentos` SET `venda_id` = `id_venda` WHERE `venda_id` IS NULL AND `id_venda` IS NOT NULL;
UPDATE `dre_lancamentos` SET `lancamento_id` = `id_lancamento` WHERE `lancamento_id` IS NULL AND `id_lancamento` IS NOT NULL;

-- ============================================================
-- TABELAS DE NOTIFICACOES WHATSAPP
-- ============================================================

CREATE TABLE IF NOT EXISTS `notificacoes_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `whatsapp_provedor` VARCHAR(50) DEFAULT 'desativado',
  `whatsapp_ativo` TINYINT(1) DEFAULT 0,
  `evolution_url` VARCHAR(255) DEFAULT NULL,
  `evolution_apikey` VARCHAR(255) DEFAULT NULL,
  `evolution_instance` VARCHAR(100) DEFAULT 'mapos',
  `meta_phone_number_id` VARCHAR(255) DEFAULT NULL,
  `meta_access_token` VARCHAR(255) DEFAULT NULL,
  `z_api_url` VARCHAR(255) DEFAULT NULL,
  `z_api_token` VARCHAR(255) DEFAULT NULL,
  `notificacao_os_criada` TINYINT(1) DEFAULT 1,
  `notificacao_os_atualizada` TINYINT(1) DEFAULT 1,
  `notificacao_os_pronta` TINYINT(1) DEFAULT 1,
  `notificacao_os_orcamento` TINYINT(1) DEFAULT 1,
  `notificacao_venda_realizada` TINYINT(1) DEFAULT 0,
  `notificacao_cobranca_gerada` TINYINT(1) DEFAULT 0,
  `notificacao_cobranca_vencimento` TINYINT(1) DEFAULT 0,
  `notificacao_lembrete_aniversario` TINYINT(1) DEFAULT 0,
  `horario_envio_inicio` TIME DEFAULT '08:00:00',
  `horario_envio_fim` TIME DEFAULT '18:00:00',
  `enviar_fim_semana` TINYINT(1) DEFAULT 0,
  `respeitar_horario` TINYINT(1) DEFAULT 1,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir config padrao se nao existir
SET @cfg_exists = (SELECT COUNT(*) FROM `notificacoes_config` WHERE `id` = 1);
SET @sql = IF(@cfg_exists = 0, 'INSERT INTO `notificacoes_config` (`id`, `whatsapp_provedor`, `whatsapp_ativo`) VALUES (1, ''desativado'', 0)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `notificacoes_templates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `chave` VARCHAR(100) NOT NULL,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT DEFAULT NULL,
  `categoria` VARCHAR(50) DEFAULT 'sistema',
  `canal` VARCHAR(20) DEFAULT 'whatsapp',
  `assunto` VARCHAR(255) DEFAULT NULL,
  `mensagem` TEXT NOT NULL,
  `variaveis` JSON DEFAULT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `e_marketing` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Templates padrao
SET @tpl_count = (SELECT COUNT(*) FROM `notificacoes_templates`);
SET @sql = IF(@tpl_count = 0, CONCAT('INSERT INTO `notificacoes_templates` (`chave`, `nome`, `categoria`, `canal`, `mensagem`, `ativo`) VALUES
  (''os_criada'', ''OS Criada'', ''os'', ''whatsapp'', ''Ola {cliente_nome}! Sua OS #{os_id} foi criada com status: {os_status}. Acompanhe pelo link: {link_sistema}'', 1),
  (''os_atualizada'', ''OS Atualizada'', ''os'', ''whatsapp'', ''Ola {cliente_nome}! Sua OS #{os_id} teve o status atualizado para: {os_status}'', 1),
  (''os_pronta'', ''OS Pronta'', ''os'', ''whatsapp'', ''Ola {cliente_nome}! Sua OS #{os_id} foi finalizada e esta pronta para retirada!'', 1),
  (''os_orcamento'', ''Orcamento Disponivel'', ''os'', ''whatsapp'', ''Ola {cliente_nome}! O orcamento da OS #{os_id} esta disponivel. Valor: R$ {os_valor}'', 1),
  (''venda_realizada'', ''Venda Realizada'', ''venda'', ''whatsapp'', ''Ola {cliente_nome}! Sua venda #{venda_id} foi realizada com sucesso!'', 1),
  (''cobranca_gerada'', ''Cobranca Gerada'', ''cobranca'', ''whatsapp'', ''Ola {cliente_nome}! Sua cobranca #{cobranca_id} foi gerada. Vencimento: {cobranca_vencimento}'', 1),
  (''cobranca_vencimento'', ''Cobranca Vencendo'', ''cobranca'', ''whatsapp'', ''Ola {cliente_nome}! Sua cobranca #{cobranca_id} vence em breve. Valor: R$ {cobranca_valor}'', 1),
  (''aniversario'', ''Aniversario'', ''marketing'', ''whatsapp'', ''Feliz aniversario {cliente_nome}! Desejamos um otimo dia! Aproveite nossa promocao especial!'', 1)
'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `notificacoes_log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `template_chave` VARCHAR(100) DEFAULT NULL,
  `cliente_id` INT DEFAULT NULL,
  `telefone` VARCHAR(20) DEFAULT NULL,
  `mensagem` TEXT DEFAULT NULL,
  `canal` VARCHAR(20) DEFAULT 'whatsapp',
  `status` VARCHAR(20) DEFAULT 'pendente',
  `tentativas` INT DEFAULT 0,
  `erro` TEXT DEFAULT NULL,
  `os_id` INT DEFAULT NULL,
  `sent_at` DATETIME DEFAULT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  `read_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `idx_template` (`template_chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissao cConfiguracao: garantir que existe
SET @perm_exists = (SELECT COUNT(*) FROM `permissoes` WHERE `idPermissao` = 1);
SET @perm_has_config = (SELECT COUNT(*) FROM `permissoes` WHERE `idPermissao` = 1 AND `permissoes` LIKE '%cConfiguracao%');
SET @sql = IF(@perm_exists > 0 AND @perm_has_config = 0, CONCAT('UPDATE `permissoes` SET `permissoes` = CONCAT(`permissoes`, '',cConfiguracao,'') WHERE `idPermissao` = 1'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- FIM DO SCRIPT
-- ============================================================