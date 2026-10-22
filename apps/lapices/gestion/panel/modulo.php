<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once __DIR__ . '/bootstrap.php';

$panel_active_section = '';
$panel_selected_section = false;
$panel_requested_section = isset($_GET['modulo'])
    ? trim((string) $_GET['modulo'])
    : '';

if ($permitido === true && $panel_has_selected_centre) {
    foreach ($panel_sections as $section) {
        if ((string) $section['key'] === $panel_requested_section) {
            $panel_selected_section = $section;
            $panel_active_section = (string) $section['key'];
            $panel_topbar_label = 'Módulo de gestión';
            $panel_topbar_title = (string) $section['title'];
            break;
        }
    }
}

if (
    $permitido === true &&
    $panel_has_selected_centre &&
    $panel_selected_section === false
) {
    http_response_code(404);
}

require __DIR__ . '/views/header.php';

if ($permitido !== true) {
    require __DIR__ . '/views/login.php';
} elseif (!$panel_has_selected_centre) {
    require __DIR__ . '/views/select-centre.php';
} elseif ($panel_selected_section !== false) {
    require __DIR__ . '/views/module.php';
} else {
    ?>
    <section class="module-landing module-landing--missing">
        <span class="eyebrow">Módulo no disponible</span>
        <h1>No tienes acceso a esta sección</h1>
        <p>La opción solicitada no existe o no está habilitada para tu perfil.</p>
        <a class="button" href="/index.php">Volver al inicio</a>
    </section>
    <?php
}

require __DIR__ . '/views/footer.php';
