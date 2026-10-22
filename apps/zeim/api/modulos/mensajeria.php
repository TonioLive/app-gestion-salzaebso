<?php
    $mensajes = array();
    $lista_mensajes = array(); 
           
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
        $estados_mensajes=array("No leido","Leido","Cerrado");
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
     
    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda AND tipo_nota=2 AND estado=0 ORDER BY fecha DESC");                 
                       
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
            $estado_mensaje="<font color='#f00'>".db_num_rows($busca_respuestas)." respuesta$coletilla_leido nueva$coletilla_leido</font>";
            } else {
            $estado_mensaje=$estados_mensajes[$mensajes["estado"]];
            }
    
			$lista_mensajes[] = array(
				'id_mensaje' => $mensajes["id_nota_padre"],
			 	'tipo' => $tipos_mensajes[$mensajes["tipo_nota"]].$subtipo_nota, 
			 	'remitente' => $nombre_remitente, 
			 	'asunto' => $mensajes["asunto"], 
			 	'fecha' => date("d/m/Y",strtotime($mensajes["fecha"])), 
			 	'hora' => date("H:i",strtotime($mensajes["hora"])), 
			 	'estado' => $estado_mensaje
			 	);    
    
            }

$mensajes['lista_mensajes'] = $lista_mensajes;    
$mensajes['total'] =  $resultado_total;
$mensajes['no_leidos'] =  db_num_rows($busca_no_leidos);
    
$json['mensajes'] = $mensajes;