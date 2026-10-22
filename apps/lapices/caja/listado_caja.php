<?php  require("../configuracion/parte_superior_docente.php"); ?>
<?php 

$formulario_name="caja";

$id_centro_caja=$_GET["id_centro_caja"];
$id_cierre_caja=$_GET["id_cierre_caja"];
$mostrar_todos=$_GET["mostrar_todos"];
if (strlen($mostrar_todos)==0) {
        $mostrar_todos=0;
        }
if (strlen($id_centro_caja)==0) {
        $id_centro_caja=$centro_actual;
        }

$url_retorno="/index.php";


$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_caja") or die(db_error()."-A");
if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro=$datos_centro["nombre_centro"];

        } else {
        if ($id_centro_caja==0) {
                $db_nombre_centro="Central";
                }
        }
$id_centro_busqueda=$id_centro_caja;


// Busca el cierre de caja
if (strlen($id_cierre_caja)==0) {
        $estado_cierres="";
        // Busca el ultimo
        $busca_cierres_caja=db_query("SELECT * FROM cierres_caja WHERE id_centro=$id_centro_busqueda ORDER BY id_cierre DESC") or die(db_error()."-B");

        if (db_num_rows($busca_cierres_caja)==0) {
                                $primero=0;
                                $ultimo=0;
                                $id_cierre_anterior=0;
                                $fecha_cierre_anterior="2000-01-01";
                                $hora_cierre_anterior="00:00:01";
                                } else {
                                $cierres_caja=db_fetch_array($busca_cierres_caja);
                                $primero=$cierres_caja["ultimo_movimiento"];
                                $ultimo=0;
                                $id_cierre_anterior=$cierres_caja["id_cierre"];
                                $fecha_cierre_anterior=$cierres_caja["fecha"];
                                $hora_cierre_anterior=$cierres_caja["hora"];
                                }
        } else {
        $estado_cierres="disabled ";

        // Busca el ultimo cierre de caja anterior al actual y muestra los tickets emitidos desde ese momento
        $busca_cierres_caja=db_query("SELECT * FROM cierres_caja WHERE id_centro=$id_centro_busqueda AND id_cierre<$id_cierre_caja ORDER BY id_cierre DESC") or die(db_query()."-3");

        if (db_num_rows($busca_cierres_caja)==0) {
                                $primero=0;
                                $id_cierre_anterior=0;
                                $fecha_cierre_anterior="2000-01-01";
                                $hora_cierre_anterior="00:00:01";
                                } else {
                                $cierres_caja=db_fetch_array($busca_cierres_caja);
                                $primero=$cierres_caja["ultimo_movimiento"];
                                $id_cierre_anterior=$cierres_caja["id_cierre"];
                                $fecha_cierre_anterior=$cierres_caja["fecha"];
                                $hora_cierre_anterior=$cierres_caja["hora"];
                                }
        // Busca el siguiente cierre de caja
        $busca_siguiente_cierre_caja=db_query("SELECT * FROM cierres_caja WHERE id_centro=$id_centro_busqueda AND id_cierre>$id_cierre_caja ORDER BY id_cierre") or die(db_query()."-3");

        if (db_num_rows($busca_siguiente_cierre_caja)==0) {
                                $id_cierre_siguiente=0;
                                } else {
                                $siguientes_cierres_caja=db_fetch_array($busca_siguiente_cierre_caja);
                                $id_cierre_siguiente=$siguientes_cierres_caja["id_cierre"];
                                }
                                
        // Datos del cierre actual
        $busca_cierres_caja=db_query("SELECT * FROM cierres_caja WHERE id_cierre=$id_cierre_caja") or die(db_query()."-4");
        $cierres_caja=db_fetch_array($busca_cierres_caja);
        $fecha_cierre=$cierres_caja["fecha"];
        $hora_cierre=$cierres_caja["hora"];
        $ultimo=$cierres_caja["ultimo_movimiento"];
        $importe_real_al_cierre=$cierres_caja["importe_al_cierre_real"];
        $fecha_cierre_actual=$cierres_caja["fecha"];
        $hora_cierre_actual=$cierres_caja["hora"];
        }

$filtro=" AND (fecha_pago>'".date("Y-m-d",strtotime($fecha_cierre_anterior))."' OR (fecha_pago='".date("Y-m-d",strtotime($fecha_cierre_anterior))."' AND hora_pago>'".date("H:i:s",strtotime($hora_cierre_anterior))."'))";
if (($ultimo!=0) or ($id_cierre_siguiente!=0)) {
        $filtro=$filtro." AND (fecha_pago<'".date("Y-m-d",strtotime($fecha_cierre_actual))."' OR (fecha_pago='".date("Y-m-d",strtotime($fecha_cierre_actual))."' AND hora_pago<='".date("H:i:s",strtotime($hora_cierre_actual))."'))";
        }

$formas_de_pago[0]="Contado";
$formas_de_pago[1]="Domiciliación";
$formas_de_pago[2]="Transferencia";
$formas_de_pago[3]="Talón";
$formas_de_pago[4]="Pagaré";
$formas_de_pago[5]="Ticket Guardería";

$estado_cambio="";
if ($id_cierre_anterior==0) {
       $estado_cambio="disabled ";
        }

if ($nivel_acceso==4) {

?><table width=75% border=0 cellpadding=0 cellspacing=0>
<tr>
<td><input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=<?=$mostrar_todos?>&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_anterior?>'" value="<< Cierre anterior" style="font-size: 10px"></td>
<td>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_centro">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Escuela: <select name="id_centro_caja" size=1 style="font-size: 10px" onchange="document.seleccion_centro.submit();">
                        <option value="0">Central</option>
                        <?php 
                        $busca_listado_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro");

                        while($datos_listado_centros=db_fetch_array($busca_listado_centros)) {
                        ?>
                        <option value="<?=$datos_listado_centros["id_centro"];?>"<?php if ($datos_listado_centros["id_centro"]==$id_centro_caja) { print " selected"; } ?>><?=$datos_listado_centros["nombre_centro"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
</td>
<td align="center"><?php 
if ($mostrar_todos==1) {
?>
<input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=0&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_caja;?>'" value="Mostrar solo los ingresos por caja" style="font-size: 10px">
<?php 
 } else {
 ?>
<input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=1&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_caja;?>'" value="Mostrar tambien ingresos por banco" style="font-size: 10px">
 <?php 
}
?></td>
<td align="right"><?php 
if ($id_cierre_siguiente!=0) {
?><input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=<?=$mostrar_todos?>&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_siguiente?>'" value="Cierre siguiente >>" style="font-size: 10px"><?php 
} else {
if ($id_cierre_caja!=0) {
?><input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=<?=$mostrar_todos?>&id_centro_caja=<?=$id_centro_caja?>'" value="Caja activa" style="font-size: 10px"><?php 
}
}
?></td>
</table>
                        <?php 
                        } else {
?><table width=75% border=0 cellpadding=0 cellspacing=0>
<tr>
<td><input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=<?=$mostrar_todos?>&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_anterior?>'" value="<< Cierre anterior" style="font-size: 10px">
</td>
<td>&nbsp;</td>
<td align="center"><?php 
if ($mostrar_todos==1) {
?>
<input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=0&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_caja;?>'" value="Mostrar solo los ingresos por caja" style="font-size: 10px">
<?php 
 } else {
 ?>
<input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=1&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_caja;?>'" value="Mostrar tambien ingresos por banco" style="font-size: 10px">
 <?php 
}
?></td>
<td align="right"><?php 
if ($id_cierre_siguiente!=0) {
?><input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=<?=$mostrar_todos?>&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_siguiente?>'" value="Cierre siguiente >>" style="font-size: 10px"><?php 
} else {
if ($id_cierre_caja!=0) {
?><input type="button" <?=$estado_cambio?>onclick="window.location.href='listado_caja.php?mostrar_todos=<?=$mostrar_todos?>&id_centro_caja=<?=$id_centro_caja?>'" value="Caja activa >>" style="font-size: 10px"><?php 
}
}
?></td>
</table>
                        <?php 
                        }
                        ?>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="listado_caja" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<input type="hidden" name="id_centro_caja" value="<?=$id_centro_caja;?>">
<input type="hidden" name="id_cierre_caja" value="<?=$id_cierre_caja;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Caja del Centro <i><?=$db_nombre_centro?></i></b><br><br><?php 
                if ($id_cierre_caja!=0) {
                        ?>
                        Caja cerrada el día <?=date("d/m/Y",strtotime($fecha_cierre))?> a las <?=date("H:i:s",strtotime($hora_cierre))?>.<br><br>
                        <?php 
                        }
                ?></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=7 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listado de movimientos de caja</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Hora</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Concepto</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Forma pago</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Importe</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Saldo</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $saldo=0;

                        $total_formas_pago_ingreso=Array();
                        $total_formas_pago_gasto=Array();

                        $total_gasto=0;
                        $total_ingreso=0;

                        $ultimo_ticket_cierre=0;

                        // $consulta_movimientos=mysql_query("SELECT * FROM pagos_a_cuenta WHERE id_centro_cobro=$id_centro_busqueda$filtro ORDER BY fecha_pago,hora_pago;") or die(mysql_error()."-C");
                        $consulta_movimientos=db_query("SELECT * FROM pagos_a_cuenta WHERE id_centro=$id_centro_busqueda$filtro ORDER BY fecha_pago,hora_pago;") or die(db_error()."-C");
                        while($movimientos=db_fetch_array($consulta_movimientos)){
                        $importe_mov=($movimientos["importe_pago"]*$movimientos["signo"]);
                        if ((($mostrar_todos==0) and ($movimientos["forma_pago"]==0)) or ($mostrar_todos==1)) {
                                $saldo=$saldo+$importe_mov;

                        $color_numero="#000000";
                        if ($importe_mov<0) {
                                $color_numero="#FF0000";
                                $total_gasto=$total_gasto+abs($movimientos["importe_pago"]);
                                $total_formas_pago_gasto[$movimientos["forma_pago"]]=$total_formas_pago_gasto[$movimientos["forma_pago"]]+abs($movimientos["importe_pago"]);
                                } else {
                                $total_ingreso=$total_ingreso+$movimientos["importe_pago"];
                                $total_formas_pago_ingreso[$movimientos["forma_pago"]]=$total_formas_pago_ingreso[$movimientos["forma_pago"]]+$movimientos["importe_pago"];
                                }
                        $color_saldo="#000000";
                        if ($saldo<0) {
                                $color_saldo="#FF0000";
                                }
                        if ($movimientos["id_pago"]>=$ultimo_ticket) {
                                $ultimo_ticket_cierre=$movimientos["id_pago"];
                                }

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y", strtotime($movimientos["fecha_pago"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i", strtotime($movimientos["hora_pago"]));?></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$movimientos["comentario"];?></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$formas_de_pago[$movimientos["forma_pago"]];?></font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_numero;?>"><?=number_format($importe_mov, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_saldo;?>"><?=number_format($saldo, 2, ',', '.');?> €</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?php if ($movimientos["id_factura"]==0) {
                                if ($id_cierre_caja==0) {
                                ?><a href="javascript:eliminar_pago(<?=$movimientos["id_pago"]?>);" title="Eliminar"><img src="/facturacion/borrar.gif" border=0></a><?php 
                                } else {
                                ?><a href="javascript:alert('No se puede eliminar un movimiento con la caja cerrada.');" title="Eliminar"><img src="/facturacion/borrar.gif" border=0></a><?php 
                                }
                                 } else {
                                ?><a href="javascript:if (confirm('Para eliminar este pago, acude al listado pagos a cuenta de facturas. ¿Quieres ver la factura?')) { ver_factura(<?=$movimientos["id_factura"];?>); };" title="Eliminar"><img src="/facturacion/borrar.gif" border=0></a><?php  } ?></font></td>
                        </tr>
                        <?php 
                                }
                        }
                        ?>
                        </table><br>
                        <input type="button" onclick="window.location.href='/facturacion/listado_alumnos.php'" value="Ir a cobro de facturas" style="font-size: 10px"><!--<input type="button" onclick="window.open('nuevo_ingreso.php?id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_caja;?>','ingresos_gastos','width=550,height=250');" <?=$estado_cierres;?>value="Añadir Ingreso" style="font-size: 10px">--> <input type="button" onclick="window.open('nuevo_gasto.php?id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_caja;?>','ingresos_gastos','width=550,height=250');" <?=$estado_cierres;?>value="Añadir Gasto" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Total ingresos</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=number_format($total_ingreso, 2, ',', '.');?> €</font></td>
                        </tr><tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Total gastos</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="#FF0000"><?=number_format($total_gasto, 2, ',', '.');?> €</font></td>
                        </tr><tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Saldo final</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_saldo;?>"><?=number_format($total_ingreso-$total_gasto, 2, ',', '.');?> €</font></td>
                        </tr><tr valign="top">
                                <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>"><b>Total por formas de pago</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Ingresos</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Gastos</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Total</font></td>
                        </tr>
                        <?php 
                        for ($fp=0;$fp<=4;$fp++) {
                        ?>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b><?=$formas_de_pago[$fp];?></b></font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=number_format($total_formas_pago_ingreso[$fp], 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="#FF0000"><?=number_format($total_formas_pago_gasto[$fp], 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=number_format($total_formas_pago_ingreso[$fp]-$total_formas_pago_gasto[$fp], 2, ',', '.');?> €</font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </tr><tr valign="top">
                                <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>"><b>Movimientos de Efectivo</b></font></td>
                        </tr>
                        <?php 
                        $total_salidas=0;
                        $total_entradas=0;

                        $consulta_movimientos=db_query("SELECT * FROM movimientos_caja WHERE id_centro=$id_centro_busqueda AND id_cierre_anterior=$id_cierre_anterior ORDER BY fecha,hora;") or die(db_error());
                        if (db_num_rows($consulta_movimientos)!=0) {
                        ?>
                        </tr><tr valign="top">
                                <td align="center" colspan=4>
                                 <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Hora</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Concepto</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Importe</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        }
                        while($movimientos=db_fetch_array($consulta_movimientos)){
                        $importe_mov=($movimientos["importe"]*$movimientos["signo"]);

                        $color_numero="#000000";
                        if ($movimientos["signo"]==-1) {
                                $color_numero="#FF0000";
                                $total_salidas=$total_salidas+$movimientos["importe"];
                                } else {
                                $total_entradas=$total_entradas+$movimientos["importe"];
                                }

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y", strtotime($movimientos["fecha"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i", strtotime($movimientos["hora"]));?></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$movimientos["comentario"];?></font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_numero;?>"><?=number_format($importe_mov, 2, ',', '.');?> €</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?php 
                                if ($id_cierre_caja==0) {
                                ?><a href="javascript:eliminar_mov(<?=$movimientos["id_movimiento"]?>);" title="Eliminar"><img src="/facturacion/borrar.gif" border=0></a><?php 
                                } else {
                                ?><a href="javascript:alert('No se puede eliminar un movimiento con la caja cerrada.');" title="Eliminar"><img src="/facturacion/borrar.gif" border=0></a><?php 
                                }
                                ?></font></td>
                        </tr>
                        <?php 
                        }
                        if (db_num_rows($consulta_movimientos)!=0) {
                        ?>
                                 </table>
                                </td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Total saldo contado</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=number_format($total_formas_pago_ingreso[0]-$total_formas_pago_gasto[0], 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Aportes de efectivo a la caja</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=number_format($total_entradas, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Retiradas de efectivo</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="#FF0000"><?=number_format($total_salidas, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Importe efectivo en caja</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=number_format($total_entradas+($total_formas_pago_ingreso[0]-$total_formas_pago_gasto[0])-$total_salidas, 2, ',', '.');?> €</font></td>
                        </tr>
<?php 
                if ($id_cierre_caja!=0) {
                        ?>
                        <tr valign="top">
                                <td align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Importe real contado</b></font></td>
                                <td align="right" bgcolor="#ffffff" colspan=3><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=number_format($importe_real_al_cierre, 2, ',', '.');?> €</font></td>
                        </tr>
                        <?php 
                        }
                ?>
                        </table>
                        <br><input type="button" onclick="window.open('nueva_aportacion.php?id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_anterior;?>','ingresos_gastos','width=550,height=250');" <?=$estado_cierres;?>value="Ingresar Efectivo" style="font-size: 10px"> <input type="button" onclick="window.open('nueva_retirada.php?id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_anterior;?>','ingresos_gastos','width=550,height=250');" <?=$estado_cierres;?>value="Retirar efectivo" style="font-size: 10px">
                        <input type="button" value="Cerrar Caja" <?=$estado_cierres;?>onclick="window.open('cerrar_caja.php?ultimo_ticket_cierre=<?=$ultimo_ticket_cierre;?>&importe_efectivo=<?=number_format($total_entradas+($total_formas_pago_ingreso[0]-$total_formas_pago_gasto[0])-$total_salidas, 2, '.', '');?>&id_centro_caja=<?=$id_centro_caja?>&id_cierre_caja=<?=$id_cierre_anterior;?>','ingresos_gastos','width=550,height=250');" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table><br><br>
<script language="Javascript">
function ver_factura(id_factura) {
        abre_ventana=window.open('/pdf/ver_factura.php?id_factura='+id_factura);
        }
function eliminar_pago(id_pago) {
        if (confirm("Se va a eliminar la línea seleccionada, ¿Estás seguro?")){
                window.location.href="eliminar_detalle_caja.php?id_cierre_caja=<?=$id_cierre_caja?>&id_centro_caja=<?=$id_centro_caja?>&id_pago="+id_pago;
                }
        }
function eliminar_mov(id_mov) {
        if (confirm("Se va a eliminar la línea seleccionada, ¿Estás seguro?")){
                window.location.href="eliminar_mov_caja.php?id_cierre_caja=<?=$id_cierre_caja?>&id_centro_caja=<?=$id_centro_caja?>&id_mov="+id_mov;
                }
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
