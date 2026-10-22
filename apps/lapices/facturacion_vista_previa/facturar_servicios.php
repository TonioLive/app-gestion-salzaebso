<?php  require("../configuracion/parte_superior_privado.php");

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        }

?>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script language="Javascript">
function comprobar_y_enviar() {
        var errores="";

        la_fecha1=self.document.albaranes.mes.value+"/"+self.document.albaranes.dia.value+"/"+self.document.albaranes.ano.value
        if (isDate(la_fecha1)!=true) { errores=errores+"La fecha introducida no es correcta.\n"; }

        if (self.document.albaranes.id_cliente.value=="") {
                errores=errores+"Falta seleccionar a quien se emiten las facturas.\n";
                }

        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.albaranes.submit();
                }
}

function comprueba() {
        if (self.document.albaranes.id_cliente.value=="0") {
                self.document.albaranes.tipo_factura[0].disabled=true;
                self.document.albaranes.tipo_factura[1].click();
                } else {
                if (isNaN(self.document.albaranes.id_cliente.value)) {
                        self.document.albaranes.tipo_factura[0].disabled=true;
                        self.document.albaranes.tipo_factura[1].click();
                        } else {
                        self.document.albaranes.tipo_factura[0].disabled=false;
                        }
                }
}

</script>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <option value="0">Todos</option>
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table width="99%" border=0 cellpadding=0 cellspacing=0>
<form name="albaranes" action="facturar_servicios1.php" method="POST">
<input type="hidden" name="id_curso_actual" value="<?=$id_curso_actual?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Facturación de servicios</b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Alumno:</b></font></td>
                                <td><select size=1 style="font-size: 10px" name="id_cliente" onchange="comprueba();">
                                <option value="">Selecciona el alumno</option>
                                <option value="">----------------------------</option>
                                <option value="0">Todos los alumnos del centro</option>
                                <option value="">----------------------------</option>
                                <?php 
                                
        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                $filtro1=" AND alumnos.id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                $filtro1="";
                                }
                                
                                $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo=0 AND id_centro=$centro_actual$filtro ORDER BY id_curso,nombre;") or die(db_error());
                                while($aulas=db_fetch_array($consulta_aulas)){
                                ?>
                                <option value="A<?=$aulas["id_aula"];?>">Todos los alumnos del <?=$aulas["nombre"];?></option>
                                <?php 
                                }
                                ?>
                                <option value="">----------------------------</option>
                                <?php 
                                $num_clientes_con_servicios_pend=0;
                                $consulta_clientes = db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario$filtro AND alumnos.id_centro=$centro_actual ORDER BY Apellido1, Apellido2, Nombre") or die (db_error());
                                while($datos_clientes = db_fetch_array($consulta_clientes)){
                                $nombre_mostrado=$datos_clientes["Apellido1"]." ".$datos_clientes["Apellido2"].", ".$datos_clientes["Nombre"];
                                ?>
                                <option value="<?=$datos_clientes["id_alumno"];?>"><?=$nombre_mostrado;?></option>
                                <?php 
                                }
                                if (db_num_rows($consulta_clientes)==0) {
                                ?>
                                <option value="">No hay alumnos a los que facturar</option>
                                <?php 
                                }
                                ?>
                                </select></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>¿Como quieres seleccionar los servicios?</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">
                                <input name="tipo_factura" type="radio" value="1" checked> Seleccionar los servicios que se incluirán en la factura de una lista (solo cuando se selecciona un alumno).<br>
                                <input name="tipo_factura" type="radio" value="2"> Seleccionar un periodo de tiempo y se facturarán todos los servicios prestados en dicho periodo.<br>
                                </font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>¿Que servicios quieres facturar?</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">
                                <input name="servicios_propios1" type="checkbox" value="1" checked> Servicios del alumno de tipo anual.<br>
                                <input name="servicios_propios2" type="checkbox" value="1" checked> Servicios del alumno de tipo mensual.<br>
                                <input name="servicios_aula1" type="checkbox" value="1" checked> Servicios del aula de tipo anual.<br>
                                <input name="servicios_aula2" type="checkbox" value="1" checked> Servicios del aula de tipo mensual.<br>
                                <input name="horas_extra" type="checkbox" value="1" checked> Horas extra consumidas.<br>
                                <input name="comedor" type="checkbox" value="1" checked> Servicio de comedor.<br>
                                <input name="comedor_extra" type="checkbox" value="1" checked> Comedores extra.<br>
								<input name="subvenciones" type="checkbox" value="1" checked> Ayudas y subvenciones.<br>
                                </font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha de emisión:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="dia" size=1 style="font-size=10">
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
        </select>/<select name="mes" size=1 style="font-size=10">
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
        </select>/<select name="ano" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php 
for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
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
                         <input type="button" value="Cancelar" onclick="window.location.href='/facturacion/gestion_facturacion.php'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
