<?php
require_once dirname(dirname(__DIR__)).'/configuracion/pool-planning.php';
$pool=APP_SITE==='neokids';
$brand=$pool ? 'Neo Kids' : 'Sant Pau';
$subtitle=$pool ? 'Gestión de piscina' : 'Escuelas municipales';
$sections=array();
function panel_section($key,$title,$summary,$accent,$items,$permission) {
    if (!app_can($permission)) {
        if($key!=='mensajes' || (int)app_value($_SESSION,'nivel_acceso',0)!==2 || !app_can('zona_aulas'))return;
        $items=array('Mensajes de mis familias'=>'/gestion/mensajes/bandeja.php','Nuevo mensaje a mis familias'=>'/gestion/mensajes/nuevo_mensaje.php');
    }
    $valid=array();
    foreach ($items as $label=>$href) {
        $path=parse_url($href,PHP_URL_PATH);
        $file=dirname(dirname(__DIR__)).$path;
        if (is_file($file) || is_file(rtrim($file,'/').'/index.php')) $valid[]=array('label'=>$label,'href'=>$href);
    }
    if ($valid) $GLOBALS['sections'][]=array('key'=>$key,'title'=>$title,'summary'=>$summary,'accent'=>$accent,'items'=>$valid);
}
if ($permitido && $centro_actual) {
    if ($pool && pool_manager()) panel_section('grupos','Grupos y horarios','Planifica las clases y consulta la ocupación.','blue',array('Horario de grupos'=>'/gestion/piscina/grupos.php','Grupos de escuelas'=>'/gestion/grupos_lapices_nuevo/','Grupos externos'=>'/gestion/grupos_externos/','Informe de ocupación'=>'/gestion/grupos_pruebas/ocupacion.php','Programaciones'=>'/gestion/grupos_pruebas/gestion_programaciones.php'),'zona_aulas');
    panel_section('alumnos','Alumnos','Inscripciones, familias y seguimiento.','rose',array('Gestión de alumnos'=>'/alumnos/gestion_alumnos.php','Contactos'=>'/contactos/listado_contactos.php','Respuestas de familias'=>'/formulario/listado.php','Lista de espera'=>'/lista_espera/listas_espera.php','Alergias y datos médicos'=>'/gestion/centros/listado_alergias.php','Listados'=>'/gestion/listados/gestion_listados.php'),'zona_alumnos');
    panel_section('aulas',$pool?'Asistencia':'Aulas','Organización y seguimiento del día a día.','blue',$pool?array('Control de asistencia'=>'/aulas/calendario_control.php','Alumnos por niveles'=>'/gestion/centros/gestion_aulas.php'):array('Listado de aulas'=>'/gestion/centros/gestion_aulas.php','Asistencia de alumnos'=>'/aulas/gestion_aulas.php','Agenda educativa'=>'/gestion/agenda_dia/calendario_programacion.php'),'zona_aulas');
    panel_section('facturacion','Facturación','Facturas, cobros y servicios del centro.','amber',array('Gestión de facturas'=>'/facturacion/gestion_facturacion.php','Caja'=>'/caja/listado_caja.php','Servicios y tarifas'=>'/facturacion/gestion_familias.php','Recibos de hoy'=>'/pdf/recibos_hoy.php'),'zona_facturacion');
    if (!$pool) panel_section('comedor','Comedor','Menús y asistencia al comedor.','green',array('Asistencia al comedor'=>'/aulas/calendario_mes_comedor.php','Previsión de menús'=>'/cocina/calendario_mes_comedor.php','Imprimir menús'=>'/menu/menus_mes.php'),'zona_comedor');
    panel_section('mensajes','Comunicación','Mensajes y avisos para las familias.','violet',array('Avisos a familias'=>'/gestion/piscina/avisos.php','Mensajes de familias'=>'/gestion/mensajes/bandeja.php','Nuevo mensaje a familias'=>'/gestion/mensajes/nuevo_mensaje.php'),'zona_mensajes');
    panel_section('personal','Personal','Equipo, horarios y documentación.','green',array('Gestión de personal'=>'/facturacion/gestion_educadoras.php','Contratos y calendario'=>'/gestion/personal/ficha.php'),'zona_personal');
    panel_section('almacen','Material','Reservas, entregas y suministros.','violet',array('Suministros'=>'/almacen/gestion_almacen.php','Reservas de material'=>'/gestion/material/gestion_material.php','Control de entregas'=>'/gestion/material/control_entregas.php'),'zona_almacen');
    panel_section('incidencias','Incidencias','Consulta y seguimiento de incidencias.','amber',array('Gestionar incidencias'=>'/gestion/incidencias/menu_incidencias.php'),'zona_incidencias');
    panel_section('pedidos','Pedidos','Pedidos del centro y entregas.','blue',array('Validar pedidos'=>'/almacen/pedidos_centro.php','Pedidos tramitados'=>'/almacen/calendario_pedidos_centro.php','Entregas'=>'/almacen/calendario_pedidos_entregados.php'),'zona_pedidos');
    if (!$pool) panel_section('gabinete','Gabinete','Evaluaciones y seguimiento educativo.','rose',array('Pruebas e informes'=>'/gestion/gabinete/gestion_aulas.php'),'zona_gabinete');
    if ($nivel_acceso===2 || ($pool && pool_staff_groups() && !pool_manager())) {
        $sections[]=array('key'=>'mi-aula','title'=>$pool?'Mis clases':'Mi aula','summary'=>'Herramientas para el equipo docente.','accent'=>'blue','items'=>array(array('label'=>$pool?'Mis grupos y programaciones':'Mis aulas','href'=>$pool?'/gestion/piscina/grupos.php':'/aulas_docentes/mis_aulas.php'),array('label'=>'Agenda y jornada','href'=>'/jornada.php')));
    }
    if (APP_SITE==='santpau' && $idUsuario===6297) {
        $sections=array_values(array_filter($sections,function($s) { return in_array($s['key'],array('alumnos','aulas','comedor')); }));
    }
}
if ($permitido && (int)$nivel_acceso===4) panel_section('piscina','Estado de piscina','Temperatura, humedad y acceso a los equipos.','blue',array('Consola de piscina'=>'/gestion/piscina/estado.php'),'zona_inferior');
$centres=$permitido ? app_centres() : array();
$admin_items=array();
if ($permitido && $centro_actual && ($nivel_acceso===4 || !empty($zona_inferior))) {
    $admin_items=app_rows('SELECT Texto,link,texto_ayuda FROM menu_administrador'.($nivel_acceso===4?'':' WHERE nivel_acceso=3').' ORDER BY Orden');
    $admin_items=array_values(array_filter($admin_items,function($row){$path=$row['link']; if(strpos($path,'enviar_mensaje_edu')!==false)return false; return substr($path,0,1)==='/' && substr($path,0,2)!=='//' && strpos($path,'\\')===false && (is_file(dirname(dirname(__DIR__)).parse_url($path,PHP_URL_PATH)) || is_file(dirname(dirname(__DIR__)).rtrim(parse_url($path,PHP_URL_PATH),'/').'/index.php'));}));
}
$weekdays=array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
$months=array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
$today=ucfirst($weekdays[date('w')]).', '.date('j').' de '.$months[date('n')-1];
$greeting=(int)date('G')<14?'Buenos días':((int)date('G')<21?'Buenas tardes':'Buenas noches');
$initial=function_exists('mb_substr')?mb_substr(app_text($nombre),0,1,'UTF-8'):substr($nombre,0,1);

$secondary_sections=array();
if ($pool) {
    $primary=array();
    foreach($sections as $section) {
        if(in_array($section['key'],array('grupos','alumnos','facturacion','mensajes','piscina','mi-aula'),true))$primary[]=$section;
        else $secondary_sections[]=$section;
    }
    $sections=$primary;
}
