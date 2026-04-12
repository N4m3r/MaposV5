<?php
/**
 * Script para executar migrations
 * Uso: php run_migrations.php
 */

// Carrega o CodeIgniter
require_once __DIR__ . '/../../../index.php';

// Carrega as migrations de email
$migrations = [
    '001_create_email_tables.php',
    '20260411000001_create_email_queue.php',
    '20260411000002_create_email_clicks.php',
    '20260411000003_create_scheduled_events.php',
    '20260411000004_add_performance_indexes.php',
    '20260411000005_create_push_notifications.php',
    '20260411000006_create_webhooks.php'
];

$ci = &get_instance();
$ci->load->database();

foreach ($migrations as $migration_file) {
    $file_path = __DIR__ . '/' . $migration_file;
    
    if (!file_exists($file_path)) {
        echo "Arquivo não encontrado: {$migration_file}\n";
        continue;
    }
    
    require_once $file_path;
    
    // Extrai o nome da classe
    $class_name = str_replace('.php', '', $migration_file);
    $class_name = str_replace(['-', '.'], '_', $class_name);
    $class_name = 'Migration_' . substr($class_name, 4); // Remove o número
    
    if (class_exists($class_name)) {
        $migration = new $class_name();
        
        try {
            echo "Executando: {$migration_file}\n";
            $migration->up($ci->db);
            echo "✓ Concluído\n\n";
        } catch (Exception $e) {
            echo "✗ Erro: " . $e->getMessage() . "\n\n";
        }
    } else {
        echo "Classe não encontrada: {$class_name}\n";
    }
}

echo "=== Migrations concluídas ===\n";
