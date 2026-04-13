-- =====================================================
-- MAPOS V5 - INSTALAÇÃO COMPLETA COM DRE, IMPOSTOS E CERTIFICADO DIGITAL
-- Data: 12/04/2025
-- =====================================================

-- =====================================================
-- TABELAS DO SISTEMA DRE (DEMONSTRAÇÃO DO RESULTADO)
-- =====================================================

-- Tabela: dre_contas (Plano de Contas para DRE)
CREATE TABLE IF NOT EXISTS `dre_contas` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL,
    `nome` VARCHAR(255) NOT NULL,
    `tipo` ENUM('RECEITA', 'CUSTO', 'DESPESA', 'IMPOSTO', 'TRANSFERENCIA') NOT NULL,
    `grupo` ENUM('RECEITA_BRUTA', 'DEDUCOES', 'RECEITA_LIQUIDA', 'CUSTO', 'LUCRO_BRUTO', 'DESPESA_OPERACIONAL', 'LUCRO_OPERACIONAL', 'OUTRAS_RECEITAS', 'OUTRAS_DESPESAS', 'RESULTADO_FINANCEIRO', 'LUCRO_ANTES_IR', 'IMPOSTO_RENDA', 'LUCRO_LIQUIDO') NOT NULL,
    `ordem` INT(11) DEFAULT 0,
    `conta_pai_id` INT(11) UNSIGNED NULL,
    `nivel` INT(2) DEFAULT 1,
    `sinal` ENUM('POSITIVO', 'NEGATIVO') DEFAULT 'POSITIVO',
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    INDEX `idx_codigo` (`codigo`),
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_grupo` (`grupo`),
    INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: dre_lancamentos (Lançamentos Contábeis)
CREATE TABLE IF NOT EXISTS `dre_lancamentos` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `conta_id` INT(11) UNSIGNED NOT NULL,
    `data` DATE NOT NULL,
    `valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tipo_movimento` ENUM('DEBITO', 'CREDITO') NOT NULL,
    `descricao` TEXT NULL,
    `documento` VARCHAR(50) NULL,
    `os_id` INT(11) UNSIGNED NULL,
    `venda_id` INT(11) UNSIGNED NULL,
    `lancamento_id` INT(11) UNSIGNED NULL,
    `usuarios_id` INT(11) UNSIGNED NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    INDEX `idx_conta_id` (`conta_id`),
    INDEX `idx_data` (`data`),
    INDEX `idx_os_id` (`os_id`),
    INDEX `idx_venda_id` (`venda_id`),
    INDEX `idx_lancamento_id` (`lancamento_id`),
    CONSTRAINT `fk_lancamentos_conta` FOREIGN KEY (`conta_id`) REFERENCES `dre_contas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_lancamentos_usuario` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios`(`idUsuarios`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: dre_config (Configurações e mapeamentos)
CREATE TABLE IF NOT EXISTS `dre_config` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tipo` ENUM('MAPEAMENTO_OS', 'MAPEAMENTO_VENDA', 'MAPEAMENTO_LANCAMENTO', 'CONFIG') NOT NULL,
    `origem_tabela` VARCHAR(50) NULL,
    `origem_campo` VARCHAR(50) NULL,
    `conta_dre_id` INT(11) UNSIGNED NOT NULL,
    `condicao` TEXT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    CONSTRAINT `fk_config_conta` FOREIGN KEY (`conta_dre_id`) REFERENCES `dre_contas`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELAS DO SISTEMA DE IMPOSTOS (SIMPLES NACIONAL)
-- =====================================================

-- Tabela: impostos_config - Configurações de alíquotas
CREATE TABLE IF NOT EXISTS `impostos_config` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `anexo` ENUM('I', 'II', 'III', 'IV', 'V') NOT NULL COMMENT 'Anexo do Simples Nacional',
    `faixa` INT(2) NOT NULL COMMENT 'Faixa de faturamento (1-5)',
    `aliquota_nominal` DECIMAL(5,2) NOT NULL COMMENT 'Alíquota nominal total (%)',
    `irpj` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '% IRPJ dentro da alíquota',
    `csll` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '% CSLL dentro da alíquota',
    `cofins` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '% COFINS dentro da alíquota',
    `pis` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '% PIS dentro da alíquota',
    `cpp` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '% Contribuição Previdenciária',
    `iss` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '% ISS dentro da alíquota',
    `outros` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `atividade_principal` VARCHAR(255) NULL COMMENT 'Descrição das atividades',
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    UNIQUE KEY `unique_anexo_faixa` (`anexo`, `faixa`),
    INDEX `idx_anexo` (`anexo`),
    INDEX `idx_faixa` (`faixa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: impostos_retidos - Registro de impostos retidos em boletos
CREATE TABLE IF NOT EXISTS `impostos_retidos` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cobranca_id` INT(11) UNSIGNED NULL COMMENT 'ID da cobrança/boleto',
    `os_id` INT(11) UNSIGNED NULL COMMENT 'ID da OS relacionada',
    `venda_id` INT(11) UNSIGNED NULL COMMENT 'ID da venda relacionada',
    `cliente_id` INT(11) UNSIGNED NOT NULL,
    `valor_bruto` DECIMAL(15,2) NOT NULL COMMENT 'Valor bruto do serviço',
    `valor_liquido` DECIMAL(15,2) NOT NULL COMMENT 'Valor após retenção',
    `aliquota_aplicada` DECIMAL(5,2) NOT NULL COMMENT 'Alíquota % aplicada',
    `irpj_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `csll_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `cofins_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `pis_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `iss_valor` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `data_competencia` DATE NOT NULL COMMENT 'Mês/ano de competência',
    `data_retencao` DATETIME NOT NULL COMMENT 'Data da retenção',
    `nota_fiscal` VARCHAR(50) NULL COMMENT 'Número da NFSe',
    `status` ENUM('Retido', 'Recolhido', 'Estornado') DEFAULT 'Retido',
    `observacao` TEXT NULL,
    `usuarios_id` INT(11) UNSIGNED NOT NULL,
    `dre_lancamento_id` INT(11) UNSIGNED NULL COMMENT 'Vínculo com lançamento DRE',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    INDEX `idx_cobranca_id` (`cobranca_id`),
    INDEX `idx_os_id` (`os_id`),
    INDEX `idx_venda_id` (`venda_id`),
    INDEX `idx_cliente_id` (`cliente_id`),
    INDEX `idx_data_competencia` (`data_competencia`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: config_sistema_impostos - Configurações gerais
CREATE TABLE IF NOT EXISTS `config_sistema_impostos` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `chave` VARCHAR(100) NOT NULL,
    `valor` TEXT NULL,
    `descricao` VARCHAR(255) NULL,
    `updated_at` DATETIME NULL,
    UNIQUE KEY `unique_chave` (`chave`),
    INDEX `idx_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELAS DO SISTEMA DE CERTIFICADO DIGITAL
-- =====================================================

-- Tabela: certificado_digital
CREATE TABLE IF NOT EXISTS `certificado_digital` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tipo` ENUM('A1', 'A3') DEFAULT 'A1' COMMENT 'Tipo do certificado',
    `cnpj` VARCHAR(14) NOT NULL COMMENT 'CNPJ do titular',
    `razao_social` VARCHAR(255) NULL,
    `nome_fantasia` VARCHAR(255) NULL,
    `arquivo_caminho` VARCHAR(500) NULL COMMENT 'Caminho do arquivo PFX (A1)',
    `arquivo_hash` VARCHAR(255) NULL COMMENT 'Hash para verificação de integridade',
    `senha` TEXT NULL COMMENT 'Senha criptografada do certificado',
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
    INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: certificado_consultas - Log de consultas à Receita
CREATE TABLE IF NOT EXISTS `certificado_consultas` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `certificado_id` INT(11) UNSIGNED NOT NULL,
    `tipo_consulta` ENUM('CNPJ', 'SIMPLES_NACIONAL', 'NFE', 'NFSE', 'SITUACAO_CADASTRO') NOT NULL,
    `data_consulta` DATETIME NOT NULL,
    `sucesso` TINYINT(1) DEFAULT 0,
    `dados_retorno` LONGTEXT NULL COMMENT 'JSON com dados retornados',
    `erro` TEXT NULL,
    INDEX `idx_certificado_id` (`certificado_id`),
    INDEX `idx_tipo_consulta` (`tipo_consulta`),
    INDEX `idx_data_consulta` (`data_consulta`),
    CONSTRAINT `fk_consulta_certificado` FOREIGN KEY (`certificado_id`) REFERENCES `certificado_digital`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: certificado_nfe_importada - Notas fiscais importadas
CREATE TABLE IF NOT EXISTS `certificado_nfe_importada` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `certificado_id` INT(11) UNSIGNED NOT NULL,
    `chave_acesso` VARCHAR(44) NOT NULL,
    `numero` VARCHAR(20) NULL,
    `serie` VARCHAR(10) NULL,
    `data_emissao` DATETIME NULL,
    `data_importacao` DATETIME NULL,
    `cnpj_destinatario` VARCHAR(14) NULL,
    `valor_total` DECIMAL(15,2) NULL,
    `valor_impostos` DECIMAL(15,2) NULL,
    `xml_path` VARCHAR(500) NULL,
    `situacao` ENUM('Autorizada', 'Cancelada', 'Denegada', 'Inutilizada') DEFAULT 'Autorizada',
    `imposto_integrado` TINYINT(1) DEFAULT 0 COMMENT 'Se já foi lançado no sistema de impostos',
    `dados_xml` LONGTEXT NULL,
    UNIQUE KEY `unique_chave_acesso` (`chave_acesso`),
    INDEX `idx_certificado_id` (`certificado_id`),
    INDEX `idx_chave_acesso` (`chave_acesso`),
    CONSTRAINT `fk_nfe_certificado` FOREIGN KEY (`certificado_id`) REFERENCES `certificado_digital`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DADOS INICIAIS
-- =====================================================

-- Inserir estrutura padrão do DRE
INSERT INTO `dre_contas` (`codigo`, `nome`, `tipo`, `grupo`, `ordem`, `conta_pai_id`, `nivel`, `sinal`, `ativo`, `created_at`, `updated_at`) VALUES
-- RECEITA BRUTA
('1', 'RECEITA BRUTA', 'RECEITA', 'RECEITA_BRUTA', 10, NULL, 1, 'POSITIVO', 1, NOW(), NOW()),
('1.1', 'Receita de Serviços', 'RECEITA', 'RECEITA_BRUTA', 11, NULL, 2, 'POSITIVO', 1, NOW(), NOW()),
('1.2', 'Receita de Vendas', 'RECEITA', 'RECEITA_BRUTA', 12, NULL, 2, 'POSITIVO', 1, NOW(), NOW()),
('1.3', 'Outras Receitas Operacionais', 'RECEITA', 'RECEITA_BRUTA', 13, NULL, 2, 'POSITIVO', 1, NOW(), NOW()),

-- DEDUÇÕES
('2', '(-) DEDUÇÕES DA RECEITA', 'IMPOSTO', 'DEDUCOES', 20, NULL, 1, 'NEGATIVO', 1, NOW(), NOW()),
('2.1', 'Impostos Sobre Vendas', 'IMPOSTO', 'DEDUCOES', 21, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('2.2', 'Devoluções e Abatimentos', 'RECEITA', 'DEDUCOES', 22, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('2.3', 'Descontos Concedidos', 'RECEITA', 'DEDUCOES', 23, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),

-- RECEITA LÍQUIDA
('3', '= RECEITA LÍQUIDA', 'TRANSFERENCIA', 'RECEITA_LIQUIDA', 30, NULL, 1, 'POSITIVO', 1, NOW(), NOW()),

-- CUSTOS
('4', '(-) CUSTO DOS SERVIÇOS/PRODUTOS', 'CUSTO', 'CUSTO', 40, NULL, 1, 'NEGATIVO', 1, NOW(), NOW()),
('4.1', 'Materiais Utilizados', 'CUSTO', 'CUSTO', 41, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('4.2', 'Mão de Obra Direta', 'CUSTO', 'CUSTO', 42, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('4.3', 'Custos Operacionais Diretos', 'CUSTO', 'CUSTO', 43, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),

-- LUCRO BRUTO
('5', '= LUCRO BRUTO', 'TRANSFERENCIA', 'LUCRO_BRUTO', 50, NULL, 1, 'POSITIVO', 1, NOW(), NOW()),

-- DESPESAS OPERACIONAIS
('6', '(-) DESPESAS OPERACIONAIS', 'DESPESA', 'DESPESA_OPERACIONAL', 60, NULL, 1, 'NEGATIVO', 1, NOW(), NOW()),
('6.1', 'Despesas Administrativas', 'DESPESA', 'DESPESA_OPERACIONAL', 61, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('6.1.1', 'Salários e Encargos Administrativos', 'DESPESA', 'DESPESA_OPERACIONAL', 611, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.1.2', 'Aluguel', 'DESPESA', 'DESPESA_OPERACIONAL', 612, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.1.3', 'Contas de Consumo', 'DESPESA', 'DESPESA_OPERACIONAL', 613, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.1.4', 'Material de Escritório', 'DESPESA', 'DESPESA_OPERACIONAL', 614, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.1.5', 'Honorários Profissionais', 'DESPESA', 'DESPESA_OPERACIONAL', 615, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.1.6', 'Outras Despesas Administrativas', 'DESPESA', 'DESPESA_OPERACIONAL', 619, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.2', 'Despesas com Vendas', 'DESPESA', 'DESPESA_OPERACIONAL', 62, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('6.2.1', 'Comissões', 'DESPESA', 'DESPESA_OPERACIONAL', 621, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.2.2', 'Propaganda e Publicidade', 'DESPESA', 'DESPESA_OPERACIONAL', 622, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('6.2.3', 'Despesas de Entrega', 'DESPESA', 'DESPESA_OPERACIONAL', 623, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),

-- LUCRO OPERACIONAL
('7', '= LUCRO/PREJUÍZO OPERACIONAL', 'TRANSFERENCIA', 'LUCRO_OPERACIONAL', 70, NULL, 1, 'POSITIVO', 1, NOW(), NOW()),

-- RESULTADO FINANCEIRO
('8', 'RESULTADO FINANCEIRO', 'TRANSFERENCIA', 'RESULTADO_FINANCEIRO', 80, NULL, 1, 'POSITIVO', 1, NOW(), NOW()),
('8.1', 'Receitas Financeiras', 'RECEITA', 'OUTRAS_RECEITAS', 81, NULL, 2, 'POSITIVO', 1, NOW(), NOW()),
('8.1.1', 'Juros Recebidos', 'RECEITA', 'OUTRAS_RECEITAS', 811, NULL, 3, 'POSITIVO', 1, NOW(), NOW()),
('8.1.2', 'Descontos Obtidos', 'RECEITA', 'OUTRAS_RECEITAS', 812, NULL, 3, 'POSITIVO', 1, NOW(), NOW()),
('8.2', 'Despesas Financeiras', 'DESPESA', 'OUTRAS_DESPESAS', 82, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('8.2.1', 'Juros Pagos', 'DESPESA', 'OUTRAS_DESPESAS', 821, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('8.2.2', 'Descontos Concedidos', 'DESPESA', 'OUTRAS_DESPESAS', 822, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),
('8.2.3', 'Tarifas Bancárias', 'DESPESA', 'OUTRAS_DESPESAS', 823, NULL, 3, 'NEGATIVO', 1, NOW(), NOW()),

-- LUCRO ANTES DO IR
('9', '= LUCRO/PREJUÍZO ANTES DO IR', 'TRANSFERENCIA', 'LUCRO_ANTES_IR', 90, NULL, 1, 'POSITIVO', 1, NOW(), NOW()),

-- IMPOSTO DE RENDA
('10', '(-) IMPOSTO DE RENDA E CONTRIBUIÇÕES', 'IMPOSTO', 'IMPOSTO_RENDA', 100, NULL, 1, 'NEGATIVO', 1, NOW(), NOW()),
('10.1', 'IRPJ', 'IMPOSTO', 'IMPOSTO_RENDA', 101, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('10.2', 'CSLL', 'IMPOSTO', 'IMPOSTO_RENDA', 102, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),
('10.3', 'PIS/COFINS', 'IMPOSTO', 'IMPOSTO_RENDA', 103, NULL, 2, 'NEGATIVO', 1, NOW(), NOW()),

-- LUCRO LÍQUIDO
('11', '= LUCRO/PREJUÍZO LÍQUIDO DO EXERCÍCIO', 'TRANSFERENCIA', 'LUCRO_LIQUIDO', 110, NULL, 1, 'POSITIVO', 1, NOW(), NOW());

-- Inserir alíquotas do Simples Nacional 2024 - Anexo III (Serviços)
INSERT INTO `impostos_config` (`anexo`, `faixa`, `aliquota_nominal`, `irpj`, `csll`, `cofins`, `pis`, `cpp`, `iss`, `outros`, `atividade_principal`, `ativo`, `created_at`) VALUES
('III', 1, 6.00, 4.00, 3.50, 12.82, 2.78, 43.40, 33.50, 0, 'Prestação de serviços em geral (Anexo III)', 1, NOW()),
('III', 2, 11.20, 4.00, 3.50, 14.05, 3.05, 38.99, 32.41, 0, 'Prestação de serviços em geral (Anexo III)', 1, NOW()),
('III', 3, 13.50, 4.00, 3.50, 13.64, 2.96, 37.62, 32.28, 0, 'Prestação de serviços em geral (Anexo III)', 1, NOW()),
('III', 4, 16.00, 4.00, 3.50, 13.26, 2.87, 35.13, 31.24, 0, 'Prestação de serviços em geral (Anexo III)', 1, NOW()),
('III', 5, 21.00, 4.00, 3.50, 12.82, 2.78, 34.23, 30.67, 0, 'Prestação de serviços em geral (Anexo III)', 1, NOW());

-- Inserir alíquotas do Simples Nacional 2024 - Anexo IV (Construção)
INSERT INTO `impostos_config` (`anexo`, `faixa`, `aliquota_nominal`, `irpj`, `csll`, `cofins`, `pis`, `cpp`, `iss`, `outros`, `atividade_principal`, `ativo`, `created_at`) VALUES
('IV', 1, 4.50, 0.00, 15.74, 14.68, 3.19, 41.50, 31.00, 0, 'Construção e serviços com ISS próprio (Anexo IV)', 1, NOW()),
('IV', 2, 9.00, 0.00, 15.74, 14.68, 3.19, 41.50, 24.89, 0, 'Construção e serviços com ISS próprio (Anexo IV)', 1, NOW()),
('IV', 3, 13.50, 0.00, 15.74, 14.68, 3.19, 42.09, 20.80, 0, 'Construção e serviços com ISS próprio (Anexo IV)', 1, NOW()),
('IV', 4, 17.00, 1.00, 14.74, 13.73, 2.98, 39.40, 24.15, 0, 'Construção e serviços com ISS próprio (Anexo IV)', 1, NOW()),
('IV', 5, 21.00, 1.00, 14.74, 13.73, 2.98, 38.48, 23.07, 0, 'Construção e serviços com ISS próprio (Anexo IV)', 1, NOW());

-- Inserir configurações padrão do sistema de impostos
INSERT INTO `config_sistema_impostos` (`chave`, `valor`, `descricao`, `updated_at`) VALUES
('IMPOSTO_ANEXO_PADRAO', 'III', 'Anexo do Simples Nacional padrão para a empresa', NOW()),
('IMPOSTO_FAIXA_ATUAL', '1', 'Faixa de faturamento atual (1-5)', NOW()),
('IMPOSTO_RETENCAO_AUTOMATICA', '1', 'Habilitar retenção automática em novos boletos (1=Sim, 0=Não)', NOW()),
('IMPOSTO_DRE_INTEGRACAO', '1', 'Integrar retenções automaticamente com DRE (1=Sim, 0=Não)', NOW()),
('IMPOSTO_ISS_MUNICIPAL', '5.00', 'Alíquota de ISS municipal para cálculo isolado (%)', NOW());

-- =====================================================
-- PERMISSÕES DO SISTEMA
-- =====================================================

-- Inserir permissões do DRE
INSERT INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`) VALUES
('Visualizar DRE', NOW(), 'a:1:{s:4:"vDRE";i:1;}', 1),
('Visualizar Relatório DRE', NOW(), 'a:1:{s:13:"vDRERelatorio";i:1;}', 1),
('Cadastrar Conta DRE', NOW(), 'a:1:{s:10:"cDREConta";i:1;}', 1),
('Deletar Conta DRE', NOW(), 'a:1:{s:10:"dDREConta";i:1;}', 1),
('Visualizar Lançamentos DRE', NOW(), 'a:1:{s:15:"vDRELancamento";i:1;}', 1),
('Cadastrar Lançamento DRE', NOW(), 'a:1:{s:15:"cDRELancamento";i:1;}', 1),
('Deletar Lançamento DRE', NOW(), 'a:1:{s:15:"dDRELancamento";i:1;}', 1),
('Integrar Dados DRE', NOW(), 'a:1:{s:14:"cDREIntegracao";i:1;}', 1),
('Exportar DRE', NOW(), 'a:1:{s:12:"vDREExportar";i:1;}', 1),
('Análise DRE', NOW(), 'a:1:{s:10:"vDREAnalise";i:1;}', 1)
ON DUPLICATE KEY UPDATE `situacao` = 1;

-- Inserir permissões de Impostos
INSERT INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`) VALUES
('Visualizar Impostos', NOW(), 'a:1:{s:10:"vImpostos";i:1;}', 1),
('Visualizar Relatório Impostos', NOW(), 'a:1:{s:19:"vImpostosRelatorio";i:1;}', 1),
('Configurar Impostos', NOW(), 'a:1:{s:15:"cImpostosConfig";i:1;}', 1),
('Editar Impostos', NOW(), 'a:1:{s:9:"eImpostos";i:1;}', 1),
('Exportar Impostos', NOW(), 'a:1:{s:16:"vImpostosExportar";i:1;}', 1)
ON DUPLICATE KEY UPDATE `situacao` = 1;

-- Inserir permissões de Certificado
INSERT INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`) VALUES
('Visualizar Certificado', NOW(), 'a:1:{s:13:"vCertificado";i:1;}', 1),
('Configurar Certificado', NOW(), 'a:1:{s:13:"cCertificado";i:1;}', 1),
('Editar Certificado', NOW(), 'a:1:{s:12:"eCertificado";i:1;}', 1),
('Remover Certificado', NOW(), 'a:1:{s:12:"dCertificado";i:1;}', 1)
ON DUPLICATE KEY UPDATE `situacao` = 1;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================
