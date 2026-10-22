<?php
ob_start();
require('../../configuracion/config.php');
require('../../configuracion/autentifica.php');
require_once('../../configuracion/merienda_aula.php');
ob_end_clean();

header('Content-Type: application/json; charset=UTF-8');

function responder_merienda_aula($estadoHttp, $datos)
{
    if (function_exists('http_response_code')) {
        http_response_code((int) $estadoHttp);
    }
    echo json_encode($datos);
    exit;
}

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST') {
    responder_merienda_aula(405, array('ok' => false, 'message' => 'Metodo no permitido.'));
}

if (!app_verify_csrf(app_request_value($_POST, '_csrf', ''))) {
    responder_merienda_aula(403, array('ok' => false, 'message' => 'La sesion ha caducado. Actualiza la pagina e intentalo de nuevo.'));
}

$idAula = app_request_int($_POST, 'id_aula', 0);
$fecha = app_request_value($_POST, 'fecha', '');
$accion = app_request_value($_POST, 'accion', '');

if ($idAula <= 0 || !app_fecha_iso_valida($fecha) || !in_array($accion, array('quitar', 'restaurar'), true)) {
    responder_merienda_aula(422, array('ok' => false, 'message' => 'Los datos enviados no son validos.'));
}

$consultaAula = db_query_params(
    'SELECT id_aula,id_centro,id_curso,nombre FROM centro_aulas WHERE id_aula=? LIMIT 1',
    'i',
    array($idAula)
);
if (!$consultaAula || db_num_rows($consultaAula) === 0) {
    responder_merienda_aula(404, array('ok' => false, 'message' => 'No se ha encontrado el aula.'));
}

$aula = db_fetch_assoc($consultaAula);
if ((int) $aula['id_centro'] !== (int) $centro_actual) {
    responder_merienda_aula(403, array('ok' => false, 'message' => 'El aula no pertenece al centro activo.'));
}

if (!app_puede_gestionar_merienda_aula($idUsuario, $nivel_acceso, $idAula, $fecha)) {
    responder_merienda_aula(403, array('ok' => false, 'message' => 'No tienes permiso para cambiar la merienda de esta aula y este dia.'));
}

$resultado = app_actualizar_merienda_aula(
    (int) $aula['id_centro'],
    (int) $aula['id_curso'],
    $idAula,
    $fecha,
    $accion === 'quitar',
    (int) $idUsuario
);
if ($resultado === false) {
    responder_merienda_aula(500, array('ok' => false, 'message' => 'No se ha podido actualizar la merienda del aula.'));
}

$quitada = $accion === 'quitar';
responder_merienda_aula(200, array(
    'ok' => true,
    'active' => $quitada,
    'updated' => (int) $resultado['actualizados'],
    'message' => $quitada
        ? 'La merienda se ha quitado para todo el aula y cocina ya no la contara.'
        : 'La merienda vuelve a estar incluida para todo el aula.'
));
