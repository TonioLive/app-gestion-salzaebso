<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 
$array_listas_de_espera=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");

$edad_lista=app_value($_GET,'edad_lista','0');
$curso_seleccionado=app_value($_GET,'id_curso_lista');
if(!ctype_digit((string)$edad_lista)||strlen((string)$edad_lista)>10||($curso_seleccionado!==''&&(!ctype_digit((string)$curso_seleccionado)||strlen((string)$curso_seleccionado)>10)))security_stop('Grupo o curso no valido.',422);
$edad_lista=(int)$edad_lista;


// Busca el curso actual

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

$curso_seleccionado=(string)$id_curso_actual;
$orden_lista=app_value($_GET,'o','fecha_alta');
if(!in_array($orden_lista,array('','fecha_alta','puntuacion DESC'),true))security_stop('Orden de lista no valido.',422);
if (strlen($orden_lista)==0) {
        $orden_lista="fecha_alta";
        }

if ($orden_lista=="fecha_alta") {
        $descripcion_orden="fecha de llegada";
        } else {
        $descripcion_orden="puntos";
        }


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>                                                                                                                                           <div align="center">
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=155 height=1 align=left>
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
    <td width=1><img src="/imagen/nada.gif" border=0 height="150" width="1"></td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
<div align="center">
<table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
<tr><td><table width=690 border=2 bordercolor="#cc424f">
<tr>
<td bgcolor="#ffffff" align="center">
<font color="#cc424f" size=3 face="<?=$fuente_general?>"><b>Lista de espera, curso <?=$datos_curso_actual["nombre"]?>. Ordenada por <?=$descripcion_orden?><br>
<?=($array_listas_de_espera[$edad_lista]??'Otros grupos');?></b><br><br></font>
<table border=0 cellspacing="1" cellpadding="2" width="100%">
<tr bgcolor="#cc424f">
<td><font color="#ffffff" size=3 face="<?=$fuente_general?>"><b>Orden</b></font></td>
<td align="left"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Nombre</b></font></td>
<td align="left"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Email</b></font></td>
<td align="center"><a href='<?=$_SERVER['PHP_SELF']?>?edad_lista=<?=$edad_lista?>&id_curso_lista=<?=$curso_seleccionado?>&o=fecha_alta'><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Fecha inscripción</b></font></a></td>
<td align="center"><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Fecha deseada de incorporación y horarios</b></font></td>
<td align="center"><a href='<?=$_SERVER['PHP_SELF']?>?edad_lista=<?=$edad_lista?>&id_curso_lista=<?=$curso_seleccionado?>&o=puntuacion DESC'><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b>Puntos</b></font></a></td>
<td width=25% align="center">&nbsp;</td>
</tr>
<?php 
$lista_emails = array();

$busca_ninios=db_query_params("SELECT l.*,COALESCE(p.puntuacion,0) AS puntuacion FROM lista_espera l LEFT JOIN (SELECT id_usu_lista,SUM(valor) AS puntuacion FROM lista_espera_respuestas GROUP BY id_usu_lista) p ON p.id_usu_lista=l.id_usu_lista WHERE l.id_curso=? AND l.edad_espera=? AND l.id_centro=? ORDER BY l.motivo_baja,l.categoria,".$orden_lista,'iii',array((int)$curso_seleccionado,$edad_lista,(int)$centro_actual));

$orden_lista=1;

$nombres_motivos_baja=array();
$busca_motivos_baja=db_query("SELECT * FROM lista_espera_motivos_baja");
while($motivos_baja=db_fetch_array($busca_motivos_baja)){
        $nombres_motivos_baja[$motivos_baja["id_motivo"]]=$motivos_baja["descripcion_motivo"];
        }
$nombres_motivos_baja[0]="Actualmente en lista de espera";

$motivo_actual=-1;

while($lista_ninios=db_fetch_array($busca_ninios)) {

// Comprueba si es alumno
$busca_si_alumno=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=? AND id_centro=?', 'iii', array((int)$lista_ninios["id_usuario"], (int)$id_curso_actual,(int)$centro_actual));

$alerta_alumno="";
if (db_num_rows($busca_si_alumno)!=0) {
	$alerta_alumno=" <font color='#ff0000'><b>Ya es alumno/a</b></font><br />";
	}

if ($motivo_actual!=$lista_ninios["motivo_baja"]) {
$motivo_actual=$lista_ninios["motivo_baja"];
        ?>
        <tr bgcolor="#cc424f">
        <td align="left" colspan=6><font color="#ffffff" size=2 face="<?=$fuente_general?>"><b><?=$nombres_motivos_baja[$motivo_actual]?></b></font></td>
        </tr>
        <?php 
        }

$busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($lista_ninios["id_usuario"]));
$datos_ninio=db_fetch_array($busca_datos_usuario);

$fecha_deseada="";
if ((strlen($lista_ninios["fecha_deseada"])!=0) and ($lista_ninios["fecha_deseada"]!="0000-00-00")) {
        $fecha_deseada=date("d/m/Y",strtotime($lista_ninios["fecha_deseada"]))."<br>";
        }
        
        
        
$emails_ninio=array();        

	$emails=str_replace("/",",",$datos_ninio["email"]);        
	$array_emails=explode(",",$emails);
	foreach($array_emails as $email) {
		if ($lista_ninios["motivo_baja"]==0) {
			$lista_emails[$email]=$email;
		}	
		
		if (!in_array($email,$emails_ninio)) {
			$emails_ninio[]=$email;
		}
	}	
?>
</tr>
<tr bgcolor="#efefef">
<td align="right"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=$orden_lista;?></font></td>
<td align="left"><font color="#000000" size=2 face="<?=$fuente_general?>"><a target="edicion_alumnos" href="/alumnos/nuevo_alumno1.php?u=<?=$lista_ninios["id_usuario"];?>"><?=$datos_ninio["Nombre"]?> <?=$datos_ninio["Apellido1"]?> <?=$datos_ninio["Apellido2"]?></a></font></td>
<td><font color="#000000" size=2 face="<?=$fuente_general?>"><?=implode("<br>",$emails_ninio)?></font></td>
<td align="center"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=date("d/m/Y",strtotime($lista_ninios["fecha_alta"]))?></font></td>
<td align="center"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=$alerta_alumno?><?=$fecha_deseada?>
<?php 
        for ($c_dia=1; $c_dia<=7; $c_dia=$c_dia+1) {
                
                        $dia_actual=$lista_ninios["dia_deseado_".$c_dia];
                        
                        if ($dia_actual==1) {
                                
                                $hora_inicio_actual=date("H:i",strtotime($lista_ninios["ini_horario_deseado_".$c_dia]));
                                $hora_fin_actual=date("H:i",strtotime($lista_ninios["fin_horario_deseado_".$c_dia]));
                                
                                echo($dia_sem_largo[$c_dia]." de ".$hora_inicio_actual." a ".$hora_fin_actual." hrs.<br>");
                                                                                                
                                }
                                
                        }
?></font></td>
<td align="right"><font color="#000000" size=2 face="<?=$fuente_general?>"><?=$lista_ninios["puntuacion"]?></font></td>
<td width=25% align="center"><input type="button" value="Editar" onclick="self.location.href='editar_n_lista.php?id_n=<?=$lista_ninios["id_usu_lista"]?>'"></td>
</tr>
<?php 
$orden_lista++;

}
?>
</table>
</td>
</tr>
</table></td>
</table>

<?php
/*
<h3>Listado emails</h3>

<table border=1 cellspacing="1" cellpadding="2" width="100%">
<?php

foreach($lista_emails as $email) {
?>
	<tr>
		<td><?=$email?></td>
	</tr>
	<?php
	}
	?>
</table>
*/
?>
<br>
<input type="button" onclick="self.location.href='listas_espera.php?curso_seleccionado=<?=$curso_seleccionado?>'" value="Salir"> <input type="button" value="Añadir alumno" onclick="self.location.href='/alumnos/nuevo_alumno.php'">
<?php  
require("../configuracion/parte_inferior.php"); ?>
