<?php
require_once __DIR__ . '/bootstrap.php';

if (
    app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST'
    || !app_verify_csrf(app_request_value($_POST, '_csrf', ''))
) {
    http_response_code(403);
    header('Location: /gestion/informes/?error=sesion');
    exit;
}

if (!($reports_database instanceof mysqli)) {
    http_response_code(503);
    header('Location: /gestion/informes/?error=database');
    exit;
}

$report_id = app_request_int($_POST, 'id_informe', 0);
$name = reports_string_limit(app_request_value($_POST, 'nombre', ''), 160);
$description = reports_string_limit(
    app_request_value($_POST, 'descripcion', ''),
    500
);
$shared = app_request_int($_POST, 'compartido', 0) === 1 ? 1 : 0;
$definition_raw = app_request_value($_POST, 'definicion', '');
$errors = array();
$definition = reports_normalize_definition(
    $reports_database,
    $definition_raw,
    $errors
);

if ($name === '') {
    $errors[] = 'Escribe un nombre para el informe.';
}

if ($definition === false || count($errors) > 0) {
    $_SESSION['panel_flash'] = array(
        'type' => 'error',
        'message' => implode(' ', array_unique($errors)),
    );
    header(
        'Location: /gestion/informes/'
        . ($report_id > 0 ? '?id=' . $report_id : '')
    );
    exit;
}

$definition_json = json_encode(
    $definition,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

if ($report_id > 0) {
    $existing = reports_load_saved(
        $reports_database,
        $report_id,
        (int) $idUsuario
    );
    if (!$existing || !reports_can_manage_saved($existing, $idUsuario)) {
        http_response_code(403);
        header('Location: /gestion/informes/guardados.php?error=permiso');
        exit;
    }

    $statement = mysqli_prepare(
        $reports_database,
        'UPDATE informes_personalizados '
        . 'SET nombre=?, descripcion=?, definicion=?, compartido=?, '
        . 'fecha_actualizacion=NOW() '
        . 'WHERE id_informe=?'
    );
    if ($statement) {
        mysqli_stmt_bind_param(
            $statement,
            'sssii',
            $name,
            $description,
            $definition_json,
            $shared,
            $report_id
        );
    }
} else {
    $creator_id = (int) $idUsuario;
    $statement = mysqli_prepare(
        $reports_database,
        'INSERT INTO informes_personalizados '
        . '(nombre, descripcion, definicion, id_usuario_creador, compartido, '
        . 'fecha_creacion, fecha_actualizacion) '
        . 'VALUES (?, ?, ?, ?, ?, NOW(), NOW())'
    );
    if ($statement) {
        mysqli_stmt_bind_param(
            $statement,
            'sssii',
            $name,
            $description,
            $definition_json,
            $creator_id,
            $shared
        );
    }
}

if (!$statement || !mysqli_stmt_execute($statement)) {
    if ($statement) {
        mysqli_stmt_close($statement);
    }
    $_SESSION['panel_flash'] = array(
        'type' => 'error',
        'message' => 'No se ha podido guardar el informe.',
    );
    header(
        'Location: /gestion/informes/'
        . ($report_id > 0 ? '?id=' . $report_id : '')
    );
    exit;
}

if ($report_id <= 0) {
    $report_id = (int) mysqli_insert_id($reports_database);
}
mysqli_stmt_close($statement);

header(
    'Location: /gestion/informes/guardados.php?guardado=1&id='
    . $report_id
);
