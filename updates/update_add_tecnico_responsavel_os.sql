-- Atualização: Adicionar coluna tecnico_responsavel na tabela OS e criar tabela de atribuições
-- Data: 2026-04-13
-- Descrição: Adiciona suporte para atribuição de técnicos às OS

-- Adicionar coluna tecnico_responsavel na tabela os
SET @colExists = (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
    AND table_name = 'os'
    AND column_name = 'tecnico_responsavel'
);

SET @addColumn = IF(@colExists = 0,
    'ALTER TABLE `os` ADD COLUMN `tecnico_responsavel` INT(11) NULL COMMENT "ID do usuario tecnico responsavel pela OS"',
    'SELECT "Coluna tecnico_responsavel já existe" as msg'
);

PREPARE stmt FROM @addColumn;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar índice na coluna tecnico_responsavel
SET @idxExists = (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
    AND table_name = 'os'
    AND index_name = 'idx_tecnico_responsavel'
);

SET @addIndex = IF(@idxExists = 0,
    'ALTER TABLE `os` ADD INDEX `idx_tecnico_responsavel` (`tecnico_responsavel`)',
    'SELECT "Índice idx_tecnico_responsavel já existe" as msg'
);

PREPARE stmt2 FROM @addIndex;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Criar tabela de histórico de atribuições se não existir
CREATE TABLE IF NOT EXISTS `os_tecnico_atribuicao` (
    `idAtribuicao` INT(11) NOT NULL AUTO_INCREMENT,
    `os_id` INT(11) NOT NULL,
    `tecnico_id` INT(11) NOT NULL COMMENT 'ID do tecnico atribuido',
    `atribuido_por` INT(11) NOT NULL COMMENT 'ID do usuario que fez a atribuicao',
    `data_atribuicao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `data_remocao` DATETIME NULL,
    `motivo_remocao` TEXT NULL,
    `observacao` TEXT NULL,
    PRIMARY KEY (`idAtribuicao`),
    INDEX `idx_os_id` (`os_id`),
    INDEX `idx_tecnico_id` (`tecnico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Verificar se a tabela foi criada
SELECT "Tabela os_tecnico_atribuicao verificada/criada com sucesso" as msg;
