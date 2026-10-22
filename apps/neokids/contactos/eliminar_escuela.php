<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_competencia=$_GET["id_competencia"];

// Comprueba si el código del contacto es correcto
if (strlen($id_competencia)!=0) {

        $borrar_articulo = db_query_params('DELETE FROM escuelas_competencia WHERE id_competencia=?', 's', array($id_competencia)) or die (db_error());
                
        }
        
header("Location: listado_escuelas.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

