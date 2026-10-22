<?php  require("../configuracion/config.php");

$nuevo_filtro_facturas=$_POST['filtro_facturas'];

if (strlen($nuevo_filtro_facturas)==0) {
        $filtro_facturas=$_SESSION['filtro_facturas'];
        } else {
        if ($nuevo_filtro_facturas=="0") {
                $_SESSION['filtro_facturas']="";
                $filtro_facturas="";
                } else {
                $_SESSION['filtro_facturas']=$nuevo_filtro_facturas;
                $filtro_facturas=$nuevo_filtro_facturas;
                }
        }

if (strlen($filtro_facturas)!=0) {
        if ($filtro_facturas!="OS") {
                $filtro_facturas_sql=" AND facturas_emitidas.serie='$filtro_facturas'";
                } else {
                $filtro_facturas_sql=" AND facturas_emitidas.serie is Null";
                }
        } else {
        $filtro_facturas_sql="";
        }


?>
<?php  require("../configuracion/autentifica.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu_ancho.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" align="center">
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
<?php 

$id_cliente=$_GET["id_cliente"];
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
if (strlen($ejercicio)!=0) {
        $filtros=" AND year(facturas_emitidas.fecha_emision)=".$ejercicio;
        }
if (strlen($id_cliente)!=0) {
        $filtros=$filtros." AND facturas_emitidas.id_cliente=".$id_cliente;
        
        // Busca datos del alumno
        $consulta_datos_del_cliente=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=".$id_cliente);

        $datos_del_cliente=db_fetch_array($consulta_datos_del_cliente);
        
        $nombre_cliente_facturas=$datos_del_cliente["banco_titular"];
        $dni_cliente_facturas=$datos_del_cliente["banco_dni_titular"];
        $nombre_alumno_facturas=$datos_del_cliente["Apellido1"]." ".$datos_del_cliente["Apellido2"].", ".$datos_del_cliente["Nombre"];
        
        }
        
if (strlen($mes)!=0) {
        $filtros=$filtros." AND month(facturas_emitidas.fecha_emision)=".$mes;
        }
if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }
$filtros=$filtros.$filtro_facturas_sql;
?>
<script language="Javascript">
function eliminar(id_factura) {
        if (confirm("Se eliminará la factra seleccionada.\n\n¿Estas seguro?")) {
                top.window.location.href='eliminar_factura_alumno.php?centro_factura=<?=$centro_factura?>&id_cliente=<?=$id_cliente?>&ejercicio=<?=$ejercicio?>&mes=<?=$mes?>&id_factura='+id_factura;
                }
        }
        
function comprobar_y_enviar() {
        top.window.document.albaranes.submit();
        }

function pagos_a_cuenta(id_factura) {
        window.open("pagos_a_cuenta.php?id_factura="+id_factura,'pagos_a_cuenta','width=550,height=450');
        }

</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>    <form name="filtro_f" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <br>
                         <input type="button" value="Salir" onclick="window.close();" style="font-size: 10px">
                         <br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=8><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Facturas del alumno - <?=$nombre_alumno_facturas;?> - Tipo: <select onchange="self.document.filtro_f.submit()" name="filtro_facturas" style="font-size: 10px">
                           <option value="0">Todas</value>
                           <option value="EDU"<?php  if ($filtro_facturas=="EDU") { print " selected"; } ?>>Educación</value>
                           <option value="OS"<?php  if ($filtro_facturas=="OS") { print " selected"; } ?>>Otros servicios</value>
                           </select></b><br>
                           <br>Facturas emitidas a nombre de: <?=$nombre_cliente_facturas;?> (<?=$dni_cliente_facturas;?>)<br><br>
                           <small>Puedes imprimir o gestionar los pagos de las facturas haciendo clic en los vínculos que encontrarás en cada línea.<br></small></font></td>
                        </tr>
                        </form>
                        <?php 
                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario$filtros ORDER BY fecha_emision DESC";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());
                        
                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center" colspan=8><font size=2 face="<?= $fuente_general; ?>">No se han encontrado facturas emitidas con los criterios seleccionados...</font></td>
                        </tr>
                        <?php 
                        }
                        
                        $contador=0;
                        
                        $total_absoluto=0;
                        $total_cobrado=0;
                        $nivel_maximo_morosidad=0;
                        $total_subvenciones=0;
                        $total_cobrado_subvenciones=0;
                        
                        $morisidades[0]="<font color=green>No tiene pagos atrasados.</font>";
                        $morisidades[1]="<font color=red>Leve</font>";
                        $morisidades[2]="<font color=red>Grave</font>";
                        $morisidades[3]="<font color=red>Muy Grave</font>";
                        $morisidades[4]="<font color=red>Expulsión</font>";

                        while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){
                        
                        // Mira si los albaranes de la factura y calcula los totales
                        $id_factura=$datos_factura["id_factura"];
// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (odbc_errormsg());

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

$contador_meses_facturacion=0;
$meses_facturacion[0]="";
$meses_facturacion[1]="";
$meses_facturacion[2]="";
$meses_facturacion[3]="";
$meses_facturacion[4]="";
$meses_facturacion[5]="";
$meses_facturacion[6]="";
$meses_facturacion[7]="";
$meses_facturacion[8]="";
$meses_facturacion[9]="";
$meses_facturacion[10]="";
$meses_facturacion[11]="";
$meses_facturacion[12]="";
$meses_facturacion[13]="";
$meses_facturacion[14]="";
$meses_facturacion[15]="";
$meses_facturacion[16]="";
$meses_facturacion[17]="";
$meses_facturacion[18]="";
$meses_facturacion[19]="";
$meses_facturacion[20]="";
$meses_facturacion[21]="";
$meses_facturacion[22]="";
$meses_facturacion[23]="";
$meses_facturacion[24]="";
$meses_facturacion[25]="";
$meses_facturacion[26]="";
$meses_facturacion[27]="";
$meses_facturacion[28]="";
$meses_facturacion[29]="";

$lineas_detalles="<tr bgcolor='#efefef'><td colspan=8><font size=2><b>Detalles de la factura</b></font></td></tr><tr>
        <td bgcolor='#efefef' colspan=5><font size=1><b>Concepto</b></font></td>
        <td bgcolor='#efefef'><font size=1><b>Precio</b></font></td>
        <td bgcolor='#efefef'><font size=1><b>Cantidad</b></font></td>
        <td bgcolor='#efefef'><font size=1><b>Total</b></font></td>
        </tr>";

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $concepto_linea=$datos_albaranes["concepto"];
        if (strlen($concepto_linea)!=0) {
                $concepto_linea=strtoupper($concepto_linea);
                }

        if ((is_numeric(strpos($concepto_linea,"EDUCACION"))) or (is_numeric(strpos($concepto_linea,"EDUCACIÓN")))) {

                $fecha_emision_factura=$datos_factura["fecha_emision"];
                $anio_factura=date("Y",strtotime($fecha_emision_factura));
                $mes_emision_factura=date("n",strtotime($fecha_emision_factura));

                // 11 Meses anteriores
                for ($c_meses=1;$c_meses<=11;$c_meses++) {
                $mes_buscado=$mes_emision_factura-$c_meses;
                if ($mes_buscado<=0) {
                        $anio_buscado=$anio_factura-1;
                        $mes_buscado=12+$mes_buscado;
                        } else {
                        $anio_buscado=$anio_factura;
                        }
                $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                        $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                        $contador_meses_facturacion++;
                        }
                }

                // Mes emision factura
                $mes_buscado=$mes_emision_factura;
                $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                $anio_buscado=$anio_factura;
                if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                        $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                        $contador_meses_facturacion++;
                        }

                // 11 Meses posteriores
                for ($c_meses=1;$c_meses<=11;$c_meses++) {
                $mes_buscado=$mes_emision_factura+$c_meses;
                if ($mes_buscado>=13) {
                        $anio_buscado=$anio_factura+1;
                        $mes_buscado=12-$mes_buscado;
                        } else {
                        $anio_buscado=$anio_factura;
                        }
                $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                        $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                        $contador_meses_facturacion++;
                        }
                }

                }
                
        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),4);
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
                
        $nombre_articulo=$datos_albaranes["concepto"];


        $lineas_detalles=$lineas_detalles."<tr bgcolor='#ffffff'>
        <td colspan=5><font size=1>".$nombre_articulo."</font></td>
        <td align='right'><font size=1>".number_format($datos_albaranes["precio"], 2, ',', '.')." €"."</font></td>
        <td align='right'><font size=1>".number_format($datos_albaranes["cantidad"], 2, ',', '.')."</font></td>
        <td align='right'><font size=1>".number_format($total_abs, 2, ',', '.')." €"."</font></td>
        </tr>";
        
        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                }
        }
        $lineas_detalles=$lineas_detalles."</tr><tr><td colspan=8><font size=1>&nbsp;</font></td></tr>";

$total_factura=round(($total_base+$total_iva+$total_recargo),2);
$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_0=number_format(Round($total_base_0,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_0=number_format(Round($total_iva_0,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

$total_dias_asistidos=0;

// Comprueba dias asistidos en el mes de facturacion
if ($meses_facturacion[0]!="") {
        $filtros="(month(fecha)=".date("n",strtotime($meses_facturacion[0]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[0])).")";
        for ($c_meses=1;$c_meses<=$contador_meses_facturacion;$c_meses++) {
                if ($meses_facturacion[$c_meses]!="") {
                        $filtros=$filtros." OR (month(fecha)=".date("n",strtotime($meses_facturacion[$c_meses]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[$c_meses])).")";
                        }
                }
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros AND id_alumno=".$datos_factura["id_usuario"]." GROUP BY fecha;");
        $total_dias_asistidos=db_num_rows($busca_dias_asistidos);
        }

// Comprueba pagos a cuenta
$total_a_cuenta=0;

$estado_factura="<font color=red><small>No cobrada</small></font>";

$busca_pagos=db_query("SELECT * FROM pagos_a_cuenta WHERE id_factura=$id_factura");
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
if ($total_a_cuenta<($total_base+$total_iva+$total_recargo)) {
        if ($total_a_cuenta>0) {
                $estado_factura="<font color=red><small>Pendiente cobro: ".number_format((($total_base+$total_iva+$total_recargo)-$total_a_cuenta), 2, ',', '.')." €</small></font>";
                } else {
                $estado_factura="<font color=red><small>No cobrada</small></font>";
                }
        $morosidad=true;
        } else {
        $estado_factura="<font color=green><small>Cobrada</small></font>";
        $morosidad=false;
        }

$tiempo_desde_emision=(strtotime(date("Y-m-d"))-strtotime($datos_factura["fecha_emision"]))/86400;

$morosidad_actual=0;

                if ($datos_factura["forma_de_pago"]==1) {
                        $forma_de_pago="Domiciliación bancaria<br>Cuenta: ".$datos_factura["banco_cuenta"];
                        if (($tiempo_desde_emision<=15) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>15) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>30) {
                                $morosidad_actual=4;
                                }
                        } else {
                        $forma_de_pago="Contado";
                        if ($datos_factura["forma_de_pago"]==5) {
                                $forma_de_pago="Cheque guardería";
                                }
                        if (($tiempo_desde_emision<=5) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>5) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>10) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=4;
                                }
                        }

if ($morosidad==true) {
        if ($morosidad_actual>=$nivel_maximo_morosidad) {
                $nivel_maximo_morosidad=$morosidad_actual;
                }
        $texto_morosidad="<br>".$morisidades[$morosidad_actual];
        } else {
        $texto_morosidad="";
        }

$total_cobrado=$total_cobrado+round($total_a_cuenta,2);
$total_absoluto=$total_absoluto+$total_factura;

$busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_factura["centro_factura"]);
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$nombre_centro=$datos_centro["nombre_centro"];
$prefijo_centro=$datos_centro["prefijo_centro"];
$razon_social=$datos_centro["razon_social"];
$domicilio=$datos_centro["domicilio_centro"];
$cp=$datos_centro["CP_centro"];
$poblacion=$datos_centro["poblacion_centro"];
$telefono=$datos_centro["telefono_centro"];
$fax=$datos_centro["fax_centro"];
$cif=$datos_centro["CIF_centro"];
}
                                  if ($datos_factura["serie"]!="EDU") {
                                        $num_factura="R".$datos_factura["id_factura"];
                                        } else {
                                        $num_factura=$datos_factura["num_factura"];
                                        }

                                ?>
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Nº de Factura</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Fecha de emisión</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Centro</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Asistencia en el periodo facturado</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Importe</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Forma de pago</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Estado</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        </tr>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_factura["serie"];?><?=$num_factura;?>/<?=$datos_factura["ejercicio_factura"];?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("j/n/Y",strtotime($datos_factura["fecha_emision"]));?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_centro;?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$total_dias_asistidos;?> días.</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_factura, 2, ',', '.');?> €</font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$forma_de_pago;?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$estado_factura;?><?=$texto_morosidad;?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:pagos_a_cuenta(<?=$datos_factura["id_factura"]?>)">Gestionar pagos a cuenta</a> | <a href="/pdf/factura.php?copia=1&id_factura=<?=$datos_factura["id_factura"]?>" target="_blank">Ver/Imprimir</a><!-- | <a href="/pdf/factura_unida.php?copia=1&id_factura=<?=$datos_factura["id_factura"]?>" target="_blank">Ver/Imprimir factura única </a><br><a href="/pdf/factura.php?id_factura=<?=$datos_factura["id_factura"]?>&dom=1" target="_blank">Imprimir con domicilo</a> <!--| <a href="/pdf/factura_unida.php?id_factura=<?=$datos_factura["id_factura"]?>&dom=1" target="_blank">Imprimir f. única con domicilo</a>--><?php 
                                  if (($nivel_acceso==3) or ($nivel_acceso==4)) {
                                  ?>
                                  <br><!-- <a href="editar_facturas1.php?id_factura=<?=$datos_factura["id_factura"]?>">Editar</a> | --><a href="javascript:eliminar(<?=$id_factura;?>)">Eliminar</a>
                                  <?php 
                                  }
                                  ?></font></td>
                                </tr>
                                <?php 
                                echo $lineas_detalles;
                                $contador=$contador+1;
                        }
                        ?>
                         </table>
                         <br>
                         <?php 
                         if ($nivel_acceso==4) {
                         ?>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Resumen <?=$ejercicio;?></b></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de Facturas emitidas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$contador;?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total facturado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_cobrado, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto-$total_cobrado, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total subvenciones:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_subvenciones, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_cobrado_subvenciones, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones pendiente de cobro:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*($total_subvenciones-$total_cobrado_subvenciones), 2, ',', '.');?> €</font></td>
                        </tr>
                        </table>
                        <?php 
                        }
                        ?>
                         <br>
                         <input type="button" value="Salir" onclick="window.close();" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php  require("../configuracion/parte_inferior.php"); ?>
