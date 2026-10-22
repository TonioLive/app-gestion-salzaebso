<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

include("funciones_resultado.php");

$formulario_name="pruebas";

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["id_alumno"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["id_alumno"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function imprimir() {
        document.getElementById('botonera').style.display='none';
        self.print();
        document.getElementById('botonera').style.display='block';
        }
function editar(codigo) {
                window.open('/alumnos/nuevo_padre1.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
function eliminar_prueba(id_prueba) {
        if (confirm("Se va a eliminar la prueba seleccionada. ¿Estás totalmente segura/o? Esta acción no se puede deshacer.")) {
                window.location.href='eliminar_prueba.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba='+id_prueba;
                }
        }
function eliminar_prueba_ampliado(id_prueba) {
        if (confirm("Se va a eliminar la prueba seleccionada. ¿Estás totalmente segura/o? Esta acción no se puede deshacer.")) {
                window.location.href='eliminar_prueba_ampliado.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba='+id_prueba;
                }
        }
function eliminar_cuestionario(id_prueba,tipo_cuestionario) {
        if (confirm("Se va a eliminar el cuestionario seleccionado. ¿Estás totalmente segura/o? Esta acción no se puede deshacer.")) {
                window.location.href='/gestion/cuestionario_edu/eliminar_cuestionario.php?id_alumno=<?=$id_usuario_seleccionado?>&id_cuestionario='+id_prueba+"&tipo_cuestionario="+tipo_cuestionario;
                }
        }
function eliminar_prueba_mcarhty(id_prueba) {
        if (confirm("Se va a eliminar la prueba seleccionada. ¿Estás totalmente segura/o? Esta acción no se puede deshacer.")) {
                window.location.href='eliminar_prueba_mcarhty.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba='+id_prueba;
                }
        }
function eliminar_prueba_comparada(id_prueba) {
        if (confirm("Se va a eliminar la prueba seleccionada. ¿Estás totalmente segura/o? Esta acción no se puede deshacer.")) {
                window.location.href='eliminar_prueba_comparada.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba='+id_prueba;
                }
        }

        
function nueva_prueba(prueba) {


        if (prueba==1) {
                self.document.<?=$formulario_name;?>.action="nuevo_brunet.php";
                }
        if (prueba==2) {
                self.document.<?=$formulario_name;?>.action="nuevo_brunet_ampliado.php";
                }
        if (prueba==3) {
                self.document.<?=$formulario_name;?>.action="nuevo_mcarthy.php";
                }
        if (prueba==4) {
                self.document.<?=$formulario_name;?>.action="nueva_comparativa.php";
                }
		if (prueba==5) {
				self.document.<?=$formulario_name;?>.tipo_cuestionario.value="_anex1a";
                self.document.<?=$formulario_name;?>.action="/gestion/cuestionario_edu/nuevo_cuestionario.php";
                }
		if (prueba==6) {
				self.document.<?=$formulario_name;?>.tipo_cuestionario.value="_anex2";
                self.document.<?=$formulario_name;?>.action="/gestion/cuestionario_edu/nuevo_cuestionario.php";
                }
                   
        var errores=0;

        var mensajes_error="";

        // Comprueba fecha inicio
        la_fecha=self.document.<?=$formulario_name;?>.fecha_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_ano.value
        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha introducida no es correcta.\u000A";errores=1; }

        if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }

        }

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

                mes_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

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

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino='+nombre_formulario+'&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
        
//-->
</SCRIPT>
<script>

var pagina_destino="/gestion/guarda_dato.php";

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);

    }
}

function guarda_checkbox(campo_busqueda,valor_busqueda,tabla,campo) {
        pagina_destino="/gestion/guarda_checkbox.php";

        sendAndLoad({desc:'campo_busqueda',dato:campo_busqueda},{desc:'valor_busqueda',dato:valor_busqueda},{desc:'tabla',dato:tabla},{desc:'campo',dato:campo});

        // window.open(pagina_destino+"?campo_busqueda="+campo_busqueda+"&valor_busqueda="+valor_busqueda+"&tabla="+tabla+"&campo="+campo);		
			
        }

function guarda_dato(campo_busqueda,valor_busqueda,tabla,campo) {

        pagina_destino="/gestion/guarda_dato.php";
        
        valor_campo=document.getElementById(campo+valor_busqueda).value;

        sendAndLoad({desc:'campo_busqueda',dato:campo_busqueda},{desc:'valor_busqueda',dato:valor_busqueda},{desc:'tabla',dato:tabla},{desc:'campo',dato:campo},{desc:'valor_campo',dato:valor_campo});

        // window.open(pagina_destino+"?campo_busqueda="+campo_busqueda+"&valor_busqueda="+valor_busqueda+"&tabla="+tabla+"&campo="+campo+"&valor_campo="+valor_campo);

        }

</script>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 

$id_curso_actual=$_GET["id_curso_alumno"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                // Datos del paso
                $usuario_sexo=$datos_usuario["sexo"];
                
if ($datos_usuario["nonato"]==1) {
        $usuario_fechanac="No ha nacido";
        } else {
                
                if (strlen($datos_usuario["fecha_nacimiento"])==0) {
                        $usuario_fechanac="";
                        } else {
                        $nacimiento=explode( '-', $datos_usuario["fecha_nacimiento"]);
                        $edad=date("Y")-$nacimiento[0];
                        $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                        $hoy=strtotime(date("Y/m/d"));
                        if (($cumple-$hoy)>0) {
                                $edad=$edad-1;
                                }
        if ($edad<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_usuario["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad=$edad_d_l." meses";
                } else {
                $edad=$edad." años";
                }
                        $usuario_fechanac=date("d/m/Y",strtotime($datos_usuario["fecha_nacimiento"])).", Edad actual: ".$edad;;
                        }
                }
                $usuario_direccion=$datos_usuario["Direccion"];
                $usuario_cp=$datos_usuario["cp"];
                $usuario_poblacion=$datos_usuario["Poblacion"];
                $usuario_provincia=$datos_usuario["Provincia"];
                $usuario_telefono1=$datos_usuario["telefono1"];
                $usuario_telefono2=$datos_usuario["telefono2"];
                $usuario_movil=$datos_usuario["movil"];
                $usuario_email=$datos_usuario["email"];
                $alergias=$datos_usuario["alergias"];
                $vacunaciones=$datos_usuario["vacunaciones"];
                $observaciones=$datos_usuario["observaciones"];
                $contacto1=$datos_usuario["contacto1"];
                $telefono_contacto1=$datos_usuario["telefono_contacto1"];
                $contacto2=$datos_usuario["contacto2"];
                $telefono_contacto2=$datos_usuario["telefono_contacto2"];
                $contacto_medico=$datos_usuario["contacto_medico"];
                $telefono_medico=$datos_usuario["telefono_medico"];
                $usuario_nacionalidad=$datos_usuario["nacionalidad"];
                }

        // Busca el curso actual
        if ($id_curso_actual==0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }
        
        // Busca inscripcion en curso seleccionado
        $busca_alumno_curso=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?', 'ss', array($id_usuario_seleccionado, $id_curso_actual));
        if (db_num_rows($busca_alumno_curso)!=0) {
                $datos_curso_alumno=db_fetch_array($busca_alumno_curso);
                $id_centro_alumno=$datos_curso_alumno["id_centro"];
                } else {
                $id_centro_alumno=$centro_actual;
                }
        }

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
      
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Ficha Alumno</b></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td rowspan=6 align="center" valign="middle" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos personales</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_nombre;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Apellidos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_apellido1;?> <?=$usuario_apellido2;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"><?php  if ($usuario_sexo=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nacionalidad</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"><?=$usuario_nacionalidad;?></font></td>
                        </tr>
                        <tr>
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>Fecha Nacimiento</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_fechanac;?></font></td>
                        </tr>
                        </table>
                        <br>
                        <input type="button" value="Examinar carpetas de informes" onclick="window.open('ver_carpeta_alumno.php?id_alumno=<?=$id_usuario_seleccionado?>','carpetas','width=550,height=450toolbar=yes,scrollbars=yes,resizables=yes');">
                        <br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=5 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Pruebas realizadas</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Edad Real</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Resultado</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $busca_pruebas_realizadas=db_query_params('SELECT * FROM pruebas_brunet WHERE id_alumno=? ORDER BY fecha', 's', array($id_usuario_seleccionado));
                        while($pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas)) {
                        
                        // Calcula resultado prueba
                        $resultado=calcular_brunet($pruebas_realizadas["id_prueba"]);;
                        
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">Brunet</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($pruebas_realizadas["fecha"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$pruebas_realizadas["er"];?> meses</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$resultado;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="nuevo_brunet.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Editar prueba"><img src="modificar.gif" border=0></a> <a href="resultado_brunet.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Ver resultado"><img src="editar.gif" border=0></a> <a target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" href="descarga_informe_brunet.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Imprimir informe"><img src="imprimir.gif" border=0></a> <a href="#" onclick="eliminar_prueba(<?=$pruebas_realizadas["id_prueba"]?>)" title="Eliminar prueba"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                        <?php 
                        }
                        $busca_pruebas_realizadas=db_query_params('SELECT * FROM pruebas_brunet_ampliado WHERE id_alumno=? ORDER BY fecha', 's', array($id_usuario_seleccionado));
                        while($pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas)) {

                        // Calcula resultado prueba
                        $resultado=calcular_brunet_a($pruebas_realizadas["id_prueba"]);;

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">Brunet Ampliado</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($pruebas_realizadas["fecha"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$pruebas_realizadas["er"];?> meses</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$resultado;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="nuevo_brunet_ampliado.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Editar prueba"><img src="modificar.gif" border=0></a> <a href="resultado_brunet_ampliado.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Ver resultado"><img src="editar.gif" border=0></a> <a target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" href="descarga_informe_brunet_ampliado.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Imprimir informe"><img src="imprimir.gif" border=0></a> <a href="#" onclick=eliminar_prueba_ampliado(<?=$pruebas_realizadas["id_prueba"]?>)" title="Eliminar prueba"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                        <?php 
                        }
                        $busca_pruebas_realizadas=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_alumno=? ORDER BY fecha', 's', array($id_usuario_seleccionado));
                        while($pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas)) {

                        // Calcula resultado prueba
                        $resultado="<table><tr valign=top><td><font size=1><b>Resultado Mc Carthy</b></font>".calcular_mccarthy($pruebas_realizadas["id_prueba"])."</td><td><font size=1><b>Comparación Brunet</b></font>".conversion_mccarthy($pruebas_realizadas["id_prueba"])."</td></tr></table>";

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">Mc Carhty</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($pruebas_realizadas["fecha"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$pruebas_realizadas["er"];?> meses</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$resultado;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="nuevo_mcarthy.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Editar prueba"><img src="modificar.gif" border=0></a> <a href="resultado_mcarthy.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Ver resultado"><img src="editar.gif" border=0></a> <a target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" href="descarga_informe_mcarthy.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Imprimir informe"><img src="imprimir.gif" border=0></a> <a href="#" onclick="eliminar_prueba_mcarhty(<?=$pruebas_realizadas["id_prueba"]?>)" title="Eliminar prueba"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                        <?php 
                        }
                        $busca_pruebas_realizadas=db_query_params('SELECT * FROM pruebas_comparadas WHERE id_alumno=? ORDER BY fecha', 's', array($id_usuario_seleccionado));
                        while($pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas)) {

                        // Busca pruebas
                        
                        $prueba1=$pruebas_realizadas["id_prueba1"];
                        $descripcion1="";
                        if (strlen($prueba1)!=0) {
                                $tipo_prueba=substr($prueba1,0,1);
                                $id_prueba=substr($prueba1,1,strlen($prueba1)-1);
                                         switch($tipo_prueba) {
                                                case "B":
                                                // 1. Brunet
                                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet WHERE id_prueba=?', 's', array($id_prueba));
                                                if (db_num_rows($busca_datos_prueba)!=0) {
                                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                                        $descripcion1="Brunet (".date("d/m/Y",strtotime($datos_prueba["fecha"])).", ER: ".$datos_prueba["er"].")";
                                                        }
                                                break;
                                                case "A":
                                                // 2. Brunet Ampliado
                                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet_ampliado WHERE id_prueba=?', 's', array($id_prueba));
                                                if (db_num_rows($busca_datos_prueba)!=0) {
                                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                                        $descripcion1="Brunet ampliado (".date("d/m/Y",strtotime($datos_prueba["fecha"])).", ER: ".$datos_prueba["er"].")";
                                                        }
                                                break;
                                                case "C":
                                                // 3. Mc Carhty
                                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba));
                                                if (db_num_rows($busca_datos_prueba)!=0) {
                                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                                        $descripcion1="Mc Carhty (".date("d/m/Y",strtotime($datos_prueba["fecha"])).", ER: ".$datos_prueba["er"].")";
                                                        }
                                                break;
                                                }
                                }
                                
                        $prueba2=$pruebas_realizadas["id_prueba2"];
                        $descripcion2="";
                        if (strlen($prueba2)!=0) {
                                $tipo_prueba=substr($prueba2,0,1);
                                $id_prueba=substr($prueba2,1,strlen($prueba2)-1);
                                         switch($tipo_prueba) {
                                                case "B":
                                                // 1. Brunet
                                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet WHERE id_prueba=?', 's', array($id_prueba));
                                                if (db_num_rows($busca_datos_prueba)!=0) {
                                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                                        $descripcion2="Brunet (".date("d/m/Y",strtotime($datos_prueba["fecha"])).", ER: ".$datos_prueba["er"].")";
                                                        }
                                                break;
                                                case "A":
                                                // 2. Brunet Ampliado
                                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet_ampliado WHERE id_prueba=?', 's', array($id_prueba));
                                                if (db_num_rows($busca_datos_prueba)!=0) {
                                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                                        $descripcion2="Brunet ampliado (".date("d/m/Y",strtotime($datos_prueba["fecha"])).", ER: ".$datos_prueba["er"].")";
                                                        }
                                                break;
                                                case "C":
                                                // 3. Mc Carhty
                                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba));
                                                if (db_num_rows($busca_datos_prueba)!=0) {
                                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                                        $descripcion2="Mc Carhty (".date("d/m/Y",strtotime($datos_prueba["fecha"])).", ER: ".$datos_prueba["er"].")";
                                                        }
                                                break;
                                                }
                                }
                        if ((strlen($descripcion1)!=0) and (strlen($descripcion1)!=0)) {
                                $descripcion=$descripcion1." - ".$descripcion2;
                                } else {
                                if (strlen($descripcion1)!=0) {
                                        $descripcion=$descripcion1;
                                        }
                                if (strlen($descripcion2)!=0) {
                                        $descripcion=$descripcion2;
                                        }
                                }

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">Comparativa</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($pruebas_realizadas["fecha"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$pruebas_realizadas["er"];?> meses</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$descripcion?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="nueva_comparativa.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Editar prueba"><img src="modificar.gif" border=0></a> <a target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" href="descarga_informe_comparado.php?id_alumno=<?=$id_usuario_seleccionado?>&id_prueba=<?=$pruebas_realizadas["id_prueba"]?>" target="prueba_<?=$pruebas_realizadas["id_prueba"]?>" title="Imprimir informe"><img src="imprimir.gif" border=0></a> <a href="#" onclick="eliminar_prueba_comparada(<?=$pruebas_realizadas["id_prueba"]?>)" title="Eliminar prueba"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                        <?php 
                        }
                        
                        // Cuestionarios
$busca_pruebas_realizadas=db_query_params('SELECT * FROM historial_educativo_cuestionarios WHERE id_usuario=? ORDER BY fecha_historial', 's', array($id_usuario_seleccionado));
                        while($pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas)) {

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">Cuestionario historial educativo - Anexo I (V.1)</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($pruebas_realizadas["fecha_historial"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">-</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">-</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="/gestion/cuestionario_edu/nuevo_cuestionario.php?tipo_cuestionario=&id_alumno=<?=$id_usuario_seleccionado?>&id_cuestionario=<?=$pruebas_realizadas["id_cuestionario"]?>" target="prueba_<?=$pruebas_realizadas["id_cuestionario"]?>" title="Editar cuestionario"><img src="modificar.gif" border=0></a> <a target="prueba_<?=$pruebas_realizadas["id_cuestionario"]?>" href="/pdf/historial_educativo_anexo.php?id_curso=<?=$pruebas_realizadas["id_curso"]?>&id_alumno=<?=$pruebas_realizadas["id_usuario"]?>&id_cuestionario=<?=$pruebas_realizadas["id_cuestionario"]?>&tipo_cuestionario=" title="Imprimir informe"><img src="imprimir.gif" border=0></a> <a href="#" onclick="eliminar_cuestionario(<?=$pruebas_realizadas["id_cuestionario"]?>,'')" title="Eliminar cuestionario"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                        <?php 
                        }
// Cuestionarios 2
$busca_pruebas_realizadas=db_query_params('SELECT * FROM historial_educativo_cuestionarios_anex2 WHERE id_usuario=? ORDER BY fecha_historial', 's', array($id_usuario_seleccionado));
                        while($pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas)) {

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">Cuestionario historial educativo - Anexo II</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($pruebas_realizadas["fecha_historial"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">-</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">-</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="/gestion/cuestionario_edu/nuevo_cuestionario.php?tipo_cuestionario=_anex2&id_alumno=<?=$id_usuario_seleccionado?>&id_cuestionario=<?=$pruebas_realizadas["id_cuestionario"]?>" target="prueba_<?=$pruebas_realizadas["id_cuestionario"]?>" title="Editar cuestionario"><img src="modificar.gif" border=0></a> <a target="prueba_<?=$pruebas_realizadas["id_cuestionario"]?>" href="/pdf/historial_educativo_anexo.php?id_curso=<?=$pruebas_realizadas["id_curso"]?>&id_alumno=<?=$pruebas_realizadas["id_usuario"]?>&id_cuestionario=<?=$pruebas_realizadas["id_cuestionario"]?>&tipo_cuestionario=_anex2" title="Imprimir informe"><img src="imprimir.gif" border=0></a> <a href="#" onclick="eliminar_cuestionario(<?=$pruebas_realizadas["id_cuestionario"]?>,'_anex1a')" title="Eliminar cuestionario"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                        <?php 
                        }
                        
// Cuestionarios 1a
$busca_pruebas_realizadas=db_query_params('SELECT * FROM historial_educativo_cuestionarios_anex1a WHERE id_usuario=? ORDER BY fecha_historial', 's', array($id_usuario_seleccionado));
                        while($pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas)) {

                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">Cuestionario historial educativo - Anexo I (V.2)</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($pruebas_realizadas["fecha_historial"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">-</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">-</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="/gestion/cuestionario_edu/nuevo_cuestionario.php?tipo_cuestionario=_anex1a&id_alumno=<?=$id_usuario_seleccionado?>&id_cuestionario=<?=$pruebas_realizadas["id_cuestionario"]?>" target="prueba_<?=$pruebas_realizadas["id_cuestionario"]?>" title="Editar cuestionario"><img src="modificar.gif" border=0></a> <a target="prueba_<?=$pruebas_realizadas["id_cuestionario"]?>" href="/pdf/historial_educativo_anexo.php?id_curso=<?=$pruebas_realizadas["id_curso"]?>&id_alumno=<?=$pruebas_realizadas["id_usuario"]?>&id_cuestionario=<?=$pruebas_realizadas["id_cuestionario"]?>&tipo_cuestionario=_anex1a" title="Imprimir informe"><img src="imprimir.gif" border=0></a> <a href="#" onclick="eliminar_cuestionario(<?=$pruebas_realizadas["id_cuestionario"]?>,'_anex1a')" title="Eliminar cuestionario"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                        <div id="botonera" align="center"><form name="<?=$formulario_name;?>" method="GET" action="">
                        <input type="hidden" value="<?=$id_usuario_seleccionado;?>" name="id_alumno">
                        <input type="hidden" value="" name="tipo_cuestionario">
                        <br><div class="caja_formulario"><br><font size=2 face="<?= $fuente_general; ?>"><b>Fecha realización prueba</b></font> <select name="fecha_dia" class="defaut" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$fecha_dia) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_mes" class="defaut" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==$fecha_mes) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_ano" class="defaut" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+1; $c_ano>=strftime("%Y")-2; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==$fecha_ano) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font><br><input type="button" onclick="nueva_prueba(1);" value="Nueva prueba Brunet" style="font-size:10px"> <input type="button" onclick="nueva_prueba(2);" value="Nueva prueba Brunet Ampliado" style="font-size:10px"><br>
<input type="button" onclick="nueva_prueba(3);" value="Nuevo Mc Carthy" style="font-size:10px"> <input type="button" onclick="nueva_prueba(4);" value="Nuevo informe comparativo" style="font-size:10px"><br>
<br></div><br><div class="caja_formulario"><br><input type="button" onclick="nueva_prueba(5);" value="Nuevo Anexo I Historial Educativo" style="font-size:10px"> <input type="button" onclick="nueva_prueba(6);" value="Nuevo Anexo II Historial Educativo" style="font-size:10px"><br><br>
<font size=2 face="<?= $fuente_general; ?>"><b>Anexo III, Informe Resumen : </b></font><?php 

// Muestra los cursos del alumno
$busca_cursos=db_query_params('SELECT * FROM cursos_academicos,alumnos WHERE cursos_academicos.id_curso=alumnos.id_curso AND alumnos.id_usuario=? ORDER BY cursos_academicos.fecha_inicio', 's', array($id_usuario_seleccionado));

while($datos_cursos=db_fetch_array($busca_cursos)) {
?>
<input type="button" style="font-size: 10px" value="Curso <?=$datos_cursos["nombre"]?>" onclick="window.open('/pdf/evaluacion_e_curso.php?id_aula=<?=$datos_cursos["aula"]?>&id_alumno=<?=$datos_cursos["id_alumno"]?>');">
<?php 
        } 
?><br>
<br><font size=2 face="<?= $fuente_general; ?>"><b>Medidas complementarias Anexo III: </b><br><textarea style="font-size: 12px; font-family: Arial" id="medidas_complementarias<?=$id_usuario_seleccionado;?>" name="medidas_complementarias<?=$id_usuario_seleccionado;?>" cols=80 rows=3 onblur="guarda_dato('id_usuario','<?=$id_usuario_seleccionado;?>','usuarios','medidas_complementarias');"><?=$datos_usuario["medidas_complementarias"]?></textarea>
<br></div><br><br>                                
<input type="button" onclick="imprimir();" value="Imprimir" style="font-size:10px"> <input type="button" onclick="window.close();" value="Cerrar" style="font-size:10px">
                        </form>
                        </div><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
</center></div>
</div>
</body>
</html>