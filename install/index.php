<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'InstallLogger.php';

$log = new InstallLogger();
$log->info("=== ACESSO À PÁGINA DE PRÉ-INSTALAÇÃO ===");
$log->logServerEnvironment();

$settings_file = __DIR__ . DIRECTORY_SEPARATOR . 'settings.json';

if (! file_exists($settings_file)) {
    $log->error("settings.json não encontrado", ['path' => $settings_file]);
    exit('Arquivo de configuração não encontrado!');
} else {
    $contents = file_get_contents($settings_file);
    $settings = json_decode($contents, true);
    if ($settings === null) {
        $log->error("settings.json corrompido", ['json_error' => json_last_error_msg()]);
    } else {
        $log->debug("settings.json carregado", $settings);
    }
}

$php_version_success = false;
$allow_url_fopen_success = false;

$php_version_required = '8.2';
$current_php_version = PHP_VERSION;

//check required php version
if (version_compare($current_php_version, $php_version_required) >= 0) {
    $php_version_success = true;
    $log->info("Versão PHP OK", ['atual' => $current_php_version, 'requerida' => $php_version_required]);
} else {
    $log->error("Versão PHP insuficiente", ['atual' => $current_php_version, 'requerida' => $php_version_required]);
}

//check allow_url_fopen
if (ini_get('allow_url_fopen')) {
    $allow_url_fopen_success = true;
    $log->info("allow_url_fopen: ON");
} else {
    $log->error("allow_url_fopen: OFF");
}

//check if all requirement is success
if ($php_version_success && $allow_url_fopen_success) {
    $all_requirement_success = true;
} else {
    $all_requirement_success = false;
}

foreach ($settings['extensions'] as $value) {
    $loaded = extension_loaded($value);
    if (! $loaded) {
        $all_requirement_success = false;
        $log->error("Extensão PHP não carregada", ['extensao' => $value]);
    } else {
        $log->info("Extensão PHP OK", ['extensao' => $value]);
    }
}

$writeableResults = [];
foreach ($settings['writeable_directories'] as $value) {
    $path = '..' . $value;
    $writable = is_writable($path);
    $writeableResults[$value] = $writable;
    if (! $writable) {
        $all_requirement_success = false;
        $log->error("Diretório não gravável", [
            'path' => $path,
            'exists' => file_exists($path),
            'perms' => file_exists($path) ? substr(sprintf('%o', @fileperms($path)), -4) : 'N/A',
        ]);
    } else {
        $log->info("Diretório gravável", ['path' => $path]);
    }
}

$log->logFilePermissions(array_merge(
    ['install_dir' => __DIR__, 'application_dir' => realpath(__DIR__ . '/..') . '/application'],
    array_combine(
        array_map(function($d) { return 'dir' . $d; }, $settings['writeable_directories']),
        array_map(function($d) { return '..' . $d; }, $settings['writeable_directories'])
    )
));

$log->info("Resultado da pré-instalação", [
    'all_requirements_ok' => $all_requirement_success,
    'php_version' => $php_version_success,
    'allow_url_fopen' => $allow_url_fopen_success,
    'extensions' => array_map(function($e) { return extension_loaded($e); }, $settings['extensions']),
    'writeable_dirs' => $writeableResults,
]);

$dashboard_url = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
$dashboard_url = preg_replace('/\/install.*/', '', $dashboard_url); //remove everything after /install
if (! empty($_SERVER['HTTPS'])) {
    $dashboard_url = 'https://' . $dashboard_url;
} else {
    $dashboard_url = 'http://' . $dashboard_url;
}
$dashboard_url = rtrim($dashboard_url, '/') . '/';

$log->debug("Dashboard URL calculada", ['url' => $dashboard_url]);

/*
 * check the .env file
 * if .env already exists, we'll assume that the installation has completed
 */
$is_installed = file_exists('..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '.env');

$installed = null;
if ($is_installed) {
    $installed = true;
    $log->warn("Sistema já está instalado (.env encontrado)");
} else {
    $log->info("Sistema não instalado, exibindo página de pré-instalação");
}

include 'view/index.php';