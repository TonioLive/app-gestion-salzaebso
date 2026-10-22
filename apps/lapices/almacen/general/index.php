<?php
require_once __DIR__ . '/bootstrap.php';

$panel_active_section = 'almacen';
$panel_topbar_label = 'Almacén general';
$panel_topbar_title = 'Stock central · Lápices G.A.';
$panel_body_class = 'warehouse-page';
$panel_extra_styles = array('/almacen/general/assets/almacen-general.css?v=20260909.1');
$panel_extra_scripts = array('/almacen/general/assets/almacen-general.js?v=20260909.1');

$schemaReady = warehouse_table_exists();
$families = array();
$zones = array();
$locations = array();
$articles = array();
$movements = array();
$familySummary = array();
$warehouseCentre = false;
$stats = array('articulos' => 0, 'unidades' => 0, 'stock_bajo' => 0, 'movimientos_hoy' => 0);
$totalArticles = 0;

$search = trim((string) app_request_value($_GET, 'q', ''));
if (function_exists('mb_substr')) {
    $search = mb_substr($search, 0, 100, 'UTF-8');
} else {
    $search = substr($search, 0, 100);
}
$familyFilter = max(0, app_request_int($_GET, 'familia', 0));
$zoneFilter = max(0, app_request_int($_GET, 'zona', 0));
$stockFilter = (string) app_request_value($_GET, 'stock', '');
if (!in_array($stockFilter, array('', 'bajo', 'agotado'), true)) {
    $stockFilter = '';
}
$pageSize = app_request_int($_GET, 'por_pagina', 20);
if (!in_array($pageSize, array(20, 50, 80), true)) {
    $pageSize = 20;
}
$page = max(1, app_request_int($_GET, 'pagina', 1));

if ($schemaReady) {
    $families = warehouse_rows(warehouse_query(
        'SELECT id_familia, nombre, color, icono, orden, activo
         FROM almacen_general_familias ORDER BY activo DESC, orden, nombre',
        '',
        array()
    ));
    $zones = warehouse_rows(warehouse_query(
        'SELECT id_zona, nombre, color, icono, orden, activo
         FROM almacen_general_zonas ORDER BY activo DESC, orden, nombre',
        '',
        array()
    ));
    $locations = warehouse_rows(warehouse_query(
        'SELECT u.id_ubicacion, u.id_zona, u.nombre, u.codigo, u.descripcion, u.activo,
                z.nombre AS zona_nombre, z.color AS zona_color
         FROM almacen_general_ubicaciones u
         INNER JOIN almacen_general_zonas z ON z.id_zona=u.id_zona
         ORDER BY z.orden, z.nombre, u.nombre',
        '',
        array()
    ));
    $warehouseCentre = warehouse_row(warehouse_query(
        'SELECT id_centro, nombre_centro, poblacion_centro FROM centros WHERE id_centro=? LIMIT 1',
        'i',
        array(ALMACEN_GENERAL_CENTRO_ID)
    ));

    $statsRow = warehouse_row(warehouse_query(
        "SELECT
            COUNT(*) AS articulos,
            COALESCE(SUM(stock_actual), 0) AS unidades,
            COALESCE(SUM(CASE WHEN stock_actual<=stock_minimo THEN 1 ELSE 0 END), 0) AS stock_bajo
         FROM almacen_general_articulos WHERE activo=1 AND id_centro=?",
        'i',
        array(ALMACEN_GENERAL_CENTRO_ID)
    ));
    if ($statsRow) {
        $stats['articulos'] = (int) $statsRow['articulos'];
        $stats['unidades'] = (float) $statsRow['unidades'];
        $stats['stock_bajo'] = (int) $statsRow['stock_bajo'];
    }
    $todayRow = warehouse_row(warehouse_query(
        'SELECT COUNT(*) AS total FROM almacen_general_movimientos WHERE creado_en>=CURDATE()',
        '',
        array()
    ));
    $stats['movimientos_hoy'] = $todayRow ? (int) $todayRow['total'] : 0;

    $familySummary = warehouse_rows(warehouse_query(
        'SELECT f.id_familia, f.nombre, f.color, f.icono,
                COUNT(a.id_articulo) AS articulos,
                COALESCE(SUM(a.stock_actual), 0) AS stock
         FROM almacen_general_familias f
         LEFT JOIN almacen_general_articulos a ON a.id_familia=f.id_familia AND a.activo=1
         WHERE f.activo=1
         GROUP BY f.id_familia, f.nombre, f.color, f.icono, f.orden
         ORDER BY f.orden, f.nombre',
        '',
        array()
    ));

    $where = array('a.activo=1', 'a.id_centro=?');
    $types = 'i';
    $parameters = array(ALMACEN_GENERAL_CENTRO_ID);
    if ($search !== '') {
        $where[] = '(a.nombre LIKE ? OR a.referencia LIKE ? OR a.descripcion LIKE ?)';
        $like = '%' . $search . '%';
        $types .= 'sss';
        $parameters[] = $like;
        $parameters[] = $like;
        $parameters[] = $like;
    }
    if ($familyFilter > 0) {
        $where[] = 'a.id_familia=?';
        $types .= 'i';
        $parameters[] = $familyFilter;
    }
    if ($zoneFilter > 0) {
        $where[] = 'z.id_zona=?';
        $types .= 'i';
        $parameters[] = $zoneFilter;
    }
    if ($stockFilter === 'bajo') {
        $where[] = 'a.stock_actual<=a.stock_minimo';
    } elseif ($stockFilter === 'agotado') {
        $where[] = 'a.stock_actual=0';
    }
    $whereSql = implode(' AND ', $where);
    $countRow = warehouse_row(warehouse_query(
        'SELECT COUNT(*) AS total
         FROM almacen_general_articulos a
         INNER JOIN almacen_general_ubicaciones u ON u.id_ubicacion=a.id_ubicacion
         INNER JOIN almacen_general_zonas z ON z.id_zona=u.id_zona
         WHERE ' . $whereSql,
        $types,
        $parameters
    ));
    $totalArticles = $countRow ? (int) $countRow['total'] : 0;
    $totalPages = max(1, (int) ceil($totalArticles / $pageSize));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $pageSize;
    $listTypes = $types . 'ii';
    $listParameters = $parameters;
    $listParameters[] = $pageSize;
    $listParameters[] = $offset;
    $articles = warehouse_rows(warehouse_query(
        'SELECT a.id_articulo, a.id_familia, a.id_ubicacion, a.nombre, a.referencia,
                a.unidad, a.stock_actual, a.stock_minimo, a.descripcion, a.activo, a.actualizado_en,
                f.nombre AS familia_nombre, f.color AS familia_color, f.icono AS familia_icono,
                u.nombre AS ubicacion_nombre, u.codigo AS ubicacion_codigo,
                z.id_zona, z.nombre AS zona_nombre
         FROM almacen_general_articulos a
         INNER JOIN almacen_general_familias f ON f.id_familia=a.id_familia
         INNER JOIN almacen_general_ubicaciones u ON u.id_ubicacion=a.id_ubicacion
         INNER JOIN almacen_general_zonas z ON z.id_zona=u.id_zona
         WHERE ' . $whereSql . '
         ORDER BY (a.stock_actual<=a.stock_minimo) DESC, f.orden, a.nombre
         LIMIT ? OFFSET ?',
        $listTypes,
        $listParameters
    ));

    $movements = warehouse_rows(warehouse_query(
        'SELECT m.id_movimiento, m.tipo, m.cantidad, m.stock_anterior, m.stock_posterior,
                m.motivo, m.observaciones, m.creado_en,
                a.nombre AS articulo_nombre, a.unidad,
                f.color AS familia_color, f.icono AS familia_icono,
                COALESCE(
                    NULLIF(TRIM(CONCAT_WS(\' \', u.Nombre, u.Apellido1, u.Apellido2)), \'\'),
                    CONCAT(\'Usuario #\', m.id_usuario)
                ) AS usuario_nombre
         FROM almacen_general_movimientos m
         INNER JOIN almacen_general_articulos a ON a.id_articulo=m.id_articulo
         INNER JOIN almacen_general_familias f ON f.id_familia=a.id_familia
         LEFT JOIN usuarios u ON u.id_usuario=m.id_usuario
         ORDER BY m.id_movimiento DESC LIMIT 50',
        '',
        array()
    ));
} else {
    $totalPages = 1;
}

function warehouse_page_url($targetPage)
{
    $query = $_GET;
    $query['pagina'] = (int) $targetPage;
    return '/almacen/general/?' . http_build_query($query) . '#inventario';
}

require dirname(dirname(__DIR__)) . '/gestion/panel/views/header.php';
?>

<section class="warehouse-hero">
    <div>
        <span class="eyebrow">Almacén central</span>
        <h1>Almacén general</h1>
        <p>Control de existencias, ubicaciones y movimientos del almacén de G.A. y la piscina NeoKids.</p>
    </div>
    <div class="warehouse-hero__actions">
        <span class="warehouse-centre">
            <small>Ubicación principal</small>
            <strong><?php echo app_escape($warehouseCentre ? $warehouseCentre['nombre_centro'] : 'Lápices G.A.'); ?></strong>
        </span>
        <?php if ($schemaReady) { ?>
            <button class="button button--primary" type="button" data-dialog-open="dialog-articulo">+ Nuevo artículo</button>
        <?php } ?>
    </div>
</section>

<?php if (!$schemaReady) { ?>
    <div class="warehouse-alert warehouse-alert--error" role="alert">
        <strong>El almacén todavía no está inicializado.</strong>
        Ejecuta el instalador de base de datos antes de utilizar este módulo.
    </div>
<?php } else { ?>
    <?php if (!empty($_SESSION['warehouse_flash']) && is_array($_SESSION['warehouse_flash'])) { ?>
        <?php $flash = $_SESSION['warehouse_flash']; unset($_SESSION['warehouse_flash']); ?>
        <div class="warehouse-alert warehouse-alert--<?php echo app_escape($flash['type']); ?>" role="status">
            <?php echo app_escape($flash['message']); ?>
        </div>
    <?php } ?>

    <nav class="warehouse-tabs" aria-label="Secciones del almacén">
        <a href="#inventario">Inventario</a>
        <a href="#movimientos">Movimientos</a>
        <a href="#organizacion">Familias y ubicaciones</a>
    </nav>

    <section class="warehouse-stats" aria-label="Resumen del almacén">
        <article><span>AR</span><div><small>Artículos activos</small><strong><?php echo $stats['articulos']; ?></strong></div></article>
        <article><span>ST</span><div><small>Stock registrado</small><strong><?php echo number_format($stats['unidades'], 3, ',', '.'); ?></strong></div></article>
        <article class="<?php echo $stats['stock_bajo'] > 0 ? 'is-warning' : ''; ?>"><span>!</span><div><small>Stock bajo</small><strong><?php echo $stats['stock_bajo']; ?></strong></div></article>
        <article><span>HO</span><div><small>Movimientos hoy</small><strong><?php echo $stats['movimientos_hoy']; ?></strong></div></article>
    </section>

    <section class="family-strip" aria-label="Familias de artículos">
        <?php foreach ($familySummary as $family) { ?>
            <a href="?familia=<?php echo (int) $family['id_familia']; ?>#inventario" style="--family-color: <?php echo app_escape($family['color']); ?>">
                <span><?php echo app_escape(warehouse_icon($family['icono'])); ?></span>
                <div><strong><?php echo app_escape($family['nombre']); ?></strong><small><?php echo (int) $family['articulos']; ?> artículos · <?php echo number_format((float) $family['stock'], 3, ',', '.'); ?></small></div>
            </a>
        <?php } ?>
    </section>

    <section class="warehouse-section" id="inventario" aria-labelledby="inventario-title">
        <div class="warehouse-section__heading">
            <div><span class="eyebrow">Existencias</span><h2 id="inventario-title">Inventario</h2></div>
            <strong><?php echo $totalArticles; ?> resultado<?php echo $totalArticles === 1 ? '' : 's'; ?></strong>
        </div>

        <form class="warehouse-filters" method="get" action="/almacen/general/">
            <label class="warehouse-search"><span>Buscar</span><input type="search" name="q" value="<?php echo app_escape($search); ?>" placeholder="Nombre, referencia o descripción"></label>
            <label><span>Familia</span><select name="familia"><option value="0">Todas</option><?php foreach ($families as $family) { if (!(int) $family['activo']) continue; ?><option value="<?php echo (int) $family['id_familia']; ?>"<?php echo $familyFilter === (int) $family['id_familia'] ? ' selected' : ''; ?>><?php echo app_escape($family['nombre']); ?></option><?php } ?></select></label>
            <label><span>Zona</span><select name="zona"><option value="0">Todas</option><?php foreach ($zones as $zone) { if (!(int) $zone['activo']) continue; ?><option value="<?php echo (int) $zone['id_zona']; ?>"<?php echo $zoneFilter === (int) $zone['id_zona'] ? ' selected' : ''; ?>><?php echo app_escape($zone['nombre']); ?></option><?php } ?></select></label>
            <label><span>Estado</span><select name="stock"><option value="">Todo el stock</option><option value="bajo"<?php echo $stockFilter === 'bajo' ? ' selected' : ''; ?>>Stock bajo</option><option value="agotado"<?php echo $stockFilter === 'agotado' ? ' selected' : ''; ?>>Agotado</option></select></label>
            <label><span>Filas</span><select name="por_pagina"><option value="20"<?php echo $pageSize === 20 ? ' selected' : ''; ?>>20</option><option value="50"<?php echo $pageSize === 50 ? ' selected' : ''; ?>>50</option><option value="80"<?php echo $pageSize === 80 ? ' selected' : ''; ?>>80</option></select></label>
            <button class="button button--primary" type="submit">Aplicar</button>
            <a class="button button--outline" href="/almacen/general/#inventario">Limpiar</a>
        </form>

        <?php if (count($articles) === 0) { ?>
            <div class="warehouse-empty"><span>□</span><strong>No hay artículos con estos filtros</strong><p>Crea el primer artículo o cambia los filtros de búsqueda.</p></div>
        <?php } else { ?>
            <div class="warehouse-table-wrap">
                <table class="warehouse-table">
                    <thead><tr><th>Artículo</th><th>Familia</th><th>Ubicación</th><th>Stock</th><th>Mínimo</th><th>Actualizado</th><th>Acciones</th></tr></thead>
                    <tbody>
                    <?php foreach ($articles as $article) { $low = (float) $article['stock_actual'] <= (float) $article['stock_minimo']; ?>
                        <tr class="<?php echo $low ? 'is-low' : ''; ?>">
                            <td data-label="Artículo"><div class="article-name"><span style="--family-color: <?php echo app_escape($article['familia_color']); ?>"><?php echo app_escape(warehouse_icon($article['familia_icono'])); ?></span><div><strong><?php echo app_escape($article['nombre']); ?></strong><small><?php echo app_escape($article['referencia'] ? $article['referencia'] : 'Sin referencia'); ?></small></div></div></td>
                            <td data-label="Familia"><span class="family-pill" style="--family-color: <?php echo app_escape($article['familia_color']); ?>"><?php echo app_escape($article['familia_nombre']); ?></span></td>
                            <td data-label="Ubicación"><strong><?php echo app_escape($article['zona_nombre']); ?></strong><small><?php echo app_escape($article['ubicacion_nombre'] . ' · ' . $article['ubicacion_codigo']); ?></small></td>
                            <td data-label="Stock"><strong class="stock-value<?php echo $low ? ' stock-value--low' : ''; ?>"><?php echo number_format((float) $article['stock_actual'], 3, ',', '.'); ?></strong> <small><?php echo app_escape($article['unidad']); ?></small></td>
                            <td data-label="Mínimo"><?php echo number_format((float) $article['stock_minimo'], 3, ',', '.'); ?></td>
                            <td data-label="Actualizado"><time datetime="<?php echo app_escape($article['actualizado_en']); ?>"><?php echo app_escape(date('d/m/Y H:i', strtotime($article['actualizado_en']))); ?></time></td>
                            <td data-label="Acciones"><div class="row-actions">
                                <button type="button" class="action action--in" data-movement-open data-id="<?php echo (int) $article['id_articulo']; ?>" data-name="<?php echo app_escape($article['nombre']); ?>" data-stock="<?php echo app_escape($article['stock_actual']); ?>" data-type="ENTRADA">Entrada</button>
                                <button type="button" class="action action--out" data-movement-open data-id="<?php echo (int) $article['id_articulo']; ?>" data-name="<?php echo app_escape($article['nombre']); ?>" data-stock="<?php echo app_escape($article['stock_actual']); ?>" data-type="SALIDA">Salida</button>
                                <button type="button" class="action" data-edit-open
                                    data-id="<?php echo (int) $article['id_articulo']; ?>"
                                    data-family="<?php echo (int) $article['id_familia']; ?>"
                                    data-location="<?php echo (int) $article['id_ubicacion']; ?>"
                                    data-name="<?php echo app_escape($article['nombre']); ?>"
                                    data-reference="<?php echo app_escape($article['referencia']); ?>"
                                    data-unit="<?php echo app_escape($article['unidad']); ?>"
                                    data-minimum="<?php echo app_escape($article['stock_minimo']); ?>"
                                    data-description="<?php echo app_escape($article['descripcion']); ?>">Editar</button>
                            </div></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1) { ?>
                <nav class="warehouse-pagination" aria-label="Páginas del inventario">
                    <a class="<?php echo $page <= 1 ? 'is-disabled' : ''; ?>" href="<?php echo app_escape(warehouse_page_url(max(1, $page - 1))); ?>">← Anterior</a>
                    <span>Página <?php echo $page; ?> de <?php echo $totalPages; ?></span>
                    <a class="<?php echo $page >= $totalPages ? 'is-disabled' : ''; ?>" href="<?php echo app_escape(warehouse_page_url(min($totalPages, $page + 1))); ?>">Siguiente →</a>
                </nav>
            <?php } ?>
        <?php } ?>
    </section>

    <section class="warehouse-section" id="movimientos" aria-labelledby="movimientos-title">
        <div class="warehouse-section__heading"><div><span class="eyebrow">Trazabilidad</span><h2 id="movimientos-title">Últimos movimientos</h2></div><small>Se muestran los 50 más recientes</small></div>
        <?php if (count($movements) === 0) { ?>
            <div class="warehouse-empty warehouse-empty--compact"><strong>Todavía no hay movimientos.</strong></div>
        <?php } else { ?>
            <div class="movement-list">
            <?php foreach ($movements as $movement) { ?>
                <article class="movement movement--<?php echo strtolower(app_escape($movement['tipo'])); ?>">
                    <span class="movement__icon" style="--family-color: <?php echo app_escape($movement['familia_color']); ?>"><?php echo app_escape(warehouse_icon($movement['familia_icono'])); ?></span>
                    <div class="movement__main"><strong><?php echo app_escape($movement['articulo_nombre']); ?></strong><span><?php echo app_escape($movement['motivo']); ?><?php echo $movement['observaciones'] !== '' ? ' · ' . app_escape($movement['observaciones']) : ''; ?></span><small><?php echo app_escape($movement['usuario_nombre']); ?> · <?php echo app_escape(date('d/m/Y H:i', strtotime($movement['creado_en']))); ?></small></div>
                    <div class="movement__amount"><strong><?php echo (float) $movement['cantidad'] > 0 ? '+' : ''; ?><?php echo number_format((float) $movement['cantidad'], 3, ',', '.'); ?></strong><span><?php echo number_format((float) $movement['stock_anterior'], 3, ',', '.'); ?> → <?php echo number_format((float) $movement['stock_posterior'], 3, ',', '.'); ?></span></div>
                </article>
            <?php } ?>
            </div>
        <?php } ?>
    </section>

    <section class="warehouse-section" id="organizacion" aria-labelledby="organizacion-title">
        <div class="warehouse-section__heading"><div><span class="eyebrow">Configuración visual</span><h2 id="organizacion-title">Familias y ubicaciones</h2></div><small>Personaliza nombres, colores e iconos.</small></div>
        <div class="organisation-grid">
            <div class="organisation-card">
                <div class="organisation-card__heading"><h3>Familias</h3><button type="button" class="button button--small" data-dialog-open="dialog-familia">+ Añadir</button></div>
                <div class="editable-list">
                <?php foreach ($families as $family) { ?>
                    <details class="editable-item" style="--family-color: <?php echo app_escape($family['color']); ?>">
                        <summary><span><?php echo app_escape(warehouse_icon($family['icono'])); ?></span><strong><?php echo app_escape($family['nombre']); ?></strong><small><?php echo (int) $family['activo'] ? 'Activa' : 'Desactivada'; ?></small></summary>
                        <form method="post" action="/almacen/general/acciones.php" class="mini-form">
                            <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>"><input type="hidden" name="accion" value="actualizar_familia"><input type="hidden" name="id_familia" value="<?php echo (int) $family['id_familia']; ?>">
                            <label>Nombre<input name="nombre" value="<?php echo app_escape($family['nombre']); ?>" required></label>
                            <label>Color<input type="color" name="color" value="<?php echo app_escape($family['color']); ?>"></label>
                            <label>Icono<select name="icono"><?php foreach (warehouse_icons() as $key => $icon) { ?><option value="<?php echo app_escape($key); ?>"<?php echo $key === $family['icono'] ? ' selected' : ''; ?>><?php echo app_escape($icon['emoji'] . ' ' . $icon['label']); ?></option><?php } ?></select></label>
                            <label>Orden<input type="number" min="0" max="999" name="orden" value="<?php echo (int) $family['orden']; ?>"></label>
                            <label class="check"><input type="checkbox" name="activo" value="1"<?php echo (int) $family['activo'] ? ' checked' : ''; ?>> Activa</label>
                            <button class="button button--primary button--small" type="submit">Guardar</button>
                        </form>
                    </details>
                <?php } ?>
                </div>
            </div>
            <div class="organisation-card">
                <div class="organisation-card__heading"><h3>Zonas y ubicaciones</h3><div><button type="button" class="button button--small" data-dialog-open="dialog-zona">+ Zona</button><button type="button" class="button button--small" data-dialog-open="dialog-ubicacion">+ Ubicación</button></div></div>
                <div class="zone-list">
                <?php foreach ($zones as $zone) { ?>
                    <article class="zone-card" style="--zone-color: <?php echo app_escape($zone['color']); ?>">
                        <header><span><?php echo app_escape(warehouse_icon($zone['icono'])); ?></span><strong><?php echo app_escape($zone['nombre']); ?></strong></header>
                        <ul><?php foreach ($locations as $location) { if ((int) $location['id_zona'] !== (int) $zone['id_zona']) continue; ?><li><strong><?php echo app_escape($location['codigo']); ?></strong><span><?php echo app_escape($location['nombre']); ?></span></li><?php } ?></ul>
                    </article>
                <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <dialog class="warehouse-dialog" id="dialog-articulo">
        <form method="post" action="/almacen/general/acciones.php">
            <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>"><input type="hidden" name="accion" value="crear_articulo">
            <div class="dialog-heading"><div><span class="eyebrow">Inventario</span><h2>Nuevo artículo</h2></div><button type="button" data-dialog-close aria-label="Cerrar">×</button></div>
            <div class="form-grid"><label class="span-2">Nombre<input name="nombre" maxlength="150" required></label><label>Referencia<input name="referencia" maxlength="80" placeholder="Opcional"></label><label>Unidad<select name="unidad"><?php foreach (warehouse_units() as $unit) { ?><option value="<?php echo app_escape($unit); ?>"><?php echo app_escape(ucfirst($unit)); ?></option><?php } ?></select></label><label>Familia<select name="id_familia" required><option value="">Selecciona</option><?php foreach ($families as $family) { if (!(int) $family['activo']) continue; ?><option value="<?php echo (int) $family['id_familia']; ?>"><?php echo app_escape($family['nombre']); ?></option><?php } ?></select></label><label>Ubicación<select name="id_ubicacion" required><option value="">Selecciona</option><?php foreach ($locations as $location) { if (!(int) $location['activo']) continue; ?><option value="<?php echo (int) $location['id_ubicacion']; ?>"><?php echo app_escape($location['zona_nombre'] . ' · ' . $location['nombre'] . ' (' . $location['codigo'] . ')'); ?></option><?php } ?></select></label><label>Stock inicial<input type="number" name="stock_inicial" min="0" step="0.001" value="0"></label><label>Stock mínimo<input type="number" name="stock_minimo" min="0" step="0.001" value="0"></label><label class="span-2">Descripción<textarea name="descripcion" rows="3" maxlength="2000"></textarea></label></div>
            <div class="dialog-actions"><button type="button" class="button button--outline" data-dialog-close>Cancelar</button><button type="submit" class="button button--primary">Crear artículo</button></div>
        </form>
    </dialog>

    <dialog class="warehouse-dialog" id="dialog-movimiento">
        <form method="post" action="/almacen/general/acciones.php">
            <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>"><input type="hidden" name="accion" value="movimiento"><input type="hidden" name="id_articulo" data-movement-id>
            <div class="dialog-heading"><div><span class="eyebrow">Movimiento de stock</span><h2 data-movement-title>Registrar movimiento</h2><p data-movement-stock></p></div><button type="button" data-dialog-close aria-label="Cerrar">×</button></div>
            <div class="movement-types"><label><input type="radio" name="tipo" value="ENTRADA" checked><span>Entrada<small>Suma unidades</small></span></label><label><input type="radio" name="tipo" value="SALIDA"><span>Salida<small>Resta unidades</small></span></label><label><input type="radio" name="tipo" value="AJUSTE"><span>Ajuste<small>Fija el stock real</small></span></label></div>
            <div class="form-grid"><label>Cantidad<input type="number" name="cantidad" min="0" step="0.001" required></label><label>Motivo<input name="motivo" maxlength="150" required placeholder="Compra, consumo, inventario..."></label><label class="span-2">Observaciones<textarea name="observaciones" rows="3" maxlength="500"></textarea></label></div>
            <div class="dialog-actions"><button type="button" class="button button--outline" data-dialog-close>Cancelar</button><button type="submit" class="button button--primary">Guardar movimiento</button></div>
        </form>
    </dialog>

    <dialog class="warehouse-dialog" id="dialog-editar">
        <form method="post" action="/almacen/general/acciones.php">
            <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>"><input type="hidden" name="accion" value="actualizar_articulo"><input type="hidden" name="id_articulo" data-edit="id">
            <div class="dialog-heading"><div><span class="eyebrow">Inventario</span><h2>Editar artículo</h2></div><button type="button" data-dialog-close aria-label="Cerrar">×</button></div>
            <div class="form-grid"><label class="span-2">Nombre<input name="nombre" data-edit="name" maxlength="150" required></label><label>Referencia<input name="referencia" data-edit="reference" maxlength="80"></label><label>Unidad<select name="unidad" data-edit="unit"><?php foreach (warehouse_units() as $unit) { ?><option value="<?php echo app_escape($unit); ?>"><?php echo app_escape(ucfirst($unit)); ?></option><?php } ?></select></label><label>Familia<select name="id_familia" data-edit="family" required><?php foreach ($families as $family) { if (!(int) $family['activo']) continue; ?><option value="<?php echo (int) $family['id_familia']; ?>"><?php echo app_escape($family['nombre']); ?></option><?php } ?></select></label><label>Ubicación<select name="id_ubicacion" data-edit="location" required><?php foreach ($locations as $location) { if (!(int) $location['activo']) continue; ?><option value="<?php echo (int) $location['id_ubicacion']; ?>"><?php echo app_escape($location['zona_nombre'] . ' · ' . $location['nombre']); ?></option><?php } ?></select></label><label>Stock mínimo<input type="number" name="stock_minimo" data-edit="minimum" min="0" step="0.001"></label><label class="check check--form"><input type="checkbox" name="activo" value="1" checked> Artículo activo</label><label class="span-2">Descripción<textarea name="descripcion" data-edit="description" rows="3" maxlength="2000"></textarea></label></div>
            <div class="dialog-actions"><button type="button" class="button button--outline" data-dialog-close>Cancelar</button><button type="submit" class="button button--primary">Guardar cambios</button></div>
        </form>
    </dialog>

    <dialog class="warehouse-dialog warehouse-dialog--small" id="dialog-familia"><form method="post" action="/almacen/general/acciones.php"><input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>"><input type="hidden" name="accion" value="crear_familia"><div class="dialog-heading"><div><span class="eyebrow">Organización</span><h2>Nueva familia</h2></div><button type="button" data-dialog-close>×</button></div><div class="form-grid"><label class="span-2">Nombre<input name="nombre" maxlength="100" required></label><label>Color<input type="color" name="color" value="#21675A"></label><label>Icono<select name="icono"><?php foreach (warehouse_icons() as $key => $icon) { ?><option value="<?php echo app_escape($key); ?>"><?php echo app_escape($icon['emoji'] . ' ' . $icon['label']); ?></option><?php } ?></select></label><input type="hidden" name="orden" value="100"></div><div class="dialog-actions"><button type="button" class="button button--outline" data-dialog-close>Cancelar</button><button type="submit" class="button button--primary">Crear familia</button></div></form></dialog>

    <dialog class="warehouse-dialog warehouse-dialog--small" id="dialog-zona"><form method="post" action="/almacen/general/acciones.php"><input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>"><input type="hidden" name="accion" value="crear_zona"><div class="dialog-heading"><div><span class="eyebrow">Ubicaciones</span><h2>Nueva zona</h2></div><button type="button" data-dialog-close>×</button></div><div class="form-grid"><label class="span-2">Nombre<input name="nombre" maxlength="100" required></label><label>Color<input type="color" name="color" value="#21675A"></label><label>Icono<select name="icono"><?php foreach (warehouse_icons() as $key => $icon) { ?><option value="<?php echo app_escape($key); ?>"><?php echo app_escape($icon['emoji'] . ' ' . $icon['label']); ?></option><?php } ?></select></label></div><div class="dialog-actions"><button type="button" class="button button--outline" data-dialog-close>Cancelar</button><button type="submit" class="button button--primary">Crear zona</button></div></form></dialog>

    <dialog class="warehouse-dialog warehouse-dialog--small" id="dialog-ubicacion"><form method="post" action="/almacen/general/acciones.php"><input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>"><input type="hidden" name="accion" value="crear_ubicacion"><div class="dialog-heading"><div><span class="eyebrow">Ubicaciones</span><h2>Nueva ubicación</h2></div><button type="button" data-dialog-close>×</button></div><div class="form-grid"><label class="span-2">Zona<select name="id_zona" required><option value="">Selecciona</option><?php foreach ($zones as $zone) { if (!(int) $zone['activo']) continue; ?><option value="<?php echo (int) $zone['id_zona']; ?>"><?php echo app_escape($zone['nombre']); ?></option><?php } ?></select></label><label>Nombre<input name="nombre" maxlength="120" placeholder="Estantería A · Balda 2" required></label><label>Código<input name="codigo" maxlength="50" placeholder="GA-A-02" required></label><label class="span-2">Descripción<textarea name="descripcion" rows="3" maxlength="255"></textarea></label></div><div class="dialog-actions"><button type="button" class="button button--outline" data-dialog-close>Cancelar</button><button type="submit" class="button button--primary">Crear ubicación</button></div></form></dialog>
<?php } ?>

<?php require dirname(dirname(__DIR__)) . '/gestion/panel/views/footer.php'; ?>
