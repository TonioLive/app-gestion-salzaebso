<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
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

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";

    // Inscripcion del hijo, mira a ver si está activo actualmente

    // $busca_datos_alumno=mysql_query("SELECT * FROM alumnos WHERE id_usuario=$id_hijo AND fecha_matricula<='".date("Y-m-d")."' AND fecha_baja>='".date("Y-m-d")."' ORDER BY id_curso DESC") or die(mysql_error()."- No se encuentran los datos del alumno.");
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.id_curso=? ORDER BY alumnos.id_curso DESC', 'ssss', array($id_usuario, (string)(date("Y-m-d")), (string)(date("Y-m-d")), $id_curso_actual)) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)==0) {
        // El niño no está en el centro
        ?>
        <font size=3>Lo sentimos, pero hoy no puedes acceder a las cámaras.</b><br><br>
        Hoy tu hijo/a no asiste a la escuela.<br><br>
        <a href="http://www.escuelas-infantiles.com"><b>Salir</b> <img border=0 align="absmiddle" src="imag/lapizmueveizq.gif"></a></font>
        <?php 
        } else {
        while($datos_alumno=db_fetch_array($busca_datos_alumno)) {

        if ($datos_alumno["comedor"]==2) {
                $asistencia_comedor=false;
                } else {
                $asistencia_comedor=true;
                }
                
        // Comprueba si se queda a comer
        $busca_si_come=db_query_params('SELECT * FROM servicios_consumidos WHERE servicios_consumidos.id_alumno=? AND fecha=? AND servicios_consumidos.tipo_menu<>2;', 'ss', array($id_hijo, (string)(date("Y-m-d")))) or die(db_error());
        if (db_num_rows($busca_si_come)!=0) {
                $asistencia_comedor=true;
                }
        
        if ($datos_alumno["sexo"]=="V") {
                        $sufijo_hijo="o";
                        } else {
                        $sufijo_hijo="a";
                        }
        
        // Finalmente muestra las cámaras
        
        // Datos de la escuela del niño
        $busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_alumno["id_centro"]));
        
        $datos_escuela=db_fetch_array($busca_datos_escuela);
        
        // Datos del aula del niño
        $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_alumno["aula"]));

        $datos_aula=db_fetch_array($busca_datos_aula);
        
// Camara del aula del niño
$id_webcam=0;
$horario_camara="";

$busca_datos_camara=db_query_params('SELECT * FROM webcam WHERE id_aula=?', 's', array($datos_alumno["aula"]));

$encendida=false;
$fuera_de_servicio=false;

$hora_inicio_camara_def=0;
$hora_fin_camara_def=0;

while($datos_camara=db_fetch_array($busca_datos_camara)) {

        $hora_inicio_camara=strtotime($datos_camara["hora_conexion"]);
        $hora_fin_camara=strtotime($datos_camara["hora_desconexion"]);

        $horario_camara=$horario_camara."De ".date("H:i",$hora_inicio_camara)." a ".date("H:i",$hora_fin_camara)." Hrs.<br>";

        $hora_actual=strtotime(date("H:i:s"));

        $timeout_camara=$hora_fin_camara-$hora_actual;

        $timeout_camara=10;
        
        if (($hora_actual>=$hora_inicio_camara) and ($hora_actual<=$hora_fin_camara)) {
                $id_webcam=$datos_camara["id_webcam"];
                $encendida=true;
                $fuera_de_servicio=false;
                $usuario_camara=$datos_camara["usuario_visor"];
                $password_camara=$datos_camara["password_visor"];
                $url_camara=$datos_camara["url_webcam"];
                $puerto_camara=$datos_camara["puerto_webcam"];
                $modelo_camara=$datos_camara["modelo"];
                if (($hora_inicio_camara_def==0) or ($hora_inicio_camara_def>strtotime($datos_camara["hora_conexion"]))) {
                        $hora_inicio_camara_def=strtotime($datos_camara["hora_conexion"]);
                        }
                if (($hora_fin_camara_def==0) or ($hora_fin_camara_def<strtotime($datos_camara["hora_desconexion"]))) {
                        $hora_fin_camara_def=strtotime($datos_camara["hora_desconexion"]);
                        }
                }

        }
        
$hora_inicio_camara=$hora_inicio_camara_def;
$hora_fin_camara=$hora_fin_camara_def;

/*
if ($id_webcam==0) {
        $encendida=false;
        $fuera_de_servicio=true;
        }
*/

?>
                                                                <table width="336" height="15" border="0" cellpadding="0" cellspacing="0" align="center">
                                                        <tr>
                                                                <td valign="top" width="360" colspan="3" align="center"><font size=2><b>Escuela:</b> <?=$datos_escuela["nombre_centro"];?> &nbsp;&nbsp;&nbsp;&nbsp;<b>Aula:</b> <?=security_escape($datos_aula["nombre"]);?></font><br>
                                                                <font size=2><b>Alumn<?=$sufijo_hijo?>: </b><?=security_escape($datos_alumno["Nombre"])." ".security_escape($datos_alumno["Apellido1"])." ".security_escape($datos_alumno["Apellido2"])?></font>
                                                                </td>
                                                        </tr>
                                                                <tr>
                                                                <td rowspan="4">&nbsp;</td>
                                                                <td valign="bottom" height="21" colspan="3" width="360"><img src="imag/arriba.jpg"></td>
                                                                <td rowspan="4">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                                <td  align="right" width="8"><img src="imag/izquierda.jpg" width="8"></td>
                                                                <td width="320" height="240" valign="middle" align="center">
<?php 

if ($encendida==true) {

/*
 * 0 => AXIX 206
 * 1 => AXIS 207
 * 2 => EDIMAX
 * 3 => EDIMAX 2013
 * 4 => APPROX 2019
 */

switch($modelo_camara){

	case 0:
		$scriptCamara=1;	
	break;
	
	case 1:
		$scriptCamara=1;	
	break;
	
	case 2:
		$scriptCamara=2;	
	break;
	
	case 3:
		$scriptCamara=3;	
	break;
	
	case 4:
		$scriptCamara=4;
	break;
	
	case 5:
		$scriptCamara=5;
	break;		
	
}

switch($scriptCamara) {
	case 1: // INICIO TIPO 1
	
?>
<SCRIPT LANGUAGE="JavaScript">
  // Set the BaseURL to the url of your camera
  var BaseURL = "http://<?=$url_camara?>:<?=$puerto_camara?>/";
  var BaseURLMozilla = "http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=$puerto_camara?>/";

  // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
  // You may change these numbers, the effect will be a stretch or a shrink of the image
  var DisplayWidth = "320";
  var DisplayHeight = "240";

  // This is the path to the image generating file inside the camera itself



  var File = "axis-cgi/mjpg/video.cgi?resolution=320x240";

  // No changes required below this point

var output = "";

if ((navigator.appName == "Microsoft Internet Explorer") &&
   (navigator.platform != "MacPPC") && (navigator.platform != "Mac68k"))
{
  // If Internet Explorer under Windows then use ActiveX

  output  = '<OBJECT ID="Player" width='
  output += DisplayWidth;
  output += ' height=';
  output += DisplayHeight;
  output += ' CLASSID="CLSID:DE625294-70E6-45ED-B895-CFFA13AEB044" ';
  output += 'CODEBASE="';
  output += BaseURL;
  output += 'activex/AMC.cab#version=3,32,19,0">';
  output += '<PARAM NAME="MediaURL" VALUE="';
  output += BaseURL;
  output += File + '">';
  output += '<param name="MediaType" value="mjpeg-unicast">';
  output += '<param name="MediaUsername" value="<?=$usuario_camara?>">';
  output += '<param name="MediaPassword" value="<?=$password_camara?>">';
  output += '<param name="ShowStatusBar" value="0">';
  output += '<param name="ShowToolbar" value="0">';
  output += '<param name="AutoStart" value="1">';
  output += '<param name="StretchToFit" value="1">';
  output += '<BR><B>Axis Media Control</B><BR>';
  output += 'The AXIS Media Control, which enables you ';
  output += 'to view live image streams in Microsoft Internet';
  output += ' Explorer, could not be registered on your computer.';
  output += '<BR></OBJECT>';
/*
  output =  '<OBJECT ID="Player" width='
  output += DisplayWidth;
  output += ' height=';
  output += DisplayHeight;
  output += ' CLASSID="CLSID:745395C8-D0E1-4227-8586-624CA9A10A8D" ';
  output += 'CODEBASE="';
  output += BaseURL;
  output += '/activex/AMC.cab#version=2,0,22,0">';
  output += '<PARAM NAME="MediaURL" VALUE="';
  output += BaseURL;
  output += File + '">';
  output += '<param name="MediaType" value="mjpeg-unicast">';
  output += '<param name="MediaUsername" value="<?=$usuario_camara?>">';
  output += '<param name="MediaPassword" value="<?=$password_camara?>">';
  output += '<param name="ShowStatusBar" value="0">';
  output += '<param name="ShowToolbar" value="0">';
  output += '<param name="AutoStart" value="1">';
  output += '<param name="StretchToFit" value="1">';
  output += '<BR><B>Axis Media Control</B><BR>';
  output += 'The AXIS Media Control, which enables you ';
  output += 'to view live image streams in Microsoft Internet';
  output += ' Explorer, could not be registered on your computer.';
  output += '<BR></OBJECT>';
*/

} else {
  // If not IE for Windows use the browser itself to display
  theDate = new Date();
  output = '<IMG SRC="';
  output += BaseURLMozilla;
  output += File;
  output += '&dummy=' + theDate.getTime().toString(10);
  output += '" HEIGHT="';
  output += DisplayHeight;
  output += '" WIDTH="';
  output += DisplayWidth;
  output += '" ALT="Camera Image">';

}

document.write(output);
document.Player.ToolbarConfiguration = "play,+snapshot,+fullscreen"


</SCRIPT>
<?php 	
	
	break; //FIN TIPO 1
	
	case 2: // INICIO TIPO 2

if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {

?>
<script language="javascript" src="enet.js"></script>
<SCRIPT language="javascript">
<!--
var ipcam_width = "320";
var ipcam_height = "240";
var ipaddr = "<?=$url_camara?>";
var port = "<?=$puerto_camara?>";
var login = <?php echo json_encode((string)getenv('ZEIM_CAMERA_USER_01'), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
var password = <?php echo json_encode((string)getenv('ZEIM_CAMERA_PASSWORD_01'), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
var code = <?php echo json_encode((getenv('ZEIM_CAMERA_CODE_01') ?: ''), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
//-->
</SCRIPT>
<div id="ipcam_div" align="left">
<script language="JScript" src="ipcam.js"></script>
</div>
<?php 
} else {
?>
<SCRIPT LANGUAGE="JavaScript">
  // Set the BaseURL to the url of your camera

  var BaseURLMozilla = "http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=($puerto_camara+1)?>/";

  // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
  // You may change these numbers, the effect will be a stretch or a shrink of the image
  var DisplayWidth = "320";
  var DisplayHeight = "240";

  // This is the path to the image generating file inside the camera itself

  var File = "mjpg/video.mjpg";

  // No changes required below this point

var output = "";

  theDate = new Date();
  output = '<IMG SRC="';
  output += BaseURLMozilla;
  output += File;
  output += '" HEIGHT="';
  output += DisplayHeight;
  output += '" WIDTH="';
  output += DisplayWidth;
  output += '" ALT="Camera Image">';



document.write(output);

</SCRIPT>
<?php 
	}

	break; // FIN TIPO 2

	case 3: // INICIO TIPO 3

if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {

?>
<iframe name="webcam" src="camara_ie.php?id_webcam=<?=$id_webcam;?>" frameborder="0" scrolling="no" width="320" height="240" marginwidth="0" marginheight="0" seamless="seamless"></iframe>
<?php 
} else {
?>
<SCRIPT LANGUAGE="JavaScript">
  // Set the BaseURL to the url of your camera

  var BaseURLMozilla = "http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=$puerto_camara?>/";

  // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
  // You may change these numbers, the effect will be a stretch or a shrink of the image
  var DisplayWidth = "320";
  var DisplayHeight = "240";

  // This is the path to the image generating file inside the camera itself

  var File = "snapshot.cgi";

  // No changes required below this point

var output = "";

  theDate = new Date();
  output = '<IMG SRC="';
  output += BaseURLMozilla;
  output += File;
  output += '" HEIGHT="';
  output += DisplayHeight;
  output += '" WIDTH="';
  output += DisplayWidth;
  output += '" ALT="Camera Image">';



document.write(output);

</SCRIPT>
<?php 
}

	break; // FIN TIPO 3

	case 4: // INICIO TIPO 4

?>
<img src="http://<?=$url_camara?>:<?=$puerto_camara?>/videostream.cgi?user=<?=$usuario_camara?>&pwd=<?=$password_camara?>" style="width:320px;height:240px" width="320" height="240" vspace="0" hspace="0" border="0">
<?php 
	
	break; // FIN TIPO 4
	
	case 5: // INICIO TIPO 5

?>
<img src="http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=$puerto_camara?>/video.cgi" style="width:320px;height:240px" width="320" height="240" vspace="0" hspace="0" border="0">
<?php 
	
	break; // FIN TIPO 5		
}

} else {
?>
<img src="imag/webcam.gif" border="0"><br><font size=2><b>Cámara desactivada</b><br>
<?php 
if ($fuera_de_servicio==true) {
?>
<br>Temporalmente fuera de servicio<br><br>Disculpen las molestias.</p>
<?php 
} else {
?>
<br>El horario de esta cámara es el siguiente:<br><br><?=$horario_camara?></p>
<?php 
        }
}
?>
                                                                </td>
                                                                <td  align="left" width="32"><img src="imag/derecha.jpg" width="32"></td>
                                                        </tr>
                                                        <tr>
                                                                <td valign="top" width="360" height="39" colspan="3"><img src="imag/abajo.jpg" width="360"></td>
                                                        </tr>
                                                        <?php 
                                                        if (($version_js==0) and ($encendida==true)) {
                                                        ?>
                                                        <!--
                                                        <tr>
                                                                <td valign="top" width="360" height="39" colspan="3" align="center"><font size=1><a href='principal.php?version_js=1'>Si tiene problemas para visualizar las imágenes haga clic aquí.</a></font></td>
                                                        </tr>
                                                        -->
                                                        <?php 
                                                        }
                                                        ?>
                                                      </table>
<?php 
        // Busca otras camaras

        $busca_datos_camaras=db_query_params('SELECT * FROM webcam WHERE id_aula=?', 's', array($datos_alumno["aula"]));

        if (db_num_rows($busca_datos_camaras)!=0) {
        ?><br>
        <table width="336" border="1" cellpadding="2" cellspacing="0" align="center" bordercolor="#000000" style="border-collapse: collapse">
        <tr>
        <td bgcolor="#efefef" align="center" colspan=2><font size=2><b>Cámaras de <?=security_escape($datos_alumno["Nombre"])?></b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2><b>Cámara</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2><b>Horario</b></font></td>
        </tr>
        <?php 
        $contador_camaras=0;
        while($datos_camaras=db_fetch_array($busca_datos_camaras)) {

        $mostrar_camara=true;

        if ($mostrar_camara==true) {
        $contador_camaras++;
        ?>
        <tr>
        <td bgcolor="#ffffff" align="left"><font size=2><a href="javascript:seleccionar_camara(<?=$datos_camaras["id_webcam"];?>,<?=$datos_alumno["padre_de"]?>)"><?=$datos_camaras["nombre_webcam"];?></a></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2><?=date("H:i",strtotime($datos_camaras["hora_conexion"]));?> a <?=date("H:i",strtotime($datos_camaras["hora_desconexion"]));?> Hrs</font></td>
        </tr>
        <?php 
                }
        }
        if ($contador_camaras==0) {
        ?>
        <tr>
        <td bgcolor="#efefef" align="center" colspan=2><font size=2><b>No hay más cámaras disponibles.</b></font></td>
        </tr>
        <?php 
        } else  {
        ?>
        <tr>
        <td bgcolor="#efefef" align="center" colspan=2><font size=2><b>Haz clic con el ratón sobre el nombre para verlas.</b></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        <?php 
        }
        
        // Busca cámaras comunes de la escuela

        $busca_datos_camaras=db_query_params('SELECT * FROM webcam WHERE tipo<>0 AND id_centro=?', 's', array($datos_alumno["id_centro"]));

        if (db_num_rows($busca_datos_camaras)!=0) {
        ?><br>
        <table width="336" border="1" cellpadding="2" cellspacing="0" align="center" bordercolor="#000000" style="border-collapse: collapse">
        <tr>
        <td bgcolor="#efefef" align="center" colspan=2><font size=2><b>Otras cámaras de la escuela</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2><b>Cámara</b></font></td>
        <td bgcolor="#efefef" align="center"><font size=2><b>Horario</b></font></td>
        </tr>
        <?php 
        $contador_camaras=0;
        while($datos_camaras=db_fetch_array($busca_datos_camaras)) {
        
        $mostrar_camara=true;
        
        if (($datos_camaras["tipo"]==1) and ($asistencia_comedor==false)) {
                // Comprueba si el niño esta en el comedor
                // $busca_alumnos_comedor=mysql_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND servicios_consumidos.tipo_menu<>2 AND usuarios.id_usuario=".$datos_hijo["id_usuario"]." ORDER BY alumnos.id_centro, tipo_menu, servicios_consumidos.tipo_menu_comedor, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(mysql_error());
                // if (mysql_num_rows($busca_alumnos_comedor)==0) {
                        $mostrar_camara=false;
                        }
        if ($mostrar_camara==true) {
        $contador_camaras++;
        ?>
        <tr>
        <td bgcolor="#ffffff" align="left"><font size=2><a href="javascript:seleccionar_camara(<?=$datos_camaras["id_webcam"];?>,<?=$datos_alumno["padre_de"]?>)"><?=$datos_camaras["nombre_webcam"];?></a></font></td>
        <td bgcolor="#ffffff" align="center"><font size=2><?=date("H:i",strtotime($datos_camaras["hora_conexion"]));?> a <?=date("H:i",strtotime($datos_camaras["hora_desconexion"]));?> Hrs</font></td>
        </tr>
        <?php 
                }
        }
        if ($contador_camaras==0) {
        ?>
        <tr>
        <td bgcolor="#efefef" align="center" colspan=2><font size=2><b>No hay más cámaras disponibles.</b></font></td>
        </tr>
        <?php 
        } else  {
        ?>
        <tr>
        <td bgcolor="#efefef" align="center" colspan=2><font size=2><b>Haz clic con el ratón sobre el nombre para verlas.</b></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        <?php 
        }
        }
        ?>
        <form name="camaras" method="GET" action="ver_camara.php">
        <input type="hidden" name="id_webcam" value="">
        <input type="hidden" name="id_hijo" value="">
        </form>
        <script language="Javascript">
        function seleccionar_camara(id_camara,id_hijo) {
                document.camaras.id_webcam.value=id_camara;
                document.camaras.id_hijo.value=id_hijo;
                document.camaras.submit();
                }
        </script>
        <?php 
      }
    }
    ?></div>
<script language="Javascript">

        function comprueba_horario() {

        <?php 
        if ($encendida==true) {
        ?>

                var hora_actual = new Date();
                var hora = hora_actual.getHours();
                var minutos = hora_actual.getMinutes();
                var segundos = hora_actual.getSeconds();

                var tiempo=(hora*60*60)+(minutos*60)+segundos;

                var hora_inicio = Number(<?=date("G",$hora_inicio_camara);?>);
                var minutos_inicio = Number(<?=date("i",$hora_inicio_camara);?>);
                var segundos_inicio = Number(<?=date("s",$hora_inicio_camara);?>);

                var tiempo_inicio=(hora_inicio*60*60)+(minutos_inicio*60)+segundos_inicio;

                var hora_fin = Number(<?=date("G",$hora_fin_camara)?>);
                var minutos_fin = Number(<?=date("i",$hora_fin_camara);?>);
                var segundos_fin = Number(<?=date("s",$hora_fin_camara);?>);

                var tiempo_fin=(hora_fin*60*60)+(minutos_fin*60)+segundos_fin;

                if ((tiempo<tiempo_inicio)||(tiempo>tiempo_fin)) {
                        self.location.href="principal.php"
                        }
        <?php 
        }
        ?>

                }

</script>
<a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a>
<?php  require("../configuracion/parte_inferior.php"); ?>
