<script>
// =============================================
// Wizard NFS-e + Boleto (simplificado e robusto)
// =============================================

var wizardData = {
    currentStep: 1,
    totalSteps: 4,
    impostosResult: null,
    valorServicos: parseFloat('<?= floatval(($result->valor_desconto ?? 0) > 0 ? $result->valor_desconto : ($totalServico ?? 0)) ?>') || 0,
    valorDeducoes: 0,
    gerarBoleto: true,
    incluirProdutos: false,
    valorApenasServicos: <?= floatval($totalServico ?? 0) ?>,
    valorTotalComProdutos: <?= floatval(($totalServico ?? 0) + ($totalProdutos ?? 0)) ?>,
    regimeTributario: '<?= ($tributacao['regime'] ?? 'simples_nacional') ?>',
    retemIss: false,
    retemIrrf: false,
    retemPis: false,
    retemCofins: false,
    retemCsll: false,
    retencoes: null,
    valorDas: null,
    isCalculating: false
};

function fmtMoneyInput(v) {
    v = parseFloat(v) || 0;
    return v.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function parseMoney(str) {
    if (!str) return 0;
    return parseFloat(String(str).replace(/\./g, '').replace(',', '.')) || 0;
}

function fmtMoney(v) {
    return 'R$ ' + parseFloat(v || 0).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function wizardGoToStep(step) {
    if (step < 1 || step > wizardData.totalSteps) return;
    if (step > wizardData.currentStep && !validarStep(wizardData.currentStep)) return;

    $('#wizard-steps li').each(function() {
        var s = parseInt($(this).data('step'));
        $(this).removeClass('active completed');
        if (s < step) $(this).addClass('completed');
        if (s === step) $(this).addClass('active');
    });

    $('.wizard-step-panel').removeClass('active');
    $('#wizard-step-' + step).addClass('active');
    wizardData.currentStep = step;

    $('#btn-wizard-anterior').prop('disabled', step === 1);
    if (step === wizardData.totalSteps) {
        $('#btn-wizard-proximo').hide();
        $('#btn-wizard-emitir').show();
        atualizarResumo();
    } else {
        $('#btn-wizard-proximo').show();
        $('#btn-wizard-emitir').hide();
    }

    if (step === 2) calcularImpostosWizard();
    if (step === 3) atualizarValorBoleto();
}

function validarStep(step) {
    if (step === 1) {
        var valor = parseMoney($('#valor-servicos-wizard').val());
        if (valor <= 0) { alert('Informe o valor dos servicos.'); $('#valor-servicos-wizard').focus(); return false; }
        var desc = $('#descricao-servico-wizard').val().trim();
        if (!desc) { alert('Informe a descricao do servico.'); $('#descricao-servico-wizard').focus(); return false; }
        wizardData.valorServicos = valor;
        wizardData.valorDeducoes = parseMoney($('#valor-deducoes-wizard').val());
        return true;
    }
    if (step === 2) {
        if (wizardData.isCalculating) {
            alert('Calculo dos impostos em andamento. Aguarde um instante.');
            return false;
        }
        if (!wizardData.impostosResult) {
            calcularImpostosWizard();
            return false;
        }
        return true;
    }
    if (step === 3) {
        wizardData.gerarBoleto = $('#gerar-boleto-wizard').is(':checked');
        if (wizardData.gerarBoleto && !$('#data-vencimento-wizard').val()) {
            alert('Informe a data de vencimento.'); return false;
        }
        return true;
    }
    return true;
}

var calcularTimeout = null;
function setWizardLoading(loading) {
    wizardData.isCalculating = loading;
    var btn = $('#btn-wizard-proximo');
    if (loading) {
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Calculando...');
        $('#wizard-loading-indicator').show();
    } else {
        btn.prop('disabled', false).html('<i class="fas fa-arrow-right"></i> Proximo');
        $('#wizard-loading-indicator').hide();
    }
}

function calcularImpostosWizard() {
    var valor = wizardData.valorServicos || parseMoney($('#valor-servicos-wizard').val());
    if (valor <= 0) return;
    clearTimeout(calcularTimeout);
    calcularTimeout = setTimeout(function() {
        setWizardLoading(true);
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
                setWizardLoading(false);
                updateCsrfToken(data);
                if (data.success) {
                    wizardData.impostosResult = data;
                    wizardData.regimeTributario = data.regime_tributario || wizardData.regimeTributario;
                    wizardData.valorDas = data.valor_das || null;
                    wizardData.retencoes = data.retencoes || null;

                    var deducoes = wizardData.valorDeducoes || parseMoney($('#valor-deducoes-wizard').val());
                    var baseCalc = data.valor_bruto - deducoes;

                    $('#imp-valor-bruto').text(fmtMoney(data.valor_bruto));
                    $('#imp-deducoes').text(fmtMoney(deducoes));
                    $('#imp-base-calculo').text(fmtMoney(baseCalc));

                    if (wizardData.regimeTributario === 'simples_nacional') {
                        if ($('#imp-das').length) $('#imp-das').html('<strong>' + fmtMoney(wizardData.valorDas || data.valor_bruto * 0.06) + '</strong>');
                        if ($('#imp-das-aliquota').length) {
                            var aliq = wizardData.valorDas ? ((wizardData.valorDas / data.valor_bruto) * 100).toFixed(2) : '6.00';
                            $('#imp-das-aliquota').text(aliq + '%');
                        }
                    } else {
                        var imp = data.impostos || {};
                        if ($('#imp-iss').length) $('#imp-iss').text(fmtMoney(imp.iss || imp.iss_valor || 0));
                        if ($('#imp-pis').length) $('#imp-pis').text(fmtMoney(imp.pis || imp.pis_valor || 0));
                        if ($('#imp-cofins').length) $('#imp-cofins').text(fmtMoney(imp.cofins || imp.cofins_valor || 0));
                        if ($('#imp-irrf').length) $('#imp-irrf').text(fmtMoney(imp.irrf || imp.irpj_valor || 0));
                        if ($('#imp-csll').length) $('#imp-csll').text(fmtMoney(imp.csll || imp.csll_valor || 0));
                        if ($('#imp-inss').length) $('#imp-inss').text(fmtMoney(imp.inss || imp.cpp_valor || 0));
                    }

                    var totalImpostos = (data.impostos || {}).valor_total_impostos || 0;
                    $('#imp-total-impostos').text(fmtMoney(totalImpostos));

                    var totalRetencao = wizardData.retencoes ? parseFloat(wizardData.retencoes.valor_total_retencao || 0) : 0;
                    if (totalRetencao > 0) { $('#retencao-row').show(); $('#imp-retencao-total').text(fmtMoney(totalRetencao)); }
                    else { $('#retencao-row').hide(); }

                    $('#imp-valor-liquido').text(fmtMoney(data.valor_liquido));
                    if (wizardData.regimeTributario === 'simples_nacional' && wizardData.valorDas) {
                        $('#das-valor-display').text(fmtMoney(wizardData.valorDas));
                    }
                    atualizarRetencoesStep1(valor);
                } else {
                    var msg = data.message || 'Erro ao calcular impostos';
                    $('#impostos-table td.imposto-valor').text('—');
                    $('#imp-valor-liquido').html('<span style="color:#dc3545">' + msg + '</span>');
                }
            },
            error: function(xhr) {
                setWizardLoading(false);
                var msg = 'Erro na comunicacao.';
                if (xhr.status === 0) msg = 'Sem conexao.';
                else if (xhr.status === 403) msg = 'Acesso negado (CSRF).';
                else if (xhr.status === 500) msg = 'Erro interno (500).';
                else try { var r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch(e){}
                $('#impostos-table td.imposto-valor').text('—');
                $('#imp-valor-liquido').html('<span style="color:#dc3545">' + msg + '</span>');
            }
        });
    }, 300);
}

function atualizarRetencoesStep1(valorBruto) {
    var aliqIss = parseFloat('<?= $tributacao["aliquota_iss"] ?? "5.00" ?>');
    var rIss = $('#retem-iss').is(':checked') ? Math.round(valorBruto * aliqIss / 100 * 100) / 100 : 0;
    var rIrrf = $('#retem-irrf').is(':checked') ? Math.round(valorBruto * 1.5 / 100 * 100) / 100 : 0;
    var rPis = $('#retem-pis').is(':checked') ? Math.round(valorBruto * 0.65 / 100 * 100) / 100 : 0;
    var rCofins = $('#retem-cofins').is(':checked') ? Math.round(valorBruto * 3.0 / 100 * 100) / 100 : 0;
    var rCsll = $('#retem-csll').is(':checked') ? Math.round(valorBruto * 1.0 / 100 * 100) / 100 : 0;
    var totalRet = rIss + rIrrf + rPis + rCofins + rCsll;

    wizardData.retemIss = $('#retem-iss').is(':checked');
    wizardData.retemIrrf = $('#retem-irrf').is(':checked');
    wizardData.retemPis = $('#retem-pis').is(':checked');
    wizardData.retemCofins = $('#retem-cofins').is(':checked');
    wizardData.retemCsll = $('#retem-csll').is(':checked');

    $('#retem-iss-valor').text(rIss > 0 ? fmtMoney(rIss) : '');
    $('#retem-irrf-valor').text(rIrrf > 0 ? fmtMoney(rIrrf) : '');
    $('#retem-pis-valor').text(rPis > 0 ? fmtMoney(rPis) : '');
    $('#retem-cofins-valor').text(rCofins > 0 ? fmtMoney(rCofins) : '');
    $('#retem-csll-valor').text(rCsll > 0 ? fmtMoney(rCsll) : '');
    $('#retem-total-valor').text(fmtMoney(totalRet));
    wizardData.impostosResult = null;
}

function atualizarValorBoleto() {
    if (!wizardData.impostosResult) return;
    var valorBruto = wizardData.impostosResult.valor_bruto;
    var deducoes = wizardData.valorDeducoes || parseMoney($('#valor-deducoes-wizard').val());
    var totalRetencao = wizardData.retencoes ? parseFloat(wizardData.retencoes.valor_total_retencao || 0) : 0;

    if (totalRetencao > 0) $('#valor-integral-section').show();
    else { $('#valor-integral-section').hide(); $('#valor-integral-wizard').prop('checked', false); }

    var valorBoleto = valorBruto - deducoes;
    if (totalRetencao > 0 && !$('#valor-integral-wizard').is(':checked')) {
        valorBoleto = valorBruto - deducoes - totalRetencao;
        $('#valor-boleto-ajuda').text('Valor liquido = Servicos - Deducoes - Retencoes');
    } else {
        $('#valor-boleto-ajuda').text('Valor integral = Servicos - Deducoes');
    }
    $('#valor-boleto-wizard').val(fmtMoneyInput(valorBoleto));
}

function atualizarResumo() {
    var valor = wizardData.valorServicos || parseMoney($('#valor-servicos-wizard').val());
    var deducoes = wizardData.valorDeducoes || parseMoney($('#valor-deducoes-wizard').val());
    $('#res-valor-servicos').text(fmtMoney(valor));
    $('#res-deducoes').text(fmtMoney(deducoes));

    var totalRetencao = wizardData.retencoes ? parseFloat(wizardData.retencoes.valor_total_retencao || 0) : 0;

    if (wizardData.impostosResult) {
        var imp = wizardData.impostosResult.impostos || {};
        var totalImpostos = imp.valor_total_impostos || 0;
        var liquido = wizardData.impostosResult.valor_bruto - deducoes - totalRetencao;

        if (wizardData.regimeTributario === 'simples_nacional') {
            $('#res-das-linha').show();
            $('#res-imposto-linha').html('<strong>DAS Estimado:</strong> ');
            $('#res-total-impostos').text(fmtMoney(totalImpostos));
            if (wizardData.valorDas) $('#res-valor-das').text(fmtMoney(wizardData.valorDas));
        } else {
            $('#res-das-linha').hide();
            $('#res-imposto-linha').html('<strong>Total Impostos:</strong> ');
            $('#res-total-impostos').text(fmtMoney(totalImpostos));
        }
        if (totalRetencao > 0) { $('#res-retencao-linha').show(); $('#res-retencao-total').text(fmtMoney(totalRetencao)); }
        else { $('#res-retencao-linha').hide(); }
        $('#res-valor-liquido').text(fmtMoney(liquido));
    }

    if (wizardData.gerarBoleto) {
        var venc = $('#data-vencimento-wizard').val();
        var vencF = venc ? venc.split('-').reverse().join('/') : '—';
        var isValorIntegral = $('#valor-integral-wizard').is(':checked');
        var valorBruto = wizardData.impostosResult ? wizardData.impostosResult.valor_bruto : 0;
        var valorBoleto = valorBruto - deducoes;
        if (totalRetencao > 0 && !isValorIntegral) valorBoleto = valorBruto - deducoes - totalRetencao;

        var html = '<strong>Gerar boleto:</strong> Sim<br>';
        if (totalRetencao > 0 && isValorIntegral) {
            html += '<strong style="color:#e67e22">Valor Integral:</strong> ' + fmtMoney(valorBoleto) + '<br><small style="color:#888">(Retencoes nao descontadas)</small><br>';
        } else if (totalRetencao > 0) {
            html += '<strong>Valor Liquido:</strong> ' + fmtMoney(valorBoleto) + '<br><small style="color:#888">(Retencoes descontadas)</small><br>';
        } else {
            html += '<strong>Valor:</strong> ' + fmtMoney(valorBoleto) + '<br>';
        }
        if (totalRetencao > 0) html += '<strong style="color:#e67e22">Retencoes:</strong> ' + fmtMoney(totalRetencao) + '<br>';
        html += '<strong>Vencimento:</strong> ' + vencF;
        $('#res-boleto-info').html(html);
    } else {
        $('#res-boleto-info').html('<strong>Gerar boleto:</strong> Nao');
    }
}

function emitirNFSeWizard() {
    var valor = wizardData.valorServicos || parseMoney($('#valor-servicos-wizard').val());
    var deducoes = wizardData.valorDeducoes || parseMoney($('#valor-deducoes-wizard').val());
    var descricao = $('#descricao-servico-wizard').val();
    var gerarBoleto = wizardData.gerarBoleto ? 1 : 0;

    var regimeLabel = wizardData.regimeTributario === 'simples_nacional' ? 'Simples Nacional' : 'Lucro Presumido';
    var msg = 'Confirmar emissao da NFS-e?\n\nRegime: ' + regimeLabel + '\nValor: ' + fmtMoney(valor);
    if (deducoes > 0) msg += '\nDeducoes: ' + fmtMoney(deducoes);
    if (wizardData.impostosResult) {
        var totalRet = wizardData.retencoes ? parseFloat(wizardData.retencoes.valor_total_retencao || 0) : 0;
        if (wizardData.regimeTributario === 'simples_nacional' && wizardData.valorDas) msg += '\nDAS: ' + fmtMoney(wizardData.valorDas);
        else msg += '\nImpostos: ' + fmtMoney((wizardData.impostosResult.impostos || {}).valor_total_impostos || 0);
        if (totalRet > 0) msg += '\nRetencoes: ' + fmtMoney(totalRet);
    }
    if (gerarBoleto) msg += '\n\nBoleto sera gerado automaticamente.';
    if (!confirm(msg)) return;

    var btn = $('#btn-wizard-emitir');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Emitindo...');

    var emitData = {
        valor_servicos: valor,
        valor_deducoes: deducoes,
        descricao_servico: descricao,
        gerar_boleto: 0,
        regime_tributario: wizardData.regimeTributario,
        competencia: new Date().toISOString().slice(0, 7) + '-01'
    };
    if (wizardData.regimeTributario === 'simples_nacional' && wizardData.valorDas) emitData.valor_das = wizardData.valorDas;
    if ($('#retem-iss').is(':checked')) { emitData.retem_iss = 1; emitData.valor_retencao_iss = (wizardData.retencoes || {}).valor_retencao_iss || 0; }
    if ($('#retem-irrf').is(':checked')) { emitData.retem_irrf = 1; emitData.valor_retencao_irrf = (wizardData.retencoes || {}).valor_retencao_irrf || 0; }
    if ($('#retem-pis').is(':checked')) { emitData.retem_pis = 1; emitData.valor_retencao_pis = (wizardData.retencoes || {}).valor_retencao_pis || 0; }
    if ($('#retem-cofins').is(':checked')) { emitData.retem_cofins = 1; emitData.valor_retencao_cofins = (wizardData.retencoes || {}).valor_retencao_cofins || 0; }
    if ($('#retem-csll').is(':checked')) { emitData.retem_csll = 1; emitData.valor_retencao_csll = (wizardData.retencoes || {}).valor_retencao_csll || 0; }
    if (wizardData.retencoes && wizardData.retencoes.valor_total_retencao) emitData.valor_total_retencao = wizardData.retencoes.valor_total_retencao;
    Object.assign(emitData, getCsrfToken());

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
                var msg = 'NFS-e emitida com sucesso!\n';
                if (response.chave_acesso) msg += 'Chave: ' + response.chave_acesso + '\n';
                if (response.numero) msg += 'Numero: ' + response.numero + '\n';
                msg += 'Ambiente: ' + (ambiente === 'producao' ? 'Producao' : 'Homologacao');

                if (gerarBoleto && nfseId) {
                    btn.html('<i class="fas fa-spinner fa-spin"></i> Gerando boleto...');
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
                        success: function() { alert(msg + '\n\nBoleto gerado!'); location.reload(); },
                        error: function() { alert(msg + '\n\nErro ao gerar boleto.'); location.reload(); }
                    });
                } else {
                    alert(msg); location.reload();
                }
            } else {
                alert('Erro: ' + (response.message || response.error || 'Erro desconhecido'));
                btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Emitir NFS-e (API Nacional)');
            }
        },
        error: function(xhr) {
            var msg = 'Erro na comunicacao.';
            try { var r = JSON.parse(xhr.responseText); msg = r.message || r.error || msg; } catch(e){}
            alert(msg);
            btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Emitir NFS-e (API Nacional)');
        }
    });
}

function toggleBoleto() {
    var checked = $('#gerar-boleto-wizard').is(':checked');
    wizardData.gerarBoleto = checked;
    if (checked) { $('#boleto-campos').show(); $('#sem-boleto-msg').hide(); }
    else { $('#boleto-campos').hide(); $('#sem-boleto-msg').show(); }
}

function getCsrfToken() {
    var name = '<?= config_item("csrf_token_name") ?>';
    var val = getCookie('<?= config_item("csrf_cookie_name") ?>');
    if (name && val) { var t = {}; t[name] = val; return t; }
    return {};
}

function updateCsrfToken(data) {
    var name = '<?= config_item("csrf_token_name") ?>';
    if (data && data[name]) {
        var cookieName = '<?= config_item("csrf_cookie_name") ?>';
        document.cookie = cookieName + '=' + encodeURIComponent(data[name]) + '; path=/; domain=' + window.location.hostname + ';';
    }
}

function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
}

function cancelarNFSe(nfseId) {
    var motivo = prompt('Motivo do cancelamento:');
    if (motivo) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/cancelar_nfse/") ?>' + nfseId;
        var input = document.createElement('input');
        input.type = 'hidden'; input.name = 'motivo'; input.value = motivo;
        form.appendChild(input);
        var csrf = getCsrfToken();
        for (var k in csrf) { var i = document.createElement('input'); i.type='hidden'; i.name=k; i.value=csrf[k]; form.appendChild(i); }
        document.body.appendChild(form);
        form.submit();
    }
}

function cancelarNFSeNacional(nfseId) {
    var motivo = prompt('Motivo do cancelamento (min. 15 caracteres):');
    if (!motivo) return;
    if (motivo.length < 15) { alert('Minimo 15 caracteres.'); return; }
    if (!confirm('Confirma o cancelamento na API Nacional?')) return;
    var btn = $(event.target).closest('button');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cancelando...');
    var cancelData = { motivo: motivo };
    Object.assign(cancelData, getCsrfToken());
    $.ajax({
        url: '<?= site_url("nfse_os/cancelar_nfse_api/") ?>' + nfseId,
        type: 'POST',
        data: cancelData,
        dataType: 'json',
        success: function(response) {
            updateCsrfToken(response);
            if (response.success) { alert('Cancelada com sucesso!'); location.reload(); }
            else { alert('Erro: ' + (response.message || 'Erro desconhecido')); btn.prop('disabled', false).html('<i class="fas fa-times"></i> Cancelar Nacional'); }
        },
        error: function(xhr) {
            var msg = 'Erro na comunicacao.';
            try { var r = JSON.parse(xhr.responseText); msg = r.message || msg; } catch(e){}
            alert(msg);
            btn.prop('disabled', false).html('<i class="fas fa-times"></i> Cancelar Nacional');
        }
    });
}

function consultarNFSeNacional(nfseId) {
    var resultDiv = $('#nfse-consulta-resultado');
    var contentDiv = $('#nfse-consulta-conteudo');
    resultDiv.show();
    contentDiv.html('<i class="fas fa-spinner fa-spin"></i> Consultando...');
    $.ajax({
        url: '<?= site_url("nfse_os/consultar_nfse/") ?>' + nfseId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data || {};
                var html = '<strong>Consulta Realizada</strong><br>';
                html += '<strong>Situacao:</strong> ' + (data.situacaoNfse || data.situacao || '---') + '<br>';
                html += '<strong>Chave:</strong> ' + (data.chaveAcesso || response.chave_acesso || '---') + '<br>';
                if (data.numero) html += '<strong>Numero:</strong> ' + data.numero + '<br>';
                if (data.dataHoraEmissao) html += '<strong>Data/Hora:</strong> ' + data.dataHoraEmissao + '<br>';
                contentDiv.html(html).removeClass('alert-info').addClass('alert-success');
            } else {
                contentDiv.html('<strong>Erro:</strong> ' + (response.message || 'Erro desconhecido')).removeClass('alert-info').addClass('alert-danger');
            }
        },
        error: function() {
            contentDiv.html('<strong>Erro na comunicacao.</strong>').removeClass('alert-info').addClass('alert-danger');
        }
    });
}

function cancelarBoleto(boletoId) {
    if (confirm('Cancelar este boleto?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url("nfse_os/cancelar_boleto/") ?>' + boletoId;
        var csrf = getCsrfToken();
        for (var k in csrf) { var i = document.createElement('input'); i.type='hidden'; i.name=k; i.value=csrf[k]; form.appendChild(i); }
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
        inputData.type = 'hidden'; inputData.name = 'data_pagamento'; inputData.value = data;
        form.appendChild(inputData);
        var csrf = getCsrfToken();
        for (var k in csrf) { var i = document.createElement('input'); i.type='hidden'; i.name=k; i.value=csrf[k]; form.appendChild(i); }
        document.body.appendChild(form);
        form.submit();
    }
}

function copiarLinhaDigitavel() {
    var input = document.getElementById('linha-digitavel');
    if (input) { input.select(); document.execCommand('copy'); alert('Copiado!'); }
}

$(document).ready(function() {
    // Inicializa valores da OS no wizard
    var valorInicial = wizardData.valorServicos || 0;
    if (valorInicial > 0) {
        $('#valor-servicos-wizard').val(fmtMoneyInput(valorInicial));
        atualizarRetencoesStep1(valorInicial);
    }

    // Mascara monetaria
    $('#valor-servicos-wizard, #valor-deducoes-wizard').on('input', function() {
        var val = this.value.replace(/[^\d]/g, '');
        if (!val.length) { this.value = ''; return; }
        val = val.replace(/^0+/, '') || '0';
        while (val.length < 3) val = '0' + val;
        var inteiro = val.substring(0, val.length - 2);
        var decimal = val.substring(val.length - 2);
        this.value = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',' + decimal;
    });

    // Recalcular ao mudar valores
    $('#valor-servicos-wizard, #valor-deducoes-wizard').on('change', function() {
        wizardData.valorServicos = parseMoney($('#valor-servicos-wizard').val());
        wizardData.valorDeducoes = parseMoney($('#valor-deducoes-wizard').val());
        wizardData.impostosResult = null;
        atualizarRetencoesStep1(wizardData.valorServicos);
    });

    // Navegacao
    $('#btn-wizard-proximo').click(function() { wizardGoToStep(wizardData.currentStep + 1); });
    $('#btn-wizard-anterior').click(function() { wizardGoToStep(wizardData.currentStep - 1); });
    $('#btn-wizard-emitir').click(function() { emitirNFSeWizard(); });

    // Toggle boleto
    $('#gerar-boleto-wizard').change(function() { toggleBoleto(); });

    // Checkbox incluir produtos
    $('#incluir-produtos-nfse').on('change', function() {
        var checked = $(this).is(':checked');
        wizardData.incluirProdutos = checked;
        var novoValor = checked ? wizardData.valorTotalComProdutos : wizardData.valorApenasServicos;
        var desconto = parseFloat('<?= floatval($result->valor_desconto ?? 0) ?>') || 0;
        var valorNFSe = desconto > 0 ? desconto : novoValor;
        $('#valor-servicos-wizard').val(fmtMoneyInput(valorNFSe));
        wizardData.valorServicos = valorNFSe;
        wizardData.impostosResult = null;
        $('#valor-servicos-help').text(checked ? 'Valor total (servicos + produtos)' : 'Valor dos servicos prestados');
    });

    // Retencoes
    $('#retem-iss, #retem-irrf, #retem-pis, #retem-cofins, #retem-csll').on('change', function() {
        var valorBruto = wizardData.valorServicos || parseMoney($('#valor-servicos-wizard').val());
        atualizarRetencoesStep1(valorBruto);
        if (wizardData.currentStep >= 2) { wizardData.impostosResult = null; calcularImpostosWizard(); }
    });

    // Aviso retencao ISS
    $('#retem-iss').on('change', function() {
        if ($(this).is(':checked')) $('#aviso-retencao-iss').slideDown(200);
        else $('#aviso-retencao-iss').slideUp(200);
    });

    // Valor integral
    $('#valor-integral-wizard').on('change', function() { atualizarValorBoleto(); });

    // Preview PDF
    $('#btn-preview-nfse').click(function() {
        var valor = parseMoney($('#valor-servicos-wizard').val());
        var deducoes = parseMoney($('#valor-deducoes-wizard').val());
        var descricao = $('#descricao-servico-wizard').val();
        var url = '<?= site_url("nfse_os/preview/" . $result->idOs) ?>';
        url += '?valor_servicos=' + encodeURIComponent(valor);
        url += '&valor_deducoes=' + encodeURIComponent(deducoes);
        url += '&descricao_servico=' + encodeURIComponent(descricao);
        window.open(url, '_blank');
    });
});
</script>
