<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/gestion/comunicacion/lib.php';
if (empty($_SESSION['permitido_padre'])) { header('Location: /app/login.php'); exit; }
if (communication_parent_id() <= 0) { header('Location: /app/login.php'); exit; }
app_security_headers();
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Avisos del centro · Lápices</title><link rel="stylesheet" href="/gestion/comunicacion/assets/comunicacion.css?v=20261005.1"><script src="/gestion/comunicacion/assets/avisos-familias.js?v=20261005.1" defer></script></head>
<body class="family-notices-page family-notices">
    <header class="family-notices-topbar"><a href="/app/index.php"><strong>Lápices</strong> · Tu espacio familiar</a><a href="/app/index.php">Volver al inicio</a></header>
    <main class="family-notices-main" data-family-notices="board">
        <span class="comms-eyebrow">Familias y escuela</span><h1>Avisos del centro</h1><p>Las novedades y recordatorios de los centros de tus hijos, siempre a mano.</p>
        <p role="status" data-family-notice-status>Cargando avisos…</p>
        <div data-family-notice-list></div>
        <button type="button" class="comms-older" data-family-notice-more hidden>Ver avisos anteriores</button>
    </main>
</body></html>
