<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php  require("../configuracion/autentifica_admin.php");

$formulario_name="contactos";
$formulario_accion="nuevo_contacto1.php";

$url_retorno="listado_contactos.php";

$id_contacto=app_value($_GET,'id_contacto');
if($id_contacto!==''&&(!ctype_digit((string)$id_contacto)||strlen((string)$id_contacto)>10))security_stop('Identificador de contacto no valido.',422);
if((string)$id_contacto==='0')$id_contacto='';
$estado_inscripcion="";
$info_aula_alumno="";
$sexo_alumno_numero=0;
// Explicit form defaults keep an empty contact free of PHP 8.5 warnings.
foreach(array('antiguo_alumno_lapices','apellido1_alu','apellido2_alu','calle','codigo_postal','comentarios','como_conocio','contacto_emergencia1','contacto_emergencia2','coordenadas','dias_quiere_asistir','direccion_complera','dni_padre1','dni_padre2','edad','email','email_familia','email_padre2','estado_inscripcion_espera','fecha_l','fecha_nacimiento_dia1','horarios_quiere_asistir','id_centro_contacto','id_curso_contacto','id_usuario','motivo_cancelacion','no_publicidad','nombre_alu','nombre_padre2','nuevo_alumno','parentesco_padre1','parentesco_padre2','persona_autorizada','persona_contacto','poblacion','proceso_decision','semanas_embarazo','sexo','telefono','telefono_contacto_emergencia1','telefono_contacto_emergencia2','telefono_padre2','telefonos_familia','texto_id_usuario','tipo_alumno','usuario_nonato','usuario_prematuro','usuario_prematuro1') as $contactField) $$contactField='';
$datos_contacto=array('fecha_nacimiento'=>'');$id_contacto_mapa=0;
$id_centro_contacto=(int)$centro_actual;$id_usuario=0;$nuevo_alumno=1;
foreach(array('fecha_nacimiento','fecha_informacion','fecha_b1','fecha_b2','fecha_b3','fecha_c') as $contactDateField)foreach(array('dia','mes','ano') as $contactDatePart)${$contactDateField.'_'.$contactDatePart}='';
$contactCourse=app_rows('SELECT id_curso FROM cursos_academicos WHERE fecha_inicio<=CURDATE() AND fecha_fin>=CURDATE() ORDER BY fecha_inicio DESC LIMIT 1');$id_curso_contacto=(int)($contactCourse[0]['id_curso']??0);

if (strlen($id_contacto)!=0) {
        $id_contacto_mapa=$id_contacto;
        $busca_datos_contacto=db_query_params('SELECT * FROM contactos WHERE id_contacto=?', 's', array($id_contacto));
        if (db_num_rows($busca_datos_contacto)!=0) {
                $datos_contacto=db_fetch_array($busca_datos_contacto);
                foreach($datos_contacto as $contactKey=>$contactValue)if($contactValue===null)$datos_contacto[$contactKey]='';
                if((int)$datos_contacto['id_centro']!==0&&!security_owned_centre((int)$datos_contacto['id_centro']))security_stop('No tienes acceso a este contacto.',403);
                $mensaje="Edición";
                $nombre_alu=$datos_contacto["Nombre"];
                $apellido1_alu=$datos_contacto["Apellido1"];                                               
                $apellido2_alu=$datos_contacto["Apellido2"];
                $persona_contacto=$datos_contacto["persona_contacto"];
                $proceso_decision=$datos_contacto["proceso_decision"];
                $comentarios=$datos_contacto["comentarios"];
                $telefono=$datos_contacto["telefono"];
                $email=$datos_contacto["email"];
                $calle=$datos_contacto["calle"];
                $poblacion=$datos_contacto["poblacion"];
                $codigo_postal=$datos_contacto["codigo_postal"];
                $coordenadas=$datos_contacto["coordenadas"];
                $sexo=$datos_contacto["sexo"];
                $como_conocio=$datos_contacto["como_conocio"];
                $motivo_cancelacion=$datos_contacto["motivo_cancelacion"];
                
                
                $no_publicidad=$datos_contacto["no_publicidad"];
                
                $direccion_complera=$datos_contacto["calle"]." ".$datos_contacto["codigo_postal"]." ".$datos_contacto["poblacion"];
                
                if ($datos_contacto["sexo"]=="H") {
                        $sexo_alumno_numero=1;
                        } else {
                        $sexo_alumno_numero=0;
                        }
                $id_curso_contacto=$datos_contacto["id_curso"];
                $id_centro_contacto=$datos_contacto["id_centro"];
                
                $usuario_nonato=$datos_contacto["nonato"];
                $usuario_prematuro=$datos_contacto["prematuro"];
                $usuario_prematuro1=$datos_contacto["prematuro1"];
                
                foreach(array('fecha_nacimiento','fecha_informacion','fecha_b1','fecha_b2','fecha_b3','fecha_c') as $contactDateField){
                    $contactDateText=(string)($datos_contacto[$contactDateField]??'');
                    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$contactDateText)||$contactDateText==='0000-00-00')continue;
                    $contactDate=DateTimeImmutable::createFromFormat('!Y-m-d',$contactDateText);
                    if(!$contactDate||$contactDate->format('Y-m-d')!==$contactDateText)continue;
                    ${$contactDateField.'_dia'}=$contactDate->format('j');${$contactDateField.'_mes'}=$contactDate->format('m');${$contactDateField.'_ano'}=$contactDate->format('Y');
                    if($contactDateField==='fecha_nacimiento'){
                        $fecha_nacimiento_dia1=$contactDate->format('d');$contactToday=new DateTimeImmutable('today');
                        if($contactDate<=$contactToday){$contactAge=$contactDate->diff($contactToday);$edad=$contactAge->y<2?($contactAge->y*12+$contactAge->m).' meses':$contactAge->y.' años';}
                    }
                }
                // Datos ficha inscripcion 2015
				
				$tipo_alumno=$datos_contacto["tipo_alumno"];
				
				$horarios_quiere_asistir=$datos_contacto["horarios_quiere_asistir"];
				$dias_quiere_asistir=$datos_contacto["dias_quiere_asistir"];
				$antiguo_alumno_lapices=$datos_contacto["antiguo_alumno_lapices"];
				
				$dni_padre1=$datos_contacto["dni_padre1"];
				$nombre_padre2=$datos_contacto["nombre_padre2"];
				$telefono_padre2=$datos_contacto["telefono_padre2"];
				$email_padre2=$datos_contacto["email_padre2"];
				$dni_padre2=$datos_contacto["dni_padre2"];
				
				$parentesco_padre1=$datos_contacto["parentesco_padre1"];
				$parentesco_padre2=$datos_contacto["parentesco_padre2"];
				
				$telefonos_familia=$datos_contacto["telefonos_familia"];
				$email_familia=$datos_contacto["email_familia"];
				
				$contacto_emergencia1=$datos_contacto["contacto_emergencia1"];
				$telefono_contacto_emergencia1=$datos_contacto["telefono_contacto_emergencia1"];
				$contacto_emergencia2=$datos_contacto["contacto_emergencia2"];
				$telefono_contacto_emergencia2=$datos_contacto["telefono_contacto_emergencia2"];
				
				$persona_autorizada=$datos_contacto["persona_autorizada"];
				
				
				
				
				
				
				$semanas_embarazo=$datos_contacto["semanas_embarazo"];
								
				// Fin datos nuevos

                $nuevo_alumno=$datos_contacto["nuevo_alumno"];                                                
                $id_usuario=$datos_contacto["id_usuario"];
                                                             
                if (intval($id_usuario)!=0) {
                        $busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario));
                        if (db_num_rows($busca_datos_usuario)!=0) {
                                $datos_usuario=db_fetch_array($busca_datos_usuario);
                                $texto_id_usuario="($id_usuario) ".$datos_usuario["Nombre"]." ".$datos_usuario["Apellido1"]." ".$datos_usuario["Apellido2"];
                                
                                // Comprueba si el alumno está inscrito en el curso seleccionado
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos WHERE id_curso=? AND id_centro=? AND id_usuario=?', 'sss', array($id_curso_contacto, $id_centro_contacto, $id_usuario));
                                if (db_num_rows($busca_inscripcion_curso_actual)!=0) {
                                        $datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual);
                                        $estado_inscripcion=" disabled";
                                        $estado_inscripcion_espera=" disabled";
                                        $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_inscripcion_curso_actual["aula"]));
                                        $datos_aula=db_fetch_array($busca_datos_aula);
                                        $info_aula_alumno="<br>Alumno inscrito en el centro seleccionado en el aula: ".$datos_aula["nombre"];
                                        }
                                // Comprueba si el alumno está inscrito en lista de espera
                                $busca_inscripcion_espera_curso_actual=db_query_params('SELECT * FROM lista_espera WHERE id_curso=? AND id_centro=? AND id_usuario=?', 'sss', array($id_curso_contacto, $id_centro_contacto, $id_usuario));
                                if (db_num_rows($busca_inscripcion_espera_curso_actual)!=0) {
                                        $estado_inscripcion_espera=" disabled";
                                        }

                                } else {
                                $texto_id_usuario="($id_usuario) Usuario Desconocido";
                                }
                        } else {
                        $texto_id_usuario="";
                        }
                 $estado_boton="";
                } else {
                $id_contacto="";
                $mensaje="Nuevo";
                $estado_boton=" disabled";
                }
        } else {
                $id_contacto="";
                $id_contacto_mapa=0;
                $mensaje="Nuevo";
                $estado_boton=" disabled";
        }

if (!preg_match('/^\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)$/D',(string)$coordenadas,$contactCoords)||abs((float)($contactCoords[1]??91))>90||abs((float)($contactCoords[2]??181))>180) {
	$coordenadas="(39.4079665, -0.5015968)";	
}

$patterns = array('/\)/','/\(/','/ /');
$espacios = array('','','');
$LatLongMapa=preg_replace($patterns,$espacios,$coordenadas);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css">
<link rel="stylesheet" href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"></script>
<script type="text/javascript" src="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js"></script>

<script type="text/javascript">
  var geocoder;
  var map;
  var errores = "";
  var id_usuario=<?=$id_contacto_mapa?>;
  var query = <?=json_encode(app_text($direccion_complera),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;

//acá cada uno crea el objeto a su manera

var pagina_destino="http://gestion.neo-kids.es/contactos/guardar_coordenadas_contacto.php";

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);

    }
}

function guarda_dato(coordenadas) {

        if (id_usuario!=0) {
                sendAndLoad({desc:'id_usuario',dato:id_usuario},{desc:'coordenadas',dato:coordenadas});
                // window.open(pagina_destino+'?id_usuario='+id_usuario+'&coordenadas='+coordenadas);
                }

        document.<?=$formulario_name?>.coordenadas.value=coordenadas;

        }

</script>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto" onload="initialize();">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=155 height=1 align="left">
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">

<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

	function initialize() {

		mymap = L.map('map_canvas').setView([<?=$LatLongMapa?>], 15);
		
		L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
	   		attribution: "Map data &copy; <a href='https://www.openstreetmap.org/'>OpenStreetMap</a> contributors",
	   		maxZoom: 18,
		}).addTo(mymap);
		
		mymap.addControl(new L.Control.Fullscreen());								 			
		
		marker = L.marker([<?=$LatLongMapa?>]).addTo(mymap);
		
		marker.addEventListener('moveend', function(){
		    
		    geocodePosition(this.getLatLng());									    		    
		    
		});
	    			

	}	

  function localizaOK(data) {
	  
	var newLatLng = new L.LatLng(data[0].lat, data[0].lon);
	marker.setLatLng(newLatLng); 							
	mymap.panTo(newLatLng);

	guarda_dato("("+newLatLng.lat+", "+newLatLng.lng+")");	
					
  }

  function codeAddress() {
    var address = query.replace(/AVDA./i,"Avenida");
    
    address = address.replace(/Avenida./i,"Avenida"); 
      
	var poblacion = $('#poblacion').val();      
        
	var geocode = 'https://nominatim.openstreetmap.org/search?q='+address+'&city='+poblacion+'&countrycodes=ES&format=json&polygon=0&addressdetails=1';			

	$.getJSON(geocode, function(data) {
		
		if (data.length==0) {
		
			// Prueba cambiando C/ y AVDA. por Calle de y Avenida de
						
			var address1 = address.replace(/CALLE/i,"Calle de ");
			var address2 = address1.replace(/C\//i,"Calle de ");			
			var address3 = address2.replace(/Avenida/i,"Avenida de ");
			
			console.log(address3);
			
			var geocode = 'https://nominatim.openstreetmap.org/search?q='+address3+'&city='+poblacion+'&countrycodes=ES&format=json&polygon=0&addressdetails=1';			
		
			$.getJSON(geocode, function(data) {	
			
				if (data.length>0) {	
												
					localizaOK(data);																											  
		        } else {
		
					// Prueba cambiando C/ y AVDA. por Calle del y Avenida del
								
					var address1 = address.replace(/CALLE/i,"Calle del ");
					var address2 = address1.replace(/C\//i,"Calle del ");			
					var address3 = address2.replace(/Avenida/i,"Avenida del ");
					
					console.log(address3);
															
					var geocode = 'https://nominatim.openstreetmap.org/search?q='+address3+'&city='+poblacion+'&countrycodes=ES&format=json&polygon=0&addressdetails=1';			
				
					$.getJSON(geocode, function(data) {	
					
						if (data.length>0) {	
														
							localizaOK(data);																											  
				        } else {
				
				        	guarda_dato("NO LOCALIZADO");
				        	
				        }			
						
					});
		        	
		        }			
				
			});			
			
		} else {	
										
			localizaOK(data);																											  
        }
        				  				  
	});	
        
  }


  function localiza() {

        query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                codeAddress();
                } else {
                alert("Escribe antes la dirección: Cale, Número, Código postal y población");
                }

        }

function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.persona_contacto.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre de la persona de contacto.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono.value=="")&&(self.document.<?=$formulario_name;?>.email.value=="")) { mensajes_error=mensajes_error+"Hay que introducir el teléfono o el e-mail de la persona de contacto.\u000A";errores=1; }
                
                query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                        codeAddress();
                        } else {
                        // mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                        }
                
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
                        
        }
        
function selecciona_fecha(nombre_campo_presel) {
                // Comprueba fecha

                mes_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino=<?=$formulario_name;?>&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
function nueva_nota() {
        window.open('nueva_nota.php?id_contacto=<?=$id_contacto;?>','editar','toolbar=no,resizable=no,width=500,height=210');
        }
function editar_nota(fila) {
        if (fila!="") {
                window.open('nueva_nota.php?id_contacto=<?=$id_contacto;?>&u='+fila,'editar','toolbar=no,resizable=no,width=500,height=210');
                } else {
                alert("Primero debes seleccionar la nota que deseas editar");
                }
        }
function borrar_nota(id_borrar,nombre_mostrar) {
        if (confirm("¿Deseas confirmar la eliminación de la nota?")) {
                document.<?=$formulario_name;?>.id_quitar_nota.value=id_borrar;
                document.<?=$formulario_name;?>.retorno.value=1;
                document.<?=$formulario_name;?>.submit();
                }
        }
function borrar_alumno() {
        if (confirm("Se va a borrar la relación entre este contacto y el alumno seleccionado.\n\nSi no se han guardado los datos, se restaurará la información que había antes de crear la relación (nombre, fecha de nacimiento y sexo del alumno)\n\n¿Continuar con la eliminación de la relación?")) {
                document.<?=$formulario_name;?>.id_usuario.value="";
                document.<?=$formulario_name;?>.texto_id_usuario.value="";
                document.<?=$formulario_name;?>.nombre.value=<?=json_encode(app_text($nombre_alu),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
                document.<?=$formulario_name;?>.apellido1.value=<?=json_encode(app_text($apellido1_alu),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
                document.<?=$formulario_name;?>.apellido2.value=<?=json_encode(app_text($apellido2_alu),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
                document.<?=$formulario_name;?>.fecha_nacimiento_dia.value='<?=app_escape($fecha_nacimiento_dia1)?>';
                document.<?=$formulario_name;?>.fecha_nacimiento_mes.value='<?=$fecha_nacimiento_mes;?>';
                document.<?=$formulario_name;?>.fecha_nacimiento_ano.value='<?=$fecha_nacimiento_ano;?>';
                document.<?=$formulario_name;?>.sexo[<?=$sexo_alumno_numero;?>].click();
                }
        }
function copiar_alumno() {

	if (document.<?=$formulario_name;?>.id_usuario.value!="") {
		
		if (confirm("Se van a copiar los datos de este formulario en la ficha del alumno.\n\n¿Estás seguro/a?")) {
		
			window.location.href="/contactos/copiar_datos.php?id_contacto=<?=$id_contacto;?>&id_alumno=" + document.getElementById("id_usuario").value;
		
		}
	
	} else {
	
		alert("El contacto no está relacionado con ningún alumno actual. Puedes relacionarlos pulsando el botón 'Buscar alumno'");
	
	}

}        

        
function buscar_alumno() {
        window.open('seleccionar_alumno.php','nuevo','scrollbars=yes,toolbar=no,resizable=no,width=600,height=500');
        }
        
function buscar_alumno_lapices(escuela) {
        window.open('seleccionar_alumno_'+escuela+'.php','nuevo','scrollbars=yes,toolbar=no,resizable=no,width=600,height=500');
        }
                
function inscribir_alumno() {
                var errores=0;

                var mensajes_error="";

                if ((self.document.<?=$formulario_name;?>.id_centro_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_centro_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar la escuela donde se inscribe.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.id_curso_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_curso_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar el curso en el que se inscribe.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.nombre.value=="") { mensajes_error=mensajes_error+"Hay que escribir el nombre del alumno.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.apellido1.value=="") { mensajes_error=mensajes_error+"Hay escribir el primer apellido del alumno.\u000A";errores=1; }
                
                // Comprueba fecha
                if (self.document.<?=$formulario_name;?>.nonato.checked==false) {
                        la_fecha=self.document.<?=$formulario_name;?>.fecha_nacimiento_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de nacimiento introducida no es correcta.\u000A";errores=1; }
                        }
                
                if (self.document.<?=$formulario_name;?>.persona_contacto.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre de la persona de contacto.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono.value=="")&&(self.document.<?=$formulario_name;?>.email.value=="")) { mensajes_error=mensajes_error+"Hay que introducir el teléfono o el e-mail de la persona de contacto.\u000A";errores=1; }

                query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                        codeAddress();
                        } else {
                        // mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                        }

                if (errores==0) {
                        document.<?=$formulario_name;?>.action="inscribir_contacto.php";
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("No se puede realizar la inscripción con los datos proporcionados.\n\nHay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }
        
function inscribir_espera() {
                var errores=0;

                var mensajes_error="";

                if ((self.document.<?=$formulario_name;?>.id_centro_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_centro_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar la escuela donde se inscribe.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.id_curso_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_curso_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar el curso en el que se inscribe.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.nombre.value=="") { mensajes_error=mensajes_error+"Hay que escribir el nombre del alumno.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.apellido1.value=="") { mensajes_error=mensajes_error+"Hay escribir el primer apellido del alumno.\u000A";errores=1; }

                // Comprueba fecha
                if (self.document.<?=$formulario_name;?>.nonato.checked==false) {
                        la_fecha=self.document.<?=$formulario_name;?>.fecha_nacimiento_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de nacimiento introducida no es correcta.\u000A";errores=1; }
                        }
                        
                if (self.document.<?=$formulario_name;?>.persona_contacto.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre de la persona de contacto.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono.value=="")&&(self.document.<?=$formulario_name;?>.email.value=="")) { mensajes_error=mensajes_error+"Hay que introducir el teléfono o el e-mail de la persona de contacto.\u000A";errores=1; }

                query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                        codeAddress();
                        } else {
                        // mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                        }

                if (errores==0) {
                        document.<?=$formulario_name;?>.action="inscribir_contacto_espera.php";
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("No se puede realizar la inscripción con los datos proporcionados.\n\nHay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }
        
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_contacto" value="<?=$id_contacto;?>">
<input type="hidden" name="formulario_enviado" value="1">
<input type="hidden" name="retorno" value="0">
<input type="hidden" name="id_quitar_nota" value="">
<input type="hidden" name="coordenadas" value="<?=app_escape($coordenadas)?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> Contacto</b><br><br></font>
                        <input type="button" value="Presupuesto" onclick="window.location.href='nuevo_presupuesto.php?id_contacto=<?=$id_contacto?>'" style="font-size: 10px"<?=$estado_boton;?>> <br>
<input type="button" value="Obtener ficha de inscripción Infantil/bebes" onclick="window.location.href='/formulario/imprimir_contacto.php?id_contacto=<?=$id_contacto?>'" style="font-size: 10px"<?=$estado_boton;?>> 
<input type="button" value="Obtener ficha de inscripción Aquagym" onclick="window.location.href='/formulario/imprimir_contacto.php?id_contacto=<?=$id_contacto?>&id_plantilla=1'" style="font-size: 10px"<?=$estado_boton;?>>
<input type="button" value="Obtener ficha de inscripción Matronatación" onclick="window.location.href='/formulario/imprimir_contacto.php?id_contacto=<?=$id_contacto?>&id_plantilla=2'" style="font-size: 10px"<?=$estado_boton;?>><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#efefef">
                        <td colspan=2><font size=1 face="<?= $fuente_general; ?>">&nbsp;</FONT></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela donde asistirá:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="id_centro_contacto" size=1 style="font-size: 10px">
                        <option value="0">Sin Escuela Seleccionada</option>
                        <?php 
                        $consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=0 OR id_centro=? ORDER BY nombre_centro;', 's', array($centro_actual));
                        while($centros=db_fetch_array($consulta_centros)){
                        ?>
                        <option value="<?=$centros["id_centro"];?>"<?php  if ($centros["id_centro"]==$id_centro_contacto) { print " selected"; } ?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                        <?php 
                        }
                        ?>
                        </select><?=$info_aula_alumno?></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Curso en el que se inscribirá:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="id_curso_contacto" size=1 style="font-size: 10px">
                        <option value="0">Sin Curso Seleccionado</option>
                        <?php 
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE estado<3 ORDER BY fecha_inicio") or die(db_error()."-4");
                        while($cursos=db_fetch_array($consulta_cursos)){
                        ?>
                        <option value="<?=$cursos["id_curso"];?>"<?php  if ($cursos["id_curso"]==$id_curso_contacto) { print " selected"; } ?>><?=$cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select> <input type="button"<?=$estado_inscripcion;?> value="Inscribir" onclick="inscribir_alumno()" style="font-size: 10px"> <input type="button" value="Inscribir lista espera" onclick="inscribir_espera()" style="font-size: 10px"></td>
                        </tr>
						<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de contacto:</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <select name="tipo_alumno" size="1" style="font-size: 10px">
                                <option value="0"<?php  if ($tipo_alumno==0) { print " selected"; } ?>>Bebes/Infantil</option>
                                <option value="1"<?php  if ($tipo_alumno==1) { print " selected"; } ?>>Aquagym</option>
                                <option value="2"<?php  if ($tipo_alumno==2) { print " selected"; } ?>>Matronatación</option>
                                </select></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Horario deseado:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="horarios_quiere_asistir" cols="77" rows=2 style="font-family: Arial; font-size: 11px"><?=app_escape($horarios_quiere_asistir)?></textarea></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Días de la semana en que desea asistir:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="dias_quiere_asistir" cols="77" rows=2 style="font-family: Arial; font-size: 11px"><?=app_escape($dias_quiere_asistir)?></textarea></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Semanas embarazo (solo matronatación):</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select id="semanas_embarazo" name="semanas_embarazo" size="1" style="font-size: 10px">
                        <option value="0"></option>
                        <?php 
                        for($semanas=1;$semanas<=40;$semanas++) {
                        ?>
                        <option value="<?=$semanas?>"<?php  if ($semanas_embarazo==$semanas) { print " selected"; } ?>><?=$semanas?></option>
                        <?php 
                        }
                        ?>
            	</select></font></td>
                        </tr>
                        
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Nuevo alumno?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="nuevo_alumno" value="1"<?php  if ($nuevo_alumno==1) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="nuevo_alumno" value="0"<?php  if ($nuevo_alumno==0) { print " checked"; } ?>> No</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Antiguo alumno Lápices?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="antiguo_alumno_lapices" value="1"<?php  if ($antiguo_alumno_lapices==1) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="antiguo_alumno_lapices" value="0"<?php  if ($antiguo_alumno_lapices==0) { print " checked"; } ?>> No</font><br /><br /><input type="button" onclick="buscar_alumno_lapices('lapices')" value="Buscar datos alumno Lápices" style="font-size: 10px"> <input type="button" onclick="buscar_alumno_lapices('santpau')" value="Buscar datos alumno Sant Pau" style="font-size: 10px"></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Cómo nos ha conocido?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <select name="como_conocio" size="1" style="font-size: 10px">
                                <?php 
                                $busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
                                while($datos_tipos=db_fetch_array($busca_tipos)) {
                                ?>
                                <option value="<?=$datos_tipos["id_tipo"]?>"<?php  if ($como_conocio==$datos_tipos["id_tipo"]) { print " selected"; } ?>><?=$datos_tipos["nombre_tipo"]?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
<tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha prevista información</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_informacion_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_informacion_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_informacion_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_informacion_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_informacion_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=date("Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_informacion_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_informacion');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr valign="top" bgcolor="#ffefef">
                        <td colspan=2 align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Rellenar el caso de que el alumno no llegue a matricularse en la escuela. Indicar el motivo.</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#ffefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Motivo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <select name="motivo_cancelacion" size="1" style="font-size: 10px">
                                <option value="0"></option>
                                <?php 
                                $busca_tipos=db_query("SELECT * FROM contactos_motivos_cancelacion ORDER BY nombre_motivo;");
                                while($datos_tipos=db_fetch_array($busca_tipos)) {
                                ?>
                                <option value="<?=$datos_tipos["id_motivo"]?>"<?php  if ($motivo_cancelacion==$datos_tipos["id_motivo"]) { print " selected"; } ?>><?=urldecode($datos_tipos["nombre_motivo"]);?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Si ya disponemos de sus datos (por que se trata de un alumno de otros cursos o bien porque ya lo hemos inscrito) debemos seleccionarlos para evitar duplicar alumnos en la base de datos.</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Datos alumno:</b></font></td>
                                <input type="hidden" name="id_usuario" id="id_usuario" value="<?=app_escape($id_usuario)?>">
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" readonly name="texto_id_usuario" value="<?=app_escape($texto_id_usuario)?>" size="38" style="font-size: 10px"><br /> <input type="button"<?=$estado_inscripcion;?> onclick="buscar_alumno()" value="Buscar datos" style="font-size: 10px"> <input type="button"<?=$estado_inscripcion;?> onclick="borrar_alumno()" value="Borrar datos" style="font-size: 10px"> <input type="button" onclick="copiar_alumno()" value="Copiar datos de contacto al alumno" style="font-size: 10px"></font></td>
                        </tr>
                        
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre y apellidos del alumno:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre" value="<?=app_escape($nombre_alu)?>" size="25" style="font-size: 10px"> <input type="text" name="apellido1" value="<?=app_escape($apellido1_alu)?>" size="25" style="font-size: 10px"> <input type="text" name="apellido2" value="<?=app_escape($apellido2_alu)?>" size="25" style="font-size: 10px"></font> <font size=1 color="#bcbcbc"><?=app_escape($nombre_alu." ".$apellido1_alu." ".$apellido2_alu)?></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="sexo" value="V"<?php  if ($sexo=="V") { print " checked"; } ?>> Varón&nbsp; &nbsp;<input type="radio" name="sexo" value="H"<?php  if ($sexo=="H") { print " checked"; } ?>>Hembra </font></td>
                        </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right" rowspan=2 valign="top"><font face="<?= $fuente_general; ?>" size="1"><b>Fecha de nacimiento</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_nacimiento_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_nacimiento_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_nacimiento_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_nacimiento_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_nacimiento_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad-100; $c_ano<=date("Y")+7; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_nacimiento_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_nacimiento');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>  &nbsp;&nbsp;&nbsp;<font size=1 color="#bcbcbc"><?=($fecha_nacimiento_dia1!==''?$fecha_nacimiento_dia1.'/'.$fecha_nacimiento_mes.'/'.$fecha_nacimiento_ano:'')?></font>
                                </td>
                                </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td><font size="1" face="<?= $fuente_general; ?>">
                                <input type="checkbox" name="nonato" value="1"<?php  if ($usuario_nonato=="1") { print " checked"; } ?>> Todavía no ha nacido<br>
                                <input type="checkbox" name="prematuro" value="1"<?php  if ($usuario_prematuro=="1") { print " checked"; } ?>> Prematuro/a (8 meses)<br>
                                <input type="checkbox" name="prematuro1" value="1"<?php  if ($usuario_prematuro1=="1") { print " checked"; } ?>> Prematuro/a (7 meses)<br>
                                </font></td>
                        </tr>
                       <?php 
                       if ($edad!="") {
                       ?>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Edad actual:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=app_escape($edad)?></font></td>
                        </tr>
                       <?php 
                       }
                       ?>                          
                        
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos de los contactos (padre, madre, tutores...)</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Persona de contacto (Padre):</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="persona_contacto" value="<?=app_escape($persona_contacto)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($persona_contacto)?></font></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Relación de parentesco con el alumno:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="parentesco_padre1" value="" size=1 style="font-size: 10px">
                                                        <option value="0"></option>
                                                        <option value="1"<?php  if ($parentesco_padre1==1) { print " selected"; } ?>>Padre/Madre</option>
                                                        <option value="2"<?php  if ($parentesco_padre1==2) { print " selected"; } ?>>Abuelo/a</option>
                                                        <option value="3"<?php  if ($parentesco_padre1==3) { print " selected"; } ?>>Tio/a</option>
                                                        <option value="4"<?php  if ($parentesco_padre1==4) { print " selected"; } ?>>Autorizado/a</option>
                                                        </select></font></td>
                        </tr>                      
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono de contacto 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono" value="<?=app_escape($telefono)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($telefono)?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email contacto 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email" value="<?=app_escape($email)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($email)?></font></font></td>
                        </tr>   
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>DNI contacto 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="dni_padre1" value="<?=app_escape($dni_padre1)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($dni_padre1)?></font></font></td>
                        </tr> 
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_padre2" value="<?=app_escape($nombre_padre2)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($nombre_padre2)?></font></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Relación de parentesco con el alumno:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="parentesco_padre2" value="" size=1 style="font-size: 10px">
                                                        <option value="0"></option>
                                                        <option value="1"<?php  if ($parentesco_padre2==1) { print " selected"; } ?>>Padre/Madre</option>
                                                        <option value="2"<?php  if ($parentesco_padre2==2) { print " selected"; } ?>>Abuelo/a</option>
                                                        <option value="3"<?php  if ($parentesco_padre2==3) { print " selected"; } ?>>Tio/a</option>
                                                        <option value="4"<?php  if ($parentesco_padre2==4) { print " selected"; } ?>>Autorizado/a</option>
                                                        </select></font></td>
                        </tr>                        
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono de contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_padre2" value="<?=app_escape($telefono_padre2)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($telefono_padre2)?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email_padre2" value="<?=app_escape($email_padre2)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($email_padre2)?></font></font></td>
                        </tr>   
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>DNI contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="dni_padre2" value="<?=app_escape($dni_padre2)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($dni_padre2)?></font></font></td>
                        </tr>  
						<tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos familiares</b></font></td>
                        </tr>                                                                   
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Calle y Número:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="calle" value="<?=app_escape($calle)?>" size="80" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($calle)?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Población:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="poblacion" value="<?=app_escape($poblacion)?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($poblacion)?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Código postal:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="codigo_postal" value="<?=app_escape($codigo_postal)?>" size="10" style="font-size: 10px"> <font color="#bcbcbc"><?=app_escape($codigo_postal)?></font></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 align="center"><input type="button" value="Localizar domicilio en el mapa" onclick="localiza();"><br><div id="map_canvas" style="width:450; height:300"></div></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfonos familia:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefonos_familia" value="<?=app_escape($telefonos_familia)?>" size="50" style="font-size: 10px"></font> <font color="#bcbcbc"><?=app_escape($telefonos_familia)?></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email familia:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email_familia" value="<?=app_escape($email_familia)?>" size="50" style="font-size: 10px"></font> <font color="#bcbcbc"><?=app_escape($email_familia)?></font></td>
                        </tr>
                        
<tr valign="top">
                        <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Contactos emergencia</b></font></td>
                        </tr>                                                                   
                       
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre contacto emergencia 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="contacto_emergencia1" value="<?=app_escape($contacto_emergencia1)?>" size="50" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono contacto emergencia 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_contacto_emergencia1" value="<?=app_escape($telefono_contacto_emergencia1)?>" size="50" style="font-size: 10px"></font></td>
                        </tr>                          
                               
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre contacto emergencia 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="contacto_emergencia2" value="<?=app_escape($contacto_emergencia2)?>" size="50" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono contacto emergencia 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_contacto_emergencia2" value="<?=app_escape($telefono_contacto_emergencia2)?>" size="50" style="font-size: 10px"></font></td>
                        </tr>                                
                          
<tr valign="top">
                        <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Otra información</b></font></td>
                        </tr>                          

<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Persona encargada de recoger al niño/a:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="persona_autorizada" value="<?=app_escape($persona_autorizada)?>" size="50" style="font-size: 10px"></font></td>
                        </tr>  
           
                                                                       
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Recibe publicidad?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="no_publicidad" value="0"<?php  if ($no_publicidad==0) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="no_publicidad" value="1"<?php  if ($no_publicidad==1) { print " checked"; } ?>> No</font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Comentarios:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="comentarios" cols="77" rows=6 style="font-family: Arial; font-size: 11px"><?=app_escape($comentarios)?></textarea></font></td>
                        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Estado Actual del proceso de decisión:</b></font></td>
        <td><select name="proceso_decision" class="defaut" style="font-size: 10px">
        <option value="b1"<?php  if ($proceso_decision=="b1") { print " selected"; } ?>>B1</option>
        <option value="b2"<?php  if ($proceso_decision=="b2") { print " selected"; } ?>>B2</option>
        <option value="b3"<?php  if ($proceso_decision=="b3") { print " selected"; } ?>>B3</option>
        <option value="c"<?php  if ($proceso_decision=="c") { print " selected"; } ?>>C</option>
        </select></td>
        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Fechas significativas</b></font></td>
                        </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Primer contacto B1</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_b1_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_b1_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b1_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_b1_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b1_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=date("Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_b1_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_b1');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Cambio a B2</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_b2_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_b2_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b2_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_b2_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b2_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=date("Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_b2_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_b2');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Cambio a B3</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_b3_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_b3_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b3_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_b3_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b3_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=date("Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_b3_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('fecha_b3');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Cambio a C</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_c_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_c_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_c_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_c_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_c_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=date("Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_c_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('fecha_c');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <?php 
                        if (strlen($id_contacto)!=0) {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Notas de seguimiento</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_notas=db_query_params('SELECT * FROM contactos_seguimiento, usuarios WHERE contactos_seguimiento.id_usuario=usuarios.id_usuario AND contactos_seguimiento.id_contacto=? ORDER BY contactos_seguimiento.fecha', 's', array($id_contacto)) or die(db_error());
                                ?><br>
                                <table width="98%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="18%" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Fechas</b></font></td>
                                                <td align="center" width="82%" colspan=4><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        </tr>

                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="9%"><font size=1 face="<?= $fuente_general; ?>"><b>Creación</b></font></td>
                                                <td align="center" width="9%"><font size=1 face="<?= $fuente_general; ?>"><b>Modificación</b></font></td>
                                                <td align="left" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Introducida por...</b></font></td>
                                                <td align="center" width="54%"><font size=1 face="<?= $fuente_general; ?>"><b>Comentarios</b></font></td>
                                                <td align="center" width="4%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                                <td align="center" width="4%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        </tr>
                                <?php 
                                if (db_num_rows($busca_notas)==0) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center" colspan=6><font size=2 face="<?= $fuente_general; ?>"><b>No se han introducido notas de seguimiento, haz clic sobre el botón <i>Agregar notas</i> para añadirlas.</b></font></td>
                                </tr>
                                <?php 
                                }

                                while($datos_notas=db_fetch_array($busca_notas)) {
                                $nombre_completo_usuario_notas=$datos_notas["Apellido1"]." ".$datos_notas["Apellido2"].", ".$datos_notas["Nombre"];
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_notas["fecha"]));?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_notas["fecha_modificacion"]));?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_completo_usuario_notas;?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_notas["comentarios"];?></font></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a title="Modificar la nota" href="javascript:editar_nota(<?=$datos_notas["id_nota"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a title="Eliminar de la lista" href="javascript:borrar_nota(<?=$datos_notas["id_nota"];?>,'')"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="nueva_nota()" style="font-size: 10px" value="Agregar notas"><br><br>
                                </td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                        <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php  require("../configuracion/parte_inferior.php"); ?>
