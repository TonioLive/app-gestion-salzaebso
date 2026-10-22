<?php  require("../configuracion/parte_superior_privado_ancho.php");

$fecha_contacto=$_GET["fecha"];

if (strlen($fecha_contacto)==0) {

	$fecha_contacto=time();

}

// Inicio y fin de semana
$diasemana=date("w",$fecha_contacto);
$dia_inicio_semana=$fecha_contacto-(($diasemana-1)*86400);
$dia_fin_semana=$dia_inicio_semana+(86400*7);


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
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

?>
<SCRIPT LANGUAGE=javascript>
<!--

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
function eliminar_contacto(id) {
        <?php  if ($idUsuario<>6297) { ?>
        if (confirm("Se va ha eliminar el contacto seleccionado.\n\n¿Estas seguro?")) {
                window.location.href='eliminar_contacto.php?id_contacto='+id;
                }
        <?php 
        } else {
        ?>
        alert("Modo solo lectura");
        <?php 
        }
        ?>
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
        
function enviar_mensaje(id_contacto) {

	if (confirm("Se va a enviar un mensaje al contacto para que complete el formulario... ¿Estás seguro/a?")) {
		self.location.href='/mailer/enviar_codigo.php?id_contacto='+id_contacto;
		}

}
//-->
</SCRIPT>
<div align="center"><center>
<br><br>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="95%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan="2"><small><b>Listado de nuevos recibidos desde el formulario durante la semana del día <?=date("d/m/Y",$dia_fin_semana)?> al día <?=date("d/m/Y",$dia_fin_semana)?></b></small></td>
  </tr>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td><a class="btn btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$dia_inicio_semana-(7*86400);?>&curso_seleccionado=<?=$curso_seleccionado?>"><i class="fa fa-angle-left"></i> Semana Anterior</a></td>
    <td class="text-right"><a class="btn btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=$dia_fin_semana+86400;?>&curso_seleccionado=<?=$curso_seleccionado?>">Semana Siguiente <i class="fa fa-angle-right"></i></a></td>
  </tr>  
<?php 
$busca_cotactos=db_query("SELECT * FROM contactos WHERE origen=1 AND fecha_contacto>='".date("Y-m-d",$dia_inicio_semana)."' AND fecha_contacto<='".date("Y-m-d",$dia_fin_semana)."'$filtro ORDER BY id_centro DESC,id_curso,proceso_decision, fecha_contacto, Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_cotactos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan="2">
        <table width="100%" class="table table-condensed table-hover">
        <tr>
        <td colspan=11><font size=2 face="<?= $fuente_general; ?>">Para editar un contacto, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)</font></td>
        </tr>
        <?php 
        $proceso_actual="";
        $curso_actual="";
        $centro_actual_contacto="";
        
        while($listado_alumnos=db_fetch_array($busca_cotactos)){
        
        if ($centro_actual_contacto!=$listado_alumnos["id_centro"]) {
        $centro_actual_contacto=$listado_alumnos["id_centro"];
        if ($centro_actual_contacto==0) {
                $nombre_centro_actual="Sin escuela seleccionada";
                } else {
                // Busca datos del centro
                $busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($listado_alumnos["id_centro"]));
                if (db_num_rows($busca_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_centro);
                        $nombre_centro_actual=$datos_centro["nombre_centro"];
                        }
                }
        ?>
        <tr bgcolor="#efefef">
        <td align="left" colspan=11><font size=3 face="<?= $fuente_general; ?>"><b>Escuela: <?=$nombre_centro_actual?></b></font></td>
        </tr>
        <?php 
        }
        
        if ($curso_actual!=$listado_alumnos["id_curso"]) {
                        // Datos del curso
                        if ($listado_alumnos["id_curso"]==0) {
                        $nombre_curso="Sin curso definido";
                        } else {
                        $busca_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($listado_alumnos["id_curso"]));
                        if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $nombre_curso=$datos_curso["nombre"];
                                }
                        }
                        $numero_color_curso=intval(substr($listado_alumnos["id_curso"],strlen($listado_alumnos["id_curso"])-1,1));
                        ?>
                        <tr valign="top" bgcolor="<?=$colores[$numero_color_curso]?>">
                                <td align="left" colspan=11><font size=3 face="<?= $fuente_general; ?>"><b>Para el Curso: <?=$nombre_curso;?></b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$listado_alumnos["id_curso"];
                        }
        
        $proceso=$listado_alumnos["proceso_decision"];
        if ($proceso!=$proceso_actual) {
        $proceso_actual=$proceso;
        ?>
        <tr bgcolor="#efefef">
        <td align="left" colspan=11><font size=2 face="<?= $fuente_general; ?>"><b>Contactos en estado <?=$proceso_actual?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre contacto</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nacimiento</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Personas  contacto</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>DNI contactos</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha contacto</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>E-mail</b></font></td>
        <td align="center" width=7%><font size=2 face="<?= $fuente_general; ?>"><b>Datos alumno</b></font></td>
        <td align="center" width=7%><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center" width=7%><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        }
        $color_fuente_linea="#000000";
        
        if (($listado_alumnos["fecha_nacimiento"]!="0000-00-00") and (strlen($listado_alumnos["fecha_nacimiento"])!=0)) {
        	$fecha_nacimiento_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"])); 
        	}else {
        	$fecha_nacimiento_alumno="-";
        	} 
        
        $fecha_contacto="";	
        if ((strlen($listado_alumnos["fecha_informacion"])!=0) and ($listado_alumnos["fecha_informacion"]!='0000-00-00')) {
        
        	$fecha_contacto=date("d/m/Y",strtotime($listado_alumnos["fecha_informacion"]));
        	
        	} 
        
        $mensaje_codigos_uso_unico="";
        
        if (strlen($listado_alumnos["codigo_uso_unico"])!=0) {
        	$mensaje_codigos_uso_unico="Invitación a rellenar formulario pendiente.";
        	if (strlen($listado_alumnos["fecha_envio_codigo"])!=0) {
        		$mensaje_codigos_uso_unico.=" Enviada el día ".date("d/m/Y",strtotime($listado_alumnos["fecha_envio_codigo"]));
        		}
        	} else {
        	if (strlen($listado_alumnos["fecha_uso_codigo"])!=0) {
        		$mensaje_codigos_uso_unico.="Formulario rellenado por invitación el día ".date("d/m/Y",strtotime($listado_alumnos["fecha_uso_codigo"]));
        		}
        	
        	}
        
        // Datos alumno asociado
        $datos_alumno_asociado="";
        
        if ($listado_alumnos["id_usuario"]!=0) {
        
        	$busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($listado_alumnos["id_usuario"]));
        	
        	if (db_num_rows($busca_datos_usuario)!=0) {
        	
        		$datos_usuario=db_fetch_array($busca_datos_usuario);
        	
        		$datos_alumno_asociado.="<a target='_blank' href='/alumnos/nuevo_alumno1.php?u=".$datos_usuario["id_usuario"]."'>".$datos_usuario["Nombre"]." ".$datos_usuario["Apellido1"]." ".$datos_usuario["Apellido2"]."</a><br />";
        		
        		$fecha_nac="Fecha de nacimiento:".date("d/m/Y",strtotime($datos_usuario["fecha_nacimiento"]));
        		
        		if ($listado_alumnos["fecha_nacimiento"]!=$datos_usuario["fecha_nacimiento"]) {
        		
        			$fecha_nac="<span class='text-danger'>".$fecha_nac."</span>";
        			
        			}
        	
        		$datos_alumno_asociado.=$fecha_nac."<br />";
        		
        		// Busca datos padres
        		$busca_datos_padres=db_query_params('SELECT * FROM padres LEFT JOIN usuarios ON padres.id_usuario=usuarios.id_usuario WHERE padres.padre_de=?', 's', array($listado_alumnos["id_usuario"]));
        	
        		$num_contacto=1;
        	
        		while($datos_padres=db_fetch_array($busca_datos_padres)) {
        				$datos_alumno_asociado.="Contacto Nº ".$num_contacto.": ".$datos_padres["Nombre"]." ".$datos_padres["Apellido1"]." ".$datos_padres["Apellido2"].", DNI: ".$datos_padres["DNI"].", Email: ".$datos_padres["email"].", Teléfono: ".$datos_padres["telefono1"]."<br />";
        				$num_contacto++;
        			}
        	
        		}
        
        		
        
        }
        
        ?>
        <tr>
        <?php 
        $color_celda="";        
        if (strlen($listado_alumnos["Apellido1"].$listado_alumnos["Apellido2"].$listado_alumnos["Nombre"])==0) {
        	$color_celda=" class='danger'";	
        }
        ?>
        <td<?=$color_celda?>><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></font></td>
        <?php 
        $color_celda="";        
        if ($fecha_nacimiento_alumno=="-") {
        	$color_celda=" class='danger'";	
        }
        ?>        
        <td align="center"<?=$color_celda?>><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nacimiento_alumno;?></font></td>
        <?php 
        $color_celda="";
        if ((strlen($listado_alumnos["persona_contacto"])<2) and (strlen($listado_alumnos["nombre_padre2"])<2)) {
        	$color_celda=" class='danger'";	
        }
        ?> 
        <td<?=$color_celda?>><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["persona_contacto"]?><?php  if (strlen($listado_alumnos["nombre_padre2"])!=0) { echo("<br />".$listado_alumnos["nombre_padre2"]); } ?></font></font></td>
        <?php 
        $color_celda="";
        if ((strlen($listado_alumnos["dni_padre1"])<2) and (strlen($listado_alumnos["dni_padre2"])<2)) {
        	$color_celda=" class='danger'";	
        }
        ?>         
        <td<?=$color_celda?>><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["dni_padre1"]?><?php  if (strlen($listado_alumnos["dni_padre2"])!=0) { echo("<br />".$listado_alumnos["dni_padre2"]); } ?></font></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_contacto;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["poblacion"]?></font></td>
        <?php 
        $color_celda="";
        if ((strlen($listado_alumnos["telefono"])<2) and (strlen($listado_alumnos["telefono_padre2"])<2)) {
        	$color_celda=" class='danger'";	
        }
        ?>        
        <td<?=$color_celda?>><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono"]?><?php  if (strlen($listado_alumnos["telefono_padre2"])!=0) { echo("<br />".$listado_alumnos["telefono_padre2"]); } ?></font></td>
        <?php 
        $color_celda="";
        if ((strlen($listado_alumnos["email"])<2) and (strlen($listado_alumnos["email_padre2"])<2)) {
        	$color_celda=" class='danger'";	
        }
        ?>  
        <td<?=$color_celda?>><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["email"]?><?php  if (strlen($listado_alumnos["email_padre2"])!=0) { echo("<br />".$listado_alumnos["email_padre2"]); } ?></font></td>
        <?php 
        $color_celda="";
        if ($datos_alumno_asociado!="") {
        	$color_celda=" class='success'";	
        }
        ?>         
        <td<?=$color_celda?>><small><small><?=$datos_alumno_asociado?></small></small></td>        
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$mensaje_codigos_uso_unico?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="nuevo_contacto.php?id_contacto=<?=$listado_alumnos["id_contacto"];?>" title="Editar contacto"><img src="modificar.gif" border=0></a> <a href="javascript:eliminar_contacto(<?=$listado_alumnos["id_contacto"];?>);" title="Eliminar contacto"><img src="borrar.gif" border=0></a><br /><a href="javascript:enviar_mensaje(<?=$listado_alumnos["id_contacto"];?>)" title="Enviar mensaje para que complete el registro"><img src="/imagen/mensajes.gif" border=0></a> <a target="_blank" href="/mailer/generar_qr.php?id_contacto=<?=$listado_alumnos["id_contacto"]?>"><img src="/imagen/qr.png"></a></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center" colspan="2"><br><br><small><small><b>Esta semana no se ha introducido ningún contacto.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>                        
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>