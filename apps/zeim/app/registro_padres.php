<?php

{
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
<form method="POST" action="intro_email.php" name="direccion_email"><?php if(function_exists("app_csrf_token")){ ?><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><?php } ?>
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

Estos derechos pueden hacerse efectivos mediante el envío de un E-Mail a la dirección: zeim@escuelas-infantiles.com
 o bien acudiendo a cualquiera de las escuelas infantiles gestionadas por la empresa.

En caso de cualquier tipo de duda o controversia respecto a nuestra política de
privacidad de datos se podrán remitir un e-mail a info@escuelasinfantileslapices.es.

Nota: El tratamiento de los datos personales está ajustado a la normativa
establecida en la Ley Orgánica 15/1999,13 de diciembre, de Protección de Datos
de Carácter Personal ( B.O.E 14/12/1999).

-----------------------------------------------
ZAEBSAL, s.l. C/ Alfauir, 15 - 46018 Valencia - B-97324123</textarea>
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
                                                                con las actividades ZABESAL, s.l.<br>
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