<?php
// Fixed destinations and read routes. Never accepts an arbitrary URL or device command.
require_once __DIR__.'/status-model.php';
function pool_controller_base($source,$config){
 if(!in_array($source,array('water','air'),true))throw new InvalidArgumentException('Equipo no válido.');
 $base=rtrim((string)($config[$source.'_base_url']??''),'/');$url=parse_url($base);
 if(!$url||empty($url['host'])||!in_array(strtolower($url['scheme']??''),array('http','https'),true)||isset($url['user'])||isset($url['pass'])||isset($url['query'])||isset($url['fragment']))throw new RuntimeException('Configura la dirección del equipo en el archivo privado de piscina.');
 return $base;
}
function pool_equipment_link($source){
 $path=dirname(dirname(__DIR__)).'/.private/pool-config.php';if(!is_file($path))return '';
 try{return pool_controller_base($source,require $path).'/standard/default.php';}catch(Exception $error){return '';}
}
function pool_cache_dir(){return dirname(dirname(__DIR__)).'/.private/pool';}
function pool_json_read($file){$data=is_file($file)?json_decode(file_get_contents($file),true):null;return is_array($data)?$data:array();}
function pool_json_write($file,$data){
 $json=json_encode($data);if($json===false)throw new RuntimeException('No se ha podido preparar el registro.');
 $temp=tempnam(dirname($file),'pool-');if($temp===false)throw new RuntimeException('No se puede guardar el registro.');
 try{if(file_put_contents($temp,$json,LOCK_EX)===false||!rename($temp,$file))throw new RuntimeException('No se puede guardar el registro.');}finally{if(is_file($temp))@unlink($temp);}
}
function pool_cached_readings($source){
 if(!in_array($source,array('water','air','vista'),true))throw new InvalidArgumentException('Fuente no válida.');
 return pool_json_read(pool_cache_dir().'/'.$source.'.json');
}
function pool_http($url,$post=null,$headers=array(),$cookie=null){
 if(!function_exists('curl_init'))throw new RuntimeException('El servidor no tiene cURL disponible.');
 $ch=curl_init($url);curl_setopt_array($ch,array(CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>4,CURLOPT_TIMEOUT=>8,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_HTTPHEADER=>$headers,CURLOPT_USERAGENT=>'NeoKids-PoolReadings/1.0'));
 $ca=dirname(dirname(__DIR__)).'/.private/cacert.pem';if(is_file($ca))curl_setopt($ch,CURLOPT_CAINFO,$ca);
 if($cookie){curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie);}
 if($post!==null){curl_setopt($ch,CURLOPT_POST,true);curl_setopt($ch,CURLOPT_POSTFIELDS,is_array($post)?http_build_query($post):$post);}
 $data=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);$error=curl_errno($ch);curl_close($ch);
 if($data===false||$error||$code<200||$code>=300)throw new RuntimeException('No se ha podido consultar el equipo (HTTP '.$code.').');
 return $data;
}
function pool_sensor_input($html,$point){
 $doc=new DOMDocument();$old=libxml_use_internal_errors(true);$doc->loadHTML($html);libxml_clear_errors();libxml_use_internal_errors($old);
 $xp=new DOMXPath($doc);$nodes=$xp->query('//input[@name="PresentValue'.(int)$point.'"]');
 if(!$nodes->length)throw new RuntimeException('La lectura del sensor no está disponible.');
 $raw=$nodes->item(0)->getAttribute('value');if(!preg_match('/^-?\d+(?:[.,]\d+)?/',$raw,$m))throw new RuntimeException('Formato de sensor no reconocido.');
 return (float)str_replace(',','.',$m[0]);
}
function pool_controller($source,$config,$dir){
 $port=$source==='air'?8086:8085;$base=pool_controller_base($source,$config);$cookie=$dir.'/controller-'.$port.'.cookies';$sid='';
 try{
  pool_http($base.'/standard/mainframe.php',null,array(),$cookie);
  $challenge=pool_http($base.'/standard/login/session.php',null,array(),$cookie);
  if(!preg_match('/onSessionCreated\s*\(\s*"4194561"\s*,\s*"([a-zA-Z0-9]+)"/',$challenge,$m))throw new RuntimeException('El controlador no ha abierto una sesión de lectura.');
  $sid=$m[1];$user=$config['controller_user'];$strong=md5(md5($config['controller_password']).$user);for($i=0;$i<50;$i++)$strong=md5($strong);
  $authenticated=pool_http($base.'/standard/mainframe.php',array('SessionID'=>$sid,'LocaleID'=>'1034','LoginSessionID'=>$sid,'LoginUserNameMD5'=>md5($user),'LoginPasswordMD5'=>md5($sid.$user.$strong),'LoginStrongPassword'=>$strong,'LoginCommand'=>'Login','LoginUserName'=>'','LoginPassword'=>''),array(),$cookie);
  if(preg_match('/name="SessionID"\s+value="([a-zA-Z0-9]+)"/',$authenticated,$sessionMatch))$sid=$sessionMatch[1];
  $object=$source==='air'?'2252341267':'2252341265';
  $html=pool_http($base.'/standard/fastaccesslists/pointsparameterslist.php?Locale=1034&ObjectID='.$object.'&Node=2&PointinName=0&Session='.rawurlencode($sid),null,array(),$cookie);
  if($source==='air')$values=array('humidity'=>pool_sensor_input($html,49),'air_temperature'=>pool_sensor_input($html,48));
  else $values=array('water_temperature'=>pool_sensor_input($html,3),'water_supply'=>pool_sensor_input($html,2));
  return array('values'=>$values,'source_time'=>null);
 } finally {
  if($sid!==''){try{pool_http($base.'/standard/footer/footer.php',array('SessionID'=>$sid,'OldSessionID'=>$sid,'LocaleID'=>'1034','Command'=>'Logout'),array(),$cookie);}catch(Exception $e){}}
 }
}
function pool_firestore_value($value){
 if(isset($value['mapValue'])){$out=array();foreach(isset($value['mapValue']['fields'])?$value['mapValue']['fields']:array() as $key=>$v)$out[$key]=pool_firestore_value($v);return $out;}
 if(isset($value['arrayValue'])){$out=array();foreach(isset($value['arrayValue']['values'])?$value['arrayValue']['values']:array() as $v)$out[]=pool_firestore_value($v);return $out;}
 return count($value)?reset($value):null;
}
function pool_vista($config,$dir){
 $file=$dir.'/vista-auth.json';$auth=is_file($file)?json_decode(file_get_contents($file),true):null;
 if(!$auth||empty($auth['token'])||empty($auth['until'])||$auth['until']<time()){
  $raw=pool_http('https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key='.rawurlencode((string)($config['vista_api_key']??'')),json_encode(array('email'=>$config['vista_user'],'password'=>$config['vista_password'],'returnSecureToken'=>true)),array('Content-Type: application/json','Referer: https://vistapool.es/'));
  $result=json_decode($raw,true);if(empty($result['idToken']))throw new RuntimeException('VistaPool necesita volver a conectar la cuenta.');
  $auth=array('token'=>$result['idToken'],'until'=>time()+3000);file_put_contents($file,json_encode($auth),LOCK_EX);@chmod($file,0600);
 }
 try{$raw=pool_http('https://firestore.googleapis.com/v1/projects/hayward-europe/databases/(default)/documents/pools/'.rawurlencode($config['vista_pool']),null,array('Authorization: Bearer '.$auth['token']));}
 catch(Exception $e){@unlink($file);throw $e;}
 $doc=json_decode($raw,true);if(empty($doc['fields']))throw new RuntimeException('VistaPool no ha devuelto lecturas.');
 $pool=array();foreach($doc['fields'] as $k=>$v)$pool[$k]=pool_firestore_value($v);
 $values=array();$main=isset($pool['main'])?$pool['main']:array();
 if(!empty($main['hasPH'])&&isset($pool['modules']['ph']['current'])&&pool_metric_number($pool['modules']['ph']['current'])!==null)$values['ph']=(float)$pool['modules']['ph']['current']/100;
 if(!empty($main['hasRX'])&&isset($pool['modules']['rx']['current'])&&pool_metric_number($pool['modules']['rx']['current'])!==null)$values['rx']=(float)$pool['modules']['rx']['current'];
 if(isset($pool['filtration']['status'])){$status=(string)$pool['filtration']['status'];$values['filtration']=$status==='1'?'Encendida':($status==='0'?'Apagada':'Estado no reconocido');}
 // VistaPool hides its temperature sensor on this installation; never show the placeholder value.
 return array('values'=>$values,'source_time'=>isset($pool['updatedAt'])?(int)$pool['updatedAt']:null,'device_online'=>!empty($pool['present']));
}
function pool_readings($source,$force=false){
 if(!in_array($source,array('water','air','vista'),true))throw new InvalidArgumentException('Fuente no válida.');
 $dir=pool_cache_dir();if(!is_dir($dir)&&!mkdir($dir,0700,true))throw new RuntimeException('No se puede acceder a la caché de lecturas.');
 $file=$dir.'/'.$source.'.json';$cache=pool_json_read($file);
 if(!$force&&$cache&&isset($cache['checked_at'])&&time()-$cache['checked_at']<600)return $cache;
 $lock=fopen($dir.'/'.$source.'.lock','c');
 if(!$lock||!flock($lock,LOCK_EX|LOCK_NB))return $cache?:array('ok'=>false,'source'=>$source,'error'=>'Actualización en curso.','values'=>array(),'checked_at'=>time(),'updated_at'=>null);
 try{
  $config=require dirname(dirname(__DIR__)).'/.private/pool-config.php';
  try{
   $result=$source==='vista'?pool_vista($config,$dir):pool_controller($source,$config,$dir);
   $cache=array_merge($result,array('ok'=>true,'source'=>$source,'checked_at'=>time(),'updated_at'=>time(),'error'=>null));
  }catch(Exception $e){
   $cache=$cache?:array('values'=>array(),'updated_at'=>null,'source_time'=>null);$cache['ok']=false;$cache['source']=$source;$cache['checked_at']=time();$cache['error']=$e->getMessage();
  }
  pool_json_write($file,$cache);return $cache;
 } finally {flock($lock,LOCK_UN);fclose($lock);}
}
function pool_dashboard_cached(){
 $sources=array();foreach(array('water','air','vista') as $source)$sources[$source]=pool_cached_readings($source);
 return pool_dashboard($sources,pool_json_read(pool_cache_dir().'/monitor.json'));
}
function pool_monitor_cycle($reason='scheduled'){
 $dir=pool_cache_dir();if(!is_dir($dir)&&!mkdir($dir,0700,true))throw new RuntimeException('No se puede acceder al registro.');
 $lock=fopen($dir.'/monitor.lock','c');if(!$lock)throw new RuntimeException('No se puede iniciar la comprobación.');
 if(!flock($lock,LOCK_EX|LOCK_NB)){fclose($lock);$result=pool_dashboard_cached();$result['refresh_status']='running';return $result;}
 try{
  $run=pool_json_read($dir.'/monitor.json');$last=isset($run['finished_at'])?(int)$run['finished_at']:0;
  if($reason==='manual'&&time()-$last<60){$result=pool_dashboard_cached();$result['refresh_status']='recent';return $result;}
  $run['started_at']=time();$run['running']=true;pool_json_write($dir.'/monitor.json',$run);
  $sources=array();foreach(array('water','air','vista') as $source)$sources[$source]=pool_readings($source,true);
  $run['finished_at']=time();$run['running']=false;$run['reason']=$reason;
  if($reason==='scheduled')$run['last_automatic']=$run['finished_at'];
  $result=pool_dashboard($sources,$run);$run['state']=$result['state'];$run['counts']=$result['counts'];pool_json_write($dir.'/monitor.json',$run);
  $history=pool_json_read($dir.'/monitor-history.json');$history[]=array('at'=>$run['finished_at'],'reason'=>$reason,'state'=>$result['state'],'counts'=>$result['counts']);pool_json_write($dir.'/monitor-history.json',array_slice($history,-144));
  $result['refresh_status']='updated';return $result;
 }finally{flock($lock,LOCK_UN);fclose($lock);}
}
