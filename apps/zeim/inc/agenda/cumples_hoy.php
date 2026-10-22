<?php 
$texto_cumples="";
$consulta_cumples="SELECT *,centro_aulas.nombre AS nombre_aula FROM usuarios, alumnos, centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' AND month(usuarios.fecha_nacimiento)=".date("n",$fecha_time_stamp)." AND day(usuarios.fecha_nacimiento)=".date("j",$fecha_time_stamp)." AND alumnos.id_centro=".$centro_actual." ORDER BY alumnos.aula DESC";

$busca_cumples=db_query($consulta_cumples);

if (db_num_rows($busca_cumples)!=0) {

if (date("Y-m-d",$fecha_time_stamp)==date("Y-m-d")) {
	$texto_cumples="<b>Cumpleaños hoy:</b><br />";
	} else {
	$texto_cumples="<b>Cumpleaños el día ".date("d/m/Y",$fecha_time_stamp).":</b><br />";
	}

	while($listado_cumples=db_fetch_array($busca_cumples)) {
	
            $nacimiento=explode( '-', $listado_cumples["fecha_nacimiento"]);
            $edad_cumple=date("Y",$fecha_time_stamp)-$nacimiento[0];
            $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y",$fecha_time_stamp));
            $hoy=strtotime(date("Y/m/d",$fecha_time_stamp));
            if (($cumple-$hoy)>0) {
                                $edad_cumple=$edad_cumple-1;
                                }
		
		if ($edad_cumple>1) {
			$coletilla_edad="s";
			}
		
		$texto_cumples.=$listado_cumples["Nombre"]." ".$listado_cumples["Apellido1"]." ".$listado_cumples["Apellido2"].", Cumple ".$edad_cumple." año$coletilla_edad. ".$listado_cumples["nombre_aula"]."<br />";
		}

	}

if ($texto_cumples!="") {
?>
<table width=100% border=0 cellpadding=2 cellspacing="0" bgcolor="#efefef"><tr><td><font size=2><?=$texto_cumples;?></font></td></tr></table>
<?php 
}	

?>