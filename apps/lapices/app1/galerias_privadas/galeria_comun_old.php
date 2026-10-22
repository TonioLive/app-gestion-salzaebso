<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php"); ?>
<?php  require("parte_superior_galeria.php");
    
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
$id_curso_actual=$_GET["id_curso"];
$id_cliente=$_GET["id_hijo"];
$id_aula_hijo=$_GET["id_aula"];

$carpeta=$_GET["carpeta"];

$parentesco=0;
                                // Comprueba si es padre de este niño
                                if (strlen($id_cliente)!=0) {
                                                        
                                // Busca datos del alumno
                                $consulta_datos_del_cliente=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=".$id_cliente);
                                
                                if (db_num_rows($consulta_datos_del_cliente)!=0) {
                                        $datos_del_cliente=db_fetch_array($consulta_datos_del_cliente);
                                        $id_usuario_cliente=$datos_del_cliente["id_usuario"];
                                        $id_usuario_seleccionado=$id_usuario_cliente;
                                        
                                        if ($id_aula_hijo!=$datos_del_cliente["aula"]) {
                                                $errores="- El alumno seleccionado no pertenece al aula indicada -";
                                                }
                                        
                                                $busca_hijo=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario AND padre_de=$id_usuario_cliente");
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

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y alumnos
        $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=$id_usuario_seleccionado AND alumnos.id_curso=$id_curso_actual ORDER BY Apellido1, Apellido2, Nombre");
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_alumnos["aula"].";");
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_aulas["id_centro"]);
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $descarga_docs_path="/home/fotos_padres/$nombre_del_curso/$nombre_escuela/comun".$carpeta;

?>
<table width=80% class="tabla_datos">
<?php 

$columnas=4;

$fecha=strftime("%m/%d/%Y");

$nombre_subcarpeta_actual="/";

?>
<tr>
<td class="tabla_celda_encabezado" colspan=<?=$columnas?>><font size=2><b><?php 

if (strlen($carpeta)!=0) {
        ?>Galería: <?=app_text_utf8(substr($carpeta,1,strlen($carpeta)-1));?></b></td><?php 
        } else {
        ?>Galerías de la escuela <?=$datos_escuelas["nombre_centro"];?> para el Curso <?=$datos_curso_actual["nombre"];?>.<br><br>Alumno: <?=$datos_alumnos["Nombre"];?> <?=$datos_alumnos["Apellido1"];?> <?=$datos_alumnos["Apellido2"];?></td><?php 
        }


$contador=0;

$d = dir($descarga_docs_path);

$contador_columnas=0;

      while($s=$d->read()) {

                if ($contador_columnas==0) {
                        ?><tr valing="top"><?php 
                        }
                        
        $elemento_mostrado=false;
                        
        if (($s!=".") and ($s!="..") and (substr($s,(strlen($s)-4),1)!=".") and (strlen($carpeta)==0)) {
                // Carpeta
                $elemento_mostrado=true;
                $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path/$s"));
                ?>
                <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><font size=2><a href="galeria_comun.php?carpeta=/<?=$carpeta.$s;?>&id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_cliente?>&id_aula=<?=$id_aula_hijo?>"><img src="/webcam/documentos/img/carpeta.png" border=0><br><?=app_text_utf8($s);?></a><?=$fecha_archivo?></font></td>
                <?php 
                
                } else {
                // Imagen
                if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PNG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="GIF")) and (substr($s,0,1)!=".")) {
                        $elemento_mostrado=true;
                        $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path/$s"));
                        ?>
                        <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><font size=2><a href="#" onclick="window.open('mostrar_imagen.php?file=<?=$descarga_docs_path."/".$s?>&width=1024','imagen','width=1024,height=768');"><img src="mostrar_imagen.php?file=<?=$descarga_docs_path."/".$s?>&width=64" border=0><br><?=app_text_utf8($s);?></a><?=$fecha_archivo?></font></td>
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
<td colspan=<?=$columnas?> align="center"><font size=3><b>La carpeta no contiene archivos</b></font></td></tr>
        <?php 
        }

if (strlen($carpeta)!=0) {
        ?>
        <tr>
<td colspan=<?=$columnas?> align="center"><font size=3><br><br><a href='galeria_comun.php?id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_cliente?>&id_aula=<?=$id_aula_hijo?>'><b>Volver al listado de galerías</b></a></font><br><br><br></td></tr>
        <?php 
        }

?>
</table>
<?php 
}
?>
<a href="/webcam/documentos.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Salir" name="Image3" border=0 id="Image3"></a>
</center></div>
</div>
    <?php 
    }
    }
    ?></div>
<?php  require("../../configuracion/parte_inferior_padres.php"); ?>
