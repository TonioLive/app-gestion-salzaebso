<?php  require("../configuracion/config.php"); ?>
<?php 
$id_contacto=$_GET["id_contacto"];
$id_usuario_seleccionado=$_GET["id_alumno"];

// Comprobamos si es nuevo
if ($id_usuario_seleccionado!=0) {
                        
// Relacionar
$fecha_c=date("Y-m-d");
db_query_params('UPDATE contactos SET id_usuario=?, fecha_c=?, proceso_decision=\'c\' WHERE id_contacto=?;', 'sss', array((string)($id_usuario_seleccionado), (string)($fecha_c), $id_contacto)) or die(db_error());

// Datos de la tabla CONTACTOS
$consulta_datos_contactos=db_query_params('SELECT * FROM contactos WHERE id_contacto=?;', 's', array($id_contacto)) or die(db_error());
$datos_contactos=db_fetch_array($consulta_datos_contactos);

// Copia datos de la tabla de contactos
db_query_params('UPDATE usuarios SET Nombre=?, Apellido1=?, Apellido2=?, sexo=?, fecha_nacimiento=?, observaciones=? WHERE id_usuario=?', 'sssssss', array((string)($datos_contactos["Nombre"]), (string)($datos_contactos["Apellido1"]), (string)($datos_contactos["Apellido2"]), (string)($datos_contactos["sexo"]), (string)($datos_contactos["fecha_nacimiento"]), (string)($datos_contactos["comentarios"]), $id_usuario_seleccionado)) or die(db_error()."-2");

// Introduce informaci車n de contacto
        
$emails_contacto=array();
        
if (strlen($datos_contactos["email_familia"])!=0) {
	$emails_contacto[]=$datos_contactos["email_familia"];	                	
}
        
if (strlen($datos_contactos["email"])!=0) {
	$emails_contacto[]=$datos_contactos["email"];		                	
}
        
if (strlen($datos_contactos["email_padre2"])!=0) {
	$emails_contacto[]=$datos_contactos["email_padre2"];       	
}
        
$email_contacto=implode(",",$emails_contacto);

$telefonos_contacto=array();
        
if (strlen($datos_contactos["telefonos_familia"])!=0) {
	$telefonos_contacto[]=$datos_contactos["telefonos_familia"];	                	
}
        
if (strlen($datos_contactos["telefono"])!=0) {
	$telefonos_contacto[]=$datos_contactos["telefono"];		                	
}
        
if (strlen($datos_contactos["telefono_padre2"])!=0) {
	$telefonos_contacto[]=$datos_contactos["telefono_padre2"];       	
}
        
$telefono_contacto=implode(",",$telefonos_contacto);        
       
        
$banco_titular=$datos_contactos["persona_contacto"];
$banco_dni_titular=$datos_contactos["dni_padre1"];
        
if (strlen($banco_titular)==0) {
	$banco_titular=$datos_contactos["nombre_padre2"];
	$banco_dni_titular=$datos_contactos["dni_padre2"];     	
}
               
        
db_query_params('UPDATE usuarios SET coordenadas=?,Direccion=?, cp=?, Poblacion=?, telefono1=?, email=?, banco_titular_alu=?, banco_dni_titular_alu=? WHERE id_usuario=?', 'sssssssss', array((string)($datos_contactos["coordenadas"]), (string)($datos_contactos["calle"]), (string)($datos_contactos["codigo_postal"]), (string)($datos_contactos["poblacion"]), (string)($telefono_contacto), (string)($email_contacto), (string)($banco_titular), (string)($banco_dni_titular), $id_usuario_seleccionado)) or die(db_error()."-15");

// Inserta padres
if (strlen($datos_contactos["persona_contacto"])!=0) {
		
			// Contacto 1
			$nombre_contacto1=explode(" ",$datos_contactos["persona_contacto"]);
			$usuario_nombre=$nombre_contacto1[0];
			$usuario_apellido1=$nombre_contacto1[1];
			$usuario_apellido2=$nombre_contacto1[2];
			
			$consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('2','".date("Y-m-d")."','1')";
            db_query($consulta_insert) or die(db_error());
            $id_nuevo_padre=db_insert_id();
			
			// Introduce informaci車n
        	db_query_params('UPDATE usuarios SET Nombre=?, Apellido1=?, Apellido2=? WHERE id_usuario=?', 'ssss', array((string)($usuario_nombre), (string)($usuario_apellido1), (string)($usuario_apellido2), $id_nuevo_padre)) or die(db_error()."-2");

        	// Introduce informaci車n de contacto
        	db_query_params('UPDATE usuarios SET DNI=?, Direccion=?, cp=?, Poblacion=?, telefono1=?, email=? WHERE id_usuario=?', 'sssssss', array((string)($datos_contactos["dni_padre1"]), (string)($datos_contactos["calle"]), (string)($datos_contactos["codigo_postal"]), (string)($datos_contactos["poblacion"]), (string)($datos_contactos["telefono"]), (string)($datos_contactos["email"]), $id_nuevo_padre)) or die(db_error()."-15");
        	
        	// Relaciona con alumno
       
		  	if (strlen($usuario_apellido2)!=0) {
        		$parte_ultima=substr($usuario_apellido2,0,2);
        	} else {
        		$parte_ultima="";
        	}
        	
  			$nombre_usu=substr($usuario_nombre,0,2).substr($usuario_apellido1,0,2).$parte_ultima;

  			mt_srand((float)microtime()*1000000);
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
				if ($cod_char>122){
					$cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
				}
				if ($cod_char>=97 && $cod_char<=122) {
					$cod_char=$cod_char-32;
				}
				$letra=chr($cod_char);
				$nombre_usu2=$nombre_usu2.$letra;

			}

  			$nombre_usu=$nombre_usu2;

  			$cuenta=0;
  			$no_existe=false;
  			while($no_existe==false){
  			
    			if ($cuenta==0) {
					$nombre_usu1=$nombre_usu;
				} else {
					$nombre_usu1=$nombre_usu.$cuenta;
				}
				
				$consulta_nombre_usu=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usu1)));

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
				mt_srand((float)microtime()*1000000);
				$letra=97+intval((mt_rand(0,10000000)/10000000)*26);
				$password=$password.chr($letra);
			}

  			for ($n=1; $n<=3; $n=$n+1) {
				mt_srand((float)microtime()*1000000);
				$letra=48+intval((mt_rand(0,10000000)/10000000)*10);
				$password=$password.chr($letra);
			}
			
        	$parentesco_actual=$contactos["parentesco_padre1"];
        	if ($parentesco_actual==0) {
        		$parentesco_actual=4;
	        }
        	// Introducimos la relacion nueva
          	security_create_family_relation($id_nuevo_padre,$id_usuario_seleccionado,$parentesco_actual,$nombre_usu,$password);
       	        	
}

if (strlen($datos_contactos["nombre_padre2"])!=0) {
		
			// Contacto 1
			$nombre_contacto1=explode(" ",$datos_contactos["nombre_padre2"]);
			$usuario_nombre=$nombre_contacto1[0];
			$usuario_apellido1=$nombre_contacto1[1];
			$usuario_apellido2=$nombre_contacto1[2];
			
			$consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('2','".date("Y-m-d")."','1')";
            db_query($consulta_insert) or die(db_error());
            $id_nuevo_padre=db_insert_id();
			
			// Introduce informaci車n
        	db_query_params('UPDATE usuarios SET Nombre=?, Apellido1=?, Apellido2=? WHERE id_usuario=?', 'ssss', array((string)($usuario_nombre), (string)($usuario_apellido1), (string)($usuario_apellido2), $id_nuevo_padre)) or die(db_error()."-2");

        	// Introduce informaci車n de contacto
        	db_query_params('UPDATE usuarios SET DNI=?, Direccion=?, cp=?, Poblacion=?, telefono1=?, email=? WHERE id_usuario=?', 'sssssss', array((string)($datos_contactos["dni_padre2"]), (string)($datos_contactos["calle"]), (string)($datos_contactos["codigo_postal"]), (string)($datos_contactos["poblacion"]), (string)($datos_contactos["telefono_padre2"]), (string)($datos_contactos["email_padre2"]), $id_nuevo_padre)) or die(db_error()."-15");
        	
        	// Relaciona con alumno
       
		  	if (strlen($usuario_apellido2)!=0) {
        		$parte_ultima=substr($usuario_apellido2,0,2);
        	} else {
        		$parte_ultima="";
        	}
        	
  			$nombre_usu=substr($usuario_nombre,0,2).substr($usuario_apellido1,0,2).$parte_ultima;

  			mt_srand((float)microtime()*1000000);
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
				if ($cod_char>122){
					$cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
				}
				if ($cod_char>=97 && $cod_char<=122) {
					$cod_char=$cod_char-32;
				}
				$letra=chr($cod_char);
				$nombre_usu2=$nombre_usu2.$letra;

			}

  			$nombre_usu=$nombre_usu2;

  			$cuenta=0;
  			$no_existe=false;
  			while($no_existe==false){
  			
    			if ($cuenta==0) {
					$nombre_usu1=$nombre_usu;
				} else {
					$nombre_usu1=$nombre_usu.$cuenta;
				}
				
				$consulta_nombre_usu=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usu1)));

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
				mt_srand((float)microtime()*1000000);
				$letra=97+intval((mt_rand(0,10000000)/10000000)*26);
				$password=$password.chr($letra);
			}

  			for ($n=1; $n<=3; $n=$n+1) {
				mt_srand((float)microtime()*1000000);
				$letra=48+intval((mt_rand(0,10000000)/10000000)*10);
				$password=$password.chr($letra);
			}
			
        	$parentesco_actual=$contactos["parentesco_padre2"];
        	if ($parentesco_actual==0) {
        		$parentesco_actual=4;
	        }
        	// Introducimos la relacion nueva
          	security_create_family_relation($id_nuevo_padre,$id_usuario_seleccionado,$parentesco_actual,$nombre_usu,$password);
       	        	
}

}

header("Location: /alumnos/nuevo_alumno1.php?u=$id_usuario_seleccionado");

?>
