<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php  require("../configuracion/autentifica_admin.php");

$paso_anterior=4;
$paso_actual=1;
$paso_siguiente=11;

$accion_texto="Editar";
if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["u"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["u"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso".$paso_anterior.".php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query_params('UPDATE usuarios SET fecha_edicion=? WHERE id_usuario=?', 'ss', array((string)(strftime("%Y/%m/%d")), $id_usuario_seleccionado)) or die(db_error()."-1");
                             $formulario_enviado=0;
                             }
        if ($resultado!="") {
                ?>
                <script language="Javascript">
                alert("<?=$resultado;?>\n\nPara ver como ha quedado la distribución de las aulas, accede al menú Aulas de la página principal.");
                </script>
                <?php 
                }
        }
$usuario_nacionalidad="ESPAÑOLA";
if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);

                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                // Datos del paso
                $usuario_sexo=$datos_usuario["sexo"];
                $usuario_fechanac_dia=date("j",strtotime($datos_usuario["fecha_nacimiento"]));
                $usuario_fechanac_mes=date("m",strtotime($datos_usuario["fecha_nacimiento"]));
                $usuario_fechanac_ano=date("Y",strtotime($datos_usuario["fecha_nacimiento"]));
                $usuario_nonato=$datos_usuario["nonato"];
                $usuario_prematuro=$datos_usuario["prematuro"];
                $usuario_prematuro1=$datos_usuario["prematuro1"];
                if ($usuario_fechanac_ano==1970) {
                        $usuario_fechanac_dia="";
                        $usuario_fechanac_mes="";
                        $usuario_fechanac_ano="";
                        }
                $usuario_direccion=$datos_usuario["Direccion"];
                $usuario_cp=$datos_usuario["cp"];
                $usuario_poblacion=$datos_usuario["Poblacion"];
                $usuario_provincia=$datos_usuario["Provincia"];
                $usuario_telefono1=$datos_usuario["telefono1"];
                $usuario_telefono2=$datos_usuario["telefono2"];
                $usuario_movil=$datos_usuario["movil"];
                $usuario_email=$datos_usuario["email"];
                $usuario_nacionalidad=$datos_usuario["nacionalidad"];

				$sip=$datos_usuario["SIP"];

                $usuario_coordenadas=$datos_usuario["coordenadas"];
                $direccion_complera=$datos_usuario["Direccion"]." ".$datos_usuario["cp"]." ".$datos_usuario["Poblacion"];
                
                }
        }

if (substr($usuario_coordenadas,0,1)!="(") {
	$usuario_coordenadas="(39.4079665, -0.5015968)";	
}

$patterns = array('/\)/','/\(/','/ /');
$espacios = array('','','');
$LatLongMapa=preg_replace($patterns,$espacios,$usuario_coordenadas);

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_alumno".$paso_siguiente.".php?u=".$id_usuario_seleccionado;

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
  var id_usuario=<?=$id_usuario_seleccionado?>;
  var query = "<?=$direccion_complera?>";
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
<?php 


// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
  
?>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_usuario_seleccionado;?>">
<input type="hidden" name="formulario_enviado" value="1">
<input type="hidden" name="coordenadas" value="<?=$usuario_coordenadas?>">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> Alumno -  Paso 1 de 4</b></font><br><br>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr bgcolor="#EFEFEF" valign="top">
                                <?php  if ($accion_texto=="Editar") { ?>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno1.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font></a><br><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno11.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;&nbsp;1.1. Insertar Foto</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno2.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno3.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Información adicional</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno4.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">4. Inscripciones</font></a></td>
                                <?php  } else { ?>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#1D50C3">1. Datos personales</font><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2"><br>&nbsp;&nbsp;1.1. Insertar Foto</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">2. Padres y personas autorizadas</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">3. Información adicional</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">4. Inscripciones</font></td>
                                <?php  } ?>
                                </tr>
                        </table><BR>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td rowspan=7 align="center" valign="middle" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos personales</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="Nombre" value="<?=$usuario_nombre;?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Apellidos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="Apellido1" value="<?=$usuario_apellido1;?>"> <input type="text" name="Apellido2" value="<?=$usuario_apellido2;?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="2" face="<?= $fuente_general; ?>"> <input type="radio" name="sexo" value="V"<?php  if ($usuario_sexo=="V") { print " checked"; } ?>> Varón&nbsp; &nbsp;<input type="radio" name="sexo" value="H"<?php  if ($usuario_sexo=="H") { print " checked"; } ?>>Hembra </font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nº SIP</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="sip" value="<?=$sip;?>"></font></td>
                        </tr>                        
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nacionalidad</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nacionalidad" value="<?=$usuario_nacionalidad;?>"></font></td>
                        </tr>
                        <tr>
                                <td width=210 align="right" rowspan=2><font face="<?= $fuente_general; ?>" size="2"><b>Fecha Nacimiento</b></font></td>
                                <td><select name="usuario_fechanac_dia" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($usuario_fechanac_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechanac_mes" class="defaut">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($usuario_fechanac_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="usuario_fechanac_ano" class="defaut">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+1; $c_ano>=1899; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($usuario_fechanac_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>
                                </td>
                    </tr>
                        <tr valign="top">
                                <td><font size="2" face="<?= $fuente_general; ?>">
                                <input type="checkbox" name="nonato" value="1"<?php  if ($usuario_nonato=="1") { print " checked"; } ?>> Todavía no ha nacido<br>
                                <input type="checkbox" name="prematuro" value="1"<?php  if ($usuario_prematuro=="1") { print " checked"; } ?>> Prematuro/a (8 meses)<br>
                                <input type="checkbox" name="prematuro1" value="1"<?php  if ($usuario_prematuro1=="1") { print " checked"; } ?>> Prematuro/a (7 meses)<br>
                                </font></td>
                        </tr>
                    <tr valign="top">
                                <td colspan=3 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Información de contacto</b></font></td>
                    </tr>
                    <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Domicilio familiar</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="Direccion" size=80 value="<?=$usuario_direccion;?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Código postal</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="CP" size=5 value="<?=$usuario_cp;?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="poblacion" size=50 value="<?=$usuario_poblacion;?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Provincia</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="provincia" class="defaut">
                                <option value="">- Seleccione la provincia donde reside -</option>
                                <?php 
                                $provincia=$usuario_provincia;
                                $busca_provincia=db_query("SELECT * FROM provincias ORDER BY id_provincia");
                                if (strlen($provincia)==0) { $provincia=46; }
                                while($lista_provincias=db_fetch_array($busca_provincia)) {
                                        print "<option value=".$lista_provincias["id_provincia"];
                                                if (intval($lista_provincias["id_provincia"])==intval($provincia)) { print " selected"; }
                                                                           print ">".$lista_provincias["provincia"]."</option>";
                                                                        }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=3 align="center"><input type="button" value="Localizar domicilio en el mapa" onclick="localiza();"><br><div id="map_canvas" style="width:450; height:300"></div></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Teléfonos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono1" size=50 value="<?=$usuario_telefono1;?>"> <input type="text" name="telefono2" size=50 value="<?=$usuario_telefono2;?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Móvil</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="movil" name="movil" size=50 value="<?=$usuario_movil;?>"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Email</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email" size=50 value="<?=$usuario_email;?>"></font></td>
                        </tr>
                         </table>
                         <br><input type="button" onclick="cambia(11);" value="Guardar cambios y siguiente paso >>"><br><br>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
</form>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">

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

        query=document.<?=$formulario_name?>.Direccion.value+" "+document.<?=$formulario_name?>.CP.value+" "+document.<?=$formulario_name?>.poblacion.value;
        if ((document.<?=$formulario_name?>.Direccion.value!="")&&(document.<?=$formulario_name?>.CP.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                codeAddress();
                } else {
                alert("Escribe antes la dirección: Cale, Número Código postal y población");
                }

        }

//acá cada uno crea el objeto a su manera

var pagina_destino="<?=$dominio_actual?>/contactos/guardar_coordenadas.php";

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

function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
function cambia(mostrar) {
        if (mostrar!=<?=$paso_actual;?>) {
                var errores=0;

                var mensajes_error="";
                if (self.document.<?=$formulario_name;?>.Nombre.value=="") { mensajes_error=mensajes_error+"Es obligatorio introducir el nombre.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.Apellido1.value=="") { mensajes_error=mensajes_error+"Hay que introducir el primer apellido.\u000A";errores=1; }

                if ((self.document.<?=$formulario_name;?>.sexo[0].checked==false)&&(self.document.<?=$formulario_name;?>.sexo[1].checked==false)) { mensajes_error=mensajes_error+"Es necesario indicar el sexo del alumno.\u000A";errores=1; }
                // if (self.document.<?=$formulario_name;?>.Direccion.value=="") { mensajes_error=mensajes_error+"Falta introducir la dirección.\u000A";errores=1; }
                // if (self.document.<?=$formulario_name;?>.CP.value=="") { mensajes_error=mensajes_error+"No se ha introducido el código postal.\u000A";errores=1; }
                // if (self.document.<?=$formulario_name;?>.poblacion.value=="") { mensajes_error=mensajes_error+"Es obligatoio introducir la población.\u000A";errores=1; }
                // if (self.document.<?=$formulario_name;?>.provincia.value=="") { mensajes_error=mensajes_error+"Hay que indicar la provincia de residencia.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono1.value=="")&&(self.document.<?=$formulario_name;?>.telefono2.value=="")&&(self.document.<?=$formulario_name;?>.movil.value=="")) { mensajes_error=mensajes_error+"Es necesario disponer de un teléfono de contacto.\u000A";errores=1; }

                // Comprueba fecha
                if (self.document.<?=$formulario_name;?>.nonato.checked) {
                        // No ha nacido
                        } else {
                        la_fecha=self.document.<?=$formulario_name;?>.usuario_fechanac_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechanac_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechanac_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de nacimiento introducida no es correcta.\u000A";errores=1; }
                        }
                
        query=document.<?=$formulario_name?>.Direccion.value+" "+document.<?=$formulario_name?>.CP.value+" "+document.<?=$formulario_name?>.poblacion.value;
        if ((document.<?=$formulario_name?>.Direccion.value!="")&&(document.<?=$formulario_name?>.CP.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                codeAddress();
                } else {
                mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                }
                
                if (errores==0) {
	                
	                	if (self.document.<?=$formulario_name;?>.sip.value=="") {
		                	alert("El Nº SIP no es obligatorio, en este momento, pero cuando vayas a matricular al alumno en un nuevo curso se exigirá y no podrás continuar si no rellenas este dato.");
	                	}	                
	                
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000ANo puedes continuar sin corregir los errores.");
                        }
                }
        }
//-->
</script>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
