<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

$id_inscripcion=$_GET["i"];

$consulta_inscripcion=db_query("SELECT * FROM $baseDatosOrigenExternos.alumnos, $baseDatosOrigenExternos.cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_alumno=$id_inscripcion") or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);

$usuario_curso=$datos_inscripcion["id_curso"];
$comedor_cliente=$datos_inscripcion["comedor"];

$lista_periodos=array();

$lista_periodos[0] = "";

$consulta_periodos = db_query("SELECT * FROM aulas_grupos_trimestres ORDER BY id_trimestre") or die (db_error());
        while($datos_periodos = db_fetch_array($consulta_periodos)){
	        
	        $lista_periodos[$datos_periodos["id_trimestre"]] = $datos_periodos["nombre_trimestre"];

        }

$usuario_fechamat=date("d/m/Y",strtotime($datos_inscripcion["fecha_matricula"]));

$tipo_mensualidad=$datos_inscripcion["tipo_mensualidad"];
$comedor=$datos_inscripcion["comedor"];
$tipo_menu_cliente=$datos_inscripcion["tipo_menu_comedor"];

                                // Tipos de comedor
                                $tipo_de_comedor[0]="Desconocido";
                                $tipo_de_comedor[1]="Comedor";
                                $tipo_de_comedor[2]="No";
                                $tipo_de_comedor[3]="Comedor Régimen";

                                // Tipos de menu
                                $tipo_de_menu[0]="Desconocido";
                                $tipo_de_menu[1]="Pequeños";
                                $tipo_de_menu[2]="Mayores";
                                $tipo_de_menu[3]="Colegial";
                                $tipo_de_menu[4]="Toma";
                                $tipo_de_menu[5]="Bebe";


                                if (($datos_inscripcion["fecha_inscripcion"]=="0000-00-00") or ($datos_inscripcion["fecha_inscripcion"]=="")) {
                                        $fecha_inscripcion="Desconocida";
                                        } else {
                                        $fecha_inscripcion=date("d/m/Y",strtotime($datos_inscripcion["fecha_inscripcion"]));
                                        }
                                if (($datos_inscripcion["fecha_matricula"]=="0000-00-00") or ($datos_inscripcion["fecha_matricula"]=="")) {
                                        $fecha_inicio_docencia="Desconocida";
                                        } else {
                                        $fecha_inicio_docencia=date("d/m/Y",strtotime($datos_inscripcion["fecha_matricula"]));
                                        }
                                if (($datos_inscripcion["fecha_baja"]=="0000-00-00") or ($datos_inscripcion["fecha_baja"]=="")) {
                                        $fecha_de_baja="Desconocida";
                                        } else {
                                        $fecha_de_baja=date("d/m/Y",strtotime($datos_inscripcion["fecha_baja"]));
                                        }

$id_usuario_seleccionado=$datos_inscripcion["id_usuario"];

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM $baseDatosOrigenExternos.usuarios WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-3");

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                }
        }

// Dias asistidos en el próximo trimestre

// Busca inicio proximo trimestre

$diasemana_inicio=date("w",(time()-(86400*30)));
$fecha_control=(time()+(86400*14))-($diasemana_inicio*86400)+86400;

$campos_inicio_dias=array("d","l","m","x","j","v","s","d");
$nombres_campos_dias=array("","l","m","x","j","v","s","d");

$num_dias_asiste=array();

$nombre_proximo_trimestre="";

for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1) {

	$diasemana=date("w",$fecha_control);

	if ($diasemana==0) {
		$diasemana=7;
	}

	$busca_proximo_trimestre=db_query("SELECT * FROM aulas_grupos_trimestres WHERE ini_".$campos_inicio_dias[$diasemana].">='".date("Y-m-d",$fecha_control)."' AND fin_".$campos_inicio_dias[$diasemana].">='".date("Y-m-d",$fecha_control)."';") or die(db_error());

	if (db_num_rows($busca_proximo_trimestre)!=0) {
        $datos_trimestre=db_fetch_array($busca_proximo_trimestre);
        
        $fecha_renovacion=true;
        $fecha_inicio_proximo_trimestre=$datos_trimestre["ini_".$campos_inicio_dias[$diasemana]];
        $fecha_fin_proximo_trimestre=$datos_trimestre["fin_".$campos_inicio_dias[$diasemana]]; 

		$nombre_proximo_trimestre=$datos_trimestre["nombre_trimestre"];

        // buscamos numero de sesiones previstas para el proximo trimestre
        $num_dias_trimestre=0;
        $inicio_bucle=strtotime($fecha_inicio_proximo_trimestre)+7200;
        $fin_bucle=strtotime($fecha_fin_proximo_trimestre)+7200;

                while($inicio_bucle<=$fin_bucle) {
                                
                        // Comprueba si es festivo
                        $busca_cal_laboral=db_query_params('SELECT * FROM calendario_laboral WHERE dia=? AND estado=1', 's', array((string)(date("Y-m-d", $inicio_bucle))));                
                        if (db_num_rows($busca_cal_laboral)==0) {
                                $num_dias_trimestre++;                        
                        }
                                
                        $inicio_bucle=$inicio_bucle+(86400*7);
                }
	}

	$num_dias_asiste[$diasemana]=$num_dias_trimestre;

	$fecha_control=$fecha_control+86400;

}

// Comprueba dia de la semana asiste alumno
$busca_inscripcion_grupo_alumno=db_query("SELECT * FROM aulas_alumnos_grupos,aulas_grupos,alumnos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND alumnos.id_usuario=".$id_usuario_seleccionado." AND aulas_alumnos_grupos.id_curso=".$usuario_curso);

$nombres_dias_asiste="";

$total_sesiones_asiste=0;
$total_dias_asiste=0;

while ($datos_dias_asiste=db_fetch_array($busca_inscripcion_grupo_alumno)) {
	
	$dia_semana_asiste=$datos_dias_asiste["dia"];
	
	$nombres_dias_asiste.="<b>".$dia_sem_largo[$datos_dias_asiste["dia"]]."</b> de ".date("H:i",strtotime($datos_dias_asiste["hora_inicio"]))." a ".date("H:i",strtotime($datos_dias_asiste["hora_fin"])).". Asistirá a ".$num_dias_asiste[$dia_semana_asiste]." sesiones durante el trimestre.<br />";
	
	$total_dias_asiste++;
	$total_sesiones_asiste+=$num_dias_asiste[$dia_semana_asiste];
	
	}
	
if ($total_dias_asiste>1) {

	$nombres_dias_asiste.="Total sesiones asistirá durante el trimestre: ".$total_sesiones_asiste;	

}

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>

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

<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<SCRIPT LANGUAGE=javascript>
<!--
window.moveTo(0,0);
// window.sizeTo(screen.width,screen.height);
window.focus();

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function cobrar_plaza_0(id_actividad,id_alumno,fecha,cantidad_cobrada,id_alu) {
        window.location.href='/gestion/material/cobro_actividad.php?no_fecha_hoy=1&solo_emitir=1&curso_seleccionado=<?=$id_curso_actual?>&inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha+'&cantidad_cobrada='+cantidad_cobrada+'&id_alu='+id_alu;
        }

function eliminar(id) {
        if (confirm("¿Realmente deseas eliminar el servicio seleccionado?")) {
                window.location.href='eliminar_servicio_externos.php?ii=<?=$id_inscripcion?>&ids='+id;
                }
        }

function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }       

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff">
                                        <font size=2 face="<?= $fuente_general; ?>"><br>Alumno/a: <b><?=$usuario_apellido1?> <?=$usuario_apellido2?>, <?=$usuario_nombre?><small> - 
                                        <b>Curso académico:</b>
                                <?php 
                                $consulta_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_inscripcion["id_curso"])) or die(db_error()."-4");;
                                $cursos=db_fetch_array($consulta_cursos);

                                echo $cursos["nombre"];
                                ?>
                                </font></small></td>
                                </tr>                               
                                <tr vAlign="top" bgcolor="#efefef">
                                        <td colspan=2 align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Servicios contratados</b></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=100% align="center" colspan=2>
                                        <table width=100% cellpadding=2 cellspacing=0 border=1 bordercolor="#000000" style="border-collapse: collapse">
                                                <tr bgcolor="#EFEFEF">
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Servicio</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Periodicidad</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Inicio</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Fin</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Importe</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Último  recibo emitido</b></font></td>
                                                        <td colspan=3><font face="<?= $fuente_general; ?>" size="2"><b>&nbsp;</b></font></td>
                                                </tr>
                                                <?php 
                                                $busca_servicios=db_query_params('SELECT * FROM servicios_contratados_externos, tarifa, tipos_iva_servicios WHERE servicios_contratados_externos.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_externos.id_alumno=? ORDER BY servicios_contratados_externos.fecha_inicio', 's', array($id_inscripcion));
                                                
                                                if (db_num_rows($busca_servicios)==0) {
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center" colspan="7">
                                                        <br><b>No hay servicios contratados, haz clic en el botón <i>"Agregar servicios"</i> para empezar a trabajar.</b><br><br>
                                                        </td>
                                                </tr>
                                                <?php 
                                                }
                                                
                                                while($servicios=db_fetch_array($busca_servicios)) {
                                                
                                                // Busca ultima factura emitida de este artículo
                                                $busca_factura=db_query_params('SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.articulo=? ORDER BY facturas_emitidas.fecha_emision DESC', 'ss', array($id_inscripcion, $servicios["id_servicio_contratado"])) or die(db_error());
                                                if (db_num_rows($busca_factura)==0) {
                                                               $ultima_facturacion='<font color=red>No facturado <button type="button" class="btn btn-default btn-xs btn-xs btn-primary" onclick="cobrar_plaza_0('.$servicios["id_servicio_contratado"].','.$datos_inscripcion["id_usuario"].','.time().','.$servicios['cantidad'].','.$datos_inscripcion["id_alumno"].')">Emitir recibo</button></font>';
                                                               $no_eliminar=false;
                                                        } else {
                                                                $datos_factura=db_fetch_array($busca_factura);
                                                                $ultima_facturacion='<small><a href="/pdf/recibos_material.php?id_factura='.$datos_factura["id_factura"].'" target="_blank" type="button" class="btn btn-default btn-xs btn-xs btn-primary">RECIBO</a> <a href="/pdf/recibos_material.php?num_vencimientos=2&id_factura='.$datos_factura["id_factura"].'" target="_blank" type="button" class="btn btn-default btn-xs btn-xs btn-primary">RECIBO 2 PAGOS</a> <a href="/pdf/recibos_material.php?original=1&id_factura='.$datos_factura["id_factura"].'" target="_blank" type="button" class="btn btn-default btn-xs btn-xs btn-warning">RECIBO ORIGINAL</a> <a href="/pdf/recibos_material.php?original=1&id_factura='.$datos_factura["id_factura"].'&num_vencimientos=2" target="_blank" type="button" class="btn btn-default btn-xs btn-xs btn-warning">ORIGINAL 2 PAGOS</a></small>';
                                                                $no_eliminar=true;
                                                        }
                                                
                                                if ($servicios["periodicidad"]==1) {
                                                      $periodicidad="Anual";
                                                      $fecha_fin=date("d/m/Y",strtotime($servicios["fecha_fin"]));
                                                      } else {
                                                      $periodicidad="Mensual";
                                                      $fecha_fin="-";
                                                      }
                                                // Calcula importe
                                                $base_imponible=round(($servicios["PRECIO_VENTA"]*$servicios["cantidad"]),4);

                                                $descuento=round($base_imponible*round(($servicios["descuento"]/100),4),2);
                                                $base_inc_dto=$base_imponible-$descuento;
                                                $importe=round($base_inc_dto*(1+($servicios["porcentaje_iva"])),4);
                                                
                                                $coletilla_periodos="";        
                                                if ($servicios["periodo"]!=0) {
	                                                
	                                              $coletilla_periodos=" - ".$lista_periodos[$servicios["periodo"]];
	                                                
                                                }                                                 
                                                
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$servicios["ARTICULO"].$coletilla_periodos;?><?php if (strlen($servicios["notas"])!=0) { echo " - ".$servicios["notas"]; } ?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$periodicidad;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($servicios["fecha_inicio"]));?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$fecha_fin;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=number_format($importe,2,",",".");?> €</font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$ultima_facturacion;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="nuevo_servicio_externo.php?ii=<?=$id_inscripcion?>&ids=<?=$servicios["id_servicio_contratado"];?>"><img src="modificar.gif" border=0></a></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="<?php  if ($no_eliminar==true) {?>javascript:alert('No se puede eliminar un servicio ya facturado');<?php } else { ?>javascript:eliminar(<?=$servicios["id_servicio_contratado"];?>);<?php  } ?>"><img src="borrar.gif" border=0></a></font></td>
                                                </tr>
                                                <?php 
                                                }

                                                $busca_servicios=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_aula=? ORDER BY servicios_contratados_aula.fecha_inicio', 's', array($datos_inscripcion["aula"]));

                                                if (db_num_rows($busca_servicios)==0) {
													// No hay servicios asociados al aula
                                                }

                                                while($servicios=db_fetch_array($busca_servicios)) {

                                                // Busca ultima factura emitida de este artículo
                                                $busca_factura=db_query_params('SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.cod_facturacion=? ORDER BY facturas_emitidas.fecha_emision DESC', 'ss', array($id_inscripcion, 'au_' . (string)($servicios["id_servicio_contratado"]))) or die(db_error());
                                                if (db_num_rows($busca_factura)==0) {
                                                               $ultima_facturacion="<font color=red>No facturado</font>";
                                                               $no_eliminar=false;
                                                        } else {
                                                                $datos_factura=db_fetch_array($busca_factura);
                                                                $ultima_facturacion='<small><a href="/pdf/factura.php?id_factura='.$datos_factura["id_factura"].'">nº '.$datos_factura["num_factura"].'/'.$datos_factura["ejercicio_factura"].' del '.date("d/m/Y",strtotime($datos_factura["fecha_emision"])).'</a></small>';
                                                                $no_eliminar=true;
                                                        }

                                                if ($servicios["periodicidad"]==1) {
                                                      $periodicidad="Anual";
                                                      $fecha_fin=date("d/m/Y",strtotime($servicios["fecha_fin"]));
                                                      } else {
                                                      $periodicidad="Mensual";
                                                      $fecha_fin="-";
                                                      }
                                                // Calcula importe
                                                $base_imponible=round(($servicios["PRECIO_VENTA"]*$servicios["cantidad"]),2);
                                                $descuento=round($base_imponible*round(($servicios["descuento"]/100),2),2);
                                                $base_inc_dto=$base_imponible-$descuento;
                                                $importe=round($base_inc_dto*(1+($servicios["porcentaje_iva"])),2);
                                                
                                                // Comprueba si lo tiene desactivado
                                                
                                                $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($servicios["id_servicio_contratado"], $id_inscripcion));
                                                if (db_num_rows($busca_si_activado)!=0) {
                                                        $efect1="<strike>";
                                                        $efect2="</strike>";
                                                        $no_eliminar=true;
                                                        $color_linea="#FF0000";
                                                        } else {
                                                        $efect1="";
                                                        $efect2="";
                                                        $color_linea="#000000";
                                                        $no_eliminar=false;
                                                        }                                                                                                    
                                                        
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>"><?=$efect1?><?=$servicios["ARTICULO"];?><?php if (strlen($servicios["notas"])!=0) { echo " - ".$servicios["notas"]; } ?><?=$efect2?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>"><?=$efect1?><?=$periodicidad;?><?=$efect2?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>"><?=$efect1?><?=date("d/m/Y",strtotime($servicios["fecha_inicio"]));?><?=$efect2?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>"><?=$efect1?><?=$fecha_fin;?><?=$efect2?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>"><?=$efect1?><?=number_format($importe,2,",",".");?> €<?=$efect2?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>"><?=$efect1?><?=$ultima_facturacion;?><?=$efect2?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>">&nbsp;</font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2" color="<?=$color_linea?>"><?php  if ($no_eliminar==true) {?><a href="javascript:activar_aula(<?=$servicios["id_servicio_contratado"];?>);"><img src="activar.gif" border=0></a><?php } else { ?><a href="javascript:eliminar_aula(<?=$servicios["id_servicio_contratado"];?>);"><img src="borrar.gif" border=0><?php  } ?></a></font></td>
                                                </tr>
                                                <?php 
                                                }

                                if (($comedor_cliente==3) or ($comedor_cliente==1)) {
                                
                                                // Busca ultima factura emitida de este artículo
                                                $busca_factura=db_query_params('SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.cod_facturacion like \'co_%\' ORDER BY facturas_emitidas.fecha_emision DESC', 's', array($id_inscripcion)) or die(db_error());
                                                if (db_num_rows($busca_factura)==0) {
                                                               $ultima_facturacion="<font color=red>No facturado</font>";
                                                               $no_eliminar=false;
                                                        } else {
                                                                $datos_factura=db_fetch_array($busca_factura);
                                                                $ultima_facturacion='<small><a href="/pdf/factura.php?id_factura='.$datos_factura["id_factura"].'">nº '.$datos_factura["num_factura"].'/'.$datos_factura["ejercicio_factura"].' del '.date("d/m/Y",strtotime($datos_factura["fecha_emision"])).'</a></small>';
                                                                $no_eliminar=true;
                                                        }
                                
                                                ?>
                                <tr vAlign="top" bgcolor="#efefef">
                                        <td colspan=8 align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Comedor</b></font></td>
                                </tr>
                                                <tr bgcolor="#EFEFEF">
                                                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="2"><b>Servicio</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Inicio</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Fin</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Importe</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Última factura emitida</b></font></td>
                                                        <td colspan=3><font face="<?= $fuente_general; ?>" size="2"><b>&nbsp;</b></font></td>
                                                </tr>
                                                <?php 
                                                $busca_servicios=db_query_params('SELECT * FROM servicios_comedor_contratados WHERE id_alumno=? ORDER BY fecha_inicio', 's', array($id_inscripcion));

                                                if (db_num_rows($busca_servicios)==0) {
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="2"><?=$tipo_de_comedor[$datos_inscripcion["comedor"]]." Menú: ".$tipo_de_menu[$datos_inscripcion["tipo_menu_comedor"]];?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$fecha_inicio_docencia?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$fecha_de_baja;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=number_format($precio_mes[$tipo_menu_cliente],2,",",".");?> €</font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$ultima_facturacion;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><img src="/imagen/nada.gif" border=0 width=16 height=16></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><img src="/imagen/nada.gif" border=0 width=16 height=16></font></td>
                                                </tr>
                                                <?php 
                                                }

                                                while($servicios=db_fetch_array($busca_servicios)) {
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="2"><?=$tipo_de_comedor[$datos_inscripcion["comedor"]]." Menú: ".$tipo_de_menu[$datos_inscripcion["tipo_menu_comedor"]];?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($servicios["fecha_inicio"]));?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$fecha_fin;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=number_format($precio_mes[$tipo_menu_cliente],2,",",".");?> €</font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$ultima_facturacion;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="nuevo_servicio.php?ii=<?=$id_inscripcion?>&ids=<?=$servicios["id_servicio_contratado"];?>"><img src="modificar.gif" border=0></a></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="<?php  if ($no_eliminar==true) {?>javascript:alert('No se puede eliminar un servicio ya facturado');<?php } else { ?>javascript:eliminar(<?=$servicios["id_servicio_contratado"];?>);<?php  } ?>"><img src="borrar.gif" border=0></a></font></td>
                                                </tr>
                                                <?php 
                                                }
                                                }
                                                ?>
                                        </table><br>
											<a href='nuevo_servicio_externo.php?ii=<?=$id_inscripcion;?>' class="btn btn-md btn-success">Agregar servicios</a>
											<button onclick="window.open('/facturacion/listado_facturas_alumno_ampliado.php?id_cliente=<?=$id_inscripcion;?>','facturas','toolbar=yes,scrollbars=yes');" class="btn btn-md btn-primary">Ver facturas emitidas</button>
                                        <br>
                                        <br>
                                        </td>
                                </tr>
                         </table>                                                                              
</center></div>
</div>
</body>
</html>
