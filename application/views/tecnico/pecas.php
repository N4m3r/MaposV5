<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="bx bx-package"></i> Peças Utilizadas</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($pecas)): ?>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Peça</th>
                            <th>Código</th>
                            <th>Quantidade</th>
                            <th>Valor Unitário</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pecas as $peca): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($peca->nome_peca ?? $peca->descricao ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($peca->codigo ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($peca->quantidade ?? 0); ?></td>
                            <td>R$ <?php echo number_format($peca->valor_unitario ?? 0, 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($peca->valor_total ?? 0, 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="alert alert-info">Nenhuma peça registrada para esta OS.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>