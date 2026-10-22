<?php
require_once dirname(__DIR__).'/configuracion/runtime.php';

	
	$sql_host=app_db_settings('sql_host');
	$sql_db=app_db_settings('sql_db');
	$sql_usuario=app_db_settings('sql_usuario');
	$sql_pass=app_db_settings('sql_pass');
	
	$db_validacion=db_connect("$sql_host","$sql_usuario","$sql_pass");
	db_select_db("$sql_db",$db_validacion);	
	
	$contenido_fichero=file_get_contents('http://10.10.1.15/cgi-bin/temp.bat');	
	
	$remove = "\n";

    $split = explode($remove, $contenido_fichero);

    $array[] = null;
    $tab = "\t";

    foreach ($split as $string)
    {
        $row = explode($tab, $string);
        array_push($array,$row);
    }
    /*
    echo("<pre>");
    echo($array[3][2]." ".$array[3][24]."<br />");
    echo($array[4][2]." ".$array[4][24]."<br />");
    echo($array[5][2]." ".$array[5][24]."<br />");
    print_r($array);
    echo("</pre>"); 
    */
    $hora_toma_datos = $array[3][17];    
    
    $busca_si_existe=db_query_params('SELECT * FROM registro_temperaturas WHERE hora_toma_datos=?', 's', array((string)($hora_toma_datos)));
    
    if (db_num_rows($busca_si_existe)==0) {
	    
	    $inserta_registro=db_query_params('INSERT INTO registro_temperaturas (temp, temp_piscina, humedad_rel, hora_toma_datos) VALUES (?,?,?, ?)', 'ssss', array((string)(str_replace(",", ".", $array[3][24])), (string)(str_replace(",", ".", $array[5][24])), (string)(str_replace(",", ".", $array[4][24])), (string)($hora_toma_datos)));	    
	    
    }    	
?>