<?php  require("../configuracion/parte_superior_privado_bootstrap.php"); ?>
<?php 
include("cuentas_y_tipos.php");

$formulario_name="seleccion_fecha";

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$cuenta_seleccionada=$_POST["cuenta_seleccionada"];
if (strlen($cuenta_seleccionada)==0) {
        $cuenta_seleccionada=$_GET["cuenta_seleccionada"];
        }
$id_centro_sel_costes=$_POST["id_centro_sel_costes"];
if (strlen($id_centro_sel_costes)==0) {
        $id_centro_sel_costes=$_GET["id_centro_sel_costes"];
        }
$partida_sel_costes=$_POST["partida_sel_costes"];
if (strlen($partida_sel_costes)==0) {
        $partida_sel_costes=$_GET["partida_sel_costes"];
        }
$ejercicio=$_POST["ejercicio"];
if (strlen($ejercicio)==0) {
        $ejercicio=$_GET["ejercicio"];
        }
$mes_seleccionado=$_POST["mes"];
if (strlen($mes_seleccionado)==0) {
        $mes_seleccionado=$_GET["mes"];
        }
$tipo_seleccionado=$_POST["tipo"];
if (strlen($tipo_seleccionado)==0) {
        $tipo_seleccionado=$_GET["tipo"];
        }
$proveedor_seleccionado=$_POST["proveedor_seleccionado"];
if (strlen($proveedor_seleccionado)==0) {
        $proveedor_seleccionado=$_GET["proveedor_seleccionado"];
        }
$semana=$_POST["semana"];
if (strlen($semana)==0) {
        $semana=$_GET["semana"];
        }
$curso_mostrado=$_POST["curso_mostrado"];
if (strlen($curso_mostrado)==0) {
        $curso_mostrado=$_GET["curso_mostrado"];
        }
$formulario_enviado=$_POST["formulario_enviado"];
if (strlen($formulario_enviado)==0) {
        $formulario_enviado=$_GET["formulario_enviado"];
        }

if (strlen($id_centro_sel_costes)==0) {
                $id_centro_sel_costes="0";
                $filtro_sc=" WHERE centro_propio=1";
                } else {
                $filtro_sc=" WHERE centro_propio=1 AND id_centro=$id_centro_sel_costes";
                }
if (strlen($cuenta_seleccionada)==0) {
                $cuenta_seleccionada="0";
                }
if (strlen($ejercicio)==0) {
                $ejercicio=strftime("%Y");
                $ejercicio=0;
                }
if (strlen($mes_seleccionado)==0) {
                $mes_seleccionado=strftime("%m");
                $mes_seleccionado=0;
                }
if (strlen($tipo_seleccionado)==0) {
                $tipo_seleccionado="0";
                }
if (strlen($proveedor_seleccionado)==0) {
                $proveedor_seleccionado="0";
                }
if (strlen($semana)==0) {
                $semana="0";
                }
                
if (strlen($curso_mostrado)==0) {
                $curso_mostrado="0";
                }
 
$fecha_inicio_ano=$_POST["fecha_inicio_ano"];
$fecha_inicio_mes=$_POST["fecha_inicio_mes"];
$fecha_inicio_dia=$_POST["fecha_inicio_dia"];
$fecha_inicio=$fecha_inicio_ano."-".$fecha_inicio_mes."-".$fecha_inicio_dia;
$fecha_fin_mes=$_POST["fecha_fin_mes"];
$fecha_fin_dia=$_POST["fecha_fin_dia"];
$fecha_fin_ano=$_POST["fecha_fin_ano"];
$fecha_fin=$fecha_fin_ano."-".$fecha_fin_mes."-".$fecha_fin_dia; 
                
                
$filtros=" WHERE finalizado=1 ";
$filtros_fechas="";
$filtros_fechas1="";
if ($semana!=0) {
        $filtros=$filtros." AND fecha_vencimiento>='".date("Y-m-d",$semana)."' AND fecha_vencimiento<='".date("Y-m-d",$semana+518400)."'";
        $filtros_fechas=$filtros_fechas." AND personal_pagos.fecha_pago>='".date("Y-m-d",$semana)."' AND personal_pagos.fecha_pago<='".date("Y-m-d",$semana+518400)."'";
        } else {
        if ($mes_seleccionado!="0") {
                $filtros=$filtros." AND month(fecha_vencimiento)=".$mes_seleccionado;
                $filtros_fechas=$filtros_fechas." AND month(personal_pagos.fecha_pago)=".$mes_seleccionado;
                }
        if ($ejercicio!="0") {
                $filtros=$filtros." AND year(fecha_vencimiento)=".$ejercicio;
                $filtros_fechas=$filtros_fechas." AND year(personal_pagos.fecha_pago)=".$ejercicio;
                }
        }
        
if ($curso_mostrado!=0) {
        $busca_datos_curso_mostrado=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_mostrado");
        $datos_curso_mostrado=db_fetch_array($busca_datos_curso_mostrado);
        $filtros=" WHERE finalizado=1 ";
        $filtros_fechas="";
        $filtros_fechas1="";
        
        $filtros=$filtros." AND fecha_vencimiento>='".$datos_curso_mostrado["fecha_inicio"]."' AND fecha_vencimiento<='".$datos_curso_mostrado["fecha_fin"]."'";
        $filtros_fechas=$filtros_fechas." AND personal_pagos.fecha_pago>='".$datos_curso_mostrado["fecha_inicio"]."' AND personal_pagos.fecha_pago<='".$datos_curso_mostrado["fecha_fin"]."'";
        }
             
if (($fecha_inicio!="----------") and ($fecha_fin!="----------")) {        
if ((checkdate($fecha_inicio_mes,$fecha_inicio_dia,$fecha_inicio_ano)) and (checkdate($fecha_fin_mes,$fecha_fin_dia,$fecha_fin_ano))) {
        $filtros=" WHERE finalizado=1 ";
        $filtros_fechas="";
        $filtros_fechas1="";
        
        $filtros=$filtros." AND fecha_vencimiento>='".$fecha_inicio."' AND fecha_vencimiento<='".$fecha_fin."'";
        $filtros_fechas=$filtros_fechas." AND personal_pagos.fecha_pago>='".$fecha_inicio."' AND personal_pagos.fecha_pago<='".$fecha_fin."'";
        }
        }
        
if ($cuenta_seleccionada!="0") {
        $filtros=$filtros." AND banco=".$cuenta_seleccionada;
        }
if ($tipo_seleccionado!="0") {
        $filtros=$filtros." AND tipo_coste=".$tipo_seleccionado;
        }
if ($proveedor_seleccionado!="0") {
        $filtros=$filtros." AND proveedor=".$proveedor_seleccionado;
        }
        
$filtros_fechas1=$filtros_fechas;
if ($id_centro_sel_costes!="0") {
        $filtros=$filtros." AND (id_centro=".$id_centro_sel_costes." OR id_centro=0)";
        $filtros_fechas=$filtros_fechas." AND usuarios.id_centro=".$id_centro_sel_costes;
        }
if (strlen($partida_sel_costes)!="0") {
        $filtros=$filtros." AND partida='".$partida_sel_costes."'";
        }


/*
if ($formulario_enviado==1) {
$query=mysql_query("SELECT * FROM gestion_costes".$filtros." ORDER BY fecha_vencimiento DESC;");

$contador=1;

while($rsslc=mysql_fetch_array($query)){
        $matriz_de_codigos[$contador]=$rsslc["id_coste"];
        $contador=$contador+1;
        }

        for ($x=1; $x<=$contador-1; $x=$x+1)
                {
                $act=$_POST["ACT".$matriz_de_codigos[$x]];
                if (strlen($act)==0) {
                                $act=$_GET["ACT".$matriz_de_codigos[$x]];
                                }
                if ($act=="SI") {
                        $act1="1";
                        } else {
                        $act1="0";
                        }
                $query2=mysql_query("UPDATE gestion_costes SET pagado=".$act1." WHERE id_coste=".$matriz_de_codigos[$x]);
                }
}

*/

// Busca alumnos por escuela

if ($curso_mostrado==0) {

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_fin DESC");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
                
               	} else {
               	$id_curso_actual=$curso_mostrado;
               	}

$alumnos_por_escuela=array();
$alumnos_por_comedor=array();
$total_alumnos_centro=0;
$total_alumnos_comedor=0;
$consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");
$numero_de_escuelas=db_num_rows($consulta_centros);
while($centros=db_fetch_array($consulta_centros)){

				$coletilla_tabla="";
				if (strlen($centros["bbdd"])!=0) {
					$coletilla_bbdd=$centros["bbdd"].".";
					}

                $busca_alumnos_escuela=db_query("SELECT * FROM ".$coletilla_bbdd."alumnos WHERE id_curso=$id_curso_actual AND id_centro=".$centros["id_centro"]);

                $alumnos_por_escuela[$centros["id_centro"]]=db_num_rows($busca_alumnos_escuela);
                
                $total_alumnos_centro=$total_alumnos_centro+$alumnos_por_escuela[$centros["id_centro"]];

                $busca_alumnos_comedor=db_query("SELECT * FROM ".$coletilla_bbdd."alumnos WHERE id_curso=$id_curso_actual AND comedor=2 AND id_centro=".$centros["id_centro"]);

                $alumnos_por_comedor[$centros["id_centro"]]=db_num_rows($busca_alumnos_comedor);
                $total_alumnos_comedor=$total_alumnos_comedor+$alumnos_por_comedor[$centros["id_centro"]];

                }

?>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">

function enviar_formulario(accion){
        if ((document.seleccion_fecha.automatico.checked==true)||(accion==1)) {
                var errores=0;

                var mensajes_error="";
                                
                // Comprueba fecha inicio
                                hay_f_ini=false;
                                hay_f_fin=false;
                
                la_fecha=self.document.<?=$formulario_name;?>.fecha_inicio_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_inicio_ano.value;
                if (la_fecha!="--/--/----") {
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de inicio introducida no es correcta.\u000A";errores=1; }
                        hay_f_ini=true;
                        }
                
                // Comprueba fecha fin
                la_fecha=self.document.<?=$formulario_name;?>.fecha_fin_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_fin_ano.value;
                if (la_fecha!="--/--/----") {
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de fin introducida no es correcta.\u000A";errores=1; }
                        hay_f_fin=true;
                                }
                        if ((hay_f_ini==false)&&(hay_f_fin==true)) {
                                mensajes_error=mensajes_error+"Falta introducir la fecha de inicio.\u000A";
                                errores=1;
                                }        
                        if ((hay_f_ini==true)&&(hay_f_fin==false)) {
                                mensajes_error=mensajes_error+"Falta introducir la fecha de fin.\u000A";
                                errores=1;
                                }        
                                
                if (errores==0) {
                                document.<?=$formulario_name;?>.formulario_enviado.value='1';
                                document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        

                }
        }
        
function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

                mes_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino='+nombre_formulario+'&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }

</script>
<input type="button" value="Salir" onclick="window.location.href='<?=$url_web?>/index.php'" style="font-size: 10px"> <input type="button" value="Borrar filtros" onclick="window.location.href='<?=$_SERVER['PHP_SELF'];?>'" style="font-size: 10px">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name?>" method="POST" action="<?=$_SERVER['PHP_SELF'];?>">
<input type="hidden" value="0" name="formulario_enviado">
        <tr>
                <td width="100%" valign="top" bgcolor="#fff" align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de gastos</b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuenta:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="cuenta_seleccionada" style="font-size: 10px" size=1 onchange="document.seleccion_fecha.submit();">
                                <option value="0"<?php if ($cuenta_seleccionada=="0") { echo " selected"; }?>>Todas las cuentas</option>
                                <?php 
                                $consulta_cuentas_bancos=db_query("SELECT * FROM cuentas_gastos ORDER BY id_cuenta");
                                while($cuentas_bancos=db_fetch_array($consulta_cuentas_bancos)) {
                                        $c_cuen=$cuentas_bancos["id_cuenta"];
                                        $nombre_cuenta=$cuentas_bancos["nombre_cuenta"];
                                ?>
                                <option value="<?=$c_cuen?>"<?php if ($cuenta_seleccionada==$c_cuen) { echo " selected"; }?>><?=$nombre_cuenta?></option>
                                <?php 
                                }
                                ?>
                                </select>&nbsp;&nbsp;&nbsp;&nbsp;<font size=2 face="<?= $fuente_general; ?>"><b>Tipo de gasto:</b> <select name="tipo" size=1 class="formulario_tipo" onchange="document.seleccion_fecha.submit();">
                                <option value="0"<?php if ($tipo_seleccionado=="0") { echo " selected"; }?>>Todos</option>
                                <?php 
                                for ($c_tipo=1; $c_tipo<=count($tipos_costes); $c_tipo=$c_tipo+1) {
                                        $nombre_tipo=$tipos_costes[$c_tipo-1];
                                ?>
                                <option value="<?=$c_tipo?>"<?php if ($tipo_seleccionado==$c_tipo) { echo " selected"; }?>><?=$nombre_tipo?></option>
                                <?php 
                                }
                                ?>
                                </select> <b>Durante el curso: </b> <select name="curso_mostrado" size=1 style="font-size: 10px" onchange="document.seleccion_fecha.submit();">
                                <option value="0">-</option>
                                <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$curso_mostrado) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?></select></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mes y año:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="mes" style="font-size: 10px" size=1 onchange="document.seleccion_fecha.submit();">
                                <option value="0">Todos</option>
                                <?php 
                                // Busca número de factura actual en el ejercicio actual
                                for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
                                {
                                ?>
                                <option value="<?php    echo $c_mes; ?>"<?php if ($c_mes==$mes_seleccionado) { print " selected"; } ?>><?=$nombre_mes[($c_mes)];?></option>
                                <?php 
                                }
                                ?></select> <select name="ejercicio" style="font-size: 10px" size=1 onchange="document.seleccion_fecha.submit();">
                                <option value="0">Todos</option>
                                <?php 
                                // Busca número de factura actual en el ejercicio actual
                                for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+5; $c_ano=$c_ano+1)
                                {
                                ?>
                                <option value="<?php    echo $c_ano; ?>"<?php if ($c_ano==$ejercicio) { print " selected"; } ?>><?php    echo $c_ano; ?></option>
                                <?php 
                                }
                                ?></select></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Proveedor:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="proveedor_seleccionado" style="font-size: 10px" size=1 onchange="document.seleccion_fecha.submit();">
                                <option value="0"<?php if ($proveedor_seleccionado=="0") { echo " selected"; }?>>Todos los proveedores</option>
                                <?php 
                                $consulta_clientes = db_query("SELECT * FROM proveedores ORDER BY RAZON_SOCIAL") or die (db_error());
                                while($datos_clientes = db_fetch_array($consulta_clientes)){
                                // Comprueba si tienen facturas emitidas
                                $busca_fact=db_query("SELECT * FROM gestion_costes WHERE proveedor=".$datos_clientes["ID_PROVEEDOR"]);
                                if (db_num_rows($busca_fact)!=0) {
                                        ?>
                                        <option value="<?=$datos_clientes["ID_PROVEEDOR"];?>"<?php if ($proveedor_seleccionado==$datos_clientes["ID_PROVEEDOR"]) { echo " selected"; }?>><?=$datos_clientes["RAZON_SOCIAL"]?></option>
                                        <?php 
                                        }
                                }
                                ?>
                                </select> <b><font size=2>&nbsp;Escuela:</font></b> <select name="id_centro_sel_costes" size=1 style="font-size: 10px" onchange="document.seleccion_fecha.submit();">
                <option value="">Todos</option>
                <option value="0">Común</option>
                <?php 
                $nombres_escuelas=array();
                $nombres_escuelas[0]="Común";
                $consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                $nombres_escuelas[$centros["id_centro"]]=$centros["nombre_centro"];
                ?>
                <option value="<?=$centros["id_centro"];?>"<?php if ($id_centro_sel_costes==$centros["id_centro"]) { echo " selected"; }?>><?=$centros["nombre_centro"];?></option>
                <?php 
                }
                ?>
                </select> <b><font size=2>&nbsp;Partida:</font></b> <select name="partida_sel_costes" size=1 style="font-size: 10px" onchange="document.seleccion_fecha.submit();">
                <option value="">Todas</option>
                <?php 
                $busca_costes_por_partida=db_query("SELECT gestion_costes.partida, Sum(gestion_costes.importe) AS SumaDeimporte FROM gestion_costes GROUP BY gestion_costes.partida ORDER BY gestion_costes.partida;");
                while($costes_por_partida=db_fetch_array($busca_costes_por_partida)) {
                ?>
                <option value="<?=$costes_por_partida["partida"];?>"<?php if ($partida_sel_costes==$costes_por_partida["partida"]) { echo " selected"; }?>><?=$costes_por_partida["partida"];?></option>
                <?php 
                }
                ?>
                </select></font></td>
                                <?php 
                                if (($mes_seleccionado!="0") and ($ejercicio!="0")) {
                                ?>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Semana:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="semana" style="font-size: 10px" size=1 onchange="document.seleccion_fecha.submit();">
                                <option value="0">Todas</option>
                                <?php 
                                // Busca el inicio de la primera semana del mes seleccionado
                                $inicio_mes=date("m/d/Y",strtotime($mes_seleccionado."/1/".$ejercicio));
                                $inicio_mes_time_stamp=strtotime($inicio_mes);
                                $diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);

                                //Inicio es el día en el que empieza la primera semana del mes
                                $inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;
                                
                                for ($c_semana=0; $c_semana<=5; $c_semana=$c_semana+1)
                                {
                                ?>
                                <option value="<?=$inicio_time_stamp;?>"<?php if ($inicio_time_stamp==$semana) { print " selected"; } ?>>del <?=date("d/m/Y",$inicio_time_stamp);?> al <?=date("d/m/Y",$inicio_time_stamp+518400);?></option>
                                <?php 
                                $inicio_time_stamp=$inicio_time_stamp+604800;
                                if ($mes_seleccionado!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
                                }
                                ?></select><?php 
                                if (date("m",$semana)!=date("m",$semana+518400)) {
                                ?> *<?php  } ?></font></td>
                                <?php 
                                } else {
                                ?>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Semana del:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Selecciona primero el mes y el año</font></td>
                                <?php 
                                }
                                ?>
                        </tr>
        <tr valign="top" bgcolor="#FFFFFF">
        <td colspa></td>
        <td align="right"><font size=2 face="Trebuchet MS"><b>Entre los días:</b></font></td>
        <td align="left" colspan=2><font size=2 face="<?= $fuente_general; ?>"><select name="fecha_inicio_dia" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_inicio_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_inicio_mes" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_inicio_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_inicio_ano" class="formulario_tipo">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+5; $c_ano>=strftime("%Y")-10; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_inicio_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_inicio','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font> y <select name="fecha_fin_dia" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_fin_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_mes" class="formulario_tipo">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_fin_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_fin_ano" class="formulario_tipo">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+5; $c_ano>=strftime("%Y")-10; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_fin_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha_fin','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></font> <input type="button" style="font-size: 10px" value="Mostrar" onclick="document.seleccion_fecha.submit();"></td>
        </tr>
                         </table>
                         <?php 
                         if (date("n",$semana)<date("n",$semana+518400)) {
                         ?><font size=2 face="Trebuchet MS">
                         * <b>NOTA:</b> La semana incluye días del mes anterior o posterior, si hay facturas con fecha de vencimiento<br>dentro de la semana pero en el mes distinto al seleccionado, tambien se incluirán en el listado.
                         </font><?php 
                         }
                         ?>
                </td>
        </tr>
        <tr>
                <td width="100%" valign="top" bgcolor="#fff" align="center">                        
                        <?php 
                        $consulta_costes=db_query("SELECT * FROM gestion_costes".$filtros." ORDER BY fecha_vencimiento");

                        if (db_num_rows($consulta_costes)==0) {
                                ?>
					<table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">                                
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td align="center" colspan=14><font size=2 face="<?= $fuente_general; ?>"><b>No se han registrado gastos del tipo seleccionado, en la cuenta seleccionada y en el periodo seleccionado</b></font></td>
                        </tr>
					</table>
                                <?php 
                                }
                        $total_costes=0;
                        $total_costes_pagado=0;
                        $total_tipos=array();
                        $total_tipos_pagado=array();
                        $total_cuentas=array();
                        $total_cuentas_pagado=array();
                        $contador_costes=0;
                        $total_centros=array();
                        $total_centros_pagado=array();
                        
                        $total_meses=array();												
                        $listado_meses=array();
                        
                        for ($c_anos=2008;$c_anos<=date("Y")-1;$c_anos++) {
	                        for ($c_meses=1;$c_meses<=12;$c_meses++) {
		                     
		                    	$listado_meses[date("Ym",strtotime($c_anos."-".$c_meses."-1"))]=date("m/Y",strtotime($c_anos."-".$c_meses."-1"));
		                    	    
	                        }	                        
                        }
                        
                        while($datos_costes=db_fetch_array($consulta_costes)) {
                        
                        // Comprueba si es comun y hay que dividirlo por escuelas
                        
                        $parte_centro_sel=0;

                        $texto_proporcional="";
                        
                        if ($datos_costes["id_centro"]==0) {
                        
                                $proporcional=$datos_costes["proporcional"];

                                $escuelas1=$datos_costes["escuelas"];

                                $contador_escuelas=0;

                                if (strlen($escuelas1)!=0) {
                                
                                        $escuelas2=explode(",",$escuelas1);

                                        for ($cs=0;$cs<=count($escuelas2);$cs++) {
                                                if ($proporcional==1) {
                                                        $parte_esta=round(($datos_costes["importe"]*round(($alumnos_por_escuela[$escuelas2[$cs]]/$total_alumnos_centro),2)),2);
                                                        } else {
                                                        $parte_esta=round(($datos_costes["importe"]/$numero_de_escuelas),2);
                                                        }
                                                $total_centros[$escuelas2[$cs]]=$total_centros[$escuelas2[$cs]]+$parte_esta;

                                                if ($escuelas2[$cs]==$id_centro_sel_costes) {
                                                        $parte_centro_sel=$parte_esta;
                                                        }
                                                if ($parte_esta>0) { $contador_escuelas++; }
                                                }

                                        } else {
                                        // En todas
                                        $consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");

                                        while($centros=db_fetch_array($consulta_centros)){
                                        
                                                $id_escuela_fila=$centros["id_centro"];
                                                
                                                if ($proporcional==1) {
                                                        if ($datos_costes["partida"]=="SUMINISTROS: Comida") {
                                                                $parte_esta=round(($datos_costes["importe"]*round(($alumnos_por_comedor[$id_escuela_fila]/$total_alumnos_comedor),2)),2);
                                                                } else {
                                                                $parte_esta=round(($datos_costes["importe"]*round(($alumnos_por_escuela[$id_escuela_fila]/$total_alumnos_centro),2)),2);
                                                                }
                                                        } else {
                                                        $parte_esta=round(($datos_costes["importe"]/$numero_de_escuelas),2);
                                                        }
                                                        
                                                $total_centros[$id_escuela_fila]=$total_centros[$id_escuela_fila]+$parte_esta;

                                                if ($id_escuela_fila==$id_centro_sel_costes) {
                                                        $parte_centro_sel=$parte_esta;
                                                        }
                                                $contador_escuelas++;
                                                }
                                        
                                        }

                                if ($proporcional==1) {
                                        if ($datos_costes["partida"]=="SUMINISTROS: Comida") {
                                                $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas proporcionalmente al numero de niños con comedor contratado en cada una.";
                                                } else {
                                                $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas proporcionalmente al numero de niños matriculados en cada una.";
                                                }
                                        } else {
                                        $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas en partes iguales.";
                                        }

                                } else {
                                $parte_centro_sel=$datos_costes["importe"];
                                }


                        if ($id_centro_sel_costes!=0) {
                        
                                $importe_total_factura=$datos_costes["importe"];
                                $importe_contabilizado=$parte_centro_sel;
                        
                                } else {
                                
                                $importe_total_factura=$datos_costes["importe"];
                                $importe_contabilizado=$importe_total_factura;

                                }
                                
                        // Tipos de coste

                        $nombre_tipo=$tipos_costes[$datos_costes["tipo_coste"]-1];

                        // Formas de pago

                        $nombre_forma_pago=$formas_de_pago[$datos_costes["forma_pago"]-1];

                        // Cuentas
                                $consulta_cuentas_bancos=db_query("SELECT * FROM cuentas_gastos WHERE id_cuenta=".$datos_costes["banco"]);
                                if (db_num_rows($consulta_cuentas_bancos)!=0) {
                                        $cuentas_bancos=db_fetch_array($consulta_cuentas_bancos);
                                        $nombre_cuenta=$cuentas_bancos["nombre_cuenta"];
                                        } else {
                                        $nombre_cuenta="Desconocida";
                                        }


                        $consulta_datos="SELECT * FROM proveedores WHERE ID_PROVEEDOR=".$datos_costes["proveedor"];
                        $consulta_datos_proveedor = db_query($consulta_datos) or die (db_error());
                        $datos_proveedor=db_fetch_array($consulta_datos_proveedor);
                        $Razon_social = $datos_proveedor["RAZON_SOCIAL"];
                        $CIF = $datos_proveedor["NIF"];

                        $total_costes=$total_costes+$importe_contabilizado;
                        $total_tipos[$datos_costes["tipo_coste"]]=$total_tipos[$datos_costes["tipo_coste"]]+$importe_contabilizado;
                        $total_cuentas[$datos_costes["banco"]]=$total_cuentas[$datos_costes["banco"]]+$importe_contabilizado;
                        $total_centros[$datos_costes["id_centro"]]=$total_centros[$datos_costes["id_centro"]]+$importe_contabilizado;

                        if ($datos_costes["pagado"]==1) {
                                $total_costes_pagado=$total_costes_pagado+$importe_contabilizado;
                                $total_tipos_pagado[$datos_costes["tipo_coste"]]=$total_tipos_pagado[$datos_costes["tipo_coste"]]+$importe_contabilizado;
                                $total_cuentas_pagado[$datos_costes["banco"]]=$total_cuentas_pagado[$datos_costes["banco"]]+$importe_contabilizado;
                                $total_centros_pagado[$datos_costes["id_centro"]]=$total_centros_pagado[$datos_costes["id_centro"]]+$importe_contabilizado;
                                }

						// Por meses
						
						$mes_actual=date("Ym",strtotime($datos_costes["fecha_vencimiento"]));
						
						if (!in_array($mes_actual, $listado_meses)) {
						
							$listado_meses[$mes_actual]=date("m/Y",strtotime($datos_costes["fecha_vencimiento"]));		
							
						}						

						$total_meses[$mes_actual][0]+=$importe_contabilizado;

                        $contador_costes=$contador_costes+1;
                        }
 
                        if (((strlen($partida_sel_costes)==0) or ($partida_sel_costes=="SUELDOS Y SALARIOS")) and ($proveedor_seleccionado==0))  {

                        // Busca nominas ingresadas en estas fechas
                        
                        $consulta_personal_centros="SELECT * FROM ((SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM usuarios,personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun<>1 AND (personal_pagos.tipo_pago=1 OR personal_pagos.tipo_pago=5)$filtros_fechas GROUP BY personal_pagos.fecha_pago, usuarios.id_centro) UNION (SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM escuelamunicipal.usuarios,escuelamunicipal.personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun<>1 AND (personal_pagos.tipo_pago=1 OR personal_pagos.tipo_pago=5)$filtros_fechas GROUP BY personal_pagos.fecha_pago, usuarios.id_centro)) AS tabla1";
                         
                        $consulta_pagos=db_query($consulta_personal_centros) or die(db_error());
                        $total_nominas=0;
                        while($datos_pagos=db_fetch_array($consulta_pagos)) {
                        
                        $total_costes=$total_costes+$datos_pagos["total_dia"];
                        $total_costes_pagado=$total_costes_pagado+$datos_pagos["total_dia"];
                        
                        $total_centros[$datos_pagos["id_centro"]]=$total_centros[$datos_pagos["id_centro"]]+$datos_pagos["total_dia"];
                        $total_centros_pagado[$datos_pagos["id_centro"]]=$total_centros_pagado[$datos_pagos["id_centro"]]+$datos_pagos["total_dia"];
                        
                        $total_tipos[1]=$total_tipos[1]+$datos_pagos["total_dia"];
                        $total_tipos_pagado[1]=$total_tipos_pagado[1]+$datos_pagos["total_dia"];

                        // $total_cuentas[$datos_costes["banco"]]=$total_cuentas[$datos_costes["banco"]]+$datos_costes["importe"];
                        $total_dia=$datos_pagos["total_dia"]+$totales_comunes[$datos_pagos["id_centro"]];
                        
                        $total_nominas=$total_nominas+$total_dia;
                        
						// Por meses
						
						$mes_actual=date("Ym",strtotime($datos_pagos["fecha_pago"]));
						
						if (!in_array($mes_actual, $listado_meses)) {
						
							$listado_meses[$mes_actual]=date("m/Y",strtotime($datos_pagos["fecha_pago"]));		
							
						}						

						$total_meses[$mes_actual][1]+=$total_dia;                        
                        
                                }
                                
                        // Busca nominas de servicios comunes ingresadas en estas fechas

						$consulta_personal_centros="SELECT * FROM ((SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM usuarios,personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun=1 AND (personal_pagos.tipo_pago=1 OR personal_pagos.tipo_pago=5)$filtros_fechas1 GROUP BY personal_pagos.fecha_pago) UNION (SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM escuelamunicipal.usuarios,escuelamunicipal.personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun=1 AND (personal_pagos.tipo_pago=1 OR personal_pagos.tipo_pago=5)$filtros_fechas1 GROUP BY personal_pagos.fecha_pago)) AS tabla1";

                        $consulta_pagos=db_query($consulta_personal_centros) or die(db_error());

                        while($datos_pagos=db_fetch_array($consulta_pagos)) {

                        // Separa por escuelas
                        $busca_escuelas=db_query("SELECT * FROM centros$filtro_sc");
                        
                        while($datos_escuelas=db_fetch_array($busca_escuelas)) {

                        $id_escuela_fila=$datos_escuelas["id_centro"];

                        $parte_esta=round(($datos_pagos["total_dia"]*round(($alumnos_por_escuela[$id_escuela_fila]/$total_alumnos_centro),2)),2);

                        $total_costes=$total_costes+$parte_esta;
                        $total_costes_pagado=$total_costes_pagado+$parte_esta;

                        $total_centros[$id_escuela_fila]=$total_centros[$id_escuela_fila]+$parte_esta;
                        $total_centros_pagado[$id_escuela_fila]=$total_centros_pagado[$id_escuela_fila]+$parte_esta;

                        $total_tipos[1]=$total_tipos[1]+$parte_esta;
                        $total_tipos_pagado[1]=$total_tipos_pagado[1]+$parte_esta;

                        $total_dia=$parte_esta+$totales_comunes[$id_escuela_fila];

                        $total_nominas=$total_nominas+$total_dia;
                        
						// Por meses
						
						$mes_actual=date("Ym",strtotime($datos_pagos["fecha_pago"]));
						
						if (!in_array($mes_actual, $listado_meses)) {
						
							$listado_meses[$mes_actual]=date("m/Y",strtotime($datos_pagos["fecha_pago"]));		
							
						}						

						$total_meses[$mes_actual][1]+=$total_dia; 
						                        
                                        }
                                }
                                
                        }
                        
                        if (((strlen($partida_sel_costes)==0) or ($partida_sel_costes=="SUELDOS Y SALARIOS: Seguridad Social")) and ($proveedor_seleccionado==0))  {

                        // Busca nominas ingresadas en estas fechas

						$consulta_personal_centros="SELECT * FROM ((SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM usuarios,personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun<>1  AND (personal_pagos.tipo_pago=2 OR personal_pagos.tipo_pago=3 OR personal_pagos.tipo_pago=6)$filtros_fechas GROUP BY personal_pagos.fecha_pago, usuarios.id_centro) UNION (SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM escuelamunicipal.usuarios,escuelamunicipal.personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun<>1  AND (personal_pagos.tipo_pago=2 OR personal_pagos.tipo_pago=3 OR personal_pagos.tipo_pago=6)$filtros_fechas GROUP BY personal_pagos.fecha_pago, usuarios.id_centro)) AS tabla1";

                        $consulta_pagos=db_query($consulta_personal_centros) or die(db_error());
                        $total_nominas1=0;
                        while($datos_pagos=db_fetch_array($consulta_pagos)) {

                        $total_costes=$total_costes+$datos_pagos["total_dia"];
                        $total_costes_pagado=$total_costes_pagado+$datos_pagos["total_dia"];

                        $total_centros[$datos_pagos["id_centro"]]=$total_centros[$datos_pagos["id_centro"]]+$datos_pagos["total_dia"];
                        $total_centros_pagado[$datos_pagos["id_centro"]]=$total_centros_pagado[$datos_pagos["id_centro"]]+$datos_pagos["total_dia"];

                        $total_tipos[1]=$total_tipos[1]+$datos_pagos["total_dia"];
                        $total_tipos_pagado[1]=$total_tipos_pagado[1]+$datos_pagos["total_dia"];

                        // $total_cuentas[$datos_costes["banco"]]=$total_cuentas[$datos_costes["banco"]]+$datos_costes["importe"];

                        $total_dia=$datos_pagos["total_dia"]+$totales_comunes1[$datos_pagos["id_centro"]];

                        $total_nominas1=$total_nominas1+$total_dia;
                        
						// Por meses
						
						$mes_actual=date("Ym",strtotime($datos_pagos["fecha_pago"]));
						
						if (!in_array($mes_actual, $listado_meses)) {
						
							$listado_meses[$mes_actual]=date("m/Y",strtotime($datos_pagos["fecha_pago"]));		
							
						}						

						$total_meses[$mes_actual][1]+=$total_dia; 
                        
                                }
                                
                        // Busca nominas de servicios comunes ingresadas en estas fechas

						$consulta_personal_centros="SELECT * FROM ((SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM usuarios,personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun=1 AND (personal_pagos.tipo_pago=2 OR personal_pagos.tipo_pago=3 OR personal_pagos.tipo_pago=6)$filtros_fechas1 GROUP BY personal_pagos.fecha_pago) UNION (SELECT sum(personal_pagos.importe) AS total_dia,personal_pagos.fecha_pago, usuarios.id_centro FROM escuelamunicipal.usuarios,escuelamunicipal.personal_pagos WHERE usuarios.id_usuario=personal_pagos.id_empleado AND usuarios.servicio_comun=1 AND (personal_pagos.tipo_pago=2 OR personal_pagos.tipo_pago=3 OR personal_pagos.tipo_pago=6)$filtros_fechas1 GROUP BY personal_pagos.fecha_pago)) AS tabla1";

                        $consulta_pagos=db_query($consulta_personal_centros) or die(db_error());

                        while($datos_pagos=db_fetch_array($consulta_pagos)) {

                        // Separa por escuelas
                        $busca_escuelas=db_query("SELECT * FROM centros$filtro_sc");

                        while($datos_escuelas=db_fetch_array($busca_escuelas)) {

                        $id_escuela_fila=$datos_escuelas["id_centro"];

                        $parte_esta=round(($datos_pagos["total_dia"]*round(($alumnos_por_escuela[$id_escuela_fila]/$total_alumnos_centro),2)),2);
                        
                        $total_costes=$total_costes+$parte_esta;
                        $total_costes_pagado=$total_costes_pagado+$parte_esta;

                        $total_centros[$id_escuela_fila]=$total_centros[$id_escuela_fila]+$parte_esta;
                        $total_centros_pagado[$id_escuela_fila]=$total_centros_pagado[$id_escuela_fila]+$parte_esta;

                        $total_tipos[1]=$total_tipos[1]+$parte_esta;
                        $total_tipos_pagado[1]=$total_tipos_pagado[1]+$parte_esta;

                        $total_dia=$parte_esta+$totales_comunes1[$id_escuela_fila];

                        $total_nominas1=$total_nominas1+$total_dia;
                        
						// Por meses
						
						$mes_actual=date("Ym",strtotime($datos_pagos["fecha_pago"]));
						
						if (!in_array($mes_actual, $listado_meses)) {
						
							$listado_meses[$mes_actual]=date("m/Y",strtotime($datos_pagos["fecha_pago"]));		
							
						}						

						$total_meses[$mes_actual][1]+=$total_dia;                         

                                        }
                                }
                                
                        }
                        
                        ?>
<br />                        
<table class="table table-bordered table-hover table-striped">
	<tr>
		<td><b>Mes</b></td>
		<?php
		$partidas_familias=array("Costes generales","Costes de personal");	
			
		foreach ($partidas_familias as $id_partida => $nombre_partida) {	
		?>
		<td><b><?=$nombre_partida?></b></td>
		<?php
		}	
		?>	
		<td><b>Total mes</b></td>			
	</tr>
	<?php	
	foreach ($listado_meses as $id_curso => $nombre_curso) {
	?>
	<tr>
		<td><?=$nombre_curso?></td>
		<?php
		$total_fila=0;
		foreach ($partidas_familias as $id_partida => $nombre_partida) {			
				$signo=1;					
		?>
		<td class="text-right"><?=number_format($total_meses[$id_curso][$id_partida]*$signo,2,",",".");?> €</td>
		<?php
			$total_fila+=$total_meses[$id_curso][$id_partida]*$signo;
		}	
		?>
		<td class="text-right"><?=number_format($total_fila,2,",",".");?> €</td>
	</tr>
	<?php
	}
	?>
</table>                       
<br />
<table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                <td align="center" bgcolor="#efefef" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Importe</b></font></td>
                                <td align="center" colspan=2 bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Pagado</b></font></td>
                                <td align="center" colspan=2 bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Falta pagar</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b>Total gastos</b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_costes, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_costes_pagado, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_costes-$total_costes_pagado, 2, ',', '.')?> &euro;</font></td>
                        </tr>
                        <?php 
                        if ($cuenta_seleccionada=="0") {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=11><font size=1 face="<?= $fuente_general; ?>"><b>Total gastos por cuentas</b></font></td>
                        </tr>
                        <?php 
                         $consulta_cuentas_bancos=db_query("SELECT * FROM cuentas_gastos ORDER BY id_cuenta");
                         while($cuentas_bancos=db_fetch_array($consulta_cuentas_bancos)) {
                         
                         $c_cuen=$cuentas_bancos["id_cuenta"];
                         $nombre_cuenta=$cuentas_bancos["nombre_cuenta"];

                         ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b><?=$nombre_cuenta?></b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_cuentas[$c_cuen], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_cuentas_pagado[$c_cuen], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_cuentas[$c_cuen]-$total_cuentas_pagado[$c_cuen], 2, ',', '.')?> &euro;</font></td>
                          </tr>
                                <?php 
                          }
                        }
                        if ($tipo_seleccionado=="0") {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=11><font size=1 face="<?= $fuente_general; ?>"><b>Total gastos por tipo</b></font></td>
                        </tr>
                        <?php 
                          for ($c_tipos=1; $c_tipos<=count($tipos_costes); $c_tipos=$c_tipos+1) {
                          if ($total_tipos[$c_tipos]!=0) {

                                $nombre_tipo=$tipos_costes[$c_tipos-1];
                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b><?=$nombre_tipo?></b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_tipos[$c_tipos], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_tipos_pagado[$c_tipos], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_tipos[$c_tipos]-$total_tipos_pagado[$c_tipos], 2, ',', '.')?> &euro;</font></td>
                          </tr>
                                <?php 
                          }
                          }
                          }
                        if ($id_centro_sel_costes==0) {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=11><font size=1 face="<?= $fuente_general; ?>"><b>Total gastos comunes</b></font></td>
                        </tr>
                        <?php 
                        $id_centro_resumen=0;
                        $nombre_centro=$nombres_escuelas[$id_centro_resumen];
                        ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b>Total <?=$nombre_centro?></b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_centros[$id_centro_resumen], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_centros_pagado[$id_centro_resumen], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_centros[$id_centro_resumen]-$total_centros_pagado[$id_centro_resumen], 2, ',', '.')?> &euro;</font></td>
                          </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=11><font size=1 face="<?= $fuente_general; ?>"><b>Total gastos desglosados por escuela</b></font></td>
                        </tr>
                        <?php 
                        $consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");

                        while($centros=db_fetch_array($consulta_centros)){
                                $id_centro_resumen=$centros["id_centro"];
                                $nombre_centro=$nombres_escuelas[$id_centro_resumen];
                          if ($total_centros[$id_centro_resumen]!=0) {
                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b><?=$nombre_centro?></b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_centros[$id_centro_resumen], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_centros_pagado[$id_centro_resumen], 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_centros[$id_centro_resumen]-$total_centros_pagado[$id_centro_resumen], 2, ',', '.')?> ?</font></td>
                          </tr>
                                <?php 
                                }
                          }

                          }
                          if (strlen($partida_sel_costes)=="0") {
                          ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=11><font size=1 face="<?= $fuente_general; ?>"><b>Total gastos por partida</b></font></td>
                        </tr>
                          <?php 
                          $ok_ss1=0;
                          $ok_ss2=0;
                          
                          $busca_costes_por_partida=db_query("SELECT gestion_costes.partida FROM gestion_costes$filtros GROUP BY gestion_costes.partida ORDER BY gestion_costes.partida;");
                          while($costes_por_partida=db_fetch_array($busca_costes_por_partida)) {
                          
                          $total_pagado_part_act=0;
                          $total_part=0;
                          
                          $busca_costes_por_partida_pagados=db_query("SELECT * FROM gestion_costes$filtros AND ((gestion_costes.partida)='".$costes_por_partida["partida"]."');");
                          while($datos_costes_por_partida=db_fetch_array($busca_costes_por_partida_pagados)) {
                          
                        if ($datos_costes_por_partida["id_centro"]==0) {

                                $proporcional=$datos_costes_por_partida["proporcional"];

                                $escuelas1=$datos_costes_por_partida["escuelas"];

                                $contador_escuelas=0;

                                if (strlen($escuelas1)!=0) {

                                        $escuelas2=explode(",",$escuelas1);

                                        for ($cs=0;$cs<=count($escuelas2);$cs++) {
                                                if ($proporcional==1) {
                                                        $parte_esta=round(($datos_costes_por_partida["importe"]*round(($alumnos_por_escuela[$escuelas2[$cs]]/$total_alumnos_centro),2)),2);
                                                        } else {
                                                        $parte_esta=round(($datos_costes_por_partida["importe"]/$numero_de_escuelas),2);
                                                        }
                                                $total_centros[$escuelas2[$cs]]=$total_centros[$escuelas2[$cs]]+$parte_esta;

                                                if ($escuelas2[$cs]==$id_centro_sel_costes) {
                                                        $parte_centro_sel=$parte_esta;
                                                        }
                                                if ($parte_esta>0) { $contador_escuelas++; }
                                                }

                                        } else {
                                        // En todas
                                        $consulta_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 ORDER BY nombre_centro;");

                                        while($centros=db_fetch_array($consulta_centros)){

                                                $id_escuela_fila=$centros["id_centro"];

                                                if ($proporcional==1) {
                                                        if ($datos_costes_por_partida["partida"]=="SUMINISTROS: Comida") {
                                                                $parte_esta=round(($datos_costes_por_partida["importe"]*round(($alumnos_por_comedor[$id_escuela_fila]/$total_alumnos_comedor),2)),2);
                                                                } else {
                                                                $parte_esta=round(($datos_costes_por_partida["importe"]*round(($alumnos_por_escuela[$id_escuela_fila]/$total_alumnos_centro),2)),2);
                                                                }
                                                        } else {
                                                        $parte_esta=round(($datos_costes_por_partida["importe"]/$numero_de_escuelas),2);
                                                        }

                                                $total_centros[$id_escuela_fila]=$total_centros[$id_escuela_fila]+$parte_esta;

                                                if ($id_escuela_fila==$id_centro_sel_costes) {
                                                        $parte_centro_sel=$parte_esta;
                                                        }
                                                $contador_escuelas++;
                                                }

                                        }

                                if ($proporcional==1) {
                                        if ($datos_costes_por_partida["partida"]=="SUMINISTROS: Comida") {
                                                $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas proporcionalmente al numero de niños con comedor contratado en cada una.";
                                                } else {
                                                $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas proporcionalmente al numero de niños matriculados en cada una.";
                                                }
                                        } else {
                                        $texto_proporcional=$texto_proporcional."entre $contador_escuelas escuelas en partes iguales.";
                                        }

                                } else {
                                $parte_centro_sel=$datos_costes_por_partida["importe"];
                                }


                        if ($id_centro_sel_costes!=0) {

                                $importe_total_factura=$datos_costes_por_partida["importe"];
                                $importe_contabilizado=$parte_centro_sel;

                                } else {

                                $importe_total_factura=$datos_costes_por_partida["importe"];
                                $importe_contabilizado=$importe_total_factura;

                                }
                                
                          $total_part=$total_part+$importe_contabilizado;
                          
                          if ($datos_costes_por_partida["pagado"]==1) {
                                  $total_pagado_part_act=$total_pagado_part_act+$importe_contabilizado;
                                }
                          
                }


                          if ($costes_por_partida["partida"]=="SUELDOS Y SALARIOS") {
                                $total_pagado_part_act=$total_pagado_part_act+$total_nominas;
                                $total_part=$total_part+$total_nominas;
                                $ok_ss1=1;
                                }
                          if ($costes_por_partida["partida"]=="SUELDOS Y SALARIOS: Seguridad Social") {
                                $total_pagado_part_act=$total_pagado_part_act+$total_nominas1;
                                $total_part=$total_part+$total_nominas1;
                                $ok_ss2=1;
                                }
                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b><?=$costes_por_partida["partida"]?></b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_part, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_part_act, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_part-$total_pagado_part_act, 2, ',', '.')?> &euro;</font></td>
                          </tr>
                                <?php 
                          }
                          
                          if ($ok_ss1==0) {
                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b>SUELDOS Y SALARIOS</b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_nominas, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_nominas, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format(0, 2, ',', '.')?> &euro;</font></td>
                          </tr>
                                <?php 
                                }
                          if ($ok_ss2==0) {
                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b>SUELDOS Y SALARIOS: Seguridad Social</b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_nominas1, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_nominas1, 2, ',', '.')?> &euro;</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format(0, 2, ',', '.')?> &euro;</font></td>
                          </tr>
                                <?php 
                                }
                                
                          }
                          ?>
                        </table>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
 db_close();

 require("../configuracion/parte_inferior.php"); ?>
