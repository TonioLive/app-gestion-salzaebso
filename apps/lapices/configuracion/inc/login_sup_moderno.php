<?php
$legacy_current_path = app_request_value($_SERVER, 'REQUEST_URI', '/');
$legacy_current_path = parse_url($legacy_current_path, PHP_URL_PATH);
if (!is_string($legacy_current_path)) {
    $legacy_current_path = '/';
}

if (!function_exists('legacy_admin_nav_active')) {
    function legacy_admin_nav_active($current_path, $prefix)
    {
        if (is_array($prefix)) {
            foreach ($prefix as $candidate_prefix) {
                if (strpos($current_path, $candidate_prefix) === 0) {
                    return ' is-active';
                }
            }

            return '';
        }

        return strpos($current_path, $prefix) === 0 ? ' is-active' : '';
    }
}

if (!function_exists('legacy_admin_can_access')) {
    function legacy_admin_can_access($permission)
    {
        if ($permission === null || $permission === '') {
            return true;
        }

        if (!empty($GLOBALS['nivel_acceso']) && (int) $GLOBALS['nivel_acceso'] >= 4) {
            return true;
        }

        return !empty($GLOBALS[$permission]);
    }
}

$legacy_navigation = array(
    array('label' => 'Alumnos', 'href' => '/alumnos/gestion_alumnos.php', 'prefix' => '/alumnos/', 'permission' => 'zona_alumnos'),
    array('label' => 'Aulas', 'href' => '/aulas/gestion_aulas.php', 'prefix' => '/aulas/', 'permission' => 'zona_aulas'),
    array('label' => 'Facturaci&oacute;n', 'href' => '/facturacion/gestion_facturacion.php', 'prefix' => '/facturacion/', 'permission' => 'zona_facturacion'),
    array('label' => 'Comedor', 'href' => '/cocina/gestion_comedor.php', 'prefix' => array('/cocina/', '/menu/'), 'permission' => null),
    array('label' => 'Almac&eacute;n', 'href' => '/almacen/gestion_almacen.php', 'prefix' => '/almacen/', 'permission' => 'zona_almacen'),
    array('label' => 'Incidencias', 'href' => '/gestion/incidencias/menu_incidencias.php', 'prefix' => '/gestion/incidencias/', 'permission' => 'zona_incidencias'),
    array('label' => 'Personal', 'href' => '/facturacion/gestion_educadoras.php', 'prefix' => '/gestion/docentes/', 'permission' => 'zona_personal'),
);

if (!empty($permitido) && (int) $nivel_acceso === 2) {
    $legacy_navigation[] = array('label' => 'Mis aulas', 'href' => '/aulas_docentes/mis_aulas.php', 'prefix' => '/aulas_docentes/mis_aulas.php', 'permission' => null);
    $legacy_navigation[] = array('label' => 'Asistencia aula', 'href' => '/aulas_docentes/gestion_aulas.php', 'prefix' => '/aulas_docentes/gestion_aulas.php', 'permission' => null);
    $legacy_navigation[] = array('label' => 'Asistencia comedor', 'href' => '/aulas_docentes/calendario_mes_comedor.php', 'prefix' => '/aulas_docentes/calendario_mes_comedor.php', 'permission' => null);
}

$legacy_can_change_centre = !empty($permitido)
    && ((int) $nivel_acceso >= 3 || !empty($usuario_multicentro));
$legacy_message_center_available = !empty($permitido)
    && !empty($centro_actual)
    && (int) $nivel_acceso >= 2
    && (int) $nivel_acceso < 10
    && (
        (int) $nivel_acceso === 2
        || (int) $nivel_acceso === 3
        || !empty($zona_mensajes)
        || (int) $nivel_acceso >= 4
    );
if ($legacy_message_center_available) {
    $legacy_navigation[] = array('label' => 'Avisos', 'href' => '/herramientas/enviar_mensaje.php', 'prefix' => '/herramientas/enviar_mensaje.php', 'permission' => null);
    $legacy_navigation[] = array('label' => 'Chat del equipo', 'href' => '/herramientas/enviar_mensaje_edu.php', 'prefix' => '/herramientas/enviar_mensaje_edu.php', 'permission' => null);
    $legacy_navigation[] = array(
        'label' => 'Mensajes',
        'href' => '/gestion/mensajes/mensajeria.php',
        'prefix' => '/gestion/mensajes/',
        'permission' => null,
    );
}
$legacy_centres = array();
if ($legacy_can_change_centre) {
    $legacy_centres_sql = (int) $idUsuario === 1
        ? 'SELECT id_centro, nombre_centro, poblacion_centro FROM centros ORDER BY centro_propio DESC, nombre_centro'
        : 'SELECT id_centro, nombre_centro, poblacion_centro FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro';
    $legacy_centres_result = db_query($legacy_centres_sql);
    if ($legacy_centres_result) {
        while ($legacy_centre = db_fetch_assoc($legacy_centres_result)) {
            $legacy_centres[] = $legacy_centre;
        }
        db_free_result($legacy_centres_result);
    }
}
?>
<link rel="stylesheet" href="/gestion/panel/assets/help.css?v=20260903.2" data-lapices-help-style>
<script src="/gestion/panel/assets/help.js?v=20260903.2" defer></script>
<?php if ($legacy_message_center_available) { ?>
    <link rel="stylesheet" href="/gestion/mensajes/assets/notificaciones.css?v=20260908.1">
    <script src="/gestion/mensajes/assets/notificaciones.js?v=20260907.1" defer></script>
<?php } ?>
<header class="legacy-admin-header">
    <div class="legacy-admin-header__main">
        <a class="legacy-admin-brand" href="/index.php" aria-label="L&aacute;pices, inicio">
            <img src="/imagen/logo_redondo.png" alt="" width="42" height="42">
            <span>
                <strong>L&aacute;pices</strong>
                <small>Gesti&oacute;n de centros</small>
            </span>
        </a>

        <?php if (!empty($permitido)) { ?>
            <div class="legacy-admin-context">
                <span class="legacy-admin-context__label">Centro activo</span>
                <?php if ($legacy_can_change_centre && count($legacy_centres) > 0) { ?>
                    <form class="legacy-admin-centre-switcher" method="post" action="/index.php">
                        <input type="hidden" name="_csrf" value="<?= app_escape(app_csrf_token()); ?>">
                        <input type="hidden" name="panel_action" value="change_centre">
                        <label class="sr-only" for="legacy-admin-centre">Cambiar de centro</label>
                        <select id="legacy-admin-centre" name="id_centro_seleccionado" onchange="this.form.submit()">
                            <?php foreach ($legacy_centres as $legacy_centre) { ?>
                                <option
                                    value="<?= (int) $legacy_centre['id_centro']; ?>"
                                    <?= (int) $centro_actual === (int) $legacy_centre['id_centro'] ? 'selected' : ''; ?>
                                >
                                    <?= app_escape($legacy_centre['nombre_centro'] . ' (' . $legacy_centre['poblacion_centro'] . ')'); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <noscript><button type="submit">Cambiar</button></noscript>
                    </form>
                <?php } else { ?>
                    <strong><?= app_escape(!empty($nombre_centro_actual) ? $nombre_centro_actual : 'Sin centro seleccionado'); ?></strong>
                <?php } ?>
            </div>

            <?php if ((int) $centro_actual > 0) {
                $global_search_context = 'legacy';
                require dirname(dirname(__DIR__)) . '/gestion/panel/views/global-search.php';
            } ?>

            <div class="legacy-admin-user">
                <span class="legacy-admin-avatar" aria-hidden="true">
                    <?= app_escape(substr(!empty($nombre_completo) ? $nombre_completo : 'LP', 0, 1)); ?>
                </span>
                <span class="legacy-admin-user__copy">
                    <strong><?= app_escape(!empty($nombre_completo) ? $nombre_completo : 'Personal'); ?></strong>
                    <small><?= app_escape(!empty($poblacion_centro_actual) ? $poblacion_centro_actual : 'Panel de personal'); ?></small>
                </span>
                <?php if ($legacy_message_center_available) {
                    $message_notification_variant = 'legacy';
                    require dirname(dirname(__DIR__)) . '/gestion/mensajes/notificacion_cabecera.php';
                } ?>
                <a
                    class="legacy-admin-action help-trigger"
                    href="/gestion/ayuda/?desde=<?= rawurlencode(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/index.php'); ?>"
                    data-help-open
                    aria-label="Abrir ayuda de esta pantalla"
                ><span class="help-trigger__icon" aria-hidden="true">?</span><span class="help-trigger__label">Ayuda</span></a>
                <a class="legacy-admin-action" href="/index.php">Inicio</a>
                <a class="legacy-admin-action legacy-admin-action--logout" href="/configuracion/cerrar_sesion.php">Salir</a>
            </div>
        <?php } else { ?>
            <form class="legacy-admin-login" method="post" action="/configuracion/inicio_sesion.php">
                <input type="hidden" name="_csrf" value="<?= app_escape(app_csrf_token()); ?>">
                <input type="hidden" name="redirect" value="<?= app_escape($legacy_current_path); ?>">
                <label>
                    <span>Usuario</span>
                    <input type="text" name="user" autocomplete="username" required>
                </label>
                <label>
                    <span>Contrase&ntilde;a</span>
                    <input type="password" name="pass" autocomplete="current-password" required>
                </label>
                <button type="submit">Entrar</button>
            </form>
        <?php } ?>
    </div>

    <?php if (!empty($permitido) && (int) $centro_actual > 0) { ?>
        <nav class="legacy-admin-nav" aria-label="M&oacute;dulos de gesti&oacute;n">
            <a class="<?= $legacy_current_path === '/index.php' ? 'is-active' : ''; ?>" href="/index.php">Resumen</a>
            <?php foreach ($legacy_navigation as $legacy_item) {
                if (!legacy_admin_can_access($legacy_item['permission'])) {
                    continue;
                }
                ?>
                <a
                    class="<?= legacy_admin_nav_active($legacy_current_path, $legacy_item['prefix']); ?>"
                    href="<?= app_escape($legacy_item['href']); ?>"
                ><?= $legacy_item['label']; ?></a>
            <?php } ?>
        </nav>
    <?php } ?>
</header>
<?php if (!empty($permitido) && (int) $centro_actual > 0) { ?>
    <script src="/gestion/panel/assets/global-search.js?v=20260730.1" defer></script>
<?php } ?>
