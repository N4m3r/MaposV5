<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
/* ===== ESTILOS PRINCIPAIS ===== */
.obras-container { padding: 24px; max-width: 1600px; margin: 0 auto; }

/* Header */
.obra-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 32px;
    color: white;
    margin-bottom: 24px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.obra-header-content { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; }
.obra-header-left { flex: 1; }
.obra-breadcrumb { font-size: 14px; opacity: 0.9; margin-bottom: 8px; }
.obra-breadcrumb a { color: rgba(255,255,255,0.8); text-decoration: none; }
.obra-breadcrumb a:hover { color: white; text-decoration: underline; }
.obra-header h1 { margin: 0; font-size: 28px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
.obra-header-subtitle { margin-top: 8px; opacity: 0.9; font-size: 15px; display: flex; gap: 20px; flex-wrap: wrap; }
.obra-header-subtitle span { display: flex; align-items: center; gap: 6px; }

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border-left: 4px solid #667eea;
    transition: transform 0.2s;
}

.stat-card:hover { transform: translateY(-2px); }
.stat-card.success { border-left-color: #27ae60; }
.stat-card.warning { border-left-color: #f39c12; }
.stat-card.danger { border-left-color: #e74c3c; }
.stat-card.info { border-left-color: #3498db; }
.stat-card.purple { border-left-color: #9b59b6; }

.stat-value { font-size: 28px; font-weight: 700; color: #333; margin-bottom: 4px; }
.stat-label { font-size: 13px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }

/* Cards */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 24px;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title i { color: #667eea; font-size: 22px; }
.card-body { padding: 24px; }

/* Filtros */
.filtros-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.filtro-group { display: flex; flex-direction: column; gap: 6px; }
.filtro-group label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: 600; }
.filtro-group select,
.filtro-group input {
    padding: 10px 14px;
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: all 0.2s;
}

.filtro-group select:focus,
.filtro-group input:focus { border-color: #667eea; }

/* Tabela de Atividades */
.atividades-table { width: 100%; border-collapse: collapse; }
.atividades-table th {
    background: #f8f9fa;
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    font-weight: 600;
    border-bottom: 2px solid #e8e8e8;
}

.atividades-table td {
    padding: 16px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.atividades-table tr:hover { background: #f8f9fa; }

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-badge.agendada { background: #f8f9fa; color: #666; }
.status-badge.agendada::before { background: #95a5a6; }

.status-badge.iniciada { background: #fff8e6; color: #f39c12; }
.status-badge.iniciada::before { background: #f39c12; }

.status-badge.pausada { background: #ffebee; color: #e74c3c; }
.status-badge.pausada::before { background: #e74c3c; }

.status-badge.concluida { background: #e8f5e9; color: #27ae60; }
.status-badge.concluida::before { background: #27ae60; }

.status-badge.cancelada { background: #eceff1; color: #546e7f; }
.status-badge.cancelada::before { background: #546e7f; }

.status-badge.reaberta { background: #f3e5f5; color: #9b59b6; }
.status-badge.reaberta::before { background: #9b59b6; }

/* Progresso */
.progress-wrapper { display: flex; align-items: center; gap: 10px; }
.progress-bar-bg {
    flex: 1;
    height: 8px;
    background: #e8e8e8;
    border-radius: 4px;
    overflow: hidden;
    max-width: 100px;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s;
}

.progress-bar-fill.low { background: #e74c3c; }
.progress-bar-fill.medium { background: #f39c12; }
.progress-bar-fill.high { background: #27ae60; }

.progress-text { font-size: 13px; font-weight: 600; color: #666; min-width: 40px; }

/* Botões */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

.btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
.btn-success { background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
.btn-warning { background: linear-gradient(135deg, #f39c12, #e67e22); color: white; }
.btn-danger { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; }
.btn-secondary { background: #f5f5f5; color: #666; }
.btn-info { background: linear-gradient(135deg, #3498db, #2980b9); color: white; }

.btn-sm { padding: 6px 12px; font-size: 13px; }
.btn-xs { padding: 4px 10px; font-size: 12px; }

.btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

/* Ações em linha */
.acoes-cell { display: flex; gap: 6px; flex-wrap: wrap; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #888;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    background: #f0f0f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 36px;
    color: #bbb;
}

.empty-state h4 { margin: 0 0 8px 0; color: #666; font-weight: 500; }
.empty-state p { margin: 0 0 20px 0; font-size: 14px; }

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-overlay.active { display: flex; }

.modal-container {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 700px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-header {
    padding: 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: #f5f5f5;
    color: #666;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.2s;
}

.modal-close:hover { background: #e8e8e8; color: #333; }

.modal-body { padding: 24px; }

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Form no modal */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group.full-width { grid-column: span 2; }

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-group label .required { color: #e74c3c; }

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 16px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    outline: none;
    transition: all 0.2s;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color: #667eea; }

.form-group textarea { resize: vertical; min-height: 100px; }

.checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}

.checkbox-wrapper input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #667eea;
}

.checkbox-wrapper label {
    margin: 0;
    font-weight: 500;
    cursor: pointer;
}

/* Reatendimento indicator */
.reatendimento-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: white;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

/* Info tooltips */
.info-cell {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-primary { font-weight: 600; color: #333; font-size: 14px; }
.info-secondary { font-size: 12px; color: #888; }
.info-tertiary { font-size: 11px; color: #aaa; }

/* Tabs */
.tabs-nav {
    display: flex;
    gap: 4px;
    padding: 0 24px;
    border-bottom: 1px solid #f0f0f0;
    background: #f8f9fa;
}

.tab-btn {
    padding: 14px 20px;
    border: none;
    background: transparent;
    color: #666;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    position: relative;
    transition: all 0.2s;
}

.tab-btn:hover { color: #667eea; }
.tab-btn.active {
    color: #667eea;
    font-weight: 600;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 3px 3px 0 0;
}

.tab-content { display: none; }
.tab-content.active { display: block; }

/* Responsividade */
@media (max-width: 1200px) {
    .form-grid { grid-template-columns: 1fr; }
    .form-group.full-width { grid-column: span 1; }
}

@media (max-width: 768px) {
    .obras-container { padding: 16px; }
    .obra-header { padding: 20px; }
    .obra-header h1 { font-size: 22px; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .filtros-grid { grid-template-columns: 1fr; }

    .atividades-table {
        display: block;
        overflow-x: auto;
    }

    .acoes-cell { flex-direction: column; }
    .btn-icon { width: 32px; height: 32px; }
}

/* Dark Mode */
body[data-theme="dark"] .card,
body[data-theme="dark"] .stat-card,
body[data-theme="dark"] .modal-container { background: #1a1d29; }

body[data-theme="dark"] .card-header,
body[data-theme="dark"] .modal-header,
body[data-theme="dark"] .modal-footer,
body[data-theme="dark"] .tabs-nav { border-color: #2d3347; }

body[data-theme="dark"] .tabs-nav { background: #252a3a; }

body[data-theme="dark"] .card-title,
body[data-theme="dark"] .modal-title,
body[data-theme="dark"] .info-primary,
body[data-theme="dark"] .stat-value { color: #e2e8f0; }

body[data-theme="dark"] .atividades-table th { background: #252a3a; border-color: #2d3347; color: #a0aec0; }
body[data-theme="dark"] .atividades-table td { border-color: #2d3347; }
body[data-theme="dark"] .atividades-table tr:hover { background: #252a3a; }

body[data-theme="dark"] .filtro-group input,
body[data-theme="dark"] .filtro-group select,
body[data-theme="dark"] .form-group input,
body[data-theme="dark"] .form-group select,
body[data-theme="dark"] .form-group textarea {
    background: #252a3a;
    border-color: #4a5568;
    color: #e2e8f0;
}

body[data-theme="dark"] .empty-state-icon { background: #252a3a; color: #4a5568; }
body[data-theme="dark"] .empty-state h4 { color: #a0aec0; }

body[data-theme="dark"] .btn-secondary { background: #252a3a; color: #e2e8f0; }
body[data-theme="dark"] .modal-close { background: #252a3a; color: #a0aec0; }
</style>

<div class="obras-container">

    <!-- Header -->
    <div class="obra-header">
        <div class="obra-header-content">
            <div class="obra-header-left">
                <div class="obra-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>">Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo htmlspecialchars($obra->nome); ?></a> &raquo;
                    <span>Atividades</span>
                </div>
                <h1><i class='bx bx-task'></i> Atividades da Obra</h1>
                <div class="obra-header-subtitle">
                    <span><i class='bx bx-building'></i> <?php echo htmlspecialchars($obra->nome); ?></span>
                    <span><i class='bx bx-user'></i> <?php echo htmlspecialchars($obra->cliente_nome ?? 'Cliente não definido'); ?></span>
                    <span><i class='bx bx-calendar'></i> <?php echo count($atividades); ?> atividade(s)</span>
                </div>
            </div>
            <div>
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Voltar à Obra
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <?php
        $total = count($atividades);
        $concluidas = count(array_filter($atividades, fn($a) => $a->status === 'concluida'));
        $em_andamento = count(array_filter($atividades, fn($a) => in_array($a->status, ['iniciada', 'pausada'])));
        $agendadas = count(array_filter($atividades, fn($a) => $a->status === 'agendada'));
        $reabertas = count(array_filter($atividades, fn($a) => $a->status === 'reaberta'));
        $percentual = $total > 0 ? round(($concluidas / $total) * 100) : 0;
        ?>
        <div class="stat-card success">
            <div class="stat-value"><?php echo $concluidas; ?></div>
            <div class="stat-label">Concluídas</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value"><?php echo $em_andamento; ?></div>
            <div class="stat-label">Em Andamento</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value"><?php echo $agendadas; ?></div>
            <div class="stat-label">Agendadas</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-value"><?php echo $reabertas; ?></div>
            <div class="stat-label">Reabertas</div>
        </div>
        <div class="stat-card <?php echo $percentual >= 80 ? 'success' : ($percentual >= 50 ? 'warning' : 'danger'); ?>">
            <div class="stat-value"><?php echo $percentual; ?>%</div>
            <div class="stat-label">Progresso</div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class='bx bx-list-ul'></i> Lista de Atividades
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn btn-primary btn-sm" onclick="abrirModalNovaAtividade()">
                    <i class='bx bx-plus'></i> Nova Atividade
                </button>
                <a href="<?php echo site_url('obras/salvarWizard/' . $obra->id); ?>" class="btn btn-success btn-sm">
                    <i class='bx bx-wizard'></i> Wizard de Etapas
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="switchTab('todas', this)">
                <i class='bx bx-grid-alt'></i> Todas (<?php echo $total; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('agendadas', this)">
                <i class='bx bx-calendar'></i> Agendadas (<?php echo $agendadas; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('andamento', this)">
                <i class='bx bx-play-circle'></i> Em Andamento (<?php echo $em_andamento; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('concluidas', this)">
                <i class='bx bx-check-circle'></i> Concluídas (<?php echo $concluidas; ?>)
            </button>
            <button class="tab-btn" onclick="switchTab('reabertas', this)">
                <i class='bx bx-refresh'></i> Reabertas (<?php echo $reabertas; ?>)
            </button>
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <div class="filtros-grid">
                <div class="filtro-group">
                    <label>Buscar</label>
                    <input type="text" id="filtroBusca" placeholder="Título, descrição..." onkeyup="filtrarAtividades()">
                </div>
                <div class="filtro-group">
                    <label>Técnico</label>
                    <select id="filtroTecnico" onchange="filtrarAtividades()">
                        <option value="">Todos</option>
                        <?php foreach ($tecnicos as $t): ?>
                        <option value="<?php echo $t->idUsuarios; ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filtro-group">
                    <label>Etapa</label>
                    <select id="filtroEtapa" onchange="filtrarAtividades()">
                        <option value="">Todas</option>
                        <?php foreach ($etapas as $e): ?>
                        <option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->nome); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filtro-group">
                    <label>Tipo</label>
                    <select id="filtroTipo" onchange="filtrarAtividades()">
                        <option value="">Todos</option>
                        <option value="trabalho">Trabalho</option>
                        <option value="impedimento">Impedimento</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
            </div>

            <!-- Tabela -->
            <?php if (empty($atividades)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class='bx bx-task-x'></i>
                </div>
                <h4>Nenhuma atividade encontrada</h4>
                <p>Esta obra ainda não possui atividades cadastradas.</p>
                <button class="btn btn-primary" onclick="abrirModalNovaAtividade()">
                    <i class='bx bx-plus'></i> Criar Primeira Atividade
                </button>
            </div>
            <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="atividades-table" id="tabelaAtividades">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Atividade</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 100px;">Progresso</th>
                            <th>Técnico</th>
                            <th>Etapa</th>
                            <th style="width: 180px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atividades as $atividade):
                            $statusClass = $atividade->status ?? 'agendada';
                            $progresso = $atividade->percentual_concluido ?? 0;
                            $progressoClass = $progresso >= 80 ? 'high' : ($progresso >= 50 ? 'medium' : 'low');
                        ?>
                        <tr class="atividade-row"
                            data-status="<?php echo $statusClass; ?>"
                            data-tecnico="<?php echo $atividade->tecnico_id ?? ''; ?>"
                            data-etapa="<?php echo $atividade->etapa_id ?? ''; ?>"
                            data-tipo="<?php echo $atividade->tipo ?? 'trabalho'; ?>"
                            data-busca="<?php echo strtolower(htmlspecialchars(($atividade->titulo ?? '') . ' ' . ($atividade->descricao ?? ''))); ?>">
                            <td><?php echo $atividade->id; ?></td>
                            <td>
                                <div class="info-cell">
                                    <span class="info-primary"><?php echo htmlspecialchars($atividade->titulo ?? 'Sem título'); ?></span>
                                    <span class="info-secondary"><?php echo htmlspecialchars(substr($atividade->descricao ?? '', 0, 60)) . (strlen($atividade->descricao ?? '') > 60 ? '...' : ''); ?></span>
                                    <span class="info-tertiary">
                                        <i class='bx bx-calendar'></i> <?php echo date('d/m/Y', strtotime($atividade->data_atividade ?? 'now')); ?>
                                        <?php if ($atividade->tipo === 'impedimento'): ?>
                                            <span style="color: #e74c3c; margin-left: 8px;"><i class='bx bx-error'></i> Impedimento</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($statusClass); ?>
                                </span>
                                <?php if ($statusClass === 'reaberta'): ?>
                                    <div class="reatendimento-badge" style="margin-top: 6px;">
                                        <i class='bx bx-refresh'></i> Reatendimento
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="progress-wrapper">
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill <?php echo $progressoClass; ?>" style="width: <?php echo $progresso; ?>%"></div>
                                    </div>
                                    <span class="progress-text"><?php echo $progresso; ?>%</span>
                                </div>
                            </td>
                            <td>
                                <div class="info-cell">
                                    <span class="info-primary"><?php echo htmlspecialchars($atividade->tecnico_nome ?? 'Não atribuído'); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="info-cell">
                                    <span class="info-primary"><?php echo htmlspecialchars($atividade->etapa_nome ?? 'Sem etapa'); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="acoes-cell">
                                    <button class="btn btn-primary btn-xs btn-icon" onclick="abrirModalEditar(<?php echo $atividade->id; ?>)" title="Editar Rápido">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade->id); ?>" class="btn btn-info btn-xs btn-icon" title="Visualizar Completo">
                                        <i class='bx bx-eye'></i>
                                    </a>
                                    <?php if (in_array($statusClass, ['concluida', 'cancelada', 'reaberta'])): ?>
                                    <button class="btn btn-warning btn-xs" onclick="reabrirAtividade(<?php echo $atividade->id; ?>)" title="Reabrir/Reatendimento">
                                        <i class='bx bx-refresh'></i> Reabrir
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($statusClass === 'agendada'): ?>
                                    <a href="<?php echo site_url('tecnicos/executar_obra/' . $obra->id . '?atividade=' . $atividade->id); ?>" class="btn btn-success btn-xs" title="Iniciar Execução">
                                        <i class='bx bx-play'></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Atividades Registradas (Hora Início/Fim) -->
    <?php if (!empty($atividades_registradas)): ?>
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class='bx bx-time'></i> Registros de Execução (Wizard)
            </div>
            <span class="badge-count" style="background: #27ae60; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                <?php echo count($atividades_registradas); ?> registros
            </span>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <table class="atividades-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Técnico</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Duração</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atividades_registradas as $reg): ?>
                        <tr>
                            <td><?php echo $reg->idAtividade; ?></td>
                            <td><?php echo htmlspecialchars($reg->tipo_nome ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($reg->nome_tecnico ?? 'N/A'); ?></td>
                            <td><?php echo $reg->hora_inicio ? date('d/m/Y H:i', strtotime($reg->hora_inicio)) : '-'; ?></td>
                            <td><?php echo $reg->hora_fim ? date('d/m/Y H:i', strtotime($reg->hora_fim)) : '-'; ?></td>
                            <td>
                                <?php if ($reg->duracao_minutos): ?>
                                    <?php echo floor($reg->duracao_minutos / 60); ?>h <?php echo $reg->duracao_minutos % 60; ?>m
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $reg->status ?? 'em_andamento'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $reg->status ?? 'em_andamento')); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('obras/visualizarAtividade/' . $reg->obra_atividade_id); ?>" class="btn btn-info btn-xs btn-icon">
                                    <i class='bx bx-eye'></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Modal: Editar Atividade -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">
                <i class='bx bx-edit'></i> Editar Atividade
            </div>
            <button class="modal-close" onclick="fecharModal('modalEditar')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <form id="formEditar" method="POST">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Título <span class="required">*</span></label>
                        <input type="text" name="titulo" id="edit_titulo" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Descrição</label>
                        <textarea name="descricao" id="edit_descricao" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status">
                            <option value="agendada">Agendada</option>
                            <option value="iniciada">Em Execução</option>
                            <option value="pausada">Pausada</option>
                            <option value="concluida">Concluída</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="reaberta">Reaberta (Reatendimento)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" name="data_atividade" id="edit_data">
                    </div>
                    <div class="form-group">
                        <label>Técnico</label>
                        <select name="tecnico_id" id="edit_tecnico">
                            <option value="">Não atribuído</option>
                            <?php foreach ($tecnicos as $t): ?>
                            <option value="<?php echo $t->idUsuarios; ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Etapa</label>
                        <select name="etapa_id" id="edit_etapa">
                            <option value="">Sem etapa</option>
                            <?php foreach ($etapas as $e): ?>
                            <option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo" id="edit_tipo">
                            <option value="trabalho">Trabalho</option>
                            <option value="impedimento">Impedimento</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Progresso (%)</label>
                        <input type="number" name="percentual_concluido" id="edit_progresso" min="0" max="100" value="0">
                    </div>
                    <div class="form-group full-width">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="visivel_cliente" id="edit_visivel" value="1">
                            <label for="edit_visivel">Visível ao cliente</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Nova Atividade -->
<div class="modal-overlay" id="modalNova">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">
                <i class='bx bx-plus'></i> Nova Atividade
            </div>
            <button class="modal-close" onclick="fecharModal('modalNova')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <form action="<?php echo site_url('obras/adicionarAtividade'); ?>" method="POST">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Título <span class="required">*</span></label>
                        <input type="text" name="titulo" required placeholder="Nome da atividade...">
                    </div>
                    <div class="form-group full-width">
                        <label>Descrição</label>
                        <textarea name="descricao" rows="3" placeholder="Descreva a atividade..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="trabalho">Trabalho</option>
                            <option value="impedimento">Impedimento</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" name="data_atividade" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Técnico</label>
                        <select name="tecnico_id">
                            <option value="">Não atribuído</option>
                            <?php foreach ($tecnicos as $t): ?>
                            <option value="<?php echo $t->idUsuarios; ?>"><?php echo htmlspecialchars($t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Etapa</label>
                        <select name="etapa_id">
                            <option value="">Sem etapa</option>
                            <?php foreach ($etapas as $e): ?>
                            <option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="visivel_cliente" value="1" checked>
                            <label>Visível ao cliente</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalNova')">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Criar Atividade
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reabrir Atividade (Reatendimento) -->
<div class="modal-overlay" id="modalReabrir">
    <div class="modal-container" style="max-width: 500px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
            <div class="modal-title" style="color: white;">
                <i class='bx bx-refresh'></i> Reabrir Atividade - Reatendimento
            </div>
            <button class="modal-close" onclick="fecharModal('modalReabrir')" style="background: rgba(255,255,255,0.2); color: white;">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <form id="formReabrir" method="POST" action="">
            <div class="modal-body">
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 14px; color: #666;">
                        <i class='bx bx-info-circle'></i>
                        Ao reabrir esta atividade, um <strong>reatendimento</strong> será criado para permitir nova execução.
                        O histórico anterior será preservado.
                    </p>
                </div>
                <div class="form-group full-width">
                    <label>Motivo da Reabertura <span class="required">*</span></label>
                    <textarea name="observacao_status" rows="3" required placeholder="Informe o motivo da reabertura..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalReabrir')">Cancelar</button>
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
                    <i class='bx bx-refresh'></i> Confirmar Reabertura
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Dados das atividades para edição rápida
const atividadesData = <?php echo json_encode(array_map(function($a) {
    return [
        'id' => $a->id,
        'titulo' => $a->titulo ?? '',
        'descricao' => $a->descricao ?? '',
        'status' => $a->status ?? 'agendada',
        'data_atividade' => $a->data_atividade ?? date('Y-m-d'),
        'tecnico_id' => $a->tecnico_id ?? '',
        'etapa_id' => $a->etapa_id ?? '',
        'tipo' => $a->tipo ?? 'trabalho',
        'percentual_concluido' => $a->percentual_concluido ?? 0,
        'visivel_cliente' => $a->visivel_cliente ?? 0
    ];
}, $atividades)); ?>;

// Abrir modal de edição
function abrirModalEditar(id) {
    const atividade = atividadesData.find(a => a.id == id);
    if (!atividade) return;

    document.getElementById('edit_titulo').value = atividade.titulo;
    document.getElementById('edit_descricao').value = atividade.descricao;
    document.getElementById('edit_status').value = atividade.status;
    document.getElementById('edit_data').value = atividade.data_atividade;
    document.getElementById('edit_tecnico').value = atividade.tecnico_id || '';
    document.getElementById('edit_etapa').value = atividade.etapa_id || '';
    document.getElementById('edit_tipo').value = atividade.tipo;
    document.getElementById('edit_progresso').value = atividade.percentual_concluido;
    document.getElementById('edit_visivel').checked = atividade.visivel_cliente == 1;

    document.getElementById('formEditar').action = '<?php echo site_url('obras/editarAtividade/'); ?>' + id;

    document.getElementById('modalEditar').classList.add('active');
}

// Abrir modal nova atividade
function abrirModalNovaAtividade() {
    document.getElementById('modalNova').classList.add('active');
}

// Reabrir atividade (reatendimento)
function reabrirAtividade(id) {
    // Limpar formulário anterior
    const form = document.getElementById('formReabrir');
    form.action = '<?php echo site_url('obras/atualizarStatusAtividade/'); ?>' + id;

    // Adicionar input hidden para o status
    let statusInput = form.querySelector('input[name="novo_status"]');
    if (!statusInput) {
        statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'novo_status';
        form.appendChild(statusInput);
    }
    statusInput.value = 'reaberta';

    document.getElementById('modalReabrir').classList.add('active');
}

// Fechar modal
function fecharModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
    }
}

// Switch tabs
function switchTab(tabName, btn) {
    // Remove active de todos os tabs
    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    // Filtrar por status
    const rows = document.querySelectorAll('.atividade-row');
    rows.forEach(row => {
        if (tabName === 'todas') {
            row.style.display = '';
        } else if (tabName === 'andamento') {
            const status = row.dataset.status;
            row.style.display = (status === 'iniciada' || status === 'pausada') ? '' : 'none';
        } else if (tabName === 'reabertas') {
            row.style.display = (row.dataset.status === 'reaberta') ? '' : 'none';
        } else {
            row.style.display = (row.dataset.status === tabName) ? '' : 'none';
        }
    });
}

// Filtrar atividades
function filtrarAtividades() {
    const busca = document.getElementById('filtroBusca').value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    const tecnico = document.getElementById('filtroTecnico').value;
    const etapa = document.getElementById('filtroEtapa').value;
    const tipo = document.getElementById('filtroTipo').value;

    const rows = document.querySelectorAll('.atividade-row');
    rows.forEach(row => {
        const rowBusca = row.dataset.busca.normalize('NFD').replace(/[̀-ͯ]/g, '');
        const matchBusca = !busca || rowBusca.includes(busca);
        const matchTecnico = !tecnico || row.dataset.tecnico === tecnico;
        const matchEtapa = !etapa || row.dataset.etapa === etapa;
        const matchTipo = !tipo || row.dataset.tipo === tipo;

        row.style.display = (matchBusca && matchTecnico && matchEtapa && matchTipo) ? '' : 'none';
    });
}

// Atalho de teclado ESC para fechar modais
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    }
});
</script>
