<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<div align="center"><center>
<?php 
$id_producto=$_GET["id_producto"];
$id_escuela=$_GET["id_escuela"];

$dia_seleccionado=$_GET["dia_sel"];

if (strlen($dia_seleccionado)==0) {
                        $dia_seleccionado=time();
                        }

$consulta_articulo = db_query("SELECT * FROM tarifa WHERE ID=$id_producto") or die (db_query());
$datos_articulo = db_fetch_array($consulta_articulo);

$familia=$datos_articulo["CATEGORIA"];

$ARTICULO=strtoupper($datos_articulo["ARTICULO"]);

$busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=$id_escuela ORDER BY nombre_centro");

$datos_escuela=db_fetch_array($busca_escuelas);

$nombre_escuela=strtoupper($datos_escuela["nombre_centro"]);

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nueva_entrada1_escuela.php";

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="id_producto" value="<?=$id_producto;?>">
<input type="hidden" name="id_escuela" value="<?=$id_escuela;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br><br>
                <font size=4 face="<?= $fuente_general; ?>"><b>Agregar/Quitar cantidades de:<br><br><?=$ARTICULO?></b> en <?=$nombre_escuela;?></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="middle">
                                <td width=210 align="right"><font size=3 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><input type="text" name="observaciones" size=60 style="font-size: 14px"></td>
                        </tr>
                        <tr valign="middle">
                                <td width=210 align="right"><font size=3 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                <td><font size=5 face="<?= $fuente_general; ?>"><input type="text" name="cantidad" size="6" style="font-size: 14px; text-align=right"> <?=$unidad_medida;?></font></td>
                        </tr>
                         </table>
                         <br><input type="button" onclick="window.location.href='resumen_entradas_producto_escuela.php?id_escuela=<?=$id_escuela?>&id_producto=<?=$id_producto?>'" value="Cancelar" style="font-size: 24px"> <input type="button" onclick="cambia(1);" value="Agregar" style="font-size: 24px"> <input type="button" onclick="cambia(-1);" value="Quitar" style="font-size: 24px"><br><br>
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
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
