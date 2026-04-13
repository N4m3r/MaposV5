<?php
// Arquivo para testar compatibilidade do PHP
header('Content-Type: application/json');

$errors = [];
$warnings = [];

// Verificar versão do PHP
if (version_compare(PHP_VERSION, '7.0', '<')) {
    $errors[] = 'PHP versão ' . PHP_VERSION . ' não suportada. Requer PHP 7.0+';
} else {
    $warnings[] = 'PHP versão: ' . PHP_VERSION . ' (OK)';
}

// Verificar extensões necessárias
$required_extensions = [
    'mysqli' => 'Conexão com MySQL',
    'json' => 'Manipulação de JSON',
    'mbstring' => 'Strings multibyte',
    'openssl' => 'Criptografia (chaves JWT)',
    'gd' => 'Manipulação de imagens',
    'curl' => 'Requisições HTTP',
    'zip' => 'Compactação de arquivos'
];

foreach ($required_extensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        $warnings[] = "Extensão {$ext}: OK ({$desc})";
    } else {
        $errors[] = "Extensão {$ext} não encontrada ({$desc})";
    }
}

// Verificar funções críticas
$critical_functions = [
    'json_encode',
    'json_decode',
    'mysqli_connect',
    'password_hash',
    'file_get_contents',
    'file_put_contents',
    'fopen',
    'fwrite',
    'fclose'
];

foreach ($critical_functions as $func) {
    if (function_exists($func)) {
        // OK, não precisa reportar
    } else {
        $errors[] = "Função crítica não disponível: {$func}";
    }
}

// Verificar permissões de escrita
$test_dirs = [
    '../application' => 'Diretório application/',
    '../updates' => 'Diretório updates/',
];

foreach ($test_dirs as $dir => $desc) {
    if (is_writable($dir)) {
        $warnings[] = "{$desc} tem permissão de escrita";
    } else {
        $errors[] = "{$desc} NÃO tem permissão de escrita";
    }
}

// Verificar existência de arquivos necessários
$required_files = [
    '../application/.env.example' => 'Template do arquivo .env',
    '../database/sql/banco.sql' => 'Script do banco de dados'
];

foreach ($required_files as $file => $desc) {
    if (file_exists($file)) {
        $warnings[] = "{$desc}: encontrado";
    } else {
        $errors[] = "{$desc}: NÃO encontrado ({$file})";
    }
}

// Resultado
$result = [
    'success' => empty($errors),
    'php_version' => PHP_VERSION,
    'errors' => $errors,
    'info' => $warnings
];

echo json_encode($result, JSON_PRETTY_PRINT);
