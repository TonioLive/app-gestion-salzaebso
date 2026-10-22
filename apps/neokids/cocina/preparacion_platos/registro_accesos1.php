<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<html>
<head>
<meta name="Description" content="<?php  echo $meta_description; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords; ?>">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo1.php"); ?>
</style>
<script lenguaje="Javascript">
self.focus();
</script>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="no">
<div align="left">
<?php 
$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

$idUsuario=$_POST["id_usuario"];
$clave=$_POST["clave"];

$busca_datos_usuario=db_query("SELECT * FROM mesas WHERE id_mesa=$idUsuario");

$paso_ok=true;
if (db_num_rows($busca_datos_usuario)!=0) {

$datos_usuario=db_fetch_array($busca_datos_usuario);

if (($datos_usuario["clave"]!=$clave) or ($clave="")) {
        $paso_ok=false;
        }

}

$hora_actual=strtotime(date("H:i:s"));
$hora_cambio_turno=strtotime("14:30:00");

if ($hora_actual>$hora_cambio_turno) {
$turno_actual=1;
} else {
$turno_actual=0;
}

?>
<div align="center" id="principal_mesas" style="display:block;padding:0px;border:0px solid">
<table width=710 height=710 cellpadding=0 cellspacing=0 border=0>
<tr>
<td width=100%>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br><br>
                <font size=5 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font><br><br>
                <?php 
                if ($paso_ok==true) {
                ?>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td colspan=2 align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><b>Turno mañana (o jornada continua *)</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                </tr>
        <?php 
        // Busca si ya está registrado
        $turno=0;

        $todos_cerrados=true;
        $busca_registro_entrada=db_query("SELECT * FROM asistencia_empleados WHERE id_usuario=".$idUsuario." AND turno=$turno AND id_centro_asistencia=$id_tienda AND fecha='".date("Y-m-d")."' ORDER BY id_asistencia") or die(db_error());

        while($datos_registro=db_fetch_array($busca_registro_entrada)) {
        ?>
        <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font>
        </td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        $todos_cerrados=false;
        ?>
        <input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)" style="font-size: 18px">
        <?php 
        } else {
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font>
        <?php 
                        }
        ?>
        </td></tr>
        <?php 
                }

        if ($todos_cerrados==true) {
        ?>
        <tr bgcolor="#efefef">
        <td align="center"><?php 
        if ($turno_actual!=0) {
        ?><input type="button" value="Registrar entrada" disabled style="font-size: 18px"><?php 
        } else {
        ?><input type="button" value="Registrar entrada" onclick="registro_entrada(<?=$turno;?>)" style="font-size: 18px"><?php 
        }
        ?></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled style="font-size: 18px"></td>
        </tr>
        <?php 
        }
        ?>
        <tr bgcolor="#FFFFFF">
                <td colspan=2 align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><b>Turno tarde</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=50%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        // Busca si ya está registrado
        $turno=1;
        $todos_cerrados=true;
        $busca_registro_entrada=db_query("SELECT * FROM asistencia_empleados WHERE id_usuario=".$idUsuario." AND turno=$turno AND id_centro_asistencia=$id_tienda AND fecha='".date("Y-m-d")."' ORDER BY id_asistencia") or die(db_error());

        while($datos_registro=db_fetch_array($busca_registro_entrada)) {
        ?>
        <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font>
        </td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        $todos_cerrados=false;
        ?>
        <input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)" style="font-size: 18px">
        <?php 
        } else {
        ?>
        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font>
        <?php 
                        }
        ?>
        </td></tr>
        <?php 
                }
                
        if ($todos_cerrados==true) {
        ?>
        <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada(<?=$turno;?>)" style="font-size: 18px"></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled style="font-size: 18px"></td>
        </tr>
        <?php 
        }
        ?>
        </table>
               <font size=2><b>* NOTA:</b> En caso de hacer jornada continua, fichar solo la entrada y la salida del primer turno</font><br><br>
               <input type="button" onclick="self.location.href='mapa_mesas.php'" value="Salir" style="font-size: 18px"><br><br><br>
        <?php 
        } else {
        ?>
        <br><br><font size=5><b>La clave introducida es incorrecta<br><br><br><input type="button" style="font-size: 18px" value="Volver" onclick="history.go(-1)"></font><br><br><br>
        <?php 
        }
        ?>
</td>
</tr>
</table>
</div>
<script language="Javascript">
function registro_entrada(turno) {
        window.location.href='registro_entrada.php?id_usuario=<?=$idUsuario?>&turno='+turno;
        }
function registro_salida(id_registro) {
        window.location.href='registro_salida.php?id_registro='+id_registro;
        }
</script>
<?php 
db_close();
?>
</div>
</body>
</html>
