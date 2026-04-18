<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="icon-user"></i>
                </span>
                <h5>Lista de Técnicos</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos_admin/adicionar_tecnico'); ?>" class="btn btn-mini btn-success">
                        <i class="icon-plus icon-white"></i> Novo Técnico
                    </a>
                </div>
            </div>

            <div class="widget-content nopadding">
                <?php if (isset($tecnicos) && !empty($tecnicos)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Nível</th>
                                <th>Veículo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tecnicos as $tecnico): ?>
                                <tr>
                                    <td><?php echo $tecnico->idUsuarios ?? $tecnico->id; ?></td>
                                    <td><?php echo $tecnico->nome; ?></td>
                                    <td><?php echo $tecnico->email; ?></td>
                                    <td><?php echo $tecnico->nivel_tecnico ?? 'II'; ?></td>
                                    <td><?php echo ($tecnico->veiculo_tipo ?? 'N/A') . ' - ' . ($tecnico->veiculo_placa ?? '-'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo ($tecnico->ativo ?? 1) ? 'success' : 'important'; ?>">
                                            <?php echo ($tecnico->ativo ?? 1) ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td class="button-tip">
                                        <a href="<?php echo site_url('tecnicos_admin/ver_tecnico/' . ($tecnico->idUsuarios ?? $tecnico->id)); ?>"
                                           class="btn btn-mini btn-info" title="Visualizar">
                                            <i class="icon-eye-open icon-white"></i>
                                        </a>
                                        <a href="<?php echo site_url('tecnicos_admin/editar_tecnico/' . ($tecnico->idUsuarios ?? $tecnico->id)); ?>"
                                           class="btn btn-mini btn-warning" title="Editar">
                                            <i class="icon-edit icon-white"></i>
                                        </a>
                                        <a href="<?php echo site_url('tecnicos_admin/estoque_tecnico/' . ($tecnico->idUsuarios ?? $tecnico->id)); ?>"
                                           class="btn btn-mini btn-success" title="Estoque">
                                            <i class="icon-shopping-cart icon-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">
                        Nenhum técnico cadastrado. <a href="<?php echo site_url('tecnicos_admin/adicionar_tecnico'); ?>">Cadastrar primeiro técnico</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
