<?php
require_once __DIR__ . '/bootstrap.php';

$report_id = app_request_int($_GET, 'id', 0);
$report_name = 'Informe personalizado';
$definition = array();

if ($report_id > 0) {
    $report = reports_load_saved(
        $reports_database,
        $report_id,
        (int) $idUsuario
    );
    if (!$report) {
        http_response_code(404);
        exit('Informe no encontrado.');
    }
    $report_name = $report['nombre'];
    $definition = $report['definition_array'];
} else {
    if (
        app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST'
        || !app_verify_csrf(app_request_value($_POST, '_csrf', ''))
    ) {
        http_response_code(403);
        exit('La sesión del formulario ha caducado.');
    }
    $report_name = reports_string_limit(
        app_request_value($_POST, 'nombre', 'Informe personalizado'),
        160
    );
    $definition = app_request_value($_POST, 'definicion', '');
}

$result = reports_execute_definition(
    $reports_database,
    $definition,
    REPORTS_EXPORT_ROWS
);
if (empty($result['ok'])) {
    http_response_code(422);
    exit(app_escape(implode(' ', $result['errors'])));
}

if ($report_id > 0) {
    reports_mark_executed($reports_database, $report_id);
}

$filename = reports_safe_filename($report_name)
    . '_' . date('Y-m-d_H-i') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: private, no-store, max-age=0');
echo "\xEF\xBB\xBF";
?>
<!doctype html>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; }
        th {
            padding: 8px;
            border: 1px solid #9eb7b0;
            color: #ffffff;
            background: #21675a;
            font-weight: bold;
        }
        td {
            padding: 7px;
            border: 1px solid #ccd9d5;
            mso-number-format: "\@";
            vertical-align: top;
        }
        .meta { color: #5f756f; }
    </style>
</head>
<body>
    <h1><?php echo app_escape($report_name); ?></h1>
    <p class="meta">
        Generado el <?php echo app_escape(date('d/m/Y H:i:s')); ?>
        <?php if (!empty($nombre_centro_actual)) { ?>
            · Centro: <?php echo app_escape($nombre_centro_actual); ?>
        <?php } ?>
    </p>
    <?php if ($result['truncated']) { ?>
        <p>
            Exportación limitada a <?php echo (int) $result['limit']; ?> filas
            según la configuración del informe.
        </p>
    <?php } ?>
    <table>
        <thead>
            <tr>
                <?php foreach ($result['headers'] as $header) { ?>
                    <th><?php echo app_escape($header); ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result['rows'] as $row) { ?>
                <tr>
                    <?php foreach ($row as $value) { ?>
                        <td><?php echo $value === null ? '' : app_escape($value); ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
