
<style>
    .widget-title h5 {
        font-weight : 500;
        padding     : 5px;
        padding-left: 36px !important;
        line-height : 12px;
        margin      : 5px 0 !important;
        font-size   : 1.3em;
        color       : var(--violeta1);
    }

    .icon-cli {
        color: #239683;
        margin-top : 3px;
        margin-left: 8px;
        position   : absolute;
        font-size  : 18px;
    }

    .icon-clic {
        color: #9faab7;
        top: 4px;
        right: 10px;
        position: absolute;
        font-size: 1.9em;
    }

    .icon-clic:hover {
        color: #3fadf6;
    }

    .widget-content {
        padding: 8px 12px 0;
    }

    .table td {
        padding: 5px;
    }

    .table {
        margin-bottom: 0;
    }

    .accordion .widget-box {
        margin-top   : 10px;
        margin-bottom: 0;
        border-radius: 6px;
    }

    .accordion {
        margin-top: -25px;
    }

    .collapse.in {
        top: -15px
    }

    .button {
    min-width: 130px;
    }

    .form-actions {
        padding: 0;
        margin-top: 20px;
        margin-bottom: 20px;
        background-color: transparent;
        border-top: 0px;
    }

    .widget-content table tbody tr:hover {
        background: transparent;
    }

@media (max-width: 480px) {
    .widget-content {
        padding      : 10px 7px !important;
        margin-bottom: -15px;
    }
}

</style>

<?php $permissoes = unserialize($result->permissoes);?>
<div class="span12" style="margin-left: 0">
    <form action="<?php echo base_url();?>index.php/permissoes/editar" id="formPermissao" method="post">
        <div class="span12" style="margin-left: 0">
            <div class="widget-box">
                <div class="widget-title">
               <span class="icon">
               <i class="fas fa-lock"></i>
               </span>
                    <h5 style="padding:12px;padding-left:18px!important;margin:-10px 0 0!important;font-size:1.7em;">Editar Permissão</h5>
                </div>
                <div class="widget-content">
                    <div class="span4">
                        <label>Nome da Permissão</label>
                        <input name="nome" type="text" id="nome" class="span12" value="<?php echo $result->nome; ?>" />
                        <input type="hidden" name="idPermissao" value="<?php echo $result->idPermissao; ?>">
                    </div>
                    <div class="span3">
                        <label>Situação</label>
                        <select name="situacao" id="situacao" class="span12">
                            <?php if ($result->situacao == 1) {
                                $sim = 'selected';
                                $nao ='';
                            } else {
                                $sim = '';
                                $nao ='selected';
                            }?>
                            <option value="1" <?php echo $sim;?>>Ativo</option>
                            <option value="0" <?php echo $nao;?>>Inativo</option>
                        </select>
                    </div>
                    <div class="span4">
                        <label>
                            <input name="" type="checkbox" value="1" id="marcarTodos" />
                            <span class="lbl"> Marcar Todos</span>
                        </label>
                    </div>

                    <div class="control-group">
                        <label for="documento" class="control-label"></label>
                        <div class="controls">

                    <div class="widget-content" style="padding: 5px 0 !important">
        <div id="tab1" class="tab-pane active" style="min-height: 300px">
            <div class="accordion" id="collapse-group">
                <!-- Clientes -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                                <span><i class='bx bx-group icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Clientes</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse in accordion-body" id="collapseGOne">
                        <div class="widget-content">
                        <table class="table table-bordered">
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['vCliente']) && $permissoes['vCliente'] == '1') ? 'checked' : ''; ?> name="vCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Cliente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aCliente']) && $permissoes['aCliente'] == '1') ? 'checked' : ''; ?> name="aCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Cliente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eCliente']) && $permissoes['eCliente'] == '1') ? 'checked' : ''; ?> name="eCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Cliente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dCliente']) && $permissoes['dCliente'] == '1') ? 'checked' : ''; ?> name="dCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Cliente</span>
                                        </label>
                                    </td>
                                </tr>
                        </table>
                    </div>
                </div>
            </div>

                <!-- Produtos -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGTwo" data-toggle="collapse">
                                <span><i class='bx bx-package icon-cli'></i></span>
                                <h5 style="padding-left: 28px">Produtos</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGTwo">
                        <div class="widget-content">
                        <table class="table table-bordered">
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vProduto']) && $permissoes['vProduto'] == '1') ? 'checked' : ''; ?> name="vProduto" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Produto</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aProduto']) && $permissoes['aProduto'] == '1') ? 'checked' : ''; ?> name="aProduto" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Produto</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eProduto']) && $permissoes['eProduto'] == '1') ? 'checked' : ''; ?> name="eProduto" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Produto</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dProduto']) && $permissoes['dProduto'] == '1') ? 'checked' : ''; ?> name="dProduto" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Produto</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Serviços -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGThree" data-toggle="collapse">
                                <span><i class='bx bx-stopwatch icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Serviços</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGThree">
                        <div class="widget-content">
                        <table class="table table-bordered">
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vServico']) && $permissoes['vServico'] == '1') ? 'checked' : ''; ?> name="vServico" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Serviço</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aServico']) && $permissoes['aServico'] == '1') ? 'checked' : ''; ?> name="aServico" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Serviço</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eServico']) && $permissoes['eServico'] == '1') ? 'checked' : ''; ?> name="eServico" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Serviço</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dServico']) && $permissoes['dServico'] == '1') ? 'checked' : ''; ?> name="dServico" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Serviço</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Ordens de Serviço -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGFour" data-toggle="collapse">
                                <span><i class='bx bx-spreadsheet icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Ordens de Serviço</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGFour">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vOs']) && $permissoes['vOs'] == '1') ? 'checked' : ''; ?> name="vOs" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar OS</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aOs']) && $permissoes['aOs'] == '1') ? 'checked' : ''; ?> name="aOs" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar OS</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eOs']) && $permissoes['eOs'] == '1') ? 'checked' : ''; ?> name="eOs" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar OS</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dOs']) && $permissoes['dOs'] == '1') ? 'checked' : ''; ?> name="dOs" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir OS</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <label>
                                            <input <?php echo (isset($permissoes['vBtnAtendimento']) && $permissoes['vBtnAtendimento'] == '1') ? 'checked' : ''; ?> name="vBtnAtendimento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Botões Iniciar/Finalizar Atendimento</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Obras e Projetos -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGObras" data-toggle="collapse">
                                <span><i class='bx bx-building-house icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Obras e Projetos</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGObras">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vObras']) && $permissoes['vObras'] == '1') ? 'checked' : ''; ?> name="vObras" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Visualizar Obras</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['cObras']) && $permissoes['cObras'] == '1') ? 'checked' : ''; ?> name="cObras" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Cadastrar Obras</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eObras']) && $permissoes['eObras'] == '1') ? 'checked' : ''; ?> name="eObras" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Editar Obras</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['dObras']) && $permissoes['dObras'] == '1') ? 'checked' : ''; ?> name="dObras" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Excluir Obras</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vObrasTodas']) && $permissoes['vObrasTodas'] == '1') ? 'checked' : ''; ?> name="vObrasTodas" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Visualizar Todas as Obras</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vObrasRelatorios']) && $permissoes['vObrasRelatorios'] == '1') ? 'checked' : ''; ?> name="vObrasRelatorios" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Relatórios de Obras</span>
                                    </label>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </table>

                        <!-- Permissões de Técnico para Obras -->
                        <div class="widget-title" style="background: #f5f5f5; padding: 5px; margin: 10px 0;">
                            <h6 style="margin: 0;"><i class='bx bx-hard-hat'></i> Permissões do Técnico em Obras</h6>
                        </div>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vTecnicoObra']) && $permissoes['vTecnicoObra'] == '1') ? 'checked' : ''; ?> name="vTecnicoObra" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Ver Obras Atribuídas</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eTecnicoExec']) && $permissoes['eTecnicoExec'] == '1') ? 'checked' : ''; ?> name="eTecnicoExec" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Executar Atividades</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eTecnicoFotos']) && $permissoes['eTecnicoFotos'] == '1') ? 'checked' : ''; ?> name="eTecnicoFotos" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Adicionar Fotos</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eTecnicoImped']) && $permissoes['eTecnicoImped'] == '1') ? 'checked' : ''; ?> name="eTecnicoImped" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Registrar Impedimentos</span>
                                    </label>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <!-- Permissões de Técnico (OS) -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGTecnico" data-toggle="collapse">
                                <span><i class='bx bx-user-check icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Permissões de Técnico (OS)</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGTecnico">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vTecnicoDashboard']) && $permissoes['vTecnicoDashboard'] == '1') ? 'checked' : ''; ?> name="vTecnicoDashboard" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Dashboard Técnico</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vTecnicoOS']) && $permissoes['vTecnicoOS'] == '1') ? 'checked' : ''; ?> name="vTecnicoOS" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Visualizar apenas OS atribuídas</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eTecnico']) && $permissoes['eTecnico'] == '1') ? 'checked' : ''; ?> name="eTecnico" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Editar como Técnico</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eTecnicoCheckin']) && $permissoes['eTecnicoCheckin'] == '1') ? 'checked' : ''; ?> name="eTecnicoCheckin" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Iniciar Atendimento (Check-in)</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eTecnicoCheckout']) && $permissoes['eTecnicoCheckout'] == '1') ? 'checked' : ''; ?> name="eTecnicoCheckout" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Finalizar Atendimento (Check-out)</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eTecnicoFotos']) && $permissoes['eTecnicoFotos'] == '1') ? 'checked' : ''; ?> name="eTecnicoFotos" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Adicionar Fotos ao Atendimento</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vTecnicoFotos']) && $permissoes['vTecnicoFotos'] == '1') ? 'checked' : ''; ?> name="vTecnicoFotos" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Visualizar Fotos do Atendimento</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vTecnicoAssinaturas']) && $permissoes['vTecnicoAssinaturas'] == '1') ? 'checked' : ''; ?> name="vTecnicoAssinaturas" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Visualizar Assinaturas</span>
                                    </label>
                                </td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>

                <!-- Vendas -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGFive" data-toggle="collapse">
                                <span><i class='bx bx-cart-alt icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Vendas</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGFive">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vVenda']) && $permissoes['vVenda'] == '1') ? 'checked' : ''; ?> name="vVenda" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Venda</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aVenda']) && $permissoes['aVenda'] == '1') ? 'checked' : ''; ?> name="aVenda" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Venda</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eVenda']) && $permissoes['eVenda'] == '1') ? 'checked' : ''; ?> name="eVenda" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Venda</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dVenda']) && $permissoes['dVenda'] == '1') ? 'checked' : ''; ?> name="dVenda" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Venda</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Cobranças -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGSix" data-toggle="collapse">
                                <span><i class='bx bx-credit-card-front icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Cobranças</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGSix">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vCobranca']) && $permissoes['vCobranca'] == '1') ? 'checked' : ''; ?> name="vCobranca" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Cobranças</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aCobranca']) && $permissoes['aCobranca'] == '1') ? 'checked' : ''; ?> name="aCobranca" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Cobranças</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eCobranca']) && $permissoes['eCobranca'] == '1') ? 'checked' : ''; ?> name="eCobranca" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Cobranças</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dCobranca']) && $permissoes['dCobranca'] == '1') ? 'checked' : ''; ?> name="dCobranca" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Cobranças</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Garantias -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGSeven" data-toggle="collapse">
                                <span><i class='bx bx-receipt icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Garantias</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGSeven">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vGarantia']) && $permissoes['vGarantia'] == '1') ? 'checked' : ''; ?> name="vGarantia" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Garantia</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aGarantia']) && $permissoes['aGarantia'] == '1') ? 'checked' : ''; ?> name="aGarantia" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Garantia</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eGarantia']) && $permissoes['eGarantia'] == '1') ? 'checked' : ''; ?> name="eGarantia" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Garantia</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dGarantia']) && $permissoes['dGarantia'] == '1') ? 'checked' : ''; ?> name="dGarantia" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Garantia</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Arquivos -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGEight" data-toggle="collapse">
                                <span><i class='bx bx-box icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Arquivos</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGEight">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vArquivo']) && $permissoes['vArquivo'] == '1') ? 'checked' : ''; ?> name="vArquivo" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Arquivo</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aArquivo']) && $permissoes['aArquivo'] == '1') ? 'checked' : ''; ?> name="aArquivo" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Arquivo</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eArquivo']) && $permissoes['eArquivo'] == '1') ? 'checked' : ''; ?> name="eArquivo" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Arquivo</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dArquivo']) && $permissoes['dArquivo'] == '1') ? 'checked' : ''; ?> name="dArquivo" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Arquivo</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagamentos -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGPagamento" data-toggle="collapse">
                                <span><i class='bx bx-money icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Pagamentos</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGPagamento">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vPagamento']) && $permissoes['vPagamento'] == '1') ? 'checked' : ''; ?> name="vPagamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Pagamento</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aPagamento']) && $permissoes['aPagamento'] == '1') ? 'checked' : ''; ?> name="aPagamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Pagamento</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['ePagamento']) && $permissoes['ePagamento'] == '1') ? 'checked' : ''; ?> name="ePagamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Pagamento</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dPagamento']) && $permissoes['dPagamento'] == '1') ? 'checked' : ''; ?> name="dPagamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Pagamento</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Financeiro -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGNine" data-toggle="collapse">
                                <span><i class='bx bx-bar-chart-square icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Financeiro</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGNine">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vLancamento']) && $permissoes['vLancamento'] == '1') ? 'checked' : ''; ?> name="vLancamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Lançamento</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['aLancamento']) && $permissoes['aLancamento'] == '1') ? 'checked' : ''; ?> name="aLancamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Lançamento</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eLancamento']) && $permissoes['eLancamento'] == '1') ? 'checked' : ''; ?> name="eLancamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Lançamento</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dLancamento']) && $permissoes['dLancamento'] == '1') ? 'checked' : ''; ?> name="dLancamento" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Lançamento</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Relatórios -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGTen" data-toggle="collapse">
                                <span><i class='bx bx-chart icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Relatórios</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGTen">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['rCliente']) && $permissoes['rCliente'] == '1') ? 'checked' : ''; ?> name="rCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório Cliente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['rServico']) && $permissoes['rServico'] == '1') ? 'checked' : ''; ?> name="rServico" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório Serviço</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['rOs']) && $permissoes['rOs'] == '1') ? 'checked' : ''; ?> name="rOs" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório OS</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['rProduto']) && $permissoes['rProduto'] == '1') ? 'checked' : ''; ?> name="rProduto" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório Produto</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['rVenda']) && $permissoes['rVenda'] == '1') ? 'checked' : ''; ?> name="rVenda" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório Venda</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['rFinanceiro']) && $permissoes['rFinanceiro'] == '1') ? 'checked' : ''; ?> name="rFinanceiro" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório Financeiro</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['vRelatorioTecnicos']) && $permissoes['vRelatorioTecnicos'] == '1') ? 'checked' : ''; ?> name="vRelatorioTecnicos" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório de Técnicos</span>
                                        </label>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Dashboard e Relatórios Avançados -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGDashboard" data-toggle="collapse">
                                <span><i class='bx bx-dashboard icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Dashboard e Relatórios Avançados</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGDashboard">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vDashboard']) && $permissoes['vDashboard'] == '1') ? 'checked' : ''; ?> name="vDashboard" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Dashboard</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['vRelatorioCompleto']) && $permissoes['vRelatorioCompleto'] == '1') ? 'checked' : ''; ?> name="vRelatorioCompleto" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório Completo</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['vExportarDados']) && $permissoes['vExportarDados'] == '1') ? 'checked' : ''; ?> name="vExportarDados" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Exportar Dados</span>
                                        </label>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Configurações e Sistema -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGEleven" data-toggle="collapse">
                                <span><i class='bx bx-cog icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Configurações e Sistema</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGEleven">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['cUsuario']) && $permissoes['cUsuario'] == '1') ? 'checked' : ''; ?> name="cUsuario" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Configurar Usuário</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['cEmitente']) && $permissoes['cEmitente'] == '1') ? 'checked' : ''; ?> name="cEmitente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Configurar Emitente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['cPermissao']) && $permissoes['cPermissao'] == '1') ? 'checked' : ''; ?> name="cPermissao" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Configurar Permissão</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['cAuditoria']) && $permissoes['cAuditoria'] == '1') ? 'checked' : ''; ?> name="cAuditoria" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Auditoria</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['cEmail']) && $permissoes['cEmail'] == '1') ? 'checked' : ''; ?> name="cEmail" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Emails</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['cSistema']) && $permissoes['cSistema'] == '1') ? 'checked' : ''; ?> name="cSistema" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Sistema</span>
                                        </label>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Backup -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGBackup" data-toggle="collapse">
                                <span><i class='bx bx-data icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Backup</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGBackup">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['cBackup']) && $permissoes['cBackup'] == '1') ? 'checked' : ''; ?> name="cBackup" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Acessar Backup</span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>

                <!-- Portal do Cliente - Usuários -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGClientePortal" data-toggle="collapse">
                                <span><i class='bx bx-user-circle icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Portal do Cliente - Usuários</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGClientePortal">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vUsuariosCliente']) && $permissoes['vUsuariosCliente'] == '1') ? 'checked' : ''; ?> name="vUsuariosCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Usuários Cliente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['cUsuariosCliente']) && $permissoes['cUsuariosCliente'] == '1') ? 'checked' : ''; ?> name="cUsuariosCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Usuários Cliente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['eUsuariosCliente']) && $permissoes['eUsuariosCliente'] == '1') ? 'checked' : ''; ?> name="eUsuariosCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Usuários Cliente</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input <?php echo (isset($permissoes['dUsuariosCliente']) && $permissoes['dUsuariosCliente'] == '1') ? 'checked' : ''; ?> name="dUsuariosCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Remover Usuários Cliente</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <label>
                                            <input <?php echo (isset($permissoes['cPermUsuariosCliente']) && $permissoes['cPermUsuariosCliente'] == '1') ? 'checked' : ''; ?> name="cPermUsuariosCliente" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Configurar Permissões Usuários Cliente</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Novas Funcionalidades V5 -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGV5" data-toggle="collapse">
                                <span><i class='bx bx-rocket icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Novas Funcionalidades V5</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGV5">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vCertificado']) && $permissoes['vCertificado'] == '1') ? 'checked' : ''; ?> name="vCertificado" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Certificado</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['cCertificado']) && $permissoes['cCertificado'] == '1') ? 'checked' : ''; ?> name="cCertificado" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Certificado</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['eCertificado']) && $permissoes['eCertificado'] == '1') ? 'checked' : ''; ?> name="eCertificado" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Certificado</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['dCertificado']) && $permissoes['dCertificado'] == '1') ? 'checked' : ''; ?> name="dCertificado" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Certificado</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vImpostos']) && $permissoes['vImpostos'] == '1') ? 'checked' : ''; ?> name="vImpostos" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Impostos</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['cImpostos']) && $permissoes['cImpostos'] == '1') ? 'checked' : ''; ?> name="cImpostos" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar Impostos</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['eImpostos']) && $permissoes['eImpostos'] == '1') ? 'checked' : ''; ?> name="eImpostos" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar Impostos</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['dImpostos']) && $permissoes['dImpostos'] == '1') ? 'checked' : ''; ?> name="dImpostos" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir Impostos</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['cImpostosConfig']) && $permissoes['cImpostosConfig'] == '1') ? 'checked' : ''; ?> name="cImpostosConfig" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Configurar Impostos</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vImpostosRelatorio']) && $permissoes['vImpostosRelatorio'] == '1') ? 'checked' : ''; ?> name="vImpostosRelatorio" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório de Impostos</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vImpostosExportar']) && $permissoes['vImpostosExportar'] == '1') ? 'checked' : ''; ?> name="vImpostosExportar" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Exportar Impostos</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                        <i class='bx bx-bar-chart-alt-2'></i> DRE Contábil
                                    </td>
                                </tr>
                                <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vDRE']) && $permissoes['vDRE'] == '1') ? 'checked' : ''; ?> name="vDRE" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar DRE</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vDREDemonstracao']) && $permissoes['vDREDemonstracao'] == '1') ? 'checked' : ''; ?> name="vDREDemonstracao" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Demonstração DRE</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vDREContas']) && $permissoes['vDREContas'] == '1') ? 'checked' : ''; ?> name="vDREContas" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Plano de Contas</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vDRELancamentos']) && $permissoes['vDRELancamentos'] == '1') ? 'checked' : ''; ?> name="vDRELancamentos" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Lançamentos DRE</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['cDRE']) && $permissoes['cDRE'] == '1') ? 'checked' : ''; ?> name="cDRE" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Adicionar DRE</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['eDRE']) && $permissoes['eDRE'] == '1') ? 'checked' : ''; ?> name="eDRE" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Editar DRE</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['dDRE']) && $permissoes['dDRE'] == '1') ? 'checked' : ''; ?> name="dDRE" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Excluir DRE</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vRelatorioAtendimentos']) && $permissoes['vRelatorioAtendimentos'] == '1') ? 'checked' : ''; ?> name="vRelatorioAtendimentos" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Relatório de Atendimentos</span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vWebhooks']) && $permissoes['vWebhooks'] == '1') ? 'checked' : ''; ?> name="vWebhooks" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Webhooks</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['cDocOs']) && $permissoes['cDocOs'] == '1') ? 'checked' : ''; ?> name="cDocOs" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Vincular Documentos à OS</span>
                                        </label>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Agente IA -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGAgenteIA" data-toggle="collapse">
                                <span><i class='bx bx-bot icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">Agente IA (WhatsApp/n8n/LLMs)</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGAgenteIA">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['vAgenteIA']) && $permissoes['vAgenteIA'] == '1') ? 'checked' : ''; ?> name="vAgenteIA" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Visualizar Painel Agente IA</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['cAgenteIA']) && $permissoes['cAgenteIA'] == '1') ? 'checked' : ''; ?> name="cAgenteIA" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Configurar Agente IA</span>
                                        </label>
                                    </td>
                                <td>
                                    <label>
                                            <input <?php echo (isset($permissoes['eAgenteIA']) && $permissoes['eAgenteIA'] == '1') ? 'checked' : ''; ?> name="eAgenteIA" class="marcar" type="checkbox" value="1" />
                                            <span class="lbl"> Autorizar/Rejeitar Agente IA</span>
                                        </label>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- NFSe e Boletos -->
                <div class="accordion-group widget-box">
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-parent="#collapse-group" href="#collapseGNFSe" data-toggle="collapse">
                                <span><i class='bx bx-receipt icon-cli' ></i></span>
                                <h5 style="padding-left: 28px">NFSe e Boletos</h5>
                                <span><i class='bx bx-chevron-right icon-clic'></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGNFSe">
                        <div class="widget-content">
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="4" style="background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                    <i class='bx bx-file'></i> NFS-e (Nota Fiscal de Serviço)
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vNFSe']) && $permissoes['vNFSe'] == '1') ? 'checked' : ''; ?> name="vNFSe" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Visualizar NFSe (OS)</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['cNFSe']) && $permissoes['cNFSe'] == '1') ? 'checked' : ''; ?> name="cNFSe" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Cadastrar NFSe (OS)</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eNFSe']) && $permissoes['eNFSe'] == '1') ? 'checked' : ''; ?> name="eNFSe" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Editar/Cancelar NFSe (OS)</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['rNFSe']) && $permissoes['rNFSe'] == '1') ? 'checked' : ''; ?> name="rNFSe" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Relatório NFSe</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                    <i class='bx bx-barcode'></i> Boletos de Cobrança
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['vBoletoOS']) && $permissoes['vBoletoOS'] == '1') ? 'checked' : ''; ?> name="vBoletoOS" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Visualizar Boleto OS</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['cBoletoOS']) && $permissoes['cBoletoOS'] == '1') ? 'checked' : ''; ?> name="cBoletoOS" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Cadastrar Boleto OS</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['eBoletoOS']) && $permissoes['eBoletoOS'] == '1') ? 'checked' : ''; ?> name="eBoletoOS" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Editar/Cancelar Boleto OS</span>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input <?php echo (isset($permissoes['pBoletoOS']) && $permissoes['pBoletoOS'] == '1') ? 'checked' : ''; ?> name="pBoletoOS" class="marcar" type="checkbox" value="1" />
                                        <span class="lbl"> Registrar Pagamento Boleto</span>
                                    </label>
                                </td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                              <button type="submit" class="button btn btn-primary">
                              <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span></button>
                                <a title="Voltar" class="button btn btn-mini btn-warning" href="<?php echo site_url() ?>/permissoes">
                                  <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<script type="text/javascript" src="<?php echo base_url()?>assets/js/validate.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#marcarTodos").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });
        $("#formPermissao").validate({
            rules :{
                nome: {required: true}
            },
            messages:{
                nome: {required: 'Campo obrigatório'}
            }});
    });
</script>
