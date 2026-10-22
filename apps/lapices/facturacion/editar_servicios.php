<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
window.moveTo(0,0);
// window.sizeTo(screen.width,screen.height);
window.focus();
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$id_inscripcion=$_GET["i"];

$consulta_inscripcion=db_query("SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_alumno=$id_inscripcion") or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);

$usuario_curso=$datos_inscripcion["curso"];
$comedor_cliente=$datos_inscripcion["comedor"];

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

$consulta_nombre_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_inscripcion["id_centro"]) or die(db_error()."-2");
$datos_centro=db_fetch_array($consulta_nombre_centro);

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

if ($datos_inscripcion["comedor_especial"]==1) {
        $precio_mes[0]=$datos_inscripcion["comedor_especial_precio"];
        $precio_mes[1]=$datos_inscripcion["comedor_especial_precio"];
        $precio_mes[2]=$datos_inscripcion["comedor_especial_precio"];
        $precio_mes[3]=$datos_inscripcion["comedor_especial_precio"];
        $precio_mes[4]=$datos_inscripcion["comedor_especial_precio"];
        $precio_mes[5]=$datos_inscripcion["comedor_especial_precio"];
        }

$id_usuario_seleccionado=$datos_inscripcion["id_usuario"];

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-3");

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                }
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "guardar_inscripcion.php";

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_usuario_seleccionado;?>">
<input type="hidden" name="id_alumno" value="<?=$id_inscripcion;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Edición de Inscripción</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff">
                                        <a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100" align="left"></a>
                                        <font size=2 face="<?= $fuente_general; ?>"><br>Alumno: <b><?=$usuario_apellido1?> <?=$usuario_apellido2?>, <?=$usuario_nombre?><br><small>
                                        Inscripción en el centro <i>"<?=$datos_centro["nombre_centro"];?>"</i>.</b><br>
                                        <b>Curso académico:</b>
                                <?php 
                                $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_inscripcion["id_curso"]) or die(db_error()."-4");;
                                $cursos=db_fetch_array($consulta_cursos);

                                echo $cursos["nombre"];
                                ?><br>
                                <b>Fecha efectiva de inscripción</b>: <?=$usuario_fechamat?><br><br>
                                <b>Tipo de mensualidad</b> <?php  if ($tipo_mensualidad==1) { print "Media jornada"; } else { print "Jornada Completa"; } ?> - <b>¿Comedor?</b> <?php  if ($comedor==1) { print "Si"; } else { print "No"; } ?></font></small></td>
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
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Última factura emitida</b></font></td>
                                                        <td colspan=3><font face="<?= $fuente_general; ?>" size="2"><b>&nbsp;</b></font></td>
                                                </tr>
                                                <?php 
                                                $busca_servicios=db_query("SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=$id_inscripcion ORDER BY servicios_contratados.fecha_inicio");
                                                
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
                                                $busca_factura=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_inscripcion AND detalles_factura.articulo=".$servicios["id_servicio_contratado"]." ORDER BY facturas_emitidas.fecha_emision DESC") or die(db_error());
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

                                                $descuento=round($base_imponible*round(($servicios["descuento"]/100),4),2);
                                                $base_inc_dto=$base_imponible-$descuento;
                                                $importe=round($base_inc_dto*(1+($servicios["porcentaje_iva"])),2);
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$servicios["ARTICULO"];?><?php if (strlen($servicios["notas"])!=0) { echo " - ".$servicios["notas"]; } ?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$periodicidad;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($servicios["fecha_inicio"]));?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$fecha_fin;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=number_format($importe,2,",",".");?> €</font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$ultima_facturacion;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="nuevo_servicio.php?ii=<?=$id_inscripcion?>&ids=<?=$servicios["id_servicio_contratado"];?>"><img src="modificar.gif" border=0></a></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="<?php  if ($no_eliminar==true) {?>javascript:alert('No se puede eliminar un servicio ya facturado');<?php } else { ?>javascript:eliminar(<?=$servicios["id_servicio_contratado"];?>);<?php  } ?>"><img src="borrar.gif" border=0></a></font></td>
                                                </tr>
                                                <?php 
                                                }
                                                ?>
                                                <tr>
                                                <td align="left" colspan=8><font size=2><b>Servicios contratados asociados al aula</b></font></td>
                                                </tr>
                                                <?php 
                                                $busca_servicios=db_query("SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_aula=".$datos_inscripcion["aula"]." ORDER BY servicios_contratados_aula.fecha_inicio");

                                                if (db_num_rows($busca_servicios)==0) {
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center" colspan=8>
                                                        <br><b>No hay servicios asociados al aula.</b><br><br>
                                                        </td>
                                                </tr>
                                                <?php 
                                                }

                                                while($servicios=db_fetch_array($busca_servicios)) {

                                                // Busca ultima factura emitida de este artículo
                                                $busca_factura=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_inscripcion AND detalles_factura.cod_facturacion='au_".$servicios["id_servicio_contratado"]."' ORDER BY facturas_emitidas.fecha_emision DESC") or die(db_error());
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
                                                
                                                $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$servicios["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
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
                                                $busca_factura=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_inscripcion AND detalles_factura.cod_facturacion like 'co_%' ORDER BY facturas_emitidas.fecha_emision DESC") or die(db_error());
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
                                                $busca_servicios=db_query("SELECT * FROM servicios_comedor_contratados WHERE id_alumno=$id_inscripcion ORDER BY fecha_inicio");

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
                                        <input type="button" onclick="window.location.href='nuevo_servicio.php?ii=<?=$id_inscripcion;?>'" value="Agregar servicios" style="font-size: 10px"> <?php if (($nivel_acceso==4) or ($zona_facturacion==1)) {
 ?><input type="button" onclick="window.open('/facturacion/listado_facturas_alumno_ampliado.php?id_cliente=<?=$id_inscripcion;?>','facturas','toolbar=yes,scrollbars=yes');" value="Ver facturas emitidas" style="font-size: 10px"> <?php } ?>
                                        <br>
                                        <br>
                                        </td>
                                </tr>
                         </table>
                         <br><input type="button" onclick="window.close()" value="Salir" style="font-size: 10px"><input type="button" onclick="cambia();" value="Guardar cambios" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function eliminar(id) {
        if (confirm("¿Realmente deseas eliminar el servicio seleccionado?")) {
                window.location.href='eliminar_servicio.php?ii=<?=$id_inscripcion?>&ids='+id;
                }
        }
function eliminar_aula(id) {
        if (confirm("¿Realmente deseas eliminar este servicio asociado?")) {
                window.location.href='desactivar_servicio.php?ii=<?=$id_inscripcion?>&ids='+id;
                }
        }
function activar_aula(id) {
        if (confirm("¿Realmente deseas volver a añadir este servicio asociado?")) {
                window.location.href='activar_servicio.php?ii=<?=$id_inscripcion?>&ids='+id;
                }
        }
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
        
function cambia() {
                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.curso.value=="") { mensajes_error=mensajes_error+"No has seleccionado el curso académico.\u000A";errores=1; }
                
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.usuario_fechamat_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de matrícula introducida no es correcta.\u000A";errores=1; }
                
                // Comprueba la entidad bancaria
                cuenta1=self.document.<?=$formulario_name;?>.banco_cuenta1.value;
                cuenta2=self.document.<?=$formulario_name;?>.banco_cuenta2.value;
                cuenta3=self.document.<?=$formulario_name;?>.banco_cuenta3.value;
                cuenta4=self.document.<?=$formulario_name;?>.banco_cuenta4.value;
                if ((cuenta1+cuenta2+cuenta3+cuenta4)!="") {
                        // Parte 1
                        if (isNaN(cuenta1)) {
                                mensajes_error=mensajes_error+"El número de la entidad no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta1=cuenta1.replace(" ","");
                                if (cuenta1.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la entidad no es correcto,debe tener 4 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 2
                        if (isNaN(cuenta2)) {
                                mensajes_error=mensajes_error+"El número de la oficina no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta2=cuenta2.replace(" ","");
                                if (cuenta2.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la oficina no es correcto,debe tener 4 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 3
                        if (isNaN(cuenta3)) {
                                mensajes_error=mensajes_error+"El dígito de control no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta3=cuenta3.replace(" ","");
                                if (cuenta3.length!=2) {
                                        mensajes_error=mensajes_error+"El dígito de control no es correcto,debe tener 2 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 4
                        if (isNaN(cuenta4)) {
                                mensajes_error=mensajes_error+"El número de la cuenta no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta4=cuenta4.replace(" ","");
                                if (cuenta4.length!=10) {
                                        mensajes_error=mensajes_error+"El número de la cuenta no es correcto,debe tener 10 dígitos.\u000A";errores=1;
                                        }
                                }
                        }
                
                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }
//-->
</script>
</center></div>
</div>
</body>
</html>
