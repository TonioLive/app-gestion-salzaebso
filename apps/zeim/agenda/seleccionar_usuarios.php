<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$idcita=$_GET["id_cita"];
$nivel=$_GET["nivel"];

if (strlen($idcita)!=0) {
        $consulta_cita=db_query("SELECT * FROM Agenda WHERE id=".$idcita) or die (db_error());
        if (db_num_rows($consulta_cita)!=0) {
                $datos_cita=db_fetch_array($consulta_cita);
                $fecha_control=date("Y-m-d",strtotime($datos_cita["Fecha"]));
                } else {
                $fecha_control=date("Y-m-d");
                }
        } else {
        $fecha_control=date("Y-m-d");
        }



?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Selecciona los asistentes</b></small></small></td>
  </tr>
<?php 
$busca_alumnos=db_query("SELECT * FROM usuarios WHERE nivel_acceso=$nivel ORDER BY id_centro, Apellido1, Apellido2, Nombre");
if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
<form name="listado_asistentes" method="POST" action="seleccionar_usuarios1.php">
<input type="hidden" name="id_cita" value="<?=$idcita?>">
<input type="hidden" name="nivel" value="<?=$nivel?>">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=2 align="center"><input type="submit" value="Guardar cambios y salir" style="font-size: 10px"></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        </tr>
        <?php 
        $escuela_actual=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

        // Comprueba si tiene contrato en vigor
        $codigo=$listado_alumnos["id_usuario"];
        $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());

        if ((db_num_rows($busca_contratos)!=0) or ($nivel!=2)) {
        
        if ($escuela_actual!=$listado_alumnos["id_centro"]) {
                // Busca datos del centro
                $busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($listado_alumnos["id_centro"]));
                if (db_num_rows($busca_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_centro);
                        $nombre_escuela=$datos_centro["nombre_centro"];
                        }

                ?>
        <tr>
        <td colspan=2 align="left" bgcolor="#efefef"><font size=2><b><?=$nombre_escuela?></b></font></td>
        </tr>
                <?php 
                $escuela_actual=$listado_alumnos["id_centro"];
                }
        
        $nombre_completo_alumno=$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"].", ".$listado_alumnos["Nombre"];
        
        // Comprueba si ya está marcado
        
        $busca_marcado=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario=".$listado_alumnos["id_usuario"]." LIMIT 0,1");
        
        ?>
        <tr>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input<?php  if (db_num_rows($busca_marcado)!=0) { print " checked"; } ?> type="checkbox" value="1" name="asistentes_<?=$listado_alumnos["id_usuario"]?>"></font></td>
        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_completo_alumno?></font></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ningún usuario.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
</body>
</html>

