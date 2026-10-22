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

$id_cliente=$_GET["id_hijo"];
$id_aula_hijo=$_GET["id_aula"];
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
                        
                                if ($parentesco!=1) {
                                        $errores="- Esta información está restringida a los padres del alumno -";
                                        }
if ($id_curso_actual==0) {
        $errores="- El curso todavía no ha comenzado -";
        }
                                        
if ($errores!="") {
    ?>
    <div align="center">
    <br><br><big><?=$errores?></big><br><br>
    <input type="button" onclick="window.location.href='/webcam/menu.php'" value="Volver">
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
        $busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=? AND alumnos.id_curso=? ORDER BY Apellido1, Apellido2, Nombre', 'ss', array($id_cliente, $id_curso_actual));
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($datos_alumnos["aula"]));
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aulas["id_centro"]));
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $descarga_docs_path="/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres";
        // File names are preserved; no shell command is run on a page view.        
        
        $descarga_centro_path="/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/padres";
        // File names are preserved; no shell command is run on a page view.

?>
<table width=80% class="tabla_datos">
<?php 

$columnas=3;

$fecha=strftime("%m/%d/%Y");

$nombre_subcarpeta_actual="/";

?>
<tr>
<td class="tabla_celda_encabezado" colspan=<?=$columnas?>><font size=2><b>Documentación del aula <?=security_escape($datos_aulas["nombre"]);?> para el Curso <?=security_escape($datos_curso_actual["nombre"]);?>.<br><br>Alumno: <?=security_escape($datos_alumnos["Nombre"]);?> <?=security_escape($datos_alumnos["Apellido1"]);?> <?=security_escape($datos_alumnos["Apellido2"]);?></td>
<?php 
$contador=0;

$d = dir($descarga_docs_path.$subcarpeta_actual);

$contador_columnas=0;

      while($s=$d->read()) {

                // Busca si leido
                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_docs_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                $texto_nuevo="";
                if (db_num_rows($busca_leido)==0) {
                        $texto_nuevo="<br /><font color='#ff0000'>¡Nuevo!</font>";
                        }

                if ($contador_columnas==0) {
                        ?><tr valing="top"><?php 
                        }

        if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) and (substr($s,0,1)!=".")) {
                
                $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path$subcarpeta_actual/$s"));
        ?>
        <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><font size=2><a href="documentos/abrir_documento.php?carpeta_descarga=<?=$descarga_docs_path.$subcarpeta_actual;?>&archivo_descarga=<?=$s;?>"><img src="documentos/img/<?=mb_strtolower(substr($s,strlen($s)-3,3))?>.gif" border=0><br><?=$s;?></a><?=$fecha_archivo.$texto_nuevo?></font></td>
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
                $busca_leido=db_query_params('SELECT * FROM agenda_documentos_padres WHERE id_usuario=? AND nombre_documento=?', 'ss', array($id_usuario, (string)($descarga_centro_path) . (string)($subcarpeta_actual) . '/' . (string)($s)));
        
                $texto_nuevo="";
                if (db_num_rows($busca_leido)==0) {
                        $texto_nuevo="<br /><font color='#ff0000'>¡Nuevo!</font>";
                        }

                if ($contador_columnas==0) {
                        ?><tr valing="top"><?php 
                        }

        if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="PPSX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="XLSX")) and (substr($s,0,1)!=".")) {
                
                // Fecha modificación archivo
                $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_centro_path$subcarpeta_actual/$s"));
                
        ?>
        <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><font size=2><a href="documentos/abrir_documento.php?carpeta_descarga=<?=$descarga_centro_path;?>&archivo_descarga=<?=$s;?>"><img src="documentos/img/<?=mb_strtolower(substr($s,strlen($s)-3,3))?>.gif" border=0><br><?=$s;?></a><?=$fecha_archivo.$texto_nuevo?></font></td>
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
<td colspan=<?=$columnas?> align="center"><font size=3><b>La carpeta no contiene documentos</b></font></td></tr>
        <?php 
        }

?>
</table>
<?php 
}
?>
<a href="documentos.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Salir" name="Image3" border=0 id="Image3"></a>
</center></div>
</div>
    <?php 
    }
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
