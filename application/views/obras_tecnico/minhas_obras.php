<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.obras-tec-container { padding: 20px; max-width: 1400px; margin: 0 auto; }

/* Header */
.obras-tec-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.obras-tec-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}
.obras-tec-title h1 {
    margin: 0 0 5px 0;
    font-size: 32px;
    font-weight: 700;
}
.obras-tec-subtitle {
    opacity: 0.95;
    font-size: 16px;
}
.obras-tec-stats {
    display: flex;
    gap: 20px;
}
.obras-tec-stat {
    background: rgba(255,255,255,0.25);
    padding: 20px 35px;
    border-radius: 15px;
    text-align: center;
    backdrop-filter: blur(10px);
}
.obras-tec-stat-value {
    font-size: 36px;
    font-weight: 700;
    line-height: 1;
}
.obras-tec-stat-label {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.95;
    margin-top: 5px;
}

/* Atividades do Dia */
.atividades-hoje {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}
.atividades-hoje-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.atividades-hoje-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.atividades-hoje-title i { color: #667eea; font-size: 24px; }
.atividades-hoje-count {
    background: #667eea;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

/* Obras Grid */
.obras-tec-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 25px;
}

/* Obra Card */
.obra-tec-card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 25px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}
.obra-tec-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.obra-tec-header-card {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
}
.obra-tec-header-card.andamento { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.obra-tec-header-card.concluida { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.obra-tec-header-card.paralisada { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.obra-tec-status {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.25);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.obra-tec-title-card {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 5px;
    line-height: 1.3;
}
.obra-tec-cliente {
    font-size: 14px;
    opacity: 0.9;
}

.obra-tec-body {
    padding: 20px;
}
.obra-tec-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f0f0f0;
}
.obra-tec-info-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.obra-tec-info-label { color: #888; font-size: 13px; }
.obra-tec-info-value { font-weight: 600; color: #333; font-size: 14px; }

.obra-tec-progress {
    margin: 15px 0;
}
.obra-tec-progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}
.obra-tec-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 4px;
}
.obra-tec-progress-text {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #666;
    margin-top: 5px;
}

.obra-tec-footer {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
}
.obra-tec-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}
.obra-tec-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

/* Atividade Item */
.atividade-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 12px;
    border-left: 4px solid #667eea;
}
.atividade-item:last-child { margin-bottom: 0; }
.atividade-item.agendada { border-left-color: #95a5a6; }
.atividade-item.iniciada { border-left-color: #3498db; }
.atividade-item.pausada { border-left-color: #f39c12; }
.atividade-item.concluida { border-left-color: #27ae60; }

.atividade-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}
.atividade-content { flex: 1; }
.atividade-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 3px;
}
.atividade-meta {
    font-size: 13px;
    color: #888;
}
.atividade-status {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
.atividade-status.agendada { background: #ecf0f1; color: #7f8c8d; }
.atividade-status.iniciada { background: #3498db; color: white; }
.atividade-status.pausada { background: #f39c12; color: white; }
.atividade-status.concluida { background: #27ae60; color: white; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-state-icon {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}
.empty-state-title {
    font-size: 22px;
    color: #333;
    margin-bottom: 10px;
}
.empty-state-text {
    color: #888;
    font-size: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .obras-tec-header-content { flex-direction: column; text-align: center; }
    .obras-tec-grid { grid-template-columns: 1fr; }
    .obras-tec-stats { justify-content: center; }
}
</style>

<div class="obras-tec-container">
    <!-- Header -->
    <div class="obras-tec-header">
        <div class="obras-tec-header-content">
            <div class="obras-tec-title">
                <h1><i class="icon-building"></i> Minhas Obras</h1>
                <div class="obras-tec-subtitle">Acompanhe suas obras e atividades</div>
            </div>
            <div class="obras-tec-stats">
                <div class="obras-tec-stat">
                    <div class="obras-tec-stat-value"><?php echo count($obras); ?></div>
                    <div class="obras-tec-stat-label">Obras</div>
                </div>
                <div class="obras-tec-stat">
                    <div class="obras-tec-stat-value"><?php echo count($atividades_hoje); ?></div>
                    <div class="obras-tec-stat-label">Atividades Hoje</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atividades do Dia -->
    <?php if (!empty($atividades_hoje)): ?>
    <div class="atividades-hoje">
        <div class="atividades-hoje-header">
            <div class="atividades-hoje-title">
                <i class="icon-calendar"></i> Atividades de Hoje
            </div>
            <div class="atividades-hoje-count"><?php echo count($atividades_hoje); ?> atividade(s)</div>
        </div>
        <?php foreach ($atividades_hoje as $ativ): ?>
        <div class="atividade-item <?php echo $ativ->status; ?>">
            <div class="atividade-icon">
                <i class="icon-tasks"></i>
            </div>
            <div class="atividade-content">
                <div class="atividade-title"><?php echo $ativ->titulo; ?></div>
                <div class="atividade-meta">
                    <i class="icon-building"></i> <?php echo $ativ->obra_nome ?? 'Obra não definida'; ?>
                    <?php if ($ativ->hora_inicio): ?> | <i class="icon-time"></i> <?php echo substr($ativ->hora_inicio, 0, 5); ?>
                    <?php endif; ?
                </div>
            </div>
            <div class="atividade-status <?php echo $ativ->status; ?>">
                <?php
                $statusLabels = [
                    'agendada' => 'Agendada',
                    'iniciada' => 'Em Execução',
                    'pausada' => 'Pausada',
                    'concluida' => 'Concluída'
                ];
                echo $statusLabels[$ativ->status] ?? $ativ->status;
                ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Obras -->
    <?php if (!empty($obras)): ?>
    <div class="obras-tec-grid">
        <?php foreach ($obras as $obra):
        $statusClass = strtolower(str_replace([' ', 'ã', 'õ', 'ç', 'í'], ['_', 'a', 'o', 'c', 'i'], $obra->status));
        ?>
        <div class="obra-tec-card">
            <div class="obra-tec-header-card <?php echo $statusClass; ?>">
                <div class="obra-tec-status"><?php echo $obra->status; ?></div>
                <div class="obra-tec-title-card"><?php echo $obra->nome; ?></div>
                <div class="obra-tec-cliente"><i class="icon-user"></i> <?php echo $obra->cliente_nome ?? 'Cliente não definido'; ?></div>
            </div>

            <div class="obra-tec-body">
                <div class="obra-tec-info-row">
                    <span class="obra-tec-info-label">Endereço</span>
                    <span class="obra-tec-info-value"><?php echo $obra->endereco ?: 'N/A'; ?></span>
                </div>

                <div class="obra-tec-info-row">
                    <span class="obra-tec-info-label">Previsão de Término</span>
                    <span class="obra-tec-info-value">
                        <?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'N/A'; ?>
                    </span>
                </div>

                <div class="obra-tec-progress">
                    <div class="obra-tec-progress-bar">
                        <div class="obra-tec-progress-fill" style="width: <?php echo $obra->percentual_concluido ?? 0; ?>%"></div>
                    </div>
                    <div class="obra-tec-progress-text">
                        <span>Progresso</span>
                        <span><?php echo $obra->percentual_concluido ?? 0; ?>%</span>
                    </div>
                </div>
            </div>

            <div class="obra-tec-footer">
                <a href="<?php echo site_url('obras_tecnico/obra/' . $obra->id); ?>" class="obra-tec-btn">
                    <i class="icon-eye-open"></i> Acessar Obra
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon"><i class="icon-building"></i></div>
        <div class="empty-state-title">Nenhuma obra atribuída</div>
        <div class="empty-state-text">Você ainda não está designado para nenhuma obra.</div>
    </div>
    <?php endif; ?>
</div>
