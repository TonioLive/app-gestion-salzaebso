(() => {
  'use strict';
  const source = document.querySelector('script[src^="/scripts/request-security.js"]');
  if (!source) return;
  const token = source.dataset.csrf;
  const sameOrigin = value => { try { return new URL(value || location.href, location.href).origin === location.origin; } catch (_) { return false; } };
  const protect = form => {
    if (!sameOrigin(form.action) || form.method.toLowerCase() !== 'post') return;
    if (!form.querySelector('input[name="_csrf"]')) { const input = document.createElement('input'); input.type = 'hidden'; input.name = '_csrf'; input.value = token; form.append(input); }
  };
  document.querySelectorAll('form').forEach(protect);
  document.addEventListener('submit', event => protect(event.target), true);
  const submit = HTMLFormElement.prototype.submit;
  HTMLFormElement.prototype.submit = function() { protect(this); return submit.call(this); };
  const open = XMLHttpRequest.prototype.open, send = XMLHttpRequest.prototype.send;
  XMLHttpRequest.prototype.open = function(method, url, ...rest) { if (method.toUpperCase() === 'GET' && sameOrigin(url) && /\/guarda_(?:dato|checkbox|comentarios)[^/]*\.php$/i.test(new URL(url, location.href).pathname)) method = 'POST'; this._appProtect = method.toUpperCase() === 'POST' && sameOrigin(url); return open.call(this, method, url, ...rest); };
  XMLHttpRequest.prototype.send = function(body) { if (this._appProtect) this.setRequestHeader('X-CSRF-Token', token); return send.call(this, body); };
})();
