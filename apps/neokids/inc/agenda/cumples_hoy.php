<?php 
$inicio_semana=$fecha_time_stamp-((date("N",($fecha_time_stamp))-1)*86400);
$fecha_inicio_semana=$inicio_semana;
$fin_semana=$inicio_semana+(7*86400);

$filtro_cumples="";
for($c_semana=0;$c_semana<7;$c_semana++){
	$filtro_cumples.="(day(usuarios.fecha_nacimiento)=".date("j",$inicio_semana)." AND month(usuarios.fecha_nacimiento)=".date("n",$inicio_semana).") OR ";
	$inicio_semana=$inicio_semana+86400;	
}

if ($filtro_cumples!="") {
	$filtro_cumples="(".substr($filtro_cumples,0,strlen($filtro_cumples)-4).")";
	}

$texto_cumples="";
$consulta_cumples="SELECT *,centro_aulas.nombre AS nombre_aula FROM usuarios, alumnos, centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=centro_aulas.id_aula AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' AND $filtro_cumples AND alumnos.id_centro=".$centro_actual." ORDER BY month(usuarios.fecha_nacimiento), day(usuarios.fecha_nacimiento)";

$busca_cumples=db_query($consulta_cumples);

if (db_num_rows($busca_cumples)!=0) {


        $encabezado_cumples="<a class='btn btn-primary btn-xs' href='".$_SERVER["PHP_SELF"]."?dia=".($fecha_inicio_semana-(86400*7))."'><i class='fa fa-caret-left' aria-hidden='true'></i> Semana anterior</a> <a class='btn btn-primary btn-xs pull-right' href='".$_SERVER["PHP_SELF"]."?dia=".($fin_semana+86400)."'>Semana siguiente <i class='fa fa-caret-right' aria-hidden='true'></i></a><b>Cumpleaños del ".date("d/m/Y",$fecha_inicio_semana)." al ".date("d/m/Y",$fin_semana)."</b><br />";
		$texto_cumples="";
		$cumple_actual="";
        while($listado_cumples=db_fetch_array($busca_cumples)) {
        
        	if ($cumple_actual!=date("d/m",strtotime($listado_cumples["fecha_nacimiento"]))) {
        		$cumple_actual=date("d/m",strtotime($listado_cumples["fecha_nacimiento"]));
        		
        		$dia_semana=date("w",strtotime(date("Y").date("-m-d",strtotime($listado_cumples["fecha_nacimiento"]))));
        		if ($dia_semana==0) {
        			$dia_semana=7;
        		}
        		
        		$texto_cumples.='
				<tr class="warning">
					<td colspan="4"><b>'.$dia_sem_largo[$dia_semana].' '.date("j",strtotime($listado_cumples["fecha_nacimiento"])).' de '.$nombre_mes[date("n",strtotime($listado_cumples["fecha_nacimiento"]))].'</b></td>		
				</tr>        		
        		<tr class="active">
					<td><b>Nombre</b></td>
					<td><b>Cumple</b></td>	
					<td><b>Grupo</b></td>
					<td><b>&nbsp;</b></td>				
				</tr>
				';
        	}
        
            $nacimiento=explode( '-', $listado_cumples["fecha_nacimiento"]);
            $edad_cumple=date("Y",$fecha_time_stamp)-$nacimiento[0];
            $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y",$fecha_time_stamp));
            $hoy=strtotime(date("Y/m/d",$fecha_time_stamp));
            if (($cumple-$hoy)>0) {
                                $edad_cumple=$edad_cumple-1;
                                }
           		if ($hoy<$cumple) {
           			$edad_cumple=$edad_cumple+1;
           			} 
           		$coletilla_edad="";                
                if ($edad_cumple>1) {
                        $coletilla_edad="s";
                        }                                   
                        
                $lista_grupos_alumno="";

                $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($listado_cumples["id_alumno"])) or die(db_error());

                $total_grupos=db_num_rows($busca_alumnos_grupo);
                $contador_grupos=0;
                while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                        $contador_grupos++;
                        
                        $separador_grupos="<br />";
                                                
                        $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).$separador_grupos;
                        }

				// busca si hay registo
				$busca_registro_cumple=db_query_params('SELECT * FROM alumnos_cumples_confirmados WHERE id_alumno=? AND fecha_cumple=?;', 'ss', array($listado_cumples["id_alumno"], (string)(date("Y-m-d", $fecha_time_stamp))));

				if (db_num_rows($busca_registro_cumple)==0) {
					$intro_registro_cumple=db_query_params('INSERT INTO alumnos_cumples_confirmados (id_alumno,fecha_cumple) VALUES (?,?);', 'ss', array((string)($listado_cumples["id_alumno"]), (string)(date("Y-m-d", $fecha_time_stamp))));		
					$id_registro_cumple=db_insert_id();
					$estado_registro_cumple=0;								
				} else {
					$datos_registro_cumple=db_fetch_array($busca_registro_cumple);
					$id_registro_cumple=$datos_registro_cumple["id_cumple"];
					$estado_registro_cumple=$datos_registro_cumple["confirmado"];
				}
				
				if ($estado_registro_cumple==0) {
                	$estado_actual_cumple="";
                } else {
                	$estado_actual_cumple=" checked";
                }				

                $texto_cumples.="<tr><td>".$listado_cumples["Nombre"]." ".$listado_cumples["Apellido1"]." ".$listado_cumples["Apellido2"]."</td><td>".$edad_cumple." año$coletilla_edad</td><td>".$lista_grupos_alumno."</td><td><input data-toggle='toggle' id='comfirmacion-cumple-".$id_registro_cumple."' value='".$id_registro_cumple."' data-size='mini' data-onstyle='success' data-offstyle='warning' data-on='Celebrado' data-off='Pendiente' type='checkbox'".$estado_actual_cumple."></td></tr>";
                }

        }

if ($texto_cumples!="") {
?>
<table width=100% border=0 cellpadding=2 cellspacing="0" bgcolor="#efefef">
	<tr>
		<td>
			<font size=2><?=$encabezado_cumples;?>
			<table class="table table-condensed">
			<?=$texto_cumples;?>
			</table></font>
		</td>
	</tr>
</table>
<?php 
}        

?>
