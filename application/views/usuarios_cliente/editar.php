<?php
/**
 * View: Editar Usuário do Portal do Cliente
 */
?>

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
    .perm-group { margin-bottom: 12px; border-radius: 6px; padding: 10px 12px; border: 1px solid rgba(0,0,0,0.08); background: var(--cinza0, rgba(0,0,0,0.02)); }
    .perm-group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.1); }
    .perm-group-header h6 { margin: 0; font-weight: 600; font-size: 0.85rem; color: var(--title, #2d3748); }
    .perm-group-header h6 i { margin-right: 6px; }
    .btn-group-toggle { display: flex; gap: 6px; }
    .btn-group-toggle .btn { padding: 3px 10px; font-size: 0.75rem; border-radius: 4px; border: 1px solid rgba(0,0,0,0.12); background: transparent; cursor: pointer; transition: all 0.2s; color: var(--title, #2d3748); }
    .btn-group-toggle .btn:hover { background: var(--sidebar-accent, #0467fc); color: #fff; border-color: var(--sidebar-accent, #0467fc); }
    .perm-group .checkbox { margin-left: 10px; margin-bottom: 5px; display: block; }
    .cnpj-card { border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid rgba(0,0,0,0.08); background: var(--cinza0, rgba(0,0,0,0.02)); }
    .cnpj-card .cnpj-numero { font-weight: 600; font-size: 0.95rem; color: var(--title, #2d3748); }
    .cnpj-card .cnpj-razao-label { font-size: 0.8rem; color: var(--subtitle, #718096); margin-top: 2px; }
    .cnpj-add-area { border: 2px dashed rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.25); border-radius: 8px; padding: 16px; }
    .input-group-cnpj-select { display: flex; gap: 0; }
    .input-group-cnpj-select select { flex: 1; border-radius: 8px 0 0 8px !important; }
    .input-group-cnpj-select .btn-add-cnpj { border-radius: 0 8px 8px 0 !important; border: 1px solid rgba(0,0,0,0.12); background: transparent; padding: 6px 14px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; white-space: nowrap; color: var(--title, #2d3748); }
    .input-group-cnpj-select .btn-add-cnpj:hover { background: var(--sidebar-accent, #0467fc); color: #fff; border-color: var(--sidebar-accent, #0467fc); }
    .input-group-cnpj-manual { display: flex; gap: 0; }
    .input-group-cnpj-manual input:first-child { flex: 2; border-radius: 8px 0 0 8px !important; }
    .input-group-cnpj-manual input:nth-child(2) { flex: 1; border-radius: 0 !important; }
    .input-group-cnpj-manual .btn-add-manual { border-radius: 0 8px 8px 0 !important; border: 1px solid rgba(0,0,0,0.12); background: transparent; padding: 6px 14px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; white-space: nowrap; color: var(--title, #2d3748); }
    .input-group-cnpj-manual .btn-add-manual:hover { background: #059669; color: #fff; border-color: #059669; }
    .cnpj-counter { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.1); color: var(--sidebar-accent, #0467fc); }

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
    body[data-theme="puredark"] .cnpj-card,
    body[data-theme="darkviolet"] .cnpj-card,
    body[data-theme="darkorange"] .cnpj-card { background: var(--dark-2, #1e293b); border-color: rgba(255,255,255,0.08); }
    body[data-theme="puredark"] .cnpj-card .cnpj-numero,
    body[data-theme="darkviolet"] .cnpj-card .cnpj-numero,
    body[data-theme="darkorange"] .cnpj-card .cnpj-numero { color: #e2e8f0; }
    body[data-theme="puredark"] .cnpj-card .cnpj-razao-label,
    body[data-theme="darkviolet"] .cnpj-card .cnpj-razao-label,
    body[data-theme="darkorange"] .cnpj-card .cnpj-razao-label { color: var(--dark-7, #a0aec0); }
    body[data-theme="puredark"] .cnpj-add-area,
    body[data-theme="darkviolet"] .cnpj-add-area,
    body[data-theme="darkorange"] .cnpj-add-area { border-color: rgba(255,255,255,0.15); }
    body[data-theme="puredark"] .input-group-cnpj-select .btn-add-cnpj,
    body[data-theme="darkviolet"] .input-group-cnpj-select .btn-add-cnpj,
    body[data-theme="darkorange"] .input-group-cnpj-select .btn-add-cnpj { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .input-group-cnpj-manual .btn-add-manual,
    body[data-theme="darkviolet"] .input-group-cnpj-manual .btn-add-manual,
    body[data-theme="darkorange"] .input-group-cnpj-manual .btn-add-manual { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .btn-group-toggle .btn,
    body[data-theme="darkviolet"] .btn-group-toggle .btn,
    body[data-theme="darkorange"] .btn-group-toggle .btn { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .cnpj-counter,
    body[data-theme="darkviolet"] .cnpj-counter,
    body[data-theme="darkorange"] .cnpj-counter { background: rgba(255,255,255,0.1); color: #e2e8f0; }

    @media (max-width: 767px) { .form-actions-bar { flex-wrap: wrap; } }
</style>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="dropdown-divider">/</span></li>
            <li><a href="<?= site_url('usuarioscliente') ?>">Usuários Cliente</a> <span class="dropdown-divider">/</span></li>
            <li class="active">Editar: <?= htmlspecialchars($usuario->nome, ENT_QUOTES, 'UTF-8') ?></li>
        </ul>
    </div>
</div>

<form action="<?= current_url() ?>" method="post" id="form-usuario">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

    <?php if (validation_errors()): ?>
    <div class="row" style="margin-top:10px;">
        <div class="col-12">
            <div class="alert alert-danger"><?= validation_errors() ?></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row" style="margin-top:10px;">
        <div class="col-md-6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="bx bx-user-pin"></i></span>
                    <h5>Dados do Usuário</h5>
                </div>
                <div class="widget-content">

                    <div class="mb-3">
                        <label class="form-label">Nome Completo <span class="required">*</span></label>
                        <input type="text" name="nome" value="<?= set_value('nome', $usuario->nome) ?>" required placeholder="Nome completo do usuário" />
                        <?= form_error('nome') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" value="<?= set_value('email', $usuario->email) ?>" required placeholder="email@exemplo.com" />
                        <?= form_error('email') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" value="<?= set_value('telefone', $usuario->telefone) ?>" id="telefone" placeholder="(00) 00000-0000" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nova Senha</label>
                        <div class="password-toggle">
                            <input type="password" name="senha" minlength="6" placeholder="Deixe em branco para manter a atual" />
                            <svg class="toggle-pw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <?= form_error('senha') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar Senha</label>
                        <div class="password-toggle">
                            <input type="password" name="confirmar_senha" placeholder="Repita a nova senha" />
                            <svg class="toggle-pw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <?= form_error('confirmar_senha') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cliente Vinculado</label>
                        <select name="cliente_id" id="select-cliente-principal">
                            <option value="">-- Nenhum --</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c->idClientes ?>"
                                        data-documento="<?= htmlspecialchars($c->documento ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-razao="<?= htmlspecialchars($c->nomeCliente ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        <?= set_select('cliente_id', $c->idClientes, $usuario->cliente_id == $c->idClientes) ?>>
                                    <?= htmlspecialchars($c->nomeCliente, ENT_QUOTES, 'UTF-8') ?>
                                    <?= $c->documento ? '(' . $c->documento . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-text"><i class="bx bx-link"></i> Vincula automaticamente todas as OS deste cliente</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <label for="ativo-edit" class="form-check-label-custom <?= ($usuario->ativo == 1) ? 'checked' : '' ?>">
                            <input type="checkbox" id="ativo-edit" name="ativo" class="badgebox" value="1" <?= set_checkbox('ativo', '1', $usuario->ativo == 1) ?> />
                            <span class="badge">&#10003;</span> Usuário <?= $usuario->ativo ? 'ativo' : 'inativo' ?> — pode fazer login
                        </label>
                    </div>
                </div>
            </div>

            <div class="widget-box" style="margin-top:15px;">
                <div class="widget-title">
                    <span class="icon"><i class="bx bx-shield"></i></span>
                    <h5>Permissões de Acesso</h5>
                    <div class="buttons">
                        <button type="button" class="btn btn-sm" id="btn-marcar-todos"><i class="bx bx-check-square"></i> Marcar Todos</button>
                        <button type="button" class="btn btn-sm" id="btn-desmarcar-todos"><i class="bx bx-square"></i> Desmarcar</button>
                    </div>
                </div>
                <div class="widget-content">
                    <div id="container-permissoes">
                        <?php
                        $permissoesAgrupadas = [
                            'Visualização de OS' => ['visualizar_os', 'visualizar_detalhes_os', 'visualizar_produtos_os', 'visualizar_servicos_os', 'visualizar_anexos_os', 'visualizar_documentos_fiscais'],
                            'Financeiro' => ['visualizar_financeiro', 'visualizar_historico_pagamentos', 'visualizar_cobrancas', 'visualizar_boletos', 'visualizar_notas_fiscais'],
                            'Obras' => ['visualizar_obras', 'visualizar_detalhes_obra'],
                            'Compras' => ['visualizar_compras'],
                            'Ações' => ['imprimir_os', 'editar_perfil', 'solicitar_orcamento', 'aprovar_os'],
                            'Notificações' => ['receber_notificacoes', 'acesso_mobile'],
                        ];

                        $labels = [
                            'visualizar_os' => 'Visualizar Ordens de Serviço',
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
                            'receber_notificacoes' => 'Receber notificações',
                            'acesso_mobile' => 'Acesso via dispositivos móveis',
                        ];

                        $iconesGrupo = [
                            'Visualização de OS' => 'bx bx-file',
                            'Financeiro' => 'bx bx-money',
                            'Obras' => 'bx bx-building-house',
                            'Compras' => 'bx bx-cart-alt',
                            'Ações' => 'bx bx-check-circle',
                            'Notificações' => 'bx bx-bell',
                        ];

                        foreach ($permissoesAgrupadas as $grupo => $chaves):
                            $temPermissaoNoGrupo = false;
                            foreach ($chaves as $chave) {
                                if (isset($permissoes_padrao[$chave])) {
                                    $temPermissaoNoGrupo = true;
                                    break;
                                }
                            }
                            if (!$temPermissaoNoGrupo) continue;
                        ?>
                            <div class="perm-group">
                                <div class="perm-group-header">
                                    <h6><i class="<?= $iconesGrupo[$grupo] ?? 'bx bx-folder-open' ?>"></i> <?= $grupo ?></h6>
                                    <button type="button" class="btn btn-marcar-grupo" data-grupo="<?= md5($grupo) ?>">
                                        <i class="bx bx-check-square"></i> Marcar Grupo
                                    </button>
                                </div>
                                <div class="checkboxes-grupo" data-grupo="<?= md5($grupo) ?>">
                                    <?php foreach ($chaves as $chave):
                                        if (!isset($permissoes_padrao[$chave])) continue;
                                        $valor_salvo = isset($permissoes[$chave]) ? $permissoes[$chave] : null;
                                        $valor_atual = ($valor_salvo !== null) ? $valor_salvo : $permissoes_padrao[$chave];
                                        $valor_atual = ($valor_atual === true || $valor_atual === '1' || $valor_atual === 1);
                                    ?>
                                        <label class="checkbox" style="margin-left:10px;margin-bottom:5px;display:block;">
                                            <input type="checkbox" name="permissoes[<?= $chave ?>]" value="1" class="checkbox-permissao" <?= $valor_atual ? 'checked="checked"' : '' ?> />
                                            <span style="margin-left:5px;"><?= $labels[$chave] ?? $chave ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="bx bx-buildings"></i></span>
                    <h5>CNPJs Vinculados</h5>
                    <div class="buttons">
                        <span class="cnpj-counter" id="contador-cnpjs"><?= count($cnpjs) ?> CNPJ(s)</span>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="mb-3" style="margin-bottom:15px;">
                        <span class="form-text"><i class="bx bx-info-circle"></i> O usuário terá acesso às OS de todos os CNPJs vinculados.</span>
                    </div>

                    <div id="cnpjs-lista" style="margin-bottom:15px;">
                        <?php if (!empty($cnpjs)): ?>
                            <?php foreach ($cnpjs as $cnpj): ?>
                            <div class="cnpj-card">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="cnpj-numero"><i class="bx bx-building"></i> <span class="cnpj-numero-text"><?= $cnpj->cnpj ?></span></div>
                                        <div class="cnpj-razao-label"><i class="bx bx-user"></i> <span class="cnpj-razao"><?= htmlspecialchars($cnpj->razao_social ?: 'Razão Social não informada', ENT_QUOTES, 'UTF-8') ?></span></div>
                                        <input type="hidden" name="cnpjs[]" value="<?= $cnpj->cnpj ?>">
                                        <input type="hidden" name="cnpjs_razao[]" value="<?= htmlspecialchars($cnpj->razao_social ?: '', ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                    <div class="col-3 text-right">
                                        <button type="button" class="btn btn-sm btn-danger btn-remover-cnpj-card" title="Remover CNPJ">
                                            <i class="bx bx-trash"></i> Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div id="sem-cnpjs" class="alert" style="margin:0 0 10px;">
                                <i class="bx bx-error-circle"></i> Nenhum CNPJ vinculado. Adicione pelo menos um CNPJ.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="cnpj-add-area">
                        <div class="form-section-title">Adicionar Novo CNPJ</div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bx bx-search"></i> Buscar cliente cadastrado</label>
                            <div class="input-group-cnpj-select">
                                <select id="select-cliente-cnpj">
                                    <option value="">-- Selecione um cliente --</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <?php if (!empty($c->documento)): ?>
                                            <option value="<?= htmlspecialchars($c->documento, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-razao="<?= htmlspecialchars($c->nomeCliente, ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($c->nomeCliente, ENT_QUOTES, 'UTF-8') ?> - <?= $c->documento ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn-add-cnpj" id="btn-adicionar-cliente">
                                    <i class="bx bx-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>

                        <div class="mb-3" style="margin-bottom:0;">
                            <label class="form-label"><i class="bx bx-pencil"></i> Ou digite o CNPJ manualmente</label>
                            <div class="input-group-cnpj-manual">
                                <input type="text" id="input-cnpj-manual" placeholder="00.000.000/0000-00" maxlength="18" />
                                <input type="text" id="input-razao-manual" placeholder="Razão Social" />
                                <button type="button" class="btn-add-manual" id="btn-adicionar-manual">
                                    <i class="bx bx-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-bar">
                        <button type="submit" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Salvar Alterações</span></button>
                        <a href="<?= site_url('usuarioscliente') ?>" class="button btn btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

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
    if ($.fn.mask) {
        $('#telefone').mask('(00) 00000-0000');
    }

    // CNPJ counter
    function atualizarContador() {
        var total = $('.cnpj-card').length;
        $('#contador-cnpjs').text(total + ' CNPJ(s)');
        if (total === 0) {
            $('#sem-cnpjs').show();
        } else {
            $('#sem-cnpjs').hide();
        }
    }

    // Check duplicate CNPJ
    function cnpjJaExiste(cnpj) {
        var cnpjLimpo = cnpj.replace(/\D/g, '');
        var existe = false;
        $('.cnpj-card').each(function() {
            var cnpjExistente = $(this).find('.cnpj-numero-text').text().replace(/\D/g, '');
            if (cnpjExistente === cnpjLimpo) {
                existe = true;
                return false;
            }
        });
        return existe;
    }

    // Format CNPJ
    function formatarCNPJ(cnpj) {
        var cnpjLimpo = cnpj.replace(/\D/g, '');
        if (cnpjLimpo.length !== 14) return cnpj;
        return cnpjLimpo.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }

    // Create CNPJ card
    function criarCardCNPJ(cnpj, razaoSocial) {
        var cnpjFormatado = formatarCNPJ(cnpj);

        if (cnpjJaExiste(cnpj)) {
            Swal.fire({ icon: 'warning', title: 'CNPJ já vinculado', text: 'Este CNPJ já está na lista.', confirmButtonColor: '#0467fc' });
            return false;
        }

        var cardHtml = `
            <div class="cnpj-card">
                <div class="row">
                    <div class="col-9">
                        <div class="cnpj-numero"><i class="bx bx-building"></i> <span class="cnpj-numero-text">${cnpjFormatado}</span></div>
                        <div class="cnpj-razao-label"><i class="bx bx-user"></i> <span class="cnpj-razao">${razaoSocial || 'Razão Social não informada'}</span></div>
                        <input type="hidden" name="cnpjs[]" value="${cnpjFormatado}">
                        <input type="hidden" name="cnpjs_razao[]" value="${razaoSocial || ''}">
                    </div>
                    <div class="col-3 text-right">
                        <button type="button" class="btn btn-sm btn-danger btn-remover-cnpj-card" title="Remover CNPJ">
                            <i class="bx bx-trash"></i> Remover
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#cnpjs-lista').append(cardHtml);
        atualizarContador();
        return true;
    }

    // Add CNPJ from client select
    $('#btn-adicionar-cliente').click(function() {
        var select = $('#select-cliente-cnpj');
        var selectedOption = select.find('option:selected');

        if (!select.val()) {
            Swal.fire({ icon: 'warning', title: 'Selecione um cliente', text: 'Por favor, selecione um cliente da lista.', confirmButtonColor: '#0467fc' });
            return;
        }

        var cnpj = selectedOption.val();
        var razao = selectedOption.data('razao');

        if (criarCardCNPJ(cnpj, razao)) {
            select.val('').trigger('change');
            Swal.fire({ icon: 'success', title: 'CNPJ Adicionado', text: 'O CNPJ foi vinculado com sucesso!', timer: 2000, showConfirmButton: false });
        }
    });

    // Add CNPJ manually
    $('#btn-adicionar-manual').click(function() {
        var cnpjInput = $('#input-cnpj-manual');
        var razaoInput = $('#input-razao-manual');
        var cnpj = cnpjInput.val().trim();
        var razao = razaoInput.val().trim();

        if (!cnpj) {
            Swal.fire({ icon: 'warning', title: 'CNPJ obrigatório', text: 'Por favor, digite o número do CNPJ.', confirmButtonColor: '#0467fc' });
            return;
        }

        var cnpjLimpo = cnpj.replace(/\D/g, '');
        if (cnpjLimpo.length !== 14) {
            Swal.fire({ icon: 'error', title: 'CNPJ inválido', text: 'O CNPJ deve ter 14 dígitos.', confirmButtonColor: '#0467fc' });
            return;
        }

        if (criarCardCNPJ(cnpj, razao)) {
            cnpjInput.val('');
            razaoInput.val('');
            Swal.fire({ icon: 'success', title: 'CNPJ Adicionado', text: 'O CNPJ foi vinculado com sucesso!', timer: 2000, showConfirmButton: false });
        }
    });

    // Remove CNPJ
    $(document).on('click', '.btn-remover-cnpj-card', function() {
        var card = $(this).closest('.cnpj-card');
        Swal.fire({
            title: 'Remover CNPJ?',
            text: 'Deseja realmente remover este CNPJ vinculado?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                card.fadeOut(300, function() {
                    card.remove();
                    atualizarContador();
                });
            }
        });
    });

    // CNPJ mask for manual input
    $('#input-cnpj-manual').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length <= 14) {
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            $(this).val(value);
        }
    });

    // Auto-suggest CNPJ on client select
    $('#select-cliente-principal').change(function() {
        var clienteId = $(this).val();
        if (clienteId) {
            var option = $(this).find('option:selected');
            var documento = option.data('documento');
            var razao = option.data('razao');

            if (documento && !cnpjJaExiste(documento)) {
                Swal.fire({
                    title: 'Adicionar CNPJ do cliente?',
                    text: 'Deseja vincular o CNPJ ' + documento + ' a este usuário?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#95a5a6',
                    confirmButtonText: 'Sim, adicionar',
                    cancelButtonText: 'Não'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        criarCardCNPJ(documento, razao);
                    }
                });
            }
        }
    });

    // Mark all permissions
    $('#btn-marcar-todos').click(function() {
        $('.checkbox-permissao').prop('checked', true);
    });

    // Unmark all permissions
    $('#btn-desmarcar-todos').click(function() {
        $('.checkbox-permissao').prop('checked', false);
    });

    // Toggle group
    $('.btn-marcar-grupo').click(function() {
        var grupoHash = $(this).data('grupo');
        var $checkboxes = $('.checkboxes-grupo[data-grupo="' + grupoHash + '"]').find('.checkbox-permissao');
        var todosMarcados = $checkboxes.length === $checkboxes.filter(':checked').length;

        if (todosMarcados) {
            $checkboxes.prop('checked', false);
            $(this).html('<i class="bx bx-check-square"></i> Marcar Grupo');
        } else {
            $checkboxes.prop('checked', true);
            $(this).html('<i class="bx bx-square"></i> Desmarcar Grupo');
        }
    });

    // Initialize counter
    atualizarContador();
});
</script>