<?php 
// Busca mensajes pendientes en bandeja de entrada

if ($mostrar_alertas_mensajes==true) {

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d")."' ORDER BY fecha_inicio");

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        } else {
                        $id_curso_actual=0;
                        }
                }

$busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND alumnos.id_curso=$id_curso_actual ORDER BY Apellido1, Apellido2, Nombre");

$consulta_mensajes="";

while($alumnos=db_fetch_array($busca_alumnos)) {
        $consulta_mensajes.=" OR agenda_notas_padres.id_alumno=".$alumnos["id_usuario"];
        }
$hay_mensajes_no_leidos=false;
if ($consulta_mensajes!="") {

        $consulta_mensajes="(".substr($consulta_mensajes,4,strlen($consulta_mensajes)-4).")";
        $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE $consulta_mensajes AND tipo_nota<>2 AND estado=0 AND destino=1 ORDER BY fecha DESC"); 
    if (db_num_rows($busca_no_leidos)!=0) {
                $hay_mensajes_no_leidos=true;
            } else {
            // Busca notas no leidas
            $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres,agenda_notas_padres_respuestas WHERE agenda_notas_padres.id_nota_padre=agenda_notas_padres_respuestas.id_nota AND $consulta_mensajes AND agenda_notas_padres_respuestas.leida=0 AND agenda_notas_padres_respuestas.direccion=0 AND agenda_notas_padres.destino=1 ORDER BY fecha DESC") or die(db_error());
            if (db_num_rows($busca_no_leidos)!=0) {
                        $hay_mensajes_no_leidos=true;
                        }
            }
        }
        
if ($hay_mensajes_no_leidos==true) {
?>
<table width=100% border=0 cellpadding=1 cellspacing="0"><tr bgcolor="#efefef"><td><a href="/gestion/mensajes/listado_mensajes_nuevos.php" title="Ver mensajes nuevos"><font color="red" size=3 face="Arial"><b><img src="/imagen/mensajes_act.gif" border=0> Hay mensajes nuevos para la coordinadora</b></font></a></td></tr></table>
<?php 
}

}
?>
