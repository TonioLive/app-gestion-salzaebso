<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<?php 
$id_producto=$_GET["id_producto"];
$id_escuela=$_GET["id_escuela"];

$consulta_articulo = db_query_params('SELECT * FROM gestionguarderias.tarifa WHERE ID=?', 's', array($id_producto)) or die (db_query());
$datos_articulo = db_fetch_array($consulta_articulo);

$familia=$datos_articulo["CATEGORIA"];

$ARTICULO=strtoupper($datos_articulo["ARTICULO"]);

$busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=? ORDER BY nombre_centro', 's', array($id_escuela));

$datos_escuela=db_fetch_array($busca_escuelas);

$nombre_escuela=strtoupper($datos_escuela["nombre_centro"]);

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Resumen de ventas a alumnos de <i><?=$ARTICULO?></i> en la escuela <?=$nombre_escuela?></b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Alumno</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Acumulado</b></font></td>
                        </tr>
                                <?php 
                                $busca_material=db_query_params('SELECT * FROM alumnos,servicios_contratados WHERE alumnos.id_alumno=servicios_contratados.id_alumno AND alumnos.id_centro=? AND servicios_contratados.id_servicio=? ORDER BY servicios_contratados.fecha_inicio', 'ss', array($id_escuela, $id_producto)) or die(db_error()."-1");
                                
                                $total=0;

                                while($datos_material=db_fetch_array($busca_material)) {
                                
                                $total=$total+$datos_material["cantidad"];
                                
                                // Busca datos alumno
                                $nombre_alumno="";
                                
                                $id_usuario_seleccionado=$datos_material["id_usuario"];
                                if ($id_usuario_seleccionado!=0) {
                                                $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado)) or die(db_error()."-4");

                                                if (db_num_rows($consulta_datos_usuario)!=0) {
                                                                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                                                                $nombre_alumno=$datos_usuario["Nombre"]." ".$datos_usuario["Apellido1"]." ".$datos_usuario["Apellido2"];
                                                                }
                                                }
                                ?>
                        <tr>
                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_alumno;?></font></td>
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_material["fecha_inicio"]));?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_material["cantidad"];?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$total;?></font></td>
                        </tr>
                                <?php 
                        }
                        ?>
                                <td align="right" colspan=3><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$total;?></font></td>
                         </table>
                         <br>
                         <input type="button" onclick="window.opener.history.go(0);window.close();" value="Salir"><br><br>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
