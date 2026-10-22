<?php
require __DIR__.'/common.php';app_db()->set_charset('utf8mb4');$group=(int)app_value($_GET,'grupo');$sub=(int)app_value($_GET,'sub');$g=pool_group($group,$sub);$enrol=(int)app_value($_GET,'alumno');
$students=app_rows('SELECT a.*,u.Nombre,u.Apellido1 FROM alumnos a JOIN usuarios u ON u.id_usuario=a.id_usuario WHERE a.id_alumno=? AND a.id_curso=? AND EXISTS(SELECT 1 FROM aulas_alumnos_grupos ag WHERE ag.id_alumno=a.id_alumno AND ag.id_curso=a.id_curso AND ag.id_grupo=? AND ag.id_subgrupo=?)','iiii',array($enrol,(int)$g['id_curso'],$group,$sub));
if(!$students){http_response_code(403);exit('El alumno no pertenece a este grupo.');}$student=$students[0];$base='evaluacion.php?grupo='.$group.'&sub='.$sub.'&alumno='.$enrol;
if($_SERVER['REQUEST_METHOD']==='POST'){
 app_check_csrf();if(app_value($_POST,'action')!=='save'){http_response_code(403);exit('No se permite borrar evaluaciones.');}
 $id=(int)app_value($_POST,'id');$date=app_value($_POST,'date');$title=trim(app_value($_POST,'title'));$body=trim(app_value($_POST,'body'));$published=app_value($_POST,'published')==='1'?1:0;
 if(!pool_valid_date($date)||$title===''||strlen($title)>150||$body===''||strlen($body)>12000)pool_error('Revisa la fecha, el título y la evaluación.');
 if($id){
  $old=app_rows('SELECT * FROM piscina_evaluaciones WHERE id=? AND id_alumno=? AND id_grupo=? AND id_subgrupo=?','iiii',array($id,$enrol,$group,$sub));if(!$old){http_response_code(403);exit;}
  $expected=(int)app_value($_POST,'version');if((int)$old[0]['version']!==$expected)pool_error('La evaluación ha cambiado. Recarga antes de guardar.');
  $db=app_db();$db->begin_transaction();
  $stmt=$db->prepare('UPDATE piscina_evaluaciones SET fecha=?,titulo=?,observaciones=?,publicada=?,autor=?,version=version+1,actualizada=NOW() WHERE id=? AND version=?');
  $stmt->bind_param('sssiiii',$date,$title,$body,$published,$idUsuario,$id,$expected);$ok=$stmt->execute();$affected=$stmt->affected_rows;$stmt->close();
  if(!$ok||$affected!==1){$db->rollback();pool_error('La evaluación ha cambiado. Recarga antes de guardar.');}
  if(!pool_history('evaluacion',$id,$old[0])){$db->rollback();pool_error('No se ha podido conservar el historial.');}$db->commit();
 }else $ok=db_query_params('INSERT INTO piscina_evaluaciones (id_alumno,id_grupo,id_subgrupo,fecha,titulo,observaciones,autor,publicada,actualizada) VALUES (?,?,?,?,?,?,?,?,NOW())','iiisssii',array($enrol,$group,$sub,$date,$title,$body,$idUsuario,$published),app_db());
 if(!$ok)pool_error('No se ha podido guardar la evaluación.');header('Location: '.$base.'&guardado=1',true,303);exit;
}
$rows=app_rows('SELECT * FROM piscina_evaluaciones WHERE id_alumno=? AND id_grupo=? AND id_subgrupo=? ORDER BY fecha DESC,id DESC','iii',array($enrol,$group,$sub));
pool_header('Evaluación del alumno','grupos');echo '<a class="back" href="programacion.php?grupo='.$group.'&sub='.$sub.'">← Volver a la programación</a><h2>'.app_escape($student['Nombre'].' '.$student['Apellido1']).'</h2>';if(isset($_GET['guardado']))echo '<p class="notice">Evaluación guardada.</p>';
$rows[]=array('id'=>0,'version'=>0,'titulo'=>'','observaciones'=>'','fecha'=>date('Y-m-d'),'publicada'=>0);
foreach($rows as $r){ ?><details class="card" <?php if(!$r['id'])echo 'open'; ?>><summary><?php echo $r['id']?app_escape($r['fecha'].' · '.$r['titulo']).($r['publicada']?' · Visible para la familia':' · Borrador'):'+ Nueva evaluación'; ?></summary><form method="post" class="stack lesson-form"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>"><input type="hidden" name="version" value="<?php echo (int)$r['version']; ?>"><label>Fecha<input type="date" name="date" value="<?php echo $r['fecha']; ?>" required></label><label>Título<input name="title" maxlength="150" value="<?php echo app_escape($r['titulo']); ?>" placeholder="Sus avances en el agua" required></label><label>Evaluación y próximos pasos<textarea name="body" rows="7" maxlength="12000" required><?php echo app_escape($r['observaciones']); ?></textarea></label><label class="check-row"><input type="checkbox" name="published" value="1" <?php if($r['publicada'])echo 'checked'; ?>> Mostrar esta evaluación a la familia</label><button class="primary">Guardar evaluación</button></form></details><?php } pool_footer(); ?>
