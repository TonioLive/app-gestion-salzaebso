<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$errores="";

$origen_cita=$_POST["oc"];
$idcita=$_POST["idcita"];
$id_centro_cita=$_POST["id_centro_cita"];

$id_aula_cita=$_POST["id_aula_cita".$id_centro_cita];

$dia=$_POST["dia"];
$mes=$_POST["mes"];
$ano=$_POST["ano"];

$hora=$_POST["hora"];
$min=$_POST["min"];

$tiempo=$hora.":".$min;
$fecha=$ano."/".$mes."/".$dia;

$texto=$_POST["Texto"];
$titulo=$_POST["Titulo"];

if (!checkdate(intval($mes),intval($dia),intval($ano))) {
  $numero_errores=$numero_errores+1;
  $errores=$errores."La fecha escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b><br><br>";
}

if (strlen($texto)==0) {
  $numero_errores=$numero_errores+1;
  $errores=$errores."No has introducido el texto de la cita.</b><br><br>";
} 
if (strlen($titulo)==0) {
  $numero_errores=$numero_errores+1;
  $errores=$errores."No has introducido el título de la cita.</b><br><br>";
} 

if ($numero_errores==0)
{


$avisar=$_POST["avisar"];

if (strlen($avisar)!=0) {
        $cita_timestamp=strtotime("$ano-$mes-$dia");
        $dia_avisar=date("Y-m-d",$cita_timestamp-(86400*$avisar));
        } else {
        $dia_avisar="";
        }

$db_cc=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_cc);

db_query("UPDATE Agenda SET avisar='$dia_avisar', id_aula='$id_aula_cita', id_centro='$id_centro_cita', Titulo='$titulo', Texto='$texto', Fecha='$fecha', Hora='$tiempo:00' WHERE id=$idcita") or die (db_error());

// Añade asistentes

// YO
$busca_si_estoy=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario=$idUsuario LIMIT 0,1");
if (db_num_rows($busca_si_estoy)==0) {
        db_query("INSERT INTO agenda_asistentes (id_cita,id_usuario) VALUES ('$idcita','$idUsuario');");
        }
        
$solo_yo=$_POST["solo_yo"];
if ($solo_yo==0) {
if (($nivel_acceso==4) or ($nivel_acceso==3)) {

        db_query("DELETE FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario Is Null");

        for ($cnivel=1;$cnivel<=4;$cnivel++) {
                if ($_POST["asistentes_nivel".$cnivel]==1) {
                        $busca_si_esta=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=$cnivel LIMIT 0,1");
                        if (db_num_rows($busca_si_esta)==0) {
                                db_query("INSERT INTO agenda_asistentes (id_cita,nivel) VALUES ('$idcita','$cnivel');");
                                }
                        }
                }

        } else {
        $busca_si_esta=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=2 LIMIT 0,1");
        if (db_num_rows($busca_si_esta)==0) {
                db_query("INSERT INTO agenda_asistentes (id_cita,nivel) VALUES ('$idcita','2');");
                }
        $busca_si_esta=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=3 LIMIT 0,1");
        if (db_num_rows($busca_si_esta)==0) {
                db_query("INSERT INTO agenda_asistentes (id_cita,nivel) VALUES ('$idcita','3');");
                }
        $busca_si_esta=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=4 LIMIT 0,1");
        if (db_num_rows($busca_si_esta)==0) {
                db_query("INSERT INTO agenda_asistentes (id_cita,nivel) VALUES ('$idcita','4');");
                }
        }
        }
if ($solo_yo==1) {
        db_query("DELETE FROM agenda_asistentes WHERE id_cita=$idcita AND (id_usuario<>$idUsuario OR id_usuario Is Null)");
        }
if ($solo_yo==2) {
        // Lo deja como está
        }

db_close($db_cc);

mt_srand((double)microtime()*1000000);

  if ($origen_cita!=1) {
        header("Location: ".$url_web."/actividades.php?dia=".strtotime($fecha)."&id=".(mt_rand(0,10000000)/10000000)."&id_curso=".$id_curso);
        } else {
        ?>
<script language="Javascript">
self.opener.actualizarCitas();
window.close();
</script>
        <?php 
        }

}
  else
{

?>
<html>

<head>
<title><?php    echo $titulo_web; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
</head>
<body bgcolor="<?= $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<img src="<?= $url_web; ?>/imagen/logo1.gif">
<p align="center">
<?php 
$ancho_tabla=325;
?>
      <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/Curso/imagen/esquina_si.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/Curso/imagen/esquina_sd.gif" border=0 width=8 height=8></td>
                </tr>
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="FFFFFF" colspan=3 align="center">
<font size=2 face="<?= $fuente_general?>"><b>
Se han producido los siguientes errores:
</b>
<br><br><small>
<?php 
  print $errores."<br><br>";
?></small>
</p>
<p align="center">
<br>
<br>
<a href="javascript:history.go(-1)">Volver y corregir los errores</a> | <a href="javascript:window.close()">Cerrar la ventana</a>
</p></font>
                        </td>
                </tr>
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/Curso/imagen/esquina_ii.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/Curso/imagen/esquina_id.gif" border=0 width=8 height=8></td>
                </tr>
    </table>
</body>
</html>
<?php 
}

?>

