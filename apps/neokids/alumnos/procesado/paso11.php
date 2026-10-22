<?php 
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
?>
