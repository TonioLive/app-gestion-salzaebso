<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");

$nombre_usuario=$_SESSION['nombre_padre'];

// Busca usuario
$busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'");

$datos_usuario=db_fetch_array($busca_usuario);

$id_usuario=$datos_usuario["id_usuario"];

$query_update="";

$email_padre=$_POST["email"];
if (strlen($email_padre)!=0) {
        $email_padre=str_replace("'","",$email_padre);
        $query_update=$query_update."email='".$email_padre."',";
        }
$dni_padre=$_POST["dni"];
if (strlen($dni_padre)!=0) {
        $dni_padre=str_replace("'","",$dni_padre);
        $query_update=$query_update."DNI='".$dni_padre."',";
        }
$telefono_padre=$_POST["telefono"];
if (strlen($telefono_padre)!=0) {
        $telefono_padre=str_replace("'","",$telefono_padre);
        $query_update=$query_update."telefono1='".$telefono_padre."',";
        }
$domicilio_padre=$_POST["domicilio"];
if (strlen($domicilio_padre)!=0) {
        $domicilio_padre=str_replace("'","",$domicilio_padre);
        $query_update=$query_update."Direccion='".$domicilio_padre."',";
        }
$codigo_postal_padre=$_POST["codigo_postal"];
if (strlen($codigo_postal_padre)!=0) {
        $codigo_postal_padre=str_replace("'","",$codigo_postal_padre);
        $query_update=$query_update."cp='".$codigo_postal_padre."',";
        }

if ($query_update!="") {
        $query_update=substr($query_update,0,(strlen($query_update)-1));
        // Introduce los datos correctos en el servidor
        $intro_datos=db_query("UPDATE usuarios SET $query_update WHERE id_usuario=$id_usuario") or die(db_error());
        
        }
header("Location: principal.php");
?>
