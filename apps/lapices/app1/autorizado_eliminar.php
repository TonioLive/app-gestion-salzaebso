<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");

$id_autorizado=$_GET["id_autorizado"];
$id_hijo=$_GET["id_hijo"];

$busca_datos_padre=db_query("SELECT * FROM padres WHERE id_padre=$id_autorizado AND padre_de=$id_hijo") or die(db_error()); 

if (db_num_rows($busca_datos_padre)!=0) {
	
	$datos_padre=db_fetch_array($busca_datos_padre);
	
	$id_usu_padre=$datos_padre["id_usuario"];
	
}


$busca_datos_padre1=db_query("SELECT * FROM padres WHERE id_padre=$id_autorizado AND padre_de=$id_hijo AND parentesco=1") or die(db_error());

	
if ((db_num_rows($busca_datos_padre)==0) or ($id_autorizado==$id_usuario) or (db_num_rows($busca_datos_padre1)!=0)){
	
		exit("Se ha detectado una actividad sospechosa. No tienes permiso para acceder a los datos que solicitas.");	
		
}

// Borramos
$b1="DELETE FROM padres WHERE id_padre=$id_autorizado AND padre_de=$id_hijo";
$b2="DELETE FROM usuarios WHERE id_usuario=$id_usu_padre";
$borra_relacion=db_query($b1);
$borra_usuario=db_query($b2);

$nombre_archivo="dni_usu".$id_usu_padre.".jpg";

$nombre_archivo_old="dni_usu".$id_usu_padre."_copia_".date("YmdHis").".jpg";		                                          
                                                              

if (file_exists($upload_path_dni."//$nombre_archivo")) {
    $cambia_nombre=rename($upload_path_dni."//$nombre_archivo", $upload_path_dni."//$nombre_archivo_old");
    $cambia_nombre=unlink($upload_path_dni."//$nombre_archivo");                                                                 
}	

/* 
echo("$b1<br>");
echo("$b2<br>");
*/

header("Location: autorizados.php?id_usuario=$id_usuario&id_usuhijo=$id_hijo");

?>