-- ============================================================================
-- MAPOS V5 - INSTALAÇÃO COMPLETA
-- Banco de Dados Inicial para Primeira Instalação
-- Versão: 4.52.0
-- Data: 2026-04-12
-- ============================================================================
--
-- INSTRUÇÕES:
-- 1. Crie um banco de dados: CREATE DATABASE mapos CHARACTER SET utf8mb4;
-- 2. Importe este arquivo: mysql -u usuario -p mapos < instalacao_completa_mapos5.sql
-- 3. Configure o arquivo application/config/database.php
--
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================================
-- 1. TABELAS BASE DO MAPOS
-- ============================================================================

-- Sessões CodeIgniter
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `idClientes` INT(11) NOT NULL AUTO_INCREMENT,
  `asaas_id` VARCHAR(255) DEFAULT NULL,
  `nomeCliente` VARCHAR(255) NOT NULL,
  `sexo` VARCHAR(20) NULL,
  `pessoa_fisica` BOOLEAN NOT NULL DEFAULT 1,
  `documento` VARCHAR(20) NOT NULL,
  `telefone` VARCHAR(20) NOT NULL,
  `celular` VARCHAR(20) NULL DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(200) NOT NULL,
  `dataCadastro` DATE NULL DEFAULT NULL,
  `rua` VARCHAR(70) NULL DEFAULT NULL,
  `numero` VARCHAR(15) NULL DEFAULT NULL,
  `bairro` VARCHAR(45) NULL DEFAULT NULL,
  `cidade` VARCHAR(45) NULL DEFAULT NULL,
  `estado` VARCHAR(20) NULL DEFAULT NULL,
  `cep` VARCHAR(20) NULL DEFAULT NULL,
  `contato` varchar(45) DEFAULT NULL,
  `complemento` varchar(45) DEFAULT NULL,
  `fornecedor` BOOLEAN NOT NULL DEFAULT 0,
  PRIMARY KEY (`idClientes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

-- Resets de senha
CREATE TABLE IF NOT EXISTS `resets_de_senha` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(200) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `data_expiracao` DATETIME NOT NULL,
  `token_utilizado` TINYINT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `idCategorias` INT NOT NULL AUTO_INCREMENT,
  `categoria` VARCHAR(80) NULL,
  `cadastro` DATE NULL,
  `status` TINYINT(1) NULL,
  `tipo` VARCHAR(15) NULL,
  PRIMARY KEY (`idCategorias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contas bancárias
CREATE TABLE IF NOT EXISTS `contas` (
  `idContas` INT NOT NULL AUTO_INCREMENT,
  `conta` VARCHAR(45) NULL,
  `banco` VARCHAR(45) NULL,
  `numero` VARCHAR(45) NULL,
  `saldo` DECIMAL(10,2) NULL,
  `cadastro` DATE NULL,
  `status` TINYINT(1) NULL,
  `tipo` VARCHAR(80) NULL,
  PRIMARY KEY (`idContas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissões de usuários
CREATE TABLE IF NOT EXISTS `permissoes` (
  `idPermissao` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `permissoes` TEXT NULL,
  `situacao` TINYINT(1) NULL,
  `data` DATE NULL,
  PRIMARY KEY (`idPermissao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuários
CREATE TABLE IF NOT EXISTS `usuarios` (
  `idUsuarios` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `rg` VARCHAR(20) NULL,
  `cpf` VARCHAR(20) NULL,
  `rua` VARCHAR(255) NULL,
  `numero` VARCHAR(15) NULL,
  `bairro` VARCHAR(255) NULL,
  `cidade` VARCHAR(255) NULL,
  `estado` VARCHAR(20) NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(200) NOT NULL,
  `telefone` VARCHAR(20) NULL,
  `celular` VARCHAR(20) NULL,
  `situacao` TINYINT(1) NULL,
  `dataCadastro` DATE NULL,
  `dataExpiracao` DATE NULL,
  `cep` varchar(20) DEFAULT NULL,
  `url_image_user` VARCHAR(100) NULL,
  `permissoes_id` INT NULL,
  PRIMARY KEY (`idUsuarios`),
  FOREIGN KEY (`permissoes_id`) REFERENCES `permissoes`(`idPermissao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Produtos
CREATE TABLE IF NOT EXISTS `produtos` (
  `idProdutos` INT NOT NULL AUTO_INCREMENT,
  `codDeBarra` VARCHAR(20) NULL,
  `nome` VARCHAR(255) NULL,
  `descricao` TEXT NULL,
  `unidade` VARCHAR(10) NULL,
  `precoCompra` DECIMAL(10,2) NULL,
  `precoVenda` DECIMAL(10,2) NULL,
  `estoque` INT NULL,
  `estoqueMinimo` INT NULL,
  `saida` INT(11) DEFAULT 0,
  `entrada` INT(11) DEFAULT 0,
  `cadastro` DATE NULL,
  `categorias_id` INT NULL,
  PRIMARY KEY (`idProdutos`),
  FOREIGN KEY (`categorias_id`) REFERENCES `categorias`(`idCategorias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Serviços
CREATE TABLE IF NOT EXISTS `servicos` (
  `idServicos` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NULL,
  `preco` DECIMAL(10,2) NULL,
  `descricao` TEXT NULL,
  PRIMARY KEY (`idServicos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ordem de Serviço (OS)
CREATE TABLE IF NOT EXISTS `os` (
  `idOs` INT NOT NULL AUTO_INCREMENT,
  `dataInicial` DATE NULL,
  `dataFinal` DATE NULL,
  `garantia` VARCHAR(45) NULL,
  `descricaoProduto` TEXT NULL,
  `defeito` TEXT NULL,
  `status` VARCHAR(45) NULL,
  `observacoes` TEXT NULL,
  `laudoTecnico` TEXT NULL,
  `valorTotal` DECIMAL(10,2) NULL,
  `clientes_id` INT(11) NOT NULL,
  `usuarios_id` INT(11) NOT NULL,
  `observacoes_cliente` TEXT NULL,
  `anotacoes_tecnico` TEXT NULL,
  `aprovado` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`idOs`),
  FOREIGN KEY (`clientes_id`) REFERENCES `clientes`(`idClientes`),
  FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios`(`idUsuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Produtos da OS
CREATE TABLE IF NOT EXISTS `produtos_os` (
  `idProduto_os` INT NOT NULL AUTO_INCREMENT,
  `quantidade` INT NULL,
  `preco` DECIMAL(10,2) NULL,
  `os_id` INT NOT NULL,
  `produtos_id` INT NOT NULL,
  PRIMARY KEY (`idProduto_os`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`),
  FOREIGN KEY (`produtos_id`) REFERENCES `produtos`(`idProdutos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Serviços da OS
CREATE TABLE IF NOT EXISTS `servicos_os` (
  `idServico_os` INT NOT NULL AUTO_INCREMENT,
  `quantidade` INT NULL,
  `preco` DECIMAL(10,2) NULL,
  `os_id` INT NOT NULL,
  `servicos_id` INT NOT NULL,
  PRIMARY KEY (`idServico_os`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`),
  FOREIGN KEY (`servicos_id`) REFERENCES `servicos`(`idServicos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vendas
CREATE TABLE IF NOT EXISTS `vendas` (
  `idVendas` INT NOT NULL AUTO_INCREMENT,
  `dataVenda` DATE NULL,
  `observacoes` TEXT NULL,
  `clientes_id` INT NOT NULL,
  `usuarios_id` INT NOT NULL,
  `valorTotal` DECIMAL(10,2) NULL,
  `observacoes_cliente` TEXT NULL,
  `garantia` INT DEFAULT NULL,
  PRIMARY KEY (`idVendas`),
  FOREIGN KEY (`clientes_id`) REFERENCES `clientes`(`idClientes`),
  FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios`(`idUsuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Produtos da Venda
CREATE TABLE IF NOT EXISTS `itens_de_vendas` (
  `idItens` INT NOT NULL AUTO_INCREMENT,
  `quantidade` INT NULL,
  `preco` DECIMAL(10,2) NULL,
  `vendas_id` INT NOT NULL,
  `produtos_id` INT NOT NULL,
  PRIMARY KEY (`idItens`),
  FOREIGN KEY (`vendas_id`) REFERENCES `vendas`(`idVendas`),
  FOREIGN KEY (`produtos_id`) REFERENCES `produtos`(`idProdutos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Lançamentos financeiros
CREATE TABLE IF NOT EXISTS `lancamentos` (
  `idLancamentos` INT NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(255) NULL,
  `valor` DECIMAL(10,2) NULL,
  `data_vencimento` DATE NULL,
  `data_pagamento` DATE NULL,
  `baixado` TINYINT(1) NULL,
  `cliente_fornecedor` VARCHAR(255) NULL,
  `forma_pgto` VARCHAR(255) NULL,
  `tipo` VARCHAR(45) NULL,
  `categoria` VARCHAR(80) NULL,
  `observacoes` TEXT NULL,
  `usuarios_id` INT(11) DEFAULT NULL,
  `observacoes_cliente` TEXT NULL,
  PRIMARY KEY (`idLancamentos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Emitente (dados da empresa)
CREATE TABLE IF NOT EXISTS `emitente` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NULL,
  `cnpj` VARCHAR(20) NULL,
  `ie` VARCHAR(20) NULL,
  `logradouro` VARCHAR(255) NULL,
  `numero` VARCHAR(15) NULL,
  `bairro` VARCHAR(255) NULL,
  `cidade` VARCHAR(255) NULL,
  `uf` VARCHAR(2) NULL,
  `telefone` VARCHAR(20) NULL,
  `email` VARCHAR(255) NULL,
  `logo` TEXT NULL,
  `cep` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Configurações do sistema
CREATE TABLE IF NOT EXISTS `configuracoes` (
  `idConfig` INT NOT NULL AUTO_INCREMENT,
  `config` VARCHAR(100) NULL,
  `valor` TEXT NULL,
  PRIMARY KEY (`idConfig`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Auditoria
CREATE TABLE IF NOT EXISTS `auditoria` (
  `idAuditoria` INT NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(80) NULL,
  `data` DATETIME NULL,
  `acao` TEXT NULL,
  `observacao` TEXT NULL,
  PRIMARY KEY (`idAuditoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 2. TABELAS V5 - NOVOS RECURSOS
-- ============================================================================

-- Fila de Emails
CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `to_email` VARCHAR(255) NOT NULL,
  `to_name` VARCHAR(255) NULL,
  `subject` VARCHAR(500) NOT NULL,
  `body_html` LONGTEXT NULL,
  `body_text` LONGTEXT NULL,
  `template` VARCHAR(100) NULL,
  `template_data` TEXT NULL,
  `attachments` TEXT NULL,
  `priority` TINYINT(1) DEFAULT 3,
  `status` ENUM('pending', 'processing', 'sent', 'failed', 'cancelled', 'scheduled') DEFAULT 'pending',
  `attempts` TINYINT(1) DEFAULT 0,
  `max_retries` TINYINT(1) DEFAULT 3,
  `tracking_id` VARCHAR(32) NULL,
  `message_id` VARCHAR(255) NULL,
  `scheduled_at` DATETIME NULL,
  `sent_at` DATETIME NULL,
  `opened_at` DATETIME NULL,
  `clicked_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `last_attempt` DATETIME NULL,
  `failed_at` DATETIME NULL,
  `error_message` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  INDEX `idx_status` (`status`),
  INDEX `idx_priority` (`priority`),
  INDEX `idx_scheduled_at` (`scheduled_at`),
  INDEX `idx_tracking_id` (`tracking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rastreamento de Emails
CREATE TABLE IF NOT EXISTS `email_tracking` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email_queue_id` INT(11) UNSIGNED NOT NULL,
  `tracking_id` VARCHAR(64) NOT NULL UNIQUE,
  `opened` TINYINT(1) DEFAULT 0,
  `opened_at` DATETIME NULL,
  `clicked` TINYINT(1) DEFAULT 0,
  `clicked_at` DATETIME NULL,
  `clicked_url` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_tracking_id` (`tracking_id`),
  INDEX `idx_email_queue_id` (`email_queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cliques em Emails
CREATE TABLE IF NOT EXISTS `email_clicks` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tracking_id` VARCHAR(32) NOT NULL,
  `url` TEXT NOT NULL,
  `clicked_at` DATETIME NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  INDEX `idx_tracking_id` (`tracking_id`),
  INDEX `idx_clicked_at` (`clicked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Eventos Agendados
CREATE TABLE IF NOT EXISTS `scheduled_events` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_type` VARCHAR(100) NOT NULL,
  `event_data` JSON NULL,
  `execute_at` DATETIME NOT NULL,
  `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
  `executed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_status` (`status`),
  INDEX `idx_execute_at` (`execute_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Webhooks
CREATE TABLE IF NOT EXISTS `webhooks` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `secret` VARCHAR(255) NULL,
  `events` JSON NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Logs de Webhooks
CREATE TABLE IF NOT EXISTS `webhook_logs` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `webhook_id` INT(11) UNSIGNED NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `payload` TEXT NULL,
  `response` TEXT NULL,
  `status_code` INT NULL,
  `success` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_webhook_id` (`webhook_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuração de Certificado Digital
CREATE TABLE IF NOT EXISTS `certificado_config` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_emitente` INT(11) UNSIGNED NOT NULL,
  `certificado_p12` LONGTEXT NULL,
  `senha_certificado` VARCHAR(255) NULL,
  `cnpj_certificado` VARCHAR(14) NULL,
  `valido_de` DATETIME NULL,
  `valido_ate` DATETIME NULL,
  `arquivo_crt` LONGTEXT NULL,
  `arquivo_key` LONGTEXT NULL,
  `ambiente` ENUM('homologacao', 'producao') DEFAULT 'homologacao',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `idx_id_emitente` (`id_emitente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NFS-e Importadas
CREATE TABLE IF NOT EXISTS `nfse_importada` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_os` INT(11) UNSIGNED NULL,
  `numero_nfse` VARCHAR(50) NOT NULL,
  `codigo_verificacao` VARCHAR(50) NULL,
  `data_emissao` DATETIME NULL,
  `valor_servico` DECIMAL(10,2) NULL,
  `valor_liquido` DECIMAL(10,2) NULL,
  `prestador_cnpj` VARCHAR(14) NULL,
  `prestador_nome` VARCHAR(255) NULL,
  `tomador_cnpj` VARCHAR(14) NULL,
  `tomador_nome` VARCHAR(255) NULL,
  `status` ENUM('ativa', 'cancelada') DEFAULT 'ativa',
  `xml_content` LONGTEXT NULL,
  `pdf_content` LONGTEXT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_id_os` (`id_os`),
  INDEX `idx_numero_nfse` (`numero_nfse`),
  INDEX `idx_prestador_cnpj` (`prestador_cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DRE - Contas
CREATE TABLE IF NOT EXISTS `dre_contas` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(50) NOT NULL,
  `nome` VARCHAR(255) NOT NULL,
  `tipo` ENUM('receita', 'custo', 'despesa') NOT NULL,
  `categoria` VARCHAR(100) NULL,
  `pai_id` INT(11) UNSIGNED NULL,
  `ordem` INT DEFAULT 0,
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DRE - Lançamentos
CREATE TABLE IF NOT EXISTS `dre_lancamentos` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conta_id` INT(11) UNSIGNED NOT NULL,
  `data_referencia` DATE NOT NULL,
  `valor` DECIMAL(15,2) NOT NULL,
  `descricao` TEXT NULL,
  `id_os` INT(11) UNSIGNED NULL,
  `id_venda` INT(11) UNSIGNED NULL,
  `id_lancamento` INT(11) UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_conta_id` (`conta_id`),
  INDEX `idx_data_referencia` (`data_referencia`),
  INDEX `idx_id_os` (`id_os`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurações de Impostos
CREATE TABLE IF NOT EXISTS `impostos_config` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tipo_regime` ENUM('simples_nacional', 'lucro_presumido', 'lucro_real') DEFAULT 'simples_nacional',
  `anexo_simples` ENUM('i', 'ii', 'iii', 'iv', 'v') NULL,
  `aliquota_iss` DECIMAL(5,2) DEFAULT 0,
  `retem_iss` TINYINT(1) DEFAULT 0,
  `aliquota_pis` DECIMAL(5,2) DEFAULT 0,
  `aliquota_cofins` DECIMAL(5,2) DEFAULT 0,
  `aliquota_csll` DECIMAL(5,2) DEFAULT 0,
  `aliquota_ir` DECIMAL(5,2) DEFAULT 0,
  `aliquota_inss` DECIMAL(5,2) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Impostos Retidos
CREATE TABLE IF NOT EXISTS `impostos_retidos` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_os` INT(11) UNSIGNED NULL,
  `id_venda` INT(11) UNSIGNED NULL,
  `tipo_imposto` ENUM('iss', 'pis', 'cofins', 'csll', 'ir', 'inss') NOT NULL,
  `base_calculo` DECIMAL(15,2) NOT NULL,
  `aliquota` DECIMAL(5,2) NOT NULL,
  `valor_retido` DECIMAL(15,2) NOT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_id_os` (`id_os`),
  INDEX `idx_id_venda` (`id_venda`),
  INDEX `idx_tipo_imposto` (`tipo_imposto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notificações Push
CREATE TABLE IF NOT EXISTS `push_notifications` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `data` JSON NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Check-in de Atendimento
CREATE TABLE IF NOT EXISTS `checkin` (
  `idCheckin` INT NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `tecnico_id` INT(11) NOT NULL,
  `tipo` ENUM('inicio', 'pausa', 'retorno', 'finalizacao', 'checkin', 'checkout') NOT NULL,
  `data_hora` DATETIME NOT NULL,
  `observacao` TEXT NULL,
  `foto` VARCHAR(255) NULL,
  `latitude` DECIMAL(10, 8) NULL,
  `longitude` DECIMAL(11, 8) NULL,
  `localizacao` VARCHAR(255) NULL,
  PRIMARY KEY (`idCheckin`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`),
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fotos de Atendimento (BLOB)
CREATE TABLE IF NOT EXISTS `fotos_atendimento` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `checkin_id` INT(11) NOT NULL,
  `os_id` INT(11) NOT NULL,
  `imagem` LONGBLOB NOT NULL,
  `tipo` ENUM('antes', 'depois', 'assinatura', 'outro') DEFAULT 'outro',
  `data` DATETIME NOT NULL,
  INDEX `idx_checkin_id` (`checkin_id`),
  INDEX `idx_os_id` (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 3. DADOS INICIAIS
-- ============================================================================

-- Permissão padrão (Administrador)
INSERT INTO `permissoes` (`idPermissao`, `nome`, `permissoes`, `situacao`, `data`) VALUES
(1, 'Administrador', 'a:81:{s:8:"aCliente";s:1:"1";s:8:"eCliente";s:1:"1";s:8:"dCliente";s:1:"1";s:8:"vCliente";s:1:"1";s:8:"aProduto";s:1:"1";s:8:"eProduto";s:1:"1";s:8:"dProduto";s:1:"1";s:8:"vProduto";s:1:"1";s:8:"aServico";s:1:"1";s:8:"eServico";s:1:"1";s:8:"dServico";s:1:"1";s:8:"vServico";s:1:"1";s:3:"aOs";s:1:"1";s:3:"eOs";s:1:"1";s:3:"dOs";s:1:"1";s:3:"vOs";s:1:"1";s:6:"aVenda";s:1:"1";s:6:"eVenda";s:1:"1";s:6:"dVenda";s:1:"1";s:6:"vVenda";s:1:"1";s:9:"aLancamento";s:1:"1";s:9:"eLancamento";s:1:"1";s:9:"dLancamento";s:1:"1";s:9:"vLancamento";s:1:"1";s:8:"aArquivo";s:1:"1";s:8:"dArquivo";s:1:"1";s:8:"vArquivo";s:1:"1";s:11:"categoria_d";s:1:"1";s:11:"categoria_v";s:1:"1";s:11:"categoria_a";s:1:"1";s:11:"categoria_e";s:1:"1";s:9:"vCategoria";s:1:"1";s:7:"aCobranca";s:1:"1";s:7:"eCobranca";s:1:"1";s:7:"dCobranca";s:1:"1";s:7:"vCobranca";s:1:"1";s:7:"aGarantia";s:1:"1";s:7:"eGarantia";s:1:"1";s:7:"dGarantia";s:1:"1";s:7:"vGarantia";s:1:"1";s:10:"aConfiguracao";s:1:"1";s:10:"eConfiguracao";s:1:"1";s:10:"dConfiguracao";s:1:"1";s:10:"vConfiguracao";s:1:"1";s:8:"aEmitente";s:1:"1";s:8:"eEmitente";s:1:"1";s:8:"dEmitente";s:1:"1";s:8:"vEmitente";s:1:"1";s:9:"aPermissao";s:1:"1";s:9:"ePermissao";s:1:"1";s:9:"dPermissao";s:1:"1";s:9:"vPermissao";s:1:"1";s:6:"aAuditoria";s:1:"1";s:6:"eAuditoria";s:1:"1";s:6:"dAuditoria";s:1:"1";s:6:"vAuditoria";s:1:"1";s:6:"aEmail";s:1:"1";s:6:"eEmail";s:1:"1";s:6:"dEmail";s:1:"1";s:6:"vEmail";s:1:"1";s:7:"aCobranca";s:1:"1";s:7:"eCobranca";s:1:"1";s:7:"dCobranca";s:1:"1";s:7:"vCobranca";s:1:"1";s:9:"rContas";s:1:"1";s:9:"rFinanceiro";s:1:"1";s:9:"rProdutos";s:1:"1";s:9:"rServicos";s:1:"1";s:6:"rVendas";s:1:"1";s:3:"rOs";s:1:"1";s:8:"rClientes";s:1:"1";s:11:"vCertificado";s:1:"1";s:10:"vImpostos";s:1:"1";s:5:"vDRE";s:1:"1";s:10:"vWebhooks";s:1:"1";s:20:"vRelatorioAtendimentos";s:1:"1";}', 1, NOW()) ON DUPLICATE KEY UPDATE idPermissao=idPermissao;

-- Usuário Administrador (senha: admin)
-- Hash: $2y$10$91JHDZDk6F6uVQkZ9n/q5ugF9hLHZwzr2F9JFSvQZdGJn5E8qZtK
INSERT INTO `usuarios` (`idUsuarios`, `nome`, `email`, `senha`, `telefone`, `situacao`, `dataCadastro`, `permissoes_id`) VALUES
(1, 'Administrador', 'admin@mapos.com.br', '$2y$10$91JHDZDk6F6uVQkZ9n/q5ugF9hLHZwzr2F9JFSvQZdGJn5E8qZtK', '', 1, NOW(), 1) ON DUPLICATE KEY UPDATE idUsuarios=idUsuarios;

-- Emitente Padrão
INSERT INTO `emitente` (`id`, `nome`, `cnpj`, `ie`, `logradouro`, `numero`, `bairro`, `cidade`, `uf`, `telefone`, `email`) VALUES
(1, 'Sua Empresa', '00.000.000/0000-00', '', 'Rua Exemplo', '123', 'Bairro', 'Cidade', 'UF', '(00) 0000-0000', 'contato@empresa.com') ON DUPLICATE KEY UPDATE id=id;

-- Configurações Padrão
INSERT INTO `configuracoes` (`config`, `valor`) VALUES
('app_name', 'Map-OS'),
('app_theme', 'white'),
('app_email', 'contato@empresa.com'),
('app telefone', '(00) 0000-0000'),
('per_page', '10'),
('email_protocol', 'mail') ON DUPLICATE KEY UPDATE valor=valor;

-- DRE - Contas Padrão
INSERT INTO `dre_contas` (`codigo`, `nome`, `tipo`, `categoria`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
('1', 'RECEITA BRUTA', 'receita', 'Receitas', 1, 1, NOW(), NOW()),
('1.1', 'Serviços', 'receita', 'Receitas', 2, 1, NOW(), NOW()),
('1.2', 'Produtos', 'receita', 'Receitas', 3, 1, NOW(), NOW()),
('2', 'IMPOSTOS', 'despesa', 'Impostos', 10, 1, NOW(), NOW()),
('2.1', 'ISS', 'despesa', 'Impostos', 11, 1, NOW(), NOW()),
('3', 'CUSTOS', 'custo', 'Custos', 20, 1, NOW(), NOW()),
('4', 'DESPESAS OPERACIONAIS', 'despesa', 'Despesas', 30, 1, NOW(), NOW()) ON DUPLICATE KEY UPDATE codigo=codigo;

-- Configuração de Impostos Padrão
INSERT INTO `impostos_config` (`tipo_regime`, `anexo_simples`, `aliquota_iss`, `retem_iss`, `created_at`, `updated_at`) VALUES
('simples_nacional', 'iii', 2.00, 0, NOW(), NOW()) ON DUPLICATE KEY UPDATE id=id;

-- ============================================================================
-- 4. FINALIZAÇÃO
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

SELECT '✅ MAPOS V5 - Instalação Completa!' AS mensagem;
SELECT 'Usuário: admin@mapos.com.br / admin' AS login_padrao;
SELECT 'Não esqueça de alterar a senha após o primeiro acesso!' AS aviso;
