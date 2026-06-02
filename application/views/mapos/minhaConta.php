<style>
    .col-lg-12,
    .col-lg-3,
    .col-lg-9,
    .col-md-12,
    .col-md-3,
    .col-md-9,
    .col-sm-12,
    .col-xs-12 {
        position: relative;
        min-height: 1px;
    }

    .img-user {
      bottom: 15px;
      right: 18px;
      padding: 6px;
      background: #d4d7df;
      color: #333649;
      border-radius: 50%;
      width: 15px;
      height: 15px;
      align-items: center;
      opacity: 0.8;
      position: absolute;
    }

    .pass-user {
        bottom: 27px;
        right: 40px;
        padding: 6px;
        background: transparent;
        border-radius: 50%;
        width: 15px;
        height: 15px;
        align-items: center;
        opacity: 0.7;
        position: absolute;
    }

    .img-user:before {
        opacity: 1;
    }

    .profileMC {
        margin-top: -60px;
    }

    section .profileMC .profile-img {
        border : 4px solid #e6e9f3;
        padding: 0;
        height: 100px;
        width: 100px;
    }

    @media (min-width: 1200px) {
        .col-lg-3 {
            width: 25%;
        }
    }

    @media (min-width: 1200px) {
        .col-lg-12,
        .col-lg-3,
        .col-lg-9 {
            float: left;
            width: 100%;
        }
    }

    @media (min-width: 480px) and (max-width: 992px) {
        .col-md-3 {
            width: 25%;
        }

        .col-lg-9 {
            width: 85%;
        }
    }

    @media (max-width: 480px) {
        .table-sm td {
            padding: 4px 5px;
        }

        .table {
            width: 100%;
        }

        .panel-body {
            padding: 0;
        }
    }
    

</style>
<div class="col-6" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title" style="margin: -10px 0 0">
            <span class="icon">
                <i class="fas fa-user"></i>
            </span>
            <h5>Minha Conta</h5>
        </div>
        <div class="widget-contentMC" style="margin: 20px 0 0;">
            <div id="userMC">
                <div class="row">
                    <div class="col-md-3 col-lg-3 " style="text-align:center;">
                        <section>
                            <div class="profileMC">
                                <div class="profile-img">
                                    <img src="<?= (!$usuario->url_image_user || !is_file(FCPATH . "assets/userImage/" . $usuario->url_image_user)) ?  base_url() . "assets/img/User.png" : base_url() . "assets/userImage/" . $usuario->url_image_user ?>" alt="">
                                    <a href="#modalImageUser" data-bs-toggle="modal" role="button"><span class="tip-top img-user button__icon" title="Alterar Foto"><i class='bx bxs-camera'></i></span></a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <ul class="site-stats">
                        <li class="bg_ls col-12"><strong>Nome:
                                <?= e($usuario->nome) ?></strong></li>
                        <li class="bg_lb col-12" style="margin-left: 0"><strong>Telefone:
                                <?= e($usuario->telefone) ?></strong></li>
                        <li class="bg_lg col-12" style="margin-left: 0"><strong>Email:
                                <?= e($usuario->email) ?></strong></li>
                        <li class="bg_lo col-12" style="margin-left: 0"><strong>Nível:
                                <?= $usuario->permissao; ?></strong></li>
                        <li class="bg_lh col-12" style="margin-left: 0; border-bottom-left-radius: 9px;border-bottom-right-radius: 9px"><strong>Acesso expira em:
                                <?= date('d/m/Y', strtotime($usuario->dataExpiracao)); ?></strong></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="col-6">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-lock"></i>
            </span>
            <h5>Alterar Minha Senha</h5>
        </div>
        <div class="widget-content">
            <div class="row">
                <div class="col-12" style="min-height: 260px">
                    <form id="formSenha" action="<?= site_url('mapos/alterarSenha'); ?>" method="post">

                        <div class="col-12" style="margin-left: 0">
                            <label for="">Senha Atual</label>
                            <input type="password" id="oldSenha" name="oldSenha" class="col-12" />
                        </div>
                        <div class="col-12" style="margin-left: 0">
                            <label for="">Nova Senha</label>
                            <input type="password" id="novaSenha" name="novaSenha" class="col-12" />
                        </div>
                        <div class="col-12" style="margin-left: 0">
                            <label for="">Confirmar Senha</label>
                            <input type="password" name="confirmarSenha" class="col-12" />
                        </div>
                            <button class="button btn btn-primary" style="text-align: center">
                              <span class="button__icon"><i class='bx bx-lock-alt'></i></span><span class="button__text2">Alterar Senha</span></button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="modalImageUser" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?= site_url('mapos/uploadUserImage'); ?>" id="formImageUser" enctype="multipart/form-data" method="post" class="form-horizontal">
        <div class="modal-header">
            <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="">MapOS - Atualizar Imagem do Usuario</h3>
        </div>
        <div class="modal-body">
            <div class="col-12 alert alert-info">Selecione uma nova imagem do usuario. Tamanho indicado (130 X 130).</div>
            <div class="mb-3">
                <label for="userfile" class="form-label"><span class="required">Foto*</span></label>
                <div class="controls">
                    <input type="file" name="userfile" value="" />
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-bs-dismiss="modal" aria-hidden="true" id="btnCancelExcluir"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
        </div>
    </form>
</div>


<script src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $("#formImageUser").validate({
            rules: {
                userfile: {
                    required: true
                }
            },
            messages: {
                userfile: {
                    required: 'Campo Requerido.'
                }
            },

            errorClass: "form-text",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.mb-3').addClass('error');
                $(element).parents('.mb-3').removeClass('success');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.mb-3').removeClass('error');
                $(element).parents('.mb-3').addClass('success');
            }
        });

        $('#formSenha').validate({
            rules: {
                oldSenha: {
                    required: true
                },
                novaSenha: {
                    required: true
                },
                confirmarSenha: {
                    equalTo: "#novaSenha"
                }
            },
            messages: {
                oldSenha: {
                    required: 'Campo Requerido'
                },
                novaSenha: {
                    required: 'Campo Requerido.'
                },
                confirmarSenha: {
                    equalTo: 'As senhas não combinam.'
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
