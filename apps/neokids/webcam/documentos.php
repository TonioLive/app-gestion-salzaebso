<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php"); ?>
<?php  $fechalimite1=$fechalimite; ?>
<div class="jumbotron fondo_2">
  <div class="container">
    <div class="row"> <!-- Menú -->
            <?php  include("menu_lateral.php"); ?>
    </div>
    <div class="row fondo_1"> <!-- Contenido -->
            <div class="col-md-12"> <!-- Columna principal -->
            <h3>Documentos e Imágenes</h3>            
        <?php     
           $fechalimite=$fechalimite1;        
           if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Busca hijos del padre
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        ?>
        <b>&nbsp;&nbsp;Carpetas de documentos</b></font>
                <table width="98%" class="tabla_datos">
        <tr>
                <td class="tabla_celda_encabezado">Curso</td>
                <td class="tabla_celda_encabezado">Escuela</td>
                <td class="tabla_celda_encabezado">Grupo</td>
                <td class="tabla_celda_encabezado">Nombre</td>
                <td>&nbsp;</td>
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

                // Datos del grupo del niño
                $busca_grupos_alumno=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_grupos.id_curso=? AND alumnos.id_usuario=?', 'ss', array($datos_hijos["id_curso"], $datos_hijos["padre_de"])) or die(db_error());
                
				$lista_grupos_alumno="";
        		while($grupos_donde=db_fetch_array($busca_grupos_alumno)) {
                	$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))."<br />";
                	}
                
				// Datos del aula del niño
                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

                $datos_aula=db_fetch_array($busca_datos_aula);                
                
                $error_documentos="";
                
                // Busca carpetas aulas
                        $nombre_escuela=$datos_escuela["nombre_centro"]."-ID".$datos_escuela["id_centro"];
                        $nombre_del_curso="Curso ".$nombre_curso_mostrado."-ID".$id_curso_mostrado;
                        $nombre_aula=$datos_aula["nombre"]."-ID".$datos_aula["id_aula"];
                        $nombre_alumno=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]."-ID".$datos_hijos["id_alumno"];
						$descarga_docs_path="/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno/padres";
                        $descarga_docs_path1="/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres";
                        $descarga_centro_path="/home/gabinete/$nombre_del_curso/$nombre_escuela/padres";
                        
                                $contador_documentos=0;       
                                $contador_documentos_aula=0; 
if (file_exists($descarga_docs_path)) {                
                                $d = dir($descarga_docs_path);

                              while($s=$d->read()) {

                                if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) and (substr($s,0,1)!='.')) {

                                                // Busca si leido
                                                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_docs_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                                                if (db_num_rows($busca_leido)==0) {
                                                        $contador_documentos++;
                                                        }
                                        }
                                      }
                                $d->close();
                        } else {
                // $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación de su hijo/a. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                }
if (file_exists($descarga_docs_path1)) {
                $d = dir($descarga_docs_path1);

                              while($s=$d->read()) {

                                if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) and (substr($s,0,1)!='.')) {

                                                // Busca si leido
                                                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_docs_path1) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                                                if (db_num_rows($busca_leido)==0) {
                                                        $contador_documentos_aula++;
                                                        }
                                        }
                                      }
                                $d->close();
                } else {
                // $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del aula. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                }
if (file_exists($descarga_centro_path)) {        
                $d = dir($descarga_centro_path);

                              while($s=$d->read()) {

                                if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) and (substr($s,0,1)!='.')) {

                                                // Busca si leido
                                                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_centro_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                                                if (db_num_rows($busca_leido)==0) {
                                                        $contador_documentos_aula++;
                                                        }
                                        }
                                      }
                                $d->close();  
                } else {
                // $error_documentos="<font color='#ff0000'>No tiene activado el acceso a la documentación del centro. Póngase en contacto con la coordinadora de su centro para que le active el acceso.</font>";
                }
                
$texto_docs="";
if ($contador_documentos!=0) {
            $coletilla_docs="";
            if ($contador_documentos>1) {
                    $coletilla_docs="s";
                    }
            $texto_docs=" (".$contador_documentos." nuevo$coletilla_docs) ";
        }
$texto_docs_aula="";
if ($contador_documentos_aula!=0) {
            $coletilla_docs_aula="";
            if ($contador_documentos_aula>1) {
                    $coletilla_docs_aula="s";
                    }
            $texto_docs_aula=" (".$contador_documentos_aula." nuevo$coletilla_docs_aula) ";
        }
                
        if ($error_documentos!="") {
                ?>
        <tr>
                <td class="tabla_celda_datos" align="center" colspan="5"><font size=2 color='#ff0000'><?=$error_documentos;?></font></td>
        </tr>
                <?php 
                }                
                ?>
        <tr>
                <td class="tabla_celda_datos" align="center"><?=$nombre_curso_mostrado;?></font></td>
                <td class="tabla_celda_datos" align="center"><?=$datos_escuela["nombre_centro"];?></font></td>
                <td class="tabla_celda_datos" align="center"><?=$lista_grupos_alumno;?></font></td>
                <td class="tabla_celda_datos" align="center"><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></font></td>
                <td class="tabla_celda_datos" align="center" width=125>
                <button onclick="window.location.href='ver_carpeta_alumno.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';" class="tabla_celda_encabezado"><i class="fa fa-file-pdf-o"></i> Abrir carpeta personal<?=$texto_docs?></button><br><button onclick="window.location.href='ver_carpeta_aula.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_aula=<?=$datos_hijos["aula"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';" class="tabla_celda_encabezado"><i class="fa fa-file-pdf-o"></i> Abrir carpeta del grupo<?=$texto_docs_aula?></button></font></td>
                <td class="tabla_celda_datos" align="center" width=125 valign="top"><button onclick="window.location.href='galerias_privadas/galeria_comun.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_aula=<?=$datos_hijos["aula"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';" class="tabla_celda_encabezado"><i class="fa fa-file-image-o"></i> Galería imágenes piscina</button></font></td>
        </tr>
                <?php 
                $contador_hijos++;
                }
        ?>
        </table>
        <?php 
        }
    
    ?>

    <?php 
    }
    ?></div>
        <div align="center">
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

                                if ($parentesco==-1) {
                                        $errores="- Esta información está restringida a los padres del alumno -";
                                        } 
                }

        }
       
if ($errores!="") {
    ?>
    <div align="center">
    <br><br><big><?=$errores?></big><br><br>
    <input type="button" onclick="window.location.href='/webcam/menu.php'" value="Volver">
        </div>
    <?php 
    } else {
    ?>
    <div align="center">
    <?php 

    // Busca hijos del padre
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? AND alumnos.fecha_matricula<=? ORDER BY alumnos.id_curso DESC', 'sss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)), (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        ?>
        <b>&nbsp;&nbsp;Informes de evaluación disponibles</b></font>
                <table width="98%" class="tabla_datos">
        <tr>
                <td class="tabla_celda_encabezado">Curso</td>
                <td class="tabla_celda_encabezado">Escuela</td>
                <td class="tabla_celda_encabezado">Grupo</td>
                <td class="tabla_celda_encabezado">Nombre</td>
                <td>&nbsp;</td>
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

                // Datos del grupo del niño
                $busca_grupos_alumno=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_grupos.id_curso=? AND alumnos.id_usuario=?', 'ss', array($datos_hijos["id_curso"], $datos_hijos["padre_de"])) or die(db_error());
                
				$lista_grupos_alumno="";
        		while($grupos_donde=db_fetch_array($busca_grupos_alumno)) {
                	$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))."<br />";
                	}

                // Datos del aula del niño
                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

                $datos_aula=db_fetch_array($busca_datos_aula);
                
                ?>
        <tr>
                <td class="tabla_celda_datos" align="center"><?=$nombre_curso_mostrado;?></font></td>
                <td class="tabla_celda_datos" align="center"><?=$datos_escuela["nombre_centro"];?></font></td>
                <td class="tabla_celda_datos" align="center"><?=$lista_grupos_alumno;?></font></td>
                <td class="tabla_celda_datos" align="center"><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></font></td>
                <td class="tabla_celda_datos" align="center" width=125><?php 
                // busca evaluaciones
                $num_evaluaciones=0;

                	// Piscina
                	
                	$busca_evaluaciones=db_query_params('SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=? AND (estado_p1+estado_p2+estado_p3+estado_p4)<>0;', 's', array($datos_hijos["id_alumno"])) or die(db_error());
                	while($evaluaciones=db_fetch_array($busca_evaluaciones)){
                        $num_evaluaciones++;

                        $trimestres_evaluados="";
                        if ($evaluaciones["estado_p1"]==1) {
                                $trimestres_evaluados.="1";
                                }
                        if ($evaluaciones["estado_p2"]==1) {
                                $trimestres_evaluados.=", 2";
                                }
                        if ($evaluaciones["estado_p3"]==1) {
                                $trimestres_evaluados.=", 3";
                                }
                        if ($evaluaciones["estado_p4"]==1) {
                                $trimestres_evaluados.=", 4";
                                }
        				if (substr($trimestres_evaluados,0,2)==", ") {
        					$trimestres_evaluados=substr($trimestres_evaluados,2,strlen($trimestres_evaluados)-2);
        					}                                

                        ?>
                        <input type="button" value="Evaluación periodo: <?=$trimestres_evaluados?>" class="tabla_celda_encabezado" onclick="abre_documento('pdf/evaluacion_piscina.php',0,0,<?=$datos_hijos["id_alumno"]?>,<?=$evaluaciones["periodo"]?>);"><br />
                        <?php 
                        }

                if ($num_evaluaciones==0) {
                ?>No hay informes disponibles</font><?php 
                }
                ?></td>
        </tr>
                <?php 
                $contador_hijos++;
                }
        ?>
        </table>
        <?php 
        }
    
    ?>

    <?php 
    }
    ?></div>
                        </div>         <!-- Fin Columna principal -->
                </div>                 <!-- Fin Contenido -->
        </div>                        <!-- Fin Contaniner -->        
</div>                                <!-- Fin Jumbotron -->            
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
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
