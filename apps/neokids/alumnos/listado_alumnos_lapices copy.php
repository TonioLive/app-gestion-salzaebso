<?php  require("../configuracion/parte_superior_privado_ancho.php");

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM gestionguarderias.cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error());

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM gestionguarderias.cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }

// Niveles piscina

$nombres_niveles=array("Bebes","Nivel 1","Nivel 2","Nivel 3");

// Nombres aulas
$nombres_aulas=array();

$busca_nombres_aulas=db_query("SELECT * FROM gestionguarderias.centro_aulas;");
while($lista_nombres_aulas=db_fetch_array($busca_nombres_aulas)) {
$nombres_aulas[$lista_nombres_aulas["id_aula"]]=$lista_nombres_aulas["nombre"];
}
?>
<div align="center"><center>
<br><br>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM gestionguarderias.cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php  if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="95%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de Alumnos de Lápices que asisten a la piscina</b></small></td>
  </tr>
<?php 
$busca_cotactos=db_query_params('SELECT * FROM gestionguarderias.usuarios,gestionguarderias.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=? GROUP BY usuarios.id_usuario ORDER BY alumnos.id_centro, alumnos.nivel_actual, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 's', array($id_curso_actual));

if (db_num_rows($busca_cotactos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <?php 
        $proceso_actual="";
        $curso_actual="";
        $centro_actual_contacto="";
        
        while($listado_alumnos=db_fetch_array($busca_cotactos)){
        
        // Busca grupos natacion
        $grupos_natacion="";
        
        $busca_grupos=db_query_params('SELECT * FROM aulas_alumnos_lapices_grupos LEFT JOIN aulas_grupos ON aulas_alumnos_lapices_grupos.id_grupo=aulas_grupos.id_grupo WHERE aulas_alumnos_lapices_grupos.id_alumno=?', 's', array($listado_alumnos["id_alumno"]));
        
        while($grupos=db_fetch_array($busca_grupos)) {
        	 $nombre_horario_mostrado=$dia_sem_largo[$grupos["dia"]]." de ".date("H:i",strtotime($grupos["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo["hora_fin"]));
        	$grupos_natacion.=$nombre_horario_mostrado.", ";
        	}

        if ($grupos_natacion!="") {
        
        $grupos_natacion=substr($grupos_natacion,0,strlen($grupos_natacion)-2);
        
        if ($centro_actual_contacto!=$listado_alumnos["id_centro"]) {
        $centro_actual_contacto=$listado_alumnos["id_centro"];
        if ($centro_actual_contacto==0) {
                $nombre_centro_actual="Sin escuela seleccionada";
                } else {
                // Busca datos del centro
                $busca_centro=db_query_params('SELECT * FROM gestionguarderias.centros WHERE id_centro=?', 's', array($listado_alumnos["id_centro"]));
                if (db_num_rows($busca_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_centro);
                        $nombre_centro_actual=$datos_centro["nombre_centro"];
                        }
                }
        ?>
        <tr bgcolor="#efefef">
        <td align="left" colspan=10><font size=3 face="<?= $fuente_general; ?>"><b>Escuela: <?=$nombre_centro_actual?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre alumno</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nacimiento (Edad)</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Aula Lápices</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Grupos Piscina</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nivel natación</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fechas Inicio y Fin</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>E-mail</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>Estado actual</b></font></td>
        </tr>        
        <?php 
        }
         
        $color_fuente_linea="#000000";
        
        if (($listado_alumnos["fecha_nacimiento"]!="0000-00-00") and (strlen($listado_alumnos["fecha_nacimiento"])!=0)) {
        	$fecha_nacimiento_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"])); 
        	}else {
        	$fecha_nacimiento_alumno="-";
        	} 
        
        $fechas_inicio_fin=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"])) . " <br>" . date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));;
        
        if ($listado_alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        }
        
        $estado_actual="Activo";
        
        if (strtotime($listado_alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
        
        	$color_fuente_linea="#ff0000";
        	$estado_actual="Baja";
        
        	}
        if (strtotime($listado_alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
        
        	$color_fuente_linea="#0000ff";
        	$estado_actual="Próximo inicio";
        
        	}
        ?>
        <tr>
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nac_alumno;?> (<?=$edad_alumno?>)</font></td>      
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$nombres_aulas[$listado_alumnos["aula"]]?></font></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$grupos_natacion?></font></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$nombres_niveles[$listado_alumnos["nivel_actual"]]?></font></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fechas_inicio_fin;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Poblacion"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono_contacto1"]?> <?=$listado_alumnos["telefono_contacto2"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["email"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$estado_actual?></font></td>
        </tr>
        <?php 
        	}
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>No se han encontrado alumnos.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
                        <?php  if ($idUsuario<>6297) { ?>
<br>
<input type="button" onclick="window.location.href='informe_contactos.php'" value="Ver informe" style="font-size: 10px"> <input type="button" onclick="window.location.href='nuevo_contacto.php'" value="Nuevo contacto" style="font-size: 10px"> <input type="button" onclick="window.location.href='/index.php'" value="Salir" style="font-size: 10px">
<br><?php  } ?>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

