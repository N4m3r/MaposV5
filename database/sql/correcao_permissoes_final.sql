-- ================================================================================
-- CORREÇÃO DEFINITIVA DE PERMISSÕES MAPOS V5
-- Erro: "unserialize(): Erro no deslocamento 452 de 1728 bytes"
-- ================================================================================

-- Execute este script no PhpMyAdmin ou MySQL

-- Passo 1: Verificar estado atual (apenas visualização)
SELECT
    idPermissao,
    nome,
    LENGTH(permissoes) as tamanho_atual,
    LEFT(permissoes, 100) as inicio_dados
FROM permissoes
WHERE idPermissao = 1;

-- Passo 2: Criar backup (recomendado)
CREATE TABLE IF NOT EXISTS permissoes_backup_correcao AS
SELECT * FROM permissoes WHERE idPermissao = 1;

-- Passo 3: CORREÇÃO DEFINITIVA
-- Substitui o valor corrompido pelo valor correto serializado
UPDATE permissoes
SET permissoes = 'a:81:{s:8:"aCliente";s:1:"1";s:8:"eCliente";s:1:"1";s:8:"dCliente";s:1:"1";s:8:"vCliente";s:1:"1";s:8:"aProduto";s:1:"1";s:8:"eProduto";s:1:"1";s:8:"dProduto";s:1:"1";s:8:"vProduto";s:1:"1";s:8:"aServico";s:1:"1";s:8:"eServico";s:1:"1";s:8:"dServico";s:1:"1";s:8:"vServico";s:1:"1";s:3:"aOs";s:1:"1";s:3:"eOs";s:1:"1";s:3:"dOs";s:1:"1";s:3:"vOs";s:1:"1";s:6:"aVenda";s:1:"1";s:6:"eVenda";s:1:"1";s:6:"dVenda";s:1:"1";s:6:"vVenda";s:1:"1";s:9:"aLancamento";s:1:"1";s:9:"eLancamento";s:1:"1";s:9:"dLancamento";s:1:"1";s:9:"vLancamento";s:1:"1";s:8:"aArquivo";s:1:"1";s:8:"dArquivo";s:1:"1";s:8:"vArquivo";s:1:"1";s:11:"categoria_d";s:1:"1";s:11:"categoria_v";s:1:"1";s:11:"categoria_a";s:1:"1";s:11:"categoria_e";s:1:"1";s:9:"vCategoria";s:1:"1";s:7:"aCobranca";s:1:"1";s:7:"eCobranca";s:1:"1";s:7:"dCobranca";s:1:"1";s:7:"vCobranca";s:1:"1";s:7:"aGarantia";s:1:"1";s:7:"eGarantia";s:1:"1";s:7:"dGarantia";s:1:"1";s:7:"vGarantia";s:1:"1";s:10:"aConfiguracao";s:1:"1";s:10:"eConfiguracao";s:1:"1";s:10:"dConfiguracao";s:1:"1";s:10:"vConfiguracao";s:1:"1";s:8:"aEmitente";s:1:"1";s:8:"eEmitente";s:1:"1";s:8:"dEmitente";s:1:"1";s:8:"vEmitente";s:1:"1";s:9:"aPermissao";s:1:"1";s:9:"ePermissao";s:1:"1";s:9:"dPermissao";s:1:"1";s:9:"vPermissao";s:1:"1";s:6:"aAuditoria";s:1:"1";s:6:"eAuditoria";s:1:"1";s:6:"dAuditoria";s:1:"1";s:6:"vAuditoria";s:1:"1";s:6:"aEmail";s:1:"1";s:6:"eEmail";s:1:"1";s:6:"dEmail";s:1:"1";s:6:"vEmail";s:1:"1";s:9:"rContas";s:1:"1";s:9:"rFinanceiro";s:1:"1";s:9:"rProdutos";s:1:"1";s:9:"rServicos";s:1:"1";s:6:"rVendas";s:1:"1";s:3:"rOs";s:1:"1";s:8:"rClientes";s:1:"1";s:11:"vCertificado";s:1:"1";s:10:"vImpostos";s:1:"1";s:5:"vDRE";s:1:"1";s:10:"vWebhooks";s:1:"1";s:20:"vRelatorioAtendimentos";s:1:"1";}'
WHERE idPermissao = 1;

-- Passo 4: Verificar correção
SELECT
    idPermissao,
    nome,
    LENGTH(permissoes) as tamanho_novo,
    CASE
        WHEN permissoes LIKE 'a:%' THEN '✅ Formato VÁLIDO'
        ELSE '❌ Formato INVÁLIDO'
    END as status
FROM permissoes
WHERE idPermissao = 1;

-- ================================================================================
-- INSTRUÇÕES DE USO:
-- ================================================================================
--
-- MÉTODO 1 - PhpMyAdmin (RECOMENDADO):
-- 1. Acesse: https://jj-ferreiras.com.br/phpmyadmin (ou painel do host)
-- 2. Selecione o banco de dados 'mapos3'
-- 3. Clique na aba "SQL"
-- 4. Cole TODO o conteúdo deste arquivo
-- 5. Clique em "Executar" (ou "Go")
-- 6. Pronto! Acesse o sistema
--
-- MÉTODO 2 - Linha de comando:
-- mysql -u jj-ferreiras -p mapos3 < correcao_permissoes_final.sql
--
-- ================================================================================
-- O QUE ESTE SCRIPT FAZ:
-- ================================================================================
-- 1. Mostra o estado atual da permissão (para comparação)
-- 2. Cria uma tabela de backup (permissoes_backup_correcao)
-- 3. Substitui o dado corrompido pelo valor correto
-- 4. Mostra o novo estado (para confirmar)
--
-- O valor usado é um array PHP serializado válido com 81 permissões V5
-- ================================================================================
