<?php
header('Content-Type: text/html; charset=UTF-8');
require("../configuracion/parte_superior_docente_ancho.php"); ?>
<?php 
if (db_link() instanceof mysqli) {
        mysqli_set_charset(db_link(), 'utf8mb4');
}
$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");





$edad_menu=2;

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
        $estado_botones=" disabled";
        }
        
$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro_curso=" AND alumnos.id_curso=$id_curso_actual";
                                } else {
                                $filtro_curso="";
                                }

$fecha_comedor=date("Y-m-d",$fecha_time_stamp);
$exclusiones_merienda_aula=array();
$busca_exclusiones_merienda=db_query(
        "SELECT id_aula,motivo FROM aulas_exclusiones_merienda "
        ."WHERE id_centro=".(int)$centro_actual
        ." AND id_curso=".(int)$id_curso_actual
        ." AND fecha='".db_escape_string($fecha_comedor)."' AND activa=1"
);
if ($busca_exclusiones_merienda) {
        while($exclusion_merienda=db_fetch_array($busca_exclusiones_merienda)){
                $exclusiones_merienda_aula[(int)$exclusion_merienda["id_aula"]]
                        =$exclusion_merienda["motivo"];
                }
        }

?>
<style>
.comedor-flash{margin:0 auto 14px;max-width:1100px;padding:12px 16px;border-radius:12px;font:600 14px/1.35 Arial,sans-serif}
.comedor-flash.success{background:#e8f6ef;color:#145c49;border:1px solid #bfe3d3}
.comedor-flash.error{background:#fff0ef;color:#9a2c26;border:1px solid #f1c1bd}
.comedor-aula-cabecera{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:10px 12px}
.comedor-aula-titulo{font:700 17px/1.25 Arial,sans-serif}
.comedor-merienda-button{appearance:none;border:1px solid #176c5b;border-radius:9px;background:#176c5b;color:#fff;padding:9px 13px;font:700 12px/1.1 Arial,sans-serif;cursor:pointer}
.comedor-merienda-button.restore{background:#fff;color:#176c5b}
.comedor-merienda-badge{display:inline-block;margin-left:8px;border-radius:999px;background:#fff0d7;color:#8a5700;padding:5px 9px;font:700 11px/1 Arial,sans-serif}
@media (max-width:720px){
  .comedor-aula-cabecera{
    align-items:stretch;
    flex-direction:column;
    position:sticky;
    left:0;
    width:calc(100vw - 54px);
    max-width:336px;
    box-sizing:border-box
  }
  .comedor-merienda-button{width:100%;min-height:42px}
}
</style>
<SCRIPT LANGUAGE=javascript>
<!--

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
        
function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.listado_alumnos.cambio_aula"+id+".value");
                window.location.href='cambio_aula.php?na='+opcion_seleccionada+'&a='+id;
                }
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
        
function editar_registro(id_registro) {
        window.open('editar_registro_c.php?id_registro='+id_registro,'registro','toolbar=no,resizable=no,width=450,height=400');
        }
function registro_entrada(id_registro) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        eval("document.listado_alumnos.boton"+id_registro+".disabled=true");
        eval("document.listado_alumnos.boton"+id_registro+".value='"+hours+":"+minutes+":"+seconds+"'");
        // Método clásico
        // window.open('registro_entrada_c.php?id_registro='+id_registro,'registro'+id_registro,'toolbar=no,resizable=no,width=450,height=400');
        // Usando AJAX
        pagina_destino="/aulas/registro_entrada_c_ajax.php";
        sendAndLoad({desc:'id_registro',dato:id_registro},{desc:'registro',dato:id_registro});
        }
function registro_salida(id_registro) {
        var Digital=new Date()
        var hours=Digital.getHours()
        var minutes=Digital.getMinutes()
        var seconds=Digital.getSeconds()
        document.getElementById("salida"+id_registro).disabled=true;
        document.getElementById("salida"+id_registro).value=hours+":"+minutes+":"+seconds;
        // window.open('registro_salida_c.php?id_registro='+id_registro,'registro'+id_registro,'toolbar=no,resizable=no,width=450,height=400');
                pagina_destino="/aulas/registro_salida_c_ajax.php";
        sendAndLoad({desc:'id_registro',dato:id_registro});
        }

function cambiar_merienda_aula(idAula, activa) {
        var mensaje=activa
                ? "La cocina dejará de contar todas las meriendas de esta aula para el día seleccionado. ¿Continuar?"
                : "Se volverán a contar las meriendas del aula para el día seleccionado. ¿Continuar?";
        if (!confirm(mensaje)) {
                return;
                }
        var formulario=document.createElement("form");
        formulario.method="post";
        formulario.action="/aulas/merienda_aula.php";
        var datos={
                "_csrf":"<?=app_escape(app_csrf_token());?>",
                "id_aula":idAula,
                "id_curso":"<?=(int)$id_curso_actual;?>",
                "fecha":"<?=app_escape($fecha_comedor);?>",
                "activa":activa ? 1 : 0
                };
        Object.keys(datos).forEach(function(nombre){
                var campo=document.createElement("input");
                campo.type="hidden";
                campo.name=nombre;
                campo.value=datos[nombre];
                formulario.appendChild(campo);
                });
        document.body.appendChild(formulario);
        formulario.submit();
        }

//-->
</SCRIPT>
<div align="center"><center>
<?php if (isset($_SESSION["comedor_flash"])) {
        $comedor_flash=$_SESSION["comedor_flash"];
        unset($_SESSION["comedor_flash"]); ?>
<div class="comedor-flash <?=app_escape($comedor_flash["type"]);?>"><?=app_escape($comedor_flash["message"]);?></div>
<?php } ?>
<input type="button" onclick="history.go(0)" value="Actualizar para editar" style="font-size: 10px"> <input type="button" onclick="window.location.href='/index.php'" value="Salir" style="font-size: 10px">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br><br><big>Control de asistencia Comedor</big></i><br><br>Escuela: <?=$nombre_centro_actual;?></b><br><br></small></td>
  </tr>
<?php 

$busca_alumnos=db_query("SELECT centro_aulas.anio_maximo, usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos, centro_aulas WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.aula=centro_aulas.id_aula AND alumnos.id_centro=$centro_actual$filtro_curso AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND servicios_consumidos.tipo_menu<>2 ORDER BY centro_aulas.anio_maximo DESC, centro_aulas.nombre, alumnos.aula, tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, servicios_consumidos.turno") or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=7><font size=1 face="<?= $fuente_general; ?>">Para ver la ficha de un alumno, haz clic sobre su nombre o sobre el botón ( <img src="editar.gif" align="absmiddle">)</font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        $totales_menu=array();
        $totales_platos=array();
        
        $usuario_anterior=0;
        $id_aula_actual=0;
        $ninios_en_aula=0;
        $fila_par_impar=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
        // Comprueba si está inscrito en una actividad que anula comedor
        $busca_actividad=db_query("SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=".$listado_alumnos["id_usuario"]." AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

        $tipo_menu_alu=$listado_alumnos["tipo_menu"];

        $menu_especial_hoy=false;
        if (db_num_rows($busca_actividad)!=0) {
                $menu_especial_hoy=true;
                $tipo_menu_alu=1;
                }
        
        if (($tipo_menu_alu>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

        if ($id_aula_actual!=$listado_alumnos["aula"]) {
        
        if ($id_aula_actual!=0) {
                ?>
                <tr>
                <td colspan=7 bgcolor="#efefef" align="left"><font size=3 face="<?= $fuente_general; ?>"><b>Nº niños que comen en <?=$nombre_aula_actual?>:</b> <?=$ninios_en_aula?></font></td>
                </tr>
                <?php 
                }
                
        $id_aula_actual=$listado_alumnos["aula"];
        
        // Busca datos del aula
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula_actual") or die(db_error());
        $datos_aula=db_fetch_array($consulta_datos_aula);
        $nombre_aula_actual=$datos_aula["nombre"];
        $aula_sin_merienda=isset($exclusiones_merienda_aula[(int)$id_aula_actual]);
        
        ?>
        <tr>
        <td colspan=7 bgcolor="#efefef">
        <div class="comedor-aula-cabecera">
          <div class="comedor-aula-titulo">
            <?=app_escape($nombre_aula_actual);?>
            <?php if ($aula_sin_merienda) { ?><span class="comedor-merienda-badge">Meriendas retiradas</span><?php } ?>
          </div>
          <button type="button"
            class="comedor-merienda-button<?php if ($aula_sin_merienda) { ?> restore<?php } ?>"
            onclick="cambiar_merienda_aula(<?=(int)$id_aula_actual;?>,<?php echo $aula_sin_merienda ? 'false' : 'true'; ?>)">
            <?php echo $aula_sin_merienda ? 'Restaurar meriendas del aula' : 'Quitar meriendas por evento'; ?>
          </button>
        </div>
        </td>
        </tr>
        <tr bgcolor="#efefef" valign="top">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Menú</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        $ninios_en_aula=0;
        }

        // Consulta centro
        $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alumnos["centro_alumno"]);
        $datos_centro=db_fetch_array($busca_datos_centro);
        
        $nombre_centro_alumno=$datos_centro["nombre_centro"];
        
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
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
                $edad_alumno=$edad_alumno." años";
                }
                
        $no_merienda=(
                (int)$listado_alumnos["no_merienda"]===1
                || isset($exclusiones_merienda_aula[(int)$listado_alumnos["aula"]])
        ) ? 1 : 0;


                        if ($menu_especial_hoy==true) {
                                        // Busca menu especial
                                        $filtro_regimen=" AND menu_menus.regimen=2";

                                        $edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                        if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                                $edad_alumno=$edad_alumno-1;
                                                }
                                        $edad_alumno_anos=$edad_alumno;
                                        // Decimales
                                        $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                                        if ($mes_edad_alumno<0) {
                                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                                } else {
                                                $edad_alumno_meses=$mes_edad_alumno;
                                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                                }

                                        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
                                        $diferencia_actual=1000;
                                        $id_menu_seleccionado=0;

                                        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                                                if ($minimo_minimorum>$menu_apropiado["edad_ini_menu"]) {
                                                        $minimo_minimorum=$menu_apropiado["edad_ini_menu"];
                                                        }
                                                $diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
                                                if ($diferencia<=$diferencia_actual) {
                                                        $diferencia_actual=$diferencia;
                                                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                                                        }
                                                }

                                        $menu_seleccionado_hoy=$id_menu_seleccionado;
                                        } else {
                                        $menu_seleccionado_hoy=$listado_alumnos["menu_seleccionado_hoy"];
                                        }

                
        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_seleccionado_hoy) or die(db_error());
        if (db_num_rows($busca_datos_menu)!=0) {
                $id_menu_seleccionado=$menu_seleccionado_hoy;
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);
                
                // Comprueba los alimentos del menú y comprueba las alergias del niño
                
                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_seleccionado AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                $motivo_no_hay="";
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                                                                 $procesar_plato=true;

                                        if ((substr($array_campos_platos[$c_platos],0,8)=="merienda") and ($no_merienda==1)) {
                                                $procesar_plato=false;
                                                }

                                        if ($procesar_plato==true) { // Inicio Procesar plato

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {
                                                $sumar_plato=true;
                                                $consulta_datos_plato=db_query("SELECT * FROM menu_platos WHERE id_plato=$codigo_plato") or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alimento NO introducido: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alergia a $nombre_alergia: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }
                                                                }
                                                        if ($sumar_plato==true) {
                                                        $edad_ini_actual=$datos_plato["edad_ini_plato"];
                                                                if ($edad_ini_actual<2) {
                                                                        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
                                                                        } else {
                                                                        $edad_ini_actual=$edad_ini_actual." años";
                                                                        }
                                                                $edad_fin_actual=$datos_plato["edad_fin_plato"];
                                                                if ($edad_fin_actual<2) {
                                                                        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
                                                                        } else {
                                                                        if ($edad_fin_actual==100) {
                                                                                                $edad_fin_actual=">18";
                                                                                                }
                                                                        $edad_fin_actual=$edad_fin_actual." años";
                                                                        }
                                                                $edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                                                $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                                }
                                                        }
                                                        }

                                                    } // Fin procesar plato
                                                }        // Fin bucle platos

                                        } else {
                                        $motivo_no_hay="Día no programado para el menú asignado: ".urldecode($datos_menu_seleccionado["nombre_menu"]);
                                        }
                
                } else {
                $nombre_menu="Menú no asignado";
                }
        
        $totales_menu[$menu_seleccionado_hoy]=$totales_menu[$menu_seleccionado_hoy]+1;
        $ninios_en_aula++;
        
        $color_fondo_fila="#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila="#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;        
        ?>
        <tr valign="top" bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        $total_menus++;
        echo $nombre_menu;
        ?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$motivo_no_hay;?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        // Busca si ya está registrado

        if ($listado_alumnos["hora_entrada_c"]=="") {
        ?>
        <input<?=$estado_botones;?> id="boton<?=$listado_alumnos["id_usuario"]?>" name="boton<?=$listado_alumnos["id_servicio_consumido"]?>" type="button" value="Registrar entrada" onclick="registro_entrada(<?=$listado_alumnos["id_servicio_consumido"]?>)"></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
        <?php 
        } else {
        ?>
        <font size=3><a href="javascript:editar_registro(<?=$listado_alumnos["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($listado_alumnos["hora_entrada_c"]));?></a></font>
        </td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">
        <?php 
        if ($listado_alumnos["hora_salida_c"]=="") {
        ?>
        <input<?=$estado_botones;?> type="button" name="salida<?=$listado_alumnos["id_servicio_consumido"]?>" id="salida<?=$listado_alumnos["id_servicio_consumido"]?>" value="Registrar salida" onclick="registro_salida(<?=$listado_alumnos["id_servicio_consumido"]?>)">
        <?php 
        } else {
        ?>
        <font size=3><a href="javascript:editar_registro(<?=$listado_alumnos["id_servicio_consumido"]?>);"><?=date("H:i:s",strtotime($listado_alumnos["hora_salida_c"]));?></a></font>
        <?php 
        }
        ?>
        </font></td>
        <?php 
        }
        ?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
        </tr>
        <?php 
        $usuario_anterior=$listado_alumnos["id_usuario"];
                }
        }
                ?>
                <tr>
                <td colspan=7 bgcolor="#efefef" align="left"><font size=3 face="<?= $fuente_general; ?>"><b>Nº niños que comen en <?=$nombre_aula_actual?>:</b> <?=$ninios_en_aula?></font></td>
                </tr>
                <?php 
        
        // Cuenta los menús de educador
        $menus_edu=0;
        
        $busca_menu_educadora=db_query("SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1 AND asistencia_docentes.id_centro_asistencia=$centro_actual ORDER BY asistencia_docentes.menu_seleccionado_hoy");
        if (db_num_rows($busca_menu_educadora)!=0) {
        ?>
        <tr bgcolor="#efefef">
        <td colspan=4><font size=2><b>Educadoras/es</b></font></td>
        <td colspan=4><font size=2><b>Menú seleccionado</b></font></td>
        </tr>
        <?php 
        }
        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {
        
        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]);
        if (db_num_rows($busca_datos_menu)!=0) {
                $id_menu_seleccionado=$menu_educadora["menu_seleccionado_hoy"];
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);
                }
                
         $totales_menu[$menu_educadora["menu_seleccionado_hoy"]]=$totales_menu[$menu_educadora["menu_seleccionado_hoy"]]+1;
        ?>
        <tr>
                <td colspan=4><font size=2><b><?=$menu_educadora["Apellido1"]?> <?=$menu_educadora["Apellido2"]?>, <?=$menu_educadora["Nombre"]?></b></font></td>
                <td colspan=3><font size=2><?=$nombre_menu;?></font></td>
        </tr>
        <?php 
        $menus_edu++;
        }
        
        ?>
        </form>
        </table><br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=2><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de menús servidos</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre menú</b></font></td>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
        </tr>
        <?php 
        $consulta_datos_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu;") or die(db_error());
        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
        if ($totales_menu[$datos_menus["id_menu"]]>0) {

        ?>
        <tr>
                <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"><?=urldecode($datos_menus["nombre_menu"])?></font></td>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$totales_menu[$datos_menus["id_menu"]];?></font></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table><br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=3><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de platos servidos</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre plato</b></font></td>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Instrucciones para servir</b></font></td>
        </tr>
        <?php 
        $consulta_datos_plato=db_query("SELECT * FROM menu_platos ORDER BY nombre_plato, edad_ini_plato DESC") or die(db_error());
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {
        
        $codigo_plato=$datos_plato["id_plato"];
        if ($totales_platos[$codigo_plato][0]>0) {
        ?>
        <tr>
                <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$totales_platos[$codigo_plato][1];?></font></td>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$totales_platos[$codigo_plato][0];?></font></td>
                <td bgcolor="#ffffff" align="left"><font size=1 face="<?= $fuente_general; ?>"> <?=urldecode($datos_plato["instrucciones_servir"]);?></font></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en el comedor.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="history.go(0)" value="Actualizar para editar" style="font-size: 10px"> <input type="button" onclick="window.location.href='/index.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>
