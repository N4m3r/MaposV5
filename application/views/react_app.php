<?php
/**
 * Entry point da aplicacao React (CoreUI).
 * Carregado pelo controller App quando o usuario acessa /app/*.
 *
 * O bundle eh gerado por Vite em assets/frontend/dist/assets/
 * Este arquivo:
 *   1. Injeta window.APP_CONFIG com user/permissoes/tema
 *   2. Carrega o mapos.css (preserva data-theme)
 *   3. Carrega TODOS os chunks JS gerados (entry + manualChunks)
 *   4. Carrega o CSS do Vite
 */

$appConfig = isset($app_config) ? $app_config : [
    'baseUrl'    => base_url() . 'index.php/',
    'userName'   => 'Usuario',
    'userEmail'  => '',
    'userAvatar' => null,
    'permissions' => [],
    'theme' => 'white',
];

$distPath = FCPATH . 'assets/frontend/dist/';
$entryJs     = '';
$entryCss    = '';
$vendorJs    = [];
$appTheme    = $appConfig['theme'] ?? 'white';

if (is_dir($distPath . 'assets')) {
    // Entry JS = main-*.js (o unico que monta a app)
    $jsFiles = glob($distPath . 'assets/main-*.js');
    if (!empty($jsFiles)) {
        $entryJs = basename($jsFiles[0]);
    }

    // Login JS = login-*.js (bundle separado da tela de login)
    $loginFiles = glob($distPath . 'assets/login-*.js');
    $loginJs = !empty($loginFiles) ? basename($loginFiles[0]) : null;

    // Vendor chunks = tudo que NAO eh main-/login-/Toast (manualChunks)
    foreach (glob($distPath . 'assets/*-*.js') as $f) {
        $bn = basename($f);
        if ($bn !== $entryJs && $bn !== $loginJs && strpos($bn, 'Toast-') !== 0) {
            $vendorJs[] = $bn;
        }
    }

    // CSS
    $cssFiles = glob($distPath . 'assets/main-*.css');
    if (!empty($cssFiles)) {
        $entryCss = basename($cssFiles[0]);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token-name" content="<?= config_item('csrf_token_name') ?>">
    <meta name="csrf-cookie-name" content="<?= config_item('csrf_cookie_name') ?>">
    <title>Mapos OS v5</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png" />

    <!-- mapos.css consolidado (mantido para tema via data-theme) -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/mapos.css?v=<?= @filemtime(FCPATH.'assets/css/mapos.css') ?>" />

    <!-- Bundle CSS do Vite (CoreUI ja vem incluido) -->
    <?php if ($entryCss): ?>
    <link rel="stylesheet" href="<?= base_url() ?>assets/frontend/dist/assets/<?= $entryCss ?>" />
    <?php endif; ?>

    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Injeta config pro React -->
    <script>
        window.APP_CONFIG = <?= json_encode($appConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
</head>
<body data-theme="<?= $appTheme ?>">
    <div id="root"></div>

    <?php if ($entryJs): ?>
        <!-- Vendor chunks (preload paralelo) -->
        <?php foreach ($vendorJs as $vjs): ?>
        <link rel="modulepreload" href="<?= base_url() ?>assets/frontend/dist/assets/<?= $vjs ?>" />
        <?php endforeach; ?>

        <!-- Entry (monta a app) -->
        <script type="module" src="<?= base_url() ?>assets/frontend/dist/assets/<?= $entryJs ?>"></script>
    <?php else: ?>
        <div style="padding:40px; text-align:center; font-family:sans-serif;">
            <h2>Bundle nao encontrado</h2>
            <p>Execute <code>cd assets/frontend &amp;&amp; npm install &amp;&amp; npm run build</code> para gerar os assets.</p>
        </div>
    <?php endif; ?>
</body>
</html>
