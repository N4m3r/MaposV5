<?php
/**
 * CLI Script para MAP-OS
 * Facilita a execução de migrações e outras tarefas via linha de comando
 *
 * Uso: php cli.php migrate
 *      php cli.php migrate version 20250403000001
 *      php cli.php seed
 *      php cli.php seed Configuracoes
 */

// Define o ambiente como CLI
$_SERVER['CI_ENVIRONMENT'] = 'cli';

echo "==========================================\n";
echo "      MAP-OS CLI Tool\n";
echo "==========================================\n\n";

// Verifica argumentos
if ($argc < 2) {
    showHelp();
    exit(0);
}

$command = $argv[1];
$subCommand = $argv[2] ?? null;
$param = $argv[3] ?? null;

switch ($command) {
    case 'migrate':
    case 'migration':
        handleMigration($subCommand, $param);
        break;

    case 'seed':
    case 'seeder':
        handleSeed($subCommand);
        break;

    case 'help':
    case '--help':
    case '-h':
        showHelp();
        break;

    default:
        echo "Comando desconhecido: {$command}\n";
        showHelp();
        exit(1);
}

function handleMigration($action, $version = null) {
    echo "Executando migrações...\n";

    if ($action === 'version' && $version) {
        echo "Migração para versão: {$version}\n";
        passthru('php index.php tools migrate ' . escapeshellarg($version));
    } else {
        echo "Migração para a última versão...\n";
        passthru('php index.php tools migrate');
    }
}

function handleSeed($name = null) {
    if ($name) {
        echo "Executando seed: {$name}\n";
        passthru('php index.php tools seed ' . escapeshellarg($name));
    } else {
        echo "Executando todos os seeds...\n";
        passthru('php index.php tools seed');
    }
}

function showHelp() {
    echo "Uso: php cli.php [comando] [opções]\n\n";
    echo "Comandos disponíveis:\n\n";

    echo "  migrate                    Executa todas as migrações pendentes\n";
    echo "  migrate version [versao]   Executa migração específica\n";
    echo "  seed                       Executa todos os seeds\n";
    echo "  seed [nome]                Executa seed específico\n";
    echo "  help                       Mostra esta ajuda\n\n";

    echo "Exemplos:\n";
    echo "  php cli.php migrate\n";
    echo "  php cli.php migrate version 20250403000001\n";
    echo "  php cli.php seed\n";
    echo "  php cli.php seed Configuracoes\n\n";

    echo "Alternativa direta (sem este script):\n";
    echo "  php index.php tools migrate\n";
    echo "  php index.php tools seed\n";
}

echo "\n==========================================\n";
echo "Operação concluída!\n";
echo "==========================================\n";
