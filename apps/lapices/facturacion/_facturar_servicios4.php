<?php  require("../configuracion/parte_superior_privado.php");
$num_errores=0;

$id_cliente=$_POST["id_cliente"];
$tipo_factura=$_POST["tipo_factura"];
$num_factura=$_POST["num_factura"];
$num_factura1=$num_factura;
$ejercicio=$_POST["ejercicio"];
$dia=$_POST["dia"];
$mes=$_POST["mes"];
$ano=$_POST["ano"];
$dia_i=$_POST["dia_i"];
$mes_i=$_POST["mes_i"];
$ano_i=$_POST["ano_i"];
$dia_f=$_POST["dia_f"];
$mes_f=$_POST["mes_f"];
$ano_f=$_POST["ano_f"];

$array_facturas="";

if ($dia_i=="--" || $mes_i=="--" || $ano_i=="----")
                {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="No has completado la fecha de de inicio del periodo.";
                } else {
                        if (!checkdate(intval($mes_i),intval($dia_i),intval($ano_i))) {
                                  $num_errores=$num_errores+1;
                                  $errores[$num_errores]="La fecha de inicio del periodo escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                                 }
                        }
if ($dia_f=="--" || $mes_f=="--" || $ano_f=="----")
                {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="No has completado la fecha de de finalización del periodo.";
                } else {
                        if (!checkdate(intval($mes_f),intval($dia_f),intval($ano_f))) {
                                  $num_errores=$num_errores+1;
                                  $errores[$num_errores]="La fecha de finalización del periodo escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                                 }
                        }
if ($num_errores==0) {

$inicio_periodo_timestamp=strtotime($ano_i."/".$mes_i."/".$ano_i);
$fin_periodo_timestamp=strtotime($ano_f."/".$mes_f."/".$ano_f);

if ($id_cliente==0) {
                $nombre_cliente="Todos los alumnos";
        } else {
                // Busca datos del cliente
                $consulta_datos_cliente = db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_alumno=".$id_cliente) or die (db_error());

                $datos_del_cliente=db_fetch_array($consulta_datos_cliente);

                $nombre_cliente=$datos_del_cliente["Apellido1"]." ".$datos_del_cliente["Apellido2"].", ".$datos_del_cliente["Nombre"];
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
        // Bucle de clientes
        $contador_alumnos=0;
        $contador_facturas=0;
        $consulta_listado_alumnos=db_query("SELECT alumnos.*, usuarios.*,alumnos.id_centro AS centro_inscripcion FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_centro=$centro_actual") or die(db_error());
        while($listado_alumnos=db_fetch_array($consulta_listado_alumnos)) {
        $nombre_alumno=$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"].", ".$listado_alumnos["Nombre"];
        $id_cliente=$listado_alumnos["id_alumno"];
        
                $id_aula_cliente=$listado_alumnos["aula"];
                $id_usuario_cliente=$listado_alumnos["id_usuario"];
                $fecha_alta_cliente=$listado_alumnos["fecha_matricula"];
                $fecha_baja_cliente=$listado_alumnos["fecha_baja"];
                $comedor_cliente=$listado_alumnos["comedor"];
                $tipo_menu_cliente=$listado_alumnos["tipo_menu_comedor"];
                $numero_tomas=$datos_del_cliente["numero_tomas"];
                
                // Consulta datos del curso
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$listado_alumnos["id_curso"]);

                $datos_curso_actual=db_fetch_array($busca_curso_actual);

                $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso=$datos_curso_actual["fecha_fin"];

                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alumnos["centro_inscripcion"]);
                if (db_num_rows($busca_datos_centro)!=0) {

                        $datos_centro=db_fetch_array($busca_datos_centro);
                        $precio_hora_extra=$datos_centro["precio_hora_extra"];
                        $precio_menu[1]=$datos_centro["precio_menu_1"];
                        $precio_menu[2]=$datos_centro["precio_menu_2"];
                        $precio_menu[3]=$datos_centro["precio_menu_3"];
                        $precio_menu[4]=$datos_centro["precio_menu_4"];
                        $precio_menu[5]=$datos_centro["precio_menu_5"];
                        $precio_semana[1]=$datos_centro["precio_semana_1"];
                        $precio_semana[2]=$datos_centro["precio_semana_2"];
                        $precio_semana[3]=$datos_centro["precio_semana_3"];
                        $precio_semana[4]=$datos_centro["precio_semana_4"];
                        $precio_semana[5]=$datos_centro["precio_semana_5"];
                        $precio_quincena[1]=$datos_centro["precio_quincena_1"];
                        $precio_quincena[2]=$datos_centro["precio_quincena_2"];
                        $precio_quincena[3]=$datos_centro["precio_quincena_3"];
                        $precio_quincena[4]=$datos_centro["precio_quincena_4"];
                        $precio_quincena[5]=$datos_centro["precio_quincena_5"];
                        $precio_mes[1]=$datos_centro["precio_mes_1"];
                        $precio_mes[2]=$datos_centro["precio_mes_2"];
                        $precio_mes[3]=$datos_centro["precio_mes_3"];
                        $precio_mes[4]=$datos_centro["precio_mes_4"];
                        $precio_mes[5]=$datos_centro["precio_mes_5"];
                        
                        $hora_entrada_centro_man=$datos_centro["hora_entrada1"];
                        $hora_salida_centro_man=$datos_centro["hora_salida1"];
                        $hora_entrada_centro_tar=$datos_centro["hora_entrada2"];
                        $hora_salida_centro_tar=$datos_centro["hora_salida2"];
                        $division_horas=($datos_centro["division_horas"]/60);
                        $periodo_gracia=($datos_centro["periodo_gracia"]*60);
                        }
                        
        // Comprueba si el numero de factura esta ocupado, si lo está buscamos el proximo libre
        $seguir=true;
        while($seguir==true) {
                $consulta_facturas=db_query("SELECT * FROM facturas_emitidas WHERE num_factura=".$num_factura." AND ejercicio_factura='".$ejercicio."'");
                if (db_num_rows($consulta_facturas)==0) {
                                $seguir=false;
                        } else {
                                $num_factura++;
                        }
                }
        
        // Inserta la factura
        if (strlen($num_factura)!=5) {
                $n_fact_completo="000000".$num_factura;
                $n_fact_completo=substr($n_fact_completo,(strlen($n_fact_completo)-5),5);
                $num_factura=$n_fact_completo;
                }
        
        $inserta_factura=db_query("INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision) VALUES ('".$num_factura."', '".$ejercicio."', '".$id_cliente."', '".$ano."/".$mes."/".$dia."');") or die (db_error());
        $id_nueva_factura=db_insert_id();

        $fecha_emision_factura_timestamp=strtotime($ano."/".$mes."/".$dia);

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

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.articulo=".$datos_albaranes["id_servicio_contratado"]." AND detalles_factura.concepto='$nombre_servicio' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $estafact = db_num_rows ($consulta_factura);
                                        if ($estafact==0) {
                                        
                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                                $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto) VALUES ('".$id_nueva_factura."', '".$datos_albaranes["id_servicio_contratado"]."', '".$datos_albaranes["PRECIO_VENTA"]."', '".$datos_albaranes["cantidad"]."', '".$datos_albaranes["descuento"]."', '".$datos_albaranes["porcentaje_iva"]."', '".$nombre_servicio."');") or die (db_error());
                                                                $contador=$contador+1;
                                                                $contador_alumnos++;
                                                                }
                                                }
                                                
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

                                // Comprueba si esta facturado
                                $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.articulo=".$datos_albaranes["id_servicio_contratado"]." ORDER BY facturas_emitidas.fecha_emision DESC";
                                $consulta_factura = db_query($consulta2) or die (db_error());

                                $estafact = db_num_rows ($consulta_factura);
                                if ($estafact==0) {
                                // Comprueba fechas
                                $fecha_albaran=strtotime($datos_albaranes["fecha_inicio"]);
                                $fecha_ini=strtotime($mes_i."/".$dia_i."/".$ano_i);
                                $fecha_fin=strtotime($mes_f."/".$dia_f."/".$ano_f);
                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto) VALUES ('".$id_nueva_factura."', '".$datos_albaranes["id_servicio_contratado"]."', '".$datos_albaranes["PRECIO_VENTA"]."', '".$datos_albaranes["cantidad"]."', '".$datos_albaranes["descuento"]."', '".$datos_albaranes["porcentaje_iva"]."', '".$nombre_servicio."');") or die (db_error());
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                }
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
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_albaranes["id_servicio_contratado"]." AND id_usuario=$id_cliente");

                        if (db_num_rows($busca_si_activado)==0) {
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
                                
                                $inicio_docencia=strtotime(date("Y/m",strtotime($fecha_alta_cliente))."/1");
                                $fin_docencia=strtotime(date("Y/m",strtotime($fecha_baja_cliente))."/1");
                                
                                while($c_fecha!=$hasta_fecha) {
                                
                                        // Comprueba si hay que facturar
                                        $f_at=strtotime($c_anio."/".$c_mes."/1");
                                        if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) {
                                
                                        $nombre_servicio=$nombre_servicio_1." ".$nombre_mes[$c_mes]." de ".$c_anio;

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='au_".$datos_albaranes["id_servicio_contratado"]."' AND detalles_factura.concepto='$nombre_servicio' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);

                                        if ($numRecords==0) {
                                        // Comprueba fechas
                                                $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                                $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                                $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                                $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion) VALUES ('".$id_nueva_factura."', '0', '".$datos_albaranes["PRECIO_VENTA"]."', '".$datos_albaranes["cantidad"]."', '".$datos_albaranes["descuento"]."', '".$datos_albaranes["porcentaje_iva"]."', '".$nombre_servicio."', 'au_".$datos_albaranes["id_servicio_contratado"]."');") or die (db_error());
                                                                $contador=$contador+1;
                                                                $contador_alumnos++;
                                                                }
                                                        }
                                                }
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
                                $inicio_docencia=strtotime($fecha_alta_cliente);
                                $fin_docencia=strtotime($fecha_baja_cliente);
                                $f_at=strtotime($datos_albaranes["fecha_inicio"]);
                                if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) {
                                
                                $nombre_servicio=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) { $nombre_servicio=$nombre_servicio.". ".$datos_albaranes["notas"]; }
                                
                                // Mira si el servicio está facturado y mira el nº de factura
                                $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='au_".$datos_albaranes["id_servicio_contratado"]."' ORDER BY facturas_emitidas.fecha_emision DESC";
                                $consulta_factura = db_query($consulta2) or die (db_error());

                                $numRecords = db_num_rows ($consulta_factura);

                                if ($numRecords==0) {
                                
                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($datos_albaranes["fecha_inicio"]);
                                        $fecha_ini=strtotime($mes_i."/".$dia_i."/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/".$dia_f."/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion) VALUES ('".$id_nueva_factura."', '0', '".$datos_albaranes["PRECIO_VENTA"]."', '".$datos_albaranes["cantidad"]."', '".$datos_albaranes["descuento"]."', '".$datos_albaranes["porcentaje_iva"]."', '".$nombre_servicio."', 'au_".$datos_albaranes["id_servicio_contratado"]."');") or die (db_error());
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                }
                                        }
                                }
                                }
                        }
                        }
                        }
                        // Final servicios aula
                        if ($fecha_emision_factura_timestamp<$fin_periodo_timestamp) {
                                        $fin_periodo_timestamp=$fecha_emision_factura_timestamp;
                                        }
                                        
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

                                // Comprueba las horas extra consumdidas durante el mes
                                $consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_usuario_cliente AND id_servicio=$id_aula_cliente AND month(fecha)=".$mes_actual." AND year(fecha)=".$c_anios." AND no_facturar=0;");
                                $total_horas_extra=0;
                                $total_horas_extra_fact=0;
                                




                                while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
                                
                                // Comprueba si esta entre las fechas indicadas
                                $fecha_hora_extra_timestamp=strtotime($datos_asistencia["fecha"]);
                                if (($fecha_hora_extra_timestamp>=$inicio_periodo_timestamp) and ($fecha_hora_extra_timestamp<=$fin_periodo_timestamp)) {
                                
                                        if ($datos_asistencia["turno"]==0) {
                                                $hora_entrada_centro=strtotime($hora_entrada_centro_man);
                                                $hora_salida_centro=strtotime($hora_salida_centro_man);
                                                } else {
                                                $hora_entrada_centro=strtotime($hora_entrada_centro_tar);
                                                $hora_salida_centro=strtotime($hora_salida_centro_tar);
                                                }
                $hora_entrada11=$datos_asistencia["hora_entrada"];
                if ($hora_entrada11=="") {
                        $hora_entrada11=intval($hora_entrada_centro);
                        } else {
                        $hora_entrada11=intval(strtotime($datos_asistencia["hora_entrada"]));
                        }
                $diferencia_entrada=intval($hora_entrada_centro)-$hora_entrada11;

                if ($diferencia_entrada>$periodo_gracia) {
                        $diferencia_entrada=round($diferencia_entrada/36)/100;
                        $diferencia_entrada=round((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_entrada=0;
                        }

                $hora_salida11=$datos_asistencia["hora_salida"];
                if ($hora_salida11=="") {
                        $hora_salida11=intval($hora_salida_centro);
                        } else {
                        $hora_salida11=intval(strtotime($datos_asistencia["hora_salida"]));
                        }

                $diferencia_salida=$hora_salida11-intval($hora_salida_centro);

                if ($diferencia_salida>$periodo_gracia) {
                        $diferencia_salida=round($diferencia_salida/36)/100;
                        $diferencia_salida=round((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_salida=0;
                        }

                // Si se queda a comer no se cobran horas
                if ($datos_asistencia["hora_entrada_c"]!="") {
                        if ($datos_asistencia["turno"]==0) {
                                $diferencia_salida=0;
                                } else {
                                $diferencia_entrada=0;
                                }
                        }

                                        // Busca bonos horas extra contratados durante todo el año
                                        $busca_bonos1=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_cliente AND tarifa.TIPO_SERVICIO=1 AND periodicidad=1 AND (fecha_inicio<='".$c_anios."-".$mes_actual."-1') AND (fecha_fin>='".$c_anios."-".$mes_actual."-1')") or die(db_error());
                                        $numero_bonos1=0;
                                        while($bonos1=db_fetch_array($busca_bonos1)) {
                                                $numero_bonos1=$numero_bonos1+$bonos1["cantidad"];
                                                }
                                        // Busca bonos horas extra contratados para este mes
                                        $busca_bonos2=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_cliente AND tarifa.TIPO_SERVICIO=1 AND periodicidad=2 AND  (month(fecha_inicio)=".$mes_actual.") AND (year(fecha_inicio)=".$c_anios.")") or die(db_error());
                                        $numero_bonos2=0;
                                        while($bonos2=db_fetch_array($busca_bonos2)) {
                                                $numero_bonos2=$numero_bonos2+$bonos2["cantidad"];
                                                }

                                        $horas_extra_hoy=($diferencia_entrada+$diferencia_salida);
                                        $horas_extra_hoy_fact=$horas_extra_hoy-$numero_bonos1-$numero_bonos2;

                                $total_horas_extra=$total_horas_extra+$horas_extra_hoy;
                                if ($horas_extra_hoy_fact>0) {
                                                $total_horas_extra_fact=$total_horas_extra_fact+$horas_extra_hoy_fact;
                                                }
                                  }
                                }
                                
                                // Comprueba si ha pasado la fecha límite

                                // Busca horas extra individuales
                                $busca_bonos3=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_cliente AND tarifa.TIPO_SERVICIO=2 AND periodicidad=2 AND (month(fecha_inicio)=".$mes_actual.") AND (year(fecha_inicio)=".$c_anios.")") or die(db_error());
                                $numero_bonos3=0;
                                while($bonos3=db_fetch_array($busca_bonos3)) {
                                                $numero_bonos3=$numero_bonos3+$bonos3["cantidad"];
                                                }
                                $total_horas_extra_fact=$total_horas_extra_fact-$numero_bonos3;
                                
                                }
                                
                                if ($total_horas_extra_fact<0) {
                                       $total_horas_extra_fact=0;
                                       }

                                // Convierte a hh:mm
                                $horas_extra_mostrar=$total_horas_extra;
                                $horas_extra_mostrar_fact=$total_horas_extra_fact;

                                $horas_extra1=round($horas_extra_mostrar,0);
                                if ($horas_extra1>$horas_extra_mostrar) {
                                                $horas_extra1=$horas_extra1-1;
                                               }
                               $minutos_extra1="00".(($horas_extra_mostrar-$horas_extra1)*60);
                               $minutos_extra1=substr($minutos_extra1,strlen($minutos_extra1)-2,2);
                               $horas_extra1="00".$horas_extra1;
                               $horas_extra1=substr($horas_extra1,strlen($horas_extra1)-2,2);
                               $horas_extra_mostrar=$horas_extra1.":".$minutos_extra1;

                               $horas_extra2=round($horas_extra_mostrar_fact,0);
                               if ($horas_extra2>$horas_extra_mostrar_fact) {
                                               $horas_extra2=$horas_extra2-1;
                                                }

                                $minutos_extra2="00".(($horas_extra_mostrar_fact-$horas_extra2)*60);
                                $minutos_extra2=substr($minutos_extra2,strlen($minutos_extra2)-2,2);
                                $horas_extra2="00".$horas_extra2;
                                $horas_extra2=substr($horas_extra2,strlen($horas_extra2)-2,2);
                                $horas_extra_mostrar_fact=$horas_extra2.":".$minutos_extra2;

                                if ($total_horas_extra_fact>0) {
                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND (detalles_factura.cod_facturacion='he_".(int)$mes_actual."_".$c_anios."' OR detalles_factura.cod_facturacion='he_".$mes_actual."_".$c_anios."') ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);
                                        
                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($mes_actual."/1/".$c_anios);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                        
                                        $nombre_servicio="Horas extra sueltas ".$nombre_mes[$mes_actual];
                                        
                                        if ($numRecords==0) {
                                                $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion) VALUES ('".$id_nueva_factura."', '0', '".$precio_hora_extra."', '".$total_horas_extra_fact."', '0', '0', '".$nombre_servicio."', 'he_".$mes_actual."_".$c_anios."');") or die (db_error());
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                } else {

                                                $total_cantidad=0;
                                                while($datos_de_factura=db_fetch_array($consulta_factura)) {
                                                        $total_cantidad=$total_cantidad+$datos_de_factura["cantidad"];
                                                        }
                                                // Comprueba si hay horas extra realizadas despues de la emisión de la factura
                                                if ($total_cantidad!=$total_horas_extra_fact) {
                                                        $faltan=$total_horas_extra_fact-$total_cantidad;
                                                        $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion) VALUES ('".$id_nueva_factura."', '0', '".$precio_hora_extra."', '".$faltan."', '0', '0', '".$nombre_servicio."', 'he_".$mes_actual."_".$c_anios."');") or die (db_error());
                                                        $contador=$contador+1;
                                                        $contador_alumnos++;
                                                        }
                                                }
                                                }
                                        }
                                }

                        // Final horas extra consumidas

                        // Comprueba si tiene contratado el servico de comedor

                        // $tipo_menu_cliente
                        $nombre_tipo_menu_cliente[0]="Desconocido";
                        $nombre_tipo_menu_cliente[1]="Pequeños";
                        $nombre_tipo_menu_cliente[2]="Mayores";
                        $nombre_tipo_menu_cliente[3]="Colegial";
                        $nombre_tipo_menu_cliente[4]="Toma";
                        $nombre_tipo_menu_cliente[5]="Bebe";

                        if (($comedor_cliente==3) or ($comedor_cliente==1)) {
                                $mes_inicio_comedor=date("n",strtotime($fecha_alta_cliente));
                                $ano_inicio_comedor=date("Y",strtotime($fecha_alta_cliente));
                                $mes_fin_comedor=date("n",strtotime($fecha_baja_cliente));
                                $ano_fin_comedor=date("Y",strtotime($fecha_baja_cliente));
                                $mes_fin_comedor=$mes_fin_comedor+(12*($ano_fin_comedor-$ano_inicio_comedor));
                                $c_anios=$ano_inicio_comedor;
                                for ($c_meses_comedor=$mes_inicio_comedor;$c_meses_comedor<=$mes_fin_comedor;$c_meses_comedor++) {
                                        if ($c_meses_comedor==13) {
                                                $c_anios++;
                                                }
                                        if ($c_meses_comedor>12) {
                                                $mes_actual=$c_meses_comedor-12;
                                                } else {
                                                $mes_actual=$c_meses_comedor;
                                                }
                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='co_".$mes_actual."_".$c_anios."' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);
                                        
                                        $nombre_servicio="Comedor ".$nombre_mes[$mes_actual]." de ".$c_anios.", Tipo de menú ".$nombre_tipo_menu_cliente[$tipo_menu_cliente];
                                        if ($comedor_cliente==3) {
                                                $nombre_servicio=$nombre_servicio." (régimen)";
                                                }
                                                
                                        if ($numRecords==0) {
                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($mes_actual."/1/".$c_anios);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                // Solo si pertenece al més próximo
                                                if (intval($mes)!=intval($mes_actual)) {
                                                   if ($tipo_menu_cliente==4) {
                                                                $cantidad_menuses=$numero_tomas;
                                                                } else {
                                                                $cantidad_menuses=1;
                                                                }
                                                        $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion) VALUES ('".$id_nueva_factura."', '0', '".$precio_mes[$tipo_menu_cliente]."', '$cantidad_menuses', '0', '0', '".$nombre_servicio."', 'co_".$mes_actual."_".$c_anios."');") or die (db_error());
                                                        $contador=$contador+1;
                                                        $contador_alumnos++;
                                                        }
                                                }
                                                }
                                        }
                                } else {
                                $consulta_asistencia_comedor=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_usuario_cliente AND id_servicio=$id_aula_cliente AND (tipo_menu=1 OR tipo_menu=3);");
                                while($asistencia_comedor=db_fetch_array($consulta_asistencia_comedor)) {
                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='cod_".$asistencia_comedor["id_servicio_consumido"]."' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);
                                        
                                        $nombre_servicio="Comedor suelto día: ".date("d/m/Y",strtotime($asistencia_comedor["fecha"])).", Tipo de menú ".$nombre_tipo_menu_cliente[$asistencia_comedor["tipo_menu_comedor"]];
                                        if ($asistencia_comedor["tipo_menu"]==3) {
                                                $nombre_servicio=$nombre_servicio." (régimen)";
                                                }
                                                
                                        if ($numRecords==0) {
                                                // Comprueba fechas
                                                $fecha_albaran=strtotime($asistencia_comedor["fecha"]);
                                                $fecha_ini=strtotime($mes_i."/".$dia_i."/".$ano_i);
                                                $fecha_fin=strtotime($mes_f."/".$dia_f."/".$ano_f);
                                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                   if ($asistencia_comedor["tipo_menu_comedor"]==4) {
                                                                $cantidad_menuses=$numero_tomas;
                                                                } else {
                                                                $cantidad_menuses=1;
                                                                }
                                                        $inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion) VALUES ('".$id_nueva_factura."', '0', '".$precio_menu[$asistencia_comedor["tipo_menu_comedor"]]."', '1', '0', '0', '".$nombre_servicio."', 'cod_".$asistencia_comedor["id_servicio_consumido"]."');") or die (db_error());
                                                        $contador=$contador+1;
                                                        $contador_alumnos++;
                                                        }
                                                }
                                        }
                                }

                        // Fin menu consumidos
        
        
        if ($contador==0) {
                          // Borramos la factura
                          $nueva_factura=db_query("DELETE FROM facturas_emitidas WHERE id_factura=".$id_nueva_factura.";") or die (db_error());
                          // $num_errores=$num_errores+1;
                          // $errores[$num_errores]="No se ha encontrado ningún servicio para el alumno <i>$nombre_alumno</i>. No se ha creado la factura.";
                          } else {
                          $array_facturas=$array_facturas.",".$id_nueva_factura;
                          $num_factura++;
                          $contador_facturas++;
                          }
}
if ($contador_facturas==0) {
                          // Borramos la factura
                          $num_errores=$num_errores+1;
                          $errores[$num_errores]="No se ha encontrado ningún servicio pendiente de facturar. No se han creado las facturas.";
                          }
if ($num_errores==0) {
$array_facturas=substr($array_facturas,1,strlen($array_facturas)-1);
?>
<p align="center"><br><br>
<font size=2 face="<?=$fuente_general;?>">Se han emitido <?=$contador_facturas?> facturas desde el nº:<?=$num_factura1;?>/<?=$ejercicio;?> hasta el nº <?=substr("00000".($num_factura-1),strlen("00000".($num_factura-1))-5,5);?>/<?=$ejercicio;?>.<br><br>
<font size=2 face="<?= $fuente_general; ?>"><a href="<?=$url_web?>/pdf/facturas.php?facturas=<?=$array_facturas;?>" target="_blank">Vista Previa/Imprimir facturas emitidas</a></font><br><br>
<a href="facturar_servicios.php">Facturar a otro alumno</a> | <a href="gestion_facturacion.php">Volver</a></font><br><br>
</p>
<?php 
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
