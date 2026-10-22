<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/parte_superior_ancho.php");

require("../prl/config/config_personal.php");

$filtro_empleados=$_GET["filtro_empleados"];
if (strlen($filtro_empleados)==0) {
        $filtro_empleados=0;
        }


$fecha=strftime("%m/%d/%Y");
$ancho_tabla=$ancho_pagina_total-185
?>
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="90%" id="AutoNumber2">
                <form name="personal" action="<?=$_SERVER["PHP_SELF"];?>" method="GET">
                <tr>
                        <td width="100%" valign="top" bgcolor="<%= $fondo_at%>" align="center">
                            <font size="3" color="#000000"><b>Personal de la escuela<br>
                            Mostrar: <select name="filtro_empleados" style="font-size: 10px" onchange="document.personal.submit()">
                            <option value="0">Personal actual (con contrato en vigor o próxima incorporación).</option>
                            <option value="1"<?php  if ($_GET["filtro_empleados"]==1) { print " selected"; } ?>>Personal que ya no trabaja en la escuela o sin contrato.</option>
                            <option value="2"<?php  if ($_GET["filtro_empleados"]==2) { print " selected"; } ?>>Todos</option>
                            </select></font></b>
                        </td>
                </tr>
                </form>
    </table><br>
    <input type="button" onclick="window.location.href='/facturacion/gestion_educadoras.php'" value="Salir" style="font-size: 10px">
<?php 

if (($nivel_acceso==4) and ($ip_segura==true)) {

?>
<input type="button" onclick="window.location.href='intro_pagas.php'" value="Introducir nóminas del personal" style="font-size: 10px"> <input type="button" onclick="window.location.href='ver_pagas.php'" value="Listado nóminas del personal" style="font-size: 10px"> <input type="button" onclick="window.open('listado_docentes_exportar.php?filtro_empleados=<?=$filtro_empleados?>');" value="Exportar Listado" style="font-size: 10px"><br><br>
<?php 

}

?>
<table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="90%">
<?php 

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);
 
?>
  <tr bgcolor="<?=  $fondo_at; ?>">
        <td colspan=12>
        <b><font color="<?=  $color_fuente; ?>">Listado de personal de la escuela <i><?=$nombre_centro_actual;?></i></b>
        </td>
  </tr>
<?php 

  $consulta_usuarios=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=? OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1', 's', array($centro_actual)) or die(db_error());

  mt_srand((float)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
  
  $codigo=$rsslc["id_usuario"];
    
  // Comprueba si tiene contrato en vigor
  $fecha_control=date("Y-m-d");
  $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());

  if (db_num_rows($busca_contratos)!=0) {
        $tipo_filtro_usuario=0;
        } else {
        $tipo_filtro_usuario=1;
        }
  
  if (($tipo_filtro_usuario==$filtro_empleados) or ($filtro_empleados==2)) {
  
  
  if ($id_tipo_listado!=$rsslc["nivel_acceso"]) {
  ?>
  <tr bgcolor="#dfdfdf">
    <td colspan=12>
        <b><font color="<?=  $color_fuente; ?>"><?=$tipo_usuario[$rsslc["nivel_acceso"]];?></b>
    </td>
    </tr>
    <tr bgcolor="#dfdfdf">
        <td><small><small><b><font color="<?=  $color_fuente; ?>">ID</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Nombre</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">DNI</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">NSS</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Domicilio</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Teléfonos<small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Email<small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Fecha nacimiento</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Inicio contrato</b></small></small></td>
        <td><small><small><b><font color="<?=  $color_fuente; ?>">Fin contrato</b></small></small></td>
        <td width=25%><small><small><b><font color="<?=  $color_fuente; ?>">Observaciones</b></small></small></td>
        <td align="center"><small><small><b><font color="<?=  $color_fuente; ?>">Acción</b></small></small></td>
  </tr>
  <?php 
  $id_tipo_listado=$rsslc["nivel_acceso"];
        }
  
    $nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
    
    $fecha_finalizacion="";
    
    // Busca el último contrato
    $observaciones="";
    
    $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? ORDER BY fecha_inicio DESC', 's', array($codigo));
    
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
    // Busca documentación PRL requerida

    $busca_doc_prl=db_query("SELECT * FROM personal_tipos_doc_prevencion WHERE requerido=1 ORDER BY nombre_tipo_doc");

        $no_esta_toda=false;

        while($doc_prl=db_fetch_array($busca_doc_prl)) {

                                $esta_creado=false;

                                // Comprueba si ya existe la carpeta de documentos
                                $carpeta_documentos_actual=$carpeta_documentos."//usuario_$codigo";

                                if (!file_exists($carpeta_documentos_actual)) {
                                        @mkdir($carpeta_documentos_actual) ;
                                        }

                                // Comprueba si hay documento es la carpeta

                                $nombre_doc=$doc_prl["nombre_doc"];

                                if (strlen($nombre_doc)!=0) {

                                        $d = dir($carpeta_documentos_actual);
                                        $nombre_doc1=$nombre_doc."_".$codigo."_".$datos_contratos["id_contrato"];
                                        while($s=$d->read()) {
                                                if (substr($s,0,strlen($nombre_doc1))==$nombre_doc1) {
                                                        $esta_creado=true;
                                                        }
                                                }
                                        $d->close();


                                        if ($esta_creado==false) {
                                                $no_esta_toda=true;
                                                }


                                        }

                }
        
    if ($no_esta_toda==true) {
                        $color_fila="#ff0000";
                        $observaciones=$observaciones."- Falta entregar los documentos requeridos de prevención de riesgos laborales.<br>";
                        } else {
                        $color_fila="#000000";
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

    ?>
    <tr valign="top" bgcolor="#ffffff">
    <td><font size=1><?=$codigo?></font></td>
    <td><font size=1><?=ucwords(strtolower($nombre_ins))?></font></td>
    <td><font size=1><?=$rsslc["DNI"];?></font></td>
    <td><font size=1><?=$rsslc["NSS"];?></font></td>
    <td><font size=1><?=$rsslc["Direccion"];?><br><?=$rsslc["cp"];?> <?=$rsslc["Poblacion"];?></font></td>
    <td><font size=1><?=$t;?></font></td>
    <td><font size=1><?=$rsslc["email"];?></font></td>
    <td><font size=1><?=date("d/m/Y",strtotime($rsslc["fecha_nacimiento"]));?></font></td>
    <td><font size=1><?=$fecha_inicio;?></font></td>
    <td><font size=1><?=$fecha_finalizacion;?></font></td>
    <td><font size=1><?=$observaciones;?></font></td>
    <td align='center'><font size=2><a href='calendario_mes.php?&id_docente=<?=$codigo;?>'>Ver calendario</a> | <a href='horario_docente.php?&id_docente=<?=$codigo;?>'>Editar Contratos</a> | <a href='javascript:abrir_ficha(<?=$codigo?>)'>Editar datos personales</a> | <a href='/gestion/prl/ficha.php?&id_usu=<?=$codigo;?>'><font color="<?=$color_fila?>">Documentación PRL</font></a></font></td>
    </tr>
    <?php 
    }
   }

  if ($contador==0)
  {
?>
  <tr bgcolor="<?= $fondo_ventana; ?>">
        <td colspan=3><small>
        <font face="<?= $fuente_general; ?>" color="<?= $color_fuente; ?>">
        Actualmente no hay ningún docente registrado.
        </font>
        </small>
        </td>
  </tr>        
        <?php  
  } 

db_close();
?>
</table><br>
</center></div></div>
</font>
</small>
</small>
<script>

function abrir_ficha(id_usuario) {

	ventana_nueva=window.open("/gestion/usuarios/datos_cand.php?dato=s&id_usu="+id_usuario,"Datos_usuario_2","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,menubar=no,width=625,height=485,titlebar=0,minimize=no,maximize=no");

}

</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>

