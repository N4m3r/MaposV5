<?php
/**
 * NFS-e - Sub-aba Servicos dentro de Notas Fiscais
 * Tema MapOS: widget-box, widget-title, widget-content
 */

$tributacao = $tributacao ?? [
    'codigo_tributacao_nacional' => '010701',
    'codigo_tributacao_municipal' => '100',
    'descricao_servico' => 'Suporte técnico em informatica, inclusive instalacao, configuracao e manutencao de programas de computacao e bancos de dados.',
    'aliquota_iss' => '5.00',
];

$emitente = $emitente ?? null;
$totalServico = floatval($totalServico ?? 0);
$totalProdutos = floatval($totalProdutos ?? 0);
$servicos = $servicos ?? [];
$produtos = $produtos ?? [];

$descontoTomador = floatval($result->valor_desconto ?? 0);
// Valor base: desconto negociado > 0 ? desconto : totalServico
// Fallback: nunca deixar 0 se houver servicos ou produtos
$valorBaseNFSe = $descontoTomador > 0 ? $descontoTomador : ($totalServico > 0 ? $totalServico : $totalProdutos);
$ambiente = $ambiente ?? 'homologacao';
$regimeTributario = $regimeTributario ?? ($tributacao['regime'] ?? 'simples_nacional');
$isSimplesNacional = ($regimeTributario === 'simples_nacional');
$regimeLabel = $isSimplesNacional ? 'Simples Nacional' : 'Lucro Presumido';

// Mostrar wizard se nao houver NFSe ativa ou se estiver cancelada
$mostrarWizard = !$nfse_atual || (isset($nfse_atual->situacao) && $nfse_atual->situacao == 'Cancelada');

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
        <div class="alert alert-warning">
            <i class="fas fa-flask"></i> <strong>Ambiente de Homologacao</strong> — NFS-e de teste, sem valor fiscal.
            <a href="<?= site_url('certificado/configurar') ?>">Alterar para Producao</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($nfse_atual && !$mostrarWizard): ?>
    <!-- ===== NFSe EMITIDA ===== -->
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                <h5>NFS-e - Nota Fiscal de Servico</h5>
                <span class="label label-<?= $nfse_atual->situacao == 'Emitida' ? 'success' : 'warning' ?>" style="margin:8px 10px 0 0; float:right">
                    <?= $nfse_atual->situacao ?>
                </span>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span6">
                        <table class="table table-condensed" style="margin-bottom:0">
                            <tr><td style="border:none; padding:2px 0"><strong>Numero:</strong></td><td style="border:none; padding:2px 0"><?= $nfse_atual->numero_nfse ?: 'Pendente' ?></td></tr>
                            <tr><td style="border:none; padding:2px 0"><strong>Data Emissao:</strong></td><td style="border:none; padding:2px 0"><?= $nfse_atual->data_emissao ? date('d/m/Y H:i', strtotime($nfse_atual->data_emissao)) : '---' ?></td></tr>
                            <?php if (!empty($nfse_atual->chave_acesso)): ?>
                            <tr><td style="border:none; padding:2px 0"><strong>Chave:</strong></td><td style="border:none; padding:2px 0"><small style="font-family:monospace"><?= $nfse_atual->chave_acesso ?></small></td></tr>
                            <?php endif; ?>
                            <?php if (!empty($nfse_atual->protocolo)): ?>
                            <tr><td style="border:none; padding:2px 0"><strong>Protocolo:</strong></td><td style="border:none; padding:2px 0"><?= $nfse_atual->protocolo ?></td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="span6 text-right">
                        <table class="table table-condensed" style="margin-bottom:0">
                            <tr><td style="border:none; padding:2px 0"><strong>Valor Servicos:</strong></td><td style="border:none; padding:2px 0"><?= fmtMoney($nfse_atual->valor_servicos) ?></td></tr>
                            <tr><td style="border:none; padding:2px 0"><strong>Impostos:</strong></td><td style="border:none; padding:2px 0"><?= fmtMoney($nfse_atual->valor_total_impostos) ?></td></tr>
                            <tr><td style="border:none; padding:2px 0"><strong style="color:#2e7d32">Valor Liquido:</strong></td><td style="border:none; padding:2px 0"><strong style="color:#2e7d32"><?= fmtMoney($nfse_atual->valor_liquido) ?></strong></td></tr>
                        </table>
                    </div>
                </div>

                <hr style="margin:10px 0">

                <h6><i class="fas fa-calculator"></i> Detalhamento dos Impostos / DAS</h6>
                <?php
                $nfseRegime = $nfse_atual->regime_tributario ?? 'simples_nacional';
                $isNfseSimples = ($nfseRegime === 'simples_nacional');
                $aliquotaEfetiva = $nfse_atual->valor_servicos > 0 ? round(($nfse_atual->valor_total_impostos / $nfse_atual->valor_servicos) * 100, 2) : 0;
                ?>
                <div class="well well-small">
                    <?php if ($isNfseSimples): ?>
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label label-success">Simples Nacional</span>
                            <div style="margin-top:6px">
                                <strong>DAS Estimado:</strong> <?= fmtMoney($nfse_atual->valor_total_impostos) ?><br>
                                <strong>Aliquota Efetiva:</strong> <?= number_format($aliquotaEfetiva, 2, ',', '.') ?>%<br>
                                <strong>Competencia:</strong> <?= date('m/Y', strtotime($nfse_atual->competencia ?? date('Y-m-01'))) ?>
                            </div>
                        </div>
                        <div class="span6">
                            <strong>Base Calculo:</strong> <?= fmtMoney($nfse_atual->valor_servicos) ?><br>
                            <strong>Registrado no DRE:</strong> <i class="fas fa-check-circle" style="color:#27ae60"></i> Sim
                        </div>
                    </div>
                    <div style="margin-top:8px; font-size:11px; color:#666">
                        <i class="fas fa-info-circle" style="color:#27ae60"></i> O DAS e recolhido mensalmente pelo prestador. O valor estimado foi registrado no DRE para controle.
                    </div>
                    <?php else: ?>
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label label-info">Lucro Presumido</span>
                            <div style="margin-top:6px">
                                <strong>ISS (<?= $nfse_atual->aliquota_iss ?>%):</strong> <?= fmtMoney($nfse_atual->valor_iss) ?><br>
                                <strong>PIS:</strong> <?= fmtMoney($nfse_atual->valor_pis) ?><br>
                                <strong>COFINS:</strong> <?= fmtMoney($nfse_atual->valor_cofins) ?>
                            </div>
                        </div>
                        <div class="span6">
                            <strong>IRRF:</strong> <?= fmtMoney($nfse_atual->valor_irrf) ?><br>
                            <strong>CSLL:</strong> <?= fmtMoney($nfse_atual->valor_csll) ?><br>
                            <strong>INSS:</strong> <?= fmtMoney($nfse_atual->valor_inss) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (floatval($nfse_atual->valor_total_retencao ?? 0) > 0): ?>
                    <hr style="margin:8px 0">
                    <div style="font-size:12px">
                        <strong style="color:#e67e22"><i class="fas fa-hand-holding-usd"></i> Retencoes do Tomador:</strong> <?= fmtMoney($nfse_atual->valor_total_retencao) ?>
                        <small style="color:#888">(reduz o valor da NFS-e, registrada para compensacao no DAS)</small>
                        <div style="margin-top:4px">
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
                    <a href="<?= site_url('nfse_os/imprimir_nfse/' . $nfse_atual->id) ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Imprimir NFS-e
                    </a>
                    <?php if (!empty($nfse_atual->url_danfe)): ?>
                        <a href="<?= htmlspecialchars($nfse_atual->url_danfe, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-success">
                            <i class="fas fa-external-link-alt"></i> DANFSe Nacional
                        </a>
                    <?php endif; ?>
                    <?php if ($nfse_atual->link_impressao): ?>
                        <a href="<?= htmlspecialchars($nfse_atual->link_impressao, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-success">
                            <i class="fas fa-print"></i> Imprimir Original
                        </a>
                    <?php endif; ?>
                    <?php if ($nfse_atual->xml_path): ?>
                        <a href="<?= base_url(htmlspecialchars($nfse_atual->xml_path, ENT_QUOTES, 'UTF-8')) ?>" target="_blank" class="btn btn-info">
                            <i class="fas fa-file-code"></i> Download XML
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($nfse_atual->chave_acesso)): ?>
                        <button type="button" class="btn btn-info" onclick="consultarNFSeNacional(<?= $nfse_atual->id ?>)">
                            <i class="fas fa-sync-alt"></i> Consultar
                        </button>
                    <?php endif; ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNFSe') && $nfse_atual->situacao != 'Cancelada'): ?>
                        <?php if (!empty($nfse_atual->chave_acesso)): ?>
                            <button type="button" class="btn btn-danger" onclick="cancelarNFSeNacional(<?= $nfse_atual->id ?>)">
                                <i class="fas fa-times"></i> Cancelar Nacional
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-danger" onclick="cancelarNFSe(<?= $nfse_atual->id ?>)">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div id="nfse-consulta-resultado" style="display:none; margin-top:10px">
                    <div class="alert alert-info" id="nfse-consulta-conteudo"></div>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ===== WIZARD EMISSAO NFS-e ===== -->
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                <h5>Emitir NFS-e — OS #<?= $result->idOs ?></h5>
            </div>
            <div class="widget-content">

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
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="well well-small">
                                <h6 style="margin-top:0"><i class="fas fa-building"></i> Prestador</h6>
                                <?php if ($emitente): ?>
                                    <strong><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '') ?></strong><br>
                                    CNPJ: <?= fmtDoc($emitente->cnpj ?? '') ?><br>
                                    <?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? ''))) ?><br>
                                    CEP: <?= $emitente->cep ?? '' ?>
                                <?php else: ?>
                                    <span class="text-warning">Dados do emitente nao configurados</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="well well-small">
                                <h6 style="margin-top:0"><i class="fas fa-user"></i> Tomador</h6>
                                <strong><?= htmlspecialchars($result->nomeCliente ?? '') ?></strong><br>
                                <?= !empty($result->cnpj) ? 'CNPJ: ' . fmtDoc($result->cnpj) : (!empty($result->cpf_cgc) ? 'CPF/CNPJ: ' . fmtDoc($result->cpf_cgc) : 'Documento nao informado') ?><br>
                                <?= htmlspecialchars(trim(($result->rua ?? '') . ', ' . ($result->numero ?? '') . ' - ' . ($result->bairro ?? ''))) ?><br>
                                <?= htmlspecialchars(trim(($result->cidade ?? '') . '/' . ($result->estado ?? '') . ' - CEP: ' . ($result->cep ?? ''))) ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($servicos) || !empty($produtos)): ?>
                    <h6><i class="fas fa-list"></i> Itens da OS</h6>
                    <table class="table table-condensed table-bordered">
                        <thead>
                            <tr><th>Item</th><th>Qtd</th><th class="text-right">Unit.</th><th class="text-right">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicos as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s->nome ?? 'Servico') ?></td>
                                <td><?= $s->quantidade ?? 1 ?></td>
                                <td class="text-right"><?= fmtMoney($s->preco ?: $s->precoVenda) ?></td>
                                <td class="text-right"><?= fmtMoney(($s->preco ?: $s->precoVenda) * ($s->quantidade ?: 1)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p->descricao ?? $p->nomeProduto ?? 'Produto') ?></td>
                                <td><?= $p->quantidade ?? 1 ?></td>
                                <td class="text-right"><?= fmtMoney($p->preco ?? (($p->subTotal ?? 0) / max(1, $p->quantidade ?? 1))) ?></td>
                                <td class="text-right"><?= fmtMoney($p->subTotal) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr><td colspan="3" class="text-right"><strong>Total Servicos:</strong></td><td class="text-right"><strong><?= fmtMoney($totalServico) ?></strong></td></tr>
                            <?php if ($totalProdutos > 0): ?>
                            <tr><td colspan="3" class="text-right"><strong>Total Produtos:</strong></td><td class="text-right"><strong><?= fmtMoney($totalProdutos) ?></strong></td></tr>
                            <?php endif; ?>
                            <?php if ($descontoTomador > 0): ?>
                            <tr><td colspan="3" class="text-right"><strong style="color:#e67e22">Desconto Tomador:</strong></td><td class="text-right"><strong style="color:#e67e22"><?= fmtMoney($descontoTomador) ?></strong></td></tr>
                            <?php endif; ?>
                            <tr><td colspan="3" class="text-right"><strong>Total OS:</strong></td><td class="text-right"><strong style="color:#2e7d32; font-size:14px"><?= fmtMoney($totalServico + $totalProdutos) ?></strong></td></tr>
                        </tfoot>
                    </table>
                    <?php endif; ?>

                    <?php if ($totalProdutos > 0): ?>
                    <div class="well well-small">
                        <label class="checkbox" style="margin:0; font-size:14px">
                            <input type="checkbox" id="incluir-produtos-nfse" value="1" style="margin-right:5px">
                            <strong>Incluir Produtos no Valor da NFS-e</strong>
                            <small style="color:#888; font-weight:normal">(R$ <?= number_format($totalProdutos, 2, ',', '.') ?>)</small>
                        </label>
                        <p style="margin:5px 0 0 0; font-size:11px; color:#888">
                            <i class="fas fa-exclamation-triangle" style="color:#e67e22"></i> Verifique a legislacao municipal antes de incluir produtos na NFS-e de servicos.
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="well well-small" style="margin-top:12px; margin-bottom:10px">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px">
                            <i class="fas fa-landmark" style="font-size:16px; color:<?= $isSimplesNacional ? '#27ae60' : '#3498db' ?>"></i>
                            <strong style="font-size:14px">Regime Tributario: <?= $regimeLabel ?></strong>
                            <span class="label label-<?= $isSimplesNacional ? 'success' : 'info' ?>"><?= $isSimplesNacional ? 'DAS' : 'Impostos Individuais' ?></span>
                        </div>
                        <?php if ($isSimplesNacional): ?>
                        <div style="margin-bottom:10px">
                            <strong><i class="fas fa-receipt" style="color:#27ae60"></i> DAS Estimado:</strong>
                            <span id="das-valor-display" style="color:#27ae60; font-weight:bold">—</span>
                            <small style="color:#666; display:block; margin-top:4px">No Simples Nacional, o imposto e recolhido via DAS mensal. Valor calculado no Passo 2.</small>
                        </div>
                        <?php else: ?>
                        <div style="margin-bottom:10px; font-size:12px; color:#555">
                            <i class="fas fa-info-circle" style="color:#3498db"></i> Lucro Presumido: impostos calculados individualmente (ISS, IRRF, PIS, COFINS, CSLL).
                        </div>
                        <?php endif; ?>

                        <hr style="margin:10px 0; border-top:1px solid #e5e5e5">
                        <div style="margin-bottom:6px"><strong style="font-size:13px"><i class="fas fa-hand-holding-usd" style="color:#e67e22"></i> Retencoes do Tomador</strong> <small style="color:#888">(impostos retidos na fonte)</small></div>
                        <div class="row-fluid">
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px"><input type="checkbox" id="retem-iss" name="retem_iss" value="1"> ISS (<span id="aliquota-iss-display"><?= $tributacao['aliquota_iss'] ?? '5.00' ?>%</span>) <span id="retem-iss-valor" style="color:#e67e22; font-weight:bold"></span></label></div>
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px"><input type="checkbox" id="retem-irrf" name="retem_irrf" value="1"> IRRF (1,5%) <span id="retem-irrf-valor" style="color:#e67e22; font-weight:bold"></span></label></div>
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px"><input type="checkbox" id="retem-pis" name="retem_pis" value="1"> PIS (0,65%) <span id="retem-pis-valor" style="color:#e67e22; font-weight:bold"></span></label></div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px"><input type="checkbox" id="retem-cofins" name="retem_cofins" value="1"> COFINS (3,0%) <span id="retem-cofins-valor" style="color:#e67e22; font-weight:bold"></span></label></div>
                            <div class="span4"><label class="checkbox" style="font-size:12px; margin-bottom:5px"><input type="checkbox" id="retem-csll" name="retem_csll" value="1"> CSLL (1,0%) <span id="retem-csll-valor" style="color:#e67e22; font-weight:bold"></span></label></div>
                            <div class="span4"><strong style="font-size:13px">Total Retido:</strong> <span id="retem-total-valor" style="color:#e67e22; font-weight:bold; font-size:14px">R$ 0,00</span></div>
                        </div>
                        <p style="margin:4px 0 0 0; font-size:11px; color:#888"><i class="fas fa-exclamation-circle" style="color:#e67e22"></i> As retencoes NAO reduzem o valor da NFS-e. Sao registradas para controle e compensacao no DAS.</p>
                        <div id="aviso-retencao-iss" style="display:none; margin-top:8px; padding:8px 12px; background:#fff3cd; border:1px solid #ffc107; border-radius:4px">
                            <strong style="color:#856404; font-size:12px"><i class="fas fa-exclamation-triangle"></i> Atencao — Retencao de ISS pelo Tomador</strong>
                            <p style="margin:4px 0 0 0; font-size:11px; color:#856404">Ao emitir esta NFS-e com retencao de ISS, marque <strong>"Sim"</strong> para <em>Retencao do ISSQN</em> no Portal do Contribuidor. O sistema ja configurara <strong>IssRetido = Sim</strong> no XML.</p>
                        </div>
                    </div>

                    <div class="row-fluid" style="margin-top:15px">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label"><strong>Valor dos Servicos:</strong></label>
                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <span class="add-on">R$</span>
                                        <input type="text" name="valor_servicos" id="valor-servicos-wizard" class="span8" value="<?= number_format($valorBaseNFSe, 2, ',', '.') ?>" placeholder="0,00">
                                    </div>
                                    <span class="help-block" id="valor-servicos-help">Valor dos servicos prestados</span>
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label"><strong>Deducoes:</strong></label>
                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <span class="add-on">R$</span>
                                        <input type="text" name="valor_deducoes" id="valor-deducoes-wizard" class="span8" value="0,00" placeholder="0,00">
                                    </div>
                                    <span class="help-block">Deducoes legais (materiais, insumos)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><strong>Descricao do Servico:</strong></label>
                        <div class="controls">
                            <textarea name="descricao_servico" id="descricao-servico-wizard" class="span12" rows="3"><?= htmlspecialchars($tributacao['descricao_servico']) ?></textarea>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Codigo Tributacao LC 116:</label>
                                <div class="controls">
                                    <input type="text" class="span12" value="<?= $tributacao['codigo_tributacao_nacional'] ?>" readonly style="background:#f5f5f5">
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Codigo Municipal:</label>
                                <div class="controls">
                                    <input type="text" class="span12" value="<?= $tributacao['codigo_tributacao_municipal'] ?>" readonly style="background:#f5f5f5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 2 -->
                <div class="wizard-step-panel" id="wizard-step-2">
                    <div class="row-fluid">
                        <div class="span6">
                            <h6><i class="fas fa-calculator"></i> Calculo de Impostos <span class="label label-<?= $isSimplesNacional ? 'success' : 'info' ?>" style="font-size:11px"><?= $regimeLabel ?></span></h6>
                            <table class="table table-condensed" id="impostos-table" style="font-size:13px">
                                <tbody>
                                    <tr><td><strong>Valor Bruto</strong></td><td class="text-right" id="imp-valor-bruto">—</td></tr>
                                    <tr><td><strong>(-) Deducoes</strong></td><td class="text-right" id="imp-deducoes">—</td></tr>
                                    <tr><td><strong>Base de Calculo</strong></td><td class="text-right" id="imp-base-calculo">—</td></tr>
                                    <?php if ($isSimplesNacional): ?>
                                    <tr class="success"><td><strong><i class="fas fa-receipt"></i> DAS (Simples Nacional)</strong></td><td class="text-right" id="imp-das"><strong>—</strong></td></tr>
                                    <tr><td style="font-size:11px; color:#888; padding-left:20px">Aliquota efetiva: <span id="imp-das-aliquota">—</span></td><td></td></tr>
                                    <?php else: ?>
                                    <tr><td>ISS (<?= $tributacao['aliquota_iss'] ?>%)</td><td class="text-right" id="imp-iss">—</td></tr>
                                    <tr><td>PIS</td><td class="text-right" id="imp-pis">—</td></tr>
                                    <tr><td>COFINS</td><td class="text-right" id="imp-cofins">—</td></tr>
                                    <tr><td>IRRF</td><td class="text-right" id="imp-irrf">—</td></tr>
                                    <tr><td>CSLL</td><td class="text-right" id="imp-csll">—</td></tr>
                                    <tr><td>INSS/CPP</td><td class="text-right" id="imp-inss">—</td></tr>
                                    <?php endif; ?>
                                    <tr style="border-top:2px solid #ccc"><td><strong>Total Impostos</strong></td><td class="text-right" id="imp-total-impostos"><strong>—</strong></td></tr>
                                    <tr id="retencao-row" style="display:none"><td style="color:#e67e22"><i class="fas fa-hand-holding-usd"></i> (-) Retencoes Tomador</td><td class="text-right" id="imp-retencao-total" style="color:#e67e22">—</td></tr>
                                    <tr class="success"><td><strong style="font-size:14px">Valor Liquido</strong></td><td class="text-right" id="imp-valor-liquido"><strong style="font-size:14px">—</strong></td></tr>
                                </tbody>
                            </table>
                            <p style="font-size:11px; color:#888; margin-top:5px">
                                <?php if ($isSimplesNacional): ?>
                                <i class="fas fa-info-circle" style="color:#27ae60"></i> Simples Nacional: imposto recolhido via DAS mensal. O valor liquido NAO e reduzido pelas retencoes.
                                <?php else: ?>
                                <i class="fas fa-info-circle" style="color:#3498db"></i> Lucro Presumido: impostos calculados individualmente. O valor liquido NAO e reduzido pelas retencoes.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="span6">
                            <div class="well well-small" style="text-align:center; padding:30px 20px">
                                <i class="fas fa-file-pdf" style="font-size:48px; color:#d9534f; display:block; margin-bottom:15px"></i>
                                <h5>Pre-visualizacao do Documento NFS-e</h5>
                                <p style="color:#666; margin:10px 0">Clique abaixo para gerar um PDF com a pre-visualizacao completa.</p>
                                <button type="button" class="btn btn-primary btn-large" id="btn-preview-nfse"><i class="fas fa-eye"></i> Pre-visualizar NFS-e (PDF)</button>
                                <p style="color:#999; font-size:11px; margin-top:10px">O PDF sera aberto em uma nova aba.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 3 -->
                <div class="wizard-step-panel" id="wizard-step-3">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label class="checkbox" style="font-size:14px; margin-bottom:15px">
                                    <input type="checkbox" id="gerar-boleto-wizard" value="1" checked>
                                    <strong>Gerar boleto de cobranca junto com a NFS-e</strong>
                                </label>
                            </div>
                            <div id="boleto-campos">
                                <div class="alert alert-info" style="margin-bottom:15px">
                                    <i class="fas fa-info-circle"></i> O boleto sera gerado com o <strong>valor integral</strong> dos servicos.
                                    <?php if ($isSimplesNacional): ?>O DAS (Simples Nacional) e recolhido mensalmente pelo prestador e <strong>nao desconta</strong> do boleto.<?php endif; ?>
                                </div>
                                <div class="row-fluid">
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Valor do Boleto:</strong></label>
                                            <div class="controls">
                                                <div class="input-prepend input-append">
                                                    <span class="add-on">R$</span>
                                                    <input type="text" id="valor-boleto-wizard" class="span8" readonly style="background:#f5f5f5; font-weight:bold; color:#2e7d32">
                                                </div>
                                                <small id="valor-boleto-ajuda" style="color:#888; font-size:11px">Valor integral = Servicos - Deducoes</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Data de Vencimento:</strong></label>
                                            <div class="controls">
                                                <input type="date" name="data_vencimento" id="data-vencimento-wizard" class="span12" value="<?= date('Y-m-d', strtotime('+5 days')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Instrucoes:</strong></label>
                                            <div class="controls">
                                                <textarea name="instrucoes" id="instrucoes-boleto-wizard" class="span12" rows="2">Pagavel em qualquer banco ate o vencimento. Apos o vencimento, consultar multas e juros.</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="valor-integral-section" style="display:none; margin-top:12px; padding:12px 15px; background:#fff3cd; border:1px solid #ffc107; border-radius:4px">
                                    <label class="checkbox" style="font-size:13px; margin-bottom:5px">
                                        <input type="checkbox" id="valor-integral-wizard" name="valor_integral" value="1" checked>
                                        <strong>Emitir boleto com valor integral</strong> (nao descontar retencoes)
                                    </label>
                                    <small style="color:#856404; display:block; margin-top:4px"><i class="fas fa-info-circle"></i> Quando ha retencao pelo tomador, o boleto pode ser emitido com valor integral. As retencoes serao registradas para compensacao no DAS mensal.</small>
                                </div>
                            </div>
                            <div id="sem-boleto-msg" style="display:none">
                                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Nenhum boleto sera gerado. Voce podera gerar separadamente apos a emissao.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 4 -->
                <div class="wizard-step-panel" id="wizard-step-4">
                    <div class="alert alert-info" style="margin-bottom:20px">
                        <i class="fas fa-clipboard-check"></i> <strong>Resumo da Emissao</strong> — Verifique os dados antes de confirmar.
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="well well-small">
                                <h6 style="margin-top:0"><i class="fas fa-file-invoice"></i> NFS-e <span class="label label-<?= $isSimplesNacional ? 'success' : 'info' ?>" style="font-size:11px"><?= $regimeLabel ?></span></h6>
                                <table class="table table-condensed" style="margin-bottom:0; font-size:12px">
                                    <tr><td style="border:none; padding:2px 0"><strong>Prestador:</strong></td><td style="border:none; padding:2px 0"><span id="res-prestador"><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '—') ?></span></td></tr>
                                    <tr><td style="border:none; padding:2px 0"><strong>Tomador:</strong></td><td style="border:none; padding:2px 0"><span id="res-tomador"><?= htmlspecialchars($result->nomeCliente ?? '—') ?></span></td></tr>
                                    <tr><td style="border:none; padding:2px 0"><strong>Valor Servicos:</strong></td><td style="border:none; padding:2px 0"><span id="res-valor-servicos">—</span></td></tr>
                                    <tr><td style="border:none; padding:2px 0"><strong>Deducoes:</strong></td><td style="border:none; padding:2px 0"><span id="res-deducoes">—</span></td></tr>
                                    <tr id="res-imposto-linha"><td style="border:none; padding:2px 0"><strong>Total Impostos:</strong></td><td style="border:none; padding:2px 0"><span id="res-total-impostos">—</span></td></tr>
                                    <tr id="res-das-linha" style="display:<?= $isSimplesNacional ? 'table-row' : 'none' ?>"><td style="border:none; padding:2px 0"><strong style="color:#27ae60">DAS:</strong></td><td style="border:none; padding:2px 0"><span id="res-valor-das" style="color:#27ae60; font-weight:bold">—</span></td></tr>
                                    <tr id="res-retencao-linha" style="display:none"><td style="border:none; padding:2px 0"><strong style="color:#e67e22">Retencoes:</strong></td><td style="border:none; padding:2px 0"><span id="res-retencao-total" style="color:#e67e22; font-weight:bold">—</span></td></tr>
                                    <tr><td style="border:none; padding:4px 0 0 0; border-top:1px solid #ddd"><strong style="color:#2e7d32; font-size:14px">Valor Liquido:</strong></td><td style="border:none; padding:4px 0 0 0; border-top:1px solid #ddd"><span id="res-valor-liquido" style="color:#2e7d32; font-size:14px; font-weight:bold">—</span></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="well well-small" id="res-boleto-section">
                                <h6 style="margin-top:0"><i class="fas fa-barcode"></i> Boleto</h6>
                                <p id="res-boleto-info">—</p>
                            </div>
                            <div class="well well-small" style="border-left:3px solid #d9534f">
                                <h6 style="margin-top:0"><i class="fas fa-exclamation-triangle"></i> Atencao</h6>
                                <p style="margin-bottom:0">Apos confirmar, a NFS-e sera emitida junto com o boleto (se selecionado). Esta acao nao pode ser desfeita facilmente.</p>
                                <?php if ($ambiente == 'homologacao'): ?>
                                <p style="color:#856404; font-weight:bold; margin-bottom:0"><i class="fas fa-flask"></i> Modo Homologacao: NFS-e de teste, sem valor fiscal.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegacao -->
                <div class="wizard-nav">
                    <button type="button" class="btn" id="btn-wizard-anterior" disabled><i class="fas fa-arrow-left"></i> Anterior</button>
                    <button type="button" class="btn btn-primary" id="btn-wizard-proximo">Proximo <i class="fas fa-arrow-right"></i></button>
                    <button type="button" class="btn btn-success" id="btn-wizard-emitir" style="display:none"><i class="fas fa-check-circle"></i> Emitir NFS-e (API Nacional)</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historico -->
    <?php if ($nfse_atual && !empty($historico_nfse) && is_array($historico_nfse) && count($historico_nfse) > 1): ?>
    <div class="span12" style="margin-top:10px">
        <div class="widget-box">
            <div class="widget-title"><span class="icon"><i class="fas fa-history"></i></span><h5>Historico de NFS-e</h5></div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>#ID</th><th>Numero</th><th>Data</th><th>Valor</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($historico_nfse as $hist): ?>
                            <?php if ($hist->id != ($nfse_atual->id ?? 0)): ?>
                                <tr>
                                    <td><?= $hist->id ?></td>
                                    <td><?= $hist->numero_nfse ?: '---' ?></td>
                                    <td><?= $hist->data_emissao ? date('d/m/Y', strtotime($hist->data_emissao)) : '---' ?></td>
                                    <td><?= fmtMoney($hist->valor_liquido) ?></td>
                                    <td><span class="label label-<?= $hist->situacao == 'Emitida' ? 'success' : ($hist->situacao == 'Cancelada' ? 'danger' : 'default') ?>"><?= $hist->situacao ?></span></td>
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
