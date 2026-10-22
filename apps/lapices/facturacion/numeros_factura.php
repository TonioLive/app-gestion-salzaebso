<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$ejercicio_factura=$_GET["e"];
$pagina=$_GET["pagina"];
$serie_factura=$_GET["serie"];

if (strlen($serie_factura)==0) {
        $serie_factura="EDU";
        }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php  

if (strlen($serie_factura)==0) {
?>
<font size=3><b>
Selecciona la serie<br>de las nuevas facturas</b>
<br><br><br>
<a href="<?=$_SERVER['PHP_SELF']?>?e=<?=$ejercicio_factura?>&serie=SS">Sin serie</a><br><br>
<a href="<?=$_SERVER['PHP_SELF']?>?e=<?=$ejercicio_factura?>&serie=EDU">Educación (EDU)y Otros Servicios (OS)</a><br><br>
<?php 
} else {

$num_filas=6;
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Selecciona el número de factura entre los disponibles</b></small></small></td>
  </tr>
<?php  
$fecha=strftime("%m/%d/%Y");

mt_srand((double)microtime()*1000000);

if ($serie_factura=="SS") {
$filtro_serie="";
$texto_series="";
} else {
$filtro_serie=" AND serie='$serie_factura'";
$texto_series=" en la serie $serie_factura";
}

// Busca el ultimo numero de factura
$consulta1="SELECT Max(num_factura) AS Expr1 FROM facturas_emitidas WHERE ejercicio_factura='".$ejercicio_factura."'$filtro_serie";
$busca_ultima_factura = db_query($consulta1) or die (db_error());

$numero_fact=db_result($busca_ultima_factura, 0, "Expr1");

if ($numero_fact!="") {

$fila_actual=1;

$num_facturas=100;

if ($numero_fact>=$num_facturas) {
        $limite_inferior=$numero_fact-$num_facturas;
        } else {
        $limite_inferior=1;
        }

if (strlen($pagina)==0) {
        $limite_superior=$numero_fact+1;
        } else {
        $limite_superior=($numero_fact+1)-($num_facturas*$pagina);
        $limite_inferior=$limite_superior-$num_facturas-1;
        if ($limite_inferior<=0) {
                $limite_inferior=1;
                }
        }
        

for ($n_fact=$limite_superior; $n_fact>=$limite_inferior; $n_fact=$n_fact-1)
{
if ($fila_actual==1) {  print "<tr>"; }
$n_fact_completo="000000".$n_fact;
$n_fact_completo=substr($n_fact_completo,(strlen($n_fact_completo)-5),5);

$consulta2="SELECT * FROM facturas_emitidas WHERE num_factura=".$n_fact." AND ejercicio_factura='".$ejercicio_factura."'$filtro_serie";

$count = db_query ($consulta2) or die (db_error());
$numRecords = db_num_rows ($count);

if ($numRecords==0) {
        $parte1="<a href=\"javascript:top.window.opener.document.albaranes.num_factura.value='".$n_fact_completo."';self.close();\">";
        $parte2="</a>";
        } else {
        $parte1="<font color='999999'>";
        $parte2="</font>";
        }
?>
<td align="center"><?=$parte1;?><font size=1 face="<?=$fuente_general;?>"><?=$n_fact_completo;?></font><?=$parte2;?></td>
<?php 
if ($fila_actual==$num_filas) {
                              print "</tr>";
                              $fila_actual=0;
                              }
$fila_actual=$fila_actual+1;
}

} else {
?>
  <tr bgcolor="#FFFFFF">
    <td colspan=<?=$num_filas;?> align="center"><br><br><small><small><b>Todavia no se ha emitido ninguna factura<br>en el ejercicio <?=$ejercicio_factura;?><?=$texto_series?><br><br><a href="javascript:top.window.opener.document.albaranes.num_factura.value='00001';self.close();">Haz clic aquí para introducir la primera</a></b></small></small><br><br></td>
  </tr>
<?php 

}

?>
</table>
<?php 
if ($pagina>0) {
?>
<a href="numeros_factura.php?serie=<?=$serie_factura;?>&e=<?=$ejercicio_factura?>&pagina=<?=$pagina-1;?>"><< Página anterior</a> &nbsp;&nbsp;&nbsp;
<?php 
}
if ($limite_inferior!=1) {
?>
&nbsp;&nbsp;&nbsp; <a href="numeros_factura.php?serie=<?=$serie_factura;?>&e=<?=$ejercicio_factura?>&pagina=<?=$pagina+1;?>">Página siguiente >></a>
<?php 
}

}
?>
</center></div>
</div>
</body>
</html>

