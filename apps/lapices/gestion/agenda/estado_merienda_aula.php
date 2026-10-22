<?php
ob_start();
require('../../configuracion/config.php');
require('../../configuracion/autentifica.php');
require_once('../../configuracion/merienda_aula.php');
ob_end_clean();

header('Content-Type: application/json; charset=UTF-8');

function responder_estado_merienda($datos, $estadoHttp)
{
    if (function_exists('http_response_code')) {
        http_response_code((int) $estadoHttp);
    }
    echo json_encode($datos);
    exit;
}

$idAula = app_request_int($_GET, 'id_aula', 0);
$dia = app_request_int($_GET, 'dia', 0);
if ($dia <= 0) {
    $dia = time();
}
$fecha = date('Y-m-d', $dia);

if ($idAula <= 0) {
    responder_estado_merienda(array('ok' => false, 'visible' => false), 422);
}

$consultaAula = db_query_params(
    'SELECT id_aula,id_centro,id_curso FROM centro_aulas WHERE id_aula=? LIMIT 1',
    'i',
    array($idAula)
);
if (!$consultaAula || db_num_rows($consultaAula) === 0) {
    responder_estado_merienda(array('ok' => false, 'visible' => false), 404);
}

$aula = db_fetch_assoc($consultaAula);
$puedeGestionar = (int) $aula['id_centro'] === (int) $centro_actual
    && app_puede_gestionar_merienda_aula($idUsuario, $nivel_acceso, $idAula, $fecha);

if (!$puedeGestionar) {
    responder_estado_merienda(array('ok' => true, 'visible' => false), 200);
}

responder_estado_merienda(array(
    'ok' => true,
    'visible' => true,
    'active' => app_aula_sin_merienda(
        (int) $aula['id_centro'],
        (int) $aula['id_curso'],
        $idAula,
        $fecha
    ),
    'date' => date('d/m/Y', $dia),
    'csrf' => app_csrf_token()
), 200);
