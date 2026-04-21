<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- WIZARD DE ATENDIMENTO MODERNO -->
<style>
    /* Modal Moderno */
    .modal-wizard-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        z-index: 9999;
        overflow-y: auto;
    }
    .modal-wizard-container {
        min-height: 100vh;
        padding: 20px;
    }
    .modal-wizard-content {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        overflow: hidden;
    }
    .modal-wizard-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-wizard-header h3 {
        margin: 0;
        font-size: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-wizard-body {
        padding: 30px;
        background: #f8f9fa;
        min-height: 500px;
    }

    /* Steps Indicator */
    .wizard-steps {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        gap: 15px;
    }
    .wizard-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        opacity: 0.5;
        transition: all 0.3s;
    }
    .wizard-step.active {
        opacity: 1;
    }
    .wizard-step.completed {
        opacity: 1;
    }
    .wizard-step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #ddd;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 8px;
        transition: all 0.3s;
    }
    .wizard-step.active .wizard-step-icon {
        background: #3498db;
        box-shadow: 0 4px 15px rgba(52,152,219,0.4);
    }
    .wizard-step.completed .wizard-step-icon {
        background: #27ae60;
    }
    .wizard-step-label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
    }
    .wizard-step.active .wizard-step-label {
        color: #3498db;
    }
    .wizard-step.completed .wizard-step-label {
        color: #27ae60;
    }

    /* Cards Modernos */
    .wizard-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e0e0e0;
    }
    .wizard-card-title {
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .wizard-card-title i {
        color: #3498db;
    }

    /* Seleção de Etapa */
    .etapas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    .etapa-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }
    .etapa-card:hover {
        border-color: #3498db;
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(52,152,219,0.2);
    }
    .etapa-card.selecionada {
        border-color: #3498db;
        background: #ebf5fb;
        box-shadow: 0 5px 20px rgba(52,152,219,0.3);
    }
    .etapa-card i {
        font-size: 32px;
        color: #95a5a6;
        margin-bottom: 10px;
    }
    .etapa-card.selecionada i {
        color: #3498db;
    }
    .etapa-card h4 {
        margin: 0 0 8px 0;
        font-size: 16px;
        color: #2c3e50;
    }
    .etapa-card .progress {
        height: 6px;
        background: #ecf0f1;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 10px;
    }
    .etapa-card .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2980b9);
        transition: width 0.3s;
    }

    /* Timer Moderno */
    .timer-display {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        color: white;
        margin-bottom: 25px;
    }
    .timer-display .time {
        font-size: 56px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: 3px;
    }
    .timer-display .label {
        font-size: 14px;
        opacity: 0.8;
        margin-top: 5px;
    }

    /* Botões de Ação */
    .acoes-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    .acao-btn {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .acao-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .acao-btn i {
        font-size: 36px;
        margin-bottom: 10px;
        display: block;
    }
    .acao-btn.atividade { border-color: #3498db; }
    .acao-btn.atividade i { color: #3498db; }
    .acao-btn.atividade:hover { background: #ebf5fb; }

    .acao-btn.foto { border-color: #9b59b6; }
    .acao-btn.foto i { color: #9b59b6; }
    .acao-btn.foto:hover { background: #f5eef8; }

    .acao-btn.pausar { border-color: #f39c12; }
    .acao-btn.pausar i { color: #f39c12; }
    .acao-btn.pausar:hover { background: #fef9e7; }

    .acao-btn.finalizar { border-color: #27ae60; }
    .acao-btn.finalizar i { color: #27ae60; }
    .acao-btn.finalizar:hover { background: #eafaf1; }

    /* Modais Internos */
    .wizard-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 10001;
        align-items: center;
        justify-content: center;
    }
    .wizard-modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .wizard-modal-header {
        background: #2c3e50;
        color: white;
        padding: 20px 25px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wizard-modal-header h4 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .wizard-modal-body {
        padding: 25px;
    }

    /* Upload de Foto */
    .foto-upload-area {
        border: 3px dashed #ddd;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #fafafa;
    }
    .foto-upload-area:hover {
        border-color: #3498db;
        background: #ebf5fb;
    }
    .foto-upload-area i {
        font-size: 48px;
        color: #95a5a6;
        margin-bottom: 15px;
    }
    .foto-preview {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 15px;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-badge.ativo {
        background: #d4edda;
        color: #155724;
    }

    /* Info Box */
    .info-box {
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        padding: 15px 20px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    /* Lista de Atividades */
    .atividade-item {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid #ddd;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .atividade-item.executada { border-left-color: #27ae60; }
    .atividade-item.pendente { border-left-color: #f39c12; }

    /* Botões Principais */
    .btn-wizard-primary {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-wizard-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(52,152,219,0.4);
    }
    .btn-wizard-primary:disabled {
        background: #95a5a6;
        cursor: not-allowed;
        transform: none;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .acoes-grid { grid-template-columns: 1fr; }
        .etapas-grid { grid-template-columns: 1fr; }
        .timer-display .time { font-size: 36px; }
    }
</style>

<!-- HEADER DA OBRA -->
<div class="widget-box">
    <div class="widget-title" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white;">
        <span class="icon"><i class="bx bx-building"></i></span>
        <h5><?= htmlspecialchars($obra->nome) ?></h5>
        <div class="buttons">
            <a href="<?= site_url('tecnicos/minhas_obras') ?>" class="btn btn-mini" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                <i class="bx bx-arrow-back"></i> Voltar
            </a>
        </div>
    </div>
    <div class="widget-content">
        <div class="row-fluid">
            <div class="span8">
                <p><strong><i class="bx bx-user"></i> Cliente:</strong> <?= htmlspecialchars($obra->cliente_nome ?? 'Não definido') ?></p>
                <p><strong><i class="bx bx-map"></i> Endereço:</strong> <?= htmlspecialchars($obra->endereco ?? 'Não definido') ?></p>
                <div class="progress" style="height: 20px; margin: 15px 0;">
                    <div class="progress-bar progress-bar-success" style="width: <?= $obra->percentual_concluido ?? 0 ?>%">
                        <?= $obra->percentual_concluido ?? 0 ?>%
                    </div>
                </div>
            </div>
            <div class="span4" style="text-align: right;">
                <?php if (!empty($wizard_em_andamento)): ?>
                    <div class="alert alert-warning" style="margin-bottom: 15px;">
                        <i class="bx bx-time"></i> <strong>Atividade em andamento</strong><br>
                        <?= htmlspecialchars($wizard_em_andamento->etapa_nome ?? 'Geral') ?><br>
                        <small>Iniciado às <?= date('H:i', strtotime($wizard_em_andamento->hora_inicio)) ?></small>
                    </div>
                    <button class="btn btn-success btn-large" onclick="WizardAtendimento.continuar(<?= $wizard_em_andamento->id ?>, '<?= $wizard_em_andamento->hora_inicio ?>', '<?= htmlspecialchars($wizard_em_andamento->etapa_nome ?? 'Atividade geral') ?>')" style="padding: 15px 30px;">
                        <i class="bx bx-play-circle"></i> CONTINUAR ATENDIMENTO
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary btn-large" onclick="WizardAtendimento.iniciar()" style="padding: 15px 30px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none;">
                        <i class="bx bx-log-in-circle"></i> INICIAR ATENDIMENTO
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ESTATISTICAS -->
<div class="row-fluid">
    <div class="span3">
        <div class="widget-box" style="text-align: center;">
            <div class="widget-content">
                <i class="bx bx-layer" style="font-size: 32px; color: #3498db;"></i>
                <h2 style="margin: 10px 0; color: #2c3e50;"><?= count($etapas) ?></h2>
                <small style="color: #7f8c8d;">Etapas Totais</small>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box" style="text-align: center;">
            <div class="widget-content">
                <i class="bx bx-check-circle" style="font-size: 32px; color: #27ae60;"></i>
                <h2 style="margin: 10px 0; color: #2c3e50;"><?= count(array_filter($etapas, function($e) { return ($e->status ?? '') === 'concluida'; })) ?></h2>
                <small style="color: #7f8c8d;">Etapas Concluídas</small>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box" style="text-align: center;">
            <div class="widget-content">
                <i class="bx bx-wrench" style="font-size: 32px; color: #f39c12;"></i>
                <h2 style="margin: 10px 0; color: #2c3e50;"><?= count($minhas_atividades) ?></h2>
                <small style="color: #7f8c8d;">Minhas Atividades</small>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box" style="text-align: center;">
            <div class="widget-content">
                <i class="bx bx-task" style="font-size: 32px; color: #e74c3c;"></i>
                <h2 style="margin: 10px 0; color: #2c3e50;"><?= count(array_filter($minhas_os, function($os) { return ($os->status ?? '') !== 'Finalizada'; })) ?></h2>
                <small style="color: #7f8c8d;">OS em Aberto</small>
            </div>
        </div>
    </div>
</div>

<!-- LISTA DE ETAPAS -->
<div class="widget-box">
    <div class="widget-title">
        <span class="icon"><i class="bx bx-list-check"></i></span>
        <h5>Etapas da Obra</h5>
    </div>
    <div class="widget-content">
        <?php if (empty($etapas)): ?>
            <div class="alert alert-info">
                <i class="bx bx-info-circle"></i> Nenhuma etapa cadastrada para esta obra.
            </div>
        <?php else: ?>
            <?php foreach ($etapas as $etapa): ?>
                <?php
                    $statusClass = '';
                    $statusLabel = '';
                    $statusIcon = '';
                    switch($etapa->status ?? 'pendente') {
                        case 'concluida':
                            $statusClass = 'concluida';
                            $statusLabel = '<span class="label label-success">Concluída</span>';
                            $statusIcon = 'bx-check-circle';
                            break;
                        case 'em-andamento':
                            $statusClass = 'em-andamento';
                            $statusLabel = '<span class="label label-info">Em Andamento</span>';
                            $statusIcon = 'bx-play-circle';
                            break;
                        default:
                            $statusClass = 'pendente';
                            $statusLabel = '<span class="label label-warning">Pendente</span>';
                            $statusIcon = 'bx-time';
                    }
                ?>
                <div class="widget-box etapa-item <?= $statusClass ?>" style="margin-bottom: 15px;">
                    <div class="widget-content">
                        <div class="row-fluid">
                            <div class="span1" style="text-align: center; padding-top: 10px;">
                                <i class="bx <?= $statusIcon ?>" style="font-size: 28px; color: <?= $statusClass === 'concluida' ? '#27ae60' : ($statusClass === 'em-andamento' ? '#3498db' : '#f39c12') ?>;"></i>
                            </div>
                            <div class="span7">
                                <h5 style="margin: 0 0 8px 0;"><?= htmlspecialchars($etapa->nome) ?></h5>
                                <?= $statusLabel ?>
                                <span class="label label-info"><?= $etapa->percentual_concluido ?? 0 ?>% concluído</span>
                            </div>
                            <div class="span4" style="text-align: right;">
                                <?php if (empty($wizard_em_andamento) && ($etapa->status ?? '') !== 'concluida'): ?>
                                    <button class="btn btn-primary" onclick="WizardAtendimento.iniciarComEtapa(<?= $etapa->id ?>, '<?= htmlspecialchars($etapa->nome) ?>')">
                                        <i class="bx bx-play"></i> Iniciar
                                    </button>
                                <?php endif; ?>
                                <a href="#etapa<?= $etapa->id ?>" class="btn" data-toggle="collapse">
                                    <i class="bx bx-chevron-down"></i> Detalhes
                                </a>
                            </div>
                        </div>

                        <!-- DETALHES DA ETAPA -->
                        <div id="etapa<?= $etapa->id ?>" class="collapse" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                            <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($etapa->descricao ?? 'Nenhuma descrição')) ?></p>

                            <?php if (!empty($atividades_por_etapa[$etapa->id])): ?>
                                <h6>Atividades Registradas:</h6>
                                <ul class="unstyled">
                                    <?php foreach ($atividades_por_etapa[$etapa->id] as $ativ): ?>
                                        <li style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                            <i class="bx bx-check-square" style="color: <?= ($ativ->status ?? '') === 'concluida' ? '#27ae60' : '#f39c12' ?>;"></i>
                                            <?= htmlspecialchars($ativ->titulo ?? $ativ->descricao ?? 'Atividade #' . $ativ->id) ?>
                                            <span class="label <?= ($ativ->status ?? '') === 'concluida' ? 'label-success' : 'label-warning' ?>">
                                                <?= ($ativ->status ?? '') === 'concluida' ? 'Concluída' : 'Pendente' ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- MINHAS ATIVIDADES RECENTES -->
<?php if (!empty($minhas_atividades)): ?>
<div class="widget-box">
    <div class="widget-title">
        <span class="icon"><i class="bx bx-history"></i></span>
        <h5>Minhas Atividades Recentes</h5>
    </div>
    <div class="widget-content">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Etapa</th>
                    <th>Período</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($minhas_atividades, 0, 5) as $ativ): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($ativ->data_atividade ?? $ativ->created_at ?? 'now')) ?></td>
                        <td><?= htmlspecialchars($ativ->etapa_nome ?? 'Geral') ?></td>
                        <td><?= !empty($ativ->hora_inicio) ? date('H:i', strtotime($ativ->hora_inicio)) : '--:--' ?> - <?= !empty($ativ->hora_fim) ? date('H:i', strtotime($ativ->hora_fim)) : '--:--' ?></td>
                        <td>
                            <?php if ($ativ->status === 'concluida'): ?>
                                <span class="label label-success"><i class="bx bx-check"></i> Concluída</span>
                            <?php elseif ($ativ->status === 'em_andamento'): ?>
                                <span class="label label-info"><i class="bx bx-time"></i> Em Andamento</span>
                            <?php else: ?>
                                <span class="label label-warning"><i class="bx bx-hourglass"></i> Pendente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ==================== MODAL WIZARD DE ATENDIMENTO ==================== -->
<div id="modalWizard" class="modal-wizard-overlay">
    <div class="modal-wizard-container">
        <div class="modal-wizard-content">

            <!-- HEADER -->
            <div class="modal-wizard-header">
                <h3><i class="bx bx-walk"></i> <span id="wizardTitle">Novo Atendimento</span></h3>
                <button class="btn btn-danger" onclick="WizardAtendimento.fechar()" style="border: none; background: rgba(255,255,255,0.2);">
                    <i class="bx bx-x"></i> Fechar
                </button>
            </div>

            <!-- STEPS INDICATOR -->
            <div class="wizard-steps" id="wizardSteps">
                <div class="wizard-step active" data-step="1">
                    <div class="wizard-step-icon"><i class="bx bx-log-in"></i></div>
                    <div class="wizard-step-label">Check-in</div>
                </div>
                <div class="wizard-step" data-step="2">
                    <div class="wizard-step-icon"><i class="bx bx-wrench"></i></div>
                    <div class="wizard-step-label">Execução</div>
                </div>
                <div class="wizard-step" data-step="3">
                    <div class="wizard-step-icon"><i class="bx bx-check-double"></i></div>
                    <div class="wizard-step-label">Finalizar</div>
                </div>
            </div>

            <!-- BODY -->
            <div class="modal-wizard-body">

                <!-- STEP 1: CHECK-IN -->
                <div id="stepCheckin" class="wizard-step-content">
                    <div class="wizard-card">
                        <div class="wizard-card-title">
                            <i class="bx bx-list-check"></i> Selecione a Etapa
                        </div>
                        <div class="etapas-grid">
                            <?php foreach ($etapas as $etapa): ?>
                                <div class="etapa-card" data-etapa-id="<?= $etapa->id ?>" onclick="WizardAtendimento.selecionarEtapa(<?= $etapa->id ?>, '<?= htmlspecialchars($etapa->nome) ?>', this)">
                                    <i class="bx bx-layer"></i>
                                    <h4><?= htmlspecialchars($etapa->nome) ?></h4>
                                    <span class="label label-info"><?= $etapa->percentual_concluido ?? 0 ?>%</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $etapa->percentual_concluido ?? 0 ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="checkinEtapaId" value="">
                    </div>

                    <div class="wizard-card">
                        <div class="wizard-card-title">
                            <i class="bx bx-camera"></i> Foto do Local (Opcional)
                        </div>
                        <div class="foto-upload-area" onclick="document.getElementById('checkinFoto').click()">
                            <i class="bx bx-camera"></i>
                            <h4>Clique para adicionar foto</h4>
                            <p class="text-muted">Registre o estado inicial do local</p>
                            <input type="file" id="checkinFoto" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'checkinPreview')">
                        </div>
                        <img id="checkinPreview" class="foto-preview" style="display: none;">
                    </div>

                    <div class="wizard-card">
                        <div class="wizard-card-title">
                            <i class="bx bx-note"></i> Observações
                        </div>
                        <textarea id="checkinObservacoes" rows="3" class="span12" placeholder="Descreva as condições do local, materiais disponíveis, etc..."></textarea>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button id="btnIniciar" class="btn-wizard-primary" onclick="WizardAtendimento.realizarCheckin()" disabled>
                            <i class="bx bx-play-circle"></i> INICIAR ATENDIMENTO
                        </button>
                    </div>
                </div>

                <!-- STEP 2: EXECUÇÃO -->
                <div id="stepExecucao" class="wizard-step-content" style="display: none;">
                    <!-- TIMER -->
                    <div class="timer-display">
                        <div class="time" id="timerDisplay">00:00:00</div>
                        <div class="label"><i class="bx bx-time"></i> Tempo de Execução</div>
                    </div>

                    <!-- INFO ETAPA -->
                    <div class="info-box">
                        <i class="bx bx-info-circle"></i>
                        <strong>Etapa em Execução:</strong> <span id="etapaExecucaoNome">--</span>
                    </div>

                    <!-- AÇÕES -->
                    <div class="acoes-grid">
                        <div class="acao-btn atividade" onclick="WizardAtendimento.abrirModalAtividade()">
                            <i class="bx bx-plus-circle"></i>
                            <h4>Registrar Atividade</h4>
                            <small>Adicione trabalhos realizados</small>
                        </div>
                        <div class="acao-btn foto" onclick="WizardAtendimento.abrirModalFoto()">
                            <i class="bx bx-camera"></i>
                            <h4>Adicionar Foto</h4>
                            <small>Documente o serviço</small>
                        </div>
                        <div class="acao-btn pausar" onclick="WizardAtendimento.pausarAtendimento()">
                            <i class="bx bx-pause-circle"></i>
                            <h4>Pausar</h4>
                            <small>Interrompa temporariamente</small>
                        </div>
                        <div class="acao-btn finalizar" onclick="WizardAtendimento.abrirModalCheckout()">
                            <i class="bx bx-check-circle"></i>
                            <h4>Finalizar</h4>
                            <small>Encerre o atendimento</small>
                        </div>
                    </div>

                    <!-- ATIVIDADES REGISTRADAS -->
                    <div class="wizard-card">
                        <div class="wizard-card-title">
                            <i class="bx bx-list-ul"></i> Atividades Registradas
                            <span class="badge badge-info" id="contadorAtividades" style="margin-left: 10px;">0</span>
                        </div>
                        <div id="listaAtividades">
                            <p class="text-muted" style="text-align: center; padding: 20px;">
                                <i class="bx bx-info-circle" style="font-size: 32px; color: #ddd; display: block; margin-bottom: 10px;"></i>
                                Nenhuma atividade registrada ainda.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- MODAL REGISTRAR ATIVIDADE -->
<div id="modalAtividade" class="wizard-modal">
    <div class="wizard-modal-content">
        <div class="wizard-modal-header">
            <h4><i class="bx bx-plus-circle"></i> Registrar Atividade</h4>
            <button class="btn btn-mini" onclick="WizardAtendimento.fecharModal('modalAtividade')" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div class="wizard-modal-body">
            <div class="control-group">
                <label class="control-label"><i class="bx bx-tag"></i> Tipo de Atividade *</label>
                <div class="controls">
                    <select id="atividadeTipoId" class="span12" style="height: 40px; font-size: 14px;">
                        <option value="">Selecione o tipo...</option>
                        <?php foreach ($tipos_atividades as $tipo): ?>
                            <option value="<?= is_object($tipo) ? $tipo->id : ($tipo['id'] ?? '') ?>">
                                <?= htmlspecialchars(is_object($tipo) ? $tipo->nome : ($tipo['nome'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><i class="bx bx-detail"></i> Descrição</label>
                <div class="controls">
                    <textarea id="atividadeDescricao" rows="4" class="span12" placeholder="Descreva detalhadamente o que foi realizado..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><i class="bx bx-status"></i> Status</label>
                <div class="controls">
                    <label class="radio inline" style="padding: 10px 20px; background: #d4edda; border-radius: 20px; margin-right: 10px;">
                        <input type="radio" name="atividadeStatus" value="executada" checked> <i class="bx bx-check" style="color: #27ae60;"></i> Executada
                    </label>
                    <label class="radio inline" style="padding: 10px 20px; background: #fff3cd; border-radius: 20px;">
                        <input type="radio" name="atividadeStatus" value="pendente"> <i class="bx bx-time" style="color: #f39c12;"></i> Pendente
                    </label>
                </div>
            </div>

            <div class="form-actions" style="text-align: right; margin: 20px 0 0 0; padding-top: 20px; border-top: 1px solid #eee;">
                <button class="btn" onclick="WizardAtendimento.fecharModal('modalAtividade')">Cancelar</button>
                <button class="btn btn-primary" onclick="WizardAtendimento.salvarAtividade()" style="padding: 10px 25px;">
                    <i class="bx bx-save"></i> Salvar Atividade
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADICIONAR FOTO -->
<div id="modalFoto" class="wizard-modal">
    <div class="wizard-modal-content">
        <div class="wizard-modal-header" style="background: #9b59b6;">
            <h4><i class="bx bx-camera"></i> Adicionar Foto</h4>
            <button class="btn btn-mini" onclick="WizardAtendimento.fecharModal('modalFoto')" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div class="wizard-modal-body">
            <div class="foto-upload-area" onclick="document.getElementById('fotoFile').click()">
                <i class="bx bx-camera" style="font-size: 48px;"></i>
                <h4>Clique para selecionar foto</h4>
                <p class="text-muted">Ou arraste e solte aqui</p>
                <input type="file" id="fotoFile" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFotoModal(this)">
            </div>
            <img id="fotoPreviewModal" class="foto-preview" style="display: none; margin-top: 15px;">

            <div class="control-group" style="margin-top: 20px;">
                <label class="control-label">Descrição da Foto</label>
                <div class="controls">
                    <textarea id="fotoDescricao" rows="2" class="span12" placeholder="Descreva o que está na foto..."></textarea>
                </div>
            </div>

            <div class="form-actions" style="text-align: right; margin: 20px 0 0 0; padding-top: 20px; border-top: 1px solid #eee;">
                <button class="btn" onclick="WizardAtendimento.fecharModal('modalFoto')">Cancelar</button>
                <button class="btn btn-success" onclick="WizardAtendimento.salvarFoto()" style="padding: 10px 25px;">
                    <i class="bx bx-camera"></i> Salvar Foto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CHECKOUT / FINALIZAR -->
<div id="modalCheckout" class="wizard-modal">
    <div class="wizard-modal-content">
        <div class="wizard-modal-header" style="background: #27ae60;">
            <h4><i class="bx bx-check-double"></i> Finalizar Atendimento</h4>
            <button class="btn btn-mini" onclick="WizardAtendimento.fecharModal('modalCheckout')" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div class="wizard-modal-body">
            <!-- RESUMO -->
            <div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h5 style="margin: 0 0 15px 0;"><i class="bx bx-time"></i> Resumo do Atendimento</h5>
                <div class="row-fluid">
                    <div class="span6">
                        <small style="opacity: 0.7;">Início:</small><br>
                        <strong id="checkoutHoraInicio" style="font-size: 18px;">--:--</strong>
                    </div>
                    <div class="span6" style="text-align: right;">
                        <small style="opacity: 0.7;">Tempo Total:</small><br>
                        <strong id="checkoutTempoTotal" style="font-size: 24px; color: #27ae60;">--:--</strong>
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><i class="bx bx-question-circle"></i> Trabalho Concluído? *</label>
                <div class="controls">
                    <select id="checkoutConcluido" class="span12" style="height: 40px;">
                        <option value="1">Sim, trabalho concluído</option>
                        <option value="0">Não, preciso retornar</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><i class="bx bx-detail"></i> Resumo do Trabalho</label>
                <div class="controls">
                    <textarea id="checkoutResumo" rows="3" class="span12" placeholder="Descreva o que foi realizado durante o atendimento..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><i class="bx bx-error-circle"></i> Problemas/Pendências</label>
                <div class="controls">
                    <textarea id="checkoutPendencias" rows="2" class="span12" placeholder="Algum problema encontrado? Material faltando?"></textarea>
                </div>
            </div>

            <div class="wizard-card" style="margin: 20px 0;">
                <div class="wizard-card-title">
                    <i class="bx bx-camera"></i> Foto de Saída (Opcional)
                </div>
                <div class="foto-upload-area" onclick="document.getElementById('checkoutFoto').click()" style="padding: 20px;">
                    <i class="bx bx-camera" style="font-size: 32px;"></i>
                    <p>Clique para adicionar foto de saída</p>
                    <input type="file" id="checkoutFoto" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'checkoutPreview')">
                </div>
                <img id="checkoutPreview" class="foto-preview" style="display: none;">
            </div>

            <div class="form-actions" style="text-align: right; margin: 20px 0 0 0; padding-top: 20px; border-top: 1px solid #eee;">
                <button class="btn" onclick="WizardAtendimento.fecharModal('modalCheckout')">Voltar</button>
                <button class="btn btn-success btn-large" onclick="WizardAtendimento.realizarCheckout()" style="padding: 12px 30px; background: linear-gradient(135deg, #27ae60 0%, #229954 100%); border: none;">
                    <i class="bx bx-check-double"></i> FINALIZAR ATENDIMENTO
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// WIZARD DE ATENDIMENTO MODERNO
window.WizardAtendimento = {
    obraId: <?= json_encode($obra->id) ?>,
    etapaSelecionadaId: null,
    etapaSelecionadaNome: '',
    atividadeEmAndamento: null,
    timerInterval: null,
    horaInicio: null,
    atividadesRegistradas: [],
    csrfTokenName: '<?= config_item("csrf_token_name") ?>',
    csrfCookieName: '<?= config_item("csrf_cookie_name") ?>',
    stepAtual: 1,

    getCookie: function(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    },

    getCsrfToken: function() {
        return this.getCookie(this.csrfCookieName);
    },

    appendCsrf: function(formData) {
        var token = this.getCsrfToken();
        if (token) {
            formData.append(this.csrfTokenName, token);
        }
        return formData;
    },

    // INICIAR WIZARD
    iniciar: function() {
        this.stepAtual = 1;
        this.atualizarSteps();
        document.getElementById('modalWizard').style.display = 'block';
        document.body.style.overflow = 'hidden';
        this.mostrarStep(1);
    },

    // INICIAR COM ETAPA PRE-SELECIONADA
    iniciarComEtapa: function(etapaId, etapaNome) {
        this.iniciar();
        setTimeout(function() {
            var el = document.querySelector('.etapa-card[data-etapa-id="' + etapaId + '"]');
            if (el) {
                WizardAtendimento.selecionarEtapa(etapaId, etapaNome, el);
            }
        }, 100);
    },

    // CONTINUAR ATENDIMENTO EM ANDAMENTO
    continuar: function(atividadeId, horaInicio, etapaNome) {
        this.atividadeEmAndamento = atividadeId;
        this.horaInicio = new Date(horaInicio);
        this.etapaSelecionadaNome = etapaNome;
        this.stepAtual = 2;

        document.getElementById('etapaExecucaoNome').textContent = etapaNome || 'Atividade geral';
        document.getElementById('wizardTitle').textContent = 'Continuar Atendimento';
        document.getElementById('modalWizard').style.display = 'block';
        document.body.style.overflow = 'hidden';
        this.atualizarSteps();
        this.mostrarStep(2);
        this.iniciarTimer();
    },

    // FECHAR WIZARD
    fechar: function() {
        document.getElementById('modalWizard').style.display = 'none';
        document.body.style.overflow = '';
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
        // Reset
        this.etapaSelecionadaId = null;
        this.etapaSelecionadaNome = '';
        document.querySelectorAll('.etapa-card').forEach(function(el) {
            el.classList.remove('selecionada');
        });
        document.getElementById('btnIniciar').disabled = true;
    },

    // ATUALIZAR INDICADOR DE STEPS
    atualizarSteps: function() {
        document.querySelectorAll('.wizard-step').forEach(function(step, index) {
            var stepNum = index + 1;
            step.classList.remove('active', 'completed');
            if (stepNum < WizardAtendimento.stepAtual) {
                step.classList.add('completed');
            } else if (stepNum === WizardAtendimento.stepAtual) {
                step.classList.add('active');
            }
        });
    },

    // MOSTRAR STEP
    mostrarStep: function(step) {
        document.querySelectorAll('.wizard-step-content').forEach(function(el) {
            el.style.display = 'none';
        });
        if (step === 1) {
            document.getElementById('stepCheckin').style.display = 'block';
        } else if (step === 2) {
            document.getElementById('stepExecucao').style.display = 'block';
        }
    },

    // SELECIONAR ETAPA
    selecionarEtapa: function(etapaId, etapaNome, elemento) {
        this.etapaSelecionadaId = etapaId;
        this.etapaSelecionadaNome = etapaNome;
        document.getElementById('checkinEtapaId').value = etapaId;

        document.querySelectorAll('.etapa-card').forEach(function(el) {
            el.classList.remove('selecionada');
        });
        elemento.classList.add('selecionada');
        document.getElementById('btnIniciar').disabled = false;
    },

    // PREVIEW FOTO
    previewFoto: function(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById(previewId);
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    },

    previewFotoModal: function(input) {
        this.previewFoto(input, 'fotoPreviewModal');
        document.querySelector('#modalFoto .foto-upload-area').style.display = 'none';
        document.getElementById('fotoPreviewModal').style.display = 'block';
    },

    // REALIZAR CHECKIN
    realizarCheckin: function() {
        var etapaId = this.etapaSelecionadaId;
        if (!etapaId) {
            alert('Selecione uma etapa.');
            return;
        }

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('etapa_id', etapaId);
        formData.append('tipo_id', '1');

        var fotoInput = document.getElementById('checkinFoto');
        if (fotoInput.files.length > 0) {
            formData.append('foto', fotoInput.files[0]);
        }

        var observacoes = document.getElementById('checkinObservacoes').value;
        if (observacoes) {
            formData.append('observacoes', observacoes);
        }

        var btn = document.getElementById('btnIniciar');
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Iniciando...';
        btn.disabled = true;

        var self = this;
        fetch('<?= site_url("atividades/checkin_obra") ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                self.atividadeEmAndamento = data.atividade_id;
                self.horaInicio = new Date();
                self.stepAtual = 2;
                document.getElementById('etapaExecucaoNome').textContent = self.etapaSelecionadaNome;
                document.getElementById('wizardTitle').textContent = 'Execução em Andamento';
                self.atualizarSteps();
                self.mostrarStep(2);
                self.iniciarTimer();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
                btn.innerHTML = '<i class="bx bx-play-circle"></i> INICIAR ATENDIMENTO';
                btn.disabled = false;
            }
        })
        .catch(function(err) {
            alert('Erro ao iniciar: ' + err.message);
            btn.innerHTML = '<i class="bx bx-play-circle"></i> INICIAR ATENDIMENTO';
            btn.disabled = false;
        });
    },

    // TIMER
    iniciarTimer: function() {
        var self = this;
        this.timerInterval = setInterval(function() {
            var agora = new Date();
            var diff = agora - self.horaInicio;

            var horas = Math.floor(diff / 3600000);
            var minutos = Math.floor((diff % 3600000) / 60000);
            var segundos = Math.floor((diff % 60000) / 1000);

            var formatado =
                String(horas).padStart(2, '0') + ':' +
                String(minutos).padStart(2, '0') + ':' +
                String(segundos).padStart(2, '0');

            document.getElementById('timerDisplay').textContent = formatado;
        }, 1000);
    },

    // MODAIS
    abrirModalAtividade: function() {
        document.getElementById('modalAtividade').style.display = 'flex';
    },

    abrirModalFoto: function() {
        document.getElementById('modalFoto').style.display = 'flex';
    },

    abrirModalCheckout: function() {
        document.getElementById('checkoutHoraInicio').textContent = this.horaInicio.toLocaleTimeString('pt-BR');

        var agora = new Date();
        var diff = agora - this.horaInicio;
        var horas = Math.floor(diff / 3600000);
        var minutos = Math.floor((diff % 3600000) / 60000);
        document.getElementById('checkoutTempoTotal').textContent =
            String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');

        document.getElementById('modalCheckout').style.display = 'flex';
    },

    fecharModal: function(modalId) {
        document.getElementById(modalId).style.display = 'none';
    },

    // SALVAR ATIVIDADE
    salvarAtividade: function() {
        var tipoId = document.getElementById('atividadeTipoId').value;
        var descricao = document.getElementById('atividadeDescricao').value;
        var status = document.querySelector('input[name="atividadeStatus"]:checked').value;

        if (!tipoId) {
            alert('Selecione o tipo de atividade.');
            return;
        }

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('tipo_id', tipoId);
        formData.append('descricao', descricao);
        formData.append('status', status);

        var self = this;
        fetch('<?= site_url("atividades/adicionar_atividade_obra") ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                self.fecharModal('modalAtividade');
                self.adicionarAtividadeLista({
                    tipo: document.getElementById('atividadeTipoId').options[document.getElementById('atividadeTipoId').selectedIndex].text,
                    descricao: descricao,
                    status: status,
                    data: new Date().toLocaleString('pt-BR')
                });
                document.getElementById('atividadeTipoId').value = '';
                document.getElementById('atividadeDescricao').value = '';
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao salvar atividade.');
            console.error(err);
        });
    },

    adicionarAtividadeLista: function(atividade) {
        var lista = document.getElementById('listaAtividades');
        if (this.atividadesRegistradas.length === 0) {
            lista.innerHTML = '';
        }
        this.atividadesRegistradas.push(atividade);
        document.getElementById('contadorAtividades').textContent = this.atividadesRegistradas.length;

        var statusClass = atividade.status === 'executada' ? 'executada' : 'pendente';
        var statusIcon = atividade.status === 'executada' ? 'bx-check' : 'bx-time';
        var statusColor = atividade.status === 'executada' ? '#27ae60' : '#f39c12';

        var html = '<div class="atividade-item ' + statusClass + '">' +
            '<div style="display: flex; justify-content: space-between; align-items: center;">' +
            '<div>' +
            '<i class="bx ' + statusIcon + '" style="color: ' + statusColor + '; margin-right: 8px;"></i>' +
            '<strong>' + atividade.tipo + '</strong><br>' +
            '<small style="color: #666;">' + atividade.descricao + '</small>' +
            '</div>' +
            '<small class="text-muted">' + atividade.data + '</small>' +
            '</div>' +
            '</div>';

        lista.innerHTML += html;
    },

    // SALVAR FOTO
    salvarFoto: function() {
        var fotoInput = document.getElementById('fotoFile');
        var descricao = document.getElementById('fotoDescricao').value;

        if (fotoInput.files.length === 0) {
            alert('Selecione uma foto.');
            return;
        }

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('foto', fotoInput.files[0]);
        formData.append('descricao', descricao);

        var self = this;
        fetch('<?= site_url("atividades/adicionar_foto_obra") ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                self.fecharModal('modalFoto');
                alert('Foto adicionada com sucesso!');
                fotoInput.value = '';
                document.getElementById('fotoDescricao').value = '';
                document.getElementById('fotoPreviewModal').style.display = 'none';
                document.querySelector('#modalFoto .foto-upload-area').style.display = 'block';
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao adicionar foto.');
            console.error(err);
        });
    },

    // PAUSAR
    pausarAtendimento: function() {
        if (!confirm('Deseja pausar este atendimento?')) return;

        var body = 'obra_id=' + this.obraId;
        var token = this.getCsrfToken();
        if (token) {
            body += '&' + this.csrfTokenName + '=' + encodeURIComponent(token);
        }

        var self = this;
        fetch('<?= site_url("atividades/pausar") ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: body
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                self.fechar();
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao pausar.');
            console.error(err);
        });
    },

    // CHECKOUT / FINALIZAR
    realizarCheckout: function() {
        var concluida = document.getElementById('checkoutConcluido').value;
        var resumo = document.getElementById('checkoutResumo').value;
        var pendencias = document.getElementById('checkoutPendencias').value;

        var formData = new FormData();
        formData = this.appendCsrf(formData);
        formData.append('obra_id', this.obraId);
        formData.append('concluida', concluida);
        formData.append('resumo_final', resumo);
        formData.append('pendencias', pendencias);

        var fotoInput = document.getElementById('checkoutFoto');
        if (fotoInput.files.length > 0) {
            formData.append('foto', fotoInput.files[0]);
        }

        var self = this;
        fetch('<?= site_url("atividades/checkout_obra") ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                alert('Atendimento finalizado com sucesso!');
                self.fechar();
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(function(err) {
            alert('Erro ao finalizar atendimento.');
            console.error(err);
        });
    }
};
</script>
