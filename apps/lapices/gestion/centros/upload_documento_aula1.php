<?php
require_once __DIR__ . '/../../configuracion/deployment-config.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
        if (db_num_rows($busca_datos_aula)!=0) {
                $datos_aula=db_fetch_array($busca_datos_aula);
                $nombre_aula=$datos_aula["nombre"];
                $id_curso_actual=$datos_aula["id_curso"];
                $id_centro_aula=$datos_aula["id_centro"];
                }

       $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y centro
        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula.";");
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$id_centro_aula);
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $descarga_docs_path=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres");
        
		$sustituir=array("Á", "À", "Â", "Ä", "á", "à", "ä", "â", "ª","É", "È", "Ê", "Ë", "é", "è", "ë", "ê","Í", "Ì", "Ï", "Î", "í", "ì", "ï", "î","Ó", "Ò", "Ö", "Ô", "ó", "ò", "ö", "ô","Ú", "Ù", "Û", "Ü", "ú", "ù", "ü", "û","Ñ", "ñ", "Ç", "ç");	
									
		$descarga_docs_path=str_replace($sustituir,"_",$descarga_docs_path);      

$carpeta_docs="$descarga_docs_path/";

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
        header("Location: ver_carpeta_aula.php?id_aula=$id_aula");
        }

} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File1']['name'];
}
?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
<p align="center">
<font face="Arial"><br><br><br>
<b>Error: Solo se permiten documentos de Adobe Acrobat (PDF),Microsoft Word (DOC o RTF), Microsoft Excel (XLS) y PowerPoint (PPT o PPS)
<br><br><br><input name="Volver" type="button" id="Cancelar" tabindex="8" value="Cancelar" onclick="window.location.href='ver_carpeta_aula.php?id_aula=<?=$id_aula?>'" />
</font></p>
</body>
</html>

