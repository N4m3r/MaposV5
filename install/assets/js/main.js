var onFormSubmit = function ($form) {
  $form.find('[type="submit"]').attr('disabled', 'disabled').find(".loader").removeClass("hide");
  $form.find('[type="submit"]').find(".button-text").addClass("hide");
  $("#alert-container").html("");
  // Mostrar barra de progresso
  $("#install-progress-container").removeClass("hide");
};

var onSubmitSussess = function ($form) {
  $form.find('[type="submit"]').removeAttr('disabled').find(".loader").addClass("hide");
  $form.find('[type="submit"]').find(".button-text").removeClass("hide");
};

var updateProgress = function(percent, message, step) {
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
    var stepText = '';
    switch(step) {
      case 1: stepText = 'Validação'; break;
      case 2: stepText = 'Conexão DB'; break;
      case 3: stepText = 'Tabelas Base'; break;
      case 4: stepText = 'Tabelas V5'; break;
      case 5: stepText = 'Dados DRE'; break;
      case 6: stepText = 'Impostos'; break;
      case 7: stepText = 'Permissões'; break;
      case 8: stepText = 'Configuração'; break;
      default: stepText = 'Processando...';
    }
    $progressStep.text('Etapa: ' + stepText);
  }

  // Adicionar classes de cor baseado no progresso
  if (percent < 30) {
    $progressBar.removeClass("progress-bar-success progress-bar-info").addClass("progress-bar-danger");
  } else if (percent < 70) {
    $progressBar.removeClass("progress-bar-danger progress-bar-success").addClass("progress-bar-info");
  } else {
    $progressBar.removeClass("progress-bar-danger progress-bar-info").addClass("progress-bar-success");
  }
};

var showInstallError = function(message, step) {
  $("#install-progress-bar").removeClass("progress-bar-striped active").addClass("progress-bar-danger");
  $("#install-progress-message").html('<i class="fa fa-times-circle"></i> <span class="text-danger">' + message + '</span>');

  var stepInfo = step ? ' <strong>(Erro na Etapa ' + step + ')</strong>' : '';
  $("#alert-container").html('<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> ' + message + stepInfo + '</div>');

  // Marcar etapa com erro
  if (step) {
    $(document).trigger('showStepError', [step]);
  }
};

$(document).ready(function () {
  var $preInstallationTab = $("#pre-installation-tab"),
  $configurationTab = $("#configuration-tab");

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

    // Usar XMLHttpRequest para ter mais controle sobre o progresso
    var formData = $form.serialize();
    var xhr = new XMLHttpRequest();

    xhr.open('POST', 'do_install.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          try {
            var result = JSON.parse(xhr.responseText);

            if (result.progress) {
              // Recebeu atualização de progresso
              updateProgress(result.percent, result.message, result.step);
            } else if (result.success) {
              // Instalação concluída
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
              // Erro na instalação
              showInstallError(result.message, result.step);
              onSubmitSussess($form);
            }
          } catch (e) {
            showInstallError('Erro ao processar resposta: ' + e.message);
            onSubmitSussess($form);
          }
        } else {
          showInstallError('Erro de comunicação com o servidor. Status: ' + xhr.status);
          onSubmitSussess($form);
        }
      }
    };

    xhr.onerror = function() {
      showInstallError('Erro de rede. Verifique sua conexão.');
      onSubmitSussess($form);
    };

    xhr.onprogress = function(evt) {
      if (evt.lengthComputable) {
        // Não usamos o progresso do upload, pois temos nosso próprio sistema
      }
    };

    xhr.send(formData);
    return false;
  });

});
