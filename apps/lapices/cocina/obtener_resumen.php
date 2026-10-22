<?php
require("../configuracion/parte_superior_privado_ancho_bootstrap.php");
?>
<div class="container">
    <form method="GET" id="listados" name="listados" action="listado_preparacion_platos_curso.php" target="_blank">
        <input type="hidden" id="fecha_ini" name="fecha_ini">
        <input type="hidden" id="fecha_fin" name="fecha_fin">
        <h1>Obtener resumen de menús servidos entre dos fechas</h1>
        <div class="row">
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="curso">Curso</label>
                    <select class="form-control" id="id_curso_informe" name="id_curso_informe">
                        <?php
                        $buscaCursos = db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<'".date("Y-m-d")."' ORDER BY fecha_inicio DESC;");
                        while($cursos=db_fetch_array($buscaCursos)) {
                        ?>
                        <option value="<?=$cursos["id_curso"]?>"><?=$cursos["nombre"]?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>                
            </div>           
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="fecha-ini">Entre el día...</label>
                    <input type="text" class="form-control" id="fecha-ini" name="fecha-ini" palceholder="Fecha inicio">
                </div>                
            </div>
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="fecha_fin">y el día...</label>
                    <input type="text" class="form-control" id="fecha-fin" name="fecha-fin" palceholder="Fecha fin">
                </div>              
            </div>
        </div>
        <button class="btn btn-primary" type="submit"> Obtener Informe</button>
        <a class="btn btn-info" type="button" href="/"> Salir</a>             
    </form>
</div>

<script src="/js/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="/js/datepicker/locales/bootstrap-datepicker.es.min.js" charset="UTF-8"></script>
<link href="/js/datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">
<script>

    $('[name^="fecha-"]').datepicker({
        format: "dd/mm/yyyy",
        language: "es"
    });


    document.getElementById("listados").addEventListener("submit", function(event) {
        event.preventDefault(); // Evita el envío hasta validar

        let fechaIniInput = document.getElementById("fecha-ini").value;
        let fechaFinInput = document.getElementById("fecha-fin").value;

        // Validar formato y fechas correctas
        if (!esFechaValida(fechaIniInput) || !esFechaValida(fechaFinInput)) {
            alert("Por favor, introduce fechas válidas en formato dd/mm/aaaa.");
            return;
        }

        let fechaIni = convertirAFecha(fechaIniInput);
        let fechaFin = convertirAFecha(fechaFinInput);

        // Validar que fecha-ini sea anterior a fecha-fin
        if (fechaIni > fechaFin) {
            alert("La fecha de inicio no puede ser posterior a la fecha de fin.");
            return;
        }

        // Validar que el periodo no supere 1 año
        let unAnioDespues = new Date(fechaIni);
        unAnioDespues.setFullYear(unAnioDespues.getFullYear() + 1);

        if (fechaFin > unAnioDespues) {
            alert("El periodo seleccionado no puede ser superior a 1 año.");
            return;
        }

        // Convertir fechas al formato yyyy-mm-dd y asignarlas a los inputs hidden
        document.getElementById("fecha_ini").value = convertirAFormatoISO(fechaIni);
        document.getElementById("fecha_fin").value = convertirAFormatoISO(fechaFin);

        // Si pasa todas las validaciones, enviar el formulario
        this.submit();
    });

    function esFechaValida(fechaStr) {
        let regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        let match = fechaStr.match(regex);

        if (!match) return false;

        let dia = parseInt(match[1], 10);
        let mes = parseInt(match[2], 10) - 1; // Mes en JavaScript es 0-indexed
        let anio = parseInt(match[3], 10);

        let fecha = new Date(anio, mes, dia);
        return fecha.getFullYear() === anio && fecha.getMonth() === mes && fecha.getDate() === dia;
    }

    function convertirAFecha(fechaStr) {
        let [dia, mes, anio] = fechaStr.split("/").map(num => parseInt(num, 10));
        return new Date(anio, mes - 1, dia);
    }

    function convertirAFormatoISO(fecha) {
        return fecha.toISOString().split("T")[0]; // Extrae la parte yyyy-mm-dd
    }

</script>
<?php
require("../configuracion/parte_inferior.php");
?>