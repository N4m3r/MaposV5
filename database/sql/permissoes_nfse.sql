-- ============================================================
-- SQL: Permissoes NFSe e Boleto OS
-- Execute este script no banco de dados para liberar o modulo
-- ============================================================

-- 1. Inserir permissoes individuais (permissoes de tela)
INSERT IGNORE INTO permissoes (nome, permissoes, situacao, data) VALUES
('Visualizar NFSe (OS)', 'a:1:{s:5:"vNFSe";i:1;}', 1, CURDATE()),
('Cadastrar NFSe (OS)', 'a:1:{s:5:"cNFSe";i:1;}', 1, CURDATE()),
('Editar NFSe (OS)', 'a:1:{s:5:"eNFSe";i:1;}', 1, CURDATE()),
('Visualizar Boleto OS', 'a:1:{s:9:"vBoletoOS";i:1;}', 1, CURDATE()),
('Cadastrar Boleto OS', 'a:1:{s:9:"cBoletoOS";i:1;}', 1, CURDATE()),
('Editar Boleto OS', 'a:1:{s:9:"eBoletoOS";i:1;}', 1, CURDATE()),
('Relatorio NFSe', 'a:1:{s:5:"rNFSe";i:1;}', 1, CURDATE());

-- 2. Atualizar o perfil Administrador (idPermissao=1) para incluir todas as permissoes
--    Este UPDATE usa REPLACE para garantir que as permissoes novas sejam mescladas
UPDATE permissoes
SET permissoes =
    CASE
        WHEN idPermissao = 1 THEN
            -- Mesclar permissoes existentes com as novas
            CASE
                WHEN permissoes LIKE '%vNFSe%' THEN permissoes
                ELSE REPLACE(
                    REPLACE(permissoes, '}', ''),
                    's:10:"a:1:{s:0:"";i:1;}"',
                    ''
                )
            END
        ELSE permissoes
    END
WHERE idPermissao = 1;

-- 3. Script alternativo (se o acima nao funcionar): atualizar via PHP/serializacao manual
--    Descomente e ajuste conforme necessario:
--
-- UPDATE permissoes SET permissoes = 'a:65:{s:8:"aCliente";s:1:"1";s:10:"eCliente";s:1:"1";s:10:"dCliente";s:1:"1";s:8:"vCliente";s:1:"1";s:8:"aProduto";s:1:"1";s:10:"eProduto";s:1:"1";s:10:"dProduto";s:1:"1";s:8:"vProduto";s:1:"1";s:8:"aServico";s:1:"1";s:10:"eServico";s:1:"1";s:10:"dServico";s:1:"1";s:8:"vServico";s:1:"1";s:3:"aOs";s:1:"1";s:5:"eOs";s:1:"1";s:5:"dOs";s:1:"1";s:3:"vOs";s:1:"1";s:6:"aVenda";s:1:"1";s:8:"eVenda";s:1:"1";s:8:"dVenda";s:1:"1";s:6:"vVenda";s:1:"1";s:9:"aGarantia";s:1:"1";s:11:"eGarantia";s:1:"1";s:11:"dGarantia";s:1:"1";s:9:"vGarantia";s:1:"1";s:11:"aArquivos";s:1:"1";s:10:"eAnexos";s:1:"1";s:10:"dAnexos";s:1:"1";s:10:"vAnexos";s:1:"1";s:8:"aLancamento";s:1:"1";s:10:"eLancamento";s:1:"1";s:10:"dLancamento";s:1:"1";s:8:"vLancamento";s:1:"1";s:8:"cUsuario";s:1:"1";s:10:"cEmitente";s:1:"1";s:10:"cPermissao";s:1:"1";s:10:"cAuditoria";s:1:"1";s:9:"cBackup";s:1:"1";s:9:"vCabecalho";s:1:"1";s:9:"vCalendario";s:1:"1";s:8:"vFinanceiro";s:1:"1";s:9:"vConfiguracao";s:1:"1";s:11:"vVendasDoDia";s:1:"1";s:10:"vOsDoDia";s:1:"1";s:10:"vRelatorio";s:1:"1";s:9:"cEmail";s:1:"1";s:9:"vEmail";s:1:"1";s:7:"cSeguro";s:1:"1";s:7:"eSeguro";s:1:"1";s:7:"dSeguro";s:1:"1";s:7:"vSeguro";s:1:"1";s:8:"aCliente";s:1:"1";s:10:"eCliente";s:1:"1";s:10:"dCliente";s:1:"1";s:8:"vCliente";s:1:"1";s:8:"aProduto";s:1:"1";s:10:"eProduto";s:1:"1";s:10:"dProduto";s:1:"1";s:8:"vProduto";s:1:"1";s:8:"aServico";s:1:"1";s:10:"eServico";s:1:"1";s:10:"dServico";s:1:"1";s:8:"vServico";s:1:"1";s:5:"cNFSe";s:1:"1";s:5:"eNFSe";s:1:"1";s:5:"vNFSe";s:1:"1";s:5:"rNFSe";s:1:"1";s:9:"cBoletoOS";s:1:"1";s:9:"eBoletoOS";s:1:"1";s:9:"vBoletoOS";s:1:"1";}'
-- WHERE idPermissao = 1;

-- 4. Verificar permissoes inseridas
SELECT idPermissao, nome, situacao FROM permissoes WHERE nome LIKE '%NFSe%' OR nome LIKE '%Boleto%';
