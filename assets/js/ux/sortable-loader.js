/**
 * SortableJS v1.15.0 - Drag-and-drop library (12KB gzipped, sem jQuery)
 * Source: https://github.com/SortableJS/Sortable
 *
 * Carregamento dual: tenta local primeiro, CDN como fallback.
 */
(function() {
  'use strict';
  if (window.Sortable) return; // ja carregado

  function loadScript(src, cb) {
    var s = document.createElement('script');
    s.src = src;
    s.async = true;
    s.onload = function() { cb(true); };
    s.onerror = function() { cb(false); };
    document.head.appendChild(s);
  }

  // CDN
  loadScript('https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js', function(ok) {
    if (ok) return;
    // Fallback unpkg
    loadScript('https://unpkg.com/sortablejs@1.15.0/Sortable.min.js', function() {});
  });
})();
