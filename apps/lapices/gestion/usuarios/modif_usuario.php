<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");

$id_usu=$_POST["Cod"];
$id_tabla_usu=$_POST["id_tabla_usu"];

$nombre=$_POST["nombre"];
$apellido1=$_POST["apellido1"];
$apellido2=$_POST["apellido2"];
$nif=$_POST["nif"];
$nss=$_POST["nss"];
$direccion=$_POST["direccion"];
$telefono1=$_POST["telefono1"];
$telefono2=$_POST["telefono2"];
$movil=$_POST["movil"];
$provincia=$_POST["provincia"];
$poblacion=$_POST["poblacion"];
$dia=$_POST["fechanac_dia"];
$mes=$_POST["fechanac_mes"];
$anyo=$_POST["fechanac_ano"];

$dia=$_POST["fechanac_dia"];
$mes=$_POST["fechanac_mes"];
$anyo=$_POST["fechanac_ano"];

$dia_prl=$_POST["prl_dia"];
$mes_prl=$_POST["prl_mes"];
$ano_prl=$_POST["prl_ano"];

$titulacion=$_POST["titulacion"];
if (strlen($titulacion)!=0) {
                        $titulacion=addslashes($titulacion);
                        }
$especialidad=$_POST["especialidad"];
if (strlen($especialidad)!=0) {
                        $especialidad=addslashes($especialidad);
                        }
$curriculum=$_POST["curriculum"];
if (strlen($curriculum)!=0) {
                        $curriculum=addslashes($curriculum);
                        }

$sexo=$_POST["sexo"];
$email=$_POST["email"];
$cp=$_POST["cp"];

$num_errores=0;

if ((strlen($nombre)==0) || (strlen($apellido1)==0))
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="No se han rellenado correctamente nombre y los apellidos.";
}

$fecha_nacimiento=$anyo."/".$mes."/".$dia;

if ($dia=="--" || $mes=="--" || $anyo=="----")
{
  // $num_errores=$num_errores+1;
  // $errores[$num_errores]="Debe escribir su fecha de nacimiento.";
  $fecha_nacimiento="";
} else {
        if (!checkdate(intval($mes),intval($dia),intval($anyo))) {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="La fecha de nacimiento escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                 }
}

$fecha_prevencion=$ano_prl."/".$mes_prl."/".$dia_prl;

if ($dia_prl=="--" || $mes_prl=="--" || $ano_prl=="----")
{
  // $num_errores=$num_errores+1;
  // $errores[$num_errores]="Debe escribir su fecha de nacimiento.";
  $fecha_prevencion="";
} else {
        if (!checkdate(intval($mes_prl),intval($dia_prl),intval($ano_prl))) {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="La fecha de entrega de la ficha de prevención escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                 }
}

/*
if ((strlen($direccion)==0))
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="No ha introducido un domicilio.";
}
if (strlen($telefono1)==0 && strlen($telefono2)==0 && strlen($movil)==0)
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="Debe introducir al menos un número de teléfono.";
}

if ((strlen($provincia)==0))
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="Debe indicar la provincia.";
}
if ((strlen($poblacion)==0))
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="Falta rellenar la población.";
}
*/
if ((strlen($email)==0))
{
  // $num_errores=$num_errores+1;
  // $errores[$num_errores]="Debe introducir una dirección de Email para que podamos contactar con usted.";
} else {

if ((strpos($_POST["email"],"@") ? strpos($_POST["email"],"@")+1 : 0)==0 || (strpos($_POST["email"]," ") ? strpos($_POST["email"]," ")+1 : 0)!=0 || (strpos($_POST["email"],".") ? strpos($_POST["email"],".")+1 : 0)==0)
{
  $num_errores=$num_errores+1;
  $errores[$num_errores]="El Email introducido parece ser incorrecto, falta la arroba (@) o contiene caracteres no permitidos (espacios, acentos, etc.).";
}

}

$db_m1=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_m1);

if (strlen($nif)==0) {
               // $num_errores=$num_errores+1;
               // $errores[$num_errores]="No has introducido el DNI del usuario.";
        } else {
                $busca_dni=db_query("SELECT * FROM usuarios WHERE DNI='".$nif."' AND id_usuario<>".$id_tabla_usu);
                if (db_num_rows($busca_dni) != 0) {
                        // $num_errores=$num_errores+1;
                        // $errores[$num_errores]="El DNI introducido ($nif) ya existe en la base de datos y pertenece a otro usuario.";
                        }
                }
if ($num_errores==0) {

//Introduce en la tabla usuarios

$consulta_update="UPDATE usuarios SET ".
"Nombre='".$nombre."', ".
"Apellido1='".$apellido1."', ".
"Apellido2='".$apellido2."', ".
"Direccion='".$direccion."', ".
"fecha_nacimiento='".$fecha_nacimiento."', ".
"sexo='".$sexo."', ".
"telefono1='".$telefono1."', ".
"telefono2='".$telefono2."', ".
"movil='".$movil."', ".
"email='".$email."', ".
"Provincia='".$provincia."', ".
"Poblacion='".$poblacion."', ".
"titulacion='".$titulacion."', ".
"especialidad='".$especialidad."', ".
"curriculum='".$curriculum."', ".
"fecha_prevencion='".$fecha_prevencion."', ".
"cp='".$cp."', ".
"DNI='".$nif."', NSS='".$nss."' WHERE id_usuario=".$id_tabla_usu;

db_query($consulta_update) or die(db_error());

header("Location: /index.php");

} else {

?>
<html>

<head>
<title>Configuración de usuarios</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_tabla1; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background="Imagen/fond_lat1_nt.gif" bgproperties="fixed" text=<?php  echo $fondo_tabla1; ?>>
<div align="center">
<p>&nbsp;</p>
<?php 
$ancho_tabla=450;
?>
      <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd.gif" border=0 width=8 height=8></td>
                </tr>
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="FFFFFF" colspan=3 align="center">
<font face="<?php  echo $fuente_general; ?>" color="<?php  echo $color_fuente_lat; ?>">
<small>
<p align="center"><b><i>
Se han detectado errores al cumplimentar el formulario.<br>Por favor, vuelva atras para revisar
los siguientes puntos:<br>
</i></b></p>
<p align="center">
<font face="<?php  echo $fuente_general; ?>">
<table width="100%">
<?php 
  if ($error_nif=true)
  {
    for ($contador=1; $contador<=$num_errores; $contador=$contador+1)
    {
      print "<tr><td><font face=\"Verdana\" size=1 color=\"".$color_fuente."\"><b>- $errores[$contador]</b></font></td></tr>";

    }
?>
</table>
<p align="center"><a href="javascript:history.go(-1)">- Volver a rellenar el formulario - </a></p>

                        </td>
                </tr>
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_ii.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_id.gif" border=0 width=8 height=8></td>
                </tr>
    </table>
</div>
</body>
</html>
<?php 
}
}
db_close();
?>
