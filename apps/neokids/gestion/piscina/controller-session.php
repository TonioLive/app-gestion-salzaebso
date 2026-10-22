<?php
require_once __DIR__.'/connector.php';
function pool_controller_session($source){
 if($source!=='water')throw new InvalidArgumentException('Equipo sin acceso directo verificado.');
 $config=require dirname(dirname(__DIR__)).'/.private/pool-config.php';$base=pool_controller_base('water',$config);$cookie=tempnam(pool_cache_dir(),'launch-');$sid='';
 try{
  pool_http($base.'/standard/mainframe.php',null,array(),$cookie);$challenge=pool_http($base.'/standard/login/session.php',null,array(),$cookie);
  if(!preg_match('/onSessionCreated\s*\(\s*"4194561"\s*,\s*"([a-zA-Z0-9]+)"/',$challenge,$m))throw new RuntimeException('No se ha podido abrir una sesión.');
  $sid=$m[1];$user=$config['controller_user'];$strong=md5(md5($config['controller_password']).$user);for($i=0;$i<50;$i++)$strong=md5($strong);
  $html=pool_http($base.'/standard/mainframe.php',array('SessionID'=>$sid,'LocaleID'=>'1034','LoginSessionID'=>$sid,'LoginUserNameMD5'=>md5($user),'LoginPasswordMD5'=>md5($sid.$user.$strong),'LoginStrongPassword'=>$strong,'LoginCommand'=>'Login','LoginUserName'=>'','LoginPassword'=>''),array(),$cookie);
  if(preg_match('/name="SessionID"\s+value="([a-zA-Z0-9]+)"/',$html,$m))$sid=$m[1];
  $check=pool_http($base.'/standard/fastaccesslists/pointsparameterslist.php?Locale=1034&ObjectID=2252341265&Node=2&PointinName=0&Session='.rawurlencode($sid),null,array(),$cookie);pool_sensor_input($check,3);
  return array('base'=>$base,'sid'=>$sid);
 }catch(Exception $e){if($sid)pool_controller_session_close(array('base'=>$base,'sid'=>$sid));throw $e;}finally{if(is_file($cookie))unlink($cookie);}
}
function pool_controller_session_close($session){try{pool_http($session['base'].'/standard/footer/footer.php',array('SessionID'=>$session['sid'],'OldSessionID'=>$session['sid'],'LocaleID'=>'1034','Command'=>'Logout'));return true;}catch(Exception $e){return false;}}
