-- Adicionar coluna 'titulo' à tabela obra_atividades se não existir
-- Execute este SQL no phpMyAdmin ou similar para corrigir o erro

-- Verificar se a coluna existe antes de adicionar
SET @dbname = DATABASE();
SET @tablename = 'obra_atividades';
SET @columnname = 'titulo';

SET @sql = IF(
    NOT EXISTS(
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = @dbname
        AND table_name = @tablename
        AND column_name = @columnname
    ),
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(255) NOT NULL AFTER os_id'),
    'SELECT "Coluna ja existe" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Alternativa simples (se a acima não funcionar, use esta):
-- ALTER TABLE obra_atividades ADD COLUMN IF NOT EXISTS titulo VARCHAR(255) NOT NULL AFTER os_id;
