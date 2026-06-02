<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
<script>
    window.emitente = <?php echo json_encode(isset($emitente) ? $emitente : null); ?>;
</script>
<style>
    .password-toggle { position: relative; }
    .password-toggle input { padding-right: 42px !important; }
    .password-toggle #imgSenha { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; cursor: pointer; opacity: 0.6; transition: opacity 0.2s; }
    .password-toggle #imgSenha:hover { opacity: 1; }
    .badgebox { opacity: 0; position: absolute; }
    .badgebox + .badge { width: 27px; text-indent: -999999px; }
    .badgebox:focus + .badge { box-shadow: inset 0px 0px 5px; }
    .badgebox:checked + .badge { text-indent: 0; }
    .form-check-label-custom { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.12); cursor: pointer; font-size: 0.875rem; transition: all 0.2s; user-select: none; }
    .form-check-label-custom:hover { background: rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.06); }
    .form-check-label-custom.checked { background: #059669; color: #fff; border-color: #059669; }
    .input-group-cnpj { display: flex; gap: 0; }
    .input-group-cnpj input { border-radius: 8px 0 0 8px !important; flex: 1; }
    .input-group-cnpj .btn-buscar { border-radius: 0 8px 8px 0 !important; border: 1.5px solid rgba(0,0,0,0.12); border-left: none; background: transparent; padding: 0 14px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; color: var(--title, #2d3748); }
    .input-group-cnpj .btn-buscar:hover { background: var(--sidebar-accent, #0467fc); color: #fff; border-color: var(--sidebar-accent, #0467fc); }
    .mb-3.error .form-text { display: flex; }
    .form-section-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--cinza0, #9aa6b3); margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.1); }
    .form-actions-bar { display: flex; justify-content: center; gap: 10px; padding: 20px 0 10px; }
    body[data-theme="puredark"] .form-check-label-custom,
    body[data-theme="darkviolet"] .form-check-label-custom,
    body[data-theme="darkorange"] .form-check-label-custom { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .form-check-label-custom:hover,
    body[data-theme="darkviolet"] .form-check-label-custom:hover,
    body[data-theme="darkorange"] .form-check-label-custom:hover { background: rgba(255,255,255,0.06); }
    body[data-theme="puredark"] .form-section-title,
    body[data-theme="darkviolet"] .form-section-title,
    body[data-theme="darkorange"] .form-section-title { color: var(--cinza0, #9aa6b3); border-bottom-color: rgba(255,255,255,0.08); }
    body[data-theme="puredark"] .input-group-cnpj .btn-buscar,
    body[data-theme="darkviolet"] .input-group-cnpj .btn-buscar,
    body[data-theme="darkorange"] .input-group-cnpj .btn-buscar { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    @media (max-width: 767px) { .form-actions-bar { flex-wrap: wrap; } }
</style>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-user-edit"></i></span>
                <h5>Editar Cliente</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formCliente" method="post">
                <?php echo form_hidden('idClientes', $result->idClientes); ?>
                <div class="widget-content nopadding tab-content">
                    <div class="row" style="margin:0">
                        <div class="col-md-6" style="padding:0 15px">
                            <div class="form-section-title">Dados Pessoais</div>
                            <div class="mb-3">
                                <label for="documento" class="form-label">CPF/CNPJ</label>
                                <div class="input-group-cnpj">
                                    <input id="documento" class="cpfcnpj" type="text" name="documento" value="<?php echo e($result->documento); ?>" placeholder="000.000.000-00">
                                    <button id="buscar_info_cnpj" class="btn-buscar" type="button">Buscar CNPJ</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nomeCliente" class="form-label">Nome/Razão Social <span class="required">*</span></label>
                                <input id="nomeCliente" type="text" name="nomeCliente" value="<?php echo e($result->nomeCliente); ?>" placeholder="Nome completo ou razão social">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="inscricao_municipal" class="form-label">Inscrição Municipal</label>
                                    <input id="inscricao_municipal" type="text" name="inscricao_municipal" value="<?php echo e($result->inscricao_municipal); ?>" placeholder="IM">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                                    <input id="inscricao_estadual" type="text" name="inscricao_estadual" value="<?php echo e($result->inscricao_estadual); ?>" placeholder="IE">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="contato" class="form-label">Contato</label>
                                <input class="contato" type="text" name="contato" value="<?php echo e($result->contato); ?>" placeholder="Nome do contato">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input id="telefone" type="text" name="telefone" value="<?php echo e($result->telefone); ?>" placeholder="(00) 0000-0000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="celular" class="form-label">Celular</label>
                                    <input id="celular" type="text" name="celular" value="<?php echo e($result->celular); ?>" placeholder="(00) 00000-0000">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email" value="<?php echo e($result->email); ?>" placeholder="email@exemplo.com">
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="password-toggle">
                                    <input id="senha" type="password" name="senha" value="" placeholder="Não preencha se não quiser alterar">
                                    <img id="imgSenha" src="<?php echo base_url() ?>assets/img/eye.svg" alt="Mostrar senha">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Cliente</label>
                                <label for="fornecedor" class="form-check-label-custom <?= ($result->fornecedor == 1) ? 'checked' : '' ?>">
                                    <input type="checkbox" id="fornecedor" name="fornecedor" class="badgebox" value="1" <?= ($result->fornecedor == 1) ? 'checked' : '' ?>>
                                    <span class="badge">&#10003;</span> Fornecedor
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6" style="padding:0 15px">
                            <div class="form-section-title">Endereço</div>
                            <div class="mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input id="cep" type="text" name="cep" value="<?php echo e($result->cep); ?>" placeholder="00000-000">
                            </div>
                            <div class="mb-3">
                                <label for="rua" class="form-label">Rua</label>
                                <input id="rua" type="text" name="rua" value="<?php echo e($result->rua); ?>" placeholder="Nome da rua">
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="numero" class="form-label">Número</label>
                                    <input id="numero" type="text" name="numero" value="<?php echo e($result->numero); ?>" placeholder="Nº">
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="complemento" class="form-label">Complemento</label>
                                    <input id="complemento" type="text" name="complemento" value="<?php echo e($result->complemento); ?>" placeholder="Apto, Sala, etc.">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input id="bairro" type="text" name="bairro" value="<?php echo e($result->bairro); ?>" placeholder="Bairro">
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="cidade" class="form-label">Cidade</label>
                                    <input id="cidade" type="text" name="cidade" value="<?php echo e($result->cidade); ?>" placeholder="Cidade">
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
                    <button type="submit" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                    <a href="<?php echo site_url() ?>/clientes" class="button btn btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
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

        $('#fornecedor').on('change', function() {
            $(this).closest('.form-check-label-custom').toggleClass('checked', this.checked);
        });

        $.getJSON('<?php echo base_url() ?>assets/json/estados.json', function(data) {
            for (var i in data.estados) {
                $('#estado').append(new Option(data.estados[i].nome, data.estados[i].sigla));
            }
            var curState = <?php echo json_encode($result->estado, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            if (curState) {
                $("#estado option[value=" + curState + "]").prop("selected", true);
            }
        });

        $('#formCliente').validate({
            rules: { nomeCliente: { required: true } },
            messages: { nomeCliente: { required: 'Campo Requerido.' } },
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