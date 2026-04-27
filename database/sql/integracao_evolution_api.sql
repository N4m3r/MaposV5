-- =============================================================================
-- SQL Auxiliar: Integração MapOS + Evolution API
-- =============================================================================
-- Execute este script no MySQL/MariaDB do MapOS para preparar o ambiente.
--
-- 1. Token de segurança para o endpoint de atualização de IP
-- 2. Template de notificação WhatsApp para NFSe emitida
-- 3. Atualização da configuração do provedor (opcional)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. TOKEN DE SEGURANCA PARA ATUALIZACAO DE IP
-- -----------------------------------------------------------------------------
-- Este token é validado pelo endpoint /api/v2/evolution/atualizar-ip
-- Substitua 'seu-token-seguro-aqui-aleatorio' por uma chave forte (min 32 chars)

INSERT INTO `configuracoes` (`config`, `valor`)
VALUES ('evolution_ip_token', 'seu-token-seguro-aqui-aleatorio')
ON DUPLICATE KEY UPDATE `valor` = 'seu-token-seguro-aqui-aleatorio';

-- -----------------------------------------------------------------------------
-- 2. TEMPLATE WHATSAPP: NFSe EMITIDA
-- -----------------------------------------------------------------------------
-- Adiciona template de notificação quando uma NFSe é emitida com sucesso.

INSERT INTO `notificacoes_templates`
    (`chave`, `nome`, `descricao`, `categoria`, `canal`, `mensagem`, `variaveis`, `ativo`, `e_marketing`)
VALUES
    (
        'nfse_emitida',
        'NFSe Emitida',
        'Notificação enviada quando uma NFSe é emitida com sucesso via API Nacional',
        'sistema',
        'whatsapp',
        'Olá {cliente_nome}! 📄\n\nSua Nota Fiscal de Serviço foi emitida com sucesso!\n\n*Número:* {nfse_numero}\n*Valor:* R$ {valor_total}\n*Data:* {data_emissao}\n\nAcesse o PDF pelo link:\n{link_pdf}\n\nObrigado pela preferência! 🤝',
        '{"cliente_nome":"Nome do cliente","nfse_numero":"Número da NFSe","valor_total":"Valor total da nota","data_emissao":"Data de emissão","link_pdf":"Link para download do PDF da NFSe"}',
        1,
        0
    )
ON DUPLICATE KEY UPDATE
    `mensagem` = VALUES(`mensagem`),
    `variaveis` = VALUES(`variaveis`),
    `ativo` = VALUES(`ativo`);

-- -----------------------------------------------------------------------------
-- 3. (OPCIONAL) ATIVAR O PROVEDOR EVOLUTION E CONFIGURAR URL INICIAL
-- -----------------------------------------------------------------------------
-- Descomente e ajuste se quiser pré-configurar a Evolution API no banco.
-- Normalmente feito pelo painel: Configurações → Notificações → WhatsApp

-- UPDATE `notificacoes_config`
-- SET
--     `whatsapp_provedor` = 'evolution',
--     `whatsapp_ativo` = 1,
--     `evolution_url` = 'http://SEU_IP_PUBLICO:8080',
--     `evolution_apikey` = 'sua-chave-secreta-aqui',
--     `evolution_instance` = 'mapos',
--     `evolution_estado` = 'desconectado'
-- WHERE `id` = 1;

-- =============================================================================
-- FIM DO SCRIPT
-- =============================================================================
