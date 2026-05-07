<?php
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
require_once 'system/core/Common.php';
require_once 'system/core/Router.php';

// Simula URI
$_SERVER['REQUEST_URI'] = '/MaposV5/api/v2/autorizacoes/verificar';
$_SERVER['SCRIPT_NAME'] = '/MaposV5/index.php';

$router = load_class('Router', 'core');
$router->_set_routing();

echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "fetch_class: " . $router->fetch_class() . "\n";
echo "fetch_method: " . $router->fetch_method() . "\n";
echo "fetch_directory: " . ($router->fetch_directory() ?? 'NULL') . "\n";
