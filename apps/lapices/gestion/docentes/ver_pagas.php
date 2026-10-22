<?php 
require("../../configuracion/parte_superior_privado_ancho.php");
$ip_segura=true;
if (($nivel_acceso==4) and ($ip_segura==true)) {

$id_centro_informe=$_GET["id_centro_informe"];

if ($id_centro_informe==0) {
        $id_centro_informe=$centro_actual;
        }

$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_informe") or die(db_error());
$datos_centro_informe=db_fetch_array($busca_datos_centro);
$nombre_centro_mostrado=$datos_centro_informe["nombre_centro"];


$fecha_pago_dia=$_GET["fecha_pago_dia"];
$fecha_pago_mes=$_GET["fecha_pago_mes"];
$fecha_pago_ano=$_GET["fecha_pago_ano"];
$fecha_pago_completa=$fecha_pago_ano."-".$fecha_pago_mes."-".$fecha_pago_dia;

if (strlen($fecha_pago_completa)==2) {
        $fecha_pago_dia=date("d");
        $fecha_pago_mes=date("m");
        $fecha_pago_ano=date("Y");
        $fecha_pago_completa=$fecha_pago_ano."-".$fecha_pago_mes."-".$fecha_pago_dia;
        }

$fecha_pago_completa=$fecha_pago_ano."-".$fecha_pago_mes."-".$fecha_pago_dia;

$ancho_tabla=$ancho_pagina_total-185;

$formulario_name="tabla";
?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

function isValidDate(date) {

        if (!/(0[1-9]|1[0-9]|2[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/([1-2][0-9]{3})/g.test(date)) {
                console.log('Incorrect date format please follow this form: dd/mm/yyyy');
                return false;
        } else {
                // secondary validation
                const parts = (date).split('/').map((p) => parseInt(p, 10));
                let day = Number(parts[0]);
                let month = Number(parts[1]) - 1; // 0-indexed month
                let year = Number(parts[2]);
                let d = new Date(year, month, day);
                if (!(d.getFullYear() == year && d.getMonth() == month && d.getDate() == day)) {
                        console.log('Incorrect date, please enter the correct day entry');
                        return false;
                } else {
                        return true;
                }
        }
}

function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";

                var la_fecha=self.document.<?=$formulario_name;?>.fecha_pago_ano.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_dia.value;

                console.log(la_fecha);

                if (la_fecha!="----/--/--") {
                        if (isValidDate(self.document.<?=$formulario_name;?>.fecha_pago_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_pago_ano.value)==false) {
                                mensajes_error=mensajes_error+"La fecha del pago introducida no es correcta.\u000A";errores=1;
                        }
                } else {
                        mensajes_error=mensajes_error+"Debes introducir la fecha del pago.\u000A";
                        errores=1;
                }

                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }

function selecciona_fecha(nombre_campo_presel) {
                // Comprueba fecha

                mes_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_ano.value;");

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

         window.open('/herramientas/calendario_mes_pagos.php?dia='+fecha_preseleccionada+'&formulario_destino=<?=$formulario_name;?>&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=246');
        }
</script>
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="<%= $fondo_at%>" align="center">
                            <font size="3" color="#000000"><b>Listado de nominas del personal de la escuela</font></b>
                        </td>
                </tr>
    </table><br>
    <input type="button" onclick="window.location.href='listado_docentes.php'" value="Salir" style="font-size: 10px"><br><br>
    
<table>
<form name="tabla" action="ver_pagas.php" method="GET">
<input type="hidden" name="id_centro_informe" value="<?=$id_centro_informe?>">
                        <tr>
                                <td align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de la nómina</b></font></td>
                                <td><select name="fecha_pago_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_pago_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_pago_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_pago_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_pago_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=2005; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_pago_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('fecha_pago');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a> <input type="button" onclick="comprobar_y_enviar();" value="Ver" style="font-size: 10px">
                                </td>
                                </tr>
        </form>
</table>
<table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%">
<?php 

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);
 
?>
  <tr bgcolor="<?=  $fondo_at; ?>">
        <td colspan=11>
        <b><font color="<?=  $color_fuente; ?>">Listado de personal de la escuela <i><?=$nombre_centro_mostrado;?></i></b>
        </td>
  </tr>
<?php 
  $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=$id_centro_informe OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1") or die(db_error());

  mt_srand((double)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  $total_por_tipo=0;
  $fila=0;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
  
  $codigo=$rsslc["id_usuario"];
  
  // Comprueba si tiene contrato en vigor
  // $fecha_control=date("Y-m-d");
  $fecha_control=$fecha_pago_completa;
  
  $consultaContratos = "SELECT * FROM personal_contratos WHERE id_empleado=$codigo AND ((fecha_inicio<='$fecha_control' AND fecha_fin>='$fecha_control') OR (fecha_fin>='$fecha_control1') OR (fecha_inicio<='$fecha_control' AND (tipo_contrato=2 OR tipo_contrato=6) AND fecha_fin='0000-00-00')) ORDER BY fecha_inicio DESC";  
  
  // $consultaContratos = "SELECT * FROM personal_contratos WHERE id_empleado=$codigo AND ((fecha_inicio<='$fecha_control' AND fecha_fin>='$fecha_control') OR (fecha_inicio<='$fecha_control' AND tipo_contrato=2 AND fecha_fin='0000-00-00')) ORDER BY fecha_inicio DESC"
  
  $busca_contratos=db_query($consultaContratos);
  

  if (db_num_rows($busca_contratos)!=0) {
  
  $datos_contrato=db_fetch_array($busca_contratos);
  
  $id_contrato=$datos_contrato["id_contrato"];

          $tot_liquido=0;
          $ss_empresa=0;
          $ss_trabajador=0;
          $ret_irpf=0;
          $extras=0;
          $bonificacion_ss=0;

          // Busca ultimo
          $busca=db_query("SELECT * FROM personal_pagos WHERE id_empleado=$codigo AND tipo_pago=1 AND fecha_pago='$fecha_pago_completa'");
                while($datos_busca=db_fetch_array($busca)) {
                        $tot_liquido=$tot_liquido+$datos_busca["importe"];
                        }
                
          // Busca ultimo
          $busca=db_query("SELECT * FROM personal_pagos WHERE id_empleado=$codigo AND tipo_pago=2 AND fecha_pago='$fecha_pago_completa'");
                while($datos_busca=db_fetch_array($busca)) {
                        $ss_empresa=$ss_empresa+$datos_busca["importe"];
                        }

          // Busca ultimo
          $busca=db_query("SELECT * FROM personal_pagos WHERE id_empleado=$codigo AND tipo_pago=3 AND fecha_pago='$fecha_pago_completa'");
                while($datos_busca=db_fetch_array($busca)) {
                        $ss_trabajador=$ss_trabajador+$datos_busca["importe"];
                        }

          // Busca ultimo
          $busca=db_query("SELECT * FROM personal_pagos WHERE id_empleado=$codigo AND tipo_pago=4 AND fecha_pago='$fecha_pago_completa'");
                while($datos_busca=db_fetch_array($busca)) {
                        $ret_irpf=$ret_irpf+$datos_busca["importe"];
                        }
                
          // Busca ultimo
          $busca=db_query("SELECT * FROM personal_pagos WHERE id_empleado=$codigo AND tipo_pago=5 AND fecha_pago='$fecha_pago_completa'");
                while($datos_busca=db_fetch_array($busca)) {
                        $extras=$extras+$datos_busca["importe"];
                        }

          // Busca ultimo
          $busca=db_query("SELECT * FROM personal_pagos WHERE id_empleado=$codigo AND tipo_pago=6 AND fecha_pago='$fecha_pago_completa'");
                while($datos_busca=db_fetch_array($busca)) {
                        $bonificacion_ss=$bonificacion_ss+$datos_busca["importe"];
                        }
  
  if ($id_tipo_listado!=$rsslc["nivel_acceso"]) {
  if ($id_tipo_listado!=-1) {
  ?>
<!--  <tr bgcolor="#dfdfdf">
    <td colspan=9>
        <b><font color="<?=  $color_fuente; ?>">Total</b>
    </td>
        <td align="right"><font size=2><?=number_format($total_por_tipo,2,",",".");?>  &#8364;</font></td>
    <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">&nbsp;</b></small></small></td>
  </tr>  -->
  <?php 
  }
  $total_por_tipo=0;
  ?>
  <tr bgcolor="#dfdfdf">
    <td colspan=11>
        <b><font color="<?=  $color_fuente; ?>"><?=$tipo_usuario[$rsslc["nivel_acceso"]];?></b>
    </td>
    </tr>
    <tr bgcolor="#dfdfdf">
        <td><small><small><b><font color="<?=  $color_fuente; ?>">ID</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Nombre</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Tot. líquido</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">S.S. Empresa</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">S.S: Trabajador</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Ret. IRPF</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Extras</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Bonificación S.S.</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Coste T. Nomina</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Coste total</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">&nbsp;</b></small></small></td>

  </tr>
  <?php 
  $id_tipo_listado=$rsslc["nivel_acceso"];
        }
  
    $nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
    
    $contador=$contador+1;

    $t="";
    $t1=$rsslc["telefono1"];
    $t2=$rsslc["telefono2"];
    $t3=$rsslc["movil"];

    if (strlen($t1)>0)
    {
      $t=$t.$t1.", ";
    } 
    if (strlen($t2)>0)
    {
      $t=$t.$t2.", ";
    } 
    if (strlen($t3)>0)
    {
      $t=$t.$t3.", ";
    } 

    if (strlen($t)>0)
    {
      $t=substr($t,0,strlen($t)-2);
    } 

    print "<tr>";
    print "<td><small><small>".$codigo."</small></small></td>";
    print "<td><small><small>";
    print "<em>".ucwords(strtolower($nombre_ins))."</em></small></small></td>";

    $total_coste_A=$tot_liquido+$ss_empresa+$ss_trabajador+$ret_irpf+$bonificacion_ss;
    
    $total_coste=$tot_liquido+$ss_empresa+$ss_trabajador+$ret_irpf+$extras+$bonificacion_ss;
    
    $total_por_tipo=$total_por_tipo+$tot_liquido+$extras;
    
?>
        <input type="hidden" name="id_empleado_<?=$fila;?>" value="<?=$codigo?>">
        <input type="hidden" name="id_contrato_<?=$fila;?>" value="<?=$id_contrato?>">
        <input type="hidden" name="nombre_fila_<?=$fila;?>" value="<?=ucwords(strtolower($nombre_ins))?>">
        <td align="right"><font size=2><?=number_format($tot_liquido,2,",",".");?>  &#8364;</font></td>
        <td align="right"><font size=2><?=number_format($ss_empresa,2,",",".");?>  &#8364;</font></td>
        <td align="right"><font size=2><?=number_format($ss_trabajador,2,",",".");?>  &#8364;</font></td>
        <td align="right"><font size=2><?=number_format($ret_irpf,2,",",".");?>  &#8364;</font></td>
        <td align="right"><font size=2><?=number_format($extras,2,",",".");?>  &#8364;</font></td>
        <td align="right"><font size=2><?=number_format($bonificacion_ss,2,",",".");?>  &#8364;</font></td>
        <td align="right"><font size=2><?=number_format($total_coste_A,2,",",".");?>  &#8364;</font></td>
        <td align="right"><font size=2><?=number_format($total_coste,2,",",".");?>  &#8364;</font></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>"><a href="eliminar_linea.php?id=<?=$codigo?>&dia=<?=$fecha_pago_completa?>"><img src="borrar.gif" border=0></a></b></small></small></td>
<?php 
    print "</tr>";
    $fila++;
    
   }
}

  if ($contador==0)
  {
?>
  <tr bgcolor="#ffffff">
        <td colspan=7 align="center"><small>
        <font face="<?= $fuente_general; ?>" size=3 color="<?= $color_fuente; ?>">
        <b>Actualmente no hay ningún docente con contrato en vigor.</b>
        </font>
        </small>
        </td>
  </tr>        
        <?php  
  } 

db_close();
?>
<!--  <tr bgcolor="#dfdfdf">
    <td colspan=9>
        <b><font color="<?=  $color_fuente; ?>">Total</b>
    </td>
        <td align="right"><font size=2><?=number_format($total_por_tipo,2,",",".");?>  &#8364;</font></td>
    <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">&nbsp;</b></small></small></td>
  </tr>  -->
</table><br>
</center></div></div>
</font>
</small>
</small>
<?php 

}

require("../../configuracion/parte_inferior.php"); ?>

