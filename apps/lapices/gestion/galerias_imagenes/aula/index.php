<?php
require_once __DIR__ . '/../../../configuracion/deployment-config.php';
require_once __DIR__ . '/../../../configuracion/encoding.php';
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$carpeta=$_GET["carpeta"];
        
// datos aula y centro
$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula.";");
$datos_aulas=db_fetch_array($busca_aulas);
$busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_aulas["id_centro"]);
$datos_escuelas=db_fetch_array($busca_escuelas);
$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_aulas["id_curso"]);
$datos_curso_actual=db_fetch_array($busca_curso_actual);

$nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
$nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$datos_aulas["id_curso"];
$nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
$nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
$baseDirectory = (lapices_storage_path('fotos_padres') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula");
$descarga_docs_path=$baseDirectory.$carpeta;
$descarga_centro_path=(lapices_storage_path('fotos_padres') . "/$nombre_del_curso/$nombre_escuela/comun");

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

$columnas=1;

$fecha=strftime("%m/%d/%Y");

$nombre_subcarpeta_actual="/";

// Comprobar nivel de profundidad subcarpetas
$crearCarpetas=true;
$nivelMaximo=2; // (Empieza a contar de 0)

// Normalizamos las rutas con `realpath()` para evitar inconsistencias
$rootPath = realpath($baseDirectory);
$currentPath = realpath($descarga_docs_path);

// Aseguramos que el directorio actual está dentro del directorio raíz
if ($rootPath && $currentPath && strpos($currentPath, $rootPath) === 0) {
    // Eliminamos la ruta del directorio raíz de la ruta actual
    $relativePath = str_replace($rootPath, '', $currentPath);
    
    // Dividimos el camino restante en sus componentes y contamos los elementos
    $depthLevel = substr_count(trim($relativePath, '/'), '/');    

    if ($depthLevel>=2) {
        $crearCarpetas=false;
    }
} 

// Nombre carpeta mostrado el real no el relativo
$nombreCarpetaMostrado = str_replace($baseDirectory, '', $currentPath);

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$nombre_centro;?> - Galería de Imágenes</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/chosen.css">
<link rel="stylesheet" href="/css/datepicker.css">
<link rel="stylesheet" type="text/css" href="uploadifive.css">
<style type="text/css">
.myField{
    width:32%;
    display:inline-block;

	}
.myField75{
    width:50%;
    display:inline-block;
	}
#queue {
	border: 1px solid #E5E5E5;
	height: auto;
	overflow: auto;
	margin-bottom: 10px;
	padding: 0 3px 3px;
	width: 300px;
}  

.uploadifive-button {
        width:150px!important;
}

</style>	


<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script src="/js/bootstrap-datepicker.js"></script>
<script src="/js/locales/bootstrap-datepicker.es.js"></script>
<script src="/inc/validacion_fechas.js" type="text/javascript"></script>
<script src="jquery.min.js" type="text/javascript"></script>
<script src="jquery.uploadifive.js" type="text/javascript"></script>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading">
			<?php
                        if (strlen($carpeta)!=0) {
                                ?><h3>Galería: <?=app_text_utf8(substr($nombreCarpetaMostrado,1,strlen($nombreCarpetaMostrado)-1));?></h3><?php 
                                if ($carpetaRaiz==false) {
                                        echo(" <a class='btn btn-md btn-default' style='margin:20px!important;' href='index.php?carpeta=".$carpeta."/.."."&id_aula=".$id_aula."'>Salir a carpeta superior <i class='fa fa-level-up' aria-hidden='true'></i></a>");
                                }
                                ?></td><?php 
                                } else {
                                        ?><h3>Galerías del aula <?=$datos_aulas["nombre"];?> para el Curso <?=$datos_curso_actual["nombre"];?>.<h3><?php                        
                                }
                        ?>
		</div>
  		<div class="panel-body">
                        <div class="col-md-6 text-center"> <!-- Columna principal -->
                        <?php
                        if (file_exists($descarga_docs_path)) {                      
                                if ($crearCarpetas==true) {
                                ?>
                                <form method="POST" action="nueva_carpeta.php" id="form_carpetas" name="form_carpetas" class="form-inline">
                                        <input type="hidden" name="id_aula" value="<?=$id_aula?>">
                                        <input type="hidden" name="carpeta" value="<?=$carpeta?>">
                                        <div class="form-group">
                                                <label for="nombre_carpeta">Nueva carpeta</label>
                                                <input type="text" class="form-control" id="nombre_carpeta" name="nombre_carpeta" placeholder="Nueva carpeta">
                                        </div>
                                        <button type="button" onclick="crear_carpeta();" class="btn btn-default">Crear carpeta</button>
                                </form>
                                <?php
                                }        
                        ?>
                        <table class="table">
                        <?php

                        $contador=0;

                        $d = dir($descarga_docs_path);

                        $contador_columnas=0;

                        while($s=$d->read()) {

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
                                        $elemento_mostrado=true;
                                        $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path/$s"));
                                        ?>
                                        <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%"><font size=2><a href="index.php?carpeta=<?=$carpeta."/".$s;?>&id_aula=<?=$id_aula?>"><img src="/webcam/documentos/img/carpeta.png" border=0 class="img-responsive"><br><?=app_text_utf8($s);?></a><?=$fecha_archivo?></font></td>
                                        <?php 
                                        
                                        } else {
                                        // Imagen
                                        if (((mb_strtoupper(substr($s,strlen($s)-4,4))=="JPEG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PNG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="GIF")) and (substr($s,0,1)!=".")) {
                                                $elemento_mostrado=true;
                                                $fecha_archivo="<br />".date("d/m/Y",filectime("$descarga_docs_path/$s"));
                                                ?>
                                                <td class="tabla_celda_datos" align="center" width="<?=floor(100/$columnas);?>%">

                                                        <a href="#" onclick="mostrar_imagen('<?=$descarga_docs_path."/".$s?>')">
                                                        <img class="img-responsive" border="0" src="mostrar_imagen.php?file=<?=$descarga_docs_path."/".$s?>&width=225" style="max-width: 225px!important; height: auto;"></a>
                                                        <?=app_text_utf8($s);?> <?=$fecha_archivo;?><br />
                                                        <button type="button" class="btn btn-success btn-md" onclick="rotar_imagen('<?=$descarga_docs_path."/".$s?>','<?=$s?>','90')">Rotar 90º a la izq.</button> <button type="button" class="btn btn-success btn-md" onclick="rotar_imagen('<?=$descarga_docs_path."/".$s?>','<?=$s?>','-90')">Rotar 90º a la der.</button><br />
                                                        <button style="margin-top:5px!important;" type="button" class="btn btn-danger btn-md" onclick="eliminar_imagen('<?=$descarga_docs_path."/".$s?>','<?=$s?>')">Eliminar</button>	

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
                        $d->close();

                        if ($contador_columnas<$columnas) {
                                                ?><td colspan="<?=($columnas-$contador_columnas)?>"></td></tr><?php 
                                                }

                        if ($contador==0) {
                                ?>
                                <tr>
                        <td colspan=<?=$columnas?> align="center"><font size=3><b>La carpeta no contiene archivos</b></font><br/><br />
                        <?php
                        if (strlen($carpeta)!=0) {
                        ?>
                        <button type="button" class="btn btn-danger btn-md" onclick="eliminar_carpeta('<?=$descarga_docs_path?>','<?=$carpeta?>')">Eliminar carpeta</button>	
                        <?php
                        }
                        ?>
                        </td></tr>
                                <?php 
                                }

                        if (strlen($carpeta)!=0) {
                                ?>
                                <tr>
                        <td colspan=<?=$columnas?> align="center"><font size=3><br><br><a href='index.php?id_aula=<?=$id_aula;?>'><b>Volver al listado de galerías</b></a></font><br><br><br></td></tr>
                                <?php 
                                }

                        ?>
                        </table>
                        <?php 
                        } else {
                                echo("<h3>No hay imágenes</h3>");    
                        }
                        ?>
                        <br /><a href="/" class="btn btn-responsive btn-md btn-primary"><i class="fa fa-backward"></i> Salir</a><br /><br />

                        </div>

                        <script>
                                function mostrar_imagen(imagen) {
                                        $("#ampliarImagen .modal-body").html("<img src='mostrar_imagen.php?file="+imagen+"&width=1024' class='img-responsive' border='0'>");
                                        $("#ampliarImagen").modal('show');
                                }
                                function eliminar_imagen(imagen,nombreimagen) {
                                        if (confirm("¿Quieres eliminar la imagen \""+nombreimagen+"\"?")) {
                                                window.location.href='eliminar_imagen.php?id_aula=<?=$id_aula?>&carpeta=<?=$carpeta?>&imagen='+imagen;
                                        }
                                }
                                function rotar_imagen(imagen,nombreimagen,angulo) {
                                        if (confirm("¿Quieres rotar la imagen \""+nombreimagen+"\"?")) {
                                                window.location.href='rotar_imagen.php?id_aula=<?=$id_aula?>&carpeta=<?=$carpeta?>&imagen='+imagen+'&angulo='+angulo;
                                        }
                                }                                
                                function eliminar_carpeta(carpeta,nombrecarpeta) {
                                        if (confirm("¿Quieres eliminar la carpeta \""+nombrecarpeta+"\"?")) {
                                                window.location.href='eliminar_carpeta.php?id_aula=<?=$id_aula?>&carpeta='+carpeta;
                                        }
                                }
                                function crear_carpeta() {
                                        var error = '';
                                        if ($('#nombre_carpeta').val()=='') {
                                                error = 'No has introducido el nombre de la nueva carpeta';
                                        }

                                        if (error=='') {
                                                $('#form_carpetas').submit();
                                        } else {
                                                alert(error);
                                        }

                                        
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
                        </div>
                        </div>
                        </div>
                <div class="col-md-6">
                <form>
                <?php
                if ($carpetaRaiz==true) {
                        ?>
                        <div class="text-center">
                                <div class="alert alert-danger" role="alert">No se pueden subir imágenes en el directorio raiz.</div>
                                <div class="alert alert-info" role="alert">Entra en la carpeta adecuada o crea una carpeta propia para subir las imágenes en ella.<br /><br />Las carpetas no deben contener un número excesivo de imágenes, para mantenerlas ordenadas y facilitar a los padres la búsqueda.</div>
                        </div>
                        <?php
                } else {
                ?>
		<div id="queue"></div>
                        <input id="file_upload" name="file_upload" type="file" multiple="true">
                        <a style="position: relative; top: 8px;" class="btn btn-primary" href="javascript:$('#file_upload').uploadifive('upload')">Subir Fotos</a>
                        <button type="button" style="position: relative; top: 8px;" class="btn btn-primary" onclick="history.go(0);">Recargar página</a>
                </form>
                <script type="text/javascript">
                        <?php $timestamp = time();?>
                        $(function() {
                                $('#file_upload').uploadifive({
                                        'auto'             : false,
                                        'checkScript'      : 'check-exists.php',
                                        'fileType'         : '.jpg,.jpeg,.gif,.png',
                                        'formData'         : {
                                                                'timestamp' : '<?php echo $timestamp;?>',
                                                                'token'     : '<?php echo md5('unique_salt' . $timestamp);?>',
                                                                'id_aula'   : '<?php echo $id_aula;?>',
                                                                'carpeta'   : '<?php echo $carpeta;?>', 
                                                        },
                                        'buttonText'       : 'Seleccionar fotos',   
                                        'queueID'          : 'queue',
                                        'uploadScript'     : 'uploadifive.php',
                                        // 'onUploadComplete' : function(file, data) { history.go(0); }
                                });
                        });
                </script>                        
                <?php
                }
                ?>
                </div>
		</div>
	</div>    
</div>
</body>
</html>