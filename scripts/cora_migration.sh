#!/bin/bash
# Script para executar migration do Cora via SSH
# Uso: ./cora_migration.sh

echo "=== MAPOS Cora Migration ==="
echo ""

# Verifica se está no diretório correto
if [ ! -f "index.php" ]; then
    echo "✗ Erro: Execute este script no diretório raiz do MAPOS"
    exit 1
fi

# Opção 1: Usar CodeIgniter Migration (se disponível)
if [ -f "application/database/migrations/20260412000001_add_cora_support.php" ]; then
    echo "✓ Migration encontrada"
    echo ""
    echo "Executando via CodeIgniter..."
    php index.php migrate/run add_cora_support 2>/dev/null || echo "Usando método alternativo..."
fi

# Opção 2: Executar SQL diretamente
echo ""
echo "Executando migration SQL..."

php -r "
\$config = require 'application/config/database.php';
\$db = \$config['default'];

\$mysqli = new mysqli(\$db['hostname'], \$db['username'], \$db['password'], \$db['database']);

if (\$mysqli->connect_error) {
    die('✗ Erro de conexão: ' . \$mysqli->connect_error . \"\n\");
}

echo \"✓ Conectado ao banco: \" . \$db['database'] . \"\n\n\";

\$queries = [
    'ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS linha_digitavel VARCHAR(255) NULL DEFAULT NULL AFTER barcode',
    'ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS pix_code TEXT NULL DEFAULT NULL AFTER link',
    'ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS paid_at DATETIME NULL DEFAULT NULL AFTER expire_at',
    'ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL DEFAULT NULL AFTER created_at',
    'ALTER TABLE cobrancas MODIFY COLUMN message TEXT NULL DEFAULT NULL',
    'CREATE INDEX IF NOT EXISTS idx_cobrancas_charge_id ON cobrancas(charge_id)',
    'CREATE INDEX IF NOT EXISTS idx_cobrancas_status_gateway ON cobrancas(status, payment_gateway)',
];

\$sucesso = 0;
foreach (\$queries as \$sql) {
    if (\$mysqli->query(\$sql)) {
        echo \"✓ OK: \" . substr(\$sql, 0, 40) . \"...\n\";
        \$sucesso++;
    } else {
        if (strpos(\$mysqli->error, 'Duplicate') !== false || strpos(\$mysqli->error, 'already exists') !== false) {
            echo \"⊘ Ignorado (já existe): \" . substr(\$sql, 0, 35) . \"...\n\";
            \$sucesso++;
        } else {
            echo \"✗ Erro: \" . \$mysqli->error . \"\n\";
        }
    }
}

echo \"\n=== Resultado ===\n\";
echo \"✓ Queries executadas: \$sucesso/\" . count(\$queries) . \"\n\";
echo \"✓ Migration concluída!\n\";

\$mysqli->close();
"

echo ""
echo "Campos adicionados:"
echo "  - linha_digitavel"
echo "  - pix_code"
echo "  - paid_at"
echo "  - updated_at"
echo "  - message (TEXT)"
echo "  - Índices criados"