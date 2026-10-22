<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");

$id_autorizado=$_POST["id_autorizado"];
$id_usu_autorizado=$_POST["id_usu_autorizado"];
$id_hijo=$_POST["id_hijo"];

$busca_datos_padre=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario AND padre_de=$id_hijo") or die(db_error()); 
	
if (db_num_rows($busca_datos_padre)==0) {
	
		exit("Se ha detectado una actividad sospechosa. No tienes permiso para gestionar las personas autorizadas.");	
		
}


        
// Recibimos datos del formulario
$usuario_nombre=$_POST["Nombre"];
$usuario_apellido1=$_POST["Apellido1"];
$usuario_apellido2=$_POST["Apellido2"];
$usuario_sexo=$_POST["sexo"];
$usuario_direccion=$_POST["Direccion"];
$usuario_DNI=$_POST["DNI"];
$usuario_profesion=$_POST["profesion"];
$usuario_cp=$_POST["CP"];
$usuario_poblacion=$_POST["poblacion"];
$usuario_provincia=$_POST["provincia"];
$usuario_telefono1=$_POST["telefono1"];
$usuario_telefono2=$_POST["telefono2"];
$usuario_movil=$_POST["movil"];
$usuario_email=$_POST["email"];
$parentesco=$_POST["parentesco"];


// Comprobamos si es nuevo
if ($id_usu_autorizado==0) {
	$consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('2','".date("Y-m-d")."','1')";
    db_query($consulta_insert) or die(db_error());
	$id_usu_autorizado=db_insert_id();
	
	// Genera usuario y contraseña	
	
	$parte_ultima="";
	
	if (strlen($usuario_apellido2)!=0) {
	    $parte_ultima=substr($usuario_apellido2,0,2);
	} 
	        
	$nombre_usu=substr($usuario_nombre,0,2).substr($usuario_apellido1,0,2).$parte_ultima;
	
	mt_srand((double)microtime()*1000000);
	$nombre_usu2="";
	for ($l=1; $l<=strlen($nombre_usu); $l=$l+1) {
	
	    $letra=substr($nombre_usu,$l-1,1);
	    $cod_char=ord($letra);
	    
	    if ($cod_char<48) {
	    	$cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
	    }
	    if ($cod_char>57 && $cod_char<65) {
			$cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
	    }
	    if ($cod_char>90 && $cod_char<97) {
			$cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
	    }
	    if ($cod_char>122) {
			$cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
	    }
	    if ($cod_char>=97 && $cod_char<=122) {
		   	$cod_char=$cod_char-32;
	    }
	    
	    $letra=chr($cod_char);
	    $nombre_usu2=$nombre_usu2.$letra;
	    	
	}
	
	$nombre_usu=$nombre_usu2;
	  
	if (strlen($usuario_apellido2)!=0) {
		$nombre_usu.=rand(10,99);
	}
	
	$cuenta=0;
	$no_existe=false;
	while($no_existe==false) {
		
		if ($cuenta==0) {
	    	$nombre_usu1=$nombre_usu;
	    } else {
	      	$nombre_usu1=$nombre_usu.$cuenta;
	    }
	
	    $consulta_nombre_usu=db_query("SELECT * FROM padres WHERE usuario_webcam='" . $nombre_usu1 . "'");
	
	    if (db_num_rows($consulta_nombre_usu)==0) {
	      $no_existe=true;
	    }
	    
	    db_free_result($consulta_nombre_usu);
	    $cuenta=$cuenta+1;
	    
	}
	
	$nombre_usu=$nombre_usu1;
	
	//Genera Password
	
	$password="";
	
	for ($l=1; $l<=3; $l=$l+1) {
		
	    mt_srand((double)microtime()*1000000);
	    $letra=97+intval((mt_rand(0,10000000)/10000000)*26);
	    $password=$password.chr($letra);
	
	}
	
	for ($n=1; $n<=3; $n=$n+1) {
		
	    mt_srand((double)microtime()*1000000);
	    $letra=48+intval((mt_rand(0,10000000)/10000000)*10);
	    $password=$password.chr($letra);
	
	}	
	
	// Crea relación de parentesco
	
	db_query("INSERT INTO padres (id_usuario, padre_de, parentesco, usuario_webcam, password_webcam) VALUES ('".$id_usu_autorizado."','".$id_hijo."','".$parentesco."','$nombre_usu','$password');");	
	
	$id_autorizado=db_insert_id();
}


if ($num_errores==0) {

	// Introduce información
	
	// Introduce datos personales
	db_query("UPDATE usuarios SET Nombre='".$usuario_nombre."', Apellido1='".$usuario_apellido1."', Apellido2='".$usuario_apellido2."', sexo='".$usuario_sexo."' WHERE id_usuario=".$id_usu_autorizado) or die(db_error()."-2");
	
	// Introduce información de contacto
	db_query("UPDATE usuarios SET DNI='".$usuario_DNI."', profesion='".$usuario_profesion."', Direccion='".$usuario_direccion."', cp='".$usuario_cp."', Poblacion='".$usuario_poblacion."', Provincia='".$usuario_provincia."', telefono1='".$usuario_telefono1."', telefono2='".$usuario_telefono2."', movil='".$usuario_movil."', email='".$usuario_email."' WHERE id_usuario=".$id_usu_autorizado) or die(db_error()."-15");
	
	// Actualiza parentesco
	db_query("UPDATE padres SET parentesco='$parentesco' WHERE id_padre=$id_autorizado");

}



header("Location: autorizados.php?id_usuario=$id_usuario&id_usuhijo=$id_hijo");

?>