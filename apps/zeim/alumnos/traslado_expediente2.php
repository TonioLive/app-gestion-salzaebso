<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Datos cambio
$id_nueva_aula=$_POST["id_nueva_aula"];

// Datos aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_nueva_aula)) or die(db_error()."-2");
$datos_aula=db_fetch_array($consulta_datos_aula);

$id_nuevo_centro=$datos_aula["id_centro"];

$usuario_fechatras_dia=$_POST["usuario_fechatras_dia"];
$usuario_fechatras_mes=$_POST["usuario_fechatras_mes"];
$usuario_fechatras_ano=$_POST["usuario_fechatras_ano"];
$usuario_fechatras=$usuario_fechatras_ano."-".$usuario_fechatras_mes."-".$usuario_fechatras_dia;

// Recibimos los datos
$id_alumno=$_POST["id_alumno"];

// Datos del alumno
$busca_datos_del_alumno=db_query_params('SELECT * FROM alumnos WHERE id_alumno=?', 's', array($id_alumno));
$datos_del_alumno=db_fetch_array($busca_datos_del_alumno);

$consulta_inscripcion=db_query_params('SELECT * FROM alumnos, cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_alumno=?', 's', array($id_alumno)) or die(db_error()."-2");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);

$usuario_curso=$datos_inscripcion["id_curso"];

$usuario_fechains=$datos_inscripcion["fecha_inscripcion"];

$usuario_fechamat=$datos_inscripcion["fecha_matricula"];

$usuario_fechabaja=$datos_inscripcion["fecha_baja"];

$id_curso_traslado=$datos_inscripcion["id_curso"];
$motivo_baja=$datos_inscripcion["motivo_baja"];
$proceso_decision=$datos_inscripcion["proceso_decision"];
$tipo_mensualidad=$datos_inscripcion["tipo_mensualidad"];
$comedor=$datos_inscripcion["comedor"];
$tipo_menu_comedor=$datos_inscripcion["tipo_menu_comedor"];
$numero_tomas=$datos_inscripcion["numero_tomas"];
$banco_entidad=$datos_inscripcion["banco_entidad"];
$banco_poblacion=$datos_inscripcion["banco_poblacion"];
$banco_titular=$datos_inscripcion["banco_titular"];
$banco_dni_titular=$datos_inscripcion["banco_dni_titular"];
$banco_domicilio=$datos_inscripcion["banco_domicilio"];
$banco_cuenta=$datos_inscripcion["banco_cuenta"];
$forma_de_pago=$datos_inscripcion["forma_de_pago"];

db_query_params('UPDATE alumnos SET motivo_baja=\'TRASLADO DE CENTRO\', fecha_baja=? WHERE id_alumno=?', 'ss', array((string)($usuario_fechatras), $id_alumno)) or die(db_error());

db_query_params('INSERT INTO alumnos (id_usuario, aula, aula_ayudas, id_centro, id_curso, motivo_baja, fecha_inscripcion, fecha_matricula, fecha_baja, tipo_mensualidad, comedor, tipo_menu_comedor, numero_tomas, forma_de_pago, banco_entidad, banco_poblacion, banco_titular, banco_dni_titular, banco_domicilio, banco_cuenta, proceso_decision) VALUES (?, ?, ?, ?, ?, \'\', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 'ssssssssssssssssssss', array((string)($datos_inscripcion["id_usuario"]), (string)($id_nueva_aula), (string)($id_nueva_aula), (string)($id_nuevo_centro), (string)($id_curso_traslado), (string)($usuario_fechatras), (string)($usuario_fechatras), (string)($fecha_baja), (string)($tipo_mensualidad), (string)($comedor), (string)($tipo_menu_comedor), (string)($numero_tomas), (string)($forma_de_pago), (string)($banco_entidad), (string)($banco_poblacion), (string)($banco_titular), (string)($banco_dni_titular), (string)($banco_domicilio), (string)($banco_cuenta), (string)($proceso_decision))) or die(db_error());

$id_nuevo_alumno_tras=db_insert_id();


// Copia servicios contratados

$busca_servicios_contratados=db_query_params('SELECT * FROM servicios_contratados WHERE id_alumno=? AND fecha_fin>?', 'ss', array($id_alumno, (string)($usuario_fechatras)));

while($servicios_contratados=db_fetch_array($busca_servicios_contratados)) {

        // Copia servicio en la nueva escuela
        $id_servicio=$servicios_contratados["id_servicio"];
        $fecha_inicio_servicio=$servicios_contratados["fecha_inicio"];
        $fecha_fin_servicio=$servicios_contratados["fecha_fin"];
        $periodicidad=$servicios_contratados["periodicidad"];
        $descuento=$servicios_contratados["descuento"];
        $cantidad=$servicios_contratados["cantidad"];
        $notas=$servicios_contratados["notas"];
        $no_facturar=$servicios_contratados["no_facturar"];
        if ($fecha_inicio_servicio<$usuario_fechatras) {
                $fecha_inicio_servicio=$usuario_fechatras;
                }
        $inserta=db_query("INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,fecha_fin,periodicidad,descuento,cantidad,notas,no_facturar) VALUES ('$id_nuevo_alumno_tras', '$id_servicio', '$fecha_inicio_servicio', '$fecha_fin_servicio', '$periodicidad', '$descuento', '$cantidad', '$notas', '$no_facturar);");
        if ($fecha_fin_servicio>$usuario_fechatras) {
                // ACTUALIZA FECHA FIN
                $actualiza=db_query_params('UPDATE servicios_contratados SET fecha_fin=? WHERE id_servicio_contratado=?', 'ss', array((string)($usuario_fechatras), $servicios_contratados["id_servicio_contratado"]));
                }
        }

?>
<script language="Javascript">
window.close();
self.opener.window.location.href='/alumnos/nuevo_alumno4.php?u=<?=$datos_del_alumno["id_usuario"]?>';
</script>
