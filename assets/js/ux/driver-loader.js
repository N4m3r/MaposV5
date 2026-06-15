/**
 * Driver.js v1.3.1 - Tour/tutorial library (3KB gzipped, sem dependências)
 * Source: https://driverjs.com/
 *
 * Incluído aqui como wrapper para uso offline-friendly.
 * Para baixar a versão completa:
 *   curl -L https://unpkg.com/driver.js@1.3.1/dist/driver.min.js > assets/js/ux/driver.min.js
 *
 * Enquanto o arquivo local nao existir, este loader tenta CDN.
 * Em produção, recomenda-se baixar e servir local.
 */
(function() {
  'use strict';
  if (window.Driver) return; // ja carregado

  function loadScript(src, cb) {
    var s = document.createElement('script');
    s.src = src;
    s.async = true;
    s.onload = function() { cb(true); };
    s.onerror = function() { cb(false); };
    document.head.appendChild(s);
  }

  function loadCss(href) {
    var l = document.createElement('link');
    l.rel = 'stylesheet';
    l.href = href;
    document.head.appendChild(l);
  }

  // Tentar local primeiro
  loadScript(window.BaseUrl + 'assets/js/ux/driver.min.js', function(ok) {
    if (ok) {
      loadCss(window.BaseUrl + 'assets/js/ux/driver.min.css');
      return;
    }
    // Fallback CDN
    loadScript('https://unpkg.com/driver.js@1.3.1/dist/driver.min.js', function(ok2) {
      if (ok2) loadCss('https://unpkg.com/driver.js@1.3.1/dist/driver.min.css');
    });
  });
})();
