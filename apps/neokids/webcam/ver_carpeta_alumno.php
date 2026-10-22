<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php"); ?>

<div class="jumbotron fondo_2">
  <div class="container">
    <div class="row"> <!-- Menú -->
    	<?php  include("menu_lateral.php"); ?>
    </div>
    <div class="row fondo_1"> <!-- Contenido -->
    	<div class="col-md-12"> <!-- Columna principal -->
    	<h3>Documentos e Imágenes</h3> 
<?php  
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
$id_cliente=$_GET["id_hijo"];
$parentesco=0;
                                // Comprueba si es padre de este niño
                                if (strlen($id_cliente)!=0) {
        						
                                // Busca datos del alumno
                                $consulta_datos_del_cliente=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_cliente));
                                
                                if (db_num_rows($consulta_datos_del_cliente)!=0) {
                                        $datos_del_cliente=db_fetch_array($consulta_datos_del_cliente);
                                        $id_usuario_cliente=$datos_del_cliente["id_usuario"];
                                        $id_usuario_seleccionado=$id_usuario_cliente;
                                        
                                                $busca_hijo=db_query_params('SELECT * FROM padres WHERE id_usuario=? AND padre_de=?', 'ss', array($id_usuario, $id_usuario_cliente));
                                                if (db_num_rows($busca_hijo)==0) {
                                                        $errores="- No tiene autorización para acceder a la información de este alumno -";
                                                        } else {
                                                        // datos parentesco
                                                        $datos_parentesco=db_fetch_array($busca_hijo);
                                                        $parentesco=$datos_parentesco["parentesco"];
                                                        }
                                        } else {
                                        $errores="- El alumno seleccionado no existe -";
                                        }
                                
                                } else {
                                die();
                                }
                        
                                if ($parentesco==-1) {
                                        $errores="- Esta información está restringida a los padres del alumno -";
                                        }
if ($id_curso_actual==0) {
	$errores="- El curso todavía no ha comenzado -";
	}
                                        
if ($errores!="") {
    ?>
    <div align="center">
    <br><br><big><?=$errores?></big><br><br>
    </div>
    <?php 
    } else {
?>
<div align="center"><center>
<?php 
        
if (strlen($id_curso_actual)!=0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y alumnos
        $busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=? AND alumnos.id_curso=? ORDER BY Apellido1, Apellido2, Nombre', 'ss', array($id_usuario_seleccionado, $id_curso_actual));
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($datos_alumnos["aula"]));
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aulas["id_centro"]));
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $descarga_docs_path="/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno/padres";

?>
<table width=98% class="tabla_datos">
<?php 

$columnas=4;

$fecha=strftime("%m/%d/%Y");

$subcarpeta_actual=$_GET["sub"];

$nombre_subcarpeta_actual=$subcarpeta_actual;

if (strlen($nombre_subcarpeta_actual)==0) {
        $nombre_subcarpeta_actual="/";
        }
        
$array_subcarpetas=explode("/",$nombre_subcarpeta_actual);

$carpeta_superior="";

for($ca=1;$ca<count($array_subcarpetas)-1;$ca++) {
        $carpeta_superior=$carpeta_superior."/".$array_subcarpetas[$ca];
        }

?>
<tr>
<td class="tabla_celda_encabezado" colspan=<?=$columnas?>><b>Documentación del Curso <?=security_escape($datos_curso_actual["nombre"]);?> de <?=security_escape($datos_alumnos["Nombre"]);?> <?=security_escape($datos_alumnos["Apellido1"]);?> <?=security_escape($datos_alumnos["Apellido2"]);?><br>Carpeta actual:</b> <?=$nombre_subcarpeta_actual?> <?php  if ($nombre_subcarpeta_actual!="/") { ?><a href="ver_carpeta_alumno.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$carpeta_superior?>" title="Subir"><img align="absmiddle" src="subir.gif" border=0></a><?php  } ?><br><?php 
$d = dir($descarga_docs_path.$subcarpeta_actual);

$contador_sc=0;

while($s=$d->read()) {
        if (($s!=".") and ($s!="..") and (substr($s,(strlen($s)-4),1)!=".")) {
                if ($contador_sc==0) { echo("<b>Listado de subcarpetas</b> <i>Haz clic en ellas para ver su contenido</i><br>"); }
                ?><img src="/imagen/nada.gif" border=0 height=1 width=77><a href="ver_carpeta_alumno.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$subcarpeta_actual."/".$s?>" title="Abrir carpeta <?=$subcarpeta_actual."/".$s?>"><?=$subcarpeta_actual."/".$s?></a><br><?php 
                $contador_sc++;
                }
        }

?></td>
<?php 
$contador=0;
$d->close();
$d = dir($descarga_docs_path.$subcarpeta_actual);

$contador_columnas=0;

      while($s=$d->read()) {
      
		// Busca si leido
		$busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_docs_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
	
		$texto_nuevo="";
		if (db_num_rows($busca_leido)==0) {
			$texto_nuevo="<br /><font color='#ff0000'>¡Nuevo!";
			}

		if ($contador_columnas==0) {
			?><tr valing="top"><?php 
			}

        if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) and (substr($s,0,1)!=".")) {
        $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path$subcarpeta_actual/$s"));
        ?>
        <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><a href="documentos/abrir_documento.php?carpeta_descarga=<?=$descarga_docs_path.$subcarpeta_actual;?>&archivo_descarga=<?=$s;?>"><img src="documentos/img/<?=mb_strtolower(substr($s,strlen($s)-3,3))?>.gif" border=0><br><?=$s;?></a><?=$fecha_archivo.$texto_nuevo?></td>
        <?php 
        $contador=$contador+1;
        $contador_columnas++;
		if ($contador_columnas==$columnas) {
			$contador_columnas=0;
			?></tr><?php 
			}
        
        }
      }
$d->close();

if ($contador_columnas<$columnas) {
			?><td colspan="<?=($columnas-$contador_columnas)?>"></td></tr><?php 
			}

if ($contador==0) {
        ?>
        <tr>
<td colspan=<?=$columnas?> align="center"><b>La carpeta no contiene documentos</b></td></tr>
        <?php 
        }

?>
</table>
<?php 
}
?>
<br /><button onclick="window.location.href='documentos.php'" class="tabla_celda_encabezado celda_margen"><i class="fa fa-backward"></i> Volver</button><br /><br />
</center></div>
</div>
    <?php 
    }
    }
    ?></div>
			</div> 	<!-- Fin Columna principal -->
		</div> 		<!-- Fin Contenido -->
	</div>			<!-- Fin Contaniner -->	
</div>				<!-- Fin Jumbotron -->	    
<?php  require("../configuracion/parte_inferior_padres.php"); ?>