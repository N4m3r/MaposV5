<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                <h5>Editar Meus Dados</h5>
            </div>
            <div class="widget-content nopadding tab-content">

                <form action="<?php echo current_url(); ?>" id="formCliente" method="post" class="form-horizontal">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="mb-3">
                        <input type="hidden" name="idClientes" id="idClientes" value="<?php echo $result->idClientes; ?>" />
                        <label for="nomeCliente" class="form-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nomeCliente" type="text" name="nomeCliente" value="<?php echo e($result->nomeCliente); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contato" class="form-label">Contato</label>
                        <div class="controls">
                            <input id="contato" type="text" name="contato" value="<?php echo e($result->contato); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <?php if ($custom_error != '') {
                            echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                        } ?>
                        <label for="documento" class="form-label">CPF/CNPJ<span class="required">*</span></label>
                        <div class="controls">
                            <input id="documento" class="cpfcnpjmine" type="text" name="documento" value="<?php echo e($result->documento); ?>" />
                            <button id="buscar_info_cnpj" class="btn btn-xs" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone<span class="required">*</span></label>
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
                        <label for="email" class="form-label">Email<span class="required">*</span></label>
                        <div class="controls">
                            <input id="email" type="text" name="email" value="<?php echo e($result->email); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="controls">
                            <input id="senha" type="password" name="senha" value="" placeholder="Não preencha se não quiser alterar." />
                            <img id="imgSenha" src="<?php echo base_url() ?>assets/img/eye.svg" alt="" style="width: 18px; cursor: pointer;">
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="cep" class="form-label">CEP<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cep" type="text" name="cep" value="<?php echo e($result->cep); ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="rua" class="form-label">Rua<span class="required">*</span></label>
                        <div class="controls">
                            <input id="rua" type="text" name="rua" value="<?php echo e($result->rua); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="numero" class="form-label">Número<span class="required">*</span></label>
                        <div class="controls">
                            <input id="numero" type="text" name="numero" value="<?php echo e($result->numero); ?>" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="complemento" class="form-label">Complmento</label>
                        <div class="controls">
                            <input id="complemento" type="text" name="complemento" value="<?php echo e($result->complemento); ?>" />
                        </div>
                    </div>
                    <div class="mb-3" class="form-label">
                        <label for="bairro" class="form-label">Bairro<span class="required">*</span></label>
                        <div class="controls">
                            <input id="bairro" type="text" name="bairro" value="<?php echo e($result->bairro); ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="cidade" class="form-label">Cidade<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cidade" type="text" name="cidade" value="<?php echo e($result->cidade); ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="estado" class="form-label">Estado<span class="required">*</span></label>
                        <div class="controls">
                            <input id="estado" type="text" name="estado" value="<?php echo e($result->estado); ?>" />
                        </div>
                    </div>


                    <div class="form-actions">
                        <div class="col-12">
                            <div class="col-6 offset-md-3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary">
                                    <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/mine/conta" id="" class="button btn btn-sm btn-warning">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
