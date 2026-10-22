<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_motivo=$_POST["id_motivo"];
$nombre_motivo=$_POST["nombre_motivo"];
if (strlen($nombre_motivo)!=0) {
        $nombre_motivo=urlencode($nombre_motivo);
        }

        if ($id_motivo==0) {
        $inserta_motivo=db_query_params('INSERT INTO contactos_motivos_cancelacion (nombre_motivo) VALUES (?)', 's', array((string)($nombre_motivo)));
        $id_motivo=db_insert_id();
        }
        
        $nuevo_articulo = db_query_params('UPDATE contactos_motivos_cancelacion SET nombre_motivo=? WHERE id_motivo=?', 'ss', array((string)($nombre_motivo), $id_motivo)) or die (db_error());

        
        header("Location:listado_motivos.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

