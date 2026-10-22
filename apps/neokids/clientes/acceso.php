<?php  require("../configuracion/config.php"); ?>
<?php 
	

$redirect=$_GET["redirect"]; 
$nombre_usuario=$_GET["user"];
$pass_usuario=$_GET["pass"];

if (strlen($nombre_usuario)==0) {
        $redirect=$_POST["redirect"];
        $nombre_usuario=$_POST["user"];
        $pass_usuario=$_POST["pass"];
        }


$errores="";

if (strlen($nombre_usuario)==0) {
        $errores=$errores."- No ha introducido su nombre de usuario.<br>";
        }
if (strlen($pass_usuario)==0) {
        $errores=$errores."- No ha introducido su contraseña.<br>";
        }

//Compruebo si se han escrito espacios o caracteres no validos en el nombre de usuario
$nombre_usuario=str_replace(" ", "", $nombre_usuario);
for ($l=1; $l<=strlen($nombre_usuario); $l=$l+1)
{
  $letra=substr($nombre_usuario,$l-1,1);
  if (ord($letra)<48) {
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
  if (ord($letra)>57 && ord($letra)<64) {
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
  if (ord($letra)>90 && ord($letra)<97) {
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
  if (ord($letra)>122){
                      $errores=$errores."- El nombre de usuario introducido no es válido ($nombre_usuario), no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
}

//Comprubebo si se han escrito espacios o caracteres no validos en la contraseña

for ($l=1; $l<=strlen($pass_usuario); $l=$l+1)
{
  $letra=substr($pass_usuario,$l-1,1);
  if (ord($letra)<48) {
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
  if (ord($letra)>57 && ord($letra)<64) {
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
  if (ord($letra)>90 && ord($letra)<97) {
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
  if (ord($letra)>122){
                      $errores=$errores."- La contraseña introducida no es válida, no puede contener espacios ni signos de puntuación.<br>";
                      break;
                      }
}

if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario)));
        
        if (db_num_rows($busca_usuario)==0) {
                $errores=$errores."El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                
                if ($datos_usuario["password_webcam"]!=$pass_usuario) {
                        $errores=$errores."La contraseña introducida no es correcta.<br>";
                        }
                
                }

        }
        
        


    if ($errores!="") {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="shortcut icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  include("estilos.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>

</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div class="container-fluid">
  <div class="row"> <!-- Encabezado -->
  	<div class="col-md-2"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" border=0></div>
  	<div class="col-md-10"></div>
  </div>
  <div class="row"> <!-- Principal -->
    
<div class="panel panel-default">
  <div class="panel-body text-center">

<p class="text-center"><h3>Acceso al área privada</h3><br />
<span class="text-danger">Se han producido los siguientes errores.</span></font></p>
<p><big><?=$errores?></big>
</p>
Vuelve a introducir tus datos de acceso.</p>
<br />
<form class="form-inline" method="POST" action="acceso.php">
  <div class="form-group">
    <label for="user">Usuario</label>
    <input type="text" class="form-control" id="user" name="user" placeholder="Nombre de usuario">
  </div>
  <div class="form-group">
    <label for="pass">Contraseña</label>
    <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña">
  </div>
  <button type="submit" class="btn btn-responsive btn-default"><i class="fa fa-sign-in"></i> Entrar</button>
</form>
 
</div>
</div>

<?php  require("../configuracion/parte_inferior_padres.php"); ?>
    <?php 
    } else {
    // Iniciamos la sesión del padre
    $id_usuario=$datos_usuario["id_usuario"];
    $id_hijo=$datos_usuario["padre_de"];

    // Busca datos del padre.
    $busca_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario)) or die(db_error()."- No se encuentran los datos del padre.");

    $datos_padre=db_fetch_array($busca_datos_padre);
    
    session_cache_limiter('nocache,private');
    $_SESSION['nombre_padre']=$nombre_usuario;
    $_SESSION['permitido_padre']=true;
    $_SESSION['id_padre']=$id_usuario;
    $_SESSION['id_hijo']=$id_hijo;
	  $_SESSION["mostrar_felicitacion"]=0;
	
    //Encriptamos la contraseña
    $password1 = md5($pass_usuario);

    $_SESSION['password_padre']=$password1;

    // estádisticas de sesion
    db_query_params('INSERT INTO control_acceso_padres (sesion, id_padre, fecha, hora, remote_host) VALUES (?, ?, ?, ?, ?)', 'sssss', array((string)(session_id()), (string)($id_usuario), (string)(date("Y/m/d")), (string)(date("H:i")), (string)($HTTP_SERVER_VARS["REMOTE_ADDR"]))) or die(db_error());
    
      if (strlen($redirect)==0) {
        header("Location: index.php");
      } else {
        header("Location: ".$redirect);        
      }
    
    }
    ?>
