<?php
require_once __DIR__ . '/../configuracion/deployment-config.php';
require("../configuracion/config.php"); 	

if (empty($_SESSION['permitido_padre'])) {
	
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
        Carpeta Documentos
        <small>Haz clic sobre el nombre del archivo para descargarlo...</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app/index.php"><i class="fa fa-home"></i> Inicio</a></li>
		<li><a href="/app/documentos"><i class="fa fa-file-image-o"></i> Documentos e Imágenes</a></li>
		<li class="active"><i class="fa fa-folder-open-o"></i> Carpeta Documentos</li>		
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php  
    
   if ($errores=="") {
   
$id_cliente=$_GET["id_hijo"];
$id_aula_hijo=$_GET["id_aula"];
$parentesco=0;
                                // Comprueba si es padre de este niño
                                if (strlen($id_cliente)!=0) {
                                                        
                                // Busca datos del alumno
                                $consulta_datos_del_cliente=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=".$id_cliente);
                                
                                if (db_num_rows($consulta_datos_del_cliente)!=0) {
                                        $datos_del_cliente=db_fetch_array($consulta_datos_del_cliente);
                                        $id_usuario_cliente=$datos_del_cliente["id_usuario"];
                                        $id_usuario_seleccionado=$id_usuario_cliente;
                                        
                                        if ($id_aula_hijo!=$datos_del_cliente["aula"]) {
                                                $errores="- El alumno seleccionado no pertenece al aula indicada -";
                                                }
                                        
                                                $busca_hijo=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario AND padre_de=$id_usuario_cliente");
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

        
if (strlen($id_curso_actual)!=0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y alumnos
        $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=$id_usuario_seleccionado AND alumnos.id_curso=$id_curso_actual ORDER BY Apellido1, Apellido2, Nombre");
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_alumnos["aula"].";");
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_aulas["id_centro"]);
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];

        $descarga_docs_path=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres");
        $descarga_centro_path=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/padres");

               
        $sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');
        
        $descarga_docs_path=str_replace($sustituir,"_",$descarga_docs_path);
        $descarga_centro_path=str_replace($sustituir,"_",$descarga_centro_path);

?>
<div class="box box-primary"> <!-- Contenido -->    
	<div class="box-body no-padding"> <!-- Inicio fondo blanco -->
    	<div class="col-md-12"> <!-- Columna principal -->
    	<h3>Documentos e Imágenes</h3> 
<div align="center">
<div align="center"><center>
<table width=98% class="tabla_datos">
<?php 

$columnas=4;

$fecha=strftime("%m/%d/%Y");

$nombre_subcarpeta_actual="/";

?>
<tr>
<td class="tabla_celda_encabezado" colspan=<?=$columnas?>><b>Documentación del Curso <?=$datos_curso_actual["nombre"];?>.<br><br>Alumno: <?=$datos_alumnos["Nombre"];?> <?=$datos_alumnos["Apellido1"];?> <?=$datos_alumnos["Apellido2"];?></td>
<?php 
$contador=0;

$d = dir($descarga_docs_path.$subcarpeta_actual);

$contador_columnas=0;

      while($s=$d->read()) {

                // Busca si leido
                $busca_leido=db_query("SELECT * FROM agenda_documentos_padres WHERE id_usuario=$id_usuario AND nombre_documento='$descarga_docs_path$subcarpeta_actual/$s'");
        
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

$d = dir($descarga_centro_path);

      while($s=$d->read()) {

                // Busca si leido
                $busca_leido=db_query("SELECT * FROM agenda_documentos_padres WHERE id_usuario=$id_usuario AND nombre_documento='$descarga_centro_path$subcarpeta_actual/$s'");
        
                $texto_nuevo="";
                if (db_num_rows($busca_leido)==0) {
                        $texto_nuevo="<br /><font color='#ff0000'>¡Nuevo!";
                        }

                if ($contador_columnas==0) {
                        ?><tr valing="top"><?php 
                        }

        if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) and (substr($s,0,1)!=".")) {
                
                // Fecha modificación archivo
                $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_centro_path$subcarpeta_actual/$s"));
                
        ?>
        <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><a href="documentos/abrir_documento.php?carpeta_descarga=<?=$descarga_centro_path;?>&archivo_descarga=<?=$s;?>"><img src="documentos/img/<?=mb_strtolower(substr($s,strlen($s)-3,3))?>.gif" border=0><br><?=$s;?></a><?=$fecha_archivo.$texto_nuevo?></td>
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
<br /><button onclick="window.location.href='documentos.php'" class="btn btn-responsive btn-md btn-primary"><i class="fa fa-backward"></i> Volver</button><br /><br />
</center></div>
</div>
    <?php 
    }
    }
    ?></div>
			</div> 	<!-- Fin Columna principal -->
		</div> 		<!-- Fin Contenido -->
	</div>			<!-- Fin Contaniner -->	
      
	  <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>