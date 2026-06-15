/**
 * Smart Notifications — Badges inteligentes no topo
 * F4.1 do Plano UX
 *
 * Faz polling em /notificacoes/smart a cada 90s e atualiza badges com:
 *   - OS atrasadas
 *   - Boletos vencendo hoje
 *   - Lancamentos atrasados
 *   - Clientes novos (7d)
 *
 * Os badges sao renderizados como .ux-smart-badge dentro de um icone
 * no topbar. O icone muda conforme a gravidade (vermelho se > 0 critico).
 */
(function(window, $) {
  'use strict';

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';
  var POLL_MS = 90000; // 90 segundos

  function render($container, smart) {
    // 1) Garante container
    if (!$container.length) return;

    // 2) Itens individuais
    var html = '';
    var grav = 0; // gravidade acumulada
    var items = [
      { key: 'os_atrasadas',          label: 'OS atrasadas',          icon: 'bx-time-five',  url: 'os?status=atrasado',           critico: true },
      { key: 'boletos_hoje',          label: 'Boletos hoje',          icon: 'bx-barcode',    url: 'cobrancas?vencimento=hoje',    critico: true },
      { key: 'lancamentos_atrasados', label: 'Lanc. atrasados',       icon: 'bx-wallet',     url: 'financeiro/lancamentos?atraso=1', critico: true },
      { key: 'os_aguardando',         label: 'Aguard. aprovacao',     icon: 'bx-pause-circle', url: 'os?status=aguardando',         critico: false },
      { key: 'clientes_novos',        label: 'Clientes novos',        icon: 'bx-user-plus',  url: 'clientes?recentes=1',          critico: false },
    ];

    items.forEach(function(it) {
      var n = smart[it.key] || 0;
      if (n > 0) {
        if (it.critico) grav++;
        html += '<a href="' + (BaseUrl + 'index.php/' + it.url) + '" class="ux-smart-item">'
              + '<i class="bx ' + it.icon + '"></i>'
              + '<span class="ux-smart-label">' + it.label + '</span>'
              + '<span class="ux-smart-count">' + n + '</span>'
              + '</a>';
      }
    });

    // 3) Determina cor do badge principal
    var badgeColor = grav > 0 ? 'critico' : 'ok';
    var totalBadge = (smart.os_atrasadas || 0) + (smart.boletos_hoje || 0) + (smart.lancamentos_atrasados || 0);

    $container.html(
      '<span class="ux-smart-bell ' + badgeColor + '" title="Notificacoes inteligentes">'
      + '<i class="bx bxs-bell"></i>'
      + (totalBadge > 0 ? '<span class="ux-smart-badge">' + (totalBadge > 99 ? '99+' : totalBadge) + '</span>' : '')
      + '</span>'
    );
    $container.next('.ux-smart-popup').remove(); // limpa popup antigo

    if (html) {
      var popup = '<div class="ux-smart-popup" style="display:none;">'
                + '<div class="ux-smart-popup-header">Notificacoes</div>'
                + html
                + '</div>';
      $container.after(popup);
    }
  }

  function fetch() {
    if (!$.ajax) return;
    $.ajax({
      url: BaseUrl + 'index.php/notificacoes/smart',
      type: 'GET',
      dataType: 'json',
      timeout: 8000,
      success: function(resp) {
        if (resp && resp.success) render($('#ux-smart-bell-container'), resp.data ? resp.data.smart : resp.smart);
      },
      error: function() { /* silencioso */ }
    });
  }

  // Wire-up
  $(function() {
    if (!$('#ux-smart-bell-container').length) return;
    fetch();
    setInterval(fetch, POLL_MS);

    // Popup on hover
    $(document).on('mouseenter', '#ux-smart-bell-container', function() {
      $('.ux-smart-popup').show();
    }).on('mouseleave', '#ux-smart-bell-container', function() {
      $('.ux-smart-popup').hide();
    });
  });

  window.UX.smartNotif = { refresh: fetch };
})(window, jQuery);
