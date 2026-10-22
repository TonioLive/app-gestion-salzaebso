<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
require("../configuracion/autentifica_admin.php");
include("cuentas_y_tipos.php");

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

$cuenta_seleccionada=$_GET["cuenta_seleccionada"];
$id_centro_sel_costes=$_GET["id_centro_sel_costes"];
$partida_sel_costes=$_GET["partida_sel_costes"];
$ejercicio=$_GET["ejercicio"];
$mes_seleccionado=$_GET["mes"];
$tipo_seleccionado=$_GET["tipo"];
if (strlen($cuenta_seleccionada)==0) {
                $cuenta_seleccionada="0";
                }
if (strlen($ejercicio)==0) {
                $ejercicio=strftime("%Y");
                }
if (strlen($mes_seleccionado)==0) {
                $mes_seleccionado=strftime("%m");
                }
if (strlen($tipo_seleccionado)==0) {
                $tipo_seleccionado="0";
                }

$id_coste=$_GET["id_coste"];
if (strlen($id_coste)==0) {
                // Borra costes no finalizados
                db_query("DELETE FROM gestion_costes WHERE finalizado=0 AND introducido_por=".$idUsuario) or die(db_error());
                // Introduce nuevo coste
                db_query_params('INSERT INTO gestion_costes (fecha_insercion, introducido_por) VALUES (?, ?)', 'ss', array((string)(date("%Y/%m/%d")), (string)($idUsuario))) or die(db_error());
                $id_coste=db_insert_id();
                }


$filtros=" WHERE id_coste=".$id_coste;

$consulta_costes=db_query("SELECT * FROM gestion_costes".$filtros);

if (db_num_rows($consulta_costes)!=0) {

$datos_coste=db_fetch_array($consulta_costes);

$proporcional=$datos_coste["proporcional"];

$escuelas1=$datos_coste["escuelas"];

if (strlen($escuelas1)!=0) {
        $escuelas2=explode(",",$escuelas1);
        
        for ($cs=0;$cs<=count($escuelas2);$cs++) {
                $escuelas[$escuelas2[$cs]]=1;
                }
        
        }

$fecha_factura=$datos_coste["fecha_factura"];
if ($fecha_factura==0) {
        $fecha_factura_dia=date("j");
        $fecha_factura_mes=date("m");
        $fecha_factura_ano=date("Y");
        } else {
        $fecha_factura_dia=date("j",strtotime($datos_coste["fecha_factura"]));
        $fecha_factura_mes=date("m",strtotime($datos_coste["fecha_factura"]));
        $fecha_factura_ano=date("Y",strtotime($datos_coste["fecha_factura"]));
        }
        
$fecha_vencimiento=$datos_coste["fecha_vencimiento"];
if ($fecha_vencimiento==0) {
        $fecha_vencimiento_dia=date("j");
        $fecha_vencimiento_mes=date("m");
        $fecha_vencimiento_ano=date("Y");
        } else {
        $fecha_vencimiento_dia=date("j",strtotime($datos_coste["fecha_vencimiento"]));
        $fecha_vencimiento_mes=date("m",strtotime($datos_coste["fecha_vencimiento"]));
        $fecha_vencimiento_ano=date("Y",strtotime($datos_coste["fecha_vencimiento"]));
        }

$id_ingreso=$datos_coste["id_ingreso"];
if ($id_ingreso!=0) {
        $consulta_ingresos=db_query_params('SELECT * FROM gestion_ingresos WHERE id_ingreso=?', 's', array($id_ingreso)) or die(db_error());
        $datos_ingreso=db_fetch_array($consulta_ingresos);
        }
        
?>
<script language="Javascript">

var dtCh= "/";
var minYear=1800;
var maxYear=2100;

function isInteger(s){
        var i;
    for (i = 0; i < s.length; i++){
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
        var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
        // February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
        for (var i = 1; i <= n; i++) {
                this[i] = 31
                if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
                if (i==2) {this[i] = 29}
   }
   return this
}

function isDate(dtStr){
        var daysInMonth = DaysArray(12)
        var pos1=dtStr.indexOf(dtCh)
        var pos2=dtStr.indexOf(dtCh,pos1+1)
        var strMonth=dtStr.substring(0,pos1)
        var strDay=dtStr.substring(pos1+1,pos2)
        var strYear=dtStr.substring(pos2+1)
        strYr=strYear
        if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
        if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
        for (var i = 1; i <= 3; i++) {
                if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
        }
        month=parseInt(strMonth)
        day=parseInt(strDay)
        year=parseInt(strYr)
        if (pos1==-1 || pos2==-1){
                //alert("El formato de la fecha debe ser : mm/dd/yyyy")
                return false
        }
        if (strMonth.length<1 || month<1 || month>12){
                //alert("Introduzca un mes correcto")
                return false
        }
        if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
                //alert("Introduzca un día correcto")
                return false
        }
        if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
                //alert("Introduzca un año de cuatro dígitos entre los valores "+minYear+" y "+maxYear)
                return false
        }
        if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
                //alert("La fecha no es correcta")
                return false
        }
return true
}

function enviar_formulario() {
                var errores=0;
                document.coste.importe.value=document.coste.importe.value.replace(",",".");
                var mensajes_error="";
                if (document.coste.importe.value=="") {
                        mensajes_error=mensajes_error+"Es obligatorio introducir el importe.\u000A";errores=1;
                                document.coste.importe.style.color="#FF0000";
                        } else {
                        if (isNaN(document.coste.importe.value)) {
                                mensajes_error=mensajes_error+"El importe debe ser un número.\u000A";errores=1;
                                document.coste.importe.style.color="#FF0000";
                                }
                        }

                // Comprueba fechas
                la_fecha=document.coste.fecha_factura_mes.value+"/"+document.coste.fecha_factura_dia.value+"/"+document.coste.fecha_factura_ano.value
                if (isDate(la_fecha)!=true) {
                                            mensajes_error=mensajes_error+"La fecha de entrada introducida no es correcta.\u000A";errores=1;
                                            document.coste.fecha_factura_mes.style.color="#FF0000";
                                            document.coste.fecha_factura_dia.style.color="#FF0000";
                                            document.coste.fecha_factura_ano.style.color="#FF0000";
                                            }
                la_fecha=document.coste.fecha_vencimiento_mes.value+"/"+document.coste.fecha_vencimiento_dia.value+"/"+document.coste.fecha_vencimiento_ano.value
                if (isDate(la_fecha)!=true) {
                                            mensajes_error=mensajes_error+"La fecha de vencimiento introducida no es correcta.\u000A";errores=1;
                                            document.coste.fecha_vencimiento_mes.style.color="#FF0000";
                                            document.coste.fecha_vencimiento_dia.style.color="#FF0000";
                                            document.coste.fecha_vencimiento_ano.style.color="#FF0000";
                                            }

                if (errores==0) {
                        document.coste.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }

function pon_nueva_opcion(nueva_opcion) {
                var oOption = document.createElement("OPTION");
                document.coste.banco.options.add(oOption);
                oOption.innerText = nueva_opcion;
                oOption.value = nueva_opcion;
                document.coste.banco.value=nueva_opcion;
        }
        
function intro_partida() {
        window.open('listado_partidas.php','partidas','scrollbars =yes,toolbar=no,resizable=no,width=350,height=400');
        }
function intro_banco() {
        window.open('listado_cajas.php','cajas','scrollbars =yes,toolbar=no,resizable=no,width=350,height=400');
        }

//-->
</script>
<table width="75%" border=0 cellpadding=0 cellspacing=0>
<form name="coste" method="POST" action="guarda_coste.php?partida_sel_costes=<?=$partida_sel_costes;?>&id_centro_sel_costes<?=$id_centro_sel_costes?>&tipo=<?=$tipo_seleccionado?>&cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>">
<input type="hidden" name="id_coste" value="<?=$id_coste;?>">
<input type="hidden" name="id_ingreso" value="<?=$id_ingreso;?>">
<input type="hidden" name="transpaso" value="0">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Gasto</b></font>
                </td>
        </tr>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top">
                                 <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela:</b></font></td>
                                 <td align="left" bgcolor="#FFFFFF" colspan=3><font size=1 face="<?= $fuente_general; ?>">
                        <select name="id_centro_coste" size=1 style="font-size: 10px">
                <option value="0">Común</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"<?php if ($datos_coste["id_centro"]==$centros["id_centro"]) { echo " selected"; }?>><?=$centros["nombre_centro"];?></option>
                <?php 
                }
                ?>
                </select></td>
                </tr>
                <tr valign="top">
                        <td align="center" bgcolor="#EFEFEF" colspan=4><font size=1 face="<?= $fuente_general; ?>"><b>Solo para gastos comunes</b></font>
                        </td>
                </tr>
                <tr valign="top">
                        <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Repartir proporcionalmente al nº de niños:</b></font></td>
                                 <td align="left" bgcolor="#FFFFFF" colspan=3><font size=1 face="<?= $fuente_general; ?>">
                        <input type="radio" value="0" name="proporcional"<?php  if (intval($proporcional)==0) { print " checked";} ?>> No <input type="radio" value="1" name="proporcional"<?php  if (intval($proporcional)==1) { print " checked";} ?>> Si </td>
                </tr>
                <tr valign="top">
                        <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>A repartir entre las escuelas:</b></font></td>
                                 <td align="left" bgcolor="#FFFFFF" colspan=3><font size=1 face="<?= $fuente_general; ?>">
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                $id_escue=$centros["id_centro"];
                $nombre_escuela=$centros["nombre_centro"];
                ?>
                        <input type="checkbox" value="<?=$id_escue?>" name="escuelas[]"<?php  if ($escuelas[$id_escue]==1) { print " checked";} ?>> <?=$nombre_escuela?><br>
                        <?php 
                        }
                        ?>
                        
                        </td>
                </tr>
                <tr valign="top">
                        <td align="center" bgcolor="#EFEFEF" colspan=4><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font>
                        </td>
                </tr>
                        <tr valign="top">
                                 <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de gasto:</b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="tipo_coste" class="formulario_tipo">
                                <?php 
                                for ($c_tipo=1; $c_tipo<=count($tipos_costes); $c_tipo=$c_tipo+1) {
                                        $nombre_tipo=$tipos_costes[$c_tipo-1];
                                ?>
                                <option value="<?=$c_tipo?>"<?php if ($datos_coste["tipo_coste"]==$c_tipo) { echo " selected"; }?>><?=$nombre_tipo?></option>
                                <?php 
                                }
                                ?>
                                </select>
                                </td>
                                 <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Forma de pago:</b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="forma_pago" class="formulario_tipo">
                                <?php 
                                for ($c_forma_pago=1; $c_forma_pago<=count($formas_de_pago); $c_forma_pago=$c_forma_pago+1) {
                                        $nombre_forma_pago=$formas_de_pago[$c_forma_pago-1];
                                ?>
                                <option value="<?=$c_forma_pago?>"<?php if ($datos_coste["forma_pago"]==$c_forma_pago) { echo " selected"; }?>><?=$nombre_forma_pago?></option>
                                <?php 
                                }
                                ?>
                                </select>
                                </td>
                        </tr>
                        
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha factura:</b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_factura_dia" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_factura_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_factura_mes" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_factura_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_factura_ano" class="formulario_tipo">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+5; $c_ano>=strftime("%Y")-10; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_factura_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select></font></td>
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha vencimiento: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_vencimiento_dia" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_vencimiento_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_vencimiento_mes" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_vencimiento_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_vencimiento_ano" class="formulario_tipo">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+5; $c_ano>=strftime("%Y")-10; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_vencimiento_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Núm. Factura: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF" colspan=3><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="num_factura" size="25" class="formulario_tipo" value="<?=$datos_coste["num_factura"];?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Proveedor: </b></font></td>
                                <td colspan=3 bgcolor="#ffffff"><select size=1 name="proveedor" class="formulario_tipo">
                                <option value="">Selecciona el proveedor</option>
                                <?php 
                                $num_clientes_con_albaranes_pend=0;
                                $consulta_clientes = db_query("SELECT * FROM proveedores ORDER BY RAZON_SOCIAL") or die (db_error());
                                while($datos_clientes = db_fetch_array($consulta_clientes)){
                                ?>
                                <option value="<?=$datos_clientes["ID_PROVEEDOR"];?>"<?php if ($datos_coste["proveedor"]==$datos_clientes["ID_PROVEEDOR"]) { echo " selected"; }?>><?=$datos_clientes["RAZON_SOCIAL"];?></option>
                                <?php 
                                }
                                ?>
                                </select></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Importe: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="importe" size="10" class="formulario_tipo" value="<?=$datos_coste["importe"];?>"> €</font></td>
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Cuenta: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="banco" style="font-size: 10px" size=1>
                                <?php 
                                $consulta_cuentas_bancos=db_query("SELECT * FROM cuentas_gastos ORDER BY id_cuenta");
                                while($cuentas_bancos=db_fetch_array($consulta_cuentas_bancos)) {
                                        $c_cuen=$cuentas_bancos["id_cuenta"];
                                        $nombre_cuenta=$cuentas_bancos["nombre_cuenta"];
                                ?>
                                <option value="<?=$c_cuen?>"<?php if ($datos_coste["banco"]==$c_cuen) { echo " selected"; }?>><?=$nombre_cuenta?></option>
                                <?php 
                                }
                                ?>
                                </select> <input type="button" value="Nueva" onclick="intro_banco()" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Partida:</b></font></td>
                                <td bgcolor="#FFFFFF" colspan=3><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="partida" size=60 readonly value="<?=$datos_coste["partida"];?>" style="font-size: 10px" onclick="intro_partida()"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td align="left" bgcolor="#FFFFFF" colspan=3>
                                <textarea class="defaut" style="FONT-SIZE: 11px; FONT-FAMILY: Arial" name="observaciones" rows="4" cols="82"><?=$datos_coste["observaciones"];?></textarea>
                                </td>
                        </tr>
                        </table>
                        <br>
                        <input type="button" value="Cancelar" onclick="window.location.href='costes.php?cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>'" style="font-size: 10px"> <input type="button" onclick="enviar_formulario();" value="Guardar cambios" class="formulario_tipo">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
} else {
?>
<p align="center"><font size=4>ERROR: No se ha encontrado el gasto solicitado</font><br><br><font size=3><a href="javascript:history.go(-1);"><< Volver</a></font></p>
<?php 
}
 db_close();
 require("../configuracion/parte_inferior.php"); ?>

