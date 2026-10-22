<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$nombre_mostrar=$_GET["nombre_mostrar"];
$filtro_nivel=$_GET["filtro_nivel"];
// Busca monitores en grupo

$monitor=array();

$busca_monitores=db_query_params('SELECT * FROM aulas_grupos_monitores WHERE id_grupo=? AND id_subgrupo=?', 'ss', array($id_grupo, $id_subgrupo)) or die(db_error());

if (db_num_rows($busca_monitores)==0) {
        $inserta1=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,1);', 'ss', array($id_grupo, $id_subgrupo));
        $inserta2=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,2);', 'ss', array($id_grupo, $id_subgrupo));
        $inserta3=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,3);', 'ss', array($id_grupo, $id_subgrupo));
        $inserta4=db_query_params('INSERT INTO aulas_grupos_monitores (id_grupo,id_subgrupo,num_monitor) VALUES (?,?,4);', 'ss', array($id_grupo, $id_subgrupo));        } else {
        while($lista_monitores=db_fetch_array($busca_monitores)) {
                $monitor[$lista_monitores["num_monitor"]]=$lista_monitores["id_usuario"];
                }
        }

$busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=? AND id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

$datos_subgrupo=db_fetch_array($busca_datos_subgrupo);

$busca_grupos=db_query_params('SELECT * FROM aulas_grupos WHERE id_grupo=?;', 's', array($id_grupo));
     
$datos_grupo=db_fetch_array($busca_grupos);

$id_curso_grupo=$datos_grupo["id_curso"];

$dia_grupo=$datos_grupo["dia"];

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Lista de Espera</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>
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

<?php 

$array_listas_de_espera=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");
$colores_listas_de_espera=array("#fff","#9ed09c","#9cbdd0","#d0af9c","#f9a63b","#ffa9f0");

for ($c_anios=0;$c_anios<count($colores_listas_de_espera);$c_anios++) {
?>
.fondo_espera<?=$c_anios?> {
        background: <?=$colores_listas_de_espera[$c_anios];?> ;
        }
<?php 
        }
?>        

</style>

<script>

window.onbeforeunload = closingCode;
function closingCode(){
   // window.opener.history.go(0);
   return null;
}

function mail_todos() {

	$('[id^=email]').click();

	} 
	
function sms_todos() {

	$('[id^=sms]').click();

	}       	       

function enviar_mail() {

	var count_checked1 = $("[name='email[]']:checked").length
	
	if ( count_checked1 > 0 ) {
	
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
	
	if ( count_checked1 > 0 ) {

		if (confirm("Se va a enviar un SMS a todos los marcados, esto tiene un coste.\n\n¿Estás seguro/a?")) {
			document.detalles_grupo.action="envios/enviar_sms_todos.php";
			$('#enviosModal').modal('show');
			document.detalles_grupo.submit();
			}
		} else {
			alert("No has seleccionado ningún alumno para el envío de SMS.");
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
<br />
<div class="panel panel-default">    
                      <div class="panel-heading">
                      	<div class="row">
							<div class="col-md-6">
			                      <h2>Lista de espera</h2><?=$_GET["nombre_mostrar"];?>, Grupo <?=$_GET["id_subgrupo"]+1;?>
							</div>                      	
							<div class="col-md-6 text-right">
								<br />
								<button class="btn btn-primary btn-sm" onclick="enviar_mail();"><i class="fa fa-envelope" aria-hidden="true"></i>  Enviar email a los marcados</button>
								<button class="btn btn-primary btn-sm" onclick="enviar_sms();"><i class="fa fa-comment-o" aria-hidden="true"></i> Enviar SMS a los marcados</button> <br /><br /> Filtrar por nivel:

						<button <?php  if (strlen($filtro_nivel)==0) { print " disabled"; } else { ?> style="background-color: <?=$colores_subniveles[0]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>'">Todos</button>
						<button <?php  if (($filtro_nivel==0) and (strlen($filtro_nivel)!=0)) { print " disabled"; } else { ?> style="background-color: <?=$colores_subniveles[0]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>&filtro_nivel=0'">0</button>
                        <button value="1"<?php  if ($filtro_nivel==1) { print " disabled"; } else { ?> style="background-color: <?=$colores_subniveles[1]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>&filtro_nivel=1'">1.1</button>
                        <button value="2"<?php  if ($filtro_nivel==2) { print " disabled";  } else { ?> style="background-color: <?=$colores_subniveles[2]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>&filtro_nivel=2'">1.2</button>
                        <button value="4"<?php  if ($filtro_nivel==4) { print " disabled"; } else { ?> style="background-color: <?=$colores_subniveles[4]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>&filtro_nivel=4'">2.1</button>
                        <button value="5"<?php  if ($filtro_nivel==5) { print " disabled"; } else { ?> style="background-color: <?=$colores_subniveles[5]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>&filtro_nivel=5'">2.2</button>
                        <button value="7"<?php  if ($filtro_nivel==7) { print " disabled"; } else { ?> style="background-color: <?=$colores_subniveles[7]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>&filtro_nivel=7'">3.1</button>
                        <button value="8"<?php  if ($filtro_nivel==8) { print " disabled"; } else { ?> style="background-color: <?=$colores_subniveles[8]?>; color: #fff"<?php } ?> onclick="self.location.href='lista_espera.php??fecha=<?=$fecha?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>&filtro_nivel=8'">3.2</button>

							</div>
						</div> 
                      </div>
                            <div class="panel-body">
                            <table class="table table-hover">
                            <tr>
                                    <td class="col-md-1"><b>Nº</b></td>
                                    <td class="col-md-2"><b>Nombre</b></td>
                                    <td class="col-md-1"><b>Fecha inscripción</b></td>
                                    <td class="col-md-3"><b>Fecha deseada de incorporación y horarios</b></td>
                                    <td class="col-md-2"><b>Observaciones</b></td>
                                    <td class="col-md-1"><a href='javascript: mail_todos()'><i class="fa fa-envelope" aria-hidden="true"></i></a> <a href='javascript: sms_todos()'><i class="fa fa-comment-o" aria-hidden="true"></i></a></td>   
                                    <td class="col-md-2">&nbsp;</td>
                            </tr>
                            <?php 
                            // Busca ordenes usuarios en lista de espera
                            $busca_lista_espera=db_query("SELECT * FROM lista_espera,usuarios WHERE lista_espera.id_usuario=usuarios.id_usuario AND lista_espera.dia_deseado_$dia_grupo=1 AND lista_espera.ini_horario_deseado_$dia_grupo<='".$datos_grupo["hora_inicio"]."' AND lista_espera.ini_horario_deseado_$dia_grupo<='".$datos_grupo["hora_fin"]."' AND lista_espera.fin_horario_deseado_$dia_grupo>='".$datos_grupo["hora_fin"]."' AND lista_espera.id_curso=$id_curso_grupo ORDER BY lista_espera.categoria,lista_espera.fecha_alta");
                            
                            $listado_en_lista=array();
                            
                            while($datos_lista_espera=db_fetch_array($busca_lista_espera)) {
                            
                                    $listado_en_lista[$datos_lista_espera["id_usuario"]]=1;
                            
                                    // comprueba si está en la lista
                                    
                                    $busca_en_lista=db_query_params('SELECT * FROM lista_espera_orden WHERE id_grupo=? AND id_usuario=?', 'ss', array($id_grupo, $datos_lista_espera["id_usuario"]));
                            
                                    if (db_num_rows($busca_en_lista)==0) {
                                    
                                            // Busca el ultimo en la lista
                                            $busca_en_lista=db_query_params('SELECT * FROM lista_espera_orden WHERE id_grupo=? ORDER BY orden DESC', 's', array($id_grupo));
                                    
                                            if (db_num_rows($busca_en_lista)==0) {
                                                    $nuevo_orden=1;                                          
                                                    } else {
                                                    $datos_orden=db_fetch_array($busca_en_lista);
                                                    $nuevo_orden=$datos_orden["orden"]+1;
                                                    }
                                    
                                            // Introduce en lista
                                            $intro_lista=db_query_params('INSERT INTO lista_espera_orden (id_grupo,id_usuario,orden) VALUES (?,?,?);', 'sss', array((string)($id_grupo), (string)($datos_lista_espera["id_usuario"]), (string)($nuevo_orden)));
                                    
                                            }
                                    
                            
                                    }
                            
                            // Limpia lista de espera
                            $busca_por_orden=db_query_params('SELECT * FROM lista_espera_orden WHERE id_grupo=?', 's', array($id_grupo));
                            
                            while($por_orden=db_fetch_array($busca_por_orden)) {
                            
                                    // Comprueba si está
                                    if ($listado_en_lista[$por_orden["id_usuario"]]!=1) {
                                            $borra_de_orden=db_query_params('DELET FROM lista_espera_orden WHERE id=?', 's', array($por_orden["id"]));
                                            }
                            
                                    }
                            
                            // Busca listas de espera
                            
                $filtro_nivel_natacion="";
                            
				if (strlen($filtro_nivel)!=0) {
				
					$filtro_nivel_natacion=" AND lista_espera.nivel_natacion=$filtro_nivel";
					
				}                            
                            
                $busca_lista_espera=db_query("SELECT * FROM lista_espera,usuarios,lista_espera_orden WHERE lista_espera.id_usuario=usuarios.id_usuario AND lista_espera.id_usuario=lista_espera_orden.id_usuario AND lista_espera.dia_deseado_$dia_grupo=1 AND lista_espera.ini_horario_deseado_$dia_grupo<='".$datos_grupo["hora_inicio"]."' AND lista_espera.ini_horario_deseado_$dia_grupo<='".$datos_grupo["hora_fin"]."' AND lista_espera.fin_horario_deseado_$dia_grupo>='".$datos_grupo["hora_fin"]."'$filtro_nivel_natacion AND lista_espera_orden.id_grupo=$id_grupo AND lista_espera.id_curso=$id_curso_grupo ORDER BY lista_espera.categoria,lista_espera_orden.orden, lista_espera.fecha_alta");
                        
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
        $texto_edad="";
        if ($datos_lista_espera["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($datos_lista_espera["fecha_nacimiento"]));

        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($datos_lista_espera["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }

        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_lista_espera["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_lista_espera["fecha_nacimiento"];
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
                $fecha_nac_alumno=date("d/m/Y",strtotime($datos_lista_espera["fecha_nacimiento"]));
                
                $texto_edad="<br />$fecha_nac_alumno<br />($edad_alumno)";
                }
                               
				$busca_notas_seguimiento=db_query_params('SELECT * FROM alumnos_seguimiento WHERE id_usuario=? ORDER BY fecha_creacion', 's', array($datos_lista_espera["id_usuario"]));
  	
  				$lista_eventos="<br />";
  				while($nota=db_fetch_array($busca_notas_seguimiento)) {
  					$lista_eventos.=date("d/m/Y",strtotime($nota["fecha_creacion"])).": ". $nota["comentarios"]."<br />";
  					}                

				// Comprueba si es alumno de otro grupo
                $id_usuario_lista=$datos_lista_espera["id_usuario"];
                
                $busca_si_grupo=db_query_params('SELECT id_grupo FROM aulas_alumnos_grupos,alumnos WHERE aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND alumnos.id_usuario=? AND aulas_alumnos_grupos.id_curso=? AND alumnos.tipo_baja=0 AND alumnos.fecha_baja>=?', 'sss', array($id_usuario_lista, $datos_lista_espera["id_curso"], (string)(date("Y-m-d"))));

				$en_otro_grupo="";
                if (db_num_rows($busca_si_grupo)!=0) {
                                $en_otro_grupo=' <span class="label label-danger">En otro grupo</span>';
                                }
                                
                $lista_espera_grupo=" class='fondo_espera".$datos_lista_espera["edad_espera"]."'";

            	switch ($datos_lista_espera["categoria"]) {
            
            		case 0:
            			$en_otro_grupo.=' <span class="label label-success">Nuevo/a</span>';
            		break;
            		
            		case 1:
            			$en_otro_grupo.=' <span class="label label-info">Ampliación</span>';
            		break;
            	
            		case 2:
            			$en_otro_grupo.=' <span class="label label-warning">Cambio</span>';
            		break;
            	            	            
            	}

                        ?>
                        <tr<?=$estilo_fila;?>>
                                <td bgcolor="<?=$colores_subniveles[$datos_lista_espera["nivel_natacion"]];?>"><?=$formato_fila1.$datos_lista_espera["orden"].$formato_fila2;?></td>
                                <td><a href='javascript:muestra_detalles(<?=$datos_lista_espera["id_usuario"]?>)'><?=$formato_fila1.$datos_lista_espera["Nombre"]." ".$datos_lista_espera["Apellido1"]." ".$datos_lista_espera["Apellido2"].$texto_edad.$formato_fila2;?></a><?=$en_otro_grupo?></td>
                                <td><?=$formato_fila1.date("d/m/Y",strtotime($datos_lista_espera["fecha_alta"])).$formato_fila2?></td>
                                <td<?=$lista_espera_grupo;?>><?=$formato_fila1.$fecha_deseada?><?php 
                                
                                for ($c_dia=1; $c_dia<=7; $c_dia=$c_dia+1) {
                
                                                        $dia_actual=$datos_lista_espera["dia_deseado_".$c_dia];
                        
                                                        if ($dia_actual==1) {
                                
                                                                $hora_inicio_actual=date("H:i",strtotime($datos_lista_espera["ini_horario_deseado_".$c_dia]));
                                                                $hora_fin_actual=date("H:i",strtotime($datos_lista_espera["fin_horario_deseado_".$c_dia]));
                                
                                                                echo($dia_sem_largo[$c_dia]." de ".$hora_inicio_actual." a ".$hora_fin_actual." hrs.<br>");
                                                                                                                        
                                                                }
                                
                                                        }
                                
                                ?><?=$formato_fila2?></td>
                                <td><?=$formato_fila1.$datos_lista_espera["observaciones_lista_espera"].$lista_eventos.$formato_fila2;?></td>
                                <td><input type="checkbox" name="email[]" id="email<?=$datos_lista_espera["id_usuario"]?>" value="<?=$datos_lista_espera["id_usuario"]?>"> <input type="checkbox" name="sms[]" id="sms<?=$datos_lista_espera["id_usuario"]?>" value="<?=$datos_lista_espera["id_usuario"]?>"></td>   
                                <td class="text-right"><?php 
                $estado_boton_su="";
                if ($datos_lista_espera["orden"]==1) {
                        $estado_boton_su="disabled ";
                        }
                ?>
                <a title="Colocar el primero de la lista" <?=$estado_boton_su?>class="btn btn-primary btn-xs" href="subir_primero.php?id_lista=<?=$datos_lista_espera["id"];?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>"><i class="fa fa-angle-double-up "></i></a> <a title="Subir un puesto en la lista" <?=$estado_boton_su?>class="btn btn-primary btn-xs" href="subir_nivel.php?id_lista=<?=$datos_lista_espera["id"];?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>"><i class="fa fa-sort-asc"></i></a>
                <?php 
                $estado_boton_ba="";
                if ($datos_lista_espera["orden"]==$total_en_lista) {
                        $estado_boton_ba="disabled ";
                        }
                ?>
                <a title="Bajar un puesto en la lista" <?=$estado_boton_ba?>class="btn btn-primary btn-xs" href="bajar_nivel.php?id_lista=<?=$datos_lista_espera["id"];?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>"><i class="fa fa-sort-desc"></i></a> <a title="Colocar el último de la lista" <?=$estado_boton_ba?>class="btn btn-primary btn-xs" href="bajar_ultimo.php?id_lista=<?=$datos_lista_espera["id"];?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>"><i class="fa fa-angle-double-down "></i></a>
                <br /><button title="Editar lista de espera" class="btn btn-success btn-xs" onclick="window.open('/lista_espera/editar_n_lista.php?id_n=<?=$datos_lista_espera["id_usu_lista"]?>','editar_lista','toolbars=no,scrollbars=yes,width=1024,height=768');"><i class="fa fa-pencil-square-o"></i></button>
                <a href="cambia_no_marcados.php?id_lista=<?=$datos_lista_espera["id"];?>&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>&nombre_mostrar=<?=$nombre_mostrar?>" class="btn btn-<?=$boton_borrado_color?> btn-xs" title="<?=$boton_borrado_mensaje?>"><i class="fa fa-thumbs-<?=$boton_borrado_imagen?>"></i></a>
                </td>
                        </tr>
                        <?php 
                        }
                            ?>
                            </table>
                            </div>
                    </div>    
    </div>

</div>
</form>
<script>

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" }
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function comprueba_tipo(objeto,valor_positivo,capa_mostrada) {

        var tipo_actual =  document.getElementById(objeto).value;

        if (tipo_actual==valor_positivo) {
                $(capa_mostrada).show();
                } else {
                $(capa_mostrada).hide();
                }

        }

function muestra_detalles(id_usuario) {
        
        $('#alumnoModal').modal('show');
        $('#detalles_alumno').attr('src', 'detalles_alumno.php?id_usuario='+id_usuario);
        
        }

$(document).on('hidden.bs.modal','#alumnoModal', function () {
	self.history.go(0);	
});

</script>

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

</body>
</html>
