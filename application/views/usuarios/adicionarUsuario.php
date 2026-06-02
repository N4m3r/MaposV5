<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>

<style>
    /* ============================================
       FORMULÁRIO DE USUÁRIO — DESIGN REFINADO
       ============================================ */
    .user-form-section {
        margin-bottom: 0;
        padding: 0;
    }
    .form-section-title {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--cinza0, #9aa6b3);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section-title svg.svg-icon {
        width: 16px;
        height: 16px;
        color: var(--sidebar-accent, #0467fc);
        stroke: var(--sidebar-accent, #0467fc);
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 20px;
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 0 20px;
    }
    .form-grid-address {
        display: grid;
        grid-template-columns: 3fr 1fr;
        gap: 0 20px;
    }
    .form-grid-city {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 0 20px;
    }
    .input-icon-group {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-icon-group .input-icon {
        position: absolute;
        left: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--cinza0, #9aa6b3);
        pointer-events: none;
        z-index: 2;
    }
    .input-icon-group .input-icon svg.svg-icon {
        width: 18px;
        height: 18px;
    }
    .input-icon-group input,
    .input-icon-group select {
        padding-left: 40px !important;
    }
    .password-toggle {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--cinza0, #9aa6b3);
        padding: 4px;
        display: flex;
        align-items: center;
        transition: color 0.2s ease;
        z-index: 2;
    }
    .password-toggle:hover {
        color: var(--sidebar-accent, #0467fc);
    }
    .password-toggle svg.svg-icon {
        width: 18px;
        height: 18px;
    }
    .form-select-styled {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 12px;
        padding-right: 36px !important;
    }
    .form-actions-bar {
        display: flex;
        justify-content: center;
        gap: 12px;
        padding: 24px 0 12px;
        border-top: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.08);
        margin-top: 8px;
    }
    .btn-form-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        background: linear-gradient(135deg, var(--sidebar-accent, #0467fc), #6366f1);
        color: #fff;
        box-shadow: 0 4px 12px rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.25);
    }
    .btn-form-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.35);
    }
    .btn-form-primary svg.svg-icon { width: 18px; height: 18px; }
    .btn-form-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid rgba(0,0,0,0.12);
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--select-bg, #ffffff);
        color: var(--title, #2d3748);
    }
    .btn-form-secondary:hover {
        background: rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.06);
        border-color: var(--sidebar-accent, #0467fc);
        color: var(--sidebar-accent, #0467fc);
    }
    .btn-form-secondary svg.svg-icon { width: 18px; height: 18px; }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .status-active { background: rgba(5, 150, 105, 0.1); color: #059669; }
    .status-inactive { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    /* Temas escuros */
    body[data-theme="puredark"] .form-section-title,
    body[data-theme="darkviolet"] .form-section-title,
    body[data-theme="darkorange"] .form-section-title {
        color: var(--cinza0, #9aa6b3);
        border-bottom-color: rgba(255,255,255,0.08);
    }
    body[data-theme="puredark"] .input-icon-group .input-icon,
    body[data-theme="darkviolet"] .input-icon-group .input-icon,
    body[data-theme="darkorange"] .input-icon-group .input-icon {
        color: var(--dark-7, #a0aec0);
    }
    body[data-theme="puredark"] .btn-form-secondary,
    body[data-theme="darkviolet"] .btn-form-secondary,
    body[data-theme="darkorange"] .btn-form-secondary {
        border-color: rgba(255,255,255,0.15);
        background: var(--dark-2, #272835);
        color: #e2e8f0;
    }
    body[data-theme="puredark"] .btn-form-secondary:hover,
    body[data-theme="darkviolet"] .btn-form-secondary:hover,
    body[data-theme="darkorange"] .btn-form-secondary:hover {
        background: rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.15);
    }
    body[data-theme="puredark"] .form-actions-bar,
    body[data-theme="darkviolet"] .form-actions-bar,
    body[data-theme="darkorange"] .form-actions-bar {
        border-top-color: rgba(255,255,255,0.06);
    }

    @media (max-width: 767px) {
        .form-grid-2, .form-grid-3, .form-grid-address, .form-grid-city {
            grid-template-columns: 1fr;
            gap: 0;
        }
        .form-actions-bar { flex-wrap: wrap; }
    }
</style>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <?= svg_icon('user-plus', 20, 20) ?>
                </span>
                <h5>Cadastro de Usuário</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger" style="margin:16px 20px;border-radius:10px;">' . e($custom_error) . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formUsuario" method="post" class="form-horizontal">
                    <div style="padding: 20px 24px;">

                        <!-- ═══════════════════════════════════════ -->
                        <!-- SEÇÃO: DADOS PESSOAIS                    -->
                        <!-- ═══════════════════════════════════════ -->
                        <div class="form-section-title">
                            <?= svg_icon('user', 16, 16) ?> Dados Pessoais
                        </div>

                        <div class="form-grid-2">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('user', 18, 18) ?></span>
                                    <input id="nome" type="text" name="nome" value="<?php echo set_value('nome'); ?>" placeholder="Nome completo do usuário">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('envelope', 18, 18) ?></span>
                                    <input id="email" type="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="email@exemplo.com">
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="mb-3">
                                <label for="cpf" class="form-label">CPF <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('id-card', 18, 18) ?></span>
                                    <input class="" type="text" id="cpfUser" name="cpf" value="<?php echo set_value('cpf'); ?>" placeholder="000.000.000-00">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="rg" class="form-label">RG <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('file-text', 18, 18) ?></span>
                                    <input id="rg" type="text" name="rg" value="<?php echo set_value('rg'); ?>" placeholder="00.000.000-0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('phone', 18, 18) ?></span>
                                    <input id="telefone" type="text" name="telefone" value="<?php echo set_value('telefone'); ?>" placeholder="(00) 00000-0000">
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="mb-3">
                                <label for="celular" class="form-label">Celular</label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('phone', 18, 18) ?></span>
                                    <input id="celular" type="text" name="celular" value="<?php echo set_value('celular'); ?>" placeholder="(00) 00000-0000">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('lock', 18, 18) ?></span>
                                    <input id="senha" type="password" name="senha" value="<?php echo set_value('senha'); ?>" placeholder="Mínimo 6 caracteres">
                                    <button type="button" class="password-toggle" onclick="togglePassword('senha', this)" aria-label="Mostrar senha">
                                        <?= svg_icon('eye', 18, 18) ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════ -->
                        <!-- SEÇÃO: ENDEREÇO                          -->
                        <!-- ═══════════════════════════════════════ -->
                        <div class="form-section-title" style="margin-top: 8px;">
                            <?= svg_icon('map', 16, 16) ?> Endereço
                        </div>

                        <div class="form-grid-2">
                            <div class="mb-3">
                                <label for="cep" class="form-label">CEP <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('search', 18, 18) ?></span>
                                    <input id="cep" type="text" name="cep" value="<?php echo set_value('cep'); ?>" placeholder="00000-000">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="rua" class="form-label">Rua <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('map', 18, 18) ?></span>
                                    <input id="rua" type="text" name="rua" value="<?php echo set_value('rua'); ?>" placeholder="Nome da rua">
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-address">
                            <div class="mb-3">
                                <label for="bairro" class="form-label">Bairro <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('building-house', 18, 18) ?></span>
                                    <input id="bairro" type="text" name="bairro" value="<?php echo set_value('bairro'); ?>" placeholder="Bairro">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="numero" class="form-label">Número <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('hash', 18, 18) ?></span>
                                    <input id="numero" type="text" name="numero" value="<?php echo set_value('numero'); ?>" placeholder="Nº">
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-city">
                            <div class="mb-3">
                                <label for="cidade" class="form-label">Cidade <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('building', 18, 18) ?></span>
                                    <input id="cidade" type="text" name="cidade" value="<?php echo set_value('cidade'); ?>" placeholder="Cidade">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('map', 18, 18) ?></span>
                                    <input id="estado" type="text" name="estado" value="<?php echo set_value('estado'); ?>" placeholder="UF">
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════ -->
                        <!-- SEÇÃO: ACESSO E PERMISSÕES               -->
                        <!-- ═══════════════════════════════════════ -->
                        <div class="form-section-title" style="margin-top: 8px;">
                            <?= svg_icon('shield', 16, 16) ?> Acesso e Permissões
                        </div>

                        <div class="form-grid-2">
                            <div class="mb-3">
                                <label for="dataExpiracao" class="form-label">Expira em <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('calendar', 18, 18) ?></span>
                                    <input id="dataExpiracao" type="date" name="dataExpiracao" value="<?php echo set_value('dataExpiracao'); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Situação <span class="required">*</span></label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><?= svg_icon('check-circle', 18, 18) ?></span>
                                    <select name="situacao" id="situacao" class="form-select-styled">
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissões <span class="required">*</span></label>
                            <div class="input-icon-group">
                                <span class="input-icon"><?= svg_icon('shield', 18, 18) ?></span>
                                <select name="permissoes_id" id="permissoes_id" class="form-select-styled">
                                    <?php foreach ($permissoes as $p) {
                                        echo '<option value="' . e($p->idPermissao) . '">' . e($p->nome) . '</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════ -->
                        <!-- BOTÕES DE AÇÃO                           -->
                        <!-- ═══════════════════════════════════════ -->
                        <div class="form-actions-bar">
                            <button type="submit" class="btn-form-primary">
                                <?= svg_icon('plus-circle', 18, 18) ?> Adicionar
                            </button>
                            <a href="<?php echo base_url() ?>index.php/usuarios" class="btn-form-secondary">
                                <?= svg_icon('chevron-left', 18, 18) ?> Voltar
                            </a>
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

    // Toggle de visibilidade da senha
    window.togglePassword = function(fieldId, btn) {
        var input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<?= svg_icon('eye-off', 18, 18) ?>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<?= svg_icon('eye', 18, 18) ?>';
        }
    };

    // Máscaras
    $('#cpfUser').mask('000.000.000-00', {reverse: true});
    $('#telefone').mask('(00) 00000-0000');
    $('#celular').mask('(00) 00000-0000');
    $('#cep').mask('00000-000', {reverse: false});
    $('#rg').mask('00.000.000-0', {reverse: true});

    // Busca CEP
    $('#cep').blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                if (!data.erro) {
                    $('#rua').val(data.logradouro || '');
                    $('#bairro').val(data.bairro || '');
                    $('#cidade').val(data.localidade || '');
                    $('#estado').val(data.uf || '');
                    $('#numero').focus();
                }
            });
        }
    });

    // Validação
    $('#formUsuario').validate({
        rules: {
            nome: { required: true },
            dataExpiracao: { required: true },
            cpf: { required: true },
            telefone: { required: true },
            email: { required: true },
            senha: { required: true, minlength: 6 },
            rua: { required: true },
            numero: { required: true },
            bairro: { required: true },
            cidade: { required: true },
            estado: { required: true },
            cep: { required: true }
        },
        messages: {
            nome: { required: 'Campo Requerido.' },
            dataExpiracao: { required: 'Campo Requerido.' },
            cpf: { required: 'Campo Requerido.' },
            telefone: { required: 'Campo Requerido.' },
            email: { required: 'Campo Requerido.' },
            senha: { required: 'Campo Requerido.', minlength: 'Mínimo 6 caracteres.' },
            rua: { required: 'Campo Requerido.' },
            numero: { required: 'Campo Requerido.' },
            bairro: { required: 'Campo Requerido.' },
            cidade: { required: 'Campo Requerido.' },
            estado: { required: 'Campo Requerido.' },
            cep: { required: 'Campo Requerido.' }
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