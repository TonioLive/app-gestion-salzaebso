<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";

    // Busca hijos del padre
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        ?>
        <table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
        <tr><td><img src="/webcam/imagenes/encabezado_menu_2024.gif" border=0></tr></td>
        <tr><td><table width=690 border=2 bordercolor="#c4cf2b">
        <tr>
                <td bgcolor="f5ce93"><font size=2 color="#949b31"><b>Curso</b></font></td>
                <td bgcolor="f5ce93"><font size=2 color="#949b31"><b>Escuela</b></td>
                <td bgcolor="f5ce93"><font size=2 color="#949b31"><b>Aula</b></td>
                <td bgcolor="f5ce93"><font size=2 color="#949b31"><b>Nombre</b></td>
                <td bgcolor="f5ce93">&nbsp;</td>
        </tr>
        <?php 
        $contador_hijos=0;
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
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
                ?>
        <tr>
                <td align="center"><font size=2><?=$nombre_curso_mostrado;?></font></td>
                <td align="center"><font size=2><?=$datos_escuela["nombre_centro"];?></font></td>
                <td align="center"><font size=2><?=security_escape($datos_aula["nombre"]);?></font></td>
                <td align="center"><font size=2><?=security_escape($datos_hijos["Nombre"])." ".security_escape($datos_hijos["Apellido1"])." ".security_escape($datos_hijos["Apellido2"])?></font></td>
                <td align="center" width=125><font size=2><a href="ver_menu.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Hijo<?=$contador_hijos?>','','/webcam/imagenes/ver_menu_act.gif',1)"><img src="/webcam/imagenes/ver_menu_des.gif" alt="Ver menú" name="Hijo<?=$contador_hijos?>" border=0 id="Hijo<?=$contador_hijos?>"></a></font></td>
        </tr>
                <?php 
                $contador_hijos++;
                }
        ?>
        <tr>
        <td align="center" colspan="5"><a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a></td>
        </tr>
        </table>
        </td></tr>
        <tr><td><img src="/webcam/imagenes/pie_pagina_menu.gif" border=0></td></tr>
        </table>
        <?php 
        }
    
    ?>

    <?php 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior.php"); ?>
