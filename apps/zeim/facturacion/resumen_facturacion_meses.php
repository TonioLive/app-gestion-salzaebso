<?php

require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$fecha_inicio_resumen="2000-01-01";
$primer_curso=1;

// Busca el curso actual
$curso_seleccionado=$_GET["curso_seleccionado"];
if (strlen($curso_seleccionado)==0) {

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$primer_curso=$datos_curso_actual["id_curso"];
		$nombre_curso_actual=$datos_curso_actual["nombre"];
		} else {
		$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));

		if (db_num_rows($busca_curso_actual)!=0) {
				$datos_curso_actual=db_fetch_array($busca_curso_actual);
				$primer_curso=$datos_curso_actual["id_curso"];
				$nombre_curso_actual=$datos_curso_actual["nombre"];
				} else {
				$primer_curso=0;
				$nombre_curso_actual="Sin curso seleccionado";
				}
		}
} else {
$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
$primer_curso=$curso_seleccionado;
}

$id_centro_informe=$_POST["id_centro_informe"];

if (strlen($id_centro_informe)==0) {
	$id_centro_informe=10;
}

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

$busca_facturacion=db_query("SELECT detalles_factura.*,tarifa.CATEGORIA,tarifa.desglose_informe,alumnos.id_curso,alumnos.aula,facturas_emitidas.fecha_emision FROM $base_datos_centro.detalles_factura LEFT JOIN $base_datos_centro.facturas_emitidas ON detalles_factura.id_factura=facturas_emitidas.id_factura LEFT JOIN $base_datos_centro.alumnos ON facturas_emitidas.id_cliente=alumnos.id_alumno LEFT JOIN $base_datos_centro.servicios_contratados ON detalles_factura.articulo=servicios_contratados.id_servicio_contratado LEFT JOIN $base_datos_centro.tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE alumnos.id_curso=$primer_curso AND facturas_emitidas.fecha_emision>='$fecha_inicio_resumen'".$filtro_centros." ORDER BY detalles_factura.id_detalle") or die(db_error());

$consulta_familias = db_query("SELECT * FROM $base_datos_centro.tarifa_desglose_informe") or die (db_error());

$partidas_familias=array();
$servicios_incluidos=array();
$partidas_mostrar=array();

while($familias=db_fetch_array($consulta_familias)) {

	$partidas_familias[$familias["nombre_desglose"]]=$familias["nombre_desglose"];	
	
}

$listado_cursos=array();

$busca_listado_cursos=db_query("SELECT id_curso,nombre FROM $base_datos_centro.cursos_academicos WHERE id_curso>=$primer_curso ORDER BY fecha_inicio");

while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
	
		$listado_cursos[$datos_listado_cursos["id_curso"]]=$datos_listado_cursos["nombre"];
		
	}

$total_partidas=array();
$listado_aulas=array();
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
	$asignado_a=null;
	
	if ($facturacion["articulo"]!=0) {
		
		// Asigna por familia			
		$listado_aulas[$facturacion["aula"]]=1;		
		$total_partidas[$facturacion["aula"]][$mes_actual][$facturacion["desglose_informe"]]+=$total_abs;
		$partidas_mostrar[$facturacion["desglose_informe"]]=1;
		
	} else {

		// Obtiene ID artículo por cod facturación
		$asignado_a="Otros conceptos";
				
		// Comedor
		if ((substr($facturacion["cod_facturacion"],0,3)=="co_") or (substr($facturacion["cod_facturacion"],0,4)=="cod_")) {
			$asignado_a="Comedor";	
			
		} 
		
		// Horas extra
		if (substr($facturacion["cod_facturacion"],0,3)=="he_") {
			$asignado_a="Horas extra sueltas";	
			
		} 		
		
		// Servicios asignados aula
		if (substr($facturacion["cod_facturacion"],0,3)=="au_") {

			// Busca servicios
			$id_servicio_aula=str_replace("au_", "", $facturacion["cod_facturacion"]);	

			$busca_servicio_aula=db_query("SELECT * FROM $base_datos_centro.servicios_contratados_aula LEFT JOIN $base_datos_centro.tarifa ON servicios_contratados_aula.id_servicio=tarifa.ID WHERE id_servicio_contratado=".$id_servicio_aula);

			if (db_num_rows($busca_servicio_aula)!=0) {

				$datos_servicio_aula=db_fetch_array($busca_servicio_aula);

				if ($datos_servicio_aula["desglose_informe"]!=0) {
					
					$asignado_a=$datos_servicio_aula["desglose_informe"];	
										
				}
				
			}
			
		} 		

		if ($asignado_a==null) {
			$asignado_a="Otros conceptos";			
		}
		
		if ($asignado_a=="Otros conceptos") {
			$concepto = (string)$facturacion["concepto"];
						
			if (substr($concepto,0,11)=='Mensualidad') {
					$asignado_a="Mensualidad";
			}
					
			if (substr($concepto,0,23)=='Servicios Complentarios') {
					$asignado_a="Servicios complementarios";
			}
			
			if ($asignado_a=="Otros conceptos") {

				$servicios_incluidos[$facturacion["concepto"]]=$facturacion["concepto"];			
			}
		}
		
		
		$listado_aulas[$facturacion["aula"]]=1;		
		$total_partidas[$facturacion["aula"]][$mes_actual][$asignado_a]+=$total_abs;
		$partidas_mostrar[$asignado_a]=1;

	}	
	
	
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
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
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<h1>Facturación mensual Aulas</h1>	
<!--
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
-->
<?php 
$busca_aulas=db_query("SELECT * FROM centro_aulas ORDER BY edad_minima;") or die(db_error());

while($aulas=db_fetch_array($busca_aulas)) {
	
	if ($listado_aulas[$aulas['id_aula']]==1) {	
?>
<h2>Aula <?=$aulas['nombre']?></h2>
<table class="table table-bordered table-condensed table-striped">
	<tr>
		<td>&nbsp;</td>
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
		<td class="text-right"><?=number_format($total_partidas[$aulas['id_aula']][$id_curso][$id_partida]*$signo,2,",",".");?> €</td>
		<?php
			$total_fila+=$total_partidas[$aulas['id_aula']][$id_curso][$id_partida]*$signo;
			}
		}	
		?>
		<td class="text-right"><?=number_format($total_fila,2,",",".");?> €</td>
	</tr>
	<?php
	}
	?>
</table>
<?php
	}
}
if (count($servicios_incluidos)>0) {
?>	
<h2>Servicios incluidos en "Otros conceptos"</h2>	
<div>
<ul>
<?php
foreach ($servicios_incluidos as $servicio) {
	echo("<li><big><big>".$servicio."</big></big></li>");
}
?>
</ul>
</div>
<?php
}
?>
</body>
</html>