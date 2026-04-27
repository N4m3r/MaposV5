<?php
/**
 * Dashboard de NFS-e e Boletos
 * Visão geral de notas fiscais e cobranças vinculadas a OS
 */
?>

<link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-receipt"></i></span>
                <h5>Dashboard NFS-e e Boletos</h5>
                <div class="buttons">
                    <a href="<?= site_url('nfse_os/relatorio') ?>" class="button btn btn-mini btn-primary">
                        <span class="button__icon"><i class="bx bx-chart"></i></span>
                        <span class="button__text">Relatório Completo</span>
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Cards de Resumo -->
                <div class="row-fluid">
                    <!-- NFS-e -->
                    <div class="span6">
                        <div class="widget-box" style="border-left: 4px solid #5bc0de;">
                            <div class="widget-title" style="background: #f0f9fc;">
                                <span class="icon" style="color: #5bc0de;"><i class="bx bx-file"></i></span>
                                <h5>NFS-e - Mês Atual</h5>
                            </div>
                            <div class="widget-content">
                                <div class="row-fluid">
                                    <?php
                                    $total_nfse = 0;
                                    $emitidas = 0;
                                    $pendentes = 0;
                                    foreach ($resumo_nfse as $r) {
                                        $total_nfse += $r->valor_total;
                                        if ($r->situacao == 'Emitida') $emitidas = $r->total;
                                        if ($r->situacao == 'Pendente') $pendentes = $r->total;
                                    }
                                    ?>
                                    <div class="span4 text-center" style="border-right: 1px solid #eee;">
                                        <h3 style="margin: 0; color: #5bc0de;"><?= $emitidas ?></h3>
                                        <small>Emitidas</small>
                                    </div>
                                    <div class="span4 text-center" style="border-right: 1px solid #eee;">
                                        <h3 style="margin: 0; color: #f0ad4e;"><?= $pendentes ?></h3>
                                        <small>Pendentes</small>
                                    </div>
                                    <div class="span4 text-center">
                                        <h3 style="margin: 0; color: #5cb85c;">R$ <?= number_format($total_nfse, 2, ',', '.') ?></h3>
                                        <small>Valor Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boletos -->
                    <div class="span6">
                        <div class="widget-box" style="border-left: 4px solid #5cb85c;">
                            <div class="widget-title" style="background: #f0fff0;">
                                <span class="icon" style="color: #5cb85c;"><i class="bx bx-barcode"></i></span>
                                <h5>Boletos - Mês Atual</h5>
                            </div>
                            <div class="widget-content">
                                <div class="row-fluid">
                                    <?php
                                    $total_boleto = 0;
                                    $pagos = 0;
                                    $vencidos = 0;
                                    foreach ($resumo_boleto as $r) {
                                        $total_boleto += $r->valor_total;
                                        if ($r->status == 'Pago') $pagos = $r->total;
                                        if ($r->status == 'Vencido') $vencidos = $r->total;
                                    }
                                    ?>
                                    <div class="span4 text-center" style="border-right: 1px solid #eee;">
                                        <h3 style="margin: 0; color: #5cb85c;"><?= $pagos ?></h3>
                                        <small>Pagos</small>
                                    </div>
                                    <div class="span4 text-center" style="border-right: 1px solid #eee;">
                                        <h3 style="margin: 0; color: #d9534f;"><?= $vencidos ?></h3>
                                        <small>Vencidos</small>
                                    </div>
                                    <div class="span4 text-center">
                                        <h3 style="margin: 0; color: #5cb85c;">R$ <?= number_format($total_boleto, 2, ',', '.') ?></h3>
                                        <small>Valor Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boletos Vencidos Alert -->
                <?php if (!empty($vencidos)) { ?>
                <div class="row-fluid" style="margin-top: 20px;">
                    <div class="span12">
                        <div class="alert alert-danger">
                            <strong><i class="bx bx-error-circle"></i> Atenção!</strong>
                            Existem <strong><?= count($vencidos) ?></strong> boleto(s) vencido(s) não pagos.
                            <a href="<?= site_url('nfse_os/relatorio?vencidos=1') ?>" class="btn btn-mini btn-danger pull-right">Ver Todos</a>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Tabela de Boletos Vencidos -->
                <?php if (!empty($vencidos)) { ?>
                <div class="row-fluid" style="margin-top: 20px;">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title" style="background: #f2dede;">
                                <span class="icon" style="color: #a94442;"><i class="bx bx-time"></i></span>
                                <h5>Boletos Vencidos</h5>
                            </div>
                            <div class="widget-content nopadding">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>OS</th>
                                            <th>Cliente</th>
                                            <th>Vencimento</th>
                                            <th class="text-right">Valor</th>
                                            <th>Dias em Atraso</th>
                                            <th>Contato</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vencidos as $boleto) {
                                            $dias_atraso = (strtotime(date('Y-m-d')) - strtotime($boleto->data_vencimento)) / (60 * 60 * 24);
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?= site_url('os/visualizar/' . $boleto->os_id) ?>" target="_blank">
                                                    #<?= sprintf('%04d', $boleto->os_id) ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($boleto->nomeCliente, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= date('d/m/Y', strtotime($boleto->data_vencimento)) ?></td>
                                            <td class="text-right">R$ <?= number_format($boleto->valor_liquido, 2, ',', '.') ?></td>
                                            <td>
                                                <span class="label label-important"><?= intval($dias_atraso) ?> dias</span>
                                            </td>
                                            <td><?= htmlspecialchars($boleto->celular_cliente ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="text-center">
                                                <a href="<?= site_url('os/visualizar/' . $boleto->os_id) ?>" class="btn btn-mini btn-info" title="Ver OS">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <?php if ($boleto->linha_digitavel) { ?>
                                                <button class="btn btn-mini btn-primary" onclick="copiar('<?= htmlspecialchars($boleto->linha_digitavel, ENT_QUOTES, 'UTF-8') ?>')" title="Copiar Linha Digitável">
                                                    <i class="bx bx-copy"></i>
                                                </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Dicas -->
                <div class="row-fluid" style="margin-top: 20px;">
                    <div class="span12">
                        <div class="alert alert-info">
                            <h5><i class="bx bx-info-circle"></i> Fluxo Recomendado:</h5>
                            <ol style="margin: 10px 0 0 20px;">
                                <li>Acesse a <strong>Ordem de Serviço</strong> que deseja faturar</li>
                                <li>Clique em <strong>"Emitir NFS-e"</strong> para calcular e emitir a nota fiscal</li>
                                <li>Após emitir a nota, clique em <strong>"Gerar Boleto"</strong> com o valor líquido</li>
                                <li>Acompanhe o pagamento pelo <strong>Relatório</strong> ou diretamente na OS</li>
                            </ol>
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
        alert('Linha digitável copiada!');
    });
}
</script>
