<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
date_default_timezone_set('Europe/Madrid');require dirname(dirname(__DIR__)).'/configuracion/pool-cli.php';require dirname(dirname(__DIR__)).'/gestion/piscina/admin-alerts.php';
$input=json_decode(file_get_contents('php://stdin'),true);$action=isset($input['action'])?$input['action']:'';
try{
 if($action==='jobs')$result=pool_alert_store(function(&$state){return pool_alert_claim($state,pool_dashboard_cached(),time());});
 elseif($action==='ack')$result=pool_alert_store(function(&$state)use($input){pool_alert_ack($state,(string)$input['id'],(int)$input['code'],time());return array('ok'=>true);});
 else $result=array('ok'=>false);
 echo json_encode($result);
}catch(Exception $e){fwrite(STDERR,"No se ha podido procesar el registro de avisos.\n");exit(1);}
