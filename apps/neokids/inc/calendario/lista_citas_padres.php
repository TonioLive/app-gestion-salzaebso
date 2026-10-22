<?php

$busca_nota=db_query_params('SELECT * FROM Agenda LEFT JOIN agenda_asistentes ON Agenda.id=agenda_asistentes.id_cita WHERE agenda_asistentes.nivel=1 AND Agenda.Fecha>=? ORDER BY Fecha;', 's', array((string)(date("Y-m-d", time() - 86400 * 7))));	

if (db_num_rows($busca_nota)!=0) {
	
?>
<br />	
<div class="alert alert-info alert-dismissible" role="alert">
	<h5>Notificaciones a los alumnos</h5>
	<?php
	while($datos_nota=db_fetch_array($busca_nota)) {
	?>
	<div class="row">
		<div class="col-md-12">
			<small><b><?=date("d/m/Y",strtotime($datos_nota["Fecha"]));?></b>
			<br />
			<?=$datos_nota["Titulo"]?>
			<br />
			<button class="btn btn-primary btn-xs pull-right" onclick="ampliar_dia('/agenda/modif_cita.php?oc=1&id=<?= $datos_nota["id"]; ?>')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="btn btn-primary btn-xs" onclick="ampliar_dia('/gestion/grupos_pruebas/envios/registro_notificacion.php?id_cita=<?= $datos_nota["id"]; ?>')"><i class="fa fa-envelope" aria-hidden="true"></i></button><br /><br /></small>
		</div>	
	</div>
	<?php	
	}
	?>
</div>
<?php
		
}
	
?>
