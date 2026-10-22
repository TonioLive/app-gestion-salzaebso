<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$errores=0;

$id_plato=$_GET["id_plato"];
$subcarpeta_actual=$_GET["sub"];

$descarga_docs_path=$upload_path_platos;
$carpeta_docs="$descarga_docs_path$subcarpeta_actual/";

if (is_uploaded_file($_FILES['File1']['tmp_name'])) {

        $nombre_archivo1=$_FILES['File1']['name'];
        $nombre_temp=$_FILES['File1']['tmp_name'];

        if (strlen($nombre_archivo1)>3) {
        		$nombre_archivo1=trim($nombre_archivo1);
                $extension_documento=strtolower(substr($nombre_archivo1,strlen($nombre_archivo1)-3,3));
                }

		
        if ($extension_documento=="jpg") {

        $nombre_archivo=$id_plato.".jpg";
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

} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File1']['name'];
}

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" type="text/css" media="screen" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

<title>Subir imagen del plato</title>
<style type="text/css">
<!--
body {
        margin-left: 0px;
        margin-top: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
}
-->
</style>
<link href="/css/ccs_prosel.css" rel="stylesheet" type="text/css" />
<?php 
if ($errores==0) {
?>
<script>
self.opener.document.getElementById('foto_plato').src='/fotos_platos/<?=$id_plato?>.jpg?' + new Date().getTime();;
window.close();
</script>
<?php 
	}
?>
</head>
<body>
<p align="center">
<font face="Arial"><br><br><br>
<?php 
if ($errores!=0) {
?>
<b>Error: Solo se permiten imágenes JPG
<br><br><br><input name="Volver" type="button" class="btn btn-primary btn-default" id="Volver" tabindex="8" value="Volver a intentarlo" onclick="window.location.href='upload_documento.php?id_plato=<?=$id_plato?>'" /> <?php  } ?><input name="Salir" type="button" class="btn btn-primary btn-default" id="Salir" tabindex="8" value="Salir" onclick="window.close();" />
</font></p>
</body>
</html>