<?php  
mt_srand((double)microtime()*1000000);

$db_m1=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_m1);

if ($permitido!=true)
{
// $pagina_actual="2";
// include "inc/pagina.php";
// include "noticias/noticias.php";
// include "inc/seleccion_usuario.php";

} else {

//Aqui la página de inicio de la zona privada

?><div align="center"><center><?php 
$ancho_tabla=700;
?><table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd.gif" border=0 width=8 height=8></td>
                </tr>
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="FFFFFF" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>AREA PRIVADA</b></font>
                        </td>
                </tr>
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_ii.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_id.gif" border=0 width=8 height=8></td>
                </tr>
    </table>
<br>
<table border="0" cellpadding=0 cellspacing=0 width="<?php  echo $ancho_tabla; ?>">
<tr>
        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si_gris.gif" border=0 width=8 height=8></td>
        <td bgcolor="#cfcfcf" width="<?= $ancho_tabla-16; ?>" ><img src="<?php  echo $url_root; ?>/imagen/nada.gif" border=0 width=1 height=1></td>
        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd_gris.gif" border=0 width=8 height=8></td>
</tr>
<tr>
        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="#cfcfcf" colspan=3 align="center">
<font size=1 face="<?= $fuente_general; ?>">
<?php 
  $hora=strftime("%H");
  if (($hora>=22 || $hora<5))
  {
    print "Buenas noches, ";  }

  if ($hora>=15 && $hora<22)
  {
    print "Buenas tardes, ";  }

  if ($hora>=5 && $hora<15)
  {
    print "Buenos dias, ";  }

  print $nombre;
?>. Bienvienido.<br><br>Servicios de la zona privada.<br><br>
</font>
<?php 
// Alertas, educadoras, coordinadoras y administradora
if (($nivel_acceso==3) or ($nivel_acceso==4) or ($nivel_acceso==2)) {
include "alertas.php";
}

 if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
 
 
if (strlen($centro_actual)!=0) {
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<?php 
if (($nivel_acceso==4) or ($zona_alumnos==1)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Alumnos</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Contactos" onclick="window.location.href='/contactos/listado_contactos.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de contactos de padres interesados en inscribir a sus hijos.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Alumnos" onclick="window.location.href='<?=$url_web;?>/alumnos/gestion_alumnos.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de alumnos matriculados en el centro.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
<?php 
}

if (($nivel_acceso==4) or ($zona_facturacion==1)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Facturación Servicios</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Facturación" onclick="window.location.href='<?=$url_web;?>/facturacion/gestion_facturacion.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Emisión y control de facturas emitidas a los alumnos matriculados en el centro.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Caja" onclick="window.location.href='<?=$url_web;?>/caja/listado_caja.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de ingresos y gastos.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
<?php 
}
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Entregas de material</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Reservas" onclick="window.location.href='<?=$url_web;?>/gestion/material/gestion_material.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de las reservas del material a los alumnos matriculados en el centro.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Entregas" onclick="window.location.href='<?=$url_web;?>/gestion/material/control_entregas.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión, cobro y facturación de las entregas del material reservado previamente a los alumnos matriculados en el centro.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Resumen" onclick="window.location.href='<?=$url_web;?>/gestion/material/ventas_material.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Resumen de las reservas y entregas del material a los alumnos.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
</td>
<td>
<?php 
if (($nivel_acceso==4) or ($zona_aulas==1)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Aulas</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Listado aulas" onclick="window.location.href='<?=$url_web;?>/gestion/centros/gestion_aulas.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de todas las aulas del centro.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia" onclick="window.location.href='<?=$url_web;?>/aulas/gestion_aulas.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de asistencia de los alumnos.</font></td>
                        </tr>
<?php 
if (($nivel_acceso==4) or ($zona_mensajes==1)) {
?>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Mensaje padres" onclick="window.location.href='<?=$url_web;?>/herramientas/enviar_mensaje.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Enviar mensaje a los padres.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Mensaje interno" onclick="window.location.href='<?=$url_web;?>/herramientas/enviar_mensaje_edu.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Enviar mensaje a las educadoras, coordinadoras y administración.</font></td>
                        </tr>
<?php 
}
?>
                        </table>
                </td>
        </tr>
</table>
<?php 
}

if (($nivel_acceso==4) or ($zona_comedor==1)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Comedor</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Listados" onclick="window.location.href='/cocina/calendario_mes_comedor.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listados de los menús que se deben preparar.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia" onclick="window.location.href='/aulas/calendario_mes_comedor.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Control de asistencia de los alumnos al comedor.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Menús" onclick="window.location.href='/menu/gestion_menu.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Elaboración de menús.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Preparación" onclick="window.location.href='/cocina/preparacion_platos/'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Control de productos empleados en la preparación de los platos del menú.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
<?php 
}
?>
</tr>
<tr valign="top">
<td width="50%">
<?php 
if (($nivel_acceso==4) or ($zona_almacen==1)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Almacén y Cocina</b></font>
                        <table bgcolor="#FFFFFF" border=1 borderc<<olor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Servicios" onclick="window.location.href='<?=$url_web;?>/facturacion/gestion_familias.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Mantenimiento de tarifas de servicios y suministros para aulas y cocina.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Suministros" onclick="window.location.href='<?=$url_web;?>/almacen/gestion_almacen.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de suministros para las aulas, recepción de pedidos de material.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Cocina" onclick="window.location.href='<?=$url_web;?>/cocina/gestion_cocina.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de suministros para la cocina, recepción de pedidos de suministros.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
<?php 
}
?>
</td>
<td>
<?php 
if (($nivel_acceso==4) or ($zona_incidencias==1)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Incidencias</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Incidencias" onclick="window.location.href='<?=$url_web;?>/gestion/incidencias/menu_incidencias.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de incidencias educativas, de asistencia, accidentes, material, etc.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
<?php 
}
if (($nivel_acceso==4) or ($zona_personal==1)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Personal</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Personal" onclick="window.location.href='<?=$url_web;?>/facturacion/gestion_educadoras.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de horarios y asistencia del personal de la escuela.</font></td>
                        </tr>
                        </table>

                </td>
        </tr>
</table>
<?php 
}
if (($nivel_acceso==4) or ($zona_pedidos==1)) {
?><table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Pedidos centro</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Pedidos mat." onclick="window.location.href='<?=$url_web;?>/almacen/pedidos_centro.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Validación de pedidos de material del centro.</font></td>
                        </tr>
                        </table>

                </td>
        </tr>
</table>
<?php 
}
?>
</td>
</tr>
</table><br>
<?php 
} else {
?><br>
<table bgcolor="#efefef" border=0><tr>
<td align="center">
<font size=2><b>Selecciona el centro que deseas gestionar</b></font>
<br><br>
<?php 
$consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");

if (db_num_rows($consulta_centros)==0) {
        ?>
        No exististe ningún centro en la base de datos, pulsa el botón para intoducir el primero.<br><br>
        <input type="button" onclick="window.location.href='/gestion/centros/editar_centro.php';" value="Dar de alta un centro">
        <?php 
        } else {
        ?>
        <form name="centros" method="post" action="<?=$path_index;?>">
        <select name="id_centro_seleccionado" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <option value="">Selecciona el centro</option>
        <?php 
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select>
        </form>
        <?php 
        }
?>
<br><br>
<?php 
}
// Menu administrador
if ($nivel_acceso>=4) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>ADMINISTRACION</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                         // Menu administrador
                         $consulta_menu_admin=db_query("SELECT * FROM menu_administrador ORDER BY Orden");
                         
                         while($rsmenuadmin=db_fetch_array($consulta_menu_admin)) {
                         ?>
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><a href="<?= $rsmenuadmin["link"]?>"><b><?= $rsmenuadmin["Texto"]?></b></a></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?= $rsmenuadmin["texto_ayuda"]?></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                         </table>
                         <?php 
                ?>
                </td>
        </tr>
</table>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Mis datos</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Editar datos" onclick="window.location.href='<?=$url_web;?>/gestion/usuarios/datos_usuario.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Edición de tus datos personales, titulación y curriculum.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Contratos" onclick="window.location.href='<?=$url_web;?>/gestion/personal/ficha.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Consulta de tus contratos, calendario laboral, horas trabajadas y documentación de prevención de riesgos laborales.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
<?php 
} else {

if ($zona_inferior==1) {

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>ADMINISTRACION</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                         // Menu administrador
                         $consulta_menu_admin=db_query("SELECT * FROM menu_administrador WHERE nivel_acceso=3 ORDER BY Orden");

                         while($rsmenuadmin=db_fetch_array($consulta_menu_admin)) {
                         ?>
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><a href="<?= $rsmenuadmin["link"]?>"><b><?= $rsmenuadmin["Texto"]?></b></a></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?= $rsmenuadmin["texto_ayuda"]?></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                         </table>
                         <?php 
                ?>
                </td>
        </tr>
</table>
<?php 

}

 }

if (($nivel_acceso>=4) or ($usuario_multicentro==1)) {

if (strlen($centro_actual)!=0) {
$consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");

        ?>
        <form name="centros" method="post" action="<?=$path_index;?>">
        <font size=1><b>Cambio rápido de centro: </b></font><select name="id_centro_seleccionado" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <?php 
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select>
        </form>
<?php 
         }
 }

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td colspan=2 align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><b>Turno mañana (o jornada continua *)</b></font></td>
                <td colspan=2 align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><b>Turno tarde</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                </tr>
                <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        // Busca si ya está registrado
        $turno=0;

        $busca_registro_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."'") or die(db_error());
        if (db_num_rows($busca_registro_entrada)==0) {
        $asistencia_comedor_activada=false;
        ?>
        <input type="button" value="Registrar entrada" onclick="registro_entrada(<?=$turno;?>)"></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
        <?php 
        } else {
        $asistencia_comedor_activada=true;
        $datos_registro=db_fetch_array($busca_registro_entrada);
        $id_registro_comedor=$datos_registro["id_asistencia"];
        $estado_registro_comedor=$datos_registro["comedor"];
        $menu_comedor_educador=$datos_registro["menu_seleccionado_hoy"];
        $centro_asistencia_comedor=$datos_registro["id_centro_asistencia"];
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font>
        </td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        ?>
        <input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)">
        <?php 
        } else {
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font>
        <?php 
        }
        ?>
        </font></td>
        <?php 
        }
        ?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        // Busca si ya está registrado
        $turno=1;

        $busca_registro_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."'") or die(db_error());
        if (db_num_rows($busca_registro_entrada)==0) {
        $estado_comedor="";
        ?>
        <input type="button" value="Registrar entrada" onclick="registro_entrada(<?=$turno;?>)"></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
        <?php 
        } else {
        $datos_registro=db_fetch_array($busca_registro_entrada);
        $estado_comedor=" disabled";
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font>
        </td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        ?>
        <input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)">
        <?php 
        } else {
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font>
        <?php 
        }
        ?>
        </font></td>
        <?php 
        }
        ?></font></td>
        </tr>
        </table>
               <font size=2><b>* NOTA:</b> En caso de hacer jornada continua, fichar solo la entrada y la salida del primer turno</font>
        <?php 
        if ($asistencia_comedor_activada==true) {
        ?>
        <table width=80%>
                <form name="asistencia_comedor_docente" method="post" action="/gestion/docentes/asistencia_comedor.php">
                <tr>
                <td align="center"><font size=2><b>Asistencia al comedor</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center">
                <font size=2><b>Me quedo a comer en... </b><select name="id_centro_comida" size=1 style="font-size: 10px">
                <option value="">Selecciona escuela</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php if ($centro_asistencia_comedor==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select>
<br></font>
                <input type="hidden" name="id_registro_comedor" value="<?=$id_registro_comedor?>">
                <font size=2><b>¿Te quedás a comer en la escuela? Selecciona menú</b>
                <select<?=$estado_comedor;?> name="menu_seleccionado_hoy" style="font-size: 10px" onchange="document.asistencia_comedor_docente.submit()">
                                        <option value="0"<?php  if ($menu_comedor_educador==0) { print " selected"; } ?>>No me quedo a comer</option>
                                        <?php 
                                        $consulta_datos_menus=db_query("SELECT * FROM menu_menus WHERE edad_ini_menu=17 ORDER BY edad_ini_menu;") or die(db_error());
                                        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
                                        ?>
                                        <option value="<?=$datos_menus["id_menu"]?>"<?php  if ($datos_menus["id_menu"]==$menu_comedor_educador) { print " selected"; }?>><?=urldecode($datos_menus["nombre_menu"])?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select>
                                        </font></td>
                </tr>
                </form>
        </table>
        <script language="Javascript">
        function envia_formulario() {
                if (document.asistencia_comedor_docente.id_centro_comida.value!="") {
                        document.asistencia_comedor_docente.submit();
                        } else {
                        alert("Selecciona la escuela donde te quedas a comer.");
                        document.asistencia_comedor_docente.asistencia_comedor[0].click();
                        }
                }
        </script>
        <?php 
        }
        ?>
<br>
<?php 
 } else {

?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td>
<?php 
if ($nivel_acceso==2) {

 // Docente
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Aulas</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Listado aulas" onclick="window.location.href='<?=$url_web;?>/aulas_docentes/listado_aulas.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de todas las aulas del centro.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia" onclick="window.location.href='<?=$url_web;?>/aulas_docentes/gestion_aulas.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de asistencia de los alumnos.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
<?php 
}
if (($nivel_acceso==2) or ($nivel_acceso==10)) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Comedor</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                        if ($nivel_acceso==2) {
                        ?>
                        <tr valign="top">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia" onclick="window.location.href='/aulas_docentes/calendario_mes_comedor.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Control de asistencia al comedor.</font></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Listados" onclick="window.location.href='/cocina/calendario_mes_comedor.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listados de los menús que se deben preparar.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Preparación" onclick="window.location.href='/cocina/preparacion_platos/'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Control de productos empleados en la preparación de los platos del menú.</font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                </td>
        </tr>
</table>
<?php 
}
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Incidencias</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Incidencias" onclick="window.location.href='<?=$url_web;?>/gestion/incidencias/menu_incidencias.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de incidencias educativas, de asistencia, accidentes, material, etc.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Mis datos</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Editar datos" onclick="window.location.href='<?=$url_web;?>/gestion/usuarios/datos_usuario.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Edición de tus datos personales, titulación y curriculum.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
<br>
</td>
</tr>
<tr valign="top">
<td width="50%">
<?php 
if ($nivel_acceso==11) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=35%><font size=2 face="<?= $fuente_general; ?>"><i>Escuela</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><i>Tiempo</i></font></td>
                </tr>
        <form name="control_asistencia">
        <?php 
        // Busca si ya está registrado
        $turno=2;

        $busca_registros_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."' ORDER BY hora_entrada") or die(db_error());

        if (db_num_rows($busca_registros_entrada)==0) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        } else {
        $tiempo_total_jornada=0;
        while($datos_registro=db_fetch_array($busca_registros_entrada)) {
        $mostrar_nuevo=false;
        $consulta_centros=db_query("SELECT * FROM centros WHERE id_centro=".$datos_registro["id_centro_asistencia"]);
        $datos_centro_asistencia=db_fetch_array($consulta_centros);
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_centro_asistencia["nombre_centro"];?></font></td>
                <td align="center"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font></td>
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <?php 
        } else {
        $tiempo_total_jornada=$tiempo_total_jornada+(strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        $tiempo_permanencia_centro=(date("H",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]))-1).date(":i:s",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$tiempo_permanencia_centro;?></font></td>
        <?php 
        $mostrar_nuevo=true;
        }
        ?>
        </tr>
        <?php 
                }
        if ($mostrar_nuevo==true) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        }
        }
        $total_jornada=(date("H",$tiempo_total_jornada)-1).date(":i:s",$tiempo_total_jornada);
        ?>
        <tr bgcolor="#efefef">
                <td align="right" colspan=3><font size=2 face="<?= $fuente_general; ?>">Duración total de la jornada:</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$total_jornada;?></font></td>
        </tr>
        </form>
        </table>
                </td>
        </tr>
</table>
<?php 
} else {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td colspan=2 align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><b>Turno mañana (o jornada continua *)</b></font></td>
                <td colspan=2 align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><b>Turno tarde</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                </tr>
                <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        // Busca si ya está registrado
        $turno=0;

        $busca_registro_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."'") or die(db_error());
        if (db_num_rows($busca_registro_entrada)==0) {
        $asistencia_comedor_activada=false;
        ?>
        <input type="button" value="Registrar entrada" onclick="registro_entrada(<?=$turno;?>)"></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
        <?php 
        } else {
        $asistencia_comedor_activada=true;
        $datos_registro=db_fetch_array($busca_registro_entrada);
        $id_registro_comedor=$datos_registro["id_asistencia"];
        $estado_registro_comedor=$datos_registro["comedor"];
        $menu_comedor_educador=$datos_registro["menu_seleccionado_hoy"];
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font>
        </td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        ?>
        <input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)">
        <?php 
        } else {
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font>
        <?php 
        }
        ?>
        </font></td>
        <?php 
        }
        ?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        // Busca si ya está registrado
        $turno=1;

        $busca_registro_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."'") or die(db_error());
        if (db_num_rows($busca_registro_entrada)==0) {
        $estado_comedor="";
        ?>
        <input type="button" value="Registrar entrada" onclick="registro_entrada(<?=$turno;?>)"></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
        <?php 
        } else {
        $datos_registro=db_fetch_array($busca_registro_entrada);
        $estado_comedor=" disabled";
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font>
        </td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        ?>
        <input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)">
        <?php 
        } else {
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font>
        <?php 
        }
        ?>
        </font></td>
        <?php 
        }
        ?></font></td>
        </tr>
        </table>
               <font size=2><b>* NOTA:</b> En caso de hacer jornada continua, fichar solo la entrada y la salida del primer turno</font>
        <?php 
        if ($asistencia_comedor_activada==true) {
        ?>
        <table width=80%>
                <form name="asistencia_comedor_docente" method="post" action="/gestion/docentes/asistencia_comedor.php">
                <input type="hidden" name="id_centro_comida" value="">
                <tr>
                <td align="center"><font size=2><b>Asistencia al comedor</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center">
                <input type="hidden" name="id_registro_comedor" value="<?=$id_registro_comedor?>">
                <font size=2><b>¿Te quedás a comer en la escuela? </b>
                <font size=2><b>¿Te quedás a comer en la escuela? Selecciona menú</b>
                <select<?=$estado_comedor;?> name="menu_seleccionado_hoy" style="font-size: 10px" onchange="document.asistencia_comedor_docente.submit()">
                                        <option value="0"<?php  if ($menu_comedor_educador==0) { print " selected"; } ?>>No me quedo a comer</option>
                                        <?php 
                                        $consulta_datos_menus=db_query("SELECT * FROM menu_menus WHERE edad_ini_menu=17 ORDER BY edad_ini_menu;") or die(db_error());
                                        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
                                        ?>
                                        <option value="<?=$datos_menus["id_menu"]?>"<?php  if ($datos_menus["id_menu"]==$menu_comedor_educador) { print " selected"; }?>><?=urldecode($datos_menus["nombre_menu"])?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select>
                </td>
                </tr>
                </form>
        </table>
        <?php 
        }
        ?>
                </td>
        </tr>
</table>
<?php 
}
?>
</td>
</tr>
</table><br>
 <?php 
 }
?>
</center></div>
<script language="Javascript">
function registro_entrada_escuela(turno) {
        // Comprueba si se ha seleccionado la escuela
        if (document.control_asistencia.id_centro_asistencia.value=="0") {
                alert("No has seleccionado la escuela...");
                } else {
                window.location.href='/gestion/docentes/registro_entrada.php?id_centro_asistencia='+document.control_asistencia.id_centro_asistencia.value+'&turno='+turno;
                }
        }
        
function registro_entrada(turno) {
        window.location.href='/gestion/docentes/registro_entrada.php?turno='+turno;
        }
function registro_salida(id_registro) {
        window.location.href='/gestion/docentes/registro_salida.php?id_registro='+id_registro;
        }
</script>
<?php 
}
db_close($db_m1);
?>

