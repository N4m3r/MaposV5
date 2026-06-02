<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
<script>
    window.emitente = <?php echo json_encode(isset($emitente) ? $emitente : null); ?>;
</script>
<style>
    .form-card { background: var(--widget-box, #fff); border-radius: 10px; padding: 24px; margin-bottom: 20px; border: 1px solid var(--border-color, #e2e8f0); }
    .form-card h6 { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--subtitle, #64748b); margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color, #e2e8f0); }
    .form-card .mb-3 { margin-bottom: 1rem; }
    .form-card label.form-label { font-weight: 500; font-size: 0.875rem; color: var(--subtitle, #475569); }
    .form-card input, .form-card select { border-radius: 6px; border: 1px solid var(--border-color, #cbd5e1); padding: 8px 12px; font-size: 0.875rem; width: 100%; background: var(--widget-box, #fff); color: var(--title, #1e293b); transition: border-color 0.2s, box-shadow 0.2s; }
    .form-card input:focus, .form-card select:focus { border-color: #0284c7; box-shadow: 0 0 0 3px rgba(2,132,199,0.15); outline: none; }
    .form-card .input-group input { border-radius: 6px 0 0 6px; }
    .form-card .input-group-btn .btn-buscar { border-radius: 0 6px 6px 0; border: 1px solid var(--border-color, #cbd5e1); border-left: none; background: var(--widget-box, #f1f5f9); padding: 8px 14px; font-size: 0.8rem; cursor: pointer; color: var(--title, #1e293b); transition: background 0.2s; }
    .form-card .input-group-btn .btn-buscar:hover { background: #0284c7; color: #fff; border-color: #0284c7; }
    .password-toggle { position: relative; }
    .password-toggle input { padding-right: 42px; }
    .password-toggle #imgSenha { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; cursor: pointer; opacity: 0.6; transition: opacity 0.2s; }
    .password-toggle #imgSenha:hover { opacity: 1; }
    .form-check-label-custom { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 6px; border: 1px solid var(--border-color, #cbd5e1); cursor: pointer; font-size: 0.875rem; color: var(--title, #1e293b); transition: all 0.2s; user-select: none; }
    .form-check-label-custom:hover { background: var(--widget-box, #f1f5f9); }
    .form-check-label-custom.checked { background: #059669; color: #fff; border-color: #059669; }
    .form-check-label-custom .badgebox { position: absolute; opacity: 0; pointer-events: none; }
    .form-actions-bar { display: flex; justify-content: center; gap: 10px; padding: 20px 0 10px; border-top: 1px solid var(--border-color, #e2e8f0); margin-top: 10px; }
    @media (max-width: 767px) { .form-card { padding: 16px; } }
</style>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon"><i class="fas fa-user-plus"></i></span>
            <h5>Cadastro de Cliente</h5>
        </div>
        <?php if ($custom_error != '') {
            echo '<div class="alert alert-danger">' . $custom_error . '</div>';
        } ?>
        <form action="<?php echo current_url(); ?>" id="formCliente" method="post">
            <div class="row" style="margin: 0">
                <!-- Coluna esquerda: Dados pessoais -->
                <div class="col-md-6">
                    <div class="form-card">
                        <h6>Dados Pessoais</h6>
                        <div class="mb-3">
                            <label for="documento" class="form-label">CPF/CNPJ</label>
                            <div class="input-group">
                                <input id="documento" class="cpfcnpj" type="text" name="documento" value="<?php echo set_value('documento'); ?>" placeholder="000.000.000-00">
                                <span class="input-group-btn">
                                    <button id="buscar_info_cnpj" class="btn-buscar" type="button">Buscar CNPJ</button>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nomeCliente" class="form-label">Nome/Razão Social <span class="required">*</span></label>
                            <input id="nomeCliente" type="text" name="nomeCliente" value="<?php echo set_value('nomeCliente'); ?>" placeholder="Nome completo ou razão social">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inscricao_municipal" class="form-label">Inscrição Municipal</label>
                                <input id="inscricao_municipal" type="text" name="inscricao_municipal" value="<?php echo set_value('inscricao_municipal'); ?>" placeholder="IM">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                                <input id="inscricao_estadual" type="text" name="inscricao_estadual" value="<?php echo set_value('inscricao_estadual'); ?>" placeholder="IE">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contato" class="form-label">Contato</label>
                            <input class="contato" type="text" name="contato" value="<?php echo set_value('contato'); ?>" placeholder="Nome do contato">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input id="telefone" type="text" name="telefone" value="<?php echo set_value('telefone'); ?>" placeholder="(00) 0000-0000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="celular" class="form-label">Celular</label>
                                <input id="celular" type="text" name="celular" value="<?php echo set_value('celular'); ?>" placeholder="(00) 00000-0000">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="email@exemplo.com" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <div class="password-toggle">
                                <input id="senha" type="password" name="senha" autocomplete="new-password" value="<?php echo set_value('senha'); ?>" placeholder="Senha de acesso">
                                <img id="imgSenha" src="<?php echo base_url() ?>assets/img/eye.svg" alt="Mostrar senha">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Cliente</label>
                            <label for="fornecedor" class="form-check-label-custom">
                                <input type="checkbox" id="fornecedor" name="fornecedor" class="badgebox" value="1">
                                <span>&#10003;</span> Fornecedor
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Coluna direita: Endereço -->
                <div class="col-md-6">
                    <div class="form-card">
                        <h6>Endereço</h6>
                        <div class="mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input id="cep" type="text" name="cep" value="<?php echo set_value('cep'); ?>" placeholder="00000-000">
                        </div>
                        <div class="mb-3">
                            <label for="rua" class="form-label">Rua</label>
                            <input id="rua" type="text" name="rua" value="<?php echo set_value('rua'); ?>" placeholder="Nome da rua">
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input id="numero" type="text" name="numero" value="<?php echo set_value('numero'); ?>" placeholder="Nº">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input id="complemento" type="text" name="complemento" value="<?php echo set_value('complemento'); ?>" placeholder="Apto, Sala, etc.">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input id="bairro" type="text" name="bairro" value="<?php echo set_value('bairro'); ?>" placeholder="Bairro">
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input id="cidade" type="text" name="cidade" value="<?php echo set_value('cidade'); ?>" placeholder="Cidade">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select id="estado" name="estado">
                                    <option value="">UF</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions-bar">
                <button type="submit" class="button btn btn-success"><span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span></button>
                <a href="<?php echo site_url() ?>/clientes" class="button btn btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Toggle de visibilidade da senha
        $('#imgSenha').on('click', function() {
            var input = $('#senha');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).attr('src', '<?php echo base_url() ?>assets/img/eye-off.svg');
            } else {
                input.attr('type', 'password');
                $(this).attr('src', '<?php echo base_url() ?>assets/img/eye.svg');
            }
        });

        // Toggle do checkbox Fornecedor com visual
        $('#fornecedor').on('change', function() {
            $(this).closest('.form-check-label-custom').toggleClass('checked', this.checked);
        });

        // Carregar estados
        $.getJSON('<?php echo base_url() ?>assets/json/estados.json', function(data) {
            for (var i in data.estados) {
                $('#estado').append(new Option(data.estados[i].nome, data.estados[i].sigla));
            }
            var curState = '<?php echo set_value('estado'); ?>';
            if (curState) {
                $("#estado option[value=" + curState + "]").prop("selected", true);
            }
        });

        $("#nomeCliente").focus();

        // Validacao do formulario
        $('#formCliente').validate({
            rules: {
                nomeCliente: { required: true }
            },
            messages: {
                nomeCliente: { required: 'Campo Requerido.' }
            },
            errorClass: "form-text",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.mb-3').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest('.mb-3').removeClass('error').addClass('success');
            }
        });
    });
</script>