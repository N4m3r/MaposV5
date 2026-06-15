/**
 * Hammer.js loader (F5.3)
 * Carrega Hammer.js v2.0.8 via CDN uma unica vez
 */
(function(window) {
  'use strict';
  if (window.Hammer) return;

  var loaded = false;
  var loading = false;
  var callbacks = [];

  function load(cb) {
    if (loaded) { if (cb) cb(); return; }
    callbacks.push(cb);
    if (loading) return;
    loading = true;

    var s = document.createElement('script');
    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js';
    s.async = true;
    s.crossOrigin = 'anonymous';
    s.onload = function() {
      loaded = true;
      loading = false;
      callbacks.forEach(function(fn) { try { fn(); } catch(e){} });
      callbacks = [];
    };
    s.onerror = function() {
      loading = false;
      // falhou silenciosamente
    };
    document.head.appendChild(s);
  }

  window.UX.hammer = { load: load, ready: function(cb){ load(cb); } };
})(window);
