<?php
ini_set("display_errors", getenv("APP_DEBUG") === "1" ? "1" : "0");
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

require("../configuracion/config.php"); 	

if (empty($_SESSION['permitido_padre'])) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}

   if ($errores=="") {
           
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario");
    while($padres=db_fetch_array($busca_padres)){
            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
            }
    if ($filtro_padres!="") {
            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
            }      
      
                  
    // Busca mensajes 
    
	$filtro_busqueda="";
	
	$busqueda=$_GET["q"];
	$tipo_b="OR";
	
	if ($busqueda) {

        // Desglosa una frase en palabras sueltas

          $palabra=explode(" ",$busqueda);

          $query="(";
          for ($cont=0; $cont<count($palabra); $cont++)
                {
                $query=$query."((agenda_notas_padres.Asunto like '%".$palabra[$cont]."%')";
                $query=$query." OR (agenda_notas_padres.texto_nota like '%".$palabra[$cont]."%')";
                $query=$query.") ".$tipo_b." ";

                $palabra_actual=$palabra_actual+1;
                }
            
          if ($tipo_b=="OR") { $cacho=4; }
          if ($tipo_b=="AND") { $cacho=5; }
          $query=substr($query,0,(strlen($query)-$cacho));
          $query=$query.")";

		$filtro_busqueda=" AND $query";
		
	}      
           
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $subtipos_mensajes=array(" a la educadora"," a la coordinadora");
        $estados_mensajes=array("<span class='text-danger'>No leido</span>","Leido","Cerrado");
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda ORDER BY fecha DESC, hora DESC");              
    
    $resultado_total=db_num_rows($busca_mensajes);
    $inicio_listado=$_GET["i"];
    $numero_resultados=10;
    if (strlen($inicio_listado)==0) {
    	$inicio_listado=0;
    }       
       
    $nombe_script="mensajeria";   
        
    $busca_ultimos_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda ORDER BY fecha DESC, hora DESC LIMIT $inicio_listado,$numero_resultados"); 
    
    $total_resultado_busqueda=db_num_rows($busca_ultimos_mensajes);
     
    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres)$filtro_busqueda AND tipo_nota=2 AND ((fecha>='2018-01-31' AND leido_por NOT like '%[".$id_usuario."]%') OR (fecha<'2018-01-31' AND estado=0)) ORDER BY fecha DESC"); 
    
    $texto_no_leidos="";
    if (db_num_rows($busca_no_leidos)!=0) {
            if (db_num_rows($busca_no_leidos)) {
            $coletilla_leido="";
            if (db_num_rows($busca_no_leidos)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $texto_no_leidos=" (<font color='#f00'>".db_num_rows($busca_no_leidos)." no leido$coletilla_leido</font>)";
            }
               
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper familias-messages">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Mensajes 
        <small><?=$texto_no_leidos?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/app/index.php"><i class="fa fa-home"></i> Inicio</a></li>
        <li class="active"><i class="fa fa-envelope"></i> Mensajes</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php
if ((isMobile()) and ($comprobarMovil==true)) {
?>
<div class="alert alert-warning alert-dismissible" role="alert" id="alertaMoviles">
	<div class="row">
		<div class="col-xs-10">
			<img src="/app/imagenes/rotar_movil_blanco.png" width="48" border=0 class="pull-right">
			<strong>¿Estás usando un movil?</strong><br>Esta tabla se ve mejor con el movil en posición horizontal...	
		</div>
		<div class="col-xs-2">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		</div>
	</div>    
</div>
<?php	
}	
?>   
          <div class="box box-primary">
            <div class="box-header with-border">
              <form class="form-inline" method="get" action="<?=$nombe_script?>.php">
                  <div class="form-group">
                    <input type="text" class="form-control" id="q" name="q" placeholder="Buscar..." value="<?=$busqueda?>">
                  </div>	
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button> <button class="btn btn-primary" type="button" onclick="window.location.href='nuevo_mensaje.php'"><i class="fa fa-envelope"></i> Nuevo mensaje</button>
                </form>              
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div>
                <table class="table">
                  <tbody>	                        
<?php     
                  
    if (db_num_rows($busca_mensajes)==0) {
            ?>
           <tr bgcolor="#ffffff">
              <td align="center"><b>No hay mensajes</b></td>
            </tr>
            <?php 
            }
    while($mensajes=db_fetch_array($busca_mensajes)) {
    
      // Busca remitente
      $busca_remitente=db_query("SELECT * FROM usuarios WHERE id_usuario=".$mensajes["id_remitente"]);
          
      if (db_num_rows($busca_remitente)!=0) {
              $datos_remitente=db_fetch_array($busca_remitente);
              $nombre_remitente=$datos_remitente["Nombre"];
              } else {
              $nombre_remitente="-";
              }
              
      if ($mensajes["tipo_nota"]!=2) {
              $subtipo_nota=$subtipos_mensajes[$mensajes["destino"]];
              } else {
              $subtipo_nota="";
              }

      // Busca respuestas no leidas

      $busca_respuestas=db_query("SELECT * FROM agenda_notas_padres_respuestas WHERE direccion=1 AND id_nota=".$mensajes["id_nota_padre"]." AND respondido_por<>$id_usuario AND ((fecha_respuesta>='2018-01-31' AND leido_por NOT like '%[".$id_usuario."]%') OR (leida=0 AND fecha_respuesta<'2018-01-31'))");

      if (db_num_rows($busca_respuestas)!=0) {
              if (db_num_rows($busca_respuestas)) {
              $coletilla_leido="";
              if (db_num_rows($busca_respuestas)>1) {
                      $coletilla_leido="s";
                      }
                      }
              $estado_mensaje="<font color='#f00'>".db_num_rows($busca_respuestas)." respuesta$coletilla_leido nueva$coletilla_leido</font>";
              } else {
            // Comprueba si leido
            if ($mensajes["fecha"]<'2018-01-31') {
            $estado_mensaje=$mensajes["estado"];   
            } else {
              $pos = strpos($mensajes["leido_por"], "[".$id_usuario."]");
              if ($pos === false) {
                $estado_mensaje=0;
              } else {
              $estado_mensaje=1;   
              }		        
            }	        
            if ($mensajes["tipo_nota"]!=2) {
              $estado_mensaje=$mensajes["estado"];
          }            
              $estado_mensaje=$estados_mensajes[$estado_mensaje];
              
              }
    
    ?>
                  <tr>
                    <td>
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h3 class="panel-title">Asunto: <a href="ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>"><b><?=$mensajes["asunto"];?></b></a></h3>                          
                        </div>
                        <div class="panel-body">
                          <b>Tipo mensaje:</b> <?=$tipos_mensajes[$mensajes["tipo_nota"]].$subtipo_nota?><br />
                          <b>De:</b> <a href="ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>"><?=$nombre_remitente?></a><br />
                          <b>Estado:</b> <?=$estado_mensaje;?><br />
                          <div class="familias-message-actions"><span class="familias-message-date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?=date("d/m/Y",strtotime($mensajes["fecha"]));?> <?=date("H:i",strtotime($mensajes["hora"]));?></span><button type="button" class="btn btn-responsive btn-default btn-sm familias-message-open" onclick="window.location.href='ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>'"><i class="fa fa-folder-open" aria-hidden="true"></i> Abrir mensaje</button></div>
                        </div>
                      </div>
                    </td>
                  </tr>                                 
    <?php 
            }
    }
    ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
          </div>
	  
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>