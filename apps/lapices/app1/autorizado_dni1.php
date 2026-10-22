<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");

$id_autorizado=$_GET["id_autorizado"];
$id_hijo=$_GET["id_hijo"];

$busca_datos_padre=db_query("SELECT * FROM padres WHERE id_usuario=$id_autorizado AND padre_de=$id_hijo") or die(db_error()); 
	
if (db_num_rows($busca_datos_padre)==0) {
	
		exit("Se ha detectado una actividad sospechosa. No tienes permiso para acceder a los datos que solicitas.");	
		
}

// Guardamos la foto
if (is_uploaded_file($_FILES['File1']['tmp_name'])) {

        $nombre_archivo=$_FILES['File1']['name'];
        $nombre_temp=$_FILES['File1']['tmp_name'];


		if ((strtoupper(substr($nombre_archivo,strlen($nombre_archivo)-3,3))=="JPG") or (strtoupper(substr($nombre_archivo,strlen($nombre_archivo)-4,4))=="JPEG")) {

        $nombre_archivo="dni_usu".$id_autorizado.".jpg";
        
		$nombre_archivo_old="dni_usu".$id_autorizado."_copia_".date("YmdHis").".jpg";		                                          
                                                                    
		$maxDim = 800;
        list($width, $height, $type, $attr) = getimagesize( $nombre_temp );
        if ( $width > $maxDim || $height > $maxDim ) {
            $target_filename = $nombre_temp;
            $fn = $nombre_temp;
            $size = getimagesize( $fn );
            $ratio = $size[0]/$size[1]; // width/height
            if( $ratio > 1) {
                $width = $maxDim;
                $height = $maxDim/$ratio;
            } else {
                $width = $maxDim*$ratio;
                $height = $maxDim;
            }
            $src = imagecreatefromstring( file_get_contents( $fn ) );
            $dst = imagecreatetruecolor( $width, $height );
            imagecopyresampled( $dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );
            imagedestroy( $src );
            imagejpeg( $dst, $target_filename ); // adjust format as needed
            imagedestroy( $dst );
        }  
        
        if (!file_exists($upload_path_dni."//$nombre_archivo")) {
            $cambia_nombre=rename($nombre_temp, $upload_path_dni."//$nombre_archivo");
            } else {
            $cambia_nombre=rename($upload_path_dni."//$nombre_archivo", $upload_path_dni."//$nombre_archivo_old");
            $cambia_nombre=rename($nombre_temp, $upload_path_dni."//$nombre_archivo");
            }              
                                                            
		}
														
} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File1']['name'];
}



header("Location: autorizados.php?id_usuario=$id_usuario&id_usuhijo=$id_hijo");

?>