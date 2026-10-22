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

if ($id_servicio==0) {
        // Introduce el nuevo servicio
        if ($periodicidad==1) {
                $nuevo_servicio = db_query("INSERT INTO servicios_contratados (id_alumno, id_servicio, fecha_inicio, fecha_fin, periodicidad, descuento, cantidad, notas) VALUES ('$id_inscripcion', '$articulo', '$fecha_ini', '$fecha_fin', '$periodicidad', '$descuento', '$cantidad', '$notas')") or die (db_error());
                } else {
                $nuevo_servicio = db_query("INSERT INTO servicios_contratados (id_alumno, id_servicio, fecha_inicio, periodicidad, descuento, cantidad, notas) VALUES ('$id_inscripcion', '$articulo', '$fecha', '$periodicidad', '$descuento', '$cantidad', '$notas')") or die (db_error());
                }
        } else {
        // Actualiza datos
        if ($periodicidad==1) {
                $editar_servicio = db_query("UPDATE servicios_contratados SET id_alumno='$id_inscripcion', id_servicio='$articulo', fecha_inicio='$fecha_ini', fecha_fin='$fecha_fin', periodicidad='$periodicidad', descuento='$descuento', cantidad='$cantidad', notas='$notas' WHERE id_servicio_contratado=$id_servicio") or die (db_error());
                } else {
                $editar_servicio = db_query("UPDATE servicios_contratados SET id_alumno='$id_inscripcion', id_servicio='$articulo', fecha_inicio='$fecha', fecha_fin='', periodicidad='$periodicidad', descuento='$descuento', cantidad='$cantidad', notas='$notas' WHERE id_servicio_contratado=$id_servicio") or die (db_error());
                }
        }
        
header("Location:editar_servicios.php?i=".$id_inscripcion);

db_close();

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

