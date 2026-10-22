<?php 
// redirecciona a la nueva zona privada
header("Location: /clientes");

require("../configuracion/config.php"); 
require("parte_superior_padres_corta_publica.php"); ?>

<div class="panel panel-default">
  <div class="panel-body text-center">
<?php

$permiso_entrar=false;

if ( (substr($_SERVER['REMOTE_ADDR'],0,8)=='10.10.1.') AND (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh')) ) {

	$permiso_entrar=true;	

}

$permiso_entrar=false;	
	
if ((time()>=strtotime('2017-10-01 00:00:00')) or ($permiso_entrar==true)) {	
?> 
<p class="text-center"><h3>Acceso al área privada</h3><br />
En <?=$nombre_centro?>, te habrán proporcionado una carta que contiene un <strong>NOMBRE DE USUARIO</strong> y una <strong>CONTRASEÑA</strong>.<br />
Estos datos te permiten acceder al área privada donde ponemos a tu disposición múltiples servicios para mantenerte al día de las actividades de <?=$nombre_centro?>.<br /><br />
Introduce a continuación tus datos de acceso<br />para empezar a utilizar el servicio.</p>
<br />
<form class="form-inline" method="POST" action="acceso.php">
  <div class="form-group">
    <label for="user">Usuario</label>
    <input type="text" class="form-control" id="user" name="user" placeholder="Nombre de usuario">
  </div>
  <div class="form-group">
    <label for="pass">Contraseña</label>
    <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña">
  </div>
  <button type="submit" class="btn btn-default"><i class="fa fa-sign-in"></i> Entrar</button>
</form>
<?php
	} else {
?>		
	<div class="text-center">
		<h1>Servicio desactivado</h1>
		<h1>Este servicio estará disponible para todos los usuarios<br />
			a partir del día 1 de octubre de 2017</h1>
		<h1>Perdone las molestias</h1>		
	</div>
<?php		
	}
?>
</div>
</div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
