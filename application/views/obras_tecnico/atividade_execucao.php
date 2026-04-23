<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.atividade-exec-container { padding: 20px; max-width: 900px; margin: 0 auto; }

/* Header */
.atividade-exec-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 25px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.atividade-exec-breadcrumb {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 10px;
}
.atividade-exec-breadcrumb a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
}
.atividade-exec-breadcrumb a:hover { opacity: 1; text-decoration: underline; }
.atividade-exec-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}
.atividade-exec-subtitle {
    opacity: 0.95;
    font-size: 15px;
}

/* Status Banner */
.status-banner {
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    text-align: center;
    font-size: 18px;
    font-weight: 600;
}
.status-banner.agendada { background: #ecf0f1; color: #7f8c8d; }
.status-banner.iniciada {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
    animation: pulse 2s infinite;
}
.status-banner.pausada { background: #f39c12; color: white; }
.status-banner.concluida { background: #27ae60; color: white; }

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(17, 153, 142, 0.4); }
    50% { box-shadow: 0 0 0 15px rgba(17, 153, 142, 0); }
}

/* Timer */
.timer-display {
    text-align: center;
    padding: 30px;
    background: white;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.timer-label {
    font-size: 14px;
    color: #888;
    margin-bottom: 10px;
}
.timer-value {
    font-size: 64px;
    font-weight: 700;
    color: #667eea;
    font-family: 'Courier New', monospace;
    line-height: 1;
}
.timer-value.running { color: #11998e; }
.timer-value.paused { color: #f39c12; }

/* Cards */
.exec-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}
.exec-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.exec-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.exec-card-title i { color: #667eea; font-size: 22px; }

/* Info Grid */
.exec-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.exec-info-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
}
.exec-info-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.exec-info-value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

/* Action Buttons */
.exec-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 20px;
}
.exec-btn {
    padding: 20px;
    border-radius: 15px;
    border: none;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    transition: all 0.3s;
    color: white;
}
.exec-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.exec-btn i { font-size: 32px; }
.exec-btn-primary {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    grid-column: span 2;
}
.exec-btn-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}
.exec-btn-info {
    background: linear-gradient(135deg, #3498db, #2980b9);
}
.exec-btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}
.exec-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Forms */
.exec-form-group {
    margin-bottom: 20px;
}
.exec-form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}
.exec-form-textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    font-size: 15px;
    resize: vertical;
    min-height: 120px;
    box-sizing: border-box;
}
.exec-form-textarea:focus {
    border-color: #667eea;
    outline: none;
}
.exec-form-select {
    width: 100%;
    padding: 15px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    font-size: 15px;
    background: white;
}

/* Camera Section */
.camera-section {
    text-align: center;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 15px;
    border: 2px dashed #ddd;
}
.camera-btn {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 30px 50px;
    background: white;
    border: 2px solid #667eea;
    border-radius: 15px;
    color: #667eea;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}
.camera-btn:hover {
    background: #667eea;
    color: white;
    transform: translateY(-3px);
}
.camera-btn i { font-size: 48px; }

/* Preview Image */
.photo-preview {
    max-width: 100%;
    border-radius: 15px;
    margin-top: 15px;
}

/* Location Status */
.location-status {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 15px;
}
.location-status i {
    font-size: 24px;
    color: #27ae60;
}
.location-status.verifying i { color: #f39c12; animation: spin 1s linear infinite; }
.location-status.error i { color: #e74c3c; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 768px) {
    .exec-actions { grid-template-columns: 1fr; }
    .exec-btn-primary { grid-column: span 1; }
    .exec-info-grid { grid-template-columns: 1fr; }
    .timer-value { font-size: 48px; }
}
</style>

<div class="atividade-exec-container">
    <!-- Header -->
    <div class="atividade-exec-header">
        <div class="atividade-exec-breadcrumb">
            <a href="<?php echo site_url('obras_tecnico/minhasObras'); ?>">Minhas Obras</a> &raquo;
            <a href="<?php echo site_url('obras_tecnico/obra/' . $obra->id); ?>"><?php echo $obra->nome; ?></a> &raquo;
            <span>Atividade</span>
        </div>
        <h1 class="atividade-exec-title">
            <i class="icon-tasks"></i> <?php echo htmlspecialchars($atividade->titulo); ?>
        </h1>
        <div class="atividade-exec-subtitle">
            <i class="icon-building"></i> <?php echo $obra->nome; ?>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="status-banner <?php echo $atividade->status; ?>">
        <i class="icon-time"></i>
        <?php
        $statusMessages = [
            'agendada' => 'Aguardando Início',
            'iniciada' => 'Atividade em Execução',
            'pausada' => 'Atividade Pausada',
            'concluida' => 'Atividade Concluída'
        ];
        echo $statusMessages[$atividade->status] ?? $atividade->status;
        ?>
    </div>

    <!-- Timer (apenas se iniciada) -->
    <?php if ($atividade->status == 'iniciada' || $atividade->status == 'pausada'): ?>
    <div class="timer-display">
        <div class="timer-label">Tempo Trabalhado</div>
        <div class="timer-value <?php echo $atividade->status; ?>" id="timer">
            <?php echo gmdate('H:i:s', $tempo_trabalhado * 3600); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Info da Atividade -->
    <div class="exec-card">
        <div class="exec-card-header">
            <div class="exec-card-title">
                <i class="icon-info-sign"></i> Informações
            </div>
        </div>

        <div class="exec-info-grid">
            <div class="exec-info-item">
                <div class="exec-info-label">Etapa</div>
                <div class="exec-info-value"><?php echo $atividade->etapa_nome ?? 'N/A'; ?></div>
            </div>

            <div class="exec-info-item">
                <div class="exec-info-label">Data</div>
                <div class="exec-info-value"><?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?></div>
            </div>

            <div class="exec-info-item">
                <div class="exec-info-label">Início</div>
                <div class="exec-info-value"><?php echo $atividade->hora_inicio ? substr($atividade->hora_inicio, 0, 5) : '--:--'; ?></div>
            </div>

            <div class="exec-info-item">
                <div class="exec-info-label">Progresso</div>
                <div class="exec-info-value"><?php echo $atividade->percentual_concluido ?? 0; ?>%</div>
            </div>
        </div>

        <?php if ($atividade->descricao): ?>
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
            <div class="exec-info-label">Descrição</div>
            <div style="margin-top: 8px; color: #666; line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($atividade->descricao)); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sistema de Registro de Tempo (Wizard de Atendimento) -->
    <?php if (file_exists(APPPATH . 'models/Atividades_model.php')): ?>
    <div class="exec-card" style="border-left: 4px solid #11998e;">
        <div class="exec-card-header">
            <div class="exec-card-title">
                <i class="bx bx-timer" style="color: #11998e;"></i> Registro de Execução (Wizard de Atendimento)
            </div>
        </div>

        <!-- Wizard em Andamento -->
        <?php if ($wizard_em_andamento): ?>
        <div style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 2px solid #11998e;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #11998e, #38ef7d); display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; animation: pulse 2s infinite;">
                    <i class="bx bx-play"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #11998e; font-size: 18px;">Execução em Andamento no Wizard</div>
                    <div style="color: #666; font-size: 14px; margin-top: 4px;">
                        <i class="bx bx-time"></i> Iniciado às <?php echo date('H:i', strtotime($wizard_em_andamento->hora_inicio)); ?>
                        <?php if ($wizard_em_andamento->etapa_nome): ?>
                            <br><i class="bx bx-layer"></i> <?php echo $wizard_em_andamento->etapa_nome; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
                <div style="font-size: 13px; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Tempo Decorrido</div>
                <div id="timerWizard" style="font-size: 48px; font-weight: 700; color: #11998e; font-family: 'Courier New', monospace;">
                    00:00:00
                </div>
                <div style="font-size: 12px; color: #11998e; margin-top: 8px;">
                    <i class="bx bx-info-circle"></i> Clique no botão abaixo para continuar e finalizar no wizard
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                <a href="<?php echo site_url('atividades/wizard_obra/' . $obra->id . '/' . $wizard_em_andamento->etapa_id . '?obra_atividade_id=' . $atividade->id); ?>"
                   class="exec-btn exec-btn-primary"
                   style="text-align: center; text-decoration: none;">
                    <i class="bx bx-refresh"></i> CONTINUAR NO WIZARD DE ATENDIMENTO
                </a>
            </div>
        </div>

        <script>
            (function() {
                // Converte data do MySQL (Y-m-d H:i:s) para Date local
                function converterDataHora(dataHoraString) {
                    if (!dataHoraString) return null;
                    var partes = dataHoraString.split(' ');
                    if (partes.length !== 2) return new Date(dataHoraString);
                    var dataPartes = partes[0].split('-');
                    var horaPartes = partes[1].split(':');
                    if (dataPartes.length !== 3 || horaPartes.length < 2) return new Date(dataHoraString);
                    var ano = parseInt(dataPartes[0], 10);
                    var mes = parseInt(dataPartes[1], 10) - 1;
                    var dia = parseInt(dataPartes[2], 10);
                    var hora = parseInt(horaPartes[0], 10);
                    var minuto = parseInt(horaPartes[1], 10);
                    var segundo = parseInt(horaPartes[2] || '0', 10);
                    return new Date(ano, mes, dia, hora, minuto, segundo);
                }

                const horaInicio = converterDataHora('<?php echo $wizard_em_andamento->hora_inicio; ?>').getTime();
                const timerEl = document.getElementById('timerWizard');

                function atualizarTimer() {
                    const agora = new Date().getTime();
                    var diff = agora - horaInicio;
                    if (diff < 0) diff = 0; // Evita tempo negativo

                    const hrs = Math.floor(diff / 3600000);
                    const mins = Math.floor((diff % 3600000) / 60000);
                    const secs = Math.floor((diff % 60000) / 1000);

                    timerEl.textContent = String(hrs).padStart(2, '0') + ':' +
                                           String(mins).padStart(2, '0') + ':' +
                                           String(secs).padStart(2, '0');
                }

                atualizarTimer();
                setInterval(atualizarTimer, 1000);
            })();
        </script>

        <?php else: ?>
        <!-- Iniciar Novo Registro no Wizard -->
        <div style="text-align: center; padding: 30px 20px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 36px;">
                <i class="bx bx-play"></i>
            </div>

            <div style="font-weight: 700; color: #333; font-size: 18px; margin-bottom: 10px;">Iniciar Execução da Atividade</div>
            <div style="color: #666; font-size: 14px; margin-bottom: 25px; line-height: 1.6;">
                O sistema abrirá o <strong>Wizard de Atendimento</strong> para registro completo:
                <br>Check-in &#8594; Registro de Atividades &#8594; Check-out com Relatório
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px;">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px;">
                    <div style="font-size: 24px; color: #11998e; margin-bottom: 5px;"><i class="bx bx-check-circle"></i></div>
                    <div style="font-size: 12px; color: #666;">Check-in com Hora Início</div>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px;">
                    <div style="font-size: 24px; color: #3498db; margin-bottom: 5px;"><i class="bx bx-task"></i></div>
                    <div style="font-size: 12px; color: #666;">Registro de Atividades</div>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px;">
                    <div style="font-size: 24px; color: #27ae60; margin-bottom: 5px;"><i class="bx bx-check-double"></i></div>
                    <div style="font-size: 12px; color: #666;">Check-out com Relatório</div>
                </div>
            </div>

            <div style="background: #e8f5e9; border: 1px solid #4caf50; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
                <div style="font-size: 13px; color: #2e7d32;">
                    <i class="bx bx-info-circle"></i> <strong>Etapa vinculada:</strong>
                    <?php echo $atividade->etapa_nome ?? 'N/A'; ?>
                    <?php if ($atividade->numero_etapa): ?>(#<?php echo $atividade->numero_etapa; ?>)<?php endif; ?>
                </div>
            </div>

            <a href="<?php echo site_url('atividades/wizard_obra/' . $obra->id . '/' . $atividade->etapa_id . '?obra_atividade_id=' . $atividade->id); ?>"
               class="exec-btn exec-btn-primary"
               style="text-align: center; text-decoration: none; width: 100%;">
                <i class="bx bx-play-circle"></i> INICIAR EXECUÇÃO NO WIZARD
            </a>
        </div>
        <?php endif; ?>

        <!-- Histórico de Execuções no Wizard -->
        <?php if (!empty($registros_execucao)): ?>
        <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #f0f0f0;">
            <div style="font-weight: 700; color: #333; margin-bottom: 15px; font-size: 16px;">
                <i class="bx bx-history"></i> Histórico de Execuções Realizadas
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($registros_execucao as $reg): ?>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid <?php echo $reg->status == 'concluida' ? '#27ae60' : ($reg->status == 'pausada' ? '#f39c12' : '#3498db'); ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 600; color: #333;"><?php echo $reg->tipo_atividade; ?></div>
                            <div style="font-size: 12px; color: #888; margin-top: 3px;">
                                <i class="bx bx-calendar"></i> <?php echo date('d/m/Y', strtotime($reg->hora_inicio)); ?>
                                <?php if ($reg->etapa_nome): ?> | <i class="bx bx-layer"></i> <?php echo $reg->etapa_nome; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 20px; font-weight: 700; color: #667eea; font-family: monospace;">
                                <?php
                                if ($reg->hora_fim) {
                                    $inicio = strtotime($reg->hora_inicio);
                                    $fim = strtotime($reg->hora_fim);
                                    $duracao = $fim - $inicio;
                                    echo gmdate('H:i:s', $duracao);
                                } else {
                                    echo 'Em andamento';
                                }
                                ?>
                            </div>
                            <div style="font-size: 12px; color: #888;">
                                <?php echo substr($reg->hora_inicio, 0, 5); ?> - <?php echo $reg->hora_fim ? substr($reg->hora_fim, 0, 5) : '--:--'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Ações do Sistema Antigo -->
    <?php if ($atividade->status == 'agendada'): ?>
    <!-- Iniciar Atividade -->
    <form action="<?php echo site_url('obras_tecnico/iniciarAtividade'); ?>" method="post" id="formIniciar">
        <input type="hidden" name="atividade_id" value="<?php echo $atividade->id; ?>">
        <input type="hidden" name="latitude" id="latIniciar">
        <input type="hidden" name="longitude" id="lngIniciar">
        <input type="hidden" name="foto_url" id="fotoIniciar">

        <div class="exec-card">
            <div class="exec-card-header">
                <div class="exec-card-title">
                    <i class="icon-camera"></i> Foto de Check-in
                </div>
            </div>

            <div class="camera-section">
                <input type="file" id="cameraIniciar" accept="image/*" capture="environment" style="display: none;">
                <label for="cameraIniciar" class="camera-btn">
                    <i class="icon-camera"></i>
                    <span>Tirar Foto</span>
                </label>
                <img id="previewIniciar" class="photo-preview" style="display: none;">
            </div>
        </div>

        <div class="exec-actions">
            <button type="submit" class="exec-btn exec-btn-primary" id="btnIniciar">
                <i class="icon-play"></i>
                <span>Iniciar Atividade</span>
            </button>
        </div>
    </form>

    <?php elseif ($atividade->status == 'iniciada'): ?>
    <!-- Ações durante execução -->
    <div class="exec-actions">
        <!-- Pausar -->
        <form action="<?php echo site_url('obras_tecnico/pausarAtividade'); ?>" method="post" style="display: contents;">
            <input type="hidden" name="atividade_id" value="<?php echo $atividade->id; ?>">
            <button type="submit" class="exec-btn exec-btn-warning">
                <i class="icon-pause"></i>
                <span>Pausar</span>
            </button>
        </form>

        <!-- Finalizar -->
        <a href="#modalFinalizar" class="exec-btn exec-btn-primary" data-toggle="modal">
            <i class="icon-check"></i>
            <span>Finalizar</span>
        </a>
    </div>

    <!-- Modal Finalizar -->
    <div id="modalFinalizar" class="modal hide fade" tabindex="-1" role="dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3>Finalizar Atividade</h3>
        </div>

        <form action="<?php echo site_url('obras_tecnico/finalizarAtividade'); ?>" method="post">
            <input type="hidden" name="atividade_id" value="<?php echo $atividade->id; ?>">
            <input type="hidden" name="latitude" id="latFinalizar">
            <input type="hidden" name="longitude" id="lngFinalizar">

            <div class="modal-body">
                <div class="exec-form-group">
                    <label class="exec-form-label">Percentual Concluído</label>
                    <input type="range" name="percentual" min="0" max="100" value="<?php echo $atividade->percentual_concluido ?? 100; ?>"
                           class="form-control" oninput="document.getElementById('percVal').textContent = this.value + '%'">
                    <div style="text-align: center; margin-top: 10px; font-size: 18px; font-weight: 600; color: #667eea;">
                        <span id="percVal"><?php echo $atividade->percentual_concluido ?? 100; ?>%</span>
                    </div>
                </div>

                <div class="exec-form-group">
                    <label class="exec-form-label">Observações Finais</label>
                    <textarea name="observacao" class="exec-form-textarea" placeholder="Descreva o que foi realizado..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Finalizar Atividade</button>
            </div>
        </form>
    </div>

    <?php elseif ($atividade->status == 'pausada'): ?>
    <!-- Retomar -->
    <form action="<?php echo site_url('obras_tecnico/retomarAtividade'); ?>" method="post">
        <input type="hidden" name="atividade_id" value="<?php echo $atividade->id; ?>">
        <input type="hidden" name="latitude" id="latRetomar">
        <input type="hidden" name="longitude" id="lngRetomar">

        <div class="exec-actions">
            <button type="submit" class="exec-btn exec-btn-info">
                <i class="icon-play"></i>
                <span>Retomar Atividade</span>
            </button>
        </div>
    </form>

    <!-- Registrar Impedimento -->
    <div class="exec-card" style="margin-top: 25px;">
        <div class="exec-card-header">
            <div class="exec-card-title">
                <i class="icon-warning-sign"></i> Registrar Impedimento
            </div>
        </div>

        <form action="<?php echo site_url('obras_tecnico/registrarImpedimento'); ?>" method="post">
            <input type="hidden" name="atividade_id" value="<?php echo $atividade->id; ?>">

            <div class="exec-form-group">
                <label class="exec-form-label">Tipo de Impedimento</label>
                <select name="tipo_impedimento" class="exec-form-select" required>
                    <option value="">Selecione...</option>
                    <option value="clima">Condições Climáticas</option>
                    <option value="falta_material">Falta de Material</option>
                    <option value="falta_ferramenta">Falta de Ferramenta</option>
                    <option value="acesso_negado">Acesso Negado</option>
                    <option value="problema_tecnico">Problema Técnico</option>
                    <option value="outro">Outro</option>
                </select>
            </div>

            <div class="exec-form-group">
                <label class="exec-form-label">Descrição</label>
                <textarea name="descricao" class="exec-form-textarea" placeholder="Descreva o impedimento..." required></textarea>
            </div>

            <button type="submit" class="exec-btn exec-btn-danger">
                <i class="icon-warning-sign"></i>
                <span>Registrar Impedimento</span>
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Botão Voltar -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="<?php echo site_url('obras_tecnico/obra/' . $obra->id); ?>" class="btn btn-large">
            <i class="icon-arrow-left"></i> Voltar para Obra
        </a>
    </div>
</div>

<script>
// Geolocalização
document.addEventListener('DOMContentLoaded', function() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latIniciar') && (document.getElementById('latIniciar').value = position.coords.latitude);
            document.getElementById('lngIniciar') && (document.getElementById('lngIniciar').value = position.coords.longitude);
            document.getElementById('latFinalizar') && (document.getElementById('latFinalizar').value = position.coords.latitude);
            document.getElementById('lngFinalizar') && (document.getElementById('lngFinalizar').value = position.coords.longitude);
            document.getElementById('latRetomar') && (document.getElementById('latRetomar').value = position.coords.latitude);
            document.getElementById('lngRetomar') && (document.getElementById('lngRetomar').value = position.coords.longitude);
        }, function(err) {
            console.error('Erro ao obter localização:', err);
        });
    }
});

// Preview da foto
document.getElementById('cameraIniciar')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewIniciar');
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('fotoIniciar').value = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Timer
document.addEventListener('DOMContentLoaded', function() {
    const timerEl = document.getElementById('timer');
    if (timerEl && timerEl.classList.contains('running')) {
        let seconds = <?php echo $tempo_trabalhado * 3600; ?>;
        setInterval(function() {
            seconds++;
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            timerEl.textContent = String(hrs).padStart(2, '0') + ':' +
                                   String(mins).padStart(2, '0') + ':' +
                                   String(secs).padStart(2, '0');
        }, 1000);
    }
});
</script>
