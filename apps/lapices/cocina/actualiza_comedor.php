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
$busca_alumnos_comedor_a=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_comedor_a)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_comedor_a)."' ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());

$contador_comedor=0;
$contador_total=0;

while($listado_alumnos_comedor_a=db_fetch_array($busca_alumnos_comedor_a)){
        
                // Comprueba si asiste hoy al comedor
                $id_inscripcion=$listado_alumnos_comedor_a["id_alumno"];
                $id_aula=$listado_alumnos_comedor_a["aula"];
                
                $comedor_contratado_aula=false;
                
                $busca_inscripcion_comedor=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=3 AND servicios_contratados.fecha_inicio<='".date("Y-m-d",$fecha_comedor_a)."' AND servicios_contratados.fecha_fin>='".date("Y-m-d",$fecha_comedor_a)."'") or die(db_error());

                if (db_num_rows($busca_inscripcion_comedor)==0) {
                
                        /*
                        // Comprueba si es un servicio asociado al aula
                        $id_inscripcion_comedor_aula=$listado_alumnos_comedor_a["id_alumno"];
                        $busca_inscripcion_comedor_aula=mysql_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.subcat=3 AND servicios_contratados_aula.fecha_inicio<='".date("Y-m-d",$fecha_comedor_a)."' AND servicios_contratados_aula.fecha_fin>='".date("Y-m-d",$fecha_comedor_a)."'") or die(mysql_error());

                        while($datos_inscripcion_comedor_aula=mysql_fetch_array($busca_inscripcion_comedor_aula)) {
                                $busca_si_activado_comedor_aula=mysql_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion_comedor_aula["id_servicio_contratado"]." AND id_usuario=$id_inscripcion_comedor_aula") or die(mysql_error());
                                if (mysql_num_rows($busca_si_activado_comedor_aula)==0) {
                                        $comedor_contratado_aula=false;
                                        } else {
                                        $comedor_contratado_aula=true;
                                        }
                                }
                        if ($comedor_contratado_aula==true) {
                                $comedor=1;
                                $contador_comedor++;
                                } else {
                                $comedor=2;
                                }
                        */
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

// Sant Pau

// Busca alumnos asisten hoy
$busca_alumnos_comedor_a=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_comedor_a)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_comedor_a)."' ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());

$contador_comedor=0;
$contador_total=0;

while($listado_alumnos_comedor_a=db_fetch_array($busca_alumnos_comedor_a)){
        
                // Comprueba si asiste hoy al comedor
                $id_inscripcion=$listado_alumnos_comedor_a["id_alumno"];

                $busca_inscripcion_comedor=db_query("SELECT * FROM escuelamunicipal.servicios_contratados, escuelamunicipal.tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=3 AND servicios_contratados.fecha_inicio<='".date("Y-m-d",$fecha_comedor_a)."' AND servicios_contratados.fecha_fin>='".date("Y-m-d",$fecha_comedor_a)."'") or die(db_error());
                if (db_num_rows($busca_inscripcion_comedor)==0) {
                        $comedor=2;
                        } else {
                        $comedor=1;
                        $contador_comedor++;
                        }
                $contador_total++;
                // Actualiza estado
                $actualiza_estado=db_query("UPDATE escuelamunicipal.alumnos SET comedor=$comedor WHERE id_alumno=$id_inscripcion");
        }

?>
