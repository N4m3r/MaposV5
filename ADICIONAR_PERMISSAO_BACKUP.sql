-- =============================================
-- SQL para Adicionar Permissão de Backup
-- Execute no phpMyAdmin ou MySQL
-- =============================================

-- Passo 1: Verificar permissões atuais do Administrador
SELECT idPermissao, nome, permissoes
FROM permissoes
WHERE nome = 'Administrador';

-- Passo 2: Atualizar permissões do Administrador para incluir cBackup
-- ATENÇÃO: Isso sobrescreve as permissões existentes!
-- Se quiser manter as existentes, use o Passo 3

-- Opção A: Definir apenas cBackup (APAGA outras permissões - NÃO RECOMENDADO)
-- UPDATE permissoes
-- SET permissoes = 'a:1:{s:7:"cBackup";s:1:"1";}'
-- WHERE nome = 'Administrador';

-- Opção B: Adicionar cBackup mantendo outras permissões (RECOMENDADO)
-- Primeiro, verifique o formato atual:
SELECT permissoes FROM permissoes WHERE nome = 'Administrador' LIMIT 1;

-- Se o formato for serialized (começa com 'a:' ou contém '{s:'), use:
UPDATE permissoes
SET permissoes = CONCAT(
    SUBSTRING(permissoes, 1, LENGTH(permissoes) - 1),
    CASE
        WHEN permissoes LIKE '%}' THEN ';s:7:"cBackup";s:1:"1";}'
        ELSE 's:7:"cBackup";s:1:"1";}'
    END
)
WHERE nome = 'Administrador'
AND permissoes NOT LIKE '%cBackup%';

-- Se o formato for JSON (começa com '{' ou '['), use:
-- UPDATE permissoes
-- SET permissoes = JSON_SET(permissoes, '$.cBackup', '1')
-- WHERE nome = 'Administrador';

-- Passo 3: Verificar se foi adicionado
SELECT idPermissao, nome, permissoes
FROM permissoes
WHERE nome = 'Administrador';

-- =============================================
-- ALTERNATIVA: Adicionar para TODOS os grupos
-- =============================================

-- Adicionar cBackup a todos os grupos que não têm:
-- UPDATE permissoes
-- SET permissoes = CONCAT(
--     SUBSTRING(permissoes, 1, LENGTH(permissoes) - 1),
--     ';s:7:"cBackup";s:1:"1";}'
-- )
-- WHERE permissoes NOT LIKE '%cBackup%';

-- =============================================
-- SOLUÇÃO TEMPORÁRIA: Bypass via Session
-- =============================================
-- Se você tem acesso ao servidor, adicione no
-- application/controllers/Backup.php no início do __construct():
-- $_SESSION['bypass_permissao_backup'] = true;
-- (Já existe no código, só descomentar)
