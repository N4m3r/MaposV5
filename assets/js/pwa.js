/**
 * PWA - Progressive Web App
 * Funcionalidades do MAPOS como PWA
 */

class MAPOSPWA {
  constructor() {
    this.deferredPrompt = null;
    this.isInstallable = false;
    this.init();
  }

  init() {
    this.registerServiceWorker();
    this.setupInstallPrompt();
    this.setupOnlineOffline();
    this.requestNotificationPermission();
  }

  /**
   * Registra o Service Worker
   */
  registerServiceWorker() {
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/assets/js/sw.js')
          .then((registration) => {
            console.log('SW registrado:', registration.scope);

            // Atualiza o SW quando houver nova versão
            registration.addEventListener('updatefound', () => {
              const newWorker = registration.installing;
              newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                  // Nova versão disponível
                  this.showUpdateNotification();
                }
              });
            });
          })
          .catch((error) => {
            console.log('SW falhou:', error);
          });
      });
    }
  }

  /**
   * Configura o prompt de instalação
   */
  setupInstallPrompt() {
    window.addEventListener('beforeinstallprompt', (e) => {
      // Previne o prompt automático
      e.preventDefault();
      this.deferredPrompt = e;
      this.isInstallable = true;
      this.showInstallButton();
    });

    window.addEventListener('appinstalled', () => {
      console.log('PWA instalado');
      this.hideInstallButton();
      this.isInstallable = false;
      this.deferredPrompt = null;
    });
  }

  /**
   * Mostra botão de instalação
   */
  showInstallButton() {
    const installBanner = document.getElementById('install-banner');
    if (installBanner) {
      installBanner.style.display = 'block';
    }
  }

  /**
   * Esconde botão de instalação
   */
  hideInstallButton() {
    const installBanner = document.getElementById('install-banner');
    if (installBanner) {
      installBanner.style.display = 'none';
    }
  }

  /**
   * Dispara a instalação do PWA
   */
  async install() {
    if (!this.deferredPrompt) {
      return;
    }

    this.deferredPrompt.prompt();
    const { outcome } = await this.deferredPrompt.userChoice;

    if (outcome === 'accepted') {
      console.log('Usuário aceitou instalação');
    } else {
      console.log('Usuário recusou instalação');
    }

    this.deferredPrompt = null;
  }

  /**
   * Configura detecção de online/offline
   */
  setupOnlineOffline() {
    window.addEventListener('online', () => {
      this.showNotification('Você está online!', 'success');
      this.syncData();
    });

    window.addEventListener('offline', () => {
      this.showNotification('Você está offline. Algumas funcionalidades podem estar limitadas.', 'warning');
    });
  }

  /**
   * Solicita permissão para notificações
   */
  async requestNotificationPermission() {
    if ('Notification' in window) {
      const permission = await Notification.requestPermission();
      if (permission === 'granted') {
        console.log('Notificações permitidas');
      }
    }
  }

  /**
   * Envia notificação push
   */
  async sendNotification(title, options = {}) {
    if ('Notification' in window && Notification.permission === 'granted') {
      return new Notification(title, {
        icon: '/assets/img/icon-192.png',
        badge: '/assets/img/icon-72.png',
        ...options
      });
    }
  }

  /**
   * Sincroniza dados quando volta online
   */
  async syncData() {
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
      try {
        const registration = await navigator.serviceWorker.ready;
        await registration.sync.register('sync-emails');
        console.log('Sync registrado');
      } catch (err) {
        console.error('Sync falhou:', err);
      }
    }
  }

  /**
   * Mostra notificação toast
   */
  showNotification(message, type = 'info') {
    // Usa notificação nativa se disponível, senão usa toast
    if ('Notification' in window && Notification.permission === 'granted' && document.hidden) {
      this.sendNotification('MAPOS', { body: message });
    } else {
      // Toast fallback
      if (window.$.notify) {
        $.notify({ message }, { type, delay: 3000 });
      }
    }
  }

  /**
   * Mostra notificação de atualização disponível
   */
  showUpdateNotification() {
    const updateBanner = document.createElement('div');
    updateBanner.className = 'update-banner';
    updateBanner.innerHTML = `
      <span>Nova versão disponível!</span>
      <button onclick="location.reload()">Atualizar</button>
    `;
    document.body.appendChild(updateBanner);
  }

  /**
   * Adiciona à tela inicial (iOS Safari)
   */
  addToHomeScreen() {
    // Detecta iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    if (isIOS) {
      alert('Para adicionar à tela inicial:\n\n1. Toque no botão Compartilhar\n2. Role e toque em "Adicionar à Tela de Início"');
    }
  }
}

// Inicializa PWA quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
  window.maposPWA = new MAPOSPWA();
});

// Registra para sincronização de emails
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.ready.then((registration) => {
    // Registra para sincronização periódica se disponível
    if ('periodicSync' in registration) {
      registration.periodicSync.register('sync-emails', {
        minInterval: 15 * 60 * 1000 // 15 minutos
      }).catch((err) => {
        console.log('Periodic sync não suportado:', err);
      });
    }
  });
}
