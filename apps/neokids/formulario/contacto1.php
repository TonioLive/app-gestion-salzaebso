<?php 

require("../configuracion/config.php"); 

$id_centro=20;
$id_curso=$_POST["id_curso_contacto"];
$como_conocio=$_POST["como_conocio"];

$horarios_quiere_asistir_str=$_POST["horarios_quiere_asistir"];

$id_contacto=$_POST["id_contacto"];

if (strlen($horarios_quiere_asistir_str)==0) {

	$horarios_quiere_asistir_1=$_POST["horarios_quiere_asistir_1"];
	$horarios_quiere_asistir_h1=$_POST["horarios_quiere_asistir_h1"];
	$horarios_quiere_asistir_str=$horarios_quiere_asistir_1;
	if ($horarios_quiere_asistir_h1) {
		$horarios_quiere_asistir_str.=" A partir de las: ".$horarios_quiere_asistir_h1;
	}

	$horarios_quiere_asistir_2=$_POST["horarios_quiere_asistir_2"];
	$horarios_quiere_asistir_h2=$_POST["horarios_quiere_asistir_h2"];

	if ($horarios_quiere_asistir_str!="") {
		$horarios_quiere_asistir_str.=", ";
	}

	$horarios_quiere_asistir_str.=$horarios_quiere_asistir_2;
	if ($horarios_quiere_asistir_h2) {
		$horarios_quiere_asistir_str.=" A partir de las: ".$horarios_quiere_asistir_h2;
	}
}

$dias_quiere_asistir=$_POST["dias_quiere_asistir"];

if (strlen($dias_quiere_asistir)==0) {

	$dias_quiere_asistir="";
	$dias_quiere_asistir1=$_POST["dias_quiere_asistir1"];
	if ($dias_quiere_asistir1) {
		$dias_quiere_asistir.=$dias_quiere_asistir1.", ";
	}
	$dias_quiere_asistir2=$_POST["dias_quiere_asistir2"];
	if ($dias_quiere_asistir2) {
		$dias_quiere_asistir.=$dias_quiere_asistir2.", ";
	}
	$dias_quiere_asistir3=$_POST["dias_quiere_asistir3"];
	if ($dias_quiere_asistir4) {
		$dias_quiere_asistir.=$dias_quiere_asistir3.", ";
	}
	$dias_quiere_asistir4=$_POST["dias_quiere_asistir4"];
	if ($dias_quiere_asistir4) {
		$dias_quiere_asistir.=$dias_quiere_asistir4.", ";
	}
	$dias_quiere_asistir5=$_POST["dias_quiere_asistir5"];
	if ($dias_quiere_asistir5) {
		$dias_quiere_asistir.=$dias_quiere_asistir5.", ";
	}
	$dias_quiere_asistir6=$_POST["dias_quiere_asistir6"];
	if ($dias_quiere_asistir6) {
		$dias_quiere_asistir.=$dias_quiere_asistir6.", ";
	}

	if (strlen($dias_quiere_asistir)!=0) {
		$dias_quiere_asistir=substr($dias_quiere_asistir,0,strlen($dias_quiere_asistir)-2);
		}
}

$Nombre=db_real_escape_string($_POST["nombre"]);
$Apellido1=db_real_escape_string($_POST["apellido1"]);
$Apellido2=db_real_escape_string($_POST["apellido2"]);
$sexo=$_POST["sexo"];
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
$antiguo_alumno_lapices=$_POST["antiguo_alumno_lapices"];

$persona_contacto=db_real_escape_string($_POST["persona_contacto"]);
$dni_padre1=db_real_escape_string($_POST["dni_padre1"]);
$telefono=db_real_escape_string($_POST["telefono"]);
$email=db_real_escape_string($_POST["email"]);

$nombre_padre2=db_real_escape_string($_POST["nombre_padre2"]);
$dni_padre2=db_real_escape_string($_POST["dni_padre2"]);
$telefono_padre2=db_real_escape_string($_POST["telefono_padre2"]);
$email_padre2=db_real_escape_string($_POST["email_padre2"]);

$parentesco_padre1=$_POST["parentesco_padre1"];
$parentesco_padre2=$_POST["parentesco_padre2"];

$calle=db_real_escape_string($_POST["calle"]);
$codigo_postal=db_real_escape_string($_POST["codigo_postal"]);
$poblacion=db_real_escape_string($_POST["poblacion"]);
$telefonos_familia=db_real_escape_string($_POST["telefonos_familia"]);
$email_familia=db_real_escape_string($_POST["email_familia"]);
$contacto_emergencia1=db_real_escape_string($_POST["contacto_emergencia1"]);
$telefono_contacto_emergencia1=db_real_escape_string($_POST["telefono_contacto_emergencia1"]);
$contacto_emergencia2=db_real_escape_string($_POST["contacto_emergencia2"]);
$telefono_contacto_emergencia2=db_real_escape_string($_POST["telefono_contacto_emergencia2"]);
$persona_autorizada=db_real_escape_string($_POST["persona_autorizada"]);

$no_publicidad=$_POST["no_publicidad"];
$comentarios=db_real_escape_string($_POST["comentarios"]);

$semanas_embarazo=$_POST["semanas_embarazo"];

$tipo_alumno=$_POST["tipo_alumno"];

$consentimiento_publicidad=$_POST['consentimiento_publicidad'];
$consentimiento_web=$_POST['consentimiento_web'];
$consentimiento_blog=$_POST['consentimiento_blog'];
$consentimiento_redes=$_POST['consentimiento_redes'];
$informado_tratamiento_datos=$_POST['informado_tratamiento_datos'];

if ($tipo_alumno!=0) {
	$persona_contacto=$Nombre." ".$Apellido1." ".$Apellido2;
	}

if (strlen($id_contacto)!=0) {

// Modifica contacto
db_query("UPDATE contactos SET id_centro='$id_centro',id_curso='$id_curso', como_conocio='$como_conocio', horarios_quiere_asistir='$horarios_quiere_asistir_str', dias_quiere_asistir='$dias_quiere_asistir', Nombre='$Nombre', Apellido1='$Apellido1', Apellido2='$Apellido2', sexo='$sexo', fecha_nacimiento='$fecha_nacimiento', antiguo_alumno_lapices='$antiguo_alumno_lapices', persona_contacto='$persona_contacto', dni_padre1='$dni_padre1', telefono='$telefono', email='$email', nombre_padre2='$nombre_padre2', dni_padre2='$dni_padre2', telefono_padre2='$telefono_padre2', email_padre2='$email_padre2', calle='$calle', codigo_postal='$codigo_postal', poblacion='$poblacion', telefonos_familia='$telefonos_familia', email_familia='$email_familia', contacto_emergencia1='$contacto_emergencia1', telefono_contacto_emergencia1='$telefono_contacto_emergencia1', contacto_emergencia2='$contacto_emergencia2', telefono_contacto_emergencia2='$telefono_contacto_emergencia2', persona_autorizada='$persona_autorizada', no_publicidad='$no_publicidad', comentarios='$comentarios', semanas_embarazo='$semanas_embarazo', tipo_alumno='$tipo_alumno', parentesco_padre1='$parentesco_padre1', parentesco_padre2='$parentesco_padre2', origen=1, consentimiento_publicidad='$consentimiento_publicidad', consentimiento_web='$consentimiento_web', consentimiento_blog='$consentimiento_blog', consentimiento_redes='$consentimiento_redes', informado_tratamiento_datos='$informado_tratamiento_datos' WHERE id_contacto=$id_contacto") or die(db_error());	


} else {
// Introduce nuevo contacto
db_query("INSERT INTO contactos (id_centro,id_curso,como_conocio,horarios_quiere_asistir,dias_quiere_asistir,Nombre,Apellido1,Apellido2,sexo,fecha_nacimiento,antiguo_alumno_lapices,persona_contacto,dni_padre1,telefono,email,nombre_padre2,dni_padre2,telefono_padre2,email_padre2,calle,codigo_postal,poblacion,telefonos_familia,email_familia,contacto_emergencia1,telefono_contacto_emergencia1,contacto_emergencia2,telefono_contacto_emergencia2,persona_autorizada,no_publicidad,comentarios,fecha_contacto,semanas_embarazo,tipo_alumno, parentesco_padre1, parentesco_padre2,origen, consentimiento_publicidad, consentimiento_web, consentimiento_blog, consentimiento_redes, informado_tratamiento_datos) VALUES ('$id_centro','$id_curso','$como_conocio','$horarios_quiere_asistir_str','$dias_quiere_asistir','$Nombre','$Apellido1','$Apellido2','$sexo','$fecha_nacimiento','$antiguo_alumno_lapices','$persona_contacto','$dni_padre1','$telefono','$email','$nombre_padre2','$dni_padre2','$telefono_padre2','$email_padre2','$calle','$codigo_postal','$poblacion','$telefonos_familia','$email_familia','$contacto_emergencia1','$telefono_contacto_emergencia1','$contacto_emergencia2','$telefono_contacto_emergencia2','$persona_autorizada','$no_publicidad','$comentarios','".date("Y-m-d")."','$semanas_embarazo','$tipo_alumno','$parentesco_padre1','$parentesco_padre2',1, '$consentimiento_publicidad', '$consentimiento_web', '$consentimiento_blog', '$consentimiento_redes', '$informado_tratamiento_datos');") or die(db_error());	

}
                       
        

header("Location: /formulario/exito.php");
?>
