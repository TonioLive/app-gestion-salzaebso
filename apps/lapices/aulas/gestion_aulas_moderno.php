<?php
require('../configuracion/parte_superior_privado.php');

if (empty($permitido)) {
    require('../configuracion/parte_inferior.php');
    return;
}

$id_centro = (int) $centro_actual;
$curso_seleccionado = app_request_int($_GET, 'curso_seleccionado', 0);
$id_curso_actual = 0;
$fecha_actual = date('Y-m-d');

if ($curso_seleccionado > 0) {
    $resultado_curso = db_query_params(
        'SELECT id_curso, nombre FROM cursos_academicos WHERE id_curso=?',
        'i',
        array($curso_seleccionado)
    );
} else {
    $resultado_curso = db_query_params(
        'SELECT id_curso, nombre FROM cursos_academicos '
        . 'WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 1',
        'ss',
        array($fecha_actual, $fecha_actual)
    );

    if (!$resultado_curso || db_num_rows($resultado_curso) === 0) {
        $resultado_curso = db_query_params(
            'SELECT id_curso, nombre FROM cursos_academicos '
            . 'WHERE fecha_inicio>? ORDER BY fecha_inicio LIMIT 1',
            's',
            array($fecha_actual)
        );
    }
}

$nombre_curso = 'Todos los cursos';
if ($resultado_curso && db_num_rows($resultado_curso) > 0) {
    $datos_curso = db_fetch_assoc($resultado_curso);
    $id_curso_actual = (int) $datos_curso['id_curso'];
    $nombre_curso = $datos_curso['nombre'];
}

if ($id_curso_actual > 0) {
    $resultado_aulas = db_query_params(
        'SELECT id_aula, nombre, id_curso FROM centro_aulas '
        . 'WHERE tipo<2 AND id_centro=? AND (id_curso=? OR id_curso=0) '
        . 'ORDER BY id_curso, nombre',
        'ii',
        array($id_centro, $id_curso_actual)
    );
} else {
    $resultado_aulas = db_query_params(
        'SELECT id_aula, nombre, id_curso FROM centro_aulas '
        . 'WHERE tipo<2 AND id_centro=? ORDER BY id_curso, nombre',
        'i',
        array($id_centro)
    );
}

$aulas_centro = array();
if ($resultado_aulas) {
    while ($aula = db_fetch_assoc($resultado_aulas)) {
        $aulas_centro[] = $aula;
    }
}
?>
<section class="legacy-module-page" aria-labelledby="aulas-title">
    <header class="legacy-module-page__heading">
        <div>
            <span class="legacy-module-page__eyebrow">Organizaci&oacute;n del centro</span>
            <h1 id="aulas-title">Aulas y asistencia</h1>
            <p>Accede al control diario, las fotograf&iacute;as y las herramientas de cada aula.</p>
        </div>
        <div class="legacy-module-page__meta">
            <span>Curso seleccionado</span>
            <strong><?= app_escape($nombre_curso); ?></strong>
        </div>
    </header>

    <?php if (empty($aulas_centro)) { ?>
        <div class="legacy-empty-state">
            <strong>No hay aulas disponibles</strong>
            <p>No se han encontrado aulas para este centro y curso.</p>
        </div>
    <?php } else { ?>
        <div class="legacy-aula-grid">
            <?php foreach ($aulas_centro as $aula) {
                $id_aula = (int) $aula['id_aula'];
                ?>
                <article class="legacy-aula-card">
                    <div class="legacy-aula-card__icon" aria-hidden="true">A</div>
                    <div class="legacy-aula-card__copy">
                        <span>Aula</span>
                        <h2><?= app_escape($aula['nombre']); ?></h2>
                    </div>
                    <div class="legacy-aula-card__actions">
                        <a class="legacy-card-action legacy-card-action--primary" href="/aulas/calendario_mes.php?id_aula=<?= $id_aula; ?>">
                            Control de asistencia
                        </a>
                        <a class="legacy-card-action" href="/gestion/galerias_imagenes/aula/index.php?id_aula=<?= $id_aula; ?>">
                            Fotograf&iacute;as
                        </a>
                    </div>
                </article>
            <?php } ?>
        </div>
    <?php } ?>
</section>
<?php require('../configuracion/parte_inferior.php'); ?>

