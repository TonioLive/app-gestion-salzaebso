<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 
// Datos Cabecera

$dia_cobro=$_GET["fechacobro_dia"];
$mes_cobro=$_GET["fechacobro_mes"];
$ano_cobro=$_GET["fechacobro_ano"];

// Presentador

$nif_presentador="ES03001B97324123";
$nombre_presentador="ZAEBSAL, S.L.";
$IBAN = (getenv('ZEIM_BANK_IBAN_01') ?: throw new RuntimeException('Configure ZEIM_BANK_IBAN_01 before generating remittances.'));
$BIC_presentador = "BSCHESMMXXX";
	     
// Registros individuales

$total_recibos=0;
$total_domiciliaciones=0;

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
$consulta_factura="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura, alumnos.fecha_inscripcion AS DtOfSgntr FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario".$filtro_facturas." ORDER BY alumnos.banco_cuenta";

$consulta_datos_factura = db_query($consulta_factura) or die (db_error()."-1");

while($datos_de_factura=db_fetch_array($consulta_datos_factura)) {

$id_factura=$datos_de_factura["id_factura"];

// Introduce fecha de cobro
$actualiza_fecha=db_query_params('UPDATE facturas_emitidas SET fecha_orden_cobro=? WHERE id_factura=?', 'ss', array((string)($ano_cobro) . '-' . (string)($mes_cobro) . '-' . (string)($dia_cobro), $id_factura));

// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg()."-2");

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
$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));

if (db_num_rows($busca_pagos)!=0) {
	
	$total_a_cuenta=0;	
	
	while ($pagos=db_fetch_array($busca_pagos)) {
		$total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
	}
	$total_cobrado=round($total_a_cuenta,2);
	$total_factura=$total_factura-$total_cobrado;
}

// Datos del centro y del aula
$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_de_factura["centro_factura"]));
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$prefijo_centro=$datos_centro["prefijo_centro"];
}

// Datos del aula
$nombre_aula="";
$busca_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_de_factura["aula"]));
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

$nombre_cliente=$datos_de_factura["banco_titular"];
$nombre_cliente=substr($nombre_cliente,0,70);

// Numero de cuenta
$numero_de_cuenta=$datos_de_factura["banco_cuenta"];
$numero_de_cuenta=str_replace(" ","",$numero_de_cuenta);
$entidad_receptora_cliente=substr($numero_de_cuenta,0,4);
$oficina_receptora_cliente=substr($numero_de_cuenta,4,4);
$dc_receptora_cliente=substr($numero_de_cuenta,8,2);
$cuenta_receptora_cliente=substr($numero_de_cuenta,10,10);

// Calcula IBAN Cliente
$IBAN_cliente = generarDCInToIban(calcularIban( $entidad_receptora_cliente, $oficina_receptora_cliente, $dc_receptora_cliente, $cuenta_receptora_cliente ));

$importe_recibo=$total_factura;
$total_recibos=$total_recibos+$importe_recibo;

$importe_recibo=number_format($importe_recibo,2,".","");

$codigo_devolucion="000000";
$codigo_interno="0000000000";

$Ustrd="ZEIM ESC. INFANTILES FACT. ".$datos_de_factura["serie"].$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"];
$Ustrd.=" Alumno: ".$datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"].", ".$datos_de_factura["Nombre"];
$Ustrd.=" ".$nombre_aula;
$Ustrd=substr($Ustrd,0,140); // Long. Máx. 140 caracteres

$DtOfSgntr=date("Y-m-d",strtotime($datos_de_factura["DtOfSgntr"]));

$total_domiciliaciones++;
/*			
$InstrId = "0000000000000000".$id_factura;
$InstrId = substr($InstrId,strlen($InstrId)-16,16);
*/
$InstrId = "0000000000000000";

// Busca BIC Cliente
$buscaBic=db_query("SELECT * FROM bic_swift WHERE cod_bancario=".$entidad_receptora_cliente);

if (db_num_rows($buscaBic)!=0) {
	$datosBIC = db_fetch_array($buscaBic);
	$BIC_cliente = $datosBIC["BIC"];
} else {
	$BIC_cliente = "NOTPROVIDED";
}



$registros_individuales .= <<<EOD

      <DrctDbtTxInf>
        <PmtId>
          <InstrId>$InstrId</InstrId>
          <EndToEndId>$referencia_cliente</EndToEndId>
        </PmtId>
        <InstdAmt Ccy="EUR">$importe_recibo</InstdAmt>
        <DrctDbtTx>
          <MndtRltdInf>
            <MndtId>$referencia_cliente</MndtId>
            <DtOfSgntr>$DtOfSgntr</DtOfSgntr>
          </MndtRltdInf>
          <CdtrSchmeId>
            <Id>
              <PrvtId>
                <Othr>
                  <Id>$nif_presentador</Id>
                  <SchmeNm>
                    <Prtry>SEPA</Prtry>
                  </SchmeNm>
                </Othr>
              </PrvtId>
            </Id>
          </CdtrSchmeId>
        </DrctDbtTx>
        <DbtrAgt>
          <FinInstnId>
			<BIC>$BIC_cliente</BIC>
		  </FinInstnId>
        </DbtrAgt>
        <Dbtr>
          <Nm>$nombre_cliente</Nm>
        </Dbtr>
        <DbtrAcct>
          <Id>
            <IBAN>$IBAN_cliente</IBAN>
          </Id>
        </DbtrAcct>
        <RmtInf>
          <Ustrd>$Ustrd</Ustrd>
        </RmtInf>
      </DrctDbtTxInf>
      
EOD;

}

// Totales
$CreDtTm=date("Y-m-d")."T".date("H:i:s");
$total_recibos=number_format($total_recibos,2,".","");

// Datos Cabecera

$dia_cobro=$_GET["fechacobro_dia"];
$mes_cobro=$_GET["fechacobro_mes"];
$ano_cobro=$_GET["fechacobro_ano"];

// Ordenante

$nif_ordenante="B97324123";
$fecha_ordenante=date("dmy");
$fecha_cargo_ordenante=date("Y-m-d",strtotime($ano_cobro."-".$mes_cobro."-".$dia_cobro));
$nombre_ordenante="ZAEBSAL, S.L.";

// PRE2018050212422442175000B97324123

$MsgId = "PRE".date("Ymdhis")."000B97324123";
$PmtInfId = $nif_ordenante."00000".date("YmdHis");


$cabecera = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<Document xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02">
  <CstmrDrctDbtInitn>
    <GrpHdr>
      <MsgId>$MsgId</MsgId>
      <CreDtTm>$CreDtTm</CreDtTm>
      <NbOfTxs>$total_domiciliaciones</NbOfTxs>
      <CtrlSum>$total_recibos</CtrlSum>
      <InitgPty>
        <Nm>$nombre_ordenante</Nm>
        <Id>
          <PrvtId>
            <Othr>
              <Id>$nif_presentador</Id>
            </Othr>
          </PrvtId>
        </Id>
      </InitgPty>
    </GrpHdr>
    <PmtInf>
      <PmtInfId>$PmtInfId</PmtInfId>
      <PmtMtd>DD</PmtMtd>
      <NbOfTxs>$total_domiciliaciones</NbOfTxs>
      <CtrlSum>$total_recibos</CtrlSum>
      <PmtTpInf>
        <SvcLvl>
          <Cd>SEPA</Cd>
        </SvcLvl>
        <LclInstrm>
          <Cd>CORE</Cd>
        </LclInstrm>
        <SeqTp>RCUR</SeqTp>
      </PmtTpInf>
      <ReqdColltnDt>$fecha_cargo_ordenante</ReqdColltnDt>
      <Cdtr>
        <Nm>$nombre_presentador</Nm>
      </Cdtr>
      <CdtrAcct>
        <Id>
          <IBAN>$IBAN</IBAN>
        </Id>
      </CdtrAcct>
      <CdtrAgt>
        <FinInstnId>
			<BIC>$BIC_presentador</BIC>
		</FinInstnId>
      </CdtrAgt>
      <CdtrSchmeId>
        <Id>
          <PrvtId>
            <Othr>
              <Id>$nif_presentador</Id>
              <SchmeNm>
                <Prtry>SEPA</Prtry>
              </SchmeNm>
            </Othr>
          </PrvtId>
        </Id>
      </CdtrSchmeId>
      
EOD;

$pie = <<<EOD

    </PmtInf>
  </CstmrDrctDbtInitn>
</Document>

EOD;

$archivo_xml=utf8_encode($cabecera.$registros_individuales.$pie);

$nombre_archivo=date("dmyHis").".xml";
$nombre_nuevo_archivo="$web_path/recibos/$nombre_archivo";
$nombre_archivo_web="$url_web/recibos/$nombre_archivo";

if (file_exists($nombre_nuevo_archivo)) {
        if (is_writable($nombre_nuevo_archivo)) {
                                unlink ($nombre_nuevo_archivo);
                }
        }

$gestor_nuevo = fopen($nombre_nuevo_archivo,"w+");
fwrite($gestor_nuevo,$archivo_xml);
fclose($gestor_nuevo);

// header("Location: ".$nombre_archivo_web);
?>
<html>
<head>
<script language="Javascript">
self.focus();
self.opener.history.go(0);
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center><br><font size=2 face=Arial><b>Exportación de recibos<br><br></b></font>
<font size=2 face=Arial>
<a href="<?=$nombre_archivo_web;?>"><b>Descargar Fichero</b></a><br><br>
Para guardar el fichero en el disco duro de tu ordenador haz clic sobre el hipervínculo con el botón derecho del ratón y selecciona<br><i>"Guardar destino como..."
</font>
</div>
</body>
</html>
