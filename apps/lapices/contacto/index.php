<?php 
require("../configuracion/config.php");

$centro_enviado=$_GET["idc"];
if (strlen($centro_enviado)==0) {
	$centro_enviado=6;
}
$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$centro_enviado");
if (db_num_rows($busca_datos_centro)!=0) {
		$datos_centro=db_fetch_array($busca_datos_centro);
		$nombre_centro=$datos_centro["nombre_centro"];
		$poblacion_centro=$datos_centro["poblacion_centro"];
		$tipo_centro=$datos_centro["tipo_centro"];
}

$id_curso_contacto = $_GET["id_curso"];
if (strlen($id_curso_contacto)==0) {
	$id_curso_contacto=32;
}

$formulario_name="contactos";
$formulario_accion="/contacto/contacto1.php";

$id_contacto=$_GET["id_contacto"];

$no_publicidad=0;
$consentimiento_publicidad=1;
$consentimiento_web=1;
$consentimiento_blog=1;
$consentimiento_redes=1;
$informado_tratamiento_datos=1;

$mensajeNuevosAlumnos="<div class='text-center text-danger'>¡ATENCIÓN! Este formulario es solo para la inscripción de nuevos alumnos.<br />Si el/la alumno/a ya está matriculado/a en nuestra escuela infantil, por favor, no utilice este formulario.<br />Cuando esté finalizando el curso actual, se activará en el área privada un enlace específico para la renovación de matrícula. Gracias.</div>";

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$nombre_centro;?> - Solicitud de información</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/chosen.css">
<link rel="stylesheet" href="/css/datepicker.css">
<style type="text/css">
.myField{
    width:32%;
    display:inline-block;

	}
.myField75{
    width:50%;
    display:inline-block;
	}
</style>	


<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script src="/js/bootstrap-datepicker.js"></script>
<script src="/js/locales/bootstrap-datepicker.es.js"></script>
<script src="/inc/validacion_fechas.js" type="text/javascript"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('.datepicker').datepicker({
    	format: 'dd/mm/yyyy',
    	language: 'es',
    	weekStart: 1,
    	startView: 2
	})
});	

function onSubmit(token) {

				var errores=0;

                var mensajes_error="";
                
                if (!$('#informado_tratamiento_datos').is(':checked')) {
	                
					mensajes_error+='- Si no da su consentimiento para el tratamiento de los datos no podrá realizar la inscripción. Para dar su consentimiento marque la casilla "Quedo informado de..."\u000A';
	            	errores++;    
	                
                }
				
                if ((self.document.<?=$formulario_name;?>.nombre_c1.value=="")&&(self.document.<?=$formulario_name;?>.apellido1.value=="")) { mensajes_error=mensajes_error+"Debe introducir el nombre y al menos el primer apellido del/la alumno/a.\u000A";errores=1; }

                if ((self.document.<?=$formulario_name;?>.nombre_c1.value=="")&&(self.document.<?=$formulario_name;?>.apellido1_c1.value=="")) { mensajes_error=mensajes_error+"Debe introducir el nombre y al menos el primer apellido de una persona de contacto (padre, madre, etc).\u000A";errores=1; }
                
                // Fecha nacimiento
                if (!$('#fecha_nacimiento').is(":checked")) {
                	if (self.document.<?=$formulario_name;?>.fecha_nacimiento.value=="") {
                		mensajes_error=mensajes_error+"Debe introducir la fecha de nacimiento del/la alumno/a.\u000A";
                		errores=1;	                	
                	}
                }
                
                // Telefonos
                if (self.document.<?=$formulario_name;?>.telefono_padre1.value=="") { mensajes_error=mensajes_error+"Es necesario introducir, al menos, un teléfono de contacto.\u000A";errores=1; }
             
                // emails
                if (self.document.<?=$formulario_name;?>.email_padre1.value=="") { mensajes_error=mensajes_error+"Es necesario introducir, al menos, una dirección de email de contacto.\u000A";errores=1; }
                                      				                
                               
                if (errores==0) {
						document.getElementById("<?=$formulario_name;?>").submit();
                        } else {
                        alert("- Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }                        
        }


function fn_ValidateIBAN(IBAN) {

    //Se pasa a Mayusculas
    IBAN = IBAN.toUpperCase();
    //Se quita los blancos de principio y final.
    IBAN = IBAN.trim();
    IBAN = IBAN.replace(/\s/g, ""); //Y se quita los espacios en blanco dentro de la cadena

    var letra1,letra2,num1,num2;
    var isbanaux;
    var numeroSustitucion;
    //La longitud debe ser siempre de 24 caracteres
    if (IBAN.length != 24) {
        return false;
    }

    // Se coge las primeras dos letras y se pasan a números
    letra1 = IBAN.substring(0, 1);
    letra2 = IBAN.substring(1, 2);
    num1 = getnumIBAN(letra1);
    num2 = getnumIBAN(letra2);
    //Se sustituye las letras por números.
    isbanaux = String(num1) + String(num2) + IBAN.substring(2);
    // Se mueve los 6 primeros caracteres al final de la cadena.
    isbanaux = isbanaux.substring(6) + isbanaux.substring(0,6);

    //Se calcula el resto, llamando a la función modulo97, definida más abajo
    resto = modulo97(isbanaux);
    if (resto == 1){
        return true;
    }else{
        return false;
    }
}

function modulo97(iban) {
    var parts = Math.ceil(iban.length/7);
    var remainer = "";

    for (var i = 1; i <= parts; i++) {
        remainer = String(parseFloat(remainer+iban.substr((i-1)*7, 7))%97);
    }

    return remainer;
}

function getnumIBAN(letra) {
    ls_letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return ls_letras.search(letra) + 10;
}

</script>
</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><h3>Escuela Infantil <?=$nombre_centro;?> - Solicitud de información</h3></div>
  		<div class="panel-body">
		<form name="<?=$formulario_name;?>" id="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
		<input type="hidden" name="id_centro" value="<?=$centro_enviado;?>">
		<input type="hidden" name="id_contacto" value="<?=$id_contacto;?>">
		<input type="hidden" name="id_usuario_alumno" value="<?=$id_alumno;?>">
		<input type="hidden" name="id_usuario_padre1" value="<?=$id_usuario;?>">
		<input type="hidden" name="id_usuario_padre2" value="<?=$id_usuario_padre2;?>">
		<input type="hidden" name="tipo_alumno" value="0">
		<div class="row">
			<div class="col-md-12">
				<?=$mensajeNuevosAlumnos;?>
			<div class="form-group">
		    	<label for="id_curso_contacto">Curso por el que se interesa:</label>
		    	<select id="id_curso_contacto" name="id_curso_contacto" class="form-control">
                        <?php 
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE fecha_fin>'".date("Y-m-d")."' ORDER BY fecha_inicio") or die(db_error()."-4");
                        while($cursos=db_fetch_array($consulta_cursos)){
                        
                        $texto_finaliza="";
                        
                        if (strtotime($cursos["fecha_inicio"])<=time()) {
                        
                        	$texto_finaliza=" (Ya iniciado, finaliza el día ".date("d/m/Y",strtotime($cursos["fecha_fin"])).")";
                        	
                        	} else {
                        	
                        	$texto_finaliza=" (Se inicia el día ".date("d/m/Y",strtotime($cursos["fecha_inicio"])).")";
                        	
                        	}
                        
                        
                        ?>
                        <option value="<?=$cursos["id_curso"];?>"<?php  if ($cursos["id_curso"]==$id_curso_contacto) { print " selected"; } ?>><?=$cursos["nombre"];?><?=$texto_finaliza?></option>
                        <?php 
                        }
                        ?>
            	</select>
			</div>
			<h3>Datos del alumno</h3>
			<div class="form-group">
		    	<label for="id_curso_contacto">Nombre y apellidos del alumno:</label>
				<br />		    
		    	<input type="text" name="nombre" id="nombre" value="<?=$nombre_alu?>" class="form-control myField" placeholder="Nombre"> <input type="text" name="apellido1" id="apellido1" value="<?=$apellido1_alu?>" class="form-control myField" placeholder="Primer Apellido"> <input type="text" name="apellido2" id="apellido2" value="<?=$apellido2_alu?>" class="form-control myField" placeholder="Segundo Apellido">		    
			</div>
			<div class="form-group">
		    	<label for="sexo">Género del alumno:</label>
				<br />		    
		    	<input type="radio" name="sexo" value="V"<?php  if ($sexo=="V") { print " checked"; } ?>> Masculino&nbsp; &nbsp;<input type="radio" name="sexo" value="H"<?php  if ($sexo=="H") { print " checked"; } ?>> Femenino 		    
			</div>
		</div>
		<div row>
			<div class="col-md-12">
				<div class="form-group">
		    		<label for="fecha_nacimiento">Fecha de nacimiento:</label>
		    		<br />	 
		    		<input name="fecha_nacimiento" id="fecha_nacimiento" value="<?=$fecha_nacimiento?>" class="form-control myField datepicker"> <input type="checkbox" name="nonato" value="1"<?php  if ($usuario_nonato=="1") { print " checked"; } ?>> Todavía no ha nacido  	    
				</div>
				</div>
			</div>						
		</div>		
		<div class="row">
			<div class="col-md-12">	
			<h3>Datos del contacto</h3>
			<div class="form-group">
		    	<label for="id_curso_contacto">Nombre y apellidos del contacto:</label>
				<br />		    
		    	<input type="text" name="nombre_c1" id="nombre_c1" value="<?=$nombre_c1?>" class="form-control myField" placeholder="Nombre"> <input type="text" name="apellido1_c1" id="apellido1_c1" value="<?=$apellido1_c1?>" class="form-control myField" placeholder="Primer Apellido"> <input type="text" name="apellido2_c1" id="apellido2_c1" value="<?=$apellido2_c1?>" class="form-control myField" placeholder="Segundo Apellido">		    
			</div>
			
			<div class="form-group">
		    	<label for="persona_contacto">Relación de parentesco con el alumno:</label>
				<br />		     
		    	<select name="parentesco_padre1" size=1 class="form-control myField">
                	<option value="0"></option>
                    <option value="1"<?php  if ($parentesco_padre1==1) { print " selected"; } ?>>Padre/Madre</option>
                	<option value="2"<?php  if ($parentesco_padre1==2) { print " selected"; } ?>>Abuelo/a</option>
                	<option value="3"<?php  if ($parentesco_padre1==3) { print " selected"; } ?>>Tio/a</option>
                	<option value="4"<?php  if ($parentesco_padre1==4) { print " selected"; } ?>>Autorizado/a</option>
                </select>		    		    			    
			</div>
			<div class="form-group">
		    	<label for="telefono">Teléfono de contacto:</label>
				<br />		    
		    	<input type="text" name="telefono_padre1" id="telefono_padre1" value="<?=$telefono_padre1?>" class="form-control" placeholder="Teléfono particular de la persona de contacto">			    		    			    
			</div>	
			<div class="form-group">
		    	<label for="email">E-mail:</label>
				<br />		    
		    	<input type="text" name="email_padre1" id="email_padre1" value="<?=$email_padre1?>" class="form-control" placeholder="E-Mail de la persona de contacto">			    		    			    
			</div>
						
			<div class="form-group">
		    	<label for="comentarios">Observaciones:</label>
				<br />		    
		    	<textarea class="form-control" rows="4" name="comentarios" id="comentarios" placeholder="Comentarios"><?=$comentarios?></textarea>			    		    			    
			</div>
			
		<div>
			<label>POLÍTICA DE PRIVACIDAD</label>	
		<textarea  class="form-control" rows=7>
POLÍTICA DE PRIVACIDAD


De acuerdo con el artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y Comercio Electrónico, ponemos en su conocimiento que ZAEBSAL S.L. con CIF B97324123 y domicilio en C/ ALFAUIR Nº 5, PUERTA 10 46025 – VALENCIA es la encargada de la gestión y funcionamiento del sitio. www.escuelas-infantiles.com

Si desea contactar con nosotros puede hacerlo mediante correo postal, en la C/ ALFAUIR Nº 5, PUERTA 10 46025 - VALENCIA , o bien a través del correo electrónico. lapices@escuelas-infantiles.com

POLÍTICA DE PROTECCIÓN DE DATOS

ZAEBSAL S.L. considera esencial garantizar la protección de los datos de carácter personal de sus clientes y visitantes (en adelante, los usuarios), recabados al objeto de prestar un correcto servicio a los mismos. Por ello, con el fin de proteger la intimidad y privacidad de todos ellos, ponemos de manifiesto nuestro respeto y cumplimiento con la legislación vigente en materia de protección de datos, así la presente política de privacidad tiene como objetivo proporcionar la máxima transparencia sobre el tratamiento de sus datos por parte de ZAEBSAL S.L.

Así pues, en cumplimiento de la Normativa Europea (UE) 2016/679 del Parlamento Europeo y del Consejo de 27 de Abril de 2016 relativo a la Protección de Datos de Carácter Personal, por medio del presente documento, ZAEBSAL S.L informa:

1. RESPONSABLE DEL TRATAMIENTO

ZAEBSAL S.L , con CIF B97324123 y domicilio en C/ ALFAUIR Nº 5, PUERTA 10 46025 – VALENCIA es la Responsable del Tratamiento. Cuando usted solicita uno de nuestros productos, servicios o información, ZAEBSAL S.L. únicamente requiere la información esencial para poder proporcionar prestar respuesta a su solicitud.

2. FINALIDAD DEL TRATAMIENTO

Todos los datos proporcionados son revisados y examinados minuciosamente para evitar el uso fraudulento de datos personales.

La finalidad del tratamiento de los datos corresponde a cada una de las actividades de tratamiento que realiza ZAEBSAL S.L. y que están accesibles en el registro de actividades de tratamiento.

3. CESIÓN DE DATOS A TERCEROS

Contamos con la colaboración de terceros, proveedores de servicios, que pueden tener acceso a los datos personales como consecuencia de la ejecución de los servicios contratados o productos suministrados.

En cualquier caso, seguimos unos criterios estrictos de selección de dichos terceros con el fin de dar cumplimiento a sus obligaciones en protección de datos y firma con ellos su correspondiente convenio de protección de datos, dónde estos terceros se obligan a cumplir con sus obligaciones en protección de datos, y en concreto, a cumplir con las medidas jurídicas, técnicas y organizativas, al tratamiento de los datos personales para las finalidades pactadas, y la prohibición de tratar dichos datos personales para otras finalidades o cesión a terceros.

Además, en las ocasiones que sea necesaria por alguna obligación legal, a las autoridades y/o a los tribunales de justicia en cumplimiento de un requerimiento formal según la legislación vigente.

Puede consultar más información sobre la cesión de datos a terceros en el Registro de Actividades, apartado “Categoría de Destinatarios”.

4. CONSERVACIÓN DE LOS DATOS

Los datos serán conservados mientras exista una relación comercial, contractual o profesional con el usuario y posteriormente durante los años necesarios para cumplir con las obligaciones legales correspondientes en cada caso.

Puede consultar más información sobre la conservación de datos específicos en el Registro de Actividades, en el apartado de cada actividad "Plazo de Supresión".

5. LEGITIMACIÓN PARA EL TRATAMIENTO DE LOS DATOS

El uso de sus datos en las condiciones anteriormente descritas está permitido por la normativa de protección de datos europea y española de conformidad con las siguientes bases jurídicas:

- Usted ha manifestado su consentimiento (se le ha presentado un formulario de consentimiento para que autorice el tratamiento de sus datos con determinadas finalidades; usted puede revocar el consentimiento prestado en todo momento)

- El tratamiento de sus datos es necesario para la gestión y mantenimiento de un contrato suscrito con usted

- El tratamiento de sus datos es necesario para cumplir con nuestras obligaciones legales

Puede consultar más información sobre la legitimación para el tratamiento de categorías especiales de datos personales (art. 9 RGPD) en el Registro de Actividades, apartado “Legitimación del tratamiento”.

6. TRATAMIENTO DE LAS IMÁGENES

De conformidad con la legislación del Menor, ZAEBSAL S.L. informa que durante el transcurso del viaje, se realizaran fotografías y/o videos y cuya finalidad es mostrar el desarrollo de las diferentes actividades contratadas. Estas imágenes podrían ser comunicadas por distintas vías, como por ejemplo, la página web www.escuelas-infantiles.com o en redes sociales.

El tratamiento de las imágenes se efectúa con serio respeto hacia la persona, de conformidad con la Ley Orgánica 1/1982, de 5 de mayo, de protección civil del derecho al honor, a la intimidad personal y familiar, y a la propia imagen, eliminando cualquier captación o filmación que pudiera atentar estos derechos fundamentales, no siendo las imágenes utilizadas para otros fines distintos a los expresados.

En relación con lo anterior, ZAEBSAL S.L. tratará las imágenes con la finalidad descrita anteriormente, incluyéndose en sistemas de tratamiento propios de la Empresa y cumpliendo con las medidas de seguridad, técnica y organizativa, de conformidad al Nuevo Reglamento general de protección de Datos (UE) 2016/679.

En todo caso, el Padre y/o Madre, o representante legal del menor (en caso de que lo fuera) podrá revocar el consentimiento u oponerse al tratamiento de la imagen, o ejercitar los derechos de acceso, rectificación, limitación del tratamiento, supresión, portabilidad y supresión al tratamiento, enviando una solicitud por escrito, acompañada de una fotocopia de su DNI dirigida a la dirección C/ ALFAUIR Nº 5, PUERTA 10 46025 – VALENCIA

7. REDES SOCIALES

ZAEBSAL S.L. tiene presencia en distintos medios o redes sociales, tales como: Facebook e Instagram, siendo la finalidad del tratamiento de datos personales las establecidas dentro de las condiciones afectas al servicio. En el caso que el registro a determinados servicios se efectuare mediante datos personales asociados a una cuenta de usuario, se informa que compartirá determinada información contenida en su cuenta. ZAEBSAL S.L. recuerda que debe conocer las políticas de privacidad de dichos medios o redes sociales en los que esté registrado en aras a evitar compartir información no deseada.

Dispone de las configuraciones de privacidad y gestión de la cuenta en las redes sociales para gestionar las preferencias de privacidad, identidad, publicidad y demás extremos afectos.

Detallando cada una de las redes sociales, en Facebook, cuando un usuario se hace fan de la de la Fan Page oficial a través del botón “Me gusta” autoriza que sus datos personales se utilicen únicamente en esta plataforma de Facebook para la gestión de la “Fan Page” y de las comunicaciones que se mantengan de forma bidireccional con dichos seguidores a través de chat, publicaciones, comentarios, mensajes u otros medios de comunicación que permita la red social ahora y en el futuro. Este tratamiento estará sujeto a las políticas de privacidad de esta red social y que el usuario puede consultar en el siguiente enlace: https://www.facebook.com/policy.php . Al hacerse fan se tendrá acceso al listado de miembros o seguidores que se han unido a la Fan Page. También se informa que cuando un usuario se hace fan, las noticias que se publiquen aparecerán también en su página de inicio y que si el usuario fan hace comentarios de estas publicaciones, será accesible por los demás fans tanto su comentario como el nombre de su perfil y, en su caso, la fotografía que tenga en el mismo o según su configuración de privacidad, o biografía y etiquetado. En todo caso, es responsabilidad del usuario el uso que haga de la red social.

En relación a Instagram, cuando un usuario sigue nuestro perfil pinchando el botón “Seguir”, se tendrá acceso a la página de perfil de las personas que lo siguen, concretamente, el nombre de usuario, fotografías (en caso de que el usuario haya puesto una fotografía en el perfil), y publicaciones realizadas por el usuario, así como al resto de la información que se haya publicado en el perfil del usuario como por ejemplo usuarios a los que se sigue o sus seguidores. Los datos de los usuarios que siguen en Instagram a nuestro perfil se utilizan únicamente para gestionar la información que se intercambian entre ambos. La política de privacidad que rige en esta red social se puede consultar en el siguiente enlace: https://help.instagram.com/519522125107875

7. COOKIES

Toda la información relevante sobre el uso de cookies está recogida en nuestra Política de Cookies

8. TRANSPARENCIA

Todos los datos facilitados por usted, son utilizados para proporcionarle el mayor nivel de servicio, igualmente el objetivo de cualquier análisis de datos es la mejora de nuestros servicios, productos o información manteniendo nuestro compromiso de cumplimiento con la legislación vigente sobre protección de datos.

9. DERECHO DE LOS INTERESADOS

Cualquier interesado tiene el derecho a:

Derecho de información
	

Tienes derecho a ser informado de forma clara ANTES de que tus datos sean recogidos sobre qué datos tuyos están siendo tratados, con qué finalidad se tratan, donde han conseguido los datos y si los van a comunicar o los han comunicado a alguien

Derecho de acceso
	

Para conocer qué datos tuyos están siendo tratados, con qué finalidad se tratan, donde han conseguido los datos y si los van a comunicar o los han comunicado a alguien

Derecho de rectificación
	

Para modificar aquellos datos tuyos inexactos o incompletos

Derecho de cancelación
	

Para cancelar tus datos inadecuados o excesivos

Derecho de oposición
	

Para evitar que se traten tus datos o que dejen de tratarse aunque sólo en los supuestos que establece la ley

Derecho de limitación del tratamiento
	

Para solicitar que se suspenda el tratamiento de datos en los supuestos que establece la ley

Derecho a la portabilidad de los datos
	

Para poder recibir tus datos facilitados en un formato electrónico estructurado y de uso habitual y poder transmitirlos a otro responsable

Derecho a no ser objeto de decisiones individualizadas
	

Con el fin de que no se tome una decisión sobre ti que produzca efectos jurídicos o te afecte basada sólo en el tratamiento de tus datos

Si desea ejercer cualquiera de estos derechos, por favor póngase en contacto con nosotros en:

ZAEBSAL S.L.
AVDA. ALFAUIR Nº 5 PUERTA 10, 46025 – VALENCIA lapices@escuelas-infantiles.com

Los datos del Delegado de Protección de Datos (DPO) son los siguientes:

Nombre: MARIA DOLORES HERREROS TABANES

Email de Contacto: dpd@escuelas-infantiles.com

AUTORIDAD DE CONTROL

Si desea plantear una reclamación en relación al tratamiento de sus datos por parte de EL ZAEBSAL S.L., le informamos que puede ponerse en contacto con la Agencia Española de Protección de datos, C/ Jorge Juan, 6 28001-Madrid http://www.agpd.es

10. LEGISLACIÓN APLICABLE Y TRIBUNALES COMPETENTES
Los términos y condiciones que rigen este website, así como las relaciones que pudieran derivarse están protegidas y quedan sujetas a la legislación española. Para la resolución de cualquier tipo de controversia, litigio o discrepancia que pudiera suscitarse entre el usuario y ZAEBSAL S.L. por el uso de este sitio web, se acuerda el sometimiento de las mismas a los Juzgados y Tribunales de Valencia España.
</textarea>			
		</div>
		<div class="form-group">
		    	<label for="horarios_quiere_asistir">Autorizo a Zaebsal, s.l. a ofrecerme productos y servicios relacionados con los solicitados, envío de publicidad u ofertas comerciales de mi interés</label><br />
		    	<input type="radio" id="consentimiento_publicidad" name="consentimiento_publicidad" value="1"<?php  if ($consentimiento_publicidad==1) { print " checked"; } ?>> Si<br/>
		    	<input type="radio" id="consentimiento_publicidad" name="consentimiento_publicidad" value="0"<?php  if ($consentimiento_publicidad==0) { print " checked"; } ?>> No<br/>
		</div>	
	
		<div class="form-group">
		    	<input type="checkbox" id="informado_tratamiento_datos" name="informado_tratamiento_datos" value="1"<?php  if ($informado_tratamiento_datos==1) { print " checked"; } ?>> Quedo Informado en todo lo relacionado con el tratamiento de datos realizado por Zaebsal, s.l. y doy mi autorización para el tratamiento de los mismos.<br/>
		</div>			
	
		<button type="button" class="btn btn-primary btn-lg g-recaptcha" 
        		data-sitekey="6LcM5OQpAAAAAAvAHz2Y2Sn-3MgT-Zws-H7kmimR" 
        		data-callback='onSubmit' 
        		data-action='submit'>Enviar la solicitud</button>			
			<a type="button" href="https://escuelas-infantiles.com" class="btn btn-danger btn-lg">Cancelar</a>
			<br />
		</div>
		</div>
		</div> <!-- final formulario -->
	    </form>	    
		</div>
	</div>    
</div>
</body>
</html>