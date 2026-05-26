<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
<script>
    window.emitente = <?php echo json_encode(isset($emitente) ? $emitente : null); ?>;
</script>
<style>
    #imgSenha {
        width: 18px;
        cursor: pointer;
    }

    /* Hiding the checkbox, but allowing it to be focused */
    .badgebox {
        opacity: 0;
    }

    .badgebox+.badge {
        /* Move the check mark away when unchecked */
        text-indent: -999999px;
        /* Makes the badge's width stay the same checked and unchecked */
        width: 27px;
    }

    .badgebox:focus+.badge {
        /* Set something to make the badge looks focused */
        /* This really depends on the application, in my case it was: */
        /* Adding a light border */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }

    .badgebox:checked+.badge {
        /* Move the check mark back when checked */
        text-indent: 0;
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
        padding-top: 15px;
    }

    .nopadding {
        padding: 0 20px !important;
        margin-right: 20px;
    }

    .widget-title h5 {
        padding-bottom: 30px;
        text-align-last: left;
        font-size: 2em;
        font-weight: 500;
    }

    @media (max-width: 480px) {
        form {
            display: contents !important;
        }

        .form-horizontal .form-label {
            margin-bottom: -6px;
        }

        .btn-xs {
            position: initial !important;
        }
    }
</style>
<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                <h5>Editar Cliente</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formCliente" method="post" class="form-horizontal">
                <div class="widget-content nopadding tab-content">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="documento" class="form-label">CPF/CNPJ</label>
                            <div class="controls">
                                <input id="documento" class="cpfcnpj" type="text" name="documento" value="<?php echo e($result->documento); ?>" />
                                <button id="buscar_info_cnpj" class="btn btn-xs" type="button">Buscar(CNPJ)</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="inscricao_municipal" class="form-label">Inscrição Municipal</label>
                            <div class="controls">
                                <input id="inscricao_municipal" type="text" name="inscricao_municipal" value="<?php echo e($result->inscricao_municipal); ?>" placeholder="IM" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                            <div class="controls">
                                <input id="inscricao_estadual" type="text" name="inscricao_estadual" value="<?php echo e($result->inscricao_estadual); ?>" placeholder="IE" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <?php echo form_hidden('idClientes', $result->idClientes) ?>
                            <label for="nomeCliente" class="form-label">Nome/Razão Social<span class="required">*</span></label>
                            <div class="controls">
                                <input id="nomeCliente" type="text" name="nomeCliente" value="<?php echo e($result->nomeCliente); ?>" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contato" class="form-label">Contato:</label>
                            <div class="controls">
                                <input class="contato" type="text" name="contato" value="<?php echo e($result->contato); ?>" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <div class="controls">
                                <input id="telefone" type="text" name="telefone" value="<?php echo e($result->telefone); ?>" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="celular" class="form-label">Celular</label>
                            <div class="controls">
                                <input id="celular" type="text" name="celular" value="<?php echo e($result->celular); ?>" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="controls">
                                <input id="email" type="text" name="email" value="<?php echo e($result->email); ?>" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <div class="controls">
                                <input id="senha" type="password" name="senha" value="" placeholder="Não preencha se não quiser alterar." />
                                <img id="imgSenha" src="<?php echo base_url() ?>assets/img/eye.svg" alt="">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Cliente</label>
                            <div class="controls">
                                <label for="fornecedor" class="btn btn-default">Fornecedor
                                    <input type="checkbox" id="fornecedor" name="fornecedor" class="badgebox" value="1" <?= ($result->fornecedor == 1) ? 'checked' : '' ?>>
                                    <span class="badge">&check;</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-3" class="form-label">
                            <label for="cep" class="form-label">CEP</label>
                            <div class="controls">
                                <input id="cep" type="text" name="cep" value="<?php echo e($result->cep); ?>" />
                            </div>
                        </div>
                        <div class="mb-3" class="form-label">
                            <label for="rua" class="form-label">Rua</label>
                            <div class="controls">
                                <input id="rua" type="text" name="rua" value="<?php echo e($result->rua); ?>" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <div class="controls">
                                <input id="numero" type="text" name="numero" value="<?php echo e($result->numero); ?>" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <div class="controls">
                                <input id="complemento" type="text" name="complemento" value="<?php echo e($result->complemento); ?>" />
                            </div>
                        </div>
                        <div class="mb-3" class="form-label">
                            <label for="bairro" class="form-label">Bairro</label>
                            <div class="controls">
                                <input id="bairro" type="text" name="bairro" value="<?php echo e($result->bairro); ?>" />
                            </div>
                        </div>
                        <div class="mb-3" class="form-label">
                            <label for="cidade" class="form-label">Cidade</label>
                            <div class="controls">
                                <input id="cidade" type="text" name="cidade" value="<?php echo e($result->cidade); ?>" />
                            </div>
                        </div>
                        <div class="mb-3" class="form-label">
                            <label for="estado" class="form-label">Estado</label>
                            <div class="controls">
                                <select id="estado" name="estado" class="">
                                    <option value="">Selecione...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="col-12">
                        <div class="col-6 offset-md-3" style="display:flex;justify-content: center">
                            <button type="submit" class="button btn btn-primary" style="max-width: 160px">
                                <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                            <a title="Voltar" class="button btn btn-warning" href="<?php echo site_url() ?>/clientes"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        let container = document.querySelector('div');
        let input = document.querySelector('#senha');
        let icon = document.querySelector('#imgSenha');

        icon.addEventListener('click', function() {
            container.classList.toggle('visible');
            if (container.classList.contains('visible')) {
                icon.src = '<?php echo base_url() ?>assets/img/eye-off.svg';
                input.type = 'text';
            } else {
                icon.src = '<?php echo base_url() ?>assets/img/eye.svg'
                input.type = 'password';
            }
        });

        $.getJSON('<?php echo base_url() ?>assets/json/estados.json', function(data) {
            for (i in data.estados) {
                $('#estado').append(new Option(data.estados[i].nome, data.estados[i].sigla));
            }
            var curState = <?php echo json_encode($result->estado, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            if (curState) {
                $("#estado option[value=" + curState + "]").prop("selected", true);
            }

        });
        $('#formCliente').validate({
            rules: {
                nomeCliente: {
                    required: true
                },
            },
            messages: {
                nomeCliente: {
                    required: 'Campo Requerido.'
                },
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
