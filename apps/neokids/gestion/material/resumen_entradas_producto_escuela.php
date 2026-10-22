<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<?php 
$id_producto=$_GET["id_producto"];
$id_escuela=$_GET["id_escuela"];

$consulta_articulo = db_query_params('SELECT * FROM tarifa WHERE ID=?', 's', array($id_producto)) or die (db_query());
$datos_articulo = db_fetch_array($consulta_articulo);

$familia=$datos_articulo["CATEGORIA"];
$estado_boton_add="";

$ARTICULO=strtoupper($datos_articulo["ARTICULO"]);
                               
                                $coletilla_db="gestionpiscina";
                                
                                if ($id_escuela==10) {
                                	if ($datos_articulo["ID_SP"]>0) {
	                                	$id_producto=$datos_articulo["ID_SP"];
	                                	$coletilla_db="escuelamunicipal";
	                                	$estado_boton_add="disabled ";
                                		}
                                	}                                	

$busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=? ORDER BY nombre_centro', 's', array($id_escuela));

$datos_escuela=db_fetch_array($busca_escuelas);

$nombre_escuela=strtoupper($datos_escuela["nombre_centro"]);

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Resumen de entradas y salidas (sin contar ventas a alumnos) de <i><?=$ARTICULO?></i> en la escuela <?=$nombre_escuela?></b></font>
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
                                $busca_material=db_query("SELECT * FROM $coletilla_db.movimientos_material_escuelas WHERE id_material=$id_producto AND id_escuela=$id_escuela ORDER BY fecha,hora");
                                
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
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php  if ($estado_boton_add=="") { ?><a href="#" onclick="if (confirm('¿Quieres eliminar este movimiento (<?=$datos_material["concepto"]?>)?')) { self.location.href='eliminar_mov_escuela.php?id_mov=<?=$datos_material["id_mov"]?>&id_producto=<?=$id_producto?>&id_escuela=<?=$id_escuela?>' }"><img src="borrar.gif" border=0 align="absmiddle"></a><?php  } ?> </font></td>
                        </tr>
                                <?php 
                        }
                        ?>
                                <td align="right" colspan=4><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$total;?></font></td>
                         </table>
                         <br>
                         <input type="button" onclick="window.opener.history.go(0);window.close();" value="Salir"> <input type="button" <?=$estado_boton_add?>value="Añadir movimiento de material" onclick="window.location.href='nueva_entrada_escuela.php?id_producto=<?=$id_producto?>&id_escuela=<?=$id_escuela?>'"><br><br>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
