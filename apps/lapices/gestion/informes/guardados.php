<?php
require_once __DIR__ . '/bootstrap.php';

$panel_topbar_title = 'Informes guardados';
$search = app_request_value($_GET, 'buscar', '');
$saved_reports = $reports_database instanceof mysqli
    ? reports_list_saved($reports_database, (int) $idUsuario, $search)
    : array();

require dirname(__DIR__) . '/panel/views/header.php';
?>
<section class="saved-reports" aria-labelledby="saved-reports-title">
    <header class="report-hero">
        <div>
            <span class="eyebrow">Biblioteca de informes</span>
            <h1 id="saved-reports-title">Informes guardados</h1>
            <p>
                Cada vez que abras un informe se consultarán de nuevo los datos,
                manteniendo su configuración original.
            </p>
        </div>
        <a class="button button--primary" href="/gestion/informes/">
            + Crear informe
        </a>
    </header>

    <?php if (app_request_int($_GET, 'guardado', 0) === 1) { ?>
        <div class="alert alert--success" role="status">
            El informe se ha guardado correctamente.
        </div>
    <?php } ?>
    <?php if (app_request_int($_GET, 'eliminado', 0) === 1) { ?>
        <div class="alert alert--success" role="status">
            El informe se ha eliminado.
        </div>
    <?php } ?>
    <?php if (app_request_value($_GET, 'error', '') !== '') { ?>
        <div class="alert alert--error" role="alert">
            No se ha podido completar la operación solicitada.
        </div>
    <?php } ?>

    <form class="saved-reports__search" method="get">
        <label>
            <span>Buscar informe</span>
            <input
                type="search"
                name="buscar"
                value="<?php echo app_escape($search); ?>"
                placeholder="Nombre o descripción"
            >
        </label>
        <button class="button button--outline" type="submit">Buscar</button>
        <?php if ($search !== '') { ?>
            <a class="button button--outline" href="/gestion/informes/guardados.php">
                Limpiar
            </a>
        <?php } ?>
    </form>

    <?php if ($reports_database_error !== '') { ?>
        <div class="alert alert--error" role="alert">
            <?php echo app_escape($reports_database_error); ?>
        </div>
    <?php } elseif (count($saved_reports) === 0) { ?>
        <div class="card saved-reports__empty">
            <span class="saved-reports__empty-icon">IN</span>
            <h2>No hay informes guardados</h2>
            <p>
                Crea el primero y podrás volver a ejecutarlo siempre con los datos
                más recientes.
            </p>
            <a class="button button--primary" href="/gestion/informes/">
                Crear mi primer informe
            </a>
        </div>
    <?php } else { ?>
        <div class="saved-report-grid">
            <?php foreach ($saved_reports as $report) { ?>
                <?php
                $definition = $report['definition_array'];
                $can_manage = reports_can_manage_saved($report, $idUsuario);
                $column_count = isset($definition['columns']) && is_array($definition['columns'])
                    ? count($definition['columns'])
                    : 0;
                $filter_count = isset($definition['filters']) && is_array($definition['filters'])
                    ? count($definition['filters'])
                    : 0;
                $join_count = isset($definition['joins']) && is_array($definition['joins'])
                    ? count($definition['joins'])
                    : 0;
                $base_table = isset($definition['base_table'])
                    ? $definition['base_table']
                    : '';
                ?>
                <article class="card saved-report-card">
                    <div class="saved-report-card__top">
                        <span class="saved-report-card__icon">IN</span>
                        <div>
                            <div class="saved-report-card__badges">
                                <?php if ((int) $report['compartido'] === 1) { ?>
                                    <span>Compartido</span>
                                <?php } ?>
                                <?php if ($can_manage) { ?>
                                    <span>Editable</span>
                                <?php } ?>
                            </div>
                            <h2><?php echo app_escape($report['nombre']); ?></h2>
                            <?php if ($report['descripcion'] !== '') { ?>
                                <p><?php echo app_escape($report['descripcion']); ?></p>
                            <?php } ?>
                        </div>
                    </div>

                    <dl class="saved-report-card__summary">
                        <div>
                            <dt>Tabla principal</dt>
                            <dd><?php echo app_escape(reports_humanize($base_table)); ?></dd>
                        </div>
                        <div>
                            <dt>Columnas</dt>
                            <dd><?php echo (int) $column_count; ?></dd>
                        </div>
                        <div>
                            <dt>Relaciones</dt>
                            <dd><?php echo (int) $join_count; ?></dd>
                        </div>
                        <div>
                            <dt>Filtros</dt>
                            <dd><?php echo (int) $filter_count; ?></dd>
                        </div>
                    </dl>

                    <div class="saved-report-card__meta">
                        Actualizado el
                        <?php echo app_escape(date('d/m/Y H:i', strtotime($report['fecha_actualizacion']))); ?>
                    </div>

                    <div class="saved-report-card__actions">
                        <a
                            class="button button--primary"
                            href="/gestion/informes/ver.php?id=<?php echo (int) $report['id_informe']; ?>"
                        >Abrir</a>
                        <a
                            class="button button--outline"
                            href="/gestion/informes/exportar_excel.php?id=<?php echo (int) $report['id_informe']; ?>"
                        >Excel</a>
                        <?php if ($can_manage) { ?>
                            <a
                                class="button button--outline"
                                href="/gestion/informes/?id=<?php echo (int) $report['id_informe']; ?>"
                            >Editar</a>
                            <form
                                method="post"
                                action="/gestion/informes/eliminar.php"
                                onsubmit="return confirm('¿Eliminar este informe guardado?');"
                            >
                                <input
                                    type="hidden"
                                    name="_csrf"
                                    value="<?php echo app_escape(app_csrf_token()); ?>"
                                >
                                <input
                                    type="hidden"
                                    name="id_informe"
                                    value="<?php echo (int) $report['id_informe']; ?>"
                                >
                                <button class="button button--danger" type="submit">
                                    Eliminar
                                </button>
                            </form>
                        <?php } ?>
                    </div>
                </article>
            <?php } ?>
        </div>
    <?php } ?>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
