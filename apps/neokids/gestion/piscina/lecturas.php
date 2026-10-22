<?php
require __DIR__.'/common.php';
pool_require_administration();
require __DIR__.'/connector.php';header('Content-Type: application/json; charset=UTF-8');header('Cache-Control: no-store, private');
$method=app_value($_SERVER,'REQUEST_METHOD');
if($method==='POST'){
 app_check_csrf();session_write_close();
 try{echo json_encode(pool_monitor_cycle('manual'));}catch(Exception $e){http_response_code(503);echo json_encode(array('error'=>'No se ha completado la comprobación. Se conservan las últimas lecturas.'));}exit;
}
if($method!=='GET'){http_response_code(405);header('Allow: GET, POST');exit;}
session_write_close();$source=(string)app_value($_GET,'source');
// Compatibility for existing consumers; GET requests only read the local cache.
if($source!==''){
 if(!in_array($source,array('water','air','vista'),true)){http_response_code(400);exit;}
 echo json_encode(pool_cached_readings($source));exit;
}
echo json_encode(pool_dashboard_cached());
