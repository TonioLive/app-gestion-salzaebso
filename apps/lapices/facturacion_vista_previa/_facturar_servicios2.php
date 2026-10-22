<?php  require("../configuracion/parte_superior_privado_imprimir.php");
$num_errores=0;

$id_cliente=$_POST["id_cliente"];
$tipo_factura=$_POST["tipo_factura"];
$dia=$_POST["dia"];
$mes=$_POST["mes"];
$ano=$_POST["ano"];

$servicios_propios1=$_POST["servicios_propios1"];
$servicios_propios2=$_POST["servicios_propios2"];
$servicios_aula1=$_POST["servicios_aula1"];
$servicios_aula2=$_POST["servicios_aula2"];
$mostrar_horas_extra=$_POST["horas_extra"];
$mostrar_comedor=$_POST["comedor"];
$mostrar_comedor_extra=$_POST["comedor_extra"];

if ($id_cliente==0) {
                $nombre_cliente="Todos los alumnos";
        } else {
                // Busca datos del cliente
                $consulta_datos_cliente = db_query("SELECT alumnos.*, usuarios.*,alumnos.id_centro AS centro_inscripcion FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_alumno=".$id_cliente) or die (db_error());

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
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_del_cliente["id_curso"]);

                $datos_curso_actual=db_fetch_array($busca_curso_actual);

                $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso=$datos_curso_actual["fecha_fin"];

                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_del_cliente["centro_inscripcion"]);
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
                        
                        $hora_entrada_centro_man=$datos_centro["hora_entrada1"];
                        $hora_salida_centro_man=$datos_centro["hora_salida1"];
                        $hora_entrada_centro_tar=$datos_centro["hora_entrada2"];
                        $hora_salida_centro_tar=$datos_centro["hora_salida2"];
                        $division_horas=($datos_centro["division_horas"]/60);
                        $periodo_gracia=($datos_centro["periodo_gracia"]*60);
                        }
        }
?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Vista previa Facturación de servicios del alumno: <?=$nombre_cliente?></b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                                 <tr>
                                 <td align="center">
<?php 
switch($tipo_factura)
        {
        case '1':
?>
                                 <table border=1 cellpadding=2 cellspacing=0 bordercolor="#000000" style="border-collapse: collapse" width="100%">
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

$total_factura=0;
$total_iva=0;
        // Inserta la factura
        
        $consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=$id_cliente ORDER BY servicios_contratados.fecha_inicio DESC";
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

                                        $albaran_actual=$_POST["albaran_".$datos_albaranes["id_servicio_contratado"]."_".$c_mes."_".$c_anio];

                                        if ($albaran_actual=="1") {
                                        
                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
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
                                
                                $albaran_actual=$_POST["albaran_".$datos_albaranes["id_servicio_contratado"]];

                                if ($albaran_actual=="1") {
                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
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
                        if ($servicios_aula1==1) {
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

                                        $albaran_actual=$_POST["albaran_aula_".$datos_albaranes["id_servicio_contratado"]."_".$c_mes."_".$c_anio];

                                        if ($albaran_actual=="1") {
                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
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
                                if ($servicios_aula2==1) {
                                // Unico
                                $inicio_docencia=strtotime($fecha_alta_cliente);
                                $fin_docencia=strtotime($fecha_baja_cliente);
                                $f_at=strtotime($datos_albaranes["fecha_inicio"]);
                                // Solo en periodo de docencia if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) {
                                
                                $nombre_servicio=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) { $nombre_servicio=$nombre_servicio.". ".$datos_albaranes["notas"]; }

                                $albaran_actual=$_POST["albaran_aula_".$datos_albaranes["id_servicio_contratado"]];

                                if ($albaran_actual=="1") {
                                $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["PRECIO_VENTA"];
                                $total_dto=$total_linea*round(($datos_albaranes["descuento"]/100),2);
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
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
                                                }
                                // Solo en periodo de docencia }
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
        if ($mostrar_horas_extra==1) {
                                $nombre_servicio="Horas extra sueltas ".$nombre_mes[$mes_actual];

                                $albaran_actual=$_POST["albaran_he_".$mes_actual."_".$c_anios];

                                if (strlen($albaran_actual)!=0) {
                                $total_linea=$albaran_actual*$precio_hora_extra;
                                $total_dto=0;
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=0;
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($albaran_actual, 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($precio_hora_extra, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($base_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format(0, 0, ',', '.')." %";?></font></td>
                                 <td align="right"><font size=2><?=number_format($iva_linea, 2, ',', '.')." €";?></font></td>
                                 <td align="right"><font size=2><?=number_format($total_abs, 2, ',', '.')." €";?></font></td>
                                 </tr>
                                 <?php 
                                                $contador=$contador+1;
                                                }
                                                
                                $albaran_actual=$_POST["albaran_he_f_".$mes_actual."_".$c_anios];

                                if (strlen($albaran_actual)!=0) {
                                $total_linea=$albaran_actual*$precio_hora_extra;
                                $total_dto=0;
                                $base_linea=$total_linea-$total_dto;
                                $iva_linea=0;
                                $total_abs=$base_linea+$iva_linea;
                                $total_factura=$total_factura+round($base_linea,2);
                                $total_iva=$total_iva+round($iva_linea,2);
                                                ?>
                                 <tr bgcolor="#ffffff">
                                 <td><font size=2><?=$nombre_servicio?></font></td>
                                 <td align="right"><font size=2><?=number_format($albaran_actual, 2, ',', '.');?></font></td>
                                 <td align="right"><font size=2><?=number_format($precio_hora_extra, 2, ',', '.')." €";?></font></td>
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
        // Final horas extra consumidas
        if ($mostrar_comedor==1) {
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
                                 <?php 
                                                $contador=$contador+1;
                                                }
                                        }
                                } else {
                                if ($mostrar_comedor_extra==1) {
                                $consulta_asistencia_comedor=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_usuario_cliente AND id_servicio=$id_aula_cliente AND (tipo_menu=1 OR tipo_menu=3);");
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
        ?>
        <tr>
        <td colspan=8><font size=2>&nbsp;</font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total Base Imponible</b></font></td>
                <td align="right"><font size=2><?=number_format($total_factura, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total IVA</b></font></td>
                <td align="right"><font size=2><?=number_format($total_iva, 2, ',', '.')." €";?></font></td>
        </tr>
        <tr bgcolor="#ffffff">
                <td align="right" colspan=7><font size=2><b>Total Factura</b></font></td>
                <td align="right"><font size=2><?=number_format($total_factura+$total_iva, 2, ',', '.')." €";?></font></td>
        </tr>
        <?php 
        // Fin menu consumidos
        
        if ($contador==0) {
                          $num_errores=$num_errores+1;
                          $errores[$num_errores]="No se ha seleccionado ningún servicio.";
                          }
        break;
        case '2':
        $dia_i=$_POST["dia_i"];
        $mes_i=$_POST["mes_i"];
        $ano_i=$_POST["ano_i"];
        $dia_f=$_POST["dia_f"];
        $mes_f=$_POST["mes_f"];
        $ano_f=$_POST["ano_f"];
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
        $fecha_i=$ano_i."-".$mes_i."-".$dia_i;
        $fecha_f=$ano_f."-".$mes_f."-".$dia_f;
                        ?>
                        <script language="Javascript">
                                function comprobar_y_enviar() {
                                        top.window.document.albaranes.submit();
                                        }
                        </script>
                        <input type="button" value="Cancelar" onclick="window.location.href='facturar_servicios.php';" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();"><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <form name="albaranes" action="facturar_servicios3.php" method="POST">
                        <input type="hidden" name="id_cliente" value="<?=$id_cliente;?>">
                        <input type="hidden" name="tipo_factura" value="<?=$tipo_factura;?>">
                        <input type="hidden" name="dia" value="<?=$dia;?>">
                        <input type="hidden" name="mes" value="<?=$mes;?>">
                        <input type="hidden" name="ano" value="<?=$ano;?>">
                        <input type="hidden" name="servicios_propios1" value="<?=$servicios_propios1;?>">
                        <input type="hidden" name="servicios_propios2" value="<?=$servicios_propios2;?>">
                        <input type="hidden" name="servicios_aula1" value="<?=$servicios_aula1;?>">
                        <input type="hidden" name="servicios_aula2" value="<?=$servicios_aula2;?>">
                        <input type="hidden" name="horas_extra" value="<?=$mostrar_horas_extra;?>">
                        <input type="hidden" name="comedor" value="<?=$mostrar_comedor;?>">
                        <input type="hidden" name="comedor_extra" value="<?=$mostrar_comedor_extra;?>">
                         <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=4><font size=2 face="<?= $fuente_general; ?>"><b>Servicios consumidos entre el día <?=$dia_i."/".$mes_i."/".$ano_i;?> y el día <?=$dia_f."/".$mes_f."/".$ano_f;?></b><br><small>Se muestran todos los servicios consumidos. Los incluidos dentro del periodo (fondo verde) se incluiran en la nueva factura (si no se habian facturado con anterioridad), pero puedes seleccionar albaranes que se encuentren fuera del periodo marcado si así lo deseas.</small></font></td>
                        </tr>
                        <?php 
                        $consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=$id_cliente ORDER BY servicios_contratados.fecha_inicio DESC";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows ($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF" colspan=4>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">El cliente no ha consumido ningún servicio...</font></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Servicio</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Fecha</font></td>
                        </tr>
                        <?php 
                        }
                        $contador=0;
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

                                        $numRecords = db_num_rows ($consulta_factura);
                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);

                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {

                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {

                                                        if ((($datos_albaranes["TIPO_SERVICIO"]!=1) and ($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==1)) and (intval($c_mes)!=intval($mes)))) {
                                                                $contador=$contador+1;
                                                                $seleccionado=" checked";
                                                                $color_fondo="#B9E9BE";
                                                                } else {
                                                                $seleccionado="";
                                                                $color_fondo="#FFFFFF";
                                                                }
                                                } else {

                                                        $seleccionado="";
                                                        $color_fondo="#FFFFFF";
                                                }
                                        
                                        if ($numRecords==0) {
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" name="albaran_<?=$datos_albaranes["id_servicio_contratado"]."_".$c_mes."_".$c_anio;?>" value="1"<?=$seleccionado;?>></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_servicio;?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?></font></td>
                                                </tr>
                                                <?php 
                                                $contador=$contador+1;
                                                } else {
                                                $datos_de_factura=db_fetch_array($consulta_factura);

                                                $texto_adicional="<br>Factura nº: ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"].". Fecha: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]));
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" disabled></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_servicio;?></font></td>
                                                        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?><?=$texto_adicional;?></font></td>
                                                </tr>
                                                <?php 
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
                                // Mira si el servicio está facturado y mira el nº de factura
                                $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.articulo=".$datos_albaranes["id_servicio_contratado"]." ORDER BY facturas_emitidas.fecha_emision DESC";
                                $consulta_factura = db_query($consulta2) or die (db_error());

                                $numRecords = db_num_rows ($consulta_factura);
                                
                                // Comprueba fechas
                                $fecha_albaran=strtotime($datos_albaranes["fecha_inicio"]);
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
                                        
                                if ($numRecords==0) {
                                        ?>
                                        <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                <td align="center"><input type="checkbox" name="albaran_<?=$datos_albaranes["id_servicio_contratado"];?>" value="1"<?=$seleccionado;?>></td>
                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_albaranes["ARTICULO"];?><?php if (strlen($datos_albaranes["notas"])!=0) { echo " - ".$datos_albaranes["notas"]; } ?></font></td>
                                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?></font></td>
                                        </tr>
                                        <?php 
                                        $contador=$contador+1;
                                        } else {
                                        $datos_de_factura=db_fetch_array($consulta_factura);

                                        $texto_adicional="<br>Factura nº: ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"].". Fecha: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]));
                                        ?>
                                        <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                <td align="center"><input type="checkbox" disabled></td>
                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_albaranes["ARTICULO"];?><?php if (strlen($datos_albaranes["notas"])!=0) { echo " - ".$datos_albaranes["notas"]; } ?></font></td>
                                                <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?><?=$texto_adicional;?></font></td>
                                        </tr>
                                        <?php 
                                        }
                                }
                                }
                        }
                        // Final servicios propios

                        $consulta1="SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_aula=$id_aula_cliente ORDER BY servicios_contratados_aula.fecha_inicio DESC";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows ($consulta_albaranes_cliente);

                        if ($numRecords!=0) {

                        if  ($contador==0) {
                        ?>
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Servicio</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Fecha</font></td>
                        </tr>
                        <?php 
                        }

                        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_albaranes["id_servicio_contratado"]." AND id_usuario=$id_cliente");

                        if (db_num_rows($busca_si_activado)==0) {
                        // Determina si el servicio es periodico o unico
                        if ($datos_albaranes["periodicidad"]==1) {
                                if ($servicios_aula1==1) {
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

                                        $fecha_servicio_buscar=$nombre_mes[$c_mes]." de ".$c_anio;

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='au_".$datos_albaranes["id_servicio_contratado"]."' AND detalles_factura.concepto like '%$fecha_servicio_buscar%' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);

                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);

                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                        if ((($datos_albaranes["TIPO_SERVICIO"]!=1) and ($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==1)) and (intval($c_mes)!=intval($mes)))) {
                                                                $contador=$contador+1;
                                                                $seleccionado=" checked";
                                                                $color_fondo="#B9E9BE";
                                                                } else {
                                                                $seleccionado="";
                                                                $color_fondo="#FFFFFF";
                                                                }
                                                } else {
                                                        $seleccionado="";
                                                        $color_fondo="#FFFFFF";
                                                }

                                        if ($numRecords==0) {
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo?>">
                                                        <td align="center"><input type="checkbox" name="albaran_aula_<?=$datos_albaranes["id_servicio_contratado"]."_".$c_mes."_".$c_anio;?>" value="1"<?=$seleccionado;?>></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_servicio;?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?></font></td>
                                                </tr>
                                                <?php 
                                                $contador=$contador+1;
                                                } else {
                                                $datos_de_factura=db_fetch_array($consulta_factura);

                                                $texto_adicional="<br>Factura nº: ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"].". Fecha: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]));
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo?>">
                                                        <td align="center"><input type="checkbox" disabled></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_servicio;?></font></td>
                                                        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?><?=$texto_adicional;?></font></td>
                                                </tr>
                                                <?php 
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
                                // Unico
                                if ($servicios_aula2==1) {
                                $inicio_docencia=strtotime($fecha_alta_cliente);
                                $fin_docencia=strtotime($fecha_baja_cliente);
                                $f_at=strtotime($datos_albaranes["fecha_inicio"]);
                                // Solo en periodo de docencia if (($f_at>=$inicio_docencia) and ($f_at<=$fin_docencia)) {
                                // Mira si el servicio está facturado y mira el nº de factura
                                $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='au_".$datos_albaranes["id_servicio_contratado"]."' ORDER BY facturas_emitidas.fecha_emision DESC";
                                $consulta_factura = db_query($consulta2) or die (db_error());

                                $numRecords = db_num_rows ($consulta_factura);
                                
                                // Comprueba fechas
                                $fecha_albaran=strtotime($datos_albaranes["fecha_inicio"]);
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
                                        
                                if ($numRecords==0) {
                                        ?>
                                        <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                <td align="center"><input type="checkbox" name="albaran_aula_<?=$datos_albaranes["id_servicio_contratado"];?>" value="1"<?=$seleccionado;?>></td>
                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_albaranes["ARTICULO"];?><?php if (strlen($datos_albaranes["notas"])!=0) { echo " - ".$datos_albaranes["notas"]; } ?></font></td>
                                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?></font></td>
                                        </tr>
                                        <?php 
                                        $contador=$contador+1;
                                        } else {
                                        $datos_de_factura=db_fetch_array($consulta_factura);

                                        $texto_adicional="<br>Factura nº: ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"].". Fecha: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]));
                                        ?>
                                        <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                <td align="center"><input type="checkbox" disabled></td>
                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_albaranes["ARTICULO"];?><?php if (strlen($datos_albaranes["notas"])!=0) { echo " - ".$datos_albaranes["notas"]; } ?></font></td>
                                                <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_albaranes["fecha_inicio"]));?><?=$texto_adicional;?></font></td>
                                        </tr>
                                        <?php 
                                        }
                                // Solo en periodo de docencia }
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
                           if ($mostrar_horas_extra==1) {
                                // Comprueba las horas extra consumdidas durante el mes
                                $consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_usuario_cliente AND id_servicio=$id_aula_cliente AND month(fecha)=".$mes_actual." AND year(fecha)=".$c_anios." AND no_facturar=0;");
                                $total_horas_extra=0;
                                $total_horas_extra_fact=0;
                                
                                $horas_comida=round(((strtotime($hora_entrada_centro_tar)-strtotime($hora_salida_centro_man))/3600),2);
                                
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
                                $busca_otro_turno=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$datos_asistencia["id_alumno"]." AND id_servicio=".$datos_asistencia["id_servicio"]." AND fecha='".date("Y-m-d",strtotime($datos_asistencia["fecha"]))."' AND turno=0;");

                                if (db_num_rows($busca_otro_turno)!=0) {
                                        $datos_otro_turno=db_fetch_array($busca_otro_turno);
                                        if ($datos_otro_turno["hora_entrada_c"]!="") {
                                                $diferencia_entrada=$diferencia_entrada-$horas_comida;
                                                if ($diferencia_entrada<0) {
                                                        $diferencia_entrada=0;
                                                        }
                                                }
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
                                // Busca horas extra individuales
                                $busca_bonos3=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_cliente AND tarifa.TIPO_SERVICIO=2 AND periodicidad=2 AND (month(fecha_inicio)=".$mes_actual.") AND (year(fecha_inicio)=".$c_anios.")") or die(db_error());
                                $numero_bonos3=0;
                                while($bonos3=db_fetch_array($busca_bonos3)) {
                                                $numero_bonos3=$numero_bonos3+$bonos3["cantidad"];
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
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND (detalles_factura.cod_facturacion='he_".(int)$mes_actual."_".$c_anios."' OR detalles_factura.cod_facturacion='he_".$mes_actual."_".$c_anios."') ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);
                                        
                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($mes_actual."/1/".$c_anios);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        $contador=$contador+1;
                                                        $seleccionado=" checked";
                                                        $color_fondo="#B9E9BE";
                                                } else {
                                                        $seleccionado="";
                                                        $color_fondo="#FFFFFF";
                                                }
                                                
                                        if ($numRecords==0) {
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" name="albaran_he_<?=$mes_actual?>_<?=$c_anios;?>" value="<?=$total_horas_extra_fact;?>"<?=$seleccionado;?>></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>">Horas extra sueltas <?=$nombre_mes[$mes_actual];?>, Cantidad: <?=$horas_extra_mostrar_fact;?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_mes[$mes_actual];?> de <?=$c_anios;?></font></td>
                                                </tr>
                                                <?php 
                                                $contador=$contador+1;
                                                } else {

                                                $total_cantidad=0;
                                                while($datos_de_factura=db_fetch_array($consulta_factura)) {
                                                $texto_adicional="<br>Factura nº: ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"].". Fecha: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]));
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" disabled></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>">Horas extra sueltas <?=$nombre_mes[$mes_actual];?>, Cantidad: <?=date("H:i",($datos_de_factura["cantidad"]-1)*60*60);?></font></td>
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_mes[$mes_actual];?> de <?=$c_anios;?><?=$texto_adicional?></font></td>
                                                </tr>
                                                <?php 
                                                $total_cantidad=$total_cantidad+$datos_de_factura["cantidad"];
                                                }
                                                // Comprueba si hay horas extra realizadas despues de la emisión de la factura
                                                if ($total_cantidad!=$total_horas_extra_fact) {
                                                        $faltan=$total_horas_extra_fact-$total_cantidad;
                                                        ?>
                                                        <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                                <td align="center"><input type="checkbox" name="albaran_he_<?=$mes_actual?>_<?=$c_anios;?>" value="<?=$faltan;?>"<?=$seleccionado;?>></td>
                                                                <td align="left"><font size=2 face="<?= $fuente_general; ?>">Horas extra sueltas <?=$nombre_mes[$mes_actual];?>, Cantidad: <?=$faltan;?></font></td>
                                                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_mes[$mes_actual];?> de <?=$c_anios;?></font></td>
                                                        </tr>
                                                        <?php 
                                                        }
                                                }
                                        }
                                }

                        // Final horas extra consumidas
                        }
                        
                        if ($mostrar_comedor==1) {
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
                                        
                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($mes_actual."/1/".$c_anios);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // Solo si pertenece al més próximo
                                                        if (intval($mes)!=intval($mes_actual)) {
                                                                $contador=$contador+1;
                                                                $seleccionado=" checked";
                                                                $color_fondo="#B9E9BE";
                                                                } else {
                                                                $seleccionado="";
                                                                $color_fondo="#FFFFFF";
                                                                }
                                                } else {
                                                        $seleccionado="";
                                                        $color_fondo="#FFFFFF";
                                                }
                                                
                                        if ($numRecords==0) {
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" name="comedor_<?=$mes_actual?>_<?=$c_anios;?>" value="1"<?=$seleccionado;?>></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>">Comedor <?=$nombre_mes[$mes_actual];?>, Tipo de menú <?=$nombre_tipo_menu_cliente[$tipo_menu_cliente];?><?php if ($comedor_cliente==3) { ?> (régimen)<?php  } ?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_mes[$mes_actual];?> de <?=$c_anios;?></font></td>
                                                </tr>
                                                <?php 
                                                $contador=$contador+1;
                                                } else {
                                                $datos_de_factura=db_fetch_array($consulta_factura);
                                                $texto_adicional="<br>Factura nº: ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"].". Fecha: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]));
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" disabled></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>">Comedor <?=$nombre_mes[$mes_actual];?>, Tipo de menú <?=$nombre_tipo_menu_cliente[$tipo_menu_cliente];?><?php if ($comedor_cliente==3) { ?> (régimen)<?php  } ?></font></td>
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_mes[$mes_actual];?> de <?=$c_anios;?><?=$texto_adicional?></font></td>
                                                </tr>
                                                <?php 
                                                }
                                        }
                                } else {
                                if ($mostrar_comedor_extra==1) {
                                $consulta_asistencia_comedor=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$id_usuario_cliente AND id_servicio=$id_aula_cliente AND (tipo_menu=1 OR tipo_menu=3);");
                                while($asistencia_comedor=db_fetch_array($consulta_asistencia_comedor)) {
                                
                                        // Comprueba la asistencia al comedor
                                        if ($asistencia_comedor["hora_entrada_c"]!="") {
                                        
                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.cod_facturacion='cod_".$asistencia_comedor["id_servicio_consumido"]."' ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $numRecords = db_num_rows ($consulta_factura);
                                        
                                // Comprueba fechas
                                $fecha_albaran=strtotime($asistencia_comedor["fecha"]);
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
                                        
                                        if ($numRecords==0) {
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" name="comedor_dia_<?=$asistencia_comedor["id_servicio_consumido"];?>" value="1"<?=$seleccionado;?>></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>">Comedor suelto día: <?=date("d/m/Y",strtotime($asistencia_comedor["fecha"]))?>, Tipo de menú <?=$nombre_tipo_menu_cliente[$asistencia_comedor["tipo_menu_comedor"]];?><?php if ($asistencia_comedor["tipo_menu"]==3) { ?> (régimen)<?php  } ?></font></td>
                                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($asistencia_comedor["fecha"]));?></font></td>
                                                </tr>
                                                <?php 
                                                $contador=$contador+1;
                                                } else {
                                                $datos_de_factura=db_fetch_array($consulta_factura);
                                                $texto_adicional="<br>Factura nº: ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"].". Fecha: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]));
                                                ?>
                                                <tr valign="top" bgcolor="<?=$color_fondo;?>">
                                                        <td align="center"><input type="checkbox" disabled></td>
                                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>">Comedor suelto día: <?=date("d/m/Y",strtotime($asistencia_comedor["fecha"]))?>, Tipo de menú <?=$nombre_tipo_menu_cliente[$asistencia_comedor["tipo_menu_comedor"]];?><?php if ($asistencia_comedor["tipo_menu"]==3) { ?> (régimen)<?php  } ?></font></td>
                                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($asistencia_comedor["fecha"]));?><?=$texto_adicional?></font></td>
                                                </tr>
                                                <?php 
                                                }
                                        }
                                    }
                                    }
                                }
                        }
                        // Fin menu consumidos
                        ?>
                        </form>
                        </table>
                        <?php 

                }
        if ($contador==0) {
                          $num_errores=$num_errores+1;
                          $errores[$num_errores]="No se han encontrado albaranes pendientes de facturar en el periodo indicado. Si quieres continuar puedes crear la factura con albaranes emitidos fuera del mismo.";
                          }
        break;
        }

if ($num_errores==0) {
if ($tipo_factura=="1") {
?>
<tr>
<td colspan=8>
<p align="center"><br><br>
<a href="facturar_servicios.php">Previsualizar otro alumno</a> | <a href="/facturacion/gestion_facturacion.php">Salir</a></font><br><br>
</p>
</td>
</tr>
<?php 
}
} else {
?><tr>
<td colspan=8>
<br><br>
<font size=2>
<p align="center"><b><i>
Se han detectado errores.<br><br>
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
</td>
</tr>
<?php  require("../configuracion/parte_inferior.php"); ?>
