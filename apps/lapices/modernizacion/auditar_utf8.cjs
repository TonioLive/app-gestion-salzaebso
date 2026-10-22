// Read-only audit of application-owned templates. Run: node modernizacion/auditar_utf8.cjs
const fs = require('node:fs');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const decoder = new TextDecoder('utf-8', {fatal: true});
function owned(p) {
    if (p.split('/').some(part => part.startsWith('.'))) return false;
    if (/^(foro|mailer|src|bootstrap-lightbox|OneSignal-Web-SDK-HTTPS-Integration-Files)\//.test(p)) return false;
    if (/^dashboard\//.test(p) && p !== 'dashboard/html/index.php') return false;
    if (/\/(plugins|bootstrap|dist|fonts|font|vendor|node_modules|minishowcase|facedetection|lightbox)\//i.test(p)) return false;
    if (/\/(?:class\.(?:pdf|ezpdf)(?:_original|\.new)?|fpdf|html2fpdf(?:_site)?|htmltoolkit|tcpdf(?:_[a-z]+)?)\.php$/i.test(p)) return false;
    return !/^pdf2\//.test(p) || /^pdf2\/facturacion\//.test(p);
}
function walk(folder) {
    return fs.readdirSync(folder, {withFileTypes: true}).flatMap(entry => {
        if (entry.name.startsWith('.') || entry.isSymbolicLink()) return [];
        const full = path.join(folder, entry.name);
        return entry.isDirectory() ? walk(full) : [full];
    });
}
const failures = [];
let checked = 0;
for (const full of walk(root)) {
    const relative = path.relative(root, full).replace(/\\/g, '/');
    if (!/\.(php|html?|js|css)$/i.test(relative) || !owned(relative)) continue;
    checked++;
    const bytes = fs.readFileSync(full);
    let source;
    try { source = decoder.decode(bytes); }
    catch (_) { failures.push({file: relative, reason: 'Invalid UTF-8 bytes'}); continue; }
    if (bytes[0] === 0xef && bytes[1] === 0xbb && bytes[2] === 0xbf) failures.push({file: relative, reason: 'UTF-8 BOM'});
    if (/charset\s*[=:]\s*["']?(windows-1252|iso-8859-1|latin1)|encoding\s*=\s*["'](?:windows-1252|iso-8859-1)/i.test(source)) {
        failures.push({file: relative, reason: 'Legacy document charset'});
    }
    if (/[\u00c3\u00c2][\u0080-\u00bf]|\u00c3[\u201c\u201d\u2018\u2019]|\u00e2[\u20ac\u2122\u0153\u017e\u201c\u2013]|\uFFFD/.test(source)) {
        failures.push({file: relative, reason: 'Possible double-encoded text'});
    }
}
console.log(JSON.stringify({checked, failures}, null, 2));
if (failures.length) process.exitCode = 1;
