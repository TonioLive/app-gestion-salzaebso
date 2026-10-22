'use strict';
const fs = require('node:fs');
const path = require('node:path');
const {execFileSync} = require('node:child_process');
const webpush = require('web-push');
const privateDir = path.resolve(__dirname, '../../.private/notifications');
function allowedEndpoint(endpoint) {
  try {
    const u = new URL(endpoint);
    return endpoint.length <= 2048 && u.protocol === 'https:' && !u.username && !u.password && !u.hash && (!u.port || u.port === '443') &&
      (['fcm.googleapis.com', 'updates.push.services.mozilla.com', 'web.push.apple.com'].includes(u.hostname) || /^[a-z0-9-]+\.notify\.windows\.com$/.test(u.hostname));
  } catch { return false; }
}
function bridge(config, input) {
  const args = [...(config.phpArgs || []), path.join(__dirname, 'bridge.php')];
  return JSON.parse(execFileSync(config.php, args, {input: JSON.stringify(input), encoding: 'utf8', timeout: 90000, maxBuffer: 4 * 1024 * 1024, windowsHide: true}));
}
async function deliver(job, send, ack) {
  if (!allowedEndpoint(job.subscription.endpoint)) { await ack({action: 'ack', id: job.id, code: 400}); return 'rejected'; }
  try {
    const response = await send(job.subscription, JSON.stringify(job.payload), {TTL: job.ttl, timeout: 12000, urgency: 'normal', topic: job.payload.tag.replace(/^neo-/, '').slice(0, 32)});
    await ack({action: 'ack', id: job.id, code: response.statusCode || 201});
    return 'accepted';
  } catch (error) {
    await ack({action: 'ack', id: job.id, code: Number(error.statusCode) || 0});
    return 'retry-or-expired';
  }
}
async function main() {
  fs.mkdirSync(privateDir, {recursive: true, mode: 0o700});
  const lock = path.join(privateDir, 'worker.lock');
  let fd;
  try { fd = fs.openSync(lock, 'wx'); }
  catch (e) {
    if (e.code !== 'EEXIST') throw e;
    // Recover only an abandoned lock. A live process retains exclusive ownership.
    try { const pid = Number(fs.readFileSync(lock, 'utf8')); if (pid > 0) { process.kill(pid, 0); return; } } catch (err) { if (err.code !== 'ESRCH' && err.code !== 'ENOENT') return; }
    if (Date.now() - fs.statSync(lock).mtimeMs < 15 * 60 * 1000) return;
    fs.unlinkSync(lock); fd = fs.openSync(lock, 'wx');
  }
  fs.writeFileSync(fd, String(process.pid));
  const status = {at: Math.floor(Date.now() / 1000), ok: false, accepted: 0, other: 0};
  try {
    const config = JSON.parse(fs.readFileSync(path.join(privateDir, 'config.json'), 'utf8'));
    webpush.setVapidDetails(config.subject, config.publicKey, config.privateKey);
    if (process.argv.includes('--check')) {
      if (!bridge(config, {action: 'health'}).ok) throw Error('database');
    } else {
      const jobs = bridge(config, {action: 'jobs'});
      // Bound concurrency to avoid bursts and preserve scheduler overlap protection.
      for (let i = 0; i < jobs.length; i += 5) {
        const results = await Promise.all(jobs.slice(i, i + 5).map(job => deliver(job, webpush.sendNotification.bind(webpush), input => bridge(config, input))));
        for (const r of results) r === 'accepted' ? status.accepted++ : status.other++;
      }
    }
    status.ok = true;
  } catch (e) {
    status.error = 'El proceso de notificaciones no ha completado la consulta. Revisa PHP, MySQL y la configuración.';
    process.exitCode = 1;
  } finally {
    fs.writeFileSync(path.join(privateDir, 'status.json'), JSON.stringify(status));
    fs.closeSync(fd); fs.unlinkSync(lock);
    process.stdout.write(JSON.stringify(status) + '\n');
  }
}
module.exports = {allowedEndpoint, deliver};
if (require.main === module) main().catch(() => { process.stderr.write('No se ha podido iniciar el proceso de notificaciones.\n'); process.exitCode = 1; });
