<?php
require("../configuracion/config.php"); 
require("../configuracion/autentifica_padre.php");      

$todos=$_GET["todos"];

$mostrarTodosMensajes=false;
if ($todos=="1") {
        $mostrarTodosMensajes=true;
}

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
        
        
        $select_mensajes="SELECT * FROM mensajes_temporal WHERE mensajes_temporal.contactos='0' AND mensajes_temporal.fecha>='$fecha_inicio_mensajes'".$lista_filtros_aula." ORDER BY mensajes_temporal.fecha";      

        $busca_mensajes=db_query($select_mensajes);

        $hay_mensajes=false;

        $busca_mensajes=db_query($select_mensajes);
         
        // Todos los mensajes, en una tabla...
        if ($mostrarTodosMensajes==true) {
                ?>
                <table class="table table-striped table-bordered">
                        <tr>
                                <td>Fecha</td>
                                <td>Asunto</td>
                                <td>&nbsp;</td>
                        </tr>
                <?php
        }

        while($datos_mensajes=db_fetch_array($busca_mensajes)) {   

                $busca_lectura=db_query_params('SELECT * FROM mensajes_registro_lectura WHERE id_mensaje_email=? AND id_usuario=?', 'ss', array($datos_mensajes["id_mensaje"], $id_usuario)) or die(db_error());                

                if ($mostrarTodosMensajes==true) {
                        // Todos los mensajes, solo en asunto y un botón para abrirlo
                        $noleidoAbre="";
                        $noLeidoCierra="";   
                        $iconito = "fa-envelope-open";             
                        if (db_num_rows($busca_lectura)==0) {
                                $noleidoAbre="<b>";
                                $noLeidoCierra="</b>"; 
                                $iconito = "fa-envelope";  
                        }
                        ?>
                        <tr>
                                <td><?=$noleidoAbre.date("d/m/Y",strtotime($datos_mensajes["fecha"])).$noLeidoCierra;?></td>
                                <td><?=$noleidoAbre.security_escape($datos_mensajes["asunto"]).$noLeidoCierra;?></td>
                                <td class="text-center"><a class="btn btn-sm btn-primary abrir-mensaje" href="/app/ver_email.php?id_mensaje=<?=$datos_mensajes["id_mensaje"]?>"><i class="fa <?=$iconito?>" aria-hidden="true"></i> Abrir</a></td>
                        </tr>
                        <?php
                        $hay_mensajes=true;
                } else {
                        // Ultimos ménsajes, muestra todo el texto...
                        if (db_num_rows($busca_lectura)==0) {
                                $hay_mensajes=true;
                                
                                ?>
                                <div class="panel panel-success">
                                <div class="panel-heading">
                                <h3 class="panel-title"><?=$datos_mensajes["fecha"]?> - <?=security_escape($datos_mensajes["asunto"]);?></h3>
                                </div>
                                <div class="panel-body">
                                <?=utf8_decode($datos_mensajes["texto_editor"]);?>
                                </div>
                                </div>                
                                <?php
                                if (db_num_rows($busca_lectura)==0) {
                                        $introduce_resgistro_lectura=db_query_params('INSERT INTO mensajes_registro_lectura (id_mensaje_email, id_usuario) VALUES (?,?);', 'ss', array((string)($datos_mensajes["id_mensaje"]), (string)($id_usuario))); 
                                }
                        
                        }
                }

        }

        // Todos los mensajes, en una tabla...
        if ($mostrarTodosMensajes==true) {
                ?>
                </table">
                <?php
        }        

        if ($hay_mensajes==false) {
                ?>
                <div class="panel panel-default">
                        <div class="panel-body text-center">
                                <h3>No hay mensajes</h3>
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