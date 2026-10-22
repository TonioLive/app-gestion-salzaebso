<?php
require_once __DIR__ . '/../../../configuracion/deployment-config.php'; 
    // ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
    // error_reporting(E_ALL ^ E_NOTICE);
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_aula=$_POST["id_aula"];
$carpeta=$_POST["carpeta"];

$nombre_carpeta_original=$_POST["nombre_carpeta"];

// Limpia nombre carpeta
if (!function_exists('limpiarCadena')) {
    function limpiarCadena($cadena) {
        // Tabla de transliteración manual compatible con versiones antiguas de PHP
        $transliteracion = array(
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ý' => 'Y', 'Þ' => 'TH', 'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
            'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'þ' => 'th', 'ÿ' => 'y',
            'Œ' => 'OE', 'œ' => 'oe', 'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z'
        );
        
        // Reemplazar caracteres acentuados usando strtr()
        $cadena = strtr($cadena, $transliteracion);

        // Reemplazar los espacios por guiones
        $cadena = str_replace(' ', '-', $cadena);

        // Eliminar todos los caracteres que no sean letras, números, guiones o puntos
        $cadena = preg_replace('/[^A-Za-z0-9\-.]/', '', $cadena);

        return $cadena;
    }
}

// Limpia nobre carpeta
$nombre_carpeta = limpiarCadena($nombre_carpeta_original);

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
$nueva_carpeta_path=(lapices_storage_path('fotos_padres') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula").$carpeta."/".$nombre_carpeta;

mkdir($nueva_carpeta_path);

header("Location: index.php?carpeta=".$carpeta."/".$nombre_carpeta."&id_aula=$id_aula");
?>