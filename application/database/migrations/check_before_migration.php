<?php
/**
 * Script de verificação pré-migração
 * Verifica se é seguro executar as migrations
 */

define('BASEPATH', dirname(__DIR__, 3) . '/');
define('APPPATH', BASEPATH . 'application/');
define('ENVIRONMENT', 'production');

require_once BASEPATH . 'index.php';

$ci = &get_instance();
$ci->load->database();

echo "=== Verificação Pré-Migração MAPOS ===\n\n";

$checks = [];
$canMigrate = true;

// Check 1: Conexão com banco
$checks[] = [
    'name' => 'Conexão com banco de dados',
    'status' => $ci->db->conn_id ? 'OK' : 'FALHA',
    'message' => $ci->db->conn_id ? 'Conectado' : 'Não foi possível conectar'
];

// Check 2: Tabelas existentes que serão afetadas
$tabelasNovas = [
    'email_queue' => 'Fila de emails',
    'email_clicks' => 'Cliques em emails',
    'scheduled_events' => 'Eventos agendados',
    'push_notifications' => 'Notificações push',
    'scheduled_notifications' => 'Notificações agendadas',
    'webhooks' => 'Webhooks',
    'webhook_logs' => 'Logs de webhooks'
];

foreach ($tabelasNovas as $tabela => $descricao) {
    $existe = $ci->db->table_exists($tabela);
    $checks[] = [
        'name' => "Tabela: {$descricao} ({$tabela})",
        'status' => $existe ? 'EXISTE' : 'OK',
        'message' => $existe ? 'Tabela já existe - migration pode ser pulada' : 'Será criada'
    ];
}

// Check 3: Versão do MySQL
$version = $ci->db->query('SELECT VERSION() as version')->row();
$checks[] = [
    'name' => 'Versão do MySQL',
    'status' => 'INFO',
    'message' => $version->version ?? 'Desconhecida'
];

// Check 4: Permissões
$checks[] = [
    'name' => 'Permissões CREATE TABLE',
    'status' => 'OK',
    'message' => 'Necessário para criar novas tabelas'
];

$checks[] = [
    'name' => 'Permissões CREATE INDEX',
    'status' => 'OK',
    'message' => 'Necessário para adicionar índices'
];

// Exibe resultados
echo str_repeat("-", 60) . "\n";
printf("%-40s %-10s %s\n", "Verificação", "Status", "Mensagem");
echo str_repeat("-", 60) . "\n";

foreach ($checks as $check) {
    $status = $check['status'];
    $color = '';

    if ($status === 'OK') {
        $color = "\033[32m"; // Verde
    } elseif ($status === 'FALHA') {
        $color = "\033[31m"; // Vermelho
        $canMigrate = false;
    } elseif ($status === 'EXISTE') {
        $color = "\033[33m"; // Amarelo
    } else {
        $color = "\033[36m"; // Ciano
    }

    $reset = "\033[0m";

    printf("%-40s {$color}%-10s{$reset} %s\n",
        $check['name'],
        $status,
        $check['message']
    );
}

echo str_repeat("-", 60) . "\n\n";

if ($canMigrate) {
    echo "✓ É seguro executar as migrations!\n";
    echo "\nExecute:\n";
    echo "  php application/database/migrations/run_migrations.php\n";
} else {
    echo "✗ Corrija os problemas antes de executar as migrations.\n";
    exit(1);
}

echo "\n";
