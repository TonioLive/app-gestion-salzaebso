<?php  require("../../configuracion/parte_superior_docente.php"); ?>
    <div align="center">
    <?php 
    $id_alumno=$_GET["id_alumno"];
    $id_mensaje=$_GET["id_mensaje"];
      
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE padre_de=?', 's', array($id_alumno));
    while($padres=db_fetch_array($busca_padres)){
    	$filtro_padres.=" OR respondido_por=".$padres["id_usuario"];
    	}
    if ($filtro_padres!="") {
    	$filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
    	}
     
    // Datos alumno
    $busca_alumno=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_alumno));  
    
    $datos_alumno=db_fetch_array($busca_alumno);     
    
    $nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];    
                  
                  
    // Busca mensajes
	$tipos_mensajes=array("Recibido","Medicación","Enviado");
	$subtipos_mensajes=array(" para la educadora"," para la coordinadora");
	$tipos_acciones_mensaje=array("Recibido","Introducida","Enviado");
	$nombres_tipos_mensajes=array("Mensaje Recibido","Instrucciones de Medicación","Mensaje Enviado");
	$nombres_tipos_mensajes=array("Mensaje recibido","Instrucciones","Mensaje");
	$estados_mensajes=array("No leido","Leido","Cerrado");

    $formulario_name="formulario_mensaje";              
    ?>
    <br>
 	<div align="left"><font size=3><b>Nuevo mensaje para <?=$nombre_completo_alumno?></b></font></div><br>
 	<table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
 	<form name="<?=$formulario_name;?>" action="nuevo_mensaje1.php" method="POST">
 	<input type="hidden" name="id_mensaje" value="<?=$id_mensaje?>">
 	<input type="hidden" name="id_alumno" value="<?=$id_alumno?>">
 	<tr>
 	<td bgcolor="#efefef" align="right"><font size=2><b>Asunto:</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><input type="text" name="asunto" value="" placeholder="Escribe aquí el asunto" size=90></font></td>
 	</tr>
 	<tr valign="top">
 	<td bgcolor="#efefef" align="right"><font size=2><b>Mensaje:</b></font></td>
 	<td bgcolor="#ffffff" align="left"><font size=2><textarea id="texto_mensaje" name="texto_mensaje" placeholder="Escribe aquí tu mensaje o instrucciones para la educadora" style="font-size: 12px; font-family: Arial" cols="115" rows="16"></textarea></font></td>
 	</tr>
 	</table><br>
 	<input type="button" value="Cancelar" onclick="window.location.href='mensajeria.php?id_alumno=<?=$id_alumno;?>'" style="font-size: 10px"> <input type="button" onclick="enviar_mensaje()" value="Enviar" style="font-size: 10px">
 	</form>	
 	<br>
 <script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">
function enviar_mensaje() {

                var errores=0;

                var mensajes_error="";
             
                if (self.document.<?=$formulario_name;?>.asunto.value=="") {
                         mensajes_error=mensajes_error+"No has escrito el asunto del mensaje.\u000A";
						 errores=1;
                        } 
                if (self.document.<?=$formulario_name;?>.texto_mensaje.value=="") {
                         mensajes_error=mensajes_error+"No has escrito el texto del mensaje.\u000A";
						 errores=1;
                        }  
                          
       
                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }
</script>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
