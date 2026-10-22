<?php 
// 2. Introducimos los nuevos
$numero_de_filas=$_POST["filas_actual"];

for ($contador_filas=1;$contador_filas<=intval($numero_de_filas);$contador_filas++) {
        $codigo_actual=$_POST["codigo".$contador_filas];
        $parentesco_actual=$_POST["parentesco".$contador_filas];
        if (strlen($codigo_actual)!=0) {
        
        
// Crea nombre de usuario

// Busca datos usuario

  // Busca datos del padre.
  $busca_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=$codigo_actual") or die(db_error()."- No se encuentran los datos del padre.");

  $datos_padre=db_fetch_array($busca_datos_padre);

  // Comprobamos si ya tiene usuario y password
  $busca_parentesco=db_query("SELECT * FROM padres WHERE padre_de=$id_usuario_seleccionado AND id_usuario=$codigo_actual");

  $datos_parentesco=db_fetch_array($busca_parentesco);
  
  $nombre_usu=$datos_parentesco["usuario_webcam"];
  $password=$datos_parentesco["password_webcam"];
  
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

  mt_srand((double)microtime()*1000000);
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

    $consulta_nombre_usu=db_query("SELECT * FROM padres WHERE usuario_webcam='" . $nombre_usu1 . "'");

    if (db_num_rows($consulta_nombre_usu)==0)
    {
      $no_existe=true;
    }
    db_free_result($consulta_nombre_usu);
    $cuenta=$cuenta+1;
  }
  $nombre_usu=$nombre_usu1;

//Genera Password

  $password="";
  for ($l=1; $l<=3; $l=$l+1)
  {
    mt_srand((double)microtime()*1000000);
    $letra=97+intval((mt_rand(0,10000000)/10000000)*26);
    $password=$password.chr($letra);

  }

  for ($n=1; $n<=3; $n=$n+1)
  {
    mt_srand((double)microtime()*1000000);
    $letra=48+intval((mt_rand(0,10000000)/10000000)*10);
    $password=$password.chr($letra);

  }
           }
        // Borramos las anteriores relaciones para evitar duplicados
          db_query("DELETE FROM padres WHERE padre_de=$id_usuario_seleccionado AND id_usuario=$codigo_actual");
        // Introducimos la relacion nueva
          $password_hash=app_password_hash_if_needed($password);
          db_query_params(
                  "INSERT INTO padres (id_usuario, padre_de, parentesco, usuario_webcam, password_webcam) VALUES (?, ?, ?, ?, ?)",
                  "iisss",
                  array((int) $codigo_actual, (int) $id_usuario_seleccionado, $parentesco_actual, $nombre_usu, $password_hash)
          );
                }
        }

?>
