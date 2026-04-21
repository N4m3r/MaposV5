<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.atividades-modern {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.atividades-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.atividades-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.atividades-header-left { flex: 1; }
.atividades-breadcrumb {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 10px;
}
.atividades-breadcrumb a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.3s;
}
.atividades-breadcrumb a:hover {
    opacity: 1;
    text-decoration: underline;
}
.atividades-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}
.atividades-header h1 i { font-size: 32px; }
.atividades-subtitle {
    margin-top: 8px;
    opacity: 0.9;
    font-size: 15px;
}

.atividades-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.atividades-btn {
    padding: 12px 24px;
    border-radius: 12px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.atividades-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.atividades-btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
}
.atividades-btn-secondary:hover { background: rgba(255,255,255,0.3); }
.atividades-btn-primary {
    background: white;
    color: #667eea;
}
.atividades-btn-primary:hover { background: #f8f9fa; }

/* Stats Cards */
.atividades-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}
.atividades-stat-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s;
}
.atividades-stat-card:hover { transform: translateY(-3px); }
.atividades-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.atividades-stat-icon.total { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
.atividades-stat-icon.hoje { background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
.atividades-stat-icon.agendadas { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
.atividades-stat-icon.concluidas { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
.atividades-stat-content { flex: 1; }
.atividades-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    line-height: 1;
}
.atividades-stat-label {
    font-size: 13px;
    color: #888;
    margin-top: 4px;
}

/* Filtros */
.atividades-filters {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
}
.atividades-filter-input,
.atividades-filter-select {
    flex: 1;
    min-width: 200px;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    color: #333;
    background: white;
    transition: all 0.3s;
}
.atividades-filter-input:focus,
.atividades-filter-select:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}
.atividades-filter-input::placeholder { color: #999; }

/* Grid de Atividades */
.atividades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.atividade-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-left: 5px solid transparent;
    transition: all 0.3s;
    cursor: pointer;
    position: relative;
}
.atividade-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.atividade-card.agendada { border-left-color: #95a5a6; }
.atividade-card.iniciada { border-left-color: #3498db; background: linear-gradient(135deg, #fff, #ebf5fb); }
.atividade-card.pausada { border-left-color: #f39c12; background: linear-gradient(135deg, #fff, #fef9e7); }
.atividade-card.concluida { border-left-color: #27ae60; background: linear-gradient(135deg, #fff, #eafaf1); }
.atividade-card.cancelada { border-left-color: #e74c3c; background: linear-gradient(135deg, #fff, #fdedec); }

.atividade-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}
.atividade-card-title-section { flex: 1; min-width: 0; }
.atividade-card-date {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0f0f0;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #555;
    margin-bottom: 10px;
}
.atividade-card-date i { color: #667eea; }
.atividade-card-title {
    font-size: 17px;
    font-weight: 700;
    color: #333;
    margin: 0;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.atividade-status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    margin-left: 10px;
}
.atividade-status-badge.agendada { background: #ecf0f1; color: #7f8c8d; }
.atividade-status-badge.iniciada { background: #3498db; color: white; }
.atividade-status-badge.pausada { background: #f39c12; color: white; }
.atividade-status-badge.concluida { background: #27ae60; color: white; }
.atividade-status-badge.cancelada { background: #e74c3c; color: white; }

.atividade-card-body { margin-bottom: 15px; }
.atividade-card-desc {
    font-size: 14px;
    color: #666;
    line-height: 1.5;
    margin-bottom: 12px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.atividade-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}
.atividade-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #555;
    background: #f8f9fa;
    padding: 6px 12px;
    border-radius: 8px;
}
.atividade-meta-item i { color: #667eea; font-size: 14px; }
.atividade-meta-item.tipo-trabalho i { color: #3498db; }
.atividade-meta-item.tipo-visita i { color: #27ae60; }
.atividade-meta-item.tipo-impedimento i { color: #e74c3c; }
.atividade-meta-item.tipo-manutencao i { color: #f39c12; }

.atividade-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
.atividade-progress-section {
    flex: 1;
    margin-right: 15px;
}
.atividade-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.atividade-progress-label {
    font-size: 12px;
    color: #888;
}
.atividade-progress-value {
    font-size: 14px;
    font-weight: 700;
    color: #667eea;
}
.atividade-progress-bar {
    height: 6px;
    background: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
}
.atividade-progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.atividade-card-actions {
    display: flex;
    gap: 8px;
}
.atividade-card-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    font-size: 14px;
}
.atividade-card-btn:hover {
    transform: scale(1.1);
}
.atividade-card-btn-view {
    background: #e3f2fd;
    color: #1976d2;
}
.atividade-card-btn-view:hover {
    background: #1976d2;
    color: white;
}
.atividade-card-btn-delete {
    background: #ffebee;
    color: #c62828;
}
.atividade-card-btn-delete:hover {
    background: #c62828;
    color: white;
}

.atividade-visibilidade {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.atividade-visibilidade.visivel {
    background: #27ae60;
    color: white;
}
.atividade-visibilidade.oculto {
    background: #95a5a6;
    color: white;
}

/* Empty State */
.atividades-empty {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.atividades-empty-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f5f7fa, #e4e8ec);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    font-size: 50px;
    color: #667eea;
}
.atividades-empty h3 {
    font-size: 22px;
    color: #333;
    margin-bottom: 10px;
}
.atividades-empty p {
    color: #888;
    margin-bottom: 25px;
}

/* Modal Moderno */
.modal-atividades .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    border: none;
    border-radius: 15px 15px 0 0;
}
.modal-atividades .modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.modal-atividades .modal-header .close {
    color: white;
    opacity: 0.9;
    font-size: 28px;
    font-weight: 300;
    text-shadow: none;
}
.modal-atividades .modal-body {
    padding: 30px;
    background: #fafafa;
}
.modal-atividades .modal-footer {
    padding: 20px 30px;
    background: white;
    border-top: 1px solid #e0e0e0;
    border-radius: 0 0 15px 15px;
}

.atividades-form-group {
    margin-bottom: 20px;
}
.atividades-form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}
.atividades-form-label .required {
    color: #e74c3c;
}
.atividades-form-input,
.atividades-form-select,
.atividades-form-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    color: #333;
    background: white;
    transition: all 0.3s;
    box-sizing: border-box;
}
.atividades-form-input:focus,
.atividades-form-select:focus,
.atividades-form-textarea:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}
.atividades-form-textarea {
    resize: vertical;
    min-height: 80px;
}
.atividades-form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.atividades-form-hint {
    font-size: 12px;
    color: #888;
    margin-top: 6px;
}

.atividades-form-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    border: 2px solid #e0e0e0;
    cursor: pointer;
    transition: all 0.3s;
}
.atividades-form-checkbox:hover {
    border-color: #667eea;
}
.atividades-form-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}
.atividades-form-checkbox-label {
    font-weight: 600;
    color: #333;
    cursor: pointer;
}
.atividades-form-checkbox-hint {
    font-size: 12px;
    color: #888;
    margin-left: 30px;
}

.atividades-btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 14px 32px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.atividades-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}
.atividades-btn-cancel {
    background: #f5f5f5;
    color: #666;
    padding: 14px 28px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.atividades-btn-cancel:hover {
    background: #e0e0e0;
}

/* Responsive */
@media (max-width: 768px) {
    .atividades-header-content { flex-direction: column; }
    .atividades-grid { grid-template-columns: 1fr; }
    .atividades-form-row { grid-template-columns: 1fr; }
    .atividades-filters { flex-direction: column; }
    .atividades-filter-input, .atividades-filter-select { width: 100%; }
}
</style>

<div class="atividades-modern">
    <!-- Header -->
    <div class="atividades-header">
        <div class="atividades-header-content">
            <div class="atividades-header-left">
                <div class="atividades-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="icon-arrow-left"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a> &raquo;
                    <span>Atividades</span>
                </div>
                <h1><i class="icon-calendar"></i> Atividades da Obra</h1>
                <div class="atividades-subtitle">Gerencie as atividades e acompanhe o progresso do trabalho</div>
            </div>
            <div class="atividades-actions">
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="atividades-btn atividades-btn-secondary">
                    <i class="icon-eye-open"></i> Ver Obra
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button onclick="$('#modalAdicionar').modal('show')" class="atividades-btn atividades-btn-primary">
                    <i class="icon-plus"></i> Nova Atividade
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mensagens Flash -->
    <?php if ($this->session->flashdata('success')): ?>
    <div style="background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="icon-ok" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('success'); ?></strong>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="icon-remove" style="font-size: 20px;"></i>
        <strong><?php echo $this->session->flashdata('error'); ?></strong>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <?php
    $total = count($atividades);
    $hoje = count(array_filter($atividades, function($a) {
        return isset($a->data_atividade) && $a->data_atividade == date('Y-m-d');
    }));
    $agendadas = count(array_filter($atividades, function($a) {
        return isset($a->status) && $a->status == 'agendada';
    }));
    $concluidas = count(array_filter($atividades, function($a) {
        return isset($a->status) && $a->status == 'concluida';
    }));
    ?>
    <div class="atividades-stats">
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon total"><i class="icon-tasks"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $total; ?></div>
                <div class="atividades-stat-label">Total de Atividades</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon hoje"><i class="icon-calendar"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $hoje; ?></div>
                <div class="atividades-stat-label">Atividades Hoje</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon agendadas"><i class="icon-time"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $agendadas; ?></div>
                <div class="atividades-stat-label">Agendadas</div>
            </div>
        </div>
        <div class="atividades-stat-card">
            <div class="atividades-stat-icon concluidas"><i class="icon-check"></i></div>
            <div class="atividades-stat-content">
                <div class="atividades-stat-value"><?php echo $concluidas; ?></div>
                <div class="atividades-stat-label">Concluídas</div>
            </div>
        </div>
    </div>

    <!-- DEBUG PANEL -->
    <div style="background: #2c3e50; color: #fff; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-family: monospace; font-size: 12px;">
        <div style="font-weight: bold; color: #e74c3c; margin-bottom: 10px; font-size: 14px;">
            <i class="icon-bug"></i> DEBUG - Dados da View
        </div>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
            <div><strong>Obra ID:</strong> <?php echo isset($obra) ? $obra->id : 'NÃO DEFINIDO'; ?></div>
            <div><strong>Obra Nome:</strong> <?php echo isset($obra) ? $obra->nome : 'NÃO DEFINIDO'; ?></div>
            <div><strong>Total Atividades:</strong> <?php echo isset($atividades) ? count($atividades) : 'NÃO DEFINIDO'; ?></div>
            <div><strong>Total Técnicos:</strong> <?php echo isset($tecnicos) ? count($tecnicos) : 'NÃO DEFINIDO'; ?></div>
            <div><strong>Total Etapas:</strong> <?php echo isset($etapas) ? count($etapas) : 'NÃO DEFINIDO'; ?></div>
            <div><strong>Variável atividades existe:</strong> <?php echo isset($atividades) ? 'SIM' : 'NÃO'; ?></div>
        </div>
        <?php if (isset($atividades) && !empty($atividades)): ?>
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #34495e;">
            <strong>Primeira atividade:</strong>
            <pre style="margin: 5px 0; background: #1a252f; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 11px; max-height: 200px;"><?php print_r($atividades[0]); ?></pre>
        </div>
        <?php elseif (isset($atividades) && empty($atividades)): ?>
        <div style="margin-top: 10px; color: #f39c12;">
            <strong>⚠️ Array de atividades está VAZIO</strong><br>
            <a href="<?php echo site_url('obras/verificarAtividades/' . (isset($obra) ? $obra->id : 0)); ?>" style="color: #3498db;">Verificar no Banco →</a>
        </div>
        <?php else: ?>
        <div style="margin-top: 10px; color: #e74c3c;">
            <strong>❌ Variável $atividades NÃO EXISTE</strong>
        </div>
        <?php endif; ?>
    </div>

    <!-- Info Panel -->
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 20px; font-size: 13px;">
        <strong><i class="icon-info-sign"></i> Informações:</strong>
        Obra ID: <code><?php echo isset($obra) ? $obra->id : 'N/A'; ?></code> |
        Total de atividades: <code id="totalAtividadesCounter"><?php echo isset($atividades) ? count($atividades) : 0; ?></code>
        <button onclick="location.reload()" style="float: right; padding: 4px 12px; font-size: 12px;">
            <i class="icon-refresh"></i> Recarregar
        </button>

        <?php if (!isset($atividades) || empty($atividades)): ?>
        <div id="semAtividadesMsg" style="margin-top: 10px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
            <i class="icon-warning-sign" style="font-size: 18px; color: #856404;"></i>
            <strong style="color: #856404;"> Nenhuma atividade cadastrada para esta obra!</strong><br><br>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button onclick="$('#modalAdicionar').modal('show')" class="btn btn-success">
                    <i class="icon-plus"></i> Adicionar Primeira Atividade
                </button>

                <a href="<?php echo site_url('obras/criarAtividadeTeste/' . (isset($obra) ? $obra->id : 0)); ?>" class="btn btn-info">
                    <i class="icon-magic"></i> Criar Atividade de Teste
                </a>
                <?php endif; ?>

                <a href="<?php echo site_url('diagnostico'); ?>" class="btn btn-mini">
                    <i class="icon-wrench"></i> Diagnóstico
                </a>
                <a href="<?php echo site_url('obras/verificarAtividades/' . (isset($obra) ? $obra->id : 0)); ?>" class="btn btn-mini btn-info" target="_blank">
                    <i class="icon-search"></i> Verificar Banco
                </a>
            </div>
        </div>
        <?php endif; ?>
        <div style="clear: both;"></div>
    </div>

    <!-- Filtros -->
    <div class="atividades-filters">
        <i class="icon-search" style="font-size: 20px; color: #667eea;"></i>
        <input type="text" id="searchAtividade" class="atividades-filter-input" placeholder="Buscar atividade..." onkeyup="filtrarAtividades()">
        <select id="filterStatus" class="atividades-filter-select" onchange="filtrarAtividades()">
            <option value="">Todos os Status</option>
            <option value="agendada">Agendada</option>
            <option value="iniciada">Iniciada</option>
            <option value="pausada">Pausada</option>
            <option value="concluida">Concluída</option>
            <option value="cancelada">Cancelada</option>
        </select>
        <select id="filterTipo" class="atividades-filter-select" onchange="filtrarAtividades()">
            <option value="">Todos os Tipos</option>
            <option value="trabalho">Trabalho</option>
            <option value="visita">Visita Técnica</option>
            <option value="manutencao">Manutenção</option>
            <option value="impedimento">Impedimento</option>
            <option value="outro">Outro</option>
        </select>
    </div>

    <!-- DEBUG GRID -->
    <div style="background: #2c3e50; color: #fff; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
        <strong>DEBUG GRID:</strong> atividades existe: <?php echo isset($atividades) ? 'SIM' : 'NÃO'; ?> |
        is_array: <?php echo (isset($atividades) && is_array($atividades)) ? 'SIM' : 'NÃO'; ?> |
        count: <?php echo isset($atividades) ? count($atividades) : 0; ?>
    </div>

    <!-- Grid de Atividades -->
    <?php if (isset($atividades) && !empty($atividades) && is_array($atividades)): ?>
    <div class="atividades-grid" id="atividadesGrid">
        <?php
        $count = 0;
        foreach ($atividades as $atividade):
            $count++;
            if (!is_object($atividade)) {
                echo '<div style="background: red; color: white; padding: 5px;">Atividade ' . $count . ' não é objeto!</div>';
                continue;
            }
        ?>
        <!-- DEBUG: Processando atividade <?php echo $count; ?>, ID: <?php echo $atividade->id; ?> -->
        <?php
        $status = $atividade->status ?? 'agendada';
        $tipo = $atividade->tipo ?? 'trabalho';
        $titulo = $atividade->titulo ?? 'Atividade #' . $atividade->id;
        $data_atividade = $atividade->data_atividade ?? null;
        $descricao = $atividade->descricao ?? null;
        $tecnico_nome = $atividade->tecnico_nome ?? null;
        $horas_trabalhadas = $atividade->horas_trabalhadas ?? null;
        $visivel_cliente = $atividade->visivel_cliente ?? 0;
        $percentual_concluido = $atividade->percentual_concluido ?? 0;

        $tipo_icons = [
            'trabalho' => 'icon-wrench',
            'impedimento' => 'icon-exclamation-sign',
            'visita' => 'icon-user',
            'manutencao' => 'icon-cog',
            'outro' => 'icon-question-sign'
        ];
        $tipo_icon = $tipo_icons[$tipo] ?? 'icon-question-sign';

        $progresso = $percentual_concluido;
        if ($progresso < 30) {
            $progressoColor = 'linear-gradient(90deg, #ff6b6b, #ee5a52)';
        } elseif ($progresso < 70) {
            $progressoColor = 'linear-gradient(90deg, #feca57, #ff9f43)';
        } else {
            $progressoColor = 'linear-gradient(90deg, #1dd1a1, #10ac84)';
        }
        ?>
        <div class="atividade-card <?php echo $status; ?>" data-status="<?php echo $status; ?>" data-tipo="<?php echo $tipo; ?>" data-titulo="<?php echo strtolower($titulo); ?>">
            <!-- Visibilidade -->
            <div class="atividade-visibilidade <?php echo $visivel_cliente ? 'visivel' : 'oculto'; ?>" title="<?php echo $visivel_cliente ? 'Visível ao cliente' : 'Oculto do cliente'; ?>">
                <i class="icon-<?php echo $visivel_cliente ? 'eye-open' : 'eye-close'; ?>"></i>
            </div>

            <div class="atividade-card-header">
                <div class="atividade-card-title-section">
                    <div class="atividade-card-date">
                        <i class="icon-calendar"></i>
                        <?php echo $data_atividade ? date('d/m/Y', strtotime($data_atividade)) : 'N/A'; ?>
                    </div>
                    <h3 class="atividade-card-title"><?php echo htmlspecialchars($titulo); ?></h3>
                </div>
                <span class="atividade-status-badge <?php echo $status; ?>">
                    <?php echo ucfirst($status); ?>
                </span>
            </div>

            <div class="atividade-card-body">
                <?php if ($descricao): ?>
                <div class="atividade-card-desc">
                    <?php echo htmlspecialchars($descricao); ?>
                </div>
                <?php endif; ?>

                <div class="atividade-card-meta">
                    <div class="atividade-meta-item tipo-<?php echo $tipo; ?>">
                        <i class="<?php echo $tipo_icon; ?>"></i>
                        <span><?php echo ucfirst($tipo); ?></span>
                    </div>
                    <?php if ($tecnico_nome): ?>
                    <div class="atividade-meta-item">
                        <i class="icon-user"></i>
                        <span><?php echo htmlspecialchars($tecnico_nome); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($horas_trabalhadas): ?>
                    <div class="atividade-meta-item">
                        <i class="icon-time"></i>
                        <span><?php echo $horas_trabalhadas; ?>h</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="atividade-card-footer">
                <div class="atividade-progress-section">
                    <div class="atividade-progress-header">
                        <span class="atividade-progress-label">Progresso</span>
                        <span class="atividade-progress-value"><?php echo $progresso; ?>%</span>
                    </div>
                    <div class="atividade-progress-bar">
                        <div class="atividade-progress-fill" style="width: <?php echo $progresso; ?>%; background: <?php echo $progressoColor; ?>"></div>
                    </div>
                </div>
                <div class="atividade-card-actions">
                    <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade->id); ?>" class="atividade-card-btn atividade-card-btn-view" title="Visualizar">
                        <i class="icon-eye-open"></i>
                    </a>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                    <a href="javascript:void(0);" onclick="confirmarExclusaoAtividade(<?php echo $atividade->id; ?>, '<?php echo htmlspecialchars(addslashes($titulo), ENT_QUOTES); ?>')" class="atividade-card-btn atividade-card-btn-delete" title="Excluir">
                        <i class="icon-trash"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <!-- DEBUG: Loop finalizado. Total: <?php echo $count; ?> atividades -->
    </div>
    <?php else: ?>
    <div class="atividades-empty">
        <div class="atividades-empty-icon">
            <i class="icon-calendar-empty"></i>
        </div>
        <h3>Nenhuma atividade encontrada</h3>
        <p>Adicione atividades para acompanhar o progresso do trabalho na obra.</p>
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
        <button onclick="$('#modalAdicionar').modal('show')" class="atividades-btn atividades-btn-primary" style="display: inline-flex;">
            <i class="icon-plus"></i> Adicionar Primeira Atividade
        </button>
        <?php endif; ?>
        <div style="margin-top: 20px;">
            <a href="<?php echo site_url('diagnostico'); ?>" class="btn btn-warning">
                <i class="icon-wrench"></i> Ir para Diagnóstico
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Adicionar Atividade -->
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
<div id="modalAdicionar" class="modal hide fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalAtividadeLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalAtividadeLabel"><i class="icon-plus-sign"></i> Nova Atividade</h3>
    </div>

    <form action="<?php echo site_url('obras/adicionarAtividade'); ?>" method="post">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

            <!-- Título -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="titulo">
                    <i class="icon-tag"></i> Título da Atividade <span class="required">*</span>
                </label>
                <input type="text" name="titulo" id="titulo" class="atividades-form-input" placeholder="Ex: Instalação elétrica, Reunião com cliente..." required>
            </div>

            <!-- Descrição -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="descricao">
                    <i class="icon-align-left"></i> Descrição
                </label>
                <textarea name="descricao" id="descricao" class="atividades-form-textarea" placeholder="Descreva os detalhes desta atividade..."></textarea>
            </div>

            <div class="atividades-form-row">
                <!-- Data -->
                <div class="atividades-form-group">
                    <label class="atividades-form-label" for="data_atividade">
                        <i class="icon-calendar"></i> Data da Atividade <span class="required">*</span>
                    </label>
                    <input type="date" name="data_atividade" id="data_atividade" class="atividades-form-input" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <!-- Tipo -->
                <div class="atividades-form-group">
                    <label class="atividades-form-label" for="tipo">
                        <i class="icon-wrench"></i> Tipo de Atividade
                    </label>
                    <select name="tipo" id="tipo" class="atividades-form-select">
                        <option value="trabalho">🔧 Trabalho</option>
                        <option value="visita">👤 Visita Técnica</option>
                        <option value="manutencao">🔨 Manutenção</option>
                        <option value="impedimento">⚠️ Impedimento</option>
                        <option value="outro">❓ Outro</option>
                    </select>
                </div>
            </div>

            <div class="atividades-form-row">
                <!-- Técnico -->
                <div class="atividades-form-group">
                    <label class="atividades-form-label" for="tecnico_id">
                        <i class="icon-user"></i> Técnico Responsável
                    </label>
                    <select name="tecnico_id" id="tecnico_id" class="atividades-form-select">
                        <option value="">Selecione um técnico...</option>
                        <?php foreach ($tecnicos as $t): ?>
                        <option value="<?php echo $t->idUsuarios; ?>"><?php echo $t->nome; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Etapa -->
                <div class="atividades-form-group">
                    <label class="atividades-form-label" for="etapa_id">
                        <i class="icon-tasks"></i> Etapa Relacionada
                    </label>
                    <select name="etapa_id" id="etapa_id" class="atividades-form-select">
                        <option value="">Selecione uma etapa...</option>
                        <?php if (isset($etapas) && !empty($etapas)): ?>
                            <?php foreach ($etapas as $e): ?>
                            <option value="<?php echo $e->id; ?>">#<?php echo $e->numero_etapa; ?> - <?php echo $e->nome; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Visível ao cliente -->
            <div class="atividades-form-group">
                <label class="atividades-form-checkbox" style="margin: 0;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <input type="checkbox" name="visivel_cliente" value="1" checked style="width: 20px; height: 20px; margin: 0;">
                        <div>
                            <div class="atividades-form-checkbox-label">Visível ao cliente</div>
                            <div class="atividades-form-checkbox-hint">Marque para que o cliente possa ver esta atividade no portal</div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="atividades-btn-cancel" data-dismiss="modal">
                <i class="icon-remove"></i> Cancelar
            </button>
            <button type="submit" class="atividades-btn-submit">
                <i class="icon-save"></i> Salvar Atividade
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<script>
// Filtro de atividades
function filtrarAtividades() {
    const search = document.getElementById('searchAtividade').value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const status = document.getElementById('filterStatus').value;
    const tipo = document.getElementById('filterTipo').value;
    const cards = document.querySelectorAll('.atividade-card');

    cards.forEach(card => {
        const titulo = card.getAttribute('data-titulo');
        const cardStatus = card.getAttribute('data-status');
        const cardTipo = card.getAttribute('data-tipo');

        const matchSearch = !search || titulo.includes(search);
        const matchStatus = !status || cardStatus === status;
        const matchTipo = !tipo || cardTipo === tipo;

        card.style.display = matchSearch && matchStatus && matchTipo ? 'block' : 'none';
    });
}

// Focus no campo título quando abrir o modal
$('#modalAdicionar').on('shown.bs.modal', function () {
    $('#titulo').focus();
});

// Animação de entrada
$(document).ready(function() {
    $('.atividade-card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});

// Auto-refresh a cada 10 segundos se a aba estiver visível
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        if (!document.hidden) {
            location.reload();
        }
    }, 10000); // 10 segundos
}

function stopAutoRefresh() {
    clearInterval(refreshInterval);
}

// Iniciar auto-refresh quando a página carregar
$(document).ready(function() {
    startAutoRefresh();
});

// Parar refresh quando o modal estiver aberto (para não perder dados do formulário)
$('#modalAdicionar').on('shown.bs.modal', function () {
    stopAutoRefresh();
});

$('#modalAdicionar').on('hidden.bs.modal', function () {
    startAutoRefresh();
});

// Função para confirmar exclusão de atividade
function confirmarExclusaoAtividade(atividadeId, titulo) {
    // Parar o auto-refresh para não interferir no modal
    stopAutoRefresh();

    swal({
        title: "Confirmar Exclusão",
        text: "Deseja realmente excluir a atividade: " + titulo + "?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sim, excluir!",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, function() {
        window.location.href = "<?php echo site_url('obras/excluirAtividade/'); ?>" + atividadeId;
    });

    // Reiniciar auto-refresh quando o alerta for fechado
    setTimeout(function() {
        startAutoRefresh();
    }, 2000);
}
</script>
