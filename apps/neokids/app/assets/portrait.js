(() => {
  'use strict';

  const orientation = window.screen && window.screen.orientation;
  const root = document.documentElement;
  const blocker = document.createElement('section');
  blocker.className = 'family-portrait-required';
  blocker.hidden = true;
  blocker.tabIndex = -1;
  blocker.setAttribute('role', 'alertdialog');
  blocker.setAttribute('aria-modal', 'true');
  blocker.setAttribute('aria-labelledby', 'family-portrait-title');
  blocker.setAttribute('aria-describedby', 'family-portrait-description');

  const card = document.createElement('div');
  card.className = 'family-portrait-card';
  const icon = document.createElement('span');
  icon.className = 'family-portrait-phone';
  icon.setAttribute('aria-hidden', 'true');
  const title = document.createElement('h2');
  title.id = 'family-portrait-title';
  title.textContent = 'Pon el móvil en vertical';
  const description = document.createElement('p');
  description.id = 'family-portrait-description';
  description.textContent = 'La app de familias se utiliza en vertical. Gira el dispositivo para continuar donde estabas.';
  card.append(icon, title, description);
  blocker.append(card);
  document.body.append(blocker);

  let blocked = false;
  let previousFocus = null;
  let requestingLock = false;
  const originalInert = new Map();

  function isMobileDevice() {
    const ua = navigator.userAgent || '';
    // iPads may identify themselves as a Mac, including with a connected mouse.
    return /Android|iPhone|iPad|iPod|Mobile|Tablet/i.test(ua)
      || (navigator.maxTouchPoints > 1
        && (/Macintosh/i.test(ua) || window.matchMedia('(pointer: coarse)').matches));
  }

  function isLandscape() {
    // Read screen orientation, not viewport height: the keyboard must not block
    // a phone that is still physically in portrait orientation.
    const type = orientation && orientation.type;
    if (typeof type === 'string' && /^(portrait|landscape)-/.test(type)) {
      return type.startsWith('landscape');
    }
    if (typeof window.orientation === 'number') {
      return Math.abs(window.orientation) % 180 === 90;
    }
    if (window.screen && window.screen.width > 0 && window.screen.height > 0) {
      return window.screen.width > window.screen.height;
    }
    // Very old browsers: preserve the previous state while typing.
    const active = document.activeElement;
    if (active && active.matches('input, textarea, select, [contenteditable="true"]')) return blocked;
    return window.matchMedia('(orientation: landscape)').matches;
  }

  function update() {
    const next = isMobileDevice() && isLandscape();
    if (next === blocked) return;
    blocked = next;
    root.classList.toggle('family-portrait-blocked', blocked);
    blocker.hidden = !blocked;
    if (blocked) {
      previousFocus = document.activeElement;
      for (const element of document.body.children) {
        if (element === blocker || /^(SCRIPT|STYLE|LINK)$/.test(element.tagName)) continue;
        originalInert.set(element, element.inert);
        element.inert = true;
      }
      blocker.focus({preventScroll: true});
    } else {
      for (const [element, inert] of originalInert) element.inert = inert;
      originalInert.clear();
      if (previousFocus && previousFocus.isConnected && previousFocus !== document.body) {
        previousFocus.focus({preventScroll: true});
      }
      previousFocus = null;
    }
  }

  async function requestPortraitLock() {
    if (!isMobileDevice() || document.hidden || requestingLock
      || !orientation || typeof orientation.lock !== 'function') return;
    requestingLock = true;
    try {
      await orientation.lock('portrait');
    } catch {
      // Browsers may require an installed app or disallow locking. The guard
      // above still requires portrait; do not force fullscreen or lose input.
    } finally {
      requestingLock = false;
      update();
    }
  }

  if (orientation && typeof orientation.addEventListener === 'function') {
    orientation.addEventListener('change', update);
  }
  window.addEventListener('orientationchange', update);
  window.addEventListener('resize', update);
  window.addEventListener('pageshow', () => { update(); requestPortraitLock(); });
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) { update(); requestPortraitLock(); }
  });
  document.addEventListener('pointerup', requestPortraitLock, {once: true, passive: true});
  update();
  requestPortraitLock();
})();
