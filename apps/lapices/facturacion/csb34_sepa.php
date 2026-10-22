<?php
require_once __DIR__ . '/../configuracion/deployment-config.php';  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 

// FUNCIONES

function quitar_tildes($cadena) {
	
	$no_permitidas = array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","À","Ã","Ì","Ò","Ù");		
	$permitidas    = array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U");
	
	$texto = str_replace($no_permitidas, $permitidas ,$cadena);
	
	return $texto;
	
}

// Datos Cabecera

$dia_cobro=$_GET["fechacobro_dia"];
$mes_cobro=$_GET["fechacobro_mes"];
$ano_cobro=$_GET["fechacobro_ano"];

$total_recibos=0;
$total_domiciliaciones=0;
$total_registros=0;

// Ordenante

$nif_ordenante="B97324123";
$sufijo_ordenante="000";
$fecha_creacion=date("Ymd");
$fecha_emision=date("Ymd",strtotime($ano_cobro."-".$mes_cobro."-".$dia_cobro));
$tipo_cuenta_ordenante="A"; // A=IBAN
$nombre_ordenante=str_pad("ZAEBSAL, S.L.", 70);
$entidad_receptora_ordenante=lapices_bank_setting('LAPICES_BANK_CSB34_SEPA_ENTIDAD_RECEPTORA_ORDENANTE', 4);
$oficina_receptora_ordenante=lapices_bank_setting('LAPICES_BANK_CSB34_SEPA_OFICINA_RECEPTORA_ORDENANTE', 4);
$dc_receptora_ordenante=lapices_bank_setting('LAPICES_BANK_CSB34_SEPA_DC_RECEPTORA_ORDENANTE', 2);
$cuenta_receptora_ordenante=lapices_bank_setting('LAPICES_BANK_CSB34_SEPA_CUENTA_RECEPTORA_ORDENANTE', 10);

$iban_ordenante=generarDCInToIban(calcularIban( $entidad_receptora_ordenante, $oficina_receptora_ordenante, $dc_receptora_ordenante, $cuenta_receptora_ordenante ));

$cabecera_ordenante=strpad("01ORD34145001".$nif_ordenante.$sufijo_ordenante.$fecha_creacion.$fecha_emision.$tipo_cuenta_ordenante.$iban_ordenante."1".$nombre_ordenante,600);

$cabecera_ordenante.=chr(13).chr(10);

$total_registros++;

// BLOQUE TRANSFERENCIAS SEPA

$registro_de_cabecera=strpad("02SCT34145".$nif_ordenante.$sufijo_ordenante,600);

$registro_de_cabecera.=chr(13).chr(10);

$total_registros++;

// Registros individuales

$registros_individuales="";

// Datos
$array_facturas=$_GET["facturas"];

if (is_numeric(strpos($array_facturas,","))) {
        $facturas=explode(",",$array_facturas);
        $filtro_facturas="";

        for ($contador_facturas=0;$contador_facturas<count($facturas);$contador_facturas++) {
                $filtro_facturas=$filtro_facturas." OR facturas_emitidas.id_factura=".$facturas[$contador_facturas];
                }

        $filtro_facturas=" AND (".substr($filtro_facturas,4,strlen($filtro_facturas)-4).")";
        } else {
        $facturas=array();
        $facturas[0]=$array_facturas;

        $filtro_facturas=" AND (facturas_emitidas.id_factura=".$array_facturas.")";

        }

// Busca datos de la factura
$consulta_factura="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario".$filtro_facturas." ORDER BY alumnos.banco_cuenta";

$consulta_datos_factura = db_query($consulta_factura) or die (db_error()."-1");

while($datos_de_factura=db_fetch_array($consulta_datos_factura)) {

$id_factura=$datos_de_factura["id_factura"];

// Introduce fecha de cobro
$actualiza_fecha=db_query("UPDATE facturas_emitidas SET fecha_orden_cobro='".$ano_cobro."-".$mes_cobro."-".$dia_cobro."' WHERE id_factura=$id_factura");

// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (odbc_errormsg()."-2");

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
        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

// Busca pagos a cuenta y descuenta del total_factura
$busca_pagos=db_query("SELECT * FROM pagos_a_cuenta WHERE id_factura=$id_factura");

if (db_num_rows($busca_pagos)!=0) {
	
	$total_a_cuenta=0;	
	
	while ($pagos=db_fetch_array($busca_pagos)) {
		$total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
	}
	$total_cobrado=round($total_a_cuenta,2);
	$total_factura=$total_factura-$total_cobrado;
}

// Datos del centro y del aula
$busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_de_factura["centro_factura"]);
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$prefijo_centro=$datos_centro["prefijo_centro"];
}

// Datos del aula
$nombre_aula="";
$busca_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_de_factura["aula"]);
if (db_num_rows($busca_aula)!=0) {
$datos_aula=db_fetch_array($busca_aula);
$nombre_aula=$datos_aula["nombre"];
}

$referencia_cliente=$datos_de_factura["banco_dni_titular"];
if (strlen($referencia_cliente)!=0) {
        $referencia_cliente=str_replace(" ","",$referencia_cliente);
        $referencia_cliente=str_replace(".","",$referencia_cliente);
        $referencia_cliente=str_replace("-","",$referencia_cliente);
        } else {
        $referencia_cliente=$datos_de_factura["id_alumno"];
        }

$referencia_cliente="000000000000".$referencia_cliente;
$referencia_cliente=substr($referencia_cliente,strlen($referencia_cliente)-12,12);

$nombre_cliente=strpad($datos_de_factura["banco_titular"],0,70);

// Numero de cuenta
$numero_de_cuenta=$datos_de_factura["banco_cuenta"];
$numero_de_cuenta=str_replace(" ","",$numero_de_cuenta);
$entidad_receptora_cliente=substr($numero_de_cuenta,0,4);
$oficina_receptora_cliente=substr($numero_de_cuenta,4,4);
$dc_receptora_cliente=substr($numero_de_cuenta,8,2);
$cuenta_receptora_cliente=substr($numero_de_cuenta,10,10);

$iban_receptor=generarDCInToIban(calcularIban( $entidad_receptora_cliente, $oficina_receptora_cliente, $dc_receptora_cliente, $cuenta_receptora_cliente ));

$bic_receptor="";

if ($entidad_receptora_cliente) {

	$busca_bic=db_query("SELECT * FROM bic_swift WHERE cod_bancario='".$entidad_receptora_cliente."'");
	
	if (db_num_rows($busca_bic)!=0) {
	
		$datos_bic=db_fetch_array($busca_bic);
		$bic_receptor=$datos_bic["BIC"];
	
	}

}

$bic_receptor=strpad($bic_receptor,11);

$referencia_ordenante=strpad($id_factura,35);

$importe_recibo=$total_factura;
$total_recibos=$total_recibos+$importe_recibo;

$importe_recibo="00000000000".number_format($importe_recibo,2,"","");
$importe_recibo=str_replace(".","",$importe_recibo);
$importe_recibo=substr($importe_recibo,strlen($importe_recibo)-11,11);

$opcional1_receptor=" ";
$opcional1_receptor=strpad($opcional1_receptor,142);

$concepto_ordenante="Bono infantil ".$datos_de_factura["Nombre"]." ".$datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"];
$concepto_ordenante=strpad($concepto_ordenante,140);

$registro_individual="03SCT34145002".$referencia_ordenante."A".$iban_receptor.$importe_recibo."3".$bic_receptor.$nombre_cliente.$opcional1_receptor;
$registro_individual=strpad($registro_individual,600);
$registro_individual.=chr(13).chr(10);

$total_registros++;

$total_domiciliaciones++;

$registros_individuales=$registros_individuales.$registro_individual;

}

// Totales Trasferencias

$total_recibos="00000000000000000".number_format($total_recibos,2,"","");
$total_recibos=str_replace(".","",$total_recibos);
$total_recibos=substr($total_recibos,strlen($total_recibos)-17,17);

$total_domiciliaciones="00000000".$total_domiciliaciones;
$total_domiciliaciones=substr($total_domiciliaciones,strlen($total_domiciliaciones)-8,8);

$total_registros_bloque="0000000000".($total_registros);
$total_registros_bloque=substr($total_registros_bloque,strlen($total_registros_bloque)-10,10);

$registro_totales="04SCT".$total_recibos.$total_domiciliaciones.$total_registros_bloque;
$registro_totales=strpad($registro_totales,600);
$registro_totales.=chr(13).chr(10);

$total_registros++;

// Totales general

$total_registros++;

$total_registros_general="0000000000".$total_registros;
$total_registros_general=substr($total_registros_general,strlen($total_registros_general)-10,10);

$registro_totales_gen="99ORD".$total_recibos.$total_domiciliaciones.$total_registros_general;
$registro_totales_gen=strpad($registro_totales_gen,600);
$registro_totales_gen.=chr(13).chr(10);

// Grabación archivo

$archivo_sepa34=$cabecera_ordenante.$registro_de_cabecera.$registros_individuales.$registro_totales.$registro_totales_gen;

$nombre_archivo=date("dmyHis").".csb";
$nombre_nuevo_archivo="$web_path/recibos/$nombre_archivo";
$nombre_archivo_web="$url_web/recibos/$nombre_archivo";

if (file_exists($nombre_nuevo_archivo)) {
        if (is_writable($nombre_nuevo_archivo)) {
                                unlink ($nombre_nuevo_archivo);
                }
        }

$gestor_nuevo = fopen($nombre_nuevo_archivo,"w+");
fwrite($gestor_nuevo,$archivo_sepa34);
fclose($gestor_nuevo);

// header("Location: ".$nombre_archivo_web);
?>
<html>
<head>
<script language="Javascript">
self.focus();
self.opener.history.go(0);
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center><br><font size=2 face=Arial><b>Exportación de recibos<br><br></b></font>
<font size=2 face=Arial>
<a href="<?=$nombre_archivo_web;?>"><b>Descargar Fichero</b></a><br><br>
Para guardar el fichero en el disco duro de tu ordenador haz clic sobre el hipervínculo con el botón derecho del ratón y selecciona<br><i>"Guardar destino como..."
</font>
</div>
</body>
</html>
