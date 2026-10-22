<?php 
                $usuario_coordenadas=$_POST["coordenadas"];

                // Comprobamos si es nuevo
                if ($id_usuario_seleccionado==0) {
                        $consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso, coordenadas) VALUES ('1','".strftime("%Y/%m/%d")."','0','".$usuario_coordenadas."')";
                        db_query($consulta_insert) or die(db_error());
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
                $usuario_estado_civil=app_request_value($_POST, "estado_civil", "");
                $estados_civiles_validos=array("CASADO/A", "DIVORCIADO/A", "PAREJA DE ECHO", "VIUDO/A", "SEPARADO/A");
                if (!in_array($usuario_estado_civil, $estados_civiles_validos, true)) {
                        $usuario_estado_civil="";
                        }
				$usuario_sip=$_POST["sip"];

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
        
        db_query("UPDATE usuarios SET Nombre='".$usuario_nombre."', Apellido1='".$usuario_apellido1."', Apellido2='".$usuario_apellido2."', sexo='".$usuario_sexo."', fecha_nacimiento='".$fecha_nacimiento."', nonato='$usuario_nonato', prematuro='$usuario_prematuro', prematuro1='$usuario_prematuro1', SIP='$usuario_sip' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-2");

        // Introduce información de contacto
        db_query("UPDATE usuarios SET nacionalidad='$usuario_nacionalidad', estado_civil='$usuario_estado_civil', Direccion='".$usuario_direccion."', cp='".$usuario_cp."', Poblacion='".$usuario_poblacion."', Provincia='".$usuario_provincia."', telefono1='".$usuario_telefono1."', telefono2='".$usuario_telefono2."', movil='".$usuario_movil."', email='".$usuario_email."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-15");
        }
        
        // Comprobamos si es nuevo
        if ($id_usuario_seleccionado!=0) {
                        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);

                        if (db_num_rows($consulta_datos_usuario)!=0)
                                {
                                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                                }
                        }
?>
