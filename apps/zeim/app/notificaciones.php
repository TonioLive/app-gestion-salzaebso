<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        &nbsp;
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
        <li class="active"><i class="fa fa-bell-o"></i> Notificaciones recibidas</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php     
   $array_aulas_hijo=array();
    
if ($errores=="") {
    ?>
    <div align="center">
    <?php             
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE id_usuario=?', 's', array($id_usuario));
    while($padres=db_fetch_array($busca_padres)){
        $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
    }
    if ($filtro_padres!="") {
        $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
    }      
      
    // Busca hijos del padre

    $aulas_hijos="";
    $centros_hijos="";
    $aulas_hijos_notas="";
        
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? ORDER BY alumnos.id_curso DESC', 'ss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        $contador_hijos=0;
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
            $busca_datos_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_hijos["id_curso"]));
            if (db_num_rows($busca_datos_cursos)!=0) {
                    $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                    $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                    } else {
                    $nombre_curso_mostrado="";
                    }
                    
            // Datos de la escuela del niño
            $busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_hijos["id_centro"]));

            $datos_escuela=db_fetch_array($busca_datos_escuela);

            // Datos del aula del niño
            $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

            $datos_aula=db_fetch_array($busca_datos_aula);

            $array_aulas_hijo[$datos_hijos["aula"]]=$datos_aula["nombre"];

            $aulas_hijos.=" OR (Agenda.id_aula=".$datos_hijos["aula"].")";
            
            $centros_hijos.=" OR (tipo='centro' AND destino=".$datos_hijos["id_centro"].")";
            
            $aulas_hijos_notas.=" OR (tipo='aula' AND destino=".$datos_hijos["aula"].")";
            
            $contador_hijos++;
        }
    }

    $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

    if (db_num_rows($busca_curso_actual)!=0) {
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
    } else {
        $id_curso_actual=0;
    }

    $hay_avisos=false;


    $filtro_usuarios=" AND (Agenda.id_centro=0$centros_hijos$aulas_hijos)";

    /*
    if (strlen($contador_hijos)!=0) {
        $centros_hijos="(".substr($centros_hijos,4,strlen($centros_hijos)-4).")";
        $aulas_hijos_notas="(".substr($aulas_hijos_notas,4,strlen($aulas_hijos_notas)-4).")";
    }
    */
                    
    // Notificaciones

    $filtro_busqueda="";

    $busqueda=$_GET["q"];
    $tipo_b="OR";

    if ($busqueda) {

        // Desglosa una frase en palabras sueltas

            $palabra=explode(" ",$busqueda);

            $query="(";
            for ($cont=0; $cont<count($palabra); $cont++)
                {
                $query=$query."(agenda_alumnos_seguimiento.nota like '%".$palabra[$cont]."%')";
                $query=$query." ".$tipo_b." ";

                $palabra_actual=$palabra_actual+1;
                }
            
            if ($tipo_b=="OR") { $cacho=4; }
            if ($tipo_b=="AND") { $cacho=5; }
            $query=substr($query,0,(strlen($query)-$cacho));
            $query=$query.")";

        $filtro_busqueda=" AND $query";		

    } 	

    if (strlen($aulas_hijos_notas)!=0) {

        $busca_mensajes=db_query("SELECT * FROM notificaciones_app WHERE fecha_envio<='".date("Y-m-d")."' AND enviada=1 AND ((tipo='token' AND destino='$id_usuario') OR (tipo='topic' AND destino='todos')$aulas_hijos_notas$centros_hijos)$filtro_busqueda ORDER BY fecha_enviada DESC") or die(db_error());

        $resultado_total=db_num_rows($busca_mensajes);
        $inicio_listado=$_GET["i"];
        $numero_resultados=10;
        if (strlen($inicio_listado)==0) {
            $inicio_listado=0;
        } 
        
        $nombe_script="notificaciones";
        
        $busca_ultimos_mensajes=db_query("SELECT * FROM notificaciones_app WHERE fecha_envio<='".date("Y-m-d")."' AND enviada=1 AND ((tipo='token' AND destino='$id_usuario') OR (tipo='topic' AND destino='todos')$aulas_hijos_notas$centros_hijos)$filtro_busqueda ORDER BY fecha_enviada DESC LIMIT $inicio_listado,$numero_resultados"); 
        
        $total_resultado_busqueda=db_num_rows($busca_ultimos_mensajes);    
        
        ?>
        <div class="row">
            <div class="col-md-12"><h3>Notificiaciones recibidas</h3>
                <form class="form-inline" method="get" action="<?=$nombe_script?>.php">
                    <div class="form-group">
                        <input type="text" class="form-control" id="q" name="q" placeholder="Buscar..." value="<?=$busqueda?>">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
                    </div>	
                </form>
            </div>
        </div>
        <?php
        if (db_num_rows($busca_mensajes)==0) {
            ?>
            <p align="center"><h3>No hay notificaciones</h3></p>
            <?php
        } else {

            while($mensajes=db_fetch_array($busca_ultimos_mensajes)) {    
            ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                    <h3 class="panel-title"><b><?=date("d/m/Y H:i:s",strtotime($mensajes["fecha_enviada"]));?></b></h3>                          
                    </div>
                    <div class="panel-body">
                    <?=$mensajes["mensaje"];?>
                    <?php
                    if ($mensajes["id_mensaje"]!=0) {
                        $buscaMensaje=db_query_params('SELECT * FROM agenda_notas_padres WHERE id_nota_padre=?;', 's', array($mensajes["id_mensaje"]));
                        if (db_num_rows($buscaMensaje)!=0) {
                            $datosMensaje=db_fetch_array($buscaMensaje);
                            ?>
                            <br /><b><?=security_escape($datosMensaje['asunto'])?></b>
                            <?php
                        }
                        ?>
                        <br />
                        <a class='btn btn-primary btn-sm' href='ver_mensaje.php?id_mensaje=<?=$mensajes["id_mensaje"]?>'>Abrir mensaje</a>
                        <?php 
                    }
                    if ($mensajes["id_nota_seguimiento"]!=0) {
                        ?>
                        <br />
                        <a class='btn btn-primary btn-sm' href='notas_educadora.php'>Ir a notas educadora...</a>
                        <?php 
                    }
                    ?>
                    </div>
                </div>    
            <?php
            }
            ?>
            <div align="left"><?php  include("paginacion.php"); ?></div> 
            <?php
        }

    } else {
        ?>
        <p align="center"><h3>No hay notificaciones</h3></p>
        <?php
    }

}
?>
	  
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>