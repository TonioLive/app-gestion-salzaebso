<?php
/** UTF-8 at the HTTP boundary, with explicit compatibility for legacy forms. */
function neo_text_utf8($value) {
    $value=(string)$value;
    if(preg_match('//u',$value))return $value;
    // Preserve existing UTF-8 islands in otherwise Windows-1252 templates.
    return preg_replace_callback('/[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE-\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}|[\x80-\xFF]/s',function($m){
        if(strlen($m[0])>1)return $m[0];
        $converted=@iconv('Windows-1252','UTF-8',$m[0]);
        return $converted===false?"\xEF\xBF\xBD":$converted;
    },$value);
}
function neo_legacy_input_value($value,$key='') {
    if(is_array($value)){foreach($value as $k=>$v)$value[$k]=neo_legacy_input_value($v,(string)$k);return $value;}
    if(!is_string($value)||preg_match('/pass|clave|token|csrf|carpeta|archivo|fichero|id_doc|nombre_doc|^sub$|url|redirect/i',$key)||!preg_match('/[\x80-\xff]/',$value)||!preg_match('//u',$value))return $value;
    $converted=@iconv('UTF-8','Windows-1252',$value);
    if($converted===false){http_response_code(422);header('Content-Type: text/plain; charset=UTF-8');exit('El texto contiene un símbolo que este formulario no admite. Revisa los símbolos especiales.');}
    return $converted;
}
function neo_utf8_input() {
    if(isset($GLOBALS['neo_original_input'])) {
        $_GET=$GLOBALS['neo_original_input']['get'];$_POST=$GLOBALS['neo_original_input']['post'];$_REQUEST=$GLOBALS['neo_original_input']['request'];
        unset($GLOBALS['neo_original_input']);
    }
}
function neo_prepare_input($route) {
    if(PHP_SAPI==='cli')return;
    $modern=preg_match('~^/(?:app|api|caja|gestion/(?:piscina|panel|mensajes))/~',$route)
        ||in_array($route,array('/index.php','/configuracion/inicio_sesion.php','/configuracion/cerrar_sesion.php'),true);
    $modern=$modern || in_array($route,array('/contactos/listado_contactos.php','/facturacion/gestion_familias.php','/facturacion/guarda_dato.php','/facturacion/pagos_a_cuenta.php','/facturacion/pagos_a_cuenta1.php','/formulario/listado.php','/gestion/agenda/guarda_checkbox.php','/gestion/agenda/guarda_dato.php','/gestion/agenda/guarda_dato_evaluacion.php','/gestion/agenda/guarda_dato_programacion.php','/gestion/agenda_auxiliares/guarda_checkbox.php','/gestion/agenda_auxiliares/guarda_dato.php','/gestion/agenda_dia/guarda_dato_evaluacion.php','/gestion/agenda_dia/guarda_dato_programacion.php','/gestion/agenda_dia_new/guarda_dato_evaluacion.php','/gestion/agenda_dia_new/guarda_dato_programacion.php','/gestion/agenda_ingles/guarda_dato.php','/gestion/agenda_ingles/guarda_dato_evaluacion.php','/gestion/agenda_ingles/guarda_dato_programacion.php','/gestion/agenda_ingles_dia/guarda_dato_evaluacion.php','/gestion/agenda_ingles_dia/guarda_dato_programacion.php','/gestion/agenda_pruebas/guarda_dato.php','/gestion/agenda_pruebas/guarda_dato_evaluacion.php','/gestion/agenda_pruebas/guarda_dato_programacion.php','/gestion/centros/guarda_dato.php','/gestion/grupos_pruebas/borrar_plantilla.php','/gestion/grupos_pruebas/duplicar_plantilla.php','/gestion/grupos_pruebas/editar_plantillas.php','/gestion/grupos_pruebas/editar_plantillas_orden1.php','/gestion/grupos_pruebas/gestion_plantillas.php','/gestion/grupos_pruebas/gestion_programaciones.php','/gestion/grupos_pruebas/guarda_comentarios.php','/gestion/grupos_pruebas/guarda_plantilla.php','/gestion/grupos_pruebas/guarda_programacion_plantilla.php','/gestion/grupos_pruebas/quitar_vinculacion.php','/gestion/grupos_pruebas/vincular_plantilla.php','/gestion/grupos_pruebas/vincular_plantilla1.php','/gestion/guarda_checkbox.php','/gestion/guarda_dato.php','/gestion/herramientas/guarda_checkbox.php','/gestion/herramientas/guarda_dato.php','/gestion_gastos/guarda_dato.php','/gestion_gastos_solo_lapices/guarda_dato.php','/herramientas/guarda_dato.php','/lista_espera/listas_espera.php'),true);
    $modern=$modern || in_array($route,array('/gestion/grupos_pruebas/editar_objetivos_plantillas.php','/gestion/grupos_pruebas/guarda_actividades_plantilla.php','/gestion/grupos_pruebas/nuevo_objetivo_plantilla.php','/gestion/grupos_pruebas/nueva_actividad_plantilla.php','/gestion/grupos_pruebas/eliminar_actividades_plantilla.php','/gestion/grupos_pruebas/imagenes/upload.php'),true);
    if($modern)return;
    $GLOBALS['neo_original_input']=array('get'=>$_GET,'post'=>$_POST,'request'=>$_REQUEST);
    $_GET=neo_legacy_input_value($_GET);$_POST=neo_legacy_input_value($_POST);$_REQUEST=neo_legacy_input_value($_REQUEST);
}
function neo_html_utf8_output($body) {
    $type='';foreach(headers_list() as $h)if(stripos($h,'Content-Type:')===0)$type=strtolower($h);
    if($type!=='' && strpos($type,'text/html')===false)return $body;
    if(stripos($body,'<html')===false && stripos($body,'<!doctype')===false)return $body;
    $body=neo_text_utf8($body);
    $body=preg_replace('/charset\s*=\s*(?:windows-125[028]|iso-8859-1)/i','charset=UTF-8',$body);
    if(!headers_sent())header('Content-Type: text/html; charset=UTF-8');
    if(stripos($body,'name="viewport"')===false && stripos($body,"name='viewport'")===false)$body=preg_replace('~<head\b[^>]*>~i','$0<meta name="viewport" content="width=device-width, initial-scale=1">',$body,1);
    if(!empty($_SESSION['permitido']) && empty($_SESSION['permitido_padre'])) {
        $assets='<link rel="stylesheet" href="/css/neo-admin.css?v=20260930-2"><script src="/scripts/neo-admin.js?v=20260930-2" defer></script>';
        if(strpos($body,'/scripts/neo-admin.js')===false)$body=preg_replace('~</head>~i',$assets.'</head>',$body,1);
    }
    return $body;
}
