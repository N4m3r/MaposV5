<style>
    .os-tabs { display: flex; gap: 6px; padding: 10px 0 8px 0; flex-wrap: wrap; align-items: center; }
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
</style>
<div class="widget-box">
    <div class="widget-title">
        <div class="os-tabs">
            <button class="os-tab-btn active" onclick="showOsTab('tab1', this)" data-tab="tab1"><i class="bx bx-user"></i> Meus Dados</button>
            <a title="Editar" class="button btn btn-success" style="margin: 5px" href="<?php echo base_url() ?>index.php/mine/editarDados/<?php echo $result->idClientes ?>">
              <span class="button__icon"><i class="bx bx-edit"></i> </span> <span class="button__text2">Editar</span></a>
        </div>
    </div>
    <div class="widget-content os-tab-content">
        <div id="tab1" class="os-tab-pane active" style="min-height: 300px">

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
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td style="text-align: right; width: 30%"><strong>Nome</strong></td>
                                        <td>
                                            <?php echo e($result->nomeCliente) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 30%"><strong>Contato</strong></td>
                                        <td>
                                            <?php echo e($result->contato) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right"><strong>Documento</strong></td>
                                        <td>
                                            <?php echo e($result->documento) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right"><strong>Data de Cadastro</strong></td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($result->dataCadastro)) ?>
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
                            <table class="table table-bordered">
                                <tbody>
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
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td style="text-align: right; width: 30%"><strong>Rua</strong></td>
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
    </div>
</div>
<script>
function showOsTab(tabId, btn) {
    document.querySelectorAll('.os-tabs .os-tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.os-tab-content .os-tab-pane').forEach(function(p) { p.classList.remove('active'); p.style.display = 'none'; });
    btn.classList.add('active');
    var pane = document.getElementById(tabId);
    if (pane) { pane.classList.add('active'); pane.style.display = 'block'; }
}
</script>
