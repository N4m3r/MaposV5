<?php
$statusLabels = [
    'pendente' => 'Pendente',
    'em_andamento' => 'Em Andamento',
    'concluida' => 'Concluída',
];

$statusCores = [
    'pendente' => '#9e9e9e',
    'em_andamento' => '#2196f3',
    'concluida' => '#4caf50',
];
?>

<!-- Técnico Executar Obra -->
<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="bx bx-play-circle"></i></span>
        <h5>Executar Obra</h5>
        <div class="buttons">
            <a href="<?= site_url('tecnicos_admin/minhas_obras') ?>" class="button btn btn-mini btn-default">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar</span>
            </a>
        </div>
    </div>

    <?php if (!empty($obra)): ?>
        <!-- Header da Obra -->
        <div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 16px; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0; font-size: 1.5rem;"><?= htmlspecialchars($obra->nome, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h3>
                    <p style="margin: 5px 0 0 0; opacity: 0.9;">
                        <i class="bx bx-user"></i> <?= htmlspecialchars($obra->cliente_nome ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?>
                    </p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2.5rem; font-weight: 700;"><?= $obra->percentual_concluido ?? 0 ?>%</div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Concluído</div>
                </div>
            </div>
            <div style="margin-top: 15px; height: 10px; background: rgba(255,255,255,0.2); border-radius: 5px; overflow: hidden;">
                <div style="height: 100%; width: <?= $obra->percentual_concluido ?? 0 ?>%; background: white; border-radius: 5px; transition: width 0.5s;"></div>
            </div>
        </div>

        <div class="row-fluid">
            <!-- Coluna Principal -->
            <div class="span8">
                <!-- Etapas para Executar -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-tasks"></i></span>
                        <h5>Etapas da Obra</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php if (!empty($etapas)): ?>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <?php foreach ($etapas as $etapa):
                                    $etapaStatus = $etapa->status ?? 'pendente';
                                    $corStatus = $statusCores[$etapaStatus] ?? '#888';
                                    $percentual = $etapa->percentual_concluido ?? 0;
                                ?>
                                <div class="etapa-card" style="padding: 20px; background: #f8f9fa; border-radius: 12px; border-left: 4px solid <?= $corStatus ?>;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                        <div>
                                            <h5 style="margin: 0; color: #333;"><?= htmlspecialchars($etapa->nome, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></h5>
                                            <?php if (!empty($etapa->descricao)): ?>
                                                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #666;"><?= htmlspecialchars($etapa->descricao, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: <?= $corStatus ?>20; color: <?= $corStatus ?>;">
                                            <?= $statusLabels[$etapaStatus] ?? ucfirst($etapaStatus) ?>
                                        </span>
                                    </div>

                                    <!-- Barra de Progresso -->
                                    <div style="margin: 15px 0;">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #666; margin-bottom: 5px;">
                                            <span>Progresso</span>
                                            <span><?= $percentual ?>%</span>
                                        </div>
                                        <div style="height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                                            <div style="height: 100%; width: <?= $percentual ?>%; background: <?= $corStatus ?>; border-radius: 3px;"></div>
                                        </div>
                                    </div>

                                    <!-- Ações -->
                                    <?php if ($etapaStatus !== 'concluida'): ?>
                                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                                            <button type="button" class="btn btn-small btn-success" onclick="atualizarProgresso(<?= $etapa->id ?>, <?= $etapa->nome ?>)" style="flex: 1;">
                                                <i class="bx bx-refresh"></i> Atualizar Progresso
                                            </button>
                                            <?php if ($etapaStatus === 'em_andamento'): ?>
                                                <button type="button" class="btn btn-small btn-primary" onclick="concluirEtapa(<?= $etapa->id ?>)">
                                                    <i class="bx bx-check"></i> Concluir
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div style="text-align: center; padding: 10px; background: #e8f5e9; border-radius: 8px; color: #388e3c; font-size: 0.9rem;">
                                            <i class="bx bx-check-circle"></i> Etapa Concluída
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #888;">
                                <i class="bx bx-list-ul" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <p>Nenhuma etapa cadastrada</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Minhas Tarefas -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <span class="icon" style="color: white;"><i class="bx bx-task"></i></span>
                        <h5 style="color: white;">Minhas Tarefas</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;" id="minhas-tarefas">
                        <div style="text-align: center; padding: 40px; color: #888;">
                            <i class="bx bx-loader-alt bx-spin" style="font-size: 2rem;"></i>
                            <p style="margin-top: 10px;">Carregando tarefas...</p>
                        </div>
                    </div>
                </div>

                <!-- Minhas OS -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-clipboard"></i></span>
                        <h5>Minhas OS na Obra</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <?php if (!empty($minhas_os)): ?>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <?php foreach ($minhas_os as $os): ?>
                                    <?php
                                    $osStatusColors = [
                                        'Aberto' => '#4caf50',
                                        'Em Andamento' => '#2196f3',
                                        'Finalizado' => '#9c27b0',
                                        'Cancelado' => '#f44336',
                                    ];
                                    $osCor = $osStatusColors[$os->status] ?? '#888';
                                    ?>
                                    <div style="display: flex; align-items: center; gap: 12px; padding: 15px; background: #f8f9fa; border-radius: 12px;">
                                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                            #<?= $os->idOs ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: #333;"><?= htmlspecialchars($os->nomeCliente, ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></div>
                                            <div style="font-size: 0.8rem; color: #888;">
                                                <i class="bx bx-calendar"></i> <?= date('d/m/Y', strtotime($os->dataInicial)) ?>
                                            </div>
                                        </div>
                                        <span style="padding: 4px 10px; border-radius: 10px; font-size: 0.75rem; background: <?= $osCor ?>20; color: <?= $osCor ?>;">
                                            <?= $os->status ?>
                                        </span>
                                        <a href="<?= site_url('os/visualizar/' . $os->idOs) ?>" class="btn btn-mini btn-info">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #888;">
                                <i class="bx bx-clipboard" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <p>Voce nao possui OS nesta obra</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral -->
            <div class="span4">
                <!-- Informações Rápidas -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                    <div class="widget-title" style="background: #f8f9fa;">
                        <span class="icon"><i class="bx bx-info-circle"></i></span>
                        <h5>Informações</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 0.8rem; color: #888; margin-bottom: 3px;">Endereço</div>
                            <div style="font-weight: 500; color: #333;"><?= htmlspecialchars($obra->endereco ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8') ?></div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 0.8rem; color: #888; margin-bottom: 3px;">Previsão Término</div>
                            <div style="font-weight: 500; color: #333;"><?= isset($obra->data_fim_prevista) ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : '-' ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: #888; margin-bottom: 3px;">Tipo</div>
                            <div style="font-weight: 500; color: #333;"><?= $obra->tipo_obra ?? 'Não informado' ?></div>
                        </div>
                    </div>
                </div>

                <!-- Registrar Atividade -->
                <div class="widget-box" style="border-radius: 16px; overflow: hidden;">
                    <div class="widget-title" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <span class="icon" style="color: white;"><i class="bx bx-plus-circle"></i></span>
                        <h5 style="color: white;">Registrar Atividade</h5>
                    </div>
                    <div class="widget-content" style="padding: 20px;">
                        <form action="<?= site_url('tecnicos_admin/adicionar_comentario') ?>" method="post">
                            <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
                            <div class="control-group">
                                <div class="controls">
                                    <select name="tipo" class="span12" style="margin-bottom: 10px;">
                                        <option value="comentario">Comentário</option>
                                        <option value="atualizacao">Atualização</option>
                                        <option value="problema">Problema</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <textarea name="descricao" class="span12" rows="3" placeholder="Descreva sua atividade..." required style="margin-bottom: 10px;"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                <i class="bx bx-save"></i> Registrar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Atualizar Progresso da Etapa -->
        <div id="modal-atualizar-progresso" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5><i class="bx bx-refresh"></i> Atualizar Progresso</h5>
            </div>
            <form id="form-atualizar-progresso" action="<?= site_url('tecnicos_admin/tecnico_atualizar_etapa') ?>" method="post">
                <input type="hidden" name="etapa_id" id="progresso-etapa-id">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="control-label">Etapa</label>
                        <div class="controls">
                            <input type="text" id="progresso-etapa-nome" class="span12" readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">% Concluido</label>
                        <div class="controls">
                            <input type="range" name="percentual" id="progresso-percentual" class="span12" min="0" max="100" style="margin: 10px 0;">
                            <div style="text-align: center; font-size: 1.2rem; font-weight: 600; color: #667eea;">
                                <span id="progresso-valor">0</span>%
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Observacao</label>
                        <div class="controls">
                            <textarea name="observacao" class="span12" rows="3" placeholder="Observacoes sobre o progresso..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bx bx-save"></i> Salvar</button>
                </div>
            </form>
        </div>

        <!-- Modal: Atualizar Progresso da Tarefa -->
        <div id="modal-tarefa-progresso" class="modal hide fade" tabindex="-1" role="dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5><i class="bx bx-task"></i> Atualizar Tarefa</h5>
            </div>
            <form id="form-tarefa-progresso">
                <input type="hidden" name="tarefa_id" id="tarefa-id">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="control-label">Tarefa</label>
                        <div class="controls">
                            <input type="text" id="tarefa-titulo" class="span12" readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">% Concluido</label>
                        <div class="controls">
                            <input type="range" name="percentual" id="tarefa-percentual" class="span12" min="0" max="100" style="margin: 10px 0;">
                            <div style="text-align: center; font-size: 1.2rem; font-weight: 600; color: #667eea;">
                                <span id="tarefa-valor">0</span>%
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Horas Trabalhadas</label>
                        <div class="controls">
                            <input type="number" name="horas_trabalhadas" id="tarefa-horas" class="span12" step="0.5" min="0" placeholder="Ex: 2.5">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Observacao</label>
                        <div class="controls">
                            <textarea name="observacao" id="tarefa-observacao" class="span12" rows="3" placeholder="O que foi feito..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bx bx-save"></i> Salvar</button>
                </div>
            </form>
        </div>

        <script>
        // Carregar tarefas do tecnico
        function carregarTarefas() {
            $.ajax({
                url: '<?= site_url("tecnicos_admin/buscar_tarefas_tecnico") ?>',
                type: 'GET',
                data: { obra_id: <?= $obra->id ?> },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.tarefas.length > 0) {
                        let html = '<div style="display: flex; flex-direction: column; gap: 10px;">';

                        response.tarefas.forEach(function(t) {
                            const prioridadeCores = {
                                'urgente': '#f44336',
                                'alta': '#ff9800',
                                'normal': '#2196f3',
                                'baixa': '#4caf50'
                            };
                            const cor = prioridadeCores[t.prioridade] || '#888';

                            const statusLabels = {
                                'pendente': 'Pendente',
                                'em_andamento': 'Em Andamento',
                                'pausada': 'Pausada',
                                'concluida': 'Concluida'
                            };

                            const isConcluida = t.status === 'concluida';

                            html += '<div class="tarefa-card" style="padding: 15px; background: #f8f9fa; border-radius: 12px; border-left: 4px solid ' + cor + '; opacity: ' + (isConcluida ? '0.7' : '1') + ';">';
                            html += '<div style="display: flex; justify-content: space-between; align-items: flex-start;">';
                            html += '<div>';
                            html += '<div style="font-weight: 600; color: #333;">' + t.titulo + '</div>';
                            if (t.descricao) {
                                html += '<div style="font-size: 0.85rem; color: #666; margin-top: 3px;">' + t.descricao.substring(0, 60) + '...</div>';
                            }
                            html += '<div style="margin-top: 5px;">';
                            html += '<span style="padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; background: ' + cor + '20; color: ' + cor + '; margin-right: 5px;">' + t.prioridade.toUpperCase() + '</span>';
                            html += '<span style="font-size: 0.8rem; color: #888;"><i class="bx bx-calendar"></i> ' + t.data_fim_prevista + '</span>';
                            html += '</div>';
                            html += '</div>';

                            if (!isConcluida) {
                                html += '<button type="button" class="btn btn-small btn-primary" onclick="abrirModalTarefa(' + t.id + ', \'' + t.titulo + '\', ' + t.percentual_concluido + ')">';
                                html += '<i class="bx bx-edit"></i>';
                                html += '</button>';
                            }
                            html += '</div>';

                            html += '<div style="margin-top: 10px;">';
                            html += '<div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #666; margin-bottom: 3px;">';
                            html += '<span>' + statusLabels[t.status] + '</span>';
                            html += '<span>' + t.percentual_concluido + '%</span>';
                            html += '</div>';
                            html += '<div style="height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">';
                            html += '<div style="height: 100%; width: ' + t.percentual_concluido + '%; background: ' + cor + '; border-radius: 3px; transition: width 0.3s;"></div>';
                            html += '</div>';
                            html += '</div>';

                            html += '</div>';
                        });

                        html += '</div>';
                        $('#minhas-tarefas').html(html);
                    } else {
                        $('#minhas-tarefas').html('<div style="text-align: center; padding: 40px; color: #888;"><i class="bx bx-task" style="font-size: 3rem; margin-bottom: 15px;"></i><p>Nenhuma tarefa atribuida</p></div>');
                    }
                },
                error: function() {
                    $('#minhas-tarefas').html('<div style="text-align: center; padding: 40px; color: #f44336;">Erro ao carregar tarefas</div>');
                }
            });
        }

        // Carregar tarefas ao iniciar
        $(document).ready(function() {
            carregarTarefas();
        });

        // Atualizar valor do range
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
            $('#tarefa-observacao').val('');
            $('#tarefa-horas').val('');
            $('#modal-tarefa-progresso').modal('show');
        }

        // Submeter atualizacao da tarefa
        $('#form-tarefa-progresso').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: '<?= site_url("tecnicos_admin/api_atualizar_tarefa_tecnico") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#modal-tarefa-progresso').modal('hide');
                        carregarTarefas();

                        // Mostrar alerta de sucesso
                        const alert = '<div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">' +
                                      '<button type="button" class="close" data-dismiss="alert">×</button>' +
                                      '<i class="bx bx-check"></i> ' + response.message +
                                      '</div>';
                        $('body').append(alert);
                        setTimeout(function() {
                            $('.alert').fadeOut(function() { $(this).remove(); });
                        }, 3000);
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function() {
                    alert('Erro ao atualizar tarefa');
                }
            });
        });

        function concluirEtapa(etapaId) {
            if (!confirm('Tem certeza que deseja marcar esta etapa como concluida?')) return;

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
        </script>

        <style>
        .tarefa-card {
            transition: all 0.3s;
        }
        .tarefa-card:hover {
            transform: translateX(3px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        </style>

    <?php else: ?>
        <div style="text-align: center; padding: 80px 20px;">
            <div style="font-size: 5rem; color: #e0e0e0; margin-bottom: 20px;">
                <i class="bx bx-error-circle"></i>
            </div>
            <h3 style="color: #666; font-weight: 400; margin-bottom: 10px;">Obra não encontrada</h3>
            <p style="color: #999; margin-bottom: 25px;">Você não tem acesso a esta obra.</p>
            <a href="<?= site_url('tecnicos_admin/minhas_obras') ?>" class="button btn btn-success">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar para Minhas Obras</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.etapa-card {
    transition: all 0.3s;
}

.etapa-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
