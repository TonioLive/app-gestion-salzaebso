<?php 
require("../../configuracion/config.php");
// require("../../configuracion/autentifica_padre.php");

$dia=$_GET["dia"];
$a=$_GET["a"];

$entrada_ma=false;

$tipos_notas_inci=array();

$busca_notas_inci=db_query("SELECT * FROM agenda_alumnos_tipos_notas");
while($notas_inci=db_fetch_array($busca_notas_inci)) {
        $tipos_notas_inci[$notas_inci["id_tipo"]]=$notas_inci["nombre_tipo"];
        }

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

$cantidades_comida=array("Todo","Mitad","Poco","Nada");
$resultados_comida=array("Bien","Regular","Mal");

if (strlen($dia)==0) {
        // $dia=time();
        $dia=strtotime("2011-06-30");
        }

// Comprueba si el alumno es hijo de su padre

$busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario' AND padre_de=$a");
// if (mysql_num_rows($busca_usuario)==0) {
if (0!=0) {
        echo("No tiene permiso para consultar esta información.");
        die();
        } else {
        $datos_hijo=db_fetch_array($busca_usuario);
        // Busca información del niño

        // Inscripcion actual del niño
        $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=$a AND alumnos.fecha_matricula<='".date("Y-m-d",$dia)."' AND alumnos.fecha_baja>='".date("Y-m-d",$dia)."';") or die(db_error()."- No se encuentran los datos del alumno.");

        if (db_num_rows($busca_datos_alumno)!=0) {
                $datos_alumno=db_fetch_array($busca_datos_alumno);

                $id_alumno=$datos_alumno["id_alumno"];
                $id_aula=$datos_alumno["aula"];
                $id_curso=$datos_alumno["id_curso"];
                $id_centro=$datos_alumno["id_centro"];

                $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas,centros WHERE centro_aulas.id_centro=centros.id_centro AND id_aula=?', 's', array($id_aula));

                if (db_num_rows($consulta_datos_aula)!=0)
                        {
                        $datos_aula=db_fetch_array($consulta_datos_aula);

                        // Datos Fijos
                        $aula_nombre=$datos_aula["nombre"];
                        $centro_nombre=$datos_aula["nombre_centro"];

                        $hora_entrada_centro_man=$datos_aula["hora_entrada1"];
                        $hora_salida_centro_man=$datos_aula["hora_salida1"];
                        $hora_entrada_centro_tar=$datos_aula["hora_entrada2"];
                        $hora_salida_centro_tar=$datos_aula["hora_salida2"];
                        $division_horas=($datos_aula["division_horas"]/60);
                        $periodo_gracia=($datos_aula["periodo_gracia"]*60);

                        }

                } else {
                echo("El alumno no está matriculado hoy");
                die();
                }

        }

$fecha_time_stamp=$dia;

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";


$curso=$_GET["curso"];

define('FPDF_FONTPATH','font/');
require('html2fpdf.php');

$pdf=new HTML2FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);
// Encabezado

ob_start();
?>
        <table width=580 border=0 cellpadding=0 cellspacing=0 align="center">
        <tr><td><img width=580 src="encabezado_agenda.jpg" border=0></tr></td>
        <tr><td><table width=580 border=2 bordercolor="#cc424f">
        <tr bgcolor="#ffffff" valign="top">
                <td width="100%" valign="top" align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Día <?=date("d/m/Y",$dia);?><br><br></b></font>
                <br>
                <tr bgcolor="#FFFFFF">
                        <td align="left"><font face="<?= $fuente_general; ?>" size="2">
                <?php 
                // Asistencia

        // Consulta servicios asistencia durante el mes seleccionado
        $consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$a AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY turno;");

        $tiene_man=false;

        while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
                $horas_extra_hoy=0;
                if ($datos_asistencia["turno"]==0) {
                        $nombre_turno="Mañana";
                        $hora_entrada_centro=strtotime($hora_entrada_centro_man);
                        $hora_salida_centro=strtotime($hora_salida_centro_man);
                        } else {
                        $nombre_turno="Tarde";
                        $hora_entrada_centro=strtotime($hora_entrada_centro_tar);
                        $hora_salida_centro=strtotime($hora_salida_centro_tar);
                        }
                $diferencia_entrada=intval($hora_entrada_centro)-intval(strtotime($datos_asistencia["hora_entrada"]));

                if ($diferencia_entrada>$periodo_gracia) {
                        $diferencia_entrada=round($diferencia_entrada/36)/100;
                        $diferencia_entrada=round((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_entrada=0;
                        }

                $diferencia_salida=intval(strtotime($datos_asistencia["hora_salida"]))-intval($hora_salida_centro);

                if ($diferencia_salida>$periodo_gracia) {
                        $diferencia_salida=round($diferencia_salida/36)/100;
                        $diferencia_salida=round((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_salida=0;
                        }

                // Si se queda a comer no se cobran horas
                $coletila="";
                $hora_entrada_comedor="-";
                $hora_salida_comedor="-";
                        if ($datos_asistencia["turno"]==0) {
                                if ($datos_asistencia["hora_entrada_c"]!="") {
                                        $coletila="<font color=red> * Comedor</font>";
                                        $diferencia_salida=$diferencia_salida-$horas_comida;
                                        if ($diferencia_salida<0) {
                                                $diferencia_salida=0;
                                                }
                                        $hora_entrada_comedor=date("H:i:s",strtotime($datos_asistencia["hora_entrada_c"]));
                                        if ($datos_asistencia["hora_salida_c"]!="") {
                                                $hora_salida_comedor=date("H:i:s",strtotime($datos_asistencia["hora_salida_c"]));
                                                } else {
                                                $hora_salida_comedor="-";
                                                }
                                        }
                                } else {
                                // Comprueba si ha entrado a comer en otro turno
                                $busca_otro_turno=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND fecha=? AND turno=0;', 'sss', array($datos_asistencia["id_alumno"], $datos_asistencia["id_servicio"], (string)(date("Y-m-d", strtotime($datos_asistencia["fecha"])))));

                                if (db_num_rows($busca_otro_turno)!=0) {
                                        $datos_otro_turno=db_fetch_array($busca_otro_turno);
                                        if ($datos_otro_turno["hora_entrada_c"]!="") {
                                                // Si el niño ha venido a comer y ha entrado por la mañana, no le cobro horas extra
                                                $diferencia_entrada=0;
                                                /*
                                                $diferencia_entrada=$diferencia_entrada-$horas_comida;
                                                if ($diferencia_entrada<0) {
                                                        $diferencia_entrada=0;
                                                        }
                                                */
                                                $hora_entrada_comedor=date("H:i:s",strtotime($datos_otro_turno["hora_entrada_c"]));
                                                if ($datos_otro_turno["hora_salida_c"]!="") {
                                                        $hora_salida_comedor=date("H:i:s",strtotime($datos_otro_turno["hora_salida_c"]));
                                                        } else {
                                                        $hora_salida_comedor="-";
                                                        }
                                                }
                                        }
                                }

                $horas_extra_hoy=($diferencia_entrada+$diferencia_salida);

                // Convierte a hh:mm
                $horas_extra_mostrar_hoy=$horas_extra_hoy;

                $horas_extra3=round($horas_extra_mostrar_hoy,0);
                if ($horas_extra3>$horas_extra_mostrar_hoy) {
                        $horas_extra3=$horas_extra3-1;
                                }
                $minutos_extra3="00".(($horas_extra_mostrar_hoy-$horas_extra3)*60);
                $minutos_extra3=substr($minutos_extra3,strlen($minutos_extra3)-2,2);
                $horas_extra3="00".$horas_extra3;
                $horas_extra3=substr($horas_extra3,strlen($horas_extra3)-2,2);
                $horas_extra_mostrar_hoy=$horas_extra3.":".$minutos_extra3;

                $total_horas_extra=0;
                $total_horas_extra_fact=0;

                $total_horas_extra=$total_horas_extra+$horas_extra_hoy;
                if ($horas_extra_hoy_fact>0) {
                        $total_horas_extra_fact=$total_horas_extra_fact+$horas_extra_hoy_fact;
                        }

                        if ($datos_asistencia["hora_entrada"]!="") {
                        $mostrar_entrada=true;
                        if ($datos_asistencia["turno"]==0) {
                               $entrada_ma=true;
                               }
                        if (($datos_asistencia["turno"]==1) and ($entrada_ma==true)) {
                                $mostrar_entrada=false;
                                }
                        if ($mostrar_entrada==true) {
                                $inserta_dato=db_query_params('INSERT INTO agenda_temp (id_alumno,fecha,hora,texto) VALUES (?,?,?,\'Llegada al centro\');', 'sss', array((string)($a), (string)(date("Y-m-d", $dia)), (string)(date("H:i:s", strtotime($datos_asistencia["hora_entrada"])))));
                                }

                        }

                        if ($datos_asistencia["hora_entrada_c"]!="") {
                        $inserta_dato=db_query_params('INSERT INTO agenda_temp (id_alumno,fecha,hora,texto) VALUES (?,?,?,\'Entrada al comedor\');', 'sss', array((string)($a), (string)(date("Y-m-d", $dia)), (string)($hora_entrada_comedor)));
                        }

                        if ($datos_asistencia["hora_salida_c"]!="") {

                        // Busca comidas y resultado
                        $descripcion_comidas="";
                        // Busca registro de comida
                        $busca_registro_comida=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id_alumno=? AND dia=?', 'ss', array($id_alumno, (string)(date("Y-m-d", $dia)))) or die(db_error());

                        if (db_num_rows($busca_registro_comida)!=0) {

                        $datos_registro_comida=db_fetch_array($busca_registro_comida);

                        $id_registro_comida=$datos_registro_comida["id"];

                        $id_menu=$datos_asistencia["menu_seleccionado_hoy"];

                        $descripcion_comidas.="<blockquote><font size=2><b>ALMUERZO:</b> ".$cantidades_comida[$datos_registro_comida["cant_almuerzo"]].", ".$resultados_comida[$datos_registro_comida["almuerzo"]]."</font><br>";

                        // Busca menu para hoy

$busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
if (db_num_rows($busca_platos_dia)!=0) {

                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_platos_dia[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {

                                                $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

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
        $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>COMIDA</b></font><br>";
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida1][$id_alumno]==1) or ($alimentos_ya_marcados[$comida1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_comida1"]].", ".$resultados_comida[$datos_registro_comida["comida1"]]."</font><br>";
                }

        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida2][$id_alumno]==1) or ($alimentos_ya_marcados[$comida2][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_comida2"]].", ".$resultados_comida[$datos_registro_comida["comida2"]]."</font><br>";
                }

        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida3][$id_alumno]==1) or ($alimentos_ya_marcados[$comida3][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_comida3"]].", ".$resultados_comida[$datos_registro_comida["comida3"]]."</font><br>";
                }

        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida4][$id_alumno]==1) or ($alimentos_ya_marcados[$comida4][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_comida4"]].", ".$resultados_comida[$datos_registro_comida["comida4"]]."</font><br>";
                }

        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida5][$id_alumno]==1) or ($alimentos_ya_marcados[$comida5][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_comida5"]].", ".$resultados_comida[$datos_registro_comida["comida5"]]."</font><br>";
                }

        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$postre1][$id_alumno]==1) or ($alimentos_ya_marcados[$postre1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_postre1"]].", ".$resultados_comida[$datos_registro_comida["postre1"]]."</font><br>";
                }
        $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>MERIENDA</b></font><br>";
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$merienda1][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_merienda1"]].", ".$resultados_comida[$datos_registro_comida["merienda1"]]."</font><br>";
                }

        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$merienda2][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda2][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<font size=2 color='$color_fuente_linea'><b>".urldecode($datos_comida["nombre_plato"]);
                $descripcion_comidas.=":</b> ".$cantidades_comida[$datos_registro_comida["cant_merienda2"]].", ".$resultados_comida[$datos_registro_comida["merienda2"]]."</font><br>";
                }

                $texto_observaciones_linea=$datos_registro_comida["observaciones_comida"];
                if (strlen($texto_observaciones_linea)!=0) {
                        $descripcion_comidas.="<br>".$texto_observaciones_linea."<br>";
                        }

                }
                $descripcion_comidas.="</blockquote>";
            }

            $inserta_dato=db_query_params('INSERT INTO agenda_temp (id_alumno,fecha,hora,texto) VALUES (?,?,?,?);', 'ssss', array((string)($a), (string)(date("Y-m-d", $dia)), (string)($hora_salida_comedor), 'Salida comedor' . (string)($descripcion_comidas))) or die(db_error()."<br>INSERT INTO agenda_temp (id_alumno,fecha,hora,texto) VALUES ('$a','".date("Y-m-d",$dia)."','".$hora_salida_comedor."','Salida comedor".$descripcion_comidas."');");

                        }
                        // Fin comidas

                        if ($datos_asistencia["hora_salida"]!="") {
                        $inserta_dato=db_query_params('INSERT INTO agenda_temp (id_alumno,fecha,hora,texto) VALUES (?,?,?,\'Salida del centro\');', 'sss', array((string)($a), (string)(date("Y-m-d", $dia)), (string)(date("H:i:s", strtotime($datos_asistencia["hora_salida"])))));
                        }
                }


                // Alimentación

                // Seguimiento
                $tipo_incidencias=1;
                $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));

                while($incidencias=db_fetch_array($busca_incidencias)) {
                        $inserta_dato=db_query_params('INSERT INTO agenda_temp (id_alumno,fecha,hora,texto) VALUES (?,?,?,?);', 'ssss', array((string)($a), (string)(date("Y-m-d", $dia)), (string)(date("H:i", strtotime($incidencias["hora"]))), (string)($tipos_notas_inci[$incidencias["tipo_nota"]]) . ': ' . (string)($incidencias["nota"])));
                        }

                // Busca eventos
                $busca_eventos=db_query_params('SELECT * FROM agenda_temp WHERE id_alumno=? AND fecha=? ORDER BY hora', 'ss', array((string)($a), (string)(date("Y-m-d", $dia))));
                while($listado_eventos=db_fetch_array($busca_eventos)){
                        ?>
                        <font color="#cc424f"><b><?=date("H:i:s",strtotime($listado_eventos["hora"]));?>.</b></font> <?=$listado_eventos["texto"]?><br>
                        <?php 
                        }

                // Borra Temp

                db_query_params('DELETE FROM agenda_temp WHERE id_alumno=? AND fecha=?', 'ss', array((string)($a), (string)(date("Y-m-d", $dia))));

                ?>
                <br>
                <br>
                        </td>
                </tr>
                </table>
        </td></tr>
        </table>
        </td></tr>
        <tr><td><img width=580 src="pie_pagina_info.jpg" border=0></td></tr>
        </table>
<?php 
$html=ob_get_contents();
ob_end_clean();

$pdf->WriteHTML($html);
$pdf->Output();

?>
