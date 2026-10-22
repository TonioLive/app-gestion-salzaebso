<?php
require_once __DIR__ . '/../../configuracion/deployment-config.php';
require("../../configuracion/config.php");

if (!isset($_GET['carpeta'])) {
    die("No se especificó la carpeta.");
}

$id_curso_actual=$_GET["id_curso"];
$id_cliente=$_GET["id_hijo"];
$id_aula_hijo=$_GET["id_aula"];
$id_usuario=$_GET["id_usuario"];
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
                die('Error: El alumno seleccionado no pertenece al aula indicada');
    }
    
    $busca_hijo=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario AND padre_de=$id_usuario_cliente");
    if (db_num_rows($busca_hijo)==0) {
        die('Error:  No tiene autorización para acceder a la información de este alumno');
    } else {
        // datos parentesco
        $datos_parentesco=db_fetch_array($busca_hijo);
        $parentesco=$datos_parentesco["parentesco"];
    }
} else {
    die('Error: El alumno seleccionado no existe (1)');
}

} else {
    die('Error: Alumno no existe (2)');
}
                        
if ($parentesco!=1) {
    die('Error: Esta información está restringida a los padres del alumno');
}
if ($id_curso_actual==0) {
    die("Error: El curso todavía no ha comenzado");
}
   
$baseDirectory = "";
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

} else {
    die("Error: No se ha indicado curso");
}


// $subcarpeta = $baseDirectory . "/" . basename($_GET['carpeta']); // Seguridad: basename evita path traversal PROVOCA ERROR

$subcarpeta = realpath($baseDirectory . '/' . ltrim($_GET['carpeta'], '/')); 

if ($subcarpeta === false || strpos($subcarpeta, realpath($baseDirectory)) !== 0) {
    die("Acceso no permitido.");
}

$zipFileName = 'imagenes-'.date("Ymdhis").'.zip';

if (!is_dir($subcarpeta)) {
    die("La carpeta no existe.");
}

$zip = new ZipArchive();
$zipPath = (lapices_storage_path('tmp_zip') . '/') . $zipFileName;

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("No se pudo crear el archivo ZIP.");
}

$files = glob($subcarpeta . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
foreach ($files as $file) {
    $zip->addFile($file, basename($file));
}

$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
header('Content-Length: ' . filesize($zipPath));

readfile($zipPath);
unlink($zipPath);
exit;
?>
