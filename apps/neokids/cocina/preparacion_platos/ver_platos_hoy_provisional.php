<?php  require("../../configuracion/parte_superior_sin_login_ancho.php"); ?>
<?php 
$id_empleado=$_GET["id_empleado"];

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}
?>
<div style="page-break-after: always;" align="center">
<table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=3 face="<?= $fuente_general; ?>"><b>Preparación de Platos para el día <?=date("d/m/Y",$fecha_time_stamp)?></b></font></td>
        </tr>
</table>
        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="700">
        <tr>
        <td colspan=3><font size=3 face="<?= $fuente_general; ?>"><b>Primeros platos</b></font></td>
        </tr>
        <?php 
        // Primeros platos
        
        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos,menu_calendario WHERE menu_platos.id_plato=menu_calendario.comida1 AND menu_calendario.fecha=? GROUP BY nombre_plato ORDER BY nombre_plato', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $columnas=3;
        $col_act=1;
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {
        
        $id_plato=$datos_plato["id_plato"];
        $nombre_del_plato=urldecode($datos_plato["nombre_plato"]);
        
        if ($col_act==1) {
        ?>
        <tr valign="middle">
        <?php 
        }
        ?>
        <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="<?=$nombre_del_plato?>" onclick="window.location.href='lista_platos.php?id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>'" style="font-size:12px; width: 255; height: 64"></font></td>
        <?php 
        if ($col_act==$columnas) {
        ?>
        </tr>
        <?php 
        $col_act=0;
        }
        $col_act++;
        }
        ?>
        <tr>
        <td colspan=3><font size=3 face="<?= $fuente_general; ?>"><b>Segundos platos</b></font></td>
        </tr>
        <?php 
        // Segundos platos

        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos,menu_calendario WHERE menu_platos.id_plato=menu_calendario.comida2 AND menu_calendario.fecha=? GROUP BY nombre_plato ORDER BY nombre_plato', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $columnas=3;
        $col_act=1;
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $id_plato=$datos_plato["id_plato"];
        $nombre_del_plato=urldecode($datos_plato["nombre_plato"]);

        if ($col_act==1) {
        ?>
        <tr valign="middle">
        <?php 
        }
        ?>
        <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="<?=$nombre_del_plato?>" onclick="window.location.href='lista_platos.php?id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>'" style="font-size:12px; width: 255; height: 64"></font></td>
        <?php 
        if ($col_act==$columnas) {
        ?>
        </tr>
        <?php 
        $col_act=0;
        }
        $col_act++;
        }
        // Terceros platos

        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos,menu_calendario WHERE menu_platos.id_plato=menu_calendario.comida3 AND menu_calendario.fecha=? GROUP BY nombre_plato ORDER BY nombre_plato', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $columnas=3;
        $col_act=1;
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $id_plato=$datos_plato["id_plato"];
        $nombre_del_plato=urldecode($datos_plato["nombre_plato"]);

        if ($col_act==1) {
        ?>
        <tr valign="middle">
        <?php 
        }
        ?>
        <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="<?=$nombre_del_plato?>" onclick="window.location.href='lista_platos.php?id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>'" style="font-size:12px; width: 255; height: 64"></font></td>
        <?php 
        if ($col_act==$columnas) {
        ?>
        </tr>
        <?php 
        $col_act=0;
        }
        $col_act++;
        }
        // Cuartos platos

        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos,menu_calendario WHERE menu_platos.id_plato=menu_calendario.comida4 AND menu_calendario.fecha=? GROUP BY nombre_plato ORDER BY nombre_plato', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $columnas=3;
        $col_act=1;
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $id_plato=$datos_plato["id_plato"];
        $nombre_del_plato=urldecode($datos_plato["nombre_plato"]);

        if ($col_act==1) {
        ?>
        <tr valign="middle">
        <?php 
        }
        ?>
        <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="<?=$nombre_del_plato?>" onclick="window.location.href='lista_platos.php?id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>'" style="font-size:12px; width: 255; height: 64"></font></td>
        <?php 
        if ($col_act==$columnas) {
        ?>
        </tr>
        <?php 
        $col_act=0;
        }
        $col_act++;
        }
        ?>
        <tr>
        <td colspan=3><font size=3 face="<?= $fuente_general; ?>"><b>Postres</b></font></td>
        </tr>
        <?php 
        // Postres 1

        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos,menu_calendario WHERE menu_platos.id_plato=menu_calendario.postre1 AND menu_calendario.fecha=? GROUP BY nombre_plato ORDER BY nombre_plato', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $columnas=3;
        $col_act=1;
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $id_plato=$datos_plato["id_plato"];
        $nombre_del_plato=urldecode($datos_plato["nombre_plato"]);

        if ($col_act==1) {
        ?>
        <tr valign="middle">
        <?php 
        }
        ?>
        <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="<?=$nombre_del_plato?>" onclick="window.location.href='lista_platos.php?id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>'" style="font-size:12px; width: 255; height: 64"></font></td>
        <?php 
        if ($col_act==$columnas) {
        ?>
        </tr>
        <?php 
        $col_act=0;
        }
        $col_act++;
        }
        ?>
        <tr>
        <td colspan=3><font size=3 face="<?= $fuente_general; ?>"><b>Meriendas</b></font></td>
        </tr>
        <?php 
        // Meriendas 1

        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos,menu_calendario WHERE menu_platos.id_plato=menu_calendario.merienda1 AND menu_calendario.fecha=? GROUP BY nombre_plato ORDER BY nombre_plato', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $columnas=3;
        $col_act=1;
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $id_plato=$datos_plato["id_plato"];
        $nombre_del_plato=urldecode($datos_plato["nombre_plato"]);

        if ($col_act==1) {
        ?>
        <tr valign="middle">
        <?php 
        }
        ?>
        <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="<?=$nombre_del_plato?>" onclick="window.location.href='lista_platos.php?id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>'" style="font-size:12px; width: 255; height: 64"></font></td>
        <?php 
        if ($col_act==$columnas) {
        ?>
        </tr>
        <?php 
        $col_act=0;
        }
        $col_act++;
        }
        // Meriendas 2

        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos,menu_calendario WHERE menu_platos.id_plato=menu_calendario.merienda2 AND menu_calendario.fecha=? GROUP BY nombre_plato ORDER BY nombre_plato', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $columnas=3;
        $col_act=1;
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $id_plato=$datos_plato["id_plato"];
        $nombre_del_plato=urldecode($datos_plato["nombre_plato"]);

        if ($col_act==1) {
        ?>
        <tr valign="middle">
        <?php 
        }
        ?>
        <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><input type="button" value="<?=$nombre_del_plato?>" onclick="window.location.href='lista_platos.php?id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>'" style="font-size:12px; width: 255; height: 64"></font></td>
        <?php 
        if ($col_act==$columnas) {
        ?>
        </tr>
        <?php 
        $col_act=0;
        }
        $col_act++;
        }
        ?>
        </table><br>
<input type="button" value="Salir" onclick="window.location.href='ver_platos_hoy.php?id_empleado=<?=$id_empleado;?>'" style="font-size:18px; width: 200; height: 64">
</div>
</body>
</html>

