<?php
require_once __DIR__.'/common.php';
$filter=(string)app_value($_GET,'filtro','todos');
if(!in_array($filter,['todos','pendientes','recibidos','enviados','administracion'],true))$filter='todos';
$q=mb_substr(trim((string)app_value($_GET,'q')),0,120);$child=(int)app_value($_GET,'id_alumno');$page=max(1,min(100000,(int)app_value($_GET,'pagina',1)));
$types='';$values=[];$where=fm_scope($types,$values);
if($child){$student=fm_child($child);$where.=' AND n.id_alumno=?';$types.='i';$values[]=$child;}
if($q!==''){$where.=' AND (n.asunto LIKE ? OR CONCAT_WS(" ",u.Nombre,u.Apellido1,u.Apellido2) LIKE ?)';$types.='ss';$values[]='%'.$q.'%';$values[]='%'.$q.'%';}
if($filter==='pendientes')$where.=' AND '.fm_unread_sql();
if($filter==='recibidos')$where.=' AND COALESCE(n.tipo_nota,0)<>2';
if($filter==='enviados')$where.=' AND n.tipo_nota=2';
if($filter==='administracion')$where.=' AND n.destino=1';
$rows=app_rows('SELECT n.*,CONCAT_WS(" ",u.Nombre,u.Apellido1,u.Apellido2) AS alumno,'.fm_unread_sql().' AS pendiente,(SELECT MAX(CONCAT(r.fecha_respuesta," ",r.hora_respuesta)) FROM agenda_notas_padres_respuestas r WHERE r.id_nota=n.id_nota_padre) AS ultima_respuesta FROM agenda_notas_padres n LEFT JOIN usuarios u ON u.id_usuario=n.id_alumno WHERE '.$where.' ORDER BY GREATEST(CONCAT(n.fecha," ",COALESCE(n.hora,"00:00:00")),COALESCE(ultima_respuesta,"")) DESC,n.id_nota_padre DESC LIMIT 31 OFFSET '.(($page-1)*30),$types,$values);
$pending=fm_unread_count();
fm_header('Mensajes a familias');
?>
<div class="fm-summary"><span><strong><?=$pending?></strong> conversaciones pendientes</span><span>Historial completo · Ordenado por última actividad</span></div>
<form class="fm-toolbar" method="get"><label class="fm-search">Buscar alumno o asunto<input type="search" name="q" value="<?=app_escape($q)?>" placeholder="Nombre, apellidos o asunto" maxlength="120"></label><label>Mostrar<select name="filtro"><?php foreach(['todos'=>'Todas las conversaciones','pendientes'=>'Pendientes de leer','recibidos'=>'Iniciadas por familias','enviados'=>'Iniciadas por el equipo','administracion'=>'Para administración'] as $key=>$label){if($key==='administracion'&&!fm_manager())continue;?><option value="<?=$key?>" <?=$filter===$key?'selected':''?>><?=$label?></option><?php }?></select></label><?php if($child)fm_input('id_alumno',$child);?><button class="primary">Filtrar</button><a class="quiet" href="nuevo_mensaje.php<?=$child?'?id_alumno='.$child:''?>">+ Nuevo mensaje</a></form>
<?php if($child){?><p class="fm-student">Familia de <?=app_escape($student['alumno'])?> <a class="quiet" href="bandeja.php">Ver todas las familias</a></p><?php }?>
<div class="fm-list"><?php foreach(array_slice($rows,0,30) as $row){?><a class="fm-item <?=$row['pendiente']?'unread':''?>" href="ver_mensaje.php?id_mensaje=<?=(int)$row['id_nota_padre']?>"><div><span class="fm-muted"><?=app_escape($row['alumno']?:'Alumno archivado')?> · <?=(int)$row['tipo_nota']===2?'Enviado por el equipo':'Recibido de la familia'?></span><h2><?=app_escape($row['asunto']?:'Mensaje de la piscina')?></h2><p class="fm-muted"><?=app_escape(mb_substr(fm_plain($row['texto_nota']),0,140))?></p></div><div class="fm-meta"><span><?=app_escape(fm_date($row['ultima_respuesta']?substr($row['ultima_respuesta'],0,10):$row['fecha'],$row['ultima_respuesta']?substr($row['ultima_respuesta'],11):$row['hora']))?></span><span class="fm-badge <?=$row['pendiente']?'pending':''?>"><?=$row['pendiente']?'Pendiente de leer':'Al día'?></span><span><?=(int)$row['destino']===1?'Administración':'Equipo de piscina'?> →</span></div></a><?php }?></div>
<?php if(!$rows){?><section class="fm-empty"><h2>No hay conversaciones en esta vista</h2><p>Prueba otro filtro o busca por el nombre del alumno.</p></section><?php }?>
<nav class="fm-pages" aria-label="Páginas de mensajes"><?php $args=['q'=>$q,'filtro'=>$filter];if($child)$args['id_alumno']=$child;if($page>1){?><a class="quiet" href="?<?=app_escape(http_build_query($args+['pagina'=>$page-1]))?>">← Más recientes</a><?php }?><span>Página <?=$page?></span><?php if(count($rows)>30){?><a class="quiet" href="?<?=app_escape(http_build_query($args+['pagina'=>$page+1]))?>">Más conversaciones →</a><?php }?></nav>
<?php fm_footer(); ?>
