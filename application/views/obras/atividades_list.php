<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-tasks"></i></span>
                <h5>Atividades da Obra: <?php echo $obra->nome; ?></h5>
                <div class="buttons">
                    <button class="btn btn-success btn-mini" data-toggle="modal" data-target="#modalAdicionar">
                        <i class="icon-plus icon-white"></i> Nova Atividade
                    </button>
                    <a href="<?php echo site_url('obras/visualizar/' . $obra->id); ?>" class="btn btn-mini">
                        <i class="icon-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="widget-content nopadding">
                <table class="table table-bordered data-table table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Título</th>
                            <th>Técnico</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Progresso</th>
                            <th>Visível Cliente</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($atividades) > 0): ?>
                            <?php foreach ($atividades as $atividade): ?>
                                <?php
                                $status_class = [
                                    'agendada' => 'default',
                                    'iniciada' => 'info',
                                    'pausada' => 'warning',
                                    'concluida' => 'success',
                                    'cancelada' => 'inverse'
                                ][$atividade->status] ?? 'default';

                                $tipo_class = [
                                    'trabalho' => 'label-info',
                                    'impedimento' => 'label-important',
                                    'visita' => 'label-success',
                                    'manutencao' => 'label-warning',
                                    'outro' => 'label-default'
                                ][$atividade->tipo] ?? 'label-default';
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($atividade->data_atividade)); ?></td>
                                    <td><?php echo $atividade->titulo; ?></td>
                                    <td><?php echo $atividade->tecnico_nome ?? 'Não atribuído'; ?></td>
                                    <td>
                                        <span class="label <?php echo $tipo_class; ?>"><?php echo ucfirst($atividade->tipo); ?></span>
                                    </td>
                                    <td>
                                        <span class="label label-<?php echo $status_class; ?>"><?php echo ucfirst($atividade->status); ?></span>
                                    </td>
                                    <td>
                                        <?php echo $atividade->percentual_concluido; ?>%
                                        <?php if ($atividade->horas_trabalhadas): ?>
                                            <small>(<?php echo $atividade->horas_trabalhadas; ?>h)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($atividade->visivel_cliente): ?>
                                            <i class="icon-ok text-success" title="Visível ao cliente"></i>
                                        <?php else: ?>
                                            <i class="icon-remove text-error" title="Oculto do cliente"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade->id); ?>" class="btn btn-mini btn-info" title="Visualizar">
                                            <i class="icon-eye-open icon-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhuma atividade encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Atividade -->
<div id="modalAdicionar" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Adicionar Atividade</h3>
    </div>
    <form action="<?php echo site_url('obras/adicionarAtividade'); ?>" method="post">
        <div class="modal-body">
            <input type="hidden" name="obra_id" value="<?php echo $obra->id; ?>">

            <div class="control-group">
                <label>Título <span class="required">*</span></label>
                <input type="text" name="titulo" class="span12" required>
            </div>

            <div class="control-group">
                <label>Descrição</label>
                <textarea name="descricao" class="span12" rows="3"></textarea>
            </div>

            <div class="row-fluid">
                <div class="span6">
                    <label>Data da Atividade</label>
                    <input type="date" name="data_atividade" class="span12" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="span6">
                    <label>Tipo</label>
                    <select name="tipo" class="span12">
                        <option value="trabalho">Trabalho</option>
                        <option value="visita">Visita Técnica</option>
                        <option value="manutencao">Manutenção</option>
                        <option value="impedimento">Impedimento</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
            </div>

            <div class="row-fluid" style="margin-top: 10px;">
                <div class="span6">
                    <label>Técnico Responsável</label>
                    <select name="tecnico_id" class="span12">
                        <option value="">Selecione...</option>
                        <?php foreach ($tecnicos as $t): ?>
                            <option value="<?php echo $t->idUsuarios; ?>"><?php echo $t->nome; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="span6">
                    <label>Etapa</label>
                    <select name="etapa_id" class="span12">
                        <option value="">Selecione...</option>
                    </select>
                </div>
            </div>

            <div class="control-group" style="margin-top: 10px;">
                <label class="checkbox">
                    <input type="checkbox" name="visivel_cliente" value="1" checked>
                    Visível ao cliente
                </label>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </form>
</div>
