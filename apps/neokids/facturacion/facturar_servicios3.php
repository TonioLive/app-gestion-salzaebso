<?php  require("../configuracion/parte_superior_privado.php");
$num_errores=0;

$id_cliente=$_POST["id_cliente"];
$tipo_factura=$_POST["tipo_factura"];
$id_curso_actual=$_POST["id_curso_actual"];
$num_factura=$_POST["num_factura"];
$ejercicio=$_POST["ejercicio"];
$dia=$_POST["dia"];
$mes=$_POST["mes"];
$ano=$_POST["ano"];

if ($id_cliente==0) {
                $nombre_cliente="Todos los alumnos";
        } else {
                // Busca datos del cliente
                $consulta_datos_cliente = db_query_params('SELECT alumnos.*, usuarios.*,alumnos.id_centro AS centro_inscripcion FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_alumno=?', 's', array($id_cliente)) or die (db_error());

                $datos_del_cliente=db_fetch_array($consulta_datos_cliente);

                $nombre_cliente=$datos_del_cliente["Apellido1"]." ".$datos_del_cliente["Apellido2"].", ".$datos_del_cliente["Nombre"];
                $id_aula_cliente=$datos_del_cliente["aula"];
                $id_usuario_cliente=$datos_del_cliente["id_usuario"];
                $fecha_alta_cliente=$datos_del_cliente["fecha_matricula"];
                $fecha_baja_cliente=$datos_del_cliente["fecha_baja"];
                $comedor_cliente=$datos_del_cliente["comedor"];
                $tipo_menu_cliente=$datos_del_cliente["tipo_menu_comedor"];
                $numero_tomas=$datos_del_cliente["numero_tomas"];
                
                // Consulta datos del curso
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_del_cliente["id_curso"]));

                $datos_curso_actual=db_fetch_array($busca_curso_actual);

                $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso=$datos_curso_actual["fecha_fin"];

                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_del_cliente["centro_inscripcion"]));
                if (db_num_rows($busca_datos_centro)!=0) {

                        $datos_centro=db_fetch_array($busca_datos_centro);
                        $precio_hora_extra=$datos_centro["precio_hora_extra"];
                        $precio_menu[0]=$datos_centro["precio_menu_3"];
                        $precio_menu[1]=$datos_centro["precio_menu_1"];
                        $precio_menu[2]=$datos_centro["precio_menu_2"];
                        $precio_menu[3]=$datos_centro["precio_menu_3"];
                        $precio_menu[4]=$datos_centro["precio_menu_4"];
                        $precio_menu[5]=$datos_centro["precio_menu_5"];
                        $precio_semana[0]=$datos_centro["precio_semana_3"];
                        $precio_semana[1]=$datos_centro["precio_semana_1"];
                        $precio_semana[2]=$datos_centro["precio_semana_2"];
                        $precio_semana[3]=$datos_centro["precio_semana_3"];
                        $precio_semana[4]=$datos_centro["precio_semana_4"];
                        $precio_semana[5]=$datos_centro["precio_semana_5"];
                        $precio_quincena[0]=$datos_centro["precio_quincena_3"];
                        $precio_quincena[1]=$datos_centro["precio_quincena_1"];
                        $precio_quincena[2]=$datos_centro["precio_quincena_2"];
                        $precio_quincena[3]=$datos_centro["precio_quincena_3"];
                        $precio_quincena[4]=$datos_centro["precio_quincena_4"];
                        $precio_quincena[5]=$datos_centro["precio_quincena_5"];
                        $precio_mes[0]=$datos_centro["precio_mes_3"];
                        $precio_mes[1]=$datos_centro["precio_mes_1"];
                        $precio_mes[2]=$datos_centro["precio_mes_2"];
                        $precio_mes[3]=$datos_centro["precio_mes_3"];
                        $precio_mes[4]=$datos_centro["precio_mes_4"];
                        $precio_mes[5]=$datos_centro["precio_mes_5"];

if ($datos_del_cliente["comedor_especial"]==1) {
        $precio_mes[0]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[1]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[2]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[3]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[4]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[5]=$datos_del_cliente["comedor_especial_precio"];
        }
                        
// Comprueba si es horario de media jornada

if ($datos_del_cliente["tipo_mensualidad"]!=1) {
$hora_entrada_centro_man=$datos_centro["hora_entrada1"];
$hora_salida_centro_man=$datos_centro["hora_salida2"];
$hora_entrada_centro_tar=$datos_centro["hora_entrada1"];
$hora_salida_centro_tar=$datos_centro["hora_salida2"];
$division_horas=($datos_centro["division_horas"]/60);
$periodo_gracia=($datos_centro["periodo_gracia"]*60);
$horas_comida=round(((strtotime($hora_entrada_centro_tar)-strtotime($hora_salida_centro_man))/3600),2);
if ($horas_comida<0) {
        $horas_comida=0;
        }
} else {
$hora_entrada_centro_man=$datos_centro["hora_entrada1_mj"];
$hora_salida_centro_man=$datos_centro["hora_salida1_mj"];
$hora_entrada_centro_tar=$datos_centro["hora_entrada2_mj"];
$hora_salida_centro_tar=$datos_centro["hora_salida2_mj"];
$division_horas=($datos_centro["division_horas"]/60);
$periodo_gracia=($datos_centro["periodo_gracia"]*60);
$horas_comida=0;
}
                        }
        }
?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Facturación de servicios del alumno: <?=$nombre_cliente?></b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                                 <tr>
                                 <td align="center">
<?php 
        // Inserta la factura

        $inserta_factura=db_query_params('INSERT INTO facturas_emitidas (serie, num_factura, ejercicio_factura, id_cliente, fecha_emision,facturada) VALUES (\'EDU\',?, ?, ?, ?,\'1\');', 'ssss', array((string)($num_factura), (string)($ejercicio), (string)($id_cliente), (string)($ano) . '/' . (string)($mes) . '/' . (string)($dia))) or die (db_error());
        $id_nueva_factura=db_insert_id();

        $consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=$id_cliente ORDER BY servicios_contratados.fecha_inicio DESC";
        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

        $numRecords = db_num_rows ($consulta_albaranes_cliente);
        $contador=0;

       if ($numRecords!=0) {

        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){

                        // Determina si el servicio es periodico o unico
                        if ($datos_albaranes["periodicidad"]==1) {
                                // Mensual
                                $nombre_servicio_1=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) {
                                        $nombre_servicio_1=$nombre_servicio_1." ".$datos_albaranes["notas"];
                                        }

                                $desde_fecha=date("n/Y",strtotime($datos_albaranes["fecha_inicio"]));
                                $mes_inicio=date("n",strtotime($datos_albaranes["fecha_inicio"]));
                                $anio_inicio=date("Y",strtotime($datos_albaranes["fecha_inicio"]));
                                $mes_fin=date("n",strtotime($datos_albaranes["fecha_fin"]))+1;
                                $anio_fin=date("Y",strtotime($datos_albaranes["fecha_fin"]));
                                $c_fecha=$desde_fecha;
                                $c_mes=$mes_inicio;
                                $c_anio=$anio_inicio;
                                if ($mes_fin==13) {
                                                $mes_fin=1;
                                                $anio_fin++;
                                                }
                                $hasta_fecha=$mes_fin."/".$anio_fin;

                                while($c_fecha!=$hasta_fecha) {
                                        $nombre_servicio=$nombre_servicio_1." ".$nombre_mes[$c_mes]." de ".$c_anio;

                                        $albaran_actual=$_POST["albaran_".$datos_albaranes["id_servicio_contratado"]."_".$c_mes."_".$c_anio];

                                        if ($albaran_actual=="1") {
                                                $concepto_desglose=$datos_albaranes["desglosar_como"];
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["cantidad"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                }

                                        $c_mes++;
                                        if ($c_mes==13) {
                                                $c_mes=1;
                                                $c_anio++;
                                                }
                                        $c_fecha=$c_mes."/".$c_anio;
                                }

                                } else {
                                // Unico
                                $nombre_servicio=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) { $nombre_servicio=$nombre_servicio.". ".$datos_albaranes["notas"]; }

                                $albaran_actual=$_POST["albaran_".$datos_albaranes["id_servicio_contratado"]];

                                if ($albaran_actual=="1") {
                                                $concepto_desglose=$datos_albaranes["desglosar_como"];
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["cantidad"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                }
                                }
                        }
        }
        // Final servicios propios
                        $consulta1="SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_aula=$id_aula_cliente ORDER BY servicios_contratados_aula.fecha_inicio DESC";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows ($consulta_albaranes_cliente);

                        if ($numRecords!=0) {

                        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){

                        // Determina si el servicio es periodico o unico
                        if ($datos_albaranes["periodicidad"]==1) {
                                // Mensual
                                $nombre_servicio_1=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) {
                                        $nombre_servicio_1=$nombre_servicio_1." ".$datos_albaranes["notas"];
                                        }

                                $desde_fecha=date("n/Y",strtotime($datos_albaranes["fecha_inicio"]));
                                $mes_inicio=date("n",strtotime($datos_albaranes["fecha_inicio"]));
                                $anio_inicio=date("Y",strtotime($datos_albaranes["fecha_inicio"]));
                                $mes_fin=date("n",strtotime($datos_albaranes["fecha_fin"]))+1;
                                $anio_fin=date("Y",strtotime($datos_albaranes["fecha_fin"]));
                                $c_fecha=$desde_fecha;
                                $c_mes=$mes_inicio;
                                $c_anio=$anio_inicio;
                                if ($mes_fin==13) {
                                                $mes_fin=1;
                                                $anio_fin++;
                                                }
                                $hasta_fecha=$mes_fin."/".$anio_fin;

                                while($c_fecha!=$hasta_fecha) {
                                        $nombre_servicio=$nombre_servicio_1." ".$nombre_mes[$c_mes]." de ".$c_anio;

                                        $albaran_actual=$_POST["albaran_aula_".$datos_albaranes["id_servicio_contratado"]."_".$c_mes."_".$c_anio];

                                        if ($albaran_actual=="1") {
                                                $concepto_desglose=$datos_albaranes["desglosar_como"];
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion, subvencion, concepto_desglosar) VALUES (?, \'0\', ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["cantidad"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), 'au_' . (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                }

                                        $c_mes++;
                                        if ($c_mes==13) {
                                                $c_mes=1;
                                                $c_anio++;
                                                }
                                        $c_fecha=$c_mes."/".$c_anio;
                                }

                                } else {
                                // Unico
                                $nombre_servicio=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) { $nombre_servicio=$nombre_servicio.". ".$datos_albaranes["notas"]; }

                                $albaran_actual=$_POST["albaran_aula_".$datos_albaranes["id_servicio_contratado"]];

                                if ($albaran_actual=="1") {
                                                $concepto_desglose=$datos_albaranes["desglosar_como"];
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion, subvencion, concepto_desglosar) VALUES (?, \'0\', ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["cantidad"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), 'au_' . (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                }
                                }
                        }
                }
        // Final servicios aula

                        $mes_inicio_curso=date("n",strtotime($fecha_inicio_curso));
                        $ano_inicio_curso=date("Y",strtotime($fecha_inicio_curso));
                        $mes_fin_curso=date("n",strtotime($fecha_fin_curso));
                        $ano_fin_curso=date("Y",strtotime($fecha_fin_curso));
                        $mes_fin_curso=$mes_inicio_curso+(12*($ano_fin_curso-$ano_inicio_curso));
                        $c_anios=$ano_inicio_curso;
                        for ($c_meses_curso=$mes_inicio_curso;$c_meses_curso<=$mes_fin_curso;$c_meses_curso++) {
                                if ($c_meses_curso==13) {
                                        $c_anios++;
                                        }
                                if ($c_meses_curso>12) {
                                        $mes_actual=$c_meses_curso-12;
                                        } else {
                                        $mes_actual=$c_meses_curso;
                                        }

                                $nombre_servicio="Horas extra sueltas ".$nombre_mes[$mes_actual];

                                $albaran_actual=$_POST["albaran_he_".$mes_actual."_".$c_anios];

                                if (strlen($albaran_actual)!=0) {
                                                $concepto_desglose=1;
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion, concepto_desglosar) VALUES (?, \'0\', ?, ?, \'0\', \'0\', ?, ?, ?);', 'ssssss', array((string)($id_nueva_factura), (string)($precio_hora_extra), (string)($albaran_actual), (string)($nombre_servicio), 'he_' . (string)($mes_actual) . '_' . (string)($c_anios), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                }

                                $albaran_actual=$_POST["albaran_he_f_".$mes_actual."_".$c_anios];

                                if (strlen($albaran_actual)!=0) {
                                                $concepto_desglose=1;
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion) VALUES (?, \'0\', ?, ?, \'0\', \'0\', ?, ?);', 'sssss', array((string)($id_nueva_factura), (string)($precio_hora_extra), (string)($albaran_actual), (string)($nombre_servicio), 'he_' . (string)($mes_actual) . '_' . (string)($c_anios))) or die (db_error());
                                                $contador=$contador+1;
                                                }
                                }

        // Final horas extra consumidas

                        // Comprueba si tiene contratado el servico de comedor
                        /*
                        // $tipo_menu_cliente
                        $nombre_tipo_menu_cliente[0]="Desconocido";
                        $nombre_tipo_menu_cliente[1]="Pequeños";
                        $nombre_tipo_menu_cliente[2]="Mayores";
                        $nombre_tipo_menu_cliente[3]="Colegial";
                        $nombre_tipo_menu_cliente[4]="Toma";
                        $nombre_tipo_menu_cliente[5]="Bebe";

                        if (($comedor_cliente==3) or ($comedor_cliente==1)) {
                                $mes_inicio_comedor=date("n",strtotime($fecha_inicio_curso));
                                $ano_inicio_comedor=date("Y",strtotime($fecha_inicio_curso));
                                $mes_fin_comedor=date("n",strtotime($fecha_fin_curso));
                                $ano_fin_comedor=date("Y",strtotime($fecha_fin_curso));
                                $mes_fin_comedor=$mes_inicio_curso+(12*($ano_fin_curso-$ano_inicio_curso));
                                $c_anios=$ano_inicio_comedor;
                                for ($c_meses_comedor=$mes_inicio_comedor;$c_meses_comedor<=$mes_fin_comedor+1;$c_meses_comedor++) {
                                        if ($c_meses_comedor==13) {
                                                $c_anios++;
                                                }
                                        if ($c_meses_comedor>12) {
                                                $mes_actual=$c_meses_comedor-12;
                                                } else {
                                                $mes_actual=$c_meses_comedor;
                                                }

                                        $nombre_servicio="Comedor ".$nombre_mes[$mes_actual]." de ".$c_anios.", Tipo de menú ".$nombre_tipo_menu_cliente[$tipo_menu_cliente];
                                        if ($comedor_cliente==3) {
                                                $nombre_servicio=$nombre_servicio." (régimen)";
                                                }

                                        $albaran_actual=$_POST["comedor_".$mes_actual."_".$c_anios];

                                        if ($tipo_menu_cliente==4) {
                                                $cantidad_menuses=$numero_tomas;
                                                } else {
                                                $cantidad_menuses=1;
                                                }

                                        if (strlen($albaran_actual)!=0) {
                                                $concepto_desglose=2;
                                                $inserta_detalle=mysql_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion, concepto_desglosar) VALUES ('".$id_nueva_factura."', '0', '".$precio_mes[$tipo_menu_cliente]."', '$cantidad_menuses', '0', '0', '".$nombre_servicio."', 'co_".$mes_actual."_".$c_anios."', '$concepto_desglose');") or die (mysql_error());
                                                $contador=$contador+1;
                                                }
                                        }
                                } else {
                                */
                                $consulta_asistencia_comedor=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND (tipo_menu=1 OR tipo_menu=3);', 'ss', array($id_usuario_cliente, $id_aula_cliente));
                                while($asistencia_comedor=db_fetch_array($consulta_asistencia_comedor)) {

                                        $nombre_servicio="Comedor suelto día: ".date("d/m/Y",strtotime($asistencia_comedor["fecha"])).", Tipo de menú ".$nombre_tipo_menu_cliente[$asistencia_comedor["tipo_menu_comedor"]];
                                        if ($asistencia_comedor["tipo_menu"]==3) {
                                                $nombre_servicio=$nombre_servicio." (régimen)";
                                                }

                                        $albaran_actual=$_POST["comedor_dia_".$asistencia_comedor["id_servicio_consumido"]];

                                        if (strlen($albaran_actual)!=0) {
                                                   if ($asistencia_comedor["tipo_menu_comedor"]==4) {
                                                                $cantidad_menuses=$numero_tomas;
                                                                } else {
                                                                $cantidad_menuses=1;
                                                                }
                                                $concepto_desglose=2;
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion, concepto_desglosar) VALUES (?, \'0\', ?, ?, \'0\', \'0\', ?, ?, ?);', 'ssssss', array((string)($id_nueva_factura), (string)($precio_menu[$asistencia_comedor["tipo_menu_comedor"]]), (string)($cantidad_menuses), (string)($nombre_servicio), 'cod_' . (string)($asistencia_comedor["id_servicio_consumido"]), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                }

                                        }
                                // }

        // Fin menu consumidos
        
                        // Servicios extra
                        $mostrar_extras=1;
                        if ($mostrar_extras==1) {

                        // Comprueba la asitencia al servicio

                                $consulta_servicos_extra=db_query_params('SELECT * FROM servicios_consumidos_asistencia, tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=? AND tarifa.CONTROL_ASISTENCIA=1 AND tarifa.tipo_facturacion_extra=0;', 's', array($id_usuario_cliente));
                                while($servicios_extra=db_fetch_array($consulta_servicos_extra)) {

                                        // Comprueba la asistencia al comedor
                                        if ($servicios_extra["hora_entrada"]!="") {

                                        // Comprueba si el alumno tiene contratado el comedor normal
                                        if (($comedor_cliente==2) or ($servicios_extra["anula_comedor"]==0)) {
                                        
                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2=array('SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.cod_facturacion=? ORDER BY facturas_emitidas.fecha_emision DESC', 'ss', array($id_cliente, 'cod_e_' . (string)($servicios_extra["id_servicio_consumido"])));
                                        $consulta_factura = db_query_params($consulta2[0], $consulta2[1], $consulta2[2]) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);

                                // Comprueba fechas
                                $fecha_albaran=strtotime($servicios_extra["fecha"]);
                                $fecha_ini=strtotime($mes_i."/".$dia_i."/".$ano_i);
                                $fecha_fin=strtotime($mes_f."/".$dia_f."/".$ano_f);
                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                        $contador=$contador+1;
                                        $seleccionado=" checked";
                                        $color_fondo="#B9E9BE";
                                        } else {
                                        $seleccionado="";
                                        $color_fondo="#FFFFFF";
                                        }
										
                                        if (strlen($_POST["extra_dia_".$servicios_extra["id_servicio_consumido"]])!=0) {
                                        
                                                $nombre_servicio="Asistencia ".$servicios_extra["ARTICULO"]." día: ".date("d/m/Y",strtotime($servicios_extra["fecha"]));
                                                $precio_linea=$servicios_extra["PRECIO_VENTA"];
                                                $cantidad_servicios=1;
                                                $concepto_desglose=2;
                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion, concepto_desglosar) VALUES (?, \'0\', ?, ?, \'0\', \'0\', ?, ?, ?);', 'ssssss', array((string)($id_nueva_factura), (string)($precio_linea), (string)($cantidad_servicios), (string)($nombre_servicio), 'cod_e_' . (string)($servicios_extra["id_servicio_consumido"]), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                }
                                        }
                                        }
                                    }
                        }
                        // Fin servicios extra
        
        if ($contador==0) {
                          // Borramos la factura
                          $nueva_factura=db_query_params('DELETE FROM facturas_emitidas WHERE id_factura=?;', 's', array($id_nueva_factura)) or die (db_error());

                          $num_errores=$num_errores+1;
                          $errores[$num_errores]="No se ha seleccionado ningún albarán. No se ha creado la factura.";
                          }

if ($num_errores==0) {
if ($id_factura_fo==0) {
?>
<p align="center"><br><br>
<font size=2 face="<?=$fuente_general;?>">La factura nº:<?=$num_factura;?>/<?=$ejercicio;?> se ha creado correctamente.<br><br>
<font size=2 face="<?= $fuente_general; ?>"><a href="<?=$url_web?>/pdf/factura.php?id_factura=<?=$id_nueva_factura;?>" target="_blank">Vista Previa/Imprimir factura</a></font><br><br>
<a href="facturar_servicios.php?curso_seleccionado=<?=$id_curso_actual;?>">Facturar a otro alumno</a> | <a href="gestion_facturacion.php">Volver</a></font><br><br>
</p>
<?php 
} else {
$array_facturas=$id_factura_fo.",".$id_nueva_factura;
?>
<p align="center"><br><br>
<font size=2 face="<?=$fuente_general;?>">Se han emitido 2 facturas.<br><br>
<font size=2 face="<?= $fuente_general; ?>"><a href="<?=$url_web?>/pdf/facturas.php?facturas=<?=$array_facturas;?>" target="_blank">Vista Previa/Imprimir facturas emitidas</a></font><br><br>
<a href="facturar_servicios.php?curso_seleccionado=<?=$id_curso_actual;?>">Facturar a otro alumno</a> | <a href="gestion_facturacion.php">Volver</a></font><br><br>
</p>
<?php 
}
} else {
?><br><br>
<font size=2>
<p align="center"><b><i>
Se han detectado errores al crear la factura.<br>Vuelve a intentarlo:<br>
</i></b></p>
<p align="center">
<font face="<?php  echo $fuente_general; ?>">
<table width="100%" bgcolor="#ececec">
<?php 
    for ($contador=1; $contador<=$num_errores; $contador=$contador+1)
    {
      print "<tr><td><font face=\"Verdana\" size=1 color=\"".$color_fuente."\"><b>- $errores[$contador]</b></font></td></tr>";
    }
?>
</table>
<?php 
  print "</small></small><br><br>";
  print "<input type=\"button\" value=\"<< Volver\" onclick=\"window.location.href='facturar_servicios.php'\" id=1 name=1></p></font>";
  }
                                 ?></td>
                                 </tr>
                         </table>
                </td>
        </tr>
</table><br><br>
<?php  require("../configuracion/parte_inferior.php"); ?>
