<?php
require __DIR__.'/common.php';pool_require_administration();
require __DIR__.'/admin-alerts.php';header('Cache-Control: no-store');header('X-Content-Type-Options: nosniff');
$config=pool_push_config();$admin=(int)$idUsuario;
try{
 if($_SERVER['REQUEST_METHOD']==='GET'){
  $incidents=pool_alert_store(function(&$state){$state=pool_alert_update($state,pool_dashboard_cached(),time());return pool_alert_public($state);});
  $worker=pool_json_read(pool_cache_dir().'/admin-worker-status.json');
  pool_push_json(array('publicKey'=>isset($config['publicKey'])?$config['publicKey']:'','incidents'=>$incidents,'workerReady'=>!empty($worker['ok'])&&time()-$worker['at']<180));
 }
 if($_SERVER['REQUEST_METHOD']!=='POST')pool_push_json(array('error'=>'Método no permitido.'),405);
 app_check_csrf();$action=app_value($_POST,'action');$endpoint=app_value($_POST,'endpoint');$hash=hash('sha256',$endpoint);
 if(!in_array($action,array('status','disable','subscribe'),true)||!pool_push_endpoint($endpoint))pool_push_json(array('error'=>'La suscripción no es válida.'),422);
 $key=app_value($_POST,'p256dh');$auth=app_value($_POST,'auth');
 if($action==='subscribe'&&(empty($config['publicKey'])||!preg_match('/^[a-zA-Z0-9_-]+={0,2}$/D',$key)||!preg_match('/^[a-zA-Z0-9_-]+={0,2}$/D',$auth)||strlen(base64_decode(strtr($key,'-_','+/'),true))!==65||strlen(base64_decode(strtr($auth,'-_','+/'),true))!==16))pool_push_json(array('error'=>'Las claves del dispositivo no son válidas.'),422);
 $result=pool_alert_store(function(&$state)use($action,$hash,$admin,$endpoint,$key,$auth){
  if(!isset($state['subscriptions']))$state['subscriptions']=array();$subs=&$state['subscriptions'];$s=isset($subs[$hash])?$subs[$hash]:null;
  if($s&&(int)$s['admin']!==$admin)return array('error'=>'Este navegador tiene los avisos asociados a otra cuenta. Desactívalos desde esa cuenta antes de cambiar.','code'=>409);
  if($action==='status')return array('active'=>$s&&!empty($s['active']));
  if($action==='disable'){if($s){$subs[$hash]['active']=false;$subs[$hash]['deliveries']=array();}return array('ok'=>true);}
  $n=0;foreach($subs as $sub)if((int)$sub['admin']===$admin&&!empty($sub['active']))$n++;
  if((!$s||empty($s['active']))&&$n>=10)return array('error'=>'Ya tienes diez dispositivos activos. Desactiva alguno antes de añadir otro.','code'=>422);
  // Inactive entries can be replaced; retain only active devices and recent inactive records.
  foreach($subs as $k=>$sub)if(empty($sub['active'])&&isset($sub['updated'])&&$sub['updated']<time()-2592000)unset($subs[$k]);
  $subs[$hash]=array('admin'=>$admin,'active'=>true,'updated'=>time(),'subscription'=>array('endpoint'=>$endpoint,'keys'=>array('p256dh'=>$key,'auth'=>$auth)),'deliveries'=>$s&&!empty($s['active'])?$s['deliveries']:array());
  return array('ok'=>true);
 });pool_push_json($result,isset($result['code'])?$result['code']:200);
}catch(Exception $e){pool_push_json(array('error'=>'No se ha podido guardar o consultar los avisos. Inténtalo de nuevo.'),503);}
