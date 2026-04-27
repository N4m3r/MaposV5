<?php
/**
 * Relatório de NFS-e e Boletos
 * Filtros e listagem completa
 */
?>

<link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-chart"></i></span>
                <h5>Relatório de NFS-e e Boletos</h5>
                <div class="buttons">
                    <a href="<?= site_url('nfse_os') ?>" class="button btn btn-mini btn-info">
                        <span class="button__icon"><i class="bx bx-dashboard"></i></span>
                        <span class="button__text">Dashboard</span>
                    </a>
                </div>
            </div>

            <div class="widget-content">

                <!-- Filtros -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12">
                        <form method="get" action="<?= site_url('nfse_os/relatorio') ?>" class="form-inline">
                            <div class="span3">
                                <label>Período:</label>
                                <input type="date" name="data_inicio" value="<?= $filtros['data_inicio'] ?>" class="input-small" style="width: 42%;">
                                <span> até </span>
                                <input type="date" name="data_fim" value="<?= $filtros['data_fim'] ?>" class="input-small" style="width: 42%;">
                            </div>

                            <div class="span2">
                                <label>Status NFSe:</label>
                                <select name="status_nfse" class="input-medium">
                                    <option value="">Todos</option>
                                    <option value="Pendente" <?= $filtros['status_nfse'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="Emitida" <?= $filtros['status_nfse'] == 'Emitida' ? 'selected' : '' ?>>Emitida</option>
                                    <option value="Cancelada" <?= $filtros['status_nfse'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                </select>
                            </div>

                            <div class="span2">
                                <label>Status Boleto:</label>
                                <select name="status_boleto" class="input-medium">
                                    <option value="">Todos</option>
                                    <option value="Pendente" <?= $filtros['status_boleto'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="Emitido" <?= $filtros['status_boleto'] == 'Emitido' ? 'selected' : '' ?>>Emitido</option>
                                    <option value="Pago" <?= $filtros['status_boleto'] == 'Pago' ? 'selected' : '' ?>>Pago</option>
                                    <option value="Vencido" <?= $filtros['status_boleto'] == 'Vencido' ? 'selected' : '' ?>>Vencido</option>
                                    <option value="Cancelado" <?= $filtros['status_boleto'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>

                            <div class="span3">
                                <button type="submit" class="btn btn-primary" style="margin-top: 24px;">
                                    <i class="bx bx-search"></i> Filtrar
                                </button>

                                <a href="<?= site_url('nfse_os/relatorio') ?>" class="btn btn-default" style="margin-top: 24px;">
                                    <i class="bx bx-reset"></i> Limpar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Resumo Estatístico -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span3">
                        <div class="alert alert-success" style="margin: 0;">
                            <strong>Total NFSe Emitida</strong>
                            <br>
                            <h4 style="margin: 5px 0;">R$ <?= number_format($total_nfse_emitida, 2, ',', '.') ?></h4>
                        </div>
                    </div>

                    <div class="span3">
                        <div class="alert alert-info" style="margin: 0;">
                            <strong>Total Boleto Pago</strong>
                            <br>
                            <h4 style="margin: 5px 0;">R$ <?= number_format($total_boleto_pago, 2, ',', '.') ?></h4>
                        </div>
                    </div>

                    <div class="span3">
                        <div class="alert alert-warning" style="margin: 0;">
                            <strong>Boletos Vencidos</strong>
                            <br>
                            <h4 style="margin: 5px 0;"><?= count($vencidos) ?></h4>
                        </div>
                    </div>

                    <div class="span3">
                        <div class="alert alert-danger" style="margin: 0;">
                            <strong>Valor Vencido</strong>
                            <br>
                            <h4 style="margin: 5px 0;">
                                R$ <?= number_format(array_sum(array_column($vencidos, 'valor_liquido')), 2, ',', '.') ?>
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Tabela de NFS-e -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-file"></i></span>
                                <h5>NFS-e Emitidas (<?= count($nfse_lista) ?>)</h5>
                            </div>

                            <div class="widget-content nopadding">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>OS</th>
                                            <th>Número NFSe</th>
                                            <th>Cliente</th>
                                            <th>Data Emissão</th>
                                            <th class="text-right">Valor Serviços</th>
                                            <th class="text-right">Impostos</th>
                                            <th class="text-right">Valor Líquido</th>
                                            <th>Status</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (empty($nfse_lista)) { ?>
                                        <tr>
                                            <td colspan="9" class="text-center" style="padding: 20px;">
                                                <i class="bx bx-inbox" style="font-size: 24px; color: #999;"></i>
                                                <br>Nenhuma NFS-e encontrada para o período.
                                            </td>
                                        </tr>
                                        <?php } else { ?>
                                        <?php foreach ($nfse_lista as $nfse) { ?>
                                        <tr>
                                            <td>
                                                <a href="<?= site_url('os/visualizar/' . $nfse->os_id) ?>" target="_blank">
                                                    #<?= sprintf('%04d', $nfse->os_id) ?>
                                                </a>
                                            </td>

                                            <td><?= htmlspecialchars($nfse->numero_nfse ?: 'Pendente', ENT_QUOTES, 'UTF-8') ?></td>

                                            <td><?= htmlspecialchars($nfse->nomeCliente, ENT_QUOTES, 'UTF-8') ?></td>

                                            <td><?= date('d/m/Y', strtotime($nfse->data_emissao)) ?></td>

                                            <td class="text-right">R$ <?= number_format($nfse->valor_servicos, 2, ',', '.') ?></td>

                                            <td class="text-right">
                                                <small>R$ <?= number_format($nfse->valor_total_impostos, 2, ',', '.') ?></small>
                                                <br>
                                                <small class="muted">(ISS: <?= htmlspecialchars($nfse->aliquota_iss, ENT_QUOTES, 'UTF-8') ?>%)</small>
                                            </td>

                                            <td class="text-right">
                                                <strong>R$ <?= number_format($nfse->valor_liquido, 2, ',', '.') ?></strong>
                                            </td>

                                            <td>
                                                <?php
                                                $label_class = 'label';
                                                switch ($nfse->situacao) {
                                                    case 'Emitida':
                                                        $label_class .= ' label-success';
                                                        break;
                                                    case 'Pendente':
                                                        $label_class .= ' label-warning';
                                                        break;
                                                    case 'Cancelada':
                                                        $label_class .= ' label-important';
                                                        break;
                                                }
                                                ?>
                                                <span class="<?= htmlspecialchars($label_class, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($nfse->situacao, ENT_QUOTES, 'UTF-8') ?></span>
                                            </td>

                                            <td class="text-center">
                                                <a href="<?= site_url('os/visualizar/' . $nfse->os_id) ?>" class="btn btn-mini btn-info" title="Ver OS">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <?php if ($nfse->xml_path) { ?>
                                                <a href="<?= base_url(htmlspecialchars($nfse->xml_path, ENT_QUOTES, 'UTF-8')) ?>" target="_blank" class="btn btn-mini btn-success" title="XML">
                                                    <i class="bx bx-code"></i>
                                                </a>
                                                <?php } ?>

                                                <?php if ($nfse->link_impressao) { ?>
                                                <a href="<?= htmlspecialchars($nfse->link_impressao, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-mini btn-inverse" title="Imprimir">
                                                    <i class="bx bx-printer"></i>
                                                </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Boletos -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="bx bx-barcode"></i></span>
                                <h5>Boletos Gerados (<?= count($boletos_lista) ?>)</h5>
                            </div>

                            <div class="widget-content nopadding">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>OS</th>
                                            <th>Cliente</th>
                                            <th>Nosso Número</th>
                                            <th>Emissão</th>
                                            <th>Vencimento</th>
                                            <th class="text-right">Valor</th>
                                            <th>Pagamento</th>
                                            <th>Status</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (empty($boletos_lista)) { ?>
                                        <tr>
                                            <td colspan="9" class="text-center" style="padding: 20px;">
                                                <i class="bx bx-inbox" style="font-size: 24px; color: #999;"></i>
                                                <br>Nenhum boleto encontrado para o período.
                                            </td>
                                        </tr>
                                        <?php } else { ?>
                                        <?php foreach ($boletos_lista as $boleto) { ?>
                                        <tr>
                                            <td>
                                                <a href="<?= site_url('os/visualizar/' . $boleto->os_id) ?>" target="_blank">
                                                    #<?= sprintf('%04d', $boleto->os_id) ?>
                                                </a>
                                            </td>

                                            <td><?= $boleto->nomeCliente ?></td>

                                            <td><?= $boleto->nosso_numero ?: 'Pendente' ?></td>

                                            <td><?= date('d/m/Y', strtotime($boleto->data_emissao)) ?></td>

                                            <td><?= date('d/m/Y', strtotime($boleto->data_vencimento)) ?></td>

                                            <td class="text-right">
                                                <strong>R$ <?= number_format($boleto->valor_liquido, 2, ',', '.') ?></strong>
                                                <?php if ($boleto->valor_desconto_impostos > 0) { ?>
                                                <br>
                                                <small class="muted">(Desc. Impostos: R$ <?= number_format($boleto->valor_desconto_impostos, 2, ',', '.') ?>)</small>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if ($boleto->data_pagamento) { ?>
                                                <?= date('d/m/Y', strtotime($boleto->data_pagamento)) ?>
                                                <br>
                                                <small class="text-success">R$ <?= number_format($boleto->valor_pago, 2, ',', '.') ?></small>
                                                <?php } else { ?>
                                                <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php
                                                $label_class = 'label';
                                                switch ($boleto->status) {
                                                    case 'Pago':
                                                        $label_class .= ' label-success';
                                                        break;
                                                    case 'Emitido':
                                                        $label_class .= ' label-info';
                                                        break;
                                                    case 'Pendente':
                                                        $label_class .= ' label-warning';
                                                        break;
                                                    case 'Vencido':
                                                        $label_class .= ' label-important';
                                                        break;
                                                    case 'Cancelado':
                                                        $label_class .= '';
                                                        break;
                                                }
                                                ?>
                                                <span class="<?= $label_class ?>"><?= $boleto->status ?></span>
                                            </td>

                                            <td class="text-center">
                                                <a href="<?= site_url('os/visualizar/' . $boleto->os_id) ?>" class="btn btn-mini btn-info" title="Ver OS">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <?php if ($boleto->linha_digitavel) { ?>
                                                <button class="btn btn-mini btn-primary" onclick="copiar('<?= $boleto->linha_digitavel ?>')" title="Copiar Linha Digitável">
                                                    <i class="bx bx-copy"></i>
                                                </button>
                                                <?php } ?>

                                                <?php if ($boleto->pdf_path) { ?>
                                                <a href="<?= base_url($boleto->pdf_path) ?>" target="_blank" class="btn btn-mini btn-inverse" title="PDF">
                                                    <i class="bx bx-file"></i>
                                                </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function copiar(texto) {
    navigator.clipboard.writeText(texto).then(function() {
        alert('Copiado para a área de transferência!');
    });
}
</script>
