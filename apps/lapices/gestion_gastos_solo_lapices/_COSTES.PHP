<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$cuenta_seleccionada=$_GET["cuenta_seleccionada"];
$ejercicio=$_GET["ejercicio"];
$mes_seleccionado=$_GET["mes"];
$tipo_seleccionado=$_GET["tipo"];
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
                
$filtros=" WHERE finalizado=1 ";
if ($mes_seleccionado!="0") {
        $filtros=$filtros." AND month(fecha_vencimiento)=".$mes_seleccionado;
        }
if ($ejercicio!="0") {
        $filtros=$filtros." AND year(fecha_vencimiento)=".$ejercicio;
        }
if ($cuenta_seleccionada!="0") {
        $filtros=$filtros." AND banco=".$cuenta_seleccionada;
        }
if ($tipo_seleccionado!="0") {
        $filtros=$filtros." AND tipo_coste=".$tipo_seleccionado;
        }
?>
<script language="Javascript">
function borrar_coste(id_coste) {
        if (confirm("¿Realmente desea eliminar el coste Nº "+id_coste+"?")) {
                window.location.href="borrar_coste.php?id_coste="+id_coste+"&tipo=<?=$tipo_seleccionado;?>&cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>";
                }
        }

</script>
<input type="button" value="Salir" onclick="window.location.href='<?=$url_web?>/index.php'" style="font-size: 10px"> <input type="button" value="Nuevo coste" onclick="window.location.href='edicion_coste.php?cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>'" style="font-size: 10px">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="seleccion_fecha" method="GET" action="<?=$_SERVER['PHP_SELF'];?>">
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
                         </table>
                </td>
        </tr>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>ID</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Factura/Cargo</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Vencimiento</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Núm. Factura</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Proveedor</b></font></td>
                                <td align="center" width=75><font size=1 face="<?= $fuente_general; ?>"><b>Importe</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Forma de pago</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Cuenta</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $consulta_costes=db_query("SELECT * FROM gestion_costes".$filtros." ORDER BY fecha_vencimiento DESC");
                        
                        if (db_num_rows($consulta_costes)==0) {
                                ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td align="center" colspan=11><font size=2 face="<?= $fuente_general; ?>"><b>No se han registrado costesdel tipo seleccionado, en la cuenta seleccionada y en el periodo seleccionado</b></font></td>
                        </tr>

                                <?php 
                                }
                        $total_costes=0;
                        $total_tipos=array();
                        $total_cuentas=array();
                        while($datos_costes=db_fetch_array($consulta_costes)) {
                        
                        // Tipos de coste
                        /* 1... Fijo
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
                        /* 1... efectivo
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
                        /* 1... Caja (contado)
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
                        
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="edicion_coste.php?id_coste=<?=$datos_costes["id_coste"];?>&tipo=<?=$tipo_seleccionado;?>&cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>" title="Editar coste"><?=$datos_costes["id_coste"]?></a></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="costes.php?cuenta_seleccionada=<?=$cuenta_seleccionada?>&tipo=<?=$datos_costes["tipo_coste"];?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>" title="Ver costes de este tipo"><?=$nombre_tipo;?></a></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_costes["fecha_factura"]));?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="costes.php?cuenta_seleccionada=<?=$cuenta_seleccionada?>&tipo=<?=$tipo_seleccionado;?>&ejercicio=<?=date("Y",strtotime($datos_costes["fecha_vencimiento"]));?>&mes=<?=date("m",strtotime($datos_costes["fecha_vencimiento"]));?>" title="Ver costes de este mes"><?=date("d/m/Y",strtotime($datos_costes["fecha_vencimiento"]));?></a></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_costes["num_factura"]?></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$Razon_social;?></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_costes["importe"], 2, ',', '.')?> €</font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_tipo;?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="costes.php?cuenta_seleccionada=<?=$datos_costes["banco"]?>&tipo=<?=$tipo_seleccionado;?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>" title="Ver costes de esta cuenta"><?=$nombre_cuenta;?></a></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_costes["observaciones"]?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="edicion_coste.php?id_coste=<?=$datos_costes["id_coste"];?>&tipo=<?=$tipo_seleccionado;?>&cuenta_seleccionada=<?=$cuenta_seleccionada?>&ejercicio=<?=$ejercicio;?>&mes=<?=$mes_seleccionado;?>" title="Editar coste"><img src="editar.gif" border=0 align="absmiddle" hspace="0" vspace="0"></a> <a href="#" onclick="borrar_coste(<?=$datos_costes["id_coste"];?>)" title="Eliminar coste"><img src="borrar.gif" border=0 align="absmiddle" hspace="0" vspace="0"></a></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" colspan=6><font size=1 face="<?= $fuente_general; ?>"><b>Total costes</b></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_costes, 2, ',', '.')?> €</font></td>
                                <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        </tr>
                        <?php 
                        if ($cuenta_seleccionada=="0") {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="center" colspan=11><font size=1 face="<?= $fuente_general; ?>"><b>Total costes por cuentas</b></font></td>
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
                                <td align="right" colspan=6><font size=1 face="<?= $fuente_general; ?>"><b><?=$nombre_cuenta?></b></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_cuentas[$c_cuen], 2, ',', '.')?> €</font></td>
                                <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                          </tr>
                                <?php 
                          }
                          }
                        }
                        if ($tipo_seleccionado=="0") {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="center" colspan=11><font size=1 face="<?= $fuente_general; ?>"><b>Total costes por tipo</b></font></td>
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
                                <td align="right" colspan=6><font size=1 face="<?= $fuente_general; ?>"><b><?=$nombre_tipo?></b></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_tipos[$c_tipos], 2, ',', '.')?> €</font></td>
                                <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
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
