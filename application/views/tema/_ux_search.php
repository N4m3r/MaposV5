<?php
/**
 * View parcial: Busca global (Cmd/Ctrl+K)
 * Incluir no rodape (antes do </body>).
 *
 * - Abre ao pressionar Ctrl+K ou Cmd+K (ja tratado em topo.php)
 * - Busca via AJAX em /busca?q=...
 * - Click ou Enter no resultado navega para a URL
 */
?>
<div class="ux-search-modal" id="ux-search-modal" role="dialog" aria-modal="true" aria-label="Busca global">
  <div class="ux-search-box">
    <div class="ux-search-input-wrap">
      <i class='bx bx-search'></i>
      <input type="search" id="ux-search-input" class="ux-search-input" placeholder="Buscar clientes, OS, produtos, servicos..." autocomplete="off" />
      <span class="ux-search-kbd">Esc</span>
    </div>
    <div class="ux-search-results" id="ux-search-results">
      <div class="ux-search-empty">
        <i class='bx bx-search-alt'></i>
        <div>Digite ao menos 2 caracteres para buscar.</div>
      </div>
    </div>
    <div class="ux-search-footer">
      <span><kbd>↑↓</kbd> navegar</span>
      <span><kbd>Enter</kbd> abrir</span>
      <span><kbd>Esc</kbd> fechar</span>
    </div>
  </div>
</div>

<script>
(function() {
  'use strict';

  var modal = document.getElementById('ux-search-modal');
  var input = document.getElementById('ux-search-input');
  var results = document.getElementById('ux-search-results');
  if (!modal || !input || !results) return;

  var focusedIndex = -1;
  var currentResults = [];
  var lastQuery = '';
  var debounceTimer = null;

  function open() {
    modal.classList.add('is-active');
    setTimeout(function() { input.focus(); input.select(); }, 30);
    document.body.style.overflow = 'hidden';
  }
  function close() {
    modal.classList.remove('is-active');
    input.value = '';
    results.innerHTML = '<div class="ux-search-empty"><i class="bx bx-search-alt"></i><div>Digite ao menos 2 caracteres para buscar.</div></div>';
    focusedIndex = -1;
    currentResults = [];
    lastQuery = '';
    document.body.style.overflow = '';
  }
  function isOpen() { return modal.classList.contains('is-active'); }

  function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, function(c) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
  }

  function renderGroup(title, items) {
    if (!items || items.length === 0) return '';
    var html = '<div class="ux-search-section">';
    html += '<div class="ux-search-section-title">' + escapeHtml(title) + '</div>';
    for (var i = 0; i < items.length; i++) {
      var it = items[i];
      var sub = it.subtitle ? '<div class="ux-search-result-subtitle">' + escapeHtml(it.subtitle) + '</div>' : '';
      html += '<a href="' + escapeHtml(it.url) + '" class="ux-search-result" data-index="' + (currentResults.length) + '">'
        + '<div class="ux-search-result-icon"><i class="bx ' + escapeHtml(it.icon || 'bx-file') + '"></i></div>'
        + '<div class="ux-search-result-content">'
        +   '<div class="ux-search-result-title">' + escapeHtml(it.title) + '</div>'
        +   sub
        + '</div></a>';
      currentResults.push(it);
    }
    html += '</div>';
    return html;
  }

  function renderResults(data) {
    currentResults = [];
    var html = '';
    html += renderGroup('Clientes', data.clientes);
    html += renderGroup('Ordens de Servico', data.os);
    html += renderGroup('Produtos', data.produtos);
    html += renderGroup('Servicos', data.servicos);
    if (currentResults.length === 0) {
      html = '<div class="ux-search-empty"><i class="bx bx-search"></i><div>Nada encontrado para "<strong>' + escapeHtml(lastQuery) + '</strong>".</div></div>';
    }
    results.innerHTML = html;
    focusedIndex = -1;
  }

  function doSearch(q) {
    if (q === lastQuery) return;
    lastQuery = q;
    if (q.length < 2) {
      results.innerHTML = '<div class="ux-search-empty"><i class="bx bx-search-alt"></i><div>Digite ao menos 2 caracteres para buscar.</div></div>';
      return;
    }
    results.innerHTML = '<div class="ux-search-empty"><div class="ux-loading-spinner" style="border-color:rgba(4,103,252,0.2);border-top-color:#0467fc;width:24px;height:24px;"></div></div>';
    var base = window.BaseUrl || '';
    fetch(base + 'index.php/busca?q=' + encodeURIComponent(q), {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function(r) { return r.json(); })
      .then(function(j) {
        if (lastQuery !== q) return; // descarte
        if (j && j.success && j.results) {
          renderResults(j.results);
        } else {
          renderResults({ clientes: [], os: [], produtos: [], servicos: [] });
        }
      })
      .catch(function() {
        if (lastQuery !== q) return;
        results.innerHTML = '<div class="ux-search-empty" style="color:#e74c3c;"><i class="bx bx-error"></i><div>Erro ao buscar. Tente novamente.</div></div>';
      });
  }

  // Eventos
  input.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    var q = input.value;
    debounceTimer = setTimeout(function() { doSearch(q); }, 200);
  });

  input.addEventListener('keydown', function(e) {
    var items = results.querySelectorAll('.ux-search-result');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (items.length === 0) return;
      focusedIndex = (focusedIndex + 1) % items.length;
      updateFocus(items);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (items.length === 0) return;
      focusedIndex = focusedIndex <= 0 ? items.length - 1 : focusedIndex - 1;
      updateFocus(items);
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (focusedIndex >= 0 && items[focusedIndex]) {
        items[focusedIndex].click();
      } else if (currentResults[0]) {
        window.location.href = currentResults[0].url;
      }
    } else if (e.key === 'Escape') {
      e.preventDefault();
      close();
    }
  });

  function updateFocus(items) {
    for (var i = 0; i < items.length; i++) {
      items[i].classList.toggle('is-focused', i === focusedIndex);
    }
    if (items[focusedIndex]) {
      items[focusedIndex].scrollIntoView({ block: 'nearest' });
    }
  }

  // Click no backdrop fecha
  modal.addEventListener('click', function(e) {
    if (e.target === modal) close();
  });

  // Fechar com Escape no documento (caso input nao tenha foco)
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && isOpen()) close();
  });

  // Expor API para o shortcut handler do topo.php
  window.UxSearch = { open: open, close: close, isOpen: isOpen };
})();
</script>
