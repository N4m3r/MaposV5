<style>
    .os-tabs { display: flex; gap: 6px; padding: 10px 0 8px 0; flex-wrap: wrap; }
    .os-tab-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border: 1px solid var(--dark-2, #272835); border-radius: 6px; background: var(--dark-2, #272835); color: var(--dark-cinz, #8788a4); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .os-tab-btn:hover { background: #2d2e3a; color: var(--branco, #caced8); text-decoration: none; }
    .os-tab-btn.active { background: #2d335b; color: #fff; font-weight: bold; border-color: #4a4d7a; }
    .os-tab-btn i { font-size: 15px; }
    .tab-content > .tab-pane { display: none; }
    .tab-content > .tab-pane.active { display: block; }
    @media (max-width: 767px) {
        .os-tabs { flex-direction: column; }
        .os-tab-btn { width: 100%; justify-content: center; }
    }
</style>
<div class="widget-box">
    <div class="widget-title" style="margin: 0;font-size: 1.1em">
        <div class="os-tabs">
            <button class="os-tab-btn active" onclick="showOsTab('tab1', this)" data-tab="tab1"><i class="bx bx-user"></i> Dados do Cliente</button>
            <button class="os-tab-btn" onclick="showOsTab('tab2', this)" data-tab="tab2"><i class="bx bx-file"></i> Ordens de Serviço</button>
            <button class="os-tab-btn" onclick="showOsTab('tab3', this)" data-tab="tab3"><i class="bx bx-cart"></i> Vendas</button>
        </div>
    </div>
    <div class="widget-content tab-content">
        <div id="tab1" class="tab-pane active" style="min-height: 300px">
            <div class="accordion" id="collapse-group">
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGOne" data-bs-toggle="collapse">
                                <span><i class='bx bx-user icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Dados Pessoais</h5>
                            </a>
                        </div>
                    </div>
                    <div class="collapse in accordion-body" id="collapseGOne">
                        <div class="widget-content">
                            <table class="table table-bordered" style="border: 1px solid #ddd">
                                <tbody>
                                <tr>
                                    <td style="text-align: right; width: 30%"><strong>Nome</strong></td>
                                    <td>
                                        <?php echo e($result->nomeCliente) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Documento</strong></td>
                                    <td>
                                        <?php echo e($result->documento) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Inscrição Municipal</strong></td>
                                    <td>
                                        <?php echo e($result->inscricao_municipal) ?: '—'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Inscrição Estadual</strong></td>
                                    <td>
                                        <?php echo e($result->inscricao_estadual) ?: '—'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Data de Cadastro</strong></td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($result->dataCadastro)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Tipo do Cliente</strong></td>
                                    <td>
                                        <?php echo $result->fornecedor == true ? 'Fornecedor' : 'Cliente'; ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGTwo" data-bs-toggle="collapse">
                                <span><i class='bx bx-phone icon-cli'></i></span>
                                <h5 style="padding-left: 28px">Contatos</h5>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGTwo">
                        <div class="widget-content">
                            <table class="table table-bordered" style="border: 1px solid #ddd">
                                <tbody>
                                <tr>
                                    <td style="text-align: right; width: 30%"><strong>Contato:</strong></td>
                                    <td>
                                        <?php echo e($result->contato) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 30%"><strong>Telefone</strong></td>
                                    <td>
                                        <?php echo e($result->telefone) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Celular</strong></td>
                                    <td>
                                        <?php echo e($result->celular) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Email</strong></td>
                                    <td>
                                        <?php echo e($result->email) ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGThree" data-bs-toggle="collapse">
                                <span><i class='bx bx-map-alt icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Endereço</h5>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGThree">
                        <div class="widget-content">
                            <table class="table table-bordered th" style="border: 1px solid #ddd;border-left: 1px solid #ddd">
                                <tbody>
                                <tr>
                                    <td style="text-align: right; width: 30%;"><strong>Rua</strong></td>
                                    <td>
                                        <?php echo e($result->rua) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Número</strong></td>
                                    <td>
                                        <?php echo e($result->numero) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Complemento</strong></td>
                                    <td>
                                        <?php echo e($result->complemento) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Bairro</strong></td>
                                    <td>
                                        <?php echo e($result->bairro) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Cidade</strong></td>
                                    <td>
                                        <?php echo e($result->cidade) ?> -
                                        <?php echo e($result->estado) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>CEP</strong></td>
                                    <td>
                                        <?php echo e($result->cep) ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Tab 2-->
        <div id="tab2" class="tab-pane" style="min-height: 300px">
            <?php if (!$results) { ?>
                <table class="table table-bordered ">
                    <thead>
                    <tr>
                        <th>N° OS</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Descricao</th>
                        <th>Defeito</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="6">Nenhuma OS Cadastrada</td>
                    </tr>
                    </tbody>
                </table>
                <?php
            } else { ?>
                <table class="table table-bordered ">
                    <thead>
                    <tr>
                        <th>N° OS</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Descricao</th>
                        <th>Defeito</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($results as $r) {
                        $dataInicial = date(('d/m/Y'), strtotime($r->dataInicial));
                        $dataFinal = date(('d/m/Y'), strtotime($r->dataFinal));
                        echo '<tr>';
                        echo '<td>' . $r->idOs . '</td>';
                        echo '<td>' . $dataInicial . '</td>';
                        echo '<td>' . $dataFinal . '</td>';
                        echo '<td>' . e($r->descricaoProduto) . '</td>';
                        echo '<td>' . e($r->defeito) . '</td>';

                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                            echo '<a href="' . base_url() . 'index.php/os/visualizar/' . $r->idOs . '" style="margin-right: 1%" class="btn tip-top" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                            echo '<a href="' . base_url() . 'index.php/os/editar/' . $r->idOs . '" class="btn btn-info tip-top" title="Editar OS"><i class="fas fa-edit"></i></a>';
                        }

                        echo  '</td>';
                        echo '</tr>';
                    } ?>
                    <tr>
                    </tr>
                    </tbody>
                </table>
                <?php
            } ?>
        </div>
        <!--Tab 3-->
        <div id="tab3" class="tab-pane" style="min-height: 300px">
            <?php if (!$result_vendas) { ?>
                <table class="table table-bordered ">
                    <thead>
                    <tr>
                        <th>N° Venda</th>
                        <th>Data</th>
                        <th>Faturado</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="6">Nenhuma OS Cadastrada</td>
                    </tr>
                    </tbody>
                </table>
                <?php
            } else { ?>
                <table class="table table-bordered ">
                    <thead>
                    <tr>
			<th>N° Venda</th>
                        <th>Data</th>
                        <th>Faturado</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($result_vendas as $r) {
                        $dataVenda = date(('d/m/Y'), strtotime($r->dataVenda));
                        if ($r->faturado == 1) {
                            $faturado = 'Sim';
                        } else {
                            $faturado = 'Não';
                        }
                        echo '<tr>';
                        echo '<td>' . $r->idVendas . '</td>';
                        echo '<td>' . $dataVenda . '</td>';
                        echo '<td>' . e($faturado) . '</td>';
                        echo '<td>R$' . e($r->valorTotal). '</td>';

                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                            echo '<a href="' . base_url() . 'index.php/vendas/visualizar/' . $r->idVendas . '" style="margin-right: 1%" class="btn tip-top" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                            echo '<a href="' . base_url() . 'index.php/vendas/editar/' . $r->idVendas . '" class="btn btn-info tip-top" title="Editar OS"><i class="fas fa-edit"></i></a>';
                        }
                        echo  '</td>';
                        echo '</tr>';
                    } ?>
                    <tr>
                    </tr>
                    </tbody>
                </table>
                <?php
            } ?>
        </div>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
            echo '<a title="Icon Title" class="button btn btn-sm btn-info" style="min-width: 140px; top:10px" href="' . base_url() . 'index.php/clientes/editar/' . $result->idClientes . '">
<span class="button__icon"><i class="bx bx-edit"></i></span> <span class="button__text2"> Editar</span></a>';
        } ?>
        <a title="Voltar" class="button btn btn-sm btn-warning" style="min-width: 140px; top:10px" href="<?php echo site_url() ?>/clientes">
          <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
    </div>
</div>
<script>
function showOsTab(tabId, btn) {
    document.querySelectorAll('.os-tabs .os-tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.tab-content .tab-pane').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    var pane = document.getElementById(tabId);
    if (pane) pane.classList.add('active');
}
</script>
