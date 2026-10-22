<?php

require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$fecha_inicio_resumen="2000-01-01";
$primer_curso=1;

$id_centro_informe=$_POST["id_centro_informe"];

$filtro_centros="";

if ($id_centro_informe) {

	$filtro_centros=" AND alumnos.id_centro=".$id_centro_informe;
	
}
$partida_subvencion_centro=119;
$base_datos_centro="";
if ($id_centro_informe==10) {
	
	$base_datos_centro="escuelamunicipal";
	$partida_subvencion_centro=120;
	
}

$busca_facturacion=db_query("SELECT detalles_factura.*,tarifa.CATEGORIA,alumnos.id_curso,facturas_emitidas.fecha_emision FROM $base_datos_centro.detalles_factura LEFT JOIN $base_datos_centro.facturas_emitidas ON detalles_factura.id_factura=facturas_emitidas.id_factura LEFT JOIN $base_datos_centro.alumnos ON facturas_emitidas.id_cliente=alumnos.id_alumno LEFT JOIN $base_datos_centro.servicios_contratados ON detalles_factura.articulo=servicios_contratados.id_servicio_contratado LEFT JOIN $base_datos_centro.tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE facturas_emitidas.fecha_emision>='$fecha_inicio_resumen'".$filtro_centros." ORDER BY detalles_factura.id_detalle") or die(db_error());

$consulta_familias = db_query("SELECT * FROM $base_datos_centro.tipos_iva_servicios WHERE tipo_servicios<>1") or die (db_error());

$partidas_familias=array();
$partidas_mostrar=array();

while($familias=db_fetch_array($consulta_familias)) {

	$partidas_familias[$familias["id_tipo_iva"]]=$familias["categoria"];	
	
}

$listado_cursos=array();

$busca_listado_cursos=db_query("SELECT id_curso,nombre FROM $base_datos_centro.cursos_academicos WHERE id_curso>=$primer_curso ORDER BY fecha_inicio");

while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
	
		$listado_cursos[$datos_listado_cursos["id_curso"]]=$datos_listado_cursos["nombre"];
		
	}

$total_partidas=array();

$listado_meses=array();

while($facturacion=db_fetch_array($busca_facturacion)){
	
	$mes_actual=date("Ym",strtotime($facturacion["fecha_emision"]));
	
	if (!in_array($mes_actual, $listado_meses)) {
	
		$listado_meses[$mes_actual]=date("m/Y",strtotime($facturacion["fecha_emision"]));		
		
	}
	
	$total_linea=$facturacion["cantidad"]*$facturacion["precio"];
    $total_dto=$total_linea*round(($facturacion["dto"]/100),2);
    $base_linea=$total_linea-$total_dto;
    $iva_linea=$base_linea*$facturacion["tipo_iva"];
    $total_abs=$base_linea+$iva_linea;
	
	// Comprueba partida
	
	if ($facturacion["articulo"]!=0) {
		
		// Asigna por familia					
		$total_partidas[$mes_actual][$facturacion["CATEGORIA"]]+=$total_abs;
		$partidas_mostrar[$facturacion["CATEGORIA"]]=1;
		
	} else {

		// Obtiene ID artículo por cod facturación
		$asignado_a=14;
				
		// Comedor
		if ((substr($facturacion["cod_facturacion"],0,3)=="co_") or (substr($facturacion["cod_facturacion"],0,4)=="cod_")) {
			$asignado_a=7;	
			
		} 
		
		// Horas extra
		if (substr($facturacion["cod_facturacion"],0,3)=="he_") {
			$asignado_a=1;	
			
		} 		
		
		// Servicios asignados aula
		if (substr($facturacion["cod_facturacion"],0,3)=="au_") {

			// Busca servicios
			$id_servicio_aula=str_replace("au_", "", $facturacion["cod_facturacion"]);	

			$busca_servicio_aula=db_query("SELECT * FROM $base_datos_centro.servicios_contratados_aula LEFT JOIN $base_datos_centro.tarifa ON servicios_contratados_aula.id_servicio=tarifa.ID WHERE id_servicio_contratado=".$id_servicio_aula);

			if (db_num_rows($busca_servicio_aula)!=0) {

				$datos_servicio_aula=db_fetch_array($busca_servicio_aula);

				if ($datos_servicio_aula["CATEGORIA"]!=0) {
					
					$asignado_a=$datos_servicio_aula["CATEGORIA"];	
										
				}
				
			}
			
		} 		
		
		$total_partidas[$mes_actual][$asignado_a]+=$total_abs;
		$partidas_mostrar[$asignado_a]=1;

	}	
	
	
}

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
<h1>Facturación mensual</h1>	
<form name="centros" method="post" action="<?=$path_index;?>">
<select name="id_centro_informe" size=1 onchange="document.centros.submit()" class="form-control">
<option value="">Todos los centros Lápices (Sin incluir Sant Pau)</option>
<?php 
$consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY centro_propio DESC, nombre_centro;");    
while($centros=db_fetch_array($consulta_centros)){
?>
<option value="<?=$centros["id_centro"];?>"<?php if ($centros["id_centro"]==$id_centro_informe) { print "selected"; } ?>><?=$centros["nombre_centro"];?></option>
<?php 
}
?>
</select>
</form>
<table class="table table-bordered table-condensed table-striped">
	<tr>
		<td>Curso</td>
		<?php
		foreach ($partidas_familias as $id_partida => $nombre_partida) {	
			if ($partidas_mostrar[$id_partida]==1) {	
		?>
		<td><?=$nombre_partida?></td>
		<?php
			}
		}	
		?>	
		<td>Total</td>			
	</tr>
	<?php	
	foreach ($listado_meses as $id_curso => $nombre_curso) {
	?>
	<tr>
		<td><?=$nombre_curso?></td>
		<?php
		$total_fila=0;
		foreach ($partidas_familias as $id_partida => $nombre_partida) {			
			if ($partidas_mostrar[$id_partida]==1) {
				$signo=1;
				if (($id_partida==4) or ($id_partida==$partida_subvencion_centro)) {
					$signo=-1;	
				}			
		?>
		<td class="text-right"><?=number_format($total_partidas[$id_curso][$id_partida]*$signo,2,",",".");?> €</td>
		<?php
			$total_fila+=$total_partidas[$id_curso][$id_partida]*$signo;
			}
		}	
		?>
		<td class="text-right"><?=number_format($total_fila,2,",",".");?> €</td>
	</tr>
	<?php
	}
	?>
</table>		
</body>
</html>