<?php 
$self = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] ; //Obtenemos la página en la que nos encontramos
// header("refresh:30; url=$self"); //Refrescamos cada 30 segundos

require("../configuracion/parte_superior_docente_ancho.php"); ?>
<?php 

$id_aula=$_GET["id_aula"];
$turno=$_GET["turno"];

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

$morisidades[0]="<font color=green>No tiene pagos atrasados.</font>";
$morisidades[1]="<font color=red>Leve</font>";
$morisidades[2]="<font color=red>Grave</font>";
$morisidades[3]="<font color=red>Muy Grave</font>";
$morisidades[4]="<font color=red>Expulsión</font>";

// busca curso actual
// Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $nombre_curso_actual=$datos_curso_actual["nombre"];
                $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        $nombre_curso_actual=$datos_curso_actual["nombre"];
                        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$curso_seleccionado;
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        }
        
$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
        
if ($turno=="1") {
        $nombre_turno="Tarde";
        } else {
        $nombre_turno="Mañana";
        $turno="0";
        }

$estado_botones="";
if (date("d-m-Y",$fecha_time_stamp)!=date("d-m-Y")) {
         if ($nivel_acceso!=4) {
                $estado_botones=" disabled";
                }
        }

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);

if ($diasemana==0) {
        $diasemana=7;
        }

$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

?>
<script language="javascript">

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

var pagina_destino="";

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);
        
    }
}

function detalles_asistencia(id_alumno) {
        window.open("/gestion/centros/detalles_asistencia.php?fecha=<?=$fecha_time_stamp;?>&id_alumno="+id_alumno,"ventana_detalle1","toolbar=no,width=750,height=550");
        }
        
function ver_ficha(codigo) {
        window.open('/gestion/grupos_pruebas/detalles_alumno.php?id_usuario='+codigo,'editar','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
        }
        
function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.listado_alumnos.cambio_aula"+id+".value");
                window.location.href='cambio_aula.php?na='+opcion_seleccionada+'&a='+id;
                }
        }
function editarservicios(fila) {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Editar servicios del alumno.</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/facturacion/editar_servicios.php?i='+fila); 
        
        // window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
        
function editar_registro(id_registro,tipo) {

                if (tipo==1) {
                                pagina_destino="/aulas/editar_registro_guarderias.php";
                            } else {
                                pagina_destino="/aulas/editar_registro.php";
                                }

        window.open(pagina_destino+'?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function editar_registro_c(id_registro) {
        window.open('editar_registro_c.php?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function registro_entrada_morosos(id_aula,id_alumno,fecha,id_registro,tipo,alerta) {


		if (alerta!='') {
			alert(alerta);
			}

        if (confirm("El alumno no ha pagado la inscripción. ¿Quieres fichar?")) {
                var Digital=new Date()
                var hours=Digital.getHours()
                var minutes=Digital.getMinutes()
                var seconds=Digital.getSeconds()
                // Método clásico
                // window.open('registro_entrada.php?turno=<?=$turno;?>&id_aula='+id_aula+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro'+id_alumno,'toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                
                if (tipo==1) {
                                pagina_destino="/aulas/registro_entrada_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_entrada_ajax.php";
                                }
                
                pagina_destino="/aulas/registro_entrada_ajax.php";
                sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
                }
        }
function registro_entrada(id_aula,id_alumno,fecha,id_registro,tipo,alerta) {

				if (alerta!='') {
					alert(alerta);
					}

                var Digital=new Date()
                var hours=Digital.getHours()
                var minutes=Digital.getMinutes()
                var seconds=Digital.getSeconds()
                document.getElementById("entrada"+id_alumno).disabled=true;
                                document.getElementById("salida_a_"+id_alumno).disabled=false;
                document.getElementById("entrada"+id_alumno).value=hours+":"+minutes+":"+seconds;
                // Método clásico
                // window.open('registro_entrada.php?turno=<?=$turno;?>&id_aula='+id_aula+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro'+id_alumno,'toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                
                
                if (tipo==1) {
                                pagina_destino="/aulas/registro_entrada_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_entrada_ajax.php";
                                }
                
                sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});

        }
function registro_salida_ventana(id_registro,tipo) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida"+id_registro).disabled=true;
        document.getElementById("salida"+id_registro).value=hours+":"+minutes+":"+seconds;
        
        if (tipo==1) {
                pagina_destino="registro_salida_guarderias.php";
            } else {
                pagina_destino="registro_salida.php";
                }
        
        window.open(pagina_destino+'?id_registro='+id_registro,'registro'+id_registro,'toolbar=no,resizable=no,width=450,height=400');
        }
 

function registro_salida_por_id_alu(id_aula,id_alumno,fecha,id_registro,tipo) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida_a_"+id_alumno).disabled=true;
        document.getElementById("salida_a_"+id_alumno).value=hours+":"+minutes+":"+seconds;
        
                if (tipo==1) {
                                pagina_destino="/aulas/registro_salida_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_salida_ajax.php";
                                }
        
        sendAndLoad({desc:'turno',dato:<?=$turno;?>},{desc:'id_aula',dato:id_aula},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        
        }
        
function registro_salida(id_registro,tipo) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida"+id_registro).disabled=true;
        document.getElementById("salida"+id_registro).value=hours+":"+minutes+":"+seconds;
        
                if (tipo==1) {
                                pagina_destino="/aulas/registro_salida_guarderias_ajax.php";
                            } else {
                                pagina_destino="/aulas/registro_salida_ajax.php";
                                }
        
        sendAndLoad({desc:'id_registro',dato:id_registro});
        // window.open(pagina_destino+'?id_registro='+id_registro,'registro'+id_registro,'toolbar=no,resizable=no,width=450,height=400');
        }

function marcar_falta(nombre_alumno,fecha,id_alumno,id_grupo,id_subgrupo) {

	if (confirm("Se marcará la no asistencia de "+nombre_alumno+" como 'Falta justificada y recuperable'.\n\nAl hacer esto podremos asignar el alumno a un grupo de recuperación.\n\n¿Estás seguro/a?")) {
	
	window.location.href='/gestion/grupos_pruebas/asigna_falta_recuperable.php?id_alumno='+id_alumno+'&fecha='+fecha+'&id_grupo='+id_grupo+'&id_subgrupo='+id_subgrupo;
	
	}


}

function editar(codigo) {
        window.open('/alumnos/editar_inscripcion.php?origen_formulario=1&i='+codigo,'editar','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
        }
        
function renovar(id_alumno) {
        window.open('/alumnos/renovar_servicios.php?origen_formulario=1&i='+id_alumno,'editar','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=600');
        } 
        
 
function muestra_facturas(id_alumno) {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Listado recibos del alumno.</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/facturacion/listado_facturas_alumno_modal.php?id_cliente='+id_alumno);        
        } 
                       
//-->
</script>
<div align="center"><center>
<form name="listado_alumnos" method="POST">
<input type="button" onclick="window.location.href='calendario_control.php'" value="Salir" style="font-size: 12px">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="95%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?=$dianombre?>, <?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?> - Curso: <?=$nombre_curso_actual?><br><br>Control de asistencia</b></small></td>
  </tr>
<?php 
// Incio datos de externos

// Curso centro externo

$busca_curso_actual_ext=db_query_params('SELECT * FROM gestionguarderias.cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual_ext)!=0) {
                $datos_curso_actual_ext=db_fetch_array($busca_curso_actual_ext);
                $id_curso_actual_ext=$datos_curso_actual["id_curso"];
                $nombre_curso_actual_ext=$datos_curso_actual["nombre"];
                }

// Nombres de centros y aulas externas

$nombres_centros=array();
$nombres_aulas=array();

$busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE piscina<>0 ORDER BY id_centro");

while($lista_centros=db_fetch_array($busca_centros)) {

        $nombres_centros[$lista_centros["id_centro"]]=$lista_centros["nombre_centro"];

        $busca_aulas=db_query_params('SELECT * FROM gestionguarderias.centro_aulas WHERE id_curso=? AND id_centro=? AND tipo=0 ORDER BY edad_minima', 'ss', array($id_curso_actual_ext, $lista_centros["id_centro"]));

        while($lista_aulas=db_fetch_array($busca_aulas)) {
                $nombres_aulas[$lista_aulas["id_aula"]]=$lista_aulas["nombre"];
                }
        }

  // Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM aulas_grupos WHERE dia=$diasemana AND id_curso=$id_curso_actual_ext ORDER BY hora_inicio");

while($datos_aula=db_fetch_array($consulta_datos_aula)) {

$encabezado_mostrado=true;

$busca_alumnos=db_query_params('SELECT *, gestionguarderias.usuarios.id_usuario AS id_usuario_inscripcion, gestionguarderias.alumnos.id_centro AS id_centro_alumno FROM gestionguarderias.usuarios,gestionguarderias.alumnos, aulas_alumnos_lapices_grupos WHERE gestionguarderias.usuarios.id_usuario=gestionguarderias.alumnos.id_usuario AND aulas_alumnos_lapices_grupos.id_alumno=gestionguarderias.alumnos.id_alumno AND aulas_alumnos_lapices_grupos.id_grupo=? AND gestionguarderias.alumnos.fecha_matricula<=? AND gestionguarderias.alumnos.fecha_baja>=? ORDER BY gestionguarderias.alumnos.nivel_actual,gestionguarderias.usuarios.Apellido1, gestionguarderias.usuarios.Apellido2, gestionguarderias.usuarios.Nombre', 'sss', array($datos_aula["id_grupo"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {

$encabezado_mostrado=true;

?>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="right"><b>Grupo: <?=date("H:i",strtotime($datos_aula["hora_inicio"]))?> - <?=date("H:i",strtotime($datos_aula["hora_fin"]))?></b></td>
  </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td width="60">&nbsp;</td>
        <td width="177"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td width="120"><font size=1 face="<?= $fuente_general; ?>"><b>Centro</b></font></td>
        <td align="center" width="145"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center" width="104"><font size=1 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center" width="104"><font size=1 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>
        <td align="center" width="104"><font size=1 face="<?= $fuente_general; ?>"><b>Falta</b></font></td>        
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Información</b></font></td>
        <td align="center" width="60"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php             
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
        $id_aula=$listado_alumnos["aula"];
        
        // Color nivel
        switch ($listado_alumnos["nivel_actual"]) {
                case 0:
                        $color_fila="#efefef";
                        break;
                case 1:
                        $color_fila="#9ed09c";
                        break;
                case 2:
                        $color_fila="#9cbdd0";
                        break;
                case 3:
                        $color_fila="#d0af9c";
                        break;
                case 4:
                        $color_fila="#9cbdd0";
                        break;                        
                }

		$edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));       
        
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($listado_alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
                
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
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
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                
        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#acacac";
                } else {
                $color_fuente_linea="#000000";
                }
        $link_javascript="registro_entrada";
        if ($fila_par_impar==1) {
                $fila_par_impar=-1;
                } 
         
        $alerta="";               
        if (strlen($listado_alumnos["observaciones"])!=0) {
        
        	$texto_alerta=str_replace("'", "", $listado_alumnos["observaciones"]);  
        	$texto_alerta=nl2br($texto_alerta);      	
        	$texto_alerta=str_replace(chr(10), "", $texto_alerta);  
        	$texto_alerta=str_replace(chr(13), "", $texto_alerta);        	
        	$texto_alerta=str_replace("<br />", "\\n", $texto_alerta);	 	
        
	        $alerta="¡ATENCIÓN! \\n\\n".$texto_alerta;
        }
                
        $fila_par_impar++;   
        ?>
                <tr bgcolor="<?=$color_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fila;?>')">
        <td>&nbsp;</td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><?=$nombres_centros[$listado_alumnos["id_centro_alumno"]]." - ".
$nombres_aulas[$listado_alumnos["aula"]]?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?><br /><?=$edad_alumno?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">        
                <?php 
        
                // Busca si ya está registrado
                $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos_guarderias WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND turno=$turno AND id_servicio=$id_aula AND fecha='".date("Y/m/d",$fecha_time_stamp)."'") or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                ?>
                <input<?=$estado_botones;?> type="button" name="entrada<?=$listado_alumnos["id_usuario"]?>" id="entrada<?=$listado_alumnos["id_usuario"]?>" value="Registrar entrada" onclick="<?=$link_javascript?>(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,1,'<?=$alerta?>')"<?=$color_boton;?>></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" name="salida_a_<?=$listado_alumnos["id_usuario"]?>" id="salida_a_<?=$listado_alumnos["id_usuario"]?>" value="Registrar salida" disabled onclick="registro_salida_por_id_alu(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,1)"></td>
                <?php 
                } else {
                $datos_registro=db_fetch_array($busca_registro_entrada);
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,1);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                </td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                <?php 
                if ($datos_registro["hora_salida"]=="") {
                ?>
                <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>,1)">
                <?php 
                } else {
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,1);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                <?php 
                }
                ?>
                </font></td>
                <?php 
                }
                ?></font></td>
                <td align="center" colspan=3>&nbsp;</td>
                </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
<?php 
        }

// Fin usuarios externos

// Inicio usuarios propios

$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos, aulas_alumnos_grupos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND ((alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_grupo=? AND alumnos.id_centro=? AND fecha_matricula<=? AND fecha_baja>=?) OR (alumnos.tipo_baja<>0 AND aulas_alumnos_grupos.id_grupo=? AND alumnos.id_centro=? AND fecha_matricula<=? AND fecha_baja>=?)) ORDER BY alumnos.nivel_actual,Apellido1, Apellido2, Nombre', 'ssssssss', array($datos_aula["id_grupo"], $centro_actual, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp - $tiempo_gracia)), $datos_aula["id_grupo"], $centro_actual, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {

if ($encabezado_mostrado==true) {

?>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="right"><b>Grupo: <?=date("H:i",strtotime($datos_aula["hora_inicio"]))?> - <?=date("H:i",strtotime($datos_aula["hora_fin"]))?></b></td>
  </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td width="60">&nbsp;</td>
        <td width="177"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td width="120"><font size=1 face="<?= $fuente_general; ?>"><b>Centro</b></font></td>
        <td align="center" width="145"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center" width="104"><font size=1 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center" width="104"><font size=1 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>
        <td align="center" width="104"><font size=1 face="<?= $fuente_general; ?>"><b>Falta</b></font></td>        
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Información</b></font></td>
        <td align="center" width="60"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        } else {
        ?>
        <tr bgcolor="#FFFFFF">
        <td align="center">
                        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <?php 
        } // Fin Encabezado
        
$total_absoluto=0;
$total_cobrado=0;
$total_subvenciones=0;
$total_cobrado_subvenciones=0;
$aula_actual=0;
$curso_actual=0;

$total_subvenciones_proveedor=array();
$total_subvenciones_pagadas_proveedor=array();
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

$listado_morosidades="";
$total_facturado_alumno=0;
$total_abs_facturado_alumno=0;
$total_abs_cobrado_alumno=0;
$total_abs_abonos_alumno=0;
$nivel_maximo_morosidad=0;
$texto_morosidad="";
        
// Comprueba deudas y pagos
        
$consulta1=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_cliente=?', 's', array($listado_alumnos["id_alumno"]));
$consulta_albaranes_cliente = db_query_params($consulta1[0], $consulta1[1], $consulta1[2]) or die (db_error());

while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){

// Mira si los albaranes de la factura y calcula los totales
$id_factura=$datos_factura["id_factura"];
// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;

$total_factura_alumno=0;
$total_cobrado_alumno=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
                
                                if (substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") {
                                                        $proveedor_actual=141;
                                                        }                               
                if ((substr($datos_albaranes["concepto"],0,14)=="Cheque Escolar") or (substr($datos_albaranes["concepto"],0,15)=="Beca de Comedor")) {
                                                        $proveedor_actual=120;                                                        
                                                        } 
         
        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $total_subvenciones_proveedor[$proveedor_actual]=$total_subvenciones_proveedor[$proveedor_actual]+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $total_subvenciones_pagadas_proveedor[$proveedor_actual]=$total_subvenciones_pagadas_proveedor[$proveedor_actual]+$total_abs;
                }
        }
        $total_facturado_alumno=$total_base+$total_iva+$total_recargo;
        // Comprueba pagos a cuenta
        $total_a_cuenta=0;

        $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));
        
        if ((db_num_rows($busca_pagos)==0) and (($total_base+$total_iva+$total_recargo)<0)) {
           $total_abs_abonos_alumno=$total_abs_abonos_alumno+round(($total_base+$total_iva+$total_recargo),2);
        }
        
        while ($pagos=db_fetch_array($busca_pagos)) {
                $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                }
                
       $total_a_cuenta=round($total_a_cuenta,2);
       $total_factura_sel=round(($total_base+$total_iva+$total_recargo),2);

        if ($total_a_cuenta<$total_factura_sel) {
                $morosidad=true;
                } else {
                $morosidad=false;
                }

        $tiempo_desde_emision=(strtotime(date("Y-m-d"))-strtotime($datos_factura["fecha_emision"]))/86400;

        $morosidad_actual=0;

                if ($datos_factura["forma_de_pago"]==0) {
                        $forma_de_pago="Domiciliación bancaria<br>Cuenta: ".$datos_factura["banco_cuenta"];
                        if (($tiempo_desde_emision<=15) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>15) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>30) {
                                $morosidad_actual=4;
                                }
                        } else {
                        $forma_de_pago="Contado";
                        if (($tiempo_desde_emision<=5) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>5) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>10) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=4;
                                }
                        }

        if ($morosidad==true) {
                if ($morosidad_actual>=$nivel_maximo_morosidad) {
                        $listado_morosidades=" F:".$id_factura." - C:".$total_a_cuenta." - T:".($total_base+$total_iva+$total_recargo);
                        $nivel_maximo_morosidad=$morosidad_actual;
                        }
                }

                $total_cobrado=$total_cobrado+round($total_a_cuenta,2);
                $total_absoluto=$total_absoluto+round(($total_base+$total_iva+$total_recargo),2);

                $total_factura_alumno=$total_factura_alumno+round($total_a_cuenta,2);
                $total_cobrado_alumno=$total_cobrado_alumno+round(($total_base+$total_iva+$total_recargo),2);;

                                $total_abs_facturado_alumno=$total_abs_facturado_alumno+round(($total_base+$total_iva+$total_recargo),2);
                                $total_abs_cobrado_alumno=$total_abs_cobrado_alumno+round($total_a_cuenta,2);

                $contador=$contador+1;
        }
        
        
        // Fin facturas
        // $texto_morosidad=$morisidades[$nivel_maximo_morosidad];
        $texto_baja="";
        if (strtotime($listado_alumnos["fecha_baja"])==strtotime(date("Y-m-d",$fecha_time_stamp))) {
                $texto_baja="<font color=red>Finaliza el día ".date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]))." debe <a href='javascript:renovar(".$listado_alumnos["id_alumno"].")'>renovar los servicios</a></font><br />";
                }

        if (strtotime($listado_alumnos["fecha_baja"])<strtotime(date("Y-m-d",$fecha_time_stamp))) {
                $texto_baja="<font color=red>Finalizó el día ".date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]))." debe <a href='javascript:renovar(".$listado_alumnos["id_alumno"].")'>renovar los servicios</a></font><br />";
                }
        /*
        if ($total_factura_alumno<$total_cobrado_alumno) {
                
                $texto_morosidad=$texto_baja."<font color=red>Pendiente de cobro: ".number_format(($total_factura_alumno-$total_cobrado_alumno)*-1,2,",",".")." €</font>"; 
                } else {
                if ($texto_baja=="") {
                        $texto_morosidad=$morisidades[0];
                        } else {
                        $texto_morosidad=$texto_baja;
                        }
                } 
        */
        $texto_morosidad=$texto_baja;        
            
        $id_aula=$listado_alumnos["aula"];
        
        // Color nivel        
        switch ($listado_alumnos["nivel_actual"]) {
                case 0:
                        $color_fila="#efefef";
                        break;
                case 1:
                        $color_fila="#9ed09c";
                        break;
                case 2:
                        $color_fila="#9cbdd0";
                        break;
                case 3:
                        $color_fila="#d0af9c";
                        break;
                case 4:
                        $color_fila="#9ed09c";
                        break;       
                }        
        
        $total_factura=0;
        $total_a_cuenta=0;
        // Comprueba si se le han incluido los servicios
        $id_inscripcion=$listado_alumnos["id_alumno"];
        $id_usuario_inscripcion=$listado_alumnos["id_usuario_inscripcion"];
        $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.subcat=1', 's', array($id_inscripcion)) or die(db_error());
        if (db_num_rows($busca_inscripcion)==0) {
                $no_inscripcion=true;                           
                } else {
                $no_inscripcion=false;
                $inscripcion_pagada=true;
                while($datos_inscripcion_alumno=db_fetch_array($busca_inscripcion)) {
                        $id_servicio_contratado=$datos_inscripcion_alumno["id_servicio_contratado"];
                        // Busca factura donde se ha incluido el servicio
                        $busca_factura=db_query_params('SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.articulo=?', 'ss', array($id_inscripcion, $id_servicio_contratado)) or die(db_error());
                        if (db_num_rows($busca_factura)!=0) {
                                $datos_factura_emitida=db_fetch_array($busca_factura);
                                $id_factura_emitida=$datos_factura_emitida["id_factura"];
                                // Calcula el total y los pagos a cuenta y comprueba si esta pagada
                                
// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura_emitida)) or die (db_error());

$total_a_cuenta=0;
$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;
$total_factura=0;

$importe_subvenciones_factura=0;
$importe_subvenciones_cobradas_factura=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }

        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $importe_subvenciones_factura=$importe_subvenciones_factura+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $importe_subvenciones_cobradas_factura=$importe_subvenciones_cobradas_factura+$total_abs;
                }
        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura_emitida));
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
if (($total_a_cuenta-$total_factura)!=0) {
        $inscripcion_pagada=false;
        } else {
        $inscripcion_pagada=true;
        }
                                } else {
                                $inscripcion_pagada=false;
                                }
                }
        }
        
if ($no_inscripcion==true) {
        // Busca la inscripción en los servicios de aula
        $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=? AND tarifa.subcat=1', 's', array($id_aula)) or die(db_error());

        if (db_num_rows($busca_inscripcion)==0) {
                $no_inscripcion=true;
                } else {
                $no_inscripcion=false;
                $inscripcion_pagada=true;
                while($datos_inscripcion_alumno=db_fetch_array($busca_inscripcion)) {
                
                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_inscripcion_alumno["id_servicio_contratado"], $id_inscripcion));
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_inscripcion=false;
                                } else {
                        $id_servicio_contratado=$datos_inscripcion_alumno["id_servicio_contratado"];
                        // Busca factura donde se ha incluido el servicio
                        $busca_factura=db_query_params('SELECT * FROM facturas_emitidas,detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.cod_facturacion=?', 'ss', array($id_inscripcion, 'au_' . (string)($id_servicio_contratado))) or die(db_error());
                        if (db_num_rows($busca_factura)!=0) {
                                $datos_factura_emitida=db_fetch_array($busca_factura);
                                $id_factura_emitida=$datos_factura_emitida["id_factura"];
                                // Calcula el total y los pagos a cuenta y comprueba si esta pagada

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura_emitida)) or die (db_error());

$total_a_cuenta=0;
$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;
$total_factura=0;

$importe_subvenciones_factura=0;
$importe_subvenciones_cobradas_factura=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }

        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $importe_subvenciones_factura=$importe_subvenciones_factura+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $importe_subvenciones_cobradas_factura=$importe_subvenciones_cobradas_factura+$total_abs;
                }
        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura_emitida));
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }
if (($total_a_cuenta-$total_factura)!=0) {
        $inscripcion_pagada=false;
        } else {
        $inscripcion_pagada=true;
        }
                                } else {
                                $inscripcion_pagada=false;
                                }
                        }
                }
        }
        // Fin busca en aula
        }

        $cuotas_impagadas=false;
        $id_servicio_impagado=0;
        
        $busca_cuotas=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=?', 's', array($id_inscripcion)) or die(db_error());
        if (db_num_rows($busca_cuotas)==0) {
                $no_cuotas=true;
                $texto_morosidad.="<font color=red>No tiene servicios contratados</font>";
                } else {
                $no_cuotas=false;
                
                while($cuotas=db_fetch_array($busca_cuotas)) {
                
                        // Busca facturas de cuotas
                        $busca_factura=db_query_params('SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.articulo=? ORDER BY facturas_emitidas.fecha_emision DESC', 'ss', array($id_inscripcion, $cuotas["id_servicio_contratado"])) or die(db_error());
                        if (db_num_rows($busca_factura)==0) {
                                $cuotas_impagadas=true;
                                $id_servicio_impagado=$cuotas["id_servicio"];
                                }
                        }
                
                }

        if ($datos_aula["comprobar_inscripcion"]==0) {
                $no_inscripcion=false;
                $inscripcion_pagada=true;
                }

        if ($datos_aula["comprobar_cuotas"]==0) {
                $no_cuotas=false;
                }

        if ($inscripcion_pagada==false) {
                $color_boton=" style='color: #FF0000'";
                $link_javascript="registro_entrada_morosos";
                } else {
                $color_boton=" style='color: #000000'";
                $link_javascript="registro_entrada";
                }

        if ($no_cuotas==true) {
                $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=?', 's', array($id_aula)) or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_inscripcion["id_servicio_contratado"], $id_inscripcion));
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_cuotas=false;
                                }
                        }
                }

        if ($cuotas_impagadas==true) {
                $texto_morosidad.="<font color='#ff0000'><br>Tiene servicios no facturados<br><a href='/gestion/material/control_asistencia_actividad.php?id_actividad=".$id_servicio_impagado."#alumno".$listado_alumnos["id_alumno"]."' target='_blank'>Ver servicios</a></font><br />";
                }

        		$edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));       
        
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($listado_alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
                
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
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
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                
        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#858585";
                } else {
                $color_fuente_linea="#000000";
                }
                             
        if ($fila_par_impar==1) {
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;  
        
        
        if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
                $enlace_fila_actual=" href='javascript:editarservicios(".$listado_alumnos["id_alumno"].");'";
                } else {
                $enlace_fila_actual="";
                }
  
        if (($total_abs_facturado_alumno>$total_abs_cobrado_alumno) and (($total_abs_facturado_alumno-$total_abs_cobrado_alumno)!=0)) {
        
                $texto_morosidad.="<font color=red>Pendiente de cobro: ".number_format(($total_abs_facturado_alumno-$total_abs_cobrado_alumno),2,",",".")." €</font>"; 
        
        } else {
        
                $texto_morosidad.=$morisidades[0];
        
        }       

        if ($total_abs_abonos_alumno!=0) {
        
                $texto_morosidad.="<br /><font color=blue>Pendiente de abonar: ".number_format($total_abs_abonos_alumno*-1,2,",",".")." €</font>";
        
                }
         
        if ($fecha_baja<date("Y-m-d",$fecha_time_stamp)) {
                $es_baja=true;
                $color_fuente_linea="#ff0000";
                }         
         
        if ($listado_alumnos["tipo_baja"]==2) {
                                $color_fila="#000";
                                $color_fuente_linea="#fff";        
                        } 
        $alerta="";               
        if (strlen($listado_alumnos["observaciones"])!=0) {
        
        	$texto_alerta=str_replace("'", "", $listado_alumnos["observaciones"]);  
        	$texto_alerta=nl2br($texto_alerta);      	
        	$texto_alerta=str_replace(chr(10), "", $texto_alerta);  
        	$texto_alerta=str_replace(chr(13), "", $texto_alerta);        	
        	$texto_alerta=str_replace("<br />", "\\n", $texto_alerta);	        	
	        $alerta="¡ATENCIÓN! \\n\\n".$texto_alerta;
        }                        
        
        $no_ha_venido=true;                      
         
        ?>
                <tr bgcolor="<?=$color_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fila;?>')">
        <td width=50><a class="btn btn-info btn-sm" href="javascript:ver_ficha(<?=$listado_alumnos["id_usuario"]?>);"><?=$listado_alumnos["id_usuario"]?> <i class="fa fa-info"></i></a></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><a name="alumno<?=$listado_alumnos["id_alumno"]?>"></a><a<?=$enlace_fila_actual?>><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$nombre_centro_actual?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?><br /><?=$edad_alumno?></font></td>
        <?php 
        if (($no_inscripcion==false) and ($no_cuotas==false)) {               
        
        $es_baja=false; // esto debe ir encima del IF
        
        if ($es_baja==true) {
        ?>
                <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
                <td align="center"><small><small><?=$texto_morosidad?></small></small></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:muestra_facturas(<?=$listado_alumnos["id_alumno"];?>);" title="Gestionar pagos de facturas"><img src="servicios.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_alumno"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
                </tr>
        <?php 
                } else {
        ?>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        
        // Comprueba si es media jornada o jornada completa
        if (($listado_alumnos["tipo_mensualidad"]==1) and ($turno==1)) {
        
        // Media Jornada y turno de tarde

                // Busca si ya está registrado por la mañana
                $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND turno=0 AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y/m/d", $fecha_time_stamp)))) or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                        // Busca si ya esta registrado por la tarde
                        $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND turno=1 AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y/m/d", $fecha_time_stamp)))) or die(db_error());
                        if (db_num_rows($busca_registro_entrada)==0) {
                        ?>
                        <input<?=$estado_botones;?> type="button" id="entrada<?=$listado_alumnos["id_usuario"]?>" name="entrada<?=$listado_alumnos["id_usuario"]?>" value="Registrar entrada" onclick="<?=$link_javascript?>(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,0,'<?=$alerta?>')"<?=$color_boton;?>></td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" name="salida_a_<?=$listado_alumnos["id_usuario"]?>" id="salida_a_<?=$listado_alumnos["id_usuario"]?>" value="Registrar salida" disabled onclick="registro_salida_por_id_alu(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,0)"></td>
                        <?php 
                        } else {
                        $no_ha_venido=false;
                        $datos_registro=db_fetch_array($busca_registro_entrada);
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                        </td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <?php 
                        if ($datos_registro["hora_salida"]=="") {
                        ?>
                        <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>,0)">
                        <?php 
                        } else {
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                        <?php 
                        }
                        ?>
                        </font></td>
                        <?php 
                        }
                } else {
                        $datos_registro=db_fetch_array($busca_registro_entrada);
                        $no_ha_venido=false;
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                        </td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <?php 
                        if ($datos_registro["hora_salida"]=="") {
                        ?>
                        <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>,0)">
                        <?php 
                        } else {
                        ?>
                        <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                        <?php 
                        }
                        ?>
                        </font></td>
                        <?php 
                }
                ?></font></td>
                <?php 
                if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
                ?>
                <td align="center"> - </td>
                <td align="center"><small><small><?=$texto_morosidad?></small></small></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:muestra_facturas(<?=$listado_alumnos["id_alumno"];?>);" title="Gestionar pagos de facturas"><img src="servicios.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_alumno"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
                <?php 
                } else {
                ?>
                <td colspan=3>&nbsp;</td>
                <?php 
                }
                ?>
                </tr>
                <?php 
        
        } else {
        
        // Jornada completa o turno de mañana
        
                // Busca si ya está registrado
                $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND turno=$turno AND id_servicio=$id_aula AND fecha='".date("Y/m/d",$fecha_time_stamp)."'") or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                ?>
                <input<?=$estado_botones;?> type="button" name="entrada<?=$listado_alumnos["id_usuario"]?>" id="entrada<?=$listado_alumnos["id_usuario"]?>" value="Registrar entrada" onclick="<?=$link_javascript?>(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,0,'<?=$alerta?>')"<?=$color_boton;?>></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" name="salida_a_<?=$listado_alumnos["id_usuario"]?>" id="salida_a_<?=$listado_alumnos["id_usuario"]?>" value="Registrar salida" disabled onclick="registro_salida_por_id_alu(<?=$id_aula?>,<?=$listado_alumnos["id_usuario"]?>,<?=$fecha_time_stamp?>,0,0)"></td>
                <?php 
                } else {
                $datos_registro=db_fetch_array($busca_registro_entrada);
                $no_ha_venido=false;
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></a></font>
                </td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                <?php 
                if ($datos_registro["hora_salida"]=="") {
                ?>
                <input<?=$estado_botones;?> type="button" name="salida<?=$datos_registro["id_servicio_consumido"]?>" id="salida<?=$datos_registro["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>,0)">
                <?php 
                } else {
                ?>
                <font size=3><a href="javascript:editar_registro(<?=$datos_registro["id_servicio_consumido"]?>,0);"><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></a></font>
                <?php 
                }
                ?>
                </font></td>
                <?php 
                }
                if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
                ?></font></td>
                <td align="center"><?php
                $estado_boton_falta = " value='Falta' style='font-size: 13px'";
                if ($no_ha_venido==true) {
	                // Comprueba si se ha marcado falta
	                $busca_falta=db_query_params('SELECT * FROM servicios_consumidos_faltas WHERE fecha=? AND id_alumno=? AND id_grupo_falta=? AND id_subgrupo_falta=?', 'ssss', array((string)(date("Y/m/d", $fecha_time_stamp)), $listado_alumnos["id_alumno"], $listado_alumnos["id_grupo"], $listado_alumnos["id_subgrupo"]));

	                if (db_num_rows($busca_falta)!=0) {
	                	$estado_boton_falta=" value='Recuperable' style='color: #ff0000; font-size: 13px' disabled='disabled'";
	                	}
	                
	            	} else {
	            		$estado_boton_falta=" value='Asiste' style='font-size: 13px' disabled='disabled'";
	            	}
                
                ?><input type="button"<?=$estado_boton_falta?> onclick="marcar_falta('<?=$listado_alumnos["Nombre"]?> <?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>',<?=$fecha_time_stamp?>,<?=$listado_alumnos["id_alumno"]?>,<?=$listado_alumnos["id_grupo"]?>,<?=$listado_alumnos["id_subgrupo"]?>);"></td>
                <td align="center"><small><small><?=$texto_morosidad?></small></small></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:muestra_facturas(<?=$listado_alumnos["id_alumno"];?>);" title="Gestionar pagos de facturas"><img src="servicios.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_alumno"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="/gestion/calendario/calendario_asistencia.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>" target="calendario_asistencia" title="Editar calendario docente del alumno"><img src="/gestion/centros/calendario.gif" border=0></a></font></td>
                <?php 
                } else {
                ?>
                <td colspan=3>&nbsp;</td>
                <?php 
                }
                ?>
                </tr>
                <?php 
                }
            }
            
            } else {
        $mensaje11="";
        if ($no_inscripcion==true) {
                $mensaje11=$mensaje11."Inscripción en el curso";
                }
        if ($no_cuotas==true) {
                if ($mensaje11!="") {
                        $mensaje11=$mensaje11." y ";
                        }
                $mensaje11=$mensaje11."Servicio de educación";
                }
        $mensaje11="Falta asigar al niño los servicios de: ".$mensaje11.".";
        
        if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
        ?>
        <td align="center"> - </td>
        <td colspan=2 align="center"><font color="red" size=1><?=$mensaje11?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$listado_alumnos["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a></td>
        <?php 
        } else {
        ?>
        <td colspan=3>&nbsp;</td>
        <?php 
        }
        ?>
        </tr>
        <?php 
        }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
        }
}
?>
</table>
</form>
</center></div>
</div>

<!-- Modal -->
<div class="modal modal-wide fade" id="grupoModal" tabindex="-1" role="dialog" aria-labelledby="grupoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_grupo"><h4 class="modal-title" id="grupoModalLabel">Título</h4></div>
      </div>    
      <div class="modal-body">
        <iframe src="" name="detalles" id="detalles" width=100% height=550 frameborder=0></iframe>
      </div>
    </div>
  </div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

