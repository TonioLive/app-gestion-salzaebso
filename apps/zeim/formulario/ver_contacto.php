<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

$id_contacto=$_GET["id_contacto"];

$bbddContactos = "contactos_web";
/*
if ($id_contacto>100000) {
	$bbddContactos = "contactos_web_sp";
}
*/


$valores_alergias=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

if (strlen($id_contacto)!=0) {

	// Marca como revisado
	$actualizaRevisado=db_query("UPDATE $bbddContactos SET revisado='".date('Y-m-d H:i:s')."' WHERE id_contacto='$id_contacto'");

	// Busca datos usuario
	
	$busca_datos_contacto=db_query("SELECT * FROM $bbddContactos WHERE id_contacto='$id_contacto'");

	if (db_num_rows($busca_datos_contacto)!=0) {
	
		$datos_contacto=db_fetch_array($busca_datos_contacto);

		$centro_enviado=$datos_contacto["id_centro"];

		$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$centro_enviado");
		if (db_num_rows($busca_datos_centro)!=0) {
				$datos_centro=db_fetch_array($busca_datos_centro);
				$nombre_centro=$datos_centro["nombre_centro"];
				$poblacion_centro=$datos_centro["poblacion_centro"];
				$tipo_centro=$datos_centro["tipo_centro"];
		}
			
		$id_contacto=$datos_contacto["id_contacto"];
		
		$id_curso_contacto=$datos_contacto["id_curso"];				
		
		$como_conocio_texto=$datos_contacto["como_conocio_texto"];

		$horario=$datos_contacto["horario"];
		$mensualidad=$datos_contacto["mensualidad"];
		$comida=$datos_contacto["comida"];
		$merienda=$datos_contacto["merienda"];
		$horas_extra=$datos_contacto["horas_extra"];
		$numero_horas_extra=$datos_contacto["numero_horas_extra"];		
		$horario_horas_extra=$datos_contacto["horario_horas_extra"];	

		$ev_semanas=security_unserialize($datos_contacto["ev_semanas"]);		
		
		$nombre_alu=$datos_contacto["Nombre"];
        $apellido1_alu=$datos_contacto["Apellido1"];
        $apellido2_alu=$datos_contacto["Apellido2"];
        $sexo=$datos_contacto["sexo"];
		$sip=$datos_contacto["SIP"];

		$fecha_nacimiento='';
		if ($datos_alumno["fecha_nacimiento"]!='0000-00-00') {
			$fecha_nacimiento=date("d/m/Y",strtotime($datos_contacto["fecha_nacimiento"]));
		}

        $usuario_nonato=$datos_contacto["nonato"];
               
		$nombre_c1=$datos_contacto["nombre_c1"];
		$apellido1_c1=$datos_contacto["apellido1_c1"];
		$apellido2_c1=$datos_contacto["apellido2_c1"];
		$telefono_padre1=$datos_contacto["telefono_padre1"];
		$telefono_t1=$datos_contacto["telefono_t1"];
		$email_padre1=$datos_contacto["email_padre1"];
		$dni_padre1=$datos_contacto["dni_padre1"];
		
		$nombre_c2=$datos_contacto["nombre_c2"];
		$apellido1_c2=$datos_contacto["apellido1_c2"];
		$apellido2_c2=$datos_contacto["apellido2_c2"];
		$telefono_padre2=$datos_contacto["telefono_padre2"];
		$telefono_t2=$datos_contacto["telefono_t2"];
		$email_padre2=$datos_contacto["email_padre2"];
		$dni_padre2=$datos_contacto["dni_padre2"];
		
		$parentesco_padre1=$datos_contacto["parentesco_padre1"];
		$parentesco_padre2=$datos_contacto["parentesco_padre2"];		
		
		$calle=$datos_contacto["calle"];
        $poblacion=$datos_contacto["poblacion"];
        $codigo_postal=$datos_contacto["codigo_postal"];        					
		
		$comentarios=$datos_contacto["comentarios"];
		
		$no_publicidad=$datos_contacto["no_publicidad"]; 

		$consentimiento_publicidad=$datos_contacto['consentimiento_publicidad'];
		$consentimiento_web=$datos_contacto['consentimiento_web'];
		$consentimiento_blog=$datos_contacto['consentimiento_blog'];
		$consentimiento_redes=$datos_contacto['consentimiento_redes'];
		$informado_tratamiento_datos=$datos_contacto['informado_tratamiento_datos'];
		
		$forma_de_pago=$datos_contacto['forma_de_pago'];
		$IBAN=$datos_contacto['IBAN'];

		foreach($array_alergias as $key => $alergia) {
		
			$valores_alergias[$alergia]=$datos_contacto[$alergia];	
		
		}		
		
		$piscina=$datos_contacto['piscina'];		
		
		}

	}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$nombre_centro;?> - Inscripción Curso</title>

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

</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><h3>Escuela Infantil <?=$nombre_centro;?> - Solicitud inscripción</h3></div>
  		<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
			<?php
			if ($como_conocio_texto!="") {
				?>	
				<div class="form-group">
					<label for="sexo">¿Cómo nos ha conocido?</label>
					<br />		    
					<?=$como_conocio_texto;?>
				</div>				
				<?php
					}
				?>				
			<div class="form-group">
		    	<label for="id_curso_contacto">Curso por el que se interesa:</label>
		    	<select id="id_curso_contacto" name="id_curso_contacto" class="form-control">
                        <?php 
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
						$escuelaVerano=false;
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio") or die(db_error()."-4");
                        while($cursos=db_fetch_array($consulta_cursos)){
                        
                        $texto_finaliza="";
                        
                        if (strtotime($cursos["fecha_inicio"])<=time()) {
                        
                        	$texto_finaliza=" (Ya iniciado, finaliza el día ".date("d/m/Y",strtotime($cursos["fecha_fin"])).")";
                        	
                        	} else {
                        	
                        	$texto_finaliza=" (Se inicia el día ".date("d/m/Y",strtotime($cursos["fecha_inicio"])).")";
                        	
                        	}
                        
                        if (($cursos["id_curso"]==$id_curso_contacto) and (date("n",strtotime($cursos["fecha_fin"]))==7)) {
							$escuelaVerano=true;		
						}
						
                        ?>
                        <option value="<?=$cursos["id_curso"];?>"<?php  if ($cursos["id_curso"]==$id_curso_contacto) { print " selected"; } ?>><?=$cursos["nombre"];?><?=$texto_finaliza?></option>
                        <?php 
                        }
                        ?>
            	</select>
			</div>
			<h3>Servicios</h3>
			<?php
			// Comprueba si es escuela de verano
			
			
			
			if ($escuelaVerano==true) {
			?>
			<div class="form-group">
		    	<label for="horarios_quiere_asistir">Semanas en las que asistirá a la escuela de verano</label><br />
				<?php
				$buscaSemanas = db_query_params('SELECT * FROM cursos_escuela_verano_semanas WHERE id_curso=? ORDER BY orden', 's', array($id_curso_contacto));
				while($semanas=db_fetch_array($buscaSemanas)) {
					$nombreSemana = "Semana " . ($semanas["orden"]-1) . ", Del " . date("d/m/Y",strtotime($semanas["inicio_semana"])) . " al " .date("d/m/Y",strtotime($semanas["fin_semana"]));
					if ($semanas["mes_completo"] == 1) {
						$nombreSemana="Mes completo, Del " . date("d/m/Y",strtotime($semanas["inicio_semana"])) . " al " .date("d/m/Y",strtotime($semanas["fin_semana"]));
					}
				?> 
		    	<input type="checkbox" id="ev_semanas1" name="ev_semanas[]" value="<?=($semanas["orden"]-1)?>"<?php  if ($ev_semanas[($semanas["orden"]-1)]==1) { print " checked"; } ?>> <?=$nombreSemana;?><br/>
				<?php
				}
				?>
			</div>
			<?php	
			if ($centro_enviado==6) {
			?>	
			<div class="form-group">
		    	<label for="sexo">¿Quiere contratar el servicio de natación?</label>
				<br />		    
		    	<input type="radio" name="piscina" value="Si"<?php  if ($piscina=="Si") { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="piscina" value="No"<?php  if ($piscina=="No") { print " checked"; } ?>> No 		    
			</div>				
			<?php
				}
			}
			?>
			<div class="form-group">
		    	<label for="dni_padre1">Horario diario:</label>
				<br />		    
		    	<input type="text" name="horario" id="horario" value="<?=$horario?>" class="form-control myField" placeholder="Horario en el que asistirá a la escuela">			    		    			    
			</div>
		
			<div class="form-group">
		    	<label for="sexo">Tipo de mensualidad:</label>
				<br />		    
		    	<input type="radio" name="mensualidad" value="media_jornada"<?php  if ($mensualidad=="mensualidad") { print " checked"; } ?>> Media Jornada&nbsp; &nbsp;<input type="radio" name="mensualidad" value="jornada_completa"<?php  if ($mensualidad=="jornada_completa") { print " checked"; } ?>> Jornada Completa 		    
			</div>	
			<div class="form-group">
		    	<label for="sexo">Comida:</label>
				<br />		    
		    	<input type="radio" name="comida" value="Si"<?php  if ($comida=="Si") { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="comida" value="No"<?php  if ($comida=="No") { print " checked"; } ?>> No 		    
			</div>
			<?php /*
			<div class="form-group">
		    	<label for="sexo">Merienda:</label>
				<br />		    
		    	<input type="radio" name="merienda" value="Si"<? if ($merienda=="Si") { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="merienda" value="No"<? if ($merienda=="No") { print " checked"; } ?>> No 		    
			</div>
			*/ ?>
			<div class="form-group">
		    	<label for="sexo">Matinera:</label>
				<br />		    
		    	<input type="radio" name="horas_extra" value="Si"<?php  if ($horas_extra=="Si") { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="horas_extra" value="No"<?php  if ($horas_extra=="No") { print " checked"; } ?>> No 		    
			</div>	
			<?php 
			/*
			<div class="form-group">
		    	<label for="dni_padre1">Nº de horas extra:</label>
				<br />		    
		    	<input type="text" name="numero_horas_extra" id="numero_horas_extra" value="<?=$numero_horas_extra?>" class="form-control myField" placeholder="Nº diario de horas extra">			    		    			    
			</div>	
			*/
			?>
			<div class="form-group">
		    	<label for="horario_horas_extra">Horario de las horas extra:</label>
		    	<select id="horario_horas_extra" name="horario_horas_extra" class="form-control">				   
						<option value=""<?php  if ($horario_horas_extra=='') { print " selected"; } ?>>Selecciona un horario para la realización de las horas extra.</option>
                        <option value="Matinera"<?php  if ($horario_horas_extra=="Matinera") { print " selected"; } ?>>Matinera, antes de las 9:00 de la mañana.</option>
                        <option value="Vespertina"<?php  if ($horario_horas_extra=="Vespertina") { print " selected"; } ?>>Vespertina, después de las 17:00 de la tarde.</option>
                        <option value="Matinera y vespertina"<?php  if ($horario_horas_extra=="Matinera y vespertina") { print " selected"; } ?>>Ambos horarios, antes de las 9:00 y después de las 17:00.</option>
            	</select>
				* Los horarios ampliados se realizarán si hay un número mínimo de alumnos o alumnas que lo soliciten. 				
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
		<div class="form-group">
			<label for="dni_padre1">Nº de tarjeta SIP:</label>
			<br />		    
			<input type="text" name="sip" id="sip" value="<?=$sip?>" class="form-control myField" placeholder="Nº tarjeta SIP del alumno">			    		    			    
		</div>	
		<div class="form-group">
		   	<label for="sexo">Alergias. Indique si el/la niño/niña sufre alguna de las siguientes alergias</label>
			<br />		
			<?php
			foreach($array_alergias as $key => $alergia) {
			?>
				<input type="checkbox" name="<?=$alergia?>" value="1"<?php  if ($valores_alergias[$alergia]==1) { print " checked"; } ?>> <?=$array_nombres_alergias[$key]?>

			<?php
			}
			?>    
		</div>			
		<div class="row">
			<div class="col-md-12">	
			<h3>Datos del contacto nº 1 (padre, madre, etc.)</h3>
			<div class="form-group">
		    	<label for="id_curso_contacto">Nombre y apellidos del contacto nº 1:</label>
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
		    	<label for="dni_padre1">DNI:</label>
				<br />		    
		    	<input type="text" name="dni_padre1" id="dni_padre1" value="<?=$dni_padre1?>" class="form-control myField" placeholder="DNI del contacto nº 1">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="telefono">Teléfono particular:</label>
				<br />		    
		    	<input type="text" name="telefono_padre1" id="telefono_padre1" value="<?=$telefono_padre1?>" class="form-control" placeholder="Teléfono particular del contacto nº 1">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="telefono">Teléfono del trabajo o en horario laboral:</label>
				<br />		    
		    	<input type="text" name="telefono_t1" id="telefono_t1" value="<?=$telefono_t1?>" class="form-control" placeholder="Teléfono en horario laboral del contacto nº 1">			    		    			    
			</div>			
			<div class="form-group">
		    	<label for="email">E-mail:</label>
				<br />		    
		    	<input type="text" name="email_padre1" id="email_padre1" value="<?=$email_padre1?>" class="form-control" placeholder="E-Mail del contacto nº 1">			    		    			    
			</div>
			
			<h3>Datos del contacto nº 2 (padre, madre, etc.)</h3>
			<div class="form-group">
		    	<label for="id_curso_contacto">Nombre y apellidos del contacto nº 2:</label>
				<br />		    
		    	<input type="text" name="nombre_c2" id="nombre_c2" value="<?=$nombre_c2?>" class="form-control myField" placeholder="Nombre"> <input type="text" name="apellido1_c2" id="apellido1_c2" value="<?=$apellido1_c2?>" class="form-control myField" placeholder="Primer Apellido"> <input type="text" name="apellido2_c2" id="apellido2_c2" value="<?=$apellido2_c2?>" class="form-control myField" placeholder="Segundo Apellido">		    
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
		    	<label for="telefono">Teléfono particular:</label>
				<br />		    
		    	<input type="text" name="telefono_padre2" id="telefono_padre2" value="<?=$telefono_padre2?>" class="form-control" placeholder="Teléfono particular del contacto nº 2">			    		    			    
			</div>
			<div class="form-group">
		    	<label for="telefono">Teléfono del trabajo o en horario laboral:</label>
				<br />		    
		    	<input type="text" name="telefono_t2" id="telefono_t2" value="<?=$telefono_t2?>" class="form-control" placeholder="Teléfono en horario laboral el contacto nº 2">			    		    			    
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
			
			<h3>Forma de pago</h3>
			<div class="form-group">
		    	<label for="sexo">Forma de pago preferida:</label>
				<br />		    
		    	<input type="radio" id="forma_de_pago" name="forma_de_pago1" value="0"<?php  if ($forma_de_pago==0) { print " checked"; } ?>> Contado&nbsp; &nbsp;<input type="radio" id="forma_de_pago2" name="forma_de_pago" value="1"<?php  if ($forma_de_pago==1) { print " checked"; } ?>> Domiciliación bancaria 		    
			</div>				
			<div class="form-group">
		    	<label for="calle">Nº de cuenta (IBAN):</label>
				<br />		    
		    	<input type="text" name="IBAN" id="IBAN" value="<?=$IBAN?>" class="form-control" placeholder="Nº de cuenta (IBAN)">		    		    			    
			</div>				
			
			<div class="form-group">
		    	<label for="no_publicidad">¿Desea recibir información de nuestros servicios en sus direcciones de E-Mail?</label>
				<br />		    
		    	<input type="radio" id="no_publicidad" name="no_publicidad" value="0"<?php  if ($no_publicidad==0) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" id="no_publicidad" name="no_publicidad" value="1"<?php  if ($no_publicidad==1) { print " checked"; } ?>> No 		    
			</div>
			
			<div class="form-group">
		    	<label for="comentarios">Observaciones:</label>
				<br />		    
		    	<textarea class="form-control" rows="4" name="comentarios" id="comentarios" placeholder="Comentarios"><?=$comentarios?></textarea>			    		    			    
			</div>
			

		<div class="form-group">
		    	<label for="horarios_quiere_asistir">Autorizo a Zaebsal, s.l. a ofrecerme productos y servicios relacionados con los solicitados, envío de publicidad u ofertas comerciales de mi interés</label><br />
		    	<input type="radio" id="consentimiento_publicidad" name="consentimiento_publicidad" value="1"<?php  if ($consentimiento_publicidad==1) { print " checked"; } ?>> Si<br/>
		    	<input type="radio" id="consentimiento_publicidad" name="consentimiento_publicidad" value="0"<?php  if ($consentimiento_publicidad==0) { print " checked"; } ?>> No<br/>
		</div>	
		<div class="form-group">
		    	<label for="horarios_quiere_asistir">Asimismo autorizo a Zaebsal, s.l. para realizar fotografías o videos de mis hijos, con el fin de publicarlas en sus:</label><br />
		    	<input type="checkbox" id="consentimiento_web" name="consentimiento_web" value="1"<?php  if ($consentimiento_web==1) { print " checked"; } ?>> Web<br/>
		    	<input type="checkbox" id="consentimiento_blog" name="consentimiento_blog" value="1"<?php  if ($consentimiento_blog==1) { print " checked"; } ?>> Blog<br/>
		    	<input type="checkbox" id="consentimiento_redes" name="consentimiento_redes" value="1"<?php  if ($consentimiento_redes==1) { print " checked"; } ?>> Redes sociales<br/>
		</div>			
		<div class="form-group">
		    	<input type="checkbox" id="informado_tratamiento_datos" name="informado_tratamiento_datos" value="1"<?php  if ($informado_tratamiento_datos==1) { print " checked"; } ?>> Quedo Informado en todo lo relacionado con el tratamiento de datos realizado por Zaebsal, s.l. y doy mi autorización para el tratamiento de los mismos.<br/>
		</div>				
	
			<a type="button" href="listado.php?id_curso=<?=$id_curso_contacto?>" class="btn btn-danger btn-lg">Volver</a>
			<br />
		</div>
		</div>
		</div> <!-- final formulario -->
		</div>
	</div>    
</div>
</body>
</html>