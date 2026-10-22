<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$id_proveedor=$_GET["u"];
if (strlen($id_proveedor)==0) {
                $id_proveedor=$_POST["u"];
                }

$accion_texto="Editar";
if (strlen($id_evaluacion)==0) {
        $id_evaluacion=$_GET["e"];
        if (strlen($id_evaluacion)==0) {
                $id_evaluacion=$_POST["e"];
                if (strlen($id_evaluacion)==0) {
                        $id_evaluacion=0;
                        $accion_texto="Nueva";
                        }
                }
        }

if ($id_proveedor!=0) {
        $consulta_datos_proveedor=db_query_params('SELECT * FROM proveedores WHERE ID_PROVEEDOR=?', 's', array($id_proveedor));

        if (db_num_rows($consulta_datos_proveedor)!=0)
                {
                $datos_proveedor=db_fetch_array($consulta_datos_proveedor);

                // Datos
                $NIF=$datos_proveedor["NIF"];
                $RAZON_SOCIAL=$datos_proveedor["RAZON_SOCIAL"];
                $DOMICILIO=$datos_proveedor["DOMICILIO"];
                $LOCALIDAD=$datos_proveedor["LOCALIDAD"];
                $CP=$datos_proveedor["CP"];
                $TELEFONO1=$datos_proveedor["TELEFONO1"];
                $TELEFONO2=$datos_proveedor["TELEFONO2"];
                $MOVIL=$datos_proveedor["MOVIL"];
                $FAX=$datos_proveedor["FAX"];
                $EMAIL=$datos_proveedor["EMAIL"];
                $REGISTRO_SANITARIO=$datos_proveedor["REGISTRO_SANITARIO"];
                $OBSERVACIONES_proveedor=$datos_proveedor["OBSERVACIONES"];
                $FORMA_DE_PAGO=$datos_proveedor["FORMA_DE_PAGO"];
                $CUENTA_CARGO=$datos_proveedor["CUENTA_CARGO"];
                $CUENTA_BANCO=$datos_proveedor["CUENTA_BANCO"];

                }
        }

$array_cuestiones=array();
        
if ($id_evaluacion!=0) {
        $consulta_datos_evaluacion=db_query_params('SELECT * FROM proveedores_evaluaciones WHERE id_evaluacion=?', 's', array($id_evaluacion));

        if (db_num_rows($consulta_datos_evaluacion)!=0)
                {
                $datos_evaluacionr=db_fetch_array($consulta_datos_evaluacion);

                // Datos
                $observaciones=$datos_evaluacionr["observaciones"];
                $valoracion_fecha=$datos_evaluacionr["fecha_valoracion"];
                if ($valoracion_fecha=="0000-00-00") {
                                $valoracion_fecha_dia="";
                                $valoracion_fecha_mes="";
                                $valoracion_fecha_ano="";
                                } else {
                                $valoracion_fecha_dia=date("j",strtotime($datos_evaluacionr["fecha_valoracion"]));
                                $valoracion_fecha_mes=date("n",strtotime($datos_evaluacionr["fecha_valoracion"]));
                                $valoracion_fecha_ano=date("Y",strtotime($datos_evaluacionr["fecha_valoracion"]));
                                }
                $valorada_por=$datos_evaluacionr["valorada_por"];

                $introducida_por=$datos_evaluacionr["introducida_por"];
                $fecha_insercion=$datos_evaluacionr["fecha_insercion"];
                
                $consulta_cuestiones=db_query("SELECT * FROM proveedores_cuestiones ORDER BY id_cuestion");
                $numero_de_cuestiones=db_num_rows($consulta_cuestiones);
                while($datos_cuestiones=db_fetch_array($consulta_cuestiones)) {
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][0]=$datos_cuestiones["cuestion"];
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][1]=$datos_evaluacionr["cuestion".$datos_cuestiones["id_cuestion"]];
                        }

                }
        } else {
                $consulta_cuestiones=db_query("SELECT * FROM proveedores_cuestiones ORDER BY id_cuestion");
                $numero_de_cuestiones=db_num_rows($consulta_cuestiones);
                while($datos_cuestiones=db_fetch_array($consulta_cuestiones)) {
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][0]=$datos_cuestiones["cuestion"];
                        $array_cuestiones[$datos_cuestiones["id_cuestion"]][1]=0;
                        }
        }
        
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nueva_evaluacion1.php";

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=3 face="<?= $fuente_general; ?>"><b><br>RC04-02-Rev.0   EVALUACIÓN SUMATIVA PROVEEDORES<br></b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=11 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos del proveedor</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Razón social</b></font></td>
                                <td colspan=10><font size=2 face="<?= $fuente_general; ?>"><?=$RAZON_SOCIAL;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>NIF/CIF</b></font></td>
                                <td colspan=10><font size=2 face="<?= $fuente_general; ?>"><?=$NIF;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=11 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Evaluación</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Evaluado por:</b></font></td>
                                <td colspan=10><font size=2 face="<?= $fuente_general; ?>"><?php 
                                $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE id_usuario=$valorada_por ORDER BY usuarios.Nombre, usuarios.Apellido1") or die(db_error());
                                if (db_num_rows($consulta_usuarios)!=0) {
                                $datos_usuarios=db_fetch_array($consulta_usuarios);
                                ?><?=$datos_usuarios["Nombre"]?> <?=$datos_usuarios["Apellido1"]?> <?=$datos_usuarios["Apellido2"]?><?php 
                                }
                                ?></font></td>
                        </tr>
                        <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de evaluación: </b></font></td>
                                        <td colspan=10><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($valoracion_fecha))?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Registrada por:</b></font></td>
                                <td colspan=10><font size=2 face="<?= $fuente_general; ?>"><?php 
                                $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE id_usuario=$introducida_por ORDER BY usuarios.Nombre, usuarios.Apellido1") or die(db_error());
                                if (db_num_rows($consulta_usuarios)!=0) {
                                $datos_usuarios=db_fetch_array($consulta_usuarios);
                                ?><?=$datos_usuarios["Nombre"]?> <?=$datos_usuarios["Apellido1"]?> <?=$datos_usuarios["Apellido2"]?><?php 
                                }
                                ?></font></td>
                        </tr>
                        <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha de registro: </b></font></td>
                                        <td colspan=10><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($fecha_insercion))?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=11 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <?php 
                                for ($c_puntos=1;$c_puntos<=10;$c_puntos++) {
                                ?>
                                <td><font size=1 face="<?= $fuente_general; ?>"><b><?=$c_puntos;?></b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                    <?php 
                    for ($c_cuest=1;$c_cuest<=$numero_de_cuestiones;$c_cuest++) {
                    ?>
                        <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b><?=$array_cuestiones[$c_cuest][0]?></b></font></td>
                                <?php 
                                for ($c_puntos=1;$c_puntos<=10;$c_puntos++) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><?php if ($array_cuestiones[$c_cuest][1]==$c_puntos) { print "X"; } else { print "&nbsp;"; } ?></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                    <?php 
                    }
                        $promedio_valoracion=0;

                        for ($c_cuest=1;$c_cuest<=$numero_de_cuestiones;$c_cuest++) {
                                $promedio_valoracion=$promedio_valoracion+$array_cuestiones[$c_cuest][1];
                                }

                        if ($numero_de_cuestiones>0) {
                                $promedio_valoracion=round(($promedio_valoracion/$numero_de_cuestiones),2);
                                }
                    ?>
                    <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Promedio:</b></font></td>
                                <td colspan=10><font size=2 face="<?= $fuente_general; ?>"><?=number_format($promedio_valoracion,2,".",",");?></font></td>
                        </tr>
                    <tr valign="top">
                                <td width=210 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td colspan=10><font size=1 face="<?= $fuente_general; ?>"><?=$observaciones;?></font></td>
                        </tr>
                         </table>
                         <font size=1><i>Fecha de impresión del registro: <?=date("d/m/Y")?></i><br></font>
                         <div id="botones" style="display='block'">
                         <br><input type="button" onclick="window.location.href='nuevo_proveedor_b.php'" value="Salir" style="font-size: 10px"> <input type="button" onclick="document.getElementById('botones').style.display='none';window.print();document.getElementById('botones').style.display='block';" value="Imprimir" style="font-size: 10px"><br>
                         </div><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
</body>
</html>
