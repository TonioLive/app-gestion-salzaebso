<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$modo_prueba=false;
if ($_POST["modo_prueba"]=="1") {
	$modo_prueba=true;
}

$fecha_retorno=$_POST["fecha_retorno"];

if (strlen($fecha_retorno)==0) {

        $fecha_retorno=time();

}

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
<?php 
if ($modo_prueba==true) {
?>
<h1 class="text-danger">¡¡ATENCIÓN!! - MODO PRUEBA - NO SE HA EMITIDO NINGÚN RECIBO</h1>
<?php 
}
?>
<a href="calendario_control_renovaciones.php?dia=<?=date("Y-m-d",$fecha_retorno)?>" target="_blank" class="btn btn-primary">Salir al calendario e imprimir los recibos emitidos</a><br />
<?php 
// Recibimos los datos

$alumnos=$_POST["alumnos"];

$total_a_facturar=0;
$total_sin_devolucion=0;
$contador=0;
$total_sesiones=0;
$total_tipos=array(0,0,0,0,0,0,0,0);
$sesiones_tipos=array(0,0,0,0,0,0,0,0);

foreach($alumnos as $fila) {

        $id_inscripcion=$fila;

                $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($id_inscripcion)) or die(db_error());
                if (db_num_rows($busca_datos_alumno)!=0) {
                
                        $datos_alumno=db_fetch_array($busca_datos_alumno);
                        
                        $apellido1_alumno=$datos_alumno["Apellido1"];
                        $apellido2_alumno=$datos_alumno["Apellido2"];
                        $id_curso_actual=$datos_alumno["id_curso"];
                        $id_usuario_inscripcion=$datos_alumno["id_usuario"];
                
                }

        $num_sesiones=$_POST["num_sesiones_".$id_inscripcion];
        $id_servicio_renovar=$_POST["id_servicio_renovar_".$id_inscripcion];
        $hermanos=$_POST["hermanos_".$id_inscripcion];;
        $nombres=$_POST["nombres_".$id_inscripcion];

        $busca_servicio_a_renovar=db_query_params('SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tarifa.ID=?', 's', array($id_servicio_renovar)) or die(db_error()."-0");
        $servicio_a_renovar=db_fetch_array($busca_servicio_a_renovar);
        
        $total_tipos[$servicio_a_renovar["tipo_neokids"]]++;
        
        $total_servicio=$num_sesiones*$servicio_a_renovar["PRECIO_VENTA"];       
        
        $texto_descuento="";
        
        // Busca si debe aplicarse descuento por hermanos
        if ($hermanos>0) {
        
                        $descuento_hermano=0;
        
                        // Busca si al hermano se le ha aplicado ya el descuento
                        $busca_hermanos=db_query_params('SELECT * FROM servicios_contratados, tarifa, usuarios,alumnos WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=alumnos.id_alumno AND usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario<>? AND alumnos.id_curso=? AND usuarios.apellido1=? AND usuarios.apellido2=?', 'ssss', array($id_usuario_inscripcion, $id_curso_actual, (string)($apellido1_alumno), (string)($apellido2_alumno))) or die(db_error());        
                
                        while($lista_hermanos=db_fetch_array($busca_hermanos)) {
                
                                if ($lista_hermanos["descuento"]==5) {
                                        $descuento_hermano++;
                                }
                
                        }
                
                        if ($descuento_hermano<$hermanos) {
                                $descuento_servicio=5;
                                $total_dto_servicio=$total_servicio*0.05;
                                $texto_descuento=" Incluye un descuento del 5% por tener hermanos en el centro.";
                        } else {
                                $descuento_servicio=0;
                                $total_dto_servicio=0;
                        }
                        
                        
                } else {
                        $descuento_servicio=0;
                        $total_dto_servicio=0;                        
                }
                
        
        $base_servicio=$total_servicio-$total_dto_servicio;
        $iva_servicio=$base_servicio*$servicio_a_renovar["porcentaje_iva"];
        $total_abs_servicio=$base_servicio+$iva_servicio;        
        
        $renovar_servicio=false;
        $renovar_fecha=false;
        
        $mensaje_dto_adicional="";

        if ($descuento_servicio!=0) {

                $mensaje_dto_adicional.=". Descuento aplicado: ".$descuento_servicio." %";;

                }

        
                
                $precio_venta=$servicio_a_renovar["PRECIO_VENTA"];
                $nombre_servicio=$servicio_a_renovar["ARTICULO"];
                $concepto_desglose=$servicio_a_renovar["desglosar_como"];
                $subvencion=$servicio_a_renovar["SUBVENCION"];                                
                        
        // Emisión del nuevo recibo
                if ($modo_prueba!=true) {
                db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,descuento) VALUES (?,?,?,\'2\',?,?)', 'sssss', array((string)($id_inscripcion), (string)($id_servicio_renovar), (string)(date("Y-m-d")), (string)($num_sesiones), (string)($descuento_servicio))) or die(db_error());
                $id_linea_serv_cont=db_insert_id();
                }
                      
        // Busca fianza de servicios educativos
        
        $texto_fianza="";
        $precio_servicio_devuelto=0;
        $cantidad_a_devolver=0;
        $introduce_servicio_devuelto=false;
        
        $periodo_devolucion=true;
                                                                                                                                                                          
        if ($total_abs_servicio<=0) {
                $periodo_devolucion=false;
        }
        
        if ($periodo_devolucion==true) {
        
        $buscaServiciosReserva=db_query("SELECT * FROM tarifa WHERE reserva_de_plaza=1;") or die(db_error());

        $arrayServiciosReserva=array();
        while($servicio_reserva=db_fetch_array($buscaServiciosReserva)) {
                $arrayServiciosReserva[]=$servicio_reserva["ID"];
        }


        // $servicio_fianza="(servicios_contratados.id_servicio=318 OR servicios_contratados.id_servicio=329 OR servicios_contratados.id_servicio=330 OR servicios_contratados.id_servicio=338 OR servicios_contratados.id_servicio=356 OR servicios_contratados.id_servicio=365 OR servicios_contratados.id_servicio=376)";
        $servicio_fianza="servicios_contratados.id_servicio IN(".implode(",", $arrayServiciosReserva).")";

        $busca_servicios_fianzas=db_query("SELECT * FROM servicios_contratados,tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND $servicio_fianza AND servicios_contratados.id_alumno=".$id_inscripcion." AND cantidad>0 ORDER BY servicios_contratados.fecha_inicio");
        
        if (db_num_rows($busca_servicios_fianzas)!=0) {
        
                        $cantidad_a_devolver=0;
        
                $busca_servicios_contratados_devueltos=db_query("SELECT * FROM servicios_contratados WHERE $servicio_fianza AND servicios_contratados.id_alumno=".$id_inscripcion." AND servicios_contratados.cantidad=-1 ORDER BY servicios_contratados.fecha_inicio") or die(db_error()."-A");
                
                if (db_num_rows($busca_servicios_contratados_devueltos)==0) {
                        $cantidad_a_devolver=-1;
                }                                        
        
                if ($cantidad_a_devolver!=0) {
                        // No se le ha devuelto la fianza
                        $introduce_servicio_devuelto=true;
                                                        
                        $texto_fianza=", se ha incluido la devolución de la fianza";                                                        
                                                        
                        $datos_servicios_devolver=db_fetch_array($busca_servicios_fianzas);        
                        
                        $id_servicio_devuelto=$datos_servicios_devolver["id_servicio"];                                                
                        $precio_servicio_devuelto=$datos_servicios_devolver["PRECIO_VENTA"];
                        $iva_servicio_devuelto=$datos_servicios_devolver["porcentaje_iva"];
                        $concepto_desglose_devuelto=$datos_servicios_devolver["desglosar_como"];
                        $nombre_servicio_devuelto=$datos_servicios_devolver["ARTICULO"];
                        $subvencion_devuelto=$datos_servicios_devolver["SUBVENCION"];                                                
                                        
                        $total_abs_servicio_devuelto=$precio_servicio_devuelto*(1+$iva_servicio_devuelto);                                                      
                        
                        if ($modo_prueba!=true) {                
                                db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad) VALUES (?,?,?,\'2\',?)', 'ssss', array((string)($id_inscripcion), (string)($id_servicio_devuelto), (string)(date("Y-m-d")), (string)($cantidad_a_devolver))) or die(db_error());
                                }
                                
                        $id_linea_servicio_devuelto=db_insert_id();
                        
                }
                
        }
        } // Fin devolución                      
                      
                                
                $id_actividad=db_insert_id();

                $ejercicio_factura=date("Y");
                if (date("n")>7) {
                        $ejercicio_factura=date("Y")+1;
                      }
                      
                $num_nueva_factura="00001";

                                if ($modo_prueba!=true) {
                        $inserta_factura=db_query_params('INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision) VALUES (?, ?, ?, ?);', 'ssss', array((string)($num_nueva_factura), (string)($ejercicio_factura), (string)($id_inscripcion), (string)(date("Y-m-d")))) or die (db_error());
                        }

                $id_nueva_factura=db_insert_id();
                
                if ($modo_prueba!=true) {
                        $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssss', array((string)($id_nueva_factura), (string)($id_actividad), (string)($precio_venta), (string)($num_sesiones), (string)($descuento_servicio), (string)($servicio_a_renovar["porcentaje_iva"]), (string)($nombre_servicio), (string)($subvencion), (string)($concepto_desglose))) or die (db_error());
                        }

                                // Introduce devolución fianza si fuera necesario
                                if ($introduce_servicio_devuelto==true) {

                                        if ($modo_prueba!=true) {
                                        $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, \'-1\', \'0\', ?, ?, ?, ?);', 'sssssss', array((string)($id_nueva_factura), (string)($id_linea_serv_cont), (string)($precio_servicio_devuelto), (string)($iva_servicio_devuelto), 'Devolución ' . (string)($nombre_servicio_devuelto), (string)($subvencion_devuelto), (string)($concepto_desglose_devuelto))) or die (db_error());
                                        }

                                        }                                


                echo("<br />".($contador+1)." - <b>".mb_convert_case($nombres, MB_CASE_TITLE)."</b>, se le ha introducido el servicio: ".$num_sesiones." unidades de \"<b><i>".$nombre_servicio."</i></b>\"".$texto_fianza." y se le ha emitido un nuevo recibo por importe de ".number_format($total_abs_servicio-$total_abs_servicio_devuelto,2,",",".")." € (".number_format($total_abs_servicio,2,",",".")." - ".number_format($total_abs_servicio_devuelto,2,",",".").").".$texto_descuento."<br />");

                $total_a_facturar+=($total_abs_servicio-$total_abs_servicio_devuelto);
                $total_sin_devolucion+=$total_abs_servicio;
                $total_sesiones+=$num_sesiones;
                
                $total_bebes++;
                $total_infantil++;
                
                $sesiones_tipos[$servicio_a_renovar["tipo_neokids"]]+=$num_sesiones;
                
        $contador++;
        }

if ($total_a_facturar!=0) {

?>
<br />
<h3>Total sin devolución: <?=number_format($total_sin_devolucion,2,",",".")?> €</h3>
<h3>Total a facturar: <?=number_format($total_a_facturar,2,",",".")?> €</h3>
<h3>Total sesiones: <?=$total_sesiones;?></h3>
<h3>Nº total bebes/Infantil: <?=$total_tipos[2];?>/<?=$total_tipos[1];?></h3>
<h3>Nº sesiones bebes/Infantil: <?=$sesiones_tipos[2];?>/<?=$sesiones_tipos[1];?></h3>
<?php 

}

?>
<br>
<a href="calendario_control_renovaciones.php?dia=<?=date("Y-m-d",$fecha_retorno)?>" target="_blank" class="btn btn-primary">Salir al calendario e imprimir los recibos emitidos</a>
</body>
