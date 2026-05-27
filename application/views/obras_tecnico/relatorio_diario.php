<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="bx bx-file"></i> Relatório Diário</h5>
            </div>
            <div class="card-body">
                <form method="get" action="<?php echo site_url('obras_tecnico/relatorio_diario'); ?>" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Data</label>
                        <input type="date" name="data" class="form-control" value="<?php echo htmlspecialchars($data ?? date('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Obra</label>
                        <select name="obra_id" class="form-select">
                            <option value="">Todas</option>
                            <?php if (!empty($obras)): foreach ($obras as $obra): ?>
                            <option value="<?php echo $obra->id; ?>" <?php echo ($obra_id ?? '') == $obra->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($obra->nome); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filtrar</button>
                    </div>
                </form>

                <?php if (!empty($relatorio)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Técnico</th>
                                <th>Obra</th>
                                <th>Atividade</th>
                                <th>Status</th>
                                <th>Horas</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($relatorio as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->tecnico_nome ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item->obra_nome ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item->atividade ?? $item->descricao ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item->status ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item->horas ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item->observacao ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">Nenhum registro encontrado para o período selecionado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>