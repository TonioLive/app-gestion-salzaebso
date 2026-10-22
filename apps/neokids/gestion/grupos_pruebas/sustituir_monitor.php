<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$monitor_Sel=$_GET["monitor"];
$nombre_mostrar=utf8_decode($_GET["nombre_mostrar"]);
$id_sustitucion=$_GET["id_sustitucion"];

if (strlen($id_sustitucion)==0) {
	$id_sustitucion=0;
	}

$colores_niveles=array(""," text-success"," text-info"," text-danger");


?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Detalles Grupo</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="/js/datepicker/js/bootstrap-datepicker.min.js"></script>

<script src="/js/datepicker/locales/bootstrap-datepicker.es.min.js" charset="UTF-8"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/js/datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>
.fondo_nivel0 {
        background: #fff;
        }
.fondo_nivel1 {
        background: #9ed09c;
        }
.fondo_nivel2 {
        background: #9cbdd0;
        }
.fondo_nivel3 {
        background: #d0af9c;
        } 
.fondo_nivel4 {
        background: #f9a63b;
        } 
.fondo_nivel5 {
        background: #ffa9f0;
        }

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

// Busca monitores en grupo

$monitor=array();

$busca_monitores=db_query("SELECT * FROM aulas_grupos_monitores LEFT JOIN usuarios ON aulas_grupos_monitores.id_usuario=usuarios.id_usuario WHERE num_monitor=$monitor_Sel AND id_grupo=$id_grupo AND id_subgrupo=$id_subgrupo") or die(db_error()."SELECT * FROM aulas_grupos_monitores LEFT JOIN usuarios ON aulas_grupos_monitores.id_usuario=usuarios.id_usuario WHERE num_monitor=$monitor_Sel AND id_grupo=$id_grupo AND id_subgrupo=$id_subgrupo");

if (db_num_rows($busca_monitores)!=0) {
	$datos_monitor=db_fetch_array($busca_monitores);
	
	$id_monitorSel=$datos_monitor["id_usuario"];
	
	
	if ($id_monitorSel) {
	
			$busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=? AND id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));
			
			$datos_subgrupo=db_fetch_array($busca_datos_subgrupo);
			
			// Busca datos susttucion actual
			if ($id_sustitucion!=0) {
				$busca_sustitucion=db_query_params('SELECT * FROM personal_asistencia_sustituciones WHERE id_sustitucion=?', 's', array($id_sustitucion));
			} else {
				$busca_sustitucion=db_query_params('SELECT * FROM personal_asistencia_sustituciones WHERE id_grupo=? AND id_subgrupo=? AND desde=?', 'sss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha))));
			}
			
			if (db_num_rows($busca_sustitucion)!=0) {
			
				$datos_sustitucion=db_fetch_array($busca_sustitucion);
				
				$tipo_ausencia=$datos_sustitucion["tipo_ausencia"];
				$sustituido_por=$datos_sustitucion["sustituido_por"];
				$desde=$datos_sustitucion["desde"];
				if ($desde) {
					$desde=date("d/m/Y",strtotime($desde));
				}
				$hasta=$datos_sustitucion["hasta"];	
				if ($hasta=='0000-00-00') {
					$hasta = null;
				}								
				if ($hasta) {
					$hasta=date("d/m/Y",strtotime($hasta));
				}	
		
				$observaciones=$datos_sustitucion["observaciones"];
			
			} else {	
				$tipo_ausencia=0;
				$sustituido_por=0;
				$desde=date("d/m/Y",$fecha);
				$hasta="";
				$observaciones="";
			}

			
?>
<h4>Monitor/a actual: <?=$datos_monitor["Nombre"]?> <?=$datos_monitor["Apellido1"]?> <?=$datos_monitor["Apellido2"]?></h4>		<form method="post" action="sustituir_monitor1.php" name="monitores">
	<input type="hidden" value="<?=$id_grupo?>" name="id_grupo">
    <input type="hidden" value="<?=$id_subgrupo?>" name="id_subgrupo">
    <input type="hidden" value="<?=$nombre_mostrar?>" name="nombre_mostrar">
    <input type="hidden" value="<?=$id_sustitucion?>" name="id_sustitucion">
    <input type="hidden" value="<?=$fecha?>" name="fecha"> 
    <input type="hidden" value="<?=$id_monitorSel?>" name="id_monitorSel">   
    <table class="table table-condensed">
    	<tr>
    		<td align="right" class="col-md-1">Sustituido/a por:</td>
    		<td class="col-md-3">
    			<select name="sustituido_por" class="form-control" required='required'><option value=""></option>
    							<?php 
                                $busca_docentes=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion=4 ORDER BY Apellido1, Apellido2, Nombre;");
                                while($datos_docentes=db_fetch_array($busca_docentes)) {
                                ?>
                                <option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$sustituido_por) { print " selected"; } ?>><?=$datos_docentes["Nombre"];?> <?=$datos_docentes["Apellido1"];?> <?=$datos_docentes["Apellido2"];?></option>
                                <?php 
                                }
                                ?>
				</select>
			</td>                                                                              
    	</tr>
		<tr>
			<td align="right" class="col-md-1">Desde el día:</td>
            <td class="col-md-3">
            	    <input type="text" id="desde" name="desde" value="<?=$desde?>" class="form-control" placeholder="Primer día de la sustitución" required='required'>
			</td>
		</tr>    	
		<tr>
			<td align="right" class="col-md-1">Hasta el día:</td>
            <td class="col-md-3">
            	    <input type="text" id="hasta" name="hasta" value="<?=$hasta?>" class="form-control" placeholder="Último día de la sustitución *">
			</td>
		</tr>      	
		<tr>
			<td align="right" class="col-md-1">Observaciones:</td>
            <td class="col-md-3">
            	<textarea name="observaciones" id="observaciones" class="form-control" rows="2"><?=$observaciones?></textarea>
			</td>
		</tr> 
	</table>
	<h6 class="text-danger">* Si la sustitución es únicamente por un día este dato no es necesario, basta con indicar la fecha inicial.</h6>	
	<?php  if ($id_sustitucion!=0) { ?><button type="button" class="btn btn-danger pull-right" onclick="eliminar()">Eliminar sustitución</button> <?php  } ?><button type="submit" class="btn btn-success pull-right">Guardar</button> 	
<?php 			
			
		} else {
?>
<h2 class="text-danger">No hay monitor asignado.</h2>
<?php 
		}
	

	
} else {
?>
<h2 class="text-danger">No hay monitor asignado</h2>
<?php 
}
?>                          
</div>
<script>

    $('#desde').datepicker({
        format: "dd/mm/yyyy",
        language: "es"
    });
    $('#hasta').datepicker({
        format: "dd/mm/yyyy",
        language: "es"
    });    

	function eliminar() {
	
		if (confirm("¿Deseas eliminar esta la sustitución?")) {
		
			window.location.href='eliminar_sustitucion.php?nombre_mostrar=<?=$nombre_mostrar?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&fecha=<?=$fecha;?>&id_sustitucion=<?=$id_sustitucion?>';
		
		}
	
	}
    
</script>
</body>
</html>