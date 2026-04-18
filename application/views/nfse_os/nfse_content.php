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
$valorServicosNFSe = $descontoTomador > 0 ? $descontoTomador : $valorTotalOS;

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

                    <!-- Desconto do Tomador -->
                    <?php if ($descontoTomador > 0): ?>
                    <div class="discount-badge">
                        <i class="fas fa-tags"></i>
                        <strong>Desconto do Tomador:</strong> <?= formatarMoedaNFSe($descontoTomador) ?>
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