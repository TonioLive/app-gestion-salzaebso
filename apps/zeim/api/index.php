<?php
require_once __DIR__.'/../configuracion/family-api.php';
header('Content-Type: application/json; charset=UTF-8');header('Cache-Control: private, no-store');
if(empty($_SERVER['HTTPS'])||$_SERVER['HTTPS']==='off')family_api_error(403,'SSL required');
app_same_origin_guard();
if(!in_array($_SERVER['REQUEST_METHOD'],array('GET','POST'),true))family_api_error(405,'Method not allowed');
if((int)app_value($_SERVER,'CONTENT_LENGTH',0)>16384)family_api_error(413,'Request too large');
$authorization=(string)app_value($_SERVER,'HTTP_AUTHORIZATION');
$token=preg_match('/\ABearer (.+)\z/i',$authorization,$m)?$m[1]:(string)app_value($_GET,'token');
if($token!==''&&$token!=='null') {
    $identity=family_api_verify($token);if(!$identity)family_api_error(401,'Unauthorized');
    echo json_encode(array('status'=>'202','message'=>'Accepted','error'=>'0: No error','id_usuario'=>(int)$identity->idUsuario));exit;
}
if($_SERVER['REQUEST_METHOD']!=='POST'||stripos((string)app_value($_SERVER,'CONTENT_TYPE'),'application/json')!==0)family_api_error(415,'JSON POST required');
$data=json_decode(file_get_contents('php://input',false,null,0,16385),true);
if(!is_array($data))family_api_error(400,'Invalid JSON');
$login=family_api_login(trim((string)app_value($data,'usuario')),(string)app_value($data,'contras'));
if(!$login)family_api_error(401,'Unauthorized');
$fcm=(string)app_value($data,'FCMtoken');
if($fcm!==''&&strlen($fcm)<=4096) {
    $rows=app_rows('SELECT id_token_fcm FROM tokens_fcm WHERE id_usuario=? AND id_padre=? AND FCMtoken=?','iis',array($login['id_usuario'],$login['id_padre'],$fcm));
    if($rows)db_query_params('UPDATE tokens_fcm SET ultimo_acceso=NOW() WHERE id_token_fcm=?','i',array((int)$rows[0]['id_token_fcm']),app_db());
    else db_query_params('INSERT INTO tokens_fcm(id_usuario,id_padre,FCMtoken,ultimo_acceso) VALUES(?,?,?,NOW())','iis',array($login['id_usuario'],$login['id_padre'],$fcm),app_db());
}
echo json_encode(array('status'=>'202','message'=>'Accepted','error'=>'0: No error','id_usuario'=>$login['id_usuario'],'token'=>$login['token']));
