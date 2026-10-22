<?php  require("../../configuracion/parte_superior.php"); ?>
<?php 

$dia_ini=$_GET["fecha_ini"];
$dia_fin=$_GET["fecha_fin"];

?>
<br><br>Consumo de cocina entre los días <?=date("d/m/Y",strtotime($dia_ini))?> y <?=date("d/m/Y",strtotime($dia_fin))?><br><br>
<table width=100% cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#000000">
        <tr bgcolor="#efefef">
                        <td width=50%><font size=2>Producto</font></td>
                        <td align="right" width=25%><font size=3>Cantidad consumida</font></td>
        </tr>
<?php 

                        // Busca cantidades consumidas en la tabla

                        $busca_consumo=db_query_params('SELECT id_producto_tarifa,sum(cantidad) AS total_consumo FROM menu_productos_consumo_cocina WHERE menu_productos_consumo_cocina.dia>=? AND menu_productos_consumo_cocina.dia<=? GROUP BY menu_productos_consumo_cocina.id_producto_tarifa', 'ss', array((string)($dia_ini), (string)($dia_fin)));

                        while($datos_consumo=db_fetch_array($busca_consumo)) {

                        $cantidad_consumida=$datos_consumo["total_consumo"];

                        // Busca en tarifa
                        
                        $busca_tarifa=db_query_params('SELECT * FROM tarifa WHERE id=?', 's', array($datos_consumo["id_producto_tarifa"]));
                        
                        if (db_num_rows($busca_tarifa)!=0) {
                                $datos_tarifa=db_fetch_array($busca_tarifa);
                                
                                $nombre_articulo=$datos_tarifa["ARTICULO"];
                                
                                } else {
                                $nombre_articulo="Borrado";
                                }

                        ?>
                        <tr bgcolor="#ffffff">
                        <td width=50%><font size=2><?=$nombre_articulo?></font></td>
                        <td align="right" width=25%><font size=3><?=$cantidad_consumida?></font></td>
                        </tr>
                        <?php 
                        }

                        ?>
</table>
<?php  require("../../configuracion/parte_inferior.php"); ?>

