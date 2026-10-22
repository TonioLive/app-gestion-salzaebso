<?php
require_once __DIR__ . '/../configuracion/deployment-config.php';
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 
// Datos Cabecera

$dia_cobro=$_GET["fechacobro_dia"];
$mes_cobro=$_GET["fechacobro_mes"];
$ano_cobro=$_GET["fechacobro_ano"];

// Datos cliente
$numero_de_cuenta=$_GET["cuenta"];
$importe_total=$_GET["importe"];
$BIC_cliente=$_GET["bic"];
$razon_social_cliente=$_GET["razon_social"];
$cif_cliente=$_GET["cif"];
$concepto_factura=$_GET["concepto"];

// Presentador
$nif_presentador="ES03001B97324123";
$nombre_presentador="ZAEBSAL, S.L.";
$IBAN = lapices_bank_setting('LAPICES_BANK_CSB19SANTANDER_SEPA_FACTURA_UNICA_IBAN', 24);
$BIC_presentador = "BSCHESMMXXX";
	
// Ordenante

$nif_ordenante="B97324123";
$fecha_ordenante=date("dmy");
$fecha_cargo_ordenante=date("Y-m-d",strtotime($ano_cobro."-".$mes_cobro."-".$dia_cobro));
$nombre_ordenante="ZAEBSAL, S.L.";


// Registros individuales

$total_recibos=0;
$total_domiciliaciones=1;

$registros_individuales="";

$referencia_cliente=$cif_cliente;
if (strlen($referencia_cliente)!=0) {
        $referencia_cliente=str_replace(" ","",$referencia_cliente);
        $referencia_cliente=str_replace(".","",$referencia_cliente);
        $referencia_cliente=str_replace("-","",$referencia_cliente);
        }

$referencia_cliente="000000000000".$referencia_cliente;
$referencia_cliente=substr($referencia_cliente,strlen($referencia_cliente)-12,12);

$nombre_cliente=$razon_social_cliente;
$nombre_cliente=mb_substr($nombre_cliente, 0, 70, 'UTF-8');

// Numero de cuenta

// Quita espacios
$numero_de_cuenta=str_replace(" ","",$numero_de_cuenta);

/*
$entidad_receptora_cliente=substr($numero_de_cuenta,0,4);
$oficina_receptora_cliente=substr($numero_de_cuenta,4,4);
$dc_receptora_cliente=substr($numero_de_cuenta,8,2);
$cuenta_receptora_cliente=substr($numero_de_cuenta,10,10);
*/
// Calcula IBAN Cliente
// $IBAN_cliente = generarDCInToIban(calcularIban( $entidad_receptora_cliente, $oficina_receptora_cliente, $dc_receptora_cliente, $cuenta_receptora_cliente ));
$IBAN_cliente = $numero_de_cuenta;

$importe_recibo=$importe_total;
$total_recibos=$total_recibos+$importe_recibo;

$importe_recibo=number_format($importe_recibo,2,".","");

$codigo_devolucion="000000";
$codigo_interno="0000000000";

$Ustrd=$concepto_factura;
$Ustrd=mb_substr($Ustrd, 0, 140, 'UTF-8'); // Long. Máx. 140 caracteres

/*
$DtOfSgntr=date("Y-m-d",strtotime($datos_de_factura["DtOfSgntr"]));
*/

$DtOfSgntr=$fecha_cargo_ordenante;

/*			
$InstrId = "0000000000000000".$id_factura;
$InstrId = substr($InstrId,strlen($InstrId)-16,16);
*/
$InstrId = "0000000000000000";

// Busca BIC Cliente
/*
$buscaBic=mysql_query("SELECT * FROM bic_swift WHERE cod_bancario=".$entidad_receptora_cliente);

if (mysql_num_rows($buscaBic)!=0) {
	$datosBIC = mysql_fetch_array($buscaBic);
	$BIC_cliente = $datosBIC["BIC"];
} else {
	$BIC_cliente = "NOTPROVIDED";
}
*/

if ($BIC_cliente=="") {
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

// Totales
$CreDtTm=date("Y-m-d")."T".date("H:i:s");
$total_recibos=number_format($total_recibos,2,".","");

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

$archivo_xml=app_text_utf8($cabecera.$registros_individuales.$pie);

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
