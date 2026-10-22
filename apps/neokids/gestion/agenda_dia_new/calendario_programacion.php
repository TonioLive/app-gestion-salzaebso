<?php  require("../../configuracion/parte_superior_docente_ancho.php"); ?>
<div align="center">
<?php 
if (strlen($id_aula_seleccionada)==0) {
        $id_aula_seleccionada=$_GET["id_aula"];
        if (strlen($id_aula_seleccionada)==0) {
                $id_aula_seleccionada=$_POST["id_aula"];
                if (strlen($id_aula_seleccionada)==0) {
                        $id_aula_seleccionada=0;
                        $accion_texto="Nueva";
                        }
                }
        }

$id_curso_actual=$_GET["id_curso_aula"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_aula_seleccionada!=0) {
        $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula_seleccionada));

        if (db_num_rows($consulta_datos_aula)!=0)
                {
                $datos_aula=db_fetch_array($consulta_datos_aula);

                // Datos Fijos
                $aula_nombre=$datos_aula["nombre"];
                $id_curso_actual=$datos_aula["id_curso"];
                $centro_aula=$datos_aula["id_centro"];
                }

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        }

if ($id_aula_seleccionada!=0) {

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
<font size=3><b>Programación durante el curso <?=$datos_curso_actual["nombre"]?> del aula <?=$aula_nombre;?></b><br></font>
<font size=2>ATENCION: Los días no lectivos son los que aparecen marcados en el calendario de color naranja.</font><br><br>
<table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000">
<tr><td>
<table>
<tr>
<td colspan=2 align="center" bgcolor="#efefef"><font size=2><b>Leyenda de colores</b></font></td>
</tr>
<tr>
<td bgcolor="#cdefbb"><font size=2>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Día programado</font></td>
</tr>
<tr>
<td bgcolor="#bbd3ef"><font size=2>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Día programado y evaluado</font></td>
</tr>
<tr>
<td bgcolor="#F17327"><font size=2>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Día festivo</font></td>
</tr>
<tr>
<td bgcolor="#F9F5BA"><font size=2>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Día fuera del periodo lectivo del curso.</font></td>
</tr>
</table></td></tr></table>
<?php 
$anio_actual=date("Y",$fecha_time_stamp);
$dias_docencia=0;

for ($mes_s=1; $mes_s<=12; $mes_s=$mes_s+1) {
$mesnombre=$nombre_mes[$mes];
$dias_docencia_mes=0;
?>
<font color="#000000" size=2 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
<table border=0 cellspacing="1" cellpadding="1" width="100%">
<form name="calendario" method="POST" action="">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<td width="5%"><font face="<?=$fuente_general?>" color="#ffffff" size=2>&nbsp;</font></td>
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=2>
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
<td valign="bottom"><input type="button" onclick="cambia_semana('<?=date("Y-m-d",$inicio_time_stamp)?>',<?=$id_curso_actual?>,<?=$centro_actual?>)" value="Ver semana" style="font-size: 10px; width:95px; text-align: center"><br><input type="button" style="font-size: 10px; width:95px; text-align: center" value="Copiar semana" onclick="window.open('calendario_copiar_semana.php?id_aula=<?=$id_aula_seleccionada?>&inicio_semana=<?=date("Y/n/j",$inicio_time_stamp);?>&dia=<?=date("Y/n/j",$inicio_time_stamp);?>','semana','toolbar=no,resizable=yes,scrollbars=yes,width=330,height=216')"></td>
<?php  
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {

    if ($mes==date("n",$inicio_time_stamp)) {
        
        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=? LIMIT 0,1', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", $inicio_time_stamp))));
        
        // Estado inicial
        $estado_boton="";
        $fondo_celda_dia="#EFEFEF";
        
        if (db_num_rows($busca_dia_en_calendario)==0) {
                if (($dias==7) or ($dias==6)) {
                        // Sábado o domingo
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
                
        // Comprueba si está programado
        $busca_actividades_dia=db_query_params('SELECT * FROM agenda_programacion WHERE id_aula=? AND fecha=? AND ((id_actividad<>0) OR (actividad IS NOT null))', 'ss', array($id_aula_seleccionada, (string)(date("Y-m-d", $inicio_time_stamp))));        
         
        if (db_num_rows($busca_actividades_dia)!=0) {
        	$fondo_celda_dia="#cdefbb";
        
        	// Comprueba si está evaluado
        	if (date("Y-m-d",$inicio_time_stamp)<=date("Y-m-d")) {
				$busca_actividades_dia=db_query_params('SELECT * FROM agenda_programacion WHERE id_aula=? AND fecha=? AND objetivo<>0 ORDER BY tiempo_trabajo,orden_actividad', 'ss', array($id_aula, (string)(date("Y-m-d", $inicio_time_stamp))));

				$esta_evaluado=false;
        
        		while($actividades_dia=db_fetch_array($busca_actividades_dia)) {
        			$busca_evaluacion=db_query_params('SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_prog=?', 's', array($actividades_dia["id_prog"]));
        			if (db_num_rows($busca_evaluacion)!=0) {
        				$esta_evaluado=true;
        				}
        			}
        		if ($esta_evaluado==true) {
	        		$fondo_celda_dia="#bbd3ef";        			
        			}
        		}
        	
        	} 
                
        ?>
        <td align="center" style="background: <?=$fondo_celda_dia;?>" id="celda<?=$inicio_time_stamp;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=2>
        <?php 
        if ($estado_boton==" disabled") {
        print "<a href='#' title='Día festivo'>";
        } else {
        ?>
        <a href="javascript:cambia_dia('<?=date("Y-m-d",$inicio_time_stamp)?>',<?=$id_curso_actual?>,<?=$centro_actual?>)" title='Seleccionar día'>
        <?php 
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
<br><input type="button" style="font-size: 10px; width:85px; text-align: center" value="Ver Día" onclick="cambia_dia('<?=date("Y-m-d",$inicio_time_stamp)?>',<?=$id_curso_actual?>,<?=$centro_actual?>)"><br><input type="button" style="font-size: 10px; width:85px; text-align: center" value="Copiar Día" onclick="window.open('calendario_copiar_dia.php?id_aula=<?=$id_aula_seleccionada?>&inicio_dia=<?=date("Y/n/j",$inicio_time_stamp);?>&dia=<?=date("Y/n/j",$inicio_time_stamp);?>','semana','toolbar=no,resizable=yes,scrollbars=yes,width=330,height=216')">
        </font>
        </td>
        <?php 
        } else {
        ?>
        <td align="center" bgcolor="#AAAAAA">
        <font face="<?=$fuente_general?>" color="#000000" size=2>&nbsp;</font>
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
<font size=2><b>Dias de docencia durante el mes:</b> <?=$dias_docencia_mes?><br><br>
<?php 
}
?>
<font size=2><b>Dias de docencia durante el curso:</b> <?=$dias_docencia?><br><br>
</div>
<script language="Javascript">

function cambia_dia(dia,curso,centro) {
		window.location.href="semana_programacion.php?id_aula=<?=$id_aula?>&fecha_inicio="+dia;
        }
function cambia_semana(dia,curso,centro) {
		window.location.href="../agenda/semana_programacion.php?id_aula=<?=$id_aula?>&fecha_inicio="+dia;
        }
</script>

<?php 
}
} else {
?>
<br><br>
<input type="button" onclick="history.go(-1);" value="Volver">
<?php 
}
require("../../configuracion/parte_inferior.php"); ?>
