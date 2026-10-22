<?php

require("../configuracion/config.php");

$id_contacto=$_GET["id_contacto"];

$bbddContactos = "contactos_web";
if ($id_contacto>100000) {
	$bbddContactos = "contactos_web_sp";
}

$centro_inscripcion = $centro_actual;

$forma_de_pago="";
$entidad="";
$oficina="";
$dc="";
$cuenta="";
$datosAdicionalesLink="";

// Comprueba si ya está inscrito
$datos_contacto=db_query("SELECT * FROM $bbddContactos WHERE id_contacto=$id_contacto") or die(db_error());

if (db_num_rows($datos_contacto)!=0) {
	
	$datos_contactos=db_fetch_array($datos_contacto);
	
	if ($datos_contactos['id_usuario_alumno']==0) {

		// Comprobamos si es nuevo
		if ($id_usuario_seleccionado==0) {
				$consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('1','".strftime("%Y/%m/%d")."','0')";
				db_query($consulta_insert) or die(db_error());
				$id_usuario_seleccionado=db_insert_id();
				}
				
		// Relacionar
		$fecha_c=date("Y-m-d");
		db_query("UPDATE $bbddContactos SET id_usuario_alumno='$id_usuario_seleccionado', fecha_c='$fecha_c', proceso_decision='c' WHERE id_contacto=$id_contacto;") or die(db_error());

		$SIP=$datos_contactos["SIP"];
		if (strlen($SIP)==0) {
			$SIP="PENDIENTE";
		}

        // Copia datos de la tabla de contactos
        db_query("UPDATE usuarios SET SIP='".$SIP."',Nombre='".$datos_contactos["Nombre"]."', Apellido1='".$datos_contactos["Apellido1"]."', Apellido2='".$datos_contactos["Apellido2"]."', sexo='".$datos_contactos["sexo"]."', fecha_nacimiento='".$datos_contactos["fecha_nacimiento"]."', nonato='".$datos_contactos["nonato"]."', prematuro='".$datos_contactos["prematuro"]."', prematuro1='".$datos_contactos["prematuro1"]."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-2");

        // Introduce información de contacto
        db_query("UPDATE usuarios SET Direccion='".$datos_contactos["calle"]."', cp='".$datos_contactos["codigo_postal"]."', Poblacion='".$datos_contactos["poblacion"]."', telefono1='".$datos_contactos["telefono_padre1"]."', email='".$datos_contactos["email_padre1"]."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-15");

		// Introducimos alergias
		

		foreach($array_alergias as $key => $alergia) {
		
			db_query("UPDATE usuarios SET $alergia='".$datos_contactos[$alergia]."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-17");	
		
		}

		// Introducimos Datos bancarios

		$forma_de_pago = $datos_contactos['forma_de_pago'];

		if (strlen($datos_contactos['IBAN'])!=0) {

			$banco_titular_aa=$datos_contactos["nombre_c1"]." ".$datos_contactos["apellido1_c1"]." ".$datos_contactos["apellido2_c1"];
			$banco_dni_titular_aa=$datos_contactos["dni_padre1"];

			// Quita espacios en blanco
			$iban = str_replace(" ","",$datos_contactos['IBAN']);

			// Quita ES
			$iban = str_replace("ES","",$iban);

			$banco_iban=substr($iban,0,2);			

			$entidad=substr($iban,2,4);
			$oficina=substr($iban,6,4);
			$dc=substr($iban,10,2);
			$cuenta=substr($iban,12,10);

			$datosAdicionalesLink="&banco_titular_aa=$banco_titular_aa&banco_dni_titular_aa=$banco_dni_titular_aa&forma_de_pago=$forma_de_pago&banco_iban=$banco_iban&entidad=$entidad&oficina=$oficina&dc=$dc&cuenta=$cuenta";

		}


		// Introducimos a los padres

		// padre 1
		$usuario_nombre=$datos_contactos["nombre_c1"];
		$usuario_apellido1=$datos_contactos["apellido1_c1"];
		$usuario_apellido2=$datos_contactos["apellido2_c1"];
		$usuario_direccion=$datos_contactos["calle"];
		$usuario_DNI=$datos_contactos["dni_padre1"];
		$usuario_cp=$datos_contactos["codigo_postal"];
		$usuario_poblacion=$datos_contactos["poblacion"];
		$usuario_telefono1=$datos_contactos["telefono_padre1"];
		$usuario_telefono2=$datos_contactos["telefono_t1"];
		$usuario_email=$datos_contactos["email_padre1"];
		$usuario_parentestco=$datos_contactos["parentesco_padre1"];

		if (strlen($usuario_nombre)!=0) {
			
			// Insertamos padre
			$consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('2','".strftime("%Y/%m/%d")."','1')";
			db_query($consulta_insert) or die(db_error());
			$id_nuevo_padre=db_insert_id();

			// Introduce datos personales
			db_query("UPDATE usuarios SET Nombre='".$usuario_nombre."', Apellido1='".$usuario_apellido1."', Apellido2='".$usuario_apellido2."' WHERE id_usuario=".$id_nuevo_padre) or die(db_error()."-2");

			// Introduce información de contacto
			db_query("UPDATE usuarios SET DNI='".$usuario_DNI."', Direccion='".$usuario_direccion."', cp='".$usuario_cp."', Poblacion='".$usuario_poblacion."', telefono1='".$usuario_telefono1."', telefono2='".$usuario_telefono2."', email='".$usuario_email."' WHERE id_usuario=".$id_nuevo_padre) or die(db_error()."-15");			

			// Crea contraseña y asigna como padre del alumno

			if (strlen($usuario_apellido2)!=0) {
				$parte_ultima=substr($usuario_apellido2,0,2);
				} else {
				$parte_ultima="";
				}
			$nombre_usu=substr($usuario_nombre,0,2).substr($usuario_apellido1,0,2).$parte_ultima;

			mt_srand((double)microtime()*1000000);
			$nombre_usu2="";
			for ($l=1; $l<=strlen($nombre_usu); $l=$l+1)
			{

			$letra=substr($nombre_usu,$l-1,1);
			$cod_char=ord($letra);
			if ($cod_char<48)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>57 && $cod_char<65)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>90 && $cod_char<97)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>122)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>=97 && $cod_char<=122)
			{
			  $cod_char=$cod_char-32;
			}
			$letra=chr($cod_char);
			$nombre_usu2=$nombre_usu2.$letra;

			}

			$nombre_usu=$nombre_usu2;

			$cuenta=0;
			$no_existe=false;
			while($no_existe==false)
			{
			if ($cuenta==0)
			{
			  $nombre_usu1=$nombre_usu;
			}
			  else
			{
			  $nombre_usu1=$nombre_usu.$cuenta;
			}

			$consulta_nombre_usu=db_query("SELECT * FROM padres WHERE usuario_webcam='" . $nombre_usu1 . "'");

			if (db_num_rows($consulta_nombre_usu)==0)
			{
			  $no_existe=true;
			}
			db_free_result($consulta_nombre_usu);
			$cuenta=$cuenta+1;
			}
			$nombre_usu=$nombre_usu1;

			//Genera Password

			$password="";
			for ($l=1; $l<=3; $l=$l+1)
			{
			mt_srand((double)microtime()*1000000);
			$letra=97+intval((mt_rand(0,10000000)/10000000)*26);
			$password=$password.chr($letra);

			}

			for ($n=1; $n<=3; $n=$n+1)
			{
			mt_srand((double)microtime()*1000000);
			$letra=48+intval((mt_rand(0,10000000)/10000000)*10);
			$password=$password.chr($letra);

			}

			// Introducimos la relacion nueva
			$password_hash=app_password_hash_if_needed($password);
			db_query_params(
				"INSERT INTO padres (id_usuario, padre_de, parentesco, usuario_webcam, password_webcam) VALUES (?, ?, ?, ?, ?)",
				"iisss",
				array((int) $id_nuevo_padre, (int) $id_usuario_seleccionado, $usuario_parentestco, $nombre_usu, $password_hash)
			);
		}
		
		// padre 2
		$usuario_nombre=$datos_contactos["nombre_c2"];
		$usuario_apellido1=$datos_contactos["apellido1_c2"];
		$usuario_apellido2=$datos_contactos["apellido2_c2"];
		$usuario_direccion=$datos_contactos["calle"];
		$usuario_DNI=$datos_contactos["dni_padre2"];
		$usuario_cp=$datos_contactos["codigo_postal"];
		$usuario_poblacion=$datos_contactos["poblacion"];
		$usuario_telefono1=$datos_contactos["telefono_padre2"];
		$usuario_telefono2=$datos_contactos["telefono_t2"];
		$usuario_email=$datos_contactos["email_padre2"];
		$usuario_parentestco=$datos_contactos["parentesco_padre2"];

		if (strlen($usuario_nombre)!=0) {

			// Insertamos padre
			$consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('2','".strftime("%Y/%m/%d")."','1')";
			db_query($consulta_insert) or die(db_error());
			$id_nuevo_padre=db_insert_id();

			// Introduce datos personales
			db_query("UPDATE usuarios SET Nombre='".$usuario_nombre."', Apellido1='".$usuario_apellido1."', Apellido2='".$usuario_apellido2."' WHERE id_usuario=".$id_nuevo_padre) or die(db_error()."-2");

			// Introduce información de contacto
			db_query("UPDATE usuarios SET DNI='".$usuario_DNI."', Direccion='".$usuario_direccion."', cp='".$usuario_cp."', Poblacion='".$usuario_poblacion."', telefono1='".$usuario_telefono1."', telefono2='".$usuario_telefono2."', email='".$usuario_email."' WHERE id_usuario=".$id_nuevo_padre) or die(db_error()."-15");		

			// Crea contraseña y asigna como padre del alumno

			if (strlen($usuario_apellido2)!=0) {
				$parte_ultima=substr($usuario_apellido2,0,2);
				} else {
				$parte_ultima="";
				}
			$nombre_usu=substr($usuario_nombre,0,2).substr($usuario_apellido1,0,2).$parte_ultima;

			mt_srand((double)microtime()*1000000);
			$nombre_usu2="";
			for ($l=1; $l<=strlen($nombre_usu); $l=$l+1)
			{

			$letra=substr($nombre_usu,$l-1,1);
			$cod_char=ord($letra);
			if ($cod_char<48)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>57 && $cod_char<65)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>90 && $cod_char<97)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>122)
			{
			  $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
			}
			if ($cod_char>=97 && $cod_char<=122)
			{
			  $cod_char=$cod_char-32;
			}
			$letra=chr($cod_char);
			$nombre_usu2=$nombre_usu2.$letra;

			}

			$nombre_usu=$nombre_usu2;

			$cuenta=0;
			$no_existe=false;
			while($no_existe==false)
			{
			if ($cuenta==0)
			{
			  $nombre_usu1=$nombre_usu;
			}
			  else
			{
			  $nombre_usu1=$nombre_usu.$cuenta;
			}

			$consulta_nombre_usu=db_query("SELECT * FROM padres WHERE usuario_webcam='" . $nombre_usu1 . "'");

			if (db_num_rows($consulta_nombre_usu)==0)
			{
			  $no_existe=true;
			}
			db_free_result($consulta_nombre_usu);
			$cuenta=$cuenta+1;
			}
			$nombre_usu=$nombre_usu1;

			//Genera Password

			$password="";
			for ($l=1; $l<=3; $l=$l+1)
			{
			mt_srand((double)microtime()*1000000);
			$letra=97+intval((mt_rand(0,10000000)/10000000)*26);
			$password=$password.chr($letra);

			}

			for ($n=1; $n<=3; $n=$n+1)
			{
			mt_srand((double)microtime()*1000000);
			$letra=48+intval((mt_rand(0,10000000)/10000000)*10);
			$password=$password.chr($letra);

			}

			// Introducimos la relacion nueva
			$password_hash=app_password_hash_if_needed($password);
			db_query_params(
				"INSERT INTO padres (id_usuario, padre_de, parentesco, usuario_webcam, password_webcam) VALUES (?, ?, ?, ?, ?)",
				"iisss",
				array((int) $id_nuevo_padre, (int) $id_usuario_seleccionado, $usuario_parentestco, $nombre_usu, $password_hash)
			);

		}
		
	} else {
		
		$id_usuario_seleccionado=$datos_contactos["id_usuario_alumno"];
		
	}
	
// Por ultimo vamos al paso de inscripción pero con el curso PRESELECCIONADO

$id_centro=$datos_contactos["id_centro"];
$id_curso=$datos_contactos["id_curso"];

header("Location: /alumnos/nuevo_alumno4.php?u=$id_usuario_seleccionado&id_centro_pre=$id_centro&id_curso_pre=$id_curso".$datosAdicionalesLink);

} else {
	
	exit("Error: El contacto no existe.");
	
}

?>
