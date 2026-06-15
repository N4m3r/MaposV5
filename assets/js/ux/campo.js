/**
 * Modo Campo (F5.7) — UI simplificada para tecnicos
 *
 * - Carrega agenda via /campo/api_agenda
 * - Fila offline salva em localStorage 'ux-campo-queue'
 * - Sincroniza fila ao detectar online
 * - Captura fotos via mobile-camera.js
 * - Relogio em tempo real
 */
(function(window, $) {
  'use strict';
  if (!window.UX) window.UX = {};
  if (!$) return;

  var QUEUE_KEY = 'ux-campo-queue';
  var clockTimer = null;

  function fmtClock() {
    var d = new Date();
    var hh = String(d.getHours()).padStart(2, '0');
    var mm = String(d.getMinutes()).padStart(2, '0');
    return hh + ':' + mm;
  }
  function startClock() {
    var el = document.getElementById('ux-campo-clock');
    if (!el) return;
    el.textContent = fmtClock();
    clearInterval(clockTimer);
    clockTimer = setInterval(function() { el.textContent = fmtClock(); }, 30000);
  }

  function updateNet() {
    var dot = document.getElementById('ux-campo-net');
    var lbl = document.getElementById('ux-campo-net-label');
    if (!dot || !lbl) return;
    if (navigator.onLine) {
      dot.classList.remove('ux-campo-offline');
      lbl.textContent = 'Conectado';
    } else {
      dot.classList.add('ux-campo-offline');
      lbl.textContent = 'Offline';
    }
  }

  function getQueue() {
    try { return JSON.parse(localStorage.getItem(QUEUE_KEY) || '[]'); }
    catch (e) { return []; }
  }
  function setQueue(arr) {
    try { localStorage.setItem(QUEUE_KEY, JSON.stringify(arr)); } catch (e) {}
    updatePending();
  }
  function enqueue(evento) {
    var q = getQueue();
    q.push(evento);
    setQueue(q);
  }
  function updatePending() {
    var el = document.getElementById('ux-campo-pending');
    if (!el) return;
    var n = getQueue().length;
    if (n > 0) {
      el.textContent = n;
      el.style.display = '';
    } else {
      el.style.display = 'none';
    }
  }

  async function syncQueue() {
    if (!navigator.onLine) {
      alert('Sem conexao. Sincronizacao adiada.');
      return;
    }
    var q = getQueue();
    if (q.length === 0) {
      alert('Nada para sincronizar.');
      return;
    }
    try {
      var r = await fetch((window.BaseUrl || '/') + 'index.php/campo/sync', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ eventos: q }),
      });
      var data = await r.json();
      if (data && data.success) {
        setQueue([]);
        alert('Sincronizado: ' + (data.processados || 0) + ' evento(s).');
      } else {
        alert('Erro ao sincronizar.');
      }
    } catch (e) {
      alert('Erro: ' + e);
    }
  }

  function carregarAgenda() {
    var target = document.getElementById('ux-campo-os');
    if (!target) return;
    target.innerHTML = '<div class="ux-campo-loading"><i class="bx bx-loader-alt bx-spin"></i> Carregando...</div>';
    fetch((window.BaseUrl || '/') + 'index.php/campo/api_agenda', {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data || !data.sucesso) {
        target.innerHTML = '<div class="ux-campo-empty">Sem informacoes no momento.</div>';
        return;
      }
      if (!data.minhas_os || data.minhas_os.length === 0) {
        target.innerHTML = '<div class="ux-campo-empty">'
          + '<i class="bx bx-check-double"></i>'
          + '<span>Nenhuma OS pendente.</span></div>';
        return;
      }
      var html = '';
      data.minhas_os.forEach(function(os) {
        html += '<div class="ux-campo-os-card" data-os-id="' + os.idOs + '">'
              + '<div class="ux-campo-os-numero">OS #' + os.idOs + '</div>'
              + '<div class="ux-campo-os-desc">' + (os.descricaoProduto || 'Sem descricao') + '</div>'
              + '<div class="ux-campo-os-data"><i class="bx bx-calendar"></i> ' + (os.dataInicial || '-') + '</div>'
              + '</div>';
      });
      target.innerHTML = html;
    })
    .catch(function() {
      target.innerHTML = '<div class="ux-campo-empty">Sem conexao. Mostrando cache local.</div>';
    });
  }

  function showModal(titulo, placeholder, onConfirm) {
    var modal = document.getElementById('ux-campo-modal');
    var input = document.getElementById('ux-campo-modal-texto');
    var tEl = document.getElementById('ux-campo-modal-titulo');
    if (!modal) return;
    tEl.textContent = titulo;
    input.value = '';
    input.placeholder = placeholder || '';
    modal.style.display = '';
    var ok = document.getElementById('ux-campo-modal-ok');
    var cancel = document.getElementById('ux-campo-modal-cancel');
    function close() {
      modal.style.display = 'none';
      ok.onclick = null;
      cancel.onclick = null;
    }
    ok.onclick = function() {
      var v = input.value;
      close();
      onConfirm(v);
    };
    cancel.onclick = close;
  }

  function init() {
    startClock();
    updateNet();
    window.addEventListener('online',  function() { updateNet(); syncQueue(); });
    window.addEventListener('offline', function() { updateNet(); });
    carregarAgenda();
    updatePending();

    document.getElementById('ux-campo-checkin').addEventListener('click', function() {
      enqueue({ tipo: 'checkin', ts: Date.now(), payload: {} });
      alert('Check-in registrado (na fila). Sincronize quando online.');
    });
    document.getElementById('ux-campo-obs').addEventListener('click', function() {
      showModal('Observacao', 'O que aconteceu?', function(texto) {
        if (!texto) return;
        // Tenta pegar a OS selecionada (clicada)
        var sel = document.querySelector('.ux-campo-os-card.ux-campo-selected');
        var osId = sel ? sel.getAttribute('data-os-id') : null;
        enqueue({ tipo: 'observacao', ts: Date.now(), payload: { os_id: osId, texto: texto } });
        alert('Observacao salva na fila.');
      });
    });
    document.getElementById('ux-campo-foto').addEventListener('click', function() {
      document.getElementById('ux-campo-foto-input').click();
    });
    document.getElementById('ux-campo-foto-input').addEventListener('change', function() {
      // Processamento: mobile-camera.js ja comprime e mostra preview
      enqueue({ tipo: 'foto', ts: Date.now(), payload: { filename: 'campo_' + Date.now() + '.jpg' } });
      alert('Foto adicionada a fila.');
    });
    document.getElementById('ux-campo-sync').addEventListener('click', syncQueue);

    // Marcar OS como selecionada
    document.addEventListener('click', function(e) {
      var card = e.target.closest('.ux-campo-os-card');
      if (!card) return;
      document.querySelectorAll('.ux-campo-os-card').forEach(function(c) { c.classList.remove('ux-campo-selected'); });
      card.classList.add('ux-campo-selected');
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.UX.campo = { sync: syncQueue, enqueue: enqueue, queue: getQueue };
})(window, jQuery);
