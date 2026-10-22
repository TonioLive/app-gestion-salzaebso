<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$formulario_name="presupuesto";
$formulario_accion="nuevo_presupuesto1.php";

$id_aula=$_GET["id_aula"];

if (strlen($id_aula)!=0) {

                // datos aula

                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
                $datos_aula=db_fetch_array($busca_datos_aula);
                
                $nombre_aula=$datos_aula["nombre"];
                $id_centro_aula=$datos_aula["id_centro"];
                $id_curso_aula=$datos_aula["id_curso"];
                
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_aula)) or die(db_error()."-4");
                        if (db_num_rows($consulta_cursos)!=0) {
                                $cursos=db_fetch_array($consulta_cursos);
                                $nombre_curso=$cursos["nombre"];
                                } else {
                                $nombre_curso="";
                                }
                                
                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro_aula));
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro=$datos_centro["nombre_centro"];
                        } else {
                        $db_nombre_centro="";
                        }
                        
                $busca_presupuesto=db_query_params('SELECT * FROM alumnos_presupuestos WHERE id_aula=?', 's', array($id_aula)) or die(db_error());
                if (db_num_rows($busca_presupuesto)!=0) {
                                $datos_presupuesto=db_fetch_array($busca_presupuesto);
                                $id_presupuesto=$datos_presupuesto["id_presupuesto"];
                                $observaciones=$datos_presupuesto["observaciones"];
                                if (strlen($observaciones)!=0) {
                                        $observaciones=urldecode($observaciones);
                                        }
                                } else {
                                $id_presupuesto=0;
                                $observaciones="";
                                }
                }

?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

function comprobar_y_enviar(accion) {

                var errores=0;

                var mensajes_error="";
                
                document.<?=$formulario_name;?>.retorno.value=accion;
                
                if (accion!=0) {
                        document.<?=$formulario_name;?>.target="_new";
                        }
                
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        if (accion!=0) {
                        self.location.href='/gestion/centros/gestion_aulas.php?curso_seleccionado=<?=$id_curso_aula?>';
                        }
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
                        
        }

</script>
<input type="button" value="Cancelar" onclick="window.location.href='/gestion/centros/gestion_aulas.php'" style="font-size: 10px"> <input type="button" value="Guardar y salir >>" style="font-size: 10px" onclick="comprobar_y_enviar(0);"> <input type="button" value="Imprimir Resumido >>" style="font-size: 10px" onclick="comprobar_y_enviar(2);"> <input type="button" value="Imprimir completo (Con totales) >>" style="font-size: 10px" onclick="comprobar_y_enviar(1);">
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_aula" value="<?=$id_aula;?>">
<input type="hidden" name="formulario_enviado" value="1">
<input type="hidden" name="retorno" value="0">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Presupuesto de Aula</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                             <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$db_nombre_centro;?></td>
                        </tr>
                             <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Aula:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_aula;?></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Curso presupuesto:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_curso;?></td>
                        </tr>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Comentarios:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="observaciones" rows=4  cols="80" style="font-size: 11px; font-family: Arial"><?= $observaciones; ?></textarea></font></td>
                       </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Servicios</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_notas=db_query_params('SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=? OR tarifa.id_curso=0) ORDER BY tarifa.CATEGORIA,tarifa.ARTICULO', 's', array($id_curso_aula)) or die(db_error());
                                ?><br>
                                <table width="98%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="52%"><font size=1 face="<?= $fuente_general; ?>"><b>Servicio</b></font></td>
                                                <td align="center" width="16%"><font size=1 face="<?= $fuente_general; ?>"><b>Precio</b></font></td>
                                                <td align="center" width="16%"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                                <td align="center" width="16%"><font size=1 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                        </tr>
                                <?php 
                                if (db_num_rows($busca_notas)==0) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center" colspan=6><font size=2 face="<?= $fuente_general; ?>"><b>No hay servicios que puedan presupuestarse para el curso seleccionado</font></td>
                                </tr>
                                <?php 
                                }
                                $familia_actual="";
                                $contador=0;
                                $total_total=0;
                                $array_codigos="";
                                while($datos_notas=db_fetch_array($busca_notas)) {
                                if ($familia_actual!=$datos_notas["categoria"]) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left" colspan=4><font size=2 face="<?= $fuente_general; ?>"><b><?=$datos_notas["categoria"];?></b></font></td>
                                </tr>
                                <?php 
                                $familia_actual=$datos_notas["categoria"];
                                }
                                $id_servicio=$datos_notas["ID"];
                                $busca_linea_presupuesto=db_query_params('SELECT * FROM alumnos_presupuestos_detalles WHERE id_presupuesto=? AND id_servicio=?', 'ss', array($id_presupuesto, $id_servicio)) or die(db_error());
                                if (db_num_rows($busca_linea_presupuesto)!=0) {
                                                $datos_linea_presupuesto=db_fetch_array($busca_linea_presupuesto);
                                                $id_detalle=$datos_linea_presupuesto["id_detalle"];
                                                $cantidad_actual=$datos_linea_presupuesto["cantidad_servicio"];
                                                } else {
                                                $cantidad_actual=0;
                                                }
                                $total_actual=round(($cantidad_actual*$datos_notas["PRECIO_VENTA"]),2);
                                $total_total=$total_total+$total_actual;
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <input type="hidden" name="precio_<?=$datos_notas["ID"];?>" value="<?=$datos_notas["PRECIO_VENTA"];?>">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_notas["ARTICULO"];?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_notas["PRECIO_VENTA"],2,".",",");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="cantidad_<?=$datos_notas["ID"];?>" onblur="actualiza_totales()" size=4 style="font-size: 10px; text-align: right" value="<?=$cantidad_actual;?>"></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="total_<?=$datos_notas["ID"];?>" readonly size=4 style="font-size: 10px; text-align: right" value="<?=$total_actual;?>"> €</font></td>
                                </tr>
                                <?php 
                                $array_codigos=$array_codigos.",".$datos_notas["ID"];
                                $contador++;
                                }
                                if (strlen($array_codigos)!=0) {
                                        $array_codigos=substr($array_codigos,1,(strlen($array_codigos)-1));
                                        }

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="right" colspan=3><font size=1 face="<?= $fuente_general; ?>"><b>Total presupuesto</b></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="total_presupuesto" size=8 style="font-size: 10px; text-align: right" value="<?=$total_total;?>"> €</font></td>
                                </tr>
                                </table>

                                </td>
                        </tr>
                        </table>
                        <input type="button" value="Cancelar" onclick="window.location.href='/gestion/centros/gestion_aulas.php'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar(0);"> <input type="button" value="Imprimir Resumido >>" style="font-size: 10px" onclick="comprobar_y_enviar(2);"> <input type="button" value="Imprimir completo (Con totales) >>" style="font-size: 10px" onclick="comprobar_y_enviar(1);">
                </td>
        </tr>
</form>
</table><br><br>
<script language="Javascript">

function actualiza_totales() {

var array_codigos=Array(<?=$array_codigos;?>);

total_presupuesto=0;
for (contador=0;contador<<?=$contador?>;contador++) {
        cantidad1=eval("document.presupuesto.cantidad_"+array_codigos[contador]+".value");

        if (isNaN(cantidad1)) {
                alert("La cantidad debe ser un número.");
                eval("document.presupuesto.cantidad_"+array_codigos[contador]+".value=0");
                } else {
                eval("document.presupuesto.total_"+array_codigos[contador]+".value=Math.round(document.presupuesto.cantidad_"+array_codigos[contador]+".value*document.presupuesto.precio_"+array_codigos[contador]+".value*100)/100")
                eval("total_presupuesto=Number(total_presupuesto)+Number(document.presupuesto.total_"+array_codigos[contador]+".value)");
                }
        }
        
document.presupuesto.total_presupuesto.value=total_presupuesto;

}

</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
