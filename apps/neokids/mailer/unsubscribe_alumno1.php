<?php 
require("../configuracion/config.php");

$cuenta=$_GET["cuenta"];

$errores="";

if (strlen($cuenta)!=0) {

// Comprueba si es correcta
if ((strpos($cuenta,"@") ? strpos($cuenta,"@")+1 : 0)==0 || (strpos($cuenta," ") ? strpos($cuenta," ")+1 : 0)!=0 || (strpos($cuenta,".") ? strpos($cuenta,".")+1 : 0)==0)
        {

          $errores="La cuenta de email introducida parece ser incorrecta, falta la arroba (@) o contiene caracteres no permitidos (espacios, acentos, etc.).";
          
        } else {

		// Inserta dirección en lista de no-recibir
		db_query_params('INSERT INTO no_recibir_mail (email) VALUES (?);', 's', array((string)($cuenta))) or die(db_error());
		
        }
        } else {
        header("Location: unsubscribe_alumno.php");
        }

db_close();
require("../configuracion/parte_superior.php");
?>
<div align="center"><br>
<font size=3 face="<?=$fuente_general?>">
<?php 
if ($errores=="") {
?>
La cuenta de correo (<?=$cuenta?>) ha sido dada de baja de nuestras listas de correo.<br><br>
<br><br>
<input type="button" onclick="window.location.href='http://www.neo-kids.es'" value="Salir">
<?php 
} else {
?>
<?=$errores?><br><br>
<br><br>
<input type="button" onclick="window.location.href='unsubscribe.php'" value="Volver a intentarlo"> <input type="button" onclick="window.location.href='/'" value="Salir">
<?php 
}
?>
</font>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>