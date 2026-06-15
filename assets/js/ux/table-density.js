/**
 * Table Density — Compacta / Padrao / Espacosa (F3.4)
 *
 * Aplica a preferencia do usuario em todas as tabelas da pagina via
 * classes no <body>. A escolha persiste em localStorage e pode ser
 * trocada via atalho (Ctrl+Alt+D) ou pelo seletor.
 *
 * Classes geradas no <body>:
 *   - .ux-density-compact     -> linhas de 28px
 *   - .ux-density-comfortable -> linhas de 44px (padrao)
 *   - .ux-density-spacious    -> linhas de 56px
 */
(function(window, $) {
  'use strict';

  var STORAGE_KEY = 'ux-table-density';
  var DEFAULT = 'comfortable';

  function apply(density) {
    if (!density) density = DEFAULT;
    var body = document.body;
    body.classList.remove('ux-density-compact', 'ux-density-comfortable', 'ux-density-spacious');
    body.classList.add('ux-density-' + density);
    try { localStorage.setItem(STORAGE_KEY, density); } catch (e) {}
    // Atualiza radios (se houver)
    document.querySelectorAll('input[name="ux-density"]').forEach(function(r) {
      r.checked = (r.value === density);
    });
  }

  // Wire-up
  $(function() {
    // 1) Aplica a preferencia persistida
    var saved = DEFAULT;
    try { saved = localStorage.getItem(STORAGE_KEY) || DEFAULT; } catch (e) {}
    apply(saved);

    // 2) Sincroniza com o seletor (radio)
    $(document).on('change', 'input[name="ux-density"]', function() {
      apply($(this).val());
    });

    // 3) Atalho: Ctrl+Alt+D cicla entre as 3 densidades
    $(document).on('keydown', function(e) {
      if (e.ctrlKey && e.altKey && (e.key === 'd' || e.key === 'D')) {
        e.preventDefault();
        var order = ['compact', 'comfortable', 'spacious'];
        var current = (localStorage.getItem(STORAGE_KEY) || DEFAULT);
        var next = order[(order.indexOf(current) + 1) % order.length];
        apply(next);
        if (window.notify) {
          var labels = { compact: 'Compacta', comfortable: 'Padrao', spacious: 'Espacosa' };
          window.notify.info('Densidade: ' + labels[next]);
        }
      }
    });
  });

  // API publica
  window.UX.density = { apply: apply };
})(window, jQuery);
