<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");


$listado_caja=db_query("SELECT * FROM pagos_a_cuenta WHERE forma_pago=0") or die(db_error());

while($caja=db_fetch_array($listado_caja)) {
	
	// Busca si est‡
	$busca_caja = db_query_params('SELECT * FROM caja WHERE id_pago=?', 's', array($caja['id_pago'])) or die(db_error());
	
	if (db_num_rows($busca_caja)==0) {
		
		// Datos alumno
		$busca_alumno=db_query_params('SELECT usuarios.Nombre,usuarios.Apellido1,usuarios.Apellido2 FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_factura=?', 's', array($caja['id_factura'])) or die(db_error());
		
		if (db_num_rows($busca_alumno)!=0) {
			
			$datos_alumno=db_fetch_array($busca_alumno);
		
			$alumno=$datos_alumno['Nombre']." ".$datos_alumno['Apellido1']." ".$datos_alumno['Apellido2'];
			
		}
		
		$id_pago = $caja['id_pago'];	
		$fecha = $caja['fecha_pago'];	
		$hora = $caja['hora_pago'];	
		$importe = number_format($caja['importe_pago']*$caja['signo'],2,",","");	
		$concepto = $caja['comentario'];
		if (strpos($concepto, "factura")) {
			$concepto = substr($concepto, 0, strpos($concepto, "factura"));		
		}
		
		$inserta_caja = db_query_params('INSERT INTO caja (id_pago,fecha,hora,importe,concepto,alumno) VALUES (?,?,?,?,?,?)', 'ssssss', array((string)($id_pago), (string)($fecha), (string)($hora), (string)($importe), (string)($concepto), (string)($alumno))) or die(db_error());	
		
	}
	
}	
	
	
?>