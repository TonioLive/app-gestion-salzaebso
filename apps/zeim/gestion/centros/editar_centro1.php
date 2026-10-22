<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$formulario_name="centros";
$formulario_accion="editar_centro2.php";

$id_centro=$_GET["id_centro"] ;
if (strlen($id_centro)==0) {
        $id_centro=$_POST["id_centro"];
        }

$url_retorno="listado_centros.php";

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

                $titular=$_POST["titular"];
                $codigo_centro=$_POST["codigo_centro"];
                $nombre_completo_centro=$_POST["nombre_completo_centro"];
                
                        $razon_social=$_POST["razon_social"];
                        $db_nombre_centro=$_POST["nombre_centro"];
                        $prefijo_centro=$_POST["prefijo_centro"];
                        $domicilio_centro=$_POST["domicilio_centro"];
                        $CIF_centro=$_POST["CIF_centro"];
                        $CP_centro=$_POST["CP_centro"];
                        $poblacion_centro=$_POST["poblacion_centro"];
                        $telefono_centro=$_POST["telefono_centro"];
                        $fax_centro=$_POST["fax_centro"];
                        $link_centro=$_POST["link_centro"];
                        $precio_hora_extra=$_POST["precio_hora_extra"];
                        $precio_menu_1=$_POST["precio_menu_1"];
                        $precio_menu_2=$_POST["precio_menu_2"];
                        $precio_menu_3=$_POST["precio_menu_3"];
                        $precio_menu_4=$_POST["precio_menu_4"];
                        $precio_menu_5=$_POST["precio_menu_5"];
                        $precio_semana_1=$_POST["precio_semana_1"];
                        $precio_semana_2=$_POST["precio_semana_2"];
                        $precio_semana_3=$_POST["precio_semana_3"];
                        $precio_semana_4=$_POST["precio_semana_4"];
                        $precio_semana_5=$_POST["precio_semana_5"];
                        $precio_quincena_1=$_POST["precio_quincena_1"];
                        $precio_quincena_2=$_POST["precio_quincena_2"];
                        $precio_quincena_3=$_POST["precio_quincena_3"];
                        $precio_quincena_4=$_POST["precio_quincena_4"];
                        $precio_quincena_5=$_POST["precio_quincena_5"];
                        $precio_mes_1=$_POST["precio_mes_1"];
                        $precio_mes_2=$_POST["precio_mes_2"];
                        $precio_mes_3=$_POST["precio_mes_3"];
                        $precio_mes_4=$_POST["precio_mes_4"];
                        $precio_mes_5=$_POST["precio_mes_5"];

                $hora_entrada1=$_POST["hora_e"].":".$_POST["min_e"].":00";
                $hora_salida1=$_POST["hora_s"].":".$_POST["min_s"].":00";;
                $hora_entrada2=$_POST["hora_e1"].":".$_POST["min_e1"].":00";;
                $hora_salida2=$_POST["hora_s1"].":".$_POST["min_s1"].":00";;

                $hora_entrada1_mj=$_POST["hora_e_mj"].":".$_POST["min_e_mj"].":00";
                $hora_salida1_mj=$_POST["hora_s_mj"].":".$_POST["min_s_mj"].":00";;
                $hora_entrada2_mj=$_POST["hora_e1_mj"].":".$_POST["min_e1_mj"].":00";;
                $hora_salida2_mj=$_POST["hora_s1_mj"].":".$_POST["min_s1_mj"].":00";;

                $division_horas=$_POST["division_horas"];
                $periodo_gracia=$_POST["periodo_gracia"];
                        
                        if (strlen($id_centro)!=0) {
                        // Guarda cambios
                        db_query("UPDATE centros SET hora_entrada1_mj='$hora_entrada1_mj', hora_salida1_mj='$hora_salida1_mj', hora_entrada2_mj='$hora_entrada2_mj', hora_salida2_mj='$hora_salida2_mj', hora_entrada1='$hora_entrada1', hora_salida1='$hora_salida1', hora_entrada2='$hora_entrada2', hora_salida2='$hora_salida2', division_horas=$division_horas, periodo_gracia=$periodo_gracia, nombre_centro='$db_nombre_centro', prefijo_centro='$prefijo_centro', domicilio_centro='$domicilio_centro', CIF_centro='$CIF_centro', CP_centro='$CP_centro', poblacion_centro='$poblacion_centro', telefono_centro='$telefono_centro', fax_centro='$fax_centro', link_centro='$link_centro', razon_social='$razon_social' WHERE id_centro=$id_centro;") or die(db_error());
                        } else {
                        // Introduce nuevo centro
                        db_query_params('INSERT INTO centros (nombre_centro, prefijo_centro, domicilio_centro, CIF_centro, CP_centro, poblacion_centro, telefono_centro, fax_centro, link_centro, razon_social, precio_hora_extra, precio_menu_1, precio_menu_2, precio_menu_3, precio_menu_4, precio_menu_5, precio_semana_1, precio_semana_2, precio_semana_3, precio_semana_4, precio_semana_5, precio_quincena_1, precio_quincena_2, precio_quincena_3, precio_quincena_4, precio_quincena_5, precio_mes_1, precio_mes_2, precio_mes_3, precio_mes_4, precio_mes_5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssssssssssssssssssssssssss', array((string)($db_nombre_centro), (string)($prefijo_centro), (string)($domicilio_centro), (string)($CIF_centro), (string)($CP_centro), (string)($poblacion_centro), (string)($telefono_centro), (string)($fax_centro), (string)($link_centro), (string)($razon_social), (string)($precio_hora_extra), (string)($precio_menu_1), (string)($precio_menu_2), (string)($precio_menu_3), (string)($precio_menu_4), (string)($precio_menu_5), (string)($precio_semana_1), (string)($precio_semana_2), (string)($precio_semana_3), (string)($precio_semana_4), (string)($precio_semana_5), (string)($precio_quincena_1), (string)($precio_quincena_2), (string)($precio_quincena_3), (string)($precio_quincena_4), (string)($precio_quincena_5), (string)($precio_mes_1), (string)($precio_mes_2), (string)($precio_mes_3), (string)($precio_mes_4), (string)($precio_mes_5))) or die(db_error());
                        $id_centro=db_insert_id();
                        }
                        db_query("UPDATE centros SET nombre_completo='$nombre_completo_centro', titular='$titular', codigo_centro='$codigo_centro', hora_entrada1='$hora_entrada1', hora_salida1='$hora_salida1', hora_entrada2='$hora_entrada2', hora_salida2='$hora_salida2', division_horas=$division_horas, periodo_gracia=$periodo_gracia, precio_hora_extra='$precio_hora_extra', precio_menu_1='$precio_menu_1', precio_menu_2='$precio_menu_2', precio_menu_3='$precio_menu_3', precio_menu_4='$precio_menu_4', precio_menu_5='$precio_menu_5', precio_semana_1='$precio_semana_1', precio_semana_2='$precio_semana_2', precio_semana_3='$precio_semana_3', precio_semana_4='$precio_semana_4', precio_semana_5='$precio_semana_5', precio_quincena_1='$precio_quincena_1', precio_quincena_2='$precio_quincena_2', precio_quincena_3='$precio_quincena_3', precio_quincena_4='$precio_quincena_4', precio_quincena_5='$precio_quincena_5', precio_mes_1='$precio_mes_1', precio_mes_2='$precio_mes_2', precio_mes_3='$precio_mes_3', precio_mes_4='$precio_mes_4', precio_mes_5='$precio_mes_5' WHERE id_centro=$id_centro;") or die(db_error());
                        }
        
if (strlen($id_centro)!=0) {
        
                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro));
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);
                        
                        $db_nombre_centro=$datos_centro["nombre_centro"];

                        }


?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Edición del Centro <i><?=$db_nombre_centro?></i></b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=5 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listado de aulas</b></font></td>
                        </tr>
                         <?php 
                         $curso_actual="";
                         $consulta_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_centro=? ORDER BY id_curso,nombre;', 's', array($id_centro)) or die(db_error());
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($aulas["id_curso"]));
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                }
                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=4 align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>">Curso: <?=$nombre_curso_academico;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Mín.</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$nombre_curso_academico;
                        }
                         ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["nombre"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["capacidad"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["ocupacion_minima"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="editar_aula.php?id_aula=<?=$aulas["id_aula"];?>"><img src="editar.gif" border=0></a></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                        </table>
                        <input type="button" onclick="window.location.href='editar_aula.php?id_centro_aula_nueva=<?=$id_centro?>'" value="Agregar aula" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
} else {
?>
<br><br>Error: <a href="/index.php">Volver a la página de inicio</a></br></br>
<?php 
}
 require("../../configuracion/parte_inferior.php"); ?>
