<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';
require_once __DIR__ . '/lib.php';
if (empty($permitido) || !$panel_has_selected_centre) { header('Location: /index.php'); exit; }
if (!communication_staff_access()) { http_response_code(403); exit('No tienes acceso a esta sección.'); }
$isChat = isset($communicationMode) && $communicationMode === 'chat';
$panel_active_section = 'mensajes';
$panel_topbar_label = 'Comunicación del centro';
$panel_topbar_title = $isChat ? 'Chat del equipo' : 'Avisos a familias';
$panel_extra_styles = array('/gestion/comunicacion/assets/comunicacion.css?v=20261005.1');
$panel_extra_scripts = array('/gestion/comunicacion/assets/comunicacion.js?v=20261005.1');
header('Content-Type: text/html; charset=UTF-8');
require dirname(__DIR__) . '/panel/views/header.php';
?>
<section class="comms" data-comms data-mode="<?=$isChat ? 'chat' : 'notices'?>" data-centre="<?=(int)$centro_actual?>" data-csrf="<?=app_escape(app_csrf_token())?>" data-can-publish="<?=communication_can_publish() ? '1' : '0'?>">
    <header class="comms-hero">
        <div><span class="comms-eyebrow"><?=app_escape($nombre_centro_actual)?></span><h1><?=$isChat ? 'El equipo, en contacto.' : 'Las novedades, a mano.'?></h1><p><?=$isChat ? 'Un espacio para hablar, coordinaros y seguir el día a día del centro.' : 'Publica avisos que todas las familias de este centro podrán consultar en su app.'?></p></div>
        <span class="comms-hero-icon" aria-hidden="true"><?=$isChat ? '☏' : '◎'?></span>
    </header>
    <nav class="comms-tabs" aria-label="Comunicación">
        <a href="/herramientas/enviar_mensaje.php" <?=$isChat ? '' : 'aria-current="page"'?>>Avisos a familias</a>
        <a href="/herramientas/enviar_mensaje_edu.php" <?=$isChat ? 'aria-current="page"' : ''?>>Chat del equipo</a>
        <a href="/gestion/mensajes/mensajeria.php">Conversaciones con familias</a>
    </nav>
    <p class="comms-error" role="alert" data-error hidden></p>
    <?php if ($isChat) { ?>
    <div class="team-chat">
        <header class="team-chat-heading"><div><strong>Equipo de <?=app_escape($nombre_centro_actual)?></strong><small data-chat-status>Conectando…</small></div><button class="comms-button comms-button--quiet" type="button" data-enable-notifications>Activar notificaciones</button></header>
        <p class="comms-notification-status" data-notification-status aria-live="polite"></p>
        <button type="button" class="comms-older" data-older hidden>Ver mensajes anteriores</button>
        <div class="team-chat-stream" data-chat-stream role="log" aria-label="Conversación del equipo" aria-live="polite" aria-relevant="additions"><p class="comms-empty">Cargando conversación…</p></div>
        <button type="button" class="comms-new-messages" data-new-messages hidden>Hay mensajes nuevos ↓</button>
        <form class="team-chat-composer" data-chat-form><label class="sr-only" for="team-message">Mensaje para el equipo</label><textarea id="team-message" name="text" rows="2" maxlength="4000" placeholder="Escribe al equipo…" required></textarea><button class="comms-button" type="submit">Enviar</button><small>Ctrl + Intro para enviar · Cada mensaje avisa al resto del equipo.</small></form>
    </div>
    <?php } else { ?>
    <div class="notice-layout">
        <?php if (communication_can_publish()) { ?>
        <aside class="notice-compose"><span class="comms-eyebrow">Un aviso para todas las familias</span><h2>Qué necesitas comunicar</h2><p>Se publicará en la app de las familias de <strong><?=app_escape($nombre_centro_actual)?></strong>.</p>
            <form data-notice-form><label for="notice-title">Título<input id="notice-title" name="title" maxlength="160" placeholder="Por ejemplo: excursión del viernes" required></label><label for="notice-text">Aviso<textarea id="notice-text" name="text" rows="8" maxlength="8000" placeholder="Cuenta lo importante e incluye las fechas y lo que las familias necesitan preparar." required></textarea></label><p class="notice-audience">Visible para todos los familiares con alumnos activos en este centro.</p><button class="comms-button" type="submit">Publicar aviso</button><p role="status" data-publish-status></p></form>
        </aside>
        <?php } ?>
        <div class="notice-board"><header class="notice-board-heading"><div><span class="comms-eyebrow">Tablón del centro</span><h2>Avisos publicados</h2></div><label class="sr-only" for="notice-filter">Estado de los avisos</label><select id="notice-filter" data-notice-filter><option value="0">Publicados</option><option value="1">Archivados</option></select></header><div data-notice-list aria-live="polite"><p class="comms-empty">Cargando avisos…</p></div><button class="comms-older" type="button" data-notices-more hidden>Ver avisos anteriores</button></div>
    </div>
    <?php } ?>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
