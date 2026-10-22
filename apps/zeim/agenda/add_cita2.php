<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$numero_errores=0;
$errores="";

$origen_cita=$_POST["oc"];
$dia_sel=$_POST["dia_sel"];

if (strlen($dia_sel)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$dia_sel;
}

$fecha_agenda=date("Y-m-d",$fecha_time_stamp);

$id_centro_cita=$_POST["id_centro_cita"];

$id_aula_cita=$_POST["id_aula_cita".$id_centro_cita];


$hora=$_POST["hora"];
$min=$_POST["min"];
$curso_seleccionado=$_POST["tipo"];

$texto=$_POST["Texto"];
if (strlen($texto)==0)
{
  $numero_errores=$numero_errores+1;
  $errores=$errores."No se ha introducido un texto para la actividad.<br><br>";
} 
$titulo=$_POST["Titulo"];
if (strlen($titulo)==0)
{
  $numero_errores=$numero_errores+1;
  $errores=$errores."No se ha introducido un título<br><br>";
} 

if ($numero_errores==0)
{

$avisar=$_POST["avisar"];

if (strlen($avisar)!=0) {
        $dia_avisar=date("Y-m-d",$fecha_time_stamp-(86400*$avisar));
        } else {
        $dia_avisar="";
        }
        
$enviarmail=$_POST["enviarmail"];

if (strlen($enviarmail)==0) {
        $enviarmail=0;
        }

$db_cc=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_cc);

db_query_params('INSERT INTO Agenda (Nombre_usu) VALUES (?)', 's', array((string)($identificador))) or die (db_error());

$idcita=db_insert_id();


$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)($fecha_agenda), (string)($fecha_agenda)));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
                
if (strlen($id_centro_cita)==0) {
        $id_centro_cita=$centro_actual;
        }


if (strlen($id_aula_cita)==0) {

$busca_aula_asignada=db_query("SELECT * FROM centro_aulas WHERE docente_titular=$idUsuario AND id_curso=$id_curso_actual LIMIT 0,1");

if (db_num_rows($busca_aula_asignada)!=0) {
        $datos_aula_asignada=db_fetch_array($busca_aula_asignada);
        $id_aula_cita=$datos_aula_asignada["id_aula"];
        } else {
        $id_aula_cita=0;
        }
}


db_query("UPDATE Agenda SET avisar='$dia_avisar', enviarmail='$enviarmail', id_aula='$id_aula_cita', id_centro='$id_centro_cita', Titulo='$titulo', Texto='$texto', Fecha='$fecha_agenda', Hora='$hora:$min:00' WHERE id=$idcita") or die (db_error());

// Añade asistentes

// YO

db_query_params('INSERT INTO agenda_asistentes (id_cita,id_usuario) VALUES (?,?);', 'ss', array((string)($idcita), (string)($idUsuario)));


$solo_yo=$_POST["solo_yo"];
if ($solo_yo!=1) {

if (($nivel_acceso==4) or ($nivel_acceso==3)) {

        for ($cnivel=1;$cnivel<=11;$cnivel++) {
                if ($_POST["asistentes_nivel".$cnivel]==1) {
                        db_query_params('INSERT INTO agenda_asistentes (id_cita,nivel) VALUES (?,?);', 'ss', array((string)($idcita), (string)($cnivel)));
                        }
                }
                
        } else {
        if ($nivel_acceso>4) {
                db_query_params('INSERT INTO agenda_asistentes (id_cita,nivel) VALUES (?,?);', 'ss', array((string)($idcita), (string)($nivel_acceso)));
                } else {
                db_query_params('INSERT INTO agenda_asistentes (id_cita,nivel) VALUES (?,\'2\');', 's', array((string)($idcita)));
                db_query_params('INSERT INTO agenda_asistentes (id_cita,nivel) VALUES (?,\'3\');', 's', array((string)($idcita)));
                db_query_params('INSERT INTO agenda_asistentes (id_cita,nivel) VALUES (?,\'4\');', 's', array((string)($idcita)));
                }
        }
        } else {
        db_query("DELETE FROM agenda_asistentes WHERE id_cita=$idcita AND (id_usuario<>$idUsuario OR id_usuario Is Null)");
        }

db_close($db_cc);

  mt_srand((float)microtime()*1000000);
  if ($origen_cita!=1) {
        header("Location: ".$url_web."/actividades.php?dia=".$fecha_time_stamp."&id=".(mt_rand(0,10000000)/10000000)."&id_curso=".$id_curso);
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
<style type="text/css">
<?php  require("../configuracion/estilos/estilo1.php"); ?>
</style>
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

