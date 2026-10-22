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
$id_proveedor=$_GET["u"];
if (strlen($id_proveedor)==0) {
                $id_proveedor=$_POST["u"];
                }

$accion_texto="Editar";
if (strlen($id_evaluacion)==0) {
        $id_evaluacion=$_GET["e"];
        if (strlen($id_evaluacion)==0) {
                $id_evaluacion=$_POST["e"];
                if (strlen($id_evaluacion)==0) {
                        $id_evaluacion=0;
                        $accion_texto="Nueva";
                        }
                }
        }

if ($id_proveedor!=0) {
        $consulta_datos_proveedor=db_query_params('SELECT * FROM proveedores WHERE ID_PROVEEDOR=?', 's', array($id_proveedor));

        if (db_num_rows($consulta_datos_proveedor)!=0)
                {
                $datos_proveedor=db_fetch_array($consulta_datos_proveedor);

                // Datos
                $NIF=$datos_proveedor["NIF"];
                $RAZON_SOCIAL=$datos_proveedor["RAZON_SOCIAL"];
                $DOMICILIO=$datos_proveedor["DOMICILIO"];
                $LOCALIDAD=$datos_proveedor["LOCALIDAD"];
                $CP=$datos_proveedor["CP"];
                $TELEFONO1=$datos_proveedor["TELEFONO1"];
                $TELEFONO2=$datos_proveedor["TELEFONO2"];
                $MOVIL=$datos_proveedor["MOVIL"];
                $FAX=$datos_proveedor["FAX"];
                $EMAIL=$datos_proveedor["EMAIL"];
                $REGISTRO_SANITARIO=$datos_proveedor["REGISTRO_SANITARIO"];
                $OBSERVACIONES_proveedor=$datos_proveedor["OBSERVACIONES"];
                $FORMA_DE_PAGO=$datos_proveedor["FORMA_DE_PAGO"];
                $CUENTA_CARGO=$datos_proveedor["CUENTA_CARGO"];
                $CUENTA_BANCO=$datos_proveedor["CUENTA_BANCO"];

                }
        }

$array_cuestiones=array();
        
if ($id_evaluacion!=0) {
        $consulta_datos_evaluacion=db_query_params('SELECT * FROM proveedores_evaluaciones WHERE id_evaluacion=?', 's', array($id_evaluacion));

        if (db_num_rows($consulta_datos_evaluacion)!=0)
                {
                $datos_evaluacionr=db_fetch_array($consulta_datos_evaluacion);

                // Datos
                $observaciones=$datos_evaluacionr["observaciones"];
                $valoracion_fecha=$datos_evaluacionr["fecha_valoracion"];
                if ($valoracion_fecha=="0000-00-00") {
                                $valoracion_fecha_dia="";
                                $valoracion_fecha_mes="";
                                $valoracion_fecha_ano="";
                                } else {
                                $valoracion_fecha_dia=date("j",strtotime($datos_evaluacionr["fecha_valoracion"]));
                                $valoracion_fecha_mes=date("n",strtotime($datos_evaluacionr["fecha_valoracion"]));
                                $valoracion_fecha_ano=date("Y",strtotime($datos_evaluacionr["fecha_valoracion"]));
                                }
                $valorada_por=$datos_evaluacionr["valorada_por"];
                
                $consulta_cuestiones=db_query("SELECT * FROM proveedores_cuestiones ORDER BY id_cuestion");
                $numero_de_cuestiones=db_num_rows($consulta_cuestiones);
                while($datos_cuestiones=db_fetch_array($consulta_cuestiones)) {
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][0]=$datos_cuestiones["cuestion"];
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][1]=$datos_evaluacionr["cuestion".$datos_cuestiones["id_cuestion"]];
                        }

                }
        } else {
                $consulta_cuestiones=db_query("SELECT * FROM proveedores_cuestiones ORDER BY id_cuestion");
                $numero_de_cuestiones=db_num_rows($consulta_cuestiones);
                while($datos_cuestiones=db_fetch_array($consulta_cuestiones)) {
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][0]=$datos_cuestiones["cuestion"];
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][1]=0;
                        }
        }
        
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nueva_evaluacion1.php";

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="e" value="<?=$id_evaluacion;?>">
<input type="hidden" name="u" value="<?=$id_proveedor;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> evaluación proveedor</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=11 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos del proveedor</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Razón social</b></font></td>
                                <td colspan=10><font size=1 face="<?= $fuente_general; ?>"><?=$RAZON_SOCIAL;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>NIF/CIF</b></font></td>
                                <td colspan=10><font size=1 face="<?= $fuente_general; ?>"><?=$NIF;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=11 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Evaluación</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Evaluado por:</b></font></td>
                                <td colspan=10><font size=1 face="<?= $fuente_general; ?>"><select name="valorada_por" style="font-size: 10px" size=1>
                                <option value="0"></option>
                                <?php 
                                $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion>=3 ORDER BY usuarios.Nombre, usuarios.Apellido1") or die(db_error());
                                while($datos_usuarios=db_fetch_array($consulta_usuarios)){
                                ?>
                                <option value="<?=$datos_usuarios["id_usuario"];?>"<?php  if ($datos_usuarios["id_usuario"]==$valorada_por) { print " selected"; } ?>><?=$datos_usuarios["Nombre"]?> <?=$datos_usuarios["Apellido1"]?> <?=$datos_usuarios["Apellido2"]?></option>
                                <?php 
                                }
                                ?>
                                </select>
                                </font></td>
                        </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de evaluación: </b></font></td>
                                        <td colspan=10><select name="valoracion_fecha_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($valoracion_fecha_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="valoracion_fecha_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($valoracion_fecha_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="valoracion_fecha_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-2; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($valoracion_fecha_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('valoracion_fecha','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                                </tr>
                        <tr valign="top">
                                <td colspan=11 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <?php 
                                for ($c_puntos=1;$c_puntos<=10;$c_puntos++) {
                                ?>
                                <td><font size=1 face="<?= $fuente_general; ?>"><b><?=$c_puntos;?></b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                    <?php 
                    for ($c_cuest=1;$c_cuest<=$numero_de_cuestiones;$c_cuest++) {
                    ?>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b><?=$array_cuestiones[$c_cuest][0]?></b></font></td>
                                <?php 
                                for ($c_puntos=1;$c_puntos<=10;$c_puntos++) {
                                ?>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="radio" name="cuestion<?=$c_cuest?>" value="<?=$c_puntos;?>"<?php if ($array_cuestiones[$c_cuest][1]==$c_puntos) { print " checked"; } ?>></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                    <?php 
                    }
                    ?>
                    <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td colspan=10><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=70 rows=5 style="font-size: 11px; font-family: Arial"><?=$observaciones;?></textarea></font></td>
                        </tr>
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

                if (self.document.<?=$formulario_name;?>.valorada_por.value==0) { mensajes_error=mensajes_error+"Debes indicar quien realiza la evaluación.\u000A";errores=1; }
                
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.valoracion_fecha_mes.value+"/"+self.document.<?=$formulario_name;?>.valoracion_fecha_dia.value+"/"+self.document.<?=$formulario_name;?>.valoracion_fecha_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha introducida no es correcta.\u000A";errores=1; }

                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
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
//-->
</script>
</body>
</html>
