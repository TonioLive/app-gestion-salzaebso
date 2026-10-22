<?php
require("../configuracion/config.php"); 

$id_centro=$_POST["id_centro"];
$id_curso=$_POST["id_curso_contacto"];
$id_contacto=$_POST["id_contacto"];

$id_usuario_alumno=$_POST["id_usuario_alumno"];
$id_usuario_padre1=$_POST["id_usuario_padre1"];
$id_usuario_padre2=$_POST["id_usuario_padre2"];

$horario=$_POST["horario"];
$mensualidad=$_POST["mensualidad"];
$comida=$_POST["comida"];
$merienda=$_POST["merienda"];
$horas_extra=$_POST["horas_extra"];
$numero_horas_extra=$_POST["numero_horas_extra"];	


$valores_alergias=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

$Nombre=db_real_escape_string($_POST["nombre"]);
$Apellido1=db_real_escape_string($_POST["apellido1"]);
$Apellido2=db_real_escape_string($_POST["apellido2"]);
$sexo=$_POST["sexo"];
$sip=db_real_escape_string($_POST["sip"]);
$fecha_nacimiento=$_POST["fecha_nacimiento"];

if (strlen($fecha_nacimiento)!=0) {

	$fecha_array=explode("/",$fecha_nacimiento);
	
	$fecha_nacimiento_dia=intval($fecha_array[0]);
	$fecha_nacimiento_mes=intval($fecha_array[1]);
	$fecha_nacimiento_ano=$fecha_array[2];
	if (!checkdate(intval($fecha_nacimiento_mes),intval($fecha_nacimiento_dia),intval($fecha_nacimiento_ano))) {
		$fecha_nacimiento_dia="";
		$fecha_nacimiento_mes="";
		$fecha_nacimiento_ano="";
		}
	if ($fecha_nacimiento_dia!="--" and $fecha_nacimiento_mes!="--" and $fecha_nacimiento_ano!="----") {
		$fecha_nacimiento=$fecha_nacimiento_ano."/".$fecha_nacimiento_mes."/".$fecha_nacimiento_dia;
		if (!checkdate(intval($fecha_nacimiento_mes),intval($fecha_nacimiento_dia),intval($fecha_nacimiento_ano))) {
			$fecha_nacimiento="";
		}
	} else {
		$fecha_nacimiento="";
	}
                        
} 
$nonato=$_POST["nonato"];
            
$nombre_c1=db_real_escape_string($_POST["nombre_c1"]);
$apellido1_c1=db_real_escape_string($_POST["apellido1_c1"]);
$apellido2_c1=db_real_escape_string($_POST["apellido2_c1"]);
$dni_padre1=db_real_escape_string($_POST["dni_padre1"]);
$telefono_padre1=db_real_escape_string($_POST["telefono_padre1"]);
$telefono_t1=db_real_escape_string($_POST["telefono_t1"]);
$email_padre1=db_real_escape_string($_POST["email_padre1"]);
$parentesco_padre1=$_POST["parentesco_padre1"];

$nombre_c2=db_real_escape_string($_POST["nombre_c2"]);
$apellido1_c2=db_real_escape_string($_POST["apellido1_c2"]);
$apellido2_c2=db_real_escape_string($_POST["apellido2_c2"]);
$dni_padre2=db_real_escape_string($_POST["dni_padre2"]);
$telefono_padre2=db_real_escape_string($_POST["telefono_padre2"]);
$telefono_t2=db_real_escape_string($_POST["telefono_t2"]);
$email_padre2=db_real_escape_string($_POST["email_padre2"]);
$parentesco_padre2=$_POST["parentesco_padre2"];

$calle=db_real_escape_string($_POST["calle"]);
$codigo_postal=db_real_escape_string($_POST["codigo_postal"]);
$poblacion=db_real_escape_string($_POST["poblacion"]);

$no_publicidad=$_POST["no_publicidad"];
$comentarios=db_real_escape_string($_POST["comentarios"]);

$consentimiento_publicidad=$_POST['consentimiento_publicidad'];
$consentimiento_web=$_POST['consentimiento_web'];
$consentimiento_blog=$_POST['consentimiento_blog'];
$consentimiento_redes=$_POST['consentimiento_redes'];
$informado_tratamiento_datos=$_POST['informado_tratamiento_datos'];

$forma_de_pago=$_POST['forma_de_pago'];
$IBAN=$_POST['IBAN'];

$queryAlergiasUpdate="";
$queryAlergiasInsert1="";
$queryAlergiasInsert2="";

print_r($POST);

foreach($array_alergias as $key => $alergia) {
		
	$valores_alergias[$alergia]=$_POST[$alergia];
	$queryAlergiasUpdate.=$alergia."='".$_POST[$alergia]."',";	
	$queryAlergiasInsert1.=$alergia.",";
	$queryAlergiasInsert2.="'".$_POST[$alergia]."',";
}

if (strlen($id_contacto)!=0) {

// Modifica contacto
	db_query("UPDATE contactos_web SET 
	id_centro='$id_centro',
	id_curso='$id_curso',
	horario='$horario',
	mensualidad='$mensualidad',
	comida='$comida',
	merienda='$merienda',
	horas_extra='$horas_extra',
	numero_horas_extra='$numero_horas_extra',
	id_usuario_alumno='$id_usuario_alumno',	
	Nombre='$Nombre',
	Apellido1='$Apellido1',
	Apellido2='$Apellido2',
	sexo='$sexo',
	fecha_nacimiento='$fecha_nacimiento',
	SIP='$sip',
	id_usuario_padre1='$id_usuario_padre1',
	nombre_c1='$nombre_c1',
	apellido1_c1='$apellido1_c1',
	apellido2_c1='$apellido2_c1',
	dni_padre1='$dni_padre1',
	telefono_padre1='$telefono_padre1',
	telefono_t1='$telefono_t1',
	email_padre1='$email_padre1',
	parentesco_padre1='$parentesco_padre1',
	id_usuario_padre2='$id_usuario_padre2',
	nombre_c2='$nombre_c2',
	apellido1_c2='$apellido1_c2',
	apellido2_c2='$apellido2_c2',
	dni_padre2='$dni_padre2',
	telefono_padre2='$telefono_padre2',
	telefono_t2='$telefono_t2',
	email_padre2='$email_padre2',
	parentesco_padre2='$parentesco_padre2',
	calle='$calle',
	codigo_postal='$codigo_postal',
	poblacion='$poblacion',
	no_publicidad='$no_publicidad',
	comentarios='$comentarios',
	consentimiento_publicidad='$consentimiento_publicidad',
	consentimiento_web='$consentimiento_web',
	consentimiento_blog='$consentimiento_blog',
	consentimiento_redes='$consentimiento_redes',
	informado_tratamiento_datos='$informado_tratamiento_datos,
	$queryAlergiasUpdate
	forma_de_pago='$forma_de_pago',
	IBAN='$IBAN'	
	WHERE id_contacto=$id_contacto") or die(db_error());	


} else {
	// Introduce nuevo contacto
	db_query("INSERT INTO contactos_web (
	id_centro,
	id_curso,
	horario,
	mensualidad,
	comida,
	merienda,
	horas_extra,
	numero_horas_extra,
	id_usuario_alumno,
	Nombre,
	Apellido1,
	Apellido2,
	sexo,
	fecha_nacimiento,
	SIP,
	id_usuario_padre1,
	nombre_c1,
	apellido1_c1,
	apellido2_c1,
	dni_padre1,
	telefono_padre1,
	telefono_t1,
	email_padre1,
	parentesco_padre1,
	id_usuario_padre2,
	nombre_c2,
	apellido1_c2,
	apellido2_c2,
	dni_padre2,
	telefono_padre2,
	telefono_t2,
	email_padre2,
	parentesco_padre2,
	calle,
	codigo_postal,
	poblacion,
	no_publicidad,
	comentarios,
	fecha_contacto,	
	consentimiento_publicidad,
	consentimiento_web,
	consentimiento_blog,
	consentimiento_redes,
	informado_tratamiento_datos,
	$queryAlergiasInsert1
	IBAN,
	forma_de_pago	
	) VALUES (
	'$id_centro',
	'$id_curso',
	'$horario',
	'$mensualidad',
	'$comida',
	'$merienda',
	'$horas_extra',
	'$numero_horas_extra',
	'$id_usuario_alumno',	
	'$Nombre',
	'$Apellido1',
	'$Apellido2',
	'$sexo',
	'$fecha_nacimiento',
	'$sip',
	'$id_usuario_padre1',
	'$nombre_c1',
	'$apellido1_c1',
	'$apellido2_c1',
	'$dni_padre1',
	'$telefono_padre1',
	'$telefono_t1',
	'$email_padre1',
	'$parentesco_padre1',
	'$id_usuario_padre2',
	'$nombre_c2',
	'$apellido1_c2',
	'$apellido2_c2',
	'$dni_padre2',
	'$telefono_padre2',
	'$telefono_t2',
	'$email_padre2',
	'$parentesco_padre2',
	'$calle',
	'$codigo_postal',
	'$poblacion',
	'$no_publicidad',
	'$comentarios',
	'".date("Y-m-d H:i:s")."',
	'$consentimiento_publicidad',
	'$consentimiento_web',
	'$consentimiento_blog',
	'$consentimiento_redes',
	'$informado_tratamiento_datos',
	$queryAlergiasInsert2
	'$IBAN',
	'$forma_de_pago'	
	);") or die(db_error());	

}
                       
        

header("Location: /formulario/exito.php");
?>