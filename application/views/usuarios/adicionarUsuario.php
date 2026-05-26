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
                <h5>Cadastro de Usuário</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . e($custom_error) . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formUsuario" method="post" class="form-horizontal">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo set_value('nome'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rg" class="form-label">RG<span class="required">*</span></label>
                        <div class="controls">
                            <input id="rg" type="text" name="rg" value="<?php echo set_value('rg'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF<span class="required">*</span></label>
                        <div class="controls">
                            <input class="" type="text" id="cpfUser" name="cpf" value="<?php echo set_value('cpf'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone<span class="required">*</span></label>
                        <div class="controls">
                            <input id="telefone" type="text" name="telefone" value="<?php echo set_value('telefone'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular</label>
                        <div class="controls">
                            <input id="celular" type="text" name="celular" value="<?php echo set_value('celular'); ?>" />
                        </div>
                    </div>


                    <div class="mb-3">
                        <label for="email" class="form-label">Email<span class="required">*</span></label>
                        <div class="controls">
                            <input id="email" type="text" name="email" value="<?php echo set_value('email'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha<span class="required">*</span></label>
                        <div class="controls">
                            <input id="senha" type="password" name="senha" value="<?php echo set_value('senha'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3" class="form-label">
                        <label for="cep" class="form-label">CEP<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cep" type="text" name="cep" value="<?php echo set_value('cep'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rua" class="form-label">Rua<span class="required">*</span></label>
                        <div class="controls">
                            <input id="rua" type="text" name="rua" value="<?php echo set_value('rua'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="numero" class="form-label">Numero<span class="required">*</span></label>
                        <div class="controls">
                            <input id="numero" type="text" name="numero" value="<?php echo set_value('numero'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bairro" class="form-label">Bairro<span class="required">*</span></label>
                        <div class="controls">
                            <input id="bairro" type="text" name="bairro" value="<?php echo set_value('bairro'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cidade" class="form-label">Cidade<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cidade" type="text" name="cidade" value="<?php echo set_value('cidade'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado<span class="required">*</span></label>
                        <div class="controls">
                            <input id="estado" type="text" name="estado" value="<?php echo set_value('estado'); ?>" />
                        </div>
                    </div>


                    <!-- Campo para inserir a data de validade de acesso do usuário-->
                    <div class="mb-3">
                        <label for="dataExpiracao" class="form-label">Expira em <span class="required">*</span></label>
                        <div class="controls">
                            <input id="dataExpiracao" type="date" name="dataExpiracao" value="<?php echo set_value('dataExpiracao'); ?>" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Situação*</label>
                        <div class="controls">
                            <select name="situacao" id="situacao">
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permissões<span class="required">*</span></label>
                        <div class="controls">
                            <select name="permissoes_id" id="permissoes_id">
                                <?php foreach ($permissoes as $p) {
                                    echo '<option value="' . e($p->idPermissao) . '">' . e($p->nome) . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="col-12">
                            <div class="col-6 offset-md-3" style="display:flex">
                                <button type="submit" class="button btn btn-success">
                                  <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/usuarios" id="" class="button btn btn-sm btn-warning">
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
