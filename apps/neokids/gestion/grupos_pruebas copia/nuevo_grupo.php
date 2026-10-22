<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_dia=$_GET["id_dia"];
$id_curso=$_GET["id_curso"];
$id_grupo=$_GET["id_grupo"];
$fecha=$_GET["fecha"];
$mostrar_boton_eliminar=false;

if (strlen($id_grupo)!=0) {
        // Busca datos grupos
        $busca_datos_grupo=db_query_params('SELECT * FROM aulas_grupos WHERE id_grupo=?', 's', array($id_grupo));
        
        $datos_grupo=db_fetch_array($busca_datos_grupo);
        
        $hora_inicio=$datos_grupo["hora_inicio"];
        $hora_fin=$datos_grupo["hora_fin"];
        
        $texto_mostrado="Editar";
                
        $busca_alumnos_grupo=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_grupo=?', 's', array($id_grupo)) or die(db_error());                
        $busca_alumnos_grupo1=db_query_params('SELECT * FROM aulas_alumnos_lapices_grupos WHERE id_grupo=?', 's', array($id_grupo)) or die(db_error());        

        if ((db_num_rows($busca_alumnos_grupo)==0) and (db_num_rows($busca_alumnos_grupo1)==0)) {                
                $mostrar_boton_eliminar=true;                
        }                
                
        } else {
                
        $hora_inicio="00:00:00";
        $hora_fin="00:00:00";        
        
        $texto_mostrado="Nuevo";                
                
        }

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Grupo</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">

<script>

function eliminar_grupo() {
        
        if (confirm("¿Deseas eliminar este grupo?")) {
                
                window.location.href='eliminar_grupo.php?id_grupo=<?=$id_grupo?>';
                
        }
        
}

</script>
</head>
<body>
<div class="container-fluid">
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2><?=$texto_mostrado;?> grupo de los <?=$dia_sem_largo[$id_dia];?></h2></div>
  <div class="panel-body">
    <form method="post" action="nuevo_grupo1.php" name="grupos">
    <input type="hidden" value="<?=$id_dia?>" name="id_dia">
    <input type="hidden" value="<?=$id_grupo?>" name="id_grupo">
    <input type="hidden" value="<?=$id_curso?>" name="id_curso">
    <input type="hidden" value="<?=$fecha?>" name="fecha">
    <table class="table table-condensed">
    <tr>
    <td align="right" class="col-md-1">Hora inicio:</td>
    <td class="col-md-3"><select size=1 style="font-size=10" name=hora_i>
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
    <select size=1 style="font-size=10" id=select2 name=min_i>
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
    </tr>
    <tr>
    <td align="right" class="col-md-1">Hora fin:</td>
    <td class="col-md-3"><select size=1 style="font-size=10" name=hora_f>
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
    <select size=1 style="font-size=10" id=select4 name=min_f>
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
    </tr>         
        </table>
        <button type="button" onclick="window.close();" class="btn btn-danger">Cancelar</button> <?php  if ((strlen($id_grupo)!=0) and ($mostrar_boton_eliminar==true)) { ?><button type="button" onclick="eliminar_grupo();" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button> <?php  } ?><button type="submit" class="btn btn-success">Guardar cambios</button>
    </form>
  </div>
</div>

</div>
</body>
</html>
