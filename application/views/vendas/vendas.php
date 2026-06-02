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
        <h5>Vendas</h5>
    </div>
    <div class="col-12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/vendas/gerenciar">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aVenda')) { ?>
                <div class="col-3">
                    <a href="<?php echo base_url(); ?>index.php/vendas/adicionar" class="button btn btn-sm btn-success">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Nova Venda</span>
                    </a>
                </div>
            <?php } ?>
            <div class="col-3">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome do cliente a pesquisar" class="col-12" value="">
            </div>
            <div class="col-2">
                <select name="status" class="col-12">
                    <option value="">Selecione status</option>
                    <option value="Aberto">Aberto</option>
                    <option value="Faturado">Faturado</option>
                    <option value="Negociação">Negociação</option>
                    <option value="Em Andamento">Em Andamento</option>
                    <option value="Orçamento">Orçamento</option>
                    <option value="Finalizado">Finalizado</option>
                    <option value="Cancelado">Cancelado</option>
                    <option value="Aguardando Peças">Aguardando Peças</option>
                    <option value="Aprovado">Aprovado</option>
                </select>
            </div>
            <div class="col-3">
                <input type="date" name="data" id="data" placeholder="De" class="col-6 datepicker" autocomplete="off" value="">
                <input type="date" name="data2" id="data2" placeholder="Até" class="col-6 datepicker" autocomplete="off" value="">
            </div>
            <div class="col-1">
                <button class="button btn btn-sm btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                </button>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Data da Venda</th>
                        <th>Venc. da Garantia</th>
                        <th>Valor Total</th>
                        <th>Desconto</th>
                        <th>Valor com Desconto</th>
                        <th>V. T. (Faturado)</th>
                        <th>Status</th>
                        <th>Faturado</th>
                        <th style="text-align:center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!$results) {
                            echo '<tr>
                                    <td colspan="12">Nenhuma Venda Cadastrada</td>
                                </tr>';
                        }
                        foreach ($results as $r) {
                            $dataVenda = date(('d/m/Y'), strtotime($r->dataVenda));
                            $vencGarantia = '';
                            
                            if ($r->garantia && is_numeric($r->garantia)) {
                                $vencGarantia = dateInterval($r->dataVenda, $r->garantia);
                            }
                            $corGarantia = '';
                            if (!empty($vencGarantia)) {
                                $dataGarantia = explode('/', $vencGarantia);
                                $dataGarantiaFormatada = $dataGarantia[2] . '-' . $dataGarantia[1] . '-' . $dataGarantia[0];
                                $corGarantia = (strtotime($dataGarantiaFormatada) >= strtotime(date('d-m-Y'))) ? '#4d9c79' : '#f24c6f';
                            } elseif ($r->garantia == "0") {
                                $vencGarantia = 'Sem Garantia';
                            }

                            $faturado = ($r->faturado == 1) ? 'Sim' : 'Não';
                            $corStatus = match($r->status) {
                                'Aberto' => '#00cd00',
                                'Em Andamento' => '#436eee',
                                'Orçamento' => '#CDB380',
                                'Negociação' => '#AEB404',
                                'Cancelado' => '#CD0000',
                                'Finalizado' => '#256',
                                'Faturado' => '#B266FF',
                                'Aguardando Peças' => '#FF7F00',
                                'Aprovado' => '#808080',
                                default => '#E0E4CC',
                            };

                            echo '<tr>';
                            echo '<td>' . e($r->idVendas) . '</td>';
                            echo '<td><a href="' . base_url() . 'index.php/clientes/visualizar/' . e($r->idClientes) . '">' . e($r->nomeCliente) . '</a></td>';
                            echo '<td class="ph1">' . e($r->nome) . '</td>';
                            echo '<td>' . $dataVenda . '</td>';
                            echo '<td class="ph3"><span class="badge" style="background-color: ' . $corGarantia . '; border-color: ' . $corGarantia . '">' . $vencGarantia . '</span> </td>';

                            if ($r->faturado == 1) {
                                echo '<td>R$ ' . number_format($r->valorTotal, 2, ',', '.') . '</td>';
                                echo '<td>R$ ' . number_format($r->desconto, 2, ',', '.') . '</td>';
                                echo '<td>R$ ' . number_format($r->valor_desconto, 2, ',', '.') . '</td>';
                                echo '<td>R$ ' . number_format($r->valor_desconto, 2, ',', '.') . '</td>'; 
                            } else {
                                $valorProdutos = isset($r->totalProdutos) ? $r->totalProdutos : 0.00;
                                $desconto = isset($r->desconto) ? $r->desconto : 0.00;
                                $valorComDesconto = $valorProdutos - $desconto;
                            
                                echo '<td>R$ ' . number_format($valorProdutos, 2, ',', '.') . '</td>';
                                echo '<td>R$ ' . number_format($desconto, 2, ',', '.') . '</td>';
                                echo '<td>R$ ' . number_format($valorComDesconto, 2, ',', '.') . '</td>';
                                echo '<td>R$ 0,00</td>';
                            }

                            echo '<td><span class="badge" style="background-color: ' . $corStatus . '; border-color: ' . $corStatus . '">' . e($r->status) . '</span> </td>';
                            echo '<td>' . $faturado . '</td>';
                            echo '<td class="text-nowrap" style="text-align:left">';

                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
                                echo '<a href="' . base_url() . 'index.php/vendas/visualizar/' . e($r->idVendas) . '" class="btn-action btn-action-view" title="Ver mais detalhes">' . svg_icon('eye', 16, 16) . '</a>';
                                echo '<a href="' . base_url() . 'index.php/vendas/imprimir/' . e($r->idVendas) . '" target="_blank" class="btn-action btn-action-print" title="Imprimir A4">' . svg_icon('printer', 16, 16) . '</a>';
                                echo '<a href="' . base_url() . 'index.php/vendas/imprimirTermica/' . e($r->idVendas) . '" target="_blank" class="btn-action btn-action-print" title="Imprimir Não Fiscal">' . svg_icon('printer', 16, 16) . '</a>';
                            }

                            $editavel = $this->vendas_model->isEditable($r->idVendas);

                            if ($r->faturado != 1 || $editavel) {
                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
                                    echo '<a href="' . base_url() . 'index.php/vendas/editar/' . e($r->idVendas) . '" class="btn-action btn-action-edit" title="Editar venda">' . svg_icon('edit', 16, 16) . '</a>';
                                }
                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dVenda')) {
                                    echo '<a href="#modal-excluir" role="button" data-bs-toggle="modal" venda="' . e($r->idVendas) . '" class="btn-action btn-action-delete" title="Excluir Venda">' . svg_icon('trash', 16, 16) . '</a>';
                                }
                            }
                            echo '</td>';
                            echo '</tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->pagination->create_links(); ?>
</div>

<!-- Modal -->
<div id="modal-excluir" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/vendas/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Venda</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idVenda" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta Venda?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-bs-dismiss="modal" aria-hidden="true">
              <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var venda = $(this).attr('venda');
            $('#idVenda').val(venda);
        });
    });
</script>
