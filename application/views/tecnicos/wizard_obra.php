<?php
// View do Wizard de Atividades para Obras - Design Moderno
$obra = $obra ?? null;
$etapa = $etapa ?? null;
$etapas = $etapas ?? [];
$atividade_em_andamento = $atividade_em_andamento ?? null;
$atividades_lista = $atividades_lista ?? [];
$tipos_atividades = $tipos_atividades ?? [];
$checkin_realizado = $checkin_realizado ?? false;
$obra_id = $obra_id ?? 0;
$etapa_id = $etapa_id ?? 0;
$is_portal_tecnico = $is_portal_tecnico ?? false;

// Busca atividades planejadas da etapa selecionada (se houver)
$atividades_planejadas_etapa = [];
if ($etapa_id && !empty($etapas)) {
    foreach ($etapas as $e) {
        if ($e->id == $etapa_id && isset($e->atividades)) {
            $atividades_planejadas_etapa = $e->atividades;
            break;
        }
    }
}
?>

<!-- Tema Moderno Obras -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/obras-modern-theme.css">

<style>
    /* Header da Obra */
    .obra-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 12px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
    }
    .obra-header h2 {
        margin: 0 0 8px 0;
        font-size: 24px;
    }
    .obra-header p {
        margin: 0;
        opacity: 0.9;
    }
    .obra-status {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 12px;
    }

    .progress-section {
        margin: 20px 0;
    }
    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .progress-bar {
        height: 10px;
        background: rgba(255,255,255,0.2);
        border-radius: 5px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: white;
        border-radius: 5px;
        transition: width 0.5s ease;
    }

    /* Cards */
    .wizard-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .wizard-card-header {
        background: #f8f9fa;
        padding: 16px 20px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .wizard-card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    .wizard-card-header .icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .wizard-card-body {
        padding: 20px;
    }

    /* Cronômetro */
    .timer-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 20px;
        text-align: center;
    }
    .timer-display {
        font-size: 48px;
        font-weight: bold;
        font-family: 'Courier New', monospace;
        margin: 10px 0;
    }
    .timer-label {
        font-size: 12px;
        text-transform: uppercase;
        opacity: 0.9;
    }
    .timer-row {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }
    .timer-col {
        text-align: center;
    }
    .timer-col .time {
        font-size: 24px;
        font-weight: bold;
    }
    .timer-col .label {
        font-size: 11px;
        opacity: 0.9;
    }

    /* Etapas */
    .etapa-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 15px;
    }
    .etapa-card {
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }
    .etapa-card:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .etapa-card.selected {
        border-color: #11998e;
        background: #e8f5e9;
    }
    .etapa-card .etapa-numero {
        position: absolute;
        top: -10px;
        left: 15px;
        background: #667eea;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
    }
    .etapa-card.selected .etapa-numero {
        background: #11998e;
    }
    .etapa-card h4 {
        margin: 12px 0 8px 0;
        font-size: 15px;
        color: #333;
    }
    .etapa-card p {
        margin: 0;
        font-size: 13px;
        color: #666;
    }
    .etapa-progress {
        margin-top: 12px;
    }
    .etapa-progress-bar {
        height: 6px;
        background: #e0e0e0;
        border-radius: 3px;
        overflow: hidden;
    }
    .etapa-progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px;
    }
    .etapa-card.selected .etapa-progress-fill {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    /* Botões */
    .btn-acao {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-acao:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }
    .btn-primary-tec {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-primary-tec:hover {
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    .btn-success-tec {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    .btn-success-tec:hover {
        color: white;
        box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4);
    }
    .btn-warning-tec {
        background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
        color: white;
    }
    .btn-danger-tec {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
    }
    .btn-secondary {
        background: #f8f9fa;
        color: #666;
        border: 1px solid #ddd;
    }
    .btn-secondary:hover {
        background: #e9ecef;
    }

    /* Atividade em Andamento */
    .atividade-andamento-card {
        background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);
        border: 2px solid #11998e;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .atividade-andamento-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    .atividade-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .atividade-info h4 {
        margin: 0;
        color: #11998e;
        font-size: 16px;
    }
    .atividade-info p {
        margin: 4px 0 0 0;
        font-size: 13px;
        color: #666;
    }

    /* Histórico */
    .historico-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid #ddd;
    }
    .historico-item.andamento {
        border-left-color: #11998e;
        background: #e8f5e9;
    }
    .historico-item.pausada {
        border-left-color: #f39c12;
        background: #fff3cd;
    }
    .historico-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .historico-tempo {
        font-size: 12px;
        color: #888;
    }
    .historico-tipo {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .tipo-execucao { background: #d4edda; color: #155724; }
    .tipo-problema { background: #f8d7da; color: #721c24; }
    .tipo-observacao { background: #fff3cd; color: #856404; }

    /* Form Elements */
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }
    .form-control {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    .form-control:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Upload Area */
    .upload-area {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-area:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .upload-area i {
        font-size: 40px;
        color: #667eea;
        margin-bottom: 10px;
    }

    /* Alertas */
    .alert-modern {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .alert-warning-modern {
        background: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffc107;
    }
    .alert-info-modern {
        background: #e8f4fd;
        color: #0c5460;
        border-left: 4px solid #3498db;
    }

    /* Grid Layout */
    .main-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    @media (min-width: 1024px) {
        .main-container {
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
    }

    /* Ações Footer */
    .acoes-footer {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    /* Atividades Planejadas */
    .atividades-planejadas-box {
        margin-top: 20px;
        padding: 15px;
        background: #fff3cd;
        border-radius: 10px;
        border-left: 4px solid #ffc107;
    }
    .atividade-planejada-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        margin: 8px 0;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .atividade-planejada-item:hover {
        border-color: #ffc107;
    }
    .atividade-planejada-item.selecionada {
        border-color: #11998e;
        background: #e8f5e9;
    }
</style>

<!-- Header da Obra -->
<div class="obra-header">
    <h2><i class='bx bx-building-house'></i> <?= htmlspecialchars($obra->nome ?? 'Obra') ?></h2>
    <p><i class='bx bx-map'></i> <?= htmlspecialchars($obra->endereco ?? 'Endereço não informado') ?></p>
    <?php if ($etapa): ?>
    <p style="margin-top: 8px;"><i class='bx bx-layer'></i> Etapa: <strong><?= htmlspecialchars($etapa->nome) ?></strong></p>
    <?php endif; ?>
    <span class="obra-status"><?= $obra->status ?? 'N/A' ?></span>

    <div class="progress-section">
        <div class="progress-header">
            <span>Progresso Geral</span>
            <span><?= $obra->percentual_concluido ?? 0 ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $obra->percentual_concluido ?? 0 ?>"></div>
        </div>
    </div>
</div>

<?php if ($atividade_em_andamento): ?>
<!-- Timer Card -->
<div class="timer-card">
    <div class="timer-label">Tempo de Execução</div>
    <div class="timer-display" id="cronometro">00:00:00</div>
    <div class="timer-row">
        <div class="timer-col">
            <div class="time" id="hora-inicio"><?= date('H:i', strtotime($atividade_em_andamento->hora_inicio)) ?></div>
            <div class="label">Início</div>
        </div>
        <div class="timer-col">
            <div class="time" id="hora-fim">--:--</div>
            <div class="label">Término</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="main-container">

    <!-- Coluna Esquerda -->
    <div>
        <?php if (!$atividade_em_andamento && !$checkin_realizado): ?>
        <!-- Seleção de Etapa -->
        <div class="wizard-card">
            <div class="wizard-card-header">
                <div class="icon"><i class='bx bx-layer'></i></div>
                <h3>Selecione a Etapa</h3>
            </div>
            <div class="wizard-card-body">
                <?php if (!empty($etapas)): ?>
                <form id="form-checkin-obra">
                    <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                    <input type="hidden" name="etapa_id" id="etapa_selecionada" value="<?= $etapa_id ?>">
                    <input type="hidden" name="obra_atividade_id" id="obra_atividade_selecionada" value="">
                    <input type="hidden" name="tipo_id" value="1">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div class="etapa-cards" id="etapa-selecao-container">
                        <?php foreach ($etapas as $index => $etapa_item): ?>
                        <div class="etapa-card" data-etapa="<?= $etapa_item->id ?>" data-index="<?= $index ?>"
                             onclick="selecionarEtapa(<?= $etapa_item->id ?>, <?= $index ?>)">
                            <div class="etapa-numero"><?= $etapa_item->numero_etapa ?? ($index + 1) ?></div>
                            <h4><?= htmlspecialchars($etapa_item->nome) ?></h4>
                            <p><?= htmlspecialchars($etapa_item->descricao ?? 'Sem descrição') ?></p>
                            <div class="etapa-progress">
                                <div class="etapa-progress-bar">
                                    <div class="etapa-progress-fill" style="width: <?= $etapa_item->progresso_real ?? $etapa_item->percentual_concluido ?? 0 ?>"></div>
                                </div>
                                <small style="color: #888;"><?= $etapa_item->progresso_real ?? $etapa_item->percentual_concluido ?? 0 ?>% concluído</small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Atividades Planejadas -->
                    <div class="atividades-planejadas-box" id="atividades-planejadas-container" style="display: none;">
                        <h5><i class='bx bx-task'></i> Atividades Planejadas</h5>
                        <div id="atividades-planejadas-lista">
                            <!-- Preenchido via JS -->
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label>Observações de Chegada</label>
                        <textarea name="observacoes" class="form-control" rows="3"
                            placeholder="Condições do local, trabalho a ser realizado..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Foto do Local</label>
                        <div class="upload-area" onclick="document.getElementById('foto-local').click()">
                            <i class='bx bx-camera'></i>
                            <p>Toque para adicionar foto</p>
                            <input type="file" name="foto" id="foto-local" accept="image/*" capture="environment"
                                   style="display: none;" onchange="previewFoto(this)">
                        </div>
                        <img id="preview-foto" style="max-width: 100%; margin-top: 15px; border-radius: 8px; display: none;">
                    </div>

                    <div class="acoes-footer">
                        <button type="button" class="btn-acao btn-success-tec" id="btn-iniciar" onclick="realizarCheckinObra()" disabled>
                            <i class='bx bx-play'></i> INICIAR TRABALHO
                        </button>
                        <a href="<?= site_url($is_portal_tecnico ? 'tecnicos/executar_obra/' . $obra_id : 'obras_tecnico/obra/' . $obra_id) ?>"
                           class="btn-acao btn-secondary">
                            <i class='bx bx-arrow-back'></i> Voltar
                        </a>
                    </div>
                </form>
                <?php else: ?>
                <div class="alert-modern alert-warning-modern">
                    <i class='bx bx-error-circle' style="font-size: 24px;"></i>
                    <div>
                        <strong>Atenção:</strong> Esta obra não possui etapas cadastradas.
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($atividade_em_andamento): ?>
        <!-- Atividade em Andamento -->
        <div class="atividade-andamento-card">
            <div class="atividade-andamento-header">
                <div class="atividade-icon">
                    <i class='bx <?= htmlspecialchars($atividade_em_andamento->tipo_icone ?? 'bx-wrench') ?>'></i>
                </div>
                <div class="atividade-info">
                    <h4><?= htmlspecialchars($atividade_em_andamento->tipo_nome) ?></h4>
                    <p>
                        <i class='bx bx-layer'></i> <?= htmlspecialchars($atividade_em_andamento->etapa_nome ?? 'Etapa não definida') ?>
                        <br>
                        <i class='bx bx-time'></i> Iniciado às <?= date('H:i', strtotime($atividade_em_andamento->hora_inicio)) ?>
                    </p>
                </div>
            </div>

            <div class="acoes-footer">
                <?php if ($atividade_em_andamento->status === 'pausada'): ?>
                <button class="btn-acao btn-success-tec" onclick="retomarAtividade(<?= $atividade_em_andamento->idAtividade ?>)">
                    <i class='bx bx-play'></i> Continuar
                </button>
                <?php else: ?>
                <button class="btn-acao btn-warning-tec" onclick="pausarAtividade(<?= $atividade_em_andamento->idAtividade ?>)">
                    <i class='bx bx-pause'></i> Pausar
                </button>
                <?php endif; ?>
                <button class="btn-acao btn-success-tec" onclick="abrirModalNovaAtividade()">
                    <i class='bx bx-plus'></i> Nova Atividade
                </button>
                <button class="btn-acao btn-primary-tec" onclick="abrirModalFoto()">
                    <i class='bx bx-camera'></i> Foto
                </button>
            </div>
        </div>

        <div class="acoes-footer" style="justify-content: flex-end;">
            <button class="btn-acao btn-success-tec" style="padding: 16px 32px; font-size: 16px;" onclick="abrirModalCheckoutObra()">
                <i class='bx bx-log-out-circle'></i> FINALIZAR TRABALHO
            </button>
        </div>
        <?php endif; ?>

        <!-- Botão Voltar -->
        <div class="acoes-footer">
            <a href="<?= site_url($is_portal_tecnico ? 'tecnicos/executar_obra/' . $obra_id : 'obras_tecnico/obra/' . $obra_id) ?>"
               class="btn-acao btn-secondary">
                <i class='bx bx-arrow-back'></i> Voltar à Obra
            </a>
        </div>
    </div>

    <!-- Coluna Direita: Histórico -->
    <div>
        <div class="wizard-card">
            <div class="wizard-card-header">
                <div class="icon"><i class='bx bx-history'></i></div>
                <h3>Histórico de Atividades</h3>
            </div>
            <div class="wizard-card-body" style="max-height: 500px; overflow-y: auto;">
                <?php if (!empty($atividades_lista)): ?>
                    <?php foreach ($atividades_lista as $atv): ?>
                    <div class="historico-item <?= $atv->status ?>">
                        <div class="historico-header">
                            <span class="historico-tempo">
                                <i class='bx bx-time'></i>
                                <?= date('H:i', strtotime($atv->hora_inicio)) ?>
                                <?php if ($atv->hora_fim): ?> - <?= date('H:i', strtotime($atv->hora_fim)) ?><?php endif; ?>
                            </span>
                            <span class="historico-tipo tipo-<?= strtolower($atv->categoria ?? 'execucao') ?>">
                                <?= $atv->tipo_nome ?>
                            </span>
                        </div>
                        <p style="margin: 0; font-size: 13px; color: #333;">
                            <?= htmlspecialchars($atv->equipamento ?? '') ?>
                        </p>
                        <?php if ($atv->etapa_nome): ?>
                        <small style="color: #888;">
                            <i class='bx bx-layer'></i> <?= htmlspecialchars($atv->etapa_nome) ?>
                        </small>
                        <?php endif; ?>
                        <div style="margin-top: 8px;">
                            <?php if ($atv->duracao_minutos): ?>
                                <span style="font-size: 12px; color: #667eea; font-weight: 600;">
                                    <i class='bx bx-time-five'></i>
                                    <?= floor($atv->duracao_minutos / 60) ?>h <?= $atv->duracao_minutos % 60 ?>min
                                </span>
                            <?php endif; ?>
                            <?php if ($atv->concluida == 1): ?>
                                <span style="font-size: 12px; color: #27ae60; margin-left: 10px;">
                                    <i class='bx bx-check-circle'></i> Concluída
                                </span>
                            <?php elseif ($atv->concluida == 0 && $atv->status == 'finalizada'): ?>
                                <span style="font-size: 12px; color: #f39c12; margin-left: 10px;">
                                    <i class='bx bx-x-circle'></i> Não Concluída
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($atv->status == 'pausada'): ?>
                        <div style="margin-top: 10px;">
                            <button class="btn-acao btn-primary-tec" style="padding: 6px 12px; font-size: 12px;"
                                    onclick="retomarAtividade(<?= $atv->idAtividade ?>)">
                                <i class='bx bx-play'></i> Retomar
                            </button>
                        </div>
                        <?php endif; ?>

                        <?php if ($atv->status == 'reaberta'): ?>
                        <div style="margin-top: 10px;">
                            <span style="font-size: 12px; color: #9b59b6; margin-right: 8px;">
                                <i class='bx bx-refresh'></i> Reatendimento Aguardando
                            </span>
                            <button class="btn-acao btn-primary-tec" style="padding: 6px 12px; font-size: 12px; background: linear-gradient(135deg, #9b59b6, #8e44ad);"
                                    onclick="iniciarReatendimento(<?= $atv->idAtividade ?>)">
                                <i class='bx bx-play'></i> Iniciar
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #888;">
                        <i class='bx bx-history' style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                        <p>Nenhuma atividade registrada</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script>
console.log('Wizard JS carregando...');
// Definir funções imediatamente para garantir disponibilidade global
window.previewFoto = window.previewFoto || function() { console.log('previewFoto não carregado ainda'); };
window.selecionarEtapa = window.selecionarEtapa || function() { console.log('selecionarEtapa não carregado ainda'); };

// Dados das etapas - usando JSON_HEX_TAG para evitar que </script> nos dados quebre o código
var etapasDados = <?= json_encode($etapas ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
console.log('Etapas carregadas:', etapasDados ? etapasDados.length : 0);
var etapaSelecionadaId = <?= $etapa_id ? json_encode($etapa_id) : 'null' ?>;

// Helper para obter cookie CSRF
function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return match[2];
}

// CSRF Token para requisições fetch
var csrfMetaName = document.querySelector('meta[name="csrf-token-name"]');
var csrfMetaCookie = document.querySelector('meta[name="csrf-cookie-name"]');
var csrfTokenName = (csrfMetaName && csrfMetaName.content) ? csrfMetaName.content : '<?= config_item("csrf_token_name") ?>';
var csrfCookieName = (csrfMetaCookie && csrfMetaCookie.content) ? csrfMetaCookie.content : '<?= config_item("csrf_cookie_name") ?>';
var csrfToken = getCookie(csrfCookieName);

// Função helper para adicionar CSRF ao FormData
function appendCsrf(formData) {
    if (csrfToken && csrfTokenName) {
        formData.append(csrfTokenName, csrfToken);
    }
    return formData;
}

// Garantir que as funções estejam disponíveis globalmente
window.selecionarEtapa = function(etapaId, index) {
    etapaSelecionadaId = etapaId;
    document.getElementById('etapa_selecionada').value = etapaId;
    document.getElementById('btn-iniciar').disabled = false;

    // Visual feedback
    document.querySelectorAll('.etapa-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.querySelector('.etapa-card[data-etapa="' + etapaId + '"]').classList.add('selected');

    // Mostra atividades planejadas
    mostrarAtividadesPlanejadas(index);
}

window.mostrarAtividadesPlanejadas = function(index) {
    const etapa = etapasDados[index];
    const container = document.getElementById('atividades-planejadas-container');
    const lista = document.getElementById('atividades-planejadas-lista');

    if (!etapa || !etapa.atividades || etapa.atividades.length === 0) {
        container.style.display = 'none';
        return;
    }

    let html = '<p style="margin: 0 0 10px 0; font-size: 13px;">Selecione uma atividade (opcional):</p>';
    html += '<div class="atividade-planejada-item" onclick="selecionarAtividadePlanejada(null)">';
    html += '<input type="radio" name="atividade_radio" value="" checked> ';
    html += '<span>Nova atividade (não vinculada)</span>';
    html += '</div>';

    etapa.atividades.forEach(function(atv) {
        const isConcluida = atv.status === 'concluida';
        const disabled = isConcluida ? 'disabled' : '';
        const classe = isConcluida ? 'disabled' : '';
        const icone = isConcluida ? '<i class="bx bx-check-circle" style="color: #28a745;"></i>' : '<i class="bx bx-circle" style="color: #ffc107;"></i>';

        html += '<div class="atividade-planejada-item ' + classe + '" onclick="selecionarAtividadePlanejada(' + atv.id + ')">';
        html += '<input type="radio" name="atividade_radio" value="' + atv.id + '" ' + disabled + '> ';
        html += icone + ' <span>' + atv.titulo + '</span>';
        if (isConcluida) {
            html += ' <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; margin-left: 10px;">Concluída</span>';
        }
        html += '</div>';
    });

    lista.innerHTML = html;
    container.style.display = 'block';
}

window.selecionarAtividadePlanejada = function(atividadeId) {
    document.getElementById('obra_atividade_selecionada').value = atividadeId || '';

    document.querySelectorAll('.atividade-planejada-item').forEach(item => {
        item.classList.remove('selecionada');
    });

    if (atividadeId) {
        const item = document.querySelector('input[value="' + atividadeId + '"]').closest('.atividade-planejada-item');
        if (item) item.classList.add('selecionada');
    }
}

window.realizarCheckinObra = function() {
    const form = document.getElementById('form-checkin-obra');
    const formData = appendCsrf(new FormData(form));

    if (!formData.get('etapa_id')) {
        alert('Por favor, selecione uma etapa.');
        return;
    }

    fetch('<?= site_url("atividades/checkin_obra") ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Resposta não é JSON:', text.substring(0, 500));
                throw new Error('Resposta inválida do servidor');
            }
        });
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(err => {
        alert('Erro ao realizar check-in: ' + err.message);
        console.error(err);
    });
}

window.previewFoto = function(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('preview-foto');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

window.pausarAtividade = function(id) {
    if (!confirm('Deseja pausar esta atividade?')) return;

    const bodyPausar = 'atividade_id=' + id + (csrfToken && csrfTokenName ? '&' + csrfTokenName + '=' + encodeURIComponent(csrfToken) : '');
    fetch('<?= site_url("atividades/pausar") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: bodyPausar
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.text().then(text => {
            try { return JSON.parse(text); } catch (e) { throw new Error('Resposta inválida'); }
        });
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(err => {
        alert('Erro ao pausar: ' + err.message);
        console.error(err);
    });
}

window.retomarAtividade = function(id) {
    const bodyRetomar = 'atividade_id=' + id + (csrfToken && csrfTokenName ? '&' + csrfTokenName + '=' + encodeURIComponent(csrfToken) : '');
    fetch('<?= site_url("atividades/retomar") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: bodyRetomar
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.text().then(text => {
            try { return JSON.parse(text); } catch (e) { throw new Error('Resposta inválida'); }
        });
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(err => {
        alert('Erro ao retomar: ' + err.message);
        console.error(err);
    });
}

window.abrirModalNovaAtividade = function() {
    const modal = document.createElement('div');
    modal.id = 'modal-nova-atividade';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;';
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; width: 90%; max-width: 500px; max-height: 90vh; overflow: auto;">
            <div style="padding: 20px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;"><i class='bx bx-plus'></i> Nova Atividade</h3>
                <button onclick="fecharModal('modal-nova-atividade')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div style="padding: 20px;">
                <form id="form-nova-atividade">
                    <div class="form-group">
                        <label>Tipo de Atividade</label>
                        <select name="tipo_id" class="form-control" id="novo-tipo-id">
                            <option value="">Selecione...</option>
                            <?php foreach ($tipos_atividades as $tipo): ?>
                            <option value="<?= is_object($tipo) ? $tipo->id : ($tipo['id'] ?? '') ?>"><?= htmlspecialchars(is_object($tipo) ? $tipo->nome : ($tipo['nome'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3" placeholder="Descreva a atividade..."></textarea>
                    </div>
                </form>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e0e0e0; display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn-acao btn-secondary" onclick="fecharModal('modal-nova-atividade')">Cancelar</button>
                <button class="btn-acao btn-primary-tec" onclick="adicionarNovaAtividade()">Adicionar</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

window.adicionarNovaAtividade = function() {
    const form = document.getElementById('form-nova-atividade');
    const formData = appendCsrf(new FormData(form));
    formData.append('obra_id', '<?= $obra_id ?>');

    fetch('<?= site_url("atividades/adicionar_atividade_obra") ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.text().then(text => {
            try { return JSON.parse(text); } catch (e) { throw new Error('Resposta inválida'); }
        });
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(err => {
        alert('Erro: ' + err.message);
        console.error(err);
    });
}

window.abrirModalFoto = function() {
    const modal = document.createElement('div');
    modal.id = 'modal-foto';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;';
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; width: 90%; max-width: 500px;">
            <div style="padding: 20px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;"><i class='bx bx-camera'></i> Adicionar Foto</h3>
                <button onclick="fecharModal('modal-foto')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div style="padding: 20px;">
                <form id="form-foto" enctype="multipart/form-data">
                    <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" name="foto" accept="image/*" capture="environment" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e0e0e0; display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn-acao btn-secondary" onclick="fecharModal('modal-foto')">Cancelar</button>
                <button class="btn-acao btn-primary-tec" onclick="adicionarFoto()">Adicionar</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

window.adicionarFoto = function() {
    const form = document.getElementById('form-foto');
    const formData = appendCsrf(new FormData(form));

    fetch('<?= site_url("atividades/adicionar_foto_obra") ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.text().then(text => {
            try { return JSON.parse(text); } catch (e) { throw new Error('Resposta inválida'); }
        });
    })
    .then(data => {
        if (data.success) {
            fecharModal('modal-foto');
            alert('Foto adicionada com sucesso!');
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(err => {
        alert('Erro: ' + err.message);
        console.error(err);
    });
}

window.abrirModalCheckoutObra = function() {
    const modal = document.createElement('div');
    modal.id = 'modal-checkout';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;';
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 90vh; overflow: auto;">
            <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;"><i class='bx bx-log-out-circle'></i> Finalizar Trabalho</h3>
                <button onclick="fecharModal('modal-checkout')" style="background: none; border: none; font-size: 24px; color: white; cursor: pointer;">&times;</button>
            </div>
            <div style="padding: 20px;">
                <form id="form-checkout-obra">
                    <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                    <div class="form-group">
                        <label>Trabalho Concluído?</label>
                        <select name="concluida" class="form-control">
                            <option value="1">Sim, trabalho concluído</option>
                            <option value="0">Não, preciso retornar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resumo do Trabalho</label>
                        <textarea name="resumo_final" class="form-control" rows="3"
                            placeholder="Descreva o que foi feito, pendências..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Problemas/Pendências</label>
                        <textarea name="pendencias" class="form-control" rows="3"
                            placeholder="Algum problema encontrado? Material faltando?"></textarea>
                    </div>
                </form>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e0e0e0; display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn-acao btn-secondary" onclick="fecharModal('modal-checkout')">Cancelar</button>
                <button class="btn-acao btn-success-tec" onclick="realizarCheckoutObra()">
                    <i class='bx bx-check-double'></i> FINALIZAR
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

window.realizarCheckoutObra = function() {
    const form = document.getElementById('form-checkout-obra');
    const formData = appendCsrf(new FormData(form));

    fetch('<?= site_url("atividades/checkout_obra") ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.text().then(text => {
            try { return JSON.parse(text); } catch (e) { throw new Error('Resposta inválida'); }
        });
    })
    .then(data => {
        if (data.success) {
            alert('Trabalho finalizado com sucesso!');
            <?php if ($is_portal_tecnico): ?>
            window.location.href = '<?= site_url("tecnicos/executar_obra/" . $obra_id) ?>';
            <?php else: ?>
            window.location.href = '<?= site_url("obras_tecnico/obra/" . $obra_id) ?>';
            <?php endif; ?>
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(err => {
        alert('Erro ao finalizar trabalho: ' + err.message);
        console.error(err);
    });
}

window.fecharModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) modal.remove();
}

// Iniciar reatendimento (reatividade reaberta)
window.iniciarReatendimento = function(reatendimentoId) {
    if (!confirm('Iniciar reatendimento desta atividade?')) return;

    fetch('<?= site_url("obras/iniciarReatendimento/") ?>' + reatendimentoId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= config_item('csrf_token_name') ?>': getCookie('<= config_item('csrf_cookie_name') ?>')
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            alert('Reatendimento iniciado com sucesso!');
            window.location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(err => {
        alert('Erro ao iniciar reatendimento: ' + err.message);
        console.error(err);
    });
}

// Helper para pegar cookie (CSRF)
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return '';
}

<?php if ($atividade_em_andamento): ?>
// Cronômetro
var cronometroIniciado = new Date('<?= $atividade_em_andamento->hora_inicio ?>');

window.atualizarCronometro = function() {
    const agora = new Date();
    const diff = agora - cronometroIniciado;

    const horas = Math.floor(diff / 3600000);
    const minutos = Math.floor((diff % 3600000) / 60000);
    const segundos = Math.floor((diff % 60000) / 1000);

    const formatado =
        String(horas).padStart(2, '0') + ':' +
        String(minutos).padStart(2, '0') + ':' +
        String(segundos).padStart(2, '0');

    const cronometro = document.getElementById('cronometro');
    if (cronometro) {
        cronometro.textContent = formatado;
    }
}

setInterval(atualizarCronometro, 1000);
atualizarCronometro();
<?php endif; ?>
</script>
