<?php 
require("../configuracion/config.php");

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["u"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["u"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }
        
// Guardamos la foto
if (is_uploaded_file($_FILES['File1']['tmp_name'])) {

        $nombre_archivo=$_FILES['File1']['name'];
        $nombre_temp=$_FILES['File1']['tmp_name'];

        $nombre_archivo="foto_usu".$id_usuario_seleccionado.".jpg";

        if (!file_exists($upload_path."//$nombre_archivo")) {
                                                            $cambia_nombre=rename($nombre_temp, $upload_path."//$nombre_archivo");
                                                            } else {
                                                            unlink($upload_path."//$nombre_archivo");
                                                            $cambia_nombre=rename($nombre_temp, $upload_path."//$nombre_archivo");
                                                            }
} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File1']['name'];
}
// Guardamos el DNI
if (is_uploaded_file($_FILES['File2']['tmp_name'])) {

        $nombre_archivo=$_FILES['File2']['name'];
        $nombre_temp=$_FILES['File2']['tmp_name'];

        $nombre_archivo="dni_usu".$id_usuario_seleccionado.".jpg";

        if (!file_exists($upload_path_dni."//$nombre_archivo")) {
                                                            $cambia_nombre=rename($nombre_temp, $upload_path_dni."//$nombre_archivo");
                                                            } else {
                                                            unlink($upload_path_dni."//$nombre_archivo");
                                                            $cambia_nombre=rename($nombre_temp, $upload_path_dni."//$nombre_archivo");
                                                            }
} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File2']['name'];
}
// Guardamos la firma
if (is_uploaded_file($_FILES['File3']['tmp_name'])) {

        $nombre_archivo=$_FILES['File3']['name'];
        $nombre_temp=$_FILES['File3']['tmp_name'];

        $nombre_archivo="firma_usu".$id_usuario_seleccionado.".jpg";

        if (!file_exists($upload_path_firma."//$nombre_archivo")) {
                                                            $cambia_nombre=rename($nombre_temp, $upload_path_firma."//$nombre_archivo");
                                                            } else {
                                                            unlink($upload_path_firma."//$nombre_archivo");
                                                            $cambia_nombre=rename($nombre_temp, $upload_path_firma."//$nombre_archivo");
                                                            }
} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File3']['name'];
}
?>
<html>
<head>
<script Language="JavaScript">
if (self.opener.name!="nuevo"){
        self.opener.nuevo_progenitor(<?=$id_usuario_seleccionado;?>,'<?=$nombre_completo_padre;?>','<?=$usuario_DNI;?>','<?=$usuario_poblacion?>');
        } else {
        self.opener.history.go(0);
        }
window.close();
</SCRIPT>
</head>
<body>
</body>
</html>

