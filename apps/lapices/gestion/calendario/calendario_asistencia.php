<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<div align="center">
<?php 
$id_alumno=$_GET["id_alumno"];
if (strlen($id_alumno)==0) {
        $id_alumno=$_POST["id_alumno"];
        }
        
$busca_datos_alumno=db_query("SELECT usuarios.*,alumnos.*,alumnos.id_centro AS centro_asistencia FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=$id_alumno LIMIT 0,1") or die(db_error());

if (db_num_rows($busca_datos_alumno)!=0) {

$datos_alumno=db_fetch_array($busca_datos_alumno);
$id_usuario=$datos_alumno["id_usuario"];

$nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];
$comedor_alumno=$datos_alumno["comedor"];

// Busca el curso actual
$curso_seleccionado=$datos_alumno["id_curso"];
if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."' LIMIT 0,1");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado LIMIT 0,1");
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }
        
if ($id_curso_actual!=0) {

$inicio=$datos_curso_actual["fecha_inicio"];


if (strlen($inicio)!=0) {
        $anio_fecha=date("Y",strtotime($inicio));
        } else {
        $anio_fecha=date("Y");
        }

if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
} 

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

//Inicio es el día en el que empieza la primera semana del mes + 6 horas (por lo del cambio horario, mire usted)
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400+21600;

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;
?>
<font size=3><b>Calendario de asistencia<br>Alumno/a <?=$nombre_completo_alumno?> durante el curso <?=$datos_curso_actual["nombre"]?></b><br></font>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <input type="hidden" name="id_alumno" value="<?=$id_alumno;?>">
                        <font size=2><b>Cambiar al curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
                        <font size=2>ATENCION: Los días no lectivos son los que aparecen marcados en el calendario de color naranja.</font><br><br>
                        <input type="button" value="Ver calendario piscina" onclick="window.location.href='calendario_asistencia_piscina.php?id_alumno=<?=$id_alumno?>'">
<table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000">
<tr><td>
<table>
<tr>
<td colspan=2 align="center" bgcolor="#efefef"><font size=1><b>Leyenda de colores</b></font></td>
</tr>
<tr>
<td bgcolor="#F17327"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día festivo</font></td>
</tr>
<tr>
<td bgcolor="#F9F5BA"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día lectivo al que niño seleccionado no asistirá.</font></td>
</tr>
<tr>
<td bgcolor="#D1FFD3"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día lectivo al que el niño seleccionado debe asistir.</font></td>
</tr>
<tr>
<td bgcolor="#FFCE70"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día no festivo fuera del periodo de inscripción del niño.</font></td>
</tr>
<tr>
<td bgcolor="#A6C6EA"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día asistido.</font></td>
</tr>
<tr>
<td bgcolor="#FFF200"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Falta de asistencia.</font></td>
</tr>
<tr>
<td bgcolor="#f797f6"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Asiste a docencia pero no se queda a comer cuando había reservado.</font></td>
</tr>
<tr>
<td bgcolor="#E51740"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día marcado como no lectivo en la sesión actual.</font></td>
</tr>
<tr>
<td bgcolor="#9DE517"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día marcado como lectivo en la sesión actual.</font></td>
</tr>
</table></td></tr></table>
<?php 
$anio_actual=date("Y",$fecha_time_stamp);
$dias_trabajados=0;
$dias_asistidos_reales=0;
$dias_comedor=0;
$dias_comedor_reales=0;
$dias_faltados=0;
$faltas_comedor=0;
for ($mes_s=1; $mes_s<=12; $mes_s=$mes_s+1) {
$mesnombre=$nombre_mes[$mes];
$dias_trabajados_mes=0;
$dias_asistidos_reales_mes=0;
$dias_comedor_mes=0;
$dias_comedor_reales_mes=0;
$dias_faltados_mes=0;
$faltas_comedor_mes=0;
?>
<font color="#000000" size=1 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
<table border=0 cellspacing="1" cellpadding="1" width="100%">
<form name="calendario" method="POST" action="">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=1>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
</b>
</font>
</td>
<?php  
}
?>
</tr>
<?php  
$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{
?>
<tr>
<?php  
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {

    if ($mes==date("n",$inicio_time_stamp)) {

        $estado_boton_comedor="";
        
        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query("SELECT * FROM calendario_laboral WHERE id_curso=$id_curso_actual AND id_centro=$centro_actual AND dia='".date("Y-m-d",$inicio_time_stamp)."' LIMIT 0,1");
        
        // Estado inicial
        $estado_boton="";
        $fondo_celda_dia="#EFEFEF";
        
        if (db_num_rows($busca_dia_en_calendario)==0) {
                if (($dias==7) or ($dias==6)) {
                        // Sábado o domingo
                        $inserta_domingo=db_query("INSERT INTO calendario_laboral (id_curso,id_centro,dia,estado) VALUES ('$id_curso_actual','$centro_actual','".date("Y-m-d",$inicio_time_stamp)."',1) LIMIT 0,1;");
                        $estado_boton=" disabled";
                        $fondo_celda_dia="#F17327";
                        }
                }
                        
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        // Día festivo
                        $estado_boton=" disabled";
                        $fondo_celda_dia="#F17327";
                        }
                }

        $dia_dentro_periodo=true;
        if ($estado_boton!=" disabled") {
                // Comprueba periodo de inscripcion
                if ((strtotime($datos_alumno["fecha_matricula"])>intval($inicio_time_stamp)) or (strtotime($datos_alumno["fecha_baja"])<intval($inicio_time_stamp-86400))) {
                        // Día fuera del periodo de asistencia
                        $fondo_celda_dia="#FFCE70";
                        $estado_boton=" checked";
                        $dia_dentro_periodo=false;
                        }
                }
        if ($estado_boton!=" disabled") {
                // Comprueba asistencia del niño
                $busca_asistencia=db_query("SELECT * FROM horario_alumnos_dias WHERE id_usuario=$id_alumno AND fecha='".date("Y-m-d",$inicio_time_stamp)."' LIMIT 0,1");
                if (db_num_rows($busca_asistencia)!=0) {
                        $datos_asistencia=db_fetch_array($busca_asistencia);
                        if ($datos_asistencia["no_comedor"]==0) {
                                $estado_boton_comedor=" checked";
                                $dias_comedor_mes++;
                                $dias_comedor++;
                                }
                        if ($datos_asistencia["no_asistencia"]==1) {
                                $estado_boton=" checked";
                                if ($dia_dentro_periodo==true) {
                                        // Dia NO asistido indicado en tabla
                                        $fondo_celda_dia="#F9F5BA";
                                        } else {
                                        $fondo_celda_dia="#FFCE70";
                                        }
                                } else {
                                // Dia asistido indicado en tabla
                                $estado_boton="";
                                $dias_trabajados_mes++;
                                $dias_trabajados++;
                                $fondo_celda_dia="#D1FFD3";
                                }
                        } else {
                        if ($estado_boton!=" checked") {
                                // Dia asistido por omisión
                                $estado_boton="";
                                $dias_trabajados_mes++;
                                $dias_trabajados++;
                                $fondo_celda_dia="#D1FFD3";
                                if ($comedor_alumno!=2) {
                                        $estado_boton_comedor=" checked";
                                        $dias_comedor_mes++;
                                        $dias_comedor++;
                                        }
                                }
                        }
                }

        // Comprueba asistencia real

        $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$id_usuario." AND fecha='".date("Y-m-d",$inicio_time_stamp)."' LIMIT 0,1") or die(db_error());
        if (db_num_rows($busca_registro_entrada)!=0) {
                $estado_boton=" disabled";
                $fondo_celda_dia="#A6C6EA";

                $dias_asistidos_reales_mes++;
                $dias_asistidos_reales++;
                
                $datos_registro_entrada=db_fetch_array($busca_registro_entrada);
                
                if ($datos_registro_entrada["hora_entrada_c"]!="") {
                        $dias_comedor_reales_mes++;
                        $dias_comedor_reales++;
                        } else {
                        if ($estado_boton_comedor==" checked") {
                                $fondo_celda_dia="#f797f6";
                                $faltas_comedor_mes++;
                                $faltas_comedor++;
                                }
                        }
                
                } else {
                if (($inicio_time_stamp<strtotime(date("Y-m-d"))) and ($fondo_celda_dia=="#D1FFD3")) {
                        $fondo_celda_dia="#FFF200";
                        $dias_faltados_mes++;
                        $dias_faltados++;
                        if ($estado_boton_comedor==" checked") {
                                $faltas_comedor_mes++;
                                $faltas_comedor++;
                                }
                        }
                }
        if ($inicio_time_stamp<strtotime(date("Y-m-d"))) {
                $estado_boton_comedor=$estado_boton_comedor." disabled";
                $estado_boton=$estado_boton." disabled";
                }
                
        db_free_result($busca_registro_entrada);
        
        ?>
        <td align="center" style="background: <?=$fondo_celda_dia;?>" id="celda<?=$inicio_time_stamp;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=1>
        <?php 
        if ($estado_boton==" disabled") {
        print "<a href='#' title='Día festivo'>";
        $estado_boton_comedor=$estado_boton_comedor." disabled";
        } else {
        print "<a href='javascript:cambia_dia($inicio_time_stamp,$id_curso_actual,$centro_actual)' title='Seleccionar día'>";
        }
        if ($mes==date("n",$inicio_time_stamp))
                {
                print "<b>";    }
        if ($dias==7)
                {
                print "<font color=#800000>";    }
        if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
                {
                print "<font color='blue'>";    }
                print (date("j",$inicio_time_stamp));
        if ($mes==date("n",$inicio_time_stamp))
                {
                print "</b>";
                }
                print "</a>";
        if ($estado_boton==" checked") {
                $estado_boton_comedor=$estado_boton_comedor." disabled";
                }
        ?>
        <br>No asiste:<input type="checkbox" value="0" name="chkbx<?=$inicio_time_stamp;?>" id="chkbx<?=$inicio_time_stamp;?>"<?=$estado_boton?> onclick="cambia_dia(<?=$inicio_time_stamp;?>,<?=$id_curso_actual;?>,<?=$centro_actual;?>)">
         Comedor: <input type="checkbox" value="0" name="comedor<?=$inicio_time_stamp;?>" id="comedor<?=$inicio_time_stamp;?>"<?=$estado_boton_comedor?> onclick="cambia_comedor(<?=$inicio_time_stamp;?>,<?=$id_curso_actual;?>,<?=$centro_actual;?>)">
        </font>
        </td>
        <?php 
        } else {
        ?>
        <td align="center" bgcolor="#AAAAAA">
        <font face="<?=$fuente_general?>" color="#000000" size=1>&nbsp;</font>
        </td>
        <?php 
        }
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php  
}
$mes=$mes+1;
if ($mes==13) {
        $mes=1;
        }
$anio_actual=date("Y",$inicio_time_stamp);
$inicio_time_stamp=$inicio_time_stamp-(86400*7);
?>
<tr>
<td>
</td>
</tr>
</form>
</table>
<font size=1><b>Dias reales de asistencia durante el mes:</b> <?=$dias_asistidos_reales_mes?><br>
<font size=1><b>Dias previstos de asistencia durante el mes:</b> <?=$dias_trabajados_mes?><br>
<font size=1><b>Faltas de asistencia durante el mes:</b> <?=$dias_faltados_mes?><br>
<font size=1><b>Dias reales de comedor durante el mes:</b> <?=$dias_comedor_reales_mes?><br>
<font size=1><b>Dias previstos de comedor durante el mes:</b> <?=$dias_comedor_mes?><br>
<font size=1><b>Faltas de asistencia al comedor durante el mes:</b> <?=$faltas_comedor_mes?><br><br></font>
<?php 
}
?>
<font size=1><b>Dias reales de asistencia durante el curso:</b> <?=$dias_asistidos_reales?><br>
<font size=1><b>Dias previstos de asistencia durante el curso:</b> <?=$dias_trabajados?><br>
<font size=1><b>Faltas de asistencia durante el curso:</b> <?=$dias_faltados?><br>
<font size=1><b>Dias reales de comedor durante el curso:</b> <?=$dias_comedor_reales?><br>
<font size=1><b>Dias previstos de comedor durante el curso:</b> <?=$dias_comedor?><br>
<font size=1><b>Faltas de asistencia al comedor durante el curso:</b> <?=$faltas_comedor?><br><br></font>
</div>
<script language="Javascript">

function cambia_dia(dia,curso,centro) {
        estado_actual=document.getElementById('chkbx'+dia).checked;
        
        if (estado_actual==false) {
                document.getElementById('celda'+dia).style.background="#9DE517";
                estado_actual=document.getElementById('comedor'+dia).disabled=false;
                } else {
                document.getElementById('celda'+dia).style.background="#E51740";
                estado_actual=document.getElementById('comedor'+dia).disabled=true;
                }
                
        window.open("cambia_estado_dia1.php?estado_inicial="+estado_actual+"&id_alumno=<?=$id_alumno?>&dia="+dia+"&curso="+curso+"&centro="+centro,"cambia_dia"+dia,"toolbar=no,width=150,height=100");
        }
</script>
<script language="Javascript">

function cambia_comedor(dia,curso,centro) {
        estado_actual=document.getElementById('comedor'+dia).checked;

        window.open("cambia_estado_dia2.php?estado_inicial="+estado_actual+"&id_alumno=<?=$id_alumno?>&dia="+dia+"&curso="+curso+"&centro="+centro,"cambia_comedor"+dia,"toolbar=no,width=150,height=100");
        }
</script>
<?php 
}
} else {
?>
El Alumno no Existe...
<br><br>
<input type="button" onclick="history.go(-1);" value="Volver">
<?php 
}
require("../../configuracion/parte_inferior.php"); ?>
