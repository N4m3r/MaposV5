/**
 * Service Worker para PWA
 * MAPOS - Sistema de OS
 */

const CACHE_NAME = 'mapos-v1';
const STATIC_ASSETS = [
  '/',
  '/assets/css/matrix-style.css',
  '/assets/css/matrix-media.css',
  '/assets/css/bootstrap-responsive.min.css',
  '/assets/css/font-awesome.css',
  '/assets/js/matrix.js',
  '/assets/js/matrix.dashboard.js',
  '/assets/img/logo.png',
  '/assets/img/icon-192.png',
  '/assets/img/icon-512.png'
];

// Instalação do Service Worker
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Cache aberto');
        return cache.addAll(STATIC_ASSETS);
      })
      .catch((err) => {
        console.error('Erro ao cachear assets:', err);
      })
  );
  self.skipWaiting();
});

// Ativação e limpeza de caches antigos
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    })
  );
  self.clients.claim();
});

// Interceptação de requisições
self.addEventListener('fetch', (event) => {
  // Estratégia: Network First para APIs, Cache First para assets estáticos
  const { request } = event;
  const url = new URL(request.url);

  // Não intercepta requisições de API
  if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/index.php/api/')) {
    return;
  }

  // Assets estáticos - Cache First
  if (request.destination === 'image' ||
      request.destination === 'style' ||
      request.destination === 'script' ||
      url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico)$/)) {

    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) {
          return cached;
        }
        return fetch(request).then((response) => {
          // Não cacheia respostas de erro
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(request, responseToCache);
          });
          return response;
        });
      })
    );
    return;
  }

  // Páginas HTML - Network First com fallback para cache
  if (request.mode === 'navigate' || request.destination === 'document') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(request, responseClone);
          });
          return response;
        })
        .catch(() => {
          return caches.match(request).then((cached) => {
            if (cached) {
              return cached;
            }
            // Fallback para página offline
            return caches.match('/');
          });
        })
    );
    return;
  }

  // Requisições padrão
  event.respondWith(
    fetch(request).catch(() => caches.match(request))
  );
});

// Sincronização em background
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-emails') {
    event.waitUntil(syncEmails());
  }
});

// Notificações push
self.addEventListener('push', (event) => {
  const data = event.data ? event.data.json() : {};
  const options = {
    body: data.body || 'Nova notificação do MAPOS',
    icon: '/assets/img/icon-192.png',
    badge: '/assets/img/icon-72.png',
    tag: data.tag || 'default',
    requireInteraction: data.requireInteraction || false,
    data: data.data || {}
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'MAPOS', options)
  );
});

// Clique na notificação
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  const { notification } = event;
  const data = notification.data || {};
  const urlToOpen = data.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window' }).then((clientList) => {
      for (const client of clientList) {
        if (client.url === urlToOpen && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(urlToOpen);
      }
    })
  );
});

// Função para sincronizar emails
async function syncEmails() {
  // Implementação para sincronizar emails pendentes
  console.log('Sincronizando emails...');
}
