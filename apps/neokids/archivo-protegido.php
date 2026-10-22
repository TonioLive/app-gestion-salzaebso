<?php
require_once __DIR__.'/configuracion/config.php';
require_once __DIR__.'/configuracion/safe-files.php';
$raw=rawurldecode((string)parse_url(app_value($_SERVER,'REQUEST_URI'),PHP_URL_PATH));
if(preg_match('~[\\\\\x00-\x1f:]|(?:^|/)\.\.(?:/|$)~',$raw))security_stop();
$parts=explode('/',trim($raw,'/'));$area=$parts[0]??'';
if(!in_array($area,array('uploads','fotos_usuarios','dni_usuarios','firma_usuarios','fotos_padres','documentacion','documentos','gabinete','recibos','descargas','prl_neokids'),true))security_stop();
$root=str_replace('\\','/',realpath(__DIR__));$file=realpath(__DIR__.$raw);
if(!$file||!is_file($file))security_stop('Archivo no disponible.',404);
$file=str_replace('\\','/',$file);if(strncasecmp($file,$root.'/'.$area.'/',strlen($root.'/'.$area.'/'))!==0)security_stop();
$file=security_download_path(dirname($file),basename($file));
if(empty($permitido)) {
    if(!security_family_valid())security_stop();
    if($area==='fotos_padres') {
        if(!is_file(__DIR__.'/configuracion/family-assets.php'))security_stop(); require_once __DIR__.'/configuracion/family-assets.php';$file=family_asset_path($file,'image');
    }elseif($area==='fotos_usuarios'&&preg_match('/\Afoto_usu(\d+)\.(?:jpg|jpeg|png|gif|webp)\z/i',basename($file),$m)) {
        $parent=(int)$_SESSION['id_padre'];$id=(int)$m[1];
        if($id!==$parent&&!app_rows('SELECT id_padre FROM padres WHERE id_usuario=? AND padre_de=?','ii',array($parent,$id)))security_stop();
    }else security_stop();
}elseif((int)$nivel_acceso!==4) {
    if(in_array($area,array('fotos_usuarios','dni_usuarios','firma_usuarios'),true)&&preg_match('/(?:foto|dni|firma)_usu(\d+)\./i',basename($file),$m)) {
        $id=(int)$m[1];if($id!==(int)$idUsuario){if(!app_can('zona_alumnos')&&!app_can('zona_personal'))security_stop();security_staff_record('usuarios','id_usuario',$id);}
    }else {
        if(!app_can('zona_alumnos')&&!app_can('zona_personal')&&!app_can('zona_actividades'))security_stop();
        $relative=substr($file,strlen($root.'/'.$area.'/'));$segments=explode('/',$relative);
        if(count($segments)<3||!preg_match('/-ID(\d+)\z/',$segments[1],$m)||!security_owned_centre((int)$m[1]))security_stop();
    }
}
security_send_file($file,true);
