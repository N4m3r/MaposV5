/**
 * Tippy.js v6.3.7 - Tooltip library (10KB gzipped, sem jQuery)
 * Source: https://atomiks.github.io/tippyjs/
 *
 * Carregamento dual: tenta local primeiro, CDN como fallback.
 *
 * Inclui Popper.js (~7KB) e Tippy.js core - tudo via um unico bundle UMD
 * distribuido em unpkg.
 */
(function() {
  'use strict';
  if (window.tippy) return; // ja carregado

  function loadScript(src, cb) {
    var s = document.createElement('script');
    s.src = src;
    s.async = true;
    s.onload = function() { cb(true); };
    s.onerror = function() { cb(false); };
    document.head.appendChild(s);
  }

  // Tenta CDN primeiro (Tippy e uma lib pequena, CDN e confiavel)
  // Em ambientes offline, baixe:
  //   curl -L https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js > assets/js/ux/popper.min.js
  //   curl -L https://unpkg.com/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js > assets/js/ux/tippy-bundle.umd.min.js
  loadScript(window.BaseUrl + 'assets/js/ux/tippy-bundle.umd.min.js', function(ok) {
    if (ok) return;
    // Fallback CDN
    loadScript('https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js', function(ok2) {
      if (ok2) {
        loadScript('https://unpkg.com/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js', function() {});
      }
    });
  });
})();
