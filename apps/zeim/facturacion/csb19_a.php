<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Datos
$array_facturas=$_GET["facturas"];

$formulario_name="seleccion_fecha_cobro";
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function enviar() {
                var errores=0;

                var mensajes_error="";

                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.fechacobro_mes.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_dia.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de cobro introducida no es correcta.\u000A";errores=1; }

                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }
function enviar1() {
                var errores=0;

                var mensajes_error="";
                document.<?=$formulario_name;?>.action="csb19g.php";
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.fechacobro_mes.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_dia.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de cobro introducida no es correcta.\u000A";errores=1; }

                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }
function enviar2() {
                var errores=0;

                var mensajes_error="";
                document.<?=$formulario_name;?>.action="csb19c.php";
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.fechacobro_mes.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_dia.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de cobro introducida no es correcta.\u000A";errores=1; }

                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }
function enviar3() {
                var errores=0;

                var mensajes_error="";
                document.<?=$formulario_name;?>.action="csb19h.php";
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.fechacobro_mes.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_dia.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de cobro introducida no es correcta.\u000A";errores=1; }

                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }
function enviar8() {
                var errores=0;

                var mensajes_error="";
                document.<?=$formulario_name;?>.action="csb19santander_sepa.php";
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.fechacobro_mes.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_dia.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de cobro introducida no es correcta.\u000A";errores=1; }

                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }  
        
function enviar10() {
                var errores=0;

                var mensajes_error="";
                document.<?=$formulario_name;?>.action="csb19santander_transfer_sepa.php";
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.fechacobro_mes.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_dia.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de abono introducida no es correcta.\u000A";errores=1; }

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
        
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center><br><font size=2><b>Exportación de recibos<br><br></b></font>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Selecciona la fecha de cobro de los recibos</b></small></small></td>
  </tr>
  <tr bgcolor="#efefef">
  <form name="seleccion_fecha_cobro" method="GET" action="csb19.php">
  <input type="hidden" name="facturas" value="<?=$array_facturas;?>">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
<select name="fechacobro_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval(date("d"))) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fechacobro_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval(date("m"))) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fechacobro_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=date("Y")+2; $c_ano>=date("Y")-2; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval(date("Y"))) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fechacobro','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a> </font>
  </td>
  </form>
  </tr>
  </table><br>
  <input type="button" onclick="window.close()" style="font-size: 10px" value="Cancelar"><br>
  <input type="button" onclick="enviar()" style="font-size: 10px" value="Generar recibos Banco Santander"> 
  <input type="button" onclick="enviar8()" style="font-size: 10px" value="Generar recibos Santander formato SEPA ISO 20022 XML"><!--<br>
  <input type="button" onclick="enviar3()" style="font-size: 10px" value="Generar recibos Bankia"><br>
  <input type="button" onclick="enviar1()" style="font-size: 10px" value="Generar recibos Banco Gallego"><br>
  <input type="button" onclick="enviar2()" style="font-size: 10px" value="Generar recibos Caixa Catalunya">-->
  <br>
  <font face="Arial"><b>Transferencias abonos</b></font><br><br>
  <input type="button" onclick="enviar10()" style="font-size: 10px" value="Generar transferencias Banco Santander formato SEPA ISO 20022 XML"><br>
</center></div>
</div>
</body>
</html>
