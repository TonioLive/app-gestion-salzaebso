<?php
    $notas = array();
    $lista_notas = array(); 
    
	$array_aulas_hijo=array();
                
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario");
    while($padres=db_fetch_array($busca_padres)){
            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
            }
    if ($filtro_padres!="") {
            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
            }      
      
	// Busca hijos del padre

    $aulas_hijos="";
    $centros_hijos="";
    $aulas_hijos_notas="";
        
    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {
    
        $contador_hijos=0;
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                $busca_datos_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_hijos["id_curso"]);
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                        } else {
                        $nombre_curso_mostrado="";
                        }
                        
                // Datos de la escuela del niño
                $busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del niño
                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"]);

                $datos_aula=db_fetch_array($busca_datos_aula);

                $array_aulas_hijo[$datos_hijos["aula"]]=$datos_aula["nombre"];

                $aulas_hijos.=" OR (Agenda.id_aula=".$datos_hijos["aula"].")";
                
                $centros_hijos.=" OR (Agenda.id_centro=".$datos_hijos["id_centro"]." AND Agenda.id_aula=0)";
                
                $aulas_hijos_notas.=" OR (agenda_alumnos_seguimiento.id_aula=".$datos_hijos["aula"].")";
                
                $contador_hijos++;
                }
            }

	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'");

	if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

	$hay_avisos=false;


	$filtro_usuarios=" AND (Agenda.id_centro=0$centros_hijos$aulas_hijos)";

	if (strlen($contador_hijos)!=0) {
        $aulas_hijos_notas="(".substr($aulas_hijos_notas,4,strlen($aulas_hijos_notas)-4).")";
        }
                  
	// Notas educadora

	$filtro_busqueda="";
	
	$busqueda=$_GET["q"];
	$tipo_b="OR";
	
	if ($busqueda) {

        // Desglosa una frase en palabras sueltas

          $palabra=explode(" ",$busqueda);

          $query="(";
          for ($cont=0; $cont<count($palabra); $cont++)
                {
                $query=$query."(agenda_alumnos_seguimiento.nota like '%".$palabra[$cont]."%')";
                $query=$query." ".$tipo_b." ";

                $palabra_actual=$palabra_actual+1;
                }
            
          if ($tipo_b=="OR") { $cacho=4; }
          if ($tipo_b=="AND") { $cacho=5; }
          $query=substr($query,0,(strlen($query)-$cacho));
          $query=$query.")";
	
		$filtro_busqueda=" AND $query";		
	
	} 	

	$busca_mensajes=db_query("SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND agenda_alumnos_seguimiento.id_alumno=0 AND $aulas_hijos_notas$filtro_busqueda ORDER BY agenda_alumnos_seguimiento.dia DESC") or die(db_error());

    $resultado_total=db_num_rows($busca_mensajes);
    $inicio_listado=$_GET["i"];
    $numero_resultados=10;
    if (strlen($inicio_listado)==0) {
    	$inicio_listado=0;
    } 
    
	$busca_ultimos_mensajes=db_query("SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND agenda_alumnos_seguimiento.id_alumno=0 AND $aulas_hijos_notas$filtro_busqueda ORDER BY agenda_alumnos_seguimiento.dia DESC LIMIT $inicio_listado,$numero_resultados"); 
    
    $total_resultado_busqueda=db_num_rows($busca_ultimos_mensajes);            
              
    while($mensajes=db_fetch_array($busca_mensajes)) {    
    
			$lista_notas[] = array(
				'id_nota' => $mensajes["id"],
			 	'tipo' => $mensajes["nombre_tipo"], 
			 	'aula' => $array_aulas_hijo[$mensajes["id_aula"]], 
			 	'nota' => $mensajes["nota"], 
			 	'fecha' => date("d/m/Y",strtotime($mensajes["dia"])), 
			 	'hora' => date("H:i",strtotime($mensajes["hora"]))
			 	);     
   
            }
   
$notas['lista_notas'] = $lista_notas;    
$notas['total'] =  $resultado_total;
    
$json['notas'] = $notas;
