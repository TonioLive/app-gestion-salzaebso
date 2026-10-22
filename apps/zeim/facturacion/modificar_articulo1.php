<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$familia=$_POST["familia"];
$codigo=$_POST["codigo"];
$volver=$_POST["volver"];
$EAN1=$_POST["EAN1"];
$EAN=$_POST["EAN"];
$ARTICULO=$_POST["ARTICULO"];
$PRECIO_VENTA=number_format($_POST["PRECIO_VENTA"],4,".","");
$PRECIO_COSTE=number_format($_POST["PRECIO_COSTE"],4,".","");
$STOCK_MINIMO=$_POST["STOCK_MINIMO"];
if (strlen($STOCK_MINIMO)!=0) {
        $STOCK_MINIMO=number_format($STOCK_MINIMO,4,".","");
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
if ((strlen($EAN)!=0) and ($EAN1!=$EAN)) {
        $consulta2=array('SELECT * FROM TARIFA WHERE ID<>? AND EAN=?', 'ss', array($codigo, (string)($EAN)));

        $count = db_query_params($consulta2[0], $consulta2[1], $consulta2[2]);
        $numRecords = db_num_rows ($count);
        }

if ($numRecords!=0) {
        header("Location:modificar_articulo.php?findemes=$findemes&unidad_medida=$unidad_medida&desglosar_como=$desglosar_como&porcion_individual=$porcion_individual&subcat=$subcat&tipo_facturacion_extra=$tipo_facturacion_extra&ID_ALIMENTO_GENERICO=$ID_ALIMENTO_GENERICO&id_curso_articulo=$id_curso_articulo&anula_horas_extra=$anula_horas_extra&anula_comedor=$anula_comedor&INCLUIR_INFORME=$INCLUIR_INFORME&ID_CENTRO_ASISTENCIA=".$ID_CENTRO_ASISTENCIA_NO_array."&CONTROL_ASISTENCIA=$CONTROL_ASISTENCIA&fecha_ini_dia=$fecha_ini_dia&fecha_ini_mes=$fecha_ini_mes&fecha_ini_ano=$fecha_ini_ano&fecha_fin_dia=$fecha_fin_dia&fecha_fin_mes=$fecha_fin_mes&fecha_fin_ano=$fecha_fin_ano&familia=".urlencode($familia)."&codigo=$codigo&duplicado=1&ARTICULO_GENERICO=$ARTICULO_GENERICO&EAN=$EAN&EAN1=$EAN1&ARTICULO=$ARTICULO&PRECIO_VENTA=$PRECIO_VENTA&PRECIO_COSTE=$PRECIO_COSTE&STOCK_MINIMO=$STOCK_MINIMO&PROVEEDOR=$PROVEEDOR&UNIDAD=$UNIDAD&SUBVENCION=$SUBVENCION");
        } else {
        // Introduce el nuevo artículo
        if ($codigo!=0) {
                $nuevo_articulo = db_query("UPDATE tarifa SET unidad_medida='$unidad_medida', desglosar_como='$desglosar_como', porcion_individual='$porcion_individual', subcat='$subcat',tipo_facturacion_extra='$tipo_facturacion_extra', ID_ALIMENTO_GENERICO='$ID_ALIMENTO_GENERICO',id_curso='$id_curso_articulo', anula_horas_extra='$anula_horas_extra',anula_comedor='$anula_comedor',INCLUIR_INFORME='$INCLUIR_INFORME', ID_CENTRO_ASISTENCIA='".$ID_CENTRO_ASISTENCIA_NO_array."', CONTROL_ASISTENCIA='$CONTROL_ASISTENCIA', FECHA_INICIO_ASISTENCIA='$fecha_ini', FECHA_FIN_ASISTENCIA='$fecha_fin', CATEGORIA='$familia', EAN='$EAN', ARTICULO='$ARTICULO', PRECIO_VENTA='$PRECIO_VENTA', PRECIO_COSTE='$PRECIO_COSTE', STOCK_MINIMO='$STOCK_MINIMO', PROVEEDOR='$PROVEEDOR', UNIDAD='$UNIDAD', TIPO_SERVICIO='$TIPO_SERVICIO', ARTICULO_GENERICO='$ARTICULO_GENERICO',SUBVENCION=$SUBVENCION,findemes='$findemes' WHERE ID=$codigo") or die (db_error());
                } else {
                $nuevo_articulo = db_query_params('INSERT INTO tarifa (CATEGORIA, EAN, ARTICULO, PRECIO_VENTA, PRECIO_COSTE, STOCK_MINIMO, PROVEEDOR, UNIDAD, porcion_individual, TIPO_SERVICIO, ARTICULO_GENERICO, ID_ALIMENTO_GENERICO, SUBVENCION, CONTROL_ASISTENCIA, ID_CENTRO_ASISTENCIA, FECHA_INICIO_ASISTENCIA, FECHA_FIN_ASISTENCIA,INCLUIR_INFORME,anula_horas_extra,anula_comedor, id_curso, tipo_facturacion_extra, subcat, desglosar_como, unidad_medida,findemes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?,?,?,?,?,?,?, ?,?)', 'ssssssssssssssssssssssssss', array((string)($familia), (string)($EAN), (string)($ARTICULO), (string)($PRECIO_VENTA), (string)($PRECIO_COSTE), (string)($STOCK_MINIMO), (string)($PROVEEDOR), (string)($UNIDAD), (string)($porcion_individual), (string)($TIPO_SERVICIO), (string)($ARTICULO_GENERICO), (string)($ID_ALIMENTO_GENERICO), (string)($SUBVENCION), (string)($CONTROL_ASISTENCIA), (string)($ID_CENTRO_ASISTENCIA_NO_array), (string)($fecha_ini), (string)($fecha_fin), (string)($INCLUIR_INFORME), (string)($anula_horas_extra), (string)($anula_comedor), (string)($id_curso_articulo), (string)($tipo_facturacion_extra), (string)($subcat), (string)($desglosar_como), (string)($unidad_medida), (string)($findemes))) or die (db_error());
                $codigo=db_insert_id();
                }
                
        if ($volver==1) {
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
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

