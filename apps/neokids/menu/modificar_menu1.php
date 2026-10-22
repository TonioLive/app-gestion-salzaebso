<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_menu=$_POST["id_menu"];
$nombre_menu=$_POST["nombre_menu"];
if (strlen($nombre_menu)!=0) {
        $nombre_menu=urlencode($nombre_menu);
        }
$id_centro_menu=$_POST["id_centro_menu"];
$edad_ini_menu=$_POST["edad_ini_menu"];
$edad_fin_menu=$_POST["edad_fin_menu"];
$regimen=$_POST["regimen"];

        if ($id_menu==0) {
        $inserta_menu=db_query_params('INSERT INTO menu_menus (nombre_menu) VALUES (?)', 's', array((string)($nombre_menu)));
        $id_menu=db_insert_id();
        }
        
        $nuevo_articulo = db_query_params('UPDATE menu_menus SET regimen=?, edad_ini_menu=?, edad_fin_menu=?, nombre_menu=?, id_centro=? WHERE id_menu=?', 'ssssss', array((string)($regimen), (string)($edad_ini_menu), (string)($edad_fin_menu), (string)($nombre_menu), (string)($id_centro_menu), $id_menu)) or die (db_error());

        
        header("Location:gestion_menu.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

