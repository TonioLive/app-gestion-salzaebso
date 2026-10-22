<?php  require("../../configuracion/parte_superior_docente.php");

$id_aula=$_GET["id_aula"];

?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">
function comprobar_y_enviar(accion) {
        var errores="";

                self.document.albaranes.action="informe_evaluacion_periodo.php";
                self.document.albaranes.target="_blank";
                if (accion==1) {
                        self.document.albaranes.action="informe_actividades_periodo.php";
                        self.document.albaranes.target="_blank";
                        }
                        
                if (accion==2) {
                        self.document.albaranes.action="informe_evaluacion_periodo_alumno.php";
                        self.document.albaranes.target="_blank";
                        }
                        
                if (accion==3) {
                        self.document.albaranes.action="informe_evaluacion_periodo_alumno_e.php";
                        self.document.albaranes.target="_blank";
                        }
                if (accion==4) {
                        self.document.albaranes.action="/pdf/evaluacion_e.php";
                        self.document.albaranes.target="_blank";
                        }

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
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Informe de evaluación</b><br><small>Selecciona el trimestre para realizar la evaluación:</td>
                        </tr>    
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Trimestre:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="trimestre" size=1 style="font-size=10">
                                <option value="1">1er Trimestre</option>
                                <option value="2">2º Trimestre</option>
                                <option value="3">3er Trimestre</option>
                                                                </select></font></td>
                        </tr>                
                         </table><br>
                         <input type="button" value="Cancelar" onclick="window.close();"> <input type="button" value="Obtener Informe de evaluación >>" onclick="comprobar_y_enviar(0);"><br><br><input type="button" value="Obtener Informe de objetivos trabajados >>" onclick="comprobar_y_enviar(1);"><br><br>
                         <input type="button" value="Obtener Informes individuales de los alumnos (por objetivos) >>" onclick="comprobar_y_enviar(2);"><br><br><input type="button" value="Obtener Informes individuales de los alumnos (por evaluación) >>" onclick="comprobar_y_enviar(3);"><br><br>
                         <input type="button" value="Obtener Informes individuales de los alumnos (por evaluación) en PDF >>" onclick="comprobar_y_enviar(4);"><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
