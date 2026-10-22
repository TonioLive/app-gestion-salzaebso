<?php  require("../../configuracion/parte_superior_sin_login_ancho.php"); ?>
<?php 
$id_empleado=$_GET["id_empleado"];
$id_plato=$_GET["id_plato"];

$solo_escuela=$_GET["solo_escuela"];
if (strlen($solo_escuela)==0) {
        $solo_escuela=0;
        }

$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_proternera","alergias_frutossecos","alergias_marisco","alergias_pielmelocoton","alergias_gluten","alergias_pescado","alergias_soja","alergias_sulfitos","alergias_cerdo");
$nombres_alergias=array("lacteos","huevo","proteina de la ternera","frutos secos","marisco","piel de melocoton","celiaco","pescado","soja","sulfitos","cerdo");

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
$hay_incidencias=false;

if ($solo_escuela==1) {
        $busca_alumnos=db_query_params('SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.comedor<>2 AND alumnos.id_centro=? ORDER BY alumnos.id_centro, alumnos.aula', 'sss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)), $centro_actual)) or die(db_error());
        } else {
        $busca_alumnos=db_query_params('SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=? AND alumnos.comedor<>2 ORDER BY alumnos.id_centro, alumnos.aula', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
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
        if (($listado_alumnos["comedor"]>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM centros,centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=?', 's', array($listado_alumnos["aula"]));
        $datos_centro=db_fetch_array($busca_datos_centro);

        $nombre_centro_alumno=$datos_centro["nombre_centro"];
        $nombre_aula_alumno=$datos_centro["nombre"];

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

                // Busca menu apropiado para la edad
                        // Edad actual del niño en años con decimales
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
                                        if ($edad_alumno_anos!=1) {
                                                $plural_anios="s";
                                                } else {
                                                $plural_anios="";
                                                }
                                        if ($edad_alumno_meses>1) {
                                                $plural_meses="es";
                                                } else {
                                                $plural_meses="";
                                                }

                // Comprueba si tiene un tipo de menu seleccionado
                $menu_actual_en_ficha=$listado_alumnos["menu_actual"];
                if ($menu_actual_en_ficha==-1) {
                                if ($datos_aula["id_menu_predefinido"]!=0) {
                                        $id_menu_seleccionado=$datos_aula["id_menu_predefinido"];
                                        } else {
                                        $menu_actual_en_ficha=0;
                                        }
                                }
                if ($menu_actual_en_ficha>0) {
                        $id_menu_seleccionado=$menu_actual_en_ficha;
                        } else {
                        if ($menu_actual_en_ficha==0) {

                        // Comprueba si es de regimen
                        $regimen=$listado_alumnos["comedor"];
                        if ($regimen==3) {
                                $filtro_regimen=" AND regimen=1";
                                } else {
                                $filtro_regimen=" AND regimen=0";
                                }

                        // Calcula el menú que le toca por edades
                        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus WHERE edad_ini_menu<=$edad_alumno$filtro_regimen");
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
                                }
                        }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query_params('SELECT * FROM menu_menus WHERE id_menu=?', 's', array($id_menu_seleccionado));
        if (db_num_rows($busca_datos_menu)!=0) {
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
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
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
        
        $totales_menu[$id_menu_seleccionado]=$totales_menu[$id_menu_seleccionado]+1;
        $totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]=$totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]+1;
        $totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]=$totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]+1;
        
        if (($motivo_no_hay!="") or ($mostrar_listado==1)) {
                $total_menus++;
                $hay_incidencias=true;
                }
        
                $usuario_anterior=$listado_alumnos["id_usuario"];
                }
        }
        
        // Cuenta los menús de educador
        $menus_edu=0;
        
        $busca_menu_educadora=db_query_params('SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha=? AND asistencia_docentes.comedor=1 ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

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
        
        $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos WHERE id_plato=?', 's', array($id_plato)) or die(db_error());

        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $plato_actual=urldecode($datos_plato["nombre_plato"]);
        $total_absoluto=0;
        $contador_platos=0;
        $num_hechas_total=0;
        $nombre_recipiente_plato=urldecode($datos_plato["nombre_recipiente"]);
        
        // Busca variantes del plato
        $busca_variantes=db_query_params('SELECT * FROM menu_platos WHERE nombre_plato=? ORDER BY edad_ini_plato DESC', 's', array((string)($datos_plato["nombre_plato"]))) or die(db_error());
        
        while($datos_variantes=db_fetch_array($busca_variantes)) {
        
        $codigo_plato=$datos_variantes["id_plato"];
        
        if (intval($totales_platos[$codigo_plato][0])>0) {
        
        if ($contador_platos==0) {
        // Encabezado
        ?>
        <div style="page-break-after: always;">
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=3 face="<?= $fuente_general; ?>"><b>Preparación de Platos para el día <?=date("d/m/Y",$fecha_time_stamp)?> - Listado Provisional</b></font></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$plato_actual?></b></font></td>
        </tr>
        </table>
        <?php 
        // Encabezado ingredientes
        ?>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
                <td bgcolor="#ffffff" align="left" rowspan=3><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <?php 
        // Muestra ingredientes necesarios
        $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY menu_alimentos.id_alimento', 's', array($codigo_plato)) or die(db_error());

        $numero_ingredientes=db_num_rows($busca_ingredientes);
        $contador_ingredientes=0;
        if (isset($array_ingredientes_ordenados)) {
                unset($array_ingredientes_ordenados);
                }
        $array_ingredientes_ordenados=array();
        if (isset($total_absoluto_escuelas)) {
                unset($total_absoluto_escuelas);
                }
        $total_absoluto_escuelas=array();
        $total_absoluto=0;
        ?>
        <td bgcolor="#efefef" colspan="<?=$numero_ingredientes*2;?>" align="center"><font size=2><b>Ingredientes</b></font></td>
        </tr>
        <?php 

        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                $array_ingredientes_ordenados[$contador_ingredientes][0]=$ingredientes["id_alimento"];
                $array_ingredientes_ordenados[$contador_ingredientes][5]=$ingredientes["preparar_separado"];
                $array_ingredientes_ordenados[$contador_ingredientes][6]=urldecode($ingredientes["nombre_alimento"]);
                ?>
                <td bgcolor="#efefef" align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b><input type="button" value="<?=urldecode($ingredientes["nombre_alimento"]);?>" onclick="window.open('intro_ingrediente.php?id_alimento=<?=$ingredientes["id_alimento"]?>&id_plato=<?=$id_plato?>&id_empleado=<?=$id_empleado;?>','intro_ingrediente','toolbar=yes,width=640,height=480,resizable=no');" style="font-size:14px"></b></font></td>
                <?php 
                $contador_ingredientes++;
                }
        ?>
        </tr>
        <tr>
        <?php 
                for($c1=0;$c1<$numero_ingredientes;$c1++) {
                ?>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total plato</b></font></td>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Por niño</b></font></td>
                <?php 
                }
        ?>
        </tr>
        <?php 
        }
        $contador_platos++;
        
$edad_ini_actual=$datos_variantes["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_variantes["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades=" De ".$edad_ini_actual." a ".$edad_fin_actual;

        // $nombre_mostrado=substr($totales_platos[$codigo_plato][1],(strlen($plato_actual)+1),(strlen($totales_platos[$codigo_plato][1])-strlen($plato_actual)-1));
        $nombre_mostrado=$edades;
        ?>
        <tr>
                <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_mostrado;?></b></font></td>
        <?php 
        // Bucle alimentos

        // Muestra ingredientes necesarios
        
        for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                $id_alimento=$array_ingredientes_ordenados[$c_ingredientes][0];
                $nombre_ingrediente=$totales_ingredientes[$codigo_plato][$id_alimento][1];
                $unidad_de_medida=$totales_ingredientes[$codigo_plato][$id_alimento][2];
                $peso_unidad=$totales_ingredientes[$codigo_plato][$id_alimento][3];
                $cantidad_raciones=$totales_ingredientes[$codigo_plato][$id_alimento][0];
                $tam_racion=$totales_ingredientes[$codigo_plato][$id_alimento][4];

                if ($unidad_de_medida==0) {
                                        $coletilla_unidad="gr.";
                                        } else {
                                        $coletilla_unidad="ml.";
                                        }
                if ($peso_unidad>0) {
                        $unidades_prod=$cantidad_raciones/$peso_unidad;
                        if (round($unidades_prod,2)>round($unidades_prod,0)) {
                                $unidades_prod=round($unidades_prod,0)+1;
                                } else {
                                $unidades_prod=round($unidades_prod,0);
                                }
                        $unidades_racion=$tam_racion/$peso_unidad;
                        if (round($unidades_racion,2)>round($unidades_racion,0)) {
                                $unidades_racion=round($unidades_racion,0)+1;
                                } else {
                                $unidades_racion=round($unidades_racion,0);
                                }
                        } else {
                        $unidades_prod=$cantidad_raciones;
                        $unidades_racion=$tam_racion;
                        }
                if ($peso_unidad==1) {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($cantidad_raciones,2,",",".");?> <?=$coletilla_unidad;?></font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($tam_racion,2,",",".");?> <?=$coletilla_unidad;?></font></td>
                <?php 
                } else {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"><a title="<?=number_format($cantidad_raciones,2,",",".");?> <?=$coletilla_unidad;?>" href="#"><?=number_format($unidades_prod,2,",",".");?> Uds.</a></font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"><a title="<?=number_format($tam_racion,2,",",".");?> <?=$coletilla_unidad;?>" href="#"><?=number_format($unidades_racion,2,",",".");?> Uds.</a></font></td>
                <?php 
                }
                $array_ingredientes_ordenados[$c_ingredientes][1]=$array_ingredientes_ordenados[$c_ingredientes][1]+$cantidad_raciones;
                $array_ingredientes_ordenados[$c_ingredientes][2]=$array_ingredientes_ordenados[$c_ingredientes][2]+$unidades_prod;
                $array_ingredientes_ordenados[$c_ingredientes][3]=$peso_unidad;
                $array_ingredientes_ordenados[$c_ingredientes][4]=$coletilla_unidad;
                }
                $total_absoluto=$total_absoluto+$totales_platos[$codigo_plato][0];
                $num_hechas_total=$num_hechas_total+($totales_platos[$codigo_plato][0]/$datos_variantes["raciones"]);
                
                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                $total_absoluto_escuelas[$centros["id_centro"]]=$total_absoluto_escuelas[$centros["id_centro"]]+$totales_platos_escuelas[$codigo_plato][$centros["id_centro"]];
                }
                ?>
        </tr>
        <?php 
                        }
                }
        if ($contador_platos>0) {
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Total todas las edades</b></font></td>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][1],2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        
                <?php 
                } else {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][2],2,",",".");?> Uds.</font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <?php 
                }
                }
                ?>
                </tr>
                <tr>
                <td bgcolor="#ffffff" colspan="<?=1+$numero_ingredientes*2;?>"><font size=1>&nbsp;</font></td>
                </tr>
                <tr>
                <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>">Cantidades consumidas</font></td>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                        $id_alimento=$array_ingredientes_ordenados[$c_ingredientes][0];
                        $nombre_ingrediente=$totales_ingredientes[$codigo_plato][$id_alimento][1];
                        $unidad_de_medida=$totales_ingredientes[$codigo_plato][$id_alimento][2];
                        $peso_unidad=$totales_ingredientes[$codigo_plato][$id_alimento][3];
                        $cantidad_raciones=$totales_ingredientes[$codigo_plato][$id_alimento][0];
                        $tam_racion=$totales_ingredientes[$codigo_plato][$id_alimento][4];
                if ($unidad_de_medida==0) {
                                        $coletilla_unidad="gr.";
                                        } else {
                                        $coletilla_unidad="ml.";
                                        }
                        ?>
                        <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><table width=100% cellpadding=1 cellspacing=0 border=0><?php 
                        
                        // Busca cantidades consumidas en la tabla

                        $busca_consumo=db_query_params('SELECT * FROM menu_productos_consumo_cocina, tarifa WHERE menu_productos_consumo_cocina.id_producto_tarifa=tarifa.ID AND menu_productos_consumo_cocina.id_plato=? AND menu_productos_consumo_cocina.id_alimento=? AND menu_productos_consumo_cocina.dia=?', 'sss', array($id_plato, $id_alimento, (string)(date("Y-m-d"))));

                        if (db_num_rows($busca_consumo)==0) {
                        ?>
                        <tr>
                                <td width=50%><font size=1>&nbsp;</font></td>
                                <td align="right" width=25%><font size=1>0,00 <?=$coletilla_unidad?></font></td>
                        </tr>
                        <?php 
                                }

                        while($datos_consumo=db_fetch_array($busca_consumo)) {
                        ?>
                        <tr>
                        <td width=50%><font size=1><?=$datos_consumo["ARTICULO"]?></font></td>
                        <td align="right" width=25%><font size=1><?=$datos_consumo["cantidad"]?> <?=$coletilla_unidad?></font></td>
                        </tr>
                        <?php 
                        }
                        
                        ?></table></font></td>
                        <?php 
                        }
                ?>
                </tr>
                </table>
                <?php 
                if ($nombre_recipiente_plato!="") {
                ?>
                <br>
                <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
                <tr><td bgcolor="#efefef" align="left" colspan=<?=($contador_ingredientes+1)?>><font size=3 face="<?= $fuente_general; ?>"><b>Instrucciones preparación</b></font></td></tr>
                <tr>
                <td><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <?php 
                $hay_separados=false;
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][5]==0) {
                ?>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b><?=$array_ingredientes_ordenados[$c_ingredientes][6];?></b></font></td>
                <?php 
                } else {
                $hay_separados=true;
                }
                        }
                ?>
                </tr>
                <?php 
                $numero_completas=round($num_hechas_total,0);
                if ($numero_completas>$num_hechas_total) {
                        $numero_completas=$numero_completas-1;
                        }
                $numero_incompletas=$num_hechas_total-$numero_completas;
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$numero_completas?> <?=$nombre_recipiente_plato?> completas cada una con los siguientes ingredientes: </b></font></td>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][5]==0) {
                if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total);
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($ingredientes_por_olla,2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>

                        <?php 
                        } else {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total);
                        $cantidad_raciones=$ingredientes_por_olla;
                        $peso_unidad=$array_ingredientes_ordenados[$c_ingredientes][3];
                        if ($peso_unidad>0) {
                                $unidades_prod=$cantidad_raciones/$peso_unidad;
                                if (round($unidades_prod,2)>round($unidades_prod,0)) {
                                        $unidades_prod=round($unidades_prod,0)+1;
                                        } else {
                                        $unidades_prod=round($unidades_prod,0);
                                        }
                                $unidades_racion=$tam_racion/$peso_unidad;
                                if (round($unidades_racion,2)>round($unidades_racion,0)) {
                                        $unidades_racion=round($unidades_racion,0)+1;
                                        } else {
                                        $unidades_racion=round($unidades_racion,0);
                                        }
                                } else {
                                $unidades_prod=$cantidad_raciones;
                                $unidades_racion=$tam_racion;
                                }
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($unidades_prod,2,",",".");?> Uds.</font></td>
                        <?php 
                        }
                        }
                }
                ?>
                </tr>
                <?php 
                if ($numero_incompletas>0) {
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>1 <?=$nombre_recipiente_plato?> con los siguientes ingredientes: </b></font></td>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][5]==0) {
                if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total)*$numero_incompletas;
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($ingredientes_por_olla,2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>
                        <?php 
                        } else {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total)*$numero_incompletas;
                        $cantidad_raciones=$ingredientes_por_olla;
                        $peso_unidad=$array_ingredientes_ordenados[$c_ingredientes][3];
                        if ($peso_unidad>0) {
                                $unidades_prod=$cantidad_raciones/$peso_unidad;
                                if (round($unidades_prod,2)>round($unidades_prod,0)) {
                                        $unidades_prod=round($unidades_prod,0)+1;
                                        } else {
                                        $unidades_prod=round($unidades_prod,0);
                                        }
                                $unidades_racion=$tam_racion/$peso_unidad;
                                if (round($unidades_racion,2)>round($unidades_racion,0)) {
                                        $unidades_racion=round($unidades_racion,0)+1;
                                        } else {
                                        $unidades_racion=round($unidades_racion,0);
                                        }
                                } else {
                                $unidades_prod=$cantidad_raciones;
                                $unidades_racion=$tam_racion;
                                }
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($unidades_prod,2,",",".");?> Uds.</font></td>
                        <?php 
                        }
                        }
                }
                ?>
                </tr>
                <?php 
                }
                ?>
                </table>
                <br>
                <?php 
                if ($hay_separados==true) {
                ?>
                <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
                <tr><td bgcolor="#efefef" align="left" colspan=2><font size=3 face="<?= $fuente_general; ?>"><b>Preparar separado</b></font></td></tr>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                        if ($array_ingredientes_ordenados[$c_ingredientes][5]==1) {
                        ?>
                        <tr>
                                <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$array_ingredientes_ordenados[$c_ingredientes][6];?></b></font></td>
                        <?php 
                        if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                                        ?>
                                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][1],2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>
                                        <?php 
                                        } else {
                                        ?>
                                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][2],2,",",".");?> Uds.</font></td>
                                        <?php 
                                        }
                                ?>
                                </tr>
                                <?php 
                                }
                        }
                        ?>
                        </table>
                        <?php 
                        }
                }
                ?>
        <br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
        <tr><td bgcolor="#efefef" align="left" colspan="<?=$numero_de_centros+3;?>"><font size=3 face="<?= $fuente_general; ?>"><b>Nº de raciones servidas</b></font></td></tr>
        <tr><td><font size=1>&nbsp;</font></td>
        <?php 
        // Instrucciones de preparacion
        if (isset($total_absoluto_escuelas)) {
                unset($total_absoluto_escuelas);
                }
        $total_absoluto_escuelas=array();
        $total_absoluto=0;

                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                ?>
                <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$centros["nombre_centro"];?></b></font></td>
                <?php 
                }
                ?>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Instrucciones preparación de raciones</b></font></td>
        </tr>
        <?php 
        // Busca variantes del plato
        $busca_variantes=db_query_params('SELECT * FROM menu_platos WHERE nombre_plato=? ORDER BY edad_ini_plato DESC', 's', array((string)($datos_plato["nombre_plato"]))) or die(db_error());

        while($datos_variantes=db_fetch_array($busca_variantes)) {

        $codigo_plato=$datos_variantes["id_plato"];

        if (intval($totales_platos[$codigo_plato][0])>0) {

        $contador_platos++;

$edad_ini_actual=$datos_variantes["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_variantes["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades=" De ".$edad_ini_actual." a ".$edad_fin_actual;

        // $nombre_mostrado=substr($totales_platos[$codigo_plato][1],(strlen($plato_actual)+1),(strlen($totales_platos[$codigo_plato][1])-strlen($plato_actual)-1));
        $nombre_mostrado=$edades;

        ?>
        <tr>
                <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_mostrado;?></b></font></td>
                <?php 
                $total_absoluto=$total_absoluto+$totales_platos[$codigo_plato][0];

                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                $total_absoluto_escuelas[$centros["id_centro"]]=$total_absoluto_escuelas[$centros["id_centro"]]+$totales_platos_escuelas[$codigo_plato][$centros["id_centro"]];
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($totales_platos_escuelas[$codigo_plato][$centros["id_centro"]]);?></font></td>
                <?php 
                }
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=intval($totales_platos[$codigo_plato][0]);?></font></td>
                <td bgcolor="#ffffff" align="left"><font size=1 face="<?= $fuente_general; ?>"> <?=urldecode($datos_variantes["instrucciones_servir"]);?></font></td>
        </tr>
                <?php 
                }
        }
        if ($contador_platos>0) {
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Total todas las edades</b></font></td>
                <?php 
                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_absoluto_escuelas[$centros["id_centro"]]);?></font></td>
                <?php 
                }
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_absoluto);?></font></td>
                <td bgcolor="#ffffff" align="left"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                </tr>
                </table><br>
                </div>
                <?php 
                }
                }
        }

} else {
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en el comedor.<br><br><br></td>
  </tr>
</table>
<?php 

}
?>
<br>
<input type="button" value="Salir" onclick="window.location.href='ver_platos_hoy_provisional.php?id_empleado=<?=$id_empleado;?>'" style="font-size:18px; width: 200; height: 64">
        </div>
    </td>
  </tr>
</table>
</div>
</body>
</html>

