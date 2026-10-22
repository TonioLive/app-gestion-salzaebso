<?php
header('Content-Type: text/html; charset=UTF-8');
require("../configuracion/parte_superior_privado_bootstrap.php");
if (db_link() instanceof mysqli) {
        mysqli_set_charset(db_link(), 'utf8mb4');
}

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

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
<link rel="stylesheet" href="/gestion/panel/assets/responsive-lists.css?v=20261005.1">
<style>
.contact-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
        min-width: 190px;
}
.contact-actions .btn {
        white-space: nowrap;
}
.contact-origin-badge {
        display: inline-block;
        margin-left: 5px;
        padding: 2px 6px;
        border-radius: 999px;
        color: #17624f;
        background: #dff4ec;
        font-size: 9px;
        font-weight: 700;
        vertical-align: middle;
}
.contacts-table-scroll {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
}
.contacts-table-scroll > table {
        min-width: 980px;
}
@media (max-width: 767px) {
        .contact-actions {
                min-width: 160px;
        }
        .contact-actions .btn {
                flex: 1 1 auto;
        }
}
</style>
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
//-->
</SCRIPT>
<div class="contacts-page" align="center"><center>
<?php if (!empty($_SESSION['contactos_flash']) && is_array($_SESSION['contactos_flash'])) { ?>
        <?php
        $contactos_flash=$_SESSION['contactos_flash'];
        unset($_SESSION['contactos_flash']);
        ?>
        <div class="alert alert-<?=
                $contactos_flash['type']==='error' ? 'danger' : 'success'
        ?>" style="max-width: 900px; text-align: left;">
                <?=app_escape($contactos_flash['message'])?>
        </div>
<?php } ?>
<?php  if ($idUsuario<>6297) { ?>
<input type="button" onclick="window.location.href='informe_contactos.php?curso='+self.document.seleccion_curso.curso_seleccionado.value" value="Ver informe" style="font-size: 10px"> <input type="button" onclick="window.location.href='nuevo_contacto.php'" value="Nuevo contacto" style="font-size: 10px"> <input type="button" onclick="window.location.href='/index.php'" value="Salir" style="font-size: 10px">
<?php 
}
?>
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
<form method="POST" name="detalles_grupo" action="enviar_email_todos.php" target="mensajes_alumnos">
<input type="hidden" value="1" name="tipo">                        
<h1 class="contacts-title">Listado de contactos en proceso de decisión</h1>
<div class="contacts-table-scroll legacy-table-scroll" role="region" aria-label="Contactos interesados: desliza para ver todas las columnas" tabindex="0" data-orientation-guard="off">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="95%">
<?php 
$busca_cotactos=db_query("SELECT * FROM contactos WHERE id_centro=0 OR id_centro=$centro_actual$filtro ORDER BY id_centro DESC,id_curso,proceso_decision, fecha_contacto, Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_cotactos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=9><font size=2 face="<?= $fuente_general; ?>">Para editar un contacto, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)</font></td>
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
                $busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alumnos["id_centro"]);
                if (db_num_rows($busca_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_centro);
                        $nombre_centro_actual=$datos_centro["nombre_centro"];
                        }
                }
        ?>
        <tr bgcolor="#efefef">
        <td align="left" colspan=9><font size=3 face="<?= $fuente_general; ?>"><b>Escuela: <?=$nombre_centro_actual?></b></font></td>
        </tr>
        <?php 
        }
        
        if ($curso_actual!=$listado_alumnos["id_curso"]) {
                        // Datos del curso
                        if ($listado_alumnos["id_curso"]==0) {
                        $nombre_curso="Sin curso definido";
                        } else {
                        $busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$listado_alumnos["id_curso"]);
                        if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $nombre_curso=$datos_curso["nombre"];
                                }
                        }
                        $numero_color_curso=intval(substr($listado_alumnos["id_curso"],strlen($listado_alumnos["id_curso"])-1,1));
                        ?>
                        <tr valign="top" bgcolor="<?=$colores[$numero_color_curso]?>">
                                <td align="left" colspan=9><font size=3 face="<?= $fuente_general; ?>"><b>Para el Curso: <?=$nombre_curso;?></b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$listado_alumnos["id_curso"];
                        }
        
        $proceso=$listado_alumnos["proceso_decision"];
        if ($proceso!=$proceso_actual) {
        $proceso_actual=$proceso;
        ?>
        <tr bgcolor="#efefef">
        <td align="left" colspan=9><font size=2 face="<?= $fuente_general; ?>"><b>Contactos en estado <?=$proceso_actual?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre alumno</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nacimiento</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre persona contacto</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha contacto</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono</b></font></td>
        <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>E-mail</b></font></td>
		<td class="text-center"><a href="javascript: mail_grupos('<?=$proceso_actual?>')" data-toggle="tooltip" data-placement="left" title="Haz clic para seleccionar todos los contactos en estado <?=$proceso_actual?> para el envío de Email"><i class="fa fa-envelope" aria-hidden="true"></i></a> <a href="javascript: sms_grupos('<?=$proceso_actual?>')" data-toggle="tooltip" data-placement="right" title="Haz clic para seleccionar todos los contactos en estado <?=$proceso_actual?> para el envío de SMS"><i class="fa fa-comment-o" aria-hidden="true"></i></a></td>          
        <td align="center" width=7%><font size=2 face="<?= $fuente_general; ?>"><font size=1 face="<?= $fuente_general; ?>"><button class="btn btn-primary btn-xs" onclick="enviar_mail();" data-toggle="tooltip" data-placement="top" title="Enviar email a los marcados"><i class="fa fa-envelope" aria-hidden="true"></i></button> <button class="btn btn-primary btn-xs" onclick="enviar_sms();" data-toggle="tooltip" data-placement="top" title="Enviar SMS a los marcados"><i class="fa fa-comment-o" aria-hidden="true"></i></button></font></font></td>
        </tr>
        <?php 
        }
        $color_fuente_linea="#000000";
        
        if (($listado_alumnos["fecha_nacimiento"]!="0000-00-00") and (strlen($listado_alumnos["fecha_nacimiento"])!=0)) {
        	$fecha_nacimiento_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"])); 
        	}else {
        	$fecha_nacimiento_alumno="-";
        	} 

        $contacto_importado_web=false;
        $busca_origen_web=db_query(
                "SELECT id_relacion FROM contactos_web_interesados "
                . "WHERE id_contacto_interesado="
                . (int)$listado_alumnos["id_contacto"]
                . " LIMIT 1"
        );
        if ($busca_origen_web && db_num_rows($busca_origen_web)!=0) {
                $contacto_importado_web=true;
        }

        $contacto_ya_matriculado=false;
        if (
                (int)$listado_alumnos["id_usuario"]>0
                && (int)$listado_alumnos["id_curso"]>0
                && (int)$listado_alumnos["id_centro"]>0
        ) {
                $busca_matricula_contacto=db_query(
                        "SELECT id_alumno FROM alumnos WHERE id_usuario="
                        . (int)$listado_alumnos["id_usuario"]
                        . " AND id_curso=".(int)$listado_alumnos["id_curso"]
                        . " AND id_centro=".(int)$listado_alumnos["id_centro"]
                        . " LIMIT 1"
                );
                if (
                        $busca_matricula_contacto
                        && db_num_rows($busca_matricula_contacto)!=0
                ) {
                        $contacto_ya_matriculado=true;
                }
        }
        
        ?>
        <tr>
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font><?php if ($contacto_importado_web) { ?><span class="contact-origin-badge">Web</span><?php } ?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nacimiento_alumno;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["persona_contacto"]?></font></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_contacto"]))?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["poblacion"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["email"]?></font></td>
        <td width="45" class="text-center"><input type="checkbox" name="email[]" id="email_<?=$proceso_actual?>_<?=$listado_alumnos["id_contacto"]?>" value="<?=$listado_alumnos["id_contacto"]?>"> <input type="checkbox" name="sms[]" id="sms_<?=$proceso_actual?>_<?=$listado_alumnos["id_contacto"]?>" value="<?=$listado_alumnos["id_contacto"]?>"></td>
        <td align="center">
                <div class="contact-actions">
                        <a class="btn btn-primary btn-xs" href="nuevo_contacto.php?id_contacto=<?=(int)$listado_alumnos["id_contacto"]?>" title="Editar contacto">
                                <i class="fa fa-pencil" aria-hidden="true"></i> Editar
                        </a>
                        <?php if (!$contacto_ya_matriculado) { ?>
                                <a class="btn btn-success btn-xs" href="matricular_contacto.php?id_contacto=<?=(int)$listado_alumnos["id_contacto"]?>">
                                        <i class="fa fa-user-plus" aria-hidden="true"></i> Inscribir
                                </a>
                        <?php } else { ?>
                                <span class="label label-success">Matriculado</span>
                        <?php } ?>
                        <a class="btn btn-danger btn-xs" href="javascript:eliminar_contacto(<?=(int)$listado_alumnos["id_contacto"]?>);" title="Eliminar contacto">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                </div>
        </td>
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
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ningún contacto.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</div>
</form>
<div class="row">
	<div class="col-md-12 text-right">
		<button class="btn btn-primary btn-sm" onclick="enviar_mail();"><i class="fa fa-envelope" aria-hidden="true"></i>  Enviar email a los marcados</button>
		<button class="btn btn-primary btn-sm" onclick="enviar_sms();"><i class="fa fa-comment-o" aria-hidden="true"></i> Enviar SMS a los marcados</button>
	</div>
</div>
                        <?php  if ($idUsuario<>6297) { ?>
<br>
<input type="button" onclick="window.location.href='informe_contactos.php'" value="Ver informe" style="font-size: 10px"> <input type="button" onclick="window.location.href='nuevo_contacto.php'" value="Nuevo contacto" style="font-size: 10px"> <input type="button" onclick="window.location.href='/index.php'" value="Salir" style="font-size: 10px">
<br><?php  } ?>
</center></div>
</div>

<div class="modal modal-wide fade" id="enviosModal" tabindex="-1" role="dialog" aria-labelledby="enviosModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="titulo_envios"><h4 class="modal-title" id="enviosModalLabel">Envío de mensajes a los alumnos</h4></div>
      </div>
      <div class="modal-body">
        <iframe src="" name="mensajes_alumnos" id="mensajes_alumnos" width=100% height=260 frameborder=0></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>

$( document ).ready(function() {


	$('[data-toggle="tooltip"]').tooltip()

});

</script>

<?php  require("../configuracion/parte_inferior.php"); ?>
