<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_factura=$_GET["id_factura"];

$consulta_factura=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_factura=?', 's', array($id_factura));

$consulta_datos_factura = db_query_params($consulta_factura[0], $consulta_factura[1], $consulta_factura[2]) or die (db_error());

$numRecords = db_num_rows ($consulta_datos_factura);

$datos_de_factura=db_fetch_array($consulta_datos_factura);

$estado_boton_facturar="disabled=disabled";


if (($nivel_acceso==4) and ($datos_de_factura["facturada"]!=1)) {

    $estado_boton_facturar="";

}

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Facturación</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<script language="javascript" src="dynamicoptionlist.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">

</head>
<body onLoad="initDynamicOptionLists();">
<div class="container-fluid">
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2>Emitir factura</h2></div>
  <div class="panel-body">
  
  <p align="center">Se va a emitir la factura del recibo seleccionado.<br><br>¿Estás seguro/a?<br><br></p>
  
    <form method="post" action="emitir_factura1.php" name="facturas">
    <p align="center"><input type="hidden" value="<?=$id_factura?>" name="id_factura">
            <button type="button" onclick="window.location.href='factura.php?id_factura=<?=$id_factura?>&albaran=1'" class="btn btn-info"><i class="fa fa-file"></i> Imprimir albar&aacute;n</button> <button type="button" onclick="window.close();" class="btn btn-danger">Cancelar</button> <button <?=$estado_boton_facturar?> type="submit" class="btn btn-success">Facturar</button></p>
    </form>

</div>
</div>
</div>
</body>
</html>
