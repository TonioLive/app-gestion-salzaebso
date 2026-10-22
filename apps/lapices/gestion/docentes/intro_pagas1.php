<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

if (($nivel_acceso==4) and ($ip_segura==true)) {

$id_pedido=$_POST["id_pedido"];

$fecha_pago_dia=$_POST["fecha_pago_dia"];
$fecha_pago_mes=$_POST["fecha_pago_mes"];
$fecha_pago_ano=$_POST["fecha_pago_ano"];

$fecha_pago_completa=$fecha_pago_ano."-".$fecha_pago_mes."-".$fecha_pago_dia;

  $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=$centro_actual OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1") or die(db_error());

  mt_srand((double)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  $fila=0;
  
  while($rsslc=db_fetch_array($consulta_usuarios))
  {

  $codigo=$rsslc["id_usuario"];
  
  // Comprueba si tiene contrato en vigor
  // $fecha_control=date("Y-m-d");
  // $fecha_control1=date("Y-m-d",(time()-(86400*31)));
  $fecha_control=date($fecha_pago_ano."-".$fecha_pago_mes."-".$fecha_pago_dia);
  $fecha_control1=date("Y-m-d",(strtotime($fecha_pago_ano."-".$fecha_pago_mes."-".$fecha_pago_dia)-(86400*31)));
  // $busca_contratos=mysql_query("SELECT * FROM personal_contratos WHERE id_empleado=$codigo AND ((fecha_inicio<='$fecha_control' AND fecha_fin>='$fecha_control') OR (fecha_fin>='$fecha_control1') OR (fecha_inicio<='$fecha_control' AND tipo_contrato=2 AND fecha_fin='0000-00-00')) ORDER BY fecha_inicio DESC");
  $busca_contratos = db_query("SELECT * FROM personal_contratos WHERE id_empleado=$codigo AND ((fecha_inicio<='$fecha_control' AND fecha_fin>='$fecha_control') OR (fecha_fin>='$fecha_control1') OR (fecha_inicio<='$fecha_control' AND (tipo_contrato=2 OR tipo_contrato=6) AND fecha_fin='0000-00-00')) ORDER BY fecha_inicio DESC");
  
  if (db_num_rows($busca_contratos)!=0) {

  $datos_contrato=db_fetch_array($busca_contratos);

  $id_contrato=$datos_contrato["id_contrato"];

  $id_empleado=$_POST["id_empleado_".$fila];
  $id_contrato=$_POST["id_contrato_".$fila];
  $nombre_fila=$_POST["nombre_fila_".$fila];
  $salario_base=$_POST["salario_base_".$fila];
  $prorrateo=$_POST["prorrateo_".$fila];
  $extras=$_POST["extras_".$fila];
  $irpf=$_POST["irpf_".$fila];
  $ss=$_POST["ss_".$fila];
  $boni=$_POST["boni_".$fila];

  $tp1=$_POST["tp1_".$fila];
  $tp2=$_POST["tp2_".$fila];
  $tp3=$_POST["tp3_".$fila];
  $tp4=$_POST["tp4_".$fila];
  $tp5=$_POST["tp5_$".$fila];
  $tp6=$_POST["tp6_".$fila];

   if ($salario_base<>0) {
        // Intropagos
        $intro_pagos=db_query("INSERT INTO personal_pagos (id_empleado,id_contrato,tipo_pago,importe,fecha_pago,cuenta) VALUES ('$id_empleado','$id_contrato','1','$salario_base','$fecha_pago_completa','$tp1');") or die(db_error());
        }
   if ($prorrateo<>0) {
        // Intropagos
        $intro_pagos=db_query("INSERT INTO personal_pagos (id_empleado,id_contrato,tipo_pago,importe,fecha_pago,cuenta) VALUES ('$id_empleado','$id_contrato','2','$prorrateo','$fecha_pago_completa','$tp2');") or die(db_error());
        }
   if ($extras<>0) {
        // Intropagos
        $intro_pagos=db_query("INSERT INTO personal_pagos (id_empleado,id_contrato,tipo_pago,importe,fecha_pago,cuenta) VALUES ('$id_empleado','$id_contrato','3','$extras','$fecha_pago_completa','$tp3');") or die(db_error());
        }
   if ($irpf<>0) {
        // Intropagos
        $intro_pagos=db_query("INSERT INTO personal_pagos (id_empleado,id_contrato,tipo_pago,importe,fecha_pago,cuenta) VALUES ('$id_empleado','$id_contrato','4','$irpf','$fecha_pago_completa','$tp4');") or die(db_error());
        }
   if ($ss<>0) {
        // Intropagos
        $intro_pagos=db_query("INSERT INTO personal_pagos (id_empleado,id_contrato,tipo_pago,importe,fecha_pago,cuenta) VALUES ('$id_empleado','$id_contrato','5','$ss','$fecha_pago_completa','$tp5');") or die(db_error());
        }
   if ($boni<>0) {
        // Intropagos
        $intro_pagos=db_query("INSERT INTO personal_pagos (id_empleado,id_contrato,tipo_pago,importe,fecha_pago,cuenta) VALUES ('$id_empleado','$id_contrato','6','$boni','$fecha_pago_completa','$tp6');") or die(db_error());
        }
   $fila++;
   }
}


db_close();

}

header("Location: listado_docentes.php");

?>
