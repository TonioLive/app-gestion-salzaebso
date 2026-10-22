<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Recibos 
        <small>Estado de cuentas</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="/clientes"><i class="fa fa-home"></i> Inicio</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
	  
    <div class="box box-primary">
        <div class="box-header with-border">	  
	  
    <?php 
    // Busca hijos del padre

$fechalimite=date('Y-m-d', strtotime('+3 months'));

    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? AND alumnos.fecha_matricula<=? ORDER BY alumnos.id_curso DESC', 'sss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)), (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {

        ?>
    <table width="98%" class="table table-bordered table-hover">
        <tr>
                <td><b>Curso</b></td>
                <td><b>Grupo</b></td>
                <td><b>Fechas inicio y baja</b></td>
                <td><b>Nombre</b></td>
                <td>&nbsp;</td>
        </tr>
        <?php 
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
                
                
                // Datos del grupo del niño
	                $busca_grupos_alumno=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_grupos.id_curso=? AND alumnos.id_usuario=?', 'sss', array($datos_hijos["id_alumno"], $datos_hijos["id_curso"], $datos_hijos["padre_de"])) or die(db_error());                
	                
	                $lista_grupos_alumno="";
	        		while($grupos_donde=db_fetch_array($busca_grupos_alumno)) {
	                	$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))."<br />";
	                	}

                // Busca ultima factura emitida
                
                
                ?>
        <tr>
                <td><?=$nombre_curso_mostrado;?></td>
                <td class="text-center"><?=$lista_grupos_alumno;?></td>
                <td class="text-center"><?=$datos_hijos["fecha_matricula"];?> - <?=$datos_hijos["fecha_baja"]?></td>
                <td class="text-center"><?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></td>
                <td class="text-center" width=125><input type="button" value="Ver Recibos" class="btn btn-responsive btn-sm btn-primary" onclick="window.location.href='ver_recibos.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>'"></td>
        </tr>
                <?php 
                $contador_hijos++;
                }
        ?>
        </table>
        <?php 
        }
    
    ?>
		</div>	  
	</div>
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>