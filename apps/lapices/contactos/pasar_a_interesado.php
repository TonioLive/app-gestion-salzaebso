<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/configuracion/autentifica_admin.php';
require_once __DIR__ . '/workflow.php';

if (
    app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST'
    || !app_verify_csrf(app_request_value($_POST, '_csrf', ''))
) {
    http_response_code(403);
    exit('La sesión del formulario ha caducado.');
}

$source_table = contactos_workflow_source_table(
    app_request_value($_POST, 'tabla_origen', '')
);
$source_id = app_request_int($_POST, 'id_contacto', 0);
$database = contactos_workflow_database();

if (!($database instanceof mysqli) || $source_table === '' || $source_id <= 0) {
    $_SESSION['panel_flash'] = array(
        'type' => 'error',
        'message' => 'No se ha podido identificar la inscripción web.',
    );
    header('Location: /formulario/listado.php');
    exit;
}

$source = contactos_workflow_fetch_source($database, $source_table, $source_id);
if (!$source) {
    $_SESSION['panel_flash'] = array(
        'type' => 'error',
        'message' => 'La inscripción web ya no está disponible.',
    );
    header('Location: /formulario/listado.php');
    exit;
}

$can_use_other_centre = (int) $nivel_acceso >= 4
    || !empty($usuario_multicentro);
if (
    !$can_use_other_centre
    && (int) $source['id_centro'] !== (int) $centro_actual
) {
    http_response_code(403);
    exit('No tienes acceso al centro de esta inscripción.');
}

$result = contactos_workflow_transfer(
    $database,
    $source_table,
    $source_id,
    (int) $idUsuario
);

if (empty($result['ok'])) {
    $_SESSION['panel_flash'] = array(
        'type' => 'error',
        'message' => isset($result['error'])
            ? $result['error']
            : 'No se ha podido pasar la inscripción a interesados.',
    );
    header('Location: /formulario/listado.php');
    exit;
}

$_SESSION['contactos_flash'] = array(
    'type' => 'success',
    'message' => !empty($result['existing'])
        ? 'Esta inscripción ya estaba vinculada a Contactos interesados.'
        : 'Inscripción trasladada a Contactos interesados con todos sus datos.',
);
header(
    'Location: /contactos/nuevo_contacto.php?id_contacto='
    . (int) $result['id_contacto']
    . '&importado=1'
);
exit;
