<script>
// =============================================
// Wizard NFS-e + Boleto
// =============================================

var wizardData = {
    currentStep: 1,
    totalSteps: 4,
    impostosResult: null,
    valorServicos: 0,
    valorDeducoes: 0,
    gerarBoleto: true,
    incluirProdutos: false,
    valorApenasServicos: <?= floatval($totalServico) ?>,
    valorTotalComProdutos: <?= floatval($totalServico + $totalProdutos) ?>
};

// Máscara monetária para campos de valor
function aplicarMascaraMoeda(input) {
    $(input).on('input', function() {
        var val = this.value.replace(/[^\d]/g, '');
        if (val.length === 0) { this.value = ''; return; }
        val = val.replace(/^0+/, '') || '0';
        while (val.length < 3) val = '0' + val;
        var inteiro = val.substring(0, val.length - 2);
        var decimal = val.substring(val.length - 2);
        this.value = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',' + decimal;
    });
}

function parseMoeda(str) {
    if (!str) return 0;
    return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
}

function formatarMoeda(valor) {
    return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Navegação do wizard
function wizardGoToStep(step) {
    if (step < 1 || step > wizardData.totalSteps) return;

    // Validação ao avançar
    if (step > wizardData.currentStep) {
        if (!validarStep(wizardData.currentStep)) return;
    }

    // Atualiza indicadores
    var steps = $('#wizard-steps li');
    steps.each(function() {
        var s = parseInt($(this).data('step'));
        $(this).removeClass('active completed');
        if (s < step) $(this).addClass('completed');
        if (s === step) $(this).addClass('active');
    });

    // Troca painel
    $('.wizard-step-panel').removeClass('active');
    $('#wizard-step-' + step).addClass('active');

    wizardData.currentStep = step;

    // Atualiza botões
    $('#btn-wizard-anterior').prop('disabled', step === 1);
    if (step === wizardData.totalSteps) {
        $('#btn-wizard-proximo').hide();
        $('#btn-wizard-emitir').show();
        atualizarResumo();
    } else {
        $('#btn-wizard-proximo').show();
        $('#btn-wizard-emitir').hide();
    }

    // Ao entrar no passo 2, calcular impostos
    if (step === 2) {
        calcularImpostosWizard();
    }

    // Ao entrar no passo 3, atualizar valor do boleto
    if (step === 3) {
        atualizarValorBoleto();
    }
}

function validarStep(step) {
    if (step === 1) {
        var valor = parseMoeda($('#valor-servicos-wizard').val());
        if (valor <= 0) {
            alert('Informe o valor dos serviços para continuar.');
            $('#valor-servicos-wizard').focus();
            return false;
        }
        var desc = $('#descricao-servico-wizard').val().trim();
        if (!desc) {
            alert('Informe a descrição do serviço.');
            $('#descricao-servico-wizard').focus();
            return false;
        }
        wizardData.valorServicos = valor;
        wizardData.valorDeducoes = parseMoeda($('#valor-deducoes-wizard').val());
        return true;
    }
    if (step === 2) {
        if (!wizardData.impostosResult) {
            alert('Aguarde o cálculo dos impostos.');
            return false;
        }
        return true;
    }
    if (step === 3) {
        wizardData.gerarBoleto = $('#gerar-boleto-wizard').is(':checked');
        if (wizardData.gerarBoleto) {
            var venc = $('#data-vencimento-wizard').val();
            if (!venc) {
                alert('Informe a data de vencimento do boleto.');
                return false;
            }
        }
        return true;
    }
    return true;
}

// Cálculo de impostos via AJAX (com debounce)
var calcularTimeout = null;

function calcularImpostosWizard() {
    var valor = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
    if (valor <= 0) return;

    // Mostra loading
    $('#impostos-table td.imposto-valor').text('...');
    $('#imp-valor-liquido').text('...');

    clearTimeout(calcularTimeout);
    calcularTimeout = setTimeout(function() {
        $.ajax({
            url: '<?= site_url("nfse_os/calcular_impostos") ?>',
            type: 'POST',
            data: { valor: valor },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    wizardData.impostosResult = data;
                    var imp = data.impostos;
                    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());
                    var baseCalc = data.valor_bruto - deducoes;

                    // Atualiza tabela de impostos
                    $('#imp-valor-bruto').text(formatarMoeda(data.valor_bruto));
                    $('#imp-deducoes').text(formatarMoeda(deducoes));
                    $('#imp-base-calculo').text(formatarMoeda(baseCalc));
                    $('#imp-iss').text(formatarMoeda(imp.iss || imp.iss_valor || 0));
                    $('#imp-pis').text(formatarMoeda(imp.pis || imp.pis_valor || 0));
                    $('#imp-cofins').text(formatarMoeda(imp.cofins || imp.cofins_valor || 0));
                    $('#imp-irrf').text(formatarMoeda(imp.irrf || imp.irpj_valor || 0));
                    $('#imp-csll').text(formatarMoeda(imp.csll || imp.csll_valor || 0));
                    $('#imp-inss').text(formatarMoeda(imp.inss || imp.cpp_valor || 0));
                    $('#imp-total-impostos').text(formatarMoeda(imp.valor_total_impostos || 0));
                    $('#imp-valor-liquido').text(formatarMoeda(data.valor_liquido - deducoes));
                } else {
                    var msg = data.message || 'Erro ao calcular impostos';
                    $('#impostos-table td.imposto-valor').text('—');
                    $('#imp-valor-liquido').html('<span style="color:#dc3545">' + msg + '</span>');
                }
            },
            error: function(xhr, status, error) {
                console.error('NFSe calcular_impostos AJAX error:', xhr.status, status, error);
                console.error('Response:', xhr.responseText ? xhr.responseText.substring(0, 500) : '(empty)');
                var msg = 'Erro na comunicação com o servidor.';
                if (xhr.status === 0) {
                    msg = 'Sem conexão com o servidor. Verifique sua rede.';
                } else if (xhr.status === 403) {
                    msg = 'Acesso negado (CSRF). Recarregue a página e tente novamente.';
                } else if (xhr.status === 500) {
                    msg = 'Erro interno do servidor (500).';
                } else {
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        if (resp.message) msg = resp.message;
                        else if (resp.error) msg = resp.error;
                    } catch(e) {
                        if (xhr.responseText) {
                            msg = msg + ' (HTTP ' + xhr.status + ')';
                        }
                    }
                }
                $('#impostos-table td.imposto-valor').text('—');
                $('#imp-valor-liquido').html('<span style="color:#dc3545">' + msg + '</span>');
            }
        });
    }, 300);
}

// Atualizar valor do boleto no passo 3
function atualizarValorBoleto() {
    if (wizardData.impostosResult) {
        var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());
        var liquido = wizardData.impostosResult.valor_liquido - deducoes;
        $('#valor-boleto-wizard').val(liquido.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }
}

// Atualizar resumo no passo 4
function atualizarResumo() {
    var valor = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());

    $('#res-valor-servicos').text(formatarMoeda(valor));
    $('#res-deducoes').text(formatarMoeda(deducoes));

    if (wizardData.impostosResult) {
        var imp = wizardData.impostosResult.impostos;
        var liquido = wizardData.impostosResult.valor_liquido - deducoes;
        $('#res-total-impostos').text(formatarMoeda(imp.valor_total_impostos || 0));
        $('#res-valor-liquido').text(formatarMoeda(liquido));
    }

    // Resumo do boleto
    if (wizardData.gerarBoleto) {
        var venc = $('#data-vencimento-wizard').val();
        var vencFormatado = venc ? venc.split('-').reverse().join('/') : '—';
        var liquidoBoleto = wizardData.impostosResult ? (wizardData.impostosResult.valor_liquido - deducoes) : 0;
        $('#res-boleto-info').html(
            '<strong>Gerar boleto:</strong> Sim<br>' +
            '<strong>Valor:</strong> ' + formatarMoeda(liquidoBoleto) + '<br>' +
            '<strong>Vencimento:</strong> ' + vencFormatado
        );
    } else {
        $('#res-boleto-info').html('<strong>Gerar boleto:</strong> Não');
    }
}

// Emitir NFS-e via AJAX
function emitirNFSeWizard() {
    var valor = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());
    var descricao = $('#descricao-servico-wizard').val();
    var gerarBoleto = wizardData.gerarBoleto ? 1 : 0;

    // Confirmação final
    var msg = 'Deseja confirmar a emissão da NFS-e?\n\n';
    msg += 'Valor dos Serviços: ' + formatarMoeda(valor) + '\n';
    if (deducoes > 0) msg += 'Deduções: ' + formatarMoeda(deducoes) + '\n';
    if (wizardData.impostosResult) {
        var imp = wizardData.impostosResult.impostos;
        var liquido = wizardData.impostosResult.valor_liquido - deducoes;
        msg += 'Total Impostos: ' + formatarMoeda(imp.valor_total_impostos || 0) + '\n';
        msg += 'Valor Líquido: ' + formatarMoeda(liquido) + '\n';
    }
    if (gerarBoleto) {
        msg += '\nBoleto será gerado automaticamente.';
    }

    if (!confirm(msg)) return;

    // Desabilita botão
    var btnEmitir = $('#btn-wizard-emitir');
    btnEmitir.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Emitindo...');

    // Emitir NFS-e
    $.ajax({
        url: '<?= site_url("nfse_os/emitir/" . $result->idOs) ?>',
        type: 'POST',
        data: {
            valor_servicos: valor,
            valor_deducoes: deducoes,
            descricao_servico: descricao,
            gerar_boleto: 0
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var nfseId = response.nfse_id || response.id;

                if (gerarBoleto && nfseId) {
                    // Gerar boleto encadeado
                    btnEmitir.html('<i class="fas fa-spinner fa-spin"></i> Gerando boleto...');
                    $.ajax({
                        url: '<?= site_url("nfse_os/gerar_boleto/" . $result->idOs) ?>/' + nfseId,
                        type: 'POST',
                        data: {
                            data_vencimento: $('#data-vencimento-wizard').val(),
                            instrucoes: $('#instrucoes-boleto-wizard').val()
                        },
                        dataType: 'json',
                        success: function(respBoleto) {
                            alert('NFS-e e Boleto emitidos com sucesso!');
                            location.reload();
                        },
                        error: function() {
                            alert('NFS-e emitida com sucesso! Erro ao gerar boleto. Tente gerar separadamente.');
                            location.reload();
                        }
                    });
                } else {
                    alert('NFS-e emitida com sucesso!');
                    location.reload();
                }
            } else {
                alert('Erro ao emitir NFS-e: ' + (response.message || response.error || 'Erro desconhecido'));
                btnEmitir.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Confirmar & Emitir NFS-e');
            }
        },
        error: function(xhr) {
            var msg = 'Erro na comunicação com o servidor.';
            try {
                var resp = JSON.parse(xhr.responseText);
                msg = resp.message || resp.error || msg;
            } catch(e) {}
            alert(msg);
            btnEmitir.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Confirmar & Emitir NFS-e');
        }
    });
}

// Toggle boleto no passo 3
function toggleBoleto() {
    var checked = $('#gerar-boleto-wizard').is(':checked');
    wizardData.gerarBoleto = checked;
    if (checked) {
        $('#boleto-campos').show();
        $('#sem-boleto-msg').hide();
    } else {
        $('#boleto-campos').hide();
        $('#sem-boleto-msg').show();
    }
}

// =============================================
// Funções existentes (mantidas)
// =============================================

function cancelarNFSe(nfseId) {
    var motivo = prompt('Informe o motivo do cancelamento:');
    if (motivo) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/cancelar_nfse/") ?>' + nfseId;
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'motivo';
        input.value = motivo;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function cancelarBoleto(boletoId) {
    if (confirm('Tem certeza que deseja cancelar este boleto?')) {
        window.location.href = '<?= site_url("nfse_os/cancelar_boleto/") ?>' + boletoId;
    }
}

function registrarPagamento(boletoId) {
    var data = prompt('Data do pagamento (YYYY-MM-DD):', '<?= date("Y-m-d") ?>');
    if (data) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/registrar_pagamento/") ?>' + boletoId;
        var inputData = document.createElement('input');
        inputData.type = 'hidden';
        inputData.name = 'data_pagamento';
        inputData.value = data;
        form.appendChild(inputData);
        document.body.appendChild(form);
        form.submit();
    }
}

function copiarLinhaDigitavel() {
    var input = document.getElementById('linha-digitavel');
    if (input) {
        input.select();
        document.execCommand('copy');
        alert('Linha digitável copiada!');
    }
}

// =============================================
// Inicialização
// =============================================

$(document).ready(function() {
    // Máscara monetária
    aplicarMascaraMoeda('#valor-servicos-wizard');
    aplicarMascaraMoeda('#valor-deducoes-wizard');

    // Recalcular ao mudar valores no passo 1
    $('#valor-servicos-wizard, #valor-deducoes-wizard').on('change', function() {
        wizardData.valorServicos = parseMoeda($('#valor-servicos-wizard').val());
        wizardData.valorDeducoes = parseMoeda($('#valor-deducoes-wizard').val());
        wizardData.impostosResult = null; // Invalida cache
    });

    // Pré-visualização em PDF (abre em nova aba)
    $('#btn-preview-nfse').click(function() {
        var valor = parseMoeda($('#valor-servicos-wizard').val());
        var deducoes = parseMoeda($('#valor-deducoes-wizard').val());
        var descricao = $('#descricao-servico-wizard').val();
        var url = '<?php echo site_url("nfse_os/preview/" . $result->idOs); ?>';
        url += '?valor_servicos=' + encodeURIComponent(valor);
        url += '&valor_deducoes=' + encodeURIComponent(deducoes);
        url += '&descricao_servico=' + encodeURIComponent(descricao);
        window.open(url, '_blank');
    });

    // Navegação
    $('#btn-wizard-proximo').click(function() {
        wizardGoToStep(wizardData.currentStep + 1);
    });

    $('#btn-wizard-anterior').click(function() {
        wizardGoToStep(wizardData.currentStep - 1);
    });

    $('#btn-wizard-emitir').click(function() {
        emitirNFSeWizard();
    });

    // Toggle boleto
    $('#gerar-boleto-wizard').change(function() {
        toggleBoleto();
    });

    // Checkbox para incluir produtos na NFSe
    $('#incluir-produtos-nfse').on('click', function(e) {
        var checkbox = $(this);
        // Se já está marcado, permite desmarcar normalmente
        if (checkbox.prop('checked') && !checkbox.prop('disabled')) {
            return true;
        }
        // Tentando marcar — requer confirmação
        e.preventDefault();
        e.stopPropagation();
        $('#modal-confirmar-produtos').modal('show');
        $('#input-confirmar-produtos').val('').focus();
        $('#msg-erro-confirmar').hide();
    });

    // Confirmar inclusão de produtos
    $('#btn-confirmar-produtos').on('click', function() {
        var texto = $('#input-confirmar-produtos').val().trim().toLowerCase();
        if (texto !== 'confirmar') {
            $('#msg-erro-confirmar').text('Digite "confirmar" para prosseguir.').show();
            return;
        }
        // Confirmado — marcar checkbox e atualizar valor
        $('#incluir-produtos-nfse').prop('checked', true).prop('disabled', false);
        wizardData.incluirProdutos = true;
        var novoValor = wizardData.valorTotalComProdutos;
        var desconto = parseFloat('<?= floatval($result->valor_desconto ?? 0) ?>') || 0;
        var valorNFSe = desconto > 0 ? desconto : novoValor;
        $('#valor-servicos-wizard').val(valorNFSe.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        wizardData.valorServicos = valorNFSe;
        wizardData.impostosResult = null;
        $('#valor-servicos-help').text('Valor total (serviços + produtos) para a NFS-e');
        $('#modal-confirmar-produtos').modal('hide');
    });

    // Desmarcar checkbox — voltar ao valor de serviços apenas
    $('#incluir-produtos-nfse').on('change', function() {
        if (!$(this).is(':checked')) {
            wizardData.incluirProdutos = false;
            var novoValor = wizardData.valorApenasServicos;
            var desconto = parseFloat('<?= floatval($result->valor_desconto ?? 0) ?>') || 0;
            var valorNFSe = desconto > 0 ? desconto : novoValor;
            $('#valor-servicos-wizard').val(valorNFSe.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
            wizardData.valorServicos = valorNFSe;
            wizardData.impostosResult = null;
            $('#valor-servicos-help').text('Valor dos serviços prestados (produtos não inclusos)');
        }
    });
});
</script>