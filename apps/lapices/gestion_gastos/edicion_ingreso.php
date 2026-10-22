<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$cuenta_seleccionada=$_GET["cuenta_seleccionada"];
$ejercicio=$_GET["ejercicio"];
$mes_seleccionado=$_GET["mes"];
if (strlen($cuenta_seleccionada)==0) {
                $cuenta_seleccionada="0";
                }
if (strlen($ejercicio)==0) {
                $ejercicio=strftime("%Y");
                }
if (strlen($mes_seleccionado)==0) {
                $mes_seleccionado=strftime("%m");
                }

$id_ingreso=$_GET["id_ingreso"];
if (strlen($id_ingreso)==0) {
                // Borra ingresos no finalizados
                db_query("DELETE FROM gestion_ingresos WHERE finalizado=0 AND introducido_por=".$idUsuario) or die(db_error());
                // Introduce nuevo ingreso
                db_query("INSERT INTO gestion_ingresos (fecha_insercion, introducido_por) VALUES ('".date("%Y/%m/%d")."', '".$idUsuario."')") or die(db_error());
                $id_ingreso=db_insert_id();
                }


$filtros=" WHERE id_ingreso=".$id_ingreso;

$consulta_ingresos=db_query("SELECT * FROM gestion_ingresos".$filtros);

if (db_num_rows($consulta_ingresos)!=0) {

$datos_ingreso=db_fetch_array($consulta_ingresos);

$fecha_entrada=$datos_ingreso["fecha_entrada"];
if ($fecha_entrada==0) {
        $fecha_entrada_dia=date("j");
        $fecha_entrada_mes=date("m");
        $fecha_entrada_ano=date("Y");
        } else {
        $fecha_entrada_dia=date("j",strtotime($datos_ingreso["fecha_entrada"]));
        $fecha_entrada_mes=date("m",strtotime($datos_ingreso["fecha_entrada"]));
        $fecha_entrada_ano=date("Y",strtotime($datos_ingreso["fecha_entrada"]));
        }
        
$fecha_vencimiento=$datos_ingreso["fecha_vencimiento"];
if ($fecha_vencimiento==0) {
        $fecha_vencimiento_dia=date("j");
        $fecha_vencimiento_mes=date("m");
        $fecha_vencimiento_ano=date("Y");
        } else {
        $fecha_vencimiento_dia=date("j",strtotime($datos_ingreso["fecha_vencimiento"]));
        $fecha_vencimiento_mes=date("m",strtotime($datos_ingreso["fecha_vencimiento"]));
        $fecha_vencimiento_ano=date("Y",strtotime($datos_ingreso["fecha_vencimiento"]));
        }
        
// Comprueba si es un transpaso de otra cuenta
$busca_coste_asociado=db_query("SELECT * FROM gestion_costes WHERE id_ingreso=".$id_ingreso);
$id_coste=0;
if (db_num_rows($busca_coste_asociado)!=0) {
        $datos_coste=db_fetch_array($busca_coste_asociado);
        $id_coste=$datos_coste["id_coste"];
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
                document.ingreso.importe.value=document.ingreso.importe.value.replace(",",".");
                var mensajes_error="";
                if (document.ingreso.importe.value=="") {
                        mensajes_error=mensajes_error+"Es obligatorio introducir el importe.\u000A";errores=1;
                                document.ingreso.importe.style.color="#FF0000";
                        } else {
                        if (isNaN(document.ingreso.importe.value)) {
                                mensajes_error=mensajes_error+"El importe debe ser un número.\u000A";errores=1;
                                document.ingreso.importe.style.color="#FF0000";
                                }
                        }

                // Comprueba fechas
                la_fecha=document.ingreso.fecha_entrada_mes.value+"/"+document.ingreso.fecha_entrada_dia.value+"/"+document.ingreso.fecha_entrada_ano.value
                if (isDate(la_fecha)!=true) {
                                            mensajes_error=mensajes_error+"La fecha de entrada introducida no es correcta.\u000A";errores=1;
                                            document.ingreso.fecha_entrada_mes.style.color="#FF0000";
                                            document.ingreso.fecha_entrada_dia.style.color="#FF0000";
                                            document.ingreso.fecha_entrada_ano.style.color="#FF0000";
                                            }
                if ((document.ingreso.tipo_ingreso.value=="2")||(document.ingreso.tipo_ingreso.value=="3")) {
                la_fecha=document.ingreso.fecha_vencimiento_mes.value+"/"+document.ingreso.fecha_vencimiento_dia.value+"/"+document.ingreso.fecha_vencimiento_ano.value
                if (isDate(la_fecha)!=true) {
                                            mensajes_error=mensajes_error+"La fecha de vencimiento introducida no es correcta.\u000A";errores=1;
                                            document.ingreso.fecha_vencimiento_mes.style.color="#FF0000";
                                            document.ingreso.fecha_vencimiento_dia.style.color="#FF0000";
                                            document.ingreso.fecha_vencimiento_ano.style.color="#FF0000";
                                            }
                        }
                
                if (errores==0) {
                        document.ingreso.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }

//-->
</script>
<table width="75%" border=0 cellpadding=0 cellspacing=0>
<form name="ingreso" method="POST" action="guarda_ingreso.php?cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>">
<input type="hidden" name="id_ingreso" value="<?=$id_ingreso;?>">
<input type="hidden" name="id_coste" value="<?=$id_coste;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Ingreso</b></font><?php 
                if ($id_coste!=0) {
                ?>
                <br><font size=1>Este ingreso proviene de otra cuenta propia, al modificar estos datos se modificarán los datos del coste asociado.</font>
                <?php 
                }
                ?>
                </td>
        </tr>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha entrada:</b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_entrada_dia" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_entrada_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_entrada_mes" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_entrada_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_entrada_ano" class="formulario_tipo">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+5; $c_ano>=strftime("%Y")-10; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_entrada_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select></font></td>
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de ingreso:</b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="tipo_ingreso" class="formulario_tipo">
                                <?php 
                                // Tipos de ingreso
                                /*       1... efectivo
                                         2... Talón bancario
                                         3... Pagaré
                                         4... Transferencia/Remesa
                                         5... Abono
                                */
                                ?>
                                <option value="1"<?php if ($datos_ingreso["tipo_ingreso"]=="1") { echo " selected"; }?>>Efectivo</option>
                                <option value="2"<?php if ($datos_ingreso["tipo_ingreso"]=="2") { echo " selected"; }?>>Talón bancario</option>
                                <option value="3"<?php if ($datos_ingreso["tipo_ingreso"]=="3") { echo " selected"; }?>>Pagaré</option>
                                <option value="4"<?php if ($datos_ingreso["tipo_ingreso"]=="4") { echo " selected"; }?>>Transferencia/Remesa</option>
                                <option value="5"<?php if ($datos_ingreso["tipo_ingreso"]=="5") { echo " selected"; }?>>Abono</option>
                                </select>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF" colspan=3><font size=1 face="<?= $fuente_general; ?>"><b>Fecha vencimiento</b> (solo talones y pagarés)<b>:</b></font></td>
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
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Núm. albarán: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="num_albaran" size="25" class="formulario_tipo" value="<?=$datos_ingreso["num_albaran"];?>"></font></td>
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Núm. Factura: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="num_factura" size="25" class="formulario_tipo" value="<?=$datos_ingreso["num_factura"];?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Cliente: </b></font></td>
                                <td colspan=3 bgcolor="#ffffff"><select size=1 name="cliente" class="formulario_tipo">
                                <option value="">Selecciona el cliente</option>
                                <?php 
                                $num_clientes_con_albaranes_pend=0;
                                $consulta_clientes = odbc_exec($odbc, "SELECT * FROM TABLA_CIENTES ORDER BY Razon_social") or die (odbc_errormsg());
                                while($datos_clientes = odbc_fetch_array($consulta_clientes)){

                                // Comprobamos si tiene pedidos
                                        $consulta2="SELECT * FROM PEDIDOS_CLIENTES WHERE ID_CLIENTE=".$datos_clientes["id_cliente"];
                                        $consulta_factura = odbc_exec($odbc, $consulta2) or die (odbc_errormsg());

                                        $countQueryString = "SELECT count(*) as results FROM (".$consulta2.")";
                                        $count = odbc_exec ($odbc, $countQueryString);
                                        $numRecords = odbc_result ($count, "results");

                                if ($numRecords!=0) {
                                $num_clientes_con_albaranes_pend=$num_clientes_con_albaranes_pend+1;
                                ?>
                                <option value="<?=$datos_clientes["id_cliente"];?>"<?php if ($datos_ingreso["cliente"]==$datos_clientes["id_cliente"]) { echo " selected"; }?>><?=$datos_clientes["Razon_social"];?></option>
                                <?php 
                                        }
                                }
                                if ($num_clientes_con_albaranes_pend==0) {
                                ?>
                                <option value="">No hay clientes en la base de datos</option>
                                <?php 
                                }
                                ?>
                                </select></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Importe: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="importe" size="10" class="formulario_tipo" value="<?=$datos_ingreso["importe"];?>"> €</font></td>
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Cuenta: </b></font></td>
                                <td align="left" bgcolor="#FFFFFF"><font size=1 face="<?= $fuente_general; ?>"><select name="cuenta_ingreso" style="font-size: 10px" size=1>
                                <option value="1"<?php if ($datos_ingreso["cuenta_ingreso"]=="1") { echo " selected"; }?>>Caja (contado)</option>
                                <option value="2"<?php if ($datos_ingreso["cuenta_ingreso"]=="2") { echo " selected"; }?>>La Caixa</option>
                                <option value="3"<?php if ($datos_ingreso["cuenta_ingreso"]=="3") { echo " selected"; }?>>BANCAJA</option>
                                <option value="4"<?php if ($datos_ingreso["cuenta_ingreso"]=="4") { echo " selected"; }?>>BANCAJA Credito</option>
                                <option value="5"<?php if ($datos_ingreso["cuenta_ingreso"]=="5") { echo " selected"; }?>>Caja Madrid</option>
                                <option value="6"<?php if ($datos_ingreso["cuenta_ingreso"]=="6") { echo " selected"; }?>>BBVA</option>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#EFEFEF"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td align="left" bgcolor="#FFFFFF" colspan=3>
                                <textarea class="defaut" style="FONT-SIZE: 11px; FONT-FAMILY: Arial" name="observaciones" rows="4" cols="82"><?=$datos_ingreso["observaciones"];?></textarea>
                                </td>
                        </tr>
                        </table>
                        <br>
                        <input type="button" value="Cancelar" onclick="window.location.href='ingresos.php?cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>'" style="font-size: 10px"> <input type="button" onclick="enviar_formulario();" value="Guardar cambios" class="formulario_tipo">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
} else {
?>
<p align="center"><font size=4>ERROR: No se ha encontrado el ingreso solicitado</font><br><br><font size=3><a href="javascript:history.go(-1);"><< Volver</a></font></p>
<?php 
}
 db_close();
 odbc_close($odbc);
 require("../configuracion/parte_inferior.php"); ?>
