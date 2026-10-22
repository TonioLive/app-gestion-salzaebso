<?php
require("../configuracion/config.php");
require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Busca hijos del padre
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        ?>
        <font size=3><b>&nbsp;&nbsp;Carpetas de documentos</b></font>
                <table width=690 class="tabla_datos">
        <tr>
                <td class="tabla_celda_encabezado">Curso</td>
                <td class="tabla_celda_encabezado">Escuela</td>
                <td class="tabla_celda_encabezado">Aula</td>
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

                // Datos del aula del niño
                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

                $datos_aula=db_fetch_array($busca_datos_aula);
                
                $error_documentos="";
               
                              
                ?>
        <tr>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$nombre_curso_mostrado;?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_escuela["nombre_centro"];?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=security_escape($datos_aula["nombre"]);?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></font></td>
                <td class="tabla_celda_datos" align="center" width=125><font size=2><input type="button" onclick="window.location.href='galerias_privadas/index.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>&id_aula=<?=$datos_hijos["aula"]?>';" class="tabla_celda_encabezado" value="Galería imágenes aula"><br><input type="button" onclick="window.location.href='galerias_privadas/galeria_comun.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_aula=<?=$datos_hijos["aula"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>';" class="tabla_celda_encabezado" value="Galería imágenes escuela"></font></td>
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
		<a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_facturas_act.gif',1)"><img src="/webcam/imagenes/salir_facturas_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a>
	</div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
