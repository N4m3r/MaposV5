/**
 * Service Worker para o Portal do Técnico
 * Habilita funcionalidades PWA como cache offline
 */

const CACHE_NAME = 'tecnico-mapos-v1';
const STATIC_ASSETS = [
  '/',
  '/assets/tecnicos/manifest.json',
  '/tecnicos/dashboard',
  '/tecnicos/login'
];

// Instalação do Service Worker
self.addEventListener('install', (event) => {
  console.log('Service Worker instalado');

  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache aberto');
        return cache.addAll(STATIC_ASSETS);
      })
      .catch(err => console.log('Erro ao pre-cache:', err))
  );

  self.skipWaiting();
});

// Ativação do Service Worker
self.addEventListener('activate', (event) => {
  console.log('Service Worker ativado');

  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames
          .filter(name => name !== CACHE_NAME)
          .map(name => caches.delete(name))
      );
    })
  );

  self.clients.claim();
});

// Interceptação de requisições
self.addEventListener('fetch', (event) => {
  // Ignorar requisições para a API
  if (event.request.url.includes('/tecnicos/') &&
      (event.request.method === 'POST' || event.request.url.includes('api_'))) {
    return;
  }

  // Ignorar requisições de fotos (são muito grandes)
  if (event.request.url.includes('/assets/tecnicos/fotos/')) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Retorna do cache se encontrado
        if (response) {
          return response;
        }

        // Senão, busca da rede
        return fetch(event.request)
          .then(networkResponse => {
            // Não cachear se não for bem-sucedido
            if (!networkResponse || networkResponse.status !== 200) {
              return networkResponse;
            }

            // Clona a resposta para cachear
            const responseToCache = networkResponse.clone();

            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return networkResponse;
          })
          .catch(() => {
            // Fallback se offline
            if (event.request.mode === 'navigate') {
              return caches.match('/tecnicos/dashboard');
            }
          });
      })
  );
});

// Sincronização em background
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-fotos') {
    event.waitUntil(syncFotosPendentes());
  }
});

async function syncFotosPendentes() {
  // Aqui seria implementada a lógica para sincronizar
  // fotos enviadas enquanto offline
  console.log('Sincronizando fotos pendentes...');
}

// Notificações push (para avisos de novas OS)
self.addEventListener('push', (event) => {
  const data = event.data.json();

  const options = {
    body: data.message || 'Nova Ordem de Serviço atribuída',
    icon: '/assets/tecnicos/icon-192x192.png',
    badge: '/assets/tecnicos/icon-72x72.png',
    tag: 'nova-os',
    requireInteraction: true,
    actions: [
      { action: 'ver', title: 'Visualizar' },
      { action: 'fechar', title: 'Fechar' }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('Portal do Técnico', options)
  );
});

// Clique na notificação
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'ver') {
    event.waitUntil(
      clients.openWindow('/tecnicos/minhas_os')
    );
  }
});
