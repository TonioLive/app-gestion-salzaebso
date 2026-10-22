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
window.moveTo(0,0);
// window.sizeTo(screen.width,screen.height);
window.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$id_aula=$_GET["i"];

$consulta_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula)) or die(db_error()."-2");

$datos_aula=db_fetch_array($consulta_aula);

$nombre_aula=$datos_aula["nombre"];

$consulta_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aula["id_centro"])) or die(db_error()."-2");
$datos_centro=db_fetch_array($consulta_nombre_centro);


// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "guardar_servicios_aula.php";

?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="id_aula" value="<?=$id_aula;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Edición de Servicios asociados al aula</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff">
                                        <font size=2 face="<?= $fuente_general; ?>"><br><b><?=$nombre_aula?><br><small>
                                        Centro <i>"<?=$datos_centro["nombre_centro"];?>"</i>.</b><br>
                                        <b>Curso académico:</b>
                                <?php 
                                $consulta_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_aula["id_curso"])) or die(db_error()."-4");;
                                $cursos=db_fetch_array($consulta_cursos);

                                echo $cursos["nombre"];
                                ?><br></font></small></td>
                                </tr>
                                <tr valign="top">
                                        <td width=100% align="center" colspan=2>
                                        <table width=100% cellpadding=2 cellspacing=0 border=1 bordercolor="#000000" style="border-collapse: collapse">
                                                <tr bgcolor="#EFEFEF">
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Servicio</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Periodicidad</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Inicio</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Fin</b></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><b>Importe</b></font></td>
                                                        <td colspan=3><font face="<?= $fuente_general; ?>" size="2"><b>&nbsp;</b></font></td>
                                                </tr>
                                                <?php 
                                                $busca_servicios=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa, tipos_iva_servicios WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_aula.id_aula=? ORDER BY servicios_contratados_aula.fecha_inicio', 's', array($id_aula));
                                                
                                                if (db_num_rows($busca_servicios)==0) {
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center" colspan="7">
                                                        <br><b>No hay servicios contratados, haz clic en el botón <i>"Agregar servicios"</i> para empezar a trabajar.</b><br><br>
                                                        </td>
                                                </tr>
                                                <?php 
                                                }
                                                
                                                while($servicios=db_fetch_array($busca_servicios)) {

                                                // Busca ultima factura emitida de este artículo
                                                $busca_factura=db_query("SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.articulo=".(100000+$servicios["id_servicio_contratado"])." ORDER BY facturas_emitidas.fecha_emision DESC") or die(db_error());
                                                if (db_num_rows($busca_factura)==0) {
                                                               $ultima_facturacion="<font color=red>No facturado</font>";
                                                               $no_eliminar=false;
                                                        } else {
                                                                $datos_factura=db_fetch_array($busca_factura);
                                                                $ultima_facturacion='<small><a href="/pdf/factura.php?id_factura='.$datos_factura["id_factura"].'">nº '.$datos_factura["num_factura"].'/'.$datos_factura["ejercicio_factura"].' del '.date("d/m/Y",strtotime($datos_factura["fecha_emision"])).'</a></small>';
                                                                $no_eliminar=true;
                                                        }
                                                
                                                if ($servicios["periodicidad"]==1) {
                                                      $periodicidad="Anual";
                                                      $fecha_fin=date("d/m/Y",strtotime($servicios["fecha_fin"]));
                                                      } else {
                                                      $periodicidad="Mensual";
                                                      $fecha_fin="-";
                                                      }
                                                // Calcula importe
                                                $base_imponible=round(($servicios["PRECIO_VENTA"]*$servicios["cantidad"]),2);
                                                $descuento=round($base_imponible*round(($servicios["descuento"]/100),4),2);
                                                $base_inc_dto=$base_imponible-$descuento;
                                                $importe=round($base_inc_dto*(1+($servicios["porcentaje_iva"])),2);
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$servicios["ARTICULO"];?><?php if (strlen($servicios["notas"])!=0) { echo " - ".$servicios["notas"]; } ?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$periodicidad;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=date("d/m/Y",strtotime($servicios["fecha_inicio"]));?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$fecha_fin;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=number_format($importe,2,",",".");?> €</font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><?=$ultima_facturacion;?></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="nuevo_servicio_aula.php?ii=<?=$id_aula?>&ids=<?=$servicios["id_servicio_contratado"];?>"><img src="modificar.gif" border=0></a></font></td>
                                                        <td align="center"><font face="<?= $fuente_general; ?>" size="2"><a href="<?php  if ($no_eliminar==true) {?>javascript:alert('No se puede eliminar un servicio ya facturado');<?php } else { ?>javascript:eliminar(<?=$servicios["id_servicio_contratado"];?>);<?php  } ?>"><img src="borrar.gif" border=0></a></font></td>
                                                </tr>
                                                <?php 
                                                }
                                                ?>
                                        </table><br>
                                        <input type="button" onclick="window.location.href='nuevo_servicio_aula.php?ii=<?=$id_aula;?>'" value="Agregar servicios" style="font-size: 10px">
                                        <br>
                                        <br>
                                        </td>
                                </tr>
                         </table>
                         <br><input type="button" onclick="window.close()" value="Salir" style="font-size: 10px"><input type="button" onclick="cambia();" value="Guardar cambios" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function eliminar(id) {
        if (confirm("¿Realmente deseas eliminar el servicio seleccionado?")) {
                window.location.href='eliminar_servicio_aula.php?ii=<?=$id_aula?>&ids='+id;
                }
        }
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
        
function cambia() {
                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.curso.value=="") { mensajes_error=mensajes_error+"No has seleccionado el curso académico.\u000A";errores=1; }
                
                // Comprueba fecha
                la_fecha=self.document.<?=$formulario_name;?>.usuario_fechamat_mes.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_dia.value+"/"+self.document.<?=$formulario_name;?>.usuario_fechamat_ano.value
                if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de matrícula introducida no es correcta.\u000A";errores=1; }
                
                // Comprueba la entidad bancaria
                cuenta1=self.document.<?=$formulario_name;?>.banco_cuenta1.value;
                cuenta2=self.document.<?=$formulario_name;?>.banco_cuenta2.value;
                cuenta3=self.document.<?=$formulario_name;?>.banco_cuenta3.value;
                cuenta4=self.document.<?=$formulario_name;?>.banco_cuenta4.value;
                if ((cuenta1+cuenta2+cuenta3+cuenta4)!="") {
                        // Parte 1
                        if (isNaN(cuenta1)) {
                                mensajes_error=mensajes_error+"El número de la entidad no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta1=cuenta1.replace(" ","");
                                if (cuenta1.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la entidad no es correcto,debe tener 4 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 2
                        if (isNaN(cuenta2)) {
                                mensajes_error=mensajes_error+"El número de la oficina no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta2=cuenta2.replace(" ","");
                                if (cuenta2.length!=4) {
                                        mensajes_error=mensajes_error+"El número de la oficina no es correcto,debe tener 4 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 3
                        if (isNaN(cuenta3)) {
                                mensajes_error=mensajes_error+"El dígito de control no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta3=cuenta3.replace(" ","");
                                if (cuenta3.length!=2) {
                                        mensajes_error=mensajes_error+"El dígito de control no es correcto,debe tener 2 dígitos.\u000A";errores=1;
                                        }
                                }
                        // Parte 4
                        if (isNaN(cuenta4)) {
                                mensajes_error=mensajes_error+"El número de la cuenta no es correcto, solo se permiten números.\u000A";errores=1;
                                } else {
                                cuenta4=cuenta4.replace(" ","");
                                if (cuenta4.length!=10) {
                                        mensajes_error=mensajes_error+"El número de la cuenta no es correcto,debe tener 10 dígitos.\u000A";errores=1;
                                        }
                                }
                        }
                
                if (errores==0) {
                                document.<?=$formulario_name;?>.submit();
                        } else {
                                alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error);
                        }
        }
//-->
</script>
</center></div>
</div>
</body>
</html>
