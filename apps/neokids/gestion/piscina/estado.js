(() => {
  'use strict';
  const button = document.getElementById('refresh');
  const message = document.getElementById('refresh-result');
  const overview = document.querySelector('.pool-overview');
  const number = new Intl.NumberFormat('es-ES', {maximumFractionDigits: 2});
  const date = ts => ts ? new Date(ts * 1000).toLocaleString('es-ES', {day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'}) : 'Sin registro';
  let busy = false;
  function render(data) {
    overview.dataset.overallState = data.state;
    document.getElementById('pool-overall-title').textContent = data.title;
    for (const key of ['ok','outside','unknown']) document.getElementById('count-' + key).textContent = data.counts[key];
    for (const [key, metric] of Object.entries(data.metrics)) {
      const card = document.getElementById('metric-' + key);
      if (!card) continue;
      card.dataset.state = metric.state;
      card.querySelector('[data-reading]').textContent = metric.value === null ? '—' : number.format(metric.value);
      card.querySelector('[data-status]').textContent = metric.label;
      const stale = ['stale','offline'].includes(metric.state);
      card.querySelector('[data-age]').textContent = (stale ? 'Último dato guardado · ' : '') + date(metric.time);
      const needle = card.querySelector('.gauge-needle');
      needle.setAttribute('visibility', metric.value === null ? 'hidden' : 'visible');
      const fraction = Math.max(0, Math.min(1, (metric.value - metric.scale_min) / (metric.scale_max - metric.scale_min)));
      needle.setAttribute('transform', `rotate(${-90 + 180 * fraction} 120 118)`);
      card.querySelector('svg').setAttribute('aria-label', `${metric.title}: ${metric.value === null ? 'sin lectura' : number.format(metric.value) + ' ' + metric.unit} · ${metric.label}`);
    }
    document.getElementById('monitor-dot').dataset.active = data.monitor.active ? 'yes' : 'no';
    document.getElementById('monitor-status').textContent = data.monitor.running ? 'Comprobación en curso' : (data.monitor.active ? 'Seguimiento automático activo' : 'Seguimiento automático sin confirmar');
    document.getElementById('last-check').textContent = 'Última comprobación: ' + date(data.monitor.last_run);
    for (const [key, source] of Object.entries(data.sources)) document.getElementById('status-' + key).textContent = source.label + ' · ' + date(source.time);
    document.getElementById('filtration-value').textContent = data.filtration.value;
    document.getElementById('filtration-age').textContent = data.sources.vista.label + ' · ' + date(data.filtration.time);
  }
  function showUnavailable() {
    overview.dataset.overallState = 'unknown';
    document.getElementById('pool-overall-title').textContent = 'No se puede confirmar el estado actual';
    document.getElementById('count-ok').textContent = '0';
    document.getElementById('count-outside').textContent = '0';
    document.getElementById('count-unknown').textContent = '5';
    document.querySelectorAll('.pool-metric').forEach(card => {
      card.dataset.state = 'stale';
      card.querySelector('[data-status]').textContent = 'Sin actualizar';
      card.querySelector('[data-age]').textContent = 'Último dato mostrado · conexión sin confirmar';
    });
    document.getElementById('monitor-status').textContent = 'No se puede consultar el seguimiento';
    document.getElementById('monitor-dot').dataset.active = 'no';
    for (const key of ['water','air','vista']) document.getElementById('status-' + key).textContent = 'No se puede confirmar la conexión.';
    document.getElementById('filtration-age').textContent = 'Último estado mostrado · conexión sin confirmar';
  }
  async function refresh(force = false) {
    if (busy) return;
    busy = true; button.disabled = true;
    if (force) {button.textContent = 'Comprobando…';message.textContent = 'Consultando los equipos de la piscina…';}
    const abort = new AbortController();
    const timeout = setTimeout(() => abort.abort(), force ? 150000 : 15000);
    try {
      const options = {credentials:'same-origin',cache:'no-store',signal:abort.signal};
      if (force) {options.method = 'POST'; options.body = new URLSearchParams({_csrf:button.dataset.csrf});}
      const response = await fetch('lecturas.php', options);
      if (!response.ok || response.redirected) throw new Error('La sesión o la conexión no están disponibles.');
      const data = await response.json();
      if (!data.metrics || !data.monitor) throw new Error('Respuesta no disponible.');
      render(data);
      if (force) message.textContent = data.refresh_status === 'running' ? 'Ya hay una comprobación en curso. El resultado aparecerá aquí.' : (data.refresh_status === 'recent' ? 'Las lecturas se comprobaron hace menos de un minuto.' : 'Comprobación finalizada. ' + data.title + '.');
      else if (!data.monitor.running) message.textContent = '';
    } catch (e) {
      showUnavailable(); message.textContent = 'No se ha podido actualizar. Comprueba la conexión o vuelve a iniciar sesión.';
    } finally {clearTimeout(timeout);busy = false;button.disabled = false;button.textContent = '↻ Comprobar ahora';}
  }
  button.addEventListener('click', () => refresh(true));
  // This only reads the cached result. Equipment polling is performed by the independent ten-minute task.
  setInterval(() => {if (!document.hidden) refresh();}, 30000);
  document.addEventListener('visibilitychange', () => {if (!document.hidden) refresh();});
  refresh();
})();
