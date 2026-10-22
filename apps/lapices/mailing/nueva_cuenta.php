<?php 
require("../configuracion/config.php");

$cuenta=$_POST["cuenta"];
$cuenta_original=$cuenta;

$db_m1=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_m1);

$errores="";

if (strlen($cuenta)!=0) {

// Comprueba si es correcta
if ((strpos($cuenta,"@") ? strpos($cuenta,"@")+1 : 0)==0 || (strpos($cuenta," ") ? strpos($cuenta," ")+1 : 0)!=0 || (strpos($cuenta,".") ? strpos($cuenta,".")+1 : 0)==0)
        {

          $errores="La cuenta de email introducida parece ser incorrecta, falta la arroba (@) o contiene caracteres no permitidos (espacios, acentos, etc.).";

        } else {

        $cuenta=urlencode($cuenta);

        $busca_fila=db_query("SELECT * FROM registro_envio_mensaje WHERE cuenta like '$cuenta%';") or die(db_error());


        if (db_num_rows($busca_fila)==0) {

                // Nuevo
                $busca_ultimo_envio=db_query("SELECT * FROM registro_envio_mensaje ORDER BY id DESC LIMIT 0,1");

                if (db_num_rows($busca_ultimo_envio)!=0) {
                        $datos_ultimo_envio=db_fetch_array($busca_ultimo_envio);
                        $id_ultimo_envio=$datos_ultimo_envio["id"];
                        } else {
                        $id_ultimo_envio=0;
                        }

                        $delete_todo2=db_query("INSERT INTO registro_envio_mensaje (fecha,hora,cuenta,id,enviado,resultado) VALUES ('".date("Y-m-d")."','".date("H:i:s")."','$cuenta','$id_ultimo_envio',1,1)");
                        } else {
                        $errores="La cuenta de correo introducida ($cuenta_original) ya había sido introducida previamente.";
                        }
        }
        } else {
        header("Location: inscripcion_club.php");
        }

db_close();

require("../configuracion/parte_superior_no_config.php");
?>
<div align="center"><br>
<form name="cancelar" action="nueva_cuenta.php" method="POST">
<font size=3 face="<?=$fuente_general?>"><b>CLUB LÁPICES</b><br><br>
<?php 
if ($errores=="") {
?>
Tu cuenta de correo (<?=$cuenta_original?>) ha sido incluida<br>en la lista de interesados en formar parte del <b>Club Lápices</b>.<br><br>
En breve recibirás noticias de las actividades programadas para los mienbros del club.<br><br>
Grácias por tu interés.<br><br>
<input type="button" onclick="window.location.href='http://www.escuelas-infantiles.com'" value="Salir">
<?php 
} else {
?>
<?=$errores?>
<br><br>
<input type="button" onclick="window.location.href='inscripcion_club.php'" value="Volver a intentarlo"> <input type="button" onclick="window.location.href='http://www.escuelas-infantiles.com'" value="Salir">
<?php 
}
?>
</font>
</form>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

