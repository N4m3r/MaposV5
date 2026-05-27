<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class='bx bx-receipt'></i> Detalhes NFS-e #<?= $nfse->id ?? '' ?></h5>
                <a href="<?= site_url('nfse_os') ?>" class="btn btn-sm btn-secondary float-end"><i class='bx bx-arrow-back'></i> Voltar</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Dados da NFS-e</h6>
                        <table class="table table-bordered">
                            <tr><th width="35%">Numero</th><td><?= e($nfse->numero ?? '') ?></td></tr>
                            <tr><th>Codigo Verificacao</th><td><?= e($nfse->codigo_verificacao ?? '') ?></td></tr>
                            <tr><th>Data Emissao</th><td><?= $nfse->data_emissao ?? '' ?></td></tr>
                            <tr><th>Status</th><td>
                                <?php $statusClass = ['emitida' => 'success', 'cancelada' => 'danger', 'pendente' => 'warning']; ?>
                                <span class="badge bg-<?= $statusClass[$nfse->status ?? ''] ?? 'secondary' ?>"><?= e($nfse->status ?? 'N/A') ?></span>
                            </td></tr>
                            <tr><th>Valor Servicos</th><td>R$ <?= number_format(($nfse->valor_servicos ?? 0), 2, ',', '.') ?></td></tr>
                            <tr><th>Valor Impostos</th><td>R$ <?= number_format(($nfse->valor_impostos ?? 0), 2, ',', '.') ?></td></tr>
                            <tr><th>Valor Liquido</th><td>R$ <?= number_format(($nfse->valor_liquido ?? 0), 2, ',', '.') ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($os)): ?>
                        <h6>OS Vinculada</h6>
                        <table class="table table-bordered">
                            <tr><th width="35%">OS</th><td>#<?= $os->idOs ?? '' ?></td></tr>
                            <tr><th>Cliente</th><td><?= e($os->nomeCliente ?? '') ?></td></tr>
                            <tr><th>Status OS</th><td><?= e($os->status ?? '') ?></td></tr>
                        </table>
                        <?php endif; ?>

                        <?php if (!empty($boleto)): ?>
                        <h6>Boleto Vinculado</h6>
                        <table class="table table-bordered">
                            <tr><th width="35%">Boleto</th><td>#<?= $boleto->id ?? '' ?></td></tr>
                            <tr><th>Valor</th><td>R$ <?= number_format(($boleto->valor ?? 0), 2, ',', '.') ?></td></tr>
                            <tr><th>Vencimento</th><td><?= $boleto->vencimento ?? '' ?></td></tr>
                            <tr><th>Status</th><td><?= e($boleto->status ?? '') ?></td></tr>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>