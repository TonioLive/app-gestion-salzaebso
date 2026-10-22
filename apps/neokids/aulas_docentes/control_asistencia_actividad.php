<?php  require("../configuracion/parte_superior_docente.php"); ?>
<?php 
$nombres_menus=array();

        $lista_menus=db_query("SELECT * FROM menu_menus ORDER BY nombre_menu");
        while($datos_menus=db_fetch_array($lista_menus)) {

        $nombres_menus[$datos_menus["id_menu"]]=urldecode($datos_menus["nombre_menu"]);
        
        }

$id_actividad=$_GET["id_actividad"];

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
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
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

// Busca datos de la actividad
$consulta_datos_actividad=db_query_params('SELECT * FROM tarifa WHERE ID=?', 's', array($id_actividad));
$datos_actividad=db_fetch_array($consulta_datos_actividad);

// Tipo actividad

$tipo_actividad=$datos_actividad["anula_comedor"];

?>
<SCRIPT LANGUAGE=javascript>
<!--

var pagina_destino="";

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

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

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
function servicios(codigo) {
        window.open('/facturacion/editar_servicios.php?i='+codigo,'editar','toolbar=no,resizable=yes,width=750,height=500');
        }

function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
function registro_entrada(id_actividad,id_alumno,fecha) {
                var Digital=new Date()
                var hours=Digital.getHours()
                var minutes=Digital.getMinutes()
                var seconds=Digital.getSeconds()
                eval("document.listado_alumnos.botonIn"+id_alumno+".disabled=true");
                eval("document.listado_alumnos.botonIn"+id_alumno+".value='"+hours+":"+minutes+":"+seconds+"'");
                // Método clásico
                // window.open('registro_entrada_actividad.php?id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                pagina_destino="registro_entrada_actividad_ajax.php";
                sendAndLoad({desc:'id_actividad',dato:id_actividad},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        }
function registro_salida(id_registro) {
        window.open('registro_salida_actividad.php?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function reservar_plaza(id_actividad,id_alumno,fecha) {
                eval("document.listado_alumnos.botonRe"+id_alumno+".disabled=true");
                eval("document.listado_alumnos.botonRe"+id_alumno+".value='Anular'");
                // Método clásico
                // window.open('reserva_actividad.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                pagina_destino="reserva_actividad_ajax.php";
                sendAndLoad({desc:'inicio',dato:<?=$fecha_time_stamp;?>},{desc:'id_actividad',dato:id_actividad},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        }
function anular_reserva(id_actividad,id_alumno,fecha) {
                eval("document.listado_alumnos.botonRe"+id_alumno+".disabled=true");
                eval("document.listado_alumnos.botonRe"+id_alumno+".value='Anular'");
                // Método clásico
                // window.open('anular_reserva_actividad.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                pagina_destino="anular_reserva_actividad_ajax.php";
                sendAndLoad({desc:'inicio',dato:<?=$fecha_time_stamp;?>},{desc:'id_actividad',dato:id_actividad},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        }
        
function renuncia_plaza(id_actividad,id_alumno,fecha) {
                eval("document.listado_alumnos.botonNo"+id_alumno+".disabled=true");
                eval("document.listado_alumnos.botonNo"+id_alumno+".value='Anular'");
                // Método clásico
                // window.open('renuncia_plaza.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                pagina_destino="renuncia_plaza_ajax.php";
                sendAndLoad({desc:'inicio',dato:<?=$fecha_time_stamp;?>},{desc:'id_actividad',dato:id_actividad},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        }
function anular_renuncia_plaza(id_actividad,id_alumno,fecha) {
                eval("document.listado_alumnos.botonNo"+id_alumno+".disabled=true");
                eval("document.listado_alumnos.botonNo"+id_alumno+".value='No asiste'");
                // Método clásico
                // window.open('anular_renuncia_plaza.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                // Usando AJAX
                pagina_destino="anular_renuncia_plaza_ajax.php";
                sendAndLoad({desc:'inicio',dato:<?=$fecha_time_stamp;?>},{desc:'id_actividad',dato:id_actividad},{desc:'id_alumno',dato:id_alumno},{desc:'fecha',dato:fecha},{desc:'registro',dato:id_alumno});
        }
        
function cobrar_plaza(id_actividad,id_alumno,fecha) {
        window.open('cobro_actividad.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=yes,width=450,height=400');
        }
function facturar_plaza(id_actividad,id_alumno,fecha) {
        window.open('factura_actividad.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=yes,width=450,height=400');
        }
function anular_cobro(id_actividad,id_alumno,fecha) {
        if (confirm("¿Deseas eliminar el cobro de esta activiad?\n\nAl hacerlo se borrará el recibo y la factura emitida.")) {
                window.open('anular_cobro_actividad.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                }
        }
function anular_asistencia(id_actividad,id_alumno,fecha) {
        if (confirm("¿Deseas eliminar el registro de asistencia de esta activiad?")) {
                window.open('anular_asistencia_actividad.php?inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                }
        }
function ver_recibo(id_recibo) {
        wver_recibo=window.open('about:blank','registro','toolbar=no,resizable=yes,width=450,height=400');
        wver_recibo.window.location.href='/pdf/recibos_asistencia.php?id_asistencia='+id_recibo;
        }
//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br><br>Control de Asistencia Actividad<br><br><big><big><i><?=$datos_actividad["ARTICULO"];?></i></big></big><br><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b><br><br><b>* NOTA:</b> Los alumnos que han contratado previamente el servicio aparecen resaltados con el fondo verde</small></td>
  </tr>
<?php 
$busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? ORDER BY alumnos.aula, Apellido1, Apellido2, Nombre', 'sss', array($centro_actual, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=8><font size=1 face="<?= $fuente_general; ?>">Para ver la ficha de un alumno, haz clic sobre su nombre o sobre el botón ( <img src="editar.gif" align="absmiddle">)</font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        $aula_actual=0;
        $cuenta_asistencia=0;
        $cuenta_aula=0;
        $asistencia_prevista_aula=0;
        $asistencia_prevista=0;
        $reservas=0;
        $reservas_aula=0;
        $pagados_aula=0;
        $pagados=0;
        $total_comedor_normal=0;
        $total_reservas_comedor_normal=0;
        $total_reservas__aula_comedor_normal=0;
        
        $totales_por_menu=array();

        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        if ($aula_actual!=$listado_alumnos["aula"]) {
        if ($aula_actual!=0) {
        ?>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Asistencia por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$cuenta_aula;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$pagados_aula;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Reservas por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$reservas_aula;?><?php  if ($tipo_actividad==1) { ?><font size=1><br>(Habituales: <?=$total_reservas__aula_comedor_normal;?>/Extras:<?=$reservas_aula-$total_reservas__aula_comedor_normal?>)</font><?php  } ?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        }
        $aula_actual=$listado_alumnos["aula"];
        // Busca datos del aula
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$aula_actual");
        $datos_aula=db_fetch_array($consulta_datos_aula);
        ?>
        <tr bgcolor="#efefef">
        <td colspan=8><font size=3 face="<?= $fuente_general; ?>"><b><?=$datos_aula["nombre"];?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Reserva</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>No asiste</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Cobro</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        $cuenta_aula=0;
        $asistencia_prevista_aula=0;
        $reservas_aula=0;
        $pagados_aula=0;
        $total_reservas__aula_comedor_normal=0;
        }
        $consulta_datos_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($listado_alumnos["id_alumno"])) or die(db_error());
        $datos_alumno=db_fetch_array($consulta_datos_alumno);

        // Todos los centros
        if ($datos_alumno["id_centro"]==$centro_actual) {

        $edad_alumno=date("Y")-date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($datos_alumno["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($datos_alumno["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_alumno["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }

        $color_fondo_fila_alu="#FFFFFF";
        // Comprueba si tiene contratado el servicio en el mes o el día seleccionado

                $busca_servicio_contratado1=db_query_params('(SELECT * FROM servicios_contratados WHERE id_servicio=? AND id_alumno=? AND fecha_inicio<=? AND fecha_fin>=? AND periodicidad=1) UNION (SELECT * FROM servicios_contratados WHERE id_servicio=? AND id_alumno=? AND fecha_inicio=? AND periodicidad=2)', 'sssssss', array($id_actividad, $datos_alumno["id_alumno"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)), $id_actividad, $datos_alumno["id_alumno"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error()."-A");
                $no_contratado1=true;
                if (db_num_rows($busca_servicio_contratado1)==0) {
                        // Comprueba si contradado en un solo pago
                        $busca_servicio_contratado1=db_query_params('(SELECT * FROM servicios_contratados WHERE id_servicio=? AND id_alumno=? AND fecha_inicio<=? AND fecha_fin>=? AND periodicidad=1) UNION (SELECT * FROM servicios_contratados WHERE id_servicio=? AND id_alumno=? AND periodicidad=2)', 'ssssss', array($id_actividad, $datos_alumno["id_alumno"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)), $id_actividad, $datos_alumno["id_alumno"])) or die(db_error()."-A");
                        if (db_num_rows($busca_servicio_contratado1)==0) {
                                $no_contratado1=true;
                                } else {
                                $no_contratado1=false;
                                }
                        } else {
                        $no_contratado1=false;
                        }
                $busca_servicio_contratado2=db_query("(SELECT * FROM servicios_contratados_aula WHERE id_servicio=".$id_actividad." AND id_aula=".$aula_actual." AND fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."' AND periodicidad=1) UNION (SELECT * FROM servicios_contratados_aula WHERE id_servicio=".$id_actividad." AND id_aula=".$aula_actual." AND fecha_inicio='".date("Y-m-d",$fecha_time_stamp)."' AND periodicidad=2)") or die(db_error()."-B");
                $no_contratado2=true;
                if (db_num_rows($busca_servicio_contratado2)==0) {
                        $no_contratado2=true;
                        } else {
                        $no_contratado2=false;
                        while($servicio_contratado2=db_fetch_array($busca_servicio_contratado2)) {
                                $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($servicio_contratado2["id_servicio_contratado"], $datos_alumno["id_alumno"]));
                                    if (db_num_rows($busca_si_activado)!=0) {
                                        $no_contratado2=true;
                                        }
                                }
                        }
                        
        if (($no_contratado1==false) or ($no_contratado2==false)) {
                $color_fondo_fila_alu="#A8E58A";
                $asistencia_prevista_aula++;
                $asistencia_prevista++;
                }

        // Comprueba comedor
        
        if ($datos_alumno["comedor"]!=2) {
                $total_comedor_normal++;
                if ($color_fondo_fila_alu=="#FFFFFF") {
                        // Tiene contratado el comedor
                        $color_fondo_fila_alu="#b4d8f7";
                        } else {
                        // Servicio facturado y además tiene contratado el comedor
                        $color_fondo_fila_alu="#f5c36d";
                        }
                }

        ?>
        <tr bgcolor="<?=$color_fondo_fila_alu?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila_alu;?>')">
        <td><font size=2 face="<?= $fuente_general; ?>"><a href="javascript:servicios(<?=$datos_alumno["id_alumno"];?>)"><b><font color="<?=$color_fuente_linea;?>"><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 

                // Busca si ya está registrado por la mañana
                $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos_asistencia WHERE id_alumno=? AND id_servicio=? AND fecha=?', 'sss', array($datos_alumno["id_usuario"], $id_actividad, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                        ?>
                        <input<?=$estado_botones;?> type="button" value="Entrada" name="botonIn<?=$datos_alumno["id_usuario"]?>" onclick="registro_entrada(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Salida" disabled></td>
                        <?php 
                        } else {
                        $datos_registro=db_fetch_array($busca_registro_entrada);
                        if ($datos_registro["hora_entrada"]=="") {
                        ?>
                        <input<?=$estado_botones;?> type="button" value="Entrada" name="botonIn<?=$datos_alumno["id_usuario"]?>" onclick="registro_entrada(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Salida" disabled></td>
                        <?php 
                        } else {
                        $cuenta_asistencia++;
                        $cuenta_aula++;
                        ?>
                        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?> <br><a href="javascript:anular_asistencia(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"><small><small>(Anular asistencia)</small></small></a></font>
                        </td>
                        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <?php 
                        if ($datos_registro["hora_salida"]=="") {
                        ?>
                        <input<?=$estado_botones;?> type="button" value="Salida" onclick="registro_salida(<?=$datos_registro["id_servicio_consumido"]?>)">
                        <?php 
                        } else {
                        ?>
                        <font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font>
                        <?php 
                        }
                        ?>
                        </font></td>
                        <?php 
                        }
                        }
                ?></font></td>
                <?php 
                // Comprueba reserva
                if (db_num_rows($busca_registro_entrada)==0) {
                        ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <input type="button" name="botonRe<?=$datos_alumno["id_usuario"]?>" value="Reservar" onclick="reservar_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                        <?php 
                        } else {
                        // Comprueba si ya ha asistido
                        if (($datos_registro["hora_entrada"]=="") and ($datos_registro["reserva"]==0)) {
                        ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <input type="button" name="botonRe<?=$datos_alumno["id_usuario"]?>" value="Reservar" onclick="reservar_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                        <?php 
                        } else {
                                if ($datos_registro["hora_entrada"]=="") {
                                        $estado_boton_reserva="";
                                        } else {
                                        $estado_boton_reserva=" disabled";
                                        }
                                if ($datos_registro["reserva"]==1) {
                                $reservas++;
                                $reservas_aula++;

/**************************************************************/
                                // Comprueba menu
                                
                                if ($tipo_actividad==1) {
        
                        // Edad actual del niño en años con decimales
                        $edad_para_menu=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                        if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                $edad_para_menu=$edad_para_menu-1;
                                }
                        $edad_para_menu_anos=$edad_para_menu;
                        // Decimales
                        $mes_edad_para_menu=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                        if ($mes_edad_para_menu<0) {
                                $edad_para_menu_meses=(12+$mes_edad_para_menu);
                                $edad_para_menu=$edad_para_menu+round(((12+$mes_edad_para_menu)/12),2);
                                } else {
                                $edad_para_menu_meses=$mes_edad_para_menu;
                                $edad_para_menu=$edad_para_menu+round(($mes_edad_para_menu/12),2);
                                }
                                        if ($edad_para_menu_anos!=1) {
                                                $plural_anios="s";
                                                } else {
                                                $plural_anios="";
                                                }
                                        if ($edad_para_menu_meses>1) {
                                                $plural_meses="es";
                                                } else {
                                                $plural_meses="";
                                                }

                // Comprueba si tiene un tipo de menu seleccionado
                $razon="";
                $menu_actual_en_ficha=$datos_alumno["menu_actual"];
                if ($menu_actual_en_ficha==-1) {
                                if ($datos_aula["id_menu_predefinido"]!=0) {
                                        $razon=$razon."Menu seleccionado en inscripción";
                                        $id_menu_seleccionado=$datos_aula["id_menu_predefinido"];
                                        } else {
                                        $menu_actual_en_ficha=0;
                                        }
                                }
                if ($menu_actual_en_ficha>0) {
                        $id_menu_seleccionado=$menu_actual_en_ficha;
                        } else {
                        if ($menu_actual_en_ficha==0) {

                        // Comprueba si es de regimen
                        $regimen=$datos_alumno["comedor"];
                        if ($regimen==3) {
                                $filtro_regimen=" AND menu_menus.regimen=1";
                                $razon=$razon."Menú de regimen";
                                } else {
                                $filtro_regimen=" AND menu_menus.regimen=0";
                                }

                        // Calcula el menú que le toca por edades
                        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.edad_ini_menu<=$edad_para_menu$filtro_regimen");
                        $diferencia_actual=1000;
                        $id_menu_seleccionado=0;

                        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                                if ($minimo_minimorum>$menu_apropiado["edad_ini_menu"]) {
                                        $minimo_minimorum=$menu_apropiado["edad_ini_menu"];
                                        }
                                $diferencia=$edad_para_menu-$menu_apropiado["edad_ini_menu"];
                                if ($diferencia<=$diferencia_actual) {
                                        $diferencia_actual=$diferencia;
                                        $edad_menu_seleccionado=$menu_apropiado["edad_ini_menu"];
                                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                                        }
                                        }
                                $razon=$razon."Seleccionado por edad (Edad niño $edad_para_menu, Edad menu $edad_menu_seleccionado, Menú seleccionado ".$nombres_menus[$id_menu_seleccionado].")";
                                }
                        }
                        
$totales_por_menu[$id_menu_seleccionado][0]++;
if ($datos_alumno["comedor"]!=2) {
                                 $total_reservas_comedor_normal++;
                                 $total_reservas__aula_comedor_normal++;
                                 $totales_por_menu[$id_menu_seleccionado][1]++;
                                 }
                        
/**************************************************************/
        
                                        }
                                ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                                <input<?=$estado_boton_reserva;?> name="botonRe<?=$datos_alumno["id_usuario"]?>" type="button" value="Anular Reserva" onclick="anular_reserva(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                                <?php 
                                } else {
                                ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                                <input type="button" name="botonRe<?=$datos_alumno["id_usuario"]?>" disabled value="Reservar"></td>
                                <?php 
                                }
                        }
                        
                        }
                // Comprueba no asistencia
                $estado_boton_no_asiste="";
                if ($datos_registro["reserva"]==1) {
                                        $estado_boton_no_asiste=" disabled";
                                        }

                if (db_num_rows($busca_registro_entrada)==0) {
                        ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <input type="button" name="botonNo<?=$datos_alumno["id_usuario"]?>" value="No asiste" onclick="renuncia_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                        <?php 
                        } else {
                        // Comprueba si ya ha asistido
                        if (($datos_registro["hora_entrada"]=="") and ($datos_registro["no_asiste"]==0)) {
                        ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <input<?=$estado_boton_no_asiste;?> name="botonNo<?=$datos_alumno["id_usuario"]?>" type="button" value="No asiste" onclick="renuncia_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                        <?php 
                        } else {
                                if ($datos_registro["hora_entrada"]=="") {
                                        $estado_boton_no_asiste="";
                                        } else {
                                        $estado_boton_no_asiste=" disabled";
                                        }
                                if ($datos_registro["reserva"]==1) {
                                        $estado_boton_no_asiste=" disabled";
                                        }
                                if ($datos_registro["no_asiste"]==1) {
                                $no_asistencias++;
                                $no_asistencias_aula++;
                                ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                                <input<?=$estado_boton_no_asiste;?> name="botonNo<?=$datos_alumno["id_usuario"]?>" type="button" value="Anular" onclick="anular_renuncia_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                                <?php 
                                } else {
                                ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                                <input<?=$estado_boton_no_asiste;?> name="botonNo<?=$datos_alumno["id_usuario"]?>" type="button" value="No asiste" onclick="renuncia_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)"></td>
                                <?php 
                                }
                        }

                        }
                // Comprueba pago
                if (($no_contratado2==true) and ($no_contratado1==true)) {

                        // Comprueba si está pagado
                        if (db_num_rows($busca_registro_entrada)==0) {
                                ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                                <input type="button" value="Cobrar" onclick="cobrar_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)" style="font-size: 10px"><!--<br>
                                <input type="button" value="Facturar" onclick="facturar_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_alumno"]?>,<?=$fecha_time_stamp?>)" style="font-size: 10px">--></td>
                                <?php 
                        
                        } else {
                        // Comprueba si ya ha asistido
                        if ($datos_registro["pagado"]==0) {
                                ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                                <input type="button" value="Cobrar" onclick="cobrar_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)" style="font-size: 10px"><!--<br>
                                <input type="button" value="Facturar" onclick="facturar_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_alumno"]?>,<?=$fecha_time_stamp?>)" style="font-size: 10px">--></td>
                                <?php 
                                } else {
                                $pagados_aula++;
                                $pagados++;
                                ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                                 <input type="button" value="Anular" onclick="anular_cobro(<?=$id_actividad?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>)" style="font-size: 10px"> <input value="Recibo" type="button" onclick="ver_recibo(<?=$datos_registro["id_servicio_consumido"]?>)" style="font-size: 10px"></td>
                                 <?php 
                                }
                        }
                        } else {
                        ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <input type="button" disabled value="En factura mes" style="font-size: 10px"></td>
                        <?php 
                        }

                        /*
                         else {
                        if (($no_contratado2==false) or ($no_contratado1==false)) {
                        $pagados_aula++;
                        $pagados++;
                        }
                        ?><td align="center"><font size=2 face="<?= $fuente_general; ?>">
                        <input type="button" disabled value="En factura mes" style="font-size: 10px"></td>
                        <?
                        }
                        */
                ?>
                <td align="center"><a href="javascript:editar(<?=$datos_alumno["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
                </tr>
                <?php 
                // Todos los centros
                }
            }
        ?>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Asistencia por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$cuenta_aula;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$pagados_aula;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Reservas por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$reservas_aula;?><?php  if ($tipo_actividad==1) { ?><font size=1><br>(Habituales: <?=$total_reservas__aula_comedor_normal;?>/Extras:<?=$reservas_aula-$total_reservas__aula_comedor_normal?>)</font><?php  } ?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Asistencia total por escuela:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$cuenta_asistencia;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$pagados;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Reservas por escuela:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$reservas;?><?php  if ($tipo_actividad==1) { ?><font size=1><br>(Habituales: <?=$total_reservas_comedor_normal;?>/Extras:<?=$reservas-$total_reservas_comedor_normal?>))</font><?php  } ?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        </form>
        </table>
<?php 
if ($tipo_actividad==1) {
?>
        <br>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td align=center colspan=4><font size=2><b>Totales por tipo de menú</b></font></td>
        </tr>
        <tr bgcolor="#efefef">
                <td align=center><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                <td align=center><font size=2 face="<?= $fuente_general; ?>"><b>Total Reservas</b></font></td>
                <td align=center><font size=2 face="<?= $fuente_general; ?>"><b>Habituales</b></font></td>
                <td align=center><font size=2 face="<?= $fuente_general; ?>"><b>Extras</b></font></td>
        </tr>
        <?php 
        $lista_menus=db_query("SELECT * FROM menu_menus ORDER BY nombre_menu");
        while($datos_menus=db_fetch_array($lista_menus)) {
        
        $id_menu_seleccionado=$datos_menus["id_menu"];
        
        $nombre_menu=urldecode($datos_menus["nombre_menu"]);
        $total_por_menu=$totales_por_menu[$id_menu_seleccionado][0];
        $total_habituales_por_menu=$totales_por_menu[$id_menu_seleccionado][1];
        if ($total_por_menu<>0){
        
        ?>
        <tr bgcolor="#efefef">
                <td align=left><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_menu?></b></font></td>
                <td align=right><font size=2 face="<?= $fuente_general; ?>"><?=$total_por_menu;?></font></td>
                <td align=right><font size=2 face="<?= $fuente_general; ?>"><?=$total_habituales_por_menu;?></font></td>
                <td align=right><font size=2 face="<?= $fuente_general; ?>"><?=$total_por_menu-$total_habituales_por_menu;?></font></td>
        </tr>
                <?php 
                }
        }
        ?>
        </table>
<?php 
}
?>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>No está prevista la asistencia de ningún alumno.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="window.location.href='gestion_aulas.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

