<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <!-- Cabeçalho -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-tasks"></i></span>
                <h5>Etapas da Obra: <?php echo $obra->nome; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn btn-mini btn-default">
                        <i class="icon-arrow-left"></i> Voltar
                    </a>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                        <button onclick="$('#modalAdicionarEtapa').modal('show')" class="btn btn-mini btn-success">
                            <i class="icon-plus icon-white"></i> Nova Etapa
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span6">
                        <p><strong>Cliente:</strong> <?php echo $obra->cliente_nome ?? 'N/A'; ?></p>
                        <p><strong>Endereço:</strong> <?php echo $obra->endereco ?? 'N/A'; ?></p>
                    </div>
                    <div class="span6 text-right">
                        <p><strong>Status:</strong>
                            <span class="label label-info"><?php echo $obra->status; ?></span>
                        </p>
                        <p><strong>Progresso:</strong> <?php echo $obra->percentual_concluido ?? 0; ?>%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Etapas -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-list"></i></span>
                <h5>Etapas Cadastradas</h5>
            </div>

            <div class="widget-content nopadding">
                <?php if (!empty($etapas)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;"># Ordem</th>
                                <th style="width: 25%;">Nome</th>
                                <th style="width: 25%;">Descrição</th>
                                <th style="width: 12%;">Previsão</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 13%;">Progresso</th>
                                <th style="width: 10%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($etapas as $etapa): ?>
                                <tr>
                                    <td class="text-center"><?php echo $etapa->numero_etapa ?? '-'; ?></td>
                                    <td><strong><?php echo $etapa->nome; ?></strong></td>
                                    <td><?php echo $etapa->descricao ? nl2br(htmlspecialchars($etapa->descricao)) : '-'; ?></td>
                                    <td>
                                        <?php if ($etapa->data_inicio_prevista): ?>
                                            <small>
                                                <?php echo date('d/m/Y', strtotime($etapa->data_inicio_prevista)); ?>
                                                <?php if ($etapa->data_fim_prevista): ?>
                                                    <br>até <?php echo date('d/m/Y', strtotime($etapa->data_fim_prevista)); ?>
                                                <?php endif; ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="label <?php
                                            echo $etapa->status == 'concluida' ? 'label-success' :
                                                ($etapa->status == 'em_andamento' ? 'label-info' :
                                                    ($etapa->status == 'atrasada' ? 'label-important' : 'label-warning'));
                                        ?>">
                                            <?php
                                            $statusLabels = [
                                                'pendente' => 'Pendente',
                                                'em_andamento' => 'Em Andamento',
                                                'concluida' => 'Concluída',
                                                'atrasada' => 'Atrasada',
                                                'cancelada' => 'Cancelada'
                                            ];
                                            echo $statusLabels[$etapa->status] ?? $etapa->status;
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="margin-bottom: 0; height: 25px;">
                                            <div class="bar <?php echo ($etapa->percentual_concluido ?? 0) == 100 ? 'bar-success' : 'bar-info'; ?>"
                                                 style="width: <?php echo $etapa->percentual_concluido ?? 0; ?>%;">
                                                <?php echo $etapa->percentual_concluido ?? 0; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?php echo site_url('obras/editarEtapa/' . $etapa->id); ?>"
                                               class="btn btn-mini btn-primary" title="Editar">
                                                <i class="icon-edit"></i>
                                            </a>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                                                <a href="<?php echo site_url('obras/excluirEtapa/' . $etapa->id); ?>"
                                                   class="btn btn-mini btn-danger" title="Excluir"
                                                   onclick="return confirm('Tem certeza que deseja excluir esta etapa?');">
                                                    <i class="icon-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="icon-info-sign"></i> Nenhuma etapa cadastrada para esta obra.
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                            <br><br>
                            <button onclick="$('#modalAdicionarEtapa').modal('show')" class="btn btn-small btn-success">
                                <i class="icon-plus icon-white"></i> Adicionar Primeira Etapa
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Etapa -->
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
    <div id="modalAdicionarEtapa" class="modal hide fade" tabindex="-1" role="dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4>Adicionar Nova Etapa</h4>
        </div>

        <form action="<?php echo site_url('obras/adicionarEtapa'); ?>" method="post">
            <div class="modal-body">
                <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

                <div class="control-group">
                    <label class="control-label" for="numero_etapa">Número da Etapa</label>
                    <div class="controls">
                        <input type="number" name="numero_etapa" id="numero_etapa" class="span2"
                               value="<?php echo count($etapas) + 1; ?>" min="1" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="nome">Nome da Etapa</label>
                    <div class="controls">
                        <input type="text" name="nome" id="nome" class="span12" maxlength="100" required>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="descricao">Descrição</label>
                    <div class="controls">
                        <textarea name="descricao" id="descricao" class="span12" rows="3"></textarea>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span6">
                        <div class="control-group">
                            <label class="control-label" for="data_inicio_prevista">Data Início Prevista</label>
                            <div class="controls">
                                <input type="date" name="data_inicio_prevista" id="data_inicio_prevista" class="span12">
                            </div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="control-group">
                            <label class="control-label" for="data_fim_prevista">Data Fim Prevista</label>
                            <div class="controls">
                                <input type="date" name="data_fim_prevista" id="data_fim_prevista" class="span12">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar Etapa</button>
            </div>
        </form>
    </div>
<?php endif; ?>
