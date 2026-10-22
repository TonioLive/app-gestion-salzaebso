<?php
require dirname(dirname(__DIR__)).'/configuracion/config.php';
require dirname(dirname(__DIR__)).'/configuracion/autentifica_admin.php';
require_once __DIR__.'/alta_model.php';
$error='';$draft=null;$actor=null;$mode=personal_value($_GET,'modo','');
try{
 $actor=personal_actor();
 if(isset($_SESSION['personal_draft'])&&$_SESSION['personal_draft']['created']>time()-3600&&(int)$_SESSION['personal_draft']['centre']===(int)$centro_actual)$draft=$_SESSION['personal_draft'];
 if(app_request_value($_SERVER,'REQUEST_METHOD')==='POST'){
  lap_wf_post();$action=personal_value($_POST,'action');
  if($action==='discard'){
   if(!$draft||!hash_equals($draft['token'],personal_value($_POST,'draft')))throw new DomainException('El borrador ya no está disponible.');
   personal_discard_draft($draft);unset($_SESSION['personal_draft']);header('Location: alta.php',true,303);exit;
  }
  if($action==='manual'||$action==='import'){
   $data=personal_defaults();$upload=null;
   if($action==='import'){$data['fecha_inicio']='';$data['tipo_contrato']='0';$data=array_merge($data,personal_contract_parse(personal_value($_POST,'contract_text')));$upload=personal_upload(isset($_FILES['contract'])?$_FILES['contract']:null);}
   if($draft)personal_discard_draft($draft);
   $courses=lap_wf_rows('SELECT id_curso FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? ORDER BY fecha_inicio DESC LIMIT 1','ss',array($data['fecha_inicio'],$data['fecha_inicio']));if($courses)$data['id_curso']=$courses[0]['id_curso'];
   foreach(array('zona_alumnos','zona_aulas','zona_mensajes') as $defaultPermission)$data[$defaultPermission]=((int)$actor['nivel_acceso']===4||!empty($GLOBALS[$defaultPermission]))?1:0;
   $draft=array('token'=>bin2hex(gestion_password_random(32)),'centre'=>(int)$centro_actual,'actor'=>(int)$actor['id_usuario'],'created'=>time(),'data'=>$data,'upload'=>$upload,'source'=>$action);
   $_SESSION['personal_draft']=$draft;header('Location: alta.php?modo=revisar',true,303);exit;
  }
  if($action==='create'){
   if(!$draft||!hash_equals($draft['token'],personal_value($_POST,'draft')))throw new DomainException('El borrador ya no está disponible. Empieza un alta nueva.');
   $draft['data']=personal_defaults();foreach($draft['data'] as $field=>$default)$draft['data'][$field]=personal_value($_POST,$field,$default);
   foreach(array_merge(array('l','m','x','j','v','s','d'),array_keys(personal_permissions())) as $field)$draft['data'][$field]=personal_value($_POST,$field,'0')==='1'?'1':'0';$_SESSION['personal_draft']=$draft;
   $result=personal_create($_POST,$draft);$_SESSION['personal_created']=$result;unset($_SESSION['personal_draft']);header('Location: alta.php?modo=creado',true,303);exit;
  }
  throw new InvalidArgumentException('La acción no es válida.');
 }
}catch(Exception $e){http_response_code($e instanceof DomainException?403:422);$error=$e->getMessage();if(isset($_SESSION['personal_draft']))$draft=$_SESSION['personal_draft'];}
$created=isset($_SESSION['personal_created'])&&$_SESSION['personal_created']['created']>time()-900?$_SESSION['personal_created']:null;
if(!$created)unset($_SESSION['personal_created']);
lap_wf_start('Alta de personal','Contrato, horario, escuela, aula y acceso al programa en un único proceso.');lap_wf_error($error);
?>
<ol class="wf-steps"><li>1 · Manual o contrato PDF</li><li>2 · Revisar datos y trabajo</li><li>3 · Crear credenciales</li><li>4 · Primer acceso del trabajador</li></ol>
<?php if(!$actor){lap_wf_end();return;} if($mode==='creado'&&$created){
 $accessBase=preg_match('~^https?://~i',$url_web)?$url_web:$dominio_actual;
 $accessUrl=rtrim($accessBase,'/').'/index.php';
 $message='Hola '.$created['name'].', ya tienes acceso al programa de Lápices.'."\n\n".'Acceso: '.$accessUrl."\n".'Usuario: '.$created['username']."\n".'Contraseña: '.$created['password']."\n\n".'En tu primer acceso debes completar tu teléfono, dirección, correo electrónico y teléfono de emergencia para entrar al programa.';
 ?>
 <section class="wf-card"><h2>Alta completada</h2><p class="wf-success">Se han guardado la ficha, el contrato, el horario y la asignación de aula indicada. El trabajador completará sus datos personales al entrar.</p><p>Credenciales preparadas para compartir con <?=app_escape($created['name'])?>.</p><textarea id="credentials-message" class="wf-result" rows="10" readonly aria-label="Mensaje con las credenciales"><?=app_escape($message)?></textarea><div class="wf-actions"><button class="wf-btn" type="button" data-copy-credentials>Copiar mensaje</button><a class="wf-btn secondary" href="/gestion/usuarios/gestion_u.php">Ver personal</a><a class="wf-btn secondary" href="alta.php">Nueva alta</a></div><p class="wf-status" data-copy-status role="status">El mensaje no se envía automáticamente. Cópialo y compártelo con el trabajador.</p><?php if(!empty($created['id'])){ ?><a class="wf-link" href="/gestion/docentes/horario_docente.php?id_docente=<?=(int)$created['id']?>">Ver contrato y horario</a><?php if(!empty($created['contract'])){ ?> <a class="wf-link" href="contrato.php?id=<?=(int)$created['id']?>">Descargar contrato PDF</a><?php } } ?></section>
<?php }elseif($draft&&$mode==='revisar'){
 $data=$draft['data'];$categories=lap_wf_rows('SELECT * FROM personal_cat_laborales ORDER BY nombre_categoria');$types=lap_wf_rows('SELECT * FROM personal_tipos_contrato ORDER BY id_tipo_contrato');$courses=lap_wf_rows('SELECT id_curso,nombre FROM cursos_academicos ORDER BY fecha_inicio DESC');
 $centres=personal_centres($actor);$centreIds=array();foreach($centres as &$centre){$centreIds[]=(int)$centre['id_centro'];$centre['label']=$centre['nombre_centro'].($centre['poblacion_centro']!==''?' ('.$centre['poblacion_centro'].')':'');}unset($centre);
 $classes=$centreIds?lap_wf_rows('SELECT id_aula,id_centro,id_curso,nombre FROM centro_aulas WHERE id_centro IN ('.implode(',',array_fill(0,count($centreIds),'?')).') AND tipo=0 ORDER BY id_curso DESC,nombre',str_repeat('i',count($centreIds)),$centreIds):array();
 function alta_select($name,$label,$rows,$key,$text,$value,$blank=true){echo '<label>'.app_escape($label).'<select name="'.app_escape($name).'">';if($blank)echo '<option value="0">Selecciona una opción</option>';foreach($rows as $row)echo '<option value="'.(int)$row[$key].'"'.((int)$row[$key]===(int)$value?' selected':'').'>'.app_escape($row[$text]).'</option>';echo '</select></label>';}
 ?>
 <?php if($draft['source']==='import'){ ?><p class="wf-success">Contrato leído: <?=app_escape($draft['upload']['name'])?>. Revisa los datos propuestos y completa los campos que estén vacíos antes de crear el usuario.</p><?php } ?>
 <form method="post" data-staff-review><?php lap_wf_token(); ?><input type="hidden" name="action" value="create"><input type="hidden" name="draft" value="<?=app_escape($draft['token'])?>">
 <section class="wf-card"><h2>Datos personales</h2><div class="wf-grid"><?php foreach(array('Nombre'=>'Nombre','Apellido1'=>'Primer apellido','Apellido2'=>'Segundo apellido','DNI'=>'DNI / NIE','NSS'=>'Número de Seguridad Social','profesion'=>'Puesto de trabajo') as $field=>$label)lap_wf_input($label,$field,personal_value($data,$field),'text',$field!=='Apellido2'); ?></div></section>
 <section class="wf-card"><h2>Contrato y periodo de trabajo</h2><div class="wf-grid">
 <?php alta_select('id_categoria','Categoría laboral',$categories,'id_categoria','nombre_categoria',personal_value($data,'id_categoria'));alta_select('tipo_contrato','Tipo de contrato',$types,'id_tipo_contrato','nombre_tipo_contrato',personal_value($data,'tipo_contrato'));lap_wf_input('Fecha de inicio','fecha_inicio',personal_value($data,'fecha_inicio'),'date',true);lap_wf_input('Fecha de fin (si corresponde)','fecha_fin',personal_value($data,'fecha_fin'),'date');lap_wf_input('Horas semanales','horas_semana',personal_value($data,'horas_semana'),'text',true); ?>
 <label>Perfil de acceso<select name="nivel_acceso"><?php foreach(array(2=>'Educadora',3=>'Coordinador/a',10=>'Cocina',11=>'Personal no docente',4=>'Administrador') as $role=>$label){if($role===4&&(int)$actor['nivel_acceso']!==4)continue; ?><option value="<?=$role?>" <?=(int)personal_value($data,'nivel_acceso')===$role?'selected':''?>><?=$label?></option><?php } ?></select></label>
 </div><p class="wf-help">El puesto del contrato y el perfil de acceso pueden ser distintos. Revisa ambos.</p>
 <details><summary>Horario habitual (opcional)</summary><div class="wf-grid"><?php foreach(array('hora_entrada1'=>'Primera entrada','hora_salida1'=>'Primera salida','hora_entrada2'=>'Segunda entrada','hora_salida2'=>'Segunda salida') as $field=>$label)lap_wf_input($label,$field,personal_value($data,$field),'time'); ?></div></details>
 <div class="wf-days"><?php foreach(array('l'=>'Lunes','m'=>'Martes','x'=>'Miércoles','j'=>'Jueves','v'=>'Viernes','s'=>'Sábado','d'=>'Domingo') as $day=>$label){ ?><label class="wf-check"><input type="checkbox" name="<?=$day?>" value="1" <?=personal_value($data,$day)==='1'?'checked':''?>><?=$label?></label><?php } ?></div>
 </section>
 <section class="wf-card"><h2>Escuela y aula de trabajo</h2><p class="wf-help">La escuela se guardará en la ficha y en el horario del trabajador.</p><div class="wf-grid"><?php alta_select('id_centro','Escuela',$centres,'id_centro','label',personal_value($data,'id_centro',(string)$draft['centre']),false); ?></div><div data-class-section><p class="wf-help">Para personal docente. Elige el curso y un aula de la escuela seleccionada.</p><div class="wf-grid"><?php alta_select('id_curso','Curso',$courses,'id_curso','nombre',personal_value($data,'id_curso')); ?><label>Aula<select name="id_aula"><option value="0">Sin aula asignada</option><?php foreach($classes as $class){ ?><option value="<?=(int)$class['id_aula']?>" data-centre="<?=(int)$class['id_centro']?>" data-course="<?=(int)$class['id_curso']?>" <?=(int)personal_value($data,'id_aula')===(int)$class['id_aula']?'selected':''?>><?=app_escape($class['nombre'])?></option><?php } ?></select></label></div></div></section>
 <details class="wf-card"><summary>Datos de contacto (los confirmará el trabajador)</summary><p class="wf-help">No es necesario rellenarlos ahora. Los cuatro serán obligatorios antes de acceder al programa.</p><div class="wf-grid"><?php foreach(array('telefono1'=>'Teléfono','Direccion'=>'Dirección completa','email'=>'Correo electrónico','telefono_contacto1'=>'Teléfono de emergencia') as $field=>$label)lap_wf_input($label,$field,personal_value($data,$field),$field==='email'?'email':'text'); ?></div></details>
 <details class="wf-card"><summary>Permisos del programa</summary><div class="wf-permissions"><?php foreach(personal_permissions() as $permission=>$label){if((int)$actor['nivel_acceso']!==4&&empty($GLOBALS[$permission]))continue; ?><label class="wf-check"><input type="checkbox" name="<?=$permission?>" value="1" <?=personal_value($data,$permission)==='1'?'checked':''?>><?=$label?></label><?php } ?></div></details>
 <div class="wf-actions"><button class="wf-btn" type="submit">Guardar alta y crear credenciales</button><a class="wf-btn secondary" href="alta.php">Volver</a></div><p class="wf-help">Se crearán el usuario, su contrato y su horario juntos. No se envía ningún correo automáticamente.</p></form><form method="post" class="wf-actions"><?php lap_wf_token(); ?><input type="hidden" name="action" value="discard"><input type="hidden" name="draft" value="<?=app_escape($draft['token'])?>"><button type="submit" class="wf-btn secondary">Descartar borrador</button></form>
<?php }else{ ?>
<div class="wf-grid"><section class="wf-card"><h2>Desde un contrato PDF</h2><p class="wf-help">Lee nombre, apellidos, DNI/NIE, Seguridad Social, puesto, fechas y horas. Después podrás revisar y completar la ficha.</p><form method="post" enctype="multipart/form-data" data-contract-upload><?php lap_wf_token(); ?><input type="hidden" name="action" value="import"><input type="hidden" name="contract_text"><div class="wf-upload"><label>Contrato de trabajo<input type="file" name="contract" accept="application/pdf,.pdf" required></label><p class="wf-help">PDF de hasta 8 MB y 30 páginas. Admite texto y documentos escaneados.</p><progress value="0" max="1" hidden></progress><p data-contract-status role="status" aria-live="polite"></p></div><div class="wf-actions"><button class="wf-btn" type="submit">Leer contrato y continuar</button></div><noscript><p>Activa JavaScript para leer el PDF o utiliza el alta manual.</p></noscript></form></section>
<section class="wf-card"><h2>Alta manual</h2><p class="wf-help">Introduce los datos directamente. Tendrás los mismos campos de contrato, horario, escuela, aula y permisos.</p><form method="post"><?php lap_wf_token(); ?><input type="hidden" name="action" value="manual"><button class="wf-btn secondary" type="submit">Continuar sin contrato</button></form><?php if($draft){ ?><div class="wf-actions"><a class="wf-link" href="alta.php?modo=revisar">Continuar el borrador pendiente</a></div><?php } ?></section></div>
<a class="wf-link" href="/gestion/usuarios/gestion_u.php">Volver al personal</a><script type="module" src="/gestion/personal/assets/contrato.js?v=20261016.3"></script>
<?php } ?>
<script src="/gestion/personal/assets/alta.js?v=20261016.3" defer></script>
<?php lap_wf_end(); ?>
