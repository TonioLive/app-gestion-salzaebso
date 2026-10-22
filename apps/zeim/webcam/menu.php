<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

$mostrar_mensajes=true;
$mostrar_documentacion=true;
$mostrar_agenda=true;

$fechalimite=strtotime(date("Y-m-d"))+(86400*15);

    // Busca hijos del padre
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? AND alumnos.fecha_matricula<=? ORDER BY alumnos.id_curso DESC', 'sss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)), (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        while($datos_hijos=db_fetch_array($busca_datos_alumno)){

                $error_documentos="";
                         
                // Busca carpetas aulas
                        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($datos_hijos["aula"]));
                        $datos_aulas=db_fetch_array($busca_aulas);
                        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_hijos["id_centro"]));
                        $datos_escuelas=db_fetch_array($busca_escuelas);

                        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
                        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
                        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
                        $nombre_alumno=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]."-ID".$datos_hijos["id_alumno"];
                        $descarga_docs_path="/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres";
						$descarga_docs_path1="/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno/padres";
								
						$sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');						
						$descarga_docs_path1=str_replace($sustituir,"_",$descarga_docs_path1);
				
                        $descarga_centro_path="/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/padres";
                                $contador_documentos=0;       
                                 
if (file_exists($descarga_docs_path)) {         
                                                
                                $d = dir($descarga_docs_path);

                              while($s=$d->read()) {

                                if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) {

                                                // Busca si leido
                                                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_docs_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                                                if (db_num_rows($busca_leido)==0) {
                                                        $contador_documentos++;
                                                        }
                                        }
                                      }
                                $d->close();
                                        } else {
                $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del aula. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                }
                                        
 if (file_exists($descarga_docs_path1)) { 
                         
                $d = dir($descarga_docs_path1);

                              while($s=$d->read()) {

                                if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) {

                                                // Busca si leido
                                                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_docs_path1) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                                                if (db_num_rows($busca_leido)==0) {
                                                        $contador_documentos++;
                                                        }
                                        }
                                      }
                                $d->close();
        } else {
                $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación de su hijo/a. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                }
 if (file_exists($descarga_centro_path)) {         
                $d = dir($descarga_centro_path);

                              while($s=$d->read()) {

                                if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) {

                                                // Busca si leido
                                                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_centro_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                                                if (db_num_rows($busca_leido)==0) {
                                                        $contador_documentos++;
                                                        }
                                        }
                                      }
                                $d->close(); 
                                } else {
                $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del centro. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                }       
                }
                 
        }


		if ($mostrar_mensajes==true) {

// Busca padres
    $filtro_padres="";
    $busca_padres=db_query_params('SELECT * FROM padres WHERE id_usuario=?', 's', array($id_usuario));
    while($padres=db_fetch_array($busca_padres)){
            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
            }
    if ($filtro_padres!="") {
            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
            }


    // Busca mensajes
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $subtipos_mensajes=array(" a la educadora"," a la coordinadora");
        $estados_mensajes=array("No leido","Leido","Cerrado");
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) ORDER BY fecha DESC, hora DESC");

    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) AND tipo_nota=2 AND ((fecha>='2018-01-31' AND leido_por NOT like '%[".$id_usuario."]%') OR (fecha<'2018-01-31' AND estado=0)) ORDER BY fecha DESC");
    $texto_no_leidos="";
    $total_no_leidos=db_num_rows($busca_no_leidos);
    
    // Busca respuestas
    while($mensajes=db_fetch_array($busca_mensajes)) {
		
            $busca_respuestas=db_query_params('SELECT * FROM agenda_notas_padres_respuestas WHERE direccion=1 AND id_nota=? AND respondido_por<>? AND ((fecha_respuesta>=\'2018-01-31\' AND leido_por NOT like ?) OR (fecha_respuesta<\'2018-01-31\' AND leida=0))', 'sss', array($mensajes["id_nota_padre"], $id_usuario, '%[' . (string)($id_usuario) . ']%'));

            if (db_num_rows($busca_respuestas)!=0) {
                    $total_no_leidos++;
                }
            }
    $texto_alerta1="";
        $alineacion_texto="middle";
    if ($total_no_leidos!=0) {
            $coletilla_leido="";
            $alineacion_texto="left";
            if ($total_no_leidos>1) {
                    $coletilla_leido="s";
                    }
            $texto_no_leidos="<br /><small>(<font color='#f00'>".$total_no_leidos." no leido$coletilla_leido</font>)</small>";
            $texto_alerta1=" ($total_no_leidos)";
            }
	}    
	
	
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
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

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";
    ?>
        <table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
        <tr><td><img src="/webcam/imagenes/encabezado_principal.gif" border=0></tr></td>
        <tr><td><table width=100% border=2 bordercolor="c4cf2b">
        <tr valign="top"><td align="center" width="50%">
                    <a href="inscripcion_curso.php" class="btn btn-danger btn-lg boton_ancho"<i class="fa fa-user-plus" aria-hidden="true"></i>Inscripción próximo curso </a>
					<br /><br />
          <a href="informacion_alumnos.php" class="btn btn-danger btn-lg boton_ancho"<i class="fa fa-user-plus" aria-hidden="true"></i>Datos solicitud Bono Infantil</a>
					<br /><br />          
					<br />
                    <a href="agenda.php" class="btn btn-success btn-lg boton_ancho"><i class="fa fa-calendar"></i> Agenda</a>
					<br /><br />	
                    <a href="mensajeria.php" class="btn btn-success btn-lg boton_ancho"><i class="fa fa-envelope"></i> Mensajes educadora<?=$texto_alerta1?></a>
					<br /><br />					
					<a href="galerias.php" class="btn btn-success btn-lg boton_ancho"><i class="fa fa-file-image-o"></i> Galerías de Fotos</a>
					<br /><br />
										
                    <a href="principal.php" class="btn btn-success btn-lg boton_ancho"><i class="fa fa-video-camera"></i> Webcam</a>
					<br /><br />
                    <a href="comedor.php" class="btn btn-success btn-lg boton_ancho"><i class="fa fa-calendar"></i> Calendario de comedor</a>
					<br /><br />
                    <a href="/webcam/facturas.php" class="btn btn-success btn-lg boton_ancho"><i class="fa fa-money"></i> Ver facturas</a>
					<br /><br />
            <button type="button" class="btn btn-success btn-lg boton_ancho" data-toggle="modal" data-target="#myModal1">
  				<i class="fa fa-envelope"></i> Ver todos los mensajes
			</button>
					<br /><br />
                    <a href="/configuracion/cerrar_sesion_padres.php" class="btn btn-info btn-lg boton_ancho"><i class="fa fa-sign-out"></i> Salir</a>
<br /><br />
        </td>
        <td align="center" width="50%">
        <?php
        
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
    <td width="100%" colspan="2" class="tabla_celda_datos"><small><small><?= htmlentities(security_html($mensajes["nota"]),ENT_QUOTES,"ISO8859-1"); ?></small></small></td>
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
?>
        </td></tr>
        </table>
        </td></tr>
        <tr><td><img src="/webcam/imagenes/pie_pagina_menu.gif" border=0></td></tr>
        </table>
    <?php 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
