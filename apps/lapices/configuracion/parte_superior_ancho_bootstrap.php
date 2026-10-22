<?php  require("configuracion/config.php"); ?>
<?php  require("configuracion/autentifica.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
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

<script src="/js/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="/js/datepicker/locales/bootstrap-datepicker.es.min.js" charset="UTF-8"></script>

<link href="/js/datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">

<?php  require("estilos/estilo1.php"); ?>

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

<?php  require("scripts/scripts.php"); ?>

<script>
      
function mail_todos() {

	$('[id^=email]').click();

	} 
	
function sms_todos() {

	$('[id^=sms]').click();

	}       	       

function mail_grupos(grupo) {

	$('[id^=email_'+grupo+'_]').click();

	} 
	
function sms_grupos(grupo) {

	$('[id^=sms_'+grupo+'_]').click();

	} 

function enviar_mail() {

	var count_checked1 = $("[name='email[]']:checked").length
	
	if ( count_checked1 > 0 ) {
	
		if (confirm("Se va a enviar un Email a todos los marcados.\n\n¿Estás seguro/a?")) {
			document.detalles_grupo.action="/gestion/contacto/envios/enviar_email_todos.php";
			$('#enviosModal').modal('show');
			document.detalles_grupo.submit();
			}	
		} else {
			alert("No has seleccionado ningún alumno para el envío de emails.");
		}

	}
	
function enviar_sms() {

	var count_checked1 = $("[name='sms[]']:checked").length
	
	if ( count_checked1 > 0 ) {

		if (confirm("Se va a enviar un SMS a todos los marcados, esto tiene un coste.\n\n¿Estás seguro/a?")) {
			document.detalles_grupo.action="/gestion/contacto/envios/enviar_sms_todos.php";
			$('#enviosModal').modal('show');
			document.detalles_grupo.submit();
			}
		} else {
			alert("No has seleccionado ningún alumno para el envío de SMS.");
		}

	}

</script> 

</head>
<body class="legacy-admin" topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0; background-repeat: no-repeat" bordercolor="#111111" width="100%" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu_ancho.gif">
  <tr>
    <td colspan="3">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td colspan="3" width="100%" style="border-style: none; border-width: medium" align="right">
        <?php  require("inc/login_sup_moderno.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td colspan="3" width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
