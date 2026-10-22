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
        Documentos y Evaluaciones
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
		<li class="active"><i class="fa fa-file-o"></i> Documentos y Evaluaciones</li>		
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php
if ((isMobile()) and ($comprobarMovil==true)) {
?>
<div class="alert alert-warning alert-dismissible" role="alert" id="alertaMoviles">
	<div class="row">
		<div class="col-xs-10">
			<img src="/app/imagenes/rotar_movil_blanco.png" width="48" border=0 class="pull-right">
			<strong>¿Estás usando un movil?</strong><br>Esta tabla se ve mejor con el movil en posición horizontal...	
		</div>
		<div class="col-xs-2">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		</div>
	</div>    
</div>
<?php	
}	
?> 
<?php 
           
if ($errores=="") {

        // Busca hijos del padre
        $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)))) or die(db_error()."- No se encuentran los datos del alumno.");
    
        if (db_num_rows($busca_datos_alumno)!=0) {
    
                ?>
                <div class="box box-primary"> <!-- Contenido -->    
                        <div class="box-body no-padding"> <!-- Inicio fondo blanco -->
                    <h3>Carpetas de documentos</h3>
                        <table class="table table-condensed table-bordered">
                    <tr>
                        <td><b>Curso</b></td>
                        <td><b>Escuela</b></td>
                        <td><b>Aula</b></td>
                        <td><b>Nombre</b></td>
                        <td><b>&nbsp;</b></td>
                    </tr>
            <?php 
            $contador_hijos=0;
            while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                            $id_curso_mostrado=$datos_hijos["id_curso"];
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
                    
                    $error_documentos="";
                    
                    // Busca carpetas aulas
                            $nombre_escuela=$datos_escuela["nombre_centro"]."-ID".$datos_escuela["id_centro"];
                            $nombre_del_curso="Curso ".$nombre_curso_mostrado."-ID".$id_curso_mostrado;
                            $nombre_aula=$datos_aula["nombre"]."-ID".$datos_aula["id_aula"];
                            $nombre_alumno=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]."-ID".$datos_hijos["id_alumno"];
                                                    $descarga_docs_path="/var/www/vhosts/escuelainfantilsantpau.com/documentos/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno/padres";
                                                                                                    
                                                    $sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');						
                                                    $descarga_docs_path=str_replace($sustituir,"_",$descarga_docs_path);
                                    
                            $descarga_docs_path1="/var/www/vhosts/escuelainfantilsantpau.com/documentacion/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres";
                            $descarga_centro_path="/var/www/vhosts/escuelainfantilsantpau.com/documentacion/$nombre_del_curso/$nombre_escuela/padres";
                                    $contador_documentos=0;       
                                    $contador_documentos_aula=0; 
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
                    $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación de su hijo/a. Póngase en contacto con la coordinadora de su centro para que le active el acceso.";
                    }
    if (file_exists($descarga_docs_path1)) {
                    $d = dir($descarga_docs_path1);
    
                                  while($s=$d->read()) {
    
                                    if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) {
    
                                                    // Busca si leido
                                                    $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_docs_path1) . (string)($subcarpeta_actual) . '/' . (string)($s)));
            
                                                    if (db_num_rows($busca_leido)==0) {
                                                            $contador_documentos_aula++;
                                                            }
                                            }
                                          }
                                    $d->close();
                    } else {
                    $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del aula. Póngase en contacto con la coordinadora de su centro para que le active el acceso.";
                    }
    if (file_exists($descarga_centro_path)) {        
                    $d = dir($descarga_centro_path);
    
                                  while($s=$d->read()) {
    
                                    if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) {
    
                                                    // Busca si leido
                                                    $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_centro_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
            
                                                    if (db_num_rows($busca_leido)==0) {
                                                            $contador_documentos_aula++;
                                                            }
                                            }
                                          }
                                    $d->close();  
                    } else {
                    $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del centro. Póngase en contacto con la coordinadora de su centro para que le active el acceso.";
                    }
                    
    $texto_docs="";
    if ($contador_documentos!=0) {
                $coletilla_docs="";
                if ($contador_documentos>1) {
                        $coletilla_docs="s";
                        }
                $texto_docs=" <span class=\"badge\">".$contador_documentos." nuevo$coletilla_docs</span> ";
            }
    $texto_docs_aula="";
    if ($contador_documentos_aula!=0) {
                $coletilla_docs_aula="";
                if ($contador_documentos_aula>1) {
                        $coletilla_docs_aula="s";
                        }
                $texto_docs_aula=" <span class=\"badge\">".$contador_documentos_aula." nuevo$coletilla_docs_aula</span> ";
            }
                  
            
            // provisional 
            $error_documentos="";
            if ($error_documentos!="") {
                    ?>
            <tr>
                    <td align="center" colspan="5"><font size=2 color='#ff0000'><?=$error_documentos;?></td>
            </tr>
                    <?php 
                    }                
                    ?>
            <tr>
                <td align="center"><?=$nombre_curso_mostrado;?></td>
                <td align="center"><?=$datos_escuela["nombre_centro"];?></td>
                <td align="center"><?=security_escape($datos_aula["nombre"]);?></td>
                <td align="center"><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></td>
                <td align="center">
                        <!-- <button style="margin-top:5px;" class="btn btn-responsive btn-primary btn-sm" onclick="window.location.href='ver_carpeta_alumno.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';">Abrir carpeta personal<?=$texto_docs?></button>-->
                        <button style="margin-top:5px;" class="btn btn-responsive btn-primary btn-sm" type="button" onclick="window.location.href='ver_carpeta_aula.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_aula=<?=$datos_hijos["aula"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';">Abrir carpeta de documentos<?=$texto_docs_aula?></button>                       
                </td>
            </tr>
                    <?php 
                    $contador_hijos++;
                    }
            ?>
            </table>
            <?php
            }
        }
        ?>
                </div>
        </div>
        <?php
    
    $nombre_usuario=$_SESSION['nombre_padre'];
    
    $version_js=$_GET["version_js"];
    
    if (strlen($version_js)!=0) {
            $_SESSION['version_js']=$version_js;
            } else {
            $version_js=$_SESSION['version_js'];
            }
    if (strlen($version_js)==0) {
            $version_js==0;
            }
    
    $errores="";
    
    if ($errores=="") {
    
            // Busca usuario
            $busca_usuario=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario)));
            
            if (db_num_rows($busca_usuario)==0) {
                    $errores="El nombre de usuario introducido no existe.<br>";
                    } else {
                    $datos_usuario=db_fetch_array($busca_usuario);
                    // Mostramos la información del niño
    
                    $id_usuario=$datos_usuario["id_usuario"];
                    $id_hijo=$datos_usuario["padre_de"];
    
                                    $parentesco=$datos_usuario["parentesco"];
    
                    // Busca datos del padre.
                    $busca_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario)) or die(db_error()."- No se encuentran los datos del padre.");
    
                    $datos_padre=db_fetch_array($busca_datos_padre);
                    if ($datos_padre["sexo"]=="V") {
                            $sufijo_padre="o";
                            } else {
                            $sufijo_padre="a";
                            }
    
                                    if ($parentesco!=1) {
                                            $errores="- Esta información está restringida a los padres del alumno -";
                                            } 
                    }
    
            }
           
    if ($errores!="") {
        ?>
        <div align="center">
                <h3><?=$errores?></h3>
                <input type="button" class="btn btn-responsive btn-primary btn-sm" onclick="window.location.href='/app/index.php'">Volver</button>
        </div>
        <?php 
        } else {
    
        // Busca hijos del padre
        $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? AND alumnos.fecha_matricula<=? ORDER BY alumnos.id_curso DESC', 'sss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)), (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");
    
        if (db_num_rows($busca_datos_alumno)!=0) {
    
            ?>
                <div class="box box-primary"> <!-- Contenido -->    
                        <div class="box-body no-padding"> <!-- Inicio fondo blanco -->
                    <h3>Informes de evaluación disponibles</h3>
                        <table class="table table-condensed table-bordered">
                    <tr>
                        <td><b>Curso</b></td>
                        <td><b>Escuela</b></td>
                        <td><b>Aula</b></td>
                        <td><b>Nombre</b></td>
                        <td><b>&nbsp;</b></td>
                    </tr>
            <?php 
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
                    
                    ?>
                    <tr>
                        <td class="text-center"><?=$nombre_curso_mostrado;?></td>
                        <td class="text-center"><?=$datos_escuela["nombre_centro"];?></td>
                        <td class="text-center"><?=$lista_grupos_alumno;?></td>
                        <td class="text-center"><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></td>
                        <td class="text-center"5><?php 
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
                                                    $trimestre_numeral="º";
                                                    }
    
                            ?>
                            <a class="btn btn-responsive btn-primary btn-sm btn-margin" href="/app/evaluacion.php?id_aula=<?=$datos_hijos["aula"]?>&id_curso=<?=$datos_hijos["id_curso"]?>&id_alumno=<?=$datos_hijos["id_alumno"]?>&trimestre=<?=$evaluaciones["trimestre"]?>"><?=$evaluaciones["trimestre"].$trimestre_numeral?> Trimestre</a>
                            <?php 
                            }
                            
                            /*
                            if ($mostrar_resto_evaluaciones==true) { 
                                    
                                            // Inglés
                                            $busca_evaluaciones=mysql_query("SELECT * FROM agenda_programacion_ingles_evaluaciones_estado WHERE id_aula=".$datos_hijos["aula"]." AND estado=1");
                                    
                                            while($evaluaciones=mysql_fetch_array($busca_evaluaciones)){
                                                    
                                                    if ($mostrar_resto_evaluaciones_trim[$evaluaciones["trimestre"]]==true) {
                                                            
                                                            $num_evaluaciones++;
                                                            $trimestre_numeral="er";
    
                                                                                            if ($evaluaciones["trimestre"]==2) {
                                                                                                            $trimestre_numeral="º";
                                                                                                            }
    
                                                            ?>
                                                            <button class="btn btn-responsive btn-primary btn-sm btn-margin" onclick="abre_documento('pdf/evaluacion_ingles_e.php',<?=$datos_hijos["aula"]?>,<?=$datos_hijos["id_curso"]?>,<?=$datos_hijos["id_alumno"]?>,<?=$evaluaciones["trimestre"]?>);"><?=$evaluaciones["trimestre"].$trimestre_numeral?> Trimestre, Inglés</button>
                                                            <?
                                                    }
                                            }
                                                                           
                            // Comedor
                            $busca_evaluaciones=mysql_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_estado_alumnos WHERE id_alumno=".$datos_hijos["id_alumno"]." AND estado=1;") or die(mysql_error());
                            while($evaluaciones=mysql_fetch_array($busca_evaluaciones)){
                            $num_evaluaciones++;
                            $trimestre_numeral="er";
    
                                            if ($evaluaciones["periodo"]==2) {
                                                    $trimestre_numeral="º";
                                                    }
    
                            ?>
                            <button class="btn btn-responsive btn-primary btn-sm btn-margin" onclick="abre_documento('pdf/evaluacion_comedor.php',<?=$datos_hijos["aula"]?>,<?=$datos_hijos["id_curso"]?>,<?=$datos_hijos["id_alumno"]?>,<?=$evaluaciones["periodo"]?>);">Evaluación Comedor</button>
                            <?
                            }
                            
                            // Piscina
                            $busca_evaluaciones=mysql_query("SELECT * FROM gestionpiscina.agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=".$datos_hijos["id_alumno"]." AND (estado_t1+estado_t2+estado_t3)<>0;") or die(mysql_error());
                            while($evaluaciones=mysql_fetch_array($busca_evaluaciones)){
                            $num_evaluaciones++;
    
                            $trimestres_evaluados="";
                            if ($evaluaciones["estado_t1"]==1) {
                                    $trimestres_evaluados.="Tri. 1";
                                    }
                            if ($evaluaciones["estado_t2"]==1) {
                                    $trimestres_evaluados.=", Tri. 2";
                                    }
                            if ($evaluaciones["estado_t3"]==1) {
                                    $trimestres_evaluados.=", Tri. 3";
                                    }
                                            if (substr($trimestres_evaluados,0,2)==", ") {
                                                    $trimestres_evaluados=substr($trimestres_evaluados,2,strlen($trimestres_evaluados)-2);
                                                    }                                
    
                            ?>
                            <button class="btn btn-responsive btn-primary btn-sm btn-margin" onclick="abre_documento('pdf/evaluacion_piscina.php',0,0,<?=$datos_hijos["id_alumno"]?>,<?=$evaluaciones["trimestre"]?>);"><?=$trimestres_evaluados?>, Natación</button>
                            <?
                            }
                        }
                           */                 
                            
                    if ($num_evaluaciones==0) {
                    ?>No hay informes disponibles<?php 
                    }
                    ?></td>
            </tr>
                    <?php 
                    $contador_hijos++;
                    }
            ?>
            </table>
                        </div>
	        </div>
            <?php 
            }

        }
        ?></div>
    <script language="Javascript">
    
    function abre_documento(destino,id_aula,id_curso,id_alumno,trimestre) {
    
            documento_evaluacion=window.open(destino+'?id_aula='+id_aula+'&id_curso='+id_curso+'&id_alumno='+id_alumno+'&trimestre='+trimestre);
    
            // Deteccion IE
            var isIE9 = document.addEventListener, isIE8 = document.querySelector, isIE7 = window.XMLHttpRequest;
    
            if ((isIE9)||(isIE8)||(isIE7)) {
                    documento_evaluacion.location.href=destino+'?id_aula='+id_aula+'&id_curso='+id_curso+'&id_alumno='+id_alumno+'&trimestre='+trimestre;
                    }
    
            }
    
    </script>

      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>