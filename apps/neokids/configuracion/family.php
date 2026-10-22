<?php
// Family identity is checked against the database on each request.
require_once __DIR__.'/config.php';
require_once __DIR__.'/security-boundary.php';
function family_forbidden() { http_response_code(403); exit('No tienes acceso a esta información.'); }
function family_identity() {
    if(!security_family_valid()){header('Location: /app/login.php',true,303);exit;}

    if (empty($_SESSION['permitido_padre']) || empty($_SESSION['id_padre']) || empty($_SESSION['_family_verified'])) { header('Location: /app/login.php'); exit; }
    if (time()-(int)app_value($_SESSION,'_family_seen',0)>3600) {
        unset($_SESSION['permitido_padre'],$_SESSION['_family_verified']); header('Location: /app/login.php'); exit;
    }
    $rows=app_rows('SELECT * FROM padres WHERE id_usuario=? AND usuario_webcam=?','is',array((int)$_SESSION['id_padre'],(string)$_SESSION['nombre_padre']));
    $valid=false;
    foreach ($rows as $r) if (empty($r['denegar_acceso_agenda'])) $valid=true;
    if (!$valid) family_forbidden();
    $_SESSION['_family_seen']=time();
    return (int)$_SESSION['id_padre'];
}
function family_children($parent) {
    $extra=APP_SITE==='santpau' ? ' AND COALESCE(p.denegar_acceso_agenda,0)=0' : '';
    return app_rows('SELECT DISTINCT u.id_usuario,u.Nombre,u.Apellido1,u.Apellido2 FROM padres p JOIN usuarios u ON u.id_usuario=p.padre_de WHERE p.id_usuario=?'.$extra.' ORDER BY u.Nombre','i',array($parent));
}
function family_child($children,$id) {
    foreach ($children as $child) if ((int)$child['id_usuario']===(int)$id) return $child;
    family_forbidden();
}
function family_login_error($message) { $_SESSION['family_error']=$message; header('Location: /app/login.php',true,303); exit; }
function family_latin($text) { return iconv('UTF-8','Windows-1252//TRANSLIT',(string)$text); }
function family_message($id,$parent) {
    $extra=APP_SITE==='santpau' ? ' AND COALESCE(p.denegar_acceso_agenda,0)=0' : '';
    $rows=app_rows('SELECT n.* FROM agenda_notas_padres n WHERE n.id_nota_padre=? AND EXISTS(SELECT 1 FROM padres p WHERE p.padre_de=n.id_alumno AND p.id_usuario=?'.$extra.')','ii',array($id,$parent));
    if (!$rows) family_forbidden(); return $rows[0];
}
