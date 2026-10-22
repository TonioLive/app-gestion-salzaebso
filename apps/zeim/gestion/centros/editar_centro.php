<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$formulario_name="centros";
$formulario_accion="editar_centro1.php";

$id_centro=$_GET["id_centro"] ;

$url_retorno="listado_centros.php";

if (strlen($id_centro)!=0) {

        $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro));
        if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $mensaje="Edición del";
                $razon_social=$datos_centro["razon_social"];
                $db_nombre_centro=$datos_centro["nombre_centro"];
                $prefijo_centro=$datos_centro["prefijo_centro"];
                $domicilio_centro=$datos_centro["domicilio_centro"];
                $CIF_centro=$datos_centro["CIF_centro"];
                $CP_centro=$datos_centro["CP_centro"];
                $poblacion_centro=$datos_centro["poblacion_centro"];
                $telefono_centro=$datos_centro["telefono_centro"];
                $fax_centro=$datos_centro["fax_centro"];
                $link_centro=$datos_centro["link_centro"];
                $precio_hora_extra=$datos_centro["precio_hora_extra"];
                $precio_menu_1=$datos_centro["precio_menu_1"];
                $precio_menu_2=$datos_centro["precio_menu_2"];
                $precio_menu_3=$datos_centro["precio_menu_3"];
                $precio_menu_4=$datos_centro["precio_menu_4"];
                $precio_menu_5=$datos_centro["precio_menu_5"];
                $precio_semana_1=$datos_centro["precio_semana_1"];
                $precio_semana_2=$datos_centro["precio_semana_2"];
                $precio_semana_3=$datos_centro["precio_semana_3"];
                $precio_semana_4=$datos_centro["precio_semana_4"];
                $precio_semana_5=$datos_centro["precio_semana_5"];
                $precio_quincena_1=$datos_centro["precio_quincena_1"];
                $precio_quincena_2=$datos_centro["precio_quincena_2"];
                $precio_quincena_3=$datos_centro["precio_quincena_3"];
                $precio_quincena_4=$datos_centro["precio_quincena_4"];
                $precio_quincena_5=$datos_centro["precio_quincena_5"];
                $precio_mes_1=$datos_centro["precio_mes_1"];
                $precio_mes_2=$datos_centro["precio_mes_2"];
                $precio_mes_3=$datos_centro["precio_mes_3"];
                $precio_mes_4=$datos_centro["precio_mes_4"];
                $precio_mes_5=$datos_centro["precio_mes_5"];
                
                $hora_entrada_centro_man=$datos_centro["hora_entrada1"];
                $hora_salida_centro_man=$datos_centro["hora_salida1"];
                $hora_entrada_centro_tar=$datos_centro["hora_entrada2"];
                $hora_salida_centro_tar=$datos_centro["hora_salida2"];
                
                $hora_entrada_centro_man_mj=$datos_centro["hora_entrada1_mj"];
                $hora_salida_centro_man_mj=$datos_centro["hora_salida1_mj"];
                $hora_entrada_centro_tar_mj=$datos_centro["hora_entrada2_mj"];
                $hora_salida_centro_tar_mj=$datos_centro["hora_salida2_mj"];
                
                $division_horas=$datos_centro["division_horas"];
                $periodo_gracia=$datos_centro["periodo_gracia"];
                
                $titular=$datos_centro["titular"];
                $codigo_centro=$datos_centro["codigo_centro"];
                $nombre_completo_centro=$datos_centro["nombre_completo"];
                
                } else {
                $id_centro="";
                $mensaje="Alta Nuevo";
                }
        } else {
        $mensaje="Alta Nuevo";
        }

?>
<script language="Javascript">
function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";

                if (self.document.<?=$formulario_name;?>.nombre_centro.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre del centro.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.poblacion_centro.value=="") { mensajes_error=mensajes_error+"Falta introducir la población.\u000A";errores=1; }

                if (self.document.<?=$formulario_name;?>.precio_hora_extra.value=="") {
                        errores=1;
                        mensajes_error=mensajes_error+"Falta introducir el precio de la hora extra.\n";
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_hora_extra.value)) {
                                errores=1;
                                mensajes_error=mensajes_error+"El precio de la hora extra debe ser un número.\n";
                                }
                        }
               if (self.document.<?=$formulario_name;?>.precio_menu_1.value=="") {
                        self.document.<?=$formulario_name;?>.precio_menu_1.value=0;
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_menu_1.value)) {
                                errores=1;
                                mensajes_error=mensajes_error+"El precio del menú para Pequeños debe ser un número.\n";
                                }
                        }
               if (self.document.<?=$formulario_name;?>.precio_menu_2.value=="") {
                        self.document.<?=$formulario_name;?>.precio_menu_2.value=0;
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_menu_2.value)) {
                                errores=1;
                                mensajes_error=mensajes_error+"El precio del menú para Mayores debe ser un número.\n";
                                }
                        }
               if (self.document.<?=$formulario_name;?>.precio_menu_3.value=="") {
                        self.document.<?=$formulario_name;?>.precio_menu_3.value=0;
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_menu_3.value)) {
                                errores=1;
                                mensajes_error=mensajes_error+"El precio del menú Colegial debe ser un número.\n";
                                }
                        }
               if (self.document.<?=$formulario_name;?>.precio_menu_4.value=="") {
                        self.document.<?=$formulario_name;?>.precio_menu_4.value=0;
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_menu_4.value)) {
                                errores=1;
                                mensajes_error=mensajes_error+"El precio de la Toma debe ser un número.\n";
                                }
                        }
               if (self.document.<?=$formulario_name;?>.precio_menu_5.value=="") {
                        self.document.<?=$formulario_name;?>.precio_menu_5.value=0;
                        } else {
                        if (isNaN(self.document.<?=$formulario_name;?>.precio_menu_5.value)) {
                                errores=1;
                                mensajes_error=mensajes_error+"El precio del menú para Bebés debe ser un número.\n";
                                }
                        }
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }
        
function abrir_anexos(codigo) {
        nueva_ventana=window.open("/blank.html","nueva_ventana","toolbar=yes, width=800, height=600, resizable=yes");
        nueva_ventana.location.href='/pdf/anexos'+codigo+'.php?id_centro=<?=$id_centro?>';
        }
        
</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> Centro</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos del centro</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre Corto del Centro:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_centro" value="<?=$db_nombre_centro;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre Completo del Centro:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_completo_centro" value="<?=$nombre_completo_centro;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Código del Centro:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="codigo_centro" value="<?=$codigo_centro;?>" size="40" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Titular o rep. del Centro:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="titular" value="<?=$titular;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Prefijo facturas:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="prefijo_centro" value="<?=$prefijo_centro;?>" size="2" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Razón Social:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="razon_social" value="<?=$razon_social;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>N.I.F./C.I.F.:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="CIF_centro" value="<?=$CIF_centro;?>" size="40" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Dirección:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="domicilio_centro" value="<?=$domicilio_centro;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>C.P.:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="CP_centro" value="<?=$CP_centro;?>" size="20" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Población:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="poblacion_centro" value="<?=$poblacion_centro;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_centro" value="<?=$telefono_centro;?>" size="40" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fax:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="fax_centro" value="<?=$fax_centro;?>" size="40" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>URL Centro:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="link_centro" value="<?=$link_centro;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Precio de la hora extra</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_hora_extra" value="<?=$precio_hora_extra?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fracción hora extra</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="division_horas" style="font-size: 10px">
                                <?php 
                                for ($tt=1;$tt<=60;$tt++) {
                                ?>
                                <option value="<?=$tt;?>"<?php  if ($tt==$division_horas) { print " selected"; } ?>><?=$tt;?></option>
                                <?php 
                                }
                                ?>
                                </select> Minutos</font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tiempo de gracia</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="periodo_gracia" style="font-size: 10px">
                                <?php 
                                for ($tt=0;$tt<=60;$tt++) {
                                ?>
                                <option value="<?=$tt;?>"<?php  if ($tt==$periodo_gracia) { print " selected"; } ?>><?=$tt;?></option>
                                <?php 
                                }
                                ?>
                                </select> Minutos</font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Precio menús</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><b>1 Comida&nbsp;&nbsp;&nbsp;&nbsp; Semana&nbsp;&nbsp;&nbsp;&nbsp; Quincena&nbsp;&nbsp;&nbsp;&nbsp; Mes</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Pequeños</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_menu_1" value="<?=$precio_menu_1?>" size=10 style="font-size: 10px"> <input type="text" name="precio_semana_1" value="<?=$precio_semana_1?>" size=10 style="font-size: 10px"> <input type="text" name="precio_quincena_1" value="<?=$precio_quincena_1?>" size=10 style="font-size: 10px"> <input type="text" name="precio_mes_1" value="<?=$precio_mes_1?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mayores</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_menu_2" value="<?=$precio_menu_2?>" size=10 style="font-size: 10px"> <input type="text" name="precio_semana_2" value="<?=$precio_semana_2?>" size=10 style="font-size: 10px"> <input type="text" name="precio_quincena_2" value="<?=$precio_quincena_2?>" size=10 style="font-size: 10px"> <input type="text" name="precio_mes_2" value="<?=$precio_mes_2?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Colegial</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_menu_3" value="<?=$precio_menu_3?>" size=10 style="font-size: 10px"> <input type="text" name="precio_semana_3" value="<?=$precio_semana_3?>" size=10 style="font-size: 10px"> <input type="text" name="precio_quincena_3" value="<?=$precio_quincena_3?>" size=10 style="font-size: 10px"> <input type="text" name="precio_mes_3" value="<?=$precio_mes_3?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Toma</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_menu_4" value="<?=$precio_menu_4?>" size=10 style="font-size: 10px"> <input type="text" name="precio_semana_4" value="<?=$precio_semana_4?>" size=10 style="font-size: 10px"> <input type="text" name="precio_quincena_4" value="<?=$precio_quincena_4?>" size=10 style="font-size: 10px"> <input type="text" name="precio_mes_4" value="<?=$precio_mes_4?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Bebe</b></font></td>
                                <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="precio_menu_5" value="<?=$precio_menu_5?>" size=10 style="font-size: 10px"> <input type="text" name="precio_semana_5" value="<?=$precio_semana_5?>" size=10 style="font-size: 10px"> <input type="text" name="precio_quincena_5" value="<?=$precio_quincena_5?>" size=10 style="font-size: 10px"> <input type="text" name="precio_mes_5" value="<?=$precio_mes_5?>" size=10 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Subvenciones</b></font></td>
                        </tr>
                        <tr valign="top">
<?php 
if (strlen($id_centro)!=0) {
?>
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><table border=1 cellpadding=1 cellspacing=0 style="border-collapse: collapse" bordercolor="#000000">
                                        <tr>
                                                <td><font size=2><b>Curso</b></font></td>
                                                <td><font size=2><b>&nbsp;</b></font></td>
                                        </tr>
                                <?php 
                                $busca_cursos=db_query("SELECT * FROM cursos_academicos WHERE tipo_curso_academico=1 ORDER BY fecha_inicio");
                                while($lista_cursos=db_fetch_array($busca_cursos)) {
                                        // Busca datos
                                        $busca_datos=db_query_params('SELECT * FROM centros_cursos WHERE id_centro=? AND id_curso=?', 'ss', array($id_centro, $lista_cursos["id_curso"]));
                                        if (db_num_rows($busca_datos)!=0) {
                                                        $datos_sub=db_fetch_array($busca_datos);
                                                        ?>
                                                        <tr>
                                                                <td><font size=2><?=$lista_cursos["nombre"]?></font></td>
                                                                <td aling="center"><font size=2><input type="button" value="Editar datos subvención" style="font-size: 10px" onclick="window.open('editar_subvencion.php?id_curso_centro=<?=$datos_sub["id_curso_centro"]?>&id_curso=<?=$lista_cursos["id_curso"]?>&id_centro=<?=$id_centro?>','sub','width=550,height=450')"></font></td>
                                                        </tr>
                                                        <?php 
                                                } else {
                                                if (strtotime($lista_cursos["fecha_fin"])>strtotime(date("Y-m-d"))) {
                                                        ?>
                                                        <tr>
                                                                <td><font size=2><?=$lista_cursos["nombre"]?></font></td>
                                                                <td aling="center"><font size=2><input type="button" value="Editar datos subvención" style="font-size: 10px" onclick="window.open('editar_subvencion.php?id_curso=<?=$lista_cursos["id_curso"]?>&id_centro=<?=$id_centro?>','sub','width=550,height=450')"></font></td>
                                                        </tr>
                                                        <?php 
                                                        }
                                                }
                                        }
                                ?>
                                </table></font></td>
                        </tr>
<?php 
}
?>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Horario del Centro</b></font></td>
                        </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mañanas:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_centro_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_centro_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_centro_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_centro_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tardes:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_centro_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_centro_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_centro_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_centro_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Horario Media Jornada</b></font></td>
                        </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mañanas:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_centro_man_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_centro_man_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_centro_man_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_centro_man_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tardes:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e1_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_centro_tar_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e1_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_centro_tar_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s1_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_centro_tar_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s1_mj" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_centro_tar_mj))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                         </table>
                         <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
