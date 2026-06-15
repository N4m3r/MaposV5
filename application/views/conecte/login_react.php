<?php
/**
 * Tela de login React (standalone).
 * Carregada em /index.php/mine/loginReact
 *
 * Bundle JS = assets/frontend/dist/assets/login-*.js (gerado pelo Vite).
 */

$distPath = FCPATH . 'assets/frontend/dist/';
$loginJs = '';
if (is_dir($distPath . 'assets')) {
    $loginFiles = glob($distPath . 'assets/login-*.js');
    if (!empty($loginFiles)) {
        $loginJs = basename($loginFiles[0]);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Login - <?php echo $this->config->item('app_name'); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo $this->config->item('app_name') . ' - ' . $this->config->item('app_subname'); ?>">
    <meta name="csrf-token-name" content="<?= config_item('csrf_token_name'); ?>">
    <meta name="csrf-hash" content="<?= $this->security->get_csrf_hash(); ?>">
    <base href="<?= base_url(); ?>">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap5.min.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/mapos.css?v=<?= filemtime(FCPATH . 'assets/css/mapos.css'); ?>" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/login-react.css?v=<?= filemtime(FCPATH . 'assets/css/login-react.css'); ?>" />
    <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
</head>

<body class="login-react-body">
    <div id="root"></div>
    <noscript>
        <div class="login-react-noscript">
            <p>Este portal requer JavaScript habilitado.</p>
        </div>
    </noscript>

    <script>
        window.MAPOS_CONFIG = {
            csrfName: <?= json_encode($this->security->get_csrf_token_name()); ?>,
            csrfHash: <?= json_encode($this->security->get_csrf_hash()); ?>,
            baseUrl: <?= json_encode(base_url()); ?>,
        };
    </script>

    <?php if ($loginJs): ?>
        <script type="module" src="<?= base_url(); ?>assets/frontend/dist/assets/<?= $loginJs ?>"></script>
    <?php else: ?>
        <div style="padding:40px; text-align:center; font-family:sans-serif;">
            <h2>Bundle de login nao encontrado</h2>
            <p>Execute <code>cd assets/frontend &amp;&amp; npm install &amp;&amp; npm run build</code> para gerar os assets.</p>
        </div>
    <?php endif; ?>
</body>

</html>
