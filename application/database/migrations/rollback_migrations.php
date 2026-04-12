<?php
/**
 * Script para reverter migrations (rollback)
 * USAR COM CUIDADO!
 * Executar via: php application/database/migrations/rollback_migrations.php
 */

define('BASEPATH', dirname(__DIR__, 3) . '/');
define('APPPATH', BASEPATH . 'application/');
define('ENVIRONMENT', 'production');

require_once BASEPATH . 'index.php';

echo "=== ATENÇÃO: ROLLBACK DE MIGRATIONS ===\n";
echo "Este script irá REMOVER as tabelas criadas pela atualização!\n\n";
echo "Tabelas que serão removidas:\n";
echo "- email_queue\n";
echo "- email_clicks\n";
echo "- scheduled_events\n";
echo "- push_notifications\n";
echo "- scheduled_notifications\n";
echo "- webhooks\n";
echo "- webhook_logs\n";
echo "- Índices de performance\n\n";

// Confirmação
if (!isset($argv[1]) || $argv[1] !== '--confirm') {
    echo "Para confirmar o rollback, execute:\n";
    echo "  php rollback_migrations.php --confirm\n\n";
    echo "Ou use --dry-run para simular:\n";
    echo "  php rollback_migrations.php --dry-run\n";
    exit(0);
}

$dryRun = (isset($argv[1]) && $argv[1] === '--dry-run');

if ($dryRun) {
    echo "[MODO SIMULAÇÃO] Nenhuma alteração será feita.\n\n";
}

$migrations = [
    '20260411000006_create_webhooks.php',
    '20260411000005_create_push_notifications.php',
    '20260411000004_add_performance_indexes.php',
    '20260411000003_create_scheduled_events.php',
    '20260411000002_create_email_clicks.php',
    '20260411000001_create_email_queue.php',
];

$reverted = 0;
$failed = 0;

foreach ($migrations as $migration_file) {
    $file_path = __DIR__ . '/' . $migration_file;

    if (!file_exists($file_path)) {
        echo "[SKIP] Arquivo não encontrado: {$migration_file}\n";
        continue;
    }

    echo "Revertendo: {$migration_file}... ";

    if ($dryRun) {
        echo "[SIMULAÇÃO - OK]\n";
        $reverted++;
        continue;
    }

    try {
        require_once $file_path;

        $class_name = str_replace(['_', '.php'], ['_', ''], $migration_file);
        $class_name = 'Migration_' . substr($class_name, strpos($class_name, '_') + 1);

        if (!class_exists($class_name)) {
            echo "[ERRO] Classe não encontrada\n";
            $failed++;
            continue;
        }

        $migration = new $class_name();

        if (method_exists($migration, 'down')) {
            $migration->down();
            echo "[OK]\n";
            $reverted++;
        } else {
            echo "[ERRO] Método down() não encontrado\n";
            $failed++;
        }

    } catch (Exception $e) {
        echo "[ERRO] " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=== Resumo ===\n";
echo "Revertidas: {$reverted}\n";
echo "Falhas: {$failed}\n";

if ($dryRun) {
    echo "\n[MODO SIMULAÇÃO] Nenhuma alteração foi feita.\n";
} else {
    echo "\nRollback concluído!\n";
}
