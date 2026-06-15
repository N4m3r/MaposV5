/**
 * AI Insights Widget (F4.2 + F4.3 + F4.7)
 *
 * Renderiza:
 *  - Insights do Agente IA no dashboard
 *  - Sugestoes automaticas em uma OS
 *  - Eventos recentes do agente (badge)
 *
 * Polling opcional a cada 5min para manter insights atualizados.
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

  function tipoCor(tipo) {
    return {
      danger:  '#dc3545',
      warning: '#f59f00',
      info:    '#0dcaf0',
      success: '#198754',
    }[tipo] || '#6c757d';
  }

  function renderInsights(target, insights) {
    if (!insights || insights.length === 0) {
      target.html('<div class="ux-ai-empty">'
        + '<i class="bx bx-check-double"></i>'
        + '<span>Tudo certo por aqui! Sem alertas no momento.</span>'
        + '</div>');
      return;
    }
    var html = '<ul class="ux-ai-insights">';
    insights.forEach(function(i) {
      html += '<li class="ux-ai-insight ux-ai-type-' + (i.tipo || 'info') + '" style="border-left-color: ' + tipoCor(i.tipo) + ';">'
            + '<i class="bx ' + (i.icone || 'bx-bulb') + '"></i>'
            + '<div class="ux-ai-insight-body">'
            +   '<strong>' + escapeHtml(i.titulo) + '</strong>'
            +   '<span>' + escapeHtml(i.detalhe || '') + '</span>'
            + '</div>'
            + '<a href="' + escapeHtml(i.link || '#') + '" class="ux-ai-insight-link" title="Abrir"><i class="bx bx-right-arrow-alt"></i></a>'
            + '</li>';
    });
    html += '</ul>';
    target.html(html);
  }

  function carregarInsights(target) {
    target.html('<div class="ux-ai-loading"><i class="bx bx-loader-alt bx-spin"></i> Analisando dados...</div>');
    $.ajax({
      url: (window.BaseUrl || '/') + 'index.php/agente_ia_dashboard/insights_dashboard',
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
    })
    .done(function(resp) {
      if (resp && resp.success) {
        renderInsights(target, resp.insights);
        target.attr('data-ux-ai-updated', resp.atualizado_em || '');
      } else {
        target.html('<div class="ux-ai-empty">Nao foi possivel carregar insights.</div>');
      }
    })
    .fail(function() {
      target.html('<div class="ux-ai-empty">Insights indisponiveis no momento.</div>');
    });
  }

  // Sugestoes: aparece dentro de uma pagina de OS
  function renderSugestoes(target, data) {
    if (!data || !data.sugestoes || data.sugestoes.length === 0) {
      target.html('<div class="ux-ai-empty">Nenhuma sugestao disponivel para esta OS.</div>');
      return;
    }
    var html = '<div class="ux-ai-suggest-header">'
             + '<i class="bx bx-bot"></i> '
             + '<span>Proximas acoes sugeridas</span>'
             + '</div>';
    html += '<ul class="ux-ai-suggestions">';
    data.sugestoes.forEach(function(s) {
      html += '<li class="ux-ai-suggestion">'
            + '<div class="ux-ai-suggestion-body">'
            +   '<strong>' + escapeHtml(s.titulo) + '</strong>'
            +   '<span>' + escapeHtml(s.detalhe || '') + '</span>'
            + '</div>'
            + '<a href="' + escapeHtml(s.link || '#') + '" class="btn btn-sm btn-outline-primary">Aplicar</a>'
            + '</li>';
    });
    html += '</ul>';
    target.html(html);
  }

  function carregarSugestoes(target, osId) {
    target.html('<div class="ux-ai-loading"><i class="bx bx-loader-alt bx-spin"></i> Gerando sugestoes...</div>');
    $.ajax({
      url: (window.BaseUrl || '/') + 'index.php/agente_ia_dashboard/sugerir_os/' + encodeURIComponent(osId),
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
    })
    .done(function(resp) {
      if (resp && resp.success) {
        renderSugestoes(target, resp);
      } else {
        target.html('<div class="ux-ai-empty">Sem sugestoes no momento.</div>');
      }
    })
    .fail(function() {
      target.html('<div class="ux-ai-empty">Nao foi possivel gerar sugestoes.</div>');
    });
  }

  function init() {
    // Insights do dashboard
    document.querySelectorAll('[data-ux-ai-insights]').forEach(function(el) {
      var autoLoad = el.getAttribute('data-ux-ai-insights') !== 'manual';
      if (autoLoad) carregarInsights($(el));
      // Refresh button
      var refreshBtn = document.querySelector('[data-ux-ai-refresh]');
      if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
          e.preventDefault();
          carregarInsights($(el));
        });
      }
    });

    // Sugestoes por OS
    document.querySelectorAll('[data-ux-ai-suggest]').forEach(function(el) {
      var osId = el.getAttribute('data-ux-ai-suggest') || el.getAttribute('data-os-id');
      if (osId) carregarSugestoes($(el), osId);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Observa novos elementos
  if (window.MutationObserver) {
    var obs = new MutationObserver(function(mutations) {
      mutations.forEach(function(m) {
        m.addedNodes.forEach(function(n) {
          if (n.nodeType !== 1) return;
          if (n.matches && n.matches('[data-ux-ai-insights]')) init();
        });
      });
    });
    obs.observe(document.body, { childList: true, subtree: true });
  }

  window.UX.ai = {
    carregarInsights: carregarInsights,
    carregarSugestoes: carregarSugestoes,
  };
})(window, jQuery);
