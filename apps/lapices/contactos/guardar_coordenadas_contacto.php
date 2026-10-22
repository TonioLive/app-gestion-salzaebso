<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_usuario=$_POST["id_usuario"];
if (strlen($id_usuario)==0) {
        $id_usuario=$_GET["id_usuario"];
        }
$coordenadas=$_POST["coordenadas"];
if (strlen($coordenadas)==0) {
        $coordenadas=$_GET["coordenadas"];
        }
        
// Comprueba si el código del contacto es correcto
if (strlen($id_usuario)!=0) {

        // Comprueba si el contacto ya ha sido borrado
        $actualiza=db_query("UPDATE contactos SET coordenadas='$coordenadas' WHERE id_contacto=".$id_usuario) or die(db_error());
                
        }
        
db_close();
?>
