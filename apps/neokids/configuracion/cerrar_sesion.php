<?php
require_once __DIR__.'/config.php';
app_check_csrf();
db_query_params('UPDATE sesion SET Fecha_cierre=CURDATE(),Cerrada=1,Hora_cierre=CURTIME() WHERE id_session=?','s',array(session_id()),app_db());
$_SESSION=array();
$cookie=session_get_cookie_params();
setcookie(session_name(),'',time()-3600,'/','',!empty($cookie['secure']),true);
session_destroy();
header('Location: /index.php',true,303); exit;
