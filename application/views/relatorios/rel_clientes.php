<div class="row" style="margin-top: 0">
    <div class="col-4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                <h5>Relatórios Rápidos</h5>
            </div>
            <div class="widget-content">
                <ul style="flex-direction: row;" class="site-stats">
                    <li><a href="<?php echo base_url() ?>index.php/relatorios/clientesRapid" target="_blank"><i
                                    class="fas fa-users"></i> <small>Todos os Clientes - pdf</small></a></li>
                    <li><a href="<?php echo base_url() ?>index.php/relatorios/clientesRapid?format=xls" target="_blank"><i
                                    class="fas fa-users"></i> <small>Todos os Clientes - xls</small></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                <h5>Relatórios Customizáveis</h5>
            </div>
            <div class="widget-content">
                <div class="col-12 well">
                    <form target="_blank" action="<?php echo base_url() ?>index.php/relatorios/clientesCustom"
                          method="get">
                        <div class="col-4">
                            <label for="">Cadastrado de:</label>
                            <input type="date" name="dataInicial" class="col-12"/>
                        </div>
                        <div class="col-4">
                            <label for="">até:</label>
                            <input type="date" name="dataFinal" class="col-12"/>
                        </div>
                        <div class="col-4">
                            <label for="">.</label>
                            <button class="button btn btn-inverse"><span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text2">Imprimir</span></button>
                        </div>
                        <div class="col-12 well" style="margin-left: 0">
                        <div class="col-12">
                            <label for="">Tipo de cliente:</label>
                            <select name="tipocliente" class="col-12">
                                <option value="0">Cliente</option>
                                <option value="1">Fornecedor</option>
                            </select>
                        </div>
                    </div>
                    </form>
                </div>
                .
            </div>
        </div>
    </div>
</div>
