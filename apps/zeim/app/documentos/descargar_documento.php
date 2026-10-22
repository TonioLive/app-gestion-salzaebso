<?php
require_once __DIR__.'/../../configuracion/family-assets.php';
$parent=family_identity();$folder=app_value($_GET,'carpeta_descarga');$name=app_value($_GET,'archivo_descarga');
if (!is_string($folder)||!is_string($name)||basename(str_replace('\\','/',$name))!==$name) family_forbidden();
if (strpos($folder,'/var/www/vhosts/')!==0 && strpos(str_replace('\\','/',$folder),str_replace('\\','/',dirname(dirname(__DIR__))))!==0) $folder=dirname(dirname(__DIR__)).'/documentacion/'.ltrim($folder,'/');
$file=family_asset_path($folder.'/'.$name,'document');
app_rows('INSERT INTO agenda_documentos_padres (id_usuario,nombre_documento,fecha_lectura,hora_lectura) VALUES (?,?,CURDATE(),CURTIME())','is',array($parent,$file));
family_send_asset($file);
