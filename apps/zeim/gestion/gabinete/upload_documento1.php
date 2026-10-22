<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_curso_actual=$_GET["id_curso_alumno"];
$id_usuario_seleccionado=$_GET["id_alumno"];
$subcarpeta_actual=$_GET["sub"];

       $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y alumnos
        $busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=? AND alumnos.id_curso=? ORDER BY Apellido1, Apellido2, Nombre', 'ss', array($id_usuario_seleccionado, $id_curso_actual));
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($datos_alumnos["aula"]));
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aulas["id_centro"]));
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $descarga_docs_path="/var/www/vhosts/escuelainfantilsantpau.com/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno";


$carpeta_docs="$descarga_docs_path$subcarpeta_actual/";

if (is_uploaded_file($_FILES['File1']['tmp_name'])) {

        $nombre_archivo1=$_FILES['File1']['name'];
        $nombre_temp=$_FILES['File1']['tmp_name'];

        if (strlen($nombre_archivo1)>3) {
                $extension_documento=strtolower(substr($nombre_archivo1,strlen($nombre_archivo1)-3,3));
                }


        if (($extension_documento=="pdf") or ($extension_documento=="doc") or ($extension_documento=="rtf") or ($extension_documento=="ppt") or ($extension_documento=="pps") or ($extension_documento=="xls")) {

        $nombre_archivo=$nombre_archivo1;
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
        header("Location: ver_carpeta_alumno.php?id_alumno=$id_usuario_seleccionado&id_curso_alumno=$id_curso_actual&sub=$subcarpeta_actual");
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

