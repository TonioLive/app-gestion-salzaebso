<?php 
$id_centro=$centro_actual;
if (strlen($id_centro)!=0) {
        
                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro));
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);
                        
                        $db_nombre_centro=$datos_centro["nombre_centro"];

                        }
        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }
?>
<script language="Javascript">
function ver_ficha_alumno(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
function poner_alertas() {
        document.getElementById('ocultar_alertas').style.display='none';
        document.getElementById('mostrar_alertas').style.display='block';
        }

function quitar_alertas() {
        document.getElementById('mostrar_alertas').style.display='none';
        document.getElementById('ocultar_alertas').style.display='block';
        }

</script>
<div id="mostrar_alertas" style="display:none;padding:0px;border:0px solid">
<table width="85%" border=1 cellpadding=0 cellspacing=0 bordercolor="#000000" style="border-collapse: collapse">
        <tr>
                <td width="100%" valign="top" bgcolor="#FFFFFF" align="left">
                <font size=2 face="<?= $fuente_general; ?>"><img src="/imagen/alerta.gif" border=0 align=absmiddle> <b>Alertas del Centro <i><?=$db_nombre_centro?></i></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:quitar_alertas();">Clic aquí para ocultar alertas</a></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                         <?php 
                         $alertas=0;
                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro$filtro ORDER BY id_curso,nombre;") or die(db_error());
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($aulas["id_curso"]));
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                }

                        // Analiza el aula
                        // Calcula ocupación actual
                        $consulta_ocupacion=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=?', 's', array($aulas["id_aula"])) or die(db_error()."-1");
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);

                        $ocupacion_max=$aulas["capacidad"];
                        $ocupacion_min=$aulas["ocupacion_minima"];
                        $color_ocupacion="#000000";
                        if ($ocupacion_actual>$ocupacion_max) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="<br>Se ha superado la capacidad del aula.";
                                }
                        if ($ocupacion_actual<$ocupacion_min) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="<br>No se alcanza la ocupación mínima.";
                                }

                        // Busca alertas por alumnos

                        if ($ocupacion_actual>0) {
                                while($listado_alumnos_aula=db_fetch_array($consulta_ocupacion)) {
                                if ($listado_alumnos_aula["sexo"]=="V") {
                                        $genero_alumno1="El niño";
                                        $genero_alumno2="del niño";
                                        } else {
                                        $genero_alumno1="La niña";
                                        $genero_alumno2="de la niña";
                                        }
                                        
                                $fecha_baja=$listado_alumnos_aula["fecha_baja"];

                                if ($fecha_baja=="0000-00-00") {
                                        $fecha_baja=$datos_curso["fecha_fin"];
                                        }
                                if ($fecha_baja<=date("Y-m-d")) {
                                        // El Alumno ya ha finalizado
                                        $color_fuente_linea="#ffacac";
                                        
                                        // El alumno finaliza hoy
                                        if ($fecha_baja==date("Y-m-d",strtotime(date("Y-m-d")))) {
                                                $alertas++;
                                                ?>
                                                <tr valign="top">
                                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=security_escape($aulas["nombre"]);?></font></td>
                                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$genero_alumno1?> <a href="javascript:ver_ficha_alumno(<?=$listado_alumnos_aula["id_usuario"];?>)" title="Ver ficha del alumno"><b><?=security_escape($listado_alumnos_aula["Nombre"])?> <?=security_escape($listado_alumnos_aula["Apellido1"])?> <?=security_escape($listado_alumnos["Apellido2"])?></b></a> termina hoy. El próximo día no asistirá al centro.</font></td>
                                                </tr>
                                                <?php 
                                                }
                                                
                                        // El alumno finalizó ayer
                                        if ($fecha_baja==date("Y-m-d",strtotime(date("Y-m-d"))-86400)) {
                                                $alertas++;
                                                ?>
                                                <tr valign="top">
                                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=security_escape($aulas["nombre"]);?></font></td>
                                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$genero_alumno1?> <a href="javascript:ver_ficha_alumno(<?=$listado_alumnos_aula["id_usuario"];?>)" title="Ver ficha del alumno"><b><?=security_escape($listado_alumnos_aula["Nombre"])?> <?=security_escape($listado_alumnos_aula["Apellido1"])?> <?=security_escape($listado_alumnos["Apellido2"])?></b></a> terminó ayer. Hoy ya no asiste al centro.</font></td>
                                                </tr>
                                                <?php 
                                                }
                                        // El alumno finalizó el viernes
                                        if (($fecha_baja==date("Y-m-d",strtotime(date("Y-m-d"))-256200)) and (date("w")==1)) {
                                                $alertas++;
                                                ?>
                                                <tr valign="top">
                                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=security_escape($aulas["nombre"]);?></font></td>
                                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$genero_alumno1?> <a href="javascript:ver_ficha_alumno(<?=$listado_alumnos_aula["id_usuario"];?>)" title="Ver ficha del alumno"><b><?=security_escape($listado_alumnos_aula["Nombre"])?> <?=security_escape($listado_alumnos_aula["Apellido1"])?> <?=security_escape($listado_alumnos["Apellido2"])?></b></a> terminó el viernes pasado. Hoy ya no asiste al centro.</font></td>
                                                </tr>
                                                <?php 
                                                }

                                        } else {
                                        if ($listado_alumnos_aula["fecha_matricula"]>date("Y-m-d")) {
                                                // El Alumno todavia no ha empezado
                                                } else {
                                                if ($listado_alumnos_aula["fecha_matricula"]==date("Y-m-d")) {
                                                        // El Alumno empieza hoy
                                                        $alertas++;
                                                        ?>
                                                        <tr valign="top">
                                                        <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=security_escape($aulas["nombre"]);?></font></td>
                                                        <td><font size=1 face="<?= $fuente_general; ?>">Hoy es el primer día de asistencia <?=$genero_alumno2?> <a href="javascript:ver_ficha_alumno(<?=$listado_alumnos_aula["id_usuario"];?>)" title="Ver ficha del alumno"><b><?=security_escape($listado_alumnos_aula["Nombre"])?> <?=security_escape($listado_alumnos_aula["Apellido1"])?> <?=security_escape($listado_alumnos_aula["Apellido2"])?></b></a>.</font></td>
                                                        </tr>
                                                        <?php 
                                                        } else {
                                                        // El alumno debe asistir normalmente
                                                        // Busca ultimo día de asistencia
                                                        $consulta_asistencia_alumno=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? ORDER BY fecha DESC;', 's', array($listado_alumnos_aula["id_usuario"]));
                                                        $nunca=false;
                                                        if (db_num_rows($consulta_asistencia_alumno)!=0) {
                                                                $asistencia_alumno=db_fetch_array($consulta_asistencia_alumno);
                                                                $dias_sin_asistir=strtotime(date("Y-m-d"))-strtotime($asistencia_alumno["fecha"]);
                                                                $dias_sin_asistir=round(($dias_sin_asistir/86400),0);
                                                                } else {
                                                                $nunca=true;
                                                                $dias_sin_asistir=strtotime(date("Y-m-d"))-strtotime($listado_alumnos_aula["fecha_matricula"]);
                                                                $dias_sin_asistir=round(($dias_sin_asistir/86400),0);
                                                                }

                                                        // Comprueba si es lunes
                                                        $limite_dias=3;
                                                        if (date("w")==1) {
                                                                $limite_dias=5;
                                                                }
                                                        if (date("w")==0) {
                                                                $limite_dias=4;
                                                                }
                                                        if ($dias_sin_asistir>=$limite_dias) {
                                                                if ($nunca==true) {
                                                                // El alumno no ha asistido nunca y ya falta más de 3 días
                                                                $alertas++;
                                                                ?>
                                                                <tr valign="top">
                                                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=security_escape($aulas["nombre"]);?></font></td>
                                                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$genero_alumno1?> <a href="javascript:ver_ficha_alumno(<?=$listado_alumnos_aula["id_usuario"];?>)" title="Ver ficha del alumno"><b><?=security_escape($listado_alumnos_aula["Nombre"])?> <?=security_escape($listado_alumnos_aula["Apellido1"])?> <?=security_escape($listado_alumnos["Apellido2"])?></b></a> debía incorporase el <?=date("d/m/Y",strtotime($listado_alumnos_aula["fecha_matricula"]));?> y lleva un retraso de <?=$dias_sin_asistir;?> días.</font></td>
                                                                </tr>
                                                                <?php 
                                                                } else {
                                                                // El alumno falta más de 3 días
                                                                $alertas++;
                                                                ?>
                                                                <tr valign="top">
                                                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=security_escape($aulas["nombre"]);?></font></td>
                                                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$genero_alumno1?> <a href="javascript:ver_ficha_alumno(<?=$listado_alumnos_aula["id_usuario"];?>)" title="Ver ficha del alumno"><b><?=security_escape($listado_alumnos_aula["Nombre"])?> <?=security_escape($listado_alumnos_aula["Apellido1"])?> <?=security_escape($listado_alumnos["Apellido2"])?></b></a> no asiste desde el día <?=date("d/m/Y",strtotime($asistencia_alumno["fecha"]));?>, hace <?=$dias_sin_asistir;?> días que no viene a clase.</font></td>
                                                                </tr>
                                                                <?php 
                                                                }
                                                                }
                                                        // Compara con calendario docente
                                                        
                                                        }
                                                }
                                        }
                                }
                        }
                }
                
                // Comprueba alertas de contactos
                $filtro_fechas="fecha_contacto='".date("Y-m-d",(strtotime(date("Y-m-d"))-(30*86400)))."'";
                if (date("w")==1) {
                                  $filtro_fechas=$filtro_fechas." OR fecha_contacto='".date("Y-m-d",(strtotime(date("Y-m-d"))-(31*86400)))."'  OR fecha_contacto='".date("Y-m-d",(strtotime(date("Y-m-d"))-(32*86400)))."'";
                                  }

                $busca_contactos_30_dias=db_query("SELECT * FROM contactos WHERE $filtro_fechas AND (id_centro=$centro_actual OR id_centro=0)");
                
                while($contactos_30_dias=db_fetch_array($busca_contactos_30_dias)) {
                        $alertas++;
                        $dias_contacto=round(((strtotime(date("Y-m-d"))-strtotime($contactos_30_dias["fecha_contacto"]))/86400),0);
                        ?>
                        <tr valign="top">
                        <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>">Contactos</font></td>
                        <td><font size=1 face="<?= $fuente_general; ?>">Hace <?=$dias_contacto?> (<?=date("d/m/Y",strtotime($contactos_30_dias["fecha_contacto"]));?>) que se realizó el primer contacto con <b><?=security_escape($contactos_30_dias["Nombre"])?> <?=security_escape($contactos_30_dias["Apellido1"])?> <?=security_escape($contactos_30_dias["Apellido2"])?></b>. Persona de contacto: <?=$contactos_30_dias["persona_contacto"];?></font></td>
                        </tr>
                        <?php 
                        }
                
                if ($alertas==0) {
                ?>
                <tr valign="top">
                        <td align="center" bgcolor="#ffffff" colspan=2><font size=1 face="<?= $fuente_general; ?>">Hoy no hay alertas.</font></td>
                </tr>
                <?php 
                }
                ?>
                </table>
                </td>
        </tr>
</table><br></div>
<div id="ocultar_alertas" style="display:block;padding:0px;border:0px solid">
<table width="85%" border=1 cellpadding=0 cellspacing=0 bordercolor="#000000" style="border-collapse: collapse">
        <tr>
                <td width="100%" valign="top" bgcolor="#FFFFFF" align="left">
                <font size=2 face="<?= $fuente_general; ?>"><img src="/imagen/alerta.gif" border=0 align=absmiddle> <b>Alertas del Centro <i><?=$db_nombre_centro?></i></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:poner_alertas();">Clic aquí para mostrar alertas</a></font>
                </td>
                
        </tr>
</table><br>
</div>
<?php 
}
?>
