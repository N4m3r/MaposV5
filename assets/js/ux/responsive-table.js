/**
 * Responsive Table → Cards (F5.2)
 *
 * Em telas < 768px, converte tabelas com .data-table em cards
 * verticais empilhados, com label de coluna em cada valor.
 * Adiciona tambem um botao "Tabela / Cards" para o usuario alternar.
 *
 * Funciona em tabelas com <th data-col-key="..."> (padrao ja usado).
 */
(function(window, $) {
  'use strict';

  var STORAGE_KEY = 'ux-table-view-mode'; // 'table' | 'cards' | 'auto'

  function currentMode() {
    try {
      return localStorage.getItem(STORAGE_KEY) || 'auto';
    } catch (e) { return 'auto'; }
  }

  function setMode(m) {
    try { localStorage.setItem(STORAGE_KEY, m); } catch (e) {}
  }

  function shouldUseCards() {
    var m = currentMode();
    if (m === 'cards') return true;
    if (m === 'table') return false;
    return window.innerWidth < 768;
  }

  function buildCards(table) {
    if (table.__uxCards) return;
    table.__uxCards = true;

    // Captura headers
    var headers = [];
    table.querySelectorAll('thead th').forEach(function(th) {
      headers.push({
        key:   th.getAttribute('data-col-key') || th.textContent.trim(),
        label: th.textContent.trim(),
        isActions: (th.getAttribute('data-col-key') || '').toLowerCase() === 'actions',
      });
    });

    // Para cada row, gera um card
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.querySelectorAll('tr');
    rows.forEach(function(tr) {
      // Pula linha de empty state
      if (tr.querySelector('.ux-empty-state')) return;
      var card = document.createElement('div');
      card.className = 'ux-data-card';
      var inner = '<div class="ux-data-card-body">';
      tr.querySelectorAll('td').forEach(function(td, i) {
        var h = headers[i];
        if (!h) return;
        if (h.isActions) {
          // Actions como rodape
          inner += '<div class="ux-data-card-actions">' + td.innerHTML + '</div>';
        } else {
          inner += '<div class="ux-data-card-row">'
                 + '<span class="ux-data-card-label">' + h.label + '</span>'
                 + '<span class="ux-data-card-value">' + (td.innerHTML || '') + '</span>'
                 + '</div>';
        }
      });
      inner += '</div>';
      card.innerHTML = inner;
      tr.style.display = 'none';
      tr.insertAdjacentElement('afterend', card);
    });
  }

  function removeCards(table) {
    if (!table.__uxCards) return;
    table.__uxCards = false;
    var next = table.querySelector('tbody tr');
    while (next) {
      var card = next.nextElementSibling;
      if (card && card.classList.contains('ux-data-card')) card.remove();
      next.style.display = '';
      next = next.nextElementSibling;
      // Pula cards
      while (next && next.classList.contains('ux-data-card')) {
        next = next.nextElementSibling;
      }
    }
  }

  function applyMode() {
    var tables = document.querySelectorAll('table.data-table');
    var useCards = shouldUseCards();
    tables.forEach(function(t) {
      if (useCards) buildCards(t);
      else removeCards(t);
      t.classList.toggle('ux-table-mode-cards', useCards);
      t.classList.toggle('ux-table-mode-table', !useCards);
    });
    // Toggle button label
    document.querySelectorAll('[data-ux-table-view]').forEach(function(btn) {
      var icon = btn.querySelector('i');
      if (icon) icon.className = useCards ? 'bx bx-table' : 'bx bx-layout';
      btn.title = useCards ? 'Ver em tabela' : 'Ver em cards';
    });
  }

  // Toggle manual
  $(document).on('click', '[data-ux-table-view]', function(e) {
    e.preventDefault();
    var cur = currentMode();
    if (cur === 'auto') {
      // Forca o oposto do auto
      setMode(window.innerWidth < 768 ? 'table' : 'cards');
    } else {
      setMode(cur === 'cards' ? 'table' : 'cards');
    }
    applyMode();
  });

  $(function() {
    applyMode();
    var t;
    window.addEventListener('resize', function() {
      clearTimeout(t);
      t = setTimeout(applyMode, 200);
    });
  });

  window.UX.tableView = { apply: applyMode, mode: currentMode };
})(window, jQuery);
