<?php 
 require("../configuracion/config.php");
 require("../configuracion/autentifica.php");

$id_pedido=$_GET["id_pedido"];
$id_fila=$_GET["id_fila"];
$id_quitar_fila=$_GET["id_quitar_fila"];

$formulario_name="albaran";

$num_errores=0;

if ($num_errores==0)
{

$odbc = odbc_connect ($access_database, '', '') or die( "Could Not Connect to ODBC Database!" );

$texto_boton="Añadir a pedido";

// Eliminar la fila seleccionada
if (strlen($id_quitar_fila)!=0) {
        $quitafila="DELETE * FROM DETALLES_PEDIDO WHERE ID_DETALLE=".$id_quitar_fila;
        odbc_exec($odbc, $quitafila) or die (odbc_errormsg());
        }
        
// Busca datos de la fila
if (strlen($id_fila)!=0) {
        $consultafila="SELECT * FROM DETALLES_PEDIDO,TARIFA WHERE DETALLES_PEDIDO.ARTICULO=TARIFA.COD AND DETALLES_PEDIDO.ID_DETALLE=".$id_fila;
        $consulta_datos_fila = odbc_exec($odbc, $consultafila) or die (odbc_errormsg());

        $datos_de_fila=odbc_fetch_array($consulta_datos_fila);
        $texto_boton="Guardar cambios";
        }

// Busca datos del pedido
$consulta_datos_pedido = odbc_exec($odbc, "SELECT * FROM PEDIDOS_CLIENTES WHERE ID_PEDIDO=".$id_pedido) or die (odbc_errormsg());

$datos_del_pedido=odbc_fetch_array($consulta_datos_pedido);


// Busca el ultimo albaran facturado en el ejercicio anterior
$consulta_alabran_ultimo="SELECT ULTIMOS_ALBARANES.EJERCICIO, [PRIMERO]-1 AS ULTIMO FROM ULTIMOS_ALBARANES WHERE EJERCICIO='".$datos_del_pedido["EJERCICIO"]."';";
$ejecuta_alabran_ultimo = odbc_exec($odbc, $consulta_alabran_ultimo) or die (odbc_errormsg());
$id_alabran_ultimo = odbc_result ($ejecuta_alabran_ultimo, "ULTIMO");
$id_albaran_actual=($datos_del_pedido["ID_PEDIDO"]-$id_alabran_ultimo);

$num_pedido="00000".$id_albaran_actual;
$num_pedido=substr($num_pedido,strlen($num_pedido)-5,5)."/".$datos_del_pedido["EJERCICIO"];

// Fechas
$mes_actual=date("m",strtotime($datos_del_pedido["FECHA_PEDIDO"]));
$anio_actual=date("Y",strtotime($datos_del_pedido["FECHA_PEDIDO"]));

$id_cliente=$datos_del_pedido["ID_CLIENTE"];
$observaciones=$datos_del_pedido["OBSERVACIONES"];

if (strlen($id_cliente)!=0) {

// Busca datos del cliente
$consulta_datos_cliente = odbc_exec($odbc, "SELECT * FROM TABLA_CIENTES WHERE id_cliente=".$id_cliente) or die (odbc_errormsg());

$datos_del_cliente=odbc_fetch_array($consulta_datos_cliente);

$descuento_por_cliente=$datos_del_cliente["CONDICIONES_CLIENTE_MATERIAL"];


$descuento_por_familia=0;
$descuento_por_articulo=0;
$oferta_articulo=0;
$oferta_familia=0;
if (strlen($id_fila)!=0) {
        // Busca descuentos

$consulta_descueto_por_tarifa = odbc_exec($odbc, "SELECT * FROM DESCUENTOS_POR_ARTICULO WHERE id_cliente=".$id_cliente) or die (odbc_errormsg());
$descuento_articulo=odbc_fetch_array($consulta_descueto_por_tarifa);
$descuento_articulo["descuento"];


$consulta_oferta_por_tarifa = odbc_exec($odbc, "SELECT * FROM OFERTAS_POR_ARTICULO WHERE mes=".$mes_actual." AND anio=".$anio_actual) or die (odbc_errormsg());
$oferta_articulo=odbc_fetch_array($consulta_oferta_por_tarifa);
$oferta_articulo["descuento"];

$consulta_descueto_por_familia = odbc_exec($odbc, "SELECT * FROM DESCUENTOS_POR_FAMILIA WHERE id_cliente=".$id_cliente) or die (odbc_errormsg());
$descueto_por_familia=odbc_fetch_array($consulta_descueto_por_familia);
$descueto_por_familia["descuento"];


$consulta_oferta_por_familia = odbc_exec($odbc, "SELECT * FROM OFERTAS_POR_FAMILIA WHERE mes=".$mes_actual." AND anio=".$anio_actual) or die (odbc_errormsg());
$oferta_familia=odbc_fetch_array($consulta_oferta_por_familia);
$oferta_familia["descuento"];
        }
?>
<html>
<head>
<meta name="Description" content="<?php  echo $meta_description; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords; ?>">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo1.php"); ?>
</style>
<script language="javascript">
self.focus();
</script>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
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

var descuento_por_cliente=<?=$descuento_por_cliente;?>;
var descuento_por_familia=0;
var descuento_por_articulo=0;
var oferta_articulo=0;
var oferta_familia=0;
var descuento_aplicado=<?=$descuento_por_cliente;?>;

<?php  require("script_tarifas.php"); ?>

/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;
var fila_seleccionada="";

/**
 * Sets/unsets the pointer and marker in browse mode
 *
 * @param   object    the table row
 * @param   interger  the row number
 * @param   string    the action calling this script (over, out or click)
 * @param   string    the default background color
 * @param   string    the color to use for mouseover
 * @param   string    the color to use for marking a row
 *
 * @return  boolean  whether pointer is set or not
 */
function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, id_detalle_pedido)
{
    var theCells = null;

    // 1. Pointer and mark feature are disabled or the browser can't get the
    //    row -> exits
    if ((thePointerColor == '' && theMarkColor == '')
        || typeof(theRow.style) == 'undefined') {
        return false;
    }

    // 2. Gets the current row and exits if the browser can't get it
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }

    // 3. Gets the current color...
    var rowCellsCnt  = theCells.length;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    // 3.1 ... with DOM compatible browsers except Opera that does not return
    //         valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        domDetect    = true;
    }
    // 3.2 ... with other browsers
    else {
        currentColor = theCells[0].style.backgroundColor;
        domDetect    = false;
    } // end 3

    // 4. Defines the new color
    // 4.1 Current color is the default one
    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            fila_seleccionada = id_detalle_pedido;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.2 Current color is the pointer one
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            fila_seleccionada = id_detalle_pedido;
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.3 Current color is the marker one
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            fila_seleccionada = "";
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
        }
    } // end 4

    // 5. Sets the new color...
    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].setAttribute('bgcolor', newColor, 0);
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
} // end of the 'setPointer()' function

function comprobar_y_enviar() {
        var errores="";
        
        // Comprueba fecha
        la_fecha=self.document.<?=$formulario_name;?>.fecha_pedido_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_pedido_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_pedido_ano.value
        if (isDate(la_fecha)!=true) { errores=errores+"- La fecha de pedido introducida no es correcta.\u000A"; }

        if ((self.document.<?=$formulario_name;?>.fecha_entrega_mes.value!="--")||(self.document.<?=$formulario_name;?>.fecha_entrega_dia.value!="--")||(+self.document.<?=$formulario_name;?>.fecha_entrega_ano.value!="----")) {
                la_fecha1=self.document.<?=$formulario_name;?>.fecha_entrega_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_entrega_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_entrega_ano.value
                if (isDate(la_fecha1)!=true) { errores=errores+"- La fecha de entrega introducida no es correcta.\u000A"; }
              }
        
                if (document.albaran.cod.value=="") {
                        errores=errores+"- No has seleccionado ningún artículo.\n";
                        document.albaran.uds.value=0
                        }
                if (document.albaran.uds.value==0) {
                        errores=errores+"- No has indicado las unidades.\n";
                        document.albaran.uds.value=0
                        }
                if (isNaN(document.albaran.uds.value)) {
                        errores=errores+"- Las unidades deben ser un dato numérico.\n";
                        document.albaran.uds.value=0
                        }
                if (isNaN(document.albaran.pvp.value)) {
                        errores=errores+"- El precio del artículo debe ser un dato numérico.\n";
                        document.albaran.pvp.value=0
                        }
                if (isNaN(document.albaran.dto_porcentaje.value)) {
                        errores=errores+"- El descuento debe ser un dato numérico.\n";
                        document.albaran.dto_porcentaje.value=0
                        } else {
                        if (document.albaran.dto_porcentaje.value<0) {
                                errores=errores+"- El descuento debe ser un valor popsitivo.\n";
                                document.albaran.dto_porcentaje.value=0;
                                }
                        if (document.albaran.dto_porcentaje.value>100) {
                                errores=errores+"- El descuento no puede ser mayor que el 100%.\n";
                                document.albaran.dto_porcentaje.value=100;
                                }
                        }
        if (errores=="") {
                top.window.document.albaran.submit();
                } else {
                alert("Revisa los siguientes errores: \n\n"+errores);
                }
}

function comprueba_al_entrar() {
        codigo_actual=document.albaran.cod.value;
        familia_actual=document.albaran.familia.value;
        }

function comprueba_id() {
        if (codigo_actual!=document.albaran.cod.value) {
        
        var fila_donde_esta=-1;
        for ( var i = 0; i < tarifa.length; i++ ) {
             if (tarifa[i][2]==document.albaran.cod.value) {
                        fila_donde_esta=i;
                        break;
                        }
                }
        if (fila_donde_esta==-1) {
                alert("La referencia introducida no corresponde a ningún artículo de la base de datos");
                document.albaran.cod.value="";
                } else {
                var fila_donde_esta_t=-1;
                // Busca familia y la selecciona
                for ( var i = 0; i < familias.length; i++ ) {
                     if (familias[i][1]==tarifa[fila_donde_esta][1]) {
                                fila_donde_esta_t=i;
                                break;
                                }
                        }
                document.albaran.familia.value=familias[fila_donde_esta_t][0];
                genera_listado();
                document.albaran.articulo.value=document.albaran.cod.value;
                comprueba_articulo();
                }

        }
        }
        
function comprueba_familia() {
        if (familia_actual!=document.albaran.familia.value) {
        
                // Borra todos los campos
                document.albaran.cod.value="";
                document.albaran.pvp.value="";
                document.albaran.uds.value="";
                document.albaran.base_imponible.value="";
                document.albaran.iva_porcentaje.value="";
                document.albaran.iva_importe.value="";
                document.albaran.importe_total.value="";
                document.albaran.dto_importe.value="";
                document.albaran.dto_porcentaje.value="";
                descuento_por_familia=0;
                descuento_por_articulo=0;
                oferta_articulo=0;
                oferta_familia=0;
                calcular_descuento();
                
                genera_listado();
                comprueba_articulo();
                }
        }

function genera_listado() {
                // Borra la lista de artículos
                var longitud_select = document.albaran.articulo.length;
                if (longitud_select>0) {
                        for (var cont=(longitud_select-1);cont>=0;cont=cont-1) {
                                document.albaran.articulo.remove(cont);
                                }
                        }

                // Genera la nueva lista de artículos

                document.albaran.articulo.disabled=true;

                var oOption = document.createElement("OPTION");
                document.albaran.articulo.options.add(oOption);
                oOption.innerText = "Selecciona el artículo";
                oOption.value = "0";

                for ( var i = 0; i < tarifa.length; i++ ) {
                     if (tarifa[i][1]==document.albaran.familia.options[document.albaran.familia.selectedIndex].text) {
                                var oOption = document.createElement("OPTION");
                                document.albaran.articulo.options.add(oOption);
                                oOption.innerText = tarifa[i][2]+"-"+tarifa[i][4];
                                oOption.value = tarifa[i][2];
                                }
                        }
                document.albaran.articulo.disabled=false;
                
                // Busca el tipo de IVA
                for ( var i = 0; i < familias.length; i++ ) {
                     if (familias[i][1]==document.albaran.familia.options[document.albaran.familia.selectedIndex].text) {
                                document.albaran.iva_porcentaje.value=Math.round(familias[i][2]*10000)/100;
                                break;
                                }
                        }
                // Comprueba descuento y aplica el más alto
                // Descuento por familia al cliente
                fila_donde_esta_df=-1;
                for ( var i = 0; i < descuentos_por_familia.length; i++ ) {
                     if (descuentos_por_familia[i][0]==document.albaran.familia.value) {
                                fila_donde_esta_df=i;
                                break;
                                }
                        }
                if (fila_donde_esta_df!=-1) {
                        descuento_por_familia=descuentos_por_familia[fila_donde_esta_df][1];
                        }
                // Ofertas por familia este mes
                fila_donde_esta_of=-1;
                for ( var i = 0; i < ofertas_por_familia.length; i++ ) {
                     if (ofertas_por_familia[i][0]==document.albaran.familia.value) {
                                fila_donde_esta_of=i;
                                break;
                                }
                        }
                if (fila_donde_esta_of!=-1) {
                        oferta_familia=ofertas_por_familia[fila_donde_esta_of][1];
                        }
                calcular_descuento();

        }
        
function comprueba_articulo() {
        if (document.albaran.articulo.value!=0) {
                document.albaran.cod.value=document.albaran.articulo.value;
                
                // Rellena información sobre el artículo
                for ( var i = 0; i < tarifa.length; i++ ) {
                     if (tarifa[i][2]==document.albaran.articulo.value) {
                                fila_donde_esta_a=i;
                                break;
                                }
                        }
                if (tarifa[i][3]=="C") {
                                document.albaran.pvp.value=tarifa[fila_donde_esta_a][5];
                        } else {
                                document.albaran.pvp.value=tarifa[fila_donde_esta_a][6];
                        }
                // Comprueba descuento y aplica el más alto
                // Descuento por tarifa al cliente
                fila_donde_esta_d=-1;
                for ( var i = 0; i < descuentos_por_tarifa.length; i++ ) {
                     if (descuentos_por_tarifa[i][0]==tarifa[fila_donde_esta_a][0]) {
                                fila_donde_esta_d=i;
                                break;
                                }
                        }
                if (fila_donde_esta_d!=-1) {
                        descuento_por_articulo=descuentos_por_tarifa[fila_donde_esta_d][1];
                        }
                // Ofertas por tarifa este mes
                fila_donde_esta_o=-1;
                for ( var i = 0; i < ofertas_por_tarifa.length; i++ ) {
                     if (ofertas_por_tarifa[i][0]==tarifa[fila_donde_esta_a][0]) {
                                fila_donde_esta_o=i;
                                break;
                                }
                        }
                if (fila_donde_esta_o!=-1) {
                        oferta_articulo=ofertas_por_tarifa[fila_donde_esta_o][1];
                        }
                
                calcular_descuento();
                document.albaran.dto_porcentaje.value=Math.round(10000*descuento_aplicado)/100;
                }
        
                // Borra la lista de lotes
                var longitud_select = document.albaran.lote.length;
                if (longitud_select>0) {
                        for (var cont=(longitud_select-1);cont>=0;cont=cont-1) {
                                document.albaran.lote.remove(cont);
                                }
                        }
                // Genera listado de lotes
                document.albaran.lote.disabled=true;

                var oOption = document.createElement("OPTION");
                document.albaran.lote.options.add(oOption);
                oOption.innerText = "Lote";
                oOption.value = "0";
                for ( var i = 0; i < lotes.length; i++ ) {
                     if (lotes[i][2]==document.albaran.articulo.value) {
                                var oOption = document.createElement("OPTION");
                                document.albaran.lote.options.add(oOption);
                                oOption.innerText = lotes[i][1];
                                oOption.value = lotes[i][0];
                                }
                        }
                if (document.albaran.lote.length>1) {
                        document.albaran.lote.disabled=false;
                        }
                calcula_totales()

        }
        
function calcula_totales() {
                var errores="";
                if (isNaN(document.albaran.uds.value)) {
                        errores=errores+"- Las unidades deben ser un dato numérico.\n\n";
                        document.albaran.uds.value=0
                        }
                if (isNaN(document.albaran.pvp.value)) {
                        errores=errores+"- El precio del artículo debe ser un dato numérico.\n\n";
                        document.albaran.pvp.value=0
                        }
                if (isNaN(document.albaran.dto_porcentaje.value)) {
                        errores=errores+"- El descuento debe ser un dato numérico.\n\n";
                        document.albaran.dto_porcentaje.value=0
                        } else {
                        if (document.albaran.dto_porcentaje.value<0) {
                                errores=errores+"- El descuento debe ser un valor popsitivo.\n\n";
                                document.albaran.dto_porcentaje.value=0;
                                }
                        if (document.albaran.dto_porcentaje.value>100) {
                                errores=errores+"- El descuento no puede ser mayor que el 100%.\n\n";
                                document.albaran.dto_porcentaje.value=100;
                                }
                        }
                        
                if (errores=="") {
                        document.albaran.dto_importe.value=Math.round(((Math.round(100*(document.albaran.pvp.value*document.albaran.uds.value))/100)*(Math.round(100*(document.albaran.dto_porcentaje.value))/10000))*100)/100;
                        document.albaran.base_imponible.value=Math.round(((Math.round(100*(document.albaran.pvp.value*document.albaran.uds.value))/100)-(document.albaran.dto_importe.value))*100)/100;
                        document.albaran.iva_importe.value=Math.round(100*document.albaran.base_imponible.value*(document.albaran.iva_porcentaje.value/100))/100;
                        document.albaran.importe_total.value=Math.round(100*(Number(document.albaran.base_imponible.value)+Number(document.albaran.iva_importe.value)))/100;
                        } else {
                        alert("Revisa los siguientes datos:\n\n"+errores);
                        }
                }
                
function ver_condiciones() {
        texto_oferta_familia="";
        if (oferta_familia>0) {
                texto_oferta_familia="Oferta para todos los articulos de la familia: "+(100*oferta_familia)+" %\n";
                }

        texto_oferta_articulo="";
        if (oferta_articulo>0) {
                texto_oferta_articulo="Oferta para el artículo seleccionado: "+(100*oferta_articulo)+" %\n";
                }
        ofertas_especiales="";
        if ((texto_oferta_familia!="")||(texto_oferta_articulo!="")) {
                ofertas_especiales="OFERTAS VIGENTES DURANTE EL MES EN EL QUE SE REALIZA EL PEDIDO\n\n"+texto_oferta_familia+texto_oferta_articulo+"\n";
                }
         alert("DESCUENTOS APLICADOS A ESTE ARTÍCULO\n\nCONDICIONES PACTADAS CON EL CLIENTE:\n\nDescuento cliente: "+(100*descuento_por_cliente)+" %\nDescuento por familia: "+(100*descuento_por_familia)+" %\nDescuento por artículo: "+(100*descuento_por_articulo)+" %\n\n"+ofertas_especiales+"Descento mínimo que se debe aplicar según condiciones pactadas: "+(100*descuento_aplicado)+" %");
         }
         
function calcular_descuento() {
        descuento_articulo_aplicado=descuento_por_articulo;
        if (oferta_articulo>=descuento_por_articulo) {
                descuento_articulo_aplicado=oferta_articulo;
                }
        descuento_familia_aplicado=descuento_por_familia;
        if (oferta_familia>=descuento_por_articulo) {
                descuento_familia_aplicado=oferta_familia;
                }
        if ((descuento_por_cliente>=descuento_familia_aplicado)&&(descuento_por_cliente>=descuento_articulo_aplicado)) {
                descuento_aplicado=descuento_por_cliente;
                }
        if ((descuento_familia_aplicado>=descuento_por_cliente)&&(descuento_familia_aplicado>=descuento_articulo_aplicado)) {
                descuento_aplicado=descuento_familia_aplicado;
                }
        if ((descuento_articulo_aplicado>=descuento_por_cliente)&&(descuento_articulo_aplicado>=descuento_familia_aplicado)) {
                descuento_aplicado=descuento_articulo_aplicado;
                }
        }

function edicion_fila() {
        if (fila_seleccionada!="") {
                if (confirm("Si has comenzado a introducir un articulo y no has pulsado el botón 'Añadir a pedido' se pederá la información introducida.\n\n¿Deseas continuar?")) {
                        window.location.href="<?=$_SERVER['PHP_SELF']?>?id_pedido=<?=$id_pedido;?>&id_fila="+fila_seleccionada;
                        }
           } else {
           alert("Selecciona antes la fila que deseas editar.\n\nLa fila estará seleccionada cuando quede resaltada en color naranja.");
           }
        }
        
function eliminar_fila() {
        if (fila_seleccionada!="") {
                if (confirm("Se va a eliminar la fila seleccionada.\n\n Tambien ten en cuenta que si has comenzado a introducir un articulo y no has pulsado el botón 'Añadir a pedido' se pederá la información introducida.\n\n¿Deseas continuar?")) {
                        window.location.href="<?=$_SERVER['PHP_SELF']?>?id_pedido=<?=$id_pedido;?>&id_quitar_fila="+fila_seleccionada;
                        }
           } else {
           alert("Selecciona antes la fila que deseas eliminar.\n\nLa fila estará seleccionada cuando quede resaltada en color naranja.");
           }
        }
        
function finalizar() {

        var errores=0;

        var mensajes_error="";

        // Comprueba fecha
        la_fecha=self.document.<?=$formulario_name;?>.fecha_pedido_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_pedido_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_pedido_ano.value
        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de pedido introducida no es correcta.\u000A";errores=1; }

        if ((self.document.<?=$formulario_name;?>.fecha_entrega_mes.value!="--")||(self.document.<?=$formulario_name;?>.fecha_entrega_dia.value!="--")||(+self.document.<?=$formulario_name;?>.fecha_entrega_ano.value!="----")) {
                la_fecha1=self.document.<?=$formulario_name;?>.fecha_entrega_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_entrega_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_entrega_ano.value
                if (isDate(la_fecha1)!=true) { mensajes_error=mensajes_error+"La fecha de entrega introducida no es correcta.\u000A";errores=1; }
              }

        if (errores==0) {
                        document.albaran.action="finalizar_edicion_albaran.php";
                        document.albaran.submit();
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
        
function modif_cliente(){
        window.open('nuevo_cliente.php?u='+self.document.<?=$formulario_name;?>.id_cliente.value,'clientes','toolbar=no,resizable=no,width=600,height=585');
        }
        
</script>
<table width="830" border=0 cellpadding=0 cellspacing=0>
<form name="albaran" action="editar_albaranes2.php" method="POST">
<input type="hidden" name="id_pedido" value="<?=$id_pedido;?>">
<input type="hidden" name="id_fila" value="<?=$id_fila;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Edición del albarán Nº <?=$num_pedido;?></b></font><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cliente:</b></font></td>
                                <td><select size=1 style="font-size: 10px" name="id_cliente">
                                <?php 
                                $consulta_clientes = odbc_exec($odbc, "SELECT * FROM TABLA_CIENTES ORDER BY Razon_social") or die (odbc_errormsg());
                                while($datos_clientes = odbc_fetch_array($consulta_clientes)){
                                ?>
                                <option value="<?=$datos_clientes["id_cliente"];?>"<?php  if ($id_cliente==$datos_clientes["id_cliente"]) { print " selected"; } ?>><?=$datos_clientes["Razon_social"];?></option>
                                <?php 
                                }
                                ?>
                                </select> <input type="button" value="Editar" style="font-size: 10px" onclick="modif_cliente()"></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Ejercicio fiscal:</b></font></td>
                                <td><select size=1 style="font-size: 10px" name="ejercicio">
                                <?php 
                                for ($ejercicio=(date("Y")+1);$ejercicio>=$ano_inicio_actividad;$ejercicio--) {
                                ?>
                                <option value="<?=$ejercicio;?>"<?php  if ($ejercicio==$datos_del_pedido["EJERCICIO"]) { print " selected"; } ?>><?=$ejercicio;?></option>
                                <?php 
                                }
                                ?>
                                </select></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo de albarán:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>">
                                <input name="tipo_albaran" type="radio" value="0"<?php if ($datos_del_pedido["tipo_albaran"]==0) { print " checked"; }?>> Normal &nbsp; &nbsp;
                                <input name="tipo_albaran" type="radio" value="1"<?php if ($datos_del_pedido["tipo_albaran"]==1) { print " checked"; }?>> Abono &nbsp; &nbsp;
                                <input name="tipo_albaran" type="radio" value="2"<?php if ($datos_del_pedido["tipo_albaran"]==2) { print " checked"; }?>> Muestra &nbsp; &nbsp;
                                <input name="tipo_albaran" type="radio" value="3"<?php if ($datos_del_pedido["tipo_albaran"]==3) { print " checked"; }?>> No Facturar
                                </font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha de pedido:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_pedido_dia" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{

  if ($c_dia<10)
  {

    $dia_a="0".$c_dia;
  }
    else
  {

    $dia_a=$c_dia;
  }

?>
    <option value="<?php    echo $dia_a; ?>"<?php if ($dia_a==date("d",strtotime($datos_del_pedido["FECHA_PEDIDO"]))) { print " selected"; } ?>><?php    echo $dia_a; ?></option>
    <?php 

}

?>
        </select>/<select name="fecha_pedido_mes" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{

  if ($c_mes<10)
  {

    $mes_a="0".$c_mes;
  }
    else
  {

    $mes_a=$c_mes;
  }

?>
    <option value="<?php    echo $mes_a; ?>"<?php if ($mes_a==date("m",strtotime($datos_del_pedido["FECHA_PEDIDO"]))) { print " selected"; } ?>><?php    echo $mes_a; ?></option>
    <?php 

}

?>
        </select>/<select name="fecha_pedido_ano" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php 
for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
{

  if ($c_ano<10)
  {

    $ano_a="0".$c_ano;
  }
    else
  {

    $ano_a=$c_ano;
  }

?>
    <option value="<?php    echo $ano_a; ?>"<?php if ($c_ano==date("Y",strtotime($datos_del_pedido["FECHA_PEDIDO"]))) { print " selected"; } ?>><?php    echo $ano_a; ?></option>
    <?php 

}

?>
</select> <font size=1><a href="javascript:selecciona_fecha('fecha_pedido','<?=$formulario_name?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha de entrega:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="fecha_entrega_dia" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{

  if ($c_dia<10)
  {

    $dia_a="0".$c_dia;
  }
    else
  {

    $dia_a=$c_dia;
  }

?>
    <option value="<?php    echo $dia_a; ?>"<?php if ($dia_a==date("d",strtotime($datos_del_pedido["FECHA_ENTREGA"]))) { print " selected"; } ?>><?php    echo $dia_a; ?></option>
    <?php 

}

?>
        </select>/<select name="fecha_entrega_mes" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{

  if ($c_mes<10)
  {

    $mes_a="0".$c_mes;
  }
    else
  {

    $mes_a=$c_mes;
  }

?>
    <option value="<?php    echo $mes_a; ?>"<?php if ($mes_a==date("m",strtotime($datos_del_pedido["FECHA_ENTREGA"]))) { print " selected"; } ?>><?php    echo $mes_a; ?></option>
    <?php 

}

?>
        </select>/<select name="fecha_entrega_ano" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php 
for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
{

  if ($c_ano<10)
  {

    $ano_a="0".$c_ano;
  }
    else
  {

    $ano_a=$c_ano;
  }

?>
    <option value="<?php    echo $ano_a; ?>"<?php if ($c_ano==date("Y",strtotime($datos_del_pedido["FECHA_ENTREGA"]))) { print " selected"; } ?>><?php    echo $ano_a; ?></option>
    <?php 

}

?>
</select> <font size=1><a href="javascript:selecciona_fecha('fecha_entrega','<?=$formulario_name?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font></td>
                        </tr>
                         </table>
                         <br>
                    <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=9><font size=2 face="<?= $fuente_general; ?>"><small>Selecciona la familia y a continuación el producto o escribe la referencia del producto (si la conoces).</small></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#EFEFEF">
                          <td align="center" width="65"><font size=1 face="<?= $fuente_general; ?>"><b>Ref.</b></font></td>
                          <td align="center" colspan=3 width="260"><font size=1 face="<?= $fuente_general; ?>"><b>Familia</b></font></td>
                          <td align="center" colspan=5 width="500"><font size=1 face="<?= $fuente_general; ?>"><b>Producto</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" style="font-size: 10px" name="cod" size=8 onblur="comprueba_id()" onfocus="comprueba_al_entrar()"<?php if (strlen($id_fila)!=0) { ?>value="<?=$datos_de_fila["COD"]?>"<?php  }?>></font></td>
                          <td align="center" colspan=3><font size=1 face="<?= $fuente_general; ?>"><select size=1 style="font-size: 10px" name="familia" onfocus="comprueba_al_entrar()" onchange="comprueba_familia()">
                                <option value="">Familia</option>
                                <?php 
                                $consulta_familias = odbc_exec($odbc, "SELECT * FROM TIPOS_IVA ORDER BY categoria") or die (odbc_errormsg());
                                while($datos_familias = odbc_fetch_array($consulta_familias)){
                                ?>
                                <option value="<?=$datos_familias["id_tipo_iva"];?>"<?php if ((strlen($id_fila)!=0) and ($datos_de_fila["CATEGORIA"]==$datos_familias["categoria"])) { print " selected"; }?>><?=str_replace(chr(34),"'",$datos_familias["categoria"]);?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                          <td align="left" colspan=5><font size=1 face="<?= $fuente_general; ?>"><select size=1 style="font-size: 10px" name="articulo" onfocus="comprueba_al_entrar()" onchange="comprueba_articulo()"<?php  if (strlen($id_fila)==0) { ?> disabled<?php  } ?>>
                                  <option value="0">Selecciona el artículo</option>
                                <?php 
                                if (strlen($id_fila)!=0) {
                                $consulta_tarifa = odbc_exec($odbc, "SELECT * FROM TARIFA WHERE CATEGORIA='".$datos_de_fila["CATEGORIA"]."' ORDER BY ARTÍCULO") or die (odbc_errormsg());
                                while($datos_tarifa = odbc_fetch_array($consulta_tarifa)){
                                        ?>
                                        <option value="<?=$datos_tarifa["COD"];?>"<?php if ($datos_de_fila["ARTÍCULO"]==$datos_tarifa["ARTÍCULO"]) { print " selected"; }?>><?=str_replace(chr(34),"'",$datos_tarifa["ARTÍCULO"]);?></option>
                                        <?php 
                                        }
                                }
                                ?>
                                  </select></font></td>
                        </tr>
                    </table>
                    <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr bgcolor="#EFEFEF">
                          <td align="center" width="105"><font size=1 face="<?= $fuente_general; ?>"><b>Num. Lote</b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b>Uds.</b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b>PVP</b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b><a href="javascript:ver_condiciones()">DTO %</a></b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b><a href="javascript:ver_condiciones()">DTO €</a></b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b>Base imp.</b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b>IVA %</b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b>IVA €</b></font></td>
                          <td align="center" width="90"><font size=1 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><select size=1 style="font-size: 10px" name="lote"<?php  if (strlen($id_fila)==0) { ?> disabled<?php  } ?>>
                                <option value="0">Lote</option>
                                <?php 
                                if (strlen($id_fila)!=0) {
                                $consulta_lote = odbc_exec($odbc, "SELECT * FROM LOTES WHERE id_producto='".$datos_de_fila["COD"]."' ORDER BY fecha_fabricacion DESC") or die (odbc_errormsg());
                                while($datos_lote = odbc_fetch_array($consulta_lote)){
                                        ?>
                                        <option value="<?=$datos_lote["id_lote"];?>"<?php if ($datos_de_fila["ID_LOTE"]==$datos_lote["id_lote"]) { print " selected"; }?>><?=$datos_lote["numero_lote"];?></option>
                                        <?php 
                                        }
                                }
                                ?>
                                  </select></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="uds" size=3 style="font-size: 10px;text-align: right" onblur="calcula_totales()"<?php if (strlen($id_fila)!=0) { ?>value="<?=$datos_de_fila["CANTIDAD"]?>"<?php  }?>></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="pvp" size=6 style="font-size: 10px;text-align: right" onblur="calcula_totales()"<?php if (strlen($id_fila)!=0) { ?>value="<?=round($datos_de_fila["PRECIO"],2)?>"<?php  }?>></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="dto_porcentaje" size=4 style="font-size: 10px;text-align: right" onblur="calcula_totales()"<?php if (strlen($id_fila)!=0) { ?>value="<?=round($datos_de_fila["DTO"]*100,2)?>"<?php  }?>></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="dto_importe" readonly size=6 style="font-size: 10px;text-align: right" onblur="calcula_totales()"></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="base_imponible" readonly size=6 style="font-size: 10px;text-align: right"></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="iva_porcentaje" readonly size=4 style="font-size: 10px;text-align: right"<?php if (strlen($id_fila)!=0) { ?>value="<?=round($datos_de_fila["TIPO_IVA"]*100,2)?>"<?php  }?>></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="iva_importe" readonly size=6 style="font-size: 10px;text-align: right"></font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="importe_total" readonly size=6 style="font-size: 10px;text-align: right"></font></td>
                        </tr>
                     </table>
                        <br><br>
                        <input type="button" value="<?=$texto_boton;?>" style="font-size: 10px" onclick="comprobar_y_enviar();">  <input type="button" value="Editar fila resaltada en naranja" style="font-size: 10px" onclick="edicion_fila();">  <input type="button" value="Eliminar fila resaltada en naranja" style="font-size: 10px" onclick="eliminar_fila();">
                        <br><br>
                        <table border=1 bordercolor="<?= $fondo_at; ?>" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Cod.</font></td>
                          <td align="center" width="40%"><font size=1 face="<?= $fuente_general; ?>">Concepto</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">P. Uni.</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Uds.</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Importe</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">DTO %</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">DTO Importe</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Base Imp.</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">IVA %</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">IVA Importe</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">TOTAL</font></td>
                        </tr>
                        <?php 
                        $consulta3="SELECT * FROM DETALLES_PEDIDO,TARIFA_COD_COMO_T WHERE DETALLES_PEDIDO.ARTICULO=TARIFA_COD_COMO_T.CODIGO AND DETALLES_PEDIDO.id_pedido=".$id_pedido;

                        $total_base=0;
                        $total_base_4=0;
                        $total_base_7=0;
                        $total_base_16=0;
                        $total_iva=0;
                        $total_iva_4=0;
                        $total_iva_7=0;
                        $total_iva_16=0;
                        $total_dto=0;

                        $consulta_detalle = odbc_exec($odbc, $consulta3) or die (odbc_errormsg());
                        while($datos_detalle = odbc_fetch_array($consulta_detalle)){
                               $total_linea=round($datos_detalle["PRECIO"]*$datos_detalle["CANTIDAD"],2);
                               $base_imponible=$total_linea*(1-$datos_detalle["DTO"]);
                               $total_iva_inc=$base_imponible*(1+$datos_detalle["TIPO_IVA"]);

                               $total_dto=$total_dto+($total_linea*$datos_detalle["DTO"]);
                               $iva_linea=$base_imponible*$datos_detalle["TIPO_IVA"];

                               $total_base=$total_base+$base_imponible;
                               $total_iva=$total_iva+$iva_linea;
                               switch($datos_detalle["TIPO_IVA"])
                                        {
                                        case 0.04:
                                                $total_iva_4=$total_iva_4+$iva_linea;
                                                $total_base_4=$total_base_4+$base_imponible;
                                        break;
                                        case 0.07:
                                                $total_iva_7=$total_iva_7+$iva_linea;
                                                $total_base_7=$total_base_7+$base_imponible;
                                        break;
                                        case 0.16:
                                                $total_iva_16=$total_iva_16+$iva_linea;
                                                $total_base_16=$total_base_16+$base_imponible;
                                        break;
                                        }
                                if ($resaltar==$datos_detalle["CODIGO"]) {
                                        $fondo_linea="#C4FFE3";
                                        } else {
                                        $fondo_linea="#FFFFFF";
                                        }

                                $consulta_lote = odbc_exec($odbc, "SELECT * FROM LOTES WHERE id_lote=".$datos_detalle["ID_LOTE"]) or die (odbc_errormsg());
                                $numero_lote = odbc_result($consulta_lote,"numero_lote");
                                if ($numero_lote!="") {
                                                $numero_lote=" (".$numero_lote.")";
                                                }
                               ?>
                                <tr valign="top" bgcolor="<?=$fondo_linea;?>" onmouseover="setPointer(this, 0, 'over', '#FFFFFF', '#CCFFCC', '#FFCC99', <?=$datos_detalle["ID_DETALLE"];?>);" onmouseout="setPointer(this, 0, 'out', '#FFFFFF', '#CCFFCC', '#FFCC99', <?=$datos_detalle["ID_DETALLE"];?>);" onmousedown="setPointer(this, 0, 'click', '#FFFFFF', '#CCFFCC', '#FFCC99', <?=$datos_detalle["ID_DETALLE"];?>);">
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_detalle["CODIGO"];?></font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_detalle["ARTÍCULO"].$numero_lote;?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_detalle["PRECIO"], 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_detalle["CANTIDAD"];?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_linea, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_detalle["DTO"]*100, 2, ',', '.');?> %</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format(($total_linea*$datos_detalle["DTO"]), 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($base_imponible, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_detalle["TIPO_IVA"]*100, 2, ',', '.');?> %</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($iva_linea, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_iva_inc, 2, ',', '.');?> €</font></td>
                                </tr>
                                <?php 
                                }
                        if ($datos_pedido["recargo_equivalencia"]==true) {
                                $total_recargo=round(($total_base*0.04),2);
                                } else {
                                $total_recargo=0;
                                }
                        ?>
                        <table>
                        <font size=1 face="<?= $fuente_general; ?>">&nbsp;</font>
                        <table border=1 bordercolor="<?= $fondo_at; ?>" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr valign="bottom" bgcolor="#ececec">
                                  <td align="center" rowspan=2><font size=1 face="<?= $fuente_general; ?>">Total<br>Descuento</font></td>
                                  <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>">IVA</font></td>
                                  <td align="center" colspan=3><font size=1 face="<?= $fuente_general; ?>">Totales albarán</font></td>
                                </tr>
                                <tr valign="bottom" bgcolor="#ececec">
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>">4 %</font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>">7 %</font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>">16 %</font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>">Total IVA</font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>">Base imponible</font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>">R.E. (4 %)</font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>">Total Albarán</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_dto, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_iva_4, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_iva_7, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_iva_16, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_iva, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_base, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_recargo, 2, ',', '.');?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_base-$total_recargo+$total_iva, 2, ',', '.');?> €</font></td>
                                </tr>
                         </table><br>
                        <table border=1 bordercolor="<?= $fondo_at; ?>" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr valign="top" bgcolor="#ececec">
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>">Observaciones</font></td>
                                  <td align="left"><textarea name="observaciones" cols=150 rows=2 style="font-family: Arial; font-size: 11px"><?=$observaciones?></textarea></td>
                                </tr>
                         </table>
                         <br>
                        <input type="button" value="Cancelar" style="font-size: 10px" onclick="window.close();"> <input type="button" value="Salir >>" style="font-size: 10px" onclick="finalizar();"><br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
if (strlen($id_fila)!=0) {
?>
<script language="Javascript">
calcular_descuento();
calcula_totales();
</script>
<?php 
        }

 odbc_close($odbc);
 } else {
 ?>
 <p align="center">No se ha seleccionado el cliente<br><br>
 <a href="<?=$url_web;?>/facturacion/facturar_albaranes.php"><< Volver</a>
 </p>
 <?php 
 }
 } else {
?><br><br>
<font size=2>
<p align="center"><b><i>
Se han detectado errores al cumplimentar el formulario.<br>Por favor, vuelva atras para revisar
los siguientes puntos:<br>
</i></b></p>
<p align="center">
<font face="<?php  echo $fuente_general; ?>">
<table width="75%" bgcolor="#ececec">
<?php 
    for ($contador=1; $contador<=$num_errores; $contador=$contador+1)
    {
      print "<tr><td><font face=\"Verdana\" size=1 color=\"".$color_fuente."\"><b>- $errores[$contador]</b></font></td></tr>";
    }
?>
</table>
<?php 
  print "</small></small><br><br><b>Vuelve al paso anterior y corrige los errores.</b><br><br>";
  print "<input type=\"button\" value=\"<< Volver al paso anterior\" onclick=\"history.go(-1)\" id=1 name=1></p></font>";
 }
?>
</body>
</html>
