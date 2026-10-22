<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$formulario_name="seleccion_fecha_cobro";
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<script src="/js/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="/js/datepicker/locales/bootstrap-datepicker.es.min.js" charset="UTF-8"></script>

<link href="/js/datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">

<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function enviar8() {
                var errores=0;

                var mensajes_error="";
                document.<?=$formulario_name;?>.action="csb19santander_sepa_factura_unica.php";
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.fechacobro_mes.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_dia.value+"/"+self.document.<?=$formulario_name;?>.fechacobro_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"\u000ALa fecha de cobro introducida no es correcta.";errores=1; }

                if (self.document.<?=$formulario_name;?>.razon_social.value=='') {
                    mensajes_error=mensajes_error+"\u000ADebes indicar la razón social o el nombre del cliente.";;errores=1;
                }

                if (self.document.<?=$formulario_name;?>.cif.value=='') {
                    mensajes_error=mensajes_error+"\u000ADebes indicar el CIF, NIF o NIE del cliente.";;errores=1;
                }
                
                if (self.document.<?=$formulario_name;?>.concepto.value=='') {
                    mensajes_error=mensajes_error+"\u000ADebes indicar el concepto del recibo.";;errores=1;
                }
                
                if (self.document.<?=$formulario_name;?>.cuenta.value=='') {
                    mensajes_error=mensajes_error+"\u000ADebes indicar IBAN del cliente.";;errores=1;
                }  else {
                    if (!fn_ValidateIBAN($( "#cuenta" ).val())) {
					mensajes_error=mensajes_error+"\u000AEl nº de cuenta introducido no es un IBAN correcto.";
					errores=1;
					}
                }              

                if (self.document.<?=$formulario_name;?>.importe.value=='') {
                    mensajes_error=mensajes_error+"\u000ADebes indicar el importe del recibo.";;errores=1;
                } else {                
                    if (isNaN(self.document.<?=$formulario_name;?>.importe.value)) {
                        mensajes_error=mensajes_error+"\u000AEl importe debe ser un número. Usa el punto (.) para separar los decimales.";;errores=1;
                    }  
                }             

                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A"+mensajes_error);
                        }
        }
   
        function fn_ValidateIBAN(IBAN) {

//Se pasa a Mayusculas
IBAN = IBAN.toUpperCase();
//Se quita los blancos de principio y final.
IBAN = IBAN.trim();
IBAN = IBAN.replace(/\s/g, ""); //Y se quita los espacios en blanco dentro de la cadena

var letra1,letra2,num1,num2;
var isbanaux;
var numeroSustitucion;
//La longitud debe ser siempre de 24 caracteres
if (IBAN.length != 24) {
    return false;
}

// Se coge las primeras dos letras y se pasan a números
letra1 = IBAN.substring(0, 1);
letra2 = IBAN.substring(1, 2);
num1 = getnumIBAN(letra1);
num2 = getnumIBAN(letra2);
//Se sustituye las letras por números.
isbanaux = String(num1) + String(num2) + IBAN.substring(2);
// Se mueve los 6 primeros caracteres al final de la cadena.
isbanaux = isbanaux.substring(6) + isbanaux.substring(0,6);

//Se calcula el resto, llamando a la función modulo97, definida más abajo
resto = modulo97(isbanaux);
if (resto == 1){
    return true;
}else{
    return false;
}
}

function modulo97(iban) {
var parts = Math.ceil(iban.length/7);
var remainer = "";

for (var i = 1; i <= parts; i++) {
    remainer = String(parseFloat(remainer+iban.substr((i-1)*7, 7))%97);
}

return remainer;
}

function getnumIBAN(letra) {
ls_letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
return ls_letras.search(letra) + 10;
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

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center><br><font size=2><b>Generar orden domiciliación SEPA <br><br></b></font>
<form name="seleccion_fecha_cobro" method="GET" action="csb19.php">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Selecciona la fecha de cobro de los recibos</b></small></small></td>
  </tr>
  <tr bgcolor="#efefef">
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
    </tr>
    <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Razón social cliente</b></small></small></td>
  </tr>
    <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
            <input type="text" name="razon_social" id="razon_social" value="" class="form-control">
        </td>
    </tr>   
    <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>CIF/NIF cliente</b></small></small></td>
  </tr>    
    <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
            <input type="text" name="cif" id="cif" value="" class="form-control">
        </td>
    </tr>  
    <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Concepto recibo</b></small></small></td>
  </tr>    
    <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
            <input type="text" name="concepto" id="concepto" value="" class="form-control">
        </td>
    </tr>  
    <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>IBAN cliente</b></small></small></td>
  </tr>    
    <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
            <input type="text" name="cuenta" id="cuenta" value="" class="form-control">
        </td>
    </tr>  
    </tr>  
    <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Entidad bancaria cliente</b></small></small></td>
  </tr>    
    <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
            <select type="text" name="bic" id="bic" class="form-control">
                <option value=""></option>
                <?php
                $buscaBic=db_query("SELECT * FROM bic_swift ORDER BY nombre;");

                while($entidades=db_fetch_array($buscaBic)) {
                    ?>
                    <option value="<?=$entidades["BIC"]?>"><?=$entidades["nombre"]?> (<?=$entidades["cod_bancario"];?>)</option>
                    <?php
                }

                ?>
            </select>
        </td>
    </tr>     
    <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Importe recibo</b></small></small></td>
  </tr> 
    <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
            <input type="text" name="importe" id="importe" value="" class="form-control">
        </td>
    </tr>                   
  </table>
  </form>
  <br>
  <input type="button" onclick="window.close()" style="font-size: 10px" value="Cancelar"> <input type="button" onclick="enviar8()" style="font-size: 10px" value="Generar recibos Santander formato SEPA ISO 20022 XML"><br>
</center></div>
</div>
</body>
</html>
