<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_pedido=$_POST["id_pedido"];
$id_fila=$_POST["id_fila"];
$observaciones=$_POST["observaciones"];

$ejercicio=$_POST["ejercicio"];
$id_cliente=$_POST["id_cliente"];
$tipo_albaran=$_POST["tipo_albaran"];
$dia=$_POST["fecha_pedido_dia"];
$mes=$_POST["fecha_pedido_mes"];
$ano=$_POST["fecha_pedido_ano"];
$dia_e=$_POST["fecha_entrega_dia"];
$mes_e=$_POST["fecha_entrega_mes"];
$ano_e=$_POST["fecha_entrega_ano"];

$ID_LOTE=$_POST["lote"];
if (strlen($ID_LOTE)!=0) {
        $troso1="ID_LOTE, ";
        $troso2="$ID_LOTE, ";
        }
$ARTICULO=$_POST["cod"];
$CANTIDAD=$_POST["uds"];
$PRECIO=$_POST["pvp"];
if (($PRECIO>0) and (is_numeric($PRECIO))) {
        $PRECIO=number_format($PRECIO,2,".","");
        } else {
        $PRECIO=0;
        }
$DTO=$_POST["dto_porcentaje"];
if (($DTO>0) and (is_numeric($DTO))) {
        $DTO=round(($DTO/100),2);
        } else {
        $DTO=0;
        }
$TIPO_IVA=$_POST["iva_porcentaje"];
if (($TIPO_IVA>0) and (is_numeric($TIPO_IVA))) {
        $TIPO_IVA=round(($TIPO_IVA/100),2);
        } else {
        $TIPO_IVA=0;
        }
        
$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

if (strlen($id_fila)==0) {
        $consulta = "INSERT INTO DETALLES_PEDIDO (ID_PEDIDO, ".$troso1."ARTICULO, CANTIDAD, PRECIO, DTO, TIPO_IVA) VALUES (".$id_pedido.", ".$troso2."'$ARTICULO', $CANTIDAD, $PRECIO, $DTO, $TIPO_IVA);";
        } else {
        $consulta = "UPDATE DETALLES_PEDIDO SET ID_LOTE=$ID_LOTE, ARTICULO='$ARTICULO', CANTIDAD=$CANTIDAD, PRECIO=$PRECIO, DTO=$DTO, TIPO_IVA=$TIPO_IVA WHERE ID_DETALLE=".$id_fila.";";
        }

$inserta_pedido = odbc_exec($odbc, $consulta) or die (odbc_errormsg());

// Actualiza observaciones

$inserta_observaciones = odbc_exec($odbc, "UPDATE PEDIDOS_CLIENTES SET FECHA_PEDIDO='".$dia."/".$mes."/".$ano."', ID_CLIENTE='$id_cliente', FECHA_ENTREGA='".$dia_e."/".$mes_e."/".$ano_e."', tipo_albaran='$tipo_albaran', EJERCICIO='$ejercicio', OBSERVACIONES='$observaciones' WHERE ID_PEDIDO=$id_pedido") or die (odbc_errormsg());

odbc_close($odbc);

header("Location: editar_albaranes1.php?id_pedido=$id_pedido");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>
