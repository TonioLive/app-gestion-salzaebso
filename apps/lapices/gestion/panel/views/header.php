<?php
app_security_headers();
$panel_is_authenticated = $permitido === true;
$panel_active_section_key = isset($panel_active_section)
    ? (string) $panel_active_section
    : '';
$panel_current_palette = isset($app_theme_palette)
    ? $app_theme_palette
    : app_theme_palette('#21675A');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="<?php echo app_escape($panel_current_palette['brand']); ?>">
    <title>Gestión Lápices</title>
    <link rel="icon" href="/favicon.ico">
    <link rel="stylesheet" href="/gestion/panel/assets/panel.css?v=20260908.1">
    <link rel="stylesheet" href="/gestion/panel/assets/global-search.css?v=20260730.4">
    <link rel="stylesheet" href="/gestion/panel/assets/help.css?v=20260903.2" data-lapices-help-style>
    <link rel="stylesheet" href="/gestion/panel/assets/table-pagination.css?v=20260908.2">
    <link rel="stylesheet" href="/gestion/panel/assets/orientation-guard.css?v=20260911.1" data-orientation-guard-style>
    <?php if ($panel_is_authenticated) { ?>
        <link rel="stylesheet" href="/gestion/panel/assets/session-timeout.css?v=20260909.1" data-session-timeout-style>
        <script src="/gestion/panel/assets/session-timeout.js?v=20260909.1" defer></script>
    <?php } ?>
    <?php if (!empty($panel_message_center_available)) { ?>
        <link rel="stylesheet" href="/gestion/mensajes/assets/notificaciones.css?v=20260908.1">
    <?php } ?>
    <script src="/gestion/panel/assets/global-search.js?v=20260730.1" defer></script>
    <script src="/gestion/panel/assets/help.js?v=20260903.2" defer></script>
    <script src="/gestion/panel/assets/table-pagination.js?v=20260908.1" defer></script>
    <script src="/gestion/panel/assets/orientation-guard.js?v=20260911.1" defer data-orientation-guard-script></script>
    <?php if (!empty($panel_message_center_available)) { ?>
        <script src="/gestion/mensajes/assets/notificaciones.js?v=20260907.1" defer></script>
    <?php } ?>
    <?php if (!empty($panel_extra_styles) && is_array($panel_extra_styles)) { ?>
        <?php foreach ($panel_extra_styles as $panel_extra_style) { ?>
            <link rel="stylesheet" href="<?php echo app_escape($panel_extra_style); ?>">
        <?php } ?>
    <?php } ?>
    <?php if ($panel_is_authenticated) { ?>
    <style>
        :root {
            --brand: <?php echo app_escape($panel_current_palette['brand']); ?>;
            --brand-dark: <?php echo app_escape($panel_current_palette['brand_dark']); ?>;
            --brand-soft: <?php echo app_escape($panel_current_palette['soft']); ?>;
            --primary: <?php echo app_escape($panel_current_palette['primary']); ?>;
            --primary-dark: <?php echo app_escape($panel_current_palette['primary_dark']); ?>;
            --primary-rgb: <?php echo app_escape($panel_current_palette['rgb']); ?>;
            --primary-contrast: <?php echo app_escape($panel_current_palette['contrast']); ?>;
        }
    </style>
    <?php } ?>
</head>
<body class="<?php
echo $panel_is_authenticated
    ? 'panel-app'
        . (!$panel_has_selected_centre ? ' panel-app--centre-required' : '')
        . (isset($app_dashboard_view) ? ' panel-view--' . app_escape($app_dashboard_view) : '')
        . (!empty($panel_body_class) ? ' ' . app_escape($panel_body_class) : '')
    : 'panel-login';
?>">
<?php if ($panel_is_authenticated) { ?>
    <div class="app-shell" data-app-shell>
        <aside class="sidebar" id="main-navigation" data-sidebar>
            <div class="sidebar__brand">
                <img src="/imagen/logo_redondo.png" alt="" width="48" height="48">
                <div>
                    <strong>Lápices</strong>
                    <span>Gestión de centros</span>
                </div>
            </div>

            <?php if ($panel_has_selected_centre) { ?>
                <nav class="sidebar__nav" aria-label="Navegación principal">
                    <a class="nav-link<?php echo $panel_active_section_key === '' ? ' nav-link--active' : ''; ?>" href="/index.php"<?php echo $panel_active_section_key === '' ? ' aria-current="page"' : ''; ?>>
                        <span class="nav-link__mark">IN</span>
                        <span>Inicio</span>
                    </a>
                    <?php foreach ($panel_sections as $section) { ?>
                        <?php $panel_section_is_active = $panel_active_section_key === (string) $section['key']; ?>
                        <a class="nav-link<?php echo $panel_section_is_active ? ' nav-link--active' : ''; ?>" href="/gestion/panel/modulo.php?modulo=<?php echo rawurlencode($section['key']); ?>"<?php echo $panel_section_is_active ? ' aria-current="page"' : ''; ?>>
                            <span class="nav-link__mark nav-link__mark--<?php echo app_escape($section['accent']); ?>">
                                <?php echo app_escape(function_exists('mb_substr') ? mb_substr($section['title'], 0, 2, 'UTF-8') : substr($section['title'], 0, 2)); ?>
                            </span>
                            <span><?php echo app_escape($section['title']); ?></span>
                        </a>
                    <?php } ?>
                </nav>
            <?php } ?>

            <div class="sidebar__footer">
                <div class="sidebar__user">
                    <span class="avatar"><?php echo app_escape($panel_user_initials); ?></span>
                    <div>
                        <strong><?php echo app_escape(isset($nombre) ? $nombre : 'Usuario'); ?></strong>
                        <span><?php echo app_escape(isset($nombre_centro_actual) && $nombre_centro_actual !== '' ? $nombre_centro_actual : 'Sin centro seleccionado'); ?></span>
                    </div>
                </div>
                <a class="sidebar__logout" href="/configuracion/cerrar_sesion.php">Cerrar sesión</a>
            </div>
        </aside>

        <div class="app-main">
            <header class="topbar">
                <button class="icon-button topbar__menu" type="button" aria-label="Abrir menú" aria-controls="main-navigation" aria-expanded="false" data-menu-toggle>
                    <span></span><span></span><span></span>
                </button>
                <div class="topbar__context">
                    <span><?php echo $panel_has_selected_centre && isset($panel_topbar_label) ? app_escape($panel_topbar_label) : ($panel_has_selected_centre ? 'Panel de gestión' : 'Antes de continuar'); ?></span>
                    <strong>
                        <?php echo $panel_has_selected_centre
                            ? app_escape(isset($panel_topbar_title) ? $panel_topbar_title : $panel_today_label)
                            : 'Selecciona el centro de trabajo'; ?>
                    </strong>
                </div>
                <?php if ($panel_has_selected_centre) {
                    $global_search_context = 'panel';
                    require __DIR__ . '/global-search.php';
                } ?>
                <div class="topbar__actions">
                    <?php if (!empty($panel_message_center_available)) {
                        $message_notification_variant = 'panel';
                        require dirname(dirname(__DIR__)) . '/mensajes/notificacion_cabecera.php';
                    } ?>
                    <a
                        class="help-trigger"
                        href="/gestion/ayuda/?desde=<?php echo rawurlencode(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/index.php'); ?>"
                        data-help-open
                        aria-label="Abrir ayuda de esta pantalla"
                    >
                        <span class="help-trigger__icon" aria-hidden="true">?</span>
                        <span class="help-trigger__label">Ayuda</span>
                    </a>
                    <?php if ($panel_has_selected_centre) { ?>
                        <a class="topbar__home" href="/gestion/usuarios/datos_usuario.php">Mis datos</a>
                        <a class="avatar avatar--large" href="/gestion/usuarios/datos_usuario.php" aria-label="Abrir mis datos">
                            <?php echo app_escape($panel_user_initials); ?>
                        </a>
                    <?php } else { ?>
                        <a class="topbar__home" href="/configuracion/cerrar_sesion.php">Cerrar sesión</a>
                        <span class="avatar avatar--large" aria-hidden="true">
                            <?php echo app_escape($panel_user_initials); ?>
                        </span>
                    <?php } ?>
                </div>
            </header>
            <main class="content" id="contenido-principal">
<?php } ?>
