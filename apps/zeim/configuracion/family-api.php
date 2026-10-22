<?php
require_once __DIR__.'/runtime.php';
require_once __DIR__.'/security-boundary.php';
require_once __DIR__.'/dependencies.php';
function family_api_error($status,$message) {http_response_code($status);echo json_encode(array('status'=>(string)$status,'message'=>$message,'error'=>'No se ha podido autorizar la solicitud.'));exit;}
function family_api_verify($token) {
    $secret=require dirname(__DIR__).'/.private/api-auth.php';
    try{$payload=Firebase\JWT\JWT::decode($token,new Firebase\JWT\Key($secret['key'],'HS256'));}
    catch(Throwable $error){return null;}
    if(($payload->iss??'')!=='zeim-family-api'||empty($payload->idUsuario)||empty($payload->usuario)||empty($payload->stamp))return null;
    $active=app_rows('SELECT id_usuario FROM tokens_activos WHERE token=? AND id_usuario=? AND creacion>=DATE_SUB(NOW(),INTERVAL 30 DAY)','si',array(hash('sha256',$token),(int)$payload->idUsuario));
    if(!$active)return null;
    $rows=app_rows('SELECT * FROM padres WHERE id_usuario=? AND usuario_webcam=?','is',array((int)$payload->idUsuario,(string)$payload->usuario));$allowed=false;
    foreach($rows as $row)if(empty($row['denegar_acceso_agenda']))$allowed=true;
    if(!$allowed||!hash_equals(security_family_stamp($rows),(string)$payload->stamp))return null;
    return $payload;
}
function family_api_login($user,$password) {
    if($user===''||strlen($user)>100||$password===''||strlen($password)>1024)return null;
    $dir=dirname(__DIR__).'/.private/api-attempts';if(!is_dir($dir))mkdir($dir,0700,true);
    $locks=array();foreach(array('ip:'.app_value($_SERVER,'REMOTE_ADDR'),'user:'.strtolower($user)) as $key){
        $h=fopen($dir.'/'.hash('sha256',$key).'.json','c+');if(!$h||!flock($h,LOCK_EX))family_api_error(503,'Unavailable');
        $state=json_decode(stream_get_contents($h),true);if(!is_array($state)||time()-(int)$state['since']>900)$state=array('since'=>time(),'count'=>0);
        if((int)$state['count']>=(str_starts_with($key,'ip:')?30:10))family_api_error(429,'Too many attempts');
        $state['count']++;rewind($h);ftruncate($h,0);fwrite($h,json_encode($state));fflush($h);$locks[]=$h;
    }
    $rows=app_rows('SELECT * FROM padres WHERE usuario_webcam=?','s',array($user));$parents=array();$account=null;
    foreach($rows as $row){$parents[(int)$row['id_usuario']]=true;$stored=(string)$row['password_webcam'];
        if(empty($row['denegar_acceso_agenda'])&&(!empty(password_get_info($stored)['algo'])?password_verify($password,$stored):hash_equals($stored,$password)))$account=$row;
    }
    if(!$account||count($parents)!==1){foreach($locks as $h){flock($h,LOCK_UN);fclose($h);}return null;}
    foreach($locks as $h){rewind($h);ftruncate($h,0);fwrite($h,json_encode(array('since'=>time(),'count'=>0)));flock($h,LOCK_UN);fclose($h);}
    $account=security_upgrade_family_password($account,$password);
    $rows=app_rows('SELECT * FROM padres WHERE id_usuario=? AND usuario_webcam=?','is',array((int)$account['id_usuario'],$user));
    $secret=require dirname(__DIR__).'/.private/api-auth.php';$now=time();
    $payload=array('iss'=>'zeim-family-api','iat'=>$now,'nbf'=>$now,'exp'=>$now+30*86400,'jti'=>bin2hex(random_bytes(16)),'usuario'=>$user,'idUsuario'=>(int)$account['id_usuario'],'idHijo'=>(int)$account['padre_de'],'idPadre'=>(int)$account['id_padre'],'stamp'=>security_family_stamp($rows),'caducidad'=>date('Y-m-d H:i:s',$now+30*86400));
    $token=Firebase\JWT\JWT::encode($payload,$secret['key'],'HS256');
    if(!db_query_params('INSERT INTO tokens_activos(token,id_usuario,id_hijo,id_padre) VALUES(?,?,?,?)','siii',array(hash('sha256',$token),$payload['idUsuario'],$payload['idHijo'],$payload['idPadre']),app_db()))throw new RuntimeException('Token storage failed');
    return array('token'=>$token,'id_usuario'=>$payload['idUsuario'],'id_padre'=>$payload['idPadre']);
}
