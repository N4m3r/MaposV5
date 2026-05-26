<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-hdd"></i>
                </span>
                <h5>Cadastro de Arquivo</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo e($custom_error); ?>
                <form action="<?php echo current_url(); ?>" id="formArquivo" method="post" class="form-horizontal">


                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Arquivo*</label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo e($result->documento); ?> " />

                            <input id="idDocumentos" type="hidden" name="idDocumentos" value="<?php echo e($result->idDocumentos); ?> " />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <div class="controls">
                            <textarea rows="3" cols="30" name="descricao" id="descricao"><?php echo e($result->descricao); ?></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Data</label>
                        <div class="controls">
                            <input id="data" type="text" class="datepicker" name="data" value="<?php echo date('d/m/Y', strtotime($result->cadastro)); ?>" />
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="col-12">
                            <div class="col-6 offset-md-3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/arquivos" class="button btn btn-sm btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#formArquivo').validate({
            rules: {
                nome: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: 'Campo Requerido.'
                }
            },

            errorClass: "form-text",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.mb-3').addClass('error');
                $(element).parents('.mb-3').removeClass('success');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.mb-3').removeClass('error');
                $(element).parents('.mb-3').addClass('success');
            }
        });


        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });
</script>
