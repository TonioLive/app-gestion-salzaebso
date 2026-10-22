<?php  require("../../configuracion/parte_superior_sin_login_ancho.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT centro_aulas.*, cursos_academicos.*, centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);
$nombre_aula=$datos_aula["nombre_aula"];
$ciclo_aula=$datos_aula["ciclo"];

$curso_del_aula=$datos_aula["id_curso"];

$fecha_limite_presentacion=$datos_aula["fecha_limite_presentacion"];


$fecha_de_inicio_curso=$datos_aula["fecha_inicio"];

if (intval(strtotime($fecha_de_inicio_curso))>intval(time())) {
        $curso_iniciado=false;
        } else {
        $curso_iniciado=true;
        }

?>
<SCRIPT LANGUAGE=javascript>
<!--

var pagina_destino="/gestion/agenda_auxiliares/guarda_dato.php";

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);

    }
}

function guarda_checkbox(campo_busqueda,valor_busqueda,tabla,campo) {

        pagina_destino="/gestion/agenda_auxiliares/guarda_checkbox.php";

        sendAndLoad({desc:'campo_busqueda',dato:campo_busqueda},{desc:'valor_busqueda',dato:valor_busqueda},{desc:'tabla',dato:tabla},{desc:'campo',dato:campo});

        // window.open(pagina_destino+"?campo_busqueda="+campo_busqueda+"&valor_busqueda="+valor_busqueda+"&tabla="+tabla+"&campo="+campo);

        }

function guarda_dato(campo_busqueda,valor_busqueda,tabla,campo) {

        pagina_destino="/gestion/agenda_auxiliares/guarda_dato.php";
        
        valor_campo=document.getElementById(campo+valor_busqueda).value;
                
        sendAndLoad({desc:'campo_busqueda',dato:campo_busqueda},{desc:'valor_busqueda',dato:valor_busqueda},{desc:'tabla',dato:tabla},{desc:'campo',dato:campo},{desc:'valor_campo',dato:valor_campo});

        // window.open(pagina_destino+"?campo_busqueda="+campo_busqueda+"&valor_busqueda="+valor_busqueda+"&tabla="+tabla+"&campo="+campo+"&valor_campo="+valor_campo);

        }
        

//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de alumnos del Aula <?=$nombre_aula;?> <i>Curso <?=$datos_aula["nombre"];?></i><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 
$orden_en_aula=1;
$busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND alumnos.aula=? ORDER BY alumnos.agenda_grupo,usuarios.fecha_nacimiento DESC', 'ss', array($centro_actual, $id_aula));

if (db_num_rows($busca_alumnos)!=0) {

$busca_orden_en_aula=db_query_params('SELECT * FROM alumnos WHERE alumnos.aula=? AND orden_en_aula<>0', 's', array($id_aula));
$con_num_orden=db_num_rows($busca_orden_en_aula);
$busca_en_aula=db_query_params('SELECT * FROM alumnos WHERE alumnos.aula=?', 's', array($id_aula));
$total_alumnos_aula=db_num_rows($busca_en_aula);


?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#dddddd">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nº</b></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Nacionalidad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Grupo Trim. 1</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Grupo Trim. 2</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Grupo Trim. 3</b></font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        
        $suma_numeros_orden=0;
        $contador_nuevos=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
                $color_fuente_linea="#000000";
        
        if ($listado_alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
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
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        }
        
              $grupo_ninyo=$listado_alumnos["agenda_grupo"];
              $grupo_ninyo2=$listado_alumnos["agenda_2_grupo"];
              $grupo_ninyo3=$listado_alumnos["agenda_3_grupo"];
              
              if ($orden_en_aula & 1) {
              $color_fondo_fila="#ffffff";
              } else {
              $color_fondo_fila="#efefef";
              }
              
              
        ?>
        <tr bgcolor="<?=$color_fondo_fila?>">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$orden_en_aula;?></font></td>
        <td><b><font size=2 color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nac_alumno;?> - <?=$edad_alumno?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php  if ($listado_alumnos["sexo"]=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["nacionalidad"]?></font></td>
        <td align="center"><select name="agenda_grupo<?=$listado_alumnos["id_alumno"]?>" id="agenda_grupo<?=$listado_alumnos["id_alumno"]?>" style="font-size: 10px" onchange="guarda_dato('id_alumno','<?=$listado_alumnos["id_alumno"];?>','alumnos','agenda_grupo');">
        <option value="0"<?php  if ($grupo_ninyo==0) { print " selected"; } ?>>Sin grupo</option>
        <?php 
        for($grupos=1;$grupos<=12;$grupos++){
        ?>
        <option value="<?=$grupos;?>"<?php  if ($grupo_ninyo==$grupos) { print " selected"; } ?>>Grupo <?=$grupos?></option>
        <?php 
        }
        ?>
        </select></td>
        <td align="center"><select name="agenda_2_grupo<?=$listado_alumnos["id_alumno"]?>" id="agenda_2_grupo<?=$listado_alumnos["id_alumno"]?>" style="font-size: 10px" onchange="guarda_dato('id_alumno','<?=$listado_alumnos["id_alumno"];?>','alumnos','agenda_2_grupo');">
        <option value="0"<?php  if ($grupo_ninyo2==0) { print " selected"; } ?>>Sin grupo</option>
        <?php 
        for($grupos=1;$grupos<=12;$grupos++){
        ?>
        <option value="<?=$grupos;?>"<?php  if ($grupo_ninyo2==$grupos) { print " selected"; } ?>>Grupo <?=$grupos?></option>
        <?php 
        }
        ?>
        </select></td>
        <td align="center"><select name="agenda_3_grupo<?=$listado_alumnos["id_alumno"]?>" id="agenda_3_grupo<?=$listado_alumnos["id_alumno"]?>" style="font-size: 10px" onchange="guarda_dato('id_alumno','<?=$listado_alumnos["id_alumno"];?>','alumnos','agenda_3_grupo');">
        <option value="0"<?php  if ($grupo_ninyo3==0) { print " selected"; } ?>>Sin grupo</option>
        <?php 
        for($grupos=1;$grupos<=12;$grupos++){
        ?>
        <option value="<?=$grupos;?>"<?php  if ($grupo_ninyo3==$grupos) { print " selected"; } ?>>Grupo <?=$grupos?></option>
        <?php 
        }
        ?>
        </select></td>
        </tr>
        <?php 
        $orden_en_aula++;
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
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en este aula.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="history.go(0);" value="Ordenar los grupos"> <input type="button" onclick="window.close();" value="Cerrar Ventana" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
