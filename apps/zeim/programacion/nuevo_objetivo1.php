<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_area=$_POST["id_area"];
$id_objetivo=$_POST["id_objetivo"];
$nombre_objetivo=$_POST["nombre_objetivo"];
$descripcion_objetivo=$_POST["descripcion_objetivo"];
$padre=$_POST["padre"];
$posicion=$_POST["posicion"];

// Ajusta posicicones

// Comprueba si ha cambiado
if ($id_objetivo!=0) {
        // Busca area actual y el padre actual, si han cambiado ponemos el ultimo de la lista
        $busca_datos_objetivo=db_query_params('SELECT * FROM programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo));
        $datos_objetivo=db_fetch_array($busca_datos_objetivo);
        if ($id_area!=$datos_objetivo["id_area"]) {
                $posicion=0;
                }
        if ($padre!=$datos_objetivo["padre"]) {
                $posicion=0;
                }
        }

if (strlen($posicion)!=0) {

        $orden=$posicion+1;

        $actualiza_ordenes=db_query("UPDATE programacion_objetivos SET orden=(orden+1) WHERE orden>$posicion");

        } else {
        // Busca el ultimo
        $busca_ultimo=db_query("SELECT * FROM programacion_objetivos WHERE id_objetivo<>$id_objetivo AND padre=$padre AND id_area=$id_area ORDER BY orden DESC LIMIT 0,1");
        if (db_num_rows()!=0) {
                $datos_ultimo=db_fetch_array($busca_ultimo);
                $orden=$datos_ultimo["orden"]+1;
                } else {
                $orden=1;
                }
        }

if (strlen($nombre_objetivo)!=0) {
        $nombre_objetivo=db_escape_string($nombre_objetivo);
        }
if (strlen($descripcion_objetivo)!=0) {
        $descripcion_objetivo=db_escape_string($descripcion_objetivo);
        }

if ($id_objetivo==0) {
        $intro_objetivo=db_query("INSERT INTO programacion_objetivos (nombre_objetivo,descripcion_objetivo,orden,id_area,padre) VALUES ('$nombre_objetivo','$descripcion_objetivo','$orden','$id_area','$padre');");
        } else {
        $actualiza_objetivo=db_query("UPDATE programacion_objetivos SET nombre_objetivo='$nombre_objetivo',descripcion_objetivo='$descripcion_objetivo',orden='$orden',id_area='$id_area',padre='$padre' WHERE id_objetivo=$id_objetivo");
        }
        
db_close();
header("Location:index.php");

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

