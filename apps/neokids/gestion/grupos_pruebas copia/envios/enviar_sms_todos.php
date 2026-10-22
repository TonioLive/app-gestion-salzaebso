<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha_limite=$_POST["fecha_limite"];

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

$listado_sms=$_POST["sms"];
$listado_sms_e=$_POST["e_sms"];
       
// Busca números de telefono
$num_creditos=0;

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

$num_sms_enviar=0;
$precio_credito=0.06;

$lista_sms_destino=array();

if ($listado_sms) {

foreach ($listado_sms as $fila) {

	$busca_datos_alumno=db_query("SELECT * FROM usuarios WHERE id_usuario=".$fila);
	$datos_alumno=db_fetch_array($busca_datos_alumno);
	
	$t1=$datos_alumno["telefono1"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t1, $matches1);
	foreach($matches1[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			}
		
		}
	}
	
	$t2=$datos_alumno["telefono2"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t2, $matches2);
	foreach($matches2[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			
			}
		
		}
	}	
	$t3=$datos_alumno["movil"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t3, $matches3);
	foreach($matches3[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			
			}
		
		}
	}
	}
}
if ($listado_sms_e) {

foreach ($listado_sms_e as $fila) {

	$busca_datos_alumno=db_query("SELECT * FROM usuarios WHERE id_usuario=".$fila);
	$datos_alumno=db_fetch_array($busca_datos_alumno);
	
	$t4=$datos_alumno["telefono1"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t4, $matches4);
	foreach($matches4[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			}
		
		}
	}
	
	$t5=$datos_alumno["telefono2"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t5, $matches5);
	foreach($matches5[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			
			}
		
		}
	}	
	$t6=$datos_alumno["movil"];
	preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $t6, $matches6);
	foreach($matches6[0] as $numero) {
		if (substr($numero,0,1)!="9") {
		
			if (!in_array($numero,$lista_sms_destino)) {
			
				$lista_sms_destino[]=$numero;
				$num_sms_enviar++;
			
			}
		
		}
	}
	}
}
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
<form method="POST" name="detalles_grupo" action="enviar_sms_todos1.php" target="mensajes_alumnos">
<input type="hidden" name="id_grupo" value="<?=$_POST["id_grupo"];?>">
<input type="hidden" name="id_subgrupo" value="<?=$_POST["id_subgrupo"];?>">
<input type="hidden" name="fecha_limite" value="<?=$_POST["fecha_limite"];?>">
<input type="hidden" name="listado_sms" value='<?=serialize($lista_sms_destino);?>'>

<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-heading"><h5><i class="fa fa-comment-o" aria-hidden="true"></i> Se van a enviar <?=$num_sms_enviar?> SMS a los alumnos seleccionados.</h5></div>
	  	<div class="panel-body">
	  		<h5>Saldo actual disponibles: <?=$num_creditos?> Créditos (1 crédito = 1 SMS). Coste del envío: <?=number_format(($num_sms_enviar*$precio_credito),2,",",".");?> € (1 crédito = <?=number_format($precio_credito,2,",",".");?> €)</h5>	
			<?php 
			if ($num_creditos<$num_sms_enviar) {
			?>
			<h4 class="text-danger">El número de mensajes a enviar es mayor que el número de créditos disponibles. No se puede enviar el mensaje.</h4>
			<?php 
			} else {
			?>
			<h6>Mensaje</h6>
			<textarea class="form-control" rows="2" name="mensaje" id="mensaje" maxlength="160"></textarea>
			<div id="textarea_feedback"></div>
			<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-comment-o" aria-hidden="true"></i> Enviar</button> Una vez redactado el mensaje, pulsa el botón "Enviar" para que comience el proceso.
			<?php 
			}
			?>
   	
  		</div>
	</div>

</div>
</form>

<script>

$(document).ready(function() {

    var text_max = 160;
    $('#textarea_feedback').html(text_max + ' caracteres restantes');

    $('#mensaje').keyup(function() {
	    var text_length = $('#mensaje').val().length;
	    var text_remaining = text_max - text_length;

    	$('#textarea_feedback').html(text_remaining + ' caracteres restantes');
      
	});
	
});
  
</script>

</body>
</html>
