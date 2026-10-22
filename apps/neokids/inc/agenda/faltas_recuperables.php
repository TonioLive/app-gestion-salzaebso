<?php

$busca_faltas_recuperables=db_query_params('SELECT * FROM usuarios, alumnos, servicios_consumidos_faltas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=servicios_consumidos_faltas.id_alumno AND alumnos.fecha_baja>=? AND servicios_consumidos_faltas.estado=0 AND servicios_consumidos_faltas.id_grupo=0 AND servicios_consumidos_faltas.id_subgrupo=0 ORDER BY usuarios.Nombre, usuarios.Apellido1, usuarios.Apellido2', 's', array((string)(date("Y-m-d")))) or die(db_error());

$busca_faltas_recuperables_programadas=db_query_params('SELECT * FROM usuarios, alumnos, servicios_consumidos_faltas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=servicios_consumidos_faltas.id_alumno AND alumnos.fecha_baja>=? AND servicios_consumidos_faltas.estado=0 AND servicios_consumidos_faltas.id_grupo!=0 AND hora_entrada is Null ORDER BY usuarios.Nombre, usuarios.Apellido1, usuarios.Apellido2, fecha_recuperacion', 's', array((string)(date("Y-m-d")))) or die(db_error());

if ((db_num_rows($busca_faltas_recuperables)!=0) or (db_num_rows($busca_faltas_recuperables_programadas))) {
?>
<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>Faltas justificadas recuperables</h4>
                        <h5><button class="btn btn-danger btn-xs pull-right" onclick="muestra_faltas()">Ver listado</button><?php  if (db_num_rows($busca_faltas_recuperables)!=0) { ?>Hay <?=db_num_rows($busca_faltas_recuperables)?> faltas recuperables pendientes de tramitar.<?php  } ?></h5>
                        <h5><button class="btn btn-warning btn-xs pull-right" onclick="muestra_recuperaciones()">Ver programación</button><?php  if (db_num_rows($busca_faltas_recuperables_programadas)!=0) { ?>Hay <?=db_num_rows($busca_faltas_recuperables_programadas)?> sesiones de recuperación programadas.<?php  } ?></h5><br />
</div>
<?php 
} else {
?>
<div class="alert alert-danger alert-dismissible" role="alert">
<button class="btn btn-warning btn-xs" onclick="muestra_recuperaciones()">Ver programación de recuperaciones</button>
</div>
<?php 
}

?>
