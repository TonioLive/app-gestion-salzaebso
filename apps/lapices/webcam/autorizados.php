<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
         
   if ($errores=="") {
	   
	$id_usuhijo=(int) app_request_value($_GET, "id_usuhijo", 0);  
	$id_hijo=(int) app_request_value($_GET, "id_hijo", 0);
	$id_padre=(int) $id_usuario; 
	
	// Datos para el formulario
	$formulario_name = "formulario1";
	$formulario_method = "POST";
	$formulario_action = "autorizados1.php?id_foto=".$id_foto;	 
	  
	// Nombre foto
	$nombre_foto="facedetection/foto_usu.php?id_alu=".$id_foto;
	$nombre_foto1="facedetection/foto_usu.php?id_alu=".$id_foto."&width=100";
	$mensaje_foto="Foto Actual";

	if (!file_exists($upload_path."/foto_usu".$id_foto.".jpg")) {
	      $nombre_foto="/fotos_usuarios/sin_foto.jpg";
	      $nombre_foto1="/fotos_usuarios/sin_foto.jpg";
	      $mensaje_foto="Insertar Foto";
	      }	  
	   
    ?>
    <div align="center">	    	    
    <?php 
    if (!empty($_SESSION['credencial_familiar_temporal'])) {
        $credencial_temporal = $_SESSION['credencial_familiar_temporal'];
        unset($_SESSION['credencial_familiar_temporal']);
        if (!empty($credencial_temporal['expires']) && (int) $credencial_temporal['expires'] >= time()) {
            echo '<div style="max-width:720px;margin:12px auto;padding:12px;border:1px solid #b9dccf;background:#effaf6">';
            echo '<strong>Credencial temporal creada.</strong> Guardala ahora; la contrasena no se volvera a mostrar.<br>';
            echo 'Usuario: <code>' . htmlspecialchars($credencial_temporal['usuario'], ENT_QUOTES, 'UTF-8') . '</code> &nbsp; ';
            echo 'Contrasena: <code>' . htmlspecialchars($credencial_temporal['password'], ENT_QUOTES, 'UTF-8') . '</code>';
            echo '</div>';
        }
    }
    // Busca hijos del padre
    $busca_datos_alumno=db_query_params("SELECT * FROM usuarios, padres WHERE usuarios.id_usuario=padres.padre_de AND padres.id_usuario=? AND usuarios.id_usuario=? AND padres.parentesco=1 LIMIT 1", "ii", array($id_padre, $id_usuhijo));

    if (db_num_rows($busca_datos_alumno)!=0) {

		$datos_hijos=db_fetch_array($busca_datos_alumno);

        ?>
        <h3>Personas autorizadas a recoger a <?=$datos_hijos["Nombre"]?></h3>
          
	        <table class="tabla_datos">
                <tr>
                    <td class="tabla_celda_encabezado">&nbsp;</td>
                    <td class="tabla_celda_encabezado"><b>Nombre</b></td>
                    <td class="tabla_celda_encabezado"><b>DNI/NIE</b></td>
                    <td class="tabla_celda_encabezado"><b>Relación de parentesco</b></td>
                    <td class="tabla_celda_encabezado">&nbsp</td>
                </tr>
	        <?php 
	        // Busca relaciones
	        $parentescos[1]="Padre/Madre";
	        $parentescos[2]="Abuelo/a";
	        $parentescos[3]="Tio/a";
	        $parentescos[4]="Autorizado/a";

			$consulta_relaciones=db_query_params("SELECT * FROM padres WHERE padre_de=?", "i", array($id_usuhijo));
			$numero_de_relaciones=db_num_rows($consulta_relaciones);	        
	        
	        $contador_relaciones=0;
	        while($relacion=db_fetch_array($consulta_relaciones)) {
		    	$contador_relaciones=$contador_relaciones+1;    
				// Busca datos padres
				$consulta_datos_padre=db_query_params("SELECT * FROM usuarios WHERE id_usuario=? LIMIT 1", "i", array((int) $relacion["id_usuario"]));
	        
				if (db_num_rows($consulta_datos_padre)!=0) {
	                $datos_padre=db_fetch_array($consulta_datos_padre);
	                ?>
                <tr>
	                <td class="tabla_celda_datos" align="center"><?=$contador_relaciones?></td>
                    <td  class="tabla_celda_datos" align="center"><?=$datos_padre["Nombre"]?> <?=$datos_padre["Apellido1"]?> <?=$datos_padre["Apellido2"]?></td>
                    <td class="tabla_celda_datos" align="center"><?=$datos_padre["DNI"]?></td>
                    <td class="tabla_celda_datos" align="center"><?=$parentescos[$relacion["parentesco"]]?></option>
                    </td>
					<td class="tabla_celda_datos" align="center"><?php  if (($relacion["parentesco"]!=1) or ($relacion["id_usuario"]==$id_usuario)) { ?><input type="button" class="tabla_celda_encabezado" value="Editar" onclick="window.location.href='autorizado_editar.php?id_autorizado=<?=$relacion["id_padre"]?>&id_hijo=<?=$id_usuhijo?>';"> <?php  } ?><input type="button" class="tabla_celda_encabezado" value="Fotocopia DNI/NIE" onclick="window.location.href='autorizado_dni.php?id_autorizado=<?=$relacion["id_padre"]?>&id_hijo=<?=$id_usuhijo?>';"><?php  if ($relacion["parentesco"]!=1) { ?> <input type="button" class="tabla_celda_encabezado" value="Eliminar" onclick="if (confirm('¿Quieres eliminar definitivamente los datos de <?=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"]?>?\n\nEsta acción no se puede deshacer.')) { window.location.href='autorizado_eliminar.php?id_autorizado=<?=$relacion["id_padre"]?>&id_hijo=<?=$id_usuhijo?>&_csrf=<?=rawurlencode(app_csrf_token())?>'; }"><?php  } ?></td>
                </tr>
                
                <?php 
                }
			}
			
			for ($c1=$contador_relaciones;$c1<=5;$c1++) {
				$contador_relaciones++;
			?>
			                <tr>
	                <td class="tabla_celda_datos" align="center"><?=$contador_relaciones?></td>
                    <td  class="tabla_celda_datos" align="center">&nbsp;</td>
                    <td class="tabla_celda_datos" align="center">&nbsp;</td>
                    <td class="tabla_celda_datos" align="center">&nbsp;</option>
                    </td>
					<td class="tabla_celda_datos" align="center"><input type="button" class="tabla_celda_encabezado" value="Añadir" onclick="window.location.href='autorizado_editar.php?id_autorizado=0&id_hijo=<?=$id_usuhijo?>';"></td>
                </tr>
			<?php 
			}
        	?>       
       
	    	</table>   
	    	                
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
