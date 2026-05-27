<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-chart'></i> Relatorio de Impostos</h5>
            </div>
            <div class="card-body">
                <form method="get" class="row g-2 mb-4">
                    <div class="col-auto">
                        <label>Data Inicio</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?= $data_inicio ?? date('Y-m-01') ?>">
                    </div>
                    <div class="col-auto">
                        <label>Data Fim</label>
                        <input type="date" name="data_fim" class="form-control" value="<?= $data_fim ?? date('Y-m-t') ?>">
                    </div>
                    <div class="col-auto align-self-end">
                        <button type="submit" class="btn btn-primary"><i class='bx bx-search'></i> Filtrar</button>
                    </div>
                </form>

                <?php if (!empty($results['impostos'])): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Base de Calculo</th>
                                <th>Aliquota</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results['impostos'] as $imp): ?>
                            <tr>
                                <td><?= e($imp['tipo'] ?? $imp['nome'] ?? '') ?></td>
                                <td>R$ <?= number_format(($imp['base_calculo'] ?? 0), 2, ',', '.') ?></td>
                                <td><?= number_format(($imp['aliquota'] ?? 0), 2, ',', '.') ?>%</td>
                                <td>R$ <?= number_format(($imp['valor'] ?? 0), 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3">Total</td>
                                <td>R$ <?= number_format(($results['total_geral'] ?? 0), 2, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center p-4">
                    <i class='bx bx-chart' style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="mt-2 text-muted">Nenhum dado encontrado para o periodo selecionado.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>