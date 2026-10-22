<?php 
// Recibimos los datos
$usuario_curso=$_POST["curso"];
$proceso_decision=$_POST["proceso_decision"];
$usuario_fechamat_dia=$_POST["usuario_fechamat_dia"];
$usuario_fechamat_mes=$_POST["usuario_fechamat_mes"];
$usuario_fechamat_ano=$_POST["usuario_fechamat_ano"];

$usuario_fechabaja_dia=$_POST["usuario_fechabaja_dia"];
$usuario_fechabaja_mes=$_POST["usuario_fechabaja_mes"];
$usuario_fechabaja_ano=$_POST["usuario_fechabaja_ano"];

$tipo_mensualidad=$_POST["tipo_mensualidad"];

$num_dias_asiste=$_POST["num_dias_asiste"];
$nivel_actual=$_POST["nivel_actual"];
$nivel_proximo=$_POST["nivel_proximo"];

$comedor=$_POST["comedor"];
if (strlen($comedor)==0) {
        $comedor=0;
        }
$tipo_menu_comedor=$_POST["tipo_menu_comedor"];
if (strlen($tipo_menu_comedor)==0) {
        $tipo_menu_comedor=0;
        }
$numero_tomas=$_POST["numero_tomas"];
if (strlen($numero_tomas)==0) {
        $numero_tomas=0;
        }
$banco_entidad=$_POST["banco_entidad"];
$banco_poblacion=$_POST["banco_poblacion"];
$banco_titular=$_POST["banco_titular"];
$banco_dni_titular=$_POST["banco_dni_titular"];
$banco_domicilio=$_POST["banco_domicilio"];
$banco_cuenta1=$_POST["banco_cuenta1"];
$banco_cuenta2=$_POST["banco_cuenta2"];
$banco_cuenta3=$_POST["banco_cuenta3"];
$banco_cuenta4=$_POST["banco_cuenta4"];
$forma_de_pago=$_POST["forma_de_pago"];

$banco_cuenta=$banco_cuenta1." ".$banco_cuenta2." ".$banco_cuenta3." ".$banco_cuenta4;

$periodo=$_POST["periodo"];

// comprobamos los datos opcionales que necesiten ser comprobados
if ($usuario_fechamat_dia!="--" and $usuario_fechamat_mes!="--" and $usuario_fechamat_ano!="----") {
        $fecha_matricula=$usuario_fechamat_ano."/".$usuario_fechamat_mes."/".$usuario_fechamat_dia;
        if (!checkdate(intval($usuario_fechamat_mes),intval($usuario_fechamat_dia),intval($usuario_fechamat_ano))) {
                        $fecha_matricula="";
                        }
                } else {
                        $fecha_matricula="";
                }

if ($usuario_fechabaja_dia!="--" and $usuario_fechabaja_mes!="--" and $usuario_fechabaja_ano!="----") {
        $fecha_baja=$usuario_fechabaja_ano."/".$usuario_fechabaja_mes."/".$usuario_fechabaja_dia;
        if (!checkdate(intval($usuario_fechabaja_mes),intval($usuario_fechabaja_dia),intval($usuario_fechabaja_ano))) {
                        $fecha_baja="";
                        }
                } else {
                        $fecha_baja="";
                }
$resultado="";
if (strlen($usuario_curso)!=0) {

// Asignación de aula
$consulta_datos_alumno=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));
$datos_alumno=db_fetch_array($consulta_datos_alumno);

// actualiza datos del banco
$actualiza_banco=db_query_params('UPDATE usuarios SET banco_entidad=?, banco_poblacion_alu=?, banco_titular_alu=?, banco_dni_titular_alu=?, banco_domicilio_alu=?, banco_cuenta_alu=? WHERE id_usuario=?', 'sssssss', array((string)($banco_entidad), (string)($banco_poblacion), (string)($banco_titular), (string)($banco_dni_titular), (string)($banco_domicilio), (string)($banco_cuenta), $id_usuario_seleccionado));

// Consulta datos del curso en el que se inscribe
$datos_proximo_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$usuario_curso");
if (db_num_rows($datos_proximo_curso)!=0) {
        $info_proximo_curso=db_fetch_array($datos_proximo_curso);
        $fecha_inicio_proximo_curso=$info_proximo_curso["fecha_inicio"];
        if ($fecha_inicio_proximo_curso=="0000-00-00") {
                $fecha_inicio_proximo_curso=date("Y-m-d");
                }
        } else {
        $fecha_inicio_proximo_curso=date("Y-m-d");
        }

if ($datos_alumno["nonato"]==1) {
        $mes_nac=12;
        $anio_nac=date("Y",$info_proximo_curso["fecha_inicio"]);
        } else {
        // 1. Obtiene mes y año de nacimiento del niño
        $mes_nac=date("n",strtotime($datos_alumno["fecha_nacimiento"]));
        $anio_nac=date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
        }

// 3. Busca aula adecuada para su edad

$consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND mes_minimo>=$mes_nac AND anio_minimo>=$anio_nac AND mes_maximo<=$mes_nac AND anio_maximo<=$anio_nac AND tipo=0 ORDER BY anio_maximo,mes_maximo,anio_minimo,mes_minimo;");
$resultado="El alumno no ha sido inscrito. No hay aulas disponibles.";
$id_aula_donde_se_inscribe=0;

if (db_num_rows($consulta_aulas)==0) {
        // No hay aulas edecuadas
        // Busca mirando solo el año
        $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND anio_minimo>=$anio_nac AND anio_maximo<=$anio_nac AND tipo=0 ORDER BY anio_maximo,anio_minimo;");
        echo db_num_rows($consulta_aulas);
        if (db_num_rows($consulta_aulas)!=0) {
                $la_de_menor_edad=db_fetch_array($consulta_aulas);
                $resultado="No hemos encontrado un aula que se adapte exactamente a la edad del niño, ha sido inscrito en el aula '".$la_de_menor_edad["nombre"]."' teniendo en cuenta únicamente el año de nacimiento.";
                $id_aula_donde_se_inscribe=$la_de_menor_edad["id_aula"];
                } else {
                // Busca mirando solo el año máximo
                $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND anio_maximo<=$anio_nac AND tipo=0 ORDER BY anio_maximo,anio_minimo;");
                if (db_num_rows($consulta_aulas)!=0) {
                        $la_de_menor_edad=db_fetch_array($consulta_aulas);
                        $resultado="No hemos encontrado un aula que se adapte exactamente a la edad del niño, ha sido inscrito en el aula '".$la_de_menor_edad["nombre"]."' teniendo en cuenta únicamente el año de nacimiento y sin tener en cuenta la edad máxima.";
                        $id_aula_donde_se_inscribe=$la_de_menor_edad["id_aula"];
                        } else {
                        // Busca mirando solo el año mínimo
                        $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_actual AND id_curso=$usuario_curso AND anio_minimo>=$anio_nac AND tipo=0 ORDER BY anio_maximo,anio_minimo;");
                        if (db_num_rows($consulta_aulas)!=0) {
                                $la_de_menor_edad=db_fetch_array($consulta_aulas);
                                $resultado="No hemos encontrado un aula que se adapte exactamente a la edad del niño, ha sido inscrito en el aula '".$la_de_menor_edad["nombre"]."' teniendo en cuenta únicamente el año de nacimiento y sin tener en cuenta la edad mínima.";
                                $id_aula_donde_se_inscribe=$la_de_menor_edad["id_aula"];
                                }
                        }
                }
        } else {
        // Hay aulas adecuadas, calcula la ocupación, el promedio de edades y decide en cual inscribirlo
        $la_de_mayor_ocupacion=0;
        $nombre_de_la_de_mayor_ocupacion="";
        $mayor_ocupacion=0;
        while($listado_aulas=db_fetch_array($consulta_aulas)){
                // Calcula ocupación
                $consulta_ocupacion=db_query_params('SELECT * FROM alumnos WHERE aula=?', 's', array($listado_aulas["id_aula"]));
                $ocupacion_actual=db_num_rows($consulta_ocupacion);

                if (($ocupacion_actual<$listado_aulas["capacidad"]) and ($ocupacion_actual>=$mayor_ocupacion)) {
                        $la_de_mayor_ocupacion=$listado_aulas["id_aula"];
                        $nombre_de_la_de_mayor_ocupacion=$listado_aulas["nombre"];
                        $mayor_ocupacion=$ocupacion_actual;
                        }
                }
        if ($la_de_mayor_ocupacion!=0) {
                $resultado="El alumno ha sido inscrito en el aula '".$nombre_de_la_de_mayor_ocupacion."'";
                $id_aula_donde_se_inscribe=$la_de_mayor_ocupacion;
                } else {
                $resultado="El alumno no ha sido inscrito. Todas las aulas están completas.";
                }
        }

if ((strlen($usuario_curso)!=0) and ($id_aula_donde_se_inscribe!=0)) {
        // Comprueba si esta en lista de espera
        $busca_en_lista=db_query("SELECT * FROM lista_espera WHERE id_usuario=$id_usuario_seleccionado AND id_curso=$usuario_curso");
        if (db_num_rows($busca_en_lista)!=0) {
                $datos_lista=db_fetch_array($busca_en_lista);
                $actualiza_estado=db_query_params('UPDATE lista_espera SET fecha_baja=?, motivo_baja=1 WHERE id_usu_lista=?', 'ss', array((string)($fecha_matricula), $datos_lista["id_usu_lista"]));
                }
        db_query_params('INSERT INTO alumnos (id_usuario, id_curso, fecha_inscripcion, fecha_matricula, fecha_baja, id_centro, tipo_mensualidad, comedor, tipo_menu_comedor, numero_tomas, forma_de_pago, banco_entidad, banco_poblacion, banco_titular, banco_dni_titular, banco_domicilio, banco_cuenta, aula, proceso_decision, aula_ayudas, num_dias_asiste, nivel_actual,nivel_proximo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?,?)', 'sssssssssssssssssssssss', array((string)($id_usuario_seleccionado), (string)($usuario_curso), (string)(date("Y-m-d")), (string)($fecha_matricula), (string)($fecha_baja), (string)($centro_actual), (string)($tipo_mensualidad), (string)($comedor), (string)($tipo_menu_comedor), (string)($numero_tomas), (string)($forma_de_pago), (string)($banco_entidad), (string)($banco_poblacion), (string)($banco_titular), (string)($banco_dni_titular), (string)($banco_domicilio), (string)($banco_cuenta), (string)($id_aula_donde_se_inscribe), (string)($proceso_decision), (string)($id_aula_donde_se_inscribe), (string)($num_dias_asiste), (string)($nivel_actual), (string)($nivel_proximo)));
        
        $id_alumno_creado=db_insert_id();
        
        // crea el servicio seleccionado
        
		$busca_servicios=db_query("SELECT *,tipos_iva_servicios.categoria AS nombre_cat FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva$filtro AND tipos_iva_servicios.tipo_servicios=0 AND tarifa.categoria=1 ORDER BY ID");
                                        
		while($servicios=db_fetch_array($busca_servicios)) {
		
			$servicio_contratado=$_POST["servicio".$servicios["ID"]];		
			$cantidad_contratada=$_POST["cantidad".$servicios["ID"]];	
			if (strlen($cantidad_contratada)==0) {
				$cantidad_contratada=1;
			}
		
			if (strlen($servicio_contratado)!=0) {
						
				$descuento_servicio=0;
				switch ($servicio_contratado) {
				    case "1":
				        // Sin DTO
				        $descuento_servicio=0;
				        break;
				    case "2":
				        // Dto. 5%
				        $descuento_servicio=5;
 					    break;
				    case "3":
				        // Dto. 10%
				        $descuento_servicio=10;
 					    break; 
				    case "4":
				        // Dto. 15%
				        $descuento_servicio=15;
 					    break; 	 					    					    
					}
					
				// Inserta servicio
				$fecha_servicio=date("Y-m-d");
				$id_actividad=$servicios["ID"];
				
				db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,descuento,periodo) VALUES (?,?,?,\'2\',?,?,?)', 'ssssss', array((string)($id_alumno_creado), (string)($id_actividad), (string)($fecha_servicio), (string)($cantidad_contratada), (string)($descuento_servicio), (string)($periodo))) or die(db_error());	
			
				$id_actividad=db_insert_id();
			
				// Cobra el servicio
	
// INICIO COBRO	
$fecha=date("Y/m/d");
$filtro_curso_actual=" AND id_curso=$usuario_curso";
				
// Busca servicio contratado
$consulta1=array('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=?', 's', array($id_actividad));
$busca_servicio_contratado=db_query_params($consulta1[0], $consulta1[1], $consulta1[2]);
$datos_albaranes=db_fetch_array($busca_servicio_contratado) or die(db_error()."-3");

// Inserta importe en movimientos caja

$cantidad_cobrada=$cantidad_contratada;
if (strlen($cantidad_cobrada)==0) {
        $cantidad_cobrada=$datos_albaranes["cantidad"];
        }

$importe_cobrado=(round(($datos_albaranes["PRECIO_VENTA"]*(1+$datos_albaranes["porcentaje_iva"])*$cantidad_cobrada),4))*round((1-($datos_albaranes["descuento"]/100)),4);         

// Datos del alumno
$consulta_datos_alumno=db_query("SELECT alumnos.*, usuarios.*, alumnos.id_centro AS centro_alu FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario$filtro_curso_actual AND usuarios.id_usuario=".$id_usuario_seleccionado." ORDER BY id_curso DESC;") or die(db_error()."-4");
$datos_alumno=db_fetch_array($consulta_datos_alumno);

$porcentaje_pagado=floatval($_POST["pagado"]);

$id_nuevo_pago=0;

if ($porcentaje_pagado>0) {		
		$nuevo_pago = db_query("INSERT INTO pagos_a_cuenta (id_factura, id_centro, id_centro_cobro, fecha_pago, importe_pago, forma_pago, observaciones_pago, comentario, hora_pago, signo, anotado_por, id_asistencia) VALUES ('0', '".$centro_actual."', '".$centro_actual."', '".date("Y-m-d")."', '".round($importe_cobrado*$porcentaje_pagado,2)."', '0', 'Cobrado al matricular alumno', '".$cantidad_cobrada." Ud. de ".$datos_albaranes["ARTICULO"].", cobrado el día  ".date("d/m/Y",strtotime($fecha))."', '".date("H:i:s")."', 1, $idUsuario, '$id_registro')") or die (db_error()."-5");

		$id_nuevo_pago=db_insert_id();
		}
	
// Introduce factura
$ejercicio_factura=date("Y");
if (date("n")>7) {
        $ejercicio_factura=date("Y")+1;
      }
      
// Busca numero de factura
$num_nueva_factura="00001";


$inserta_factura=db_query_params('INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision, id_asistencia) VALUES (?, ?, ?, ?,?);', 'sssss', array((string)($num_nueva_factura), (string)($ejercicio_factura), (string)($id_alumno_creado), (string)(date("Y-m-d")), (string)($id_registro))) or die (db_error());
$id_nueva_factura=db_insert_id();


$concepto_desglose=$datos_albaranes["desglosar_como"];
$nombre_servicio=$datos_albaranes["ARTICULO"];
$inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar,periodo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?);', 'ssssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($cantidad_cobrada), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose), (string)($periodo))) or die (db_error());

if ($porcentaje_pagado>0) {
		// Relaciona pago con factura
		$relaciona_pago=db_query_params('UPDATE pagos_a_cuenta SET id_factura=? WHERE id_pago=?', 'ss', array($id_nueva_factura, $id_nuevo_pago)) or die(db_error());
		}
// FIN COBRO			
			
			
				}
		
		
			}
        
        }
}
?>
