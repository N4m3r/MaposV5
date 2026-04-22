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


    <!-- Grid de Atividades (Mesclado: Sistema Antigo + Wizard) -->
    <?php $this->load->view('obras/atividades_list_new'); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Iniciar Registro de Atividade (Hora Início/Fim) -->
<div id="modalIniciarRegistro" class="modal hide fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8;">&times;</button>
        <h3 id="modalRegistroLabel"><i class="bx bx-timer"></i> Iniciar Atividade - Registro de Tempo</h3>
    </div>

    <form id="formIniciarRegistro" onsubmit="return iniciarRegistroAtividade(event)">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <input type="hidden" name="latitude" id="registro_latitude">
            <input type="hidden" name="longitude" id="registro_longitude">

            <!-- Seleção de Etapa (OBRIGATÓRIA) -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="etapa_id_registro">
                    <i class="bx bx-layer"></i> Etapa da Obra <span style="color: #dc3545;">*</span>
                </label>
                <select name="etapa_id" id="etapa_id_registro" class="atividades-form-select" required>
                    <option value="">Selecione uma etapa...</option>
                    <?php if (isset($etapas) && !empty($etapas)): ?>
                        <?php foreach ($etapas as $e): ?>
                        <option value="<?php echo $e->id; ?>">
                            #<?php echo $e->numero_etapa ?? 'N/A'; ?> - <?php echo $e->nome; ?>
                            <?php if (isset($e->progresso_real) && $e->progresso_real > 0): ?>
                                (<?php echo $e->progresso_real; ?>%)
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Nenhuma etapa cadastrada</option>
                    <?php endif; ?>
                </select>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                    <i class="bx bx-info-circle"></i> Selecione a etapa em que você está trabalhando. Obrigatório.
                </div>
            </div>

            <!-- Tipo de Atividade -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="tipo_id_registro">
                    <i class="bx bx-wrench"></i> Tipo de Atividade <span style="color: #dc3545;">*</span>
                </label>
                <select name="tipo_id" id="tipo_id_registro" class="atividades-form-select" required>
                    <option value="">Selecione o tipo...</option>
                    <?php if (!empty($tipos_atividades)): ?>
                        <optgroup label="Rede Estruturada">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'rede'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="rede">
                                <i class="bx bx-network-chart"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="CFTV">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'cftv'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="cftv">
                                <i class="bx bx-camera"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Infraestrutura">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'infra'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="infra">
                                <i class="bx bx-server"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Segurança">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if ($tipo->categoria == 'seguranca'): ?>
                            <option value="<?php echo $tipo->idTipo; ?>" data-categoria="seguranca">
                                <i class="bx bx-shield"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Geral">
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <?php if (!in_array($tipo->categoria, ['rede', 'cftv', 'infra', 'seguranca'])): ?>
                            <option value="<?php echo $tipo->idTipo; ?>">
                                <i class="bx bx-wrench"></i> <?php echo $tipo->nome; ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </optgroup>
                    <?php else: ?>
                        <option value="1">Trabalho Técnico</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Descrição -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="descricao_registro">
                    <i class="bx bx-detail"></i> Descrição da Atividade
                </label>
                <textarea name="descricao" id="descricao_registro" class="atividades-form-textarea" rows="2" placeholder="Descreva o trabalho que será realizado..."></textarea>
            </div>

            <!-- Equipamento/Local -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="equipamento_registro">
                    <i class="bx bx-wrench"></i> Equipamento/Local
                </label>
                <input type="text" name="equipamento" id="equipamento_registro" class="atividades-form-input" placeholder="Ex: Rack principal, Câmera 1, Sala do servidor...">
            </div>

            <!-- GPS -->
            <div class="atividades-form-group">
                <label class="atividades-form-label">
                    <i class="bx bx-map"></i> Localização GPS
                </label>
                <button type="button" class="btn btn-info" onclick="obterLocalizacaoRegistro()">
                    <i class="bx bx-map-pin"></i> Obter Localização
                </button>
                <div id="gps_info_registro" style="margin-top: 10px; font-size: 12px; color: #666;">
                    <i class="bx bx-info-circle"></i> Clique no botão acima para registrar sua localização.
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="atividades-btn-cancel" data-dismiss="modal">
                <i class="bx bx-x"></i> Cancelar
            </button>
            <button type="submit" class="atividades-btn-submit" id="btnIniciarRegistro" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <i class="bx bx-play"></i> INICIAR ATIVIDADE
            </button>
        </div>
    </form>
</div>

<script>
// Função para obter localização GPS
function obterLocalizacaoRegistro() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('registro_latitude').value = position.coords.latitude;
                document.getElementById('registro_longitude').value = position.coords.longitude;
                document.getElementById('gps_info_registro').innerHTML =
                    '<i class="bx bx-check-circle" style="color: #28a745;"></i> Localização obtida com sucesso!';
            },
            function(error) {
                document.getElementById('gps_info_registro').innerHTML =
                    '<i class="bx bx-error-circle" style="color: #dc3545;"></i> Erro ao obter localização: ' + error.message;
            }
        );
    } else {
        document.getElementById('gps_info_registro').innerHTML =
            '<i class="bx bx-error-circle" style="color: #dc3545;"></i> GPS não disponível no dispositivo.';
    }
}

// Função para iniciar o registro de atividade
function iniciarRegistroAtividade(event) {
    event.preventDefault();

    const form = document.getElementById('formIniciarRegistro');
    const formData = new FormData(form);

    // Validação
    const etapaId = formData.get('etapa_id');
    const tipoId = formData.get('tipo_id');

    if (!etapaId) {
        alert('Por favor, selecione uma etapa da obra.');
        document.getElementById('etapa_id_registro').focus();
        return false;
    }

    if (!tipoId) {
        alert('Por favor, selecione o tipo de atividade.');
        document.getElementById('tipo_id_registro').focus();
        return false;
    }

    // Desabilita botão para evitar duplo clique
    document.getElementById('btnIniciarRegistro').disabled = true;
    document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Iniciando...';

    // Envia requisição AJAX
    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fecha modal e recarrega página
            $('#modalIniciarRegistro').modal('hide');
            alert('Atividade iniciada com sucesso! Hora Início registrada.');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro ao iniciar atividade'));
            document.getElementById('btnIniciarRegistro').disabled = false;
            document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-play"></i> INICIAR ATIVIDADE';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao iniciar atividade. Tente novamente.');
        document.getElementById('btnIniciarRegistro').disabled = false;
        document.getElementById('btnIniciarRegistro').innerHTML = '<i class="bx bx-play"></i> INICIAR ATIVIDADE';
    });

    return false;
}
</script>

<!-- Modal Adicionar Atividade (Integrado com Wizard) -->
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
<div id="modalAdicionar" class="modal hide fade modal-atividades" tabindex="-1" role="dialog" aria-labelledby="modalAtividadeLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalAtividadeLabel"><i class="icon-plus-sign"></i> Nova Atividade - Wizard</h3>
    </div>

    <form id="formAdicionarAtividade" onsubmit="return salvarAtividadeWizard(event)">
        <div class="modal-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">
            <input type="hidden" name="latitude" id="nova_latitude">
            <input type="hidden" name="longitude" id="nova_longitude">

            <!-- Alerta informativo -->
            <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 12px 15px; margin-bottom: 20px; border-radius: 0 8px 8px 0;">
                <i class="icon-info-sign" style="color: #2196f3;"></i>
                <strong>Modo Wizard:</strong> Esta atividade será criada no sistema de atendimento técnico.
            </div>

            <!-- Seleção de Etapa (OBRIGATÓRIA) -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="etapa_id_nova">
                    <i class="icon-tasks"></i> Etapa da Obra <span style="color: #dc3545;">*</span>
                </label>
                <select name="etapa_id" id="etapa_id_nova" class="atividades-form-select" required>
                    <option value="">Selecione uma etapa...</option>
                    <?php if (isset($etapas) && !empty($etapas)): ?>
                        <?php foreach ($etapas as $e): ?>
                        <option value="<?php echo $e->id; ?>">
                            #<?php echo $e->numero_etapa ?? 'N/A'; ?> - <?php echo $e->nome; ?>
                            <?php if (isset($e->progresso_real) && $e->progresso_real > 0): ?>
                                (<?php echo $e->progresso_real; ?>%)
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Nenhuma etapa cadastrada</option>
                    <?php endif; ?>
                </select>
                <div class="atividades-form-hint">
                    <i class="icon-info-sign"></i> Selecione a etapa em que a atividade será executada.
                </div>
            </div>

            <!-- Tipo de Atividade (do wizard) -->
            <?php if (!empty($tipos_atividades)): ?>
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="tipo_id_nova">
                    <i class="icon-wrench"></i> Tipo de Atividade <span style="color: #dc3545;">*</span>
                </label>
                <select name="tipo_id" id="tipo_id_nova" class="atividades-form-select" required>
                    <option value="">Selecione o tipo...</option>
                    <?php foreach ($tipos_atividades as $tipo): ?>
                    <option value="<?php echo $tipo->idTipo; ?>" data-categoria="<?php echo $tipo->categoria ?? 'geral'; ?>">
                        <?php echo $tipo->nome; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
            <input type="hidden" name="tipo_id" value="1">
            <?php endif; ?>

            <!-- Título -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="titulo_nova">
                    <i class="icon-tag"></i> Título da Atividade <span style="color: #dc3545;">*</span>
                </label>
                <input type="text" name="titulo" id="titulo_nova" class="atividades-form-input" placeholder="Ex: Instalação elétrica..." required>
            </div>

            <!-- Descrição -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="descricao_nova">
                    <i class="icon-align-left"></i> Descrição da Atividade
                </label>
                <textarea name="descricao" id="descricao_nova" class="atividades-form-textarea" rows="2" placeholder="Descreva o trabalho que será realizado..."></textarea>
            </div>

            <!-- Equipamento/Local -->
            <div class="atividades-form-group">
                <label class="atividades-form-label" for="equipamento_nova">
                    <i class="icon-wrench"></i> Equipamento/Local
                </label>
                <input type="text" name="equipamento" id="equipamento_nova" class="atividades-form-input" placeholder="Ex: Rack principal, Câmera 1, Sala do servidor...">
            </div>

            <!-- Localização GPS -->
            <div class="atividades-form-group">
                <label class="atividades-form-label">
                    <i class="icon-map-marker"></i> Localização GPS
                </label>
                <button type="button" class="btn btn-info" onclick="obterLocalizacaoNovaAtividade()" style="margin-bottom: 10px;">
                    <i class="icon-map-marker"></i> Obter Localização Atual
                </button>
                <div id="gps_info_nova" class="atividades-form-hint">
                    <i class="icon-info-sign"></i> Clique para registrar a localização.
                </div>
            </div>

            <hr style="margin: 20px 0; border-color: #e0e0e0;">

            <!-- Campos adicionais -->
            <div class="atividades-form-row">
                <!-- Técnico Responsável -->
                <div class="atividades-form-group">
                    <label class="atividades-form-label" for="tecnico_id_nova">
                        <i class="icon-user"></i> Técnico Responsável
                    </label>
                    <select name="tecnico_id" id="tecnico_id_nova" class="atividades-form-select">
                        <option value="">Selecione um técnico...</option>
                        <?php if (!empty($tecnicos)): ?>
                            <?php foreach ($tecnicos as $t): ?>
                            <option value="<?php echo $t->idUsuarios; ?>"><?php echo $t->nome; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Visível ao Cliente -->
                <div class="atividades-form-group">
                    <label class="atividades-form-checkbox" style="margin: 10px 0 0 0;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <input type="checkbox" name="visivel_cliente" value="1" checked style="width: 20px; height: 20px; margin: 0;">
                            <div>
                                <div class="atividades-form-checkbox-label">Visível ao cliente</div>
                                <div class="atividades-form-checkbox-hint">O cliente poderá ver esta atividade</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="atividades-btn-cancel" data-dismiss="modal">
                <i class="icon-remove"></i> Cancelar
            </button>
            <button type="submit" class="atividades-btn-submit" id="btnSalvarAtividade" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <i class="icon-save"></i> CRIAR ATIVIDADE
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<script>
// Função para obter localização GPS
function obterLocalizacaoNovaAtividade() {
    if ('geolocation' in navigator) {
        document.getElementById('gps_info_nova').innerHTML = '<i class="icon-time"></i> Obtendo localização...';
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('nova_latitude').value = position.coords.latitude;
                document.getElementById('nova_longitude').value = position.coords.longitude;
                document.getElementById('gps_info_nova').innerHTML = '<i class="icon-ok" style="color: #28a745;"></i> Localização: ' + position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude.toFixed(6);
            },
            function(error) {
                document.getElementById('gps_info_nova').innerHTML = '<i class="icon-remove" style="color: #dc3545;"></i> Erro: ' + error.message;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    } else {
        document.getElementById('gps_info_nova').innerHTML = '<i class="icon-remove" style="color: #dc3545;"></i> GPS não disponível.';
    }
}

// Função para salvar atividade no formato do wizard
function salvarAtividadeWizard(event) {
    event.preventDefault();

    const form = document.getElementById('formAdicionarAtividade');
    const formData = new FormData(form);

    // Validação
    const etapaId = formData.get('etapa_id');
    const tipoId = formData.get('tipo_id');
    const titulo = formData.get('titulo');

    if (!etapaId) {
        alert('Por favor, selecione uma etapa da obra.');
        document.getElementById('etapa_id_nova').focus();
        return false;
    }

    if (!tipoId) {
        alert('Por favor, selecione o tipo de atividade.');
        document.getElementById('tipo_id_nova').focus();
        return false;
    }

    if (!titulo || titulo.trim() === '') {
        alert('Por favor, informe o título da atividade.');
        document.getElementById('titulo_nova').focus();
        return false;
    }

    // Desabilita botão para evitar duplo clique
    const btn = document.getElementById('btnSalvarAtividade');
    btn.disabled = true;
    btn.innerHTML = '<i class="icon-time"></i> Salvando...';

    // Envia requisição AJAX
    fetch('<?php echo site_url("atividades/checkin_obra"); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#modalAdicionar').modal('hide');
            alert('Atividade criada com sucesso no sistema de atendimento!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro ao criar atividade'));
            btn.disabled = false;
            btn.innerHTML = '<i class="icon-save"></i> CRIAR ATIVIDADE';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar atividade. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = '<i class="icon-save"></i> CRIAR ATIVIDADE';
    });

    return false;
}
</script>

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
</script>
