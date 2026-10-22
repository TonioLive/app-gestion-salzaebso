'use strict';
// No offline caching of children's data, messages or invoices.
self.addEventListener('install', event => event.waitUntil(self.skipWaiting()));
self.addEventListener('activate', event => event.waitUntil(self.clients.claim()));
self.addEventListener('push', event => {
  let data = {};
  try { data = event.data ? event.data.json() : {}; } catch { /* Generic fallback, without private data. */ }
  event.waitUntil(self.registration.showNotification(data.title || 'Neo Kids', {
    body: data.body || 'Hay novedades en tu app de familias.',
    icon: '/app/assets/icon-192.png', badge: '/app/assets/icon-192.png',
    tag: data.tag || 'neo-kids', data: {url: data.url || '/app/'},
  }));
});
self.addEventListener('notificationclick', event => {
  event.notification.close();
  let url = new URL('/app/', self.location.origin);
  try {
    const candidate = new URL(event.notification.data.url, self.location.origin);
    if (candidate.origin === self.location.origin && candidate.pathname.startsWith('/app/')) url = candidate;
  } catch { /* Keep the safe application URL. */ }
  event.waitUntil(self.clients.openWindow(url.href));
});
