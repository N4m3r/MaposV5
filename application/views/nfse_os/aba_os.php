<?php
/**
 * Aba de NFSe e Boleto na Visualização da OS
 * Incluir no arquivo visualizarOs.php
 */
?>

<!-- Aba de Documentos Fiscais -->
<div class="tab-pane" id="tab-documentos-fiscais">
    <div class="row-fluid" style="margin-top: 20px;">

        <!-- Card da NFS-e -->
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-file-invoice"></i></span>
                    <h5>NFS-e - Nota Fiscal de Serviço</h5>
                </div>

                <div class="widget-content">
                    <?php if ($nfse_atual): ?>

                        <div class="alert alert-<?= $nfse_atual->situacao == 'Emitida' ? 'success' : ($nfse_atual->situacao == 'Pendente' ? 'warning' : 'danger') ?>">
                            <div class="row-fluid">
                                <div class="span6">
                                    <strong>Status:</strong> <span class="label label-<?= $nfse_atual->situacao == 'Emitida' ? 'success' : ($nfse_atual->situacao == 'Pendente' ? 'warning' : 'danger') ?>"><?= $nfse_atual->situacao ?></span><br>
                                    <strong>Número:</strong> <?= $nfse_atual->numero_nfse ?: 'Pendente' ?><br>
                                    <strong>Data Emissão:</strong> <?= $nfse_atual->data_emissao ? date('d/m/Y H:i', strtotime($nfse_atual->data_emissao)) : '---' ?>
                                </div>
                                <div class="span6 text-right">
                                    <strong>Valor Serviços:</strong> R$ <?= number_format($nfse_atual->valor_servicos, 2, ',', '.') ?><br>
                                    <strong>Impostos:</strong> R$ <?= number_format($nfse_atual->valor_total_impostos, 2, ',', '.') ?><br>
                                    <strong>Valor Líquido:</strong> R$ <?= number_format($nfse_atual->valor_liquido, 2, ',', '.') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Detalhamento dos Impostos -->
                        <h6><i class="fas fa-calculator"></i> Detalhamento dos Impostos</h6>
                        <div class="well well-small">
                            <div class="row-fluid">
                                <div class="span6">
                                    <strong>ISS (<?= $nfse_atual->aliquota_iss ?>%):</strong> R$ <?= number_format($nfse_atual->valor_iss, 2, ',', '.') ?><br>
                                    <strong>PIS:</strong> R$ <?= number_format($nfse_atual->valor_pis, 2, ',', '.') ?><br>
                                    <strong>COFINS:</strong> R$ <?= number_format($nfse_atual->valor_cofins, 2, ',', '.') ?>
                                </div>
                                <div class="span6">
                                    <strong>IRRF:</strong> R$ <?= number_format($nfse_atual->valor_irrf, 2, ',', '.') ?><br>
                                    <strong>CSLL:</strong> R$ <?= number_format($nfse_atual->valor_csll, 2, ',', '.') ?><br>
                                    <strong>INSS:</strong> R$ <?= number_format($nfse_atual->valor_inss, 2, ',', '.') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="btn-group">
                            <?php if ($nfse_atual->link_impressao): ?>
                                <a href="<?= $nfse_atual->link_impressao ?>" target="_blank" class="btn btn-success">
                                    <i class="fas fa-print"></i> Imprimir NFS-e
                                </a>
                            <?php endif; ?>

                            <?php if ($nfse_atual->xml_path): ?>
                                <a href="<?= base_url($nfse_atual->xml_path) ?>" target="_blank" class="btn btn-info">
                                    <i class="fas fa-file-code"></i> Download XML
                                </a>
                            <?php endif; ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNFSe') && $nfse_atual->situacao != 'Cancelada'): ?>
                                <button type="button" class="btn btn-danger" onclick="cancelarNFSe(<?= $nfse_atual->id ?>)">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            <?php endif; ?>
                        </div>

                    <?php else: ?>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Nenhuma NFS-e emitida</strong><br>
                            Clique no botão abaixo para emitir uma NFS-e para esta OS.
                        </div>

                        <!-- Preview de Cálculo -->
                        <div id="preview-calculo" style="display: none;">
                            <div class="well well-small">
                                <h6>Preview do Cálculo de Impostos</h6>
                                <div id="preview-conteudo"></div>
                            </div>
                        </div>

                        <!-- Form de Emissão -->
                        <form id="form-emitir-nfse" method="post" action="<?= site_url('nfse_os/emitir/' . $result->idOs) ?>">
                            <div class="control-group">
                                <label class="control-label">Valor dos Serviços:</label>
                                <div class="controls">
                                    <div class="input-append">
                                        <input type="number" name="valor_servicos" id="valor-servicos"
                                               class="span8" step="0.01" min="0"
                                               value="<?= $result->valorTotal ?? 0 ?>"
                                               onchange="calcularImpostos()">
                                        <span class="add-on">R$</span>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">Deduções:</label>
                                <div class="controls">
                                    <div class="input-append">
                                        <input type="number" name="valor_deducoes" id="valor-deducoes"
                                               class="span8" step="0.01" min="0" value="0"
                                               onchange="calcularImpostos()">
                                        <span class="add-on">R$</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-invoice"></i> Emitir NFS-e
                                </button>

                                <label class="checkbox inline" style="margin-left: 20px;">
                                    <input type="checkbox" name="gerar_boleto" value="1" checked>
                                    Gerar boleto automaticamente
                                </label>
                            </div>
                        </form>

                    <?php endif; ?>

                    <!-- Histórico de NFS-e -->
                    <?php if (!empty($historico_nfse) && count($historico_nfse) > 1): ?>
                        <hr>
                        <h6><i class="fas fa-history"></i> Histórico de NFS-e</h6>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Número</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historico_nfse as $hist): ?>
                                    <?php if ($hist->id != ($nfse_atual->id ?? 0)): ?>
                                        <tr>
                                            <td><?= $hist->id ?></td>
                                            <td><?= $hist->numero_nfse ?: '---' ?></td>
                                            <td><?= $hist->data_emissao ? date('d/m/Y', strtotime($hist->data_emissao)) : '---' ?></td>
                                            <td>R$ <?= number_format($hist->valor_liquido, 2, ',', '.') ?></td>
                                            <td>
                                                <span class="label label-<?= $hist->situacao == 'Emitida' ? 'success' : ($hist->situacao == 'Cancelada' ? 'danger' : 'default') ?>">
                                                    <?= $hist->situacao ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card do Boleto -->
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-barcode"></i></span>
                    <h5>Boleto de Cobrança</h5>
                </div>

                <div class="widget-content">
                    <?php if ($boleto_atual): ?>

                        <div class="alert alert-<?= $boleto_atual->status == 'Pago' ? 'success' : ($boleto_atual->status == 'Vencido' ? 'danger' : 'warning') ?>">
                            <div class="row-fluid">
                                <div class="span6">
                                    <strong>Status:</strong> <span class="label label-<?= $boleto_atual->status == 'Pago' ? 'success' : ($boleto_atual->status == 'Vencido' ? 'danger' : ($boleto_atual->status == 'Emitido' ? 'info' : 'default')) ?>"><?= $boleto_atual->status ?></span><br>
                                    <strong>Nosso Número:</strong> <?= $boleto_atual->nosso_numero ?: '---' ?><br>
                                    <strong>Emissão:</strong> <?= date('d/m/Y', strtotime($boleto_atual->data_emissao)) ?>
                                </div>
                                <div class="span6 text-right">
                                    <strong>Vencimento:</strong> <?= date('d/m/Y', strtotime($boleto_atual->data_vencimento)) ?><br>
                                    <strong>Valor Original:</strong> R$ <?= number_format($boleto_atual->valor_original, 2, ',', '.') ?><br>
                                    <?php if ($boleto_atual->valor_desconto_impostos > 0): ?>
                                        <strong>Desconto Impostos:</strong> R$ <?= number_format($boleto_atual->valor_desconto_impostos, 2, ',', '.') ?><br>
                                    <?php endif; ?>
                                    <strong>Valor Líquido:</strong> R$ <?= number_format($boleto_atual->valor_liquido, 2, ',', '.') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Informações de Pagamento -->
                        <?php if ($boleto_atual->status == 'Pago'): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <strong>Pago em <?= date('d/m/Y', strtotime($boleto_atual->data_pagamento)) ?></strong><br>
                                <strong>Valor Pago:</strong> R$ <?= number_format($boleto_atual->valor_pago, 2, ',', '.') ?>
                                <?php if ($boleto_atual->multa > 0): ?> <br><strong>Multa:</strong> R$ <?= number_format($boleto_atual->multa, 2, ',', '.') ?>
                                <?php endif; ?>
                                <?php if ($boleto_atual->juros > 0): ?> <br><strong>Juros:</strong> R$ <?= number_format($boleto_atual->juros, 2, ',', '.') ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Linha Digitável -->
                        <?php if ($boleto_atual->linha_digitavel): ?>
                            <div class="well well-small">
                                <strong>Linha Digitável:</strong><br>
                                <div class="input-append" style="margin-top: 5px;">
                                    <input type="text" id="linha-digitavel" value="<?= $boleto_atual->linha_digitavel ?>"
                                           class="span10" readonly style="font-family: monospace;">
                                    <button type="button" class="btn" onclick="copiarLinhaDigitavel()">
                                        <i class="fas fa-copy"></i> Copiar
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Botões de Ação -->
                        <div class="btn-group">

                            <?php if ($boleto_atual->pdf_path): ?>
                                <a href="<?= base_url($boleto_atual->pdf_path) ?>" target="_blank" class="btn btn-success">
                                    <i class="fas fa-file-pdf"></i> Visualizar PDF
                                </a>
                            <?php endif; ?>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eBoletoOS')): ?>

                                <?php if ($boleto_atual->status != 'Pago' && $boleto_atual->status != 'Cancelado'): ?>
                                    <button type="button" class="btn btn-primary" onclick="registrarPagamento(<?= $boleto_atual->id ?>)">
                                        <i class="fas fa-check"></i> Registrar Pagamento
                                    </button>

                                    <button type="button" class="btn btn-danger" onclick="cancelarBoleto(<?= $boleto_atual->id ?>)">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>

                                <?php endif; ?>

                                <a href="<?= site_url('nfse_os/enviar_boleto_email/' . $boleto_atual->id) ?>" class="btn btn-info">
                                    <i class="fas fa-envelope"></i> Enviar por Email
                                </a>

                            <?php endif; ?>
                        </div>

                    <?php else: ?>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Nenhum boleto gerado</strong><br>
                            <?php if (!empty($nfse_atual)): ?>
                                Emita uma NFS-e primeiro para gerar o boleto com dedução de impostos.
                            <?php else: ?>
                                Clique no botão abaixo para gerar um boleto para esta OS.
                            <?php endif; ?>
                        </div>

                        <!-- Form de Geração -->
                        <?php if (empty($nfse_atual) && $this->permission->checkPermission($this->session->userdata('permissao'), 'cBoletoOS')): ?>

                            <form method="post" action="<?= site_url('nfse_os/gerar_boleto/' . $result->idOs) ?>">

                                <div class="control-group">
                                    <label class="control-label">Data de Vencimento:</label>
                                    <div class="controls">
                                        <input type="date" name="data_vencimento" class="span8"
                                               value="<?= date('Y-m-d', strtotime('+5 days')) ?>" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Instruções:</label>
                                    <div class="controls">
                                        <textarea name="instrucoes" class="span8" rows="2">Pagável em qualquer banco até o vencimento.</textarea>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-barcode"></i> Gerar Boleto
                                    </button>
                                </div>
                            </form>

                        <?php endif; ?>

                    <?php endif; ?>

                    <!-- Histórico de Boletos -->
                    <?php if (!empty($historico_boleto) && count($historico_boleto) > 1): ?>
                        <hr>
                        <h6><i class="fas fa-history"></i> Histórico de Boletos</h6>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Nosso Número</th>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historico_boleto as $hist): ?>
                                    <?php if ($hist->id != ($boleto_atual->id ?? 0)): ?>
                                        <tr>
                                            <td><?= $hist->id ?></td>
                                            <td><?= $hist->nosso_numero ?: '---' ?></td>
                                            <td><?= date('d/m/Y', strtotime($hist->data_vencimento)) ?></td>
                                            <td>R$ <?= number_format($hist->valor_liquido, 2, ',', '.') ?></td>
                                            <td>
                                                <span class="label label-<?= $hist->status == 'Pago' ? 'success' : ($hist->status == 'Vencido' ? 'danger' : ($hist->status == 'Cancelado' ? 'inverse' : 'info')) ?>">
                                                    <?= $hist->status ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calcularImpostos() {
    const valor = parseFloat(document.getElementById('valor-servicos').value) || 0;
    const deducoes = parseFloat(document.getElementById('valor-deducoes').value) || 0;

    if (valor > 0) {
        document.getElementById('preview-calculo').style.display = 'block';
        document.getElementById('preview-conteudo').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculando...';

        $.ajax({
            url: '<?= site_url("nfse_os/calcular_impostos") ?>',
            type: 'POST',
            data: { valor: valor },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    let html = '<div class="row-fluid">';
                    html += '<div class="span6">';
                    html += '<strong>Valor Bruto:</strong> R$ ' + data.valor_bruto.toFixed(2).replace('.', ',') + '<br>';
                    html += '<strong>Impostos:</strong> R$ ' + data.impostos.valor_total_impostos.toFixed(2).replace('.', ',') + '<br>';
                    html += '<strong>Deduções:</strong> R$ ' + deducoes.toFixed(2).replace('.', ',') + '<br>';
                    html += '</div>';
                    html += '<div class="span6 text-right">';
                    html += '<strong style="font-size: 1.2em;">Valor Líquido:</strong><br>';
                    html += '<span style="font-size: 1.5em; color: green;">R$ ' + data.valor_liquido.toFixed(2).replace('.', ',') + '</span>';
                    html += '</div>';
                    html += '</div>';

                    html += '<hr><div class="row-fluid" style="font-size: 0.9em;">';
                    html += '<div class="span6">ISS: R$ ' + (data.impostos.iss || 0).toFixed(2).replace('.', ',') + '<br>';
                    html += 'PIS: R$ ' + (data.impostos.pis || 0).toFixed(2).replace('.', ',') + '<br>';
                    html += 'COFINS: R$ ' + (data.impostos.cofins || 0).toFixed(2).replace('.', ',') + '</div>';
                    html += '<div class="span6">IRRF: R$ ' + (data.impostos.irrf || 0).toFixed(2).replace('.', ',') + '<br>';
                    html += 'CSLL: R$ ' + (data.impostos.csll || 0).toFixed(2).replace('.', ',') + '</div>';
                    html += '</div>';

                    document.getElementById('preview-conteudo').innerHTML = html;
                }
            },
            error: function() {
                document.getElementById('preview-conteudo').innerHTML = '<div class="alert alert-error">Erro ao calcular impostos</div>';
            }
        });
    } else {
        document.getElementById('preview-calculo').style.display = 'none';
    }
}

function cancelarNFSe(nfseId) {
    const motivo = prompt('Informe o motivo do cancelamento:');
    if (motivo) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/cancelar_nfse/") ?>' + nfseId;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'motivo';
        input.value = motivo;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function cancelarBoleto(boletoId) {
    if (confirm('Tem certeza que deseja cancelar este boleto?')) {
        window.location.href = '<?= site_url("nfse_os/cancelar_boleto/") ?>' + boletoId;
    }
}

function registrarPagamento(boletoId) {
    const data = prompt('Data do pagamento (YYYY-MM-DD):', '<?= date("Y-m-d") ?>');
    if (data) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/registrar_pagamento/") ?>' + boletoId;

        const inputData = document.createElement('input');
        inputData.type = 'hidden';
        inputData.name = 'data_pagamento';
        inputData.value = data;

        form.appendChild(inputData);
        document.body.appendChild(form);
        form.submit();
    }
}

function copiarLinhaDigitavel() {
    const input = document.getElementById('linha-digitavel');
    input.select();
    document.execCommand('copy');
    alert('Linha digitável copiada!');
}

$(document).ready(function() {
    calcularImpostos();
});
</script>
