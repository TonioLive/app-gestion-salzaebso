<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$origen_cita=$_GET["oc"];

$idcita=$_GET["id"];

?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<img src="<?= $url_web; ?>/imagen/logo1.gif">
<div align="center">
<center>

<?php 

$db_cc=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_cc);

$consulta_cita=db_query("SELECT * FROM Agenda WHERE id=".$idcita) or die (db_error());

if (db_num_rows($consulta_cita)!=0) {

$rscita=db_fetch_array($consulta_cita);

$ancho_tabla=325;
?>
      <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="FFFFFF" align="center">
<br><br><font size=2 face="<?= $fuente_general?>">
<b>¿Realmente desea eliminar la cita?</b><br><br>"<?= $rscita["Titulo"]; ?>"<br>Del día <?= date("j",strtotime($rscita["Fecha"])); ?> de <?= $nombre_mes[date("n",strtotime($rscita["Fecha"]))]; ?>
<br><br>
<a href="borra_cita2.php?oc=<?=$origen_cita;?>&idCita=<?= $idcita; ?>">Aceptar</a> | <?php  if ($origen_cita!=1) { ?><a href="javascript:history.go(-1)">Cancelar</a><?php  } else { ?><a href="javascript:window.close()">Cancelar</a><?php  } ?>
<br><br></font>
                        </td>
                </tr>
    </table>
<?php 
} 
db_close($db_cc);
?>
<br>
<a href="javascript:window.close()">Cerrar la ventana</a>
</small>
</p>
</body>
</html>

