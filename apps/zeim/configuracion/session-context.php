<?php
$permitido = !empty($_SESSION['permitido']) && !empty($_SESSION['idusuario']) && isset($_SESSION['nombre_usu'],$_SESSION['usuario_password']);
$identificador=$nombre_completo=$email=$nombre=$nombre_centro_actual=$poblacion_centro_actual='';
$nivel_acceso=$idUsuario=$usuario_multicentro=$centro_actual=0;
$permissions=array('zona_alumnos','zona_aulas','zona_facturacion','zona_comedor','zona_almacen','zona_incidencias','zona_personal','zona_pedidos','zona_inferior','zona_mensajes','zona_compras','zona_gabinete','zona_corregir_pruebas','zona_actividades');
foreach ($permissions as $permission) $$permission=0;
app_same_origin_guard();
require_once __DIR__.'/request-security.php';
app_refresh_staff_identity($permissions);
$permitido = !empty($_SESSION['permitido']) && !empty($_SESSION['idusuario']);
if ($permitido) {
    $identificador=app_value($_SESSION,'nombre_usu'); $nombre_completo=app_value($_SESSION,'nombre_completo');
    $nombre=app_value($_SESSION,'nombre'); $email=app_value($_SESSION,'email_usu');
    $nivel_acceso=(int)app_value($_SESSION,'nivel_acceso',0); $idUsuario=(int)$_SESSION['idusuario'];
    $usuario_multicentro=(int)app_value($_SESSION,'usuario_multicentro',0);
    foreach ($permissions as $permission) $$permission=(int)app_value($_SESSION,$permission,0);
    $centres=app_centres();
    if (isset($_POST['id_centro_seleccionado']) || isset($_GET['id_centro_seleccionado'])) {
        app_check_csrf();
        $selected=filter_var(app_value($_POST,'id_centro_seleccionado'),FILTER_VALIDATE_INT);
        $allowed=false;
        foreach ($centres as $centre) if ((int)$centre['id_centro']===$selected) $allowed=true;
        if (!$allowed) { http_response_code(403); exit('No tienes acceso a este centro.'); }
        $_SESSION['centro_actual']=$selected;
        header('Location: /index.php',true,303); exit;
    }
    $centro_actual=(int)app_value($_SESSION,'centro_actual',0);
    if (!app_can_change_centre()) $centro_actual=(int)app_value($_SESSION,'id_centro_usuario',0);
    if (!$centro_actual && count($centres)===1) $centro_actual=(int)$centres[0]['id_centro'];
    $found=false;
    foreach ($centres as $centre) if ((int)$centre['id_centro']===$centro_actual) {
        $found=true; $nombre_centro_actual=$centre['nombre_centro']; $poblacion_centro_actual=$centre['poblacion_centro'];
        foreach (array('hora_entrada_centro_man'=>'hora_entrada1','hora_salida_centro_man'=>'hora_salida1','hora_entrada_centro_tar'=>'hora_entrada2','hora_salida_centro_tar'=>'hora_salida2','hora_entrada_centro_man_mj'=>'hora_entrada1_mj','hora_salida_centro_man_mj'=>'hora_salida1_mj','hora_entrada_centro_tar_mj'=>'hora_entrada2_mj','hora_salida_centro_tar_mj'=>'hora_salida2_mj') as $var=>$column) $$var=app_value($centre,$column);
    }
    if (!$found) $centro_actual=0;
    $_SESSION['centro_actual']=$centro_actual; $_SESSION['nombre_centro_actual']=$nombre_centro_actual; $_SESSION['poblacion_centro_actual']=$poblacion_centro_actual;
}
$filtroViAulas=(APP_SITE==='santpau' && $idUsuario===6297) ? ' AND centro_aulas.visibilidad=1' : '';

app_guard_legacy_request();
