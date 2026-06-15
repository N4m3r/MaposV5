/**
 * A11y Audit (F6.7) — roda axe-core e exibe resultados
 */
(function(window, $) {
  'use strict';
  if (!window.UX) window.UX = {};

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function severityClass(impact) {
    return {
      critical: 'ux-a11y-critical',
      serious:  'ux-a11y-serious',
      moderate: 'ux-a11y-moderate',
      minor:    'ux-a11y-minor',
    }[impact] || 'ux-a11y-info';
  }

  function render(results) {
    var target = document.getElementById('ux-a11y-results');
    var summary = document.getElementById('ux-a11y-summary');
    if (!target) return;

    var total = results.violations.length;
    var passes = results.passes.length;
    var incomplete = results.incomplete.length;
    var inapplicable = results.inapplicable.length;

    summary.style.display = '';
    summary.innerHTML =
      '<div class="ux-a11y-stat ux-a11y-stat-danger">' +
        '<div class="ux-a11y-stat-num">' + total + '</div>' +
        '<div class="ux-a11y-stat-label">violacoes</div>' +
      '</div>' +
      '<div class="ux-a11y-stat ux-a11y-stat-warning">' +
        '<div class="ux-a11y-stat-num">' + incomplete + '</div>' +
        '<div class="ux-a11y-stat-label">incompletos</div>' +
      '</div>' +
      '<div class="ux-a11y-stat ux-a11y-stat-success">' +
        '<div class="ux-a11y-stat-num">' + passes + '</div>' +
        '<div class="ux-a11y-stat-label">aprovados</div>' +
      '</div>' +
      '<div class="ux-a11y-stat ux-a11y-stat-info">' +
        '<div class="ux-a11y-stat-num">' + inapplicable + '</div>' +
        '<div class="ux-a11y-stat-label">nao aplicaveis</div>' +
      '</div>';

    if (total === 0) {
      target.innerHTML = '<div class="ux-a11y-pass">'
        + '<i class="bx bx-check-double"></i>'
        + '<span>Nenhuma violacao encontrada. Otimo trabalho!</span>'
        + '</div>';
      return;
    }

    var html = '<h3>Violacoes encontradas</h3>';
    html += '<ol class="ux-a11y-violations">';
    results.violations.forEach(function(v) {
      html += '<li class="' + severityClass(v.impact) + '">'
            + '<div class="ux-a11y-viol-head">'
            +   '<strong>' + esc(v.id) + '</strong>'
            +   '<span class="ux-a11y-impact">' + esc(v.impact || 'unknown') + '</span>'
            + '</div>'
            + '<p class="ux-a11y-viol-help">' + esc(v.help) + '</p>'
            + '<p class="ux-a11y-viol-link"><a href="' + esc(v.helpUrl) + '" target="_blank" rel="noopener">Documentacao</a></p>'
            + '<div class="ux-a11y-viol-nodes">'
            +   '<strong>Elementos afetados (' + v.nodes.length + '):</strong>'
            +   '<ul>';
      v.nodes.slice(0, 5).forEach(function(node) {
        html += '<li><code>' + esc(node.html.substring(0, 200)) + '</code>';
        if (node.failureSummary) {
          html += '<div class="ux-a11y-viol-summary">' + esc(node.failureSummary.replace(/\n/g, '<br>')) + '</div>';
        }
        html += '</li>';
      });
      if (v.nodes.length > 5) {
        html += '<li><em>... e mais ' + (v.nodes.length - 5) + ' elemento(s)</em></li>';
      }
      html += '</ul></div></li>';
    });
    html += '</ol>';
    target.innerHTML = html;
  }

  function run() {
    var target = document.getElementById('ux-a11y-results');
    if (!window.axe) {
      target.innerHTML = '<div class="ux-a11y-pass">axe-core nao carregou. Verifique o CDN.</div>';
      return;
    }
    target.innerHTML = '<div class="ux-a11y-loading"><i class="bx bx-loader-alt bx-spin"></i> Analisando pagina...</div>';
    window.axe.run(document, {
      runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'] },
      resultTypes: ['violations', 'incomplete', 'passes', 'inapplicable'],
    })
    .then(function(results) {
      render(results);
    })
    .catch(function(err) {
      target.innerHTML = '<div class="ux-a11y-pass">Erro: ' + esc(err.message || err) + '</div>';
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('ux-a11y-run');
    if (btn) btn.addEventListener('click', run);
  });

  window.UX.a11yAudit = { run: run };
})(window, jQuery);
