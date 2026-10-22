<?php  require("../configuracion/parte_superior_privado.php");

// Busca pedidos penientes de servir

$busca_pedidos_pendientes=db_query("SELECT detalles_pedido_material.generico_solicitado, sum(detalles_pedido_material.cantidad) AS suma_total FROM detalles_pedido_material,pedidos_material,centro_aulas WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_pedido_empresa=0 AND detalles_pedido_material.confirmado=1 AND detalles_pedido_material.id_albaran=0 GROUP BY detalles_pedido_material.generico_solicitado ORDER BY detalles_pedido_material.generico_solicitado") or die(db_error()." -1");

if (db_num_rows($busca_pedidos_pendientes)!=0) {

$busca_nombres_centros=db_query("SELECT * FROM centros ORDER BY id_centro");

$nombres_centros=array();

while($datos_centros=db_fetch_array($busca_nombres_centros)) {
        $nombres_centros[$datos_centros["id_centro"]]=$datos_centros["nombre_centro"];
        }

?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                <form name="pedidos_pendientes" action="guarda_cambios_pedidos_empresa.php" method="POST">
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Relación de pedidos nuevos o pendientes de servir</b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width="100%">
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="left" width="50%" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Material</b></font></td>
                                                <td align="center" width="40%"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                                <td align="center" width="10%"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        </tr>
                                <?php 

                                $contador_filas=0;
                                $nombre_articulo="";

                                while($datos_detalle=db_fetch_array($busca_pedidos_pendientes)) {
                                
                                $contador_filas++;

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left" width="50%" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=$datos_detalle["generico_solicitado"];?></font></td>
                                        <td align="right" width="40%"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_detalle["suma_total"];?></font></td>
                                        <td align="center" width=10%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><input type="checkbox" value="1" name="lista_pedidos_validar_<?=$contador_filas?>"></td>
                                </tr>
                                <tr>
                                <td colspan=4>
                                <table bordercolor="#000000" style="border-collapse: collapse" width=100% border=1 cellpadding=1 cellspacing=0 id="capa_detalles_o_<?=$contador_filas?>" style="display: block">
                                <tr bgcolor="#efefef">
                                        <td><a href='javascript: mostrar("<?=$contador_filas?>")'><img src="/imagen/menu/mas.gif" border=0></a></td>
                                </tr>
                                </table>
                                <table bordercolor="#000000" style="border-collapse: collapse" width=100% border=1 cellpadding=1 cellspacing=0 id="capa_detalles_m_<?=$contador_filas?>" style="display: none">
                                        <tr bgcolor="#efefef" valign="top">
                                        <td align="left" colspan=5><font size=2 face="<?= $fuente_general; ?>"><a href='javascript: ocultar("<?=$contador_filas?>")'><img src="/imagen/menu/menos.gif" border=0></a></font></td>
                                        </tr>

                                <?php 
                                
                                // Detalle aulas
                                $busca_material_aulas=db_query_params('SELECT * FROM detalles_pedido_material,pedidos_material,centro_aulas WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_pedido_empresa=0 AND detalles_pedido_material.confirmado=1 AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.generico_solicitado=? ORDER BY centro_aulas.id_centro, centro_aulas.nombre', 's', array((string)($datos_detalle["generico_solicitado"]))) or die(db_error());
                                // $busca_material_aulas=mysql_query("SELECT * FROM detalles_pedido_material,pedidos_material,centro_aulas WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_albaran=0 AND centro_aulas.id_centro=$centro_actual AND detalles_pedido_material.generico_solicitado='".$datos_detalle["generico_solicitado"]."' AND detalles_pedido_material.id_pedido_empresa=0 AND detalles_pedido_material.confirmado=1 ORDER BY centro_aulas.id_centro, centro_aulas.nombre") or die(mysql_error());
                                
                                $centro_actual_filas=0;
                                $total_centro_actual=0;
                                
                                while($material_aulas=db_fetch_array($busca_material_aulas)) {
                                
                                
                                if ($centro_actual_filas!=$material_aulas["id_centro"]) {
                                        $centro_actual_filas=$material_aulas["id_centro"];
                                        ?>
                                        <tr bgcolor="#efefef" valign="top">
                                        <td align="left" colspan=5><font size=2 face="<?= $fuente_general; ?>"><?=$nombres_centros[$centro_actual_filas];?></font></td>
                                        </tr>
                                        <tr bgcolor="#efefef" valign="top">
                                        <td align="left" width="45%"><font size=2 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
                                        <td align="right" width="15%"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Pedido</b></font></td>
                                        <td align="right" width="15%"><font size=2 face="<?= $fuente_general; ?>"><b>Existencias Aula</b></font></td>
                                        <td align="right" width="15%"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad pedido</b></font></td>
                                        <td align="center" width=10%><font size=2 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                        </tr>
                                        <?php 
                                        }
                                
                                $busca_existencias_articulo=db_query_params('SELECT * FROM existencias_aulas WHERE id_aula=? AND nombre_generico=?', 'ss', array($material_aulas["id_aula"], (string)($datos_detalle["generico_solicitado"])));
                                if (db_num_rows($busca_existencias_articulo)==0) {
                                        $existencias_articulo=number_format(0,2,",",".");
                                        } else {
                                        $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                                        $existencias_articulo=number_format($datos_existencias_articulo["stock_actual"],2,",",".");
                                        }
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$material_aulas["nombre"];?></font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($material_aulas["fecha_pedido"]));?></font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$existencias_articulo;?></font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($material_aulas["cantidad"],2,",",".");?></font></td>
                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar_pedido_aula('<?=$material_aulas["id_pedido"];?>')"><img src="editar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                        }
                                ?>
                                </table>
                                </td>
                                </tr>
                                <?php 
                                }
                                ?>
                                <tr>
                                        <td colspan=4 bgcolor="#efefef">
                                        <font size=2><b>Peticiones recibidas desde observaciones:</b></font>
                                        </td>
                                </tr>
                                <?php 
                                // Busca observaciones
                                $busca_lista_observaciones=db_query("SELECT * FROM detalles_pedido_material,pedidos_material,centro_aulas,centros WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND centro_aulas.id_centro=centros.id_centro AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.id_pedido_empresa=0 AND detalles_pedido_material.confirmado=1 GROUP BY detalles_pedido_material.id_pedido ORDER BY centro_aulas.nombre") or die(db_error()." -1");
                                while($lista_observaciones=db_fetch_array($busca_lista_observaciones)) {
                                        if (strlen($lista_observaciones["observaciones"])!=0) {
                                        ?>
                                        <tr>
                                        <td colspan=4>
                                        <font size=2><b>Escuela: <?=$lista_observaciones["nombre_centro"]?>, Aula <?=$lista_observaciones["nombre"]?></b><br>
                                        <?=$lista_observaciones["observaciones"];?></font>
                                        </td>
                                        </tr>
                                        <?php 
                                                }
                                        }

                                $observaciones_escuelas="";
                                $id_pedido_seleccionado1=0;
                                $busca_lista_observaciones=db_query("SELECT * FROM detalles_pedido_material,pedidos_material_centros,centro_aulas,centros WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido_centro=pedidos_material_centros.id_pedido_centro AND pedidos_material_centros.id_centro=centros.id_centro AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.id_pedido_empresa=0 AND detalles_pedido_material.confirmado=1 GROUP BY detalles_pedido_material.id_pedido_centro ORDER BY centros.nombre_centro") or die(db_error()." -1");
                                while($lista_observaciones=db_fetch_array($busca_lista_observaciones)) {
                                        $observaciones_escuela_actual=$lista_observaciones["observaciones"];
                                        if (strlen($observaciones_escuela_actual)!=0) {
                                                $observaciones_escuelas=$observaciones_escuelas."Escuela: ".$lista_observaciones["nombre_centro"].": ".$observaciones_escuela_actual."\n\n";
                                                }
                                        }

                                ?>
                                <tr>
                                        <td colspan=4 bgcolor="#efefef">
                                        <font size=2><b>Observaciones:</b></font>
                                        </td>
                                </tr>
                                <tr>
                                        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=140 rows=8 style="font-size: 12px; font-family: Arial"><?=$observaciones_escuelas;?></textarea></font></td>
                                </tr>
                                </table>
                                <br><input type="button" onclick="validar_pedidos()" style="font-size: 10px" value="Guardar cambios y genera pedido definitivo del material marcado"> <input type="button" onclick="window.location.href='/index.php'" style="font-size: 10px" value="Salir"><br><br>
                                </td>
                        </tr>
                        </tr>
                        </form>
                </table>
<script language="Javascript">

 function validar_pedidos() {
        self.document.pedidos_pendientes.submit();
        }

function mostrar(id_capa) {
        document.getElementById('capa_detalles_o_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_m_'+id_capa).style.display='block';
        }

function ocultar(id_capa) {
        document.getElementById('capa_detalles_m_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_o_'+id_capa).style.display='block';
        }
        
function editar_pedido_aula(id_pedido) {
        window.open('editar_pedido_aula.php?id_pedido='+id_pedido,'pedido','toolbar=yes,resizable=yes,width=800,height=600');
        }

</script>
<?php 
} else {
?>
<p align="center"><br><br>No hay pedidos nuevos</p><br>
<p align="center"><input type="button" onclick="window.location.href='calendario_pedidos_empresa.php'" style="font-size: 10px" value="Ver otros pedidos"> <input type="button" onclick="window.location.href='/index.php'" style="font-size: 10px" value="Salir"></p>
<?php 
}
require("../configuracion/parte_inferior.php"); ?>
