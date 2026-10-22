<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_aula=$_POST["id_aula"];
$id_servicio=$_POST["id_servicio"];
$familia=$_POST["familia"];
$articulo=$_POST["articulo"];
$periodicidad=$_POST["periodicidad"];
$fecha_ini_dia=$_POST["fecha_ini_dia"];
$fecha_ini_mes=$_POST["fecha_ini_mes"];
$fecha_ini_ano=$_POST["fecha_ini_ano"];
$fecha_ini=$fecha_ini_ano."/".$fecha_ini_mes."/".$fecha_ini_dia;
$fecha_fin_dia=$_POST["fecha_fin_dia"];
$fecha_fin_mes=$_POST["fecha_fin_mes"];
$fecha_fin_ano=$_POST["fecha_fin_ano"];
$fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;
$fecha_dia=$_POST["fecha_dia"];
$fecha_mes=$_POST["fecha_mes"];
$fecha_ano=$_POST["fecha_ano"];
$fecha=$fecha_ano."/".$fecha_mes."/".$fecha_dia;
$precio=$_POST["precio"];
$cantidad=$_POST["cantidad"];
$descuento=$_POST["descuento"];
$tipo_iva=$_POST["tipo_iva"];
$notas=$_POST["notas"];

if ($id_servicio==0) {
        // Introduce el nuevo servicio
        if ($periodicidad==1) {
                $nuevo_servicio = db_query_params('INSERT INTO servicios_contratados_aula (id_aula, id_servicio, fecha_inicio, fecha_fin, periodicidad, descuento, cantidad, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', 'ssssssss', array((string)($id_aula), (string)($articulo), (string)($fecha_ini), (string)($fecha_fin), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas))) or die (db_error());
                } else {
                $nuevo_servicio = db_query_params('INSERT INTO servicios_contratados_aula (id_aula, id_servicio, fecha_inicio, periodicidad, descuento, cantidad, notas) VALUES (?, ?, ?, ?, ?, ?, ?)', 'sssssss', array((string)($id_aula), (string)($articulo), (string)($fecha), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas))) or die (db_error());
                }
        } else {
        // Actualiza datos
        if ($periodicidad==1) {
                $editar_servicio = db_query_params('UPDATE servicios_contratados_aula SET id_aula=?, id_servicio=?, fecha_inicio=?, fecha_fin=?, periodicidad=?, descuento=?, cantidad=?, notas=? WHERE id_servicio_contratado=?', 'sssssssss', array((string)($id_aula), (string)($articulo), (string)($fecha_ini), (string)($fecha_fin), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas), $id_servicio)) or die (db_error());
                } else {
                $editar_servicio = db_query_params('UPDATE servicios_contratados_aula SET id_aula=?, id_servicio=?, fecha_inicio=?, fecha_fin=\'\', periodicidad=?, descuento=?, cantidad=?, notas=? WHERE id_servicio_contratado=?', 'ssssssss', array((string)($id_aula), (string)($articulo), (string)($fecha), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas), $id_servicio)) or die (db_error());
                }
        }
        
header("Location:editar_servicios_aula.php?i=".$id_aula);

db_close();

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

