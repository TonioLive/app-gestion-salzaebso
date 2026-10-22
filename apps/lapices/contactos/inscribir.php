<?php  require("../configuracion/config.php"); ?>
<?php 
$id_contacto=$_GET["id_contacto"];
$id_usuario_seleccionado=$_GET["id_alumno"];

                // Comprobamos si es nuevo
                if ($id_usuario_seleccionado==0) {
                        $consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('1','".strftime("%Y/%m/%d")."','0')";
                        db_query($consulta_insert) or die(db_error());
                        $id_usuario_seleccionado=db_insert_id();
                        }
                        
                // Relacionar
                $fecha_c=date("Y-m-d");
                db_query("UPDATE contactos SET id_usuario='$id_usuario_seleccionado', fecha_c='$fecha_c', proceso_decision='c' WHERE id_contacto=$id_contacto;") or die(db_error());

                // Datos de la tabla CONTACTOS
                $consulta_datos_contactos=db_query("SELECT * FROM contactos WHERE id_contacto=$id_contacto;") or die(db_error());
                $datos_contactos=db_fetch_array($consulta_datos_contactos);

        // Copia datos de la tabla de contactos
        db_query("UPDATE usuarios SET SIP='".$datos_contactos["SIP"]."',Nombre='".$datos_contactos["Nombre"]."', Apellido1='".$datos_contactos["Apellido1"]."', Apellido2='".$datos_contactos["Apellido2"]."', sexo='".$datos_contactos["sexo"]."', fecha_nacimiento='".$datos_contactos["fecha_nacimiento"]."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-2");

        // Introduce información de contacto
        db_query("UPDATE usuarios SET coordenadas='".$datos_contactos["coordenadas"]."',Direccion='".$datos_contactos["calle"]."', cp='".$datos_contactos["codigo_postal"]."', Poblacion='".$datos_contactos["poblacion"]."', telefono1='".$datos_contactos["telefono"]."', email='".$datos_contactos["email"]."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-15");


// Por ultimo vamos al paso de inscripción pero con el curso PRESELECCIONADO

$id_centro=$datos_contactos["id_centro"];
$id_curso=$datos_contactos["id_curso"];

header("Location: /alumnos/nuevo_alumno4.php?u=$id_usuario_seleccionado&id_centro_pre=$id_centro&id_curso_pre=$id_curso");

?>
