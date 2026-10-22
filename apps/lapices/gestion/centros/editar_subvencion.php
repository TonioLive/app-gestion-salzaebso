<?php 
require("../../configuracion/parte_superior_sin_login_ancho.php");

if ($permitido!=true) { die();}
if ($nivel_acceso<3) { die();}
if ($nivel_acceso>=10) { die();}

$formulario_name="subvenciones";
$formulario_accion="editar_subvencion1.php";

$id_curso_centro=$_GET["id_curso_centro"];
$id_centro=$_GET["id_centro"];
$id_curso=$_GET["id_curso"];

$url_retorno="listado_centros.php";

if (strlen($id_curso_centro)!=0) {

        $busca_datos_centro=db_query("SELECT * FROM centros_cursos WHERE id_curso_centro=$id_curso_centro");
        if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $mensaje="Edición de la";

                $tope_mes_01=$datos_centro["tope_mes_01"];
                $tope_mes_12=$datos_centro["tope_mes_12"];
                $tope_mes_23=$datos_centro["tope_mes_23"];
                
                $precio_coste_01=$datos_centro["precio_coste_01"];
                $precio_coste_12=$datos_centro["precio_coste_12"];
                $precio_coste_23=$datos_centro["precio_coste_23"];
                
                $tope_maximo2=$datos_centro["tope_maximo2"];
                $subvencion2=$datos_centro["subvencion2"];
                $alumnos_ciclo2=$datos_centro["alumnos_ciclo2"];
                
                } else {
                $id_centro="";
                $mensaje="Alta Nueva";
                }
        } else {
        $mensaje="Alta Nueva";
        }

?>
<script language="Javascript">
function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";

                if (self.document.<?=$formulario_name;?>.tope_mes_01.value=="") {
                        // errores=errores+"Falta introducir el importe de la cuantía máxima por alumno (0 a 1 años) y mes del primer ciclo.\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.tope_mes_01.value)) {
                                errores=errores+"El importe de la cuantía máxima por alumno y mes del primer ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.tope_mes_12.value=="") {
                        // errores=errores+"Falta el importe de la cuantía máxima por alumno (1 a 2 años) y mes del primer ciclo\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.tope_mes_12.value)) {
                                errores=errores+"El importe de la cuantía máxima por alumno (1 a 2 años) y mes del primer ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.tope_mes_23.value=="") {
                        // errores=errores+"Falta el importe de la cuantía máxima por alumno (2 a 3 años) y mes del primer ciclo\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.tope_mes_23.value)) {
                                errores=errores+"El importe de la cuantía máxima por alumno (2 a 3 años) y mes del primer ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.precio_coste_01.value=="") {
                        // errores=errores+"Falta introducir el importe de la cuantía máxima por alumno (0 a 1 años) y mes del primer ciclo.\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_coste_01.value)) {
                                errores=errores+"El importe de la cuantía máxima por alumno y mes del primer ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.precio_coste_12.value=="") {
                        // errores=errores+"Falta el importe de la cuantía máxima por alumno (1 a 2 años) y mes del primer ciclo\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_coste_12.value)) {
                                errores=errores+"El importe de la cuantía máxima por alumno (1 a 2 años) y mes del primer ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.precio_coste_23.value=="") {
                        // errores=errores+"Falta el importe de la cuantía máxima por alumno (2 a 3 años) y mes del primer ciclo\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_coste_23.value)) {
                                errores=errores+"El importe de la cuantía máxima por alumno (2 a 3 años) y mes del primer ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.tope_maximo2.value=="") {
                        // errores=errores+"Falta introducir el importe del módulo económico máximo por unidad del segundo ciclo.\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.tope_maximo2.value)) {
                                errores=errores+"El importe del módulo económico máximo por unidad del segundo ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.subvencion2.value=="") {
                        // errores=errores+"Falta introducir importe de la subvención concedida por unidad del segundo ciclo.\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.subvencion2.value)) {
                                errores=errores+"El importe de la subvención concedida por unidad del segundo ciclo debe ser un número.\n";
                                }
                        }
                if (self.document.<?=$formulario_name;?>.alumnos_ciclo2.value=="") {
                        // errores=errores+"Falta introducir importe de la subvención concedida por unidad del segundo ciclo.\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.alumnos_ciclo2.value)) {
                                errores=errores+"El número de alumnos del segundo ciclo debe ser un número.\n";
                                }
                        }

                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+errores+"\u000A\u000A")
                        }
        }
        
function abrir_anexos(codigo) {
        nueva_ventana=window.open("/blank.html","nueva_ventana","toolbar=yes, width=800, height=600, resizable=yes");
        nueva_ventana.location.href='/pdf/<?=$id_curso?>/anexos'+codigo+'.php?curso_seleccionado=<?=$id_curso?>&id_centro=<?=$id_centro?>';
        }
        
</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_curso_centro" value="<?=$id_curso_centro;?>">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="id_curso" value="<?=$id_curso;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> subvención</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Subvención Consellería primer ciclo de educación infantil</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Precio coste edu. alu./mes (0-1)</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_coste_01" value="<?=$precio_coste_01?>" size=15 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Precio coste edu. alu./mes (1-2)</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_coste_12" value="<?=$precio_coste_12?>" size=15 style="font-size: 10px"></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Precio coste edu alu./mes (2-3)</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_coste_23" value="<?=$precio_coste_23?>" size=15 style="font-size: 10px"></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuantía máx. alu./mes (0-1)</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="tope_mes_01" value="<?=$tope_mes_01?>" size=15 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuantía máx. alu./mes (1-2)</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="tope_mes_12" value="<?=$tope_mes_12?>" size=15 style="font-size: 10px"></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cuantía máx. alu./mes (2-3)</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="tope_mes_23" value="<?=$tope_mes_23?>" size=15 style="font-size: 10px"></td>
                        </tr>
                        <tr>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Anexos 1er. ciclo</b></font></td>
                                <td align="left" bgcolor="#ffffff">
                                <input type='button' style='font-size: 10px' onclick='abrir_anexos(5)' value='Imprimir Anexo II.b'><br>
                                <input type='button' style='font-size: 10px' onclick='abrir_anexos(6)' value='Imprimir Anexo II.c'><br>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Subvención Consellería segundo ciclo de educación infantil</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Módulo económico máximo</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="tope_maximo2" value="<?=$tope_maximo2?>" size=15 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Subvención concedida por aula</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="subvencion2" value="<?=$subvencion2?>" size=15 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nº de alumnos 2º ciclo</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="alumnos_ciclo2" value="<?=$alumnos_ciclo2?>" size=15 style="font-size: 10px"></font></td>
                        </tr>
                        <tr>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Anexos 2º ciclo</b></font></td>
                                <td align="left" bgcolor="#ffffff">
                                <input type='button' style='font-size: 10px' onclick='abrir_anexos(3)' value='Imprimir Anexo IV'><br>
                                <input type='button' style='font-size: 10px' onclick='abrir_anexos(4)' value='Imprimir Anexo IV.a'><br>
                                </td>
                        </tr>
                         </table>
                         <input type="button" value="Cancelar" onclick="window.close()" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
