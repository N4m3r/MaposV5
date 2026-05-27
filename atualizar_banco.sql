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
-- DELIMITER para procedimentos armazenados
-- ============================================================
DELIMITER $$

-- ============================================================
-- PROCEDIMENTO: Adicionar coluna se nao existir
-- ============================================================
DROP PROCEDURE IF EXISTS add_column_if_not_exists$$
CREATE PROCEDURE add_column_if_not_exists(
    IN p_table VARCHAR(100),
    IN p_column VARCHAR(100),
    IN p_definition TEXT
)
BEGIN
    DECLARE col_count INT;
    SELECT COUNT(*) INTO col_count
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND COLUMN_NAME = p_column;
    IF col_count = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

-- ============================================================
-- PROCEDIMENTO: Criar indice se nao existir
-- ============================================================
DROP PROCEDURE IF EXISTS create_index_if_not_exists$$
CREATE PROCEDURE create_index_if_not_exists(
    IN p_table VARCHAR(100),
    IN p_index_name VARCHAR(100),
    IN p_columns VARCHAR(500)
)
BEGIN
    DECLARE idx_count INT;
    SELECT COUNT(*) INTO idx_count
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND INDEX_NAME = p_index_name;
    IF idx_count = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD INDEX `', p_index_name, '` (', p_columns, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

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

CALL add_column_if_not_exists('cobrancas', 'linha_digitavel', "VARCHAR(255) DEFAULT NULL AFTER `barcode`");
CALL add_column_if_not_exists('cobrancas', 'pix_code', "TEXT DEFAULT NULL AFTER `link`");
CALL add_column_if_not_exists('cobrancas', 'paid_at', "DATETIME DEFAULT NULL AFTER `expire_at`");
CALL add_column_if_not_exists('cobrancas', 'updated_at', "DATETIME DEFAULT NULL AFTER `created_at`");
CALL create_index_if_not_exists('cobrancas', 'idx_cobrancas_charge_id', '`charge_id`');
CALL create_index_if_not_exists('cobrancas', 'idx_cobrancas_status_gateway', '`status`, `payment_gateway`');

-- --- os ---
CALL add_column_if_not_exists('os', 'tecnico_responsavel', "INT(11) DEFAULT NULL COMMENT 'ID do usuario tecnico responsavel pela OS'");
CALL add_column_if_not_exists('os', 'nfse_status', "ENUM('Pendente','Emitida','Cancelada') DEFAULT 'Pendente' COMMENT 'Status da NFS-e vinculada'");
CALL add_column_if_not_exists('os', 'boleto_status', "ENUM('Pendente','Emitido','Pago','Vencido','Cancelado') DEFAULT 'Pendente' COMMENT 'Status do boleto vinculado'");
CALL add_column_if_not_exists('os', 'data_vencimento_boleto', "DATE DEFAULT NULL COMMENT 'Data de vencimento do boleto'");
CALL add_column_if_not_exists('os', 'valor_com_impostos', "DECIMAL(15,2) DEFAULT NULL COMMENT 'Valor liquido apos deducao de impostos'");
CALL add_column_if_not_exists('os', 'certificado_vinculado', "INT(11) UNSIGNED DEFAULT NULL AFTER `garantia`");
CALL add_column_if_not_exists('os', 'retencao_impostos', "TINYINT(1) DEFAULT 0");
CALL add_column_if_not_exists('os', 'calculo_impostos', "TEXT DEFAULT NULL COMMENT 'JSON com detalhes dos impostos calculados'");
CALL add_column_if_not_exists('os', 'obra_id', "INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID da obra vinculada'");
CALL create_index_if_not_exists('os', 'idx_tecnico_responsavel', '`tecnico_responsavel`');
CALL create_index_if_not_exists('os', 'idx_obra_id', '`obra_id`');
CALL add_column_if_not_exists('os', 'status', "VARCHAR(45) DEFAULT NULL");

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
CALL add_column_if_not_exists('lancamentos', 'observacoes', "TEXT DEFAULT NULL");
CALL add_column_if_not_exists('lancamentos', 'webhook_notificado', "TINYINT(1) DEFAULT 0");

-- --- usuarios ---
CALL add_column_if_not_exists('usuarios', 'is_tecnico', "TINYINT(1) DEFAULT 0 COMMENT 'Indica se e tecnico de campo'");
CALL add_column_if_not_exists('usuarios', 'nivel_tecnico', "ENUM('I','II','III','IV') DEFAULT 'II' COMMENT 'Nivel do tecnico'");
CALL add_column_if_not_exists('usuarios', 'especialidades', "VARCHAR(255) DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'veiculo_placa', "VARCHAR(10) DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'veiculo_tipo', "ENUM('Moto','Carro','Nenhum') DEFAULT 'Nenhum'");
CALL add_column_if_not_exists('usuarios', 'coordenadas_base_lat', "DECIMAL(10,8) DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'coordenadas_base_lng', "DECIMAL(11,8) DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'raio_atuacao_km', "INT DEFAULT 50");
CALL add_column_if_not_exists('usuarios', 'plantao_24h', "TINYINT(1) DEFAULT 0");
CALL add_column_if_not_exists('usuarios', 'app_tecnico_instalado', "TINYINT(1) DEFAULT 0");
CALL add_column_if_not_exists('usuarios', 'token_app', "VARCHAR(255) DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'token_expira', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'ultimo_acesso_app', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'foto_tecnico', "VARCHAR(255) DEFAULT NULL");

-- --- clientes ---
CALL add_column_if_not_exists('clientes', 'contato', "VARCHAR(45) DEFAULT NULL");
CALL add_column_if_not_exists('clientes', 'complemento', "VARCHAR(45) DEFAULT NULL");
CALL add_column_if_not_exists('clientes', 'fornecedor', "BOOLEAN DEFAULT 0");
CALL add_column_if_not_exists('clientes', 'senha', "VARCHAR(200) DEFAULT NULL");
CALL add_column_if_not_exists('clientes', 'asaas_id', "VARCHAR(255) DEFAULT NULL");

-- --- vendas ---
CALL add_column_if_not_exists('vendas', 'observacoes', "TEXT DEFAULT NULL");
CALL add_column_if_not_exists('vendas', 'observacoes_cliente', "TEXT DEFAULT NULL");
CALL add_column_if_not_exists('vendas', 'garantia', "VARCHAR(45) DEFAULT NULL");
CALL add_column_if_not_exists('vendas', 'status', "VARCHAR(45) DEFAULT NULL");

-- --- emitente ---
CALL add_column_if_not_exists('emitente', 'cep', "VARCHAR(20) DEFAULT NULL");
CALL add_column_if_not_exists('emitente', 'inscricao_municipal', "VARCHAR(50) DEFAULT NULL");

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
CALL create_index_if_not_exists('notificacoes', 'idx_usuario_lida', '`usuario_id`, `lida`');
CALL create_index_if_not_exists('notificacoes', 'idx_data', '`data_notificacao`');

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
CALL create_index_if_not_exists('checkin', 'idx_os_id', '`os_id`');

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
CALL create_index_if_not_exists('os_checkin', 'idx_os_id', '`os_id`');
CALL create_index_if_not_exists('os_checkin', 'idx_status', '`status`');

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
CALL create_index_if_not_exists('os_assinaturas', 'idx_os_id', '`os_id`');
CALL create_index_if_not_exists('os_assinaturas', 'idx_tipo', '`tipo`');

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
CALL create_index_if_not_exists('os_fotos_atendimento', 'idx_os_id', '`os_id`');
CALL create_index_if_not_exists('os_fotos_atendimento', 'idx_etapa', '`etapa`');

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
CALL create_index_if_not_exists('usuarios_cliente', 'idx_cliente_id', '`cliente_id`');

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
CALL create_index_if_not_exists('usuarios_cliente_cnpjs', 'idx_usuario_cnpj', '`usuario_cliente_id`, `cnpj`');

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
CALL create_index_if_not_exists('usuarios_cliente_permissoes', 'idx_usuario_chave', '`usuario_cliente_id`, `chave`');

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
CALL create_index_if_not_exists('dre_contas', 'idx_tipo', '`tipo`');
CALL create_index_if_not_exists('dre_contas', 'idx_ativo', '`ativo`');

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
CALL add_column_if_not_exists('dre_lancamentos', 'os_id', "INT(11) UNSIGNED DEFAULT NULL AFTER `documento`");
CALL add_column_if_not_exists('dre_lancamentos', 'venda_id', "INT(11) UNSIGNED DEFAULT NULL AFTER `os_id`");
CALL add_column_if_not_exists('dre_lancamentos', 'lancamento_id', "INT(11) UNSIGNED DEFAULT NULL AFTER `venda_id`");
CALL add_column_if_not_exists('dre_lancamentos', 'usuarios_id', "INT(11) UNSIGNED DEFAULT NULL AFTER `lancamento_id`");
CALL add_column_if_not_exists('dre_lancamentos', 'updated_at', "DATETIME DEFAULT NULL AFTER `created_at`");
CALL add_column_if_not_exists('dre_lancamentos', 'tipo_movimento', "ENUM('CREDITO','DEBITO') DEFAULT 'CREDITO' AFTER `valor`");
CALL add_column_if_not_exists('dre_lancamentos', 'documento', "VARCHAR(100) DEFAULT NULL AFTER `tipo_movimento`");
CALL create_index_if_not_exists('dre_lancamentos', 'idx_conta_id', '`conta_id`');
CALL create_index_if_not_exists('dre_lancamentos', 'idx_data_referencia', '`data`');
CALL create_index_if_not_exists('dre_lancamentos', 'idx_os_id', '`os_id`');

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
CALL create_index_if_not_exists('dre_demonstracoes', 'idx_data', '`data_inicio`, `data_fim`');
CALL create_index_if_not_exists('dre_demonstracoes', 'idx_tipo', '`tipo`');

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
CALL create_index_if_not_exists('impostos_retidos', 'idx_id_os', '`id_os`');
CALL create_index_if_not_exists('impostos_retidos', 'idx_id_venda', '`id_venda`');
CALL create_index_if_not_exists('impostos_retidos', 'idx_tipo_imposto', '`tipo_imposto`');

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

CALL add_column_if_not_exists('obra_etapas', 'progresso_real', "INT(3) DEFAULT 0 COMMENT 'Progresso baseado nas atividades registradas'");

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
CALL add_column_if_not_exists('certificado_digital', 'ambiente', "ENUM('homologacao','producao') DEFAULT 'homologacao' AFTER `ativo`");

-- ============================================================
-- MIGRATION: 20260428000001 - remove lucro_presumido (skip - config change)
-- ============================================================
-- (Config change only - no schema changes)

-- ============================================================
-- MIGRATION: 20260428000002 - add inscricoes to clientes
-- ============================================================
CALL add_column_if_not_exists('clientes', 'inscricao_estadual', "VARCHAR(50) DEFAULT NULL");
CALL add_column_if_not_exists('clientes', 'inscricao_municipal', "VARCHAR(50) DEFAULT NULL");

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
CALL add_column_if_not_exists('os_nfse_emitida', 'n_dps', "VARCHAR(50) DEFAULT NULL");

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
CALL create_index_if_not_exists('agente_ia_permissoes', 'idx_acao', '`acao`');
CALL create_index_if_not_exists('agente_ia_permissoes', 'idx_ativo', '`ativo`');

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
CALL create_index_if_not_exists('agente_ia_autorizacoes', 'idx_expires_at', '`expires_at`');
CALL create_index_if_not_exists('agente_ia_autorizacoes', 'idx_acao_status', '`acao`, `status`');
CALL create_index_if_not_exists('agente_ia_autorizacoes', 'idx_usuarios_id', '`numero_telefone`');

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
CALL create_index_if_not_exists('whatsapp_log_interacoes', 'idx_numero', '`numero_telefone`');
CALL create_index_if_not_exists('whatsapp_log_interacoes', 'idx_intencao', '`intencao_detectada`');
CALL create_index_if_not_exists('whatsapp_log_interacoes', 'idx_created', '`created_at`');

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
CALL create_index_if_not_exists('agente_ia_notificacoes_agendadas', 'idx_numero', '`numero_telefone`');
CALL create_index_if_not_exists('agente_ia_notificacoes_agendadas', 'idx_tipo', '`tipo_notificacao`');

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
CALL add_column_if_not_exists('os', 'deleted_at', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('clientes', 'deleted_at', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('produtos', 'deleted_at', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('servicos', 'deleted_at', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('usuarios', 'deleted_at', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('lancamentos', 'deleted_at', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('cobrancas', 'deleted_at', "DATETIME DEFAULT NULL");

-- ============================================================
-- MIGRATION: 20260524000003 - performance indexes
-- ============================================================
CALL create_index_if_not_exists('os', 'idx_os_status', '`status`');
CALL create_index_if_not_exists('os', 'idx_os_dataInicial', '`dataInicial`');
CALL create_index_if_not_exists('os', 'idx_os_clientes_id', '`clientes_id`');
CALL create_index_if_not_exists('os', 'idx_os_usuarios_id', '`usuarios_id`');
CALL create_index_if_not_exists('os', 'idx_os_status_data', '`status`, `dataInicial`');
CALL create_index_if_not_exists('lancamentos', 'idx_lanc_baixado', '`baixado`');
CALL create_index_if_not_exists('lancamentos', 'idx_lanc_data_vencimento', '`data_vencimento`');
CALL create_index_if_not_exists('lancamentos', 'idx_lanc_tipo', '`tipo`');
CALL create_index_if_not_exists('lancamentos', 'idx_lanc_tipo_baixado', '`tipo`, `baixado`');
CALL create_index_if_not_exists('produtos_os', 'idx_produtos_os_os_id', '`os_id`');
CALL create_index_if_not_exists('servicos_os', 'idx_servicos_os_os_id', '`os_id`');
CALL create_index_if_not_exists('clientes', 'idx_clientes_nomeCliente', '`nomeCliente`');
CALL create_index_if_not_exists('clientes', 'idx_clientes_documento', '`documento`');
CALL create_index_if_not_exists('clientes', 'idx_clientes_email', '`email`');
CALL create_index_if_not_exists('usuarios', 'idx_usuarios_email', '`email`');
CALL create_index_if_not_exists('usuarios', 'idx_usuarios_situacao', '`situacao`');
CALL create_index_if_not_exists('cobrancas', 'idx_cobrancas_status', '`status`');
CALL create_index_if_not_exists('cobrancas', 'idx_cobrancas_os_id', '`os_id`');

-- ============================================================
-- MIGRATION: 20260525000001 - LGPD + token_acesso
-- ============================================================
CALL add_column_if_not_exists('clientes', 'consentimento_lgpd', "TINYINT(1) DEFAULT 0");
CALL add_column_if_not_exists('clientes', 'data_consentimento', "DATETIME DEFAULT NULL");
CALL add_column_if_not_exists('clientes', 'origem_dados', "VARCHAR(50) DEFAULT NULL");
CALL add_column_if_not_exists('clientes', 'token_acesso', "VARCHAR(64) DEFAULT NULL");
CALL create_index_if_not_exists('clientes', 'idx_clientes_token_acesso', '`token_acesso`');

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
CALL create_index_if_not_exists('audit_log', 'idx_audit_table_record', '`table_name`, `record_id`');
CALL create_index_if_not_exists('audit_log', 'idx_audit_user', '`user_id`');
CALL create_index_if_not_exists('audit_log', 'idx_audit_action', '`action`');
CALL create_index_if_not_exists('audit_log', 'idx_audit_created', '`created_at`');

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
-- LIMPAR PROCEDIMENTOS
-- ============================================================
DROP PROCEDURE IF EXISTS `add_column_if_not_exists`;
DROP PROCEDURE IF EXISTS `create_index_if_not_exists`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- FIM DO SCRIPT
-- ============================================================