// Funções de controle do formulário
function onFormSubmit($form) {
    $form.find('[type="submit"]').attr('disabled', 'disabled').find(".loader").removeClass("hide");
    $form.find('[type="submit"]').find(".button-text").addClass("hide");
    $("#alert-container").html("");
    $("#install-progress-container").removeClass("hide");
}

function onSubmitSuccess($form) {
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
function showInstallError(message, step) {
    $("#install-progress-bar").removeClass("progress-bar-striped active").addClass("progress-bar-danger");
    $("#install-progress-message").html('<i class="fa fa-times-circle"></i> <span class="text-danger">' + message + '</span>');

    var stepInfo = step ? ' <strong>(Erro na Etapa ' + step + ')</strong>' : '';
    $("#alert-container").html('<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> ' + message + stepInfo + '</div>');

    // Marcar etapa com erro
    if (step) {
        $('#step-' + step).addClass('error');
        $('#step-' + step + ' i').removeClass().addClass('fa fa-times-circle');
    }
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

// Document Ready
$(document).ready(function () {
    var $preInstallationTab = $("#pre-installation-tab");
    var $configurationTab = $("#configuration-tab");

    $(".form-next").click(function () {
        if ($preInstallationTab.hasClass("active")) {
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

        // Iniciar progresso simulado
        startSimulatedProgress();

        // Usar XMLHttpRequest
        var formData = $form.serialize();
        var xhr = new XMLHttpRequest();

        xhr.open('POST', 'do_install.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                stopSimulatedProgress();

                if (xhr.status === 200) {
                    try {
                        var result = JSON.parse(xhr.responseText);

                        if (result.success) {
                            updateProgress(100, 'Instalação concluída!', 8);
                            setTimeout(function() {
                                $configurationTab.removeClass("active");
                                $("#configuration").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
                                $("#finished").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
                                $("#finished").addClass("active");
                                $("#finished-tab").addClass("active");
                                $("#install-progress-container").addClass("hide");
                            }, 500);
                        } else {
                            showInstallError(result.message, result.step);
                            onSubmitSuccess($form);
                        }
                    } catch (e) {
                        // Se não for JSON válido, pode ser erro PHP
                        if (xhr.responseText.indexOf('<') === 0) {
                            showInstallError('Erro no servidor. Verifique o log de erros do PHP.', null);
                        } else {
                            showInstallError('Erro ao processar resposta: ' + e.message, null);
                        }
                        onSubmitSuccess($form);
                    }
                } else {
                    showInstallError('Erro de comunicação com o servidor. Status: ' + xhr.status, null);
                    onSubmitSuccess($form);
                }
            }
        };

        xhr.onerror = function() {
            stopSimulatedProgress();
            showInstallError('Erro de rede. Verifique sua conexão.', null);
            onSubmitSuccess($form);
        };

        xhr.send(formData);
        return false;
    });
});