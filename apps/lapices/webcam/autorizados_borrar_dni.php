<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");

$id_autorizado=$_GET["id_autorizado"];
$id_hijo=$_GET["id_hijo"];

$busca_datos_padre=db_query("SELECT * FROM padres WHERE id_usuario=$id_autorizado AND padre_de=$id_hijo") or die(db_error()); 
	
if (db_num_rows($busca_datos_padre)==0) {
	
		exit("Se ha detectado una actividad sospechosa. No tienes permiso para acceder a los datos que solicitas.");	
		
}

$nombre_archivo="dni_usu".$id_autorizado.".jpg";

$nombre_archivo_old="dni_usu".$id_autorizado."_copia_".date("YmdHis").".jpg";		                                          
                                                              

if (file_exists($upload_path_dni."//$nombre_archivo")) {
    $cambia_nombre=rename($upload_path_dni."//$nombre_archivo", $upload_path_dni."//$nombre_archivo_old");
    $cambia_nombre=unlink($upload_path_dni."//$nombre_archivo");                                                                 
}													


header("Location: autorizados.php?id_usuario=$id_usuario&id_usuhijo=$id_hijo");

?>