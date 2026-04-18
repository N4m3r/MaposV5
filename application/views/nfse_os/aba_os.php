<?php
/**
 * Aba de NFSe e Boleto na Visualização da OS
 * Wizard de 4 passos para emissão com pré-visualização
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

// Valor total da OS (serviços + produtos)
$valorTotalOS = floatval($totalServico) + floatval($totalProdutos);

// Desconto do tomador (cliente) se houver
$descontoTomador = floatval($result->valor_desconto ?? 0);

// Valor final para NFS-e (desconto do tomador já aplicado se existir)
$valorServicosNFSe = $descontoTomador > 0 ? $descontoTomador : $valorTotalOS;

// Formatação para exibição
function formatarMoeda($valor) {
    return 'R$ ' . number_format(floatval($valor), 2, ',', '.');
}

function formatarDocumento($doc) {
    $doc = preg_replace('/\D/', '', $doc);
    if (strlen($doc) == 14) {
        return substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 12, 2);
    }
    if (strlen($doc) == 11) {
        return substr($doc, 0, 3) . '.' . substr($doc, 3, 3) . '.' . substr($doc, 6, 3) . '-' . substr($doc, 9, 2);
    }
    return $doc;
}
?>

<!-- Aba de Documentos Fiscais -->
<div class="tab-pane" id="tab-documentos-fiscais">
    <div class="row-fluid" style="margin-top: 20px;">

        <?php if ($nfse_atual): ?>
        <!-- ============================================ -->
        <!-- NFS-e JÁ EMITIDA - Card de status (mantido)  -->
        <!-- ============================================ -->
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fas fa-file-invoice"></i></span>
                    <h5>NFS-e - Nota Fiscal de Serviço</h5>
                </div>

                <div class="widget-content">
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
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- ============================================ -->
        <!-- WIZARD DE EMISSÃO NFS-e                      -->
        <!-- ============================================ -->
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
                                        <p>CNPJ: <?= formatarDocumento($emitente->cnpj ?? '') ?></p>
                                        <p><?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->estado ?? ''))) ?></p>
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
                                    <p><?= !empty($result->cnpj) ? 'CNPJ: ' . formatarDocumento($result->cnpj) : (!empty($result->cpf_cgc) ? 'CPF/CNPJ: ' . formatarDocumento($result->cpf_cgc) : 'Documento não informado') ?></p>
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
                                    <td class="text-right"><?= formatarMoeda($s->preco ?: $s->precoVenda) ?></td>
                                    <td class="text-right"><?= formatarMoeda(($s->preco ?: $s->precoVenda) * ($s->quantidade ?: 1)) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php foreach ($produtos as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p->descricao ?? $p->nomeProduto ?? 'Produto') ?></td>
                                    <td><?= $p->quantidade ?? 1 ?></td>
                                    <td class="text-right"><?= formatarMoeda($p->preco ?? $p->subTotal / max(1, $p->quantidade)) ?></td>
                                    <td class="text-right"><?= formatarMoeda($p->subTotal) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total Serviços:</strong></td>
                                    <td class="text-right"><strong><?= formatarMoeda($totalServico) ?></strong></td>
                                </tr>
                                <?php if ($totalProdutos > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total Produtos:</strong></td>
                                    <td class="text-right"><strong><?= formatarMoeda($totalProdutos) ?></strong></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total OS:</strong></td>
                                    <td class="text-right"><strong style="color: #2e7d32; font-size: 14px;"><?= formatarMoeda($valorTotalOS) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php endif; ?>

                        <!-- Desconto do Tomador -->
                        <?php if ($descontoTomador > 0): ?>
                        <div class="discount-badge">
                            <i class="fas fa-tags"></i>
                            <strong>Desconto do Tomador:</strong> <?= formatarMoeda($descontoTomador) ?>
                            <small>(valor já negociado com o cliente)</small>
                        </div>
                        <?php endif; ?>

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
                                        <span class="help-block">Valor total dos serviços prestados</span>
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
                                <h6><i class="fas fa-calculator"></i> Cálculo de Impostos</h6>
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
                                        <tr class="total-row">
                                            <td class="imposto-nome">Total Impostos</td>
                                            <td class="imposto-valor" id="imp-total-impostos">-</td>
                                        </tr>
                                        <tr class="liquido-row">
                                            <td class="imposto-nome">Valor Líquido</td>
                                            <td class="imposto-valor" id="imp-valor-liquido">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pré-visualização do Documento NFS-e -->
                            <div class="span7">
                                <h6><i class="fas fa-eye"></i> Pré-visualização do Documento</h6>
                                <div class="nfse-preview-document" id="nfse-preview">
                                    <div class="nfse-preview-watermark">PRÉ-VISUALIZAÇÃO</div>

                                    <div class="nfse-preview-header">
                                        <h5>NOTA FISCAL DE SERVIÇOS ELETRÔNICA</h5>
                                        <small>Documento gerado automaticamente — Pré-visualização</small>
                                    </div>

                                    <div class="nfse-preview-section">
                                        <div class="nfse-preview-section-title">PRESTADOR DE SERVIÇO</div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">Nome/Razão Social:</span>
                                            <span class="value-col" id="prev-prestador-nome"><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '—') ?></span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">CNPJ:</span>
                                            <span class="value-col" id="prev-prestador-cnpj"><?= formatarDocumento($emitente->cnpj ?? '') ?></span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">Endereço:</span>
                                            <span class="value-col" id="prev-prestador-end"><?= htmlspecialchars(trim(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' - ' . ($emitente->bairro ?? '') . ', ' . ($emitente->cidade ?? '') . '/' . ($emitente->estado ?? ''))) ?></span>
                                        </div>
                                    </div>

                                    <div class="nfse-preview-section">
                                        <div class="nfse-preview-section-title">TOMADOR DE SERVIÇO</div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">Nome/Razão Social:</span>
                                            <span class="value-col" id="prev-tomador-nome"><?= htmlspecialchars($result->nomeCliente ?? '—') ?></span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">CPF/CNPJ:</span>
                                            <span class="value-col" id="prev-tomador-doc"><?= !empty($result->cnpj) ? formatarDocumento($result->cnpj) : (!empty($result->cpf_cgc) ? formatarDocumento($result->cpf_cgc) : '—') ?></span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">Endereço:</span>
                                            <span class="value-col" id="prev-tomador-end"><?= htmlspecialchars(trim(($result->rua ?? '') . ', ' . ($result->numero ?? '') . ' - ' . ($result->bairro ?? '') . ', ' . ($result->cidade ?? '') . '/' . ($result->estado ?? ''))) ?></span>
                                        </div>
                                    </div>

                                    <div class="nfse-preview-section">
                                        <div class="nfse-preview-section-title">DISCRIMINAÇÃO</div>
                                        <p id="prev-descricao" style="font-size: 11px; color: #333; margin: 0;"><?= htmlspecialchars($tributacao['descricao_servico']) ?></p>
                                        <p style="font-size: 10px; color: #888; margin: 4px 0 0 0;">Código Tributação: <?= $tributacao['codigo_tributacao_nacional'] ?> / Municipal: <?= $tributacao['codigo_tributacao_municipal'] ?></p>
                                    </div>

                                    <div class="nfse-preview-values">
                                        <div class="nfse-preview-row">
                                            <span class="label-col">Valor dos Serviços:</span>
                                            <span class="value-col" id="prev-valor-servicos">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">(-) Deduções:</span>
                                            <span class="value-col" id="prev-deducoes">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">Base de Cálculo:</span>
                                            <span class="value-col" id="prev-base-calculo">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">(-) ISS:</span>
                                            <span class="value-col" id="prev-iss">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">(-) PIS:</span>
                                            <span class="value-col" id="prev-pis">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">(-) COFINS:</span>
                                            <span class="value-col" id="prev-cofins">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">(-) IRRF:</span>
                                            <span class="value-col" id="prev-irrf">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">(-) CSLL:</span>
                                            <span class="value-col" id="prev-csll">—</span>
                                        </div>
                                        <div class="nfse-preview-row">
                                            <span class="label-col">(-) INSS:</span>
                                            <span class="value-col" id="prev-inss">—</span>
                                        </div>
                                        <div class="nfse-preview-total-row">
                                            <span class="label-col">Valor Líquido:</span>
                                            <span class="value-col" id="prev-valor-liquido">—</span>
                                        </div>
                                    </div>
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
                                    <h6><i class="fas fa-file-invoice"></i> NFS-e</h6>
                                    <p><strong>Prestador:</strong> <span id="res-prestador"><?= htmlspecialchars($emitente->nome ?? $emitente->razaosocial ?? '—') ?></span></p>
                                    <p><strong>Tomador:</strong> <span id="res-tomador"><?= htmlspecialchars($result->nomeCliente ?? '—') ?></span></p>
                                    <p><strong>Valor dos Serviços:</strong> <span id="res-valor-servicos">—</span></p>
                                    <p><strong>Deduções:</strong> <span id="res-deducoes">—</span></p>
                                    <p><strong>Total Impostos:</strong> <span id="res-total-impostos">—</span></p>
                                    <p><strong style="color: #2e7d32; font-size: 14px;">Valor Líquido:</strong> <span id="res-valor-liquido" style="color: #2e7d32; font-size: 14px; font-weight: bold;">—</span></p>
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

        <!-- ============================================ -->
        <!-- Card do Boleto (separado, quando NFSe já existe) -->
        <!-- ============================================ -->
        <?php if ($nfse_atual): ?>
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
                            Gere um boleto vinculado a esta NFS-e.
                        </div>

                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBoletoOS') && $nfse_atual): ?>
                            <form method="post" action="<?= site_url('nfse_os/gerar_boleto/' . $result->idOs . '/' . $nfse_atual->id) ?>">
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
        <?php endif; ?>

        <!-- Histórico de NFS-e (aparece para ambos os casos) -->
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
</div>

<script>
// =============================================
// Wizard NFS-e + Boleto
// =============================================

var wizardData = {
    currentStep: 1,
    totalSteps: 4,
    impostosResult: null,
    valorServicos: 0,
    valorDeducoes: 0,
    gerarBoleto: true
};

// Máscara monetária para campos de valor
function aplicarMascaraMoeda(input) {
    $(input).on('input', function() {
        var val = this.value.replace(/[^\d]/g, '');
        if (val.length === 0) { this.value = ''; return; }
        val = val.replace(/^0+/, '') || '0';
        while (val.length < 3) val = '0' + val;
        var inteiro = val.substring(0, val.length - 2);
        var decimal = val.substring(val.length - 2);
        this.value = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',' + decimal;
    });
}

function parseMoeda(str) {
    if (!str) return 0;
    return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
}

function formatarMoeda(valor) {
    return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Navegação do wizard
function wizardGoToStep(step) {
    if (step < 1 || step > wizardData.totalSteps) return;

    // Validação ao avançar
    if (step > wizardData.currentStep) {
        if (!validarStep(wizardData.currentStep)) return;
    }

    // Atualiza indicadores
    var steps = $('#wizard-steps li');
    steps.each(function() {
        var s = parseInt($(this).data('step'));
        $(this).removeClass('active completed');
        if (s < step) $(this).addClass('completed');
        if (s === step) $(this).addClass('active');
    });

    // Troca painel
    $('.wizard-step-panel').removeClass('active');
    $('#wizard-step-' + step).addClass('active');

    wizardData.currentStep = step;

    // Atualiza botões
    $('#btn-wizard-anterior').prop('disabled', step === 1);
    if (step === wizardData.totalSteps) {
        $('#btn-wizard-proximo').hide();
        $('#btn-wizard-emitir').show();
        atualizarResumo();
    } else {
        $('#btn-wizard-proximo').show();
        $('#btn-wizard-emitir').hide();
    }

    // Ao entrar no passo 2, calcular impostos
    if (step === 2) {
        calcularImpostosWizard();
    }

    // Ao entrar no passo 3, atualizar valor do boleto
    if (step === 3) {
        atualizarValorBoleto();
    }
}

function validarStep(step) {
    if (step === 1) {
        var valor = parseMoeda($('#valor-servicos-wizard').val());
        if (valor <= 0) {
            alert('Informe o valor dos serviços para continuar.');
            $('#valor-servicos-wizard').focus();
            return false;
        }
        var desc = $('#descricao-servico-wizard').val().trim();
        if (!desc) {
            alert('Informe a descrição do serviço.');
            $('#descricao-servico-wizard').focus();
            return false;
        }
        wizardData.valorServicos = valor;
        wizardData.valorDeducoes = parseMoeda($('#valor-deducoes-wizard').val());
        return true;
    }
    if (step === 2) {
        if (!wizardData.impostosResult) {
            alert('Aguarde o cálculo dos impostos.');
            return false;
        }
        return true;
    }
    if (step === 3) {
        wizardData.gerarBoleto = $('#gerar-boleto-wizard').is(':checked');
        if (wizardData.gerarBoleto) {
            var venc = $('#data-vencimento-wizard').val();
            if (!venc) {
                alert('Informe a data de vencimento do boleto.');
                return false;
            }
        }
        return true;
    }
    return true;
}

// Cálculo de impostos via AJAX (com debounce)
var calcularTimeout = null;

function calcularImpostosWizard() {
    var valor = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
    if (valor <= 0) return;

    // Mostra loading
    $('#impostos-table td.imposto-valor').text('...');
    $('.nfse-preview-values .value-col').text('...');

    clearTimeout(calcularTimeout);
    calcularTimeout = setTimeout(function() {
        $.ajax({
            url: '<?= site_url("nfse_os/calcular_impostos") ?>',
            type: 'POST',
            data: { valor: valor },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    wizardData.impostosResult = data;
                    var imp = data.impostos;
                    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());
                    var baseCalc = data.valor_bruto - deducoes;

                    // Atualiza tabela de impostos
                    $('#imp-valor-bruto').text(formatarMoeda(data.valor_bruto));
                    $('#imp-deducoes').text(formatarMoeda(deducoes));
                    $('#imp-base-calculo').text(formatarMoeda(baseCalc));
                    $('#imp-iss').text(formatarMoeda(imp.iss || 0));
                    $('#imp-pis').text(formatarMoeda(imp.pis || 0));
                    $('#imp-cofins').text(formatarMoeda(imp.cofins || 0));
                    $('#imp-irrf').text(formatarMoeda(imp.irrf || 0));
                    $('#imp-csll').text(formatarMoeda(imp.csll || 0));
                    $('#imp-inss').text(formatarMoeda(imp.inss || 0));
                    $('#imp-total-impostos').text(formatarMoeda(imp.valor_total_impostos || 0));
                    $('#imp-valor-liquido').text(formatarMoeda(data.valor_liquido - deducoes));

                    // Atualiza preview do documento
                    var liquido = data.valor_liquido - deducoes;
                    $('#prev-valor-servicos').text(formatarMoeda(data.valor_bruto));
                    $('#prev-deducoes').text(formatarMoeda(deducoes));
                    $('#prev-base-calculo').text(formatarMoeda(baseCalc));
                    $('#prev-iss').text(formatarMoeda(imp.iss || 0));
                    $('#prev-pis').text(formatarMoeda(imp.pis || 0));
                    $('#prev-cofins').text(formatarMoeda(imp.cofins || 0));
                    $('#prev-irrf').text(formatarMoeda(imp.irrf || 0));
                    $('#prev-csll').text(formatarMoeda(imp.csll || 0));
                    $('#prev-inss').text(formatarMoeda(imp.inss || 0));
                    $('#prev-valor-liquido').text(formatarMoeda(liquido));

                    // Atualiza descrição no preview
                    var descServ = $('#descricao-servico-wizard').val();
                    if (descServ) {
                        $('#prev-descricao').text(descServ);
                    }
                }
            },
            error: function() {
                $('#impostos-table td.imposto-valor').text('Erro');
            }
        });
    }, 300);
}

// Atualizar valor do boleto no passo 3
function atualizarValorBoleto() {
    if (wizardData.impostosResult) {
        var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());
        var liquido = wizardData.impostosResult.valor_liquido - deducoes;
        $('#valor-boleto-wizard').val(liquido.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }
}

// Atualizar resumo no passo 4
function atualizarResumo() {
    var valor = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());

    $('#res-valor-servicos').text(formatarMoeda(valor));
    $('#res-deducoes').text(formatarMoeda(deducoes));

    if (wizardData.impostosResult) {
        var imp = wizardData.impostosResult.impostos;
        var liquido = wizardData.impostosResult.valor_liquido - deducoes;
        $('#res-total-impostos').text(formatarMoeda(imp.valor_total_impostos || 0));
        $('#res-valor-liquido').text(formatarMoeda(liquido));
    }

    // Resumo do boleto
    if (wizardData.gerarBoleto) {
        var venc = $('#data-vencimento-wizard').val();
        var vencFormatado = venc ? venc.split('-').reverse().join('/') : '—';
        var liquidoBoleto = wizardData.impostosResult ? (wizardData.impostosResult.valor_liquido - deducoes) : 0;
        $('#res-boleto-info').html(
            '<strong>Gerar boleto:</strong> Sim<br>' +
            '<strong>Valor:</strong> ' + formatarMoeda(liquidoBoleto) + '<br>' +
            '<strong>Vencimento:</strong> ' + vencFormatado
        );
    } else {
        $('#res-boleto-info').html('<strong>Gerar boleto:</strong> Não');
    }
}

// Emitir NFS-e via AJAX
function emitirNFSeWizard() {
    var valor = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());
    var descricao = $('#descricao-servico-wizard').val();
    var gerarBoleto = wizardData.gerarBoleto ? 1 : 0;

    // Confirmação final
    var msg = 'Deseja confirmar a emissão da NFS-e?\n\n';
    msg += 'Valor dos Serviços: ' + formatarMoeda(valor) + '\n';
    if (deducoes > 0) msg += 'Deduções: ' + formatarMoeda(deducoes) + '\n';
    if (wizardData.impostosResult) {
        var imp = wizardData.impostosResult.impostos;
        var liquido = wizardData.impostosResult.valor_liquido - deducoes;
        msg += 'Total Impostos: ' + formatarMoeda(imp.valor_total_impostos || 0) + '\n';
        msg += 'Valor Líquido: ' + formatarMoeda(liquido) + '\n';
    }
    if (gerarBoleto) {
        msg += '\nBoleto será gerado automaticamente.';
    }

    if (!confirm(msg)) return;

    // Desabilita botão
    var btnEmitir = $('#btn-wizard-emitir');
    btnEmitir.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Emitindo...');

    // Emitir NFS-e
    $.ajax({
        url: '<?= site_url("nfse_os/emitir/" . $result->idOs) ?>',
        type: 'POST',
        data: {
            valor_servicos: valor,
            valor_deducoes: deducoes,
            descricao_servico: descricao,
            gerar_boleto: 0
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var nfseId = response.nfse_id || response.id;

                if (gerarBoleto && nfseId) {
                    // Gerar boleto encadeado
                    btnEmitir.html('<i class="fas fa-spinner fa-spin"></i> Gerando boleto...');
                    $.ajax({
                        url: '<?= site_url("nfse_os/gerar_boleto/" . $result->idOs) ?>/' + nfseId,
                        type: 'POST',
                        data: {
                            data_vencimento: $('#data-vencimento-wizard').val(),
                            instrucoes: $('#instrucoes-boleto-wizard').val()
                        },
                        dataType: 'json',
                        success: function(respBoleto) {
                            alert('NFS-e e Boleto emitidos com sucesso!');
                            location.reload();
                        },
                        error: function() {
                            alert('NFS-e emitida com sucesso! Erro ao gerar boleto. Tente gerar separadamente.');
                            location.reload();
                        }
                    });
                } else {
                    alert('NFS-e emitida com sucesso!');
                    location.reload();
                }
            } else {
                alert('Erro ao emitir NFS-e: ' + (response.message || response.error || 'Erro desconhecido'));
                btnEmitir.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Confirmar & Emitir NFS-e');
            }
        },
        error: function(xhr) {
            var msg = 'Erro na comunicação com o servidor.';
            try {
                var resp = JSON.parse(xhr.responseText);
                msg = resp.message || resp.error || msg;
            } catch(e) {}
            alert(msg);
            btnEmitir.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Confirmar & Emitir NFS-e');
        }
    });
}

// Toggle boleto no passo 3
function toggleBoleto() {
    var checked = $('#gerar-boleto-wizard').is(':checked');
    wizardData.gerarBoleto = checked;
    if (checked) {
        $('#boleto-campos').show();
        $('#sem-boleto-msg').hide();
    } else {
        $('#boleto-campos').hide();
        $('#sem-boleto-msg').show();
    }
}

// =============================================
// Funções existentes (mantidas)
// =============================================

function cancelarNFSe(nfseId) {
    var motivo = prompt('Informe o motivo do cancelamento:');
    if (motivo) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/cancelar_nfse/") ?>' + nfseId;
        var input = document.createElement('input');
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
    var data = prompt('Data do pagamento (YYYY-MM-DD):', '<?= date("Y-m-d") ?>');
    if (data) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/registrar_pagamento/") ?>' + boletoId;
        var inputData = document.createElement('input');
        inputData.type = 'hidden';
        inputData.name = 'data_pagamento';
        inputData.value = data;
        form.appendChild(inputData);
        document.body.appendChild(form);
        form.submit();
    }
}

function copiarLinhaDigitavel() {
    var input = document.getElementById('linha-digitavel');
    if (input) {
        input.select();
        document.execCommand('copy');
        alert('Linha digitável copiada!');
    }
}

// =============================================
// Inicialização
// =============================================

$(document).ready(function() {
    // Máscara monetária
    aplicarMascaraMoeda('#valor-servicos-wizard');
    aplicarMascaraMoeda('#valor-deducoes-wizard');

    // Recalcular ao mudar valores no passo 1
    $('#valor-servicos-wizard, #valor-deducoes-wizard').on('change', function() {
        wizardData.valorServicos = parseMoeda($('#valor-servicos-wizard').val());
        wizardData.valorDeducoes = parseMoeda($('#valor-deducoes-wizard').val());
        wizardData.impostosResult = null; // Invalida cache
    });

    // Atualizar descrição no preview
    $('#descricao-servico-wizard').on('input', function() {
        $('#prev-descricao').text($(this).val());
    });

    // Navegação
    $('#btn-wizard-proximo').click(function() {
        wizardGoToStep(wizardData.currentStep + 1);
    });

    $('#btn-wizard-anterior').click(function() {
        wizardGoToStep(wizardData.currentStep - 1);
    });

    $('#btn-wizard-emitir').click(function() {
        emitirNFSeWizard();
    });

    // Toggle boleto
    $('#gerar-boleto-wizard').change(function() {
        toggleBoleto();
    });
});
</script>