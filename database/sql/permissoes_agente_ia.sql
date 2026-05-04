-- ============================================================
-- SQL: Permissoes Agente IA (vAgenteIA / cAgenteIA)
-- Execute este script no banco de dados para liberar o modulo
-- no grupo Administrador e criar seeds de permissoes individuais.
-- ============================================================

-- 1. Garantir que as permissoes individuais existam (para listagem/criacao de novos grupos)
INSERT IGNORE INTO permissoes (nome, permissoes, situacao, data) VALUES
('Visualizar Painel Agente IA', 'a:1:{s:9:"vAgenteIA";i:1;}', 1, CURDATE()),
('Configurar Agente IA',        'a:1:{s:9:"cAgenteIA";i:1;}', 1, CURDATE());

-- 2. Atualizar o perfil Administrador (idPermissao=1) para incluir vAgenteIA e cAgenteIA
--    Esta abordagem usa CASE + REPLACE para mesclar sem quebrar a serializacao existente.
UPDATE permissoes
SET permissoes =
    CASE
        WHEN idPermissao = 1 THEN
            CASE
                -- Ja tem vAgenteIA? Nao altera nada
                WHEN permissoes LIKE '%vAgenteIA%' THEN permissoes
                -- Senao, insere antes do ultimo '}'
                ELSE REPLACE(permissoes, '}', 's:9:"vAgenteIA";s:1:"1";s:9:"cAgenteIA";s:1:"1";}' )
            END
        ELSE permissoes
    END
WHERE idPermissao = 1;

-- 3. Script alternativo (se o CASE acima nao funcionar devido a formatos de serializacao diferentes)
--    Descomente e ajuste conforme necessario:
--
-- UPDATE permissoes
-- SET permissoes = CONCAT(
--     LEFT(permissoes, LENGTH(permissoes) - 1),
--     's:9:"vAgenteIA";s:1:"1";s:9:"cAgenteIA";s:1:"1";}'
-- )
-- WHERE idPermissao = 1 AND permissoes NOT LIKE '%vAgenteIA%';

-- 4. Verificar permissoes atualizadas
SELECT idPermissao, nome, permissoes FROM permissoes WHERE idPermissao = 1;
