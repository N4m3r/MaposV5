/**
 * Push Manager (F5.6)
 *
 * 1) Pede permissao ao usuario
 * 2) Inscreve no Push Service (FCM/Mozilla/etc)
 * 3) Envia a subscription para o backend
 * 4) Disponibiliza UX.push.unsubscribe()
 */
(function(window) {
  'use strict';
  if (!window.UX) window.UX = {};
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
    window.UX.push = { supported: false };
    return;
  }

  function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    var raw = window.atob(base64);
    var out = new Uint8Array(raw.length);
    for (var i = 0; i < raw.length; ++i) out[i] = raw.charCodeAt(i);
    return out;
  }

  async function subscribe() {
    if (Notification.permission === 'denied') {
      return { ok: false, reason: 'denied' };
    }
    if (Notification.permission === 'default') {
      const perm = await Notification.requestPermission();
      if (perm !== 'granted') {
        return { ok: false, reason: perm };
      }
    }
    try {
      const reg = await navigator.serviceWorker.ready;
      // Pega chave publica VAPID
      const vapidRes = await fetch((window.BaseUrl || '/') + 'index.php/push/vapid');
      const vapidData = await vapidRes.json();
      if (!vapidData || !vapidData.publicKey) {
        return { ok: false, reason: 'vapid_missing' };
      }
      const sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidData.publicKey),
      });
      // Envia para o backend
      const r = await fetch((window.BaseUrl || '/') + 'index.php/push/subscribe', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(sub),
      });
      const data = await r.json();
      return { ok: !!(data && data.success), reason: (data && data.msg) || 'ok' };
    } catch (e) {
      return { ok: false, reason: String(e) };
    }
  }

  async function unsubscribe() {
    try {
      const reg = await navigator.serviceWorker.ready;
      const sub = await reg.pushManager.getSubscription();
      if (!sub) return { ok: true, msg: 'nenhuma inscricao' };
      // Envia para o backend
      await fetch((window.BaseUrl || '/') + 'index.php/push/unsubscribe', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ endpoint: sub.endpoint }),
      });
      await sub.unsubscribe();
      return { ok: true };
    } catch (e) {
      return { ok: false, reason: String(e) };
    }
  }

  async function status() {
    if (!('serviceWorker' in navigator)) return { supported: false };
    try {
      const reg = await navigator.serviceWorker.ready;
      const sub = await reg.pushManager.getSubscription();
      return {
        supported: true,
        permission: Notification.permission,
        subscribed: !!sub,
      };
    } catch (e) {
      return { supported: true, error: String(e) };
    }
  }

  // Auto-prompt apos 30s se ainda nao perguntou
  function autoPrompt() {
    try {
      if (Notification.permission === 'default') {
        const STORAGE_KEY = 'ux-push-prompted';
        if (localStorage.getItem(STORAGE_KEY) === '1') return;
        setTimeout(async function() {
          const r = await subscribe();
          if (r.ok) {
            localStorage.setItem(STORAGE_KEY, '1');
          }
        }, 30000);
      }
    } catch (e) { /* silencioso */ }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoPrompt);
  } else {
    autoPrompt();
  }

  window.UX.push = {
    supported: true,
    subscribe: subscribe,
    unsubscribe: unsubscribe,
    status: status,
  };
})(window);
