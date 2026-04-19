<div class="new122">
    <!-- Header -->
    <div class="widget-title" style="margin: -20px 0 0; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span class="icon"><i class="bx bx-task"></i></span>
            <h5>Tarefas da Obra: <?= htmlspecialchars($obra->nome, ENT_QUOTES, 'UTF-8') ?></h5>
        </div>
        <div class="buttons">
            <a href="<?= site_url('tecnicos_admin/ver_obra/' . $obra->id) ?>" class="button btn btn-mini btn-default">
                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                <span class="button__text2">Voltar a Obra</span>
            </a>
            <a href="#modal-nova-tarefa" data-toggle="modal" class="button btn btn-mini btn-success">
                <span class="button__icon"><i class="bx bx-plus"></i></span>
                <span class="button__text2">Nova Tarefa</span>
            </a>
            <a href="<?= site_url('tecnicos_admin/relatorio_atividades_dia?obra_id=' . $obra->id) ?>" class="button btn btn-mini btn-info">
                <span class="button__icon"><i class="bx bx-file"></i></span>
                <span class="button__text2">Relatorio do Dia</span>
            </a>
        </div>
    </div>

    <!-- Estatisticas -->
    <div class="row-fluid" style="margin-top: 20px;">
        <div class="span3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px;">
                <div style="font-size: 2rem; font-weight: 700;"><?= $stats['total'] ?></div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Total de Tarefas</div>
            </div>
        </div>
        <div class="span3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px;">
                <div style="font-size: 2rem; font-weight: 700;"><?= $stats['pendentes'] ?></div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Pendentes</div>
            </div>
        </div>
        <div class="span3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px;">
                <div style="font-size: 2rem; font-weight: 700;"><?= $stats['em_andamento'] ?></div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Em Andamento</div>
            </div>
        </div>
        <div class="span3">
            <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; border-radius: 10px;">
                <div style="font-size: 2rem; font-weight: 700;"><?= $stats['concluidas'] ?></div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Concluidas</div>
            </div>
        </div>
    </div>

    <!-- Lista de Tarefas -->
    <div class="row-fluid" style="margin-top: 20px;">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="bx bx-list-check"></i></span>
                    <h5>Lista de Tarefas</h5>
                    <div class="filtros-tarefas" style="float: right; margin-right: 10px;">
                        <select id="filtro-status" class="input-small" style="margin-bottom: 0;">
                            <option value="">Todos os Status</option>
                            <option value="pendente">Pendente</option>
                            <option value="em_andamento">Em Andamento</option>
                            <option value="concluida">Concluida</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                        <select id="filtro-prioridade" class="input-small" style="margin-bottom: 0;">
                            <option value="">Todas Prioridades</option>
                            <option value="urgente">Urgente</option>
                            <option value="alta">Alta</option>
                            <option value="normal">Normal</option>
                            <option value="baixa">Baixa</option>
                        </select>
                    </div>
                </div>
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped" id="tabela-tarefas">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Titulo</th>
                                <th style="width: 150px;">Tecnico</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;">Prioridade</th>
                                <th style="width: 100px;">Progresso</th>
                                <th style="width: 120px;">Prazo</th>
                                <th style="width: 150px;">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tarefas)): ?>
                                <?php foreach ($tarefas as $tarefa): ?>
                                    <?php
                                    $corStatus = [
                                        'pendente' => ['#ff9800', 'Pendente'],
                                        'em_andamento' => ['#2196f3', 'Em Andamento'],
                                        'pausada' => ['#9e9e9e', 'Pausada'],
                                        'concluida' => ['#4caf50', 'Concluida'],
                                        'cancelada' => ['#f44336', 'Cancelada']
                                    ][$tarefa->status] ?? ['#9e9e9e', $tarefa->status];

                                    $corPrioridade = [
                                        'urgente' => ['#f44336', 'Urgente'],
                                        'alta' => ['#ff9800', 'Alta'],
                                        'normal' => ['#2196f3', 'Normal'],
                                        'baixa' => ['#4caf50', 'Baixa']
                                    ][$tarefa->prioridade] ?? ['#9e9e9e', $tarefa->prioridade];

                                    $hoje = date('Y-m-d');
                                    $atrasada = $tarefa->status !== 'concluida' && $tarefa->data_fim_prevista < $hoje;
                                    ?>
                                    <tr data-status="<?= $tarefa->status ?>" data-prioridade="<?= $tarefa->prioridade ?>">
                                        <td><?= $tarefa->id ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($tarefa->titulo, ENT_QUOTES, 'UTF-8') ?></strong>
                                            <?php if ($tarefa->descricao): ?>
                                                <br><small style="color: #666;"><?= htmlspecialchars(substr($tarefa->descricao, 0, 100), ENT_QUOTES, 'UTF-8') ?>...</small>
                                            <?php endif; ?>
                                            <?php if ($atrasada): ?>
                                                <span class="label label-important">ATRASADA</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $tarefa->tecnico_nome ?: 'Nao atribuido' ?></td>
                                        <td>
                                            <span class="label" style="background: <?= $corStatus[0] ?>; color: white;">
                                                <?= $corStatus[1] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="label" style="background: <?= $corPrioridade[0] ?>; color: white;">
                                                <?= $corPrioridade[1] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="margin: 0; height: 20px;">
                                                <div class="bar" style="width: <?= $tarefa->percentual_concluido ?>%; background: <?= $tarefa->percentual_concluido >= 100 ? '#4caf50' : '#2196f3' ?>;">
                                                    <?= $tarefa->percentual_concluido ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($tarefa->data_fim_prevista)) ?>
                                            <?php if ($atrasada): ?>
                                                <br><small style="color: #f44336;">(<?= floor((strtotime($hoje) - strtotime($tarefa->data_fim_prevista)) / 86400) ?> dias)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="#modal-editar-tarefa" data-toggle="modal" data-id="<?= $tarefa->id ?>"
                                                   class="btn btn-mini btn-info btn-editar" title="Editar">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <a href="#modal-historico" data-toggle="modal" data-id="<?= $tarefa->id ?>"
                                                   class="btn btn-mini btn-default btn-historico" title="Historico">
                                                    <i class="bx bx-history"></i>
                                                </a>
                                                <a href="<?= site_url('tecnicos_admin/excluir_tarefa/' . $tarefa->id) ?>"
                                                   class="btn btn-mini btn-danger" title="Excluir"
                                                   onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px;">
                                        <i class="bx bx-task-x" style="font-size: 3rem; color: #ddd;"></i>
                                        <p style="color: #999; margin-top: 10px;">Nenhuma tarefa cadastrada.</p>
                                        <a href="#modal-nova-tarefa" data-toggle="modal" class="btn btn-small btn-success">
                                            <i class="bx bx-plus"></i> Criar Primeira Tarefa
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nova Tarefa -->
<div id="modal-nova-tarefa" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h5><i class="bx bx-plus-circle"></i> Nova Tarefa</h5>
    </div>
    <form action="<?= site_url('tecnicos_admin/salvar_tarefa') ?>" method="post">
        <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
        <div class="modal-body">
            <div class="control-group">
                <label class="control-label">Titulo *</label>
                <div class="controls">
                    <input type="text" name="titulo" class="span11" required placeholder="Ex: Instalacao eletrica - Quadro principal">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Descricao</label>
                <div class="controls">
                    <textarea name="descricao" class="span11" rows="3" placeholder="Detalhes da tarefa..."></textarea>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Tecnico Responsavel *</label>
                        <div class="controls">
                            <select name="tecnico_id" class="span12" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($tecnicos as $tec): ?>
                                    <option value="<?= $tec->idUsuarios ?>"><?= $tec->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Prioridade</label>
                        <div class="controls">
                            <select name="prioridade" class="span12">
                                <option value="baixa">Baixa</option>
                                <option value="normal" selected>Normal</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <div class="control-group">
                        <label class="control-label">Data de Inicio</label>
                        <div class="controls">
                            <input type="date" name="data_inicio" class="span12" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
                <div class="span4">
                    <div class="control-group">
                        <label class="control-label">Data de Termino *</label>
                        <div class="controls">
                            <input type="date" name="data_fim_prevista" class="span12" required>
                        </div>
                    </div>
                </div>
                <div class="span4">
                    <div class="control-group">
                        <label class="control-label">Status Inicial</label>
                        <div class="controls">
                            <select name="status" class="span12">
                                <option value="pendente" selected>Pendente</option>
                                <option value="em_andamento">Em Andamento</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success"><i class="bx bx-save"></i> Salvar Tarefa</button>
        </div>
    </form>
</div>

<!-- Modal: Editar Tarefa -->
<div id="modal-editar-tarefa" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h5><i class="bx bx-edit"></i> Editar Tarefa</h5>
    </div>
    <form action="<?= site_url('tecnicos_admin/salvar_tarefa') ?>" method="post" id="form-editar-tarefa">
        <input type="hidden" name="obra_id" value="<?= $obra->id ?>">
        <input type="hidden" name="tarefa_id" id="edit-tarefa-id">
        <div class="modal-body">
            <div class="control-group">
                <label class="control-label">Titulo *</label>
                <div class="controls">
                    <input type="text" name="titulo" id="edit-titulo" class="span11" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Descricao</label>
                <div class="controls">
                    <textarea name="descricao" id="edit-descricao" class="span11" rows="3"></textarea>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Tecnico Responsavel *</label>
                        <div class="controls">
                            <select name="tecnico_id" id="edit-tecnico" class="span12" required>
                                <?php foreach ($tecnicos as $tec): ?>
                                    <option value="<?= $tec->idUsuarios ?>"><?= $tec->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label">Prioridade</label>
                        <div class="controls">
                            <select name="prioridade" id="edit-prioridade" class="span12">
                                <option value="baixa">Baixa</option>
                                <option value="normal">Normal</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <div class="control-group">
                        <label class="control-label">Data de Inicio</label>
                        <div class="controls">
                            <input type="date" name="data_inicio" id="edit-data-inicio" class="span12">
                        </div>
                    </div>
                </div>
                <div class="span4">
                    <div class="control-group">
                        <label class="control-label">Data de Termino *</label>
                        <div class="controls">
                            <input type="date" name="data_fim_prevista" id="edit-data-fim" class="span12" required>
                        </div>
                    </div>
                </div>
                <div class="span4">
                    <div class="control-group">
                        <label class="control-label">Status</label>
                        <div class="controls">
                            <select name="status" id="edit-status" class="span12">
                                <option value="pendente">Pendente</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="pausada">Pausada</option>
                                <option value="concluida">Concluida</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Progresso (%)</label>
                <div class="controls">
                    <input type="number" name="percentual" id="edit-percentual" class="span4" min="0" max="100" value="0">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Observacoes</label>
                <div class="controls">
                    <textarea name="observacoes" id="edit-observacoes" class="span11" rows="2" placeholder="Observacoes sobre a execucao..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Atualizar Tarefa</button>
        </div>
    </form>
</div>

<!-- Modal: Historico da Tarefa -->
<div id="modal-historico" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h5><i class="bx bx-history"></i> Historico da Tarefa</h5>
    </div>
    <div class="modal-body">
        <div id="historico-conteudo">
            <p style="text-align: center; color: #999;">Carregando...</p>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Fechar</button>
    </div>
</div>

<script>
$(document).ready(function() {
    // Filtros
    $('#filtro-status, #filtro-prioridade').on('change', function() {
        var status = $('#filtro-status').val();
        var prioridade = $('#filtro-prioridade').val();

        $('#tabela-tarefas tbody tr').each(function() {
            var rowStatus = $(this).data('status');
            var rowPrioridade = $(this).data('prioridade');

            var showStatus = !status || rowStatus === status;
            var showPrioridade = !prioridade || rowPrioridade === prioridade;

            if (showStatus && showPrioridade) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Editar tarefa - carregar dados
    $('.btn-editar').on('click', function() {
        var tarefaId = $(this).data('id');

        $.ajax({
            url: '<?= site_url("tecnicos_admin/ver_tarefa") ?>/' + tarefaId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var t = response.tarefa;
                    $('#edit-tarefa-id').val(t.id);
                    $('#edit-titulo').val(t.titulo);
                    $('#edit-descricao').val(t.descricao);
                    $('#edit-tecnico').val(t.tecnico_id);
                    $('#edit-prioridade').val(t.prioridade);
                    $('#edit-status').val(t.status);
                    $('#edit-data-inicio').val(t.data_inicio);
                    $('#edit-data-fim').val(t.data_fim_prevista);
                    $('#edit-percentual').val(t.percentual_concluido);
                    $('#edit-observacoes').val(t.observacoes);
                }
            }
        });
    });

    // Ver historico
    $('.btn-historico').on('click', function() {
        var tarefaId = $(this).data('id');
        var html = '';

        $.ajax({
            url: '<?= site_url("tecnicos_admin/ver_tarefa") ?>/' + tarefaId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.historico.length > 0) {
                    html = '<div style="max-height: 400px; overflow-y: auto;">';
                    response.historico.forEach(function(h) {
                        var data = new Date(h.data_registro).toLocaleString('pt-BR');
                        html += '<div style="border-left: 3px solid #667eea; padding: 10px 15px; margin-bottom: 10px; background: #f8f9fa;">';
                        html += '<div style="font-weight: 600; color: #333;">' + h.tipo + '</div>';
                        html += '<div style="color: #666; font-size: 0.9rem; margin: 5px 0;">' + h.descricao + '</div>';
                        if (h.percentual_novo > 0) {
                            html += '<div style="font-size: 0.85rem; color: #667eea;">Progresso: ' + h.percentual_anterior + '% -> ' + h.percentual_novo + '%</div>';
                        }
                        if (h.horas_trabalhadas > 0) {
                            html += '<div style="font-size: 0.85rem; color: #28a745;">Horas: ' + h.horas_trabalhadas + 'h</div>';
                        }
                        html += '<div style="font-size: 0.8rem; color: #999;">' + data + '</div>';
                        html += '</div>';
                    });
                    html += '</div>';
                } else {
                    html = '<p style="text-align: center; color: #999;">Nenhum historico registrado.</p>';
                }
                $('#historico-conteudo').html(html);
            },
            error: function() {
                $('#historico-conteudo').html('<p style="text-align: center; color: #f44336;">Erro ao carregar historico.</p>');
            }
        });
    });
});
</script>

<style>
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-3px);
}

.filtros-tarefas select {
    height: 30px;
    margin-left: 5px;
}

.progress {
    background: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress .bar {
    transition: width 0.3s;
    text-align: center;
    color: white;
    font-size: 0.75rem;
    line-height: 20px;
}
</style>
