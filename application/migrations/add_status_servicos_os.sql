-- Adicionar coluna status na tabela servicos_os se não existir
ALTER TABLE servicos_os ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'Pendente' AFTER subTotal;

-- Atualizar registros existentes sem status
UPDATE servicos_os SET status = 'Pendente' WHERE status IS NULL OR status = '';
