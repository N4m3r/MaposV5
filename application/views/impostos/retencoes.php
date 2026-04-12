<?php
/**
 * Lista de Retenções de Impostos
 */
?>

<!-- Header -->
<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?= site_url('dashboard') ?>">Dashboard</a> <span class="divider">/</span></li>
            <li><a href="<?= site_url('impostos') ?>">Impostos</a> <span class="divider">/</span></li>
            <li class="active">Retenções</li>
        </ul>
    </div>
</div>

<!-- Filtros e Totais -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-filter"></i></span>
                <h5>Período e Totais</h5>
                <div class="buttons">
                    <a href="<?= site_url('impostos/exportar?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?>" class="btn btn-success btn-small">
                        <i class="fas fa-download"></i> Exportar
                    </a>
                </div>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span3">
                        <form method="get" action="<?= site_url('impostos/retencoes') ?>">
                            <label>Data Início:</label>
                            <input type="date" name="data_inicio" class="span12" value="<?= $data_inicio ?>" />
                    </div>
                    <div class="span3">
                            <label>Data Fim:</label>
                            <input type="date" name="data_fim" class="span12" value="<?= $data_fim ?>" />
                    </div>
                    <div class="span2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary span12">Filtrar</button>
                        </form>
                    </div>
                    <div class="span4">
                        <div class="well well-small text-center">
                            <strong>Total Retido no Período:</strong> <br>
                            <span style="font-size: 24px; color: #e74c3c;">R$ <?= number_format($totais->total_impostos ?: 0, 2, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Retenções -->
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-list"></i></span>
                <h5>Retenções Realizadas</h5>
            </div>
            <div class="widget-content nopadding">
                <?php if (empty($retencoes)): ?>
                <div class="alert alert-info" style="margin: 20px;">
                    <i class="fas fa-info-circle"></i> Nenhuma retenção encontrada no período.
                </div>
                <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th class="text-right">Valor Bruto</th>
                            <th class="text-right">Impostos</th>
                            <th class="text-right">Líquido</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($retencoes as $r): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($r->data_retencao)) ?></td>
                            <td><?= $r->nomeCliente ?></td>
                            <td>
                                <?= $r->charge_id ? 'Boleto #' . $r->charge_id : '' ?>
                                <?= $r->nota_fiscal ? '<br>NF: ' . $r->nota_fiscal : '' ?>
                            </td>
                            <td class="text-right">R$ <?= number_format($r->valor_bruto, 2, ',', '.') ?></td>
                            <td class="text-right" style="color: #e74c3c;">R$ <?= number_format($r->total_impostos, 2, ',', '.') ?></td>
                            <td class="text-right">R$ <?= number_format($r->valor_liquido, 2, ',', '.') ?></td>
                            <td class="text-center">
                                <span class="label label-<?= strtolower($r->status) == 'retido' ? 'important' : ($r->status == 'Recolhido' ? 'success' : 'default') ?>">
                                    <?= $r->status ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-mini btn-info" title="Ver Detalhes" onclick="verDetalhes(<?= $r->id ?>); return false;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($r->status == 'Retido'): ?>
                                <a href="<?= site_url('impostos/atualizar_status/' . $r->id) ?>" class="btn btn-mini btn-success" title="Marcar como Recolhido"
                                   onclick="return confirm('Confirmar que este imposto foi recolhido ao governo?')">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div id="modal-detalhes" class="modal hide">
    <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button">×</button>
        <h3>Detalhes da Retenção</h3>
    </div>
    <div class="modal-body">
        <!-- Preenchido via JS -->
    </div>
    <div class="modal-footer">
        <a data-dismiss="modal" class="btn" href="#">Fechar</a>
    </div>
</div>
