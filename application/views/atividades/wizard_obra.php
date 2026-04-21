<?php
// View do Wizard de Atividades para Obras (integração com sistema de obras)
$obra = $obra ?? null;
$etapa = $etapa ?? null;
$etapas = $etapas ?? [];
$atividade_em_andamento = $atividade_em_andamento ?? null;
$atividades_lista = $atividades_lista ?? [];
$tipos_atividades = $tipos_atividades ?? [];
$checkin_realizado = $checkin_realizado ?? false;
$obra_id = $obra_id ?? 0;
$etapa_id = $etapa_id ?? 0;
?>

<style>
.wizard-obra-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.etapa-selecao {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.etapa-card {
    background: #f8f9fa;
    border: 2px solid transparent;
    border-radius: 10px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s;
}

.etapa-card:hover {
    border-color: #11998e;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.etapa-card.selected {
    border-color: #11998e;
    background: #e8f5e9;
}

.painel-hora-obra {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
}

.painel-hora-obra .hora-label {
    font-size: 12px;
    text-transform: uppercase;
    opacity: 0.9;
    margin-bottom: 5px;
}

.painel-hora-obra .hora-valor {
    font-size: 36px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}

.painel-hora-obra .cronometro {
    font-size: 48px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}

.historico-atividades-obra {
    max-height: 400px;
    overflow-y: auto;
}
</style>

<div class="row-fluid wizard-container">
    <div class="span12">
        <!-- Painel de Hora -->
        <div class="painel-hora-obra">
            <div class="row-fluid">
                <div class="span4">
                    <div class="hora-label">Hora Início</div>
                    <div class="hora-valor" id="hora-inicio">
                        <?= $atividade_em_andamento ? date('H:i', strtotime($atividade_em_andamento->hora_inicio)) : '--:--' ?>
                    </div>
                </div>
                <div class="span4">
                    <div class="hora-label">Tempo Decorrido</div>
                    <div class="cronometro" id="cronometro">00:00:00</div>
                </div>
                <div class="span4">
                    <div class="hora-label">Hora Fim</div>
                    <div class="hora-valor" id="hora-fim">--:--</div>
                </div>
            </div>
        </div>

        <!-- Obra Info -->
        <div class="wizard-obra-header">
            <h4><i class="bx bx-building-house"></i> <?= htmlspecialchars($obra->nome) ?></h4>
            <p><i class="bx bx-map"></i> <?= htmlspecialchars($obra->endereco ?? 'N/A') ?></p>
            <?php if ($etapa): ?>
            <p><i class="bx bx-layer"></i> Etapa: <strong><?= htmlspecialchars($etapa->nome) ?></strong></p>
            <?php endif; ?>
        </div>

        <?php if (!$atividade_em_andamento && !$checkin_realizado): ?>
        <!-- PASSO 1: CHECK-IN E SELEÇÃO DE ETAPA -->
        <div class="widget-box" id="step-checkin-obra">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-log-in-circle"></i></span>
                <h5>Check-in - Início do Trabalho na Obra</h5>
            </div>
            <div class="widget-content">
                <form id="form-checkin-obra">
                    <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                    <input type="hidden" name="etapa_id" id="etapa_selecionada" value="<?= $etapa_id ?>">
                    <input type="hidden" name="tipo_id" value="1"> <!-- Tipo: Check-in -->
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <?php if (!$etapa && count($etapas) > 0): ?>
                    <!-- Seleção de Etapa -->
                    <div class="control-group">
                        <label class="control-label">Selecione a Etapa da Obra</label>
                        <div class="controls etapa-selecao">
                            <?php foreach ($etapas as $etapa_item): ?>
                            <div class="etapa-card" data-etapa="<?= $etapa_item->id ?>" onclick="selecionarEtapa(<?= $etapa_item->id ?>)">
                                <h5><?= htmlspecialchars($etapa_item->nome) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($etapa_item->descricao ?? 'Sem descrição') ?></p>
                                <div class="progress" style="margin-top: 10px;">
                                    <div class="bar" style="width: <?= $etapa_item->percentual_concluido ?? 0 ?>%"></div>
                                </div>
                                <small><?= $etapa_item->percentual_concluido ?? 0 ?>% concluído</small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Foto do Local</label>
                                <div class="controls">
                                    <input type="file" name="foto" id="foto-local" accept="image/*" capture="environment" class="input-block-level">
                                    <img id="preview-foto" style="max-width: 100%; margin-top: 10px; display: none;">
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Localização GPS</label>
                                <div class="controls">
                                    <button type="button" class="btn btn-info" onclick="obterLocalizacao()">
                                        <i class="bx bx-map-pin"></i> Atualizar Localização
                                    </button>
                                    <div id="info-gps" class="text-muted" style="margin-top: 10px;">
                                        Nenhuma localização detectada
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Observações de Chegada</label>
                        <div class="controls">
                            <textarea name="observacoes" class="input-block-level" rows="3" placeholder="Condições do local, trabalho a ser realizado, etc."></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success btn-large" <?= !$etapa && count($etapas) > 0 ? 'disabled id="btn-iniciar"' : '' ?>>
                            <i class="bx bx-play"></i> INICIAR TRABALHO
                        </button>
                        <a href="<?= site_url('obras_tecnico/obra/' . $obra_id) ?>" class="btn">
                            <i class="bx bx-arrow-back"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php else: ?>
        <!-- PASSO 2: ATIVIDADES EM ANDAMENTO -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-time-five"></i></span>
                <h5>Atividade em Andamento</h5>
            </div>
            <div class="widget-content">
                <?php if ($atividade_em_andamento): ?>
                <div class="card-atividade em-andamento" id="card-atual">
                    <div class="row-fluid">
                        <div class="span8">
                            <h4>
                                <i class="bx <?= htmlspecialchars($atividade_em_andamento->tipo_icone ?? 'bx-wrench') ?>"
                                   style="color: <?= htmlspecialchars($atividade_em_andamento->tipo_cor ?? '#007bff') ?>"></i>
                                <?= htmlspecialchars($atividade_em_andamento->tipo_nome) ?>
                            </h4>
                            <p><strong>Início:</strong> <?= date('H:i', strtotime($atividade_em_andamento->hora_inicio)) ?></p>
                            <p id="duracao-atual"><strong>Duração:</strong> Calculando...</p>
                            <?php if ($atividade_em_andamento->equipamento): ?>
                                <p><strong>Equipamento:</strong> <?= htmlspecialchars($atividade_em_andamento->equipamento) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="span4 text-right">
                            <button class="btn btn-warning" onclick="pausarAtividade(<?= $atividade_em_andamento->idAtividade ?>)">
                                <i class="bx bx-pause"></i> Pausar
                            </button>
                            <button class="btn btn-danger" onclick="abrirModalFinalizar(<?= $atividade_em_andamento->idAtividade ?>)">
                                <i class="bx bx-stop"></i> Finalizar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row-fluid" style="margin-top: 15px;">
                    <div class="span6">
                        <button class="btn btn-info" onclick="abrirModalNovaAtividade()">
                            <i class="bx bx-plus"></i> Nova Atividade
                        </button>
                        <button class="btn" onclick="abrirModalFoto()">
                            <i class="bx bx-camera"></i> Adicionar Foto
                        </button>
                    </div>
                    <div class="span6 text-right">
                        <button class="btn btn-success btn-large" onclick="abrirModalCheckoutObra()">
                            <i class="bx bx-log-out-circle"></i> FINALIZAR TRABALHO
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bx bx-info-circle"></i> Nenhuma atividade em andamento.
                </div>
                <button class="btn btn-info" onclick="abrirModalNovaAtividade()">
                    <i class="bx bx-plus"></i> Iniciar Nova Atividade
                </button>
                <button class="btn btn-success" onclick="abrirModalCheckoutObra()">
                    <i class="bx bx-log-out-circle"></i> Finalizar Trabalho
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- HISTÓRICO -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-history"></i></span>
                <h5>Histórico de Atividades nesta Obra</h5>
            </div>
            <div class="widget-content historico-atividades-obra">
                <div class="historico-timeline" id="historico-atividades">
                    <?php foreach ($atividades_lista as $atv): ?>
                    <div class="timeline-item <?= $atv->status == 'em_andamento' ? 'andamento' : '' ?> <?= $atv->status == 'pausada' ? 'pausada' : '' ?>">
                        <div class="row-fluid">
                            <div class="span2">
                                <strong><?= date('H:i', strtotime($atv->hora_inicio)) ?></strong>
                                <?php if ($atv->hora_fim): ?>
                                    <br><small>até <?= date('H:i', strtotime($atv->hora_fim)) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="span7">
                                <strong><?= htmlspecialchars($atv->tipo_nome) ?></strong>
                                <?php if ($atv->duracao_minutos): ?>
                                    <span class="label label-info"><?= floor($atv->duracao_minutos / 60) ?>h <?= $atv->duracao_minutos % 60 ?>min</span>
                                <?php endif; ?>
                                <?php if ($atv->concluida == 1): ?>
                                    <span class="label label-success"><i class="bx bx-check"></i> Concluída</span>
                                <?php elseif ($atv->concluida == 0 && $atv->status == 'finalizada'): ?>
                                    <span class="label label-warning"><i class="bx bx-x"></i> Não Concluída</span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($atv->equipamento ?? '') ?></small>
                            </div>
                            <div class="span3 text-right">
                                <?php if ($atv->status == 'pausada'): ?>
                                    <button class="btn btn-small btn-info" onclick="retomarAtividade(<?= $atv->idAtividade ?>)">
                                        <i class="bx bx-play"></i> Retomar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Botão Voltar -->
        <a href="<?= site_url('obras_tecnico/obra/' . $obra_id) ?>" class="btn">
            <i class="bx bx-arrow-back"></i> Voltar à Obra
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Modais (iguais ao wizard.php original) -->
<?php // Os modais são os mesmos do wizard.php - Nova Atividade, Finalizar, Checkout, Foto ?>

<script>
// Funções específicas para obra
let etapaSelecionada = <?= $etapa_id ? 'true' : 'false' ?>;

function selecionarEtapa(id) {
    document.getElementById('etapa_selecionada').value = id;
    document.getElementById('btn-iniciar').disabled = false;

    // Visual feedback
    document.querySelectorAll('.etapa-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.querySelector('.etapa-card[data-etapa="' + id + '"]').classList.add('selected');
}

// Função de check-in específica para obra
function realizarCheckinObra() {
    const form = document.getElementById('form-checkin-obra');
    const formData = new FormData(form);

    fetch('<?= site_url("atividades/checkin_obra") ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    });
}

// Checkout específico para obra
function abrirModalCheckoutObra() {
    $('#modal-checkout').modal('show');
}

// Outras funções são iguais ao wizard original (pausar, retomar, finalizar, etc.)
// ... copiar do wizard.php
</script>
