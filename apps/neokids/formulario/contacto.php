<?php 
require("../configuracion/config.php");

$formulario_name="contactos";
$formulario_accion="contacto1.php";

$codigo_uso_unico=$_GET["unid"];

if (strlen($codigo_uso_unico)!=0) {

	// Busca datos usuario
	
	$busca_datos_contacto=db_query_params('SELECT * FROM contactos WHERE codigo_uso_unico=?', 's', array((string)($codigo_uso_unico)));

	if (db_num_rows($busca_datos_contacto)!=0) {
	
		$datos_contacto=db_fetch_array($busca_datos_contacto);
		
		$id_contacto=$datos_contacto["id_contacto"];
		
		// Borra codigo de uso único
		$borra_codigo=db_query_params('UPDATE contactos SET codigo_uso_unico=\'\', fecha_uso_codigo=? WHERE id_contacto=?', 'ss', array((string)(date("Y-m-d")), $id_contacto));
		
		$id_curso_contacto=$datos_contacto["id_curso"];				
		$como_conocio=$datos_contacto["como_conocio"];
		
		$horarios_quiere_asistir=$datos_contacto["horarios_quiere_asistir"];
		$dias_quiere_asistir=$datos_contacto["dias_quiere_asistir"];
		
		$nombre_alu=$datos_contacto["Nombre"];
        $apellido1_alu=$datos_contacto["Apellido1"];
        $apellido2_alu=$datos_contacto["Apellido2"];
        $sexo=$datos_contacto["sexo"];
        $fecha_nacimiento=date("d/m/Y",strtotime($datos_contacto["fecha_nacimiento"]));
        $usuario_nonato=$datos_contacto["nonato"];
        $antiguo_alumno_lapices=$datos_contacto["antiguo_alumno_lapices"]; 
               
        $persona_contacto=$datos_contacto["persona_contacto"];
		$telefono=$datos_contacto["telefono"];
        $email=$datos_contacto["email"];
		$dni_padre1=$datos_contacto["dni_padre1"];
		$nombre_padre2=$datos_contacto["nombre_padre2"];
		$telefono_padre2=$datos_contacto["telefono_padre2"];
		$email_padre2=$datos_contacto["email_padre2"];
		$dni_padre2=$datos_contacto["dni_padre2"];
		
		$parentesco_padre1=$datos_contacto["parentesco_padre1"];
		$parentesco_padre2=$datos_contacto["parentesco_padre2"];		
		
		$calle=$datos_contacto["calle"];
        $poblacion=$datos_contacto["poblacion"];
        $codigo_postal=$datos_contacto["codigo_postal"];
        
		$telefonos_familia=$datos_contacto["telefonos_familia"];
		$email_familia=$datos_contacto["email_familia"];
				
		$contacto_emergencia1=$datos_contacto["contacto_emergencia1"];
		$telefono_contacto_emergencia1=$datos_contacto["telefono_contacto_emergencia1"];
		$contacto_emergencia2=$datos_contacto["contacto_emergencia2"];
		$telefono_contacto_emergencia2=$datos_contacto["telefono_contacto_emergencia2"];
				
		$persona_autorizada=$datos_contacto["persona_autorizada"];
				
		$semanas_embarazo=$datos_contacto["semanas_embarazo"];  
		
		$comentarios=$datos_contacto["comentarios"];
		
		$no_publicidad=$datos_contacto["no_publicidad"];      
		}

	}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>La piscina de Neokids - Nuevo Contacto</title>

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
<script type="text/javascript">
$( document ).ready(function() {
    $('.datepicker').datepicker({
    	format: 'dd/mm/yyyy',
    	language: 'es',
    	weekStart: 1,
    	startView: 2
	})
});	

function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";
                
                if (!$('#informado_tratamiento_datos').is(':checked')) {
	                
					mensajes_error+='Si no da su consentimiento para el tratamiento de los datos no podrá realizar la inscripción. Para dar su consentimiento marque la casilla "Quedo informado de..."\u000A';
	            	errores++;    
	                
                }
                
                if (self.document.<?=$formulario_name;?>.persona_contacto.value=="") { mensajes_error=mensajes_error+"Debe introducir el nombre de la persona de contacto.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono.value=="")&&(self.document.<?=$formulario_name;?>.email.value=="")) { mensajes_error=mensajes_error+"Debe introducir el teléfono o el e-mail de la persona de contacto.\u000A";errores=1; }
                               
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
                        
        }

</script>
</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" class="pull-right"><br /><br /><br /><h3>La Piscina de Neokids - Nuevo contacto Bebes e Infantil</h3></div>
  		<div class="panel-body">
		<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
		<input type="hidden" name="id_contacto" value="<?=$id_contacto;?>">
		<input type="hidden" name="tipo_alumno" value="0">
		<div class="row">
			<div class="col-md-12">
			<div class="form-group">
		    	<label for="id_curso_contacto">Curso por el que se interesa:</label>
		    	<select id="id_curso_contacto" name="id_curso_contacto" class="form-control">
                        <?php 
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_fin>? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d")))) or die(db_error()."-4");
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
			<div class="form-group">
		    	<label for="como_conocio">¿Cómo nos ha conocido?</label>
		    	<select id="como_conocio" name="como_conocio" class="form-control">
                        <?php 
                        $busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
                        while($datos_tipos=db_fetch_array($busca_tipos)) {
                        ?>
                        <option value="<?=$datos_tipos["id_tipo"]?>"<?php  if ($como_conocio==$datos_tipos["id_tipo"]) { print " selected"; } ?>><?=$datos_tipos["nombre_tipo"]?></option>
                        <?php 
                        }
                        ?>
            	</select>
			</div>	
			<?php 
			/*
			if (strlen($horarios_quiere_asistir)!=0) {
			?>
			<div class="form-group">
		    	<label for="horarios_quiere_asistir">Horario deseado:</label>
				<br />		    
		    	<textarea class="form-control" rows="2" name="horarios_quiere_asistir" id="horarios_quiere_asistir" placeholder="Horario deseado"><?=$horarios_quiere_asistir?></textarea>			    		    			    
			</div>
			<?
			} else {
			?>
			<div class="form-group">
		    	<label for="horarios_quiere_asistir">Horario deseado:</label><br />
		    	<input type="checkbox" id="horarios_quiere_asistir_1" name="horarios_quiere_asistir_1" value="De lunes a viernes"> De Lunes a Viernes (De 17:00 hs a 20:00 hs), A partir de la hora: <input type="text" name="horarios_quiere_asistir_h1" id="horarios_quiere_asistir_h1" value="" class="form-control myField" placeholder=""><br/>
		    	<input type="checkbox" id="horarios_quiere_asistir_2" name="horarios_quiere_asistir_2" value="Sábados: (De 9:00 hs a 13:00 hs)"> Sábados: (De 9:00 hs a 13:00 hs), A partir de la hora: <input type="text" name="horarios_quiere_asistir_h2" id="horarios_quiere_asistir_h2" value="" class="form-control myField" placeholder=""><br/>
			</div>	
			<?
			}

			if (strlen($dias_quiere_asistir)!=0) {
			?>
			<div class="form-group">
		    	<label for="dias_quiere_asistir">Días de la semana en que desea asistir:</label>
				<br />		    
		    	<textarea class="form-control" rows="2" name="dias_quiere_asistir" id="dias_quiere_asistir" placeholder="Horario deseado"><?=$dias_quiere_asistir?></textarea>			    		    			    
			</div>
			<?
			} else {						
			?>
			<div class="form-group">
		    	<label for="dias_quiere_asistir1">Días de la semana en que desea asistir:</label><br />
		    	<input type="checkbox" id="dias_quiere_asistir1" name="dias_quiere_asistir1" value="Lunes"> Lunes 
		    	<input type="checkbox" id="dias_quiere_asistir2" name="dias_quiere_asistir2" value="Martes"> Martes 
		    	<input type="checkbox" id="dias_quiere_asistir3" name="dias_quiere_asistir3" value="Miércoles"> Miércoles 
		    	<input type="checkbox" id="dias_quiere_asistir4" name="dias_quiere_asistir4" value="Jueves"> Jueves 
		    	<input type="checkbox" id="dias_quiere_asistir5" name="dias_quiere_asistir5" value="Viernes"> Viernes 
		    	<input type="checkbox" id="dias_quiere_asistir6" name="dias_quiere_asistir6" value="Sábado"> Sábado 				</div>	
		    <?
		    }
			*/			
		    ?>

			<div class="form-group">
		    	<label for="id_curso_contacto">Nombre y apellidos del alumno:</label>
				<br />		    
		    	<input type="text" name="nombre" id="nombre" value="<?=$nombre_alu?>" class="form-control myField" placeholder="Nombre"> <input type="text" name="apellido1" id="apellido1" value="<?=$apellido1_alu?>" class="form-control myField" placeholder="Primer Apellido"> <input type="text" name="apellido2" id="apellido2" value="<?=$apellido2_alu?>" class="form-control myField" placeholder="Segundo Apellido">		    
			</div>
			<div class="form-group">
		    	<label for="sexo">Sexo del alumno:</label>
				<br />		    
		    	<input type="radio" name="sexo" value="V"<?php  if ($sexo=="V") { print " checked"; } ?>> Varón&nbsp; &nbsp;<input type="radio" name="sexo" value="H"<?php  if ($sexo=="H") { print " checked"; } ?>> Hembra 		    
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
			<div class="form-group">
		    	<label for="antiguo_alumno_lapices">¿Es antiguo alumno de la Escuela Infantil Lápices?</label>
				<br />		    
		    	<input type="radio" id="antiguo_alumno_lapices" name="antiguo_alumno_lapices" value="1"<?php  if ($antiguo_alumno_lapices=="1") { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" id="antiguo_alumno_lapices" name="antiguo_alumno_lapices" value="0"<?php  if ($antiguo_alumno_lapices=="0") { print " checked"; } ?>> No 		    
			</div>					
			
			<h3>Datos del contacto nº 1 (padre, madre, etc.)</h3>
			<div class="form-group">
		    	<label for="persona_contacto">Nombre y apellidos:</label>
				<br />		    
		    	<input type="text" name="persona_contacto" id="persona_contacto" value="<?=$persona_contacto?>" class="form-control" placeholder="Nombre y apellidos del contacto nº 1">		    		    			    
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
		    	<label for="dni_padre1">DNI:</label>
				<br />		    
		    	<input type="text" name="dni_padre1" id="dni_padre1" value="<?=$dni_padre1?>" class="form-control myField" placeholder="DNI del contacto nº 1">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="telefono">Teléfonos:</label>
				<br />		    
		    	<input type="text" name="telefono" id="telefono" value="<?=$telefono?>" class="form-control" placeholder="Teléfonos de contacto del contacto nº 1">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="email">E-mail:</label>
				<br />		    
		    	<input type="text" name="email" id="email" value="<?=$email?>" class="form-control" placeholder="E-Mail del contacto nº 1">			    		    			    
			</div>
			
			<h3>Datos del contacto nº 2 (padre, madre, etc.)</h3>
			<div class="form-group">
		    	<label for="nombre_padre2">Nombre y apellidos:</label>
				<br />		    
		    	<input type="text" name="nombre_padre2" id="nombre_padre2" value="<?=$nombre_padre2?>" class="form-control" placeholder="Nombre y apellidos del contacto nº 2">		    		    			    
			</div>

			<div class="form-group">
		    	<label for="persona_contacto">Relación de parentesco con el alumno:</label>
				<br />		    
		    	<select name="parentesco_padre2" size=1 class="form-control myField">
                	<option value="0"></option>
                    <option value="1"<?php  if ($parentesco_padre2==1) { print " selected"; } ?>>Padre/Madre</option>
                	<option value="2"<?php  if ($parentesco_padre2==2) { print " selected"; } ?>>Abuelo/a</option>
                	<option value="3"<?php  if ($parentesco_padre2==3) { print " selected"; } ?>>Tio/a</option>
                	<option value="4"<?php  if ($parentesco_padre2==4) { print " selected"; } ?>>Autorizado/a</option>
                </select>		    		    			    
			</div>			
			
			<div class="form-group">
		    	<label for="dni_padre2">DNI:</label>
				<br />		    
		    	<input type="text" name="dni_padre2" id="dni_padre2" value="<?=$dni_padre2?>" class="form-control myField" placeholder="DNI del contacto nº 2">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="telefono_padre2">Teléfonos:</label>
				<br />		    
		    	<input type="text" name="telefono_padre2" id="telefono_padre2" value="<?=$telefono_padre2?>" class="form-control" placeholder="Teléfonos de contacto del contacto nº 2">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="email_padre2">E-mail:</label>
				<br />		    
		    	<input type="text" name="email_padre2" id="email_padre2" value="<?=$email_padre2?>" class="form-control" placeholder="E-Mail del contacto nº 2">			    		    			    
			</div>
			
			<h3>Datos familiares</h3>
			<div class="form-group">
		    	<label for="calle">Calle y Número:</label>
				<br />		    
		    	<input type="text" name="calle" id="calle" value="<?=$calle?>" class="form-control" placeholder="Domicilio: Calle y Número">		    		    			    
			</div>
			<div class="form-group">
		    	<label for="codigo_postal">Código postal y población:</label>
				<br />		    
		    	<input type="text" name="codigo_postal" id="codigo_postal" value="<?=$codigo_postal?>" class="form-control myField" placeholder="Código postal"> <input type="text" name="poblacion" id="poblacion" value="<?=$poblacion?>" class="form-control myField75" placeholder="Población">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="telefonos_familia">Teléfonos:</label>
				<br />		    
		    	<input type="text" name="telefonos_familia" id="telefonos_familia" value="<?=$telefonos_familia?>" class="form-control" placeholder="Teléfonos de contacto">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="email_familia">E-mail:</label>
				<br />		    
		    	<input type="text" name="email_familia" id="email_familia" value="<?=$email_familia?>" class="form-control" placeholder="E-Mail de contacto">			    		    			    
			</div>
			<?php
			/*
			?>
			<h3>Contactos emergencias</h3>
			<div class="form-group">
		    	<label for="contacto_emergencia1">Contacto emergencias Nº 1:</label>
				<br />		    
		    	Nombre: <input type="text" name="contacto_emergencia1" id="contacto_emergencia1" value="<?=$contacto_emergencia1?>" class="form-control myField75" placeholder="Contacto emergencias 1"> Teléfono: <input type="text" name="telefono_contacto_emergencia1" id="telefono_contacto_emergencia1" value="<?=$telefono_contacto_emergencia1?>" class="form-control myField" placeholder="Teléfono emergencias 1">				    		    			    
			</div>
			<div class="form-group">
		    	<label for="contacto_emergencia2">Contacto emergencias Nº 2:</label>
				<br />		    
		    	Nombre: <input type="text" name="contacto_emergencia2" id="contacto_emergencia2" value="<?=$contacto_emergencia2?>" class="form-control myField75" placeholder="Contacto emergencias 2"> Teléfono: <input type="text" name="telefono_contacto_emergencia2" id="telefono_contacto_emergencia2" value="<?=$telefono_contacto_emergencia2?>" class="form-control myField" placeholder="Teléfono emergencias 2">		    		    			    
			</div>
			
			
			<div class="form-group">
		    	<label for="persona_autorizada">Persona encargada de recoger al niño/a:</label>
				<br />		    
		    	<input type="text" name="persona_autorizada" id="persona_autorizada" value="<?=$persona_autorizada?>" class="form-control" placeholder="Nombre de la persona autorizada a recoger al niño/a"><br />
		    	Cuando acudas al centro debes facilitarnos una fotocopia del DNI de la persona autorizada.		    		    			    
			</div>
		<?php
		*/
		?>			
			<div class="form-group">
		    	<label for="no_publicidad">¿Desea recibir información de nuestros servicios en sus direcciones de E-Mail?</label>
				<br />		    
		    	<input type="radio" id="no_publicidad" name="no_publicidad" value="0"<?php  if ($no_publicidad==0) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" id="no_publicidad" name="no_publicidad" value="1"<?php  if ($no_publicidad==1) { print " checked"; } ?>> No 		    
			</div>
			
			<div class="form-group">
		    	<label for="comentarios">Observaciones a tener en cuenta para el monitor:</label>
				<br />		    
		    	<textarea class="form-control" rows="4" name="comentarios" id="comentarios" placeholder="Comentarios"><?=$comentarios?></textarea>			    		    			    
			</div>
			
		<div>

			<label>POLÍTICA DE PRIVACIDAD Y PROTECCIÓN DE DATOS - NORMAS DE LA PISCINA</label>	
		<textarea  class="form-control" rows=7>
De conformidad con lo establecido en la Normativa vigente en Protección de datos de Carácter Personal y su Reglamento general de protección de Datos de UE 2016/679 en su ART.13,  le informamos que sus datos personales serán incorporados al sistema de tratamiento titularidad de NEOKIDS S.L con CIF  b97324123 y con domicilio fiscal sito en la C/ MANUEL TABERNER 23 PTA 3, 46018 – VALENCIA y cuya finalidad es el seguimiento, control y gestión de nuestros centros de Educación Infantil, en base a una legitimación que está basada por la Ley Orgánica de Educación de 2006 (LOE) para el tratamiento de los datos en el ejercicio de la función educativa. También legitimada para el desarrollo y ejecución de la relación jurídica que se produce con la matriculación del alumno en un centro, así como por el consentimiento de los interesados, o de sus padres o tutores si son menores de 14 años, o por la existencia en los centros privados de intereses legítimos que, previa ponderación con los derechos y libertades de los afectados, se considere que prevalecen sobre éstos.

Los datos proporcionados podrán serán cedidos a terceros con los que la Organización tiene firmados contratos legítimos y necesarios para el desarrollo de la actividad por lo que usted no presta su consentimiento.

NEOKIDS S.L le  informa que procederá a tratar los datos de manera lícita, leal, transparente, adecuada, pertinente, limitada, exacta y actualizada y por ello nos comprometemos a adoptar todas las medidas técnicas y organizativas para su tratamiento.
En cumplimiento con la normativa vigente, NEOKIDS S.L le informa que sus datos serán conservados mientras se mantenga la relación comercial o durante los años necesarios para cumplir con las obligaciones legales. De acuerdo con los derechos que le confiere el nuevo Reglamento general de protección de Datos de (UE) 2016/679, podrá ejercer los derechos de acceso, rectificación, limitación de tratamiento, supresión, portabilidad y oposición al tratamiento de los mismos, dirigiendo su petición a la dirección postal indicada o al email dpd@neo-kids.es  pudiendo retirar el consentimiento prestado en el momento que crea oportuno. 

Le informamos que las todas aquellas fotografías o videos realizados de forma ajena por los padres o tutores legales en el zona de piscina mientras sus hijos realizan las actividades acuáticas y que se hagan públicas de motu propio por dichos padres o tutores bien a través de redes sociales u otros medios sociales como Whatssap serán Responsabilidad única y exclusivamente de los mismos quedando exonerado en todo momento de dicha responsabilidad NEOKIDS S.L.

En cualquier caso podrá obtener información adicional y detallada sobre Protección de datos en nuestra web www.neo-kids.es /politicadeprivacidad  o dirigiendo su petición a la dirección  C/Manuel Taberner 23, 46018 – Valencia o a la dirección electrónica de nuestro DPO dpd@neo-kids.es 
	
Si desea plantear una reclamación en relación al tratamiento de sus datos por parte de Zaebsal S.L , le informamos que puede ponerse en contacto con la Agencia Española de Protección de datos, C/ Jorge Juan, 6 28001-Madrid – www.agpd.es 

NORMAS DE LA PISCINA

1. Es obligatorio el uso de gorro, bañador y calcetines antideslizantes para los niños mayores de veinticuatro (24) meses, y pañal de agua y bañador para los niños menores de veinticuatro (24) meses.
2. Los padres que accedan a los vestuarios y a la zona del vaso de la piscina deben utilizar calzado adecuado (chanclas de goma). Si no disponen de él se les facilitarán bolsas cubre pies en la recepción.
3. Es obligatorio ducharse antes de entrar en la piscina.
4. Si el usuario de la piscina padece algún tipo de patología, su padre o tutor debe informar a los responsables de Neokids. Además, no se debe realizar actividad acuática si el alumno presenta fiebre o febrícula, es decir, si su temperatura corporal supera los treinta y siete grados y medio (37,5ºC). El incumplimiento de esta norma es responsabilidad exclusiva del familiar, tutor legal o responsable que acompaña al alumno. Además, para garantizar la salubridad e higiene de las instalaciones, los usuarios deben informar si padecen alguna enfermedad infecciosa o tienen alguna herida abierta.
5. Las clases de los niños pueden recuperarse cuando la ausencia a la misma sea por causa de fuerza mayor o prescripción médica, debidamente justificada, entre los que se destaca, a modo enunciativo, pero no limitativo, los siguientes supuestos: ingreso hospitalario, fracturas, esguinces, gastroenteritis, otitis, conjuntivitis, bronquiolitis, neumonía, varicela y/o enfermedades contagiosas que así se especifiquen en el informe médico. Las clases no podrán recuperarse si el motivo es un viaje, festival, graduación o evento similar. La recuperación se realizará en el período contratado y en los días marcados por la dirección de Neokids. No está contemplado la prolongación de ningún período.
6. Una vez abonado el importe del curso de natación y/o actividad educativa o recreativa por la duración seleccionada, si las familias/usuarios deciden voluntariamente dar de baja al alumno/a, no se reembolsará ningún importe de la cantidad abonada, salvo cuando se ejercite el derecho de desistimiento contemplado en la norma 16. 
7. La reserva de plaza de un curso a otro se realizará en los meses de junio y julio previa entrega de una cantidad fijada. Importe que se descontará del recibo del primer período y no será reembolsable. 
8. Se prohíbe el uso de teléfonos móviles en el interior del perímetro de la piscina y en los vestuarios. Queda totalmente prohibido realizar fotografías o grabación de imágenes en los vestuarios. La realización de fotografías o grabación de imágenes descritas en este punto podrá ser sancionada bajo multa económica de cien euros (100,00 €) y/o expulsión definitiva de las instalaciones de la piscina, sin perjuicio de las acciones legales y de responsabilidad civil que puedan derivarse de dicho acto. 
9. Los únicos elementos flotantes permitidos dentro del vaso de la piscina son aquellos proporcionados y homologados por los monitores de Neokids.
10. No está permitida la entrada de animales, a excepción de aquellos cuyo uso este prescrito médica o judicialmente y además presenten condiciones de higiene excelentes, como por ejemplo, el uso de perros guía
11. Queda prohibido introducir alimentos o material de vidrio en los vestuarios y en el perímetro de la piscina.
12. No está permitido correr en las instalaciones.
13. Únicamente puede acceder al vestuario un adulto por niño, salvo en casos concretos en los que, por motivo médico debidamente justificado, el cuidado del menor haga necesaria la presencia de dos adultos simultáneamente.
14. Los grupos dentro de los diferentes cursos que se imparten en las instalaciones se formarán a partir de tres niños en infantil y cinco en bebes. Los grupos de nivel bajo (verde) podrán tener un máximo de cinco alumnos, y los niveles (azul) y alto (rojo) podrán tener un máximo de seis alumnos por calle. El nivel de los alumnos será evaluado por los monitores y profesionales de Neokids.
15. El pago de los períodos de los cursos se realiza en los quince (15) días previos a su inicio. De no ser así, la empresa entiende que el alumno no desea continuar y se reserva el derecho de proceder a su baja. 
16. El alumno tiene derecho a desistir de cualquier contrato de servicios que celebre con Neokids en el plazo de catorce (14) días naturales sin necesidad de justificación salvo en aquellos supuestos en los que la Ley establezca un plazo diferente. El plazo de desistimiento expirará a los catorce (14) días naturales a contar desde el día de la celebración del contrato. Para ejercer el derecho de desistimiento previsto en el presente punto, el alumno deberá notificar a Neokids su decisión de desistir del contrato a través de una declaración inequívoca.
17. Los horarios, los grupos y los monitores de Neokids están sujetos a variaciones de un curso a otro en beneficio de la mejor organización de todos los alumnos.
18. Una vez alcanzada la edad de dos (2) años y previa evaluación del monitor, los niños que pasen del grupo de bebé al de infantil se les ofrecerá una plaza exclusivamente en el horario de las 19:25h. Aquellos que, al cumplir dos (2) años, no quieran pasar al grupo infantil, tendrán la opción de permanecer en el grupo de bebés solo un trimestre más. En ambos casos, se les ofrecerá la opción de cambiar de horario al terminar cada trimestre, siempre y cuando quedasen plazas disponibles.

El cumplimiento de esta normativa persigue garantizar  una correcta convivencia, el respeto a los demás y el buen manejo de las instalaciones, con el fin de conseguir su máximo aprovechamiento en condiciones de seguridad y comodidad.
</textarea>			
		</div>
		<div class="form-group">
		    	<label for="horarios_quiere_asistir">Autorizo a Neokids, s.l. a ofrecerme productos y servicios relacionados con los solicitados, envío de publicidad u ofertas comerciales de mi interés</label><br />
		    	<input type="radio" id="consentimiento_publicidad" name="consentimiento_publicidad" value="1" checked> Si<br/>
		    	<input type="radio" id="consentimiento_publicidad" name="consentimiento_publicidad" value="0"> No<br/>
		</div>	
		<div class="form-group">
		    	<label for="horarios_quiere_asistir">Asimismo autorizo a Neokids, s.l. para realizar fotografías o videos de mis hijos en el aula terrestre o acuática, con el fin de publicarlas en sus:</label><br />
		    	<input type="checkbox" id="consentimiento_web" name="consentimiento_web" value="1" checked> Web<br/>
		    	<input type="checkbox" id="consentimiento_blog" name="consentimiento_blog" value="1" checked> Blog<br/>
		    	<input type="checkbox" id="consentimiento_redes" name="consentimiento_redes" value="1" checked> Redes sociales<br/>
		</div>			
		<div class="form-group">
		    	<input type="checkbox" id="informado_tratamiento_datos" name="informado_tratamiento_datos" value="1"> Quedo Informado en todo lo relacionado con el tratamiento de datos realizado por NEOKIDS S.L y doy mi autorización para el tratamiento de los mismos.<br/>
		</div>				
	
			<button type="button" onclick="comprobar_y_enviar();" class="btn btn-primary btn-lg">Enviar la información</button>
			
			<button type="button" onclick="window.location.href='/formulario'" class="btn btn-danger btn-lg">Cancelar</button>
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