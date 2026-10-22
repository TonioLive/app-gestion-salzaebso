<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_factura=$_GET["id_factura"];

$formulario_name="nuevo_pago_cuenta";


// Datos de la factura

$contador=0;

$total_absoluto=0;
$total_cobrado=0;
$nivel_maximo_morosidad=0;

$morisidades[0]="<font color=green>No tiene pagos atrasados.</font>";
$morisidades[1]="<font color=red>Leve</font>";
$morisidades[2]="<font color=red>Grave</font>";
$morisidades[3]="<font color=red>Muy Grave</font>";
$morisidades[4]="<font color=red>Expulsión</font>";

$consulta1=db_query_params('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND id_factura=? ORDER BY fecha_emision DESC', 's', array($id_factura));
$datos_factura = db_fetch_array($consulta1);

// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
        }

$total_factura=round(($total_base+$total_iva+$total_recargo), 2);
$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_0=number_format(Round($total_base_0,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_0=number_format(Round($total_iva_0,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

$total_absoluto=$total_absoluto+round(($total_base+$total_iva+$total_recargo),2);

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$nombre_centro=$datos_centro["nombre_centro"];
$prefijo_centro=$datos_centro["prefijo_centro"];
$razon_social=$datos_centro["razon_social"];
$domicilio=$datos_centro["domicilio_centro"];
$cp=$datos_centro["CP_centro"];
$poblacion=$datos_centro["poblacion_centro"];
$telefono=$datos_centro["telefono_centro"];
$fax=$datos_centro["fax_centro"];
$cif=$datos_centro["CIF_centro"];
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

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<div id="botonera" style="display:block;padding:0px;border:0px solid"><br>
<form name="nuevo_pago_cuenta" method="POST" action="pagos_a_cuenta1.php">
<input type="hidden" name="id_factura" value="<?=$id_factura?>">
<input type="hidden" name="nombre_alumno" value="<?=$datos_factura["Nombre"]." ".$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"]?>">
<input type="hidden" name="id_centro_factura" value="<?=$datos_factura["centro_factura"]?>">
<input type="hidden" name="numero_factura" value="<?=$datos_de_factura["serie"].$datos_factura["num_factura"];?>/<?=$datos_factura["ejercicio_factura"];?>">
<font size=2><b>Nuevo pago a cuenta</b></font><br><br>
<table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <tr vAlign="top">
                                        <td align="right" bgcolor="#ececec"><font face="<?= $fuente_general; ?>" size="1"><b>Fecha del pago</b></font></td>
                                        <td bgcolor="#ffffff"><select name="pago_fechains_dia" style="font-size: 10px; text-align: right; background-color: #ffffff; border: 1px solid #cccccc">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($dia_a==date("d")) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="pago_fechains_mes" style="font-size: 10px; text-align: right; background-color: #ffffff; border: 1px solid #cccccc">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($mes_a==date("m")) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="pago_fechains_ano" style="font-size: 10px; text-align: right; background-color: #ffffff; border: 1px solid #cccccc">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-2; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($ano_a==date("Y")) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('pago_fechains','nuevo_pago_cuenta');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></td>
                                </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>"><b>Importe cobrado:</b></font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="importe_pago" value="" size=10 style="font-size: 10px; text-align: right; background-color: #ffffff; border: 1px solid #cccccc"> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>"><b>Forma de pago:</b></font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="forma_pago" style="font-size: 10px; text-align: right; background-color: #ffffff; border: 1px solid #cccccc">
                          <option value="0">Contado</option>
                          <option value="1">Domiciliacion bancaria, Cuenta: <?=$datos_factura["banco_cuenta"];?></option>
                          <option value="2">Transferencia (Pedir justificante)</option>
                          <option value="5">Ticket Guardería</option>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones:</b></font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="observaciones_pago" value="" size=80 style="font-size: 10px; text-align: left; background-color: #ffffff; border: 1px solid #cccccc"></font></td>
                        </tr>
</table>
<br>
<input type="button" onclick="imprimir()" value="Imprimir" style="font-size: 10px"> <input type="button" onclick="window.close();" value="Salir" style="font-size: 10px"> <input type="button" onclick="cambia();" value="Introducir pago" style="font-size: 10px">
</form>
</div>
Listado de pagos a cuenta de la factura Nº <?=$prefijo_centro.$datos_factura["num_factura"];?>/<?=$datos_factura["ejercicio_factura"];?><br><br>

<?php 
// Comprueba pagos a cuenta
$total_a_cuenta=0;

$formas_de_pago[0]="Contado";
$formas_de_pago[1]="Domiciliacion bancaria, Cuenta: ".$datos_factura["banco_cuenta"];
$formas_de_pago[2]="Transferencia";
$formas_de_pago[5]="Cheque guardería";

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));

if (db_num_rows($busca_pagos)==0) {
        ?>
        <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                <tr vAlign="top">
                        <td align="center" bgcolor="#ececec"><font face="<?= $fuente_general; ?>" size="2"><b>No se han efectuado pagos cuenta de esta factura.</b></font></td>
                </tr>
        </table>
        <?php 
        $total_cobrado=0;
        } else {
        ?>
        <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                <tr vAlign="top">
                        <td align="center" bgcolor="#ececec"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha</b></font></td>
                        <td align="center" bgcolor="#ececec"><font face="<?= $fuente_general; ?>" size="2"><b>Forma de pago</b></font></td>
                        <td align="center" bgcolor="#ececec"><font face="<?= $fuente_general; ?>" size="2"><b>Importe</b></font></td>
                        <td align="center" bgcolor="#ececec"><font face="<?= $fuente_general; ?>" size="2"><b>Observaciones</b></font></td>
                        <td align="center" bgcolor="#ececec"><font face="<?= $fuente_general; ?>" size="2"><b>&nbsp;</b></font></td>
                </tr>
        <?php 
while ($pagos=db_fetch_array($busca_pagos)) {
?>
                <tr vAlign="top">
                        <td align="center" bgcolor="#ffffff"><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($pagos["fecha_pago"]));?></font></td>
                        <td align="center" bgcolor="#ffffff"><font face="<?= $fuente_general; ?>" size="2"><?=$formas_de_pago[$pagos["forma_pago"]]?></font></td>
                        <td align="right" bgcolor="#ffffff"><font face="<?= $fuente_general; ?>" size="2"><?=number_format($pagos["importe_pago"], 2, ',', '.');?> €</font></td>
                        <td align="center" bgcolor="#ffffff"><font face="<?= $fuente_general; ?>" size="2"><?=$pagos["observaciones_pago"]?></font></td>
                        <td align="center" bgcolor="#ffffff"><font face="<?= $fuente_general; ?>" size="2"><a href="/pdf/recibos.php?id_factura=<?=$id_factura?>&id_pago=<?=$pagos["id_pago"];?>" title="Imprimir recibo" target="recibos"><img src="servicios.gif" border=0></a> <a href="javascript:eliminar_pago(<?=$pagos["id_pago"]?>);" title="Eliminar pago a cuenta"><img src="borrar.gif" border=0></a></font></td>
                </tr>
<?php 
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
?>
</table>
<?php 
$total_cobrado=round($total_a_cuenta,2);

}

if ($total_cobrado<$total_factura) {
        if ($total_a_cuenta>0) {
                $estado_factura="<font color=red>Pendiente cobro: ".number_format(($total_factura-$total_a_cuenta), 2, ',', '.')." €</font>";
                } else {
                $estado_factura="<font color=red>No cobrada, Importe pendiente de cobro: ".number_format(($total_factura-$total_a_cuenta), 2, ',', '.')." €</font>";
                }
        } else {
        $estado_factura="<font color=green>Cobrada</font>";
        }

?>
<br><font size=2>Estado de la factura: <b><?=$estado_factura?></b></font>
<br>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">

var importe_maximo=<?=$total_factura?>;
var importe_cobrado=<?=$total_cobrado?>;
var importe_restante=<?=$total_factura-$total_cobrado?>;

document.nuevo_pago_cuenta.importe_pago.value=importe_restante;

function imprimir() {
        document.getElementById('botonera').style.display='none';
        window.print();
        document.getElementById('botonera').style.display='block';
        }
        
function cambia() {
                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.importe_pago.value=="") {
                        mensajes_error=mensajes_error+"Falta introducir el importe pagado.\u000A";
                        errores=1;
                        } else {
                        importe_pago=self.document.<?=$formulario_name;?>.importe_pago.value;
                        importe_pago=importe_pago.replace(",",".");
                        if (isNaN(importe_pago)) {
                                mensajes_error=mensajes_error+"El importe pagado debe ser una cantidad numérica.\u000A";
                                errores=1;
                                } else {
                                if (importe_pago>importe_restante) {
                                        mensajes_error=mensajes_error+"El importe pagado no puede ser superior al importe de la factura o al importe pendiente de pago.\u000A";
                                        errores=1;
                                        }
                                }
                        }
                
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.pago_fechains_mes.value+"/"+self.document.<?=$formulario_name;?>.pago_fechains_dia.value+"/"+self.document.<?=$formulario_name;?>.pago_fechains_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha introducida no es correcta.\u000A";errores=1; }

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

function eliminar_pago(id_pago) {
        if (confirm("Se va a eliminar el pago a cuenta seleccionado, ¿Estás seguro?")){
                window.location.href="eliminar_pago_a_cuenta.php?id_factura=<?=$id_factura?>&id_pago="+id_pago;
                }
        }

        
</script>
</center></div>
</div>
</body>
</html>
