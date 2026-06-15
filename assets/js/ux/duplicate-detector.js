/**
 * Duplicate Detector — Detecta duplicatas ao cadastrar (F4.5)
 *
 * Funciona em formularios com inputs que tenham data-dup-check="entidade:campo"
 *   Ex: <input name="documento" data-dup-check="clientes:documento">
 *       <input name="email"     data-dup-check="clientes:email">
 *
 * Quando o usuario sai do campo (blur), faz uma busca AJAX e mostra
 * um aviso amarelo com os duplicatas encontrados + link para o registro.
 *
 * Endpoint backend: /ux_dup/buscar?entidade=X&campo=Y&valor=Z
 */
(function(window, $) {
  'use strict';

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';
  var DEBOUNCE = 350; // ms

  function debounce(fn, ms) {
    var t;
    return function() {
      var args = arguments, ctx = this;
      clearTimeout(t);
      t = setTimeout(function() { fn.apply(ctx, args); }, ms);
    };
  }

  function showWarning(input, matches) {
    hideWarning(input);
    if (!matches || matches.length === 0) return;
    var $input = $(input);
    $input.addClass('ux-dup-input');
    var html = '<div class="ux-dup-warning">'
             + '<i class="bx bx-error-circle"></i>'
             + '<div class="ux-dup-content">'
             + '<strong>Atenção: ' + matches.length + ' registro(s) similar(es) encontrado(s)</strong>'
             + '<ul>';
    matches.slice(0, 5).forEach(function(m) {
      var url = m.url || '#';
      html += '<li><a href="' + url + '" target="_blank">' + (m.label || m.id) + '</a></li>';
    });
    if (matches.length > 5) {
      html += '<li><em>... e mais ' + (matches.length - 5) + '</em></li>';
    }
    html += '</ul></div></div>';
    var $w = $(html);
    $w.insertAfter($input);
    // Guarda referencia para cleanup
    $input.data('ux-dup-warning', $w);
  }

  function hideWarning(input) {
    var $w = $(input).data('ux-dup-warning');
    if ($w) { $w.remove(); $(input).removeData('ux-dup-warning'); }
    $(input).removeClass('ux-dup-input');
  }

  function check(input) {
    var raw = input.getAttribute('data-dup-check') || '';
    var parts = raw.split(':');
    if (parts.length !== 2) return;
    var entidade = parts[0];
    var campo   = parts[1];
    var valor   = (input.value || '').trim();
    if (valor.length < 3) { hideWarning(input); return; }

    if ($.ajax) {
      $.ajax({
        url: BaseUrl + 'index.php/ux_dup/buscar',
        type: 'GET',
        dataType: 'json',
        data: { entidade: entidade, campo: campo, valor: valor },
        timeout: 5000,
        success: function(resp) {
          if (resp && resp.success) {
            showWarning(input, resp.matches || []);
          }
        }
      });
    }
  }

  function attach(input) {
    if (input.__uxDupAttached) return;
    input.__uxDupAttached = true;
    var fn = debounce(function() { check(input); }, DEBOUNCE);
    input.addEventListener('blur', fn);
    input.addEventListener('input', fn);
  }

  function init() {
    document.querySelectorAll('[data-dup-check]').forEach(attach);
  }

  $(function() {
    init();
    setTimeout(init, 2000);
  });

  window.UX.dup = { init: init, attach: attach };
})(window, jQuery);
