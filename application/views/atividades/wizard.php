<?php
$os = $os ?? null;
$atividade_em_andamento = $atividade_em_andamento ?? null;
$atividades_lista = $atividades_lista ?? [];
$tipos_atividades = $tipos_atividades ?? [];
$checkin_realizado = $checkin_realizado ?? false;
?>

<style>
.wizard-container {
    max-width: 900px;
    margin: 0 auto;
}

.painel-hora {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
}

.painel-hora .hora-label {
    font-size: 12px;
    text-transform: uppercase;
    opacity: 0.9;
    margin-bottom: 5px;
}

.painel-hora .hora-valor {
    font-size: 36px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}

.painel-hora .cronometro {
    font-size: 48px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}

.card-atividade {
    border-left: 4px solid #28a745;
    margin-bottom: 10px;
}

.card-atividade.em-andamento {
    border-left-color: #ffc107;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.card-atividade.pausada {
    border-left-color: #6c757d;
}

.categoria-btn {
    margin: 5px;
    padding: 15px 20px;
    border-radius: 8px;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.categoria-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.categoria-btn.active {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.3);
}

.tipo-atividade-item {
    cursor: pointer;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.tipo-atividade-item:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.tipo-atividade-item.selected {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.assinatura-canvas {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    width: 100%;
    height: 200px;
    cursor: crosshair;
}

.historico-timeline {
    position: relative;
    padding-left: 30px;
}

.historico-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #28a745;
}

.timeline-item.andamento::before {
    background: #ffc107;
    animation: pulse 2s infinite;
}

.timeline-item.pausada::before {
    background: #6c757d;
}
</style>

<div class="row-fluid wizard-container">
    <div class="span12">
        <!-- Painel de Hora -->
        <div class="painel-hora">
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

        <!-- OS Info -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-file"></i></span>
                <h5>OS #<?= $os->idOs ?> - <?= htmlspecialchars($os->nomeCliente) ?></h5>
            </div>
            <div class="widget-content">
                <p><i class="bx bx-map"></i> <?= htmlspecialchars($os->rua . ', ' . $os->numero . ' - ' . $os->bairro) ?></p>
                <p><i class="bx bx-phone"></i> <?= htmlspecialchars($os->telefone) ?></p>
                <p><i class="bx bx-wrench"></i> <?= htmlspecialchars($os->descricaoProduto) ?></p>
            </div>
        </div>

        <?php if (!$atividade_em_andamento && !$checkin_realizado): ?>
        <!-- PASSO 1: CHECK-IN -->
        <div class="widget-box" id="step-checkin">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-log-in-circle"></i></span>
                <h5>Check-in - Início do Atendimento</h5>
            </div>
            <div class="widget-content">
                <form id="form-checkin">
                    <input type="hidden" name="os_id" value="<?= $os->idOs ?>">
                    <input type="hidden" name="tipo_id" value="1"> <!-- Tipo: Chegada/Check-in -->
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

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
                            <textarea name="observacoes" class="input-block-level" rows="3" placeholder="Condições do local, equipamentos encontrados, etc."></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Assinatura do Cliente (opcional)</label>
                        <div class="controls">
                            <canvas id="assinatura-cliente" class="assinatura-canvas"></canvas>
                            <button type="button" class="btn btn-small" onclick="limparAssinatura()">
                                <i class="bx bx-eraser"></i> Limpar
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success btn-large">
                            <i class="bx bx-play"></i> INICIAR ATENDIMENTO
                        </button>
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

                <!-- Botões de Ação -->
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
                        <button class="btn btn-success btn-large" onclick="abrirModalCheckout()">
                            <i class="bx bx-log-out-circle"></i> FINALIZAR ATENDIMENTO
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <!-- Atividade finalizada, pode iniciar nova ou fazer checkout -->
                <div class="alert alert-info">
                    <i class="bx bx-info-circle"></i> Nenhuma atividade em andamento.
                </div>
                <button class="btn btn-info" onclick="abrirModalNovaAtividade()">
                    <i class="bx bx-plus"></i> Iniciar Nova Atividade
                </button>
                <button class="btn btn-success" onclick="abrirModalCheckout()">
                    <i class="bx bx-log-out-circle"></i> Finalizar Atendimento
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- HISTÓRICO -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-history"></i></span>
                <h5>Histórico de Atividades</h5>
            </div>
            <div class="widget-content">
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
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: Nova Atividade -->
<div id="modal-nova-atividade" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Nova Atividade</h3>
    </div>
    <div class="modal-body">
        <form id="form-nova-atividade">
            <input type="hidden" name="os_id" value="<?= $os->idOs ?>">

            <div class="control-group">
                <label>Categoria</label>
                <div class="controls">
                    <?php foreach ($tipos_atividades as $cat_key => $cat): ?>
                    <button type="button" class="categoria-btn"
                            style="background: <?= $cat['info']['cor'] ?>20; color: <?= $cat['info']['cor'] ?>; border-color: <?= $cat['info']['cor'] ?>"
                            onclick="selecionarCategoria('<?= $cat_key ?>')"
                            data-categoria="<?= $cat_key ?>">
                        <i class="bx <?= $cat['info']['icone'] ?>"></i> <?= $cat['info']['nome'] ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="control-group">
                <label>Tipo de Atividade</label>
                <div class="controls" id="lista-tipos">
                    <p class="text-muted">Selecione uma categoria acima</p>
                </div>
            </div>

            <div class="control-group">
                <label>Equipamento/Local</label>
                <div class="controls">
                    <input type="text" name="equipamento" class="input-block-level"
                           placeholder="Ex: Câmera IP - Frente do prédio">
                </div>
            </div>

            <div class="control-group">
                <label>Descrição Técnica</label>
                <div class="controls">
                    <textarea name="descricao" class="input-block-level" rows="3"
                              placeholder="Detalhes da atividade a ser realizada..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label>Prioridade</label>
                <div class="controls">
                    <select name="prioridade" class="input-block-level">
                        <option value="baixa">Baixa</option>
                        <option value="normal" selected>Normal</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="iniciarAtividade()">Iniciar Atividade</button>
    </div>
</div>

<!-- MODAL: Finalizar Atividade -->
<div id="modal-finalizar" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Finalizar Atividade</h3>
    </div>
    <div class="modal-body">
        <form id="form-finalizar">
            <input type="hidden" name="atividade_id" id="finalizar-atividade-id">

            <div class="control-group">
                <label>Status da Atividade</label>
                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="concluida" value="1" checked onchange="toggleMotivoNaoConcluida()">
                        <i class="bx bx-check-circle" style="color: #28a745;"></i> Concluída com sucesso
                    </label>
                    <label class="radio">
                        <input type="radio" name="concluida" value="0" onchange="toggleMotivoNaoConcluida()">
                        <i class="bx bx-x-circle" style="color: #dc3545;"></i> Não concluída
                    </label>
                </div>
            </div>

            <div class="control-group" id="motivo-nao-concluida" style="display: none;">
                <label>Motivo (não concluída)</label>
                <div class="controls">
                    <textarea name="motivo_nao_concluida" class="input-block-level" rows="2"
                              placeholder="Por que não foi possível concluir?"></textarea>
                </div>
            </div>

            <div class="control-group">
                <label>Problemas Encontrados</label>
                <div class="controls">
                    <textarea name="problemas_encontrados" class="input-block-level" rows="2"
                              placeholder="Descreva problemas encontrados durante a execução..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label>Solução Aplicada</label>
                <div class="controls">
                    <textarea name="solucao_aplicada" class="input-block-level" rows="2"
                              placeholder="O que foi feito para resolver..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label>Observações Finais</label>
                <div class="controls">
                    <textarea name="observacoes_final" class="input-block-level" rows="2"
                              placeholder="Observações adicionais..."></textarea>
                </div>
            </div>
        </form>

        <div class="alert alert-info">
            <strong>Hora de Início:</strong> <span id="info-hora-inicio">--:--</span><br>
            <strong>Hora de Fim:</strong> <span id="info-hora-fim">--:--</span><br>
            <strong>Duração:</strong> <span id="info-duracao">Calculando...</span>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" onclick="finalizarAtividade()">
            <i class="bx bx-stop"></i> Finalizar Atividade
        </button>
    </div>
</div>

<!-- MODAL: Check-out -->
<div id="modal-checkout" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Finalizar Atendimento - Check-out</h3>
    </div>
    <div class="modal-body">
        <form id="form-checkout">
            <input type="hidden" name="os_id" value="<?= $os->idOs ?>">

            <div class="control-group">
                <label>Foto de Finalização</label>
                <div class="controls">
                    <input type="file" name="foto" accept="image/*" capture="environment">
                </div>
            </div>

            <div class="control-group">
                <label>Resumo do Trabalho Realizado</label>
                <div class="controls">
                    <textarea name="resumo_final" class="input-block-level" rows="4"
                              placeholder="Descreva o trabalho realizado, resultados alcançados..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label>Pendências/Observações</label>
                <div class="controls">
                    <textarea name="pendencias" class="input-block-level" rows="2"
                              placeholder="Há algo pendente? Equipamentos que faltam, retornos necessários..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label>Atividade foi realizada?</label>
                <div class="controls">
                    <label class="radio inline">
                        <input type="radio" name="concluida" value="1" checked> Sim
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="concluida" value="0"> Não
                    </label>
                </div>
            </div>

            <div class="control-group">
                <label>Assinatura do Cliente</label>
                <div class="controls">
                    <canvas id="assinatura-cliente-checkout" class="assinatura-canvas"></canvas>
                    <button type="button" class="btn btn-small" onclick="limparAssinaturaCheckout()">
                        <i class="bx bx-eraser"></i> Limpar
                    </button>
                </div>
            </div>
        </form>

        <div class="alert alert-success">
            <h4>Resumo do Atendimento</h4>
            <div id="resumo-atendimento">
                Calculando...
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-success btn-large" onclick="realizarCheckout()">
            <i class="bx bx-check-double"></i> FINALIZAR ATENDIMENTO
        </button>
    </div>
</div>

<!-- MODAL: Foto -->
<div id="modal-foto" class="modal hide fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Adicionar Foto</h3>
    </div>
    <div class="modal-body">
        <form id="form-foto" enctype="multipart/form-data">
            <input type="hidden" name="atividade_id" id="foto-atividade-id" value="<?= $atividade_em_andamento ? $atividade_em_andamento->idAtividade : '' ?>">

            <div class="control-group">
                <label>Tipo de Foto</label>
                <div class="controls">
                    <select name="tipo_foto" class="input-block-level">
                        <option value="chegada">Chegada no Local</option>
                        <option value="execucao" selected>Execução do Serviço</option>
                        <option value="conclusao">Conclusão</option>
                        <option value="problema">Problema Encontrado</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label>Foto</label>
                <div class="controls">
                    <input type="file" name="foto" accept="image/*" capture="environment" class="input-block-level" required>
                </div>
            </div>

            <div class="control-group">
                <label>Descrição</label>
                <div class="controls">
                    <input type="text" name="descricao" class="input-block-level" placeholder="Descreva a foto...">
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="adicionarFoto()">Adicionar Foto</button>
    </div>
</div>

<script>
// ========== VARIÁVEIS GLOBAIS ==========
let atividadeAtual = <?= $atividade_em_andamento ? json_encode($atividade_em_andamento) : 'null' ?>;
let cronometroInterval = null;
let tempoInicio = atividadeAtual ? new Date(atividadeAtual.hora_inicio) : null;

// ========== INICIALIZAÇÃO ==========
$(document).ready(function() {
    // Inicia cronômetro se houver atividade em andamento
    if (atividadeAtual && atividadeAtual.status === 'em_andamento') {
        iniciarCronometro();
    }

    // Previne envio do form de check-in
    $('#form-checkin').on('submit', function(e) {
        e.preventDefault();
        realizarCheckin();
    });

    // Preview da foto
    $('#foto-local').on('change', function() {
        previewFoto(this, '#preview-foto');
    });

    // Inicializa canvas de assinatura
    inicializarAssinatura('assinatura-cliente');
    inicializarAssinatura('assinatura-cliente-checkout');

    // Carrega tipos de atividades para o modal
    window.tiposAtividades = <?= json_encode($tipos_atividades) ?>;
});

// ========== GPS ==========
function obterLocalizacao() {
    if (!navigator.geolocation) {
        alert('Geolocalização não suportada neste navegador.');
        return;
    }

    $('#info-gps').html('<i class="bx bx-loader-alt bx-spin"></i> Obtendo localização...');

    navigator.geolocation.getCurrentPosition(
        function(position) {
            $('#latitude').val(position.coords.latitude);
            $('#longitude').val(position.coords.longitude);
            $('#info-gps').html(
                '<i class="bx bx-check" style="color: #28a745;"></i> ' +
                'Localização obtida: ' + position.coords.latitude.toFixed(4) + ', ' + position.coords.longitude.toFixed(4)
            );
        },
        function(error) {
            $('#info-gps').html('<span style="color: #dc3545;"><i class="bx bx-x"></i> Erro: ' + error.message + '</span>');
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

// ========== ASSINATURA ==========
let assinaturaCanvas = {};

function inicializarAssinatura(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;

    let desenhando = false;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    function comecar(e) {
        desenhando = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        e.preventDefault();
    }

    function mover(e) {
        if (!desenhando) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        e.preventDefault();
    }

    function terminar() {
        desenhando = false;
    }

    canvas.addEventListener('mousedown', comecar);
    canvas.addEventListener('mousemove', mover);
    canvas.addEventListener('mouseup', terminar);
    canvas.addEventListener('mouseleave', terminar);

    canvas.addEventListener('touchstart', comecar);
    canvas.addEventListener('touchmove', mover);
    canvas.addEventListener('touchend', terminar);

    assinaturaCanvas[canvasId] = { canvas, ctx };
}

function limparAssinatura() {
    const { canvas, ctx } = assinaturaCanvas['assinatura-cliente'];
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function limparAssinaturaCheckout() {
    const { canvas, ctx } = assinaturaCanvas['assinatura-cliente-checkout'];
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function obterAssinaturaBase64(canvasId) {
    const { canvas } = assinaturaCanvas[canvasId];
    // Verifica se o canvas tem desenho
    const ctx = canvas.getContext('2d');
    const dados = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
    let temDesenho = false;
    for (let i = 3; i < dados.length; i += 4) {
        if (dados[i] > 0) { temDesenho = true; break; }
    }
    return temDesenho ? canvas.toDataURL() : null;
}

// ========== CHECK-IN ==========
function realizarCheckin() {
    const form = $('#form-checkin')[0];
    const formData = new FormData(form);

    // Adiciona assinatura
    const assinatura = obterAssinaturaBase64('assinatura-cliente');
    if (assinatura) {
        formData.append('assinatura_cliente', assinatura);
    }

    $.ajax({
        url: '<?= site_url("atividades/checkin") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('button[type="submit"]').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Processando...');
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                location.reload();
            } else {
                alert('Erro: ' + data.message);
                $('button[type="submit"]').prop('disabled', false).html('<i class="bx bx-play"></i> INICIAR ATENDIMENTO');
            }
        },
        error: function() {
            alert('Erro ao realizar check-in. Tente novamente.');
            $('button[type="submit"]').prop('disabled', false).html('<i class="bx bx-play"></i> INICIAR ATENDIMENTO');
        }
    });
}

// ========== CRONÔMETRO ==========
function iniciarCronometro() {
    if (cronometroInterval) clearInterval(cronometroInterval);

    cronometroInterval = setInterval(function() {
        if (!tempoInicio) return;

        const agora = new Date();
        const diff = agora - tempoInicio;

        const horas = Math.floor(diff / 3600000);
        const minutos = Math.floor((diff % 3600000) / 60000);
        const segundos = Math.floor((diff % 60000) / 1000);

        $('#cronometro').text(
            String(horas).padStart(2, '0') + ':' +
            String(minutos).padStart(2, '0') + ':' +
            String(segundos).padStart(2, '0')
        );

        // Atualiza hora fim estimada (apenas visual)
        $('#hora-fim').text('--:--');
    }, 1000);
}

function pararCronometro() {
    if (cronometroInterval) {
        clearInterval(cronometroInterval);
        cronometroInterval = null;
    }
}

// ========== NOVA ATIVIDADE ==========
function abrirModalNovaAtividade() {
    $('#modal-nova-atividade').modal('show');
}

function selecionarCategoria(categoria) {
    $('.categoria-btn').removeClass('active');
    $(`.categoria-btn[data-categoria="${categoria}"]`).addClass('active');

    const tipos = window.tiposAtividades[categoria];
    let html = '';

    if (tipos && tipos.tipos) {
        tipos.tipos.forEach(function(tipo) {
            html += `
                <div class="tipo-atividade-item" onclick="selecionarTipo(${tipo.idTipo}, this)" data-id="${tipo.idTipo}">
                    <i class="bx ${tipo.icone}" style="color: ${tipo.cor}"></i>
                    <strong>${tipo.nome}</strong>
                    ${tipo.duracao_estimada ? `<small class="text-muted">(~${tipo.duracao_estimada}min)</small>` : ''}
                    <br>
                    <small class="text-muted">${tipo.descricao || ''}</small>
                </div>
            `;
        });
    }

    $('#lista-tipos').html(html);
}

let tipoSelecionado = null;

function selecionarTipo(id, elemento) {
    tipoSelecionado = id;
    $('.tipo-atividade-item').removeClass('selected');
    $(elemento).addClass('selected');
}

function iniciarAtividade() {
    if (!tipoSelecionado) {
        alert('Selecione um tipo de atividade.');
        return;
    }

    const formData = $('#form-nova-atividade').serialize();
    const data = formData + '&tipo_id=' + tipoSelecionado;

    $.ajax({
        url: '<?= site_url("atividades/iniciar_atividade") ?>',
        type: 'POST',
        data: data,
        beforeSend: function() {
            $('#modal-nova-atividade .btn-primary').prop('disabled', true).text('Iniciando...');
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                location.reload();
            } else {
                alert('Erro: ' + result.message);
                $('#modal-nova-atividade .btn-primary').prop('disabled', false).text('Iniciar Atividade');
            }
        },
        error: function() {
            alert('Erro ao iniciar atividade.');
            $('#modal-nova-atividade .btn-primary').prop('disabled', false).text('Iniciar Atividade');
        }
    });
}

// ========== PAUSAR/RETOMAR ==========
function pausarAtividade(id) {
    const motivo = prompt('Motivo da pausa (opcional):');

    $.ajax({
        url: '<?= site_url("atividades/pausar") ?>',
        type: 'POST',
        data: { atividade_id: id, motivo: motivo },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                location.reload();
            } else {
                alert('Erro: ' + result.message);
            }
        }
    });
}

function retomarAtividade(id) {
    $.ajax({
        url: '<?= site_url("atividades/retomar") ?>',
        type: 'POST',
        data: { atividade_id: id },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                location.reload();
            } else {
                alert('Erro: ' + result.message);
            }
        }
    });
}

// ========== FINALIZAR ATIVIDADE ==========
function abrirModalFinalizar(id) {
    $('#finalizar-atividade-id').val(id);

    // Calcula informações de tempo
    const atividade = atividadeAtual;
    if (atividade) {
        const inicio = new Date(atividade.hora_inicio);
        const agora = new Date();
        const diff = Math.floor((agora - inicio) / 60000); // minutos

        $('#info-hora-inicio').text(inicio.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}));
        $('#info-hora-fim').text(agora.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}));
        $('#info-duracao').text(`${Math.floor(diff / 60)}h ${diff % 60}min`);
    }

    $('#modal-finalizar').modal('show');
}

function toggleMotivoNaoConcluida() {
    const naoConcluida = $('input[name="concluida"]:checked').val() === '0';
    $('#motivo-nao-concluida')[naoConcluida ? 'show' : 'hide']();
}

function finalizarAtividade() {
    const formData = $('#form-finalizar').serialize();

    $.ajax({
        url: '<?= site_url("atividades/finalizar_atividade") ?>',
        type: 'POST',
        data: formData,
        beforeSend: function() {
            $('#modal-finalizar .btn-danger').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Finalizando...');
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                pararCronometro();
                location.reload();
            } else {
                alert('Erro: ' + result.message);
                $('#modal-finalizar .btn-danger').prop('disabled', false).html('<i class="bx bx-stop"></i> Finalizar Atividade');
            }
        },
        error: function() {
            alert('Erro ao finalizar atividade.');
            $('#modal-finalizar .btn-danger').prop('disabled', false).html('<i class="bx bx-stop"></i> Finalizar Atividade');
        }
    });
}

// ========== CHECKOUT ==========
function abrirModalCheckout() {
    // Calcula resumo do atendimento
    calcularResumoAtendimento();
    $('#modal-checkout').modal('show');
}

function calcularResumoAtendimento() {
    const atividades = <?= json_encode($atividades_lista) ?>;
    let tempoTotal = 0;
    let atividadesConcluidas = 0;
    let atividadesNaoConcluidas = 0;

    atividades.forEach(function(atv) {
        if (atv.duracao_minutos) {
            tempoTotal += parseInt(atv.duracao_minutos);
        }
        if (atv.concluida == 1) {
            atividadesConcluidas++;
        } else if (atv.status === 'finalizada') {
            atividadesNaoConcluidas++;
        }
    });

    const horas = Math.floor(tempoTotal / 60);
    const minutos = tempoTotal % 60;

    const html = `
        <p><strong>Total de Atividades:</strong> ${atividades.length}</p>
        <p><strong>Concluídas:</strong> <span style="color: #28a745;">${atividadesConcluidas}</span></p>
        <p><strong>Não Concluídas:</strong> <span style="color: #dc3545;">${atividadesNaoConcluidas}</span></p>
        <p><strong>Tempo Total:</strong> ${horas}h ${minutos}min</p>
    `;

    $('#resumo-atendimento').html(html);
}

function realizarCheckout() {
    const form = $('#form-checkout')[0];
    const formData = new FormData(form);

    // Adiciona assinatura
    const assinatura = obterAssinaturaBase64('assinatura-cliente-checkout');
    if (assinatura) {
        formData.append('assinatura_cliente_saida', assinatura);
    }

    $.ajax({
        url: '<?= site_url("atividades/checkout") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#modal-checkout .btn-success').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Finalizando...');
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                alert('Atendimento finalizado com sucesso!');
                window.location.href = '<?= site_url("atividades") ?>';
            } else {
                alert('Erro: ' + result.message);
                $('#modal-checkout .btn-success').prop('disabled', false).html('<i class="bx bx-check-double"></i> FINALIZAR ATENDIMENTO');
            }
        },
        error: function() {
            alert('Erro ao finalizar atendimento.');
            $('#modal-checkout .btn-success').prop('disabled', false).html('<i class="bx bx-check-double"></i> FINALIZAR ATENDIMENTO');
        }
    });
}

// ========== FOTO ==========
function abrirModalFoto() {
    $('#modal-foto').modal('show');
}

function adicionarFoto() {
    const form = $('#form-foto')[0];
    const formData = new FormData(form);

    $.ajax({
        url: '<?= site_url("atividades/adicionar_foto") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#modal-foto .btn-primary').prop('disabled', true).text('Adicionando...');
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                $('#modal-foto').modal('hide');
                alert('Foto adicionada com sucesso!');
                $('#form-foto')[0].reset();
            } else {
                alert('Erro: ' + result.message);
            }
            $('#modal-foto .btn-primary').prop('disabled', false).text('Adicionar Foto');
        },
        error: function() {
            alert('Erro ao adicionar foto.');
            $('#modal-foto .btn-primary').prop('disabled', false).text('Adicionar Foto');
        }
    });
}

// ========== UTILS ==========
function previewFoto(input, previewSelector) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $(previewSelector).attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
