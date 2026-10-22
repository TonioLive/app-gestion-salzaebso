<?php  require("../../configuracion/parte_superior_docente.php"); ?>
<?php 

$id_incidencia=$_GET["id_incidencia"];

$busca_incidencia=db_query_params('SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.id_incidencia=?', 's', array($id_incidencia));

$datos_incidencia=db_fetch_array($busca_incidencia);

$nombre_tipo_actual=$datos_incidencia["nombre_tipo"];
$fecha=strtotime($datos_incidencia["fecha_incidencia"]);
$hora_incidencia=date("H:i",strtotime($datos_incidencia["hora_incidencia"]));
$mes=date("n",$fecha);
$mesnombre=$nombre_mes[$mes];
$medidas_adoptadas1=$datos_incidencia["medidas_adoptadas1"];
$descripcion=$datos_incidencia["descripcion"];
$medidas_adoptadas=$datos_incidencia["medidas_adoptadas"];
$resuelta=$datos_incidencia["resuelta"];

        // Busca nombre de la persona que notifica

        $busca_notificador=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_incidencia["notificada_por"]);

        if (db_num_rows($busca_notificador)!=0) {
                $datos_notificador=db_fetch_array($busca_notificador);
                $nombre_notificador=$datos_notificador["Apellido1"]." ".$datos_notificador["Apellido2"].", ".$datos_notificador["Nombre"];
                } else {
                $nombre_notificador="Desconocido";
                }


        // Busca nombre de la persona que hace el seguimiento

        $busca_seguimiento=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_incidencia["seguimiento_por"]);
        $seguimiento_por=$datos_incidencia["seguimiento_por"];
        if (db_num_rows($busca_seguimiento)==0) {
                $busca_seguimiento=db_query("SELECT * FROM usuarios WHERE id_usuario=$idUsuario");
                $seguimiento_por=$idUsuario;
                }
         $datos_seguimiento=db_fetch_array($busca_seguimiento);
         $nombre_seguimiento=$datos_seguimiento["Apellido1"]." ".$datos_seguimiento["Apellido2"].", ".$datos_seguimiento["Nombre"];
         
        // Busca nombre de la persona que ha provocado la incidencia

        $busca_incidenciado=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($datos_incidencia["id_usuario_incidencia"]));

        if (db_num_rows($busca_incidenciado)!=0) {
                $datos_incidenciado=db_fetch_array($busca_incidenciado);
                $nombre_incidenciado=$datos_incidenciado["Apellido1"]." ".$datos_incidenciado["Apellido2"].", ".$datos_incidenciado["Nombre"];
                $tipo_usuario_inci="";
                if ($datos_incidenciado["tipo_inscripcion"]==1) {
                        $tipo_usuario_inci=" (Alumno)";
                        }
                if ($datos_incidenciado["tipo_inscripcion"]==2) {
                        $tipo_usuario_inci=" (Padre)";
                        }
                if ($datos_incidenciado["tipo_inscripcion"]==4) {
                        $tipo_usuario_inci=" (Personal lápices)";
                        }
                } else {
                $nombre_incidenciado="Desconocido";
                $tipo_usuario_inci="";
                }

?>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2>Incidencia de <b><?=$nombre_tipo_actual;?></b><br><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?><br><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b><br><br>
    <b>Seguimiento iniciado por:</b> <?=$nombre_notificador?></font></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
  <table>
  <tr>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Incidencia:</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$datos_incidencia["nombre_incidencia"]?></b></font></td>
  <tr>
  <tr>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Notificada por:</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_notificador?></b></font></td>
  <tr>
  <tr>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Alumno/Padre/Personal Lápices:</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_incidenciado;?><?=$tipo_usuario_inci;?></b></font></td>
  <tr>
  <tr>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Fecha de notificación:</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=date("d/m/Y",$fecha);?></b></font></td>
  <tr>
  <tr>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Hora de notificación:</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$hora_incidencia;?></b></font></td>
  <tr>
  </table><br>
  <b>Descripción de la incidencia:</b><br><textarea style="font-family: Arial; font-size: 11px" name="descripcion" cols=85 rows=12 readonly><?=urldecode($descripcion);?></textarea><br><br>
  <b>Medidas adoptadas por la persona que notifica la incidencia:</b><br><textarea style="font-family: Arial; font-size: 11px" name="medidas_adoptadas1" cols=85 rows=6 readonly><?=urldecode($medidas_adoptadas1);?></textarea><br><br>
  <font size=2><b>Estado de la incidencia:</b> <?php  if ($resuelta==0) { print " Pendiente de resolución"; } else { print " Resuelta"; } ?><br><br>
  <b>Medidas adoptadas para la resolución de la incidencia:</b><br><textarea style="font-family: Arial; font-size: 11px" name="medidas_adoptadas" cols=85 rows=6 readonly><?=urldecode($medidas_adoptadas);?></textarea><br><br>
  <input type="button" onclick="window.location.href='mis_incidencias.php?c=<?=$resuelta;?>'" value="Salir"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

