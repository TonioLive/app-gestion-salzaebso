<?php
require("../configuracion/config.php"); 	

if ($_SESSION['permitido_padre']!=true) {
	
	header("Location: login.php");exit;	
	
} else {

	require("parte_superior_padres.php");

}
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Titulo...
        <small>Texto texto texto del encabezado....</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="/app"><i class="fa fa-home"></i> Inicio</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	  <!-- / AQUÍ EL CONTENIDO -->
      <!-- / FINAL CONTENIDO -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php

require("parte_inferior_padres.php");
	
?>