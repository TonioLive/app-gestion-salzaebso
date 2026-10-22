<?php  require("../configuracion/config.php"); ?>
<?php  require("parte_superior_padres.php");
    
   if ($errores=="") {
    ?>
    <div align="center">
    <?php             
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario");
    while($padres=db_fetch_array($busca_padres)){
            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
            }
    if ($filtro_padres!="") {
            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
            }      
      
                  
    // Busca mensajes 
    
	$filtro_busqueda="";
	
	$busqueda=$_GET["q"];
	$tipo_b="OR";
	
	if ($busqueda) {

        // Desglosa una frase en palabras sueltas

          $palabra=explode(" ",$busqueda);

          $query="(";
          for ($cont=0; $cont<count($palabra); $cont++)
                {
                $query=$query."((agenda_notas_padres.Asunto like '%".$palabra[$cont]."%')";
                $query=$query." OR (agenda_notas_padres.texto_nota like '%".$palabra[$cont]."%')";
                $query=$query.") ".$tipo_b." ";

                $palabra_actual=$palabra_actual+1;
                }
            
          if ($tipo_b=="OR") { $cacho=4; }
          if ($tipo_b=="AND") { $cacho=5; }
          $query=substr($query,0,(strlen($query)-$cacho));
          $query=$query.")";

		$filtro_busqueda=" AND $query";
		
	}      
           
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $subtipos_mensajes=array(" a la educadora"," a la coordinadora");
        $estados_mensajes=array("<span class='text-danger'>No leido</span>","Leido","Cerrado");
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda ORDER BY fecha DESC, hora DESC");              
    
    $resultado_total=db_num_rows($busca_mensajes);
    $inicio_listado=$_GET["i"];
    $numero_resultados=10;
    if (strlen($inicio_listado)==0) {
    	$inicio_listado=0;
    }       
       
    $nombe_script="mensajeria";   
        
    $busca_ultimos_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda ORDER BY fecha DESC, hora DESC LIMIT $inicio_listado,$numero_resultados"); 
    
    $total_resultado_busqueda=db_num_rows($busca_ultimos_mensajes);
     
    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda AND tipo_nota=2 AND ((fecha>='2018-01-31' AND leido_por NOT like '%[".$id_usuario."]%') OR (fecha<'2018-01-31' AND estado=0)) ORDER BY fecha DESC"); 
    
    $texto_no_leidos="";
    if (db_num_rows($busca_no_leidos)!=0) {
            if (db_num_rows($busca_no_leidos)) {
            $coletilla_leido="";
            if (db_num_rows($busca_no_leidos)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $texto_no_leidos=" (<font color='#f00'>".db_num_rows($busca_no_leidos)." no leido$coletilla_leido</font>)";
            }
                  
    ?>
    <div class="row">
    	<div class="col-md-6"><h3>&nbsp;Mensajes<?=$texto_no_leidos?></h3>
    	<form class="form-inline" method="get" action="<?=$nombe_script?>.php">
  			<div class="form-group">
    			<input type="text" class="form-control" id="q" name="q" placeholder="Buscar..." value="<?=$busqueda?>">
  			</div>	
  			<button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
		</form>
    	</div>
    	<div class="col-md-6 text-right">
    		<h3>&nbsp;</h3>
    		<input type="button" value="Nuevo mensaje" class="tabla_celda_encabezado" onclick="window.location.href='nuevo_mensaje.php'">&nbsp;&nbsp;
    	</div>
    </div>    
         <table width=98%  class="tabla_datos" align="center">
         <tr>
         <td class="tabla_celda_encabezado">Tipo</td>
         <td class="tabla_celda_encabezado">De</td>
         <td class="tabla_celda_encabezado">Asunto</td>
         <td class="tabla_celda_encabezado">Fecha</td>
         <td class="tabla_celda_encabezado">Estado</td>
         <td>&nbsp;</td>
         </tr>
    <?php 
    if (db_num_rows($busca_mensajes)==0) {
            ?>
           <tr bgcolor="#ffffff">
         <td align="center" colspan=6><font size=2><b>No hay mensajes</b></font></td>
         </tr>
            <?php 
            }

            
            
    while($mensajes=db_fetch_array($busca_ultimos_mensajes)) {
    
    // Busca remitente
    $busca_remitente=db_query("SELECT * FROM usuarios WHERE id_usuario=".$mensajes["id_remitente"]);
    
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
    
    $busca_respuestas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE direccion=1 AND id_nota=".$mensajes["id_nota_padre"]." AND respondido_por<>$id_usuario AND ((fecha_respuesta>='2018-01-31' AND leido_por NOT like '%[".$id_usuario."]%') OR (leida=0 AND fecha_respuesta<'2018-01-31'))");
    
    if (db_num_rows($busca_respuestas)!=0) {
            if (db_num_rows($busca_respuestas)) {
            $coletilla_leido="";
            if (db_num_rows($busca_respuestas)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $estado_mensaje="<font color='#f00'>".db_num_rows($busca_respuestas)." respuesta$coletilla_leido nueva$coletilla_leido</font>";
            } else {
	        // Comprueba si leido
	        if ($mensajes["fecha"]<'2018-01-31') {
		    	$estado_mensaje=$mensajes["estado"];   
	        } else {
		        $pos = strpos($mensajes["leido_por"], "[".$id_usuario."]");
		        if ($pos === false) {
			        $estado_mensaje=0;
		        } else {
			     	$estado_mensaje=1;   
		        }		        
	        }	        
	        if ($mensajes["tipo_nota"]!=2) {
		        $estado_mensaje=$mensajes["estado"];
		    }            
            $estado_mensaje=$estados_mensajes[$estado_mensaje];
            
            }
    
    ?>
         <tr>
         <td class="tabla_celda_datos"><font size=2><?=$tipos_mensajes[$mensajes["tipo_nota"]].$subtipo_nota?></font></td>
         <td class="tabla_celda_datos"><font size=2><?=$nombre_remitente?></font></td>
         <td class="tabla_celda_datos"><font size=2><?=$mensajes["asunto"];?></b></font></td>
         <td class="tabla_celda_datos"><font size=1><?=date("d/m/Y",strtotime($mensajes["fecha"]));?> <?=date("H:i",strtotime($mensajes["hora"]));?></font></td>
         <td class="tabla_celda_datos"><font size=2><?=$estado_mensaje;?></b></font></td>
         <td class="tabla_celda_datos" align="center"><font size=2><input type="button" value="Abrir" class="tabla_celda_encabezado" onclick="window.location.href='ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>'"></b></font></td>
         </tr>
    <?php 
            }
    }
    ?>
    <tr><td colspan=6><br></td></tr>
    </table>
    </div>
<div align="left"><?php  include("paginacion.php"); ?></div>    
<p align=center><a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.png',1)"><img src="/webcam/imagenes/salir_des.png" alt="Salir" name="Image3" border=0 id="Image3"></a></p>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
