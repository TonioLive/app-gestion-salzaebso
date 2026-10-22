<?php  require("../configuracion/config.php"); ?>
<?php 
$id_contacto=$_GET["id_contacto"];
$id_usuario_seleccionado=$_GET["id_alumno"];

                // Comprobamos si es nuevo
                if ($id_usuario_seleccionado==0) {
                        $consulta_insert=array('INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES (\'1\',?,\'0\')', 's', array((string)(strftime("%Y/%m/%d"))));
                        db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
                        $id_usuario_seleccionado=db_insert_id();
                        }
                        
                // Relacionar
                $fecha_c=date("Y-m-d");
                db_query_params('UPDATE contactos SET id_usuario=?, fecha_c=?, proceso_decision=\'c\' WHERE id_contacto=?;', 'sss', array((string)($id_usuario_seleccionado), (string)($fecha_c), $id_contacto)) or die(db_error());

                // Datos de la tabla CONTACTOS
                $consulta_datos_contactos=db_query_params('SELECT * FROM contactos WHERE id_contacto=?;', 's', array($id_contacto)) or die(db_error());
                $datos_contactos=db_fetch_array($consulta_datos_contactos);

        // Copia datos de la tabla de contactos
        db_query_params('UPDATE usuarios SET SIP=?,Nombre=?, Apellido1=?, Apellido2=?, sexo=?, fecha_nacimiento=? WHERE id_usuario=?', 'sssssss', array((string)($datos_contactos["SIP"]), (string)($datos_contactos["Nombre"]), (string)($datos_contactos["Apellido1"]), (string)($datos_contactos["Apellido2"]), (string)($datos_contactos["sexo"]), (string)($datos_contactos["fecha_nacimiento"]), $id_usuario_seleccionado)) or die(db_error()."-2");

        // Introduce información de contacto
        db_query_params('UPDATE usuarios SET cp=?, Poblacion=?, telefono1=?, email=? WHERE id_usuario=?', 'sssss', array((string)($datos_contactos["codigo_postal"]), (string)($datos_contactos["poblacion"]), (string)($datos_contactos["telefono"]), (string)($datos_contactos["email"]), $id_usuario_seleccionado)) or die(db_error()."-15");


// Por ultimo vamos al paso de inscripción pero con el curso PRESELECCIONADO

$id_centro=$datos_contactos["id_centro"];
$id_curso=$datos_contactos["id_curso"];

header("Location: /alumnos/nuevo_alumno6.php?u=$id_usuario_seleccionado&id_centro_pre=$id_centro&id_curso_pre=$id_curso");

?>
