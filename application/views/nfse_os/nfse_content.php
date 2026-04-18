<?php
/**
 * Conteúdo NFS-e - Card de status ou Wizard de emissão
 * Usado como sub-aba dentro de Notas Fiscais
 */

// Dados tributários com fallback
$tributacao = $tributacao ?? [
    'codigo_tributacao_nacional' => '010701',
    'codigo_tributacao_municipal' => '100',
    'descricao_servico' => 'Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.',
    'aliquota_iss' => '5.00',
];

$emitente = $emitente ?? null;
$totalServico = $totalServico ?? 0;
$totalProdutos = $totalProdutos ?? 0;
$servicos = $servicos ?? [];
$produtos = $produtos ?? [];

$valorTotalOS = floatval($totalServico) + floatval($totalProdutos);
$descontoTomador = floatval($result->valor_desconto ?? 0);
// Padrão: NFSe com valor de serviços apenas
$valorServicosNFSe = $descontoTomador > 0 ? $descontoTomador : floatval($totalServico);
$ambiente = $ambiente ?? 'homologacao';
$regimeTributario = $regimeTributario ?? ($tributacao['regime'] ?? 'simples_nacional');
$isSimplesNacional = ($regimeTributario === 'simples_nacional');
$regimeLabel = $isSimplesNacional ? 'Simples Nacional' : 'Lucro Presumido';

if (!function_exists('formatarMoedaNFSe')) {
    function formatarMoedaNFSe($valor) {
        return 'R$ ' . number_format(floatval($valor), 2, ',', '.');
    }
}

if (!function_exists('formatarDocumentoNFSe')) {
    function formatarDocumentoNFSe($doc) {
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

<div class="row-fluid" style="margin-top: 20px;">

    <?php if ($ambiente == 'homologacao'): ?>
    <div class="alert alert-warning" style="margin-bottom: 15px;">
        <i class="fas fa-flask"></i> <strong>Ambiente de Homologação</strong> — As NFS-e emitidas serão de teste, sem valor fiscal.
        <a href="<?= site_url('certificado/configurar') ?>" style="text-decoration: underline;">Alterar para Produção</a>
    </div>
    <?php else: ?>
    <div class="alert alert-success" style="margin-bottom: 15px;">
        <i class="fas fa-shield-alt"></i> <strong>Ambiente de Produção</strong> — NFS-e emitidas com valor fiscal real.
    </div>
    <?php endif; ?>

    <?php if ($nfse_atual): ?>
    <!-- NFS-e JÁ EMITIDA - Card de status -->
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                <h5>NFS-e - Nota Fiscal de Serviço</h5>
            </div>

            <div class="widget-content">
                <div class="alert alert-<?= $nfse_atual->situacao == 'Emitida' ? 'success' : ($nfse_atual->situacao == 'Pendente' ? 'warning' : 'danger') ?>">
                    <div class="row-fluid">
                        <div class="span6">
                            <strong>Status:</strong> <span class="label label-<?= $nfse_atual->situacao == 'Emitida' ? 'success' : ($nfse_atual->situacao == 'Pendente' ? 'warning' : 'danger') ?>"><?= $nfse_atual->situacao ?></span>
                            <?php if (isset($nfse_atual->ambiente) && $nfse_atual->ambiente): ?>
                                <span class="label label-<?= $nfse_atual->ambiente == 'producao' ? 'success' : 'warning' ?>"><?= $nfse_atual->ambiente == 'producao' ? 'Produção' : 'Homologação' ?></span>
                            <?php endif; ?>
                            <br>
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

                <div class="btn-group">
                    <a href="<?= site_url('nfse_os/imprimir_nfse/' . $nfse_atual->id) ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Imprimir NFS-e
                    </a>

                    <?php if ($nfse_atual->link_impressao): ?>
                        <a href="<?= $nfse_atual->link_impressao ?>" target="_blank" class="btn btn-success">
                            <i class="fas fa-print"></i> Imprimir (Original)
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
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- WIZARD DE EMISSÃO NFS-e -->
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                <h5>Emitir NFS-e - Ordem de Serviço #<?= $result->idOs ?></h5>
            </div>

            <div class="widget-content">
                <!-- Barra de Passos -->
                <ul class="wizard-steps" id="wizard-steps">
                    <li class="active" data-step="1">
                        <span class="wizard-step-number">1</span>
                        <span class="wizard-step-label">Dados do Serviço</span>
                    </li>
                    <li data-step="2">
                        <span class="wizard-step-number">2</span>
                        <span class="wizard-step-label">Impostos & Preview</span>
                    </li>
                    <li data-step="3">
                        <span class="wizard-step-number">3</span>
                        <span class="wizard-step-label">Boleto</span>
                    </li>
                    <li data-step="4">
                        <span class="wizard-step-number">4</span>
                        <span class="wizard-step-label">Confirmação</span>
                    </li>
                </ul>

                <!-- PASSO 1: Dados do Serviço -->
                <div class="wizard-step-panel active" id="wizard-step-1">
                    <div class="row-fluid">
                        <!-- Prestador -->
                        <div class="span6">
                            <div class="wizard-info-card">
                                <h6><i class="fas fa-building"></i> Prestador</h6>
                                <?php if ($emitente): ?>
                                    <p><strong><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '') ?></strong></p>
                                    <p>CNPJ: <?= formatarDocumentoNFSe($emitente->cnpj ?? '') ?></p>
                                    <p><?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? ''))) ?></p>
                                    <p>CEP: <?= $emitente->cep ?? '' ?></p>
                                <?php else: ?>
                                    <p class="text-warning">Dados do emitente não configurados</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tomador -->
                        <div class="span6">
                            <div class="wizard-info-card">
                                <h6><i class="fas fa-user"></i> Tomador</h6>
                                <p><strong><?= htmlspecialchars($result->nomeCliente ?? '') ?></strong></p>
                                <p><?= !empty($result->cnpj) ? 'CNPJ: ' . formatarDocumentoNFSe($result->cnpj) : (!empty($result->cpf_cgc) ? 'CPF/CNPJ: ' . formatarDocumentoNFSe($result->cpf_cgc) : 'Documento não informado') ?></p>
                                <p><?= htmlspecialchars(trim(($result->rua ?? '') . ', ' . ($result->numero ?? '') . ' - ' . ($result->bairro ?? ''))) ?></p>
                                <p><?= htmlspecialchars(trim(($result->cidade ?? '') . '/' . ($result->estado ?? '') . ' - CEP: ' . ($result->cep ?? ''))) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes da OS -->
                    <?php if (!empty($servicos) || !empty($produtos)): ?>
                    <h6 style="margin-top: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                        <i class="fas fa-list"></i> Itens da Ordem de Serviço
                    </h6>
                    <table class="table table-condensed table-bordered wizard-services-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qtd</th>
                                <th class="text-right">Valor Unit.</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicos as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s->nome ?? 'Serviço') ?></td>
                                <td><?= $s->quantidade ?? 1 ?></td>
                                <td class="text-right"><?= formatarMoedaNFSe($s->preco ?: $s->precoVenda) ?></td>
                                <td class="text-right"><?= formatarMoedaNFSe(($s->preco ?: $s->precoVenda) * ($s->quantidade ?: 1)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p->descricao ?? $p->nomeProduto ?? 'Produto') ?></td>
                                <td><?= $p->quantidade ?? 1 ?></td>
                                <td class="text-right"><?= formatarMoedaNFSe($p->preco ?? $p->subTotal / max(1, $p->quantidade)) ?></td>
                                <td class="text-right"><?= formatarMoedaNFSe($p->subTotal) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total Serviços:</strong></td>
                                <td class="text-right"><strong><?= formatarMoedaNFSe($totalServico) ?></strong></td>
                            </tr>
                            <?php if ($totalProdutos > 0): ?>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total Produtos:</strong></td>
                                <td class="text-right"><strong><?= formatarMoedaNFSe($totalProdutos) ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total OS:</strong></td>
                                <td class="text-right"><strong style="color: #2e7d32; font-size: 14px;"><?= formatarMoedaNFSe($valorTotalOS) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php endif; ?>

                    <?php if ($totalProdutos > 0): ?>
                    <!-- Painel persistente: inclusão de produtos na NFS-e -->
                    <div id="painel-incluir-produtos" style="margin-top: 12px; margin-bottom: 10px; border: 2px solid #f0ad4e; border-radius: 6px; background: #fdf8ed; padding: 12px 15px;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <div style="flex: 1;">
                                <label style="margin: 0; font-size: 14px; cursor: pointer;" for="incluir-produtos-nfse">
                                    <input type="checkbox" id="incluir-produtos-nfse" value="1" style="margin-right: 5px; transform: scale(1.2); vertical-align: middle;">
                                    <strong>Incluir Produtos no Valor da NFS-e</strong>
                                </label>
                                <div id="nfse-valor-info" style="margin-top: 8px; padding: 8px 12px; background: #fff; border: 1px solid #e0e0e0; border-radius: 4px;">
                                    <table style="width: 100%; font-size: 13px;">
                                        <tr>
                                            <td style="padding: 2px 0;"><i class="fas fa-wrench" style="color: #3498db; width: 16px;"></i> Total Serviços:</td>
                                            <td style="text-align: right; font-weight: bold; color: #27ae60; padding: 2px 0;"><?= formatarMoedaNFSe($totalServico) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 2px 0;"><i class="fas fa-box" style="color: #e67e22; width: 16px;"></i> Total Produtos:</td>
                                            <td style="text-align: right; font-weight: bold; color: #e67e22; padding: 2px 0;"><?= formatarMoedaNFSe($totalProdutos) ?></td>
                                        </tr>
                                        <tr style="border-top: 1px solid #ddd;">
                                            <td style="padding: 4px 0;"><strong><i class="fas fa-calculator" style="width: 16px;"></i> Valor da NFS-e:</strong></td>
                                            <td style="text-align: right; font-weight: bold; font-size: 14px; padding: 4px 0;" id="nfse-valor-display">
                                                <span id="nfse-valor-servicos" style="color: #27ae60;"><?= formatarMoedaNFSe($totalServico) ?></span>
                                                <span id="nfse-valor-total" style="color: #e67e22; display: none;"><?= formatarMoedaNFSe($valorTotalOS) ?></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <p style="margin: 6px 0 0 0; font-size: 11px; color: #888;">
                                    <i class="fas fa-exclamation-triangle" style="color: #e67e22;"></i>
                                    Atente-se à legislação municipal — nem todos os municípios permitem incluir produtos na NFS-e de serviços.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de Confirmação -->
                    <div id="modal-confirmar-produtos" class="modal hide fade" tabindex="-1" role="dialog" style="display: none;">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h5><i class="fas fa-exclamation-triangle" style="color: #e67e22;"></i> Confirmar Inclusão de Produtos na NFS-e</h5>
                        </div>
                        <div class="modal-body">
                            <p>Você está prestes a incluir <strong>produtos</strong> no valor da NFS-e de serviços.</p>
                            <table class="table table-bordered" style="margin-top: 10px;">
                                <tr><td>Valor atual (apenas serviços):</td><td style="text-align: right; font-weight: bold; color: #27ae60;"><?= formatarMoedaNFSe($totalServico) ?></td></tr>
                                <tr><td>Produtos a incluir:</td><td style="text-align: right; font-weight: bold; color: #e67e22;"><?= formatarMoedaNFSe($totalProdutos) ?></td></tr>
                                <tr><td><strong>Novo valor total:</strong></td><td style="text-align: right; font-weight: bold; font-size: 16px; color: #e67e22;"><?= formatarMoedaNFSe($valorTotalOS) ?></td></tr>
                            </table>
                            <br>
                            <p><strong>Digite "confirmar" para prosseguir:</strong></p>
                            <input type="text" id="input-confirmar-produtos" class="span4" placeholder="confirmar" autocomplete="off" style="font-size: 16px; height: 30px;" />
                            <span id="msg-erro-confirmar" style="color: #e74c3c; display: none; margin-left: 10px;"></span>
                        </div>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                            <button class="btn btn-warning" id="btn-confirmar-produtos"><i class="fas fa-check"></i> Confirmar</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Desconto do Tomador -->
                    <?php if ($descontoTomador > 0): ?>
                    <div class="discount-badge">
                        <i class="fas fa-tags"></i>
                        <strong>Desconto do Tomador:</strong> <?= formatarMoedaNFSe($descontoTomador) ?>
                        <small>(valor já negociado com o cliente)</small>
                    </div>
                    <?php endif; ?>

                    <!-- Regime Tributário & Retenções -->
                    <div id="regime-retencoes-section" style="margin-top: 12px; margin-bottom: 10px; border: 2px solid <?= $isSimplesNacional ? '#27ae60' : '#3498db' ?>; border-radius: 6px; padding: 12px 15px; background: <?= $isSimplesNacional ? '#eafaf1' : '#ebf5fb' ?>;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                            <i class="fas fa-landmark" style="font-size: 16px; color: <?= $isSimplesNacional ? '#27ae60' : '#3498db' ?>;"></i>
                            <strong style="font-size: 14px;">Regime Tributário: <?= $regimeLabel ?></strong>
                            <span class="label label-<?= $isSimplesNacional ? 'success' : 'info' ?>"><?= $isSimplesNacional ? 'DAS' : 'Impostos Individuais' ?></span>
                        </div>

                        <?php if ($isSimplesNacional): ?>
                        <div id="das-info" style="padding: 8px 12px; background: #fff; border: 1px solid #c8e6c9; border-radius: 4px; margin-bottom: 10px;">
                            <table style="width: 100%; font-size: 13px;">
                                <tr>
                                    <td style="padding: 2px 0;"><i class="fas fa-receipt" style="color: #27ae60; width: 16px;"></i> <strong>DAS (Documento de Arrecadação):</strong></td>
                                    <td style="text-align: right; font-weight: bold; color: #27ae60; padding: 2px 0;" id="das-valor-display">—</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding: 4px 0 0 0; font-size: 11px; color: #666;">
                                        <i class="fas fa-info-circle" style="color: #27ae60;"></i>
                                        No Simples Nacional, o imposto é recolhido via DAS mensal. O valor estimado será calculado no Passo 2.
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php else: ?>
                        <div id="lucro-presumido-info" style="padding: 8px 12px; background: #fff; border: 1px solid #b3d9f2; border-radius: 4px; margin-bottom: 10px; font-size: 12px; color: #555;">
                            <i class="fas fa-info-circle" style="color: #3498db;"></i>
                            No Lucro Presumido, os impostos são calculados individualmente (ISS, IRRF, PIS, COFINS, CSLL).
                        </div>
                        <?php endif; ?>

                        <!-- Retenções do Tomador -->
                        <div style="padding: 8px 12px; background: #fff; border: 1px solid #e0e0e0; border-radius: 4px;">
                            <div style="margin-bottom: 6px;">
                                <strong style="font-size: 13px;"><i class="fas fa-hand-holding-usd" style="color: #e67e22;"></i> Retenções do Tomador</strong>
                                <span style="font-size: 11px; color: #888; margin-left: 5px;">(impostos retidos na fonte pelo cliente)</span>
                            </div>
                            <div class="row-fluid">
                                <div class="span4">
                                    <label class="checkbox" style="font-size: 12px; margin-bottom: 5px;">
                                        <input type="checkbox" id="retem-iss" name="retem_iss" value="1" style="margin-right: 3px;">
                                        ISS (<span id="aliquota-iss-display"><?= $tributacao['aliquota_iss'] ?? '5.00' ?>%</span>)
                                        <span id="retem-iss-valor" style="color: #e67e22; font-weight: bold; margin-left: 5px;"></span>
                                    </label>
                                </div>
                                <div class="span4">
                                    <label class="checkbox" style="font-size: 12px; margin-bottom: 5px;">
                                        <input type="checkbox" id="retem-irrf" name="retem_irrf" value="1" style="margin-right: 3px;">
                                        IRRF (1,5%)
                                        <span id="retem-irrf-valor" style="color: #e67e22; font-weight: bold; margin-left: 5px;"></span>
                                    </label>
                                </div>
                                <div class="span4">
                                    <label class="checkbox" style="font-size: 12px; margin-bottom: 5px;">
                                        <input type="checkbox" id="retem-pis" name="retem_pis" value="1" style="margin-right: 3px;">
                                        PIS (0,65%)
                                        <span id="retem-pis-valor" style="color: #e67e22; font-weight: bold; margin-left: 5px;"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span4">
                                    <label class="checkbox" style="font-size: 12px; margin-bottom: 5px;">
                                        <input type="checkbox" id="retem-cofins" name="retem_cofins" value="1" style="margin-right: 3px;">
                                        COFINS (3,0%)
                                        <span id="retem-cofins-valor" style="color: #e67e22; font-weight: bold; margin-left: 5px;"></span>
                                    </label>
                                </div>
                                <div class="span4">
                                    <label class="checkbox" style="font-size: 12px; margin-bottom: 5px;">
                                        <input type="checkbox" id="retem-csll" name="retem_csll" value="1" style="margin-right: 3px;">
                                        CSLL (1,0%)
                                        <span id="retem-csll-valor" style="color: #e67e22; font-weight: bold; margin-left: 5px;"></span>
                                    </label>
                                </div>
                                <div class="span4">
                                    <strong style="font-size: 13px;">Total Retido:</strong>
                                    <span id="retem-total-valor" style="color: #e67e22; font-weight: bold; font-size: 14px;">R$ 0,00</span>
                                </div>
                            </div>
                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #888;">
                                <i class="fas fa-exclamation-circle" style="color: #e67e22;"></i>
                                As retenções NÃO reduzem o valor da NFS-e. Elas são registradas para controle e serão deduzidas no DRE como crédito a compensar.
                            </p>
                        </div>
                    </div>

                    <!-- Campos de Emissão -->
                    <div class="row-fluid" style="margin-top: 15px;">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label"><strong>Valor dos Serviços:</strong></label>
                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <span class="add-on">R$</span>
                                        <input type="text" name="valor_servicos" id="valor-servicos-wizard"
                                               class="span8" value="<?= number_format($valorServicosNFSe, 2, ',', '.') ?>"
                                               placeholder="0,00">
                                    </div>
                                    <span class="help-block" id="valor-servicos-help">Valor dos serviços prestados (produtos não inclusos)</span>
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label"><strong>Deduções:</strong></label>
                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <span class="add-on">R$</span>
                                        <input type="text" name="valor_deducoes" id="valor-deducoes-wizard"
                                               class="span8" value="0,00"
                                               placeholder="0,00">
                                    </div>
                                    <span class="help-block">Deduções legais (materiais, insumos, etc.)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><strong>Descrição do Serviço:</strong></label>
                        <div class="controls">
                            <textarea name="descricao_servico" id="descricao-servico-wizard" class="span12" rows="3"><?= htmlspecialchars($tributacao['descricao_servico']) ?></textarea>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Código Tributação LC 116:</label>
                                <div class="controls">
                                    <input type="text" class="span12" value="<?= $tributacao['codigo_tributacao_nacional'] ?>" readonly style="background: #f5f5f5;">
                                </div>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <label class="control-label">Código Municipal:</label>
                                <div class="controls">
                                    <input type="text" class="span12" value="<?= $tributacao['codigo_tributacao_municipal'] ?>" readonly style="background: #f5f5f5;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 2: Impostos & Pré-visualização -->
                <div class="wizard-step-panel" id="wizard-step-2">
                    <div class="row-fluid">
                        <!-- Tabela de Impostos -->
                        <div class="span5">
                            <h6><i class="fas fa-calculator"></i> Cálculo de Impostos
                                <span class="label label-<?= $isSimplesNacional ? 'success' : 'info' ?>" style="font-size: 11px; margin-left: 5px;"><?= $regimeLabel ?></span>
                            </h6>
                            <table class="impostos-table" id="impostos-table">
                                <tbody>
                                    <tr>
                                        <td class="imposto-nome">Valor Bruto</td>
                                        <td class="imposto-valor" id="imp-valor-bruto">-</td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome">(-) Deduções</td>
                                        <td class="imposto-valor" id="imp-deducoes">-</td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome">Base de Cálculo</td>
                                        <td class="imposto-valor" id="imp-base-calculo">-</td>
                                    </tr>
                                    <?php if ($isSimplesNacional): ?>
                                    <!-- Simples Nacional: DAS -->
                                    <tr style="background: #e8f5e9;">
                                        <td class="imposto-nome"><strong><i class="fas fa-receipt" style="color: #27ae60;"></i> DAS (Simples Nacional)</strong></td>
                                        <td class="imposto-valor" id="imp-das"><strong>-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome" style="font-size: 11px; color: #888; padding-left: 20px;">Alíquota efetiva: <span id="imp-das-aliquota">-</span></td>
                                        <td></td>
                                    </tr>
                                    <?php else: ?>
                                    <!-- Lucro Presumido: impostos individuais -->
                                    <tr>
                                        <td class="imposto-nome">ISS (<?= $tributacao['aliquota_iss'] ?>%)</td>
                                        <td class="imposto-valor" id="imp-iss">-</td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome">PIS</td>
                                        <td class="imposto-valor" id="imp-pis">-</td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome">COFINS</td>
                                        <td class="imposto-valor" id="imp-cofins">-</td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome">IRRF</td>
                                        <td class="imposto-valor" id="imp-irrf">-</td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome">CSLL</td>
                                        <td class="imposto-valor" id="imp-csll">-</td>
                                    </tr>
                                    <tr>
                                        <td class="imposto-nome">INSS/CPP</td>
                                        <td class="imposto-valor" id="imp-inss">-</td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr class="total-row">
                                        <td class="imposto-nome">Total Impostos</td>
                                        <td class="imposto-valor" id="imp-total-impostos">-</td>
                                    </tr>
                                    <!-- Retenções do Tomador -->
                                    <tr id="retencao-row" style="display: none;">
                                        <td class="imposto-nome" style="color: #e67e22;"><i class="fas fa-hand-holding-usd"></i> (-) Retenções Tomador</td>
                                        <td class="imposto-valor" id="imp-retencao-total" style="color: #e67e22;">-</td>
                                    </tr>
                                    <tr class="liquido-row">
                                        <td class="imposto-nome">Valor Líquido</td>
                                        <td class="imposto-valor" id="imp-valor-liquido">-</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="font-size: 11px; color: #888; margin-top: 5px;" id="imposto-regime-note">
                                <?php if ($isSimplesNacional): ?>
                                <i class="fas fa-info-circle" style="color: #27ae60;"></i> Simples Nacional: imposto recolhido via DAS mensal. O valor líquido NÃO é reduzido pelas retenções do tomador.
                                <?php else: ?>
                                <i class="fas fa-info-circle" style="color: #3498db;"></i> Lucro Presumido: impostos calculados individualmente. O valor líquido NÃO é reduzido pelas retenções do tomador.
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- Pré-visualização do Documento NFS-e (PDF) -->
                        <div class="span7">
                            <div style="text-align: center; padding: 35px 20px; background: #f9f9f9; border: 1px dashed #ccc; border-radius: 5px;">
                                <i class="fas fa-file-pdf" style="font-size: 48px; color: #d9534f; display: block; margin-bottom: 15px;"></i>
                                <h5>Pré-visualização do Documento NFS-e</h5>
                                <p style="color: #666; margin: 10px 0;">Clique abaixo para gerar um PDF com a pré-visualização completa do documento,<br>incluindo logo da empresa e QR Code PIX para pagamento.</p>
                                <button type="button" class="btn btn-primary btn-large" id="btn-preview-nfse">
                                    <i class="fas fa-eye"></i> Pré-visualizar NFS-e (PDF)
                                </button>
                                <p style="color: #999; font-size: 11px; margin-top: 10px;">O PDF será aberto em uma nova aba do navegador.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 3: Boleto -->
                <div class="wizard-step-panel" id="wizard-step-3">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label class="checkbox" style="font-size: 14px; margin-bottom: 15px;">
                                    <input type="checkbox" id="gerar-boleto-wizard" value="1" checked>
                                    <strong>Gerar boleto de cobrança junto com a NFS-e</strong>
                                </label>
                            </div>

                            <div id="boleto-campos">
                                <div class="alert alert-info" style="margin-bottom: 15px;">
                                    <i class="fas fa-info-circle"></i> O boleto será gerado com o <strong>valor líquido</strong> da NFS-e (impostos já descontados).
                                </div>

                                <div class="row-fluid">
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Valor do Boleto:</strong></label>
                                            <div class="controls">
                                                <div class="input-prepend input-append">
                                                    <span class="add-on">R$</span>
                                                    <input type="text" id="valor-boleto-wizard" class="span8" readonly style="background: #f5f5f5; font-weight: bold; color: #2e7d32;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Data de Vencimento:</strong></label>
                                            <div class="controls">
                                                <input type="date" name="data_vencimento" id="data-vencimento-wizard"
                                                       class="span12" value="<?= date('Y-m-d', strtotime('+5 days')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label"><strong>Instruções:</strong></label>
                                            <div class="controls">
                                                <textarea name="instrucoes" id="instrucoes-boleto-wizard"
                                                          class="span12" rows="2">Pagável em qualquer banco até o vencimento.</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="sem-boleto-msg" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Nenhum boleto será gerado. Você poderá gerar um boleto separadamente após a emissão da NFS-e.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSO 4: Confirmação -->
                <div class="wizard-step-panel" id="wizard-step-4">
                    <div class="alert alert-info" style="margin-bottom: 20px;">
                        <i class="fas fa-clipboard-check"></i> <strong>Resumo da Emissão</strong> — Verifique os dados abaixo antes de confirmar.
                    </div>

                    <div class="row-fluid">
                        <div class="span6">
                            <div class="wizard-summary-section">
                                <h6><i class="fas fa-file-invoice"></i> NFS-e
                                    <span class="label label-<?= $isSimplesNacional ? 'success' : 'info' ?>" style="font-size: 11px; margin-left: 5px;"><?= $regimeLabel ?></span>
                                </h6>
                                <p><strong>Prestador:</strong> <span id="res-prestador"><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '—') ?></span></p>
                                <p><strong>Tomador:</strong> <span id="res-tomador"><?= htmlspecialchars($result->nomeCliente ?? '—') ?></span></p>
                                <p><strong>Valor dos Serviços:</strong> <span id="res-valor-servicos">—</span></p>
                                <p><strong>Deduções:</strong> <span id="res-deducoes">—</span></p>
                                <p id="res-imposto-linha"><strong>Total Impostos:</strong> <span id="res-total-impostos">—</span></p>
                                <p id="res-das-linha" style="display: <?= $isSimplesNacional ? 'block' : 'none' ?>;"><strong style="color: #27ae60;"><i class="fas fa-receipt"></i> DAS (Simples Nacional):</strong> <span id="res-valor-das" style="color: #27ae60; font-weight: bold;">—</span></p>
                                <p id="res-retencao-linha" style="display: none;"><strong style="color: #e67e22;"><i class="fas fa-hand-holding-usd"></i> Retenções Tomador:</strong> <span id="res-retencao-total" style="color: #e67e22; font-weight: bold;">—</span></p>
                                <p><strong style="color: #2e7d32; font-size: 14px;">Valor Líquido:</strong> <span id="res-valor-liquido" style="color: #2e7d32; font-size: 14px; font-weight: bold;">—</span></p>
                                <p style="font-size: 11px; color: #888;" id="res-liquido-note">Valor líquido = Serviços - Impostos. Retenções NÃO reduzem este valor.</p>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="wizard-summary-section boleto-section" id="res-boleto-section">
                                <h6><i class="fas fa-barcode"></i> Boleto</h6>
                                <p id="res-boleto-info">—</p>
                            </div>

                            <div class="wizard-summary-section" style="border-left-color: #d9534f;">
                                <h6><i class="fas fa-exclamation-triangle"></i> Atenção</h6>
                                <p>Após confirmar, a NFS-e será emitida junto com o boleto (se selecionado).</p>
                                <p>Esta ação não pode ser desfeita facilmente.</p>
                                <?php if ($ambiente == 'homologacao'): ?>
                                <p style="color: #856404; font-weight: bold;"><i class="fas fa-flask"></i> Modo Homologação: a NFS-e será de teste, sem valor fiscal.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegação do Wizard -->
                <div class="wizard-nav">
                    <button type="button" class="btn" id="btn-wizard-anterior" disabled>
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-wizard-proximo">
                        Próximo <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-success" id="btn-wizard-emitir" style="display: none;">
                        <i class="fas fa-check-circle"></i> Confirmar & Emitir NFS-e
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <!-- Histórico de NFS-e -->
    <?php if ($nfse_atual && !empty($historico_nfse) && count($historico_nfse) > 1): ?>
    <div class="span12" style="margin-top: 10px;">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Histórico de NFS-e</h5>
            </div>
            <div class="widget-content">
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
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>