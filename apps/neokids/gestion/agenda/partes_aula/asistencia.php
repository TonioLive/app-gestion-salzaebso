<?php 
$media_jornada_doble_fichaje=true;

$colores_niveles=array("#ffffff","#9ed09c","#9cbdd0","#d0af9c");

// Nombres cursos
$busca_lista_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
$nombres_cursos=array();
while($lista_cursos=db_fetch_array($busca_lista_cursos)) {
        $nombres_cursos[$lista_cursos["id_curso"]]=$lista_cursos["nombre"];
}

$listado_padres="";

?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#cdcdcd">
                                <table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                                <?php 
                                $pagina_act=$contador_capas;
                                for ($contador_pagi=0;$contador_pagi<count($array_paginas);$contador_pagi++) {

                                if (substr($nombres_partes[$contador_pagi],0,5)!="link:") {
                                
                                        if ($pagina_act==$contador_pagi) {
                                ?>
                                <td bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><b> <?=$array_paginas[$contador_pagi]?></b></font></td>
                                <?php 
                                        } else {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='javascript:mostrar(<?=$contador_pagi?>)'><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                <?php 
                                        }
                                        } else {
                                        ?>
                                        <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='<?=substr($nombres_partes[$contador_pagi],5,strlen($nombres_partes[$contador_pagi])-5);?>?id_grupo=<?=$id_grupo?>' target="_new"><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                        <?php 
                                        }
                                }
                                ?>
                                </tr></table>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Control de asistencia del grupo <?=$nombre_grupo;?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>">
<div align="center"><center>
<input type="button" onclick="history.go(0)" value="Actualizar para editar" style="font-size: 10px">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 

$busca_alumnos=db_query_params('SELECT *, gestionguarderias.usuarios.id_usuario AS id_usuario_inscripcion, gestionguarderias.alumnos.id_centro AS id_centro_alumno FROM gestionguarderias.usuarios,gestionguarderias.alumnos, aulas_alumnos_lapices_grupos WHERE gestionguarderias.usuarios.id_usuario=gestionguarderias.alumnos.id_usuario AND aulas_alumnos_lapices_grupos.id_alumno=gestionguarderias.alumnos.id_alumno AND aulas_alumnos_lapices_grupos.id_grupo=? AND gestionguarderias.alumnos.fecha_matricula<=? AND gestionguarderias.alumnos.fecha_baja>=? ORDER BY aulas_alumnos_lapices_grupos.id_subgrupo, gestionguarderias.alumnos.nivel_actual,gestionguarderias.usuarios.Apellido1, gestionguarderias.usuarios.Apellido2, gestionguarderias.usuarios.Nombre
', 'sss', array($id_grupo, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

$num_alumnos_aula=db_num_rows($busca_alumnos);

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=10><font size=2 face="<?= $fuente_general; ?>">Para ver la ficha de un alumno, haz clic sobre su nombre o sobre el botón ( <img src="editar.gif" align="absmiddle">)</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Evaluado</b></font></td>        
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type=button onclick="imprimir_seleccion()" value="Imprimir seleccionados"><br /><input type=button onclick="imprimir_portadas()" value="Imprimir portadas"><br /><input type=button onclick="check_todas();" value="Selecconar/deseleccionar todos"><br />
        <input type="checkbox" id="imprimir_logo" name="imprimir_logo"> Imprimir logotipo en los informes        
        </b></font></td>
        <td class="text-center"><small><small>Nivel<br />Máx.<br />Sup.</small></small></td>
        <td class="text-center"><small><small>Nivel<br />Máx.<br />Tra.</small></small></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nivel Actual</font></td>
		<td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nivel<br />Próximo curso</b></font></td>		
        </tr>
        <?php 
        $fila_par_impar=0;
        $nivel_actual=-1;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
                // Nivel proximo curso
                $nivel_proximo_curso_tmp=($listado_alumnos["nivel_actual"]*3)-2;
                
                $nivel_proximo_curso=$listado_alumnos["nivel_proximo"]; 

				$nivel_proximo1_curso=$listado_alumnos["nivel_proximo1"]; 
				
                if (($nivel_proximo_curso==0) and ($listado_alumnos["nivel_actual"]!=0)) {
                
                        $nivel_proximo_curso=$nivel_proximo_curso_tmp;
                
                }         
        
        if ($subgrupo_actual!=$listado_alumnos["id_subgrupo"]) {
                $nivel_actual=-1;
                $subgrupo_actual=$listado_alumnos["id_subgrupo"];
                
                $busca_monitores=db_query("SELECT * FROM aulas_grupos_monitores LEFT JOIN usuarios ON aulas_grupos_monitores.id_usuario=usuarios.id_usuario WHERE id_grupo=$id_grupo AND id_subgrupo=$subgrupo_actual") or die(db_error());
                $monitor=array();
                
                while($lista_monitores=db_fetch_array($busca_monitores)) {
                        if ($lista_monitores["id_usuario"]!=0) {
                                        $monitor[]=$lista_monitores["Nombre"]." ".$lista_monitores["Apellido1"]." ".$lista_monitores["Apellido2"];;
                                        }
                                }
                                
                        $lista_monitores=implode(",",$monitor);
                        
                        unset($monitor);
                
                ?>
                <tr>
                <td colspan=10 bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Grupo <?=$subgrupo_actual+1?>. Monitor/a: <?=$lista_monitores?></b></td>
        </tr>
                <?php 
        }        
        
        if ($nivel_actual!=$listado_alumnos["nivel_actual"]) {
                $nivel_actual=$listado_alumnos["nivel_actual"];
                ?>
                <tr>
                <td colspan=10 bgcolor="<?=$colores_niveles[$nivel_actual]?>"><font size=2 face="<?= $fuente_general; ?>"><b>Nivel <?=$nivel_actual?></b></td>
        </tr>
                <?php 
                }
        
        $id_aula=$listado_alumnos["aula"];
        
        $esta_en_la_escuela=false;

        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }

        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d",$fecha_time_stamp)) {
                $color_fuente_linea="#acacac";
                } else {
                $color_fuente_linea="#000000";
                }
                
        $color_fondo_fila="#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila="#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;
        $colores_asistencia[$listado_alumnos["id_usuario"]]="#ec8b81";
        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#ff8e8e"; 
        $link_javascript="registro_entrada"; 
        
        // Comprueba si el alumno ha sido evaluado en el periodo/trimestre actual
        $busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND trimestre=$trimestre AND (estado_t1+estado_t2+estado_t3)<>0;");
        
        $evaluado="<span class='text-danger'>No</span>";
        if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
                        $datos_evaluacion=db_fetch_array($busca_estado_evaluacion_guardada);
                        $trimestres_evaluados="";
                        if ($datos_evaluacion["estado_t1"]==1) {
                                $trimestres_evaluados.="Tri. 1";
                                }
                        if ($datos_evaluacion["estado_t2"]==1) {
                                $trimestres_evaluados.=", Tri. 2";
                                }
                        if ($datos_evaluacion["estado_t3"]==1) {
                                $trimestres_evaluados.=", Tri. 3";
                                }
                        if (substr($trimestres_evaluados,0,2)==", ") {
                                $trimestres_evaluados=substr($trimestres_evaluados,2,strlen($trimestres_evaluados)-2);
                        }                                
                $evaluado="<span class='text-success'>$trimestres_evaluados</span>";
                }

        // Busca evaluaciones anteriores
        $busca_alumnos_usuario=db_query_params('SELECT *, agenda_programacion_evaluaciones_estado_alumnos.id_curso AS curso_evaluacion FROM gestionguarderias.alumnos, agenda_programacion_evaluaciones_estado_alumnos WHERE gestionguarderias.alumnos.id_alumno=agenda_programacion_evaluaciones_estado_alumnos.id_alumno AND gestionguarderias.alumnos.id_usuario=? AND gestionguarderias.alumnos.id_alumno<>? ORDER BY gestionguarderias.alumnos.fecha_matricula', 'ss', array($listado_alumnos["id_usuario"], $listado_alumnos["id_alumno"])) or die(db_error());
        $enlace_otras_evaluaciones="";
        while($otras_evaluaciones=db_fetch_array($busca_alumnos_usuario)) {
                $enlace_otras_evaluaciones.="<br /><small><small><a href='javascript:ver_eval_lapices(".$otras_evaluaciones["id_alumno"].")' title='Ver evaluación del curso ".$nombres_cursos[$otras_evaluaciones["curso_evaluacion"]]."'>Ver evaluación del curso ".$nombres_cursos[$otras_evaluaciones["curso_evaluacion"]]."</a></small></small>";
        }
        $evaluado.=$enlace_otras_evaluaciones;
        ?>
        <tr valign="top" bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
        <td><?php 
                $estado_boton_su="";
                if ($nivel_actual==0) {
                        $estado_boton_su="disabled ";
                        }
                ?>
                <a <?=$estado_boton_su?>class="btn btn-primary btn-xs" href="subir_nivel.php?externo=1&id_alumno=<?=$listado_alumnos["id_alumno"];?>&id_grupo=<?=$id_grupo?>&dia=<?=$fecha_time_stamp?>&nivel=<?=$nivel_actual?>"><i class="fa fa-sort-asc"></i></a>
                <?php 
                $estado_boton_ba="";
                if ($nivel_actual==4) {
                        $estado_boton_ba="disabled ";
                        }
                ?>
                <a <?=$estado_boton_ba?>class="btn btn-primary btn-xs" href="bajar_nivel.php?externo=1&id_alumno=<?=$listado_alumnos["id_alumno"];?>&id_grupo=<?=$id_grupo?>&dia=<?=$fecha_time_stamp?>&nivel=<?=$nivel_actual?>"><i class="fa fa-sort-desc"></i></a> <font size=2 face="<?= $fuente_general; ?>"><a href="javascript:editar(<?=$listado_alumnos["id_alumno"];?>)"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td align="center"><?=$evaluado;?></td>
        <?php 
        if (($no_inscripcion==false) and ($no_cuotas==false)) {
        if ($fecha_baja<date("Y-m-d",$fecha_time_stamp)) {
        
        
                // Busca si ya está registrado por la mañana
                $asistio=false;
                $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos_guarderias WHERE id_alumno=? AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y/m/d", $fecha_time_stamp)))) or die(db_error());
                if (db_num_rows($busca_registro_entrada)!=0) {
                                $asistio=true;
                        }
        
        ?>
                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <?php  if ($asistio==true) { ?><a href="javascript:ver_informe_lapices(<?=$listado_alumnos["id_alumno"];?>)" title="Evaluar alumno"><img src="pruebas.gif" border=0></a> <a href="javascript:imprimir_informe_lapices(<?=$listado_alumnos["id_alumno"];?>)" title="Imprimir informe de evaluación"><img src="imprimir.gif" border=0></a><?php  } ?>  <input type="checkbox" value="L<?=$listado_alumnos["id_alumno"];?>" name="imprimir_inf"></font></td>
                </tr>
        <?php 
                } else {
        ?>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php                

                // Busca si ya está registrado
                $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos_guarderias WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND turno=$turno AND id_servicio=$id_aula AND fecha='".date("Y/m/d",$fecha_time_stamp)."'") or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                ?>
                <input<?=$estado_botones;?> type="button" name="entrada<?=$listado_alumnos["id_usuario"]?>" id="entrada<?=$listado_alumnos["id_usuario"]?>" value="Registrar entrada" onclick="<?=$link_javascript?>(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,1)"<?=$color_boton;?>></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" name="salida_a_<?=$listado_alumnos["id_usuario"]?>" id="salida_a_<?=$listado_alumnos["id_usuario"]?>" value="Registrar salida" disabled onclick="registro_salida_por_id_alu(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,1)"></td>
                <?php 
                } else {
                $colores_asistencia[$listado_alumnos["id_usuario"]]="#ffffff";
                        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#cee1eb"; 
                $datos_registro=db_fetch_array($busca_registro_entrada);
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,1);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                </td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                <?php 
                if ($datos_registro["hora_salida"]=="") {
                ?>
                <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>,1)">
                <?php 
                } else {
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,1);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                <?php 
                }
                ?>
                </font></td>
                <?php 
                }
                
// Comprobamos mensajes no leidos

if (($nivel_acceso==3) or ($nivel_acceso==4)) {
        // Coordinadora
        $filtro_coordinadora="";
        } else {
        // Educadora
        $filtro_coordinadora=" AND agenda_notas_padres.destino<>1";
        }

$coletilla_mensajes="";

    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND tipo_nota<>2 AND estado=0".$filtro_coordinadora." ORDER BY fecha DESC"); 
    if (db_num_rows($busca_no_leidos)!=0) {
                $coletilla_mensajes="_act";
            } else {
            // Busca notas no leidas
            $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres,agenda_notas_padres_respuestas WHERE agenda_notas_padres.id_nota_padre=agenda_notas_padres_respuestas.id_nota AND agenda_notas_padres.id_alumno=".$listado_alumnos["id_usuario"]." AND agenda_notas_padres_respuestas.leida=0 AND agenda_notas_padres_respuestas.direccion=0".$filtro_coordinadora." ORDER BY fecha DESC") or die(db_error());
            if (db_num_rows($busca_no_leidos)!=0) {
                        $coletilla_mensajes="_act";
                        }
            }
                
                
                
                ?></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="javascript:ver_informe_lapices(<?=$listado_alumnos["id_alumno"];?>)" title="Evaluar alumno"><img src="pruebas.gif" border=0></a> <a href="javascript:imprimir_informe_lapices(<?=$listado_alumnos["id_alumno"];?>)" title="Imprimir informe de evaluación"><img src="imprimir.gif" border=0></a> <a href="/gestion/mensajes/mensajeria.php?id_alumno=<?=$listado_alumnos["id_usuario"];?>" title="Mensajes" target="_blank"><img src="/imagen/mensajes<?=$coletilla_mensajes?>.gif" border=0></a> <input type="checkbox" value="L<?=$listado_alumnos["id_alumno"];?>" name="imprimir_inf"></font></td>
                <td class="text-center"><span class='badge badge-<?=$listado_alumnos["nivel_maximo_superado"];?>'><?=$nombres_subniveles[$listado_alumnos["nivel_maximo_superado"]];?></span></td>
                <td class="text-center"><span class='badge badge-<?=$listado_alumnos["nivel_maximo_trabajado"];?>'><?=$nombres_subniveles[$listado_alumnos["nivel_maximo_trabajado"]];?></td>
                <td align="center"><select id="nivel_e_<?=$listado_alumnos["id_alumno"];?>" name="nivel_e_<?=$listado_alumnos["id_alumno"];?>" onchange="cambia_nivel(<?=$listado_alumnos["id_alumno"];?>,1);" style="background-color: <?=$colores_subniveles[$nivel_proximo_curso]?>; color: #fff">
                        <option value="0"<?php  if ($nivel_proximo_curso==0) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[0]?>; color: #fff">0</option>
                        <option value="1"<?php  if ($nivel_proximo_curso==1) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[1]?>; color: #fff">1.1</option>
                        <option value="2"<?php  if ($nivel_proximo_curso==2) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[2]?>; color: #fff">1.2</option>
                        <option value="4"<?php  if ($nivel_proximo_curso==4) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[4]?>; color: #fff">2.1</option>
                        <option value="5"<?php  if ($nivel_proximo_curso==5) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[5]?>; color: #fff">2.2</option>
                        <option value="7"<?php  if ($nivel_proximo_curso==7) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[7]?>; color: #fff">3.1</option>
                        <option value="8"<?php  if ($nivel_proximo_curso==8) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[8]?>; color: #fff">3.2</option>
                </select></td>                
                <td align="center"><select id="nivel_e1_<?=$listado_alumnos["id_alumno"];?>" name="nivel_e1_<?=$listado_alumnos["id_alumno"];?>" onchange="cambia_nivel1(<?=$listado_alumnos["id_alumno"];?>,1);" style="background-color: <?=$colores_subniveles[$nivel_proximo1_curso]?>; color: #fff">
                        <option value="0"<?php  if ($nivel_proximo1_curso==0) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[0]?>; color: #fff">0</option>
                        <option value="1"<?php  if ($nivel_proximo1_curso==1) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[1]?>; color: #fff">1.1</option>
                        <option value="2"<?php  if ($nivel_proximo1_curso==2) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[2]?>; color: #fff">1.2</option>
                        <option value="4"<?php  if ($nivel_proximo1_curso==4) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[4]?>; color: #fff">2.1</option>
                        <option value="5"<?php  if ($nivel_proximo1_curso==5) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[5]?>; color: #fff">2.2</option>
                        <option value="7"<?php  if ($nivel_proximo1_curso==7) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[7]?>; color: #fff">3.1</option>
                        <option value="8"<?php  if ($nivel_proximo1_curso==8) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[8]?>; color: #fff">3.2</option>
                </select></td> 				
                </tr>
                <?php 
            }

            } else {
        $mensaje11="";
        if ($no_inscripcion==true) {
                $mensaje11=$mensaje11."Inscripción en el curso";
                }
        if ($no_cuotas==true) {
                if ($mensaje11!="") {
                        $mensaje11=$mensaje11." y ";
                        }
                $mensaje11=$mensaje11."Servicio de educación";
                }
        $mensaje11="Falta asigar al niño los servicios de: ".$mensaje11.".";
        ?>
        <td colspan=2 align="center"><font color="red" size=1><?=$mensaje11?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$listado_alumnos["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a></td>        
        </tr>
        <?php 
        }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
}

// Fin alumnos guarderias
 
// Alumnos Propios 
        
$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion, alumnos.id_centro AS id_centro_alumno FROM usuarios,alumnos, aulas_alumnos_grupos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? ORDER BY aulas_alumnos_grupos.id_subgrupo, alumnos.nivel_actual,usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre
', 'sss', array($id_grupo, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

$num_alumnos_aula=db_num_rows($busca_alumnos);

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=10><font size=2 face="<?= $fuente_general; ?>">Para ver la ficha de un alumno, haz clic sobre su nombre o sobre el botón ( <img src="editar.gif" align="absmiddle">)</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Evaluado</b></font></td>          
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>        
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type=button onclick="imprimir_seleccion()" value="Imprimir seleccionados"><br /><input type=button onclick="enviar_seleccion()" value="Enviar evaluación seleccionados"><br /><input type=button onclick="check_todas();" value="Selecconar/deseleccionar todos"><br />
        <input type="checkbox" id="imprimir_logo" name="imprimir_logo"> Imprimir logotipo en los informes </b></font></td>
        <td class="text-center"><small><small>Nivel<br />Máx.<br />Sup.</small></small></td>
        <td class="text-center"><small><small>Nivel<br />Máx.<br />Tra.</small></small></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nivel Actual</font></td>
		<td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nivel<br />Próximo curso</b></font></td>		
        </tr>
        <?php 
        $fila_par_impar=0;
        $nivel_actual=-1;
        $subgrupo_actual=-1;
        while($listado_alumnos=db_fetch_array($busca_alumnos)){       
        
                // Nivel proximo curso
                $nivel_proximo_curso_tmp=($listado_alumnos["nivel_actual"]*3)-2;
                
                $nivel_proximo_curso=$listado_alumnos["nivel_proximo"]; 
				
				$nivel_proximo1_curso=$listado_alumnos["nivel_proximo1"]; 
				
				
                if (($nivel_proximo_curso==0) and ($listado_alumnos["nivel_actual"]!=0)) {
                
                        $nivel_proximo_curso=$nivel_proximo_curso_tmp;
                        
                        if ($listado_alumnos["nivel_proximo1"]!=0) {
	                        
	                     // $nivel_proximo1_curso=$nivel_proximo_curso_tmp;
	                        
                        }
						
                
                }         
        
        if ($subgrupo_actual!=$listado_alumnos["id_subgrupo"]) {
                $nivel_actual=-1;
                $subgrupo_actual=$listado_alumnos["id_subgrupo"];
                
                $busca_monitores=db_query("SELECT * FROM aulas_grupos_monitores LEFT JOIN usuarios ON aulas_grupos_monitores.id_usuario=usuarios.id_usuario WHERE id_grupo=$id_grupo AND id_subgrupo=$subgrupo_actual") or die(db_error());
                $monitor=array();
                
                while($lista_monitores=db_fetch_array($busca_monitores)) {
                        if ($lista_monitores["id_usuario"]!=0) {
                                        $monitor[]=$lista_monitores["Nombre"]." ".$lista_monitores["Apellido1"]." ".$lista_monitores["Apellido2"];;
                                        }
                                }
                                
                        $lista_monitores=implode(",",$monitor);
                        
                        unset($monitor);
                
                ?>
                <tr>
                <td colspan=10 bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Grupo <?=$subgrupo_actual+1?>. Monitor/a: <?=$lista_monitores?></b></td>
        </tr>
                <?php 
        } 
        
        if ($nivel_actual!=$listado_alumnos["nivel_actual"]) {
                $nivel_actual=$listado_alumnos["nivel_actual"];
                ?>
                <tr>
                <td colspan=10 bgcolor="<?=$colores_niveles[$nivel_actual]?>"><font size=2 face="<?= $fuente_general; ?>"><b>Nivel <?=$nivel_actual?></b></td>
        </tr>
                <?php 
                }
                
        $id_aula=$listado_alumnos["aula"];
        
        $esta_en_la_escuela=false;

        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }

        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d",$fecha_time_stamp)) {
                $color_fuente_linea="#acacac";
                } else {
                $color_fuente_linea="#000000";
                }
                
        $color_fondo_fila="#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila="#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;
        $colores_asistencia[$listado_alumnos["id_usuario"]]="#ec8b81";
        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#ff8e8e"; 
        $link_javascript="registro_entrada";  
        
        // Comprueba si el alumno ha sido evaluado en el periodo/trimestre actual
        $busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND periodo=$periodo AND (estado_p1+estado_p2+estado_p3+estado_p4)<>0;");       
        
        $evaluado="<span class='text-danger'>No</span>";
        $uid="";
        if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {       
        
                        $datos_evaluacion=db_fetch_array($busca_estado_evaluacion_guardada);
                        
                        // Genera UID
                        
                        $digitos_id_alumno=strlen($listado_alumnos["id_alumno"]);
                        $digitos_id_evaluacion=strlen($datos_evaluacion["id_estado"]);
                        
                        $ale1=rand(10000, 99999);
                        $ale2=rand(10000, 99999);
                        
                        $parte1=substr($ale1,0,(5-$digitos_id_evaluacion)).$datos_evaluacion["id_estado"];
                        $parte2=substr($ale2,0,(5-$digitos_id_alumno)).$listado_alumnos["id_alumno"];
                        
                        $parte3="00".$datos_evaluacion["id_curso"];
                        $parte3=substr($parte3,strlen($parte3)-2,2);  
                        
                        $uid=$parte1.$parte2.$parte3.$digitos_id_alumno.$digitos_id_evaluacion;                   
                        
                        $trimestres_evaluados="";
                        if ($datos_evaluacion["estado_p1"]==1) {
                                $trimestres_evaluados.="Per. 1";
                                }
                        if ($datos_evaluacion["estado_p2"]==1) {
                                $trimestres_evaluados.=", Per. 2";
                                }
                        if ($datos_evaluacion["estado_p3"]==1) {
                                $trimestres_evaluados.=", Per. 3";
                                }
                        if ($datos_evaluacion["estado_p4"]==1) {
                                $trimestres_evaluados.=", Per. 4";
                                }
                        if (substr($trimestres_evaluados,0,2)==", ") {
                                $trimestres_evaluados=substr($trimestres_evaluados,2,strlen($trimestres_evaluados)-2);
                        }
                $evaluado="<span class='text-success'>$trimestres_evaluados</span><br /><small>Cód. acceso padres: $uid</small>";
                }       
        // Busca evaluaciones anteriores
        $busca_alumnos_usuario=db_query_params('SELECT *, agenda_programacion_evaluaciones_estado_alumnos.id_curso AS curso_evaluacion FROM alumnos, agenda_programacion_evaluaciones_estado_alumnos WHERE alumnos.id_alumno=agenda_programacion_evaluaciones_estado_alumnos.id_alumno AND alumnos.id_usuario=? AND alumnos.id_alumno<>? ORDER BY alumnos.fecha_matricula', 'ss', array($listado_alumnos["id_usuario"], $listado_alumnos["id_alumno"])) or die(db_error());
        $enlace_otras_evaluaciones="";
        while($otras_evaluaciones=db_fetch_array($busca_alumnos_usuario)) {
                $enlace_otras_evaluaciones.="<br /><small><small><a href='javascript:ver_eval(".$otras_evaluaciones["id_alumno"].")' title='Ver evaluación del curso ".$nombres_cursos[$otras_evaluaciones["curso_evaluacion"]]."'>Ver evaluación del curso ".$nombres_cursos[$otras_evaluaciones["curso_evaluacion"]]."</a></small></small>";
        }
        $evaluado.=$enlace_otras_evaluaciones;
        ?>
        <tr valign="top" bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
        <td><?php 
                $estado_boton_su="";
                if ($nivel_actual==0) {
                        $estado_boton_su="disabled ";
                        }
                ?>
                <a <?=$estado_boton_su?>class="btn btn-primary btn-xs" href="subir_nivel.php?externo=0&id_alumno=<?=$listado_alumnos["id_alumno"];?>&id_grupo=<?=$id_grupo?>&dia=<?=$fecha_time_stamp?>&nivel=<?=$nivel_actual?>"><i class="fa fa-sort-asc"></i></a>
                <?php 
                $estado_boton_ba="";
                if ($nivel_actual==4) {
                        $estado_boton_ba="disabled ";
                        }
                ?>
                <a <?=$estado_boton_ba?>class="btn btn-primary btn-xs" href="bajar_nivel.php?externo=0&id_alumno=<?=$listado_alumnos["id_alumno"];?>&id_grupo=<?=$id_grupo?>&dia=<?=$fecha_time_stamp?>&nivel=<?=$nivel_actual?>"><i class="fa fa-sort-desc"></i></a> <font size=2 face="<?= $fuente_general; ?>"><a href="javascript:editar(<?=$listado_alumnos["id_alumno"];?>)"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td align="center"><?=$evaluado;?></td>        
        <?php 
        if ($fecha_baja<date("Y-m-d",$fecha_time_stamp)) {
        
        
                // Busca si ya está registrado por la mañana
                $asistio=false;
                $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y/m/d", $fecha_time_stamp)))) or die(db_error());
                
                if (db_num_rows($busca_registro_entrada)!=0) {
                                $asistio=true;
                        }
        
        ?>
                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <?php  if ($asistio==true) { ?><a href="javascript:ver_informe(<?=$listado_alumnos["id_alumno"];?>)" title="Evaluar alumno"><img src="pruebas.gif" border=0></a> <a href="javascript:imprimir_informe(<?=$listado_alumnos["id_alumno"];?>)" title="Imprimir informe de evaluación"><img src="imprimir.gif" border=0></a><?php  } ?>  <input type="checkbox" value="<?=$listado_alumnos["id_alumno"];?>" name="imprimir_inf"></font></td>
                </tr>
        <?php 
                } else {
        ?>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php                
                
                // Busca si ya está registrado
                $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND turno=$turno AND id_servicio=$id_aula AND fecha='".date("Y/m/d",$fecha_time_stamp)."'") or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                ?>
                <input<?=$estado_botones;?> type="button" name="entrada<?=$listado_alumnos["id_usuario"]?>" id="entrada<?=$listado_alumnos["id_usuario"]?>" value="Registrar entrada" onclick="<?=$link_javascript?>(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,0)"<?=$color_boton;?>></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" name="salida_a_<?=$listado_alumnos["id_usuario"]?>" id="salida_a_<?=$listado_alumnos["id_usuario"]?>" value="Registrar salida" disabled onclick="registro_salida_por_id_alu(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,0)"></td>
                <?php 
                } else {
                $colores_asistencia[$listado_alumnos["id_usuario"]]="#ffffff";
                        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#cee1eb"; 
                $datos_registro=db_fetch_array($busca_registro_entrada);
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                </td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                <?php 
                if ($datos_registro["hora_salida"]=="") {
                ?>
                <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>,0)">
                <?php 
                } else {
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                <?php 
                }
                ?>
                </font></td>
                <?php 
                }
                
                
// Comprobamos mensajes no leidos

if (($nivel_acceso==3) or ($nivel_acceso==4)) {
        // Coordinadora
        $filtro_coordinadora="";
        } else {
        // Educadora
        $filtro_coordinadora=" AND agenda_notas_padres.destino<>1";
        }

$coletilla_mensajes="";

    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND tipo_nota<>2 AND estado=0".$filtro_coordinadora." ORDER BY fecha DESC"); 
    if (db_num_rows($busca_no_leidos)!=0) {
                $coletilla_mensajes="_act";
            } else {
            // Busca notas no leidas
            $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres,agenda_notas_padres_respuestas WHERE agenda_notas_padres.id_nota_padre=agenda_notas_padres_respuestas.id_nota AND agenda_notas_padres.id_alumno=".$listado_alumnos["id_usuario"]." AND agenda_notas_padres_respuestas.leida=0 AND agenda_notas_padres_respuestas.direccion=0".$filtro_coordinadora." ORDER BY fecha DESC") or die(db_error());
            if (db_num_rows($busca_no_leidos)!=0) {
                        $coletilla_mensajes="_act";
                        }
            }
                
                /*
                $color_letra_select="#fff";
                if ($nivel_proximo_curso==0) {
                        $color_letra_select="#000";
                }
                */
                ?></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="javascript:ver_informe(<?=$listado_alumnos["id_alumno"];?>)" title="Evaluar alumno"><img src="pruebas.gif" border=0></a> <a href="javascript:imprimir_informe(<?=$listado_alumnos["id_alumno"];?>)" title="Imprimir informe de evaluación"><img src="imprimir.gif" border=0></a> <a href="/gestion/mensajes/mensajeria.php?id_alumno=<?=$listado_alumnos["id_usuario"];?>" title="Mensajes" target="_blank"><img src="/imagen/mensajes<?=$coletilla_mensajes?>.gif" border=0></a> <input type="checkbox" value="<?=$listado_alumnos["id_alumno"];?>" name="imprimir_inf"></font></td>
                <td class="text-center"><span class='badge badge-<?=$listado_alumnos["nivel_maximo_superado"];?>'><?=$nombres_subniveles[$listado_alumnos["nivel_maximo_superado"]];?></span></td>
                <td class="text-center"><span class='badge badge-<?=$listado_alumnos["nivel_maximo_trabajado"];?>'><?=$nombres_subniveles[$listado_alumnos["nivel_maximo_trabajado"]];?></td>
                <td align="center"><select id="nivel<?=$listado_alumnos["id_alumno"];?>" name="nivel<?=$listado_alumnos["id_alumno"];?>" onchange="cambia_nivel(<?=$listado_alumnos["id_alumno"];?>,0);" style="background-color: <?=$colores_subniveles[$nivel_proximo_curso]?>; color: #fff">
                        <option value="0"<?php  if ($nivel_proximo_curso==0) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[0]?>; color: #fff">0</option>
                        <option value="1"<?php  if ($nivel_proximo_curso==1) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[1]?>; color: #fff">1.1</option>
                        <option value="2"<?php  if ($nivel_proximo_curso==2) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[2]?>; color: #fff">1.2</option>
                        <option value="4"<?php  if ($nivel_proximo_curso==4) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[4]?>; color: #fff">2.1</option>
                        <option value="5"<?php  if ($nivel_proximo_curso==5) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[5]?>; color: #fff">2.2</option>
                        <option value="7"<?php  if ($nivel_proximo_curso==7) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[7]?>; color: #fff">3.1</option>
                        <option value="8"<?php  if ($nivel_proximo_curso==8) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[8]?>; color: #fff">3.2</option>
                </select></td>
                <td align="center"><select id="nivel1<?=$listado_alumnos["id_alumno"];?>" name="nivel1<?=$listado_alumnos["id_alumno"];?>" onchange="cambia_nivel1(<?=$listado_alumnos["id_alumno"];?>,0);" style="background-color: <?=$colores_subniveles[$nivel_proximo1_curso]?>; color: #fff">
                        <option value="0"<?php  if ($nivel_proximo1_curso==0) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[0]?>; color: #fff">0</option>
                        <option value="1"<?php  if ($nivel_proximo1_curso==1) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[1]?>; color: #fff">1.1</option>
                        <option value="2"<?php  if ($nivel_proximo1_curso==2) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[2]?>; color: #fff">1.2</option>
                        <option value="4"<?php  if ($nivel_proximo1_curso==4) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[4]?>; color: #fff">2.1</option>
                        <option value="5"<?php  if ($nivel_proximo1_curso==5) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[5]?>; color: #fff">2.2</option>
                        <option value="7"<?php  if ($nivel_proximo1_curso==7) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[7]?>; color: #fff">3.1</option>
                        <option value="8"<?php  if ($nivel_proximo1_curso==8) { print " selected"; } ?> style="background-color: <?=$colores_subniveles[8]?>; color: #fff">3.2</option>
                </select></td> 								
                </tr>
                <?php 
            }

        }
        ?>
        </table>
        </td>
</tr>
<?php 
}
?>
</table>
</center></div>
</div>
                                </font></td>
                        </tr>
                </table>
<SCRIPT LANGUAGE=javascript>
<!--

<?php 
if ($num_alumnos_aula==1) {
?>
var solo_hay_uno=true;
<?php  } else { ?>
var solo_hay_uno=false;
<?php 
}
?>

function imprimir_seleccion() {
        lista_alumnos="";

        if (solo_hay_uno==false) {
        for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                if (document.<?=$formulario_name?>.imprimir_inf[c_alumnos].checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf[c_alumnos].value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }
                } else {
                if (document.<?=$formulario_name?>.imprimir_inf.checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf.value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }

        if (lista_alumnos!="") {
                
                                var mostrar_logo="";
                
                                        if ( document.getElementById("imprimir_logo").checked ) {                
                                                mostrar_logo="&logo=1";                                
                                        }
                
                lista_alumnos=lista_alumnos.substr(0,(lista_alumnos.length-1))
                // window.location.href="/pdf/facturas.php?facturas="+lista_alumnos;
                ventana_facturas=window.open("/pdf/evaluacion_piscina.php?multiple=1&lista_alumnos="+lista_alumnos+mostrar_logo,"impresion_informes","toolbar=yes,scrollbars=yes,width=640,height=480")
                } else {
                alert("Debes seleccionar algún alumno para imprimir su agenda");
                }
        }
        
function imprimir_portadas() {
        lista_alumnos="";

        if (solo_hay_uno==false) {
        for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                if (document.<?=$formulario_name?>.imprimir_inf[c_alumnos].checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf[c_alumnos].value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }
                } else {
                if (document.<?=$formulario_name?>.imprimir_inf.checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf.value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }

        if (lista_alumnos!="") {                
                
                lista_alumnos=lista_alumnos.substr(0,(lista_alumnos.length-1))
                // window.location.href="/pdf/facturas.php?facturas="+lista_alumnos;
                ventana_facturas=window.open("/pdf/portada_informe.php?lista_alumnos="+lista_alumnos,"impresion_informes","toolbar=yes,scrollbars=yes,width=640,height=480")
                } else {
                alert("Debes seleccionar algún alumno para imprimir su agenda");
                }
        }        
       
function enviar_seleccion() {

        lista_alumnos="";
                contador_alumnos = 0;
                nombre_mensaje = " alumnos";
                
        if (solo_hay_uno==false) {
        for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                if (document.<?=$formulario_name?>.imprimir_inf[c_alumnos].checked) {
                                contador_alumnos++;
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf[c_alumnos].value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }
                } else {
                if (document.<?=$formulario_name?>.imprimir_inf.checked) {
                                contador_alumnos++;                                
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf.value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }

        if (lista_alumnos!="") {                
                if (contador_alumnos==1) {
                        nombre_mensaje = " alumno";
                        }
                if (confirm("Se va a enviar un mensaje a "+contador_alumnos+nombre_mensaje+"\n\n¿Estás seguro/a?")) {
                                        lista_alumnos=lista_alumnos.substr(0,(lista_alumnos.length-1))
                        // window.location.href="/pdf/facturas.php?facturas="+lista_alumnos;
                        ventana_envios=window.open("/mailer/enviar_codigo_evaluacion.php?lista_alumnos="+lista_alumnos,"envio_codigos","toolbar=yes,scrollbars=yes,width=1024,height=768");
                }
                
                
                } else {
                alert("Debes seleccionar algún alumno para enviar el código de la evaluación a sus padres.");
                }
        }       
        
function check_todas() {
        if (solo_hay_uno==false) {
                for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                        document.<?=$formulario_name?>.imprimir_inf[c_alumnos].click();
                        }
                } else {
                        document.<?=$formulario_name?>.imprimir_inf.click();
                }
        }

var pagina_destino="";

function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);

    }
}

function detalles_asistencia(id_alumno) {
        window.open("/gestion/centros/detalles_asistencia.php?fecha=<?=$fecha_time_stamp;?>&id_alumno="+id_alumno,"ventana_detalle1","toolbar=no,width=750,height=550");
        }

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar'+codigo,'toolbar=no,resizable=no,width=600,height=500');
        }

function ver_informe(codigo) {
        window.open('/gestion/grupos_pruebas/evaluar_alumno.php?id_alumno='+codigo,'informe'+codigo,'toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }

function ver_informe_lapices(codigo) {
        window.open('/gestion/grupos_lapices_nuevo/evaluar_alumno.php?id_alumno='+codigo,'informe'+codigo,'toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }

function ver_eval(codigo) {
        window.open('/gestion/agenda/evaluacion_piscina.php?id_alumno='+codigo,'informe'+codigo,'toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }

function ver_eval_lapices(codigo) {
        window.open('/gestion/agenda/evaluacion_piscina.php?lista_alumnos=L'+codigo,'informe'+codigo,'toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }
        

function imprimir_informe(codigo) {
                
                var mostrar_logo="";
                
                if ( document.getElementById("imprimir_logo").checked ) {                
                        mostrar_logo="&logo=1";                                
                }
        
        window.open('/pdf/evaluacion_piscina.php?id_alumno='+codigo+mostrar_logo,'informe','toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }

function imprimir_informe_lapices(codigo) {
                var mostrar_logo="";
                
                if ( document.getElementById("imprimir_logo").checked ) {                
                        mostrar_logo="&logo=1";                                
                }
                
        window.open('/pdf/evaluacion_piscina.php?lista_alumnos=L'+codigo+mostrar_logo,'informe','toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
                }        
        
function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.<?=$formulario_name;?>.cambio_aula"+id+".value");
                window.location.href='/aulas/cambio_aula.php?na='+opcion_seleccionada+'&a='+id;
                }
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }

function editar_registro(id_registro,tipo) {

                if (tipo==1) {
                                pagina_destino="/aulas/editar_registro_guarderias.php";
                            } else {
                                pagina_destino="/aulas/editar_registro.php";
                                }

        window.open(pagina_destino+'?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function editar_registro_c(id_registro) {
        window.open('/aulas/editar_registro_c.php?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function registro_entrada_morosos(id_aula,id_alumno,fecha,id_registro,tipo) {
        if (confirm("El alumno no ha pagado la inscripción. ¿Quieres fichar?")) {
                var Digital=new Date()
                var hours=Digital.getHours()
                var minutes=Digital.getMinutes()
                var seconds=Digital.getSeconds()
                // Método clásico
                // window.open('registro_entrada.php?turno=<?=$turno;?>&id_aula='+id_aula+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro'+id_alumno,'toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                
                if (tipo==1) {
                                pagina_destino="/aulas/registro_entrada_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_entrada_ajax.php";
                                }
                
                pagina_destino="/aulas/registro_entrada_ajax.php";
                sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
                }
        }
function registro_entrada(id_aula,id_alumno,fecha,id_registro,tipo) {
                var Digital=new Date()
                var hours=Digital.getHours()
                var minutes=Digital.getMinutes()
                var seconds=Digital.getSeconds()
                document.getElementById("entrada"+id_alumno).disabled=true;
                                document.getElementById("salida_a_"+id_alumno).disabled=false;
                document.getElementById("entrada"+id_alumno).value=hours+":"+minutes+":"+seconds;               
                
                if (tipo==1) {
                                pagina_destino="/aulas/registro_entrada_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_entrada_ajax.php";
                                }
                
                // Método clásico
                // window.open(pagina_destino+'?turno=<?=$turno;?>&id_aula='+id_aula+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro'+id_alumno,'toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});

        }
function registro_salida_ventana(id_registro,tipo) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida"+id_registro).disabled=true;
        document.getElementById("salida"+id_registro).value=hours+":"+minutes+":"+seconds;
        
        if (tipo==1) {
                pagina_destino="/aulas/registro_salida_guarderias.php";
            } else {
                pagina_destino="/aulas/registro_salida.php";
                }
        
        window.open(pagina_destino+'?id_registro='+id_registro,'registro'+id_registro,'toolbar=no,resizable=no,width=450,height=400');
        }
 

function registro_salida_por_id_alu(id_aula,id_alumno,fecha,id_registro,tipo) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida_a_"+id_alumno).disabled=true;
        document.getElementById("salida_a_"+id_alumno).value=hours+":"+minutes+":"+seconds;
        
                if (tipo==1) {
                                pagina_destino="/aulas/registro_salida_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_salida_ajax.php";
                                }
        
        sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        
        }
        
function registro_salida(id_registro,tipo) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida"+id_registro).disabled=true;
        document.getElementById("salida"+id_registro).value=hours+":"+minutes+":"+seconds;
        
                if (tipo==1) {
                                pagina_destino="/aulas/registro_salida_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_salida_ajax.php";
                                }
        
        sendAndLoad({desc:'id_registro',dato:id_registro});
        // window.open(pagina_destino+'?id_registro='+id_registro,'registro'+id_registro,'toolbar=no,resizable=no,width=450,height=400');
        }
        
function cambia_nivel(id_alumno,externo) {
        
        if (externo==1) {
                nuevo_nivel=document.getElementById("nivel_e_"+id_alumno).value;
                } else {
                nuevo_nivel=document.getElementById("nivel"+id_alumno).value;
                }
        
        
        pagina_destino="/gestion/agenda/cambia_nivel.php";

        sendAndLoad({desc:'id_alumno',dato:id_alumno},{desc:'nuevo_nivel',dato:nuevo_nivel},{desc:'externo',dato:externo});
        // window.open(pagina_destino+'?id_alumno='+id_alumno+'&nuevo_nivel='+nuevo_nivel+'&externo='+externo,'Pruebas','toolbar=no,resizable=no,width=450,height=400');
        }                       
		
function cambia_nivel1(id_alumno,externo) {
        
        if (externo==1) {
                nuevo_nivel=document.getElementById("nivel_e1_"+id_alumno).value;
                } else {
                nuevo_nivel=document.getElementById("nivel1"+id_alumno).value;
                }
        
        
        pagina_destino="/gestion/agenda/cambia_nivel1.php";

        sendAndLoad({desc:'id_alumno',dato:id_alumno},{desc:'nuevo_nivel',dato:nuevo_nivel},{desc:'externo',dato:externo});
        // window.open(pagina_destino+'?id_alumno='+id_alumno+'&nuevo_nivel='+nuevo_nivel+'&externo='+externo,'Pruebas','toolbar=no,resizable=no,width=450,height=400');
        }      		

$(document).ready(function() {

   $("[id^=nivel]").change(function() {
   
	   var colores_subniveles = new Array('<?=implode("','",$colores_subniveles)?>');
	   var nombres_subniveles = new Array('<?=implode("','",$nombres_subniveles)?>');  

	   var current = $(this).val();

	   $(this).css('background-color', colores_subniveles[current] );

    }); 
});

//-->
</SCRIPT>
