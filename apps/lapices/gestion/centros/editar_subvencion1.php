<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_admin.php");

$formulario_name="centros";
$formulario_accion="editar_centro2.php";

$id_curso_centro=$_GET["id_curso_centro"];
if (strlen($id_curso_centro)==0) {
        $id_curso_centro=$_POST["id_curso_centro"];
        }
$id_centro=$_GET["id_centro"];
if (strlen($id_centro)==0) {
        $id_centro=$_POST["id_centro"];
        }
$id_curso=$_GET["id_curso"];
if (strlen($id_curso)==0) {
        $id_curso=$_POST["id_curso"];
        }

$url_retorno="listado_centros.php";

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

                $tope_mes_01=$_POST["tope_mes_01"];
                $tope_mes_12=$_POST["tope_mes_12"];
                $tope_mes_23=$_POST["tope_mes_23"];
                $precio_coste_01=$_POST["precio_coste_01"];
                $precio_coste_12=$_POST["precio_coste_12"];
                $precio_coste_23=$_POST["precio_coste_23"];
                $tope_maximo2=$_POST["tope_maximo2"];
                $subvencion2=$_POST["subvencion2"];
                $alumnos_ciclo2=$_POST["alumnos_ciclo2"];

                        if (strlen($id_curso_centro)!=0) {
                        // Guarda cambios
                        db_query("UPDATE centros_cursos SET id_centro='$id_centro', id_curso='$id_curso', tope_mes_01='$tope_mes_01',tope_mes_12='$tope_mes_12',tope_mes_23='$tope_mes_23', precio_coste_01='$precio_coste_01',precio_coste_12='$precio_coste_12',precio_coste_23='$precio_coste_23',tope_maximo2='$tope_maximo2',subvencion2='$subvencion2',alumnos_ciclo2='$alumnos_ciclo2' WHERE id_curso_centro=$id_curso_centro;") or die(db_error());
                        } else {
                        // Introduce nuevo centro
                        db_query("INSERT INTO centros_cursos (id_centro,id_curso) VALUES ('$id_centro', '$id_curso');") or die(db_error());
                        $id_curso_centro=db_insert_id();
                        }
                        db_query("UPDATE centros_cursos SET tope_mes_01='$tope_mes_01',tope_mes_12='$tope_mes_12',tope_mes_23='$tope_mes_23',precio_coste_01='$precio_coste_01',precio_coste_12='$precio_coste_12',precio_coste_23='$precio_coste_23',tope_maximo2='$tope_maximo2',subvencion2='$subvencion2',alumnos_ciclo2='$alumnos_ciclo2' WHERE id_curso_centro=$id_curso_centro;") or die(db_error());
                        }
        

?>
<html>
<head>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>
