<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Busca hijos del padre
    $busca_datos_alumno=db_query("SELECT *,alumnos.id_usuario AS id_usuario_alu  FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        ?>
        <font size=3><b>&nbsp;&nbsp;Gestionar datos de mis hijos/as</b></font>
                <table width=690 class="tabla_datos">
        <tr>
	        	<td class="tabla_celda_encabezado">Foto</td>
                <td class="tabla_celda_encabezado">Nombre</td>	        
                <td class="tabla_celda_encabezado">Curso</td>
                <td class="tabla_celda_encabezado">Escuela</td>
                <td class="tabla_celda_encabezado">Aula</td>
                <td>&nbsp;</td>
        </tr>
        <?php 
        $contador_hijos=0;
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                        $id_curso_mostrado=$datos_hijos["id_curso"];
                $busca_datos_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_hijos["id_curso"]);
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                        } else {
                        $nombre_curso_mostrado="";
                        }
                        
                // Datos de la escuela del ni–o
                $busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del ni–o
                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"]);

                $datos_aula=db_fetch_array($busca_datos_aula);
                
                 
                // Nombre foto
				$nombre_foto="facedetection/foto_usu.php?id_alu=".$datos_hijos["id_usuario_alu"];
				$mensaje_foto="Cambiar Foto";

				if (!file_exists($upload_path."/foto_usu".$datos_hijos["id_usuario_alu"].".jpg")) {
				      $nombre_foto="/fotos_usuarios/sin_foto.jpg";
				      $mensaje_foto="Insertar Foto";
				      }				     		       
                               
                ?>
        <tr>
	        	<td class="tabla_celda_datos" align="center"><a href="javascript:ampliar('<?=$nombre_foto?>')"><img src="<?=$nombre_foto?>&width=100" border=0 width="100"></a></td> 
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></font></td>                       
                <td class="tabla_celda_datos" align="center"><font size=2><?=$nombre_curso_mostrado;?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_escuela["nombre_centro"];?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_aula["nombre"];?></font></td>
                <td class="tabla_celda_datos" align="center" width=125><font size=2><input type="button" onclick="window.location.href='fotos.php?id_usuario=<?=$datos_hijos["id_usuario"]?>&id_foto=<?=$datos_hijos["id_usuario_alu"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';" class="tabla_celda_encabezado" value="<?=$mensaje_foto?>"><br><input type="button" onclick="window.location.href='autorizados.php?id_usuario=<?=$datos_hijos["id_usuario"]?>&id_usuhijo=<?=$datos_hijos["id_usuario_alu"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';" class="tabla_celda_encabezado" value="Personas autorizadas"></font></td>
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
    
    <a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_facturas_act.gif',1)"><img src="/webcam/imagenes/salir_facturas_des.gif" alt="Ver menœ" name="Image3" border=0 id="Image3"></a>
    
   <script>
		function ampliar(foto) {
        	window.open(foto,'ampliacion','width=600,height=500');
       	}
   </script>
   
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
