<?php

if (!defined('GESTION_PANEL_BOOTSTRAPPED')) {
    define('GESTION_PANEL_BOOTSTRAPPED', true);
    require_once dirname(dirname(__DIR__)) . '/configuracion/user_preferences.php';
    require_once __DIR__ . '/centre_stats.php';

    function panel_has_access($permission)
    {
        if (!empty($GLOBALS['nivel_acceso']) && (int) $GLOBALS['nivel_acceso'] >= 4) {
            return true;
        }

        return !empty($GLOBALS[$permission]);
    }

    function panel_fetch_all($result)
    {
        $rows = array();
        if (!($result instanceof mysqli_result)) {
            return $rows;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        mysqli_free_result($result);
        return $rows;
    }

    function panel_duration($seconds)
    {
        $seconds = max(0, (int) $seconds);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    function panel_safe_link($link)
    {
        if (!is_string($link) || trim($link) === '') {
            return '#';
        }

        $link = trim($link);
        if (strpos($link, '/') === 0 && strpos($link, '//') !== 0) {
            return $link;
        }

        if (preg_match('#^https?://#i', $link)) {
            return $link;
        }

        return '/' . ltrim($link, '/');
    }

    function panel_initials($name)
    {
        $name = trim((string) $name);
        if ($name === '') {
            return 'LP';
        }

        $parts = preg_split('/\s+/u', $name);
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= function_exists('mb_substr')
                ? mb_substr($part, 0, 1, 'UTF-8')
                : substr($part, 0, 1);
        }

        return function_exists('mb_strtoupper')
            ? mb_strtoupper($initials, 'UTF-8')
            : strtoupper($initials);
    }

    $panel_hour = (int) date('G');
    if ($panel_hour >= 22 || $panel_hour < 5) {
        $panel_greeting = 'Buenas noches';
    } elseif ($panel_hour >= 15) {
        $panel_greeting = 'Buenas tardes';
    } else {
        $panel_greeting = 'Buenos días';
    }

    $panel_months = array(
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre',
    );
    $panel_weekdays = array(
        0 => 'domingo',
        1 => 'lunes',
        2 => 'martes',
        3 => 'miércoles',
        4 => 'jueves',
        5 => 'viernes',
        6 => 'sábado',
    );
    $panel_today_label = ucfirst($panel_weekdays[(int) date('w')])
        . ', ' . date('j')
        . ' de ' . $panel_months[(int) date('n')]
        . ' de ' . date('Y');

    $panel_sections = array();
    $panel_has_selected_centre = $permitido === true && (int) $centro_actual > 0;
    $panel_message_center_available = $panel_has_selected_centre
        && (int) $nivel_acceso >= 2
        && (int) $nivel_acceso < 10
        && (
            (int) $nivel_acceso === 2
            || (int) $nivel_acceso === 3
            || panel_has_access('zona_mensajes')
        );

    if ($panel_has_selected_centre) {
        if ((int) $nivel_acceso === 2) {
            $panel_sections[] = array(
                'key' => 'mi-aula',
                'title' => 'Mi aula',
                'summary' => 'Informacion del aula, alumnado y controles diarios.',
                'accent' => 'blue',
                'items' => array(
                    array('label' => 'Listado de aulas', 'href' => '/aulas_docentes/listado_aulas.php'),
                    array('label' => 'Mis aulas', 'href' => '/aulas_docentes/mis_aulas.php'),
                    array('label' => 'Asistencia del aula', 'href' => '/aulas_docentes/gestion_aulas.php'),
                    array('label' => 'Asistencia al comedor', 'href' => '/aulas_docentes/calendario_mes_comedor.php'),
                ),
            );
        }

        if (panel_has_access('zona_alumnos')) {
            $panel_sections[] = array(
                'key' => 'alumnos',
                'title' => 'Alumnos',
                'summary' => 'Matrículas, familias, alergias y solicitudes.',
                'accent' => 'rose',
                'items' => array(
                    array('label' => 'Gestión de alumnos', 'href' => '/alumnos/gestion_alumnos.php'),
                    array('label' => 'Alergias y datos médicos', 'href' => '/gestion/centros/listado_alergias.php'),
                    array('label' => 'Contactos interesados', 'href' => '/contactos/listado_contactos.php'),
                    array('label' => 'Inscripciones web', 'href' => '/formulario/listado.php'),
                    array('label' => 'Lista de espera', 'href' => '/lista_espera/listas_espera.php'),
                    array('label' => 'Gestión de listados', 'href' => '/gestion/listados/gestion_listados.php'),
                ),
            );
        }

        if (panel_has_access('zona_aulas')) {
            $panel_sections[] = array(
                'key' => 'aulas',
                'title' => 'Aulas',
                'summary' => 'Asistencia, organización y comunicación diaria.',
                'accent' => 'blue',
                'items' => array(
                    array('label' => 'Listado de aulas', 'href' => '/gestion/centros/gestion_aulas.php'),
                    array('label' => 'Asistencia de alumnos', 'href' => '/aulas/gestion_aulas.php'),
                    array('label' => 'Agenda educativa', 'href' => '/gestion/agenda_dia/calendario_programacion.php'),
                    array('label' => 'Avisos a familias', 'href' => '/herramientas/enviar_mensaje.php'),
                ),
            );
        }

        if (panel_has_access('zona_facturacion')) {
            $panel_sections[] = array(
                'key' => 'facturacion',
                'title' => 'Facturación',
                'summary' => 'Facturas, caja, recibos y servicios.',
                'accent' => 'amber',
                'items' => array(
                    array('label' => 'Gestión de facturación', 'href' => '/facturacion/gestion_facturacion.php'),
                    array('label' => 'Caja', 'href' => '/caja/listado_caja.php'),
                    array('label' => 'Servicios y tarifas', 'href' => '/facturacion/gestion_familias.php'),
                    array('label' => 'Informes de facturación', 'href' => '/facturacion/resumen_facturacion.php'),
                ),
            );
        }

        if (panel_has_access('zona_comedor')) {
            $panel_sections[] = array(
                'key' => 'comedor',
                'title' => 'Comedor',
                'summary' => 'Menús, asistencia, cocina y consumos.',
                'accent' => 'green',
                'items' => array(
                    array('label' => 'Gestión de comedor', 'href' => '/cocina/gestion_comedor.php'),
                    array('label' => 'Menús', 'href' => '/menu/gestion_menu.php'),
                    array('label' => 'Asistencia al comedor', 'href' => '/aulas/calendario_mes_comedor.php'),
                    array('label' => 'Preparación de platos', 'href' => '/cocina/preparacion_platos/'),
                    array('label' => 'Imprimir menús', 'href' => '/menu/menus_mes.php'),
                ),
            );
        } else {
            $panel_sections[] = array(
                'key' => 'comedor',
                'title' => 'Comedor',
                'summary' => 'Consulta e imprime los menús que se sirven en el centro.',
                'accent' => 'green',
                'items' => array(
                    array('label' => 'Gestión de comedor', 'href' => '/cocina/gestion_comedor.php'),
                    array('label' => 'Imprimir menús', 'href' => '/menu/menus_mes.php'),
                ),
            );
        }

        if (panel_has_access('zona_almacen')) {
            $panel_sections[] = array(
                'key' => 'almacen',
                'title' => 'Almacén',
                'summary' => 'Suministros, material y pedidos internos.',
                'accent' => 'violet',
                'items' => array(
                    array('label' => 'Almacén general', 'href' => '/almacen/general/'),
                    array('label' => 'Suministros de aulas', 'href' => '/almacen/gestion_almacen.php'),
                    array('label' => 'Suministros de cocina', 'href' => '/cocina/gestion_cocina.php'),
                    array('label' => 'Reservas de material', 'href' => '/gestion/material/gestion_material.php'),
                    array('label' => 'Control de entregas', 'href' => '/gestion/material/control_entregas.php'),
                ),
            );
        }

        if (panel_has_access('zona_incidencias')) {
            $panel_sections[] = array(
                'key' => 'incidencias',
                'title' => 'Incidencias',
                'summary' => 'Seguimiento educativo, accidentes y avisos.',
                'accent' => 'red',
                'items' => array(
                    array('label' => 'Gestión de incidencias', 'href' => '/gestion/incidencias/menu_incidencias.php'),
                    array('label' => 'Mis incidencias', 'href' => '/gestion/incidencias/mis_incidencias.php'),
                    array('label' => 'Histórico anterior', 'href' => '/gestion/incidenciasOLD/menu_incidencias.php'),
                ),
            );
        }

        if (panel_has_access('zona_personal')) {
            $panel_sections[] = array(
                'key' => 'personal',
                'title' => 'Personal',
                'summary' => 'Equipo, contratos, horarios y presencia.',
                'accent' => 'teal',
                'items' => array(
                    array('label' => 'Gestión de personal', 'href' => '/facturacion/gestion_educadoras.php'),
                    array('label' => 'Listado de docentes', 'href' => '/gestion/docentes/listado_docentes.php'),
                    array('label' => 'Contratos y documentos', 'href' => '/gestion/personal/ficha.php'),
                    array('label' => 'Resumen de horas', 'href' => '/gestion/docentes/resumen_horas.php'),
                ),
            );
        }

        if ($panel_message_center_available) {
            $panel_sections[] = array(
                'key' => 'mensajes',
                'title' => 'Mensajes',
                'summary' => 'Comunicación con familias y con el equipo.',
                'accent' => 'sky',
                'items' => array(
                    array('label' => 'Centro de mensajes', 'href' => '/gestion/mensajes/mensajeria.php'),
                    array('label' => 'Avisos a familias', 'href' => '/herramientas/enviar_mensaje.php'),
                    array('label' => 'Chat del equipo', 'href' => '/herramientas/enviar_mensaje_edu.php'),
                    array('label' => 'Exportar contactos', 'href' => '/gestion/contacto/envios/'),
                ),
            );
        }

        if (panel_has_access('zona_pedidos')) {
            $panel_sections[] = array(
                'key' => 'pedidos',
                'title' => 'Pedidos',
                'summary' => 'Validación, consulta y entrega de pedidos.',
                'accent' => 'orange',
                'items' => array(
                    array('label' => 'Validar pedidos del centro', 'href' => '/almacen/pedidos_centro.php'),
                    array('label' => 'Pedidos tramitados', 'href' => '/almacen/calendario_pedidos_centro.php'),
                    array('label' => 'Listas de entrega', 'href' => '/almacen/calendario_pedidos_entregados.php'),
                    array('label' => 'Departamentos', 'href' => '/gestion/centros/gestion_almacenes.php'),
                ),
            );
        }

    }

    $panel_database = false;
    $panel_database_available = false;
    $panel_centres = array();
    $panel_attendance = array();
    $panel_attendance_total = 0;
    $panel_open_attendance = null;
    $panel_admin_items = array();
    $panel_centre_stats = panel_empty_centre_stats();
    $app_user_preferences = app_user_preferences_defaults();
    $app_theme_color = $app_user_preferences['color_principal'];
    $app_dashboard_view = $app_user_preferences['vista_inicio'];
    $app_theme_palette = app_theme_palette($app_theme_color);

    if ($permitido === true) {
        $panel_database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
        $panel_database_available = $panel_database instanceof mysqli;
    }

    if ($panel_database_available) {
        $app_user_preferences = app_load_user_preferences(
            $panel_database,
            isset($idUsuario) ? (int) $idUsuario : 0
        );
        $app_theme_color = $app_user_preferences['color_principal'];
        $app_dashboard_view = $app_user_preferences['vista_inicio'];
        $app_theme_palette = app_theme_palette($app_theme_color);

        $centre_sql = (int) $idUsuario === 1
            ? 'SELECT id_centro, nombre_centro, poblacion_centro FROM centros ORDER BY centro_propio DESC, nombre_centro'
            : 'SELECT id_centro, nombre_centro, poblacion_centro FROM centros WHERE mostrar_en_lapices = 1 ORDER BY centro_propio DESC, nombre_centro';
        $panel_centres = panel_fetch_all(mysqli_query($panel_database, $centre_sql));

        if ($panel_has_selected_centre) {
            $attendance_statement = mysqli_prepare(
                $panel_database,
                'SELECT a.id_asistencia, a.hora_entrada, a.hora_salida, a.id_centro_asistencia, '
                . 'c.nombre_centro FROM asistencia_docentes a '
                . 'LEFT JOIN centros c ON c.id_centro = a.id_centro_asistencia '
                . 'WHERE a.id_usuario = ? AND a.turno = 2 AND a.fecha = ? ORDER BY a.hora_entrada'
            );
            if ($attendance_statement) {
                $today = date('Y-m-d');
                $user_id = (int) $idUsuario;
                mysqli_stmt_bind_param($attendance_statement, 'is', $user_id, $today);
                mysqli_stmt_execute($attendance_statement);
                $attendance_result = mysqli_stmt_get_result($attendance_statement);
                $panel_attendance = panel_fetch_all($attendance_result);
                mysqli_stmt_close($attendance_statement);
            }

            foreach ($panel_attendance as $attendance_row) {
                if (empty($attendance_row['hora_salida'])) {
                    $panel_open_attendance = $attendance_row;
                    continue;
                }

                $panel_attendance_total += max(
                    0,
                    strtotime($attendance_row['hora_salida']) - strtotime($attendance_row['hora_entrada'])
                );
            }

            if ((int) $nivel_acceso >= 4 || !empty($zona_inferior)) {
                $admin_sql = (int) $nivel_acceso >= 4
                    ? 'SELECT link, Texto, texto_ayuda FROM menu_administrador ORDER BY Orden'
                    : 'SELECT link, Texto, texto_ayuda FROM menu_administrador WHERE nivel_acceso = 3 ORDER BY Orden';
                $panel_admin_items = panel_fetch_all(mysqli_query($panel_database, $admin_sql));
                $panel_admin_items_without_cameras = array();
                foreach ($panel_admin_items as $panel_admin_item) {
                    $panel_admin_link = isset($panel_admin_item['link'])
                        ? strtolower((string) $panel_admin_item['link'])
                        : '';
                    $panel_admin_text = isset($panel_admin_item['Texto'])
                        ? strtolower((string) $panel_admin_item['Texto'])
                        : '';
                    if (
                        strpos($panel_admin_link, 'listado_camaras.php') !== false
                        || strpos($panel_admin_text, 'gestión cámaras') !== false
                        || strpos($panel_admin_text, 'gestion camaras') !== false
                    ) {
                        continue;
                    }
                    $panel_admin_items_without_cameras[] = $panel_admin_item;
                }
                $panel_admin_items = $panel_admin_items_without_cameras;
            }

            if ($app_dashboard_view === 'analisis') {
                $panel_centre_stats = panel_fetch_centre_stats(
                    $panel_database,
                    (int) $centro_actual,
                    date('Y-m-d')
                );
            }
        }
    }

    $panel_can_access_gabinete = $permitido === true
        && $panel_has_selected_centre
        && (int) $nivel_acceso >= 3
        && (int) $nivel_acceso < 10
        && panel_has_access('zona_gabinete');

    if ($panel_can_access_gabinete) {
        $panel_has_gabinete_admin_item = false;
        foreach ($panel_admin_items as $panel_admin_item) {
            $panel_admin_item_link = isset($panel_admin_item['link'])
                ? '/' . ltrim(trim((string) $panel_admin_item['link']), '/')
                : '';
            if (
                $panel_admin_item_link === '/gestion/gabinete' ||
                $panel_admin_item_link === '/gestion/gabinete/' ||
                $panel_admin_item_link === '/gestion/gabinete/index.php'
            ) {
                $panel_has_gabinete_admin_item = true;
                break;
            }
        }

        if (!$panel_has_gabinete_admin_item) {
            array_unshift($panel_admin_items, array(
                'link' => '/gestion/gabinete/',
                'Texto' => 'Gabinete psicopedagógico',
                'texto_ayuda' => 'Seguimiento, pruebas, informes y documentación del alumnado.',
            ));
        }
    }

    if (
        $permitido === true
        && $panel_has_selected_centre
        && (int) $nivel_acceso >= 4
        && (int) $nivel_acceso < 10
    ) {
        $panel_report_links = array(
            '/gestion/informes/' => array(
                'Texto' => 'Informes personalizados',
                'texto_ayuda' => 'Crea informes con tablas, columnas, filtros y relaciones a tu medida.',
            ),
            '/gestion/informes/guardados.php' => array(
                'Texto' => 'Informes guardados',
                'texto_ayuda' => 'Ejecuta y exporta configuraciones guardadas con datos actualizados.',
            ),
        );

        foreach (array_reverse($panel_report_links, true) as $panel_report_link => $panel_report_item) {
            $panel_has_report_item = false;
            foreach ($panel_admin_items as $panel_admin_item) {
                $current_admin_link = isset($panel_admin_item['link'])
                    ? '/' . ltrim(trim((string) $panel_admin_item['link']), '/')
                    : '';
                if ($current_admin_link === $panel_report_link) {
                    $panel_has_report_item = true;
                    break;
                }
            }

            if (!$panel_has_report_item) {
                array_unshift($panel_admin_items, array(
                    'link' => $panel_report_link,
                    'Texto' => $panel_report_item['Texto'],
                    'texto_ayuda' => $panel_report_item['texto_ayuda'],
                ));
            }
        }
    }

    if ($permitido === true && $panel_has_selected_centre) {
        $panel_has_personalization_item = false;
        foreach ($panel_admin_items as $panel_admin_item) {
            if (
                isset($panel_admin_item['link']) &&
                strpos((string) $panel_admin_item['link'], '/gestion/panel/personalizacion.php') !== false
            ) {
                $panel_has_personalization_item = true;
                break;
            }
        }

        if (!$panel_has_personalization_item) {
            array_unshift($panel_admin_items, array(
                'link' => '/gestion/panel/personalizacion.php',
                'Texto' => 'Personalización del programa',
                'texto_ayuda' => 'Elige tu color y la vista de inicio que prefieras.',
            ));
        }
    }

    $panel_can_change_centre = $permitido === true
        && ((int) $nivel_acceso >= 3 || !empty($usuario_multicentro));
    $panel_user_initials = panel_initials(isset($nombre_completo) ? $nombre_completo : '');
}
