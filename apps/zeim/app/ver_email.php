<?php
require("../configuracion/config.php"); 
require("../configuracion/autentifica_padre.php");      

$id_mensaje=$_GET["id_mensaje"];

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
<link rel="shortcut icon" href="https://gestion.escuelainfantilbenicalap.com/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://gestion.escuelainfantilbenicalap.com/favicon.ico" type="image/vnd.microsoft.icon">
<title>Mensajes recibidos</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</head>
<body>
        <?php
      
        $busca_datos_alumno=db_query_params('SELECT *, alumnos.id_centro AS id_centro_alumno FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.id_curso=? ORDER BY alumnos.id_curso DESC', 'ssss', array($id_usuario, (string)(date("Y-m-d")), (string)(date("Y-m-d")), $id_curso_actual)) or die(db_error()."- No se encuentran los datos del alumno.");
                
        $fecha_inicio_mensajes='2024-10-01';
        $arrayFiltrosAula=array();
        while($datos_alumno=db_fetch_array($busca_datos_alumno)) {
                $arrayFiltrosCentro[$datos_alumno["id_centro_alumno"]]="(aul='' AND cen=".$datos_alumno["id_centro_alumno"].")";
                $arrayFiltrosAula[$datos_alumno["aula"]]="(aul=".$datos_alumno["aula"].")";
                
                // Muestra solo mensajes desde que el alumno se inscribió
                if (strtotime($datos_alumno["fecha_matricula"])>=strtotime($fecha_inicio_mensajes)) {
                        $fecha_inicio_mensajes=$datos_alumno["fecha_matricula"];
                }
                        
        }
        
        $listaFiltros1=implode(" OR ",$arrayFiltrosCentro);
        $listaFiltros2=implode(" OR ",$arrayFiltrosAula);
        
        $lista_filtros_aula=" AND (".$listaFiltros1." OR ".$listaFiltros2.")";        
        
        
        $select_mensajes="SELECT * FROM mensajes_temporal WHERE mensajes_temporal.contactos='0' AND mensajes_temporal.fecha>='$fecha_inicio_mensajes'".$lista_filtros_aula." AND id_mensaje=".$id_mensaje." ORDER BY mensajes_temporal.fecha";      

        $busca_mensajes=db_query($select_mensajes);

        $hay_mensajes=false;

        $busca_mensajes=db_query($select_mensajes);        

        while($datos_mensajes=db_fetch_array($busca_mensajes)) {   

            $hay_mensajes=true;
            
            ?>
            <div class="panel panel-default">
            <div class="panel-heading">        
                <div class="row">
                    <div clas="col-md-1">
                        <a style="margin-left:20px;margin-right:20px;" class="pull-left btn btn-primary btn-sm abrir-mensaje" href="/app/ver_ultimo_mensaje.php?todos=1"><i class="fa fa-arrow-left" aria-hidden="true"></i> Volver</a>    
                    </div>
                    <div clas="col-md-11">
                        <h3 class="panel-title" style="margin-top:5px;"><?=$datos_mensajes["fecha"]?> - <?=security_escape($datos_mensajes["asunto"]);?></h3>
                    </div>
                </div>   
            </div>
            <div class="panel-body">
            <?=utf8_decode($datos_mensajes["texto_editor"]);?>
            </div>
            </div>                
            <?php                     

        }     

        if ($hay_mensajes==false) {
                ?>
                <div class="panel panel-default">
                        <div class="panel-body text-center">
                                <h3>No se encuentra el mensaje solicitado</h3>
                        </div>
                </div>                 
                <?php
        }
        
        ?>        
        <script>
                $('a:not(.abrir-mensaje)').attr('target', '_blank');
        </script>
</body>
</html>        