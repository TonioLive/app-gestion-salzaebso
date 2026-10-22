<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_centro_caja=$_GET["id_centro_caja"];
$id_cierre_caja=$_GET["id_cierre_caja"];

$formulario_name="nuevo_movimiento";

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
<div id="botonera" style="display:block;padding:0px;border:0px solid"><br>
<form name="<?=$formulario_name;?>" method="POST" action="nueva_retirada1.php">
<input type="hidden" name="id_centro_caja" value="<?=$id_centro_caja?>">
<input type="hidden" name="id_cierre_caja" value="<?=$id_cierre_caja?>">
<font size=2><b>Nueva Retirada de efectivo de la caja</b></font><br><br>
<table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>"><b>Importe retirado:</b></font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="importe_pago" value="" size=10 style="font-size: 10px; text-align: right; background-color: #ffffff; border: 1px solid #cccccc"> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>"><b>Concepto:</b></font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="comentario" value="" size=80 style="font-size: 10px; text-align: left; background-color: #ffffff; border: 1px solid #cccccc"></font></td>
                        </tr>
</table>
<br>
<input type="button" onclick="window.close();self.opener.history.go(0);" value="Salir" style="font-size: 10px"> <input type="button" onclick="cambia();" value="Introducir nueva retirada de efectivo" style="font-size: 10px">
</form>
</div>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">

function imprimir() {
        document.getElementById('botonera').style.display='none';
        window.print();
        document.getElementById('botonera').style.display='block';
        }
        
function cambia() {
                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.comentario.value=="") {
                        mensajes_error=mensajes_error+"Falta indicar el concepto.\u000A";
                        errores=1;
                        }
                        
                if (self.document.<?=$formulario_name;?>.importe_pago.value=="") {
                        mensajes_error=mensajes_error+"Falta introducir el importe ingresado.\u000A";
                        errores=1;
                        } else {
                        importe_pago=self.document.<?=$formulario_name;?>.importe_pago.value;
                        importe_pago=importe_pago.replace(",",".");
                        if (isNaN(importe_pago)) {
                                mensajes_error=mensajes_error+"El importe ingresado debe ser una cantidad numérica.\u000A";
                                errores=1;
                                }
                        }

                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }
        
function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

                mes_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino='+nombre_formulario+'&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }

</script>
</center></div>
</div>
</body>
</html>
