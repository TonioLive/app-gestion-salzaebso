<?php
require_once __DIR__.'/configuracion/config.php';
require __DIR__.'/gestion/panel/bootstrap.php';
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="es" class="theme-<?php echo APP_SITE; ?>">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow"><meta name="theme-color" content="<?php echo $pool?'#075985':'#173b35'; ?>">
<title><?php echo app_escape($brand.' · '.$subtitle); ?></title>
<link rel="icon" href="/favicon.ico"><link rel="stylesheet" href="/gestion/panel/assets/panel.css?v=20260923">
<link rel="stylesheet" href="/gestion/panel/assets/site.css?v=20260923">
<script src="/gestion/panel/assets/site.js?v=20260923" defer></script>
</head>
<body class="<?php echo $permitido?'panel-app':'panel-login'; ?>">
<?php if (!$permitido) { ?>
<main class="login-shell">
<section class="login-card" aria-labelledby="login-title">
 <div class="login-card__intro">
  <a class="login-brand" href="/"><span class="brand-symbol" aria-hidden="true"><?php echo $pool?'≈':'SP'; ?></span><span><strong><?php echo $brand; ?></strong><small><?php echo $subtitle; ?></small></span></a>
  <div class="login-card__message"><span class="eyebrow">Área de personal</span><h1 id="login-title"><?php echo $pool?'Un buen día<br>empieza en el agua.':'Todo tu centro,<br>en un mismo lugar.'; ?></h1><p><?php echo $pool?'Clases, alumnos y equipo. Todo lo que necesitas para organizar el día a día de tu piscina.':'Organiza la jornada, consulta la agenda y accede a las herramientas de tus escuelas municipales.'; ?></p></div>
  <div class="login-decoration" aria-hidden="true"><span></span><span></span><span></span></div>
  <p class="login-card__support">Acceso exclusivo para personal autorizado.</p>
 </div>
 <div class="login-card__form">
  <div class="login-form__heading"><span class="eyebrow">Bienvenido de nuevo</span><h2>Inicia sesión</h2><p>Accede con tu usuario y contraseña habituales.</p></div>
  <?php if (!empty($_SESSION['login_error'])) { ?><div class="alert alert--error" role="alert"><?php echo app_escape($_SESSION['login_error']); unset($_SESSION['login_error']); ?></div><?php } ?>
  <form class="login-form" method="post" action="/configuracion/inicio_sesion.php">
   <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
   <label class="field"><span>Usuario</span><input name="user" autocomplete="username" maxlength="100" required autofocus></label>
   <label class="field"><span>Contraseña</span><span class="password-field"><input id="password" type="password" name="pass" autocomplete="current-password" maxlength="1024" required><button type="button" data-password-toggle aria-controls="password" aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button></span></label>
   <button class="button button--primary button--full" type="submit">Entrar al panel <span aria-hidden="true">→</span></button>
  </form>
  <p class="login-help"><a href="/app/login.php">¿Eres una familia? Entra en tu app →</a></p><p class="login-help">Si necesitas recuperar el acceso, contacta con la administración de tu centro.</p>
 </div>
</section></main>
<?php } else { ?>
<a href="#contenido-principal" class="skip-link">Saltar al contenido</a>
<div class="app-shell" data-app-shell>
 <aside class="sidebar" id="main-navigation" data-sidebar>
  <a href="/" class="sidebar__brand"><span class="brand-symbol" aria-hidden="true"><?php echo $pool?'≈':'SP'; ?></span><div><strong><?php echo $brand; ?></strong><span><?php echo $subtitle; ?></span></div></a>
  <nav class="sidebar__nav" aria-label="Navegación principal"><a class="nav-link nav-link--active" href="/index.php" aria-current="page"><span class="nav-link__mark">IN</span>Inicio</a>
  <?php foreach ($sections as $section) { ?><a class="nav-link" href="#modulo-<?php echo $section['key']; ?>"><span class="nav-link__mark"><?php echo app_escape(strtoupper(substr($section['key'],0,2))); ?></span><?php echo $section['title']; ?></a><?php } ?>
  <?php if ($centro_actual) { ?><a class="nav-link" href="/jornada.php"><span class="nav-link__mark">AG</span>Agenda y jornada</a><?php } ?></nav>
  <div class="sidebar__footer"><div class="sidebar__user"><span class="avatar"><?php echo app_escape($initial); ?></span><div><strong><?php echo app_escape($nombre); ?></strong><span><?php echo app_escape($nombre_centro_actual); ?></span></div></div>
  <form method="post" action="/configuracion/cerrar_sesion.php"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><button class="sidebar__logout" type="submit">Cerrar sesión</button></form></div>
 </aside>
 <div class="app-main">
  <header class="topbar"><button class="icon-button topbar__menu" type="button" aria-label="Abrir menú" aria-controls="main-navigation" aria-expanded="false" data-menu-toggle><span></span><span></span><span></span></button><div class="topbar__context"><span><?php echo $subtitle; ?></span><strong><?php echo app_escape($nombre_centro_actual?:'Selecciona tu centro'); ?></strong></div><div class="topbar__actions"><span class="topbar-date"><?php echo $today; ?></span><a class="avatar avatar--large" href="/gestion/usuarios/datos_usuario.php" aria-label="Mis datos"><?php echo app_escape($initial); ?></a></div></header>
  <main class="content" id="contenido-principal">
   <section class="welcome<?php echo $pool?' welcome--pool':''; ?>"><div><span class="eyebrow"><?php echo $pool?'Tu piscina, en marcha':'Tu espacio de trabajo'; ?></span><h1><?php echo $greeting.', '.app_escape($nombre); ?>.</h1><p><?php echo $pool?'Todo listo para una nueva jornada en el agua.':'Aquí tienes las herramientas para organizar el día de tu centro.'; ?></p></div><div class="welcome__date"><strong><?php echo date('d'); ?></strong><span><?php echo strtoupper(substr($months[date('n')-1],0,3)); ?></span></div></section>
   <?php if (app_can_change_centre() || !$centro_actual) { ?><section class="centre-switcher"><div><span class="eyebrow">Centro de trabajo</span><h2><?php echo app_escape($nombre_centro_actual?:'Elige un centro para continuar'); ?></h2></div><form method="post" action="/index.php" class="centre-switcher__form"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><label class="sr-only" for="centre">Centro</label><select id="centre" name="id_centro_seleccionado" required><option value="">Selecciona un centro</option><?php foreach ($centres as $centre) { ?><option value="<?php echo (int)$centre['id_centro']; ?>" <?php if ($centro_actual===(int)$centre['id_centro']) echo 'selected'; ?>><?php echo app_escape($centre['nombre_centro']); ?></option><?php } ?></select><button class="button button--primary" type="submit">Abrir centro</button></form></section><?php } ?>
   <?php if ($centro_actual) { ?>
   <section class="quick-access" aria-label="Accesos frecuentes"><?php $quick=array_slice($sections,0,3); foreach ($quick as $n=>$section) { $item=$section['items'][0]; ?><a href="<?php echo app_escape($item['href']); ?>" class="quick-tile"><span class="quick-tile__number">0<?php echo $n+1; ?></span><strong><?php echo app_escape($item['label']); ?></strong><span aria-hidden="true">↗</span></a><?php } ?><a href="/jornada.php" class="quick-tile"><span class="quick-tile__number">HOY</span><strong>Agenda y jornada</strong><span aria-hidden="true">↗</span></a></section>
   <section class="modules"><div class="section-heading"><div><span class="eyebrow">Herramientas</span><h2>Áreas de gestión</h2></div><label class="tool-search"><span class="sr-only">Buscar herramienta</span><input type="search" placeholder="Buscar herramienta…" data-tool-search autocomplete="off"></label></div>
    <p class="search-empty" data-search-empty hidden>No hay herramientas con ese nombre.</p>
    <div class="module-grid"><?php foreach ($sections as $section) { ?><article class="module-card module-card--<?php echo $section['accent']; ?>" id="modulo-<?php echo $section['key']; ?>" data-module><div class="module-card__heading"><span class="module-card__icon"><?php echo app_escape(strtoupper(substr($section['key'],0,2))); ?></span><div><h3><?php echo $section['title']; ?></h3><p><?php echo $section['summary']; ?></p></div></div><div class="module-card__links"><?php foreach ($section['items'] as $item) { ?><a href="<?php echo app_escape($item['href']); ?>"><span><?php echo app_escape($item['label']); ?></span><span aria-hidden="true">→</span></a><?php } ?></div></article><?php } ?></div>
   </section>
   <?php if (!empty($secondary_sections)) { ?><details class="card admin-card"><summary>Más herramientas del equipo</summary><div class="admin-grid"><?php foreach($secondary_sections as $secondary) { foreach($secondary['items'] as $item) { ?><a href="<?php echo app_escape($item['href']); ?>"><strong><?php echo app_escape($item['label']); ?></strong><span><?php echo app_escape($secondary['title']); ?></span></a><?php } } ?></div></details><?php } ?>
   <?php if ($admin_items) { ?><section class="card admin-card"><div class="section-heading"><div><span class="eyebrow">Configuración</span><h2>Administración</h2></div></div><div class="admin-grid"><?php foreach ($admin_items as $item) { ?><a href="<?php echo app_escape($item['link']); ?>"><strong><?php echo app_escape($item['Texto']); ?></strong><span><?php echo app_escape($item['texto_ayuda']); ?></span></a><?php } ?></div></section><?php } ?>
   <?php } ?>
  </main><footer class="app-footer"><span><?php echo $brand.' · '.$subtitle; ?></span><span>Área de personal</span></footer>
 </div><button class="sidebar-backdrop" type="button" aria-label="Cerrar menú" data-sidebar-backdrop></button>
</div>
<?php } ?>
</body></html>
