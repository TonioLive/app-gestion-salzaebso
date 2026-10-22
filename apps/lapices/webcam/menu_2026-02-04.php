<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres_modal.php"); ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/es_LA/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php     
if ($errores=="") {
?>
    <div align="center">
<table width=760 border=0 cellpadding="0" cellspacing=0 align=center>
<tr valign="top">
<td width="185"><?php  include("menu_lateral.php"); ?></td>
<td width="675">
<table cellpadding=2 cellspacing=0 border=0 width=100%>
<tr valign="top">
<td><?php 
    // Mostramos bienvenida
    print "<div align=center>¡Hola! ";
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

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br></div>";
				  
$mostrar_inscripcion = true;
$mostrar_bono = false;
$mostrar_encuesta = false;

if (date("Y-m-d")>='2025-04-01') {
        $mostrar_encuesta=true;
}

if ($mostrar_encuesta==true) {
       ?><br />
<div class="alert alert-dismissible" role="alert" id="alertaApp">
        <div class="panel panel-default">
                <div class="panel-heading">
                        <h3 class="panel-title"><b>Encuesta para las familias</b></h3>                                
                </div>
                <div class="panel-body">
                        <p class="text-center"> Hola, ¿te importaría dedicar 5 minutos a completar este cuestionario? Tus respuestas nos ayudará a mejorar la atención y el servicio que damos a nuestros niños y niñas</p>
                        <p class="text-center"><a href="https://forms.office.com/r/TdrTUKEugi" target="_blank" class="btn btn-danger btn-lg boton_ancho"><font color="#fff">Completar cuestionario</font></a></p>
                </div>
        </div>
</div>       
       <?php 
}


if ($mostrar_inscripcion==true) {
	?>
	<div class="text-center">
		<br><a href="inscripcion_curso.php" class="btn btn-danger btn-lg boton_ancho"><font color="#fff">Inscripción en próximos cursos</font></a><br><br>
	</div>
        <?php 
        }
        if ($mostrar_bono==true) {
        ?>        
	<div class="text-center">
		<br><a href="informacion_alumnos.php" class="btn btn-danger btn-lg boton_ancho"><font color="#fff">Datos para solicitud Bono Infantil</font></a><br><br>
	</div>        
	<?php 
}

// Muestra boton navidad
if ($mostrar_felicitacion_navidad==true) {
?>
<div class="text-center"><br /><button type="button" class="btn btn-primary btn-danger" data-toggle="modal" data-target="#myModalEspecial">¿Quieres ver otra vez la felicitación de Navidad?</button><br /><br /></div>
<?php 
}

// Busca hijos cumple
$timestamp_hoy=time();
                 
    $busca_cumple_hoy=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d")."' AND month(usuarios.fecha_nacimiento)=".date("n",$timestamp_hoy)." AND day(usuarios.fecha_nacimiento)=".date("j",$timestamp_hoy)." ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

        $numero_cumples_hoy=db_num_rows($busca_cumple_hoy);

    if ($numero_cumples_hoy!=0) {
        
        $nombre_cumple="";
        
        $contador_cumples=0;
        
        $sexos_hijos=array();
        $edad_hijo=0;        
        
        while($datos_cumple_hoy=db_fetch_array($busca_cumple_hoy)) {
        
                        if ($datos_cumple_hoy["sexo"]=="V") {
                                $sexos_hijos[0]=1;
                                } else {
                                $sexos_hijos[1]=1;
                                }
        
                        $contador_cumples++;
                        if (($contador_cumples==$numero_cumples_hoy) and ($numero_cumples_hoy>1)) {
                                $nombre_cumple.=" y ";
                                } else {
                                if ($numero_cumples_hoy>1) {
                                        $nombre_cumple.=", ";
                                        }
                                }
                        $nombre_cumple.=ucwords($datos_cumple_hoy["Nombre"]);
                        
            $nacimiento=explode( '-', $datos_cumple_hoy["fecha_nacimiento"]);
            $edad_hijo=date("Y",$timestamp_hoy)-$nacimiento[0];
            $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y",$timestamp_hoy));
            $hoy=strtotime(date("Y/m/d",$timestamp_hoy));
            if (($cumple-$hoy)>0) {
                                $edad_hijo=$edad_hijo-1;
                                }
                        
                        }
                        
        $coletilla_n="";        
        $coletilla_hijos="";
        $coletilla_sexo="o";        
        $coletilla_anios="";
        
        if ($numero_cumples_hoy>1) {
                $coletilla_n="n";
                $coletilla_hijos="s";
                }        
        
        if (($sexos_hijos[0]==0) and ($sexos_hijos[1]==1)) {
                $coletilla_sexo="a";
                }
                
        if ($edad_hijo>1) {
                $coletilla_anios="s";
                }
                                
        ?>
<div id="modal" style="border:1px solid #447a00; background-color:#f1e272; font-size:100%; text-align:center; display:none; background:url(/webcam/imagenes/felicitacion_cumple.jpg) no-repeat; width: 726px; height:515px; overflow:hidden;"><div align="right"><input type="button" value="Cerrar Felicitación" onClick="Popup.hide('modal')"></div><table align="center" width=405><tr><td align="center" style="font-size:125%; text-align:center;"><img src="/imagen/nada.gif" width=1 height=250 border=0>Hoy tu<?=$coletilla_hijos?> hij<?=$coletilla_sexo;?><?=$coletilla_hijos?> <?=$nombre_cumple?> cumple<?=$coletilla_n?> <?=$edad_hijo;?> año<?=$coletilla_anios?>.<br /><br />¡¡Felicidades <?=$nombre_cumple?>!!</td></tr></table>
</div>
<p align="center">
        <a href="#" onclick="Popup.showModal('modal');return false;">Hoy tu<?=$coletilla_hijos?> hij<?=$coletilla_sexo;?><?=$coletilla_hijos?> cumple<?=$coletilla_n?> años, mira su felicitación.</a>
</p>
        <?php 
        if ($_SESSION["mostrar_felicitacion"]!=1) {
                ?>
                <script>
                Popup.showModal('modal');
                </script>
                <?php 
                $_SESSION["mostrar_felicitacion"]=1;
                }
        
        }                

if (strlen($texto_alerta)!=0) { 
        echo($texto_alerta);
         } else {
         echo("<br>");
         }

// Día
if (strlen($_GET["dia"])!=0) {
        $fecha_time_stamp=$_GET["dia"];
        } else {
        $fecha_time_stamp=time();
        }
        
// Busca circulares enviadas

/*
$busca_mensajes=mysql_query("SELECT * FROM mensajes_temporal WHERE fecha<='".date("Y-m-d")."' ORDER BY fecha DESC LIMIT 0,1");

$num_circulares=mysql_num_rows($busca_mensajes);

$num_circulares=0;

if ($num_circulares!=0) {
?>
<span class="texto_encabezado"><a href="circulares.php">Circulares</a></span><br />
<?php 
while($mensajes=mysql_fetch_array($busca_mensajes)){
?>
<div class="page80"><span class="subtitulo_encabezado"><?=$mensajes["asunto"];?></span><br /><?=app_text_utf8($mensajes["texto_editor"]);?></div><br />
<?php 
}

}

*/
        
// Agenda de actividades padres

// Busca hijos del padre

   $array_aulas_hijo=array();
   
        $aulas_hijos="";
        $centros_hijos="";
        $aulas_hijos_notas="";

    $fechalimite=$timestamp_hoy;
        
    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {
    
        $contador_hijos=0;
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                $busca_datos_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_hijos["id_curso"]);
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                        } else {
                        $nombre_curso_mostrado="";
                        }
                        
                // Datos de la escuela del niño
                $busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del niño
                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"]);

                $datos_aula=db_fetch_array($busca_datos_aula);

                $array_aulas_hijo[$datos_hijos["aula"]]=$datos_aula["nombre"];

                $aulas_hijos.=" OR (Agenda.id_aula=".$datos_hijos["aula"].")";
                
                $centros_hijos.=" OR (Agenda.id_centro=".$datos_hijos["id_centro"]." AND Agenda.id_aula=0)";
                
                $aulas_hijos_notas.=" OR (agenda_alumnos_seguimiento.id_aula=".$datos_hijos["aula"].")";
                
                $contador_hijos++;
                }
            }

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'");

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

$hay_avisos=false;


$filtro_usuarios=" AND (Agenda.id_centro=0$centros_hijos$aulas_hijos)";

if (strlen($contador_hijos)!=0) {
        $aulas_hijos_notas="(".substr($aulas_hijos_notas,4,strlen($aulas_hijos_notas)-4).")";
        }

// Info Comunidad Whatsapp
?>
<div class="alert alert-dismissible" role="alert" id="alertaApp">
        <div class="panel panel-default">
                <div class="panel-heading">
                        <button type="button" class="close pull-right" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;&nbsp;&nbsp;&nbsp;</span></button>&nbsp;&nbsp;
                </div>
                <div class="panel-body">         
                        <div class="row">
                                <div class="col-xs-12">
                                <p>Hola familias,</p>
                                <p>Queremos informaros que hemos creado una comunidad en WhatsApp para facilitar la comunicación entre vosotros y las escuelas infantiles Lápices.</p>
                                <p>Hemos creado un grupo general de avisos, donde compartiremos información importante sobre eventos y actividades de todas las escuelas, y grupos específicos para cada una de las escuelas (General Avilés, Río Nervión, Gran Vía, Vicente Branchat).</p>
                                <p>Para unirte a los grupos, verificaremos tus números de teléfono en nuestra base de datos, asegurándonos de que toda la comunidad esté formada por padres y familiares autorizados.</p>
                                <p>Únete haciendo clic en en siguiente enlace:</p>
                                <p align="center"><a href="https://chat.whatsapp.com/KtmypA78aaD9Ya7TheChMd" alt="Unirse a la Comunidad de Whatsapp" target="_blank"><img src="/webcam/imagenes/whatsapp.png" width="150"></a></p>
                                <p>Saludos cordiales,<br />El equipo de Lápices Escuelas Infantiles</p>
                                </div>
                        </div>    
                </div>
        </div>
</div>
<?php

// Notas educadora

if (strlen($aulas_hijos_notas)!=0) {
        $busca_mensajes=db_query("SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND agenda_alumnos_seguimiento.id_alumno=0 AND $aulas_hijos_notas ORDER BY agenda_alumnos_seguimiento.dia DESC LIMIT 0,5") or die(db_error());

        $busca_mensajes_todas=db_query("SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND agenda_alumnos_seguimiento.id_alumno=0 AND $aulas_hijos_notas ORDER BY agenda_alumnos_seguimiento.dia DESC") or die(db_error());

        $num_circulares=db_num_rows($busca_mensajes);
        $num_circulares_todas=db_num_rows($busca_mensajes_todas);
        }  else {
        $num_circulares=0;
        $num_circulares_todas=0;
        }

if ($num_circulares!=0) {
?>
<table width=98%  class="tabla_datos" align="center">
<tr><td class="tabla_celda_encabezado"><font size=3><b>Notas de las educadoras</b></font></td></tr>
</table>
<table width=98%  class="tabla_datos" align="center">
<?php 
while($mensajes=db_fetch_array($busca_mensajes)){
?>
<tr valign=top>
    <td width="17%" class="tabla_celda_datos"><font color="<?=$color_cita?>" size=2><b><?=date("d/m/Y",strtotime($mensajes["dia"]));?></b></font></td>
    <td width="83%" class="tabla_celda_datos"><font color="<?=$color_cita?>" size=2><b><?= htmlentities($mensajes["nombre_tipo"],ENT_QUOTES,"ISO8859-1"); ?> - Aula <?=$array_aulas_hijo[$mensajes["id_aula"]];?></b></font></td>
  </tr>
  <tr valign=top>
  <?php /* htmlentities($mensajes["nota"],ENT_QUOTES,"ISO8859-1");  */ ?>
    <td width="100%" colspan="2" class="tabla_celda_datos"><small><small><?=$mensajes["nota"]?></small></small></td>
  </tr>
<?php 
}
if ($num_circulares_todas>$num_circulares) {
?>
<tr><td width="100%" colspan="2" class="tabla_celda_datos" align=center><a href="notas_educadora.php">Hay <?=$num_circulares_todas-$num_circulares?> más... Ver todas las notas...</a></td></tr>
<?php 
}
?>
</table><br />
<?php 
}

// Agenda

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
                        <td class="tabla_celda_datos"colspan=2><font color="#025392"><small><b><?= htmlentities($nombres_escuelas[$rsNot["id_centro"]],ENT_QUOTES,"ISO8859-1"); ?></b></small></font></td>
                </tr>
                <?php 
                }
        if (($aula_actual!=$rsNot["id_aula"]) and ($escuela_actual!=0)) {
                $aula_actual=$rsNot["id_aula"];
                ?>
                <tr valign=top>
                        <td width="100%"  class="tabla_celda_datos" colspan=2><font color="#025392"><small><b><?= htmlentities($nombres_aulas[$rsNot["id_aula"]],ENT_QUOTES,"ISO8859-1"); ?></b></small></font></td>
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
    <td width="83%" class="tabla_celda_datos"><font color="<?=$color_cita?>" size=2><b><?= htmlentities($rsNot["Titulo"],ENT_QUOTES,"ISO8859-1"); ?></b></font></td>
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
    <td width="83%" class="tabla_celda_datos"><small><small><?= nl2br($rsNot["Texto"]); ?></small></small></td>
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
<td width="100%" class="tabla_celda_encabezado" align="left"><font color="<?=$color_cita?>" size=2><b><?=date("d/m/Y",strtotime($rsNot["Fecha"]));?></b></font></td>
</tr>
<?php 
                $fecha_actual_citas=$rsNot["Fecha"];
        }
?>
<tr>
<td width="100%" class="tabla_celda_datos"><font color="<?=$color_cita?>" size=2><b><?=htmlentities($rsNot["Titulo"],ENT_QUOTES,"ISO8859-1");?></b></font></td>
</tr>
<tr>
<td width="100%" class="tabla_celda_datos"><small><small><?= nl2br($rsNot["Texto"]); ?></small></small></td>
</tr>

<?php 
}
?>
</table>
<?php 
}
?>
<p align="center"><a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp-86400; ?>">¿y ayer?</a> - <a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp+86400; ?>">¿y mañana?</a></p></td>
<!--<td width="254"><div class="fb-like-box" data-href="https://www.facebook.com/Escuela-Infantil-Lapices-179065358864516/" data-height="840" data-width="250" data-show-faces="true" data-stream="true" data-header="true"></div></td>-->
</tr>
</table>
</td>
</tr>
</table>
    <?php 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres_modal.php"); ?>
