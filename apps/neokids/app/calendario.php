<?php
require_once dirname(__DIR__).'/configuracion/family.php';require_once dirname(__DIR__).'/configuracion/pool-family.php';$parent=family_identity();app_db()->set_charset('utf8mb4');$child=family_child(family_children($parent),(int)app_value($_GET,'child'));
$found=null;foreach(pool_family_schedule((int)$child['id_usuario']) as $s)if((int)$s['id_grupo']===(int)app_value($_GET,'grupo')&&(int)$s['id_subgrupo']===(int)app_value($_GET,'sub')&&(int)$s['id_alumno']===(int)app_value($_GET,'alumno'))$found=$s;
if(!$found||$found['bloqueado'])family_forbidden();$ics=pool_ics($found,app_text($child['Nombre']));if(!$ics){http_response_code(404);exit('No hay próximas clases en este curso.');}
header('Content-Type: text/calendar; charset=UTF-8');header('Content-Disposition: attachment; filename="clases-neokids.ics"');header('Cache-Control: private, no-store');echo $ics;
