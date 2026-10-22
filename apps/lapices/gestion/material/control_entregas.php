<?php
require_once __DIR__ . '/bootstrap.php';

$course_id = app_request_int($_GET, 'curso_seleccionado', 0);
if ($course_id <= 0) {
    $course_id = material_default_course($material_database);
}
$course = material_course($material_database, $course_id);
$courses = material_courses($material_database);
$products = material_products(
    $material_database,
    (int) $centro_actual,
    $course_id
);
$product_id = app_request_int($_GET, 'id_material', 0);
if ($product_id <= 0 && isset($products[0])) {
    $product_id = (int) $products[0]['id_material'];
}

$selected_product = false;
foreach ($products as $product) {
    if ((int) $product['id_material'] === $product_id) {
        $selected_product = $product;
        break;
    }
}
if (!$selected_product && isset($products[0])) {
    $selected_product = $products[0];
    $product_id = (int) $selected_product['id_material'];
}

$pupils = material_pupils(
    $material_database,
    (int) $centro_actual,
    $course_id
);
$deliveries = $selected_product
    ? material_deliveries(
        $material_database,
        (int) $centro_actual,
        $course_id,
        $product_id
    )
    : array();
$current_stock = $selected_product
    ? material_stock($material_database, (int) $centro_actual, $product_id)
    : 0;
$selected_product_image = $selected_product
    ? material_product_image(
        $selected_product['ARTICULO'],
        $selected_product['categoria']
    )
    : false;
$pending_count = 0;
foreach ($deliveries as $delivery) {
    if ($delivery['estado'] === 'entregado') {
        $pending_count++;
    }
}

$panel_topbar_title = 'Control de entregas';
$flash = material_take_flash();
require dirname(__DIR__) . '/panel/views/header.php';
?>
<section class="material-page">
    <header class="material-hero">
        <div>
            <span class="eyebrow">Gestión de material</span>
            <h1>Control de entregas</h1>
            <p>
                Registra a quién se entrega cada artículo, descuéntalo del stock,
                cóbralo y gestiona cambios de talla sin perder la trazabilidad.
            </p>
        </div>
        <div class="material-hero__actions">
            <a
                class="button button--outline"
                href="/gestion/material/gestion_material.php?curso_seleccionado=<?php echo $course_id; ?>"
            >
                Previsión de material
            </a>
        </div>
    </header>

    <?php if ($flash) { ?>
        <div class="alert alert--<?php echo app_escape($flash['type']); ?>">
            <?php echo app_escape($flash['message']); ?>
        </div>
    <?php } ?>

    <form class="card material-toolbar" method="get">
        <label>
            <span>Curso</span>
            <select name="curso_seleccionado" onchange="this.form.submit()">
                <?php foreach ($courses as $course_option) { ?>
                    <option
                        value="<?php echo (int) $course_option['id_curso']; ?>"
                        <?php echo (int) $course_option['id_curso'] === $course_id ? ' selected' : ''; ?>
                    >
                        <?php echo app_escape($course_option['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </label>
        <label>
            <span>Artículo</span>
            <select name="id_material" onchange="this.form.submit()">
                <?php foreach ($products as $product) { ?>
                    <option
                        value="<?php echo (int) $product['id_material']; ?>"
                        <?php echo (int) $product['id_material'] === $product_id ? ' selected' : ''; ?>
                    >
                        <?php echo app_escape($product['ARTICULO']); ?>
                        · Stock <?php echo number_format((float) $product['stock_actual'], 0, ',', '.'); ?>
                    </option>
                <?php } ?>
            </select>
        </label>
    </form>

    <?php if (!$selected_product) { ?>
        <div class="card material-empty">
            No hay artículos de material disponibles para este curso.
        </div>
    <?php } else { ?>
        <div class="material-stats">
            <article class="card material-stat">
                <span>Stock disponible</span>
                <strong><?php echo number_format($current_stock, 0, ',', '.'); ?></strong>
                <small>En <?php echo app_escape($nombre_centro_actual); ?></small>
            </article>
            <article class="card material-stat">
                <span>Alumnado</span>
                <strong><?php echo count($pupils); ?></strong>
                <small><?php echo app_escape($course['nombre']); ?></small>
            </article>
            <article class="card material-stat">
                <span>Entregas registradas</span>
                <strong><?php echo count($deliveries); ?></strong>
                <small>Del artículo seleccionado</small>
            </article>
            <article class="card material-stat">
                <span>Pendientes de cobro</span>
                <strong><?php echo $pending_count; ?></strong>
                <small>Se pueden facturar desde el listado</small>
            </article>
        </div>

        <div class="delivery-layout">
            <aside class="card delivery-side">
                <div class="delivery-product">
                    <img
                        src="<?php echo app_escape($selected_product_image['src']); ?>"
                        alt="<?php echo app_escape($selected_product_image['alt']); ?>"
                        width="160"
                        height="160"
                        decoding="async"
                    >
                    <div>
                    <span>Artículo seleccionado</span>
                    <strong><?php echo app_escape($selected_product['ARTICULO']); ?></strong>
                    <span>
                        <?php echo number_format((float) $selected_product['PRECIO_VENTA'], 2, ',', '.'); ?> €
                        · <?php echo number_format($current_stock, 0, ',', '.'); ?> en stock
                    </span>
                    </div>
                </div>

                <form
                    class="delivery-form"
                    method="post"
                    action="/gestion/material/entregar.php"
                >
                    <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                    <input type="hidden" name="curso_seleccionado" value="<?php echo $course_id; ?>">
                    <input type="hidden" name="id_material" value="<?php echo $product_id; ?>">
                    <label>
                        <span>Alumno que recibe el artículo</span>
                        <select name="id_alumno" required>
                            <option value="">Selecciona un alumno</option>
                            <?php foreach ($pupils as $pupil) { ?>
                                <option value="<?php echo (int) $pupil['id_alumno']; ?>">
                                    <?php echo app_escape(trim(
                                        $pupil['Apellido1'] . ' '
                                        . $pupil['Apellido2'] . ', '
                                        . $pupil['Nombre']
                                    )); ?>
                                    · <?php echo app_escape($pupil['nombre_aula']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>
                    <label>
                        <span>Cantidad entregada</span>
                        <input
                            type="number"
                            name="cantidad"
                            min="1"
                            max="<?php echo max(1, (int) floor($current_stock)); ?>"
                            step="1"
                            value="1"
                            required
                        >
                    </label>
                    <label>
                        <span>Nota opcional</span>
                        <textarea
                            name="notas"
                            maxlength="500"
                            placeholder="Ej. entregado a la madre, talla probada en el centro…"
                        ></textarea>
                    </label>
                    <button
                        class="button"
                        type="submit"
                        <?php echo $current_stock < 1 ? ' disabled' : ''; ?>
                    >
                        Entregar y descontar del stock
                    </button>
                </form>
            </aside>

            <section class="card delivery-list">
                <div class="delivery-list__heading">
                    <div>
                        <span class="eyebrow">Historial</span>
                        <h2>Entregas del artículo</h2>
                    </div>
                    <input
                        type="search"
                        placeholder="Buscar alumno o aula"
                        data-delivery-search
                    >
                </div>

                <?php if (!$deliveries) { ?>
                    <div class="material-empty">
                        Todavía no hay entregas registradas para este artículo.
                    </div>
                <?php } else { ?>
                    <div class="delivery-table-wrap">
                        <table class="delivery-table">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Fecha</th>
                                    <th>Cantidad</th>
                                    <th>Estado</th>
                                    <th>Facturación y cambios</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deliveries as $delivery) { ?>
                                    <tr data-delivery-row>
                                        <td>
                                            <strong><?php echo app_escape(trim(
                                                $delivery['Apellido1'] . ' '
                                                . $delivery['Apellido2'] . ', '
                                                . $delivery['Nombre']
                                            )); ?></strong><br>
                                            <small><?php echo app_escape($delivery['nombre_aula']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo app_escape(date(
                                                'd/m/Y H:i',
                                                strtotime($delivery['fecha_entrega'])
                                            )); ?>
                                        </td>
                                        <td><?php echo app_escape($delivery['cantidad']); ?></td>
                                        <td>
                                            <span class="delivery-status delivery-status--<?php echo app_escape($delivery['estado']); ?>">
                                                <?php echo app_escape($delivery['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="delivery-row__actions">
                                                <?php if ($delivery['estado'] === 'entregado') { ?>
                                                    <form method="post" action="/gestion/material/cobrar.php">
                                                        <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                                                        <input type="hidden" name="curso_seleccionado" value="<?php echo $course_id; ?>">
                                                        <input type="hidden" name="id_material" value="<?php echo $product_id; ?>">
                                                        <input type="hidden" name="id_entrega" value="<?php echo (int) $delivery['id_entrega']; ?>">
                                                        <button class="button button--small" type="submit">
                                                            Cobrar
                                                        </button>
                                                    </form>
                                                <?php } ?>
                                                <?php if ((int) $delivery['id_factura'] > 0) { ?>
                                                    <a
                                                        class="button button--outline button--small"
                                                        href="/pdf/factura.php?id_factura=<?php echo (int) $delivery['id_factura']; ?>"
                                                        target="_blank"
                                                    >
                                                        Factura
                                                    </a>
                                                <?php } ?>
                                            </div>

                                            <?php if ($delivery['estado'] === 'cobrado') { ?>
                                                <details class="exchange-details">
                                                    <summary>Cambiar talla o artículo</summary>
                                                    <form
                                                        class="exchange-form"
                                                        method="post"
                                                        action="/gestion/material/cambiar.php"
                                                    >
                                                        <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                                                        <input type="hidden" name="curso_seleccionado" value="<?php echo $course_id; ?>">
                                                        <input type="hidden" name="id_material_actual" value="<?php echo $product_id; ?>">
                                                        <input type="hidden" name="id_entrega" value="<?php echo (int) $delivery['id_entrega']; ?>">
                                                        <label>
                                                            <span>Nuevo artículo</span>
                                                            <select name="id_material_nuevo" required>
                                                                <?php foreach ($products as $exchange_product) { ?>
                                                                    <?php if ((float) $exchange_product['stock_actual'] > 0) { ?>
                                                                        <option value="<?php echo (int) $exchange_product['id_material']; ?>">
                                                                            <?php echo app_escape($exchange_product['ARTICULO']); ?>
                                                                            · Stock <?php echo number_format((float) $exchange_product['stock_actual'], 0, ',', '.'); ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </select>
                                                        </label>
                                                        <label>
                                                            <span>Cantidad nueva</span>
                                                            <input
                                                                type="number"
                                                                name="cantidad_nueva"
                                                                min="1"
                                                                step="1"
                                                                value="<?php echo app_escape($delivery['cantidad']); ?>"
                                                                required
                                                            >
                                                        </label>
                                                        <label>
                                                            <span>Motivo del cambio</span>
                                                            <textarea
                                                                name="notas"
                                                                maxlength="500"
                                                                placeholder="Ej. necesita una talla más"
                                                            ></textarea>
                                                        </label>
                                                        <button class="button" type="submit">
                                                            Abonar anterior y cobrar nuevo
                                                        </button>
                                                    </form>
                                                </details>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </section>
        </div>
    <?php } ?>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
