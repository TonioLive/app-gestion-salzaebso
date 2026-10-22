<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha_limite=$_GET["fecha_limite"];

if (strlen($id_subgrupo)!=0) {

        $busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

        $datos_grupo=db_fetch_array($busca_datos_subgrupo);

        $id_curso_actual=$datos_grupo["id_curso"];
        $c_dia=$datos_grupo["dia"];
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        
}

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite)+$tiempo_gracia;

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

function chequea(id_falta) {

	var href = "/gestion/grupos_pruebas/cambia_checkbox.php?id_falta="+id_falta;

	var respuesta = $.ajax ({url: href}); 
	
	respuesta.always(function() {
				
		var textoRespuesta = respuesta.responseText;
				
		if( textoRespuesta.substr(0, 2) != "ok" ) {
				alert(textoRespuesta);						
			} else {
				alert(textoRespuesta.substr(4));
			}	
			
				
		});

}

function quitar_alumno(id_alumno,id_falta) {

        $('#boton_alumno' + id_alumno).attr('disabled',true);
        
        top.location.href = "desasigna_alumno_recuperacion.php?origen=<?=$_SERVER['HTTP_REFERER'];?>&id_falta="+id_falta+"&fecha=<?=$fecha?>&id_alumno=" + id_alumno + "&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>";

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

</script> 
</head>
<body>
<div class="container-fluid">
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
				<td><b>Confirmada</b></td>
                <td><b>Observaciones</b></td>       
                <td class="col-md-1">&nbsp;</td>                        
        </tr>
<?php 
        // Busca alumnos subgrupo       
        $busca_alumnos_grupo=db_query_params('SELECT * FROM usuarios, alumnos, servicios_consumidos_faltas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=servicios_consumidos_faltas.id_alumno AND alumnos.id_curso=? AND servicios_consumidos_faltas.id_grupo=? AND servicios_consumidos_faltas.id_subgrupo=? AND servicios_consumidos_faltas.fecha_recuperacion=? ORDER BY usuarios.Nombre, usuarios.Apellido1, usuarios.Apellido2', 'ssss', array($id_curso_actual, $id_grupo, $id_subgrupo, (string)(date("Y-m-d", strtotime($fecha_limite))))) or die(db_error());       
        
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
        
        $estado_boton="";
        if (!is_null($alumnos["hora_entrada"])) {
        
        	$estado_boton=" disabled='disabled'";	
        
        }
        
        if ($alumnos["confirmada"]==1) {
        	$estado_checkbox=" checked";
        }
                                                         
        ?>
        <tr<?=$color_fuente_linea;?> >
                <td><a href='javascript:muestra_detalles(<?=$alumnos["id_usuario"]?>)'><?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></a></td>
                <td><?=$edad_alumno;?><br /><small>Fecha Nac. <?=$fecha_nac_alumno?></small></td>
                <td><?=$fecha_incorporacion;?></td>
                <td><?=$fecha_baja;?></td>
                <td><?=$alumnos["telefono1"]." ".$alumnos["telefono2"]." ".$alumnos["movil"];?></td>
                <td><?=$lista_emails?></td>
                <td class="text-center"><input type="checkbox" onclick="chequea(<?=$alumnos["id_falta"];?>)"<?=$estado_checkbox;?>></td>                 
                <td><?=$lista_eventos;?></td>       
                <td><button class="btn btn-primary btn-xs" id="boton_editar<?=$alumnos["id_alumno"]?>" onclick="muestra_inscripcion(<?=$alumnos["id_alumno"]?>);" title="Editar inscripción alumno"> <i class="fa fa-pencil-square-o "></i></button> <button class="btn btn-danger btn-xs" id="boton_alumno<?=$alumnos["id_alumno"]?>" onclick="if (confirm('¿Deseas quitar a <?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?> del grupo?')) { quitar_alumno(<?=$alumnos["id_alumno"]?>,<?=$alumnos["id_falta"]?>); }" title="Quitar alumno del grupo"<?=$estado_boton?>> <i class="fa fa-user-times"></i></button></td>                                
        </tr>
        <?php 
        }
	}
?>
</table>        

</div>

<div class="modal modal-wide fade" id="alumnoModal" tabindex="-1" role="dialog" aria-labelledby="alumnoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_alumno"><h4 class="modal-title" id="alumnoModalLabel">Detalles alumno</h4></div>
      </div>
      <div class="modal-body">
        <iframe src="" name="detalles_alumno" id="detalles_alumno" width=100% height=450 frameborder=0></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
