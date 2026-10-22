<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Busca hijos del padre
    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        ?>
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
                $busca_datos_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_hijos["id_curso"]);
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                        } else {
                        $nombre_curso_mostrado="";
                        }
                        
                // Datos de la escuela del niño
                $busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del niño
                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"]);

                $datos_aula=db_fetch_array($busca_datos_aula);
                ?>
        <tr>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$nombre_curso_mostrado;?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_escuela["nombre_centro"];?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_aula["nombre"];?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></font></td>
                <td class="tabla_celda_datos" align="center" width=125><input type="button" value="Ver menú" class="tabla_celda_encabezado" onclick="window.location.href='ver_menu.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>'"></td>                 
        </tr>
                <?php 
                $contador_hijos++;
                }
        ?>
        <tr>
        <td align="center" colspan="5"><a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a></td>
        </tr>
        </table>
        <?php 
        }
    
    ?>

    <?php 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
