<?php
require('../configuracion/parte_superior_privado_alumnos.php');
require_once dirname(__DIR__) . '/gestion/panel/paginacion.php';

if (empty($permitido)) {
    require('../configuracion/parte_inferior.php');
    return;
}

$cursos = array();
$consulta_cursos = db_query(
    'SELECT id_curso, nombre, fecha_inicio, fecha_fin '
    . 'FROM cursos_academicos ORDER BY fecha_inicio DESC'
);
if ($consulta_cursos) {
    while ($curso = db_fetch_assoc($consulta_cursos)) {
        $cursos[] = $curso;
    }
}

$id_curso = app_request_int($_GET, 'id_curso', 0);
if ($id_curso === 0) {
    foreach ($cursos as $curso) {
        if ($curso['fecha_inicio'] <= date('Y-m-d') && $curso['fecha_fin'] >= date('Y-m-d')) {
            $id_curso = (int) $curso['id_curso'];
            break;
        }
    }
}
if ($id_curso === 0 && !empty($cursos)) {
    $id_curso = (int) $cursos[0]['id_curso'];
}

$curso_seleccionado = null;
foreach ($cursos as $curso) {
    if ((int) $curso['id_curso'] === $id_curso) {
        $curso_seleccionado = $curso;
        break;
    }
}

$alumnos = array();
$total_alumnos = 0;
$modo_impresion = app_request_int($_GET, 'imprimir', 0) === 1;
$por_pagina = app_pagination_per_page($_GET, 'por_pagina');
$pagina = app_pagination_page($_GET, 'pagina');
if ($curso_seleccionado !== null) {
    $consulta_total = db_query_params(
        'SELECT COUNT(*) AS total '
        . 'FROM alumnos a '
        . 'INNER JOIN usuarios u ON u.id_usuario=a.id_usuario '
        . 'WHERE a.id_centro=? AND a.id_curso=? AND u.tipo_inscripcion=1',
        'ii',
        array((int) $centro_actual, $id_curso)
    );
    if ($consulta_total) {
        $fila_total = db_fetch_assoc($consulta_total);
        $total_alumnos = isset($fila_total['total']) ? (int) $fila_total['total'] : 0;
        db_free_result($consulta_total);
    }

    $total_paginas = max(1, (int) ceil($total_alumnos / $por_pagina));
    $pagina = min($pagina, $total_paginas);
    $desplazamiento = ($pagina - 1) * $por_pagina;
    $limite_sql = $modo_impresion ? '' : ' LIMIT ?, ?';
    $tipos_consulta = $modo_impresion ? 'ii' : 'iiii';
    $parametros_consulta = $modo_impresion
        ? array((int) $centro_actual, $id_curso)
        : array((int) $centro_actual, $id_curso, $desplazamiento, $por_pagina);

    $consulta_alumnos = db_query_params(
        'SELECT '
        . 'u.id_usuario, u.Nombre, u.Apellido1, u.Apellido2, u.fecha_nacimiento, '
        . 'a.id_alumno, a.fecha_matricula, a.fecha_baja, a.comedor, a.swiming, '
        . 'ca.nombre AS nombre_aula, '
        . 'CASE WHEN '
        . 'EXISTS ('
        . 'SELECT 1 FROM servicios_contratados sc '
        . 'INNER JOIN tarifa t ON t.ID=sc.id_servicio '
        . 'WHERE sc.id_alumno=a.id_alumno AND t.CATEGORIA=118'
        . ') '
        . 'OR EXISTS ('
        . 'SELECT 1 FROM servicios_contratados_aula sca '
        . 'INNER JOIN tarifa ta ON ta.ID=sca.id_servicio '
        . 'WHERE sca.id_aula=a.aula AND ta.CATEGORIA=118 '
        . 'AND NOT EXISTS ('
        . 'SELECT 1 FROM servicios_no_contratados snc '
        . 'WHERE snc.id_servicio_aula=sca.id_servicio_contratado '
        . 'AND snc.id_usuario=a.id_alumno'
        . ')'
        . ') '
        . 'THEN 1 ELSE a.swiming END AS piscina '
        . 'FROM alumnos a '
        . 'INNER JOIN usuarios u ON u.id_usuario=a.id_usuario '
        . 'LEFT JOIN centro_aulas ca ON ca.id_aula=a.aula '
        . 'WHERE a.id_centro=? AND a.id_curso=? AND u.tipo_inscripcion=1 '
        . 'ORDER BY ca.nombre, u.Apellido1, u.Apellido2, u.Nombre'
        . $limite_sql,
        $tipos_consulta,
        $parametros_consulta
    );

    if ($consulta_alumnos) {
        while ($alumno = db_fetch_assoc($consulta_alumnos)) {
            $alumnos[] = $alumno;
        }
    }
}

function listado_fecha($fecha)
{
    if (empty($fecha) || $fecha === '0000-00-00') {
        return '';
    }

    return date('d/m/Y', strtotime($fecha));
}

function listado_comedor($valor)
{
    $tipos = array(
        0 => 'Sin indicar',
        1 => 'Si',
        2 => 'No',
        3 => 'Regimen',
    );

    return isset($tipos[(int) $valor]) ? $tipos[(int) $valor] : 'Sin indicar';
}
?>
<section class="legacy-module-page legacy-simplified-list<?= $modo_impresion ? ' legacy-simplified-list--print' : ''; ?>" aria-labelledby="listado-simplificado-title">
    <header class="legacy-module-page__heading">
        <div>
            <span class="legacy-module-page__eyebrow">Alumnos</span>
            <h1 id="listado-simplificado-title">Listado simplificado</h1>
            <p>Datos esenciales del alumnado matriculado en el centro y curso seleccionados.</p>
        </div>
        <div class="legacy-module-page__meta">
            <span>Centro</span>
            <strong><?= app_escape($nombre_centro_actual); ?></strong>
        </div>
    </header>

    <form class="legacy-search-panel" method="get" action="/alumnos/listado_simplificado.php">
        <label class="legacy-search-panel__field">
            <span>Curso academico</span>
            <select name="id_curso">
                <?php foreach ($cursos as $curso) { ?>
                    <option value="<?= (int) $curso['id_curso']; ?>"<?= (int) $curso['id_curso'] === $id_curso ? ' selected' : ''; ?>>
                        <?= app_escape($curso['nombre']); ?>
                        (<?= app_escape(listado_fecha($curso['fecha_inicio'])); ?> -
                        <?= app_escape(listado_fecha($curso['fecha_fin'])); ?>)
                    </option>
                <?php } ?>
            </select>
        </label>
        <button type="submit">Mostrar</button>
        <button type="submit" name="imprimir" value="1" formtarget="_blank">Imprimir</button>
    </form>

    <div class="legacy-results-heading">
        <div>
            <strong><?= $curso_seleccionado ? app_escape($curso_seleccionado['nombre']) : 'Sin curso'; ?></strong>
            <span><?= $total_alumnos; ?> alumno(s)</span>
        </div>
        <a class="legacy-card-action" href="/alumnos/gestion_alumnos.php">Volver a alumnos</a>
    </div>

    <?php if (empty($alumnos)) { ?>
        <div class="legacy-empty-state">
            <strong>No hay alumnos en este listado</strong>
            <p>Selecciona otro curso o comprueba el centro activo.</p>
        </div>
    <?php } else { ?>
        <div class="legacy-data-table-wrap legacy-data-table-wrap--cards">
            <table class="legacy-data-table legacy-responsive-list" data-pagination="server">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Aula</th>
                        <th>Fecha de nacimiento</th>
                        <th>Piscina</th>
                        <th>Comedor</th>
                        <th>Inicio de curso</th>
                        <th>Fin de curso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $alumno) { ?>
                        <tr>
                            <td data-label="Nombre" class="legacy-responsive-list__primary">
                                <a class="legacy-student-name" href="/alumnos/nuevo_alumno1.php?u=<?= (int) $alumno['id_usuario']; ?>">
                                    <?= app_escape($alumno['Nombre']); ?>
                                </a>
                            </td>
                            <td data-label="Apellidos"><?= app_escape(trim($alumno['Apellido1'] . ' ' . $alumno['Apellido2'])); ?></td>
                            <td data-label="Aula"><span class="legacy-table-tag"><?= app_escape($alumno['nombre_aula']); ?></span></td>
                            <td data-label="Fecha de nacimiento"><?= app_escape(listado_fecha($alumno['fecha_nacimiento'])); ?></td>
                            <td data-label="Piscina"><?= (int) $alumno['piscina'] === 1 ? 'Si' : 'No'; ?></td>
                            <td data-label="Comedor"><?= app_escape(listado_comedor($alumno['comedor'])); ?></td>
                            <td data-label="Inicio de curso"><?= app_escape(listado_fecha($alumno['fecha_matricula'])); ?></td>
                            <td data-label="Fin de curso"><?= app_escape(listado_fecha($alumno['fecha_baja'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php if (!$modo_impresion) { ?>
            <?php app_render_pagination(
                $total_alumnos,
                $pagina,
                $por_pagina,
                array(
                    'page_key' => 'pagina',
                    'per_page_key' => 'por_pagina',
                    'label' => 'alumnos',
                )
            ); ?>
        <?php } ?>
    <?php } ?>
</section>
<?php if ($modo_impresion) { ?>
<script>
window.addEventListener('load', function () {
    window.print();
});
</script>
<?php } ?>
<?php require('../configuracion/parte_inferior.php'); ?>
