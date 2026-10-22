<?php
require("../../../configuracion/parte_superior_privado_ancho.php");
?>
<div class="container">
    <form method="GET" id="listados" name="listados" action="listado_contactos.php" target="_blank">
        <h1>Exportar contactos</h1>
        <div class="row">
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="curso">Curso</label>
                    <select class="form-control" id="id_curso" name="id_curso">
                        <option value="">Todos</option>
                        <option value="aa">Antiguos alumnos</option>
                        <?php
                        $buscaCursos = db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio DESC;");
                        while($cursos=db_fetch_array($buscaCursos)) {
                        ?>
                        <option value="<?=$cursos["id_curso"]?>"><?=$cursos["nombre"]?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>            
                <div class="form-group">
                    <label for="id_periodo">Trimestre</label>
                    <select class="form-control" id="id_trimestre" name="id_trimestre">
                        <option value="">Todos</option>
                        <?php
                        $buscaCursosTrimestres = db_query("SELECT aulas_grupos_trimestres.* FROM aulas_grupos_trimestres ORDER BY aulas_grupos_trimestres.fecha_inicio DESC;");
                        while($trimestres=db_fetch_array($buscaCursosTrimestres)) {
                        ?>
                        <option value="<?=$trimestres["id_trimestre"]?>"><?=$trimestres["nombre_trimestre"]?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>                      
            </div>
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="ciclo">Ciclo formativo</label>
                    <select class="form-control" id="ciclo" name="ciclo">
                        <option value="">Todos</option>
                        <option value="1">Educación Infantil de Primer ciclo</option>
                        <option value="2">Educación Infantil de Segundo ciclo</option>
                    </select>  
                </div>              
            </div>
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="id_centro">Centro</label>
                    <select class="form-control" id="id_centro" name="id_centro">
                        <option value="">Todos</option>
                    <?php
                        $buscaCentros = db_query("SELECT * FROM centros WHERE codigo_centro<>'' ORDER BY nombre_centro DESC;");
                        while($centros=db_fetch_array($buscaCentros)) {
                        ?>
                        <option value="<?=$centros["id_centro"]?>"><?=$centros["nombre_centro"]?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>                                               
        </div>
        <div class="row">
        <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="origendatos">Orgigen de los datos</label>
                    <select class="form-control" id="origendatos" name="origendatos">
                        <option value="alumnos">Alumnos</option>
                        <option value="contactos">Contactos</option>
                    </select>
                </div>                
            </div>            
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="fechanac">Nacidos entre el año...</label>
                    <select class="form-control" id="fechanac" name="fechanac">
                        <option value="">...</option>
                        <?php
                        for ($cDate=date("Y");$cDate>2000;$cDate--) {
                        ?>
                        <option value="<?=$cDate?>"><?=$cDate?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>                
            </div>
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label for="fechafin">...hasta el año</label>
                    <select class="form-control" id="fechafin" name="fechafin">
                        <option value="">...</option>
                        <?php
                        for ($cDate=date("Y");$cDate>2000;$cDate--) {
                        ?>
                        <option value="<?=$cDate?>"><?=$cDate?></option>
                        <?php
                        }
                        ?>
                    </select>  
                </div>              
            </div>
        </div>
        <div class="row"
            <div class="col-md-12 text-left">
                <div class="form-group">
                    <label for="id_centro">¿Incluir bebes que no han nacido todavía? (madres embarazadas que ya han reservado la plaza)</label>
                    <input type="radio" value="1" checked=checked name="incluirnonatos"> Si <input type="radio" value="0" name="incluirnonatos"> No
                    </select>
                </div>
            </div>                                               
        </div>
        <button class="btn btn-primary" type="button" onclick="emails()"><i class="fa fa-envelope" aria-hidden="true"></i> Descargar listado de emails</button>
        <button class="btn btn-success" type="button" onclick="moviles()"><i class="fa fa-mobile" aria-hidden="true"></i> Descargar listado de móviles</button><br />     
        <button class="btn btn-info" type="button" onclick="padres()" style="margin-top: 5px;"><i class="fa fa-address-book" aria-hidden="true"></i> Descargar listado con datos padres</button>   
        <button class="btn btn-warning" type="button" onclick="alumnos()" style="margin-top: 5px;"><i class="fa fa-address-book" aria-hidden="true"></i> Descargar listado de alumnos</button>           
    </form>
</div>
<script>

function emails() {
    document.listados.action = 'listado_contactos.php';
    document.listados.submit();
}

function moviles() {
    document.listados.action = 'listado_contactos_telefonos.php';
    document.listados.submit();    
}

function padres() {
    document.listados.action = 'listado_contactos_padres.php';
    document.listados.submit();    
}

function alumnos() {
    document.listados.action = 'listado_alumnos.php';
    document.listados.submit();    
}

</script>
<?php
require("../../../configuracion/parte_inferior.php");
?>