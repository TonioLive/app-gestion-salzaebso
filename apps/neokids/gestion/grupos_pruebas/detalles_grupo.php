<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha_limite=$_GET["fecha_limite"];

if (strlen($id_subgrupo)!=0) {

        $busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

        $datos_grupo=db_fetch_array($busca_datos_subgrupo);

		$id_grupo_subgrupo=$datos_grupo["id_grupo_subgrupo"];
        $id_curso_actual=$datos_grupo["id_curso"];
        $c_dia=$datos_grupo["dia"];
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        $inicio_curso_actual=$datos_curso_actual["fecha_inicio"];
        $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];             
               
}

$tiempo_gracia=0; 
$permitir_tiempo_gracia=1; // Cambiar para curso normal
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite)+$tiempo_gracia;
$fecha_limite=date('Y-m-d',$fecha_actual);

$estado_actual_grupo="";

if ($datos_grupo["bloqueado"]==1) {

	$estado_actual_grupo=" checked";	

	$usuario_bloqueo=$datos_grupo["usuario_bloqueo"];
	
	if ($usuario_bloqueo) {
		
		$busca_nombre_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=$usuario_bloqueo");
	
		if (db_num_rows($busca_nombre_usuario)!=0) {
		
			$datos_usuario_bloqueo=db_fetch_array($busca_nombre_usuario);
		
			$nombre_usuario_bloqueo=$datos_usuario_bloqueo["Nombre"]." ".$datos_usuario_bloqueo["Apellido1"]." ".$datos_usuario_bloqueo["Apellido2"];
		
			}						
	}
}

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

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<link rel="stylesheet" href="style.css">

<style>
.fondo_nivelB {
        background: #000;
        }                        
 
.textoBlanco {
                color: #fff;
                } 
                
.panelFijo{
    height: 1000vh;
    overflow-y: no;
    overflow-x: hidden;
}
 
.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 
</style>

<script>

function quitar_alumno(id_alumno) {

        $('#boton_alumno' + id_alumno).attr('disabled',true);
        
        top.location.href = "desasigna_alumno.php?origen=<?=$_SERVER['HTTP_REFERER'];?>&fecha=<?=$fecha?>&id_alumno=" + id_alumno + "&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>";

}

function muestra_detalles(id_usuario) {
        
        $('#alumnoModal').modal('show');
        $('#detalles_alumno').attr('src', 'detalles_alumno.php?id_usuario='+id_usuario);
        
        }

		$(document).on('hidden.bs.modal','#alumnoModal', function () {
			top.document.getElementById('detalles_grupo').contentWindow.location.reload();	
		});

function muestra_inscripcion(fila) {
        window.open('/alumnos/editar_inscripcion.php?origen_formulario=1&i='+fila,'matriculas','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
        }
        

function copiar_grupo() {

	if (confirm("Se inscribirán todos los alumnos del grupo en el próximo curso. ¿Estás seguro/a?")) {
	
		self.location.href='copiar_grupo.php?id_grupo=<?=$id_grupo;?>&id_subgrupo=<?=$id_subgrupo;?>&fecha_limite=<?=$fecha_limite;?>';
	
		}

	}
	
function copiar_alumno(id_alumno) {

	if (confirm("Se inscribirá al los alumno en el próximo. ¿Estás seguro/a?")) {
	
		self.location.href='copiar_grupo.php?id_alumno='+id_alumno+'&id_grupo=<?=$id_grupo;?>&id_subgrupo=<?=$id_subgrupo;?>&fecha_limite=<?=$fecha_limite;?>';
	
		}

	}	

function mail_todos() {

	$('[id^=email]').click();

	} 
	
function sms_todos() {

	$('[id^=sms]').click();

	}
        
function mail_todos_e() {

	$('[id^=e_email]').click();

	} 
	
function sms_todos_e() {

	$('[id^=e_sms]').click();

	}	       

function enviar_mail() {

	var count_checked1 = $("[name='email[]']:checked").length
	var count_checked2 = $("[name='e_email[]']:checked").length
	
	if ( (count_checked1+count_checked2) > 0 ) {
	
		if (confirm("Se va a enviar un Email a todos los marcados.\n\n¿Estás seguro/a?")) {
			document.detalles_grupo.action="envios/enviar_email_todos.php";
			$('#enviosModal').modal('show');
			document.detalles_grupo.submit();
			}	
		} else {
			alert("No has seleccionado ningún alumno para el envío de emails.");
		}

	}
	
function enviar_sms() {

	var count_checked1 = $("[name='sms[]']:checked").length
	var count_checked2 = $("[name='e_sms[]']:checked").length
	
	if ( (count_checked1+count_checked2) > 0 ) {

		if (confirm("Se va a enviar un SMS a todos los marcados, esto tiene un coste.\n\n¿Estás seguro/a?")) {
			document.detalles_grupo.action="envios/enviar_sms_todos.php";
			$('#enviosModal').modal('show');
			document.detalles_grupo.submit();
			}
		} else {
			alert("No has seleccionado ningún alumno para el envío de SMS.");
		}

	}
	
function eliminar_todo(id_alumno) {

	if (confirm("Se va a eliminar completamente el alumno de este curso, esta acción no se puede deshacer.\n\n¿Estás seguro/a?")) {
		window.location.href='borrar_totalmente_alumno.php?id_alumno='+id_alumno+'&id_curso=<?=$id_curso_actual?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&fecha_limite=<?=$fecha_limite?>';
	}

}

</script> 
</head>
<body>
<form method="POST" name="detalles_grupo" action="enviar_email_todos.php" target="mensajes_alumnos">
<input type="hidden" name="id_grupo" value="<?=$_GET["id_grupo"];?>">
<input type="hidden" name="id_subgrupo" value="<?=$_GET["id_subgrupo"];?>">
<input type="hidden" name="fecha_limite" value="<?=$_GET["fecha_limite"];?>">

<div class="container-fluid">
<div class="panel panel-default">
  <div class="panel-body">
	<div class="row">
		<div class="col-md-7">
					<button class="btn btn-success btn-sm" onclick="copiar_grupo();"><i class="fa fa-files-o" aria-hidden="true"></i>  Copiar grupo a otro curso</button>
					<button class="btn btn-primary btn-sm" onclick="enviar_mail();"><i class="fa fa-envelope" aria-hidden="true"></i>  Enviar email a los marcados</button>
					<button class="btn btn-primary btn-sm" onclick="enviar_sms();"><i class="fa fa-comment-o" aria-hidden="true"></i> Enviar SMS a los marcados</button>
		</div>
		<div class="col-md-5 text-right">
			Estado de las inscripciones en el grupo: <input data-toggle='toggle' id='estado_grupo' value='<?=$id_grupo_subgrupo;?>' data-size='mini' data-onstyle='danger' data-offstyle='primary' data-on='Bloqueado' data-off='Activo' type='checkbox'<?=$estado_actual_grupo?>>
		</div>
	</div>    
  </div>
</div>
<?php 
// Busca alumnos grupo
if (strlen($id_subgrupo)!=0) {
?>
<table class="table table-condensed table-striped">
        <tr>
                <td><b>Nombre</b></td>
                <td><b>Edad</b></td>                        
                <td><b>Fecha Inicio</b></td>
                <td><b>Fecha Baja</b></td>
                <td><b>Teléfono</b></td>
                <td><b>Email</b></td> 
                <td><b>Observaciones</b></td>     
                <td class="col-md-1"><a href='javascript: mail_todos()'><i class="fa fa-envelope" aria-hidden="true"></i></a> <a href='javascript: sms_todos()'><i class="fa fa-comment-o" aria-hidden="true"></i></a></td>   
                <td class="col-md-2">&nbsp;</td>                        
        </tr>
<?php 
        // Busca alumnos subgrupo
        
        $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND ((alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_subgrupo=? AND aulas_alumnos_grupos.id_grupo=? AND alumnos.fecha_baja>=?) OR (alumnos.tipo_baja<>0 AND aulas_alumnos_grupos.id_subgrupo=? AND aulas_alumnos_grupos.id_grupo=? AND alumnos.fecha_baja>=?))', 'ssssss', array($id_subgrupo, $id_grupo, (string)($fecha_limite), $id_subgrupo, $id_grupo, (string)($fecha_limite))) or die(db_error());
        		
        while($alumnos=db_fetch_array($busca_alumnos_grupo)) {                
        
// Fecha de incorporación
                $fecha_incorporacion=date("d/m/Y",strtotime($alumnos["fecha_matricula"]));
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                        $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</span>";
                        }
                $fecha_baja=date("d/m/Y",strtotime($alumnos["fecha_baja"]));
                if (strtotime($alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                        $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_baja"]))."</span>";
                        }
                if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));       
        
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
                
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;
                                if ($dif_meses==0) {
                                        $edad_d_l="";
                                        } else {
                                        $unidad_meses="meses";
                                        if ($dif_meses==1) {
                                                $unidad_meses="mes";
                                                }
                                        if ($dif_meses<0) {
                                                $dif_meses=12+$dif_meses;
                                                $edad_a_l=$edad_a_l-1;
                                        }
                                        $edad_d_l=" y ".$dif_meses." ".$unidad_meses;
                                        }                                
                $edad_alumno=$edad_a_l." años".$edad_d_l;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 
        
        $color_fuente_linea="";
    if ($alumnos["fecha_baja"]<date("Y-m-d",$fecha_actual)) {
                $color_fuente_linea=" class='text-danger'";
                }
         
        if ($alumnos["tipo_baja"]==2) {
        			$color_fuente_linea=" class='fondo_nivelB textoBlanco'";	
        		}
        		
		$busca_notas_seguimiento=db_query_params('SELECT * FROM alumnos_seguimiento WHERE id_usuario=? ORDER BY fecha_creacion', 's', array($alumnos["id_usuario"]));
                                
						$lista_eventos="";
  						while($nota=db_fetch_array($busca_notas_seguimiento)) {
  							$lista_eventos.=date("d/m/Y",strtotime($nota["fecha_creacion"])).": ". $nota["comentarios"]."<br />";
  							}         		
        		

						// Array con todos los emails del alumno

                        $listado_emails=str_replace(" ","",$alumnos["email"]);

                        $array_to = explode(",",$listado_emails);
        				
        				$lista_emails="";
        
                        foreach ($array_to as $to) {        
        
                                if (comprobar_email($to)!=0) {
                                
                                	$lista_emails.='<a href="mailto: '.$to.'">'.$to.'</a> ';
                                
                                }
                        }  
                                                         
        ?>
        <tr<?=$color_fuente_linea;?> >
                <td><a href='javascript:muestra_detalles(<?=$alumnos["id_usuario"]?>)'><?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></a></td>
                <td><?=$edad_alumno;?><br /><small>Fecha Nac. <?=$fecha_nac_alumno?></small></td>
                <td><?=$fecha_incorporacion;?></td>
                <td><?=$fecha_baja;?></td>
                <td><?=$alumnos["telefono1"]." ".$alumnos["telefono2"]." ".$alumnos["movil"];?></td>
                <td><?=$lista_emails?></td>
                <td><?=$lista_eventos;?></td>   
                <td><input type="checkbox" name="email[]" id="email<?=$alumnos["id_usuario"]?>" value="<?=$alumnos["id_usuario"]?>"> <input type="checkbox" name="sms[]" id="sms<?=$alumnos["id_usuario"]?>" value="<?=$alumnos["id_usuario"]?>"></td>     
                <td><button class="btn btn-primary btn-xs" id="boton_editar<?=$alumnos["id_alumno"]?>" onclick="muestra_inscripcion(<?=$alumnos["id_alumno"]?>);" title="Editar inscripción alumno"> <i class="fa fa-pencil-square-o "></i></button> <button class="btn btn-danger btn-xs" id="boton_alumno<?=$alumnos["id_alumno"]?>" onclick="if (confirm('¿Deseas quitar a <?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?> del grupo?')) { quitar_alumno(<?=$alumnos["id_alumno"]?>); }" title="Quitar alumno del grupo"> <i class="fa fa-user-times"></i></button> <button class="btn btn-success btn-xs" id="boton_copiar<?=$alumnos["id_alumno"]?>" onclick="copiar_alumno(<?=$alumnos["id_alumno"]?>);" title="Copiar inscripción alumno"> <i class="fa fa-files-o "></i></button> <button class="btn btn-warning btn-xs" id="boton_servicios<?=$alumnos["id_alumno"]?>" onclick="window.parent.editarservicios(<?=$alumnos["id_alumno"]?>);" title="Ver servicios contratados"> &nbsp;<i class="fa fa-eur"></i>&nbsp;</button> <button class="btn btn-info btn-xs" id="boton_inscripcion<?=$alumnos["id_alumno"]?>" onclick="window.parent.editarinscripciones(<?=$alumnos["id_usuario"]?>);" title="Editar inscripciones"> &nbsp;<i class="fa fa-graduation-cap"></i>&nbsp;</button> <?php  if (strtotime($inicio_curso_actual)>time()) { ?><br /><button class="btn btn-danger btn-xs" onclick="eliminar_todo(<?=$alumnos["id_alumno"];?>)"><i class="fa fa-trash"></i> Eliminar alumno&nbsp;</button><?php  } ?></td>                                
        </tr>
        <?php 
        }
?>
</table>        
<?php         

$busca_lista_espera=db_query("SELECT * FROM lista_espera,usuarios,lista_espera_orden WHERE lista_espera.id_usuario=usuarios.id_usuario AND lista_espera.id_usuario=lista_espera_orden.id_usuario AND lista_espera.id_curso=$id_curso_actual AND lista_espera.dia_deseado_$c_dia=1 AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_inicio"]."' AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_fin"]."' AND lista_espera.fin_horario_deseado_$c_dia>='".$datos_grupo["hora_fin"]."' AND lista_espera_orden.id_grupo=$id_grupo ORDER BY lista_espera_orden.orden");

?>
<h4>Lista de espera</h4>
<table class="table table-condensed table-striped">
        <tr>
        	<td class="col-md-1"><b>Nº</b></td>
        	<td class="col-md-2"><b>Nombre</b></td>
        	<td class="col-md-1"><b>Fecha inscripción</b></td>
        	<td class="col-md-2"><b>Fecha deseada de incorporación y horarios</b></td>
        	<td class="col-md-2"><b>Observaciones</b></td>
        	<td class="col-md-2"><b>Teléfono</b></td>
        	<td class="col-md-1"><b>Email</b></td> 
			<td class="col-md-1"><a href='javascript: mail_todos_e()'><i class="fa fa-envelope" aria-hidden="true"></i></a> <a href='javascript: sms_todos_e()'><i class="fa fa-comment-o" aria-hidden="true"></i></a></td>                                               
        </tr>
<?php 
                       $total_en_lista=db_num_rows($busca_lista_espera); 
                        
                while($datos_lista_espera=db_fetch_array($busca_lista_espera)) {
                
                        $fecha_deseada="";
                                        if ((strlen($datos_lista_espera["fecha_deseada"])!=0) and ($datos_lista_espera["fecha_deseada"]!="0000-00-00")) {
                                        $fecha_deseada=date("d/m/Y",strtotime($datos_lista_espera["fecha_deseada"]))."<br>";
                                        }
                                        

                        $estilo_fila="";
                        $formato_fila1="";
                        $formato_fila2="";
                        $boton_tachado="";
                        $boton_borrado_color="danger";
                        $boton_borrado_imagen="down";
                        $boton_borrado_mensaje="Descartar de este horario";
                        if ($datos_lista_espera["no_mostrado"]==1) {
                                $estilo_fila=" class='danger'";
                                $formato_fila1="<strike>";
                                $formato_fila2="</strike>";
                                $boton_borrado_color="primary";
                                $boton_borrado_imagen="up";
                                $boton_borrado_mensaje="Volver a incluir en este horario";
                                }
                         
                        $busca_notas_seguimiento=db_query_params('SELECT * FROM alumnos_seguimiento WHERE id_usuario=? ORDER BY fecha_creacion', 's', array($datos_lista_espera["id_usuario"]));
                                
						$lista_eventos="<br />";
  						while($nota=db_fetch_array($busca_notas_seguimiento)) {
  							$lista_eventos.=date("d/m/Y",strtotime($nota["fecha_creacion"])).": ". $nota["comentarios"]."<br />";
  							}                                  

                        ?>
                        <tr<?=$estilo_fila;?>>
                                <td><?=$formato_fila1.$datos_lista_espera["orden"].$formato_fila2;?></td>
                                <td><a href='javascript:muestra_detalles(<?=$datos_lista_espera["id_usuario"]?>)'><?=$formato_fila1.$datos_lista_espera["Nombre"]." ".$datos_lista_espera["Apellido1"]." ".$datos_lista_espera["Apellido2"].$formato_fila2;?></a></td>
                                <td><?=$formato_fila1.date("d/m/Y",strtotime($datos_lista_espera["fecha_alta"])).$formato_fila2?></td>
                                                                <td><?=$formato_fila1.$fecha_deseada?><?php 
                                
                                for ($c_dia=1; $c_dia<=7; $c_dia=$c_dia+1) {
                
                                                        $dia_actual=$datos_lista_espera["dia_deseado_".$c_dia];
                        
                                                        if ($dia_actual==1) {
                                
                                                                $hora_inicio_actual=date("H:i",strtotime($datos_lista_espera["ini_horario_deseado_".$c_dia]));
                                                                $hora_fin_actual=date("H:i",strtotime($datos_lista_espera["fin_horario_deseado_".$c_dia]));
                                
                                                                echo($dia_sem_largo[$c_dia]." de ".$hora_inicio_actual." a ".$hora_fin_actual." hrs.<br>");
                                                                                                                        
                                                                }
                                
                                                        }
                                                        
                                                        
						// Array con todos los emails del alumno

                        $listado_emails=str_replace(" ","",$datos_lista_espera["email"]);

                        $array_to = explode(",",$listado_emails);
        				
        				$lista_emails="";
        
                        foreach ($array_to as $to) {        
        
                                if (comprobar_email($to)!=0) {
                                
                                	$lista_emails.='<a href="mailto: '.$to.'">'.$to.'</a> ';
                                
                                }
                        }                                                        
                                                        
                                
                                ?><?=$formato_fila2?></td>
                                <td><?=$formato_fila1.$datos_lista_espera["observaciones_lista_espera"].$lista_eventos.$formato_fila2;?></td>
                                                                <td><?=$datos_lista_espera["telefono1"]." ".$datos_lista_espera["telefono2"]." ".$datos_lista_espera["movil"];?></td>
                                                                <td><?=$lista_emails?></td> 
                                                                <td><input type="checkbox" name="e_email[]" id="e_email<?=$datos_lista_espera["id_usuario"]?>" value="<?=$datos_lista_espera["id_usuario"]?>"> <input type="checkbox" name="e_sms[]" id="e_sms<?=$datos_lista_espera["id_usuario"]?>" value="<?=$datos_lista_espera["id_usuario"]?>"></td>                                
                        </tr>
        <?php 
        }
?>
</table>
<?php 
}
?>
</div>
</form>

<div class="modal modal-wide fade" id="alumnoModal" tabindex="-1" role="dialog" aria-labelledby="alumnoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_alumno"><h4 class="modal-title" id="alumnoModalLabel">Detalles alumno</h4></div>
      </div>
      <div class="modal-body">
        <iframe src="" name="detalles_alumno" id="detalles_alumno" width=100% height=260 frameborder=0></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-wide fade" id="enviosModal" tabindex="-1" role="dialog" aria-labelledby="enviosModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="titulo_envios"><h4 class="modal-title" id="enviosModalLabel">Envío de mensajes a los alumnos</h4></div>
      </div>
      <div class="modal-body">
        <iframe src="" name="mensajes_alumnos" id="mensajes_alumnos" width=100% height=260 frameborder=0></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>

$( document ).ready(function() {

    $('#estado_grupo').change(function() {
      
      	<?php 
      	if ((($usuario_bloqueo==$idUsuario) and ($datos_grupo["bloqueado"]==1)) or ($datos_grupo["bloqueado"]==0)) {
      	?>
      	
		var href = "/gestion/grupos_pruebas/cambia_bloqueo.php?id_subgrupo="+$(this).val();

		var respuesta = $.ajax ({url: href}); 
	
		respuesta.always(function() {
				
			var textoRespuesta = respuesta.responseText;
				
			if( textoRespuesta.substr(0, 2) != "ok" ) {
					alert(textoRespuesta);						
				} else {
					alert(textoRespuesta.substr(4));
				}	
							
			});       	
      	
      	<?php 
      	} else {
      	?>
      	if(!$(this).is(':checked')) {
      	alert("Solo el usuario <?=$nombre_usuario_bloqueo?> puede desbloquear este grupo");
      	}
      	$('#estado_grupo').bootstrapToggle('on')
      	<?php 
      	}
      	?>
          
      
    })

});

</script>
</body>
</html>
