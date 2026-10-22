<?php
require_once __DIR__ . '/bootstrap.php';

$course_id = app_request_int($_GET, 'curso_seleccionado', 0);
if ($course_id <= 0) {
    $course_id = material_default_course($material_database);
}
$course = material_course($material_database, $course_id);
if (!$course) {
    $course_id = material_default_course($material_database);
    $course = material_course($material_database, $course_id);
}

$courses = material_courses($material_database);
$pupils = material_pupils($material_database, (int) $centro_actual, $course_id);
$products = material_products(
    $material_database,
    (int) $centro_actual,
    $course_id
);

$boys = 0;
$girls = 0;
foreach ($pupils as $pupil) {
    $gender = material_pupil_gender($pupil['sexo']);
    if ($gender === 'H') {
        $boys++;
    } elseif ($gender === 'M') {
        $girls++;
    }
}

$recommended_total = 0;
$planned_total = 0;
$planned_line_count = 0;
foreach ($products as $index => $product) {
    $estimate = material_estimate($product, $pupils, $course);
    $products[$index]['estimate'] = $estimate;
    $recommended_total += $estimate['recommended'];
    $planned_total += (float) $product['cantidad_planificada'];
    if ((float) $product['cantidad_planificada'] > 0) {
        $planned_line_count++;
    }
}
$order_token = material_order_request_token(
    (int) $centro_actual,
    $course_id
);
$recent_orders = material_recent_orders(
    $material_database,
    (int) $centro_actual,
    6
);

$panel_topbar_title = 'Previsión y gestión de material';
$flash = material_take_flash();
require dirname(__DIR__) . '/panel/views/header.php';
?>
<section class="material-page">
    <header class="material-hero">
        <div>
            <span class="eyebrow">Gestión de material</span>
            <h1>Previsión de uniformes y material</h1>
            <p>
                Consulta el stock del centro, revisa la recomendación calculada a
                partir del alumnado y guarda la cantidad que realmente quieres
                preparar.
            </p>
        </div>
        <div class="material-hero__actions">
            <a
                class="button"
                href="/gestion/material/control_entregas.php?curso_seleccionado=<?php echo $course_id; ?>"
            >
                Control de entregas
            </a>
            <a class="button button--outline" href="/gestion/panel/modulo.php?modulo=almacen">
                Volver a Almacén
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
            <span>Curso para calcular la previsión</span>
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
            <span>Buscar artículo, talla, sexo o categoría</span>
            <input
                type="search"
                placeholder="Ej. uniforme verano talla 2"
                data-material-search
            >
        </label>
    </form>

    <div class="material-stats">
        <article class="card material-stat">
            <span>Alumnado del curso</span>
            <strong><?php echo count($pupils); ?></strong>
            <small><?php echo app_escape($course['nombre']); ?></small>
        </article>
        <article class="card material-stat">
            <span>Distribución</span>
            <strong><?php echo $boys; ?> / <?php echo $girls; ?></strong>
            <small>Niños / niñas con sexo indicado</small>
        </article>
        <article class="card material-stat">
            <span>Recomendación total</span>
            <strong><?php echo $recommended_total; ?></strong>
            <small>Alumnado por talla + 10 % de margen</small>
        </article>
        <article class="card material-stat">
            <span>Planificado</span>
            <strong><?php echo number_format($planned_total, 0, ',', '.'); ?></strong>
            <small>Cantidad guardada por el centro</small>
        </article>
    </div>

    <section class="card material-order-panel">
        <div class="material-order-panel__content">
            <span class="material-order-panel__icon" aria-hidden="true">PDF</span>
            <div>
                <span class="eyebrow">Pedido al proveedor</span>
                <h2>Generar pedido desglosado y sumar el stock</h2>
                <?php if ($planned_total > 0) { ?>
                    <p>
                        Se incluirán
                        <strong><?php echo $planned_line_count; ?> artículos</strong>
                        y
                        <strong><?php echo number_format($planned_total, 0, ',', '.'); ?> unidades</strong>.
                        Al generar el documento, esas unidades se añadirán una sola vez
                        al stock del centro y la previsión enviada quedará a cero.
                    </p>
                <?php } else { ?>
                    <p>
                        Guarda primero una cantidad planificada en los artículos que
                        quieras incluir. El pedido solo recogerá cantidades mayores que cero.
                    </p>
                <?php } ?>
            </div>
        </div>
        <form
            class="material-order-form"
            method="post"
            action="/gestion/material/generar_pedido.php"
            onsubmit="return confirm('Se generará el PDF y se sumarán <?php echo number_format($planned_total, 0, '.', ''); ?> unidades al stock. ¿Continuar?');"
        >
            <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
            <input type="hidden" name="curso_seleccionado" value="<?php echo $course_id; ?>">
            <input type="hidden" name="token_peticion" value="<?php echo app_escape($order_token); ?>">
            <button
                class="button material-order-button"
                type="submit"
                <?php echo $planned_total > 0 ? '' : ' disabled'; ?>
            >
                Generar pedido PDF
                <?php if ($planned_total > 0) { ?>
                    <span><?php echo number_format($planned_total, 0, ',', '.'); ?> uds.</span>
                <?php } ?>
            </button>
        </form>
    </section>

    <?php if ($recent_orders) { ?>
        <section class="card material-orders">
            <div class="material-orders__heading">
                <div>
                    <span class="eyebrow">Historial</span>
                    <h2>Pedidos generados</h2>
                </div>
                <p>Volver a descargar un PDF no modifica el stock.</p>
            </div>
            <div class="material-orders__list">
                <?php foreach ($recent_orders as $recent_order) { ?>
                    <article class="material-order-row">
                        <div>
                            <strong><?php echo app_escape($recent_order['codigo']); ?></strong>
                            <span>
                                <?php echo app_escape($recent_order['curso_nombre']); ?>
                                ·
                                <?php echo app_escape(date('d/m/Y H:i', strtotime($recent_order['fecha_creacion']))); ?>
                            </span>
                        </div>
                        <div class="material-order-row__totals">
                            <strong><?php echo number_format((float) $recent_order['total_unidades'], 0, ',', '.'); ?> uds.</strong>
                            <span><?php echo (int) $recent_order['total_lineas']; ?> artículos</span>
                        </div>
                        <?php if ($recent_order['estado'] === 'stock_actualizado') { ?>
                            <span class="material-order-status">Stock sumado</span>
                            <a
                                class="button button--outline"
                                href="/gestion/material/pedido_pdf.php?id=<?php echo (int) $recent_order['id_pedido']; ?>"
                            >
                                Descargar PDF
                            </a>
                        <?php } else { ?>
                            <span class="material-order-status material-order-status--pending">
                                Pendiente
                            </span>
                        <?php } ?>
                    </article>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <?php if (!$products) { ?>
        <div class="card material-empty">
            No hay artículos de material configurados para este curso.
        </div>
    <?php } else { ?>
        <div class="material-grid">
            <?php foreach ($products as $product_index => $product) { ?>
                <?php
                $estimate = $product['estimate'];
                $meta = $estimate['meta'];
                $image = material_product_image(
                    $product['ARTICULO'],
                    $product['categoria']
                );
                $input_id = 'planned-' . (int) $product['id_material'];
                $planned = (float) $product['cantidad_planificada'];
                if ($planned <= 0 && $estimate['recommended'] > 0) {
                    $planned = $estimate['recommended'];
                }
                ?>
                <article
                    class="card material-card"
                    data-material-card
                >
                    <figure class="material-card__visual material-card__visual--<?php echo app_escape($image['kind']); ?>">
                        <img
                            src="<?php echo app_escape($image['src']); ?>"
                            alt="<?php echo app_escape($image['alt']); ?>"
                            width="900"
                            height="900"
                            loading="<?php echo $product_index < 4 ? 'eager' : 'lazy'; ?>"
                            decoding="async"
                            <?php echo $product_index === 0 ? ' fetchpriority="high"' : ''; ?>
                        >
                        <figcaption class="material-card__stock<?php echo (float) $product['stock_actual'] <= 0 ? ' material-card__stock--empty' : ''; ?>">
                            <?php if ((float) $product['stock_actual'] > 0) { ?>
                                <?php echo number_format((float) $product['stock_actual'], 0, ',', '.'); ?> en stock
                            <?php } else { ?>
                                Sin stock
                            <?php } ?>
                        </figcaption>
                    </figure>
                    <div class="material-card__top">
                        <div class="material-card__badges">
                            <span><?php echo app_escape($product['categoria']); ?></span>
                            <?php if ($meta['is_uniform']) { ?><span>Uniforme</span><?php } ?>
                            <?php if ($meta['size'] > 0) { ?><span>Talla <?php echo $meta['size']; ?></span><?php } ?>
                            <?php if ($meta['gender'] === 'H') { ?><span>Niño</span><?php } ?>
                            <?php if ($meta['gender'] === 'M') { ?><span>Niña</span><?php } ?>
                        </div>
                        <span class="material-card__price">
                            <?php echo number_format((float) $product['PRECIO_VENTA'], 2, ',', '.'); ?> €
                        </span>
                    </div>
                    <h2><?php echo app_escape($product['ARTICULO']); ?></h2>

                    <div class="material-card__numbers">
                        <div>
                            <span>Stock del centro</span>
                            <strong><?php echo number_format((float) $product['stock_actual'], 0, ',', '.'); ?></strong>
                            <small>Entradas menos entregas</small>
                        </div>
                        <div>
                            <span>Recomendado</span>
                            <strong><?php echo $estimate['recommended']; ?></strong>
                            <small>
                                <?php echo $estimate['base']; ?> alumno(s)
                                <?php echo $estimate['recommended'] > 0 ? ' + margen' : ''; ?>
                            </small>
                        </div>
                    </div>

                    <form
                        class="material-card__form"
                        method="post"
                        action="/gestion/material/guardar_prevision.php"
                    >
                        <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                        <input type="hidden" name="curso_seleccionado" value="<?php echo $course_id; ?>">
                        <input type="hidden" name="id_material" value="<?php echo (int) $product['id_material']; ?>">
                        <div class="material-quantity">
                            <button
                                type="button"
                                aria-label="Restar una unidad"
                                data-quantity-step="-1"
                                data-target="<?php echo $input_id; ?>"
                            >−</button>
                            <input
                                id="<?php echo $input_id; ?>"
                                type="number"
                                name="cantidad_planificada"
                                min="0"
                                step="1"
                                value="<?php echo app_escape($planned); ?>"
                                aria-label="Cantidad planificada"
                            >
                            <button
                                type="button"
                                aria-label="Añadir una unidad"
                                data-quantity-step="1"
                                data-target="<?php echo $input_id; ?>"
                            >+</button>
                        </div>
                        <div class="material-card__actions">
                            <button class="button button--outline" type="submit">
                                Guardar previsión
                            </button>
                            <a
                                class="button"
                                href="/gestion/material/control_entregas.php?curso_seleccionado=<?php echo $course_id; ?>&id_material=<?php echo (int) $product['id_material']; ?>"
                            >
                                Entregas
                            </a>
                        </div>
                    </form>
                </article>
            <?php } ?>
        </div>
    <?php } ?>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
