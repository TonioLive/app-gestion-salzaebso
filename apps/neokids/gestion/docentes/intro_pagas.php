<?php 
require("../../configuracion/parte_superior_privado_ancho.php");

if (($nivel_acceso==4) and ($ip_segura==true)) {

$rellenar_tabla=$_GET["rt"];

$fecha=strftime("%m/%d/%Y");

$fecha_pago_dia=date("j");
$fecha_pago_mes=date("n");
$fecha_pago_ano=date("Y");

$ancho_tabla=$ancho_pagina_total-185
?>
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="<%= $fondo_at%>" align="center">
                            <font size="3" color="#000000"><b>Introducción de nominas del personal de la escuela</font></b>
                        </td>
                </tr>
    </table><br>
    <input type="button" onclick="window.location.href='listado_docentes.php'" value="Cancelar" style="font-size: 10px"> <input type="button" onclick="window.location.href='intro_pagas.php?rt=1'" value="Rellenar con datos de la última nómina" style="font-size: 10px"> <input type="button" onclick="guardar()" value="Guardar cambios y salir" style="font-size: 10px"><br><br>
    
<table>
<form name="tabla" action="intro_pagas1.php" method="POST">
                        <tr>
                                <td align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de nómina</b></font></td>
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
                                                                      </select>
                                </td>
                                </tr>
</table>
<!--<font size=1><br>* Si el empleado ya tiene un pago en la fecha indicada, el pago actual sustituirá al pago existente.<br><br></font>-->
<table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%">
<?php 

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);
 
?>
  <tr bgcolor="<?=  $fondo_at; ?>">
        <td colspan=8>
        <b><font color="<?=  $color_fuente; ?>">Listado de personal de la escuela <i><?=$nombre_centro_actual;?></i></b>
        </td>
  </tr>
<?php 
  $consulta_usuarios=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=? OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1', 's', array($centro_actual)) or die(db_error());

  mt_srand((float)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  $fila=0;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
  
  $codigo=$rsslc["id_usuario"];
  $servicio_comun=$rsslc["servicio_comun"];
  
  // Comprueba si tiene contrato en vigor
  $fecha_control=date("Y-m-d");
  $fecha_control1=date("Y-m-d",(time()-(86400*31)));
  $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_fin>=?) OR (fecha_inicio<=? AND tipo_contrato=2 AND fecha_fin=\'0000-00-00\')) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control1), (string)($fecha_control)));
  
  if (db_num_rows($busca_contratos)!=0) {
  
  $datos_contrato=db_fetch_array($busca_contratos);
  
  $id_contrato=$datos_contrato["id_contrato"];
  
          $tot_liquido=0;
          $ss_empresa=0;
          $ss_trabajador=0;
          $ret_irpf=0;
          $bonificacion_ss=0;

  if (($rellenar_tabla==1) and ($servicio_comun==0)) {

          // Busca ultimo
          $busca=db_query_params('SELECT * FROM personal_pagos WHERE id_empleado=? AND tipo_pago=1 ORDER BY fecha_pago DESC', 's', array($codigo));
          if (db_num_rows($busca)!=0) {
                $datos_busca=db_fetch_array($busca);
                $tot_liquido=$datos_busca["importe"];
                }
                
          // Busca ultimo
          $busca=db_query_params('SELECT * FROM personal_pagos WHERE id_empleado=? AND tipo_pago=2 ORDER BY fecha_pago DESC', 's', array($codigo));
          if (db_num_rows($busca)!=0) {
                $datos_busca=db_fetch_array($busca);
                $ss_empresa=$datos_busca["importe"];
                }

          // Busca ultimo
          $busca=db_query_params('SELECT * FROM personal_pagos WHERE id_empleado=? AND tipo_pago=3 ORDER BY fecha_pago DESC', 's', array($codigo));
          if (db_num_rows($busca)!=0) {
                $datos_busca=db_fetch_array($busca);
                $ss_trabajador=$datos_busca["importe"];
                }

          // Busca ultimo
          $busca=db_query_params('SELECT * FROM personal_pagos WHERE id_empleado=? AND tipo_pago=4 ORDER BY fecha_pago DESC', 's', array($codigo));
          if (db_num_rows($busca)!=0) {
                $datos_busca=db_fetch_array($busca);
                $ret_irpf=$datos_busca["importe"];
                }

          // Busca ultimo
          $busca=db_query_params('SELECT * FROM personal_pagos WHERE id_empleado=? AND tipo_pago=6 ORDER BY fecha_pago DESC', 's', array($codigo));
          if (db_num_rows($busca)!=0) {
                $datos_busca=db_fetch_array($busca);
                $bonificacion_ss=$datos_busca["importe"];
                }

  
          }

  if ($id_tipo_listado!=$rsslc["nivel_acceso"]) {
  ?>
  <tr bgcolor="#dfdfdf">
    <td colspan=8>
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
    
?>
        <input type="hidden" name="id_empleado_<?=$fila;?>" value="<?=$codigo?>">
        <input type="hidden" name="id_contrato_<?=$fila;?>" value="<?=$id_contrato?>">
        <input type="hidden" name="nombre_fila_<?=$fila;?>" value="<?=ucwords(strtolower($nombre_ins))?>">
        <td align="center"><input onblur="comprueba_cantidades(<?=$fila;?>)" type="text" name="salario_base_<?=$fila;?>" value="<?=$tot_liquido;?>" style="font-size: 10px; text-align: right" size=8><select name="tp1_<?=$fila;?>" size=1 style="font-size: 10px">
        <?php 
$busca_tipos_de_cuenta=db_query("SELECT * FROM personal_tipos_cuentas_pago ORDER BY id_cuenta");

$nombres_cuentas=array();

$numero_de_cuentas=db_num_rows($busca_tipos_de_cuenta);

$contador_cuentas=0;

while($tipos_de_cuenta=db_fetch_array($busca_tipos_de_cuenta)){
        $nombres_cuentas[$contador_cuentas][0]=$tipos_de_cuenta["id_cuenta"];
        $nombres_cuentas[$contador_cuentas][1]=$tipos_de_cuenta["nombre_cuenta"];
        ?>
        <option value="<?=$tipos_de_cuenta["id_cuenta"]?>"><?=$tipos_de_cuenta["nombre_cuenta"]?></option>
        <?php 
        $contador_cuentas++;
        }
        ?>
        </select></td>
        <td align="center"><input onblur="comprueba_cantidades(<?=$fila;?>)" type="text" name="prorrateo_<?=$fila;?>" value="<?=$ss_empresa?>" style="font-size: 10px; text-align: right" size=8><select name="tp2_<?=$fila;?>" size=1 style="font-size: 10px"><?php 
        for ($contador_cuentas=0;$contador_cuentas<$numero_de_cuentas;$contador_cuentas++) {
        ?>
        <option value="<?=$nombres_cuentas[$contador_cuentas][0]?>"><?=$nombres_cuentas[$contador_cuentas][1]?></option>
        <?php 
        }
        ?></select></td>
        <td align="center"><input onblur="comprueba_cantidades(<?=$fila;?>)" type="text" name="extras_<?=$fila;?>" value="<?=$ss_trabajador?>" style="font-size: 10px; text-align: right" size=8><select name="tp3_<?=$fila;?>" size=1 style="font-size: 10px"><?php 
        for ($contador_cuentas=0;$contador_cuentas<$numero_de_cuentas;$contador_cuentas++) {
        ?>
        <option value="<?=$nombres_cuentas[$contador_cuentas][0]?>"><?=$nombres_cuentas[$contador_cuentas][1]?></option>
        <?php 
        }
        ?></select></td>
        <td align="center"><input onblur="comprueba_cantidades(<?=$fila;?>)" type="text" name="irpf_<?=$fila;?>" value="<?=$ret_irpf?>" style="font-size: 10px; text-align: right" size=8><select name="tp4_<?=$fila;?>" size=1 style="font-size: 10px"><?php 
        for ($contador_cuentas=0;$contador_cuentas<$numero_de_cuentas;$contador_cuentas++) {
        ?>
        <option value="<?=$nombres_cuentas[$contador_cuentas][0]?>"><?=$nombres_cuentas[$contador_cuentas][1]?></option>
        <?php 
        }
        ?></select></td>
        <td align="center"><input onblur="comprueba_cantidades(<?=$fila;?>)" type="text" name="ss_<?=$fila;?>" value="0" style="font-size: 10px; text-align: right" size=8><select name="tp5_<?=$fila;?>" size=1 style="font-size: 10px"><?php 
        for ($contador_cuentas=0;$contador_cuentas<$numero_de_cuentas;$contador_cuentas++) {
        ?>
        <option value="<?=$nombres_cuentas[$contador_cuentas][0]?>"<?php  if ($nombres_cuentas[$contador_cuentas][0]==2) { print " selected"; } ?>><?=$nombres_cuentas[$contador_cuentas][1]?></option>
        <?php 
        }
        ?></select></td>
        <td align="center"><input onblur="comprueba_cantidades(<?=$fila;?>)" type="text" name="boni_<?=$fila;?>" value="<?=$bonificacion_ss?>" style="font-size: 10px; text-align: right" size=8><select name="tp6_<?=$fila;?>" size=1 style="font-size: 10px"><?php 
        for ($contador_cuentas=0;$contador_cuentas<$numero_de_cuentas;$contador_cuentas++) {
        ?>
        <option value="<?=$nombres_cuentas[$contador_cuentas][0]?>"><?=$nombres_cuentas[$contador_cuentas][1]?></option>
        <?php 
        }
        ?></select></td>
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
</form>
</table><br>
</center></div></div>
</font>
</small>
</small>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

function comprueba_cantidades(fila) {
        var errores=0;
        var mensajes_error="";

        salario_base=eval("self.document.tabla.salario_base_"+fila+".value");
        extras=eval("self.document.tabla.extras_"+fila+".value");
        irpf=eval("self.document.tabla.irpf_"+fila+".value");
        ss=eval("self.document.tabla.ss_"+fila+".value");
        prorrateo=eval("self.document.tabla.prorrateo_"+fila+".value");
        bonificacion=eval("self.document.tabla.boni_"+fila+".value");
        nombre_fila=eval("self.document.tabla.nombre_fila_"+fila+".value");
        
        if (salario_base!="") {
                        salario_base=salario_base.replace(",",".");
                        eval("self.document.tabla.salario_base_"+fila+".value='"+salario_base+"'");
                        if (isNaN(salario_base)) {
                                mensajes_error=mensajes_error+"El total líquido de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (extras!="") {
                        extras=extras.replace(",",".");
                        eval("self.document.tabla.extras_"+fila+".value='"+extras+"'");
                        if (isNaN(extras)) {
                                mensajes_error=mensajes_error+"La S.S. de la Empresa de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (irpf!="") {
                        irpf=irpf.replace(",",".");
                        eval("self.document.tabla.irpf_"+fila+".value='"+irpf+"'");
                        if (isNaN(irpf)) {
                                mensajes_error=mensajes_error+"La S.S. del Trabajador de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (ss!="") {
                        ss=ss.replace(",",".");
                        eval("self.document.tabla.ss_"+fila+".value='"+ss+"'");
                        if (isNaN(ss)) {
                                mensajes_error=mensajes_error+"La Ret. IRPF de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (prorrateo!="") {
                        prorrateo=prorrateo.replace(",",".");
                        eval("self.document.tabla.prorrateo_"+fila+".value='"+prorrateo+"'");
                        if (isNaN(prorrateo)) {
                                mensajes_error=mensajes_error+"Los extras de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (bonificacion!="") {
                        prorrateo=prorrateo.replace(",",".");
                        eval("self.document.tabla.prorrateo_"+fila+".value='"+prorrateo+"'");
                        if (isNaN(prorrateo)) {
                                mensajes_error=mensajes_error+"La bonificación de la S.S. de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
                        

        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                }
        }

function guardar() {
        <?php 
        if ($fila==0) {
        ?>
        alert("No hay empleados con contrato en vigor.");
        <?php 
        } else {
        ?>

        var errores=0;
        var mensajes_error="";

        la_fecha1=self.document.tabla.fecha_pago_mes.value+"/"+self.document.tabla.fecha_pago_dia.value+"/"+self.document.tabla.fecha_pago_ano.value
        if (isDate(la_fecha1)!=true) { mensajes_error=mensajes_error+"La fecha de pago introducida no es correcta.\u000A";errores=1; }

        for (fila=0;fila<<?=$fila;?>;fila++) {

        salario_base=eval("self.document.tabla.salario_base_"+fila+".value");
        extras=eval("self.document.tabla.extras_"+fila+".value");
        irpf=eval("self.document.tabla.irpf_"+fila+".value");
        ss=eval("self.document.tabla.ss_"+fila+".value");
        prorrateo=eval("self.document.tabla.prorrateo_"+fila+".value");
        bonificacion=eval("self.document.tabla.boni_"+fila+".value");
        nombre_fila=eval("self.document.tabla.nombre_fila_"+fila+".value");

        if (salario_base!="") {
                        salario_base=salario_base.replace(",",".");
                        eval("self.document.tabla.salario_base_"+fila+".value='"+salario_base+"'");
                        if (isNaN(salario_base)) {
                                mensajes_error=mensajes_error+"El total líquido de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (extras!="") {
                        extras=extras.replace(",",".");
                        eval("self.document.tabla.extras_"+fila+".value='"+extras+"'");
                        if (isNaN(extras)) {
                                mensajes_error=mensajes_error+"La S.S. de la Empresa de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (irpf!="") {
                        irpf=irpf.replace(",",".");
                        eval("self.document.tabla.irpf_"+fila+".value='"+irpf+"'");
                        if (isNaN(irpf)) {
                                mensajes_error=mensajes_error+"La S.S. del Trabajador de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (ss!="") {
                        ss=ss.replace(",",".");
                        eval("self.document.tabla.ss_"+fila+".value='"+ss+"'");
                        if (isNaN(ss)) {
                                mensajes_error=mensajes_error+"La Ret. IRPF de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (prorrateo!="") {
                        prorrateo=prorrateo.replace(",",".");
                        eval("self.document.tabla.prorrateo_"+fila+".value='"+prorrateo+"'");
                        if (isNaN(prorrateo)) {
                                mensajes_error=mensajes_error+"Los extras de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }
        if (bonificacion!="") {
                        prorrateo=prorrateo.replace(",",".");
                        eval("self.document.tabla.prorrateo_"+fila+".value='"+prorrateo+"'");
                        if (isNaN(prorrateo)) {
                                mensajes_error=mensajes_error+"La bonificación de la S.S. de '"+nombre_fila+"' debe ser un valor numérico.\u000A";
                                errores=1;
                                }
                        }

        }

        if (errores==1) {
                alert("Error:\n\n"+mensajes_error);
                } else {
                document.tabla.submit();
                }
        <?php 
        }
        ?>
        }

</script>
<?php 

}

require("../../configuracion/parte_inferior.php"); ?>

