<?php
require_once __DIR__.'/family.php';
$id_usuario=family_identity();$nombre_usuario=(string)$_SESSION['nombre_padre'];
$accounts=app_rows('SELECT * FROM padres WHERE id_usuario=? AND usuario_webcam=? LIMIT 1','is',array($id_usuario,$nombre_usuario));
$datos_usuario=$accounts[0];$parents=app_rows('SELECT * FROM usuarios WHERE id_usuario=?','i',array($id_usuario));
if (!$parents) family_forbidden();$datos_padre=$parents[0];$sufijo_padre=$datos_padre['sexo']==='V'?'o':'a';
$familyChildren=family_children($id_usuario);
$page=basename((string)parse_url(app_value($_SERVER,'REQUEST_URI'),PHP_URL_PATH));
if ($page==='evaluacion_piscina_c.php' && !empty($_GET['uid'])) {
    $uid=$_GET['uid'];
    if (!is_string($uid)||!preg_match('/^\d{14}$/D',$uid)) family_forbidden();
    $digits=(int)substr($uid,12,1);
    if ($digits<1||$digits>5) family_forbidden();
    $enrolment=(int)substr(substr($uid,5,5),-$digits);
    $owned=app_rows('SELECT a.id_alumno FROM alumnos a JOIN padres p ON p.padre_de=a.id_usuario WHERE a.id_alumno=? AND p.id_usuario=?','ii',array($enrolment,$id_usuario));
    if (!$owned) family_forbidden();
}
foreach (array('_GET','_POST') as $source) {
    foreach ($GLOBALS[$source] as $key=>$value) {
        if (preg_match('/^(id_(?:mensaje|hijo|alumno|curso|aula|cliente|remitente|usuario|encuesta)|mes|ejercicio|centro_factura|trimestre|periodo|numero_hijos|dia|a|curso)$/',$key)) {
            if ($value!=='' && (!is_scalar($value) || !ctype_digit((string)$value))) family_forbidden();
            if ($value!=='') $GLOBALS[$source][$key]=(int)$value;
        }
    }
}
foreach (array($_GET,$_POST) as $source) {
    if (!empty($source['id_mensaje']) && in_array($page,array('ver_mensaje.php','responder_mensaje.php','responder_mensaje1.php','eliminar_mensaje.php'),true)) family_message((int)$source['id_mensaje'],$id_usuario);
    foreach(array('id_hijo','id_alumno') as $key) if (!empty($source[$key])) {
        $rows=app_rows('SELECT a.id_alumno FROM alumnos a JOIN padres p ON p.padre_de=a.id_usuario WHERE a.id_alumno=? AND p.id_usuario=?','ii',array((int)$source[$key],$id_usuario));
        if (!$rows) family_forbidden();
    }
    if (!empty($source['id_remitente']) && (int)$source['id_remitente']!==$id_usuario) family_forbidden();
}
foreach (array($_GET,$_POST) as $source) if (isset($source['a'])) family_child($familyChildren,(int)$source['a']);
if (!empty($_POST['destinatario'])) {
    $dest=(string)$_POST['destinatario'];if(!preg_match('/^[AC]\d+$/',$dest))family_forbidden();family_child($familyChildren,(int)substr($dest,1));
}
if ($_SERVER['REQUEST_METHOD']==='POST') app_check_csrf();
if ($page==='eliminar_mensaje.php' && $_SERVER['REQUEST_METHOD']!=='POST') {http_response_code(405);exit('Usa el botón de eliminar del mensaje.');}
