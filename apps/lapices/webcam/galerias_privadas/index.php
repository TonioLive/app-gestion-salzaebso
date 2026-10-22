<?php
require_once __DIR__ . '/../../configuracion/deployment-config.php';
require_once __DIR__ . '/../../configuracion/encoding.php';
ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

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
        $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=$id_cliente AND alumnos.id_curso=$id_curso_actual ORDER BY Apellido1, Apellido2, Nombre");
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_alumnos["aula"].";");
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_aulas["id_centro"]);
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $baseDirectory = (lapices_storage_path('fotos_padres') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula");
        $descarga_docs_path=$baseDirectory.$carpeta;            
        $descarga_centro_path=(lapices_storage_path('fotos_padres') . "/$nombre_del_curso/$nombre_escuela/comun");

		$sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');						
		$descarga_docs_path=str_replace($sustituir,"_",$descarga_docs_path); 
		$descarga_centro_path=str_replace($sustituir,"_",$descarga_centro_path); 		
	
                
        // Comprueba acceso inapropiado..
        $carpetaRaiz=false;

        // Directorio base en el que permites acceder a carpetas

        // Obtenemos el nombre de la carpeta desde GET o POST
        $folderName = isset($_GET['carpeta']) ? $_GET['carpeta'] : null;

        if (realpath($descarga_docs_path)==realpath($baseDirectory)) {
                $carpetaRaiz=true;
                // Resetea variable $carpeta
                $carpeta = "";
        }

        // Verificamos si el nombre de la carpeta existe y no está vacío
        if ($folderName && is_string($folderName)) {
        // Normalizamos el nombre de la carpeta
        $folderPath = realpath($baseDirectory . $folderName);

        // Validamos que el directorio exista y esté dentro del directorio base
        if ($folderPath && strpos($folderPath, $baseDirectory) === 0) {
                // Listamos el contenido de la carpeta segura
                // OK
        } else {
                die('Acceso denegado: Carpeta no válida.');
        }
        }

        // Nombre carpeta mostrado el real no el relativo
        $nombreCarpetaMostrado = str_replace($baseDirectory, '', realpath($descarga_docs_path));

?>
        <table width=98% class="tabla_datos">
        <?php 

        if (file_exists($descarga_docs_path)) {                              

$columnas=2;

$fecha=strftime("%m/%d/%Y");

$nombre_subcarpeta_actual="/";
?>
<tr>
<td class="tabla_celda_encabezado" colspan=<?=$columnas?>><font size=2><?php 

if (strlen($carpeta)!=0) {
        ?><b>Galería: <?=app_text_utf8(substr($nombreCarpetaMostrado,1,strlen($nombreCarpetaMostrado)-1));?></b><?php
        if ($carpetaRaiz==false) {
                echo(" <a class='btn btn-md btn-default' style='margin:20px!important;' href='index.php?carpeta=".$carpeta."/.."."&id_curso=".$id_curso_actual."&id_hijo=".$id_cliente."&id_aula=".$id_aula_hijo."'>Salir a carpeta superior <i class='fa fa-level-up' aria-hidden='true'></i></a>");
        }
        ?></td><?php 
        } else {
        ?><b>Galerías del aula <?=$datos_aulas["nombre"];?> para el Curso <?=$datos_curso_actual["nombre"];?>.<br><br>Alumno:</b> <?=$datos_alumnos["Nombre"];?> <?=$datos_alumnos["Apellido1"];?> <?=$datos_alumnos["Apellido2"];?><?php 
        }

        // if (($carpeta != "") and ($_SERVER['REMOTE_ADDR']=='79.116.187.213')) {
        if ($carpeta != "") {
            echo(" <a class='btn btn-md btn-default' style='margin:20px!important;' target='_blank' href='descargar_zip.php?carpeta=".$carpeta."&id_curso=".$id_curso_actual."&id_hijo=".$id_cliente."&id_aula=".$id_aula_hijo."&id_usuario=".$id_usuario."'>Descargar las fotos de esta carpeta<br />comprimidas en un archivo ZIP</a>");
        }
?>
</td>
<?php
// NUEVO CODIGO
$directorio = realpath($descarga_docs_path);
$fechaCorte = '2025-02-13'; // Fecha de corte, solo se marcarán nuevas las imágenes modificadas después de esta fecha

// Obtener imágenes vistas previamente por el usuario
$query = db_query("SELECT nombre_imagen FROM registro_vista_imagenes WHERE id_usuario = ".$id_usuario);
$imagenes_vistas = [];
while ($row = db_fetch_array($query)) {
    $imagenes_vistas[] = $row['nombre_imagen'];
}

/**
 * Función recursiva para listar imágenes en una carpeta y verificar si son nuevas.
 *
 * @param string $directorio El directorio a procesar.
 * @param array $imagenes_vistas Las imágenes vistas previamente.
 * @param string $criterioOrden Criterio de ordenación ('nombre' o 'fecha').
 * @param bool $registrar Indica si se deben marcar las imágenes como vistas.
 * @param string $fechaCorte Fecha de corte para marcar imágenes como nuevas.
 * @return array Listado con imágenes y carpetas (incluyendo subcarpetas).
 */

function listarArchivosRecursivos($directorio, $imagenes_vistas, $criterioOrden, $registrar, $fechaCorte) {
    $elementos = array_diff(scandir($directorio), array('.', '..')); // Excluir '.' y '..'
    $listado = array();
    $imagenes_a_registrar = array();

    foreach ($elementos as $elemento) {
        $rutaCompleta = $directorio . '/' . $elemento;
        if (is_dir($rutaCompleta)) {
            // Procesar subcarpeta
            $sublistado = listarArchivosRecursivos($rutaCompleta, $imagenes_vistas, $criterioOrden, false, $fechaCorte);
            $numNuevas = 0;
            foreach ($sublistado as $subelemento) {
                $numNuevas += isset($subelemento['nuevas']) ? $subelemento['nuevas'] : 0;
            }
            $listado[] = array(
                'nombre' => $elemento,
                'tipo' => 'carpeta',
                'nuevas' => $numNuevas,
                'contenido' => $sublistado, // Detalle de contenido de la carpeta
            );
        } else {
            // Verificar si es una imagen
            $extensionesImagen = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
            $extension = pathinfo($elemento, PATHINFO_EXTENSION);      
            if (in_array(strtolower($extension), $extensionesImagen)) {
                // Comprobar si la imagen fue vista antes y si su fecha es posterior a la fecha de corte
                $esNuevo = !in_array($rutaCompleta, $imagenes_vistas) && (filemtime($rutaCompleta) > strtotime($fechaCorte));                      
                if ($esNuevo && $registrar) {
                    $imagenes_a_registrar[] = $rutaCompleta;
                }
                $listado[] = array(
                    'nombre' => $elemento,
                    'ruta' => $rutaCompleta,
                    'tipo' => 'imagen',
                    'nuevas' => $esNuevo ? 1 : 0,
                );
            }
        }
    }

    // Registrar imágenes nuevas (solo en la carpeta actual)
    if ($registrar && !empty($imagenes_a_registrar)) {
        foreach ($imagenes_a_registrar as $imagen) {
            $query = db_query("INSERT INTO registro_vista_imagenes (nombre_imagen, fecha_lectura, hora_lectura, id_usuario) VALUES ('$imagen', CURDATE(), CURTIME(), '".$GLOBALS['id_usuario']."')");
        }
    }

    // Ordenar elementos según el criterio
    usort($listado, function($a, $b) use ($criterioOrden, $directorio) {
        // Carpeta primero
        if ($a['tipo'] === 'carpeta' && $b['tipo'] === 'imagen') {
            return -1;
        }
        if ($a['tipo'] === 'imagen' && $b['tipo'] === 'carpeta') {
            return 1;
        }

        // Ordenar por nombre o fecha
        if ($criterioOrden === 'fecha') {
            $rutaA = $directorio . '/' . $a['nombre'];
            $rutaB = $directorio . '/' . $b['nombre'];
            $fechaA = filemtime($rutaA);
            $fechaB = filemtime($rutaB);
            return $fechaA === $fechaB ? 0 : ($fechaA < $fechaB ? -1 : 1);
        }
        return strcasecmp($a['nombre'], $b['nombre']); // Orden alfabético
    });

    return $listado;
}

// Obtener listado recursivo de la carpeta principal
$listadoCompleto = listarArchivosRecursivos($directorio, $imagenes_vistas, $criterioOrden, true, $fechaCorte);

// FIN NUEVO CODIGO

$contador=0;
$contador_columnas=0;
// $d = dir($descarga_docs_path);

      foreach($listadoCompleto as $elementoLista) {
      // while($s=$d->read()) {
        
      $s = $elementoLista['nombre'];

                if ($contador_columnas==0) {
                        ?><tr valing="top"><?php 
                        }
                        
        $elemento_mostrado=false;
                        
        // Omitimos los directorios especiales '.' y '..'
        if ($s !== '.' && $s !== '..') {
                $filePath = $descarga_docs_path . '/' . $s;
    
                // Comprobamos si es un directorio o un archivo
                if (is_dir($filePath)) {
                        // Carpeta
                        // Compruebo si contiene imágenes
                        if (containsImages($filePath, $imageExtensions)) {
                                // La carpeta o alguna de sus subcarpetas contiene imágenes y se puede mostrar                                                       
                                $imagenesNuevas = $elementoLista['nuevas'];

                                $elemento_mostrado=true;
                                $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path/$s"));
                                ?>
                                <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%">
                                    <div style="position: relative; display: inline-block;">
                                        <?php 
                                        // Mostrar badge si hay imágenes nuevas
                                        if ($imagenesNuevas > 0): ?>
                                            <span class="badge" style="position: absolute; top: 10px; left: -40px; background-color: #f0ad4e; color: #fff; font-size: 14px; padding: 5px 10px; z-index: 10;">
                                                <?php echo ($imagenesNuevas == 1) ? '¡1 Nueva!' : '¡' . $imagenesNuevas . ' Nuevas!'; ?>
                                            </span>
                                        <?php endif; ?>                                                                                   
                                        <a href="index.php?carpeta=<?=$carpeta."/".$s;?>&id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_cliente?>&id_aula=<?=$id_aula_hijo?>">
                                        <img src="/webcam/documentos/img/carpeta.png" border=0 class="img-responsive">
                                        <br><?=app_text_utf8($s);?></a>
                                        <?=$fecha_archivo?>
                                    </div>
                                </td>
                                <?php 
                        }
                } else {
                        // Imagen
                        if (((mb_strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="JPEG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PNG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="GIF")) and (substr($s,0,1)!=".")) {
                                $elemento_mostrado=true;
                                $fecha_archivo=date("d/m/Y",filectime("$descarga_docs_path/$s"));
                                ?>


                                <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%">
                                    <?php
                                    $nueva = $elementoLista['nuevas']; // Bandera para imágenes nuevas
                                    ?>

                                    <!-- Contenedor para la imagen -->
                                    <div style="position: relative; display: inline-block;">

                                    <?php if ($nueva==1): ?>
                                            <span class="badge" style="position: absolute; top: 10px; left: 10px; background-color: #f0ad4e; color: #fff; font-size: 14px; padding: 5px 10px; z-index: 10;">¡Nueva!</span>
                                        <?php endif; ?>

                                        <!-- Enlace para abrir la imagen al hacer clic -->
                                        <a href="#" onclick="mostrar_imagen('<?=$descarga_docs_path."/".$s?>')">
                                            <img class="img-responsive" border="0" 
                                                src="mostrar_imagen.php?file=<?=$descarga_docs_path."/".$s?>&width=150" 
                                                style="max-width: 150px!important; height: auto;">
                                        </a>
                                        <!-- Nombre de la imagen y fecha -->
                                        <?=$fecha_archivo;?>                                        
                                    </div>
                                </td>
                                <?php 
                        }
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
// $d->close();

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
<td colspan=<?=$columnas?> align="center"><font size=3><br><br><a class="btn btn-primary" href='index.php?id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_cliente?>&id_aula=<?=$id_aula_hijo?>'><b><font color="#fff">Volver al listado de galerías</a></b></a></font><br><br><br></td></tr>
        <?php 
        }

?>
</table>
<?php 
        } else {
                echo("<h3>No hay imágenes</h3>");    
        }
} // Curso actual

} // errores
?>
<a href="/webcam/documentos.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Salir" name="Image3" border=0 id="Image3"></a>
</center></div>
</div>
    <?php 
    }
    ?></div>
    
		<script type="text/javascript">
			$(document).ready(function ($) {

				// delegate calls to data-toggle="lightbox"
				$(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
					event.preventDefault();
					return $(this).ekkoLightbox({
						always_show_close: true
					});
				});

			});

            function mostrar_imagen(imagen) {
                $("#ampliarImagen .modal-body").html("<img src='mostrar_imagen.php?file="+imagen+"&width=1024' class='img-responsive' border='0'>");
                $("#ampliarImagen .modal-footer").html("<a href='descargar_imagen.php?file="+imagen+"' class='btn btn-default btn-xs pull-right'>Descargar imagen de tamaño completo</a>");
                $("#ampliarImagen").modal('show');
            }
        </script>   
        
        <div class="modal fade" id="ampliarImagen" tabindex="-1" role="dialog" aria-labelledby="ampliarImagenLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="ampliarImagenBody">
      </div>
      <div class="modal-footer" id="ampliarImagenFooter">
      </div>      
    </div>
  </div>
</div>        
<?php  require("../../configuracion/parte_inferior_padres.php"); ?>