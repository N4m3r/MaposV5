<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                <h5>Editar Usuário</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . e($custom_error) . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formUsuario" method="post" class="form-horizontal">
                    <div class="mb-3">
                        <?php echo form_hidden('idUsuarios', $result->idUsuarios) ?>
                        <label for="nome" class="form-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo e($result->nome); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rg" class="form-label">RG<span class="required">*</span></label>
                        <div class="controls">
                            <input id="rg" type="text" name="rg" value="<?php echo e($result->rg); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF<span class="required">*</span></label>
                        <div class="controls">
                            <input class="cpfUser" type="text" name="cpf" value="<?php echo e($result->cpf); ?>"/>
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
                            <i class="icon-exclamation-sign tip-top" title="Se não quiser alterar a senha, não preencha esse campo."></i>
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="cep" class="form-label">CEP<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cep" type="text" name="cep" value="<?php echo e($result->cep); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rua" class="form-label">Rua<span class="required">*</span></label>
                        <div class="controls">
                            <input id="rua" type="text" name="rua" value="<?php echo e($result->rua); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="numero" class="form-label">Numero<span class="required">*</span></label>
                        <div class="controls">
                            <input id="numero" type="text" name="numero" value="<?php echo e($result->numero); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bairro" class="form-label">Bairro<span class="required">*</span></label>
                        <div class="controls">
                            <input id="bairro" type="text" name="bairro" value="<?php echo e($result->bairro); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cidade" class="form-label">Cidade<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cidade" type="text" name="cidade" value="<?php echo e($result->cidade); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado<span class="required">*</span></label>
                        <div class="controls">
                            <input id="estado" type="text" name="estado" value="<?php echo e($result->estado); ?>" />
                        </div>
                    </div>

                    <!--DATA-->
                    <div class="mb-3">
                        <label for="dataExpiracao" class="form-label">Expira em<span class="required">*</span></label>
                        <div class="controls">
                            <input id="dataExpiracao" type="date" name="dataExpiracao" value="<?php echo e($result->dataExpiracao); ?>" />
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Situação*</label>
                        <div class="controls">
                            <select name="situacao" id="situacao">
                                <?php if ($result->situacao == 1) {
                                    $ativo = 'selected';
                                    $inativo = '';
                                } else {
                                    $ativo = '';
                                    $inativo = 'selected';
                                } ?>
                                <option value="1" <?php echo $ativo; ?>>Ativo</option>
                                <option value="0" <?php echo $inativo; ?>>Inativo</option>
                            </select>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Permissões<span class="required">*</span></label>
                        <div class="controls">
                            <select name="permissoes_id" id="permissoes_id">
                                <?php foreach ($permissoes as $p) {
                                    if ($p->idPermissao == $result->permissoes_id) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    echo '<option value="' . e($p->idPermissao) . '"' . $selected . '>' . e($p->nome) . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="col-12">
                            <div class="col-6 offset-md-3" style="display:flex">
                                <button type="submit" class="button btn btn-primary">
                                  <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/usuarios" id="" class="button btn btn-sm btn-warning">
                                  <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text">Voltar</span></a>
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

        $('#formUsuario').validate({
            rules: {
                nome: {
                    required: true
                },
                dataExpiracao: {
                    required: true
                },
                cpf: {
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
                nome: {
                    required: 'Campo Requerido.'
                },
                dataExpiracao: {
                    required: 'Campo Requerido.'
                },
                cpf: {
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
