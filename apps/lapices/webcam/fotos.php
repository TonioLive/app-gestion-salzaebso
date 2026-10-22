<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
         
   if ($errores=="") {
	   
	$id_foto=$_GET["id_foto"];  
	$id_hijo=$_GET["id_hijo"];
	$id_padre=$_GET["id_usuario"]; 
	
	// Datos para el formulario
	$formulario_name = "formulario1";
	$formulario_method = "POST";
	$formulario_action = "fotos1.php?id_foto=".$id_foto;	 
	  
	// Nombre foto
	$nombre_foto="facedetection/foto_usu.php?id_alu=".$id_foto;
	$nombre_foto1="facedetection/foto_usu.php?id_alu=".$id_foto."&width=150";
	$mensaje_foto="Foto Actual";

	if (!file_exists($upload_path."/foto_usu".$id_foto.".jpg")) {
	      $nombre_foto="/fotos_usuarios/sin_foto.jpg";
	      $nombre_foto1="/fotos_usuarios/sin_foto.jpg";
	      $mensaje_foto="Insertar Foto";
	      }	  
	   
    ?>
    <div align="center">	    	    
    <?php 
    // Busca hijos del padre
    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_padre AND alumnos.id_alumno=$id_hijo ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

		$datos_hijos=db_fetch_array($busca_datos_alumno);

        ?>
        <h3>Cambiar la foto de <?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></h3>
        <h4><?=$mensaje_foto?></h4>
        <a href="javascript:ampliar('<?=$nombre_foto?>')"><img src="<?=$nombre_foto1?>" border=0 width="150"></a>
        
        
        <form ENCTYPE="multipart/form-data" name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
	    <b>Sube una foto en la que se vea claramente la cara de tu hijo/a y, si puede ser, sobre un fondo liso.<br />Lo ideal es una foto en formato carnet.<br />Selecciona el archivo con la Foto pulsando el botón "Examinar", una vez elegido pulsa "Enviar".<br />Solo se permiten archivos JPG</b>
	    <br /><br />
	    
	    <input type="file" name="File1" size=50>
        <br /><br />
            
        <input type="submit" value="Enviar">
                            
        </form>
        <br />
        <?php 
        }
    
    ?>

    <?php 
    }
    ?>
    </div>
    
    <a href="mis_datos.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_facturas_act.gif',1)"><img src="/webcam/imagenes/salir_facturas_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a>
    
   <script Language="JavaScript">
   		function ampliar(foto) {
        	window.open(foto,'ampliacion','width=600,height=500');
        }
    </script> 
    
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
