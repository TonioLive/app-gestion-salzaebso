<?php 
function conversor_segundos($seg_ini,$formato) {

        $horas = floor($seg_ini/3600);
         $minutos = floor(($seg_ini-($horas*3600))/60);
         $segundos = $seg_ini-($horas*3600)-($minutos*60);
         $minus="00".$minutos;         
         
         if ($formato==0) {                
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m:'.'s';
                 } else {        
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m';
                 }
         
         return $resultado;

        }

$tipo_incidencias=0;

// Empezamos
$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Legumbres","Lacteos");
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_proternera","alergias_frutossecos","alergias_marisco","alergias_pielmelocoton","alergias_gluten","alergias_pescado","alergias_soja","alergias_sulfitos");
$nombres_alergias=array("Lacteos","Huevo","Proteina de la ternera","Frutos secos","Marisco","Piel de melocoton","Celiaco");

?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="background: #ffffff; border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#cdcdcd">
                                <table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                                <?php 
                                $pagina_act=$contador_capas;
                                for ($contador_pagi=0;$contador_pagi<count($array_paginas);$contador_pagi++) {

                                if (substr($nombres_partes[$contador_pagi],0,5)!="link:") {

                                        if ($pagina_act==$contador_pagi) {
                                ?>
                                <td bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><b> <?=$array_paginas[$contador_pagi]?></b></font></td>
                                <?php 
                                        } else {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='javascript:mostrar(<?=$contador_pagi?>)'><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                <?php 
                                        }
                                        } else {
                                        ?>
                                        <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='<?=substr($nombres_partes[$contador_pagi],5,strlen($nombres_partes[$contador_pagi])-5);?>?id_aula=<?=$id_aula?>' target="_new"><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                        <?php 
                                        }
                                }
                                ?>
                                </tr></table>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Control de comidas, <i><?=$datos_aula["nombre"];?></i></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>">
                                

<div align="center"><center>
&nbsp;
<?php  include("seguimiento.php"); ?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 
$alimentos_ya_marcados=array();
$alergias_ya_marcadas=array();

$platos_no_introducidos=array();
$platos_alaergias=array();

$turno=0;
$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'ssss', array($centro_actual, $id_aula, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=9><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Almuerzo</b></font></td>
        <td align="center" colspan=5><font size=1 face="<?= $fuente_general; ?>"><b>Comida</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Postre</b></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Merienda</b></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Siestas</b></font></td>
        <td><font size=1><a href="javascript:observaciones_todos()">Agregar observaciones a todos</a></font></td>
        </tr>
        <form name="listado_comidas" method="POST">
        <?php 
        $fila_par_impar=0;
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

                        // Busca registro de comida
                        $busca_registro_comida=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id_alumno=? AND dia=?', 'ss', array($listado_alumnos["id_alumno"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                        if (db_num_rows($busca_registro_comida)==0) {
                                // No hay? Lo creo
                                $nuevo_registro_comida=db_query_params('INSERT INTO agenda_alumnos_comidas (id_alumno,dia,id_aula) VALUES (?,?,?);', 'sss', array((string)($listado_alumnos["id_alumno"]), (string)(date("Y-m-d", $fecha_time_stamp)), (string)($id_aula))) or die(db_error());
                                $id_registro_comida=db_insert_id();
                                $busca_registro_comida=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id=?', 's', array($id_registro_comida));
                                }

                        $datos_registro_comida=db_fetch_array($busca_registro_comida);

                        $id_registro_comida=$datos_registro_comida["id"];
                        $id_alumno=$listado_alumnos["id_alumno"];

        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#acacac";
                } else {
                $color_fuente_linea="#000000";
                }
                
        $color_fondo_fila=$colores_asistencia[$listado_alumnos["id_usuario"]]; // "#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila=$colores_asistencia_par[$listado_alumnos["id_usuario"]]; // "#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;        
        ?>
        <tr valign="top" bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
        <td valign="top"><font size=2 face="<?= $fuente_general; ?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></font></td>
        <?php 
        if ($fecha_baja<date("Y-m-d")) {
        ?>
                <td align="center" colspan=9><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
                </tr>
        <?php 
                } else {

                // Busca si ya está registrado
                $busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND turno=0 AND id_servicio=? AND fecha=?', 'sss', array($listado_alumnos["id_usuario"], $id_aula, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                if (db_num_rows($busca_registro_entrada)==0) {
                ?>
                <td align="center" colspan=11><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <?php 
                } else {
                ?>
                        <td align='center' width='150' valign='bottom'>
                                <br><select name='cant_almuerzo_<?=$id_registro_comida?>' id='cant_almuerzo_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_almuerzo'},{desc:'valor',dato:document.getElementById('cant_almuerzo_<?=$id_registro_comida?>').value});">
                                        <option value="0"<?php  if ($datos_registro_comida["cant_almuerzo"]==0) { print " selected"; } ?>>Toda</option>
                                        <option value="1"<?php  if ($datos_registro_comida["cant_almuerzo"]==1) { print " selected"; } ?>>Mitad</option>
                                        <option value="2"<?php  if ($datos_registro_comida["cant_almuerzo"]==2) { print " selected"; } ?>>Poca</option>
                                        <option value="3"<?php  if ($datos_registro_comida["cant_almuerzo"]==3) { print " selected"; } ?>>Nada</option>
                                        <option value="4"<?php  if ($datos_registro_comida["cant_almuerzo"]==4) { print " selected"; } ?>>No trae</option>
                                </select> <select name='almuerzo_<?=$id_registro_comida?>' id='almuerzo_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'almuerzo'},{desc:'valor',dato:document.getElementById('almuerzo_<?=$id_registro_comida?>').value});">
                                        <option value="0"<?php  if ($datos_registro_comida["almuerzo"]==0) { print " selected"; } ?>>Bien</option>
                                        <option value="1"<?php  if ($datos_registro_comida["almuerzo"]==1) { print " selected"; } ?>>Reg.</option>
                                        <option value="2"<?php  if ($datos_registro_comida["almuerzo"]==2) { print " selected"; } ?>>Mal</option>
                                </select>
                        </td>
                <?php 
                $datos_registro=db_fetch_array($busca_registro_entrada);
                
                $tipo_menu_hoy=$datos_registro["tipo_menu"];

        // Comprueba si está inscrito en una actividad que anula comedor
        $busca_actividad=db_query_params('SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=? AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha=?', 'ss', array($listado_alumnos["id_usuario"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $tipo_menu_alu=$listado_alumnos["tipo_menu"];

        $menu_especial_hoy=false;
        if (db_num_rows($busca_actividad)!=0) {
                $menu_especial_hoy=true;
                $tipo_menu_alu=1;
                }

                if ($tipo_menu_hoy==2) {

                        // busca si ha asistido a comida especial
                        $busca_comida_navidad_2015=db_query_params('SELECT * FROM servicios_consumidos_asistencia, tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=? AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha=?', 'ss', array($listado_alumnos["id_usuario"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                        if (db_num_rows($busca_comida_navidad_2015)!=0) {
                        
                                $datos_registro_navidad_2015=db_fetch_array($busca_comida_navidad_2015);
                                if ($datos_registro_navidad_2015["hora_entrada"]!="") {
                                        $tipo_menu_hoy=0;
                                        }
                        
                                }
                        }

                if ($tipo_menu_hoy==2) {
                        ?>
                        <td align="center" colspan=6><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        <td align='center' width='150' valign='bottom'>
                                <br><select name='cant_merienda1_<?=$id_registro_comida?>' id='cant_merienda1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_merienda1'},{desc:'valor',dato:document.getElementById('cant_merienda1_<?=$id_registro_comida?>').value});">
                                <option value="0"<?php  if ($datos_registro_comida["cant_merienda1"]==0) { print " selected"; } ?>>Toda</option>
                                <option value="1"<?php  if ($datos_registro_comida["cant_merienda1"]==1) { print " selected"; } ?>>Mitad</option>
                                <option value="2"<?php  if ($datos_registro_comida["cant_merienda1"]==2) { print " selected"; } ?>>Poca</option>
                                <option value="3"<?php  if ($datos_registro_comida["cant_merienda1"]==3) { print " selected"; } ?>>Nada</option>
                                <option value="4"<?php  if ($datos_registro_comida["cant_merienda1"]==4) { print " selected"; } ?>>No trae</option>
                                </select> <select name='meirenda1_<?=$id_registro_comida?>' id='meirenda1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'merienda1'},{desc:'valor',dato:document.getElementById('merienda1_<?=$id_registro_comida?>').value});">
                                <option value="0"<?php  if ($datos_registro_comida["merienda1"]==0) { print " selected"; } ?>>Bien</option>
                                <option value="1"<?php  if ($datos_registro_comida["merienda1"]==1) { print " selected"; } ?>>Reg.</option>
                                <option value="2"<?php  if ($datos_registro_comida["merienda1"]==2) { print " selected"; } ?>>Mal</option>
                                </select>
                        </td>
                        <td align='center' width='150' valign='bottom'>                                
                                &nbsp;
                        </td>                        
<?php 
// Siestas
                ?>
                                <td align='center' valign='bottom'><font size=1>1ª Siesta<br><select name='siesta1_<?=$id_registro_comida?>' id='siesta1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'siesta1'},{desc:'valor',dato:document.getElementById('siesta1_<?=$id_registro_comida?>').value});">
                                                <option value="0"<?php  if ($datos_registro_comida["siesta1"]==0) { print " selected"; } ?>>No duerme</option>
                                                <?php 
                                                for ($minutos_siesta=15;$minutos_siesta<=120;$minutos_siesta=$minutos_siesta+15) {
                                                ?>
                        <option value="<?=$minutos_siesta?>"<?php  if ($datos_registro_comida["siesta1"]==$minutos_siesta) { print " selected"; } ?>><?=conversor_segundos($minutos_siesta*60,1);?></option>
                                                <?php 
                                                }
                                                ?>
                        </select></font></td>
                                <td align='center' valign='bottom'><font size=1'>2ª Siesta<br><select name='siesta2_<?=$id_registro_comida?>' id='siesta2_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'siesta2'},{desc:'valor',dato:document.getElementById('siesta2_<?=$id_registro_comida?>').value});">
                                                <option value="0"<?php  if ($datos_registro_comida["siesta2"]==0) { print " selected"; } ?>>No duerme</option>
                                                <?php 
                                                for ($minutos_siesta=15;$minutos_siesta<=120;$minutos_siesta=$minutos_siesta+15) {
                                                ?>
                        <option value="<?=$minutos_siesta?>"<?php  if ($datos_registro_comida["siesta2"]==$minutos_siesta) { print " selected"; } ?>><?=conversor_segundos($minutos_siesta*60,1);?></option>
                                                <?php 
                                                }
                                                ?>
                        </select></font></td>
                        <?php 
                        $texto_observaciones_linea=$datos_registro_comida["observaciones_comida"];
                        if (strlen($texto_observaciones_linea)==0) {
                                $texto_observaciones_linea="Agregar observaciones";
                                }
                        ?>
                        <td width=100 align="center"><font size=1><a href="javascript:observaciones_linea(<?=$id_registro_comida?>)"><?=$texto_observaciones_linea?></a></font></td>
                        <?php 
                        } else {

                        // Busca menu para hoy
                        $id_menu=$datos_registro["menu_seleccionado_hoy"];

                        if ($menu_especial_hoy==true) {
                                        // Busca menu especial
                                        $filtro_regimen=" AND menu_menus.regimen=2";


                                        $edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                        if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                                $edad_alumno=$edad_alumno-1;
                                                }
                                        $edad_alumno_anos=$edad_alumno;
                                        // Decimales
                                        $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                                        if ($mes_edad_alumno<0) {
                                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                                } else {
                                                $edad_alumno_meses=$mes_edad_alumno;
                                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                                }

                                        // Busca menu especial
                                        if  ($listado_alumnos["menu_actual"]!=0) {

                                                $busca_menu_preseleccionado=db_query("SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=".$listado_alumnos["menu_actual"]);
                                                if (db_num_rows($busca_menu_preseleccionado)!=0) {

                                                        $datos_menu_preseleccionada=db_fetch_array($busca_menu_preseleccionado);
                                                        $edad_alumno=$datos_menu_preseleccionada["edad_ini_menu"];

                                                }

                                        } else {

                                        // Busca por edad
                                        $edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                        if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                                $edad_alumno=$edad_alumno-1;
                                                }
                                        $edad_alumno_anos=$edad_alumno;
                                        // Decimales
                                        $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                                        if ($mes_edad_alumno<0) {
                                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                                } else {
                                                $edad_alumno_meses=$mes_edad_alumno;
                                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                                }

                                        }

                                        $filtro_regimen=" AND menu_menus.regimen=2";

                                        $busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
                                        $diferencia_actual=1000;
                                        $id_menu_seleccionado=0;

                                        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                                                if ($minimo_minimorum>$menu_apropiado["edad_ini_menu"]) {
                                                        $minimo_minimorum=$menu_apropiado["edad_ini_menu"];
                                                        }
                                                $diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
                                                if ($diferencia<=$diferencia_actual) {
                                                        $diferencia_actual=$diferencia;
                                                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                                                        }
                                                }

                                        $id_menu=$id_menu_seleccionado;
                                        } else {
                                        $id_menu=$datos_registro["menu_seleccionado_hoy"];
                                        }

                        if ($id_menu==0) {
                        
/**************************************************************/
                                // Comprueba menu

                        // Edad actual del niño en años con decimales
                        $edad_para_menu=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                        if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                $edad_para_menu=$edad_para_menu-1;
                                }
                        $edad_para_menu_anos=$edad_para_menu;
                        // Decimales
                        $mes_edad_para_menu=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                        if ($mes_edad_para_menu<0) {
                                $edad_para_menu_meses=(12+$mes_edad_para_menu);
                                $edad_para_menu=$edad_para_menu+round(((12+$mes_edad_para_menu)/12),2);
                                } else {
                                $edad_para_menu_meses=$mes_edad_para_menu;
                                $edad_para_menu=$edad_para_menu+round(($mes_edad_para_menu/12),2);
                                }
                                        if ($edad_para_menu_anos!=1) {
                                                $plural_anios="s";
                                                } else {
                                                $plural_anios="";
                                                }
                                        if ($edad_para_menu_meses>1) {
                                                $plural_meses="es";
                                                } else {
                                                $plural_meses="";
                                                }

                // Comprueba si tiene un tipo de menu seleccionado

                $menu_actual_en_ficha=0;

                $razon="";

                if ($menu_actual_en_ficha>0) {
                        $id_menu_seleccionado=$menu_actual_en_ficha;
                        } else {
                        if ($menu_actual_en_ficha==0) {

                        // Comprueba si es de regimen
                        $regimen=$listado_alumnos["comedor"];
                        $filtro_regimen=" AND menu_menus.regimen=2";
                        /*
                        if ($regimen==3) {
                                $filtro_regimen=" AND menu_menus.regimen=1";
                                $razon=$razon."Menú de regimen";
                                } else {
                                $filtro_regimen=" AND menu_menus.regimen=0";
                                }
                        */

                        // Calcula el menú que le toca por edades
                        $busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_para_menu$filtro_regimen");

                        $diferencia_actual=1000;
                        $id_menu_seleccionado=0;

                        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                                if ($minimo_minimorum>$menu_apropiado["edad_ini_menu"]) {
                                        $minimo_minimorum=$menu_apropiado["edad_ini_menu"];
                                        }
                                $diferencia=$edad_para_menu-$menu_apropiado["edad_ini_menu"];
                                if ($diferencia<=$diferencia_actual) {
                                        $diferencia_actual=$diferencia;
                                        $edad_menu_seleccionado=$menu_apropiado["edad_ini_menu"];
                                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                                        }
                                        }
                                }
                        }

                $id_menu=$id_menu_seleccionado;

/**************************************************************/

                }

                        
$busca_platos_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $fecha_time_stamp))));
if (db_num_rows($busca_platos_dia)!=0) {

                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_platos_dia[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {

                                                $consulta_datos_plato=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes, gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        if ($alimentos_ya_marcados[$codigo_plato][$id_alumno]!=1) {
                                                                                                $platos_no_introducidos[$c][$id_alumno]=$platos_no_introducidos[$c][$id_alumno].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
                                                                                                $alimentos_ya_marcados[$codigo_plato][$id_alumno]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        if ($alergias_ya_marcadas[$codigo_plato][$id_alumno]!=1) {
                                                                                                $platos_alaergias[$c][$id_alumno]=$platos_alaergias[$c][$id_alumno].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
                                                                                                $alergias_ya_marcadas[$codigo_plato][$id_alumno]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }
                                                                }

                                                        }

                                                }
                                                }
                // Edita el nuevo día
                $actualizar=false;
                $id_dia_menu=$datos_platos_dia["id_dia"];
                $comida1=$datos_platos_dia["comida1"];
                $comida2=$datos_platos_dia["comida2"];
                $comida3=$datos_platos_dia["comida3"];
                $comida4=$datos_platos_dia["comida4"];
                $comida5=$datos_platos_dia["comida5"];
                $postre1=$datos_platos_dia["postre1"];
                $merienda1=$datos_platos_dia["merienda1"];
                $merienda2=$datos_platos_dia["merienda2"];

        $color_alerta="#FF0000";

        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida1][$id_alumno]==1) or ($alimentos_ya_marcados[$comida1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_comida1_<?=$id_registro_comida?>' id='cant_comida1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_comida1'},{desc:'valor',dato:document.getElementById('cant_comida1_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_comida1"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_comida1"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_comida1"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_comida1"]==3) { print " selected"; } ?>>Nada</option>
                        </select> <select name='comida1_<?=$id_registro_comida?>' id='comida1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'comida1'},{desc:'valor',dato:document.getElementById('comida1_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["comida1"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["comida1"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["comida1"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida2][$id_alumno]==1) or ($alimentos_ya_marcados[$comida2][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_comida2_<?=$id_registro_comida?>' id='cant_comida2_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_comida2'},{desc:'valor',dato:document.getElementById('cant_comida2_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_comida2"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_comida2"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_comida2"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_comida2"]==3) { print " selected"; } ?>>Nada</option>
                        </select> <select name='comida2_<?=$id_registro_comida?>' id='comida2_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'comida2'},{desc:'valor',dato:document.getElementById('comida2_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["comida2"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["comida2"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["comida2"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida3][$id_alumno]==1) or ($alimentos_ya_marcados[$comida3][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_comida3_<?=$id_registro_comida?>' id='cant_comida3_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_comida3'},{desc:'valor',dato:document.getElementById('cant_comida3_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_comida3"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_comida3"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_comida3"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_comida3"]==3) { print " selected"; } ?>>Nada</option>
                        </select> <select name='comida3_<?=$id_registro_comida?>' id='comida3_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'comida3'},{desc:'valor',dato:document.getElementById('comida3_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["comida3"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["comida3"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["comida3"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida4][$id_alumno]==1) or ($alimentos_ya_marcados[$comida4][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_comida4_<?=$id_registro_comida?>' id='cant_comida4_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_comida4'},{desc:'valor',dato:document.getElementById('cant_comida4_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_comida4"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_comida4"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_comida4"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_comida4"]==3) { print " selected"; } ?>>Nada</option>
                        </select> <select name='comida4_<?=$id_registro_comida?>' id='comida4_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'comida4'},{desc:'valor',dato:document.getElementById('comida4_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["comida4"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["comida4"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["comida4"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida5][$id_alumno]==1) or ($alimentos_ya_marcados[$comida5][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_comida5_<?=$id_registro_comida?>' id='cant_comida5_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_comida5'},{desc:'valor',dato:document.getElementById('cant_comida5_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_comida5"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_comida5"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_comida5"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_comida5"]==3) { print " selected"; } ?>>Nada</option>
                        </select> <select name='comida5_<?=$id_registro_comida?>' id='comida5_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'comida5'},{desc:'valor',dato:document.getElementById('comida5_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["comida5"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["comida5"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["comida5"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$postre1][$id_alumno]==1) or ($alimentos_ya_marcados[$postre1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_postre1_<?=$id_registro_comida?>' id='cant_postre1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_postre1'},{desc:'valor',dato:document.getElementById('cant_postre1_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_postre1"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_postre1"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_postre1"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_postre1"]==3) { print " selected"; } ?>>Nada</option>
                        </select> <select name='postre1_<?=$id_registro_comida?>' id='postre1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'postre1'},{desc:'valor',dato:document.getElementById('postre1_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["postre1"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["postre1"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["postre1"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$merienda1][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_merienda1_<?=$id_registro_comida?>' id='cant_merienda1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_merienda1'},{desc:'valor',dato:document.getElementById('cant_merienda1_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_merienda1"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_merienda1"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_merienda1"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_merienda1"]==3) { print " selected"; } ?>>Nada</option>
                        <option value="4"<?php  if ($datos_registro_comida["cant_merienda1"]==4) { print " selected"; } ?>>No toma</option>
                        </select> <select name='merienda1_<?=$id_registro_comida?>' id='merienda1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'merienda1'},{desc:'valor',dato:document.getElementById('merienda1_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["merienda1"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["merienda1"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["merienda1"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$merienda2][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda2][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<td align='center' width='150' valign='bottom'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                ?>
                        <br><select name='cant_merienda2_<?=$id_registro_comida?>' id='cant_merienda2_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'cant_merienda2'},{desc:'valor',dato:document.getElementById('cant_merienda2_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["cant_merienda2"]==0) { print " selected"; } ?>>Toda</option>
                        <option value="1"<?php  if ($datos_registro_comida["cant_merienda2"]==1) { print " selected"; } ?>>Mitad</option>
                        <option value="2"<?php  if ($datos_registro_comida["cant_merienda2"]==2) { print " selected"; } ?>>Poca</option>
                        <option value="3"<?php  if ($datos_registro_comida["cant_merienda2"]==3) { print " selected"; } ?>>Nada</option>
                        <option value="4"<?php  if ($datos_registro_comida["cant_merienda2"]==4) { print " selected"; } ?>>No toma</option>
                        </select> <select name='merienda2_<?=$id_registro_comida?>' id='merienda2_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'merienda2'},{desc:'valor',dato:document.getElementById('merienda2_<?=$id_registro_comida?>').value});">
                        <option value="0"<?php  if ($datos_registro_comida["merienda2"]==0) { print " selected"; } ?>>Bien</option>
                        <option value="1"<?php  if ($datos_registro_comida["merienda2"]==1) { print " selected"; } ?>>Reg.</option>
                        <option value="2"<?php  if ($datos_registro_comida["merienda2"]==2) { print " selected"; } ?>>Mal</option>
                        </select>
                </td>
                <?php 
                } else {
                        echo "<td align='center'><font size=1 color='$color_fuente_linea'>&nbsp;</font></td>";
                }
        }                        

// Siestas
                ?>
                                <td align='center' valign='bottom'><font size=1>1ª Siesta<br><select name='siesta1_<?=$id_registro_comida?>' id='siesta1_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'siesta1'},{desc:'valor',dato:document.getElementById('siesta1_<?=$id_registro_comida?>').value});">
                                                <option value="0"<?php  if ($datos_registro_comida["siesta1"]==0) { print " selected"; } ?>>No duerme</option>
                                                <?php 
                                                for ($minutos_siesta=15;$minutos_siesta<=120;$minutos_siesta=$minutos_siesta+15) {
                                                ?>
                        <option value="<?=$minutos_siesta?>"<?php  if ($datos_registro_comida["siesta1"]==$minutos_siesta) { print " selected"; } ?>><?=conversor_segundos($minutos_siesta*60,1);?></option>
                                                <?php 
                                                }
                                                ?>
                        </select></font></td>
                                <td align='center' valign='bottom'><font size=1>2ª Siesta<br><select name='siesta2_<?=$id_registro_comida?>' id='siesta2_<?=$id_registro_comida?>' style='font-size:10px' size=1 onchange="sendAndLoadComida({desc:'fila',dato:<?=$id_registro_comida?>},{desc:'campo',dato:'siesta2'},{desc:'valor',dato:document.getElementById('siesta2_<?=$id_registro_comida?>').value});">
                                                <option value="0"<?php  if ($datos_registro_comida["siesta2"]==0) { print " selected"; } ?>>No duerme</option>
                                                <?php 
                                                for ($minutos_siesta=15;$minutos_siesta<=120;$minutos_siesta=$minutos_siesta+15) {
                                                ?>
                        <option value="<?=$minutos_siesta?>"<?php  if ($datos_registro_comida["siesta2"]==$minutos_siesta) { print " selected"; } ?>><?=conversor_segundos($minutos_siesta*60,1);?></option>
                                                <?php 
                                                }
                                                ?>
                        </select></font></td>
                        <?php 
                        $texto_observaciones_linea=$datos_registro_comida["observaciones_comida"];
                        if (strlen($texto_observaciones_linea)==0) {
                                $texto_observaciones_linea="Agregar observaciones";
                                }
                        ?>
                        <td width=100 align="center"><font size=1><a href="javascript:observaciones_linea(<?=$id_registro_comida?>)"><?=$texto_observaciones_linea?></a></font></td>
                        <?php 
                        }
                }
                ?></tr>
                <?php 

            }

        }
        ?>
        </form>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>No está prevista la asistencia al comedor de ningún alumno.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
                                
                                </font></td>
                        </tr>
                </table>
<SCRIPT LANGUAGE=javascript>
<!--

function observaciones_linea(id_fila) {
        editar_observaciones=window.open("editar_observaciones_fila.php?id_fila="+id_fila,"observaciones","width=640,height=350,resizable=yes,scrollbars=yes,toolbar=no");
        }
function observaciones_todos() {
        editar_observaciones=window.open("editar_observaciones_todos.php?id_aula=<?=$id_aula?>&dia=<?=$fecha_time_stamp?>","observaciones","width=640,height=350,resizable=yes,scrollbars=yes,toolbar=no");
        }

//-->
</SCRIPT>
