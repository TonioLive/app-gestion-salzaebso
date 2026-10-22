<?php
require_once __DIR__.'/config.php';
if (app_value($_SERVER,'REQUEST_METHOD')!=='POST') { header('Location: /index.php'); exit; }
app_check_csrf();
$user=trim((string)app_value($_POST,'user')); $password=(string)app_value($_POST,'pass');
if ($user==='' || strlen($user)>100 || $password==='' || strlen($password)>1024) app_login_error('Revisa el usuario y la contraseña.');
// Persistent limits independent of the browser cookie; no passwords are logged.
$directory=dirname(__DIR__).'/.private/login-attempts';
if (!is_dir($directory) && !mkdir($directory,0700,true)) { http_response_code(503); exit('Acceso temporalmente no disponible.'); }
$locks=array();
foreach (array('ip:'.app_value($_SERVER,'REMOTE_ADDR'), 'user:'.strtolower($user)) as $key) {
    $handle=fopen($directory.'/'.hash('sha256',$key).'.json','c+');
    if (!$handle || !flock($handle,LOCK_EX)) { http_response_code(503); exit('Acceso temporalmente no disponible.'); }
    $state=json_decode(stream_get_contents($handle),true);
    if (!is_array($state) || time()-(int)$state['since']>900) $state=array('since'=>time(),'count'=>0);
    if ($state['count'] >= (strpos($key,'ip:')===0 ? 30 : 10)) { fclose($handle); app_login_error('Demasiados intentos. Espera 15 minutos antes de volver a intentarlo.'); }
    $state['count']++; rewind($handle); ftruncate($handle,0); fwrite($handle,json_encode($state)); fflush($handle); $locks[]=$handle;
}
$rows=app_rows('SELECT * FROM usuarios WHERE usuario=? LIMIT 2','s',array($user));
$account=count($rows)===1 ? $rows[0] : null;
$valid=false;
if ($account) {
    $stored=(string)$account['pass']; $info=password_get_info($stored);
    $valid=!empty($info['algo']) ? password_verify($password,$stored) : hash_equals($stored,$password);
}
// Keep the existing contract rule (including contracts starting in the future).
if ($valid && in_array((int)$account['nivel_acceso'],array(2,3,4,10,11),true)) {
    $contracts=app_rows("SELECT id_contrato FROM personal_contratos WHERE id_empleado=? AND (fecha_fin>=CURDATE() OR fecha_fin='0000-00-00' OR fecha_fin IS NULL OR fecha_inicio>CURDATE()) LIMIT 1",'i',array((int)$account['id_usuario']));
    $valid=count($contracts)>0;
} else $valid=false;
if (!$valid) {
    foreach ($locks as $handle) { flock($handle,LOCK_UN); fclose($handle); }
    // Equal cost for unknown accounts and old plaintext credentials.
    password_verify($password,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
    app_login_error('No se ha podido iniciar sesión. Revisa tus credenciales y tu acceso al centro.');
}
foreach ($locks as $handle) { rewind($handle); ftruncate($handle,0); fwrite($handle,json_encode(array('since'=>time(),'count'=>0))); flock($handle,LOCK_UN); fclose($handle); }
// Upgrade legacy credentials only after successful identity and contract checks.
if (security_password_needs_rehash((string)$account['pass'])) {
    $newHash=security_new_password($password);
    if (!db_query_params('UPDATE usuarios SET pass=? WHERE id_usuario=? AND pass=?','sis',array($newHash,(int)$account['id_usuario'],(string)$account['pass']),app_db())) {
        app_login_error('No se ha podido completar el acceso. Vuelve a intentarlo.');
    }
    $account['pass']=$newHash;
}
$_SESSION=array(); session_regenerate_id(true);
$_SESSION['_staff_credential_stamp']=hash('sha256',(string)$account['pass']);
$_SESSION['permitido']=true; $_SESSION['nombre_usu']=$account['usuario'];
$_SESSION['usuario_password']='authenticated';
foreach (array('nivel_acceso'=>'nivel_acceso','nombre'=>'Nombre','idusuario'=>'id_usuario','id_centro_usuario'=>'id_centro','usuario_multicentro'=>'multicentro') as $key=>$column) $_SESSION[$key]=$account[$column];
$_SESSION['nombre_completo']=trim($account['Nombre'].' '.$account['Apellido1'].' '.$account['Apellido2']);
$_SESSION['email_usu']=app_value($account,'email',app_value($account,'Email'));
foreach ($account as $key=>$value) if (strpos($key,'zona_')===0) $_SESSION[$key]=(int)$value;
$_SESSION['_last_activity']=time();
app_csrf_token();
$db=app_db();
db_query_params("INSERT INTO sesion (id_session,Usuario,Tipo,Fecha,Hora,REMOTE_ADDR,HTTP_ACCEPT_LANGUAGE,HTTP_User_Agent,Cerrada,nombre_usuario) VALUES (?,?,'0',CURDATE(),CURTIME(),?,?,?,'0',?)",'sissss',array(session_id(),(int)$account['id_usuario'],substr(app_value($_SERVER,'REMOTE_ADDR'),0,255),substr(app_value($_SERVER,'HTTP_ACCEPT_LANGUAGE'),0,255),substr(app_value($_SERVER,'HTTP_USER_AGENT'),0,255),substr($_SESSION['nombre_completo'],0,100)),$db);
header('Location: /index.php',true,303); exit;
