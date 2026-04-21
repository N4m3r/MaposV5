<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- WIZARD DE ATENDIMENTO - PADRAO MAPOS -->
<style>
    /* Estilos essenciais apenas para funcionalidades do wizard */
    .wizard-atendimento-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        overflow-y: auto;
    }
    .wizard-atendimento-container {
        min-height: 100%;
        padding: 20px;
    }
    .wizard-atendimento-content {
        max-width: 900px;
        margin: 0 auto;
    }
    .wizard-etapa-select {
        cursor: pointer;
        padding: 15px;
        border: 2px solid #ddd;
        margin-bottom: 10px;
        border-radius: 4px;
        background: #f9f9f9;
        transition: all 0.2s;
    }
    .wizard-etapa-select:hover {
        border-color: #005580;
        background: #f0f0f0;
    }
    .wizard-etapa-select.selecionada {
        border-color: #005580;
        background: #005580;
        color: white;
    }
    .wizard-etapa-select.selecionada .label {
        background: white;
        color: #005580;
    }
    .wizard-timer-display {
        font-size: 48px;
        font-weight: bold;
        text-align: center;
        font-family: 'Courier New', monospace;
        padding: 20px;
        background: #2c3e50;
        color: white;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .wizard-btn-close {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
    }
    .etapa-item {
        border-left: 4px solid #ddd;
        margin-bottom: 15px;
    }
    .etapa-item.pendente { border-left-color: #f39c12; }
    .etapa-item.em-andamento { border-left-color: #3498db; }
    .etapa-item.concluida { border-left-color: #27ae60; }
</style>

<!-- HEADER DA OBRA -->
<div class="widget-box">
    <div class="widget-title" style="background: #2c3e50; color: white;">
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
                <p><strong>Cliente:</strong> <?= htmlspecialchars($obra->cliente_nome ?? 'Não definido') ?></p>
                <p><strong>Endereço:</strong> <?= htmlspecialchars($obra->endereco ?? 'Não definido') ?></p>
                <p><strong>Progresso Geral:</strong> <?= $obra->percentual_concluido ?? 0 ?>%</p>
            </div>
            <div class="span4" style="text-align: right;">
                <?php if (!empty($wizard_em_andamento)): ?>
                    <div class="alert alert-warning">
                        <i class="bx bx-time"></i> Atividade em andamento<br>
                        <strong><?= htmlspecialchars($wizard_em_andamento->etapa_nome ?? 'Geral') ?></strong><br>
                        <small>Iniciado às <?= date('H:i', strtotime($wizard_em_andamento->hora_inicio)) ?></small>
                    </div>
                    <button class="btn btn-success btn-large" onclick="WizardAtendimento.continuar(<?= $wizard_em_andamento->id ?>, '<?= $wizard_em_andamento->hora_inicio ?>', '<?= htmlspecialchars($wizard_em_andamento->etapa_nome ?? 'Atividade geral') ?>')">
                        <i class="bx bx-play"></i> CONTINUAR ATENDIMENTO
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary btn-large" onclick="WizardAtendimento.iniciar()">
                        <i class="bx bx-log-in"></i> INICIAR ATENDIMENTO
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ESTATISTICAS -->
<div class="row-fluid">
    <div class="span3">
        <div class="widget-box">
            <div class="widget-content" style="text-align: center;">
                <h2 style="margin: 0; color: #3498db;"><?= count($etapas) ?></h2>
                <small>Etapas Totais</small>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box">
            <div class="widget-content" style="text-align: center;">
                <h2 style="margin: 0; color: #27ae60;"><?= count(array_filter($etapas, function($e) { return ($e->status ?? '') === 'concluida'; })) ?></h2>
                <small>Etapas Concluídas</small>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box">
            <div class="widget-content" style="text-align: center;">
                <h2 style="margin: 0; color: #f39c12;"><?= count($minhas_atividades) ?></h2>
                <small>Minhas Atividades</small>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="widget-box">
            <div class="widget-content" style="text-align: center;">
                <h2 style="margin: 0; color: #e74c3c;"><?= count(array_filter($minhas_os, function($os) { return ($os->status ?? '') !== 'Finalizada'; })) ?></h2>
                <small>OS em Aberto</small>
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
                    $statusBtn = '';
                    switch($etapa->status ?? 'pendente') {
                        case 'concluida':
                            $statusClass = 'concluida';
                            $statusLabel = '<span class="label label-success">Concluída</span>';
                            break;
                        case 'em-andamento':
                            $statusClass = 'em-andamento';
                            $statusLabel = '<span class="label label-info">Em Andamento</span>';
                            break;
                        default:
                            $statusClass = 'pendente';
                            $statusLabel = '<span class="label label-warning">Pendente</span>';
                    }
                ?>
                <div class="widget-box etapa-item <?= $statusClass ?>">
                    <div class="widget-content">
                        <div class="row-fluid">
                            <div class="span8">
                                <h5 style="margin: 0 0 10px 0;"><?= htmlspecialchars($etapa->nome) ?></h5>
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
                                <h6>Atividades:</h6>
                                <ul>
                                    <?php foreach ($atividades_por_etapa[$etapa->id] as $ativ): ?>
                                        <li>
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
        <table class="table table-bordered">
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
                        <td><?= date('H:i', strtotime($ativ->hora_inicio)) ?> - <?= $ativ->hora_fim ? date('H:i', strtotime($ativ->hora_fim)) : '--:--' ?></td>
                        <td>
                            <?php if ($ativ->status === 'concluida'): ?>
                                <span class="label label-success">Concluída</span>
                            <?php elseif ($ativ->status === 'em_andamento'): ?>
                                <span class="label label-info">Em Andamento</span>
                            <?php else: ?>
                                <span class="label label-warning">Pendente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- BOTAO FECHAR WIZARD -->
<button class="btn btn-danger wizard-btn-close" onclick="WizardAtendimento.fechar()" style="display: none;" id="btnFecharWizard">
    <i class="bx bx-x"></i> Fechar
</button>

<!-- WIZARD CONTAINER -->
<div id="wizardAtendimento" class="wizard-atendimento-overlay">
    <div class="wizard-atendimento-container">
        <div class="wizard-atendimento-content">

            <!-- STEP 1: CHECK-IN -->
            <div id="wizardStepCheckin" style="display: none;">

                <!-- HEADER -->
                <div class="widget-box" style="margin-bottom: 20px;">
                    <div class="widget-title" style="background: #2c3e50; color: white;">
                        <span class="icon"><i class="bx bx-log-in"></i></span>
                        <h5>Check-in de Entrada</h5>
                    </div>
                    <div class="widget-content" style="text-align: center; padding: 30px;">
                        <i class="bx bx-camera" style="font-size: 48px; color: #2c3e50; display: block; margin-bottom: 10px;"></i>
                        <h3>Iniciar Atendimento</h3>
                        <p class="help-block">Selecione a etapa e registre sua entrada</p>
                    </div>
                </div>

                <!-- SELECAO DE ETAPA -->
                <div class="widget-box" style="margin-bottom: 20px;">
                    <div class="widget-title">
                        <span class="icon"><i class="bx bx-list-check"></i></span>
                        <h5>1. Selecione a Etapa *</h5>
                    </div>
                    <div class="widget-content">
                        <div class="row-fluid">
                            <?php foreach ($etapas as $etapa): ?>
                            <div class="span4">
                                <div class="wizard-etapa-select" data-etapa-id="<?= $etapa->id ?>" onclick="WizardAtendimento.selecionarEtapa(<?= $etapa->id ?>, '<?= htmlspecialchars($etapa->nome) ?>', this)">
                                    <strong><?= htmlspecialchars($etapa->nome) ?></strong><br>
                                    <span class="label label-info"><?= isset($etapa->percentual_concluido) ? $etapa->percentual_concluido : 0 ?>% concluído</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="checkinEtapaId" value="">
                    </div>
                </div>

                <!-- FOTO DO CHECKIN -->
                <div class="widget-box" style="margin-bottom: 20px;">
                    <div class="widget-title">
                        <span class="icon"><i class="bx bx-camera"></i></span>
                        <h5>2. Foto do Local (Opcional)</h5>
                    </div>
                    <div class="widget-content" style="text-align: center;">
                        <div style="border: 2px dashed #ddd; padding: 30px; cursor: pointer;" onclick="document.getElementById('checkinFoto').click()">
                            <i class="bx bx-camera" style="font-size: 40px; color: #999; margin-bottom: 10px;"></i>
                            <p>Clique para adicionar foto do local</p>
                            <input type="file" id="checkinFoto" accept="image/*" capture="environment" style="display: none;" onchange="WizardAtendimento.previewFoto(this, 'checkinFotoPreview')">
                        </div>
                        <img id="checkinFotoPreview" style="max-width: 100%; margin-top: 15px; display: none;">
                    </div>
                </div>

                <!-- OBSERVACOES -->
                <div class="widget-box" style="margin-bottom: 20px;">
                    <div class="widget-title">
                        <span class="icon"><i class="bx bx-note"></i></span>
                        <h5>3. Observações</h5>
                    </div>
                    <div class="widget-content">
                        <textarea id="checkinObservacoes" rows="3" class="span12" placeholder="Condições do local, trabalho a ser realizado..."></textarea>
                    </div>
                </div>

                <!-- BOTAO INICIAR -->
                <button id="btnIniciarCheckin" class="btn btn-success btn-large btn-block" onclick="WizardAtendimento.realizarCheckin()" disabled>
                    <i class="bx bx-play"></i> INICIAR ATENDIMENTO
                </button>

            </div>

            <!-- STEP 2: EXECUCAO EM ANDAMENTO -->
            <div id="wizardStepExecucao" style="display: none;">

                <!-- TIMER -->
                <div class="wizard-timer-display">
                    <i class="bx bx-time"></i>
                    <div id="timerExecucao">00:00:00</div>
                    <small style="font-size: 14px;">Tempo de Execução</small>
                </div>

                <!-- INFO ETAPA -->
                <div class="alert alert-info" style="margin-bottom: 20px;">
                    <i class="bx bx-info-circle"></i>
                    <strong>Etapa em Execução:</strong> <span id="etapaEmExecucao">--</span>
                </div>

                <!-- BOTOES DE ACAO -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span3">
                        <button class="btn btn-block btn-large" onclick="WizardAtendimento.abrirModalAtividade()" style="padding: 20px;">
                            <i class="bx bx-plus" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            <strong>Registrar Atividade</strong>
                        </button>
                    </div>
                    <div class="span3">
                        <button class="btn btn-block btn-large btn-info" onclick="WizardAtendimento.abrirModalFoto()" style="padding: 20px;">
                            <i class="bx bx-camera" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            <strong>Adicionar Foto</strong>
                        </button>
                    </div>
                    <div class="span3">
                        <button class="btn btn-block btn-large btn-warning" onclick="WizardAtendimento.pausarAtendimento()" style="padding: 20px;">
                            <i class="bx bx-pause" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            <strong>Pausar</strong>
                        </button>
                    </div>
                    <div class="span3">
                        <button class="btn btn-block btn-large btn-success" onclick="WizardAtendimento.abrirModalCheckout()" style="padding: 20px;">
                            <i class="bx bx-check" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            <strong>Finalizar</strong>
                        </button>
                    </div>
                </div>

                <!-- HISTORICO DE ATIVIDADES -->
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="bx bx-history"></i></span>
                        <h5>Atividades Registradas</h5>
                    </div>
                    <div class="widget-content" id="listaAtividadesRegistradas">
                        <p class="help-block" style="text-align: center; padding: 20px;">
                            <i class="bx bx-info-circle"></i> Nenhuma atividade registrada ainda.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- MODAL REGISTRAR ATIVIDADE -->
<div id="modalAtividade" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10001;">
    <div style="width: 90%; max-width: 500px; margin: 50px auto; background: white; border-radius: 4px;">
        <div class="widget-title" style="margin: 0;">
            <span class="icon"><i class="bx bx-plus"></i></span>
            <h5>Registrar Atividade</h5>
            <div class="buttons">
                <button class="btn btn-mini" onclick="WizardAtendimento.fecharModal('modalAtividade')">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>
        <div class="widget-content">
            <div class="control-group">
                <label class="control-label">Tipo de Atividade *</label>
                <div class="controls">
                    <select id="atividadeTipoId" class="span12">
                        <option value="">Selecione...</option>
                        <?php foreach ($tipos_atividades as $tipo): ?>
                        <option value="<?= is_object($tipo) ? $tipo->id : ($tipo['id'] ?? '') ?>"><?= htmlspecialchars(is_object($tipo) ? $tipo->nome : ($tipo['nome'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Descrição</label>
                <div class="controls">
                    <textarea id="atividadeDescricao" rows="3" class="span12" placeholder="Descreva o que foi feito..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Status</label>
                <div class="controls">
                    <label class="radio inline">
                        <input type="radio" name="atividadeStatus" value="executada" checked> Executada
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="atividadeStatus" value="pendente"> Pendente
                    </label>
                </div>
            </div>

            <div class="form-actions" style="text-align: right; margin-bottom: 0;">
                <button class="btn" onclick="WizardAtendimento.fecharModal('modalAtividade')">Cancelar</button>
                <button class="btn btn-primary" onclick="WizardAtendimento.salvarAtividade()">
                    <i class="bx bx-save"></i> Salvar Atividade
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADICIONAR FOTO -->
<div id="modalFoto" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10001;">
    <div style="width: 90%; max-width: 500px; margin: 50px auto; background: white; border-radius: 4px;">
        <div class="widget-title" style="margin: 0;">
            <span class="icon"><i class="bx bx-camera"></i></span>
            <h5>Adicionar Foto</h5>
            <div class="buttons">
                <button class="btn btn-mini" onclick="WizardAtendimento.fecharModal('modalFoto')">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>
        <div class="widget-content">
            <div class="control-group">
                <label class="control-label">Foto</label>
                <div class="controls">
                    <input type="file" id="fotoFile" accept="image/*" capture="environment" class="span12">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Descrição</label>
                <div class="controls">
                    <textarea id="fotoDescricao" rows="2" class="span12" placeholder="Descrição da foto..."></textarea>
                </div>
            </div>

            <div class="form-actions" style="text-align: right; margin-bottom: 0;">
                <button class="btn" onclick="WizardAtendimento.fecharModal('modalFoto')">Cancelar</button>
                <button class="btn btn-success" onclick="WizardAtendimento.salvarFoto()">
                    <i class="bx bx-camera"></i> Salvar Foto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CHECKOUT / FINALIZAR -->
<div id="modalCheckout" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10001;">
    <div style="width: 90%; max-width: 550px; margin: 50px auto; background: white; border-radius: 4px;">
        <div class="widget-title" style="margin: 0; background: #27ae60;">
            <span class="icon"><i class="bx bx-check-double"></i></span>
            <h5 style="color: white;">Finalizar Atendimento</h5>
            <div class="buttons">
                <button class="btn btn-mini" onclick="WizardAtendimento.fecharModal('modalCheckout')" style="background: rgba(255,255,255,0.3); border: none; color: white;">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>
        <div class="widget-content">

            <!-- RESUMO DO TEMPO -->
            <div class="alert alert-info" style="margin-bottom: 20px;">
                <h5 style="margin-top: 0;"><i class="bx bx-time"></i> Resumo do Atendimento</h5>
                <div style="display: flex; justify-content: space-between;">
                    <span>Início:</span>
                    <strong id="checkoutHoraInicio">--:--</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Tempo Total:</span>
                    <strong id="checkoutTempoTotal" style="font-size: 18px;">--:--</strong>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Trabalho Concluído? *</label>
                <div class="controls">
                    <select id="checkoutConcluido" class="span12">
                        <option value="1">Sim, trabalho concluído</option>
                        <option value="0">Não, preciso retornar</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Resumo do Trabalho</label>
                <div class="controls">
                    <textarea id="checkoutResumo" rows="3" class="span12" placeholder="Descreva o que foi feito, serviços realizados..."></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Problemas/Pendências</label>
                <div class="controls">
                    <textarea id="checkoutPendencias" rows="2" class="span12" placeholder="Algum problema encontrado? Material faltando?"></textarea>
                </div>
            </div>

            <!-- FOTO DE SAIDA -->
            <div class="control-group">
                <label class="control-label">Foto de Saída (Opcional)</label>
                <div class="controls">
                    <input type="file" id="checkoutFoto" accept="image/*" capture="environment" class="span12">
                </div>
            </div>

            <div class="form-actions" style="text-align: right; margin-bottom: 0;">
                <button class="btn" onclick="WizardAtendimento.fecharModal('modalCheckout')">Cancelar</button>
                <button class="btn btn-success btn-large" onclick="WizardAtendimento.realizarCheckout()">
                    <i class="bx bx-check-double"></i> FINALIZAR ATENDIMENTO
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// WIZARD DE ATENDIMENTO INTEGRADO - PADRAO MAPOS
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

    // INICIAR WIZARD (ABRIR MODAL)
    iniciar: function() {
        document.getElementById('wizardAtendimento').style.display = 'block';
        document.getElementById('btnFecharWizard').style.display = 'block';
        document.body.style.overflow = 'hidden';
        this.mostrarStep('checkin');
    },

    // INICIAR COM ETAPA PRE-SELECIONADA
    iniciarComEtapa: function(etapaId, etapaNome) {
        this.iniciar();
        // Selecionar a etapa automaticamente
        setTimeout(function() {
            var el = document.querySelector('[data-etapa-id="' + etapaId + '"]');
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

        document.getElementById('etapaEmExecucao').textContent = etapaNome || 'Atividade geral';
        document.getElementById('wizardAtendimento').style.display = 'block';
        document.getElementById('btnFecharWizard').style.display = 'block';
        document.body.style.overflow = 'hidden';
        this.mostrarStep('execucao');
        this.iniciarTimer();
    },

    fechar: function() {
        document.getElementById('wizardAtendimento').style.display = 'none';
        document.getElementById('btnFecharWizard').style.display = 'none';
        document.body.style.overflow = '';
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    },

    mostrarStep: function(step) {
        document.getElementById('wizardStepCheckin').style.display = step === 'checkin' ? 'block' : 'none';
        document.getElementById('wizardStepExecucao').style.display = step === 'execucao' ? 'block' : 'none';
    },

    // CHECKIN
    selecionarEtapa: function(etapaId, etapaNome, elemento) {
        this.etapaSelecionadaId = etapaId;
        this.etapaSelecionadaNome = etapaNome;
        document.getElementById('checkinEtapaId').value = etapaId;

        // Remover seleção anterior
        document.querySelectorAll('.wizard-etapa-select').forEach(function(el) {
            el.classList.remove('selecionada');
        });
        // Adicionar seleção atual
        elemento.classList.add('selecionada');

        // Habilitar botão
        document.getElementById('btnIniciarCheckin').disabled = false;
    },

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

        var btn = document.getElementById('btnIniciarCheckin');
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
                document.getElementById('etapaEmExecucao').textContent = self.etapaSelecionadaNome;
                self.mostrarStep('execucao');
                self.iniciarTimer();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
                btn.innerHTML = '<i class="bx bx-play"></i> INICIAR ATENDIMENTO';
                btn.disabled = false;
            }
        })
        .catch(function(err) {
            alert('Erro ao iniciar: ' + err.message);
            console.error(err);
            btn.innerHTML = '<i class="bx bx-play"></i> INICIAR ATENDIMENTO';
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

            document.getElementById('timerExecucao').textContent = formatado;
        }, 1000);
    },

    // MODAIS
    abrirModalAtividade: function() {
        document.getElementById('modalAtividade').style.display = 'block';
    },

    abrirModalFoto: function() {
        document.getElementById('modalFoto').style.display = 'block';
    },

    abrirModalCheckout: function() {
        // Preencher dados do checkout
        document.getElementById('checkoutHoraInicio').textContent = this.horaInicio.toLocaleTimeString('pt-BR');

        var agora = new Date();
        var diff = agora - this.horaInicio;
        var horas = Math.floor(diff / 3600000);
        var minutos = Math.floor((diff % 3600000) / 60000);
        document.getElementById('checkoutTempoTotal').textContent =
            String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0');

        document.getElementById('modalCheckout').style.display = 'block';
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
        var lista = document.getElementById('listaAtividadesRegistradas');
        if (this.atividadesRegistradas.length === 0) {
            lista.innerHTML = '';
        }
        this.atividadesRegistradas.push(atividade);

        var statusClass = atividade.status === 'executada' ? 'label-success' : 'label-warning';
        var statusText = atividade.status === 'executada' ? 'Executada' : 'Pendente';

        var html = '<div class="alert alert-' + (atividade.status === 'executada' ? 'success' : 'warning') + '" style="margin-bottom: 10px;">' +
            '<span class="label ' + statusClass + '">' + statusText + '</span> ' +
            '<strong>' + atividade.tipo + '</strong><br>' +
            '<small>' + atividade.descricao + '</small><br>' +
            '<small class="muted">' + atividade.data + '</small>' +
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
