<?php
require_once __DIR__.'/family.php';
// Resolve only existing family files in an authorised course/centre/class hierarchy.
function family_asset_path($raw,$kind) {
    $parent=family_identity();
    if (!is_string($raw)||strpos($raw,"\0")!==false) family_forbidden();
    $raw=str_replace('\\','/',$raw);
    $old='/var/www/vhosts/escuelainfantilsantpau.com/';
    $root=str_replace('\\','/',dirname(__DIR__)).'/';
    if (strpos($raw,$old)===0) $raw=$root.substr($raw,strlen($old));
    $areas=$kind==='image'?array('fotos_padres'):array('documentacion','documentos','gabinete');
    if ($kind==='image' && strpos($raw,$root)!==0) $raw=$root.'fotos_padres/'.ltrim($raw,'/');
    if (strpos($raw,$root)!==0 || preg_match('~(?:^|/)\.\.(?:/|$)~',$raw)) family_forbidden();
    $relative=substr($raw,strlen($root));$parts=explode('/',$relative);$area=array_shift($parts);
    if (!in_array($area,$areas,true)) family_forbidden();
    $base=realpath($root.$area);$file=realpath($raw);
    if (!$base||!$file||!is_file($file)||!is_readable($file)) {http_response_code(404);exit('Archivo no disponible.');}
    $base=str_replace('\\','/',$base).'/';$file=str_replace('\\','/',$file);
    if (strncmp($file,$base,strlen($base))!==0) family_forbidden();
    $parts=explode('/',substr($file,strlen($base)));$ids=array();
    foreach (array_slice($parts,0,4) as $part) {if (preg_match('/-ID(\d+)$/D',$part,$m)) $ids[]=(int)$m[1];else break;}
    if (count($ids)<2) family_forbidden();
    $rows=app_rows('SELECT a.id_alumno,a.aula FROM alumnos a JOIN padres p ON p.padre_de=a.id_usuario WHERE p.id_usuario=? AND a.id_curso=? AND a.id_centro=? AND COALESCE(p.denegar_acceso_agenda,0)=0','iii',array($parent,$ids[0],$ids[1]));
    $allowed=false;
    foreach($rows as $row) {
        if (isset($ids[2])&&(int)$row['aula']!==$ids[2]) continue;
        if (isset($ids[3])&&(int)$row['id_alumno']!==$ids[3]) continue;
        if ($kind==='image') {
            if (count($ids)>=3 || (isset($parts[2])&&$parts[2]==='comun')) $allowed=true;
        } elseif (isset($parts[count($ids)])&&$parts[count($ids)]==='padres') $allowed=true;
    }
    if (!$allowed) family_forbidden();
    $extension=strtolower(pathinfo($file,PATHINFO_EXTENSION));
    $extensions=$kind==='image'?array('jpg','jpeg','png','gif','webp'):array('pdf','doc','docx','xls','xlsx','ppt','pptx','pps','ppsx','rtf','txt','odt','ods');
    if (!in_array($extension,$extensions,true)) family_forbidden();
    return $file;
}
function family_send_asset($file,$image=false,$download=false) {
    $mime='application/octet-stream';
    if ($image) {$info=@getimagesize($file);if (!$info) family_forbidden();$mime=$info['mime'];}
    header('Content-Type: '.$mime);header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: '.($download||!$image?'attachment':'inline').'; filename="'.preg_replace('/[^a-zA-Z0-9._-]/','_',basename($file)).'"; filename*=UTF-8\'\''.rawurlencode(app_text(basename($file))));
    header('Cache-Control: private, no-store');header('Content-Length: '.filesize($file));
    session_write_close();readfile($file);exit;
}
