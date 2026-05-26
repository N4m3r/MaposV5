<?php
// Diagnostico Map-OS V5 - Remover apos uso!
// Acesse: https://jj-ferreiras.com.br/mapos3/diag.php

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNOSTICO MAP-OS V5 ===\n\n";

// 1. PHP Version
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP SAPI: " . PHP_SAPI . "\n\n";

// 2. Required Extensions
$required = ['mysqli', 'mbstring', 'curl', 'gd', 'json', 'session', 'openssl'];
echo "Extensoes PHP:\n";
foreach ($required as $ext) {
    echo "  $ext: " . (extension_loaded($ext) ? 'OK' : 'FALTANDO!') . "\n";
}
echo "\n";

// 3. Composer Autoload
$autoloadPath = __DIR__ . '/application/vendor/autoload.php';
echo "Composer autoload: " . (file_exists($autoloadPath) ? 'EXISTS' : 'FALTANDO!') . "\n";
if (file_exists($autoloadPath)) {
    try {
        require_once $autoloadPath;
        echo "  Carregado: OK\n";
    } catch (Exception $e) {
        echo "  ERRO: " . $e->getMessage() . "\n";
    }
}
echo "\n";

// 4. .env File
$envPath = __DIR__ . '/application/.env';
echo ".env file: " . (file_exists($envPath) ? 'EXISTS' : 'FALTANDO!') . "\n\n";

// 5. System Directory
$systemPath = __DIR__ . '/application/vendor/codeigniter/framework/system/';
echo "CI System dir: " . (is_dir($systemPath) ? 'EXISTS' : 'FALTANDO!') . "\n";
echo "CodeIgniter.php: " . (file_exists($systemPath . 'core/CodeIgniter.php') ? 'EXISTS' : 'FALTANDO!') . "\n\n";

// 6. Critical Files
$files = [
    'Permission.php' => __DIR__ . '/application/libraries/Permission.php',
    'MY_Controller.php' => __DIR__ . '/application/core/MY_Controller.php',
    'Api_docs.php' => __DIR__ . '/application/controllers/Api_docs.php',
    'routes_api.php' => __DIR__ . '/application/config/routes_api.php',
    'BaseController.php' => __DIR__ . '/application/controllers/api/v2/BaseController.php',
];
echo "Arquivos criticos:\n";
foreach ($files as $name => $path) {
    echo "  $name: " . (file_exists($path) ? 'EXISTS' : 'FALTANDO!') . "\n";
}
echo "\n";

// 7. Syntax check critical files
echo "Syntax check (php -l):\n";
$criticalFiles = glob(__DIR__ . '/application/controllers/*.php');
$criticalFiles = array_merge($criticalFiles, glob(__DIR__ . '/application/libraries/*.php'));
$criticalFiles = array_merge($criticalFiles, glob(__DIR__ . '/application/core/*.php'));
$criticalFiles = array_merge($criticalFiles, glob(__DIR__ . '/application/config/*.php'));

foreach ($criticalFiles as $file) {
    $output = [];
    $result = null;
    exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $result);
    $output = implode("\n", $output);
    if (strpos($output, 'No syntax errors') === false) {
        echo "  ERRO: " . basename($file) . " - " . trim($output) . "\n";
    }
}
echo "  Syntax check completo.\n\n";

// 8. Database Connection
echo "Conexao MySQL:\n";
try {
    $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';
    preg_match('/DB_HOSTNAME="([^"]+)"/', $envContent, $host);
    preg_match('/DB_USERNAME="([^"]+)"/', $envContent, $user);
    preg_match('/DB_PASSWORD="([^"]+)"/', $envContent, $pass);
    preg_match('/DB_DATABASE="([^"]+)"/', $envContent, $db);

    $host = $host[1] ?? 'N/A';
    $user = $user[1] ?? 'N/A';
    $db = $db[1] ?? 'N/A';

    if (function_exists('mysqli_connect')) {
        $conn = @mysqli_connect($host, $user, $pass ?? '', $db);
        if ($conn) {
            echo "  Conexao: OK\n";
            echo "  Server: " . mysqli_get_server_info($conn) . "\n";

            // Check ci_sessions table
            $result = mysqli_query($conn, "SHOW TABLES LIKE 'ci_sessions'");
            echo "  ci_sessions: " . (mysqli_num_rows($result) > 0 ? 'EXISTS' : 'FALTANDO!') . "\n";

            // Check permissoes table
            $result = mysqli_query($conn, "SHOW TABLES LIKE 'permissoes'");
            echo "  permissoes: " . (mysqli_num_rows($result) > 0 ? 'EXISTS' : 'FALTANDO!') . "\n";

            // Check admin permissions
            $result = mysqli_query($conn, "SELECT idPermissao, nome, LEFT(permissoes, 100) as perms_preview FROM permissoes WHERE idPermissao = 1");
            if ($row = mysqli_fetch_assoc($result)) {
                echo "  Admin perm id: " . $row['idPermissao'] . "\n";
                echo "  Admin nome: " . $row['nome'] . "\n";
                echo "  Admin perms preview: " . $row['perms_preview'] . "...\n";

                // Check if serialized or JSON
                $rawResult = mysqli_query($conn, "SELECT permissoes FROM permissoes WHERE idPermissao = 1");
                $rawRow = mysqli_fetch_assoc($rawResult);
                $raw = $rawRow['permissoes'];
                $jsonDecoded = json_decode($raw, true);
                $isSerialized = (@unserialize($raw) !== false);
                echo "  Formato permissoes: " . ($jsonDecoded !== null ? 'JSON' : ($isSerialized ? 'SERIALIZED (legado)' : 'DESCONHECIDO!')) . "\n";
            }

            mysqli_close($conn);
        } else {
            echo "  ERRO: " . mysqli_connect_error() . "\n";
        }
    } else {
        echo "  mysqli extension nao carregada!\n";
    }
} catch (Exception $e) {
    echo "  ERRO: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DIAGNOSTICO ===\n";