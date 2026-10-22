<?php 
require("configuracion/config.php");

$url = explode("?",$_SERVER['HTTP_REFERER']);
$pag_referida=$url[0];
$redir=$pag_referida;
$num_errores=0;

$identificador=$_POST["user"];
$password1=$_POST["pass"];

$descripcion_fallos_registro="";

//comprobamos longitud de el nombre de usuario y la contraseña
//si son 0 es que se ha olvidado de algo
if (strlen($identificador)==0) {
                            $num_errores=$num_errores+1;
                            $errores[$num_errores]="No se han introducido un nombre de usuario";
                            $descripcion_fallos_registro=$descripcion_fallos_registro."Falta usuario ";
                            }
if (strlen($password1)==0) {
                            $num_errores=$num_errores+1;
                            $errores[$num_errores]="No se ha introducido la contraseña";
                            $descripcion_fallos_registro=$descripcion_fallos_registro."Falta password ";
                            }

//Comprubebo si se han escrito espacios o caracteres no validos en el nombre de usuario

for ($l=1; $l<=strlen($identificador); $l=$l+1)
{
  $letra=substr($identificador,$l-1,1);
  if (ord($letra)<48) {
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="El nombre de usuario introducido no es válido, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Usuario no válido ";
                      break;
                      }
  if (ord($letra)>57 && ord($letra)<64) {
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="El nombre de usuario introducido no es válido, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Usuario no válido ";
                      break;
                      }
  if (ord($letra)>90 && ord($letra)<97) {
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="El nombre de usuario introducido no es válido, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Usuario no válido ";
                      break;
                      }
  if (ord($letra)>122){
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="El nombre de usuario introducido no es válido, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Usuario no válido ";
                      break;
                      }
}

//Comprubebo si se han escrito espacios o caracteres no validos en la contraseña

for ($l=1; $l<=strlen($password1); $l=$l+1)
{
  $letra=substr($password1,$l-1,1);
  if (ord($letra)<48) {
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Password no válido ";
                      break;
                      }
  if (ord($letra)>57 && ord($letra)<64) {
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Password no válido ";
                      break;
                      }
  if (ord($letra)>90 && ord($letra)<97) {
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Password no válido ";
                      break;
                      }
  if (ord($letra)>122){
                      $num_errores=$num_errores+1;
                      $errores[$num_errores]="La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación";
                      $descripcion_fallos_registro=$descripcion_fallos_registro."Password no válido ";
                      break;
                      }
}

//compruebo la validez de los datos en la tabla
if ($num_errores==0)
{

$query="SELECT * FROM usuarios WHERE usuario='" . $identificador . "'";

$db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_validacion);

$consulta=db_query($query);

  if (db_num_rows($consulta) == 0)
  {
   $num_errores=$num_errores+1;
   $errores[$num_errores]="El usuario introducido no existe";
   $descripcion_fallos_registro=$descripcion_fallos_registro."Usuario no existe ";
  }
    else
  {
    $rst=db_fetch_array($consulta);
    if ($password1!=$rst["pass"])
    {
   $num_errores=$num_errores+1;
   $errores[$num_errores]="La contraseña introducida no es correcta";
   $descripcion_fallos_registro=$descripcion_fallos_registro."Password incorrecto ";
    } else {
    // comprueba si tiene contrato en vigor
    // Comprueba si tiene contrato en vigor
    $fecha_control=date("Y-m-d");
    $codigo_usuario=$rst["id_usuario"];
    $busca_contratos=db_query("SELECT * FROM personal_contratos WHERE id_empleado=$codigo_usuario AND ((fecha_inicio<='$fecha_control' AND fecha_fin>='$fecha_control') OR (fecha_inicio<='$fecha_control' AND ((fecha_fin='0000-00-00') OR (fecha_fin is Null)))OR (fecha_inicio>'$fecha_control')) ORDER BY fecha_inicio DESC") or die(db_error());

    if (db_num_rows($busca_contratos)==0) {
           $num_errores=$num_errores+1;
           $errores[$num_errores]="El usuario no trabaja actualmente en esta empresa";
           $descripcion_fallos_registro=$descripcion_fallos_registro."El usuario no trabaja";
        }
    
    }
  }
}

//Si hemos llegada hasta aquí y todo_ok=true ya podemos procesar al usuario y validar su acceso
//A continuación leemos los datos adicionales del usuario y los metemos en variables de sesion

if ($num_errores==0)
{

  if (($rst["nivel_acceso"]>=3) and ($rst["nivel_acceso"]<=4)) {
        include("inc/genera_htpasswd.php");
        }

  session_cache_limiter('nocache,private');
  $_SESSION['nombre_usu']=$identificador;
  $_SESSION['permitido']=true;
  $_SESSION['nivel_acceso']=$rst["nivel_acceso"];
  $_SESSION['nombre_completo']=$rst["Nombre"]." ".$rst["Apellido1"]." ".$rst["Apellido2"];
  $_SESSION['email_usu']=$rst["Email"];
  $_SESSION['nombre']=$rst["Nombre"];
  $_SESSION['idusuario']=$rst["id_usuario"];
  $_SESSION['id_centro_usuario']=$rst["id_centro"];
  $_SESSION['usuario_multicentro']=$rst["multicentro"];

  $_SESSION['zona_alumnos']=$rst["zona_alumnos"];
  $_SESSION['zona_aulas']=$rst["zona_aulas"];
  $_SESSION['zona_facturacion']=$rst["zona_facturacion"];
  $_SESSION['zona_comedor']=$rst["zona_comedor"];
  $_SESSION['zona_almacen']=$rst["zona_almacen"];
  $_SESSION['zona_incidencias']=$rst["zona_incidencias"];
  $_SESSION['zona_personal']=$rst["zona_personal"];
  $_SESSION['zona_pedidos']=$rst["zona_pedidos"];
  $_SESSION['zona_inferior']=$rst["zona_inferior"];
  $_SESSION['zona_mensajes']=$rst["zona_mensajes"];
  $_SESSION['zona_compras']=$rst["zona_compras"];
  $_SESSION['zona_gabinete']=$rst["zona_gabinete"];
  $_SESSION['zona_corregir_pruebas']=$rst["zona_corregir_pruebas"];
  $_SESSION['zona_actividades']=$rst["zona_actividades"];
  
  //Encriptamos la contraseña
  $password1 = md5($password1);
  
  $_SESSION['usuario_password']=$password1;
  
  $REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];
  $HTTP_ACCEPT_LANGUAGE=$_SERVER["HTTP_ACCEPT_LANGUAGE"];
  $HTTP_USER_AGENT=$_SERVER["HTTP_USER_AGENT"];


  //Introducimos en la tabla de sesiones
  db_query("INSERT INTO sesion (id_session, Usuario, Tipo, Fecha, Hora, REMOTE_ADDR, HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent, Cerrada, nombre_usuario) VALUES ('".session_id()."', '".$rst["id_usuario"]."', '0', '".date("Y/m/d")."', '".date("H:i")."', '".$REMOTE_ADDR."', '".$HTTP_ACCEPT_LANGUAGE."', '".$HTTP_USER_AGENT."', '0', '".$rst["Nombre"]." ".$rst["Apellido1"]." ".$rst["Apellido2"]."')") or die(db_error());
  
  db_close($db_validacion);
  
  header("Location: ".$redir);
} else {

// Inserta en registro de fallos
  db_query("INSERT INTO sesion_fallos (id_session, Usuario, Pass, Fecha, Hora, REMOTE_ADDR, HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent, descripcion_fallo) VALUES ('".session_id()."', '$identificador', '$password1', '".date("Y/m/d")."', '".date("H:i")."', '".$REMOTE_ADDR."', '".$HTTP_ACCEPT_LANGUAGE."', '".$HTTP_USER_AGENT."', '$descripcion_fallos_registro')") or die(db_error());

}

?>
<html>

<head>
<title>Inicio de sesión</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">
<!--#INCLUDE FILE="../estilos/estilo1.inc" -->
</style>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<p align=right><img src="<?php  echo $url_root; ?>/imagen/nada.gif" height="64"></p>
<br>
<div align="center">
<table width="95%">
<tr>
<td bgcolor="<?php  echo $fondo_tabla1; ?>">
<font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente_lat; ?>">
<small>
<p align="center"><b><i>
Se han detectado errores al cumplimentar el formulario.<br>Por favor, vuelva atras para revisar
los siguientes puntos:<br>
</i></b></p>
<p align="center">
<font face="<?php  echo $fuente_general; ?>">
<table>
<?php  
    for ($contador=1; $contador<=$num_errores; $contador=$contador+1)
    {
      print "<tr><td><font face=\"Verdana\" size=1 color=\"".$color_fuente."\"><b>- $errores[$contador]</b></font></td></tr>";

    }
?>
</table>
<br><br>
<a href="javascript:history.go(-1)">- Volver a rellenar el formulario - </a><br><br>
</small>
</font>
</td>
</tr>
</table>
</div>
</body>
</html>
