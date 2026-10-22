<?php
require_once __DIR__.'/connector.php';
require_once __DIR__.'/admin-access.php';
require_once dirname(dirname(__DIR__)).'/configuracion/pool-notifications.php';
function pool_alert_names(){return array('water'=>'Agua y calefacción','air'=>'Ambiente y deshumidificación','vista'=>'Tratamiento del agua · VistaPool');}
function pool_alert_update($state,$dashboard,$now){
 if(!isset($state['incidents']))$state['incidents']=array();
 if(!isset($state['subscriptions']))$state['subscriptions']=array();
 if(!isset($state['sequence']))$state['sequence']=0;
 foreach(pool_alert_names() as $source=>$name){
  $health=$dashboard['sources'][$source];$bad=$health['state']!=='current';$reason=$health['label'];
  if(!$bad)foreach($dashboard['metrics'] as $metric)if($metric['source']===$source&&$metric['monitored']&&$metric['value']===null){$bad=true;$reason='Faltan lecturas del equipo';break;}
  $active=isset($state['incidents'][$source])&&!empty($state['incidents'][$source]['active']);
  if($bad&&!$active){$state['sequence']++;$state['incidents'][$source]=array('id'=>$state['sequence'],'source'=>$source,'name'=>$name,'since'=>$now,'active'=>true,'reason'=>$reason,'last_data'=>$health['time']);}
  elseif($bad){$state['incidents'][$source]['reason']=$reason;$state['incidents'][$source]['last_data']=$health['time'];}
  elseif($active){$state['incidents'][$source]['active']=false;$state['incidents'][$source]['resolved']=$now;}
 }
 return $state;
}
// One private store and one lock serialize subscriptions, incident transitions and delivery leases.
function pool_alert_store($callback){
 $dir=pool_cache_dir();$lock=fopen($dir.'/admin-alerts.lock','c');if(!$lock||!flock($lock,LOCK_EX))throw new RuntimeException('No se puede abrir el registro de avisos.');
 try{$file=$dir.'/admin-alerts.json';$state=array();if(is_file($file)){$state=json_decode(file_get_contents($file),true);if(!is_array($state))throw new RuntimeException('El registro de avisos no es legible.');}$result=$callback($state);pool_json_write($file,$state);return $result;}finally{flock($lock,LOCK_UN);fclose($lock);}
}
function pool_alert_public($state){$out=array();foreach(isset($state['incidents'])?$state['incidents']:array() as $i)if($i['active'])$out[]=$i;return $out;}
function pool_alert_admin_valid($id){return pool_admin_account_valid($id);}
function pool_alert_claim(&$state,$dashboard,$now){
 $state=pool_alert_update($state,$dashboard,$now);$jobs=array();
 foreach($state['subscriptions'] as $hash=>&$s){
  if(empty($s['active']))continue;
  if(!pool_alert_admin_valid($s['admin'])||!pool_push_endpoint($s['subscription']['endpoint'])){$s['active']=false;continue;}
  foreach($state['incidents'] as $source=>$incident){
   if(!$incident['active']){unset($s['deliveries'][$source]);continue;}
   $d=isset($s['deliveries'][$source])?$s['deliveries'][$source]:array();
   if(!$d||$d['incident']!==$incident['id'])$d=array('incident'=>$incident['id'],'attempts'=>0,'next'=>0,'sent'=>false);
   if($d['sent']||$d['attempts']>=6||$d['next']>$now)continue;
   $d['attempts']++;$d['next']=$now+300;$d['lease']=bin2hex(function_exists('random_bytes')?random_bytes(16):openssl_random_pseudo_bytes(16));$s['deliveries'][$source]=$d;
   $jobs[]=array('id'=>$hash.':'.$source.':'.$incident['id'].':'.$d['lease'],'subscription'=>$s['subscription'],'ttl'=>600,'payload'=>array('title'=>'Neo Kids · Equipo sin datos','body'=>$incident['name'].': '.$incident['reason'].'. Revisa el estado de la piscina.','url'=>'/gestion/piscina/estado.php','tag'=>'neo-'.hash('sha256','pool-'.$source.'-'.$incident['id'])));
  }
 }unset($s);return $jobs;
}
function pool_alert_ack(&$state,$id,$code,$now){
 $parts=explode(':',$id);if(count($parts)!==4)return;$hash=$parts[0];$source=$parts[1];
 if(!isset($state['subscriptions'][$hash]['deliveries'][$source]))return;
 $s=&$state['subscriptions'][$hash];$d=&$s['deliveries'][$source];
 if((string)$d['incident']!==$parts[2]||!isset($d['lease'])||!hash_equals($d['lease'],$parts[3]))return;
 if($code>=200&&$code<300){$d['sent']=true;$d['sent_at']=$now;}
 elseif($code===404||$code===410){$s['active']=false;}
 else{$d['next']=$now+min(3600,60*pow(2,$d['attempts']));$d['last_code']=$code;}
 unset($d['lease']);
}
