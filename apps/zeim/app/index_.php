<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
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
        <li class="active"><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQU? EL CONTENIDO -->
<?php
/*
if ($_SERVER['HTTP_HOST']!="santpau.escuelas-infantiles.com") {
?>
<div class="alert alert-dismissible" role="alert" id="alertaApp">
        <div class="panel panel-default">
                <div class="panel-heading">
                        <button type="button" class="close pull-right" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;&nbsp;&nbsp;&nbsp;</span></button>&nbsp;&nbsp;
                </div>
                <div class="panel-body">         
                        <div class="row">
                                <div class="col-xs-12 text-center">
                                        <strong>Ya est? disponible la aplicaci?n para m?viles para IOS y Andorid</strong><br />
                                        Desde ella podr?s acceder a todas la informaci?n aqu? mostrada y adem?s recibir notificaciones cuando recibas alg?n mensaje o informaci?n.<br /><br />
                                        Toca el bot?n que corresponda con el sistema de tu smartphone para instalarla.<br /><br />
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-md-6 col-xs-12 text-center"><a target="_blank"  href="https://apps.apple.com/app/zeim-escuelas-infantiles/id6670217277"><img style="max-width:150px;" src="/app/imagenes/disponible_app_store.png"></a></div>
                                <div class="col-md-6 col-xs-12 text-center"><a target="_blank" href="https://play.google.com/store/apps/details?id=com.escuelasinfantileslapices.agenda"><img style="max-width:150px;" src="/app/imagenes/disponible_google_play.png"></a></div>
                        </div>
                        <div class="row">
                                <div class="col-xs-12 text-center">
                                        <br /><a href="/app/instrucciones_app.php" class="btn btn-primary" style="text-decoration: none;">Instrucciones b?sicas</a>
                                </div>
                        </div>        
                </div>
        </div>
</div>
<?php
}
*/
?>
<?php        

if ($errores=="") {
?>
  <div class="container">
    <div class="row"> <!-- Contenido -->
    	<div class="col-md-10"> <!-- Columna principal -->
    	<?php
// Inscripci?n cursos...
$abiertaInscripcion=false;

if ($id_usuario==10200) {
        // Usuario DEMO prueba de la inscripción
        $abiertaInscripcion=true;
}

if ($abiertaInscripcion==true) {
        ?>
<div class="alert alert-dismissible" role="alert" id="alertaApp">
        <div class="panel panel-default">
                <div class="panel-heading">
                        <h3 class="panel-title pull-left">Inscripci?n pr?ximo curso</h3>
                        <button type="button" class="close pull-right" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;&nbsp;&nbsp;&nbsp;</span></button>&nbsp;&nbsp;
                </div>
                <div class="panel-body text-center">
                        Ya están abiertas las inscripciones para el próximo curso.<br /><br />
                        Haz clic en el botón para acceder al formulario.
                        <br /><br />
                        <a href="/app/inscripcion_curso.php" class="btn btn-danger btn-md"><font color="#fff">Inscripción en próximos cursos</font></a><br><br>
        
                </div>
        </div>
</div>
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
<p align="center">
        Hoy <?=$nombre_cumple?> cumple<?=$coletilla_n?> <?=$edad_hijo;?> a?o<?=$coletilla_anios?>.<br /><br />??Felicidades <?=$nombre_cumple?>!!</p>
        <?php
        
        }                


// D?a
if (strlen($_GET["dia"])!=0) {
        $fecha_time_stamp=$_GET["dia"];
        } else {
        $fecha_time_stamp=time();
        }
        
// Agenda de actividades padres

// Busca hijos del padre
$contador_hijos=0;
$array_aulas_hijo=array();
$array_hijos=array();
$aulas_hijos="";
$centros_hijos="";
$centros_hijos_consultas=array();
$query_hijos_consultas=array();
$aulas_hijos_notas="";
$aulas_hijos_filtro_notas="";

$busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)))) or die(db_error()."- No se encuentran los datos del alumno.");

$mostrarEvaluaciones=false;
$linksEvaluaciones = "";

if (db_num_rows($busca_datos_alumno)!=0) {
            
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                $busca_datos_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_hijos["id_curso"]));
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                } else {
                        $nombre_curso_mostrado="";
                }
                        
                // Datos de la escuela del ni?o
                $busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_hijos["id_centro"]));

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del ni?o
                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

                $datos_aula=db_fetch_array($busca_datos_aula);

                $array_aulas_hijo[$datos_hijos["aula"]]=$datos_aula["nombre"];
                $aulas_hijos.=" OR (Agenda.id_aula=".$datos_hijos["aula"].")";                
                $centros_hijos.=" OR (Agenda.id_centro=".$datos_hijos["id_centro"]." AND Agenda.id_aula=0)";  
                $centros_hijos_consultas[]="(id_centro=".$datos_hijos["id_centro"].")";   
                $query_hijos_consultas[]="(id_alumno=".$datos_hijos["padre_de"].")";              
                $aulas_hijos_notas.=" OR (agenda_alumnos_seguimiento.id_aula=".$datos_hijos["aula"].")";
                $aulas_hijos_filtro_notas.=" OR (agenda_alumnos_seguimiento.id_alumno=".$datos_hijos["id_alumno"].")";
                $array_hijos[$contador_hijos][0]=$datos_hijos["padre_de"];
                $array_hijos[$contador_hijos][1]=$datos_hijos["id_alumno"];
                $array_hijos[$contador_hijos][2]=$datos_hijos["id_centro"];
                $array_hijos[$contador_hijos][3]=$datos_hijos["aula"];
                $array_hijos[$contador_hijos][4]=$datos_hijos["Nombre"]; 
                $array_hijos[$contador_hijos][5]=$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"];                                
                $contador_hijos++;

                // busca evaluaciones
                $num_evaluaciones=0;
                                $mostrar_resto_evaluaciones_trim=array();
                                $mostrar_resto_evaluaciones=false;

                // Ordinaria
                $busca_evaluaciones=db_query_params('SELECT * FROM agenda_programacion_evaluaciones_estado WHERE id_aula=? AND estado=1', 's', array($datos_hijos["aula"]));
                
                while($evaluaciones=db_fetch_array($busca_evaluaciones)){
                        $num_evaluaciones++;
                        $mostrar_resto_evaluaciones=true;
                        $mostrar_resto_evaluaciones_trim[$evaluaciones["trimestre"]]=true;
                        $trimestre_numeral="er";

                        if ($evaluaciones["trimestre"]==2) {
                                $trimestre_numeral="?";
                                }
                        $linksEvaluaciones.='<a class="btn btn-responsive btn-primary btn-sm btn-margin" href="/app/evaluacion.php?id_aula='.$datos_hijos["aula"].'&id_curso='.$datos_hijos["id_curso"].'&id_alumno='.$datos_hijos["id_alumno"].'&trimestre='.$evaluaciones["trimestre"].'">Evaluaci?n '.$evaluaciones["trimestre"].$trimestre_numeral.' Trimestre de '.$datos_hijos["Nombre"].'</a><br />';
                }

        }
}

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                        // Busca el ultimo curso finalizado
                        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_fin<=? ORDER BY fecha_fin DESC LIMIT 0,1', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));
                        if (db_num_rows($busca_curso_actual)!=0) {
                                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                                $id_curso_actual=$datos_curso_actual["id_curso"];
                        } else {
                                // No hay curso actual
                                $datos_curso_actual=array();
                                $id_curso_actual=0;
                        }       
                }

$hay_avisos=false;


$filtro_usuarios=" AND (Agenda.id_centro=0$centros_hijos$aulas_hijos)";

if ((strlen($contador_hijos)!=0) and (strlen($aulas_hijos_notas)!=0)) {
        $aulas_hijos_notas="(".substr($aulas_hijos_notas,4,strlen($aulas_hijos_notas)-4).")";
        $aulas_hijos_filtro_notas="(".substr($aulas_hijos_filtro_notas,4,strlen($aulas_hijos_filtro_notas)-4).")";        
        }

// Muestra encuesta asistencia vacaciones si disponible
$hayEncuestas = false;

$buscaEncuestas = db_query("SELECT * FROM consultas_padres WHERE (".implode(" OR ",$centros_hijos_consultas).") AND fecha_inicio<='".date('Y-m-d')."' AND fecha_fin>='".date('Y-m-d')."';");

if (db_num_rows($buscaEncuestas)!=0) {
        $hayEncuestas = true;

        while($encuestas=db_fetch_array($buscaEncuestas)) {

                // Comprueba si hay respuesta de los hijos
                $buscaResupuestas = db_query("SELECT * FROM consultas_padres_respuestas WHERE id_consulta=".$encuestas["id_consulta"]." AND (".implode(" OR ",$query_hijos_consultas).");");

                if (db_num_rows($buscaResupuestas)<count($array_hijos)) {                                
                ?>
<form name="encuestas" id="encuestas" action="/app/guardar_encuesta.php" method="POST"><?php if(function_exists("app_csrf_token")){ ?><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><?php } ?>
        <input type="hidden" name="id_encuesta" value="<?=$encuestas["id_consulta"];?>">
        <input type="hidden" name="numero_hijos" value="<?=count($array_hijos);?>">
        <div class="alert alert-dismissible" role="alert" id="alertaEncuetas" style="padding-right: 0px!important;padding: 0px!important;margin-bottom:0px!important;">
                <div class="panel panel-default">
                        <div class="panel-heading">
                                <h3 class="panel-title pull-left"><?=security_escape($encuestas["nombre"])?></h3>
                                <button type="button" class="close pull-right" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;&nbsp;&nbsp;&nbsp;</span></button>&nbsp;&nbsp;
                        </div>
                        <div class="panel-body text-center">
                                <div class="text-left"><?=$encuestas["texto_presentacion"]?></div>
                                <?php
                                for ($c_hijos=0;$c_hijos<count($array_hijos);$c_hijos++) {                        
                                        // Comprueba si hay respuesta de este hijo
                                        $buscaResupuestasHijo = db_query("SELECT * FROM consultas_padres_respuestas WHERE id_consulta=".$encuestas["id_consulta"]." AND (id_alumno=".$array_hijos[$c_hijos][0].");");

                                        if (db_num_rows($buscaResupuestasHijo)==0) {   
                                ?>
                                <div class="row text-left border grupo">
                                        <input type="hidden" name="id_hijo_<?=$c_hijos?>" value="<?=$array_hijos[$c_hijos][0]?>">
                                        <div class="col-md-4">
                                                <b>Alumno:</b> <?=$array_hijos[$c_hijos][4]." ".$array_hijos[$c_hijos][5]?>
                                        </div>
                                        <div class="col-md-4">
                                        <label for="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>_1" class="control-label">?Asistir? a la escuela durante estos d?as?</label>
                                        <?php
                                        if ($encuestas["numero_respuestas_posibles"]==3) {
                                        ?>        
                                        <label class="radio-inline">
                                                <input type="radio" name="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>" id="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>_1" value="1"> No asistir?
                                        </label>
                                        <label class="radio-inline">
                                                <input type="radio" name="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>" id="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>_2" value="2"> Asistir? todos los d?as
                                        </label>
                                        <label class="radio-inline">
                                                <input type="radio" name="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>" id="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>_3" value="3"> Asistir? los siguientes d?as (indicar en el cuadro de texto siguiente)
                                        </label>
                                        <?php
                                        }
                                        if ($encuestas["numero_respuestas_posibles"]==2) {
                                        ?>        
                                        <label class="radio-inline">
                                                <input type="radio" name="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>" id="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>_1" value="1"> No asistir?
                                        </label>
                                        <label class="radio-inline">
                                                <input type="radio" name="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>" id="asiste_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>_2" value="2"> Asistir?
                                        </label>
                                        <?php
                                        }                                        
                                        ?>                                
                                        </div>
                                        <div class="col-md-4">
                                                <label for="horario_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>" class="control-label"><?php
                                        if ($encuestas["numero_respuestas_posibles"]==3) {
                                        ?>D?as que asistir? y horario que realizar?<?php } else { ?>Horario que realizar?<?php } ?></label>
                                                <textarea type="text" class="form-control" id="horario_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>" name="horario_<?=$encuestas["id_consulta"]?>_<?=$array_hijos[$c_hijos][0]?>"></textarea>
                                        </div>                                                                
                                </div>
                                <?php
                                        }
                                }
                                ?>
                                <br /><button type="button" id="validarBtn" class="btn btn-primary">Enviar respuestas</button>
                        </div>
                </div>
        </div>
</form>                                             
                <?php
                }
        }        
}
// Agenda de hoy disponible (de lunes a viernes a las 17:00)
if ((date("N")<=5) and (date("G")>=17)) {
?>
<div class="alert alert-dismissible" role="alert" id="alertaApp">
        <div class="panel panel-default">
                <div class="panel-heading">
                        <h3 class="panel-title pull-left">Ya est? disponible el informe de hoy</h3>
                        <button type="button" class="close pull-right" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;&nbsp;&nbsp;&nbsp;</span></button>&nbsp;&nbsp;
                </div>
                <div class="panel-body text-center">
<?php
$inicio_time_stamp=strtotime(date('Y-m-d'));
for ($c_hijos=0;$c_hijos<count($array_hijos);$c_hijos++) {

        // Valores predeterminados 
        $mostrar_informe=false;
        $mensaje_informe=$array_hijos[$c_hijos][4]." no asiste a la escuela el d?a ".date("d/m/Y",$inicio_time_stamp)." o el informe no est? disponible para esta fecha (festivos, vacaciones, etc).";
        $color_boton="#7b3535";

        // Comprueba asistencia        
        $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$array_hijos[$c_hijos][0]." AND id_servicio=".$array_hijos[$c_hijos][3]." AND fecha='".date("Y-m-d",$inicio_time_stamp)."'") or die(db_error());
        if (db_num_rows($busca_registro_entrada)!=0) {
                $mostrar_informe=true;
                $color_boton="#2e921d";
        }

        // Comprueba si es hoy y si se puede mostrar (a partir de las 17:00)

        $busca_mensualidad=db_query("SELECT * FROM alumnos WHERE id_alumno=".$array_hijos[$c_hijos][1]);

        $hora_limite=17;

        if (db_num_rows($busca_mensualidad)!=0) {
                $datos_del_cliente=db_fetch_array($busca_mensualidad);

                if ($datos_del_cliente["tipo_mensualidad"]==1) {
                        // Media jornada
                        if ($datos_del_cliente["comedor"]!=2) {
                                // Se queda a comer
                                $hora_limite=15;
                                } else  {
                                // No se queda a comer
                                $hora_limite=12;
                        }
                }

        } 

        if ($id_usuario!=10200) { // Solo usuario DEMO puede verse el informe antes de las 17:00       
                if ((date("m/d/Y",$inicio_time_stamp)==date("m/d/Y")) and (date("G")<$hora_limite)) {
                        $mostrar_informe=false;
                        $mensaje_informe="El informe de hoy para ".$array_hijos[$c_hijos][4]." estar? disponible a partir de las ".$hora_limite.":00 hrs.";
                        $color_boton="#0000ff";
                }
        }


        if ($mostrar_informe==true) {
                // comprueba si leido
                $busca_lectura=db_query("SELECT * FROM agenda_registro_lecturas WHERE dia_informe='".date("Y-m-d",$inicio_time_stamp)."' AND id_padre=$id_usuario AND id_alumno=".$array_hijos[$c_hijos][0]);

                if (db_num_rows($busca_lectura)!=0) {
                        $color_boton="#ff9600";
                }

                ?><button type="button" id="boton_<?=$inicio_time_stamp?>" onclick="document.getElementById('boton_<?=$inicio_time_stamp?>').style.color='#ff9600';window.location.href='/app/agenda/informe_dia_html.php?dia=<?=$inicio_time_stamp;?>&a=<?=$array_hijos[$c_hijos][0]?>';" style="font-size: 18px; color: <?=$color_boton?>;padding:10px;margin-bottom:20px;">Ver informe de <?=$array_hijos[$c_hijos][4]?></button><br /><?php
        } else {
                ?><?=$mensaje_informe;?><br /><?php
        }
}
        ?>
                </div>
        </div>
</div>
<?php
}

// Alerta evalaci?n
if (($linksEvaluaciones!='') and ($mostrarEvaluaciones==true)) {
?>
<div class="alert alert-dismissible" role="alert" id="alertaApp">
        <div class="panel panel-default">
                <div class="panel-heading">
                        <h3 class="panel-title pull-left">Ya est?n disponibles las evaluaciones trimestrales</h3>
                        <button type="button" class="close pull-right" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;&nbsp;&nbsp;&nbsp;</span></button>&nbsp;&nbsp;
                </div>
                <div class="panel-body text-center">
                        Haz clic en el bot?n para acceder a las evaluaciones.<br /><br />
                        <?=$linksEvaluaciones?>        
                </div>
        </div>
</div>
<?php
}
// FIN Evaluaci?n

// Notas educadora
if (strlen($aulas_hijos_notas)!=0) {

  $consultaMensajes = "SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND (agenda_alumnos_seguimiento.id_alumno=0 OR ($aulas_hijos_filtro_notas)) AND $aulas_hijos_notas ORDER BY agenda_alumnos_seguimiento.dia DESC,agenda_alumnos_seguimiento.hora DESC LIMIT 0,5";

  $busca_mensajes=db_query($consultaMensajes) or die(db_error()." - 1");

  $consultaMensajesTodas = "SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND (agenda_alumnos_seguimiento.id_alumno=0 OR ($aulas_hijos_filtro_notas)) AND $aulas_hijos_notas ORDER BY agenda_alumnos_seguimiento.dia DESC,agenda_alumnos_seguimiento.hora DESC";

  $busca_mensajes_todas=db_query($consultaMensajesTodas) or die(db_error()." - 2");

  $num_circulares=db_num_rows($busca_mensajes);
  $num_circulares_todas=db_num_rows($busca_mensajes_todas);
  }  else {
  $num_circulares=0;
  $num_circulares_todas=0;
  }

if ($num_circulares!=0) {
?>
<h3>Notas de las educadoras</h3>
<?php
while($mensajes=db_fetch_array($busca_mensajes)){

        $nombre_alumno="";
        if ($mensajes['id_alumno']!=0) {
                // Muestra nombre del alumno al que se refiere la nota
                $busca_nombre_alumno=db_query_params('SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($mensajes['id_alumno']));
                if (db_num_rows($busca_nombre_alumno)!=0) {
                $datos_nombre_alumno=db_fetch_array($busca_nombre_alumno);  
                $nombre_alumno=" ".date("H:i",strtotime($mensajes['hora'])) . " - ". $datos_nombre_alumno["Nombre"]." ".$datos_nombre_alumno["Apellido1"]." ".$datos_nombre_alumno["Apellido2"];
                }   
        }

?>
<div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title"><b><?=date("d/m/Y",strtotime($mensajes["dia"]));?><?=$nombre_alumno?></b></h3>                          
      <?= $mensajes["nombre_tipo"]; ?> - Aula <?=$array_aulas_hijo[$mensajes["id_aula"]];?>
    </div>
    <div class="panel-body">
    <?=security_html($mensajes["nota"])?>
    </div>
</div>
<?php
}
if ($num_circulares_todas>$num_circulares) {
?>
<a href="notas_educadora.php">Hay <?=$num_circulares_todas-$num_circulares?> m?s... Ver todas las notas...</a><br /><br />
<?php
}
?>
<br />
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
                        <td class="tabla_celda_datos"colspan=2><font color="#025392"><small><b><?= $nombres_escuelas[$rsNot["id_centro"]]; ?></b></small></font></td>
                </tr>
                <?php
                }
        if (($aula_actual!=$rsNot["id_aula"]) and ($escuela_actual!=0)) {
                $aula_actual=$rsNot["id_aula"];
                ?>
                <tr valign=top>
                        <td width="100%"  class="tabla_celda_datos" colspan=2><font color="#025392"><small><b><?= $nombres_aulas[$rsNot["id_aula"]]; ?></b></small></font></td>
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
    <td><span class="text-primary"><b><?php if (date("H:i",strtotime($rsNot["Hora"]))!="00:00") { echo(date("H:i",strtotime($rsNot["Hora"]))." - "); } ?></b> <b><?= $rsNot["Titulo"]; ?></b><span></td>
  </tr>
  <tr>
    <td><?= $rsNot["Texto"]; ?></td>
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
<h4 class="text-center">En los pr?ximos d?as</h4>
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
<td><span class="text-primary"><b><?=$rsNot["Titulo"];?></b></span></td>
</tr>
<tr>
<td><?= $rsNot["Texto"]; ?></td>
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
<p align="center"><a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp-86400; ?>">?y ayer?</a> - <a href="<?=$_SERVER["PHP_SELF"]?>?dia=<?= $fecha_time_stamp+86400; ?>">?y ma?ana?</a></p>
    	
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