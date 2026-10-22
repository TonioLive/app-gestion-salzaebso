<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_profesion=$_POST["id_profesion"];
$nombre_profesion=$_POST["nombre_profesion"];
if (strlen($nombre_profesion)!=0) {
        $nombre_profesion=urlencode($nombre_profesion);
        }

        if ($id_profesion==0) {
        $inserta_profesion=db_query("INSERT INTO padres_profesiones (nombre_profesion) VALUES ('$nombre_profesion')");
        $id_profesion=db_insert_id();
        }
        
        $nuevo_articulo = db_query("UPDATE padres_profesiones SET nombre_profesion='$nombre_profesion' WHERE id_profesion=$id_profesion") or die (db_error());

        
        header("Location:listado_profesiones.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

