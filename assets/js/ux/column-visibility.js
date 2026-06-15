/**
 * Column Visibility — Persiste quais colunas o usuario quer ver
 * F3.5 do Plano UX
 *
 * Funcionamento:
 *   1) Em qualquer tabela com [data-table-key="clientes"] (chave unica por listagem),
 *      adiciona um botao "Colunas" automaticamente
 *   2) Ao clicar, abre um dropdown com checkboxes para cada coluna
 *   3) A escolha persiste em localStorage e opcionalmente no backend
 *
 * Como usar:
 *   <table class="data-table" data-table-key="clientes">
 *     <thead><tr><th data-col-key="nome">Nome</th>...
 *
 * Para cada <th> com data-col-key, o usuario pode ocultar/mostrar.
 * A aplicacao e feita adicionando a classe ux-col-hidden na <th> e nas <td> correspondentes.
 */
(function(window, $) {
  'use strict';

  var BaseUrl = (typeof window.BaseUrl === 'string') ? window.BaseUrl : '/';
  var STORAGE_PREFIX = 'ux-cols-';
  var BACKEND_ENABLED = true; // sincroniza com backend se logado

  function getKey(table) {
    return table.getAttribute('data-table-key') || ('table-' + Array.prototype.indexOf.call(document.querySelectorAll('table'), table));
  }

  function loadState(key) {
    // Tenta local primeiro
    try {
      var local = localStorage.getItem(STORAGE_PREFIX + key);
      if (local) return JSON.parse(local);
    } catch (e) {}
    return null;
  }

  function saveStateLocal(key, state) {
    try { localStorage.setItem(STORAGE_PREFIX + key, JSON.stringify(state)); } catch (e) {}
  }

  function saveStateBackend(key, state) {
    if (!BACKEND_ENABLED || !$.ajax) return;
    $.ajax({
      url: BaseUrl + 'index.php/ux_columns/salvar',
      type: 'POST',
      dataType: 'json',
      data: { table_key: key, hidden: state.hidden, order: state.order }
    });
  }

  function applyState(table, state) {
    var hidden = state.hidden || [];
    var headers = table.querySelectorAll('th[data-col-key]');
    headers.forEach(function(th, i) {
      var colKey = th.getAttribute('data-col-key');
      if (hidden.indexOf(colKey) >= 0) {
        th.classList.add('ux-col-hidden');
        // Esconde a coluna correspondente em todas as linhas
        var rows = table.querySelectorAll('tbody tr');
        rows.forEach(function(tr) {
          var td = tr.children[i];
          if (td) td.classList.add('ux-col-hidden');
        });
      } else {
        th.classList.remove('ux-col-hidden');
        var rows = table.querySelectorAll('tbody tr');
        rows.forEach(function(tr) {
          var td = tr.children[i];
          if (td) td.classList.remove('ux-col-hidden');
        });
      }
    });
  }

  function getColumnsFromTable(table) {
    var cols = [];
    table.querySelectorAll('th[data-col-key]').forEach(function(th) {
      cols.push({
        key:   th.getAttribute('data-col-key'),
        label: th.textContent.trim() || th.getAttribute('data-col-key')
      });
    });
    return cols;
  }

  function buildButton(table) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ux-cols-toggle';
    btn.innerHTML = '<i class="bx bx-columns"></i> Colunas';
    btn.title = 'Mostrar/ocultar colunas';
    return btn;
  }

  function buildPanel(table, columns) {
    var state = loadState(getKey(table)) || { hidden: [], order: [] };
    var panel = document.createElement('div');
    panel.className = 'ux-cols-panel';
    panel.style.display = 'none';
    var html = '<div class="ux-cols-panel-header">'
             + '<strong>Colunas visiveis</strong>'
             + '<button type="button" class="ux-cols-close" aria-label="Fechar"><i class="bx bx-x"></i></button>'
             + '</div>'
             + '<div class="ux-cols-panel-body">';
    columns.forEach(function(col) {
      var isHidden = state.hidden.indexOf(col.key) >= 0;
      html += '<label class="ux-cols-option">'
            + '<input type="checkbox" data-col-key="' + col.key + '"' + (isHidden ? '' : ' checked') + '>'
            + '<span>' + col.label + '</span>'
            + '</label>';
    });
    html += '</div>'
          + '<div class="ux-cols-panel-footer">'
          + '<button type="button" class="ux-cols-reset">Restaurar padrao</button>'
          + '</div>';
    panel.innerHTML = html;
    return panel;
  }

  function attach(table) {
    if (table.__uxColsAttached) return;
    var key = getKey(table);
    var columns = getColumnsFromTable(table);
    if (columns.length < 2) return; // sem sentido com 1 coluna

    table.__uxColsAttached = true;

    // Wrapper para o botao
    var wrapper = document.createElement('div');
    wrapper.className = 'ux-cols-wrapper';
    table.parentNode.insertBefore(wrapper, table);
    wrapper.appendChild(table);

    var btn = buildButton(table);
    wrapper.appendChild(btn);

    var panel = buildPanel(table, columns);
    document.body.appendChild(panel);

    // Aplica estado salvo
    var state = loadState(key) || { hidden: [], order: [] };
    applyState(table, state);

    // Posiciona o panel abaixo do botao
    function positionPanel() {
      var rect = btn.getBoundingClientRect();
      panel.style.position = 'absolute';
      panel.style.top = (window.scrollY + rect.bottom + 4) + 'px';
      panel.style.left = (window.scrollX + rect.left) + 'px';
    }

    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var visible = panel.style.display !== 'none';
      // Fecha todos
      document.querySelectorAll('.ux-cols-panel').forEach(function(p) { p.style.display = 'none'; });
      if (!visible) {
        positionPanel();
        panel.style.display = 'block';
      }
    });

    panel.querySelector('.ux-cols-close').addEventListener('click', function() {
      panel.style.display = 'none';
    });

    panel.querySelectorAll('input[type="checkbox"]').forEach(function(cb) {
      cb.addEventListener('change', function() {
        var colKey = this.getAttribute('data-col-key');
        var current = loadState(key) || { hidden: [], order: [] };
        if (this.checked) {
          current.hidden = current.hidden.filter(function(k) { return k !== colKey; });
        } else {
          if (current.hidden.indexOf(colKey) < 0) current.hidden.push(colKey);
        }
        saveStateLocal(key, current);
        saveStateBackend(key, current);
        applyState(table, current);
      });
    });

    panel.querySelector('.ux-cols-reset').addEventListener('click', function() {
      var empty = { hidden: [], order: [] };
      saveStateLocal(key, empty);
      saveStateBackend(key, empty);
      // Reseta os checkboxes
      panel.querySelectorAll('input[type="checkbox"]').forEach(function(cb) { cb.checked = true; });
      applyState(table, empty);
    });
  }

  // Fecha ao clicar fora
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.ux-cols-panel') && !e.target.closest('.ux-cols-toggle')) {
      document.querySelectorAll('.ux-cols-panel').forEach(function(p) { p.style.display = 'none'; });
    }
  });

  // Wire-up
  $(function() {
    var tables = document.querySelectorAll('table.data-table[data-table-key]');
    tables.forEach(attach);
  });

  // API publica
  window.UX.columns = {
    refresh: function() {
      document.querySelectorAll('table.data-table[data-table-key]').forEach(attach);
    }
  };
})(window, jQuery);
