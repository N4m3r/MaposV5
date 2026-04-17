// ============================================
// SISTEMA DE LOGS FRONTEND - MapOS V5 Install
// ============================================
var InstallFrontendLog = {
    logs: [],
    maxLogs: 500,

    _formatTime: function() {
        var d = new Date();
        return d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0') + ' ' +
            String(d.getHours()).padStart(2, '0') + ':' +
            String(d.getMinutes()).padStart(2, '0') + ':' +
            String(d.getSeconds()).padStart(2, '0') + '.' +
            String(d.getMilliseconds()).padStart(3, '0');
    },

    _write: function(level, message, data) {
        var entry = {
            time: this._formatTime(),
            level: level,
            message: message,
            data: data || null
        };
        this.logs.push(entry);
        if (this.logs.length > this.maxLogs) {
            this.logs.shift();
        }

        // Também logar no console do navegador
        var consoleFn = level === 'ERROR' ? 'error' : level === 'WARN' ? 'warn' : 'log';
        var prefix = '[' + entry.time + '] [' + level + '] ';
        if (data) {
            console[consoleFn](prefix + message, data);
        } else {
            console[consoleFn](prefix + message);
        }
    },

    info: function(message, data) { this._write('INFO', message, data); },
    warn: function(message, data) { this._write('WARN', message, data); },
    error: function(message, data) { this._write('ERROR', message, data); },
    debug: function(message, data) { this._write('DEBUG', message, data); },

    // Exportar todos os logs como texto formatado
    export: function() {
        var lines = [];
        for (var i = 0; i < this.logs.length; i++) {
            var e = this.logs[i];
            var line = '[' + e.time + '] [' + e.level + '] ' + e.message;
            if (e.data) {
                try {
                    line += '\n' + JSON.stringify(e.data, null, 2);
                } catch (err) {
                    line += '\n(dados não serializáveis)';
                }
            }
            lines.push(line);
        }
        return lines.join('\n');
    },

    // Obter resumo de erros
    getErrors: function() {
        return this.logs.filter(function(e) { return e.level === 'ERROR'; });
    },

    // Obter resumo de avisos
    getWarnings: function() {
        return this.logs.filter(function(e) { return e.level === 'WARN'; });
    }
};

// Capturar erros JavaScript globais
window.onerror = function(message, source, lineno, colno, error) {
    InstallFrontendLog.error('Erro JavaScript global', {
        message: message,
        source: source ? source.replace(/^.*\/install\//, '') : source,
        line: lineno,
        column: colno,
        stack: error ? error.stack : null
    });
};

window.addEventListener('unhandledrejection', function(event) {
    InstallFrontendLog.error('Promise rejeitada sem tratamento', {
        reason: event.reason ? event.reason.message || event.reason : 'desconhecido',
        stack: event.reason ? event.reason.stack : null
    });
});

// ============================================
// FUNÇÕES DO FORMULÁRIO
// ============================================
function onFormSubmit($form) {
    InstallFrontendLog.info('Formulário submetido - iniciando instalação');
    $form.find('[type="submit"]').attr('disabled', 'disabled').find(".loader").removeClass("hide");
    $form.find('[type="submit"]').find(".button-text").addClass("hide");
    $("#alert-container").html("");
    $("#install-progress-container").removeClass("hide");
}

function onSubmitSuccess($form) {
    InstallFrontendLog.info('Botão de submit reabilitado');
    $form.find('[type="submit"]').removeAttr('disabled').find(".loader").addClass("hide");
    $form.find('[type="submit"]').find(".button-text").removeClass("hide");
}

// Atualizar barra de progresso
function updateProgress(percent, message, step) {
    var $progressBar = $("#install-progress-bar");
    var $progressText = $("#install-progress-text");
    var $progressMessage = $("#install-progress-message");
    var $progressStep = $("#install-progress-step");

    $progressBar.css("width", percent + "%");
    $progressBar.attr("aria-valuenow", percent);
    $progressText.text(percent + "%");

    if (message) {
        $progressMessage.html('<i class="fa fa-spinner fa-spin"></i> ' + message);
    }

    if (step) {
        var stepNames = {
            1: 'Validação',
            2: 'Conexão DB',
            3: 'Tabelas Base',
            4: 'Tabelas V5',
            5: 'Dados DRE',
            6: 'Impostos',
            7: 'Permissões',
            8: 'Config .env'
        };
        $progressStep.text('Etapa: ' + (stepNames[step] || 'Processando...'));
        updateStepVisual(step);
    }

    // Cor da barra baseada no progresso
    if (percent < 30) {
        $progressBar.removeClass("progress-bar-success progress-bar-info").addClass("progress-bar-danger");
    } else if (percent < 70) {
        $progressBar.removeClass("progress-bar-danger progress-bar-success").addClass("progress-bar-info");
    } else {
        $progressBar.removeClass("progress-bar-danger progress-bar-info").addClass("progress-bar-success");
    }
}

// Atualizar visual das etapas
function updateStepVisual(currentStep) {
    for (var i = 1; i <= 8; i++) {
        var $step = $('#step-' + i);
        $step.removeClass('active completed error');

        if (i < currentStep) {
            $step.addClass('completed');
            $step.find('i').removeClass().addClass('fa fa-check-circle');
        } else if (i === currentStep) {
            $step.addClass('active');
            $step.find('i').removeClass().addClass('fa fa-spinner fa-spin');
        }
    }
}

// Mostrar erro
function showInstallError(message, step, logFile) {
    InstallFrontendLog.error('Erro na instalação', {
        message: message,
        step: step,
        logFile: logFile
    });

    $("#install-progress-bar").removeClass("progress-bar-striped active").addClass("progress-bar-danger");
    $("#install-progress-message").html('<i class="fa fa-times-circle"></i> <span class="text-danger">' + message + '</span>');

    var stepInfo = step ? ' <strong>(Erro na Etapa ' + step + ')</strong>' : '';
    var logInfo = logFile ? ' <small class="text-muted">Log: ' + logInfo + '</small>' : '';
    $("#alert-container").html(
        '<div class="alert alert-danger" role="alert">' +
        '<i class="fa fa-exclamation-triangle"></i> ' + message + stepInfo +
        (logFile ? '<br><small class="text-muted">Arquivo de log: install/logs/' + logFile + '</small>' : '') +
        '<br><button type="button" class="btn btn-xs btn-default" onclick="showLogDetails()" style="margin-top:5px;">' +
        '<i class="fa fa-file-text"></i> Ver detalhes do log</button>' +
        '</div>'
    );

    // Marcar etapa com erro
    if (step) {
        $('#step-' + step).addClass('error');
        $('#step-' + step + ' i').removeClass().addClass('fa fa-times-circle');
    }
}

// Mostrar detalhes do log em modal/alerta
function showLogDetails() {
    var errors = InstallFrontendLog.getErrors();
    var warnings = InstallFrontendLog.getWarnings();
    var allLogs = InstallFrontendLog.export();

    var content = '<div class="panel panel-default" style="text-align:left; max-height:400px; overflow-y:auto;">' +
        '<div class="panel-heading"><strong>Detalhes do Log da Instalação</strong></div>' +
        '<div class="panel-body" style="font-size:11px; font-family:monospace; white-space:pre-wrap;">';

    if (errors.length > 0) {
        content += '=== ERROS (' + errors.length + ') ===\n';
        for (var i = 0; i < errors.length; i++) {
            content += errors[i].message + '\n';
            if (errors[i].data) {
                try { content += JSON.stringify(errors[i].data, null, 2) + '\n'; } catch(e) {}
            }
        }
        content += '\n';
    }

    if (warnings.length > 0) {
        content += '=== AVISOS (' + warnings.length + ') ===\n';
        for (var i = 0; i < warnings.length; i++) {
            content += warnings[i].message + '\n';
        }
        content += '\n';
    }

    content += '=== LOG COMPLETO ===\n' + allLogs;
    content += '</div></div>';

    $("#alert-container").append(content);
}

// Simular progresso durante a requisição (enquanto o servidor processa)
var progressInterval = null;
function startSimulatedProgress() {
    var currentPercent = 5;
    var currentStep = 1;
    var steps = [
        { maxPercent: 10, step: 1, message: 'Validando dados do formulário...' },
        { maxPercent: 20, step: 2, message: 'Conectando ao banco de dados...' },
        { maxPercent: 45, step: 3, message: 'Criando tabelas base...' },
        { maxPercent: 60, step: 4, message: 'Criando tabelas adicionais V5...' },
        { maxPercent: 70, step: 5, message: 'Inserindo dados DRE...' },
        { maxPercent: 78, step: 6, message: 'Configurando impostos...' },
        { maxPercent: 85, step: 7, message: 'Criando permissões do administrador...' },
        { maxPercent: 92, step: 8, message: 'Gerando arquivo de configuração...' }
    ];

    var stepIndex = 0;
    progressInterval = setInterval(function() {
        if (stepIndex < steps.length && currentPercent >= steps[stepIndex].maxPercent - 5) {
            currentStep = steps[stepIndex].step;
            stepIndex++;
        }

        // Desacelerar conforme aproxima do limite da etapa atual
        var maxForCurrentStep = stepIndex < steps.length ? steps[stepIndex].maxPercent : 95;
        var increment = currentPercent > maxForCurrentStep - 10 ? 1 : 2;

        if (currentPercent < maxForCurrentStep) {
            currentPercent += increment;
            updateProgress(currentPercent, steps[Math.min(stepIndex, steps.length - 1)].message, currentStep);
        }
    }, 800);
}

function stopSimulatedProgress() {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
}

// ============================================
// DOCUMENT READY
// ============================================
$(document).ready(function () {
    InstallFrontendLog.info('Página de instalação carregada');
    InstallFrontendLog.debug('User Agent', { ua: navigator.userAgent });
    InstallFrontendLog.debug('URL atual', { url: window.location.href });

    var $preInstallationTab = $("#pre-installation-tab");
    var $configurationTab = $("#configuration-tab");

    $(".form-next").click(function () {
        if ($preInstallationTab.hasClass("active")) {
            InstallFrontendLog.info('Navegando para aba de Configuração');
            $preInstallationTab.removeClass("active");
            $configurationTab.addClass("active");
            $("#pre-installation").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
            $("#configuration").addClass("active");
            $("#host").focus();
        }
    });

    $("#config-form").submit(function () {
        var $form = $(this);
        onFormSubmit($form);

        // Reset visual steps
        $('.step-item').removeClass('active completed error');
        $('.step-item i').removeClass().addClass('fa fa-check-circle-o');

        // Coletar dados do formulário para log
        var formDataObj = {};
        $.each($form.serializeArray(), function(_, field) {
            formDataObj[field.name] = field.name === 'dbpassword' || field.name === 'password'
                ? '********'
                : field.value;
        });
        InstallFrontendLog.info('Enviando dados para do_install.php', formDataObj);

        // Iniciar progresso simulado
        startSimulatedProgress();

        // Usar XMLHttpRequest
        var formData = $form.serialize();
        var xhr = new XMLHttpRequest();
        var startTime = Date.now();

        InstallFrontendLog.info('XHR POST para do_install.php - iniciado');

        xhr.open('POST', 'do_install.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                stopSimulatedProgress();
                var elapsed = ((Date.now() - startTime) / 1000).toFixed(2);

                InstallFrontendLog.info('XHR completado', {
                    status: xhr.status,
                    elapsed_seconds: elapsed,
                    response_length: xhr.responseText.length
                });

                // Tentar parsear JSON da resposta independentemente do status HTTP
                // O servidor retorna JSON com detalhes do erro mesmo em status 500
                try {
                    var result = JSON.parse(xhr.responseText);
                    InstallFrontendLog.debug('Resposta JSON do servidor', {
                        success: result.success,
                        step: result.step,
                        message: result.message,
                        log_file: result.log_file || null,
                        http_status: xhr.status
                    });

                    if (result.success && xhr.status === 200) {
                        updateProgress(100, 'Instalação concluída!', 8);
                        InstallFrontendLog.info('Instalação concluída com sucesso!');
                        setTimeout(function() {
                            $configurationTab.removeClass("active");
                            $("#configuration").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
                            $("#finished").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
                            $("#finished").addClass("active");
                            $("#finished-tab").addClass("active");
                            $("#install-progress-container").addClass("hide");
                        }, 500);
                    } else {
                        InstallFrontendLog.error('Instalação falhou', {
                            message: result.message,
                            step: result.step,
                            log_file: result.log_file || null,
                            http_status: xhr.status
                        });
                        showInstallError(result.message, result.step, result.log_file);
                        onSubmitSuccess($form);
                    }
                } catch (e) {
                    InstallFrontendLog.error('Falha ao parsear resposta do servidor', {
                        error: e.message,
                        response_preview: xhr.responseText.substring(0, 500),
                        http_status: xhr.status
                    });

                    if (xhr.responseText.indexOf('<') === 0) {
                        showInstallError('Erro no servidor. Verifique o log de erros do PHP em install/logs/.', null, null);
                    } else {
                        showInstallError('Erro ao processar resposta (HTTP ' + xhr.status + '): ' + e.message, null, null);
                    }
                    onSubmitSuccess($form);
                }
            }
        };

        xhr.onerror = function() {
            stopSimulatedProgress();
            InstallFrontendLog.error('Erro de rede no XHR', {
                type: 'network_error',
                readyState: xhr.readyState,
                status: xhr.status
            });
            showInstallError('Erro de rede. Verifique sua conexão.', null, null);
            onSubmitSuccess($form);
        };

        xhr.ontimeout = function() {
            stopSimulatedProgress();
            InstallFrontendLog.error('Timeout no XHR');
            showInstallError('A requisição demorou muito. Tente novamente.', null, null);
            onSubmitSuccess($form);
        };

        xhr.send(formData);
        return false;
    });
});