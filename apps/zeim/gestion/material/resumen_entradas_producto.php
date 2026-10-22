<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<?php 
$id_producto=$_GET["id_producto"];

$consulta_articulo = db_query_params('SELECT * FROM tarifa WHERE ID=?', 's', array($id_producto)) or die (db_query());
$datos_articulo = db_fetch_array($consulta_articulo);

$familia=$datos_articulo["CATEGORIA"];

$ARTICULO=strtoupper($datos_articulo["ARTICULO"]);

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Resumen de entradas y salidas (sin contar ventas a alumnos) de <i><?=$ARTICULO?></i></b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Hora</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Acumulado</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                                <?php 
                                $busca_material=db_query_params('SELECT * FROM gestionguarderias.movimientos_material WHERE id_material=? ORDER BY fecha,hora', 's', array($id_producto));
                                
                                $total=0;

                                while($datos_material=db_fetch_array($busca_material)) {
                                
                                $total=$total+$datos_material["cantidad"];
                                
                                ?>
                        <tr>
                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_material["concepto"];?></font></td>
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_material["fecha"]));?></font></td>
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("H:i:s",strtotime($datos_material["hora"]));?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_material["cantidad"];?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$total;?></font></td>
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><a href="#" onclick="if (confirm('¿Quieres eliminar este movimiento (<?=$datos_material["concepto"]?>)?')) { self.location.href='eliminar_mov.php?id_mov=<?=$datos_material["id_mov"]?>&id_producto=<?=$id_producto?>' }"><img src="borrar.gif" border=0 align="absmiddle"></a></font></td>
                        </tr>
                                <?php 
                        }
                        ?>
                                <td align="right" colspan=4><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$total;?></font></td>
                         </table>
                         <br>
                         <input type="button" onclick="window.opener.history.go(0);window.close();" value="Salir"> <input type="button" value="Añadir movimiento de material" onclick="window.location.href='nueva_entrada.php?id_producto=<?=$id_producto?>'"><br><br>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
