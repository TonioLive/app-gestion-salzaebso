<?php
require('../configuracion/parte_superior_docente_ancho.php');

$nivel_comedor = isset($nivel_acceso) ? (int) $nivel_acceso : 0;
$es_docente_comedor = $nivel_comedor === 2;
$puede_gestionar_cocina = $nivel_comedor >= 3 && $nivel_comedor <= 10;

$opciones_comedor = array();

if ($puede_gestionar_cocina) {
    $opciones_comedor[] = array(
        'icono' => 'C',
        'categoria' => 'Operativa diaria',
        'titulo' => 'Calendario de cocina',
        'resumen' => 'Previsiones, preparacion, albaranes y etiquetas organizados por dia.',
        'href' => '/cocina/calendario_mes_comedor.php',
        'principal' => true,
    );
}

$opciones_comedor[] = array(
    'icono' => 'M',
    'categoria' => 'Consulta',
    'titulo' => 'Menus del mes',
    'resumen' => 'Consulta e imprime los menus programados del centro.',
    'href' => '/menu/menus_mes.php',
    'target' => '_blank',
    'principal' => !$puede_gestionar_cocina,
);

if ($es_docente_comedor) {
    $opciones_comedor[] = array(
        'icono' => 'A',
        'categoria' => 'Asistencia',
        'titulo' => 'Asistencia comedor',
        'resumen' => 'Registra y revisa la asistencia diaria al comedor de tus aulas.',
        'href' => '/aulas_docentes/calendario_mes_comedor.php',
    );
}

if ($puede_gestionar_cocina) {
    $opciones_comedor[] = array(
        'icono' => 'G',
        'categoria' => 'Planificacion',
        'titulo' => 'Gestion de menus',
        'resumen' => 'Crea, edita y organiza los menus disponibles y sus platos.',
        'href' => '/menu/gestion_menu.php',
    );
    $opciones_comedor[] = array(
        'icono' => 'P',
        'categoria' => 'Produccion',
        'titulo' => 'Preparacion de platos',
        'resumen' => 'Consulta las tareas de preparacion y el estado diario de los platos.',
        'href' => '/cocina/preparacion_platos/',
    );
    $opciones_comedor[] = array(
        'icono' => 'L',
        'categoria' => 'Compras',
        'titulo' => 'Listas de compra',
        'resumen' => 'Genera, revisa e imprime las listas de compra entre fechas.',
        'href' => '/cocina/gestion_cocina.php',
    );
    $opciones_comedor[] = array(
        'icono' => 'N',
        'categoria' => 'Planificacion',
        'titulo' => 'Asignacion de menus',
        'resumen' => 'Revisa la asignacion de menus por edades, centros y necesidades.',
        'href' => '/cocina/asignacion_de_menus.php',
    );
    $opciones_comedor[] = array(
        'icono' => 'T',
        'categoria' => 'Control',
        'titulo' => 'Temperaturas y albaranes',
        'resumen' => 'Registra temperaturas, horas de salida y datos de entrega.',
        'href' => '/cocina/intro_temperaturas.php',
    );
    $opciones_comedor[] = array(
        'icono' => 'F',
        'categoria' => 'Entregas',
        'titulo' => 'Firmar albaranes',
        'resumen' => 'Selecciona una fecha y gestiona la firma de los albaranes de entrega.',
        'href' => '/cocina/albaranes_entrega_seleccion.php',
    );
    $opciones_comedor[] = array(
        'icono' => 'R',
        'categoria' => 'Consumos',
        'titulo' => 'Resumen de consumos',
        'resumen' => 'Consulta ingredientes y cantidades consumidas por los menus servidos.',
        'href' => '/cocina/listado_consumos.php',
    );
}
?>
<section class="legacy-module-page" aria-labelledby="gestion-comedor-title">
    <header class="legacy-module-page__heading">
        <div>
            <span class="legacy-module-page__eyebrow">Comedor y cocina</span>
            <h1 id="gestion-comedor-title">Gestion de comedor</h1>
            <p>Menus, asistencia y herramientas de cocina reunidos en una sola pantalla.</p>
        </div>
        <div class="legacy-module-page__meta">
            <span>Centro actual</span>
            <strong><?= app_escape($nombre_centro_actual); ?></strong>
        </div>
    </header>

    <div class="legacy-aula-grid">
        <?php foreach ($opciones_comedor as $opcion) { ?>
            <article class="legacy-aula-card">
                <span class="legacy-aula-card__icon" aria-hidden="true"><?= app_escape($opcion['icono']); ?></span>
                <div class="legacy-aula-card__copy">
                    <span><?= app_escape($opcion['categoria']); ?></span>
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
