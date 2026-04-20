<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
.obra-execucao { padding: 20px; }

/* Header */
.exec-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
}
.exec-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}
.exec-header-info h1 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
}
.exec-header-info p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}
.exec-progress-section { flex: 1; max-width: 300px; }
.exec-progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
}
.exec-progress-bar {
    height: 12px;
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
    overflow: hidden;
}
.exec-progress-fill {
    height: 100%;
    background: white;
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Layout Grid */
.exec-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

/* Cards */
.exec-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
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
.exec-card-title i { color: #11998e; font-size: 22px; }

/* Timeline de Etapas */
.etapas-timeline-modern {
    position: relative;
    padding-left: 30px;
}
.etapas-timeline-modern::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #11998e, #38ef7d);
    border-radius: 3px;
}
.etapa-timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 25px;
}
.etapa-timeline-item:last-child { margin-bottom: 0; }
.etapa-dot-modern {
    position: absolute;
    left: -26px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ddd;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #e8e8e8;
}
.etapa-dot-modern.concluida {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.3);
}
.etapa-dot-modern.andamento {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.3);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.etapa-card-modern {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    border-left: 4px solid #ddd;
    transition: all 0.3s;
}
.etapa-card-modern:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.etapa-card-modern.concluida { border-left-color: #11998e; }
.etapa-card-modern.andamento { border-left-color: #4facfe; }
.etapa-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.etapa-title-modern { font-weight: 700; color: #333; font-size: 16px; }
.etapa-status-badge {
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
.etapa-status-badge.pendente { background: #e0e0e0; color: #666; }
.etapa-status-badge.andamento { background: #e3f2fd; color: #1976d2; }
.etapa-status-badge.concluida { background: #e8f5e9; color: #388e3c; }

/* Progress Bar Small */
.progress-small { margin: 15px 0; }
.progress-header-small {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 5px;
}
.progress-bar-small {
    height: 6px;
    background: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}
.progress-bar-small .fill {
    height: 100%;
    border-radius: 5px;
    transition: width 0.3s;
}

/* Action Buttons */
.etapa-actions { display: flex; gap: 10px; margin-top: 15px; }
.btn-action {
    flex: 1;
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.btn-action-primary {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
}
.btn-action-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
}
.btn-action-secondary {
    background: #e3f2fd;
    color: #1976d2;
}
.btn-action-success {
    background: #e8f5e9;
    color: #388e3c;
}

/* Tarefas */
.tarefas-list { display: flex; flex-direction: column; gap: 12px; }
.tarefa-card-modern {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    border-left: 4px solid #667eea;
    transition: all 0.3s;
}
.tarefa-card-modern:hover { transform: translateX(3px); }
.tarefa-card-modern.urgente { border-left-color: #f44336; }
.tarefa-card-modern.alta { border-left-color: #ff9800; }
.tarefa-card-modern.normal { border-left-color: #2196f3; }
.tarefa-card-modern.baixa { border-left-color: #4caf50; }
.tarefa-card-modern.concluida { opacity: 0.7; border-left-color: #9e9e9e; }

/* Sidebar Info */
.info-card { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
.info-row { display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e8e8e8; }
.info-row:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
.info-label { color: #888; font-size: 13px; }
.info-value { font-weight: 600; color: #333; }

/* Registrar Atividade */
.registrar-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px;
    color: white;
}
.registrar-card h4 { margin: 0 0 15px 0; }
.form-modern { display: flex; flex-direction: column; gap: 12px; }
.form-modern select, .form-modern textarea {
    border: none;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 14px;
    font-family: inherit;
}
.form-modern textarea { resize: vertical; min-height: 80px; }
.btn-registrar {
    background: white;
    color: #667eea;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-registrar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

/* Empty State */
.empty-state-modern {
    text-align: center;
    padding: 50px 20px;
    color: #888;
}
.empty-state-modern i { font-size: 50px; color: #ddd; margin-bottom: 15px; }

/* OS Cards */
.os-card-modern {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 12px;
    transition: all 0.3s;
}
.os-card-modern:hover { background: #e8e8e8; transform: translateX(3px); }
.os-avatar {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}
.os-info { flex: 1; }
.os-cliente { font-weight: 600; color: #333; }
.os-data { font-size: 12px; color: #888; }
.os-status {
    padding: 4px 10px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
}

/* Concluído Badge */
.concluido-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: #e8f5e9;
    border-radius: 10px;
    color: #388e3c;
    font-weight: 600;
}

@media (max-width: 992px) {
    .exec-grid { grid-template-columns: 1fr; }
    .exec-header-top { flex-direction: column; }
    .exec-progress-section { max-width: 100%; width: 100%; }
}
</style>

<div class="obra-execucao">
    <?php if (!empty($obra)): ?>

        <!-- Header -->
        <div class="exec-header">
            <div class="exec-header-top">
                <div class="exec-header-info">
                    <h1><i class="icon-play-circle"></i> <?= htmlspecialchars($obra->nome) ?></h1>
                    <p><i class="icon-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Não informado') ?></p>
                </div>
                <a href="<?= site_url('tecnicos_admin/minhas_obras') ?>" class="btn-action" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="icon-arrow-left"></i> Voltar
                </a>
            </div>

            <div style="margin-top: 25px;">
                <div class="exec-progress-label">
                    <span>Progresso Geral da Obra</span>
                    <span style="font-size: 24px; font-weight: 700;"><?= $obra->percentual_concluido ?? 0 ?>%</span>
                </div>
                <div class="exec-progress-bar">
                    <div class="exec-progress-fill" style="width: <?= $obra->percentual_concluido ?? 0 ?>%;"></div>
                </div>
            </div>
        </div>

        <div class="exec-grid">
            <!-- Coluna Principal -->
            <div class="exec-main">

                <!-- Etapas -->
                <div class="exec-card">
                    <div class="exec-card-header">
                        <div class="exec-card-title">
                            <i class="icon-tasks"></i> Etapas da Obra
                        </div>
                    </div>

                    <?php if (!empty($etapas)): ?>
                        <div class="etapas-timeline-modern">
                            <?php foreach ($etapas as $etapa):
                                $etapaStatus = $etapa->status ?? 'pendente';
                                $dotClass = ($etapaStatus == 'concluida') ? 'concluida' : (($etapaStatus == 'em_andamento') ? 'andamento' : '');
                                $cardClass = ($etapaStatus == 'concluida') ? 'concluida' : (($etapaStatus == 'em_andamento') ? 'andamento' : '');
                                $percentual = $etapa->percentual_concluido ?? 0;
                            ?>
                            <div class="etapa-timeline-item">
                                <div class="etapa-dot-modern <?= $dotClass ?>"></div>
                                <div class="etapa-card-modern <?= $cardClass ?>">
                                    <div class="etapa-header-modern">
                                        <div>
                                            <div class="etapa-title-modern"><?= htmlspecialchars($etapa->nome) ?></div>
                                            <?php if (!empty($etapa->descricao)): ?>
                                                <small style="color: #888;"><?= htmlspecialchars(substr($etapa->descricao, 0, 60)) ?>...</small>
                                            <?php endif; ?>
                                        </div>
                                        <span class="etapa-status-badge <?= $etapaStatus ?>">
                                            <?= ucfirst(str_replace('_', ' ', $etapaStatus)) ?>
                                        </span>
                                    </div>

                                    <div class="progress-small">
                                        <div class="progress-header-small">
                                            <span>Progresso</span>
                                            <span><?= $percentual ?>%</span>
                                        </div>
                                        <div class="progress-bar-small">
                                            <div class="fill" style="width: <?= $percentual ?>%; background: linear-gradient(90deg, #11998e, #38ef7d);"></div>
                                        </div>
                                    </div>

                                    <?php if ($etapaStatus !== 'concluida'): ?>
                                        <div class="etapa-actions">
                                            <button type="button" class="btn-action btn-action-primary" onclick="atualizarProgresso(<?= $etapa->id ?>, '<?= htmlspecialchars($etapa->nome) ?>')">
                                                <i class="icon-refresh"></i> Atualizar
                                            </button>
                                            <?php if ($etapaStatus === 'em_andamento'): ?>
                                                <button type="button" class="btn-action btn-action-success" onclick="concluirEtapa(<?= $etapa->id ?>)" style="flex: 0.5;">
                                                    <i class="icon-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="concluido-badge">
                                            <i class="icon-check-circle"></i> Etapa Concluída
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state-modern">
                            <i class="icon-list-ul"></i>
                            <p>Nenhuma etapa cadastrada para esta obra.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Minhas Tarefas -->
                <div class="exec-card">
                    <div class="exec-card-header">
                        <div class="exec-card-title" style="color: #667eea;">
                            <i class="icon-pushpin"></i> Minhas Tarefas
                        </div>
                    </div>

                    <div id="minhas-tarefas">
                        <div class="empty-state-modern">
                            <i class="icon-refresh icon-spin"></i>
                            <p>Carregando tarefas...</p>
                        </div>
                    </div>
                </div>

                <!-- Minhas OS -->
                <div class="exec-card">
                    <div class="exec-card-header">
                        <div class="exec-card-title" style="color: #764ba2;">
                            <i class="icon-file-alt"></i> Minhas OS na Obra
                        </div>
                    </div>

                    <?php if (!empty($minhas_os)): ?>
                        <?php foreach ($minhas_os as $os):
                            $osStatusColors = [
                                'Aberto' => ['#4caf50', '#e8f5e9'],
                                'Em Andamento' => ['#2196f3', '#e3f2fd'],
                                'Finalizado' => ['#9c27b0', '#f3e5f5'],
                                'Cancelado' => ['#f44336', '#ffebee']
                            ];
                            $osStyle = $osStatusColors[$os->status] ?? ['#888', '#f5f5f5'];
                        ?>
                        <div class="os-card-modern">
                            <div class="os-avatar">#<?= $os->idOs ?></div>
                            <div class="os-info">
                                <div class="os-cliente"><?= htmlspecialchars($os->nomeCliente) ?></div>
                                <div class="os-data"><i class="icon-calendar"></i> <?= date('d/m/Y', strtotime($os->dataInicial)) ?></div>
                            </div>
                            <span class="os-status" style="background: <?= $osStyle[1] ?>; color: <?= $osStyle[0] ?>;">
                                <?= $os->status ?>
                            </span>
                            <a href="<?= site_url('os/visualizar/' . $os->idOs) ?>" class="btn-action btn-action-secondary" style="padding: 8px 12px;">
                                <i class="icon-eye-open"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state-modern">
                            <i class="icon-clipboard"></i>
                            <p>Você não possui OS nesta obra.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="exec-sidebar">

                <!-- Informações -->
                <div class="exec-card" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                    <div class="exec-card-header" style="border-bottom-color: #e0e0e0;">
                        <div class="exec-card-title">
                            <i class="icon-info-sign" style="color: #764ba2;"></i> Informações
                        </div>
                    </div>

                    <div class="info-card" style="background: white;">
                        <div class="info-row">
                            <span class="info-label"><i class="icon-map-marker"></i> Endereço</span>
                            <span class="info-value"><?= htmlspecialchars($obra->endereco ?? 'N/A') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="icon-calendar"></i> Previsão</span>
                            <span class="info-value"><?= isset($obra->data_fim_prevista) ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : '-' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="icon-tag"></i> Tipo</span>
                            <span class="info-value"><?= $obra->tipo_obra ?? 'N/A' ?></span>
                        </div>
                    </div>
                </div>

                <!-- Registrar Atividade -->
                <div class="registrar-card">
                    <h4><i class="icon-plus-sign"></i> Registrar Atividade</h4>

                    <form action="<?= site_url('tecnicos_admin/adicionar_comentario') ?>" method="post" class="form-modern">
                        <input type="hidden" name="obra_id" value="<?= $obra->id ?>">

                        <select name="tipo" required>
                            <option value="comentario">💬 Comentário</option>
                            <option value="atualizacao">📊 Atualização</option>
                            <option value="problema">⚠️ Problema</option>
                        </select>

                        <textarea name="descricao" placeholder="Descreva sua atividade..." required></textarea>

                        <button type="submit" class="btn-registrar">
                            <i class="icon-save"></i> Registrar
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- Modal: Atualizar Progresso -->
        <div id="modal-atualizar-progresso" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header" style="background: linear-gradient(135deg, #11998e, #38ef7d); color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
                <h4><i class="icon-refresh"></i> Atualizar Progresso</h4>
            </div>
            <form id="form-atualizar-progresso" action="<?= site_url('tecnicos_admin/tecnico_atualizar_etapa') ?>" method="post">
                <input type="hidden" name="etapa_id" id="progresso-etapa-id">
                <div class="modal-body" style="padding: 30px;">
                    <div class="control-group">
                        <label style="font-weight: 600; color: #333;">Etapa</label>
                        <input type="text" id="progresso-etapa-nome" class="span12" readonly style="background: #f5f5f5; border: none; padding: 12px; border-radius: 8px;">
                    </div>

                    <div class="control-group" style="margin-top: 20px;">
                        <label style="font-weight: 600; color: #333;">% Concluído</label>
                        <input type="range" name="percentual" id="progresso-percentual" class="span12" min="0" max="100" style="margin: 15px 0;">
                        <div style="text-align: center; font-size: 32px; font-weight: 700; color: #11998e;">
                            <span id="progresso-valor">0</span>%
                        </div>
                    </div>

                    <div class="control-group" style="margin-top: 20px;">
                        <label style="font-weight: 600; color: #333;">Observação</label>
                        <textarea name="observacao" class="span12" rows="3" placeholder="Descreva o que foi realizado..." style="border-radius: 10px; border: 2px solid #e8e8e8; padding: 12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-large" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-large"><i class="icon-save"></i> Salvar Progresso</button>
                </div>
            </form>
        </div>

        <!-- Modal: Tarefa -->
        <div id="modal-tarefa-progresso" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">×</button>
                <h4><i class="icon-pushpin"></i> Atualizar Tarefa</h4>
            </div>
            <form id="form-tarefa-progresso">
                <input type="hidden" name="tarefa_id" id="tarefa-id">
                <div class="modal-body" style="padding: 30px;">
                    <div class="control-group">
                        <label style="font-weight: 600; color: #333;">Tarefa</label>
                        <input type="text" id="tarefa-titulo" class="span12" readonly style="background: #f5f5f5; border: none; padding: 12px; border-radius: 8px;">
                    </div>

                    <div class="control-group" style="margin-top: 20px;">
                        <label style="font-weight: 600; color: #333;">% Concluído</label>
                        <input type="range" name="percentual" id="tarefa-percentual" class="span12" min="0" max="100" style="margin: 15px 0;">
                        <div style="text-align: center; font-size: 32px; font-weight: 700; color: #667eea;">
                            <span id="tarefa-valor">0</span>%
                        </div>
                    </div>

                    <div class="form-row" style="margin-top: 20px;">
                        <div class="control-group" style="flex: 1;">
                            <label style="font-weight: 600; color: #333;">Horas Trabalhadas</label>
                            <input type="number" name="horas_trabalhadas" id="tarefa-horas" class="span12" step="0.5" min="0" placeholder="Ex: 2.5" style="border-radius: 10px; border: 2px solid #e8e8e8; padding: 12px;">
                        </div>
                    </div>

                    <div class="control-group" style="margin-top: 20px;">
                        <label style="font-weight: 600; color: #333;">Observação</label>
                        <textarea name="observacao" id="tarefa-observacao" class="span12" rows="3" placeholder="O que foi realizado..." style="border-radius: 10px; border: 2px solid #e8e8e8; padding: 12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-large" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-large"><i class="icon-save"></i> Salvar</button>
                </div>
            </form>
        </div>

        <script>
        // Range input updates
        $('#progresso-percentual').on('input', function() {
            $('#progresso-valor').text($(this).val());
        });

        $('#tarefa-percentual').on('input', function() {
            $('#tarefa-valor').text($(this).val());
        });

        function atualizarProgresso(etapaId, etapaNome) {
            $('#progresso-etapa-id').val(etapaId);
            $('#progresso-etapa-nome').val(etapaNome);
            $('#modal-atualizar-progresso').modal('show');
        }

        function abrirModalTarefa(tarefaId, titulo, percentual) {
            $('#tarefa-id').val(tarefaId);
            $('#tarefa-titulo').val(titulo);
            $('#tarefa-percentual').val(percentual);
            $('#tarefa-valor').text(percentual);
            $('#modal-tarefa-progresso').modal('show');
        }

        function concluirEtapa(etapaId) {
            if (!confirm('Tem certeza que deseja marcar esta etapa como concluída?')) return;

            $.ajax({
                url: '<?= site_url("tecnicos_admin/atualizar_status_etapa") ?>',
                type: 'POST',
                data: { etapa_id: etapaId, status: 'concluida' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function() {
                    alert('Erro ao atualizar status');
                }
            });
        }

        // Carregar tarefas
        function carregarTarefas() {
            $.ajax({
                url: '<?= site_url("tecnicos_admin/buscar_tarefas_tecnico") ?>',
                type: 'GET',
                data: { obra_id: <?= $obra->id ?> },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.tarefas.length > 0) {
                        let html = '<div class="tarefas-list">';

                        response.tarefas.forEach(function(t) {
                            const prioridadeClasses = {
                                'urgente': 'urgente',
                                'alta': 'alta',
                                'normal': 'normal',
                                'baixa': 'baixa'
                            };
                            const prioridadeCores = {
                                'urgente': '#f44336',
                                'alta': '#ff9800',
                                'normal': '#2196f3',
                                'baixa': '#4caf50'
                            };
                            const cor = prioridadeCores[t.prioridade] || '#888';
                            const classe = prioridadeClasses[t.prioridade] || 'normal';
                            const isConcluida = t.status === 'concluida';

                            html += '<div class="tarefa-card-modern ' + classe + (isConcluida ? ' concluida' : '') + '">';
                            html += '<div style="display: flex; justify-content: space-between; align-items: flex-start;">';
                            html += '<div style="flex: 1;">';
                            html += '<div style="font-weight: 600; color: #333; margin-bottom: 5px;">' + t.titulo + '</div>';
                            if (t.descricao) {
                                html += '<div style="font-size: 13px; color: #666;">' + t.descricao.substring(0, 50) + '...</div>';
                            }
                            html += '<div style="margin-top: 8px; display: flex; gap: 10px; align-items: center;">';
                            html += '<span style="padding: 2px 8px; border-radius: 10px; font-size: 11px; background: ' + cor + '20; color: ' + cor + ';">' + t.prioridade.toUpperCase() + '</span>';
                            html += '<span style="font-size: 12px; color: #888;"><i class="icon-calendar"></i> ' + t.data_fim_prevista + '</span>';
                            html += '</div>';
                            html += '</div>';

                            if (!isConcluida) {
                                html += '<button type="button" class="btn-action btn-action-secondary" onclick="abrirModalTarefa(' + t.id + ', \'' + t.titulo + '\', ' + t.percentual_concluido + ')" style="padding: 8px 12px;">';
                                html += '<i class="icon-edit"></i>';
                                html += '</button>';
                            }
                            html += '</div>';

                            html += '<div style="margin-top: 12px;">';
                            html += '<div style="display: flex; justify-content: space-between; font-size: 12px; color: #666; margin-bottom: 4px;">';
                            html += '<span>' + (t.status === 'concluida' ? 'Concluída' : t.status === 'em_andamento' ? 'Em Andamento' : 'Pendente') + '</span>';
                            html += '<span style="font-weight: 600;">' + t.percentual_concluido + '%</span>';
                            html += '</div>';
                            html += '<div class="progress-bar-small">';
                            html += '<div class="fill" style="width: ' + t.percentual_concluido + '%; background: ' + cor + ';"></div>';
                            html += '</div>';
                            html += '</div>';

                            html += '</div>';
                        });

                        html += '</div>';
                        $('#minhas-tarefas').html(html);
                    } else {
                        $('#minhas-tarefas').html('<div class="empty-state-modern"><i class="icon-pushpin" style="font-size: 40px;"></i><p>Nenhuma tarefa atribuída</p></div>';
                    }
                },
                error: function() {
                    $('#minhas-tarefas').html('<div class="empty-state-modern" style="color: #f44336;"><i class="icon-exclamation-sign"></i><p>Erro ao carregar tarefas</p></div>';
                }
            });
        }

        $(document).ready(function() {
            carregarTarefas();
        });

        // Animate progress bars
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.exec-progress-fill');
            const width = progressBar.style.width;
            progressBar.style.width = '0%';
            setTimeout(() => {
                progressBar.style.width = width;
            }, 300);
        });
        </script>

    <?php else: ?>
        <div style="text-align: center; padding: 100px 20px; background: white; border-radius: 20px; box-shadow: 0 2px 20px rgba(0,0,0,0.08);">
            <div style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;">
                <i class="icon-exclamation-sign"></i>
            </div>
            <h2 style="color: #666; font-weight: 400; margin-bottom: 15px;">Obra não encontrada</h2>
            <p style="color: #999; margin-bottom: 30px;">Você não tem acesso a esta obra ou ela não existe.</p>
            <a href="<?= site_url('tecnicos_admin/minhas_obras') ?>" class="btn-action btn-action-primary" style="display: inline-flex; padding: 15px 30px;">
                <i class="icon-arrow-left"></i> Voltar para Minhas Obras
            </a>
        </div>
    <?php endif; ?>
</div>
