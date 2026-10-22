<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");  ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Galería de Imágenes
        <small>Haz clic sobre la imagen para ampliarla...</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
		<li><a href="/app/fotos.php"><i class="fa fa-file-image-o"></i> Fotos</a></li>	
		<li class="active"><i class="fa fa-file-image-o"></i> Galería de Imágenes Escuela</li>	
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
	  

<?php    
   if ($errores=="") {


$id_curso_actual=$_GET["id_curso"];
$id_cliente=$_GET["id_hijo"];
$id_aula_hijo=$_GET["id_aula"];

$carpeta=$_GET["carpeta"];

$parentesco=0;
                                // Comprueba si es padre de este niño
                                if (strlen($id_cliente)!=0) {
                                                        
                                // Busca datos del alumno
                                $consulta_datos_del_cliente=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_cliente));
                                
                                if (db_num_rows($consulta_datos_del_cliente)!=0) {
                                        $datos_del_cliente=db_fetch_array($consulta_datos_del_cliente);
                                        $id_usuario_cliente=$datos_del_cliente["id_usuario"];
                                        $id_usuario_seleccionado=$id_usuario_cliente;
                                        
                                        if ($id_aula_hijo!=$datos_del_cliente["aula"]) {
                                                $errores="- El alumno seleccionado no pertenece al aula indicada -";
                                                }
                                        
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
        $descarga_docs_path="/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela/comun".$carpeta;

?>
<div class="box box-primary"> <!-- Contenido -->    
	<div class="box-body no-padding"> <!-- Inicio fondo blanco -->
    <div class="row fondo_1"> <!-- Contenido -->
    	<div class="col-md-12"> <!-- Columna principal -->
<table width=98% class="tabla_datos">
<?php 

$columnas=2;

$fecha=strftime("%m/%d/%Y");

$nombre_subcarpeta_actual="/";

?>
<tr>
<td colspan=<?=$columnas?>><b><?php 

if (strlen($carpeta)!=0) {
        ?>Galería: <?=utf8_decode(substr($carpeta,1,strlen($carpeta)-1));?></b></td><?php 
        } else {
        ?>Galerías de la escuela <?=$datos_escuelas["nombre_centro"];?> para el Curso <?=security_escape($datos_curso_actual["nombre"]);?>.<br><br>Alumno: <?=security_escape($datos_alumnos["Nombre"]);?> <?=security_escape($datos_alumnos["Apellido1"]);?> <?=security_escape($datos_alumnos["Apellido2"]);?></td><?php 
        }

        if (file_exists($descarga_docs_path)) {                              

$contador=0;

$d = dir($descarga_docs_path);

$contador_columnas=0;

      while($s=$d->read()) {

                if ($contador_columnas==0) {
                        ?><tr valing="top"><?php 
                        }
                        
        $elemento_mostrado=false;
                        
        if (($s!=".") and ($s!="..") and (substr($s,(strlen($s)-4),1)!=".") and (strlen($carpeta)==0)) {
        		if (substr($s,0,1)!=".") {
                // Carpeta
                $elemento_mostrado=true;
                $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path/$s"));
                ?>
                <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><a href="galeria_comun.php?carpeta=/<?=$carpeta.$s;?>&id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_cliente?>&id_aula=<?=$id_aula_hijo?>"><big><big><big><big><big><i class="fa fa-folder-open-o"></i></big></big></big></big></big><br><?=utf8_decode($s);?></a><?=$fecha_archivo?></td>
                <?php 
                	}
                } else {
                // Imagen
                if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PNG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="GIF")) and (substr($s,0,1)!=".")) {
                        $elemento_mostrado=true;
                        $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path/$s"));
                        ?>
                        <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%">

                                <a href="#" onclick="mostrar_imagen('<?=$descarga_docs_path."/".$s?>')">
                                <img class="img-responsive" border="0" src="mostrar_imagen.php?file=<?=$descarga_docs_path."/".$s?>&width=175"></a>
                                <?=utf8_decode($s);?> <?=$fecha_archivo;?>	

                        </td>
                        <?php 
                        }
                }
        if ($elemento_mostrado==true) {
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
<td colspan=<?=$columnas?> align="center"><b>La carpeta no contiene archivos</b></td></tr>
        <?php 
        }

if (strlen($carpeta)!=0) {
        ?>
        <tr>
<td colspan=<?=$columnas?> align="center"><br><br><a class="btn btn-primary" href='galeria_comun.php?id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_cliente?>&id_aula=<?=$id_aula_hijo?>'><b>Volver al listado de galerías</b></a><br><br><br></td></tr>
        <?php 
        }

?>
</table>
<?php 
        } else {
                echo("<h3>No hay imágenes</h3>");    
        }
}
?>
<br /><button onclick="window.location.href='fotos.php'" class="btn btn-responsive btn-md btn-primary"><i class="fa fa-backward"></i> Volver</button><br /><br />

    <?php 
    }
    }
    ?></div>
			</div> 	<!-- Fin Columna principal -->
		</div> 		<!-- Fin Contenido -->
	</div>			<!-- Fin Contaniner -->	
</div>				<!-- Fin Jumbotron -->	   

      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<script>
        function mostrar_imagen(imagen) {
                $("#ampliarImagen .modal-body").html("<img src='mostrar_imagen.php?file="+imagen+"&width=1024' class='img-responsive' border='0'>");
                $("#ampliarImagen").modal('show');
        }
</script>

<div class="modal fade" id="ampliarImagen" tabindex="-1" role="dialog" aria-labelledby="ampliarImagenLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="ampliarImagenBody">
      </div>
    </div>
  </div>
</div>

<?php  require("parte_inferior_padres.php"); ?>
