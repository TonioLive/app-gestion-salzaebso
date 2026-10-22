<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$familia=$_POST["familia"];
$familia=$_POST["codigo_familia"];
$codigo_producto=$_POST["codigo_producto"];
$volver=$_POST["volver"];
$EAN=$_POST["EAN"];
$ARTICULO=$_POST["ARTICULO"];
$PRECIO_VENTA=number_format($_POST["PRECIO_VENTA"],2,".","");
$PRECIO_COSTE=number_format($_POST["PRECIO_COSTE"],2,".","");
$STOCK_MINIMO=$_POST["STOCK_MINIMO"];
if (strlen($STOCK_MINIMO)!=0) {
        $STOCK_MINIMO=number_format($STOCK_MINIMO,2,".","");
        } else {
        $STOCK_MINIMO=0;
        }
$PROVEEDOR=$_POST["PROVEEDOR"];
$desglosar_como=$_POST["desglosar_como"];
$UNIDAD=$_POST["UNIDAD"];
$porcion_individual=$_POST["porcion_individual"];
$TIPO_SERVICIO=$_POST["TIPO_SERVICIO"];
$subcat=$_POST["subcat"];
$ARTICULO_GENERICO=$_POST["ARTICULO_GENERICO"];
$ID_ALIMENTO_GENERICO=$_POST["ID_ALIMENTO_GENERICO"];
$SUBVENCION=$_POST["SUBVENCION"];
$CONTROL_ASISTENCIA=$_POST["CONTROL_ASISTENCIA"];
$ID_CENTRO_ASISTENCIA_array=$_POST["ID_CENTRO_ASISTENCIA"];
if (count($ID_CENTRO_ASISTENCIA_array)==0) {
        $ID_CENTRO_ASISTENCIA_NO_array="";
        } else {
        $ID_CENTRO_ASISTENCIA_NO_array=implode (",", $ID_CENTRO_ASISTENCIA_array);
        }
$fecha_ini_dia=$_POST["fecha_ini_dia"];
$fecha_ini_mes=$_POST["fecha_ini_mes"];
$fecha_ini_ano=$_POST["fecha_ini_ano"];
$fecha_ini=$fecha_ini_ano."/".$fecha_ini_mes."/".$fecha_ini_dia;
$fecha_fin_dia=$_POST["fecha_fin_dia"];
$fecha_fin_mes=$_POST["fecha_fin_mes"];
$fecha_fin_ano=$_POST["fecha_fin_ano"];
$fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;
$INCLUIR_INFORME=$_POST["INCLUIR_INFORME"];
$anula_comedor=$_POST["anula_comedor"];
$anula_horas_extra=$_POST["anula_horas_extra"];
$id_curso_articulo=$_POST["id_curso_articulo"];
$tipo_facturacion_extra=$_POST["tipo_facturacion_extra"];
$findemes=$_POST["findemes"];
$unidad_medida=$_POST["unidad_medida"];

$numRecords=0;
if (strlen($EAN)!=0) {
        $consulta2="SELECT * FROM TARIFA WHERE EAN='".$EAN."'";

        $count = db_query ($consulta2);
        $numRecords = db_num_rows ($count);
        }
        
if ($numRecords!=0) {
        header("Location:nuevo_articulo.php?codigo_familia=$codigo_familia&codigo_producto=$codigo_producto&findemes=$findemes&unidad_medida=$unidad_medida&desglosar_como=$desglosar_como&porcion_individual=$porcion_individual&tipo_facturacion_extra=$tipo_facturacion_extra&ID_ALIMENTO_GENERICO=$ID_ALIMENTO_GENERICO&id_curso_articulo=$id_curso_articulo&anula_horas_extra=$anula_horas_extra&anula_comedor=$anula_comedor&INCLUIR_INFORME=$INCLUIR_INFORME&ID_CENTRO_ASISTENCIA=".$ID_CENTRO_ASISTENCIA_NO_array."&CONTROL_ASISTENCIA=$CONTROL_ASISTENCIA&fecha_ini_dia=$fecha_ini_dia&fecha_ini_mes=$fecha_ini_mes&fecha_ini_ano=$fecha_ini_ano&fecha_fin_dia=$fecha_fin_dia&fecha_fin_mes=$fecha_fin_mes&fecha_fin_ano=$fecha_fin_ano&familia=".urlencode($familia)."&duplicado=1&ARTICULO_GENERICO=$ARTICULO_GENERICO&EAN=$EAN&ARTICULO=$ARTICULO&PRECIO_VENTA=$PRECIO_VENTA&PRECIO_COSTE=$PRECIO_COSTE&STOCK_MINIMO=$STOCK_MINIMO&PROVEEDOR=$PROVEEDOR&UNIDAD=$UNIDAD");
        } else {
        // Introduce el nuevo artículo
        
        $nuevo_articulo = db_query("INSERT INTO tarifa (codigo_familia, codigo_producto,CATEGORIA, EAN, ARTICULO, PRECIO_VENTA, PRECIO_COSTE, STOCK_MINIMO, PROVEEDOR, UNIDAD, porcion_individual, TIPO_SERVICIO, ARTICULO_GENERICO, ID_ALIMENTO_GENERICO, SUBVENCION, CONTROL_ASISTENCIA, ID_CENTRO_ASISTENCIA, FECHA_INICIO_ASISTENCIA, FECHA_FIN_ASISTENCIA,INCLUIR_INFORME,anula_horas_extra,anula_comedor, id_curso, tipo_facturacion_extra, subcat, desglosar_como, unidad_medida,findemes) VALUES ('$codigo_familia', '$codigo_producto', '$familia', '$EAN', '$ARTICULO', '$PRECIO_VENTA', '$PRECIO_COSTE', '$STOCK_MINIMO', '$PROVEEDOR', '$UNIDAD', '$porcion_individual', '$TIPO_SERVICIO', '$ARTICULO_GENERICO', '$ID_ALIMENTO_GENERICO','$SUBVENCION', '$CONTROL_ASISTENCIA', '".$ID_CENTRO_ASISTENCIA_NO_array."', '$fecha_ini', '$fecha_fin', '$INCLUIR_INFORME','$anula_horas_extra','$anula_comedor','$id_curso_articulo','$tipo_facturacion_extra','$subcat','$desglosar_como', '$unidad_medida','$findemes')") or die (db_error());
        
        if ($volver==1) {
                $codigo=db_insert_id();
                header("Location:modificar_articulo.php?cod=".$codigo);
                } else {
                header("Location:ver_familia.php?familia=".urlencode($familia));
                }
        }

db_close();

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

