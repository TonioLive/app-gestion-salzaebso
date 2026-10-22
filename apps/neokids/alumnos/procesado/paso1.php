<?php 
                $usuario_coordenadas=$_POST["coordenadas"];

                // Comprobamos si es nuevo
                if ($id_usuario_seleccionado==0) {
                        $consulta_insert=array('INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso, coordenadas) VALUES (\'1\',?,\'0\',?)', 'ss', array((string)(strftime("%Y/%m/%d")), (string)($usuario_coordenadas)));
                        db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
                        $id_usuario_seleccionado=db_insert_id();
                        }


                // Recibimos datos del formulario
                $usuario_nombre=$_POST["Nombre"];
                $usuario_apellido1=$_POST["Apellido1"];
                $usuario_apellido2=$_POST["Apellido2"];
                $usuario_sexo=$_POST["sexo"];
                $usuario_fechanac_dia=$_POST["usuario_fechanac_dia"];
                $usuario_fechanac_mes=$_POST["usuario_fechanac_mes"];
                $usuario_fechanac_ano=$_POST["usuario_fechanac_ano"];
                $usuario_nonato=$_POST["nonato"];
                $usuario_prematuro=$_POST["prematuro"];
                $usuario_prematuro1=$_POST["prematuro1"];
                $usuario_direccion=$_POST["Direccion"];
                $usuario_cp=$_POST["CP"];
                $usuario_poblacion=$_POST["poblacion"];
                $usuario_provincia=$_POST["provincia"];
                $usuario_telefono1=$_POST["telefono1"];
                $usuario_telefono2=$_POST["telefono2"];
                $usuario_movil=$_POST["movil"];
                $usuario_email=$_POST["email"];
                $usuario_nacionalidad=$_POST["nacionalidad"];


                // comprobamos los datos opcionales que necesiten ser comprobados
                if ($usuario_fechanac_dia!="--" and $usuario_fechanac_mes!="--" and $usuario_fechanac_ano!="----") {
                        $fecha_nacimiento=$usuario_fechanac_ano."/".$usuario_fechanac_mes."/".$usuario_fechanac_dia;
                        if (!checkdate(intval($usuario_fechanac_mes),intval($usuario_fechanac_dia),intval($usuario_fechanac_ano))) {
                                $fecha_nacimiento="";
                                }
                        } else {
                        $fecha_nacimiento="";
                        }
                if ($usuario_nonato==1) {
                        $fecha_nacimiento="";
                        }

if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        
        db_query_params('UPDATE usuarios SET Nombre=?, Apellido1=?, Apellido2=?, sexo=?, fecha_nacimiento=?, nonato=?, prematuro=?, prematuro1=? WHERE id_usuario=?', 'sssssssss', array((string)($usuario_nombre), (string)($usuario_apellido1), (string)($usuario_apellido2), (string)($usuario_sexo), (string)($fecha_nacimiento), (string)($usuario_nonato), (string)($usuario_prematuro), (string)($usuario_prematuro1), $id_usuario_seleccionado)) or die(db_error()."-2");

        // Introduce información de contacto
        db_query_params('UPDATE usuarios SET nacionalidad=?, Direccion=?, cp=?, Poblacion=?, Provincia=?, telefono1=?, telefono2=?, movil=?, email=? WHERE id_usuario=?', 'ssssssssss', array((string)($usuario_nacionalidad), (string)($usuario_direccion), (string)($usuario_cp), (string)($usuario_poblacion), (string)($usuario_provincia), (string)($usuario_telefono1), (string)($usuario_telefono2), (string)($usuario_movil), (string)($usuario_email), $id_usuario_seleccionado)) or die(db_error()."-15");
        }
        
        // Comprobamos si es nuevo
        if ($id_usuario_seleccionado!=0) {
                        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

                        if (db_num_rows($consulta_datos_usuario)!=0)
                                {
                                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                                }
                        }
?>
