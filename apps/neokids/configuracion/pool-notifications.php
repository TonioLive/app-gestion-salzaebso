<?php
function pool_push_endpoint($endpoint){
 if(!is_string($endpoint)||strlen($endpoint)>2048)return false;$u=parse_url($endpoint);
 if(!$u||!isset($u['scheme'],$u['host'])||$u['scheme']!=='https'||isset($u['user'])||isset($u['pass'])||isset($u['fragment'])||(isset($u['port'])&&$u['port']!==443))return false;
 $host=strtolower($u['host']);return in_array($host,array('fcm.googleapis.com','updates.push.services.mozilla.com','web.push.apple.com'),true)||preg_match('/^[a-z0-9-]+\.notify\.windows\.com$/D',$host);
}
function pool_push_config(){static $config;if($config===null){$file=dirname(__DIR__).'/.private/notifications/config.json';$config=is_file($file)?json_decode(file_get_contents($file),true):array();}return $config;}
function pool_push_json($data,$code=200){http_response_code($code);header('Content-Type: application/json; charset=UTF-8');header('Cache-Control: no-store');echo json_encode($data);exit;}
function pool_push_shift($date,$modify){$copy=clone $date;$copy->modify($modify);return $copy;}
function pool_push_queue($s,$event,$kind,$title,$body,$url,$expires){
 return db_query_params('INSERT IGNORE INTO piscina_push_cola (suscripcion,evento,tipo,titulo,texto,url,proximo,caduca,creado) VALUES (?,?,?,?,?,?,NOW(),?,NOW())','issssss',array((int)$s['id'],$event,$kind,$title,$body,$url,$expires),app_db());
}
function pool_push_collect($now=null,$only=0){
 $now=$now?:new DateTime('now',new DateTimeZone('Europe/Madrid'));$stamp=$now->format('Y-m-d H:i:s');$date=$now->format('Y-m-d');$expire=pool_push_shift($now,'+1 day')->format('Y-m-d H:i:s');$created=0;
 $subs=app_rows('SELECT s.* FROM piscina_push_suscripciones s WHERE s.activa=1 AND (?=0 OR s.id=?) AND EXISTS(SELECT 1 FROM padres p WHERE p.id_usuario=s.padre AND p.usuario_webcam<>\'\')','ii',array($only,$only));
 foreach($subs as $s){$parent=(int)$s['padre'];$subscription=(int)$s['id'];
  if($s['mensajes']){
   $notes=app_rows("SELECT DISTINCT n.id_nota_padre,n.id_alumno FROM agenda_notas_padres n WHERE n.id_nota_padre>? AND EXISTS(SELECT 1 FROM padres p WHERE p.id_usuario=? AND p.padre_de=n.id_alumno) AND NOT EXISTS(SELECT 1 FROM padres sender WHERE sender.id_usuario=n.id_remitente AND sender.padre_de=n.id_alumno) AND NOT EXISTS(SELECT 1 FROM piscina_push_cola q WHERE q.suscripcion=? AND q.evento=CONCAT('mensaje:',n.id_nota_padre)) ORDER BY n.id_nota_padre LIMIT 100",'iii',array((int)$s['nota_desde'],$parent,$subscription));
   foreach($notes as $n){pool_push_queue($s,'mensaje:'.$n['id_nota_padre'],'mensajes','Nuevo mensaje de Neo Kids','Tienes un mensaje del equipo de la piscina.','/app/?view=mensajes&child='.$n['id_alumno'].'&message='.$n['id_nota_padre'],$expire);$created++;}
   $replies=app_rows("SELECT r.id_respuesta,n.id_nota_padre,n.id_alumno FROM agenda_notas_padres_respuestas r JOIN agenda_notas_padres n ON n.id_nota_padre=r.id_nota WHERE r.id_respuesta>? AND EXISTS(SELECT 1 FROM padres p WHERE p.id_usuario=? AND p.padre_de=n.id_alumno) AND NOT EXISTS(SELECT 1 FROM padres sender WHERE sender.id_usuario=r.respondido_por AND sender.padre_de=n.id_alumno) AND NOT EXISTS(SELECT 1 FROM piscina_push_cola q WHERE q.suscripcion=? AND q.evento=CONCAT('respuesta:',r.id_respuesta)) ORDER BY r.id_respuesta LIMIT 100",'iii',array((int)$s['respuesta_desde'],$parent,$subscription));
   foreach($replies as $r){pool_push_queue($s,'respuesta:'.$r['id_respuesta'],'mensajes','Respuesta de Neo Kids','El equipo de la piscina ha respondido a un mensaje.','/app/?view=mensajes&child='.$r['id_alumno'].'&message='.$r['id_nota_padre'],$expire);$created++;}
  }
  if($s['avisos']){
   $rows=app_rows("SELECT n.id_aviso FROM piscina_avisos n WHERE n.id_aviso>? AND n.archivado=0 AND n.publicado<=? AND (n.caduca IS NULL OR n.caduca>=?) AND EXISTS(SELECT 1 FROM padres p JOIN alumnos a ON a.id_usuario=p.padre_de JOIN cursos_academicos c ON c.id_curso=a.id_curso WHERE p.id_usuario=? AND a.id_centro=n.id_centro AND c.fecha_inicio<=? AND c.fecha_fin>=? AND (a.fecha_baja='0000-00-00' OR a.fecha_baja>=?)) AND NOT EXISTS(SELECT 1 FROM piscina_push_cola q WHERE q.suscripcion=? AND q.evento=CONCAT('aviso:',n.id_aviso)) ORDER BY n.id_aviso LIMIT 100",'ississsi',array((int)$s['aviso_desde'],$stamp,$date,$parent,$date,$date,$date,$subscription));
   foreach($rows as $r){pool_push_queue($s,'aviso:'.$r['id_aviso'],'avisos','Nuevo aviso de Neo Kids','Hay una novedad de la piscina en tu app.','/app/?view=avisos',$expire);$created++;}
  }
  if($s['facturas']){
   $rows=app_rows("SELECT f.id_factura,a.id_usuario FROM facturas_emitidas f JOIN alumnos a ON a.id_alumno=f.id_cliente WHERE f.id_factura>? AND f.tipo_cliente=0 AND COALESCE(f.num_factura,'')<>'' AND f.fecha_emision<=? AND EXISTS(SELECT 1 FROM padres p WHERE p.id_usuario=? AND p.padre_de=a.id_usuario) AND EXISTS(SELECT 1 FROM detalles_factura d WHERE d.id_factura=f.id_factura) AND NOT EXISTS(SELECT 1 FROM piscina_push_cola q WHERE q.suscripcion=? AND q.evento=CONCAT('factura:',f.id_factura)) ORDER BY f.id_factura LIMIT 100",'isii',array((int)$s['factura_desde'],$date,$parent,$subscription));
   foreach($rows as $r){pool_push_queue($s,'factura:'.$r['id_factura'],'facturas','Nueva factura disponible','Puedes consultar una nueva factura en tu app de Neo Kids.','/app/?view=facturacion&child='.$r['id_usuario'].'&invoice='.$r['id_factura'],$expire);$created++;}
  }
  if($s['clases']){
   $target=pool_push_shift($now,'+1 hour');$targetDate=$target->format('Y-m-d');
   $children=app_rows('SELECT DISTINCT padre_de FROM padres WHERE id_usuario=?','i',array($parent));
   foreach($children as $child)foreach(pool_family_schedule((int)$child['padre_de'],$targetDate) as $lesson){
    if((int)$lesson['dia']!==(int)$target->format('N')||pool_class_cancelled($lesson,$targetDate))continue;
    $start=new DateTime($targetDate.' '.$lesson['hora_inicio'],new DateTimeZone('Europe/Madrid'));$seconds=$start->getTimestamp()-$now->getTimestamp();if($seconds>3600||$seconds<3300)continue;
    $event='clase:'.$lesson['id_alumno'].':'.$lesson['id_grupo'].':'.$lesson['id_subgrupo'].':'.$start->format('Ymd\THi');
    pool_push_queue($s,$event,'clases','Hoy nos vemos en el agua','Tu próxima clase de natación empieza a las '.$start->format('H:i').'.','/app/?view=horario&child='.$child['padre_de'],pool_push_shift($now,'+5 minutes')->format('Y-m-d H:i:s'));
   }
  }
 }
 return $created;
}
function pool_push_still_allowed($job){
 $p=(int)$job['padre'];$e=explode(':',$job['evento']);$id=isset($e[1])?(int)$e[1]:0;
 if(!app_rows("SELECT id_usuario FROM padres WHERE id_usuario=? AND usuario_webcam<>'' LIMIT 1",'i',array($p)))return false;
 if(!in_array($job['tipo'],array('mensajes','avisos','facturas','clases'),true)||empty($job[$job['tipo']]))return false;
 if($e[0]==='mensaje'||$e[0]==='respuesta'){
  $sql=$e[0]==='mensaje'?'SELECT n.id_alumno FROM agenda_notas_padres n WHERE n.id_nota_padre=?':'SELECT n.id_alumno FROM agenda_notas_padres_respuestas r JOIN agenda_notas_padres n ON n.id_nota_padre=r.id_nota WHERE r.id_respuesta=?';
  return (bool)app_rows($sql.' AND EXISTS(SELECT 1 FROM padres p WHERE p.id_usuario=? AND p.padre_de=n.id_alumno)','ii',array($id,$p));
 }
 if($e[0]==='factura')return (bool)app_rows("SELECT f.id_factura FROM facturas_emitidas f JOIN alumnos a ON a.id_alumno=f.id_cliente JOIN padres p ON p.padre_de=a.id_usuario WHERE f.id_factura=? AND f.tipo_cliente=0 AND COALESCE(f.num_factura,'')<>'' AND p.id_usuario=?",'ii',array($id,$p));
 if($e[0]==='aviso')return (bool)app_rows("SELECT n.id_aviso FROM piscina_avisos n WHERE n.id_aviso=? AND n.archivado=0 AND n.publicado<=NOW() AND (n.caduca IS NULL OR n.caduca>=CURDATE()) AND EXISTS(SELECT 1 FROM padres p JOIN alumnos a ON a.id_usuario=p.padre_de JOIN cursos_academicos c ON c.id_curso=a.id_curso WHERE p.id_usuario=? AND a.id_centro=n.id_centro AND c.fecha_inicio<=CURDATE() AND c.fecha_fin>=CURDATE() AND (a.fecha_baja='0000-00-00' OR a.fecha_baja>=CURDATE()))",'ii',array($id,$p));
 if($e[0]==='clase'&&count($e)===5){
  $rows=app_rows('SELECT a.id_usuario FROM alumnos a JOIN padres p ON p.padre_de=a.id_usuario WHERE a.id_alumno=? AND p.id_usuario=?','ii',array($id,$p));if(!$rows)return false;
  $start=DateTime::createFromFormat('!Ymd\THi',$e[4],new DateTimeZone('Europe/Madrid'));if(!$start)return false;$date=$start->format('Y-m-d');
  foreach(pool_family_schedule((int)$rows[0]['id_usuario'],$date) as $s)if((int)$s['id_alumno']===$id&&(int)$s['id_grupo']===(int)$e[2]&&(int)$s['id_subgrupo']===(int)$e[3]&&(int)$s['dia']===(int)$start->format('N')&&substr($s['hora_inicio'],0,5)===$start->format('H:i')&&!pool_class_cancelled($s,$date))return true;
 }
 return false;
}
