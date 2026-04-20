<?php
// Inicializar variáveis
$totalServico = 0;
$totalProdutos = 0;
$cobrancas_os = isset($cobrancas_os) ? $cobrancas_os : [];
$notas_fiscais_os = isset($notas_fiscais_os) ? $notas_fiscais_os : [];

// Helper para cores de status
function getStatusColor($status) {
    $colors = [
        'pago' => '#27ae60',
        'Pago' => '#27ae60',
        'PAGO' => '#27ae60',
        'pendente' => '#f39c12',
        'Pendente' => '#f39c12',
        'vencido' => '#e74c3c',
        'Vencido' => '#e74c3c',
        'cancelado' => '#95a5a6',
        'Cancelado' => '#95a5a6',
        'Emitida' => '#27ae60',
        'Cancelada' => '#e74c3c',
        'Processando' => '#3498db'
    ];
    return $colors[$status] ?? '#7f8c8d';
}
?>

<style>
/* Tabs modernas */
.os-tabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 20px;
    display: flex;
    gap: 5px;
}

.os-tab {
    padding: 12px 20px;
    border: none;
    background: transparent;
    color: #6c757d;
    font-weight: 500;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.os-tab:hover {
    color: #495057;
    background: #f8f9fa;
}

.os-tab.active {
    color: #667eea;
    border-bottom-color: #667eea;
    background: #fff;
}

.os-tab .badge-count {
    background: #e9ecef;
    color: #495057;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
}

.os-tab.active .badge-count {
    background: #667eea;
    color: white;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Cards de boletos/notas */
.doc-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #e9ecef;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.doc-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.doc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.doc-type {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.doc-type.boleto {
    background: #fff3cd;
    color: #856404;
}

.doc-type.nota {
    background: #d1ecf1;
    color: #0c5460;
}

.doc-status {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.doc-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 10px 0;
}

.doc-info {
    color: #6c757d;
    font-size: 0.85rem;
}

.doc-actions {
    margin-top: 12px;
    display: flex;
    gap: 8px;
}

.doc-actions .btn {
    flex: 1;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}
</style>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Ordem de Serviço #<?php echo $result->idOs; ?></h5>
                <div class="buttons" style=" padding-left:5px;">
                    <?php if ($result->status == 'Finalizado' || $result->status == 'Finalizada'): ?>
                        <a title="Relatório de Atendimento" class="button btn btn-mini btn-success" href="<?php echo base_url('index.php/mine/relatorioAtendimento/' . $result->idOs); ?>">
                            <span class="button__icon"><i class="bx bx-file"></i></span> <span class="button__text">Relatório</span></a>
                    <?php endif; ?>
                    <?php if ($pode_aprovar && in_array($result->status, ['Orçamento', 'Aberto', 'Negociação'])): ?>
                        <a title="Aprovar" class="button btn btn-mini btn-success" href="<?php echo base_url('index.php/mine/aprovarOs/' . $result->idOs); ?>">
                            <span class="button__icon"><i class="bx bx-check"></i></span> <span class="button__text">Aprovar</span></a>
                    <?php endif; ?>
                    <a target="_blank" title="Imprimir" class="button btn btn-mini btn-inverse" href="<?php echo base_url('index.php/mine/imprimirOs/' . $result->idOs); ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Imprimir</span></a>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="widget-content" style="padding-bottom: 0;">
                <div class="os-tabs">
                    <button class="os-tab active" onclick="showTab('tab-detalhes')" data-tab="tab-detalhes">
                        <i class="bx bx-detail"></i> Detalhes
                    </button>
                    <button class="os-tab" onclick="showTab('tab-boletos')" data-tab="tab-boletos">
                        <i class="bx bx-barcode"></i> Boletos
                        <?php if (!empty($cobrancas_os)): ?>
                            <span class="badge-count"><?php echo count($cobrancas_os); ?></span>
                        <?php endif; ?>
                    </button>

                    <button class="os-tab" onclick="showTab('tab-notas')" data-tab="tab-notas">
                        <i class="bx bx-receipt"></i> Notas Fiscais
                        <?php if (!empty($notas_fiscais_os)): ?>
                            <span class="badge-count"><?php echo count($notas_fiscais_os); ?></span>
                        <?php endif; ?>
                    </button>
                </div>
            </div>

            <!-- Tab Detalhes -->
            <div id="tab-detalhes" class="tab-content active">
            <div class="widget-content" id="printOs">
                <div class="invoice-content">
                    <div class="invoice-head" style="margin-bottom: 0">

                        <table class="table table-condensed">
                            <tbody>
                                <?php if ($emitente == null) { ?>
                                    <tr>
                                        <td colspan="3" class="alert">Os dados do emitente não foram configurados.</td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td style="width: 25%"><img src=" <?php echo $emitente->url_logo; ?> " style="max-height: 100px"></td>
                                        <td>
                                            <span style="font-size: 20px;"><?php echo $emitente->nome; ?></span></br>
                                            <?php if($emitente->cnpj != "00.000.000/0000-00") { ?><span class="icon"><i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php echo $emitente->cnpj; ?></span></br><?php } ?>
                                            <span class="icon"><i class="fas fa-map-marker-alt" style="margin:4px 3px"></i> <?php echo $emitente->rua . ', ' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?></span></br>
                                            <span class="icon"><i class="fas fa-comments" style="margin:5px 1px"></i> E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></span></br>
                                            <span class="icon"><i class="fas fa-user-check"></i> Responsável: <?php echo $result->nome ?>
                                        </td>
                                        <td style="width: 18%; text-align: center">
                                            <span><b>N° OS: </b><?php echo $result->idOs ?></span></br></br>
                                            <span>Emissão: <?php echo date('d/m/Y') ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 0; padding-top: 0">
                        <table class="table table-condensed">
                            <tbody>
                                    <?php if ($result->dataInicial != null) { ?>
                                        <tr>
                                            <td>
                                                <b>STATUS OS: </b><?php echo $result->status ?>
                                            </td>

                                            <td>
                                                <b>DATA INICIAL: </b><?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>
                                            </td>

                                            <td>
                                                <b>DATA FINAL: </b><?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : ''; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty ($result->garantia)) { ?>
                                                    <b>GARANTIA: </b><?php echo $result->garantia . ' dia(s)'; ?>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <b><?php if ($result->status == 'Finalizado') { ?> VENC. DA GARANTIA: </b><?php echo dateInterval($result->dataFinal, $result->garantia); ?><?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>

                        <table class="table table-condensed">
                            <?php if ($result->descricaoProduto != null || $result->defeito != null || $result->laudoTecnico != null || $result->observacoes) { ?>
                                    <?php if ($result->descricaoProduto != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>DESCRIÇÃO: </strong><br>
                                                <?php echo htmlspecialchars_decode($result->descricaoProduto) ?>
                                            </td>
                                        </tr>

                                    <?php } ?>

                                    <?php if ($result->defeito != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>DEFEITO APRESENTADO: </strong><br>
                                                <?php echo htmlspecialchars_decode($result->defeito) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($result->observacoes != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>OBSERVAÇÕES: </strong><br>
                                                <?php echo htmlspecialchars_decode($result->observacoes) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($result->laudoTecnico != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>LAUDO TÉCNICO: </strong><br>
                                                <?php echo htmlspecialchars_decode($result->laudoTecnico) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($result->garantias_id != null) { ?>
                                        <tr>
                                            <td>
                                                <strong>TERMO DE GARANTIA </strong><br>
                                                <?php echo htmlspecialchars_decode($result->textoGarantia) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            <?php } ?>
                        </table>
                        
                        <?php if ($anexos != null) { ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>Anexo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <th>
                                    <?php foreach ($anexos as $a) {
                                        if ($a->thumb == null) {
                                            $thumb = base_url() . 'assets/img/icon-file.png';
                                            $link = base_url() . 'assets/img/icon-file.png';
                                        } else {
                                            $thumb = $a->url . '/thumbs/' . $a->thumb;
                                            $link = $a->url . '/' . $a->anexo;
                                        }
                                        echo '<div class="span3" style="min-height: 150px; margin-left: 0"><a style="min-height: 150px;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal"><img src="' . $thumb . '" alt=""></a></div>';
                                    } ?>
                                    </th>
                                </tbody>
                            </table>
                        <?php } ?>

                        <?php $totalServico = 0; $totalProdutos = 0; ?>
                        <?php if ($produtos != null) { ?>
                            <br />
                            <table class="table table-bordered table-condensed" id="tblProdutos">
                                <thead>
                                    <tr>
                                        <th>PRODUTO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $p) {
                                        $totalProdutos = $totalProdutos + $p->subTotal;
                                        echo '<tr>';
                                        echo '<td>' . $p->descricao . '</td>';
                                        echo '<td>' . $p->quantidade . '</td>';
                                        echo '<td>R$ ' . $p->preco ?: $p->precoVenda . '</td>';
                                        echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>

                        <?php if ($servicos != null) { ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>SERVIÇO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php setlocale(LC_MONETARY, 'en_US'); foreach ($servicos as $s) {
                                        $totalServico = $totalServico + $s->subTotal;
                                        $preco = $s->preco ?: $s->precoVenda;
                                        $subtotal = $preco * ($s->quantidade ?: 1);
                                        echo '<tr>';
                                        echo '<td>' . $s->nome . '</td>';
                                        echo '<td>' . ($s->quantidade ?: 1) . '</td>';
                                        echo '<td>R$ ' . $preco . '</td>';
                                        echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                        <td colspan="3" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalServico, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>

                        <table class="table table-condensed">
                            <thead>
                                <td>
                                    <?php if ($totalProdutos != 0 || $totalServico != 0) {
                                        if ($result->valor_desconto != 0) {
                                            echo "<h4 style='text-align: right'>SUBTOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                            echo $result->valor_desconto != 0 ? "<h4 style='text-align: right'>DESCONTO: R$ " . number_format($result->valor_desconto != 0 ? $result->valor_desconto - ($totalProdutos + $totalServico) : 0.00, 2, ',', '.') . "</h4>" : "";
                                            echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($result->valor_desconto, 2, ',', '.') . "</h4>";
                                        } else { echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>"; }
                                    }?>
                                </td>

                                <?php if ($result->status == 'Finalizado' || $result->status == 'Aprovado') { ?>
                                    <?php if ($qrCode) : ?>
                                        <td style="width: 15%; padding-left: 0; text-align:center;">
                                            <img style="margin:0px" src="<?php echo base_url(); ?>assets/img/logo_pix.png" width="48px" alt="QR Code de Pagamento" /></br>
                                            <img style="margin:6px 0px 0px 0px" width="94px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></br>
                                            <?php echo '<span style="margin:0px;font-size: 80%;text-align:center;">Chave PIX: ' . $chaveFormatada . '</span>';?>
                                        </td>
                                    <?php endif ?>
                                <?php } ?>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>

            <!-- Tab Boletos -->
            <div id="tab-boletos" class="tab-content">
                <div class="widget-content">
                    <?php if (!empty($cobrancas_os)): ?>
                        <div class="row-fluid">
                            <?php foreach ($cobrancas_os as $cobranca): ?>
                                <div class="span6">
                                    <div class="doc-card">
                                        <div class="doc-header">
                                            <span class="doc-type boleto">
                                                <i class="bx bx-barcode"></i> BOLETO
                                            </span>
                                            <span class="doc-status" style="background: <?= getStatusColor($cobranca->status) ?>20; color: <?= getStatusColor($cobranca->status) ?>">
                                                <?= ucfirst($cobranca->status ?? 'Pendente') ?>
                                            </span>
                                        </div>

                                        <div class="doc-value">
                                            R$ <?= number_format($cobranca->total ?? $cobranca->valor ?? 0, 2, ',', '.') ?>
                                        </div>

                                        <div class="doc-info">
                                            <p><strong>Descrição:</strong> <?= htmlspecialchars($cobranca->descricao ?? 'Boleto #' . $cobranca->idCobranca) ?></p>
                                            <p><strong>Vencimento:</strong> <?= isset($cobranca->expire_at) ? date('d/m/Y', strtotime($cobranca->expire_at)) : '-' ?></p>
                                            <?php if (!empty($cobranca->charge_id)): ?>
                                                <p><strong>Código:</strong> <?= $cobranca->charge_id ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="doc-actions">
                                            <?php if (!empty($cobranca->payment_url)): ?>
                                                <a href="<?= $cobranca->payment_url ?>" target="_blank" class="btn btn-success">
                                                    <i class="bx bx-credit-card"></i> Pagar Agora
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($cobranca->pdf)): ?>
                                                <a href="<?= $cobranca->pdf ?>" target="_blank" class="btn btn-info">
                                                    <i class="bx bx-file"></i> Ver Boleto
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="bx bx-barcode"></i>
                            <h4>Nenhum boleto encontrado</h4>
                            <p>Não há boletos vinculados a esta ordem de serviço.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab Notas Fiscais -->
            <div id="tab-notas" class="tab-content">
                <div class="widget-content">
                    <?php if (!empty($notas_fiscais_os)): ?>
                        <div class="row-fluid">
                            <?php foreach ($notas_fiscais_os as $nf): ?>
                                <div class="span6">
                                    <div class="doc-card">
                                        <div class="doc-header">
                                            <span class="doc-type nota">
                                                <i class="bx bx-receipt"></i> NFS-e
                                            </span>
                                            <span class="doc-status" style="background: <?= getStatusColor($nf->status ?? 'Emitida') ?>20; color: <?= getStatusColor($nf->status ?? 'Emitida') ?>">
                                                <?= $nf->status ?? 'Emitida' ?>
                                            </span>
                                        </div>

                                        <div class="doc-value" style="font-size: 1rem;">
                                            <strong>Nº <?= $nf->numero_nfse ?? $nf->numero ?? '-' ?></strong>
                                        </div>

                                        <div class="doc-info">
                                            <p><strong>Data Emissão:</strong> <?= isset($nf->data_emissao) ? date('d/m/Y', strtotime($nf->data_emissao)) : (isset($nf->created_at) ? date('d/m/Y', strtotime($nf->created_at)) : '-') ?></p>
                                            <p><strong>Valor Total:</strong> R$ <?= number_format($nf->valor_total ?? $nf->valor ?? 0, 2, ',', '.') ?></p>
                                            <?php if (!empty($nf->prestador_nome)): ?>
                                                <p><strong>Prestador:</strong> <?= htmlspecialchars($nf->prestador_nome) ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="doc-actions">
                                            <?php if (!empty($nf->link_pdf) || !empty($nf->pdf_url)): ?>
                                                <a href="<?= $nf->link_pdf ?? $nf->pdf_url ?>" target="_blank" class="btn btn-success">
                                                    <i class="bx bx-download"></i> Download PDF
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($nf->caminho_xml)): ?>
                                                <a href="<?= base_url($nf->caminho_xml) ?>" target="_blank" class="btn btn-info">
                                                    <i class="bx bx-file"></i> Download XML
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="bx bx-receipt"></i>
                            <h4>Nenhuma nota fiscal encontrada</h4>
                            <p>Não há notas fiscais vinculadas a esta ordem de serviço.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabId) {
    // Esconde todas as tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Remove active de todos os botões
    document.querySelectorAll('.os-tab').forEach(btn => {
        btn.classList.remove('active');
    });

    // Mostra a tab selecionada
    document.getElementById(tabId).classList.add('active');

    // Adiciona active ao botão
    document.querySelector('[data-tab="' + tabId + '"]').classList.add('active');
}
</script>

<script>
function copyBarcode(barcode) {
    navigator.clipboard.writeText(barcode).then(function() {
        alert('Código de barras copiado!');
    }, function(err) {
        console.error('Erro ao copiar:', err);
    });
}
</script>

<!-- Inicio Modal visualizar anexo -->
<div id="modal-anexo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-anexo" style="text-align: center">
            <div class='progress progress-info progress-striped active'>
                <div class='bar' style='width: 100%'></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id-imagem="" class="btn btn-inverse" id="download">Download</a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/os/excluirAnexo/';
            $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            $("#download").attr('href', "<?php echo base_url(); ?>index.php/os/downloadanexo/" + id);
        });
    });
</script>
<!-- Fim Modal visualizar anexo -->

<script type="text/javascript">
    $(document).ready(function() {
        $("#imprimir").click(function() {
            PrintElem('#printOs');
        })

        function PrintElem(elem) {
            Popup($(elem).html());
        }

        function Popup(data) {
            var mywindow = window.open('', 'mydiv', 'height=600,width=800');
            mywindow.document.open();
            mywindow.document.onreadystatechange = function() {
                if (this.readyState === 'complete') {
                    this.onreadystatechange = function() {};
                    mywindow.focus();
                    mywindow.print();
                    mywindow.close();
                }
            }

            mywindow.document.write('<html><head><title>Map Os</title>');
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/bootstrap.min.css' />");
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css' />");
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/matrix-style.css' />");
            mywindow.document.write("<link rel='stylesheet' href='<?php echo base_url(); ?>assets/css/matrix-media.css' />");

            mywindow.document.write("</head><body >");
            mywindow.document.write(data);
            mywindow.document.write("</body></html>");

            mywindow.document.close(); // necessary for IE >= 10

            return true;
        }
    });
</script>
