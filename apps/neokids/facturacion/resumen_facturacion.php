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
<?php  require("../configuracion/autentifica_admin.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
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
$id_aula_facturas=$_GET["id_aula_facturas"];

$id_curso_facturas=$_GET["curso_mostrado"];


if ($id_curso_facturas!=0) {
        $busca_datos_curso_mostrado=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_facturas));
        $datos_curso_mostrado=db_fetch_array($busca_datos_curso_mostrado);
        
        $filtros="year(facturas_emitidas.fecha_emision)>=".date("Y",strtotime($datos_curso_mostrado["fecha_inicio"]))." AND year(facturas_emitidas.fecha_emision)<=".date("Y",strtotime($datos_curso_mostrado["fecha_fin"]));
        $filtros_asistencia="year(servicios_consumidos.fecha)>=".date("Y",strtotime($datos_curso_mostrado["fecha_inicio"]))." AND year(servicios_consumidos.fecha)<=".date("Y",strtotime($datos_curso_mostrado["fecha_fin"]));

        }

if (strlen($ejercicio)!=0) {
        $filtros="year(facturas_emitidas.fecha_emision)=".$ejercicio;
        $filtros_asistencia="year(servicios_consumidos.fecha)=".$ejercicio;
        }

if (strlen($id_cliente)!=0) {
        $filtros=$filtros." AND facturas_emitidas.id_cliente=".$id_cliente;
        }
if (strlen($mes)!=0) {
        $filtros=$filtros." AND month(facturas_emitidas.fecha_emision)=".$mes;
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".$mes;
        }
if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }
if (strlen($id_aula_facturas)!=0) {
        $filtros=$filtros." AND alumnos.aula=".$id_aula_facturas;
        }
if ((strlen($id_curso_facturas)!=0) and ($id_curso_facturas!=0)) {
        $filtros=$filtros." AND alumnos.id_curso=".$id_curso_facturas;
        }
        
$filtros=$filtros.$filtro_facturas_sql;

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=13><font size=2 face="<?= $fuente_general; ?>"><b>Resumen de Facturación <?=$nombre_mes[$mes];?> <?=$ejercicio;?></b></font></td>
                        </tr>
                        <?php 
                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND $filtros ORDER BY alumnos.id_centro, alumnos.aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre DESC";                       

                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center" colspan=13><font size=2 face="<?= $fuente_general; ?>">No se han encontrado facturas emitidas con los criterios seleccionados...</font></td>
                        </tr>
                        <?php 
                        }

                        $contador=0;
                        $contador1=0;

                        $total_absoluto=0;
                        $total_cobrado=0;
                        $total_no_cobrado=0;
                        $total_subvenciones=0;
                        $total_cobrado_subvenciones=0;
                        $aula_actual=0;
                        $total_devoluciones=0;
                        $curso_actual=0;
                        $total_cobrado_absoluto=0;
						$total_subvenciones_proveedor=array();
						$total_subvenciones_pagadas_proveedor=array();
                        $meses_facturacion=array();

                        while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){

                        // Mira si los albaranes de la factura y calcula los totales
                        $id_factura=$datos_factura["id_factura"];


// Calcula totales

// Comprueba si ya tenemos los datos en la tabla factura

if (($datos_factura["total_factura"]!=0) and ($datos_factura["total_subvenciones"]==$datos_factura["total_subvenciones_cobrado"])) {
        $no_calcular=true;
        } else {
        $no_calcular=false;
        }

if ($no_calcular==true) {
        $total_factura=$datos_factura["total_factura"];
        $total_subvenciones=$total_subvenciones+$datos_factura["total_subvenciones"];
        $total_cobrado_subvenciones=$total_cobrado_subvenciones+$datos_factura["total_subvenciones_cobrado"];
        if (strlen($datos_factura["filtros_educacion"])!=0) {
                $filtros_asistencia=urldecode($datos_factura["filtros_educacion"]);
                }

        } else {

// Desde aqui

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (db_error());

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

$importe_subvenciones_factura=0;
$importe_subvenciones_cobradas_factura=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $concepto_linea=$datos_albaranes["concepto"];
        if (strlen($concepto_linea)!=0) {
                $concepto_linea=strtoupper($concepto_linea);
                }

        if ((is_numeric(strpos($concepto_linea,"EDUCACION"))) or (is_numeric(strpos($concepto_linea,"EDUCACIÓN")))) {

                $fecha_emision_factura=$datos_factura["fecha_emision"];
                $anio_factura=date("Y",strtotime($fecha_emision_factura));
                $mes_emision_factura=date("n",strtotime($fecha_emision_factura));

                $encontrado=false;

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
                        $encontrado=true;
                        break;
                        }
                }

                if ($encontrado==false) {
                        // Mes emision factura
                        $mes_buscado=$mes_emision_factura;
                        $nombre_mes_buscado=strtoupper($nombre_mes[$mes_buscado]);
                        $anio_buscado=$anio_factura;
                        if (is_numeric(strpos($concepto_linea,$nombre_mes_buscado." DE ".$anio_buscado))) {
                                $meses_facturacion[$contador_meses_facturacion]=$anio_buscado."-".$mes_buscado."-1";
                                $contador_meses_facturacion++;
                                $encontrado=true;
                                }
                        }
                        
                if ($encontrado==false) {
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
                                break;
                                }
                        }
                        }


                }

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

				if (substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") {
                                			$proveedor_actual=141;
                                			}                               
                if ((substr($datos_albaranes["concepto"],0,14)=="Cheque Escolar") or (substr($datos_albaranes["concepto"],0,15)=="Beca de Comedor")) {
                                			$proveedor_actual=120;                                			
                                			}         
                
        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $importe_subvenciones_factura=$importe_subvenciones_factura+$total_abs;
                $total_subvenciones_proveedor[$proveedor_actual]=$total_subvenciones_proveedor[$proveedor_actual]+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $importe_subvenciones_cobradas_factura=$importe_subvenciones_cobradas_factura+$total_abs;
                $total_subvenciones_pagadas_proveedor[$proveedor_actual]=$total_subvenciones_pagadas_proveedor[$proveedor_actual]+$total_abs;
                }
        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

if ($meses_facturacion[0]!="") {
        $filtros_asistencia="(month(fecha)=".date("n",strtotime($meses_facturacion[0]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[0])).")";
        for ($c_meses=1;$c_meses<=$contador_meses_facturacion;$c_meses++) {
                if ($meses_facturacion[$c_meses]!="") {
                        $filtros_asistencia=$filtros_asistencia." OR (month(fecha)=".date("n",strtotime($meses_facturacion[$c_meses]))." AND year(fecha)=".date("Y",strtotime($meses_facturacion[$c_meses])).")";
                        }
                }
        }

// Hasta aquí

// Introduce la info en la tabla para no tener que recalcular
$update_factura=db_query_params('UPDATE facturas_emitidas SET total_factura=?,total_subvenciones=?,total_subvenciones_cobrado=?,filtros_educacion=? WHERE id_factura=?', 'sssss', array((string)($total_factura), (string)($importe_subvenciones_factura), (string)($importe_subvenciones_cobradas_factura), (string)(urlencode($filtros_asistencia)), $id_factura)) or die (db_error());

}

$total_absoluto=$total_absoluto+$total_factura;

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
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Resumen <?=$nombre_mes[$mes];?> <?=$ejercicio;?></b></font></td>
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
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total subvenciones:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_subvenciones, 2, ',', '.');?> €</font></td>
                        </tr>
<tr><td colspan="2">&nbsp;</td></tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_proveedor[120]*-1,2,",",".");?> €</font></td>
                                </tr>
                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe cobrado subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_pagadas_proveedor[120]*-1,2,",",".");?> €</font></td>
                                </tr>                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($total_subvenciones_proveedor[120]-$total_subvenciones_pagadas_proveedor[120])*-1,2,",",".");?> €</font></td>
                                </tr> 
                        
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Total subvenciones Conselleria Educación <?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_proveedor[141]*-1,2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe cobrado subvenciones Conselleria Educación</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_pagadas_proveedor[141]*-1,2,",",".");?> €</font></td>
                                </tr>                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro subvenciones Conselleria Educación</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($total_subvenciones_proveedor[141]-$total_subvenciones_pagadas_proveedor141)*-1,2,",",".");?> €</font></td>
                                </tr> 
                        </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='seleccion_facturas.php';" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
                        </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='seleccion_facturas.php';" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table><?php 
}
?><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php  require("../configuracion/parte_inferior.php"); ?>
