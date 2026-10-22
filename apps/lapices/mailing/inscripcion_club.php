<?php 
require("../configuracion/parte_superior.php");
?>
<div align="center"><br>
<form name="cancelar" action="nueva_cuenta.php" method="POST">
<font size=3 face="<?=$fuente_general?>">
<b>CLUB LÁPICES</b><br><br>Has llegado aquí porque has recibido una invitación para unirte al Club Lápices.<br><br>
Si desea inscribirte a nuestro Club,<br>
introduce aquí tu cuenta de correo y pulsa el botón <b>"Iniciar inscripción"</b><br><br>
Dirección de E-Mail: <input type="text" name="cuenta" size=45><br><br>
<input type="submit" value="Iniciar inscripción"> <input type="button" onclick="window.location.href='http://www.escuelas-infantiles.com'" value="Cancelar"><br><br>
Al pulsar el botón, está aceptando las siguientes condiciones.<br><br>
<font face="<?php  echo $fuente_general; ?>" color="#000000" size=2><b>Condiciones generales</b></font><br><br>
<textarea cols="75" rows="20" style="font-size: 11px; font-family: Arial" readonly><?=$nombre_centro?>

<?=$subtitulo_empresa?>

Sobre la protección de la confidencialidad de los datos

DATOS SOLICITADOS Y TRATAMIENTO

Nuestra web permite poder navegar sin dar información personal y permanecer en el anonimato durante la visita del usuario.

Si el usuario decide enviar su información en nuestra web, se solicitarán los datos estrictamente necesarios para la consecución del fin a qué está destinada nuestra web site, que no es otro que el de facilitar información comercial sobre los productos fabricados y comercializados por la empresa.

A tales efectos se requerirá a los interesados el envío de su información de contacto, lo cual conlleva facilitar datos de carácter personal tales como: nombre, dirección, teléfono, e-mail, etc. Su tratamiento estará enfocado únicamente a la consecución de este fin, siempre dentro del marco normativo establecido.

Mediante el envío de los datos el usuario da su consentimiento al tratamiento de los mismos, así como su cesión a terceros (con los condicionantes que se comentarán en el apartado siguiente).

DERECHO DE OPOSICIÓN, CORRECCIÓN Y ACTUALIZACIÓN DE DATOS

El usuario tiene derecho a acceder a esta información, a rectificarla si los datos son erróneos y a darse de baja del fichero. En este último caso <?=$nombre_centro?> pierde la información automáticamente.

Estos derechos pueden hacerse efectivos enviando un email a la dirección <?=$mail_principal?> en el que se solicita la baja de nuestras bases de datos. En caso de problemas para la realización efectiva on line así como para cualquier tipo de duda o controversia respecto a nuestra política de privacidad de datos se podrán remitir un e-mail a <?=$mail_principal?>.

Nota: El tratamiento de los datos personales está ajustado a la normativa establecida en la Ley Orgánica 15/1999,13 de diciembre, de Protección de Datos de Carácter Personal ( B.O.E 14/12/1999).

-----------------------------------------------
<?=$empresa_curso;?>.
<?=$domicilio_empresa?> - <?=$cp_empresa?> <?=$poblacion_empresa?> (<?=$provincia_empresa?>)</textarea>
<br>
</font>
</form>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

