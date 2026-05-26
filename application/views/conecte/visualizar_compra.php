<?php $totalProdutos = 0; ?>
<div class="row" style="margin-top: 0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <h5>Venda</h5>
                <div class="buttons">

                    <a id="imprimir" target="_blank" title="Imprimir" class="btn btn-sm btn-inverse" href="<?php echo base_url('index.php/mine/imprimirCompra/' . $result->idVendas); ?>"><i class="fas fa-print"></i> Imprimir</a>
                </div>
            </div>
            <div class="widget-content" id="printOs">
                <div class="invoice-content">
                    <div class="invoice-head">
                        <table class="table">
                            <tbody>

                                <?php if ($emitente == null) { ?>

                                    <tr>
                                        <td colspan="3" class="alert">Os dados do emitente não foram configurados. </td>
                                    </tr>
                                <?php
                                } else { ?>

                                    <tr>
                                        <td style="width: 25%"><img src=" <?php echo e($emitente->url_logo); ?> "></td>
                                        <td> <span style="font-size: 20px; ">
                                                <?php echo e($emitente->nome); ?></span> </br><span>
                                                <?php echo e($emitente->cnpj); ?> </br>
                                                <?php echo e($emitente->rua) . ', nº:' . e($emitente->numero) . ', ' . e($emitente->bairro) . ' - ' . e($emitente->cidade) . ' - ' . e($emitente->uf); ?> </span> </br> <span> E-mail:
                                                <?php echo e($emitente->email) . ' - Fone: ' . e($emitente->telefone); ?> </br>
                                                Responsável: <?php echo e($result->nome) ?></span></td>
                                        <td style="width: 18%; text-align: center">#Venda: <span>
                                                <?php echo $result->idVendas ?></span></br> </br> <span>Emissão:
                                                <?php echo date('d/m/Y'); ?></span></td>
                                    </tr>

                                <?php
                                } ?>
                            </tbody>
                        </table>

                        <table class="table">
                            <tbody>
                                <tr>
                                <td style="width: 50%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5>Cliente</h5>
                                                    <span>
                                                        <?php echo e($result->nomeCliente) ?>
                                                    </span><br />
                                                    <span>
                                                        <?php echo e($result->rua) ?>, <?php echo e($result->numero) ?>, <?php echo e($result->bairro) ?>
                                                    </span><br/>
                                                    <span>
                                                        <?php echo e($result->cidade) ?> - <?php echo e($result->estado) ?> - CEP: <?php echo e($result->cep) ?>
                                                    </span><br/>
                                                    <span>
                                                        Email: <?php echo e($result->emailCliente) ?>
                                                    </span></br>
                                                    <?php if ($result->contato) { ?>
                                                        <span>Contato: <?php echo e($result->contato) ?> </span>
                                                    <?php } ?>
                                                    <span>Celular: <?php echo e($result->celular) ?></span>
							                    </span>
                                            </li>
                                        </ul>
                                    </td>
                                    <?php if ($qrCode) : ?>
                                        <td style="width: 12%; padding: 0;text-align:center;">
                                            <img style="margin:12px 0px 0px 0px" src="<?php echo base_url(); ?>assets/img/logo_pix.png" width="64px" alt="QR Code de Pagamento" /></br>
                                            <img style="margin:5px 0px 0px 0px" width="94px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></br>
                                            <?php echo '<span style="margin:0px;font-size: 80%;text-align:center;">Chave PIX: ' . e($chaveFormatada) . '</span>' ;?>
                                        </td>
                                    <?php endif ?>
                                </tr>
                            </tbody>
                        </table>
                    
                    <div style="margin-top: 0; padding-top: 0">
                        <table class="table table-sm">
                            <tbody>
                                <?php if ($result->dataVenda != null) { ?>
                                    <tr>
                                        <td>
                                            <b>Status Venda: </b><?php echo e($result->status) ?>
                                        </td>

                                        <td>
                                            <b>Data da Venda: </b><?php echo date('d/m/Y', strtotime($result->dataVenda)); ?>
                                        </td>

                                        <td>
                                            <?php if ($result->garantia) { ?>
                                                <b>Garantia: </b><?php echo e($result->garantia) . ' dia(s)'; ?>
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <?php if ($result->status == 'Finalizado' || $result->status == 'Faturado') { ?>
                                                <b>Venc. da Garantia: </b><?php echo dateInterval($result->dataVenda, $result->garantia); ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="4"> 
                                            <b>Observações: </b>
                                        <?php echo htmlspecialchars_decode($result->observacoes_cliente) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                    <div style="margin-top: 0; padding-top: 0">
                        <?php if ($produtos != null) { ?>

                            <table class="table table-bordered table-sm" id="tblProdutos" style="margin-top: 0; padding-top: 0">
                                <thead>
                                    <tr>
                                        <th style="font-size: 15px">Produto</th>
                                        <th style="font-size: 15px">Quantidade</th>
                                        <th style="font-size: 15px">Sub-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    foreach ($produtos as $p) {
                                        $totalProdutos = $totalProdutos + $p->subTotal;
                                        echo '<tr>';
                                        echo '<td>' . e($p->descricao) . '</td>';
                                        echo '<td>' . $p->quantidade . '</td>';

                                        echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>

                                    <tr>
                                        <td colspan="2" style="text-align: right"><strong>Total:</strong></td>
                                        <td><strong>R$
                                                <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php
                        } ?>

                        <hr />

                        <h4 style="text-align: right">Valor Total: R$
                            <?php echo number_format($totalProdutos, 2, ',', '.'); ?>
                        </h4>
                        <?php if ($result->valor_desconto != 0 || $result->valor_desconto != 0) { ?>
                        <h4 style="text-align: right">Desconto: R$
                            <?php echo number_format($result->valor_desconto - $totalProdutos, 2, ',', '.'); ?>
                        </h4>
                        <h4 style="text-align: right">Total Com Desconto: R$
                            <?php echo number_format($result->valor_desconto, 2, ',', '.'); ?>
                        </h4>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
