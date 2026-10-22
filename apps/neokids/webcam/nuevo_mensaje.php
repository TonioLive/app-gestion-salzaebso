<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
<div class="jumbotron fondo_2">
  <div class="container">
    <div class="row"> <!-- Menú -->
    	<?php  include("menu_lateral.php"); ?>
    </div>
    <div class="row fondo_1"> <!-- Contenido -->
    	<div class="col-md-12"> <!-- Columna principal -->    
    <?php                       
                  
    // Busca mensajes
	$tipos_mensajes=array("Enviado","Medicación","Recibido");
	$tipos_acciones_mensaje=array("Enviado","Introducida","Recibido");
	$nombres_tipos_mensajes=array("Mensaje Enviado","Instrucciones de Medicación","Mensaje Recibido");
	$nombres_tipos_mensajes=array("Mensaje","Instrucciones","Mensaje");
	$estados_mensajes=array("No leido","Leido","Cerrado");

    $formulario_name="formulario_mensaje";              
    ?>
 	<h3>Nuevo mensaje</h3>
    <div align="center"> 	
 	<table width=98%  class="tabla_datos" align="center">
 	<form name="<?=$formulario_name;?>" action="nuevo_mensaje1.php" method="POST">
 	<input type="hidden" name="id_mensaje" value="<?=$id_mensaje?>">
 	<input type="hidden" name="id_remitente" value="<?=$id_usuario?>">
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right" width="73"><b>Para:</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left">&nbsp;<select size=1 style="font-size: 14px" name="destinatario">
 	<?php 
    // Busca destinatarios (docentes de las aulas de sus hijos)
	$busca_datos_alumno=db_query_params('SELECT *,usuarios.Nombre AS nombre_hijo FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.id_curso=? ORDER BY alumnos.id_curso DESC', 'ssss', array($id_usuario, (string)(date("Y-m-d")), (string)(date("Y-m-d")), $id_curso_actual)) or die(db_error()."- No se encuentran los datos del alumno.") or die(db_error());

	$array_centros_alumno=array();

    if (db_num_rows($busca_datos_alumno)!=0) {
      	while($datos_alumno=db_fetch_array($busca_datos_alumno)) {
      	
        if ($datos_alumno["sexo"]=="V") {
                        $sufijo_hijo="o";
                        } else {
                        $sufijo_hijo="a";
                        }
        
        // Finalmente muestra las cámaras
        
        // Datos de la escuela del niño
        $busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_alumno["id_centro"]));
        
        $datos_escuela=db_fetch_array($busca_datos_escuela);
        ?>
        <option value="A<?=$datos_alumno["padre_de"];?>">Monitor/a de <?=$datos_alumno["nombre_hijo"];?></option>
        <?php 
        if (strlen($array_centros_alumno[$datos_alumno["id_centro"]])!=1) {
        ?>
        <option value="C<?=$datos_alumno["padre_de"];?>">Adminstración de <?=$datos_escuela["nombre_centro"];?></option>
        <?php 
        }
        
        $array_centros_alumno[$datos_alumno["id_centro"]]=1;

       
     }   
    }
 	?>
 	</select></td>
 	</tr>
 	<tr>
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Asunto:</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left">&nbsp;<input type="text" name="asunto" value="" placeholder="Escribe aquí el asunto" size=90></td>
 	</tr>
 	<tr valign="top">
 	<td class="tabla_celda_encabezado celda_margen" align="right"><b>Mensaje:</b></td>
 	<td class="tabla_celda_datos celda_margen" align="left">&nbsp;<textarea id="texto_mensaje" name="texto_mensaje" placeholder="Escribe aquí tu mensaje o instrucciones para la educadora" style="font-size: 12px; font-family: Arial" cols="115" rows="10"></textarea></td>
 	</tr> 	 	 	
 	</table> 	
 	</form>
 	<br />	
 	<button onclick="window.location.href='mensajeria.php'" class="tabla_celda_encabezado celda_margen"><i class="fa fa-backward"></i> Cancelar</button> <button onclick="enviar_mensaje()" class="tabla_celda_encabezado celda_margen"><i class="fa fa-paper-plane"></i> Enviar</button>
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
</script>
    <?php 		
    }
    ?></div><br />
			</div> 	<!-- Fin Columna principal -->
		</div> 		<!-- Fin Contenido -->
	</div>			<!-- Fin Contaniner -->	
</div>				<!-- Fin Jumbotron -->	    
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
