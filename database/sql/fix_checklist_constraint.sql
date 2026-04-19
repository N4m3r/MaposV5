-- Remover constraint UNIQUE que impede múltiplos templates do mesmo tipo
-- Execute este SQL no seu banco de dados MySQL

ALTER TABLE tec_checklist_template DROP INDEX uk_template;

-- Verificar se foi removido
SHOW INDEX FROM tec_checklist_template;
