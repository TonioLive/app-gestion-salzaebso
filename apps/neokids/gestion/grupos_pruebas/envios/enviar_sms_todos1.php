<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha_limite=$_POST["fecha_limite"];
$listado_sms=$_POST["listado_sms"];
$mensaje=$_POST["mensaje"];

if (strlen($id_subgrupo)!=0) {

        $busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

        $datos_grupo=db_fetch_array($busca_datos_subgrupo);

		$id_grupo_subgrupo=$datos_grupo["id_grupo_subgrupo"];
        $id_curso_actual=$datos_grupo["id_curso"];
        $c_dia=$datos_grupo["dia"];
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        
}

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite)+$tiempo_gracia;

       

	// Comprueba saldo SMS
	$url = 'http://api.labsmobile.com/balance.php';
    $username = (getenv('NEOKIDS_SMS_USER') ?: '');
    $password = (getenv('NEOKIDS_SMS_PASSWORD') ?: '');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $result = curl_exec($ch); curl_close($ch);

	$inicio_numero=strpos($result, "<messages>");
	$fin_numero=strpos($result, "</messages>");
	
	$num_creditos=substr($result,($inicio_numero+10),($fin_numero-$inicio_numero-10));


$precio_credito=0.06;

$lista_sms_destino=security_unserialize($listado_sms);

$num_sms_enviar=count($lista_sms_destino);

$url = 'https://api.labsmobile.com/clients/';
$username = (getenv('NEOKIDS_SMS_USER') ?: '');
$password = (getenv('NEOKIDS_SMS_PASSWORD') ?: '');

$lista_recepcion="";

foreach ($lista_sms_destino as $numero) {

	$lista_recepcion.="<msisdn>34".$numero."</msisdn>";
	
	}	

/* TEST
$sms = '<sms> 
   		<recipient>  
			'.$lista_recepcion.'
   		</recipient>  
   		<tpoa>34630726246</tpoa>
   		<test>1</test> 
    	<message>'.$mensaje.'</message> 
 	</sms>';
*/
$sms = '<sms> 
   		<recipient>  
			'.$lista_recepcion.'
   		</recipient>
   		<tpoa>34630726246</tpoa>
    	<message>'.$mensaje.'</message> 
 	</sms>';
 	 	 
$sms = utf8_encode($sms);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'XmlData='.$sms);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$result = curl_exec($ch); curl_close($ch);

print_r($result);

$inicio_numero=strpos($result, "<code>");
$fin_numero=strpos($result, "</code>");
	
$codigo_resultado=substr($result,($inicio_numero+6),($fin_numero-$inicio_numero-6));

$resultados_posibles=array(
0 => 'Message has been successfully sent',
10 => 'Missing XML data in request',
11 => 'Badly formed XML in request',
20 => 'The message element must be present in the XML',
21 => 'The message element cannot be empty',
22 => 'Message too long. There is a limit of 160 7-bit characters',
23 => 'There are no recipients',
24 => 'Too many recipients',
25 => 'TPOA is exceeding max length',
26 => 'TPOA change is not allowed for this account',
27 => 'This message contained one or more invalid character(s)',
28 => 'Subid is exceeding maximum length',
30 => 'There was an error while sending the message',
31 => 'AckLevel has been given but missing AckUrl',
32 => 'AckUrl has been given but missing AckLevel',
33 => 'An unknown value for AckLevel has been given. Allowed values are gateway, operator or handset.',
34 => 'Label field too long',
35 => 'The account has no enough credit for this sending',
36 => 'Msisdn format [number] is not allowed',
37 => 'The account has reach the maximum messages per day',
38 => 'There was an error while sending the message to this MSISDNs',
39 => 'The value of the Scheduled field is not a valid datetime format',
40 => 'The username cannot send scheduled messages',
41 => 'Scheduled messages cannot be send in test mode');

$resultado = $resultados_posibles[$codigo_resultado];

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Enviar SMS a Grupo</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<link rel="stylesheet" href="style.css">

<style>
.fondo_nivelB {
        background: #000;
        }                        
 
.textoBlanco {
                color: #fff;
                } 
                
.panelFijo{
    height: 1000vh;
    overflow-y: no;
    overflow-x: hidden;
}
 
.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 
</style>

</head>
<body>

<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-heading"><h5><i class="fa fa-comment-o" aria-hidden="true"></i> Se han enviado <?=$num_sms_enviar?> SMS a los alumnos seleccionados.</h5></div>
	  	<div class="panel-body">
	  		<h5>Resultado:</h5>	
			<?=$resultado?>
   	
  		</div>
	</div>

</div>

</body>
</html>
