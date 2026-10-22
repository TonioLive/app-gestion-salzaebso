<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
$formulario_name="aulas";
$formulario_accion="editar_aula1.php";

$id_aula=$_GET["id_aula"];
$o=$_GET["o"];

if (strlen($id_aula)!=0) {

        $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
        if (db_num_rows($busca_datos_aula)!=0) {
                $datos_aula=db_fetch_array($busca_datos_aula);
                $mensaje="Edición";
                $nombre_aula=$datos_aula["nombre"];
                $capacidad=$datos_aula["capacidad"];
                $ocupacion_minima=$datos_aula["ocupacion_minima"];
                $descripcion=$datos_aula["descripcion"];
                $equipamiento=$datos_aula["equipamiento"];
                $edad_minima=$datos_aula["edad_minima"];
                $edad_maxima=$datos_aula["edad_maxima"];

                $edad_minima_prog=$datos_aula["edad_minima_prog"];
                $edad_maxima_prog=$datos_aula["edad_maxima_prog"];
                
                $mes_minimo=$datos_aula["mes_minimo"];
                $anio_minimo=$datos_aula["anio_minimo"];
                $mes_maximo=$datos_aula["mes_maximo"];
                $anio_maximo=$datos_aula["anio_maximo"];
                
                if (strlen($mes_minimo)==0) {
                        $mes_minimo=12;
                        }
                if (strlen($anio_minimo)==0) {
                        $anio_minimo=date("Y")-$edad_minima;
                        }
                        
                if (strlen($mes_maximo)==0) {
                        $mes_maximo=1;
                        }
                if (strlen($anio_maximo)==0) {
                        $anio_maximo=date("Y")-$edad_maxima;
                        }
                        
                $curso_academico=$datos_aula["id_curso"];
                $docente_titular=$datos_aula["docente_titular"];
                $id_centro=$datos_aula["id_centro"];
                $tipo=$datos_aula["tipo"];
                $menu_actual=$datos_aula["id_menu_predefinido"];
                $ciclo=$datos_aula["ciclo"];
                } else {
                $id_aula="";
                $id_centro=$centro_actual;
                $mensaje="Alta";
                }
        } else {
                $id_aula="";
                $id_centro=$_GET["id_centro_aula_nueva"];
                $mensaje="Alta";
        }
if ($o==1) {
        $url_retorno="gestion_aulas.php?curso_seleccionado=".$curso_academico;
        } else {
        $url_retorno="editar_centro1.php?id_centro=".$id_centro;
        }
        
if (strlen($id_centro)==0) {
        $id_centro=$centro_actual;
        }
        
// Busca datos centro

        $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro));
        if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);

                $tope_mes_01=$datos_centro["tope_mes_01"];
                $tope_mes_12=$datos_centro["tope_mes_12"];
                $tope_mes_23=$datos_centro["tope_mes_23"];
                $tope_maximo2=$datos_centro["tope_maximo2"];
                $subvencion2=$datos_centro["subvencion2"];

                } else {
                
                $tope_mes_01=0;
                $tope_mes_12=0;
                $tope_mes_23=0;
                $tope_maximo2=0;
                $subvencion2=0;
                
                }

        
?>
<script language="Javascript">
function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.nombre_aula.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre del aula.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.curso_academico.value=="") { mensajes_error=mensajes_error+"Debes seleccionar un curso lectivo.\u000A";errores=1; }
                
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }

 function comprobar_si_hay_curso(){
        if (self.document.<?=$formulario_name;?>.curso_academico.value!="") {
                self.document.<?=$formulario_name;?>.nombre_aula.disabled=false;
                self.document.<?=$formulario_name;?>.tipo.disabled=false;
                self.document.<?=$formulario_name;?>.capacidad.disabled=false;
                self.document.<?=$formulario_name;?>.ocupacion_minima.disabled=false;
                self.document.<?=$formulario_name;?>.descripcion.disabled=false;
                self.document.<?=$formulario_name;?>.equipamiento.disabled=false;
                self.document.<?=$formulario_name;?>.edad_minima.disabled=false;
                self.document.<?=$formulario_name;?>.edad_maxima.disabled=false;
                self.document.<?=$formulario_name;?>.edad_minima_prog.disabled=false;
                self.document.<?=$formulario_name;?>.edad_maxima_prog.disabled=false;
                self.document.<?=$formulario_name;?>.mes_minimo.disabled=false;
                self.document.<?=$formulario_name;?>.mes_maximo.disabled=false;
                self.document.<?=$formulario_name;?>.anio_minimo.disabled=false;
                self.document.<?=$formulario_name;?>.anio_maximo.disabled=false;
                } else {
                self.document.<?=$formulario_name;?>.nombre_aula.disabled=true;
                self.document.<?=$formulario_name;?>.tipo.disabled=true;
                self.document.<?=$formulario_name;?>.capacidad.disabled=true;
                self.document.<?=$formulario_name;?>.ocupacion_minima.disabled=true;
                self.document.<?=$formulario_name;?>.descripcion.disabled=true;
                self.document.<?=$formulario_name;?>.equipamiento.disabled=true;
                self.document.<?=$formulario_name;?>.edad_minima.disabled=true;
                self.document.<?=$formulario_name;?>.edad_maxima.disabled=true;
                self.document.<?=$formulario_name;?>.edad_minima_prog.disabled=true;
                self.document.<?=$formulario_name;?>.edad_maxima_prog.disabled=true;
                self.document.<?=$formulario_name;?>.mes_minimo.disabled=true;
                self.document.<?=$formulario_name;?>.mes_maximo.disabled=true;
                self.document.<?=$formulario_name;?>.anio_minimo.disabled=true;
                self.document.<?=$formulario_name;?>.anio_maximo.disabled=true;
                }
        }
        
function editarservicios() {
        window.open('/facturacion/editar_servicios_aula.php?i=<?=$id_aula;?>','servicios','toolbar=no,resizable=no,width=600,height=500');
        }
</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_aula" value="<?=$id_aula;?>">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="formulario_enviado" value="1">
<input type="hidden" name="tope_maximo2" value="<?=$tope_maximo2;?>">
<input type="hidden" name="subvencion2" value="<?=$subvencion2;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> Aula</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos aula</b></font></td>
                        </tr>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Curso Académico:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><select name="curso_academico" size=1 style="font-size: 10px" onchange="comprobar_si_hay_curso();">
                                <option value="">Selecciona el curso</option>
                                <?php 
                                $busca_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio DESC;");
                                while($datos_cursos=db_fetch_array($busca_cursos)) {
                                ?>
                                <option value="<?=$datos_cursos["id_curso"];?>"<?php  if ($datos_cursos["id_curso"]==$curso_academico) { print " selected"; } ?>><?=$datos_cursos["nombre"];?></option>
                                <?php 
                                }
                                ?>
                                </select> <input type="button" value="Nuevo curso académico" onclick="window.location.href='editar_curso.php?id_aula=<?=$id_aula?>'" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Ciclo educativo:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><select name="ciclo" size=1 style="font-size: 10px">
                                <option value="">No procede</option>
                                <?php 
                                $busca_ciclos=db_query("SELECT * FROM ciclos_formativos ORDER BY id_ciclo;");
                                while($datos_ciclos=db_fetch_array($busca_ciclos)) {
                                ?>
                                <option value="<?=$datos_ciclos["id_ciclo"];?>"<?php  if ($datos_ciclos["id_ciclo"]==$ciclo) { print " selected"; } ?>><?=$datos_ciclos["nombre_ciclo"];?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre del aula:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><input disabled type="text" name="nombre_aula" value="<?=$nombre_aula;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <?php 
                        if ($nivel_acceso==4) {
                                $estado_select="";
                                } else {
                                $estado_select=" disabled";
                                }
                        ?>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de Aula:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><select name="tipo" size=1 style="font-size: 10px"<?=$estado_select?>>
                                <option value="0"<?php  if ($tipo==0) { print " selected"; } ?>>Docencia</option>
                                <option value="2"<?php  if ($tipo==2) { print " selected"; } ?>>CHEQUE</option>
                                <option value="3"<?php  if ($tipo==3) { print " selected"; } ?>>Almacén</option>
                                <option value="4"<?php  if ($tipo==4) { print " selected"; } ?>>Despacho</option>
                                <option value="5"<?php  if ($tipo==5) { print " selected"; } ?>>Zona de servicios</option>
                                </select></font></td>
                        </tr>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Docente:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><select name="docente_titular" size=1 style="font-size: 10px" onchange="comprobar_si_hay_curso();">
                                <option value="">Selecciona el docente</option>
                                <?php 
                                $busca_docentes=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion=4 ORDER BY Apellido1, Apellido2, Nombre;");
                                while($datos_docentes=db_fetch_array($busca_docentes)) {
                                ?>
                                <option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$docente_titular) { print " selected"; } ?>><?=$datos_docentes["Apellido1"];?> <?=$datos_docentes["Apellido2"];?>, <?=$datos_docentes["Nombre"];?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select disabled name="capacidad" size="1" style="font-size: 10px">
                                <option value=""></option>
                                <?php 
                                for ($capacidades=1;$capacidades<=50;$capacidades++) {
                                ?>
                                <option value="<?=$capacidades;?>"<?php  if ($capacidades==$capacidad) { print " selected"; } ?>><?=$capacidades;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación mínima:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select disabled name="ocupacion_minima" size="1" style="font-size: 10px">
                                <option value=""></option>
                                <?php 
                                for ($capacidades=1;$capacidades<=50;$capacidades++) {
                                ?>
                                <option value="<?=$capacidades;?>"<?php  if ($capacidades==$ocupacion_minima) { print " selected"; } ?>><?=$capacidades;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Descripción del aula:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><textarea disabled name="descripcion" rows=4  cols="80" style="font-size: 11px; font-family: Arial"><?= $descripcion; ?></textarea></font></td>
                       </tr>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Equipamiento del aula:</b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><textarea disabled name="equipamiento" rows=4  cols="80" style="font-size: 11px; font-family: Arial"><?= $equipamiento; ?></textarea></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Edad mínima:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select disabled name="edad_minima" size="1" style="font-size: 10px">
                                <?php 
                                for ($edades=0;$edades<=$limite_de_edad;$edades++) {
                                ?>
                                <option value="<?=$edades;?>"<?php  if ($edades==$edad_minima) { print " selected"; } ?>><?=$edades;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Edad máxima:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select disabled name="edad_maxima" size="1" style="font-size: 10px">
                                <?php 
                                for ($edades=0;$edades<=$limite_de_edad;$edades++) {
                                ?>
                                <option value="<?=$edades;?>"<?php  if ($edades==$edad_maxima) { print " selected"; } ?>><?=$edades;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Edad mínima programación:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select disabled name="edad_minima_prog" size="1" style="font-size: 10px">
                                <?php 
                                for ($edades=0;$edades<=$limite_de_edad;$edades++) {
                                ?>
                                <option value="<?=$edades;?>"<?php  if ($edades==$edad_minima_prog) { print " selected"; } ?>><?=$edades;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Edad máxima programación:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select disabled name="edad_maxima_prog" size="1" style="font-size: 10px">
                                <?php 
                                for ($edades=0;$edades<=$limite_de_edad;$edades++) {
                                ?>
                                <option value="<?=$edades;?>"<?php  if ($edades==$edad_maxima_prog) { print " selected"; } ?>><?=$edades;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nacidos entre </b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><select disabled name="mes_maximo" size="1" style="font-size: 10px">
                                <?php 
                                for ($meses=1;$meses<=12;$meses++) {
                                ?>
                                <option value="<?=$meses;?>"<?php  if ($mes_maximo==$meses) { print " selected"; } ?>><?=$nombre_mes[$meses];?></option>
                                <?php 
                                }
                                ?>
                                </select> de <select disabled name="anio_maximo" size="1" style="font-size: 10px">
                                <?php 
                                for ($anios=1995;$anios<=date("Y")+2;$anios++) {
                                ?>
                                <option value="<?=$anios;?>"<?php  if ($anio_maximo==$anios) { print " selected"; } ?>><?=$anios;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>...y </b></font></td>
                                <td colspan=3><font size=1 face="<?= $fuente_general; ?>"><select disabled name="mes_minimo" size="1" style="font-size: 10px">
                                <?php 
                                for ($meses=1;$meses<=12;$meses++) {
                                ?>
                                <option value="<?=$meses;?>"<?php  if ($mes_minimo==$meses) { print " selected"; } ?>><?=$nombre_mes[$meses];?></option>
                                <?php 
                                }
                                ?>
                                </select> de <select disabled name="anio_minimo" size="1" style="font-size: 10px">
                                <?php 
                                for ($anios=1995;$anios<=date("Y")+2;$anios++) {
                                ?>
                                <option value="<?=$anios;?>"<?php  if ($anio_minimo==$anios) { print " selected"; } ?>><?=$anios;?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                                <tr valign="top">
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Menú predeterminado</b></font></td>
                                        <td colspan=2><font size="1" face="<?= $fuente_general; ?>"><select name="id_menu_predefinido" style="font-size: 10px">
                                        <option value="0">Ninguno</option>
                                        <?php 
                                        $consulta_datos_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu;") or die(db_error());
                                        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
                                        ?>
                                        <option value="<?=$datos_menus["id_menu"]?>"<?php  if ($datos_menus["id_menu"]==$menu_actual) { print " selected"; }  if ($datos_menus["id_menu"]==$id_menu_seleccionado) { print " style='color: #1eae32'"; } ?>><?=urldecode($datos_menus["nombre_menu"])?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select></font></td>
                                </tr>
                        </table>
                        <?php 
                        if (($nivel_acceso==4) and (strlen($id_aula)!=0)) {
                        ?><input type="button" value="Eliminar aula" onclick="eliminar_aula()" style="font-size: 10px"><?php 
                        }
                        if (strlen($id_aula)!="") {
                        ?>
                        <input type="button" value="Editar servicios asociados" onclick="editarservicios()" style="font-size: 10px"> <?php  } ?><input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
<script language="Javascript">
comprobar_si_hay_curso();
<?php 
if (strlen($id_aula)!=0) {
?>
function eliminar_aula() {
        <?php 
        // Comprueba si hay alumnos
        
        $busca_alumnos=db_query_params('SELECT * FROM alumnos WHERE aula=?', 's', array($id_aula));
        
        if (db_num_rows($busca_alumnos)!=0) {
        ?>
        alert("EL AULA TIENE ALUMNOS\n\nNo puede ser eliminada");
        <?php 
        } else {
        ?>
        if (confirm("Se va a eliminar definitivamente el aula ¿Estás seguro?")) {
                window.location.href="eliminar_aula.php?id_centro=<?=$id_centro?>&id_aula=<?=$id_aula?>";
                }
        <?php 
        }
        ?>
        }
<?php 
}
?>
</script>
</table><br><br>
<?php  require("../../configuracion/parte_inferior.php"); ?>
