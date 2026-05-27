<div class="row" style="margin-top: 0">
    <div class="col-4">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-wrench"></i>
                </span>
                <h5>Relatórios Rápidos</h5>
            </div>
            <div class="widget-content">
                <ul style="flex-direction: row;" class="site-stats">
                    <li><a target="_blank" href="<?php echo base_url() ?>index.php/relatorios/servicosRapid"><i class="fas fa-wrench"></i> <small>Todos os Serviços</small></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-wrench"></i>
                </span>
                <h5>Relatórios Customizáveis</h5>
            </div>
            <div class="widget-content">
                <div class="col-12 well">
                    <form target="_blank" action="<?php echo base_url() ?>index.php/relatorios/servicosCustom" method="get">
                        <div class="col-12 well">
                            <div class="col-6">
                                <label for="">Preço de:</label>
                                <input type="text" name="precoInicial" class="col-12 money" />
                            </div>
                            <div class="col-6">
                                <label for="">até:</label>
                                <input type="text" name="precoFinal" class="col-12 money" />
                            </div>
                        </div>
                        <div class="col-12" style="display:flex;justify-content: center">
                            <button type="reset" class="button btn btn-warning">
                                <span class="button__icon"><i class="bx bx-brush-alt"></i></span>
                                <span class="button__text">Limpar</span>
                            </button>
                            <button class="button btn btn-inverse">
                                <span class="button__icon"><i class="bx bx-printer"></i></span>
                                <span class="button__text">Imprimir</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".money").maskMoney();
    });
</script>
