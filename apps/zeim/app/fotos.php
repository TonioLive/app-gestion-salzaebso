<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}

?>
<style>
        @keyframes parpadeo {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }

        .parpadeante {
            animation: parpadeo 1s infinite;
        }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Fotos
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
		<li class="active"><i class="fa fa-file-image-o"></i> Fotos</li>		
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
        $fechaMostrarImagenes = $fechalimite + (86400*31); // Añade 31 días a la fecha límite para mostrar imágenes
        $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_matricula<=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");
    
        if (db_num_rows($busca_datos_alumno)!=0) {
    
                ?>
                <div class="box box-primary"> <!-- Contenido -->    
                        <div class="box-body no-padding"> <!-- Inicio fondo blanco -->
                    <h3>Galerías de Fotos</h3>
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
                    
                    $nombre_escuela=$datos_escuela["nombre_centro"]."-ID".$datos_escuela["id_centro"];
                    $nombre_del_curso="Curso ".$datos_curso_mostrado["nombre"]."-ID".$id_curso_mostrado;
                    $nombre_aula=$datos_aula["nombre"]."-ID".$datos_aula["id_aula"];
                    $baseDirectory = "/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela/$nombre_aula";
                    $descarga_centro_path="/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela/comun";
                    
             // Comprueba si hay nuevas
             
             // Parámetros
             $directorio = $baseDirectory; // Directorio donde se buscan las imágenes             
                          
             // Contar las imágenes nuevas en el directorio principal
             $imagenesNuevas = contarImagenesNuevas($directorio, $imagenes_vistas, $fechaCorte); 

             // Mostrar el número de imágenes nuevas
             $mensaje = "";
             if ($imagenesNuevas>0) {
                $textoNuevas = "imágenes nuevas";
                if ($imagenesNuevas==1) {
                        $textoNuevas = "imagen nueva";
                }
                $mensaje = '<br /><span class="parpadeante">(Hay '.$imagenesNuevas.' ' . $textoNuevas . ')</span>';           
             }

                    ?>
            <tr>
                <td align="center"><?=$nombre_curso_mostrado;?></td>
                <td align="center"><?=$datos_escuela["nombre_centro"];?></td>
                <td align="center"><?=security_escape($datos_aula["nombre"]);?></td>
                <td align="center"><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></td>
                <td align="center">
                        <button style="margin-top:5px;" class="btn btn-responsive btn-primary btn-sm" onclick="window.location.href='galerias_privadas/index.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>&id_aula=<?=$datos_hijos["aula"]?>';">Galería imágenes aula<?=$mensaje?></button>
                        <!--<button style="margin-top:5px;" class="btn btn-responsive btn-primary btn-sm" type="button" onclick="window.location.href='galeria_comun.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_aula=<?=$datos_hijos["aula"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';">Galería imágenes escuela</button>-->
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
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>