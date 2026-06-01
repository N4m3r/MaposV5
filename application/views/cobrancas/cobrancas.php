<style>
  select {
    width: 70px;
  }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-cash-register"></i>
            </span>
            <h5>Cobranças</h5>
    </div>
    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Gateway</th>
                        <th>Tipo</th>
                        <th>Data de Vencimento</th>
                        <th>Referência</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results) {
                        echo '<tr>
                                <td colspan="8">Nenhuma cobrança Cadastrada</td>
                            </tr>';
                    }
                    foreach ($results as $r) {
                        $dataVenda = date(('d/m/Y'), strtotime($r->expire_at));
                        $cobrancaStatus = getCobrancaTransactionStatus(
                            $this->config->item('payment_gateways'),
                            $r->payment_gateway,
                            $r->status
                        );

                        echo '<tr>';
                        echo '<td>' . e($r->idCobranca) . '</td>';
                        echo '<td>' . e($r->payment_gateway) . '</td>';
                        echo '<td>' . e($r->payment_method) . '</td>';
                        echo '<td>' . e($dataVenda) . '</td>';

                        if ($r->os_id != '') {
                            echo '<td><a href="' . base_url() . 'index.php/os/visualizar/' . $r->os_id . '"> Ordem de Serviço: #' . e($r->os_id) . '</a></td>';
                        }
                        if ($r->vendas_id != '') {
                            echo '<td><a href="' . base_url() . 'index.php/vendas/visualizar/' . $r->vendas_id . '"> Venda: #' . e($r->vendas_id) . '</a></td>';
                        }

                        echo '<td>' . e($cobrancaStatus) . '</td>';
                        echo '<td>R$ ' . e(number_format($r->total / 100, 2, ',', '.')) . '</td>';
                        echo '<td class="text-nowrap">';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
                            echo '<a href="#modal-cancelar" role="button" data-bs-toggle="modal" cancela_id="' . $r->idCobranca . '" class="btn-action btn-action-delete" title="Cancelar Cobrança">' . svg_icon('x', 16, 16) . '</a>';
                            echo '<a href="' . base_url() . 'index.php/cobrancas/atualizar/' . $r->idCobranca . '" class="btn-action btn-action-secondary" title="Atualizar Cobrança">' . svg_icon('refresh', 16, 16) . '</a>';
                            echo '<a href="#modal-confirmar" role="button" data-bs-toggle="modal" confirma_id="' . $r->idCobranca . '" class="btn-action btn-action-finance" title="Confirmar pagamento">' . svg_icon('check', 16, 16) . '</a>';
                            echo '<a href="' . base_url() . 'index.php/cobrancas/visualizar/' . $r->idCobranca . '" class="btn-action btn-action-view" title="Ver mais detalhes">' . svg_icon('eye', 16, 16) . '</a>';
                            echo '<a href="' . base_url() . 'index.php/cobrancas/enviarEmail/' . $r->idCobranca . '" class="btn-action btn-action-finance" title="Enviar por E-mail">' . svg_icon('envelope', 16, 16) . '</a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca') && $r->barcode != '') {
                            echo '<a href="' . e($r->link) . '" target="_blank" class="btn-action btn-action-secondary" title="Visualizar boleto">' . svg_icon('barcode', 16, 16) . '</a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCobranca')) {
                            echo '<a href="#modal-excluir" role="button" data-bs-toggle="modal" excluir_id="' . $r->idCobranca . '" class="btn-action btn-action-delete" title="Excluir Cobrança">' . svg_icon('trash', 16, 16) . '</a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->pagination->create_links(); ?>

    <!-- Modal -->
    <div id="modal-excluir" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/cobrancas/excluir" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Excluir cobrança</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="excluir_id" name="excluir_id" value="" />
                <h5 style="text-align: center">Deseja realmente excluir esta cobrança? A cobrança será cancelada.</h5>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-warning" data-bs-dismiss="modal" aria-hidden="true"><span class="button__icon"><?= svg_icon('x', 16, 16) ?></span><span class="button__text2">Cancelar</span></button>
                <button class="button btn btn-danger"><span class="button__icon"><?= svg_icon('trash', 16, 16) ?></span> <span class="button__text2">Excluir</span></button>
            </div>
        </form>
    </div>

    <div id="modal-confirmar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/cobrancas/confirmarpagamento" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Confirmar pagamento</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="confirma_id" name="confirma_id" value="" />
                <h5 style="text-align: center">Deseja realmente confirmar pagamento desta cobrança?</h5>
            </div>
            <div class="modal-footer">
                <button class="btn" data-bs-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button class="btn btn-success">Confirmar</button>
            </div>
        </form>
    </div>

    <div id="modal-cancelar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/cobrancas/cancelar" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Cancelar cobrança</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancela_id" name="cancela_id" value="" />
                <h5 style="text-align: center">Deseja realmente Cancelar esta cobrança?</h5>
            </div>
            <div class="modal-footer">
                <button class="btn" data-bs-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button class="btn btn-danger">Confirmar</button>
            </div>
        </form>
    </div>
</div>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', 'a', function(event) {
                var cobranca = $(this).attr('excluir_id');
                $('#excluir_id').val(cobranca);
            });

            $(document).on('click', 'a', function(event) {
                var cobranca = $(this).attr('confirma_id');
                $('#confirma_id').val(cobranca);
            });

            $(document).on('click', 'a', function(event) {
                var cobranca = $(this).attr('cancela_id');
                $('#cancela_id').val(cobranca);
            });
        });
    </script>