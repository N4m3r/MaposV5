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
    valorTotalComProdutos: <?= floatval($totalServico + $totalProdutos) ?>,
    regimeTributario: '<?= $regimeTributario ?? "simples_nacional" ?>',
    retemIss: false,
    retemIrrf: false,
    retemPis: false,
    retemCofins: false,
    retemCsll: false,
    retencoes: null,
    valorDas: null
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
    if ($('#imp-das').length) $('#imp-das').text('...');
    if ($('#imp-das-aliquota').length) $('#imp-das-aliquota').text('...');

    clearTimeout(calcularTimeout);
    calcularTimeout = setTimeout(function() {
        // Montar dados de retenção
        var postData = { valor: valor };
        if ($('#retem-iss').is(':checked')) postData.retem_iss = 1;
        if ($('#retem-irrf').is(':checked')) postData.retem_irrf = 1;
        if ($('#retem-pis').is(':checked')) postData.retem_pis = 1;
        if ($('#retem-cofins').is(':checked')) postData.retem_cofins = 1;
        if ($('#retem-csll').is(':checked')) postData.retem_csll = 1;
        Object.assign(postData, getCsrfToken());

        $.ajax({
            url: '<?= site_url("nfse_os/calcular_impostos") ?>',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(data) {
                updateCsrfToken(data);
                if (data.success) {
                    wizardData.impostosResult = data;
                    wizardData.regimeTributario = data.regime_tributario || wizardData.regimeTributario;
                    wizardData.valorDas = data.valor_das || null;
                    wizardData.retencoes = data.retencoes || null;

                    var imp = data.impostos;
                    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());
                    var baseCalc = data.valor_bruto - deducoes;

                    // Atualiza tabela de impostos
                    $('#imp-valor-bruto').text(formatarMoeda(data.valor_bruto));
                    $('#imp-deducoes').text(formatarMoeda(deducoes));
                    $('#imp-base-calculo').text(formatarMoeda(baseCalc));

                    // Regime-specific display
                    if (wizardData.regimeTributario === 'simples_nacional') {
                        // Simples Nacional: show DAS instead of individual taxes
                        if ($('#imp-das').length) {
                            $('#imp-das').text(formatarMoeda(wizardData.valorDas || data.valor_bruto * 0.06));
                        }
                        if ($('#imp-das-aliquota').length) {
                            var aliquota = wizardData.valorDas ? ((wizardData.valorDas / data.valor_bruto) * 100).toFixed(2) : '6.00';
                            $('#imp-das-aliquota').text(aliquota + '%');
                        }
                    } else {
                        // Lucro Presumido: show individual taxes
                        if ($('#imp-iss').length) $('#imp-iss').text(formatarMoeda(imp.iss || imp.iss_valor || 0));
                        if ($('#imp-pis').length) $('#imp-pis').text(formatarMoeda(imp.pis || imp.pis_valor || 0));
                        if ($('#imp-cofins').length) $('#imp-cofins').text(formatarMoeda(imp.cofins || imp.cofins_valor || 0));
                        if ($('#imp-irrf').length) $('#imp-irrf').text(formatarMoeda(imp.irrf || imp.irpj_valor || 0));
                        if ($('#imp-csll').length) $('#imp-csll').text(formatarMoeda(imp.csll || imp.csll_valor || 0));
                        if ($('#imp-inss').length) $('#imp-inss').text(formatarMoeda(imp.inss || imp.cpp_valor || 0));
                    }

                    var totalImpostos = imp.valor_total_impostos || 0;
                    $('#imp-total-impostos').text(formatarMoeda(totalImpostos));

                    // Retenções do Tomador
                    var totalRetencao = 0;
                    if (wizardData.retencoes) {
                        totalRetencao = parseFloat(wizardData.retencoes.valor_total_retencao || 0);
                    }
                    if (totalRetencao > 0) {
                        $('#retencao-row').show();
                        $('#imp-retencao-total').text(formatarMoeda(totalRetencao));
                    } else {
                        $('#retencao-row').hide();
                    }

                    // Valor líquido da NFS-e: bruto - deduções - retenções (impostos do prestador não reduzem)
                    var valorLiquido = data.valor_liquido;
                    $('#imp-valor-liquido').text(formatarMoeda(valorLiquido));

                    // Atualizar DAS display no Step 1
                    if (wizardData.regimeTributario === 'simples_nacional' && wizardData.valorDas) {
                        $('#das-valor-display').text(formatarMoeda(wizardData.valorDas));
                    }

                    // Atualizar resumo de retenções no Step 1
                    atualizarRetencoesStep1(valor);
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

// Atualizar valores de retenção no Step 1
function atualizarRetencoesStep1(valorBruto) {
    var aliquotaIss = parseFloat('<?= $tributacao["aliquota_iss"] ?? "5.00" ?>');
    var retencaoIss = $('#retem-iss').is(':checked') ? Math.round(valorBruto * aliquotaIss / 100 * 100) / 100 : 0;
    var retencaoIrrf = $('#retem-irrf').is(':checked') ? Math.round(valorBruto * 1.5 / 100 * 100) / 100 : 0;
    var retencaoPis = $('#retem-pis').is(':checked') ? Math.round(valorBruto * 0.65 / 100 * 100) / 100 : 0;
    var retencaoCofins = $('#retem-cofins').is(':checked') ? Math.round(valorBruto * 3.0 / 100 * 100) / 100 : 0;
    var retencaoCsll = $('#retem-csll').is(':checked') ? Math.round(valorBruto * 1.0 / 100 * 100) / 100 : 0;
    var totalRetencao = retencaoIss + retencaoIrrf + retencaoPis + retencaoCofins + retencaoCsll;

    wizardData.retemIss = $('#retem-iss').is(':checked');
    wizardData.retemIrrf = $('#retem-irrf').is(':checked');
    wizardData.retemPis = $('#retem-pis').is(':checked');
    wizardData.retemCofins = $('#retem-cofins').is(':checked');
    wizardData.retemCsll = $('#retem-csll').is(':checked');

    // Atualizar labels
    $('#retem-iss-valor').text(retencaoIss > 0 ? formatarMoeda(retencaoIss) : '');
    $('#retem-irrf-valor').text(retencaoIrrf > 0 ? formatarMoeda(retencaoIrrf) : '');
    $('#retem-pis-valor').text(retencaoPis > 0 ? formatarMoeda(retencaoPis) : '');
    $('#retem-cofins-valor').text(retencaoCofins > 0 ? formatarMoeda(retencaoCofins) : '');
    $('#retem-csll-valor').text(retencaoCsll > 0 ? formatarMoeda(retencaoCsll) : '');
    $('#retem-total-valor').text(formatarMoeda(totalRetencao));

    // Invalidar cache de impostos se retenções mudaram
    wizardData.impostosResult = null;
}

// Atualizar valor do boleto no passo 3
function atualizarValorBoleto() {
    if (wizardData.impostosResult) {
        var valorBruto = wizardData.impostosResult.valor_bruto;
        var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());

        // Verificar retenções do tomador
        var totalRetencao = 0;
        if (wizardData.retencoes && wizardData.retencoes.valor_total_retencao) {
            totalRetencao = parseFloat(wizardData.retencoes.valor_total_retencao);
        }

        // Mostrar/esconder seção de valor integral (só quando há retenção)
        if (totalRetencao > 0) {
            $('#valor-integral-section').show();
        } else {
            $('#valor-integral-section').hide();
            $('#valor-integral-wizard').prop('checked', false);
        }

        // Padrão: boleto com valor integral (serviços - deduções)
        var valorBoleto = valorBruto - deducoes;

        // Se houver retenção e usuário NÃO marcou valor integral, descontar retenções
        if (totalRetencao > 0 && !$('#valor-integral-wizard').is(':checked')) {
            valorBoleto = valorBruto - deducoes - totalRetencao;
            $('#valor-boleto-ajuda').html('Valor líquido = Serviços - Deduções - Retenções');
        } else {
            $('#valor-boleto-ajuda').html('Valor integral = Serviços - Deduções (impostos/DAS não descontam)');
        }

        $('#valor-boleto-wizard').val(valorBoleto.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }
}

// Atualizar resumo no passo 4
function atualizarResumo() {
    var valor = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
    var deducoes = wizardData.valorDeducoes || parseMoeda($('#valor-deducoes-wizard').val());

    $('#res-valor-servicos').text(formatarMoeda(valor));
    $('#res-deducoes').text(formatarMoeda(deducoes));

    var totalRetencao = 0;
    if (wizardData.retencoes && wizardData.retencoes.valor_total_retencao) {
        totalRetencao = parseFloat(wizardData.retencoes.valor_total_retencao);
    }

    if (wizardData.impostosResult) {
        var imp = wizardData.impostosResult.impostos;
        var totalImpostos = imp.valor_total_impostos || 0;

        // Valor líquido da NFS-e: bruto - deduções - retenções (impostos do prestador não reduzem)
        var liquido = wizardData.impostosResult.valor_bruto - deducoes - totalRetencao;

        // Show DAS line for Simples Nacional
        if (wizardData.regimeTributario === 'simples_nacional') {
            $('#res-das-linha').show();
            $('#res-imposto-linha').html('<strong>DAS Estimado (Simples Nacional):</strong> ');
            $('#res-total-impostos').text(formatarMoeda(totalImpostos));
            if (wizardData.valorDas) {
                $('#res-valor-das').text(formatarMoeda(wizardData.valorDas));
            }
        } else {
            $('#res-das-linha').hide();
            $('#res-imposto-linha').html('<strong>Total Impostos:</strong> ');
            $('#res-total-impostos').text(formatarMoeda(totalImpostos));
        }

        // Show retentions line if any
        if (totalRetencao > 0) {
            $('#res-retencao-linha').show();
            $('#res-retencao-total').text(formatarMoeda(totalRetencao));
        } else {
            $('#res-retencao-linha').hide();
        }

        $('#res-valor-liquido').text(formatarMoeda(liquido));
    }

    // Resumo do boleto
    if (wizardData.gerarBoleto) {
        var venc = $('#data-vencimento-wizard').val();
        var vencFormatado = venc ? venc.split('-').reverse().join('/') : '—';
        var instrucoes = $('#instrucoes-boleto-wizard').val() || '—';
        var isValorIntegral = $('#valor-integral-wizard').is(':checked');
        var valorBruto = wizardData.impostosResult ? wizardData.impostosResult.valor_bruto : 0;

        // Padrão: boleto com valor integral (bruto - deduções)
        var valorBoleto = valorBruto - deducoes;

        // Se houver retenção e NÃO marcou valor integral, descontar retenções
        if (totalRetencao > 0 && !isValorIntegral) {
            valorBoleto = valorBruto - deducoes - totalRetencao;
        }

        var boletoHtml = '<strong>Gerar boleto:</strong> Sim<br>';
        if (totalRetencao > 0 && isValorIntegral) {
            boletoHtml += '<strong style="color: #e67e22;">Valor Integral:</strong> ' + formatarMoeda(valorBoleto) + '<br>';
            boletoHtml += '<small style="color: #888;">(Retenções pelo tomador não descontadas do boleto)</small><br>';
        } else if (totalRetencao > 0) {
            boletoHtml += '<strong>Valor Líquido:</strong> ' + formatarMoeda(valorBoleto) + '<br>';
            boletoHtml += '<small style="color: #888;">(Retenções pelo tomador descontadas do boleto)</small><br>';
        } else {
            boletoHtml += '<strong>Valor:</strong> ' + formatarMoeda(valorBoleto) + '<br>';
        }
        if (totalRetencao > 0) {
            boletoHtml += '<strong style="color: #e67e22;">Retenções Tomador:</strong> ' + formatarMoeda(totalRetencao) + '<br>';
        }
        boletoHtml += '<strong>Vencimento:</strong> ' + vencFormatado + '<br>' +
            '<strong>Instruções:</strong> ' + instrucoes;
        $('#res-boleto-info').html(boletoHtml);
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
    var regimeLabel = wizardData.regimeTributario === 'simples_nacional' ? 'Simples Nacional (DAS)' : 'Lucro Presumido';
    var msg = 'Deseja confirmar a emissão da NFS-e?\n\n';
    msg += 'Regime Tributário: ' + regimeLabel + '\n';
    msg += 'Valor dos Serviços: ' + formatarMoeda(valor) + '\n';
    if (deducoes > 0) msg += 'Deduções: ' + formatarMoeda(deducoes) + '\n';
    if (wizardData.impostosResult) {
        var imp = wizardData.impostosResult.impostos;
        var totalRetencao = 0;
        if (wizardData.retencoes && wizardData.retencoes.valor_total_retencao) {
            totalRetencao = parseFloat(wizardData.retencoes.valor_total_retencao);
        }
        var liquido = wizardData.impostosResult.valor_bruto - deducoes - totalRetencao;
        if (wizardData.regimeTributario === 'simples_nacional' && wizardData.valorDas) {
            msg += 'DAS Estimado (Simples Nacional): ' + formatarMoeda(wizardData.valorDas) + '\n';
        } else {
            msg += 'Total Impostos: ' + formatarMoeda(imp.valor_total_impostos || 0) + '\n';
        }
        msg += 'Valor Líquido NFS-e: ' + formatarMoeda(liquido) + '\n';
        // Retenções
        var totalRetencao = 0;
        if (wizardData.retencoes && wizardData.retencoes.valor_total_retencao) {
            totalRetencao = parseFloat(wizardData.retencoes.valor_total_retencao);
            msg += 'Retenções Tomador: ' + formatarMoeda(totalRetencao) + '\n';
        }
    }
    if (gerarBoleto) {
        msg += '\nBoleto será gerado automaticamente.';
    }

    if (!confirm(msg)) return;

    // Desabilita botão
    var btnEmitir = $('#btn-wizard-emitir');
    btnEmitir.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Emitindo...');

    // Montar dados de retenção
    var emitData = {
        valor_servicos: valor,
        valor_deducoes: deducoes,
        descricao_servico: descricao,
        gerar_boleto: 0,
        regime_tributario: wizardData.regimeTributario,
        competencia: new Date().toISOString().slice(0, 7) + '-01'
    };

    // DAS (se Simples Nacional)
    if (wizardData.regimeTributario === 'simples_nacional' && wizardData.valorDas) {
        emitData.valor_das = wizardData.valorDas;
    }

    // Retenções do Tomador
    if ($('#retem-iss').is(':checked')) {
        emitData.retem_iss = 1;
        emitData.valor_retencao_iss = wizardData.retencoes ? (wizardData.retencoes.valor_retencao_iss || 0) : 0;
    }
    if ($('#retem-irrf').is(':checked')) {
        emitData.retem_irrf = 1;
        emitData.valor_retencao_irrf = wizardData.retencoes ? (wizardData.retencoes.valor_retencao_irrf || 0) : 0;
    }
    if ($('#retem-pis').is(':checked')) {
        emitData.retem_pis = 1;
        emitData.valor_retencao_pis = wizardData.retencoes ? (wizardData.retencoes.valor_retencao_pis || 0) : 0;
    }
    if ($('#retem-cofins').is(':checked')) {
        emitData.retem_cofins = 1;
        emitData.valor_retencao_cofins = wizardData.retencoes ? (wizardData.retencoes.valor_retencao_cofins || 0) : 0;
    }
    if ($('#retem-csll').is(':checked')) {
        emitData.retem_csll = 1;
        emitData.valor_retencao_csll = wizardData.retencoes ? (wizardData.retencoes.valor_retencao_csll || 0) : 0;
    }

    // Total retenções
    if (wizardData.retencoes && wizardData.retencoes.valor_total_retencao) {
        emitData.valor_total_retencao = wizardData.retencoes.valor_total_retencao;
    }

    Object.assign(emitData, getCsrfToken());

    // Emitir NFS-e via API Nacional
    $.ajax({
        url: '<?= site_url("nfse_os/emitir_nfse_api/" . $result->idOs) ?>',
        type: 'POST',
        data: emitData,
        dataType: 'json',
        success: function(response) {
            updateCsrfToken(response);
            if (response.success) {
                var nfseId = response.nfse_id || response.id;
                var ambiente = response.ambiente || 'homologacao';
                var msg = 'NFS-e emitida com sucesso via API Nacional!\n\n';
                if (response.chave_acesso) msg += 'Chave de Acesso: ' + response.chave_acesso + '\n';
                if (response.numero) msg += 'Número: ' + response.numero + '\n';
                if (response.protocolo) msg += 'Protocolo: ' + response.protocolo + '\n';
                msg += 'Ambiente: ' + (ambiente === 'producao' ? 'Produção' : 'Homologação') + '\n';
                if (response.url_danfe) msg += '\nDANFSe: ' + response.url_danfe;

                if (gerarBoleto && nfseId) {
                    // Gerar boleto encadeado
                    btnEmitir.html('<i class="fas fa-spinner fa-spin"></i> Gerando boleto...');
                    var boletoData = {
                        data_vencimento: $('#data-vencimento-wizard').val(),
                        instrucoes: $('#instrucoes-boleto-wizard').val(),
                        valor_integral: $('#valor-integral-wizard').is(':checked') ? 1 : 0
                    };
                    Object.assign(boletoData, getCsrfToken());

                    $.ajax({
                        url: '<?= site_url("nfse_os/gerar_boleto/" . $result->idOs) ?>/' + nfseId,
                        type: 'POST',
                        data: boletoData,
                        dataType: 'json',
                        success: function(respBoleto) {
                            alert(msg + '\n\nBoleto gerado com sucesso!');
                            location.reload();
                        },
                        error: function() {
                            alert(msg + '\n\nNFS-e emitida! Erro ao gerar boleto. Tente gerar separadamente.');
                            location.reload();
                        }
                    });
                } else {
                    alert(msg);
                    location.reload();
                }
            } else {
                alert('Erro ao emitir NFS-e: ' + (response.message || response.error || 'Erro desconhecido'));
                btnEmitir.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Emitir NFS-e (API Nacional)');
            }
        },
        error: function(xhr) {
            var msg = 'Erro na comunicação com o servidor.';
            try {
                var resp = JSON.parse(xhr.responseText);
                msg = resp.message || resp.error || msg;
            } catch(e) {}
            alert(msg);
            btnEmitir.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Emitir NFS-e (API Nacional)');
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

function addCsrfTokenToForm(form) {
    var csrfName = '<?= config_item("csrf_token_name") ?>';
    var csrfValue = getCookie('<?= config_item("csrf_cookie_name") ?>');
    if (csrfName && csrfValue) {
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = csrfName;
        csrfInput.value = csrfValue;
        form.appendChild(csrfInput);
    }
}

function getCsrfToken() {
    var csrfName = '<?= config_item("csrf_token_name") ?>';
    var csrfValue = getCookie('<?= config_item("csrf_cookie_name") ?>');
    if (csrfName && csrfValue) {
        var token = {};
        token[csrfName] = csrfValue;
        return token;
    }
    return {};
}

function updateCsrfToken(data) {
    var csrfName = '<?= config_item("csrf_token_name") ?>';
    if (data && data[csrfName]) {
        var csrfCookieName = '<?= config_item("csrf_cookie_name") ?>';
        var domain = window.location.hostname;
        var path = '/';
        document.cookie = csrfCookieName + '=' + encodeURIComponent(data[csrfName]) + '; path=' + path + '; domain=' + domain + ';';
    }
}

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
        addCsrfTokenToForm(form);
        document.body.appendChild(form);
        form.submit();
    }
}

// Cancelar NFS-e via API Nacional
function cancelarNFSeNacional(nfseId) {
    var motivo = prompt('Informe o motivo do cancelamento (mín. 15 caracteres):');
    if (!motivo) return;
    if (motivo.length < 15) {
        alert('O motivo do cancelamento deve ter pelo menos 15 caracteres (requisito da API Nacional).');
        return;
    }
    if (!confirm('Confirma o cancelamento desta NFS-e na API Nacional?\n\nEsta ação é irreversível.')) return;

    var btn = event.target.closest('button');
    if (btn) btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cancelando...');

    var cancelData = { motivo: motivo };
    Object.assign(cancelData, getCsrfToken());

    $.ajax({
        url: '<?= site_url("nfse_os/cancelar_nfse_api/") ?>' + nfseId,
        type: 'POST',
        data: cancelData,
        dataType: 'json',
        success: function(response) {
            updateCsrfToken(response);
            if (response.success) {
                alert('NFS-e cancelada com sucesso na API Nacional!' +
                    (response.protocolo ? '\nProtocolo: ' + response.protocolo : ''));
                location.reload();
            } else {
                alert('Erro ao cancelar NFS-e: ' + (response.message || 'Erro desconhecido'));
                if (btn) btn.prop('disabled', false).html('<i class="fas fa-times"></i> Cancelar (Nacional)');
            }
        },
        error: function(xhr) {
            var msg = 'Erro na comunicação com o servidor.';
            try {
                var resp = JSON.parse(xhr.responseText);
                msg = resp.message || resp.error || msg;
            } catch(e) {}
            alert(msg);
            if (btn) btn.prop('disabled', false).html('<i class="fas fa-times"></i> Cancelar (Nacional)');
        }
    });
}

// Consultar NFS-e na API Nacional
function consultarNFSeNacional(nfseId) {
    var resultDiv = $('#nfse-consulta-resultado');
    var contentDiv = $('#nfse-consulta-conteudo');

    resultDiv.show();
    contentDiv.html('<i class="fas fa-spinner fa-spin"></i> Consultando NFS-e na API Nacional...');

    $.ajax({
        url: '<?= site_url("nfse_os/consultar_nfse/") ?>' + nfseId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data || {};
                var html = '<strong>Consulta Realizada com Sucesso</strong><br>';
                html += '<strong>Situação:</strong> ' + (data.situacaoNfse || data.situacao || '---') + '<br>';
                html += '<strong>Chave de Acesso:</strong> ' + (data.chaveAcesso || response.chave_acesso || '---') + '<br>';
                if (data.numero) html += '<strong>Número:</strong> ' + data.numero + '<br>';
                if (data.dataHoraEmissao) html += '<strong>Data/Hora:</strong> ' + data.dataHoraEmissao + '<br>';
                contentDiv.html(html).removeClass('alert-info').addClass('alert-success');
            } else {
                contentDiv.html('<strong>Erro na consulta:</strong> ' + (response.message || 'Erro desconhecido'))
                    .removeClass('alert-info').addClass('alert-danger');
            }
        },
        error: function(xhr) {
            contentDiv.html('<strong>Erro na comunicação com o servidor.</strong>')
                .removeClass('alert-info').addClass('alert-danger');
        }
    });
}

function cancelarBoleto(boletoId) {
    if (confirm('Tem certeza que deseja cancelar este boleto?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/cancelar_boleto/") ?>' + boletoId;
        addCsrfTokenToForm(form);
        document.body.appendChild(form);
        form.submit();
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
        addCsrfTokenToForm(form);
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
        if (checkbox.prop('checked')) {
            return true;
        }
        // Tentando marcar — requer confirmação via modal
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
        $('#incluir-produtos-nfse').prop('checked', true);
        wizardData.incluirProdutos = true;
        var novoValor = wizardData.valorTotalComProdutos;
        var desconto = parseFloat('<?= floatval($result->valor_desconto ?? 0) ?>') || 0;
        var valorNFSe = desconto > 0 ? desconto : novoValor;
        $('#valor-servicos-wizard').val(valorNFSe.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        wizardData.valorServicos = valorNFSe;
        wizardData.impostosResult = null;
        $('#valor-servicos-help').text('Valor total (serviços + produtos) para a NFS-e');
        // Atualizar painel visual
        $('#nfse-valor-servicos').hide();
        $('#nfse-valor-total').show();
        $('#painel-incluir-produtos').css('border-color', '#27ae60').css('background', '#eafaf1');
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
            // Atualizar painel visual
            $('#nfse-valor-servicos').show();
            $('#nfse-valor-total').hide();
            $('#painel-incluir-produtos').css('border-color', '#f0ad4e').css('background', '#fdf8ed');
        }
    });

    // Fechar modal ao pressionar Enter no campo de confirmação
    $('#input-confirmar-produtos').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn-confirmar-produtos').click();
        }
    });

    // Retenções do Tomador — recalcular ao marcar/desmarcar checkboxes
    $('#retem-iss, #retem-irrf, #retem-pis, #retem-cofins, #retem-csll').on('change', function() {
        var valorBruto = wizardData.valorServicos || parseMoeda($('#valor-servicos-wizard').val());
        atualizarRetencoesStep1(valorBruto);
        // Se já estamos no passo 2+, invalidar cache para recalcular
        if (wizardData.currentStep >= 2) {
            wizardData.impostosResult = null;
            calcularImpostosWizard();
        }
    });

    // Aviso de retenção de ISS — mostrar/esconder quando marcar/desmarcar
    $('#retem-iss').on('change', function() {
        if ($(this).is(':checked')) {
            $('#aviso-retencao-iss').slideDown(200);
        } else {
            $('#aviso-retencao-iss').slideUp(200);
        }
    });

    // Valor integral — recalcular valor do boleto ao marcar/desmarcar
    $('#valor-integral-wizard').on('change', function() {
        atualizarValorBoleto();
    });
});
</script>