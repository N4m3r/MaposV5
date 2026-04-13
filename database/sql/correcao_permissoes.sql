-- ================================================================================
-- CORREÇÃO: Dados de Permissões com Erro de Serialização
-- Execute este script no PhpMyAdmin ou MySQL para corrigir permissões mal formatadas
-- ================================================================================

-- ================================================================================
-- MÉTODO 1: Correção via SQL (copie e cole o valor abaixo exatamente)
-- ================================================================================

-- ATENÇÃO: Este UPDATE deve ser executado em uma única linha
-- O valor abaixo é um array serializado PHP válido com 81 permissões V5

UPDATE permissoes SET permissoes = 'a:81:{s:8:"aCliente";s:1:"1";s:8:"eCliente";s:1:"1";s:8:"dCliente";s:1:"1";s:8:"vCliente";s:1:"1";s:8:"aProduto";s:1:"1";s:8:"eProduto";s:1:"1";s:8:"dProduto";s:1:"1";s:8:"vProduto";s:1:"1";s:8:"aServico";s:1:"1";s:8:"eServico";s:1:"1";s:8:"dServico";s:1:"1";s:8:"vServico";s:1:"1";s:3:"aOs";s:1:"1";s:3:"eOs";s:1:"1";s:3:"dOs";s:1:"1";s:3:"vOs";s:1:"1";s:6:"aVenda";s:1:"1";s:6:"eVenda";s:1:"1";s:6:"dVenda";s:1:"1";s:6:"vVenda";s:1:"1";s:9:"aLancamento";s:1:"1";s:9:"eLancamento";s:1:"1";s:9:"dLancamento";s:1:"1";s:9:"vLancamento";s:1:"1";s:8:"aArquivo";s:1:"1";s:8:"dArquivo";s:1:"1";s:8:"vArquivo";s:1:"1";s:11:"categoria_d";s:1:"1";s:11:"categoria_v";s:1:"1";s:11:"categoria_a";s:1:"1";s:11:"categoria_e";s:1:"1";s:9:"vCategoria";s:1:"1";s:7:"aCobranca";s:1:"1";s:7:"eCobranca";s:1:"1";s:7:"dCobranca";s:1:"1";s:7:"vCobranca";s:1:"1";s:7:"aGarantia";s:1:"1";s:7:"eGarantia";s:1:"1";s:7:"dGarantia";s:1:"1";s:7:"vGarantia";s:1:"1";s:10:"aConfiguracao";s:1:"1";s:10:"eConfiguracao";s:1:"1";s:10:"dConfiguracao";s:1:"1";s:10:"vConfiguracao";s:1:"1";s:8:"aEmitente";s:1:"1";s:8:"eEmitente";s:1:"1";s:8:"dEmitente";s:1:"1";s:8:"vEmitente";s:1:"1";s:9:"aPermissao";s:1:"1";s:9:"ePermissao";s:1:"1";s:9:"dPermissao";s:1:"1";s:9:"vPermissao";s:1:"1";s:6:"aAuditoria";s:1:"1";s:6:"eAuditoria";s:1:"1";s:6:"dAuditoria";s:1:"1";s:6:"vAuditoria";s:1:"1";s:6:"aEmail";s:1:"1";s:6:"eEmail";s:1:"1";s:6:"dEmail";s:1:"1";s:6:"vEmail";s:1:"1";s:9:"rContas";s:1:"1";s:9:"rFinanceiro";s:1:"1";s:9:"rProdutos";s:1:"1";s:9:"rServicos";s:1:"1";s:6:"rVendas";s:1:"1";s:3:"rOs";s:1:"1";s:8:"rClientes";s:1:"1";s:11:"vCertificado";s:1:"1";s:10:"vImpostos";s:1:"1";s:5:"vDRE";s:1:"1";s:10:"vWebhooks";s:1:"1";s:20:"vRelatorioAtendimentos";s:1:"1";}' WHERE idPermissao = 1;

-- Verificar se a correção foi aplicada
SELECT idPermissao, nome, LENGTH(permissoes) as tamanho,
       CASE
         WHEN permissoes LIKE 'a:%' THEN 'Formato válido'
         ELSE 'FORMATO INVÁLIDO - Execute a correção novamente'
       END as status
FROM permissoes
WHERE idPermissao = 1;

-- ================================================================================
-- INSTRUÇÕES DE USO:
-- ================================================================================
--
-- OPÇÃO 1 - PhpMyAdmin:
-- 1. Acesse o PhpMyAdmin
-- 2. Selecione o banco de dados 'mapos3'
-- 3. Clique na aba "SQL"
-- 4. Cole TODO o conteúdo deste arquivo
-- 5. Clique em "Executar"
--
-- OPÇÃO 2 - Linha de comando:
-- mysql -u usuario -p mapos3 < correcao_permissoes.sql
--
-- OPÇÃO 3 - Script PHP (RECOMENDADO):
-- Use o arquivo corrigir_permissoes.php na raiz do projeto
-- Acesse: https://seusite.com/corrigir_permissoes.php
--
-- ================================================================================
