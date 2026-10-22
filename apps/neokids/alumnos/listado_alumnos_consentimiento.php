<?php  require("../configuracion/parte_superior_privado_ancho.php");

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error());

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$curso_seleccionado;
        $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }
$anio_control=date("Y",strtotime($fecha_inicio_curso))-3;

$contador=0;

// Nombres escuelas
$nombres_escuelas=array();

$busca_nombres_centros=db_query("SELECT * FROM gestionguarderias.centros;");
while($lista_nombres_centros=db_fetch_array($busca_nombres_centros)) {
$nombres_centros[$lista_nombres_centros["id_centro"]]=$lista_nombres_centros["nombre_centro"];
}

?>
<div align="center"><center>
<br><br>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php  if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<?php 

// Busca numero de niños que acaban en Lapices y no siguen
$busca_salidas=db_query("SELECT * FROM gestionguarderias.usuarios,gestionguarderias.alumnos,gestionguarderias.cursos_academicos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=cursos_academicos.id_curso AND year(cursos_academicos.fecha_fin)=".date("Y",strtotime($fecha_inicio_curso))." AND year(fecha_nacimiento)=$anio_control AND (alumnos.id_centro=6 OR alumnos.id_centro=7 OR alumnos.id_centro=8);");

$cuenta_siguen=array();
$cuenta_siguen_total=0;
$cuenta_se_quedan=0;

$ya_estan=array();

while($lista_lapcies=db_fetch_array($busca_salidas)) {
        	
        		$escuela=$lista_lapcies["id_centro"];
        	
        		$comprueba_si_siguen=db_query_params('SELECT * FROM gestionguarderias.alumnos,gestionguarderias.cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_usuario=? AND cursos_academicos.fecha_fin>=?', 'ss', array($lista_lapcies["id_usuario"], (string)($fecha_inicio_curso))) or die(db_error());
        		
        		if (db_num_rows($comprueba_si_siguen)==0) {
        			if (in_array($lista_lapcies["id_usuario"],$ya_estan)==false) {
        				$cuenta_siguen[$escuela]++;
        				$cuenta_siguen_total++;
        				$ya_estan[]=$lista_lapcies["id_usuario"];
	        		}        			
        		} else {
        			$cuenta_se_quedan++;
        		}        	
        	}
        	

$busca_cotactos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND year(fecha_nacimiento)=$anio_control AND alumnos.id_curso=$id_curso_actual GROUP BY usuarios.id_usuario ORDER BY alumnos.id_centro, alumnos.nivel_actual, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");

if (db_num_rows($busca_cotactos)!=0) {

?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="95%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de Alumnos de Lápices nacidos el año <?=$anio_control?> que vienen a Neokids, de un total de <?=$cuenta_siguen_total?> niños que terminan en Lápices.</b></small></td>
  </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nº</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre alumno</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nacimiento (Edad)</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Escuela Lápices</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Grupos Piscina</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fechas Inicio y Fin</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>E-mail</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>Estado actual</b></font></td>
        </tr>              
        
        <?php 
        $proceso_actual="";
        $curso_actual="";
        $centro_actual_contacto="";
        
        while($listado_alumnos=db_fetch_array($busca_cotactos)){
        
        // Comprueba si han sido alumnos de lápices en cursos anteriores
        $busca_alumnos_lapices=db_query("SELECT * FROM gestionguarderias.usuarios,gestionguarderias.alumnos,gestionguarderias.cursos_academicos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=cursos_academicos.id_curso AND cursos_academicos.fecha_fin<'".$fecha_inicio_curso."' AND year(fecha_nacimiento)=$anio_control AND usuarios.Apellido1='".$listado_alumnos["Apellido1"]."' AND usuarios.Apellido2='".$listado_alumnos["Apellido2"]."' AND usuarios.Nombre='".$listado_alumnos["Nombre"]."'");       
        
        if (db_num_rows($busca_alumnos_lapices)!=0) {
        
        	// Comprueba si siguen en lapices el año que viene
        	$sigue=false;
        	$escuela=0;
        	while($lista_lapcies=db_fetch_array($busca_alumnos_lapices)) {
        	
        		$escuela=$lista_lapcies["id_centro"];

        		$comprueba_si_siguen=db_query_params('SELECT * FROM gestionguarderias.alumnos,gestionguarderias.cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND alumnos.id_usuario=? AND cursos_academicos.fecha_fin>=?', 'ss', array($lista_lapcies["id_usuario"], (string)($fecha_inicio_curso)));
        		
        		if (db_num_rows($comprueba_si_siguen)!=0) {
        			$sigue=true;
        		}
        	
        	}
        	
        if ($sigue==false) {	
        
        $contador++;
        
        // Busca grupos natacion
        $grupos_natacion="";
        
        $busca_grupos=db_query_params('SELECT * FROM aulas_alumnos_grupos LEFT JOIN aulas_grupos ON aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo WHERE aulas_alumnos_grupos.id_alumno=?', 's', array($listado_alumnos["id_alumno"]));
        
        while($grupos=db_fetch_array($busca_grupos)) {
        	 $nombre_horario_mostrado=$dia_sem_largo[$grupos["dia"]]." de ".date("H:i",strtotime($grupos["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo["hora_fin"]));
        	$grupos_natacion.=$nombre_horario_mostrado.", ";
        	}

        if ($grupos_natacion!="") {
        
        $grupos_natacion=substr($grupos_natacion,0,strlen($grupos_natacion)-2);       
        
         
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
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$contador?></font></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nac_alumno;?> (<?=$edad_alumno?>)</font></td> 
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$nombres_centros[$escuela]; ?></font></font></td> 
           
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$grupos_natacion?></font></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fechas_inicio_fin;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Poblacion"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono_contacto1"]?> <?=$listado_alumnos["telefono_contacto2"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["email"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$estado_actual?></font></td>
        </tr>
        <?php 
        			}
        		}
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

