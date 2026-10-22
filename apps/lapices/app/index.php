<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php"); 	

if (empty($_SESSION['permitido_padre'])) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}
	
?>
<?php
$familias_hora_actual=(int) date("G");
if ($familias_hora_actual>=22 || $familias_hora_actual<5) {
    $familias_saludo="Buenas noches";
} elseif ($familias_hora_actual>=15) {
    $familias_saludo="Buenas tardes";
} else {
    $familias_saludo="Buenos d&iacute;as";
}
$familias_nombre_inicio=htmlspecialchars(mb_convert_case($datos_padre["Nombre"], MB_CASE_TITLE), ENT_QUOTES, "UTF-8");
$familias_inicial=htmlspecialchars(mb_substr(mb_convert_case($datos_padre["Nombre"], MB_CASE_UPPER), 0, 1), ENT_QUOTES, "UTF-8");
$familias_dias=array("domingo","lunes","martes","mi&eacute;rcoles","jueves","viernes","s&aacute;bado");
$familias_meses=array(1=>"enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$familias_fecha_hoy=ucfirst($familias_dias[(int) date("w")]).", ".date("j")." de ".$familias_meses[(int) date("n")]." de ".date("Y");
?>  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper familias-home">
    <section class="content-header familias-home-hero">
      <div class="familias-home-hero__copy">
        <span class="familias-home-hero__eyebrow">Tu espacio familiar</span>
        <h1><?=$familias_saludo?>, <?=$familias_nombre_inicio?></h1>
        <p><?=$familias_fecha_hoy?>. Consulta de un vistazo las novedades, la agenda y la documentaci&oacute;n del centro.</p>
      </div>
      <div class="familias-home-hero__avatar" aria-hidden="true"><?=$familias_inicial?></div>
    </section>
    <!-- Main content -->
    <section class="content familias-home__content">
      <nav class="familias-quick-grid" aria-label="Accesos r&aacute;pidos">
        <a class="familias-quick-card" href="/app/mensajeria.php">
          <span class="familias-quick-card__icon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
          <span class="familias-quick-card__copy"><strong>Mensajes</strong><small>Habla con el centro y revisa las respuestas.</small></span>
          <?php if ($total_no_leidos>0) { ?><span class="familias-quick-card__badge"><?=$total_no_leidos?></span><?php } ?>
        </a>
        <a class="familias-quick-card" href="/app/agenda.php">
          <span class="familias-quick-card__icon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
          <span class="familias-quick-card__copy"><strong>Agenda</strong><small>Consulta actividades, festivos y fechas importantes.</small></span>
        </a>
        <a class="familias-quick-card" href="/app/documentos.php">
          <span class="familias-quick-card__icon"><i class="fa fa-folder-open" aria-hidden="true"></i></span>
          <span class="familias-quick-card__copy"><strong>Documentos</strong><small>Circulares, evaluaciones, im&aacute;genes y archivos.</small></span>
          <?php if ($contador_documentos>0) { ?><span class="familias-quick-card__badge"><?=$contador_documentos?></span><?php } ?>
        </a>
        <a class="familias-quick-card" href="/app/recibos.php">
          <span class="familias-quick-card__icon"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
          <span class="familias-quick-card__copy"><strong>Recibos</strong><small>Revisa el estado de tus cuentas y recibos emitidos.</small></span>
        </a>
      </nav>
      <?php require __DIR__ . '/avisos_inicio.php'; ?>
	  <!-- / AQUÍ EL CONTENIDO -->
<?php     
if ($errores=="") {
?>
  <div class="container">
    <div class="row"> <!-- Contenido -->
    	<div class="col-md-6"> <!-- Columna principal -->
    	
    	<?php 
    // Mostramos bienvenida
    print "<div class='text-center legacy-home-greeting'><h3>¡Hola! ";
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
<p class="familias-day-nav"><a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp-86400; ?>">¿y ayer?</a> - <a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp+86400; ?>">¿y mañana?</a></p>
    	
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