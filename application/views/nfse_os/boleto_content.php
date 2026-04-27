<?php
/**
 * Conteúdo Boleto - Card de status, formulário de geração e histórico
 * Usado como sub-aba dentro de Notas Fiscais
 */

$nfse_atual = $nfse_atual ?? null;
$boleto_atual = $boleto_atual ?? null;
$historico_boleto = $historico_boleto ?? [];
?>

<div class="row-fluid" style="margin-top: 20px;">

    <?php if (!$nfse_atual && !$boleto_atual): ?>
    <div class="span12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> <strong>Nenhuma NFS-e emitida</strong><br>
            Emita uma NFS-e primeiro para gerar boletos de cobrança.
        </div>
    </div>

    <?php elseif ($boleto_atual): ?>
    <!-- BOLETO JÁ GERADO - Card de status -->
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-barcode"></i></span>
                <h5>Boleto de Cobrança</h5>
            </div>

            <div class="widget-content">
                <div class="alert alert-<?= $boleto_atual->status == 'Pago' ? 'success' : ($boleto_atual->status == 'Vencido' ? 'danger' : 'warning') ?>">
                    <div class="row-fluid">
                        <div class="span6">
                            <strong>Status:</strong> <span class="label label-<?= $boleto_atual->status == 'Pago' ? 'success' : ($boleto_atual->status == 'Vencido' ? 'danger' : ($boleto_atual->status == 'Emitido' ? 'info' : 'default')) ?>"><?= $boleto_atual->status ?></span><br>
                            <strong>Nosso Número:</strong> <?= $boleto_atual->nosso_numero ?: '---' ?><br>
                            <strong>Emissão:</strong> <?= date('d/m/Y', strtotime($boleto_atual->data_emissao)) ?>
                        </div>
                        <div class="span6 text-right">
                            <strong>Vencimento:</strong> <?= $boleto_atual->data_vencimento ? date('d/m/Y', strtotime($boleto_atual->data_vencimento)) : '---' ?><br>
                            <strong>Valor Original:</strong> R$ <?= number_format($boleto_atual->valor_original, 2, ',', '.') ?><br>
                            <?php if ($boleto_atual->valor_desconto_impostos > 0): ?>
                                <strong>Desconto Impostos:</strong> R$ <?= number_format($boleto_atual->valor_desconto_impostos, 2, ',', '.') ?><br>
                            <?php endif; ?>
                            <strong>Valor Líquido:</strong> R$ <?= number_format($boleto_atual->valor_liquido, 2, ',', '.') ?>
                        </div>
                    </div>
                </div>

                <?php if ($boleto_atual->status == 'Pago'): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <strong>Pago<?= $boleto_atual->data_pagamento ? ' em ' . date('d/m/Y', strtotime($boleto_atual->data_pagamento)) : '' ?></strong><br>
                        <strong>Valor Pago:</strong> R$ <?= number_format(floatval($boleto_atual->valor_pago ?? 0), 2, ',', '.') ?>
                        <?php if (floatval($boleto_atual->multa ?? 0) > 0): ?> <br><strong>Multa:</strong> R$ <?= number_format(floatval($boleto_atual->multa), 2, ',', '.') ?>
                        <?php endif; ?>
                        <?php if (floatval($boleto_atual->juros ?? 0) > 0): ?> <br><strong>Juros:</strong> R$ <?= number_format(floatval($boleto_atual->juros), 2, ',', '.') ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($boleto_atual->linha_digitavel): ?>
                    <div class="well well-small">
                        <strong>Linha Digitável:</strong><br>
                        <div class="input-append" style="margin-top: 5px;">
                            <input type="text" id="linha-digitavel" value="<?= htmlspecialchars($boleto_atual->linha_digitavel, ENT_QUOTES, 'UTF-8') ?>"
                                   class="span10" readonly style="font-family: monospace;">
                            <button type="button" class="btn" onclick="copiarLinhaDigitavel()">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="btn-group">
                    <a href="<?= site_url('nfse_os/preview_boleto/' . $boleto_atual->id) ?>" target="_blank" class="btn btn-inverse">
                        <i class="fas fa-print"></i> Preview A4
                    </a>

                    <?php if ($boleto_atual->pdf_path): ?>
                        <a href="<?= base_url(htmlspecialchars($boleto_atual->pdf_path, ENT_QUOTES, 'UTF-8')) ?>" target="_blank" class="btn btn-success">
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
            </div>
        </div>
    </div>

    <?php elseif ($nfse_atual): ?>
    <!-- NFS-e existe mas sem boleto - formulário de geração -->
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-barcode"></i></span>
                <h5>Gerar Boleto de Cobrança</h5>
            </div>

            <div class="widget-content">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Nenhum boleto gerado</strong><br>
                    Gere um boleto vinculado a esta NFS-e. O valor será o integral dos serviços (impostos/DAS do prestador não descontam).
                </div>

                <?php
                $regime = $nfse_atual->regime_tributario ?? 'simples_nacional';
                $isSimples = ($regime === 'simples_nacional');
                $valorOriginal = floatval($nfse_atual->valor_servicos ?? 0);
                $descontoImpostos = floatval($nfse_atual->valor_total_impostos ?? 0);
                $valorLiquido = floatval($nfse_atual->valor_liquido ?? $valorOriginal);
                ?>

                <!-- Resumo Financeiro da NFS-e -->
                <div class="well well-small" style="margin-bottom: 20px; background: #f9f9f9;">
                    <h6 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                        <i class="fas fa-calculator"></i> Resumo Financeiro da NFS-e
                        <span class="label label-<?= $isSimples ? 'success' : 'info' ?>" style="font-size: 11px; margin-left: 5px;">
                            <?= $isSimples ? 'Simples Nacional' : 'Lucro Presumido' ?>
                        </span>
                    </h6>
                    <div class="row-fluid">
                        <div class="span6 text-center" style="border-right: 1px solid #eee;">
                            <h4 style="margin: 5px 0; color: #333;">R$ <?= number_format($valorOriginal, 2, ',', '.') ?></h4>
                            <small style="color: #888;">Valor dos Serviços</small>
                        </div>
                        <div class="span6 text-center">
                            <h4 style="margin: 5px 0; color: #5cb85c;">R$ <?= number_format($valorLiquido, 2, ',', '.') ?></h4>
                            <small style="color: #888;"><strong>Valor Líquido da NFS-e</strong></small>
                        </div>
                    </div>
                    <?php if ($isSimples): ?>
                    <div style="margin-top: 8px; font-size: 11px; color: #666; text-align: center;">
                        <i class="fas fa-info-circle" style="color: #27ae60;"></i>
                        No Simples Nacional, o DAS é recolhido mensalmente pelo prestador e <strong>não desconta</strong> do valor do boleto.
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBoletoOS')): ?>
                    <form method="post" action="<?= site_url('nfse_os/gerar_boleto/' . $result->idOs . '/' . $nfse_atual->id) ?>">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <label class="control-label"><strong>Data de Vencimento:</strong></label>
                                    <div class="controls">
                                        <input type="date" name="data_vencimento" class="span12"
                                               value="<?= date('Y-m-d', strtotime('+5 days')) ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <label class="control-label"><strong>Descrição / Instruções:</strong></label>
                                    <div class="controls">
                                        <textarea name="instrucoes" class="span12" rows="2" placeholder="Instruções que aparecerão no boleto...">Pagável em qualquer banco até o vencimento. Após o vencimento, consultar multas e juros.</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $temRetencao = floatval($nfse_atual->valor_total_retencao ?? 0) > 0;
                        ?>
                        <?php if ($temRetencao): ?>
                        <div class="row-fluid" style="margin-top: 10px;">
                            <div class="span12">
                                <div style="padding: 12px 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
                                    <label class="checkbox" style="font-size: 13px; margin-bottom: 5px;">
                                        <input type="checkbox" name="valor_integral" value="1">
                                        <strong>Emitir boleto com valor integral</strong> (R$ <?= number_format($valorOriginal, 2, ',', '.') ?>)
                                    </label>
                                    <small style="color: #856404; display: block; margin-top: 4px;">
                                        <i class="fas fa-info-circle"></i>
                                        Esta NFS-e possui retenção pelo tomador de R$ <?= number_format($nfse_atual->valor_total_retencao, 2, ',', '.') ?>.
                                        Ao marcar esta opção, o boleto será emitido com o valor integral dos serviços.
                                        As retenções serão registradas para compensação no DAS mensal.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-actions" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-success btn-large">
                                <i class="fas fa-barcode"></i> Gerar Boleto de R$ <?= number_format($valorLiquido, 2, ',', '.') ?>
                            </button>
                            <a href="<?= site_url('os/visualizar/' . $result->idOs) ?>" class="btn">
                                <i class="fas fa-arrow-left"></i> Voltar para OS
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-lock"></i> Você não possui permissão para gerar boletos.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <!-- Histórico de Boletos -->
    <?php if (!empty($historico_boleto) && count($historico_boleto) > 1): ?>
    <div class="span12" style="margin-top: 10px;">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Histórico de Boletos</h5>
            </div>
            <div class="widget-content">
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
                                    <td><?= $hist->data_vencimento ? date('d/m/Y', strtotime($hist->data_vencimento)) : '---' ?></td>
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
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>