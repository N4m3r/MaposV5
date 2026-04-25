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

/* Status Badge com cores diferentes */
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
.atividade-status-badge.agendada { background: linear-gradient(135deg, #95a5a6, #7f8c8d); }
.atividade-status-badge.iniciada { background: linear-gradient(135deg, #f39c12, #e67e22); }
.atividade-status-badge.pausada { background: linear-gradient(135deg, #e74c3c, #c0392b); }
.atividade-status-badge.concluida { background: linear-gradient(135deg, #27ae60, #2ecc71); }
.atividade-status-badge.cancelada { background: linear-gradient(135deg, #34495e, #2c3e50); }
.atividade-status-badge.reaberta { background: linear-gradient(135deg, #9b59b6, #8e44ad); }

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
    border-left: 4px solid #667eea;
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

/* Wizard Info Section - Destacado */
.wizard-info-section {
    background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
    border: 2px solid #11998e;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
}
.wizard-info-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px dashed #11998e;
}
.wizard-info-header i {
    font-size: 28px;
    color: #11998e;
}
.wizard-info-header h2 {
    margin: 0;
    font-size: 20px;
    color: #11998e;
}
.wizard-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}
.wizard-info-item {
    background: white;
    border-radius: 10px;
    padding: 15px;
    border-left: 4px solid;
}
.wizard-info-item.etapa { border-color: #9b59b6; }
.wizard-info-item.tipo { border-color: #3498db; }
.wizard-info-item.inicio { border-color: #27ae60; }
.wizard-info-item.fim { border-color: #e74c3c; }
.wizard-info-item.duracao { border-color: #667eea; grid-column: span 2; }
.wizard-info-item.visivel { border-color: #f39c12; }

.wizard-info-item .label {
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.wizard-info-item .value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}
.wizard-info-item.duracao .value {
    font-size: 24px;
    color: #667eea;
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
    background: linear-gradient(90deg, #11998e, #38ef7d);
    border-radius: 6px;
    transition: width 0.5s ease;
}

/* Fotos Section */
.fotos-section {
    margin-top: 20px;
}
.fotos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}
.foto-item {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.foto-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
}
.foto-item .tipo-badge {
    position: absolute;
    bottom: 10px;
    left: 10px;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    color: white;
}
.foto-item .tipo-badge.checkin { background: #11998e; }
.foto-item .tipo-badge.checkout { background: #e74c3c; }
.foto-item .tipo-badge.execucao { background: #667eea; }

/* Localização */
.localizacao-box {
    background: #e3f2fd;
    border-radius: 10px;
    padding: 15px;
    margin-top: 20px;
}
.localizacao-box .titulo {
    color: #1976d2;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Anotações */
.anotacoes-section {
    margin-top: 20px;
}
.anotacao-item {
    background: white;
    border-left: 4px solid #3498db;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.anotacao-item.problema { border-left-color: #f39c12; }
.anotacao-item.solucao { border-left-color: #27ae60; }
.anotacao-header {
    display: flex;
    justify-content: space-between;
    align-size: 12px;
    color: #888;
    margin-bottom: 8px;
}
.anotacao-content {
    font-size: 14px;
    color: #333;
    line-height: 1.5;
}

/* Status Form */
.status-form-container {
    margin-top: 15px;
    padding: 15px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    backdrop-filter: blur(10px);
}
.status-form-container select,
.status-form-container textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
    margin-bottom: 10px;
    font-size: 14px;
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
.action-btn-success {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .atividade-grid { grid-template-columns: 1fr; }
    .info-grid { grid-template-columns: 1fr; }
    .wizard-info-grid { grid-template-columns: 1fr; }
    .wizard-info-item.duracao { grid-column: span 1; }
}
</style>

<div class="atividade-view">

    <!-- Verificar se atividade existe -->
    <?php if (empty($atividade)): ?>
    <div class="atividade-card" style="text-align: center; padding: 60px 20px;">
        <i class="icon-warning-sign" style="font-size: 48px; color: #e74c3c;"></i>
        <h2 style="margin: 20px 0; color: #333;">Atividade não encontrada</h2>
        <p style="color: #666;">A atividade solicitada não existe ou foi removida.</p>
        <a href="<?php echo site_url('obras'); ?>" class="action-btn action-btn-primary" style="display: inline-flex; margin-top: 20px;">
            <i class="icon-arrow-left"></i> Voltar para Obras
        </a>
    </div>
    <?php return; endif; ?>

    <!-- Header -->
    <div class="atividade-header">
        <div class="atividade-header-content">
            <div class="atividade-header-left">
                <div class="atividade-breadcrumb">
                    <a href="<?php echo site_url('obras'); ?>">Obras</a> &raquo;
                    <a href="<?php echo site_url('obras/visualizar/' . ($obra->id ?? 0)); ?>"><?php echo htmlspecialchars($obra->nome ?? 'Obra'); ?></a> &raquo;
                    <span>Atividade #<?php echo $atividade->id ?? ''; ?></span>
                </div>
                <h1><i class="icon-tasks"></i> <?php echo htmlspecialchars($atividade->titulo ?? 'Atividade sem nome'); ?></h1>
                <div class="atividade-subtitle">
                    <i class="icon-user"></i> Técnico: <?php echo htmlspecialchars($atividade_real->nome_tecnico ?? $atividade->tecnico_nome ?? 'Não atribuído'); ?>
                    | <i class="icon-calendar"></i> Data: <?php echo (!empty($atividade_real->hora_inicio)) ? date('d/m/Y', strtotime($atividade_real->hora_inicio)) : date('d/m/Y', strtotime($atividade->data_atividade ?? 'now')); ?>
                </div>
            </div>
            <div class="atividade-status-section">
                <?php $status_atual = $atividade->status ?? 'agendada'; ?>
                <span class="atividade-status-badge <?php echo $status_atual; ?>" id="statusBadge">
                    <i class="icon-time"></i>
                    <span id="statusText">
                        <?php
                        $statusLabels = [
                            'agendada' => 'Agendada',
                            'iniciada' => 'Em Execução',
                            'pausada' => 'Pausada',
                            'concluida' => 'Concluída',
                            'cancelada' => 'Cancelada',
                            'reaberta' => 'Reaberta (Reatendimento)'
                        ];
                        echo $statusLabels[$status_atual] ?? ucfirst($status_atual);
                        ?>
                    </span>
                </span>

                <!-- Botão para alterar status -->
                <button type="button" class="action-btn" style="margin-top: 10px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);" onclick="toggleStatusForm()">
                    <i class="icon-edit"></i> Alterar Status
                </button>

                <!-- Formulário de alteração de status -->
                <div id="statusForm" class="status-form-container" style="display: none;">
                    <form action="<?php echo site_url('obras/atualizarStatusAtividade/' . ($atividade->id ?? 0)); ?>" method="POST">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                        <select name="novo_status" required>
                            <option value="agendada" <?php echo $status_atual === 'agendada' ? 'selected' : ''; ?>>📅 Agendada</option>
                            <option value="iniciada" <?php echo $status_atual === 'iniciada' ? 'selected' : ''; ?>>🟡 Em Execução</option>
                            <option value="pausada" <?php echo $status_atual === 'pausada' ? 'selected' : ''; ?>>⏸️ Pausada</option>
                            <option value="concluida" <?php echo $status_atual === 'concluida' ? 'selected' : ''; ?>>✅ Concluída</option>
                            <option value="cancelada" <?php echo $status_atual === 'cancelada' ? 'selected' : ''; ?>>❌ Cancelada</option>
                            <option value="reaberta" <?php echo $status_atual === 'reaberta' ? 'selected' : ''; ?> style="color: #9b59b6; font-weight: bold;">🔄 Reaberta (Reatendimento)</option>
                        </select>

                        <textarea name="observacao_status" rows="2" placeholder="Motivo da alteração (opcional)"></textarea>

                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="action-btn action-btn-success" style="flex: 1; padding: 10px;">
                                <i class="icon-save"></i> Salvar
                            </button>
                            <button type="button" class="action-btn" style="flex: 1; background: rgba(255,255,255,0.2); color: white; padding: 10px;" onclick="toggleStatusForm()">
                                <i class="icon-remove"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="atividade-grid">
        <!-- Main Content -->
        <div class="atividade-main">

            <!-- Informações do Wizard de Atendimento -->
            <?php if (!empty($atividade_real)): ?>
            <div class="wizard-info-section">
                <div class="wizard-info-header">
                    <i class="icon-time"></i>
                    <h2>Registro do Wizard de Atendimento</h2>
                </div>

                <div class="wizard-info-grid">
                    <!-- Etapa -->
                    <div class="wizard-info-item etapa">
                        <div class="label"><i class="icon-hard-hat"></i> Etapa</div>
                        <div class="value">
                            <?php
                            $etapa_nome = null;

                            // Prioridade 1: etapa_nome da atividade real
                            if (!empty($atividade_real->etapa_nome)) {
                                $etapa_nome = $atividade_real->etapa_nome;
                            }
                            // Prioridade 2: etapa_nome da atividade planejada
                            elseif (!empty($atividade->etapa_nome)) {
                                $etapa_nome = $atividade->etapa_nome;
                            }
                            // Prioridade 3: buscar pelo etapa_id da atividade real
                            elseif (!empty($atividade_real->etapa_id)) {
                                $etapa_query = $this->db->get_where('obra_etapas', ['id' => $atividade_real->etapa_id]);
                                $etapa_row = $etapa_query->row();
                                if ($etapa_row) {
                                    $etapa_nome = $etapa_row->nome;
                                }
                            }
                            // Prioridade 4: buscar pelo etapa_id da atividade planejada
                            elseif (!empty($atividade->etapa_id)) {
                                $etapa_query = $this->db->get_where('obra_etapas', ['id' => $atividade->etapa_id]);
                                $etapa_row = $etapa_query->row();
                                if ($etapa_row) {
                                    $etapa_nome = $etapa_row->nome;
                                }
                            }

                            echo $etapa_nome ? htmlspecialchars($etapa_nome) : 'N/A';
                            ?>
                        </div>
                    </div>

                    <!-- Tipo -->
                    <div class="wizard-info-item tipo">
                        <div class="label"><i class="icon-tasks"></i> Tipo</div>
                        <div class="value">
                            <?php
                            // Verificar impedimento em ambas as atividades (real e planejada)
                            $impedimento = ($atividade_real->impedimento ?? $atividade->impedimento ?? 0);
                            $tipo_execucao = $impedimento ? 'impedimento' : 'trabalho';
                            $tipo_icon = $tipo_execucao === 'impedimento' ? 'warning-sign' : 'wrench';
                            $tipo_cor = $tipo_execucao === 'impedimento' ? '#e67e22' : '#27ae60';
                            $tipo_label = $tipo_execucao === 'impedimento' ? 'Impedimento' : 'Trabalho';
                            ?>
                            <span style="color: <?php echo $tipo_cor; ?>; font-weight: 600;">
                                <i class="icon-<?php echo $tipo_icon; ?>"></i> <?php echo $tipo_label; ?>
                            </span>
                            <?php if (!empty($atividade_real->tipo_nome)): ?>
                            <br><small style="color: #666;"><?php echo htmlspecialchars($atividade_real->tipo_nome); ?></small>
                            <?php endif; ?>

                            <?php
                            $motivo_impedimento = $atividade_real->motivo_impedimento ?? $atividade->motivo_impedimento ?? null;
                            $tipo_impedimento = $atividade_real->tipo_impedimento ?? $atividade->tipo_impedimento ?? null;
                            if ($impedimento && $motivo_impedimento): ?>
                            <div style="margin-top: 8px; padding: 10px 12px; background: #fff3e0; border-left: 4px solid #e67e22; border-radius: 6px;">
                                <div style="font-size: 11px; color: #d35400; font-weight: 600; margin-bottom: 4px;">
                                    <i class="icon-warning-sign"></i> Motivo do Impedimento
                                    <?php if ($tipo_impedimento): ?>
                                    (<?php echo ucfirst(htmlspecialchars($tipo_impedimento)); ?>)
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 12px; color: #333; line-height: 1.5;">
                                    <?php echo nl2br(htmlspecialchars($motivo_impedimento)); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Hora Início -->
                    <div class="wizard-info-item inicio">
                        <div class="label"><i class="icon-signin"></i> Hora Início</div>
                        <div class="value" style="color: #27ae60;">
                            <?php echo !empty($atividade_real->hora_inicio) ? date('d/m/Y H:i', strtotime($atividade_real->hora_inicio)) : '--:--'; ?>
                        </div>
                    </div>

                    <!-- Hora Fim -->
                    <div class="wizard-info-item fim">
                        <div class="label"><i class="icon-signout"></i> Hora Fim</div>
                        <div class="value" style="color: #e74c3c;">
                            <?php echo !empty($atividade_real->hora_fim) ? date('d/m/Y H:i', strtotime($atividade_real->hora_fim)) : '--:--'; ?>
                        </div>
                    </div>

                    <!-- Horas Trabalhadas -->
                    <div class="wizard-info-item duracao">
                        <div class="label"><i class="icon-time"></i> Horas Trabalhadas</div>
                        <div class="value">
                            <?php
                            if (!empty($atividade_real->duracao_minutos)) {
                                $horas = floor($atividade_real->duracao_minutos / 60);
                                $minutos = $atividade_real->duracao_minutos % 60;
                                echo sprintf('%02d:%02d', $horas, $minutos);
                                echo ' <small style="font-size: 14px; color: #888;">(' . $atividade_real->duracao_minutos . ' min)</small>';
                            } elseif (!empty($atividade_real->hora_inicio) && !empty($atividade_real->hora_fim)) {
                                $inicio = strtotime($atividade_real->hora_inicio);
                                $fim = strtotime($atividade_real->hora_fim);
                                $duracao = $fim - $inicio;
                                $horas = floor($duracao / 3600);
                                $minutos = floor(($duracao % 3600) / 60);
                                echo sprintf('%02d:%02d', $horas, $minutos);
                            } else {
                                echo '--:--';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Visível ao Cliente -->
                    <div class="wizard-info-item visivel">
                        <div class="label"><i class="icon-eye-open"></i> Visível ao Cliente</div>
                        <div class="value">
                            <?php if ($atividade->visivel_cliente ?? 0): ?>
                                <span style="color: #27ae60;"><i class="icon-check"></i> Sim</span>
                            <?php else: ?>
                                <span style="color: #e74c3c;"><i class="icon-remove"></i> Não</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Progresso -->
                <div class="progress-section" style="margin-top: 20px; background: white;">
                    <div class="progress-header">
                        <span class="progress-label">Progresso da Atividade</span>
                        <span class="progress-value"><?php echo $atividade->percentual_concluido ?? 0; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $atividade->percentual_concluido ?? 0; ?>%"></div>
                    </div>
                </div>

                <!-- Localização -->
                <?php
                // Buscar latitude/longitude da atividade real ou planejada
                $latitude = $atividade_real->latitude ?? $atividade->latitude ?? null;
                $longitude = $atividade_real->longitude ?? $atividade->longitude ?? null;
                if (!empty($latitude) && !empty($longitude)): ?>
                <div class="localizacao-box">
                    <div class="titulo">
                        <i class="icon-map-marker"></i> Localização Registrada
                    </div>
                    <div style="font-size: 13px; color: #555;">
                        <strong>Latitude:</strong> <?php echo $latitude; ?> |
                        <strong>Longitude:</strong> <?php echo $longitude; ?>
                        <a href="https://www.google.com/maps?q=<?php echo $latitude; ?>,<?php echo $longitude; ?>"
                           target="_blank"
                           style="margin-left: 10px; color: #3498db; text-decoration: none;">
                            <i class="icon-external-link"></i> Ver no Maps
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Fotos -->
            <?php if (!empty($fotos_atividade) && count($fotos_atividade) > 0): ?>
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-camera"></i> Fotos Registradas (<?php echo count($fotos_atividade); ?>)
                    </div>
                </div>
                <div class="fotos-section">
                    <div class="fotos-grid">
                        <?php foreach ($fotos_atividade as $foto): ?>
                        <div class="foto-item">
                            <?php
                            $url_foto = '';
                            if (!empty($foto->caminho_arquivo)) {
                                $url_foto = base_url('assets/atividades/fotos/' . $foto->caminho_arquivo);
                            } elseif (!empty($foto->foto_base64)) {
                                $url_foto = $foto->foto_base64;
                            }
                            ?>
                            <?php if ($url_foto): ?>
                            <a href="<?php echo $url_foto; ?>" target="_blank">
                                <img src="<?php echo $url_foto; ?>" alt="Foto da atividade">
                            </a>
                            <?php endif; ?>

                            <?php
                            $tipo_foto = $foto->tipo_foto ?? 'execucao';
                            $tipo_class = '';
                            $tipo_text = '';
                            switch($tipo_foto) {
                                case 'checkin':
                                    $tipo_class = 'checkin';
                                    $tipo_text = 'Check-in';
                                    break;
                                case 'checkout':
                                    $tipo_class = 'checkout';
                                    $tipo_text = 'Check-out';
                                    break;
                                default:
                                    $tipo_class = 'execucao';
                                    $tipo_text = 'Execução';
                            }
                            ?>
                            <span class="tipo-badge <?php echo $tipo_class; ?>"><?php echo $tipo_text; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Histórico de Reatendimentos -->
            <?php if (!empty($historico_execucoes) && count($historico_execucoes) > 0): ?>
            <div class="atividade-card" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-color: #9b59b6;">
                <div class="atividade-card-header">
                    <div class="atividade-card-title" style="color: #9b59b6;">
                        <i class="icon-refresh"></i> Histórico de Execuções e Reatendimentos
                        <span style="background: #9b59b6; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-left: 10px;">
                            <?php echo count($historico_execucoes); ?> registro(s)
                        </span>
                    </div>
                </div>

                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($historico_execucoes as $index => $exec): ?>
                    <div style="padding: 15px; margin-bottom: 15px; background: white; border-radius: 12px; border-left: 4px solid <?php echo $exec->reatendimento ? '#9b59b6' : '#27ae60'; ?>; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: <?php echo $exec->reatendimento ? '#9b59b6' : '#27ae60'; ?>; color: white; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;">
                                    <i class="icon-<?php echo $exec->reatendimento ? 'refresh' : 'ok'; ?>"></i>
                                    <?php echo $exec->reatendimento ? 'Reatendimento #' . ($index + 1) : 'Execução Original'; ?>
                                </span>
                                <span style="font-size: 13px; color: #666;">
                                    <i class="icon-user"></i> <?php echo htmlspecialchars($exec->nome_tecnico ?? 'Técnico não definido'); ?>
                                </span>
                            </div>
                            <div style="font-size: 12px; color: #888;">
                                ID: #<?php echo $exec->idAtividade; ?>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 10px;">
                            <div>
                                <div style="font-size: 11px; color: #888; text-transform: uppercase;">Início</div>
                                <div style="font-size: 14px; font-weight: 600; color: #333;">
                                    <?php echo !empty($exec->hora_inicio) ? date('d/m/Y H:i', strtotime($exec->hora_inicio)) : '--:--'; ?>
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 11px; color: #888; text-transform: uppercase;">Fim</div>
                                <div style="font-size: 14px; font-weight: 600; color: #333;">
                                    <?php echo !empty($exec->hora_fim) ? date('d/m/Y H:i', strtotime($exec->hora_fim)) : '--:--'; ?>
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 11px; color: #888; text-transform: uppercase;">Status</div>
                                <div style="font-size: 14px; font-weight: 600;">
                                    <?php
                                    $execStatusClass = [
                                        'reaberta' => '#9b59b6',
                                        'em_andamento' => '#f39c12',
                                        'finalizada' => '#27ae60',
                                        'pausada' => '#e74c3c'
                                    ][$exec->status] ?? '#95a5a6';
                                    ?>
                                    <span style="color: <?php echo $execStatusClass; ?>;">
                                        <i class="icon-time"></i>
                                        <?php
                                        echo [
                                            'reaberta' => 'Reaberta',
                                            'em_andamento' => 'Em Andamento',
                                            'finalizada' => 'Finalizada',
                                            'pausada' => 'Pausada'
                                        ][$exec->status] ?? ucfirst($exec->status);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($exec->duracao_minutos)): ?>
                        <div style="background: #f8f9fa; padding: 10px 15px; border-radius: 8px; margin-top: 10px;">
                            <span style="font-size: 12px; color: #666;">
                                <i class="icon-time" style="color: #667eea;"></i>
                                Duração:
                                <strong>
                                    <?php
                                    $horas = floor($exec->duracao_minutos / 60);
                                    $minutos = $exec->duracao_minutos % 60;
                                    echo sprintf('%02d:%02d', $horas, $minutos);
                                    ?>
                                </strong>
                                (<?php echo $exec->duracao_minutos; ?> minutos)
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($exec->motivo_reabertura)): ?>
                        <div style="background: #fff3e0; padding: 10px 15px; border-radius: 8px; margin-top: 10px; border-left: 3px solid #ff9800;">
                            <span style="font-size: 12px; color: #e65100;">
                                <i class="icon-info-sign"></i>
                                <strong>Motivo da Reabertura:</strong> <?php echo htmlspecialchars($exec->motivo_reabertura); ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if ($exec->status === 'reaberta'): ?>
                        <div style="margin-top: 10px; text-align: right;">
                            <a href="<?php echo site_url('obras/iniciarReatendimento/' . $exec->idAtividade); ?>" class="action-btn action-btn-success" style="display: inline-flex; padding: 8px 16px; font-size: 13px;">
                                <i class="icon-play"></i> Iniciar Reatendimento
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Anotações e Registros -->
            <?php
            // Buscar anotações de ambas as fontes
            $observacoes = null;
            $problemas = null;
            $solucao = null;

            // Verificar se atividade_real existe e tem as propriedades
            if (!empty($atividade_real)) {
                if (is_object($atividade_real)) {
                    $observacoes = property_exists($atividade_real, 'observacoes') ? $atividade_real->observacoes : null;
                    $problemas = property_exists($atividade_real, 'problemas_encontrados') ? $atividade_real->problemas_encontrados : null;
                    $solucao = property_exists($atividade_real, 'solucao_aplicada') ? $atividade_real->solucao_aplicada : null;
                }
            }

            // Se não encontrou em atividade_real, buscar em atividade
            if (empty($observacoes) && !empty($atividade) && is_object($atividade)) {
                $observacoes = property_exists($atividade, 'observacoes') ? $atividade->observacoes : null;
            }
            if (empty($problemas) && !empty($atividade) && is_object($atividade)) {
                $problemas = property_exists($atividade, 'problemas_encontrados') ? $atividade->problemas_encontrados : null;
            }
            if (empty($solucao) && !empty($atividade) && is_object($atividade)) {
                $solucao = property_exists($atividade, 'solucao_aplicada') ? $atividade->solucao_aplicada : null;
            }

            if (!empty($observacoes) || !empty($problemas) || !empty($solucao)): ?>
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-edit"></i> Anotações e Registros
                    </div>
                </div>
                <div class="anotacoes-section">
                    <?php if (!empty($observacoes)): ?>
                    <div class="anotacao-item">
                        <div class="anotacao-header">
                            <span><i class="icon-comment"></i> Observações Gerais</span>
                        </div>
                        <div class="anotacao-content">
                            <?php echo nl2br(htmlspecialchars($observacoes)); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($problemas)): ?>
                    <div class="anotacao-item problema">
                        <div class="anotacao-header">
                            <span><i class="icon-warning-sign"></i> Problemas Encontrados</span>
                        </div>
                        <div class="anotacao-content">
                            <?php echo nl2br(htmlspecialchars($problemas)); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($solucao)): ?>
                    <div class="anotacao-item solucao">
                        <div class="anotacao-header">
                            <span><i class="icon-check"></i> Solução Aplicada</span>
                        </div>
                        <div class="anotacao-content">
                            <?php echo nl2br(htmlspecialchars($solucao)); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <!-- Mensagem quando não há registro do wizard -->
            <div class="atividade-card" style="text-align: center; padding: 40px;">
                <i class="icon-info-sign" style="font-size: 48px; color: #95a5a6;"></i>
                <h3 style="margin: 15px 0; color: #666;">Sem Registro do Wizard</h3>
                <p style="color: #888;">Esta atividade ainda não foi executada através do wizard de atendimento.</p>
                <?php if ($atividade->status === 'agendada'): ?>
                <a href="<?php echo site_url('tecnicos/executar_obra/' . ($obra->id ?? 0)); ?>" class="action-btn action-btn-success" style="display: inline-flex; margin-top: 20px;">
                    <i class="icon-play"></i> Iniciar Atendimento
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Ações -->
            <div class="actions-bar">
                <a href="<?php echo site_url('obras/atividades/' . ($obra->id ?? 0)); ?>" class="action-btn action-btn-secondary">
                    <i class="icon-arrow-left"></i> Voltar
                </a>
                <a href="<?php echo site_url('obras/editarAtividade/' . ($atividade->id ?? 0)); ?>" class="action-btn action-btn-primary">
                    <i class="icon-edit"></i> Editar Atividade
                </a>
                <a href="<?php echo site_url('obras/imprimirAtividade/' . ($atividade->id ?? 0)); ?>" target="_blank" class="action-btn" style="background: linear-gradient(135deg, #e67e22, #d35400); color: white;">
                    <i class="icon-print"></i> Imprimir Relatório
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="atividade-sidebar">
            <!-- Info da Obra -->
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-building"></i> Obra
                    </div>
                </div>
                <div style="text-align: center; padding: 15px;">
                    <div style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 10px;">
                        <?php echo htmlspecialchars($obra->nome ?? 'Obra não definida'); ?>
                    </div>
                    <div style="color: #888; font-size: 14px; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($obra->cliente_nome ?? 'Cliente não definido'); ?>
                    </div>
                    <a href="<?php echo site_url('obras/visualizar/' . ($obra->id ?? 0)); ?>" class="action-btn action-btn-primary" style="display: inline-flex;">
                        <i class="icon-eye-open"></i> Ver Obra
                    </a>
                </div>
            </div>

            <!-- Histórico de Alterações -->
            <?php if (!empty($historico)): ?>
            <div class="atividade-card">
                <div class="atividade-card-header">
                    <div class="atividade-card-title">
                        <i class="icon-history"></i> Histórico
                    </div>
                </div>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($historico as $hist): ?>
                    <div style="padding: 10px; border-bottom: 1px solid #f0f0f0; font-size: 13px;">
                        <div style="color: #888; font-size: 11px; margin-bottom: 3px;">
                            <?php echo date('d/m/Y H:i', strtotime($hist->created_at)); ?>
                        </div>
                        <div style="color: #333;">
                            <?php echo htmlspecialchars($hist->descricao ?? ''); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleStatusForm() {
    var form = document.getElementById('statusForm');
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
