<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_inscripcion=$_GET["i"];
$origen_formulario=$_GET["origen_formulario"];

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


// Detecta trimestres
$fecha_actual=date("Y-m-d");
$ejercicio_actual=date("Y");

$busca_trimestre=db_query("SELECT * FROM aulas_grupos_trimestres WHERE ejercicio=$ejercicio_actual AND fecha_inicio<='$fecha_actual' AND fecha_fin>='$fecha_actual'") or die(db_error());

$datos_trimestre=db_fetch_array($busca_trimestre);

$trimestre_actual=$datos_trimestre["trimestre"];

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
</head>
<body>
<form method="post" action="renovar_servicios1.php" name="servicios">
    <input type="hidden" value="<?=$id_inscripcion?>" name="id_alumno">
    <input type="hidden" value="<?=$id_usuario_seleccionado?>" name="id_usuario">
<div class="container-fluid">
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2>Renovar Servicios</h2>
  <b>Alumno:</b> <?=$usuario_nombre?> <?=$usuario_apellido1?> <?=$usuario_apellido2?><br>
  <b>Edad actual del usuario:</b> <?=$edad_alumno_anos;?> año<?=$plural_anios?> y <?=$edad_alumno_meses?> mes<?=$plural_meses?></b><br>
  <b>Días que asiste:</b> <?=$dias_semana_asiste;?><br />
  <b>Fechas siguiente periodo:</b> del <?=date("d/m/Y",$proximo_inicio)?> al &nbsp; <select size=1 style="font-size=10" name=proximo_fin_dia>
    <?php  
  $dia=date("d",$proximo_fin);
  $sele="";
  for ($d=1; $d<=31; $d=$d+1)
  {

    if ($d<10)
    {
      $d="0".$d;
    } 
    if (intval($d)==intval($dia))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $d; ?>"><?= $d; ?></option>
    <?php  

  } 

?>
    </select>/
    <select size=1 style="font-size=10" id=select1 name=proximo_fin_mes>
    <?php  
  $mes=date("m",$proximo_fin);
  $sele="";
  for ($m=1; $m<=12; $m=$m+1)
  {

    if ($m<10)
    {
      $m="0".$m;
    } 
    if (intval($m)==intval($mes))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
    <?php  

  } 

?>
    </select>/
    <select size=1 style="font-size=10" id=select2 name=proximo_fin_ano>
    <?php  
  $ano=date("Y",$proximo_fin);
  $sele="";
  for ($a=2009; $a<=date("Y")+2; $a=$a+1)
  {

    if (intval($a)==intval($ano))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $a; ?>"><?= $a; ?></option>
    <?php  

  } 

?>
    </select> <a href="javascript:seleccionar_fecha('proximo_fin');" title="Selecciona la fecha en el calendario"><i class="fa fa-calendar"></i></a><br /></b></div>
  <div class="panel-body">
  
  <b>Lista de servicios contratados anteriormente</b><br>
<?php 
$busca_servicios=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? ORDER BY servicios_contratados.fecha_inicio', 's', array($id_inscripcion));

if (db_num_rows($busca_servicios)==0) {
        ?>
        <div class="panel panel-danger">
        <div class="panel-body">
        <h4>El usuario todavía no ha contratado ningún servicio.</h4>
        </div>
        </div>
        <?php 
        } else {
        ?>
        <table class="table table-condensed">
        <tr>
        <td>Servicio</td>
        <td>Fecha</td>
        <td>Precio</td>
        <td>Cantidad</td>
        <td>Descuento</td>
        <td>Importe</td>
        </tr>
        <?php 
        $tipo_servicio_anterior=2;
        $descuento_anterior=0;
        while($servicios=db_fetch_array($busca_servicios)) {

                if (strpos($servicios["ARTICULO"],"bebe")==true) {
                        $tipo_servicio_anterior=1;
                        }
                if (strpos($servicios["ARTICULO"],"Aquagym")==true) {
                        $tipo_servicio_anterior=3;
                        }
                        
                // Calcula importe
                $base_imponible=round(($servicios["PRECIO_VENTA"]*$servicios["cantidad"]),2);

                if ($servicios["descuento"]==10) {
                        $descuento_anterior=$servicios["descuento"];
                        }
                if ($servicios["descuento"]==5) {
                        $descuento_anterior=$servicios["descuento"];
                        }

                $descuento=round($base_imponible*round(($servicios["descuento"]/100),4),2);
                $base_inc_dto=$base_imponible-$descuento;
                $importe=round($base_inc_dto*(1+($servicios["porcentaje_iva"])),2);
                $num_sesiones_semana_anterior=$servicios["sesiones_semana"];
                ?>
        <tr>
        <td><?=$servicios["ARTICULO"];?></td>
        <td><?=date("d/m/Y",strtotime($servicios["fecha_inicio"]));?></td>
        <td align=right><?=number_format($servicios["PRECIO_VENTA"],2,",",".")?> €</td>
        <td align=right><?=$servicios["cantidad"]?></td>
        <td align=right><?=number_format($servicios["descuento"],2,",",".")?> %</td>
        <td align=right><?=number_format($importe,2,",",".")?> €</td>
        </tr>
                <?php 
                }
        ?>
        </table>
        <?php 
        }
?>
<table class="table table-condensed">
    <tr>
    <td align="right" class="col-md-1">Nº de sesiones a renovar:</td>
    <td class="col-md-1"><select name="num_sesiones" class="form-control">
                                                            <?php 
                                for ($num_sesiones_semana=1;$num_sesiones_semana<=18;$num_sesiones_semana++) {
                                $total_sesiones_renovar=$num_sesiones_semana;
                                ?>
                                <option value="<?=$total_sesiones_renovar;?>"<?php  if ($total_sesiones_renovar==($num_dias_asiste*9)) { print " selected"; } ?>><?=$total_sesiones_renovar;?></option>
                                <?php 
                                }
                                ?>
                                                                                </select></td>
    </tr>
    <tr>
    <td align="right" class="col-md-1">Nº de sesiones semanales (Solo Aquagym):</td>
    <td class="col-md-1"><select name="sesiones_semana" class="form-control">
                                                            <?php 
                                for ($num_sesiones_semana=1;$num_sesiones_semana<=2;$num_sesiones_semana++) {
                                ?>
                                <option value="<?=$num_sesiones_semana;?>"<?php  if ($num_sesiones_semana==$num_sesiones_semana_anterior) { print " selected"; } ?>><?=$num_sesiones_semana;?></option>
                                <?php 
                                }
                                ?>
                                                                                </select></td>
    </tr>    
    <tr>
    <td align="right" class="col-md-1">Típo servicio renovado:</td>
    <td class="col-md-1"><select name="tipo_servicio" class="form-control">
                                <option value="2"<?php  if ($tipo_servicio_anterior==1) { print " selected"; } ?>>Bebes</option>
                                <option value="1"<?php  if ($tipo_servicio_anterior==2) { print " selected"; } ?>>Infantil</option>
                                <option value="3"<?php  if ($tipo_servicio_anterior==3) { print " selected"; } ?>>Aquagym</option>
                                <option value="4"<?php  if ($tipo_servicio_anterior==4) { print " selected"; } ?>>Matronatación</option>
                        </select></td>
    </tr>
    <tr>
    <td align="right" class="col-md-1">Descuento aplicado:</td>
    <td class="col-md-1"><select name="descuento_aplicado" class="form-control">
                                <option value="0">Sin descuento</option>
                                <option value="5"<?php  if ($descuento_anterior==10) { print " selected"; } ?>>Descuento de hermano (5 %)</option>
                                <option value="10"<?php  if ($descuento_anterior==10) { print " selected"; } ?>>Descuento de antiguo alumno (10 %)</option>
                        </select></td>
    </tr>
    </table>
        <button type="button" onclick="window.close();" class="btn btn-danger">Cancelar</button> <button type="submit" class="btn btn-success">Aceptar</button>
  </div>
</div>

</div>
</form>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

function seleccionar_fecha(nombre_campo_presel) {
                // Comprueba fecha

                mes_presel=eval("self.document.servicios."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document.servicios."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document.servicios."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino=servicios&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }

</script>
</body>
</html>