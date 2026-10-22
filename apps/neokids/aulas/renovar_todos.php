<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Renovación servicios</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<a href="/pdf/recibos_hoy.php" target="_blank" class="btn btn-primary">Imprimir los recibos emitidos</a><br /><br />
<?php 
// Recibimos los datos

$fecha_inicio_proximo_trimestre=$_POST["fecha_inicio_proximo_trimestre"];
$fecha_fin_proximo_trimestre=$_POST["fecha_fin_proximo_trimestre"];

$alumnos=$_POST["alumnos"];
$servicios=$_POST["servicios"];
$contratado=$_POST["contratado"];
$opciones=$_POST["opciones"];
$nombres=$_POST["nombres"];
$fechas_fin=$_POST["fechas_fin"];

$contador=0;
foreach($alumnos as $fila) {

	$id_inscripcion=$fila;

	$busca_servicio_a_renovar=db_query("SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=".$contratado[$contador]) or die(db_error());
	$servicio_a_renovar=db_fetch_array($busca_servicio_a_renovar);
	
	$total_servicio=$servicio_a_renovar["cantidad"]*$servicio_a_renovar["PRECIO_VENTA"];
	$total_dto_servicio=$total_servicio*round(($servicio_a_renovar["descuento"]/100),2);
	$base_servicio=$total_servicio-$total_dto_servicio;
	$iva_servicio=$base_servicio*$servicio_a_renovar["porcentaje_iva"];
	$total_abs_servicio=$base_servicio+$iva_servicio;	
	
	$renovar_servicio=false;
	$renovar_fecha=false;
	
	switch ($opciones[$contador]) {
    	case 0:
			$renovar_servicio=true;
			$renovar_fecha=true;
			$id_servicio_a_renovar=$servicios[$contador]; 
			$precio_venta=$servicio_a_renovar["PRECIO_VENTA"];
			$nombre_servicio=$servicio_a_renovar["ARTICULO"];
			$concepto_desglose=$servicio_a_renovar["desglosar_como"];
			$subvencion=$servicio_a_renovar["SUBVENCION"];
			  	
        	echo($nombres[$contador].", se le ha renovado el servicio \"<b><i>".$servicio_a_renovar["ARTICULO"]."</i></b>\" y se le ha emitido un nuevo recibo por importe de ".number_format($total_abs_servicio,2,",",".")." €. Nueva fecha de baja: ".date("d/m/Y",strtotime($fechas_fin[$contador])).".<br />");	
        	break;
    	case 1:
			$renovar_servicio=true;
			$renovar_fecha=true;
			       	
			$busca_nuevo_servicio=db_query("SELECT * FROM tarifa WHERE tipo_neokids=".$servicio_a_renovar["tipo_neokids"]." AND sesiones_semana=".$servicio_a_renovar["sesiones_semana"]." AND numero_bonos=".($servicio_a_renovar["sesiones_semana"]*10)." AND id_curso=".$servicio_a_renovar["id_curso"]);
			$nuevo_servicio_a_renovar=db_fetch_array($busca_nuevo_servicio);
				
			$total_nuevo_servicio=$servicio_a_renovar["cantidad"]*$nuevo_servicio_a_renovar["PRECIO_VENTA"];
			$total_dto_nuevo_servicio=$total_nuevo_servicio*round(($servicio_a_renovar["descuento"]/100),2);
			$base_nuevo_servicio=$total_nuevo_servicio-$total_dto_nuevo_servicio;
			$iva_nuevo_servicio=$base_nuevo_servicio*$servicio_a_renovar["porcentaje_iva"];
			$total_abs_nuevo_servicio=$base_nuevo_servicio+$iva_nuevo_servicio;					
			
			$id_servicio_a_renovar=$nuevo_servicio_a_renovar["ID"];
			$precio_venta=$nuevo_servicio_a_renovar["PRECIO_VENTA"];
			$nombre_servicio=$nuevo_servicio_a_renovar["ARTICULO"];
			$concepto_desglose=$nuevo_servicio_a_renovar["desglosar_como"];
			$subvencion=$nuevo_servicio_a_renovar["SUBVENCION"];
				
        	echo($nombres[$contador].", se le ha renovado el servicio del trimestre completo \"<b><i>".$nuevo_servicio_a_renovar["ARTICULO"]."</i></b>\" y se le ha emitido un nuevo recibo por importe de ".number_format($total_abs_nuevo_servicio,2,",",".")." €. Nueva fecha de baja: ".date("d/m/Y",strtotime($fechas_fin[$contador])).".<br />");	
        	break;
    	case 2:
			$renovar_servicio=true;
			$renovar_fecha=true;       	
			$busca_nuevo_servicio=db_query("SELECT * FROM tarifa WHERE tipo_neokids=2 AND sesiones_semana=".$servicio_a_renovar["sesiones_semana"]." AND numero_bonos=".($servicio_a_renovar["sesiones_semana"]*$servicio_a_renovar["numero_bonos"])." AND id_curso=".$servicio_a_renovar["id_curso"]);
			$nuevo_servicio_a_renovar=db_fetch_array($busca_nuevo_servicio);
				
			$total_nuevo_servicio=$servicio_a_renovar["cantidad"]*$nuevo_servicio_a_renovar["PRECIO_VENTA"];
			$total_dto_nuevo_servicio=$total_nuevo_servicio*round(($servicio_a_renovar["descuento"]/100),2);
			$base_nuevo_servicio=$total_nuevo_servicio-$total_dto_nuevo_servicio;
			$iva_nuevo_servicio=$base_nuevo_servicio*$servicio_a_renovar["porcentaje_iva"];
			$total_abs_nuevo_servicio=$base_nuevo_servicio+$iva_nuevo_servicio;	
			
			$id_servicio_a_renovar=$nuevo_servicio_a_renovar["ID"];
			$precio_venta=$nuevo_servicio_a_renovar["PRECIO_VENTA"];
			$nombre_servicio=$nuevo_servicio_a_renovar["ARTICULO"];
			$concepto_desglose=$nuevo_servicio_a_renovar["desglosar_como"];
			$subvencion=$nuevo_servicio_a_renovar["SUBVENCION"];
			
        	echo($nombres[$contador].", se ha promocionado de bebe a infantil \"<b><i>".$nuevo_servicio_a_renovar["ARTICULO"]."</i></b>\" y se le ha emitido un nuevo recibo por importe de ".number_format($total_abs_nuevo_servicio,2,",",".")." €. Nueva fecha de baja: ".date("d/m/Y",strtotime($fechas_fin[$contador])).".<br />");	
        	echo "i es igual a 2";
        	break;
    	case 3:
			$renovar_servicio=false;
			$renovar_fecha=true;       	
        	echo ($nombres[$contador].", Renovar solo la fecha. Nueva fecha: ".date("d/m/Y",strtotime($fechas_fin[$contador])).".<br />");
        	break;
    	case 4:
    		$renovar_servicio=false;
			$renovar_fecha=true;   
        	echo ($nombres[$contador].", No se le renovará el servicio<br />");
        	break;        	        	
		}
			
	// Paso 1, emisión del nuevo recibo
	if ($renovar_servicio==true) {
		
		db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,descuento) VALUES (?,?,?,\'2\',\'1\',?)', 'ssss', array((string)($id_inscripcion), (string)($id_servicio_a_renovar), (string)($fecha_inicio_proximo_trimestre), (string)($servicio_a_renovar["descuento"]))) or die(db_error());
                                
		$id_actividad=db_insert_id();

		$ejercicio_factura=date("Y");
		if (date("n")>7) {
			$ejercicio_factura=date("Y")+1;
		  }
		  
		$num_nueva_factura="00001";

		$inserta_factura=db_query_params('INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision) VALUES (?, ?, ?, ?);', 'ssss', array((string)($num_nueva_factura), (string)($ejercicio_factura), (string)($id_inscripcion), (string)(date("Y-m-d")))) or die (db_error());

		$id_nueva_factura=db_insert_id();
		
		$inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, \'1\', ?, ?, ?, ?, ?);', 'ssssssss', array((string)($id_nueva_factura), (string)($id_actividad), (string)($precio_venta), (string)($servicio_a_renovar["descuento"]), (string)($servicio_a_renovar["porcentaje_iva"]), (string)($nombre_servicio), (string)($subvencion), (string)($concepto_desglose))) or die (db_error());

		}
                                
	// Paso 2, modificación de fecha de baja
	if ($renovar_fecha==true) {
		$actualiza_fecha=db_query_params('UPDATE alumnos SET fecha_baja=? WHERE id_alumno=?', 'ss', array((string)($fechas_fin[$contador]), $id_inscripcion));
		}

	$contador++;
	}

?>
<br>
<a href="calendario_control_renovaciones.php" target="_blank" class="btn btn-primary">Salir al calendario e imprimir los recibos emitidos</a>
</body>
