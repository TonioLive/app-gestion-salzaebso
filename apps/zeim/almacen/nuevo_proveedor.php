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
$accion_texto="Editar";
if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["u"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["u"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM proveedores WHERE ID_PROVEEDOR=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);

                // Datos
                $NIF=$datos_usuario["NIF"];
                $RAZON_SOCIAL=$datos_usuario["RAZON_SOCIAL"];
                $nombre_comercial=$datos_usuario["nombre_comercial"];
                $DOMICILIO=$datos_usuario["DOMICILIO"];
                $LOCALIDAD=$datos_usuario["LOCALIDAD"];
                $CP=$datos_usuario["CP"];
                $TELEFONO1=$datos_usuario["TELEFONO1"];
                $TELEFONO2=$datos_usuario["TELEFONO2"];
                $MOVIL=$datos_usuario["MOVIL"];
                $FAX=$datos_usuario["FAX"];
                $EMAIL=$datos_usuario["EMAIL"];
                $REGISTRO_SANITARIO=$datos_usuario["REGISTRO_SANITARIO"];
                $OBSERVACIONES=$datos_usuario["OBSERVACIONES"];
                $FORMA_DE_PAGO=$datos_usuario["FORMA_DE_PAGO"];
                $CUENTA_CARGO=$datos_usuario["CUENTA_CARGO"];
                $CUENTA_BANCO=$datos_usuario["CUENTA_BANCO"];
                $tipo_proveedor=$datos_usuario["tipo_proveedor"];

                }
        }
        
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_proveedor1.php";

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_usuario_seleccionado;?>">
<input type="hidden" name="id_expediente" value="<?=$id_expediente;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> proveedor</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos del proveedor</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo proveedor</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="tipo_proveedor" size="1" style="font-size: 10px">
                                <?php 
                                for ($icc=0;$icc<count($tipos_proveedores);$icc++) {
                                ?>
                                <option value="<?=$icc;?>"<?php  if ($icc==$tipo_proveedor) { print " selected"; } ?>><?=$tipos_proveedores[$icc][1]?></option>
                                <?php 
                                        }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Razón social</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="RAZON_SOCIAL" value="<?=$RAZON_SOCIAL;?>" size="60" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre comercial:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_comercial" value="<?=$nombre_comercial;?>" size="60" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>NIF/CIF</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="NIF" value="<?=$NIF;?>" size="20" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nº Registro Sanitario</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="REGISTRO_SANITARIO" value="<?=$REGISTRO_SANITARIO;?>" size="40" style="font-size: 10px"></font></td>
                        </tr>
                    <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Información de contacto</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Domicilio</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="DOMICILIO" value="<?=$DOMICILIO;?>" size="60" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>CP</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="CP" value="<?=$CP;?>" size="5" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Localidad</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="LOCALIDAD" value="<?=$LOCALIDAD;?>" size="60" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="EMAIL" value="<?=$EMAIL;?>" size="60" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfonos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="TELEFONO1" value="<?=$TELEFONO1;?>" style="font-size: 10px"> <input type="text" name="TELEFONO2" value="<?=$TELEFONO2;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Fax</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="FAX" value="<?=$FAX;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Otros datos</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Cuenta abono</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="CUENTA_BANCO" value="<?=$CUENTA_BANCO;?>" style="font-size: 10px" sie="60"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Cuenta cargo</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="CUENTA_CARGO" value="<?=$CUENTA_CARGO;?>" style="font-size: 10px" sie="60"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Forma de pago</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="FORMA_DE_PAGO" style="font-size: 10px">
                                <?php 
                                $busca_formas_pago=db_query("SELECT * FROM formas_de_pago_proveedores ORDER BY id_forma_pago");
                                while($formas_pago=db_fetch_array($busca_formas_pago)) {
                                ?>
                                <option value="<?=$formas_pago["id_forma_pago"]?>"<?php  if ($FORMA_DE_PAGO==$formas_pago["id_forma_pago"]) { print " selected"; } ?>><?=$formas_pago["nombre"]?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                    <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea  name="OBSERVACIONES" cols=70 rows=5 style="font-size: 11px; font-family: Arial"><?=$OBSERVACIONES;?></textarea></font></td>
                        </tr>
                         </table>
                         <br><input type="button" onclick="window.close();" value="Cancelar" style="font-size: 10px"> <input type="button" onclick="cambia();" value="Finalizar >>" style="font-size: 10px"><br><br><?php 
                         if ($id_usuario_seleccionado!=0) {
                         ?>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Evaluaciones del proveedor</b></font></td>
                        </tr>
                        <?php 
                        $consulta_evaluaciones=db_query_params('SELECT * FROM proveedores_evaluaciones WHERE id_proveedor=? ORDER BY fecha_valoracion DESC', 's', array($id_usuario_seleccionado)) or die(db_error());
                        
                        if (db_num_rows($consulta_evaluaciones)==0) {
                        ?>
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><i>Proveedor no evaluado</i></font></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Evaluado por</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Promedio</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>">-</font></td>
                        </tr>
                        <?php 
                        $consulta_cuestiones=db_query("SELECT * FROM proveedores_cuestiones ORDER BY id_cuestion");
                        $numero_de_cuestiones=db_num_rows($consulta_cuestiones);
                        
                        while($datos_evaluaciones=db_fetch_array($consulta_evaluaciones)) {
                        
                        $busca_datos_evaluador=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_evaluaciones["valorada_por"]);
                        
                        if (db_num_rows($busca_datos_evaluador)!=0) {
                                $datos_evaluador=db_fetch_array($busca_datos_evaluador);
                                $nombre_valorador=$datos_evaluador["Nombre"]." ".$datos_evaluador["Apellido1"]." ".$datos_evaluador["Apellido2"];
                                } else {
                                $nombre_valorador="Desconocido";
                                }

                        $promedio_valoracion=0;
                        
                        for ($c_cuest=1;$c_cuest<=$numero_de_cuestiones;$c_cuest++) {
                                $promedio_valoracion=$promedio_valoracion+$datos_evaluaciones["cuestion".$c_cuest];
                                }
                                
                        if ($numero_de_cuestiones>0) {
                                $promedio_valoracion=round(($promedio_valoracion/$numero_de_cuestiones),2);
                                }
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_evaluaciones["fecha_valoracion"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_valorador;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($promedio_valoracion,2,".",",");?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="nueva_evaluacion.php?u=<?=$id_usuario_seleccionado?>&e=<?=$datos_evaluaciones["id_evaluacion"]?>">Editar</a> | <a href="ver_evaluacion.php?u=<?=$id_usuario_seleccionado?>&e=<?=$datos_evaluaciones["id_evaluacion"]?>">Ver/Imprimir</a></font></td>
                        </tr>
                        <?php 
                                }
                        }
                        ?>
                        </table>
                        <br><input type="button" onclick="window.location.href='nueva_evaluacion.php?u=<?=$id_usuario_seleccionado?>'" value="Nueva evaluación" style="font-size: 10px"><br><br><?php 
                        }
                        ?>
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
                if (self.document.<?=$formulario_name;?>.RAZON_SOCIAL.value=="") { mensajes_error=mensajes_error+"Es obligatorio indicar la razón social del proveedor.\u000A";errores=1; }

                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
//-->
</script>
</body>
</html>
