<?php
require('../configuracion/parte_superior_privado_alumnos.php');

if (empty($permitido)) {
    require('../configuracion/parte_inferior.php');
    return;
}

$opciones_alumnos = array(
    array(
        'icono' => '+',
        'titulo' => 'Inscribir alumno',
        'resumen' => 'Alta de un alumno nuevo o recuperacion de un expediente anterior.',
        'href' => '/alumnos/nuevo_alumno.php',
        'principal' => true,
    ),
    array(
        'icono' => 'B',
        'titulo' => 'Buscar alumno',
        'resumen' => 'Localiza fichas por nombre, documento, telefono, SIP u otros datos.',
        'href' => '/alumnos/busqueda_alumno.php',
    ),
    array(
        'icono' => 'L',
        'titulo' => 'Listado simplificado',
        'resumen' => 'Nombre, aula, nacimiento, piscina, comedor y fechas del curso.',
        'href' => '/alumnos/listado_simplificado.php',
    ),
    array(
        'icono' => 'A',
        'titulo' => 'Ocupacion de aulas',
        'resumen' => 'Resumen de alumnos matriculados por aula, curso y centro.',
        'href' => '/gestion/centros/recuento_alumnos_aulas.php',
    ),
    array(
        'icono' => 'M',
        'titulo' => 'Resumen de matriculas',
        'resumen' => 'Consulta la evolucion de matriculas por edades, cursos y centros.',
        'href' => '/gestion/centros/recuento_alumnos.php',
    ),
    array(
        'icono' => 'P',
        'titulo' => 'Asistencia a piscina',
        'resumen' => 'Resumen de alumnos que acuden a piscina por curso y centro.',
        'href' => '/gestion/centros/recuento_alumnos_piscina.php',
    ),
    array(
        'icono' => 'X',
        'titulo' => 'Exportar listados',
        'resumen' => 'Genera listados para Excel, etiquetas y combinaciones de correspondencia.',
        'href' => '/alumnos/seleccion_listados.php',
    ),
    array(
        'icono' => 'C',
        'titulo' => 'Cartas webcam',
        'resumen' => 'Imprime las cartas de acceso a la webcam para las familias.',
        'href' => '/pdf/cartas.php?id_escuela=' . (int) $centro_actual,
        'target' => '_blank',
    ),
);
?>
<section class="legacy-module-page" aria-labelledby="gestion-alumnos-title">
    <header class="legacy-module-page__heading">
        <div>
            <span class="legacy-module-page__eyebrow">Alumnos</span>
            <h1 id="gestion-alumnos-title">Gestion de alumnos</h1>
            <p>Altas, consultas, listados y seguimiento de las matriculas del centro activo.</p>
        </div>
        <div class="legacy-module-page__meta">
            <span>Centro actual</span>
            <strong><?= app_escape($nombre_centro_actual); ?></strong>
        </div>
    </header>

    <div class="legacy-aula-grid">
        <?php foreach ($opciones_alumnos as $opcion) { ?>
            <article class="legacy-aula-card">
                <span class="legacy-aula-card__icon" aria-hidden="true"><?= app_escape($opcion['icono']); ?></span>
                <div class="legacy-aula-card__copy">
                    <span>Alumnos</span>
                    <h2><?= app_escape($opcion['titulo']); ?></h2>
                    <p><?= app_escape($opcion['resumen']); ?></p>
                </div>
                <div class="legacy-aula-card__actions">
                    <a
                        class="legacy-card-action<?= !empty($opcion['principal']) ? ' legacy-card-action--primary' : ''; ?>"
                        href="<?= app_escape($opcion['href']); ?>"
                        <?= !empty($opcion['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                    >Abrir</a>
                </div>
            </article>
        <?php } ?>
    </div>
</section>
<?php require('../configuracion/parte_inferior.php'); ?>
