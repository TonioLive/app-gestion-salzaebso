<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=listado_personal_".date("d-m-Y").".xls");
header("Pragma: no-cache");
header("Expires: 0");

$titulo_hoja="Personal actual (con contrato en vigor o próxima incorporación)";

if ($_GET["filtro_empleados"]==1) { $titulo_hoja="Personal que ya no trabaja en la escuela o sin contrato."; }

if ($_GET["filtro_empleados"]==2) { $titulo_hoja="Listado completo de personal"; }

echo("<h1>".$titulo_hoja."</h1>");
echo('<table cellspacing="0" cellpadding="0" border="1">');
echo("<tr>");
echo("<th><b>ID</b></th>");
echo("<th><b>Tipo empleado</b></th>");
echo("<th><b>Nombre</b></th>");
echo("<th><b>DNI</b></th>");
echo("<th><b>NSS</b></th>");
echo("<th><b>Domicilio</b></th>");
echo("<th><b>Teléfonos</b></th>");
echo("<th><b>Email</b></th>");
echo("<th><b>Fecha nacimiento</b></th>");
echo("<th><b>Inicio contrato</b></th>");
echo("<th><b>Fin contrato</b></th>");
echo("</tr>");


$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);
 

$filtro_empleados=$_GET["filtro_empleados"];
if (strlen($filtro_empleados)==0) {
        $filtro_empleados=0;
        }

  $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=$centro_actual OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1") or die(db_error());

  mt_srand((double)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
  
  $codigo=$rsslc["id_usuario"];
    
  // Comprueba si tiene contrato en vigor
  $fecha_control=date("Y-m-d");
  $busca_contratos=db_query("SELECT * FROM personal_contratos WHERE id_empleado=$codigo AND ((fecha_inicio<='$fecha_control' AND fecha_fin>='$fecha_control') OR (fecha_inicio<='$fecha_control' AND ((fecha_fin='0000-00-00') OR (fecha_fin is Null)))OR (fecha_inicio>'$fecha_control')) ORDER BY fecha_inicio DESC") or die(db_error());

  if (db_num_rows($busca_contratos)!=0) {
        $tipo_filtro_usuario=0;
        } else {
        $tipo_filtro_usuario=1;
        }
  
  if (($tipo_filtro_usuario==$filtro_empleados) or ($filtro_empleados==2)) {
  
  
  if ($id_tipo_listado!=$rsslc["nivel_acceso"]) {

  $id_tipo_listado=$rsslc["nivel_acceso"];
        }
  
    $nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
    
    $fecha_finalizacion="";
    
    // Busca el último contrato
    $observaciones="";
    
    $busca_contratos=db_query("SELECT * FROM personal_contratos WHERE id_empleado=$codigo ORDER BY fecha_inicio DESC");
    
    if (db_num_rows($busca_contratos)==0) {
    $observaciones=$observaciones."- No se le ha introducido ningún contrato laboral.<br>";
    } else {
    $datos_contratos=db_fetch_array($busca_contratos);
    
    $fecha_inicio=date("d/m/Y",strtotime($datos_contratos["fecha_inicio"]));
    // Comprueba si el contrato laboral ha finalizado
    if ((is_null($datos_contratos["fecha_fin"])) or ($datos_contratos["fecha_fin"]=="") or (strlen($datos_contratos["fecha_fin"])==0) or ($datos_contratos["fecha_fin"]=="0000-00-00")) {
                $fecha_finalizacion="Indefinida/o";
                } else {
                if (($datos_contratos["fecha_fin"]<date("Y-m-d"))) {
                        $observaciones=$observaciones."- El último contrato introducido ya ha finalizado, actualmente no está contratada/o.<br>";
                        $fecha_finalizacion="";
                        } else {
                        $fecha_finalizacion=date("d/m/Y",strtotime($datos_contratos["fecha_fin"]));
                        }
                }
    }                
    
    $contador=$contador+1;

    $t="";
    $t1=$rsslc["telefono1"];
    $t2=$rsslc["telefono2"];
    $t3=$rsslc["movil"];

    if (strlen($t1)>0)
    {
      $t=$t.$t1.", ";
    } 
    if (strlen($t2)>0)
    {
      $t=$t.$t2.", ";
    } 
    if (strlen($t3)>0)
    {
      $t=$t.$t3.", ";
    } 

    if (strlen($t)>0)
    {
      $t=substr($t,0,strlen($t)-2);
    } 

	echo("<tr>");
	echo("<td>".$codigo."</td>");
	echo("<td>".$tipo_usuario[$rsslc["nivel_acceso"]]."</td>");
	echo("<td>".ucwords(strtolower($nombre_ins))."</td>");
	echo("<td>".$rsslc["DNI"]."</td>");
	echo("<td>".$rsslc["NSS"]."</td>");
	echo("<td>".$rsslc["Direccion"]."<br>".$rsslc["cp"]."<br>".$rsslc["Poblacion"]."</td>");
	echo("<td>".$t."</td>");
	echo("<td>".$rsslc["email"]."</td>");
	echo("<td>".date("d/m/Y",strtotime($rsslc["fecha_nacimiento"]))."</td>");
	echo("<td>".$fecha_inicio."</td>");
	echo("<td>".$fecha_finalizacion."</td>");
    echo("</tr>");           
    }
   }

db_close();

echo("</table>");
echo("</body>");
echo("</html>");

?>