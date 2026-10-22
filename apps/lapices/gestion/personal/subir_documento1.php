<?php 
require("../../configuracion/config.php");

require("../../configuracion/autentifica_docente.php");

require("config/config_personal.php");

$id_doc=$_GET["id_doc"];
$id_usu=$_GET["id_usu"];
$id_contrato=$_GET["id_contrato"];

if (is_uploaded_file($_FILES['File1']['tmp_name'])) {


// Busca plantilla
$busca_doc_prl=db_query("SELECT * FROM personal_tipos_doc_prevencion WHERE id_tipo_doc=$id_doc");

$doc_prl=db_fetch_array($busca_doc_prl);

$nombre_plantilla=$doc_prl["nombre_plantilla"];
$nombre_doc=$doc_prl["nombre_doc"];

$carpeta_documentos_actual=$carpeta_documentos."/usuario_$idUsuario";

$nombre_nuevo_archivo=$carpeta_documentos_actual."/$nombre_doc"."_".$id_usu."_".$id_contrato."_".date("d-m-Y").".";

        $nombre_archivo1=$_FILES['File1']['name'];
        $nombre_temp=$_FILES['File1']['tmp_name'];

        if (strlen($nombre_archivo1)!=0) {
                $extension_documento=substr($nombre_archivo1,strlen($nombre_archivo1)-3,3);
                }


        $nombre_nuevo_archivo=$nombre_nuevo_archivo.$extension_documento;

        if (!file_exists($nombre_nuevo_archivo)) {
                                                $cambia_nombre=copy($nombre_temp, $nombre_nuevo_archivo);
                                                } else {
                                                unlink($nombre_nuevo_archivo);
                                                $cambia_nombre=copy($nombre_temp, $nombre_nuevo_archivo);
                                                }

} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File1']['name'];
}
?>
<html>
<head>
<script Language="JavaScript">
self.location.href='ficha.php';
</SCRIPT>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

