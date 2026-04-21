<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.obra-dash-container { padding: 20px; max-width: 1400px; margin: 0 auto; }

/* Header */
.obra-dash-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.obra-dash-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.obra-dash-title h1 {
    margin: 0 0 5px 0;
    font-size: 28px;
    font-weight: 700;
}
.obra-dash-subtitle {
    opacity: 0.95;
    font-size: 16px;
}
.obra-dash-subtitle a {
    color: white;
    text-decoration: none;
}
.obra-dash-subtitle a:hover {
    text-decoration: underline;
}
.obra-dash-progress {
    text-align: center;
    min-width: 150px;
}
.obra-dash-progress-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 700;
    margin: 0 auto 10px;
    border: 4px solid rgba(255,255,255,0.3);
}
.obra-dash-progress-label {
    font-size: 14px;
    opacity: 0.9;
}

/* Content Grid */
.obra-dash-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

/* Cards */
.obra-dash-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}
.obra-dash-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.obra-dash-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.obra-dash-card-title i { color: #667eea; font-size: 22px; }

/* Atividades List */
.atividades-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.atividade-card {
    display: flex;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    transition: all 0.3s;
}
.atividade-card:hover {
    background: #f0f2f5;
    transform: translateX(5px);
}
.atividade-card.agendada { border-left-color: #95a5a6; }
.atividade-card.iniciada { border-left-color: #3498db; }
.atividade-card.pausada { border-left-color: #f39c12; }
.atividade-card.concluida { border-left-color: #27ae60; }

.atividade-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}
.atividade-info { flex: 1; }
.atividade-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    font-size: 16px;
}
.atividade-meta {
    font-size: 13px;
    color: #888;
    margin-bottom: 8px;
}
.atividade-meta span {
    margin-right: 15px;
}
.atividade-status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
.atividade-status-badge.agendada { background: #ecf0f1; color: #7f8c8d; }
.atividade-status-badge.iniciada { background: #3498db; color: white; }
.atividade-status-badge.pausada { background: #f39c12; color: white; }
.atividade-status-badge.concluida { background: #27ae60; color: white; }

.atividade-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.btn-acao {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
}
.btn-acao:hover { transform: translateY(-2px); }
.btn-acao-iniciar {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
}
.btn-acao-continuar {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}
.btn-acao-visualizar {
    background: #f5f5f5;
    color: #666;
}

/* Etapas */
.etapas-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.etapa-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
}
.etapa-numero {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
}
.etapa-info { flex: 1; }
.etapa-nome {
    font-weight: 600;
    color: #333;
    margin-bottom: 3px;
}
.etapa-meta {
    font-size: 12px;
    color: #888;
}
.etapa-status {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
.etapa-status.NaoIniciada { background: #ecf0f1; color: #7f8c8d; }
.etapa-status.EmAndamento { background: #3498db; color: white; }
.etapa-status.Concluida { background: #27ae60; color: white; }
.etapa-status.Atrasada { background: #e74c3c; color: white; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}
.empty-state-icon {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 15px;
}
.empty-state-text {
    color: #888;
    font-size: 15px;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 20px;
}
.quick-action-btn {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}
.quick-action-btn:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}
.quick-action-btn i {
    font-size: 24px;
    margin-bottom: 8px;
    display: block;
}
.quick-action-btn span {
    font-size: 13px;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
    .obra-dash-grid { grid-template-columns: 1fr; }
    .obra-dash-header-content { flex-direction: column; text-align: center; }
    .quick-actions { grid-template-columns: 1fr; }
}
</style>

<div class="obra-dash-container">
    <!-- Header -->
    <div class="obra-dash-header">
        <div class="obra-dash-header-content">
            <div class="obra-dash-title">
                <h1><i class="icon-building"></i> <?php echo $obra->nome; ?></h1>
                <div class="obra-dash-subtitle">
                    <i class="icon-user"></i> <?php echo $obra->cliente_nome ?? 'Cliente não definido'; ?> |
                    <i class="icon-map-marker"></i> <?php echo $obra->endereco ?: 'Endereço não informado'; ?>
                </div>
            </div>
            <div class="obra-dash-progress">
                <div class="obra-dash-progress-circle"><?php echo $obra->percentual_concluido ?? 0; ?>%</div>
                <div class="obra-dash-progress-label">Progresso</div>
            </div>
        </div>
    </div>

    <div class="obra-dash-grid">
        <!-- Coluna Principal -->
        <div class="obra-dash-main">
            <!-- Minhas Atividades -->
            <div class="obra-dash-card">
                <div class="obra-dash-card-header">
                    <div class="obra-dash-card-title">
                        <i class="icon-tasks"></i> Minhas Atividades
                    </div>
                </div>

                <?php if (!empty($minhas_atividades)): ?>
                <div class="atividades-list">
                    <?php foreach ($minhas_atividades as $ativ):
                    $statusClass = $ativ->status;
                    ?>
                    <div class="atividade-card <?php echo $statusClass; ?>">
                        <div class="atividade-icon">
                            <i class="icon-tasks"></i>
                        </div>
                        <div class="atividade-info">
                            <div class="atividade-title"><?php echo $ativ->titulo; ?></div>
                            <div class="atividade-meta">
                                <span><i class="icon-calendar"></i> <?php echo date('d/m/Y', strtotime($ativ->data_atividade)); ?></span>
                                <span><i class="icon-time"></i> <?php echo $ativ->hora_inicio ? substr($ativ->hora_inicio, 0, 5) : '--:--'; ?></span>
                            </div>
                            <div class="atividade-status-badge <?php echo $ativ->status; ?>">
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

                        <div class="atividade-actions">
                            <?php if ($ativ->status == 'agendada'): ?>>
                            <a href="<?php echo site_url('obras_tecnico/atividade/' . $ativ->id); ?>" class="btn-acao btn-acao-iniciar">
                                <i class="icon-play"></i> Iniciar
                            </a>
                            <?php elseif ($ativ->status == 'iniciada'): ?>>
                            <a href="<?php echo site_url('obras_tecnico/atividade/' . $ativ->id); ?>" class="btn-acao btn-acao-continuar">
                                <i class="icon-refresh"></i> Continuar
                            </a>
                            <?php else: ?>>
                            <a href="<?php echo site_url('obras_tecnico/atividade/' . $ativ->id); ?>" class="btn-acao btn-acao-visualizar">
                                <i class="icon-eye-open"></i> Ver
                            </a>
                            <?php endif; ?>>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>>
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="icon-tasks"></i></div>
                    <div class="empty-state-text">Nenhuma atividade atribuída para você nesta obra.</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sistema de Registro de Atividades (Hora Início/Fim) -->
            <div class="obra-dash-card" style="border-left: 4px solid #11998e;">
                <div class="obra-dash-card-header">
                    <div class="obra-dash-card-title">
                        <i class="bx bx-timer" style="color: #11998e;"></i> Registro de Atividades
                    </div>
                </div>
                <div style="background: linear-gradient(135deg, #11998e10 0%, #38ef7d10 100%); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                    <p style="margin: 0 0 10px 0; font-size: 14px;">
                        <strong><i class="bx bx-info-circle"></i> Registre Hora Início e Hora Fim</strong>
                        <br>Utilize o novo sistema para registrar atividades com precisão de tempo.
                    </p>
                    <a href="<?php echo site_url('atividades/wizard_obra/' . $obra->id); ?>" class="btn-acao btn-acao-iniciar" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                        <i class="bx bx-play"></i> Iniciar Atividade
                    </a>
                </div>
                <p style="font-size: 12px; color: #666; margin: 0;">
                    <i class="bx bx-check-circle"></i> Registro preciso de tempo<br>
                    <i class="bx bx-camera"></i> Fotos das atividades<br>
                    <i class="bx bx-map"></i> Geolocalização
                </p>
            </div>

            <!-- Etapas -->
            <div class="obra-dash-card">
                <div class="obra-dash-card-header">
                    <div class="obra-dash-card-title">
                        <i class="icon-road"></i> Etapas da Obra
                    </div>
                </div>

                <?php if (!empty($etapas)): ?>
                <div class="etapas-list">
                    <?php foreach ($etapas as $etapa): ?>
                    <div class="etapa-item">
                        <div class="etapa-numero"><?php echo $etapa->numero_etapa; ?></div>
                        <div class="etapa-info">
                            <div class="etapa-nome"><?php echo $etapa->nome; ?></div>
                            <div class="etapa-meta">
                                <?php if ($etapa->data_inicio_prevista): ?>
                                Previsão: <?php echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)); ?>
                                <?php if ($etapa->data_fim_prevista): ?>
                                - <?php echo date('d/m/Y', strtotime($etapa->data_fim_prevista)); ?>
                                <?php endif; ?>>
                                <?php endif; ?>>
                            </div>
                        </div>
                        <div class="etapa-status <?php echo $etapa->status; ?>">
                            <?php
                            $etapaStatus = [
                                'NaoIniciada' => 'Não Iniciada',
                                'EmAndamento' => 'Em Andamento',
                                'Concluida' => 'Concluída',
                                'Atrasada' => 'Atrasada',
                                'Paralisada' => 'Paralisada'
                            ];
                            echo $etapaStatus[$etapa->status] ?? $etapa->status;
                            ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>>
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="icon-road"></i></div>
                    <div class="empty-state-text">Nenhuma etapa cadastrada.</div>
                </div>
                <?php endif; ?>>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="obra-dash-sidebar">
            <!-- Ações Rápidas -->
            <div class="obra-dash-card">
                <div class="obra-dash-card-header">
                    <div class="obra-dash-card-title">
                        <i class="icon-bolt"></i> Ações Rápidas
                    </div>
                </div>

                <div class="quick-actions">
                    <a href="<?php echo site_url('obras_tecnico/minhasObras'); ?>" class="quick-action-btn">
                        <i class="icon-arrow-left"></i>
                        <span>Voltar</span>
                    </a>

                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $obra->cliente_telefone ?? ''); ?>" class="quick-action-btn">
                        <i class="icon-phone"></i>
                        <span>Ligar Cliente</span>
                    </a>
                </div>
            </div>

            <!-- Info da Obra -->
            <div class="obra-dash-card">
                <div class="obra-dash-card-header">
                    <div class="obra-dash-card-title">
                        <i class="icon-info-sign"></i> Informações
                    </div>
                </div>

                <div class="info-list">
                    <div class="obra-tec-info-row" style="margin-bottom: 12px;">
                        <span class="obra-tec-info-label">Status</span>
                        <span class="obra-tec-info-value"><?php echo $obra->status; ?></span>
                    </div>

                    <div class="obra-tec-info-row" style="margin-bottom: 12px;">
                        <span class="obra-tec-info-label">Data Início</span>
                        <span class="obra-tec-info-value">
                            <?php echo $obra->data_inicio_contrato ? date('d/m/Y', strtotime($obra->data_inicio_contrato)) : 'N/A'; ?>
                        </span>
                    </div>

                    <div class="obra-tec-info-row" style="margin-bottom: 12px;">
                        <span class="obra-tec-info-label">Previsão Término</span>
                        <span class="obra-tec-info-value">
                            <?php echo $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : 'N/A'; ?>
                        </span>
                    </div>

                    <?php if ($obra->observacoes): ?>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                        <div class="obra-tec-info-label" style="margin-bottom: 5px;">Observações</div>
                        <div style="font-size: 14px; color: #666; line-height: 1.5;">
                            <?php echo nl2br(htmlspecialchars($obra->observacoes)); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
