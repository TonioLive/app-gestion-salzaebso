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
window.moveTo(0,0);
// window.sizeTo(screen.width,screen.height);
window.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 

// Parametros
$id_aula=$_GET["id_aula"];
$id_pedido=$_GET["id_pedido"];

// Busca pedido

$busca_datos_pedido=db_query("SELECT * FROM pedidos_material WHERE id_pedido=$id_pedido");

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];
$fecha_pedido=date("d/m/Y",strtotime($datos_pedido["fecha_pedido"]));
$observaciones=$datos_pedido["observaciones"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_articulo1.php";

if (strlen($id_servicio)==0) {
                $mensaje="Agregar material al pedido";
                // Valores por defecto
                $id_servicio=0;
                $familia="";
                $articulo="";
                $precio="";
                $cantidad=1;
        }
?>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">

<?php  require("script_tarifas_aula.php"); ?>

function genera_listado() {
                // Borra la lista de artículos
                var longitud_select = document.datos_articulo.articulo.length;
                if (longitud_select>0) {
                        for (var cont=(longitud_select-1);cont>=0;cont=cont-1) {
                                document.datos_articulo.articulo.remove(cont);
                                }
                        }

                // Genera la nueva lista de artículos

                document.datos_articulo.articulo.disabled=true;

                var oOption = document.createElement("OPTION");
                document.datos_articulo.articulo.options.add(oOption);
                oOption.innerText = "Selecciona el artículo";
                oOption.value = "0";
                hay_art=false;
                for ( var i = 0; i < tarifa.length; i++ ) {
                     if (tarifa[i][1]==document.datos_articulo.familia.options[document.datos_articulo.familia.selectedIndex].value) {
                                var oOption = document.createElement("OPTION");
                                document.datos_articulo.articulo.options.add(oOption);
                                oOption.innerText = tarifa[i][4];
                                oOption.value = tarifa[i][2];
                                hay_art=true;
                                }
                        }
                if (hay_art==false) {
                                alert("No hay servicios en la categoría seleccionada");
                        } else {
                                document.datos_articulo.articulo.disabled=false;
                        }
        }
        
function comprueba_al_entrar() {
        familia_actual=document.datos_articulo.familia.value;
        }
        
function comprueba_y_envia() {
        var errores="";
        var mensajes_error="";

        if (self.document.datos_articulo.familia.value=="") {
                mensajes_error=mensajes_error+"Falta seleccionar la familia.\n";
                errores=1;
                }

        if (self.document.datos_articulo.articulo.value=="0") {
                mensajes_error=mensajes_error+"Falta seleccionar el material.\n";
                errores=1;
                }
                

        if (isNaN(self.document.datos_articulo.cantidad.value)) {
                                                        mensajes_error=mensajes_error+"La cantidad debe ser un número.\n";
                                                        errores=1;
                                                        }

        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+mensajes_error);
                } else {
                self.document.datos_articulo.submit();
                }
        }
        
        
function selecciona_unidad() {
        var indice_array=-1;

        for ( var i = 0; i < tarifa.length; i++ ) {
                     if (tarifa[i][0]==document.datos_articulo.articulo.value) {
                                indice_array=i;
                                }
                        }
        if (indice_array==-1) {
                self.document.datos_articulo.unidad.value="Unidades";
                } else {
                if (tarifa[indice_array][5]!="") {
                                self.document.datos_articulo.unidad.value=tarifa[indice_array][5];
                        } else {
                                self.document.datos_articulo.unidad.value="Unidades";
                        }
                }
        }

</script>
<?php 

?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?><br></b></font><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width=450>
                        <form name="datos_articulo" method="POST" action="nuevo_articulo1.php">
                        <input type="hidden" name="id_pedido" value="<?=$id_pedido?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec" width=100><font size=1 face="<?= $fuente_general; ?>">Categoría:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select size=1 style="font-size: 10px" name="familia" onfocus="comprueba_al_entrar()" onchange="genera_listado()">
                                <option value="">Selecciona la categoria</option>
                                <?php 
                                $consulta_familias = db_query("SELECT * FROM tipos_iva_servicios ORDER BY categoria") or die (db_error());
                                while($datos_familias = db_fetch_array($consulta_familias)){
                                // comprueba si hay articulos
                                $busca_art=db_query("SELECT * FROM tarifa WHERE CATEGORIA=".$datos_familias["id_tipo_iva"]." AND TIPO_SERVICIO=3");
                                if (db_num_rows($busca_art)!=0) {
                                ?>
                                <option value="<?=$datos_familias["id_tipo_iva"];?>"<?php if ($familia==$datos_familias["id_tipo_iva"]) { print " selected"; }?>><?=$datos_familias["categoria"];?></option>
                                <?php 
                                }
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                        <?php 
                        if ($articulo!=0) {
                                $estado_select="";
                                } else {
                                $estado_select=" disabled";
                                }
                        ?>
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Servicio:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select size=1 style="font-size: 10px" name="articulo"<?=$estado_select;?> onchange="selecciona_unidad();">
                                  <option value="0">Selecciona el servicio</option>
                                  <?php 
                                  if (strlen($articulo)!=0) {
                                  $consulta_datos_articulos=db_query("SELECT * FROM tarifa WHERE CATEGORIA=$familia AND TIPO_SERVICIO=3 ORDER BY ARTICULO") or die(db_error());
                                  while($datos_articulos=db_fetch_array($consulta_datos_articulos)){
                                        ?>
                                        <option value="<?=$datos_articulos["ID"];?>"<?php if ($articulo==$datos_articulos["ID"]) { print " selected"; }?>><?=$datos_articulos["ARTICULO"];?></option>
                                        <?php 
                                        }
                                  }
                                  ?>
                                  </select></font></td>
                        </tr>
                        <tr valign="top" id="cantidades" style="display: block">
                          <td align="right" bgcolor="#ececec" width=100><font size=1 face="<?= $fuente_general; ?>">Cantidad:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" onblur="recalcular_precios();" value="<?=$cantidad;?>" name="cantidad" size=10 style="font-size: 10px; text-align: right"> <input type="text" name="unidad" size=10 style="font-size: 10px; background-color: #efefef; border: 1px solid #C0C0C0" readonly></font></td>
                        </tr>
                        </form>
                         </table><br>
                         <input type="button" value="Cancelar" onclick="window.close()" style="font-size: 10px"> <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
</center></div>
</div>
<script languege="Javascript">

</script>
</body>
</html>
