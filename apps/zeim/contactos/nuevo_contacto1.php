<?php  require("../configuracion/config.php"); ?>
<?php 
$id_contacto=$_POST["id_contacto"];

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

$nombre_alu=$_POST["nombre"];
$apellido1_alu=$_POST["apellido1"];
$apellido2_alu=$_POST["apellido2"];
$persona_contacto=$_POST["persona_contacto"];
$proceso_decision=$_POST["proceso_decision"];
$comentarios=$_POST["comentarios"];
$telefono=$_POST["telefono"];
$email=$_POST["email"];
$calle=$_POST["calle"];
$poblacion=$_POST["poblacion"];
$codigo_postal=$_POST["codigo_postal"];
$sexo=$_POST["sexo"];
$id_curso_contacto=$_POST["id_curso_contacto"];
$id_centro_contacto=$_POST["id_centro_contacto"];
$como_conocio=$_POST["como_conocio"];
$coordenadas=$_POST["coordenadas"];
$motivo_cancelacion=$_POST["motivo_cancelacion"];
$sip=$_POST["sip"];

$nuevo_alumno=$_POST["nuevo_alumno"];
$id_usuario=$_POST["id_usuario"];

$nonato=$_POST["nonato"];
$prematuro=$_POST["prematuro"];
$prematuro1=$_POST["prematuro1"];

$fecha_nacimiento_dia=$_POST["fecha_nacimiento_dia"];
$fecha_nacimiento_mes=$_POST["fecha_nacimiento_mes"];
$fecha_nacimiento_ano=$_POST["fecha_nacimiento_ano"];
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
                        
$fecha_informacion_dia=$_POST["fecha_informacion_dia"];
$fecha_informacion_mes=$_POST["fecha_informacion_mes"];
$fecha_informacion_ano=$_POST["fecha_informacion_ano"];
if (!checkdate(intval($fecha_informacion_mes),intval($fecha_informacion_dia),intval($fecha_informacion_ano))) {
                                $fecha_informacion_dia="";
                                $fecha_informacion_mes="";
                                $fecha_informacion_ano="";
                                }
if ($fecha_informacion_dia!="--" and $fecha_informacion_mes!="--" and $fecha_informacion_ano!="----") {
                        $fecha_informacion=$fecha_informacion_ano."/".$fecha_informacion_mes."/".$fecha_informacion_dia;
                        if (!checkdate(intval($fecha_informacion_mes),intval($fecha_informacion_dia),intval($fecha_informacion_ano))) {
                                $fecha_informacion="";
                                }
                        } else {
                        $fecha_informacion="";
                        } 
                        
$fecha_b1_dia=$_POST["fecha_b1_dia"];
$fecha_b1_mes=$_POST["fecha_b1_mes"];
$fecha_b1_ano=$_POST["fecha_b1_ano"];
if (!checkdate(intval($fecha_b1_mes),intval($fecha_b1_dia),intval($fecha_b1_ano))) {
                                $fecha_b1_dia="";
                                $fecha_b1_mes="";
                                $fecha_b1_ano="";
                                }
if ($fecha_b1_dia!="--" and $fecha_b1_mes!="--" and $fecha_b1_ano!="----") {
                        $fecha_b1=$fecha_b1_ano."/".$fecha_b1_mes."/".$fecha_b1_dia;
                        if (!checkdate(intval($fecha_b1_mes),intval($fecha_b1_dia),intval($fecha_b1_ano))) {
                                $fecha_b1="";
                                }
                        } else {
                        $fecha_b1="";
                        }
$fecha_b2_dia=$_POST["fecha_b2_dia"];
$fecha_b2_mes=$_POST["fecha_b2_mes"];
$fecha_b2_ano=$_POST["fecha_b2_ano"];
if (!checkdate(intval($fecha_b2_mes),intval($fecha_b2_dia),intval($fecha_b2_ano))) {
                                $fecha_b2_dia="";
                                $fecha_b2_mes="";
                                $fecha_b2_ano="";
                                }
if ($fecha_b2_dia!="--" and $fecha_b2_mes!="--" and $fecha_b2_ano!="----") {
                        $fecha_b2=$fecha_b2_ano."/".$fecha_b2_mes."/".$fecha_b2_dia;
                        if (!checkdate(intval($fecha_b2_mes),intval($fecha_b2_dia),intval($fecha_b2_ano))) {
                                $fecha_b2="";
                                }
                        } else {
                        $fecha_b2="";
                        }
$fecha_b3_dia=$_POST["fecha_b3_dia"];
$fecha_b3_mes=$_POST["fecha_b3_mes"];
$fecha_b3_ano=$_POST["fecha_b3_ano"];
if (!checkdate(intval($fecha_b3_mes),intval($fecha_b3_dia),intval($fecha_b3_ano))) {
                                $fecha_b3_dia="";
                                $fecha_b3_mes="";
                                $fecha_b3_ano="";
                                }
if ($fecha_b3_dia!="--" and $fecha_b3_mes!="--" and $fecha_b3_ano!="----") {
                        $fecha_b3=$fecha_b3_ano."/".$fecha_b3_mes."/".$fecha_b3_dia;
                        if (!checkdate(intval($fecha_b3_mes),intval($fecha_b3_dia),intval($fecha_b3_ano))) {
                                $fecha_b3="";
                                }
                        } else {
                        $fecha_b3="";
                        }
$fecha_c_dia=$_POST["fecha_c_dia"];
$fecha_c_mes=$_POST["fecha_c_mes"];
$fecha_c_ano=$_POST["fecha_c_ano"];
if (!checkdate(intval($fecha_c_mes),intval($fecha_c_dia),intval($fecha_c_ano))) {
                                $fecha_c_dia="";
                                $fecha_c_mes="";
                                $fecha_c_ano="";
                                }
if ($fecha_c_dia!="--" and $fecha_c_mes!="--" and $fecha_c_ano!="----") {
                        $fecha_c=$fecha_c_ano."/".$fecha_c_mes."/".$fecha_c_dia;
                        if (!checkdate(intval($fecha_c_mes),intval($fecha_c_dia),intval($fecha_c_ano))) {
                                $fecha_c="";
                                }
                        } else {
                        $fecha_c="";
                        }

$id_quitar_nota=$_POST["id_quitar_nota"];
                // Borra la nota
                if (strlen($id_quitar_nota)!=0) {
                        db_query_params('DELETE FROM contactos_seguimiento WHERE id_nota=?', 's', array($id_quitar_nota));
                        }
                        
if (strlen($id_contacto)!=0) {
        // Guarda cambios
        db_query_params('UPDATE contactos SET calle=?, poblacion=?, Nombre=?, Apellido1=?, Apellido2=?, persona_contacto=?, proceso_decision=?, telefono=?, email=?, comentarios=? WHERE id_contacto=?;', 'sssssssssss', array((string)($calle), (string)($poblacion), (string)($nombre_alu), (string)($apellido1_alu), (string)($apellido2_alu), (string)($persona_contacto), (string)($proceso_decision), (string)($telefono), (string)($email), (string)($comentarios), $id_contacto)) or die(db_error());
        db_query_params('UPDATE contactos SET SIP=?, motivo_cancelacion=?, como_conocio=?,nonato=?,prematuro=?,prematuro1=?,id_usuario=?, nuevo_alumno=?, id_centro=?,id_curso=?,fecha_nacimiento=?, sexo=?, codigo_postal=?, fecha_informacion=?, fecha_b1=?, fecha_b2=?, fecha_b3=?, fecha_c=? WHERE id_contacto=?;', 'sssssssssssssssssss', array((string)($sip), (string)($motivo_cancelacion), (string)($como_conocio), (string)($nonato), (string)($prematuro), (string)($prematuro1), (string)($id_usuario), (string)($nuevo_alumno), $id_centro_contacto, $id_curso_contacto, (string)($fecha_nacimiento), (string)($sexo), (string)($codigo_postal), (string)($fecha_informacion), (string)($fecha_b1), (string)($fecha_b2), (string)($fecha_b3), (string)($fecha_c), $id_contacto)) or die(db_error());
        } else {
        // Introduce nuevo centro
        db_query_params('INSERT INTO contactos (calle,poblacion, Nombre, Apellido1, Apellido2, persona_contacto, proceso_decision, telefono, email, fecha_contacto, comentarios, coordenadas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);', 'ssssssssssss', array((string)($calle), (string)($poblacion), (string)($nombre_alu), (string)($apellido1_alu), (string)($apellido2_alu), (string)($persona_contacto), (string)($proceso_decision), (string)($telefono), (string)($email), (string)(date("Y-m-d")), (string)($comentarios), (string)($coordenadas))) or die(db_error());
        $id_contacto=db_insert_id();
        db_query_params('UPDATE contactos SET SIP=?,motivo_cancelacion=?, como_conocio=?,nonato=?,prematuro=?,prematuro1=?,id_usuario=?, nuevo_alumno=?, id_centro=?,id_curso=?,fecha_nacimiento=?, sexo=?, codigo_postal=?, fecha_informacion=?, fecha_b1=?, fecha_b2=?, fecha_b3=?, fecha_c=? WHERE id_contacto=?;', 'sssssssssssssssssss', array((string)($sip), (string)($motivo_cancelacion), (string)($como_conocio), (string)($nonato), (string)($prematuro), (string)($prematuro1), (string)($id_usuario), (string)($nuevo_alumno), $id_centro_contacto, $id_curso_contacto, (string)($fecha_nacimiento), (string)($sexo), (string)($codigo_postal), (string)($fecha_informacion), (string)($fecha_b1), (string)($fecha_b2), (string)($fecha_b3), (string)($fecha_c), $id_contacto)) or die(db_error());
        }
}
if ($_POST["retorno"]==1) {
        $url_retorno="nuevo_contacto.php?id_contacto=$id_contacto";
        } else {
        $url_retorno="listado_contactos.php";
        }
header("Location: ".$url_retorno);
?>
