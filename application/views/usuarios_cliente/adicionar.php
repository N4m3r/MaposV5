<style>
    .badgebox { opacity: 0; position: absolute; }
    .badgebox + .badge { width: 27px; text-indent: -999999px; }
    .badgebox:focus + .badge { box-shadow: inset 0px 0px 5px; }
    .badgebox:checked + .badge { text-indent: 0; }
    .form-check-label-custom { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.12); cursor: pointer; font-size: 0.875rem; transition: all 0.2s; user-select: none; }
    .form-check-label-custom:hover { background: rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.06); }
    .form-check-label-custom.checked { background: #059669; color: #fff; border-color: #059669; }
    .password-toggle { position: relative; }
    .password-toggle input { padding-right: 42px !important; }
    .password-toggle .toggle-pw { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; cursor: pointer; opacity: 0.6; transition: opacity 0.2s; }
    .password-toggle .toggle-pw:hover { opacity: 1; }
    .form-section-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--cinza0, #9aa6b3); margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.1); }
    .form-actions-bar { display: flex; justify-content: center; gap: 10px; padding: 20px 0 10px; }
    .cnpj-row { margin-bottom: 10px; }
    .cnpj-row .input-group { display: flex; gap: 0; }
    .cnpj-row .input-group input { flex: 1; border-radius: 8px 0 0 8px !important; }
    .cnpj-row .input-group .btn-consultar-cnpj { border-radius: 0 !important; border: 1px solid rgba(0,0,0,0.12); background: transparent; padding: 6px 12px; cursor: pointer; transition: all 0.2s; color: var(--title, #2d3748); }
    .cnpj-row .input-group .btn-consultar-cnpj:hover { background: var(--sidebar-accent, #0467fc); color: #fff; border-color: var(--sidebar-accent, #0467fc); }
    .cnpj-row .input-group .btn-remover-cnpj { border-radius: 0 8px 8px 0 !important; border: 1px solid rgba(0,0,0,0.12); background: transparent; padding: 6px 12px; cursor: pointer; transition: all 0.2s; color: #e11d48; }
    .cnpj-row .input-group .btn-remover-cnpj:hover { background: #e11d48; color: #fff; border-color: #e11d48; }
    .cnpj-razao { margin-top: 5px; }
    .perm-group { margin-bottom: 12px; border-radius: 6px; padding: 10px 12px; border: 1px solid rgba(0,0,0,0.08); background: var(--cinza0, rgba(0,0,0,0.02)); }
    .perm-group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.1); }
    .perm-group-header h6 { margin: 0; font-weight: 600; font-size: 0.85rem; color: var(--title, #2d3748); }
    .perm-group-header h6 i { margin-right: 6px; }
    .btn-group-toggle { display: flex; gap: 6px; }
    .btn-group-toggle .btn { padding: 3px 10px; font-size: 0.75rem; border-radius: 4px; border: 1px solid rgba(0,0,0,0.12); background: transparent; cursor: pointer; transition: all 0.2s; color: var(--title, #2d3748); }
    .btn-group-toggle .btn:hover { background: var(--sidebar-accent, #0467fc); color: #fff; border-color: var(--sidebar-accent, #0467fc); }
    .perm-group .checkbox { margin-left: 10px; margin-bottom: 5px; display: block; }
    .input-group-cnpj-select { display: flex; gap: 0; }
    .input-group-cnpj-select select { flex: 1; border-radius: 8px 0 0 8px !important; }
    .input-group-cnpj-select .btn-add-cnpj { border-radius: 0 8px 8px 0 !important; border: 1px solid rgba(0,0,0,0.12); background: transparent; padding: 6px 14px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; white-space: nowrap; color: var(--title, #2d3748); }
    .input-group-cnpj-select .btn-add-cnpj:hover { background: var(--sidebar-accent, #0467fc); color: #fff; border-color: var(--sidebar-accent, #0467fc); }

    /* Dark theme overrides */
    body[data-theme="puredark"] .form-check-label-custom,
    body[data-theme="darkviolet"] .form-check-label-custom,
    body[data-theme="darkorange"] .form-check-label-custom { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .form-check-label-custom:hover,
    body[data-theme="darkviolet"] .form-check-label-custom:hover,
    body[data-theme="darkorange"] .form-check-label-custom:hover { background: rgba(255,255,255,0.06); }
    body[data-theme="puredark"] .form-section-title,
    body[data-theme="darkviolet"] .form-section-title,
    body[data-theme="darkorange"] .form-section-title { color: var(--cinza0, #9aa6b3); border-bottom-color: rgba(255,255,255,0.08); }
    body[data-theme="puredark"] .perm-group,
    body[data-theme="darkviolet"] .perm-group,
    body[data-theme="darkorange"] .perm-group { background: var(--dark-2, #1e293b); border-color: rgba(255,255,255,0.08); }
    body[data-theme="puredark"] .perm-group-header h6,
    body[data-theme="darkviolet"] .perm-group-header h6,
    body[data-theme="darkorange"] .perm-group-header h6 { color: #e2e8f0; }
    body[data-theme="puredark"] .cnpj-row .input-group .btn-consultar-cnpj,
    body[data-theme="darkviolet"] .cnpj-row .input-group .btn-consultar-cnpj,
    body[data-theme="darkorange"] .cnpj-row .input-group .btn-consultar-cnpj { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .input-group-cnpj-select .btn-add-cnpj,
    body[data-theme="darkviolet"] .input-group-cnpj-select .btn-add-cnpj,
    body[data-theme="darkorange"] .input-group-cnpj-select .btn-add-cnpj { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .btn-group-toggle .btn,
    body[data-theme="darkviolet"] .btn-group-toggle .btn,
    body[data-theme="darkorange"] .btn-group-toggle .btn { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }

    @media (max-width: 767px) { .form-actions-bar { flex-wrap: wrap; } }
</style>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-user-plus"></i></span>
                <h5>Novo Usuário do Portal do Cliente</h5>
            </div>

            <div class="widget-content nopadding">
                <form action="<?= current_url() ?>" method="post" class="form-horizontal">
                    <div class="row" style="margin:0">
                        <div class="col-md-6" style="padding:0 15px">
                            <div class="form-section-title">Dados Básicos</div>

                            <div class="mb-3">
                                <label class="form-label">Nome Completo <span class="required">*</span></label>
                                <input type="text" name="nome" value="<?= set_value('nome') ?>" required placeholder="Nome completo do usuário" />
                                <?= form_error('nome') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email <span class="required">*</span></label>
                                <input type="email" name="email" value="<?= set_value('email') ?>" required placeholder="email@exemplo.com" />
                                <?= form_error('email') ?>
                                <span class="form-text">Será usado para login</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" value="<?= set_value('telefone') ?>" id="telefone" placeholder="(00) 00000-0000" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Senha <span class="required">*</span></label>
                                <div class="password-toggle">
                                    <input type="password" name="senha" required minlength="6" placeholder="Mínimo 6 caracteres" />
                                    <svg class="toggle-pw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </div>
                                <?= form_error('senha') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar Senha <span class="required">*</span></label>
                                <div class="password-toggle">
                                    <input type="password" name="confirmar_senha" required placeholder="Repita a senha" />
                                    <svg class="toggle-pw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </div>
                                <?= form_error('confirmar_senha') ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cliente / Fornecedor Vinculado</label>
                                <select name="cliente_id">
                                    <option value="">-- Selecione (opcional) --</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= $c->idClientes ?>" <?= set_select('cliente_id', $c->idClientes) ?>>
                                            <?= htmlspecialchars($c->nomeCliente) ?> <?= $c->documento ? '(' . $c->documento . ')' : '' ?> <?= isset($c->fornecedor) && $c->fornecedor ? '[Fornecedor]' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="form-text">Vincula automaticamente as OS deste cliente/fornecedor</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ativo</label>
                                <label for="ativo-add" class="form-check-label-custom">
                                    <input type="checkbox" id="ativo-add" name="ativo" class="badgebox" value="1" <?= set_checkbox('ativo', '1', true) ?> />
                                    <span class="badge">&#10003;</span> Usuário ativo pode fazer login
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6" style="padding:0 15px">
                            <div class="form-section-title">CNPJs Vinculados</div>

                            <div class="mb-3">
                                <label class="form-label">Buscar Cliente / Fornecedor Cadastrado</label>
                                <div class="input-group-cnpj-select">
                                    <select id="buscar-cliente-cnpj">
                                        <option value="">-- Selecione para preencher CNPJ --</option>
                                        <?php foreach ($clientes as $c): ?>
                                            <?php if (!empty($c->documento)): ?>
                                                <option value="<?= htmlspecialchars($c->documento) ?>" data-razao="<?= htmlspecialchars($c->nomeCliente) ?>" data-id="<?= $c->idClientes ?>">
                                                    <?= htmlspecialchars($c->nomeCliente) ?> - <?= $c->documento ?> <?= isset($c->fornecedor) && $c->fornecedor ? '[Fornecedor]' : '' ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn-add-cnpj" id="btn-buscar-cliente">
                                        <i class="bx bx-plus"></i> Adicionar CNPJ
                                    </button>
                                </div>
                                <span class="form-text">Selecione um cliente cadastrado para preencher automaticamente</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">CNPJs Vinculados</label>
                                <div id="cnpjs-container">
                                    <div class="cnpj-row">
                                        <div class="input-group">
                                            <input type="text" name="cnpjs[]" class="cnpj-input" placeholder="00.000.000/0000-00" maxlength="18" />
                                            <button type="button" class="btn-consultar-cnpj" title="Consultar na ReceitaWS">
                                                <i class="bx bx-search"></i>
                                            </button>
                                            <button type="button" class="btn-remover-cnpj" title="Remover" style="display:none;">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                        <input type="text" name="cnpjs_razao[]" class="cnpj-razao" placeholder="Razão Social (preenchido automaticamente)" readonly />
                                    </div>
                                </div>
                                <button type="button" class="button btn btn-success btn-sm" id="btn-adicionar-cnpj">
                                    <i class="bx bx-plus"></i> Adicionar CNPJ Manual
                                </button>
                                <span class="form-text">O usuário terá acesso às OS de todos os CNPJs vinculados</span>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin:0">
                        <div class="col-12" style="padding:0 15px">
                            <div class="form-section-title">Permissões</div>
                            <div class="btn-group-toggle" style="margin-bottom:12px;">
                                <button type="button" class="btn" id="btn-marcar-todos-add"><i class="bx bx-check-square"></i> Marcar Todos</button>
                                <button type="button" class="btn" id="btn-desmarcar-todos-add"><i class="bx bx-square"></i> Desmarcar</button>
                            </div>

                            <?php
                            $permissoesAgrupadasAdd = [
                                'Visualização de OS' => ['visualizar_os', 'visualizar_detalhes_os', 'visualizar_produtos_os', 'visualizar_servicos_os', 'visualizar_anexos_os', 'visualizar_documentos_fiscais'],
                                'Financeiro' => ['visualizar_financeiro', 'visualizar_historico_pagamentos', 'visualizar_cobrancas', 'visualizar_boletos', 'visualizar_notas_fiscais'],
                                'Obras' => ['visualizar_obras', 'visualizar_detalhes_obra'],
                                'Compras' => ['visualizar_compras'],
                                'Ações' => ['imprimir_os', 'editar_perfil', 'solicitar_orcamento', 'aprovar_os'],
                                'Notificações' => ['receber_notificacoes', 'acesso_mobile'],
                            ];

                            $labelsAdd = [
                                'visualizar_os' => 'Visualizar Ordens de Serviço',
                                'visualizar_os_apenas_vinculadas' => 'Apenas OS vinculadas aos seus CNPJs',
                                'visualizar_detalhes_os' => 'Ver detalhes da OS',
                                'visualizar_produtos_os' => 'Ver produtos da OS',
                                'visualizar_servicos_os' => 'Ver serviços da OS',
                                'visualizar_anexos_os' => 'Ver anexos da OS',
                                'visualizar_documentos_fiscais' => 'Ver documentos fiscais',
                                'visualizar_financeiro' => 'Ver informações financeiras',
                                'visualizar_historico_pagamentos' => 'Ver histórico de pagamentos',
                                'visualizar_cobrancas' => 'Visualizar Cobranças',
                                'visualizar_boletos' => 'Visualizar Boletos',
                                'visualizar_notas_fiscais' => 'Visualizar Notas Fiscais (NFS-e)',
                                'visualizar_obras' => 'Visualizar Obras',
                                'visualizar_detalhes_obra' => 'Ver detalhes da Obra',
                                'visualizar_compras' => 'Visualizar Compras',
                                'imprimir_os' => 'Imprimir relatório da OS',
                                'editar_perfil' => 'Editar próprio perfil',
                                'solicitar_orcamento' => 'Solicitar novo orçamento',
                                'aprovar_os' => 'Aprovar/Reprovar OS',
                                'receber_notificacoes' => 'Receber notificações por email',
                                'acesso_mobile' => 'Acesso via dispositivos móveis',
                            ];

                            $iconesGrupoAdd = [
                                'Visualização de OS' => 'bx bx-file',
                                'Financeiro' => 'bx bx-money',
                                'Obras' => 'bx bx-building-house',
                                'Compras' => 'bx bx-cart-alt',
                                'Ações' => 'bx bx-check-circle',
                                'Notificações' => 'bx bx-bell',
                            ];

                            foreach ($permissoesAgrupadasAdd as $grupo => $chaves): ?>
                                <div class="perm-group">
                                    <div class="perm-group-header">
                                        <h6><i class="<?= $iconesGrupoAdd[$grupo] ?? 'bx bx-folder-open' ?>"></i> <?= $grupo ?></h6>
                                        <button type="button" class="btn btn-marcar-grupo-add" data-grupo="<?= md5($grupo) ?>">
                                            <i class="bx bx-check-square"></i> Marcar Grupo
                                        </button>
                                    </div>
                                    <div class="checkboxes-grupo-add" data-grupo="<?= md5($grupo) ?>">
                                        <?php foreach ($chaves as $chave):
                                            if (!isset($permissoes_padrao[$chave])) continue;
                                            $valor_padrao = $permissoes_padrao[$chave];
                                            $label = $labelsAdd[$chave] ?? $chave;
                                        ?>
                                            <label class="checkbox" style="margin-left:10px;margin-bottom:5px;display:block;">
                                                <input type="checkbox" name="permissoes[<?= $chave ?>]" value="1" class="checkbox-permissao-add" <?= set_checkbox('permissoes[' . $chave . ']', '1', $valor_padrao) ?> />
                                                <span style="margin-left:5px;"><?= $label ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-actions-bar">
                        <button type="submit" class="button btn btn-success"><span class="button__icon"><i class="bx bx-plus-circle"></i></span><span class="button__text2">Adicionar</span></button>
                        <a href="<?= site_url('usuarioscliente') ?>" class="button btn btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Password toggle
    $('.toggle-pw').on('click', function() {
        var input = $(this).siblings('input');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            $(this).html('<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>');
        } else {
            input.attr('type', 'password');
            $(this).html('<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>');
        }
    });

    // Toggle checkbox visual
    $('.form-check-label-custom input[type="checkbox"]').on('change', function() {
        $(this).closest('.form-check-label-custom').toggleClass('checked', this.checked);
    });
    $('.form-check-label-custom input[type="checkbox"]:checked').each(function() {
        $(this).closest('.form-check-label-custom').addClass('checked');
    });

    // Phone mask
    $('#telefone').mask('(00) 00000-0000');

    // CNPJ mask
    $(document).on('input', '.cnpj-input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length <= 14) {
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            $(this).val(value);
        }
    });

    // Add CNPJ row
    $('#btn-adicionar-cnpj').click(function() {
        var newRow = `
            <div class="cnpj-row">
                <div class="input-group">
                    <input type="text" name="cnpjs[]" class="cnpj-input" placeholder="00.000.000/0000-00" maxlength="18" />
                    <button type="button" class="btn-consultar-cnpj" title="Consultar">
                        <i class="bx bx-search"></i>
                    </button>
                    <button type="button" class="btn-remover-cnpj" title="Remover">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
                <input type="text" name="cnpjs_razao[]" class="cnpj-razao" placeholder="Razão Social (preenchido automaticamente)" readonly />
            </div>
        `;
        $('#cnpjs-container').append(newRow);
        updateRemoveButtons();
    });

    // Remove CNPJ
    $(document).on('click', '.btn-remover-cnpj', function() {
        $(this).closest('.cnpj-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        var rows = $('.cnpj-row');
        if (rows.length <= 1) {
            rows.find('.btn-remover-cnpj').hide();
        } else {
            rows.find('.btn-remover-cnpj').show();
        }
    }
    updateRemoveButtons();

    // Consult CNPJ
    $(document).on('click', '.btn-consultar-cnpj', function() {
        var row = $(this).closest('.cnpj-row');
        var cnpjInput = row.find('.cnpj-input');
        var razaoInput = row.find('.cnpj-razao');
        var cnpj = cnpjInput.val().replace(/\D/g, '');

        if (cnpj.length !== 14) {
            alert('CNPJ inválido');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).find('i').removeClass('bx-search').addClass('bx-loader bx-spin');

        $.ajax({
            url: '<?= site_url("usuarioscliente/api_consultar_cnpj") ?>',
            type: 'GET',
            data: { cnpj: cnpj },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    razaoInput.val(response.data.nome);
                } else {
                    alert(response.error || 'Erro ao consultar CNPJ');
                }
            },
            error: function() {
                alert('Erro na consulta. Tente novamente.');
            },
            complete: function() {
                btn.prop('disabled', false).find('i').removeClass('bx-loader bx-spin').addClass('bx-search');
            }
        });
    });

    // Buscar cliente cadastrado
    $('#btn-buscar-cliente').click(function() {
        var select = $('#buscar-cliente-cnpj');
        var selectedOption = select.find('option:selected');

        if (!select.val()) {
            alert('Selecione um cliente primeiro');
            return;
        }

        var cnpj = selectedOption.val();
        var razaoSocial = selectedOption.data('razao');
        var clienteId = selectedOption.data('id');

        var cnpjExistente = false;
        $('.cnpj-input').each(function() {
            if ($(this).val().replace(/\D/g, '') === cnpj.replace(/\D/g, '')) {
                cnpjExistente = true;
                return false;
            }
        });

        if (cnpjExistente) {
            alert('Este CNPJ já foi adicionado!');
            return;
        }

        var cnpjFormatado = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');

        var linhaVazia = null;
        $('.cnpj-row').each(function() {
            if ($(this).find('.cnpj-input').val() === '') {
                linhaVazia = $(this);
                return false;
            }
        });

        if (linhaVazia) {
            linhaVazia.find('.cnpj-input').val(cnpjFormatado);
            linhaVazia.find('.cnpj-razao').val(razaoSocial);
        } else {
            var newRow = `
                <div class="cnpj-row">
                    <div class="input-group">
                        <input type="text" name="cnpjs[]" class="cnpj-input" placeholder="00.000.000/0000-00" maxlength="18" value="${cnpjFormatado}" />
                        <button type="button" class="btn-consultar-cnpj" title="Consultar na ReceitaWS">
                            <i class="bx bx-search"></i>
                        </button>
                        <button type="button" class="btn-remover-cnpj" title="Remover">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                    <input type="text" name="cnpjs_razao[]" class="cnpj-razao" placeholder="Razão Social (preenchido automaticamente)" readonly value="${razaoSocial}" />
                </div>
            `;
            $('#cnpjs-container').append(newRow);
        }

        updateRemoveButtons();
        select.val('');
        alert('CNPJ adicionado com sucesso!\n\nCliente: ' + razaoSocial + '\nCNPJ: ' + cnpjFormatado);
    });

    // Auto-suggest CNPJ on cliente select
    $('select[name="cliente_id"]').change(function() {
        var clienteId = $(this).val();
        if (clienteId) {
            var clienteOption = $(this).find('option:selected');
            var texto = clienteOption.text();
            var match = texto.match(/\(([^)]+)\)/);

            if (match && confirm('Deseja adicionar o CNPJ deste cliente aos CNPJs vinculados?')) {
                var cnpj = match[1];
                $('#buscar-cliente-cnpj option').each(function() {
                    if ($(this).val() === cnpj) {
                        $(this).prop('selected', true);
                        $('#btn-buscar-cliente').click();
                        return false;
                    }
                });
            }
        }
    });

    // Marcar Todos
    $('#btn-marcar-todos-add').click(function() {
        $('.checkbox-permissao-add').prop('checked', true);
    });

    // Desmarcar Todos
    $('#btn-desmarcar-todos-add').click(function() {
        $('.checkbox-permissao-add').prop('checked', false);
    });

    // Marcar Grupo
    $('.btn-marcar-grupo-add').click(function() {
        var grupoHash = $(this).data('grupo');
        var $checkboxes = $('.checkboxes-grupo-add[data-grupo="' + grupoHash + '"]').find('.checkbox-permissao-add');
        var todosMarcados = $checkboxes.length === $checkboxes.filter(':checked').length;

        if (todosMarcados) {
            $checkboxes.prop('checked', false);
            $(this).html('<i class="bx bx-check-square"></i> Marcar Grupo');
        } else {
            $checkboxes.prop('checked', true);
            $(this).html('<i class="bx bx-square"></i> Desmarcar Grupo');
        }
    });
});
</script>