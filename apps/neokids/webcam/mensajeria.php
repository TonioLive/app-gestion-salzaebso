<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php"); ?>

<div class="jumbotron fondo_2">
  <div class="container">
    <div class="row"> <!-- Menú -->
    	<?php  include("menu_lateral.php"); ?>
    </div>
    <div class="row fondo_1"> <!-- Contenido -->
    	<div class="col-md-12"> <!-- Columna principal -->

<?php     
   if ($errores=="") {
           
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE id_usuario=?', 's', array($id_usuario));
    while($padres=db_fetch_array($busca_padres)){
            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
            }
    if ($filtro_padres!="") {
            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
            }      
      
                  
    // Busca mensajes
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $subtipos_mensajes=array(" a la educadora"," a la coordinadora");
        $estados_mensajes=array("No leido","Leido","Cerrado");
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) ORDER BY fecha DESC, hora DESC");              
     
    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) AND tipo_nota=2 AND estado=0 ORDER BY fecha DESC"); 
    $texto_no_leidos="";
    if (db_num_rows($busca_no_leidos)!=0) {
            if (db_num_rows($busca_no_leidos)) {
            $coletilla_leido="";
            if (db_num_rows($busca_no_leidos)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $texto_no_leidos=" (".db_num_rows($busca_no_leidos)." no leido$coletilla_leido)";
            }
                  
    ?>
    <div class="row">
    	<div class="col-md-6"><h3>Mensajes<?=$texto_no_leidos?></h3></div>
    	<div class="col-md-6 text-right"><br /><button class="tabla_celda_encabezado celda_margen" onclick="window.location.href='nuevo_mensaje.php'"><i class="fa fa-envelope"></i> Nuevo mensaje</button></div>
    </div>
    <div align="center">    
         <table width=98%  class="tabla_datos" align="center">
         <tr>
         <td class="tabla_celda_encabezado celda_margen">Tipo</td>
         <td class="tabla_celda_encabezado celda_margen">De</td>
         <td class="tabla_celda_encabezado celda_margen">Asunto</td>
         <td class="tabla_celda_encabezado celda_margen">Fecha</td>
         <td class="tabla_celda_encabezado celda_margen">Estado</td>
         <td>&nbsp;</td>
         </tr>
    <?php 
    if (db_num_rows($busca_mensajes)==0) {
            ?>
           <tr bgcolor="#ffffff">
         <td align="center" colspan=6><b>No hay mensajes</b></td>
         </tr>
            <?php 
            }
    while($mensajes=db_fetch_array($busca_mensajes)) {
    
    // Busca remitente
    $busca_remitente=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($mensajes["id_remitente"]));
    
    if (db_num_rows($busca_remitente)!=0) {
            $datos_remitente=db_fetch_array($busca_remitente);
            $nombre_remitente=$datos_remitente["Nombre"];
            } else {
            $nombre_remitente="-";
            }
            
    if ($mensajes["tipo_nota"]!=2) {
            $subtipo_nota=$subtipos_mensajes[$mensajes["destino"]];
            } else {
            $subtipo_nota="";
            }
    
    // Busca respuestas no leidas
    
    $busca_respuestas=db_query_params('SELECT * FROM agenda_notas_padres_respuestas WHERE leida=0 AND id_nota=? AND respondido_por<>?', 'ss', array($mensajes["id_nota_padre"], $id_usuario));
    
    if (db_num_rows($busca_respuestas)!=0) {
            if (db_num_rows($busca_respuestas)) {
            $coletilla_leido="";
            if (db_num_rows($busca_respuestas)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $estado_mensaje="<font color='#f00'>".db_num_rows($busca_respuestas)." respuesta$coletilla_leido nueva$coletilla_leido";
            } else {
            $estado_mensaje=$estados_mensajes[$mensajes["estado"]];
            }
    
    ?>
         <tr>
         <td class="tabla_celda_datos celda_margen"><?=$tipos_mensajes[$mensajes["tipo_nota"]].$subtipo_nota?></td>
         <td class="tabla_celda_datos celda_margen"><?=$nombre_remitente?></td>
         <td class="tabla_celda_datos celda_margen"><?=security_escape($mensajes["asunto"]);?></b></td>
         <td class="tabla_celda_datos celda_margen"><?=date("d/m/Y",strtotime($mensajes["fecha"]));?> <?=date("H:i",strtotime($mensajes["hora"]));?></td>
         <td class="tabla_celda_datos celda_margen"><?=$estado_mensaje;?></b></td>
         <td class="tabla_celda_datos celda_margen" align="center"><button class="tabla_celda_encabezado celda_margen" onclick="window.location.href='ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>'"><i class="fa fa-folder-open"></i> Abrir</button></b></td>
         </tr>
    <?php 
            }
    }
    ?>
    <tr><td colspan=6><br></td></tr>
    </table></div>
			</div> 	<!-- Fin Columna principal -->
		</div> 		<!-- Fin Contenido -->
	</div>			<!-- Fin Contaniner -->	
</div>				<!-- Fin Jumbotron -->	
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
