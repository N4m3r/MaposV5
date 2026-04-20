<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Recuperar Senha - <?php echo $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
    <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/matrix-login.css" />
    <link href="<?= base_url('assets/css/particula.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <script src="<?php echo base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
</head>

<body>
    <div class="main-login">
        <div class="left-login">
            <h1 class="h-one">Recuperar Senha</h1>
            <img src="<?php echo base_url() ?>assets/img/forms-animate.svg" class="left-login-imagec" alt="Map-OS 5.0">
        </div>

        <div id="loginbox">
            <form class="form-vertical" id="formRecuperar" method="post" action="<?php echo current_url(); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="d-flex flex-column">
                    <div class="right-login">
                        <div class="container">
                            <div class="card card-cad">
                                <div class="content">
                                    <div id="newlog">
                                        <div class="icon2">
                                            <img src="<?php echo base_url() ?>assets/img/logo-two.png">
                                        </div>
                                        <div class="title01">
                                            <img src="<?php echo base_url() ?>assets/img/logo-mapos-branco.png">
                                        </div>
                                    </div>
                                    <div id="mcell">Versao: <?= $this->config->item('app_version'); ?></div>

                                    <div class="control-group" style="margin-bottom: 20px;">
                                        <div style="text-align: center; color: #666; font-size: 14px; margin-bottom: 15px;">
                                            <i class='bx bx-envelope' style="font-size: 48px; color: #2d335b; display: block; margin-bottom: 10px;"></i>
                                            Digite seu e-mail cadastrado para receber<br>instrucoes de recuperacao de senha.
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <div class="main_input_box">
                                                <span class="add-on bg_lg"><i class='bx bx-envelope iconU'></i></span>
                                                <input id="email" name="email" type="email" placeholder="Digite seu e-mail" required autofocus />
                                            </div>
                                        </div>
                                    </div>

                                    <button style="margin: 0" type="submit" class="btn btn-info btn-large">
                                        <i class='bx bx-send'></i> Enviar Instrucoes
                                    </button>

                                    <div class="links-uteis">
                                        <a href="<?= site_url('mine') ?>">
                                            <p style="margin:15px 0 0"><i class='bx bx-arrow-back'></i> Voltar para o Login</p>
                                        </a>
                                    </div>

                                    <div class="links-uteis">
                                        <a href="https://github.com/RamonSilva20/mapos">
                                            <p><?= date('Y'); ?> &copy; Ramon Silva</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo base_url() ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
    <script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>

    <?php if ($this->session->flashdata('success') != null) { ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: '<?php echo $this->session->flashdata('success'); ?>',
                showConfirmButton: false,
                timer: 4000
            })
        </script>
    <?php } ?>

    <?php if ($this->session->flashdata('error') != null) { ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: '<?php echo $this->session->flashdata('error'); ?>',
                showConfirmButton: false,
                timer: 4000
            })
        </script>
    <?php } ?>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#formRecuperar").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    email: {
                        required: 'Campo Requerido.',
                        email: 'Insira um e-mail valido'
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                },
                errorClass: "help-inline",
                errorElement: "span",
                highlight: function(element, errorClass, validClass) {
                    $(element).parents('.control-group').addClass('error');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).parents('.control-group').removeClass('error');
                    $(element).parents('.control-group').addClass('success');
                }
            });
        });
    </script>
</body>

</html>
