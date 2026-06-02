<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<div class="obras-config-container">
    <div class="obras-config-content" style="max-width: 100%; flex: unset; padding: 0;">
        <div class="obras-main-header">
            <div class="obras-header-content">
                <div class="obras-header-title">
                    <h1><?= svg_icon('cog', 28, 28) ?> Configurações do Sistema de Obras</h1>
                    <p>Gerencie tipos, status, especialidades, funções e preferências</p>
                </div>
                <div class="obras-header-actions">
                    <a href="<?php echo site_url('obras'); ?>" class="obras-filter-btn secondary">
                        <?= svg_icon('chevron-left', 16, 16) ?> Voltar às Obras
                    </a>
                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
        <div class="obras-alert-success">
            <?= svg_icon('check-circle', 20, 20) ?> <strong><?php echo htmlspecialchars($this->session->flashdata('success')); ?></strong>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
        <div class="obras-alert-error">
            <?= svg_icon('x', 20, 20) ?> <strong><?php echo htmlspecialchars($this->session->flashdata('error')); ?></strong>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="obras-config-container">
    <div class="obras-config-sidebar">
        <div id="aba-btn-geral" class="aba-menu active" onclick="mostrarAba('geral')">
            <i class="bx bx-slider"></i> <span>Geral</span>
        </div>
        <div id="aba-btn-tipos-obra" class="aba-menu" onclick="mostrarAba('tipos-obra')">
            <i class="bx bx-building-house"></i> <span>Tipos de Obra</span>
            <span class="aba-menu-count"><?php echo count($tipos_obra); ?></span>
        </div>
        <div id="aba-btn-tipos-atividade" class="aba-menu" onclick="mostrarAba('tipos-atividade')">
            <i class="bx bx-task"></i> <span>Tipos de Atividade</span>
            <span class="aba-menu-count"><?php echo count($tipos_atividades); ?></span>
        </div>
        <div id="aba-btn-status-obra" class="aba-menu" onclick="mostrarAba('status-obra')">
            <i class="bx bx-flag"></i> <span>Status de Obra</span>
            <span class="aba-menu-count"><?php echo count($status_obra); ?></span>
        </div>
        <div id="aba-btn-status-atividade" class="aba-menu" onclick="mostrarAba('status-atividade')">
            <i class="bx bx-check-circle"></i> <span>Status de Atividade</span>
            <span class="aba-menu-count"><?php echo count($status_atividade); ?></span>
        </div>
        <div id="aba-btn-especialidades" class="aba-menu" onclick="mostrarAba('especialidades')">
            <i class="bx bx-hard-hat"></i> <span>Especialidades</span>
            <span class="aba-menu-count"><?php echo count($especialidades); ?></span>
        </div>
        <div id="aba-btn-funcoes" class="aba-menu" onclick="mostrarAba('funcoes')">
            <i class="bx bx-group"></i> <span>Funções da Equipe</span>
            <span class="aba-menu-count"><?php echo count($funcoes_equipe); ?></span>
        </div>
        <div id="aba-btn-notificacoes" class="aba-menu" onclick="mostrarAba('notificacoes')">
            <i class="bx bx-bell"></i> <span>Notificações</span>
        </div>
    </div>

    <div class="obras-config-content">
        <div id="aba-geral" class="config-section active">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('cog', 22, 22) ?> Configurações Gerais</div>
                </div>
                <div class="widget-content">
                    <form method="post" action="<?php echo site_url('obras/salvarConfiguracao'); ?>">
                        <div class="mb-3">
                            <label class="form-label">Nome do Sistema de Obras</label>
                            <div class="controls">
                                <input type="text" name="nome_sistema" class="form-input col-6" value="<?php echo htmlspecialchars($config['nome_sistema'] ?? 'Gestão de Obras'); ?>">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Prazo Padrão para Início (dias)</label>
                                <input type="number" name="prazo_inicio_padrao" class="form-input" value="<?php echo (int)($config['prazo_inicio_padrao'] ?? 7); ?>" min="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Prazo Padrão para Execução (dias)</label>
                                <input type="number" name="prazo_execucao_padrao" class="form-input" value="<?php echo (int)($config['prazo_execucao_padrao'] ?? 30); ?>" min="1">
                            </div>
                        </div>
                        <hr class="obras-modal-divider">
                        <h5 style="margin: 0 0 12px 0;">Funcionalidades</h5>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr><td>Sistema de Atividades</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_atividades" <?php echo ($config['habilitar_atividades'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Sistema de Etapas</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_etapas" <?php echo ($config['habilitar_etapas'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Check-in/Check-out</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_checkin" <?php echo ($config['habilitar_checkin'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Geolocalização</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_gps" <?php echo ($config['habilitar_gps'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Reatendimento</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_reatendimento" <?php echo ($config['habilitar_reatendimento'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Portal do Técnico</td><td class="text-center" style="width:60px;"><input type="checkbox" name="habilitar_portal_tecnico" <?php echo ($config['habilitar_portal_tecnico'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            </tbody>
                        </table>
                        <div class="form-actions-bar">
                            <button type="submit" class="form-btn form-btn-primary"><?= svg_icon('save', 16, 16) ?> Salvar Configurações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="aba-tipos-obra" class="config-section">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('building-house', 22, 22) ?> Tipos de Obra</div>
                    <button class="form-btn form-btn-primary" onclick="abrirModal('tipo-obra', null)"><?= svg_icon('plus', 16, 16) ?> Novo Tipo</button>
                </div>
                <div class="widget-content">
                    <div class="obras-alert-info">
                        Tipos de Obra categorizam as obras no cadastro e relatórios.
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th style="width:40px;">Cor</th><th>Nome</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($tipos_obra as $tipo): ?>
                            <tr data-id="<?php echo (int)$tipo->id; ?>">
                                <td><span class="obra-status-color" style="background: <?php echo htmlspecialchars($tipo->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($tipo->icone); ?>"></i> <?php echo htmlspecialchars($tipo->nome); ?></td>
                                <td><?php echo htmlspecialchars($tipo->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-sm" onclick="abrirModal('tipo-obra', <?php echo (int)$tipo->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="excluirItem('tipo-obra', <?php echo (int)$tipo->id; ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="aba-tipos-atividade" class="config-section">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('list-check', 22, 22) ?> Tipos de Atividade</div>
                    <button class="form-btn form-btn-primary" onclick="abrirModal('tipo-atividade', null)"><?= svg_icon('plus', 16, 16) ?> Novo Tipo</button>
                </div>
                <div class="widget-content">
                    <div class="obras-alert-info">
                        Tipos de Atividade definem as categorias de trabalho nas obras.
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
                                <td>
                                    <button class="btn btn-sm" onclick="abrirModal('tipo-atividade', <?php echo (int)($tipo->idTipo ?? $tipo->id); ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="excluirItem('tipo-atividade', <?php echo (int)($tipo->idTipo ?? $tipo->id); ?>, '<?php echo htmlspecialchars($tipo->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="aba-status-obra" class="config-section">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('flag', 22, 22) ?> Status de Obra</div>
                    <button class="form-btn form-btn-primary" onclick="abrirModal('status-obra', null)"><?= svg_icon('plus', 16, 16) ?> Novo Status</button>
                </div>
                <div class="widget-content">
                    <div class="alert alert-warning">
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
                                <td>
                                    <button class="btn btn-sm" onclick="abrirModal('status-obra', <?php echo (int)$status->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <?php if (!in_array($status->nome, ['Prospeccao', 'Em Andamento', 'Concluida'])): ?>
                                    <button class="btn btn-sm btn-danger" onclick="excluirItem('status-obra', <?php echo (int)$status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')"><i class="bx bx-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="aba-status-atividade" class="config-section">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('check-circle', 22, 22) ?> Status de Atividade</div>
                    <button class="form-btn form-btn-primary" onclick="abrirModal('status-atividade', null)"><?= svg_icon('plus', 16, 16) ?> Novo Status</button>
                </div>
                <div class="widget-content">
                    <div class="obras-alert-info">
                        Fluxo padrão: Agendada → Iniciada → Pausada (opcional) → Concluída/Cancelada
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
                                <td>
                                    <button class="btn btn-sm" onclick="abrirModal('status-atividade', <?php echo (int)$status->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <?php if (!in_array($status->nome, ['Agendada', 'Iniciada', 'Concluida'])): ?>
                                    <button class="btn btn-sm btn-danger" onclick="excluirItem('status-atividade', <?php echo (int)$status->id; ?>, '<?php echo htmlspecialchars($status->nome); ?>')"><i class="bx bx-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="aba-especialidades" class="config-section">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('hard-hat', 22, 22) ?> Especialidades (Etapas)</div>
                    <button class="form-btn form-btn-primary" onclick="abrirModal('especialidade', null)"><?= svg_icon('plus', 16, 16) ?> Nova Especialidade</button>
                </div>
                <div class="widget-content">
                    <div class="obras-alert-info">
                        Especialidades classificam as etapas da obra (Ex: Elétrica, Hidráulica, Acabamento).
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th style="width:40px;">Cor</th><th>Nome</th><th>Descrição</th><th style="width:100px;">Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($especialidades as $esp): ?>
                            <tr data-id="<?php echo (int)$esp->id; ?>">
                                <td><span class="obra-status-color" style="background: <?php echo htmlspecialchars($esp->cor); ?>;"></span></td>
                                <td><i class="bx <?php echo htmlspecialchars($esp->icone); ?>"></i> <?php echo htmlspecialchars($esp->nome); ?></td>
                                <td><?php echo htmlspecialchars($esp->descricao ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-sm" onclick="abrirModal('especialidade', <?php echo (int)$esp->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="excluirItem('especialidade', <?php echo (int)$esp->id; ?>, '<?php echo htmlspecialchars($esp->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="aba-funcoes" class="config-section">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('group', 22, 22) ?> Funções da Equipe</div>
                    <button class="form-btn form-btn-primary" onclick="abrirModal('funcao', null)"><?= svg_icon('plus', 16, 16) ?> Nova Função</button>
                </div>
                <div class="widget-content">
                    <div class="obras-alert-info">
                        Funções definem os papéis dos membros da equipe na obra.
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
                                <td>
                                    <button class="btn btn-sm" onclick="abrirModal('funcao', <?php echo (int)$funcao->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="excluirItem('funcao', <?php echo (int)$funcao->id; ?>, '<?php echo htmlspecialchars($funcao->nome); ?>')"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="aba-notificacoes" class="config-section">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><?= svg_icon('bell', 22, 22) ?> Configurações de Notificações</div>
                </div>
                <div class="widget-content">
                    <form method="post" action="<?php echo site_url('obras/salvarConfiguracaoNotificacoes'); ?>">
                        <h5>Eventos que geram notificações</h5>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr><td>Nova obra cadastrada</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_nova_obra" <?php echo ($config_notif['nova_obra'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Obra concluída</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_obra_concluida" <?php echo ($config_notif['obra_concluida'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Atividade atrasada</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_atividade_atrasada" <?php echo ($config_notif['atividade_atrasada'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Atividade reaberta</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_atividade_reaberta" <?php echo ($config_notif['atividade_reaberta'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Check-in do técnico</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_checkin" <?php echo ($config_notif['checkin'] ?? false) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Impedimento registrado</td><td class="text-center" style="width:60px;"><input type="checkbox" name="notif_impedimento" <?php echo ($config_notif['impedimento'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            </tbody>
                        </table>
                        <hr class="obras-modal-divider">
                        <h5>Canais de Notificação</h5>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr><td>E-mail</td><td class="text-center" style="width:60px;"><input type="checkbox" name="canal_email" <?php echo ($config_notif['canal_email'] ?? true) ? 'checked' : ''; ?>></td></tr>
                                <tr><td>Notificação no Sistema</td><td class="text-center" style="width:60px;"><input type="checkbox" name="canal_sistema" <?php echo ($config_notif['canal_sistema'] ?? true) ? 'checked' : ''; ?>></td></tr>
                            </tbody>
                        </table>
                        <div class="form-actions-bar">
                            <button type="submit" class="form-btn form-btn-primary"><?= svg_icon('save', 16, 16) ?> Salvar Configurações</button>
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
    var botoes = document.getElementsByClassName('aba-menu');
    for (var i = 0; i < botoes.length; i++) {
        botoes[i].classList.remove('active');
    }
    var btn = document.getElementById('aba-btn-' + abaId);
    if (btn) {
        btn.classList.add('active');
    }
    var caixas = document.getElementsByClassName('config-section');
    for (var j = 0; j < caixas.length; j++) {
        caixas[j].classList.remove('active');
    }
    var alvo = document.getElementById('aba-' + abaId);
    if (alvo) {
        alvo.classList.add('active');
    }
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
            '<div class="form-grid"><div class="form-group"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div class="form-group"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'tipo-atividade') {
        titulo.innerHTML = (id ? 'Editar' : 'Novo') + ' Tipo de Atividade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div class="form-grid"><div class="form-group"><label class="form-label">Categoria</label><select id="f_categoria" class="form-select"><option value="execucao"' + (categoriaItem === 'execucao' ? ' selected' : '') + '>Execução</option><option value="visita"' + (categoriaItem === 'visita' ? ' selected' : '') + '>Visita</option><option value="manutencao"' + (categoriaItem === 'manutencao' ? ' selected' : '') + '>Manutenção</option><option value="impedimento"' + (categoriaItem === 'impedimento' ? ' selected' : '') + '>Impedimento</option><option value="outro"' + (categoriaItem === 'outro' ? ' selected' : '') + '>Outro</option></select></div>' +
            '<div class="form-group"><label class="form-label">Duração (min)</label><input type="number" id="f_duracao" class="form-input" value="' + duracaoItem + '" min="5"></div>' +
            '<div class="form-group"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div>' +
            '<div class="form-group"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div></div>';
    } else if (tipo === 'status-obra') {
        titulo.innerHTML = (id ? 'Editar' : 'Novo') + ' Status de Obra';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div class="form-grid"><div class="form-group"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div class="form-group"><label class="form-label">Ordem</label><input type="number" id="f_ordem" class="form-input" value="' + ordemItem + '" min="1"></div><div class="form-group"><label class="form-label">Finalizado?</label><div class="checkbox-container"><input type="checkbox" id="f_finalizado"' + (finalizadoItem ? ' checked' : '') + '><label for="f_finalizado">Sim</label></div></div></div>' +
            '<div class="mb-3"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div>';
    } else if (tipo === 'status-atividade') {
        titulo.innerHTML = (id ? 'Editar' : 'Novo') + ' Status de Atividade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div class="form-grid"><div class="form-group"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div class="form-group"><label class="form-label">Fluxo</label><select id="f_fluxo" class="form-select"><option value="inicial"' + (fluxoItem === 'inicial' ? ' selected' : '') + '>Inicial</option><option value="normal"' + (fluxoItem === 'normal' ? ' selected' : '') + '>Normal</option><option value="pausa"' + (fluxoItem === 'pausa' ? ' selected' : '') + '>Pausa</option><option value="final"' + (fluxoItem === 'final' ? ' selected' : '') + '>Final</option></select></div></div>' +
            '<div class="mb-3"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div>';
    } else if (tipo === 'especialidade') {
        titulo.innerHTML = (id ? 'Editar' : 'Nova') + ' Especialidade';
        html = '<input type="hidden" id="f_id" value="' + (id || '') + '">' +
            '<div class="mb-3"><label class="form-label">Nome</label><input type="text" id="f_nome" class="form-input" value="' + escapeHtml(nomeItem) + '" required></div>' +
            '<div class="mb-3"><label class="form-label">Descrição</label><textarea id="f_descricao" class="form-textarea" rows="2">' + escapeHtml(descricaoItem) + '</textarea></div>' +
            '<div class="form-grid"><div class="form-group"><label class="form-label">Cor</label><input type="color" id="f_cor" value="' + corItem + '" style="width:60px;height:40px;"></div><div class="form-group"><label class="form-label">Ícone</label><select id="f_icone" class="form-select">' + opcoesIcone(iconeItem) + '</select></div></div>';
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