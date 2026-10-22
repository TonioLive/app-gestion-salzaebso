<?php 
$media_jornada_doble_fichaje=true;

$turno=$_GET["turno"];

$listado_padres="";

if (strlen($turno)==0) {
        if (date("G")>15) {
                $turno="1";
                } else {
                $turno="0";
                }
        }

if ($turno=="1") {
        $nombre_turno="Tarde";
        $turno_contrario="Mañana";
        $turno_contrario_id=0;
        } else {
        $nombre_turno="Mañana";
        $turno_contrario="Tarde";
        $turno="0";
        $turno_contrario_id=1;
        }
?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="background: #ffffff; border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#cdcdcd">
                                <table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                                <?php 
                                $pagina_act=$contador_capas;
                                for ($contador_pagi=0;$contador_pagi<count($array_paginas);$contador_pagi++) {

                                if (substr($nombres_partes[$contador_pagi],0,5)!="link:") {
                                
                                        if ($pagina_act==$contador_pagi) {
                                ?>
                                <td bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><b> <?=$array_paginas[$contador_pagi]?></b></font></td>
                                <?php 
                                        } else {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='javascript:mostrar(<?=$contador_pagi?>)'><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                <?php 
                                        }
                                        } else {
                                        ?>
                                        <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='<?=substr($nombres_partes[$contador_pagi],5,strlen($nombres_partes[$contador_pagi])-5);?>?id_aula=<?=$id_aula?>' target="_new"><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                        <?php 
                                        }
                                }
                                ?>
                                </tr></table>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Control de asistencia del turno de <?=$nombre_turno;?>, <i><?=$datos_aula["nombre"];?></i></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>">
<div align="center"><center>
<input type="button" onclick="history.go(0)" value="Actualizar para editar" style="font-size: 10px"> <input type="button" onclick="window.location.href='dia_aula.php?dia=<?=$fecha_time_stamp?>&id_aula=<?=$id_aula?>&turno=<?=$turno_contrario_id?>'" value="Cambiar al turno de <?=$turno_contrario?>" style="font-size: 10px">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 

$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'ssss', array($centro_actual, $id_aula, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

$num_alumnos_aula=db_num_rows($busca_alumnos);

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=6><font size=1 face="<?= $fuente_general; ?>">Para ver la ficha de un alumno, haz clic sobre su nombre o sobre el botón ( <img src="editar.gif" align="absmiddle">)</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>E. comedor</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type=button onclick="imprimir_seleccion()" value="Imprimir seleccionados"> <input type=button onclick="check_todas();" value="Selecconar/deseleccionar todos"></b></font></td>
        </tr>
        <?php 
        $fila_par_impar=0;
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        /*
        // Busca padres
        $busca_padres=mysql_query("SELECT * FROM padres WHERE padre_de=".$listado_alumnos["id_usuario"]);
        
        while($padres=mysql_fetch_array($busca_padres)) {
                $listado_padres.=$padres["id_usuario"]."<br>";
        }
        */
        
        $esta_en_la_escuela=false;
        $total_factura=0;
        $total_a_cuenta=0;
        // Comprueba si se le han incluido los servicios
        $id_inscripcion=$listado_alumnos["id_alumno"];
        $id_usuario_inscripcion=$listado_alumnos["id_usuario_inscripcion"];
        $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=1', 's', array($id_inscripcion)) or die(db_error());
        if (db_num_rows($busca_inscripcion)==0) {
                $no_inscripcion=true;
                } else {
                $no_inscripcion=false;
                $inscripcion_pagada=true;
                while($datos_inscripcion_alumno=db_fetch_array($busca_inscripcion)) {
                        $id_servicio_contratado=$datos_inscripcion_alumno["id_servicio_contratado"];
                        // Busca factura donde se ha incluido el servicio
                        $busca_factura=db_query_params('SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.articulo=?', 'ss', array($id_inscripcion, $id_servicio_contratado)) or die(db_error());
                        if (db_num_rows($busca_factura)!=0) {
                                $datos_factura_emitida=db_fetch_array($busca_factura);
                                $id_factura_emitida=$datos_factura_emitida["id_factura"];
                                // Calcula el total y los pagos a cuenta y comprueba si esta pagada

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura_emitida)) or die (db_error());

$total_a_cuenta=0;
$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;
$total_factura=0;

$importe_subvenciones_factura=0;
$importe_subvenciones_cobradas_factura=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }

        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $importe_subvenciones_factura=$importe_subvenciones_factura+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $importe_subvenciones_cobradas_factura=$importe_subvenciones_cobradas_factura+$total_abs;
                }
        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura_emitida));
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
if (($total_a_cuenta-$total_factura)!=0) {
        $inscripcion_pagada=false;
        } else {
        $inscripcion_pagada=true;
        }
                                } else {
                                $inscripcion_pagada=false;
                                }
                }
        }

if ($no_inscripcion==true) {
        // Busca la inscripción en los servicios de aula
        $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=? AND tarifa.subcat=1', 's', array($id_aula)) or die(db_error());

        if (db_num_rows($busca_inscripcion)==0) {
                $no_inscripcion=true;
                } else {
                $no_inscripcion=false;
                $inscripcion_pagada=true;
                while($datos_inscripcion_alumno=db_fetch_array($busca_inscripcion)) {

                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_inscripcion_alumno["id_servicio_contratado"], $id_inscripcion));
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_inscripcion=false;
                                } else {
                        $id_servicio_contratado=$datos_inscripcion_alumno["id_servicio_contratado"];
                        // Busca factura donde se ha incluido el servicio
                        $busca_factura=db_query_params('SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.cod_facturacion=?', 'ss', array($id_inscripcion, 'au_' . (string)($id_servicio_contratado))) or die(db_error());
                        if (db_num_rows($busca_factura)!=0) {
                                $datos_factura_emitida=db_fetch_array($busca_factura);
                                $id_factura_emitida=$datos_factura_emitida["id_factura"];
                                // Calcula el total y los pagos a cuenta y comprueba si esta pagada

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura_emitida)) or die (db_error());

$total_a_cuenta=0;
$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;
$total_factura=0;

$importe_subvenciones_factura=0;
$importe_subvenciones_cobradas_factura=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }

        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $importe_subvenciones_factura=$importe_subvenciones_factura+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $importe_subvenciones_cobradas_factura=$importe_subvenciones_cobradas_factura+$total_abs;
                }
        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura_emitida));
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
if (($total_a_cuenta-$total_factura)!=0) {
        $inscripcion_pagada=false;
        } else {
        $inscripcion_pagada=true;
        }
                                } else {
                                $inscripcion_pagada=false;
                                }
                        }
                }
        }
        // Fin busca en aula
        }

        $busca_cuotas=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=2', 's', array($id_inscripcion)) or die(db_error());
        if (db_num_rows($busca_cuotas)==0) {
                $no_cuotas=true;
                } else {
                $no_cuotas=false;
                }

        if ($datos_aula["comprobar_inscripcion"]==0) {
                $no_inscripcion=false;
                $inscripcion_pagada=true;
                }

        if ($datos_aula["comprobar_cuotas"]==0) {
                $no_cuotas=false;
                }

        if ($inscripcion_pagada==false) {
                $color_boton=" style='color: #FF0000'";
                $link_javascript="registro_entrada_morosos";
                } else {
                $color_boton=" style='color: #000000'";
                $link_javascript="registro_entrada";
                }

        if ($no_cuotas==true) {
                $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=? AND tarifa.subcat=2', 's', array($id_aula)) or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_inscripcion["id_servicio_contratado"], $id_inscripcion));
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_cuotas=false;
                                }
                        }
                }

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

        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d",$fecha_time_stamp)) {
                $color_fuente_linea="#acacac";
                } else {
                $color_fuente_linea="#000000";
                }
                
        $color_fondo_fila="#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila="#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;
        $colores_asistencia[$listado_alumnos["id_usuario"]]="#ec8b81";
        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#ff8e8e"; 

        // Busca ultima evaluación del comedor

        $busca_ultima_moficiacion_guardada=db_query_params('SELECT * FROM agenda_programacion_evaluaciones_comedor_trimestrales WHERE id_curso=? AND id_aula=? AND id_alumno=? AND fecha_modificacion is NOT Null ORDER BY fecha_modificacion DESC LIMIT 0,1', 'sss', array($id_curso_actual, $id_aula, $listado_alumnos["id_alumno"])) or die(db_error()) or die(db_error()."-6");
        $modificada_por="";
        if (db_num_rows($busca_ultima_moficiacion_guardada)!=0) {

                $ultima_modificacion_guardada=db_fetch_array($busca_ultima_moficiacion_guardada);

                $modificada_por=date("d/m/Y",strtotime($ultima_modificacion_guardada["fecha_modificacion"]))." a las ".date("H:i",strtotime($ultima_modificacion_guardada["fecha_modificacion"]))."<br />".$ultima_modificacion_guardada["modificado_por"];

        }
        
		$busca_estado_evaluacion_guardada=db_query_params('SELECT * FROM agenda_programacion_evaluaciones_comedor_estado_alumnos WHERE id_curso=? AND id_alumno=?;', 'ss', array($id_curso_actual, $id_inscripcion)) or die(db_error()."-2");
		  
		$observaciones_comedor="";
		                                                        
		if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
		    $datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);

		    $observaciones_evaluacion_guardada=$datos_estado_evaluacion_guardada["observaciones"];
		    if (strlen($observaciones_evaluacion_guardada)!=0) {
			    $observaciones_comedor.=" 2º,";
		    }
		    $observaciones_evaluacion_guardada1=$datos_estado_evaluacion_guardada["observaciones1"];
		    if (strlen($observaciones_evaluacion_guardada1)!=0) {
			    $observaciones_comedor.=" 1º,";
		    }
		    $observaciones_evaluacion_guardada2=$datos_estado_evaluacion_guardada["observaciones2"];
		    if (strlen($observaciones_evaluacion_guardada2)!=0) {
			    $observaciones_comedor.=" 3º,";
		    }
		    
		    if ($observaciones_comedor!="") {
			    $modificada_por.="<br /><b>Comentarios en trimestres:</b>".substr($observaciones_comedor, 0, strlen($observaciones_comedor)-1);
		    	}
		    }        
        
              
        ?>
        <tr valign="top" bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
        <td><font size=2 face="<?= $fuente_general; ?>"><a href="javascript:detalles_asistencia(<?=$listado_alumnos["id_alumno"];?>)"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <?php 
        if (($no_inscripcion==false) and ($no_cuotas==false)) {
        if ($fecha_baja<date("Y-m-d",$fecha_time_stamp)) {
        
        
                // Busca si ya está registrado por la mañana
                $asistio=false;
                $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y/m/d", $fecha_time_stamp)))) or die(db_error());
                if (db_num_rows($busca_registro_entrada)!=0) {
                                $asistio=true;
                        }
        
        ?>
                <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
                <td align="center"><small><small><?=$modificada_por?></small></small></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="javascript:evaluar_comedor(<?=$listado_alumnos["id_alumno"];?>)" title="Evaluar comedor"><img src="/gestion/centros/menu.gif" border=0></a> <?php  if ($asistio==true) { ?><a href="javascript:ver_informe(<?=$listado_alumnos["id_usuario"];?>)" title="Ver informe del día"><img src="pruebas.gif" border=0></a> <a href="javascript:imprimir_informe(<?=$listado_alumnos["id_usuario"];?>)" title="Imprimir informe del día"><img src="imprimir.gif" border=0></a><?php  } ?>  <input type="checkbox" value="<?=$listado_alumnos["id_usuario"];?>" name="imprimir_inf"></font></td>
                </tr>
        <?php 
                } else {
        ?>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
                
        // Comprueba si es media jornada o jornada completa
        if (($listado_alumnos["tipo_mensualidad"]==1) and ($turno==1) and ($media_jornada_doble_fichaje==false)) {

        // Media Jornada y turno de tarde

                // Busca si ya está registrado por la mañana
                $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND turno=0 AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y/m/d", $fecha_time_stamp)))) or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                        // Busca si ya esta registrado por la tarde
                        $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND turno=1 AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y/m/d", $fecha_time_stamp)))) or die(db_error());
                        if (db_num_rows($busca_registro_entrada)==0) {
                        ?>
                        <input<?=$estado_botones;?> type="button" id="boton<?=$listado_alumnos["id_usuario"]?>" name="boton<?=$listado_alumnos["id_usuario"]?>" value="Registrar entrada" onclick="<?=$link_javascript?>(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>)"<?=$color_boton;?>></td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" disabled></td>
                        <?php 
                        } else {
                        $colores_asistencia[$listado_alumnos["id_usuario"]]="#ffffff";
                                        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#cee1eb"; 
                                        
                        $datos_registro=db_fetch_array($busca_registro_entrada);
                        
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                        </td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <?php 
                        if ($datos_registro["hora_salida"]=="") {
                        $esta_en_la_escuela=true;
                        ?>
                        <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>)">
                        <?php 
                        } else {
                        $esta_en_la_escuela=false;
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                        <?php 
                        }
                        ?>
                        </font></td>
                        <?php 
                        }
                } else {
                        $colores_asistencia[$listado_alumnos["id_usuario"]]="#ffffff";
                                        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#cee1eb"; 
                                        
                        $datos_registro=db_fetch_array($busca_registro_entrada);
                        
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                        </td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <?php 
                        if ($datos_registro["hora_salida"]=="") {
                        $esta_en_la_escuela=true;
                        ?>
                        <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>)">
                        <?php 
                        } else {
                        $esta_en_la_escuela=false; 
                        if (($datos_registro["hora_salida_c"]!="") and ($datos_registro["hora_salida_c"]=="")) { 
                                $esta_en_la_escuela=true;
                                }                      
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                        <?php 
                        }
                        ?>
                        </font></td>
                        <?php 
                }
                ?></font></td>
                <td align="center"><small><small><?=$modificada_por?></small></small></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="javascript:evaluar_comedor(<?=$listado_alumnos["id_alumno"];?>)" title="Evaluar comedor"><img src="/gestion/centros/menu.gif" border=0></a> <a href="javascript:ver_informe(<?=$listado_alumnos["id_usuario"];?>)" title="Ver informe del día"><img src="pruebas.gif" border=0></a> <a href="javascript:imprimir_informe(<?=$listado_alumnos["id_usuario"];?>)" title="Imprimir informe del día"><img src="imprimir.gif" border=0></a> <a href="/gestion/mensajes/mensajeria.php?id_alumno=<?=$listado_alumnos["id_usuario"];?>" title="Mensajes" target="_blank"><img src="/imagen/mensajes<?=$coletilla_mensajes?>.gif" border=0></a>  <input type="checkbox" value="<?=$listado_alumnos["id_usuario"];?>" name="imprimir_inf"></font></td>
                </tr>
                <?php 

        } else {

        // Jornada completa o turno de mañana

                // Busca si ya está registrado
                $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND turno=$turno AND id_servicio=$id_aula AND fecha='".date("Y/m/d",$fecha_time_stamp)."'") or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                ?>
                <input<?=$estado_botones;?> type="button" name="boton<?=$listado_alumnos["id_usuario"]?>" id="<?=$listado_alumnos["id_usuario"]?>" value="Registrar entrada" onclick="<?=$link_javascript?>(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>)"<?=$color_boton;?>></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" disabled></td>
                <?php 
                } else {
                        $colores_asistencia[$listado_alumnos["id_usuario"]]="#ffffff";
                                        $colores_asistencia_par[$listado_alumnos["id_usuario"]]="#cee1eb"; 
                                        
                        $datos_registro=db_fetch_array($busca_registro_entrada);
                
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                </td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                <?php 
                if ($datos_registro["hora_salida"]=="") {
                $esta_en_la_escuela=true;
                ?>
                <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>)">
                <?php 
                } else {
                $esta_en_la_escuela=false;
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                <?php 
                }
                ?>
                </font></td>
                <?php 
                }
                
// Comprobamos mensajes no leidos
$mostrar_alertas_mensajes=true;
if (($nivel_acceso==3) or ($nivel_acceso==4)) {
        // Coordinadora
        $filtro_coordinadora="";
		$filtro_coordinadora=" AND agenda_notas_padres.destino<>1";
        } else {
        // Educadora
        $filtro_coordinadora=" AND agenda_notas_padres.destino<>1";
        }

$coletilla_mensajes="";

if ($mostrar_alertas_mensajes==true) {

    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND tipo_nota<>2 AND estado=0 AND (agenda_notas_padres.id_centro_alumno=$centro_actual OR agenda_notas_padres.id_centro_alumno=0)".$filtro_coordinadora." ORDER BY fecha DESC"); 
    if (db_num_rows($busca_no_leidos)!=0) {
                $coletilla_mensajes="_act";
            } else {
            // Busca notas no leidas
            $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres,agenda_notas_padres_respuestas WHERE agenda_notas_padres.id_nota_padre=agenda_notas_padres_respuestas.id_nota AND agenda_notas_padres.id_alumno=".$listado_alumnos["id_usuario"]." AND agenda_notas_padres_respuestas.leida=0 AND agenda_notas_padres_respuestas.direccion=0 AND (agenda_notas_padres.id_centro_alumno=$centro_actual OR agenda_notas_padres.id_centro_alumno=0)".$filtro_coordinadora." ORDER BY fecha DESC") or die(db_error());
            if (db_num_rows($busca_no_leidos)!=0) {
                        $coletilla_mensajes="_act";
                        }
            }
                
            }
                
                ?></font></td>
                <td align="center"><small><small><?=$modificada_por?></small></small></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="javascript:evaluar_comedor(<?=$listado_alumnos["id_alumno"];?>)" title="Evaluar comedor"><img src="/gestion/centros/menu.gif" border=0></a> <a href="javascript:ver_informe(<?=$listado_alumnos["id_usuario"];?>)" title="Ver informe del día"><img src="pruebas.gif" border=0></a> <a href="javascript:imprimir_informe(<?=$listado_alumnos["id_usuario"];?>)" title="Imprimir informe del día"><img src="imprimir.gif" border=0></a> <a href="/gestion/mensajes/mensajeria.php?id_alumno=<?=$listado_alumnos["id_usuario"];?>" title="Mensajes" target="_blank"><img src="/imagen/mensajes<?=$coletilla_mensajes?>.gif" border=0></a> <input type="checkbox" value="<?=$listado_alumnos["id_usuario"];?>" name="imprimir_inf"></font></td>
                </tr>
                <?php 
                }
            }

            } else {
        $mensaje11="";
        if ($no_inscripcion==true) {
                $mensaje11=$mensaje11."Inscripción en el curso";
                }
        if ($no_cuotas==true) {
                if ($mensaje11!="") {
                        $mensaje11=$mensaje11." y ";
                        }
                $mensaje11=$mensaje11."Servicio de educación";
                }
        $mensaje11="Falta asigar al niño los servicios de: ".$mensaje11.".";
        ?>
        <td colspan=2 align="center"><font color="red" size=1><?=$mensaje11?></font></td>
        <td align="center"><small><small><?=$modificada_por?></small></small></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$listado_alumnos["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a></td>
        </tr>
        <?php 
        }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>No está prevista la asistencia de ningún alumno.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
                                </font></td>
                        </tr>
                </table>
<SCRIPT LANGUAGE=javascript>
<!--

<?php 
if ($num_alumnos_aula==1) {
?>
var solo_hay_uno=true;
<?php  } else { ?>
var solo_hay_uno=false;
<?php 
}
?>

function imprimir_seleccion() {
        lista_alumnos="";

        if (solo_hay_uno==false) {
        for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                if (document.<?=$formulario_name?>.imprimir_inf[c_alumnos].checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf[c_alumnos].value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }
                } else {
                if (document.<?=$formulario_name?>.imprimir_inf.checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf.value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }

        if (lista_alumnos!="") {
                lista_alumnos=lista_alumnos.substr(0,(lista_alumnos.length-1))
                // window.location.href="/pdf/facturas.php?facturas="+lista_alumnos;
                ventana_facturas=window.open("/webcam/agenda/informe_dia_html_docente_imprimir.php?dia=<?=$fecha_time_stamp;?>&multiple=1&lista_alumnos="+lista_alumnos,"impresion_informes","toolbar=yes,scrollbars=yes,width=640,height=480")
                } else {
                alert("Debes seleccionar algún alumno para imprimir su agenda");
                }
        }
        
function check_todas() {
        if (solo_hay_uno==false) {
                for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                        document.<?=$formulario_name?>.imprimir_inf[c_alumnos].click();
                        }
                } else {
                        document.<?=$formulario_name?>.imprimir_inf.click();
                }
        }

var pagina_destino="";

function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);

    }
}

function detalles_asistencia(id_alumno) {
        window.open("/gestion/centros/detalles_asistencia.php?fecha=<?=$fecha_time_stamp;?>&id_alumno="+id_alumno,"ventana_detalle1","toolbar=no,width=750,height=550,scrollbars=yes");
        }

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
        }

function evaluar_comedor(codigo) {
        // alert("Opción desactivada, estamos trabajando en terminar el formulario. \n\nEn breve estará disponible.");
        window.open('/gestion/agenda/evaluar_alumno.php?id_alumno='+codigo,'evaluacion','toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }

function ver_informe(codigo) {
        window.open('/webcam/agenda/informe_dia_html_docente.php?dia=<?=$fecha_time_stamp;?>&a='+codigo,'informe','toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }

function imprimir_informe(codigo) {
        window.open('/webcam/agenda/informe_dia_html_docente_imprimir.php?dia=<?=$fecha_time_stamp;?>&a='+codigo,'informe','toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');
        }

function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.<?=$formulario_name;?>.cambio_aula"+id+".value");
                window.location.href='/aulas/cambio_aula.php?na='+opcion_seleccionada+'&a='+id;
                }
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }

function editar_registro(id_registro) {
        window.open('/aulas/editar_registro.php?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function editar_registro_c(id_registro) {
        window.open('/aulas/editar_registro_c.php?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function registro_entrada_morosos(id_aula,id_alumno,fecha) {
        if (confirm("El alumno no ha pagado la inscripción. ¿Quieres fichar?")) {
                var Digital=new Date()
                var hours=Digital.getHours()
                var minutes=Digital.getMinutes()
                var seconds=Digital.getSeconds()
                eval("document.<?=$formulario_name;?>.boton"+id_alumno+".disabled=true");
                eval("document.<?=$formulario_name;?>.boton"+id_alumno+".value='"+hours+":"+minutes+":"+seconds+"'");
                // Método clásico
                // window.open('/aulas/registro_entrada.php?turno=<?=$turno;?>&id_aula='+id_aula+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro'+id_alumno,'toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                pagina_destino="/aulas/registro_entrada_ajax.php";
                sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
                }
        }
function registro_entrada(id_aula,id_alumno,fecha) {
                var Digital=new Date()
                var hours=Digital.getHours()
                var minutes=Digital.getMinutes()
                var seconds=Digital.getSeconds()
                eval("document.<?=$formulario_name;?>.boton"+id_alumno+".disabled=true");
                eval("document.<?=$formulario_name;?>.boton"+id_alumno+".value='"+hours+":"+minutes+":"+seconds+"'");
                // Método clásico
                // window.open('/aulas/registro_entrada.php?turno=<?=$turno;?>&id_aula='+id_aula+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro'+id_alumno,'toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                pagina_destino="/aulas/registro_entrada_ajax.php";
                sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        }
function registro_salida(id_registro) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida"+id_registro).disabled=true;
        document.getElementById("salida"+id_registro).value=hours+":"+minutes+":"+seconds;
        // window.open('/aulas/registro_salida.php?id_registro='+id_registro,'registro'+id_registro,'toolbar=no,resizable=no,width=450,height=400');
                pagina_destino="/aulas/registro_salida_ajax.php";
        sendAndLoad({desc:'id_registro',dato:id_registro});
        }

//-->
</SCRIPT>
