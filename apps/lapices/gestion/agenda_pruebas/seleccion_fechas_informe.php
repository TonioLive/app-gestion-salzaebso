<?php  require("../../configuracion/parte_superior_docente.php");

$id_aula=$_GET["id_aula"];

?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">
function comprobar_y_enviar() {
        var errores="";
        
        la_fecha1=self.document.albaranes.mes_i.value+"/"+self.document.albaranes.dia_i.value+"/"+self.document.albaranes.ano_i.value
        if (isDate(la_fecha1)!=true) { errores=errores+"La fecha de inicio introducida no es correcta.\n"; }
        
        la_fecha2=self.document.albaranes.mes_f.value+"/"+self.document.albaranes.dia_f.value+"/"+self.document.albaranes.ano_f.value
        if (isDate(la_fecha2)!=true) { errores=errores+"La fecha de finalización introducida no es correcta.\n"; }


        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.albaranes.submit();
                }
}

</script>
<br><br><br>
<table width="75%" border=0 cellpadding=0 cellspacing=0>
<form name="albaranes" action="informe_evaluacion_periodo.php" method="POST">
<input type="hidden" name="id_aula" value="<?=$id_aula?>">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Informe de evaluación</b><br><small>Selecciona las fechas de inicio y de fin del periodo y se generará el informe de evaluación con todos los objetivos y actividades trabajados entre las dos fechas (incluidos los trabajados en esas mismas fechas).</small></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Desde:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="dia_i" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{

  if ($c_dia<10)
  {

    $dia_a="0".$c_dia;
  }
    else
  {

    $dia_a=$c_dia;
  }

?>
    <option value="<?php    echo $dia_a; ?>"<?php if ($dia_a=="01") { print " selected"; } ?>><?php    echo $dia_a; ?></option>
    <?php 

}

?>
        </select>/<select name="mes_i" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{

  if ($c_mes<10)
  {

    $mes_a="0".$c_mes;
  }
    else
  {

    $mes_a=$c_mes;
  }

?>
    <option value="<?php    echo $mes_a; ?>"<?php if ($mes_a==strftime("%m")) { print " selected"; } ?>><?php    echo $mes_a; ?></option>
    <?php 

}

?>
        </select>/<select name="ano_i" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php 
for ($c_ano=strftime("%Y")-1; $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
{

  if ($c_ano<10)
  {

    $ano_a="0".$c_ano;
  }
    else
  {

    $ano_a=$c_ano;
  }

?>
    <option value="<?php    echo $ano_a; ?>"<?php if ($c_ano==strftime("%Y")) { print " selected"; } ?>><?php    echo $ano_a; ?></option>
    <?php 

}

?>
</select></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Hasta:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="dia_f" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{

  if ($c_dia<10)
  {

    $dia_a="0".$c_dia;
  }
    else
  {

    $dia_a=$c_dia;
  }

?>
    <option value="<?php    echo $dia_a; ?>"<?php if ($dia_a==strftime("%d")) { print " selected"; } ?>><?php    echo $dia_a; ?></option>
    <?php 

}

?>
        </select>/<select name="mes_f" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{

  if ($c_mes<10)
  {

    $mes_a="0".$c_mes;
  }
    else
  {

    $mes_a=$c_mes;
  }

?>
    <option value="<?php    echo $mes_a; ?>"<?php if ($mes_a==strftime("%m")) { print " selected"; } ?>><?php    echo $mes_a; ?></option>
    <?php 

}

?>
        </select>/<select name="ano_f" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php 
for ($c_ano=strftime("%Y")-1; $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
{

  if ($c_ano<10)
  {

    $ano_a="0".$c_ano;
  }
    else
  {

    $ano_a=$c_ano;
  }

?>
    <option value="<?php    echo $ano_a; ?>"<?php if ($c_ano==strftime("%Y")) { print " selected"; } ?>><?php    echo $ano_a; ?></option>
    <?php 

}

?>
</select></font></td>
                        </tr>                        
                         </table>
                         <input type="button" value="Cancelar" onclick="window.close();" style="font-size: 10px"> <input type="button" value="Obtener Informe >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
