<?php
// Teste detalhado para identificar problema do do_install.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

echo "=== TESTE DE INSTALAÇÃO MAPOS ===\n\n";

// 1. Versão do PHP
echo "1. VERSÃO DO PHP\n";
echo "   PHP: " . phpversion() . "\n";
echo "   PHP 7.0+: " . (version_compare(PHP_VERSION, '7.0', '>=') ? "OK ✓" : "NÃO SUPORTADO ✗") . "\n\n";

// 2. Extensões necessárias
echo "2. EXTENSÕES\n";
$exts = ['mysqli', 'json', 'mbstring', 'openssl', 'gd', 'curl'];
foreach ($exts as $ext) {
    echo "   {$ext}: " . (extension_loaded($ext) ? "OK ✓" : "FALTA ✗") . "\n";
}
echo "\n";

// 3. Verificar settings.json
echo "3. ARQUIVO settings.json\n";
$settings_file = __DIR__ . DIRECTORY_SEPARATOR . 'settings.json';
if (file_exists($settings_file)) {
    echo "   Arquivo existe: SIM\n";
    $contents = file_get_contents($settings_file);
    echo "   Tamanho: " . strlen($contents) . " bytes\n";
    $settings = json_decode($contents, true);
    if ($settings === null) {
        echo "   JSON válido: NÃO\n";
        echo "   Erro JSON: " . json_last_error_msg() . "\n";
    } else {
        echo "   JSON válido: SIM\n";
        echo "   database_file: " . ($settings['database_file'] ?? 'NÃO DEFINIDO') . "\n";
    }
} else {
    echo "   Arquivo existe: NÃO\n";
    echo "   Caminho esperado: {$settings_file}\n";
}
echo "\n";

// 4. Verificar banco.sql
echo "4. ARQUIVO banco.sql\n";
$possible_paths = [
    __DIR__ . '/../database/sql/banco.sql',
    __DIR__ . '/database/sql/banco.sql',
    realpath(__DIR__ . '/..') . '/database/sql/banco.sql',
];
$found = false;
foreach ($possible_paths as $path) {
    echo "   Verificando: {$path}\n";
    if (file_exists($path)) {
        echo "   ENCONTRADO! Tamanho: " . filesize($path) . " bytes\n";
        $found = true;
        break;
    }
}
if (!$found) {
    echo "   ARQUIVO NÃO ENCONTRADO em nenhum caminho!\n";
}
echo "\n";

// 5. Verificar .env.example
echo "5. ARQUIVO .env.example\n";
$env_file = __DIR__ . '/../application/.env.example';
if (file_exists($env_file)) {
    echo "   Arquivo existe: SIM\n";
    echo "   Legível: " . (is_readable($env_file) ? "SIM" : "NÃO") . "\n";
} else {
    echo "   Arquivo existe: NÃO\n";
}
echo "\n";

// 6. Permissões de escrita
echo "6. PERMISSÕES DE ESCRITA\n";
echo "   application/: " . (is_writable(__DIR__ . '/../application') ? "SIM ✓" : "NÃO ✗") . "\n";
echo "   Raiz do projeto: " . (is_writable(__DIR__ . '/..') ? "SIM ✓" : "NÃO ✗") . "\n";
echo "\n";

// 7. Teste de sintaxe do do_install.php
echo "7. SINTAXE DO do_install.php\n";
$output = [];
$return = 0;
exec('php -l ' . escapeshellarg(__DIR__ . '/do_install.php') . ' 2>&1', $output, $return);
if ($return === 0) {
    echo "   Sintaxe: OK ✓\n";
} else {
    echo "   Sintaxe: ERRO ✗\n";
    echo "   " . implode("\n   ", $output) . "\n";
}
echo "\n";

// 8. Tentar carregar o arquivo
echo "8. TESTE DE CARREGAMENTO\n";
try {
    ob_start();
    include_once __DIR__ . '/do_install.php';
    $contents = ob_get_clean();
    echo "   Carregamento: OK (mas pode ter erros de lógica)\n";
    echo "   Saída: " . (empty($contents) ? "Vazia" : "Há conteúdo (pode indicar erro)") . "\n";
} catch (Throwable $e) {
    ob_end_clean();
    echo "   Erro: " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
