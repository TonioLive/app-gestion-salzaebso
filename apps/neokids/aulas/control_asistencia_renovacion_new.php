<?php 
$self = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] ; //Obtenemos la página en la que nos encontramos
// header("refresh:30; url=$self"); //Refrescamos cada 30 segundos

require("../configuracion/parte_superior_docente_ancho.php"); ?>
<?php 

$contador_recibos=0;

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

// Comprueba si es neceario renovar
$fecha_renovacion=false;
$fecha_inicio_proximo_trimestre='';
$fecha_fin_proximo_trimestre='';

// Busca inicio proximo trimestre
$fecha_control=time()+(86400*14);

$campos_inicio_dias=array("d","l","m","x","j","v","s","d");
$nombres_campos_dias=array("","l","m","x","j","v","s","d");

$busca_proximo_trimestre=db_query("SELECT * FROM aulas_grupos_trimestres WHERE ini_".$campos_inicio_dias[$diasemana].">='".date("Y-m-d",$fecha_control)."' AND fin_".$campos_inicio_dias[$diasemana].">='".date("Y-m-d",$fecha_control)."';") or die(db_error());

if (db_num_rows($busca_proximo_trimestre)!=0) {
        $datos_trimestre=db_fetch_array($busca_proximo_trimestre);
        
        $fecha_renovacion=true;
        $fecha_inicio_proximo_trimestre=$datos_trimestre["ini_".$campos_inicio_dias[$diasemana]];
        $fecha_fin_proximo_trimestre=$datos_trimestre["fin_".$campos_inicio_dias[$diasemana]]; 

        // buscamos numero de sesiones previstas para el proximo trimestre
        $num_dias_trimestre=0;
        $inicio_bucle=strtotime($fecha_inicio_proximo_trimestre);
        $fin_bucle=strtotime($fecha_fin_proximo_trimestre);

                while($inicio_bucle<=$fin_bucle) {
                                
                        // Comprueba si es festivo
                        $busca_cal_laboral=db_query_params('SELECT * FROM calendario_laboral WHERE dia=? AND estado=1', 's', array((string)(date("Y-m-d", $inicio_bucle))));                
                        if (db_num_rows($busca_cal_laboral)==0) {
                                $num_dias_trimestre++;                        
                        }
                                
                        $inicio_bucle=$inicio_bucle+(86400*7);
                }
}

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
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
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
function registro_entrada_morosos(id_aula,id_alumno,fecha,id_registro,tipo) {
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
function registro_entrada(id_aula,id_alumno,fecha,id_registro,tipo) {
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

function editar(codigo) {
        window.open('/alumnos/editar_inscripcion.php?origen_formulario=1&i='+codigo,'editar','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=500');
        }
        
function renovar(id_alumno) {
        window.open('/alumnos/renovar_servicios.php?origen_formulario=1&i='+id_alumno,'editar','toolbar=no,resizable=yes,scrollbars=yes,width=600,height=600');
        } 
        

function renovar_todos() {
                if (confirm("Se van a renovar los servicios de todos los alumnos según las opciones marcadas.\n\n¿Estás seguro/a?")) {
                        document.listado_alumnos.submit();
                        }
                }
                       
//-->
</script>
<div align="center"><center>
<input type="button" class="" onclick="renovar_todos()" value="Renovar según las opciones marcadas"> <input type="button" onclick="window.location.href='calendario_control_renovaciones.php'" value="Salir">
<form name="listado_alumnos" method="POST" action="renovar_todos_new.php">
<input type="hidden" name="fecha_inicio_proximo_trimestre" value="<?=$fecha_inicio_proximo_trimestre?>">
<input type="hidden" name="fecha_fin_proximo_trimestre" value="<?=$fecha_fin_proximo_trimestre?>">
<input type="hidden" name="num_dias_trimestre" value="<?=$num_dias_trimestre;?>">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="75%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?=$dianombre?>, <?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?> - Curso: <?=$nombre_curso_actual?><br><br>Control de asistencia</b></small></td>
  </tr>
<?php 

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT * FROM aulas_grupos WHERE dia=$diasemana AND id_curso=$id_curso_actual ORDER BY hora_inicio");

while($datos_aula=db_fetch_array($consulta_datos_aula)) {

$encabezado_mostrado=true;

// Inicio usuarios propios

$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos, aulas_alumnos_grupos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_grupo=? AND alumnos.id_centro=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY alumnos.nivel_actual,Apellido1, Apellido2, Nombre', 'ssss', array($datos_aula["id_grupo"], $centro_actual, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp - $tiempo_gracia)))) or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {

if ($encabezado_mostrado==true) {

?>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="left"><b>Grupo: <?=date("H:i",strtotime($datos_aula["hora_inicio"]))?> - <?=date("H:i",strtotime($datos_aula["hora_fin"]))?></b></td>
  </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td>&nbsp;</td>
        <td width="250"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td width="120"><font size=1 face="<?= $fuente_general; ?>"><b>Centro</b></font></td>
        <td align="center" width="145"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center" width="250"><font size=1 face="<?= $fuente_general; ?>"><b>Información</b></font></td>
        <td align="center" width="100"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
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
        if (db_num_rows($consulta_albaranes_cliente)==0) {
                $texto_morosidad.="<font color=red>No tiene servicios contratados</font>"; 
                }
            
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
                $texto_morosidad.="<font color='#ff0000'><br>Tiene servicios no facturados<br><a href='/facturacion/editar_servicios.php?i=".$datos_inscripcion["id_alumno"]."#alumno".$listado_alumnos["id_alumno"]."' target='_blank'>Ver servicios</a></font>";
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
                $enlace_fila_actual=" href='/facturacion/editar_servicios.php?i=".$listado_alumnos["id_alumno"]."' target='_blank'";
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

        // Comprueba si debe ser renovado (solo a los activos)
        $info_autorenovacion="";
        if (($fecha_renovacion==true) and ($es_baja==false)) {                                                                
        
                // Comprobamos si se le ha renovado ya en el periodo del proximo trimestre
                $busca_servicio_a_renovar=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? AND tarifa.CATEGORIA=1 AND tarifa.subcat=0 ORDER BY fecha_inicio DESC', 's', array($id_inscripcion)) or die(db_error());
                if (db_num_rows($busca_servicio_a_renovar)!=0) {                                
                        $servicio_a_renovar=db_fetch_array($busca_servicio_a_renovar);
                        
                        $id_contratado=$servicio_a_renovar["id_servicio_contratado"];
                        $dto_servicio_a_renovar=$servicio_a_renovar["descuento"];
                        
                        // Comprobamos si se ha renovado ya
                        if ($fecha_baja<$fecha_inicio_proximo_trimestre) {
                        
                                                        $dias_semana_asiste="";
                                                                                                                $que_dias=array();
                                                                                                                
                                                        $busca_dias_asiste=db_query_params('SELECT * FROM aulas_grupos,aulas_alumnos_grupos WHERE aulas_grupos.id_grupo=aulas_alumnos_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? ORDER BY aulas_grupos.dia', 's', array($id_inscripcion)) or die(db_error());

                                                        $total_dias_asiste=db_num_rows($busca_dias_asiste);

                                                        $proximo_fin=0;

                                                        while($datos_dias_asiste=db_fetch_array($busca_dias_asiste)) {
                                                                        
                                                                        if (!in_array($datos_dias_asiste["dia"],$que_dias)) {
                                                                        
                                                                                $que_dias[]=$datos_dias_asiste["dia"];
                                                                        
                                                                        }

                                                                if ($proximo_fin==0) {
                                                                        $proximo_fin=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                                                                        } else {
                                                                        if ($proximo_fin<=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]])) {
                                                                                $proximo_fin=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                                                                                }
                                                                        }


                                                                }                                                                       

                                                                // ¿Que pasa si viene más de 1 día
                                                                
                                                                if (count($que_dias)>1) {
                                                                        
                                                                        $total_dias_alumno=0;
                                                                        
                                                                        foreach ($que_dias as $dia) {
                                                                        
                                                                                $fecha_inicio_proximo_trimestreA=$datos_trimestre["ini_".$campos_inicio_dias[$dia]];
                                                                                $fecha_fin_proximo_trimestreA=$datos_trimestre["fin_".$campos_inicio_dias[$dia]]; 

                                                                                // buscamos numero de sesiones previstas para el proximo trimestre
                                                                                $inicio_bucleA=strtotime($fecha_inicio_proximo_trimestreA);
                                                                                $fin_bucleA=strtotime($fecha_fin_proximo_trimestreA);

                                                                                        while($inicio_bucleA<=$fin_bucleA) {
                                
                                                                                                // Comprueba si es festivo
                                                                                                $busca_cal_laboralA=db_query_params('SELECT * FROM calendario_laboral WHERE dia=? AND estado=1', 's', array((string)(date("Y-m-d", $inicio_bucleA))));                
                                                                                                if (db_num_rows($busca_cal_laboralA)==0) {
                                                                                                        $total_dias_alumno++;                        
                                                                                                }
                                
                                                                                                $inicio_bucleA=$inicio_bucleA+(86400*7);
                                                                                        }                                                                                
                                                                        
                                                                                }
                                                                
                                                                
                                                                } else {
                                                                        $total_dias_alumno=$num_dias_trimestre;
                                                                }

                                                                // Comprueba si es de los nuevos o de los viejos
                                                                $descuento_adicional=0;
                                                                if ($servicio_a_renovar["cantidad"]!=1) {
                                                                        // De los nuevos
                                                                        $cantidad_a_renovar=$total_dias_alumno;
                                                                } else {
                                                                        // De los viejos
                                                                        $cantidad_a_renovar=$servicio_a_renovar["cantidad"];
                                                                        // Busca servicio equivalente
                                                                        $busca_nuevo_servicio=db_query("SELECT * FROM tarifa LEFT JOIN tipos_iva_servicios ON tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva  WHERE tipo_neokids=".$servicio_a_renovar["tipo_neokids"]." AND numero_bonos=".$total_dias_alumno." AND id_curso=".$servicio_a_renovar["id_curso"]) or die(db_error());
                                                                        if (db_num_rows($busca_nuevo_servicio)!=0) {
                                                                                $servicio_a_renovar=db_fetch_array($busca_nuevo_servicio);
                                                                        } else {
                                                                                // No hay un servicio apropiado, calculo un descuento equivalente
                                                                                $num_sesiones_anterior=$servicio_a_renovar["numero_bonos"];
                                                                                if ($num_sesiones_anterior!=0) {
                                                                                        $descuento_adicional=round(($num_sesiones_anterior-$total_dias_alumno)/$num_sesiones_anterior,2);
                                                                                }                                                                                
                                                                        }
                                                                }

                                $total_servicio=$cantidad_a_renovar*$servicio_a_renovar["PRECIO_VENTA"];
                                
                                $mensaje_dto_adicional="";
                                
                                if ($dto_servicio_a_renovar!=0) {
                                
                                        $mensaje_dto_adicional.=". Descuento aplicado: ".$dto_servicio_a_renovar." %";;
                                
                                }
                                
                                if ($descuento_adicional==0) {
                                        $total_dto_servicio=$total_servicio*round(($dto_servicio_a_renovar/100),2);
                                        } else {
                                        $mensaje_dto_adicional.=" (".$servicio_a_renovar["numero_bonos"]." sesiones con un descuento adicional del ".($descuento_adicional*100)." % por no existir un servicio de ".$total_dias_alumno." sesiones)";
                                        if ($dto_servicio_a_renovar!=0) {
                                                $total_dto_servicio=$total_servicio*$descuento_adicional*round(($dto_servicio_a_renovar/100),2);
                                                } else {
                                                $total_dto_servicio=$total_servicio*$descuento_adicional;
                                                }                                        
                                        }
                                        
                                $base_servicio=$total_servicio-$total_dto_servicio;
                                $iva_servicio=$base_servicio*$servicio_a_renovar["porcentaje_iva"];
                                $total_abs_servicio=$base_servicio+$iva_servicio;                               
                                
                                $opcion_preseleccionada_0="selected";
                                $opcion_preseleccionada_1="";
                                if (($servicio_a_renovar["numero_bonos"]!=10) and ($servicio_a_renovar["numero_bonos"]!=20)) {
                                        $opcion_preseleccionada_0="";
                                        $opcion_preseleccionada_1="selected";
                                        }
                                
                                if (($servicio_a_renovar["numero_bonos"]==10) or ($servicio_a_renovar["numero_bonos"]==20)) {
                                        $opcion_preseleccionada_0="";
                                        $opcion_preseleccionada_1="selected";
                                        }                                                                                      
                                
                                $contador_recibos++;
                                
                                $info_autorenovacion.="<tr>";
                                $info_autorenovacion.="<td colspan=9>".$contador_recibos." ";
                                $info_autorenovacion.="<input type='hidden' name='num_sesiones_".$id_inscripcion."' value='".$cantidad_a_renovar."'>";
                                $info_autorenovacion.="<input type='hidden' name='id_servicio_renovar_".$id_inscripcion."' value='".$servicio_a_renovar["ID"]."'>";
                                $info_autorenovacion.="<input type='hidden' name='dto_servicio_renovar_".$id_inscripcion."' value='".$descuento_adicional."'>";
                                $info_autorenovacion.="<input type='hidden' name='alumnos[]' value='".$id_inscripcion."'>";
                                $info_autorenovacion.="<input type='hidden' name='servicios_".$id_inscripcion."' value='".$servicio_a_renovar["id_servicio"]."'>";
                                $info_autorenovacion.="<input type='hidden' name='contratado_".$id_inscripcion."' value='".$id_contratado."'>";
                                $info_autorenovacion.="<input type='hidden' name='nombres_".$id_inscripcion."' value='".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]."'>";
                                $info_autorenovacion.="<input type='hidden' name='fechas_fin_".$id_inscripcion."' value='".date("Y-m-d",$proximo_fin)."'>";
                                $info_autorenovacion.="<select name='opciones_".$id_inscripcion."'>";

                                        $info_autorenovacion.="<option ".$opcion_preseleccionada_0." value='0'>Renovar el servicio del trimestre anterior</option>";
                                        $info_autorenovacion.="<option ".$opcion_preseleccionada_1." value='1'>Renovar trimestre completo (".$total_dias_alumno." sesiones)</option>";
                                        $info_autorenovacion.="<option ".$opcion_preseleccionada_2." value='2'>Promocionar de bebe a infantil</option>";
                                        $info_autorenovacion.="<option ".$opcion_preseleccionada_3." value='3'>Renovar solo la fecha</option>";
                                        $info_autorenovacion.="<option ".$opcion_preseleccionada_4." value='4' class='text-danger'>No renovar</option></option>";

                                $info_autorenovacion.="</select>";
                                $info_autorenovacion.="<br /><font color'#0000ff' size=2> A <b><i>".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]."</i></b> se le renovará el servicio \"<b><i>".$servicio_a_renovar["ARTICULO"]."</i></b>\" y se emitirá un nuevo recibo por importe de ".number_format($total_abs_servicio,2,",",".")." €$mensaje_dto_adicional. ";
                                
                                $info_autorenovacion.="<br />Se ha modificado la fecha de baja, nueva fecha: ".date("Y-m-d",$proximo_fin).".</font></td></tr>";
                                                                
                        }
                }
        
        }
        // $texto_morosidad.=$info_autorenovacion;
        ?>
<tr bgcolor="<?=$color_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fila;?>')">
        <td width=50><a class="btn btn-info btn-sm" href="javascript:ver_ficha(<?=$listado_alumnos["id_usuario"]?>);"><?=$listado_alumnos["id_usuario"]?> <i class="fa fa-info"></i></a></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><a name="alumno<?=$listado_alumnos["id_alumno"]?>"></a><a<?=$enlace_fila_actual?>><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$nombre_centro_actual?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?><br /><?=$edad_alumno?></font></td>
                <td align="center"><small><small><?=$texto_morosidad?></small></small></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a target="facturas_alumno" href="/facturacion/listado_facturas_alumno.php?id_cliente=<?=$listado_alumnos["id_alumno"];?>" title="Gestionar pagos de facturas"><img src="servicios.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_alumno"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="/gestion/calendario/calendario_asistencia.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>" target="calendario_asistencia" title="Editar calendario docente del alumno"><img src="/gestion/centros/calendario.gif" border=0></a></font></td>
</tr>
        <?php 

        // Texto autorenovacion
        echo($info_autorenovacion);

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
<br>
<input type="button" onclick="renovar_todos()" value="Renovar según las opciones marcadas"> <input type="button" onclick="window.location.href='calendario_control_renovaciones.php'" value="Salir">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

