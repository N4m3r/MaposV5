<?php
/**
 * Catálogo de Serviços para Técnicos
 */
$servicos = $servicos ?? [];
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-wrench"></i></span>
                <h5>Catálogo de Serviços</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos_admin/adicionar_servico'); ?>" class="btn btn-mini btn-success">
                        <i class="icon-plus icon-white"></i> Novo Serviço
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($servicos)): ?>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Categoria</th>
                                <th>Tempo Estimado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicos as $servico): ?>
                                <tr>
                                    <td><code><?php echo $servico->codigo ?? $servico->id; ?></code></td>
                                    <td><?php echo $servico->nome; ?></td>
                                    <td>
                                        <span class="badge badge-info"><?php echo $servico->tipo ?? 'Geral'; ?></span>
                                    </td>
                                    <td><?php echo $servico->categoria ?? '-'; ?></td>
                                    <td><?php echo ($servico->tempo_estimado_horas ?? '-') . 'h'; ?></td>
                                    <td class="button-tip">
                                        <a href="#" class="btn btn-mini btn-info" title="Visualizar">
                                            <i class="icon-eye-open icon-white"></i>
                                        </a>
                                        <a href="#" class="btn btn-mini btn-warning" title="Editar">
                                            <i class="icon-edit icon-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="icon-info-sign"></i> Nenhum serviço cadastrado no catálogo.
                        <a href="<?php echo site_url('tecnicos_admin/adicionar_servico'); ?>" class="btn btn-small btn-success">
                            <i class="icon-plus icon-white"></i> Cadastrar primeiro serviço
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
