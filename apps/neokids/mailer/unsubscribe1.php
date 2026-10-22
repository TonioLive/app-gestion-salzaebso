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

        $busca_fila=db_query_params('SELECT * FROM contactos WHERE email like ? or  email_padre2 like ? or   email_familia like ?;', 'sss', array('%' . (string)($cuenta) . '%', '%' . (string)($cuenta) . '%', '%' . (string)($cuenta) . '%')) or die(db_error());

        if (db_num_rows($busca_fila)!=0) {

                // Borra todo
                        $delete_todo2=db_query_params('UPDATE contactos SET no_publicidad=1 WHERE email like ? or  email_padre2 like ? or   email_familia like ?', 'sss', array('%' . (string)($cuenta) . '%', '%' . (string)($cuenta) . '%', '%' . (string)($cuenta) . '%'));
                        } else {
                        $errores="La cuenta de correo introducida ($cuenta_original) no se ha encontrado en nuestra base de datos.";
                        }
        }
        } else {
        header("Location: unsubscribe.php");
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