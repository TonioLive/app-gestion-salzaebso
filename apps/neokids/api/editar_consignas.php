<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Registro de temperaturas</title>
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

<link rel="stylesheet" href="style.css">
<style>

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
         
</style> 
  
</head>
<body>
<div class="container-fluid">

<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Editar consignas y alertas</title>
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

<link rel="stylesheet" href="style.css">
<style>

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
         
</style> 
  
</head>
<body>
	
<div class="container-fluid">
	
	
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2>Editar alertas de temperatura</h2></div>
  <div class="panel-body">
    <form method="post" action="editar_consignas1.php" name="temperaturas">
    <table class="table table-condensed table-striped">
	    <tr>
		    <td>Nombre</td>
		    <td>Horas</td>
		    <td>Dias semana <span class="badge" data-toggle="tooltip" data-placement="left" title="Indicar los días de la semana como números del 1 al 7 separados por comas, siendo 1 el lunes y 7 el domingo.">?</span></td>
		    <td>Valor mín. y máx.</td>
		    <td>Notificaciones</td>
	    </tr>
	<?php
		
	// Lee Configuracion de Alarmas
	
	$busca_configuracion=db_query("SELECT * FROM registro_temperaturas.configuracion WHERE desactivada=0 ORDER BY nombre_temp");
	
	while ($configuracion=db_fetch_array($busca_configuracion)) {	
	
	$hora_inicio=$configuracion["horario_inicio"];
    $hora_fin=$configuracion["horario_fin"];
		
	?>
		<tr>
			<td rowspan=2><?=$configuracion["nombre_amigable"];?></td>
			<td>Ini. <select size=1 style="font-size=10" name="hora_i<?=$configuracion["id"];?>">
    <?php  
  $hora=date("H",strtotime($hora_inicio));
  $sele="";
  for ($h=0; $h<=23; $h=$h+1)
  {

    if ($h<10)
    {
      $h="0".$h;
    } 
    if (intval($h)==intval($hora))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 


?>
    <option<?= $sele; ?> value="<?= $h; ?>"><?= $h; ?></option>
    <?php  

  } 

?>
    </select>:
    <select size=1 style="font-size=10" id=select2 name="min_i<?=$configuracion["id"];?>">
    <?php  
  $minuto=date("i",strtotime($hora_inicio));
  $sele="";
  for ($m=0; $m<=59; $m=$m+5)
  {
    if ($m<10)
    {
      $m="0".$m;
    } 
    if (intval($m)==intval($minuto))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
    <?php  

  } 

?>
    </select></td>
    <td rowspan=2><input type="text" value="<?=$configuracion["dias_semana"];?>" name="dias_semana<?=$configuracion["id"];?>" id="dias_semana<?=$configuracion["id"];?>" required></td>
    <td><input type="text" value="<?=$configuracion["limite_alarma_piscina"];?>" name="limite_alarma_piscina<?=$configuracion["id"];?>" id="limite_alarma_piscina<?=$configuracion["id"];?>" required class="text-right"> <?=$configuracion["simbolo"];?></td>
    <td><i class="fa fa-envelope" aria-hidden="true"></i> <input type="text" value="<?=$configuracion["email_notificaciones"];?>" name="email_notificaciones<?=$configuracion["id"];?>" id="email_notificaciones<?=$configuracion["id"];?>" required></td>
    </tr> 
    <tr>
		<td>Fin <select size=1 style="font-size=10" name="hora_f<?=$configuracion["id"];?>">
	    <?php  
	  $hora=date("H",strtotime($hora_fin));
	  $sele="";
	  for ($h=0; $h<=23; $h=$h+1)
	  {
	
	    if ($h<10)
	    {
	      $h="0".$h;
	    } 
	    if (intval($h)==intval($hora))
	    {
	
	      $sele=" selected";
	    }
	      else
	    {
	
	      $sele="";
	    } 
	
	
	?>
	    <option<?= $sele; ?> value="<?= $h; ?>"><?= $h; ?></option>
	    <?php  
	
	  } 
	
	?>
	    </select>:
	    <select size=1 style="font-size=10" id=select4 name="min_f<?=$configuracion["id"];?>">
	    <?php  
	  $minuto=date("i",strtotime($hora_fin));
	  $sele="";
	  for ($m=0; $m<=59; $m=$m+5)
	  {
	    if ($m<10)
	    {
	      $m="0".$m;
	    } 
	    if (intval($m)==intval($minuto))
	    {
	
	      $sele=" selected";
	    }
	      else
	    {
	
	      $sele="";
	    } 
	
	?>
	    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
	    <?php  
	
	  } 
	
	?>
	    </select></td> 
	    <td><input type="text" value="<?=$configuracion["limite_sup_alarma_piscina"];?>" name="limite_sup_alarma_piscina<?=$configuracion["id"];?>" id="limite_sup_alarma_piscina<?=$configuracion["id"];?>" required class="text-right"> <?=$configuracion["simbolo"];?></td>   
	    <td><i class="fa fa-phone" aria-hidden="true"></i> <input type="text" value="<?=$configuracion["telefono_notificaciones"];?>" name="telefono_notificaciones<?=$configuracion["id"];?>" id="telefono_notificaciones<?=$configuracion["id"];?>" required></td> 
    </tr>      
    <?php 
	    }
	?>  
        </table>
        <a type="button" href="registro_temperaturas.php" class="btn btn-danger">Cancelar</a> <button type="submit" class="btn btn-success">Guardar cambios</button>
    </form>
  </div>
</div>
	                  
</div>

<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
</script>

</body>
</html>