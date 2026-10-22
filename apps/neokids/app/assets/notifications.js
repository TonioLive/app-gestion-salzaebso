(() => {
  'use strict';
  const enable = document.getElementById('push-enable');
  const status = document.getElementById('push-status');
  const save = document.getElementById('push-save');
  const disable = document.getElementById('push-disable');
  const badge = document.getElementById('push-badge');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  let registration, config, busy = false;
  const supported = window.isSecureContext && 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
  const prefs = () => Object.fromEntries([...document.querySelectorAll('[data-push-pref]')].map(input => [input.dataset.pushPref, input.checked ? '1' : '0']));
  async function api(data) {
    const response = await fetch('/app/notificaciones-api.php', {method: 'POST', credentials: 'same-origin', body: new URLSearchParams({_csrf: csrf, ...data})});
    const result = await response.json();
    if (!response.ok) throw Error(result.error || 'No se ha podido guardar. Vuelve a iniciar sesión.');
    return result;
  }
  function show(active) {
    badge.textContent = active ? 'Activadas' : 'Sin activar';
    enable.hidden = active; save.hidden = !active; disable.hidden = !active;
  }
  async function subscribe() {
    let sub = await registration.pushManager.getSubscription();
    if (!sub) {
      const key = Uint8Array.from(atob(config.publicKey.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));
      sub = await registration.pushManager.subscribe({userVisibleOnly: true, applicationServerKey: key});
    }
    const json = sub.toJSON();
    await api({action: 'subscribe', endpoint: json.endpoint, p256dh: json.keys.p256dh, auth: json.keys.auth, ...prefs()});
    show(true);status.textContent = config.workerReady ? 'Este dispositivo recibirá los avisos que has elegido.' : 'Permiso guardado. El envío está pendiente de que se active el servicio de notificaciones de la piscina.';
  }
  async function run(action) {
    if (busy) return;busy = true;
    for (const b of [enable, save, disable]) b.disabled = true;
    try { await action(); } catch (e) { status.textContent = e.message || 'No se ha podido activar. Revisa los permisos del navegador.'; }
    finally { busy = false; for (const b of [enable, save, disable]) b.disabled = false; }
  }
  // Explicit sign-out also disables this browser, preventing cross-account notices on shared devices.
  const logout = document.querySelector('form[action="/app/salir.php"]');
  if (logout && supported) logout.addEventListener('submit', async event => {
    event.preventDefault();
    try { const r = await navigator.serviceWorker.getRegistration('/app/'); const sub = r && await r.pushManager.getSubscription();if (sub) { await api({action: 'disable', endpoint: sub.endpoint});await sub.unsubscribe(); } } catch { /* Server also deactivates the session's subscription. */ }
    logout.submit();
  });
  if (!enable) return;
  enable.addEventListener('click', () => run(async () => {
    if (!registration || !config?.publicKey) throw Error('El servicio todavía no está preparado. Recarga esta pantalla.');
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') throw Error('No se han permitido las notificaciones. Puedes cambiar el permiso en los ajustes del navegador.');
    await subscribe();
  }));
  save.addEventListener('click', () => run(subscribe));
  disable.addEventListener('click', () => run(async () => {
    const sub = await registration.pushManager.getSubscription();
    if (sub) { await api({action: 'disable', endpoint: sub.endpoint});await sub.unsubscribe(); }
    show(false);status.textContent = 'Notificaciones desactivadas en este dispositivo.';
  }));
  (async () => {
    if (!supported) { enable.disabled = true; status.textContent = !window.isSecureContext ? 'Este acceso local no admite notificaciones. Para activarlas, abre la app con una conexión HTTPS segura.' : 'Este navegador no admite avisos en segundo plano. En iPhone, añade la app a la pantalla de inicio y ábrela desde su icono.';return; }
    try {
      registration = await navigator.serviceWorker.register('/app/sw.js', {scope: '/app/'});
      registration = await navigator.serviceWorker.ready;
      config = await (await fetch('/app/notificaciones-api.php', {credentials: 'same-origin', cache: 'no-store'})).json();
      if (!config.publicKey) throw Error('La piscina está preparando el servicio de notificaciones.');
      const sub = await registration.pushManager.getSubscription();
      if (sub) {
        const found = await api({action: 'status', endpoint: sub.endpoint});
        if (found.subscription && Number(found.subscription.activa)) {
          document.querySelectorAll('[data-push-pref]').forEach(input => {input.checked = !!Number(found.subscription[input.dataset.pushPref]);});
          show(true);status.textContent = config.workerReady ? 'Notificaciones activadas en este dispositivo.' : 'El servicio de envío no está disponible ahora. Tus preferencias están guardadas.';return;
        }
      }
      show(false);status.textContent = 'Activa los avisos en este dispositivo. Puedes elegir cuáles recibir.';
    } catch (e) {status.textContent = e.message || 'No se puede consultar el servicio. Recarga la pantalla.';}
  })();
})();
