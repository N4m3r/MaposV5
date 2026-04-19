<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Área do Cliente - <?php echo $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
    <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-media.css" />
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.css" />
    <link href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/sweetalert.min.js"></script>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
</head>

<body>
    <!--Header-part-->
    <div id="header">
        <h1><a href="dashboard.html"><?php echo $this->config->item('app_name'); ?></a></h1>
    </div>
    <!--close-Header-part-->

    <?php
// Carregar helper de permissões
$CI =& get_instance();
$CI->load->helper('cliente_permissions');

// Verificar permissões do usuário
$podeVerOS = clienteHasPermission('visualizar_os');
$podeVerCompras = clienteHasPermission('visualizar_compras');
$podeVerCobrancas = clienteHasPermission('visualizar_cobrancas');
$podeVerBoletos = clienteHasPermission('visualizar_boletos');
$podeVerNotasFiscais = clienteHasPermission('visualizar_notas_fiscais');
$podeVerObras = clienteHasPermission('visualizar_obras');
$podeEditarPerfil = clienteHasPermission('editar_perfil');
?>

<!--top-Header-menu-->
    <div class="navebarn" style="margin-top: -60px;height: 25px;margin-bottom: 15px">
        <div id="user-nav" class="navbar navbar-inverse">
            <ul class="nav">
                <?php
                // Suporte para ambos os sistemas de login
                $nomeUsuario = $this->session->userdata('usuario_cliente_nome') ?: $this->session->userdata('nome') ?: 'Usuário';
                $tipoAcesso = $this->session->userdata('tipo_acesso');
                $linkSair = ($tipoAcesso == 'usuario_cliente') ? 'mine/sair_usuario' : 'mine/sair';
                ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class='bx bx-user-circle iconN1'></i> <?= htmlspecialchars($nomeUsuario) ?> </a>
                    <ul class="dropdown-menu">
                        <?php if ($podeEditarPerfil): ?>
                        <li class=""><a title="Meu Perfil" href="<?php echo base_url() ?>index.php/mine/conta"><i class="fas fa-user"></i> <span class="text">Meu Perfil</span></a></li>
                        <li class="divider"></li>
                        <?php endif; ?>
                        <li class=""><a title="Sair" href="<?php echo base_url() ?>index.php/<?= $linkSair ?>"><i class="fas fa-sign-out-alt"></i> <span class="text">Sair</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <nav id="sidebar">
        <div id="newlog">
            <div class="icon2">
                <img src="<?php echo base_url() ?>assets/img/logo-two.png">
            </div>
            <div class="title1">
                <img src="<?= base_url() ?>assets/img/logo-mapos-branco.png">
            </div>
        </div>
        <a href="#" class="visible-phone">
            <div class="mode">
                <div class="moon-menu">
                    <i class='bx bx-chevron-right iconX open-2'></i>
                    <i class='bx bx-chevron-left iconX close-2'></i>
                </div>
            </div>
        </a>

        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links" style="position: relative;">
                    <li class="<?php if (isset($menuPainel)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/painel"><i class='bx bx-home-alt iconX'></i> <span class="title">Painel</span></a></li>

                    <?php if ($podeEditarPerfil): ?>
                    <li class="<?php if (isset($menuConta)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/conta"><i class="bx bx-user-circle iconX"></i> <span class="title">Minha Conta</span></a></li>
                    <?php endif; ?>

                    <?php if ($podeVerOS): ?>
                    <li class="<?php if (isset($menuOs)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/os"><i class='bx bx-spreadsheet iconX'></i> <span class="title">Ordens de Serviço</span></a></li>
                    <?php endif; ?>

                    <?php if ($podeVerCompras): ?>
                    <li class="<?php if (isset($menuVendas)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/compras"><i class='bx bx-cart-alt iconX'></i> <span class="title">Compras</span></a></li>
                    <?php endif; ?>

                    <?php if ($podeVerCobrancas): ?>
                    <li class="<?php if (isset($menuCobrancas)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/cobrancas"><i class='bx bx-credit-card-front iconX'></i> <span class="title">Cobranças</span></a></li>
                    <?php endif; ?>

                    <?php if ($podeVerBoletos): ?>
                    <li class="<?php if (isset($menuBoletos)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/boletos"><i class='bx bx-barcode iconX'></i> <span class="title">Boletos</span></a></li>
                    <?php endif; ?>

                    <?php if ($podeVerNotasFiscais): ?>
                    <li class="<?php if (isset($menuNotas)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/notasfiscais"><i class='bx bx-receipt iconX'></i> <span class="title">Notas Fiscais</span></a></li>
                    <?php endif; ?>

                    <?php if ($podeVerObras): ?>
                    <li class="<?php if (isset($menuObras)) { echo 'active'; } ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/obras"><i class='bx bx-building-house iconX'></i> <span class="title">Obras</span></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="botton-content">
                <li class="">
                    <a class="tip-bottom" title="" href="<?= site_url('login/sair'); ?>">
                        <i class='bx bx-log-out-circle iconX'></i>
                        <span class="title">Sair</span></a>
                </li>
            </div>

        </div>
    </nav>

    <div style="background: #f3f4f6" id="content">
        <div class="content-header" id="content-header">
            <div id="breadcrumb"><a href="<?php echo base_url(); ?>index.php/mine/painel" title="Painel" class="tip-bottom"><i class="fas fa-home"></i> Painel</a></div>
        </div>

        <div class="container-fluid">
            <div class="row-fluid">

                <div class="span12">
                    <?php if ($var = $this->session->flashdata('success')) : ?><script>
                            swal("Sucesso!", "<?php echo str_replace('"', '', $var); ?>", "success");
                        </script><?php endif; ?>
                    <?php if ($var = $this->session->flashdata('error')) : ?><script>
                            swal("Falha!", "<?php echo str_replace('"', '', $var); ?>", "error");
                        </script><?php endif; ?>
                    <?php if (isset($output)) {
                        $this->load->view($output);
                    } ?>

                </div>
            </div>

        </div>
    </div>
    <!--Footer-part-->
    <div class="row-fluid">
        <div id="footer" class="span12">
            <?= date('Y') ?> &copy;
            <?php echo $this->config->item('app_name'); ?> - Versão:
            <?php echo $this->config->item('app_version'); ?>
        </div>
    </div>

    <!-- javascript
================================================== -->

    <script src="<?= base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/matrix.js"></script>

    <style>
        /* Fix para modal-backdrop bloqueando a tela */
        .modal-backdrop {
            display: none !important;
        }
        body.modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }
        /* Remove overlay do SweetAlert se ficar preso */
        .swal-overlay, .swal2-container {
            z-index: 99999 !important;
        }
    </style>

    <script>
        // Remove qualquer modal-backdrop ou overlay que possa estar bloqueando a tela
        $(document).ready(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', 'auto');
            $('.swal-overlay, .swal2-container').remove();
        });

        // Corrige problema de overlay do SweetAlert após redirecionamentos
        $(window).on('load', function() {
            setTimeout(function() {
                $('.modal-backdrop').fadeOut(300, function() {
                    $(this).remove();
                });
                $('body').css('padding-right', '').removeClass('modal-open');
            }, 500);
        });
    </script>
</body>

</html>
