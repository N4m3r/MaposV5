/**
 * Service Worker - MaposV5 PWA (F5.5)
 *
 * Estrategia:
 *   - HTML (navegacao): network-first, fallback cache
 *   - CSS/JS/imagens: cache-first
 *   - API (index.php/*): network-only (sempre vai ao servidor)
 *
 * Instalacao:
 *   O service worker e registrado em /service-worker.js (raiz do site).
 *   Mude o BASE_PATH se o sistema nao estiver na raiz.
 */

var CACHE_NAME = 'mapos-v5-v1';
var STATIC_CACHE = 'mapos-v5-static-v1';
var BASE_PATH = '/';

// Assets que SEMPRE serao cacheados no install
var PRE_CACHE = [
  BASE_PATH + 'assets/css/mapos.css',
  BASE_PATH + 'assets/css/ux-components.css',
  BASE_PATH + 'assets/js/jquery-3.7.1.min.js',
  BASE_PATH + 'assets/js/shortcut.js',
  BASE_PATH + 'assets/js/funcoesGlobal.js',
  BASE_PATH + 'assets/js/datatables.min.js',
  BASE_PATH + 'assets/manifest.json',
  BASE_PATH + 'assets/img/favicon.png',
  BASE_PATH + 'assets/svg/icons.svg',
];

// Install: pre-cache de assets estaticos
self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(STATIC_CACHE).then(function(cache) {
      return cache.addAll(PRE_CACHE).catch(function() {
        // Falha silenciosa - pode ter paths errados
      });
    }).then(function() {
      return self.skipWaiting();
    })
  );
});

// Activate: limpa caches antigos
self.addEventListener('activate', function(event) {
  event.waitUntil(
    caches.keys().then(function(names) {
      return Promise.all(names.map(function(name) {
        if (name !== CACHE_NAME && name !== STATIC_CACHE) {
          return caches.delete(name);
        }
      }));
    }).then(function() {
      return self.clients.claim();
    })
  );
});

// Fetch handler
self.addEventListener('fetch', function(event) {
  var url = new URL(event.request.url);

  // Ignora: outras origens, POST, PUT, DELETE
  if (url.origin !== self.location.origin) return;
  if (event.request.method !== 'GET') return;

  // API: network-only
  if (url.pathname.indexOf('/index.php/') === 0 ||
      url.pathname.indexOf('/api/') === 0) {
    return; // deixa passar direto
  }

  // HTML (navegacao): network-first
  if (event.request.mode === 'navigate' ||
      event.request.headers.get('accept').indexOf('text/html') >= 0) {
    event.respondWith(
      fetch(event.request).then(function(response) {
        // Cacheia copia bem-sucedida
        var copy = response.clone();
        caches.open(CACHE_NAME).then(function(cache) {
          cache.put(event.request, copy).catch(function() {});
        });
        return response;
      }).catch(function() {
        // Offline: tenta cache
        return caches.match(event.request).then(function(cached) {
          return cached || new Response(
            '<h1>Sem conexao</h1><p>Voce esta offline. Verifique sua internet.</p>',
            { status: 503, headers: { 'Content-Type': 'text/html; charset=utf-8' } }
          );
        });
      })
    );
    return;
  }

  // Assets estaticos: cache-first
  event.respondWith(
    caches.match(event.request).then(function(cached) {
      if (cached) return cached;
      return fetch(event.request).then(function(response) {
        if (response.ok) {
          var copy = response.clone();
          caches.open(STATIC_CACHE).then(function(cache) {
            cache.put(event.request, copy).catch(function() {});
          });
        }
        return response;
      });
    })
  );
});
