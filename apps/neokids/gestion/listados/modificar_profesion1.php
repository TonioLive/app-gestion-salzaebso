<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_profesion=$_POST["id_profesion"];
$nombre_profesion=$_POST["nombre_profesion"];
if (strlen($nombre_profesion)!=0) {
        $nombre_profesion=urlencode($nombre_profesion);
        }

        if ($id_profesion==0) {
        $inserta_profesion=db_query_params('INSERT INTO padres_profesiones (nombre_profesion) VALUES (?)', 's', array((string)($nombre_profesion)));
        $id_profesion=db_insert_id();
        }
        
        $nuevo_articulo = db_query_params('UPDATE padres_profesiones SET nombre_profesion=? WHERE id_profesion=?', 'ss', array((string)($nombre_profesion), $id_profesion)) or die (db_error());

        
        header("Location:listado_profesiones.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

