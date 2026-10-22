<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_pedido_empresa=$_GET["id_pedido"];
$id_pedido=$id_pedido_empresa;

$busca_datos_lista=db_query("SELECT * FROM pedidos_material_empresa WHERE id_pedido_empresa=$id_pedido") or die(db_error());

$datos_lista=db_fetch_array($busca_datos_lista);

$texto_observaciones=$datos_lista["observaciones"];

if (strlen($texto_observaciones)!=0) {
        $texto_observaciones=urldecode($texto_observaciones);
        }

$texto_encabezado="material aulas";

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
self.print();
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<br><br>
<div align="center"><center><font size=3><b>Lista de entrega <?=$texto_encabezado;?></b></font>
<br><br>
<table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
<?php 

$aula_seleccionada="";

$nombre_proveedor="";
$texto_observaciones="";
$pedido_actual=0;

$unidad_medida[0]="g.&nbsp;&nbsp;";
$unidad_medida[1]="ml.";
$busca_alimentos=db_query("SELECT detalles_pedido_material.id_pedido_centro, centro_aulas.id_centro,detalles_pedido_material.generico_solicitado, sum(detalles_pedido_material.cantidad) AS totalCantidad FROM pedidos_material,detalles_pedido_material,centro_aulas WHERE pedidos_material.id_pedido=detalles_pedido_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_pedido_empresa=$id_pedido AND detalles_pedido_material.cantidad<>0 GROUP BY centro_aulas.id_centro,detalles_pedido_material.generico_solicitado ORDER BY centro_aulas.id_centro,detalles_pedido_material.generico_solicitado") or die(db_error());

$id_pedido_centro=0;

while($listado_alimentos=db_fetch_array($busca_alimentos)) {

if ($aula_seleccionada!=$listado_alimentos["id_centro"]) {

$id_pedido_centro=$listado_alimentos["id_pedido_centro"];

$busca_datos_centro_actual=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alimentos["id_centro"]);

$datos_centro_actual=db_fetch_array($busca_datos_centro_actual);

// Total proveedor
if ($aula_seleccionada!="") {
?>
  <tr valign="top">
    <td align="right" bgcolor="#efefef"><font size=2><b>Observaciones</b></font></td>
    <td align="left" bgcolor="#ffffff" colspan=2><font size=2><?=$texto_observaciones?></font></td>
  </tr>
  </table><br>
  <table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
<?php 
        }

?>
  <tr valign="top">
    <td align="left" colspan=3 bgcolor="#efefef"><font size=3><b><?=$datos_centro_actual["nombre_centro"]?></b></font></td>
  </tr>
  <tr valign="top">
    <td align="left" width=180 bgcolor="#efefef"><font size=2><b>Artículo</b></font></td>
    <td align="left" width=330 bgcolor="#efefef"><font size=2><b>&nbsp;</b></font></td>
    <td align="center" width=280 bgcolor="#efefef"><font size=2><b>Cantidad</b></font></td>
  </tr>
<?php 
        $aula_seleccionada=$listado_alimentos["id_centro"];
        $texto_observaciones="";
        
        $busca_observaciones_centro=db_query("SELECT pedidos_material.observaciones AS obs_pedido FROM pedidos_material,detalles_pedido_material,centro_aulas WHERE pedidos_material.id_pedido=detalles_pedido_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_pedido_empresa=$id_pedido AND detalles_pedido_material.cantidad<>0 AND centro_aulas.id_centro=$aula_seleccionada GROUP BY detalles_pedido_material.id_pedido ORDER BY detalles_pedido_material.id_pedido") or die(db_error());
        while($lista_observaciones_centro=db_fetch_array($busca_observaciones_centro)) {
                if (strlen($lista_observaciones_centro["obs_pedido"])!=0) {
                        $texto_observaciones=$texto_observaciones.$lista_observaciones_centro["obs_pedido"]."<br>";
                        }
                }
                
// Busca observaciones pedido centro
if ($id_pedido_centro!=0) {
	$busca_pedido_centro=db_query("SELECT * FROM pedidos_material_centros WHERE id_pedido_centro=".$id_pedido_centro);
	
	if (db_num_rows($busca_pedido_centro)!=0) {
	
		$datos_pedido_centro=db_fetch_array($busca_pedido_centro);
		
		if (strlen($datos_pedido_centro["observaciones"])!=0) {
                        $texto_observaciones.=$datos_pedido_centro["observaciones"]."<br>";
                        }
	
		}
	
	}                
                
        }       
?>
<tr bgcolor="#ffffff">
        <td align="left" colspan=2><font size=2><?=urldecode($listado_alimentos["generico_solicitado"]);?></font></td>
        <td align="right"><font size=2><?=number_format($listado_alimentos["totalCantidad"],2,",",".");?></font></td>
</tr>
<?php 
}
?>
  <tr valign="top">
    <td align="right" bgcolor="#efefef"><font size=2><b>Observaciones</b></font></td>
    <td align="left" bgcolor="#ffffff" colspan=2><font size=2><?=$texto_observaciones?></font></td>
  </tr>
  </table><br>
</center></div>
</div>
</body>
</html>
