-- =============================================================================
-- SQL: Tabelas do Agente IA com Autorizacoes - MapOS V5
-- =============================================================================
-- Versao: 1.0
-- Data: 2026-05-01
-- Descricao: Cria as tabelas necessarias para o sistema de agente IA com
--            controle de acessos, autorizacoes e relatorios agendados.
--
-- Tabelas criadas:
-- 1. whatsapp_integracao     (vinculo de numeros com clientes/usuarios)
-- 2. agente_ia_autorizacoes  (tokens de autorizacao para acoes criticas)
-- 3. agente_ia_permissoes    (regras de acesso por perfil)
-- 4. agente_ia_relatorios_agendados (relatorios agendados via WhatsApp)
-- =============================================================================

SET SQL_MODE='ALLOW_INVALID_DATES';
SET FOREIGN_KEY_CHECKS=0;

-- =============================================================================
-- 1. TABELA: whatsapp_integracao
-- =============================================================================
-- Ja utilizada por scripts existentes (setup_agente.sh, whatsapp-agent).
-- Garante a existencia com colunas padrao se ainda nao existir.

CREATE TABLE IF NOT EXISTS `whatsapp_integracao` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_telefone` VARCHAR(20) NOT NULL COMMENT 'Numero com DDD e DDI, ex: 5592912345678',
  `clientes_id` INT(11) NULL DEFAULT NULL COMMENT 'Vinculo com cliente',
  `usuarios_id` INT(11) NULL DEFAULT NULL COMMENT 'Vinculo com usuario interno',
  `situacao` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `ultima_interacao` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_numero_telefone` (`numero_telefone`),
  INDEX `idx_clientes_id` (`clientes_id`),
  INDEX `idx_usuarios_id` (`usuarios_id`),
  INDEX `idx_situacao` (`situacao`),
  INDEX `idx_numero_situacao` (`numero_telefone`, `situacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Vinculo de numeros WhatsApp com clientes ou usuarios do sistema';

-- Coluna situacao caso tabela ja exista sem ela
SET @existe_situacao = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'whatsapp_integracao'
  AND COLUMN_NAME = 'situacao');

SET @sql_situacao = IF(@existe_situacao = 0,
  'ALTER TABLE `whatsapp_integracao` ADD COLUMN `situacao` TINYINT(1) NOT NULL DEFAULT 1 COMMENT \'1=Ativo, 0=Inativo\', ADD INDEX `idx_situacao` (`situacao`), ADD INDEX `idx_numero_situacao` (`numero_telefone`, `situacao`)',
  'SELECT 1');
PREPARE stmt FROM @sql_situacao;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Coluna ultima_interacao caso nao exista
SET @existe_ultima_interacao = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'whatsapp_integracao'
  AND COLUMN_NAME = 'ultima_interacao');

SET @sql_ultima = IF(@existe_ultima_interacao = 0,
  'ALTER TABLE `whatsapp_integracao` ADD COLUMN `ultima_interacao` DATETIME NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql_ultima;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================================================
-- 2. TABELA: agente_ia_autorizacoes
-- =============================================================================
-- Controle de autorizacoes para acoes criticas executadas pelo agente IA.
-- Cada acao que exige confirmacao gera um token unico com prazo de expiracao.

CREATE TABLE IF NOT EXISTS `agente_ia_autorizacoes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` VARCHAR(64) NOT NULL COMMENT 'Token unico da autorizacao (ex: AUTH-7X9K2M)',
  `numero_telefone` VARCHAR(20) NOT NULL COMMENT 'Numero que solicitou a acao',
  `usuarios_id` INT(11) NULL DEFAULT NULL COMMENT 'Usuario vinculado, se houver',
  `clientes_id` INT(11) NULL DEFAULT NULL COMMENT 'Cliente vinculado, se houver',
  `acao` VARCHAR(100) NOT NULL COMMENT 'ex: criar_os, aprovar_orcamento',
  `dados_json` JSON NOT NULL COMMENT 'Parametros da acao serializados',
  `nivel_criticidade` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=leitura, 5=critica',
  `status` ENUM('pendente','aprovada','rejeitada','expirada','executada') DEFAULT 'pendente',
  `metodo_autorizacao` ENUM('whatsapp','email','painel') DEFAULT 'whatsapp',
  `resposta_usuario` VARCHAR(255) NULL COMMENT 'Texto da resposta do usuario',
  `ip_autorizacao` VARCHAR(45) NULL COMMENT 'IP de origem no momento da resposta',
  `user_agent` VARCHAR(255) NULL,
  `executado_por` ENUM('agente_ia','usuario') DEFAULT 'agente_ia',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL COMMENT 'Validade do token (ex: NOW() + INTERVAL 15 MINUTE)',
  `executed_at` DATETIME NULL COMMENT 'Quando a acao foi efetivamente executada',
  `resultado_json` JSON NULL COMMENT 'Retorno da execucao da acao',
  `observacoes` TEXT NULL COMMENT 'Observacoes adicionais',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  INDEX `idx_status` (`status`),
  INDEX `idx_numero_telefone` (`numero_telefone`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_expires_at` (`expires_at`),
  INDEX `idx_acao_status` (`acao`, `status`),
  INDEX `idx_usuarios_id` (`usuarios_id`),
  INDEX `idx_clientes_id` (`clientes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tokens de autorizacao para acoes do agente IA';

-- =============================================================================
-- 3. TABELA: agente_ia_permissoes
-- =============================================================================
-- Regras de acesso: define quais perfis podem executar quais acoes,
-- ate qual nivel maximo sem autorizacao, e se requer 2FA.

CREATE TABLE IF NOT EXISTS `agente_ia_permissoes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `perfil` ENUM('cliente','tecnico','admin','financeiro','vendedor','desconhecido') NOT NULL DEFAULT 'desconhecido',
  `acao` VARCHAR(100) NOT NULL COMMENT 'Nome da acao: ex: criar_os, consultar_status_os',
  `nivel_maximo_automatico` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Ate qual nivel o agente executa sem pedir (1-5)',
  `requer_2fa` TINYINT(1) DEFAULT 0 COMMENT '1=Requer segundo fator (codigo por email)',
  `horario_permitido_inicio` TIME DEFAULT '00:00:00',
  `horario_permitido_fim` TIME DEFAULT '23:59:59',
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_perfil_acao` (`perfil`, `acao`),
  INDEX `idx_perfil` (`perfil`),
  INDEX `idx_acao` (`acao`),
  INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Permissoes do agente IA por perfil';

-- -----------------------------------------------------------------------------
-- Seed de permissoes padrao
-- -----------------------------------------------------------------------------
INSERT INTO `agente_ia_permissoes` (`perfil`, `acao`, `nivel_maximo_automatico`, `requer_2fa`) VALUES
('cliente', 'consultar_status_os', 1, 0),
('cliente', 'consultar_divida', 1, 0),
('cliente', 'consultar_cliente', 1, 0),
('cliente', 'consultar_estoque', 1, 0),
('cliente', 'solicitar_orcamento', 2, 0),
('tecnico', 'consultar_minhas_os', 1, 0),
('tecnico', 'consultar_status_os', 1, 0),
('tecnico', 'atualizar_status_os', 3, 0),
('tecnico', 'registrar_atividade', 3, 0),
('tecnico', 'criar_os', 3, 0),
('admin', 'consultar_status_os', 1, 0),
('admin', 'consultar_cliente', 1, 0),
('admin', 'consultar_estoque', 1, 0),
('admin', 'consultar_lancamentos', 1, 0),
('admin', 'criar_os', 3, 0),
('admin', 'atualizar_status_os', 3, 0),
('admin', 'registrar_atividade', 3, 0),
('admin', 'aprovar_orcamento', 4, 1),
('admin', 'gerar_cobranca', 4, 1),
('admin', 'emitir_nfse', 5, 1),
('financeiro', 'consultar_lancamentos', 1, 0),
('financeiro', 'consultar_divida', 1, 0),
('financeiro', 'gerar_boleto', 4, 1),
('financeiro', 'gerar_cobranca', 4, 1),
('vendedor', 'consultar_cliente', 1, 0),
('vendedor', 'consultar_estoque', 1, 0),
('vendedor', 'solicitar_orcamento', 2, 0),
('desconhecido', 'consultar_status_os', 1, 0)
ON DUPLICATE KEY UPDATE
  `nivel_maximo_automatico` = VALUES(`nivel_maximo_automatico`),
  `requer_2fa` = VALUES(`requer_2fa`),
  `ativo` = 1;

-- =============================================================================
-- 4. TABELA: agente_ia_relatorios_agendados
-- =============================================================================
-- Relatorios agendados pelo usuario via comandos WhatsApp com o agente IA.
-- Ex: "Manda todo dia as 8h o relatorio de OS"

CREATE TABLE IF NOT EXISTS `agente_ia_relatorios_agendados` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_telefone` VARCHAR(20) NOT NULL,
  `usuarios_id` INT(11) NULL DEFAULT NULL,
  `tipo_relatorio` VARCHAR(50) NOT NULL COMMENT 'os_diario, os_mensal, financeiro, vendas, estoque, etc',
  `parametros_json` JSON DEFAULT NULL COMMENT 'Filtros e parametros do relatorio',
  `formato` ENUM('texto','pdf_whatsapp','pdf_email','audio') DEFAULT 'texto',
  `frequencia` ENUM('diario','semanal','mensal','unico') NOT NULL,
  `horario` TIME DEFAULT '08:00:00',
  `dia_semana` TINYINT(1) NULL COMMENT '0=dom, 1=seg... para semanal',
  `dia_mes` TINYINT(2) NULL COMMENT '1-31 para mensal',
  `ativo` TINYINT(1) DEFAULT 1,
  `ultimo_envio` DATETIME NULL,
  `proximo_envio` DATETIME NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_numero_telefone` (`numero_telefone`),
  INDEX `idx_proximo_envio` (`proximo_envio`),
  INDEX `idx_ativo` (`ativo`),
  INDEX `idx_usuarios_id` (`usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Relatorios agendados pelo agente IA via WhatsApp';

-- =============================================================================
-- 5. TABELA: agente_ia_logs_conversa
-- =============================================================================
-- Historico de interacoes entre o agente IA e os usuarios via WhatsApp,
-- para auditoria e depuracao.

CREATE TABLE IF NOT EXISTS `agente_ia_logs_conversa` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_telefone` VARCHAR(20) NOT NULL,
  `usuarios_id` INT(11) NULL DEFAULT NULL,
  `clientes_id` INT(11) NULL DEFAULT NULL,
  `tipo` ENUM('recebido','enviado','sistema','erro') NOT NULL DEFAULT 'recebido',
  `mensagem` TEXT NOT NULL COMMENT 'Conteudo da mensagem',
  `intencao_detectada` VARCHAR(100) NULL COMMENT 'Intencao classificada pelo LLM',
  `acao_executada` VARCHAR(100) NULL COMMENT 'Acao realizada em resposta',
  `metadados_json` JSON NULL COMMENT 'Dados extras: token, os_id, etc',
  `ip_origem` VARCHAR(45) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_numero_telefone` (`numero_telefone`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_intencao` (`intencao_detectada`),
  INDEX `idx_usuarios_id` (`usuarios_id`),
  INDEX `idx_clientes_id` (`clientes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Log de conversas com o agente IA';

-- =============================================================================
-- FIM DO SCRIPT
-- =============================================================================
SET FOREIGN_KEY_CHECKS=1;
