<?php
/**
 * Verificação pós-instalação
 * Confirma que todas as tabelas foram criadas corretamente
 */

define('BASEPATH', dirname(__DIR__, 3) . '/');
define('APPPATH', BASEPATH . 'application/');
define('ENVIRONMENT', 'production');

require_once BASEPATH . 'index.php';

$ci = &get_instance();
$ci->load->database();

echo "=== Verificação Pós-Instalação MAPOS 5.0 ===\n\n";

$tabelasObrigatorias = [
    'email_queue' => 'Fila de emails',
    'email_clicks' => 'Tracking de cliques',
    'scheduled_events' => 'Eventos agendados',
    'push_notifications' => 'Notificações push',
    'scheduled_notifications' => 'Notificações agendadas',
    'webhooks' => 'Webhooks',
    'webhook_logs' => 'Logs de webhooks'
];

$indicesEsperados = [
    'idx_os_cliente_status',
    'idx_vendas_cliente',
    'idx_lancamentos_data',
    'idx_produtos_codigo'
];

$allOk = true;

// Verifica tabelas
echo "Tabelas:\n";
echo str_repeat("-", 50) . "\n";

foreach ($tabelasObrigatorias as $tabela => $descricao) {
    $existe = $ci->db->table_exists($tabela);
    $status = $existe ? '✓' : '✗';
    $msg = $existe ? 'OK' : 'FALTA';

    if (!$existe) $allOk = false;

    echo "{$status} {$descricao} ({$tabela}): {$msg}\n";
}

// Verifica índices
echo "\nÍndices:\n";
echo str_repeat("-", 50) . "\n";

foreach ($indicesEsperados as $indice) {
    // Verifica se índice existe
    $existe = verificarIndiceExiste($ci->db, $indice);
    $status = $existe ? '✓' : '○';
    $msg = $existe ? 'OK' : 'Opcional (pode ser criado depois)';

    echo "{$status} {$indice}: {$msg}\n";
}

// Verifica conectividade
echo "\nConectividade:\n";
echo str_repeat("-", 50) . "\n";

// Testa se o banco está respondendo
$query = $ci->db->query("SELECT 1 as test")->row();
$status = $query ? '✓' : '✗';
echo "{$status} Conexão MySQL: " . ($query ? 'OK' : 'FALHA') . "\n";

// Testa tabela os
$os = $ci->db->query("SELECT COUNT(*) as total FROM os")->row();
$status = $os ? '✓' : '✗';
echo "{$status} Tabela 'os': " . ($os ? $os->total . ' registros' : 'FALHA') . "\n";

// Resumo
echo "\n" . str_repeat("=", 50) . "\n";
if ($allOk) {
    echo "✓ TODAS AS TABELAS FORAM CRIADAS COM SUCESSO!\n";
    echo "O sistema está pronto para uso.\n";
} else {
    echo "✗ ALGUMAS TABELAS ESTÃO FALTANDO\n";
    echo "Execute novamente:\n";
    echo "  php application/database/migrations/run_migrations.php\n";
}
echo str_repeat("=", 50) . "\n";

function verificarIndiceExiste($db, $indice) {
    try {
        $result = $db->query("SHOW INDEX FROM os WHERE Key_name = '{$indice}'")->row();
        return $result !== null;
    } catch (Exception $e) {
        return false;
    }
}
