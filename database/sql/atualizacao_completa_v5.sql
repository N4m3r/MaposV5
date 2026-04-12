-- =============================================
-- ATUALIZAÇÃO COMPLETA MAPOS V5
-- Script SQL para criar todas as tabelas necessárias
-- =============================================

-- Tabela: email_queue (Fila de Emails)
CREATE TABLE IF NOT EXISTS email_queue (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    to_email VARCHAR(255) NOT NULL,
    to_name VARCHAR(255) NULL,
    subject VARCHAR(500) NOT NULL,
    body_html LONGTEXT NULL,
    body_text LONGTEXT NULL,
    template VARCHAR(100) NULL,
    template_data TEXT NULL,
    attachments TEXT NULL,
    priority TINYINT(1) DEFAULT 3,
    status ENUM('pending', 'processing', 'sent', 'failed', 'cancelled', 'scheduled') DEFAULT 'pending',
    attempts TINYINT(1) DEFAULT 0,
    max_retries TINYINT(1) DEFAULT 3,
    tracking_id VARCHAR(32) NULL,
    message_id VARCHAR(255) NULL,
    scheduled_at DATETIME NULL,
    sent_at DATETIME NULL,
    opened_at DATETIME NULL,
    clicked_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    last_attempt DATETIME NULL,
    failed_at DATETIME NULL,
    error_message TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_tracking_id (tracking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: email_tracking (Rastreamento de aberturas)
CREATE TABLE IF NOT EXISTS email_tracking (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email_queue_id INT(11) UNSIGNED NOT NULL,
    tracking_id VARCHAR(64) NOT NULL UNIQUE,
    opened TINYINT(1) DEFAULT 0,
    opened_at DATETIME NULL,
    clicked TINYINT(1) DEFAULT 0,
    clicked_at DATETIME NULL,
    clicked_url TEXT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_tracking_id (tracking_id),
    INDEX idx_email_queue_id (email_queue_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: email_clicks (Registro de cliques)
CREATE TABLE IF NOT EXISTS email_clicks (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(32) NOT NULL,
    url TEXT NOT NULL,
    clicked_at DATETIME NOT NULL,
    ip_address VARCHAR(45) NULL,
    INDEX idx_tracking_id (tracking_id),
    INDEX idx_clicked_at (clicked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: scheduled_events (Eventos Agendados)
CREATE TABLE IF NOT EXISTS scheduled_events (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) NOT NULL,
    event_data JSON NULL,
    execute_at DATETIME NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    executed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_status (status),
    INDEX idx_execute_at (execute_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: webhooks
CREATE TABLE IF NOT EXISTS webhooks (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    secret VARCHAR(255) NULL,
    events JSON NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: webhook_logs
CREATE TABLE IF NOT EXISTS webhook_logs (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    webhook_id INT(11) UNSIGNED NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    payload TEXT NULL,
    response TEXT NULL,
    status_code INT NULL,
    success TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX idx_webhook_id (webhook_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: certificado_config
CREATE TABLE IF NOT EXISTS certificado_config (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_emitente INT(11) UNSIGNED NOT NULL,
    certificado_p12 LONGTEXT NULL,
    senha_certificado VARCHAR(255) NULL,
    cnpj_certificado VARCHAR(14) NULL,
    valido_de DATETIME NULL,
    valido_ate DATETIME NULL,
    arquivo_crt LONGTEXT NULL,
    arquivo_key LONGTEXT NULL,
    ambiente ENUM('homologacao', 'producao') DEFAULT 'homologacao',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_id_emitente (id_emitente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: nfse_importada
CREATE TABLE IF NOT EXISTS nfse_importada (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_os INT(11) UNSIGNED NULL,
    numero_nfse VARCHAR(50) NOT NULL,
    codigo_verificacao VARCHAR(50) NULL,
    data_emissao DATETIME NULL,
    valor_servico DECIMAL(10,2) NULL,
    valor_liquido DECIMAL(10,2) NULL,
    prestador_cnpj VARCHAR(14) NULL,
    prestador_nome VARCHAR(255) NULL,
    tomador_cnpj VARCHAR(14) NULL,
    tomador_nome VARCHAR(255) NULL,
    status ENUM('ativa', 'cancelada') DEFAULT 'ativa',
    xml_content LONGTEXT NULL,
    pdf_content LONGTEXT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_id_os (id_os),
    INDEX idx_numero_nfse (numero_nfse),
    INDEX idx_prestador_cnpj (prestador_cnpj)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: dre_contas
CREATE TABLE IF NOT EXISTS dre_contas (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    tipo ENUM('receita', 'custo', 'despesa') NOT NULL,
    categoria VARCHAR(100) NULL,
    pai_id INT(11) UNSIGNED NULL,
    ordem INT DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_tipo (tipo),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: dre_lancamentos
CREATE TABLE IF NOT EXISTS dre_lancamentos (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conta_id INT(11) UNSIGNED NOT NULL,
    data_referencia DATE NOT NULL,
    valor DECIMAL(15,2) NOT NULL,
    descricao TEXT NULL,
    id_os INT(11) UNSIGNED NULL,
    id_venda INT(11) UNSIGNED NULL,
    id_lancamento INT(11) UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_conta_id (conta_id),
    INDEX idx_data_referencia (data_referencia),
    INDEX idx_id_os (id_os)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: impostos_config
CREATE TABLE IF NOT EXISTS impostos_config (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_regime ENUM('simples_nacional', 'lucro_presumido', 'lucro_real') DEFAULT 'simples_nacional',
    anexo_simples ENUM('i', 'ii', 'iii', 'iv', 'v') NULL,
    aliquota_iss DECIMAL(5,2) DEFAULT 0,
    retem_iss TINYINT(1) DEFAULT 0,
    aliquota_pis DECIMAL(5,2) DEFAULT 0,
    aliquota_cofins DECIMAL(5,2) DEFAULT 0,
    aliquota_csll DECIMAL(5,2) DEFAULT 0,
    aliquota_ir DECIMAL(5,2) DEFAULT 0,
    aliquota_inss DECIMAL(5,2) DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: impostos_retidos
CREATE TABLE IF NOT EXISTS impostos_retidos (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_os INT(11) UNSIGNED NULL,
    id_venda INT(11) UNSIGNED NULL,
    tipo_imposto ENUM('iss', 'pis', 'cofins', 'csll', 'ir', 'inss') NOT NULL,
    base_calculo DECIMAL(15,2) NOT NULL,
    aliquota DECIMAL(5,2) NOT NULL,
    valor_retido DECIMAL(15,2) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_id_os (id_os),
    INDEX idx_id_venda (id_venda),
    INDEX idx_tipo_imposto (tipo_imposto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: push_notifications
CREATE TABLE IF NOT EXISTS push_notifications (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados iniciais para DRE
INSERT INTO dre_contas (codigo, nome, tipo, categoria, ordem, ativo, created_at, updated_at) VALUES
('1', 'RECEITA BRUTA', 'receita', 'Receitas', 1, 1, NOW(), NOW()),
('1.1', 'Serviços', 'receita', 'Receitas', 2, 1, NOW(), NOW()),
('1.2', 'Produtos', 'receita', 'Receitas', 3, 1, NOW(), NOW()),
('2', 'IMPOSTOS', 'despesa', 'Impostos', 10, 1, NOW(), NOW()),
('2.1', 'ISS', 'despesa', 'Impostos', 11, 1, NOW(), NOW()),
('2.2', 'PIS/COFINS', 'despesa', 'Impostos', 12, 1, NOW(), NOW()),
('3', 'CUSTOS', 'custo', 'Custos', 20, 1, NOW(), NOW()),
('3.1', 'Material', 'custo', 'Custos', 21, 1, NOW(), NOW()),
('3.2', 'Mão de Obra', 'custo', 'Custos', 22, 1, NOW(), NOW()),
('4', 'DESPESAS OPERACIONAIS', 'despesa', 'Despesas', 30, 1, NOW(), NOW()),
('4.1', 'Aluguel', 'despesa', 'Despesas', 31, 1, NOW(), NOW()),
('4.2', 'Energia', 'despesa', 'Despesas', 32, 1, NOW(), NOW()),
('4.3', 'Internet/Telefone', 'despesa', 'Despesas', 33, 1, NOW(), NOW()),
('4.4', 'Salários', 'despesa', 'Despesas', 34, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- =============================================
-- MENSAGEM DE CONCLUSÃO
-- =============================================
SELECT 'Tabelas do MAPOS V5 criadas/atualizadas com sucesso!' AS mensagem;
