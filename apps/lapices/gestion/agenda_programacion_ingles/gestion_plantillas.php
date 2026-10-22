<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Busca el curso actual
$curso_seleccionado=$_GET["curso_seleccionado"];
if (strlen($curso_seleccionado)==0) {

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$id_curso_actual=$datos_curso_actual["id_curso"];
		} else {
		$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d")."' ORDER BY fecha_inicio");

		if (db_num_rows($busca_curso_actual)!=0) {
				$datos_curso_actual=db_fetch_array($busca_curso_actual);
				$id_curso_actual=$datos_curso_actual["id_curso"];
				} else {
				$id_curso_actual=0;
				}
		}
} else {
$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
$id_curso_actual=$curso_seleccionado;
}

$id_plantilla=$_GET["id_plantilla"];

// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$nombres_niveles=array("Bebés","1-2","2-3","3-4","4-5","5-6");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

$descripcion="";
$num_sesiones=0;
$nivel=0;

if (strlen($id_plantilla)!=0) {

        $busca_datos=db_query("SELECT * FROM agenda_programacion_plantillas WHERE id_plantilla=$id_plantilla");
        
        if (db_num_rows($busca_datos)!=0) {
        
                $datos_plantilla=db_fetch_array($busca_datos);
                
                $descripcion=$datos_plantilla["descripcion"];
                $nivel=$datos_plantilla["nivel"];
                $num_sesiones=$datos_plantilla["num_sesiones"];
                $monitor=$datos_plantilla["monitor"];
                $periodicidad=$datos_plantilla["periodicidad"];
                        
        } else {
        
        $id_plantilla = 0;
        
        }        

} else {
	
	$id_plantilla = 0;
	
}


$nombres_centros = array();

$busca_datos_centros=db_query("SELECT * FROM centros;");

while($datos_centros=db_fetch_array($busca_datos_centros)) {

	$nombres_centros[$datos_centros["id_centro"]]=$datos_centros["nombre_centro"];
	
}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Programaciones</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/bootstrap-duallistbox.css" type="text/css">
<link rel="stylesheet" href="/src/chosen.css">

<style>

.objetivo_general {
    font-weight: bold;
}

.objetivo_especifico {
        font-weight: bold;
        font-style: oblique;
}

.actividad {

}

.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 

</style>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/jquery.bootstrap-duallistbox.js"></script>
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $(".chosen-select").chosen()   
    
    $('#vincularModal').on('hidden.bs.modal', function (e) {

                  history.go(0);

        })
    
});        



function guardar() {

        if ($('select#periodicidad').val()==0) {
        
                var limite = 104; // Solo se permiten 104 semanas
        
        } else {
        
                var limite= 12 // Solo se permiten 12 meses
        
        }

        if ($('select#num_sesiones').val()>limite) {
        
                alert("Con la periodicidad seleccionada solo se permite un máximo de "+limite+" sesiones.");        
        
        } else {
        
                $('input[name="retorno"]').val(0);
                document.monitores.submit();        
        
        }

}

function recargar() {
        $('input[name="retorno"]').val(1);
        document.monitores.submit();

}

function muestra_sesion(id_sesion) {

        $('input[name="retorno"]').val(2);
        $('input[name="sesion_editada"]').val(id_sesion);
        document.monitores.submit();        

}

function calendario(id_aula,nombre_aula) {
        
        $('#nombreModal').html('<h4 class="modal-title" id="serviciosModalLabel">Editar programación '+nombre_aula+'</h4>');
		$('#vincularModal').modal('show');
		$('#detallesVincular').attr('src', 'calendario_programacion.php?id_aula='+id_aula+'&id_plantilla=<?=$id_plantilla?>'); 
        
}

</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
<div class="container-fluid">
        <div class="panel panel-default">
                  <div class="panel-heading"><h3>Edición de programaciones de Inglés</h3>
				  <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
<input type="hidden" name="id_plantilla" value="<?=$id_plantilla;?>">
<font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
<?php 
$busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio DESC");

while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
?>
<option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
<?php 
}
?>
</select></b></font></form>
				  </div>
                  <div class="panel-body">
                    <form method="post" action="guarda_plantilla.php" name="monitores">
                            <input type="hidden" value="<?=$id_plantilla?>" name="id_plantilla">
                            <input type="hidden" value="0" name="retorno" id="retorno">
                            <input type="hidden" value="0" name="sesion_editada" id="sesion_editada">   
                                                     
                            <h4>Nombre programación</h4> 
                            <input type="text" name="descripcion" id="descripcion" required placeholder="Nombre o descripción de la programación" class="form-control" value="<?=$descripcion?>"> 
                            <br />
                            <h4>Edad</h4>
                                <select data-placeholder="Nivel" name="nivel" id="nivel" required tabindex=2 class="form-control">
                                        <?php 
                                        for ($niveles=0;$niveles<=5;$niveles++) {
                                
                                                
                                        $color_fila=$colores_niveles[$niveles];
                                                
                                        ?>
                                        <option value="<?=$niveles?>"<?=$color_fila?><?php  if ($niveles==$nivel) { print " selected"; } ?>><?=$nombres_niveles[$niveles];?></option>
                                        <?php 
                                }
                                        ?>
                            </select>
                                <h4>Periodo de repetición de las sesiones</h4>
                                <select data-placeholder="Periodicidad" name="periodicidad" required id="periodicidad" tabindex=3 class="form-control">
                                        <option value="0"<?php  if ($periodicidad==0) { ?> selected<?php  } ?>>Semanal: Los ejercicios de cada sesión se realizan una vez por semana.</option>                                        
                            </select>                            
                            <h4>Número de sesiones</h4>
                                <select data-placeholder="Número de sesiones" name="num_sesiones" required id="num_sesiones" tabindex=4 class="form-control" onchange="recargar();">
                                        <?php 
                                        for ($nums_sesiones=1;$nums_sesiones<=100;$nums_sesiones++) {
                                        ?>
                                        <option value="<?=$nums_sesiones?>"<?php  if ($nums_sesiones==$num_sesiones) { print " selected"; } ?>><?=$nums_sesiones;?></option>
                                        <?php 
                                }
                                        ?>
                            </select>
                            <?php 
                            if ($num_sesiones>0) {
                            ?>
                            <h4>Editar sesiones</h4>
                            <table class="table">
                            <?php                     
                            $columna_actual=0;
                            for ($sesiones=1;$sesiones<=$num_sesiones;$sesiones++) {
                                    if ($columna_actual==0) {
                                    ?>
                                    <tr>
                                    <?php 
                                    }
                                    
                                    // Comprueba si está programada
                                    $busca_programacion=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesiones");
                                    
                                    $color_boton="danger";
                                    
                                    if (db_num_rows($busca_programacion)!=0) {
                                            $color_boton="success";
                                    }
                                    
                                    ?>
                                    <td class="col-md-2 text-center">
                                                 <button type="button" class="btn btn-<?=$color_boton?>" onclick="muestra_sesion(<?=$sesiones?>)">Sesión nº <?=$sesiones?></button>
                                         </td>
                                    <?php 
                                    $columna_actual++;
                                    if ($columna_actual==6) {
                                            $columna_actual=0;
                                            ?>
                                    </tr>
                                            <?php 
                                    }
                            }
                            ?>
                            </table>  
                            <?php 
                            }
                            ?>                

                    </form>  
                    
                                <div>
                                <h5>Aulas a los que se ha asignado esta programación</h5>
                                        <table class="table table-condensed table-striped">  
                                <?php 
                                // busca aulas
                                $busca_grupos_donde=db_query("SELECT *  FROM centro_aulas LEFT JOIN centros ON centro_aulas.id_centro=centros.id_centro WHERE mostrar_programacion_ingles=1 AND centro_aulas.id_curso=$id_curso_actual AND centro_aulas.tipo=0 ORDER BY centros.id_centro,centro_aulas.edad_minima_prog") or die(db_error()."-4");

                                if (db_num_rows($busca_grupos_donde)==0) {
                                ?>
                                <tr>
                                        <td>No se han econtrado Aulas</td>                                
                                </tr>
                                <?php 
                                } else {
                                ?>
                                <tr>
	                                	<td><b>Centro</b></td>
                                        <td><b>Aula</b></td>
										<td><b>Nº de sesiones programadas</b></td>
                                        <td>&nbsp;</td>
                                </rh>
                                <?php 
                                }

                                while($grupos_donde=db_fetch_array($busca_grupos_donde)) {        
        
										// Busca sesiones programadas
										$busca_sesiones=db_query("SELECT * FROM agenda_programacion_ingles_calendario WHERE id_aula=".$grupos_donde["id_aula"]." AND id_programacion=$id_plantilla AND estado=1");
										
										$sesiones_programadas=db_num_rows($busca_sesiones);
										
										$color_boton="danger";
										if ($sesiones_programadas!=0) {
										
											$color_boton="success";
											
										}
		
                                        ?>
                                        <tr>
                                                <td>
                                                        <?=$nombres_centros[$grupos_donde["id_centro"]];?>
                                                </td>	                                        
                                                <td>
                                                        <?=$grupos_donde["nombre"];?>
                                                </td>
                                                <td>
														<?=$sesiones_programadas?>
                                                </td>												
                                                <td class="text-center">
												 <button onclick="calendario(<?=$grupos_donde["id_aula"]?>,'<?=$grupos_donde["nombre"]." de ".$nombres_centros[$grupos_donde["id_centro"]]?>');" class="btn btn-<?=$color_boton?> btn-md"><i class="fa fa-calendar" aria-hidden="true"></i> Editar calendario</button>
												 <a target="_blank" href="/gestion/agenda_ingles/seleccion_fechas_informe.php?id_aula=<?=$grupos_donde["id_aula"]?>" class="btn btn-<?=$color_boton?> btn-md"><i class="fa fa-check" aria-hidden="true"></i> Evaluación</a>
                                                </td>
                                        </tr>
                                        <?php 
                                        }
                                        ?>
                                        </table>
                                </div>
                                <br /><a type="button" href="gestion_programaciones.php" class="btn btn-danger">Salir</a> <button type="button" class="btn btn-success" onclick="guardar();">Guardar y salir</button>
                                <br /><br />
                                </div>             
                </div>
        </div> 
</div> 

<div class="modal modal-wide fade" id="vincularModal" tabindex="-1" role="dialog" aria-labelledby="vincularModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombreModal"><h4 class="modal-title" id="vincularModalLabel">Título</h4></div>
      </div>    
      <div class="modal-body">
        <iframe src="" name="detallesVincular" id="detallesVincular" width=100% height=550 frameborder=0></iframe>
      </div>
    </div>
  </div>
</div>
       
</body>
</html>
