<?php
require __DIR__.'/programaciones-common.php';
$templates=app_rows("SELECT p.*, TRIM(CONCAT_WS(' ',u.Nombre,u.Apellido1,u.Apellido2)) AS monitor_nombre,
 COALESCE(a.sesiones_preparadas,0) AS sesiones_preparadas,COALESCE(v.grupos,0) AS grupos
 FROM agenda_programacion_plantillas p LEFT JOIN usuarios u ON u.id_usuario=p.monitor
 LEFT JOIN (SELECT a.id_plantilla,COUNT(DISTINCT a.numero_sesion) AS sesiones_preparadas FROM agenda_programacion_plantillas_actividades a JOIN agenda_programacion_plantillas p ON p.id_plantilla=a.id_plantilla WHERE a.numero_sesion BETWEEN 1 AND p.num_sesiones GROUP BY a.id_plantilla) a ON a.id_plantilla=p.id_plantilla
 LEFT JOIN (SELECT id_plantilla,COUNT(*) AS grupos FROM agenda_programacion_grupos_vinculados GROUP BY id_plantilla) v ON v.id_plantilla=p.id_plantilla
 ORDER BY p.nivel,p.descripcion,p.id_plantilla");
$monitors=[];$assigned=0;$pending=0;
foreach($templates as $row){$monitors[(int)$row['monitor']]=$row['monitor_nombre']?:'Sin monitor asignado';$assigned+=(int)$row['grupos']>0?1:0;$pending+=(int)$row['sesiones_preparadas']<(int)$row['num_sesiones']?1:0;}
asort($monitors,SORT_NATURAL|SORT_FLAG_CASE);
prog_header('Programaciones','Organiza las sesiones por nivel, revisa su preparación y asígnalas a los grupos.');
$messages=['saved'=>'Programación guardada.','copied'=>'Copia creada. Asigna su monitor antes de utilizarla.','deleted'=>'Programación eliminada.'];
if(isset($messages[app_value($_GET,'done')])){?><p class="prog-notice" role="status"><?=app_escape($messages[app_value($_GET,'done')])?></p><?php }?>
<div class="prog-summary"><div><strong><?=count($templates)?></strong><span>Programaciones</span></div><div><strong><?=$assigned?></strong><span>Con grupos asignados</span></div><div><strong><?=$pending?></strong><span>Con sesiones por preparar</span></div><a class="prog-button" href="gestion_plantillas.php">+ Nueva programación</a></div>
<section class="prog-panel" aria-label="Biblioteca de programaciones">
<form method="get" class="prog-filters" id="prog-filters" role="search">
<label class="prog-search">Buscar<input type="search" name="q" id="prog-search" placeholder="Nombre de la programación o monitor" autocomplete="off"></label>
<label>Nivel<select id="prog-level"><option value="">Todos los niveles</option><?php for($i=0;$i<4;$i++){?><option value="<?=$i?>"><?=app_escape(prog_level($i))?></option><?php }?></select></label>
<label>Monitor<select id="prog-monitor"><option value="">Todos los monitores</option><?php foreach($monitors as $id=>$name){?><option value="<?=$id?>"><?=app_escape($name)?></option><?php }?></select></label>
<label>Preparación<select id="prog-state"><option value="">Todas</option><option value="ready">Sesiones preparadas</option><option value="pending">Con sesiones pendientes</option></select></label>
<button class="prog-button secondary" type="reset">Limpiar filtros</button></form><p class="prog-result-count" id="prog-results" aria-live="polite"><?=count($templates)?> programaciones</p>
<div class="prog-table-wrap"><table class="prog-table"><caption class="sr-only">Programaciones disponibles, preparación de sesiones y acciones</caption><thead><tr><th scope="col">Programación</th><th scope="col">Monitor</th><th scope="col">Sesiones</th><th scope="col">Grupos</th><th scope="col">Acciones</th></tr></thead><tbody>
<?php foreach($templates as $row){$id=(int)$row['id_plantilla'];$total=max(0,(int)$row['num_sesiones']);$ready=min($total,(int)$row['sesiones_preparadas']);$complete=$total>0&&$ready===$total; ?>
<tr data-programacion data-level="<?=(int)$row['nivel']?>" data-monitor="<?=(int)$row['monitor']?>" data-state="<?=$complete?'ready':'pending'?>" data-search="<?=app_escape($row['descripcion'].' '.$row['monitor_nombre'])?>">
<td><a class="prog-name" href="gestion_plantillas.php?id_plantilla=<?=$id?>"><?=app_escape($row['descripcion']?:'Programación sin nombre')?></a><div class="prog-row-meta"><span class="prog-badge level-<?=(int)$row['nivel']?>"><?=app_escape(prog_level($row['nivel']))?></span><span><?=(int)$row['periodicidad']===1?'Mensual':'Semanal'?></span></div></td>
<td><?=app_escape($row['monitor_nombre']?:'Sin asignar')?></td><td><div class="prog-progress-label"><strong><?=$ready?> / <?=$total?></strong><span>Preparadas</span></div><progress value="<?=$ready?>" max="<?=max(1,$total)?>" aria-label="<?=$ready?> de <?=$total?> sesiones preparadas"></progress></td><td><span class="prog-group-count"><?=(int)$row['grupos']?></span></td>
<td><div class="prog-row-actions"><a class="prog-button small" href="gestion_plantillas.php?id_plantilla=<?=$id?>">Abrir</a><form method="post" action="duplicar_plantilla.php" data-confirm="Se creará una copia con todas sus sesiones, sin monitor ni grupos asignados."><input type="hidden" name="id_plantilla" value="<?=$id?>"><button class="prog-button small secondary" type="submit">Duplicar</button></form><details class="prog-more"><summary aria-label="Más acciones para <?=app_escape($row['descripcion'])?>">Más</summary><form method="post" action="borrar_plantilla.php" data-confirm="¿Eliminar esta programación sin grupos asignados? Esta acción no se puede deshacer."><input type="hidden" name="id_plantilla" value="<?=$id?>"><button class="prog-button small danger" type="submit" <?=(int)$row['grupos']>0?'disabled title="Desvincula primero sus grupos desde la programación"':''?>>Eliminar</button></form></details></div></td></tr>
<?php }?></tbody></table></div><div class="prog-empty" id="prog-empty" <?=count($templates)?'hidden':''?>><h2>No hay programaciones para mostrar</h2><p>Prueba otros filtros o crea una nueva programación.</p></div></section>
<aside class="prog-help">La biblioteca define las sesiones de cada nivel. Para consultar y editar el trabajo de una clase concreta, entra en <a href="/gestion/piscina/grupos.php">Grupos y horarios</a>.</aside>
<?php prog_footer(); ?>