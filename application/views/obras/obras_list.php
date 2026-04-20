<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-building"></i></span>
                <h5>Gestão de Obras</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('obras/adicionar'); ?>" class="btn btn-success btn-mini">
                        <i class="icon-plus icon-white"></i> Nova Obra
                    </a>
                </div>
            </div>

            <div class="widget-content nopadding">
                <!-- Filtros -->
                <form method="get" action="<?php echo site_url('obras/gerenciar'); ?>" class="form-inline" style="padding: 10px; background: #f5f5f5;">
                    <label>Status:</label>
                    <select name="status" class="span2">
                        <option value="">Todos</option>
                        <option value="Prospeccao" <?php echo $this->input->get('status') == 'Prospeccao' ? 'selected' : ''; ?>>Prospecção</option>
                        <option value="Em Andamento" <?php echo $this->input->get('status') == 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                        <option value="Paralisada" <?php echo $this->input->get('status') == 'Paralisada' ? 'selected' : ''; ?>>Paralisada</option>
                        <option value="Concluida" <?php echo $this->input->get('status') == 'Concluida' ? 'selected' : ''; ?>>Concluída</option>
                    </select>

                    <label style="margin-left: 10px;">Cliente:</label>
                    <input type="text" name="cliente" class="span3" value="<?php echo $this->input->get('cliente'); ?>" placeholder="Buscar por cliente...">

                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                        <i class="icon-search icon-white"></i> Filtrar
                    </button>

                    <?php if ($this->input->get('status') || $this->input->get('cliente')): ?>
                        <a href="<?php echo site_url('obras/gerenciar'); ?>" class="btn btn-default" style="margin-left: 5px;">
                            <i class="icon-remove"></i> Limpar
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Tabela de Obras -->
                <table class="table table-bordered data-table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Progresso</th>
                            <th>Previsão</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($results) && count($results) > 0): ?>
                            <?php foreach ($results as $r): ?>
                                <tr>
                                    <td><?php echo $r->id; ?></td>
                                    <td>
                                        <a href="<?php echo site_url('obras/visualizar/' . $r->id); ?>">
                                            <?php echo $r->nome; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $r->cliente_nome ?? 'N/A'; ?></td>
                                    <td>
                                        <span class="label <?php echo $this->obras_model->getStatusLabelClass($r->status); ?>">
                                            <?php echo $r->status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="margin-bottom: 0;">
                                            <div class="bar" style="width: <?php echo $r->percentual_concluido ?? 0; ?>%;"></div>
                                        </div>
                                        <small><?php echo $r->percentual_concluido ?? 0; ?>%</small>
                                    </td>
                                    <td>
                                        <?php echo $r->data_fim_prevista ? date('d/m/Y', strtotime($r->data_fim_prevista)) : 'N/A'; ?>
                                    </td>
                                    <td>R$ <?php echo number_format($r->valor_contrato ?? 0, 2, ',', '.'); ?></td>
                                    <td>
                                        <a href="<?php echo site_url('obras/visualizar/' . $r->id); ?>" class="btn btn-mini btn-info" title="Visualizar">
                                            <i class="icon-eye-open icon-white"></i>
                                        </a>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eObras')): ?>
                                            <a href="<?php echo site_url('obras/editar/' . $r->id); ?>" class="btn btn-mini btn-primary" title="Editar">
                                                <i class="icon-edit icon-white"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dObras')): ?>
                                            <button class="btn btn-mini btn-danger" title="Excluir" onclick="confirmarExclusao(<?php echo $r->id; ?>)">
                                                <i class="icon-remove icon-white"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhuma obra encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <?php echo $this->pagination->create_links(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="modalExcluir" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Confirmar Exclusão</h3>
    </div>
    <div class="modal-body">
        <p>Tem certeza que deseja excluir esta obra?</p>
        <p class="text-warning">Esta ação não poderá ser desfeita.</p>
    </div>
    <div class="modal-footer">
        <form id="formExcluir" method="post" action="<?php echo site_url('obras/excluir'); ?>">
            <input type="hidden" name="id" id="obraIdExcluir">
            <button class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Excluir</button>
        </form>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    document.getElementById('obraIdExcluir').value = id;
    $('#modalExcluir').modal('show');
}
</script>
