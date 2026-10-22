<?php
require_once __DIR__.'/../configuracion/config.php';
require_once __DIR__.'/../configuracion/autentifica.php';
require_once __DIR__.'/../configuracion/autentifica_admin.php';
if(app_value($_SERVER,'REQUEST_METHOD')!=='POST')security_stop('Usa el formulario de contactos para eliminar.',405);
app_check_csrf();
$id=app_value($_GET,'id_contacto',app_value($_POST,'id_contacto'));
if(!is_scalar($id)||!ctype_digit((string)$id)||(int)$id<1)security_stop('Identificador no valido.',422);
$rows=app_rows('SELECT id_centro FROM contactos WHERE id_contacto=?','i',array((int)$id));
if(!$rows)security_stop('El contacto ya no existe.',404);
$centre=(int)$rows[0]['id_centro'];
if($centre!==0&&!security_owned_centre($centre))security_stop();
db_query_params('DELETE FROM contactos WHERE id_contacto=? AND id_centro=?','ii',array((int)$id,$centre),app_db());
header('Location: listado_contactos.php',true,303);exit;
