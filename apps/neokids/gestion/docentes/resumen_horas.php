<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];
$mes_sel=$_GET["mes"];
$ano_sel=$_GET["ano"];

$nombre_trabajador=$datos_aula["Nombre"]." ".$datos_aula["Apellido1"]." ".$datos_aula["Apellido2"];
$nif_trabajador=$datos_aula["DNI"];
$nss_trabajador=$datos_aula["nss"];

$duracion_clases=0.75;

$periodo_liquidacion=$nombre_mes[$mes_sel]." ".$ano_sel;

$numero_dias_mes=date("t",strtotime($ano_sel."-".$mes_sel."-1"));

$fecha_time_stamp=strtotime($ano_sel."-".$mes_sel."-15");

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        } else {
                        $id_curso_actual=0;
                        }
                }

// Busca datos del aula
$consulta_datos_educadora=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_educadora);
?>
<div align="center"><center>
<form name="lita_horas" id="lista_horas">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Resumen de horas realizadas por <i><?=$datos_aula["Apellido1"];?> <?=$datos_aula["Apellido2"];?>, <?=$datos_aula["Nombre"];?></i> durante el mes de <?=$nombre_mes[$mes_sel];?> de <?=$ano_sel;?><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
  
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td align="center"><font size=2><b>Fecha</b></font></td>
        <td align="center"><font size=2><b>Hora entrada mañana</b></font></td>
        <td align="center"><font size=2><b>Hora salida mañana</b></font></td>
        <td align="center"><font size=2><b>Hora entrada tarde</b></font></td>
        <td align="center"><font size=2><b>Hora salida tarde</b></font></td>        
        <td align="center"><font size=2><b>Horas realizadas</b></font></td>
        <td align="center"><font size=2><b>Horas previstas</b></font></td>        
        <td align="center"><font size=2><b>Nº de clases realizadas</b></font></td>
        <td align="center"><font size=2><b>Nº de clases previstas</b></font></td>
        </tr>    
<?php 
$total_diferencia=0;
$total_clases=0;
$total_clases_realizadas=0;
$info_sustituciones="";

for ($dia_mes=1;$dia_mes<=$numero_dias_mes;$dia_mes++) {

	$color_fondo_fila="#fff";
	if (date("Ymd",strtotime($ano_sel."-".$mes_sel."-".$dia_mes))>date("Ymd")) {
		$color_fondo_fila="#ff9191";
	}
	

	$busca_asistencia=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND fecha=? ORDER BY fecha,turno', 'ss', array($id_aula, (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes)))));
	
	$hora_entrada_man="";
	$hora_entrada_tar="";
	$hora_salida_man="";
	$hora_salida_tar="";
	$horas_diferencia=0;
	
	$c_dia=date("w",strtotime($ano_sel."-".$mes_sel."-".$dia_mes));
	if ($c_dia==0) {
		$c_dia=7;
		}	
	
	
	
	while($datos_asistencia=db_fetch_array($busca_asistencia)){
		
                if ((strlen($datos_asistencia["hora_salida"])!=0) and (strlen($datos_asistencia["hora_entrada"])!=0)) {
                        $diferencia_entrada=intval(strtotime($datos_asistencia["hora_salida"]))-intval(strtotime($datos_asistencia["hora_entrada"]));
                        } else {
                        $diferencia_entrada=0;
                        }
                
                $horas_diferencia=($diferencia_entrada/60)/60;
                $total_diferencia=$total_diferencia+$horas_diferencia;
                if ($horas_diferencia<round((($diferencia_entrada/60)/60),0)) {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0)-1;
                        } else {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0);
                        }

                $minutos_diferencia=$horas_diferencia-$horas_diferencia1;
                $minutos_diferencia=round(($minutos_diferencia*60),0);
                
                $total_horas=intval($total_horas)+intval($horas_diferencia1);
                $total_minutos=intval($total_minutos)+intval($minutos_diferencia);
                
                $horas_diferencia1="0".$horas_diferencia1;
                $minutos_diferencia="0".$minutos_diferencia;
                
                $horas_diferencia1=substr($horas_diferencia1,strlen($horas_diferencia1)-2,2);
                $minutos_diferencia=substr($minutos_diferencia,strlen($minutos_diferencia)-2,2);
                
                $hora_entrada_texto=date("H:i:s",strtotime($datos_asistencia["hora_entrada"]));
                
                if ($hora_entrada_texto=="01:00:00") {
                	// $hora_entrada_texto="No ficha entrada";
                	$hora_salida_texto="";
                }
                
                $hora_salida_texto=date("H:i:s",strtotime($datos_asistencia["hora_salida"]));
                
                if ($hora_salida_texto=="01:00:00") {
                	// $hora_salida_texto="No ficha salida";
                	$hora_salida_texto="";
                }
                
                if ($diferencia_entrada!=0) {
                        $diferencia_horas=$horas_diferencia1.":".$minutos_diferencia;
                        } else {
                        $diferencia_horas="00:00";
                        }
                 
                if (date("G",strtotime($datos_asistencia["hora_entrada"]))<15) {
                	$turno=0;
                } else {
                	$turno=1;
                } 
                
                $hora_entrada_man="";
                $hora_salida_man="";
                $hora_entrada_tar="";
                $hora_salida_tar="";
                    
                if ($turno==0) {
	 	                $hora_entrada_man=$hora_entrada_texto;
	 	                $hora_salida_man=$hora_salida_texto;
	 	                
	 	                if ($hora_entrada_man!="") {
		 	                
		 	                $hora_entrada_man="<input type='text' value='".$hora_entrada_man."' size=8 name='hora_entrada-".$datos_asistencia["asistencia_docentes"]."' id='hora_entrada-".$datos_asistencia["id_asistencia"]."'>";		 	                		                
		 	                $hora_salida_man="<input type='text' value='".$hora_salida_man."' size=8 name='hora_salida-".$datos_asistencia["asistencia_docentes"]."' id='hora_salida-".$datos_asistencia["id_asistencia"]."'>";
		 	                
	 	                }	 	                
	 	                
                        $nombre_turno="Mañana";
                        $filtro_turnos=" AND aulas_grupos.hora_inicio<='15:00:00'";
                        } else {
                        $hora_entrada_tar=$hora_entrada_texto;
                        $hora_salida_tar=$hora_salida_texto;
                        
	 	                if ($hora_entrada_tar!="") {
		 	                
		 	                $hora_entrada_tar="<input type='text' value='".$hora_entrada_tar."' size=8 name='hora_entrada-".$datos_asistencia["asistencia_docentes"]."' id='hora_entrada-".$datos_asistencia["id_asistencia"]."'>";
		 	                $hora_salida_tar="<input type='text' value='".$hora_salida_tar."' size=8 name='hora_salida-".$datos_asistencia["asistencia_docentes"]."' id='hora_salida-".$datos_asistencia["id_asistencia"]."'>";
		 	                
	 	                }	 	                                      
                        
                        $nombre_turno="Tarde";
                        $filtro_turnos=" AND aulas_grupos.hora_inicio>'15:00:00'";
                        }  
                        
				// Busca Horas previstas
				$busca_grupos=db_query("SELECT * FROM aulas_grupos, aulas_grupos_monitores WHERE aulas_grupos.id_grupo=aulas_grupos_monitores.id_grupo AND aulas_grupos_monitores.id_usuario=$id_aula AND aulas_grupos.id_curso=$id_curso_actual AND aulas_grupos.dia=".$c_dia.$filtro_turnos);
	
				$total_grupos=db_num_rows($busca_grupos);
	
				$alerta_sustituciones="";
	
				// Comprueba sustituciones positivas
				$busca_sustituciones_positivas=db_query("SELECT * FROM aulas_grupos, personal_asistencia_sustituciones WHERE aulas_grupos.id_grupo=personal_asistencia_sustituciones.id_grupo AND personal_asistencia_sustituciones.sustituido_por=$id_aula AND aulas_grupos.id_curso=$id_curso_actual AND ((personal_asistencia_sustituciones.desde<='".$datos_asistencia["fecha"]."' AND personal_asistencia_sustituciones.hasta>='".$datos_asistencia["fecha"]."') OR (personal_asistencia_sustituciones.desde='".$datos_asistencia["fecha"]."')) AND aulas_grupos.dia=".$c_dia.$filtro_turnos) or die(db_error());
				
				$sustituciones_positivas=db_num_rows($busca_sustituciones_positivas);
				
				if ($sustituciones_positivas>0) {
					$alerta_sustituciones="<font color='#ff0000'> *</font>";
					while($lista_sp=db_fetch_array($busca_sustituciones_positivas)) {
					
						$busca_sustituciones=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($lista_sp["id_usuario"]));
						$sustitucion=db_fetch_array($busca_sustituciones);
					
						$info_sustituciones.="- El día ".date("d/m/Y",strtotime($datos_asistencia["fecha"]))." sustituyó a ".$sustitucion["Nombre"]." ".$sustitucion["Apellido1"]." ".$sustitucion["Apellido2"]." en el grupo de ".date("H:i",strtotime($lista_sp["hora_inicio"]))." a ".date("H:i",strtotime($lista_sp["hora_fin"]))."<br />";					
					
					}
				}				
				
				// Comprueba sustituciones negativas
				$busca_sustituciones_negativas=db_query("SELECT * FROM aulas_grupos, personal_asistencia_sustituciones WHERE aulas_grupos.id_grupo=personal_asistencia_sustituciones.id_grupo AND personal_asistencia_sustituciones.id_usuario=$id_aula AND aulas_grupos.id_curso=$id_curso_actual AND ((personal_asistencia_sustituciones.desde<='".$datos_asistencia["fecha"]."' AND personal_asistencia_sustituciones.hasta>='".$datos_asistencia["fecha"]."') OR (personal_asistencia_sustituciones.desde='".$datos_asistencia["fecha"]."')) AND aulas_grupos.dia=".$c_dia.$filtro_turnos) or die(db_error());
				
				$sustituciones_negativas=db_num_rows($busca_sustituciones_negativas);
				
				if ($sustituciones_negativas>0) {
					$alerta_sustituciones="<font color='#ff0000'> *</font>";
					while($lista_sp=db_fetch_array($busca_sustituciones_negativas)) {
					
						$busca_sustituciones=db_query("SELECT * FROM usuarios WHERE id_usuario=".$lista_sp["sustituido_por"]);
						$sustitucion=db_fetch_array($busca_sustituciones);
					
						$info_sustituciones.="- El día ".date("d/m/Y",strtotime($datos_asistencia["fecha"]))." fue sustituida/o por ".$sustitucion["Nombre"]." ".$sustitucion["Apellido1"]." ".$sustitucion["Apellido2"]." en el grupo de ".date("H:i",strtotime($lista_sp["hora_inicio"]))." a ".date("H:i",strtotime($lista_sp["hora_fin"]))."<br />";					
					
					}
				}
	
				$total_grupos=$total_grupos+$sustituciones_positivas-$sustituciones_negativas;
	
				$total_clases+=$total_grupos;                        
                                                            	
	
		$color_horas="#000";
		
		if ((($total_grupos*$duracion_clases)>$horas_diferencia) and (($total_grupos*$duracion_clases)!=0)) {
			$color_horas="#ff0000";
		}
		if ((($total_grupos*$duracion_clases)<$horas_diferencia) and (($total_grupos*$duracion_clases)!=0)) {
			$color_horas="#0000ff";
		}	
		if ((($total_grupos*$duracion_clases)==0) and ($horas_diferencia!=0)) {
			$color_horas="#4ea00b";
		}						
	
		$total_clases_realizadas_hoy=floor($horas_diferencia/0.75);
		if ($total_clases_realizadas_hoy>$total_grupos) {
		
			$total_clases_realizadas_hoy=$total_grupos;
		
		}
		$total_clases_realizadas+=$total_clases_realizadas_hoy;				
		
	    ?>
        <tr bgcolor="<?=$color_fondo_fila?>">
        <td align="center"><font size=2><?=date("d/m/Y",strtotime($ano_sel."-".$mes_sel."-".$dia_mes));?></font></td>
        <td align="center"><font size=2><?=$hora_entrada_man;?></font></td>
        <td align="center"><font size=2><?=$hora_salida_man;?></font></td>
        <td align="center"><font size=2><?=$hora_entrada_tar;?></font></td>       
        <td align="center"><font size=2><?=$hora_salida_tar;?></font></td>
        <td align="right"><font size=2 color="<?=$color_horas?>"><?=number_format($horas_diferencia,2,",",".");?></font></td>
        <td align="right"><font size=2><?=number_format($total_grupos*$duracion_clases,2,",",".");?></font></td>
        <td align="right"><font size=2><?=$total_clases_realizadas_hoy;?></font></td> 
        <td align="right"><font size=2><?=$total_grupos;?><?=$alerta_sustituciones?></font></td>        
        </tr>
        <?php  
	} 
	if (db_num_rows($busca_asistencia)==0) {
	
		// Busca Horas previstas
		$busca_grupos=db_query("SELECT * FROM aulas_grupos, aulas_grupos_monitores WHERE aulas_grupos.id_grupo=aulas_grupos_monitores.id_grupo AND aulas_grupos_monitores.id_usuario=$id_aula AND aulas_grupos.id_curso=$id_curso_actual AND aulas_grupos.dia=".$c_dia);
	
		$total_grupos=db_num_rows($busca_grupos);
	
	    ?>
        <tr bgcolor="<?=$color_fondo_fila?>">
        <td align="center"><font size=2><?=date("d/m/Y",strtotime($ano_sel."-".$mes_sel."-".$dia_mes));?></font></td>
        <td align="center"><font size=2>&nbsp;</font></td>
        <td align="center"><font size=2>&nbsp;</font></td>
        <td align="center"><font size=2>&nbsp;</font></td>       
        <td align="center"><font size=2>&nbsp;</font></td>
        <td align="right"><font size=2><?=number_format(0,2,",",".");?></font></td>
        <td align="right"><font size=2><?=number_format($total_grupos*$duracion_clases,2,",",".");?></font></td>
        <td align="right"><font size=2>0</font></td> 
        <td align="right"><font size=2><?=$total_grupos;?></font></td>        
        </tr>
        <?php  	
	}       
	
}
        
        // Totales

        $total_minutos=$total_minutos/60;
        if ($total_minutos<round($total_minutos,0)) {
                        $total_minutos1=round($total_minutos,0)-1;
                        } else {
                        $total_minutos1=round($total_minutos,0);
                        }

        $minutos_restantes=round((($total_minutos-$total_minutos1)*60),0);
        
        $total_horas=$total_horas+$total_minutos1;
        
        $total_horas="0".$total_horas;
        $minutos_restantes="0".$minutos_restantes;

        $total_horas=substr($total_horas,strlen($total_horas)-2,2);
        $minutos_restantes=substr($minutos_restantes,strlen($minutos_restantes)-2,2);

		$color_horas="#000";
		
		if ((($total_clases*$duracion_clases)>$total_diferencia) and (($total_clases*$duracion_clases)!=0)) {
			$color_horas="#ff0000";
		}
		if ((($total_clases*$duracion_clases)<$total_diferencia) and (($total_clases*$duracion_clases)!=0)) {
			$color_horas="#0000ff";
		}
		
		if ((($total_clases*$duracion_clases)==0) and ($total_diferencia!=0)) {
			$color_horas="#4ea00b";
		}		

        ?>
        <tr>
        <td align="center" colspan=3><font size=2>&nbsp;</font></td>
        <td align="right" colspan=2><font size=2>Total horas mes:</font></td>
        <td align="right"><font size=2 color="<?=$color_horas?>"><?=number_format($total_diferencia,2,",",".");?></font></td>        
        <td align="right"><font size=2><?=number_format($total_clases*$duracion_clases,2,",",".");?></font></td>
		<td align="right"><font size=2><?=$total_clases_realizadas?></font></td> 
		<td align="right"><font size=2><?=$total_clases?></font></td>        
        </tr>
        </table>
        <?php          
        if ($info_sustituciones) {
        ?>
        <b><font color="#ff0000">Información sobre sustituciones</font></b>
        <p align=left><small><?=$info_sustituciones?></small></p>
        <?php         
        }
        ?>
        </td>
</tr>
</table>
<br>
<input type="button" value="Recalcular" onclick="history.go(0);"> <input type="button" onclick="window.location.href='resumen_horas_exportar.php?id_aula=<?=$id_aula?>&ano=<?=$ano_sel?>&mes=<?=$mes_sel?>'" value="Crear Registro Diario de Jornada"> <input type="button" onclick="window.location.href='ver_calendario.php?id_docente=<?=$id_aula?>&dia=<?=$ano_sel?>/<?=$mes_sel?>/1'" value="Ver como calendario"> <input type="button" onclick="window.location.href='seleccion_fecha_horas.php'" value="Volver">
<br>
</center></div>
</div>
</form>
<script>
	
    function validateHhMm(inputField) {
        var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(inputField);
        return isValid;
    }	
	
	$( document ).ready(function() {
	
			$("#lista_horas input[type=text]").on("change",function(e) {		  
				
				if ((validateHhMm($(this).val()))||($(this).val()=='')) {
							
					var href = "cambia_dato_horas.php?nombre_campo=" + $(this).attr("id") + "&valor_campo=" + $(this).val();
		
					var respuesta = $.ajax ({url: href}); 
					respuesta.always(function() {
						
						var textoRespuesta = respuesta.responseText;
						
						if( textoRespuesta.substr(0, 2) != "ok" ) {							
							alert(textoRespuesta);						
						}
						
					}); 
				
				} else {

					alert("No es un formato de hora correcto.");
					
				}
							
	
			}); 
	
	
	});
	
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>

