<?php
require_once __DIR__ . '/bootstrap.php';

$report_id = app_request_int($_GET, 'id', 0);
$report = $reports_database instanceof mysqli
    ? reports_load_saved($reports_database, $report_id, (int) $idUsuario)
    : false;
if (!$report) {
    http_response_code(404);
    header('Location: /gestion/informes/guardados.php?error=no_encontrado');
    exit;
}

$result = reports_execute_definition(
    $reports_database,
    $report['definition_array'],
    REPORTS_SCREEN_ROWS
);
if (!empty($result['ok'])) {
    reports_mark_executed($reports_database, $report_id);
}

$can_manage = reports_can_manage_saved($report, $idUsuario);
$panel_topbar_title = $report['nombre'];

require dirname(__DIR__) . '/panel/views/header.php';
?>
<section class="report-result" aria-labelledby="report-result-title">
    <header class="report-hero report-hero--result">
        <div>
            <span class="eyebrow">Informe actualizado</span>
            <h1 id="report-result-title"><?php echo app_escape($report['nombre']); ?></h1>
            <?php if ($report['descripcion'] !== '') { ?>
                <p><?php echo app_escape($report['descripcion']); ?></p>
            <?php } ?>
        </div>
        <div class="report-hero__actions">
            <a class="button button--outline" href="/gestion/informes/guardados.php">
                Volver
            </a>
            <?php if ($can_manage) { ?>
                <a
                    class="button button--outline"
                    href="/gestion/informes/?id=<?php echo (int) $report_id; ?>"
                >Editar</a>
            <?php } ?>
            <a
                class="button button--primary"
                href="/gestion/informes/exportar_excel.php?id=<?php echo (int) $report_id; ?>"
            >Exportar Excel</a>
        </div>
    </header>

    <?php if (empty($result['ok'])) { ?>
        <div class="alert alert--error" role="alert">
            <?php echo app_escape(implode(' ', $result['errors'])); ?>
        </div>
    <?php } else { ?>
        <div class="report-result__summary">
            <div>
                <span>Filas mostradas</span>
                <strong><?php echo (int) $result['row_count']; ?></strong>
            </div>
            <div>
                <span>Columnas</span>
                <strong><?php echo count($result['headers']); ?></strong>
            </div>
            <div>
                <span>Actualizado</span>
                <strong><?php echo app_escape(date('H:i:s')); ?></strong>
            </div>
        </div>

        <?php if ($result['truncated']) { ?>
            <div class="alert alert--warning" role="status">
                Se muestran las primeras <?php echo (int) $result['limit']; ?> filas.
                Puedes ampliar el límite editando el informe o exportarlo a Excel.
            </div>
        <?php } ?>

        <div class="card report-result__table-card">
            <div class="report-table-wrap">
                <table class="report-data-table">
                    <thead>
                        <tr>
                            <?php foreach ($result['headers'] as $header) { ?>
                                <th><?php echo app_escape($header); ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($result['rows']) === 0) { ?>
                            <tr>
                                <td colspan="<?php echo max(1, count($result['headers'])); ?>">
                                    No hay datos que cumplan los filtros.
                                </td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($result['rows'] as $row) { ?>
                            <tr>
                                <?php foreach ($row as $index => $value) { ?>
                                    <td data-label="<?php echo app_escape($result['headers'][$index]); ?>">
                                        <?php echo $value === null || $value === ''
                                            ? '<span class="report-null">—</span>'
                                            : app_escape($value); ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
