<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");

// busca alumnos de un curso

$centro_seleccionado_listado=$_GET["cen"];
if (strlen($centro_seleccionado_listado)==0) {
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



$latitudes='';
$longitudes='';

$busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=$curso_seleccionado_listado AND alumnos.id_centro=$centro_seleccionado_listado AND usuarios.coordenadas is Not Null ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");

while($alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos)) {

                // Escribe fila
                if ((strlen($alumnos_inscritos["coordenadas"])!=0) and ($alumnos_inscritos["coordenadas"]!="NO LOCALIZADO")) {
                        $coordenadas_actuales=str_replace("(","",$alumnos_inscritos["coordenadas"]);
                        $coordenadas_actuales=str_replace(")","",$coordenadas_actuales);
                        $array_coordenadas=explode(",",$coordenadas_actuales);
                        $latitud_actual=$array_coordenadas[0];
                        $longitud_actual=$array_coordenadas[1];
                        $latitudes=$latitudes.$latitud_actual.',';
                        $longitudes=$longitudes.$longitud_actual.',';
                        }

}

if (strlen($latitudes)!=0) {
        $latitudes=substr($latitudes,0,strlen($latitudes)-1);
        $longitudes=substr($longitudes,0,strlen($longitudes)-1);
        }
        
// Competencia
$latitudes_c='';
$longitudes_c='';

$busca_escuelas_competencia=db_query("SELECT * FROM escuelas_competencia");
while($competencia=db_fetch_array($busca_escuelas_competencia)) {
                        $latitudes_c=$latitudes_c.str_replace(",",".",$competencia["latitud"]).',';
                        $longitudes_c=$longitudes_c.str_replace(",",".",$competencia["longitud"]).',';
        }
if (strlen($latitudes_c)!=0) {
        $latitudes_c=substr($latitudes_c,0,strlen($latitudes_c)-1);
        $longitudes_c=substr($longitudes_c,0,strlen($longitudes_c)-1);
        }

$formulario_accion="mapa.php";

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
  var latitudes = [<?=$latitudes;?>];
  var longitudes = [<?=$longitudes;?>];
  var latitudes_c = [<?=$latitudes_c;?>];
  var longitudes_c = [<?=$longitudes_c;?>];


  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom:15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    muestra_direccion();
    muestra_escuelas();
  }

  function Localiza(latitud,longitud) {

        var point = new google.maps.LatLng(latitud, longitud);

        map.setCenter(point);
        var marker = new google.maps.Marker({
            map: map,
            position: point,
        });

        }
        
  function Localiza_c(latitud,longitud) {

            var image = 'images/beachflag.png';
            var myLatLng = new google.maps.LatLng(latitud, longitud);
            var beachMarker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: image
            });
        }

  function codeAddress(consulta) {
    var address = consulta;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        // alert("No se ha encontrado la dirección en el mapa: " + status + " " + consulta);
        // errores = errores+"No se ha encontrado la dirección en el mapa: " + status + "Dirección: " + consulta + "<br>";
      }
    });
  }

  function muestra_direccion() {

      for (conta=0;conta<latitudes.length;conta++) {
                Localiza(latitudes[conta],longitudes[conta]);
                }
      }
  function muestra_escuelas() {

      for (conta=0;conta<latitudes_c.length;conta++) {
                Localiza_c(latitudes_c[conta],longitudes_c[conta]);
                }
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
    <td align="center" colspan=4><small><b>Mapa de distribución de los alumnos</b></small></td>
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
                        <td rowspan=2 bgcolor="#f2efe9">
                        <font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Leyenda</b><br>
                        &nbsp;&nbsp;&nbsp;<img src="images/marca1.gif" border=0> Alumno<br>
                        &nbsp;&nbsp;&nbsp;<img src="images/marca2.gif" border=0> Escuela Infantil<br></font></td>
                        <td rowspan=2 bgcolor="#f2efe9" align="center"><font size=2><a href="nueva_escuela.php">Añadir escuela infantil</a><br><br>
                        <a href="listado_escuelas.php">Listado de escuelas</a></font></td>
                        </tr>
                <tr>
                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Centro:</b></font></td>
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
                        <input type="submit" value="Mostar en el mapa" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px"><br><br>
                        </form>
<div id="map_canvas" style="width:1240; height:700"></div>
<div id="capa_errores" style="visible: block"></div>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>
