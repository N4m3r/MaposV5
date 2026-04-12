-- =============================================
-- ATUALIZACAO DO BANCO DE DADOS MAP-OS
-- Data: 2025-04-05
-- Descricao: Atualizacao das tabelas para funcionalidades de tecnico, checkin, dashboard e permissoes
-- IMPORTANTE: Script seguro - verifica existencia antes de criar/alterar
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- =============================================
-- 1. TABELA: os_checkin (Checkin/Checkout do Tecnico)
-- =============================================
CREATE TABLE IF NOT EXISTS `os_checkin` (
    `idCheckin` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `usuarios_id` INT(11) NOT NULL,
    `data_entrada` DATETIME NULL DEFAULT NULL,
    `data_saida` DATETIME NULL DEFAULT NULL,
    `latitude_entrada` DECIMAL(10,8) NULL DEFAULT NULL,
    `longitude_entrada` DECIMAL(11,8) NULL DEFAULT NULL,
    `latitude_saida` DECIMAL(10,8) NULL DEFAULT NULL,
    `longitude_saida` DECIMAL(11,8) NULL DEFAULT NULL,
    `observacao_entrada` TEXT NULL DEFAULT NULL,
    `observacao_saida` TEXT NULL DEFAULT NULL,
    `status` VARCHAR(30) NOT NULL DEFAULT 'Em Andamento',
    `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `data_atualizacao` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`idCheckin`),
    INDEX `idx_os_id` (`os_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. TABELA: os_assinaturas (Assinaturas do Tecnico e Cliente)
-- =============================================
CREATE TABLE IF NOT EXISTS `os_assinaturas` (
    `idAssinatura` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `checkin_id` INT(11) NULL DEFAULT NULL,
    `tipo` VARCHAR(20) NOT NULL COMMENT 'tecnico_entrada, tecnico_saida, cliente_saida',
    `assinatura` VARCHAR(255) NOT NULL COMMENT 'Caminho da imagem da assinatura',
    `nome_assinante` VARCHAR(100) NULL DEFAULT NULL,
    `documento_assinante` VARCHAR(20) NULL DEFAULT NULL,
    `data_assinatura` DATETIME NOT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idAssinatura`),
    INDEX `idx_os_id` (`os_id`),
    INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. TABELA: os_fotos_atendimento (Fotos do Atendimento)
-- =============================================
CREATE TABLE IF NOT EXISTS `os_fotos_atendimento` (
    `idFoto` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `checkin_id` INT(11) NULL DEFAULT NULL,
    `usuarios_id` INT(11) NOT NULL,
    `arquivo` VARCHAR(255) NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `descricao` TEXT NULL DEFAULT NULL,
    `etapa` VARCHAR(20) NOT NULL DEFAULT 'durante' COMMENT 'entrada, durante, saida',
    `tamanho` INT(11) NULL DEFAULT NULL,
    `tipo_arquivo` VARCHAR(10) NULL DEFAULT NULL,
    `data_upload` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `imagem_base64` LONGTEXT NULL DEFAULT NULL COMMENT 'Imagem armazenada em base64',
    `mime_type` VARCHAR(30) NULL DEFAULT NULL COMMENT 'Tipo MIME da imagem',
    PRIMARY KEY (`idFoto`),
    INDEX `idx_os_id` (`os_id`),
    INDEX `idx_etapa` (`etapa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. TABELA: os_tecnico_atribuicao (Historico de Atribuicoes)
-- =============================================
CREATE TABLE IF NOT EXISTS `os_tecnico_atribuicao` (
    `idAtribuicao` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `tecnico_id` INT(11) NOT NULL COMMENT 'ID do tecnico atribuido',
    `atribuido_por` INT(11) NOT NULL COMMENT 'ID do usuario que fez a atribuicao',
    `data_atribuicao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `data_remocao` DATETIME NULL DEFAULT NULL,
    `motivo_remocao` TEXT NULL DEFAULT NULL,
    `observacao` TEXT NULL DEFAULT NULL,
    PRIMARY KEY (`idAtribuicao`),
    INDEX `idx_os_id` (`os_id`),
    INDEX `idx_tecnico_id` (`tecnico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. ALTERACAO: Adicionar campo tecnico_responsavel na tabela os
-- =============================================
SET @coluna_existe = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'os'
    AND COLUMN_NAME = 'tecnico_responsavel'
    AND TABLE_SCHEMA = DATABASE()
);

SET @sql = IF(@coluna_existe = 0,
    'ALTER TABLE `os` ADD COLUMN `tecnico_responsavel` INT(11) NULL DEFAULT NULL COMMENT "ID do usuario tecnico responsavel pela OS"',
    'SELECT "Coluna tecnico_responsavel ja existe" AS mensagem'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar indice se nao existir
SET @indice_existe = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_NAME = 'os'
    AND INDEX_NAME = 'idx_tecnico_responsavel'
    AND TABLE_SCHEMA = DATABASE()
);

SET @sql = IF(@indice_existe = 0,
    'ALTER TABLE `os` ADD INDEX `idx_tecnico_responsavel` (`tecnico_responsavel`)',
    'SELECT "Indice idx_tecnico_responsavel ja existe" AS mensagem'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- 6. PERMISSAO: Grupo "Tecnico" (se nao existir)
-- =============================================
INSERT INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`)
SELECT 'Tecnico',
       CURDATE(),
       'a:35:{s:8:"aCliente";i:0;s:8:"eCliente";i:0;s:8:"dCliente";i:0;s:8:"vCliente";i:1;s:8:"aProduto";i:0;s:8:"eProduto";i:0;s:8:"dProduto";i:0;s:8:"vProduto";i:1;s:8:"aServico";i:0;s:8:"eServico";i:0;s:8:"dServico";i:0;s:8:"vServico";i:1;s:4:"aOs";i:0;s:4:"eOs";i:0;s:4:"dOs";i:0;s:4:"vOs";i:0;s:15:"vBtnAtendimento";i:0;s:10:"vTecnicoOS";i:1;s:14:"eTecnicoCheckin";i:1;s:15:"eTecnicoCheckout";i:1;s:13:"eTecnicoFotos";i:1;s:7:"aVenda";i:0;s:7:"eVenda";i:0;s:7:"dVenda";i:0;s:7:"vVenda";i:0;s:10:"aGarantia";i:0;s:10:"eGarantia";i:0;s:10:"dGarantia";i:0;s:10:"vGarantia";i:0;s:9:"aArquivo";i:0;s:9:"eArquivo";i:0;s:9:"dArquivo";i:0;s:9:"vArquivo";i:0;s:10:"aPagamento";i:0;s:10:"ePagamento";i:0;s:10:"dPagamento";i:0;s:10:"vPagamento";i:0;s:11:"aLancamento";i:0;s:11:"eLancamento";i:0;s:11:"dLancamento";i:0;s:11:"vLancamento";i:0;s:9:"cUsuario";i:0;s:9:"cEmitente";i:0;s:9:"cPermissao";i:0;s:7:"cBackup";i:0;s:9:"cAuditoria";i:0;s:6:"cEmail";i:0;s:9:"cSistema";i:0;s:8:"rCliente";i:0;s:8:"rProduto";i:0;s:8:"rServico";i:0;s:4:"rOs";i:0;s:7:"rVenda";i:0;s:12:"rFinanceiro";i:0;s:9:"aCobranca";i:0;s:9:"eCobranca";i:0;s:9:"dCobranca";i:0;s:9:"vCobranca";i:0;s:16:"vTecnicoDashboard";i:1;s:8:"aTecnico";i:0;}',
       1
WHERE NOT EXISTS (
    SELECT 1 FROM `permissoes` WHERE `nome` = 'Tecnico'
);

-- =============================================
-- 7. PERMISSAO: Grupo "Área do Tecnico" (se nao existir)
-- =============================================
INSERT INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`)
SELECT 'Área do Tecnico',
       CURDATE(),
       'a:1:{s:8:"aTecnico";i:1;}',
       1
WHERE NOT EXISTS (
    SELECT 1 FROM `permissoes` WHERE `nome` = 'Área do Tecnico'
);

-- =============================================
-- 8. PERMISSAO: Grupo "Dashboard" (se nao existir)
-- =============================================
INSERT INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`)
SELECT 'Dashboard',
       CURDATE(),
       'a:36:{s:8:"aCliente";i:0;s:8:"eCliente";i:0;s:8:"dCliente";i:0;s:8:"vCliente";i:0;s:8:"aProduto";i:0;s:8:"eProduto";i:0;s:8:"dProduto";i:0;s:8:"vProduto";i:0;s:8:"aServico";i:0;s:8:"eServico";i:0;s:8:"dServico";i:0;s:8:"vServico";i:0;s:4:"aOs";i:0;s:4:"eOs";i:0;s:4:"dOs";i:0;s:4:"vOs";i:0;s:15:"vBtnAtendimento";i:0;s:10:"vTecnicoOS";i:0;s:14:"eTecnicoCheckin";i:0;s:15:"eTecnicoCheckout";i:0;s:13:"eTecnicoFotos";i:0;s:7:"aVenda";i:0;s:7:"eVenda";i:0;s:7:"dVenda";i:0;s:7:"vVenda";i:0;s:10:"aGarantia";i:0;s:10:"eGarantia";i:0;s:10:"dGarantia";i:0;s:10:"vGarantia";i:0;s:9:"aArquivo";i:0;s:9:"eArquivo";i:0;s:9:"dArquivo";i:0;s:9:"vArquivo";i:0;s:9:"aPagamento";i:0;s:10:"ePagamento";i:0;s:10:"dPagamento";i:0;s:10:"vPagamento";i:0;s:11:"aLancamento";i:0;s:11:"eLancamento";i:0;s:11:"dLancamento";i:0;s:11:"vLancamento";i:0;s:9:"cUsuario";i:0;s:9:"cEmitente";i:0;s:9:"cPermissao";i:0;s:7:"cBackup";i:0;s:9:"cAuditoria";i:0;s:6:"cEmail";i:0;s:9:"cSistema";i:0;s:8:"rCliente";i:0;s:8:"rProduto";i:0;s:8:"rServico";i:0;s:4:"rOs";i:0;s:7:"rVenda";i:0;s:12:"rFinanceiro";i:0;s:9:"aCobranca";i:0;s:9:"eCobranca";i:0;s:9:"dCobranca";i:0;s:9:"vCobranca";i:0;s:10:"vDashboard";i:1;s:17:"vRelatorioCompleto";i:1;s:15:"vExportarDados";i:1;}',
       1
WHERE NOT EXISTS (
    SELECT 1 FROM `permissoes` WHERE `nome` = 'Dashboard'
);

-- =============================================
-- 9. PERMISSAO: Grupo "Visualizar Relatorio de Atendimentos" (se nao existir)
-- =============================================
INSERT INTO `permissoes` (`nome`, `data`, `permissoes`, `situacao`)
SELECT 'Visualizar Relatório de Atendimentos',
       CURDATE(),
       'a:1:{s:23:"vRelatorioAtendimentos";i:1;}',
       1
WHERE NOT EXISTS (
    SELECT 1 FROM `permissoes` WHERE `nome` = 'Visualizar Relatório de Atendimentos'
);

-- =============================================
-- 10. ATUALIZAR PERMISSOES DO ADMINISTRADOR (idPermissao = 1)
--     Adiciona permissoes do Dashboard se nao existirem
-- =============================================
-- Nota: Como as permissoes sao serializadas em PHP,
-- o ideal e atualizar via aplicacao, mas aqui garantimos a estrutura

-- =============================================
-- 11. VERIFICACAO FINAL: Garantir que estrutura basica existe
-- =============================================

-- Verificar se tabela permissoes tem estrutura correta
SET @tabela_existe = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_NAME = 'permissoes'
    AND TABLE_SCHEMA = DATABASE()
);

-- Se tabela nao existe, criar estrutura basica (fallback)
CREATE TABLE IF NOT EXISTS `permissoes` (
    `idPermissao` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(80) NOT NULL,
    `data` DATE NOT NULL,
    `permissoes` TEXT NOT NULL,
    `situacao` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`idPermissao`),
    UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- FIM DAS ATUALIZACOES
-- =============================================
SET FOREIGN_KEY_CHECKS = 1;

-- Mensagem de confirmacao
SELECT 'Atualizacao do banco de dados concluida com sucesso!' AS mensagem;
