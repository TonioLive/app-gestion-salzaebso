<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        &nbsp;
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="/clientes"><i class="fa fa-home"></i> Inicio</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
	  
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.10";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php     
if ($errores=="") {
?>
  <div class="container">
    <div class="row"> <!-- Contenido -->
    	<div class="col-md-6"> <!-- Columna principal -->
    	
    	<?php 
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
                                <p>Queremos informaros que hemos creado una comunidad en WhatsApp para facilitar la comunicación entre vosotros y la Piscina de Neokis.</p>
                                <p>Hemos creado un grupo donde compartiremos información importante sobre eventos y actividades de toda la piscina.</p>
                                <p>Para unirte a los grupos, verificaremos tus números de teléfono en nuestra base de datos, asegurándonos de que toda la comunidad esté formada por padres y familiares autorizados.</p>
                                <p>Únete haciendo clic en en siguiente enlace:</p>
                                <p align="center"><a href="https://chat.whatsapp.com/IBlFKU5rYdZCy33adRLM76" alt="Unirse a la Comunidad de Whatsapp" target="_blank"><img src="/clientes/imagenes/whatsapp.png" width="150"></a></p>
                                <p>Saludos cordiales,<br />La Piscina de Neokids</p>
                                </div>
                        </div>    
                </div>
        </div>
</div>
<?php

    // Mostramos bienvenida
    print "<div class='text-center'><h3>¡Hola! ";
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

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i></h4></div>";

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
<p align="center">
        Hoy <?=$nombre_cumple?> cumple<?=$coletilla_n?> <?=$edad_hijo;?> año<?=$coletilla_anios?>.<br /><br />¡¡Felicidades <?=$nombre_cumple?>!!</p>
        <?php 
        
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
<?
while($mensajes=mysql_fetch_array($busca_mensajes)){
?>
<div class="page80"><span class="subtitulo_encabezado"><?=$mensajes["asunto"];?></span><br /><?=utf8_decode($mensajes["texto_editor"]);?></div><br />
<?
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
        
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)))) or die(db_error()."- No se encuentran los datos del alumno.");

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

                $array_aulas_hijo[$datos_hijos["aula"]]=$datos_aula["nombre"];

                $aulas_hijos.=" OR (Agenda.id_aula=".$datos_hijos["aula"].")";
                
                $centros_hijos.=" OR (Agenda.id_centro=".$datos_hijos["id_centro"]." AND Agenda.id_aula=0)";
                
                $aulas_hijos_notas.=" OR (agenda_alumnos_seguimiento.id_aula=".$datos_hijos["aula"].")";
                
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

if (strlen($contador_hijos)!=0) {
        $aulas_hijos_notas="(".substr($aulas_hijos_notas,4,strlen($aulas_hijos_notas)-4).")";
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
<div class="box box-primary"> <!-- Contenido -->    
	<div class="box-body no-padding"> <!-- Inicio fondo blanco -->
		<h4 class="text-center">Agenda de actividades - <?=date("d/m/Y",$fecha_time_stamp)?></h4>
<?php 
//Encabezado
?>
<table class="table table-bordered table-condensed">
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
  <tr>
    <td><span class="text-primary"><b><?php  if (date("H:i",strtotime($rsNot["Hora"]))!="00:00") { echo(date("H:i",strtotime($rsNot["Hora"]))." - "); } ?></b> <b><?= htmlentities($rsNot["Titulo"]); ?></b><span></td>
  </tr>
  <tr>
    <td><?= htmlentities($rsNot["Texto"]); ?></td>
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
    <td colspan="2" class="text-center"><span class="text-primary">No <?=$hay_o_habia?> actividades programadas para <?=$hoy_o_dia?></span></td>
  </tr>
<?php 
}
?>
</table>
    </div>
</div>    		
<?php 
// Consultamos citas de la agenda

$fecha_avisos=date("Y-m-d",$fecha_time_stamp);

$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND Agenda.avisar<='".$fecha_avisos."' AND Agenda.Fecha>'".$fecha_avisos."' AND agenda_asistentes.nivel=1$filtro_usuarios GROUP BY Agenda.id ORDER BY Agenda.Fecha,Agenda.Hora");

if (db_num_rows($rsNot_query) != 0)
{
?>
<h4 class="text-center">En los próximos días</h4>
<div class="box box-primary"> <!-- Contenido -->    
	<div class="box-body no-padding"> <!-- Inicio fondo blanco -->
<table class="table table-bordered table-condensed">
<?php 
$fecha_actual_citas="";
while($rsNot=db_fetch_array($rsNot_query)) {

$dia_cita_timestamp=strtotime($rsNot["Fecha"]);
$color_cita="#025392";

if ($fecha_actual_citas!=$rsNot["Fecha"]) {
?>
<tr>
<td><span class="text-primary"><b><?=date("d/m/Y",strtotime($rsNot["Fecha"]));?></b></span></td>
</tr>
<?php 
                $fecha_actual_citas=$rsNot["Fecha"];
        }
?>
<tr>
<td><span class="text-primary"><b><?=htmlentities($rsNot["Titulo"]);?></b></span></td>
</tr>
<tr>
<td><?= htmlentities($rsNot["Texto"]); ?></td>
</tr>

<?php 
}
?>
</table>
	</div>
</div>		
<?php 
}
?>
<p align="center"><a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp-86400; ?>">¿y ayer?</a> - <a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp+86400; ?>">¿y mañana?</a></p>
    	
    	</div>	
    	<div class="col-md-6"> <!-- Columna redes sociales -->
    	<h4>Búscanos en Facebook</h4>
    	<div class="fb-page" data-href="https://www.facebook.com/pages/La-piscina-de-Neokids/866745676682816" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/pages/La-piscina-de-Neokids/866745676682816" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/pages/La-piscina-de-Neokids/866745676682816">La piscina de Neokids</a></blockquote></div>
    	<br>
    	</div>    	
    </div>    
  </div>
    <?php 
    }
    ?></div>
	  
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>