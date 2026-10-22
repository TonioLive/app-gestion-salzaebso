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
        <li class="active"><i class="fa fa-envelope"></i> Notas educadora</li>
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
    $aulas_hijos_filtro_notas="";

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
            
            $centros_hijos.=" OR (Agenda.id_centro=".$datos_hijos["id_centro"]." AND Agenda.id_aula=0)";
            
            $aulas_hijos_notas.=" OR (agenda_alumnos_seguimiento.id_aula=".$datos_hijos["aula"].")";
            
            $aulas_hijos_filtro_notas.=" OR (agenda_alumnos_seguimiento.id_alumno=".$datos_hijos["id_alumno"].")";


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

    if (strlen($contador_hijos)!=0) {
        $aulas_hijos_notas="(".substr($aulas_hijos_notas,4,strlen($aulas_hijos_notas)-4).")";
        $aulas_hijos_filtro_notas="(".substr($aulas_hijos_filtro_notas,4,strlen($aulas_hijos_filtro_notas)-4).")";        
    }
                    
    // Notas educadora

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

        $busca_mensajes=db_query("SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND (agenda_alumnos_seguimiento.id_alumno=0 OR ($aulas_hijos_filtro_notas)) AND $aulas_hijos_notas$filtro_busqueda ORDER BY agenda_alumnos_seguimiento.dia DESC, agenda_alumnos_seguimiento.hora DESC") or die(db_error());

        $resultado_total=db_num_rows($busca_mensajes);
        $inicio_listado=$_GET["i"];
        $numero_resultados=10;
        if (strlen($inicio_listado)==0) {
            $inicio_listado=0;
        } 
        
        $nombe_script="notas_educadora";
        
        $busca_ultimos_mensajes=db_query("SELECT * FROM agenda_alumnos_seguimiento,agenda_alumnos_tipos_notas WHERE agenda_alumnos_seguimiento.tipo_nota=agenda_alumnos_tipos_notas.id_tipo AND agenda_alumnos_seguimiento.dia<='".date("Y-m-d")."' AND agenda_alumnos_tipos_notas.inicio_agenda=1 AND (agenda_alumnos_seguimiento.id_alumno=0 OR ($aulas_hijos_filtro_notas)) AND $aulas_hijos_notas$filtro_busqueda ORDER BY agenda_alumnos_seguimiento.dia DESC, agenda_alumnos_seguimiento.hora DESC LIMIT $inicio_listado,$numero_resultados"); 
        
        $total_resultado_busqueda=db_num_rows($busca_ultimos_mensajes);    
        
        ?>
        <div class="row">
            <div class="col-md-12"><h3>Notas de las educadoras</h3>
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
            <p align="center"><h3>No hay notas de las educadoras</h3></p>
            <?php
        } else {

            while($mensajes=db_fetch_array($busca_ultimos_mensajes)) {    

                $nombre_alumno="";
                if ($mensajes['id_alumno']!=0) {
                    // Muestra nombre del alumno al que se refiere la nota
                    $busca_nombre_alumno=db_query_params('SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($mensajes['id_alumno']));
                    if (db_num_rows($busca_nombre_alumno)!=0) {
                        $datos_nombre_alumno=db_fetch_array($busca_nombre_alumno);  
                        $nombre_alumno=" ".date("H:i",strtotime($mensajes['hora'])) . " - ". $datos_nombre_alumno["Nombre"]." ".$datos_nombre_alumno["Apellido1"]." ".$datos_nombre_alumno["Apellido2"];
                    }   
                }

            ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                    <h3 class="panel-title"><b><?=date("d/m/Y",strtotime($mensajes["dia"]));?><?=$nombre_alumno?></b></h3>                          
                    <?= htmlentities($mensajes["nombre_tipo"],ENT_QUOTES,"ISO8859-1"); ?> - Aula <?=$array_aulas_hijo[$mensajes["id_aula"]];?>
                    </div>
                    <div class="panel-body">
                    <?= security_html($mensajes["nota"]); ?>
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
        <p align="center"><h3>No hay notas de las educadoras</h3></p>
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