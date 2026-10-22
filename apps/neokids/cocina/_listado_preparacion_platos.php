<?php  require("../configuracion/parte_superior_sin_login_ancho.php"); ?>
<?php 

$solo_escuela=$_GET["solo_escuela"];
if (strlen($solo_escuela)==0) {
        $solo_escuela=0;
        }

$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_proternera","alergias_frutossecos","alergias_marisco","alergias_pielmelocoton","alergias_gluten");
$nombres_alergias=array("lacteos","huevo","proteina de la ternera"," frutos secos"," marisco"," piel de melocoton","celiaco");

$edad_menu=2;

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

$estado_botones="";
if (date("d-m-Y",$fecha_time_stamp)!=date("d-m-Y")) {
        $estado_botones=" disabled";
        }
        
$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

?>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br><br><big>Asistencia Comedor</big></i><br><br>Escuela: <?=$nombre_centro_actual;?></b><br><br></small></td>
  </tr>
<?php 
if ($solo_escuela==1) {
        $busca_alumnos=db_query_params('SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.id_centro=? AND servicios_consumidos.fecha=? AND servicios_consumidos.tipo_menu<>2 ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno', 'ss', array($centro_actual, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
        } else {
        $busca_alumnos=db_query_params('SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha=? AND servicios_consumidos.tipo_menu<>2 ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
        }

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <?php 
        $totales_menu=array();
        $totales_platos=array();

        $totales_menu_escuelas=array();
        $totales_platos_escuelas=array();
        
        $totales_menu_aulas=array();
        $totales_platos_aulas=array();

        $totales_ingredientes=array();
        
        $usuario_anterior=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        if (($listado_alumnos["tipo_menu"]>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM centros,centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=?', 's', array($listado_alumnos["aula"]));
        $datos_centro=db_fetch_array($busca_datos_centro);
        
        $nombre_centro_alumno=$datos_centro["nombre_centro"];
        $nombre_aula_alumno=$datos_centro["nombre"];


        $nombre_centro_alumno=$datos_centro["nombre_centro"];
        
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }

        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$listado_alumnos["menu_seleccionado_hoy"]);
        if (db_num_rows($busca_datos_menu)!=0) {
                $id_menu_seleccionado=$listado_alumnos["menu_seleccionado_hoy"];
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);
                
                // Comprueba los alimentos del menú y comprueba las alergias del niño
                
                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?;', 'ss', array($id_menu_seleccionado, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                                $motivo_no_hay="";
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {
                                                $sumar_plato=true;
                                                $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                        $numero_ingredientes=db_num_rows($busca_ingredientes);

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alimento NO introducido: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alergia a $nombre_alergia: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }
                                                                if ($sumar_plato==true) {
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+$ingredientes["cantidad_racion"];
                                                                        }
                                                                }
                                                        if ($sumar_plato==true) {
                                                        $edad_ini_actual=$datos_plato["edad_ini_plato"];
                                                                if ($edad_ini_actual<2) {
                                                                        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
                                                                        } else {
                                                                        $edad_ini_actual=$edad_ini_actual." años";
                                                                        }
                                                                $edad_fin_actual=$datos_plato["edad_fin_plato"];
                                                                if ($edad_fin_actual<2) {
                                                                        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
                                                                        } else {
                                                                        if ($edad_fin_actual==100) {
                                                                                                $edad_fin_actual=">18";
                                                                                                }
                                                                        $edad_fin_actual=$edad_fin_actual." años";
                                                                        }
                                                                $edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                                                $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                                $totales_platos[$codigo_plato][2]=$numero_ingredientes;
                                                                
                                                                $totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]=$totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]+1;
                                                                
                                                                $totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]=$totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]+1;
                                                                
                                                                }
                                                        }

                                                }
                                                }

                                        } else {
                                        $motivo_no_hay="Día no programado para el menú asignado: ".urldecode($datos_menu_seleccionado["nombre_menu"]);
                                        }
                
                } else {
                $nombre_menu="Menú no asignado";
                }
        
        $totales_menu[$listado_alumnos["menu_seleccionado_hoy"]]=$totales_menu[$listado_alumnos["menu_seleccionado_hoy"]]+1;
        $totales_menu_escuelas[$listado_alumnos["menu_seleccionado_hoy"]][$listado_alumnos["centro_alumno"]]=$totales_menu_escuelas[$listado_alumnos["menu_seleccionado_hoy"]][$listado_alumnos["centro_alumno"]]+1;
        $totales_menu_aulas[$listado_alumnos["menu_seleccionado_hoy"]][$listado_alumnos["aula"]]=$totales_menu_aulas[$listado_alumnos["menu_seleccionado_hoy"]][$listado_alumnos["aula"]]+1;
        
        
        $usuario_anterior=$listado_alumnos["id_usuario"];
                }
        }
        
        // Cuenta los menús de educador
        $menus_edu=0;
        
        $busca_menu_educadora=db_query_params('SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha=? AND asistencia_docentes.comedor=1 AND asistencia_docentes.id_centro_asistencia=? ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), $centro_actual));

        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($menu_educadora["id_centro_asistencia"]));
        if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $nombre_centro_educador=$datos_centro["nombre_centro"];
                } else {
                $nombre_centro_educador="No indicado";
                }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]);
        if (db_num_rows($busca_datos_menu)!=0) {
                $id_menu_seleccionado=$menu_educadora["menu_seleccionado_hoy"];
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);
                }
                
         $totales_menu[$menu_educadora["menu_seleccionado_hoy"]]=$totales_menu[$menu_educadora["menu_seleccionado_hoy"]]+1;
         $totales_menu_escuelas[$menu_educadora["menu_seleccionado_hoy"]][$menu_educadora["id_centro_asistencia"]]=$totales_menu_escuelas[$menu_educadora["menu_seleccionado_hoy"]][$menu_educadora["id_centro_asistencia"]]+1;
         
         
                // Comprueba los alimentos del menú y comprueba las alergias del niño

                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;
                                $observaciones_edu="";
                                $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]." AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                                $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                                if ($codigo_plato!=0) {
                                                        $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                        $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                        $totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]=$totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]+1;
                                                        }
                                                }
                                        } else {
                                        $observaciones_edu="Día no programado para el menú seleccionado";
                                        }

        $menus_edu++;
        }
        
        ?>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=7><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de menús servidos</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre menú</b></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$centros["nombre_centro"];?></b></font></td>
        <?php 
        }
        ?>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad Total</b></font></td>
        </tr>
        <?php 
        $consulta_datos_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu;") or die(db_error());
        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
        if ($totales_menu[$datos_menus["id_menu"]]>0) {

        ?>
        <tr>
                <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"><?=urldecode($datos_menus["nombre_menu"])?></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#ffffff" align="right"><?=number_format($totales_menu_escuelas[$datos_menus["id_menu"]][$centros["id_centro"]],0,",",".");?></td>
        <?php 
        }
        ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$totales_menu[$datos_menus["id_menu"]];?></font></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table><br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=8><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de platos servidos</b></font></td>
        </tr>
        <?php 
        $consulta_datos_plato=db_query("SELECT * FROM menu_platos ORDER BY nombre_plato, edad_ini_plato DESC") or die(db_error());
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {
        
        $codigo_plato=$datos_plato["id_plato"];
        if ($totales_platos[$codigo_plato][0]>0) {
        ?>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre plato</b></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
        $numero_centros=db_num_rows($busca_centros);
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$centros["nombre_centro"];?></b></font></td>
        <?php 
        }
        ?>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad total</b></font></td>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Instrucciones para servir</b></font></td>
        </tr>
        <tr>
                <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$totales_platos[$codigo_plato][1];?></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=number_format($totales_platos_escuelas[$codigo_plato][$centros["id_centro"]],0,",",".");?></font></td>
        <?php 
        }
        ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$totales_platos[$codigo_plato][0];?></font></td>
                <td bgcolor="#ffffff" align="left"><font size=1 face="<?= $fuente_general; ?>"> <?=urldecode($datos_plato["instrucciones_preparacion"]);?></font></td>
        </tr>
        <tr>
                <td bgcolor="#efefef" align="right" colspan="<?=$numero_centros+1?>"><font size=2 face="<?= $fuente_general; ?>"><b>Ingrediente</b></font></td>
                <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"> <b>Cantidad Peso</b></font></td>
                <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"> <b>Cantidad Unidades</b></font></td>
        </tr>
        <?php 
        // Muestra ingredientes necesarios
        $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

        $numero_ingredientes=db_num_rows($busca_ingredientes);

        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                $nombre_ingrediente=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1];
                $unidad_de_medida=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2];
                $peso_unidad=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3];
                $cantidad_raciones=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0];
                if ($unidad_de_medida==0) {
                                        $coletilla_unidad="gr.";
                                        } else {
                                        $coletilla_unidad="ml.";
                                        }
                if ($peso_unidad>0) {
                        $unidades_prod=$cantidad_raciones/$peso_unidad;
                        if ($unidades_prod>round($unidades_prod,0)) {
                                $unidades_prod=round($unidades_prod,0)+1;
                                } else {
                                $unidades_prod=round($unidades_prod,0);
                                }
                        } else {
                        $unidades_prod=$cantidad_raciones;
                        }
                // Ingredientes por marcas
                $busca_marcas=db_query_params('SELECT * FROM tarifa WHERE ID_ALIMENTO_GENERICO=?', 's', array($ingredientes["id_alimento"]));
                $lista_marcas="";
                if (db_num_rows($busca_marcas)!=0) {
                        while($marcas=db_fetch_array($busca_marcas)) {
                                $unidades_marca_actual=$marcas["UNIDAD"];
                                if (is_numeric($unidades_marca_actual)) {
                                        if ($unidades_marca_actual>0) {
                                                $total_unidades_marca=$cantidad_raciones/$unidades_marca_actual;
                                                if ($total_unidades_marca>round($total_unidades_marca,0)) {
                                                        $total_unidades_marca=round($total_unidades_marca,0)+1;
                                                        } else {
                                                        $total_unidades_marca=round($total_unidades_marca,0);
                                                        }
                                                }
                                        $lista_marcas=$lista_marcas.$total_unidades_marca." de ".$marcas["ARTICULO"]." ó ";
                                        }
                                }
                        if (strlen($lista_marcas)!=0) {
                                $lista_marcas="<br>".substr($lista_marcas,0,strlen($lista_marcas)-3);
                                }
                        }
                
                ?>
        <tr>
                <td bgcolor="#ffffff" align="right" colspan="<?=$numero_centros+1?>"><font size=2 face="<?= $fuente_general; ?>"> <?=$nombre_ingrediente;?></font></td>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=number_format($cantidad_raciones,2,",",".");?> <?=$coletilla_unidad;?></font></td>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=number_format($unidades_prod,2,",",".").$lista_marcas;?></font></td>
        </tr>
                <?php 
                }
        ?>
        <tr><td colspan="<?=($numero_centros+3);?>"><font size=1>&nbsp;</font></td></tr>
        <?php 
                }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en el comedor.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="window.close()" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

