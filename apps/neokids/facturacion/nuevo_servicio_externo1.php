<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_inscripcion=$_POST["id_inscripcion"];
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
$periodo=$_POST["periodo"];

if ($id_servicio==0) {
        // Introduce el nuevo servicio
        if ($periodicidad==1) {
                $nuevo_servicio = db_query_params('INSERT INTO servicios_contratados_externos (id_alumno, id_servicio, fecha_inicio, fecha_fin, periodicidad, descuento, cantidad, notas, periodo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', 'sssssssss', array((string)($id_inscripcion), (string)($articulo), (string)($fecha_ini), (string)($fecha_fin), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas), (string)($periodo))) or die (db_error());
                } else {
                $nuevo_servicio = db_query_params('INSERT INTO servicios_contratados_externos (id_alumno, id_servicio, fecha_inicio, periodicidad, descuento, cantidad, notas, periodo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', 'ssssssss', array((string)($id_inscripcion), (string)($articulo), (string)($fecha), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas), (string)($periodo))) or die (db_error());
                }
        } else {
        // Actualiza datos
        if ($periodicidad==1) {
                $editar_servicio = db_query_params('UPDATE servicios_contratados_externos SET id_alumno=?, id_servicio=?, fecha_inicio=?, fecha_fin=?, periodicidad=?, descuento=?, cantidad=?, notas=?, periodo=? WHERE id_servicio_contratado=?', 'ssssssssss', array((string)($id_inscripcion), (string)($articulo), (string)($fecha_ini), (string)($fecha_fin), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas), (string)($periodo), $id_servicio)) or die (db_error());
                } else {
                $editar_servicio = db_query_params('UPDATE servicios_contratados_externos SET id_alumno=?, id_servicio=?, fecha_inicio=?, fecha_fin=\'\', periodicidad=?, descuento=?, cantidad=?, notas=?, periodo=? WHERE id_servicio_contratado=?', 'sssssssss', array((string)($id_inscripcion), (string)($articulo), (string)($fecha), (string)($periodicidad), (string)($descuento), (string)($cantidad), (string)($notas), (string)($periodo), $id_servicio)) or die (db_error());
                }
        }
        
header("Location:editar_servicios_externos.php?i=".$id_inscripcion);

db_close();

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

