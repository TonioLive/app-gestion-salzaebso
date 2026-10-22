<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
         
   if ($errores=="") {
	   
	$id_autorizado=$_GET["id_autorizado"];  
	$id_hijo=$_GET["id_hijo"];	 
	  
	// Busca datos padre
	
	$busca_datos_padre=db_query("SELECT * FROM padres LEFT JOIN usuarios ON padres.id_usuario=usuarios.id_usuario WHERE id_padre=$id_autorizado AND padre_de=$id_hijo") or die(db_error()); 
	
	if (db_num_rows($busca_datos_padre)==0) {
	
		exit("Se ha detectado una actividad sospechosa. No tienes permiso para acceder a los datos que solicitas.");	
		
	}
	
	$datos_padre=db_fetch_array($busca_datos_padre);
	
	$id_padre=$datos_padre["id_usuario"];
	  
	// Datos para el formulario
	$formulario_name = "formulario1";
	$formulario_method = "POST";
	$formulario_action = "autorizado_dni1.php?id_hijo=".$id_hijo."&id_autorizado=".$id_padre;		  	  
	  
	// Nombre foto
	$nombre_foto="facedetection/dni_usu.php?id_autorizado=".$id_autorizado."&id_alu=".$id_hijo;
	$nombre_foto1="facedetection/dni_usu.php?id_autorizado=".$id_autorizado."&id_alu=".$id_hijo."&width=150";
	$mensaje_foto="DNI/NIE Actual";

	if (!file_exists($upload_path_dni."/dni_usu".$id_padre.".jpg")) {
	      $nombre_foto="/dni_usuarios/sin_dni.jpg";
	      $nombre_foto1="/dni_usuarios/sin_dni.jpg";
	      $mensaje_foto="Insertar DNI/NIE";
	      }	  
	   
    ?>
    <div align="center">	    	    
    <?php 
    // Busca hijos del padre
    $busca_datos_alumno=db_query("SELECT * FROM usuarios, padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.id_usuario=$id_padre AND padres.padre_de=$id_hijo") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

		$datos_hijos=db_fetch_array($busca_datos_alumno);

        ?>
        <h3>Cambiar el DNI de <?=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"]?></h3>
        <h4><?=$mensaje_foto?></h4>
        <a href="javascript:ampliar('<?=$nombre_foto?>')"><img src="<?=$nombre_foto1?>" border=0 width="150"></a>
        
        
        <form ENCTYPE="multipart/form-data" name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
	    <b>Sube foto del DNI/NIE de <?=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"]?> en la que se pueda leer claramente sus datos.<br />Selecciona el archivo con la Foto pulsando el botón "Examinar", una vez elegido pulsa "Enviar".<br />Solo se permiten archivos JPG</b>
	    <br /><br />
	    
	    <input type="file" name="File1" size=50>
        <br /><br />
            
        <input type="submit" value="Enviar">
        <?php 
	    if ($mensaje_foto=="DNI/NIE Actual") {     
	    ?>
        <br /><br />
        <input type="button" class="text-danger" onclick="if (confirm('¿Quieres eliminar definitivamente el DNI/NIE de <?=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"]?>?\n\nEsta acción no se puede deshacer.')) { window.location.href='autorizados_borrar_dni.php?id_autorizado=<?=$id_padre?>&id_hijo=<?=$id_hijo?>' } " value="Eliminar DNI">  
        <?php 
	    }    
	    ?>                  
        </form>
        <br />
        <?php 
        }
    
    ?>

    <?php 
    }
    ?>
    </div>
    
    <a href="autorizados.php?id_usuario=<?=$id_usuario?>&id_usuhijo=<?=$id_hijo?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_facturas_act.gif',1)"><img src="/webcam/imagenes/salir_facturas_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a>
    
   <script Language="JavaScript">
   		function ampliar(foto) {
        	window.open(foto,'ampliacion','width=600,height=500');
        }
    </script> 
    
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
