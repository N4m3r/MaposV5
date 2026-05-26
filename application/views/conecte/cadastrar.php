<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Área do Cliente - <?= $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
    <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url(); ?>assets/img/favicon.png" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap5.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/matrix-style.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/matrix-media.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/fullcalendar.css" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" integrity="sha384-B6nB7GjeyR0Ln2Rf3Znp7Z7r4B4eR3i0Uq8k5+u2C2YJXTHVDbB+m9Z8dDqWlZ4H" crossorigin="anonymous">
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
</head>

<body>
    <style>
        #imgSenha {
            width: 18px;
            cursor: pointer;
        }

        .mb-3.error .form-text {
            display: flex;
        }

        .form-horizontal .mb-3 {
            border-bottom: 1px solid #ffffff;
        }

        .form-horizontal .controls {
            margin-left: 20px;
            padding-bottom: 8px 0;
        }

        .form-horizontal .form-label {
            text-align: left;
            padding-top: 0;
        }

        .nopadding {
            padding: 0 20px !important;
            margin-right: 20px;
        }

        .widget-title h5 {
            padding-bottom: 15px;
            text-align-last: center;
            font-size: 1.5em;
            font-weight: 500;
        }

        

        .widget-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            /* gap:20px; */
        }

        @media (max-width: 480px) {
            .row {
                display: block;
            }
            
            form {
                display: block !important;
            }

            .form-horizontal .form-label {
                margin-bottom: -6px;
            }

            .btn-xs {
                position: initial !important;
            }
            .controls {
                margin-left: 50%;
            }

            .btn {
                position: absolute; /* Reposiciona o botão na posição absoluta */
                width: auto; /* Define a largura automática do botão */
            }
        }

        @media (max-width: 767px) {
            .widget-content {
                display: block; /* Exibe os campos em bloco */
            }

            .mb-3 {
                margin-bottom: 15px; /* Espaçamento entre os campos */
            }

            .controls {
                margin-left: 0; /* Remove o deslocamento à esquerda */
            }

            .mb-3 input[type="text"],
            .mb-3 input[type="password"],
            .mb-3 select {
                width: 100%; /* Faz os inputs ocuparem toda a largura disponível */
            }

            .btn {
                position: static; /* Remove a posição absoluta do botão */
                width: 100%; /* Faz o botão ocupar a largura total do campo */
                margin-top: 10px; /* Adiciona espaçamento acima do botão */
            }

            .form-actions {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .form-actions .col-12 {
                display: flex;
                justify-content: center;
            }
        }
    </style>

    <div class="row" style="width: 100vw;height: 96%;display: grid;align-items: center;justify-content: center">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Cadastre-se no Sistema</h5>
            </div>
            <form action="<?= current_url() ?>" id="formCliente" method="post" class="form-horizontal">
                <div class="widget-content nopadding tab-content">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="mb-3">
                        <label for="nomeCliente" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="nomeCliente" type="text" placeholder="Nome*" name="nomeCliente" value="<?= set_value('nomeCliente') ?>" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <?php if (isset($custom_error) && $custom_error != '') {
                            echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                        } ?>
                        <label for="documento" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="documento" class="cpfcnpj" type="text" placeholder="CPF/CNPJ*" name="documento" value="<?= set_value('documento') ?>" />
                            <button style="top:70px;right:40px;position:absolute" id="buscar_info_cnpj" class="btn btn-xs" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="telefone" type="text" placeholder="Telefone*" name="telefone" value="<?= set_value('telefone') ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="celular" class="form-label"></label>
                        <div class="controls">
                            <input id="celular" type="text" placeholder="Celular" name="celular" value="<?= set_value('celular') ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="email" type="text" placeholder="Email*" name="email" value="<?= set_value('email') ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="senha" type="password" placeholder="Senha*" name="senha" value="<?= set_value('senha') ?>" />
                            <img id="imgSenha" src="<?= base_url() ?>assets/img/eye.svg" alt="">
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="cep" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="cep" type="text" placeholder="CEP*" name="cep" value="<?= set_value('cep') ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="rua" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="rua" type="text" placeholder="Rua*" name="rua" value="<?= set_value('rua') ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="numero" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="numero" type="text" placeholder="Número*" name="numero" value="<?= set_value('numero') ?>" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="complemento" class="form-label"></label>
                        <div class="controls">
                            <input id="complemento" type="text" placeholder="Complemento" name="complemento" value="<?= set_value('complemento') ?>" />
                        </div>
                    </div>
                    <div class="mb-3" class="form-label">
                        <label for="bairro" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="bairro" type="text" placeholder="Bairro*" name="bairro" value="<?= set_value('bairro') ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="cidade" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <input id="cidade" type="text" placeholder="Cidade*" name="cidade" value="<?= set_value('cidade') ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="contato" class="form-label"></label>
                        <div class="controls">
                            <input id="contato" type="text" placeholder="Contato*" name="contato" value="<?= set_value('contato') ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="estado" class="form-label"><span class="required"></span></label>
                        <div class="controls">
                            <select id="estado" name="estado">
                                <option value="">Selecione Seu Estado...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3 col-12" style="background-color:transparent;border:none;padding: 10px;margin-left: 0;margin-bottom: 0;">
                    <div style="display:flex; justify-content: center; flex-direction: column; align-items: center;">
                        <img src="<?= base_url() ?>index.php/mine/captcha" alt="">
                        <div class="controls" style="margin-left: 0;">
                            <input id="captcha" type="text" placeholder="Digite o texto da imagem*" name="captcha" value="" />
                        </div>
                    </div>
                </div>

                <div class="form-actions" style="background-color:transparent;border:none;padding: 10px;margin-bottom: 0">
                    <div class="col-12">
                        <div class="col-6 offset-md-3" style="display:flex;justify-content: center">
                            <button type="submit" class="button btn btn-success btn-large"><span class="button__icon"><i class='bx bx-user-plus'></i></span><span class="button__text2">Cadastrar</span></button>
                            <a href="<?= base_url() ?>index.php/mine" id="" class="button btn btn-warning"><span class="button__icon"><i class='bx bx-undo'></i></span><span class="button__text2">Voltar</span></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/funcoes.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/bootstrap5.bundle.min.js"></script>
    <script type="text/javascript">
        <?php if ($this->session->flashdata('error') != null) { ?>
            console.log('Erro');
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: '<?= $this->session->flashdata('error') ?>',
                showConfirmButton: false,
                timer: 4000
            });
        <?php } ?>

        $(document).ready(function() {
            $.getJSON('<?= base_url() ?>assets/json/estados.json', function(data) {
                for (i in data.estados) {
                    $('#estado').append(new Option(data.estados[i].nome, data.estados[i].sigla));
                    var curState = '<?= set_value('estado'); ?>';
                    if (curState) {
                        $("#estado option[value=" + curState + "]").prop("selected", true);
                    }
                }
            });

            let container = document.querySelector('div');
            let input = document.querySelector('#senha');
            let icon = document.querySelector('#imgSenha');

            icon.addEventListener('click', function() {
                container.classList.toggle('visible');
                if (container.classList.contains('visible')) {
                    icon.src = '<?= base_url() ?>assets/img/eye-off.svg';
                    input.type = 'text';
                } else {
                    icon.src = '<?= base_url() ?>assets/img/eye.svg'
                    input.type = 'password';
                }
            });
            $('#formCliente').validate({
                rules: {
                    nomeCliente: {
                        required: true
                    },
                    documento: {
                        required: true
                    },
                    telefone: {
                        required: true
                    },
                    email: {
                        required: true
                    },
                    senha: {
                        required: true
                    },
                    rua: {
                        required: true
                    },
                    numero: {
                        required: true
                    },
                    bairro: {
                        required: true
                    },
                    cidade: {
                        required: true
                    },
                    estado: {
                        required: true
                    },
                    cep: {
                        required: true
                    },
                    captcha: {
                        required: true
                    }
                },
                messages: {

                    nomeCliente: {
                        required: 'Campo Requerido.'
                    },
                    documento: {
                        required: 'Campo Requerido.'
                    },
                    telefone: {
                        required: 'Campo Requerido.'
                    },
                    email: {
                        required: 'Campo Requerido.'
                    },
                    senha: {
                        required: 'Campo Requerido.'
                    },
                    rua: {
                        required: 'Campo Requerido.'
                    },
                    numero: {
                        required: 'Campo Requerido.'
                    },
                    bairro: {
                        required: 'Campo Requerido.'
                    },
                    cidade: {
                        required: 'Campo Requerido.'
                    },
                    estado: {
                        required: 'Campo Requerido.'
                    },
                    cep: {
                        required: 'Campo Requerido.'
                    },
                    captcha: {
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
        <div id="footer" class="col-12" style="padding: 10px">
            <a class="pecolor" href="https://github.com/RamonSilva20/mapos" target="_blank">
                <?= date('Y') ?> &copy; Ramon Silva - <?= $this->config->item('app_name') ?> - Versão: <?= $this->config->item('app_version'); ?>
            </a>
        </div>
    </div>

</body>

</html>
