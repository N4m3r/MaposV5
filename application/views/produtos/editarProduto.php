<style>
    .badgebox { opacity: 0; position: absolute; }
    .badgebox + .badge { width: 27px; text-indent: -999999px; }
    .badgebox:focus + .badge { box-shadow: inset 0px 0px 5px; }
    .badgebox:checked + .badge { text-indent: 0; }
    .form-check-label-custom { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.12); cursor: pointer; font-size: 0.875rem; transition: all 0.2s; user-select: none; }
    .form-check-label-custom:hover { background: rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.06); }
    .form-check-label-custom.checked { background: #059669; color: #fff; border-color: #059669; }
    .form-section-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--cinza0, #9aa6b3); margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.1); }
    .form-actions-bar { display: flex; justify-content: center; gap: 10px; padding: 20px 0 10px; }
    .input-group-inline { display: flex; align-items: center; gap: 8px; }
    .input-group-inline input, .input-group-inline select { flex: 1; }
    .input-group-inline .btn-inline { border-radius: 6px !important; border: 1px solid rgba(0,0,0,0.12); background: transparent; padding: 8px 14px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; white-space: nowrap; color: var(--title, #2d3748); }
    .input-group-inline .btn-inline:hover { background: var(--sidebar-accent, #0467fc); color: #fff; border-color: var(--sidebar-accent, #0467fc); }
    .input-group-inline .input-small { width: 5em !important; flex: none !important; }
    .input-group-inline select { width: 10em; flex: none; }
    .info-icon { color: var(--cinza0, #9aa6b3); cursor: help; font-size: 1.1rem; transition: color 0.2s; }
    .info-icon:hover { color: var(--sidebar-accent, #0467fc); }
    .error-alert { color: #e11d48; font-size: 0.8rem; margin-top: 4px; }
    body[data-theme="puredark"] .form-check-label-custom,
    body[data-theme="darkviolet"] .form-check-label-custom,
    body[data-theme="darkorange"] .form-check-label-custom { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .form-check-label-custom:hover,
    body[data-theme="darkviolet"] .form-check-label-custom:hover,
    body[data-theme="darkorange"] .form-check-label-custom:hover { background: rgba(255,255,255,0.06); }
    body[data-theme="puredark"] .form-section-title,
    body[data-theme="darkviolet"] .form-section-title,
    body[data-theme="darkorange"] .form-section-title { color: var(--cinza0, #9aa6b3); border-bottom-color: rgba(255,255,255,0.08); }
    body[data-theme="puredark"] .input-group-inline .btn-inline,
    body[data-theme="darkviolet"] .input-group-inline .btn-inline,
    body[data-theme="darkorange"] .input-group-inline .btn-inline { border-color: rgba(255,255,255,0.15); color: #e2e8f0; }
    body[data-theme="puredark"] .info-icon,
    body[data-theme="darkviolet"] .info-icon,
    body[data-theme="darkorange"] .info-icon { color: var(--dark-7, #a0aec0); }
    @media (max-width: 767px) { .input-group-inline { flex-wrap: wrap; } .form-actions-bar { flex-wrap: wrap; } }
</style>

<div class="row" style="margin-top:0">
    <div class="col-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-shopping-bag"></i></span>
                <h5>Editar Produto</h5>
            </div>
            <?php echo e($custom_error); ?>
            <form action="<?php echo current_url(); ?>" id="formProduto" method="post">
                <?php echo form_hidden('idProdutos', $result->idProdutos); ?>
                <div class="widget-content nopadding tab-content">
                    <div class="row" style="margin:0">
                        <div class="col-md-6" style="padding:0 15px">
                            <div class="form-section-title">Informações do Produto</div>
                            <div class="mb-3">
                                <label for="codDeBarra" class="form-label">Código de Barra</label>
                                <input id="codDeBarra" type="text" name="codDeBarra" value="<?php echo e($result->codDeBarra); ?>" placeholder="Código de barras">
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição <span class="required">*</span></label>
                                <input id="descricao" type="text" name="descricao" value="<?php echo e($result->descricao); ?>" placeholder="Nome ou descrição do produto">
                            </div>
                            <div class="mb-3">
                                <label for="precoCompra" class="form-label">Preço de Compra <span class="required">*</span></label>
                                <input id="precoCompra" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="precoCompra" value="<?php echo e($result->precoCompra); ?>" placeholder="R$ 0,00">
                                <span class="error-alert" id="errorAlert"></span>
                            </div>
                            <div class="mb-3">
                                <label for="Lucro" class="form-label">Lucro</label>
                                <div class="input-group-inline">
                                    <select id="selectLucro" name="selectLucro">
                                        <option value="markup">Markup</option>
                                        <option value="margemLucro">Margem de Lucro</option>
                                    </select>
                                    <input id="Lucro" name="Lucro" type="text" placeholder="%" maxlength="3" class="input-small">
                                    <i class="bx bx-info-circle info-icon tip-left" title="Markup: Porcentagem aplicada ao valor de compra | Margem de Lucro: Porcentagem aplicada ao valor de venda"></i>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="precoVenda" class="form-label">Preço de Venda <span class="required">*</span></label>
                                <div class="input-group-inline">
                                    <input id="precoVenda" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="precoVenda" value="<?php echo e($result->precoVenda); ?>" placeholder="R$ 0,00">
                                    <button type="button" class="btn-inline" onclick="zerarValorVenda()" title="Zerar Valor"><i class="bx bx-reset"></i> Zerar</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" style="padding:0 15px">
                            <div class="form-section-title">Estoque e Movimento</div>
                            <div class="mb-3">
                                <label for="unidade" class="form-label">Unidade <span class="required">*</span></label>
                                <select id="unidade" name="unidade"></select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="estoque" class="form-label">Estoque <span class="required">*</span></label>
                                    <input id="estoque" type="text" name="estoque" value="<?php echo e($result->estoque); ?>" placeholder="Quantidade">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estoqueMinimo" class="form-label">Estoque Mínimo</label>
                                    <input id="estoqueMinimo" type="text" name="estoqueMinimo" value="<?php echo e($result->estoqueMinimo); ?>" placeholder="Qtd mínima">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Movimento</label>
                                <div style="display:flex;gap:10px;flex-wrap:wrap">
                                    <label for="entrada" class="form-check-label-custom <?= ($result->entrada == 1) ? 'checked' : '' ?>">Entrada
                                        <input type="checkbox" id="entrada" name="entrada" class="badgebox" value="1" <?= ($result->entrada == 1) ? 'checked' : '' ?>>
                                        <span class="badge">&#10003;</span>
                                    </label>
                                    <label for="saida" class="form-check-label-custom <?= ($result->saida == 1) ? 'checked' : '' ?>">Saída
                                        <input type="checkbox" id="saida" name="saida" class="badgebox" value="1" <?= ($result->saida == 1) ? 'checked' : '' ?>>
                                        <span class="badge">&#10003;</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions-bar">
                    <button type="submit" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                    <a href="<?php echo base_url() ?>index.php/produtos" class="button btn btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    function calcLucro(precoCompra, Lucro) {
        var lucroTipo = $('#selectLucro').val();
        var precoVenda;
        if (lucroTipo === 'markup') {
            precoVenda = (precoCompra * (1 + Lucro / 100)).toFixed(2);
        } else if (lucroTipo === 'margemLucro') {
            precoVenda = (precoCompra / (1 - (Lucro / 100))).toFixed(2);
        }
        return precoVenda;
    }

    function atualizarPrecoVenda() {
        var precoCompra = Number($("#precoCompra").val());
        var lucro = Number($("#Lucro").val());
        if (precoCompra > 0 && lucro >= 0) {
            $('#precoVenda').val(calcLucro(precoCompra, lucro));
        }
    }

    $("#precoCompra, #Lucro, #selectLucro").on('input change', atualizarPrecoVenda);

    $("#precoCompra, #Lucro").on('input change', function() {
        if ($("#precoCompra").val() == '0.00' && $('#precoVenda').val() != '') {
            $('#errorAlert').text('Você não pode preencher valor de compra e depois apagar.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
            $("#precoCompra").focus();
        } else if ($("#precoCompra").val() != '' && $("#Lucro").val() != '') {
            atualizarPrecoVenda();
        }
    });

    $("#Lucro").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#precoCompra").val() == null || $("#precoCompra").val() == '') {
            $('#errorAlert').text('Preencher valor da compra primeiro.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
            $("#precoCompra").focus();
        } else if (Number($("#Lucro").val()) >= 0) {
            $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#Lucro").val())));
        } else {
            $('#errorAlert').text('Não é permitido número negativo.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
        }
    });

    $('#precoVenda').focusout(function() {
        if (Number($('#precoVenda').val()) < Number($("#precoCompra").val())) {
            $('#errorAlert').text('Preço de venda não pode ser menor que o preço de compra.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
            if ($("#margemLucro").val() != "" || $("#margemLucro").val() != null) {
                $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#margemLucro").val())));
            }
        }
    });

    function zerarValorVenda() {
        if (confirm('Deseja realmente zerar o valor de venda deste produto?')) {
            $('#precoVenda').val('0.00');
        }
    }

    $(document).ready(function() {
        $(".money").maskMoney();
        $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
            for (i in data.medidas) {
                $('#unidade').append(new Option(data.medidas[i].descricao, data.medidas[i].sigla));
            }
            $("#unidade option[value=" + <?php echo json_encode($result->unidade, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?> + "]").prop("selected", true);
        });

        // Toggle checkbox visual
        $('.form-check-label-custom input[type="checkbox"]').on('change', function() {
            $(this).closest('.form-check-label-custom').toggleClass('checked', this.checked);
        });
        // Init checked state
        $('.form-check-label-custom input[type="checkbox"]:checked').each(function() {
            $(this).closest('.form-check-label-custom').addClass('checked');
        });

        $('#formProduto').validate({
            rules: {
                descricao: { required: true },
                unidade: { required: true },
                precoCompra: { required: true },
                precoVenda: { required: true },
                estoque: { required: true }
            },
            messages: {
                descricao: { required: 'Campo Requerido.' },
                unidade: { required: 'Campo Requerido.' },
                precoCompra: { required: 'Campo Requerido.' },
                precoVenda: { required: 'Campo Requerido.' },
                estoque: { required: 'Campo Requerido.' }
            },
            errorClass: "form-text",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.mb-3').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest('.mb-3').removeClass('error').addClass('success');
            }
        });
    });
</script>