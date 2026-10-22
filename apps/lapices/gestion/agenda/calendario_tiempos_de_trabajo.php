                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="background: #ffffff; border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#cdcdcd">
                                <table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                                <?php
require_once __DIR__ . '/../../configuracion/encoding.php';
$pagina_act=$tiempo_de_trabajo;
                                for ($contador_tiempos=1;$contador_tiempos<=$tiempos_de_trabajo;$contador_tiempos++) {
                                        if ($pagina_act==$contador_tiempos) {
                                ?>
                                <td bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><b> Tiempo de trabajo <?=$contador_tiempos?></b></font></td>
                                <?php 
                                        } else {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='javascript:mostrar_capa(<?=$contador_tiempos?>)'>Tiempo de trabajo <?=$contador_tiempos?></a></b></font></td>
                                <?php 
                                        }
                                }
                                ?>
                                </tr></table>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>">
<?php 

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];

$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_time_stamp);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_time_stamp);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }

//Inicio es el día en el que empieza la primera semana del mes + 6 horas (por lo del cambio horario)
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400+21600;

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;

$anio_actual=date("Y",$fecha_time_stamp);
$dias_docencia=0;

$mesnombre=$nombre_mes[$mes];
$dias_docencia_mes=0;
?>
<br>
<table border=0 cellspacing="1" cellpadding="1" width="100%">
<tr valign="top">
<td colspan=2>
<input type="button" value="< Semana anterior" onclick="window.location.href='semana_programacion.php?id_aula=<?=$id_aula?>&fecha_inicio=<?=date("Y-m-d",$fecha_time_stamp-604800);?>&pagina_activa=<?=$tiempo_de_trabajo;?>'">
</td>
<td>
<font color="#000000" size=3 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
</td>
<td colspan=2 align="right">
<input type="button" value="Semana siguiente >" onclick="window.location.href='semana_programacion.php?id_aula=<?=$id_aula?>&fecha_inicio=<?=date("Y-m-d",$fecha_time_stamp+604800);?>&pagina_activa=<?=$tiempo_de_trabajo;?>'">
</td>
</tr>
<?php 
$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{

  $semana_actual=false;
  if (date("W",$fecha_time_stamp)==date("W",$inicio_time_stamp)) {
                $semana_actual=true;
                
        ?>
        <tr valign="top">
        <?php 
                
                }
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {

        $estado_boton_comedor="";

        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query("SELECT * FROM calendario_laboral WHERE id_curso=$id_curso_actual AND id_centro=$centro_actual AND dia='".date("Y-m-d",$inicio_time_stamp)."' LIMIT 0,1") or die(db_error());

        // Estado inicial
        $estado_boton="";
        $fondo_celda_dia="#EFEFEF";
        $mostrar_formulario=true;

        if (db_num_rows($busca_dia_en_calendario)==0) {
                if (($dias==7) or ($dias==6)) {
                        // Sábado o domingo
                        $inserta_domingo=db_query("INSERT INTO calendario_laboral (id_curso,id_centro,dia,estado) VALUES ('$id_curso_actual','$centro_actual','".date("Y-m-d",$inicio_time_stamp)."',1) LIMIT 0,1;");
                        $estado_boton=" disabled";
                        $fondo_celda_dia="#F17327";
                        $mostrar_formulario=false;
                        }
                }

        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        // Día festivo
                        $estado_boton=" disabled";
                        $fondo_celda_dia="#F17327";
                        $mostrar_formulario=false;
                        }
                }

        $dia_dentro_periodo=true;
        if ($estado_boton!=" disabled") {
                // Comprueba periodo lectivo
                if ((strtotime($datos_curso_actual["fecha_inicio"])>intval($inicio_time_stamp)) or (strtotime($datos_curso_actual["fecha_fin"])<intval($inicio_time_stamp-86400))) {
                        // Día fuera del periodo de asistencia
                        $fondo_celda_dia="#FFCE70";
                        $estado_boton=" checked";
                        $dia_dentro_periodo=false;
                        }  else {
                        $dias_docencia++;
                        $dias_docencia_mes++;
                        }
                }
        $ancho_celda="20%";
        if ($dias<6) {
        if (($semana_actual==true) and ($mostrar_formulario==true) and ($dia_dentro_periodo==true)) {

        // Busca programacion

        $busca_programacion=db_query("SELECT * FROM agenda_programacion WHERE fecha='".date("Y-m-d",$inicio_time_stamp)."' AND tiempo_trabajo=$tiempo_de_trabajo AND id_aula=$id_aula ORDER BY orden_actividad");

        if (db_num_rows($busca_programacion)==0) {
                $inserta_prog=db_query("INSERT INTO agenda_programacion (id_aula,fecha,tiempo_trabajo,lugar,orden_actividad) VALUES ('$id_aula','".date("Y-m-d",$inicio_time_stamp)."','$tiempo_de_trabajo','Clase','1');");
                $busca_programacion=db_query("SELECT * FROM agenda_programacion WHERE fecha='".date("Y-m-d",$inicio_time_stamp)."' AND tiempo_trabajo=$tiempo_de_trabajo AND id_aula=$id_aula ORDER BY orden_actividad");
                }
        ?>
        <td align="center" style="background: <?=$fondo_celda_dia;?>" id="celda<?=$inicio_time_stamp;?>" width="<?=$ancho_celda?>">
        <font face="<?=$fuente_general?>" color="#000000" size=2><?php 
        if ($mes==date("n",$inicio_time_stamp))
                {
                print "<b>";    }
        if ($dias==7)
                {
                print "<font color=#800000>";    }
        if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
                {
                print "<font color='blue'>";    }
                print ($dia_sem_largo[$dias]." ".date("j",$inicio_time_stamp));
        if ($mes==date("n",$inicio_time_stamp))
                {
                print "</b>";
                }
                
        $c_act=0;
        while($programacion=db_fetch_array($busca_programacion)) {
        $c_act++;
        $id_prog=$programacion["id_prog"];
        
        if ($programacion["id_actividad"]!=0) {
                $estado_actividad=" disabled";
                $descripcion_actividad_actual=$programacion["actividad"];
                } else {
                $estado_actividad="";
                $descripcion_actividad_actual=app_text_utf8($programacion["actividad"]);
                }
        ?>
         <input type="button" value="Evaluar día" style="font-size: 10px" onclick="evaluar(<?=$inicio_time_stamp?>);"><br>
        <table width=100% border=0 cellpadding=1 cellspacing=1>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="center" colspan=2><font size=1>Tiempo de trabajo: <?=$tiempo_de_trabajo?>. Actividad <?=$programacion["orden_actividad"]?>ª</font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="right" width=20%><font size=1><b>Turno: </b></font></td>
                        <td bgcolor="#ffffff" align="left"><font size=1>
                        <input type="radio" name="turno_<?=$id_prog?>" id="turno_<?=$id_prog?>" value="0"<?php  if ($programacion["turno"]==0) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'turno'},{desc:'valor',dato:'0'});"> Mañana
                        <input type="radio" name="turno_<?=$id_prog?>" id="turno_<?=$id_prog?>" value="1"<?php  if ($programacion["turno"]==1) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'turno'},{desc:'valor',dato:'1'});"> Tarde</td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" width=20%><font size=1><b>Área: </b></font></td>
                        <td bgcolor="#ffffff" align="left"><font size=1>
                        <input type="radio" name="area_<?=$id_prog?>" id="area_<?=$id_prog?>" value="1"<?php  if ($programacion["area"]==1) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:1},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'area'},{desc:'valor',dato:'1'});"> I
                        <input type="radio" name="area_<?=$id_prog?>" id="area_<?=$id_prog?>" value="2"<?php  if ($programacion["area"]==2) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:1},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'area'},{desc:'valor',dato:'2'});"> II
                        <input type="radio" name="area_<?=$id_prog?>" id="area_<?=$id_prog?>" value="3"<?php  if ($programacion["area"]==3) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:1},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'area'},{desc:'valor',dato:'3'});"> III
                        <input type="radio" name="area_<?=$id_prog?>" id="area_<?=$id_prog?>" value="0"<?php  if ($programacion["area"]==0) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:1},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'area'},{desc:'valor',dato:'0'});"> Sin área
                        </font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" width=20%><font size=1><b>Objetivo: </b></font></td>
                        <td bgcolor="#ffffff" align="left"><font size=1>
                        <select name="objetivo_<?=$id_prog?>" id="objetivo_<?=$id_prog?>" style="font-size: 10px" onchange="self.location.href='guarda_dato_programacion.php?recargar=1&texto_largo=0&fila=<?=$id_prog?>&id_aula=<?=$id_aula?>&fecha_inicio=<?=$inicio_time_stamp;?>&pagina_activa=<?=$tiempo_de_trabajo?>&nombre_id=id_prog&tabla=agenda_programacion&campo=objetivo&borrar_actividades=1&valor='+document.getElementById('objetivo_<?=$id_prog?>').value;">
                        <?php 
						
						$mesActual=date("n",$inicio_time_stamp);
                                                $mes_anteriorActual=$mesActual-1;
                                                $ano_anteriorActual=date("Y",$inicio_time_stamp);
                                                $mes_siguienteActual=$mesActual+1;
                                                $ano_siguienteActual=date("Y",$inicio_time_stamp);
                                                if ($mesActual==1) {
                                                        $mes_anteriorActual=12;
                                                        $ano_anteriorActual=$ano_anteriorActual-1;
                                                        }
                                                if ($meActuals==12) {
                                                        $mes_siguienteActual=1;
                                                        $ano_siguienteActual=$ano_siguienteActual+1;
                                                        }
                                                $mes_programacionActual=date("n",$inicio_time_stamp);				

						$consulta_busca_area0="SELECT * FROM agenda_programacion_objetivos WHERE id_area=0 AND edad>=$edad_aula AND edad<=$edad_aula_max AND (mes=$mes_programacionActual OR mes=$mes_siguienteActual OR mes=$mes_anteriorActual OR mes=0) AND editorial=0";
						$consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE id_area=".$programacion["area"]." AND edad>=$edad_aula AND edad<=$edad_aula_max AND (mes=$mes_programacionActual OR mes=$mes_siguienteActual OR mes=$mes_anteriorActual OR mes=0) AND editorial=0 ORDER BY mes DESC,nombre_objetivo";
						
						// Comprueba si la programación debe ser EDEBE
						$nueva_programacion_activa=true;
						if (($inicio_time_stamp<=strtotime("2019-08-31")) and ($inicio_time_stamp>=strtotime("2017-09-01")) and (($edad_aula==1) or ($edad_aula==2)) and ($nueva_programacion_activa==true)) {
							
							$editorialProgramacion=1;
							if ($inicio_time_stamp>=strtotime("2018-09-01")) {
								$editorialProgramacion=2;
									
								// 1 AÑO
								if (($mes_programacion>=9) and ($mes_programacion<=12)) {
									$filtro_meses_programacion="mes=1 OR mes=2 OR mes=3";
									$trimestre=1;
								}
								if (($mes_programacion>=1) and ($mes_programacion<=3)) {
									$filtro_meses_programacion="mes=4 OR mes=5 OR mes=6";
									$trimestre=2;
								}
								if (($mes_programacion>=4) and ($mes_programacion<=6)) {
									$filtro_meses_programacion="mes=7 OR mes=8 OR mes=9";
									$trimestre=3;
								}							
								if ($edad_aula==2) {
									
									// 2 AÑOS
									
									if (($mes_programacion>=9) and ($mes_programacion<=12)) {
										$filtro_meses_programacion="mes=1 OR mes=2";
										$trimestre=1;
									}
									if (($mes_programacion>=1) and ($mes_programacion<=3)) {
										$filtro_meses_programacion="mes=3 OR mes=4";
										$trimestre=2;
									}
									if (($mes_programacion>=4) and ($mes_programacion<=6)) {
										$filtro_meses_programacion="mes=5 OR mes=6";
										$trimestre=3;
									}								
									
								} 								
	
									
							} else {
							
								// 1 AÑO
								if (($mes_programacion>=9) and ($mes_programacion<=12)) {
									$filtro_meses_programacion="mes=1";
									$trimestre=1;
								}
								if (($mes_programacion>=1) and ($mes_programacion<=3)) {
									$filtro_meses_programacion="mes=2 OR mes=3";
									$trimestre=3;
								}
								if (($mes_programacion>=4) and ($mes_programacion<=6)) {
									$filtro_meses_programacion="mes=4 OR mes=5";
									$trimestre=3;
								}							
								if ($edad_aula==2) {
									
									// 2 AÑOS
									
									if (($mes_programacion>=9) and ($mes_programacion<=12)) {
										$filtro_meses_programacion="mes=1 OR mes=2";
										$trimestre=1;
									}
									if (($mes_programacion>=1) and ($mes_programacion<=3)) {
										$filtro_meses_programacion="mes=3 OR mes=4";
										$trimestre=2;
									}
									if (($mes_programacion>=4) and ($mes_programacion<=6)) {
										$filtro_meses_programacion="mes=5 OR mes=6";
										$trimestre=3;
									}								
									
								} 								
								
							}														
							
							// $consulta_busca_area0="SELECT * FROM agenda_programacion_objetivos WHERE id_area=0 AND edad>=$edad_aula AND edad<=$edad_aula_max AND (".$filtro_meses_programacion." OR mes=0) AND editorial=$editorialProgramacion";
							// $consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE id_area=".$programacion["area"]." AND edad>=$edad_aula AND edad<=$edad_aula_max AND (".$filtro_meses_programacion." OR mes=0) AND editorial=$editorialProgramacion ORDER BY mes DESC,nombre_objetivo";

							if ($editorialProgramacion==2) {
								$consulta_busca_area0="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.id_area=0 AND agenda_programacion_objetivos.edad>=$edad_aula AND agenda_programacion_objetivos.edad<=$edad_aula_max AND agenda_programacion_objetivos.editorial=$editorialProgramacion";
								// $consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.id_area=".$programacion["area"]." AND agenda_programacion_objetivos.edad>=$edad_aula AND agenda_programacion_objetivos.edad<=$edad_aula_max AND agenda_programacion_objetivos.editorial=$editorialProgramacion AND agenda_evaluacion_items.trimestre=".$trimestre." ORDER BY agenda_programacion_objetivos.mes DESC,agenda_programacion_objetivos.nombre_objetivo";
								$consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.id_area=".$programacion["area"]." AND agenda_programacion_objetivos.edad>=$edad_aula AND agenda_programacion_objetivos.edad<=$edad_aula_max AND agenda_programacion_objetivos.editorial=$editorialProgramacion ORDER BY agenda_programacion_objetivos.mes DESC,agenda_programacion_objetivos.nombre_objetivo";
							} else {
								$consulta_busca_area0="SELECT * FROM agenda_programacion_objetivos WHERE id_area=0 AND edad>=$edad_aula AND edad<=$edad_aula_max AND (".$filtro_meses_programacion." OR mes=0) AND editorial=$editorialProgramacion";
								$consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE id_area=".$programacion["area"]." AND edad>=$edad_aula AND edad<=$edad_aula_max AND (".$filtro_meses_programacion." OR mes=0) AND editorial=$editorialProgramacion ORDER BY mes DESC,nombre_objetivo";
							}
							
						}						
						
						$busca_area0=db_query($consulta_busca_area0) or die(db_error()."-0 ".$consulta_busca_area0);
                        
                        if (($programacion["area"]!=0) or (db_num_rows($busca_area0)!=0)) {
                        ?>
                        <option value="0"></option>
                        <?php 
                        $busca_objetivos=db_query($consulta_busca_objetivos) or die(db_error()." -1 ".$consulta_busca_objetivos);
                        while($objetivos=db_fetch_array($busca_objetivos)){
                        
						if ($objetivos["editorial"]>=1) {
							$nombre_mes_edad="Unidad ".$objetivos["mes"];
						} else {
							if ($objetivos["edad"]!=0) {
									if ($objetivos["mes"]==0) {
											$nombre_mes_edad="Curso";
											} else {
											$nombre_mes_edad=$nombre_mes_corto[$objetivos["mes"]];
											}
									} else {
									$nombre_mes_edad=$objetivos["mes"]." mes";
									if ($objetivos["mes"]!=1) {
											$nombre_mes_edad.="es";
											}
									}
						}
                        ?>
                        <option value="<?=$objetivos["id_objetivo"]?>"<?php  if ($programacion["objetivo"]==$objetivos["id_objetivo"]) { print " selected"; } ?>><?=$objetivos["nombre_objetivo"];?> (<?=$nombre_mes_edad?> Edad: <?=$objetivos["edad"]?> año<?php  if ($objetivos["edad"]!=1) { ?>s<?php  } ?>)</option>
                        <?php 
                        }
                        
                        // Busca objetivos adicionales
                        $mes_sel_objetivos=date("n",$inicio_time_stamp);
                                                $anyo_sel_objetivos=date("Y",$inicio_time_stamp);
                                                
                        $busca_objetivos_adicionales=db_query("SELECT * FROM agenda_programacion_objetivos_recuperados,agenda_programacion_objetivos WHERE agenda_programacion_objetivos_recuperados.id_objetivo=agenda_programacion_objetivos.id_objetivo AND agenda_programacion_objetivos_recuperados.aula=$id_aula AND agenda_programacion_objetivos_recuperados.mes=$mes_sel_objetivos AND agenda_programacion_objetivos_recuperados.anyo=$anyo_sel_objetivos AND (agenda_programacion_objetivos.mes<>$mes_programacion AND agenda_programacion_objetivos.mes<>$mes_siguiente AND agenda_programacion_objetivos.mes<>$mes_anterior AND agenda_programacion_objetivos.mes<>0)") or die(db_error());
                        
                        
                        while($objetivos_adicionales=db_fetch_array($busca_objetivos_adicionales)){

                        $nombre_mes_edad="Recuperado";

                        ?>
                        <option value="<?=$objetivos_adicionales["id_objetivo"]?>"<?php  if ($programacion["objetivo"]==$objetivos_adicionales["id_objetivo"]) { print " selected"; } ?>><?=$objetivos_adicionales["nombre_objetivo"];?> (<?=$nombre_mes_edad?>)</option>
                        <?php 
                        }                        
                        
                        } else {
                        ?>
                        <option value="0">Selecciona primero el área</option>
                        <?php 
                        }
                        ?>
                        </select><br>
                        <?php 
// Muestra objetivos trabajados
if (strlen($programacion["objetivos"])!=0) {
$objetivos_trabajados=str_replace("[", "", $programacion["objetivos"]);
$objetivos_trabajados=str_replace("]", "", $objetivos_trabajados);

$array_objetivos_trabajados=explode(",", $objetivos_trabajados);

for($c_obj=0;$c_obj<count($array_objetivos_trabajados);$c_obj++) {
// Busca nombre objetivo
$busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_trabajados[$c_obj]);
if (db_num_rows($busca_datos_objetivo)!=0) {
        $datos_objetivo=db_fetch_array($busca_datos_objetivo);
        ?>
        - <?=$datos_objetivo["nombre_objetivo"]?><br>
        <?php 
        }
}  
}                                              
                        ?><input type="button" value="Añadir objetivos adicionales" style="font-size: 10px" onclick="ver_objetivos(<?=$id_prog?>);">
                        </font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" colspan=2><font size=1><b>Actividad: <input type="button" value="Ver actividades" onclick="ver_actividades(<?=$id_prog?>);" style="font-size: 10px"></b></font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="center" colspan=2><font size=1><textarea<?=$estado_actividad?> name="actividad_<?=$id_prog?>" id="actividad_<?=$id_prog?>" cols=45 rows=8 style="font-family: arial; font-size: 10px" onblur="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'actividad'},{desc:'valor',dato:document.getElementById('actividad_<?=$id_prog?>').value});"><?=$descripcion_actividad_actual;?></textarea></font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" width=20%><font size=1><b>Metodología: </b></font></td>
                        <td bgcolor="#ffffff" align="left"><font size=1>
                        <input type="radio" name="metodologia_<?=$id_prog?>" id="metodologia_<?=$id_prog?>" value="1"<?php  if ($programacion["metodologia"]==1) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'metodologia'},{desc:'valor',dato:'1'});"> Gran grupo<br>
                        <input type="radio" name="metodologia_<?=$id_prog?>" id="metodologia_<?=$id_prog?>" value="2"<?php  if ($programacion["metodologia"]==2) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'metodologia'},{desc:'valor',dato:'2'});"> Pequeño grupo<br>
                        <input type="radio" name="metodologia_<?=$id_prog?>" id="metodologia_<?=$id_prog?>" value="3"<?php  if ($programacion["metodologia"]==3) { print " checked"; } ?> onclick="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'metodologia'},{desc:'valor',dato:'3'});"> Individual<br>
                        </font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" width=20%><font size=1><b>Tiempo: </b></font></td>
                        <td bgcolor="#ffffff" align="left"><font size=1>
                        <select name="tiempo_<?=$id_prog?>" id="tiempo_<?=$id_prog?>" style="font-size: 10px" onchange="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'tiempo'},{desc:'valor',dato:document.getElementById('tiempo_<?=$id_prog?>').value});">
                        <option value=""></option>
                        <?php 
                        for ($c_min=5;$c_min<=60;$c_min=$c_min+5) {
                        ?>
                        <option value="<?=$c_min?>"<?php  if ($programacion["tiempo"]==$c_min) { print " selected"; } ?>><?=$c_min?></option>
                        <?php 
                        }
                        ?>
                        </select> minutos
                        </font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" colspan=2><font size=1><b>Materiales: </b></font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="center" colspan=2><font size=1><textarea name="materiales_<?=$id_prog?>" id="materiales_<?=$id_prog?>" cols=45 rows=5 style="font-family: arial; font-size: 10px" onblur="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'materiales'},{desc:'valor',dato:document.getElementById('materiales_<?=$id_prog?>').value});"><?=app_text_utf8($programacion["materiales"]);?></textarea></font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" width=20%><font size=1><b>Lugar: </b></font></td>
                        <td bgcolor="#ffffff" align="left"><font size=1><input type="text" name="lugar_<?=$id_prog?>" id="lugar_<?=$id_prog?>" value="<?=app_text_utf8($programacion["lugar"]);?>" style="font-size: 10px" size=22 onblur="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'lugar'},{desc:'valor',dato:document.getElementById('lugar_<?=$id_prog?>').value});">
                        </font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="left" colspan=2><font size=1><b>Evaluación: </b></font></td>
                </tr>
                <tr valign="top">
                        <td bgcolor="#ffffff" align="center" colspan=2><font size=1><textarea name="evaluacion_<?=$id_prog?>" id="evaluacion_<?=$id_prog?>" cols=45 rows=10 style="font-family: arial; font-size: 10px" onblur="sendAndLoad({desc:'recargar',dato:0},{desc:'texto_largo',dato:1},{desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_prog?>},{desc:'nombre_id',dato:'id_prog'},{desc:'tabla',dato:'agenda_programacion'},{desc:'campo',dato:'evaluacion'},{desc:'valor',dato:document.getElementById('evaluacion_<?=$id_prog?>').value});"><?=app_text_utf8($programacion["evaluacion"]);?></textarea></font></td>
                </tr>
        </table>
        <?php 
        }
        ?>
        <br>
        <input type="button" style="font-size: 10px" value="Nueva actividad" onclick="nueva_actividad(<?=$id_aula?>,'<?=date("Y-m-d",$inicio_time_stamp);?>',<?=$tiempo_de_trabajo?>)"> <?php  if ($c_act>1) { ?><input type="button" style="font-size: 10px" value="Quitar última" onclick="quitar_actividad(<?=$id_aula?>,'<?=date("Y-m-d",$inicio_time_stamp);?>',<?=$tiempo_de_trabajo?>)"><?php  } ?>
        </font>
        </td>
        <?php 
                } else {
                if ($semana_actual==true) {
                        ?>
        <td align="center" style="background: <?=$fondo_celda_dia;?>" id="celda<?=$inicio_time_stamp;?>" width="<?=$ancho_celda?>">
        <font face="<?=$fuente_general?>" color="#000000" size=2><?php 
        if ($mes==date("n",$inicio_time_stamp))
                {
                print "<b>";    }
        if ($dias==7)
                {
                print "<font color=#800000>";    }
        if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
                {
                print "<font color='blue'>";    }
                print ($dia_sem_largo[$dias]." ".date("j",$inicio_time_stamp));
        if ($mes==date("n",$inicio_time_stamp))
                {
                print "</b>";
                }
        ?>
        <br>
        </font>
        </td>
                        <?php 
                        }
                }
        }
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
if ($semana_actual==true) {
?>
</tr>
<?php 
        }
}
?>
<tr>
<td>
</td>
</tr>
</table>
<font size=1><b>Dias de docencia durante el mes:</b> <?=$dias_docencia_mes?><br><br>
</td></table>
