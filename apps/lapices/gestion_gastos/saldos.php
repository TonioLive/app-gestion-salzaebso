<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$cuenta_seleccionada=$_GET["cuenta_seleccionada"];
$ejercicio=$_POST["ejercicio"];
$mes_seleccionado=$_POST["mes"];
$tipo_seleccionado=$_POST["tipo"];
$proveedor_seleccionado=$_POST["proveedor_seleccionado"];
$semana=$_POST["semana"];
$formulario_enviado=$_POST["formulario_enviado"];

if (strlen($cuenta_seleccionada)==0) {
                $cuenta_seleccionada="0";
                }
if (strlen($ejercicio)==0) {
                $ejercicio=strftime("%Y");
                }
if (strlen($mes_seleccionado)==0) {
                $mes_seleccionado=strftime("%m");
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
                
$filtros=" WHERE finalizado=1 ";
if ($semana!=0) {
        $filtros=$filtros." AND fecha_vencimiento>='".date("Y-m-d",$semana)."' AND fecha_vencimiento<='".date("Y-m-d",$semana+518400)."'";
        } else {
        if ($mes_seleccionado!="0") {
                $filtros=$filtros." AND month(fecha_vencimiento)=".$mes_seleccionado;
                }
        if ($ejercicio!="0") {
                $filtros=$filtros." AND year(fecha_vencimiento)=".$ejercicio;
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

if ($formulario_enviado==1) {
$query=db_query("SELECT * FROM gestion_costes".$filtros." ORDER BY fecha_vencimiento DESC;");

$contador=1;

while($rsslc=db_fetch_array($query)){
        $matriz_de_codigos[$contador]=$rsslc["id_coste"];
        $contador=$contador+1;
        }

        for ($x=1; $x<=$contador-1; $x=$x+1)
                {
                $act=$_POST["ACT".$matriz_de_codigos[$x]];
                if ($act=="SI") {
                        $act1="1";
                        } else {
                        $act1="0";
                        }
                $query2=db_query("UPDATE gestion_costes SET pagado=".$act1." WHERE id_coste=".$matriz_de_codigos[$x]);
                }
}

?>
<script language="Javascript">
function borrar_coste(id_coste) {
        if (confirm("¿Realmente desea eliminar el coste Nº "+id_coste+"?")) {
                window.location.href="borrar_coste.php?id_coste="+id_coste+"&tipo=<?=$tipo_seleccionado;?>&cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>";
                }
        }
function enviar_formulario(){
        if (document.seleccion_fecha.automatico.checked==true) {
                document.seleccion_fecha.formulario_enviado.value='1';
                document.seleccion_fecha.submit();
                }
        }

</script>
<input type="button" value="Salir" onclick="window.location.href='<?=$url_web?>/index.php'" style="font-size: 10px"> <input type="button" value="Nuevo coste" onclick="window.location.href='edicion_coste.php?cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>'" style="font-size: 10px">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="seleccion_fecha" method="POST" action="<?=$_SERVER['PHP_SELF'];?>">
<input type="hidden" value="0" name="formulario_enviado">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de costes</b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuenta:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="cuenta_seleccionada" style="font-size: 10px" size=1 onchange="document.seleccion_fecha.submit();">
                                <option value="0"<?php if ($cuenta_seleccionada=="0") { echo " selected"; }?>>Todas las cuentas</option>
                                <option value="1"<?php if ($cuenta_seleccionada=="1") { echo " selected"; }?>>Caja (contado)</option>
                                <option value="2"<?php if ($cuenta_seleccionada=="2") { echo " selected"; }?>>La Caixa</option>
                                <option value="3"<?php if ($cuenta_seleccionada=="3") { echo " selected"; }?>>BANCAJA</option>
                                <option value="4"<?php if ($cuenta_seleccionada=="4") { echo " selected"; }?>>BANCAJA Credito</option>
                                <option value="5"<?php if ($cuenta_seleccionada=="5") { echo " selected"; }?>>Caja Madrid</option>
                                <option value="6"<?php if ($cuenta_seleccionada=="6") { echo " selected"; }?>>BBVA</option>
                                </select>&nbsp;&nbsp;&nbsp;&nbsp;<font size=2 face="<?= $fuente_general; ?>"><b>Tipo de coste:</b></font> <select name="tipo" size=1 class="formulario_tipo" onchange="document.seleccion_fecha.submit();">
                                <option value="0"<?php if ($tipo_seleccionado=="0") { echo " selected"; }?>>Todos</option>
                                <option value="1"<?php if ($tipo_seleccionado=="1") { echo " selected"; }?>>Fijos</option>
                                <option value="2"<?php if ($tipo_seleccionado=="2") { echo " selected"; }?>>Variables</option>
                                </select></font></td>
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
                                <?php 
                                if (($mes_seleccionado!="0") and ($ejercicio!="0")) {
                                ?>
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Semana:</b></font></td>
                                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><select name="semana" style="font-size: 10px" size=1 onchange="document.seleccion_fecha.submit();">
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
                                <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Semana del:</b></font></td>
                                <td colspan=2><font size=1 face="<?= $fuente_general; ?>">Selecciona primero el mes y el año</font></td>
                                <?php 
                                }
                                ?>
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
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Concepto</b></font></td>
                                <td align="center" width=75><font size=1 face="<?= $fuente_general; ?>"><b>Importe</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Forma de pago</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Cuenta</b></font></td>
                        </tr>
                        <?php 
                        // Consulta ingresos y gastos
                        $historial=array();
                        
                        $consulta_costes=db_query("SELECT * FROM gestion_costes".$filtros." ORDER BY fecha_vencimiento DESC");
                        $contador_array=0;
                        $total_costes=0;
                        $total_costes_pagado=0;
                        $total_tipos=array();
                        $total_tipos_pagado=array();
                        $total_cuentas=array();
                        $total_cuentas_pagado=array();
                        $contador_costes=0;
                        while($datos_costes=db_fetch_array($consulta_costes)) {
                                // Tipos de coste
                                /*
                                1... Fijo
                                2... Variable
                                */
                                switch($datos_costes["tipo_coste"])
                                        {
                                        case 1:
                                        $nombre_tipo="Fijo";
                                        break;
                                        case 2:
                                        $nombre_tipo="Variable";
                                        break;
                                        }

                                // Formas de pago
                                /*
                                1... efectivo
                                2... Talón bancario
                                3... Pagaré
                                4... Transferencia/Remesa
                                5... Abono
                                */
                                switch($datos_costes["forma_pago"])
                                        {
                                        case 1:
                                        $nombre_forma_pago="Efectivo";
                                        break;
                                        case 2:
                                        $nombre_forma_pago="Talón bancario";
                                        break;
                                        case 3:
                                        $nombre_forma_pago="Pagaré";
                                        break;
                                        case 4:
                                        $nombre_forma_pago="Transferencia/Remesa";
                                        break;
                                        case 5:
                                        $nombre_forma_pago="Abono";
                                        break;
                                        }
                                // Cuentas
                                /*
                                1... Caja (contado)
                                2... La Caixa
                                3... BANCAJA
                                4... BANCAJA Credito
                                5... Caja Madrid
                                6... BBVA
                                */
                                switch($datos_costes["banco"])
                                        {
                                        case 1:
                                        $nombre_cuenta="Caja";
                                        break;
                                        case 2:
                                        $nombre_cuenta="La Caixa";
                                        break;
                                        case 3:
                                        $nombre_cuenta="BANCAJA";
                                        break;
                                        case 4:
                                        $nombre_cuenta="BANCAJA Credito";
                                        break;
                                        case 5:
                                        $nombre_cuenta="Caja Madrid";
                                        break;
                                        case 6:
                                        $nombre_cuenta="BBVA";
                                        break;
                                        }

                                $consulta_datos="SELECT * FROM PROVEEDORES WHERE ID_PROVEEDOR=".$datos_costes["proveedor"];
                                $datos_proveedor = odbc_exec($odbc, $consulta_datos) or die (odbc_errormsg());
                                $Razon_social = odbc_result ($datos_proveedor, "RAZON_SOCIAL");
                                $CIF = odbc_result ($datos_proveedor, "NIF");

                                $total_costes=$total_costes+$datos_costes["importe"];
                                $total_tipos[$datos_costes["tipo_coste"]]=$total_tipos[$datos_costes["tipo_coste"]]+$datos_costes["importe"];
                                $total_cuentas[$datos_costes["banco"]]=$total_cuentas[$datos_costes["banco"]]+$datos_costes["importe"];

                                if ($datos_costes["pagado"]==1) {
                                        $total_costes_pagado=$total_costes_pagado+$datos_costes["importe"];
                                        $total_tipos_pagado[$datos_costes["tipo_coste"]]=$total_tipos_pagado[$datos_costes["tipo_coste"]]+$datos_costes["importe"];
                                        $total_cuentas_pagado[$datos_costes["banco"]]=$total_cuentas_pagado[$datos_costes["banco"]]+$datos_costes["importe"];
                                        }
                                
                                $historial[$contador_array]=array('id_cliente'=>0,'fecha'=>$datos_entradas["FECHA_PEDIDO"],'entradas'=>$datos_entradas["CANTIDAD"],'salidas'=>0,'id_pedido'=>'P-'.$num_pedido,'cliente'=>'');
                                $contador_array=$contador_array+1;
                                }


                                $consulta_ingresos=db_query("SELECT * FROM gestion_ingresos".$filtros." ORDER BY fecha_entrada DESC");


                                $total_ingresos=0;
                                $total_tipos_i=array();
                                $total_cuentas_i=array();
                                while($datos_ingresos=db_fetch_array($consulta_ingresos)) {

                                // Tipos de ingreso
                                /*
                                1... efectivo
                                2... Talón bancario
                                3... Pagaré
                                4... Transferencia/Remesa
                                5... Abono
                                */
                                switch($datos_ingresos["tipo_ingreso"])
                                        {
                                        case 1:
                                        $nombre_tipo="Efectivo";
                                        break;
                                        case 2:
                                        $nombre_tipo="Talón bancario (".date("d/m/Y",strtotime($datos_ingresos["fecha_vencimiento"])).")";
                                        break;
                                        case 3:
                                        $nombre_tipo="Pagaré (".date("d/m/Y",strtotime($datos_ingresos["fecha_vencimiento"])).")";
                                        break;
                                        case 4:
                                        $nombre_tipo="Transferencia/Remesa";
                                        break;
                                        case 5:
                                        $nombre_tipo="Abono";
                                        break;
                                        }
                                // Cuentas
                                /*
                                1... Caja (contado)
                                2... La Caixa
                                3... BANCAJA
                                4... BANCAJA Credito
                                5... Caja Madrid
                                6... BBVA
                                */
                                switch($datos_ingresos["cuenta_ingreso"])
                                        {
                                        case 1:
                                        $nombre_cuenta="Caja";
                                        break;
                                        case 2:
                                        $nombre_cuenta="La Caixa";
                                        break;
                                        case 3:
                                        $nombre_cuenta="BANCAJA";
                                        break;
                                        case 4:
                                        $nombre_cuenta="BANCAJA Credito";
                                        break;
                                        case 5:
                                        $nombre_cuenta="Caja Madrid";
                                        break;
                                        case 6:
                                        $nombre_cuenta="BBVA";
                                        break;
                                        }
                                $consulta_datos="SELECT * FROM TABLA_CIENTES WHERE id_cliente=".$datos_ingresos["cliente"];
                                $datos_cliente = odbc_exec($odbc, $consulta_datos) or die (odbc_errormsg());
                                $Razon_social = odbc_result ($datos_cliente, "Razon_social");
                                $CIF = odbc_result ($datos_cliente, "CIF");
                                $total_ingresos=$total_ingresos+$datos_ingresos["importe"];
                                $total_tipos_i[$datos_ingresos["tipo_ingreso"]]=$total_tipos_i[$datos_ingresos["tipo_ingreso"]]+$datos_ingresos["importe"];
                                $total_cuentas_i[$datos_ingresos["cuenta_ingreso"]]=$total_cuentas_i[$datos_ingresos["cuenta_ingreso"]]+$datos_ingresos["importe"];

                                $historial[$contador_array]=array('id_cliente'=>$datos_salidas["id_cliente"],'fecha'=>$datos_salidas["FECHA_PEDIDO"],'entradas'=>0,'salidas'=>$datos_salidas["CANTIDAD"],'id_pedido'=>'C-'.$num_pedido,'cliente'=>$datos_salidas["Nombre"]);
                                $contador_array=$contador_array+1;
                                }

                        include "ordenar_array.php";

                        $historial_ordenado = mu_sort($historial, "fecha");
                        

                        for($i = 0; $i < $contador_array; $i++){
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_tipo;?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($fecha));?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$concepto;?></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($importe, 2, ',', '.')?> €</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$forma_pago;?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$cuenta;?></font></td>
                        </tr>
                        <?php 
                        $contador_filas=$contador_filas+1;
                        }
                        ?>
                        <tr valign="top">
                                <td align="right" colspan=12><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                <td align="center" bgcolor="#efefef" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Importe</b></font></td>
                                <td align="center" colspan=2 bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Pagado</b></font></td>
                                <td align="center" colspan=3 bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Falta pagar</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b>Total costes</b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_costes, 2, ',', '.')?> €</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_costes_pagado, 2, ',', '.')?> €</font></td>
                                <td align="right" colspan=3><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_costes-$total_costes_pagado, 2, ',', '.')?> €</font></td>
                        </tr>
                        <?php 
                        if ($cuenta_seleccionada=="0") {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=12><font size=1 face="<?= $fuente_general; ?>"><b>Total costes por cuentas</b></font></td>
                        </tr>
                        <?php 
                          for ($c_cuen=1; $c_cuen<=6; $c_cuen=$c_cuen+1) {
                          if ($total_cuentas[$c_cuen]!=0) {
                          switch($c_cuen)
                                {
                                case 1:
                                $nombre_cuenta="Caja";
                                break;
                                case 2:
                                $nombre_cuenta="La Caixa";
                                break;
                                case 3:
                                $nombre_cuenta="BANCAJA";
                                break;
                                case 4:
                                $nombre_cuenta="BANCAJA Credito";
                                break;
                                case 5:
                                $nombre_cuenta="Caja Madrid";
                                break;
                                case 6:
                                $nombre_cuenta="BBVA";
                                break;
                                }
                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b><?=$nombre_cuenta?></b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_cuentas[$c_cuen], 2, ',', '.')?> €</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_cuentas_pagado[$c_cuen], 2, ',', '.')?> €</font></td>
                                <td align="right" colspan=3><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_cuentas[$c_cuen]-$total_cuentas_pagado[$c_cuen], 2, ',', '.')?> €</font></td>
                          </tr>
                                <?php 
                          }
                          }
                        }
                        if ($tipo_seleccionado=="0") {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="left" colspan=12><font size=1 face="<?= $fuente_general; ?>"><b>Total costes por tipo</b></font></td>
                        </tr>
                        <?php 
                          for ($c_tipos=1; $c_tipos<=5; $c_tipos=$c_tipos+1) {
                          if ($total_tipos[$c_tipos]!=0) {
                          switch($c_tipos)
                                {
                                case 1:
                                $nombre_tipo="Efectivo";
                                break;
                                case 2:
                                $nombre_tipo="Talón bancario";
                                break;
                                case 3:
                                $nombre_tipo="Pagaré";
                                break;
                                case 4:
                                $nombre_tipo="Transferencia/Remesa";
                                break;
                                case 5:
                                $nombre_tipo="Abono";
                                break;
                                }
                          ?>
                          <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b><?=$nombre_tipo?></b></font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_tipos[$c_tipos], 2, ',', '.')?> €</font></td>
                                <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_tipos_pagado[$c_tipos], 2, ',', '.')?> €</font></td>
                                <td align="right" colspan=3><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_tipos[$c_tipos]-$total_tipos_pagado[$c_tipos], 2, ',', '.')?> €</font></td>
                          </tr>
                                <?php 
                          }
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
 odbc_close($odbc);
 require("../configuracion/parte_inferior.php"); ?>
