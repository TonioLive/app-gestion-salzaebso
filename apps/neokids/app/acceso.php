<?php
require_once dirname(__DIR__).'/configuracion/family.php';
if (app_value($_SERVER,'REQUEST_METHOD')!=='POST') { header('Location: /app/login.php'); exit; }
app_check_csrf();
$user=trim((string)app_value($_POST,'user')); $password=(string)app_value($_POST,'pass');
if ($user==='' || strlen($user)>100 || $password==='' || strlen($password)>1024) family_login_error('Revisa tu usuario y contraseña.');
$directory=dirname(__DIR__).'/.private/family-attempts';
if (!is_dir($directory) && !mkdir($directory,0700,true)) { http_response_code(503);exit; }
$locks=array();
foreach (array('ip:'.app_value($_SERVER,'REMOTE_ADDR'),'user:'.strtolower($user)) as $key) {
    $h=fopen($directory.'/'.hash('sha256',$key).'.json','c+');
    if (!$h || !flock($h,LOCK_EX)) { http_response_code(503);exit; }
    $state=json_decode(stream_get_contents($h),true);
    if (!is_array($state) || time()-(int)$state['since']>900) $state=array('since'=>time(),'count'=>0);
    if ($state['count']>=(strpos($key,'ip:')===0?30:10)) family_login_error('Demasiados intentos. Espera 15 minutos para volver a entrar.');
    $state['count']++;rewind($h);ftruncate($h,0);fwrite($h,json_encode($state));fflush($h);$locks[]=$h;
}
$rows=app_rows('SELECT * FROM padres WHERE usuario_webcam=?','s',array($user));
$account=null;$parents=array();
foreach ($rows as $r) {
    $parents[(int)$r['id_usuario']]=true;
    $hash=(string)$r['password_webcam'];$info=password_get_info($hash);
    if (empty($r['denegar_acceso_agenda']) && (!empty($info['algo'])?password_verify($password,$hash):hash_equals($hash,$password))) $account=$r;
}
if (!$account || count($parents)!==1) {
    password_verify($password,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
    family_login_error('No se ha podido iniciar sesión. Revisa los datos facilitados por el centro.');
}
$account=security_upgrade_family_password($account,$password);
foreach ($locks as $h) {rewind($h);ftruncate($h,0);fwrite($h,json_encode(array('since'=>time(),'count'=>0)));flock($h,LOCK_UN);fclose($h);}
session_regenerate_id(true);
$_SESSION['permitido_padre']=true;$_SESSION['id_padre']=(int)$account['id_usuario'];
$_SESSION['nombre_padre']=$account['usuario_webcam'];$_SESSION['id_hijo']=(int)$account['padre_de'];
$_SESSION['_family_credential_stamp']=security_family_stamp(app_rows('SELECT * FROM padres WHERE id_usuario=? AND usuario_webcam=?','is',array((int)$account['id_usuario'],(string)$account['usuario_webcam'])));
$_SESSION['_family_verified']=true;$_SESSION['_family_seen']=time();$_SESSION['mostrar_felicitacion']=0;
unset($_SESSION['password_padre'],$_SESSION['pass_padre'],$_SESSION['token'],$_SESSION['pool_push_id']);
$_SESSION['_csrf']=null;app_csrf_token();
header('Location: /app/',true,303);exit;
