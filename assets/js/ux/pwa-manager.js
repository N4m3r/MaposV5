/**
 * PWA Manager (F5.5)
 * - Registra o service worker
 * - Detecta prompt de instalacao e mostra botao
 * - Adiciona <link rel="manifest"> e meta theme-color
 */
(function() {
  'use strict';

  if (!('serviceWorker' in navigator)) return;

  window.addEventListener('load', function() {
    // Detecta instalacao
    window.addEventListener('beforeinstallprompt', function(e) {
      e.preventDefault();
      window.__pwaPrompt = e;
      // Mostra o botao de instalar se existir
      var btn = document.getElementById('ux-pwa-install');
      if (btn) {
        btn.style.display = '';
        btn.addEventListener('click', function() {
          e.prompt();
          e.userChoice.then(function() {
            btn.style.display = 'none';
            window.__pwaPrompt = null;
          });
        });
      }
    });

    // Registra SW
    navigator.serviceWorker.register('/service-worker.js')
      .then(function(reg) {
        // console.log('[PWA] Service Worker registrado:', reg.scope);
      })
      .catch(function(err) {
        // console.warn('[PWA] Falha ao registrar SW:', err);
      });
  });
})();
