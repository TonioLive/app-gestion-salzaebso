<?php  require("../../configuracion/parte_superior_docente_ancho.php"); ?>
<?php 

$id_docente=$idUsuario;
if (strlen($id_docente)==0) {
        $id_docente=$_POST["id_docente"];
        }

if (strlen($centro_actual)==0) {
        $centro_actual=0;
        }

if (strlen($id_docente)!=0) {
        $busca_datos_docente=db_query("SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=$id_docente AND horario_docentes.id_centro=$centro_actual") or die(db_error());
        if (db_num_rows($busca_datos_docente)!=0) {
                $datos_docente=db_fetch_array($busca_datos_docente);
                $id_horario=$datos_docente["id_horario"];
                $mensaje="Edición";
                $nombre_docente=$datos_docente["Nombre"]." ".$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"];

                $hora_entrada_man=$datos_docente["hora_entrada1"];
                if ($hora_entrada_man=="00:00:00") {
                        $hora_entrada_man=$hora_entrada_centro_man;
                        }
                $hora_salida_man=$datos_docente["hora_salida1"];
                if ($hora_salida_man=="00:00:00") {
                        $hora_salida_man=$hora_salida_centro_man;
                        }
                $hora_entrada_tar=$datos_docente["hora_entrada2"];
                if ($hora_entrada_tar=="00:00:00") {
                        $hora_entrada_tar=$hora_entrada_centro_tar;
                        }
                $hora_salida_tar=$datos_docente["hora_salida2"];
                if ($hora_salida_tar=="00:00:00") {
                        $hora_salida_tar=$hora_salida_centro_tar;
                        }
;
                $diaseman[1]=$datos_docente["l"];
                $diaseman[2]=$datos_docente["m"];
                $diaseman[3]=$datos_docente["x"];
                $diaseman[4]=$datos_docente["j"];
                $diaseman[5]=$datos_docente["v"];
                $diaseman[6]=$datos_docente["s"];
                $diaseman[7]=$datos_docente["d"];
                }

        }

// A continuación detecta en que dia cae el primer lunes de esta mes
// Inicio=fecha del lunes
// mesini=Nombre del mes en el que empieza la semana
// mesfin=Nombre del mes en el que acaba la semana
// Si hoy es domingo pasa automaticamente a la semana siguiente

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
}


$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'");

if (db_num_rows($busca_curso_actual)!=0) {
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        } else {
        $id_curso_actual=0;
        }

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

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

//Inicio es el día en el que empieza la primera semana del mes
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;

$dias_laborales_mes=0;
$dias_trabajados_mes=0;
$no_ficha_salida=0;
?>
<font color="#000000" size=3 face="<?=$fuente_general?>"><b><?=$nombre_docente;?><br>
<?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?>
</b></font>
<table border=0 cellspacing="1" cellpadding="1" width="100%" height="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
<?php 
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
</font>
<td valign="top">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
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

  if ($mes==date("n",$inicio_time_stamp))
    {
    $fondo_celda_dia="#EFEFEF";
    } else {
    $fondo_celda_dia="#AAAAAA";
    }
  if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    $fondo_celda_dia="#DD9999";
    }

        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query("SELECT * FROM calendario_laboral WHERE id_curso=$id_curso_actual AND id_centro=$centro_actual AND dia='".date("Y-m-d",$inicio_time_stamp)."'") or die(db_error());
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        $fondo_celda_dia="#F17327";
                        } else {
                        $dias_laborales_mes++;
                        }
                } else {
                $dias_laborales_mes++;
                }

        $horas_hoy=0;
        $minutos_hoy=0;

        $ficha_hoy=false;

        $busca_asistencia=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=$id_docente AND fecha='".date("Y-m-d",$inicio_time_stamp)."' ORDER BY turno") or die(db_error());
        $horas_entrada_y_salida="";
        while($datos_asistencia=db_fetch_array($busca_asistencia)){
                $ficha_hoy=true;
                if ((strlen($datos_asistencia["hora_salida"])!=0) and (strlen($datos_asistencia["hora_entrada"])!=0)) {
                        $diferencia_entrada=intval(strtotime($datos_asistencia["hora_salida"]))-intval(strtotime($datos_asistencia["hora_entrada"]));
                        } else {
                        $diferencia_entrada=0;
                        }

                $horas_diferencia=($diferencia_entrada/60)/60;
                if ($horas_diferencia<round((($diferencia_entrada/60)/60),0)) {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0)-1;
                        } else {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0);
                        }

                $minutos_diferencia=$horas_diferencia-$horas_diferencia1;
                $minutos_diferencia=round(($minutos_diferencia*60),0);

                $total_horas=intval($total_horas)+intval($horas_diferencia1);
                $total_minutos=intval($total_minutos)+intval($minutos_diferencia);
                $minutos_hoy=intval($minutos_hoy)+intval($minutos_diferencia);

                $horas_diferencia1="0".$horas_diferencia1;
                $minutos_diferencia="0".$minutos_diferencia;

                $horas_diferencia1=substr($horas_diferencia1,strlen($horas_diferencia1)-2,2);
                $minutos_diferencia=substr($minutos_diferencia,strlen($minutos_diferencia)-2,2);

                if ($diferencia_entrada!=0) {
                        $diferencia_horas=$horas_diferencia1.":".$minutos_diferencia;
                        } else {
                        $diferencia_horas="00:00";
                        }

                if ($datos_asistencia["turno"]==0) {
                        $nombre_turno="Mañana";
                        } else {
                        $nombre_turno="Tarde";
                        }

        $horas_entrada_y_salida=$horas_entrada_y_salida.$nombre_turno.": ".date("H:i",strtotime($datos_asistencia["hora_entrada"]))."-".date("H:i",strtotime($datos_asistencia["hora_salida"])).".<br>Horas realizadas: ".$diferencia_horas."<br>";
        $horas_hoy=$horas_hoy+$diferencia_entrada;

        }

        if ($ficha_hoy==true) {
                $total_dias++;
                $fondo_celda_dia="#aad0a4";
                if ($horas_hoy==0) {
                        $fondo_celda_dia="#ff5a5a";
                        $no_ficha_salida++;
                        }
                }

?>
<td align="right" bgcolor="<?=$fondo_celda_dia;?>" valign="top">
<font face="<?=$fuente_general?>" color="#000000" size=3>
<?php 
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
    $no_asistencia=0;
    $busca_horario_hoy=db_query("SELECT * FROM horario_docentes_dias WHERE id_usuario=$id_docente AND fecha='".date("Y-m-d",$inicio_time_stamp)."'");
    if (db_num_rows($busca_horario_hoy)!=0) {
        $horario_hoy=db_fetch_array($busca_horario_hoy);
        $hora_entrada_man_h=$horario_hoy["hora_entrada1"];
        $hora_salida_man_h=$horario_hoy["hora_salida1"];
        $hora_entrada_tar_h=$horario_hoy["hora_entrada2"];
        $hora_salida_tar_h=$horario_hoy["hora_salida2"];
        $no_asistencia=$horario_hoy["no_asistencia"];
        } else {
        // Comprueba si el dia es festivo en el calendario laboral
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $no_asistencia=1;
                } else {
                $no_asistencia=0;
                }
        $hora_entrada_man_h=$hora_entrada_man;
        $hora_salida_man_h=$hora_salida_man;
        $hora_entrada_tar_h=$hora_entrada_tar;
        $hora_salida_tar_h=$hora_salidaa_tar;
        }

?>
</font><div align="center"><font size=1><?php 
if ($no_asistencia!=1) {
if (($diaseman[$dias]==1) or (db_num_rows($busca_horario_hoy)!=0)) {
        $dias_trabajados_mes++;
        if (strlen($horas_entrada_y_salida)!=0) {
        ?><?=$horas_entrada_y_salida;?><?php 
        } else {
        ?>Mañana: <?=substr($hora_entrada_man,0,5);?>-<?=substr($hora_salida_man,0,5)?><br>Tarde: <?=substr($hora_entrada_tar,0,5)?>-<?=substr($hora_salida_tar,0,5);?><?php 
        }
        }
}
?></font></div>
</td>
<?php 
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php 
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

?>
<tr>
<td>
</td>
</tr>
</table>
<br><font size=2>
<table width=100% bordercolor="#000000" border=1 style="border-collapse: collapse">
<tr>
<td align="right" bgcolor="#efefef" width=40%><font size=2><b>Días laborables:</b></font></td><td align="right"><font size=2><?=$dias_laborales_mes;?></font></td>
<td align="right" bgcolor="#efefef" width=40%><font size=2><b>Días previstos de trabajo:</b></font></td><td align="right"><font size=2><?=$dias_trabajados_mes;?></font></td>
</tr><tr>
<td align="right" bgcolor="#efefef" width=40%><font size=2><b>Días reales trabajados:</b></font></td><td align="right"><font size=2><?=$total_dias;?></font></td>
<td align="right" bgcolor="#efefef" width=40%><font size=2><b>Días que olvida fichar salida:</b></font></td><td align="right"><font size=2><?=$no_ficha_salida;?></font></td>
</tr><tr>
<td align="right" bgcolor="#efefef" width=40%><font size=2><b>Total horas trabajadas durante el mes:</b></font></td><td align="right"><font size=2><?=$total_horas.":".$minutos_restantes;?></font></td>
<td colspan=2 bgcolor="#efefef" width=50%><table align=center>
<tr>
<td><font size=1><b>Leyenda: </b></font></td>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#aad0a4"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Trabajado</font></td>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#ff5a5a"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Olvida fichar</font></td>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#F17327"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Festivo</font></td>
</tr>
</table></td>
</tr>
</table>
<br>
<a href="ver_calendario.php?dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1"><< Mes anterior | <a href="ver_calendario.php?dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1">Mes siguiente >></a>
</font>
<br>
<br>
<br><input type="button" onclick="window.location.href='ficha.php'" value="Volver">
<?php  require("../../configuracion/parte_inferior.php"); ?>
