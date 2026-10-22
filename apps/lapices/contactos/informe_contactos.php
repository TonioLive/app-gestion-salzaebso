<?php  require("../configuracion/parte_superior_privado_imprimir.php");


$id_curso_informe=$_GET["curso"];

        if (strlen($id_curso_informe)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $fecha_seleccionada=strtotime($datos_curso_actual["fecha_inicio"]);

                $nombre_curso=$datos_curso_actual["nombre"];
                } else {
                $id_curso_actual=0;
                }
        } else {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_informe");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);

                $fecha_seleccionada=strtotime($datos_curso_actual["fecha_inicio"]);

                $nombre_curso=$datos_curso_actual["nombre"];

                }
        }

// $fecha_seleccionada=$_GET["fecha_sel"];

if (strlen($fecha_seleccionada)==0) {
        $fecha_seleccionada=time();
        }
        
$anio_seleccionado=date("Y",$fecha_seleccionada);

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

?>
<SCRIPT LANGUAGE=javascript>
<!--

//-->
</SCRIPT>
<div align="center"><center>
<input type="button" onclick="window.location.href='listado_contactos.php'" value="Salir" style="font-size: 10px">
<br><br>
<?php 

$busca_cotactos=db_query("SELECT * FROM contactos WHERE id_curso=$id_curso_informe ORDER BY id_centro DESC,id_curso,proceso_decision, fecha_contacto, Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_cotactos)!=0) {
        $totales_por_semana=array();
        $proceso_actual="";
        $curso_actual="";
        $centro_actual_contacto="";

        $totales_tipos_meses_desglosado=array();
        $totales_tipos_centros_meses_desglosado=array();
        
        $totales_tipos=array();
        $totales_tipos_centros=array();
        $totales_centros=array();
        $totales_centros_meses=array();
        $totales_procesos=array();
        $totales_meses=array();
        $total_absoluto=0;
        $total_existos=0;
        $tiempo_total=0;
        $exitos_por_escuela=array();
        $tiempo_total_escuela=array();
		
		$totales_c_meses=array();
		$totales_c_meses_centros=array();
        
        $anio_inicio=3000;
        $anio_fin=0;
        
        while($listado_alumnos=db_fetch_array($busca_cotactos)){
        
        if ($centro_actual_contacto!=$listado_alumnos["id_centro"]) {
        $centro_actual_contacto=$listado_alumnos["id_centro"];
        if ($centro_actual_contacto==0) {
                $nombre_centro_actual="Sin escuela seleccionada";
                } else {
                // Busca datos del centro
                $busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alumnos["id_centro"]);
                if (db_num_rows($busca_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_centro);
                        $nombre_centro_actual=$datos_centro["nombre_centro"];
                        }
                }
        }
        
        $proceso=$listado_alumnos["proceso_decision"];
        if ($proceso!=$proceso_actual) {
        $proceso_actual=$proceso;
        switch($proceso_actual)
                {
                case "b1":
                        $id_proceso_actual=1;
                break;
                case "b2":
                        $id_proceso_actual=2;
                break;
                case "b3":
                        $id_proceso_actual=3;
                break;
                case "c":
                        $id_proceso_actual=4;
                break;
                }
        }
        
        $mes_seleccionado=date("n",strtotime($listado_alumnos["fecha_contacto"]));
        $anio=date("Y",strtotime($listado_alumnos["fecha_contacto"]));
        
        if ($anio<$anio_inicio) {
                $anio_inicio=$anio;
                }
        if ($anio>$anio_fin) {
                $anio_fin=$anio;
                }

        $tipo_proceso_actual=$listado_alumnos["como_conocio"];
        
        // Cuenta
        $totales_centros[$centro_actual_contacto][0]++;
        
        $totales_tipos[$tipo_proceso_actual]++;
        $totales_tipos_centros[$centro_actual_contacto][$tipo_proceso_actual]++;

        $totales_tipos_meses_desglosado[$mes_seleccionado][$tipo_proceso_actual]++;
        $totales_tipos_centros_meses_desglosado[$centro_actual_contacto][$mes_seleccionado][$tipo_proceso_actual]++;
        
		if (($listado_alumnos["fecha_c"]!='0000-00-00') and ($listado_alumnos["fecha_c"]!='')) {

			$totales_c_meses[date("n",strtotime($listado_alumnos["fecha_c"]))]++;
			$totales_c_meses_centros[$centro_actual_contacto][date("n",strtotime($listado_alumnos["fecha_c"]))]++;
			
		}
		
		
        $totales_centros[$centro_actual_contacto][$id_proceso_actual]++;
        $totales_centros_meses[$centro_actual_contacto][$mes_seleccionado]++;
        $totales_centros_meses_desglosado[$centro_actual_contacto][$mes_seleccionado][$anio]++;
        
        $totales_procesos[$id_proceso_actual]++;
        $totales_meses_desglosado[$mes_seleccionado][$anio]++;
        $totales_meses[$mes_seleccionado]++;
        $total_absoluto++;

        if ((strlen($listado_alumnos["fecha_c"])!=0) and ($listado_alumnos["fecha_c"]!='0000-00-00')) {
                $tiempo_respuesta=strtotime($listado_alumnos["fecha_c"])-strtotime($listado_alumnos["fecha_contacto"]);
                if ($tiempo_respuesta>0) {
                        $tiempo_total=$tiempo_total+$tiempo_respuesta;
                        $tiempo_total_escuela[$centro_actual_contacto]=$tiempo_total_escuela[$centro_actual_contacto]+$tiempo_respuesta;
                        }
                $total_existos++;
                $exitos_por_escuela[$centro_actual_contacto]++;
                }
        
        }
        
$altura_grafico=300;
$unidades_grafico=round(($total_absoluto/300),4);
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=13><small><b>Informe de contactos realizados para el curso <?=$nombre_curso?> en todas las escuelas</b></small></td>
  </tr>
  <tr>
    <td align="left" colspan=13><small>
    <b>Total contactos:</b> <?=$total_absoluto?><br>
    <b>Total inscripciones:</b> <?=$total_existos?> (con fecha confirmada)<br><?php 
    if ($total_absoluto>0) {
    ?>
    <b>Porcentaje de exito:</b> <?=number_format((100*($total_existos/$total_absoluto)), 2, ',', '.');?> %<br>
    <?php 
    }
    if ($total_existos>0) {
    ?>
    <b>Tiempo medio de respuesta:</b> <?=number_format((($tiempo_total/$total_existos)/86400), 2, ',', '.');?> días. (Para los contactos finalmente inscritos)<br>
    <?php 
    }
    ?>
    <b>Total inscripciones:</b> <?=$totales_procesos[4]?> (incluyendo sin fecha confirmada)<br>
    <?php 
    if ($total_absoluto>0) {
    ?>
    <b>Porcentaje de éxito:</b> <?=number_format((100*($totales_procesos[4]/$total_absoluto)), 2, ',', '.');?> %<br>
    <?php 
    }
    ?>
    <b>Totales por estado</b><br>
        b1: <?=$totales_procesos[1]?><br>
        b2: <?=$totales_procesos[2]?><br>
        b3: <?=$totales_procesos[3]?><br>
        &nbsp;&nbsp;c: <?=$totales_procesos[4]?>
    </small></td>
  </tr>
<tr>
    <td><img src="/imagen/nada.gif" width=1 height=<?=$altura_grafico?>></td>
<?php 
for ($c_mes=1;$c_mes<=12;$c_mes++) {

$mensaje_desglose="";

for ($anios=$anio_inicio;$anios<=$anio_fin;$anios++) {
        $mensaje_desglose=$mensaje_desglose."<a href='detalles_mes.php?mes=".$c_mes."&anio=".$anios."'>".$anios.": ".intval($totales_meses_desglosado[$c_mes][$anios])."</a><br>";
        }
$mensaje_desglose=$mensaje_desglose."<br>";
$busca_tipos_contactos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo");
while($tipos_contactos=db_fetch_array($busca_tipos_contactos)){
        if (intval($totales_tipos_meses_desglosado[$c_mes][$tipos_contactos["id_tipo"]])!=0) {
                $mensaje_desglose=$mensaje_desglose."<a title='".$tipos_contactos["id_tipo"].": ".$tipos_contactos["nombre_tipo"]."'>".$tipos_contactos["id_tipo"].": ".intval($totales_tipos_meses_desglosado[$c_mes][$tipos_contactos["id_tipo"]])."</a><br>";
                }
        }
if ($mensaje_desglose!="") {
        $mensaje_desglose=substr($mensaje_desglose,0,strlen($mensaje_desglose)-4);
        }

?>
<td align="center" valign="bottom"><a href='detalles_mes.php?mes=<?=$c_mes;?>&anio=<?=$anio_seleccionado;?>'><font size=2><b><?=intval($totales_meses[$c_mes]);?></b></a><br>C: <?=$totales_c_meses[$c_mes]?><br><small><?=$mensaje_desglose?></small><br><img src="/imagen/dias/M.gif" height="<?=round($totales_meses[$c_mes]/$unidades_grafico,0)?>" width="28" border=0></font></td>
<?php 
}
?>
</tr>
<tr>
<td></td>
<?php 
for ($c_mes=1;$c_mes<=12;$c_mes++) {
?>
<td align="center"><font size=1><b><?=$nombre_mes[$c_mes]?></b></font></td>
<?php 
}
?>
</tr>
</table>
<?php 
$busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
$total_columnas=db_num_rows($busca_tipos);
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
<tr>
        <td align="center" colspan=<?=$total_columnas+1;?>><font size=2><b>¿Cómo nos conoció?</b></font></td>
</tr>
<tr>
    <td><img src="/imagen/nada.gif" width=1 height=<?=$altura_grafico?>></td>
<?php 
while($datos_tipos=db_fetch_array($busca_tipos)) {
?>
<td align="center" valign="bottom" width="<?=round(((750-1)/$total_columnas),0);?>"><font size=2><b><?=intval($totales_tipos[$datos_tipos["id_tipo"]]);?></b><br><img src="/imagen/dias/M.gif" height="<?=round($totales_tipos[$datos_tipos["id_tipo"]]/$unidades_grafico,0)?>" width="28" border=0></font></a></td>
<?php 
}
?>
</tr>
<tr>
<td></td>
<?php 
$busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
while($datos_tipos=db_fetch_array($busca_tipos)) {
?>
<td align="center"><font size=1><b><?=$datos_tipos["id_tipo"]?>: <?=$datos_tipos["nombre_tipo"]?></b></font></td>
<?php 
}
?>
</tr>
</table><br>
<?php 

// Por escuelas
$centro_actual_contacto=0;
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=13><small><b>Informe de contactos realizados para el curso <?=$nombre_curso?> sin escuela indicada</b></small></td>
  </tr>
  <tr>
    <td align="left" colspan=13><small>
    <b>Total contactos:</b> <?=$totales_centros[$centro_actual_contacto][0]?><br>
    <b>Total inscripciones:</b> <?=$exitos_por_escuela[$centro_actual_contacto]?> (con fecha confirmada)<br><?php 
    if ($totales_centros[$centro_actual_contacto][0]>0) {
    ?>
    <b>Porcentaje de exito:</b> <?=number_format((100*($exitos_por_escuela[$centro_actual_contacto]/$totales_centros[$centro_actual_contacto][0])), 2, ',', '.');?> %<br>
    <?php 
    }
    if ($exitos_por_escuela[$centro_actual_contacto]>0) {
    ?>
    <b>Tiempo medio de respuesta:</b> <?=number_format((($tiempo_total_escuela[$centro_actual_contacto]/$exitos_por_escuela[$centro_actual_contacto])/86400), 2, ',', '.');?> días. (Para los contactos finalmente inscritos)<br>
    <?php 
    }
    ?>
    <b>Total inscripciones:</b> <?=$totales_centros[$centro_actual_contacto][4]?> (incluyendo sin fecha confirmada)<br>
    <?php 
    if ($totales_centros[$centro_actual_contacto][0]>0) {
    ?>
    <b>Porcentaje de éxito:</b> <?=number_format((100*($totales_centros[$centro_actual_contacto][4]/$totales_centros[$centro_actual_contacto][0])), 2, ',', '.');?> %<br>
    <?php 
    }
    ?>
    <b>Totales por estado</b><br>
        b1: <?=$totales_centros[$centro_actual_contacto][1]?><br>
        b2: <?=$totales_centros[$centro_actual_contacto][2]?><br>
        b3: <?=$totales_centros[$centro_actual_contacto][3]?><br>
        &nbsp;&nbsp;c: <?=$totales_centros[$centro_actual_contacto][4]?>
    </small></td>
  </tr>
<tr>
    <td><img src="/imagen/nada.gif" width=1 height=<?=$altura_grafico?>></td>
<?php 
for ($c_mes=1;$c_mes<=12;$c_mes++) {

$mensaje_desglose="";


for ($anios=$anio_inicio;$anios<=$anio_fin;$anios++) {
        $mensaje_desglose=$mensaje_desglose."<a href='detalles_mes.php?mes=".$c_mes."&anio=".$anios."'>".$anios.": ".intval($totales_centros_meses_desglosado[$centro_actual_contacto][$c_mes][$anios])."</a><br>";
        }
$mensaje_desglose=$mensaje_desglose."<br>";
$busca_tipos_contactos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo");
while($tipos_cofntactos=db_fetch_array($busca_tipos_contactos)){
        if (intval($totales_tipos_centros_meses_desglosado[$centro_actual_contacto][$c_mes][$tipos_contactos["id_tipo"]])!=0) {
                $mensaje_desglose=$mensaje_desglose."<a title='".$tipos_contactos["id_tipo"].": ".$tipos_contactos["nombre_tipo"]."'>".$tipos_contactos["id_tipo"].": ".intval($totales_tipos_centros_meses_desglosado[$centro_actual_contacto][$c_mes][$tipos_contactos["id_tipo"]])."</a><br>";
                }
        }
if ($mensaje_desglose!="") {
        $mensaje_desglose=substr($mensaje_desglose,0,strlen($mensaje_desglose)-4);
        }

?>
<td align="center" valign="bottom"><a href='detalles_mes.php?id_escuela=-1&mes=<?=$c_mes;?>&anio=<?=$anio_seleccionado;?>'><font size=2><b><?=intval($totales_centros_meses[$centro_actual_contacto][$c_mes]);?></b></a><br>C: <?=$totales_c_meses_centros[$centro_actual_contacto][$c_mes]?><br><small><?=$mensaje_desglose?></small><br><img src="/imagen/dias/M.gif" height="<?=round($totales_centros_meses[$centro_actual_contacto][$c_mes]/$unidades_grafico,0)?>" width="28" border=0></font></td>
<?php 
}
?>
</tr>
<tr>
<td></td>
<?php 
for ($c_mes=1;$c_mes<=12;$c_mes++) {
?>
<td align="center"><font size=1><b><?=$nombre_mes[$c_mes]?></b></font></td>
<?php 
}
?>
</tr>
</table>
<?php 
$busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
$total_columnas=db_num_rows($busca_tipos);
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
<tr>
        <td align="center" colspan=<?=$total_columnas+1;?>><font size=2><b>¿Cómo nos conoció?</b></font></td>
</tr>
<tr>
    <td><img src="/imagen/nada.gif" width=1 height=<?=$altura_grafico?>></td>
<?php 
while($datos_tipos=db_fetch_array($busca_tipos)) {
?>
<td align="center" valign="bottom" width="<?=round(((750-1)/$total_columnas),0);?>"><font size=2><b><?=intval($totales_tipos_centros[$centro_actual_contacto][$datos_tipos["id_tipo"]]);?></b><br><img src="/imagen/dias/M.gif" height="<?=round($totales_tipos_centros[$centro_actual_contacto][$datos_tipos["id_tipo"]]/$unidades_grafico,0)?>" width="28" border=0></font></a></td>
<?php 
}
?>
</tr>
<tr>
<td></td>
<?php 
$busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
while($datos_tipos=db_fetch_array($busca_tipos)) {
?>
<td align="center"><font size=1><b><?=$datos_tipos["id_tipo"]?>: <?=$datos_tipos["nombre_tipo"]?></b></font></td>
<?php 
}
?>
</tr>
</table><br>
<?php 
if ($nivel_acceso==4) {
$consulta_centros="SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro";
} else {
$consulta_centros="SELECT * FROM centros WHERE id_centro=$centro_actual ORDER BY nombre_centro";
}

$busca_escuelas=db_query($consulta_centros);

while($lista_escuelas=db_fetch_array($busca_escuelas)) {
$centro_actual_contacto=$lista_escuelas["id_centro"];
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=13><small><b>Informe de contactos realizados para el curso <?=$nombre_curso?> en al escuela <?=$lista_escuelas["nombre_centro"];?></b></small></td>
  </tr>
  <tr>
    <td align="left" colspan=13><small>
    <b>Total contactos:</b> <?=$totales_centros[$centro_actual_contacto][0]?><br>
    <b>Total inscripciones:</b> <?=$exitos_por_escuela[$centro_actual_contacto]?> (con fecha confirmada)<br><?php 
    if ($totales_centros[$centro_actual_contacto][0]>0) {
    ?>
    <b>Porcentaje de exito:</b> <?=number_format((100*($exitos_por_escuela[$centro_actual_contacto]/$totales_centros[$centro_actual_contacto][0])), 2, ',', '.');?> %<br>
    <?php 
    }
    if ($exitos_por_escuela[$centro_actual_contacto]>0) {
    ?>
    <b>Tiempo medio de respuesta:</b> <?=number_format((($tiempo_total_escuela[$centro_actual_contacto]/$exitos_por_escuela[$centro_actual_contacto])/86400), 2, ',', '.');?> días. (Para los contactos finalmente inscritos)<br>
    <?php 
    }
    ?>
    <b>Total inscripciones:</b> <?=$totales_centros[$centro_actual_contacto][4]?> (incluyendo sin fecha confirmada)<br>
    <?php 
    if ($totales_centros[$centro_actual_contacto][0]>0) {
    ?>
    <b>Porcentaje de éxito:</b> <?=number_format((100*($totales_centros[$centro_actual_contacto][4]/$totales_centros[$centro_actual_contacto][0])), 2, ',', '.');?> %<br>
    <?php 
    }
    ?>
    <b>Totales por estado</b><br>
        b1: <?=$totales_centros[$centro_actual_contacto][1]?><br>
        b2: <?=$totales_centros[$centro_actual_contacto][2]?><br>
        b3: <?=$totales_centros[$centro_actual_contacto][3]?><br>
        &nbsp;&nbsp;c: <?=$totales_centros[$centro_actual_contacto][4]?>
    </small></td>
  </tr>
<tr>
    <td><img src="/imagen/nada.gif" width=1 height=<?=$altura_grafico?>></td>
<?php 
for ($c_mes=1;$c_mes<=12;$c_mes++) {

$mensaje_desglose="";

for ($anios=$anio_inicio;$anios<=$anio_fin;$anios++) {
        $mensaje_desglose=$mensaje_desglose."<a href='detalles_mes.php?<?=$centro_actual_contacto?>&mes=".$c_mes."&anio=".$anios."'>".$anios.": ".intval($totales_centros_meses_desglosado[$centro_actual_contacto][$c_mes][$anios])."</a><br>";
        }
$mensaje_desglose=$mensaje_desglose."<br>";
$busca_tipos_contactos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo");
while($tipos_contactos=db_fetch_array($busca_tipos_contactos)){
        if (intval($totales_tipos_centros_meses_desglosado[$centro_actual_contacto][$c_mes][$tipos_contactos["id_tipo"]])!=0) {
                $mensaje_desglose=$mensaje_desglose."<a title='".$tipos_contactos["id_tipo"].": ".$tipos_contactos["nombre_tipo"]."'>".$tipos_contactos["id_tipo"].": ".intval($totales_tipos_centros_meses_desglosado[$centro_actual_contacto][$c_mes][$tipos_contactos["id_tipo"]])."</a><br>";
                }
        }

if ($mensaje_desglose!="") {
        $mensaje_desglose=substr($mensaje_desglose,0,strlen($mensaje_desglose)-4);
        }

?>
<td align="center" valign="bottom"><a href='detalles_mes.php?id_escuela=<?=$centro_actual_contacto?>&mes=<?=$c_mes;?>&anio=<?=$anio_seleccionado;?>'><font size=2><b><?=intval($totales_centros_meses[$centro_actual_contacto][$c_mes]);?></b></a><br>C: <?=$totales_c_meses_centros[$centro_actual_contacto][$c_mes]?><br><small><?=$mensaje_desglose?></small><br><img src="/imagen/dias/M.gif" height="<?=round($totales_centros_meses[$centro_actual_contacto][$c_mes]/$unidades_grafico,0)?>" width="28" border=0></font></td>
<?php 
}
?>
</tr>
<tr>
<td></td>
<?php 
for ($c_mes=1;$c_mes<=12;$c_mes++) {
?>
<td align="center"><font size=1><b><?=$nombre_mes[$c_mes]?></b></font></td>
<?php 
}
?>
</tr>
</table>
<?php 
$busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
$total_columnas=db_num_rows($busca_tipos);
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
<tr>
        <td align="center" colspan=<?=$total_columnas+1;?>><font size=2><b>¿Cómo nos conoció?</b></font></td>
</tr>
<tr>
    <td><img src="/imagen/nada.gif" width=1 height=<?=$altura_grafico?>></td>
<?php 
while($datos_tipos=db_fetch_array($busca_tipos)) {
?>
<td align="center" valign="bottom" width="<?=round(((750-1)/$total_columnas),0);?>"><font size=2><b><?=intval($totales_tipos_centros[$centro_actual_contacto][$datos_tipos["id_tipo"]]);?></b><br><img src="/imagen/dias/M.gif" height="<?=round($totales_tipos_centros[$centro_actual_contacto][$datos_tipos["id_tipo"]]/$unidades_grafico,0)?>" width="28" border=0></font></a></td>
<?php 
}
?>
</tr>
<tr>
<td></td>
<?php 
$busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
while($datos_tipos=db_fetch_array($busca_tipos)) {
?>
<td align="center"><font size=1><b><?=$datos_tipos["id_tipo"]?>: <?=$datos_tipos["nombre_tipo"]?></b></font></td>
<?php 
}
?>
</tr>
</table>
<br>
<?php 
}


} else {
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ningún contacto.<br><br><br></td>
  </tr>
</table>
<?php 

}
?>
<br>
<input type="button" onclick="window.location.href='listado_contactos.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

