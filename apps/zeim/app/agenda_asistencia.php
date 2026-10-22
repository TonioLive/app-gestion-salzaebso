<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}

   if ($errores=="") {
           
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE id_usuario=?', 's', array($id_usuario));
    while($padres=db_fetch_array($busca_padres)){
            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
            }
    if ($filtro_padres!="") {
            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
            }      
      
                  
    // Busca mensajes 
    
	$filtro_busqueda="";
	
	$busqueda=$_GET["q"];
	$tipo_b="OR";
	
	if ($busqueda) {

        // Desglosa una frase en palabras sueltas

          $palabra=explode(" ",$busqueda);

          $query="(";
          for ($cont=0; $cont<count($palabra); $cont++)
                {
                $query=$query."((agenda_notas_padres.Asunto like '%".$palabra[$cont]."%')";
                $query=$query." OR (agenda_notas_padres.texto_nota like '%".$palabra[$cont]."%')";
                $query=$query.") ".$tipo_b." ";

                $palabra_actual=$palabra_actual+1;
                }
            
          if ($tipo_b=="OR") { $cacho=4; }
          if ($tipo_b=="AND") { $cacho=5; }
          $query=substr($query,0,(strlen($query)-$cacho));
          $query=$query.")";

		$filtro_busqueda=" AND $query";
		
	}      
           
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $subtipos_mensajes=array(" a la educadora"," a la coordinadora");
        $estados_mensajes=array("<span class='text-danger'>No leido</span>","Leido","Cerrado");
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda ORDER BY fecha DESC, hora DESC");              
    
    $resultado_total=db_num_rows($busca_mensajes);
    $inicio_listado=$_GET["i"];
    $numero_resultados=10;
    if (strlen($inicio_listado)==0) {
    	$inicio_listado=0;
    }       
       
    $nombe_script="mensajeria";   
        
    $busca_ultimos_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda ORDER BY fecha DESC, hora DESC LIMIT $inicio_listado,$numero_resultados"); 
    
    $total_resultado_busqueda=db_num_rows($busca_ultimos_mensajes);
     
    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda AND tipo_nota=2 AND ((fecha>='2018-01-31' AND leido_por NOT like '%[".$id_usuario."]%') OR (fecha<'2018-01-31' AND estado=0)) ORDER BY fecha DESC"); 
    
    $texto_no_leidos="";
    if (db_num_rows($busca_no_leidos)!=0) {
            if (db_num_rows($busca_no_leidos)) {
            $coletilla_leido="";
            if (db_num_rows($busca_no_leidos)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $texto_no_leidos=" (<font color='#f00'>".db_num_rows($busca_no_leidos)." no leido$coletilla_leido</font>)";
            }
               
        }	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Mensajes 
        <small><?=$texto_no_leidos?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
        <li class="active"><i class="fa fa-calendar-check-o"></i> Agenda Escolar</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php 
       
if ($errores!="") {
    ?>
    <div align="center">
    <br><br><big><?=$errores?></big><br><br>
    <a class="btn btn-primary" type="button" href="/app/index.php">Volver</a>
    </div>
    <?php 
    } else {
    ?>
    <div align="center">
    <?php 
    // Busca hijos del padre
    $busca_datos_alumno=db_query_params('SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=? AND alumnos.fecha_baja>=? AND alumnos.fecha_matricula<=? ORDER BY alumnos.id_curso DESC', 'sss', array($id_usuario, (string)(date("Y-m-d", $fechalimite)), (string)(date("Y-m-d")))) or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {
    
        $contador_hijos=0;
        $array_hijos=array();
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                $busca_datos_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_hijos["id_curso"]));
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                        } else {
                        $nombre_curso_mostrado="";
                        }
                        
                // Datos de la escuela del niño
                $busca_datos_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_hijos["id_centro"]));

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del niño
                $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_hijos["aula"]));

                $datos_aula=db_fetch_array($busca_datos_aula);
                $array_hijos[$contador_hijos][0]=$datos_hijos["padre_de"];
                $array_hijos[$contador_hijos][1]=$datos_hijos["id_alumno"];
                $array_hijos[$contador_hijos][2]=$datos_hijos["id_centro"];
                $array_hijos[$contador_hijos][3]=$datos_hijos["aula"];
                $array_hijos[$contador_hijos][4]=$datos_hijos["Nombre"];
                $contador_hijos++;
                }
        // Muestra calendario

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
}


$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_curso_actual)!=0) {
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        } else {
        $id_curso_actual=0;
        }

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_time_stamp);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_time_stamp);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }

//Inicio es el día en el que empieza la primera semana del mes
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;

$dias_laborales_mes=0;
$dias_trabajados_mes=0;
$no_ficha_salida=0;
?>
<font color="#000000" size=3><b>Agenda <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?></b></font><br><br>
<font color="#000000" size=2>Para ver el informe de un día toca el botón con el nombre de tu hijo/a.<br/>
Si el nombre aparece de color <font color="#2e921d">verde</font>, el informe de ese día está disponible.
Si aparece de color <font color="#7b3535">rojo</font>, no está disponible.<br />
El informe de hoy no estará disponible hasta las 17:00 hrs.
Hasta esa hora el nombre aparecerá de color <font color="#0000ff">azul</font>.</font>
<table border=0 cellspacing="1" cellpadding="1" width="100%" height="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font color="#ffffff" size=3>
<?php 
for ($dia_s=1; $dia_s<=5; $dia_s=$dia_s+1)
{
?>
</font>
<td valign="top">
<font color="#ffffff" size=3>
<b>
<?php    echo $dia_sem_largo[$dia_s]; ?>
</b>
</font>
</td>
<?php 
}
?>
</tr>
<?php 
$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{
?>
<tr>
<?php 
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {

  if ($dias<=5) {

  if ($mes==date("n",$inicio_time_stamp))
    {
    $fondo_celda_dia="#EFEFEF";
    } else {
    $fondo_celda_dia="#AAAAAA";
    }
  if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    $fondo_celda_dia="#DD9999";
    }
?>
<td align="right" bgcolor="<?=$fondo_celda_dia;?>" valign="top">
<font color="#000000" size=3>
<?php 
    if ($mes==date("n",$inicio_time_stamp))
    {
      print "<b>";    }
    if ($dias==7)
    {
      print "<font color=#800000>";    }
    if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    print "<font color='blue'>";    }
    print (date("j",$inicio_time_stamp));
    if ($mes==date("n",$inicio_time_stamp))
    {
    print "</b>";
    }
    print "</a>";
    $no_asistencia=0;


?>
</font><div align="center"><?php 

for ($c_hijos=0;$c_hijos<count($array_hijos);$c_hijos++) {

// Comprueba asistencia
$mostrar_informe=false;
$mensaje_informe=$array_hijos[$c_hijos][4]." no asiste a la escuela el día ".date("d/m/Y",$inicio_time_stamp)." o el informe no está disponible para esta fecha.";
$color_boton="#7b3535";
        
$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$array_hijos[$c_hijos][0]." AND id_servicio=".$array_hijos[$c_hijos][3]." AND fecha='".date("Y/m/d",$inicio_time_stamp)."'") or die(db_error());
if (db_num_rows($busca_registro_entrada)!=0) {
        $mostrar_informe=true;
        $color_boton="#2e921d";
        }

// Comprueba si es hoy

$busca_mensualidad=db_query("SELECT * FROM alumnos WHERE id_alumno=".$array_hijos[$c_hijos][1]);

$hora_limite=17;

if (db_num_rows($busca_mensualidad)!=0) {
        $datos_del_cliente=db_fetch_array($busca_mensualidad);

        if ($datos_del_cliente["tipo_mensualidad"]==1) {
                // Media jornada
                if ($datos_del_cliente["comedor"]!=2) {
                        // Se queda a comer
                        $hora_limite=15;
                        } else  {
                        // No se queda a comer
                        $hora_limite=12;
                        }
                }

        }
        
        
if ((date("m/d/Y",$inicio_time_stamp)==date("m/d/Y")) and (date("G")<$hora_limite)) {
        $mostrar_informe=false;
        $mensaje_informe="El informe de hoy estará disponible a partir de las ".$hora_limite.":00 hrs.";
        $color_boton="#0000ff";

        if ($id_usuario==10200) { // Solo usuario DEMO puede verse el informe antes de las 17:00
                $mostrar_informe=true;
        }

        }


if ($inicio_time_stamp<strtotime("2012-12-16")) {
        $mostrar_informe=false;
        $mensaje_informe="El informe no está disponible para esta fecha.";
        $color_boton="#7b3535";
        }

if ($mostrar_informe==true) {

// comprueba si leido

$busca_lectura=db_query("SELECT * FROM agenda_registro_lecturas WHERE dia_informe='".date("Y-m-d",$inicio_time_stamp)."' AND id_padre=$id_usuario AND id_alumno=".$array_hijos[$c_hijos][0]);

if (db_num_rows($busca_lectura)!=0) {
	$color_boton="#ff9600";
	}

?><input type="button" id="boton_<?=$inicio_time_stamp?>" value="<?=$array_hijos[$c_hijos][4]?>" onclick="document.getElementById('boton_<?=$inicio_time_stamp?>').style.color='#ff9600';window.location.href='/app/agenda/informe_dia_html.php?dia=<?=$inicio_time_stamp;?>&a=<?=$array_hijos[$c_hijos][0]?>';" style="font-size: 12px; color: <?=$color_boton?>"><br /><?php 
        } else {
?><input type="button" value="<?=$array_hijos[$c_hijos][4]?>" onclick="mostrar_mensaje('<?=$mensaje_informe;?>');" style="font-size: 12px; color: <?=$color_boton?>"><br /><?php 
        }
}
?></div>
</td>
<?php 
        }
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php 
}
?>
<tr>
<td>
</td>
</tr>
</table>
<br>
<a class="btn btn-primary btn-sm" href="<?=$_SERVER["PHP_SELF"];?>?dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1"><< Mes anterior</a> <a class="btn btn-primary btn-sm" href="<?=$_SERVER["PHP_SELF"];?>?dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1">Mes siguiente >></a>
</font>
<br>
<br>
<table>
<tr>
<td colspan=2 align="center" bgcolor="#efefef"><font size=2><b>Leyenda de colores</b></font></td>
</tr>
<tr>
<td bgcolor="#2e921d"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe disponible.</font></td>
</tr>
<tr>
<td bgcolor="#ff9600"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe disponible y ya leido anteriormente. *</font></td>
</tr>
<tr>
<td bgcolor="#0000ff"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe no disponible hasta la salida de la escuela.</font></td>
</tr>
<tr>
<td bgcolor="#7b3535"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe no disponible, ese día no ha asistido a la escuela.</font></td>
</tr>
</table>
        <?php 
        // Fin Calendario
        }
    }
    ?></div>
      <!-- / FINAL CONTENIDO -->
      </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- Modal -->
<div class="modal fade" id="modalNota" tabindex="-1" role="dialog" aria-labelledby="modalNotaLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalNotaLabel">El informe no se puede mostrar</h4>
      </div>
      <div class="modal-body" id="modalNotaBody">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>

function mostrar_mensaje(mensaje) {
        $("#modalNota .modal-body").html(mensaje);
        $("#modalNota").modal('show');
}

</script>
<?php

require("parte_inferior_padres.php");
	
?>