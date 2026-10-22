<?php
require_once dirname(__DIR__, 2).'/configuracion/config.php';
require_once dirname(__DIR__, 2).'/configuracion/autentifica.php';
if (!app_can('zona_aulas') || !in_array((int)$nivel_acceso, [3,4], true)) {
    http_response_code(403); exit('La biblioteca de programaciones está disponible para dirección. Los monitores pueden editar las sesiones de sus grupos en Grupos y horarios.');
}
if(function_exists('neo_utf8_input'))neo_utf8_input();
app_db()->set_charset('utf8mb4');
header('Content-Type: text/html; charset=UTF-8');
function prog_id($source, $key, $allowZero=false) {
    $value=app_value($source,$key,$allowZero?'0':'');
    if(!preg_match('/^\d{1,9}$/D',(string)$value) || (!$allowZero && (int)$value<1)) prog_error('El identificador no es válido.');
    return (int)$value;
}
function prog_query($sql,$types='', $values=[]) {
    $result=$types===''?app_db()->query($sql):db_query_params($sql,$types,$values,app_db());
    if($result===false)throw new RuntimeException('No se ha podido guardar la programación.');
    return $result;
}
function prog_template($id,$lock=false) {
    $result=prog_query('SELECT * FROM agenda_programacion_plantillas WHERE id_plantilla=?'.($lock?' FOR UPDATE':''),'i',[$id]);
    $row=$result->fetch_assoc();if(!$row)prog_error('Esta programación ya no está disponible.',404);return $row;
}
function prog_post() {
    if(app_value($_SERVER,'REQUEST_METHOD')!=='POST'){header('Allow: POST');prog_error('Abre la programación y utiliza sus botones para guardar los cambios.',405);}
    app_check_csrf();
}
function prog_level($level){return ['Bebés','Nivel 1','Nivel 2','Nivel 3'][(int)$level]??'Nivel sin definir';}
function prog_header($title,$subtitle='') {
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?=app_escape($title)?> · Neo Kids</title><link rel="stylesheet" href="/gestion/grupos_pruebas/programaciones.css?v=20260930"><script src="/gestion/grupos_pruebas/programaciones.js?v=20260930" defer></script></head><body class="nk-programaciones"><header class="prog-header"><a class="prog-brand" href="/"><span class="prog-symbol">≈</span><span>Neo Kids<small>Gestión de piscina</small></span></a><a href="/" class="prog-button secondary">Inicio</a></header><nav class="prog-nav" aria-label="Programaciones"><a href="/gestion/piscina/grupos.php">Grupos y horarios</a><a href="/gestion/grupos_pruebas/gestion_programaciones.php" aria-current="page">Biblioteca de programaciones</a></nav><main class="prog-main"><div class="prog-title"><div><p class="prog-eyebrow">Aprender en el agua</p><h1><?=app_escape($title)?></h1><?php if($subtitle!==''){?><p class="prog-subtitle"><?=app_escape($subtitle)?></p><?php }?></div></div><?php
}
function prog_footer(){echo '</main><footer class="prog-footer">Neo Kids · Programaciones de piscina</footer></body></html>';}
function prog_error($message,$status=422){http_response_code($status);prog_header('Revisa la programación');echo '<div class="prog-notice error">'.app_escape($message).'</div><a class="prog-button" href="/gestion/grupos_pruebas/gestion_programaciones.php">Volver a programaciones</a>';prog_footer();exit;}
function prog_redirect($target){header('Location: '.$target,true,303);exit;}
function prog_atomic($callback){$db=app_db();$db->begin_transaction();try{$result=$callback();$db->commit();return $result;}catch(Throwable $error){$db->rollback();error_log('Programaciones: '.$error->getMessage());prog_error('No se han guardado los cambios. Vuelve a intentarlo.');}}
function prog_csrf(){echo '<input type="hidden" name="_csrf" value="'.app_escape(app_csrf_token()).'">';}
function prog_library_target($value){
 if(!preg_match('/^(O?)([1-9]\d{0,8})$/D',(string)$value,$parts))prog_error('Selecciona un objetivo o una actividad válida.');
 $objective=$parts[1]==='O';$id=(int)$parts[2];
 $rows=$objective?app_rows('SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=? AND id_objetivo_g<>0','i',[$id]):app_rows('SELECT * FROM agenda_programacion_actividades_piscina WHERE id_actividad=?','i',[$id]);
 if(!$rows)prog_error('El objetivo o la actividad ya no está disponible.',404);
 return [$objective,$id,$rows[0]];
}
function prog_library_return($id,$session,$message='saved') {prog_redirect('editar_objetivos_plantillas.php?id_plantilla='.$id.'&sesion_editada='.$session.'&done='.rawurlencode($message));}
