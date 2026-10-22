<?php  require("../../configuracion/parte_superior_privado_bootstrap.php"); ?>
<?php 
        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $fecha_ini_curso_act=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso_act=$datos_curso_actual["fecha_fin"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $fecha_ini_curso_act=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso_act=$datos_curso_actual["fecha_fin"];
                }
        }
        if ($id_curso_actual!=0) {
                                $filtro_curso=" AND fecha_incidencia>='$fecha_ini_curso_act' AND fecha_incidencia<='$fecha_fin_curso_act'";
                                } else {
                                $filtro_curso="";
                                }

$mostrar_todas=$_GET["todas"];
if ($mostrar_todas==1) {
        $filtro_curso="";
        }


$texto_titulo="Registro incidencias";

?>
<SCRIPT LANGUAGE=javascript>
<!--

function eliminar_incidencia(id_elim_incidencia) {
        if (confirm("¿Deseas eliminar la incidencia seleccionada?")) {
                window.location.href='eliminar_incidencia.php?c=<?=$filtro_consulta;?>&id_incidencia='+id_elim_incidencia;
                }
        }

//-->
</SCRIPT>
<div align="center"><center>
<form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso" class="form-inline">
                        <h3>Curso: <select name="curso_seleccionado" size=1  class="form-control" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></h3></form>
<table class="table table-striped table-hover">
	<tr>
		<td><b>Emitida por</b></td>
		<td><b>Seguimiento por</b></td>
		<td><b>Categoria</b></td>
		<td><b>Tipo</b></td>		
		<td><b>Código</b></td>
		<td><b>Fecha</b></td>
		<td><b>Descripción</b></td>
		<td><b>Fecha cierre</b></td>
		<td><b>Observaciones</b></td>
		<td>&nbsp;</td>
	</tr>
	<?php
    $busca_incidencias=db_query("SELECT * FROM incidencias_2017, incidencias_tipos_2017 WHERE incidencias_2017.tipo=incidencias_tipos_2017.id_tipo$filtro_curso ORDER BY incidencias_2017.fecha_incidencia DESC,incidencias_2017.tipo");
    if (db_num_rows($busca_incidencias)==0) {	
	?>	
    <tr>
    	<td class="text-center" colspan="9"><b>No hay incidencias para mostrar</b></td>
    </tr>
	<?php	
	} else {
		
		while($listado_incidencias=db_fetch_array($busca_incidencias)){
			
	        // Busca nombre de la persona que notifica
	        
	        $busca_notificador=db_query("SELECT * FROM usuarios WHERE id_usuario=".$listado_incidencias["notificada_por"]);
	        
	        if (db_num_rows($busca_notificador)!=0) {
                $datos_notificador=db_fetch_array($busca_notificador);
                $nombre_notificador=$datos_notificador["Apellido1"]." ".$datos_notificador["Apellido2"].", ".$datos_notificador["Nombre"];
            } else {
                $nombre_notificador="Desconocido";
            }
	        
	        
	        // Busca nombre de la persona que hace el seguimiento
	        
	        $busca_seguimiento=db_query("SELECT * FROM usuarios WHERE id_usuario=".$listado_incidencias["seguimiento_por"]);
	
	        if (db_num_rows($busca_seguimiento)!=0) {
	            $datos_seguimiento=db_fetch_array($busca_seguimiento);
	            $nombre_seguimiento=$datos_seguimiento["Apellido1"]." ".$datos_seguimiento["Apellido2"].", ".$datos_seguimiento["Nombre"];
	        } else {
	            $nombre_seguimiento="Desconocido";
	        }	
	        		
			$nombre_categoria_actual=$listado_incidencias["grupo"];
			$nombre_tipo_actual=$listado_incidencias["nombre_tipo"];
			
			$fecha_cierre="<span class='text-danger'>Abierta</span>";
			
			if ($listado_incidencias["resuelta"]==1) {
				
				$fecha_cierre="<span class='text-success'>Resuelta</span>";	
				
			}
			
			if ((strlen($listado_incidencias["fecha_cierre"])!=0) and ($listado_incidencias["fecha_cierre"]!="0000-00-00")) {
				
				$fecha_cierre="<span class='text-success'>Resuelta: ".date("d/m/Y",strtotime($listado_incidencias["fecha_cierre"]))."</span>";	
				
			}
			
	?>	
    <tr>
    	<td><?=$nombre_notificador?></td>
    	<td><?=$nombre_seguimiento?></td>
    	<td><?=$nombre_categoria_actual?></td>
    	<td><?=$nombre_tipo_actual?></td>
    	<td><?=$listado_incidencias["id_incidencia"]?>-<?=date("Y",strtotime($listado_incidencias["fecha_incidencia"]))?></td>
    	<td><?=date("d/m/Y",strtotime($listado_incidencias["fecha_incidencia"]))?></td>
    	<td><?=substr(urldecode($listado_incidencias["descripcion"]),0,256)?><?php if (strlen($listado_incidencias["descripcion"])>256) { ?><a href="detalles_incidencia_general.php?id_incidencia=<?=$listado_incidencias["id_incidencia"];?>" class="text-primary">...Ver más</a><?php } ?></td>
    	<td><?=$fecha_cierre?></td>
    	<td><?=substr(urldecode($listado_incidencias["medidas_adoptadas1"]),0,256)?><?php if (strlen($listado_incidencias["medidas_adoptadas1"])>256) { ?><a href="detalles_incidencia_general.php?id_incidencia=<?=$listado_incidencias["id_incidencia"];?>" class="text-primary">...Ver más</a><?php } ?></td>
    	<td><a class="btn btn-sm btn-primary" href="detalles_incidencia_general.php?id_incidencia=<?=$listado_incidencias["id_incidencia"];?>" style="margin-left: 5px;margin-top: 5px;margin-right: 5px;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <button type="button" class="btn btn-sm btn-danger" onclick="eliminar_incidencia(<?=$listado_incidencias["id_incidencia"];?>)" style="margin-left: 5px;margin-top: 5px;margin-right: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
    </tr>
	<?php	
		}
		
		
	}	
	?>	
</table>                                               
<br>
<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='incidencias_general.php?todas=1';">Mostrar las incidencias de cualquier curso</button>  <button type="button" class="btn btn-danger btn-sm" onclick="window.location.href='menu_incidencias.php'">Salir</button>
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

