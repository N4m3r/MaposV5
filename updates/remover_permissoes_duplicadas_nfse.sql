-- =====================================================
-- SCRIPT DE LIMPEZA: Remover permissões NFSe/Boleto duplicadas
-- Execute este script no phpMyAdmin ou MySQL Workbench
-- para remover as permissões individuais que agora são
-- controladas via checkboxes na categoria "NFSe e Boletos"
-- =====================================================

-- Remover permissões NFSe individuais
DELETE FROM `permissoes`
WHERE `nome` IN (
    'Visualizar NFSe (OS)',
    'Cadastrar NFSe (OS)',
    'Editar NFSe (OS)',
    'Visualizar Boleto OS',
    'Cadastrar Boleto OS',
    'Editar Boleto OS',
    'Relatório NFSe'
);

-- Verificar se foram removidas
SELECT * FROM `permissoes`
WHERE `nome` LIKE '%NFSe%'
   OR `nome` LIKE '%Boleto OS%'
   OR `permissoes` LIKE '%vNFSe%'
   OR `permissoes` LIKE '%cNFSe%'
   OR `permissoes` LIKE '%eNFSe%'
   OR `permissoes` LIKE '%rNFSe%'
   OR `permissoes` LIKE '%vBoletoOS%'
   OR `permissoes` LIKE '%cBoletoOS%'
   OR `permissoes` LIKE '%eBoletoOS%';

-- =====================================================
-- Após executar este script:
-- 1. As permissões NFSe/Boleto serão controladas apenas via checkboxes
-- 2. Acesse: Sistema > Configurações > Permissões > Editar
-- 3. Expanda a categoria "NFSe e Boletos" para configurar as permissões
-- =====================================================
