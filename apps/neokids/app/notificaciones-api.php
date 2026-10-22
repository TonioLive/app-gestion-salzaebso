<?php
require_once dirname(__DIR__).'/configuracion/family.php';require_once dirname(__DIR__).'/configuracion/pool-notifications.php';$parent=family_identity();app_db()->set_charset('utf8mb4');$config=pool_push_config();
if($_SERVER['REQUEST_METHOD']==='GET'){
 $id=(int)app_value($_SESSION,'pool_push_id');$rows=$id?app_rows('SELECT mensajes,avisos,facturas,clases,activa FROM piscina_push_suscripciones WHERE id=? AND padre=?','ii',array($id,$parent)):array();
 $state=dirname(__DIR__).'/.private/notifications/status.json';$health=is_file($state)?json_decode(file_get_contents($state),true):array();
 pool_push_json(array('publicKey'=>app_value($config,'publicKey'),'subscription'=>$rows?$rows[0]:null,'workerReady'=>!empty($health['ok'])&&!empty($health['at'])&&time()-(int)$health['at']<300));
}
app_check_csrf();$action=app_value($_POST,'action');$endpoint=app_value($_POST,'endpoint');$hash=hash('sha256',$endpoint);
if($action==='status'){
 $rows=app_rows('SELECT id,mensajes,avisos,facturas,clases,activa FROM piscina_push_suscripciones WHERE padre=? AND endpoint_hash=?','is',array($parent,$hash));
 if($rows)$_SESSION['pool_push_id']=(int)$rows[0]['id'];pool_push_json(array('subscription'=>$rows?$rows[0]:null));
}
if($action==='disable'){
 db_query_params('UPDATE piscina_push_suscripciones SET activa=0,actualizada=NOW() WHERE padre=? AND endpoint_hash=?','is',array($parent,$hash),app_db());unset($_SESSION['pool_push_id']);pool_push_json(array('ok'=>true));
}
if($action!=='subscribe'||empty($config['publicKey'])||!pool_push_endpoint($endpoint))pool_push_json(array('error'=>'La suscripción no es válida.'),422);
$key=app_value($_POST,'p256dh');$auth=app_value($_POST,'auth');$decode=function($v){return base64_decode(strtr($v,'-_','+/'),true);};
if(!preg_match('/^[a-zA-Z0-9_-]+={0,2}$/D',$key)||!preg_match('/^[a-zA-Z0-9_-]+={0,2}$/D',$auth)||strlen($decode($key))!==65||strlen($decode($auth))!==16)pool_push_json(array('error'=>'La suscripción no es válida.'),422);
$prefs=array();foreach(array('mensajes','avisos','facturas','clases') as $p)$prefs[]=(int)(app_value($_POST,$p)==='1');
$existing=app_rows('SELECT * FROM piscina_push_suscripciones WHERE endpoint_hash=?','s',array($hash));
if($existing&&(int)$existing[0]['padre']!==$parent)pool_push_json(array('error'=>'Este dispositivo está asociado a otra cuenta. Desactiva sus notificaciones o restablece el permiso del navegador antes de cambiar de familia.'),409);
if($existing){
 $resets=array('mensajes'=>array('nota_desde','agenda_notas_padres','id_nota_padre'),'avisos'=>array('aviso_desde','piscina_avisos','id_aviso'),'facturas'=>array('factura_desde','facturas_emitidas','id_factura'));
 foreach($resets as $kind=>$spec)if(!$existing[0]['activa']||empty($existing[0][$kind])){
  db_query_params('UPDATE piscina_push_suscripciones SET '.$spec[0].'=(SELECT COALESCE(MAX('.$spec[2].'),0) FROM '.$spec[1].') WHERE id=?','i',array((int)$existing[0]['id']),app_db());
  if($kind==='mensajes')db_query_params('UPDATE piscina_push_suscripciones SET respuesta_desde=(SELECT COALESCE(MAX(id_respuesta),0) FROM agenda_notas_padres_respuestas) WHERE id=?','i',array((int)$existing[0]['id']),app_db());
 }
 $id=(int)$existing[0]['id'];$ok=db_query_params('UPDATE piscina_push_suscripciones SET endpoint=?,p256dh=?,auth=?,mensajes=?,avisos=?,facturas=?,clases=?,activa=1,actualizada=NOW() WHERE id=? AND padre=?','sssiiiiii',array($endpoint,$key,$auth,$prefs[0],$prefs[1],$prefs[2],$prefs[3],$id,$parent),app_db());
 if(!$existing[0]['activa'])db_query_params("UPDATE piscina_push_cola SET estado='cancelado' WHERE suscripcion=? AND estado IN ('pendiente','enviando')",'i',array($id),app_db());
}else{
 $limit=app_rows('SELECT COUNT(*) AS n FROM piscina_push_suscripciones WHERE padre=? AND activa=1','i',array($parent));if((int)$limit[0]['n']>=10)pool_push_json(array('error'=>'Ya tienes diez dispositivos activos. Desactiva alguno para añadir este.'),422);
 $ok=db_query_params('INSERT INTO piscina_push_suscripciones (padre,endpoint_hash,endpoint,p256dh,auth,mensajes,avisos,facturas,clases,alta,actualizada,nota_desde,respuesta_desde,factura_desde,aviso_desde) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW(),(SELECT COALESCE(MAX(id_nota_padre),0) FROM agenda_notas_padres),(SELECT COALESCE(MAX(id_respuesta),0) FROM agenda_notas_padres_respuestas),(SELECT COALESCE(MAX(id_factura),0) FROM facturas_emitidas),(SELECT COALESCE(MAX(id_aviso),0) FROM piscina_avisos))','issssiiii',array($parent,$hash,$endpoint,$key,$auth,$prefs[0],$prefs[1],$prefs[2],$prefs[3]),app_db());$id=(int)app_db()->insert_id;
}
if(!$ok)pool_push_json(array('error'=>'No se ha podido guardar la suscripción.'),503);
foreach(array('mensajes','avisos','facturas','clases') as $i=>$kind)if(!$prefs[$i])db_query_params("UPDATE piscina_push_cola SET estado='cancelado' WHERE suscripcion=? AND tipo=? AND estado IN ('pendiente','enviando')",'is',array($id,$kind),app_db());
$_SESSION['pool_push_id']=$id;pool_push_json(array('ok'=>true));
