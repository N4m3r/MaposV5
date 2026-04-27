<?php
/**
 * Boleto — Sub-aba dentro de Notas Fiscais
 * Tema Dark MapOS
 */

$nfse_atual = $nfse_atual ?? null;
$boleto_atual = $boleto_atual ?? null;
$historico_boleto = $historico_boleto ?? [];

if (!function_exists('fmtMoney')) {
    function fmtMoney($v) {
        return 'R$ ' . number_format(floatval($v), 2, ',', '.');
    }
}
?>

<div class="row-fluid" style="margin-top:0">

    <?php if (!$nfse_atual && !$boleto_atual): ?>
    <div class="span12">
        <div class="alert" style="background:rgba(16,134,221,0.15); border-color:rgba(16,134,221,0.3); color:#1086dd">
            <i class="fas fa-info-circle"></i> <strong>Nenhuma NFS-e emitida</strong><br>
            Emita uma NFS-e primeiro para gerar boletos de cobranca.
        </div>
    </div>

    <?php elseif ($boleto_atual): ?>
    <!-- BOLETO GERADO -->
    <div class="span12">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-barcode" style="color:var(--dark-azul,#1086dd)"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Boleto de Cobranca</h5>
                <?php
                $boletoStatusColor = '#8788a4';
                if ($boleto_atual->status == 'Pago') $boletoStatusColor = '#26a38e';
                elseif ($boleto_atual->status == 'Vencido') $boletoStatusColor = '#fd7670';
                elseif ($boleto_atual->status == 'Emitido') $boletoStatusColor = '#1086dd';
                ?>
                <span class="label" style="margin:8px 10px 0 0; float:right; background:<?= $boletoStatusColor ?>; color:#fff"><?= $boleto_atual->status ?></span>
            </div>
            <div class="widget-content" style="background:var(--wid-dark,#1c1d26); color:var(--branco,#caced8)">
                <div class="row-fluid">
                    <div class="span6">
                        <table class="table table-condensed" style="margin-bottom:0; background:transparent">
                            <tr>
                                <td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Status:</strong></td>
                                <td style="border:none; padding:2px 0"><span class="label" style="background:<?= $boletoStatusColor ?>; color:#fff"><?= $boleto_atual->status ?></span></td>
                            </tr>
                            <tr>
                                <td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Nosso Numero:</strong></td>
                                <td style="border:none; padding:2px 0"><?= $boleto_atual->nosso_numero ?: '---' ?></td>
                            </tr>
                            <tr>
                                <td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Emissao:</strong></td>
                                <td style="border:none; padding:2px 0"><?= $boleto_atual->data_emissao ? date('d/m/Y', strtotime($boleto_atual->data_emissao)) : '---' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="span6 text-right">
                        <table class="table table-condensed" style="margin-bottom:0; background:transparent">
                            <tr>
                                <td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Vencimento:</strong></td>
                                <td style="border:none; padding:2px 0"><?= $boleto_atual->data_vencimento ? date('d/m/Y', strtotime($boleto_atual->data_vencimento)) : '---' ?></td>
                            </tr>
                            <tr>
                                <td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Valor Original:</strong></td>
                                <td style="border:none; padding:2px 0"><?= fmtMoney($boleto_atual->valor_original) ?></td>
                            </tr>
                            <?php if ($boleto_atual->valor_desconto_impostos > 0): ?>
                            <tr>
                                <td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Desconto Impostos:</strong></td>
                                <td style="border:none; padding:2px 0"><?= fmtMoney($boleto_atual->valor_desconto_impostos) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:#62eba6">Valor Liquido:</strong></td>
                                <td style="border:none; padding:2px 0"><strong style="color:#62eba6"><?= fmtMoney($boleto_atual->valor_liquido) ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if ($boleto_atual->status == 'Pago'): ?>
                <hr style="margin:10px 0; border-top:1px solid var(--dark-2,#272835)">
                <div class="alert" style="margin-bottom:0; background:rgba(38,163,142,0.15); border-color:rgba(38,163,142,0.3); color:#62eba6">
                    <i class="fas fa-check-circle"></i> <strong>Pago<?= $boleto_atual->data_pagamento ? ' em ' . date('d/m/Y', strtotime($boleto_atual->data_pagamento)) : '' ?></strong><br>
                    <strong style="color:var(--branco,#caced8)">Valor Pago:</strong> <?= fmtMoney(floatval($boleto_atual->valor_pago ?? 0)) ?>
                    <?php if (floatval($boleto_atual->multa ?? 0) > 0): ?> <br><strong style="color:var(--branco,#caced8)">Multa:</strong> <?= fmtMoney(floatval($boleto_atual->multa)) ?><?php endif; ?>
                    <?php if (floatval($boleto_atual->juros ?? 0) > 0): ?> <br><strong style="color:var(--branco,#caced8)">Juros:</strong> <?= fmtMoney(floatval($boleto_atual->juros)) ?><?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($boleto_atual->linha_digitavel): ?>
                <hr style="margin:10px 0; border-top:1px solid var(--dark-2,#272835)">
                <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                    <strong style="color:var(--title,#d4d8e0)">Linha Digitavel:</strong><br>
                    <div class="input-append" style="margin-top:5px">
                        <input type="text" id="linha-digitavel" value="<?= htmlspecialchars($boleto_atual->linha_digitavel, ENT_QUOTES, 'UTF-8') ?>" class="span10" readonly style="font-family:monospace; background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:#62eba6">
                        <button type="button" class="btn" onclick="copiarLinhaDigitavel()" style="background:var(--dark-2,#272835); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)"><i class="fas fa-copy"></i> Copiar</button>
                    </div>
                </div>
                <?php endif; ?>

                <hr style="margin:10px 0; border-top:1px solid var(--dark-2,#272835)">
                <div class="btn-group">
                    <a href="<?= site_url('nfse_os/preview_boleto/' . $boleto_atual->id) ?>" target="_blank" class="btn btn-inverse" style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)"><i class="fas fa-print"></i> Preview A4</a>
                    <?php if ($boleto_atual->pdf_path): ?>
                        <a href="<?= base_url(htmlspecialchars($boleto_atual->pdf_path, ENT_QUOTES, 'UTF-8')) ?>" target="_blank" class="btn btn-success" style="background:#26a38e; border-color:#1fb5a8"><i class="fas fa-file-pdf"></i> Visualizar PDF</a>
                    <?php endif; ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eBoletoOS')): ?>
                        <?php if ($boleto_atual->status != 'Pago' && $boleto_atual->status != 'Cancelado'): ?>
                            <button type="button" class="btn btn-primary" onclick="registrarPagamento(<?= $boleto_atual->id ?>)" style="background:#1086dd; border-color:#1086dd"><i class="fas fa-check"></i> Registrar Pagamento</button>
                            <button type="button" class="btn btn-danger" onclick="cancelarBoleto(<?= $boleto_atual->id ?>)" style="background:#dc3545; border-color:#dc3545"><i class="fas fa-times"></i> Cancelar</button>
                        <?php endif; ?>
                        <a href="<?= site_url('nfse_os/enviar_boleto_email/' . $boleto_atual->id) ?>" class="btn btn-info" style="background:#52459f; border-color:#52459f"><i class="fas fa-envelope"></i> Enviar Email</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($nfse_atual): ?>
    <!-- FORMULARIO GERAR BOLETO -->
    <div class="span12">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-barcode" style="color:var(--dark-azul,#1086dd)"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Gerar Boleto de Cobranca</h5>
            </div>
            <div class="widget-content" style="background:var(--wid-dark,#1c1d26); color:var(--branco,#caced8)">
                <div class="alert" style="background:rgba(16,134,221,0.15); border-color:rgba(16,134,221,0.3); color:#1086dd">
                    <i class="fas fa-info-circle"></i> Nenhum boleto gerado. O valor sera o integral dos servicos.
                </div>

                <?php
                $regime = $nfse_atual->regime_tributario ?? 'simples_nacional';
                $isSimples = ($regime === 'simples_nacional');
                $valorOriginal = floatval($nfse_atual->valor_servicos ?? 0);
                $valorLiquido = floatval($nfse_atual->valor_liquido ?? $valorOriginal);
                ?>

                <div class="well well-small" style="margin-bottom:20px; background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                    <h6 style="margin-top:0; border-bottom:1px solid var(--dark-2,#272835); padding-bottom:8px; color:var(--title,#d4d8e0)">
                        <i class="fas fa-calculator" style="color:var(--dark-azul,#1086dd)"></i> Resumo Financeiro da NFS-e
                        <span class="label" style="font-size:11px; margin-left:5px; background:<?= $isSimples ? '#1086dd' : '#52459f' ?>; color:#fff"><?= $isSimples ? 'Simples Nacional' : 'Lucro Presumido' ?></span>
                    </h6>
                    <div class="row-fluid">
                        <div class="span6 text-center" style="border-right:1px solid var(--dark-2,#272835)">
                            <h4 style="margin:5px 0; color:var(--title,#d4d8e0)"><?= fmtMoney($valorOriginal) ?></h4>
                            <small style="color:var(--dark-cinz,#8788a4)">Valor dos Servicos</small>
                        </div>
                        <div class="span6 text-center">
                            <h4 style="margin:5px 0; color:#62eba6"><?= fmtMoney($valorLiquido) ?></h4>
                            <small style="color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Valor Liquido da NFS-e</strong></small>
                        </div>
                    </div>
                    <?php if ($isSimples): ?>
                    <div style="margin-top:8px; font-size:11px; color:var(--dark-cinz,#8788a4); text-align:center">
                        <i class="fas fa-info-circle" style="color:#62eba6"></i> No Simples Nacional, o DAS e recolhido mensalmente pelo prestador e <strong style="color:var(--branco,#caced8)">nao desconta</strong> do valor do boleto.
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBoletoOS')): ?>
                    <form method="post" action="<?= site_url('nfse_os/gerar_boleto/' . $result->idOs . '/' . $nfse_atual->id) ?>">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Data de Vencimento:</strong></label>
                                    <div class="controls">
                                        <input type="date" name="data_vencimento" class="span12" value="<?= date('Y-m-d', strtotime('+5 days')) ?>" required style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Descricao / Instrucoes:</strong></label>
                                    <div class="controls">
                                        <textarea name="instrucoes" class="span12" rows="2" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">Pagavel em qualquer banco ate o vencimento. Apos o vencimento, consultar multas e juros.</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $temRetencao = floatval($nfse_atual->valor_total_retencao ?? 0) > 0; ?>
                        <?php if ($temRetencao): ?>
                        <div class="row-fluid" style="margin-top:10px">
                            <div class="span12">
                                <div style="padding:12px 15px; background:rgba(252,157,15,0.15); border:1px solid rgba(252,157,15,0.3); border-radius:4px">
                                    <label class="checkbox" style="font-size:13px; margin-bottom:5px; color:var(--branco,#caced8)">
                                        <input type="checkbox" name="valor_integral" value="1">
                                        <strong style="color:var(--title,#d4d8e0)">Emitir boleto com valor integral</strong> (<?= fmtMoney($valorOriginal) ?>)
                                    </label>
                                    <small style="color:var(--dark-cinz,#8788a4); display:block; margin-top:4px">
                                        <i class="fas fa-info-circle"></i> Esta NFS-e possui retencao pelo tomador de <?= fmtMoney($nfse_atual->valor_total_retencao) ?>. Ao marcar, o boleto sera emitido com valor integral.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-actions" style="margin-top:10px; background:transparent; border-top:1px solid var(--dark-2,#272835)">
                            <button type="submit" class="btn btn-success btn-large" style="background:#26a38e; border-color:#1fb5a8">
                                <i class="fas fa-barcode"></i> Gerar Boleto de <?= fmtMoney($valorLiquido) ?>
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert" style="background:rgba(252,157,15,0.15); border-color:rgba(252,157,15,0.3); color:#fc9d0f"><i class="fas fa-lock"></i> Voce nao possui permissao para gerar boletos.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historico -->
    <?php if (!empty($historico_boleto) && count($historico_boleto) > 1): ?>
    <div class="span12" style="margin-top:10px">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-history" style="color:var(--dark-azul,#1086dd)"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Historico de Boletos</h5>
            </div>
            <div class="widget-content nopadding" style="background:var(--wid-dark,#1c1d26)">
                <table class="table table-bordered table-striped" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                    <thead>
                        <tr style="background:var(--dark-1,#14141a)">
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">#ID</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Nosso Numero</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Vencimento</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Valor</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico_boleto as $hist): ?>
                            <?php if ($hist->id != ($boleto_atual->id ?? 0)): ?>
                                <tr>
                                    <td style="border-color:var(--dark-2,#272835)"><?= $hist->id ?></td>
                                    <td style="border-color:var(--dark-2,#272835)"><?= $hist->nosso_numero ?: '---' ?></td>
                                    <td style="border-color:var(--dark-2,#272835)"><?= $hist->data_vencimento ? date('d/m/Y', strtotime($hist->data_vencimento)) : '---' ?></td>
                                    <td style="border-color:var(--dark-2,#272835)"><?= fmtMoney($hist->valor_liquido) ?></td>
                                    <td style="border-color:var(--dark-2,#272835)">
                                        <?php
                                        $hColor = '#8788a4';
                                        if ($hist->status == 'Pago') $hColor = '#26a38e';
                                        elseif ($hist->status == 'Vencido') $hColor = '#fd7670';
                                        elseif ($hist->status == 'Cancelado') $hColor = 'var(--dark-1,#14141a)';
                                        ?>
                                        <span class="label" style="background:<?= $hColor ?>; color:#fff"><?= $hist->status ?></span>
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
