/**
 * Bulk Actions Bar (F4.6)
 *
 * Adiciona barra de acoes em lote no topo de tabelas com [data-ux-bulk]
 *
 * Uso:
 *   <table data-ux-bulk data-ux-bulk-url="os/bulk" data-ux-bulk-id="idOs">
 *     <th><input type="checkbox" class="ux-bulk-select-all"></th>
 *     ...
 *
 *   <button data-ux-bulk-action="concluir">Concluir</button>
 *   <button data-ux-bulk-action="excluir">Excluir</button>
 *   <button data-ux-bulk-action="imprimir">Imprimir</button>
 */
(function(window, $) {
  'use strict';
  if (!window.UX) window.UX = {};
  if (!$) return;

  function getRowId(row, idAttr) {
    return row.getAttribute('data-ux-bulk-row') || row.getAttribute('data-' + idAttr) || null;
  }

  function setupTable(table) {
    if (table.__uxBulkInit) return;
    table.__uxBulkInit = true;

    var url = table.getAttribute('data-ux-bulk-url');
    var idAttr = table.getAttribute('data-ux-bulk-id') || 'id';
    var barId = 'ux-bulk-bar-' + Math.random().toString(36).slice(2, 8);
    var bar = document.createElement('div');
    bar.className = 'ux-bulk-bar';
    bar.id = barId;
    bar.setAttribute('role', 'region');
    bar.setAttribute('aria-live', 'polite');
    bar.setAttribute('aria-label', 'Acoes em lote');
    bar.innerHTML =
      '<div class="ux-bulk-bar-info">' +
      '  <i class="bx bx-check-square"></i>' +
      '  <span><span class="ux-bulk-count">0</span> selecionado(s)</span>' +
      '</div>' +
      '<div class="ux-bulk-bar-actions">' +
      '  <button type="button" class="btn btn-sm" data-ux-bulk-do="concluir"><i class="bx bx-check"></i> Concluir</button>' +
      '  <button type="button" class="btn btn-sm" data-ux-bulk-do="imprimir"><i class="bx bx-printer"></i> Imprimir</button>' +
      '  <button type="button" class="btn btn-sm" data-ux-bulk-do="exportar"><i class="bx bx-download"></i> Exportar</button>' +
      '  <button type="button" class="btn btn-sm btn-danger" data-ux-bulk-do="excluir"><i class="bx bx-trash"></i> Excluir</button>' +
      '</div>' +
      '<button type="button" class="ux-bulk-bar-cancel" aria-label="Cancelar selecao">' +
      '  <i class="bx bx-x"></i> Cancelar' +
      '</button>';
    table.parentNode.insertBefore(bar, table);

    // Adiciona checkbox em cada linha
    var headers = table.querySelectorAll('thead th');
    var firstTh = headers[0];
    if (firstTh && !firstTh.querySelector('.ux-bulk-checkbox')) {
      var thCb = document.createElement('input');
      thCb.type = 'checkbox';
      thCb.className = 'ux-bulk-checkbox ux-bulk-select-all';
      thCb.setAttribute('aria-label', 'Selecionar todos');
      firstTh.innerHTML = '';
      firstTh.appendChild(thCb);
    }
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
      if (row.querySelector('.ux-bulk-checkbox')) return;
      var firstTd = row.querySelector('td');
      if (!firstTd) return;
      var cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.className = 'ux-bulk-checkbox ux-bulk-row-cb';
      cb.setAttribute('aria-label', 'Selecionar linha');
      var tdCb = document.createElement('td');
      tdCb.appendChild(cb);
      row.insertBefore(tdCb, firstTd);
    });

    function update() {
      var checked = table.querySelectorAll('.ux-bulk-row-cb:checked');
      var n = checked.length;
      bar.classList.toggle('ux-bulk-active', n > 0);
      bar.querySelector('.ux-bulk-count').textContent = n;
      // Marca visualmente as linhas selecionadas
      rows.forEach(function(r) {
        var cb = r.querySelector('.ux-bulk-row-cb');
        if (cb && cb.checked) r.classList.add('ux-bulk-selected');
        else r.classList.remove('ux-bulk-selected');
      });
      // Atualiza checkbox do header
      var allCb = table.querySelector('.ux-bulk-select-all');
      if (allCb && allCb.classList.contains('ux-bulk-select-all')) {
        allCb.checked = (n > 0 && n === rows.length);
        allCb.indeterminate = (n > 0 && n < rows.length);
      }
    }

    // Listeners
    table.addEventListener('change', function(e) {
      var t = e.target;
      if (t.classList.contains('ux-bulk-select-all')) {
        rows.forEach(function(r) {
          var cb = r.querySelector('.ux-bulk-row-cb');
          if (cb) cb.checked = t.checked;
        });
        update();
      } else if (t.classList.contains('ux-bulk-row-cb')) {
        update();
      }
    });

    bar.querySelector('.ux-bulk-bar-cancel').addEventListener('click', function() {
      table.querySelectorAll('.ux-bulk-row-cb, .ux-bulk-select-all').forEach(function(c) { c.checked = false; });
      update();
    });

    bar.querySelectorAll('[data-ux-bulk-do]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var action = btn.getAttribute('data-ux-bulk-do');
        var ids = [];
        table.querySelectorAll('.ux-bulk-row-cb:checked').forEach(function(cb) {
          var row = cb.closest('tr');
          if (row) {
            var id = getRowId(row, idAttr);
            if (id) ids.push(id);
          }
        });
        if (!ids.length) return;

        if (action === 'imprimir') {
          // Imprime sem chamar backend
          var url2 = url ? (window.BaseUrl || '/') + 'index.php/' + url + '/imprimir?ids=' + ids.join(',') : '#';
          window.open(url2, '_blank');
          return;
        }
        if (action === 'exportar') {
          var url3 = url ? (window.BaseUrl || '/') + 'index.php/' + url + '/exportar?ids=' + ids.join(',') : '#';
          window.location.href = url3;
          return;
        }

        if (action === 'excluir' && !confirm('Excluir ' + ids.length + ' registro(s)? Esta acao nao pode ser desfeita.')) {
          return;
        }

        $.ajax({
          url: (window.BaseUrl || '/') + 'index.php/' + url,
          type: 'POST',
          dataType: 'json',
          data: { acao: action, ids: ids, csrf_token_name: window.csrf_token || '' },
          traditional: true,
        })
        .done(function(resp) {
          if (resp && resp.success) {
            if (window.UxToast && window.UxToast.success) {
              window.UxToast.success(resp.msg || 'Operacao concluida');
            } else {
              alert(resp.msg || 'Operacao concluida');
            }
            setTimeout(function() { window.location.reload(); }, 800);
          } else {
            alert((resp && resp.msg) || 'Erro ao processar');
          }
        })
        .fail(function() {
          alert('Erro de comunicacao com o servidor');
        });
      });
    });

    update();
  }

  function init() {
    document.querySelectorAll('table[data-ux-bulk]').forEach(setupTable);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  if (window.MutationObserver) {
    var obs = new MutationObserver(function() { init(); });
    obs.observe(document.body, { childList: true, subtree: true });
  }

  window.UX.bulk = { init: init, setup: setupTable };
})(window, jQuery);
