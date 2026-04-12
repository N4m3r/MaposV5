#!/usr/bin/env php
<?php
/**
 * Script CLI para executar migration do Cora
 * Uso: php run_cora_migration.php
 */

if (php_sapi_name() !== 'cli') {
    die("Este script deve ser executado via CLI\n");
}

echo "=== MAPOS Cora Migration ===\n\n";

// Caminhos
define('BASEPATH', dirname(__DIR__, 3) . '/');
define('APPPATH', BASEPATH . 'application/');

echo "Carregando configurações...\n";

// Carrega configuração do banco
require_once APPPATH . 'config/database.php';

// Conecta ao banco
try {
    $dsn = "mysql:host={$db['default']['hostname']};dbname={$db['default']['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db['default']['username'], $db['default']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conectado ao banco: {$db['default']['database']}\n\n";
} catch (Exception $e) {
    die("✗ Erro ao conectar ao banco: " . $e->getMessage() . "\n");
}

// Verifica se migration já foi executada
$stmt = $pdo->query("SHOW COLUMNS FROM cobrancas LIKE 'pix_code'");
if ($stmt->rowCount() > 0) {
    echo "⚠ Migration já foi executada anteriormente.\n";
    echo "Deseja RODAR NOVAMENTE? (s/N): ";
    $resposta = trim(fgets(STDIN));
    if (strtolower($resposta) !== 's') {
        die("Operação cancelada.\n");
    }
    echo "\n";
}

// Executa as alterações
$queries = [
    // Adiciona linha_digitavel
    "ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS linha_digitavel VARCHAR(255) NULL DEFAULT NULL AFTER barcode",

    // Adiciona pix_code
    "ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS pix_code TEXT NULL DEFAULT NULL AFTER link",

    // Adiciona paid_at
    "ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS paid_at DATETIME NULL DEFAULT NULL AFTER expire_at",

    // Adiciona updated_at
    "ALTER TABLE cobrancas ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL DEFAULT NULL AFTER created_at",

    // Aumenta message para TEXT
    "ALTER TABLE cobrancas MODIFY COLUMN message TEXT NULL DEFAULT NULL",

    // Índices
    "CREATE INDEX IF NOT EXISTS idx_cobrancas_charge_id ON cobrancas(charge_id)",
    "CREATE INDEX IF NOT EXISTS idx_cobrancas_status_gateway ON cobrancas(status, payment_gateway)",
];

$sucesso = 0;
$erros = [];

foreach ($queries as $query) {
    try {
        $pdo->exec($query);
        echo "✓ Executado: " . substr($query, 0, 50) . "...\n";
        $sucesso++;
    } catch (PDOException $e) {
        // Ignora erros de "coluna já existe" ou "índice já existe"
        if (strpos($e->getMessage(), 'Duplicate') !== false ||
            strpos($e->getMessage(), 'already exists') !== false) {
            echo "⊘ Ignorado (já existe): " . substr($query, 0, 40) . "...\n";
            $sucesso++;
        } else {
            echo "✗ Erro: " . $e->getMessage() . "\n";
            $erros[] = $e->getMessage();
        }
    }
}

echo "\n=== Resultado ===\n";
echo "✓ Queries executadas: {$sucesso}/" . count($queries) . "\n";

if (count($erros) > 0) {
    echo "✗ Erros: " . count($erros) . "\n";
    foreach ($erros as $erro) {
        echo "  - {$erro}\n";
    }
    exit(1);
}

echo "✓ Migration concluída com sucesso!\n";
echo "\nCampos adicionados:\n";
echo "  - linha_digitavel (VARCHAR 255)\n";
echo "  - pix_code (TEXT)\n";
echo "  - paid_at (DATETIME)\n";
echo "  - updated_at (DATETIME)\n";
echo "  - message alterado para TEXT\n";
echo "  - Índices criados\n";
