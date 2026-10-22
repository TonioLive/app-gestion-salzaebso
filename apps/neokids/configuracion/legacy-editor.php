<?php
require_once __DIR__.'/config.php';
neo_utf8_input();
require_once __DIR__.'/security-boundary.php';
if(empty($permitido))security_stop();
if(app_value($_SERVER,'REQUEST_METHOD')!=='POST') {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Confirmar cambio</title></head><body><h1>Confirmar cambio</h1><form method="post" action="'.app_escape(app_value($_SERVER,'REQUEST_URI')).'"><input type="hidden" name="_csrf" value="'.app_csrf_token().'"><button>Guardar el cambio</button></form></body></html>';exit;
}
app_check_csrf();
function editor_value($names,$default='') { foreach((array)$names as $n){if(isset($_POST[$n]))return app_value($_POST,$n);if(isset($_GET[$n]))return app_value($_GET,$n);}return $default; }
$route=$GLOBALS['security_entry_route'];
$table=strtolower((string)editor_value(array('tabla','nombre_tabla')));
if($table==='' && preg_match('~/agenda(?:_pruebas|_ingles)?/guarda_dato\.php$~',$route))$table='agenda_alumnos_comidas';
$allowed=array('alumnos'=>'zona_aulas','usuarios'=>'zona_gabinete','contactos'=>'zona_alumnos','detalles_factura'=>'zona_facturacion','gestion_costes'=>'zona_compras','aulas_grupos_trimestres'=>'zona_aulas','agenda_alumnos_comidas'=>'zona_aulas','agenda_alumnos_seguimiento'=>'zona_aulas','servicios_consumidos_faltas'=>'zona_aulas');
foreach(array('agenda_programacion','agenda_programacion_evaluacion_alumnos','agenda_programacion_evaluaciones_estado','agenda_programacion_evaluaciones_estado_alumnos','agenda_programacion_evaluaciones_trimestrales','agenda_programacion_evaluaciones_comedor_trimestrales','agenda_programacion_evaluaciones_comedor_estado_alumnos','agenda_programacion_ingles','agenda_programacion_ingles_evaluacion_alumnos','agenda_programacion_ingles_evaluaciones_estado','agenda_programacion_ingles_evaluaciones_trimestrales') as $t)$allowed[$t]='zona_aulas';
if(!isset($allowed[$table])||!app_can($allowed[$table]))security_stop();
if(APP_SITE==='neokids'&&strpos($table,'agenda_programacion')===0&&(int)$nivel_acceso!==4&&!((int)$nivel_acceso===3&&app_can('zona_aulas')))security_stop('Usa la programación de tus grupos para modificar sus actividades.');
$field=(string)editor_value(array('campo','nombre_campo'));
$key=(string)editor_value(array('nombre_campo_busqueda','nombre_fila','nombre_id','campo_id'));
$id=editor_value(array('fila','id_fila','valor_busqueda','id_trimestre'));
if($key===''&&editor_value('campo_busqueda')!==''){$key=(string)editor_value('campo_busqueda');$id=editor_value('valor_busqueda');}
elseif(editor_value('nombre_campo_busqueda')!=='')$id=editor_value('campo_busqueda');
if($table==='aulas_grupos_trimestres'&&$key==='')$key='id_trimestre';
if($table==='agenda_alumnos_comidas'&&$key==='')$key='id';
if(!preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/',$field)||!preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/',$key)||!ctype_digit((string)$id)||(int)$id<1)security_stop('Identificador no válido.',422);
$columns=app_rows('SHOW COLUMNS FROM `'.$table.'`');$schema=array();$primary=array();
foreach($columns as $c){$schema[$c['Field']]=$c;if($c['Key']==='PRI')$primary[]=$c['Field'];}
if(count($primary)!==1||$primary[0]!==$key||!isset($schema[$field])||$field===$key)security_stop();
if($table==='usuarios'&&!in_array($field,array('medidas_complementarias'),true))security_stop();
if($table==='contactos'&&!in_array($field,array('consentimiento_fotos','consentimiento_blog','consentimiento_redes'),true))security_stop();
if($table==='alumnos'&&!in_array($field,array('agenda_grupo','orden_en_aula'),true))security_stop();
if($table==='detalles_factura'&&$field!=='subvencion_cobrada')security_stop();
if($table==='gestion_costes'&&$field!=='pagado')security_stop();
if(strpos($field,'id_')===0)security_stop();
$rows=app_rows('SELECT * FROM `'.$table.'` WHERE `'.$key.'`=?','i',array((int)$id));if(!$rows)security_stop('El registro ya no existe.',404);$row=$rows[0];
if((int)$nivel_acceso!==4){
    if($table==='usuarios')security_staff_record('usuarios','id_usuario',(int)$id);
    elseif($table==='detalles_factura')security_staff_record('facturas_emitidas','id_factura',(int)$row['id_factura']);
    elseif(!empty($row['id_centro'])){if(!security_owned_centre($row['id_centro']))security_stop();}
    elseif(!empty($row['id_aula']))security_staff_record('centro_aulas','id_aula',(int)$row['id_aula']);
    elseif(!empty($row['id_alumno'])&&strpos($table,'agenda_programacion')===0)security_staff_record('alumnos','id_alumno',(int)$row['id_alumno']);
    elseif(!empty($row['id_alumno'])&&in_array($table,array('agenda_alumnos_comidas','agenda_alumnos_seguimiento'),true))security_staff_record('usuarios','id_usuario',(int)$row['id_alumno']);
    else security_stop();
}
$value=editor_value(array('valor_campo','valor_nuevo','valor'));
if($value==='true')$value='1';elseif($value==='false')$value='0';
$toggle=strpos(basename($route),'checkbox')!==false||editor_value('es_checkbox')==='1';
if(strlen((string)$value)>100000)security_stop('El texto es demasiado largo.',422);
if(!$toggle && preg_match('/^(?:tinyint|smallint|mediumint|int|bigint|decimal|float|double)/i',$schema[$field]['Type']) && !is_numeric($value))security_stop('El valor debe ser numérico.',422);
if(!$toggle && preg_match('/^(?:date|datetime)/i',$schema[$field]['Type']) && !preg_match('/\A\d{4}-\d{2}-\d{2}(?: \d{2}:\d{2}:\d{2})?\z/',(string)$value))security_stop('La fecha no es válida.',422);
app_db()->set_charset('utf8mb4');
$extra='';if(editor_value('borrar_actividades')==='1'&&isset($schema['id_actividad'],$schema['actividad']))$extra=", id_actividad=0, actividad=''";
if($toggle)$ok=db_query_params('UPDATE `'.$table.'` SET `'.$field.'`=IF(`'.$field.'`=1,0,1)'.$extra.' WHERE `'.$key.'`=?','i',array((int)$id),app_db());
else $ok=db_query_params('UPDATE `'.$table.'` SET `'.$field.'`=?'.$extra.' WHERE `'.$key.'`=?','si',array(app_text($value),(int)$id),app_db());
if(!$ok)security_stop('No se ha podido guardar el cambio.',500);
if(editor_value('cerrar_ventana')==='1'){echo '<script>window.close();</script>';exit;}
if(editor_value('recargar')!==''&&editor_value('recargar')!=='0') {
    $query=http_build_query(array('id_aula'=>(int)editor_value('id_aula'),'fecha_inicio'=>date('Y-m-d',(int)editor_value('fecha_inicio')),'pagina_activa'=>(int)editor_value('pagina_activa')));
    header('Location: semana_programacion.php?'.$query,true,303);exit;
}
if(basename($route)==='guarda_dato_evaluacion_1.php'){header('Content-Type: application/json');echo json_encode(array('ok'=>1));exit;}
echo 'ok';exit;
