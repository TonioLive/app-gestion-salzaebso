<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
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

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$id_contacto=$_GET["id_contacto"];

$accion_texto="Editar";
if (strlen($id_nota_seleccionada)==0) {
        $id_nota_seleccionada=$_GET["u"];
        if (strlen($id_nota_seleccionada)==0) {
                $id_nota_seleccionada=$_POST["u"];
                if (strlen($id_nota_seleccionada)==0) {
                        $id_nota_seleccionada=0;
                        $accion_texto="Nueva";
                        }
                }
        }

if ($id_nota_seleccionada!=0) {
        $consulta_datos_movimiento=db_query_params('SELECT * FROM contactos_seguimiento WHERE id_nota=?', 's', array($id_nota_seleccionada));

        if (db_num_rows($consulta_datos_movimiento)!=0)
                {
                $datos_movimiento=db_fetch_array($consulta_datos_movimiento);

                // Datos
                $comentarios=$datos_movimiento["comentarios"];
                }
        }
        
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nueva_nota1.php";

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_nota_seleccionada;?>">
<input type="hidden" name="id_contacto" value="<?=$id_contacto;?>">
<input type="hidden" name="formulario_enviado" value="1">
<input type="hidden" name="r" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> nota de seguimiento</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Escribe el texto de la nota que deseas añadir</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center"><textarea  name="comentarios" cols=85 rows=6 style="font-size: 11px; font-family: Arial"><?=$comentarios;?></textarea></td>
                         </table>
                         <br><input type="button" onclick="window.close();" value="Cancelar" style="font-size: 10px"> <input type="button" onclick="cambia();" value="Finalizar >>" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">

function cambia() {
                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.comentarios.value=="") { mensajes_error=mensajes_error+"Debes escribir un texto para la nota.\u000A";errores=1; }

                if (errores==0) {
                        document.<?=$formulario_name;?>.r.value=0;
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
        
//-->
</script>
</body>
</html>
