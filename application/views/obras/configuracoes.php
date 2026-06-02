<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.config-tabs { display: flex; gap: 2px; flex-wrap: wrap; margin-bottom: 0; border-bottom: 2px solid var(--border-color, #dee2e6); padding-bottom: 0; }
.config-tab { padding: 8px 16px; cursor: pointer; font-size: 13px; font-weight: 500; color: var(--subtitle, #718096); border: 1px solid transparent; border-bottom: none; border-radius: 6px 6px 0 0; background: transparent; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
.config-tab:hover { background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.06); color: var(--title, #2d3748); }
.config-tab.active { background: var(--widget-box, #fff); color: var(--title, #2d3748); border-color: var(--border-color, #dee2e6); border-bottom: 2px solid var(--widget-box, #fff); margin-bottom: -2px; font-weight: 600; }
.config-tab .tab-count { background: rgba(var(--sidebar-accent-rgb, 4,103,252), 0.12); color: var(--sidebar-accent, #0467fc); font-size: 11px; padding: 1px 7px; border-radius: 10px; }
body[data-theme="puredark"] .config-tab.active,
body[data-theme="darkviolet"] .config-tab.active,
body[data-theme="darkorange"] .config-tab.active { background: var(--dark-2); border-color: rgba(255,255,255,0.08); }
.config-section { display: none; }
.config-section.active { display: block; }
.obra-status-color { display: inline-block; width: 16px; height: 16px; border-radius: 4px; vertical-align: middle; }
.config-add-btn { float: right; }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-cog"></i>
        </span>
        <h5>Configurações - Obras</h5>
        <div class="buttons">
            <a href="<?php echo site_url('obras'); ?>" class="button btn btn-sm btn-warning">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar</span>
            </a>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="col-12" style="margin-left:0;margin-top:8px;">
        <div class="alert alert-success">
            <i class="bx bx-check-circle"></i> <?php echo htmlspecialchars($this->session->flashdata('success')); ?>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
    <div class="col-12" style="margin-left:0;margin-top:8px;">
        <div class="alert alert-danger">
            <i class="bx bx-x-circle"></i> <?php echo htmlspecialchars($this->session->flashdata('error')); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="col-12" style="margin-left:0;margin-top:8px;">
        <div class="config-tabs">
            <div class="config-tab active" onclick="mostrarAba('geral')"><i class="bx bx-slider"></i> Geral</div>
            <div class="config-tab" onclick="mostrarAba('tipos-obra')"><i class="bx bx-building-house"></i> Tipos de Obra <span class="tab-count"><?php echo count($tipos_obra); ?></span></div>
            <div class="config-tab" onclick="mostrarAba('tipos-atividade')"><i class="bx bx-task"></i> Tipos de Atividade <span class="tab-count"><?php echo count($tipos_atividades); ?></span></div>
            <div class="config-tab" onclick="mostrarAba('status-obra')"><i class="bx bx-flag"></i> Status de Obra <span class="tab-count"><?php echo count($status_obra); ?></span></div>
            <div class="config-tab" onclick="mostrarAba('status-atividade')"><i class="bx bx-check-circle"></i> Status de Atividade <span class="tab-count"><?php echo count($status_atividade); ?></span></div>
            <div class="config-tab" onclick="mostrarAba('especialidades')"><i class="bx bx-hard-hat"></i> Especialidades <span class="tab-count"><?php echo count($especialidades); ?></span></div>
            <div class="config-tab" onclick="mostrarAba('funcoes')"><i class="bx bx-group"></i> Funções da Equipe <span class="tab-count"><?php echo count($funcoes_equipe); ?></span></div>
            <div class="config-tab" onclick="mostrarAba('notificacoes')"><i class="bx bx-bell"></i> Notificações</div>
        </div>
    </div>

    <div class="widget-box" style="margin-top: -1px; border-top-left-radius: 0; border-top-right-radius: 0;">
        <div class="widget-content nopadding tab-content">

            <!-- ABA: GERAL -->
            <div id="aba-geral" class="config-section active">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-slider"></i></span>
                    <h5>Configurações Gerais</h5>
                </div>
                <div class="widget-content" style="padding: 12px;">
                    <form method="post" action="<?php echo site_url('obras/salvarConfiguracao'); ?>">
                        <div class="mb-3">
                            <label class="form-label">Nome do Sistema de Obras</label>
                            <input type="text" name="nome_sistema" class="form-input col-6" value="<?php echo htmlspecialchars($config['nome_sistema'] ?? 'Gestão de Obras'); ?>">
                        </div>
                        <div style="display:flex;gap:16px;margin-bottom:12px;">
                            <div style="flex:1;">
                                <label class="form-label">Prazo Padrão para Início (dias)</label>
                                <input type="number" name="prazo_inicio_padrao" class="form-input" value="<?php echo (int)($config['prazo_inicio_padrao'] ?? 7); ?>" min="0">
                            </div>
                            <div style="flex:1;">
                                <label class="form-label">Prazo Padrão para Execução (dias)</label>
                                <input type="number" name="prazo_execucao_padrao" class="form-input" value="<?php echo (int)($config['prazo_execucao_padrao'] ?? 30); ?>" min="1">
                            </div>
                        </div>
                        <h5 style="margin: 16px 0 8px;">Funcionalidades</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr><td>Sistema de Atividades</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_atividades" <?php echo ($config['habilitar_atividades'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Sistema de Etapas</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_etapas" <?php echo ($config['habilitar_etapas'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Check-in/Check-out</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_checkin" <?php echo ($config['habilitar_checkin'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Geolocalização</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_gps" <?php echo ($config['habilitar_gps'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Reatendimento</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_reatendimento" <?php echo ($config['habilitar_reatendimento'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Portal do Técnico</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_portal_tecnico" <?php echo ($config['habilitar_portal_tecnico'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            </tbody>
                        </table>
                        <div style="margin-top: 12px;">
                            <button type="submit" class="button btn btn-sm btn-success">
                                <span class="button__icon"><i class="bx bx-save"></i></span>
                                <span class="button__text2">Salvar Configurações</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ABA: TIPOS DE OBRA -->
            <div id="aba-tipos-obra" class="config-section">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-building-house"></i></span>
                    <h5>Tipos de Obra</h5>
                    <div class="buttons">
                        <button class="button btn btn-sm btn-success" onclick="abrirModal('tipo-obra', null)">
                            <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                            <span class="button__text2">Novo Tipo</span>
                        </button>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <div class="alert alert-info" style="margin:8px;border-radius:6px;">
                        <i class="bx bx-info-circle"></i> Tipos de Obra categorizam as obras no cadastro e relatórios.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th style="width:40px;">Cor</th><th>Nome</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($tipos_obra as $tipo): ?>
                            <tr data-id="<?php echo (int)$tipo->id; ?>">
                                <td><span class="obra-status-color" style="background: <?php echo htmlspecialchars($tipo->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($tipo->icone); ?>"></i> <?php echo htmlspecialchars($tipo->nome); ?></td>
                                <td><?php echo htmlspecialchars($tipo->descricao ?? ''); ?></td>
                                <td class="text-nowrap">
                                    <a href="javascript:void(0)" class="btn-action btn-action-edit" title="Editar" onclick="abrirModal('tipo-obra', <?php echo (int)$tipo->id; ?>)">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#edit"/></svg>
                                    </a>
                                    <a href="javascript:void(0)" class="btn-action btn-action-delete" title="Excluir" onclick="excluirItem('tipo-obra', <?php echo (int)$tipo->id; ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#delete"/></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA: TIPOS DE ATIVIDADE -->
            <div id="aba-tipos-atividade" class="config-section">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-task"></i></span>
                    <h5>Tipos de Atividade</h5>
                    <div class="buttons">
                        <button class="button btn btn-sm btn-success" onclick="abrirModal('tipo-atividade', null)">
                            <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                            <span class="button__text2">Novo Tipo</span>
                        </button>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <div class="alert alert-info" style="margin:8px;border-radius:6px;">
                        <i class="bx bx-info-circle"></i> Tipos de Atividade definem as categorias de trabalho nas obras.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th style="width:40px;">Cor</th><th>Nome</th><th>Categoria</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($tipos_atividades as $tipo): ?>
                            <tr data-id="<?php echo (int)($tipo->idTipo ?? $tipo->id); ?>">
                                <td><span class="obra-status-color" style="background: <?php echo htmlspecialchars($tipo->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($tipo->icone); ?>"></i> <?php echo htmlspecialchars($tipo->nome); ?></td>
                                <td><span class="label"><?php echo htmlspecialchars($tipo->categoria ?? 'outro'); ?></span></td>
                                <td><?php echo htmlspecialchars($tipo->descricao ?? ''); ?></td>
                                <td class="text-nowrap">
                                    <a href="javascript:void(0)" class="btn-action btn-action-edit" title="Editar" onclick="abrirModal('tipo-atividade', <?php echo (int)($tipo->idTipo ?? $tipo->id); ?>)">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#edit"/></svg>
                                    </a>
                                    <a href="javascript:void(0)" class="btn-action btn-action-delete" title="Excluir" onclick="excluirItem('tipo-atividade', <?php echo (int)($tipo->idTipo ?? $tipo->id); ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#delete"/></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA: STATUS DE OBRA -->
            <div id="aba-status-obra" class="config-section">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-flag"></i></span>
                    <h5>Status de Obra</h5>
                    <div class="buttons">
                        <button class="button btn btn-sm btn-success" onclick="abrirModal('status-obra', null)">
                            <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                            <span class="button__text2">Novo Status</span>
                        </button>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <div class="alert alert-warning" style="margin:8px;border-radius:6px;">
                        <i class="bx bx-error-circle"></i> <strong>Atenção:</strong> Alterar status padrão pode afetar relatórios existentes.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th style="width:40px;">Cor</th><th>Nome</th><th>Ordem</th><th>Finalizado</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($status_obra as $status): ?>
                            <tr data-id="<?php echo (int)$status->id; ?>">
                                <td><span class="obra-status-color" style="background: <?php echo htmlspecialchars($status->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($status->icone); ?>"></i> <?php echo htmlspecialchars($status->nome); ?></td>
                                <td><?php echo (int)$status->ordem; ?></td>
                                <td><?php echo ($status->finalizado ?? false) ? '<span class="badge bg-success">Sim</span>' : '<span class="label">Não</span>'; ?></td>
                                <td><?php echo htmlspecialchars($status->descricao ?? ''); ?></td>
                                <td class="text-nowrap">
                                    <a href="javascript:void(0)" class="btn-action btn-action-edit" title="Editar" onclick="abrirModal('status-obra', <?php echo (int)$status->id; ?>)">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#edit"/></svg>
                                    </a>
                                    <?php if (!in_array($status->nome, ['Prospeccao', 'Em Andamento', 'Concluida'])): ?>
                                    <a href="javascript:void(0)" class="btn-action btn-action-delete" title="Excluir" onclick="excluirItem('status-obra', <?php echo (int)$status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#delete"/></svg>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA: STATUS DE ATIVIDADE -->
            <div id="aba-status-atividade" class="config-section">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-check-circle"></i></span>
                    <h5>Status de Atividade</h5>
                    <div class="buttons">
                        <button class="button btn btn-sm btn-success" onclick="abrirModal('status-atividade', null)">
                            <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                            <span class="button__text2">Novo Status</span>
                        </button>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <div class="alert alert-info" style="margin:8px;border-radius:6px;">
                        <i class="bx bx-info-circle"></i> Fluxo padrão: Agendada → Iniciada → Pausada (opcional) → Concluída/Cancelada
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th style="width:40px;">Cor</th><th>Nome</th><th>Fluxo</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($status_atividade as $status): ?>
                            <tr data-id="<?php echo (int)$status->id; ?>">
                                <td><span class="obra-status-color" style="background: <?php echo htmlspecialchars($status->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($status->icone); ?>"></i> <?php echo htmlspecialchars($status->nome); ?></td>
                                <td><span class="label"><?php echo htmlspecialchars($status->fluxo ?? 'normal'); ?></span></td>
                                <td><?php echo htmlspecialchars($status->descricao ?? ''); ?></td>
                                <td class="text-nowrap">
                                    <a href="javascript:void(0)" class="btn-action btn-action-edit" title="Editar" onclick="abrirModal('status-atividade', <?php echo (int)$status->id; ?>)">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#edit"/></svg>
                                    </a>
                                    <?php if (!in_array($status->nome, ['Agendada', 'Iniciada', 'Concluida'])): ?>
                                    <a href="javascript:void(0)" class="btn-action btn-action-delete" title="Excluir" onclick="excluirItem('status-atividade', <?php echo (int)$status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#delete"/></svg>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA: ESPECIALIDADES -->
            <div id="aba-especialidades" class="config-section">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-hard-hat"></i></span>
                    <h5>Especialidades (Etapas)</h5>
                    <div class="buttons">
                        <button class="button btn btn-sm btn-success" onclick="abrirModal('especialidade', null)">
                            <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                            <span class="button__text2">Nova Especialidade</span>
                        </button>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <div class="alert alert-info" style="margin:8px;border-radius:6px;">
                        <i class="bx bx-info-circle"></i> Especialidades classificam as etapas da obra (Ex: Elétrica, Hidráulica, Acabamento).
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th style="width:40px;">Cor</th><th>Nome</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($especialidades as $esp): ?>
                            <tr data-id="<?php echo (int)$esp->id; ?>">
                                <td><span class="obra-status-color" style="background: <?php echo htmlspecialchars($esp->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($esp->icone); ?>"></i> <?php echo htmlspecialchars($esp->nome); ?></td>
                                <td><?php echo htmlspecialchars($esp->descricao ?? ''); ?></td>
                                <td class="text-nowrap">
                                    <a href="javascript:void(0)" class="btn-action btn-action-edit" title="Editar" onclick="abrirModal('especialidade', <?php echo (int)$esp->id; ?>)">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#edit"/></svg>
                                    </a>
                                    <a href="javascript:void(0)" class="btn-action btn-action-delete" title="Excluir" onclick="excluirItem('especialidade', <?php echo (int)$esp->id; ?>, '<?php echo htmlspecialchars($esp->nome); ?>')">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#delete"/></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA: FUNÇÕES DA EQUIPE -->
            <div id="aba-funcoes" class="config-section">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-group"></i></span>
                    <h5>Funções da Equipe</h5>
                    <div class="buttons">
                        <button class="button btn btn-sm btn-success" onclick="abrirModal('funcao', null)">
                            <span class="button__icon"><i class="bx bx-plus-circle"></i></span>
                            <span class="button__text2">Nova Função</span>
                        </button>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <div class="alert alert-info" style="margin:8px;border-radius:6px;">
                        <i class="bx bx-info-circle"></i> Funções definem os papéis dos membros da equipe na obra.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Nome</th><th>Nível</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($funcoes_equipe as $funcao):
                                $nivelCor = ['alto' => '#e74c3c', 'medio' => '#f39c12', 'baixo' => '#27ae60'][($funcao->nivel ?? 'baixo')] ?? '#95a5a6';
                            ?>
                            <tr data-id="<?php echo (int)$funcao->id; ?>">
                                <td><?php echo htmlspecialchars($funcao->nome); ?></td>
                                <td><span class="label" style="background: <?php echo $nivelCor; ?>; color: white;"><?php echo htmlspecialchars($funcao->nivel ?? 'baixo'); ?></span></td>
                                <td><?php echo htmlspecialchars($funcao->descricao ?? ''); ?></td>
                                <td class="text-nowrap">
                                    <a href="javascript:void(0)" class="btn-action btn-action-edit" title="Editar" onclick="abrirModal('funcao', <?php echo (int)$funcao->id; ?>)">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#edit"/></svg>
                                    </a>
                                    <a href="javascript:void(0)" class="btn-action btn-action-delete" title="Excluir" onclick="excluirItem('funcao', <?php echo (int)$funcao->id; ?>, '<?php echo htmlspecialchars($funcao->nome); ?>')">
                                        <svg><use href="<?php echo base_url(); ?>assets/svg/icons.svg#delete"/></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA: NOTIFICAÇÕES -->
            <div id="aba-notificacoes" class="config-section">
                <div class="widget-title" style="margin:0;">
                    <span class="icon"><i class="bx bx-bell"></i></span>
                    <h5>Configurações de Notificações</h5>
                </div>
                <div class="widget-content" style="padding: 12px;">
                    <form method="post" action="<?php echo site_url('obras/salvarConfiguracaoNotificacoes'); ?>">
                        <h5 style="margin: 0 0 8px;">Eventos que geram notificações</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr><td>Nova obra cadastrada</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_nova_obra" <?php echo ($config_notif['nova_obra'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Obra concluída</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_obra_concluida" <?php echo ($config_notif['obra_concluida'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Atividade atrasada</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_atividade_atrasada" <?php echo ($config_notif['atividade_atrasada'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Atividade reaberta</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_atividade_reaberta" <?php echo ($config_notif['atividade_reaberta'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Check-in do técnico</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_checkin" <?php echo ($config_notif['checkin'] ?? false) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Impedimento registrado</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_impedimento" <?php echo ($config_notif['impedimento'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            </tbody>
                        </table>
                        <h5 style="margin: 16px 0 8px;">Canais de Notificação</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr><td>E-mail</td><td class="text-center" style="width:60px;"><input type="checkbox" name="canal_email" <?php echo ($config_notif['canal_email'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Notificação no Sistema</td><td class="text-center" style="width:60px;"><input type="checkbox" name="canal_sistema" <?php echo ($config_notif['canal_sistema'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            </tbody>
                        </table>
                        <div style="margin-top: 12px;">
                            <button type="submit" class="button btn btn-sm btn-success">
                                <span class="button__icon"><i class="bx bx-save"></i></span>
                                <span class="button__text2">Salvar Configurações</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL DE EDIÇÃO -->
<div id="modalEditar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 10000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="modalEditarTitulo">Editar Item</h3>
            </div>
            <div class="modal-body" id="modalEditarBody">
            </div>
            <div class="modal-footer">
                <button class="btn" data-bs-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button class="btn btn-primary" onclick="salvarModal()"><i class="bx bx-save"></i> Salvar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function mostrarAba(abaId) {
    document.querySelectorAll('.config-tab').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.config-section').forEach(function(s) { s.classList.remove('active'); });
    var tabMap = {
        'geral': 0, 'tipos-obra': 1, 'tipos-atividade': 2, 'status-obra': 3,
        'status-atividade': 4, 'especialidades': 5, 'funcoes': 6, 'notificacoes': 7
    };
    var tabs = document.querySelectorAll('.config-tab');
    if (tabs[tabMap[abaId]]) tabs[tabMap[abaId]].classList.add('active');
    var alvo = document.getElementById('aba-' + abaId);
    if (alvo) alvo.classList.add('active');
}

var TIPO_ATUAL = null;
var ITEM_EDITANDO = null;

var URLS = {
    'tipo-obra': '<?php echo site_url("obras/salvarTipoObra"); ?>',
    'tipo-atividade': '<?php echo site_url("obras/salvarTipoAtividade"); ?>',
    'status-obra': '<?php echo site_url("obras/salvarStatusObra"); ?>',
    'status-atividade': '<?php echo site_url("obras/salvarStatusAtividade"); ?>',
    'especialidade': '<?php echo site_url("obras/salvarEspecialidade"); ?>',
    'funcao': '<?php echo site_url("obras/salvarFuncao"); ?>'
};

var URLS_EXCLUIR = {
    'tipo-obra': '<?php echo site_url("obras/excluirTipoObra"); ?>',
    'tipo-atividade': '<?php echo site_url("obras/excluirTipoAtividade"); ?>',
    'status-obra': '<?php echo site_url("obras/excluirStatusObra"); ?>',
    'status-atividade': '<?php echo site_url("obras/excluirStatusAtividade"); ?>',
    'especialidade': '<?php echo site_url("obras/excluirEspecialidade"); ?>',
    'funcao': '<?php echo site_url("obras/excluirFuncao"); ?>'
};

function opcoesIcone(selecionado) {
    var icones = [
        ['bx-building', 'Prédio'], ['bx-home', 'Casa'], ['bx-brush', 'Pincel'], ['bx-wrench', 'Ferramenta'],
        ['bx-plug', 'Plug'], ['bx-box', 'Caixa'], ['bx-hard-hat', 'Capacete'], ['bx-bolt-circle', 'Raio'],
        ['bx-flag', 'Bandeira'], ['bx-calendar', 'Calendário'], ['bx-check-circle', 'Check'], ['bx-search', 'Lupa'],
        ['bx-cog', 'Engrenagem'], ['bx-block', 'Bloqueio'], ['bx-task', 'Tarefa'], ['bx-user', 'Usuário'],
        ['bx-group', 'Grupo'], ['bx-bell', 'Sino'], ['bx-star', 'Estrela'], ['bx-heart', 'Coração']
    ];
    var opts = '';
    for (var i = 0; i < icones.length; i++) {
        opts = opts + '<option value="' + icones[i][0] + '"' + (icones[i][0] === selecionado ? ' selected' : '') + '>' + icones[i][1] + '</option>';
    }
    return opts;
}

function escapeHtml(texto) {
    if (!texto) return '';
    return texto.toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function abrirModal(tipo, id) {
    TIPO_ATUAL = tipo;
    ITEM_EDITANDO = id;

    var titulo = document.getElementById('modalEditarTitulo');
    var body = document.getElementById('modalEditarBody');
    var html = '';
    var nomeItem = '';
    var descricaoItem = '';
    var corItem = '#3498db';
    var iconeItem = 'bx-building';
    var categoriaItem = 'outro';
    var duracaoItem = 30;
    var ordemItem = 1;
    var finalizadoItem = false;
    var fluxoItem = 'normal';
    var nivelItem = 'baixo';

    if (id !== null) {
        var linha = document.querySelector('tr[data-id="' + id + '"]');
        if (linha) {
            var tds = linha.getElementsByTagName('td');
            if (tds.length > 1) nomeItem = (tds[1].innerText || tds[1].textContent || '').replace(/^\s+|\s+$/g, '');
            if (tds.length > 2) {
                if (tipo === 'status-obra') {
                    ordemItem = parseInt((tds[2].innerText || tds[2].textContent || '1'), 10) || 1;
                    finalizadoItem = (tds[3].innerText || tds[3].textContent || '').indexOf('Sim') !== -1;
                    descricaoItem = (tds[4].innerText || tds[4].textContent || '').replace(/^\s+|\s+$/g, '');
                } else if (tipo === 'status-atividade') {
                    fluxoItem = (tds[2].innerText || tds[2].textContent || '').replace(/^\s+|\s+$/g, '').toLowerCase();
                    descricaoItem = (tds[3].innerText || tds[3].textContent || '').replace(/^\s+|\s+$/g, '');
                } else if (tipo === 'funcao') {
                    nivelItem = (tds[1].innerText || tds[1].textContent || '').replace(/^\s+|\s+$/g, '').toLowerCase();
                    descricaoItem = (tds[2].innerText || tds[2].textContent || '').replace(/^\s+|\s+$/g, '');
                } else if (tipo === 'tipo-atividade') {
                    categoriaItem = (tds[2].innerText || tds[2].textContent || '').replace(/^\s+|\s+$/g, '').toLowerCase();
                    descricaoItem = (tds[3].innerText || tds[3].textContent || '').replace(/^\s+|\s+$/g, '');
                } else {
                    descricaoItem = (tds[2].innerText || tds[2].textContent || '').replace(/^\s+|\s+$/g, '');
                }
            }
            var spanCor = linha.querySelector('.obra-status-color');
            if (spanCor) {
                var estilo = spanCor.getAttribute('style') || '';
                var match = estilo.match(/background:\s*([^;]+)/);
                if (match) corItem = match[1].replace(/^\s+|\s+$/g, '');
            }
        }
    }

    if (tipo === 'tipo-obra') {
        titulo.innerHTML = (id ? 'Editar' : 'Novo') + ' Tipo de Obra';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div style="display:flex;gap:16px;"><div style="flex:1;"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div style="flex:1;"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'tipo-atividade') {
        titulo.innerHTML = (id ? 'Editar' : 'Novo') + ' Tipo de Atividade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div style="display:flex;gap:16px;"><div style="flex:1;"><label class="form-label">Categoria</label><select id="f_categoria" class="form-select"><option value="execucao"' + (categoriaItem === 'execucao' ? ' selected' : '') + '>Execução</option><option value="visita"' + (categoriaItem === 'visita' ? ' selected' : '') + '>Visita</option><option value="manutencao"' + (categoriaItem === 'manutencao' ? ' selected' : '') + '>Manutenção</option><option value="impedimento"' + (categoriaItem === 'impedimento' ? ' selected' : '') + '>Impedimento</option><option value="outro"' + (categoriaItem === 'outro' ? ' selected' : '') + '>Outro</option></select></div>' +
            '<div style="flex:1;"><label class="form-label">Duração (min)</label><input type="number" id="f_duracao" class="form-input" value="' + duracaoItem + '" min="5"></div></div>' +
            '<div style="display:flex;gap:16px;margin-top:8px;"><div style="flex:1;"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div style="flex:1;"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'status-obra') {
        titulo.innerHTML = (id ? 'Editar' : 'Novo') + ' Status de Obra';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div style="display:flex;gap:16px;"><div style="flex:1;"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div style="flex:1;"><label class="form-label">Ordem</label><input type="number" id="f_ordem" class="form-input" value="' + ordemItem + '" min="1"></div><div style="flex:1;"><label class="form-label">Finalizado?</label><div style="margin-top:6px;"><input type="checkbox" id="f_finalizado"' + (finalizadoItem ? ' checked' : '') + '> <label for="f_finalizado">Sim</label></div></div></div>' +
            '<div class="mb-3" style="margin-top:8px;"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div>';
    } else if (tipo === 'status-atividade') {
        titulo.innerHTML = (id ? 'Editar' : 'Novo') + ' Status de Atividade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div style="display:flex;gap:16px;"><div style="flex:1;"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div style="flex:1;"><label class="form-label">Fluxo</label><select id="f_fluxo" class="form-select"><option value="inicial"' + (fluxoItem === 'inicial' ? ' selected' : '') + '>Inicial</option><option value="normal"' + (fluxoItem === 'normal' ? ' selected' : '') + '>Normal</option><option value="pausa"' + (fluxoItem === 'pausa' ? ' selected' : '') + '>Pausa</option><option value="final"' + (fluxoItem === 'final' ? ' selected' : '') + '>Final</option></select></div></div>' +
            '<div class="mb-3" style="margin-top:8px;"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div>';
    } else if (tipo === 'especialidade') {
        titulo.innerHTML = (id ? 'Editar' : 'Nova') + ' Especialidade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div style="display:flex;gap:16px;"><div style="flex:1;"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div style="flex:1;"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'funcao') {
        titulo.innerHTML = (id ? 'Editar' : 'Nova') + ' Função';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div class="mb-3"><label class="form-label">Nível</label><select id="f_nivel" class="form-select"><option value="baixo"' + (nivelItem === 'baixo' ? ' selected' : '') + '>Baixo</option><option value="medio"' + (nivelItem === 'medio' ? ' selected' : '') + '>Médio</option><option value="alto"' + (nivelItem === 'alto' ? ' selected' : '') + '>Alto</option></select></div>';
    }

    body.innerHTML = html;
    $('#modalEditar').modal('show');
}

function salvarModal() {
    if (!TIPO_ATUAL) { alert('Nenhum tipo selecionado'); return; }

    var dados = {
        id: document.getElementById('f_id').value,
        nome: document.getElementById('f_nome').value,
        descricao: document.getElementById('f_descricao').value
    };
    if (!dados.nome) { alert('Nome é obrigatório'); return; }

    if (document.getElementById('f_cor')) dados.cor = document.getElementById('f_cor').value;
    if (document.getElementById('f_icone')) dados.icone = document.getElementById('f_icone').value;
    if (document.getElementById('f_categoria')) dados.categoria = document.getElementById('f_categoria').value;
    if (document.getElementById('f_duracao')) dados.duracao = document.getElementById('f_duracao').value;
    if (document.getElementById('f_ordem')) dados.ordem = document.getElementById('f_ordem').value;
    if (document.getElementById('f_finalizado')) dados.finalizado = document.getElementById('f_finalizado').checked ? 1 : 0;
    if (document.getElementById('f_fluxo')) dados.fluxo = document.getElementById('f_fluxo').value;
    if (document.getElementById('f_nivel')) dados.nivel = document.getElementById('f_nivel').value;

    $.ajax({
        url: URLS[TIPO_ATUAL],
        type: 'POST',
        data: dados,
        dataType: 'json',
        success: function(resp) {
            if (resp && resp.success) {
                $('#modalEditar').modal('hide');
                location.reload();
            } else {
                alert('Erro: ' + (resp && resp.message ? resp.message : 'Erro ao salvar'));
            }
        },
        error: function(xhr, status, error) {
            alert('Erro ao salvar. Verifique o console (F12).');
        }
    });
}

function excluirItem(tipo, id, nome) {
    if (!confirm('Tem certeza que deseja excluir "' + nome + '"?')) return;
    $.ajax({
        url: URLS_EXCLUIR[tipo],
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(resp) {
            if (resp && resp.success) {
                location.reload();
            } else {
                alert('Erro: ' + (resp && resp.message ? resp.message : 'Erro ao excluir'));
            }
        },
        error: function() {
            alert('Erro ao excluir. Verifique o console.');
        }
    });
}
</script>