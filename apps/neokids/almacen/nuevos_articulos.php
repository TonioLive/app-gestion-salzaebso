<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<style type="text/css">
<!--
body {  background-color: <?=$fondo_web;?>; font-family: <?=$fuente_general;?>; font-size: x-small; font-style: normal; line-height: normal; font-weight: normal; font-variant: normal; color: <?=$color_fuente;?>; text-decoration: none;}
a {  font-style: normal; font-weight: normal; color: <?=$color_fuente;?>; text-decoration: none}
a:hover {  font-style: normal; font-weight: normal; text-decoration: underline}

.opciones_menu
{
a {font-style: normal; font-weight: normal; color: <?=$color_fuente_opciones?>; text-decoration: none}
a:hover {font-style: normal; font-weight: normal; text-decoration: underline}
}

.submenuStyle {
    position: absolute;
    visibility: hidden;
}
.menuStyle {
        visibility: visible;
}
.m
{
    FONT-SIZE: x-small;
    COLOR: <?=$color_fuente_menu;?>;
    FONT-FAMILY: <?=$fuente_general;?>;
    TEXT-DECORATION: none
}

.menu
{
a {font-style: normal; font-weight: normal; color: <?=$color_fuente_menu;?>; text-decoration: none}
a:hover {font-style: normal; font-weight: normal; text-decoration: underline}
}

.formulario_tipo {font-size: 10px}

#capa1{position:absolute; top:250px; left:100px; width:10px; height:10px; clip:rect(0px,10px,10px,0px); background-color:<?=$fondo_web;?>; layer-background-color:#<?=$fondo_web;?>; visibility: hidden}

-->
</style>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu_ancho.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
<?php 
$id_pedido=$_GET["id_pedido"];

// Busca articulos genéricos que pueden pedirse

$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_material_empresa WHERE id_pedido_empresa=?', 's', array($id_pedido));

$datos_pedido=db_fetch_array($busca_datos_pedido);

?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                    <form name="nuevo_pedido" method="POST" action="guarda_pedido.php">
                        <input type="hidden" name="id_pedido" value="<?=$id_pedido?>">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Añadir material a pedido</b></font><br><font size=2 face="<?= $fuente_general; ?>"><b>Pedido Nº: <?=$id_pedido?></b><br><br>
                                </font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_detalle=db_query("SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.ARTICULO_GENERICO<>'' AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=1 GROUP BY tarifa.ARTICULO_GENERICO ORDER BY tarifa.CATEGORIA, tarifa.ARTICULO_GENERICO");
                                ?><br>
                                <?php 
                                $contador_filas=0;
                                $categoria_actual=0;
                                while($datos_detalle=db_fetch_array($busca_detalle)) {
                                
                                if ($categoria_actual!=$datos_detalle["CATEGORIA"]) {
                                
                                if ($categoria_actual==0) {
                                        $coletilla2="<b>Cantidad</b><br>Introduce aquí<br>la cantidad que necesitas";
                                        } else {
                                        $coletilla2="&nbsp;";
                                        ?>
                                        </table>
                                        <?php 
                                        }
                                
                                $categoria_actual=$datos_detalle["CATEGORIA"];
                                $busca_nombre_familia=db_query("SELECT * FROM tipos_iva_servicios WHERE id_tipo_iva=$categoria_actual");
                                if (db_num_rows($busca_nombre_familia)!=0) {
                                        $datos_familia_actual=db_fetch_array($busca_nombre_familia);
                                        $nombre_familia_actual=$datos_familia_actual["categoria"];
                                        }
                                ?>
                                <table width=75% cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" id="capa_detalles_o_<?=$categoria_actual?>" style="display: block">
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left" width="40%"><font size=2 face="<?= $fuente_general; ?>"><a href='javascript: mostrar("<?=$categoria_actual?>")'><img src="/imagen/menu/mas.gif" border=0> <b><?=$nombre_familia_actual;?></b></a></font></td>
                                        <td align="center" width="20%"><font size=2 face="<?= $fuente_general; ?>"><?=$coletilla2?></font></td>
                                </tr>
                                </table>
                                <table width=75% cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" id="capa_detalles_m_<?=$categoria_actual?>" style="display: none">
                                <tr bgcolor="#efefef" valign="bottom">
                                        <td align="left" width="40%"><font size=2 face="<?= $fuente_general; ?>"><a href='javascript: ocultar("<?=$categoria_actual?>")'><img src="/imagen/menu/menos.gif" border=0> <b><?=$nombre_familia_actual;?></b></a></font></td>
                                        <td align="center" width="20%"><font size=2 face="<?= $fuente_general; ?>"><?=$coletilla2?></font></td>
                                </tr>
                                <?php 
                                }
                                
                                $contador_filas++;

                                $nombre_articulo=$datos_detalle["ARTICULO_GENERICO"];

                                $unidad_medida=$datos_detalle["unidad_medida"];
                                if (is_numeric($unidad_medida)) {
                                        $unidad_medida="";
                                        }
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <input type="hidden" name="nombre_generico_<?=$contador_filas;?>" value="<?=$datos_detalle["ARTICULO_GENERICO"]?>">
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_articulo;?></font></td>
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><input type="text" size=5 name="cantidad_<?=$contador_filas;?>" value="<?=$cantidad;?>" style="text-align= right" onblur="comprueba_cantidades(<?=$contador_filas;?>)"> <?=$unidad_medida?> <?=$pendiente_de_servir?></font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="window.close()" value="Cerrar"> <input type="button" onclick="guardar()" value="Añadir a pedido"><br><br>
                                </td>
                        </tr>
                        <input type="hidden" name="contador_filas" value="<?=$contador_filas;?>">
                    </form>
                </table>
<script language="Javascript">

function mostrar(id_capa) {
        document.getElementById('capa_detalles_o_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_m_'+id_capa).style.display='block';
        }

function ocultar(id_capa) {
        document.getElementById('capa_detalles_m_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_o_'+id_capa).style.display='block';
        }

function comprueba_cantidades(fila) {
        var errores=0;
        var mensajes_error="";

        cantidad=eval("self.document.nuevo_pedido.cantidad_"+fila+".value");
        nombre_fila=eval("self.document.nuevo_pedido.nombre_generico_"+fila+".value");
        
        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }

        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                }
        }
function guardar() {
        var errores=0;
        var mensajes_error="";
        for (fila=1;fila<=<?=$contador_filas;?>;fila++) {
        cantidad=eval("self.document.nuevo_pedido.cantidad_"+fila+".value");
        nombre_fila=eval("self.document.nuevo_pedido.nombre_generico_"+fila+".value");
        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        if (isNaN(cantidad)) {
                                mensajes_error=mensajes_error+"La cantidad de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        }
        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                } else {
                document.nuevo_pedido.action="guarda_articulos.php?r=1";
                document.nuevo_pedido.submit();
                }
                
        }
self.focus();
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
