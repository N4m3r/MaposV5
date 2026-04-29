-- ============================================================
-- Script de criacao/atualizacao da tabela notificacoes_config
-- Executar no PHPMyAdmin ou mysql CLI
-- Caso a tabela ja exista, apenas adiciona colunas que faltam
-- ============================================================

CREATE TABLE IF NOT EXISTS `notificacoes_config` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `whatsapp_provedor` ENUM('evolution','meta_api','z_api','desativado') DEFAULT 'desativado',
    `whatsapp_ativo` TINYINT(1) DEFAULT 0,
    `evolution_url` VARCHAR(255) DEFAULT NULL,
    `evolution_apikey` VARCHAR(255) DEFAULT NULL,
    `evolution_instance` VARCHAR(100) DEFAULT 'Mapos',
    `evolution_instance_token` VARCHAR(255) DEFAULT NULL,
    `evolution_version` ENUM('v1','v2','go') DEFAULT 'v2',
    `evolution_estado` VARCHAR(50) DEFAULT 'desconectado',
    `meta_phone_number_id` VARCHAR(50) DEFAULT NULL,
    `meta_access_token` TEXT DEFAULT NULL,
    `meta_webhook_verify_token` VARCHAR(100) DEFAULT NULL,
    `z_api_url` VARCHAR(255) DEFAULT NULL,
    `z_api_token` VARCHAR(255) DEFAULT NULL,
    `notificacao_os_criada` TINYINT(1) DEFAULT 1,
    `notificacao_os_atualizada` TINYINT(1) DEFAULT 1,
    `notificacao_os_pronta` TINYINT(1) DEFAULT 1,
    `notificacao_os_orcamento` TINYINT(1) DEFAULT 1,
    `notificacao_venda_realizada` TINYINT(1) DEFAULT 1,
    `notificacao_cobranca_gerada` TINYINT(1) DEFAULT 1,
    `notificacao_cobranca_vencimento` TINYINT(1) DEFAULT 1,
    `notificacao_lembrete_aniversario` TINYINT(1) DEFAULT 0,
    `horario_envio_inicio` TIME DEFAULT '08:00:00',
    `horario_envio_fim` TIME DEFAULT '18:00:00',
    `enviar_fim_semana` TINYINT(1) DEFAULT 0,
    `respeitar_horario` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insere registro padrao (id=1) se nao existir
INSERT INTO `notificacoes_config` (`id`, `whatsapp_provedor`, `whatsapp_ativo`, `evolution_url`, `evolution_apikey`, `evolution_instance`, `evolution_instance_token`, `evolution_version`)
VALUES (1, 'evolution', 1, 'https://evo.jj-ferreiras.com.br', '7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2', 'Mapos', '9e907a2a-1b06-4812-badb-02e5205df9f7', 'v2')
ON DUPLICATE KEY UPDATE id=id;

-- ============================================================
-- Adiciona colunas que possam estar faltando (ALTER TABLE)
-- ============================================================

SET @dbname = DATABASE();
SET @tablename = 'notificacoes_config';

-- evolution_instance_token
SET @col = 'evolution_instance_token';
SET @sql = CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @col, '` VARCHAR(255) DEFAULT NULL AFTER `evolution_instance`');
SET @exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col);
SET @sql = IF(@exists = 0, @sql, 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- evolution_version
SET @col = 'evolution_version';
SET @sql = CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @col, '` ENUM(\'v1\',\'v2\',\'go\') DEFAULT \'v2\' AFTER `evolution_instance_token`');
SET @exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col);
SET @sql = IF(@exists = 0, @sql, 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- evolution_estado
SET @col = 'evolution_estado';
SET @sql = CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @col, '` VARCHAR(50) DEFAULT \'desconectado\' AFTER `evolution_version`');
SET @exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col);
SET @sql = IF(@exists = 0, @sql, 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- meta_webhook_verify_token
SET @col = 'meta_webhook_verify_token';
SET @sql = CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @col, '` VARCHAR(100) DEFAULT NULL AFTER `meta_access_token`');
SET @exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col);
SET @sql = IF(@exists = 0, @sql, 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- notificacao_os_orcamento
SET @col = 'notificacao_os_orcamento';
SET @sql = CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @col, '` TINYINT(1) DEFAULT 1 AFTER `notificacao_os_pronta`');
SET @exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col);
SET @sql = IF(@exists = 0, @sql, 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- notificacao_cobranca_vencimento
SET @col = 'notificacao_cobranca_vencimento';
SET @sql = CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @col, '` TINYINT(1) DEFAULT 1 AFTER `notificacao_cobranca_gerada`');
SET @exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col);
SET @sql = IF(@exists = 0, @sql, 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Atualiza valores padrao no registro 1 (caso exista e esteja vazio)
UPDATE `notificacoes_config` SET
    `whatsapp_provedor` = COALESCE(NULLIF(`whatsapp_provedor`, ''), 'evolution'),
    `whatsapp_ativo` = COALESCE(`whatsapp_ativo`, 1),
    `evolution_url` = COALESCE(NULLIF(`evolution_url`, ''), 'https://evo.jj-ferreiras.com.br'),
    `evolution_apikey` = COALESCE(NULLIF(`evolution_apikey`, ''), '7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2'),
    `evolution_instance` = COALESCE(NULLIF(`evolution_instance`, ''), 'Mapos'),
    `evolution_instance_token` = COALESCE(NULLIF(`evolution_instance_token`, ''), '9e907a2a-1b06-4812-badb-02e5205df9f7'),
    `evolution_version` = COALESCE(NULLIF(`evolution_version`, ''), 'v2'),
    `notificacao_os_criada` = COALESCE(`notificacao_os_criada`, 1),
    `notificacao_os_atualizada` = COALESCE(`notificacao_os_atualizada`, 1),
    `notificacao_os_pronta` = COALESCE(`notificacao_os_pronta`, 1),
    `notificacao_os_orcamento` = COALESCE(`notificacao_os_orcamento`, 1),
    `notificacao_venda_realizada` = COALESCE(`notificacao_venda_realizada`, 1),
    `notificacao_cobranca_gerada` = COALESCE(`notificacao_cobranca_gerada`, 1),
    `notificacao_cobranca_vencimento` = COALESCE(`notificacao_cobranca_vencimento`, 1),
    `horario_envio_inicio` = COALESCE(NULLIF(`horario_envio_inicio`, ''), '08:00:00'),
    `horario_envio_fim` = COALESCE(NULLIF(`horario_envio_fim`, ''), '18:00:00'),
    `respeitar_horario` = COALESCE(`respeitar_horario`, 1)
WHERE `id` = 1;
