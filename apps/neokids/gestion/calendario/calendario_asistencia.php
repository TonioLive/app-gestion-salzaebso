<?php  require("../../configuracion/parte_superior_sin_login_ancho.php"); ?>
<div align="center">
<?php 
$id_alumno=$_GET["id_alumno"];
if (strlen($id_alumno)==0) {
        $id_alumno=$_POST["id_alumno"];
        }
        
$busca_datos_alumno=db_query_params('SELECT usuarios.*,alumnos.*,alumnos.id_centro AS centro_asistencia FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=? LIMIT 0,1', 's', array($id_alumno)) or die(db_error());

if (db_num_rows($busca_datos_alumno)!=0) {

$datos_alumno=db_fetch_array($busca_datos_alumno);
$id_usuario=$datos_alumno["id_usuario"];

$nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];

// Busca el curso actual
$curso_seleccionado=$datos_alumno["id_curso"];
if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
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
                        <font size=2>ATENCION: Los días no lectivos son los que aparecen marcados en el calendario de color naranja.</font><br><br>
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
<td bgcolor="#F9F5BA"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día lectivo al que ñino seleccionado no asistirá.</font></td>
</tr>
<tr>
<td bgcolor="#D1FFD3"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Dia léctivo al que el niño seleccionado debe asistir.</font></td>
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
$dias_faltados=0;
for ($mes_s=1; $mes_s<=12; $mes_s=$mes_s+1) {
$mesnombre=$nombre_mes[$mes];
$dias_trabajados_mes=0;
$dias_asistidos_reales_mes=0;
$dias_faltados_mes=0;
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
        
        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=? LIMIT 0,1', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", $inicio_time_stamp))));
        
        // Estado inicial
        $estado_boton="";
        $fondo_celda_dia="#EFEFEF";       
                        
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
                
				// Comprueba los grupos
                $busca_grupo_alumno=db_query("SELECT * FROM aulas_alumnos_grupos LEFT JOIN aulas_grupos ON aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo WHERE aulas_alumnos_grupos.id_alumno=$id_alumno AND aulas_grupos.dia=$dias AND aulas_grupos.id_curso=$id_curso_actual");                
                
                if ((strtotime($datos_alumno["fecha_matricula"])>intval($inicio_time_stamp)) or (strtotime($datos_alumno["fecha_baja"])<intval($inicio_time_stamp-86400)) or (db_num_rows($busca_grupo_alumno)==0)) {
                        // Día fuera del periodo de asistencia
                        $fondo_celda_dia="#FFCE70";
                        $estado_boton=" checked";
                        $dia_dentro_periodo=false;
                        }

                }
        if ($estado_boton!=" disabled") {
                // Comprueba asistencia del niño
                $busca_asistencia=db_query_params('SELECT * FROM horario_alumnos_dias WHERE id_usuario=? AND fecha=? LIMIT 0,1', 'ss', array($id_alumno, (string)(date("Y-m-d", $inicio_time_stamp))));
                if (db_num_rows($busca_asistencia)!=0) {
                        $datos_asistencia=db_fetch_array($busca_asistencia);

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
                                }
                        }
                }

        // Comprueba asistencia real

        $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND fecha=? LIMIT 0,1', 'ss', array($id_usuario, (string)(date("Y-m-d", $inicio_time_stamp)))) or die(db_error());
        if (db_num_rows($busca_registro_entrada)!=0) {
                $estado_boton=" disabled";
                $fondo_celda_dia="#A6C6EA";

                $dias_asistidos_reales_mes++;
                $dias_asistidos_reales++;
                
                $datos_registro_entrada=db_fetch_array($busca_registro_entrada);             
                
                } else {
                if (($inicio_time_stamp<strtotime(date("Y-m-d"))) and ($fondo_celda_dia=="#D1FFD3")) {
                        $fondo_celda_dia="#FFF200";
                        $dias_faltados_mes++;
                        $dias_faltados++;
                        }
                }
        if ($inicio_time_stamp<strtotime(date("Y-m-d"))) {
                $estado_boton=$estado_boton." disabled";
                }
                
        db_free_result($busca_registro_entrada);
        
        ?>
        <td align="center" style="background: <?=$fondo_celda_dia;?>" id="celda<?=$inicio_time_stamp;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=1>
        <?php 
        if ($estado_boton==" disabled") {
        print "<a href='#' title='Día festivo'>";
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
        ?>
        <br>No asiste:<input type="checkbox" value="0" name="chkbx<?=$inicio_time_stamp;?>" id="chkbx<?=$inicio_time_stamp;?>"<?=$estado_boton?> onclick="cambia_dia(<?=$inicio_time_stamp;?>,<?=$id_curso_actual;?>,<?=$centro_actual;?>)">
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
<?php 
}
?>
<font size=1><b>Dias reales de asistencia durante el curso:</b> <?=$dias_asistidos_reales?><br>
<font size=1><b>Dias previstos de asistencia durante el curso:</b> <?=$dias_trabajados?><br>
<font size=1><b>Faltas de asistencia durante el curso:</b> <?=$dias_faltados?><br>
</div>
<script language="Javascript">

function cambia_dia(dia,curso,centro) {
        estado_actual=document.getElementById('chkbx'+dia).checked;
        
        if (estado_actual==false) {
                document.getElementById('celda'+dia).style.background="#9DE517";
                } else {
                document.getElementById('celda'+dia).style.background="#E51740";
                }
                
        window.open("cambia_estado_dia1.php?estado_inicial="+estado_actual+"&id_alumno=<?=$id_alumno?>&dia="+dia+"&curso="+curso+"&centro="+centro,"cambia_dia"+dia,"toolbar=no,width=150,height=100");
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
