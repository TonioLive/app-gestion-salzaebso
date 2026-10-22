<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");

$busqueda_google="C/ general aviles, 12 VALENCIA";

// busca alumnos de un curso

$centro_seleccionado_listado=$_GET["cen"];
if (strlen($curso_seleccionado_listado)==0) {
        $centro_seleccionado_listado=$centro_actual;
        }

$curso_seleccionado_listado=$_GET["cur"];
if (strlen($curso_seleccionado_listado)==0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $curso_seleccionado_listado=$datos_curso_actual["id_curso"];
                } else {
                $curso_seleccionado_listado=0;
                }
        }



$busquedas_google='';

$busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND (usuarios.coordenadas is Null) ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre LIMIT 0,10");
$nombre_padre=array();
$email_padre=array();
$telefonos_padres=array();

while($alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos)) {

                // Busca provincia
                $id_provincia_sel=$alumnos_inscritos["Provincia"];
                if (strlen($id_provincia_sel)==0) {
                        $id_provincia_sel=0;
                        }
                $busca_provincia=db_query_params('SELECT * FROM provincias WHERE id_provincia=?', 's', array($id_provincia_sel));
                if (db_num_rows($busca_provincia)!=0) {
                        $datos_provincia=db_fetch_array($busca_provincia);
                        $nombre_provincia=$datos_provincia["provincia"];
                        } else {
                        $nombre_provincia="";
                        }

                // Busca nombre del padre
                $nombre_padre[0]="";
                $email_padre[0]="";
                $nombre_padre[1]="";
                $email_padre[1]="";
                $nombre_padre[2]="";
                $email_padre[2]="";
                $nombre_padre[3]="";
                $email_padre[3]="";
                $telefonos_padres[0]="";
                $telefonos_padres[1]="";
                $telefonos_padres[2]="";
                $telefonos_padres[3]="";

                $consulta_relaciones=db_query_params('SELECT * FROM padres WHERE padre_de=? ORDER BY parentesco', 's', array($alumnos_inscritos["id_usuario"])) or die(db_error());

                $contador_padres=0;

                while($relaciones=db_fetch_array($consulta_relaciones)) {
                        $consulta_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($relaciones["id_usuario"]));

                        $nombre_padre_act="";
                        if (db_num_rows($consulta_datos_padre)!=0) {
                                        $datos_padre=db_fetch_array($consulta_datos_padre);
                                        $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                                        $email_padre_act=$datos_padre["email"];
                                        $telefono_padre_act=$datos_padre["telefono1"]." ".$datos_padre["telefono2"];
                                        }
                        $nombre_padre[$contador_padres]=$nombre_padre_act;
                        $email_padre[$contador_padres]=$email_padre_act;
                        $telefonos_padres[$contador_padres]=$telefono_padre_act;
                        $contador_padres++;
                        }

                $consulta_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($alumnos_inscritos["id_centro_ins"])) or die(db_error()."-2");
                $datos_centro=db_fetch_array($consulta_nombre_centro);

                // Busca datos del aula
                $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($alumnos_inscritos["aula"]));
                $datos_aula=db_fetch_array($consulta_datos_aula);

                $telefono_contacto1=$alumnos_inscritos["telefono_contacto1"];
                $telefono_contacto2=$alumnos_inscritos["telefono_contacto2"];
                $telefonos=$telefono_contacto1." ".$telefono_contacto2;
                // Escribe fila
                $direccion_complera=$alumnos_inscritos["Direccion"]." ".$alumnos_inscritos["cp"]." ".$alumnos_inscritos["Poblacion"];

                $busquedas_google=$busquedas_google.'"'.$direccion_complera.'",';
                $lista_usuarios=$lista_usuarios.'"'.$alumnos_inscritos["id_usuario"].'",';

}

if (strlen($busquedas_google)!=0) {
        $busquedas_google=substr($busquedas_google,0,strlen($busquedas_google)-1);
        $lista_usuarios=substr($lista_usuarios,0,strlen($lista_usuarios)-1);
        }

$formulario_accion="mapa_distribucion.php";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title><?php  echo $titulo_web ; ?></title>
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=ES"></script>
<script type="text/javascript">
  var geocoder;
  var map;
  var errores = "";
  var query = "<?=$busqueda_google;?>";
  var querys = [<?=$busquedas_google;?>];
  var lista_usuarios = [<?=$lista_usuarios;?>]

  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom:15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    muestra_direccion();
  }

  function codeAddress(consulta,id_usuario) {
    var address = consulta;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        guarda_dato(id_usuario,results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        guarda_dato(id_usuario,"NO LOCALIZADO");
        // alert("No se ha encontrado la dirección en el mapa: " + status + " " + consulta);
        // errores = errores+"No se ha encontrado la dirección en el mapa: " + status + "Dirección: " + consulta + "<br>";
      }
    });
  }

  function muestra_direccion() {
      errores="";

      for (conta=0;conta<querys.length;conta++) {
                codeAddress(querys[conta],lista_usuarios[conta]);
                }
      if (errores!="") {
              document.getElementById("capa_errores").innerHTML=errores;
              alert(errores);
              }
      }

</script>
<script>
//acá cada uno crea el objeto a su manera

var pagina_destino="http://www.escuelasinfantileslapices.es/contactos/guardar_coordenadas.php";

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

function guarda_dato(id_usuario,coordenadas) {
        sendAndLoad({desc:'id_usuario',dato:id_usuario},{desc:'coordenadas',dato:coordenadas});
        // window.open(pagina_destino+'?id_usuario='+id_usuario+'&coordenadas='+coordenadas);
        }

</script>
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto" onload="initialize();">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu_ancho.gif">
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
<div align="center"><center>
<br>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=13><small><b>Mapa de distribución de los alumnos</b></small></td>
  </tr>
 <form method="GET" action="<?=$formulario_accion;?>" name="listados">
                        <tr><td align="right">
                        <font size=2><b>Curso: </b></font></td><td><select name="cur" size=1 style="font-size: 10px">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$curso_seleccionado_listado) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></td>
                        </tr>
                <tr>
                <td align="right"><font size=2 face="<?= $fuente_general; ?>">Centro:</font></td>
                <td><select name="cen" size=1 style="font-size: 10px">
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"<?php if ($centros["id_centro"]==$centro_seleccionado_listado) { print " selected"; } ?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                </tr>
                        </table><br>
                        <input type="submit" value="Mostar en el mapa" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='listado_contactos.php'" style="font-size: 10px"><br><br>
                        </form>
<div id="map_canvas" style="width:960; height:580"></div>
<div id="capa_errores" style="visible: block"></div>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>
