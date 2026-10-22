<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
if (($nivel_acceso==4) and ($ip_segura==true)) {

$mensaje="Nuevo";
$formulario_name="contrato";
$formulario_accion="nuevo_contrato1.php";

$id_docente=$_GET["id_usuario"];
$id_contrato=$_GET["id_contrato"];

if ((strlen($id_docente)==0) and (strlen($id_contrato)!=0)) {

$mensaje="Editar";

        // Busca datos contrato
        
        $busca_contratos=db_query("SELECT * FROM personal_contratos WHERE id_contrato=$id_contrato");

        if (db_num_rows($busca_contratos)!=0) {

                $datos_contrato=db_fetch_array($busca_contratos);
                $id_docente=$datos_contrato["id_empleado"];

                $id_categoria=$datos_contrato["id_categoria"];
                $tipo_contrato=$datos_contrato["tipo_contrato"];
                $salario_base=$datos_contrato["salario_base"];
                $observaciones=$datos_contrato["observaciones"];
                if (strlen($observaciones)!=0) {
                        $observaciones=stripslashes($observaciones);
                        }
                        // Fechas
                        if ($datos_contrato["fecha_inicio"]=="0000-00-00") {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                } else {
                                $fecha_inicio_dia=date("j",strtotime($datos_contrato["fecha_inicio"]));
                                $fecha_inicio_mes=date("m",strtotime($datos_contrato["fecha_inicio"]));
                                $fecha_inicio_ano=date("Y",strtotime($datos_contrato["fecha_inicio"]));
                                }

                        if ($datos_contrato["fecha_fin"]=="0000-00-00") {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                } else {
                                $fecha_fin_dia=date("j",strtotime($datos_contrato["fecha_fin"]));
                                $fecha_fin_mes=date("m",strtotime($datos_contrato["fecha_fin"]));
                                $fecha_fin_ano=date("Y",strtotime($datos_contrato["fecha_fin"]));
                                }


                }

        }

if (strlen($id_contrato)==0) {
        $id_contrato=0;
        }

if (strlen($id_docente)!=0) {

        $busca_datos_docente=db_query("SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=$id_docente AND horario_docentes.id_centro=$centro_actual") or die(db_error());

        $datos_docente=db_fetch_array($busca_datos_docente);
        $id_horario=$datos_docente["id_horario"];
                $nombre_docente=$datos_docente["Nombre"]." ".$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"];
                $nombre_docente=ucwords(strtolower($nombre_docente));

        }

$url_retorno="horario_docente.php?id_docente=$id_docente";

$busca_tipos_de_pago=db_query("SELECT * FROM personal_tipos_pagos ORDER BY id_tipo_pago");

$nombres_tipos_pago=array();

while($tipos_de_pago=db_fetch_array($busca_tipos_de_pago)){
        $nombres_tipos_pago[$tipos_de_pago["id_tipo_pago"]]=$tipos_de_pago["nombre_tipo_pago"];
        }

$busca_tipos_de_cuenta=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

$nombres_cuentas=array();

while($tipos_de_cuenta=db_fetch_array($busca_tipos_de_cuenta)){
        $nombres_cuentas[$tipos_de_cuenta["id_cuenta"]]=$tipos_de_cuenta["nombre_cuenta"];
        }

?>
<script language="Javascript">

function borrar_pago(id_pago,nombre_pago) {

        if (confirm("Vas a eliminar el pago " + nombre_pago + ".\n\n¿Estás seguro/a?")) {
                window.location.href="borrar_pago.php?id_contrato=<?=$id_contrato?>&id_docente=<?=$id_docente?>&id_pago=" + id_pago;
                }

        }

</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_contrato" value="<?=$id_contrato;?>">
<input type="hidden" name="id_docente" value="<?=$id_docente;?>">
<input type="hidden" name="o" value="<?=$o;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Resumen económico del contrato</b><br><br>
                </font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=4 face="<?= $fuente_general; ?>"><b><?=$nombre_docente?></b></font></td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Categoría</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2">
                                <?php 
                                $busca_tipos_cat=db_query("SELECT * FROM personal_cat_laborales WHERE id_categoria=$id_categoria");
                                
                                $tipos_cat=db_fetch_array($busca_tipos_cat);
                                ?>
                                <?=$tipos_cat["nombre_categoria"]?></font></td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Tipo de contrato</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2">
                                <?php 
                                $busca_tipos_contrato=db_query("SELECT * FROM personal_tipos_contrato WHERE id_tipo_contrato=$tipo_contrato");

                                $tipos_contrato=db_fetch_array($busca_tipos_contrato);
                                ?>
                                <?=$tipos_contrato["nombre_tipo_contrato"]?></font>
                                </td>
                        </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha alta</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($datos_contrato["fecha_inicio"]));?></font>
                                </td>
                                </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha baja</b></font></td>
                                <td><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($datos_contrato["fecha_fin"]));?></font>
                                </td>
                                </tr>
                        <tr>
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Sal. Neto mensual</b></font></td>
                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($salario_base,2,",",".");?>  &#8364;</font>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Observaciones</b></font></td>
                                <td><font size=2><?=$observaciones?></font>
                                </td>
                        </tr>
                        </table><br>
                        <input type="button" value="Nuevo pago" onclick="window.location.href='nuevo_pago.php?id_contrato=<?=$id_contrato?>&id_docente=<?=$id_docente?>'" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"><br><br>
                        <?php 
                        $anio_actual=0;
                        $mes_actual=0;
                        
                        $totales_anio=array();
                        $totales_mes=array();
                        
                        $totales_anio_cuentas=array();
                        $totales_mes_cuentas=array();
                        
                        $busca_gastos=db_query("SELECT * FROM personal_pagos WHERE id_contrato=$id_contrato ORDER BY fecha_pago;");
                        
                        $contador=0;
                        
                        while($gastos=db_fetch_array($busca_gastos)) {

                                if ($anio_actual!=date("Y",strtotime($gastos["fecha_pago"]))) {
                                
                                        if ($anio_actual!=0) {
                                                ?>
                                                <tr>
                                                <td align="right" colspan=2 bgcolor="#ffffff"><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total año <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_anio[$anio_actual][0],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                // Por cuentas de gastos
                                                $busca_tipos_de_cuenta=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

                                                while($tipos_de_cuenta=db_fetch_array($busca_tipos_de_cuenta)){
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total <?=$tipos_de_cuenta["nombre_cuenta"]?> <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_anio_cuentas[$anio_actual][$tipos_de_cuenta["id_cuenta"]],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                }
                                                
                                                // Por tipo de pago
                                                
                                                $busca_tipos_de_pago=db_query("SELECT * FROM personal_tipos_pagos ORDER BY id_tipo_pago");
                                                while($tipos_de_pago=db_fetch_array($busca_tipos_de_pago)){
                                                        ?>
                                                        <tr bgcolor="#ffffff">
                                                        <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Suma <?=$tipos_de_pago["nombre_tipo_pago"]?> año <?=$anio_actual?></b></font></td>
                                                        <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_anio[$anio_actual][$tipos_de_pago["id_tipo_pago"]],2,",",".");?>  &#8364;</font>
                                                        <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                        </tr>
                                                        <?php 
                                                        }
                                                ?>
                                                </table>
                                                <br>
                                                <?php 
                                                }
                                
                                        $anio_actual=date("Y",strtotime($gastos["fecha_pago"]));
                                        ?>
                                        <table width=100% border=1 bordercolor="#000000" style="bordercollapse: collapse" cellspacing=0 cellpadding=2>
                                        <tr bgcolor="#efefef">
                                        <td colspan=5>Año <?=$anio_actual?></td>
                                        </tr>
                                        <?php 
                                        }
                                        
                                if ($mes_actual!=date("n",strtotime($gastos["fecha_pago"]))) {

                                        if ($mes_actual!=0) {
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total <?=$nombre_mes[$mes_actual]?> de <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_mes[$mes_actual][0],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                // Por cuentas de gastos
                                                $busca_tipos_de_cuenta=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

                                                while($tipos_de_cuenta=db_fetch_array($busca_tipos_de_cuenta)){
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total <?=$tipos_de_cuenta["nombre_cuenta"]?> <?=$nombre_mes[$mes_actual]?> de <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_mes_cuentas[$mes_actual][$tipos_de_cuenta["id_cuenta"]],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                }
                                                
                                                // Por tipo de pago

                                                $busca_tipos_de_pago=db_query("SELECT * FROM personal_tipos_pagos ORDER BY id_tipo_pago");
                                                while($tipos_de_pago=db_fetch_array($busca_tipos_de_pago)){
                                                        ?>
                                                        <tr bgcolor="#ffffff">
                                                        <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Suma <?=$tipos_de_pago["nombre_tipo_pago"]?> <?=$nombre_mes[$mes_actual]?> de <?=$anio_actual?></b></font></td>
                                                        <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_mes[$mes_actual][$tipos_de_pago["id_tipo_pago"]],2,",",".");?>  &#8364;</font>
                                                        <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                        </tr>
                                                        <?php 
                                                        $totales_mes[$mes_actual][$gastos["tipo_pago"]]=0;
                                                        }
                                                $totales_mes[$mes_actual][0]=0;
                                                
                                                $busca_tipos_de_cuenta=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

                                                while($tipos_de_cuenta=db_fetch_array($busca_tipos_de_cuenta)){
                                                        $totales_mes_cuentas[$mes_actual][$tipos_de_cuenta["id_cuenta"]]=0;
                                                        }
                                                }

                                        $mes_actual=date("n",strtotime($gastos["fecha_pago"]));
                                        ?>
                                        <tr>
                                        <td colspan=5>&nbsp;</td>
                                        </tr>
                                        <tr bgcolor="#efefef">
                                        <td colspan=5><?=$nombre_mes[$mes_actual]?></td>
                                        </tr>
                                        <tr bgcolor="#ffffff">
                                                <td><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Tipo</b></font></td>
                                                <td><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Fecha</b></font></td>
                                                <td><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Importe</b></font></td>
                                                <td><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Cuenta</b></font></td>
                                                <td><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                        </tr>
                                        <?php 
                                        }
                                $nombre_pago="de ".number_format($gastos["importe"],2,",",".")." &#8364; realizado el día ".date("d/m/Y",strtotime($gastos["fecha_pago"]))." en concepto de ".$nombres_tipos_pago[$gastos["tipo_pago"]];
                                ?>
                                <tr bgcolor="#ffffff">
                                        <td><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=$nombres_tipos_pago[$gastos["tipo_pago"]];?></font></td>
                                        <td><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($gastos["fecha_pago"]))?></font></td>
                                        <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($gastos["importe"],2,",",".");?>  &#8364;</font>
                                        <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=$nombres_cuentas[$gastos["cuenta"]];?></font>
                                        <td align="center"><a title="Editar pago" href="nuevo_pago.php?id_contrato=<?=$id_contrato?>&id_docente=<?=$id_docente?>&id_pago=<?=$gastos["id_pago"];?>"><img src="editar.gif" border=0></a> <a title="Borrar pago" href="#" onclick="borrar_pago(<?=$gastos["id_pago"];?>,'<?=$nombre_pago?>');"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                $totales_mes_cuentas[$mes_actual][$gastos["cuenta"]]=$totales_mes_cuentas[$mes_actual][$gastos["cuenta"]]+$gastos["importe"];
                                $totales_anio_cuentas[$anio_actual][$gastos["cuenta"]]=$totales_anio_cuentas[$anio_actual][$gastos["cuenta"]]+$gastos["importe"];
                                
                                $totales_anio[$anio_actual][0]=$totales_anio[$anio_actual][0]+$gastos["importe"];
                                $totales_mes[$mes_actual][0]=$totales_mes[$mes_actual][0]+$gastos["importe"];
                                $totales_mes[$mes_actual][$gastos["tipo_pago"]]=$totales_mes[$mes_actual][$gastos["tipo_pago"]]+$gastos["importe"];
                                $totales_anio[$anio_actual][$gastos["tipo_pago"]]=$totales_anio[$anio_actual][$gastos["tipo_pago"]]+$gastos["importe"];
                                $contador++;
                                }

                        if ($contador!=0) {
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total <?=$nombre_mes[$mes_actual]?> de <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_mes[$mes_actual][0],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                // Por cuentas de gastos
                                                $busca_tipos_de_cuenta=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

                                                while($tipos_de_cuenta=db_fetch_array($busca_tipos_de_cuenta)){
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total <?=$tipos_de_cuenta["nombre_cuenta"]?> <?=$nombre_mes[$mes_actual]?> de <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_mes_cuentas[$mes_actual][$tipos_de_cuenta["id_cuenta"]],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                }
                                                
                                                // Por tipo de pago

                                                $busca_tipos_de_pago=db_query("SELECT * FROM personal_tipos_pagos ORDER BY id_tipo_pago");
                                                while($tipos_de_pago=db_fetch_array($busca_tipos_de_pago)){
                                                        ?>
                                                        <tr bgcolor="#ffffff">
                                                        <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Suma <?=$tipos_de_pago["nombre_tipo_pago"]?> <?=$nombre_mes[$mes_actual]?> de <?=$anio_actual?></b></font></td>
                                                        <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_mes[$mes_actual][$tipos_de_pago["id_tipo_pago"]],2,",",".");?>  &#8364;</font>
                                                        <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                        </tr>
                                                        <?php 
                                                        }
                                                ?>
                                                <tr>
                                                <td colspan=5>&nbsp;</td>
                                                </tr>
                                                <tr bgcolor="#ffffff">
                                                <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total año <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_anio[$anio_actual][0],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                // Por cuentas de gastos
                                                $busca_tipos_de_cuenta=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

                                                while($tipos_de_cuenta=db_fetch_array($busca_tipos_de_cuenta)){
                                                ?>
                                                <tr bgcolor="#ffffff">
                                                <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Coste total <?=$tipos_de_cuenta["nombre_cuenta"]?> <?=$anio_actual?></b></font></td>
                                                <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_anio_cuentas[$anio_actual][$tipos_de_cuenta["id_cuenta"]],2,",",".");?>  &#8364;</font>
                                                <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                </tr>
                                                <?php 
                                                }
                                                
                                                // Por tipo de pago

                                                $busca_tipos_de_pago=db_query("SELECT * FROM personal_tipos_pagos ORDER BY id_tipo_pago");
                                                while($tipos_de_pago=db_fetch_array($busca_tipos_de_pago)){
                                                        ?>
                                                        <tr bgcolor="#ffffff">
                                                        <td align="right" colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2"><b>Suma <?=$tipos_de_pago["nombre_tipo_pago"]?> año <?=$anio_actual?></b></font></td>
                                                        <td align="right"><font size=2><font face="<?= $fuente_general; ?>" size="2"><?=number_format($totales_anio[$anio_actual][$tipos_de_pago["id_tipo_pago"]],2,",",".");?>  &#8364;</font>
                                                        <td colspan=2><font size=2><font face="<?= $fuente_general; ?>" size="2">&nbsp;</font></td>
                                                        </tr>
                                                        <?php 
                                                        }
                                                ?>
                                                </table>
                                                <br>
                                                <?php 
                                }
                        
                        ?>
                        <input type="button" value="Nuevo pago" onclick="window.location.href='nuevo_pago.php?id_contrato=<?=$id_contrato?>&id_docente=<?=$id_docente?>'" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px">
                        <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
}
require("../../configuracion/parte_inferior.php"); ?>
