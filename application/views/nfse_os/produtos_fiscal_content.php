<?php
/**
 * Conteúdo Produtos - Tabela fiscal de produtos da OS
 * Usado como sub-aba dentro de Notas Fiscais
 */

$produtos = $produtos ?? [];
$totalProdutos = $totalProdutos ?? 0;
$tributacao = $tributacao ?? [];
$result = $result ?? null;
?>

<div class="row-fluid" style="margin-top: 20px;">

    <?php if (!empty($produtos)): ?>
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-box"></i></span>
                <h5>Produtos da OS #<?= $result->idOs ?? '' ?></h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Cod. Barra</th>
                            <th>Unidade</th>
                            <th class="text-right">Qtd</th>
                            <th class="text-right">Valor Unit.</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p->descricao ?? $p->nomeProduto ?? 'Produto') ?></td>
                            <td><?= $p->codDeBarra ?? '---' ?></td>
                            <td><?= $p->unidade ?? 'UN' ?></td>
                            <td class="text-right"><?= $p->quantidade ?? 1 ?></td>
                            <td class="text-right">R$ <?= number_format($p->preco ?: ($p->subTotal / max(1, $p->quantidade)), 2, ',', '.') ?></td>
                            <td class="text-right">R$ <?= number_format($p->subTotal, 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Produtos:</strong></td>
                            <td class="text-right"><strong>R$ <?= number_format(floatval($totalProdutos), 2, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php if (!empty($tributacao)): ?>
    <div class="span12" style="margin-top: 10px;">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-calculator"></i></span>
                <h5>Informações Tributárias</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span6">
                        <div class="well well-small">
                            <strong>Código Tributação LC 116:</strong> <?= $tributacao['codigo_tributacao_nacional'] ?? '---' ?><br>
                            <strong>Código Municipal:</strong> <?= $tributacao['codigo_tributacao_municipal'] ?? '---' ?><br>
                            <strong>Alíquota ISS:</strong> <?= $tributacao['aliquota_iss'] ?? '---' ?>%
                        </div>
                    </div>
                    <div class="span6">
                        <div class="well well-small">
                            <strong>Descrição Serviço:</strong><br>
                            <small><?= htmlspecialchars($tributacao['descricao_servico'] ?? '---') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="span12">
        <div class="alert alert-info">
            <i class="bx bx-info-circle"></i> Nenhum produto vinculado a esta OS.
        </div>
    </div>
    <?php endif; ?>

</div>