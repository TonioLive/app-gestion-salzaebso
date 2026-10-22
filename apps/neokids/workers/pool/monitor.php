<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
date_default_timezone_set('Europe/Madrid');
require dirname(dirname(__DIR__)).'/gestion/piscina/connector.php';
try{
 $result=pool_monitor_cycle('scheduled');
 // A connection or range problem is a completed check, recorded as such in monitor.json.
 echo json_encode(array('completed'=>true,'state'=>$result['state'],'counts'=>$result['counts'],'checked_at'=>$result['monitor']['last_run'])).PHP_EOL;
}catch(Exception $e){fwrite(STDERR,"No se ha podido completar el registro de la piscina.\n");exit(1);}
