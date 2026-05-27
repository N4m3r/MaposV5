<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Mine - Área do Cliente - <?php echo $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
    <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo base_url(); ?>assets/img/favicon.png" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap5.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-media.css" />
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet' integrity="sha384-B6nB7GjeyR0Ln2Rf3Znp7Z7r4B4eR3i0Uq8k5+u2C2YJXTHVDbB+m9Z8dDqWlZ4H" crossorigin="anonymous">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
</head>

<body>
    <div class="row" style="width: 100vw;height: 100vh;display: flex;align-items: center;justify-content: center">
        <div class="widget-box" style="align-items: center;padding: 0 15px">
            <div class="widget-title">
                <h5 style="padding-left: 10px">Recuperar Senha do Sistema</h5>
            </div>
            <div class="widget-content nopadding tab-content">

                <form action="<?php echo base_url() . "index.php/mine/gerarTokenResetarSenha" ?>" id="formCliente" method="post" class="form-horizontal">

                    <div class="mb-3" style="display: flex;margin-bottom: 7pxpx;grid-column-gap: 5px;justify-content: space-evenly;border-bottom: 0px">
                        <label style="width: auto" for="email" class="form-label">Email<span class="required">*</span></label>
                        <div class="controls" style="margin: 0">
                            <input id="email" type="text" name="email" value="" />
                        </div>
                    </div>

                    <div class="form-actions" style="background-color:transparent;border:none;padding: 10px;margin-top: 15px">
                        <div class="col-12">
                            <div class="col-6 offset-md-3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-success btn-large"><span class="button__icon"><i class='bx bx-mail-send'></i></span><span class="button__text2">Enviar</span></button>
                                <a href="<?php echo base_url() ?>index.php/mine" id="" class="button btn btn-warning"><span class="button__icon"><i class='bx bx-lock-alt'></i></span><span class="button__text2">Acessar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
            $('#formCliente').validate({
                rules: {
                    email: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: 'Campo Requerido.'
                    }
                },

                errorClass: "form-text",
                errorElement: "span",
                highlight: function(element, errorClass, validClass) {
                    $(element).parents('.mb-3').addClass('error');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).parents('.mb-3').removeClass('error');
                    $(element).parents('.mb-3').addClass('success');
                }
            });
        });
    </script>


    <!--Footer-part-->
    <div class="row">
        <div id="footer" class="col-12" style="padding: 10px"> <a class="pecolor" href="https://github.com/RamonSilva20/mapos" target="_blank">
                <?= date('Y') ?> &copy; Ramon Silva - <?php echo $this->config->item('app_name') ?> - Versão: <?= $this->config->item('app_version'); ?>
            </a></div>
    </div>

    <!-- javascript
================================================== -->

    <script src="<?php echo base_url(); ?>assets/js/bootstrap5.bundle.min.js"></script>
</body>

</html>
