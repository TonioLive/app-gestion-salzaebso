<?php  require("../configuracion/parte_superior_privado_imprimir.php");
$num_errores=0;

$id_cliente=$_POST["id_cliente"];
$tipo_factura=$_POST["tipo_factura"];
$dia=$_POST["dia"];
$mes=$_POST["mes"];
$ano=$_POST["ano"];
$dia_i=$_POST["dia_i"];
$mes_i=$_POST["mes_i"];
$ano_i=$_POST["ano_i"];
$dia_f=$_POST["dia_f"];
$mes_f=$_POST["mes_f"];
$ano_f=$_POST["ano_f"];

$servicios_propios1=$_POST["servicios_propios1"];
$servicios_propios2=$_POST["servicios_propios2"];
$servicios_aula1=$_POST["servicios_aula1"];
$servicios_aula2=$_POST["servicios_aula2"];
$mostrar_horas_extra=$_POST["horas_extra"];
$mostrar_comedor=$_POST["comedor"];
$mostrar_comedor_extra=$_POST["comedor_extra"];

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

if (substr($id_cliente,0,1)=="A") {
                $id_aula=substr($id_cliente,1,strlen($id_cliente)-1);
                // Busca datos del aula
                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
                if (db_num_rows($busca_datos_aula)!=0) {
                        $datos_aula=db_fetch_array($busca_datos_aula);
                        $nombre_aula=$datos_aula["nombre"];
                        } else {
                        $nombre_aula="Aula desconocida";
                        }
                $nombre_cliente="Todos los alumnos del ".$nombre_aula;
        } else {
                // Busca datos del cliente
                $consulta_datos_cliente = db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_alumno=?', 's', array($id_cliente)) or die (db_error());

                $datos_del_cliente=db_fetch_array($consulta_datos_cliente);

                $nombre_cliente=$datos_del_cliente["Apellido1"]." ".$datos_del_cliente["Apellido2"].", ".$datos_del_cliente["Nombre"];
        }
?>
<br><br>
<table width="95%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" colspan=3 align="center">
                <font size=4 face="<?= $fuente_general; ?>"><b>Vista previa de Facturación de servicios de: <?=$nombre_cliente?></b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                                 <tr>
                                 <td align="center">
<?php 
        // Bucle de clientes
        $contador_alumnos=0;
        $contador_facturas=0;
        $total_absoluto_facturado=0;
        $total_absoluto_iva_facturado=0;
        $consulta_listado_alumnos=db_query_params('SELECT alumnos.*, usuarios.*,alumnos.id_centro AS centro_inscripcion FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 's', array($id_aula)) or die(db_error());

        while($listado_alumnos=db_fetch_array($consulta_listado_alumnos)) {
        
        $anular_comedor=false;
        $anular_horas_extra=false;
        
        $nombre_alumno=$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"].", ".$listado_alumnos["Nombre"];
        $id_cliente=$listado_alumnos["id_alumno"];
        
                $id_aula_cliente=$listado_alumnos["aula"];
                $id_usuario_cliente=$listado_alumnos["id_usuario"];
                $fecha_alta_cliente=$listado_alumnos["fecha_matricula"];
                $fecha_baja_cliente=$listado_alumnos["fecha_baja"];
                $comedor_cliente=$listado_alumnos["comedor"];
                $tipo_menu_cliente=$listado_alumnos["tipo_menu_comedor"];
                $numero_tomas=$listado_alumnos["numero_tomas"];
                
                // Consulta datos del curso
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($listado_alumnos["id_curso"]));

                $datos_curso_actual=db_fetch_array($busca_curso_actual);

                $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso=$datos_curso_actual["fecha_fin"];

                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($listado_alumnos["centro_inscripcion"]));
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

if ($listado_alumnos["tipo_mensualidad"]!=1) {
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
                        
                        $total_factura=0;
                        $total_iva=0;
?>
                                 <table border=1 cellpadding=2 cellspacing=0 bordercolor="#000000" style="border-collapse: collapse" width="100%">
                                 <tr>
                                 <td colspan=3 bgcolor="#efefef" align="right"><font size=2><b>Alumno:</b></font></td>
                                 <td colspan=5 bgcolor="#ffffff"><font size=2><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></td>
                                 </tr>
                                 <tr>
                                 <td colspan=3 bgcolor="#efefef" align="right"><font size=2><b>Periodo facturado:</b></font></td>
                                 <td colspan=5 bgcolor="#ffffff"><font size=2><?=$dia_i?>/<?=$mes_i?>/<?=$ano_i?> a <?=$dia_f?>/<?=$mes_f?>/<?=$ano_f?></font></td>
                                 </tr>
                                 <tr bgcolor="#efefef">
                                 <td><font size=2><b>Descripción</b></font></td>
                                 <td align="center"><font size=2><b>Cantidad</b></font></td>
                                 <td align="center"><font size=2><b>Precio</b></font></td>
                                 <td align="center"><font size=2><b>DTO</b></font></td>
                                 <td align="center"><font size=1><b>Base Imponible</b></font></td>
                                 <td align="center"><font size=2><b>IVA</b></font></td>
                                 <td align="center"><font size=1><b>Importe IVA</b></font></td>
                                 <td align="center"><font size=2><b>Total</b></font></td>
                                 </tr>
<?php 
        // Factura EDU
        
        $consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=$id_cliente AND tarifa.desglosar_como=3 ORDER BY servicios_contratados.fecha_inicio DESC";
        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

        $numRecords = db_num_rows ($consulta_albaranes_cliente);
        $contador=0;

       if ($numRecords!=0) {

        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){

                        // Determina si el servicio es periodico o unico
                        if ($datos_albaranes["periodicidad"]==1) {
                                if ($servicios_propios1==1) {
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

                                        $fecha_servicio_buscar=$nombre_mes[$c_mes]." de ".$c_anio;

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.articulo=".$datos_albaranes["id_servicio_contratado"]." AND detalles_factura.concepto like '%$fecha_servicio_buscar%' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $estafact = db_num_rows ($consulta_factura);
                                        if ($estafact==0) {

                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                        if (((($datos_albaranes["TIPO_SERVICIO"]!=1) or ($datos_albaranes["TIPO_SERVICIO"]!=0)) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==1)) and (intval($c_mes)!=intval($mes)))) {

                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                

                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                                <?php 
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
                                }
                                } else {

                                if ($servicios_propios2==1) {

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

                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                

                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                }
                                        }
                                }
                                }
                        }
        }

                        // Final servicios propios
                        $consulta1="SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_aula=$id_aula_cliente AND tarifa.desglosar_como=3 ORDER BY servicios_contratados_aula.fecha_inicio DESC";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows ($consulta_albaranes_cliente);

                        if ($numRecords!=0) {

                        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){
                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_albaranes["id_servicio_contratado"], $id_cliente));

                        if (db_num_rows($busca_si_activado)==0) {

                        if ($servicios_aula1==1) {
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
                                        // if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) { // Restricción facturas cosas solo del curso

                                        $nombre_servicio=$nombre_servicio_1." ".$nombre_mes[$c_mes]." de ".$c_anio;

                                        $fecha_servicio_buscar=$nombre_mes[$c_mes]." de ".$c_anio;

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='au_".$datos_albaranes["id_servicio_contratado"]."' AND detalles_factura.concepto like '%$fecha_servicio_buscar%' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);

                                        if ($numRecords==0) {
                                        // Comprueba fechas
                                                $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                                $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                                $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                        if (((($datos_albaranes["TIPO_SERVICIO"]!=1) or ($datos_albaranes["TIPO_SERVICIO"]!=0)) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==1)) and (intval($c_mes)!=intval($mes)))) {
                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                

                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                                $contador=$contador+1;
                                                                $contador_alumnos++;
                                                                }
                                                        }
                                                }
                                        // } // Restricción facturas cosas solo del curso

                                        $c_mes++;
                                        if ($c_mes==13) {
                                                $c_mes=1;
                                                $c_anio++;
                                                }
                                        $c_fecha=$c_mes."/".$c_anio;
                                }
                                } else {


                                if ($servicios_aula2==1) {

                                // Unico
                                $inicio_docencia=strtotime($fecha_alta_cliente);
                                $fin_docencia=strtotime($fecha_baja_cliente);
                                $f_at=strtotime($datos_albaranes["fecha_inicio"]);
                                // Solo en periodo de docencia if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) {

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

                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                

                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                }
                                        }
                                // Solo en periodo de docencia }
                                }
                                }
                        }
                        }
                        }
                        }
                        // Final servicios aula
?>
        <tr>
        <td colspan=8><font size=2>&nbsp;</font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total Educación</b></font></td>
                <td align="right"><font size=2><?=number_format($total_factura, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total IVA</b></font></td>
                <td align="right"><font size=2><?=number_format($total_iva, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total Factura Educación</b></font></td>
                <td align="right"><font size=2><?=number_format($total_factura+$total_iva, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr>
        <td colspan=8><font size=2>&nbsp;</font></td>
        </tr>
<?php 
                        
// Factura OS
                        $total_factura=0;
                        $total_iva=0;
                        
        $consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=$id_cliente AND tarifa.desglosar_como<>3 ORDER BY servicios_contratados.fecha_inicio DESC";
        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

        $numRecords = db_num_rows ($consulta_albaranes_cliente);
        $contador=0;

       if ($numRecords!=0) {

        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){

                        // Determina si el servicio es periodico o unico
                        if ($datos_albaranes["periodicidad"]==1) {
                                if ($servicios_propios1==1) {
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

                                        $fecha_servicio_buscar=$nombre_mes[$c_mes]." de ".$c_anio;

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.articulo=".$datos_albaranes["id_servicio_contratado"]." AND detalles_factura.concepto like '%$fecha_servicio_buscar%' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $estafact = db_num_rows ($consulta_factura);
                                        if ($estafact==0) {

                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                        if (((($datos_albaranes["TIPO_SERVICIO"]!=1) or ($datos_albaranes["TIPO_SERVICIO"]!=0)) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==1)) and (intval($c_mes)!=intval($mes)))) {

                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);


                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                                <?php 
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
                                }
                                } else {

                                if ($servicios_propios2==1) {

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

                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);


                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                }
                                        }
                                }
                                }
                        }
        }

                        // Final servicios propios
                        $consulta1="SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_aula=$id_aula_cliente AND tarifa.desglosar_como<>3 ORDER BY servicios_contratados_aula.fecha_inicio DESC";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows ($consulta_albaranes_cliente);

                        if ($numRecords!=0) {

                        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){
                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_albaranes["id_servicio_contratado"], $id_cliente));

                        if (db_num_rows($busca_si_activado)==0) {

                        if ($servicios_aula1==1) {
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
                                        // if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) { // Restricción facturas cosas solo del curso

                                        $nombre_servicio=$nombre_servicio_1." ".$nombre_mes[$c_mes]." de ".$c_anio;

                                        $fecha_servicio_buscar=$nombre_mes[$c_mes]." de ".$c_anio;

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='au_".$datos_albaranes["id_servicio_contratado"]."' AND detalles_factura.concepto like '%$fecha_servicio_buscar%' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);

                                        if ($numRecords==0) {
                                        // Comprueba fechas
                                                $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                                $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                                $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                        if (((($datos_albaranes["TIPO_SERVICIO"]!=1) or ($datos_albaranes["TIPO_SERVICIO"]!=0)) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==1)) and (intval($c_mes)!=intval($mes)))) {
                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);


                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                                $contador=$contador+1;
                                                                $contador_alumnos++;
                                                                }
                                                        }
                                                }
                                        // } // Restricción facturas cosas solo del curso

                                        $c_mes++;
                                        if ($c_mes==13) {
                                                $c_mes=1;
                                                $c_anio++;
                                                }
                                        $c_fecha=$c_mes."/".$c_anio;
                                }
                                } else {


                                if ($servicios_aula2==1) {

                                // Unico
                                $inicio_docencia=strtotime($fecha_alta_cliente);
                                $fin_docencia=strtotime($fecha_baja_cliente);
                                $f_at=strtotime($datos_albaranes["fecha_inicio"]);
                                // Solo en periodo de docencia if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) {

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

                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);


                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }


                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["cantidad"], 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["PRECIO_VENTA"], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($datos_albaranes["descuento"], 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(($datos_albaranes["porcentaje_iva"]*100), 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                }
                                        }
                                // Solo en periodo de docencia }
                                }
                                }
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
//                        $c_anios=$ano_inicio_curso;
                        $c_anios=$ano_i;

                        $mes_inicio_contador=$mes_i;
                        if ($mes_f<$mes_i) {
                                $mes_fin_contador=12+$mes_f;
                                } else {
                                $mes_fin_contador=$mes_f;
                                }

                        for ($c_meses_curso=$mes_inicio_contador;$c_meses_curso<=$mes_fin_contador;$c_meses_curso++) {
                                if ($c_meses_curso==13) {
                                        $c_anios++;
                                        }
                                if ($c_meses_curso>12) {
                                        $mes_actual=$c_meses_curso-12;
                                        } else {
                                        $mes_actual=$c_meses_curso;
                                        }
                        
                                $mes_actual=intval($mes_actual);

                        if ($anular_horas_extra==false) {
                        if ($mostrar_horas_extra==1) {
	                        
                                // Busca bonos horas extra contratados durante todo el año
                                $busca_bonos1=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=1 AND periodicidad=1 AND (fecha_inicio<=?) AND (fecha_fin>=?)', 'sss', array($id_cliente, (string)(date("Y-m-t", strtotime($c_anios . "-" . $mes_actual . "-1"))), (string)(date("Y-m-d", strtotime($c_anios . "-" . $mes_actual . "-1"))))) or die(db_error());
                                $numero_bonos1=0;
                                while($bonos1=db_fetch_array($busca_bonos1)) {
                                        $numero_bonos1=$numero_bonos1+$bonos1["cantidad"];
                                        }
                                // Busca bonos horas extra contratados para este mes
                                $busca_bonos2=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=1 AND periodicidad=2 AND  (month(fecha_inicio)=?) AND (year(fecha_inicio)=?)', 'sss', array($id_cliente, $mes_actual, $c_anios)) or die(db_error());
                                $numero_bonos2=0;
                                while($bonos2=db_fetch_array($busca_bonos2)) {
                                        $numero_bonos2=$numero_bonos2+$bonos2["cantidad"];
                                        }
	                        	
	                            // Busca horas extra individuales
                                $busca_bonos3=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=2 AND periodicidad=2 AND (month(fecha_inicio)=?) AND (year(fecha_inicio)=?)', 'sss', array($id_cliente, $mes_actual, $c_anios)) or die(db_error());
                                $numero_bonos3=0;
                                while($bonos3=db_fetch_array($busca_bonos3)) {
                                                $numero_bonos3=$numero_bonos3+$bonos3["cantidad"];
                                                }
	                        
								$bonos_consumidos_dia = array();
								$bonos_consumidos_dia2 = array();	
								$ultimoMes = date("t",strtotime($ano_sel."-".$mes_sel."-01"));	
										
								for ($diaMes=1;$diaMes<=$ultimoMes;$diaMes++) {
									$bonos_consumidos_dia[$id_usuario_cliente][(int)$mes_actual."-".$diaMes]=($numero_bonos1+$numero_bonos2);
									$bonos_consumidos_dia1[$id_usuario_cliente][(int)$mes_actual."-".$diaMes]=($numero_bonos1+$numero_bonos2);		
								}	                        
								
	                        
                                // Comprueba las horas extra consumdidas durante el mes
                                $consulta_asistencia=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND month(fecha)=? AND year(fecha)=? AND no_facturar=0;', 'ssss', array($id_usuario_cliente, $id_aula_cliente, $mes_actual, $c_anios));
                                $total_horas_extra=0;
                                $total_horas_extra_fact=0;

                                while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
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
                        if (strtotime("$c_anios-$mes_actual-01")>=strtotime("2012-12-01")) {
                                $diferencia_entrada=ceil((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                                } else {
                                $diferencia_entrada=round((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                                }
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
                        if (strtotime("$c_anios-$mes_actual-01")>=strtotime("2012-12-01")) {
                               $diferencia_salida=ceil((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                               } else {
                               $diferencia_salida=round((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                               }
                        } else {
                        $diferencia_salida=0;
                        }

                $diferencia_entrada1=$diferencia_entrada;
                $$diferencia_salida1=$diferencia_salida;

                // Si se queda a comer no se cobran horas
                         if ($datos_asistencia["turno"]==0) {
                                if ($datos_asistencia["hora_entrada_c"]!="") {
                                        $coletila="<font color=red> * Comedor</font>";
                                        $diferencia_salida=$diferencia_salida-$horas_comida;
                                        if ($diferencia_salida<0) {
                                                $diferencia_salida=0;
                                                }
                                        }
                                } else {
                                // Comprueba si ha entrado a comer en otro turno
                                $busca_otro_turno=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND fecha=? AND turno=0;', 'sss', array($datos_asistencia["id_alumno"], $datos_asistencia["id_servicio"], (string)(date("Y-m-d", strtotime($datos_asistencia["fecha"])))));

                                if (db_num_rows($busca_otro_turno)!=0) {
                                        $datos_otro_turno=db_fetch_array($busca_otro_turno);
                                        if ($datos_otro_turno["hora_entrada_c"]!="") {
                                                // Si el niño ha venido a comer y ha entrado por la mañana, no le cobro horas extra
                                                $diferencia_entrada=0;
                                                /*
                                                $diferencia_entrada=$diferencia_entrada-$horas_comida;
                                                if ($diferencia_entrada<0) {
                                                        $diferencia_entrada=0;
                                                        }
                                                */
                                                }
                                        }
                                }
                                
                if ($diferencia_entrada1<$diferencia_entrada) {
                        $diferencia_entrada=$diferencia_entrada1;
                        }
                if ($$diferencia_salida1<$diferencia_salida) {
                        $diferencia_salida=$$diferencia_salida1;
                        }
                        
                $horas_extra_hoy=($diferencia_entrada+$diferencia_salida);
				
				// Descuenta bonos
                
                if ($datos_asistencia['fecha']<'2019-04-01') {
	                
	            	// Sistema antiguo  
	                if (($numero_bonos1-$numero_bonos2)>0) {
		                $horas_extra_hoy_fact=$horas_extra_hoy-$numero_bonos1-$numero_bonos2;
	                } else {
		             	$horas_extra_hoy_fact=$horas_extra_hoy;    
	                }
	                
                } else {
                	
                	// Nuevo sistema
	                $bonoshoy = $bonos_consumidos_dia[$id_usuario_cliente][date("n-j",strtotime($datos_asistencia['fecha']))];
	                
	                if ($bonoshoy>0) {
		                	                
		                $bonos_restantes_hoy = $bonoshoy-$horas_extra_hoy;
		                		                
		                if ($bonos_restantes_hoy<0) {
			                $bonos_restantes_hoy = 0;
		                }                
		                	                	                
						$bonos_consumidos_dia[$id_usuario_cliente][date("n-j",strtotime($datos_asistencia['fecha']))]=$bonos_restantes_hoy;	                                
		                
		                $horas_extra_hoy_fact=$horas_extra_hoy-$bonoshoy;	                
		                
	                } else {
		            	
		            	// No hay bonos
		            	
		            	$horas_extra_hoy_fact=$horas_extra_hoy;  
		                
	                }
                
                } 

                $total_horas_extra=$total_horas_extra+$horas_extra_hoy;
                
                                if ($horas_extra_hoy_fact>0) {
                                                $total_horas_extra_fact=$total_horas_extra_fact+$horas_extra_hoy_fact;
                                                }
                                }

                                $total_horas_extra_fact=$total_horas_extra_fact-$numero_bonos3;
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
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='he_".$mes_actual."_".$c_anios."' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);

                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($mes_actual."/1/".$c_anios);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {

                                        $nombre_servicio="Horas extra sueltas ".$nombre_mes[$mes_actual];

                                        if ($numRecords==0) {
                                $total_linea=$total_horas_extra_fact*$precio_hora_extra;
                                $total_dto=0;
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=0;
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_horas_extra_fact, 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($precio_hora_extra, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                } else {

                                                $total_cantidad=0;
                                                while($datos_de_factura=db_fetch_array($consulta_factura)) {
                                                        $total_cantidad=$total_cantidad+$datos_de_factura["cantidad"];
                                                        }
                                                // Comprueba si hay horas extra realizadas despues de la emisión de la factura
                                                if (($total_cantidad!=$total_horas_extra_fact) and (($total_horas_extra_fact-$total_cantidad)>0)) {
                                                        $faltan=$total_horas_extra_fact-$total_cantidad;
                                $total_linea=$faltan*$precio_hora_extra;
                                $total_dto=0;
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=0;
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($faltan, 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($precio_hora_extra, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                        $contador=$contador+1;
                                                        $contador_alumnos++;
                                                        }
                                                }
                                                }
                                        }
                                }
                        }
                        }
                        // Final horas extra consumidas

                        // Comprueba si tiene contratado el servico de comedor
                        //$mostrar_comedor=0;
                        if ($anular_comedor==false) {
                        if ($mostrar_comedor==1) {
                        /*
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
                                        $consulta_factura = mysql_query($consulta2) or die (mysql_error());

                                        $numRecords = mysql_num_rows ($consulta_factura);

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
                                $total_linea=$cantidad_menuses*$precio_mes[$tipo_menu_cliente];
                                $total_dto=0;
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=0;
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($cantidad_menuses, 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($precio_mes[$tipo_menu_cliente], 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?
                                                        $contador=$contador+1;
                                                        $contador_alumnos++;
                                                        }
                                                }
                                                }
                                        }
                                } else {
                                */
                                if ($mostrar_comedor_extra==1) {
                                $consulta_asistencia_comedor=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND (tipo_menu=1 OR tipo_menu=3);', 'ss', array($id_usuario_cliente, $id_aula_cliente));
                                while($asistencia_comedor=db_fetch_array($consulta_asistencia_comedor)) {
                                        // Comprueba si tiene contratado comedor este día
                                        $busca_inscripcion_comedor=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=3 AND servicios_contratados.fecha_inicio<=? AND servicios_contratados.fecha_fin>=?', 'sss', array($id_cliente, (string)($asistencia_comedor["fecha"]), (string)($asistencia_comedor["fecha"]))) or die(db_error());
                                        if (db_num_rows($busca_inscripcion_comedor)==0) {
                                        
                                        // Comprueba la asistencia al comedor
                                        // if ($asistencia_comedor["hora_entrada_c"]!="") {
                                        if (($asistencia_comedor["hora_entrada_c"]!="") and ($asistencia_comedor["fecha"]!="2007-12-14")) {

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
                                $total_linea=$cantidad_menuses*$precio_menu[$asistencia_comedor["tipo_menu_comedor"]];
                                $total_dto=0;
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=0;
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($cantidad_menuses, 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                        $contador=$contador+1;
                                                        $contador_alumnos++;
                                                        }
                                                }
                                        }
                                   }
                                  }
                                }
                                // }
                        }
                        }
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
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='cod_e_".$servicios_extra["id_servicio_consumido"]."' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);

                                // Comprueba fechas
                                $fecha_albaran=strtotime($servicios_extra["fecha"]);
                                $fecha_ini=strtotime($mes_i."/".$dia_i."/".$ano_i);
                                $fecha_fin=strtotime($mes_f."/".$dia_f."/".$ano_f);
                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {

                                        if (($numRecords!=0) or ($servicios_extra["pagado"]==1)) {
                                                // Pagado o facturado
                                                } else {
                                                $total_linea=$servicios_extra["PRECIO_VENTA"];
                                                $total_dto=0;
                                                $base_linea=$total_linea-$total_dto;
                                                $iva_linea=0;
                                                $total_abs=$base_linea+$iva_linea;
                                                $total_factura=$total_factura+round($base_linea,2);
                                                $total_iva=$total_iva+round($iva_linea,2);
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                <td><font size=2>Asistencia <?=$servicios_extra["ARTICULO"]?> día: <?=date("d/m/Y",strtotime($servicios_extra["fecha"]))?></font></td>
                                                <td align="right"><font size=2><?=number_format($cantidad_menuses, 2, ',', '.');?></font></td>
                                                <td align="right"><font size=2><?=number_format($precio_menu[$asistencia_comedor["tipo_menu_comedor"]], 2, ',', '.')." €";?></font></td>
                                                <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                                <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                                <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                                <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                                <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                                </tr>
                                                <?php 
                                                $contador=$contador+1;
                                                }
                                                }
                                        }
                                        }
                                    }
                        }
                        // Fin servicios extra
                        
        if ($contador!=0) {
                          $array_facturas=$array_facturas.",".$id_nueva_factura;
                          $num_factura++;
                          $contador_facturas++;
                          }
        ?>
        <tr>
        <td colspan=8><font size=2>&nbsp;</font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total Otros servicios:</b></font></td>
                <td align="right"><font size=2><?=number_format($total_factura, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total IVA:</b></font></td>
                <td align="right"><font size=2><?=number_format($total_iva, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total Factura Otros servicios:</b></font></td>
                <td align="right"><font size=2><?=number_format($total_factura+$total_iva, 2, ',', '.')." €";?></font></td>
        </tr>
        </table><br>
        <?php 
        $total_absoluto_facturado=$total_absoluto_facturado+$total_factura;
        $total_absoluto_iva_facturado=$total_absoluto_iva_facturado+$total_iva;
}

}
if ($nivel_acceso==4) {
?>
<br><font size=4>Totales</font><br><br>
<table border=1 cellpadding=2 cellspacing=0 bordercolor="#000000" style="border-collapse: collapse" width="100%">
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total Base Imponible que se facturará:</b></font></td>
                <td align="right"><font size=2><?=number_format($total_absoluto_facturado, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total IVA:</b></font></td>
                <td align="right"><font size=2><?=number_format($total_absoluto_iva_facturado, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Importe total que se facturrá en el periodo:</b></font></td>
                <td align="right"><font size=2><?=number_format($total_absoluto_facturado+$total_absoluto_iva_facturado, 2, ',', '.')." €";?></font></td>
        </tr>
</table><br>
<?php 
}
if ($num_errores==0) {
?>
<p align="center"><br><br>
<a href="facturar_servicios.php">Previsualizar otros alumnos</a> | <a href="/facturacion/gestion_facturacion.php">Salir</a></font><br><br>
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
