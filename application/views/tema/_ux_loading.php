<?php
/**
 * View parcial: Loading overlay global + JS de interceptacao AJAX.
 * Incluir no rodape, antes do </body>.
 *
 * Mostra spinner em:
 *   1. Toda requisicao AJAX com duracao > 300ms
 *   2. Botoes de submit (form nao-AJAX) - classe .ux-btn.is-loading
 *   3. Links com [data-loading] - opcional
 */
?>
<!-- Loading overlay (Fase 1.5) -->
<div class="ux-loading-overlay" id="ux-loading-overlay" role="status" aria-live="polite" aria-hidden="true">
  <div class="ux-loading-box">
    <div class="ux-loading-spinner" aria-hidden="true"></div>
    <span>Processando...</span>
  </div>
</div>

<script>
(function() {
  'use strict';

  var overlay = document.getElementById('ux-loading-overlay');
  if (!overlay) return;

  var showTimer = null;
  var activeRequests = 0;
  var MIN_DELAY = 300; // ms - so mostra se demorar mais que isso

  function show() {
    clearTimeout(showTimer);
    overlay.classList.add('is-active');
    overlay.setAttribute('aria-hidden', 'false');
  }
  function hide() {
    clearTimeout(showTimer);
    overlay.classList.remove('is-active');
    overlay.setAttribute('aria-hidden', 'true');
  }
  function scheduleShow() {
    clearTimeout(showTimer);
    showTimer = setTimeout(show, MIN_DELAY);
  }

  // Interceptar jQuery AJAX (CI3 usa jQuery por padrao)
  if (window.jQuery) {
    var $ = window.jQuery;
    $(document).ajaxStart(function() {
      activeRequests++;
      scheduleShow();
    }).ajaxStop(function() {
      activeRequests = Math.max(0, activeRequests - 1);
      if (activeRequests === 0) hide();
    }).ajaxError(function() {
      activeRequests = Math.max(0, activeRequests - 1);
      if (activeRequests === 0) hide();
    });
  }

  // Interceptar fetch nativo
  if (window.fetch) {
    var origFetch = window.fetch;
    window.fetch = function() {
      activeRequests++;
      scheduleShow();
      return origFetch.apply(this, arguments)
        .then(function(res) {
          activeRequests = Math.max(0, activeRequests - 1);
          if (activeRequests === 0) hide();
          return res;
        })
        .catch(function(err) {
          activeRequests = Math.max(0, activeRequests - 1);
          if (activeRequests === 0) hide();
          throw err;
        });
    };
  }

  // Form submit nao-AJAX: marcar botao como loading
  document.addEventListener('submit', function(e) {
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    var btn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (btn) {
      btn.classList.add('is-loading');
      btn.setAttribute('data-original-text', btn.innerHTML);
      var label = btn.innerHTML;
      // Trocar texto por "Processando..."
      if (btn.tagName === 'BUTTON') {
        btn.innerHTML = '<span class="ux-loading-spinner" style="display:inline-block;width:12px;height:12px;border-width:2px;margin-right:6px;vertical-align:middle;"></span> Processando...';
      }
      // Reabilitar apos 15s por seguranca (caso o form nao redirecione)
      setTimeout(function() {
        btn.classList.remove('is-loading');
        if (btn.tagName === 'BUTTON' && btn.hasAttribute('data-original-text')) {
          btn.innerHTML = label;
        }
      }, 15000);
    }
  });

  // Links com data-loading="true" tambem disparam
  document.addEventListener('click', function(e) {
    var a = e.target.closest('a[data-loading="true"]');
    if (a) {
      scheduleShow();
      // Garantir que esconda se navegacao falhar (botao voltar)
      setTimeout(hide, 8000);
    }
  });
})();
</script>
