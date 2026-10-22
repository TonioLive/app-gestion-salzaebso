<?php  require("../configuracion/config.php"); 
require("../configuracion/autentifica_padre.php");      

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="shortcut icon" href="http://www.escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://www.escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<title>Mensajes recibidos</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</head>
<body>
<?php 

$busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.id_curso=? ORDER BY alumnos.id_curso DESC', 'ssss', array($id_usuario, (string)(date("Y-m-d")), (string)(date("Y-m-d")), $id_curso_actual)) or die(db_error()."- No se encuentran los datos del alumno.");

$fecha_inicio_mensajes='2015-11-05';
$lista_filtros_aula=" AND ((aul='')";
while($datos_alumno=db_fetch_array($busca_datos_alumno)) {
        $lista_filtros_aula.=" OR (aul=".$datos_alumno["aula"].")";
		
		if (strtotime($datos_alumno["fecha_matricula"])>=strtotime($fecha_inicio_mensajes)) {
			$fecha_inicio_mensajes=$datos_alumno["fecha_matricula"];
		}
		
        }
$lista_filtros_aula.=")";
  
$select_mensajes="SELECT * FROM mensajes_temporal WHERE mensajes_temporal.fecha>='$fecha_inicio_mensajes'".$lista_filtros_aula." ORDER BY mensajes_temporal.fecha";

$busca_mensajes=db_query($select_mensajes);

$hay_mensajes=false;

while($datos_mensajes=db_fetch_array($busca_mensajes)) {   
        
        $busca_lectura=db_query_params('SELECT * FROM mensajes_registro_lectura WHERE id_mensaje=? AND id_usuario=?', 'ss', array($datos_mensajes["id_mensaje"], $id_usuario)) or die(db_error());

        $fecha_lectura="";

        if (db_num_rows($busca_lectura)!=0) {
                $datos_lectura=db_fetch_array($busca_lectura);
        
                $fecha_lectura="<h5 class='text-danger'>Leído el día ".date("d/m/Y",strtotime($datos_lectura["leido"]));
                $fecha_lectura.=" a las ".date("H:i",strtotime($datos_lectura["leido"]))."</h5>";
        
        }
                ?>
                <div class="panel panel-success">
                  <div class="panel-heading">
                    <h3 class="panel-title"><?=$datos_mensajes["fecha"]?> - <?=security_escape($datos_mensajes["asunto"]);?></h3><?=$fecha_lectura?>
                  </div>
                  <div class="panel-body">
                    <?=utf8_decode($datos_mensajes["texto_editor"]);?>
                  </div>
                </div>                
                <?php 

        }
        ?>
<p align="center">        
        <button type="button" class="btn btn-default" onclick="parent.$('#myModal').modal('hide');">Cerrar ventana</button>
</p>
</body>
</html>        
