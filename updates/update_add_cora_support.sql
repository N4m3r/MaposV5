-- Migração para adicionar suporte ao Banco Cora (Boleto + PIX)
-- Campos adicionais para armazenar dados completos da cobrança

-- Adiciona campo para linha digitável do boleto
ALTER TABLE `cobrancas`
ADD COLUMN IF NOT EXISTS `linha_digitavel` VARCHAR(255) NULL DEFAULT NULL AFTER `barcode`;

-- Adiciona campo específico para código PIX
ALTER TABLE `cobrancas`
ADD COLUMN IF NOT EXISTS `pix_code` TEXT NULL DEFAULT NULL AFTER `link`;

-- Adiciona campo para data de pagamento
ALTER TABLE `cobrancas`
ADD COLUMN IF NOT EXISTS `paid_at` DATETIME NULL DEFAULT NULL AFTER `expire_at`;

-- Adiciona campo para data de atualização
ALTER TABLE `cobrancas`
ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL DEFAULT NULL AFTER `created_at`;

-- Aumenta o tamanho do campo message para descrições mais longas
ALTER TABLE `cobrancas`
MODIFY COLUMN `message` TEXT NULL DEFAULT NULL;

-- Índice para melhorar buscas por charge_id
CREATE INDEX IF NOT EXISTS `idx_cobrancas_charge_id` ON `cobrancas`(`charge_id`);

-- Índice para buscas por status e gateway
CREATE INDEX IF NOT EXISTS `idx_cobrancas_status_gateway` ON `cobrancas`(`status`, `payment_gateway`);