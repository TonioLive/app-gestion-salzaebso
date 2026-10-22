<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once __DIR__ . '/lib.php';
app_security_headers();
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, max-age=0');

function communication_response($payload, $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit;
}

try {
    if (!communication_staff_access()) { communication_response(array('ok'=>false,'message'=>'Inicia sesión con acceso a este centro.'),403); }
    $source = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
    if (app_request_int($source, 'centro_id', 0) !== (int) $centro_actual) {
        communication_response(array('ok'=>false,'message'=>'Has cambiado de centro. Recarga esta página antes de continuar.'),409);
    }
    $action = (string) app_request_value($_GET, 'action', 'chat');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if ($action === 'chat') {
            communication_response(array_merge(array('ok'=>true), communication_chat(app_request_int($_GET,'after',0), app_request_int($_GET,'before',0))));
        }
        if ($action === 'notifications') {
            communication_response(array_merge(array('ok'=>true), communication_notifications(app_request_int($_GET,'after',-1))));
        }
        if ($action === 'notices') {
            $rows = communication_notices(app_request_int($_GET,'archived',0) === 1, app_request_int($_GET,'before',0));
            $more = count($rows) > 50;
            if ($more) { array_pop($rows); }
            communication_response(array('ok'=>true,'notices'=>$rows,'more'=>$more));
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!app_verify_csrf((string) app_request_value($_POST,'_csrf',''))) {
            communication_response(array('ok'=>false,'message'=>'La sesión ha caducado. Recarga la página.'),419);
        }
        if ($action === 'send') {
            $id = communication_send(app_request_value($_POST,'text',''),app_request_value($_POST,'key',''));
            communication_response(array('ok'=>true,'id'=>$id),201);
        }
        if ($action === 'read') {
            communication_mark_chat_read(app_request_int($_POST,'last_id',0));
            communication_response(array('ok'=>true));
        }
        if ($action === 'publish') {
            $id = communication_publish(app_request_value($_POST,'title',''),app_request_value($_POST,'text',''),app_request_value($_POST,'key',''));
            communication_response(array('ok'=>true,'id'=>$id),201);
        }
        if ($action === 'archive') {
            communication_archive(app_request_int($_POST,'id',0),app_request_int($_POST,'archived',1) === 1);
            communication_response(array('ok'=>true));
        }
    }
    communication_response(array('ok'=>false,'message'=>'Operación no disponible.'),405);
} catch (DomainException $error) {
    communication_response(array('ok'=>false,'message'=>$error->getMessage()),403);
} catch (InvalidArgumentException $error) {
    communication_response(array('ok'=>false,'message'=>$error->getMessage()),400);
} catch (Exception $error) {
    communication_response(array('ok'=>false,'message'=>'No se ha podido completar la operación. Inténtalo de nuevo.'),503);
}
