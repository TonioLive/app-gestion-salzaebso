<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
         
   if ($errores=="") {
	   
	$id_autorizado=(int) app_request_value($_GET, "id_autorizado", 0);  
	$id_hijo=(int) app_request_value($_GET, "id_hijo", 0);	 
	
	$usuario_provincia=46;
	
	if ($id_autorizado!=0) { 
		 
		// Busca datos padre
				
		$busca_datos_padre=db_query_params("SELECT * FROM padres LEFT JOIN usuarios ON padres.id_usuario=usuarios.id_usuario WHERE id_padre=? AND padre_de=? LIMIT 1", "ii", array($id_autorizado, $id_hijo)); 
		
		if (db_num_rows($busca_datos_padre)==0) {
		
			exit("Se ha detectado una actividad sospechosa. No tienes permiso para acceder a los datos que solicitas.");	
			
		}
		
		$datos_padre=db_fetch_array($busca_datos_padre);
		
		if (($datos_padre["parentesco"]==1) and ($datos_padre["id_usuario"]!=$id_usuario)) {
			
			exit("Se ha detectado una actividad sospechosa. No tienes permiso para acceder a los datos que solicitas.");
				
		}
		
		$id_padre=$datos_padre["id_usuario"];
		
        // Datos Fijos
        $usuario_nombre=$datos_padre["Nombre"];
        $usuario_apellido1=$datos_padre["Apellido1"];
        $usuario_apellido2=$datos_padre["Apellido2"];

        // Datos del paso
        $usuario_sexo=$datos_padre["sexo"];
        $usuario_fechanac_dia=date("j",strtotime($datos_padre["fecha_nacimiento"]));
        $usuario_fechanac_mes=date("m",strtotime($datos_padre["fecha_nacimiento"]));
        $usuario_fechanac_ano=date("Y",strtotime($datos_padre["fecha_nacimiento"]));
        $usuario_direccion=$datos_padre["Direccion"];
        $usuario_cp=$datos_padre["cp"];
        $usuario_poblacion=$datos_padre["Poblacion"];
        $usuario_provincia=$datos_padre["Provincia"];
        $usuario_telefono1=$datos_padre["telefono1"];
        $usuario_telefono2=$datos_padre["telefono2"];
        $usuario_movil=$datos_padre["movil"];
        $usuario_email=$datos_padre["email"];
        $usuario_DNI=$datos_padre["DNI"];
        $usuario_profesion=$datos_padre["profesion"];
        $parentesco=$datos_padre["parentesco"];	
	
	} 
	  
	// Datos para el formulario
	$formulario_name = "formulario1";
	$formulario_method = "POST";
	$formulario_action = "autorizado_editar1.php";		  	  
	  
	  
	 
	
// Busca hijos del padre
    $busca_datos_alumno=db_query_params("SELECT * FROM usuarios WHERE id_usuario=? LIMIT 1", "i", array($id_hijo));

    if (db_num_rows($busca_datos_alumno)!=0) {

		$datos_hijos=db_fetch_array($busca_datos_alumno);
	
		$nombre_alumno=$datos_hijos["Nombre"]; 
		
	}		
	   	   
    ?>
    <div align="center">	    	    
        <h3>Editar datos de persona autorizada para recoger a <?=$nombre_alumno?></h3>
        
        <form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
	        <input type="hidden" name="_csrf" value="<?=htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8')?>">
	        <input type="hidden" name="id_autorizado" value="<?=$id_autorizado?>">
	        <input type="hidden" name="id_usu_autorizado" value="<?=$id_padre?>">
	        <input type="hidden" name="id_hijo" value="<?=$id_hijo?>">
	        
			<table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
				
				<tr valign="top">
                    <td width=210 align="right"><b>Relación de parentesco con <?=$nombre_alumno?></b></td>
                    <td>
						<select name="parentesco<?=$contador_relaciones;?>" value="" size=1>
	                        <option value="0"></option>
	                        <option value="1"<?php  if ($parentesco==1) { print " selected"; } ?>>Padre/Madre</option>
	                        <option value="2"<?php  if ($parentesco==2) { print " selected"; } ?>>Abuelo/a</option>
	                        <option value="3"<?php  if ($parentesco==3) { print " selected"; } ?>>Tio/a</option>
	                        <option value="4"<?php  if ($parentesco==4) { print " selected"; } ?>>Autorizado/a</option>
	                    </select>
					</td>
            	<tr valign="top">                                
                    <td colspan=2 align="center" bgcolor="#efefef"><b>Datos personales</b></td>
            	</tr>
				<tr valign="top">
                    <td width=210 align="right"><b>Nombre</b></td>
                    <td><input type="text" name="Nombre" value="<?=$usuario_nombre;?>"></td>
            	</tr>
				<tr valign="top">
                    <td width=210 align="right"><b>Apellidos</b></td>
                    <td><input type="text" name="Apellido1" value="<?=$usuario_apellido1;?>"> <input type="text" name="Apellido2" value="<?=$usuario_apellido2;?>"></td>
            	</tr>
				<tr valign="top">
                    <td width=210 align="right"><b>DNI/NIE:</b></td>
                    <td><input type="text" name="DNI" value="<?=$usuario_DNI;?>"></td>
            	</tr>
				<tr valign="top">
                    <td colspan=2 align="center" bgcolor="#efefef"><b>Información de contacto</b></td>
            	</tr>
				<tr valign="top">
                    <td width=310 align="right"><b>Domicilio</b></td>
                    <td><input type="text" name="Direccion" size=50 value="<?=$usuario_direccion;?>"></td>
            	</tr>
				<tr valign="top">
                    <td width=310 align="right"><b>Código postal</b></td>
                    <td><input type="text" name="CP" size=5 value="<?=$usuario_cp;?>"></td>
            	</tr>
				<tr valign="top">
                    <td width=310 align="right"><b>Población</b></td>
                    <td><input type="text" name="poblacion" size=50 value="<?=$usuario_poblacion;?>"></td>
            	</tr>
				<tr valign="top">
                    <td width=310 align="right"><b>Provincia</b></td>
                    <td><select name="provincia">
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
                    </select></td>
            	</tr>
				<tr valign="top">
                    <td width=310 align="right"><b>Teléfonos</b></td>
                    <td><input type="text" name="telefono1" value="<?=$usuario_telefono1;?>"> <input type="text" name="telefono2" value="<?=$usuario_telefono2;?>"></td>
            	</tr>
				<tr valign="top">
                    <td width=310 align="right"><b>Móvil</b></td>
                    <td><input type="movil" name="movil" size=50 value="<?=$usuario_movil;?>"></td>
            	</tr>
				<tr valign="top">
                    <td width=310 align="right"><b>Email</b></td>
                    <td><input type="text" name="email" size=50 value="<?=$usuario_email;?>"></td>
            	</tr>
        </table>  
                       
        </form>
        
		<br />
            
        <input type="button" onclick="cambia()" value="Enviar">        
        
        <br />
    <?php 
    }
    ?>
    </div>
    
    <a href="autorizados.php?id_usuario=<?=$id_usuario?>&id_usuhijo=<?=$id_hijo?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_facturas_act.gif',1)"><img src="/webcam/imagenes/salir_facturas_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a>  
    
<script Language="JavaScript">

	function cambia() {
                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.parentesco.value==0) { mensajes_error=mensajes_error+"Debes seleccionar la relación de parentesco de esta persona con <?=$nombre_alumno?>.\u000A";errores=1; }
                
                if (self.document.<?=$formulario_name;?>.Nombre.value=="") { mensajes_error=mensajes_error+"Es obligatorio introducir el nombre.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.Apellido1.value=="") { mensajes_error=mensajes_error+"Hay que introducir el primer apellido.\u000A";errores=1; }

                if (self.document.<?=$formulario_name;?>.DNI.value=="") { mensajes_error=mensajes_error+"Hay que introducir el número del DNI o NIE de la persona que va a recoger a <?=$nombre_alumno?>.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono1.value=="")&&(self.document.<?=$formulario_name;?>.telefono2.value=="")&&(self.document.<?=$formulario_name;?>.movil.value=="")) { mensajes_error=mensajes_error+"Es necesario disponer de un teléfono de contacto.\u000A";errores=1; }

                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
//-->
</script>
      
    
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
