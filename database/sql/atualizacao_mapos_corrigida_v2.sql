-- ========================================================
-- ATUALIZAÇÃO COMPLETA DO MAPOS - VERSÃO CORRIGIDA
-- Corrigido: colunas da tabela os (idOs, não id)
-- Data: 2026-04-12
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ========================================================
-- 1. SISTEMA DRE CONTÁBIL
-- ========================================================

CREATE TABLE IF NOT EXISTS `dre_demonstracoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `descricao` TEXT,
    `data_inicio` DATE NOT NULL,
    `data_fim` DATE NOT NULL,
    `tipo` ENUM('mensal', 'trimestral', 'anual') DEFAULT 'mensal',
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_periodo` (`data_inicio`, `data_fim`),
    KEY `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `dre_contas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `codigo` VARCHAR(20) NOT NULL,
    `nome` VARCHAR(255) NOT NULL,
    `descricao` TEXT,
    `tipo` ENUM('receita', 'custo', 'despesa', 'deducao', 'resultado') NOT NULL,
    `categoria` VARCHAR(100),
    `conta_pai_id` INT(11) UNSIGNED DEFAULT NULL,
    `ordem` INT(5) DEFAULT 0,
    `formula` TEXT COMMENT 'Fórmula de cálculo se conta calculada',
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_codigo` (`codigo`),
    KEY `idx_tipo` (`tipo`),
    KEY `idx_pai` (`conta_pai_id`),
    KEY `idx_ordem` (`ordem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `dre_lancamentos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `demonstracao_id` INT(11) UNSIGNED NOT NULL,
    `conta_id` INT(11) UNSIGNED NOT NULL,
    `descricao` VARCHAR(255),
    `valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `data_lancamento` DATE NOT NULL,
    `referencia_id` INT(11) COMMENT 'ID da OS, venda ou outro documento',
    `referencia_tipo` VARCHAR(50) COMMENT 'os, venda, despesa, etc.',
    `observacoes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_demonstracao` (`demonstracao_id`),
    KEY `idx_conta` (`conta_id`),
    KEY `idx_data` (`data_lancamento`),
    KEY `idx_referencia` (`referencia_tipo`, `referencia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `dre_contas` (`codigo`, `nome`, `descricao`, `tipo`, `categoria`, `ordem`) VALUES
('1', 'RECEITAS OPERACIONAIS', 'Receitas com vendas e serviços', 'receita', 'receita', 100),
('1.1', 'Vendas de Produtos', 'Receita com vendas de produtos', 'receita', 'vendas', 110),
('1.2', 'Serviços Prestados', 'Receita com prestação de serviços', 'receita', 'servicos', 120),
('2', 'DEDUÇÕES DA RECEITA', 'Impostos e descontos sobre vendas', 'deducao', 'deducao', 200),
('2.1', 'ICMS sobre Vendas', 'ICMS incidente sobre vendas', 'deducao', 'impostos', 210),
('2.2', 'PIS/COFINS', 'PIS e COFINS sobre faturamento', 'deducao', 'impostos', 220),
('2.3', 'ISSQN', 'ISS sobre serviços', 'deducao', 'impostos', 230),
('2.4', 'Descontos Incondicionais', 'Descontos concedidos nas vendas', 'deducao', 'descontos', 240),
('3', 'RECEITA LÍQUIDA', 'Receita bruta menos deduções', 'resultado', 'resultado', 300),
('4', 'CUSTOS', 'Custos dos produtos e serviços vendidos', 'custo', 'custos', 400),
('4.1', 'CPV - Custo dos Produtos', 'Custo das mercadorias vendidas', 'custo', 'cpv', 410),
('4.2', 'CSP - Custo dos Serviços', 'Custo dos serviços prestados', 'custo', 'csp', 420),
('4.3', 'Mão de Obra Direta', 'Salários e encargos da equipe técnica', 'custo', 'mod', 430),
('4.4', 'Material de Consumo', 'Materiais utilizados nos serviços', 'custo', 'insumos', 440),
('5', 'LUCRO BRUTO', 'Receita líquida menos custos', 'resultado', 'resultado', 500),
('6', 'DESPESAS OPERACIONAIS', 'Despesas administrativas e comerciais', 'despesa', 'despesas', 600),
('6.1', 'Despesas Administrativas', 'Despesas gerais e administrativas', 'despesa', 'administrativas', 610),
('6.2', 'Despesas Comerciais', 'Despesas de vendas e marketing', 'despesa', 'comerciais', 620),
('6.3', 'Despesas com Pessoal', 'Salários administrativos e encargos', 'despesa', 'pessoal', 630),
('6.4', 'Aluguel e Condomínio', 'Despesas com imóvel', 'despesa', 'imoveis', 640),
('6.5', 'Serviços de Terceiros', 'Contador, advogado, consultorias', 'despesa', 'terceiros', 650),
('6.6', 'Despesas Financeiras', 'Juros, tarifas bancárias, etc.', 'despesa', 'financeiras', 660),
('7', 'LAIR', 'Lucro antes dos impostos', 'resultado', 'resultado', 700),
('8', 'IMPOSTOS SOBRE LUCRO', 'IRPJ e CSLL', 'despesa', 'impostos', 800),
('8.1', 'IRPJ', 'Imposto de Renda Pessoa Jurídica', 'despesa', 'irpj', 810),
('8.2', 'CSLL', 'Contribuição Social sobre Lucro Líquido', 'despesa', 'csll', 820),
('9', 'LUCRO/PREJUÍZO LÍQUIDO', 'Resultado final do período', 'resultado', 'resultado', 900);

-- ========================================================
-- 2. SISTEMA DE IMPOSTOS
-- ========================================================

CREATE TABLE IF NOT EXISTS `certificados digitais` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `cnpj` VARCHAR(18) NOT NULL,
    `tipo` ENUM('A1', 'A3') DEFAULT 'A1',
    `arquivo_pfx` VARCHAR(500),
    `senha_criptografada` TEXT COMMENT 'Senha criptografada com AES-256',
    `data_emissao` DATE,
    `data_validade` DATE NOT NULL,
    `data_vencimento` DATE,
    `emissor` VARCHAR(255),
    `ativo` TINYINT(1) DEFAULT 1,
    `ultima_verificacao` DATETIME,
    `status_validacao` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cnpj` (`cnpj`),
    KEY `idx_validade` (`data_validade`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `configuracoes_impostos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cnpj` VARCHAR(18) NOT NULL,
    `razao_social` VARCHAR(255),
    `anexo_simples` ENUM('I', 'II', 'III', 'IV', 'V') DEFAULT 'III',
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
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cnpj` (`cnpj`),
    KEY `idx_anexo` (`anexo_simples`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `calculos_impostos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cnpj` VARCHAR(18) NOT NULL,
    `os_id` INT(11),
    `venda_id` INT(11),
    `cobranca_id` INT(11),
    `valor_bruto` DECIMAL(15,2) NOT NULL,
    `valor_liquido` DECIMAL(15,2) NOT NULL,
    `iss` DECIMAL(15,2) DEFAULT 0.00,
    `pis` DECIMAL(15,2) DEFAULT 0.00,
    `cofins` DECIMAL(15,2) DEFAULT 0.00,
    `csll` DECIMAL(15,2) DEFAULT 0.00,
    `inss` DECIMAL(15,2) DEFAULT 0.00,
    `ir` DECIMAL(15,2) DEFAULT 0.00,
    `total_impostos` DECIMAL(15,2) DEFAULT 0.00,
    `aliquota_efetiva` DECIMAL(5,2),
    `competencia` DATE NOT NULL,
    `status` ENUM('calculado', 'retido', 'recolhido', 'cancelado') DEFAULT 'calculado',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cnpj` (`cnpj`),
    KEY `idx_os` (`os_id`),
    KEY `idx_venda` (`venda_id`),
    KEY `idx_cobranca` (`cobranca_id`),
    KEY `idx_competencia` (`competencia`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `nfse_importadas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cnpj` VARCHAR(18) NOT NULL,
    `numero_nota` VARCHAR(50) NOT NULL,
    `codigo_verificacao` VARCHAR(20),
    `data_emissao` DATETIME,
    `valor_servicos` DECIMAL(15,2) DEFAULT 0.00,
    `valor_deducoes` DECIMAL(15,2) DEFAULT 0.00,
    `valor_iss` DECIMAL(15,2) DEFAULT 0.00,
    `aliquota_iss` DECIMAL(5,2) DEFAULT 0.00,
    `valor_pis` DECIMAL(15,2) DEFAULT 0.00,
    `valor_cofins` DECIMAL(15,2) DEFAULT 0.00,
    `valor_csll` DECIMAL(15,2) DEFAULT 0.00,
    `valor_inss` DECIMAL(15,2) DEFAULT 0.00,
    `valor_ir` DECIMAL(15,2) DEFAULT 0.00,
    `tomador_cnpj` VARCHAR(18),
    `tomador_nome` VARCHAR(255),
    `xml_conteudo` LONGTEXT,
    `status` ENUM('pendente', 'processada', 'cancelada') DEFAULT 'pendente',
    `os_vinculada_id` INT(11),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_nota` (`cnpj`, `numero_nota`),
    KEY `idx_cnpj` (`cnpj`),
    KEY `idx_tomador` (`tomador_cnpj`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- 3. SISTEMA DE USUÁRIOS DO CLIENTE
-- ========================================================

CREATE TABLE IF NOT EXISTS `usuarios_cliente` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `telefone` VARCHAR(20),
    `celular` VARCHAR(20),
    `ativo` TINYINT(1) DEFAULT 1,
    `ultimo_acesso` DATETIME,
    `token_reset` VARCHAR(255),
    `token_expira` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `idx_token` (`token_reset`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `usuarios_cliente_cnpjs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) UNSIGNED NOT NULL,
    `cnpj` VARCHAR(18) NOT NULL,
    `razao_social` VARCHAR(255),
    `nome_fantasia` VARCHAR(255),
    `principal` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_usuario_cnpj` (`usuario_id`, `cnpj`),
    KEY `idx_cnpj` (`cnpj`),
    KEY `idx_principal` (`principal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `usuarios_cliente_permissoes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) UNSIGNED NOT NULL,
    `permissao` VARCHAR(100) NOT NULL,
    `valor` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_usuario_permissao` (`usuario_id`, `permissao`),
    KEY `idx_permissao` (`permissao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- 4. SISTEMA DE WEBHOOKS
-- ========================================================

CREATE TABLE IF NOT EXISTS `webhooks` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `url` TEXT NOT NULL,
    `events` TEXT COMMENT 'JSON array de eventos',
    `secret` VARCHAR(255),
    `active` TINYINT(1) DEFAULT 1,
    `retry_count` TINYINT(1) DEFAULT 3,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `webhook_logs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `webhook_id` INT(11) UNSIGNED NOT NULL,
    `event` VARCHAR(100) NOT NULL,
    `payload` LONGTEXT COMMENT 'JSON do payload enviado',
    `response` LONGTEXT,
    `http_code` INT(4),
    `success` TINYINT(1) DEFAULT 0,
    `error` TEXT,
    `attempt` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_webhook` (`webhook_id`),
    KEY `idx_event` (`event`),
    KEY `idx_success` (`success`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- 5. VINCULAÇÃO DE DOCUMENTOS À OS
-- ========================================================

CREATE TABLE IF NOT EXISTS `os_documentos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `tipo` ENUM('boleto', 'nfse', 'nfe', 'nfce', 'recibo', 'contrato', 'outro') NOT NULL,
    `descricao` VARCHAR(255),
    `numero_documento` VARCHAR(100),
    `valor` DECIMAL(15,2),
    `data_emissao` DATE,
    `data_vencimento` DATE,
    `status` VARCHAR(50),
    `arquivo` VARCHAR(500),
    `link_externo` TEXT,
    `gateway_id` VARCHAR(100) COMMENT 'ID do boleto no gateway de pagamento',
    `charge_id` VARCHAR(100) COMMENT 'ID da cobrança',
    `nfse_id` INT(11),
    `observacoes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_os` (`os_id`),
    KEY `idx_tipo` (`tipo`),
    KEY `idx_gateway` (`gateway_id`),
    KEY `idx_charge` (`charge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- 6. COLUNAS ADICIONAIS NA TABELA OS (CORRIGIDO)
-- ========================================================
-- NOTA: A tabela OS no MAPOS tem idOs, valorTotal, garantias_id
-- As colunas serão adicionadas no final da tabela

-- Verificar e adicionar colunas uma a uma
SET @dbname = DATABASE();

-- Coluna certificado_vinculado
SET @coluna_certificado = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = 'os'
    AND COLUMN_NAME = 'certificado_vinculado'
);
SET @sql = IF(@coluna_certificado = 0,
    'ALTER TABLE os ADD COLUMN certificado_vinculado INT(11) NULL',
    'SELECT "Coluna certificado_vinculado já existe"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Coluna retencao_impostos
SET @coluna_retencao = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = 'os'
    AND COLUMN_NAME = 'retencao_impostos'
);
SET @sql = IF(@coluna_retencao = 0,
    'ALTER TABLE os ADD COLUMN retencao_impostos TINYINT(1) DEFAULT 0',
    'SELECT "Coluna retencao_impostos já existe"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Coluna valor_liquido
SET @coluna_liquido = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = 'os'
    AND COLUMN_NAME = 'valor_liquido'
);
SET @sql = IF(@coluna_liquido = 0,
    'ALTER TABLE os ADD COLUMN valor_liquido DECIMAL(15,2) NULL',
    'SELECT "Coluna valor_liquido já existe"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Coluna calculo_impostos
SET @coluna_calculo = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = 'os'
    AND COLUMN_NAME = 'calculo_impostos'
);
SET @sql = IF(@coluna_calculo = 0,
    'ALTER TABLE os ADD COLUMN calculo_impostos TEXT NULL COMMENT "JSON com valores dos impostos"',
    'SELECT "Coluna calculo_impostos já existe"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Coluna em lancamentos
SET @tabela_lancamentos = (
    SELECT COUNT(*)
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = 'lancamentos'
);

SET @coluna_webhook = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = 'lancamentos'
    AND COLUMN_NAME = 'webhook_notificado'
);

SET @sql = IF(@tabela_lancamentos > 0 AND @coluna_webhook = 0,
    'ALTER TABLE lancamentos ADD COLUMN webhook_notificado TINYINT(1) DEFAULT 0',
    'SELECT "Tabela lancamentos não existe ou coluna já existe"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- FIM DO SCRIPT - SUCESSO!
-- ========================================================
