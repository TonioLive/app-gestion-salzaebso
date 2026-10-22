<?php

use \Firebase\JWT\JWT;

$key = app_jwt_secret();

// Ejemplo
/*
$token = array(
    "usuario" => "luis",
    "idUsuario" => "1",
    "nombre" => "Luis",
    "apellido1" => "Doassans",
    "apellido2" => "Pizzo"
);
*/
// Codificar TOKEN JWT:         $jwt = JWT::encode($token, $key);
// Decodificar TOKEN JWT:         $decoded = JWT::decode($jwt, $key, array('HS256'));

// Borra tokens expirados
$fecha_control=date("Y-m-d H:i:s",(time()-86400));
$borra_token=db_query("DELETE FROM tokens_activos WHERE creacion<'".$fecha_control."'");

/*
//SOLO PARA DEBUG

$log_api='';

foreach (getallheaders() as $name => $value) {
        $log_api.="$name: $value\n";
    }

foreach ($_POST as $key => $value) {
        $log_api.="$key: $value\n";
    }

$log_api.=$json;

//Introducimos en la tabla de sesiones
mysql_query("INSERT INTO log_api (Fecha, Hora, REMOTE_ADDR, HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent,log_api) VALUES ('".date("Y/m/d")."', '".date("H:i")."', '".$REMOTE_ADDR."', '".$HTTP_ACCEPT_LANGUAGE."', '".$HTTP_USER_AGENT."','".$log_api."')") or die(mysql_error());
  
*/

$token = $_GET['token'];

$json = file_get_contents('php://input');

$data = json_decode($json);

$nombre_usuario = $data->usuario;
$pass_usuario = $data->contras;
$token_fcm = $data->FCMtoken;

$json = array();
$error = 0;

if ((strlen($token)!=0) and ($token!='null')) {
        // Comprueba si el token es válido
        
        $decoded = JWT::decode($token, $key, array('HS256'));
        
        $caducidad = $decoded->caducidad;

        if ($caducidad) {
                // Comprueba caducidad
                if (strtotime($caducidad)>=time()) {
                        // Busca si el token está activo
                        $busca_datos_usuario=db_query("SELECT * FROM tokens_activos WHERE token='$token'");
                        if (db_num_rows($busca_datos_usuario)==0) {
                                // El Token ha caducado
                                $error = 3;
                                $error = "3: Token deleted";
                        }                
                } else {
                        // El token era correcto pero ha caducado 
                        $error = 2;
                        $error = "2: Token expired";                
                }
        
        } else {                
                // El token no es correcto
                $error = 1;
                $error = "1: Token invalid";        
        }        
        
        if ($error == 0) {
                // Todo correcto
                $error = 202;
                $json['status'] = '202';
                $json['message'] = 'Accepted';
                $json['error'] = '0: No error';  
                $json['id_usuario'] = $id_usuario;      
                $id_usuario = $decoded->idUsuario;
        } else {
                // Error Token
                $json['status'] = '401';
                $json['message'] = 'Unauthorized';
                $json['error'] = $error;        
        }
} 


if ($error!=202) {
        
        if ((strlen($nombre_usuario)!=0) and (strlen($pass_usuario)!=0)) {
        
                // Comprueba si usuario y contraseña son válidos                
                
$errores="";

                if (strlen($nombre_usuario)==0) {
                $errores=$errores."- No ha introducido su nombre de usuario";
        }
                if (strlen($pass_usuario)==0) {
                $errores=$errores."- No ha introducido su contraseña";
        }

                //Compruebo si se han escrito espacios o caracteres no validos en el nombre de usuario
                $nombre_usuario=str_replace(" ", "", $nombre_usuario);
                for ($l=1; $l<=strlen($nombre_usuario); $l=$l+1)
                {
                          $letra=substr($nombre_usuario,$l-1,1);
                          if (ord($letra)<48) {
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación";
                      break;
                      }
                          if (ord($letra)>57 && ord($letra)<64) {
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación";
                      break;
                      }
                          if (ord($letra)>90 && ord($letra)<97) {
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación";
                      break;
                      }
                          if (ord($letra)>122){
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación";
                      break;
                      }
                }

                //Comprubebo si se han escrito espacios o caracteres no validos en la contraseña

                for ($l=1; $l<=strlen($pass_usuario); $l=$l+1)
                {
                          $letra=substr($pass_usuario,$l-1,1);
                          if (ord($letra)<48) {
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      break;
                      }
                          if (ord($letra)>57 && ord($letra)<64) {
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      break;
                      }
                          if (ord($letra)>90 && ord($letra)<97) {
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      break;
                      }
                          if (ord($letra)>122){
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      break;
                      }
                }

                if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'");
        
        if (db_num_rows($busca_usuario)==0) {
                $errores=$errores."El nombre de usuario introducido no existe";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                
                if ($datos_usuario["password_webcam"]!=$pass_usuario) {
                        $errores=$errores."La contraseña introducida no es correcta";
                        }
                
                }

        }                                                      
                
                // Fin comprobacion usuario y contraseña
                
                if ($errores=="") {
                        $json['status'] = '202';
                        $json['message'] = 'Accepted';        
                        $json['error'] = '0: No error';
                        
                    // Iniciamos la sesión del padre
                    $id_usuario=$datos_usuario["id_usuario"];
                    $id_hijo=$datos_usuario["padre_de"];
                    $id_padre=$datos_usuario["id_padre"];

                    // Busca datos del padre.
                    $busca_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario") or die(db_error()."- No se encuentran los datos del padre.");

                    $datos_padre=db_fetch_array($busca_datos_padre);                                
                        
                        $nuevoToken = array(
                            "usuario"   => $nombre_usuario,
                            "idUsuario" => $id_usuario,
                            "idHijo"    => $id_hijo,
                            "idPadre"    => $id_padre,
                            "nombre"    => $datos_padre->Nombre,
                            "apellido1" => $datos_padre->Apellido1,
                            "apellido2" => $datos_padre->Apellido2,
                            "caducidad" => date("Y-m-d H:i:s", (time()+86400))
                        );
                        
                        $jwt = JWT::encode($nuevoToken, $key);
                        
                        // Inserta nuevo TOKEN
                        $inserta_token=db_query("INSERT INTO tokens_activos (token,id_usuario,id_hijo,id_padre) VALUES ('$jwt','$id_usuario','$id_hijo','$id_padre');");   

                        // Comprueba si tenemos su token de FCM
                        $buscaFcmToken = db_query("SELECT * FROM tokens_fcm WHERE id_usuario='id_usuario' AND $id_padre='$id_padre' AND FCMtoken='$token_fcm'");
                        if (db_num_rows($buscaFcmToken)==0) {
                                $inserta_token_fcm = db_query("INSERT INTO tokens_fcm (id_usuario,id_padre,FCMtoken,ultimo_acceso) VALUES ('$id_usuario','$id_padre','$token_fcm','".date("Y-m-d H:i:S")."');"); 
                        } else {
                                $actualiza_fecha_token = db_query("UPDATE tokens_fcm SET ultimo_acceso='".date("Y-m-d H:i:S")."' WHERE id_token_fcm=".$datosTokenFcm["id_token_fcm"].";");
                        }

                        $json['id_usuario'] = $id_usuario;                       
                        $json['token'] = $jwt;
                        
                } else {
                        $json['status'] = '401';
                        $json['message'] = 'Unauthorized';
                        $json['error'] = $errores;                
                }
        } else {
                $json['status'] = '403';
                $json['message'] = 'Forbidden';                
        }
}

