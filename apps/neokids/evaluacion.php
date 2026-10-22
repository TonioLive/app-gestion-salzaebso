<?php  require("configuracion/parte_superior_sin_login.php"); ?>
    <div align="center">
<br /><br /><br /><br /><br /><br /><br /><br />
<form method="GET" action="/pdf/evaluacion_piscina.php" id="form1b" name="form1b" target="_blank">
      <table border="0" cellpadding="0" cellspacing="4" width="50%" id="AutoNumber3">
        <tr>
        <td>
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">La piscina de Neokids</h3>
  </div>
  <div class="panel-body">
   	<h4>Para ver la evaluación de su hijo introduzca aquí el código que le ha sido proporcionado y pulse el botón "Ver evaluación"</h4>   	
   	<p>
<div class="input-group">
  <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
  <input type="text" class="form-control" placeholder="Código único de 14 digitos" name="uid">
</div>
<br />
<button type="submit" class="btn btn-primary"><i class="fa fa-thumbs-up"></i> Ver evaluación</button> 	   	
   	</p>
  </div>
		</td>
        </tr>
      </table>
</form>
    </div>
<?php  require("configuracion/parte_inferior.php"); ?>
