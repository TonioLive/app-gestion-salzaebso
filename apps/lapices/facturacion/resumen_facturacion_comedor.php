<?php
exit();
ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED ^ E_WARNING);

require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$primer_curso=18;

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

$consulta_familias = db_query("SELECT * FROM $base_datos_centro.tipos_iva_servicios WHERE tipo_servicios<>1") or die (db_error());

$partidas_familias=array();
$partidas_mostrar=array();

while($familias=db_fetch_array($consulta_familias)) {

	$partidas_familias[$familias["id_tipo_iva"]]=$familias["categoria"];	
	
}

$listado_cursos=array();
$array_alumnos_comedor=array();
$array_total_alumnos_comedor=array();
$array_total_alumnos_curso=array();
$array_media_alumnos_curso=array();

$array_total_menus_cursos=array();

$busca_listado_cursos=db_query("SELECT id_curso,nombre,fecha_inicio,fecha_fin FROM $base_datos_centro.cursos_academicos WHERE id_curso>=$primer_curso AND month(fecha_inicio)=9 AND fecha_inicio<='".date("Y-m-d")."' ORDER BY fecha_inicio");

while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
			
		$listado_cursos[$datos_listado_cursos["id_curso"]]=$datos_listado_cursos["nombre"];
		$array_alumnos_comedor[$datos_listado_cursos["id_curso"]] = array();
		$fechas_cursos[$datos_listado_cursos["id_curso"]]=array('fecha_inicio'=>$datos_listado_cursos["fecha_inicio"],'fecha_fin'=>$datos_listado_cursos["fecha_fin"]);
		
		// Busca alumnos curso
		$busca_alumnos_curso=db_query("SELECT * FROM alumnos WHERE id_curso=".$datos_listado_cursos["id_curso"]);
		$array_total_alumnos_curso[$datos_listado_cursos["id_curso"]]=db_num_rows($busca_alumnos_curso);
		
		// Calcula la ocupación media durante el curso
		
		$total_menus_curso=0;
		$total_alumnos_comedor_curso=0;
		
		$total_curso=0;
		$total_curso_comedor=0;
		$total_meses_media=0;
		$mes_actual=9;
		$curso_actual_l=$datos_listado_cursos["id_curso"];
		$anio_actual=date("Y",strtotime($datos_listado_cursos["fecha_inicio"]));
		for ($num_mes=0;$num_mes<10;$num_mes++) {
			$total_alumnos_centros=0;
			$total_alumnos_comedor_centros=0;
			for ($id_centro=6;$id_centro<=8;$id_centro++) {
				
				$ultimo_dia_mes=date("t",strtotime("$anio_actual-$mes_actual-1"));
				
				$fecha_inicio_mes=date("Y-m-d",strtotime("$anio_actual-$mes_actual-1"));
				$fecha_fin_mes=date("Y-m-d",strtotime("$anio_actual-$mes_actual-$ultimo_dia_mes"));
				
				$busca_alumnos=db_query("SELECT id_alumno FROM alumnos LEFT JOIN usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_centro=$id_centro AND alumnos.id_curso=$curso_actual_l AND alumnos.fecha_matricula<='$fecha_fin_mes' AND alumnos.fecha_baja>='$fecha_inicio_mes'") or die(db_error());
				
				$busca_alumnos_comedor=db_query("SELECT id_alumno FROM alumnos LEFT JOIN usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_centro=$id_centro AND alumnos.id_curso=$curso_actual_l AND alumnos.fecha_matricula<='$fecha_fin_mes' AND alumnos.fecha_baja>='$fecha_inicio_mes' AND (alumnos.comedor=1 OR alumnos.comedor=3)") or die(db_error());
				
				$total_alumnos_comedor=db_num_rows($busca_alumnos_comedor);
				$total_alumnos_comedor_centros+=$total_alumnos;
				
				$total_alumnos=db_num_rows($busca_alumnos);
				$total_alumnos_centros+=$total_alumnos;
				
				// Busca menús servidos ese mes
			
				$consulta_menus_mes="SELECT count(id_servicio_consumido) as total_comedores FROM ((SELECT * FROM servicios_consumidos) UNION (SELECT * FROM `servicios_consumidos_2017-09-27` WHERE id_servicio_consumido<958640) UNION (SELECT * FROM `servicios_consumidos_2014-12-16` WHERE id_servicio_consumido<689954) UNION (SELECT * FROM `servicios_consumidos2006` WHERE id_servicio_consumido<612334)) AS Tabla1, alumnos WHERE Tabla1.id_alumno=alumnos.id_usuario AND alumnos.id_curso=$curso_actual_l AND (Tabla1.tipo_menu=1 OR Tabla1.tipo_menu=3) AND (alumnos.comedor=1 OR alumnos.comedor=3) AND Tabla1.fecha>='$fecha_inicio_mes' AND Tabla1.fecha<='$fecha_fin_mes'";
				$busca_menus_mes=db_query($consulta_menus_mes);
				if (db_num_rows($consulta_menus_mes)!=0) {
					$menus_mes=db_fetch_array($consulta_menus_mes);
					$numero_memus_mes=$menus_mes[total_comedores];
					$total_menus_curso+=$numero_memus_mes;
				}
				
			}
			
			if ($total_alumnos_centros>0) {
				$total_meses_media++;
				}
			$total_curso+=$total_alumnos_centros;
			$total_curso_comedor+=$total_alumnos_comedor_centros;
			
			$mes_actual++;
			if ($mes_actual==13) {
				$mes_actual=1;
				$anio_actual++;
			}
		}
		
		$media_curso=0;
		$media_comedor_curso=0;
		if (($total_curso>0) and ($total_meses_media>0)) {
				$media_curso=ceil(($total_curso/$total_meses_media));
				if ($total_curso_comedor>0) {
					$media_comedor_curso=ceil(($total_curso_comedor/$total_meses_media));
				}
		}

		$array_total_menus_cursos[$datos_listado_cursos["id_curso"]]=$total_menus_curso;
		$array_total_alumnos_comedor[$datos_listado_cursos["id_curso"]]=$total_curso_comedor;
		$array_media_alumnos_curso[$datos_listado_cursos["id_curso"]]=$media_curso;
		$array_media_alumnos_comedor_curso[$datos_listado_cursos["id_curso"]]=$media_comedor_curso;
	}
$busca_facturacion=db_query("SELECT detalles_factura.*,tarifa.CATEGORIA,alumnos.id_curso, alumnos.id_alumno FROM $base_datos_centro.detalles_factura LEFT JOIN $base_datos_centro.facturas_emitidas ON detalles_factura.id_factura=facturas_emitidas.id_factura LEFT JOIN $base_datos_centro.alumnos ON facturas_emitidas.id_cliente=alumnos.id_alumno LEFT JOIN $base_datos_centro.servicios_contratados ON detalles_factura.articulo=servicios_contratados.id_servicio_contratado LEFT JOIN $base_datos_centro.tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE alumnos.id_curso>=$primer_curso".$filtro_centros." ORDER BY detalles_factura.id_detalle");
	
$total_partidas=array();

while($facturacion=db_fetch_array($busca_facturacion)){
	
	$total_linea=$facturacion["cantidad"]*$facturacion["precio"];
    $total_dto=$total_linea*round(($facturacion["dto"]/100),2);
    $base_linea=$total_linea-$total_dto;
    $iva_linea=$base_linea*$facturacion["tipo_iva"];
    $total_abs=$base_linea+$iva_linea;
	
	// Comprueba partida
	
	if ($facturacion["articulo"]!=0) {
		
		// Asigna por familia	

		if ($facturacion["CATEGORIA"]==7) {
			
			if (substr($facturacion["cod_facturacion"],0,4)!="cod_") {
				// Comedor habitual, no extra
				if (!in_array($facturacion["id_alumno"],$array_alumnos_comedor[$facturacion["id_curso"]])) {
						$array_alumnos_comedor[$facturacion["id_curso"]][]=$facturacion["id_alumno"];
				}
				
			}
			
		$total_partidas[$facturacion["id_curso"]][$facturacion["CATEGORIA"]]+=$total_abs;
		$partidas_mostrar[$facturacion["CATEGORIA"]]=1;		
		
		}
		
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
		
		if ($asignado_a==7) {
			
			if (substr($facturacion["cod_facturacion"],0,4)!="cod_") {
				// Comedor habitual, no extra
				if (!in_array($facturacion["id_alumno"],$array_alumnos_comedor[$facturacion["id_curso"]])) {
						$array_alumnos_comedor[$facturacion["id_curso"]][]=$facturacion["id_alumno"];
				}
				
			}
			
			$total_partidas[$facturacion["id_curso"]][$asignado_a]+=$total_abs;
			$partidas_mostrar[$asignado_a]=1;
		}

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
<h1>Comedores por curso de todos los centros Lápices (Sin incluir Sant Pau)</h1>	
<table class="table table-bordered table-condensed table-striped">
	<tr>
		<td>Curso</td>
		<?php
		foreach ($partidas_familias as $id_partida => $nombre_partida) {	
			if ($partidas_mostrar[$id_partida]==1) {	
		?>
		<td>Facturación <?=$nombre_partida?></td>
		<td>Total alumnos curso</td>
		<td>Total alumnos comedor</td>
		<td>Facturación promedio alumno/curso</td>
		<td>Precio promedio alumno/menú</td>
		<td>Nº total menús servidos</td>
		<td>Promedio menús alumno/curso</td>
		<?php
			}
		}	
		?>			
	</tr>
	<?php	
	foreach ($listado_cursos as $id_curso => $nombre_curso) {
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
			
			$total_alumnos_comedor=$array_media_alumnos_comedor_curso[$id_curso];
		
		/*		
			// Comprueba numero de comedores servidos por curso y promedio por alumnos
			$total_menus_curso=0;
			$promedio_alumnos=0;
			$consulta_menus_curso="SELECT count(id_servicio_consumido) as total_comedores FROM ((SELECT * FROM servicios_consumidos) UNION (SELECT * FROM `servicios_consumidos_2017-09-27` WHERE id_servicio_consumido<958640) UNION (SELECT * FROM `servicios_consumidos_2014-12-16` WHERE id_servicio_consumido<689954) UNION (SELECT * FROM `servicios_consumidos2006` WHERE id_servicio_consumido<612334)) AS Tabla1 WHERE hora_entrada_c is not Null AND fecha>='".$fechas_cursos[$id_curso]['fecha_inicio']."' AND fecha<='".$fechas_cursos[$id_curso]['fecha_fin']."'";
			
			$numero_menus_curso=mysql_query($consulta_menus_curso);
			if (mysql_num_rows($numero_menus_curso)!=0) {
				$menus_curso=mysql_fetch_array($numero_menus_curso);
				$total_menus_curso=$menus_curso["total_comedores"];
				if ($total_alumnos_comedor>0) {
					
					$promedio_alumnos=ceil($total_menus_curso/$total_alumnos_comedor);
					
				}
			}
			
		*/
		
		$porcentaje_alumnos_comedor=0;
		if (($total_alumnos_comedor>0) and ($array_media_alumnos_curso[$id_curso]>0)) {
			$porcentaje_alumnos_comedor=number_format(($total_alumnos_comedor/$array_media_alumnos_curso[$id_curso])*100,2,",",".");
		}
			
		?>
		<td class="text-right"><?=number_format($total_partidas[$id_curso][$id_partida]*$signo,2,",",".");?> €</td>
		<td class="text-right"><?=$array_media_alumnos_curso[$id_curso];?> </td>
		<td class="text-right"><?=$total_alumnos_comedor;?> (<?=$porcentaje_alumnos_comedor?> %)</td>
		<td class="text-right"><?=number_format(($total_partidas[$id_curso][$id_partida]*$signo)/$total_alumnos_comedor,2,",",".");?> €</td>
		<td class="text-right"><?=number_format(($total_partidas[$id_curso][$id_partida]*$signo)/$array_total_menus_cursos[$id_curso],2,",",".");?> €</td>
		<td class="text-right"><?=$array_total_menus_cursos[$id_curso];?> </td>
		<td class="text-right"><?=$array_media_alumnos_comedor_curso[$id_curso];?> </td>
		<?php
			$total_fila+=$total_partidas[$id_curso][$id_partida]*$signo;
			}
		}	
		?>
	</tr>
	<?php
	}
	?>
</table>		
</body>
</html>