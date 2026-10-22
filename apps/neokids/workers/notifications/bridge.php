<?php
if(PHP_SAPI!=='cli')exit;
$root=dirname(dirname(__DIR__));require $root.'/configuracion/pool-cli.php';require $root.'/configuracion/pool-family.php';require $root.'/configuracion/pool-notifications.php';
$input=json_decode(file_get_contents('php://stdin'),true);if(!is_array($input))$input=array();$action=app_value($input,'action');
if($action==='health'){echo json_encode(array('ok'=>(bool)app_rows('SELECT COUNT(*) AS n FROM piscina_push_suscripciones')));exit;}
if($action==='jobs'){
 $only=max(0,(int)app_value($input,'only_subscription',0));pool_push_collect(null,$only);$db=app_db();
 $db->query("UPDATE piscina_push_cola SET estado='caducado' WHERE estado IN ('pendiente','enviando') AND caduca<NOW()");
 $db->query("UPDATE piscina_push_cola SET estado='pendiente' WHERE estado='enviando' AND proximo<NOW()");
 $rows=app_rows("SELECT q.*,s.padre,s.endpoint,s.p256dh,s.auth,s.mensajes,s.avisos,s.facturas,s.clases FROM piscina_push_cola q JOIN piscina_push_suscripciones s ON s.id=q.suscripcion WHERE q.estado='pendiente' AND q.proximo<=NOW() AND q.caduca>NOW() AND s.activa=1 AND (?=0 OR s.id=?) ORDER BY q.id LIMIT 100",'ii',array($only,$only));$jobs=array();
 foreach($rows as $row){
  if(!pool_push_endpoint($row['endpoint'])||!pool_push_still_allowed($row)){db_query_params("UPDATE piscina_push_cola SET estado='cancelado' WHERE id=?",'i',array((int)$row['id']),$db);continue;}
  $jobId=(int)$row['id'];$stmt=$db->prepare("UPDATE piscina_push_cola SET estado='enviando',intentos=intentos+1,proximo=DATE_ADD(NOW(),INTERVAL 10 MINUTE) WHERE id=? AND estado='pendiente'");$stmt->bind_param('i',$jobId);$ok=$stmt->execute();$affected=$stmt->affected_rows;$stmt->close();if(!$ok||$affected!==1)continue;
  $jobs[]=array('id'=>(int)$row['id'],'subscription'=>array('endpoint'=>$row['endpoint'],'keys'=>array('p256dh'=>$row['p256dh'],'auth'=>$row['auth'])),'payload'=>array('title'=>$row['titulo'],'body'=>$row['texto'],'url'=>$row['url'],'tag'=>'neo-'.hash('sha256',$row['evento'])),'ttl'=>max(1,min(86400,strtotime($row['caduca'])-time())));
 }
 echo json_encode($jobs);exit;
}
if($action==='ack'){
 $id=(int)app_value($input,'id');$code=(int)app_value($input,'code');$rows=app_rows('SELECT suscripcion,intentos FROM piscina_push_cola WHERE id=? AND estado=\'enviando\'','i',array($id));
 if($rows){
  if($code>=200&&$code<300)db_query_params("UPDATE piscina_push_cola SET estado='enviado',enviado=NOW(),error_codigo=NULL WHERE id=?",'i',array($id),app_db());
  elseif(in_array($code,array(404,410),true)){
   db_query_params('UPDATE piscina_push_suscripciones SET activa=0,actualizada=NOW() WHERE id=?','i',array((int)$rows[0]['suscripcion']),app_db());
   db_query_params("UPDATE piscina_push_cola SET estado='cancelado',error_codigo=? WHERE suscripcion=? AND estado IN ('pendiente','enviando')",'si',array((string)$code,(int)$rows[0]['suscripcion']),app_db());
  }else{
   $tries=(int)$rows[0]['intentos'];$status=$tries>=6?'fallido':'pendiente';$delay=min(3600,60*pow(2,min(6,$tries)));
   db_query_params('UPDATE piscina_push_cola SET estado=?,error_codigo=?,proximo=DATE_ADD(NOW(),INTERVAL ? SECOND) WHERE id=?','ssii',array($status,(string)$code,$delay,$id),app_db());
  }
 }
 echo '{"ok":true}';exit;
}
http_response_code(400);echo '{"error":"Unknown action"}';
