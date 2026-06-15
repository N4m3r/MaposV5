/**
 * Timeline widget (F4.7)
 *
 * Renderiza o feed de eventos em #ux-timeline-feed
 * Endpoint: /timeline/feed
 */
(function(window, $) {
  'use strict';
  if (!window.UX) window.UX = {};
  if (!$) return;

  function escapeHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function render(target, eventos) {
    if (!eventos || eventos.length === 0) {
      target.html('<div class="ux-timeline-item">'
        + '<span class="ux-timeline-time">-</span>'
        + '<span class="ux-timeline-title">Nenhum evento no periodo selecionado.</span>'
        + '</div>');
      return;
    }
    var html = '';
    eventos.forEach(function(e) {
      var tipo = e.tipo || 'info';
      html += '<div class="ux-timeline-item ux-timeline-' + tipo + '">'
            + '<span class="ux-timeline-time">' + escapeHtml(e.relativo) + '</span>'
            + '<span class="ux-timeline-title"><i class="bx ' + escapeHtml(e.icone) + '"></i> '
            + escapeHtml(e.titulo) + '</span>'
            + '<span class="ux-timeline-desc">' + escapeHtml(e.descricao) + '</span>';
      if (e.link) {
        html += '<a href="' + escapeHtml(e.link) + '" class="ux-timeline-link">'
              + 'Ver detalhes <i class="bx bx-right-arrow-alt"></i></a>';
      }
      html += '</div>';
    });
    target.html(html);
  }

  function carregar(target) {
    var periodo = $('#ux-timeline-periodo').val() || '30';
    var modulo  = $('#ux-timeline-modulo').val() || '';
    target.html('<div class="ux-timeline-item ux-timeline-info">'
      + '<span class="ux-timeline-time">...</span>'
      + '<span class="ux-timeline-title"><i class="bx bx-loader-alt bx-spin"></i> Carregando eventos...</span>'
      + '</div>');

    $.ajax({
      url: (window.BaseUrl || '/') + 'index.php/timeline/feed',
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
      data: { periodo: periodo, modulo: modulo, limite: 80 },
    })
    .done(function(resp) {
      if (resp && resp.success) {
        render(target, resp.eventos);
      } else {
        target.html('<div class="ux-timeline-item ux-timeline-warning">'
          + '<span class="ux-timeline-title">Nao foi possivel carregar a timeline.</span>'
          + '</div>');
      }
    })
    .fail(function() {
      target.html('<div class="ux-timeline-item ux-timeline-danger">'
        + '<span class="ux-timeline-title">Erro de comunicacao com o servidor.</span>'
        + '</div>');
    });
  }

  function init() {
    var target = $('#ux-timeline-feed');
    if (target.length === 0) return;
    carregar(target);
    $('#ux-timeline-periodo, #ux-timeline-modulo').on('change', function() { carregar(target); });
    $('#ux-timeline-refresh').on('click', function() { carregar(target); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.UX.timeline = { carregar: carregar };
})(window, jQuery);
