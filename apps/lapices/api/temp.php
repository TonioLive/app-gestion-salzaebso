<?php
	
	$sql_host = (getenv('APP_DB_HOST') ?: 'localhost');
	$sql_db = (getenv('APP_DB_NAME') ?: '');
	$sql_usuario = (getenv('APP_DB_USER') ?: '');
	$sql_pass = (getenv('APP_DB_PASSWORD') ?: '');
	
	$db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
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
    
    $busca_si_existe=db_query("SELECT * FROM registro_temperaturas WHERE hora_toma_datos='$hora_toma_datos'");
    
    if (db_num_rows($busca_si_existe)==0) {
	    
	    $inserta_registro=db_query("INSERT INTO registro_temperaturas (temp, temp_piscina, humedad_rel, hora_toma_datos) VALUES ('".str_replace(",",".",$array[3][24])."','".str_replace(",",".",$array[5][24])."','".str_replace(",",".",$array[4][24])."', '".$hora_toma_datos."')");	    
	    
    }    	
?>