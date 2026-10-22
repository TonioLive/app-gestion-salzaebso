<?php
require '../../configuracion/config.php';
require_once __DIR__ . '/password_consulta_lib.php';
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, private, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
try {
    gestion_password_validate_request(app_request_value($_SERVER,'REQUEST_METHOD',''),app_request_value($_POST,'_csrf',''));
    $rows = gestion_password_reveal(app_request_value($_POST,'usuarios',array()), app_request_value($_POST,'confirmacion',''));
    echo json_encode(array('ok'=>true,'usuarios'=>$rows));
    exit;
} catch (OverflowException $error) { http_response_code(429); }
catch (UnexpectedValueException $error) { http_response_code(401); }
catch (DomainException $error) { http_response_code(403); }
catch (InvalidArgumentException $error) { http_response_code(400); }
catch (Exception $error) { http_response_code(503); }
echo json_encode(array('ok'=>false,'mensaje'=>$error->getMessage()));
