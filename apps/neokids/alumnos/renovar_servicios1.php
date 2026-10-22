<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos

$id_alumno=$_POST["id_alumno"];
$id_inscripcion=$id_alumno;

$inicio=$_POST["fecha"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

$hora=time();

$fecha=date("Y/m/d",$fecha_time_stamp);
$hora_entrada=date("H:i:s",$hora);

$id_usuario=$_POST["id_usuario"];
$tipo_servicio=$_POST["tipo_servicio"];
$descuento_aplicado=$_POST["descuento_aplicado"];
$num_sesiones=$_POST["num_sesiones"];
$sesiones_semana=$_POST["sesiones_semana"];

// Busca servicio a renovar
$busca_tarifa=db_query("SELECT * FROM tarifa WHERE numero_bonos=$num_sesiones AND sesiones_semana=$sesiones_semana AND tipo_neokids=$tipo_servicio") or die(db_error());

if (db_num_rows($busca_tarifa)!=0) {
        $servicios=db_fetch_array($busca_tarifa);
        $id_tarifa_aplicada=$servicios["ID"];
        $nombre_tarifa_aplicada=$servicios["ARTICULO"];
        $precio_tarifa_aplicada=$servicios["PRECIO_VENTA"];
        $iva_tarifa_aplicada=0.21;
        }
        
$consulta_inscripcion=db_query_params('SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_alumno=?', 's', array($id_inscripcion)) or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);

$usuario_curso=$datos_inscripcion["curso"];
$usuario_aula=$datos_inscripcion["aula"];

$usuario_fechains_dia=date("j",strtotime($datos_inscripcion["fecha_inscripcion"]));
$usuario_fechains_mes=date("n",strtotime($datos_inscripcion["fecha_inscripcion"]));
$usuario_fechains_ano=date("Y",strtotime($datos_inscripcion["fecha_inscripcion"]));
if ($datos_inscripcion["fecha_inscripcion"]=="0000-00-00") {
                                $usuario_fechains_dia="";
                                $usuario_fechains_mes="";
                                $usuario_fechains_ano="";
                                }

$usuario_fechamat_dia=date("j",strtotime($datos_inscripcion["fecha_matricula"]));
$usuario_fechamat_mes=date("n",strtotime($datos_inscripcion["fecha_matricula"]));
$usuario_fechamat_ano=date("Y",strtotime($datos_inscripcion["fecha_matricula"]));
if ($datos_inscripcion["fecha_matricula"]=="0000-00-00") {
                                $usuario_fechamat_dia=date("j",strtotime($datos_inscripcion["fecha_inicio"]));
                                $usuario_fechamat_mes=date("n",strtotime($datos_inscripcion["fecha_inicio"]));
                                $usuario_fechamat_ano=date("Y",strtotime($datos_inscripcion["fecha_inicio"]));
                                }

$usuario_fechabaja_dia=date("j",strtotime($datos_inscripcion["fecha_baja"]));
$usuario_fechabaja_mes=date("n",strtotime($datos_inscripcion["fecha_baja"]));
$usuario_fechabaja_ano=date("Y",strtotime($datos_inscripcion["fecha_baja"]));
if ($datos_inscripcion["fecha_baja"]=="0000-00-00") {
                                $usuario_fechabaja_dia=date("j",strtotime($datos_inscripcion["fecha_fin"]));
                                $usuario_fechabaja_mes=date("n",strtotime($datos_inscripcion["fecha_fin"]));
                                $usuario_fechabaja_ano=date("Y",strtotime($datos_inscripcion["fecha_fin"]));
                                }

$usuario_fechamat=strtotime($datos_inscripcion["fecha_matricula"]);
$usuario_fechabaja=strtotime($datos_inscripcion["fecha_baja"]);

$motivo_baja=$datos_inscripcion["motivo_baja"];
$tipo_baja=$datos_inscripcion["tipo_baja"];

$num_dias_asiste=$datos_inscripcion["num_dias_asiste"];

$id_usuario_seleccionado=$datos_inscripcion["id_usuario"];

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado)) or die(db_error()."-3");

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                        // Edad actual del niño en años con decimales
                        $edad_alumno=date("Y")-date("Y",strtotime($datos_usuario["fecha_nacimiento"]));
                        if (date("m")<date("m",strtotime($datos_usuario["fecha_nacimiento"]))) {
                                $edad_alumno=$edad_alumno-1;
                                }
                        $edad_alumno_anos=$edad_alumno;
                        // Decimales
                        $mes_edad_alumno=date("n")-date("n",strtotime($datos_usuario["fecha_nacimiento"]));

                        if ($mes_edad_alumno<0) {
                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                } else {
                                $edad_alumno_meses=$mes_edad_alumno;
                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                }
        if ($edad_alumno_anos>1) {
                $plural_anios="s";
                } else {
                $plural_anios="";
                }
        if ($edad_alumno_meses>1) {
                $plural_meses="es";
                } else {
                $plural_meses="";
                }

                }
        }


$trimestre_actual=1;
$ejercicio_actual=2015;

$busca_trimestre=db_query("SELECT * FROM aulas_grupos_trimestres WHERE ejercicio=$ejercicio_actual AND trimestre=$trimestre_actual") or die(db_error());

$datos_trimestre=db_fetch_array($busca_trimestre);

$dias_semana_asiste="";

$busca_dias_asiste=db_query_params('SELECT * FROM aulas_grupos,aulas_alumnos_grupos WHERE aulas_grupos.id_grupo=aulas_alumnos_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? ORDER BY aulas_grupos.dia', 's', array($id_inscripcion)) or die(db_error());

$total_dias_asiste=db_num_rows($busca_dias_asiste);

$contador=0;

$proximo_inicio=0;
$proximo_fin=0;


$nombres_campos_dias=array("","l","m","x","j","v","s","d");

while($datos_dias_asiste=db_fetch_array($busca_dias_asiste)) {

        if ($proximo_inicio==0) {
                $proximo_inicio=strtotime($datos_trimestre["ini_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                } else {
                if ($proximo_inicio>=strtotime($datos_trimestre["ini_".$nombres_campos_dias[$datos_dias_asiste["dia"]]])) {
                        $proximo_inicio=strtotime($datos_trimestre["ini_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                        }
                }


        if ($proximo_fin==0) {
                $proximo_fin=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                } else {
                if ($proximo_fin<=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]])) {
                        $proximo_fin=strtotime($datos_trimestre["fin_".$nombres_campos_dias[$datos_dias_asiste["dia"]]]);
                        }
                }



        $dias_semana_asiste.=$dia_sem_largo[$datos_dias_asiste["dia"]];
        $contador++;

        if ($contador==($total_dias_asiste-1)) {
                $dias_semana_asiste.=" y ";
                } else {
                if ($contador<$total_dias_asiste) {
                        $dias_semana_asiste.=", ";
                        }
                }

        }
        
$proximo_fin_dia=$_POST["proximo_fin_dia"];        
$proximo_fin_mes=$_POST["proximo_fin_mes"];
$proximo_fin_ano=$_POST["proximo_fin_ano"];

$proximo_fin=strtotime("$proximo_fin_ano-$proximo_fin_mes-$proximo_fin_dia");
 
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Renovar Servicios</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">

<script>


</script>
</head>
<body>
<div class="container-fluid">
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2>Renovar Servicios</h2>
  <b>Alumno:</b> <?=$usuario_nombre?> <?=$usuario_apellido1?> <?=$usuario_apellido2?><br>
  <b>Edad actual del usuario:</b> <?=$edad_alumno_anos;?> año<?=$plural_anios?> y <?=$edad_alumno_meses?> mes<?=$plural_meses?></b><br>
  <b>Días que asiste:</b> <?=$dias_semana_asiste;?><br />
  <b>Fechas siguiente trimestre:</b> del <?=date("d/m/Y",$proximo_inicio)?> al <?=date("d/m/Y",$proximo_fin)?><br /></b></div>
  <div class="panel-body">
  <p align=center>
  <?php 
  if ($id_tarifa_aplicada=="") {
  ?>
   <div align=center>
   		<h4>No se han encontrado servicios con las condiciones propuestas (tipo y número de sesiones)</h4><br>
        <button type="button" onclick="window.location.href='renovar_servicios.php?i=<?=$id_inscripcion?>';" class="btn btn-danger">Cancelar</button> <button type="button" onclick="history.go(-1);" class="btn btn-success">Volver atrás y corregir</button>
        </div>
  <?php 
  } else {
  ?>
  <b>Se va a renovar al alumno con el siguiente servicio:</b><br><br>
  <?=$nombre_tarifa_aplicada;?>, Precio: <?=number_format($precio_tarifa_aplicada*(1+$iva_tarifa_aplicada),2,".",",");?> €<br><br>
  <?php 
  if ($descuento_aplicado!=0) {
        ?>
        Descuento: <?=$descuento_aplicado?> %, Total a pagar incluido descuento: <?=number_format(($precio_tarifa_aplicada*(1+$iva_tarifa_aplicada))*(1-($descuento_aplicado/100)),2,".",",")?> €<br><br>
        <?php 
        }
  ?>
  <b>¿Es correcto?</b><br/>
  </p>
    <form method="post" action="renovar_servicios2.php" name="servicios">
    <input type="hidden" value="<?=$id_inscripcion?>" name="id_alumno">
    <input type="hidden" value="<?=$id_tarifa_aplicada?>" name="id_servicio">
    <input type="hidden" value="<?=$descuento_aplicado?>" name="descuento">
    <input type="hidden" value="<?=$id_usuario_seleccionado?>" name="id_usuario">
    <input type="hidden" value="<?=$proximo_inicio?>" name="fecha_inicio">
    <input type="hidden" value="<?=$proximo_fin?>" name="fecha_fin">
    <div align=center>
        <button type="button" onclick="window.location.href='renovar_servicios.php?i=<?=$id_inscripcion?>';" class="btn btn-danger">Cancelar</button> <button type="submit" class="btn btn-success">Aceptar</button>
        </div>
    </form>
    <?php 
    }
    ?>
  </div>
</div>

</div>
</body>
</html>
