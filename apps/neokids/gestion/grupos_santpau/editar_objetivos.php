<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];

$cargar_plantilla=$_GET["cargar_plantilla"];

$busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=? AND id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

$datos_subgrupo=db_fetch_array($busca_datos_subgrupo);


$filtro_niveles="nivel is not null";

// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

if ($cargar_plantilla==1) {

                // Comprueba si hay plantilla
                $busca_plantilla=db_query_params('SELECT * FROM agenda_programacion_externos WHERE id_grupo=? AND id_subgrupo=? AND fecha=\'0000-00-00\'', 'ss', array($id_grupo, $id_subgrupo));
        
                while($datos_plantilla=db_fetch_array($busca_plantilla)) {
        
                        // Comprueba si esté está
                        
                        $busca_si_esta=db_query_params('SELECT * FROM agenda_programacion_externos WHERE id_grupo=? AND id_subgrupo=? AND fecha=? AND id_actividad=?', 'ssss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha)), $datos_plantilla['id_actividad']));
                        
                        if (db_num_rows($busca_si_esta)==0) {
                                $inserta=db_query_params('INSERT INTO agenda_programacion_externos (id_grupo,id_subgrupo,fecha,id_actividad) VALUES (?,?,?,?);', 'ssss', array((string)($id_grupo), (string)($id_subgrupo), (string)(date("Y-m-d", $fecha)), (string)($datos_plantilla['id_actividad'])));                        
                                }
                                        
                }

}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Edición de objetivos y actividades</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/bootstrap-duallistbox.css" type="text/css">
<link rel="stylesheet" href="/src/chosen.css">

<style>

.objetivo_general {
    font-weight: bold;
}

.objetivo_especifico {
	font-weight: bold;
	font-style: oblique;
}

.actividad {

}

</style>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/chosen.jquery.js"></script>
<script type="text/javascript">

$( document ).ready(function() {
    
        $('.chosen-select').chosen({
                no_results_text: "Sin resultados para",
                include_group_label_in_selected: true
        }); 
            
        $("#id_actividad").change(function(){
            $('#texto_editado').val($("#id_actividad option:selected").html());
        });
                
<?php 
$mensaje_error=$_GET["mensaje_error"];

if ($mensaje_error) {
?>
        alert("<?=$mensaje_error?>");
<?php 
}

?>              
    
});

function guardar() {

        if (confirm("Al guardar modificarás el nombre del objetivo o actividad seleccionado, esto afectará a la programación ya existente.\n\n¿Estás seguro/a?")) {
                document.monitores.submit();
        }

}

function eliminar() {

        id_actividad = $("#id_actividad").val();

        if (id_actividad!=null) {
        
                if (confirm("Se va a eliminar el objetivo o actividad seleccionada.\n\n¿Estás seguro/a?")) {
                
                        document.monitores.action="eliminar_actividades.php";        
                        document.monitores.submit();
                
                }        
        
        } else {
        
                alert("Selecciona el objetivo o actividad que deseas eliminar.");
        
        }
        
}

function nuevo_objetivo() {

        // Comprueba si el formulario está completo
        objetivo_nivel = $("#objetivo_nivel").val();
        objetivo_nuevo = $("#objetivo_nuevo").val();

        if ((objetivo_nivel!="")&&(objetivo_nuevo!="")) {

                document.monitores.action="nuevo_objetivo.php";        
                document.monitores.submit();
        } else {
        
                alert("Debes rellenar los datos del nuevo objetivo (nivel y descripción).");
        
        }

}

function nueva_actividad() {

        // Comprueba si el formulario está completo
        actividad_nueva = $("#actividad_nueva").val();
        objetivo_nueva = $("#objetivo_nueva").val();

        if ((actividad_nueva!="")&&(objetivo_nueva!="")) {
        
                document.monitores.action="nueva_actividad.php";
                document.monitores.submit();
        
        } else {
        
                alert("Debes rellenar los datos de la nueva actividad (Objetivo y descripción).");
        
        }

}

</script>
</head>
<body>
<div class="container-fluid">
<div class="panel panel-default">
  <div class="panel-heading"><h3>Edición de objetivos y actividades</h3></div>
  <div class="panel-body">
    <form method="post" action="guarda_actividades.php" name="monitores">
    <input type="hidden" value="0" id="nueva_act" name="nueva_act">
    <input type="hidden" value="<?=$id_grupo?>" name="id_grupo">
    <input type="hidden" value="<?=$id_subgrupo?>" name="id_subgrupo">
    <input type="hidden" value="<?=$_GET["nombre_mostrar"];?>" name="nombre_mostrar"> 
    <input type="hidden" value="<?=$fecha?>" name="fecha">  
          <h4>Actividades</h4>
          <select data-placeholder="Buscar..." name="id_actividad" id="id_actividad" class="chosen-select" tabindex=2 style="width:100%;">
                        <option value=""></option>
                <?php  
                
                $objetivo_actual=0;
                $nivel_actual=-1;                
                        
                $busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=0 ORDER BY id_objetivo");
                
                while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) { 
                 
                $objetivo_general_mostrado=false;                            
                      
                $busca_actividades=db_query("SELECT *, agenda_programacion_objetivos_piscina.id_objetivo AS id_obje FROM agenda_programacion_objetivos_piscina LEFT JOIN agenda_programacion_actividades_piscina ON agenda_programacion_actividades_piscina.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo WHERE ($filtro_niveles) AND agenda_programacion_objetivos_piscina.id_objetivo_g=".$objetivos_generales["id_objetivo"]."   ORDER BY agenda_programacion_objetivos_piscina.nivel, agenda_programacion_objetivos_piscina.id_objetivo, agenda_programacion_objetivos_piscina.nombre_objetivo, agenda_programacion_actividades_piscina.id_actividad") or die(db_error());               
                
                while($actividades=db_fetch_array($busca_actividades)) {
                
                        if ($actividades["nivel"]!=$nivel_actual) {
                        
                                if ($actividades["nivel"]!=-1) {
                                        ?>
                                        </optgroup>
                                        <?php 
                                }
                                ?>
                                        <optgroup label="Nivel <?=$actividades["nivel"]?>">
                                <?php 
                                $nivel_actual=$actividades["nivel"];
                        }
                
                		if ($objetivo_general_mostrado==false) {
                				?>
                				<option value="O<?=$objetivos_generales["id_objetivo"];?>" class="text-default objetivo_general"><?=$objetivos_generales["id_objetivo"];?>. <?=$objetivos_generales["nombre_objetivo"];?></option>
                				<?php   
                				$objetivo_general_mostrado=true;
                				}               
                
                        if ($actividades["id_obje"]!=$objetivo_actual) {
                                ?>
                                <option value="O<?=$actividades["id_obje"]?>" class="text-warning objetivo_especifico"><?=$actividades["nombre_objetivo"];?></option>
                                <?php 
                                $objetivo_actual=$actividades["id_obje"];
                        }                        
                        
                ?>
                <option class="<?=$colores_niveles[$actividades["nivel"]]?> actividad" value="<?=$actividades["id_actividad"]?>"<?php  if (db_num_rows($busca_si_esta)!=0) { echo(" selected"); } ?>><?=$actividades["descripcion_actividad"];?></option>
                <?php 
        }
        
        }
        ?>
        </optgroup>      
      </select>     
    <br />
    <input type="text" name="texto_editado" id="texto_editado" placeholder="Edita el objetivo o actividad aquí..." class="form-control">
    <br />
    <h4>Nuevo objetivo</h4>
    <div class="row">
                  <div class="col-md-5">
                            <select data-placeholder="Nivel" name="objetivo_nivel" id="objetivo_nivel" tabindex=3 class="form-control">
                                          <option>Selecciona el nivel</option>
                                          <?php 
                                          for ($n=0;$n<=3;$n++) {
                                          ?>
                                        <option value="<?=$n?>"class="<?=$colores_niveles[$n]?>"><?=$n?></option>
                                        <?php 
                                }
                                        ?>
                        </select>
                  </div>
                  <div class="col-md-5">
                        <input type="text" name="objetivo_nuevo" id="objetivo_nuevo" placeholder="Descripción nuevo objetivo" class="form-control">
                  </div>
                  <div class="col-md-2">
                  <button type="button" class="btn btn-default" onclick="nuevo_objetivo();">Añadir objetivo</button>
                  </div>
        </div>
    <h4>Nueva actividad</h4>
    <div class="row">
                  <div class="col-md-5">
                            <select data-placeholder="Objetivo" name="objetivo_nueva" id="objetivo_nueva" tabindex=2 class="form-control">
                                          <option>Selecciona el objetivo de la nueva actividad</option>
                                        <?php 
                                        $busca_objetivos=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE ($filtro_niveles) AND id_objetivo_g!=0 ORDER BY agenda_programacion_objetivos_piscina.nivel, agenda_programacion_objetivos_piscina.id_objetivo");
                 
                
                                        while($objetivos=db_fetch_array($busca_objetivos)) {
                                        
                                                // Busca si tienen actividades
                                                $color_fila="";
                                                $busca_a=db_query_params('SELECT * FROM agenda_programacion_actividades_piscina WHERE id_objetivo=?', 's', array($objetivos["id_objetivo"]));
                                                
                                                if (db_num_rows($busca_a)==0) {
                                                        $color_fila=" class='text-danger'";
                                                }
                                                
                                        ?>
                                        <option value="<?=$objetivos["id_objetivo"]?>"<?=$color_fila?>><?=$objetivos["nombre_objetivo"];?></option>
                                        <?php 
                                }
                                        ?>
                        </select>
                  </div>
                  <div class="col-md-5">
                        <input type="text" name="actividad_nueva" id="actividad_nueva" placeholder="Descripción nueva actividad" class="form-control">
                  </div>
                  <div class="col-md-2">
                  <button type="button" class="btn btn-default" onclick="nueva_actividad();">Añadir actividad</button>
                  </div>
        </div>        
        <br />
        <a type="button" href="gestion_programacion.php?fecha=<?=$_GET["fecha"];?>&id_grupo=<?=$_GET["id_grupo"];?>&id_subgrupo=<?=$_GET["id_subgrupo"];?>&nombre_mostrar=<?=$_GET["nombre_mostrar"];?>" class="btn btn-danger">Salir</a> <button type="button" onclick="guardar();" class="btn btn-success">Guardar cambios</button> <button type="button" onclick="eliminar();" class="btn btn-warning">Eliminar seleccionada</button>
    </form> 
        <br />
        <br />              
</div>
</div>         
</body>
</html>
