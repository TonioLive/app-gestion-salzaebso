<?php
require_once __DIR__ . '/../configuracion/deployment-config.php'; 
$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

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
    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' AND alumnos.fecha_matricula<='".date("Y-m-d")."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        while($datos_hijos=db_fetch_array($busca_datos_alumno)){

                $error_documentos="";
                         
                // Busca carpetas aulas
                        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"].";");
                        $datos_aulas=db_fetch_array($busca_aulas);
                        $busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);
                        $datos_escuelas=db_fetch_array($busca_escuelas);

                        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
                        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
                        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
                        $nombre_alumno=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]."-ID".$datos_hijos["id_alumno"];
                        $descarga_docs_path=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres");

						$sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');						
						
						$descarga_docs_path=str_replace($sustituir,"_",$descarga_docs_path);
						
						$descarga_docs_path1=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno/padres");
						
						$descarga_docs_path1=str_replace($sustituir,"_",$descarga_docs_path1);
						
                        $descarga_centro_path=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/padres");
												
                                $contador_documentos=0;       
                                 
if (file_exists($descarga_docs_path)) {         
                                                
                                $d = dir($descarga_docs_path);

                              while($s=$d->read()) {

                                if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) {

                                                // Busca si leido
                                                $busca_leido=db_query("SELECT * FROM agenda_documentos_padres WHERE id_usuario=$id_usuario AND nombre_documento='$descarga_docs_path$subcarpeta_actual/$s'");
        
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
                                                $busca_leido=db_query("SELECT * FROM agenda_documentos_padres WHERE id_usuario=$id_usuario AND nombre_documento='$descarga_docs_path1$subcarpeta_actual/$s'");
        
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
                                                $busca_leido=db_query("SELECT * FROM agenda_documentos_padres WHERE id_usuario=$id_usuario AND nombre_documento='$descarga_centro_path$subcarpeta_actual/$s'");
        
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

?>
<table border=0 cellpadding="2" cellspacing=2>
<tr><td><div><a href="principal.php"><img src="/webcam/imagenes/botones/1.png" border=0 align=middle>Webcam</a></div></td></tr>
<?php  if ($mostrar_mensajes==true) {

// Busca padres
    $filtro_padres="";
    $busca_padres=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario");
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
		
            $busca_respuestas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE direccion=1 AND id_nota=".$mensajes["id_nota_padre"]." AND respondido_por<>$id_usuario AND ((fecha_respuesta>='2018-01-31' AND leido_por NOT like '%[".$id_usuario."]%') OR (fecha_respuesta<'2018-01-31' AND leida=0))");

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
            $texto_alerta1="$total_no_leidos mensaje$coletilla_leido no leido$coletilla_leido";
            }

?>
<tr><td><div><a href="mensajeria.php"><img src="/webcam/imagenes/botones/2.png" border=0 align=<?=$alineacion_texto?>>Mensajes</a><?=$texto_no_leidos?></div></td></tr>
<?php  } ?>
<tr><td><div><a href="comedor.php"><img src="/webcam/imagenes/botones/3.png" border=0 align=middle>Comedor</a></div></td></tr>
<tr><td><div><a href="https://blog.escuelas-infantiles.com" target="_blank"><img src="/webcam/imagenes/botones/4.png" border=0 align=middle>Blog</a></div></td></tr>
<?php  if ($mostrar_documentacion==true) {

$texto_docs="";
$texto_alerta2="";
$alineacion_texto="middle";
if ($contador_documentos!=0) {
            $coletilla_docs="";
            $alineacion_texto="left";
            if ($contador_documentos>1) {
                    $coletilla_docs="s";
                    }
            $texto_docs="<br /><small>(<font color='#f00'>".$contador_documentos." nuevo$coletilla_docs</font>)</small>";
            $texto_alerta2="$contador_documentos documento$coletilla_docs nuevo$coletilla_docs";
        }
?>
<tr><td><div><a href="documentos.php"><img src="/webcam/imagenes/botones/8.png" border=0 align=<?=$alineacion_texto?>>Documentos</a><?=$texto_docs;?></div></td></tr>
<?php 
}
            if (($mostrar_agenda==true)) {
?>
<tr><td><div><a href="agenda.php"><img src="/webcam/imagenes/botones/5.png" border=0 align=middle><small>Agenda Escolar</small></a></div></td></tr>
<?php 
}
?>
<tr><td><div><a href="facturas.php"><img src="/webcam/imagenes/botones/6.png" border=0 align=middle>Facturas</a></div></td></tr>
<tr><td><div><a href="/configuracion/cerrar_sesion_padres.php"><img src="/webcam/imagenes/botones/7.png" border=0 align=middle>Salir</a></div></td></tr>
</table>
<?php 
$texto_alerta=$texto_alerta1;
if ((strlen($texto_alerta1)!=0) and (strlen($texto_alerta2)!=0)) {
        $texto_alerta.=" y ";
        }
$texto_alerta.=$texto_alerta2;        
if (strlen($texto_alerta)!=0) {
        $texto_alerta="<p align='center'><font color=#ff0000>¡ATENCIÓN! Tienes ".$texto_alerta."</font></p>";
        }
if (strlen($error_documentos)!=0) {
        $texto_alerta="<p align='center'>".$error_documentos."</p>".$texto_alerta;
        }
?>
