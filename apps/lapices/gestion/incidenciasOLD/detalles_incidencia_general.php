<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$id_incidencia=$_GET["id_incidencia"];

$busca_incidencia=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.id_incidencia=$id_incidencia");

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

        $busca_incidenciado=db_query("SELECT * FROM usuarios WHERE id_usuario=".$datos_incidencia["id_usuario_incidencia"]);

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
         
$formulario_name="formulario1";
?>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2>Incidencia de <b><?=$nombre_tipo_actual;?></b><br><br>
    <b><?php  echo date("d",$fecha); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha); ?><br><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b><br><br>
    <b>Seguimiento iniciado por:</b> <?=$nombre_notificador?></font></td>
  </tr>
  <form method=POST action="detalles_incidencia_general1.php" name="<?=$formulario_name;?>">
  <input type="hidden" name="id_incidencia" value="<?=$id_incidencia?>">
  <input type="hidden" name="seguimiento_por" value="<?=$seguimiento_por?>">
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
  <table>
  <tr valign="top">
  <td  align="right"><font size=2 face="<?= $fuente_general; ?>">Tipo de incidencia: </font></td>
  <td align="left"><font size=2 face="<?= $fuente_general; ?>"><select name="tipo" size=1 style="font-size: 10px">
        <?php 
        $busca_tipos=db_query("SELECT * FROM incidencias_tipos ORDER BY id_tipo");
        while($tipos=db_fetch_array($busca_tipos)){
        ?>
        <option value="<?=$tipos["id_tipo"];?>"<?php  if ($tipos["id_tipo"]==$datos_incidencia["tipo"]) { print " selected"; } ?>><?=$tipos["nombre_tipo"];?></option>
        <?php 
        }
        ?></select></font></td>
  </tr>
  <tr valign="top">
        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Incidencia:</font></td>
        <td><font size="2" face="Arial, Helvetica"><input type="text" name="nombre_incidencia" size=60 readonly value="<?=urldecode($datos_incidencia["nombre_incidencia"])?>" style="font-size: 10px" onclick="intro_motivo()"></font></td>
  </tr>
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
  <tr valign="top">
        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Tipo de medida adoptada:</font></td>
        <td><font size="2" face="Arial, Helvetica"><input type="text" name="tipo_medida" size=60 readonly value="<?=urldecode($datos_incidencia["tipo_medida"])?>" style="font-size: 10px" onclick="intro_medida()"></font></td>
  </tr>
  </table>
  <br>
  <b>Descripción de la incidencia:</b><br><textarea style="font-family: Arial; font-size: 11px" name="descripcion" cols=85 rows=12><?=urldecode($descripcion);?></textarea><br><br>
  <b>Medidas adoptadas por la persona que notifica la incidencia:</b><br><textarea style="font-family: Arial; font-size: 11px" name="medidas_adoptadas1" cols=85 rows=6><?=urldecode($medidas_adoptadas1);?></textarea><br><br>
  <font size=1>Estado de la incidencia: <input type="radio" name="resuelta"  value="0"<?php  if ($resuelta==0) { print " checked"; } ?>> Pendiente de resolución <input type="radio" name="resuelta" value="1"<?php  if ($resuelta==1) { print " checked"; } ?>>Resuelta<br><br></font>
  <b>Medidas adoptadas para la resolución de la incidencia:</b><br><textarea style="font-family: Arial; font-size: 11px" name="medidas_adoptadas" cols=85 rows=6><?=urldecode($medidas_adoptadas);?></textarea><br><br>
  <input type="button" onclick="enviar();" value="Guardar cambios"> <input type="button" onclick="window.location.href='incidencias_general.php?c=<?=$resuelta;?>'" value="Cancelar"><br><br><br>
  </form>
  </td>
  </tr>
</table>
</center></div>
<script Language="JavaScript">

function enviar() {
                var errores=0;

                var mensajes_error="";

                if (self.document.<?=$formulario_name;?>.descripcion.value=="") { mensajes_error=mensajes_error+"Debes describir la incidencia.\u000A";errores=1; }

                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
        
function intro_motivo() {
        window.open('listado_tipos_incidencias.php','tipos','toolbar=no,resizable=no,width=250,height=400');
        }
function intro_medida() {
        window.open('listado_tipos_medidas.php','tipos','toolbar=no,resizable=no,width=250,height=400');
        }
//-->
</script>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

