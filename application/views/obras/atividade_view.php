<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.atividade-view { padding: 20px; max-width: 1400px; margin: 0 auto; }

/* Header */
.atividade-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.atividade-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.atividade-header-left { flex: 1; }
.atividade-breadcrumb {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 10px;
}
.atividade-breadcrumb a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.3s;
}
.atividade-breadcrumb a:hover {
    opacity: 1;
    text-decoration: underline;
}
.atividade-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}
.atividade-header h1 i { font-size: 32px; }
.atividade-subtitle {
    margin-top: 8px;
    opacity: 0.9;
    font-size: 15px;
}
.atividade-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(10px);
}

/* Content Grid */
.atividade-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

/* Cards */
.atividade-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
    margin-bottom: 25px;
}
.atividade-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.atividade-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.atividade-card-title i { color: #667eea; font-size: 22px; }

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.info-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
}
.info-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.info-value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

/* Progress Section */
.progress-section {
    margin: 20px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
}
.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.progress-label { font-size: 14px; color: #666; }
.progress-value { font-size: 24px; font-weight: 700; color: #667eea; }
.progress-bar {
    height: 12px;
    background: #e0e0e0;
    border-radius: 6px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 6px;
    transition: width 0.5s ease;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #667eea, #764ba2);
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 20px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #667eea;
}
.timeline-item.inicio::before { background: #27ae60; }
.timeline-item.pausa::before { background: #f39c12; }
.timeline-item.retorno::before { background: #3498db; }
.timeline-item.conclusao::before { background: #9b59b6; }
.timeline-item.impedimento::before { background: #e74c3c; }

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.timeline-title {
    font-weight: 600;
    color: #333;
}
.timeline-time {
    font-size: 12px;
    color: #888;
}
.timeline-content {
    font-size: 14px;
    color: #666;
}

/* Checkins */
.checkin-card {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 15px;
}
.checkin-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.checkin-icon.entrada { background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
.checkin-icon.saida { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; }
.checkin-icon.pausa { background: linear-gradient(135deg, #f39c12, #e67e22); color: white; }
.checkin-content { flex: 1; }
.checkin-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}
.checkin-meta {
    font-size: 13px;
    color: #888;
}

/* Actions */
.actions-bar {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
}
.action-btn {
    padding: 12px 24px;
    border-radius: 10px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
}
.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.action-btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}
.action-btn-secondary {
    background: #f5f5f5;
    color: #666;
}

/* Responsive */
@media (max-width: 768px) {
    .atividade-grid { grid-template-columns: 1fr; }
    .info-grid { grid-template-columns: 1fr; }
}
</style>

<div class="atividade-view">
    <!-- Header -->
    <div class="atividade-header">
        <div class="atividade-header-content">
            <div class="atividade-header-left">
                <div class="atividade-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>">Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>"><?php echo $obra->nome; ?></a> &raquo;
                    <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>">Atividades</a> &raquo;
                    <span>Detalhes</span>
                </div>
                <h1><i class="icon-tasks"></i> <?php echo htmlspecialchars($atividade->titulo ?? 'Atividade #' . $atividade->id); ?></h1>
                <div class="atividade-subtitle">
                    <i class="icon-user"></i> Técnico: <?php echo $atividade->tecnico_nome ?? 'Não atribuído'; ?> |
                    <i class="icon-calendar"></i> Data: <?php echo ($atividade->data_atividade ?? null) ? date('d/m/Y', strtotime($atividade->data_atividade)) : 'N/A'; ?>
                </div>
            </div>
            <div class="atividade-status-section">
                <span class="atividade-status-badge">
                    <i class="icon-time"></i>
                    <?php
                    $statusLabels = [
                        'agendada' => 'Agendada',
                        'iniciada' => 'Em Execução',
                        'pausada' => 'Pausada',
                        'concluida' => 'Concluída',
                        'cancelada' => 'Cancelada'
                    ];
                    echo $statusLabels[$atividade->status ?? 'agendada'] ?? ($atividade->status ?? 'Agendada');
                    ?>
                </span>
            </div>
        </div>
    </div>

    <div class="atividade-grid">
        <!-- Main Content -->
        <div class="atividade-main">
            <!-- Informações da Atividade -->
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-info-sign"></i> Informações da Atividade
                    </div>
                </div>
                <!-- Mensagem de status -->
                <?php if (($atividade->status ?? 'agendada') == 'agendada'): ?>
                <div style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="icon-info-sign"></i> <strong>Atividade agendada</strong> - Ainda não foi iniciada. As informações de horário e execução serão preenchidas automaticamente quando o técnico iniciar a atividade.
                </div>
                <?php endif; ?>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Etapa</div>
                        <div class="info-value">
                            <?php
                            if ($atividade->etapa_nome ?? null) {
                                echo '#' . ($atividade->numero_etapa ?? '') . ' ' . $atividade->etapa_nome;
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
                            <?php
                            $tipoLabels = [
                                'trabalho' => 'Trabalho',
                                'impedimento' => 'Impedimento',
                                'visita' => 'Visita',
                                'manutencao' => 'Manutenção',
                                'outro' => 'Outro'
                            ];
                            echo $tipoLabels[$atividade->tipo ?? 'trabalho'] ?? ucfirst($atividade->tipo ?? 'trabalho');
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Hora Início</div>
                        <div class="info-value"><?php echo ($atividade->hora_inicio ?? null) ? substr($atividade->hora_inicio, 0, 5) : '--:--'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Hora Fim</div>
                        <div class="info-value"><?php echo ($atividade->hora_fim ?? null) ? substr($atividade->hora_fim, 0, 5) : '--:--'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Horas Trabalhadas</div>
                        <div class="info-value"><?php echo $atividade->horas_trabalhadas ?? 0; ?>h</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Visível ao Cliente</div>
                        <div class="info-value">
                            <?php echo ($atividade->visivel_cliente ?? 0) ? '<span style="color: #27ae60;"><i class="icon-eye-open"></i> Sim</span>' : '<span style="color: #e74c3c;"><i class="icon-eye-close"></i> Não</span>'; ?>
                        </div>
                    </div>
                </div>

                <?php if ($atividade->descricao ?? null): ?>
                <div style="margin-top: 20px;">
                    <div class="info-label">Descrição</div>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-top: 8px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($atividade->descricao)); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($atividade->impedimento ?? null): ?>
                <div style="margin-top: 20px; background: #fff5f5; border-left: 4px solid #e74c3c; padding: 15px; border-radius: 10px;">
                    <div style="color: #e74c3c; font-weight: 600; margin-bottom: 5px;">
                        <i class="icon-warning-sign"></i> Impedimento Registrado
                    </div>
                    <div style="color: #666;">
                        <strong>Tipo:</strong> <?php echo $atividade->tipo_impedimento ?? 'N/A'; ?><br>
                        <strong>Motivo:</strong> <?php echo nl2br(htmlspecialchars($atividade->motivo_impedimento ?? '')); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Progresso -->
                <div class="progress-section">
                    <div class="progress-header">
                        <span class="progress-label">Progresso da Atividade</span>
                        <span class="progress-value"><?php echo $atividade->percentual_concluido ?? 0; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Check-ins -->
            <?php if (!empty($checkins)): ?>
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-map-marker"></i> Registros de Check-in
                    </div>
                </div>
                <?php foreach ($checkins as $checkin): ?>
                <div class="checkin-card">
                    <div class="checkin-icon <?php echo $checkin->tipo; ?>">
                        <i class="icon-<?php echo $checkin->tipo == 'checkin' ? 'signin' : ($checkin->tipo == 'checkout' ? 'signout' : 'time'); ?>"></i>
                    </div>
                    <div class="checkin-content">
                        <div class="checkin-title">
                            <?php
                            $tipoCheckin = [
                                'checkin' => 'Check-in Inicial',
                                'checkout' => 'Check-out Final',
                                'pausa' => 'Pausa',
                                'retorno' => 'Retorno'
                            ];
                            echo $tipoCheckin[$checkin->tipo] ?? $checkin->tipo;
                            ?>
                        </div>
                        <div class="checkin-meta">
                            <i class="icon-time"></i> <?php echo date('d/m/Y H:i', strtotime($checkin->created_at)); ?>
                            <?php if ($checkin->endereco_detectado): ?>
                            | <i class="icon-map-marker"></i> <?php echo $checkin->endereco_detectado; ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($checkin->observacao): ?>
                        <div style="margin-top: 8px; font-size: 13px; color: #666;">
                            <?php echo htmlspecialchars($checkin->observacao); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Ações -->
            <div class="actions-bar">
                <a href="<?php echo site_url('obras/atividades/' . $obra->id); ?>" class="action-btn action-btn-secondary">
                    <i class="icon-arrow-left"></i> Voltar
                </a>
                <a href="<?php echo site_url('obras/editarAtividade/' . $atividade->id); ?>" class="action-btn action-btn-primary">
                    <i class="icon-edit"></i> Editar Atividade
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="atividade-sidebar">
            <!-- Obra Info -->
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-building"></i> Obra
                    </div>
                </div>
                <div style="text-align: center; padding: 15px;">
                    <div style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 10px;">
                        <?php echo $obra->nome; ?>
                    </div>
                    <div style="color: #888; font-size: 14px; margin-bottom: 15px;">
                        <?php echo $obra->cliente_nome ?? 'Cliente não definido'; ?>
                    </div>
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="action-btn action-btn-primary" style="display: inline-flex;">
                        <i class="icon-eye-open"></i> Ver Obra
                    </a>
                </div>
            </div>

            <!-- Histórico -->
            <?php if (!empty($historico)): ?>
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-history"></i> Histórico
                    </div>
                </div>
                <div class="timeline">
                    <?php foreach ($historico as $hist): ?>
                    <div class="timeline-item <?php echo $hist->tipo_alteracao; ?>">
                        <div class="timeline-header">
                            <span class="timeline-title">
                                <?php
                                $tipoAlt = [
                                    'inicio' => 'Início',
                                    'pausa' => 'Pausa',
                                    'retorno' => 'Retorno',
                                    'conclusao' => 'Conclusão',
                                    'impedimento' => 'Impedimento',
                                    'foto' => 'Foto Adicionada',
                                    'observacao' => 'Observação'
                                ];
                                echo $tipoAlt[$hist->tipo_alteracao] ?? $hist->tipo_alteracao;
                                ?>
                            </span>
                            <span class="timeline-time">
                                <?php echo date('d/m H:i', strtotime($hist->created_at)); ?>
                            </span>
                        </div>
                        <?php if ($hist->descricao): ?>
                        <div class="timeline-content">
                            <?php echo htmlspecialchars($hist->descricao); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
