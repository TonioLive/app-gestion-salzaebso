<?php  require("../../configuracion/parte_superior_privado_bootstrap.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];
$tipo_listado=$_GET["tl"];
if (strlen($tipo_listado)==0) {
        $tipo_listado=0;
}


// Busca datos del aula
$consulta_datos_aula=db_query("SELECT centro_aulas.*, cursos_academicos.*, centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);
$nombre_aula=$datos_aula["nombre_aula"];
$ciclo_aula=$datos_aula["ciclo"];

$curso_del_aula=$datos_aula["id_curso"];

$fecha_limite_presentacion=$datos_aula["fecha_limite_presentacion"];


$fecha_de_inicio_curso=$datos_aula["fecha_inicio"];

if (intval(strtotime($fecha_de_inicio_curso))>intval(time())) {
        $curso_iniciado=false;
        } else {
        $curso_iniciado=true;
        }

?>
<SCRIPT LANGUAGE=javascript>
<!--

function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,scrollbars=yes,width="+screen.width+",height="+screen.height+"');
        }


//-->
</SCRIPT>
<div class="text-center">
<h3>Listado regularización subvenciones del Aula <?=$nombre_aula;?> <i>Curso <?=$datos_aula["nombre"];?></i><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></h3>
<?php 
$orden_en_aula=1;
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND alumnos.aula=$id_aula ORDER BY Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_alumnos)!=0) {

$busca_orden_en_aula=db_query("SELECT * FROM alumnos WHERE alumnos.aula=$id_aula AND orden_en_aula<>0");
$con_num_orden=db_num_rows($busca_orden_en_aula);
$busca_en_aula=db_query("SELECT * FROM alumnos WHERE alumnos.aula=$id_aula");
$total_alumnos_aula=db_num_rows($busca_en_aula);

$total_a_devolver=0;
$total_a_devolver_cheque=0;
$total_a_devolver_total=0;
?>
<table class="table table-striped table-bordered table-hover">
        <tr>
                <td colspan=3><small>&nbsp;</small></td>
        <td colspan=3 class="text-center"><small><b>Bono infantil</b></small></td>
        <td colspan=5><small>&nbsp;</small></td>
        </tr>
        <tr>
                <td><small><b>Nº</b></small></td>
        <td><small><b>Nombre</b></small></td>
        <td><small><b>Situación actual</b></small></td>
        <td><small><b>Concedido</b></small></td>
        <td><small><b>Adelantado</b></small></td>
        <td><small><b>Saldo Bono</b></small></td>
        <?php
        if ($tipo_listado==0) {
        ?>
        <td><small><b>Cheque</b></small></td>
        <td><small><b>Saldo total</b></small></td>        
        <td><small><b>Resumen</b></small></td>
        <td><small><b>Acciones</b></small></td>
        <?php
        }
        ?>
        <td><small>&nbsp;</small></td>
        </tr>
        <?php 
        
        $suma_numeros_orden=0;
        $contador_nuevos=0;
        $total_piscina=0;
        $total_comedor=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

        // Comprueba si es antiguo alumno
        $busca_inscripcion_ant=db_query("SELECT * FROM alumnos WHERE id_usuario=".$listado_alumnos["id_usuario"]." AND id_curso<$curso_del_aula") or die(db_error());

        if (db_num_rows($busca_inscripcion_ant)!=0) {
                $obs="<font color='blue'>Antiguo</font>";
                } else {
                $obs="<font color='red'>Nuevo</font>";
                $contador_nuevos++;
                }
        

        // Comprueba si se le han incluido los servicios
        $id_inscripcion=$listado_alumnos["id_alumno"];
        $busca_inscripcion=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=1") or die(db_error());

         $busca_inscripcion_facturada=db_query("SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.concepto='Inscripción de curso 2012-2013'");

        if ((db_num_rows($busca_inscripcion)==0) and (db_num_rows($busca_inscripcion_facturada)==0)) {
                $no_inscripcion=true;
                } else {
                $no_inscripcion=false;
                }
        if ($datos_aula["comprobar_inscripcion"]==0) {
                $no_inscripcion=false;
                }
        $busca_cuotas=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=2") or die(db_error());
        if (db_num_rows($busca_cuotas)==0) {
                $no_cuotas=true;
                } else {
                $no_cuotas=false;
                }
         if ($datos_aula["comprobar_cuotas"]==0) {
                $no_cuotas=false;
                }
        if ($no_inscripcion==true) {
                $busca_inscripcion=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.subcat=1") or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_inscripcion=false;
                                }
                        }
                }
                
        if ($no_cuotas==true) {
                $busca_inscripcion=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.subcat=2") or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_cuotas=false;
                                }
                        }
                }

        
        if ($listado_alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        }
        
        $fecha_baja=$listado_alumnos["fecha_baja"];
                $fecha_matricula=$listado_alumnos["fecha_matricula"];


        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#ffacac";
                } else {
                if ($listado_alumnos["fecha_matricula"]>date("Y-m-d")) {
                        $color_fuente_linea="#45A9F6";
                        } else {
                        $color_fuente_linea="#000000";
                        }
                }
                
        if ($fecha_baja==$listado_alumnos["fecha_matricula"]) {
                $color_fuente_linea="#ffacac";
                }
                                // Tipos de comedor
                                $tipo_de_comedor[0]="Desconocido";
                                $tipo_de_comedor[1]="Si";
                                $tipo_de_comedor[2]="No";
                                $tipo_de_comedor[3]="Régimen";

                                // Tipos de menu
                                $tipo_de_menu[0]="Desconocido";
                                $tipo_de_menu[1]="Pequeños";
                                $tipo_de_menu[2]="Mayores";
                                $tipo_de_menu[3]="Colegial";
                                $tipo_de_menu[4]="Toma";
                                $tipo_de_menu[5]="Bebe";
         
             // Num mensualidades abonar
        $mes_inicio_asistencia=date("n",strtotime($listado_alumnos["fecha_matricula"])); 
        $mes_fin_asistencia=date("n",strtotime($listado_alumnos["fecha_baja"]));
        
                $num_meses_devolucion=0;
                $num_meses_devolucion_cheque=0;
                
                if ($mes_inicio_asistencia>=9) {

                        if ($mes_fin_asistencia>9) {
        
                                // Ha terminado este año                
                                $num_meses_devolucion=$mes_fin_asistencia-$mes_inicio_asistencia+1;
                
                                $num_meses_devolucion_cheque=10-$mes_inicio_asistencia+1;
                
                                if ($num_meses_devolucion_cheque<0) {
                                        $num_meses_devolucion_cheque=0;
                                }
        
                        } else {
        
                                // Termina el año que viene        
                                $num_meses_devolucion=12-$mes_inicio_asistencia+1;
                                $num_meses_devolucion_cheque=10-$mes_inicio_asistencia+1;
        
                        }
                }
        
        // Comprueba numero meses devolución por facturas emitidas
        
        $num_meses_devolucion_por_inscripcion=$num_meses_devolucion;
                
        $busca_facturas_emitidas_educacion1=db_query("SELECT * FROM detalles_factura LEFT JOIN facturas_emitidas ON detalles_factura.id_factura=facturas_emitidas.id_factura WHERE ((concepto like '%educaci%' AND concepto like '%Diciembre de 2016%') OR (concepto like '%educaci%' AND concepto like '%Noviembre de 2016%') OR (concepto like '%educaci%' AND concepto like '%Octubre de 2016%') OR (concepto like '%educaci%' AND concepto like '%Septiembre de 2016%')) AND facturas_emitidas.id_cliente=$id_inscripcion") or die(db_error());        
        
        $num_meses_devolucion=db_num_rows($busca_facturas_emitidas_educacion1);
        
        $alerta_devolucion=false;
        if ($num_meses_devolucion_por_inscripcion!=$num_meses_devolucion) {
	    	$alerta_devolucion=true;    
        }
        
        /*
        $busca_facturas_emitidas_educacion2=mysql_query("SELECT * FROM detalles_factura LEFT JOIN facturas_emitidas ON detalles_factura.id_factura=facturas_emitidas.id_factura WHERE ((concepto like '%Mensualidad educación%' AND concepto like '%Octubre de 2016%') OR (concepto like '%Mensualidad educación%' AND concepto like '%Septiembre de 2016%')) AND facturas_emitidas.id_cliente=$id_inscripcion") or die(mysql_error());
        
        $num_meses_devolucion_cheque=mysql_num_rows($busca_facturas_emitidas_educacion2);
        
        */        
                                
        // Dias asistidos
        $filtros_asistencia="year(servicios_consumidos.fecha)=".date("Y");
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".date("n");
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_asistidos=db_num_rows($busca_dias_asistidos);
        
        $busca_dias_comedor=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND (tipo_menu=1 OR tipo_menu=3) AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_comedor=db_num_rows($busca_dias_comedor);

		$mensaje_alerta_devolucion="";
		if ($alerta_devolucion==true) {
			$mensaje_alerta_devolucion="<br /><span class='text-danger'>¡ATENCIÓN! Los meses asistidos no coinciden con los meses facturados</span>";
		}

        ?>
        <tr>
        <td><small><?=$orden_en_aula;?></small></td>
        <td><small><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></small></td>
        <?php        
        if ($fecha_baja<date("Y-m-d")) {
        ?>
                <td class="text-danger"><small>BAJA</small></td>
        <?php 
                } else {
                        if ($fecha_matricula>=date("Y-m-d")) {
                                        ?>
                                        <td class="text-primary"><small>Próximo inicio</small></td>
                                        <?php 
                                } else {                
                                        ?>
                                        <td><small>Asitiendo</small></td>
                                        <?php 
                                        }
                }       
        
        $resumen="";
        $acciones="";
        $total_concedido=0;
        $total_devolucion=0;
        $total_adelantado=0;
        $num_mensualidades_adelantadas=0;  
        $total_devolucion_cheque=0;     
        
        // Busca subvención concedida
        $busca_concedida=db_query("SELECT * FROM servicios_contratados LEFT JOIN tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE servicios_contratados.id_servicio>=1858 AND servicios_contratados.id_servicio<=1868 AND servicios_contratados.id_alumno=".$id_inscripcion);
        
        if (db_num_rows($busca_concedida)!=0) {       
        
                $resumen.="<b>Subvención concedida:</b><br />";
        
                while($concedida=db_fetch_array($busca_concedida)) {
                
                        $resumen.=$concedida["ARTICULO"].". Importe: ".number_format($concedida["PRECIO_VENTA"],2,",",".")." €/mes programada desde el día ".date("d/m/Y",strtotime($concedida["fecha_inicio"]))." al día ".date("d/m/Y",strtotime($concedida["fecha_fin"]))."<br />";
                        
                        $total_concedido+=($concedida["PRECIO_VENTA"]*$num_meses_devolucion*-1);
                        $total_devolucion+=($concedida["PRECIO_VENTA"]*$num_meses_devolucion*-1);                                        
                
                }                
        
        }
        
        // Busca subvención adelantada
        $busca_adelantada=db_query("SELECT * FROM servicios_contratados LEFT JOIN tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE servicios_contratados.id_servicio>=1837 AND servicios_contratados.id_servicio<=1839 AND servicios_contratados.id_alumno=".$id_inscripcion);
        
        if (db_num_rows($busca_adelantada)!=0) {
        
                $resumen.="<b>Subvención adelantada:</b><br />";
        
                while($adelantada=db_fetch_array($busca_adelantada)) {
                
                        $resumen.=$adelantada["ARTICULO"].". Importe: ".number_format($adelantada["PRECIO_VENTA"],2,",",".")." €/mes programada desde el día ".date("d/m/Y",strtotime($adelantada["fecha_inicio"]))." al día ".date("d/m/Y",strtotime($adelantada["fecha_fin"]))."<br />";
                        
                        // Busca facturas emitidas de este artículo
                $busca_factura=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_inscripcion AND detalles_factura.articulo=".$adelantada["id_servicio_contratado"]." ORDER BY facturas_emitidas.fecha_emision DESC") or die(db_error());
                
                while($adelantado=db_fetch_array($busca_factura)) {
                        $total_adelantado+=round(($adelantado["precio"]*$adelantado["cantidad"]),2);
                }                                                                                
                
                }
        
        }   
        
        // Busca subvención adelantada, servicio asignado al aula
        $busca_adelantada=db_query("SELECT * FROM servicios_contratados_aula LEFT JOIN tarifa ON servicios_contratados_aula.id_servicio=tarifa.ID WHERE servicios_contratados_aula.id_servicio>=1837 AND servicios_contratados_aula.id_servicio<=1839 AND servicios_contratados_aula.id_aula=".$id_aula);        
        
        if (db_num_rows($busca_adelantada)!=0) {
        
                while($adelantada=db_fetch_array($busca_adelantada)) {
                
                        // Comprueba si tiene el servicio                       
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$adelantada["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
                        
                if (db_num_rows($busca_si_activado)==0) {
                            $resumen.="Subvención adelantada:<br />";
                
                                $resumen.=$adelantada["ARTICULO"].". Importe: ".number_format($adelantada["PRECIO_VENTA"],2,",",".")." €/mes programada desde el día ".date("d/m/Y",strtotime($adelantada["fecha_inicio"]))." al día ".date("d/m/Y",strtotime($adelantada["fecha_fin"]))."<br />";
                        
                                // Busca facturas emitidas de este artículo
                        $busca_factura=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_inscripcion AND detalles_factura.cod_facturacion='au_".$adelantada["id_servicio_contratado"]."' ORDER BY facturas_emitidas.fecha_emision DESC") or die(db_error());
                
                        while($adelantado=db_fetch_array($busca_factura)) {
                                $total_adelantado+=round(($adelantado["precio"]*$adelantado["cantidad"]),2);
                        }                
                         
                                }                                     
                
                }
        
        }       
              
        $total_devolucion+=$total_adelantado;  
        
        if ($total_devolucion>0) {
        
                        $acciones.="Descontar ".number_format($total_devolucion,2,",",".")." € correspondientes a $num_meses_devolucion mensualidades del Bono infantil de los meses de septiembre a diciembre.<br />";        
        
        }
        
        if ($total_devolucion==0) {
        
                        $acciones.="No se realiza ninguna acción por el Bono escolar, el saldo es 0 €.<br />";        
        
        }
        
        if ($total_devolucion<0) {
        
                        $acciones.="Cobrar ".number_format($total_devolucion*-1,2,",",".")." € correspondientes la dinero descontado por la subvención no concedida entre los meses de septiembre y diciembre.<br />";        
        
        }
        
        // Cheque escolar
        $busca_concedido=db_query("SELECT * FROM servicios_contratados LEFT JOIN tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE servicios_contratados.id_servicio>=1850 AND servicios_contratados.id_servicio<=1852 AND servicios_contratados.id_alumno=".$id_inscripcion);
        if (db_num_rows($busca_concedido)!=0) {       
        
                $resumen.="<b>Cheque escolar:</b><br />";
        
                while($concedido=db_fetch_array($busca_concedido)) {
                
                        $resumen.=$concedido["ARTICULO"].". Importe: ".number_format($concedido["PRECIO_VENTA"],2,",",".")." €/mes<br />";
                        
                        /*
                        $num_meses_devolucion_cheque=0;
                        if (date("n",strtotime($concedido["fecha_inicio"]))>=9) {
                                $num_meses_devolucion_cheque=date("n",strtotime($concedido["fecha_inicio"]))-9;
                                } 
                        */                       
                        
                        // Comprueba fecha cheque
                        if ($concedido["fecha_inicio"]!=$fecha_matricula) {
                        
                                $resumen.='<br /><a href="javascript:editarservicios('.$listado_alumnos["id_alumno"].')" title="Editar servicios contratados"><span class="text-danger">¡ATENCIÓN! Fecha de cheque escolar incorrecta ('.date("d/m/Y",strtotime($concedido["fecha_inicio"])).'), debe corregirse.</span></a>';                                                                
                        
                        }                
                        
                        $total_devolucion_cheque+=($concedido["PRECIO_VENTA"]*$num_meses_devolucion_cheque*-1);                                        
                
                }                
        
        }
        
                if ($total_devolucion_cheque>0) {
        
                        $acciones.="Descontar ".number_format($total_devolucion_cheque,2,",",".")." € correspondientes a $num_meses_devolucion_cheque mensualidades del Cheque Escolar entre los meses de septiembre a noviembre.<br />";        
        
        }        
        
        
                $total_a_devolver+=$total_devolucion;
                $total_a_devolver_cheque+=$total_devolucion_cheque;
                $total_devolucion_total=$total_devolucion+$total_devolucion_cheque;
                $total_a_devolver_total+=$total_devolucion_total;
                
                if ($fecha_matricula>=date("Y-m-d")) {
                        $resumen="Próximo inicio.";
                        $acciones="Próximo inicio.";
                }
                
        ?>
                <td class="text-right"><small><?=number_format($total_concedido,2,",",".");?> €<?=$mensaje_alerta_devolucion?></small></td>
                <td class="text-right"><small><?=number_format($total_adelantado,2,",",".");?> €</small></td>
                <td class="text-right"><small><?=number_format($total_devolucion,2,",",".");?> €</small></td>
        <?php
        if ($tipo_listado==0) {
        ?>                
                <td class="text-right"><small><?=number_format($total_devolucion_cheque,2,",",".");?> €</small></td>  
                <td class="text-right"><small><?=number_format($total_devolucion_total,2,",",".");?> €</small></td>               
                <td><small><?=$resumen;?></small></td>
                <td><small><?=$acciones;?></small></td>
        <?php
        }
        ?>
                <td><button class="btn btn-success" onclick="editarservicios(<?=$id_inscripcion?>)"><i class="fa fa-money" aria-hidden="true"></i></button></td>
        </tr>
        <?php 
        $orden_en_aula++;
        }

                if ($total_a_devolver_total<0) {                
                        $descripcion_saldo_final="<span class='text-success'>A favor de la escuela.</span>";
                } else {                
                        $descripcion_saldo_final="<span class='text-danger'>A favor de los padres.</span>";
                }
                
                if ($total_a_devolver_total==0) {
                        $descripcion_saldo_final="<span class='text-primary'>Ni para ti ni para mi...</span>";
                }

        ?>
        <tr>
                <td colspan=5><small>Saldo final aula</small></td>
                <td class="text-right"><small><?=number_format($total_a_devolver,2,",",".");?> €</small></td>
        <?php
        if ($tipo_listado==0) {
        ?>                  
                <td class="text-right"><small><?=number_format($total_a_devolver_cheque,2,",",".");?> €</small></td>
                <td class="text-right"><small><?=number_format($total_a_devolver_total,2,",",".");?> €</small></td>
                <td colspan=3><small><?=$descripcion_saldo_final?></small></td>
        <?php
        }
        ?>
        </tr>
        </table>
<?php
}
?>
</div>

<?php  require("../../configuracion/parte_inferior.php"); ?>
