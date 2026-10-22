<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_pedido_empresa=$_GET["id_pedido"];
$id_pedido=$id_pedido_empresa;

$array_id_pedidos=explode(",",$id_pedido);

$filtro_pedidos="";
$filtro_pedidos2="";

for ($c=0;$c<=count($array_id_pedidos);$c++) {
        if (strlen($array_id_pedidos[$c])!=0) {
                $filtro_pedidos=$filtro_pedidos."id_pedido_empresa=".$array_id_pedidos[$c]." OR ";
                $filtro_pedidos2=$filtro_pedidos2."detalles_pedido_material.id_pedido_empresa=".$array_id_pedidos[$c]." OR ";
                }
        }
        
if (strlen($filtro_pedidos)!=0) {
        $filtro_pedidos1=substr($filtro_pedidos,0,strlen($filtro_pedidos)-3);
        $filtro_pedidos3=substr($filtro_pedidos2,0,strlen($filtro_pedidos2)-4);
        }

$busca_datos_lista=db_query("SELECT * FROM pedidos_material_empresa WHERE $filtro_pedidos1") or die(db_error());

$datos_lista=db_fetch_array($busca_datos_lista);

$texto_observaciones=$datos_lista["observaciones"];

if (strlen($texto_observaciones)!=0) {
        $texto_observaciones=urldecode($texto_observaciones);
        }

$texto_encabezado="material aulas ".$nombre_centro_actual;

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

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
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
$busca_alimentos=db_query("SELECT *,pedidos_material.observaciones AS obs_pedido FROM pedidos_material,detalles_pedido_material,centro_aulas WHERE (pedidos_material.id_pedido=detalles_pedido_material.id_pedido) AND pedidos_material.id_aula=centro_aulas.id_aula AND ($filtro_pedidos3) AND (detalles_pedido_material.cantidad<>0 OR detalles_pedido_material.cantidad_original<>0) AND centro_aulas.id_centro=$centro_actual ORDER BY pedidos_material.id_aula,pedidos_material.id_pedido,detalles_pedido_material.generico_solicitado") or die(db_error());
while($listado_alimentos=db_fetch_array($busca_alimentos)) {


if ($aula_seleccionada!=$listado_alimentos["id_aula"]) {

// Total proveedor
if ($aula_seleccionada!="") {
?>
  <tr valign="top">
    <td align="right" bgcolor="#efefef"><font size=2><b>Observaciones</b></font></td>
    <td align="left" bgcolor="#ffffff" colspan=3><font size=2><?=$texto_observaciones?></font></td>
  </tr>
  </table><br>
  <table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
<?php 
        }

?>
  <tr valign="top">
    <td align="left" colspan=4 bgcolor="#efefef"><font size=3><b><?=$listado_alimentos["nombre"]?></b></font></td>
  </tr>
  <tr valign="top">
    <td align="left" width=180 bgcolor="#efefef"><font size=2><b>Artículo</b></font></td>
    <td align="left" width=330 bgcolor="#efefef"><font size=2><b>&nbsp;</b></font></td>
    <td align="center" width=140 bgcolor="#efefef"><font size=2><b>Cantidad recibida</b></font></td>
    <td align="center" width=140 bgcolor="#efefef"><font size=2><b>Cantidad pedido aula</b></font></td>
  </tr>
<?php 
        $aula_seleccionada=$listado_alimentos["id_aula"];
        $texto_observaciones="";
        }

if ($pedido_actual!=$listado_alimentos["id_pedido"]) {
        $texto_observaciones=$texto_observaciones.$listado_alimentos["obs_pedido"]."<br><br>";
        $pedido_actual=$listado_alimentos["id_pedido"];
        }

?>
<tr bgcolor="#ffffff">
        <td align="left" colspan=2><font size=2><?=urldecode($listado_alimentos["generico_solicitado"]);?></font></td>
        <td align="right"><font size=2><?=number_format($listado_alimentos["cantidad"],2,",",".");?></font></td>
        <td align="right"><font size=2><?=number_format($listado_alimentos["cantidad_original"],2,",",".");?></font></td>
</tr>
<?php 
}
?>
  <tr valign="top">
    <td align="right" bgcolor="#efefef"><font size=2><b>Observaciones</b></font></td>
    <td align="left" bgcolor="#ffffff" colspan=3><font size=2><?=$texto_observaciones?></font></td>
  </tr>
  </table><br>
</center></div>
</div>
</body>
</html>
