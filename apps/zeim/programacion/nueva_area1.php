<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_area=$_POST["id_area"];
$nombre_area=$_POST["nombre_area"];
$descripcion_area=$_POST["descripcion_area"];
$posicion=$_POST["posicion"];

// Ajusta posicicones
if (strlen($posicion)!=0) {

        $orden=$posicion+1;

        $actualiza_ordenes=db_query("UPDATE programacion_areas SET orden=(orden+1) WHERE orden>$posicion");

        } else {
        // Busca el ultimo
        $busca_ultimo=db_query_params('SELECT * FROM programacion_areas WHERE id_area<>? ORDER BY orden DESC LIMIT 0,1', 's', array($id_area));
        if (db_num_rows()!=0) {
                $datos_ultimo=db_fetch_array($busca_ultimo);
                $orden=$datos_ultimo["orden"]+1;
                } else {
                $orden=1;
                }
        }

if (strlen($nombre_area)!=0) {
        $nombre_area=db_escape_string($nombre_area);
        }
if (strlen($descripcion_area)!=0) {
        $descripcion_area=db_escape_string($descripcion_area);
        }

if ($id_area==0) {
        $intro_area=db_query("INSERT INTO programacion_areas (nombre_area,descripcion_area,orden) VALUES ('$nombre_area','$descripcion_area','$orden');");
        } else {
        $actualiza_area=db_query("UPDATE programacion_areas SET nombre_area='$nombre_area',descripcion_area='$descripcion_area',orden='$orden' WHERE id_area=$id_area");
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

