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
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.etapa-card {
    background: #f8f9fa;
    border: 3px solid transparent;
    border-radius: 10px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.etapa-card:hover {
    border-color: #11998e;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.etapa-card.selected {
    border-color: #11998e;
    background: #e8f5e9;
    box-shadow: 0 4px 15px rgba(17,153,142,0.3);
}

.etapa-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.etapa-card .etapa-numero {
    position: absolute;
    top: -10px;
    left: 15px;
    background: #667eea;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.etapa-card.selected .etapa-numero {
    background: #11998e;
}

.etapa-card h5 {
    margin: 10px 0 8px 0;
    font-size: 16px;
    color: #333;
}

.etapa-card .progress {
    height: 6px;
    margin: 10px 0;
    background: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
}

.etapa-card .progress-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s;
}

.etapa-card.selected .progress-bar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.atividades-planejadas {
    margin-top: 20px;
    padding: 15px;
    background: #fff3cd;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
}

.atividades-planejadas h6 {
    margin: 0 0 10px 0;
    color: #856404;
    font-size: 14px;
}

.atividade-planejada-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    margin: 5px 0;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
}

.atividade-planejada-item:hover {
    border-color: #ffc107;
    transform: translateX(3px);
}

.atividade-planejada-item.selecionada {
    border-color: #11998e;
    background: #e8f5e9;
}

.atividade-planejada-item input[type="radio"] {
    margin: 0;
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

.etapa-badge {
    display: inline-block;
    padding: 4px 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    font-size: 12px;
    margin-bottom: 10px;
}

.alert-etapa-obrigatoria {
    background: #fff3cd;
    color: #856404;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid #ffc107;
}
</style>

<div class="row-fluid wizard-container">
    <div class="span12">
        <!-- Painel de Hora -->
        <?php if ($atividade_em_andamento): ?>
        <div class="painel-hora-obra">
            <div class="row-fluid">
                <div class="span4">
                    <div class="hora-label">Hora Início</div>
                    <div class="hora-valor" id="hora-inicio">
                        <?= date('H:i', strtotime($atividade_em_andamento->hora_inicio)) ?>
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
        <?php endif; ?>

        <!-- Obra Info -->
        <div class="wizard-obra-header">
            <h4><i class="bx bx-building-house"></i> <?= htmlspecialchars($obra->nome) ?></h4>
            <p><i class="bx bx-map"></i> <?= htmlspecialchars($obra->endereco ?? 'N/A') ?></p>
            <?php if ($etapa): ?>
            <p><i class="bx bx-layer"></i> Etapa: <strong><?= htmlspecialchars($etapa->nome) ?></strong></p>
            <?php endif; ?>
        </div>

        <!-- Alerta de Etapa Obrigatória -->
        <?php if (!$atividade_em_andamento && empty($etapa_id)): ?>
        <div class="alert-etapa-obrigatoria">
            <i class="bx bx-info-circle"></i>
            <strong>Atenção:</strong> Para iniciar uma atividade, você deve selecionar a <strong>Etapa da Obra</strong> em que está trabalhando.
        </div>
        <?php endif; ?>

        <?php if (!$atividade_em_andamento && !$checkin_realizado): ?>
        <!-- PASSO 1: CHECK-IN E SELEÇÃO DE ETAPA -->
        <div class="widget-box" id="step-checkin-obra">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-log-in-circle"></i></span>
                <h5>Check-in - Selecione a Etapa e Inicie o Trabalho</h5>
            </div>
            <div class="widget-content">
                <form id="form-checkin-obra">
                    <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                    <input type="hidden" name="etapa_id" id="etapa_selecionada" value="<?= $etapa_id ?>">
                    <input type="hidden" name="obra_atividade_id" id="obra_atividade_selecionada" value="">
                    <input type="hidden" name="tipo_id" value="1"> <!-- Tipo: Check-in -->
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <?php if (count($etapas) > 0): ?>
                    <!-- Seleção de Etapa (OBRIGATÓRIO) -->
                    <div class="control-group">
                        <label class="control-label">
                            <i class="bx bx-layer"></i> Selecione a Etapa da Obra <span style="color: #dc3545;">*</span>
                        </label>
                        <div class="controls etapa-selecao" id="etapa-selecao-container">
                            <?php foreach ($etapas as $index => $etapa_item): ?>
                            <div class="etapa-card" data-etapa="<?= $etapa_item->id ?>" data-index="<?= $index ?>"
                                 onclick="selecionarEtapa(<?= $etapa_item->id ?>, <?= $index ?>)">
                                <div class="etapa-numero"><?= $etapa_item->numero_etapa ?? ($index + 1) ?></div>
                                <h5><?= htmlspecialchars($etapa_item->nome) ?></h5>
                                <p class="text-muted" style="font-size: 13px;"><?= htmlspecialchars($etapa_item->descricao ?? 'Sem descrição') ?></p>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $etapa_item->progresso_real ?? $etapa_item->percentual_concluido ?? 0 ?>%"></div>
                                </div>
                                <small><?= $etapa_item->progresso_real ?? $etapa_item->percentual_concluido ?? 0 ?>% concluído</small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Atividades Planejadas da Etapa Selecionada -->
                    <div class="control-group" id="atividades-planejadas-container" style="display: none;">
                        <label class="control-label">
                            <i class="bx bx-task"></i> Selecione a Atividade Planejada (Opcional)
                        </label>
                        <div class="controls">
                            <div class="atividades-planejadas" id="atividades-planejadas-lista">
                                <!-- Preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bx bx-error-circle"></i>
                        <strong>Atenção:</strong> Esta obra não possui etapas cadastradas. Entre em contato com o administrador.
                    </div>
                    <?php endif; ?>

                    <div class="row-fluid" style="margin-top: 20px;">
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
                        <button type="submit" class="btn btn-success btn-large" id="btn-iniciar" disabled>
                            <i class="bx bx-play"></i> INICIAR TRABALHO
                        </button>
                        <a href="<?= site_url(($is_portal_tecnico ?? false) ? 'tecnicos/executar_obra/' . $obra_id : 'obras_tecnico/obra/' . $obra_id) ?>" class="btn">
                            <i class="bx bx-arrow-back"></i> Voltar à Obra
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
                <div class="etapa-badge" id="etapa-atual-badge">
                    <i class="bx bx-layer"></i> <?= htmlspecialchars($atividade_em_andamento->etapa_nome ?? 'Etapa não definida') ?>
                </div>
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
                            <button class="btn btn-warning" onclick="pausarAtividade(<?= $atividade_em_andamento->idAtividade ?>")>
                                <i class="bx bx-pause"></i> Pausar
                            </button>
                            <button class="btn btn-danger" onclick="abrirModalFinalizar(<?= $atividade_em_andamento->idAtividade ?>")>
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
                                <?php if ($atv->etapa_nome): ?>
                                    <br><small class="text-muted"><i class="bx bx-layer"></i> <?= htmlspecialchars($atv->etapa_nome) ?></small>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($atv->equipamento ?? '') ?></small>
                            </div>
                            <div class="span3 text-right">
                                <?php if ($atv->status == 'pausada'): ?>
                                    <button class="btn btn-small btn-info" onclick="retomarAtividade(<?= $atv->idAtividade ?>")>
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
        <a href="<?= site_url(($is_portal_tecnico ?? false) ? 'tecnicos/executar_obra/' . $obra_id : 'obras_tecnico/obra/' . $obra_id) ?>" class="btn">
            <i class="bx bx-arrow-back"></i> Voltar à Obra
        </a>
        <?php endif; ?>
    </div>
</div>

<script>
// Dados das etapas e suas atividades planejadas
const etapasDados = <?= json_encode($etapas ?? []) ?>;
let etapaSelecionadaId = <?= $etapa_id ?: 'null' ?>;

function selecionarEtapa(etapaId, index) {
    etapaSelecionadaId = etapaId;
    document.getElementById('etapa_selecionada').value = etapaId;
    document.getElementById('btn-iniciar').disabled = false;

    // Visual feedback
    document.querySelectorAll('.etapa-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.querySelector('.etapa-card[data-etapa="' + etapaId + '"]').classList.add('selected');

    // Mostra atividades planejadas da etapa
    mostrarAtividadesPlanejadas(index);
}

function mostrarAtividadesPlanejadas(index) {
    const etapa = etapasDados[index];
    const container = document.getElementById('atividades-planejadas-container');
    const lista = document.getElementById('atividades-planejadas-lista');

    if (!etapa || !etapa.atividades || etapa.atividades.length === 0) {
        container.style.display = 'none';
        return;
    }

    let html = '<h6><i class="bx bx-list-check"></i> Atividades planejadas para esta etapa:</h6>';
    html += '<div style="margin-bottom: 10px;">';
    html += '<label class="atividade-planejada-item" onclick="selecionarAtividadePlanejada(null)">';
    html += '<input type="radio" name="atividade_planejada_radio" value="" checked onchange="atualizarAtividadeSelecionada(null)">';
    html += '<span>Nova atividade (não vinculada)</span>';
    html += '</label>';
    html += '</div>';

    etapa.atividades.forEach(function(atv) {
        const isConcluida = atv.status === 'concluida';
        const disabled = isConcluida ? 'disabled' : '';
        const classe = isConcluida ? 'disabled' : '';
        const icone = isConcluida ? '<i class="bx bx-check-circle" style="color: #28a745;"></i>' : '<i class="bx bx-circle" style="color: #ffc107;"></i>';

        html += '<label class="atividade-planejada-item ' + classe + '" onclick="selecionarAtividadePlanejada(' + atv.id + ')">';
        html += '<input type="radio" name="atividade_planejada_radio" value="' + atv.id + '" ' + disabled + ' onchange="atualizarAtividadeSelecionada(' + atv.id + ')">';
        html += icone + ' <span>' + atv.titulo + '</span>';
        if (isConcluida) {
            html += ' <span class="label label-success" style="margin-left: 10px;">Concluída</span>';
        }
        html += '</label>';
    });

    lista.innerHTML = html;
    container.style.display = 'block';
}

function selecionarAtividadePlanejada(atividadeId) {
    document.getElementById('obra_atividade_selecionada').value = atividadeId || '';

    // Visual feedback
    document.querySelectorAll('.atividade-planejada-item').forEach(item => {
        item.classList.remove('selecionada');
    });

    if (atividadeId) {
        const item = document.querySelector('input[value="' + atividadeId + '"]').closest('.atividade-planejada-item');
        if (item) item.classList.add('selecionada');
    }
}

function atualizarAtividadeSelecionada(atividadeId) {
    document.getElementById('obra_atividade_selecionada').value = atividadeId || '';
}

// Função de check-in específica para obra
function realizarCheckinObra() {
    const form = document.getElementById('form-checkin-obra');
    const formData = new FormData(form);

    // Validação de etapa obrigatória
    if (!formData.get('etapa_id')) {
        alert('Por favor, selecione uma etapa da obra.');
        return;
    }

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
    })
    .catch(err => {
        alert('Erro ao realizar check-in. Tente novamente.');
        console.error(err);
    });
}

// Event listener para o formulário
document.getElementById('form-checkin-obra')?.addEventListener('submit', function(e) {
    e.preventDefault();
    realizarCheckinObra();
});

// GPS
function obterLocalizacao() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                document.getElementById('info-gps').innerHTML =
                    '<i class="bx bx-check-circle" style="color: #28a745;"></i> Localização obtida';
            },
            function(error) {
                document.getElementById('info-gps').innerHTML =
                    '<i class="bx bx-error-circle" style="color: #dc3545;"></i> Erro ao obter localização';
            }
        );
    } else {
        document.getElementById('info-gps').innerHTML =
            '<i class="bx bx-error-circle" style="color: #dc3545;"></i> GPS não disponível';
    }
}

// Preview da foto
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-foto').src = e.target.result;
            document.getElementById('preview-foto').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Event listener para preview da foto
document.getElementById('foto-local')?.addEventListener('change', function() {
    previewFoto(this);
});

// Funções adicionais para controle de atividades

function pausarAtividade(id) {
    if (!confirm('Deseja pausar esta atividade?')) return;

    fetch('<?= site_url("atividades/pausar") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'atividade_id=' + id
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

function retomarAtividade(id) {
    fetch('<?= site_url("atividades/retomar") ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'atividade_id=' + id
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

function abrirModalNovaAtividade() {
    // Cria modal dinamicamente
    const modal = document.createElement('div');
    modal.id = 'modal-nova-atividade';
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="fecharModal('modal-nova-atividade')">&times;</button>
                    <h4><i class="bx bx-plus"></i> Nova Atividade</h4>
                </div>
                <div class="modal-body">
                    <form id="form-nova-atividade">
                        <div class="control-group">
                            <label>Tipo de Atividade</label>
                            <select name="tipo_id" class="input-block-level" id="novo-tipo-id">
                                <option value="">Selecione...</option>
                                <?php foreach ($tipos_atividades as $tipo): ?>
                                <option value="<?= is_object($tipo) ? $tipo->id : ($tipo['id'] ?? '') ?>"><?= htmlspecialchars(is_object($tipo) ? $tipo->nome : ($tipo['nome'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="control-group">
                            <label>Descrição</label>
                            <textarea name="descricao" class="input-block-level" rows="3" placeholder="Descreva a atividade..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn" onclick="fecharModal('modal-nova-atividade')">Cancelar</button>
                    <button class="btn btn-primary" onclick="adicionarNovaAtividade()">Adicionar</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    $('#modal-nova-atividade').modal('show');
}

function adicionarNovaAtividade() {
    const form = document.getElementById('form-nova-atividade');
    const formData = new FormData(form);
    formData.append('obra_id', '<?= $obra_id ?>');

    fetch('<?= site_url("atividades/adicionar_atividade_obra") ?>', {
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

function abrirModalFoto() {
    const modal = document.createElement('div');
    modal.id = 'modal-foto';
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="fecharModal('modal-foto')">&times;</button>
                    <h4><i class="bx bx-camera"></i> Adicionar Foto</h4>
                </div>
                <div class="modal-body">
                    <form id="form-foto" enctype="multipart/form-data">
                        <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                        <div class="control-group">
                            <label>Foto</label>
                            <input type="file" name="foto" accept="image/*" capture="environment" class="input-block-level">
                        </div>
                        <div class="control-group">
                            <label>Descrição</label>
                            <textarea name="descricao" class="input-block-level" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn" onclick="fecharModal('modal-foto')">Cancelar</button>
                    <button class="btn btn-primary" onclick="adicionarFoto()">Adicionar Foto</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    $('#modal-foto').modal('show');
}

function adicionarFoto() {
    const form = document.getElementById('form-foto');
    const formData = new FormData(form);

    fetch('<?= site_url("atividades/adicionar_foto_obra") ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            fecharModal('modal-foto');
            alert('Foto adicionada com sucesso!');
        } else {
            alert('Erro: ' + data.message);
        }
    });
}

function fecharModal(id) {
    $('#' + id).modal('hide');
    setTimeout(() => document.getElementById(id)?.remove(), 300);
}

// Modal de Checkout para Obra
function abrirModalCheckoutObra() {
    const modal = document.createElement('div');
    modal.id = 'modal-checkout-obra';
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <button type="button" class="close" onclick="fecharModal('modal-checkout-obra')" style="color: white; opacity: 0.8;">&times;</button>
                    <h4><i class="bx bx-log-out-circle"></i> Finalizar Trabalho</h4>
                </div>
                <div class="modal-body">
                    <form id="form-checkout-obra">
                        <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <label>Trabalho Concluído?</label>
                                    <select name="concluida" class="input-block-level">
                                        <option value="1">Sim, trabalho concluído</option>
                                        <option value="0">Não, preciso retornar</option>
                                    </select>
                                </div>
                                <div class="control-group">
                                    <label>Resumo do Trabalho Realizado</label>
                                    <textarea name="resumo_final" class="input-block-level" rows="4"
                                        placeholder="Descreva o que foi feito, pendências, próximos passos..."></textarea>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <label>Problemas/Pendências</label>
                                    <textarea name="pendencias" class="input-block-level" rows="4"
                                        placeholder="Algum problema encontrado? Material faltando?"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-large" onclick="fecharModal('modal-checkout-obra')">Cancelar</button>
                    <button class="btn btn-success btn-large" onclick="realizarCheckoutObra()">
                        <i class="bx bx-check-double"></i> FINALIZAR TRABALHO
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    $('#modal-checkout-obra').modal('show');
}

function realizarCheckoutObra() {
    const form = document.getElementById('form-checkout-obra');
    const formData = new FormData(form);

    fetch('<?= site_url("atividades/checkout_obra") ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Trabalho finalizado com sucesso!');
            // Redireciona para a página correta baseado no portal
            <?php if ($is_portal_tecnico ?? false): ?>
            window.location.href = '<?= site_url("tecnicos/executar_obra/" . $obra_id) ?>';
            <?php else: ?>
            window.location.href = '<?= site_url("obras_tecnico/obra/" . $obra_id) ?>';
            <?php endif; ?>
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(err => {
        alert('Erro ao finalizar trabalho.');
        console.error(err);
    });
}

<?php if ($atividade_em_andamento): ?>
// Cronômetro
let cronometroIniciado = new Date('<?= $atividade_em_andamento->hora_inicio ?>');

function atualizarCronometro() {
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

    // Atualiza duração da atividade atual
    const duracaoAtual = document.getElementById('duracao-atual');
    if (duracaoAtual) {
        duracaoAtual.innerHTML = '<strong>Duração:</strong> ' + formatado;
    }
}

setInterval(atualizarCronometro, 1000);
atualizarCronometro();
<?php endif; ?>
</script>
