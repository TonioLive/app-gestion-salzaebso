<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/gestion/comunicacion/lib.php';
app_security_headers();
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, max-age=0');
function family_notices_response($payload, $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit;
}
try {
    $parent = communication_parent_id();
    if ($parent <= 0) { family_notices_response(array('ok'=>false,'message'=>'Inicia sesión para consultar los avisos.'),403); }
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $data = communication_parent_notices($parent, app_request_int($_GET,'before',0));
        $data['more'] = count($data['notices']) > 50;
        if ($data['more']) { array_pop($data['notices']); }
        $data['ok'] = true;
        $data['csrf'] = app_csrf_token();
        family_notices_response($data);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!app_verify_csrf((string) app_request_value($_POST,'_csrf',''))) {
            family_notices_response(array('ok'=>false,'message'=>'Recarga la página para actualizar tu sesión.'),419);
        }
        communication_parent_read($parent, app_request_int($_POST,'id',0));
        family_notices_response(array('ok'=>true));
    }
    family_notices_response(array('ok'=>false,'message'=>'Operación no disponible.'),405);
} catch (DomainException $e) {
    family_notices_response(array('ok'=>false,'message'=>$e->getMessage()),403);
} catch (Exception $e) {
    family_notices_response(array('ok'=>false,'message'=>'No se pueden cargar los avisos en este momento.'),503);
}
