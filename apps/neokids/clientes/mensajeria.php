<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");	
	
} else {

	require("parte_superior_padres.php");

}

   if ($errores=="") {
           
    // Busca padres  
    $filtro_padres="";  
    $busca_padres=db_query_params('SELECT * FROM padres WHERE id_usuario=?', 's', array($id_usuario));
    while($padres=db_fetch_array($busca_padres)){
            $filtro_padres.=" OR id_alumno=".$padres["padre_de"];
            }
    if ($filtro_padres!="") {
            $filtro_padres=substr($filtro_padres,4,(strlen($filtro_padres)-4));
            }      
      
                  
    // Busca mensajes
        $tipos_mensajes=array("Enviado","Medicación","Recibido");
        $subtipos_mensajes=array(" a la monitora"," a la coordinadora de Neokids");
        $estados_mensajes=array("<span class='text-danger'>No leido</span>","<span class='text-primary'>Leido</span>","<span class='text-primary'>Cerrado</span>");
    $busca_mensajes=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) ORDER BY fecha DESC, hora DESC");              
     
    $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE ($filtro_padres) AND tipo_nota=2 AND estado=0 ORDER BY fecha DESC"); 
    $texto_no_leidos="";
    if (db_num_rows($busca_no_leidos)!=0) {
            if (db_num_rows($busca_no_leidos)) {
            $coletilla_leido="";
            if (db_num_rows($busca_no_leidos)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $texto_no_leidos=" (".db_num_rows($busca_no_leidos)." no leido$coletilla_leido)";
            }
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Mensajes 
        <small><?=$texto_no_leidos?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/clientes"><i class="fa fa-home"></i> Inicio</a></li>
        <li class="active"><i class="fa fa-envelope"></i> Mensajes</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
<?php
if(isMobile()) {
?>
<div class="alert alert-warning alert-dismissible" role="alert" id="alertaMoviles">
	<div class="row">
		<div class="col-xs-10">
			<img src="/clientes/imagenes/rotar_movil_blanco.png" width="48" border=0 class="pull-right">
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
              <h3 class="box-title">&nbsp;</h3>
              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <button class="btn btn-responsive btn-primary btn-block margin-bottom" onclick="window.location.href='nuevo_mensaje.php'"><i class="fa fa-envelope"></i> Nuevo mensaje</button>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                  <tbody>	                        
	                 <tr>
				         <td>Tipo</td>
				         <td>De</td>
				         <td>Asunto</td>				         
				         <td>Estado</td>
				         <td>Fecha</td>
				         <td>&nbsp;</td>
			         </tr>
<?php     
                  
    if (db_num_rows($busca_mensajes)==0) {
            ?>
           <tr bgcolor="#ffffff">
         <td align="center" colspan=6><b>No hay mensajes</b></td>
         </tr>
            <?php 
            }
    while($mensajes=db_fetch_array($busca_mensajes)) {
    
    // Busca remitente
    $busca_remitente=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($mensajes["id_remitente"]));
    
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
    
    $busca_respuestas=db_query_params('SELECT * FROM agenda_notas_padres_respuestas WHERE leida=0 AND id_nota=? AND respondido_por<>?', 'ss', array($mensajes["id_nota_padre"], $id_usuario));
    
    if (db_num_rows($busca_respuestas)!=0) {
            if (db_num_rows($busca_respuestas)) {
            $coletilla_leido="";
            if (db_num_rows($busca_respuestas)>1) {
                    $coletilla_leido="s";
                    }
                    }
            $estado_mensaje="<span class='text-danger'>".db_num_rows($busca_respuestas)." respuesta$coletilla_leido nueva$coletilla_leido</span>";
            } else {
            $estado_mensaje=$estados_mensajes[$mensajes["estado"]];
            }
    
    ?>
                  <tr>
                    <td><?=$tipos_mensajes[$mensajes["tipo_nota"]].$subtipo_nota?></td>
                    <td class="mailbox-name"><a href="ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>"><?=$nombre_remitente?></a></td>
                    <td class="mailbox-subject"><a href="ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>"><b><?=$mensajes["asunto"];?></b></a></td>
                    <td class="mailbox-attachment"><?=$estado_mensaje;?></td>
                    <td class="mailbox-date"><?=date("d/m/Y",strtotime($mensajes["fecha"]));?> <?=date("H:i",strtotime($mensajes["hora"]));?></td>
                    <td align="center"><button class="btn btn-responsive btn-default btn-sm" onclick="window.location.href='ver_mensaje.php?id_mensaje=<?=$mensajes["id_nota_padre"]?>'"><i class="fa fa-folder-open"></i> Abrir</button></b></td>
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