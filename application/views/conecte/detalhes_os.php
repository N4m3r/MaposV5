<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>

<style>
    .os-tabs { display: flex; gap: 6px; padding: 10px 0 8px 0; flex-wrap: wrap; }
    .os-tab-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border: 1px solid var(--dark-2, #272835); border-radius: 6px; background: var(--dark-2, #272835); color: var(--dark-cinz, #8788a4); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .os-tab-btn:hover { background: #2d2e3a; color: var(--branco, #caced8); text-decoration: none; }
    .os-tab-btn.active { background: #2d335b; color: #fff; font-weight: bold; border-color: #4a4d7a; }
    .os-tab-btn i { font-size: 15px; }
    .os-tab-content > .os-tab-pane { display: none; }
    .os-tab-content > .os-tab-pane.active { display: block; }
    @media (max-width: 767px) {
        .os-tabs { flex-direction: column; }
        .os-tab-btn { width: 100%; justify-content: center; }
    }
    .ui-datepicker {
        z-index: 9999 !important;
    }

    .trumbowyg-box {
        margin-top: 0;
        margin-bottom: 0;
    }
</style>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Detalhes OS</h5>
                <div class="buttons">
                    <a href="<?php echo base_url('index.php/mine/os'); ?>" class="btn btn-sm btn-inverse">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding os-tab-content">


                <div class="col-12" id="divProdutosServicos" style=" margin-left: 0">
                    <div class="os-tabs">
                        <button class="os-tab-btn active" onclick="showOsTab('tab1', this)" data-tab="tab1"><i class="bx bx-file"></i> Detalhes da OS</button>
                        <button class="os-tab-btn" onclick="showOsTab('tab2', this)" data-tab="tab2"><i class="bx bx-package"></i> Produtos</button>
                        <button class="os-tab-btn" onclick="showOsTab('tab3', this)" data-tab="tab3"><i class="bx bx-wrench"></i> Serviços</button>
                        <button class="os-tab-btn" onclick="showOsTab('tab4', this)" data-tab="tab4"><i class="bx bx-paperclip"></i> Anexos</button>
                    </div>
                    <div class="os-tab-content">
                        <div class="os-tab-pane active" id="tab1">

                            <div class="col-12" id="divCadastrarOs">


                                <div class="col-12" style="padding: 1%; margin-left: 0">


                                    <div class="col-6" style="margin-left: 0">
                                        <h3>#Protocolo:
                                            <?php echo $result->idOs ?>
                                        </h3>
                                        <input id="valorTotal" type="hidden" name="valorTotal" value="" />
                                    </div>
                                    <div class="col-6">
                                        <label for="tecnico">Técnico / Responsável</label>
                                        <input disabled="disabled" id="tecnico" class="col-12" type="text" name="tecnico" value="<?php echo $result->nome ?>" />

                                    </div>
                                </div>
                                <div class="col-12" style="padding: 1%; margin-left: 0">
                                    <div class="col-3">
                                        <label for="status">Status<span class="required"></span></label>
                                        <input disabled="disabled" type="text" name="status" id="status" value="<?php echo $result->status; ?>">

                                    </div>
                                    <div class="col-3">
                                        <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                        <input id="dataInicial" disabled="disabled" class="col-12 datepicker" type="text" name="dataInicial" value="<?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>" />
                                    </div>
                                    <div class="col-3">
                                        <label for="dataFinal">Data Final</label>
                                        <input id="dataFinal" disabled="disabled" class="col-12 datepicker" type="text" name="dataFinal" value="<?php echo date('d/m/Y', strtotime($result->dataFinal)); ?>" />
                                    </div>

                                    <div class="col-3">
                                        <label for="garantia">Garantia</label>
                                        <input id="garantia" disabled="disabled" type="text" class="col-12" name="garantia" value="<?php echo $result->garantia ?>" />
                                    </div>
                                </div>


                                <div class="col-12" style="padding: 1%; margin-left: 0">
                                    <label for="descricaoProduto">Descrição Produto/Serviço</label>
                                    <textarea class="col-12 editor" name="descricaoProduto" id="descricaoProduto" cols="30" rows="5" disabled><?php echo $result->descricaoProduto; ?></textarea>
                                </div>

                                <div class="col-12" style="padding: 1%; margin-left: 0">
                                    <label for="defeito">Defeito</label>
                                    <textarea class="col-12 editor" name="defeito" id="defeito" cols="30" rows="5" disabled><?php echo $result->defeito; ?></textarea>
                                </div>

                                <div class="col-12" style="padding: 1%; margin-left: 0">
                                    <label for="observacoes">Observações</label>
                                    <textarea class="col-12 editor" name="observacoes" id="observacoes" cols="30" rows="5" disabled><?php echo $result->observacoes; ?></textarea>
                                </div>

                                <div class="col-12" style="padding: 1%; margin-left: 0">
                                    <label for="laudoTecnico">Laudo Técnico</label>
                                    <textarea class="col-12 editor" name="laudoTecnico" id="laudoTecnico" cols="30" rows="5" disabled><?php echo $result->laudoTecnico; ?></textarea>
                                </div>

                            </div>

                        </div>


                        <!--Produtos-->
                        <div class="os-tab-pane" id="tab2" style="display:none;">

                            <div class="col-12" id="divProdutos" style="margin-left: 0">
                                <table class="table table-bordered" id="tblProdutos">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Preço unit.</th>
                                            <th>Quantidade</th>
                                            <th>Sub-total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
foreach ($produtos as $p) {
    $total = $total + $p->subTotal;
    echo '<tr>';
    echo '<td>' . $p->descricao . '</td>';
    echo '<td>R$ ' . number_format($p->preco, 2, ',', '.') . '</td>';
    echo '<td>' . $p->quantidade . '</td>';
    echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
    echo '</tr>';
} ?>

                                        <tr>
                                            <td colspan="3" style="text-align: right"><strong>Total:</strong></td>
                                            <td><strong>R$
                                                    <?php echo number_format($total, 2, ',', '.'); ?><input type="hidden" id="total-venda" value="<?php echo number_format($total, 2); ?>"></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <!--Serviços-->
                        <div class="os-tab-pane" id="tab3" style="display:none;">
                            <div class="col-12" style="padding: 1%; margin-left: 0">

                                <div class="col-12" id="divServicos" style="margin-left: 0">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Serviço</th>
                                                <th>Preço unit.</th>
                                                <th>Quantidade</th>
                                                <th>Sub-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
    $total = 0;
foreach ($servicos as $s) {
    $total = $total + $s->subTotal;
    echo '<tr>';
    echo '<td>' . $s->nome . '</td>';
    echo '<td>R$ ' . number_format($s->preco, 2, ',', '.') . '</td>';
    echo '<td>' . $s->quantidade . '</td>';
    echo '<td>R$ ' . number_format($s->subTotal, 2, ',', '.') . '</td>';
    echo '</tr>';
} ?>

                                            <tr>
                                                <td colspan="3" style="text-align: right"><strong>Total:</strong></td>
                                                <td><strong>R$
                                                        <?php echo number_format($total, 2, ',', '.'); ?><input type="hidden" id="total-servico" value="<?php echo number_format($total, 2); ?>"></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>


                        <!--Anexos-->
                        <div class="os-tab-pane" id="tab4" style="display:none;">
                            <div class="col-12" style="padding: 1%; margin-left: 0">

                                <?php if ($this->session->userdata('cliente_anexa')) { ?>
                                    <div class="col-12 well" style="padding: 1%; margin-left: 0" id="form-anexos">
                                        <form id="formAnexos" enctype="multipart/form-data" action="javascript:;" accept-charset="utf-8" s method="post">
                                            <div class="col-10">

                                                <input type="hidden" name="idOsServico" id="idOsServico" value="<?php echo $result->idOs ?>" />
                                                <label for="">Anexo</label>
                                                <input type="file" class="col-12" name="userfile[]" multiple="multiple" size="20" />
                                            </div>
                                            <div class="col-2">
                                                <label for="">.</label>
                                                <button class="btn btn-success col-12"><i class="fas fa-paperclip"></i> Anexar</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php
                                } ?>

                                <div class="col-12" id="divAnexos" style="margin-left: 0">
                                    <?php foreach ($anexos as $a) {
                                        if ($a->thumb == null) {
                                            $thumb = base_url() . 'assets/img/icon-file.png';
                                            $link = base_url() . 'assets/img/icon-file.png';
                                        } else {
                                            $thumb = $a->url . '/thumbs/' . $a->thumb;
                                            $link = $a->url . '/' . $a->anexo;
                                        }
                                        echo '<div class="col-3" style="min-height: 150px; margin-left: 0">
                                            <a style="min-height: 150px;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo col-12" data-bs-toggle="modal">
                                            <img src="' . $thumb . '" alt="">
                                            </a>
                                            <span>' . $a->anexo . '</span>
                                            </div>';
                                    }?>
                                </div>

                            </div>
                        </div>



                    </div>

                </div>


                .

            </div>

        </div>
    </div>
</div>





<!-- Modal visualizar anexo -->
<div id="modal-anexo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="col-12" id="div-visualizar-anexo" style="text-align: center">
            <div class='progress progress-info progress-striped active'>
                <div class='bar' style='width: 100%'></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-bs-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id-imagem="" class="btn btn-inverse" id="download">Download</a>
    </div>
</div>





<!-- Modal Faturar-->
<div id="modal-faturar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="formFaturar" action="<?php echo current_url() ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Faturar Venda</h3>
        </div>
        <div class="modal-body">

            <div class="col-12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com asterisco.</div>
            <div class="col-12" style="margin-left: 0">
                <label for="descricao">Descrição*</label>
                <input class="col-12" id="descricao" type="text" name="descricao" value="Fatura de Venda - #<?php echo $result->idOs; ?> " />

            </div>
            <div class="col-12" style="margin-left: 0">
                <div class="col-12" style="margin-left: 0">
                    <label for="cliente">Cliente*</label>
                    <input class="col-12" id="cliente" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                    <input type="hidden" name="clientes_id" id="clientes_id" value="<?php echo $result->clientes_id ?>">
                    <input type="hidden" name="os_id" id="os_id" value="<?php echo $result->idOs; ?>">
                </div>


            </div>
            <div class="col-12" style="margin-left: 0">
                <div class="col-4" style="margin-left: 0">
                    <label for="valor">Valor*</label>
                    <input type="hidden" id="tipo" name="tipo" value="receita" />
                    <input class="col-12 money" id="valor" type="text" name="valor" value="<?php echo number_format($total, 2, '.', ''); ?> " />
                </div>
                <div class="col-4">
                    <label for="vencimento">Data Vencimento*</label>
                    <input class="col-12 datepicker" id="vencimento" type="text" name="vencimento" />
                </div>

            </div>

            <div class="col-12" style="margin-left: 0">
                <div class="col-4" style="margin-left: 0">
                    <label for="recebido">Recebido?</label>
                    &nbsp &nbsp &nbsp &nbsp <input id="recebido" type="checkbox" name="recebido" value="1" />
                </div>
                <div id="divRecebimento" class="col-8" style=" display: none">
                    <div class="col-6">
                        <label for="recebimento">Data Recebimento</label>
                        <input class="col-12 datepicker" id="recebimento" type="text" name="recebimento" />
                    </div>
                    <div class="col-6">
                        <label for="formaPgto">Forma Pgto</label>
                        <select name="formaPgto" id="formaPgto" class="col-12">
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Débito">Débito</option>
                            <option value="Pix">Pix</option>
                        </select>
                    </div>

                </div>


            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-bs-dismiss="modal" aria-hidden="true" id="btn-cancelar-faturar">Cancelar</button>
            <button class="btn btn-primary">Faturar</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.editor').trumbowyg({
            lang: 'pt_br',
            semantic: { 'strikethrough': 's', }
        });
    });

    $(document).on('click', '.anexo', function(event) {
        event.preventDefault();
        var link = $(this).attr('link');
        var id = $(this).attr('imagem');
        $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
        $("#download").attr('href', "<?php echo base_url(); ?>index.php/mine/downloadanexo/" + id);
    });
</script>
<script>
function showOsTab(tabId, btn) {
    document.querySelectorAll('.os-tabs .os-tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.os-tab-content .os-tab-pane').forEach(function(p) { p.classList.remove('active'); p.style.display = 'none'; });
    btn.classList.add('active');
    var pane = document.getElementById(tabId);
    if (pane) { pane.classList.add('active'); pane.style.display = 'block'; }
}
</script>
