<?php 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

// Comprobar si 'nocache' está presente en la URL
if (!isset($_GET['nocache']) || $_GET['nocache'] < strtotime('today')) {
  $url = $_SERVER['REQUEST_URI'];
  $separator = (strpos($url, '?') === false) ? '?' : '&';

  // Generar un nuevo timestamp basado en la fecha actual
  $newNocache = time();

  // Si 'nocache' ya está en la URL, reemplazarlo
  if (isset($_GET['nocache'])) {
      $url = preg_replace('/([?&])nocache=\d+/', '$1nocache=' . $newNocache, $url);
  } else {
      $url .= $separator . "nocache=" . $newNocache;
  }

  // Redirigir con el nuevo valor
  header("Location: " . $url);
  exit;
}

require("../configuracion/parte_superior_docente_ancho.php"); ?>
<?php
include("actualiza_comedor.php");

$id_aula=$_GET["id_aula"];

$id_centro=$centro_actual;

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

?>
<font color="#000000" size=3 face="<?=$fuente_general?>"><b>Control Asistencia Comedor<br>
<?php echo $mesnombre; ?> de <?php echo date("Y",$fecha_time_stamp); ?>
</b></font>
<br><br>Porcentaje de asistencia previsto: <select name="porcentaje" id="porcentaje">
	<?php
	for ($porcentaje=1;$porcentaje>=0.5;$porcentaje=$porcentaje-0.1) {
	?>
		<option value="<?=$porcentaje?>"><?=round($porcentaje*100,0)?> %</option>
	<?php
	}
	?>
</select><br>
<table border=0 cellspacing="1" cellpadding="1" width="100%" height="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
<?php 
for ($dia_s=1; $dia_s<=5; $dia_s=$dia_s+1)
{
?>
</font>
<td valign="top">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
<b>
<?php   echo $dia_sem_largo[$dia_s]; ?>
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

    if ($dias<=5)
    {
?>
<td align="right" bgcolor="<?=$fondo_celda_dia;?>">
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

?>
</font><div align="left">
<!--
<a href="#" onclick="window.open('prevision_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">1. Previsión</a><br>
<a href="#" onclick="window.open('prevision_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>&mostrar_listado=1','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">2. Prev. con alumnos</a><br>
<a href="#" onclick="window.open('prevision_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>&solo_escuela=1','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">3. Previsión solo escuela actual.</a><br>
<a href="#" onclick="window.open('prevision_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>&mostrar_listado=1&solo_escuela=1','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">4. Prev. con alumnos escuela actual</a><br>
<a href="#" onclick="window.open('control_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">5. Lista Definitiva</a><br>
<a href="#" onclick="window.open('control_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>&mostrar_listado=1','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">6. Definitiva con alumnos.</a><br>
<a href="#" onclick="window.open('control_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>&solo_escuela=1','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">7. Lista Def. solo escuela actual</a><br>
<a href="#" onclick="window.open('control_asistencia_comedor.php?dia=<?= $inicio_time_stamp; ?>&mostrar_listado=1&solo_escuela=1','listado_cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">8. Def. con alumnos escuela actual.</a><br>
-->
<a href="#" onclick="window.open('prevision_preparacion_platos.php?dia=<?= $inicio_time_stamp; ?>','cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">1. Previsión preparación platos.</a><br>
<a href="#" onclick="window.open('prevision_preparacion_platos_nuevo.php?dia=<?= $inicio_time_stamp; ?>&porcentaje='+document.getElementById('porcentaje').value,'cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600')">2. Previsión preparación con % de asistencia.</a><br>
<a href="#" onclick="window.open('listado_preparacion_platos.php?dia=<?= $inicio_time_stamp; ?>','cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600,scrollbars=yes')">3. Definitivo preparación platos.</a><br>
<?php
/*
<a href="#" onclick="window.open('intro_temperaturas.php?dia=<?= $inicio_time_stamp; ?>','cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600,scrollbars=yes')">4. Albaranes entrega platos.</a><br>
<a href="albaranes_entrega_seleccion.php?dia=<?= $inicio_time_stamp; ?>">5. Firmar Albaranes de entrega.</a><br>
<a href="#" onclick="window.open('/pdf/etiquetas_platos.php?dia=<?= $inicio_time_stamp; ?>','cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600,scrollbars=yes')">6. Imprimir etiquetas platos.</a><br>
<a href="#" onclick="window.open('albaranes_entrega_platos.php?dia=<?= $inicio_time_stamp; ?>&id_centro=<?=$centro_actual;?>','cocina','scrollbars=yes,toolbar=yes,resizable=yes,width=800,height=600,scrollbars=yes')">7. Albaran entrega de mi escuela.</a><br>
*/
?>
</div>
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
?>
<tr>
<td>
</td>
</tr>
</table>
<br><font size=2>
<a href="calendario_mes_comedor.php?id_aula=<?=$id_aula?>&dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1"><< Mes anterior | <a href="calendario_mes_comedor.php?id_aula=<?=$id_aula?>&dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1">Mes siguiente >></a>
</font>
<br>
<br>
<font color="#000000" size=2 face="<?=$fuente_general?>">
(*) Haz click! en el día para acceder al control de asistencia<br>
</font>
<script>
  const reloadInterval = 30; // Tiempo en segundos

  setTimeout(() => {
      const url = new URL(window.location.href);
      url.searchParams.set('nocache', Date.now()); // Agrega un timestamp único
      window.location.replace(url.toString()); // Recarga con la nueva URL
  }, reloadInterval * 1000);
<script>
<?php require("../configuracion/parte_inferior.php"); ?>
