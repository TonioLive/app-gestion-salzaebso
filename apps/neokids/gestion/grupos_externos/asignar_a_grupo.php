<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$tipo_piscina=2;

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha_limite=$_GET["fecha_limite"];

$fecha_time_stamp=strtotime($fecha_limite);

if (strlen($id_subgrupo)!=0) {

        $busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

        $datos_grupo=db_fetch_array($busca_datos_subgrupo);

        $id_curso_actual_neokids=$datos_grupo["id_curso"];
        $c_dia=$datos_grupo["dia"];
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual_neokids));
        $datos_curso_actual_nk=db_fetch_array($busca_curso_actual);
        $nombre_curso_actual_nk=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        
}

// Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM gestionguarderias.cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM gestionguarderias.cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM gestionguarderias.cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

$num_dias_asisten_externos=$datos_curso_actual_nk["num_dias_asisten_externos"];

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite);

$fecha=$_GET["fecha"];
if (strlen($fecha)==0) {
        $fecha=strtotime($fecha_limite)-$tiempo_gracia;
}

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Detalles Grupo</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>
.fondo_nivel0 {
        background: #fff;
        }
.fondo_nivel1 {
        background: #9ed09c;
        }
.fondo_nivel2 {
        background: #9cbdd0;
        }
.fondo_nivel3 {
        background: #d0af9c;
        } 
.fondo_nivel4 {
        background: #f9a63b;
        } 
.fondo_nivel5 {
        background: #ffa9f0;
        }  

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
        
</style>  
<script>

function ver_eval_lapices(codigo) {

        $('#grupoModal').modal('show');
        $('#detalles_evaluacion').attr('src', '/gestion/agenda/evaluacion_piscina.php?lista_alumnos=L'+codigo);

        }

function agrega_alumno(id_alumno) {

        $('#boton_alumno' + id_alumno).attr('disabled',true);
        
        // top.location.href = "asigna_alumno.php?origen=<?=$_SERVER['HTTP_REFERER'];?>&fecha=<?=$fecha_time_stamp?>&id_alumno=" + id_alumno + "&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>";

        url = "asigna_alumno_ajax.php?origen=<?=$_SERVER['HTTP_REFERER'];?>&fecha=<?=$fecha_time_stamp?>&id_alumno=" + id_alumno + "&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>";

        $.ajax({
                url: url
                });

}

function salir() {

        top.location.href='<?=$_SERVER['HTTP_REFERER'];?>';

}

</script>     
</head>
<body>
<div class="container-fluid">
<?php 
// Busca alumnos grupo
if (strlen($id_subgrupo)!=0) {

$estado_aulas=array();

$busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE piscina=$tipo_piscina ORDER BY id_centro");

while($lista_centros=db_fetch_array($busca_centros)) {

        $busca_aulas=db_query_params('SELECT * FROM gestionguarderias.centro_aulas WHERE id_curso=? AND id_centro=? AND tipo=0 ORDER BY edad_minima', 'ss', array($id_curso_actual, $lista_centros["id_centro"]));

        while($lista_aulas=db_fetch_array($busca_aulas)) {

                $busca_alumnos=db_query_params('SELECT * FROM gestionguarderias.usuarios, gestionguarderias.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND gestionguarderias.alumnos.aula=? AND gestionguarderias.alumnos.fecha_matricula<=? AND gestionguarderias.alumnos.fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($lista_aulas["id_aula"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

                $total_alumnos_nuevos=0;

                if (db_num_rows($busca_alumnos)!=0) {


                        while($alumnos=db_fetch_array($busca_alumnos)) {

                        // Comprueba si está asignado

                        $busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_lapices_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual_neokids));

                                if (db_num_rows($busca_grupo_alumno)<$num_dias_asisten_externos) {
                                        $total_alumnos_nuevos++;
                                }
                        }

                $estado_aulas[$lista_aulas["id_aula"]]=$total_alumnos_nuevos;

                }
        }
}

$total_nuevos_alumnos=0;

// Inicio bucle aulas

$inicial_in=" in";

$busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE piscina=$tipo_piscina ORDER BY id_centro");

while($lista_centros=db_fetch_array($busca_centros)) {

$busca_aulas=db_query_params('SELECT * FROM gestionguarderias.centro_aulas WHERE id_curso=? AND id_centro=? AND tipo=0 ORDER BY edad_minima', 'ss', array($id_curso_actual, $lista_centros["id_centro"]));

$nombres_centros[$lista_centros["id_centro"]]=$lista_centros["nombre_centro"];

while($lista_aulas=db_fetch_array($busca_aulas)) {

$nombres_aulas[$lista_aulas["id_aula"]]=$lista_aulas["nombre"];

$busca_alumnos=db_query_params('SELECT * FROM gestionguarderias.usuarios, gestionguarderias.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND gestionguarderias.alumnos.aula=? AND gestionguarderias.alumnos.fecha_matricula<=? AND gestionguarderias.alumnos.fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($lista_aulas["id_aula"], (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

if ($estado_aulas[$lista_aulas["id_aula"]]!=0) {

if (db_num_rows($busca_alumnos)!=0) {

?>  
<table class="table table-condensed table-striped table-hover">
        <tr>
                <td colspan="5"><b><?=$lista_aulas["nombre"]?> - <?=$lista_centros["nombre_centro"]?></b></td>
        </tr>
        <tr>
                <td class="col-md-6"><b>Nombre</b></td>
                <td class="col-md-1"><b>Edad</b></td>                        
                <td class="col-md-2"><b>Fecha Inicio</b></td>
                <td class="col-md-2"><b>Fecha Baja</b></td>
                <td class="col-md-1"><b>&nbsp;</b></td>                                
        </tr>
<?php 

while($alumnos=db_fetch_array($busca_alumnos)) {
$inicial_in="";
// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_lapices_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual_neokids));

if (db_num_rows($busca_grupo_alumno)<$num_dias_asisten_externos) {

$dias_faltan=$num_dias_asisten_externos-db_num_rows($busca_grupo_alumno);

if ($dias_faltan>0) {

if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                                if ($dif_meses==0) {
                                        $edad_d_l="";
                                        } else {
                                        $unidad_meses="meses";
                                        if ($dif_meses==1) {
                                                $unidad_meses="mes";
                                                }
                                        if ($dif_meses<0) {
                                                $dif_meses=12+$dif_meses;
                                                $edad_a_l=$edad_a_l-1;
                                        }
                                        $edad_d_l=" y ".$dif_meses." ".$unidad_meses;
                                        }                                
                $edad_alumno=$edad_a_l." años".$edad_d_l;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 

        $total_nuevos_alumnos++;

        // Busca evaluaciones anteriores
        $busca_alumnos_usuario=db_query_params('SELECT *, agenda_programacion_evaluaciones_estado_alumnos.id_curso AS curso_evaluacion FROM gestionguarderias.alumnos, agenda_programacion_evaluaciones_estado_alumnos WHERE gestionguarderias.alumnos.id_alumno=agenda_programacion_evaluaciones_estado_alumnos.id_alumno AND gestionguarderias.alumnos.id_usuario=? AND gestionguarderias.alumnos.id_alumno<>? ORDER BY gestionguarderias.alumnos.fecha_matricula DESC LIMIT 0,1', 'ss', array($alumnos["id_usuario"], $alumnos["id_alumno"])) or die(db_error());
        $boton_evaluacion="";
        while($otras_evaluaciones=db_fetch_array($busca_alumnos_usuario)) {
                $boton_evaluacion=' <button class="btn btn-success btn-xs" id="boton_informe' . $otras_evaluaciones["id_alumno"] . '" onclick="ver_eval_lapices(' . $otras_evaluaciones["id_alumno"] . ')" title="Ver evaluación del curso '.$nombres_cursos[$otras_evaluaciones["curso_evaluacion"]].'"> <i class="fa fa-file-text"></i></button>';
        }

        $fila_tabla='<tr' . $color_fuente_linea . ' >'
                . '<td>' . $alumnos["Nombre"] . ' ' . $alumnos["Apellido1"] . ' ' . $alumnos["Apellido2"] . '</td>'
                . '<td>' . $edad_alumno . '</td>'                        
                . '<td>' . date("d/m/Y",strtotime($alumnos["fecha_matricula"])) . $coletilla_fecha . '</td>'
                . '<td>' . date("d/m/Y",strtotime($alumnos["fecha_baja"])) . '</td>'
                . '<td><button class="btn btn-primary btn-xs" id="boton_alumno' . $alumnos["id_alumno"] . '" onclick="agrega_alumno(' . $alumnos["id_alumno"] . ')" title="Añadir alumno al grupo"> <i class="fa fa-user-plus"></i></button>'.$boton_evaluacion.'</td>'
                . '</tr>';

        echo($fila_tabla);

                }
        }
}
?>
</table>
<?php 
                        }
                }
        }
}

// Fin bucle aulas

if ($total_nuevos_alumnos==0) {
?>
<h4>No hay alumnos pendientes de asignar.</h4>
<?php 
}

}
?>                          
</div>

<!-- Modal -->
<div class="modal modal-wide fade" id="grupoModal" tabindex="-1" role="dialog" aria-labelledby="grupoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <iframe src="/gestion/agenda/evaluacion_piscina.php" name="detalles_evaluacion" id="detalles_evaluacion" width=100% height=350 frameborder=0></iframe>
      </div>
    </div>
  </div>
</div>

</body>
</html>
