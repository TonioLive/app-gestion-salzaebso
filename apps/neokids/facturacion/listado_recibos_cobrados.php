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

$fechaSeleccionada=$_GET['fecha_seleccionada'];

if (strlen($fechaSeleccionada)==0) {
    $fechaSeleccionada=strtotime(date("Y-m-d"));
}

$fechaManana = strtotime("+1 day", $fechaSeleccionada);
$fechaAyer = strtotime("-1 day", $fechaSeleccionada);
?>
<?php  require("../configuracion/autentifica.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<a href="listado_recibos_cobrados.php?fecha_seleccionada=<?=$fechaManana?>" class="btn btn-md btn-primary pull-right">Avanzar un día ></a>
<a href="listado_recibos_cobrados.php?fecha_seleccionada=<?=$fechaAyer?>" class="btn btn-md btn-primary">< Retroceder un día</a>    
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
        }
        
if (strlen($mes)!=0) {
        $filtros=$filtros." AND month(facturas_emitidas.fecha_emision)=".$mes;
        }
if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }

$filtros=$filtros." AND date(facturas_emitidas.hora_entrega_recibo)='".date("Y-m-d",$fechaSeleccionada)."'";


$filtros=$filtros.$filtro_facturas_sql;
?>
<script language="Javascript">
function eliminar(id_factura) {
        if (confirm("Se eliminará la factra seleccionada.\n\n¿Estas seguro?")) {
                top.window.location.href='eliminar_factura_alumno.php?origen=_modal&centro_factura=<?=$centro_factura?>&id_cliente=<?=$id_cliente?>&ejercicio=<?=$ejercicio?>&mes=<?=$mes?>&id_factura='+id_factura;
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
<form name="filtro_f" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>    
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <table border=1 bordercolor="#fff" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=8><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Recibos firmados el día <?=date("d/m/Y",$fechaSeleccionada)?></b><br>
                           <small>Puedes imprimir o gestionar los pagos de los recibos haciendo clic en los vínculos que encontrarás en cada línea.<br></small></font></td>
                        </tr>
                        <?php 
                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario$filtros ORDER BY fecha_emision DESC";

                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());
                        
                        //$consulta1="SELECT facturas_emitidas.ejercicio_factura,facturas_emitidas.serie,facturas_emitidas.id_factura,facturas_emitidas.num_factura,facturas_emitidas.fecha_emision,usuarios.id_usuario,alumnos.forma_de_pago,alumnos.banco_cuenta, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario$filtros AND facturas_emitidas.tipo_cliente=0 ORDER BY fecha_emision DESC";
			// $consulta2="SELECT facturas_emitidas.ejercicio_factura,facturas_emitidas.serie,facturas_emitidas.id_factura,facturas_emitidas.num_factura,facturas_emitidas.fecha_emision,usuarios.id_usuario,alumnos.forma_de_pago,alumnos.banco_cuenta, alumnos.id_centro AS centro_factura FROM facturas_emitidas, $baseDatosOrigenExternos.alumnos, $baseDatosOrigenExternos.usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario$filtros AND facturas_emitidas.tipo_cliente=1 ORDER BY fecha_emision DESC";
												
                        // $consulta_albaranes_cliente = mysql_query("(".$consulta1.") UNION (".$consulta2.")") or die (mysql_error());
                        
                        // $consulta_albaranes_cliente = mysql_query($consulta1) or die (mysql_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center" colspan=8><font size=2 face="<?= $fuente_general; ?>">No se han encontrado recibos con los criterios seleccionados...</font></td>
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
                        
                            $nombre_cliente_facturas=$datos_factura["banco_titular"];
                            $dni_cliente_facturas=$datos_factura["banco_dni_titular"];
                            $nombre_alumno_facturas=$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"].", ".$datos_factura["Nombre"];
                            


                        // Mira si los albaranes de la factura y calcula los totales
                        $id_factura=$datos_factura["id_factura"];
// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura));

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

$total_factura=round(($total_base+$total_iva+$total_recargo),4);
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

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));

if (db_num_rows($busca_pagos)!=0) {

        while ($pagos=db_fetch_array($busca_pagos)) {
                $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
        if ($total_a_cuenta<($total_base+$total_iva+$total_recargo)) {
                if ($total_a_cuenta>0) {
                        $estado_factura="<font color=red><small>Pendiente cobro: ".number_format(($total_factura-$total_a_cuenta), 2, ',', '.')." €</small></font>";
                } else {
                        $estado_factura="<font color=red><small>No cobrada</small></font>";
                }
                        $morosidad=true;
        } else {
                $estado_factura="<font color=green><small>Cobrada</small></font>";
                $morosidad=false;
        }

        } else {
        if (($total_base+$total_iva+$total_recargo)<0) {
                $estado_factura="<font color=blue><small>No abonada</small></font>";
        }
        
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

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
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

                                $lista_grupos_alumno="";

                                $tablaGruposAlumnos="aulas_alumnos_grupos";
                                $busca_alumnos_grupo=db_query("SELECT * FROM usuarios,$tablaGruposAlumnos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND $tablaGruposAlumnos.id_alumno=alumnos.id_alumno AND $tablaGruposAlumnos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=".$datos_factura["id_cliente"]) or die(db_error());

                                while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                                    $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).", ";
                                }
                                
                                if (strlen($lista_grupos_alumno)!=0) {
                                    $lista_grupos_alumno=substr($lista_grupos_alumno,0,strlen($lista_grupos_alumno)-2);
                                }

                                ?>
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center"><small><b>Nº de Factura</b></small></td>
                          <td align="center"><small><b>Fecha de emisión</b></small></td>
                          <td align="center"><small><b>Alumno</b></small></td>
                          <td align="center"><small><b>Grupo</b></small></td>
                          <td align="center"><small><b>Importe</b></small></td>
                          <td align="center"><small><b>Forma de pago</b></small></td>
                          <td align="center"><small><b>Estado</b></small></td>
                          <td align="center"><small><b>&nbsp;</b></small></td>
                        </tr>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="center"><small><?=$datos_factura["serie"];?><?=$num_factura;?>/<?=$datos_factura["ejercicio_factura"];?></small></td>
                                  <td align="center"><small><?=date("j/n/Y",strtotime($datos_factura["fecha_emision"]));?></b></small></td>
                                  <td align="center"><small><?=$nombre_alumno_facturas;?></small></td>
                                  <td align="center"><small><?=$lista_grupos_alumno;?> días.</td>
                                  <td align="right"><small><?=number_format($total_factura, 2, ',', '.');?> €</small></td>
                                  <td align="center"><small><?=$forma_de_pago;?></small></td>
                                  <td align="center"><small><?=$estado_factura;?><?=$texto_morosidad;?></small></td>
                                  <td align="center">
                                  	<button onclick="pagos_a_cuenta(<?=$datos_factura["id_factura"]?>)" type="button" class="btn btn-default btn-xs btn-xs btn-primary">Pagos a cuenta</button>

                                  	<a href="/pdf/recibos_material.php?id_factura=<?=$datos_factura["id_factura"]?>" target="_blank" type="button" class="btn btn-default btn-xs btn-xs btn-primary">RECIBO</a> <a href="/pdf/recibos_material.php?id_factura=<?=$datos_factura["id_factura"]?>&num_vencimientos=2" target="_blank" type="button" class="btn btn-default btn-xs btn-xs btn-primary">RECIBO 2 PAGOS</a>
                                  	<button onclick="if (confirm('Los originales solo se deben imprimir si se ha producido un error en la anterior impresión, si no es así siempre se deben imprimir copias.\n\n¿Quieres continuar con la impresión de los originales? En caso afirmativo pulsa el botón Aceptar')) { window.open('/pdf/recibos_material.php?original=1&id_factura=<?=$datos_factura["id_factura"]?>'); }" type="button" class="btn btn-default btn-xs btn-xs btn-warning">RECIBO ORIGINAL</button>
                                  	
                                  	<button onclick="if (confirm('Los originales solo se deben imprimir si se ha producido un error en la anterior impresión, si no es así siempre se deben imprimir copias.\n\n¿Quieres continuar con la impresión de los originales? En caso afirmativo pulsa el botón Aceptar')) { window.open('/pdf/recibos_material.php?num_vencimientos=2&original=1&id_factura=<?=$datos_factura["id_factura"]?>'); }" type="button" class="btn btn-default btn-xs btn-xs btn-warning">ORIGINAL 2 PAGOS</button>
                                  	
                                  	<a href="/pdf/factura.php?copia=1&id_factura=<?=$datos_factura["id_factura"]?>" target="_blank" type="button" class="btn btn-default btn-xs btn-primary">Albarán o Factura</a><?php 
                                  if (($nivel_acceso==3) or ($nivel_acceso==4)) {
                                  ?>
                                  <a href="javascript:eliminar(<?=$id_factura;?>)" type="button" class="btn btn-default btn-xs btn-danger">&nbsp;<i class="fa fa-trash"></i>&nbsp;</a>
                                  <?php 
                                  }
                                  ?></td>
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
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de recibos firmados:</font></td>
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
                        </table>
                        <?php 
                        }
                        ?>
                </td>
        </tr>
</table><br><br>
</form>
</div>

</body>
</html>