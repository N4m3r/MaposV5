<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.etapas-modern {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.etapas-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.etapas-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.etapas-header-left {
    flex: 1;
}
.etapas-breadcrumb {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 10px;
}
.etapas-breadcrumb a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.3s;
}
.etapas-breadcrumb a:hover {
    opacity: 1;
    text-decoration: underline;
}
.etapas-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}
.etapas-header h1 i {
    font-size: 32px;
}
.etapas-subtitle {
    margin-top: 8px;
    opacity: 0.9;
    font-size: 15px;
}

.etapas-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.etapas-btn {
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
.etapas-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.etapas-btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
}
.etapas-btn-secondary:hover {
    background: rgba(255,255,255,0.3);
}
.etapas-btn-primary {
    background: white;
    color: #667eea;
}
.etapas-btn-primary:hover {
    background: #f8f9fa;
}

/* Stats Cards */
.etapas-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}
.etapas-stat-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s;
}
.etapas-stat-card:hover {
    transform: translateY(-3px);
}
.etapas-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.etapas-stat-icon.total { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
.etapas-stat-icon.concluidas { background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
.etapas-stat-icon.andamento { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
.etapas-stat-icon.pendentes { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
.etapas-stat-content {
    flex: 1;
}
.etapas-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    line-height: 1;
}
.etapas-stat-label {
    font-size: 13px;
    color: #888;
    margin-top: 4px;
}

/* Timeline Container */
.etapas-timeline-container {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    position: relative;
}
.etapas-timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}
.etapas-timeline-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.etapas-timeline-title i {
    color: #667eea;
    font-size: 24px;
}

/* Timeline */
.etapas-timeline {
    position: relative;
    padding-left: 40px;
}
.etapas-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #667eea, #764ba2);
    border-radius: 3px;
}

.etapas-item {
    position: relative;
    margin-bottom: 30px;
    transition: all 0.3s;
}
.etapas-item:last-child {
    margin-bottom: 0;
}
.etapas-item:hover .etapas-card {
    transform: translateX(5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.etapas-dot {
    position: absolute;
    left: -33px;
    top: 20px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 1;
    transition: transform 0.3s;
}
.etapas-item:hover .etapas-dot {
    transform: scale(1.2);
}
.etapas-dot.pendente { background: #95a5a6; }
.etapas-dot.em_andamento { background: #3498db; animation: pulse 2s infinite; }
.etapas-dot.concluida { background: #27ae60; }
.etapas-dot.atrasada { background: #e74c3c; }

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(52, 152, 219, 0); }
}

.etapas-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
    border-left: 5px solid transparent;
    transition: all 0.3s;
    cursor: pointer;
}
.etapas-card.pendente { border-left-color: #95a5a6; }
.etapas-card.em_andamento { border-left-color: #3498db; background: #ebf5fb; }
.etapas-card.concluida { border-left-color: #27ae60; background: #eafaf1; }
.etapas-card.atrasada { border-left-color: #e74c3c; background: #fdedec; }

.etapas-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}
.etapas-card-title-section {
    flex: 1;
}
.etapas-card-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 8px;
}
.etapas-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
}
.etapas-card-subtitle {
    font-size: 13px;
    color: #666;
    margin-top: 4px;
}

.etapas-status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.etapas-status-badge.pendente { background: #ecf0f1; color: #7f8c8d; }
.etapas-status-badge.em_andamento { background: #3498db; color: white; }
.etapas-status-badge.concluida { background: #27ae60; color: white; }
.etapas-status-badge.atrasada { background: #e74c3c; color: white; }

.etapas-card-body {
    margin-bottom: 15px;
}
.etapas-card-desc {
    font-size: 14px;
    color: #555;
    line-height: 1.6;
    margin-bottom: 15px;
}

.etapas-dates {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.etapas-date-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #666;
}
.etapas-date-item i {
    color: #667eea;
    font-size: 16px;
}

.etapas-progress-section {
    margin: 15px 0;
    padding: 15px;
    background: white;
    border-radius: 10px;
}
.etapas-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.etapas-progress-label {
    font-size: 13px;
    color: #666;
}
.etapas-progress-value {
    font-size: 18px;
    font-weight: 700;
    color: #667eea;
}
.etapas-progress-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}
.etapas-progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s ease;
}

.etapas-card-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}
.etapas-card-btn {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.etapas-card-btn:hover {
    transform: translateY(-2px);
}
.etapas-card-btn-edit {
    background: #e3f2fd;
    color: #1976d2;
}
.etapas-card-btn-edit:hover {
    background: #1976d2;
    color: white;
}
.etapas-card-btn-delete {
    background: #ffebee;
    color: #c62828;
}
.etapas-card-btn-delete:hover {
    background: #c62828;
    color: white;
}

/* Empty State */
.etapas-empty {
    text-align: center;
    padding: 60px 20px;
}
.etapas-empty-icon {
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
.etapas-empty h3 {
    font-size: 22px;
    color: #333;
    margin-bottom: 10px;
}
.etapas-empty p {
    color: #888;
    margin-bottom: 25px;
}

/* Modal Moderno */
.modal-etapas .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    border: none;
    border-radius: 15px 15px 0 0;
}
.modal-etapas .modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}
.modal-etapas .modal-header .close {
    color: white;
    opacity: 0.9;
    font-size: 28px;
    font-weight: 300;
    text-shadow: none;
}
.modal-etapas .modal-body {
    padding: 30px;
    background: #fafafa;
}
.modal-etapas .modal-footer {
    padding: 20px 30px;
    background: white;
    border-top: 1px solid #e0e0e0;
    border-radius: 0 0 15px 15px;
}

.etapas-form-group {
    margin-bottom: 20px;
}
.etapas-form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}
.etapas-form-label .required {
    color: #e74c3c;
}
.etapas-form-input,
.etapas-form-select,
.etapas-form-textarea {
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
.etapas-form-input:focus,
.etapas-form-select:focus,
.etapas-form-textarea:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}
.etapas-form-textarea {
    resize: vertical;
    min-height: 100px;
}
.etapas-form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.etapas-form-hint {
    font-size: 12px;
    color: #888;
    margin-top: 6px;
}

.etapas-btn-submit {
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
.etapas-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}
.etapas-btn-cancel {
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
.etapas-btn-cancel:hover {
    background: #e0e0e0;
}

/* Responsive */
@media (max-width: 768px) {
    .etapas-header-content {
        flex-direction: column;
    }
    .etapas-timeline {
        padding-left: 30px;
    }
    .etapas-timeline::before {
        left: 10px;
    }
    .etapas-dot {
        left: -26px;
        width: 20px;
        height: 20px;
    }
    .etapas-form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="etapas-modern">
    <!-- Header -->
    <div class="etapas-header">
        <div class="etapas-header-content">
            <div class="etapas-header-left">
                <div class="etapas-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>"><i class="icon-arrow-left"></i> Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a> &raquo;
                    <span>Etapas</span>
                </div>
                <h1><i class="icon-tasks"></i> Etapas da Obra</h1>
                <div class="etapas-subtitle">Gerencie as etapas e acompanhe o progresso da obra</div>
            </div>
            <div class="etapas-actions">
                <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="etapas-btn etapas-btn-secondary">
                    <i class="icon-eye-open"></i> Ver Obra
                </a>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                <button onclick="$('#modalAdicionarEtapa').modal('show')" class="etapas-btn etapas-btn-primary">
                    <i class="icon-plus"></i> Nova Etapa
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <?php
    $total = count($etapas);
    $concluidas = count(array_filter($etapas, function($e) { return $e->status == 'Concluida'; }));
    $em_andamento = count(array_filter($etapas, function($e) { return $e->status == 'EmAndamento'; }));
    $pendentes = count(array_filter($etapas, function($e) { return $e->status == 'NaoIniciada'; }));
    ?>
    <div class="etapas-stats">
        <div class="etapas-stat-card">
            <div class="etapas-stat-icon total"><i class="icon-tasks"></i></div>
            <div class="etapas-stat-content">
                <div class="etapas-stat-value"><?php echo $total; ?></div>
                <div class="etapas-stat-label">Total de Etapas</div>
            </div>
        </div>
        <div class="etapas-stat-card">
            <div class="etapas-stat-icon concluidas"><i class="icon-check"></i></div>
            <div class="etapas-stat-content">
                <div class="etapas-stat-value"><?php echo $concluidas; ?></div>
                <div class="etapas-stat-label">Concluídas</div>
            </div>
        </div>
        <div class="etapas-stat-card">
            <div class="etapas-stat-icon andamento"><i class="icon-refresh"></i></div>
            <div class="etapas-stat-content">
                <div class="etapas-stat-value"><?php echo $em_andamento; ?></div>
                <div class="etapas-stat-label">Em Andamento</div>
            </div>
        </div>
        <div class="etapas-stat-card">
            <div class="etapas-stat-icon pendentes"><i class="icon-time"></i></div>
            <div class="etapas-stat-content">
                <div class="etapas-stat-value"><?php echo $pendentes; ?></div>
                <div class="etapas-stat-label">Pendentes</div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="etapas-timeline-container">
        <div class="etapas-timeline-header">
            <div class="etapas-timeline-title">
                <i class="icon-road"></i> Linha do Tempo das Etapas
            </div>
        </div>

        <?php if (!empty($etapas)): ?>
        <div class="etapas-timeline">
            <?php foreach ($etapas as $index => $etapa): ?>
            <?php
            // Mapear status do ENUM para classes CSS
            $statusMap = [
                'NaoIniciada' => 'pendente',
                'EmAndamento' => 'em_andamento',
                'Concluida' => 'concluida',
                'Atrasada' => 'atrasada',
                'Paralisada' => 'atrasada'
            ];
            $statusClass = $statusMap[$etapa->status] ?? 'pendente';
            $statusLabels = [
                'NaoIniciada' => 'Não Iniciada',
                'EmAndamento' => 'Em Andamento',
                'Concluida' => 'Concluída',
                'Atrasada' => 'Atrasada',
                'Paralisada' => 'Paralisada'
            ];
            $statusLabel = $statusLabels[$etapa->status] ?? $etapa->status;
            $progresso = $etapa->percentual_concluido ?? ($etapa->status == 'Concluida' ? 100 : ($etapa->status == 'EmAndamento' ? 50 : 0));

            // Calcular cor da barra de progresso
            if ($progresso < 30) {
                $progressoColor = 'linear-gradient(90deg, #ff6b6b, #ee5a52)';
            } elseif ($progresso < 70) {
                $progressoColor = 'linear-gradient(90deg, #feca57, #ff9f43)';
            } else {
                $progressoColor = 'linear-gradient(90deg, #1dd1a1, #10ac84)';
            }
            ?>
            <div class="etapas-item">
                <div class="etapas-dot <?php echo $statusClass; ?>"></div>
                <div class="etapas-card <?php echo $statusClass; ?>">
                    <div class="etapas-card-header">
                        <div class="etapas-card-title-section">
                            <div class="etapas-card-number"><?php echo $etapa->numero_etapa; ?></div>
                            <h3 class="etapas-card-title"><?php echo htmlspecialchars($etapa->nome); ?></h3>
                            <?php if ($etapa->especialidade): ?>
                            <div class="etapas-card-subtitle">
                                <i class="icon-briefcase"></i> <?php echo htmlspecialchars($etapa->especialidade); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <span class="etapas-status-badge <?php echo $statusClass; ?>">
                            <?php echo $statusLabel; ?>
                        </span>
                    </div>

                    <div class="etapas-card-body">
                        <?php if ($etapa->descricao): ?>
                        <div class="etapas-card-desc">
                            <?php echo nl2br(htmlspecialchars($etapa->descricao)); ?>
                        </div>
                        <?php endif; ?>

                        <div class="etapas-dates">
                            <?php if ($etapa->data_inicio_prevista): ?>
                            <div class="etapas-date-item">
                                <i class="icon-calendar"></i>
                                <span>Início: <?php echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($etapa->data_fim_prevista): ?>
                            <div class="etapas-date-item">
                                <i class="icon-calendar-check"></i>
                                <span>Término: <?php echo date('d/m/Y', strtotime($etapa->data_fim_prevista)); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="etapas-progress-section">
                            <div class="etapas-progress-header">
                                <span class="etapas-progress-label">Progresso</span>
                                <span class="etapas-progress-value"><?php echo $progresso; ?>%</span>
                            </div>
                            <div class="etapas-progress-bar">
                                <div class="etapas-progress-fill" style="width: <?php echo $progresso; ?>%; background: <?php echo $progressoColor; ?>"></div>
                            </div>
                        </div>
                    </div>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                    <div class="etapas-card-footer">
                        <a href="<?php echo site_url('obras/editarEtapa/' . $etapa->id); ?>" class="etapas-card-btn etapas-card-btn-edit">
                            <i class="icon-edit"></i> Editar
                        </a>
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                        <a href="<?php echo site_url('obras/excluirEtapa/' . $etapa->id); ?>" class="etapas-card-btn etapas-card-btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta etapa?');">
                            <i class="icon-trash"></i> Excluir
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="etapas-empty">
            <div class="etapas-empty-icon">
                <i class="icon-tasks"></i>
            </div>
            <h3>Nenhuma etapa cadastrada</h3>
            <p>Adicione etapas para organizar e acompanhar o progresso da obra.</p>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
            <button onclick="$('#modalAdicionarEtapa').modal('show')" class="etapas-btn etapas-btn-primary" style="display: inline-flex;">
                <i class="icon-plus"></i> Adicionar Primeira Etapa
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Adicionar Etapa -->
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
<div id="modalAdicionarEtapa" class="modal hide fade modal-etapas" tabindex="-1" role="dialog" aria-labelledby="modalEtapaLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="modalEtapaLabel"><i class="icon-plus-sign"></i> Nova Etapa</h3>
    </div>

    <form action="<?php echo site_url('obras/adicionarEtapa'); ?>" method="post">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

            <div class="etapas-form-row">
                <div class="etapas-form-group">
                    <label class="etapas-form-label" for="numero_etapa">
                        <i class="icon-sort-by-order"></i> Número da Etapa <span class="required">*</span>
                    </label>
                    <input type="number" name="numero_etapa" id="numero_etapa" class="etapas-form-input" value="<?php echo count($etapas) + 1; ?>" min="1" required>
                    <div class="etapas-form-hint">Ordem de execução desta etapa</div>
                </div>

                <div class="etapas-form-group">
                    <label class="etapas-form-label" for="nome">
                        <i class="icon-tag"></i> Nome da Etapa <span class="required">*</span>
                    </label>
                    <input type="text" name="nome" id="nome" class="etapas-form-input" maxlength="100" placeholder="Ex: Fundação, Estrutura, Acabamento..." required>
                </div>
            </div>

            <div class="etapas-form-group">
                <label class="etapas-form-label" for="especialidade">
                    <i class="icon-briefcase"></i> Especialidade
                </label>
                <select name="especialidade" id="especialidade" class="etapas-form-select">
                    <option value="">Selecione uma especialidade...</option>
                    <option value="Alvenaria">Alvenaria</option>
                    <option value="Arquitetura">Arquitetura</option>
                    <option value="Elétrica">Elétrica</option>
                    <option value="Estrutura">Estrutura</option>
                    <option value="Fundação">Fundação</option>
                    <option value="Hidráulica">Hidráulica</option>
                    <option value="Impermeabilização">Impermeabilização</option>
                    <option value="Marcenaria">Marcenaria</option>
                    <option value="Pintura">Pintura</option>
                    <option value="Piso/Revestimento">Piso/Revestimento</option>
                    <option value="Serralheria">Serralheria</option>
                    <option value="Telhado">Telhado</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>

            <div class="etapas-form-group">
                <label class="etapas-form-label" for="descricao">
                    <i class="icon-align-left"></i> Descrição
                </label>
                <textarea name="descricao" id="descricao" class="etapas-form-textarea" rows="3" placeholder="Descreva os detalhes desta etapa..."></textarea>
            </div>

            <div class="etapas-form-row">
                <div class="etapas-form-group">
                    <label class="etapas-form-label" for="data_inicio_prevista">
                        <i class="icon-calendar"></i> Data Início Prevista
                    </label>
                    <input type="date" name="data_inicio_prevista" id="data_inicio_prevista" class="etapas-form-input">
                </div>

                <div class="etapas-form-group">
                    <label class="etapas-form-label" for="data_fim_prevista">
                        <i class="icon-calendar-check"></i> Data Término Prevista
                    </label>
                    <input type="date" name="data_fim_prevista" id="data_fim_prevista" class="etapas-form-input">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="etapas-btn-cancel" data-dismiss="modal">
                <i class="icon-remove"></i> Cancelar
            </button>
            <button type="submit" class="etapas-btn-submit">
                <i class="icon-save"></i> Salvar Etapa
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<script>
// Focus no primeiro campo quando abrir o modal
$('#modalAdicionarEtapa').on('shown.bs.modal', function () {
    $('#numero_etapa').focus();
});

// Animação de entrada dos cards
$(document).ready(function() {
    $('.etapas-item').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(400);
    });
});
</script>
