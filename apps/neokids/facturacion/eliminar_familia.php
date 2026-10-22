<?php
require_once __DIR__.'/../configuracion/config.php';
require_once __DIR__.'/../configuracion/autentifica.php';
require_once __DIR__.'/../configuracion/autentifica_admin.php';
if(app_value($_SERVER,'REQUEST_METHOD')!=='POST')security_stop('Usa el formulario de familias para eliminar.',405);
app_check_csrf();
$id=app_value($_GET,'familia',app_value($_POST,'familia'));
if(!is_scalar($id)||!ctype_digit((string)$id)||(int)$id<1)security_stop('Identificador no valido.',422);
$used=app_rows('SELECT 1 FROM tarifa WHERE CATEGORIA=? LIMIT 1','i',array((int)$id));
if($used){header('Location: gestion_familias.php?error=servicios',true,303);exit;}
db_query_params('DELETE FROM tipos_iva_servicios WHERE id_tipo_iva=? AND NOT EXISTS (SELECT 1 FROM tarifa WHERE CATEGORIA=tipos_iva_servicios.id_tipo_iva)','i',array((int)$id),app_db());
header('Location: gestion_familias.php',true,303);exit;
