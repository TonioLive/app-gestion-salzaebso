<?php 

$dia_comedor=$_GET["dia"];
if (strlen($dia_comedor)==0)
{
  $fecha_comedor_a=time();
}
  else
{

  if (is_numeric($dia_comedor)) {
  	$dia_comedor_timestamp=$dia_comedor;
  } else {
  	$dia_comedor_timestamp=strtotime($dia_comedor);
  }

  $fecha_comedor_a=$dia_comedor_timestamp;
}

// Busca alumnos asisten hoy
$busca_alumnos_comedor_a=db_query_params('SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_centro, alumnos.aula', 'ss', array((string)(date("Y-m-d", $fecha_comedor_a)), (string)(date("Y-m-d", $fecha_comedor_a)))) or die(db_error());

$contador_comedor=0;
$contador_total=0;

while($listado_alumnos_comedor_a=db_fetch_array($busca_alumnos_comedor_a)){
        
                // Comprueba si asiste hoy al comedor
                $id_inscripcion=$listado_alumnos_comedor_a["id_alumno"];

                $busca_inscripcion_comedor=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=3 AND servicios_contratados.fecha_inicio<=? AND servicios_contratados.fecha_fin>=?', 'sss', array($id_inscripcion, (string)(date("Y-m-d", $fecha_comedor_a)), (string)(date("Y-m-d", $fecha_comedor_a)))) or die(db_error());
                if (db_num_rows($busca_inscripcion_comedor)==0) {
                        $comedor=2;
                        } else {
                        $comedor=1;
                        $contador_comedor++;
                        }
                $contador_total++;
                // Actualiza estado
                $actualiza_estado=db_query("UPDATE alumnos SET comedor=$comedor WHERE id_alumno=$id_inscripcion");
        }
// echo($contador_total." ".$contador_comedor);
?>
