<?php
/**
 * NFS-e — Sub-aba Servicos dentro de Notas Fiscais
 * Tema Dark MapOS
 */

$tributacao = $tributacao ?? [
    'codigo_tributacao_nacional' => '010701',
    'codigo_tributacao_municipal' => '100',
    'descricao_servico' => 'Suporte tecnico em informatica, inclusive instalacao, configuracao e manutencao de programas de computacao e bancos de dados.',
    'aliquota_iss' => '5.00',
];

$emitente = $emitente ?? null;
$totalServico = floatval($totalServico ?? 0);
$totalProdutos = floatval($totalProdutos ?? 0);
$servicos = $servicos ?? [];
$produtos = $produtos ?? [];

$descontoTomador = floatval($result->valor_desconto ?? 0);
$valorBaseNFSe = $descontoTomador > 0 ? $descontoTomador : ($totalServico > 0 ? $totalServico : $totalProdutos);
$ambiente = $ambiente_nfse ?? 'homologacao';

// Garantir que a NFSe emitida apareça corretamente
$situacaoNfse = is_object($nfse_atual) ? ($nfse_atual->situacao ?? '') : ($nfse_atual['situacao'] ?? '');
$mostrarWizard = empty($nfse_atual) || $situacaoNfse === 'Cancelada';

if (!function_exists('fmtMoney')) {
    function fmtMoney($v) {
        return 'R$ ' . number_format(floatval($v), 2, ',', '.');
    }
}
if (!function_exists('fmtDoc')) {
    function fmtDoc($doc) {
        $doc = preg_replace('/\D/', '', $doc);
        if (strlen($doc) == 14) {
            return substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 12, 2);
        }
        if (strlen($doc) == 11) {
            return substr($doc, 0, 3) . '.' . substr($doc, 3, 3) . '.' . substr($doc, 6, 3) . '-' . substr($doc, 9, 2);
        }
        return $doc;
    }
}
?>

<div class="row-fluid" style="margin-top:0">

    <?php if ($ambiente == 'homologacao'): ?>
    <div class="span12">
        <div class="alert" style="background:rgba(252,157,15,0.15); border-color:rgba(252,157,15,0.3); color:#fc9d0f">
            <i class="fas fa-flask"></i> <strong>Ambiente de Homologacao</strong> — NFS-e de teste, sem valor fiscal.
            <a href="<?= site_url('certificado/configurar') ?>" style="color:#1086dd; text-decoration:underline">Alterar para Producao</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($nfse_atual && !$mostrarWizard): ?>
    <!-- NFSe EMITIDA -->
    <div class="span12">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-file-invoice" style="color:var(--dark-azul,#1086dd)"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">NFS-e - Nota Fiscal de Servico</h5>
                <span class="label" style="margin:8px 10px 0 0; float:right; background:<?= $nfse_atual->situacao == 'Emitida' ? '#1086dd' : '#fc9d0f' ?>; color:#fff">
                    <?= $nfse_atual->situacao ?>
                </span>
            </div>
            <div class="widget-content" style="background:var(--wid-dark,#1c1d26); color:var(--branco,#caced8)">
                <div class="row-fluid">
                    <div class="span6">
                        <table class="table table-condensed" style="margin-bottom:0; background:transparent">
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Numero:</strong></td><td style="border:none; padding:3px 0"><?= $nfse_atual->numero_nfse ?: 'Pendente' ?></td></tr>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Data Emissao:</strong></td><td style="border:none; padding:3px 0"><?= $nfse_atual->data_emissao ? date('d/m/Y H:i', strtotime($nfse_atual->data_emissao)) : '---' ?></td></tr>
                            <?php if (!empty($nfse_atual->chave_acesso)): ?>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Chave:</strong></td><td style="border:none; padding:3px 0"><small style="font-family:monospace; color:var(--dark-cinz,#8788a4)"><?= $nfse_atual->chave_acesso ?></small></td></tr>
                            <?php endif; ?>
                            <?php if (!empty($nfse_atual->protocolo)): ?>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Protocolo:</strong></td><td style="border:none; padding:3px 0"><?= $nfse_atual->protocolo ?></td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="span6 text-right">
                        <table class="table table-condensed" style="margin-bottom:0; background:transparent">
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Valor Servicos:</strong></td><td style="border:none; padding:3px 0"><?= fmtMoney($nfse_atual->valor_servicos) ?></td></tr>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Impostos:</strong></td><td style="border:none; padding:3px 0"><?= fmtMoney($nfse_atual->valor_total_impostos) ?></td></tr>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:#62eba6">Valor Liquido:</strong></td><td style="border:none; padding:3px 0"><strong style="color:#62eba6"><?= fmtMoney($nfse_atual->valor_liquido) ?></strong></td></tr>
                        </table>
                    </div>
                </div>

                <hr style="margin:10px 0; border-top:1px solid var(--dark-2,#272835)">

                <h6 style="color:var(--title,#d4d8e0)"><i class="fas fa-calculator" style="color:var(--dark-azul,#1086dd)"></i> Detalhamento dos Impostos / DAS</h6>
                <?php
                $aliquotaEfetiva = $nfse_atual->valor_servicos > 0 ? round(($nfse_atual->valor_total_impostos / $nfse_atual->valor_servicos) * 100, 2) : 0;
                ?>
                <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                        <div class="row-fluid">
                        <div class="span6">
                            <span class="label" style="background:#1086dd; color:#fff">Simples Nacional</span>
                            <div style="margin-top:8px; color:var(--branco,#caced8)">
                                <strong style="color:var(--title,#d4d8e0)">DAS Estimado:</strong> <?= fmtMoney($nfse_atual->valor_total_impostos) ?><br>
                                <strong style="color:var(--title,#d4d8e0)">Aliquota Efetiva:</strong> <?= number_format($aliquotaEfetiva, 2, ',', '.') ?>%<br>
                                <strong style="color:var(--title,#d4d8e0)">Competencia:</strong> <?= date('m/Y', strtotime($nfse_atual->competencia ?? date('Y-m-01'))) ?>
                            </div>
                        </div>
                        <div class="span6" style="color:var(--branco,#caced8)">
                            <strong style="color:var(--title,#d4d8e0)">Base Calculo:</strong> <?= fmtMoney($nfse_atual->valor_servicos) ?><br>
                            <strong style="color:var(--title,#d4d8e0)">Registrado no DRE:</strong> <i class="fas fa-check-circle" style="color:#62eba6"></i> Sim
                        </div>
                    </div>
                    <div style="margin-top:10px; font-size:11px; color:var(--dark-cinz,#8788a4)">
                        <i class="fas fa-info-circle" style="color:#1086dd"></i> O DAS e recolhido mensalmente pelo prestador. O valor estimado foi registrado no DRE para controle.
                    </div>

                    <?php if (floatval($nfse_atual->valor_total_retencao ?? 0) > 0): ?>
                    <hr style="margin:10px 0; border-top:1px solid var(--dark-2,#272835)">
                    <div style="font-size:12px; color:var(--branco,#caced8)">
                        <strong style="color:#fd7670"><i class="fas fa-hand-holding-usd"></i> Retencoes do Tomador:</strong> <?= fmtMoney($nfse_atual->valor_total_retencao) ?>
                        <small style="color:var(--dark-cinz,#8788a4)">(reduz o valor da NFS-e, registrada para compensacao no DAS)</small>
                        <div style="margin-top:4px; color:var(--dark-cinz,#8788a4)">
                            <?php if (floatval($nfse_atual->valor_retencao_iss ?? 0) > 0): ?>ISS <?= fmtMoney($nfse_atual->valor_retencao_iss) ?> &nbsp;<?php endif; ?>
                            <?php if (floatval($nfse_atual->valor_retencao_irrf ?? 0) > 0): ?>IRRF <?= fmtMoney($nfse_atual->valor_retencao_irrf) ?> &nbsp;<?php endif; ?>
                            <?php if (floatval($nfse_atual->valor_retencao_pis ?? 0) > 0): ?>PIS <?= fmtMoney($nfse_atual->valor_retencao_pis) ?> &nbsp;<?php endif; ?>
                            <?php if (floatval($nfse_atual->valor_retencao_cofins ?? 0) > 0): ?>COFINS <?= fmtMoney($nfse_atual->valor_retencao_cofins) ?> &nbsp;<?php endif; ?>
                            <?php if (floatval($nfse_atual->valor_retencao_csll ?? 0) > 0): ?>CSLL <?= fmtMoney($nfse_atual->valor_retencao_csll) ?><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="btn-group" style="margin-bottom:10px">
                    <a href="<?= site_url('nfse_os/imprimir_nfse/' . $nfse_atual->id) ?>" target="_blank" class="btn btn-primary" style="background:#1086dd; border-color:#0d6efd">
                        <i class="fas fa-file-pdf"></i> Imprimir NFS-e
                    </a>
                    <?php if (!empty($nfse_atual->url_danfe)): ?>
                        <a href="<?= htmlspecialchars($nfse_atual->url_danfe, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-success" style="background:#26a38e; border-color:#1fb5a8">
                            <i class="fas fa-external-link-alt"></i> DANFSe Nacional
                        </a>
                    <?php endif; ?>
                    <?php if ($nfse_atual->link_impressao): ?>
                        <a href="<?= htmlspecialchars($nfse_atual->link_impressao, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-success" style="background:#26a38e; border-color:#1fb5a8">
                            <i class="fas fa-print"></i> Imprimir Original
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($nfse_atual->chave_acesso)): ?>
                        <button type="button" class="btn btn-info" style="background:#52459f; border-color:#52459f" onclick="consultarNFSeNacional(<?= $nfse_atual->id ?>)">
                            <i class="fas fa-sync-alt"></i> Consultar
                        </button>
                    <?php endif; ?>
                    <?php if (!empty($nfse_atual->xml_dps) || !empty($nfse_atual->xml_nfse)): ?>
                        <a href="<?= site_url('nfse_os/download_xml/' . $nfse_atual->id) ?>" class="btn btn-info" style="background:#52459f; border-color:#52459f">
                            <i class="fas fa-file-code"></i> Download XML
                        </a>
                    <?php endif; ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNFSe') && $nfse_atual->situacao != 'Cancelada'): ?>
                        <?php if (!empty($nfse_atual->chave_acesso)): ?>
                            <button type="button" class="btn btn-danger" style="background:#dc3545; border-color:#dc3545" onclick="cancelarNFSeNacional(<?= $nfse_atual->id ?>)">
                                <i class="fas fa-times"></i> Cancelar Nacional
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-danger" style="background:#dc3545; border-color:#dc3545" onclick="cancelarNFSe(<?= $nfse_atual->id ?>)">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div id="nfse-consulta-resultado" style="display:none; margin-top:10px">
                    <div class="alert" id="nfse-consulta-conteudo" style="background:rgba(16,134,221,0.15); border-color:rgba(16,134,221,0.3); color:#1086dd"></div>
                </div>
            </div>
        </div>
    </div>

    <?php elseif (!empty($nfse_importada)): ?>
    <!-- NFSe IMPORTADA (XML externo) -->
    <div class="span12">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-file-import" style="color:#26a38e"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">NFS-e Importada — OS #<?= $result->idOs ?></h5>
                <span class="label" style="margin:8px 10px 0 0; float:right; background:#26a38e; color:#fff">
                    <?= $nfse_importada->situacao ?: 'Importada' ?>
                </span>
            </div>
            <div class="widget-content" style="background:var(--wid-dark,#1c1d26); color:var(--branco,#caced8)">
                <div class="row-fluid">
                    <div class="span6">
                        <table class="table table-condensed" style="margin-bottom:0; background:transparent">
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Numero:</strong></td><td style="border:none; padding:3px 0"><?= $nfse_importada->numero ?: '---' ?></td></tr>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Serie:</strong></td><td style="border:none; padding:3px 0"><?= $nfse_importada->serie ?: '---' ?></td></tr>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Data Emissao:</strong></td><td style="border:none; padding:3px 0"><?= $nfse_importada->data_emissao ? date('d/m/Y', strtotime($nfse_importada->data_emissao)) : '---' ?></td></tr>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Data Importacao:</strong></td><td style="border:none; padding:3px 0"><?= $nfse_importada->data_importacao ? date('d/m/Y H:i', strtotime($nfse_importada->data_importacao)) : '---' ?></td></tr>
                            <?php if (!empty($nfse_importada->chave_acesso)): ?>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Chave:</strong></td><td style="border:none; padding:3px 0"><small style="font-family:monospace; color:var(--dark-cinz,#8788a4)"><?= preg_replace('/^NFS/i', '', $nfse_importada->chave_acesso) ?></small></td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="span6 text-right">
                        <table class="table table-condensed" style="margin-bottom:0; background:transparent">
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Valor Total:</strong></td><td style="border:none; padding:3px 0"><?= fmtMoney($nfse_importada->valor_total) ?></td></tr>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Impostos:</strong></td><td style="border:none; padding:3px 0"><?= fmtMoney($nfse_importada->valor_impostos ?? 0) ?></td></tr>
                            <?php $vlLiquido = floatval($nfse_importada->valor_total ?? 0) - floatval($nfse_importada->valor_impostos ?? 0); ?>
                            <tr><td style="border:none; padding:3px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:#62eba6">Valor Liquido:</strong></td><td style="border:none; padding:3px 0"><strong style="color:#62eba6"><?= fmtMoney($vlLiquido) ?></strong></td></tr>
                        </table>
                    </div>
                </div>

                <hr style="margin:10px 0; border-top:1px solid var(--dark-2,#272835)">

                <div class="btn-group" style="margin-bottom:10px">
                    <a href="<?= site_url('certificado/download_xml/' . $nfse_importada->id) ?>" class="btn btn-info" style="background:#52459f; border-color:#52459f">
                        <i class="fas fa-file-code"></i> Download XML
                    </a>
                    <button type="button" class="btn btn-primary" style="background:#1086dd; border-color:#0d6efd" onclick="window.open('<?= site_url('certificado/visualizar_xml/' . $nfse_importada->id) ?>','_blank')">
                        <i class="fas fa-eye"></i> Visualizar XML
                    </button>
                    <a href="<?= site_url('certificado/imprimir_nfse_importada/' . $nfse_importada->id) ?>" target="_blank" class="btn btn-success" style="background:#26a38e; border-color:#1fb5a8">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- WIZARD EMISSAO NFS-e -->
    <div class="span12">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-file-invoice" style="color:var(--dark-azul,#1086dd)"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Emitir NFS-e — OS #<?= $result->idOs ?></h5>
            </div>
            <div class="widget-content" style="background:var(--wid-dark,#1c1d26); color:var(--branco,#caced8)">

                <!-- Importar XML externo -->
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cNFSe')): ?>
                <div class="row-fluid" style="margin-bottom:15px">
                    <div class="span12">
                        <div class="well well-small" style="background:rgba(82,69,159,0.15); border-color:rgba(82,69,159,0.3); color:#caced8">
                            <div style="display:flex; align-items:center; justify-content:space-between">
                                <div>
                                    <strong style="color:var(--title,#d4d8e0)"><i class="fas fa-file-import" style="color:#52459f"></i> Ja emitiu esta NFS-e em outro sistema?</strong>
                                    <br>
                                    <small style="color:var(--dark-cinz,#8788a4)">Importe o XML da nota fiscal com preview dos dados antes de salvar.</small>
                                </div>
                                <a href="<?= site_url('certificado/importar_nfse') ?>?os_id=<?= $result->idOs ?>" class="btn btn-small" style="background:#52459f; border-color:#52459f; color:#fff; white-space:nowrap">
                                    <i class="fas fa-upload"></i> Importar XML
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Passos -->
                <div class="row-fluid" style="margin-bottom:20px">
                    <div class="span12">
                        <ul class="wizard-steps" id="wizard-steps">
                            <li class="active" data-step="1"><span class="wizard-step-number">1</span><span class="wizard-step-label">Dados</span></li>
                            <li data-step="2"><span class="wizard-step-number">2</span><span class="wizard-step-label">Impostos</span></li>
                            <li data-step="3"><span class="wizard-step-number">3</span><span class="wizard-step-label">Boleto</span></li>
                            <li data-step="4"><span class="wizard-step-number">4</span><span class="wizard-step-label">Confirmar</span></li>
                        </ul>
                    </div>
                </div>

                <!-- PASSO 1 -->
                <div class="wizard-step-panel active" id="wizard-step-1">

                    <!-- RESUMO DA OS -->
                    <div class="row-fluid" style="margin-bottom:15px">
                        <div class="span12">
                            <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                <div class="row-fluid">
                                    <div class="span6">
                                        <h6 style="margin-top:0; color:var(--title,#d4d8e0)">
                                            <i class="fas fa-clipboard-list" style="color:var(--dark-azul,#1086dd)"></i>
                                            Resumo da OS #<?= $result->idOs ?>
                                        </h6>
                                        <strong style="color:var(--title,#d4d8e0)"><?= htmlspecialchars($result->nomeCliente ?? '') ?></strong><br>
                                        <span style="color:var(--dark-cinz,#8788a4)"><?= !empty($result->documento) ? 'CPF/CNPJ: ' . fmtDoc($result->documento) : 'Documento nao informado' ?></span>
                                    </div>
                                    <div class="span6 text-right">
                                        <div style="display:inline-block; text-align:left; min-width:180px">
                                            <div style="margin-bottom:4px"><strong style="color:var(--title,#d4d8e0)">Servicos:</strong> <span style="color:var(--branco,#caced8)"><?= fmtMoney($totalServico) ?></span></div>
                                            <?php if ($totalProdutos > 0): ?>
                                            <div style="margin-bottom:4px"><strong style="color:var(--title,#d4d8e0)">Produtos:</strong> <span style="color:var(--branco,#caced8)"><?= fmtMoney($totalProdutos) ?></span></div>
                                            <?php endif; ?>
                                            <?php if ($descontoTomador > 0): ?>
                                            <div style="margin-bottom:4px"><strong style="color:#fc9d0f">Desconto:</strong> <span style="color:#fc9d0f"><?= fmtMoney($descontoTomador) ?></span></div>
                                            <?php endif; ?>
                                            <div style="border-top:1px solid var(--dark-2,#272835); padding-top:4px; margin-top:4px">
                                                <strong style="color:#62eba6; font-size:14px">Total OS:</strong>
                                                <span style="color:#62eba6; font-size:14px; font-weight:bold"><?= fmtMoney($totalServico + $totalProdutos) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                <h6 style="margin-top:0; color:var(--title,#d4d8e0); border-bottom:1px solid var(--dark-2,#272835); padding-bottom:6px"><i class="fas fa-building" style="color:var(--dark-azul,#1086dd)"></i> Prestador</h6>
                                <?php if ($emitente): ?>
                                    <strong style="color:var(--title,#d4d8e0)"><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '') ?></strong><br>
                                    <span style="color:var(--dark-cinz,#8788a4)">CNPJ: <?= fmtDoc($emitente->cnpj ?? '') ?></span><br>
                                    <span style="color:var(--dark-cinz,#8788a4)"><?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? ''))) ?></span><br>
                                    <span style="color:var(--dark-cinz,#8788a4)">CEP: <?= $emitente->cep ?? '' ?></span>
                                <?php else: ?>
                                    <span style="color:#fc9d0f">Dados do emitente nao configurados</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                <h6 style="margin-top:0; color:var(--title,#d4d8e0); border-bottom:1px solid var(--dark-2,#272835); padding-bottom:6px"><i class="fas fa-user" style="color:var(--dark-azul,#1086dd)"></i> Tomador</h6>
                                <strong style="color:var(--title,#d4d8e0)"><?= htmlspecialchars($result->nomeCliente ?? '') ?></strong><br>
                                <span style="color:var(--dark-cinz,#8788a4)"><?= !empty($result->documento) ? 'CPF/CNPJ: ' . fmtDoc($result->documento) : 'Documento nao informado' ?></span><br>
                                <span style="color:var(--dark-cinz,#8788a4)"><?= htmlspecialchars(trim(($result->rua ?? '') . ', ' . ($result->numero ?? '') . ' - ' . ($result->bairro ?? ''))) ?></span><br>
                                <span style="color:var(--dark-cinz,#8788a4)"><?= htmlspecialchars(trim(($result->cidade ?? '') . '/' . ($result->estado ?? '') . ' - CEP: ' . ($result->cep ?? ''))) ?></span><br>
                                <?php if (!empty($result->inscricao_municipal) || !empty($result->inscricao_estadual)): ?>
                                <span style="color:var(--dark-cinz,#8788a4); font-size:11px">
                                    <?php if (!empty($result->inscricao_municipal)) echo 'IM: ' . htmlspecialchars($result->inscricao_municipal) . ' '; ?>
                                    <?php if (!empty($result->inscricao_estadual)) echo 'IE: ' . htmlspecialchars($result->inscricao_estadual); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($servicos) || !empty($produtos)): ?>
                    <h6 style="color:var(--title,#d4d8e0)"><i class="fas fa-list" style="color:var(--dark-azul,#1086dd)"></i> Itens da OS</h6>
                    <table class="table table-condensed table-bordered" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                        <thead>
                            <tr style="background:var(--dark-1,#14141a)">
                                <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Item</th>
                                <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Qtd</th>
                                <th class="text-right" style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Unit.</th>
                                <th class="text-right" style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicos as $s): ?>
                            <tr>
                                <td style="border-color:var(--dark-2,#272835)"><?= htmlspecialchars($s->nome ?? 'Servico') ?></td>
                                <td style="border-color:var(--dark-2,#272835)"><?= $s->quantidade ?? 1 ?></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><?= fmtMoney($s->preco ?: $s->precoVenda) ?></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><?= fmtMoney(($s->preco ?: $s->precoVenda) * ($s->quantidade ?: 1)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td style="border-color:var(--dark-2,#272835)"><?= htmlspecialchars($p->descricao ?? $p->nomeProduto ?? 'Produto') ?></td>
                                <td style="border-color:var(--dark-2,#272835)"><?= $p->quantidade ?? 1 ?></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><?= fmtMoney($p->preco ?? (($p->subTotal ?? 0) / max(1, $p->quantidade ?? 1))) ?></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><?= fmtMoney($p->subTotal) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--dark-1,#14141a)">
                                <td colspan="3" class="text-right" style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"><strong>Total Servicos:</strong></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><strong><?= fmtMoney($totalServico) ?></strong></td>
                            </tr>
                            <?php if ($totalProdutos > 0): ?>
                            <tr style="background:var(--dark-1,#14141a)">
                                <td colspan="3" class="text-right" style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"><strong>Total Produtos:</strong></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><strong><?= fmtMoney($totalProdutos) ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($descontoTomador > 0): ?>
                            <tr style="background:var(--dark-1,#14141a)">
                                <td colspan="3" class="text-right" style="border-color:var(--dark-2,#272835)"><strong style="color:#fc9d0f">Desconto Tomador:</strong></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><strong style="color:#fc9d0f"><?= fmtMoney($descontoTomador) ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            <tr style="background:var(--dark-1,#14141a)">
                                <td colspan="3" class="text-right" style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"><strong>Total OS:</strong></td>
                                <td class="text-right" style="border-color:var(--dark-2,#272835)"><strong style="color:#62eba6; font-size:14px"><?= fmtMoney($totalServico + $totalProdutos) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php endif; ?>

                    <?php if ($totalProdutos > 0): ?>
                    <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                        <label class="checkbox" style="margin:0; font-size:14px; color:var(--branco,#caced8)">
                            <input type="checkbox" id="incluir-produtos-nfse" value="1" style="margin-right:5px">
                            <strong style="color:var(--title,#d4d8e0)">Incluir Produtos no Valor da NFS-e</strong>
                            <small style="color:var(--dark-cinz,#8788a4); font-weight:normal">(R$ <?= number_format($totalProdutos, 2, ',', '.') ?>)</small>
                        </label>
                        <p style="margin:5px 0 0 0; font-size:11px; color:var(--dark-cinz,#8788a4)">
                            <i class="fas fa-exclamation-triangle" style="color:#fc9d0f"></i> Verifique a legislacao municipal antes de incluir produtos na NFS-e de servicos.
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="well well-small" style="margin-top:12px; margin-bottom:10px; background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px">
                            <i class="fas fa-landmark" style="font-size:16px; color:#62eba6"></i>
                            <strong style="font-size:14px; color:var(--title,#d4d8e0)">Regime Tributario: Simples Nacional</strong>
                            <span class="label" style="background:#1086dd; color:#fff; font-size:11px">DAS</span>
                        </div>
                        <div style="margin-bottom:10px; color:var(--branco,#caced8)">
                            <strong style="color:var(--title,#d4d8e0)"><i class="fas fa-receipt" style="color:#62eba6"></i> DAS Estimado:</strong>
                            <span id="das-valor-display" style="color:#62eba6; font-weight:bold">—</span>
                            <small style="color:var(--dark-cinz,#8788a4); display:block; margin-top:4px">No Simples Nacional, o imposto e recolhido via DAS mensal. Valor calculado no Passo 2.</small>
                        </div>

                        <hr style="margin:10px 0; border-top:1px solid var(--dark-2,#272835)">
                        <div style="margin-bottom:6px"><strong style="font-size:13px; color:var(--title,#d4d8e0)"><i class="fas fa-hand-holding-usd" style="color:#fd7670"></i> Retencoes do Tomador</strong> <small style="color:var(--dark-cinz,#8788a4)">(impostos retidos na fonte)</small></div>
                        <div class="row-fluid">
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px; color:var(--branco,#caced8)"><input type="checkbox" id="retem-iss" name="retem_iss" value="1"> ISS (<span id="aliquota-iss-display"><?= $tributacao['aliquota_iss'] ?? '5.00' ?>%</span>) <span id="retem-iss-valor" style="color:#fd7670; font-weight:bold"></span></label></div>
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px; color:var(--branco,#caced8)"><input type="checkbox" id="retem-irrf" name="retem_irrf" value="1"> IRRF (1,5%) <span id="retem-irrf-valor" style="color:#fd7670; font-weight:bold"></span></label></div>
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px; color:var(--branco,#caced8)"><input type="checkbox" id="retem-pis" name="retem_pis" value="1"> PIS (0,65%) <span id="retem-pis-valor" style="color:#fd7670; font-weight:bold"></span></label></div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px; color:var(--branco,#caced8)"><input type="checkbox" id="retem-cofins" name="retem_cofins" value="1"> COFINS (3,0%) <span id="retem-cofins-valor" style="color:#fd7670; font-weight:bold"></span></label></div>
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px; color:var(--branco,#caced8)"><input type="checkbox" id="retem-csll" name="retem_csll" value="1"> CSLL (1,0%) <span id="retem-csll-valor" style="color:#fd7670; font-weight:bold"></span></label></div>
                            <div class="span4"><strong style="font-size:13px; color:var(--title,#d4d8e0)">Total Retido:</strong> <span id="retem-total-valor" style="color:#fd7670; font-weight:bold; font-size:14px">R$ 0,00</span></div>
                        </div>
                        <p style="margin:4px 0 0 0; font-size:11px; color:var(--dark-cinz,#8788a4)"><i class="fas fa-exclamation-circle" style="color:#fd7670"></i> As retencoes NAO reduzem o valor da NFS-e. Sao registradas para controle e compensacao no DAS.</p>
                        <div id="aviso-retencao-iss" style="display:none; margin-top:8px; padding:8px 12px; background:rgba(253,118,112,0.15); border:1px solid rgba(253,118,112,0.3); border-radius:4px; color:#fd7670">
                            <strong style="font-size:12px"><i class="fas fa-exclamation-triangle"></i> Atencao — Retencao de ISS pelo Tomador</strong>
                            <p style="margin:4px 0 0 0; font-size:11px">Ao emitir esta NFS-e com retencao de ISS, marque <strong>"Sim"</strong> para <em>Retencao do ISSQN</em> no Portal do Contribuidor. O sistema ja configurara <strong>IssRetido = Sim</strong> no XML.</p>
                        </div>
                    </div>

                    <div class="row-fluid" style="margin-top:15px">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Valor dos Servicos:</strong></label>
                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <span class="add-on" style="background:var(--dark-2,#272835); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">R$</span>
                                        <input type="text" name="valor_servicos" id="valor-servicos-wizard" class="span8" value="<?= number_format($valorBaseNFSe, 2, ',', '.') ?>" placeholder="0,00" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                    </div>
                                    <span class="help-block" id="valor-servicos-help" style="color:var(--dark-cinz,#8788a4)">Valor dos servicos prestados</span>
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Deducoes:</strong></label>
                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <span class="add-on" style="background:var(--dark-2,#272835); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">R$</span>
                                        <input type="text" name="valor_deducoes" id="valor-deducoes-wizard" class="span8" value="0,00" placeholder="0,00" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                    </div>
                                    <span class="help-block" style="color:var(--dark-cinz,#8788a4)">Deducoes legais (materiais, insumos)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Descricao do Servico:</strong></label>
                        <div class="controls">
                            <textarea name="descricao_servico" id="descricao-servico-wizard" class="span12" rows="3" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)"><?= htmlspecialchars($tributacao['descricao_servico']) ?></textarea>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label" style="color:var(--dark-cinz,#8788a4)">Codigo Tributacao LC 116:</label>
                                <div class="controls">
                                    <input type="text" class="span12" value="<?= $tributacao['codigo_tributacao_nacional'] ?>" readonly style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:var(--dark-cinz,#8788a4)">
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label" style="color:var(--dark-cinz,#8788a4)">Codigo Municipal:</label>
                                <div class="controls">
                                    <input type="text" class="span12" value="<?= $tributacao['codigo_tributacao_municipal'] ?>" readonly style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:var(--dark-cinz,#8788a4)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 2 -->
                <div class="wizard-step-panel" id="wizard-step-2">
                    <div id="wizard-loading-indicator" style="display:none; margin-bottom:15px; padding:12px 15px; background:rgba(16,134,221,0.15); border:1px solid rgba(16,134,221,0.3); border-radius:4px; color:#1086dd; text-align:center">
                        <i class="fas fa-spinner fa-spin"></i> <strong>Calculando impostos...</strong> Aguarde um instante.
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <h6 style="color:var(--title,#d4d8e0)"><i class="fas fa-calculator" style="color:var(--dark-azul,#1086dd)"></i> Calculo de Impostos <span class="label" style="background:#1086dd; color:#fff; font-size:11px">Simples Nacional</span></h6>
                            <table class="table table-condensed" id="impostos-table" style="font-size:13px; background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                <tbody>
                                    <tr><td style="border-color:var(--dark-2,#272835)"><strong>Valor Bruto</strong></td><td class="text-right" id="imp-valor-bruto" style="border-color:var(--dark-2,#272835)">—</td></tr>
                                    <tr><td style="border-color:var(--dark-2,#272835)"><strong>(-) Deducoes</strong></td><td class="text-right" id="imp-deducoes" style="border-color:var(--dark-2,#272835)">—</td></tr>
                                    <tr><td style="border-color:var(--dark-2,#272835)"><strong>Base de Calculo</strong></td><td class="text-right" id="imp-base-calculo" style="border-color:var(--dark-2,#272835)">—</td></tr>
                                    <tr style="background:rgba(16,134,221,0.1)"><td style="border-color:var(--dark-2,#272835)"><strong><i class="fas fa-receipt"></i> DAS (Simples Nacional)</strong></td><td class="text-right" id="imp-das" style="border-color:var(--dark-2,#272835)"><strong>—</strong></td></tr>
                                    <tr><td style="font-size:11px; color:var(--dark-cinz,#8788a4); padding-left:20px; border-color:var(--dark-2,#272835)">Aliquota efetiva: <span id="imp-das-aliquota">—</span></td><td style="border-color:var(--dark-2,#272835)"></td></tr>
                                    <tr style="border-top:2px solid var(--dark-2,#272835)"><td style="border-color:var(--dark-2,#272835)"><strong>Total Impostos</strong></td><td class="text-right" id="imp-total-impostos" style="border-color:var(--dark-2,#272835)"><strong>—</strong></td></tr>
                                    <tr id="retencao-row" style="display:none"><td style="color:#fd7670; border-color:var(--dark-2,#272835)"><i class="fas fa-hand-holding-usd"></i> (-) Retencoes Tomador</td><td class="text-right" id="imp-retencao-total" style="color:#fd7670; border-color:var(--dark-2,#272835)">—</td></tr>
                                    <tr style="background:rgba(98,235,166,0.1)"><td style="border-color:var(--dark-2,#272835)"><strong style="font-size:14px">Valor Liquido</strong></td><td class="text-right" id="imp-valor-liquido" style="border-color:var(--dark-2,#272835)"><strong style="font-size:14px; color:#62eba6">—</strong></td></tr>
                                </tbody>
                            </table>
                            <p style="font-size:11px; color:var(--dark-cinz,#8788a4); margin-top:5px">
                                <i class="fas fa-info-circle" style="color:#1086dd"></i> Simples Nacional: imposto recolhido via DAS mensal. O valor liquido NAO e reduzido pelas retencoes.
                            </p>
                        </div>
                        <div class="span6">
                            <div class="well well-small" style="text-align:center; padding:30px 20px; background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                                <i class="fas fa-file-pdf" style="font-size:48px; color:#fd7670; display:block; margin-bottom:15px"></i>
                                <h5 style="color:var(--title,#d4d8e0)">Pre-visualizacao do Documento NFS-e</h5>
                                <p style="color:var(--dark-cinz,#8788a4); margin:10px 0">Clique abaixo para gerar um PDF com a pre-visualizacao completa.</p>
                                <button type="button" class="btn btn-primary btn-large" id="btn-preview-nfse" style="background:#1086dd; border-color:#1086dd"><i class="fas fa-eye"></i> Pre-visualizar NFS-e (PDF)</button>
                                <p style="color:var(--dark-cinz,#8788a4); font-size:11px; margin-top:10px">O PDF sera aberto em uma nova aba.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 3 -->
                <div class="wizard-step-panel" id="wizard-step-3">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label class="checkbox" style="font-size:14px; margin-bottom:15px; color:var(--branco,#caced8)">
                                    <input type="checkbox" id="gerar-boleto-wizard" value="1" checked>
                                    <strong style="color:var(--title,#d4d8e0)">Gerar boleto de cobranca junto com a NFS-e</strong>
                                </label>
                            </div>
                            <div id="boleto-campos">
                                <div class="alert" style="margin-bottom:15px; background:rgba(16,134,221,0.15); border-color:rgba(16,134,221,0.3); color:#1086dd">
                                    <i class="fas fa-info-circle"></i> O boleto sera gerado com o <strong>valor integral</strong> dos servicos. O DAS (Simples Nacional) e recolhido mensalmente pelo prestador e <strong>nao desconta</strong> do boleto.
                                </div>
                                <div class="row-fluid">
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Valor do Boleto:</strong></label>
                                            <div class="controls">
                                                <div class="input-prepend input-append">
                                                    <span class="add-on" style="background:var(--dark-2,#272835); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">R$</span>
                                                    <input type="text" id="valor-boleto-wizard" class="span8" readonly style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:#62eba6; font-weight:bold">
                                                </div>
                                                <small id="valor-boleto-ajuda" style="color:var(--dark-cinz,#8788a4); font-size:11px">Valor integral = Servicos - Deducoes</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Data de Vencimento:</strong></label>
                                            <div class="controls">
                                                <input type="date" name="data_vencimento" id="data-vencimento-wizard" class="span12" value="<?= date('Y-m-d', strtotime('+5 days')) ?>" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label" style="color:var(--title,#d4d8e0)"><strong>Instrucoes:</strong></label>
                                            <div class="controls">
                                                <textarea name="instrucoes" id="instrucoes-boleto-wizard" class="span12" rows="2" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">Pagavel em qualquer banco ate o vencimento. Apos o vencimento, consultar multas e juros.</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="valor-integral-section" style="display:none; margin-top:12px; padding:12px 15px; background:rgba(252,157,15,0.15); border:1px solid rgba(252,157,15,0.3); border-radius:4px; color:#fc9d0f">
                                    <label class="checkbox" style="font-size:13px; margin-bottom:5px; color:var(--branco,#caced8)">
                                        <input type="checkbox" id="valor-integral-wizard" name="valor_integral" value="1" checked>
                                        <strong style="color:var(--title,#d4d8e0)">Emitir boleto com valor integral</strong> (nao descontar retencoes)
                                    </label>
                                    <small style="color:var(--dark-cinz,#8788a4); display:block; margin-top:4px"><i class="fas fa-info-circle"></i> Quando ha retencao pelo tomador, o boleto pode ser emitido com valor integral. As retencoes serao registradas para compensacao no DAS mensal.</small>
                                </div>
                            </div>
                            <div id="sem-boleto-msg" style="display:none">
                                <div class="alert" style="background:rgba(252,157,15,0.15); border-color:rgba(252,157,15,0.3); color:#fc9d0f"><i class="fas fa-exclamation-triangle"></i> Nenhum boleto sera gerado. Voce podera gerar separadamente apos a emissao.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 4 -->
                <div class="wizard-step-panel" id="wizard-step-4">
                    <div class="alert" style="margin-bottom:20px; background:rgba(16,134,221,0.15); border-color:rgba(16,134,221,0.3); color:#1086dd">
                        <i class="fas fa-clipboard-check"></i> <strong>Resumo da Emissao</strong> — Verifique os dados antes de confirmar.
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                                <h6 style="margin-top:0; color:var(--title,#d4d8e0)"><i class="fas fa-file-invoice" style="color:var(--dark-azul,#1086dd)"></i> NFS-e <span class="label" style="background:#1086dd; color:#fff; font-size:11px">Simples Nacional</span></h6>
                                <table class="table table-condensed" style="margin-bottom:0; font-size:12px; background:transparent; border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                                    <tr><td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Prestador:</strong></td><td style="border:none; padding:2px 0"><span id="res-prestador"><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '—') ?></span></td></tr>
                                    <tr><td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Tomador:</strong></td><td style="border:none; padding:2px 0"><span id="res-tomador"><?= htmlspecialchars($result->nomeCliente ?? '—') ?></span></td></tr>
                                    <tr><td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Valor Servicos:</strong></td><td style="border:none; padding:2px 0"><span id="res-valor-servicos">—</span></td></tr>
                                    <tr><td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Deducoes:</strong></td><td style="border:none; padding:2px 0"><span id="res-deducoes">—</span></td></tr>
                                    <tr id="res-imposto-linha"><td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:var(--branco,#caced8)">Total Impostos:</strong></td><td style="border:none; padding:2px 0"><span id="res-total-impostos">—</span></td></tr>
                                    <tr id="res-das-linha"><td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:#62eba6">DAS:</strong></td><td style="border:none; padding:2px 0"><span id="res-valor-das" style="color:#62eba6; font-weight:bold">—</span></td></tr>
                                    <tr id="res-retencao-linha" style="display:none"><td style="border:none; padding:2px 0; color:var(--dark-cinz,#8788a4)"><strong style="color:#fd7670">Retencoes:</strong></td><td style="border:none; padding:2px 0"><span id="res-retencao-total" style="color:#fd7670; font-weight:bold">—</span></td></tr>
                                    <tr><td style="border:none; padding:4px 0 0 0; border-top:1px solid var(--dark-2,#272835); color:var(--dark-cinz,#8788a4)"><strong style="color:#62eba6; font-size:14px">Valor Liquido:</strong></td><td style="border:none; padding:4px 0 0 0; border-top:1px solid var(--dark-2,#272835)"><span id="res-valor-liquido" style="color:#62eba6; font-size:14px; font-weight:bold">—</span></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="well well-small" id="res-boleto-section" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                                <h6 style="margin-top:0; color:var(--title,#d4d8e0)"><i class="fas fa-barcode" style="color:#fc9d0f"></i> Boleto</h6>
                                <p id="res-boleto-info" style="color:var(--branco,#caced8)">—</p>
                            </div>
                            <div class="well well-small" style="border-left:3px solid #fd7670; background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835)">
                                <h6 style="margin-top:0; color:var(--title,#d4d8e0)"><i class="fas fa-exclamation-triangle" style="color:#fd7670"></i> Atencao</h6>
                                <p style="margin-bottom:0; color:var(--branco,#caced8)">Apos confirmar, a NFS-e sera emitida junto com o boleto (se selecionado). Esta acao nao pode ser desfeita facilmente.</p>
                                <?php if ($ambiente == 'homologacao'): ?>
                                <p style="color:#fc9d0f; font-weight:bold; margin-bottom:0"><i class="fas fa-flask"></i> Modo Homologacao: NFS-e de teste, sem valor fiscal.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegacao -->
                <div class="wizard-nav">
                    <button type="button" class="btn" id="btn-wizard-anterior" disabled style="background:var(--dark-2,#272835); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)"><i class="fas fa-arrow-left"></i> Anterior</button>
                    <button type="button" class="btn btn-primary" id="btn-wizard-proximo" style="background:#1086dd; border-color:#1086dd"><i class="fas fa-arrow-right"></i> Proximo</button>
                    <button type="button" class="btn btn-success" id="btn-wizard-emitir" style="display:none; background:#26a38e; border-color:#1fb5a8"><i class="fas fa-check-circle"></i> Emitir NFS-e (API Nacional)</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historico -->
    <?php if ($nfse_atual && !empty($historico_nfse) && is_array($historico_nfse) && count($historico_nfse) > 1): ?>
    <div class="span12" style="margin-top:10px">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-history" style="color:var(--dark-azul,#1086dd)"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Historico de NFS-e</h5>
            </div>
            <div class="widget-content nopadding" style="background:var(--wid-dark,#1c1d26)">
                <table class="table table-bordered table-striped" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                    <thead>
                        <tr style="background:var(--dark-1,#14141a)">
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">#ID</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Numero</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Data</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Valor</th>
                            <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico_nfse as $hist): ?>
                            <?php if ($hist->id != ($nfse_atual->id ?? 0)): ?>
                                <tr>
                                    <td style="border-color:var(--dark-2,#272835)"><?= $hist->id ?></td>
                                    <td style="border-color:var(--dark-2,#272835)"><?= $hist->numero_nfse ?: '---' ?></td>
                                    <td style="border-color:var(--dark-2,#272835)"><?= $hist->data_emissao ? date('d/m/Y', strtotime($hist->data_emissao)) : '---' ?></td>
                                    <td style="border-color:var(--dark-2,#272835)"><?= fmtMoney($hist->valor_liquido) ?></td>
                                    <td style="border-color:var(--dark-2,#272835)"><span class="label" style="background:<?= $hist->situacao == 'Emitida' ? '#1086dd' : ($hist->situacao == 'Cancelada' ? '#dc3545' : 'var(--dark-cinz,#8788a4)') ?>; color:#fff"><?= $hist->situacao ?></span></td>
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
