<?php
/**
 * Loader manual para testes de integracao — bypass do platform_check.
 */
declare(strict_types=1);

$root = dirname(__DIR__, 3);   // C:\Users\...\MaposV5
$appDir = $root . '/application';
$tests = $appDir . '/tests';

// Constantes que CI3 espera antes de carregar arquivos de sistema.
// Devem ser definidas ANTES do bootstrap, que tenta detectar vendor/system.
if (!defined('FCPATH'))    define('FCPATH', $root . '/');
if (!defined('VIEWPATH'))  define('VIEWPATH', $appDir . '/views/');

$psr4 = [
    'Application\\Traits\\' => $appDir . '/traits/',
    'Libraries\\'           => $appDir . '/libraries/',
];

spl_autoload_register(function (string $class) use ($psr4, $appDir) {
    foreach ($psr4 as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $rel = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $rel) . '.php';
            if (file_exists($file)) { require_once $file; return; }
        }
    }
    if (!str_contains($class, '\\')) {
        $ctrl = $appDir . '/controllers/' . $class . '.php';
        if (file_exists($ctrl)) { require_once $ctrl; return; }
        $model = $appDir . '/models/' . $class . '.php';
        if (file_exists($model)) { require_once $model; return; }
    }
});

// Re-define get_instance() APOS bootstrap (que define a sua propria versao).
// Garante que o stub dos testes de integracao seja o usado.
require $tests . '/bootstrap.php';

if (!function_exists('setCI')) {
    function setCI(object $env): void { $GLOBALS['CI_ENV'] = $env; }
}
if (!function_exists('setControllerInstance')) {
    function setControllerInstance(object $ci): void { $GLOBALS['CI_INSTANCE'] = $ci; }
}
if (!function_exists('get_instance_for_test')) {
    // Nao pode redefinir get_instance — mas podemos usar outro nome.
}

$ciPath = BASEPATH . 'core/Controller.php';
if (file_exists($ciPath)) require_once $ciPath;

require $appDir . '/core/MY_Controller.php';
require_once $appDir . '/traits/ApiCrudTrait.php';

require_once $appDir . '/controllers/Clientes.php';
require_once $appDir . '/controllers/Os.php';
require_once $appDir . '/controllers/Vendas.php';

require_once $appDir . '/traits/Os/OsEmailTrait.php';
require_once $appDir . '/traits/Os/OsAutocompleteTrait.php';
require_once $appDir . '/traits/Os/OsAttachmentTrait.php';
require_once $appDir . '/traits/Os/OsItemTrait.php';
require_once $appDir . '/traits/Os/OsValidationTrait.php';
require_once $appDir . '/traits/Os/OsEstoqueTrait.php';
require_once $appDir . '/traits/Os/OsAnotacaoTrait.php';
require_once $appDir . '/traits/LegacyJsonResponseTrait.php';

// Stub para base_url() e is_really_writable() etc. — evita fatal em controllers
if (!function_exists('base_url')) {
    function base_url($uri = '', $protocol = null) { return '/' . ltrim($uri, '/'); }
}

// Substitui a implementacao do bootstrap do get_instance para que retorne
// o controller testavel quando setado, senao o stub.
if (function_exists('get_instance')) {
    // Nao podemos redefinir funcoes nativas — usamos uma constante proxy.
    // Em vez disso, sobrescrevemos via patch de namespace.
    runkit_redefine_function_if_available:
}

// Como runkit nao esta disponivel, usamos uma estrategia diferente:
// o stub de load->model usa o controller atual via closure bound a $this.
// (Implementado no proprio api_detail_integration.php.)

echo "Loader pronto (autoload manual, sem platform_check)\n";
