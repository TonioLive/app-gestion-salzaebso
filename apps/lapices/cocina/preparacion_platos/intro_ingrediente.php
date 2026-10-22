<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
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
$id_empleado=$_GET["id_empleado"];
$id_plato=$_GET["id_plato"];
$id_alimento=$_GET["id_alimento"];

                $dia_seleccionado=$_GET["dia_sel"];

                if (strlen($dia_seleccionado)==0) {
                        $dia_seleccionado=time();
                        }

$busca_ingrediente=db_query("SELECT * FROM menu_alimentos WHERE id_alimento=$id_alimento");

$datos_ingrediente=db_fetch_array($busca_ingrediente);

$nombre_ingrediente=app_url_text_utf8($datos_ingrediente["nombre_alimento"]);

if ($datos_ingrediente["unidad_medida"]==1) {
        $unidad_medida="ml.";
        } else {
        $unidad_medida="gr.";
        }
        
$peso_unidad=$datos_ingrediente["peso_unidad"];
// Comprueba si son unidades o gramos
if ($peso_unidad>1) {
                $unidad_medida="Uds.";
                }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "intro_ingrediente1.php";

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="id_empleado" value="<?=$id_empleado;?>">
<input type="hidden" name="id_plato" value="<?=$id_plato;?>">
<input type="hidden" name="id_alimento" value="<?=$id_alimento;?>">
<input type="hidden" name="peso_unidad" value="<?=$peso_unidad;?>">
<input type="hidden" name="dia_sel" value="<?=$dia_seleccionado;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br><br>
                <font size=4 face="<?= $fuente_general; ?>"><b>Agregar/Quitar consumo de <?=$nombre_ingrediente?></b></font><br><br>
                <font size=4 face="<?= $fuente_general; ?>"><b><?=date("d/m/Y");?></b></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="middle">
                                <td width=210 align="right"><font size=5 face="<?= $fuente_general; ?>"><b>Producto</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">
                                <select name="ingrediente" size=1 style="font-size: 18px">
                                <?php 
                                $busca_tarifa=db_query("SELECT tarifa.ID, tarifa.ARTICULO FROM tarifa,detalles_pedido_proveedor WHERE tarifa.ID=detalles_pedido_proveedor.id_articulo AND tarifa.ID_ALIMENTO_GENERICO=$id_alimento GROUP BY tarifa.ID ORDER BY detalles_pedido_proveedor.id_pedido DESC");

                                if (db_num_rows($busca_tarifa)==0){
                                        $busca_tarifa=db_query("SELECT * FROM tarifa WHERE ID_ALIMENTO_GENERICO=$id_alimento ORDER BY ARTICULO");
                                        }
                                while($datos_tarifa=db_fetch_array($busca_tarifa)){
                                ?>
                                <option value="<?=$datos_tarifa["ID"];?>"><?=$datos_tarifa["ARTICULO"];?></option>
                                <?php 
                                }
                                ?>
                                </select>
                                </font></td>
                        </tr>
                        <tr valign="middle">
                                <td width=210 align="right"><font size=5 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                <td><font size=5 face="<?= $fuente_general; ?>"><input type="text" name="cantidad" size="6" style="font-size: 24px; text-align=right"> <?=$unidad_medida;?></font></td>
                        </tr>
                         </table>
                         <br><input type="button" onclick="window.close();" value="Cancelar" style="font-size: 24px"> <input type="button" onclick="cambia(1);" value="Agregar" style="font-size: 24px"> <input type="button" onclick="cambia(-1);" value="Quitar" style="font-size: 24px"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function cambia(signo) {
                var errores=0;

                var mensajes_error="";

                if (self.document.<?=$formulario_name;?>.cantidad.value!="") {
                        cantidad=self.document.<?=$formulario_name;?>.cantidad.value;
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad debe ser un número.\u000A";
                                errores=1;
                                }
                        } else {
                                mensajes_error=mensajes_error+"Debes introducir la cantidad.\u000A";
                                errores=1;
                        }

                if (errores==0) {
                        self.document.<?=$formulario_name;?>.cantidad.value=self.document.<?=$formulario_name;?>.cantidad.value*signo;
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
//-->
</script>
</body>
</html>
