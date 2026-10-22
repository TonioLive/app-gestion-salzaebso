<?php
ob_start();
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once __DIR__ . '/bootstrap.php';
ob_end_clean();

app_security_headers();
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, private, max-age=0');
header('Pragma: no-cache');

function global_search_response($payload, $status)
{
    http_response_code((int) $status);
    echo json_encode($payload);
    exit;
}

function global_search_length($value)
{
    return function_exists('mb_strlen')
        ? mb_strlen($value, 'UTF-8')
        : strlen($value);
}

function global_search_substr($value, $length)
{
    return function_exists('mb_substr')
        ? mb_substr($value, 0, $length, 'UTF-8')
        : substr($value, 0, $length);
}

function global_search_normalize($value)
{
    $value = html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
    $value = function_exists('mb_strtolower')
        ? mb_strtolower($value, 'UTF-8')
        : strtolower($value);
    $value = strtr($value, array(
        'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
        'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
        'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
        'ñ' => 'n', 'ç' => 'c',
    ));
    $value = preg_replace('/[^a-z0-9]+/', ' ', $value);
    return trim(preg_replace('/\s+/', ' ', $value));
}

function global_search_terms($query)
{
    $parts = preg_split('/[\s,;]+/u', trim($query));
    $terms = array();
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        $terms[] = global_search_substr($part, 40);
        if (count($terms) >= 4) {
            break;
        }
    }
    return $terms;
}

function global_search_bind($statement, $types, $parameters)
{
    if ($types === '') {
        return true;
    }

    $arguments = array($types);
    foreach ($parameters as $index => $value) {
        $arguments[] = &$parameters[$index];
    }

    return call_user_func_array(array($statement, 'bind_param'), $arguments);
}

function global_search_statement_rows($statement)
{
    $rows = array();
    if (function_exists('mysqli_stmt_get_result')) {
        $result = mysqli_stmt_get_result($statement);
        if ($result instanceof mysqli_result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            mysqli_free_result($result);
            return $rows;
        }
    }

    $metadata = mysqli_stmt_result_metadata($statement);
    if (!$metadata) {
        return $rows;
    }

    $current = array();
    $bindings = array();
    while ($field = mysqli_fetch_field($metadata)) {
        $current[$field->name] = null;
        $bindings[] = &$current[$field->name];
    }
    mysqli_free_result($metadata);
    call_user_func_array(array($statement, 'bind_result'), $bindings);

    while (mysqli_stmt_fetch($statement)) {
        $copy = array();
        foreach ($current as $key => $value) {
            $copy[$key] = $value;
        }
        $rows[] = $copy;
    }

    return $rows;
}

function global_search_query($database, $sql, $types, $parameters)
{
    $statement = mysqli_prepare($database, $sql);
    if (!$statement) {
        return array();
    }

    if (!global_search_bind($statement, $types, $parameters)) {
        mysqli_stmt_close($statement);
        return array();
    }

    if (!mysqli_stmt_execute($statement)) {
        mysqli_stmt_close($statement);
        return array();
    }

    $rows = global_search_statement_rows($statement);
    mysqli_stmt_close($statement);
    return $rows;
}

function global_search_matches($haystack, $terms)
{
    $haystack = global_search_normalize($haystack);
    foreach ($terms as $term) {
        if (strpos($haystack, global_search_normalize($term)) === false) {
            return false;
        }
    }
    return true;
}

function global_search_option_score($title, $haystack, $query, $terms)
{
    $title = global_search_normalize($title);
    $haystack = global_search_normalize($haystack);
    $query = global_search_normalize($query);
    $score = 0;

    if ($title === $query) {
        $score += 200;
    } elseif (strpos($title, $query) === 0) {
        $score += 120;
    } elseif (strpos($title, $query) !== false) {
        $score += 80;
    }

    foreach ($terms as $term) {
        $term = global_search_normalize($term);
        if (strpos($title, $term) !== false) {
            $score += 18;
        } elseif (strpos($haystack, $term) !== false) {
            $score += 6;
        }
    }

    return $score;
}

if ($permitido !== true) {
    global_search_response(array('ok' => false, 'message' => 'Sesión no válida.'), 401);
}

if (!$panel_has_selected_centre) {
    global_search_response(array('ok' => false, 'message' => 'Selecciona un centro antes de buscar.'), 409);
}

if (!$panel_database_available) {
    global_search_response(array('ok' => false, 'message' => 'La búsqueda no está disponible.'), 503);
}

$query = trim((string) app_request_value($_GET, 'q', ''));
$query = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $query);
$query = trim(preg_replace('/\s+/u', ' ', $query));
if (global_search_length($query) < 2) {
    global_search_response(array('ok' => true, 'groups' => array(), 'total' => 0), 200);
}
$query = global_search_substr($query, 80);
$terms = global_search_terms($query);

$groups = array();
$total = 0;
$can_search_students = panel_has_access('zona_alumnos') || (int) $nivel_acceso === 2;
$student_result_count = 0;

if ($can_search_students && count($terms) > 0) {
    $student_conditions = array();
    $student_types = 'i';
    $student_parameters = array((int) $centro_actual);
    foreach ($terms as $term) {
        $student_conditions[] = '('
            . "CONCAT_WS(' ', u.Nombre, u.Apellido1, u.Apellido2) LIKE ? OR "
            . "CONCAT_WS(' ', u.Apellido1, u.Apellido2, u.Nombre) LIKE ? OR "
            . 'u.DNI LIKE ? OR u.telefono1 LIKE ? OR u.telefono2 LIKE ? OR '
            . 'u.movil LIKE ? OR u.Email LIKE ? OR u.SIP LIKE ? OR '
            . 'CAST(u.id_usuario AS CHAR) LIKE ?'
            . ')';
        $like = '%' . $term . '%';
        for ($index = 0; $index < 9; $index++) {
            $student_types .= 's';
            $student_parameters[] = $like;
        }
    }

    $teacher_filter = '';
    if ((int) $nivel_acceso === 2) {
        $teacher_filter = ' AND EXISTS ('
            . 'SELECT 1 FROM personal_historial ph '
            . 'WHERE ph.id_empleado = ? AND ph.id_aula = a.aula '
            . 'AND (ph.id_escuela = ? OR ph.id_escuela = 0 OR ph.id_escuela IS NULL) '
            . "AND (ph.fecha_inicio IS NULL OR ph.fecha_inicio = '0000-00-00' OR ph.fecha_inicio <= CURDATE()) "
            . "AND (ph.fecha_fin IS NULL OR ph.fecha_fin = '0000-00-00' OR ph.fecha_fin >= CURDATE())"
            . ')';
        $student_types .= 'ii';
        $student_parameters[] = (int) $idUsuario;
        $student_parameters[] = (int) $centro_actual;
    }

    $student_sql = 'SELECT u.id_usuario, u.Nombre, u.Apellido1, u.Apellido2, '
        . 'u.fecha_nacimiento, u.SIP, '
        . "SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT CONCAT(COALESCE(ca.nombre, 'Sin aula'), "
        . "' · ', COALESCE(ac.nombre, '')) ORDER BY a.id_curso DESC SEPARATOR '||'), '||', 1) AS aula_curso, "
        . "MAX(CASE WHEN (a.fecha_matricula IS NULL OR a.fecha_matricula = '0000-00-00' "
        . 'OR a.fecha_matricula <= CURDATE()) AND (a.fecha_baja IS NULL '
        . "OR a.fecha_baja = '0000-00-00' OR a.fecha_baja >= CURDATE()) THEN 1 ELSE 0 END) AS activo "
        . 'FROM usuarios u '
        . 'INNER JOIN alumnos a ON a.id_usuario = u.id_usuario '
        . 'LEFT JOIN centro_aulas ca ON ca.id_aula = a.aula '
        . 'LEFT JOIN cursos_academicos ac ON ac.id_curso = a.id_curso '
        . 'WHERE a.id_centro = ? AND u.tipo_inscripcion = 1 AND '
        . implode(' AND ', $student_conditions)
        . $teacher_filter
        . ' GROUP BY u.id_usuario, u.Nombre, u.Apellido1, u.Apellido2, '
        . 'u.fecha_nacimiento, u.SIP '
        . 'ORDER BY activo DESC, u.Apellido1, u.Apellido2, u.Nombre LIMIT 8';

    $student_rows = global_search_query(
        $panel_database,
        $student_sql,
        $student_types,
        $student_parameters
    );
    $student_items = array();
    foreach ($student_rows as $row) {
        $student_name = trim(
            (string) $row['Apellido1'] . ' '
            . (string) $row['Apellido2'] . ', '
            . (string) $row['Nombre']
        );
        $student_meta = array();
        if (!empty($row['aula_curso'])) {
            $student_meta[] = $row['aula_curso'];
        }
        if (!empty($row['fecha_nacimiento']) && $row['fecha_nacimiento'] !== '0000-00-00') {
            $student_meta[] = 'Nac. ' . date('d/m/Y', strtotime($row['fecha_nacimiento']));
        }
        if (!empty($row['SIP'])) {
            $student_meta[] = 'SIP ' . $row['SIP'];
        }
        $student_items[] = array(
            'title' => $student_name,
            'meta' => implode(' · ', $student_meta),
            'href' => '/alumnos/nuevo_alumno1.php?u=' . (int) $row['id_usuario'],
            'kind' => 'alumno',
            'mark' => 'AL',
        );
    }
    if (count($student_items) > 0) {
        $groups[] = array('key' => 'alumnos', 'label' => 'Alumnos', 'items' => $student_items);
        $student_result_count = count($student_items);
        $total += count($student_items);
    }
}

if (panel_has_access('zona_facturacion') && count($terms) > 0) {
    $service_conditions = array();
    $service_types = '';
    $service_parameters = array();
    foreach ($terms as $term) {
        $service_conditions[] = '(t.ARTICULO LIKE ? OR f.categoria LIKE ? OR CAST(t.ID AS CHAR) LIKE ?)';
        $like = '%' . $term . '%';
        $service_types .= 'sss';
        $service_parameters[] = $like;
        $service_parameters[] = $like;
        $service_parameters[] = $like;
    }
    $service_types .= 'ii';
    $service_parameters[] = (int) $centro_actual;
    $service_parameters[] = (int) $centro_actual;

    $service_sql = 'SELECT t.ID, t.ARTICULO, t.PRECIO_VENTA, t.id_curso, '
        . 'f.categoria, ac.nombre AS curso '
        . 'FROM tarifa t '
        . 'LEFT JOIN tipos_iva_servicios f ON f.id_tipo_iva = t.CATEGORIA '
        . 'LEFT JOIN cursos_academicos ac ON ac.id_curso = t.id_curso '
        . 'WHERE ' . implode(' AND ', $service_conditions)
        . ' AND NOT EXISTS ('
        . 'SELECT 1 FROM material_catalogo_retirado retirado '
        . 'WHERE retirado.id_material=t.ID AND retirado.activo=1'
        . ')'
        . ' AND ('
        . "(COALESCE(TRIM(t.ID_CENTRO_ASISTENCIA), '') = '' "
        . "AND COALESCE(TRIM(t.id_centro_ventas), '') = '') "
        . "OR FIND_IN_SET(?, REPLACE(COALESCE(t.ID_CENTRO_ASISTENCIA, ''), ' ', '')) > 0 "
        . "OR FIND_IN_SET(?, REPLACE(COALESCE(t.id_centro_ventas, ''), ' ', '')) > 0"
        . ') '
        . 'ORDER BY t.ARTICULO LIMIT 8';

    $service_rows = global_search_query(
        $panel_database,
        $service_sql,
        $service_types,
        $service_parameters
    );
    $service_items = array();
    foreach ($service_rows as $row) {
        $service_meta = array();
        if (!empty($row['categoria'])) {
            $service_meta[] = $row['categoria'];
        }
        if (!empty($row['curso'])) {
            $service_meta[] = $row['curso'];
        }
        if (isset($row['PRECIO_VENTA']) && $row['PRECIO_VENTA'] !== '') {
            $service_meta[] = number_format((float) $row['PRECIO_VENTA'], 2, ',', '.') . ' €';
        }
        $service_items[] = array(
            'title' => $row['ARTICULO'],
            'meta' => implode(' · ', $service_meta),
            'href' => '/facturacion/modificar_articulo.php?cod=' . (int) $row['ID'],
            'kind' => 'servicio',
            'mark' => 'SE',
        );
    }
    if (count($service_items) > 0) {
        $groups[] = array(
            'key' => 'servicios',
            'label' => 'Servicios y tarifas',
            'items' => $service_items,
        );
        $total += count($service_items);
    }
}

$option_catalog = array(
    array(
        'title' => 'Inicio',
        'meta' => 'Resumen del centro y accesos principales',
        'href' => '/index.php',
        'keywords' => 'portada resumen inicio principal centro',
    ),
    array(
        'title' => 'Mis datos',
        'meta' => 'Perfil y datos del usuario',
        'href' => '/gestion/usuarios/datos_usuario.php',
        'keywords' => 'perfil usuario contraseña datos personales',
    ),
    array(
        'title' => 'Ayuda y manual de uso',
        'meta' => 'Instrucciones paso a paso para utilizar el programa',
        'href' => '/gestion/ayuda/',
        'keywords' => 'ayuda tutorial manual instrucciones aprender como usar formación',
    ),
);

if ($panel_can_change_centre) {
    $option_catalog[] = array(
        'title' => 'Cambiar de centro',
        'meta' => 'Seleccionar otro centro de trabajo',
        'href' => '/index.php',
        'keywords' => 'escuela seleccionar cambiar centro activo',
    );
}

foreach ($panel_sections as $section) {
    $module_href = '/gestion/panel/modulo.php?modulo=' . rawurlencode($section['key']);
    $option_catalog[] = array(
        'title' => $section['title'],
        'meta' => $section['summary'],
        'href' => $module_href,
        'keywords' => $section['key'] . ' modulo opciones ' . $section['summary'],
    );
    foreach ($section['items'] as $item) {
        $option_catalog[] = array(
            'title' => $item['label'],
            'meta' => $section['title'],
            'href' => panel_safe_link($item['href']),
            'keywords' => $section['title'] . ' ' . $section['summary'],
        );
    }
}

foreach ($panel_admin_items as $admin_item) {
    $option_catalog[] = array(
        'title' => isset($admin_item['Texto']) ? $admin_item['Texto'] : 'Administración',
        'meta' => isset($admin_item['texto_ayuda'])
            ? $admin_item['texto_ayuda']
            : 'Opción de administración',
        'href' => panel_safe_link(isset($admin_item['link']) ? $admin_item['link'] : '#'),
        'keywords' => 'administracion configurar gestion',
    );
}

$seen_options = array();
$matched_options = array();
foreach ($option_catalog as $option) {
    $dedupe_key = $option['href'] . '|' . global_search_normalize($option['title']);
    if (isset($seen_options[$dedupe_key])) {
        continue;
    }
    $seen_options[$dedupe_key] = true;
    $haystack = $option['title'] . ' ' . $option['meta'] . ' ' . $option['keywords'];
    if (!global_search_matches($haystack, $terms)) {
        continue;
    }
    $option['score'] = global_search_option_score(
        $option['title'],
        $haystack,
        $query,
        $terms
    );
    $matched_options[] = $option;
}

usort($matched_options, function ($left, $right) {
    if ($left['score'] === $right['score']) {
        return strcasecmp($left['title'], $right['title']);
    }
    return $left['score'] > $right['score'] ? -1 : 1;
});

$option_items = array();
foreach (array_slice($matched_options, 0, 10) as $option) {
    $option_items[] = array(
        'title' => $option['title'],
        'meta' => $option['meta'],
        'href' => $option['href'],
        'kind' => 'opcion',
        'mark' => 'OP',
    );
}
if (count($option_items) > 0) {
    $groups[] = array(
        'key' => 'opciones',
        'label' => 'Opciones del programa',
        'items' => $option_items,
    );
    $total += count($option_items);
}

global_search_response(array(
    'ok' => true,
    'query' => $query,
    'groups' => $groups,
    'total' => $total,
    'more_href' => $can_search_students && $student_result_count > 0
        ? '/alumnos/busqueda_alumno.php?b=' . rawurlencode($query)
        : '',
), 200);
