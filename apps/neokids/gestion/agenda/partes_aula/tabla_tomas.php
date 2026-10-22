<?php 
$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'ssss', array($centro_actual, $id_aula, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));
?><table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
<form name="tomas">
        <tr>
        <td colspan=5><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        $fila_par_impar=0;
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

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

        $listado_incidencias="";

        $busca_incidencias=db_query("SELECT * FROM agenda_alumnos_seguimiento WHERE area=$tipo_incidencias AND tipo_nota=$tipo_nota_tabla AND dia='".date("Y-m-d",$fecha_time_stamp)."' AND id_alumno=".$listado_alumnos["id_alumno"]." ORDER BY hora");

        while($incidencias=db_fetch_array($busca_incidencias)) {
                $listado_incidencias=$listado_incidencias.date("H:i",strtotime($incidencias["hora"])).". ".$tipos_notas_inci[$incidencias["tipo_nota"]].": ".$incidencias["nota"]."<br>";
                }

        $color_fondo_fila=$colores_asistencia[$listado_alumnos["id_usuario"]]; // "#ffffff";               
        if ($fila_par_impar==1) {
        	$color_fondo_fila=$colores_asistencia_par[$listado_alumnos["id_usuario"]]; // "#cee1eb";
        	$fila_par_impar=-1;
        	} 
        	
        $fila_par_impar++;	
        ?>
        <tr valign="top" bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
        <td width="20%"><font size=2 face="<?= $fuente_general; ?>"><a href="javascript:editar_nota(<?=$id_aula?>,<?=$listado_alumnos["id_alumno"]?>,<?=$fecha_time_stamp?>,<?=$tipo_incidencias?>)"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="left" width="30%"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_incidencias?></td>
        <td align="left" width="40%"><font size=1 face="<?= $fuente_general; ?>"><b>Toma: <select style="font-size=10" id="toma<?=$listado_alumnos["id_alumno"]?>" name="toma<?=$listado_alumnos["id_alumno"]?>">
    <option value="todo">Todo</option>
    <option value="la mitad">Mitad</option>
    <option value="poco">Poco</option>
    <option value="">Nada</option>
    </select> Obs: </b><input type="text" id="nota_toma<?=$listado_alumnos["id_alumno"]?>" name="nota_toma<?=$listado_alumnos["id_alumno"]?>" style="font-size: 10pxl" size=35> <input type="button" onclick="nueva_toma(<?=$listado_alumnos["id_alumno"]?>);" value="Añadir toma" style="font-size: 10px"></font></td>
        <td align="center" width="10%"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><a href='javascript:editar_nota(<?=$id_aula?>,<?=$listado_alumnos["id_alumno"]?>,<?=$fecha_time_stamp?>,<?=
$tipo_incidencias?>)'>Editar</a></font></td>
        <?php 
        }
        ?>
        </form>
        </table>
