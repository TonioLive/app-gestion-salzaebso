<?php
require_once __DIR__.'/security-boundary.php';
function security_file_area($file) {
    $root=str_replace('\\','/',realpath(dirname(__DIR__)));
    $areas=array('uploads','fotos_usuarios','dni_usuarios','firma_usuarios','fotos_padres','documentacion','documentos','gabinete','recibos','descargas','prl_neokids');
    $roots=array();foreach($areas as $area)$roots[$root.'/'.$area]=$area;
    // Fixed storage locations used by the legacy deployments, never request input.
    foreach(array('/home/documentacion/prl'=>'personal','/home/gabinete'=>'gabinete','/var/descarga'=>'plantillas','I:/Documentos_aplicacion/subvenciones'=>'subvenciones') as $dir=>$area)$roots[$dir]=$area;
    foreach($roots as $dir=>$area){$base=realpath($dir);if(!$base)continue;$base=rtrim(str_replace('\\','/',$base),'/').'/';if(strncasecmp($file,$base,strlen($base))===0)return array($area,substr($file,strlen($base)));}
    security_stop('Archivo fuera de las carpetas autorizadas.');
}
function security_authorize_file($file,$area,$relative) {
    if(PHP_SAPI==='cli')return;
    if(!empty($_SESSION['permitido'])&&(int)app_value($_SESSION,'nivel_acceso',0)===4)return;
    if(empty($_SESSION['permitido'])) {
        if(!security_family_valid())security_stop();
        if($area==='fotos_usuarios'&&preg_match('/\Afoto_usu(\d+)\.(?:jpg|jpeg|png|gif|webp)\z/i',$relative,$m)) {
            $extra=in_array(basename(dirname(__DIR__)),array('santpau.escuelas-infantiles.com','zeim'),true)?' AND COALESCE(denegar_acceso_agenda,0)=0':'';
            $parent=(int)$_SESSION['id_padre'];if((int)$m[1]===$parent||app_rows('SELECT id_padre FROM padres WHERE id_usuario=? AND padre_de=?'.$extra,'ii',array($parent,(int)$m[1])))return;
            security_stop();
        }
        require_once __DIR__.'/family-assets.php';family_asset_path($file,$area==='fotos_padres'?'image':'document');return;
    }
    if(in_array($area,array('personal','prl_neokids','subvenciones'),true)&&preg_match('~\Ausuario_(\d+)/~',$relative,$m)) {
        if((int)$m[1]===(int)app_value($_SESSION,'idusuario',0))return;security_stop();
    }
    if(in_array($area,array('fotos_usuarios','dni_usuarios','firma_usuarios'),true)&&preg_match('/(?:foto|dni|firma)_usu(\d+)\./i',basename($file),$m)) {
        if((int)$m[1]===(int)app_value($_SESSION,'idusuario',0))return;
        if(!app_can('zona_alumnos')&&!app_can('zona_personal'))security_stop();security_staff_record('usuarios','id_usuario',(int)$m[1]);return;
    }
    if($area==='plantillas'&&app_can('zona_compras'))return;
    if(!app_can('zona_alumnos')&&!app_can('zona_gabinete')&&!app_can('zona_actividades')&&!app_can('zona_aulas'))security_stop();
    $parts=explode('/',$relative);
    if(count($parts)>=3&&preg_match('/-ID(\d+)\z/',$parts[1],$m)&&security_owned_centre((int)$m[1]))return;
    security_stop();
}
function security_download_path($directory,$name) {
    if(!is_string($directory)||!is_string($name)||$name===''||preg_match('~[\\\\/:\x00-\x1f\x7f]|[. ]$~',$name)||strpos($name,'..')!==false)security_stop('Archivo no permitido.');
    $ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
    if(!in_array($ext,array('pdf','doc','docx','xls','xlsx','ppt','pptx','pps','ppsx','rtf','txt','csv','odt','ods','zip','jpg','jpeg','png','gif','webp'),true))security_stop('Archivo no permitido.');
    $base=realpath($directory);$file=realpath($directory.'/'.$name);
    if(!$base||!$file||!is_file($file)||!is_readable($file))security_stop('Archivo no disponible.',404);
    $base=str_replace('\\','/',$base).'/';$file=str_replace('\\','/',$file);
    if(strncasecmp($file,$base,strlen($base))!==0||preg_match('~/(?:\.private|configuracion|vendor|node_modules)/~i',$file))security_stop('Archivo no permitido.');
    list($area,$relative)=security_file_area($file);security_authorize_file($file,$area,$relative);return $file;
}
function security_send_file($file,$inline=false) {
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file);$image=in_array($mime,array('image/jpeg','image/png','image/gif','image/webp'),true);
    while(ob_get_level())ob_end_clean();
    header('Content-Type: '.($inline&&$image?$mime:'application/octet-stream'));
    header('Content-Disposition: '.($inline&&$image?'inline':'attachment').'; filename="'.preg_replace('/[^A-Za-z0-9._-]/','_',basename($file)).'"; filename*=UTF-8\'\''.rawurlencode(app_text(basename($file))));
    header('Cache-Control: no-store, private');header('X-Content-Type-Options: nosniff');header('Content-Length: '.filesize($file));
    session_write_close();readfile($file);exit;
}
