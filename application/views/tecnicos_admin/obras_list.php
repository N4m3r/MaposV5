<?php
/**
 * Gestão de Obras - Lista
 */
$obras = $obras ?? [];
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-building"></i></span>
                <h5>Gestão de Obras</h5>
                <div class="buttons">
                    <a href="#" class="btn btn-mini btn-success">
                        <i class="icon-plus icon-white"></i> Nova Obra
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <?php if (!empty($obras)): ?>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Cliente</th>
                                <th>Responsável</th>
                                <th>Status</th>
                                <th>Progresso</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($obras as $obra): ?>
                                <tr>
                                    <td><?php echo $obra->id; ?></td>
                                    <td><?php echo $obra->nome; ?></td>
                                    <td><?php echo $obra->cliente_nome ?? '-'; ?></td>
                                    <td><?php echo $obra->responsavel_nome ?? '-'; ?></td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'planejada' => 'default',
                                            'em_execucao' => 'warning',
                                            'paralisada' => 'important',
                                            'concluida' => 'success',
                                        ][$obra->status ?? 'planejada'] ?? 'default';
                                        ?>
                                        <span class="badge badge-<?php echo $status_class; ?>">
                                            <?php echo $obra->status ?? 'Planejada'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress">
                                            <div class="bar" style="width: <?php echo $obra->progresso ?? 0; ?>%;">
                                                <?php echo $obra->progresso ?? 0; ?>%
                                            </div>
                                        </div>
                                    </td>
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
                        <i class="icon-info-sign"></i> Nenhuma obra cadastrada.
                        <hr>
                        <p>O módulo de obras permite gerenciar projetos maiores que envolvem múltiplas OS e equipes.</p>
                        <ul>
                            <li>Agrupar OS relacionadas a um mesmo projeto</li>
                            <li>Acompanhar progresso geral</li>
                            <li>Controlar materiais e recursos</li>
                            <li>Gerenciar equipe alocada</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
