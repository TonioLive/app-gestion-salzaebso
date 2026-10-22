<?php 
require("../configuracion/config.php");

require("../configuracion/autentifica_padre.php");


$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
	$nombre_pagina_actual="menu";
	}
 
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Escuela Infantil Lápices</title>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
<link href='http://fonts.googleapis.com/css?family=Delius+Swash+Caps' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="style.css" type="text/css" media="all">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript"></script>
</head>

<body>
<div class="logo-wrapper">
<div class="logo">
    <a href="index.html"><img src="images/logo.png" alt="Escuela Infantil Lápices - Entorno privado padres"></a>
</div>
</div>
<div class="header">
	<div class="wrap top-bar">
    	<button class="menu-show"><img src="images/plus.png" alt="plus"></button>
    	<button class="menu-hide"><img src="images/minus.png" alt="minus"></button>
        <div class="search">
        	<form>
            	<input type="text">
                <input type="submit" value="">
            </form>
        </div>
        <div class="clear-both"></div>
        <nav class="menu">
            <ul>
<?php  include("menu_lateral.php"); ?>
            </ul>
        </nav>
    </div>
</div>
<div class="content">
	<div class="wrap">
	<?php 
	print "<div align=center><h2>¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print strtoupper($datos_padre["Nombre"])."</h2></div>";
	if (strlen($texto_alerta)!=0) { 
	?>
        <div class="post">
            <p><?=$texto_alerta?></p>
         </div>
    <?php 
    }

// Día
if (strlen($_GET["dia"])!=0) {
        $fecha_time_stamp=$_GET["dia"];
        } else {
        $fecha_time_stamp=time();
        }
        
// Busca circulares enviadas

$busca_mensajes=db_query_params('SELECT * FROM mensajes_temporal WHERE fecha<=? ORDER BY fecha DESC LIMIT 0,1', 's', array((string)(date("Y-m-d"))));

$num_circulares=db_num_rows($busca_mensajes);

$num_circulares=0;

if ($num_circulares!=0) {
?>
<span class="texto_encabezado"><a href="circulares.php">Circulares</a></span><br />
<?php 
while($mensajes=db_fetch_array($busca_mensajes)){
?>
<div class="page80"><span class="subtitulo_encabezado"><?=$mensajes["asunto"];?></span><br /><?=utf8_decode($mensajes["texto_editor"]);?></div><br />
<?php 
}

}
// Agenda de actividades padres

// Busca hijos del padre

        $aulas_hijos="";
        $centros_hijos="";
        
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {
    
        $contador_hijos=0;
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                $busca_datos_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_hijos["id_curso"]));
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                        } else {
                        $nombre_curso_mostrado="";
                        }
                        
                // Datos de la escuela del niño
                $busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_hijos["id_centro"]));

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del niño
                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

                $datos_aula=db_fetch_array($busca_datos_aula);
                
                $aulas_hijos.=" OR (Agenda.id_aula=".$datos_hijos["aula"].")";
                
                $centros_hijos.=" OR (Agenda.id_centro=".$datos_hijos["id_centro"]." AND Agenda.id_aula=0)";
                
                $contador_hijos++;
                }
            }

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

$hay_avisos=false;


$filtro_usuarios=" AND (Agenda.id_centro=0$centros_hijos$aulas_hijos)";

$hay=false;

// Consultamos citas de la agenda
$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND Agenda.Fecha='".date("Y-m-d",$fecha_time_stamp)."' AND agenda_asistentes.nivel=1$filtro_usuarios GROUP BY Agenda.id ORDER BY Agenda.id_centro,Agenda.Hora,Agenda.id_aula");

// $rsNot_query=mysql_query("SELECT * FROM Agenda WHERE DATE_FORMAT(Fecha,'%d/%m/%Y')='".$fecha_agenda."' ORDER BY id_centro,Hora,id_aula");

if (!$rsNot_query) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

if (db_num_rows($rsNot_query) != 0)
{
  $hay=true;
}

?>
<table width=98%  class="tabla_datos" align="center">
<tr><td class="tabla_celda_encabezado"><font size=3><b>Agenda de actividades - <?=date("d/m/Y",$fecha_time_stamp)?></b></font></td></tr>
</table>
<?php 
//Encabezado
?>
<table width=98%  class="tabla_datos" align="center">
<?php 
if ($hay==true)
{
  $escuela_actual=-1;
  $aula_actual=-1;
  // Citas de la agenda
  while($rsNot=db_fetch_array($rsNot_query))
  {
        if ($escuela_actual!=$rsNot["id_centro"]) {
                $escuela_actual=$rsNot["id_centro"];
                ?>
                <tr valign=top>
                        <td class="tabla_celda_datos"colspan=2><font color="#025392"><small><b><?= htmlentities($nombres_escuelas[$rsNot["id_centro"]]); ?></b></small></font></td>
                </tr>
                <?php 
                }
        if (($aula_actual!=$rsNot["id_aula"]) and ($escuela_actual!=0)) {
                $aula_actual=$rsNot["id_aula"];
                ?>
                <tr valign=top>
                        <td width="100%"  class="tabla_celda_datos" colspan=2><font color="#025392"><small><b><?= htmlentities($nombres_aulas[$rsNot["id_aula"]]); ?></b></small></font></td>
                </tr>
                <?php 
                }
                
                
if (strtotime($rsNot["Hora"])<strtotime(date("H:i"))) {
        $color_cita="#025392";
        } else {
        $color_cita="#1476c9";
        }
                
?>
  <tr valign=top>
    <td width="17%" class="tabla_celda_datos"><font color="<?=$color_cita?>" size=2><b><?php  if (date("H:i",strtotime($rsNot["Hora"]))!="00:00") { date("H:i",strtotime($rsNot["Hora"])); } else { ?>&nbsp;<?php  } ?></b></font></td>
    <td width="83%" class="tabla_celda_datos"><font color="<?=$color_cita?>" size=2><b><?= htmlentities($rsNot["Titulo"]); ?></b></font></td>
  </tr>
  <tr valign=top>
    <td width="17%" align="center" class="tabla_celda_datos">
    <?php 
     if ($rsNot["Nombre_usu"]==$identificador) {
                     $esmia=true;
             } else {
                     $esmia=false;
             }

    ?>&nbsp;
    </td>
    <td width="83%" class="tabla_celda_datos"><small><small><?= htmlentities($rsNot["Texto"]); ?></small></small></td>
  </tr>
<?php 
  }
} else {

if (date("Y-m-d")!=date("Y-m-d",$fecha_time_stamp)) {
        $hoy_o_dia="este d&iacute;a";
        } else {
        $hoy_o_dia="hoy";
        }

if (strtotime(date("Y-m-d"))>strtotime(date("Y-m-d",$fecha_time_stamp))) {
        $hay_o_habia="hab&iacute;a";
        } else {
        $hay_o_habia="hay";
        }
        
$hay_o_habra        

?>
  <tr valign=top>
    <td width="100%" colspan="2" align="center" class="tabla_celda_datos"><font color="#025392" size=3>No <?=$hay_o_habia?> actividades programadas para <?=$hoy_o_dia?></font></td>
  </tr>
<?php 
}
?>
</table>
<?php 
// Consultamos citas de la agenda

$fecha_avisos=date("Y-m-d",$fecha_time_stamp);

$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND Agenda.avisar<='".$fecha_avisos."' AND Agenda.Fecha>'".$fecha_avisos."' AND agenda_asistentes.nivel=1$filtro_usuarios GROUP BY Agenda.id ORDER BY Agenda.Fecha,Agenda.Hora");

if (db_num_rows($rsNot_query) != 0)
{
?>
<table width=98%  class="tabla_datos" align="center">
<tr><td class="tabla_celda_encabezado"><font size=3><b>En los próximos días</b></font></td></tr>
</table>
<table width=98%  class="tabla_datos" align="center">
<?php 
$fecha_actual_citas="";
while($rsNot=db_fetch_array($rsNot_query)) {

$dia_cita_timestamp=strtotime($rsNot["Fecha"]);
$color_cita="#025392";

if ($fecha_actual_citas!=$rsNot["Fecha"]) {
?>
<tr>
<td width="17%" class="tabla_celda_encabezado"><font color="<?=$color_cita?>" size=2><b><?=date("d/m/Y",strtotime($rsNot["Fecha"]));?></b></font></td>
<td width="83%" align="right" class="tabla_celda_datos"><font size=1>&nbsp;</font></td>
</tr>
<?php 
$fecha_actual_citas=$rsNot["Fecha"];
        }
?>
<tr>
<td width="17%"><font color="<?=$color_cita?>" size=2><b><?php  if (date("H:i",strtotime($rsNot["Hora"]))!="00:00") { date("H:i",strtotime($rsNot["Hora"])); } else { ?>&nbsp;<?php  } ?></b></font></td>
<td width="83%" class="tabla_celda_datos"><font color="<?=$color_cita?>" size=1><?=htmlentities($rsNot["Titulo"]);?>.</font></td>
</tr>
<?php 
}
?>
</table>
<?php 
}
?>
<p align="center"><a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp-86400; ?>">¿y ayer?</a> - <a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp+86400; ?>">¿y mañana?</a></p></td>
<td width="254"><div class="fb-like-box" data-href="https://www.facebook.com/lapicesescuelainfantil" data-width="250" data-show-faces="true" data-stream="true" data-header="true"></div></td>
</tr>
</table>
</td>
</tr>
</table>
    </div>
</div>
<div class="footer">
	<div class="wrap bot-bar">
    	&copy; <?=date("Y");?> Escuelas Infantiles Lapices &bull;
        <div class="clear-both"></div>
    </div>
</div>
<script type="text/javascript">
	$('.menu').hide();
	$('.menu-show').show();
	$('.menu-hide').hide();
	$('.menu-show').click(function(){
		$('.menu-show').toggle();
		$('.menu-hide').toggle();
		$('.menu').slideDown();
	});
	$('.menu-hide').click(function(){
		$('.menu-hide').toggle();
		$('.menu-show').toggle();
		$('.menu').slideUp();
	});
</script>
</body>
</html>
