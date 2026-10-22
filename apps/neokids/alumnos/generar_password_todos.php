<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

if((int)$nivel_acceso!==4)security_stop();
$contador_contrase=0;

// Recibimos los datos
$busca_padres=db_query("SELECT * FROM padres;");

while($padres=db_fetch_array($busca_padres)) {
$id_padre=$padres["id_padre"];

// Crea nombre de usuario

// Busca datos usuario

  // Comprobamos si ya tiene usuario y password
  $busca_parentesco=db_query_params('SELECT * FROM padres WHERE id_padre=?', 's', array($id_padre));

  $datos_parentesco=db_fetch_array($busca_parentesco);

  $nombre_usu=$datos_parentesco["usuario_webcam"];
  $password=$datos_parentesco["password_webcam"];

  // Busca datos del padre.
  $busca_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($datos_parentesco["id_usuario"])) or die(db_error()."- No se encuentran los datos del padre.");

  $datos_padre=db_fetch_array($busca_datos_padre);

  if (strlen($nombre_usu)==0) {

  $usuario_nombre=$datos_padre["Nombre"];
  $usuario_apellido1=$datos_padre["Apellido1"];
  $usuario_apellido2=$datos_padre["Apellido2"];

  if (strlen($usuario_apellido2)!=0) {
        $parte_ultima=substr($usuario_apellido2,0,2);
        } else {
        $parte_ultima="";
        }
  $nombre_usu=substr($usuario_nombre,0,2).substr($usuario_apellido1,0,2).$parte_ultima;

  mt_srand((float)microtime()*1000000);
  $nombre_usu2="";
  for ($l=1; $l<=strlen($nombre_usu); $l=$l+1)
  {

    $letra=substr($nombre_usu,$l-1,1);
    $cod_char=ord($letra);
    if ($cod_char<48)
    {
      $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
    }
    if ($cod_char>57 && $cod_char<65)
    {
      $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
    }
    if ($cod_char>90 && $cod_char<97)
    {
      $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
    }
    if ($cod_char>122)
    {
      $cod_char=intval((26*(mt_rand(0,10000000)/10000000))+65);
    }
    if ($cod_char>=97 && $cod_char<=122)
    {
      $cod_char=$cod_char-32;
    }
    $letra=chr($cod_char);
    $nombre_usu2=$nombre_usu2.$letra;

  }

  $nombre_usu=$nombre_usu2;

  $cuenta=0;
  $no_existe=false;
  while($no_existe==false)
  {
    if ($cuenta==0)
    {
      $nombre_usu1=$nombre_usu;
    }
      else
    {
      $nombre_usu1=$nombre_usu.$cuenta;
    }

    $consulta_nombre_usu=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usu1)));

    if (db_num_rows($consulta_nombre_usu)==0)
    {
      $no_existe=true;
    }
    db_free_result($consulta_nombre_usu);
    $cuenta=$cuenta+1;
  }
  $nombre_usu=$nombre_usu1;

$password=security_generate_password($nombre_usu);
$hash=security_password_hash($password);
// Introducimos nombre de usuario y password
db_query_params('UPDATE padres SET usuario_webcam=?, password_webcam=? WHERE id_padre=?;', 'sss', array((string)($nombre_usu), $hash, $id_padre));
security_store_family_password(array('id_usuario'=>$datos_parentesco['id_usuario'],'usuario_webcam'=>$nombre_usu,'password_webcam'=>$hash),$password);
$contador_contrase++;
}

}
?>
Total contraseñas creadas: <?=$contador_contrase;?>
