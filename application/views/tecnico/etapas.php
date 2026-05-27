<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="bx bx-list-ol"></i> Etapas da OS</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($etapas)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Etapa</th>
                                <th>Status</th>
                                <th>Responsável</th>
                                <th>Início</th>
                                <th>Conclusão</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($etapas as $etapa): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($etapa->etapa ?? $etapa->nome ?? ''); ?></td>
                                <td>
                                    <?php
                                    $status = $etapa->status ?? 'pendente';
                                    $badgeClass = match($status) {
                                        'concluida' => 'bg-success',
                                        'em_andamento' => 'bg-warning',
                                        'cancelada' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($etapa->responsavel_nome ?? $etapa->responsavel_id ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($etapa->iniciado_at ?? $etapa->data_inicio ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($etapa->concluido_at ?? $etapa->data_fim ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($etapa->observacao ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">Nenhuma etapa registrada para esta OS.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>