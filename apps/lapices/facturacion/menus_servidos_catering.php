<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$fecha=$_GET["fecha"];

if (strlen($fecha)==0) {

    $fecha=strtotime(date("Y-06-01"));

}
$fechaSel=date('Y-m-d',$fecha);
$mesSeleccionado=date("n",$fecha);
$anySeleccionado=date("Y",$fecha);

$queryMenus = "SELECT * FROM menu_pedidos_externos LEFT JOIN centros ON menu_pedidos_externos.id_centro=centros.id_centro LEFT JOIN menu_menus ON menu_pedidos_externos.id_menu=menu_menus.id_menu WHERE menu_pedidos_externos.cantidad>0 AND menu_pedidos_externos.confirmado=1 AND month(menu_pedidos_externos.fecha)=".$mesSeleccionado." AND year(menu_pedidos_externos.fecha)=".$anySeleccionado." ORDER BY menu_pedidos_externos.id_centro,menu_pedidos_externos.fecha;";
$consultaMenus = db_query($queryMenus);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<?php require("../configuracion/estilos/estilo1.php"); ?>
<?php require("../configuracion/scripts/scripts.php"); ?>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<h1>Menús servidos durante <?=$nombre_mes[$mesSeleccionado]?> de <?=$anySeleccionado?></h1>	
<table class="table table-bordered table-condensed table-striped">
	<tr>
		<td><b>Cliente</b></td>
		<td><b>Fecha</b></td>	
		<td><b>Menú</b></td>
		<td><b>Raciones servidas</b></td>
	</tr>
	<?php	
    while($menus=db_fetch_array($consultaMenus)) {
	?>
	<tr>
        <td><?=$menus['nombre_centro'];?></td>
		<td><?=date('d/m/Y',strtotime($menus['fecha']));?></td>
		<td><?=urldecode($menus['nombre_menu']);?></td>
		<td class="text-right"><?=number_format($menus['cantidad'],0,",",".");?></td>
	</tr>
	<?php
	}
	?>
</table>
<div align="center">
<a href="<?=$_SERVER["PHP_SELF"];?>?fecha=<?=strtotime('-1 month',$fecha)?>" class="btn btn-md btn-primary"><< Mes anterior</a>
<a href="/facturacion/gestion_facturacion.php" class="btn btn-md btn-success">salir</a>
<a href="<?=$_SERVER["PHP_SELF"];?>?fecha=<?=strtotime('+1 month',$fecha)?>" class="btn btn-md btn-primary">Mes siguiente >></a>		
</div>
</body>
</html>