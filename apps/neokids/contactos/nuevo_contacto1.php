<?php  require("../configuracion/config.php"); ?>
<?php 
require_once __DIR__.'/../configuracion/autentifica.php';
require_once __DIR__.'/../configuracion/autentifica_admin.php';
if(app_value($_SERVER,'REQUEST_METHOD')!=='POST')security_stop('Usa el formulario de contactos para guardar.',405);
app_check_csrf();
$id_contacto=app_value($_POST,'id_contacto');
if($id_contacto!==''&&(!ctype_digit((string)$id_contacto)||strlen((string)$id_contacto)>10))security_stop('Identificador de contacto no valido.',422);
if((string)$id_contacto==='0')$id_contacto='';
if($id_contacto!==''){
    $contactScope=app_rows('SELECT id_centro FROM contactos WHERE id_contacto=?','i',array((int)$id_contacto));
    if(!$contactScope)security_stop('El contacto ya no existe.',404);
    if((int)$contactScope[0]['id_centro']!==0&&!security_owned_centre((int)$contactScope[0]['id_centro']))security_stop('No tienes acceso a este contacto.',403);
}

$formulario_enviado=app_value($_POST,'formulario_enviado');
if ($formulario_enviado=="1") {


				$tipo_alumno=app_value($_POST,'tipo_alumno');
				
				$horarios_quiere_asistir=app_value($_POST,'horarios_quiere_asistir');
				$dias_quiere_asistir=app_value($_POST,'dias_quiere_asistir');
				$antiguo_alumno_lapices=app_value($_POST,'antiguo_alumno_lapices');
				
				$dni_padre1=app_value($_POST,'dni_padre1');
				$nombre_padre2=app_value($_POST,'nombre_padre2');
				$telefono_padre2=app_value($_POST,'telefono_padre2');
				$email_padre2=app_value($_POST,'email_padre2');
				$dni_padre2=app_value($_POST,'dni_padre2');
				
				$parentesco_padre1=app_value($_POST,'parentesco_padre1');
				$parentesco_padre2=app_value($_POST,'parentesco_padre2');				
				
				$telefonos_familia=app_value($_POST,'telefonos_familia');
				$email_familia=app_value($_POST,'email_familia');
				
				$contacto_emergencia1=app_value($_POST,'contacto_emergencia1');
				$telefono_contacto_emergencia1=app_value($_POST,'telefono_contacto_emergencia1');
				$contacto_emergencia2=app_value($_POST,'contacto_emergencia2');
				$telefono_contacto_emergencia2=app_value($_POST,'telefono_contacto_emergencia2');
				
				$persona_autorizada=app_value($_POST,'persona_autorizada');

				$semanas_embarazo=app_value($_POST,'semanas_embarazo');

$nombre_alu=app_value($_POST,'nombre');
$apellido1_alu=app_value($_POST,'apellido1');
$apellido2_alu=app_value($_POST,'apellido2');
$persona_contacto=app_value($_POST,'persona_contacto');
$proceso_decision=app_value($_POST,'proceso_decision');
$comentarios=app_value($_POST,'comentarios');
$telefono=app_value($_POST,'telefono');
$email=app_value($_POST,'email');
$calle=app_value($_POST,'calle');
$poblacion=app_value($_POST,'poblacion');
$codigo_postal=app_value($_POST,'codigo_postal');
$sexo=app_value($_POST,'sexo');
$id_curso_contacto=app_value($_POST,'id_curso_contacto');
$id_centro_contacto=app_value($_POST,'id_centro_contacto');
if(!ctype_digit((string)$id_centro_contacto)||strlen((string)$id_centro_contacto)>10)security_stop('Selecciona un centro valido.',422);
$id_centro_contacto=(int)$id_centro_contacto;
if($id_centro_contacto!==0){
    if(!security_owned_centre($id_centro_contacto))security_stop('No puedes asignar el contacto a otro centro.',403);
    if(!app_rows('SELECT id_centro FROM centros WHERE id_centro=?','i',array($id_centro_contacto)))security_stop('El centro no existe.',422);
}
$como_conocio=app_value($_POST,'como_conocio');
$coordenadas=app_value($_POST,'coordenadas');
$motivo_cancelacion=app_value($_POST,'motivo_cancelacion');

$no_publicidad=app_value($_POST,'no_publicidad');

$nuevo_alumno=app_value($_POST,'nuevo_alumno');
$id_usuario=app_value($_POST,'id_usuario');

$nonato=app_value($_POST,'nonato');
$prematuro=app_value($_POST,'prematuro');
$prematuro1=app_value($_POST,'prematuro1');

$fecha_nacimiento_dia=app_value($_POST,'fecha_nacimiento_dia');
$fecha_nacimiento_mes=app_value($_POST,'fecha_nacimiento_mes');
$fecha_nacimiento_ano=app_value($_POST,'fecha_nacimiento_ano');
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
                        
$fecha_informacion_dia=app_value($_POST,'fecha_informacion_dia');
$fecha_informacion_mes=app_value($_POST,'fecha_informacion_mes');
$fecha_informacion_ano=app_value($_POST,'fecha_informacion_ano');
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
                        
$fecha_b1_dia=app_value($_POST,'fecha_b1_dia');
$fecha_b1_mes=app_value($_POST,'fecha_b1_mes');
$fecha_b1_ano=app_value($_POST,'fecha_b1_ano');
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
$fecha_b2_dia=app_value($_POST,'fecha_b2_dia');
$fecha_b2_mes=app_value($_POST,'fecha_b2_mes');
$fecha_b2_ano=app_value($_POST,'fecha_b2_ano');
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
$fecha_b3_dia=app_value($_POST,'fecha_b3_dia');
$fecha_b3_mes=app_value($_POST,'fecha_b3_mes');
$fecha_b3_ano=app_value($_POST,'fecha_b3_ano');
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
$fecha_c_dia=app_value($_POST,'fecha_c_dia');
$fecha_c_mes=app_value($_POST,'fecha_c_mes');
$fecha_c_ano=app_value($_POST,'fecha_c_ano');
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

$id_quitar_nota=app_value($_POST,'id_quitar_nota');
                // Borra la nota
                if (strlen($id_quitar_nota)!=0) {
                        db_query_params('DELETE FROM contactos_seguimiento WHERE id_nota=? AND id_contacto=?', 'si', array($id_quitar_nota,(int)$id_contacto));
                        }
                        
if (strlen($id_contacto)!=0) {
        // Guarda cambios
        db_query_params('UPDATE contactos SET no_publicidad=?, calle=?, poblacion=?, Nombre=?, Apellido1=?, Apellido2=?, persona_contacto=?, proceso_decision=?, telefono=?, email=?, comentarios=? WHERE id_contacto=?;', 'ssssssssssss', array((string)($no_publicidad), (string)($calle), (string)($poblacion), (string)($nombre_alu), (string)($apellido1_alu), (string)($apellido2_alu), (string)($persona_contacto), (string)($proceso_decision), (string)($telefono), (string)($email), (string)($comentarios), $id_contacto)) or die(db_error());
        db_query_params('UPDATE contactos SET motivo_cancelacion=?, como_conocio=?,nonato=?,prematuro=?,prematuro1=?,id_usuario=?, nuevo_alumno=?, id_centro=?,id_curso=?,fecha_nacimiento=?, sexo=?, codigo_postal=?, fecha_informacion=?, fecha_b1=?, fecha_b2=?, fecha_b3=?, fecha_c=? WHERE id_contacto=?;', 'ssssssssssssssssss', array((string)($motivo_cancelacion), (string)($como_conocio), (string)($nonato), (string)($prematuro), (string)($prematuro1), (string)($id_usuario), (string)($nuevo_alumno), $id_centro_contacto, $id_curso_contacto, (string)($fecha_nacimiento), (string)($sexo), (string)($codigo_postal), (string)($fecha_informacion), (string)($fecha_b1), (string)($fecha_b2), (string)($fecha_b3), (string)($fecha_c), $id_contacto)) or die(db_error());
        
        db_query_params('UPDATE contactos SET tipo_alumno=?, horarios_quiere_asistir=?, dias_quiere_asistir=?, antiguo_alumno_lapices=?, dni_padre1=?, nombre_padre2=?, telefono_padre2=?, email_padre2=?, dni_padre2=?, telefonos_familia=?, email_familia=?, contacto_emergencia1=?, telefono_contacto_emergencia1=?, contacto_emergencia2=?, telefono_contacto_emergencia2=?, persona_autorizada=?, semanas_embarazo=?, parentesco_padre1=?, parentesco_padre2=? WHERE id_contacto=?;', 'ssssssssssssssssssss', array((string)($tipo_alumno), (string)($horarios_quiere_asistir), (string)($dias_quiere_asistir), (string)($antiguo_alumno_lapices), (string)($dni_padre1), (string)($nombre_padre2), (string)($telefono_padre2), (string)($email_padre2), (string)($dni_padre2), (string)($telefonos_familia), (string)($email_familia), (string)($contacto_emergencia1), (string)($telefono_contacto_emergencia1), (string)($contacto_emergencia2), (string)($telefono_contacto_emergencia2), (string)($persona_autorizada), (string)($semanas_embarazo), (string)($parentesco_padre1), (string)($parentesco_padre2), $id_contacto)) or die(db_error());
        
        } else {
        // Introduce nuevo centro
        db_query_params('INSERT INTO contactos (calle,poblacion, Nombre, Apellido1, Apellido2, persona_contacto, proceso_decision, telefono, email, fecha_contacto, comentarios, coordenadas, no_publicidad,motivo_cancelacion,como_conocio,nonato,prematuro,prematuro1,id_usuario,nuevo_alumno, id_centro,id_curso,fecha_nacimiento, sexo, codigo_postal, fecha_informacion, fecha_b1, fecha_b2, fecha_b3, fecha_c) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?,?,?,?,?,?, ?,?,?, ?, ?, ?, ?, ?, ?, ?);', 'ssssssssssssssssssssssssssssss', array((string)($calle), (string)($poblacion), (string)($nombre_alu), (string)($apellido1_alu), (string)($apellido2_alu), (string)($persona_contacto), (string)($proceso_decision), (string)($telefono), (string)($email), (string)(date("Y-m-d")), (string)($comentarios), (string)($coordenadas), (string)($no_publicidad), (string)($motivo_cancelacion), (string)($como_conocio), (string)($nonato), (string)($prematuro), (string)($prematuro1), (string)($id_usuario), (string)($nuevo_alumno), (string)($id_centro_contacto), (string)($id_curso_contacto), (string)($fecha_nacimiento), (string)($sexo), (string)($codigo_postal), (string)($fecha_informacion), (string)($fecha_b1), (string)($fecha_b2), (string)($fecha_b3), (string)($fecha_c))) or die(db_error());
        $id_contacto=db_insert_id();
        
        db_query_params('UPDATE contactos SET tipo_alumno=?, horarios_quiere_asistir=?, dias_quiere_asistir=?, antiguo_alumno_lapices=?, dni_padre1=?, nombre_padre2=?, telefono_padre2=?, email_padre2=?, dni_padre2=?, telefonos_familia=?, email_familia=?, contacto_emergencia1=?, telefono_contacto_emergencia1=?, contacto_emergencia2=?, telefono_contacto_emergencia2=?, persona_autorizada=?, semanas_embarazo=?, parentesco_padre1=?, parentesco_padre2=? WHERE id_contacto=?;', 'ssssssssssssssssssss', array((string)($tipo_alumno), (string)($horarios_quiere_asistir), (string)($dias_quiere_asistir), (string)($antiguo_alumno_lapices), (string)($dni_padre1), (string)($nombre_padre2), (string)($telefono_padre2), (string)($email_padre2), (string)($dni_padre2), (string)($telefonos_familia), (string)($email_familia), (string)($contacto_emergencia1), (string)($telefono_contacto_emergencia1), (string)($contacto_emergencia2), (string)($telefono_contacto_emergencia2), (string)($persona_autorizada), (string)($semanas_embarazo), (string)($parentesco_padre1), (string)($parentesco_padre2), $id_contacto)) or die(db_error());
        
        /*        
        mysql_query("UPDATE contactos SET motivo_cancelacion='$motivo_cancelacion', como_conocio='$como_conocio',nonato='$nonato',prematuro='$prematuro',prematuro1='$prematuro1',id_usuario='$id_usuario', nuevo_alumno='$nuevo_alumno', id_centro=$id_centro_contacto,id_curso=$id_curso_contacto,fecha_nacimiento='$fecha_nacimiento', sexo='$sexo', codigo_postal='$codigo_postal', fecha_informacion='$fecha_informacion', fecha_b1='$fecha_b1', fecha_b2='$fecha_b2', fecha_b3='$fecha_b3', fecha_c='$fecha_c' WHERE id_contacto=$id_contacto;") or die(mysql_error());
        */
        }
}
if (app_value($_POST,'retorno')==1) {
        $url_retorno="nuevo_contacto.php?id_contacto=$id_contacto";
        } else {
        $url_retorno="listado_contactos.php";
        }
header("Location: ".$url_retorno);
?>
