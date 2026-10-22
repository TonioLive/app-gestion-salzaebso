<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_contacto=$_GET["id_contacto"];

// Comprueba si el código del contacto es correcto
if (strlen($id_contacto)!=0) {

        // Comprueba si el contacto ya ha sido borrado
        $busca_contacto_si_borrado=db_query_params('SELECT * FROM contactos WHERE id_contacto=?', 's', array($id_contacto));

        if (db_num_rows($busca_contacto_si_borrado)!=0) {
                $borrar_articulo = db_query_params('DELETE FROM contactos WHERE id_contacto=?', 's', array($id_contacto)) or die (db_error());
                }
                
        }
        
header("Location: listado_contactos.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

