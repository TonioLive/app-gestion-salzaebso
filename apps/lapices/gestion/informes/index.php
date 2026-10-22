<?php
require_once __DIR__ . '/bootstrap.php';

if (!($reports_database instanceof mysqli)) {
    $report_tables = array();
} else {
    $report_tables = reports_list_tables($reports_database);
}

$report_id = app_request_int($_GET, 'id', 0);
$saved_report = false;
$report_name = '';
$report_description = '';
$report_shared = 0;
$initial_definition = array(
    'version' => REPORTS_DEFINITION_VERSION,
    'base_table' => isset(reports_table_map($reports_database)['alumnos'])
        ? 'alumnos'
        : (isset($report_tables[0]['name']) ? $report_tables[0]['name'] : ''),
    'joins' => array(),
    'columns' => array(),
    'filters' => array(),
    'sort' => array(),
    'limit' => 1000,
);

if ($report_id > 0 && $reports_database instanceof mysqli) {
    $saved_report = reports_load_saved(
        $reports_database,
        $report_id,
        (int) $idUsuario
    );
    if (!$saved_report || !reports_can_manage_saved($saved_report, $idUsuario)) {
        http_response_code(403);
        header('Location: /gestion/informes/guardados.php');
        exit;
    }

    $report_name = $saved_report['nombre'];
    $report_description = $saved_report['descripcion'];
    $report_shared = (int) $saved_report['compartido'];
    $initial_definition = $saved_report['definition_array'];
}

$panel_topbar_title = $saved_report
    ? 'Editar informe personalizado'
    : 'Nuevo informe personalizado';
$panel_extra_scripts = array(
    '/gestion/informes/assets/report-builder.js?v=20260728.1',
);

require dirname(__DIR__) . '/panel/views/header.php';
?>
<section
    class="report-builder"
    data-report-builder
    data-schema-endpoint="/gestion/informes/api/esquema.php"
    data-preview-endpoint="/gestion/informes/api/previsualizar.php"
>
    <header class="report-hero">
        <div>
            <span class="eyebrow">Informes personalizados</span>
            <h1><?php echo $saved_report ? 'Editar informe' : 'Construye tu informe'; ?></h1>
            <p>
                Combina tablas, elige exactamente las columnas que necesitas y aplica
                filtros sin escribir consultas SQL.
            </p>
        </div>
        <a class="button button--outline" href="/gestion/informes/guardados.php">
            Informes guardados
        </a>
    </header>

    <?php if (!empty($_SESSION['panel_flash']) && is_array($_SESSION['panel_flash'])) { ?>
        <?php
        $report_flash = $_SESSION['panel_flash'];
        unset($_SESSION['panel_flash']);
        ?>
        <div
            class="alert alert--<?php echo app_escape($report_flash['type']); ?>"
            role="alert"
        >
            <?php echo app_escape($report_flash['message']); ?>
        </div>
    <?php } ?>

    <?php if ($reports_database_error !== '') { ?>
        <div class="alert alert--error" role="alert">
            <?php echo app_escape($reports_database_error); ?>
        </div>
    <?php } ?>

    <form
        method="post"
        action="/gestion/informes/guardar.php"
        class="report-builder__form"
        data-report-form
    >
        <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
        <input type="hidden" name="id_informe" value="<?php echo (int) $report_id; ?>">
        <input type="hidden" name="definicion" value="" data-report-definition>

        <section class="card report-identity" aria-labelledby="report-identity-title">
            <div class="report-section-heading">
                <span class="report-step">1</span>
                <div>
                    <span class="eyebrow">Identificación</span>
                    <h2 id="report-identity-title">Nombre del informe</h2>
                </div>
            </div>
            <div class="report-identity__fields">
                <label>
                    <span>Nombre *</span>
                    <input
                        type="text"
                        name="nombre"
                        maxlength="160"
                        value="<?php echo app_escape($report_name); ?>"
                        placeholder="Ej. Alumnado activo por aula"
                        required
                    >
                </label>
                <label>
                    <span>Descripción</span>
                    <input
                        type="text"
                        name="descripcion"
                        maxlength="500"
                        value="<?php echo app_escape($report_description); ?>"
                        placeholder="Explica brevemente para qué sirve"
                    >
                </label>
                <label class="report-share-toggle">
                    <input
                        type="checkbox"
                        name="compartido"
                        value="1"
                        <?php echo $report_shared === 1 ? 'checked' : ''; ?>
                    >
                    <span>
                        <strong>Compartir con otros administradores</strong>
                        <small>Podrán ejecutarlo y exportarlo, pero no modificarlo.</small>
                    </span>
                </label>
            </div>
        </section>

        <section class="card report-source" aria-labelledby="report-source-title">
            <div class="report-section-heading">
                <span class="report-step">2</span>
                <div>
                    <span class="eyebrow">Origen de los datos</span>
                    <h2 id="report-source-title">Tablas y relaciones</h2>
                </div>
            </div>

            <label class="report-base-table">
                <span>Tabla principal</span>
                <select data-base-table <?php echo count($report_tables) === 0 ? 'disabled' : ''; ?>>
                    <option value="">Selecciona una tabla</option>
                    <?php foreach ($report_tables as $table) { ?>
                        <option value="<?php echo app_escape($table['name']); ?>">
                            <?php echo app_escape($table['label']); ?>
                            <?php echo $table['type'] === 'view' ? ' · vista' : ''; ?>
                        </option>
                    <?php } ?>
                </select>
                <small>Están disponibles las <?php echo count($report_tables); ?> tablas y vistas de la base actual.</small>
            </label>

            <div class="report-joins" data-joins></div>
            <button class="button button--outline report-add-button" type="button" data-add-join>
                + Relacionar otra tabla
            </button>
            <p class="report-help">
                Las relaciones son manuales porque la base antigua no declara claves foráneas.
                Por ejemplo: <strong>alumnos.id_usuario = usuarios.id_usuario</strong>.
            </p>
        </section>

        <section class="card report-columns" aria-labelledby="report-columns-title">
            <div class="report-section-heading">
                <span class="report-step">3</span>
                <div>
                    <span class="eyebrow">Contenido</span>
                    <h2 id="report-columns-title">Columnas del informe</h2>
                </div>
            </div>

            <div class="report-field-adder">
                <label>
                    <span>Campo disponible</span>
                    <select data-available-field>
                        <option value="">Selecciona primero una tabla</option>
                    </select>
                </label>
                <button class="button button--outline" type="button" data-add-column>
                    Añadir columna
                </button>
            </div>

            <div class="report-selected-columns" data-selected-columns>
                <div class="report-empty" data-columns-empty>
                    Todavía no has añadido columnas.
                </div>
            </div>
        </section>

        <section class="card report-rules" aria-labelledby="report-rules-title">
            <div class="report-section-heading">
                <span class="report-step">4</span>
                <div>
                    <span class="eyebrow">Condiciones</span>
                    <h2 id="report-rules-title">Filtros y ordenación</h2>
                </div>
            </div>

            <div class="report-rule-block">
                <div class="report-rule-block__heading">
                    <div>
                        <h3>Filtros</h3>
                        <p>Puedes añadir hasta <?php echo REPORTS_MAX_FILTERS; ?> condiciones.</p>
                    </div>
                    <button class="button button--outline" type="button" data-add-filter>
                        + Añadir filtro
                    </button>
                </div>
                <div class="report-filter-list" data-filters></div>
            </div>

            <div class="report-rule-block">
                <div class="report-rule-block__heading">
                    <div>
                        <h3>Ordenación</h3>
                        <p>Ordena por cualquiera de las columnas elegidas.</p>
                    </div>
                    <button class="button button--outline" type="button" data-add-sort>
                        + Añadir orden
                    </button>
                </div>
                <div class="report-sort-list" data-sorts></div>
            </div>

            <label class="report-limit">
                <span>Máximo de filas al ejecutar o exportar</span>
                <input
                    type="number"
                    min="1"
                    max="<?php echo REPORTS_EXPORT_ROWS; ?>"
                    step="1"
                    value="<?php echo isset($initial_definition['limit']) ? (int) $initial_definition['limit'] : 1000; ?>"
                    data-report-limit
                >
                <small>La vista previa muestra como máximo <?php echo REPORTS_PREVIEW_ROWS; ?> filas.</small>
            </label>
        </section>

        <div class="report-builder__actions">
            <button class="button button--outline" type="button" data-preview-report>
                Previsualizar
            </button>
            <button
                class="button button--outline"
                type="submit"
                formaction="/gestion/informes/exportar_excel.php"
                formtarget="_blank"
                data-export-report
            >
                Exportar configuración a Excel
            </button>
            <button class="button button--primary" type="submit" data-save-report>
                <?php echo $saved_report ? 'Guardar cambios' : 'Guardar informe'; ?>
            </button>
        </div>
    </form>

    <section class="card report-preview" aria-labelledby="report-preview-title">
        <div class="report-rule-block__heading">
            <div>
                <span class="eyebrow">Datos actuales</span>
                <h2 id="report-preview-title">Vista previa</h2>
            </div>
            <span class="report-preview__status" data-preview-status>
                Configura el informe y pulsa «Previsualizar».
            </span>
        </div>
        <div class="report-preview__message" data-preview-message hidden></div>
        <div class="report-table-wrap" data-preview-table-wrap hidden>
            <table class="report-data-table">
                <thead data-preview-head></thead>
                <tbody data-preview-body></tbody>
            </table>
        </div>
    </section>

    <script type="application/json" id="report-builder-config"><?php
        echo json_encode(array(
            'tables' => $report_tables,
            'definition' => $initial_definition,
            'csrf' => app_csrf_token(),
            'limits' => array(
                'joins' => REPORTS_MAX_JOINS,
                'columns' => REPORTS_MAX_COLUMNS,
                'filters' => REPORTS_MAX_FILTERS,
                'sorts' => REPORTS_MAX_SORTS,
            ),
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?></script>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
