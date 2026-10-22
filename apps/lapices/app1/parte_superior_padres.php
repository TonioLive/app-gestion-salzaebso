<?php  require("../configuracion/autentifica_padre.php");

$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
        $nombre_pagina_actual="menu";
        }

if (strlen($_GET["id_curso"])!=0) {
$id_curso_actual=$_GET["id_curso"];
} else {

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<?php  include("estilos.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<SCRIPT LANGUAGE="JavaScript" SRC="/webcam/js/popups.js"></SCRIPT>
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
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" class="tabla_principal" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1">
  <tr>
    <td align="center">
        <img border="0" src="/webcam/imagenes/cabecera_<?=$nombre_pagina_actual?>.png">
    </td>
  </tr>
  <tr>
    <td width="<?= $ancho_pagina_total; ?>" valign="top" align="center">
        <div align="center">
<?php 
$nombre_usuario=$_SESSION['nombre_padre'];

$version_js=$_GET["version_js"];

if (strlen($version_js)!=0) {
        $_SESSION['version_js']=$version_js;
        } else {
        $version_js=$_SESSION['version_js'];
        }
if (strlen($version_js)==0) {
        $version_js==0;
        }

$errores="";

if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'");
        
        if (db_num_rows($busca_usuario)==0) {
                $errores="El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                // Mostramos la información del niño

                $id_usuario=$datos_usuario["id_usuario"];
                $id_hijo=$datos_usuario["padre_de"];

                // Busca datos del padre.
                $busca_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario") or die(db_error()."- No se encuentran los datos del padre.");

                $datos_padre=db_fetch_array($busca_datos_padre);
                if ($datos_padre["sexo"]=="V") {
                        $sufijo_padre="o";
                        } else {
                        $sufijo_padre="a";
                        }

                // Comprueba si tiene email
                $no_email=false;
                if (strlen($datos_padre["email"])==0) {
                        $no_email=true;
                        $errores="-No disponemos de tu dirección de Email.<br>";
                        } else {
                        // y si el mail es correcto
                        $email_padre=$datos_padre["email"];
                        if ((strpos($email_padre,"@") ? strpos($email_padre,"@")+1 : 0)==0 || (strpos($email_padre," ") ? strpos($email_padre," ")+1 : 0)!=0 || (strpos($email_padre,".") ? strpos($email_padre,".")+1 : 0)==0)
                                {
                                $no_email=true;
                                $errores="- El E-Mail que tenemos parece ser incorrecto, falta la arroba (@) o contiene caracteres no permitidos (espacios, acentos, etc.).<br>";
                                }
                        }
                // Comprueba DNI
                $dni_padre=$datos_padre["DNI"];
                if (strlen($dni_padre)==0)
                                {
                                $errores="- No disponemos de tu nº de DNI o NIE.";
                                }
                // Comprueba telefono
                $telefono_padre=$datos_padre["telefono1"].$datos_padre["telefono2"].$datos_padre["movil"];
                if (strlen($telefono_padre)==0)
                                {
                                $errores="- No disponemos de ningún teléfono de contacto.";
                                }
                // Comprueba domicilio
                $domicilio_padre=$datos_padre["Direccion"];
                if (strlen($domicilio_padre)==0)
                                {
                                $errores="- No disponemos de tu domicilio.";
                                }
                // Comprueba cp
                $codigo_postal_padre=$datos_padre["cp"];
                if (strlen($codigo_postal_padre)==0)
                                {
                                $errores="- No disponemos de tu código postal.";
                                }
                }

        }
        
if ($errores!="") {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";
    ?><font size=1>
    Hemos detectado que no disponemos de algunos de tus datos.<br><br>
    Si no quieres introducirlos ahora, puedes ir a escuela, ponerte en contacto con la coordinadora y facilitarselos.<br><br>
    No podrás acceder a las cámaras hasta que no dispongamos de la información, es importante para la seguridad tener identificadas a todas las personas que acceden a las imágenes.<br><br>
    Introduce en el siguiente formulario los datos que faltan. Una vez introducidos, lee las condiciones generales y si estás de acuerdo selecciona la opción <i>"He leído las condiciones y las acepto"</i> y pulsa el botón <b>Siguiente >></b>.<br><br></font>
    <table width=70% border=0 cellpadding=0 cellspacing=0>
    <tr valign="top">
<form method="POST" action="intro_email.php" name="direccion_email">
    <td colspan=2 align="center">
        <table border=0 cellpadding=2 cellspacing=0>
        <?php 
        if ($no_email==true) {
        ?>
        <tr valign="top">
        <td align="right"><font size=2><b>Dirección de E-Mail:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="text" name="email" size="40" align="right" value="<?=$email_padre;?>"></font></td>
        </tr>
        <?php 
        }
        if (strlen($dni_padre)==0) {
        ?>
        <tr valign="top">
        <td align="right"><font size=2><b>DNI:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="text" name="dni" size="20" align="right" value="<?=$dni_padre;?>"></font></td>
        </tr>
        <?php 
        }
        if (strlen($telefono_padre)==0) {
        ?>
        <tr valign="top">
        <td align="right"><font size=2><b>Teléfono de contacto:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="text" name="telefono" size="20" align="right" value="<?=$telefono_padre;?>"></font></td>
        </tr>
        <?php 
        }
        if (strlen($domicilio_padre)==0) {
        ?>
        <tr valign="top">
        <td align="right"><font size=2><b>Domicilio:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="text" name="domicilio" size="50" align="right" value="<?=$domicilio_padre;?>"></font></td>
        </tr>
        <?php 
        }
        if (strlen($codigo_postal_padre)==0) {
        ?>
        <tr valign="top">
        <td align="right"><font size=2><b>Código postal:&nbsp;</b></font></td>
        <td align="lect"><font size=2><input type="text" name="codigo_postal" size="10" align="right" value="<?=$codigo_postal_padre;?>"></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
     </td>
</form>
     </tr>
</table>
                                                                <br>
                                                                Condiciones generales<br>
                                                                </font></b>
   <textarea cols="100" rows="5" style="font-size: 11px; font-family: Arial" readonly>ZAEBSAL, s.l.

Sobre la protección de la confidencialidad de los datos

DATOS SOLICITADOS Y TRATAMIENTO

Nuestra web permite poder navegar sin dar información personal y permanecer
en el anonimato durante la visita del usuario.

Si el usuario decide enviar su información en nuestra web, se solicitarán los datos
estrictamente necesarios para la consecución del fin a qué está destinada
nuestra web site, que no es otro que el de facilitar información sobre las actividades de la empresa.

A tales efectos se requerirá a los interesados el envío de su información de contacto, lo
cual conlleva facilitar datos de carácter personal tales como: nombre,
dirección, teléfono, e-mail, etc.
Su tratamiento estará enfocado únicamente a la consecución de este fin, siempre
dentro del marco normativo establecido.

Mediante el envío de los datos el usuario da su consentimiento al tratamiento
de los mismos, así como su cesión a terceros (con los condicionantes que se
comentarán en el apartado siguiente).

DERECHO DE OPOSICIÓN, CORRECCIÓN Y ACTUALIZACIÓN DE DATOS

El usuario tiene derecho a acceder a esta información, a rectificarla si los
datos son erróneos y a darse de baja del fichero. En este último caso ZAEBSAL, s.l.
pierde la información automáticamente.

Estos derechos pueden hacerse efectivos mediante el envío de un E-Mail a la dirección: info@escuelasinfantileslapices.es
 o bien acudiendo a cualquiera de las escuelas infantiles gestionadas por la empresa.

En caso de cualquier tipo de duda o controversia respecto a nuestra política de
privacidad de datos se podrán remitir un e-mail a info@escuelasinfantileslapices.es.

Nota: El tratamiento de los datos personales está ajustado a la normativa
establecida en la Ley Orgánica 15/1999,13 de diciembre, de Protección de Datos
de Carácter Personal ( B.O.E 14/12/1999).

-----------------------------------------------
ZAEBSAL, s.l. C/ Río Nervion, 21-23 - 46025 Valencia - B-97324123</textarea>
                                                                <form name="comprobar">
                                                                <font face="Arial" size="1"><input type="radio" name="aceptacion" value="SI"> He
                                                                leído las condiciones y las acepto <input type="radio" name="aceptacion" value="NO"> No acepto las condiciones<br><br>
                                                                <input type="button" name="cancelar" value="Cancelar" style="font-size: 10px" onclick="history.go(-1)"> <input type="button" onclick="comprobar_aceptacion()" value="Siguiente >>" style="font-size: 10px">
                                                                <br>
                                                                </form>
                                                                Cumplimentar el formulario no le compromete a
                                                                nada.<br>
                                                                Sus datos serán utilizados para facilitarte
                                                                información relacionada<br>
                                                                con las actividades ZAEBSAL, s.l.<br>
                                                                relacionadas con la formación de su hijo/a.</font></p>

<SCRIPT LANGUAGE="Javascript">
<!--

function emailCheck (emailStr) {

var emailPat=/^(.+)@(.+)$/
var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]"
var validChars="\[^\\s" + specialChars + "\]"
var quotedUser="(\"[^\"]*\")"
var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/
var atom=validChars + '+'
var word="(" + atom + "|" + quotedUser + ")"
var userPat=new RegExp("^" + word + "(\\." + word + ")*$")
var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$")

var matchArray=emailStr.match(emailPat)
if (matchArray==null) {

        var errStr="El Email no parece ser correcto (revisa la arroba (@) o los puntos (.))"
        return errStr
}
var user=matchArray[1]
var domain=matchArray[2]

if (user.match(userPat)==null) {
    var errStr="El nombre de usuario no parece ser válido."
    return errStr
}

var IPArray=domain.match(ipDomainPat)
if (IPArray!=null) {
          for (var i=1;i<=4;i++) {
            if (IPArray[i]>255) {
                var errStr="La dirección IP de destino no es válida."
                return errStr
            }
    }
    return true
}

var domainArray=domain.match(domainPat)
if (domainArray==null) {
    var errStr="El nombre de dominio no parece ser válido."
    return errStr
}

var atomPat=new RegExp(atom,"g")
var domArr=domain.match(atomPat)
var len=domArr.length
if (domArr[domArr.length-1].length<2 ||
    domArr[domArr.length-1].length>3) {
   var errStr="El tipo de dominio puede tener únicamente 3 o 2 letras."
   return errStr
}

if (len<2) {
   var errStr="A la dirección de Email le falta el nombre de dominio!"
   return errStr
}

return true;
}

function comprobar_aceptacion() {
        if (top.window.document.comprobar.aceptacion[0].checked) {
        var errores="";
                
        <?php 
        if ($no_email==true) {
        ?>
        if (top.window.document.direccion_email.email.value=="") {
                 errores=errores+"- Debe introducir una direción de E-mail.\n\n";
                 } else {
                 if (emailCheck(top.window.document.direccion_email.email.value)!=true) {
                        errores=errores+"- "+emailCheck(top.window.document.direccion_email.email.value)+"\n\n";
                        }
                 }
        <?php 
        }
        if (strlen($dni_padre)==0) {
        ?>
        if (top.window.document.direccion_email.dni.value=="") {
                 errores=errores+"- Debe introducir un DNI, NIE o nº de documento de identificación.\n\n";
                 }
        <?php 
        }
        if (strlen($telefono_padre)==0) {
        ?>
        if (top.window.document.direccion_email.telefono.value=="") {
                 errores=errores+"- Debe introducir nº de teléfono de contacto.\n\n";
                 }
        <?php 
        }
        if (strlen($domicilio_padre)==0) {
        ?>
        if (top.window.document.direccion_email.domicilio.value=="") {
                 errores=errores+"- Falta el domicilio.\n\n";
                 }
        <?php 
        }
        if (strlen($codigo_postal_padre)==0) {
        ?>
        if (top.window.document.direccion_email.codigo_postal.value=="") {
                 errores=errores+"- No se ha indicado el código postal.\n\n";
                 }
        <?php 
        }
        ?>
                
                if (errores=="") {
                        window.document.direccion_email.submit();
                        } else {
                        alert("Se han detectado los siguientes errores:\n\n"+errores);
                        }
                } else {
                if (confirm("No ha aceptado las condiciones, ¿desea volver a la página de inicio?")) {
                        window.location.href="http://www.escuelas-infantiles.com";
                        }
                }
        }

//-->
</script>
<?php 
    } 
?>
