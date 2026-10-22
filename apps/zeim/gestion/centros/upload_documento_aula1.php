<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Limpia nombre archivo
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

$id_aula=$_GET["id_aula"];
$busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
        if (db_num_rows($busca_datos_aula)!=0) {
                $datos_aula=db_fetch_array($busca_datos_aula);
                $nombre_aula=$datos_aula["nombre"];
                $id_curso_actual=$datos_aula["id_curso"];
                $id_centro_aula=$datos_aula["id_centro"];
                }

       $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y centro
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($id_aula));
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro_aula));
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $descarga_docs_path="/var/www/vhosts/escuelainfantilsantpau.com/documentacion/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres";


$carpeta_docs="$descarga_docs_path/";

if (is_uploaded_file($_FILES['File1']['tmp_name'])) {

        $nombre_archivo1=$_FILES['File1']['name'];
        $nombre_temp=$_FILES['File1']['tmp_name'];

        if (strlen($nombre_archivo1)>3) {
                $extension_documento=strtolower(substr($nombre_archivo1,strlen($nombre_archivo1)-3,3));
                }


        if (($extension_documento=="pdf") or ($extension_documento=="doc") or ($extension_documento=="rtf") or ($extension_documento=="ppt") or ($extension_documento=="pps") or ($extension_documento=="xls")) {

        $nombre_archivo=limpiarCadena($nombre_archivo1);
        // $nombre_archivo=urlencode($nombre_archivo);
        if (!file_exists($carpeta_docs.$nombre_archivo)) {
                                                            $cambia_nombre=copy($nombre_temp, $carpeta_docs.$nombre_archivo);
                                                            } else {
                                                            unlink($carpeta_docs.$nombre_archivo);
                                                            $cambia_nombre=copy($nombre_temp, $carpeta_docs.$nombre_archivo);
                                                            }

        } else {
        $errores=1;
        }
        
if ($errores==0) {
        header("Location: ver_carpeta_aula.php?id_aula=$id_aula");
        }

} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File1']['name'];
}
?>
<html>
<head>
</head>
<body>
<p align="center">
<font face="Arial"><br><br><br>
<b>Error: Solo se permiten documentos de Adobe Acrobat (PDF),Microsoft Word (DOC o RTF), Microsoft Excel (XLS) y PowerPoint (PPT o PPS)
<br><br><br><input name="Volver" type="button" id="Cancelar" tabindex="8" value="Cancelar" onclick="window.location.href='ver_carpeta_alumno.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$subcarpeta_actual?>'" />
</font></p>
</body>
</html>

